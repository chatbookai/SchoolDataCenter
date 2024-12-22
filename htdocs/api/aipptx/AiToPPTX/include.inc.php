<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

require_once(__DIR__.'/lib/functions.inc.php');
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

//把JSON数据转换为PPTX文件
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
		$FilePath 			  = $TargetCacheDir."/ppt/slides/slide".($i+1).".xml";
		$RelationPath 		= $TargetCacheDir."/ppt/slides/_rels/slide".($i+1).".xml.rels";
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

//把Markdown转为JSON Data
function Markdown_To_JsonData($MarkdownData, $JsonData) {
  //非空处理
  $FullResponeTextArray = explode("\n", $MarkdownData);
  $FullResponeTextArrayNotNullLine = [];
  foreach($FullResponeTextArray as $Item) {
    if(trim($Item)!="") {
      $FullResponeTextArrayNotNullLine[] = trim($Item);
    }
  }

  //转为MAP
  $Map    = [];
  $PPTX标题 = "";
  $章节标题 = "";
  $小节标题 = "";
  $小节内容 = "";
  foreach($FullResponeTextArrayNotNullLine as $Item) {
    if(substr($Item, 0, 2) == '# ') {
      $PPTX标题     = $Item;
      //$Map['标题']  = $PPTX标题;
    }
    else if(substr($Item, 0, 3) == '## ') {
      $章节标题 = $Item;
      //$Map['章节'][$章节标题] = $章节标题;
    }
    else if(substr($Item, 0, 4) == '### ') {
      $小节标题 = $Item;
      //$Map['小节'][$章节标题][] = $小节标题;
    }
    else {
      $Map[$PPTX标题][$章节标题][$小节标题][] = $Item;
    }
  }
  print_R($Map);exit;


}

//把Markdown转换为GenerateContent.php接口中最后一步的JSON Data
function Markdown_To_Generate_Content_Json($FullResponeText) {

  //非空处理
  $FullResponeTextArray = explode("\n", $FullResponeText);
  $FullResponeTextArrayNotNullLine = [];
  foreach($FullResponeTextArray as $Item) {
    if(trim($Item)!="") {
      $FullResponeTextArrayNotNullLine[] = trim($Item);
    }
  }

  //转为MAP
  $Map    = [];
  $PPTX标题 = "";
  $章节标题 = "";
  $小节标题 = "";
  $小节内容 = "";
  foreach($FullResponeTextArrayNotNullLine as $Item) {
    if(substr($Item, 0, 2) == '# ') {
      $PPTX标题     = $Item;
      //$Map['标题']  = $PPTX标题;
    }
    else if(substr($Item, 0, 3) == '## ') {
      $章节标题 = $Item;
      //$Map['章节'][$章节标题] = $章节标题;
    }
    else if(substr($Item, 0, 4) == '### ') {
      $小节标题 = $Item;
      //$Map['小节'][$章节标题][] = $小节标题;
    }
    else {
      $Map[$PPTX标题][$章节标题][$小节标题][] = $Item;
    }
  }
  //print_R($Map);exit;

  //输出为JSON
  $页面JSON列表 = [];
  foreach($Map[$PPTX标题] as $章节名称 => $章节信息) {
    $章节JSON列表 = [];
    foreach($章节信息 as $小节名称 => $小节列表) {
      //print_R($章节名称);
      //print_R($小节列表);
      $小节JSON列表 = [];
      for($i=0;$i<sizeof($小节列表);$i=$i+2) {
        $小节标题 = $小节列表[$i];
        $小节内容 = $小节列表[$i+1];
        //print_R($小节标题);
        //print_R($小节内容);
        $小节JSON = [];
        $小节JSON['level']      = 4;
        $小节JSON['name']       = $小节标题;
        $小节JSON['children']   = [['children'=>[], 'level'=>0, 'type'=>'-', 'name'=>$小节内容]];
        $小节JSON列表[]        = $小节JSON;
      }
      $章节JSON               = [];
      $章节JSON['level']      = 3;
      $章节JSON['name']       = $小节名称;
      $章节JSON['children']   = $小节JSON列表;
      $章节JSON列表[]         = $章节JSON;
    }
    $二级标题JSON               = [];
    $二级标题JSON['level']      = 2;
    $二级标题JSON['name']       = $章节名称;
    $二级标题JSON['children']   = $章节JSON列表;
    $页面JSON列表[]             = $二级标题JSON;
    //print_R($二级标题JSON);
  }

  $最终结构 = [];
  $最终结构['level']      = 1;
  $最终结构['name']       = $PPTX标题;
  $最终结构['children']   = $页面JSON列表;

  return $最终结构;
}

?>
