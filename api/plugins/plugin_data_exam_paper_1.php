<?php

//FlowName: 考试明细

function plugin_data_exam_paper_1_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_1_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_1_add_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    if($_POST['试题抽取方式']=="所有学生共用一套试题")   {
        $单选题目数量   = $_POST['单选题目数量'];
        $题库分类       = $_POST['题库分类'];
        if($单选题目数量>0) {
            $sql        = "select * from data_exam_question where 题库分类='$题库分类' and 类型='单选'";
            $rs         = $db->Execute($sql);
            $rs_a       = $rs->GetArray();
            $NUM        = sizeof($rs_a);
            for($i=0;$i<$单选题目数量;$i++) {
                $Item = $rs_a[rand(0,$NUM-1)];
                $题目序号列表['单选'][] = $Item['id'];
            }
        }
        $多选题目数量 = $_POST['多选题目数量'];
        if($多选题目数量>0) {
            $sql        = "select * from data_exam_question where 题库分类='$题库分类' and 类型='多选'";
            $rs         = $db->Execute($sql);
            $rs_a       = $rs->GetArray();
            $NUM        = sizeof($rs_a);
            for($i=0;$i<$多选题目数量;$i++) {
                $Item = $rs_a[rand(0,$NUM-1)];
                $题目序号列表['多选'][] = $Item['id'];
            }
        }
        $判断题目数量 = $_POST['判断题目数量'];
        if($判断题目数量>0) {
            $sql        = "select * from data_exam_question where 题库分类='$题库分类' and 类型='判断'";
            $rs         = $db->Execute($sql);
            $rs_a       = $rs->GetArray();
            $NUM        = sizeof($rs_a);
            for($i=0;$i<$判断题目数量;$i++) {
                $Item = $rs_a[rand(0,$NUM-1)];
                $题目序号列表['判断'][] = $Item['id'];
            }
        }
        $试卷数据   = base64_encode(json_encode($题目序号列表));
        $sql        = "update $TableName set 试卷数据='$试卷数据' where id='$id'";
        $db->Execute($sql);
    }
}

function plugin_data_exam_paper_1_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_1_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_1_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    if($_POST['试题抽取方式']=="所有学生共用一套试题")   {
        $单选题目数量   = $_POST['单选题目数量'];
        $题库分类       = $_POST['题库分类'];
        if($单选题目数量>0) {
            $sql        = "select * from data_exam_question where 题库分类='$题库分类' and 类型='单选'";
            $rs         = $db->Execute($sql);
            $rs_a       = $rs->GetArray();
            $NUM        = sizeof($rs_a);
            for($i=0;$i<$单选题目数量;$i++) {
                $Item = $rs_a[rand(0,$NUM-1)];
                $题目序号列表['单选'][] = $Item['id'];
            }
        }
        $多选题目数量 = $_POST['多选题目数量'];
        if($多选题目数量>0) {
            $sql        = "select * from data_exam_question where 题库分类='$题库分类' and 类型='多选'";
            $rs         = $db->Execute($sql);
            $rs_a       = $rs->GetArray();
            $NUM        = sizeof($rs_a);
            for($i=0;$i<$多选题目数量;$i++) {
                $Item = $rs_a[rand(0,$NUM-1)];
                $题目序号列表['多选'][] = $Item['id'];
            }
        }
        $判断题目数量 = $_POST['判断题目数量'];
        if($判断题目数量>0) {
            $sql        = "select * from data_exam_question where 题库分类='$题库分类' and 类型='判断'";
            $rs         = $db->Execute($sql);
            $rs_a       = $rs->GetArray();
            $NUM        = sizeof($rs_a);
            for($i=0;$i<$判断题目数量;$i++) {
                $Item = $rs_a[rand(0,$NUM-1)];
                $题目序号列表['判断'][] = $Item['id'];
            }
        }
        $试卷数据   = base64_encode(json_encode($题目序号列表));
        $sql        = "update $TableName set 试卷数据='$试卷数据' where id='$id'";
        $db->Execute($sql);
    }
}

function plugin_data_exam_paper_1_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_1_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_1_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_exam_paper_1_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_exam_paper_1_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>