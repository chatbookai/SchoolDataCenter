<?php
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$USER_ID = $GLOBAL_USER->USER_ID;

$payload        = file_get_contents('php://input');
$_POST          = json_decode($payload,true);


if($_GET['action'] == 'getAppList')  {
  $sql          = "select * from data_ai_app order by OrderId asc";
  $rs           = $db->Execute($sql);
  $rs_a         = $rs->GetArray();
  $RS           = [];
  $RS['data']   = [['title'=>'通用对话', 'children'=>$rs_a]];
  $RS['status'] = "OK";
  $RS['msg']    = __("Success");
  print json_encode($RS);
  exit;
}

?>
