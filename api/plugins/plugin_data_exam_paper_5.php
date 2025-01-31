<?php

//FlowName: 我的考试

function plugin_data_exam_paper_5_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_5_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_5_add_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    /*
    $sql        = "select * from `$TableName` where id = '$id'";
    $rs         = $db->Execute($sql);
    $rs_a       = $rs->GetArray();
    foreach($rs_a as $Line)  {
        //
    }
    */
}

function plugin_data_exam_paper_5_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code

    $edit_default_mode  = [];
    $edit_default       = [];
    $defaultValues      = [];


    $sql            = "SELECT * FROM `data_exam_paper` where id='$id'";
    $rs             = $db->Execute($sql);
    $试卷信息       = $rs->fields;
    $试题抽取方式   = $试卷信息['试题抽取方式'];
    $题库分类       = $试卷信息['题库分类'];
    $单选题目数量   = $试卷信息['单选题目数量'];
    $多选题目数量   = $试卷信息['多选题目数量'];
    $判断题目数量   = $试卷信息['判断题目数量'];
    $考试名称       = $试卷信息['考试名称'];

    //判断是否已经有考试记录
    $学号           = $GLOBAL_USER->学号;
    $sql            = "select COUNT(*) AS NUM from data_exam_question_record where 学号='$学号' and 考试名称='$考试名称'";
    $rs             = $db->Execute($sql);
    $NUM            = $rs->fields['NUM'];
    if($NUM>0)   {
        $RS['edit_default']['allFields']      = [];
        $RS['edit_default']['allFieldsMode']  = [];
        $RS['edit_default']['defaultValues']  = [];
        $RS['edit_default']['dialogContentHeight']  = "850px";
        $RS['edit_default']['submitaction']  = "edit_default_data";
        $RS['edit_default']['componentsize'] = "small";
        $RS['edit_default']['submittext']    = "";
        $RS['edit_default']['canceltext']    = __("Cancel");
        $RS['edit_default']['titletext']     = $考试名称;
        $RS['edit_default']['titlememo']     = "您已经参加过本次考试,不需要再次考试.";
        $RS['edit_default']['tablewidth']    = 650;
        $RS['edit_default']['submitloading']    = __("SubmitLoading");
        $RS['edit_default']['loading']          = __("Loading");

        $RS['status']   = "OK";
        $RS['msg']      = "获得数据成功";
        $RS['forceuse'] = true; //强制使用当前结构数据来渲染表单
        $RS['data']     = $defaultValues;
        $RS['EnableFields']     = [];
        print_R(json_encode($RS, true));
        exit;
    }

    $试卷数据       = json_decode(base64_decode($试卷信息['试卷数据']),true);
    $题库抽取       = [];
    $题目序号列表   = [];
    if($试题抽取方式=="所有学生共用一套试题"&&$单选题目数量>0) {
        $sql        = "select * from data_exam_question where id in ('".join("','",$试卷数据['单选'])."')";
        $rs         = $db->Execute($sql);
        $rs_a       = $rs->GetArray();
        $NUM        = sizeof($rs_a);
        for($i=0;$i<sizeof($rs_a);$i++) {
            $Item = $rs_a[$i];
            $题库抽取['单选'][] = $Item;
            $题目序号列表[] = $Item['id'];
        }
    }
    if($试题抽取方式=="所有学生共用一套试题"&&$多选题目数量>0) {
        $sql        = "select * from data_exam_question where id in ('".join("','",$试卷数据['多选'])."')";
        $rs         = $db->Execute($sql);
        $rs_a       = $rs->GetArray();
        $NUM        = sizeof($rs_a);
        for($i=0;$i<sizeof($rs_a);$i++) {
            $Item = $rs_a[$i];
            $题库抽取['多选'][] = $Item;
            $题目序号列表[] = $Item['id'];
        }
    }
    if($试题抽取方式=="所有学生共用一套试题"&&$判断题目数量>0) {
        $sql        = "select * from data_exam_question where id in ('".join("','",$试卷数据['判断'])."')";
        $rs         = $db->Execute($sql);
        $rs_a       = $rs->GetArray();
        $NUM        = sizeof($rs_a);
        for($i=0;$i<sizeof($rs_a);$i++) {
            $Item = $rs_a[$i];
            $题库抽取['判断'][] = $Item;
            $题目序号列表[] = $Item['id'];
        }
    }
    $序号 = 1;
    foreach($题库抽取 AS $题目类型=>$对应题目) {
        $edit_default_mode[] = ['value'=>$题目类型, 'label'=>$题目类型];
        foreach($对应题目 AS $单个题目)     {
            $题目选项 = [];
            if($单个题目['A']!="")      {
                $题目选项[] = ['value'=>'A', 'label'=>$单个题目['A']];
            }
            if($单个题目['B']!="")      {
                $题目选项[] = ['value'=>'B', 'label'=>$单个题目['B']];
            }
            if($单个题目['C']!="")      {
                $题目选项[] = ['value'=>'C', 'label'=>$单个题目['C']];
            }
            if($单个题目['D']!="")      {
                $题目选项[] = ['value'=>'D', 'label'=>$单个题目['D']];
            }
            if($单个题目['E']!="")      {
                $题目选项[] = ['value'=>'E', 'label'=>$单个题目['E']];
            }
            if($单个题目['F']!="")      {
                $题目选项[] = ['value'=>'F', 'label'=>$单个题目['F']];
            }
            if($题目类型=="单选" || $题目类型=="判断")        {
                $edit_default[$题目类型][] = ['name' => "题目_".$单个题目['id'], 'show'=>true, 'type'=>'radiogroup', 'options'=>$题目选项, 'label' => $序号."、".$单个题目['题干'], 'value' => "A", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>12, 'row'=>false]];
                $defaultValues["题目_".$单个题目['id']] = "";
            }
            else if($题目类型=="多选") {
                $edit_default[$题目类型][] = ['name' => "题目_".$单个题目['id'], 'show'=>true, 'type'=>'checkbox', 'options'=>$题目选项, 'label' => $序号."、".$单个题目['题干'], 'value' => "A", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'row'=>false, 'xs'=>12, 'sm'=>12]];
                $defaultValues["题目_".$单个题目['id']] = "";
            }
            $序号 ++;
        }
    }
    $edit_default[$题目类型][] = ['name' => "题目序号列表", 'show'=>true, 'type'=>'hidden', 'label' => "题目序号列表", 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>12]];
    $defaultValues['题目序号列表'] = EncryptID(join(',',$题目序号列表));

    $RS['edit_default']['allFields']      = $edit_default;
    $RS['edit_default']['allFieldsMode']  = $edit_default_mode;
    $RS['edit_default']['defaultValues']  = $defaultValues;
    $RS['edit_default']['dialogContentHeight']  = "850px";
    $RS['edit_default']['submitaction']  = "edit_default_data";
    $RS['edit_default']['componentsize'] = "small";
    $RS['edit_default']['submittext']    = __("Submit");
    $RS['edit_default']['canceltext']    = __("Cancel");
    $RS['edit_default']['titletext']     = "开始您的考试";
    $RS['edit_default']['titlememo']     = "不限制时间,每次随机出题";
    $RS['edit_default']['tablewidth']    = 650;
    $RS['edit_default']['submitloading']    = __("SubmitLoading");
    $RS['edit_default']['loading']          = __("Loading");

    $RS['status']   = "OK";
    $RS['msg']      = "获得数据成功";
    $RS['forceuse'] = true; //强制使用当前结构数据来渲染表单
    $RS['data']     = $defaultValues;
    $RS['EnableFields']     = [];
    print_R(json_encode($RS, true));

    exit;
}

