<?php

function 测评选项转分值($测评名称,$测评选项) {
    $心理健康选项['中学生心理健康量表(MSSMHS)']['无']   = 0;
    $心理健康选项['中学生心理健康量表(MSSMHS)']['轻度'] = 1;
    $心理健康选项['中学生心理健康量表(MSSMHS)']['中度'] = 2;
    $心理健康选项['中学生心理健康量表(MSSMHS)']['偏重'] = 3;
    $心理健康选项['中学生心理健康量表(MSSMHS)']['严重'] = 4;

    $心理健康选项['症状自评量表(SCL-90)']['无']   = 0;
    $心理健康选项['症状自评量表(SCL-90)']['轻度'] = 1;
    $心理健康选项['症状自评量表(SCL-90)']['中度'] = 2;
    $心理健康选项['症状自评量表(SCL-90)']['偏重'] = 3;
    $心理健康选项['症状自评量表(SCL-90)']['严重'] = 4;

    $心理健康选项['中学生学科兴趣测评']['很符合自己的情况']   = 5;
    $心理健康选项['中学生学科兴趣测评']['比较符合自己的情况'] = 4;
    $心理健康选项['中学生学科兴趣测评']['很难说'] = 3;
    $心理健康选项['中学生学科兴趣测评']['较不符合自己的情况'] = 2;
    $心理健康选项['中学生学科兴趣测评']['很不符合自己的情况'] = 1;

    $心理健康选项['明尼苏达多项人格测验(MMPI)-399']['是']   = 1;
    $心理健康选项['明尼苏达多项人格测验(MMPI)-399']['否']   = 0;
    $心理健康选项['明尼苏达多项人格测验(MMPI)-566']['是']   = 1;
    $心理健康选项['明尼苏达多项人格测验(MMPI)-566']['否']   = 0;

    $心理健康选项['中小学生心理健康量表(MHT)']['是']   = 1;
    $心理健康选项['中小学生心理健康量表(MHT)']['否']   = 0;

    $心理健康选项['儿童焦虑性情绪障碍筛查表(SCARED)']['无']     = 0;
    $心理健康选项['儿童焦虑性情绪障碍筛查表(SCARED)']['有时']   = 1;
    $心理健康选项['儿童焦虑性情绪障碍筛查表(SCARED)']['经常']   = 2;


    return $心理健康选项[$测评名称][$测评选项];
}

function 中学生心理健康量表_根据测评明细分析测评结果($测评名称) {
    global $db;
    global $GLOBAL_USER;

    $sql            = "SELECT * FROM `data_xinlijiankang_ceping` where 测评名称='$测评名称'";
    $rs             = $db->Execute($sql);
    $测评信息       = $rs->fields;

    $强迫症状LIST   = [3,10,12,22,23,48];
    $偏执LIST       = [11,20,24,26,47,49];
    $敌对LIST       = [19,21,25,50,52,58];
    $人际关系紧张与敏感LIST = [4,17,18,45,51,59];
    $抑郁LIST       = [5,13,14,16,44,57];
    $焦虑LIST       = [6,15,34,43,46,56];
    $学习压力LIST   = [31,33,36,38,40,55];
    $适应不良LIST   = [1,8,9,29,39,41];
    $情绪不平衡LIST = [2,7,27,32,35,53];
    $心理不平衡LIST = [28,30,37,42,54,60];

    $量表解释               = [];
    $量表解释['强迫症状']   = "反映受试者做作业必须反复检查，反复数数，总是在想一些不必要的事情，总害怕考试成绩不理想等强迫症状。";
    $量表解释['偏执']       = "反映受试者觉得别人占自己便宜，在背后议论自己，对多数人不相信，别人对自己评价不当，别人跟自己作对等偏执问题。";
    $量表解释['敌对']       = "反映受试者控制不住自己的脾气，经常与别人争论，易激动，有想摔东西的冲动等。";
    $量表解释['人际关系紧张与敏感'] = "反映受试者觉得别人不理解自己，对自己不友好，感情容易受到伤害，对别人求全责备，同异性在一起觉得不自在等问题。";
    $量表解释['抑郁']       = "反映受试者感到生活单调，自己没有前途，容易哭泣，责备自己，无精打采等问题。";
    $量表解释['焦虑']       = "反映受试者感到紧张，心神不宁，无缘无故的害怕，心里烦躁，心里不踏实等问题。";
    $量表解释['学习压力']   = "反映受试者感到学习负担重，怕老师提问，讨厌做作业，讨厌上学，害怕和讨厌考试等问题。";
    $量表解释['适应不良']   = "反映受试者对学校生活不适应，不愿意参加课外活动，不适应教师教学方法，不适应家里的学习环境等。";
    $量表解释['情绪不平衡'] = "反映受试者情绪不稳定，对老师和同学以及父母态度多变，学习成绩忽高忽低的问题。";
    $量表解释['心理不平衡'] = "反映受试者感到老师和父母对自己不公平，对同学比自己成绩好感到难过和不服气等问题。";

    $sql            = "SELECT * FROM `data_xinlijiankang_cepingxuanxiang` where 测评名称='$测评名称' limit 0, 300";
    $rs             = $db->Execute($sql);
    $测评选项RSA     = (array)$rs->GetArray();
    $测评选项转序号   = [];
    foreach($测评选项RSA AS $测评选项RS) {
        $测评选项转序号[$测评选项RS['测评项目']] = $测评选项RS['序号'];
    }

    $Element    = [];
    $用户名     = $GLOBAL_USER->USER_ID;
    $学号       = $GLOBAL_USER->学号;
    if($GLOBAL_USER->type == "User") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 用户名='$用户名'";
        $Element['用户名'] = $用户名;
        $Element['姓名'] = $GLOBAL_USER->USER_NAME;
    }
    else if($GLOBAL_USER->type == "Student") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 学号='$学号'";
        $Element['学号'] = $学号;
        $Element['姓名'] = $GLOBAL_USER->姓名;
        $Element['班级'] = $GLOBAL_USER->班级;
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $测评明细RSA     = (array)$rs->GetArray();
    $测评分数 = 0;
    $强迫症状VALUE = $偏执VALUE = $敌对VALUE = $人际关系紧张与敏感VALUE = $抑郁VALUE = 0;
    $焦虑VALUE = $学习压力VALUE = $适应不良VALUE = $情绪不平衡VALUE = $心理不平衡VALUE = 0;
    $强迫症状Array = $偏执Array = $敌对Array = $人际关系紧张与敏感Array = $抑郁Array = [];
    $焦虑Array = $学习压力Array = $适应不良Array = $情绪不平衡Array = $心理不平衡Array = [];
    foreach($测评明细RSA AS $测评明细) {
        $测评分数 += $测评明细['测评分值'];
        $序号 = $测评选项转序号[$测评明细['测评项目']];
        if($序号>0 && in_array($序号, $强迫症状LIST)) {
            $强迫症状VALUE += $测评明细['测评分值'];
            $强迫症状Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $偏执LIST)) {
            $偏执VALUE += $测评明细['测评分值'];
            $偏执Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $敌对LIST)) {
            $敌对VALUE += $测评明细['测评分值'];
            $敌对Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $人际关系紧张与敏感LIST)) {
            $人际关系紧张与敏感VALUE += $测评明细['测评分值'];
            $人际关系紧张与敏感Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $抑郁LIST)) {
            $抑郁VALUE += $测评明细['测评分值'];
            $抑郁Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $焦虑LIST)) {
            $焦虑VALUE += $测评明细['测评分值'];
            $焦虑Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $学习压力LIST)) {
            $学习压力VALUE += $测评明细['测评分值'];
            $学习压力Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $适应不良LIST)) {
            $适应不良VALUE += $测评明细['测评分值'];
            $适应不良Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $情绪不平衡LIST)) {
            $情绪不平衡VALUE += $测评明细['测评分值'];
            $情绪不平衡Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $心理不平衡LIST)) {
            $心理不平衡VALUE += $测评明细['测评分值'];
            $心理不平衡Array[] = $测评明细;
        }
    }
    $测评分析 = [];
    $测评分析['强迫症状'] = number_format($强迫症状VALUE/6, 1);
    $测评分析['偏执'] = number_format($偏执VALUE/6, 1);
    $测评分析['敌对'] = number_format($敌对VALUE/6, 1);
    $测评分析['人际关系紧张与敏感'] = number_format($人际关系紧张与敏感VALUE/6, 1);
    $测评分析['抑郁'] = number_format($抑郁VALUE/6, 1);
    $测评分析['焦虑'] = number_format($焦虑VALUE/6, 1);
    $测评分析['学习压力'] = number_format($学习压力VALUE/6, 1);
    $测评分析['适应不良'] = number_format($适应不良VALUE/6, 1);
    $测评分析['情绪不平衡'] = number_format($情绪不平衡VALUE/6, 1);
    $测评分析['心理不平衡'] = number_format($心理不平衡VALUE/6, 1);
    $Element['测评名称'] = $测评明细['测评名称'];
    $Element['测试项目数量'] = sizeof($测评明细RSA);
    $Element['测评分数'] = $测评分数;
    $Element['测评时间'] = Date('Y-m-d H:i:s');

    $我的测评记录 = [];
    $我的测评记录['强迫症状Array'] = $强迫症状Array;
    $我的测评记录['偏执Array'] = $偏执Array;
    $我的测评记录['敌对Array'] = $敌对Array;
    $我的测评记录['人际关系紧张与敏感Array'] = $人际关系紧张与敏感Array;
    $我的测评记录['抑郁Array'] = $抑郁Array;
    $我的测评记录['焦虑Array'] = $焦虑Array;
    $我的测评记录['学习压力Array'] = $学习压力Array;
    $我的测评记录['适应不良Array'] = $适应不良Array;
    $我的测评记录['情绪不平衡Array'] = $情绪不平衡Array;
    $我的测评记录['心理不平衡Array'] = $心理不平衡Array;

    $因子分析 = [];
    $因子分析[] = [ '名称'=>'强迫症状', '测评分数'=>$测评分析['强迫症状'], '因子解释'=>$量表解释['强迫症状'], '测评明细'=> $强迫症状Array ];
    $因子分析[] = [ '名称'=>'偏执', '测评分数'=>$测评分析['偏执'], '因子解释'=>$量表解释['偏执'], '测评明细'=> $偏执Array ];
    $因子分析[] = [ '名称'=>'敌对', '测评分数'=>$测评分析['敌对'], '因子解释'=>$量表解释['敌对'], '测评明细'=> $敌对Array ];
    $因子分析[] = [ '名称'=>'人际关系紧张与敏感', '测评分数'=>$测评分析['人际关系紧张与敏感'], '因子解释'=>$量表解释['人际关系紧张与敏感'], '测评明细'=> $人际关系紧张与敏感Array ];
    $因子分析[] = [ '名称'=>'抑郁', '测评分数'=>$测评分析['抑郁'], '因子解释'=>$量表解释['抑郁'], '测评明细'=> $抑郁Array ];
    $因子分析[] = [ '名称'=>'焦虑', '测评分数'=>$测评分析['焦虑'], '因子解释'=>$量表解释['焦虑'], '测评明细'=> $焦虑Array ];
    $因子分析[] = [ '名称'=>'学习压力', '测评分数'=>$测评分析['学习压力'], '因子解释'=>$量表解释['学习压力'], '测评明细'=> $学习压力Array ];
    $因子分析[] = [ '名称'=>'适应不良', '测评分数'=>$测评分析['适应不良'], '因子解释'=>$量表解释['适应不良'], '测评明细'=> $适应不良Array ];
    $因子分析[] = [ '名称'=>'情绪不平衡', '测评分数'=>$测评分析['情绪不平衡'], '因子解释'=>$量表解释['情绪不平衡'], '测评明细'=> $情绪不平衡Array ];
    $因子分析[] = [ '名称'=>'心理不平衡', '测评分数'=>$测评分析['心理不平衡'], '因子解释'=>$量表解释['心理不平衡'], '测评明细'=> $心理不平衡Array ];

    $输出报告 = [];
    $输出报告['测评说明'] = $测评信息['测评说明'];
    $输出报告['评分标准'] = $测评信息['评分标准'];
    $输出报告['因子分析'] = $因子分析;
    $输出报告['测评分析'] = $测评分析;
    $Element['测评分析'] = base64_encode(json_encode($输出报告));
    if($GLOBAL_USER->type == "User") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,用户名,测评时间', 0);
    }
    else if($GLOBAL_USER->type == "Student") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,学号,测评时间', 0);
    }
}

