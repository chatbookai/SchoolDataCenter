<?php
header("Content-Type: application/json");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../config.inc.php');
require_once('../../adodb5/adodb.inc.php');
require_once("../../vendor/autoload.php");

$JsonContent      = file_get_contents("../json/课程学习汇报.json");
$Array            = json_decode($JsonContent, true);

function 得到单个页面的所有文本($Page) {
	$PageChildren 	= (array)$Page['children'];
	//$Page['children'][0]['children'][0]['children'];
	//$Page['children'][0]['children'][0]['children'][0]['text'] = "PPT标题";
	//$Page['children'][1]['children'][0]['children'][0]['text'] = "汇报人";
	$Page数据信息 = [];
	foreach($PageChildren as $PageChildrenItem)  {
		if($PageChildrenItem['children'][0]['children'][0]['text']!="")  {
			//print_R($PageChildrenItem['children'][0]['children'][0]['text']);
			//除以10表示用于兼容处理细微的布局差异
			$X = intval($PageChildrenItem['point'][0]/5) + 10000;
			$Y = intval($PageChildrenItem['point'][1]/5) + 10000;
			if($PageChildrenItem['children'][0]['children'][0]['text']!="")  {
				$Page数据信息[$X.rand(1111,9999)] = $Y."_".$X."____".trim($PageChildrenItem['children'][0]['children'][0]['text']);
			}
		}
		//print $PageChildrenItem['children'][0]['children'][0]['text']."\n";
	}
	ksort($Page数据信息);
	return array_values($Page数据信息);
}

function 解析首页和目录页面和尾页($Array)  {
	$首页            = $Array['pages'][0];
	$首页数据信息 		= 得到单个页面的所有文本($首页);

	$尾页            = array_pop($Array['pages']);
	$尾页数据信息 		= 得到单个页面的所有文本($尾页);
	//print_R($首页数据信息);


	$目录				= $Array['pages'][1];
	$目录数据信息 		= 得到单个页面的所有文本($目录);
	$目录数据信息MAP 		= [];
	$目录数据信息MAP['首页']['标题'] 	= $首页数据信息[0];
	$目录数据信息MAP['首页']['汇报人'] 	= $首页数据信息[1];
	$目录数据信息MAP['尾页']['标题'] 	= $尾页数据信息[0];
	$目录数据信息MAP['尾页']['汇报人'] 	= $尾页数据信息[1];
	$目录数据信息MAP['目录']['标题'] 	= $目录数据信息[0];
	if(sizeof($目录数据信息)==13)  {
		for($i=1;$i<sizeof($目录数据信息);$i=$i+2)  {
			$Key 	= $目录数据信息[$i];
			$Value 	= $目录数据信息[$i+1];
			$目录数据信息MAP['目录']['章节'][$Value] = $Key;
		}
		ksort($目录数据信息MAP['目录']['章节']);
	}
	return $目录数据信息MAP;
}

$解析首页和目录页面和尾页 = 解析首页和目录页面和尾页($Array);
//print_R($解析首页和目录页面和尾页);

$所有页面 = (array)$Array['pages'];
//print_R($所有页面[2]);exit;

//$单个页面数据信息	= 得到单个页面的所有文本($所有页面[8]);print_R($单个页面数据信息);exit;

