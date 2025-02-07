<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING); 
require_once('config.inc.php');
require_once('./AiToPPTX/include.inc.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");
header('Content-Type: text/event-stream; charset=utf-8');
header('Cache-Control: no-cache');
header('Pragma: no-cache');

// 处理 OPTIONS 请求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

//清除缓存, 请除两天以前的数据, 尽可能使用Redis保持一个小的状态
$date = date('Ymd', strtotime("-2 day"));
$result = $redis->del("PPTX_CONTENT_" . $date);
$result = $redis->del("PPTX_OUTLINE_" . $date);
$result = $redis->del("PPTX_DOWNLOAD_" . $date);
$result = $redis->del("PPTX_CurrentPage_" . $date);

//把 Application/json的payload data 转为 _POST
$_POST = json_decode(file_get_contents("php://input"), true);

$_POST['asyncGenPptx']  = true;

if($_POST['asyncGenPptx'] == true)   {
    $pptId              = date("Ymd_His_").rand("1111",9999);

    $outlineMarkdown    = $_POST['outlineMarkdown'];

    //此变量为测试时候使用, 正式发布以后, 则没有使用
    $outlineMarkdown_TEST_MODE    = '# 实时生成PPT行业总结性报告

## 1. 实时生成PPT技术概述
### 1.1 技术定义与背景
1.1.1 实时生成PPT的基本概念。
1.1.2 技术的发展历程。
1.1.3 当前市场的主要应用场景。

### 1.2 技术原理与架构
1.2.1 实时生成PPT的核心技术。
1.2.2 系统架构与工作流程。
1.2.3 数据处理与算法优化。

### 1.3 技术优势与挑战
1.3.1 实时生成PPT的主要优势。
1.3.2 技术实现中的关键挑战。
1.3.3 未来技术发展趋势。

## 2. 实时生成PPT市场分析
### 2.1 市场规模与增长
2.1.1 全球市场规模与增长率。
2.1.2 主要地区市场分析。
2.1.3 市场驱动因素与制约因素。

### 2.2 竞争格局与主要玩家
2.2.1 主要竞争者的市场份额。
2.2.2 竞争者的技术优势与劣势。
2.2.3 新兴竞争者与市场机会。

### 2.3 用户需求与行为分析
2.3.1 用户需求的主要特点。
2.3.2 用户行为模式与偏好。
2.3.3 用户反馈与改进建议。

## 3. 实时生成PPT应用案例
### 3.1 企业应用案例
3.1.1 大型企业的应用场景。
3.1.2 中小企业的应用案例。
3.1.3 企业应用的效果评估。

### 3.2 教育领域应用案例
3.2.1 在线教育平台的应用。
3.2.2 学校与培训机构的应用。
3.2.3 教育应用的效果分析。

### 3.3 其他行业应用案例
3.3.1 医疗行业的应用。
3.3.2 政府与公共部门的应用。
3.3.3 其他行业的创新应用。

## 4. 实时生成PPT技术发展趋势
### 4.1 技术创新与突破
4.1.1 人工智能与机器学习的应用。
4.1.2 云计算与边缘计算的结合。
4.1.3 数据安全与隐私保护技术。

### 4.2 行业标准与规范
4.2.1 行业标准的制定与推广。
4.2.2 技术规范与最佳实践。
4.2.3 国际合作与标准化进程。

### 4.3 未来市场预测
4.3.1 未来市场规模预测。
4.3.2 技术发展趋势预测。
4.3.3 市场机会与风险分析。

## 5. 实时生成PPT的挑战与解决方案
### 5.1 技术挑战
5.1.1 实时性与准确性的平衡。
5.1.2 数据处理与存储的挑战。
5.1.3 系统稳定性与可靠性。

### 5.2 市场挑战
5.2.1 市场竞争与价格压力。
5.2.2 用户教育与市场推广。
5.2.3 法规与政策的影响。

### 5.3 解决方案与策略
5.3.1 技术优化与创新策略。
5.3.2 市场定位与差异化竞争。
5.3.3 用户支持与服务提升。

## 6. 实时生成PPT的未来展望
### 6.1 技术未来发展方向
6.1.1 智能化与自动化趋势。
6.1.2 跨平台与多设备支持。
6.1.3 个性化与定制化服务。

### 6.2 市场未来发展趋势
6.2.1 新兴市场的潜力与机会。
6.2.2 行业整合与并购趋势。
6.2.3 用户需求的变化与应对。

### 6.3 社会与经济影响
6.3.1 对工作效率的提升。
6.3.2 对教育与培训的影响。
6.3.3 对社会创新的推动作用。';

    $result = $redis->hSet("PPTX_OUTLINE_".date('Ymd'), $pptId, $outlineMarkdown);

    $promptText = "
        你是一位PPTX大纲的编写人员, 需要根据以下要求对PPTX大纲结构进行解释和扩充.

        PPTX大纲结构规则:
        1 # 开头的表示PPTX的标题
        2 ## 开头的表示PPTX的某个章节
        3 ### 开头的表示的是某个章节下面的小节
        4 类似于这样'1.1.1'开头的是PPTX小节的内容项

        你的任务:
        1 以# ## ###开头的标题,章节或是小节,则不需要做任何修改,直接按原有结构返回即可.
        2 把类似于这样'1.1.1'开头的是PPTX小节的内容项进行解释和扩充, 形成1.1.1.1的内容, 扩充后的内容要求在20 - 50个字之间.

        示例输入:
        ### 1.1 AI生成PPTX的定义与背景
        1.1.1 定义AI生成PPTX的概念。
        1.1.2 介绍AI在办公自动化中的应用背景。
        1.1.3 分析PPTX格式在现代办公中的重要性。

        示例输出:
        ### 1.1 AI生成PPTX的定义与背景
        1.1.1 定义AI生成PPTX的概念。
        AI生成PPTX是指利用人工智能技术自动创建演示文稿文件（PPTX）。这项技术结合自然语言处理和机器学习等领域，通过输入主题或文本，生成结构化和视觉化的演示内容，旨在提升用户的工作效率和创造力。
        1.1.2 介绍AI在办公自动化中的应用背景。
        在现代办公自动化中，AI技术被广泛应用于数据分析、文档生成、自动化流程等领域。诸如自然语言处理、图像识别等AI功能，极大地提高了工作效率，降低了繁琐的手动操作，使得办公软件能够更智能化地支持用户。
        1.1.3 分析PPTX格式在现代办公中的重要性。
        PPTX格式是Microsoft PowerPoint使用的演示文稿格式，被广泛用于商务会议、学术报告及教育培训中。其多媒体支持、丰富的动画效果和易操作的界面，使得PPTX成为信息传递的重要工具，能有效增强沟通效果与信息吸引力。

        注意事项:
        1 请注意: 本次要求只是对原有内容的内容项做扩充, 不需要对PPTX的大纲结构做任何修改.
        2 只输出必要的数据，不需要输出跟大纲无关的内容，输出的结果以Markdown的格式输出。
        3 不需要输出总结性的文本。
        4 '1.1.1 定义AI生成PPTX的概念。'前面不要加 ###

        以下是需要处理的文本:
        $outlineMarkdown
    ";

    //对原始数据进行分页后统计,得到总有多少页
    //输出的时候,第一页和第二页不需要做扩充,所以可以直接输出
    //依赖于AI的部分是从第三页开始
    $TotalPagesNumber = 根据大纲得到PPTX页码($outlineMarkdown);

    $curl       = curl_init();

    $messages 	= [];
    $messages[] = ['content'=> $promptText, 'role'=>'user'];

    //删除昨天的记录数据
    $result = $redis->del("PPTX_CONTENT_".date('Ymd', strtotime('-1 day')));

    $redis->hSet("PPTX_CONTENT_".date('Ymd'), $pptId, json_encode(['data'=>'', 'total'=>0, 'current'=>0, 'finished'=>false]));
    print 'data: {"current":1, "pptId":"'.$pptId.'", "status":3, "text":"", "total":'.$TotalPagesNumber.'}'."\n\n";

    $CURLOPT_POSTFIELDS = [
        "model" => $API_MODE,
        "messages" => $messages,
        "frequency_penalty" => 0,
        "max_tokens" => 2048,
        "presence_penalty" => 0,
        "response_format" => [
            "type" => "text"
        ],
        "stream" => true,
        "temperature" => 0,
        "top_p" => 1,
        "tool_choice" => "none",
        "logprobs" => false,
    ];
    $CURLOPT_POSTFIELDS = json_encode($CURLOPT_POSTFIELDS, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $FullResponeText    = '';
    $分段结构输出情况     = [];
    curl_setopt_array($curl, array(
        CURLOPT_URL => $API_URL . '/chat/completions',
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_WRITEFUNCTION => function($curl, $data) use (&$FullResponeText, &$分段结构输出情况, &$pptId, &$TotalPagesNumber, &$redis) {
          static $buffer = '';  // 用于存储不完整的数据块
          $buffer .= $data;     // 将当前数据块追加到缓冲区

          // 检查是否包含结束标记 [DONE]
          if (strpos($buffer, '[DONE]') !== false) {
            // 输出最终的 FullResponeText
            //print "Final FullResponeText: $FullResponeText\n";
            $Result           = [];
            $Result['result'] = Markdown_To_Generate_Content_Json($FullResponeText);
            print 'data: {"current":'.$TotalPagesNumber.', "pptId":"'.$pptId.'", "status":3, "text":"## ", "total":'.$TotalPagesNumber.'}'."\n\n";
            print "data: ".json_encode($Result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n\n";
            $redis->hSet("PPTX_CONTENT_".date('Ymd'), $pptId, json_encode(['data'=>$FullResponeText, 'total'=>$TotalPagesNumber, 'current'=>$TotalPagesNumber, 'finished'=>true]));
            //$redis->hSet("PPTX_CurrentPage_".date('Ymd'), $pptId, $CurrentPage);
            return strlen($data);
          }

          while (preg_match('/"content":"([^"]*)"/', $buffer, $matches)) {
              $outputData = $matches[1];
              $FullResponeText .= $outputData;
              //echo $outputData;
              echo 'data: {"status":3,"text":"'.$outputData.'"}'."\n\n";
              $FullResponeTextArray = explode("\\n", $FullResponeText);
              $FullResponeTextArrayNotNullLine = [];
              foreach($FullResponeTextArray as $Item) {
                if(trim($Item)!="") {
                  $FullResponeTextArrayNotNullLine[] = trim($Item);
                }
              }
              $LastElement1 = array_pop($FullResponeTextArrayNotNullLine);
              if($LastElement1 && substr($LastElement1, 0, 3) == '## ') {
                //print_R($LastElement1);
                //print_R($FullResponeTextArrayNotNullLine);
                //分段结构只需要输出一次即可, 为实现这个目标, 需要加一个输出标记
                $LastElement2 = array_pop($FullResponeTextArrayNotNullLine);
                $分段结构标记 = md5($LastElement2);
                if(!in_array($分段结构标记, $分段结构输出情况))  {
                  $CurrentPage      = sizeof($分段结构输出情况) + 2;
                  print 'data: {"current":'.$CurrentPage.', "pptId":"'.$pptId.'", "status":3, "text":"## ", "total":'.$TotalPagesNumber.'}'."\n\n";
                  $分段结构输出情况[] = $分段结构标记;
                  $redis->hSet("PPTX_CONTENT_".date('Ymd'), $pptId, json_encode(['data'=>$FullResponeText, 'total'=>$TotalPagesNumber, 'current'=>$CurrentPage, 'finished'=>false]));
                  //$redis->hSet("PPTX_CurrentPage_".date('Ymd'), $pptId, $CurrentPage);
                }
              }
              if($LastElement1 && substr($LastElement1, 0, 4) == '### ') {
                //print_R($LastElement1);
                $LastElement2 = array_pop($FullResponeTextArrayNotNullLine);
                if($LastElement2 && substr($LastElement2, 0, 3) == '## ') {
                  //上一个元素是一个二级标题 当前是一个大的章节的第一个页面时
                  //分段结构只需要输出一次即可, 为实现这个目标, 需要加一个输出标记
                  $分段结构标记 = md5($LastElement2);
                  if(!in_array($分段结构标记, $分段结构输出情况))  {
                    $CurrentPage      = sizeof($分段结构输出情况) + 2;
                    print 'data: {"current":'.$CurrentPage.', "pptId":"'.$pptId.'", "status":3, "text":"### ", "total":'.$TotalPagesNumber.'}'."\n\n";
                    $分段结构输出情况[] = $分段结构标记;
                    $redis->hSet("PPTX_CONTENT_".date('Ymd'), $pptId, json_encode(['data'=>$FullResponeText, 'total'=>$TotalPagesNumber, 'current'=>$CurrentPage, 'finished'=>false]));
                    //$redis->hSet("PPTX_CurrentPage_".date('Ymd'), $pptId, $CurrentPage);
                  }
                }
                else {
                  //需要得到上一个结构是什么
                  $所有内容的结构信息 = [];
                  $上一个结构信息 = [];
                  foreach($FullResponeTextArrayNotNullLine as $Item) {
                    if(substr($Item, 0, 4) == '### ') {
                      if(sizeof($上一个结构信息)>0) {
                        $所有内容的结构信息[] = $上一个结构信息;
                      }
                      $上一个结构信息 = [];
                    }
                    elseif(substr($Item, 0, 2) != '# ') { //非首行记录
                      $上一个结构信息[] = $Item;
                    }
                  }
                  if(sizeof($上一个结构信息)>0) {
                    //分段结构只需要输出一次即可, 为实现这个目标, 需要加一个输出标记
                    $分段结构标记 = md5(serialize($上一个结构信息));
                    if(!in_array($分段结构标记, $分段结构输出情况))  {
                      //print "上一个结构信息:";
                      //print_R($上一个结构信息);
                      $CurrentPage      = sizeof($分段结构输出情况) + 2;
                      print 'data: {"current":'.$CurrentPage.', "pptId":"'.$pptId.'", "status":3, "text":"", "total":'.$TotalPagesNumber.'}'."\n\n";
                      $分段结构输出情况[] = $分段结构标记;
                      $redis->hSet("PPTX_CONTENT_".date('Ymd'), $pptId, json_encode(['data'=>$FullResponeText, 'total'=>$TotalPagesNumber, 'current'=>$CurrentPage, 'finished'=>false]));
                      //$redis->hSet("PPTX_CurrentPage_".date('Ymd'), $pptId, $CurrentPage);
                    }
                  }
                }
                //print_R($FullResponeTextArrayNotNullLine);
              }
              //后续需要实现统计出有多少页PPTX, 然后需要标记当前页码, 从而实现实时渲染
              //print "\n"; print_R($FullResponeTextArrayNotNullLine); print "\n";
              if(ob_get_level() > 0) ob_flush();
              flush();
              // 从缓冲区中移除已处理的部分
              $buffer = substr($buffer, strpos($buffer, $matches[0]) + strlen($matches[0]));
          }

          return strlen($data);
        },
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $CURLOPT_POSTFIELDS,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $API_KEY
        ),
    ));
    curl_exec($curl);
    curl_close($curl);
    ob_flush();
    flush();

}



/*

if($data != "[DONE]" && $data != "")   {
$Content = '';
try {
	$JsonArray = (array)json_decode($data, true);
	if (isset($JsonArray['choices']) && is_array($JsonArray['choices'])) {
		foreach ($JsonArray['choices'] as $Item) {
			if (isset($Item['delta']['content'])) {
				$Content .= $Item['delta']['content'];
			}
		}
	}
	$ContentArray = ['text' => $Content];
	$Content = json_encode($ContentArray);
} catch (Exception $Error) {
	error_log("Error processing JSON data: " . $Error->getMessage());
}
echo $Content;
ob_flush();
flush();
return strlen($Content);
}

*/
?>