function 症状自评量表SCL90($测评名称) {
    global $db;
    global $GLOBAL_USER;

    $sql            = "SELECT * FROM `data_xinlijiankang_ceping` where 测评名称='$测评名称'";
    $rs             = $db->Execute($sql);
    $测评信息       = $rs->fields;

    $躯体化LIST     = [1,4,12,27,40,42,48,49,52,53,56,58];
    $强迫症状LIST   = [3,9,10,28,38,45,46,51,55,65];
    $人际关系敏感LIST   = [6,21,34,36,37,41,61,69,73];
    $抑郁LIST       = [5,14,15,20,22,26,29,30,31,32,54,71,79];
    $焦虑LIST       = [2,17,23,33,39,57,72,78,80,86];
    $敌意LIST       = [11,24,63,67,74,81];
    $恐怖LIST       = [13,25,47,50,70,75,82];
    $偏执LIST       = [8,18,43,68,76,83];
    $精神病性LIST       = [7,16,35,62,77,84,85,87,88,90];
    $其他LIST   = [19,44,59,60,64,66,89];

    $量表解释                   = [];
    $量表解释['躯体化']         = "该因子主要反映主观的身体不适感，包括心血管、肠胃道、呼吸道系统主诉不适和头痛、脊痛、肌肉酸痛、以及焦虑的其他躯体表现。";
    $量表解释['强迫症状']       = "该因子主要指那种明知没有必要，但又无法摆脱的无意义的思想、冲动、行为等表现，还有一些比较一般的感知障碍(如脑子变空了，“记忆力不行”等)也在这一因子中反映。";
    $量表解释['人际关系敏感']   = "该因子主要是反映某些个人不自在感与自卑感，尤其是在与其他人相比较时更为突出。自卑感、懊丧、以及在人事关系明显相处不好的人，往往这一因子得高分。";
    $量表解释['抑郁']           = "反映的是临床上忧郁症状群相联系的广泛的概念。忧郁苦闷的感情和心境是代表性症状，它还以对生活的兴趣减退，缺乏活动的愿望、丧失活动力等为特征，并包括失望、悲叹、与忧郁相联系的其它感知及躯体方面的问题。";
    $量表解释['焦虑']           = "包括一些通常临床上明显与焦虑症状相联系的症状与体验。一般指那些无法静息、神经过敏、紧张、以及由此产生躯体征象 (如震颤) 。那种游离不定的焦虑及惊恐发作是本因子的主要内容，它还包括有一个反映“解体”的项目。";
    $量表解释['敌意']           = "主要以三方面来反映病人的人际关系敏感表现、思想、感情及行为。包括从厌烦、争论、摔物、直至争斗和不可抑制的冲动暴发等各个方面。";
    $量表解释['恐怖']           = "与传统的恐怖状态所反映的内容基本一致，恐惧的对象包括出门旅行，空旷场地、人群、或公共场合及交通工具。此外还有反映社交恐怖的项目。";
    $量表解释['偏执']           = "偏执是一个十分复杂的概念，本因子只是包括了它的一些基本内容，主要是指思维方面，  如投射性思维，人际关系敏感、猜疑、关系妄想、忘想、被动体验和夸大等。";
    $量表解释['精神病性']       = "其中有幻想、思维播散、被控制感、思维被插入等反映精神分裂症择定状项目。";
    $量表解释['其它']           = "该因子是反映睡眠及饮食情况的。";

    $sql            = "SELECT * FROM `data_xinlijiankang_cepingxuanxiang` where 测评名称='$测评名称' limit 0, 300";
    $rs             = $db->Execute($sql);
    $测评选项RSA     = (array)$rs->GetArray();
    $测评选项转序号   = [];
    foreach($测评选项RSA AS $测评选项RS) {
        $测评选项转序号[$测评选项RS['测评项目']] = $测评选项RS['序号'];
    }

    $Element    = [];
    $用户名     = $GLOBAL_USER->USER_ID;
    $学号       = $GLOBAL_USER->学号;
    if($GLOBAL_USER->type == "User") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 用户名='$用户名'";
        $Element['用户名'] = $用户名;
        $Element['姓名'] = $GLOBAL_USER->USER_NAME;
    }
    else if($GLOBAL_USER->type == "Student") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 学号='$学号'";
        $Element['学号'] = $学号;
        $Element['姓名'] = $GLOBAL_USER->姓名;
        $Element['班级'] = $GLOBAL_USER->班级;
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $测评明细RSA     = (array)$rs->GetArray();
    $测评分数 = 0;
    $阳性项目数 = 0;
    $阴性项目数 = 0;
    $躯体化VALUE = $强迫症状VALUE = $人际关系敏感VALUE = $抑郁VALUE = $其他VALUE = 0;
    $焦虑VALUE = $敌意VALUE = $恐怖VALUE = $偏执VALUE = $精神病性VALUE = 0;
    $躯体化Array = $强迫症状Array = $人际关系敏感Array = $抑郁Array = $其他Array = [];
    $焦虑Array = $敌意Array = $恐怖Array = $偏执Array = $精神病性Array = [];
    $总累计得分 = 0;
    foreach($测评明细RSA AS $测评明细) {
        $测评分数 += $测评明细['测评分值'];
        $总累计得分 += $测评明细['测评分值'];
        if($测评明细['测评分值'] == 1) {
            $阴性项目数 += 1;
        }

        $序号 = $测评选项转序号[$测评明细['测评项目']];
        if($序号>0 && in_array($序号, $躯体化LIST)) {
            $躯体化VALUE += $测评明细['测评分值'];
            $躯体化Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $强迫症状LIST)) {
            $强迫症状VALUE += $测评明细['测评分值'];
            $强迫症状Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $人际关系敏感LIST)) {
            $人际关系敏感VALUE += $测评明细['测评分值'];
            $人际关系敏感Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $抑郁LIST)) {
            $抑郁VALUE += $测评明细['测评分值'];
            $抑郁Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $焦虑LIST)) {
            $焦虑VALUE += $测评明细['测评分值'];
            $焦虑Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $敌意LIST)) {
            $敌意VALUE += $测评明细['测评分值'];
            $敌意Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $恐怖LIST)) {
            $恐怖VALUE += $测评明细['测评分值'];
            $恐怖Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $偏执LIST)) {
            $偏执VALUE += $测评明细['测评分值'];
            $偏执Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $精神病性LIST)) {
            $精神病性VALUE += $测评明细['测评分值'];
            $精神病性Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $其他LIST)) {
            $其他VALUE += $测评明细['测评分值'];
            $其他Array[] = $测评明细;
        }
    }

    $测评分析 = [];
    $测评分析['躯体化'] = number_format($躯体化VALUE/sizeof($躯体化LIST), 1);
    $测评分析['强迫症状'] = number_format($强迫症状VALUE/sizeof($强迫症状LIST), 1);
    $测评分析['人际关系敏感'] = number_format($人际关系敏感VALUE/sizeof($人际关系敏感LIST), 1);
    $测评分析['抑郁'] = number_format($抑郁VALUE/sizeof($抑郁LIST), 1);
    $测评分析['焦虑'] = number_format($焦虑VALUE/sizeof($焦虑LIST), 1);
    $测评分析['敌意'] = number_format($敌意VALUE/sizeof($敌意LIST), 1);
    $测评分析['恐怖'] = number_format($恐怖VALUE/sizeof($恐怖LIST), 1);
    $测评分析['偏执'] = number_format($偏执VALUE/sizeof($偏执LIST), 1);
    $测评分析['精神病性'] = number_format($精神病性VALUE/sizeof($精神病性LIST), 1);
    $测评分析['其他'] = number_format($其他VALUE/sizeof($其他LIST), 1);

    $得分换算表 = [];
    $得分换算表['躯体化'] = ['项目编号'=>$躯体化LIST, '累计得分'=>$躯体化VALUE, 'T分数'=>$测评分析['躯体化']];
    $得分换算表['强迫症状'] = ['项目编号'=>$强迫症状LIST, '累计得分'=>$强迫症状VALUE, 'T分数'=>$测评分析['强迫症状']];
    $得分换算表['人际关系敏感'] = ['项目编号'=>$人际关系敏感LIST, '累计得分'=>$人际关系敏感VALUE, 'T分数'=>$测评分析['人际关系敏感']];
    $得分换算表['抑郁'] = ['项目编号'=>$抑郁LIST, '累计得分'=>$抑郁VALUE, 'T分数'=>$测评分析['抑郁']];
    $得分换算表['焦虑'] = ['项目编号'=>$焦虑LIST, '累计得分'=>$焦虑VALUE, 'T分数'=>$测评分析['焦虑']];
    $得分换算表['敌意'] = ['项目编号'=>$敌意LIST, '累计得分'=>$敌意VALUE, 'T分数'=>$测评分析['敌意']];
    $得分换算表['恐怖'] = ['项目编号'=>$恐怖LIST, '累计得分'=>$恐怖VALUE, 'T分数'=>$测评分析['恐怖']];
    $得分换算表['偏执'] = ['项目编号'=>$偏执LIST, '累计得分'=>$偏执VALUE, 'T分数'=>$测评分析['偏执']];
    $得分换算表['精神病性'] = ['项目编号'=>$精神病性LIST, '累计得分'=>$精神病性VALUE, 'T分数'=>$测评分析['精神病性']];
    $得分换算表['其他'] = ['项目编号'=>$其他LIST, '累计得分'=>$其他VALUE, 'T分数'=>$测评分析['其他']];

    $Element['测评名称'] = $测评明细['测评名称'];
    $Element['测试项目数量'] = sizeof($测评明细RSA);
    $Element['测评分数'] = $测评分数;
    $Element['测评时间'] = Date('Y-m-d H:i:s');

    $我的测评记录 = [];
    $我的测评记录['躯体化Array'] = $躯体化Array;
    $我的测评记录['强迫症状Array'] = $强迫症状Array;
    $我的测评记录['人际关系敏感Array'] = $人际关系敏感Array;
    $我的测评记录['抑郁Array'] = $抑郁Array;
    $我的测评记录['焦虑Array'] = $焦虑Array;
    $我的测评记录['敌意Array'] = $敌意Array;
    $我的测评记录['恐怖Array'] = $恐怖Array;
    $我的测评记录['偏执Array'] = $偏执Array;
    $我的测评记录['精神病性Array'] = $精神病性Array;
    $我的测评记录['其他Array'] = $其他Array;

    $因子分析 = [];
    $因子分析[] = [ '名称'=>'躯体化', '测评分数'=>$测评分析['躯体化'], '因子解释'=>$量表解释['躯体化'], '测评明细'=> $躯体化Array ];
    $因子分析[] = [ '名称'=>'强迫症状', '测评分数'=>$测评分析['强迫症状'], '因子解释'=>$量表解释['强迫症状'], '测评明细'=> $强迫症状Array ];
    $因子分析[] = [ '名称'=>'人际关系敏感', '测评分数'=>$测评分析['人际关系敏感'], '因子解释'=>$量表解释['人际关系敏感'], '测评明细'=> $人际关系敏感Array ];
    $因子分析[] = [ '名称'=>'抑郁', '测评分数'=>$测评分析['抑郁'], '因子解释'=>$量表解释['抑郁'], '测评明细'=> $抑郁Array ];
    $因子分析[] = [ '名称'=>'焦虑', '测评分数'=>$测评分析['焦虑'], '因子解释'=>$量表解释['焦虑'], '测评明细'=> $焦虑Array ];
    $因子分析[] = [ '名称'=>'敌意', '测评分数'=>$测评分析['敌意'], '因子解释'=>$量表解释['敌意'], '测评明细'=> $敌意Array ];
    $因子分析[] = [ '名称'=>'恐怖', '测评分数'=>$测评分析['恐怖'], '因子解释'=>$量表解释['恐怖'], '测评明细'=> $恐怖Array ];
    $因子分析[] = [ '名称'=>'偏执', '测评分数'=>$测评分析['偏执'], '因子解释'=>$量表解释['偏执'], '测评明细'=> $偏执Array ];
    $因子分析[] = [ '名称'=>'精神病性', '测评分数'=>$测评分析['精神病性'], '因子解释'=>$量表解释['精神病性'], '测评明细'=> $精神病性Array ];
    $因子分析[] = [ '名称'=>'其他', '测评分数'=>$测评分析['其他'], '因子解释'=>$量表解释['其他'], '测评明细'=> $其他Array ];

    $输出报告 = [];
    $输出报告['测评说明'] = $测评信息['测评说明'];
    $输出报告['评分标准'] = $测评信息['评分标准'];
    $输出报告['因子分析'] = $因子分析;
    $输出报告['测评分析'] = $测评分析;
    $输出报告['得分换算表'] = $得分换算表;
    $输出报告['总累计得分'] = $总累计得分;
    $输出报告['阴性项目数'] = $阴性项目数;
    $输出报告['阳性项目数'] = 90 - $阴性项目数;
    $输出报告['总因子分数'] = array_sum(array_values($测评分析));

    $Element['测评分析'] = base64_encode(json_encode($输出报告));
    if($GLOBAL_USER->type == "User") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,用户名,测评时间', 0);
    }
    else if($GLOBAL_USER->type == "Student") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,学号,测评时间', 0);
    }

}

