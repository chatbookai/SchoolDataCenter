<?php
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$USER_ID = $GLOBAL_USER->USER_ID;

$当前学期  = getCurrentXueQi();

$sql          = "select * from data_ai_app where id = '1'";
$rs           = $db->Execute($sql);
$rs_a         = (array)$rs->GetArray();
$默认设置      = $rs_a[0];

if($GLOBAL_USER->type=="User") {
  $USER_ID  = "810128";
  $sql      = "select id, 班级名称, 教师姓名, 课程名称, 考核, 教师用户名 from data_execplan where 学期名称='$当前学期' and 教师用户名='$USER_ID'";
}
if($GLOBAL_USER->type=="Student") {
  $sql      = "select id, 班级名称, 教师姓名, 课程名称, 考核, 教师用户名  from data_execplan where 学期名称='$当前学期' and 班级名称='".$GLOBAL_USER->班级."'";
}
if($sql != "")   {
  $rs     = $db->Execute($sql);
  $rs_a   = (array)$rs->GetArray();
  //重置结果
  $NewArray = [];
  foreach($rs_a as $Item)  {
    $NewItem              = $默认设置;
    $NewItem['班级名称']   = $Item['班级名称'];
    $NewItem['教师姓名']   = $Item['教师姓名'];
    $NewItem['课程名称']   = $Item['课程名称'];
    $NewItem['考核']        = $Item['考核'];
    $NewItem['教师用户名']   = $Item['教师用户名'];
    //$NewItem['id']          = $Item['id'];
    $NewItem['AppName']     = $Item['课程名称']."-".$Item['班级名称'];
    $NewArray[] = $NewItem;
  }
  $RS     = [];
  $RS['status'] = "OK";
  $RS['msg']    = __("Success");
  $RS['data']   = $NewArray;
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
