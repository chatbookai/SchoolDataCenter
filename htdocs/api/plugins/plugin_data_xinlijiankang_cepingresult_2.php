<?php

//FlowName: 我的心理测评

function plugin_data_xinlijiankang_cepingresult_2_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_xinlijiankang_cepingresult_2_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_xinlijiankang_cepingresult_2_add_default_data_after_submit($id)  {
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

function plugin_data_xinlijiankang_cepingresult_2_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_xinlijiankang_cepingresult_2_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_xinlijiankang_cepingresult_2_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_xinlijiankang_cepingresult_2_edit_default_configsetting_data($id)  {
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

function plugin_data_xinlijiankang_cepingresult_2_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code

    $sql            = "SELECT * FROM `data_unit`";
    $rs             = $db->CacheExecute(10,$sql);
    $测评信息       = $rs->fields;
    $单位名称       = $测评信息['UNIT_NAME'];

    $sql            = "SELECT * FROM `data_xinlijiankang_cepingresult` where id='$id'";
    $rs             = $db->CacheExecute(10,$sql);
    $测评信息       = $rs->fields;

    $测评分析       = json_decode(base64_decode($测评信息['测评分析']), true);
    $DeepSeek     = base64_decode($测评信息['DeepSeek']);

    $测评分析['用户信息'] = ['学号'=>$测评信息['学号'], '姓名'=>$测评信息['姓名'], '班级'=>$测评信息['班级'], '测评时间'=>$测评信息['测评时间'], '使用时间'=>$测评信息['使用时间'], '测评分数'=>$测评信息['测评分数']];
    $测评分析['DeepSeek'] = $DeepSeek;
    $测评分析['单位名称'] = $单位名称;
    $测评分析['测评名称'] = $测评信息['测评名称'];

    $RS['status']   = "OK";
    $RS['model']    = '测评模式';
    $RS['data']     = $测评分析;
    print_R(json_encode($RS, true));

    exit;
}

function plugin_data_xinlijiankang_cepingresult_2_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_xinlijiankang_cepingresult_2_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_xinlijiankang_cepingresult_2_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_xinlijiankang_cepingresult_2_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>