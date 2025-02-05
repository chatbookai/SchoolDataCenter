<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken");
//header("Content-type: text/html; charset=utf-8");
//header('Content-Type: text/event-stream');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../include.inc.php');

//CheckAuthUserLoginStatus();

//$DeepSeekAiChatResult = OpenKeyCloudAiChat("你是谁?", "你是谁?", $历史消息=[], $temperature=0.7, $IsStream='false', $备注='');
//$DeepSeekAiChatResultJSON = json_decode($DeepSeekAiChatResult, true);
//$名称 = $DeepSeekAiChatResultJSON['choices'][0]['message']['content'];
//print_R($名称);print_R($DeepSeekAiChatResult);exit;

$USER_ID        = $GLOBAL_USER->USER_ID;

$payload        = file_get_contents('php://input');
$_POST          = json_decode($payload,true);

$temperature    = intval($_POST['temperature']*10)/10;
$历史消息        = (array)$_POST['history'];
$用户输入        = (string)$_POST['question'];
$模块            = (string)$_POST['module'];

$sql      = "select * from data_ai_app where AppName = 'AI智能仪表盘' ";
$rs       = $db->Execute($sql);
$rs_a     = $rs->GetArray();
$应用配置  = $rs_a[0];
$AppModel   = $应用配置['AppModel'];

$sql      = "select * from data_ai_model where Name = '$AppModel' ";
$rs       = $db->Execute($sql);
$rs_a     = $rs->GetArray();
$模型信息  = $rs_a[0];
//print_R($模型信息);

$DB_TYPE        = 'mysqli';
if($用户输入 != "" && $_GET['action'] == 'router')   {
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

  $用户输入Array = [];
  foreach($历史消息 as $消息) {
    $用户输入Array[] = $消息[0];
  }
  $用户输入Array[] = $用户输入;

  //$用户输入Array = array_reverse($用户输入Array);

  //构建提示词语
  $构建提示词语 = "我的信息输入为: '".join(',', $用户输入Array)."', 我输入的内容为多个命令, 中间使用逗号隔开了, 优化级规则为:最后面输入内容的优先级为最高, 如果跟前面的内容冲突,则使用最后面的内容. 要求:从以下可能的条件中选取其中一个. 切记: 最后面的命令的优先级为最高. \n";
  for($i=0;$i<sizeof($rs_a);$i++) {
    $RS_Item = $rs_a[$i];
    $构建提示词语 .= "第".($i+1)."种情况下面需要匹配的文本: '".$RS_Item['AI匹配关键字']."', 当前情况如果匹配成功, 就直接返回: '".$RS_Item['名称']."' \n";
  }
  $构建提示词语 .= "要求直接返回结果, 不需要返回过多解释信息.\n";

  $SystemPrompt = "需要把用户输入的信息, 跟提交的几个选项进行对比, 返回匹配度最高的一个选项.";
  switch($AppModel) {
    case 'DeepSeekChat':
      $DeepSeekAiChatResult = DeepSeekAiChat($SystemPrompt, $构建提示词语, $历史消息=[], $temperature, $IsStream='false', $备注);
      break;
    case 'OpenKeyCloud':
      $DeepSeekAiChatResult = OpenKeyCloudAiChat($SystemPrompt, $构建提示词语, $历史消息=[], $temperature, $IsStream='false', $备注);
      break;
    default:
      $DeepSeekAiChatResult = OpenKeyCloudAiChat($SystemPrompt, $构建提示词语, $历史消息=[], $temperature, $IsStream='false', $备注);
      break;
  }
  $DeepSeekAiChatResultJSON = json_decode($DeepSeekAiChatResult, true);
  $名称 = $DeepSeekAiChatResultJSON['choices'][0]['message']['content'];

  if($DeepSeekAiChatResultJSON['error']['message'] != "")  {
    $RS = [];
    $RS['data']     = [];
    $RS['message']  = $DeepSeekAiChatResultJSON['error']['message'];
    $RS['module']   = 'msg';
    $RS['DeepSeekAiChatResultJSON']  = $DeepSeekAiChatResultJSON;
    $RS['构建提示词语']  = $构建提示词语;
    $RS['历史消息']     = $历史消息;
    print_R(json_encode($RS));
    exit;
  }
  elseif($名称 == "")  {
    $RS = [];
    $RS['data']     = [];
    $RS['message']  = '没有获得到对应的模块';
    $RS['module']   = 'msg';
    $RS['DeepSeekAiChatResultJSON']  = $DeepSeekAiChatResultJSON;
    $RS['构建提示词语']  = $构建提示词语;
    $RS['历史消息']     = $历史消息;
    print_R(json_encode($RS));
    exit;
  }
  else {
    $RS = [];
    $RS['data']     = [];
    $RS['message']  = $名称;
    $RS['module']   = 'status';
    //$RS['DeepSeekAiChatResultJSON']  = $DeepSeekAiChatResultJSON;
    $RS['构建提示词语']  = $构建提示词语;
    $RS['历史消息']     = $历史消息;
    print_R(json_encode($RS));
    exit;
  }
}


