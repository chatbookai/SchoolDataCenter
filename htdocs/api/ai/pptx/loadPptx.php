<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('pptx.lib.inc.php');

$SLIDEPAGE = 1;

$SLIDE_PATH = "./json/0001/ppt/slides/slide".$SLIDEPAGE.".xml";
$xmlString 	= file_get_contents($SLIDE_PATH);
$xmlString 	= str_replace(':', '____', $xmlString);
//$xml 		= simplexml_load_string($xmlString);

$JsonContent      	= file_get_contents("./json/0001.json");
$JsonData          	= json_decode($JsonContent, true);

$slideMasters = $JsonData['slideMasters'];
$slideLayouts = $JsonData['slideMasters'][0]['slideLayouts'];
//print_R($JsonData['slideMasters'][0]['slideLayouts']);exit;

$绘制单个页面XML 		= 绘制单个页面($JsonData['pages'][intval($SLIDEPAGE-1)]);
$绘制单个页面XML 		= str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $绘制单个页面XML);
$最后输出PPTX_SLIDE1 	= '';
file_put_contents($SLIDE_PATH, $绘制单个页面XML);

print $绘制单个页面XML;


// 使用示例
$source			= './json/0001';  // 要压缩的文件或文件夹路径
$destination 	= './0001.pptx';  // ZIP 文件的输出路径
createZip($source, $destination);



?>
