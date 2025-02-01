<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken");
header("Content-type: text/html; charset=utf-8");
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../include.inc.php');

//CheckAuthUserLoginStatus();


$USER_ID        = $GLOBAL_USER->USER_ID;

$payload        = file_get_contents('php://input');
$_POST          = json_decode($payload,true);

$temperature    = intval($_POST['temperature']*10)/10;
$历史消息        = (array)$_POST['history'];
$用户输入        = (string)$_POST['question'];

$DB_TYPE        = 'mysqli';
if($用户输入 != "")   {
  //保存到数据
  $sql  = "select * from data_ai_dashboard ";
  $rs   = $db->Execute($sql);
  $rs_a = (array)$rs->GetArray();
  //补充数据表结构
  for($i=0;$i<sizeof($rs_a);$i++) {
    $RS_Item = $rs_a[$i];
    //判断数据表结构是否存在
    if($RS_Item['数据表结构'] == "" && $RS_Item['数据源'] != "")  {
      $sql = "select * from data_datasource where id='".$RS_Item['数据源']."'";
      $rs = $db->Execute($sql);
      $rs_a = $rs->GetArray();
      $Item = $rs_a[0];
      $db_remote = NewADOConnection($DB_TYPE='mysqli');
      $db_remote->connect($Item['数据库主机'], $Item['数据库用户名'], DecryptID($Item['数据库密码']), $Item['数据库名称']);
      $db_remote->Execute("Set names utf8;");
      $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
      if($db_remote->databaseName!="" && $db_remote->databaseName==$Item['数据库名称']) {
          $sql = "SHOW CREATE TABLE `".$RS_Item['数据表']."`;";
          $数据表结构RS = $db_remote->Execute($sql);
          $CreateTable = $数据表结构RS->fields['Create Table'];
          $sql = "update data_ai_dashboard set 数据表结构 = '".base64_encode($CreateTable)."' where id='".$RS_Item['id']."'";
          $db->Execute($sql);
          $rs_a[$i]['数据表结构'] = base64_encode($CreateTable);
      }
    }
  }

  //构建提示词语
  $构建提示词语 = "我的信息输入为: '".$用户输入."', 需要从以下可能的条件中选取其中一个. \n";
  for($i=0;$i<sizeof($rs_a);$i++) {
    $RS_Item = $rs_a[$i];
    $构建提示词语 .= "第".($i+1)."种情况下面需要匹配的文本: '".$RS_Item['AI匹配关键字']."', 当前情况如果匹配成功, 就直接返回: '".$RS_Item['名称']."' \n";
  }
  $构建提示词语 .= "要求直接返回结果, 不需要返回过多解释信息.\n";

  $SystemPrompt = "需要把用户输入的信息,跟提交的几个选项进行对比, 返回匹配度最高的一个选项.";
  $DeepSeekAiChatResult = DeepSeekAiChat($SystemPrompt, $构建提示词语, $历史消息, $temperature, $IsStream='false', $备注);
  $DeepSeekAiChatResultJSON = json_decode($DeepSeekAiChatResult, true);
  $名称 = $DeepSeekAiChatResultJSON['choices'][0]['message']['content'];

  //获得具体SQL语句
  $当前学期      = getCurrentXueQi();
  $sql          = "select * from data_ai_dashboard where 名称='".$名称."'";
  $rs           = $db->Execute($sql);
  $rs_a         = $rs->GetArray();
  $Item         = $rs_a[0];
  $提示词语 = $Item['提示词语'];
  $提示词语 = str_replace("[数据表结构]", base64_decode($Item['数据表结构']), $提示词语);
  $提示词语 = str_replace("[当前学期]", $当前学期, $提示词语);
  $提示词语 = str_replace("[当前日期]", date('Y-m-d'), $提示词语);
  $提示词语 = str_replace("[我的用户名]", "810120", $提示词语);
  $提示词语 = str_replace("[我的学号]", "240401049", $提示词语);
  $构建提示词语 = $用户输入;
  $DeepSeekAiChatResult = DeepSeekAiChat($提示词语, $构建提示词语, $历史消息, $temperature, $IsStream='false', $备注);
  $DeepSeekAiChatResultJSON = json_decode($DeepSeekAiChatResult, true);
  $SQL结果 = $DeepSeekAiChatResultJSON['choices'][0]['message']['content'];
  $SQL结果 = str_replace("```sql", "", $SQL结果);
  $SQL结果 = str_replace("```", "", $SQL结果);

  //开始执行SQL语句
  $sql          = "select * from data_datasource where id='".$Item['数据源']."'";
  $rs           = $db->Execute($sql);
  $rs_a         = $rs->GetArray();
  $Item         = $rs_a[0];
  $db_remote = NewADOConnection($DB_TYPE='mysqli');
  $db_remote->connect($Item['数据库主机'], $Item['数据库用户名'], DecryptID($Item['数据库密码']), $Item['数据库名称']);
  $db_remote->Execute("Set names utf8;");
  $db_remote->setFetchMode(ADODB_FETCH_ASSOC);

  $rs   = $db_remote->Execute($SQL结果);
  $rs_a = $rs->GetArray();
  $RS = [];
  $RS['sql']      = $SQL结果;
  $RS['data']     = $rs_a;
  $RS['module']   = 'table';
  //$RS['提示词语1']  = $构建提示词语;
  //$RS['提示词语2']  = $提示词语;
  print_R(json_encode($RS));
  exit;

}