function plugin_data_exam_paper_5_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code

    $sql            = "SELECT * FROM `data_exam_paper` where id='$id'";
    $rs             = $db->Execute($sql);
    $试卷信息       = $rs->fields;
    $试题抽取方式   = $试卷信息['试题抽取方式'];
    $题库分类       = $试卷信息['题库分类'];
    $考试类型       = $试卷信息['考试类型'];
    $考试名称       = $试卷信息['考试名称'];
    $单选题目数量   = $试卷信息['单选题目数量'];
    $多选题目数量   = $试卷信息['多选题目数量'];
    $判断题目数量   = $试卷信息['判断题目数量'];
    $用户答题_正确  = 0;
    $用户答题_错误  = 0;
    $用户答题_得分  = 0;

    $题目序号列表 = DecryptID($_POST['题目序号列表']);
    $题目序号列表Array = explode(',',$题目序号列表);
    foreach($题目序号列表Array AS $Item) {
        if($Item!="")   {
            $用户所选 = $_POST["题目_".$Item];
            if($用户所选!="")  {
                $sql            = "SELECT * FROM `data_exam_question` where id='$Item'";
                $rs             = $db->Execute($sql);
                $题目信息        = $rs->fields;
                $Element = [];
                $Element['考试名称']    = $考试名称;
                $Element['考试类型']    = $考试类型;
                $Element['题库分类']    = $题库分类;
                $Element['题目ID']      = $题目信息['id'];
                $Element['所选']        = $用户所选;
                $Element['答案']        = $题目信息['答案'];
                $Element['题目类型']     = $题目信息['类型'];
                if($Element['答案']==$用户所选)  {
                    $对错="正确";
                }
                else {
                    $对错="错误";
                }
                $Element['对错']        = $对错;
                if($对错=="正确")   {
                    $Element['得分']    = $题目信息['分值'];
                    $用户答题_得分      += $题目信息['分值'];
                    $用户答题_正确 ++;
                }
                else {
                    $Element['得分']    = 0;
                    $用户答题_错误 ++;
                }
                $Element['学号']        = $GLOBAL_USER->学号;
                $Element['姓名']        = $GLOBAL_USER->姓名;
                $Element['班级']        = $GLOBAL_USER->班级;
                $Element['时间']        = date("Y-m-d H:i:s");
                [$Record,$sql]  = InsertOrUpdateTableByArray("data_exam_question_record", $Element, '题目ID,学号,时间', 0, "Insert");
                if($Record->EOF) {
                }
            }
        }
    }

    $RS = [];
    $RS['status']   = "OK";
    $RS['msg']      = "您已经完成本次考试,正确:".$用户答题_正确." 错误:".$用户答题_错误." 得分:".$用户答题_得分."";
    $RS['_GET']     = $_GET;
    $RS['_POST']    = $_POST;
    print json_encode($RS);
    exit;
}

function plugin_data_exam_paper_5_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_5_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_5_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_5_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_5_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_exam_paper_5_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>
