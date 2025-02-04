<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken");
header("Content-type: text/html; charset=utf-8");
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../include.inc.php');

//CheckAuthUserLoginStatus();

$USER_ID        = $GLOBAL_USER->USER_ID;

$payload        = file_get_contents('php://input');
$_POST          = json_decode($payload,true);

$temperature    = 0.5;
$questionCounts = (array)$_POST['questionCounts'];
$difficulties   = (array)$_POST['difficulties'];
$requirements   = (string)$_POST['requirements'];
$grade          = (string)$_POST['grade'];
$textbook       = (string)$_POST['textbook'];
$课程名称        = (string)$_POST['课程名称'];
$班级名称        = (string)$_POST['班级名称'];
$学期名称        = (string)$_POST['学期名称'];



$用户输入        = "你需要按以下要求, 生成一些用于中小学生课堂测验或是用于考试的题目.
课程: ".$课程名称."
教材: ".$textbook."
年级: ".$grade."
题目的要求: ".$requirements."
要求单选题目数量: ".(int)$questionCounts['single']." 要求单选题目难度: ".$difficulties['single']." 要求输出: 题目,选项,正确答案,答案解析 四个部分.
要求多选题目数量: ".(int)$questionCounts['single']." 要求多选题目难度: ".$difficulties['single']." 要求输出: 题目,选项,正确答案,答案解析 四个部分.
要求判断题目数量: ".(int)$questionCounts['single']." 要求判断题目难度: ".$difficulties['single']." 要求输出: 题目,选项,正确答案,答案解析 四个部分.
要求填空题目数量: ".(int)$questionCounts['single']." 要求填空题目难度: ".$difficulties['single']." 要求输出: 题目,正确答案,答案解析 三个部分.
要求问答题目数量: ".(int)$questionCounts['single']." 要求问答题目难度: ".$difficulties['single']." 要求输出: 题目,正确答案,答案解析 三个部分.

每个题目生成一个知识点, 知识点要在50-100字左右.
给每一个题目增加一个分值, 单选题目和多选题目和判断题目分值小一些, 填空题目分值适当多一些, 问答题目分值可以大一些, 要求所有题目的分值加起来要等于100
问答题的知识点和答案解析, 要求内容在50-100字左右.

生成的题目示例如下, 不要输出其它无用的信息:
### 单选题
1. **题目**: 下列哪个字是“日”字的正确写法？
   - A. 曰
   - B. 目
   - C. 日
   - D. 月
   **正确答案**: C
   **答案解析**: “日”字的正确写法是“日”，其他选项分别是“曰”、“目”和“月”。
   **知识点**: 该题的知识点
   **分值**: 2

2. **题目**: 下列哪个词是“太阳”的同义词？
   - A. 月亮
   - B. 星星
   - C. 阳光
   - D. 云朵
   **正确答案**: C
   **答案解析**: “阳光”是“太阳”的同义词，其他选项分别是“月亮”、“星星”和“云朵”。
   **知识点**: 该题的知识点
   **分值**: 2

3. **题目**: 下列哪个字是“水”字的正确写法？
   - A. 氵
   - B. 水
   - C. 氺
   - D. 氷
   **正确答案**: B
   **答案解析**: “水”字的正确写法是“水”，其他选项分别是“氵”、“氺”和“氷”。
   **知识点**: 该题的知识点
   **分值**: 2

### 多选题
1. **题目**: 下列哪些字是“木”字的偏旁？
   - A. 林
   - B. 森
   - C. 树
   - D. 花
   **正确答案**: A, B, C
   **答案解析**: “林”、“森”和“树”都包含“木”字的偏旁，而“花”不包含。
   **知识点**: 该题的知识点
   **分值**: 2

2. **题目**: 下列哪些词是“春天”的同义词？
   - A. 春季
   - B. 夏日
   - C. 秋日
   - D. 冬雪
   **正确答案**: A
   **答案解析**: “春季”是“春天”的同义词，其他选项分别是“夏日”、“秋日”和“冬雪”。
   **知识点**: 该题的知识点
   **分值**: 2

3. **题目**: 下列哪些字是“火”字的偏旁？
   - A. 炎
   - B. 热
   - C. 灯
   - D. 冰
   **正确答案**: A, B, C
   **答案解析**: “炎”、“热”和“灯”都包含“火”字的偏旁，而“冰”不包含。
   **知识点**: 该题的知识点
   **分值**: 2