function 中学生学科兴趣测评($测评名称) {
    global $db;
    global $GLOBAL_USER;

    $sql            = "SELECT * FROM `data_xinlijiankang_ceping` where 测评名称='$测评名称'";
    $rs             = $db->Execute($sql);
    $测评信息       = $rs->fields;

    $地理LIST       = [1, 13, 25, 37, 49, 61, 73, 85, 97];
    $外语LIST       = [2, 14, 26, 38, 50, 62, 74, 86, 98];
    $历史LIST       = [3, 15, 27, 39, 51, 63, 75, 87, 99];
    $数学LIST       = [4, 16, 28, 40, 52, 64, 76, 88, 100];
    $政治LIST       = [5, 17, 29, 41, 53, 65, 77, 89, 101];
    $美术LIST       = [6, 18, 30, 42, 54, 66, 78, 90, 102];
    $音乐LIST       = [7, 19, 31, 43, 55, 67, 79, 91, 103];
    $语文LIST       = [8, 20, 32, 44, 56, 68, 80, 92, 104];
    $生物LIST       = [9, 21, 33, 45, 57, 69, 81, 93, 105];
    $物理LIST       = [10, 22, 34, 46, 58, 70, 82, 94, 106];
    $体育LIST       = [11, 23, 35, 47, 59, 71, 83, 95, 107];
    $化学LIST       = [12, 24, 36, 48, 60, 72, 84, 96, 108];

    $量表解释                   = [];
    $量表解释['地理']           = "";
    $量表解释['外语']           = "";
    $量表解释['历史']           = "";
    $量表解释['数学']           = "";
    $量表解释['政治']           = "";
    $量表解释['美术']           = "";
    $量表解释['音乐']           = "";
    $量表解释['语文']           = "";
    $量表解释['生物']           = "";
    $量表解释['物理']           = "";
    $量表解释['体育']           = "";
    $量表解释['化学']           = "";

    $sql            = "SELECT * FROM `data_xinlijiankang_cepingxuanxiang` where 测评名称='$测评名称' limit 0, 300";
    $rs             = $db->Execute($sql);
    $测评选项RSA     = (array)$rs->GetArray();
    $测评选项转序号   = [];
    foreach($测评选项RSA AS $测评选项RS) {
        $测评选项转序号[$测评选项RS['测评项目']] = $测评选项RS['序号'];
    }

    $Element    = [];
    $用户名     = $GLOBAL_USER->USER_ID;
    $学号       = $GLOBAL_USER->学号;
    if($GLOBAL_USER->type == "User") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 用户名='$用户名'";
        $Element['用户名'] = $用户名;
        $Element['姓名'] = $GLOBAL_USER->USER_NAME;
    }
    else if($GLOBAL_USER->type == "Student") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 学号='$学号'";
        $Element['学号'] = $学号;
        $Element['姓名'] = $GLOBAL_USER->姓名;
        $Element['班级'] = $GLOBAL_USER->班级;
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $测评明细RSA     = (array)$rs->GetArray();
    $测评分数 = 0;
    $阳性项目数 = 0;
    $阴性项目数 = 0;
    $地理VALUE = $外语VALUE = $历史VALUE = $数学VALUE = $物理VALUE = 0;
    $政治VALUE = $美术VALUE = $音乐VALUE = $语文VALUE = $生物VALUE = 0;
    $体育VALUE = $化学VALUE = 0;
    $地理Array = $外语Array = $历史Array = $数学Array = $物理Array = [];
    $政治Array = $美术Array = $音乐Array = $语文Array = $生物Array = [];
    $体育Array = $化学Array = [];
    $总累计得分 = 0;
    foreach($测评明细RSA AS $测评明细) {
        $测评分数 += $测评明细['测评分值'];
        $总累计得分 += $测评明细['测评分值'];
        if($测评明细['测评分值'] == 1) {
            $阴性项目数 += 1;
        }

        $序号 = $测评选项转序号[$测评明细['测评项目']];
        if($序号>0 && in_array($序号, $地理LIST)) {
            $地理VALUE += $测评明细['测评分值'];
            $地理Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $外语LIST)) {
            $外语VALUE += $测评明细['测评分值'];
            $外语Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $历史LIST)) {
            $历史VALUE += $测评明细['测评分值'];
            $历史Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $数学LIST)) {
            $数学VALUE += $测评明细['测评分值'];
            $数学Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $政治LIST)) {
            $政治VALUE += $测评明细['测评分值'];
            $政治Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $美术LIST)) {
            $美术VALUE += $测评明细['测评分值'];
            $美术Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $音乐LIST)) {
            $音乐VALUE += $测评明细['测评分值'];
            $音乐Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $语文LIST)) {
            $语文VALUE += $测评明细['测评分值'];
            $语文Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $生物LIST)) {
            $生物VALUE += $测评明细['测评分值'];
            $生物Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $物理LIST)) {
            $物理VALUE += $测评明细['测评分值'];
            $物理Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $体育LIST)) {
            $体育VALUE += $测评明细['测评分值'];
            $体育Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $化学LIST)) {
            $化学VALUE += $测评明细['测评分值'];
            $化学Array[] = $测评明细;
        }
    }

    $测评分析 = [];
    $测评分析['地理'] = number_format($地理VALUE/sizeof($地理LIST), 1);
    $测评分析['外语'] = number_format($外语VALUE/sizeof($外语LIST), 1);
    $测评分析['历史'] = number_format($历史VALUE/sizeof($历史LIST), 1);
    $测评分析['数学'] = number_format($数学VALUE/sizeof($数学LIST), 1);
    $测评分析['政治'] = number_format($政治VALUE/sizeof($政治LIST), 1);
    $测评分析['美术'] = number_format($美术VALUE/sizeof($美术LIST), 1);
    $测评分析['音乐'] = number_format($音乐VALUE/sizeof($音乐LIST), 1);
    $测评分析['语文'] = number_format($语文VALUE/sizeof($语文LIST), 1);
    $测评分析['生物'] = number_format($生物VALUE/sizeof($生物LIST), 1);
    $测评分析['物理'] = number_format($物理VALUE/sizeof($物理LIST), 1);
    $测评分析['体育'] = number_format($体育VALUE/sizeof($体育LIST), 1);
    $测评分析['化学'] = number_format($化学VALUE/sizeof($化学LIST), 1);

    $得分换算表 = [];
    $得分换算表['地理'] = ['项目编号'=>$地理LIST, '累计得分'=>$地理VALUE, 'T分数'=>$测评分析['地理']];
    $得分换算表['外语'] = ['项目编号'=>$外语LIST, '累计得分'=>$外语VALUE, 'T分数'=>$测评分析['外语']];
    $得分换算表['历史'] = ['项目编号'=>$历史LIST, '累计得分'=>$历史VALUE, 'T分数'=>$测评分析['历史']];
    $得分换算表['数学'] = ['项目编号'=>$数学LIST, '累计得分'=>$数学VALUE, 'T分数'=>$测评分析['数学']];
    $得分换算表['政治'] = ['项目编号'=>$政治LIST, '累计得分'=>$政治VALUE, 'T分数'=>$测评分析['政治']];
    $得分换算表['美术'] = ['项目编号'=>$美术LIST, '累计得分'=>$美术VALUE, 'T分数'=>$测评分析['美术']];
    $得分换算表['音乐'] = ['项目编号'=>$音乐LIST, '累计得分'=>$音乐VALUE, 'T分数'=>$测评分析['音乐']];
    $得分换算表['语文'] = ['项目编号'=>$语文LIST, '累计得分'=>$语文VALUE, 'T分数'=>$测评分析['语文']];
    $得分换算表['生物'] = ['项目编号'=>$生物LIST, '累计得分'=>$生物VALUE, 'T分数'=>$测评分析['生物']];
    $得分换算表['物理'] = ['项目编号'=>$物理LIST, '累计得分'=>$物理VALUE, 'T分数'=>$测评分析['物理']];
    $得分换算表['体育'] = ['项目编号'=>$体育LIST, '累计得分'=>$体育VALUE, 'T分数'=>$测评分析['体育']];
    $得分换算表['化学'] = ['项目编号'=>$化学LIST, '累计得分'=>$化学VALUE, 'T分数'=>$测评分析['化学']];

    $Element['测评名称'] = $测评明细['测评名称'];
    $Element['测试项目数量'] = sizeof($测评明细RSA);
    $Element['测评分数'] = $测评分数;
    $Element['测评时间'] = Date('Y-m-d H:i:s');

    $我的测评记录 = [];
    $我的测评记录['地理Array'] = $地理Array;
    $我的测评记录['外语Array'] = $外语Array;
    $我的测评记录['历史Array'] = $历史Array;
    $我的测评记录['数学Array'] = $数学Array;
    $我的测评记录['政治Array'] = $政治Array;
    $我的测评记录['美术Array'] = $美术Array;
    $我的测评记录['音乐Array'] = $音乐Array;
    $我的测评记录['语文Array'] = $语文Array;
    $我的测评记录['生物Array'] = $生物Array;
    $我的测评记录['物理Array'] = $物理Array;
    $我的测评记录['体育Array'] = $体育Array;
    $我的测评记录['化学Array'] = $化学Array;

    $因子分析 = [];
    $因子分析[] = [ '名称'=>'地理', '测评分数'=>$测评分析['地理'], '因子解释'=>$量表解释['地理'], '测评明细'=> $地理Array ];
    $因子分析[] = [ '名称'=>'外语', '测评分数'=>$测评分析['外语'], '因子解释'=>$量表解释['外语'], '测评明细'=> $外语Array ];
    $因子分析[] = [ '名称'=>'历史', '测评分数'=>$测评分析['历史'], '因子解释'=>$量表解释['历史'], '测评明细'=> $历史Array ];
    $因子分析[] = [ '名称'=>'数学', '测评分数'=>$测评分析['数学'], '因子解释'=>$量表解释['数学'], '测评明细'=> $数学Array ];
    $因子分析[] = [ '名称'=>'政治', '测评分数'=>$测评分析['政治'], '因子解释'=>$量表解释['政治'], '测评明细'=> $政治Array ];
    $因子分析[] = [ '名称'=>'美术', '测评分数'=>$测评分析['美术'], '因子解释'=>$量表解释['美术'], '测评明细'=> $美术Array ];
    $因子分析[] = [ '名称'=>'音乐', '测评分数'=>$测评分析['音乐'], '因子解释'=>$量表解释['音乐'], '测评明细'=> $音乐Array ];
    $因子分析[] = [ '名称'=>'语文', '测评分数'=>$测评分析['语文'], '因子解释'=>$量表解释['语文'], '测评明细'=> $语文Array ];
    $因子分析[] = [ '名称'=>'生物', '测评分数'=>$测评分析['生物'], '因子解释'=>$量表解释['生物'], '测评明细'=> $生物Array ];
    $因子分析[] = [ '名称'=>'物理', '测评分数'=>$测评分析['物理'], '因子解释'=>$量表解释['物理'], '测评明细'=> $物理Array ];
    $因子分析[] = [ '名称'=>'体育', '测评分数'=>$测评分析['体育'], '因子解释'=>$量表解释['体育'], '测评明细'=> $体育Array ];
    $因子分析[] = [ '名称'=>'化学', '测评分数'=>$测评分析['化学'], '因子解释'=>$量表解释['化学'], '测评明细'=> $化学Array ];

    $输出报告 = [];
    $输出报告['测评说明'] = $测评信息['测评说明'];
    $输出报告['评分标准'] = $测评信息['评分标准'];
    $输出报告['因子分析'] = $因子分析;
    $输出报告['测评分析'] = $测评分析;
    $输出报告['得分换算表'] = $得分换算表;
    $输出报告['总累计得分'] = $总累计得分;
    $输出报告['阴性项目数'] = $阴性项目数;
    $输出报告['阳性项目数'] = 90 - $阴性项目数;
    $输出报告['总因子分数'] = array_sum(array_values($测评分析));

    $Element['测评分析'] = base64_encode(json_encode($输出报告));
    if($GLOBAL_USER->type == "User") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,用户名,测评时间', 0);
    }
    else if($GLOBAL_USER->type == "Student") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,学号,测评时间', 0);
    }

}

