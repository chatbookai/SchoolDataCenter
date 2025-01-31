<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

function AiToPptx_DrawSingleObject($childrenItem, $DirPath)  {
	global $SharpCounter;
	$Type 			= $childrenItem['type'];
  $Point 			= $childrenItem['point'];
	$anchor 		= $childrenItem['extInfo']['property']['anchor'];
	$realType 		= $childrenItem['extInfo']['property']['realType'];
	$shapeType 		= $childrenItem['extInfo']['property']['shapeType'];
	$fillStyle 		= $childrenItem['extInfo']['property']['fillStyle'];
	$strokeStyle 	= $childrenItem['extInfo']['property']['strokeStyle'];
	$effectLst 		= $childrenItem['extInfo']['property']['effectLst'];
  $geometry 		= $childrenItem['extInfo']['property']['geometry'];
	$placeholder	= $childrenItem['extInfo']['property']['placeholder'];
	$prstTxWarp 	= $childrenItem['extInfo']['property']['prstTxWarp'];
	$flipVertical 	= $childrenItem['extInfo']['property']['flipVertical'];
	$flipHorizontal = $childrenItem['extInfo']['property']['flipHorizontal'];
	$rotation 		= $childrenItem['extInfo']['property']['rotation'];
	$fileName 		= $childrenItem['extInfo']['property']['fileName'];
	$imageData 		= $childrenItem['extInfo']['property']['image'];
	$extension 		= $childrenItem['extInfo']['property']['extension'];
	$contentType	= $childrenItem['extInfo']['property']['contentType'];
	$clipping 		= $childrenItem['extInfo']['property']['clipping'];
	//print_R($childrenItem['type']);

	// 1. 创建 DOMDocument 对象
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true; // 格式化输出

	// 2. 创建根元素 <p:sp> 并附加到 DOM
  //text/freeform/image/container/diagram/connector/table/graphicFrame
	switch($Type) {
		case 'text':
			$pSp = $dom->createElement('p:sp');
			break;
		case 'freeform':
			$pSp = $dom->createElement('p:sp');
			break;
		case 'image':
			$pSp = $dom->createElement('p:pic');
			break;
		case 'container':
			$pSp = $dom->createElement('p:container');
			break;
		case 'diagram':
			$pSp = $dom->createElement('p:diagram');
			break;
		case 'connector':
			$pSp = $dom->createElement('p:cxnSp');
			break;
		case 'table':
			$pSp = $dom->createElement('p:tbl');
			break;
		case 'graphicFrame':
			$pSp = $dom->createElement('p:graphicFrame');
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

  if($Type == "connector")  {
		$nvSpPr = $dom->createElement('p:nvCxnSpPr');
		$pSp->appendChild($nvSpPr);
		$cNvPr = $dom->createElement('p:cNvPr');
		$cNvPr->setAttribute('name', $realType .' '. $SharpCounter++);
		$cNvPr->setAttribute('id', $SharpCounter++);
		$nvSpPr->appendChild($cNvPr);
		$cNvSpPr = $dom->createElement('p:cNvCxnSpPr');
		$cNvSpPr->setAttribute('id', $SharpCounter++);
		if($realType == "TextBox") {
			$cNvSpPr->setAttribute('txBox', 'true');
		}
		$nvSpPr->appendChild($cNvSpPr);
	}

	if($Type == "image" && $fileName != "")  {

		// 添加 <p:nvPicPr> 子元素及其子元素
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

    // 存储图片 - 存在文件类型和文件名
    if($imageData != "")  {
      AiToPptx_SaveBase64ImageToFile($imageData, $DirPath."/".$fileName);
      global $关系引用ID值列表SlideLayout;
      $关系引用ID = sizeof((array)$关系引用ID值列表SlideLayout) + 1;
      $关系引用ID值列表SlideLayout[] = '<Relationship Id="rId'.$关系引用ID.'" Target="../media/'.$fileName.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image"/>';
    }

		// 创建 <p:blipFill> 元素
		$p_blipFill = $dom->createElement('p:blipFill');
		// 创建 <a:blip> 元素，并添加 r:embed 属性
		$a_blip = $dom->createElement('a:blip');
		$a_blip->setAttribute('r:embed', 'rId'.$关系引用ID);
		// 将 <a:blip> 添加到 <p:blipFill> 中
		$p_blipFill->appendChild($a_blip);

    //这个是需要使用的
    if($fillStyle['texture']['alphaModFix'] != "")   {
      $a_alphaModFix = $dom->createElement('a:alphaModFix');
      $a_alphaModFix->setAttribute('amt', $fillStyle['texture']['alphaModFix']);
      $a_blip->appendChild($a_alphaModFix);
    }

    if($clipping[1] == "91199")  {
      //print_R($childrenItem);exit;
    }

		$a_srcRect = $dom->createElement('a:srcRect');
		if(isset($clipping[0]) && $clipping[0]>0 ) $a_srcRect->setAttribute('t', $clipping[0]);
		if(isset($clipping[1]) && $clipping[1]>0 ) $a_srcRect->setAttribute('l', $clipping[1]);
		if(isset($clipping[2]) && $clipping[2]>0 ) $a_srcRect->setAttribute('b', $clipping[2]);
		if(isset($clipping[3]) && $clipping[3]>0 ) $a_srcRect->setAttribute('r', $clipping[3]);
		$p_blipFill->appendChild($a_srcRect);

		$a_stretch = $dom->createElement('a:stretch');
		$a_fillRect = $dom->createElement('a:fillRect');
		$a_fillRect->setAttribute('t', $fillStyle['texture']['stretch'][0]);
		$a_fillRect->setAttribute('l', $fillStyle['texture']['stretch'][1]);
		$a_fillRect->setAttribute('b', $fillStyle['texture']['stretch'][2]);
		$a_fillRect->setAttribute('r', $fillStyle['texture']['stretch'][3]);
		$a_stretch->appendChild($a_fillRect);

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
		case 'CONTENT':
			$pPh = $dom->createElement('p:ph');
			$pPh->setAttribute('type', 'obj');
			if(isset($placeholder['size'])) $pPh->setAttribute('sz', strtolower($placeholder['size']));
			if(isset($placeholder['idx'])) 	$pPh->setAttribute('idx', strtolower($placeholder['idx']));
			$nvPr->appendChild($pPh);
			break;
	}

	if($nvSpPr)  {
		$nvSpPr->appendChild($nvPr);
	}

	// 4. 添加 <p:spPr> 及其子元素
  if($Type == "connector")  {
    $spPr = $dom->createElement('p:spPr');
  }
  else {
    $spPr = $dom->createElement('p:spPr');
  }
	$pSp->appendChild($spPr);

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
	$spPr->appendChild($xfrm);

	$off = $dom->createElement('a:off');
	$off->setAttribute('x', strval(intval($anchor[0] * 12700)));
	$off->setAttribute('y', strval(intval($anchor[1] * 12700)));
	$xfrm->appendChild($off);

  if(strval(intval($anchor[0] * 12700)) == "7077820")  {
    //print_R($fillStyle)."\n"; exit;
  }

	$ext = $dom->createElement('a:ext');
	$ext->setAttribute('cx', strval(intval($anchor[2] * 12700)));
	$ext->setAttribute('cy', strval(intval($anchor[3] * 12700)));
	$xfrm->appendChild($ext);
  // ellipse roundRect
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

  if(isset($childrenItem['extInfo']['property']['scene3d']))  {
    $scene3d = $childrenItem['extInfo']['property']['scene3d'];
		$a_scene3d = $dom->createElement('a:scene3d');

    if($scene3d['cameraPrst']!="")  {
      $camera = $dom->createElement('a:camera');
      $camera->setAttribute('prst', $scene3d['cameraPrst']);
      $camera->setAttribute('zoom', $scene3d['cameraZoom']);
      $a_scene3d->appendChild($camera);
    }
    if($scene3d['lightRigRig']!="")  {
      $lightRig = $dom->createElement('a:lightRig');
      $lightRig->setAttribute('rig', $scene3d['lightRigRig']);
      $lightRig->setAttribute('dir', $scene3d['lightRigDir']);
      $rot = $dom->createElement('a:rot');
      $rot->setAttribute('lat', $scene3d['lightRigRot']['x'] * 60000);
      $rot->setAttribute('lon', $scene3d['lightRigRot']['y'] * 60000);
      $rot->setAttribute('rev', $scene3d['lightRigRot']['z'] * 60000);
      $lightRig->appendChild($rot);
      $a_scene3d->appendChild($lightRig);
    }

    $spPr->appendChild($a_scene3d);
	}

  if(isset($childrenItem['extInfo']['property']['sp3d']))  {
    $sp3d = $childrenItem['extInfo']['property']['sp3d'];
		$a_sp3d = $dom->createElement('a:sp3d');
    if($sp3d['extrusionH']!='') {
      $a_sp3d->setAttribute('extrusionH', $sp3d['extrusionH']);
    }

    $spPr->appendChild($a_sp3d);
	}

	//绘制任意几何图形
  //print "TYPE:".$Type." ".$geometry['name']."<BR>";
	if ( ($Type == "text" && $geometry['name'] == "custom") || ($realType == "Picture" && $geometry['name'] == "custom")
      ) {
		//print_R($childrenItem);
		$a_custGeom = $dom->createElement('a:custGeom');
		$spPr->appendChild($a_custGeom);
    //print_R($geometry);
		// 添加节点
    $geometryKeys = array_keys($geometry);
		if(in_array('avLst', $geometryKeys)) {
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

    }

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
			$points = explode(' ', trim(substr($command, 1))); // 提取点数据

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

  $spPr = 渲染fillStyle($dom, $fillStyle, $spPr, $DirPath);
  $spPr = 渲染strokeStyle($dom, $strokeStyle, $spPr, $DirPath);

  if(strval(intval($anchor[0] * 12700)) == "7077820")  {
    //print_R($fillStyle); print $dom->saveXML(); exit;
  }

  if(is_array($effectLst))  {
    $a_effectLst = $dom->createElement('a:effectLst');

    $shadow 		 = $childrenItem['extInfo']['property']['shadow'];
    if(is_array($shadow)) {
      $outerShdw = $dom->createElement('a:outerShdw');
      $outerShdw->setAttribute('algn', $shadow['algn']);
      $outerShdw->setAttribute('blurRad', $shadow['blur'] * 12700);
      $outerShdw->setAttribute('dir', $shadow['angle'] * 60000);
      $outerShdw->setAttribute('dist', $shadow['distance'] * 12700);
      $outerShdw->setAttribute('rotWithShape', $shadow['rotWithShape'] == 1 ? 'true' : 'false');

      $fillStyleInShadow = $shadow['fillStyle'];
      $渲染fillStyleInShadow = 渲染fillStyle($dom, $fillStyleInShadow, $outerShdw, $DirPath, $Show_A_SolidFill=false);
      //print_R($fillStyleInShadow);//exit;
      $a_effectLst->appendChild($渲染fillStyleInShadow);
    }
    if($effectLst['softEdgeRad'] != '')  {
      $softEdge = $dom->createElement('a:softEdge');
      $softEdge->setAttribute('rad', $effectLst['softEdgeRad'] * 12700);
      $a_effectLst->appendChild($softEdge);
    }

		$spPr->appendChild($a_effectLst);
  }

	// 5. 添加 <p:txBody> 及其子元素
	if($realType != "Picture")  {
		$txBody = $dom->createElement('p:txBody');
		$pSp->appendChild($txBody);

		$bodyPr = $dom->createElement('a:bodyPr');
		//$bodyPr->setAttribute('rtlCol', 'false');
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
				case 'RESIZE_SHAPE':
					$spAutoFit = $dom->createElement('a:spAutoFit');
					$bodyPr->appendChild($spAutoFit);
					break;
			}
      if(is_array($childrenItem['extInfo']['property']['textInsets']))  {
        // 未实现 - [3.6, 7.2, 3.6, 7.2] // 文本形状调整边距 [top,left,bottom,right]
        $bodyPr->setAttribute('tIns', intval($childrenItem['extInfo']['property']['textInsets'][0] * 12700));
        $bodyPr->setAttribute('lIns', intval($childrenItem['extInfo']['property']['textInsets'][1] * 12700));
        $bodyPr->setAttribute('bIns', intval($childrenItem['extInfo']['property']['textInsets'][2] * 12700));
        $bodyPr->setAttribute('rIns', intval($childrenItem['extInfo']['property']['textInsets'][3] * 12700));
      }
		}
		switch($childrenItem['extInfo']['property']['textDirection']) {
			case 'HORIZONTAL':
				$bodyPr->setAttribute('vert', 'horz');
				break;
			case 'VERTICAL':
				$bodyPr->setAttribute('vert', 'vert');
				break;
			case 'EA_VERTICAL':
				$bodyPr->setAttribute('vert', 'eaVert');
				break;
			case 'VERTICAL_270':
				$bodyPr->setAttribute('vert', 'vert270'); //未检验
				break;
			case 'STACKED':
				$bodyPr->setAttribute('vert', 'stacked'); //未检验
				break;
			case 'WORD_ART_VERT':
				$bodyPr->setAttribute('vert', 'WORD_ART_VERT'); //未检验
				break;
		}
    if(isset($childrenItem['extInfo']['property']['textRotation'])) {
			$bodyPr->setAttribute('rot', $childrenItem['extInfo']['property']['textRotation']);
		}
		switch($childrenItem['extInfo']['property']['textVerticalAlignment']) {
			case 'TOP':
				$bodyPr->setAttribute('anchor', 't');
				break;
			case 'BOTTOM':
				$bodyPr->setAttribute('anchor', 'b');
				break;
			case 'LEFT':
				//$bodyPr->setAttribute('anchor', 'l');
				break;
			case 'RIGHT':
				//$bodyPr->setAttribute('anchor', 'r');
				break;
			case 'CENTER':
				//$bodyPr->setAttribute('anchor', 'ctr');
				break;
			case 'MIDDLE':
				$bodyPr->setAttribute('anchor', 'ctr');
				break;
			case 'JUSTIFY':
				$bodyPr->setAttribute('anchor', 'JUSTIFY'); //未检验
				break;
			case 'DISTRIBUTED':
				$bodyPr->setAttribute('anchor', 'DISTRIBUTED'); //未检验
				break;
		}
		if($childrenItem['extInfo']['property']['textWordWrap'] == 1) {
			$bodyPr->setAttribute('wrap', 'square');
		}
		elseif(isset($childrenItem['extInfo']['property']['textWordWrap'])) {
			$bodyPr->setAttribute('wrap', 'none');
		}
		if($childrenItem['extInfo']['property']['textHorizontalCentered'] == 1) {
			$bodyPr->setAttribute('anchorCtr', 'true');
		}
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

		//$lstStyle = $dom->createElement('a:lstStyle');
		//$txBody->appendChild($lstStyle);

		// 6. 文本框, 创建段落 <a:p> 及其内容, <a:p>对像可能会有多个,所以需要循环过滤
		$childrenList 	= $childrenItem['children'];
		foreach($childrenList as $childrenListItem)  {
			$文本属性 		= $childrenListItem['extInfo'];
			if ($Type == "text" || $childrenListItem['extInfo']['type'] == "p") {
				$p = $dom->createElement('a:p');
				$txBody->appendChild($p);

				$pPr = $dom->createElement('a:pPr');
				$p->appendChild($pPr);
        switch($文本属性['property']['fontAlign']) {
					case 'AUTO':
						$pPr->setAttribute('fontAlgn', 'auto');
						break;
          case 'TOP':
            $pPr->setAttribute('fontAlgn', 't');
            break;
					case 'CENTER':
						$pPr->setAttribute('fontAlgn', 'ctr');
						break;
					case 'BASELINE':
						$pPr->setAttribute('fontAlgn', 'base');
						break;
					case 'BOTTOM':
						$pPr->setAttribute('fontAlgn', 'b');
						break;
				}
        //print $文本属性['property']['textAlign'];
				switch($文本属性['property']['textAlign']) {
					case 'LEFT':
						$pPr->setAttribute('algn', 'l');
						break;
					case 'CENTER':
						$pPr->setAttribute('algn', 'ctr');
						break;
					case 'RIGHT':
						$pPr->setAttribute('algn', 'r');
						break;
					case 'JUSTIFY':
						$pPr->setAttribute('algn', 'just');
						break;
					case 'JUSTIFY_LOW':
						$pPr->setAttribute('algn', 'JUSTIFY_LOW');
						break;
					case 'DIST':
						$pPr->setAttribute('algn', 'dist');
						break;
					case 'THAI_DIST':
						$pPr->setAttribute('algn', 'THAI_DIST');
						break;
				}
				if(isset($文本属性['property']['indent'])) {
					$pPr->setAttribute('indent', intval($文本属性['property']['indent'] * 12700));
				}
				if(isset($文本属性['property']['indentLevel'])) {
					$pPr->setAttribute('lvl', intval($文本属性['property']['indentLevel']));
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
          if($文本属性['property']['lineSpacing']>=50)  {
            $spcPct = $dom->createElement('a:spcPct');
            $spcPct->setAttribute('val', strval(abs(intval($文本属性['property']['lineSpacing'] * 1000))));
            $lnSpc->appendChild($spcPct);
          }
          else {
            $spcPts = $dom->createElement('a:spcPts');
            $spcPts->setAttribute('val', strval(abs(intval($文本属性['property']['lineSpacing'] * 100))));
            $lnSpc->appendChild($spcPts);
          }
					$pPr->appendChild($lnSpc);
				}
				if(isset($文本属性['property']['spaceBefore'])) {
					$spcBef = $dom->createElement('a:spcBef');
					$spcPts = $dom->createElement('a:spcPts');
					$spcPts->setAttribute('val', strval(abs(intval($文本属性['property']['spaceBefore'] * 100))));
					$spcBef->appendChild($spcPts);
					$pPr->appendChild($spcBef);
				}
        if(isset($文本属性['property']['spaceAfter'])) {
					$spcAft = $dom->createElement('a:spcAft');
					$spcPts = $dom->createElement('a:spcPts');
					$spcPts->setAttribute('val', strval(abs(intval($文本属性['property']['spaceAfter'] * 100))));
					$spcAft->appendChild($spcPts);
					$pPr->appendChild($spcAft);
				}
				if(isset($文本属性['property']['bulletStyle']['buNone'])) {
					$buNone = $dom->createElement('a:buNone');
					$pPr->appendChild($buNone);
				}

        // 未实现 leftMargin rightMargin spaceAfter

        //print_R($文本属性); 暂时注释掉
				//$defRPr = $dom->createElement('a:defRPr');
				//$pPr->appendChild($defRPr);


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

            $rPr = 渲染fillStyle($dom, $文本对像['extInfo']['property']['fontColor'], $rPr, $DirPath);

						$latin = $dom->createElement('a:latin');
						$latin->setAttribute('typeface', $文本对像['extInfo']['property']['fontFamily']);
						$rPr->appendChild($latin);

						$ea = $dom->createElement('a:ea');
						$ea->setAttribute('typeface', $文本对像['extInfo']['property']['fontFamily']);
						$rPr->appendChild($ea);

						$t = $dom->createElement('a:t', $文本对像['text']);
						$r->appendChild($t);

            if($文本对像['text'] == "添加文本")  {
              //print_R($文本对像);exit;
            }
					}
				}

				//$endParaRPr = $dom->createElement('a:endParaRPr');
				//$endParaRPr->setAttribute('lang', 'en-US');
				//$endParaRPr->setAttribute('sz', '1100');
				//$p->appendChild($endParaRPr);
			}

		}

	}
	if(strval(intval($anchor[0] * 12700)) == '1490272')  {
		//print_R($childrenItem);
		//print_R($dom->saveXML());
	}
  //print_R($dom->saveXML());exit;

	return $pSp;
}


