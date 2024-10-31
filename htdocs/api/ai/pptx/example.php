<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('./AiToPPTX/include.inc.php');

// 导入原始数据
$JsonContent      	= file_get_contents("./json/0001.json");
$JsonData          	= json_decode($JsonContent, true);
//print_R($JsonData);exit;

$TargetCacheDir 		= "./json/0001";
$TargetPptxFilePath = '0002.pptx';

AiToPptx_MakePptx($JsonData, $TargetCacheDir, $TargetPptxFilePath)


?>
