<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

$SLIDEPAGE = 2;

$SLIDE_PATH = "./json/0001/ppt/slides/slide".$SLIDEPAGE.".xml";
$xmlString 	= file_get_contents($SLIDE_PATH);
$xmlString 	= str_replace(':', '____', $xmlString);
$xml 		= simplexml_load_string($xmlString);

$JsonContent      	= file_get_contents("./json/0001.json");
$Array            	= json_decode($JsonContent, true);
$childrenList 		= $Array['pages'][intval($SLIDEPAGE-1)]['children'];

$所有文本信息 = [];
$Sections = $xml->p____cSld->p____spTree->p____sp;
//print_R(sizeof($xml->p____cSld->p____spTree));exit;

$PPTX文本元素列表p____spTree = new SimpleXMLElement('
        <p____spTree>
            <p____nvGrpSpPr>
                <p____cNvPr id="1" name=""/>
                <p____cNvGrpSpPr/>
                <p____nvPr/>
            </p____nvGrpSpPr>
            <p____grpSpPr>
                <a____xfrm>
                    <a____off x="0" y="0"/>
                    <a____ext cx="0" cy="0"/>
                    <a____chOff x="0" y="0"/>
                    <a____chExt cx="0" cy="0"/>
                </a____xfrm>
            </p____grpSpPr>
		</p____spTree>
');
//print_R($childrenList);exit;

// 遍历 childrenList 并处理每个元素
$SharpCounter = 0;
foreach ($childrenList as $childrenItem) {
    $Type 			= $childrenItem['type'];
    $Point 			= $childrenItem['point'];
    $文本对像 		= $childrenItem['children'][0]['children'][0];
	$geometryInfo 	= $childrenItem['extInfo']['property']['geometry'];
	$realType 		= $childrenItem['extInfo']['property']['realType'];
	$shapeType 		= $childrenItem['extInfo']['property']['shapeType'];
	$fillStyle 		= $childrenItem['extInfo']['property']['fillStyle'];
	$strokeStyle 	= $childrenItem['extInfo']['property']['strokeStyle'];
	$prstTxWarp 	= $childrenItem['extInfo']['property']['prstTxWarp'];
	$flipVertical 	= $childrenItem['extInfo']['property']['flipVertical'];
	print_R($childrenItem);
	
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
	if($geometryInfo['name'] != "custom") {
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
	$off->setAttribute('x', intval($Point[0] * 12700));
	$off->setAttribute('y', intval($Point[1] * 12700));
	$xfrm->appendChild($off);

	$ext = $dom->createElement('a:ext');
	$ext->setAttribute('cx', intval($Point[2] * 12700));
	$ext->setAttribute('cy', intval($Point[3] * 12700));
	$xfrm->appendChild($ext);
	
	if($childrenItem['extInfo']['property']['shapeType'] != "")  {
		$prstGeom = $dom->createElement('a:prstGeom');
		if($childrenItem['extInfo']['property']['shapeType'] == "rect")  {
			$prstGeom->setAttribute('prst', 'rect');
		}
		$spPr->appendChild($prstGeom);

		$avLst = $dom->createElement('a:avLst');
		$prstGeom->appendChild($avLst);

		$noFill = $dom->createElement('a:noFill');
		$spPr->appendChild($noFill);
	}
	
	
	//绘制任意几何图形
	if ($Type == "text" && $geometryInfo['name'] == "custom") {
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
		$pathInfo = $geometryInfo['data']['paths'][0];
		
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
		
		$commands = preg_split('/ (?=[MLZ])/', $pathInfo['path']);
		// 遍历并生成路径指令
		foreach ($commands as $command) {
			$type = $command[0]; // 指令类型 (M, L, C, Z)
			$points = explode(' ', trim(substr($command, 1))); // 提取点数据
			if ($type === 'M') {
				// 创建 <a:moveTo> 节点
				$moveTo = $dom->createElement('a:moveTo');
				$pt = $dom->createElement('a:pt');
				$pt->setAttribute('x', $points[0]);
				$pt->setAttribute('y', $points[1]);
				$moveTo->appendChild($pt);
				$a_path->appendChild($moveTo);
			} 
			elseif ($type === 'L') {
				// 创建 <a:lnTo> 节点
				$lnTo = $dom->createElement('a:lnTo');
				$pt = $dom->createElement('a:pt');
				$pt->setAttribute('x', $points[0]);
				$pt->setAttribute('y', $points[1]);
				$lnTo->appendChild($pt);
				$a_path->appendChild($lnTo);
			} 
			elseif ($type === 'C') {
				// 创建 <a:cubicBezTo> 节点
				$cubicBezTo = $dom->createElement('a:cubicBezTo');
				for ($i = 0; $i < count($points); $i += 2) {
					$pt = $dom->createElement('a:pt');
					$pt->setAttribute('x', $points[$i]);
					$pt->setAttribute('y', $points[$i + 1]);
					$cubicBezTo->appendChild($pt);
				}
				$a_path->appendChild($cubicBezTo);
			} 
			elseif ($type === 'Z') {
				// 创建 <a:close> 节点
				$a_path->appendChild($dom->createElement('a:close'));
			}
		}
	}
	
	if ($fillStyle['type'] == 'color') {
		// 创建 <a:solidFill> 节点
		$a_solidFill = $dom->createElement('a:solidFill');
		$spPr->appendChild($a_solidFill);

		// 创建 <a:schemeClr> 节点并设置属性
		$a_schemeClr = $dom->createElement('a:schemeClr');
		$a_schemeClr->setAttribute('val', $fillStyle['color']['scheme']);

		// 创建 <a:alpha> 节点并设置属性
		if($fillStyle['color']['alpha'] != "")  {
			$a_alpha = $dom->createElement('a:alpha');
			$a_alpha->setAttribute('val', $fillStyle['color']['alpha']);
			$a_schemeClr->appendChild($a_alpha);
		}

		// 将 <a:schemeClr> 添加到 <a:solidFill>
		$a_solidFill->appendChild($a_schemeClr);
	}
	if ($fillStyle['type'] == 'noFill') {
		// 创建 <a:solidFill> 节点
		$a_noFill = $dom->createElement('a:noFill');
		$spPr->appendChild($a_noFill);
	}
	
	if($strokeStyle['lineCap'] != "")   {
		// 创建 <a:ln> 节点并设置属性
		$a_ln = $dom->createElement('a:ln');
		if($strokeStyle['lineCap'] == "ROUND")  {
			$a_ln->setAttribute('cap', 'rnd'); // 设置 cap 属性为 "rnd"
		}
		if($strokeStyle['lineCompound'] == "SINGLE")  {
			$a_ln->setAttribute('cmpd', 'sng'); // 设置 cmpd 属性为 "sng"
		}
		$spPr->appendChild($a_ln);
		
		// 创建 <a:prstDash> 节点并设置属性
		$a_prstDash = $dom->createElement('a:prstDash');
		if($strokeStyle['lineDash'] == "SOLID")  {
			$a_prstDash->setAttribute('val', 'solid'); // 设置 val 属性为 "solid"
		}

		// 将 <a:prstDash> 添加到 <a:ln>
		$a_ln->appendChild($a_prstDash);
	}
	
	// 5. 添加 <p:txBody> 及其子元素
	$txBody = $dom->createElement('p:txBody');
	$pSp->appendChild($txBody);

	$bodyPr = $dom->createElement('a:bodyPr');
	$bodyPr->setAttribute('rtlCol', 'false');
	if($realType == "TextBox")  {
		switch($childrenItem['extInfo']['property']['textAutofit']) {
			case 'NORMAL':
				$spAutoFit = $dom->createElement('a:spAutoFit');
				$bodyPr->appendChild($spAutoFit);
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
		$pPr->setAttribute('algn', 'l');
		$pPr->setAttribute('marL', '0');
		$p->appendChild($pPr);

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

		$srgbClr = $dom->createElement('a:srgbClr');
		$srgbClr->setAttribute('val', 数字转颜色($文本对像['extInfo']['property']['fontColor']['color']['color']));
		$solidFill->appendChild($srgbClr);

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

	// 7. 输出 XML 内容
	print $dom->saveXML();
	
	$domList = dom_import_simplexml($PPTX文本元素列表p____spTree)->ownerDocument;
	$domElement = $domList->importNode(dom_import_simplexml(simplexml_load_string($dom->saveXML())), true);
	$domList->documentElement->appendChild($domElement);
	
	
}

//echo $PPTX文本元素列表p____spTree->asXML();//exit;

//加入节点 p____cSld
$PPTX文本元素列表p____cSld = new SimpleXMLElement('
        <p____cSld>
			<p____bg>
				<p____bgPr>
					<a____solidFill>
						<a____srgbClr val="FFFFFF"/>
					</a____solidFill>
				</p____bgPr>
			</p____bg>
		</p____cSld>
');
$domList = dom_import_simplexml($PPTX文本元素列表p____cSld)->ownerDocument;
$domElement = $domList->importNode(dom_import_simplexml($PPTX文本元素列表p____spTree), true);
$domList->documentElement->appendChild($domElement);

$PPTX文本元素列表p____sld = new SimpleXMLElement('
        <p____sld xmlns____p="http____//schemas.openxmlformats.org/presentationml/2006/main" xmlns____a="http____//schemas.openxmlformats.org/drawingml/2006/main">
			<p____clrMapOvr>
				<a____masterClrMapping/>
			</p____clrMapOvr>
		</p____sld>
');
$domList = dom_import_simplexml($PPTX文本元素列表p____sld)->ownerDocument;
$domElement = $domList->importNode(dom_import_simplexml($PPTX文本元素列表p____cSld), true);
$domList->documentElement->appendChild($domElement);

$最后输出PPTX_SLIDE = $PPTX文本元素列表p____sld->asXML();
$最后输出PPTX_SLIDE = str_replace('____', ':', $最后输出PPTX_SLIDE);
$最后输出PPTX_SLIDE = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $最后输出PPTX_SLIDE);
file_put_contents($SLIDE_PATH, $最后输出PPTX_SLIDE);
print $最后输出PPTX_SLIDE;


function createPathNode($pathInfo) {
	
    // 拆分字符串为指令和坐标
    $commands = preg_split('/ (?=[MLZ])/', $pathInfo['path']);

    // 创建XML根元素：<a:path>
    $path = new SimpleXMLElement('<a____path />');
    $path->addAttribute('w', $pathInfo['w']);
    $path->addAttribute('h', $pathInfo['h']);
    $path->addAttribute('stroke', $pathInfo['stroked'] == 1 ? "true" : "false");
    $path->addAttribute('fill', strtolower($pathInfo['fill']));
    $path->addAttribute('extrusionOk', $pathInfo['extrusionOk'] == 1 ? "true" : "false");

    // 解析路径指令并生成相应的XML子元素
    foreach ($commands as $command) {
        $parts = explode(' ', $command);
        $cmd = array_shift($parts); // 获取指令 (M, L, Z)
        switch ($cmd) {
            case 'M': // moveTo
                $moveTo = $path->addChild('a____moveTo');
                $pt = $moveTo->addChild('a____pt');
                $pt->addAttribute('x', $parts[0]);
                $pt->addAttribute('y', $parts[1]);
                break;
            case 'L': // lineTo
                $lnTo = $path->addChild('a____lnTo');
                $pt = $lnTo->addChild('a____pt');
                $pt->addAttribute('x', $parts[0]);
                $pt->addAttribute('y', $parts[1]);
                break;
            case 'Z': // close path
                $path->addChild('a____close');
                break;
        }
    }

    // 格式化并返回XML字符串
    $dom = dom_import_simplexml($path)->ownerDocument;
    $dom->formatOutput = true;
    return $dom->saveXML();
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


// 使用示例
$source			= './json/0001';  // 要压缩的文件或文件夹路径
$destination 	= './0001.pptx';  // ZIP 文件的输出路径
createZip($source, $destination);


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
