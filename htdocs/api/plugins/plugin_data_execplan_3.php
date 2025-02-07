<?php

//FlowName: AI生成课堂测验

function plugin_data_execplan_3_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_execplan_3_init_default_filter_RS($RS)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code

    //$返回编辑页面的页面结构Value = 返回编辑页面的页面结构();

    //$RS['edit_default'] = $返回编辑页面的页面结构Value;

    return $RS;
}

function plugin_data_execplan_3_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    exit;
}

function plugin_data_execplan_3_add_default_data_after_submit($id)  {
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

function plugin_data_execplan_3_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    $edit_default_mode  = [];
    $edit_default       = [];
    $defaultValues      = [];

    $返回编辑页面的页面结构Value = 返回编辑页面的页面结构();

    $RS['edit_default'] = $返回编辑页面的页面结构Value;
    $RS['status']     = "OK";
    $RS['msg']        = "获得数据成功";
    $RS['forceuse']   = true; //强制使用当前结构数据来渲染表单
    $RS['data']       = $返回编辑页面的页面结构Value['defaultValues'];
    $RS['EnableFields']     = [];
    print_R(json_encode($RS, true));

    exit;

}

function 返回编辑页面的页面结构() {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;

    $课程名称        = (string)$_GET['课程名称'];
    $班级名称        = (string)$_GET['班级名称'];
    $学期名称        = (string)$_GET['学期名称'];

    $sql        = "select * from data_exam_question where 学期='$学期名称' and 班级='$班级名称' and 课程='$课程名称' order by id asc";
    $rs         = $db->Execute($sql);
    $rs_a       = $rs->GetArray();
    $NUM        = sizeof($rs_a);
    for($i=0;$i<$NUM;$i++) {
        $Item = $rs_a[$i];
        $题库抽取[$Item['类型']][] = $Item;
        $题目序号列表[] = $Item['id'];
    }
    $序号 = 1;
    foreach($题库抽取 AS $题目类型=>$对应题目) {
        if($题目类型=="单选题" || $题目类型=="判断题" || $题目类型=="多选题" || $题目类型=="问答题" || $题目类型=="填空题")  {
          $edit_default_mode[] = ['value'=>$题目类型, 'label'=>$题目类型];
        }
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
            if($题目类型=="单选题" || $题目类型=="判断题")        {
                $edit_default[$题目类型][] = ['name' => "Question_".$单个题目['id'], 'show'=>true, 'type'=>'radiogroup', 'options'=>$题目选项, 'label' => $序号."、[".$题目类型."] ".$单个题目['题干']."(".$单个题目['分值']."分)", 'value' => $单个题目['答案'], 'placeholder' => "", 'helptext' => "解析: ".$单个题目['解析'], 'rules' => ['required' => true, 'disabled' => true, 'xs'=>12, 'sm'=>12, 'row'=>false]];
                $defaultValues["Question_".$单个题目['id']] = $单个题目['答案'];
                $序号 ++;
            }
            else if($题目类型=="多选题") {
                $edit_default[$题目类型][] = ['name' => "Question_".$单个题目['id'], 'show'=>true, 'type'=>'checkbox', 'options'=>$题目选项, 'label' => $序号."、[".$题目类型."] ".$单个题目['题干']."(".$单个题目['分值']."分)", 'value' => str_replace(" ", "", $单个题目['答案']), 'placeholder' => "", 'helptext' => "解析: ".$单个题目['解析'], 'rules' => ['required' => true, 'disabled' => true, 'row'=>false, 'xs'=>12, 'sm'=>12]];
                $defaultValues["Question_".$单个题目['id']] = str_replace(" ", "", $单个题目['答案']);
                $序号 ++;
            }
            else if($题目类型=="问答题") {
                $edit_default[$题目类型][] = ['name' => "Question_".$单个题目['id'], 'show'=>true, 'type'=>'textarea', 'label' => $序号."、[".$题目类型."] ".$单个题目['题干']."(".$单个题目['分值']."分)", 'value' => str_replace(" ", "", $单个题目['答案']), 'placeholder' => "", 'helptext' => "解析: ".$单个题目['解析'], 'rules' => ['required' => true, 'disabled' => true, 'row'=>false, 'xs'=>12, 'sm'=>12]];
                $defaultValues["Question_".$单个题目['id']] = str_replace(" ", "", $单个题目['答案']);
                $序号 ++;
            }
            else if($题目类型=="填空题") {
                $edit_default[$题目类型][] = ['name' => "Question_".$单个题目['id'], 'show'=>true, 'type'=>'input', 'label' => $序号."、[".$题目类型."] ".$单个题目['题干']."(".$单个题目['分值']."分)", 'value' => str_replace(" ", "", $单个题目['答案']), 'placeholder' => "", 'helptext' => "解析: ".$单个题目['解析'], 'rules' => ['required' => true, 'disabled' => true, 'row'=>false, 'xs'=>12, 'sm'=>12]];
                $defaultValues["Question_".$单个题目['id']] = str_replace(" ", "", $单个题目['答案']);
                $序号 ++;
            }
        }
    }
    $edit_default[$题目类型][] = ['name' => "题目序号列表", 'show'=>true, 'type'=>'hidden', 'label' => "题目序号列表", 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>12]];
    //$defaultValues['题目序号列表'] = EncryptID(join(',',$题目序号列表));

    $RS['edit_default']['allFields']            = $edit_default;
    $RS['edit_default']['allFieldsMode']        = $edit_default_mode;
    $RS['edit_default']['defaultValues']        = $defaultValues;
    $RS['edit_default']['dialogContentHeight']  = "850px";
    $RS['edit_default']['submitaction']     = ""; //"edit_default_data";
    $RS['edit_default']['componentsize']    = "small";
    $RS['edit_default']['submittext']       = ""; //__("Submit");
    $RS['edit_default']['canceltext']       = ""; //__("Cancel");
    $RS['edit_default']['titletext']        = ""; //"开始您的测验";
    $RS['edit_default']['titlememo']        = ""; //"不限制时间,每次随机出题";
    $RS['edit_default']['tablewidth']       = 650;
    $RS['edit_default']['submitloading']    = __("SubmitLoading");
    $RS['edit_default']['loading']          = __("Loading");
    $RS['edit_default']['sql']              = $sql;
    $RS['edit_default']['_GET']             = $_GET;

    return $RS['edit_default'];
}

function plugin_data_execplan_3_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_execplan_3_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_execplan_3_edit_default_configsetting_data($id)  {
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

function plugin_data_execplan_3_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_execplan_3_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_execplan_3_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_execplan_3_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_execplan_3_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>