function 中小学生心理健康量表MHT($测评名称) {
    global $db;
    global $GLOBAL_USER;

    $sql            = "SELECT * FROM `data_xinlijiankang_ceping` where 测评名称='$测评名称'";
    $rs             = $db->Execute($sql);
    $测评信息       = $rs->fields;

    $学习焦虑LIST       = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
    $对人焦虑LIST       = [16,17,18,19,20,21,22,23,24,25];
    $孤独倾向LIST       = [26,27,28,29,30,31,32,33,34,35,100];
    $自责倾向LIST       = [36,37,38,39,40,41,42,43,44,45];
    $过敏倾向LIST       = [46,47,48,49,50,51,52,53,54,55];
    $身体症状LIST       = [56,57,58,59,60,61,62,63,64,65,66,67,68,69,70];
    $恐怖倾向LIST       = [71,72,73,74,75,76,77,78,79,80];
    $冲动倾向LIST       = [81,83,85,87,89,91,93,95,97,99];

    $量表解释                   = [];
    $量表解释['学习焦虑']       = "高分(8分以上)：对考试怀有恐惧心理，无法安心学习，十分关心考试分数。这类被试必须接受为他制定的有针对性的特别指导计划。低分(3分以下)：学习焦虑低，学习不会受到困扰，能正确对待考试成绩。";
    $量表解释['对人焦虑']       = "高分(8分以上)：过分注重自己的形象，害怕与人交往，退缩。这类被试必须接受为他制定的有针对性的特别指导计划。低分(3分以下)：热情，大方，容易结交朋友。";
    $量表解释['孤独倾向']       = "高分(8分以上)：孤独、抑郁，不善与人交往，自我封闭。这类被试必须接受为他制定的有针对性的特别指导计划。低分(3分以下)：爱好社交，喜欢寻求刺激，喜欢与他人在一起。";
    $量表解释['自责倾向']       = "高分(8分以上)：自卑，常怀疑自己的能力，常将失败、过失归咎于自己。这类被试必须接受为他制定的有针对性的特别指导计划。低分(3分以下)：自信，能正确看待失败。";
    $量表解释['过敏倾向']   = "高分(8分以上)：过于敏感，容易为一些小事而烦恼。这类被试必须接受为他制定的有针对性的特别指导计划。低分(3分以下)：敏感性较低，能较好地处理日常事物。";
    $量表解释['身体症状']   = "高分(8分以上)：在极度焦虑的时候，会出现呕吐失眠、小便失禁等明显症状。这类被试必须接受为他制定的有针对性的特别指导计划。低分(3分以下)：基本没有身体异常表现。";
    $量表解释['恐怖倾向']   = "高分(8分以上)：对某些门常事物，如黑暗等，有较严重的恐惧感。这类被试必须接受为他制定的有针对性的特别指导计划。低分(3分以下)：基本没有恐怖感。";
    $量表解释['冲动倾向']   = "高分(8分以上)：十分冲动，自制力差。这类被试必须接受为他制定的有针对性的特别指导计划。低分(3分以下)：基本没有冲动。";

    $sql            = "SELECT * FROM `data_xinlijiankang_cepingxuanxiang` where 测评名称='$测评名称' limit 0, 300";
    $rs             = $db->Execute($sql);
    $测评选项RSA     = (array)$rs->GetArray();
    $测评选项转序号   = [];
    foreach($测评选项RSA AS $测评选项RS) {
        $测评选项转序号[$测评选项RS['测评项目']] = $测评选项RS['序号'];
    }

    $Element    = [];
    $用户名     = $GLOBAL_USER->USER_ID;
    $学号       = $GLOBAL_USER->学号;
    if($GLOBAL_USER->type == "User") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 用户名='$用户名'";
        $Element['用户名'] = $用户名;
        $Element['姓名'] = $GLOBAL_USER->USER_NAME;
    }
    else if($GLOBAL_USER->type == "Student") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 学号='$学号'";
        $Element['学号'] = $学号;
        $Element['姓名'] = $GLOBAL_USER->姓名;
        $Element['班级'] = $GLOBAL_USER->班级;
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $测评明细RSA     = (array)$rs->GetArray();
    $测评分数 = 0;
    $学习焦虑VALUE = $对人焦虑VALUE = $孤独倾向VALUE = $过敏倾向VALUE = $自责倾向VALUE = 0;
    $焦虑VALUE = $身体症状VALUE = $恐怖倾向VALUE = $冲动倾向VALUE = $心理不平衡VALUE = 0;
    $学习焦虑Array = $对人焦虑Array = $孤独倾向Array = $过敏倾向Array = $自责倾向Array = [];
    $焦虑Array = $身体症状Array = $恐怖倾向Array = $冲动倾向Array = $心理不平衡Array = [];
    foreach($测评明细RSA AS $测评明细) {
        $测评分数 += $测评明细['测评分值'];
        $序号 = $测评选项转序号[$测评明细['测评项目']];
        if($序号>0 && in_array($序号, $学习焦虑LIST)) {
            $学习焦虑VALUE += $测评明细['测评分值'];
            $学习焦虑Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $对人焦虑LIST)) {
            $对人焦虑VALUE += $测评明细['测评分值'];
            $对人焦虑Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $孤独倾向LIST)) {
            $孤独倾向VALUE += $测评明细['测评分值'];
            $孤独倾向Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $过敏倾向LIST)) {
            $过敏倾向VALUE += $测评明细['测评分值'];
            $过敏倾向Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $自责倾向LIST)) {
            $自责倾向VALUE += $测评明细['测评分值'];
            $自责倾向Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $身体症状LIST)) {
            $身体症状VALUE += $测评明细['测评分值'];
            $身体症状Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $恐怖倾向LIST)) {
            $恐怖倾向VALUE += $测评明细['测评分值'];
            $恐怖倾向Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $冲动倾向LIST)) {
            $冲动倾向VALUE += $测评明细['测评分值'];
            $冲动倾向Array[] = $测评明细;
        }
    }
    $测评分析 = [];
    $测评分析['学习焦虑'] = $学习焦虑VALUE;
    $测评分析['对人焦虑'] = $对人焦虑VALUE;
    $测评分析['孤独倾向'] = $孤独倾向VALUE;
    $测评分析['自责倾向'] = $自责倾向VALUE;
    $测评分析['过敏倾向'] = $过敏倾向VALUE;
    $测评分析['身体症状'] = $身体症状VALUE;
    $测评分析['恐怖倾向'] = $恐怖倾向VALUE;
    $测评分析['冲动倾向'] = $冲动倾向VALUE;
    $Element['测评名称'] = $测评明细['测评名称'];
    $Element['测试项目数量'] = sizeof($测评明细RSA);
    $Element['测评分数'] = $测评分数;
    $Element['测评时间'] = Date('Y-m-d H:i:s');

    $我的测评记录 = [];
    $我的测评记录['学习焦虑Array'] = $学习焦虑Array;
    $我的测评记录['对人焦虑Array'] = $对人焦虑Array;
    $我的测评记录['孤独倾向Array'] = $孤独倾向Array;
    $我的测评记录['过敏倾向Array'] = $过敏倾向Array;
    $我的测评记录['自责倾向Array'] = $自责倾向Array;
    $我的测评记录['身体症状Array'] = $身体症状Array;
    $我的测评记录['恐怖倾向Array'] = $恐怖倾向Array;
    $我的测评记录['冲动倾向Array'] = $冲动倾向Array;

    $因子分析 = [];
    $因子分析[] = [ '名称'=>'学习焦虑', '测评分数'=>$测评分析['学习焦虑'], '因子解释'=>$量表解释['学习焦虑'], '测评明细'=> $学习焦虑Array ];
    $因子分析[] = [ '名称'=>'对人焦虑', '测评分数'=>$测评分析['对人焦虑'], '因子解释'=>$量表解释['对人焦虑'], '测评明细'=> $对人焦虑Array ];
    $因子分析[] = [ '名称'=>'孤独倾向', '测评分数'=>$测评分析['孤独倾向'], '因子解释'=>$量表解释['孤独倾向'], '测评明细'=> $孤独倾向Array ];
    $因子分析[] = [ '名称'=>'过敏倾向', '测评分数'=>$测评分析['过敏倾向'], '因子解释'=>$量表解释['过敏倾向'], '测评明细'=> $过敏倾向Array ];
    $因子分析[] = [ '名称'=>'自责倾向', '测评分数'=>$测评分析['自责倾向'], '因子解释'=>$量表解释['自责倾向'], '测评明细'=> $自责倾向Array ];
    $因子分析[] = [ '名称'=>'身体症状', '测评分数'=>$测评分析['身体症状'], '因子解释'=>$量表解释['身体症状'], '测评明细'=> $身体症状Array ];
    $因子分析[] = [ '名称'=>'恐怖倾向', '测评分数'=>$测评分析['恐怖倾向'], '因子解释'=>$量表解释['恐怖倾向'], '测评明细'=> $恐怖倾向Array ];
    $因子分析[] = [ '名称'=>'冲动倾向', '测评分数'=>$测评分析['冲动倾向'], '因子解释'=>$量表解释['冲动倾向'], '测评明细'=> $冲动倾向Array ];

    $输出报告 = [];
    $输出报告['测评说明'] = $测评信息['测评说明'];
    $输出报告['评分标准'] = $测评信息['评分标准'];
    $输出报告['因子分析'] = $因子分析;
    $输出报告['测评分析'] = $测评分析;
    $Element['测评分析'] = base64_encode(json_encode($输出报告));
    if($GLOBAL_USER->type == "User") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,用户名,测评时间', 0);
    }
    else if($GLOBAL_USER->type == "Student") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,学号,测评时间', 0);
    }
}

