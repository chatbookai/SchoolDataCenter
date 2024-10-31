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

$写入文件目录 		= "./json/0001";

// 确保子文件夹都存在
if(!is_dir($写入文件目录."/_rels")) 		mkdir($写入文件目录."/_rels");
if(!is_dir($写入文件目录."/docProps")) 	mkdir($写入文件目录."/docProps");
if(!is_dir($写入文件目录."/ppt")) 		mkdir($写入文件目录."/ppt");
if(!is_dir($写入文件目录."/ppt/_rels")) 	mkdir($写入文件目录."/ppt/_rels");
if(!is_dir($写入文件目录."/ppt/media")) 	mkdir($写入文件目录."/ppt/media");
if(!is_dir($写入文件目录."/ppt/theme")) 	mkdir($写入文件目录."/ppt/theme");
if(!is_dir($写入文件目录."/ppt/slideLayouts")) 	mkdir($写入文件目录."/ppt/slideLayouts");
if(!is_dir($写入文件目录."/ppt/slideMasters")) 	mkdir($写入文件目录."/ppt/slideMasters");
if(!is_dir($写入文件目录."/ppt/slides")) 			mkdir($写入文件目录."/ppt/slides");
if(!is_dir($写入文件目录."/ppt/theme/_rels")) 	mkdir($写入文件目录."/ppt/theme/_rels");
if(!is_dir($写入文件目录."/ppt/slideLayouts/_rels")) 	mkdir($写入文件目录."/ppt/slideLayouts/_rels");
if(!is_dir($写入文件目录."/ppt/slideMasters/_rels")) 	mkdir($写入文件目录."/ppt/slideMasters/_rels");
if(!is_dir($写入文件目录."/ppt/slides/_rels")) 		mkdir($写入文件目录."/ppt/slides/_rels");
if(!is_dir($写入文件目录."/ppt/theme/_rels")) 		mkdir($写入文件目录."/ppt/theme/_rels");


// 生成每个Slide页面
$pages = $JsonData['pages'];
for($i=0;$i<sizeof($pages);$i++) {
	$FilePath 			= "./json/0001/ppt/slides/slide".($i+1).".xml";
	$RelationPath 		= "./json/0001/ppt/slides/_rels/slide".($i+1).".xml.rels";
	$绘制单个页面XML 		= 绘制单个页面($JsonData['pages'][$i], $FilePath, $RelationPath);
}

//生成slideMaster页面,也有可能会有多个页面
$slideMasters 		= $JsonData['slideMasters'];
$MakeMasterXmlData 	= MakeMasterXml($JsonData['slideMasters'], $写入文件目录);

// 生成theme页面,也有可能会有多个页面
$slideMasters 		= $JsonData['slideMasters'];
$MakeThemeXmlData 	= MakeThemeXml($JsonData['slideMasters'], $写入文件目录);

// 生成slideLayouts页面
$slideLayouts = (array)$JsonData['slideMasters'][0]['slideLayouts'];
for($i=0;$i<sizeof($slideLayouts);$i++) {
	$MakeSlideLayoutData = MakeSlideLayout($slideLayouts[$i], $写入文件目录."/ppt/slideLayouts/slideLayout".($i+1).".xml", $写入文件目录."/ppt/slideLayouts/_rels/slideLayout".($i+1).".xml.rels");
	//print $MakeSlideLayoutData;
}

// 生成 /ppt/presentation.xml
MakePresentationXml($JsonData, $写入文件目录);


// 生成 /ppt/_rels/presentation.xml.rels
MakePresentationXmlRelations($JsonData, $写入文件目录);

// 复制必备文件
copy("./lib/xml/presProps.xml", $写入文件目录."/ppt/presProps.xml");
copy("./lib/xml/tableStyles.xml", $写入文件目录."/ppt/tableStyles.xml");
copy("./lib/xml/theme.xml", $写入文件目录."/ppt/theme.xml");
copy("./lib/xml/viewProps.xml", $写入文件目录."/ppt/viewProps.xml");
copy("./lib/xml/app.xml", $写入文件目录."/docProps/app.xml");
copy("./lib/xml/core.xml", $写入文件目录."/docProps/core.xml");


// 生成 /ppt/_rels/presentation.xml.rels
MakePresentationXmlRelations($JsonData, $写入文件目录);


// 生成 /_rels/.rels
MakeRootRelations($JsonData, $写入文件目录);

// 生成 /Content_Types.xml
MakeContentTypesXml($JsonData, $写入文件目录);


// 压缩所有文件,并且生成PPTX
$source			= './json/0001';  // 要压缩的文件或文件夹路径
$destination 	= './0001.pptx';  // ZIP 文件的输出路径
createZip($source, $destination);


?>
