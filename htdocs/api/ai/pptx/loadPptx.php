<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('pptx.lib.inc.php');

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
    
	$绘制单个元素RESULT = 绘制单个元素($childrenItem);

	
	$domList = dom_import_simplexml($PPTX文本元素列表p____spTree)->ownerDocument;
	$domElement = $domList->importNode(dom_import_simplexml(simplexml_load_string($绘制单个元素RESULT)), true);
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
