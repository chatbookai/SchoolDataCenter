<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

function AiToPptx_MakeThemeXml($slideMasters, $DirPath)  {
  global $关系引用ID值列表SlideLayout;
	$关系引用ID值列表SlideLayout 		= [];
  $关系引用ID值列表SlideLayout[] 	= '';

	// 创建一个新的DOM文档
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true; // 格式化输出
	$dom->preserveWhiteSpace = false; // 忽略不必要的空白
	// 加载theme模板文件
	$dom->load(__DIR__.'/../xml/theme.xml');

	$theme 				= $slideMasters[0]['theme'];
	$colorsThemeList 	= (array)$theme['colors'];
	$clrScheme = $dom->createElement('a:clrScheme');
	$clrScheme->setAttribute('name', "Office");
	foreach($colorsThemeList as $key => $value) 		{
		$keyElement = $dom->createElement('a:'.$key);
		$srgbClr = $dom->createElement('a:srgbClr');
		$srgbClr->setAttribute('val', AiToPptx_NumberToColor($value));
		$keyElement->appendChild($srgbClr);
		$clrScheme->appendChild($keyElement);
	}

	// 使用 DOMXPath 解析带有命名空间的 XML
	$xpath = new DOMXPath($dom);
	// 注册命名空间：前缀 'a' 和它在XML中的URI一致
	$xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
	// 使用 XPath 查询带有命名空间的节点
	$wantToReplaceNodeList = $xpath->query('//a:clrScheme');
	if ($wantToReplaceNodeList->length > 0) {
		$wantToReplaceNode = $wantToReplaceNodeList->item(0); // 获取第一个 clrScheme 节点
		$parent = $wantToReplaceNode->parentNode; // 获取父节点
		$parent->replaceChild($clrScheme, $wantToReplaceNode); // 替换节点
	}

	//写入文件
	$FilePath = $DirPath."/ppt/theme/theme1.xml";
	$dom->save($FilePath);

}


?>
