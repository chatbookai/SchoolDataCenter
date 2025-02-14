<?php

require_once('config.inc.php');

$HTTP_ORIGIN    = $_SERVER['HTTP_ORIGIN'];
if (in_array($HTTP_ORIGIN, $allowedOrigins)) {
    //header("Access-Control-Allow-Origin:" . $HTTP_ORIGIN);
}
header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken");
header("Content-type: text/html; charset=utf-8");
header('Cache-Control: no-cache');

?>
