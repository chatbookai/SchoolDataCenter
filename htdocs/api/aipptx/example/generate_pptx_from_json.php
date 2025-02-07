<?php
//从JSON格式数据, 生成PPTX文件

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('./../AiToPPTX/include.inc.php');

//确保该文件一定存在
$TargetCacheDir 		= "../cache";
if(!is_dir($TargetCacheDir))   mkdir($TargetCacheDir);

// 导入原始数据
$JsonContent      	= file_get_contents("./../json/地理课程模板.json");
$JsonData          	= json_decode($JsonContent, true);
//print_R($JsonData);exit;

$AiToPptx_DeleteCacheDirectory_Status = true;
$TargetCacheDir 		= "../cache/".date('Ymd_His')."_".rand(1111,9999);
//$TargetCacheDir 		= "../cache";

$TargetPptxFilePath = './../output/地理课程模板.pptx';

AiToPptx_MakePptx($JsonData, $TargetCacheDir, $TargetPptxFilePath)

?>
