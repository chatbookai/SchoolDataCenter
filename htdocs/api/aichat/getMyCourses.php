<?php
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$USER_ID = $GLOBAL_USER->USER_ID;

$当前学期  = getCurrentXueQi();

if($GLOBAL_USER->type=="User") {
  $USER_ID  = "810128";
  $sql      = "select 班级名称, 教师姓名, 课程名称, 考核, 教师用户名 from data_execplan where 学期名称='$当前学期' and 教师用户名='$USER_ID'";
}
if($GLOBAL_USER->type=="Student") {
  $sql      = "select 班级名称, 教师姓名, 课程名称, 考核, 教师用户名  from data_execplan where 学期名称='$当前学期' and 班级名称='".$GLOBAL_USER->班级."'";
}
if($sql != "")   {
  $rs     = $db->Execute($sql);
  $rs_a   = $rs->GetArray();
  $RS     = [];
  $RS['status'] = "OK";
  $RS['msg']    = __("Success");
  $RS['data']   = $rs_a;
  $RS['_GET']   = $_GET;
  $RS['_POST']  = $_POST;
  print json_encode($RS);
  exit;

}
else {
  $RS = [];
  $RS['status'] = "ERROR";
  $RS['msg']    = __("Error Id Value");
  $RS['_GET']   = $_GET;
  $RS['_POST']  = $_POST;
  print json_encode($RS);
  exit;
}


?>
