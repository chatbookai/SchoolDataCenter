<?php
require_once('config.inc.php');

$HTTP_ORIGIN    = $_SERVER['HTTP_ORIGIN'];
$allowedOrigins = ['http://localhost:3000']; // 允许的域名列表

if (in_array($HTTP_ORIGIN, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $HTTP_ORIGIN);
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, token");

// 处理 OPTIONS 请求
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// 设置 Content-Type 为 text/event-stream
header("Content-type: text/event-stream; charset=utf-8");
header('Cache-Control: no-cache');



$_POST = json_decode(file_get_contents("php://input"), true);
print_R(json_encode($_POST));

if($_POST['templateId'] != '' && $_POST['asyncGenPptx'] == true)   {
    
    $outlineMarkdown    = $_POST['outlineMarkdown'];
    $templateId         = $_POST['templateId'];

    $promptText = "
        $outlineMarkdown

        上述结构解释说明
        #开头的表示PPTX的标题
        ##开头的表示PPTX的某个章节
        ###表示的是某个章节下面的PPTX的页面信息,统计有多少个###开头的行,就代码整个PPTX有多少个页面.

        针对对以上PPTX的大纲信息数据处理的要求:
        1 把以上PPTX的大纲信息进行每小节的内容进行解释和扩充,扩充后的内容要求在10-30个字之间. 比如对1.1.1进行扩充, 只需要扩充一句,不需要做二次扩充. 如果已经是1.1.1.1这样四级结构,则不需要扩充,按原有值输出即可.
        2 分别对上述PPTX大纲进行按每页进行内容解释和扩充.
        3 当遇到1.1  或 1.2 之类的结构,表示就是新的一页的开始部分, 紧接着下面的内容也是这一页的内容.以此区别每页内容,并在每页之间输出一个JSON结构{counter:1, type: '内容'}
        4 在封面下面生成一个目录页面, 在目录中不需要显示额外的#, 然后输出一个JSON结构 {counter:2, type: '目录'}
        5 在章节的后面生成一个单独的章节标签, 在单独的章节标签里不需要显示额外的#, 然后输出一个JSON结构 {counter:3, type: '章节'}

        下面是一个例子:

        # AI to PPTX

        ## 1. AI技术概述

        ### 1.1 AI的基本概念
        1.1.1 AI的定义与历史发展
        1.1.2 AI的主要分支与应用领域
        1.1.3 AI技术的核心算法与模型

        ### 1.2 AI在PPT制作中的应用
        1.2.1 AI生成PPT的原理与技术
        1.2.2 AI在PPT设计中的自动化工具
        1.2.3 AI提升PPT内容质量的案例分析

        ### 1.3 AI与PPT的未来趋势
        1.3.1 AI技术在PPT制作中的前沿应用
        1.3.2 AI与PPT用户体验的优化
        1.3.3 AI对PPT行业的影响与挑战

        ## 2. AI生成PPTX的技术实现

        ### 2.1 数据处理与内容生成
        2.1.1 数据收集与预处理技术
        2.1.2 自然语言处理（NLP）在PPT内容生成中的应用
        2.1.3 内容生成模型的选择与优化

        ### 2.2 设计自动化
        2.2.1 设计元素的自动生成与布局
        2.2.2 色彩与风格的智能匹配
        2.2.3 设计一致性与品牌识别的保持

        ### 2.3 输出与优化
        2.3.1 PPTX文件格式的生成与导出
        2.3.2 输出质量的自动检测与优化
        2.3.3 用户反馈与持续改进机制

        ## 3. AI to PPTX的商业应用与市场分析

        ### 3.1 市场现状与需求分析
        3.1.1 AI在PPT制作市场的渗透率
        3.1.2 主要用户群体的需求分析
        3.1.3 市场竞争格局与主要玩家

        ### 3.2 商业应用案例
        3.2.1 企业内部的PPT自动化解决方案
        3.2.2 教育与培训领域的应用案例
        3.2.3 营销与广告行业的创新应用

        ### 3.3 未来市场预测与投资机会
        3.3.1 AI to PPTX市场的增长预测
        3.3.2 关键技术与市场驱动因素
        3.3.3 投资机会与风险分析 

        根据例子,需要输出以下格式的内容:
        # AI to PPTX
        {counter:1, type: '封面'}

        ## 1. AI技术概述
        ## 2. AI生成PPTX的技术实现
        ## 3. AI to PPTX的商业应用与市场分析
        {counter:2, type: '目录'}

        ## 1. AI技术概述
        {counter:3, type: '章节'}

        ### 1.1 AI的基本概念
        1.1.1 AI的定义与历史发展
        1.1.2 AI的主要分支与应用领域
        1.1.3 AI技术的核心算法与模型
        {counter:4, type: '内容'}

        ### 1.2 AI在PPT制作中的应用
        1.2.1 AI生成PPT的原理与技术
        1.2.2 AI在PPT设计中的自动化工具
        1.2.3 AI提升PPT内容质量的案例分析
        {counter:5, type: '内容'}

        ### 1.3 AI与PPT的未来趋势
        1.3.1 AI技术在PPT制作中的前沿应用
        1.3.2 AI与PPT用户体验的优化
        1.3.3 AI对PPT行业的影响与挑战
        {counter:6, type: '内容'}

        ## 2. AI生成PPTX的技术实现
        {counter:7, type: '章节'}

        ### 2.1 数据处理与内容生成
        2.1.1 数据收集与预处理技术
        2.1.2 自然语言处理（NLP）在PPT内容生成中的应用
        2.1.3 内容生成模型的选择与优化
        {counter:8, type: '内容'}

        ### 2.2 设计自动化
        2.2.1 设计元素的自动生成与布局
        2.2.2 色彩与风格的智能匹配
        2.2.3 设计一致性与品牌识别的保持
        {counter:9, type: '内容'}

        ### 2.3 输出与优化
        2.3.1 PPTX文件格式的生成与导出
        2.3.2 输出质量的自动检测与优化
        2.3.3 用户反馈与持续改进机制
        {counter:10, type: '内容'}

        ## 3. AI to PPTX的商业应用与市场分析
        {counter:11, type: '章节'}

        ### 3.1 市场现状与需求分析
        3.1.1 AI在PPT制作市场的渗透率
        3.1.2 主要用户群体的需求分析
        3.1.3 市场竞争格局与主要玩家
        {counter:12, type: '内容'}

        ### 3.2 商业应用案例
        3.2.1 企业内部的PPT自动化解决方案
        3.2.2 教育与培训领域的应用案例
        3.2.3 营销与广告行业的创新应用
        {counter:13, type: '内容'}

        ### 3.3 未来市场预测与投资机会
        3.3.1 AI to PPTX市场的增长预测
        3.3.2 关键技术与市场驱动因素
        3.3.3 投资机会与风险分析 
        {counter:14, type: '内容'}

        请按上述要求进行输入内容.
    ";

    $curl = curl_init();

    $CURLOPT_POSTFIELDS = [
        "model" => $API_MODE,
        "messages" => [
            [
                "role" => "user",
                "content" => $promptText
            ]
        ],
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

    curl_setopt_array($curl, array(
        CURLOPT_URL => $API_URL . '/chat/completions',
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_WRITEFUNCTION => function($curl, $data) {
			if ($data != "[DONE]" && $data != "") {
				// 记录原始数据
				error_log("Raw data: " . $data);

				// 去除可能的 "data: " 前缀
				$data = str_replace('data: ', '', $data);

				// 尝试解码 JSON
				$data2 = json_decode($data, true);

				// 检查解码是否成功
				if (json_last_error() !== JSON_ERROR_NONE) {
					error_log("JSON decode error: " . json_last_error_msg());
					return strlen($data);
				}

				// 重新编码为 JSON
				$data = json_encode($data2);

				// 检查编码是否成功
				if (json_last_error() !== JSON_ERROR_NONE) {
					error_log("JSON encode error: " . json_last_error_msg());
					return strlen($data);
				}

				// 输出数据
				echo 'data: ' . $data;
				ob_flush();
				flush();

				// 返回处理后的数据长度
				return strlen('data: ' . $data);
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