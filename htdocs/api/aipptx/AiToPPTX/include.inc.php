<?php
/*
* 基础架构: 单点低代码开发平台 & AiToPPTX
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2024
* License: GPL V3 or Commercial license
* Version: 0.0.1
*/
header("Content-Type: application/json; charset=utf-8");

require_once(__DIR__.'/lib/functions.inc.php');
require_once(__DIR__.'/lib/AiToPptx_DrawGroupObject.php');
require_once(__DIR__.'/lib/AiToPptx_DrawSingleObject.php');
require_once(__DIR__.'/lib/AiToPptx_MakeContentTypesXml.php');
require_once(__DIR__.'/lib/AiToPptx_MakeMasterXml.php');
require_once(__DIR__.'/lib/AiToPptx_MakePresentationXml.php');
require_once(__DIR__.'/lib/AiToPptx_MakePresentationXmlRelations.php');
require_once(__DIR__.'/lib/AiToPptx_MakeRootRelations.php');
require_once(__DIR__.'/lib/AiToPptx_MakeSingleSlide.php');
require_once(__DIR__.'/lib/AiToPptx_MakeSlideLayout.php');
require_once(__DIR__.'/lib/AiToPptx_MakeThemeXml.php');

//把JSON数据转换为PPTX文件
function AiToPptx_MakePptx($JsonData, $TargetCacheDir, $TargetPptxFilePath) {

	// 确保子文件夹都存在
	if(!is_dir($TargetCacheDir."/_rels")) 		mkdir($TargetCacheDir."/_rels");
	if(!is_dir($TargetCacheDir."/docProps")) 	mkdir($TargetCacheDir."/docProps");
	if(!is_dir($TargetCacheDir."/ppt")) 		mkdir($TargetCacheDir."/ppt");
	if(!is_dir($TargetCacheDir."/ppt/_rels")) 	mkdir($TargetCacheDir."/ppt/_rels");
	if(!is_dir($TargetCacheDir."/ppt/media")) 	mkdir($TargetCacheDir."/ppt/media");
	if(!is_dir($TargetCacheDir."/ppt/theme")) 	mkdir($TargetCacheDir."/ppt/theme");
	if(!is_dir($TargetCacheDir."/ppt/slideLayouts")) 	mkdir($TargetCacheDir."/ppt/slideLayouts");
	if(!is_dir($TargetCacheDir."/ppt/slideMasters")) 	mkdir($TargetCacheDir."/ppt/slideMasters");
	if(!is_dir($TargetCacheDir."/ppt/slides")) 			mkdir($TargetCacheDir."/ppt/slides");
	if(!is_dir($TargetCacheDir."/ppt/theme/_rels")) 	mkdir($TargetCacheDir."/ppt/theme/_rels");
	if(!is_dir($TargetCacheDir."/ppt/slideLayouts/_rels")) 	mkdir($TargetCacheDir."/ppt/slideLayouts/_rels");
	if(!is_dir($TargetCacheDir."/ppt/slideMasters/_rels")) 	mkdir($TargetCacheDir."/ppt/slideMasters/_rels");
	if(!is_dir($TargetCacheDir."/ppt/slides/_rels")) 		mkdir($TargetCacheDir."/ppt/slides/_rels");
	if(!is_dir($TargetCacheDir."/ppt/theme/_rels")) 		mkdir($TargetCacheDir."/ppt/theme/_rels");

	// 生成每个Slide页面
	$pages = $JsonData['pages'];
	for($i=0;$i<sizeof($pages);$i++) {
		$FilePath 			  = $TargetCacheDir."/ppt/slides/slide".($i+1).".xml";
		$RelationPath 		= $TargetCacheDir."/ppt/slides/_rels/slide".($i+1).".xml.rels";
		AiToPptx_MakeSingleSlide($JsonData['pages'][$i], $FilePath, $RelationPath);
	}

	//生成slideMaster页面,也有可能会有多个页面
	$slideMasters 		= $JsonData['slideMasters'];
	$MakeMasterXmlData 	= AiToPptx_MakeMasterXml($JsonData['slideMasters'], $TargetCacheDir);

	// 生成theme页面,也有可能会有多个页面
	$slideMasters 		= $JsonData['slideMasters'];
	$MakeThemeXmlData 	= AiToPptx_MakeThemeXml($JsonData['slideMasters'], $TargetCacheDir);

	// 生成slideLayouts页面
	$slideLayouts = (array)$JsonData['slideMasters'][0]['slideLayouts'];
	for($i=0;$i<sizeof($slideLayouts);$i++) {
		$MakeSlideLayoutData = AiToPptx_MakeSlideLayout($slideLayouts[$i], $TargetCacheDir."/ppt/slideLayouts/slideLayout".($i+1).".xml", $TargetCacheDir."/ppt/slideLayouts/_rels/slideLayout".($i+1).".xml.rels");
		//print $MakeSlideLayoutData;
	}

	// 生成 /ppt/presentation.xml
	AiToPptx_MakePresentationXml($JsonData, $TargetCacheDir);

	// 生成 /ppt/_rels/presentation.xml.rels
	AiToPptx_MakePresentationXmlRelations($JsonData, $TargetCacheDir);

	// 复制必备文件
	copy(__DIR__."/xml/presProps.xml", $TargetCacheDir."/ppt/presProps.xml");
	copy(__DIR__."/xml/tableStyles.xml", $TargetCacheDir."/ppt/tableStyles.xml");
	copy(__DIR__."/xml/theme.xml", $TargetCacheDir."/ppt/theme.xml");
	copy(__DIR__."/xml/viewProps.xml", $TargetCacheDir."/ppt/viewProps.xml");
	copy(__DIR__."/xml/app.xml", $TargetCacheDir."/docProps/app.xml");
	copy(__DIR__."/xml/core.xml", $TargetCacheDir."/docProps/core.xml");

	// 生成 /ppt/_rels/presentation.xml.rels
	AiToPptx_MakePresentationXmlRelations($JsonData, $TargetCacheDir);

	// 生成 /_rels/.rels
	AiToPptx_MakeRootRelations($JsonData, $TargetCacheDir);

	// 生成 /Content_Types.xml
	AiToPptx_MakeContentTypesXml($JsonData, $TargetCacheDir);

	// 压缩所有文件,并且生成PPTX
	AiToPptx_CreateZip($TargetCacheDir, $TargetPptxFilePath);

}

