<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

require_once(__DIR__.'/functions.inc.php');
require_once(__DIR__.'/lib/AiToPptx_DrawGroupObject.php');
require_once(__DIR__.'/lib/AiToPptx_DrawSingleObject.php');
require_once(__DIR__.'/lib/AiToPptx_MakeContentTypesXml.php');
require_once(__DIR__.'/lib/AiToPptx_MakeMasterXml.php');
require_once(__DIR__.'/lib/AiToPptx_MakePresentationXml.php');
require_once(__DIR__.'/lib/AiToPptx_MakePresentationXmlRelations.php');
require_once(__DIR__.'/lib/AiToPptx_MakeRootRelations.php');
require_once(__DIR__.'/lib/AiToPptx_MakeSingleSlide.php');
require_once(__DIR__.'/lib/AiToPptx_MakeSlideLayout.php');
require_once(__DIR__.'/lib/AiToPptx_MakeThemeXml.php');

function AiToPptx_MakePptx($JsonData, $TargetCacheDir, $TargetPptxFilePath) {

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
		AiToPptx_MakeSingleSlide($JsonData['pages'][$i], $FilePath, $RelationPath);
	}

	//生成slideMaster页面,也有可能会有多个页面
	$slideMasters 		= $JsonData['slideMasters'];
	$MakeMasterXmlData 	= AiToPptx_MakeMasterXml($JsonData['slideMasters'], $TargetDir);

	// 生成theme页面,也有可能会有多个页面
	$slideMasters 		= $JsonData['slideMasters'];
	$MakeThemeXmlData 	= AiToPptx_MakeThemeXml($JsonData['slideMasters'], $TargetDir);

	// 生成slideLayouts页面
	$slideLayouts = (array)$JsonData['slideMasters'][0]['slideLayouts'];
	for($i=0;$i<sizeof($slideLayouts);$i++) {
		$MakeSlideLayoutData = AiToPptx_MakeSlideLayout($slideLayouts[$i], $TargetDir."/ppt/slideLayouts/slideLayout".($i+1).".xml", $TargetDir."/ppt/slideLayouts/_rels/slideLayout".($i+1).".xml.rels");
		//print $MakeSlideLayoutData;
	}

	// 生成 /ppt/presentation.xml
	AiToPptx_MakePresentationXml($JsonData, $TargetDir);


	// 生成 /ppt/_rels/presentation.xml.rels
	AiToPptx_MakePresentationXmlRelations($JsonData, $TargetDir);

	// 复制必备文件
	copy("/lib/xml/presProps.xml", $TargetDir."/ppt/presProps.xml");
	copy("/lib/xml/tableStyles.xml", $TargetDir."/ppt/tableStyles.xml");
	copy("/lib/xml/theme.xml", $TargetDir."/ppt/theme.xml");
	copy("/lib/xml/viewProps.xml", $TargetDir."/ppt/viewProps.xml");
	copy("/lib/xml/app.xml", $TargetDir."/docProps/app.xml");
	copy("/lib/xml/core.xml", $TargetDir."/docProps/core.xml");

	// 生成 /ppt/_rels/presentation.xml.rels
	AiToPptx_MakePresentationXmlRelations($JsonData, $TargetDir);


	// 生成 /_rels/.rels
	AiToPptx_MakeRootRelations($JsonData, $TargetDir);

	// 生成 /Content_Types.xml
	AiToPptx_MakeContentTypesXml($JsonData, $TargetDir);


	// 压缩所有文件,并且生成PPTX
	$source			= $TargetCacheDir;  // 要压缩的文件或文件夹路径
	AiToPptx_CreateZip($source, $TargetPptxFilePath);

}

?>
