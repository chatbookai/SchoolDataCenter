<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('pptx.lib.inc.php');

$SLIDEPAGE = 1;

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
	//print_R($realType);
	if($realType == "Group") {
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
		$cNvPr->setAttribute('name', 'Group 4');
		$cNvPr->setAttribute('id', '4');

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

		$off = $dom->createElement('a:off');
		$off->setAttribute('x', intval($anchor[0] * 12700));
		$off->setAttribute('y', intval($anchor[1] * 12700));

		$ext = $dom->createElement('a:ext');
		$ext->setAttribute('cx', intval($anchor[2] * 12700));
		$ext->setAttribute('cy', intval($anchor[3] * 12700));

		$chOff = $dom->createElement('a:chOff');
		$chOff->setAttribute('x', intval($interiorAnchor[0] * 12700));
		$chOff->setAttribute('y', intval($interiorAnchor[1] * 12700));

		$chExt = $dom->createElement('a:chExt');
		$chExt->setAttribute('cx', intval($interiorAnchor[2] * 12700));
		$chExt->setAttribute('cy', intval($interiorAnchor[3] * 12700));

		// 将子节点添加到 <a:xfrm>
		$xfrm->appendChild($off);
		$xfrm->appendChild($ext);
		$xfrm->appendChild($chOff);
		$xfrm->appendChild($chExt);

		// 将 <a:xfrm> 添加到 <p:grpSpPr>
		$grpSpPr->appendChild($xfrm);

		// 将 <p:nvGrpSpPr> 和 <p:grpSpPr> 添加到 <p:grpSp>
		$grpSp->appendChild($nvGrpSpPr);
		$grpSp->appendChild($grpSpPr);

		$childrenList = $childrenItem['children'];
		foreach($childrenList as $children) {
			//print_R($children);
			$绘制元素RESULT 	= 绘制单个元素($children);
			$importedNode = $dom->importNode($绘制元素RESULT->documentElement, true);
			$grpSp->appendChild($importedNode);
		}

		// 将 <p:grpSp> 添加到 DOM 的根节点
		$dom->appendChild($grpSp);

		// 输出生成的 XML 结构
		$绘制元素RESULT = $dom->saveXML();
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
file_put_contents($SLIDE_PATH, $最后输出PPTX_SLIDE);
print $最后输出PPTX_SLIDE;


// 使用示例
$source			= './json/0001';  // 要压缩的文件或文件夹路径
$destination 	= './0001.pptx';  // ZIP 文件的输出路径
createZip($source, $destination);



?>
