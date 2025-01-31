<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING); 
require_once('config.inc.php');
require_once('./AiToPPTX/include.inc.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");

$payload            = file_get_contents('php://input');
$_POST              = json_decode($payload,true);

$pptId              = FilterString($_POST['id']);

$pptxProperty       = $redis->hGet("PPTX_DOWNLOAD_".date('Ymd'), $pptId);
$gzData             = base64_decode($pptxProperty);
$jsonData           = gzdecode($gzData);
$originalData       = json_decode($jsonData, true);

$TargetCacheDir 		= realpath("./cache");
$TargetPptxFilePath = './output/'.$pptId.'.pptx';

if($originalData)   {
  AiToPptx_MakePptx($originalData, $TargetCacheDir, $TargetPptxFilePath);
  $fileUrl = 'downloadPptxFile.php?id=' . $pptId;
  echo json_encode(['success' => true, 'data' => ['fileUrl' => $fileUrl]]);
}

exit;

?>
