<?php
require_once('config.inc.php');
require_once('./AiToPPTX/include.inc.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");

$payload            = file_get_contents('php://input');
$_POST              = json_decode($payload,true);

$pptId              = intval($_GET['id']);

$pptxProperty       = $redis->hGet("PPTX_DOWNLOAD", $pptId);
$gzData             = base64_decode($pptxProperty);
$jsonData           = gzdecode($gzData);
$originalData       = json_decode($jsonData, true);

$TargetCacheDir 		= realpath("./cache");
$TargetPptxFilePath = './output/'.$pptId.'.pptx';

AiToPptx_MakePptx($originalData, $TargetCacheDir, $TargetPptxFilePath);

// 检查文件是否存在
if (!file_exists($TargetPptxFilePath)) {
    die("文件不存在");
}

// 获取文件名
$fileName = basename($TargetPptxFilePath);

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
