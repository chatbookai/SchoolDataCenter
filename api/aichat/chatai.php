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

CheckAuthUserLoginStatus();


$USER_ID        = $GLOBAL_USER->USER_ID;

$payload        = file_get_contents('php://input');
$_POST          = json_decode($payload,true);

$temperature    = intval($_POST['temperature']*10)/10;
$历史消息        = (array)$_POST['history'];
$用户输入        = (string)$_POST['question'];
//$系统模板        = (string)$_POST['template'];
$appId          = (string)$_POST['appId'];
$备注           = (string)$_POST['MarkId'];

if($用户输入 != "" && $appId != "")  {
  //保存到数据
  $appIdArray = explode('-', $appId);
  if($appIdArray[0] == 'ChatApp' && $appIdArray[1] != '')  {
    $AppIdValue = intval($appIdArray[1]);
    if($AppIdValue == 0)  {
      print "appId: ".$appId." is invalid";
      exit;
    }
    $sql  = "select * from data_ai_app where id = '$AppIdValue' ";
    $rs   = $db->Execute($sql);
    $rs_a = $rs->GetArray();
    $AppName          = $rs_a[0]['AppName'];
    $AppModel         = $rs_a[0]['AppModel']; //值默认是: DeepSeekChat
    $MaxTokens        = $rs_a[0]['MaxTokens'];
    $TopP             = $rs_a[0]['TopP'];
    $HistoryRecords   = $rs_a[0]['HistoryRecords'];
    $PresencePenalty  = $rs_a[0]['PresencePenalty'];
    $SystemPrompt     = $rs_a[0]['SystemPrompt'];
    $SystemPrompt     .= " 只需要输入一个 \n 就可以，不要输出 \\n \\\n \\\\n \\\\\n 等这样的内容。";

    $sql      = "select * from data_ai_model where Name = '$AppModel' ";
    $rs       = $db->Execute($sql);
    $rs_a     = $rs->GetArray();
    $模型信息  = $rs_a[0];
    //print_R($sql);print_R($模型信息);exit;

    switch($AppModel) {
      case 'DeepSeekChat':
        //实时输出结果, 返回结果的JSON不要做解析, 放到客户端进行解析.
        DeepSeekAiChat($SystemPrompt, $用户输入, $历史消息, $temperature, $AppName, $备注);
        break;
      case 'OpenKeyCloud':
        //实时输出结果, 返回结果的JSON不要做解析, 放到客户端进行解析.
        OpenKeyCloudAiChat($SystemPrompt, $用户输入, $历史消息, $temperature, $AppName, $备注);
        break;
    }

  }

  exit;
}

function DeepSeekAiChat($系统模板, $用户输入, $历史消息, $temperature, $AppName, $备注)     {
  global $模型信息;
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
  //print_R($messages);exit;
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
      "stream": true,
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

  $输出TEXT = "";
  curl_setopt($curl, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$AppName, &$用户输入, &$输出TEXT) {
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
      print_R($输出TEXT);
      保存到数据库($AppName, $用户输入, $输出TEXT, $备注);
    }
    return strlen($data);
  });

  curl_exec($curl);

  if (curl_errno($curl)) {
    echo 'Error: ' . curl_error($curl);
  }

  curl_close($curl);

}

function OpenKeyCloudAiChat($系统模板, $用户输入, $历史消息, $temperature, $AppName, $备注)     {
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
      "stream": true,
      "temperature": '.$temperature.'
      }',
      CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Accept: application/json',
          'Authorization: Bearer '.$模型信息['Token']
      ),
  ));

  $输出TEXT = "";
  curl_setopt($curl, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$AppName, &$用户输入, &$输出TEXT) {
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
      print_R($输出TEXT);
      保存到数据库($AppName, $用户输入, $输出TEXT, $备注);
    }
    return strlen($data);
  });

  curl_exec($curl);
  if (curl_errno($curl)) {
    echo '执行错误Error: ' . curl_error($curl);
  }
  curl_close($curl);

}

function 保存到数据库($AppName, $用户输入, $输出TEXT, $备注)  {
  global $db;
  global $GLOBAL_USER;
  $Element            = [];
  $Element['AI应用']  = $AppName;
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
