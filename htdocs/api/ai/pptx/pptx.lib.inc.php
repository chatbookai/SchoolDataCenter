<?php

function 绘制单个页面($PageData)  {
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
			$绘制元素RESULT 	= 绘制Group元素($childrenItem);
		}
		else {
			$绘制元素RESULT 	= 绘制单个元素对像($childrenItem);
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

	// 输出 XML
	//echo $dom->saveXML();

	$最后输出PPTX_SLIDE = $dom->saveXML();
	
	return $最后输出PPTX_SLIDE;
}


function 绘制Group元素($childrenItem)  {
	global $SharpCounter;
	$anchor 			= $childrenItem['extInfo']['property']['anchor'];
	$interiorAnchor 	= $childrenItem['extInfo']['property']['interiorAnchor'];
	// 初始化 DOMDocument
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;

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
	if($rotation > 0) {
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
				$srgbClr->setAttribute('val', 数字转颜色($groupFillStyle['color']['color']));
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

		// 创建 <a:alpha> 节点并设置属性
		if($groupFillStyle['color']['alpha'] != "" && $groupFillStyle['color']['scheme'] != "")  {
			$a_alpha = $dom->createElement('a:alpha');
			$a_alpha->setAttribute('val', $groupFillStyle['color']['alpha']);
			$a_schemeClr->appendChild($a_alpha);
		}

		// 将 <a:schemeClr> 添加到 <a:solidFill>
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
		//print_R($children);
		$绘制元素RESULT 	= 绘制单个元素对像($children);
		$importedNode = $dom->importNode($绘制元素RESULT, true);
		$grpSp->appendChild($importedNode);
	}

	// 将 <p:grpSp> 添加到 DOM 的根节点
	//$dom->appendChild($grpSp);

	// 输出生成的 XML 结构
	//$绘制元素RESULT = $dom->saveXML();
	
	if(strval(intval($anchor[0] * 12700)) == '9002135')  {
		print_R($childrenItem);
		print_R($绘制元素RESULT->saveXML());
	}
	//print $绘制元素RESULT;
	
	return $grpSp;
}


