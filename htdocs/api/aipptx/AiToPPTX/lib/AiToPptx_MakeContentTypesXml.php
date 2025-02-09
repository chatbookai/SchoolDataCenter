<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: 商业授权
* Version: 0.0.1
*/

function AiToPptx_MakeContentTypesXml($JsonData, $写入文件目录) {
	// 创建DOM对象
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;  // 格式化输出
	$dom->xmlStandalone = true; // 设置 standalone="yes"

	// 创建根节点 <Types> 并设置命名空间
	$types = $dom->createElementNS(
		'http://schemas.openxmlformats.org/package/2006/content-types',
		'Types'
	);

	// 添加 <Default> 节点的数据数组
	$defaultTypes = [
		['Extension' => 'jpeg', 'ContentType' => 'image/jpeg'],
		['Extension' => 'rels', 'ContentType' => 'application/vnd.openxmlformats-package.relationships+xml'],
		['Extension' => 'xml', 'ContentType' => 'application/xml']
	];

	// 创建并添加 <Default> 节点
	foreach ($defaultTypes as $default) {
		$defaultElement = $dom->createElement('Default');
		$defaultElement->setAttribute('Extension', $default['Extension']);
		$defaultElement->setAttribute('ContentType', $default['ContentType']);
		$types->appendChild($defaultElement);
	}

	// 添加 <Override> 节点的数据数组
	$overrideTypes = [
		['PartName' => '/docProps/app.xml', 'ContentType' => 'application/vnd.openxmlformats-officedocument.extended-properties+xml'],
		['PartName' => '/docProps/core.xml', 'ContentType' => 'application/vnd.openxmlformats-package.core-properties+xml'],
		['PartName' => '/ppt/presentation.xml', 'ContentType' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation.main+xml'],
		['PartName' => '/ppt/presProps.xml', 'ContentType' => 'application/vnd.openxmlformats-officedocument.presentationml.presProps+xml'],
		['PartName' => '/ppt/tableStyles.xml', 'ContentType' => 'application/vnd.openxmlformats-officedocument.presentationml.tableStyles+xml'],
		['PartName' => '/ppt/viewProps.xml', 'ContentType' => 'application/vnd.openxmlformats-officedocument.presentationml.viewProps+xml'],
		['PartName' => '/ppt/theme/theme1.xml', 'ContentType' => 'application/vnd.openxmlformats-officedocument.theme+xml'],
		['PartName' => '/ppt/slideMasters/slideMaster1.xml', 'ContentType' => 'application/vnd.openxmlformats-officedocument.presentationml.slideMaster+xml'],
	];

	$slideLayoutIndex = 0;
	$slideLayouts = (array)$JsonData['slideMasters'][0]['slideLayouts'];
	foreach($slideLayouts as $slideLayout) {
		$slideLayoutIndex ++;
		$overrideTypes[] = ['PartName' => '/ppt/slideLayouts/slideLayout'.$slideLayoutIndex.'.xml', 'ContentType' => 'application/vnd.openxmlformats-officedocument.presentationml.slideLayout+xml'];
	}
	$pages = $JsonData['pages'];
	for($i=0;$i<sizeof($pages);$i++) {
		$overrideTypes[] = ['PartName' => '/ppt/slides/slide'.($i+1).'.xml', 'ContentType' => 'application/vnd.openxmlformats-officedocument.presentationml.slide+xml'];
	}

	// 创建并添加 <Override> 节点
	foreach ($overrideTypes as $override) {
		$overrideElement = $dom->createElement('Override');
		$overrideElement->setAttribute('PartName', $override['PartName']);
		$overrideElement->setAttribute('ContentType', $override['ContentType']);
		$types->appendChild($overrideElement);
	}

	// 将根节点添加到DOM对象
	$dom->appendChild($types);

	// 输出XML内容
	$dom->save($写入文件目录."/[Content_Types].xml");

}


?>