//把Markdown转换为GenerateContent.php接口中最后一步的JSON Data
function Markdown_To_Generate_Content_Json($FullResponeText) {

  //非空处理
  $FullResponeTextArray = explode("\n", $FullResponeText);
  $FullResponeTextArrayNotNullLine = [];
  foreach($FullResponeTextArray as $Item) {
    if(trim($Item)!="") {
      $FullResponeTextArrayNotNullLine[] = trim($Item);
    }
  }

  //转为MAP
  $Map    = [];
  $PPTX标题 = "";
  $章节标题 = "";
  $小节标题 = "";
  $小节内容 = "";
  foreach($FullResponeTextArrayNotNullLine as $Item) {
    if(substr($Item, 0, 2) == '# ') {
      $PPTX标题     = $Item;
      //$Map['标题']  = $PPTX标题;
    }
    else if(substr($Item, 0, 3) == '## ') {
      $章节标题 = $Item;
      //$Map['章节'][$章节标题] = $章节标题;
    }
    else if(substr($Item, 0, 4) == '### ') {
      $小节标题 = $Item;
      //$Map['小节'][$章节标题][] = $小节标题;
    }
    else {
      $Map[$PPTX标题][$章节标题][$小节标题][] = $Item;
    }
  }
  //print_R($Map);exit;

  //输出为JSON
  $页面JSON列表 = [];
  foreach($Map[$PPTX标题] as $章节名称 => $章节信息) {
    $章节JSON列表 = [];
    foreach($章节信息 as $小节名称 => $小节列表) {
      //print_R($章节名称);
      //print_R($小节列表);
      $小节JSON列表 = [];
      for($i=0;$i<sizeof($小节列表);$i=$i+2) {
        $小节标题 = $小节列表[$i];
        $小节内容 = $小节列表[$i+1];
        //print_R($小节标题);
        //print_R($小节内容);
        $小节JSON = [];
        $小节JSON['level']      = 4;
        $小节JSON['name']       = $小节标题;
        $小节JSON['children']   = [['children'=>[], 'level'=>0, 'type'=>'-', 'name'=>$小节内容]];
        $小节JSON列表[]        = $小节JSON;
      }
      $章节JSON               = [];
      $章节JSON['level']      = 3;
      $章节JSON['name']       = $小节名称;
      $章节JSON['children']   = $小节JSON列表;
      $章节JSON列表[]         = $章节JSON;
    }
    $二级标题JSON               = [];
    $二级标题JSON['level']      = 2;
    $二级标题JSON['name']       = $章节名称;
    $二级标题JSON['children']   = $章节JSON列表;
    $页面JSON列表[]             = $二级标题JSON;
    //print_R($二级标题JSON);
  }

  $最终结构 = [];
  $最终结构['level']      = 1;
  $最终结构['name']       = $PPTX标题;
  $最终结构['children']   = $页面JSON列表;

  return $最终结构;
}

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