if($用户输入 != "" && $_GET['action'] == 'content' && $模块 != '')   {
  //获得具体SQL语句
  $当前学期      = getCurrentXueQi();
  $sql          = "select * from data_ai_dashboard where 名称='".$模块."'";
  $rs           = $db->Execute($sql) or print $sql;
  $rs_a         = $rs->GetArray();
  $Item         = $rs_a[0];
  $提示词语 = $Item['提示词语'];
  $提示词语 = str_replace("[数据表结构]", base64_decode($Item['数据表结构']), $提示词语);
  $提示词语 = str_replace("[当前学期]", $当前学期, $提示词语);
  $提示词语 = str_replace("[当前日期]", date('Y-m-d'), $提示词语);
  $提示词语 = str_replace("[我的用户名]", "810120", $提示词语);
  $提示词语 = str_replace("[我的学号]", "230401001", $提示词语);
  $提示词语 = str_replace("[我管理的班级]", "", $提示词语);
  $提示词语 = str_replace("[我所教课的班级]", "", $提示词语);
  $提示词语 = str_replace("[我所教课的课程]", "", $提示词语);
  $构建提示词语 = $用户输入;

  switch($AppModel) {
    case 'DeepSeekChat':
      $DeepSeekAiChatResult = DeepSeekAiChat($提示词语, $构建提示词语, $历史消息, $temperature, $IsStream='false', $备注);
      break;
    case 'OpenKeyCloud':
      $DeepSeekAiChatResult = OpenKeyCloudAiChat($提示词语, $构建提示词语, $历史消息, $temperature, $IsStream='false', $备注);
      break;
    default:
      $DeepSeekAiChatResult = OpenKeyCloudAiChat($提示词语, $构建提示词语, $历史消息, $temperature, $IsStream='false', $备注);
      break;
  }
  $DeepSeekAiChatResultJSON = json_decode($DeepSeekAiChatResult, true);
  $SQLJSON结果 = $DeepSeekAiChatResultJSON['choices'][0]['message']['content'];
  $SQLJSON结果 = str_replace("json", "", $SQLJSON结果);
  $SQLJSON结果 = str_replace("```", "", $SQLJSON结果);
  $SQLJSON结果 = trim($SQLJSON结果);
  $SQLJSON     = json_decode($SQLJSON结果, true);

  $ChartType = $SQLJSON['type'];
  $ChartSql = $SQLJSON['sql'];
  $ChartName = $SQLJSON['name'];

  if($DeepSeekAiChatResultJSON == null)  {
    $RS = [];
    $RS['data']     = [];
    $RS['message']  = '没有从AI模型获得到对应的数据库查询条件';
    $RS['module']   = 'msg';
    $RS['DeepSeekAiChatResultJSON']  = $DeepSeekAiChatResultJSON;
    $RS['构建提示词语']   = $构建提示词语;
    $RS['提示词语']       = $提示词语;
    print_R(json_encode($RS));
    exit;
  }

  if($ChartSql == "")  {
    $RS = [];
    $RS['data']     = [];
    $RS['message']  = '没有获得到对应的数据库查询条件. ' . $SQLJSON结果;
    $RS['module']   = 'msg';
    $RS['DeepSeekAiChatResultJSON']  = $DeepSeekAiChatResultJSON;
    $RS['构建提示词语']  = $构建提示词语;
    $RS['提示词语']     = $提示词语;
    print_R(json_encode($RS));
    exit;
  }

  if(!in_array($ChartType, ['table','line','bar','pie']))  {
    $RS = [];
    $RS['data']     = [];
    $RS['message']  = '只支持表格, 线状图, 柱状图, 饼状图, 不支持: '.$ChartType;
    $RS['module']   = 'msg';
    $RS['DeepSeekAiChatResultJSON']  = $DeepSeekAiChatResultJSON;
    $RS['构建提示词语']  = $构建提示词语;
    $RS['提示词语']     = $提示词语;
    print_R(json_encode($RS));
    exit;
  }

  //开始执行SQL语句
  $sql          = "select * from data_datasource where id='".$Item['数据源']."'";
  $rs           = $db->Execute($sql) or print_R($Item);
  $rs_a         = $rs->GetArray();
  $Item         = $rs_a[0];
  $db_remote = NewADOConnection($DB_TYPE='mysqli');
  $db_remote->connect($Item['数据库主机'], $Item['数据库用户名'], DecryptID($Item['数据库密码']), $Item['数据库名称']);
  $db_remote->Execute("Set names utf8;");
  $db_remote->setFetchMode(ADODB_FETCH_ASSOC);

  if(!$db_remote)  {
    $RS = [];
    $RS['data']     = [];
    $RS['message']  = '远程数据源连接失败';
    $RS['module']   = 'msg';
    $RS['DeepSeekAiChatResultJSON']  = $DeepSeekAiChatResultJSON;
    print_R(json_encode($RS));
    exit;
  }

  $rs   = $db_remote->Execute($ChartSql) or 远程数据库SQL执行错误("数据库执行错误: ".$ChartSql);
  $rs_a = $rs->GetArray();
  $RS = [];
  $RS['sql']       = $ChartSql;
  $RS['data']      = $rs_a;
  $RS['module']    = $ChartType;
  $RS['message']   = sizeof($rs_a) == 0 ? '没有获取到数据,您可以尝试一下其它的查询条件.' : '';
  $RS['提示词语1']  = $构建提示词语;
  $RS['提示词语2']  = $提示词语;
  if($ChartType == 'line' || $ChartType == 'bar' || $ChartType == 'pie')  {
    $dataX = [];
    $dataY = [];
    foreach($rs_a as $Item) {
      $Keys   = array_keys($Item);
      $Values = array_values($Item);
      $dataX[] = $Values[0];
      $dataY[] = $Values[1];

    }
    $ChartJson = [];
    $ChartJson['Title']     = "";
    $ChartJson['SubTitle']  = $ChartName;
    $ChartJson['TopRightOptions']   = [];
    $ChartJson['dataX']     = $dataX;
    $ChartJson['dataY']     = [['name'=>'统计图表', 'data'=>$dataY]];
    $ChartJson['type']      = 'ApexLineChart';
    $ChartJson['grid']      = 8;
    $RS['Chart']            = $ChartJson;
  }
  print_R(json_encode($RS));
  exit;

}

