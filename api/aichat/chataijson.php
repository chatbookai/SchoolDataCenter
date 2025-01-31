<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken");
header("Content-type: text/html; charset=utf-8");
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

if($用户输入 != "" && $appId != "")  {
  //保存到数据
  $appIdArray = explode('-', $appId);
  if($appIdArray[0] == 'ChatApp' && $appIdArray[1] != '')  {
    $AppIdValue = intval($appIdArray[1]);
    $sql  = "select * from data_ai_app where id = '$AppIdValue' ";
    $rs   = $db->Execute($sql);
    $rs_a = $rs->GetArray();
    $AppModel         = $rs_a[0]['AppModel']; //值默认是: DeepSeekChat
    $MaxTokens        = $rs_a[0]['MaxTokens'];
    $TopP             = $rs_a[0]['TopP'];
    $HistoryRecords   = $rs_a[0]['HistoryRecords'];
    $PresencePenalty  = $rs_a[0]['PresencePenalty'];
    $SystemPrompt     = $rs_a[0]['SystemPrompt'];

    switch($AppModel) {
      case 'DeepSeekChat':
        //实时输出结果, 返回结果的JSON不要做解析, 放到客户端进行解析.
        DeepSeekAiChat($SystemPrompt, $用户输入, $历史消息, $temperature);
        break;
    }

  }

  exit;
}

function DeepSeekAiChat($系统模板, $用户输入, $历史消息, $temperature)     {
  global $APIKEY;
  $curl 		  = curl_init();
  $messages 	= [];
  $messages[] = ['content'=>$系统模板, 'role'=>'system'];
  foreach($历史消息 as $消息) {
    $messages[] = ['content'=>$消息[0], 'role'=>'user'];
    $messages[] = ['content'=>$消息[1], 'role'=>'assistant'];
  }
  $messages[] = ['content'=>$用户输入, 'role'=>'user'];
  //print_R($messages);exit;
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
      "stream": false,
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

  $result = curl_exec($curl);

  curl_close($curl);

  print $result;

}

?>
