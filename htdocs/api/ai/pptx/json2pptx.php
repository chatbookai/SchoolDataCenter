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
			print_R($childrenItem);exit;
			switch($Type) {
				case 'text':
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
							<a:off x="1180395" y="1861130"/>
							<a:ext cx="6703784" cy="2387600"/>
						  </a:xfrm>
						</p:spPr>
						<p:txBody>
						  <a:bodyPr vert="horz" anchor="b" tIns="45720" lIns="91440" bIns="45720" rIns="91440">
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
