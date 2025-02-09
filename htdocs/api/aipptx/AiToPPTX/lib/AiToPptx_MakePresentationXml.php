<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: 商业授权
* Version: 0.0.1
*/

function AiToPptx_MakePresentationXml($JsonData, $写入文件目录) {
	// 创建DOM对象
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true; // 格式化输出，便于阅读

	// 创建 <p:presentation> 根元素，并设置命名空间
	$presentation = $dom->createElementNS('http://schemas.openxmlformats.org/presentationml/2006/main', 'p:presentation');
	$presentation->setAttribute('saveSubsetFonts', '1');
	$presentation->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
	$presentation->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

	// <p:sldMasterIdLst> 节点
	$sldMasterIdLst = $dom->createElement('p:sldMasterIdLst');
	$sldMasterId = $dom->createElement('p:sldMasterId');
	$sldMasterId->setAttribute('id', '2147483648');
	$sldMasterId->setAttribute('r:id', 'rId1');
	$sldMasterIdLst->appendChild($sldMasterId);
	$presentation->appendChild($sldMasterIdLst);

	// <p:sldIdLst> 节点
	$sldIdLst = $dom->createElement('p:sldIdLst');

	// 创建 <p:sldId> 子节点
	$pages = $JsonData['pages'];
	for($i=0;$i<sizeof($pages);$i++) {
		$sldIdItem = $dom->createElement('p:sldId');
		$sldIdItem->setAttribute('id', 255 + ($i+1));
		$sldIdItem->setAttribute('r:id', 'rId'.($i+6));
		$sldIdLst->appendChild($sldIdItem);
	}

	$presentation->appendChild($sldIdLst);

	$幻灯片的尺寸 = [1280, 720];
	$备注页的尺寸 = [1280, 720];

	// <p:sldSz> 节点
	$sldSz = $dom->createElement('p:sldSz');
	$sldSz->setAttribute('cx', $幻灯片的尺寸[0] * 9525);
	$sldSz->setAttribute('cy', $幻灯片的尺寸[1] * 9525);
	$presentation->appendChild($sldSz);

	// <p:notesSz> 节点
	$notesSz = $dom->createElement('p:notesSz');
	$notesSz->setAttribute('cx', $备注页的尺寸[0] * 9525);
	$notesSz->setAttribute('cy', $备注页的尺寸[1] * 9525);
	$presentation->appendChild($notesSz);

	// <p:defaultTextStyle> 节点
	$defaultTextStyle = $dom->createElement('p:defaultTextStyle');
	$presentation->appendChild($defaultTextStyle);

	// 将根元素添加到DOM对象
	$dom->appendChild($presentation);

	// 输出XML内容
	// print $写入文件目录."/ppt/presentation.xml";
	$dom->save($写入文件目录."/ppt/presentation.xml");

}


?>