function 儿童焦虑性情绪障碍筛查表SCARED($测评名称) {
    global $db;
    global $GLOBAL_USER;

    $sql            = "SELECT * FROM `data_xinlijiankang_ceping` where 测评名称='$测评名称'";
    $rs             = $db->Execute($sql);
    $测评信息       	= $rs->fields;

    $躯体化惊恐LIST         = [1,6,9,12,15,18,19,22,24,27,30,34,38];
    $广泛性焦虑LIST         = [5,7,14,21,23,28,33,35,37];
    $分离性焦虑LIST         = [4,8,13,16,20,25,29,31];
    $社交恐怖LIST           = [3,10,26,32,39,40,41];
    $学校恐怖LIST           = [2,11,17,36];

    $量表解释                   = [];
    $量表解释['躯体化惊恐']     = "";
    $量表解释['广泛性焦虑']     = "";
    $量表解释['分离性焦虑']     = "";
    $量表解释['社交恐怖']       = "";
    $量表解释['学校恐怖']       = "";

    $sql            = "SELECT * FROM `data_xinlijiankang_cepingxuanxiang` where 测评名称='$测评名称' limit 0, 300";
    $rs             = $db->Execute($sql);
    $测评选项RSA     = (array)$rs->GetArray();
    $测评选项转序号   = [];
    foreach($测评选项RSA AS $测评选项RS) {
        $测评选项转序号[$测评选项RS['测评项目']] = $测评选项RS['序号'];
    }

    $Element    = [];
    $用户名     = $GLOBAL_USER->USER_ID;
    $学号       = $GLOBAL_USER->学号;
    if($GLOBAL_USER->type == "User") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 用户名='$用户名'";
        $Element['用户名'] = $用户名;
        $Element['姓名'] = $GLOBAL_USER->USER_NAME;
    }
    else if($GLOBAL_USER->type == "Student") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 学号='$学号'";
        $Element['学号'] = $学号;
        $Element['姓名'] = $GLOBAL_USER->姓名;
        $Element['班级'] = $GLOBAL_USER->班级;
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $测评明细RSA     = (array)$rs->GetArray();
    $测评分数 = 0;
    $躯体化惊恐VALUE = $广泛性焦虑VALUE = $分离性焦虑VALUE = $学校恐怖VALUE = $社交恐怖VALUE = 0;
    $焦虑VALUE = $身体症状VALUE = $恐怖倾向VALUE = $冲动倾向VALUE = $心理不平衡VALUE = 0;
    $躯体化惊恐Array = $广泛性焦虑Array = $分离性焦虑Array = $学校恐怖Array = $社交恐怖Array = [];
    $焦虑Array = $身体症状Array = $恐怖倾向Array = $冲动倾向Array = $心理不平衡Array = [];
    foreach($测评明细RSA AS $测评明细) {
        $测评分数 += $测评明细['测评分值'];
        $序号 = $测评选项转序号[$测评明细['测评项目']];
        if($序号>0 && in_array($序号, $躯体化惊恐LIST)) {
            $躯体化惊恐VALUE += $测评明细['测评分值'];
            $躯体化惊恐Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $广泛性焦虑LIST)) {
            $广泛性焦虑VALUE += $测评明细['测评分值'];
            $广泛性焦虑Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $分离性焦虑LIST)) {
            $分离性焦虑VALUE += $测评明细['测评分值'];
            $分离性焦虑Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $社交恐怖LIST)) {
            $社交恐怖VALUE += $测评明细['测评分值'];
            $社交恐怖Array[] = $测评明细;
        }
        if($序号>0 && in_array($序号, $学校恐怖LIST)) {
            $学校恐怖VALUE += $测评明细['测评分值'];
            $学校恐怖Array[] = $测评明细;
        }
    }
    $测评分析 = [];
    $测评分析['躯体化惊恐'] = $躯体化惊恐VALUE;
    $测评分析['广泛性焦虑'] = $广泛性焦虑VALUE;
    $测评分析['分离性焦虑'] = $分离性焦虑VALUE;
    $测评分析['社交恐怖'] = $社交恐怖VALUE;
    $测评分析['学校恐怖'] = $学校恐怖VALUE;
    $Element['测评名称'] = $测评明细['测评名称'];
    $Element['测试项目数量'] = sizeof($测评明细RSA);
    $Element['测评分数'] = $测评分数;
    $Element['测评时间'] = Date('Y-m-d H:i:s');

    $我的测评记录 = [];
    $我的测评记录['躯体化惊恐Array'] = $躯体化惊恐Array;
    $我的测评记录['广泛性焦虑Array'] = $广泛性焦虑Array;
    $我的测评记录['分离性焦虑Array'] = $分离性焦虑Array;
    $我的测评记录['社交恐怖Array'] = $社交恐怖Array;
    $我的测评记录['学校恐怖Array'] = $学校恐怖Array;

    $因子分析 	= [];
    $因子分析[] 	= [ '名称'=>'躯体化惊恐', '测评分数'=>$测评分析['躯体化惊恐'], '因子解释'=>$量表解释['躯体化惊恐'], '测评明细'=> $躯体化惊恐Array ];
    $因子分析[] 	= [ '名称'=>'广泛性焦虑', '测评分数'=>$测评分析['广泛性焦虑'], '因子解释'=>$量表解释['广泛性焦虑'], '测评明细'=> $广泛性焦虑Array ];
    $因子分析[] 	= [ '名称'=>'分离性焦虑', '测评分数'=>$测评分析['分离性焦虑'], '因子解释'=>$量表解释['分离性焦虑'], '测评明细'=> $分离性焦虑Array ];
    $因子分析[] 	= [ '名称'=>'社交恐怖', '测评分数'=>$测评分析['社交恐怖'], '因子解释'=>$量表解释['社交恐怖'], '测评明细'=> $社交恐怖Array ];
    $因子分析[] 	= [ '名称'=>'学校恐怖', '测评分数'=>$测评分析['学校恐怖'], '因子解释'=>$量表解释['学校恐怖'], '测评明细'=> $学校恐怖Array ];

    $输出报告 	= [];
    $输出报告['测评说明'] = $测评信息['测评说明'];
    $输出报告['评分标准'] = $测评信息['评分标准'];
    $输出报告['因子分析'] = $因子分析;
    $输出报告['测评分析'] = $测评分析;
    $Element['测评分析'] = base64_encode(json_encode($输出报告));
    if($GLOBAL_USER->type == "User") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,用户名,测评时间', 0);
    }
    else if($GLOBAL_USER->type == "Student") {
        [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingresult", $Element, '测评名称,学号,测评时间', 0);
    }
}

