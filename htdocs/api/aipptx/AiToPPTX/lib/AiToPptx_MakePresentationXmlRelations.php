<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/

function AiToPptx_MakePresentationXmlRelations($JsonData, $写入文件目录)  {
	$pages = $JsonData['pages'];
	$$MakePresentationXmlList = [];
	for($i=0;$i<sizeof($pages);$i++) {
		$MakePresentationXmlList[] = '<Relationship Id="rId'.($i+6).'" Target="slides/slide'.($i+1).'.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide"/>';
	}
	$MakePresentationXmlContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Target="slideMasters/slideMaster1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideMaster"/>
<Relationship Id="rId2" Target="presProps.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/presProps"/>
<Relationship Id="rId3" Target="viewProps.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/viewProps"/>
<Relationship Id="rId4" Target="theme/theme1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme"/>
<Relationship Id="rId5" Target="tableStyles.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/tableStyles"/>
'.join("\n",$MakePresentationXmlList).'
</Relationships>';
	file_put_contents($写入文件目录."/ppt/_rels/presentation.xml.rels", $MakePresentationXmlContent);
}


?>
