<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");
header("Content-type: text/html; charset=utf-8");
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

$API_URL 	= "https://api.deepseek.com";
$API_KEY 	= "sk-a3dafc724335489e94a30f495dcb10d8";
$API_MODE 	= "deepseek-chat";

global $allowedOrigins;
$allowedOrigins = [];
$allowedOrigins[] = 'http://localhost:3000';
$allowedOrigins[] = 'http://localhost:3000/';

// #################################################################################
$redis = new Redis();
$redis->connect('127.0.0.1', 16379);

?>
