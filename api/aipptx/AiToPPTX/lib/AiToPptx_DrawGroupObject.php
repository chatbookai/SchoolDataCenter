<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

function AiToPptx_DrawGroupObject($childrenItem, $DirPath)  {
	global $SharpCounter;
	$anchor 			= $childrenItem['extInfo']['property']['anchor'];
	$interiorAnchor 	= $childrenItem['extInfo']['property']['interiorAnchor'];
	$flipVertical 	  = $childrenItem['extInfo']['property']['flipVertical'];
	$flipHorizontal 	= $childrenItem['extInfo']['property']['flipHorizontal'];
  $groupFillStyle   = $childrenItem['extInfo']['property']['groupFillStyle'];
	$rotation 		    = $childrenItem['extInfo']['property']['rotation'];

	// 初始化 DOMDocument
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
  //print_R($childrenItem);//exit;
	// 创建 <p:grpSp> 节点
	$grpSp = $dom->createElement('p:grpSp');

	// 创建 <p:nvGrpSpPr> 节点
	$nvGrpSpPr = $dom->createElement('p:nvGrpSpPr');

	// 创建 <p:cNvPr> 节点，并设置属性
	$cNvPr = $dom->createElement('p:cNvPr');
	$cNvPr->setAttribute('name', 'Group '. $SharpCounter++);
	$cNvPr->setAttribute('id', $SharpCounter++);

	// 创建 <p:cNvGrpSpPr> 和 <p:nvPr> 节点
	$cNvGrpSpPr = $dom->createElement('p:cNvGrpSpPr');
	$nvPr = $dom->createElement('p:nvPr');

	// 将 <p:cNvPr>, <p:cNvGrpSpPr>, <p:nvPr> 添加到 <p:nvGrpSpPr>
	$nvGrpSpPr->appendChild($cNvPr);
	$nvGrpSpPr->appendChild($cNvGrpSpPr);
	$nvGrpSpPr->appendChild($nvPr);

	// 创建 <p:grpSpPr> 节点
	$grpSpPr = $dom->createElement('p:grpSpPr');

	// 创建 <a:xfrm> 节点及其子节点
	$xfrm = $dom->createElement('a:xfrm');
	if($flipVertical == 1) {
		$xfrm->setAttribute('flipV', 'true');
	}
  if($flipHorizontal == 1) {
		$xfrm->setAttribute('flipH', 'true');
	}
	if($rotation != "") {
		$xfrm->setAttribute('rot', $rotation * 60000);
	}

	$off = $dom->createElement('a:off');
	$off->setAttribute('x', strval(intval($anchor[0] * 12700)));
	$off->setAttribute('y', strval(intval($anchor[1] * 12700)));

	$ext = $dom->createElement('a:ext');
	$ext->setAttribute('cx', strval(intval($anchor[2] * 12700)));
	$ext->setAttribute('cy', strval(intval($anchor[3] * 12700)));

	$chOff = $dom->createElement('a:chOff');
	$chOff->setAttribute('x', strval(intval($interiorAnchor[0] * 12700)));
	$chOff->setAttribute('y', strval(intval($interiorAnchor[1] * 12700)));

	$chExt = $dom->createElement('a:chExt');
	$chExt->setAttribute('cx', strval(intval($interiorAnchor[2] * 12700)));
	$chExt->setAttribute('cy', strval(intval($interiorAnchor[3] * 12700)));

	// 将子节点添加到 <a:xfrm>
	$xfrm->appendChild($off);
	$xfrm->appendChild($ext);
	$xfrm->appendChild($chOff);
	$xfrm->appendChild($chExt);

	// 将 <a:xfrm> 添加到 <p:grpSpPr>
	$grpSpPr->appendChild($xfrm);

	if ($groupFillStyle['type'] == 'color') {
		// 创建 <a:solidFill> 节点
		$a_solidFill = $dom->createElement('a:solidFill');
		$grpSpPr->appendChild($a_solidFill);

		// 创建 <a:schemeClr> 节点并设置属性
		if($groupFillStyle['color']['scheme'] != "")  {
			$a_schemeClr = $dom->createElement('a:schemeClr');
			$a_schemeClr->setAttribute('val', $groupFillStyle['color']['scheme']);
			$a_solidFill->appendChild($a_schemeClr);
			if($groupFillStyle['color']['alpha'] != "")  {
				$a_alpha = $dom->createElement('a:alpha');
				$a_alpha->setAttribute('val', $groupFillStyle['color']['alpha']);
				$a_schemeClr->appendChild($a_alpha);
			}
			if($groupFillStyle['color']['lumMod'] != "")  {
				$lumMod = $dom->createElement('a:lumMod');
				$lumMod->setAttribute('val', $groupFillStyle['color']['lumMod']);
				$a_schemeClr->appendChild($lumMod);
			}
			if($groupFillStyle['color']['lumOff'] != "")  {
				$lumOff = $dom->createElement('a:lumOff');
				$lumOff->setAttribute('val', $groupFillStyle['color']['lumOff']);
				$a_schemeClr->appendChild($lumOff);
			}
		}

		if($groupFillStyle['color']['realColor'] != '' && $groupFillStyle['color']['scheme'] == '')  {
			$srgbClr = $dom->createElement('a:srgbClr');
			if($groupFillStyle['color']['color'] == '-1')  {
				$srgbClr->setAttribute('val', 'FFFFFF');
			}
			else {
				$srgbClr->setAttribute('val', AiToPptx_NumberToColor($groupFillStyle['color']['color']));
			}
			if($groupFillStyle['color']['alpha'] != "" && $groupFillStyle['color']['scheme'] == "")  {
				$a_alpha = $dom->createElement('a:alpha');
				$a_alpha->setAttribute('val', $groupFillStyle['color']['alpha']);
				$srgbClr->appendChild($a_alpha);
			}
			if($groupFillStyle['color']['lumMod'] != "" && $groupFillStyle['color']['scheme'] == "")  {
				$lumMod = $dom->createElement('a:lumMod');
				$lumMod->setAttribute('val', $groupFillStyle['color']['lumMod']);
				$srgbClr->appendChild($lumMod);
			}
			if($groupFillStyle['color']['lumOff'] != "" && $groupFillStyle['color']['scheme'] == "")  {
				$lumOff = $dom->createElement('a:lumOff');
				$lumOff->setAttribute('val', $groupFillStyle['color']['lumOff']);
				$srgbClr->appendChild($lumOff);
			}
			$a_solidFill->appendChild($srgbClr);
		}

	}

	if ($groupFillStyle['type'] == 'gradient') {
		// 创建 <a:gradFill> 根节点
		$gradFill = $dom->createElement('a:gradFill');
		$dom->appendChild($gradFill);

		// 创建 <a:gsLst> 节点
		$gsLst = $dom->createElement('a:gsLst');
		$gradFill->appendChild($gsLst);

		// 遍历 colors 数组并生成 <a:gs> 节点
		foreach ($groupFillStyle['gradient']['colors'] as $index => $color) {
			$gs 	= $dom->createElement('a:gs');
			$pos 	= $groupFillStyle['gradient']['fractions'][$index] * 100000; // 将比例转换为整数形式
			$gs->setAttribute('pos', (string)$pos);
			$gsLst->appendChild($gs);

			// 创建 <a:srgbClr> 节点并设置颜色
			$srgbClr = $dom->createElement('a:srgbClr');
			$srgbClr->setAttribute('val', strtoupper(dechex($color['color'] & 0xFFFFFF))); // 将颜色转换为十六进制格式
			$gs->appendChild($srgbClr);

			// 如果存在 lumMod 和 lumOff，添加这些节点
      if (isset($color['alpha'])) {
				$alpha = $dom->createElement('a:alpha');
				$alpha->setAttribute('val', (string)$color['alpha']);
				$srgbClr->appendChild($alpha);
			}

			if (isset($color['lumMod'])) {
				$lumMod = $dom->createElement('a:lumMod');
				$lumMod->setAttribute('val', (string)$color['lumMod']);
				$srgbClr->appendChild($lumMod);
			}

			if (isset($color['lumOff'])) {
				$lumOff = $dom->createElement('a:lumOff');
				$lumOff->setAttribute('val', (string)$color['lumOff']);
				$srgbClr->appendChild($lumOff);
			}
		}

		// 创建 <a:lin> 节点并设置角度
		$lin 	= $dom->createElement('a:lin');
		$angle 	= $groupFillStyle['gradient']['angle'] * 60000; // 将角度转换为 EMU 单位（1° = 60000 EMU）
		$lin->setAttribute('ang', (string)$angle);
		$gradFill->appendChild($lin);

		$grpSpPr->appendChild($gradFill);
	}

	if ($groupFillStyle['type'] == 'groupFill') {
		// 创建 <a:solidFill> 节点
		$grpFill = $dom->createElement('a:grpFill');
		$grpSpPr->appendChild($grpFill);
	}

	if ($groupFillStyle['type'] == 'noFill') {
		// 创建 <a:solidFill> 节点
		$noFill = $dom->createElement('a:noFill');
		$grpSpPr->appendChild($noFill);
	}

	// 将 <p:nvGrpSpPr> 和 <p:grpSpPr> 添加到 <p:grpSp>
	$grpSp->appendChild($nvGrpSpPr);
	$grpSp->appendChild($grpSpPr);

	$childrenList = $childrenItem['children'];
	foreach($childrenList as $children) {
    $Type 				  = $children['type'];
		$realType 			= $children['extInfo']['property']['realType'];
		$rotation 			= $children['extInfo']['property']['rotation'];
		$groupFillStyle = $children['extInfo']['property']['groupFillStyle'];
    if($realType == "Group") {
			//print_R($childrenItem);
			$绘制元素RESULT 	= AiToPptx_DrawGroupObject($children, $DirPath);
      $importedNode = $dom->importNode($绘制元素RESULT, true); // 深度导入整个节点及其子节点
      $grpSp->appendChild($importedNode);
		}
    else {
      $绘制元素RESULT  = AiToPptx_DrawSingleObject($children, $DirPath);
      $importedNode   = $dom->importNode($绘制元素RESULT, true);
      $grpSp->appendChild($importedNode);
    }
	}

	// 将 <p:grpSp> 添加到 DOM 的根节点
	//$dom->appendChild($grpSp); print_R($dom->saveXML());//exit;

	// 输出生成的 XML 结构
	//$绘制元素RESULT = $dom->saveXML();

	if(strval(intval($anchor[0] * 12700)) == '549425')  {
		//print_R($childrenItem);
		//print_R($绘制元素RESULT->saveXML());
	}
	//print $绘制元素RESULT;

	return $grpSp;
}


?>