function 替换首页或尾页($指定页面JSON, $PPTX标题, $PPTX作者, $页面页码, $页面标题)  {
  //print_R($指定页面JSON);exit;
  //替换首页信息
  //$Page['children'][0]['children'][0]['children'][0]['text'] = "PPT标题";
	//$Page['children'][1]['children'][0]['children'][0]['text'] = "汇报人";
  $PageChildren 	= (array)$指定页面JSON['children'];
  $Counter        = 0;
	for($i=0;$i<sizeof($PageChildren);$i++) {
		if($PageChildren[$i]['type'] == 'text' && $PageChildren[$i]['children'][0]['children'][0]['text']!="" && $Counter == 0)  {
      $PageChildren[$i]['children'][0]['children'][0]['text'] = $PPTX标题;
      $Counter += 1;
      //print $PageChildrenItem['children'][0]['children'][0]['text']."<BR>";
    }
    else if($PageChildren[$i]['type'] == 'text' && $PageChildren[$i]['children'][0]['children'][0]['text']!="" && $Counter == 1)  {
      $PageChildren[$i]['children'][0]['children'][0]['text'] = $PPTX作者;
      $Counter += 1;
      //print $PageChildrenItem['children'][0]['children'][0]['text']."<BR>";
    }
    //print_R($PageChildren[$i]['children'][0]['children'][0]['text']);
  }
  $指定页面JSON['children'] = $PageChildren;
  $指定页面JSON['title']    = $页面标题;
  $指定页面JSON['page']     = $页面页码;
  //print_R($指定页面JSON);
  return $指定页面JSON;
}

