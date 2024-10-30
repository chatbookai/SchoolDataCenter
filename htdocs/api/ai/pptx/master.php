<?php
header("Content-Type: application/json; charset=utf-8");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

require_once('pptx.lib.inc.php');

$SLIDE_PATH = "./json/0001/ppt/slideMasters/slideMaster1.xml";
$xmlString 	= file_get_contents($SLIDE_PATH);
$xmlString 	= str_replace(':', '____', $xmlString);
$xml 		= simplexml_load_string($xmlString);

$JsonContent      	= file_get_contents("./json/0001.json");
$JsonData          	= json_decode($JsonContent, true);

$slideMasters = $JsonData['slideMasters'];
$slideLayouts = $JsonData['slideMasters'][0]['slideLayouts'];

//print_R($slideLayouts[5]);

$MakeSlideLayoutData = MakeSlideLayout($slideLayouts[0], "./json/0001/ppt/slideLayouts/slideLayout1.xml");
print $MakeSlideLayoutData;

$MakeSlideLayoutData = MakeSlideLayout($slideLayouts[1], "./json/0001/ppt/slideLayouts/slideLayout2.xml");
print $MakeSlideLayoutData;

$MakeSlideLayoutData = MakeSlideLayout($slideLayouts[2], "./json/0001/ppt/slideLayouts/slideLayout3.xml");
print $MakeSlideLayoutData;

$MakeSlideLayoutData = MakeSlideLayout($slideLayouts[3], "./json/0001/ppt/slideLayouts/slideLayout4.xml");
print $MakeSlideLayoutData;

$MakeSlideLayoutData = MakeSlideLayout($slideLayouts[4], "./json/0001/ppt/slideLayouts/slideLayout5.xml");
print $MakeSlideLayoutData;

$MakeSlideLayoutData = MakeSlideLayout($slideLayouts[5], "./json/0001/ppt/slideLayouts/slideLayout6.xml");
print $MakeSlideLayoutData;

$MakeMasterXmlData = MakeMasterXml($JsonData['slideMasters'], "./json/0001/ppt/slideMasters/slideMaster1.xml");

// 使用示例
$source			= './json/0001';  // 要压缩的文件或文件夹路径
$destination 	= './0001.pptx';  // ZIP 文件的输出路径
createZip($source, $destination);

