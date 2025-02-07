<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

function AiToPptx_MakeMasterXml($slideMasters, $DirPath)  {

  global $关系引用ID值列表SlideLayout;
	$关系引用ID值列表SlideLayout 		= [];
  //设置图片ID的初始值
  $slideLayouts = (array)$slideMasters[0]['slideLayouts'];
  $设置图片ID的初始值 = sizeof($slideLayouts) + 1; //所有的Layout页面, 再加上Theme页面
  for($i=0;$i<$设置图片ID的初始值;$i++)  {
    $关系引用ID值列表SlideLayout[] = '';
  }

	// 创建一个新的DOM文档
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true; // 格式化输出

	// 创建根元素并添加命名空间
	$sldMaster = $dom->createElementNS('http://schemas.openxmlformats.org/presentationml/2006/main', 'p:sldMaster');
	$sldMaster->setAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
	$sldMaster->setAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
	$dom->appendChild($sldMaster);

	// 添加 <p:cSld> 元素
	$cSld = $dom->createElement('p:cSld');
	$sldMaster->appendChild($cSld);

	// 添加 <p:bg> 元素
	$bg = $dom->createElement('p:bg');
	$cSld->appendChild($bg);

	// 添加 <p:bgPr> 元素
	$pBgPr = $dom->createElement('p:bgPr');
	$bg->appendChild($pBgPr);

  $DirPathMedia       .= "/ppt/media";

  //print_R($slideMasters[0]['background']);exit;
  $fillStyle 		   = $slideMasters[0]['background']['fillStyle'];
  $pBgPr           = 渲染fillStyle($dom, $fillStyle, $pBgPr, $DirPathMedia);
	$strokeStyle 		 = $slideMasters[0]['background']['strokeStyle'];
  $pBgPr           = 渲染strokeStyle($dom, $strokeStyle, $pBgPr, $DirPathMedia);

	// 添加 <a:srgbClr> 元素 暂时不启用
	//$srgbClr = $dom->createElement('a:srgbClr');
	//$srgbClr->setAttribute('val', 'FFFFFF');
	//$solidFill->appendChild($srgbClr);

	// 添加 <p:spTree> 元素
	$spTree = $dom->createElement('p:spTree');
	$cSld->appendChild($spTree);

	// 添加 <p:nvGrpSpPr> 元素
	$nvGrpSpPr = $dom->createElement('p:nvGrpSpPr');
	$spTree->appendChild($nvGrpSpPr);

	// 添加 <p:cNvPr> 元素
	$cNvPr = $dom->createElement('p:cNvPr');
	$cNvPr->setAttribute('id', '1');
	$cNvPr->setAttribute('name', '');
	$nvGrpSpPr->appendChild($cNvPr);

	// 添加 <p:cNvGrpSpPr> 元素
	$cNvGrpSpPr = $dom->createElement('p:cNvGrpSpPr');
	$nvGrpSpPr->appendChild($cNvGrpSpPr);

	// 添加 <p:nvPr> 元素
	$nvPr = $dom->createElement('p:nvPr');
	$nvGrpSpPr->appendChild($nvPr);

	// 添加 <p:grpSpPr> 元素
	$grpSpPr = $dom->createElement('p:grpSpPr');
	$spTree->appendChild($grpSpPr);

	// 添加 <a:xfrm> 元素
	$xfrm = $dom->createElement('a:xfrm');
	$grpSpPr->appendChild($xfrm);

	// 添加 <a:off> 元素
	$off = $dom->createElement('a:off');
	$off->setAttribute('x', '0');
	$off->setAttribute('y', '0');
	$xfrm->appendChild($off);

	// 添加 <a:ext> 元素
	$ext = $dom->createElement('a:ext');
	$ext->setAttribute('cx', '0');
	$ext->setAttribute('cy', '0');
	$xfrm->appendChild($ext);

	// 添加 <a:chOff> 元素
	$chOff = $dom->createElement('a:chOff');
	$chOff->setAttribute('x', '0');
	$chOff->setAttribute('y', '0');
	$xfrm->appendChild($chOff);

	// 添加 <a:chExt> 元素
	$chExt = $dom->createElement('a:chExt');
	$chExt->setAttribute('cx', '0');
	$chExt->setAttribute('cy', '0');
	$xfrm->appendChild($chExt);

	$childrenList = $slideMasters[0]['children'];
  foreach ($childrenList as $childrenItem)    {
		$Type 				    = $childrenItem['type'];
		$realType 			  = $childrenItem['extInfo']['property']['realType'];
		$rotation 			  = $childrenItem['extInfo']['property']['rotation'];
		$groupFillStyle 	= $childrenItem['extInfo']['property']['groupFillStyle'];
		if($realType == "Group") {
			//print_R($childrenItem);
			$绘制元素RESULT 	= AiToPptx_DrawGroupObject($childrenItem, $DirPath);
		}
		else {
			$绘制元素RESULT 	= AiToPptx_DrawSingleObject($childrenItem, $DirPath);
		}
		$importedNode = $dom->importNode($绘制元素RESULT, true); // 深度导入整个节点及其子节点
		$spTree->appendChild($importedNode);
	}


	// 创建 <p:clrMap> 元素并设置属性
	$themeMap = $slideMasters[0]['theme'];
	$clrMap = $dom->createElement('p:clrMap');
	if(isset($themeMap['colors']['lt1'])) $clrMap->setAttribute('bg1', 'lt1');
	if(isset($themeMap['colors']['lt2'])) $clrMap->setAttribute('bg2', 'lt2');
	if(isset($themeMap['colors']['dk1'])) $clrMap->setAttribute('tx1', 'dk1');
	if(isset($themeMap['colors']['dk2'])) $clrMap->setAttribute('tx2', 'dk2');
	if(isset($themeMap['colors']['accent1'])) $clrMap->setAttribute('accent1', 'accent1');
	if(isset($themeMap['colors']['accent2'])) $clrMap->setAttribute('accent2', 'accent2');
	if(isset($themeMap['colors']['accent3'])) $clrMap->setAttribute('accent3', 'accent3');
	if(isset($themeMap['colors']['accent4'])) $clrMap->setAttribute('accent4', 'accent4');
	if(isset($themeMap['colors']['accent5'])) $clrMap->setAttribute('accent5', 'accent5');
	if(isset($themeMap['colors']['accent6'])) $clrMap->setAttribute('accent6', 'accent6');
	if(isset($themeMap['colors']['hlink'])) $clrMap->setAttribute('hlink', 'hlink');
	if(isset($themeMap['colors']['folHlink'])) $clrMap->setAttribute('folHlink', 'folHlink');
	$sldMaster->appendChild($clrMap);

	// 创建 <p:sldLayoutIdLst> 元素
	$sldLayoutIdLst = $dom->createElement('p:sldLayoutIdLst');

	// 定义幻灯片布局 ID 和 r:id 的数组
	$slideLayouts = (array)$slideMasters[0]['slideLayouts'];
	$layouts = [];
	$slideMasterContent = [];
	$slideMasterContent[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	$slideMasterContent[] = '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
	foreach($slideLayouts as $slideLayout) {
		$slideLayoutIndex ++;
		$slideMasterContent[] = '<Relationship Id="rId'.$slideLayoutIndex.'" Target="../slideLayouts/slideLayout'.$slideLayoutIndex.'.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout"/>';
		$layouts[] = ['id' => 10000+$slideLayoutIndex, 'r:id' => 'rId'.$slideLayoutIndex];
	}
	$slideLayoutIndex ++;
	$slideMasterContent[] = '<Relationship Id="rId'.$slideLayoutIndex.'" Target="../theme/theme1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme"/>';
	//$layouts[] = ['id' => 10000+$slideLayoutIndex, 'r:id' => 'rId'.$slideLayoutIndex];

  //加入图片映射
  foreach($关系引用ID值列表SlideLayout as $关系引用ID值列表SlideLayoutItem) {
    if($关系引用ID值列表SlideLayoutItem!="")   {
      $slideMasterContent[] = $关系引用ID值列表SlideLayoutItem;
    }
  }

	$slideMasterContent[] = '</Relationships>';

	foreach ($layouts as $layout) {
		$sldLayoutId = $dom->createElement('p:sldLayoutId');
		$sldLayoutId->setAttribute('id', $layout['id']);
		$sldLayoutId->setAttribute('r:id', $layout['r:id']);
		$sldLayoutIdLst->appendChild($sldLayoutId);
	}
	$sldMaster->appendChild($sldLayoutIdLst);

	$dom->appendChild($sldMaster);

  //print $dom->saveXML(); exit;

	//写入文件
	$FilePath = $DirPath."/ppt/slideMasters/slideMaster1.xml";
	$dom->save($FilePath);

	//生成slideMaster的relation文件
	$slideMasterPath = $DirPath."/ppt/slideMasters/_rels/slideMaster1.xml.rels";
	$slideMasterContentString = join("\n", $slideMasterContent);
	file_put_contents($slideMasterPath, $slideMasterContentString);

	return $dom->saveXML();

}

?>