function 绘制单个元素对像($childrenItem)  {
	global $SharpCounter;
	$Type 			= $childrenItem['type'];
    $Point 			= $childrenItem['point'];
	$anchor 		= $childrenItem['extInfo']['property']['anchor'];
	$realType 		= $childrenItem['extInfo']['property']['realType'];
	$realType 		= $childrenItem['extInfo']['property']['realType'];
	$shapeType 		= $childrenItem['extInfo']['property']['shapeType'];
	$fillStyle 		= $childrenItem['extInfo']['property']['fillStyle'];
	$strokeStyle 	= $childrenItem['extInfo']['property']['strokeStyle'];
	$geometry 		= $childrenItem['extInfo']['property']['geometry'];
	$placeholder	= $childrenItem['extInfo']['property']['placeholder'];
	$prstTxWarp 	= $childrenItem['extInfo']['property']['prstTxWarp'];
	$flipVertical 	= $childrenItem['extInfo']['property']['flipVertical'];
	$rotation 		= $childrenItem['extInfo']['property']['rotation'];
	$fileName 		= $childrenItem['extInfo']['property']['fileName'];
	$clipping 		= $childrenItem['extInfo']['property']['clipping'];
	//print_R($childrenItem);

	// 1. 创建 DOMDocument 对象
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true; // 格式化输出

	// 2. 创建根元素 <p:sp> 并附加到 DOM
	switch($Type) {
		case 'text':
			$pSp = $dom->createElement('p:sp');
			break;
		case 'image':
			$pSp = $dom->createElement('p:pic');
			break;
		case 'graphicFrame':
			$pSp = $dom->createElement('p:graphicFrame');
			break;
		case 'table':
			$pSp = $dom->createElement('p:tbl');
			break;
		case 'connectshape':
			$pSp = $dom->createElement('p:cxnSp');
			break;
		case 'media':
			$pSp = $dom->createElement('p:media');
			break;
		case 'group':
			$pSp = $dom->createElement('p:group');
			break;
		case 'oleObj':
			$pSp = $dom->createElement('p:oleObj');
			break;
		case 'ph':
			$pSp = $dom->createElement('p:ph');
			break;
		default:
			$pSp = $dom->createElement('p:sp');
			break;
	}
	$dom->appendChild($pSp);
	
	if($Type == "text")  {
		// 3. 添加 <p:nvSpPr> 子元素及其子元素
		$nvSpPr = $dom->createElement('p:nvSpPr');
		$pSp->appendChild($nvSpPr);
		$cNvPr = $dom->createElement('p:cNvPr');
		$cNvPr->setAttribute('name', $realType .' '. $SharpCounter++);
		$cNvPr->setAttribute('id', $SharpCounter++);
		$nvSpPr->appendChild($cNvPr);
		$cNvSpPr = $dom->createElement('p:cNvSpPr');
		$cNvSpPr->setAttribute('id', $SharpCounter++);
		if($realType == "TextBox") {
			$cNvSpPr->setAttribute('txBox', 'true');
		}
		$nvSpPr->appendChild($cNvSpPr);
	}
	if($Type == "image")  {
		// 3. 添加 <p:nvPicPr> 子元素及其子元素
		$nvSpPr = $dom->createElement('p:nvPicPr');
		$pSp->appendChild($nvSpPr);
		$cNvPr = $dom->createElement('p:cNvPr');
		$cNvPr->setAttribute('name', $fileName);
		$cNvPr->setAttribute('id', $SharpCounter++);
		$nvSpPr->appendChild($cNvPr);
		$cNvPicPr = $dom->createElement('p:cNvPicPr');
		$cNvPicPr->setAttribute('id', $SharpCounter++);
		
		$picLocks = $dom->createElement('a:picLocks');
		$picLocks->setAttribute('noChangeAspect', "true");
		$cNvPicPr->appendChild($picLocks);
		
		$nvSpPr->appendChild($cNvPicPr);
		
		// 创建 <p:blipFill> 元素
		$p_blipFill = $dom->createElement('p:blipFill');
		// 创建 <a:blip> 元素，并添加 r:embed 属性
		$a_blip = $dom->createElement('a:blip');
		$a_blip->setAttribute('r:embed', 'rId2');
		// 将 <a:blip> 添加到 <p:blipFill> 中
		$p_blipFill->appendChild($a_blip);
		
		$a_srcRect = $dom->createElement('a:srcRect');
		if(isset($clipping[0]) && $clipping[0]>0 ) $a_srcRect->setAttribute('t', $clipping[0]);
		if(isset($clipping[1]) && $clipping[1]>0 ) $a_srcRect->setAttribute('r', $clipping[1]);
		if(isset($clipping[2]) && $clipping[2]>0 ) $a_srcRect->setAttribute('b', $clipping[2]);
		if(isset($clipping[3]) && $clipping[3]>0 ) $a_srcRect->setAttribute('l', $clipping[3]);
		$p_blipFill->appendChild($a_srcRect);
		
		// 创建 <a:stretch> 元素
		$a_stretch = $dom->createElement('a:stretch');
		// 创建 <a:fillRect> 元素，并设置边距属性
		$a_fillRect = $dom->createElement('a:fillRect');
		$a_fillRect->setAttribute('t', '0');
		$a_fillRect->setAttribute('l', '0');
		$a_fillRect->setAttribute('b', '0');
		$a_fillRect->setAttribute('r', '0');
		// 将 <a:fillRect> 添加到 <a:stretch> 中
		$a_stretch->appendChild($a_fillRect);
		// 将 <a:stretch> 添加到 <p:blipFill> 中
		$p_blipFill->appendChild($a_stretch);
		
		$pSp->appendChild($p_blipFill);
	}
	

	$nvPr = $dom->createElement('p:nvPr');
	switch($placeholder['type']) {
		case 'TITLE':
			$pPh = $dom->createElement('p:ph');
			$pPh->setAttribute('type', strtolower($placeholder['type']));
			if(isset($placeholder['size'])) $pPh->setAttribute('sz', strtolower($placeholder['size']));
			if(isset($placeholder['idx'])) 	$pPh->setAttribute('idx', strtolower($placeholder['idx']));
			$nvPr->appendChild($pPh);
			break;
		case 'BODY':
			$pPh = $dom->createElement('p:ph');
			$pPh->setAttribute('type', 'body');
			if(isset($placeholder['size'])) $pPh->setAttribute('sz', strtolower($placeholder['size']));
			if(isset($placeholder['idx'])) 	$pPh->setAttribute('idx', strtolower($placeholder['idx']));
			$nvPr->appendChild($pPh);
			break;
		case 'DATETIME':
			$pPh = $dom->createElement('p:ph');
			$pPh->setAttribute('type', 'dt');
			if(isset($placeholder['size'])) $pPh->setAttribute('sz', strtolower($placeholder['size']));
			if(isset($placeholder['idx'])) 	$pPh->setAttribute('idx', strtolower($placeholder['idx']));
			$nvPr->appendChild($pPh);
			break;
		case 'FOOTER':
			$pPh = $dom->createElement('p:ph');
			$pPh->setAttribute('type', 'ftr');
			if(isset($placeholder['size'])) $pPh->setAttribute('sz', strtolower($placeholder['size']));
			if(isset($placeholder['idx'])) 	$pPh->setAttribute('idx', strtolower($placeholder['idx']));
			$nvPr->appendChild($pPh);
			break;
		case 'SLIDE_NUMBER':
			$pPh = $dom->createElement('p:ph');
			$pPh->setAttribute('type', 'sldNum');
			if(isset($placeholder['size'])) $pPh->setAttribute('sz', strtolower($placeholder['size']));
			if(isset($placeholder['idx'])) 	$pPh->setAttribute('idx', strtolower($placeholder['idx']));
			$nvPr->appendChild($pPh);
			break;
		case 'CENTERED_TITLE':
			$pPh = $dom->createElement('p:ph');
			$pPh->setAttribute('type', 'ctrTitle');
			if(isset($placeholder['size'])) $pPh->setAttribute('sz', strtolower($placeholder['size']));
			if(isset($placeholder['idx'])) 	$pPh->setAttribute('idx', strtolower($placeholder['idx']));
			$nvPr->appendChild($pPh);
			break;
		case 'SUBTITLE':
			$pPh = $dom->createElement('p:ph');
			$pPh->setAttribute('type', 'subTitle');
			if(isset($placeholder['size'])) $pPh->setAttribute('sz', strtolower($placeholder['size']));
			if(isset($placeholder['idx'])) 	$pPh->setAttribute('idx', strtolower($placeholder['idx']));
			$nvPr->appendChild($pPh);
			break;
	}
	
	$nvSpPr->appendChild($nvPr);
	
	// 4. 添加 <p:spPr> 及其子元素
	$spPr = $dom->createElement('p:spPr');
	$pSp->appendChild($spPr);

	$xfrm = $dom->createElement('a:xfrm');
	if($flipVertical == 1) {
		$xfrm->setAttribute('flipV', 'true');
	}
	if($rotation > 0) {
		$xfrm->setAttribute('rot', $rotation * 60000);
	}
	$spPr->appendChild($xfrm);

	$off = $dom->createElement('a:off');
	$off->setAttribute('x', strval(intval($anchor[0] * 12700)));
	$off->setAttribute('y', strval(intval($anchor[1] * 12700)));
	$xfrm->appendChild($off);

	$ext = $dom->createElement('a:ext');
	$ext->setAttribute('cx', strval(intval($anchor[2] * 12700)));
	$ext->setAttribute('cy', strval(intval($anchor[3] * 12700)));
	$xfrm->appendChild($ext);
	
	if($childrenItem['extInfo']['property']['shapeType'] != "")  {
		$prstGeom = $dom->createElement('a:prstGeom');
		$prstGeom->setAttribute('prst', $childrenItem['extInfo']['property']['geometry']['name']);
		$spPr->appendChild($prstGeom);

		$avLst = $dom->createElement('a:avLst');
		if($childrenItem['extInfo']['property']['geometry']['avLst'][0] != "") {
			$avLstArray = $childrenItem['extInfo']['property']['geometry']['avLst'];
			foreach($avLstArray as $avLstItem) {
				//avLstItem value: 'adj:val 50000'
				$avLstItemArray = explode(':', $avLstItem);
				$gd = $dom->createElement('a:gd');
				$gd->setAttribute('name', $avLstItemArray[0]);
				$gd->setAttribute('fmla', $avLstItemArray[1]);
				$avLst->appendChild($gd);
			}
		}
		$prstGeom->appendChild($avLst);

		$noFill = $dom->createElement('a:noFill');
		$spPr->appendChild($noFill);
	}
	
	
	//绘制任意几何图形
	if ($Type == "text" && $geometry['name'] == "custom" && 1) {
		//print_R($childrenItem);
		$a_custGeom = $dom->createElement('a:custGeom');
		$spPr->appendChild($a_custGeom);

		// 添加节点
		$a_custGeom->appendChild($dom->createElement('a:avLst'));
		$a_custGeom->appendChild($dom->createElement('a:gdLst'));
		$a_custGeom->appendChild($dom->createElement('a:ahLst'));
		$a_custGeom->appendChild($dom->createElement('a:cxnLst'));
		
		$a_rect = $dom->createElement('a:rect');
		$a_rect->setAttribute('r', 'r');
		$a_rect->setAttribute('b', 'b');
		$a_rect->setAttribute('t', 't');
		$a_rect->setAttribute('l', 'l');
		$a_custGeom->appendChild($a_rect);

		// 创建 <a:pathLst> 和 <a:path> 节点
		$pathInfo = $geometry['data']['paths'][0];
		
		$a_pathLst = $dom->createElement('a:pathLst');
		$a_custGeom->appendChild($a_pathLst);
		//print_R($pathInfo);
		$a_path = $dom->createElement('a:path');
		$a_path->setAttribute('w', $pathInfo['w']);
		$a_path->setAttribute('h', $pathInfo['h']);
		$a_path->setAttribute('stroke', $pathInfo['stroked'] ? 'true' : 'false');
		$a_path->setAttribute('fill', strtolower($pathInfo['fill']));
		$a_path->setAttribute('extrusionOk', $pathInfo['extrusionOk'] ? 'true' : 'false');
		$a_pathLst->appendChild($a_path);
		
		//print $pathInfo['path'];
		$commands = preg_split('/ (?=[MLCZ])/', $pathInfo['path']);
		//print_R($commands);exit;
		// 遍历并生成路径指令
		foreach ($commands as $command) {
			$typeLine = $command[0]; // 指令类型 (M, L, C, Z)
			$points = array_filter(explode(' ', trim(substr($command, 1)))); // 提取点数据

			if ($typeLine === 'M') {
				// 创建 <a:moveTo> 节点
				$moveTo = $dom->createElement('a:moveTo');
				$pt = $dom->createElement('a:pt');
				$pt->setAttribute('x', strval(intval($points[0])));
				$pt->setAttribute('y', strval(intval($points[1])));
				$moveTo->appendChild($pt);
				$a_path->appendChild($moveTo);

			} elseif ($typeLine === 'L') {
				// 创建 <a:lnTo> 节点
				$lnTo = $dom->createElement('a:lnTo');
				$pt = $dom->createElement('a:pt');
				$pt->setAttribute('x', strval(intval($points[0])));
				$pt->setAttribute('y', strval(intval($points[1])));
				$lnTo->appendChild($pt);
				$a_path->appendChild($lnTo);

			} elseif ($typeLine === 'C') {
				// 创建 <a:cubicBezTo> 节点
				$cubicBezTo = $dom->createElement('a:cubicBezTo');
				for ($i = 0; $i < count($points); $i += 2) {
					$pt = $dom->createElement('a:pt');
					$pt->setAttribute('x', strval(intval($points[$i])));
					$pt->setAttribute('y', strval(intval($points[$i + 1])));
					$cubicBezTo->appendChild($pt);
				}
				$a_path->appendChild($cubicBezTo);

			} elseif ($typeLine === 'Z') {
				// 创建 <a:close> 节点
				$a_path->appendChild($dom->createElement('a:close'));
			}
		}
		//print $dom->asXML();exit;
	}
	
	if ($fillStyle['type'] == 'texture') {
		// 未完成
	}
	
	if ($fillStyle['type'] == 'color') {
		// 创建 <a:solidFill> 节点
		$a_solidFill = $dom->createElement('a:solidFill');
		$spPr->appendChild($a_solidFill);

		// 创建 <a:schemeClr> 节点并设置属性
		if($fillStyle['color']['scheme'] != "")  {
			$a_schemeClr = $dom->createElement('a:schemeClr');
			$a_schemeClr->setAttribute('val', $fillStyle['color']['scheme']);
			$a_solidFill->appendChild($a_schemeClr);
			if($fillStyle['color']['alpha'] != "")  {
				$a_alpha = $dom->createElement('a:alpha');
				$a_alpha->setAttribute('val', $fillStyle['color']['alpha']);
				$a_schemeClr->appendChild($a_alpha);
			}
			if($fillStyle['color']['lumMod'] != "")  {
				$lumMod = $dom->createElement('a:lumMod');
				$lumMod->setAttribute('val', $fillStyle['color']['lumMod']);
				$a_schemeClr->appendChild($lumMod);
			}
			if($fillStyle['color']['lumOff'] != "")  {
				$lumOff = $dom->createElement('a:lumOff');
				$lumOff->setAttribute('val', $fillStyle['color']['lumOff']);
				$a_schemeClr->appendChild($lumOff);
			}
		}
		
		if($fillStyle['color']['realColor'] != '' && $fillStyle['color']['scheme'] == '')  {
			$srgbClr = $dom->createElement('a:srgbClr');
			if($fillStyle['color']['color'] == '-1')  {
				$srgbClr->setAttribute('val', 'FFFFFF');
			}
			else {
				$srgbClr->setAttribute('val', 数字转颜色($fillStyle['color']['color']));
			}
			if($fillStyle['color']['alpha'] != "" && $fillStyle['color']['scheme'] == "")  {
				$a_alpha = $dom->createElement('a:alpha');
				$a_alpha->setAttribute('val', $fillStyle['color']['alpha']);
				$srgbClr->appendChild($a_alpha);
			}
			if($fillStyle['color']['lumMod'] != "" && $fillStyle['color']['scheme'] == "")  {
				$lumMod = $dom->createElement('a:lumMod');
				$lumMod->setAttribute('val', $fillStyle['color']['lumMod']);
				$srgbClr->appendChild($lumMod);
			}
			if($fillStyle['color']['lumOff'] != "" && $fillStyle['color']['scheme'] == "")  {
				$lumOff = $dom->createElement('a:lumOff');
				$lumOff->setAttribute('val', $fillStyle['color']['lumOff']);
				$srgbClr->appendChild($lumOff);
			}
			$a_solidFill->appendChild($srgbClr);
		}

		// 创建 <a:alpha> 节点并设置属性
		if($fillStyle['color']['alpha'] != "" && $fillStyle['color']['scheme'] != "")  {
			$a_alpha = $dom->createElement('a:alpha');
			$a_alpha->setAttribute('val', $fillStyle['color']['alpha']);
			$a_schemeClr->appendChild($a_alpha);
		}

		// 将 <a:schemeClr> 添加到 <a:solidFill>
	}
	
	if ($fillStyle['type'] == 'gradient') {
		// 创建 <a:gradFill> 根节点
		$gradFill = $dom->createElement('a:gradFill');
		$dom->appendChild($gradFill);

		// 创建 <a:gsLst> 节点
		$gsLst = $dom->createElement('a:gsLst');
		$gradFill->appendChild($gsLst);

		// 遍历 colors 数组并生成 <a:gs> 节点
		foreach ($fillStyle['gradient']['colors'] as $index => $color) {
			$gs 	= $dom->createElement('a:gs');
			$pos 	= $fillStyle['gradient']['fractions'][$index] * 100000; // 将比例转换为整数形式
			$gs->setAttribute('pos', (string)$pos);
			$gsLst->appendChild($gs);

			// 创建 <a:srgbClr> 节点并设置颜色
			$srgbClr = $dom->createElement('a:srgbClr');
			$srgbClr->setAttribute('val', strtoupper(dechex($color['color'] & 0xFFFFFF))); // 将颜色转换为十六进制格式
			$gs->appendChild($srgbClr);

			// 如果存在 lumMod 和 lumOff，添加这些节点
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
		$angle 	= $fillStyle['gradient']['angle'] * 60000; // 将角度转换为 EMU 单位（1° = 60000 EMU）
		$lin->setAttribute('ang', (string)$angle);
		$gradFill->appendChild($lin);

		$spPr->appendChild($gradFill);
	}
	
	if ($fillStyle['type'] == 'groupFill') {
		// 创建 <a:solidFill> 节点
		$grpFill = $dom->createElement('a:grpFill');
		$spPr->appendChild($grpFill);
	}
	
	if ($fillStyle['type'] == 'noFill') {
		// 创建 <a:solidFill> 节点
		$noFill = $dom->createElement('a:noFill');
		$spPr->appendChild($noFill);
	}
	
	//print_R($strokeStyle['paint']['color']['color']);
	if($strokeStyle['lineWidth'] != "" || $strokeStyle['lineCap'] != "" || $strokeStyle['lineDash'] != "")   {
		// 创建 <a:ln> 节点并设置属性
		$a_ln = $dom->createElement('a:ln');
		if($strokeStyle['lineWidth'] != "")  {
			$a_ln->setAttribute('w', strval(intval($strokeStyle['lineWidth'] * 12700)));
		}
		if($strokeStyle['lineCap'] == "ROUND")  {
			$a_ln->setAttribute('cap', 'rnd'); // 设置 cap 属性为 "rnd"
		}
		else if($strokeStyle['lineCap'] != "")  {
			$a_ln->setAttribute('cap', strtolower($strokeStyle['lineCap']));
		}
		if($strokeStyle['lineCompound'] == "SINGLE")  {
			$a_ln->setAttribute('cmpd', 'sng'); // 设置 cmpd 属性为 "sng"
		}
		$spPr->appendChild($a_ln);
		
		if($strokeStyle['paint']['color']['color'] != '')  {
			$solidFill = $dom->createElement('a:solidFill');
			$a_ln->appendChild($solidFill);
			$srgbClr = $dom->createElement('a:srgbClr');
			if($strokeStyle['paint']['color']['color'] == '-1')  {
				$srgbClr->setAttribute('val', 'FFFFFF');
			}
			else {
				$srgbClr->setAttribute('val', 数字转颜色($strokeStyle['paint']['color']['color']));
			}
			if($strokeStyle['paint']['color']['lumMod'] != '')  {
				$lumMod = $dom->createElement('a:lumMod');
				$lumMod->setAttribute('val', $strokeStyle['paint']['color']['lumMod']);
				$srgbClr->appendChild($lumMod);
			}
			$solidFill->appendChild($srgbClr);
		}
		
		// 创建 <a:prstDash> 节点并设置属性
		if($strokeStyle['lineDash'] != "")  {
			$a_prstDash = $dom->createElement('a:prstDash');
			$a_prstDash->setAttribute('val', strtolower($strokeStyle['lineDash']));
			$a_ln->appendChild($a_prstDash);
		}
		
		if($strokeStyle['lineHeadWidth'] != "" && $strokeStyle['lineHeadLength'] != "")  {
			$headEnd = $dom->createElement('a:headEnd');
			$headEnd->setAttribute('w', $strokeStyle['lineHeadWidth'] == "LARGE" ? "lg" : "sm");
			$headEnd->setAttribute('len', $strokeStyle['lineHeadLength'] == "LARGE" ? "lg" : "sm");
			$a_ln->appendChild($headEnd);
		}
		
		if($strokeStyle['lineTailLength'] != "" && $strokeStyle['lineTailDecoration'] != "")  {
			$tailEnd = $dom->createElement('a:tailEnd');
			$tailEnd->setAttribute('len', $strokeStyle['lineTailLength'] == "LARGE" ? "lg" : "sm");
			$tailEnd->setAttribute('type', $strokeStyle['lineTailDecoration'] == "LARGE" ? "lg" : "sm");
			$a_ln->appendChild($tailEnd);
		}
		
		if($strokeStyle['paint']['gradient']['gradientType'] != "")  {
			//print_R($strokeStyle['paint']['gradient']);exit;
			// 创建根节点 <a:gradFill>
			$gradFill = $dom->createElement('a:gradFill');

			// 创建 <a:gsLst> 节点
			$gsLst = $dom->createElement('a:gsLst');
			$gradFill->appendChild($gsLst);

			// 遍历 colors 数组，生成 <a:gs> 节点
			foreach ($strokeStyle['paint']['gradient']['colors'] as $index => $color) {
				// 创建 <a:gs>，并设置 pos 属性
				$gs = $dom->createElement('a:gs');
				$pos = $strokeStyle['paint']['gradient']['fractions'][$index] * 100000; // 转换为 0 ~ 100000 的范围
				$gs->setAttribute('pos', (string)$pos);
				$gsLst->appendChild($gs);

				// 创建 <a:srgbClr>，并设置 val 属性
				$srgbClr = $dom->createElement('a:srgbClr');
				$srgbClr->setAttribute('val', 数字转颜色($color['color']));
				$gs->appendChild($srgbClr);

				// 如果有 alpha 属性，添加 <a:alpha> 节点
				if (isset($color['alpha'])) {
					$alpha = $dom->createElement('a:alpha');
					$alpha->setAttribute('val', (string)$color['alpha']);
					$srgbClr->appendChild($alpha);
				}

				// 如果有 lumMod 属性，添加 <a:lumMod> 节点
				if (isset($color['lumMod'])) {
					$lumMod = $dom->createElement('a:lumMod');
					$lumMod->setAttribute('val', (string)$color['lumMod']);
					$srgbClr->appendChild($lumMod);
				}

				// 如果有 lumOff 属性，添加 <a:lumOff> 节点
				if (isset($color['lumOff'])) {
					$lumOff = $dom->createElement('a:lumOff');
					$lumOff->setAttribute('val', (string)$color['lumOff']);
					$srgbClr->appendChild($lumOff);
				}
			}

			// 创建 <a:lin> 节点，并设置 ang 属性
			$lin = $dom->createElement('a:lin');
			$angle = $strokeStyle['paint']['gradient']['angle'] * 60000; // 将角度转换为 EMU 单位
			$lin->setAttribute('ang', (string)$angle);
			$gradFill->appendChild($lin);
			
			$a_ln->appendChild($gradFill);
		}
		
	}
	
	// 5. 添加 <p:txBody> 及其子元素
	if($realType != "Picture")  {
		$txBody = $dom->createElement('p:txBody');
		$pSp->appendChild($txBody);

		$bodyPr = $dom->createElement('a:bodyPr');
		$bodyPr->setAttribute('rtlCol', 'false');
		if($realType == "TextBox" || $realType == "Auto" || $realType == "Freeform")  {
			switch($childrenItem['extInfo']['property']['textAutofit']) {
				case 'NORMAL':
					$normAutofit = $dom->createElement('a:normAutofit');
					$bodyPr->appendChild($normAutofit);
					break;
				case 'SHAPE':
					$spAutoFit = $dom->createElement('a:spAutoFit');
					$bodyPr->appendChild($spAutoFit);
					break;
				case 'NONE':
					$spAutoFit = $dom->createElement('a:noAutofit');
					$bodyPr->appendChild($spAutoFit);
					break;
			}
		}
		switch($childrenItem['extInfo']['property']['textDirection']) {
			case 'HORIZONTAL':
				$bodyPr->setAttribute('vert', 'horz');
				break;
			case 'VERTICAL':
				$bodyPr->setAttribute('vert', 'vert');
				break;
		}
		switch($childrenItem['extInfo']['property']['textVerticalAlignment']) {
			case 'TOP':
				$bodyPr->setAttribute('anchor', 't');
				break;
			case 'BOTTOM':
				$bodyPr->setAttribute('anchor', 'b');
				break;
			case 'LEFT':
				$bodyPr->setAttribute('anchor', 'l');
				break;
			case 'RIGHT':
				$bodyPr->setAttribute('anchor', 'r');
				break;
			case 'CENTER':
				$bodyPr->setAttribute('anchor', 'ctr');
				break;
			case 'MIDDLE':
				$bodyPr->setAttribute('anchor', 'ctr');
				break;
		}
		if($childrenItem['extInfo']['property']['textWordWrap'] == 1) {
			$bodyPr->setAttribute('wrap', 'square');
		}
		elseif(isset($childrenItem['extInfo']['property']['textWordWrap'])) {
			$bodyPr->setAttribute('wrap', 'none');
		}
		$bodyPr->setAttribute('tIns', '45720');
		$bodyPr->setAttribute('lIns', '91440');
		$bodyPr->setAttribute('bIns', '45720');
		$bodyPr->setAttribute('rIns', '91440');
		$txBody->appendChild($bodyPr);
		
		if($childrenItem['extInfo']['property']['prstTxWarp']['prst'] != "")  {
			// 创建 <a:prstTxWarp> 节点并设置属性
			$a_prstTxWarp = $dom->createElement('a:prstTxWarp');
			$a_prstTxWarp->setAttribute('prst', $childrenItem['extInfo']['property']['prstTxWarp']['prst']);
			$bodyPr->appendChild($a_prstTxWarp);
			
			// 创建 <a:avLst> 节点
			$a_avLst = $dom->createElement('a:avLst');

			// 将 <a:avLst> 添加到 <a:prstTxWarp>
			$a_prstTxWarp->appendChild($a_avLst);

			// 创建 <a:noAutofit> 节点
			$a_noAutofit = $dom->createElement('a:noAutofit');

			// 将 <a:noAutofit> 添加到根节点（或适当的位置）
			$bodyPr->appendChild($a_noAutofit);
		}
		
		$lstStyle = $dom->createElement('a:lstStyle');
		$txBody->appendChild($lstStyle);

		// 6. 文本框, 创建段落 <a:p> 及其内容, <a:p>对像可能会有多个,所以需要循环过滤
		$childrenList 	= $childrenItem['children'];
		foreach($childrenList as $childrenListItem)  {
			$文本属性 		= $childrenListItem['extInfo'];
			if ($Type == "text" || $childrenListItem['extInfo']['type'] == "p") {
				$p = $dom->createElement('a:p');
				$txBody->appendChild($p);

				$pPr = $dom->createElement('a:pPr');
				$p->appendChild($pPr);
				if(isset($文本属性['property']['indent'])) {
					$pPr->setAttribute('indent', intval($文本属性['property']['indent'] * 12700));
				}
				if(isset($文本属性['property']['indentLevel'])) {
					$pPr->setAttribute('lvl', intval($文本属性['property']['indentLevel']));
				}
				switch($文本属性['property']['textAlign']) {
					case 'CENTER':
						$pPr->setAttribute('algn', 'ctr');
						break;
					case 'LEFT':
						$pPr->setAttribute('algn', 'l');
						break;
					case 'RIGHT':
						$pPr->setAttribute('algn', 'r');
						break;
					case 'TOP':
						$pPr->setAttribute('algn', 't');
						break;
					case 'BOTTOM':
						$pPr->setAttribute('algn', 'b');
						break;
				}
				if(isset($文本属性['property']['leftMargin'])) {
					$pPr->setAttribute('marL', intval($文本属性['property']['leftMargin'] * 12700));
				}
				if(isset($文本属性['property']['bulletStyle']['bulletFont'])) {
					$buFont = $dom->createElement('a:buFont');
					$buFont->setAttribute('typeface', $文本属性['property']['bulletStyle']['bulletFont']);
					$pPr->appendChild($buFont);
				}
				
				if(isset($文本属性['property']['bulletStyle']['bulletCharacter'])) {
					$buChar = $dom->createElement('a:buChar');
					$buChar->setAttribute('char', $文本属性['property']['bulletStyle']['bulletCharacter']);
					$pPr->appendChild($buChar);
				}
				
				if(isset($文本属性['property']['lineSpacing'])) {
					$lnSpc = $dom->createElement('a:lnSpc');
					$spcPct = $dom->createElement('a:spcPct');
					$spcPct->setAttribute('val', strval(intval($文本属性['property']['lineSpacing'] * 1000)));
					$lnSpc->appendChild($spcPct);
					$pPr->appendChild($lnSpc);
				}
				
				if(isset($文本属性['property']['spaceBefore'])) {
					$spcBef = $dom->createElement('a:spcBef');
					$spcPts = $dom->createElement('a:spcPts');
					$spcPts->setAttribute('val', strval(abs(intval($文本属性['property']['spaceBefore'] * 100))));
					$spcBef->appendChild($spcPts);
					$pPr->appendChild($spcBef);
				}
				
				$defRPr = $dom->createElement('a:defRPr');
				$pPr->appendChild($defRPr);
				
				
				$文本对像List		= (array)$childrenListItem['children'];
				foreach($文本对像List as $文本对像)   {
					if(isset($文本对像['text']))  {
						if($文本对像['extInfo']['property']['slideNum'] == 1) {
							$r = $dom->createElement('a:fld');
							$r->setAttribute('type', 'slidenum');
							$r->setAttribute('id', 'NotSetting');
						}
						else {
							$r = $dom->createElement('a:r');
						}
						$p->appendChild($r);

						// 设置文本和样式属性		
						$rPr = $dom->createElement('a:rPr');
						$rPr->setAttribute('lang', $文本对像['extInfo']['property']['lang']);
						$rPr->setAttribute('b', $文本对像['extInfo']['property']['bold'] == 1 ? "true" : "false");
						$rPr->setAttribute('i', $文本对像['extInfo']['property']['italic'] == 1 ? "true" : "false");
						$rPr->setAttribute('sz', ($文本对像['extInfo']['property']['fontSize'] * 100));
						$rPr->setAttribute('baseline', $文本对像['extInfo']['property']['baseline']);
						$rPr->setAttribute('u', 'none');
						$rPr->setAttribute('altLang', 'en-US');
						$r->appendChild($rPr);

						$solidFill = $dom->createElement('a:solidFill');
						$rPr->appendChild($solidFill);
						
						if($文本对像['extInfo']['property']['fontColor']['type'] == "gradient")  {
							//print_R($文本对像['extInfo']['property']);exit;
							// 创建根节点 <a:gradFill>
							$gradFill = $dom->createElement('a:gradFill');

							// 创建 <a:gsLst> 节点
							$gsLst = $dom->createElement('a:gsLst');
							$gradFill->appendChild($gsLst);

							// 遍历 colors 数组，生成 <a:gs> 节点
							foreach ($文本对像['extInfo']['property']['fontColor']['gradient']['colors'] as $index => $color) {
								// 创建 <a:gs>，并设置 pos 属性
								$gs = $dom->createElement('a:gs');
								$pos = $文本对像['extInfo']['property']['fontColor']['gradient']['fractions'][$index] * 100000; // 转换为 0 ~ 100000 的范围
								$gs->setAttribute('pos', (string)$pos);
								$gsLst->appendChild($gs);

								// 创建 <a:srgbClr>，并设置 val 属性
								$srgbClr = $dom->createElement('a:srgbClr');
								$srgbClr->setAttribute('val', 数字转颜色($color['color']));
								$gs->appendChild($srgbClr);

								// 如果有 alpha 属性，添加 <a:alpha> 节点
								if (isset($color['alpha'])) {
									$alpha = $dom->createElement('a:alpha');
									$alpha->setAttribute('val', (string)$color['alpha']);
									$srgbClr->appendChild($alpha);
								}

								// 如果有 lumMod 属性，添加 <a:lumMod> 节点
								if (isset($color['lumMod'])) {
									$lumMod = $dom->createElement('a:lumMod');
									$lumMod->setAttribute('val', (string)$color['lumMod']);
									$srgbClr->appendChild($lumMod);
								}

								// 如果有 lumOff 属性，添加 <a:lumOff> 节点
								if (isset($color['lumOff'])) {
									$lumOff = $dom->createElement('a:lumOff');
									$lumOff->setAttribute('val', (string)$color['lumOff']);
									$srgbClr->appendChild($lumOff);
								}
							}

							// 创建 <a:lin> 节点，并设置 ang 属性
							$lin = $dom->createElement('a:lin');
							$angle = $文本对像['extInfo']['property']['fontColor']['gradient']['angle'] * 60000; // 将角度转换为 EMU 单位
							$lin->setAttribute('ang', (string)$angle);
							$gradFill->appendChild($lin);
							
							$rPr->appendChild($gradFill);
						}
						
						//此处变量多增加了一个['color'],需要看是否会影响到其它slide页面,目前是在layout中有效
						if($文本对像['extInfo']['property']['fontColor']['color']['color'] !="" )  {
							$srgbClr = $dom->createElement('a:srgbClr');
							$srgbClr->setAttribute('val', 数字转颜色($文本对像['extInfo']['property']['fontColor']['color']['color']));
							$solidFill->appendChild($srgbClr);
							if($文本对像['extInfo']['property']['fontColor']['color']['alpha'] != "")  {
								$alpha = $dom->createElement('a:alpha');
								$alpha->setAttribute('val', $文本对像['extInfo']['property']['fontColor']['alpha']);
								$srgbClr->appendChild($alpha);
							}
							if($文本对像['extInfo']['property']['fontColor']['color']['lumMod'] != "")  {
								$lumMod = $dom->createElement('a:lumMod');
								$lumMod->setAttribute('val', $文本对像['extInfo']['property']['fontColor']['color']['lumMod']);
								$srgbClr->appendChild($lumMod);
							}
							if($文本对像['extInfo']['property']['fontColor']['color']['lumOff'] != "")  {
								$lumOff = $dom->createElement('a:lumOff');
								$lumOff->setAttribute('val', $文本对像['extInfo']['property']['fontColor']['color']['lumOff']);
								$srgbClr->appendChild($lumOff);
							}
						}
						if($文本对像['extInfo']['property']['fontColor']['color']['scheme'] !="" )  {
							$schemeClr = $dom->createElement('a:schemeClr');
							$schemeClr->setAttribute('val', $文本对像['extInfo']['property']['fontColor']['color']['scheme']);
							$solidFill->appendChild($schemeClr);
						}

						$latin = $dom->createElement('a:latin');
						$latin->setAttribute('typeface', $文本对像['extInfo']['property']['fontFamily']);
						$rPr->appendChild($latin);

						$ea = $dom->createElement('a:ea');
						$ea->setAttribute('typeface', $文本对像['extInfo']['property']['fontFamily']);
						$rPr->appendChild($ea);

						$t = $dom->createElement('a:t', $文本对像['text']);
						$r->appendChild($t);
					}
				}
					
				$endParaRPr = $dom->createElement('a:endParaRPr');
				$endParaRPr->setAttribute('lang', 'en-US');
				$endParaRPr->setAttribute('sz', '1100');
				$p->appendChild($endParaRPr);
			}
			
		}
		
	}
	if(strval(intval($anchor[0] * 12700)) == '1490272')  {
		print_R($childrenItem);
		print_R($dom->saveXML());
	}
	
	return $pSp;	
}


function createZip($source, $destination) {
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        return false;
    }

    $source = realpath($source);

    // 如果是文件夹，递归添加其中的文件和文件夹
    if (is_dir($source)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $filePath = realpath($file);
            $relativePath = substr($filePath, strlen($source) + 1);

            if (is_dir($filePath)) {
                $zip->addEmptyDir($relativePath);
            } else if (is_file($filePath)) {
                $zip->addFile($filePath, $relativePath);
            }
        }
    } else if (is_file($source)) {
        // 如果是单个文件，直接添加
        $zip->addFile($source, basename($source));
    }

    return $zip->close();
}


function 数字转颜色($color) {
	// 提取 RGB 部分
	$realColor = $color & 0xFFFFFF; // 获取 RGB 部分
	
	// 提取红色、绿色和蓝色通道
	$r = ($realColor >> 16) & 0xFF; // 红色通道
	$g = ($realColor >> 8) & 0xFF;  // 绿色通道
	$b = $realColor & 0xFF;         // 蓝色通道

	// 格式化为两位十六进制并连接
	$hexColor = sprintf("%02X%02X%02X", $r, $g, $b);
	
	return $hexColor;
}


?>