function 替换目录页($指定页面JSON, $目录LIST)  {
  //print_R($指定页面JSON);exit;
  //替换首页信息
  //$Page['children'][0]['children'][0]['children'][0]['text'] = "PPT标题";
	//$Page['children'][1]['children'][0]['children'][0]['text'] = "汇报人";
  $PageChildren 	= (array)$指定页面JSON['children'];
	for($i=0;$i<sizeof($PageChildren);$i++) {
		if(
      $PageChildren[$i]['type'] == 'text' &&
      $PageChildren[$i]['children'][0]['type']=="p" &&
      $PageChildren[$i]['children'][0]['children'][0]['text']!=""
      )  {
      //$PageChildren[$i]['children'][0]['children'][0]['text'] = $PPTX标题;
      //print $PageChildren[$i]['children'][0]['children'][0]['text']."<BR>";
      //除以10表示用于兼容处理细微的布局差异
			$X = intval($PageChildren[$i]['point'][0]/5) + 10000;
			$Y = intval($PageChildren[$i]['point'][1]/5) + 10000;
			if($PageChildren[$i]['children'][0]['children'][0]['text']!="" && $PageChildren[$i]['children'][0]['children'][0]['text']!="CONTENTS")  {
				$Page数据信息[$Y."_".$X] = ['title'=>trim($PageChildren[$i]['children'][0]['children'][0]['text']), 'id'=>$i];
			}
    }
  }
	ksort($Page数据信息);
  $标题列表 = [];
  foreach($Page数据信息 as $KEY => $Title_Id)  {
    if(strlen($Title_Id['title'])>5)  {
      $标题列表[] = $Title_Id;
    }
  }

  //把当前页面参数转为TEXT,然后做替换,最后再转化为JSON
  $指定页面JSONText = json_encode($指定页面JSON);
  for($i=0;$i<sizeof($目录LIST);$i++)  {
    $目录新标题       = $目录LIST[$i];
    $目录旧标题Title  = $标题列表[$i]['title'];
    $目录旧标题Id     = $标题列表[$i]['id'];
    $PageChildren[$目录旧标题Id]['children'][0]['children'][0]['text'] = $目录新标题;
  }

  $指定页面JSON['children'] = $PageChildren;

  return $指定页面JSON;
}

function 得到所有的内容明细页面($pages)     {
  $可以随机抽取使用的页面数据 = [];
  for($i=3;$i<sizeof($pages);$i++)    {
    $Page                     = $pages[$i];
    $得到指定页面的标题列表Data = 得到指定页面的标题列表($Page);
    if(sizeof($得到指定页面的标题列表Data) == 7) { //7代表是三个大项, 再加一个标题
      $可以随机抽取使用的页面数据[7][] = $Page;
    }
    if(sizeof($得到指定页面的标题列表Data) == 9) { //9代表是四个大项, 再加一个标题
      $可以随机抽取使用的页面数据[9][] = $Page;
    }
  }
  $可以随机抽取使用的页面数据[7] = array_merge($可以随机抽取使用的页面数据[7], $可以随机抽取使用的页面数据[7]);
  $可以随机抽取使用的页面数据[9] = array_merge($可以随机抽取使用的页面数据[9], $可以随机抽取使用的页面数据[9]);
  $可以随机抽取使用的页面数据[9] = array_merge($可以随机抽取使用的页面数据[9], $可以随机抽取使用的页面数据[9]);

  return $可以随机抽取使用的页面数据;
}

function 得到指定页面的标题列表($指定页面JSON)  {
  //print_R($章节小节名称);exit;
  //替换首页信息
  //$Page['children'][0]['children'][0]['children'][0]['text'] = "PPT标题";
	//$Page['children'][1]['children'][0]['children'][0]['text'] = "汇报人";
  $PageChildren 	= (array)$指定页面JSON['children'];
	for($i=0;$i<sizeof($PageChildren);$i++) {
    //检查是否需要把第0和第1进行互换
    if( $PageChildren[$i]['type'] == 'text' &&
        $PageChildren[$i]['children'][0]['type']=="p" &&
        $PageChildren[$i]['children'][1]['type']=="p" &&
        $PageChildren[$i]['children'][0]['children'][0]['text']=="" &&
        $PageChildren[$i]['children'][1]['children'][0]['text']!=""
      )  {
        $Temp = $PageChildren[$i]['children'][0];
        $PageChildren[$i]['children'][0] = $PageChildren[$i]['children'][1];
        $PageChildren[$i]['children'][1] = $Temp;
      }
		if( $PageChildren[$i]['type'] == 'text' &&
        $PageChildren[$i]['children'][0]['type']=="p" &&
        $PageChildren[$i]['children'][0]['children'][0]['text']!=""
      )  {
      //$PageChildren[$i]['children'][0]['children'][0]['text'] = $PPTX标题;
      //print $PageChildren[$i]['children'][0]['children'][0]['text']."<BR>";
      //除以10表示用于兼容处理细微的布局差异
			$X = intval($PageChildren[$i]['point'][0]/5) + 10000;
			$Y = intval($PageChildren[$i]['point'][1]/5) + 10000;
			if($PageChildren[$i]['children'][0]['children'][0]['text']!="" && $PageChildren[$i]['children'][0]['children'][0]['text']!="CONTENTS")  {
				$Page数据信息[$Y."_".$X] = ['title'=>trim($PageChildren[$i]['children'][0]['children'][0]['text']), 'id'=>$i];
			}
    }
  }
	ksort($Page数据信息);
  //print_R($Page数据信息);//exit;
  $标题列表 = [];
  foreach($Page数据信息 as $KEY => $Title_Id)  {
    if(strlen($Title_Id['title'])>5)  {
      $标题列表[] = $Title_Id;
    }
  }

  return $标题列表;
}

