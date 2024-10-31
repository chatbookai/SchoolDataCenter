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

	// 创建DOM对象并设置XML版本和编码
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;

	// 创建根元素 <p:sldLayout>
	$sldLayout = $dom->createElementNS(
		'http://schemas.openxmlformats.org/presentationml/2006/main',
		'p:sldLayout'
	);
	$sldLayout->setAttribute('type', 'blank');
	$sldLayout->setAttribute('preserve', '1');

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
	$solidFill = $dom->createElement('a:solidFill');
	$srgbClr = $dom->createElement('a:srgbClr');
	$srgbClr->setAttribute('val', 'FFFFFF');

	// 组装 <p:bg> 树
	$solidFill->appendChild($srgbClr);
	$bgPr->appendChild($solidFill);
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
	global $关系引用ID值列表SlideLayout;
	$关系引用ID值列表SlideLayout 		= [];
	$关系引用ID值列表SlideLayout[] 	= '<Relationship Id="rId1" Target="../slideMasters/slideMaster1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster"/>';

  //得到图片路径信息
  $得到图片路径信息 = explode('/', $FilePath);
  array_pop($得到图片路径信息);
  array_pop($得到图片路径信息);
  $得到图片路径信息[] = 'media';
  $DirPath = join('/', $得到图片路径信息);
	foreach($Layout['children'] as $ChildrenItem) 		{
		$绘制单个元素对像RESULT 	= AiToPptx_DrawSingleObject($ChildrenItem, $DirPath); //在这个函数中会更新 $关系引用ID值列表SlideLayout
		//print $绘制元素RESULT;//exit;
		$importedpSp = $dom->importNode($绘制单个元素对像RESULT, true); // 深度导入整个节点及其子节点
		$spTree->appendChild($importedpSp);
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
	$masterClrMapping = $dom->createElement('a:masterClrMapping');
	$clrMapOvr->appendChild($masterClrMapping);

	// 将所有子元素附加到根元素
	$sldLayout->appendChild($cSld);
	$sldLayout->appendChild($clrMapOvr);

	// 将根元素附加到DOM对象
	$dom->appendChild($sldLayout);

	//写入文件
	$dom->save($FilePath);

	return $dom->saveXML();

}


?>
