<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

$SLIDE_PATH = "./json/0001/ppt/slides/slide2.xml";
$xmlString 	= file_get_contents($SLIDE_PATH);
$xmlString 	= str_replace(':', '____', $xmlString);
$xml 		= simplexml_load_string($xmlString);

$JsonContent      	= file_get_contents("./json/0001.json");
$Array            	= json_decode($JsonContent, true);
$childrenList 		= $Array['pages'][1]['children'];

$所有文本信息 = [];
$Sections = $xml->p____cSld->p____spTree->p____sp;
//print_R(sizeof($xml->p____cSld->p____spTree));exit;

$PPTX元素_文本 = '<p:sp>
					<p:nvSpPr>
						<p:cNvPr name="TextBox 6" id="6"/>
						<p:cNvSpPr txBox="true"/>
						<p:nvPr/>
					</p:nvSpPr>
					<p:spPr>
						<a:xfrm>
							<a:off x="会被替换掉的数值" y="会被替换掉的数值"/>
							<a:ext cx="会被替换掉的数值" cy="会被替换掉的数值"/>
						</a:xfrm>
						<a:prstGeom prst="rect">
							<a:avLst/>
						</a:prstGeom>
						<a:noFill/>
					</p:spPr>
					<p:txBody>
						<a:bodyPr anchor="t" rtlCol="false" vert="horz" wrap="square" tIns="45720" lIns="91440" bIns="45720" rIns="91440">
							<a:spAutoFit/>
						</a:bodyPr>
						<a:lstStyle/>
						<a:p>
							<a:pPr algn="l" marL="0">
								<a:defRPr/>
							</a:pPr>
							<a:r>
								<a:rPr lang="zh-CN" b="false" i="false" sz="1800" baseline="0" u="none" altLang="en-US">
									<a:solidFill>
										<a:srgbClr val="会被替换掉的数值"/>
									</a:solidFill>
									<a:latin typeface="会被替换掉的数值"/>
									<a:ea typeface="会被替换掉的数值"/>
								</a:rPr>
								<a:t>会被替换掉的数值</a:t>
							</a:r>
							<a:endParaRPr lang="en-US" sz="1100"/>
						</a:p>
					</p:txBody>
				</p:sp>
				';