function MakeSlideLayout($Layout, $FilePath) {
	
	// 创建DOM对象并设置XML版本和编码
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;

	// 创建根元素 <p:sldLayout>
	$sldLayout = $dom->createElementNS(
		'http://schemas.openxmlformats.org/presentationml/2006/main',
		'p:sldLayout'
	);
	$sldLayout->setAttribute('type', 'blank');
	$sldLayout->setAttribute('preserve', '1');

	// 注册命名空间前缀
	$sldLayout->setAttributeNS(
		'http://www.w3.org/2000/xmlns/', 'xmlns:a',
		'http://schemas.openxmlformats.org/drawingml/2006/main'
	);
	$sldLayout->setAttributeNS(
		'http://www.w3.org/2000/xmlns/', 'xmlns:r',
		'http://schemas.openxmlformats.org/officeDocument/2006/relationships'
	);
	$sldLayout->setAttributeNS(
		'http://www.w3.org/2000/xmlns/', 'xmlns:p',
		'http://schemas.openxmlformats.org/presentationml/2006/main'
	);

	// 创建子元素 <p:cSld> 并附加到根元素
	$cSld = $dom->createElement('p:cSld');
	$cSld->setAttribute('name', $Layout['name']);

	// 创建 <p:bg> 元素及其子元素
	$bg = $dom->createElement('p:bg');
	$bgPr = $dom->createElement('p:bgPr');
	$solidFill = $dom->createElement('a:solidFill');
	$srgbClr = $dom->createElement('a:srgbClr');
	$srgbClr->setAttribute('val', 'FFFFFF');

	// 组装 <p:bg> 树
	$solidFill->appendChild($srgbClr);
	$bgPr->appendChild($solidFill);
	$bg->appendChild($bgPr);
	$cSld->appendChild($bg);

	// 创建 <p:spTree> 结构
	$spTree = $dom->createElement('p:spTree');
	$nvGrpSpPr = $dom->createElement('p:nvGrpSpPr');
	$cNvPr = $dom->createElement('p:cNvPr');
	$cNvPr->setAttribute('id', '1');
	$cNvPr->setAttribute('name', '');
	$cNvGrpSpPr = $dom->createElement('p:cNvGrpSpPr');
	$nvPr = $dom->createElement('p:nvPr');

	// 组装 <p:spTree> 的非可视属性部分
	$nvGrpSpPr->appendChild($cNvPr);
	$nvGrpSpPr->appendChild($cNvGrpSpPr);
	$nvGrpSpPr->appendChild($nvPr);
	$spTree->appendChild($nvGrpSpPr);

	// 创建 <p:grpSpPr> 及其变换属性
	$grpSpPr = $dom->createElement('p:grpSpPr');
	$xfrm = $dom->createElement('a:xfrm');
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

	// 组装 <p:grpSpPr>
	$xfrm->appendChild($off);
	$xfrm->appendChild($ext);
	$xfrm->appendChild($chOff);
	$xfrm->appendChild($chExt);
	$grpSpPr->appendChild($xfrm);	
	$spTree->appendChild($grpSpPr);
	
	// 组装 <p:sp>
	foreach($Layout['children'] as $ChildrenItem) 		{
		$绘制单个元素对像RESULT 	= 绘制单个元素对像($ChildrenItem);
		//print $绘制元素RESULT;//exit;
		$importedpSp = $dom->importNode($绘制单个元素对像RESULT, true); // 深度导入整个节点及其子节点
		$spTree->appendChild($importedpSp);
	}
	
	// Add spTree
	$cSld->appendChild($spTree);

	// 创建 <p:clrMapOvr> 及其子元素
	$clrMapOvr = $dom->createElement('p:clrMapOvr');
	$masterClrMapping = $dom->createElement('a:masterClrMapping');
	$clrMapOvr->appendChild($masterClrMapping);

	// 将所有子元素附加到根元素
	$sldLayout->appendChild($cSld);
	$sldLayout->appendChild($clrMapOvr);

	// 将根元素附加到DOM对象
	$dom->appendChild($sldLayout);

	//写入文件
	$dom->save($FilePath);
	
	return $dom->saveXML();
	
}


function MakeMasterXml($slideMasters, $FilePath)  {
	
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
	$bgPr = $dom->createElement('p:bgPr');
	$bg->appendChild($bgPr);

	// 添加 <a:solidFill> 元素
	$solidFill = $dom->createElement('a:solidFill');
	$bgPr->appendChild($solidFill);

	// 添加 <a:srgbClr> 元素
	$srgbClr = $dom->createElement('a:srgbClr');
	//$srgbClr->setAttribute('val', 'FFFFFF');
	$solidFill->appendChild($srgbClr);

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
	
	$slideChildrenList = $slideMasters[0]['children'];
	foreach($slideChildrenList as $ChildrenItem) 		{
		$绘制单个元素对像RESULT 	= 绘制单个元素对像($ChildrenItem);
		//print $绘制元素RESULT;//exit;
		$importedpSp = $dom->importNode($绘制单个元素对像RESULT, true); // 深度导入整个节点及其子节点
		$spTree->appendChild($importedpSp);
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
	$layouts = [
		['id' => '2147483655', 'r:id' => 'rId1'],
		['id' => '2147483656', 'r:id' => 'rId3'],
		['id' => '2147483657', 'r:id' => 'rId4'],
		['id' => '2147483658', 'r:id' => 'rId5'],
		['id' => '2147483659', 'r:id' => 'rId6'],
		['id' => '2147483660', 'r:id' => 'rId7']
	];
	foreach ($layouts as $layout) {
		$sldLayoutId = $dom->createElement('p:sldLayoutId');
		$sldLayoutId->setAttribute('id', $layout['id']);
		$sldLayoutId->setAttribute('r:id', $layout['r:id']);
		$sldLayoutIdLst->appendChild($sldLayoutId);
	}
	$sldMaster->appendChild($sldLayoutIdLst);
	
	//写入文件
	$dom->save($FilePath);

	return $dom->saveXML();

	// 将XML输出到浏览器或保存到文件
	//header('Content-Type: application/xml');
	//echo $dom->saveXML();

	// 或保存为文件
	// $dom->save('slide_master.xml');
	
}




?>