function 渲染fillStyle($dom, $fillStyle, $spPr, $DirPath, $Show_A_SolidFill=true)                {

  if(substr($DirPath, -10) != "/ppt/media") {
    $DirPath .= "/ppt/media";
  }
  //print $DirPath." 渲染fillStyle \n";

  //color/gradient/texture/pattern/groupFill/bgFill/noFill
  if ($fillStyle['type'] == 'texture')        {

    // 存储图片 - 没有文件名,应该是纹路填充类
    if(strlen($fillStyle['texture']['imageData'])>100 && $fillStyle['texture']['contentType'] == "image/jpeg")  {
      global $GlobalImageCounter;
      $GlobalImageCounter += 1;
      $fileName = "image".$GlobalImageCounter.".jpeg";
      AiToPptx_SaveBase64ImageToFile($fillStyle['texture']['imageData'], $DirPath."/".$fileName);
      global $关系引用ID值列表SlideLayout;
      $关系引用ID = sizeof((array)$关系引用ID值列表SlideLayout) + 1;
      $关系引用ID值列表SlideLayout[] = '<Relationship Id="rId'.$关系引用ID.'" Target="../media/'.$fileName.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image"/>';
    }
    if(strlen($fillStyle['texture']['imageData'])>100 && $fillStyle['texture']['contentType'] == "image/png")  {
      //print_R($fillStyle);
      global $GlobalImageCounter;
      $GlobalImageCounter += 1;
      $fileName = "image".$GlobalImageCounter.".png";
      //print $fileName."\n";
      AiToPptx_SaveBase64ImageToFile($fillStyle['texture']['imageData'], $DirPath."/".$fileName);
      global $关系引用ID值列表SlideLayout;
      $关系引用ID = sizeof((array)$关系引用ID值列表SlideLayout) + 1;
      $关系引用ID值列表SlideLayout[] = '<Relationship Id="rId'.$关系引用ID.'" Target="../media/'.$fileName.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image"/>';
    }
    //print $GlobalImageCounter;exit;

    $a_blipFill = $dom->createElement('a:blipFill');
    $blip = $dom->createElement('a:blip');
    $blip->setAttribute('r:embed', 'rId' . $关系引用ID);

    if($fillStyle['texture']['alphaModFix'] != "")   {
      $a_alphaModFix = $dom->createElement('a:alphaModFix');
      $a_alphaModFix->setAttribute('amt', $fillStyle['texture']['alphaModFix']);
      $blip->appendChild($a_alphaModFix);
    }

    if($fillStyle['texture']['duoTone'][0]['scheme'] != "")  {
      $duotone = $dom->createElement('a:duotone');

      if($fillStyle['texture']['duoTonePrst'][0])   {
        $prstClr = $dom->createElement('a:prstClr');
        $prstClr->setAttribute('val', $fillStyle['texture']['duoTonePrst'][0]);
        $duotone->appendChild($prstClr);
      }

      $schemeClr = $dom->createElement('a:schemeClr');
      $schemeClr->setAttribute('val', $fillStyle['texture']['duoTone'][0]['scheme']);
      if($fillStyle['texture']['duoTone'][0]['alpha'] != "")  {
				$a_alpha = $dom->createElement('a:alpha');
				$a_alpha->setAttribute('val', $fillStyle['texture']['duoTone'][0]['alpha']);
				$schemeClr->appendChild($a_alpha);
			}
			if($fillStyle['texture']['duoTone'][0]['lumMod'] != "")  {
				$lumMod = $dom->createElement('a:lumMod');
				$lumMod->setAttribute('val', $fillStyle['texture']['duoTone'][0]['lumMod']);
				$schemeClr->appendChild($lumMod);
			}
			if($fillStyle['texture']['duoTone'][0]['lumOff'] != "")  {
				$lumOff = $dom->createElement('a:lumOff');
				$lumOff->setAttribute('val', $fillStyle['texture']['duoTone'][0]['lumOff']);
				$schemeClr->appendChild($lumOff);
			}
			if($fillStyle['texture']['duoTone'][0]['hueMod'] != "")  {
				$hueMod = $dom->createElement('a:hueMod');
				$hueMod->setAttribute('val', $fillStyle['texture']['duoTone'][0]['hueMod']);
				$schemeClr->appendChild($hueMod);
			}
			if($fillStyle['texture']['duoTone'][0]['hueOff'] != "")  {
				$hueOff = $dom->createElement('a:hueOff');
				$hueOff->setAttribute('val', $fillStyle['texture']['duoTone'][0]['hueOff']);
				$schemeClr->appendChild($hueOff);
			}
			if($fillStyle['texture']['duoTone'][0]['satMod'] != "")  {
				$satMod = $dom->createElement('a:satMod');
				$satMod->setAttribute('val', $fillStyle['texture']['duoTone'][0]['satMod']);
				$schemeClr->appendChild($satMod);
			}
			if($fillStyle['texture']['duoTone'][0]['satOff'] != "")  {
				$satOff = $dom->createElement('a:satOff');
				$satOff->setAttribute('val', $fillStyle['texture']['duoTone'][0]['satOff']);
				$schemeClr->appendChild($satOff);
			}
			if($fillStyle['texture']['duoTone'][0]['shade'] != "")  {
				$shade = $dom->createElement('a:shade');
				$shade->setAttribute('val', $fillStyle['texture']['duoTone'][0]['shade']);
				$schemeClr->appendChild($shade);
			}
			if($fillStyle['texture']['duoTone'][0]['tint'] != "")  {
				$tint = $dom->createElement('a:tint');
				$tint->setAttribute('val', $fillStyle['texture']['duoTone'][0]['tint']);
				$schemeClr->appendChild($tint);
			}

      $duotone->appendChild($schemeClr);

      $blip->appendChild($duotone);
    }

    //TOP/TOP_LEFT/TOP_RIGHT/LEFT/BOTTOM/BOTTOM_LEFT/BOTTOM_RIGHT/RIGHT/CENTER
    if($fillStyle['texture']['alignment'])   {
      switch($fillStyle['texture']['alignment']) {
        case 'TOP':
          $blip->setAttribute('algn', 't');
          break;
        case 'TOP_LEFT':
          $blip->setAttribute('algn', 'tl');
          break;
        case 'TOP_RIGHT':
          $blip->setAttribute('algn', 'tr');
          break;
        case 'LEFT':
          $blip->setAttribute('algn', 'l');
          break;
        case 'BOTTOM':
          $blip->setAttribute('algn', 'b');
          break;
        case 'BOTTOM_LEFT':
          $blip->setAttribute('algn', 'bl');
          break;
        case 'BOTTOM_RIGHT':
          $blip->setAttribute('algn', 'br');
          break;
        case 'RIGHT':
          $blip->setAttribute('algn', 'r');
          break;
        case 'CENTER':
          $blip->setAttribute('algn', 'c');
          break;
      }
    }

    //X,Y,XY,NONE
    if($fillStyle['texture']['flipMode'])   {
      switch($fillStyle['texture']['flipMode']) {
        case 'X':
          //$blip->setAttribute('algn', 'x');
          break;
        case 'Y':
          //$blip->setAttribute('algn', 'y');
          break;
        case 'XY':
          //$blip->setAttribute('algn', 'xy');
          break;
        case 'NONE':
          //$blip->setAttribute('algn', 'none');
          break;
      }
    }

    // 将 <a:blip> 添加到 <a:blipFill>
    $a_blipFill->appendChild($blip);

    $srcRect = $dom->createElement('a:srcRect');
    $a_blipFill->appendChild($srcRect);

    $a_stretch = $dom->createElement('a:stretch');
		$a_fillRect = $dom->createElement('a:fillRect');
		$a_fillRect->setAttribute('t', $fillStyle['texture']['stretch'][0]);
		$a_fillRect->setAttribute('l', $fillStyle['texture']['stretch'][1]);
		$a_fillRect->setAttribute('b', $fillStyle['texture']['stretch'][2]);
		$a_fillRect->setAttribute('r', $fillStyle['texture']['stretch'][3]);
		$a_stretch->appendChild($a_fillRect);

    $a_blipFill->appendChild($a_stretch);

    if(strlen($fillStyle['texture']['imageData'])>100)  {
      $spPr->appendChild($a_blipFill); //不要增加,有些地方是不需要输出的.
    }
    //print_R($childrenItem)."\n"; exit;
	}

	if ($fillStyle['type'] == 'color') {
		// 创建 <a:solidFill> 节点
		$a_solidFill = $dom->createElement('a:solidFill');
    if($Show_A_SolidFill) {
      $spPr->appendChild($a_solidFill);
    }

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
			if($fillStyle['color']['hueMod'] != "")  {
				$hueMod = $dom->createElement('a:hueMod');
				$hueMod->setAttribute('val', $fillStyle['color']['hueMod']);
				$a_schemeClr->appendChild($hueMod);
			}
			if($fillStyle['color']['hueOff'] != "")  {
				$hueOff = $dom->createElement('a:hueOff');
				$hueOff->setAttribute('val', $fillStyle['color']['hueOff']);
				$a_schemeClr->appendChild($hueOff);
			}
			if($fillStyle['color']['satMod'] != "")  {
				$satMod = $dom->createElement('a:satMod');
				$satMod->setAttribute('val', $fillStyle['color']['satMod']);
				$a_schemeClr->appendChild($satMod);
			}
			if($fillStyle['color']['satOff'] != "")  {
				$satOff = $dom->createElement('a:satOff');
				$satOff->setAttribute('val', $fillStyle['color']['satOff']);
				$a_schemeClr->appendChild($satOff);
			}
			if($fillStyle['color']['shade'] != "")  {
				$shade = $dom->createElement('a:shade');
				$shade->setAttribute('val', $fillStyle['color']['shade']);
				$a_schemeClr->appendChild($shade);
			}
			if($fillStyle['color']['tint'] != "")  {
				$tint = $dom->createElement('a:tint');
				$tint->setAttribute('val', $fillStyle['color']['tint']);
				$a_schemeClr->appendChild($tint);
			}

      //在shadow的fillStyle,不需要挂在 a_solidFill 下面,则是直接挂在父结点下面,具体控制参数,还没有找到
      if($Show_A_SolidFill == false) {
        $spPr->appendChild($a_schemeClr);
      }
      else {
        $a_solidFill->appendChild($a_schemeClr);
      }
		}

		if($fillStyle['color']['realColor'] != '' && $fillStyle['color']['scheme'] == '')  {
			$srgbClr = $dom->createElement('a:srgbClr');
			if($fillStyle['color']['color'] == '-1')  {
				$srgbClr->setAttribute('val', 'FFFFFF');
			}
			else {
				$srgbClr->setAttribute('val', AiToPptx_NumberToColor($fillStyle['color']['color']));
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
			if($fillStyle['color']['hueMod'] != "" && $fillStyle['color']['scheme'] == "")  {
				$hueMod = $dom->createElement('a:hueMod');
				$hueMod->setAttribute('val', $fillStyle['color']['hueMod']);
				$srgbClr->appendChild($hueMod);
			}
			if($fillStyle['color']['hueOff'] != "" && $fillStyle['color']['scheme'] == "")  {
				$hueOff = $dom->createElement('a:hueOff');
				$hueOff->setAttribute('val', $fillStyle['color']['hueOff']);
				$srgbClr->appendChild($hueOff);
			}
			if($fillStyle['color']['satMod'] != "" && $fillStyle['color']['scheme'] == "")  {
				$satMod = $dom->createElement('a:satMod');
				$satMod->setAttribute('val', $fillStyle['color']['satMod']);
				$srgbClr->appendChild($satMod);
			}
			if($fillStyle['color']['satOff'] != "" && $fillStyle['color']['scheme'] == "")  {
				$satOff = $dom->createElement('a:satOff');
				$satOff->setAttribute('val', $fillStyle['color']['satOff']);
				$srgbClr->appendChild($satOff);
			}
			if($fillStyle['color']['shade'] != "" && $fillStyle['color']['scheme'] == "")  {
				$shade = $dom->createElement('a:shade');
				$shade->setAttribute('val', $fillStyle['color']['shade']);
				$srgbClr->appendChild($shade);
			}
			if($fillStyle['color']['tint'] != "" && $fillStyle['color']['scheme'] == "")  {
				$tint = $dom->createElement('a:tint');
				$tint->setAttribute('val', $fillStyle['color']['tint']);
				$srgbClr->appendChild($tint);
			}

      //在shadow的fillStyle,不需要挂在 a_solidFill 下面,则是直接挂在父结点下面,具体控制参数,还没有找到
      if($Show_A_SolidFill == false) {
        $spPr->appendChild($srgbClr);
      }
      else {
        $a_solidFill->appendChild($srgbClr);
      }
		}

		// 将 <a:schemeClr> 添加到 <a:solidFill>
	}

	if ($fillStyle['type'] == 'gradient') {
		// 创建 <a:gradFill> 根节点
		$gradFill = $dom->createElement('a:gradFill');
		$dom->appendChild($gradFill);
    if($fillStyle['gradient']['rotWithShape'] == "1") {
      $gradFill->setAttribute('rotWithShape', "true");
    }
    if($fillStyle['gradient']['flip'] != "") {
      $gradFill->setAttribute('flip', $fillStyle['gradient']['flip']);
    }

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
			if($color['hueMod'] != "")  {
				$hueMod = $dom->createElement('a:hueMod');
				$hueMod->setAttribute('val', $color['hueMod']);
				$srgbClr->appendChild($hueMod);
			}
			if($color['hueOff'] != "")  {
				$hueOff = $dom->createElement('a:hueOff');
				$hueOff->setAttribute('val', $color['hueOff']);
				$srgbClr->appendChild($hueOff);
			}
			if($color['satMod'] != "")  {
				$satMod = $dom->createElement('a:satMod');
				$satMod->setAttribute('val', $color['satMod']);
				$srgbClr->appendChild($satMod);
			}
			if($color['satOff'] != "")  {
				$satOff = $dom->createElement('a:satOff');
				$satOff->setAttribute('val', $color['satOff']);
				$srgbClr->appendChild($satOff);
			}
			if($color['shade'] != "")  {
				$shade = $dom->createElement('a:shade');
				$shade->setAttribute('val', $color['shade']);
				$srgbClr->appendChild($shade);
			}
			if($color['tint'] != "")  {
				$tint = $dom->createElement('a:tint');
				$tint->setAttribute('val', $color['tint']);
				$srgbClr->appendChild($tint);
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

  if ($fillStyle['type'] == 'pattern') {
    $pattFill = $dom->createElement('a:pattFill');
    $spPr->appendChild($pattFill);

    if($fillStyle['pattern']['prst'] != "")   {
      $pattFill->setAttribute('prst', $fillStyle['pattern']['prst']);
    }

    if(is_array($fillStyle['pattern']['fgColor']))   {
      $fgClr = $dom->createElement('a:fgClr');

      $schemeClrFg = $dom->createElement('a:schemeClr');
      $schemeClrFg->setAttribute('val', $fillStyle['pattern']['fgColor']['scheme']);

      if($fillStyle['pattern']['fgColor']['alpha'] != '')  {
        $alpha = $dom->createElement('a:alpha');
        $alpha->setAttribute('val', $fillStyle['pattern']['fgColor']['alpha']);
        $schemeClrFg->appendChild($alpha);
      }
      if($fillStyle['pattern']['fgColor']['lumMod'] != '')  {
        $lumMod = $dom->createElement('a:lumMod');
        $lumMod->setAttribute('val', $fillStyle['pattern']['fgColor']['lumMod']);
        $schemeClrFg->appendChild($lumMod);
      }
      if($fillStyle['pattern']['fgColor']['lumOff'] != '')  {
        $lumOff = $dom->createElement('a:lumOff');
        $lumOff->setAttribute('val', $fillStyle['pattern']['fgColor']['lumOff']);
        $schemeClrFg->appendChild($lumOff);
      }
      if($fillStyle['pattern']['fgColor']['hueMod'] != "")  {
        $hueMod = $dom->createElement('a:hueMod');
        $hueMod->setAttribute('val', $fillStyle['pattern']['fgColor']['hueMod']);
        $schemeClrFg->appendChild($hueMod);
      }
      if($fillStyle['pattern']['fgColor']['hueOff'] != "")  {
        $hueOff = $dom->createElement('a:hueOff');
        $hueOff->setAttribute('val', $fillStyle['pattern']['fgColor']['hueOff']);
        $schemeClrFg->appendChild($hueOff);
      }
      if($fillStyle['pattern']['fgColor']['satMod'] != "")  {
        $satMod = $dom->createElement('a:satMod');
        $satMod->setAttribute('val', $fillStyle['pattern']['fgColor']['satMod']);
        $schemeClrFg->appendChild($satMod);
      }
      if($fillStyle['pattern']['fgColor']['satOff'] != "")  {
        $satOff = $dom->createElement('a:satOff');
        $satOff->setAttribute('val', $fillStyle['pattern']['fgColor']['satOff']);
        $schemeClrFg->appendChild($satOff);
      }
      if($fillStyle['pattern']['fgColor']['shade'] != "")  {
        $shade = $dom->createElement('a:shade');
        $shade->setAttribute('val', $fillStyle['pattern']['fgColor']['shade']);
        $schemeClrFg->appendChild($shade);
      }
      if($fillStyle['pattern']['fgColor']['tint'] != "")  {
        $tint = $dom->createElement('a:tint');
        $tint->setAttribute('val', $fillStyle['pattern']['fgColor']['tint']);
        $schemeClrFg->appendChild($tint);
      }

      $fgClr->appendChild($schemeClrFg);
      $pattFill->appendChild($fgClr);
    }

    if(is_array($fillStyle['pattern']['bgColor']))   {
      $bgClr = $dom->createElement('a:bgClr');

      $schemeClrFg = $dom->createElement('a:schemeClr');
      $schemeClrFg->setAttribute('val', $fillStyle['pattern']['bgColor']['scheme']);

      if($fillStyle['pattern']['bgColor']['alpha'] != '')  {
        $alpha = $dom->createElement('a:alpha');
        $alpha->setAttribute('val', $fillStyle['pattern']['bgColor']['alpha']);
        $schemeClrFg->appendChild($alpha);
      }
      if($fillStyle['pattern']['bgColor']['lumMod'] != '')  {
        $lumMod = $dom->createElement('a:lumMod');
        $lumMod->setAttribute('val', $fillStyle['pattern']['bgColor']['lumMod']);
        $schemeClrFg->appendChild($lumMod);
      }
      if($fillStyle['pattern']['bgColor']['lumOff'] != '')  {
        $lumOff = $dom->createElement('a:lumOff');
        $lumOff->setAttribute('val', $fillStyle['pattern']['bgColor']['lumOff']);
        $schemeClrFg->appendChild($lumOff);
      }
      if($fillStyle['pattern']['bgColor']['hueMod'] != "")  {
        $hueMod = $dom->createElement('a:hueMod');
        $hueMod->setAttribute('val', $fillStyle['pattern']['bgColor']['hueMod']);
        $schemeClrFg->appendChild($hueMod);
      }
      if($fillStyle['pattern']['bgColor']['hueOff'] != "")  {
        $hueOff = $dom->createElement('a:hueOff');
        $hueOff->setAttribute('val', $fillStyle['pattern']['bgColor']['hueOff']);
        $schemeClrFg->appendChild($hueOff);
      }
      if($fillStyle['pattern']['bgColor']['satMod'] != "")  {
        $satMod = $dom->createElement('a:satMod');
        $satMod->setAttribute('val', $fillStyle['pattern']['bgColor']['satMod']);
        $schemeClrFg->appendChild($satMod);
      }
      if($fillStyle['pattern']['bgColor']['satOff'] != "")  {
        $satOff = $dom->createElement('a:satOff');
        $satOff->setAttribute('val', $fillStyle['pattern']['bgColor']['satOff']);
        $schemeClrFg->appendChild($satOff);
      }
      if($fillStyle['pattern']['bgColor']['shade'] != "")  {
        $shade = $dom->createElement('a:shade');
        $shade->setAttribute('val', $fillStyle['pattern']['bgColor']['shade']);
        $schemeClrFg->appendChild($shade);
      }
      if($fillStyle['pattern']['bgColor']['tint'] != "")  {
        $tint = $dom->createElement('a:tint');
        $tint->setAttribute('val', $fillStyle['pattern']['bgColor']['tint']);
        $schemeClrFg->appendChild($tint);
      }

      $bgClr->appendChild($schemeClrFg);
      $pattFill->appendChild($bgClr);
    }

	}

  if ($fillStyle['type'] == 'bgFill') {
		// 未开发
	}

  return $spPr;

}


function 渲染strokeStyle($dom, $strokeStyle, $spPr, $DirPath) {

  if($strokeStyle['lineWidth'] != "" || $strokeStyle['lineCap'] != "" || $strokeStyle['lineDash'] != "" || $strokeStyle['paint']['color']['color'] != "" || $strokeStyle['paint']['type'] != "")   {
    //print_R($strokeStyle);
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
    //exit;
    if(strval(intval($strokeStyle['lineWidth'] * 12700)) == 38100)  {
      //print_R($childrenItem);exit;
    }

    // 未实现 lineHeadDecoration lineTailDecoration

		if($strokeStyle['paint']['color']['color'] != '')  {
			$solidFill = $dom->createElement('a:solidFill');
			$a_ln->appendChild($solidFill);
      //print_R($strokeStyle['paint']['color']);exit;
			if($strokeStyle['paint']['color']['color'] != '')  {
        $srgbClr = $dom->createElement('a:srgbClr');
				$srgbClr->setAttribute('val', AiToPptx_NumberToColor($strokeStyle['paint']['color']['color']));
        if($strokeStyle['paint']['color']['alpha'] != '')  {
          $alpha = $dom->createElement('a:alpha');
          $alpha->setAttribute('val', $strokeStyle['paint']['color']['alpha']);
          $srgbClr->appendChild($alpha);
        }
        if($strokeStyle['paint']['color']['lumMod'] != '')  {
          $lumMod = $dom->createElement('a:lumMod');
          $lumMod->setAttribute('val', $strokeStyle['paint']['color']['lumMod']);
          $srgbClr->appendChild($lumMod);
        }
        if($strokeStyle['paint']['color']['lumOff'] != '')  {
          $lumOff = $dom->createElement('a:lumOff');
          $lumOff->setAttribute('val', $strokeStyle['paint']['color']['lumOff']);
          $srgbClr->appendChild($lumOff);
        }
        if($strokeStyle['paint']['color']['hueMod'] != "")  {
          $hueMod = $dom->createElement('a:hueMod');
          $hueMod->setAttribute('val', $strokeStyle['paint']['color']['hueMod']);
          $srgbClr->appendChild($hueMod);
        }
        if($strokeStyle['paint']['color']['hueOff'] != "")  {
          $hueOff = $dom->createElement('a:hueOff');
          $hueOff->setAttribute('val', $strokeStyle['paint']['color']['hueOff']);
          $srgbClr->appendChild($hueOff);
        }
        if($strokeStyle['paint']['color']['satMod'] != "")  {
          $satMod = $dom->createElement('a:satMod');
          $satMod->setAttribute('val', $strokeStyle['paint']['color']['satMod']);
          $srgbClr->appendChild($satMod);
        }
        if($strokeStyle['paint']['color']['satOff'] != "")  {
          $satOff = $dom->createElement('a:satOff');
          $satOff->setAttribute('val', $strokeStyle['paint']['color']['satOff']);
          $srgbClr->appendChild($satOff);
        }
        if($strokeStyle['paint']['color']['shade'] != "")  {
          $shade = $dom->createElement('a:shade');
          $shade->setAttribute('val', $strokeStyle['paint']['color']['shade']);
          $srgbClr->appendChild($shade);
        }
        if($strokeStyle['paint']['color']['tint'] != "")  {
          $tint = $dom->createElement('a:tint');
          $tint->setAttribute('val', $strokeStyle['paint']['color']['tint']);
          $srgbClr->appendChild($tint);
        }
        $solidFill->appendChild($srgbClr);
			}
      if($strokeStyle['paint']['color']['scheme']!="")   {
        $schemeClr = $dom->createElement('a:schemeClr');
        $schemeClr->setAttribute('val', $strokeStyle['paint']['color']['scheme']);
        $solidFill->appendChild($schemeClr);
      }
		}

		// 创建 <a:prstDash> 节点并设置属性
		if($strokeStyle['lineDash'] != "")  {
      if($strokeStyle['lineDash'] == "SYS_DOT") {
        $strokeStyle['lineDash'] = "sysDot";
      }
      else if($strokeStyle['lineDash'] == "SYS_DASH") {
        $strokeStyle['lineDash'] = "sysDash";
      }
      else {
        $strokeStyle['lineDash'] = strtolower($strokeStyle['lineDash']);
      }
			$a_prstDash = $dom->createElement('a:prstDash');
			$a_prstDash->setAttribute('val', $strokeStyle['lineDash']);
			$a_ln->appendChild($a_prstDash);
		}

    if($strokeStyle['lineHeadWidth'] != "" || $strokeStyle['lineHeadLength'] != "" || $strokeStyle['lineHeadDecoration'] != "")  {
			$headEnd = $dom->createElement('a:headEnd');
			if($strokeStyle['lineHeadWidth'] != "")       $headEnd->setAttribute('w', $strokeStyle['lineHeadWidth'] == "LARGE" ? "lg" : "sm");
			if($strokeStyle['lineHeadLength'] != "")      $headEnd->setAttribute('len', $strokeStyle['lineHeadLength'] == "LARGE" ? "lg" : "sm");
			if($strokeStyle['lineHeadDecoration'] != "")  $headEnd->setAttribute('type', $strokeStyle['lineHeadDecoration']);
			$a_ln->appendChild($headEnd);
		}

		if($strokeStyle['lineTailWidth'] != "" || $strokeStyle['lineTailLength'] != "" || $strokeStyle['lineTailDecoration'] != "")  {
			$tailEnd = $dom->createElement('a:tailEnd');
			if($strokeStyle['lineTailWidth'] != "")       $tailEnd->setAttribute('w', $strokeStyle['lineTailWidth'] == "LARGE" ? "lg" : "sm");
			if($strokeStyle['lineTailLength'] != "")      $tailEnd->setAttribute('len', $strokeStyle['lineTailLength'] == "LARGE" ? "lg" : "sm");
			if($strokeStyle['lineTailDecoration'] != "")  $tailEnd->setAttribute('type', $strokeStyle['lineTailDecoration']);
			$a_ln->appendChild($tailEnd);
		}

    if($strokeStyle['miterLimit'] != "" && $strokeStyle['lineJoin'] == "MITER")  {
			$miter = $dom->createElement('a:miter');
			$miter->setAttribute('lim', $strokeStyle['miterLimit'] * 100000);
			$a_ln->appendChild($miter);
		}

    if($strokeStyle['paint']['type'] == "noFill")  {
			$noFill = $dom->createElement('a:noFill');
		  $a_ln->appendChild($noFill);
		}

    // 类型：linear,circular,rectangular,shape
		if($strokeStyle['paint']['type'] == "gradient")  {
			//print_R($strokeStyle['paint']['gradient']);exit;
			// 创建根节点 <a:gradFill>
			$gradFill = $dom->createElement('a:gradFill');
      if($strokeStyle['paint']['gradient']['rotWithShape'] == "1") {
        $gradFill->setAttribute('rotWithShape', "true");
      }
      if($strokeStyle['paint']['gradient']['flip'] != "") {
        $gradFill->setAttribute('flip', $strokeStyle['paint']['gradient']['flip']);
      }

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
				$srgbClr->setAttribute('val', AiToPptx_NumberToColor($color['color']));
				$gs->appendChild($srgbClr);

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
        if($color['hueMod'] != "")  {
          $hueMod = $dom->createElement('a:hueMod');
          $hueMod->setAttribute('val', $color['hueMod']);
          $srgbClr->appendChild($hueMod);
        }
        if($color['hueOff'] != "")  {
          $hueOff = $dom->createElement('a:hueOff');
          $hueOff->setAttribute('val', $color['hueOff']);
          $srgbClr->appendChild($hueOff);
        }
        if($color['satMod'] != "")  {
          $satMod = $dom->createElement('a:satMod');
          $satMod->setAttribute('val', $color['satMod']);
          $srgbClr->appendChild($satMod);
        }
        if($color['satOff'] != "")  {
          $satOff = $dom->createElement('a:satOff');
          $satOff->setAttribute('val', $color['satOff']);
          $srgbClr->appendChild($satOff);
        }
        if($color['shade'] != "")  {
          $shade = $dom->createElement('a:shade');
          $shade->setAttribute('val', $color['shade']);
          $srgbClr->appendChild($shade);
        }
        if($color['tint'] != "")  {
          $tint = $dom->createElement('a:tint');
          $tint->setAttribute('val', $color['tint']);
          $srgbClr->appendChild($tint);
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

  return $spPr;

}
?>
