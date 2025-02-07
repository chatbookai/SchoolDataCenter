<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

function AiToPptx_MakeRootRelations($JsonData, $写入文件目录) {
	// 创建DOM对象
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;  // 格式化输出
	$dom->xmlStandalone = true; // 设置 standalone="yes"

	// 创建根节点 <Relationships> 并设置命名空间
	$relationships = $dom->createElementNS(
		'http://schemas.openxmlformats.org/package/2006/relationships',
		'Relationships'
	);

	// 创建关系数据数组
	$relationshipsData = [
		['Id' => 'rId1', 'Target' => 'ppt/presentation.xml', 'Type' => 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument'],
		['Id' => 'rId2', 'Target' => 'docProps/thumbnail.jpeg', 'Type' => 'http://schemas.openxmlformats.org/package/2006/relationships/metadata/thumbnail'],
		['Id' => 'rId3', 'Target' => 'docProps/core.xml', 'Type' => 'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties'],
		['Id' => 'rId4', 'Target' => 'docProps/app.xml', 'Type' => 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties'],
	];

	$slideLayoutIndex = 0;
	$slideLayouts = (array)$JsonData['slideMasters'][0]['slideLayouts'];
	foreach($slideLayouts as $slideLayout) {
		$slideLayoutIndex ++;
		$relationshipsData[] = ['Id' => 'rId'.($slideLayoutIndex+4), 'Target' => 'ppt/slideLayouts/slideLayout'.($slideLayoutIndex+4).'.xml', 'Type' => 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout'];
	}

	// 遍历关系数据并创建 <Relationship> 节点
	foreach ($relationshipsData as $data) {
		$relationship = $dom->createElement('Relationship');
		$relationship->setAttribute('Id', $data['Id']);
		$relationship->setAttribute('Target', $data['Target']);
		$relationship->setAttribute('Type', $data['Type']);
		$relationships->appendChild($relationship);
	}

	// 将根节点添加到DOM对象
	$dom->appendChild($relationships);

	// 输出XML内容
	//print $写入文件目录."/_rels/.rels";
	$dom->save($写入文件目录."/_rels/.rels");

}



?>
