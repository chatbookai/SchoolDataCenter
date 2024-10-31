<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

function AiToPptx_MakeSingleSlide($PageData, $FilePath, $RelationPath)  {
	global $SharpCounter;
	$childrenList	= $PageData['children'];

	// 开始处理 Slide 页面
	// 创建 DOMDocument 实例
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true; // 格式化输出

	// 创建根元素 <p:sld> 并添加命名空间
	$pSld = $dom->createElementNS(
		'http://schemas.openxmlformats.org/presentationml/2006/main',
		'p:sld'
	);
	$pSld->setAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');

	// 创建 <p:clrMapOvr> 元素及其子元素 <a:masterClrMapping>
	$clrMapOvr = $dom->createElement('p:clrMapOvr');
	$masterClrMapping = $dom->createElement('a:masterClrMapping');
	$clrMapOvr->appendChild($masterClrMapping);

	// 创建 <p:cSld> 元素
	$cSld = $dom->createElement('p:cSld');

	// 创建 <p:bg> 元素及其子元素
	$pBg = $dom->createElement('p:bg');
	$pBgPr = $dom->createElement('p:bgPr');
	$solidFill = $dom->createElement('a:solidFill');
	$srgbClr = $dom->createElement('a:srgbClr');
	$srgbClr->setAttribute('val', 'FFFFFF');

	// 构建背景元素层级关系
	$solidFill->appendChild($srgbClr);
	$pBgPr->appendChild($solidFill);
	$pBg->appendChild($pBgPr);

	// 将 <p:bg> 添加到 <p:cSld>
	$cSld->appendChild($pBg);

	// 创建 <p:spTree> 元素
	$spTree = $dom->createElement('p:spTree');

	// 创建 <p:nvGrpSpPr> 及其子元素
	$nvGrpSpPr = $dom->createElement('p:nvGrpSpPr');
	$cNvPr = $dom->createElement('p:cNvPr');
	$cNvPr->setAttribute('id', $SharpCounter++);
	$cNvPr->setAttribute('name', '');

	$cNvGrpSpPr = $dom->createElement('p:cNvGrpSpPr');
	$nvPr = $dom->createElement('p:nvPr');

	// 构建 <p:nvGrpSpPr> 层级关系
	$nvGrpSpPr->appendChild($cNvPr);
	$nvGrpSpPr->appendChild($cNvGrpSpPr);
	$nvGrpSpPr->appendChild($nvPr);

	// 创建 <p:grpSpPr> 及其子元素 <a:xfrm>
	$grpSpPr = $dom->createElement('p:grpSpPr');
	$xfrm = $dom->createElement('a:xfrm');

	// 创建位置信息元素
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

	// 构建 <a:xfrm> 层级关系
	$xfrm->appendChild($off);
	$xfrm->appendChild($ext);
	$xfrm->appendChild($chOff);
	$xfrm->appendChild($chExt);
	$grpSpPr->appendChild($xfrm);

	// 将 <p:nvGrpSpPr> 和 <p:grpSpPr> 添加到 <p:spTree>
	$spTree->appendChild($nvGrpSpPr);
	$spTree->appendChild($grpSpPr);

	// 添加 <p:sp>
	// 遍历 childrenList 并处理每个元素
	$SharpCounter = 0;
	foreach ($childrenList as $childrenItem) {

		$Type 				= $childrenItem['type'];
		$realType 			= $childrenItem['extInfo']['property']['realType'];
		$rotation 			= $childrenItem['extInfo']['property']['rotation'];
		$groupFillStyle 	= $childrenItem['extInfo']['property']['groupFillStyle'];

		if($realType == "Group") {
			//print_R($childrenItem);
			$绘制元素RESULT 	= AiToPptx_DrawGroupObject($childrenItem);
		}
		else {
      //得到图片路径信息
      $得到图片路径信息 = explode('/', $FilePath);
      array_pop($得到图片路径信息);
      array_pop($得到图片路径信息);
      $得到图片路径信息[] = 'media';
      $DirPath = join('/', $得到图片路径信息);
			$绘制元素RESULT 	= AiToPptx_DrawSingleObject($childrenItem, $DirPath);
		}
		$importedNode = $dom->importNode($绘制元素RESULT, true); // 深度导入整个节点及其子节点
		$spTree->appendChild($importedNode);
	}

	// 将 <p:spTree> 添加到 <p:cSld>
	$cSld->appendChild($spTree);

	// 将所有主要部分添加到 <p:sld>
	$pSld->appendChild($clrMapOvr);
	$pSld->appendChild($cSld);

	// 将 <p:sld> 作为根节点添加到文档
	$dom->appendChild($pSld);

  //写入文件
	$dom->save($FilePath);

	//写入Relation文件
	$slideLayoutIdx 	= $PageData['extInfo']['slideLayoutIdx'];
	$RelationContent 	= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Target="../slideLayouts/slideLayout'.($slideLayoutIdx+1).'.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout"/>
</Relationships>';
	file_put_contents($RelationPath, $RelationContent);

	return $dom->saveXML();
}


?>
