<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('config.inc.php');
require_once('adodb5/adodb.inc.php');
require_once("vendor/autoload.php");


function 中学生心理健康量表_心理健康AiDeepSeek测评($测评名称, $用户名='', $学号='') {
    global $db;
    if($用户名!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 用户名='$用户名'";
    }
    else if($学号!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 学号='$学号'";
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $rs_a           = (array)$rs->GetArray();
    foreach($rs_a as $测评结果) {
        $测评分析   = json_decode(base64_decode($测评结果['测评分析']), true);
        $系统模板   = "要求你做一个针对中学生的心理咨询老师,根据学生做出的心理测评题目做出有针对性的解释说明,然后再给出一份综合性的论述和结论报告.
        ----------------------------
        心理健康测评说明:
        ".$测评分析['测评说明']. "
        ----------------------------
        心理健康评分标准:
        ----------------------------
        ". $测评分析['评分标准']."
        ----------------------------
        要求:
        1 根据学生做出的心理测评题目做出有针对性的解释说明
        2 总计有10个测评因子,分别针对每个测评因子给出相应的总结说明
        3 给出一份500字左右的综合性论述
        4 给出一份500字左右的结论报告
        5 给出一份500字左右的建议和改进措施,在改进措施中,要增加出去旅游,吃吨好吃的,或是去电影院看个大片之类的,如果家里面经费紧张,去郊区转转,或是坐个公交车欣赏一下城市风景也可以.
        ";
        //print $系统模板;
        $因子分析 = (array)$测评分析['因子分析'];
        $用户输入 = "";
        foreach($因子分析 as $因子分析项目) {
            $名称   = $因子分析项目['名称'];
            $因子解释 = $因子分析项目['因子解释'];
            $测评分数 = $因子分析项目['测评分数'];
            $因子记录 = "我在测评因子为: ".$名称." 的项目中,平均得分为: ".$测评分数.", 以下是我在该因子中的测评明细:\n";
            foreach((array)$因子分析项目['测评明细'] as $测评记录) {
                $测评项目 = $测评记录['测评项目'];
                $测评选项 = $测评记录['测评选项'];
                $测评分值 = $测评记录['测评分值'];
                $因子记录 .= "测评项目为:".$测评项目.", 我选了:".$测评选项." 得分为:".$测评分值."\n";
            }
            $用户输入 .= $因子记录."\n---------------\n";
        }
        //print $用户输入;

        $结果 = DeepSeekAiChat($系统模板, $用户输入);
        if($结果 != "") {
            $sql = "update data_xinlijiankang_cepingresult set DeepSeek = '".base64_encode($结果)."' where id = '".$测评结果['id']."' ";
            $db->Execute($sql);
            $sql = "insert into data_xinlijiankang_airecords (学号,姓名,班级,用户名,测评名称,测评时间,AI模型,AI结果)
            values('".$测评结果['学号']."','".$测评结果['姓名']."','".$测评结果['班级']."','".$测评结果['用户名']."','$测评名称','".$测评结果['测评时间']."','DeepSeek','".base64_encode($结果)."');";
            $db->Execute($sql);
        }

    }
}

function 中学生心理健康量表_中小学生心理健康量表MHTAiDeepSeek测评($测评名称, $用户名='', $学号='') {
    global $db;
    if($用户名!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 用户名='$用户名'";
    }
    else if($学号!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 学号='$学号'";
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $rs_a           = (array)$rs->GetArray();
    foreach($rs_a as $测评结果) {
        $测评分析   = json_decode(base64_decode($测评结果['测评分析']), true);
        $系统模板   = "要求你做一个针对中学生的心理咨询老师,根据学生做出的心理测评题目做出有针对性的解释说明,然后再给出一份综合性的论述和结论报告.
        ----------------------------
        心理健康测评说明:
        ".$测评分析['测评说明']. "
        ----------------------------
        心理健康评分标准:
        ----------------------------
        ". $测评分析['评分标准']."
        ----------------------------
        要求:
        1 根据学生做出的心理测评题目做出有针对性的解释说明
        2 总计有8个测评因子,分别针对每个测评因子给出相应的总结说明
        3 给出一份500字左右的综合性论述
        4 给出一份500字左右的结论报告
        5 给出一份500字左右的建议和改进措施,在改进措施中,要增加出去旅游,吃吨好吃的,或是去电影院看个大片之类的,如果家里面经费紧张,去郊区转转,或是坐个公交车欣赏一下城市风景也可以.
        ";
        //print $系统模板;
        $因子分析 = (array)$测评分析['因子分析'];
        $用户输入 = "";
        foreach($因子分析 as $因子分析项目) {
            $名称   = $因子分析项目['名称'];
            $因子解释 = $因子分析项目['因子解释'];
            $测评分数 = $因子分析项目['测评分数'];
            $因子记录 = "我在测评因子为: ".$名称." 的项目中,平均得分为: ".$测评分数.", 以下是我在该因子中的测评明细:\n";
            foreach((array)$因子分析项目['测评明细'] as $测评记录) {
                $测评项目 = $测评记录['测评项目'];
                $测评选项 = $测评记录['测评选项'];
                $测评分值 = $测评记录['测评分值'];
                $因子记录 .= "测评项目为:".$测评项目.", 我选了:".$测评选项." 得分为:".$测评分值."\n";
            }
            $用户输入 .= $因子记录."\n---------------\n";
        }
        //print $用户输入;

        $结果 = DeepSeekAiChat($系统模板, $用户输入);
        if($结果 != "") {
            $sql = "update data_xinlijiankang_cepingresult set DeepSeek = '".base64_encode($结果)."' where id = '".$测评结果['id']."' ";
            $db->Execute($sql);
            $sql = "insert into data_xinlijiankang_airecords (学号,姓名,班级,用户名,测评名称,测评时间,AI模型,AI结果)
            values('".$测评结果['学号']."','".$测评结果['姓名']."','".$测评结果['班级']."','".$测评结果['用户名']."','$测评名称','".$测评结果['测评时间']."','DeepSeek','".base64_encode($结果)."');";
            $db->Execute($sql);
        }

    }
}

function 中学生心理健康量表_儿童焦虑性情绪障碍筛查表SCAREDAiDeepSeek测评($测评名称, $用户名='', $学号='') {
    global $db;
    if($用户名!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 用户名='$用户名'";
    }
    else if($学号!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 学号='$学号'";
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $rs_a           = (array)$rs->GetArray();
    foreach($rs_a as $测评结果) {
        $测评分析   = json_decode(base64_decode($测评结果['测评分析']), true);
        $系统模板   = "要求你做一个针对中学生,关于儿童焦虑性情绪障碍筛查方面的心理咨询老师,根据学生做出的心理测评题目做出有针对性的解释说明,然后再给出一份综合性的论述和结论报告.
        ----------------------------
        儿童焦虑性情绪障碍筛查测评说明:
        ".$测评分析['测评说明']. "
        ----------------------------
        儿童焦虑性情绪障碍筛查分标准:
        ----------------------------
        ". $测评分析['评分标准']."
        ----------------------------
        要求:
        1 根据学生做出的儿童焦虑性情绪障碍筛查题目做出有针对性的解释说明
        2 总计有5个测评因子,分别针对每个测评因子给出相应的总结说明
        3 给出一份500字左右的综合性论述
        4 给出一份500字左右的结论报告
        5 给出一份500字左右的建议和改进措施.
        ";
        //print $系统模板;
        $因子分析 = (array)$测评分析['因子分析'];
        $用户输入 = "";
        foreach($因子分析 as $因子分析项目) {
            $名称   = $因子分析项目['名称'];
            $因子解释 = $因子分析项目['因子解释'];
            $测评分数 = $因子分析项目['测评分数'];
            $因子记录 = "我在测评因子为: ".$名称." 的项目中,平均得分为: ".$测评分数.", 以下是我在该因子中的测评明细:\n";
            foreach((array)$因子分析项目['测评明细'] as $测评记录) {
                $测评项目 = $测评记录['测评项目'];
                $测评选项 = $测评记录['测评选项'];
                $测评分值 = $测评记录['测评分值'];
                $因子记录 .= "测评项目为:".$测评项目.", 我选了:".$测评选项." 得分为:".$测评分值."\n";
            }
            $用户输入 .= $因子记录."\n---------------\n";
        }
        //print $用户输入;

        $结果 = DeepSeekAiChat($系统模板, $用户输入);
        if($结果 != "") {
            $sql = "update data_xinlijiankang_cepingresult set DeepSeek = '".base64_encode($结果)."' where id = '".$测评结果['id']."' ";
            $db->Execute($sql);
            $sql = "insert into data_xinlijiankang_airecords (学号,姓名,班级,用户名,测评名称,测评时间,AI模型,AI结果)
            values('".$测评结果['学号']."','".$测评结果['姓名']."','".$测评结果['班级']."','".$测评结果['用户名']."','$测评名称','".$测评结果['测评时间']."','DeepSeek','".base64_encode($结果)."');";
            $db->Execute($sql);
        }

    }
}

function 中学生心理健康量表_症状自评量表SCL90AiDeepSeek测评($测评名称, $用户名='', $学号='') {
    global $db;
    if($用户名!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 用户名='$用户名'";
    }
    else if($学号!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 学号='$学号'";
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $rs_a           = (array)$rs->GetArray();
    foreach($rs_a as $测评结果) {
        $测评分析   = json_decode(base64_decode($测评结果['测评分析']), true);
        $系统模板   = "
        要求你做为专业的心理咨询医师,针对症状自评量表 (The self-report symptom inventory，Symptom checklist，90，简称 SCL90)的记录做出有针对性的解释说明,然后再给出一份综合性的论述和结论报告.
        ----------------------------
        症状自评量表说明:
        ".$测评分析['测评说明']. "
        ----------------------------
        症状自评量表评分标准:
        ----------------------------
        ". $测评分析['评分标准']."
        ----------------------------
        要求:
        1 根据用户做出的心理测评题目做出有针对性的解释说明
        2 总计有10个测评因子,分别针对每个测评因子给出相应的总结说明
        3 给出一份500字左右的综合性论述
        4 给出一份500字左右的结论报告
        5 给出一份500字左右的建议和改进措施,在改进措施中,要增加出去旅游,吃吨好吃的,或是去电影院看个大片之类的,如果家里面经费紧张,去郊区转转,或是坐个公交车欣赏一下城市风景也可以.
        ----------------------------\n
        ";
        //print $系统模板;
        $因子分析 = (array)$测评分析['因子分析'];
        $用户输入 = "";
        foreach($因子分析 as $因子分析项目) {
            $名称   = $因子分析项目['名称'];
            $因子解释 = $因子分析项目['因子解释'];
            $测评分数 = $因子分析项目['测评分数'];
            $因子记录 = "我在测评因子为: ".$名称." 的项目中,平均得分为: ".$测评分数.", 以下是我在该因子中的测评明细:\n";
            foreach((array)$因子分析项目['测评明细'] as $测评记录) {
                $测评项目 = $测评记录['测评项目'];
                $测评选项 = $测评记录['测评选项'];
                $测评分值 = $测评记录['测评分值'];
                $因子记录 .= "
                测评项目为:".$测评项目.", 我选了:".$测评选项." 得分为:".$测评分值."\n";
            }
            $用户输入 .= $因子记录."\n---------------\n";
        }
        //print $用户输入; exit;

        $结果 = DeepSeekAiChat($系统模板, $用户输入);
        if($结果 != "") {
            $sql = "update data_xinlijiankang_cepingresult set DeepSeek = '".base64_encode($结果)."' where id = '".$测评结果['id']."' ";
            $db->Execute($sql);
            $sql = "insert into data_xinlijiankang_airecords (学号,姓名,班级,用户名,测评名称,测评时间,AI模型,AI结果)
            values('".$测评结果['学号']."','".$测评结果['姓名']."','".$测评结果['班级']."','".$测评结果['用户名']."','$测评名称','".$测评结果['测评时间']."','DeepSeek','".base64_encode($结果)."');";
            $db->Execute($sql);
        }

    }
}

function 中学生心理健康量表_中学生学科兴趣测评AiDeepSeek测评($测评名称, $用户名='', $学号='') {
    global $db;
    if($用户名!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 用户名='$用户名'";
    }
    else if($学号!="") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称 = '$测评名称' and 学号='$学号'";
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $rs_a           = (array)$rs->GetArray();
    foreach($rs_a as $测评结果) {
        $测评分析   = json_decode(base64_decode($测评结果['测评分析']), true);
        $系统模板   = "
        要求你做为一个学校专业学科咨询顾问,针对学生所做的题目,判断当前学生针对每一个科学的兴趣和爱好的程度,做出有针对性的解释说明,然后再给出一份综合性的论述,告诉学生哪一个学科是学生最喜爱的.
        ----------------------------
        中学生学科兴趣测评表说明:
        ".$测评分析['测评说明']. "
        ----------------------------
        中学生学科兴趣测评表评分标准:
        ----------------------------
        ". $测评分析['评分标准']."
        ----------------------------
        要求:
        1 根据学生做出的每一个学科的爱好程度,做出有针对性的解释说明
        2 总计有12个学科,分别针对每个学科给出相应的总结说明
        3 给出一份500字左右的综合性论述
        4 给出一份500字左右的结论报告, 告诉学生哪一个学科是学生最喜爱的
        ----------------------------\n
        ";
        //print $系统模板;
        $因子分析 = (array)$测评分析['因子分析'];
        $用户输入 = "";
        foreach($因子分析 as $因子分析项目) {
            $名称   = $因子分析项目['名称'];
            $因子解释 = $因子分析项目['因子解释'];
            $测评分数 = $因子分析项目['测评分数'];
            $因子记录 = "我在学科为: ".$名称." 的项目中,平均得分为: ".$测评分数.", 以下是我在该学科中的测评明细:\n";
            foreach((array)$因子分析项目['测评明细'] as $测评记录) {
                $测评项目 = $测评记录['测评项目'];
                $测评选项 = $测评记录['测评选项'];
                $测评分值 = $测评记录['测评分值'];
                $因子记录 .= "
                测评项目为:".$测评项目.", 我选了:".$测评选项." 得分为:".$测评分值."\n";
            }
            $用户输入 .= $因子记录."\n---------------\n";
        }
        //print $用户输入; exit;

        $结果 = DeepSeekAiChat($系统模板, $用户输入);
        if($结果 != "") {
            $sql = "update data_xinlijiankang_cepingresult set DeepSeek = '".base64_encode($结果)."' where id = '".$测评结果['id']."' ";
            $db->Execute($sql);
            $sql = "insert into data_xinlijiankang_airecords (学号,姓名,班级,用户名,测评名称,测评时间,AI模型,AI结果)
            values('".$测评结果['学号']."','".$测评结果['姓名']."','".$测评结果['班级']."','".$测评结果['用户名']."','$测评名称','".$测评结果['测评时间']."','DeepSeek','".base64_encode($结果)."');";
            $db->Execute($sql);
        }

    }
}

function DeepSeekAiChat($系统模板, $用户输入) {

    $APIKEY     = "sk-af19f867c995411e82414d0f74ff74c5";
    $curl = curl_init();
    $messages = [];
    $messages[] = ['content'=>$系统模板, 'role'=>'system'];
    $messages[] = ['content'=>$用户输入, 'role'=>'user'];
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
        "stream": false,
        "temperature": 1,
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
    $response = curl_exec($curl);
    curl_close($curl);
    $JSON = json_decode($response, true);
    return $JSON['choices'][0]['message']['content'];
    //echo $response;
}

?>
