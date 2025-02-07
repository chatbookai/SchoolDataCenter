<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING); 
require_once('config.inc.php');
require_once('./AiToPPTX/include.inc.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");

$payload            = file_get_contents('php://input');
$_POST              = json_decode($payload,true);

$pptId              = FilterString($_GET['id']);

$pptxProperty       = $redis->hGet("PPTX_DOWNLOAD_".date('Ymd'), $pptId);
$gzData             = base64_decode($pptxProperty);
$jsonData           = gzdecode($gzData);
$originalData       = json_decode($jsonData, true);

if($originalData == NULL)  {
    die("PPTX内容不存在");
    exit;
}

$TargetCacheDir 		= realpath("./cache");
$TargetPptxFilePath     = './output/'.$pptId.'.pptx';

if (!file_exists($TargetPptxFilePath)) {
    AiToPptx_MakePptx($originalData, $TargetCacheDir, $TargetPptxFilePath);
}

// 检查文件是否存在
if (!file_exists($TargetPptxFilePath)) {
    die("PPTX文件不存在");
}

// 获取文件名
$fileName   = basename($TargetPptxFilePath);

// 设置 HTTP 头
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($TargetPptxFilePath));

// 读取并输出文件内容
readfile($TargetPptxFilePath);
exit;
?>