### 判断题
1. **题目**: “山”字的正确写法是“山”。
   - A. 正确
   - B. 错误
   **正确答案**: A
   **答案解析**: “山”字的正确写法确实是“山”。
   **知识点**: 该题的知识点
   **分值**: 2

2. **题目**: “月亮”是“太阳”的同义词。
   - A. 正确
   - B. 错误
   **正确答案**: B
   **答案解析**: “月亮”不是“太阳”的同义词，它们是不同的天体。
   **知识点**: 该题的知识点
   **分值**: 2

3. **题目**: “水”字的偏旁是“氵”。
   - A. 正确
   - B. 错误
   **正确答案**: A
   **答案解析**: “水”字的偏旁确实是“氵”。
   **知识点**: 该题的知识点
   **分值**: 2

### 填空题
1. **题目**: “日”字的正确写法是______。
   **正确答案**: 日
   **答案解析**: “日”字的正确写法是“日”。
   **知识点**: 该题的知识点
   **分值**: 2

2. **题目**: “春天”的同义词是______。
   **正确答案**: 春季
   **答案解析**: “春天”的同义词是“春季”。
   **知识点**: 该题的知识点
   **分值**: 2

3. **题目**: “火”字的偏旁是______。
   **正确答案**: 火
   **答案解析**: “火”字的偏旁是“火”。
   **知识点**: 该题的知识点
   **分值**: 2

### 问答题
1. **题目**: 请写出“山”字的正确写法。
   **正确答案**: 山
   **答案解析**: “山”字的正确写法是“山”。
   **知识点**: 该题的知识点
   **分值**: 2

2. **题目**: 请写出“春天”的同义词。
   **正确答案**: 春季
   **答案解析**: “春天”的同义词是“春季”。
   **知识点**: 该题的知识点
   **分值**: 2

3. **题目**: 请写出“水”字的偏旁。
   **正确答案**: 氵
   **答案解析**: “水”字的偏旁是“氵”。
   **知识点**: 该题的知识点
   **分值**: 2

";
$历史消息       = [];
$AppModel      = "DeepSeekChat";
$AppName       = "AI出题";
if((int)$questionCounts['single'] >= 0 && $课程名称 != "" && $grade != "" && $textbook != "")  {
  $SystemPrompt   = "
  要求你扮演为一位中小学老师, 为指定的课程, 出一些可以用于考试或是测验的题目.
  返回的数据格式要求为 Markdown 格式. 不要在结果中输出 \n.";
  switch($AppModel) {
    case 'DeepSeekChat':
      //实时输出结果, 返回结果的JSON不要做解析, 放到客户端进行解析.
      //print $用户输入;
      DeepSeekAiChat($SystemPrompt, $用户输入, $历史消息, $temperature, $AppName, $备注);
      break;
  }
  exit;
}

function DeepSeekAiChat($系统模板, $用户输入, $历史消息, $temperature, $AppName, $备注)     {
  global $APIKEY;
  $curl 		  = curl_init();
  $messages 	= [];
  $messages[] = ['content'=>$系统模板, 'role'=>'system'];
  foreach($历史消息 as $消息) {
    $过滤AI回复文本 = str_replace("\\\\\\n", "\n", $消息[1]);
    $过滤AI回复文本 = str_replace("\\\\n", "\n", $过滤AI回复文本);
    $过滤AI回复文本 = str_replace("\\n", "\n", $过滤AI回复文本);
    $messages[] = ['content'=>$消息[0], 'role'=>'user'];
    $messages[] = ['content'=>$过滤AI回复文本, 'role'=>'assistant'];
  }
  $messages[] = ['content'=>$用户输入, 'role'=>'user'];
  //print_R($messages);exit;
  curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.deepseek.com/chat/completions',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
      "messages": '.json_encode($messages).',
      "model": "deepseek-chat",
      "frequency_penalty": 0,
      "max_tokens": 2048,
      "presence_penalty": 0,
      "stop": null,
      "stream": true,
      "temperature": '.$temperature.',
      "top_p": 1,
      "logprobs": false,
      "top_logprobs": null
      }',
      CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Accept: application/json',
          'Authorization: Bearer ' . $APIKEY
      ),
  ));

  $输出TEXT = "";
  curl_setopt($curl, CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$AppName, &$用户输入, &$输出TEXT) {
    print $data;
    ob_flush();
    flush();

    static $buffer = ''; // 用于存储不完整的数据块
    $buffer .= $data; // 将当前数据块追加到缓冲区
    while (preg_match('/"content":"([^"]*)"/', $buffer, $matches)) {
        $outputData = $matches[1];
        $输出TEXT .= $outputData;
        //echo $outputData;
        //ob_flush();
        //flush();
        // 从缓冲区中移除已处理的部分
        $buffer = substr($buffer, strpos($buffer, $matches[0]) + strlen($matches[0]));
    }
    if(strpos($data, "data: [DONE]") !== false)  {
      解析题目文本到数据库($输出TEXT);
    }
    return strlen($data);
  });

  curl_exec($curl);

  if (curl_errno($curl)) {
    echo 'Error: ' . curl_error($curl);
  }

  curl_close($curl);

}


