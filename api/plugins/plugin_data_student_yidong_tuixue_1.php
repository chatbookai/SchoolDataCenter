<?php

//FlowName: 学生申请

function plugin_data_student_yidong_tuixue_1_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_student_yidong_tuixue_1_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    $USER_ID    = ForSqlInjection($GLOBAL_USER->USER_ID);
    $sql        = "select COUNT(*) AS NUM from $TableName where 学号='".$USER_ID."' and 班主任审核状态!='通过' ";
    $rsTemp     = $db->Execute($sql);
    if($rsTemp->fields['NUM']>0) {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = "处于填写状态中的记录只能有一条记录,目前您已经填写过,不需要再次填写.";
        $RS['sql'] = $sql;
        $RS['_GET'] = $_GET;
        $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }
    $USER_ID    = ForSqlInjection($GLOBAL_USER->USER_ID);
    $sql        = "select COUNT(*) AS NUM from $TableName where 学号='".$USER_ID."' and 班主任审核状态!='通过' ";
    $rsTemp     = $db->Execute($sql);
    if($rsTemp->fields['NUM']>0) {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = "处于填写状态中的记录只能有一条记录,目前您已经填写过,不需要再次填写.";
        $RS['sql'] = $sql;
        $RS['_GET'] = $_GET;
        $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }
}

function plugin_data_student_yidong_tuixue_1_add_default_data_after_submit($id)  {
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

function plugin_data_student_yidong_tuixue_1_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_student_yidong_tuixue_1_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_student_yidong_tuixue_1_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_student_yidong_tuixue_1_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_student_yidong_tuixue_1_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_student_yidong_tuixue_1_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}



?>