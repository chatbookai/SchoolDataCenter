<?php
//从JSON格式数据, 生成PPTX文件

header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('./../AiToPPTX/include.inc.php');

// 导入原始数据
$JsonContent      	= file_get_contents("./../json/课程学习汇报.json");
$JsonData          	= json_decode($JsonContent, true);
//print_R($JsonData);exit;

$TargetCacheDir 		= realpath("./../cache");
$TargetPptxFilePath = './../cache/课程学习汇报.pptx';

AiToPptx_MakePptx($JsonData, $TargetCacheDir, $TargetPptxFilePath)

?>
