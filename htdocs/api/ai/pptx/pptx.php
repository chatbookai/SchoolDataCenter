<?php
header("Content-Type: application/json");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");


$JsonContent      = file_get_contents("./json/0001.json");
$Array            = json_decode($JsonContent, true);

$首页             = $Array['pages'][0];
$首页['children'][0]['children'][0]['children'];
$首页['children'][0]['children'][0]['children'][0]['text'] = "PPT标题";
$首页['children'][1]['children'][0]['children'][0]['text'] = "汇报人";
//print_R($首页['children'][1]['children'][0]['children'][0]['text']);

$目录			= $Array['pages'][1];
$目录Children 	= (array)$目录['children'];
//$目录['children'][0]['children'][0]['children'];
//$目录['children'][0]['children'][0]['children'][0]['text'] = "PPT标题";
//$目录['children'][1]['children'][0]['children'][0]['text'] = "汇报人";
foreach($目录Children as $目录ChildrenItem)  {
	if($目录ChildrenItem['children'][0]['children'][0]['text']!="")  {
		//print_R($目录ChildrenItem['children'][0]['children'][0]['text']);
		//print_R($目录ChildrenItem['point']);
	}
	//print $目录ChildrenItem['children'][0]['children'][0]['text']."\n";
}
//print_R(json_encode($目录Children));

$章节封面	= $Array['pages'][2];
//print_R($章节封面['title']);
foreach($章节封面['children'] as $章节封面ChildrenItem)  {
	//print $章节封面ChildrenItem['children'][0]['children'][0]['text']."\n";
}
//print_R(json_encode($章节封面));

$内容页	= $Array['pages'][3];
//print_R($内容页['title']);
foreach($内容页['children'] as $内容页ChildrenItem)  {
	//print $内容页ChildrenItem['children'][0]['children'][0]['text']."\n";
	//print $内容页ChildrenItem['children'][1]['children'][0]['text']."\n";
}
//print_R(json_encode($内容页));


$内容页	= $Array['pages'][4];
//print_R($内容页['title']);
//print "元素数量：".sizeof($内容页['children'])."\n";
foreach($内容页['children'] as $内容页ChildrenItem)  {
	//print $内容页ChildrenItem['children'][0]['children'][0]['text']."\n";
	//print $内容页ChildrenItem['children'][1]['children'][0]['text']."\n";
}
//print_R(json_encode($内容页));

$章节封面	= $Array['pages'][5];
//print_R($章节封面['title']);
//print "元素数量：".sizeof($章节封面['children'])."\n";
foreach($章节封面['children'] as $章节封面ChildrenItem)  {
	//print $章节封面ChildrenItem['children'][0]['children'][0]['text']."\n";
}


$所有页面 = (array)$Array['pages'];
$PageId = 1;
foreach($所有页面 as $单个页面) 	{
	$有效元素数量 = 0;
	print "\n\n----------------------------------------------------------\n";
	print "PageId: $PageId 元素数量：".sizeof($单个页面['children'])."\n";
	foreach($单个页面['children'] as $单个页面ChildrenItem)  {
		if($单个页面ChildrenItem['children'][0]['children'][0]['text']!="")  {
			print $单个页面ChildrenItem['children'][0]['children'][0]['text']."\n";
			$有效元素数量 ++;
		}
	}
	print "有效元素数量: $有效元素数量\n";
	$PageId ++ ;
}

?>
