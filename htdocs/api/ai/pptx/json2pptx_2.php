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

$目录JSON = $所有页面[1];

渲染目录($目录JSON);

function 渲染目录($目录JSON) {
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
		
		$childrenList = $目录JSON['children'];
		foreach($childrenList as $childrenItem)  {
			$Type 	= $childrenItem['type'];
			$Point 	= $childrenItem['point'];
			$childrenSub 	= $childrenItem['children'];
			$文本对像 		= $childrenItem['children'][0]['children'][0];
			$interiorAnchor = $childrenItem['extInfo']['property']['interiorAnchor'];
			if($文本对像['text']!="")  {
				//print_R($childrenItem);
			}
			switch($Type) {
				case 'text':
					//print_R($childrenItem);//exit;
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
							<p:cNvPr name="AutoShape 5" id="5"/>
							<p:cNvSpPr/>
							<p:nvPr>
								<p:ph type="title" idx="4294967295"/>
							</p:nvPr>
						</p:nvSpPr>
						<p:spPr>
							<a:xfrm>
								<a:off x="'.intval($childrenItem['point'][0] * 12700).'" y="'.intval($childrenItem['point'][1] * 12700).'"/>
								<a:ext cx="'.intval($childrenItem['point'][2] * 12700).'" cy="'.intval($childrenItem['point'][3] * 12700).'"/>
							</a:xfrm>
							<a:prstGeom prst="rect">
								<a:avLst/>
							</a:prstGeom>
						</p:spPr>
						<p:txBody>
							<a:bodyPr vert="horz" anchor="b" tIns="45720" lIns="91440" bIns="45720" rIns="91440">
								<a:normAutofit/>
							</a:bodyPr>
							<a:p>
								<a:pPr algn="l">
									<a:lnSpc>
										<a:spcPct val="90000"/>
									</a:lnSpc>
									<a:spcBef>
										<a:spcPct val="0"/>
									</a:spcBef>
								</a:pPr>
								<a:r>
								  <a:rPr lang="'.$文本对像['extInfo']['property']['lang'].'" b="'.$文本对像['extInfo']['property']['bold'].'" i="false" sz="'.($文本对像['extInfo']['property']['fontSize']*100).'" baseline="'.$文本对像['extInfo']['property']['baseline'].'" u="none" altLang="en-US">
									<a:solidFill>
									  <a:srgbClr val="'.$文本对像['extInfo']['property']['fontColor']['color']['scheme'].'"/>
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
