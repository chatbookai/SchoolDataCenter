<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('pptx.lib.inc.php');

// 导入原始数据
$JsonContent      	= file_get_contents("./json/0001.json");
$JsonData          	= json_decode($JsonContent, true);
//print_R($JsonData);exit;

$TargetDir 		= "./json/0001";

// 确保子文件夹都存在
if(!is_dir($TargetDir."/_rels")) 		mkdir($TargetDir."/_rels");
if(!is_dir($TargetDir."/docProps")) 	mkdir($TargetDir."/docProps");
if(!is_dir($TargetDir."/ppt")) 		mkdir($TargetDir."/ppt");
if(!is_dir($TargetDir."/ppt/_rels")) 	mkdir($TargetDir."/ppt/_rels");
if(!is_dir($TargetDir."/ppt/media")) 	mkdir($TargetDir."/ppt/media");
if(!is_dir($TargetDir."/ppt/theme")) 	mkdir($TargetDir."/ppt/theme");
if(!is_dir($TargetDir."/ppt/slideLayouts")) 	mkdir($TargetDir."/ppt/slideLayouts");
if(!is_dir($TargetDir."/ppt/slideMasters")) 	mkdir($TargetDir."/ppt/slideMasters");
if(!is_dir($TargetDir."/ppt/slides")) 			mkdir($TargetDir."/ppt/slides");
if(!is_dir($TargetDir."/ppt/theme/_rels")) 	mkdir($TargetDir."/ppt/theme/_rels");
if(!is_dir($TargetDir."/ppt/slideLayouts/_rels")) 	mkdir($TargetDir."/ppt/slideLayouts/_rels");
if(!is_dir($TargetDir."/ppt/slideMasters/_rels")) 	mkdir($TargetDir."/ppt/slideMasters/_rels");
if(!is_dir($TargetDir."/ppt/slides/_rels")) 		mkdir($TargetDir."/ppt/slides/_rels");
if(!is_dir($TargetDir."/ppt/theme/_rels")) 		mkdir($TargetDir."/ppt/theme/_rels");


// 生成每个Slide页面
$pages = $JsonData['pages'];
for($i=0;$i<sizeof($pages);$i++) {
	$FilePath 			= "./json/0001/ppt/slides/slide".($i+1).".xml";
	$RelationPath 		= "./json/0001/ppt/slides/_rels/slide".($i+1).".xml.rels";
	$绘制单个页面XML 		= 绘制单个页面($JsonData['pages'][$i], $FilePath, $RelationPath);
}

//生成slideMaster页面,也有可能会有多个页面
$slideMasters 		= $JsonData['slideMasters'];
$MakeMasterXmlData 	= MakeMasterXml($JsonData['slideMasters'], $TargetDir);

// 生成theme页面,也有可能会有多个页面
$slideMasters 		= $JsonData['slideMasters'];
$MakeThemeXmlData 	= MakeThemeXml($JsonData['slideMasters'], $TargetDir);

// 生成slideLayouts页面
$slideLayouts = (array)$JsonData['slideMasters'][0]['slideLayouts'];
for($i=0;$i<sizeof($slideLayouts);$i++) {
	$MakeSlideLayoutData = MakeSlideLayout($slideLayouts[$i], $TargetDir."/ppt/slideLayouts/slideLayout".($i+1).".xml", $TargetDir."/ppt/slideLayouts/_rels/slideLayout".($i+1).".xml.rels");
	//print $MakeSlideLayoutData;
}

// 生成 /ppt/presentation.xml
MakePresentationXml($JsonData, $TargetDir);


// 生成 /ppt/_rels/presentation.xml.rels
MakePresentationXmlRelations($JsonData, $TargetDir);

// 复制必备文件
copy("./lib/xml/presProps.xml", $TargetDir."/ppt/presProps.xml");
copy("./lib/xml/tableStyles.xml", $TargetDir."/ppt/tableStyles.xml");
copy("./lib/xml/theme.xml", $TargetDir."/ppt/theme.xml");
copy("./lib/xml/viewProps.xml", $TargetDir."/ppt/viewProps.xml");
copy("./lib/xml/app.xml", $TargetDir."/docProps/app.xml");
copy("./lib/xml/core.xml", $TargetDir."/docProps/core.xml");


// 生成 /ppt/_rels/presentation.xml.rels
MakePresentationXmlRelations($JsonData, $TargetDir);


// 生成 /_rels/.rels
MakeRootRelations($JsonData, $TargetDir);

// 生成 /Content_Types.xml
MakeContentTypesXml($JsonData, $TargetDir);


// 压缩所有文件,并且生成PPTX
$source			= './json/0001';  // 要压缩的文件或文件夹路径
$destination 	= './0001.pptx';  // ZIP 文件的输出路径
createZip($source, $destination);


?>