function DeepSeekAiChat($系统模板, $用户输入, $历史消息, $temperature, $IsStream, $备注)     {
  global $APIKEY;
  $curl 		  = curl_init();
  $messages 	= [];
  $messages[] = ['content'=>$系统模板, 'role'=>'system'];
  foreach($历史消息 as $消息) {
    $过滤AI回复文本 = str_replace("\\\\\\n", "\n", $消息[1]);
    $过滤AI回复文本 = str_replace("\\\\n", "\n", $过滤AI回复文本);
    $过滤AI回复文本 = str_replace("\\n", "\n", $过滤AI回复文本);
    $messages[] = ['content'=>$消息[0], 'role'=>'user'];
    $messages[] = ['content'=>$过滤AI回复文本, 'role'=>'assistant'];
  }
  $messages[] = ['content'=>$用户输入, 'role'=>'user'];
  curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.deepseek.com/chat/completions',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
      "messages": '.json_encode($messages).',
      "model": "deepseek-chat",
      "frequency_penalty": 0,
      "max_tokens": 2048,
      "presence_penalty": 0,
      "stop": null,
      "stream": '.$IsStream.',
      "temperature": '.$temperature.',
      "top_p": 1,
      "logprobs": false,
      "top_logprobs": null
      }',
      CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Accept: application/json',
          'Authorization: Bearer ' . $APIKEY
      ),
  ));

  if($IsStream == 'true')   {
    $输出TEXT = "";
    curl_setopt($curl, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$IsStream, &$用户输入, &$输出TEXT) {
      print $data;
      ob_flush();
      flush();

      static $buffer = ''; // 用于存储不完整的数据块
      $buffer .= $data; // 将当前数据块追加到缓冲区
      while (preg_match('/"content":"([^"]*)"/', $buffer, $matches)) {
          $outputData = $matches[1];
          $输出TEXT .= $outputData;
          //echo $outputData;
          //ob_flush();
          //flush();
          // 从缓冲区中移除已处理的部分
          $buffer = substr($buffer, strpos($buffer, $matches[0]) + strlen($matches[0]));
      }
      if(trim($data) == "data: [DONE]")  {
        //print_R($输出TEXT);
        //保存到数据库($IsStream, $用户输入, $输出TEXT, $备注);
      }
      return strlen($data);
    });

    curl_exec($curl);
    if (curl_errno($curl)) {
      echo 'Error: ' . curl_error($curl);
    }
    curl_close($curl);
  }
  else {
    $result = curl_exec($curl);
    if (curl_errno($curl)) {
      echo 'Error: ' . curl_error($curl);
    }
    curl_close($curl);
    return $result;
  }
}

function 保存到数据库($IsStream, $用户输入, $输出TEXT, $备注)  {
  global $db;
  global $GLOBAL_USER;
  $Element            = [];
  $Element['AI应用']  = $IsStream;
  $Element['用户名']  = $GLOBAL_USER->USER_ID;
  $Element['姓名']    = $GLOBAL_USER->USER_NAME;
  $Element['学号']    = $GLOBAL_USER->学号;
  $Element['班级']    = $GLOBAL_USER->班级;
  $Element['花费Token']   = '1';
  $Element['输入']        = addslashes($用户输入);
  $Element['输出']        = addslashes($输出TEXT);
  $Element['时间']        = date('Y-m-d H:i:s');
  $Element['数据']        = "";
  $Element['备注']        = $备注;
  $KEYS			  = array_keys($Element);
	$VALUES			= array_values($Element);
  $sql	      = "insert into data_ai_chatlog(`".join('`,`',$KEYS)."`) values('".join("','",$VALUES)."')";
	$rs         = $db->Execute($sql);
}
?>
