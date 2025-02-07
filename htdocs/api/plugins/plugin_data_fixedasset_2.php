<?php

//FlowName: 分配资产

function plugin_data_fixedasset_2_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_2_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_2_add_default_data_after_submit($id)  {
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

function plugin_data_fixedasset_2_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_2_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    $数量   = intval($_POST['数量']);
    if($数量<1)    {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = "调拨数量不能小于1";
        print json_encode($RS);
        exit;
    }

    $sql    = "select * from data_fixedasset where id='$id'";
    $rs     = $db->Execute($sql);
    if($数量>intval($rs->fields['数量']))    {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = "调拨数量不能超过该资产的现有数量:".$rs->fields['数量'];
        print json_encode($RS);
        exit;
    }
    
    if($数量<intval($rs->fields['数量']))    {
        $db->BeginTrans();
        $Line       = $rs->fields;
        $Element    = $rs->fields;
        $Element['id'] = NULL;
        $sql        = "select MAX(资产编码) AS 资产编码 from data_fixedasset";
        $rs         = $db->Execute($sql);
        $资产编码    = $rs->fields['资产编码'];
        $最后五位   = substr($资产编码,-5);
        $最后五位   += 1;
        if(strlen($最后五位)==4) $最后五位 = "0".$最后五位;
        if(strlen($最后五位)==3) $最后五位 = "00".$最后五位;
        if(strlen($最后五位)==2) $最后五位 = "000".$最后五位;
        if(strlen($最后五位)==1) $最后五位 = "0000".$最后五位;
        $资产编码   = substr($资产编码,0,-5).$最后五位;
        $Element['资产编码'] = $资产编码;
        $Element['所属部门'] = $_POST['所属部门'];
        $Element['使用部门'] = $_POST['使用部门'];
        $Element['所属班级'] = $_POST['所属班级'];
        $Element['所属宿舍'] = $_POST['所属宿舍'];
        $Element['使用人员'] = $_POST['使用人员'];
        $Element['责任人员'] = $_POST['责任人员'];
        $Element['存放地点'] = $_POST['存放地点'];
        $Element['数量']    = $_POST['数量'];
        $KEYS = array_keys($Element);
        $VALUES = array_values($Element);
        $sql = "insert into data_fixedasset (".join(',',$KEYS).") values('".join("','",$VALUES)."')";
        $db->Execute($sql) or print $sql."\n";
        //修改现有资产的数量
        $剩余数量 = $Line['数量'] - $数量;
        $sql = "update data_fixedasset set 数量='$剩余数量' where id='$id' ";
        $db->Execute($sql);
        $db->CommitTrans();
        $sql = "update data_fixedasset set 金额=单价*数量";
        $db->Execute($sql);
        $RS = [];
        $RS['status'] = "OK";
        $RS['msg'] = "资产调拨成功";
        print json_encode($RS);
        exit;
    }
    else if($数量==intval($rs->fields['数量']))    {
        $Element            = [];
        $Element['id']      = $id;
        $Element['所属部门'] = $_POST['所属部门'];
        $Element['使用部门'] = $_POST['使用部门'];
        $Element['所属班级'] = $_POST['所属班级'];
        $Element['所属宿舍'] = $_POST['所属宿舍'];
        $Element['使用人员'] = $_POST['使用人员'];
        $Element['责任人员'] = $_POST['责任人员'];
        [$rs,$sql]  = InsertOrUpdateTableByArray("data_fixedasset",$Element,'id',0,'Update');
        $RS         = [];
        $RS['status'] = "OK";
        $RS['sql']  = $sql;
        $RS['msg']  = "修改资产使用对像成功";
        print json_encode($RS);
        exit;
    }

}

function plugin_data_fixedasset_2_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_2_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_2_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_2_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_2_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_fixedasset_2_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>