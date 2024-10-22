<?php

$PATH = "./json/0001/ppt/slides/slide1.xml";
$xmlString 	= file_get_contents($PATH);
$xmlString 	= str_replace(':', '____', $xmlString);
$xml 		= simplexml_load_string($xmlString);

$所有文本信息 = [];
$p____spTree = $xml->p____cSld->p____spTree->p____sp[1];
$Pages = $xml->p____cSld->p____spTree->p____sp;

if($Pages)   {
	for($i=0;$i<sizeof($Pages);$i++) {
		$Page = $Pages[$i];
		$Text = (array)$Page->p____txBody->a____p->a____r->a____t;
		if($Text[0]!="")  {
			$所有文本信息[] = $Text[0];
		}
		//print_R($所有文本信息);
	}
	print_R($所有文本信息);

	$标题目录 = [];
	for($i=1;$i<sizeof($所有文本信息);$i=$i+2) {
		$Value1 = $所有文本信息[$i];
		$Value2 = $所有文本信息[$i+1];
		if($Value2!="")  {
			if(strlen($Value1) < strlen($Value2)) {
				$标题目录[intval($Value1)] = $Value2;
			}
			else {
				$标题目录[intval($Value2)] = $Value1;
			}
		}
	}
	print_R($标题目录);

	print_R(sizeof($xml->p____cSld->p____spTree->p____sp));
}

$xmlString = $xml->asXML();
$xmlString = str_replace('____', ':', $xmlString);
//print_R($xmlString);
exit;

?>