function 解析题目文本到数据库($题目文本) {
  global $db, $学期名称;
  $年级   = (string)$_POST['grade'];
  $教材   = (string)$_POST['textbook'];
  $课程   = (string)$_POST['课程名称'];
  $班级   = (string)$_POST['班级名称'];

  $题目文本Array  = explode("\\n", $题目文本);

  //清空已经题库 学期,班级,课程,题干
  if(sizeof($题目文本Array)>10) {
    $sql = "delete from data_exam_question where 学期='$学期名称' and 班级='$班级' and 课程='$课程'";
    $db->Execute($sql);
  }

  //插入新的题库
  for($i=0;$i<sizeof($题目文本Array);$i++)  {
    $Line = trim($题目文本Array[$i]);
    if(substr($Line, 0, 4) == '### ') {
      $题目类型 = substr($Line, 4, strlen($Line));
    }

    $LineArray = explode("**题目**:", $Line);
    if($LineArray[1] != "") {
      $题目标题 = trim($LineArray[1]);
      $A = $B = $C = $D = $E = $F = $正确答案 = $答案解析 = $知识点 = '';
    }

    $LineArray = explode("- A.", $Line);
    if($LineArray[1] != "") {
      $A = trim($LineArray[1]);
    }

    $LineArray = explode("- B.", $Line);
    if($LineArray[1] != "") {
      $B = trim($LineArray[1]);
    }

    $LineArray = explode("- C.", $Line);
    if($LineArray[1] != "") {
      $C = trim($LineArray[1]);
    }

    $LineArray = explode("- D.", $Line);
    if($LineArray[1] != "") {
      $D = trim($LineArray[1]);
    }

    $LineArray = explode("- E.", $Line);
    if($LineArray[1] != "") {
      $E = trim($LineArray[1]);
    }

    $LineArray = explode("- F.", $Line);
    if($LineArray[1] != "") {
      $F = trim($LineArray[1]);
    }

    $LineArray = explode("**正确答案**:", $Line);
    if($LineArray[1] != "") {
      $正确答案 = trim($LineArray[1]);
    }

    $LineArray = explode("**答案解析**:", $Line);
    if($LineArray[1] != "") {
      $答案解析 = trim($LineArray[1]);
    }

    $LineArray = explode("**知识点**:", $Line);
    if($LineArray[1] != "") {
      $知识点 = trim($LineArray[1]);
    }

    $LineArray = explode("**分值**:", $Line);
    if($LineArray[1] != "" && $班级 != "" && $课程 != "") {
      $分值     = trim($LineArray[1]);
      $Element  = [];
      $Element['类型'] = $题目类型;
      $Element['题干'] = $题目标题;
      $Element['A'] = $A;
      $Element['B'] = $B;
      $Element['C'] = $C;
      $Element['D'] = $D;
      $Element['E'] = $E;
      $Element['F'] = $F;
      $Element['答案']      = addslashes($正确答案);
      $Element['解析']      = addslashes($答案解析);
      $Element['知识点']    = addslashes($知识点);
      $Element['难度']      = addslashes($难度);
      $Element['题库分类']  = addslashes($题库分类);
      $Element['分值']      = addslashes($分值);
      $Element['学期']      = addslashes($学期名称);
      $Element['班级']      = addslashes($班级);
      $Element['课程']      = addslashes($课程);
      $Element['教材']      = addslashes($教材);
      //print_R($Element);
      [$rs,$sql] = InsertOrUpdateTableByArray("data_exam_question",$Element,'学期,班级,课程,题干',0); //,'Insert'
      print_R($rs->EOF);print_R($sql);
    }

  }
  //print_R($题目文本Array); exit;
}

?>
