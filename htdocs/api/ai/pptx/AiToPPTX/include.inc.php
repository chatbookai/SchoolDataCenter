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
	if(!is_dir($TargetCacheDir."/_rels")) 		mkdir($TargetCacheDir."/_rels");
	if(!is_dir($TargetCacheDir."/docProps")) 	mkdir($TargetCacheDir."/docProps");
	if(!is_dir($TargetCacheDir."/ppt")) 		mkdir($TargetCacheDir."/ppt");
	if(!is_dir($TargetCacheDir."/ppt/_rels")) 	mkdir($TargetCacheDir."/ppt/_rels");
	if(!is_dir($TargetCacheDir."/ppt/media")) 	mkdir($TargetCacheDir."/ppt/media");
	if(!is_dir($TargetCacheDir."/ppt/theme")) 	mkdir($TargetCacheDir."/ppt/theme");
	if(!is_dir($TargetCacheDir."/ppt/slideLayouts")) 	mkdir($TargetCacheDir."/ppt/slideLayouts");
	if(!is_dir($TargetCacheDir."/ppt/slideMasters")) 	mkdir($TargetCacheDir."/ppt/slideMasters");
	if(!is_dir($TargetCacheDir."/ppt/slides")) 			mkdir($TargetCacheDir."/ppt/slides");
	if(!is_dir($TargetCacheDir."/ppt/theme/_rels")) 	mkdir($TargetCacheDir."/ppt/theme/_rels");
	if(!is_dir($TargetCacheDir."/ppt/slideLayouts/_rels")) 	mkdir($TargetCacheDir."/ppt/slideLayouts/_rels");
	if(!is_dir($TargetCacheDir."/ppt/slideMasters/_rels")) 	mkdir($TargetCacheDir."/ppt/slideMasters/_rels");
	if(!is_dir($TargetCacheDir."/ppt/slides/_rels")) 		mkdir($TargetCacheDir."/ppt/slides/_rels");
	if(!is_dir($TargetCacheDir."/ppt/theme/_rels")) 		mkdir($TargetCacheDir."/ppt/theme/_rels");


	// 生成每个Slide页面
	$pages = $JsonData['pages'];
	for($i=0;$i<sizeof($pages);$i++) {
		$FilePath 			= "./json/0001/ppt/slides/slide".($i+1).".xml";
		$RelationPath 		= "./json/0001/ppt/slides/_rels/slide".($i+1).".xml.rels";
		AiToPptx_MakeSingleSlide($JsonData['pages'][$i], $FilePath, $RelationPath);
	}

	//生成slideMaster页面,也有可能会有多个页面
	$slideMasters 		= $JsonData['slideMasters'];
	$MakeMasterXmlData 	= AiToPptx_MakeMasterXml($JsonData['slideMasters'], $TargetCacheDir);

	// 生成theme页面,也有可能会有多个页面
	$slideMasters 		= $JsonData['slideMasters'];
	$MakeThemeXmlData 	= AiToPptx_MakeThemeXml($JsonData['slideMasters'], $TargetCacheDir);

	// 生成slideLayouts页面
	$slideLayouts = (array)$JsonData['slideMasters'][0]['slideLayouts'];
	for($i=0;$i<sizeof($slideLayouts);$i++) {
		$MakeSlideLayoutData = AiToPptx_MakeSlideLayout($slideLayouts[$i], $TargetCacheDir."/ppt/slideLayouts/slideLayout".($i+1).".xml", $TargetCacheDir."/ppt/slideLayouts/_rels/slideLayout".($i+1).".xml.rels");
		//print $MakeSlideLayoutData;
	}

	// 生成 /ppt/presentation.xml
	AiToPptx_MakePresentationXml($JsonData, $TargetCacheDir);


	// 生成 /ppt/_rels/presentation.xml.rels
	AiToPptx_MakePresentationXmlRelations($JsonData, $TargetCacheDir);

	// 复制必备文件
	copy(__DIR__."/xml/presProps.xml", $TargetCacheDir."/ppt/presProps.xml");
	copy(__DIR__."/xml/tableStyles.xml", $TargetCacheDir."/ppt/tableStyles.xml");
	copy(__DIR__."/xml/theme.xml", $TargetCacheDir."/ppt/theme.xml");
	copy(__DIR__."/xml/viewProps.xml", $TargetCacheDir."/ppt/viewProps.xml");
	copy(__DIR__."/xml/app.xml", $TargetCacheDir."/docProps/app.xml");
	copy(__DIR__."/xml/core.xml", $TargetCacheDir."/docProps/core.xml");

	// 生成 /ppt/_rels/presentation.xml.rels
	AiToPptx_MakePresentationXmlRelations($JsonData, $TargetCacheDir);

	// 生成 /_rels/.rels
	AiToPptx_MakeRootRelations($JsonData, $TargetCacheDir);

	// 生成 /Content_Types.xml
	AiToPptx_MakeContentTypesXml($JsonData, $TargetCacheDir);

	// 压缩所有文件,并且生成PPTX
	AiToPptx_CreateZip($TargetCacheDir, $TargetPptxFilePath);

}

?>
