<?php
require_once('config.inc.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");
header("Content-Type: application/json; charset=utf-8");
header('Cache-Control: no-cache');

// 处理 OPTIONS 请求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// 导入原始数据
$JsonContent      	= file_get_contents("./json/10001.json");
$JsonData          	= json_decode($JsonContent, true);
//print_R($JsonData);exit;

$pptxProperty = base64_encode(gzencode(json_encode($JsonData)));

$RS             = [];
$RS['code']     = 0;
$RS['message']  = 'ok';
$RS['data']['current'] = 8;
$RS['data']['total'] = 15;
$RS['data']['pptxProperty'] = $pptxProperty;

print_R(json_encode($RS));
?>
