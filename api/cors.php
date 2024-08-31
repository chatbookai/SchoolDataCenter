<?php

require_once('config.inc.php');

$HTTP_ORIGIN    = $_SERVER['HTTP_ORIGIN'];
if (in_array($HTTP_ORIGIN, $allowedOrigins)) {
    header("Access-Control-Allow-Origin:" . $HTTP_ORIGIN);
}

header('Access-Control-Allow-Methods:GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Authorization, satoken");
header("Content-type: text/html; charset=utf-8");

?>