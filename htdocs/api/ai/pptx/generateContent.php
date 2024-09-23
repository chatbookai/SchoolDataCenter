<?php
require_once('config.inc.php');

$HTTP_ORIGIN    = $_SERVER['HTTP_ORIGIN'];
if (in_array($HTTP_ORIGIN, $allowedOrigins)) {
    header("Access-Control-Allow-Origin:" . $HTTP_ORIGIN);
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken");
header("Content-type: text/html; charset=utf-8");
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$_POST = json_decode(file_get_contents("php://input"), true);

if($_POST['action'] == 'stream' && $_POST['asyncGenPptx'] == true)   {
    
    $outlineMarkdown    = $_POST['outlineMarkdown'];
    $templateId         = $_POST['templateId'];

    $promptText = "
        $outlineMarkdown

        上述结构解释说明
        #开头的表示整个PPTX的标题
        ##开头的表示PPTX下面的章节
        ###表示的是某个章节下面的PPTX的页面信息,统计有多少个###开头的行,就代码整个PPTX有多少个页面.

        把以上PPTX的大纲信息进行每小节的内容进行解释和扩充,扩充后的内容要求在10-30个字之间. 比如对1.1.1进行扩充, 只需要扩充一句,不需要做二次扩充. 如果已经是1.1.1.1这样四级结构,则不需要扩充,按原有值输出即可.

        分别对上述PPTX大纲进行按每页进行内容解释和扩充.
        当遇到1.1  或 1.2 之类的结构,表示就是新的一页.
        需要在这一页结尾处输出对应页面的JSON格式的数据,例如: {\"current\":1,\"total\":需要替换为当前PPTX大纲所记录的总页面数量}, 其中current为当前PPTX的第几页, totaol为所有PPTX总页面的数量.
        当遇到1.1.1 或 1.2.1之类的结构,表示就是一个功能描述点,不需要输出JSON结构.

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
        $Temp = str_replace('data: ', '', $data);
        $JsonData = @json_decode($Temp, true);
        $content = @$JsonData['choices'][0]['delta']['content'];
        echo $content; 
        ob_flush();
        flush();
        return strlen($content);
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

?>