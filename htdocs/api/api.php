<?php
header("Content-Type: application/json");
require_once('cors.php');
require_once('include.inc.php');

$payload        = file_get_contents('php://input');
$_POST          = json_decode($payload,true);
$Data           = $_POST['Data'];

//$_POST['Model']                 = "TVE9PQ==";
//$_POST['Page']                  = 0;
//$_SERVER['HTTP_AUTHORIZATION']  = "F1E985B98FA504232F2BEF58CC0D357C";

$Model      = intval(base64_decode(base64_decode($_POST['Model'])));
$Page       = intval($_POST['Page']);
$Datetime   = intval($_POST['Datetime']);
$HTTP_AUTHORIZATION = $_SERVER['HTTP_AUTHORIZATION'];
if($HTTP_AUTHORIZATION == "") {
  $RS         = [];
  $RS['status']   = "Error";
  $RS['message']  = "Authorization值未设置";
  $RS['time']   = date('Y-m-d H:i:s');
  print_R(json_encode($RS));
  exit;
}

if($Model > 0)  {
  $sql          = "select * from data_api where id = '$Model'";
  $rs           = $db->Execute($sql);
  $ApiInfo      = $rs->fields;
  $Setting      = $ApiInfo['Setting'];
  $FormId       = intval($ApiInfo['FormId']);
  $ExpireTime   = $ApiInfo['ExpireTime'];
  $Token        = $ApiInfo['Token'];
  $AddSql       = $ApiInfo['AddSql'];
  $ApiName      = $ApiInfo['ApiName'];
  $PageCount    = intval($ApiInfo['PageCount']);
  $签名         = md5($Datetime."|".$Token."|".$_POST['Model']."|".$Page);

  if($Setting == '' || $Token == '') {
    $RS             = [];
    $RS['status']   = "Error";
    $RS['message']  = "Setting or Token 没有设置";
    $RS['time']     = date('Y-m-d H:i:s');
    print_R(json_encode($RS));
    ApiLogRecord($Model, $ApiName, $RS['message'], $RS['status'], $HTTP_AUTHORIZATION, $sql='');
    exit;
  }

  if($签名 != $HTTP_AUTHORIZATION) {
    $RS             = [];
    $RS['status']   = "Error";
    $RS['message']  = "签名无效";
    $RS['Datetime'] = $Datetime;
    $RS['Model']    = $_POST['Model'];
    $RS['Page']     = $_POST['Page'];
    $RS['HTTP_AUTHORIZATION']   = $HTTP_AUTHORIZATION;
    $RS['time']     = date('Y-m-d H:i:s');
    print_R(json_encode($RS));
    ApiLogRecord($Model, $ApiName, $RS['message'], $RS['status'], $HTTP_AUTHORIZATION, $sql='');
    exit;
  }

  if($ExpireTime < date('Y-m-d')) {
    $RS         = [];
    $RS['status']   = "Error";
    $RS['message']  = "Token值过期,请联系管理员进行重新设置";
    $RS['time']   = date('Y-m-d H:i:s');
    print_R(json_encode($RS));
    ApiLogRecord($Model, $ApiName, $RS['message'], $RS['status'], $HTTP_AUTHORIZATION, $sql='');
    exit;
  }

  $sql        = "select * from form_formname where id='$FormId'";
  $rs         = $db->Execute($sql);
  $FormInfo   = $rs->fields;
  $TableName  = $FormInfo['TableName'];

  $sql        = "select COUNT(*) AS NUM from $TableName where 1=1 $AddSql";
  $rs         = $db->Execute($sql);
  $FormInfo   = $rs->fields;
  $total      = $FormInfo['NUM'];

  $From       = $Page * $PageCount;
  $sql        = "select $Setting from $TableName where 1=1 $AddSql limit $From,$PageCount";
  $rs         = $db->Execute($sql);
  $rs_a       = $rs->GetArray();

  $RS             = [];
  $RS['status']   = "OK";
  $RS['message']  = "成功";
  $RS['data']     = $rs_a;
  $RS['total']    = $total;
  $RS['time']     = date('Y-m-d H:i:s');
  print_R(base64_encode(gzcompress(gzcompress(json_encode($RS)))));
  ApiLogRecord($Model, $ApiName, $RS['message'], $RS['status'], $HTTP_AUTHORIZATION, $sql);
  exit;
}
else {
  $RS             = [];
  $RS['status']   = "Error";
  $RS['message']  = "Model值错误";
  $RS['time']     = date('Y-m-d H:i:s');
  print_R(json_encode($RS));
  exit;
}


function ApiLogRecord($ApiId, $ApiName='', $AccessAction='', $Status='', $Token='', $sql='') {
	global $db,$GLOBAL_USER;
	global $FormId,$FormName,$FlowId,$FlowName;
	$Element 					        = [];
	$Element['id'] 				    = NULL;
	$Element['ApiId'] 				= addslashes($ApiId);
	$Element['ApiName'] 			= addslashes($ApiName);
	$Element['AccessAction'] 	= addslashes($AccessAction);
	$Element['AccessTime'] 		= date("Y-m-d H:i:s");
	$Element['REMOTE_ADDR'] 	= addslashes($_SERVER['REMOTE_ADDR']);
	$Element['HTTP_USER_AGENT'] = base64_encode($_SERVER['HTTP_USER_AGENT']);
	$Element['QUERY_STRING'] 	= addslashes($_SERVER['QUERY_STRING']);
	$Element['SCRIPT_NAME'] 	= addslashes($_SERVER['SCRIPT_NAME']);
	$Element['Token'] 				= addslashes(Substr($Token, 0 , 10)."...");
	$Element['SqlText'] 			= addslashes($sql);
	$Element['Status'] 			  = addslashes($Status);
	$sql = "insert into data_api_log(".join(",",array_keys($Element)).") values('".join("','",array_values($Element))."');";
	$db->Execute($sql);
}

?>
