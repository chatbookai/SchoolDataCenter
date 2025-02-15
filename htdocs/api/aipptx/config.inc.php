<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");
header("Content-type: text/html; charset=utf-8");
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING); 

$API_URL    = "https://api.deepseek.com";
$API_KEY    = "sk-a3dafc724335489e94a30f495dcb10d8";
$API_MODE 	= "deepseek-chat";

global $allowedOrigins;
$allowedOrigins = [];
$allowedOrigins[] = 'http://localhost:3000';
$allowedOrigins[] = 'http://localhost:3000/';

// #################################################################################
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$Global_Templates = [
    [
        "id" => "0",
        "subject" => "课程学习汇报"
    ],
    [
        "id" => "1",
        "subject" => "读书分享演示"
    ],
    [
        "id" => "2",
        "subject" => "蓝色通用商务"
    ],
    [
        "id" => "3",
        "subject" => "蓝色工作汇报总结"
    ]
];


function FilterString($input) {
    return preg_replace('/[^a-zA-Z0-9_]/', '', $input);
}


?>
