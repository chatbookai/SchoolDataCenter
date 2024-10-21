<?php
header("Content-Type: application/json");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

$JsonContent      = file_get_contents("./json/0001.json");
$Array            = json_decode($JsonContent, true);


$所有页面 = (array)$Array['pages'];

$首页JSON = $所有页面[0];
//print_R($首页JSON);

渲染单个页面($首页JSON);

function 渲染单个页面($首页JSON) {
	print '<?xml version="1.0" encoding="UTF-8"?>
<p:sld xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">
	<p:cSld>
		<p:bg>
		  <p:bgPr>
			<a:solidFill>
			  <a:srgbClr val="FFFFFF"/>
			</a:solidFill>
		  </p:bgPr>
		</p:bg>
		<p:spTree>
		  <p:nvGrpSpPr>
			<p:cNvPr id="1" name=""/>
			<p:cNvGrpSpPr/>
			<p:nvPr/>
		  </p:nvGrpSpPr>
		  <p:grpSpPr>
			<a:xfrm>
			  <a:off x="0" y="0"/>
			  <a:ext cx="0" cy="0"/>
			  <a:chOff x="0" y="0"/>
			  <a:chExt cx="0" cy="0"/>
			</a:xfrm>
		  </p:grpSpPr>
		
		';
		
		$childrenList = $首页JSON['children'];
		foreach($childrenList as $childrenItem)  {
			$Type 	= $childrenItem['type'];
			$Point 	= $childrenItem['point'];
			$childrenSub 	= $childrenItem['children'];
			$文本对像 		= $childrenItem['children'][0]['children'][0];
			$interiorAnchor = $childrenItem['extInfo']['property']['interiorAnchor'];
			switch($Type) {
				case 'container':
					print_R($childrenItem);//exit;
					print '
					<p:grpSp>
						<p:nvGrpSpPr>
						  <p:cNvPr name="Group 4" id="4"/>
						  <p:cNvGrpSpPr/>
						  <p:nvPr/>
						</p:nvGrpSpPr>
						<p:grpSpPr>
						  <a:xfrm>
							<a:off x="'.intval($childrenItem['point'][0] * 12700).'" y="'.intval($childrenItem['point'][1] * 12700).'"/>
							<a:ext cx="'.intval($childrenItem['point'][2] * 12700).'" cy="'.intval($childrenItem['point'][3] * 12700).'"/>
							<a:chOff x="'.intval($interiorAnchor[0] * 12700).'" y="'.intval($interiorAnchor[1] * 12700).'"/>
							<a:chExt cx="'.intval($interiorAnchor[2] * 12700).'" cy="'.intval($interiorAnchor[3] * 12700).'"/>
						  </a:xfrm>
						</p:grpSpPr>
						<p:sp>
						  <p:nvSpPr>
							<p:cNvPr name="AutoShape 5" id="5"/>
							<p:cNvSpPr/>
							<p:nvPr/>
						  </p:nvSpPr>
						  <p:spPr>
							<a:xfrm flipV="true">
							  <a:off x="'.intval($interiorAnchor[0] * 12700).'" y="'.intval($interiorAnchor[1] * 12700).'"/>
							  <a:ext cx="'.intval($interiorAnchor[2] * 12700).'" cy="'.intval($interiorAnchor[3] * 12700).'"/>
							</a:xfrm>
							<a:prstGeom prst="ellipse">
							  <a:avLst/>
							</a:prstGeom>
							<a:noFill/>
							<a:ln w="6350" cap="rnd" cmpd="sng">
							  <a:solidFill>
								<a:srgbClr val="FFFFFF"/>
							  </a:solidFill>
							  <a:prstDash val="solid"/>
							</a:ln>
						  </p:spPr>
						  <p:txBody>
							<a:bodyPr vert="horz" rot="0" anchor="ctr" wrap="square" tIns="45720" lIns="91440" bIns="45720" rIns="91440">
							  <a:normAutofit/>
							</a:bodyPr>
							<a:p>
							  <a:pPr algn="ctr" marL="0">
								<a:lnSpc>
								  <a:spcPct val="96000"/>
								</a:lnSpc>
							  </a:pPr>
							  <a:r>
								<a:rPr lang="en-US" b="true" i="false" sz="1250" baseline="0" u="none">
								  <a:solidFill>
									<a:schemeClr val="lt1"/>
								  </a:solidFill>
								  <a:latin typeface="微软雅黑"/>
								  <a:ea typeface="微软雅黑"/>
								</a:rPr>
								<a:t>0</a:t>
							  </a:r>
							</a:p>
						  </p:txBody>
						</p:sp>
						<p:sp>
						  <p:nvSpPr>
							<p:cNvPr name="Freeform 6" id="6"/>
							<p:cNvSpPr/>
							<p:nvPr/>
						  </p:nvSpPr>
						  <p:spPr>
							<a:xfrm flipV="true">
							  <a:off x="3652018" y="5638500"/>
							  <a:ext cx="77807" cy="87971"/>
							</a:xfrm>
							<a:custGeom>
							  <a:avLst/>
							  <a:gdLst/>
							  <a:ahLst/>
							  <a:cxnLst/>
							  <a:rect r="r" b="b" t="t" l="l"/>
							  <a:pathLst>
								<a:path w="1635382" h="1849045" stroke="true" fill="norm" extrusionOk="true">
								  <a:moveTo>
									<a:pt x="1496663" y="679532"/>
								  </a:moveTo>
								  <a:lnTo>
									<a:pt x="432721" y="41167"/>
								  </a:lnTo>
								  <a:cubicBezTo>
									<a:pt x="242316" y="-73133"/>
									<a:pt x="0" y="64122"/>
									<a:pt x="0" y="286245"/>
								  </a:cubicBezTo>
								  <a:lnTo>
									<a:pt x="0" y="1562881"/>
								  </a:lnTo>
								  <a:cubicBezTo>
									<a:pt x="0" y="1785004"/>
									<a:pt x="242316" y="1922164"/>
									<a:pt x="432721" y="1807864"/>
								  </a:cubicBezTo>
								  <a:lnTo>
									<a:pt x="1496568" y="1169594"/>
								  </a:lnTo>
								  <a:cubicBezTo>
									<a:pt x="1681639" y="1058532"/>
									<a:pt x="1681639" y="790499"/>
									<a:pt x="1496663" y="679532"/>
								  </a:cubicBezTo>
								  <a:close/>
								</a:path>
							  </a:pathLst>
							</a:custGeom>
							<a:solidFill>
							  <a:srgbClr val="FFFFFF"/>
							</a:solidFill>
							<a:ln cap="rnd" cmpd="sng">
							  <a:prstDash val="solid"/>
							</a:ln>
						  </p:spPr>
						  <p:txBody>
							<a:bodyPr vert="horz" rot="0" anchor="ctr" wrap="square" tIns="45720" lIns="91440" bIns="45720" rIns="91440">
							  <a:normAutofit/>
							</a:bodyPr>
							<a:p>
							  <a:pPr algn="ctr" marL="0"/>
							</a:p>
						  </p:txBody>
						</p:sp>
					  </p:grpSp>
					  ';
					break;
				case 'text':
					print_R($childrenItem);//exit;
					$textVerticalAlignment = $childrenItem['extInfo']['property']['textVerticalAlignment'];
					switch($textVerticalAlignment) {
						case 'TOP':
							$textVerticalAlignmentShort = "t";
							break;
						case 'BOTTOM':
							$textVerticalAlignmentShort = "b";
							break;
						case 'LEFT':
							$textVerticalAlignmentShort = "l";
							break;
						case 'RIGHT':
							$textVerticalAlignmentShort = "r";
							break;
					}
					print '
					<p:sp>
						<p:nvSpPr>
						  <p:cNvPr name="AutoShape 2" id="2"/>
						  <p:cNvSpPr/>
						  <p:nvPr>
							<p:ph type="ctrTitle"/>
						  </p:nvPr>
						</p:nvSpPr>
						<p:spPr>
						  <a:xfrm>
							<a:off x="'.intval($childrenItem['point'][0] * 12700).'" y="'.intval($childrenItem['point'][1] * 12700).'"/>
							<a:ext cx="'.intval($childrenItem['point'][2] * 12700).'" cy="'.intval($childrenItem['point'][3] * 12700).'"/>
						  </a:xfrm>
						</p:spPr>
						<p:txBody>
						  <a:bodyPr vert="horz" anchor="'.$textVerticalAlignmentShort.'" tIns="45720" lIns="91440" bIns="45720" rIns="91440">
							<a:normAutofit/>
						  </a:bodyPr>
						  <a:p>
							<a:pPr algn="l" marL="0">
							  <a:lnSpc>
								<a:spcPct val="100000"/>
							  </a:lnSpc>
							  <a:spcBef>
								<a:spcPct val="0"/>
							  </a:spcBef>
							</a:pPr>
							<a:r>
							  <a:rPr lang="'.$文本对像['extInfo']['property']['lang'].'" b="'.$文本对像['extInfo']['property']['bold'].'" i="false" sz="'.($文本对像['extInfo']['property']['fontSize']*100).'" baseline="'.$文本对像['extInfo']['property']['baseline'].'" u="none" altLang="en-US">
								<a:solidFill>
								  <a:srgbClr val="FFFFFF"/>
								</a:solidFill>
								<a:latin typeface="'.$文本对像['extInfo']['property']['fontFamily'].'"/>
								<a:ea typeface="'.$文本对像['extInfo']['property']['fontFamily'].'"/>
							  </a:rPr>
							  <a:t>'.$文本对像['text'].'</a:t>
							</a:r>
						  </a:p>
						</p:txBody>
					</p:sp>
					';
					break;
			}
		}
		
		
		


			
		print '
		<p:spTree>
	<p:cSld>
	
	<p:clrMapOvr>
		<a:masterClrMapping/>
	</p:clrMapOvr>
</p:sld>';
;
}

?>
