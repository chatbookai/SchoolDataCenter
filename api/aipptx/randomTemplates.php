<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");
header("Content-Type: application/json");
header('Cache-Control: no-cache');

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING); 
require_once('config.inc.php');

$RS = [];
$RS['data'] = $Global_Templates;
$RS['code'] = 0;
$RS['message'] = 'ok';

print json_encode($RS);

?>