$PPTX元素_色块 = '<p:sp>
					<p:nvSpPr>
						<p:cNvPr name="Freeform 2" id="2"/>
						<p:cNvSpPr/>
						<p:nvPr/>
					</p:nvSpPr>
					<p:spPr>
						<a:xfrm flipV="true">
							<a:off x="会被替换掉的数值" y="会被替换掉的数值"/>
							<a:ext cx="会被替换掉的数值" cy="会被替换掉的数值"/>
						</a:xfrm>
						<a:custGeom>
							<a:avLst/>
							<a:gdLst/>
							<a:ahLst/>
							<a:cxnLst/>
							<a:rect r="r" b="b" t="t" l="l"/>
							<a:pathLst>
								<a:path w="2248349" h="2675877" stroke="true" fill="norm" extrusionOk="true">
									<a:moveTo>
										<a:pt x="0" y="2675877"/>
									</a:moveTo>
									<a:lnTo>
										<a:pt x="1575045" y="2675877"/>
									</a:lnTo>
									<a:lnTo>
										<a:pt x="2248349" y="1337939"/>
									</a:lnTo>
									<a:lnTo>
										<a:pt x="1575045" y="0"/>
									</a:lnTo>
									<a:lnTo>
										<a:pt x="0" y="0"/>
									</a:lnTo>
									<a:close/>
								</a:path>
							</a:pathLst>
						</a:custGeom>
						<a:solidFill>
							<a:schemeClr val="accent4">
								<a:alpha val="10000"/>
							</a:schemeClr>
						</a:solidFill>
						<a:ln cap="rnd" cmpd="sng">
							<a:prstDash val="solid"/>
						</a:ln>
					</p:spPr>
					<p:txBody>
						<a:bodyPr vert="horz" rot="0" anchor="ctr" wrap="square" tIns="45720" lIns="91440" bIns="45720" rIns="91440">
							<a:prstTxWarp prst="textNoShape">
								<a:avLst/>
							</a:prstTxWarp>
							<a:noAutofit/>
						</a:bodyPr>
						<a:p>
							<a:pPr algn="ctr" marL="0"/>
						</a:p>
					</p:txBody>
				</p:sp>
				';
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
foreach ($childrenList as $childrenItem) {
    $Type = $childrenItem['type'];
    $Point = $childrenItem['point'];
    $文本对像 = $childrenItem['children'][0]['children'][0];
	//print_R($childrenItem);
    if ($Type == "text" && $文本对像['text'] != "") {
        $PPTX元素_文本 = str_replace(':', '____', $PPTX元素_文本);
		$PPTX文本元素 = simplexml_load_string($PPTX元素_文本);

		// 设置位置信息
		$PPTX文本元素->p____spPr->a____xfrm->a____off['x'] = intval($Point[0] * 12700);
		$PPTX文本元素->p____spPr->a____xfrm->a____off['y'] = intval($Point[1] * 12700);
		$PPTX文本元素->p____spPr->a____xfrm->a____ext['cx'] = intval($Point[2] * 12700);
		$PPTX文本元素->p____spPr->a____xfrm->a____ext['cy'] = intval($Point[3] * 12700);

		// 设置文本和样式属性
		$PPTX文本元素->p____txBody->a____p->a____r->a____t = $文本对像['text'];
		$PPTX文本元素->p____txBody->a____p->a____r->a____rPr['lang'] = $文本对像['extInfo']['property']['lang'];
		$PPTX文本元素->p____txBody->a____p->a____r->a____rPr['b'] = $文本对像['extInfo']['property']['bold'] == 1 ? "true" : "false";
		$PPTX文本元素->p____txBody->a____p->a____r->a____rPr['i'] = $文本对像['extInfo']['property']['italic'] == 1 ? "true" : "false";
		$PPTX文本元素->p____txBody->a____p->a____r->a____rPr['sz'] = ($文本对像['extInfo']['property']['fontSize'] * 100);
		$PPTX文本元素->p____txBody->a____p->a____r->a____rPr->a____latin['typeface'] = $文本对像['extInfo']['property']['fontFamily'];
		$PPTX文本元素->p____txBody->a____p->a____r->a____rPr->a____solidFill->a____srgbClr['val'] = $文本对像['extInfo']['property']['fontColor']['color']['scheme'];

		// 将 $PPTX文本元素 合并到 $PPTX文本元素列表p____spTree
		$domList = dom_import_simplexml($PPTX文本元素列表p____spTree)->ownerDocument;
		$domElement = $domList->importNode(dom_import_simplexml($PPTX文本元素), true);
		$domList->documentElement->appendChild($domElement);
    }
	
	//绘制任意几何图形
	$geometryInfo = $childrenItem['extInfo']['property']['geometry'];
	$geometry_name = $geometryInfo['name'];
	if ($Type == "text" && $geometry_name == "custom") {
		//print_R($geometryInfo);
        $PPTX元素_色块 = str_replace(':', '____', $PPTX元素_色块);
		$PPTX文本元素 = simplexml_load_string($PPTX元素_色块);

		// 设置位置信息
		$PPTX文本元素->p____spPr->a____xfrm->a____off['x'] = intval($Point[0] * 12700);
		$PPTX文本元素->p____spPr->a____xfrm->a____off['y'] = intval($Point[1] * 12700);
		$PPTX文本元素->p____spPr->a____xfrm->a____ext['cx'] = intval($Point[2] * 12700);
		$PPTX文本元素->p____spPr->a____xfrm->a____ext['cy'] = intval($Point[3] * 12700);
		
		
		
		//删除节点 a____path
		$dom 	= dom_import_simplexml($PPTX文本元素)->ownerDocument;
		$xpath 	= new DOMXPath($dom);
		$nodes 	= $xpath->query('//p____spPr/a____custGeom/a____pathLst/a____path');
		foreach ($nodes as $node) {
			$node->parentNode->removeChild($node);  
		}
		//绘制任意几何图形
		$createPathNodeResult = createPathNode($geometryInfo['data']['paths'][0]);
		//添加节点 a____path
		$pathLstNodes 	= $xpath->query('//p____spPr/a____custGeom/a____pathLst');
		if ($pathLstNodes->length > 0) {
			$pathLstNode = $pathLstNodes->item(0);  // 只取第一个匹配的节点
			$pathLstNode->appendChild($dom->importNode(dom_import_simplexml(simplexml_load_string($createPathNodeResult)), true));
		}
		
		//$PPTX文本元素->p____spPr->a____custGeom->a____pathLst->a____path->a____moveTo->a____pt['x'] = '';
		//$PPTX文本元素->p____spPr->a____custGeom->a____pathLst->a____path->a____moveTo->a____pt['y'] = '';
		
		//print_R($geometryInfo['data']['paths']);exit;

		// 设置文本和样式属性
		//$PPTX文本元素->p____txBody->a____p->a____r->a____t = $文本对像['text'];
		//$PPTX文本元素->p____txBody->a____p->a____r->a____rPr['lang'] = $文本对像['extInfo']['property']['lang'];
		//$PPTX文本元素->p____txBody->a____p->a____r->a____rPr['b'] = $文本对像['extInfo']['property']['bold'];
		//$PPTX文本元素->p____txBody->a____p->a____r->a____rPr['i'] = $文本对像['extInfo']['property']['i'];
		//$PPTX文本元素->p____txBody->a____p->a____r->a____rPr['sz'] = ($文本对像['extInfo']['property']['fontSize'] * 100);
		//$PPTX文本元素->p____txBody->a____p->a____r->a____rPr->a____latin['typeface'] = $文本对像['extInfo']['property']['fontFamily'];
		//$PPTX文本元素->p____txBody->a____p->a____r->a____rPr->a____solidFill->a____srgbClr['val'] = $文本对像['extInfo']['property']['fontColor']['color']['scheme'];
		
		//print_R($PPTX文本元素->asXML());
		
		// 将 $PPTX文本元素 合并到 $PPTX文本元素列表p____spTree
		$domList = dom_import_simplexml($PPTX文本元素列表p____spTree)->ownerDocument;
		$domElement = $domList->importNode(dom_import_simplexml($PPTX文本元素), true);
		$domList->documentElement->appendChild($domElement);
    }
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



?>
