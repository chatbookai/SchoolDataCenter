<?php
header("Content-Type: application/json"); 
require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$payload    = file_get_contents('php://input');
$_POST      = json_decode($payload, true);


$RS = [];
$RS['status']   = "OK";

$sql    = "select count(*) AS NUM from data_user";
$rs     = $db->CacheExecute(180,$sql);
$rs_a   = $rs->GetArray();
$RS['data']['user']['count']= $rs_a[0]['NUM'];

$sql    = "select count(*) AS NUM from data_banji";
$rs     = $db->CacheExecute(180,$sql);
$rs_a   = $rs->GetArray();
$RS['data']['banji']['count']= $rs_a[0]['NUM'];

$sql    = "select count(*) AS NUM from data_student";
$rs     = $db->CacheExecute(180,$sql);
$rs_a   = $rs->GetArray();
$RS['data']['student']['count']= $rs_a[0]['NUM'];


$RS['msg']      = "获取远程数据成功";
print json_encode($RS);

?>