function plugin_data_xinlijiankang_ceping_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code

    $edit_default_mode  = [];
    $edit_default       = [];
    $defaultValues      = [];

    $sql            = "SELECT * FROM `data_xinlijiankang_ceping` where id='$id'";
    $rs             = $db->Execute($sql);
    $测评信息       	= $rs->fields;
    $测评名称       	= $测评信息['测评名称'];

    $用户名     		= $GLOBAL_USER->USER_ID;
    $学号       		= $GLOBAL_USER->学号;
    if($GLOBAL_USER->type == "User") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 用户名='".$用户名."'";
    }
    else if($GLOBAL_USER->type == "Student") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingjilu` where 测评名称='$测评名称' and 学号='".$学号."'";
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $测评记录        	= (array)$rs->GetArray();
    $测评记录MAP     	= [];
    foreach($测评记录 as $Item) {
        $测评记录MAP[$Item['测评项目']] = $Item['测评选项'];
    }

    $sql            = "SELECT * FROM `data_xinlijiankang_cepingxuanxiang` where 测评名称='$测评名称' limit 0, 200";
    $rs             = $db->Execute($sql);
    $题库信息        	= $rs->GetArray();
    $题目序号列表    	= [];
    $序号           	= 1;
    $题目类型       	= "单选";
    $edit_default_mode[] = ['value'=>$题目类型, 'label'=>$题目类型];
    foreach($题库信息 AS $单个题目) {
        $题目选项 = [];
        $题目序号列表[] = $单个题目['id'];
        if($单个题目['测评选项A']!="")      {
            $题目选项[] = ['value'=>$单个题目['测评选项A'], 'label'=>$单个题目['测评选项A']];
        }
        if($单个题目['测评选项B']!="")      {
            $题目选项[] = ['value'=>$单个题目['测评选项B'], 'label'=>$单个题目['测评选项B']];
        }
        if($单个题目['测评选项C']!="")      {
            $题目选项[] = ['value'=>$单个题目['测评选项C'], 'label'=>$单个题目['测评选项C']];
        }
        if($单个题目['测评选项D']!="")      {
            $题目选项[] = ['value'=>$单个题目['测评选项D'], 'label'=>$单个题目['测评选项D']];
        }
        if($单个题目['测评选项E']!="")      {
            $题目选项[] = ['value'=>$单个题目['测评选项E'], 'label'=>$单个题目['测评选项E']];
        }
        if($单个题目['测评选项F']!="")      {
            $题目选项[] = ['value'=>$单个题目['测评选项F'], 'label'=>$单个题目['测评选项F']];
        }
        if($题目类型=="单选" || $题目类型=="判断")        {
            $edit_default[$题目类型][] = ['name' => "题目_".$单个题目['id'], 'show'=>true, 'type'=>'radiogroup', 'options'=>$题目选项, 'label' => $序号."、".$单个题目['测评项目'], 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>12, 'row'=>false]];
            $defaultValues["题目_".$单个题目['id']] = ""; //不需要设置默认值 $测评记录MAP[$单个题目['测评项目']];
        }
        $序号 ++;
    }
    $edit_default[$题目类型][] 				= ['name' => "题目序号列表", 'show'=>true, 'type'=>'hidden', 'label' => "题目序号列表", 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>12]];
    $defaultValues['题目序号列表'] 			= EncryptID(join(',',$题目序号列表));

    $RS['edit_default']['allFields']      	= $edit_default;
    $RS['edit_default']['allFieldsMode']  	= $edit_default_mode;
    $RS['edit_default']['defaultValues']  	= $defaultValues;
    $RS['edit_default']['dialogContentHeight']  = "850px";
    $RS['edit_default']['submitaction']  	= "edit_default_data";
    $RS['edit_default']['componentsize'] 	= "small";
    $RS['edit_default']['submittext']    	= __("Submit");
    $RS['edit_default']['canceltext']    	= __("Cancel");
    $RS['edit_default']['titletext']     	= $测评名称;
    $RS['edit_default']['titlememo']     	= "限时20分钟,每天只能测评一次.";
    $RS['edit_default']['titlememo1']     	= "测评完成五分钟内会帮您生成完整的测评报告.";
    $RS['edit_default']['titlememo2']     	= "";
    $RS['edit_default']['tablewidth']    	= 650;
    $RS['edit_default']['submitloading']    = __("SubmitLoading");
    $RS['edit_default']['loading']          = __("Loading");
    $RS['edit_default']['model']            = "Loop";
	$RS['edit_default']['processtext']      = "测评进度";

    $RS['status']   = "OK";
    $RS['msg']      = "获得数据成功";
    $RS['forceuse'] = true; //强制使用当前结构数据来渲染表单
    $RS['data']     = $defaultValues;
    $RS['EnableFields']     = [];
    print_R(json_encode($RS, true));

    exit;

}

function plugin_data_xinlijiankang_ceping_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code

    $sql            = "SELECT * FROM `data_xinlijiankang_ceping` where id='$id'";
    $rs             = $db->Execute($sql);
    $测评信息       = $rs->fields;
    $测评名称       = $测评信息['测评名称'];

    $sql            = "SELECT * FROM `data_xinlijiankang_cepingxuanxiang` where 测评名称='$测评名称' limit 0, 200";
    $rs             = $db->Execute($sql);
    $题库信息        = $rs->GetArray();

    $当前学期        = getCurrentXueQi();

    $用户名     = $GLOBAL_USER->USER_ID;
    $学号       = $GLOBAL_USER->学号;
    if($GLOBAL_USER->type == "User") {
        $姓名       = $GLOBAL_USER->USER_NAME;
        $学号       = '';
    }
    else if($GLOBAL_USER->type == "Student") {
        $姓名       = $GLOBAL_USER->姓名;
        $用户名     = '';
    }
    else {
        return ;
    }

    foreach($题库信息 AS $单个题目) {
        $题目选项 = [];
        $测评选项 = $_POST['题目_'.$单个题目['id']];
        if($测评选项!="")      {
            $Element = [];
            $Element['测评名称'] = $单个题目['测评名称'];
            $Element['测评项目'] = $单个题目['测评项目'];
            $Element['测评选项'] = $测评选项;
            $Element['学期名称'] = $当前学期;
            $Element['测评分值'] = 测评选项转分值($Element['测评名称'],$Element['测评选项']);
            $Element['测评时间'] = Date('Y-m-d H:i:s');
            //print_R($ABCDE);print_R($单个题目);print_R($Element);exit;
            if($GLOBAL_USER->type == "User") {
                $Element['用户名']      = $用户名;
                $Element['姓名']        = $姓名;
                [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingjilu", $Element, '测评名称,测评项目,用户名', 0);
            }
            else if($GLOBAL_USER->type == "Student") {
                $Element['学号']      = $学号;
                $Element['姓名']      = $姓名;
                [$Record, $sql, $InsertRecordId]  = InsertOrUpdateTableByArray("data_xinlijiankang_cepingjilu", $Element, '测评名称,测评项目,学号', 0);
            }
            else {
                return ;
            }
        }
    }

    //根据明细分析测评结果
    switch($测评名称) {
        case '中学生心理健康量表(MSSMHS)':
            中学生心理健康量表_根据测评明细分析测评结果($测评名称);
            //中学生心理健康量表_心理健康AiDeepSeek测评($测评名称, $用户名, $学号);
            break;
        case '症状自评量表(SCL-90)':
            症状自评量表SCL90($测评名称);
            //中学生心理健康量表_症状自评量表SCL90AiDeepSeek测评($测评名称, $用户名, $学号);
            break;
        case '中学生学科兴趣测评':
            中学生学科兴趣测评($测评名称);
            //中学生心理健康量表_中学生学科兴趣测评AiDeepSeek测评($测评名称, $用户名, $学号);
            break;
        case '中小学生心理健康量表(MHT)':
            中小学生心理健康量表MHT($测评名称);
            //中学生心理健康量表_中小学生心理健康量表MHTAiDeepSeek测评($测评名称, $用户名, $学号);
            break;
        case '儿童焦虑性情绪障碍筛查表(SCARED)':
            儿童焦虑性情绪障碍筛查表SCARED($测评名称);
            //中学生心理健康量表_儿童焦虑性情绪障碍筛查表SCAREDAiDeepSeek测评($测评名称, $用户名, $学号);
            break;
    }

    $RS = [];
    $RS['status']   = "OK";
    $RS['msg']      = "成功提交心理测评,完整的心理测评报告会在5-30分钟内帮您生成.";
    $RS['_GET']     = $_GET;
    $RS['_POST']    = $_POST;
    print json_encode($RS);
    exit;

}

function plugin_data_xinlijiankang_ceping_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code

    $sql            = "SELECT * FROM `data_unit`";
    $rs             = $db->Execute($sql);
    $测评信息       = $rs->fields;
    $单位名称       = $测评信息['UNIT_NAME'];

    $sql            = "SELECT * FROM `data_xinlijiankang_ceping` where id='$id'";
    $rs             = $db->Execute($sql);
    $测评信息       = $rs->fields;
    $测评名称       = $测评信息['测评名称'];

    $用户名     		= $GLOBAL_USER->USER_ID;
    $学号       		= $GLOBAL_USER->学号;
    if($GLOBAL_USER->type == "User") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称='$测评名称' and 用户名='$用户名' order by id desc";
    }
    else if($GLOBAL_USER->type == "Student") {
        $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where 测评名称='$测评名称' and 学号='$学号' order by id desc";
    }
    else {
        return ;
    }
    $rs             = $db->Execute($sql);
    $测评结果       = $rs->fields;
    $测评分析       = json_decode(base64_decode($测评结果['测评分析']), true);
    $DeepSeek       = base64_decode($测评结果['DeepSeek']);

    if($测评结果['测评时间'] == "")  {
      $测评分析['测评说明'] = $测评信息['测评说明'];
      $测评分析['评分标准'] = $测评信息['评分标准'];
    }

    $测评分析['用户信息'] = ['学号'=>$测评结果['学号'], '姓名'=>$测评结果['姓名'], '用户名'=>$测评结果['用户名'], '班级'=>$测评结果['班级'], '测评时间'=>$测评结果['测评时间'], '使用时间'=>$测评结果['使用时间'], '测评分数'=>$测评结果['测评分数']];
    $测评分析['DeepSeek'] = $DeepSeek;
    $测评分析['单位名称'] = $单位名称;
    $测评分析['测评名称'] = $测评名称;

    $测评分析['sharedModel']  = ['title'=>'分享图片', 'actions'=>['Save', 'Wechat', 'More']];
    $RS['status']             = "OK";
    $RS['model']              = '测评模式';
    $RS['data']               = $测评分析;
    print_R(json_encode($RS, true));


    exit;

}

function plugin_data_xinlijiankang_ceping_init_default_filter_RS($RS)  {
  global $db;
  global $SettingMap;
  global $MetaColumnNames;
  global $GLOBAL_USER;
  global $TableName;
  //Here is your write code

  return $RS;
}

function plugin_data_xinlijiankang_ceping_init_default_mobile_line_control($Element)  {
  global $db;
  global $SettingMap;
  global $MetaColumnNames;
  global $GLOBAL_USER;
  global $TableName;
  //Here is your write code
  $id         = DecryptID($Element['Id']);
  $sql        = "select * from data_xinlijiankang_ceping where id = '$id'";
  $rs         = $db->Execute($sql);
  $测评RSA  = (array)$rs->GetArray();
  $测评名称 = $测评RSA[0]['测评名称'];

  //只需要得到最近一次的测评记录
  $sql        = "select * from data_xinlijiankang_cepingresult where 测评名称 = '$测评名称' and DATE(测评时间) = CURDATE() order by id desc limit 1";
  $rs         = $db->Execute($sql);
  $测评记录   = (array)$rs->GetArray();

  if(sizeof($测评记录)>0 && $测评记录[0]['测评时间']!="")    {
    $Element['EditUrl']   = "";
    $Element['EditIcon']  = "";
    $Element['MobileEndSecondLineRight'] = "测评时间:".substr($测评记录[0]['测评时间'], 5, 11);
  }

  return $Element;
}


?>
