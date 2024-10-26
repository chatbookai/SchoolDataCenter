<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('pptx.lib.inc.php');

$SLIDEPAGE = 22;

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
    
	$Type 				= $childrenItem['type'];
	$realType 			= $childrenItem['extInfo']['property']['realType'];
	$rotation 			= $childrenItem['extInfo']['property']['rotation'];
	$groupFillStyle 	= $childrenItem['extInfo']['property']['groupFillStyle'];
	
	if($realType == "Group") {
		//print_R($childrenItem);
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
			print_R($children);
			$绘制元素RESULT 	= 绘制单个元素($children);
			$importedNode = $dom->importNode($绘制元素RESULT->documentElement, true);
			$grpSp->appendChild($importedNode);
		}

		// 将 <p:grpSp> 添加到 DOM 的根节点
		$dom->appendChild($grpSp);

		// 输出生成的 XML 结构
		$绘制元素RESULT = $dom->saveXML();
		
		if(strval(intval($anchor[0] * 12700)) == '9002135')  {
			print_R($childrenItem);
			print_R($绘制元素RESULT->saveXML());
		}
		print $绘制元素RESULT;
	}
	else {
		$绘制元素RESULT 	= 绘制单个元素($childrenItem)->saveXML();
	}
	
	$domList = dom_import_simplexml($PPTX文本元素列表p____spTree)->ownerDocument;
	$domElement = $domList->importNode(dom_import_simplexml(simplexml_load_string($绘制元素RESULT)), true);
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
$最后输出PPTX_SLIDE1 = '';
file_put_contents($SLIDE_PATH, $最后输出PPTX_SLIDE);
print $最后输出PPTX_SLIDE;


// 使用示例
$source			= './json/0001';  // 要压缩的文件或文件夹路径
$destination 	= './0001.pptx';  // ZIP 文件的输出路径
createZip($source, $destination);



?>
