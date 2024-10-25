<?php


function 绘制单个元素($childrenItem)  {
	
	$Type 			= $childrenItem['type'];
    $Point 			= $childrenItem['point'];
    $文本对像 		= $childrenItem['children'][0]['children'][0];
    $文本属性 		= $childrenItem['children'][0]['extInfo'];
	$anchor 		= $childrenItem['extInfo']['property']['anchor'];
	$realType 		= $childrenItem['extInfo']['property']['realType'];
	$realType 		= $childrenItem['extInfo']['property']['realType'];
	$shapeType 		= $childrenItem['extInfo']['property']['shapeType'];
	$fillStyle 		= $childrenItem['extInfo']['property']['fillStyle'];
	$strokeStyle 	= $childrenItem['extInfo']['property']['strokeStyle'];
	$geometry 		= $childrenItem['extInfo']['property']['geometry'];
	$prstTxWarp 	= $childrenItem['extInfo']['property']['prstTxWarp'];
	$flipVertical 	= $childrenItem['extInfo']['property']['flipVertical'];
	//print_R($childrenItem);

	// 1. 创建 DOMDocument 对象
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true; // 格式化输出

	// 2. 创建根元素 <p:sp> 并附加到 DOM
	$pSp = $dom->createElement('p:sp');
	$dom->appendChild($pSp);

	// 3. 添加 <p:nvSpPr> 子元素及其子元素
	$nvSpPr = $dom->createElement('p:nvSpPr');
	$pSp->appendChild($nvSpPr);

	$cNvPr = $dom->createElement('p:cNvPr');
	$cNvPr->setAttribute('name', 'TextBox '. $SharpCounter);
	$cNvPr->setAttribute('id', $SharpCounter++);
	$nvSpPr->appendChild($cNvPr);

	$cNvSpPr = $dom->createElement('p:cNvSpPr');
	if($realType == "TextBox") {
		$cNvSpPr->setAttribute('txBox', 'true');
	}
	$nvSpPr->appendChild($cNvSpPr);

	$nvPr = $dom->createElement('p:nvPr');
	$nvSpPr->appendChild($nvPr);

	// 4. 添加 <p:spPr> 及其子元素
	$spPr = $dom->createElement('p:spPr');
	$pSp->appendChild($spPr);

	$xfrm = $dom->createElement('a:xfrm');
	if($flipVertical == 1) {
		$xfrm->setAttribute('flipV', 'true');
	}
	$spPr->appendChild($xfrm);

	$off = $dom->createElement('a:off');
	$off->setAttribute('x', intval($anchor[0] * 12700));
	$off->setAttribute('y', intval($anchor[1] * 12700));
	$xfrm->appendChild($off);

	$ext = $dom->createElement('a:ext');
	$ext->setAttribute('cx', intval($anchor[2] * 12700));
	$ext->setAttribute('cy', intval($anchor[3] * 12700));
	$xfrm->appendChild($ext);
	
	if($childrenItem['extInfo']['property']['shapeType'] != "")  {
		$prstGeom = $dom->createElement('a:prstGeom');
		$prstGeom->setAttribute('prst', $childrenItem['extInfo']['property']['shapeType']);
		$spPr->appendChild($prstGeom);

		$avLst = $dom->createElement('a:avLst');
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
			$type = $command[0]; // 指令类型 (M, L, C, Z)
			$points = array_filter(explode(' ', trim(substr($command, 1)))); // 提取点数据

			if ($type === 'M') {
				// 创建 <a:moveTo> 节点
				$moveTo = $dom->createElement('a:moveTo');
				$pt = $dom->createElement('a:pt');
				$pt->setAttribute('x', $points[0]);
				$pt->setAttribute('y', $points[1]);
				$moveTo->appendChild($pt);
				$a_path->appendChild($moveTo);

			} elseif ($type === 'L') {
				// 创建 <a:lnTo> 节点
				$lnTo = $dom->createElement('a:lnTo');
				$pt = $dom->createElement('a:pt');
				$pt->setAttribute('x', $points[0]);
				$pt->setAttribute('y', $points[1]);
				$lnTo->appendChild($pt);
				$a_path->appendChild($lnTo);

			} elseif ($type === 'C') {
				// 创建 <a:cubicBezTo> 节点
				$cubicBezTo = $dom->createElement('a:cubicBezTo');
				for ($i = 0; $i < count($points); $i += 2) {
					$pt = $dom->createElement('a:pt');
					$pt->setAttribute('x', $points[$i]);
					$pt->setAttribute('y', $points[$i + 1]);
					$cubicBezTo->appendChild($pt);
				}
				$a_path->appendChild($cubicBezTo);

			} elseif ($type === 'Z') {
				// 创建 <a:close> 节点
				$a_path->appendChild($dom->createElement('a:close'));
			}
		}
		//print $dom->asXML();exit;
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
		}
		
		if($fillStyle['color']['color'] != '')  {
			$srgbClr = $dom->createElement('a:srgbClr');
			if($fillStyle['color']['color'] == '-1')  {
				$srgbClr->setAttribute('val', 'FFFFFF');
			}
			else {
				$srgbClr->setAttribute('val', 数字转颜色($fillStyle['color']['color']));
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
	if ($fillStyle['type'] == 'noFill') {
		// 创建 <a:solidFill> 节点
		$a_noFill = $dom->createElement('a:noFill');
		$spPr->appendChild($a_noFill);
	}
	
	//print_R($strokeStyle['paint']['color']['color']);
	if($strokeStyle['lineCap'] != "")   {
		// 创建 <a:ln> 节点并设置属性
		$a_ln = $dom->createElement('a:ln');
		if($strokeStyle['lineWidth'] != "")  {
			$a_ln->setAttribute('w', intval($strokeStyle['lineWidth'] * 12700));
		}
		if($strokeStyle['lineCap'] == "ROUND")  {
			$a_ln->setAttribute('cap', 'rnd'); // 设置 cap 属性为 "rnd"
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
			$solidFill->appendChild($srgbClr);
		}
		
		// 创建 <a:prstDash> 节点并设置属性
		$a_prstDash = $dom->createElement('a:prstDash');
		if($strokeStyle['lineDash'] != "")  {
			$a_prstDash->setAttribute('val', strtolower($strokeStyle['lineDash']));
		}

		// 将 <a:prstDash> 添加到 <a:ln>
		$a_ln->appendChild($a_prstDash);
	}
	
	// 5. 添加 <p:txBody> 及其子元素
	$txBody = $dom->createElement('p:txBody');
	$pSp->appendChild($txBody);

	$bodyPr = $dom->createElement('a:bodyPr');
	$bodyPr->setAttribute('rtlCol', 'false');
	if($realType == "TextBox" || $realType == "Auto")  {
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
	}
	if($childrenItem['extInfo']['property']['textWordWrap'] == 1) {
		$bodyPr->setAttribute('wrap', 'square');
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

	// 6. 文本框, 创建段落 <a:p> 及其内容
    if ($Type == "text" && $文本对像['text'] != "") {
		$p = $dom->createElement('a:p');
		$txBody->appendChild($p);

		$pPr = $dom->createElement('a:pPr');
		switch($文本属性['property']['textAlign']) {
			case 'CENTER':
				$pPr->setAttribute('algn', 'ctr');
				break;
		}
		if($文本属性['property']['leftMargin'] != '') {
			$pPr->setAttribute('marL', $文本属性['property']['leftMargin']);
		}
		$p->appendChild($pPr);
		
		
		if($文本属性['property']['lineSpacing'] != '') {
			$lnSpc = $dom->createElement('a:lnSpc');
			$spcPct = $dom->createElement('a:spcPct');
			$spcPct->setAttribute('val', intval($文本属性['property']['lineSpacing'] * 1000));
			$lnSpc->appendChild($spcPct);
			$pPr->appendChild($lnSpc);
		}

		$defRPr = $dom->createElement('a:defRPr');
		$pPr->appendChild($defRPr);

		$r = $dom->createElement('a:r');
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
		
		if($文本对像['extInfo']['property']['fontColor']['color']['color'] !="" )  {
			$srgbClr = $dom->createElement('a:srgbClr');
			$srgbClr->setAttribute('val', 数字转颜色($文本对像['extInfo']['property']['fontColor']['color']['color']));
			$solidFill->appendChild($srgbClr);
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

		$endParaRPr = $dom->createElement('a:endParaRPr');
		$endParaRPr->setAttribute('lang', 'en-US');
		$endParaRPr->setAttribute('sz', '1100');
		$p->appendChild($endParaRPr);
	}
	
	return $dom;	
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