function 替换内容页($指定页面JSON, $章节小节名称, $章节小节内容, $页面页码)  {
  //print_R($章节小节名称);exit;
  //替换首页信息
  //$Page['children'][0]['children'][0]['children'][0]['text'] = "PPT标题";
	//$Page['children'][1]['children'][0]['children'][0]['text'] = "汇报人";
  $PageChildren 	= (array)$指定页面JSON['children'];
	for($i=0;$i<sizeof($PageChildren);$i++) {
    //检查是否需要把第0和第1进行互换
    if( $PageChildren[$i]['type'] == 'text' &&
        $PageChildren[$i]['children'][0]['type']=="p" &&
        $PageChildren[$i]['children'][1]['type']=="p" &&
        $PageChildren[$i]['children'][0]['children'][0]['text']=="" &&
        $PageChildren[$i]['children'][1]['children'][0]['text']!=""
      )  {
        $Temp = $PageChildren[$i]['children'][0];
        $PageChildren[$i]['children'][0] = $PageChildren[$i]['children'][1];
        $PageChildren[$i]['children'][1] = $Temp;
      }
		if( $PageChildren[$i]['type'] == 'text' &&
        $PageChildren[$i]['children'][0]['type']=="p" &&
        $PageChildren[$i]['children'][0]['children'][0]['text']!=""
      )  {
      //$PageChildren[$i]['children'][0]['children'][0]['text'] = $PPTX标题;
      //print $PageChildren[$i]['children'][0]['children'][0]['text']."<BR>";
      //除以10表示用于兼容处理细微的布局差异
			$X = intval($PageChildren[$i]['point'][0]/5) + 10000;
			$Y = intval($PageChildren[$i]['point'][1]/5) + 10000;
			if($PageChildren[$i]['children'][0]['children'][0]['text']!="" && $PageChildren[$i]['children'][0]['children'][0]['text']!="CONTENTS")  {
				$Page数据信息[$Y."_".$X] = ['title'=>trim($PageChildren[$i]['children'][0]['children'][0]['text']), 'id'=>$i];
			}
    }
  }
	ksort($Page数据信息);
  //print_R($Page数据信息);//exit;
  $标题列表 = [];
  foreach($Page数据信息 as $KEY => $Title_Id)  {
    if(strlen($Title_Id['title'])>5)  {
      $标题列表[] = $Title_Id;
    }
  }

  print_R($标题列表);//exit;
  //把当前页面参数转为TEXT,然后做替换,最后再转化为JSON
  array_unshift($章节小节内容, $章节小节名称);
  for($i=0;$i<sizeof($章节小节内容);$i++)  {
    $目录新标题       = $章节小节内容[$i];
    $目录旧标题Title  = $标题列表[$i]['title'];
    $目录旧标题Id     = $标题列表[$i]['id'];
    $PageChildren[$目录旧标题Id]['children'][0]['children'][0]['text'] = $目录新标题;
  }

  $指定页面JSON['children'] = $PageChildren;
  $指定页面JSON['title']    = $章节小节名称;
  $指定页面JSON['page']     = $页面页码;

  return $指定页面JSON;
}