function OpenKeyCloudAiChat($系统模板, $用户输入, $历史消息, $temperature, $IsStream, $备注)     {
  global $模型信息;
  $curl 		  = curl_init();
  $messages 	= [];
  $messages[] = ['content'=>$系统模板, 'role'=>'system'];
  foreach($历史消息 as $消息) {
    $messages[] = ['content'=>$消息[0], 'role'=>'user'];
    $messages[] = ['content'=>"", 'role'=>'assistant'];
  }
  $messages[] = ['content'=>$用户输入, 'role'=>'user'];
  //print_R($messages);
  curl_setopt_array($curl, array(
      CURLOPT_URL => $模型信息['API'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
      "messages": '.json_encode($messages).',
      "model": "'.$模型信息['Model'].'",
      "max_tokens": 512,
      "stop": null,
      "stream": '.$IsStream.',
      "temperature": '.$temperature.'
      }',
      CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Accept: application/json',
          'Authorization: Bearer '.$模型信息['Token']
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
      echo '执行错误Error: ' . curl_error($curl);
    }
    curl_close($curl);
  }
  else {
    $result = curl_exec($curl);
    if (curl_errno($curl)) {
      echo '执行错误Error: ' . curl_error($curl);
    }
    curl_close($curl);
    return $result;
  }
}

function DeepSeekAiChat($系统模板, $用户输入, $历史消息, $temperature, $IsStream, $备注)     {
  $curl 		  = curl_init();
  $messages 	= [];
  $messages[] = ['content'=>$系统模板, 'role'=>'system'];
  foreach($历史消息 as $消息) {
    $messages[] = ['content'=>$消息[0], 'role'=>'user'];
    $messages[] = ['content'=>"", 'role'=>'assistant'];
  }
  $messages[] = ['content'=>$用户输入, 'role'=>'user'];
  //print_R($messages);
  curl_setopt_array($curl, array(
      CURLOPT_URL => $模型信息['API'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
      "messages": '.json_encode($messages).',
      "model": "'.$模型信息['Model'].'",
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
          'Authorization: Bearer ' . $模型信息['Token']
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
      echo '执行错误Error: ' . curl_error($curl);
    }
    curl_close($curl);
  }
  else {
    $result = curl_exec($curl);
    if (curl_errno($curl)) {
      echo '执行错误Error: ' . curl_error($curl);
    }
    curl_close($curl);
    return $result;
  }
}

function 远程数据库SQL执行错误($message) {
  $RS = [];
  $RS['data']     = [];
  $RS['message']  = $message;
  $RS['module']   = 'msg';
  print_R(json_encode($RS));
  exit;
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
