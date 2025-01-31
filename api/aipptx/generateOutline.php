<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING); 
require_once('config.inc.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken, Token");
header("Content-type: text/html; charset=utf-8");
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$_POST = json_decode(file_get_contents("php://input"), true);

$_POST['action']  = 'stream';
//$_POST['subject'] = '如何使用AI来生成PPTX';

if($_POST['action'] == 'stream' && $_POST['subject'] != '')   {

    $subject = $_POST['subject'];

    $promptText = "
        请为“".$subject."”生成一个详细的PPT大纲, 涵盖内容请根据topic提供的信息生成一份与时俱进的完美的ppt大纲。

        大纲应包含主要 6 个大的章节，每个章节下面要求有 3-5 个子章节，每个子章节进一步细分为 3 个小节, 不要生成 4 个小节。小节的数量应根据主题的复杂性灵活调整, 最多不超过6个。

        如果“".$subject."”里面有要求子章节和小点的数量，请根据要求生成对应的子章节数量和小点数量。

        格式要求：

        生成一份PPT的大纲, 以行业总结性报告的形式显现。
        示例：

        1.1 标题名称
        1.1.1 简短描述要点1的内容。
        1.1.2 简短描述要点2的内容。
        1.1.3 简短描述要点3的内容。

        只需要精确到1.1.1就可以,不需要扩充到1.1.1.1这样的四级结构.

        只输出必要的数据, 不需要输出跟大纲无关的内容, 输出的结果以Markdown的格式输出。

        不需要输出总结性的文本。

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
            echo $data;
            if(ob_get_level() > 0) ob_flush();
            flush();
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
CURLOPT_WRITEFUNCTION => function($curl, $data) use (&$FullResponeText) {
    // 使用正则表达式提取 "content":"..." 部分的内容
    if (preg_match('/"content":"([^"]*)"/', $data, $matches)) {
      $outputData = $matches[1];
      $FullResponeText .= $outputData;
      echo 'data: {"status":3,"text":"'.$outputData.'"}'."\n\r";
      ob_flush();
      flush();
      return strlen($data);
    }
    else {
      echo $FullResponeText;
    }
    return strlen($data);
},
*/

?>