//把Markdown转为JSON Data
function Markdown_To_JsonData($MarkdownData, $JsonData) {
  //非空处理
  $FullResponeTextArray = explode("\n", $MarkdownData);
  $FullResponeTextArrayNotNullLine = [];
  foreach($FullResponeTextArray as $Item) {
    if(trim($Item)!="") {
      $FullResponeTextArrayNotNullLine[] = trim($Item);
    }
  }

  //转为MAP
  $Map    = [];
  $PPTX标题 = "";
  $章节标题 = "";
  $小节标题 = "";
  $小节内容 = "";
  foreach($FullResponeTextArrayNotNullLine as $Item) {
    if(substr($Item, 0, 2) == '# ') {
      $PPTX标题     = $Item;
      //$Map['标题']  = $PPTX标题;
    }
    else if(substr($Item, 0, 3) == '## ') {
      $章节标题 = $Item;
      //$Map['章节'][$章节标题] = $章节标题;
    }
    else if(substr($Item, 0, 4) == '### ') {
      $小节标题 = $Item;
      //$Map['小节'][$章节标题][] = $小节标题;
    }
    else {
      $Map[$PPTX标题][$章节标题][$小节标题][] = $Item;
    }
  }
  $pages      = $JsonData['pages'];
  $首页       = $pages[0];
  $目录页     = $pages[1];
  $章节标题页 = $pages[2];
  $尾页       = $pages[sizeof($pages)-1];

  //$替换后首页 = 替换首页或尾页($首页, $PPTX标题, "AiToPPTX", 1, $PPTX标题);
  //print_R($替换后首页);

  //$替换后尾页 = 替换首页或尾页($尾页, '非常感谢大家聆听', "AiToPPTX", sizeof($pages), '非常感谢大家聆听');
  //print_R($替换后尾页);

  //$目录LIST     = array_keys($Map[$PPTX标题]);
  //$替换后目录页  = 替换目录页($目录页, $目录LIST, 2, $PPTX标题);
  //print_R($替换后目录页);

  //$替换后章节标题页 = 替换首页或尾页($章节标题页, "01", $章节标题, 3, $章节标题);
  //print_R($替换后章节标题页);

  $得到所有的内容明细页面Data = 得到所有的内容明细页面($pages);
  //print_R($得到所有的内容明细页面Data);exit;

  $最终输出页面数据     = [];
  $最终输出页面数据[0]  = 替换首页或尾页($首页, $PPTX标题, "AiToPPTX", 1, $PPTX标题);

  $目录LIST     = array_keys($Map[$PPTX标题]);
  $最终输出页面数据[1]  = 替换目录页($目录页, $目录LIST, 2, $PPTX标题);

  $StartPage = 2;
  foreach((array)$Map[$PPTX标题] as $章节名称 => $章节小节列表)  {
    $最终输出页面数据[$StartPage]       = 替换首页或尾页($章节标题页, $章节名称, "0".($StartPage-1), $StartPage+1, $章节名称);
    $StartPage += 1;
    foreach($章节小节列表 as $章节小节名称 => $章节小节内容)  {
      if(sizeof($章节小节内容) == 6)  {
        $内容页                         = array_shift($得到所有的内容明细页面Data[7]);
        $最终输出页面数据[$StartPage]    = 替换内容页($内容页, $章节小节名称, $章节小节内容, $StartPage+1);
        $StartPage += 1;
      }
      if(sizeof($章节小节内容) == 8)  {
        $内容页                         = array_shift($得到所有的内容明细页面Data[9]);
        $最终输出页面数据[$StartPage]    = 替换内容页($内容页, $章节小节名称, $章节小节内容, $StartPage+1);
        $StartPage += 1;
      }
    }
  }

  $JsonData['pages'] = $最终输出页面数据;

  return $JsonData;

}

?>
