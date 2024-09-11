<?php

//FlowName: 收费标准

function plugin_data_middle_shoufeibiaozhun_1_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_middle_shoufeibiaozhun_1_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    $_POST['收费标准'] = $_POST['学期'] ."-". $_POST['学部'];
    $_POST['收费合计'] = intval($_POST['学费实收']) + intval($_POST['住宿费']) + intval($_POST['伙食费']) + intval($_POST['床上用品校服费']) + intval($_POST['代管费']);
}

function plugin_data_middle_shoufeibiaozhun_1_add_default_data_after_submit($id)  {
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
    $学费实收 = $_POST['学费'] * $_POST['学费折扣'];
    $sql = "update $TableName set where 学费实收='$学费实收' id='$id'";
    $db->Execute($sql);
}

function plugin_data_middle_shoufeibiaozhun_1_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_middle_shoufeibiaozhun_1_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    $_POST['收费标准'] = $_POST['学期'] ."-". $_POST['学部'];
    $_POST['收费合计'] = intval($_POST['学费实收']) + intval($_POST['住宿费']) + intval($_POST['伙食费']) + intval($_POST['床上用品校服费']) + intval($_POST['代管费']);
}

function plugin_data_middle_shoufeibiaozhun_1_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    $学费实收 = $_POST['学费'] * $_POST['学费折扣'];
    $sql = "update $TableName set where 学费实收='$学费实收' id='$id'";
    $db->Execute($sql);
}

function plugin_data_middle_shoufeibiaozhun_1_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_middle_shoufeibiaozhun_1_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_middle_shoufeibiaozhun_1_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_middle_shoufeibiaozhun_1_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_middle_shoufeibiaozhun_1_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>