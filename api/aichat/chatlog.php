<?php
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$USER_ID = $GLOBAL_USER->USER_ID;


$payload        = file_get_contents('php://input');
$_POST          = json_decode($payload,true);


if($_POST['action'] == 'deleteByChatApp')  {
  $RS     = [];
  $RS['status'] = "OK";
  $RS['msg']    = __("Success");
  print json_encode($RS);
  exit;
}

if($_POST['action'] == 'deleteByChatId')  {
  $RS     = [];
  $RS['status'] = "OK";
  $RS['msg']    = __("Success");
  print json_encode($RS);
  exit;
}

if($_POST['action'] == 'getChatList')  {
  $当前学期  = getCurrentXueQi();

  $rs_a   = [];
  $RS     = [];
  $RS['status'] = "OK";
  $RS['msg']    = __("Success");
  $RS['data']   = $rs_a;
  print json_encode($RS);
  exit;
}


?>