//只过滤数据页. 首页,目录和尾页则不需要在此处过滤
$单个页面结果 = [];
for($i=2;$i<sizeof($所有页面)-1;$i++)  {
	$单个页面数据信息	= 得到单个页面的所有文本($所有页面[$i]);
	//print_R($单个页面数据信息);
	switch(sizeof($单个页面数据信息)) {
		case 2:
			//章节标题页面
			$序号Array = explode('____', $单个页面数据信息[0]);
			$标题Array = explode('____', $单个页面数据信息[1]);
			$单个页面结果[] = ['类型'=>'章节标题', '数据'=>['序号'=>$序号Array[1], '标题'=>$标题Array[1]]];
			break;
		case 4:
			//3个要点,不带序号
			//$单个页面结果[] = ['标题'=>$单个页面数据信息[0], '类型'=>'内容3要点', '内容'=>array_slice($单个页面数据信息, 1)];
			break;
		case 7:
			//3个要点,不带序号
			print_R($单个页面数据信息);
			$单个页面数据信息重新排序 = [];
			for($ii=1;$ii<sizeof($单个页面数据信息);$ii=$ii+1)  {
				$单个页面数据信息ItemArray = explode('____', $单个页面数据信息[$ii]);
				$单个页面数据信息重新排序[$单个页面数据信息ItemArray[0]] = $单个页面数据信息ItemArray[1];
			}
			ksort($单个页面数据信息重新排序);
			$单个页面数据信息重新排序 = array_values($单个页面数据信息重新排序);
			if($单个页面数据信息重新排序[0]=='03')  {
				$单个页面数据信息重新排序Result = [];
				foreach($单个页面数据信息重新排序 as $单个页面数据信息重新排序Item) {
					if(strlen($单个页面数据信息重新排序Item)>5) {
						$单个页面数据信息重新排序Result[] = $单个页面数据信息重新排序Item;
					}
				}
				rsort($单个页面数据信息重新排序Result);
			}
			//print_R($单个页面数据信息重新排序Result);
			$标题Array = explode('____', $单个页面数据信息[0]);
			$单个页面结果[] = ['标题'=>$标题Array[1], '类型'=>'内容3要点', '内容'=>$单个页面数据信息重新排序Result];
			break;
		case 10:
			//3个要点,带序号
			//print_R($单个页面数据信息);
			$单个页面数据信息重新排序 = [];
			for($ii=1;$ii<sizeof($单个页面数据信息);$ii=$ii+1)  {
				$单个页面数据信息ItemArray = explode('____', $单个页面数据信息[$ii]);
				$单个页面数据信息重新排序[$单个页面数据信息ItemArray[0]] = $单个页面数据信息ItemArray[1];
			}
			ksort($单个页面数据信息重新排序);
			$单个页面数据信息重新排序 = array_values($单个页面数据信息重新排序);
			print_R($单个页面数据信息重新排序);
			if($单个页面数据信息重新排序[0]=='01' && $单个页面数据信息重新排序[1]=='02' && $单个页面数据信息重新排序[2]=='03')  {
				$单个页面数据信息重新排序Result = [];
				$单个页面数据信息重新排序Result[$单个页面数据信息重新排序[3]] = $单个页面数据信息重新排序[6];
				$单个页面数据信息重新排序Result[$单个页面数据信息重新排序[4]] = $单个页面数据信息重新排序[7];
				$单个页面数据信息重新排序Result[$单个页面数据信息重新排序[5]] = $单个页面数据信息重新排序[8];
				$标题Array = explode('____', $单个页面数据信息[0]);
				$单个页面结果[] = ['标题'=>$标题Array[1], '类型'=>'内容3要点带序号', '内容'=>$单个页面数据信息重新排序Result];
			}
			if($单个页面数据信息重新排序[0]=='03' && $单个页面数据信息重新排序[3]=='02' && $单个页面数据信息重新排序[6]=='01')  {
				$单个页面数据信息重新排序Result = [];
				$单个页面数据信息重新排序Result[$单个页面数据信息重新排序[1]] = $单个页面数据信息重新排序[2];
				$单个页面数据信息重新排序Result[$单个页面数据信息重新排序[4]] = $单个页面数据信息重新排序[5];
				$单个页面数据信息重新排序Result[$单个页面数据信息重新排序[7]] = $单个页面数据信息重新排序[8];
				$标题Array = explode('____', $单个页面数据信息[0]);
				$单个页面结果[] = ['标题'=>$标题Array[1], '类型'=>'内容3要点带序号', '内容'=>$单个页面数据信息重新排序Result];
			}
			break;
		case 9:
			//4个要点,不带序号
			//print_R($单个页面数据信息);
			$单个页面数据信息重新排序 = [];
			for($ii=1;$ii<sizeof($单个页面数据信息);$ii=$ii+2)  {
				$单个页面数据信息ItemArray = explode('____', $单个页面数据信息[$ii]);
				$数据项1 = $单个页面数据信息ItemArray[1];
				$单个页面数据信息ItemArray = explode('____', $单个页面数据信息[$ii+1]);
				$数据项2 = $单个页面数据信息ItemArray[1];
				if(strlen($数据项1)<strlen($数据项2)) {
					$单个页面数据信息重新排序[$数据项1] = $数据项2;
				}
				else {
					$单个页面数据信息重新排序[$数据项2] = $数据项1;
				}
			}
			$单个页面结果[] = ['标题'=>$单个页面数据信息[0], '类型'=>'内容4要点', '内容'=>$单个页面数据信息重新排序];
			break;
	}
}
print_R($单个页面结果);



/*
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
*/

?>
