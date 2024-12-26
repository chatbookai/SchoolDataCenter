<?php
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

$_POST = json_decode(file_get_contents("php://input"), true);
//print_R(json_encode($_POST));

$_POST['asyncGenPptx']  = true;
$_POST['templateId']    = "1869467528347127808";

if($_POST['templateId'] != '' && $_POST['asyncGenPptx'] == true)   {

    $outlineMarkdown    = $_POST['outlineMarkdown'];
    $templateId         = $_POST['templateId'];

    $outlineMarkdown    = '# 如何使用AI来生成PPTX - PPT大纲

    ## 1. AI生成PPTX的基础知识

    ### 1.1 AI生成PPTX的定义与背景
    1.1.1 定义AI生成PPTX的概念。
    1.1.2 介绍AI在办公自动化中的应用背景。
    1.1.3 分析PPTX格式在现代办公中的重要性。

    ### 1.2 AI生成PPTX的技术原理
    1.2.1 介绍自然语言处理（NLP）在AI生成PPTX中的作用。
    1.2.2 解释机器学习模型如何生成PPTX内容。
    1.2.3 讨论AI如何处理视觉设计元素。

    ### 1.3 AI生成PPTX的优势与挑战
    1.3.1 列举AI生成PPTX的主要优势。
    1.3.2 分析AI生成PPTX面临的技术挑战。
    1.3.3 讨论AI生成PPTX对传统PPT制作的影响。

    ## 2. 使用AI生成PPTX的实践指南

    ### 2.1 选择合适的AI工具
    2.1.1 介绍市场上主流的AI生成PPTX工具。
    2.1.2 分析各工具的功能特点与适用场景。
    2.1.3 提供选择工具的评估标准。

    ### 2.2 输入数据与内容准备
    2.2.1 解释如何准备输入数据以供AI处理。
    2.2.2 讨论内容结构化的重要性。
    2.2.3 提供内容准备的实用技巧。

    ### 2.3 AI生成PPTX的流程
    2.3.1 详细描述AI生成PPTX的步骤。
    2.3.2 解释如何调整和优化生成的PPTX。
    2.3.3 讨论如何进行最终的审查与修改。

    ## 3. AI生成PPTX的未来发展

    ### 3.1 AI生成PPTX的技术趋势
    3.1.1 预测AI生成PPTX技术的未来发展方向。
    3.1.2 讨论AI与人类设计师的协作模式。
    3.1.3 分析AI生成PPTX在不同行业的应用潜力。

    ### 3.2 用户体验与界面设计
    3.2.1 探讨AI工具用户界面的设计原则。
    3.2.2 分析用户体验对AI生成PPTX的影响。
    3.2.3 提供优化用户体验的建议。

    ### 3.3 数据安全与隐私保护
    3.3.1 讨论AI生成PPTX过程中的数据安全问题。
    3.3.2 分析隐私保护在AI工具中的重要性。
    3.3.3 提供数据安全与隐私保护的实践指南。';

    $result = $redis->hSet("PPTX_OUTLINE_".date('Ymd'), $templateId, $outlineMarkdown);

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

    $redis->hSet("PPTX_CONTENT_".date('Ymd'), $templateId, json_encode(['data'=>'', 'total'=>0, 'current'=>0, 'finished'=>false]));
    print 'data: {"current":1, "pptId":"'.$templateId.'", "status":3, "text":"", "total":'.$TotalPagesNumber.'}'."\n\n";

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
        CURLOPT_WRITEFUNCTION => function($curl, $data) use (&$FullResponeText, &$分段结构输出情况, &$templateId, &$TotalPagesNumber, &$redis) {
          static $buffer = '';  // 用于存储不完整的数据块
          $buffer .= $data;     // 将当前数据块追加到缓冲区

          // 检查是否包含结束标记 [DONE]
          if (strpos($buffer, '[DONE]') !== false) {
            // 输出最终的 FullResponeText
            //print "Final FullResponeText: $FullResponeText\n";
            $Result           = [];
            $Result['result'] = Markdown_To_Generate_Content_Json($FullResponeText);
            print 'data: {"current":'.$TotalPagesNumber.', "pptId":"'.$templateId.'", "status":3, "text":"## ", "total":'.$TotalPagesNumber.'}'."\n\n";
            print "data: ".json_encode($Result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n\n";
            $redis->hSet("PPTX_CONTENT_".date('Ymd'), $templateId, json_encode(['data'=>$FullResponeText, 'total'=>$TotalPagesNumber, 'current'=>$TotalPagesNumber, 'finished'=>true]));

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
              if(substr($LastElement1, 0, 3) == '## ') {
                //print_R($LastElement1);
                //print_R($FullResponeTextArrayNotNullLine);
                //分段结构只需要输出一次即可, 为实现这个目标, 需要加一个输出标记
                $LastElement2 = array_pop($FullResponeTextArrayNotNullLine);
                $分段结构标记 = md5($LastElement2);
                if(!in_array($分段结构标记, $分段结构输出情况))  {
                  $CurrentPage      = sizeof($分段结构输出情况) + 2;
                  print 'data: {"current":'.$CurrentPage.', "pptId":"'.$templateId.'", "status":3, "text":"## ", "total":'.$TotalPagesNumber.'}'."\n\n";
                  $分段结构输出情况[] = $分段结构标记;
                  $redis->hSet("PPTX_CONTENT_".date('Ymd'), $templateId, json_encode(['data'=>$FullResponeText, 'total'=>$TotalPagesNumber, 'current'=>$CurrentPage, 'finished'=>false]));
                }
              }
              if(substr($LastElement1, 0, 4) == '### ') {
                //print_R($LastElement1);
                $LastElement2 = array_pop($FullResponeTextArrayNotNullLine);
                if(substr($LastElement2, 0, 3) == '## ') {
                  //上一个元素是一个二级标题 当前是一个大的章节的第一个页面时
                  //分段结构只需要输出一次即可, 为实现这个目标, 需要加一个输出标记
                  $分段结构标记 = md5($LastElement2);
                  if(!in_array($分段结构标记, $分段结构输出情况))  {
                    $CurrentPage      = sizeof($分段结构输出情况) + 2;
                    print 'data: {"current":'.$CurrentPage.', "pptId":"'.$templateId.'", "status":3, "text":"### ", "total":'.$TotalPagesNumber.'}'."\n\n";
                    $分段结构输出情况[] = $分段结构标记;
                    $redis->hSet("PPTX_CONTENT_".date('Ymd'), $templateId, json_encode(['data'=>$FullResponeText, 'total'=>$TotalPagesNumber, 'current'=>$CurrentPage, 'finished'=>false]));
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
                      print 'data: {"current":'.$CurrentPage.', "pptId":"'.$templateId.'", "status":3, "text":"", "total":'.$TotalPagesNumber.'}'."\n\n";
                      $分段结构输出情况[] = $分段结构标记;
                      $redis->hSet("PPTX_CONTENT_".date('Ymd'), $templateId, json_encode(['data'=>$FullResponeText, 'total'=>$TotalPagesNumber, 'current'=>$CurrentPage, 'finished'=>false]));
                    }
                  }
                }
                //print_R($FullResponeTextArrayNotNullLine);
              }
              //后续需要实现统计出有多少页PPTX, 然后需要标记当前页码, 从而实现实时渲染
              //print "\n"; print_R($FullResponeTextArrayNotNullLine); print "\n";
              ob_flush();
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
