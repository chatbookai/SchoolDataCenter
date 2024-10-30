<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('pptx.lib.inc.php');

//导入原始数据
$JsonContent      	= file_get_contents("./json/0001.json");
$JsonData          	= json_decode($JsonContent, true);
print_R($JsonData);

$写入文件目录 		= "./json/0001/ppt";

//生成每个Slide页面
$pages = $JsonData['pages'];
for($i=0;$i<sizeof($pages);$i++) {
	//$FilePath 			= "./json/0001/ppt/slides/slide".($i+1).".xml";
	//$绘制单个页面XML 		= 绘制单个页面($JsonData['pages'][$i], $FilePath);
}

//生成slideMaster页面,也有可能会有多个页面
$slideMasters 		= $JsonData['slideMasters'];
$MakeMasterXmlData 	= MakeMasterXml($JsonData['slideMasters'], $写入文件目录);

//生成slideLayouts页面
$slideLayouts = (array)$JsonData['slideMasters'][0]['slideLayouts'];
for($i=0;$i<sizeof($slideLayouts);$i++) {
	$MakeSlideLayoutData = MakeSlideLayout($slideLayouts[$i], $写入文件目录."/ppt/slideLayouts/slideLayout".($i+1).".xml");
	//print $MakeSlideLayoutData;
}

// 压缩所有文件,并且生成PPTX
$source			= './json/0001';  // 要压缩的文件或文件夹路径
$destination 	= './0001.pptx';  // ZIP 文件的输出路径
createZip($source, $destination);


?>
