<?php

//FlowName: 数据库连接池

function plugin_data_datasource_1_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    global $DB_TYPE;
    //Here is your write code
    //检查数据库连接池情况
    $sql = "select * from data_datasource";
    $rs = $db->Execute($sql);
    $rs_a = $rs->GetArray();
    foreach($rs_a AS $Item) {
        //Check Mysql Db
        $db_remote = NewADOConnection($DB_TYPE);
        $db_remote->connect($Item['数据库主机'], $Item['数据库用户名'], DecryptID($Item['数据库密码']), $Item['数据库名称']);
        $db_remote->Execute("Set names utf8;");
        $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
        if($db_remote->databaseName!="" && $db_remote->databaseName==$Item['数据库名称']) {
            $sql = "update data_datasource set 连接状态='正常' where id='".$Item['id']."'";
        }
        else {
            $sql = "update data_datasource set 连接状态='失败' where id='".$Item['id']."'";
        }
        $db->Execute($sql);
    }
}

function plugin_data_datasource_1_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_1_add_default_data_after_submit($id)  {
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

function plugin_data_datasource_1_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_1_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_1_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_1_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_1_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_1_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_1_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_datasource_1_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>