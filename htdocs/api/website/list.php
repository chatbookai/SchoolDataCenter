<?php
require_once('../cors.php');
header('Cache-Control: no-cache');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../include.inc.php');

$payload  = file_get_contents('php://input');
$_POST    = json_decode($payload,true);

$类别     = ForSqlInjection($_POST['type']);

$类别     = "学校领导";

$sql      = "select id,标题,类别,发布状态,发布部门,创建人,创建时间,阅读次数 from data_schoolnews where 类别 = '$类别' order by 创建时间 desc";
$rs       = $db->Execute($sql) or print $sql;
$rs_a     = (array)$rs->GetArray();

$RS           = [];
$RS['status'] = 'ok';
$RS['sql']    = $sql;
$RS['data']   = $rs_a;

print_R(json_encode($RS));

?>
