<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');

//$externalId = 16;

//Get Table Infor
$sql        = "select * from data_interface";
$rs         = $db->CacheExecute(30, $sql);
$Info       = $rs->fields;

print_R(json_encode($Info, true));

?>