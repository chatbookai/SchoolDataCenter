<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

function AiToPptx_MakeSlideLayout($Layout, $FilePath, $RelationPath) {

	global $关系引用ID值列表SlideLayout;
	$关系引用ID值列表SlideLayout 		= [];
	$关系引用ID值列表SlideLayout[] 	= '<Relationship Id="rId1" Target="../slideMasters/slideMaster1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster"/>';

	// 创建DOM对象并设置XML版本和编码
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
  //print_R($Layout);
	// 创建根元素 <p:sldLayout>
	$sldLayout = $dom->createElementNS(
		'http://schemas.openxmlformats.org/presentationml/2006/main',
		'p:sldLayout'
	);
  if($Layout['type'] == "BLANK")    {
    $sldLayout->setAttribute('type', 'blank');
    $sldLayout->setAttribute('preserve', '1');
  }
  if($Layout['type'] == "TITLE_AND_CONTENT")    {
    $sldLayout->setAttribute('type', 'obj');
    $sldLayout->setAttribute('preserve', '1');
  }
  if($Layout['type'] == "SECTION_HEADER")    {
    $sldLayout->setAttribute('type', 'secHead');
    $sldLayout->setAttribute('preserve', '1');
  }
  if($Layout['type'] == "CUST") {
    $sldLayout->setAttribute('preserve', '1');
    $sldLayout->setAttribute('userDrawn', '1');
  }
  if($Layout['noMaster'] == "1") {
    $sldLayout->setAttribute('showMasterSp', 'false');
  }

	// 注册命名空间前缀
	$sldLayout->setAttributeNS(
		'http://www.w3.org/2000/xmlns/', 'xmlns:a',
		'http://schemas.openxmlformats.org/drawingml/2006/main'
	);
	$sldLayout->setAttributeNS(
		'http://www.w3.org/2000/xmlns/', 'xmlns:r',
		'http://schemas.openxmlformats.org/officeDocument/2006/relationships'
	);
	$sldLayout->setAttributeNS(
		'http://www.w3.org/2000/xmlns/', 'xmlns:p',
		'http://schemas.openxmlformats.org/presentationml/2006/main'
	);

	// 创建子元素 <p:cSld> 并附加到根元素
	$cSld = $dom->createElement('p:cSld');
	$cSld->setAttribute('name', $Layout['name']);

	// 创建 <p:bg> 元素及其子元素
	$bg = $dom->createElement('p:bg');
	$bgPr = $dom->createElement('p:bgPr');
  /*
	$solidFill = $dom->createElement('a:solidFill');
  if($Layout['background']['realType']=="Background" && $Layout['background']['fillStyle']['type']=="color")  {
    if($Layout['background']['fillStyle']['color']['scheme']!="") {
      $schemeClr = $dom->createElement('a:schemeClr');
      $schemeClr->setAttribute('val', $Layout['background']['fillStyle']['color']['scheme']);

      if($Layout['background']['fillStyle']['color']['alpha']) {
        $alpha = $dom->createElement('a:alpha');
        $alpha->setAttribute('val', $Layout['background']['fillStyle']['color']['alpha']);
        $schemeClr->appendChild($alpha);
      }
      if($Layout['background']['fillStyle']['color']['lumMod']) {
        $lumMod = $dom->createElement('a:lumMod');
        $lumMod->setAttribute('val', $Layout['background']['fillStyle']['color']['lumMod']);
        $schemeClr->appendChild($lumMod);
      }
      if($Layout['background']['fillStyle']['color']['lumOff']) {
        $lumOff = $dom->createElement('a:lumOff');
        $lumOff->setAttribute('val', $Layout['background']['fillStyle']['color']['lumOff']);
        $schemeClr->appendChild($lumOff);
      }

      $solidFill->appendChild($schemeClr);
    }
    if($Layout['background']['fillStyle']['color']['color']!="") {
      $srgbClr = $dom->createElement('a:srgbClr');
      if($Layout['background']['fillStyle']['color']['color'] == '-1')  {
				$srgbClr->setAttribute('val', 'FFFFFF');
			}
			else {
				$srgbClr->setAttribute('val', AiToPptx_NumberToColor($Layout['background']['fillStyle']['color']['color']));
			}
      $solidFill->appendChild($srgbClr);
    }
  }
	$bgPr->appendChild($solidFill);
  */

  //得到图片路径信息
  $得到图片路径信息 = explode('/', $FilePath);
  array_pop($得到图片路径信息);
  array_pop($得到图片路径信息);
  $得到图片路径信息[] = 'media';
  $DirPath = join('/', $得到图片路径信息);
  //print $DirPath." AiToPptx_MakeSlideLayout \n";

	$fillStyle 		  = $Layout['background']['fillStyle'];
  $bgPr           = 渲染fillStyle($dom, $fillStyle, $bgPr, $DirPath);
	$strokeStyle 		= $Layout['background']['strokeStyle'];
  $bgPr           = 渲染strokeStyle($dom, $strokeStyle, $bgPr, $DirPath);

	// 组装 <p:bg> 树
	$bg->appendChild($bgPr);
	$cSld->appendChild($bg);

	// 创建 <p:spTree> 结构
	$spTree = $dom->createElement('p:spTree');
	$nvGrpSpPr = $dom->createElement('p:nvGrpSpPr');
	$cNvPr = $dom->createElement('p:cNvPr');
	$cNvPr->setAttribute('id', '1');
	$cNvPr->setAttribute('name', '');
	$cNvGrpSpPr = $dom->createElement('p:cNvGrpSpPr');
	$nvPr = $dom->createElement('p:nvPr');

	// 组装 <p:spTree> 的非可视属性部分
	$nvGrpSpPr->appendChild($cNvPr);
	$nvGrpSpPr->appendChild($cNvGrpSpPr);
	$nvGrpSpPr->appendChild($nvPr);
	$spTree->appendChild($nvGrpSpPr);

	// 创建 <p:grpSpPr> 及其变换属性
	$grpSpPr = $dom->createElement('p:grpSpPr');
	$xfrm = $dom->createElement('a:xfrm');
	$off = $dom->createElement('a:off');
	$off->setAttribute('x', '0');
	$off->setAttribute('y', '0');
	$ext = $dom->createElement('a:ext');
	$ext->setAttribute('cx', '0');
	$ext->setAttribute('cy', '0');
	$chOff = $dom->createElement('a:chOff');
	$chOff->setAttribute('x', '0');
	$chOff->setAttribute('y', '0');
	$chExt = $dom->createElement('a:chExt');
	$chExt->setAttribute('cx', '0');
	$chExt->setAttribute('cy', '0');

	// 组装 <p:grpSpPr>
	$xfrm->appendChild($off);
	$xfrm->appendChild($ext);
	$xfrm->appendChild($chOff);
	$xfrm->appendChild($chExt);
	$grpSpPr->appendChild($xfrm);
	$spTree->appendChild($grpSpPr);

	// 组装 <p:sp>

  foreach ($Layout['children'] as $childrenItem)    {
		$Type 				  = $childrenItem['type'];
		$realType 			= $childrenItem['extInfo']['property']['realType'];
		$rotation 			= $childrenItem['extInfo']['property']['rotation'];
		$groupFillStyle 	= $childrenItem['extInfo']['property']['groupFillStyle'];
		if($realType == "Group") {
			//print_R($childrenItem);
			$绘制元素RESULT 	= AiToPptx_DrawGroupObject($childrenItem, $DirPath);
      $importedNode = $dom->importNode($绘制元素RESULT, true); // 深度导入整个节点及其子节点
      $spTree->appendChild($importedNode);
		}
		else {
			$绘制元素RESULT 	= AiToPptx_DrawSingleObject($childrenItem, $DirPath);
      $importedNode = $dom->importNode($绘制元素RESULT, true); // 深度导入整个节点及其子节点
      $spTree->appendChild($importedNode);
		}
	}

	//print_R($关系引用ID值列表SlideLayout);
	//写入Relation文件
	$RelationContent 	= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
'.join('', $关系引用ID值列表SlideLayout).'
</Relationships>';
	file_put_contents($RelationPath, $RelationContent);

	// Add spTree
	$cSld->appendChild($spTree);

	// 创建 <p:clrMapOvr> 及其子元素
	$clrMapOvr = $dom->createElement('p:clrMapOvr');
  $clrMap = $Layout['clrMap'];
  if($clrMap)  {
    $overrideClrMapping = $dom->createElement('a:overrideClrMapping');
    $overrideClrMapping->setAttribute('accent1', $clrMap['accent1']);
    $overrideClrMapping->setAttribute('accent2', $clrMap['accent2']);
    $overrideClrMapping->setAttribute('accent3', $clrMap['accent3']);
    $overrideClrMapping->setAttribute('accent4', $clrMap['accent4']);
    $overrideClrMapping->setAttribute('accent5', $clrMap['accent5']);
    $overrideClrMapping->setAttribute('accent6', $clrMap['accent6']);
    $overrideClrMapping->setAttribute('bg1', $clrMap['bg1']);
    $overrideClrMapping->setAttribute('bg2', $clrMap['bg2']);
    $overrideClrMapping->setAttribute('tx1', $clrMap['tx1']);
    $overrideClrMapping->setAttribute('tx2', $clrMap['tx2']);
    $overrideClrMapping->setAttribute('hlink', $clrMap['hlink']);
    $overrideClrMapping->setAttribute('folHlink', $clrMap['folHlink']);
    $clrMapOvr->appendChild($overrideClrMapping);
  }
  else {
	  $masterClrMapping = $dom->createElement('a:masterClrMapping');
    $clrMapOvr->appendChild($masterClrMapping);
  }

	// 将所有子元素附加到根元素
	$sldLayout->appendChild($cSld);
	$sldLayout->appendChild($clrMapOvr);

	// 将根元素附加到DOM对象
	$dom->appendChild($sldLayout);

	//写入文件
	$dom->save($FilePath);

  //print $dom->saveXML();print_R($Layout);exit;

	return $dom->saveXML();

}


?>
