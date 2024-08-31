<?php

//FlowName: 采购员入库

function plugin_data_fixedasset_in_9_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_in_9_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_in_9_add_default_data_after_submit($id)  {
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

function plugin_data_fixedasset_in_9_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_in_9_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_in_9_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    //进行资产入库操作.
    $db->BeginTrans();
    $sql    = "select * from data_fixedasset_in where id='$id'";
    $rs     = $db->Execute($sql);
    $资产采购编码 = $rs->fields['资产采购编码'];
    $sql    = "select * from data_fixedasset_in_detail where 资产采购编码='$资产采购编码' and 采购状态='资产入库' and 入库时间='' ";//
    $rs     = $db->Execute($sql);
    $rs_a   = $rs->GetArray();
    foreach($rs_a AS $Line)     {
        //得到最大的资产编码
        $sql        = "select MAX(资产编码) AS 资产编码 from data_fixedasset";
        $rs         = $db->Execute($sql);
        $资产编码   = intval($rs->fields['资产编码']);
        if($资产编码==0) {
            $资产编码 = 100000;
        }
        $最后五位   = substr($资产编码,-5);
        
        $最后五位   += 1;
        if(strlen($最后五位)==4) $最后五位 = "0".$最后五位;
        if(strlen($最后五位)==3) $最后五位 = "00".$最后五位;
        if(strlen($最后五位)==2) $最后五位 = "000".$最后五位;
        if(strlen($最后五位)==1) $最后五位 = "0000".$最后五位;
        $资产编码   = substr($资产编码,0,-5).$最后五位;
        $Element = [];
        $Element['资产状态'] = "购置未分配";
        $Element['维修状态'] = "正常";
        $Element['资产来源'] = "自购";
        $Element['资产编码'] = $资产编码;
        $Element['资产名称'] = $Line['资产名称'];
        $Element['分类代码'] = $Line['分类代码'];
        $Element['分类名称'] = $Line['分类名称'];
        $Element['数量'] = $Line['数量'];
        $Element['单价'] = $Line['单价'];
        $Element['金额'] = $Line['金额'];
        $Element['单位'] = $Line['单位'];
        $Element['使用方向'] = $Line['使用方向'];
        $Element['供应商名称'] = $Line['供应商名称'];

        $sql = "select * from data_fixedasset_provider where 供应商名称='".ForSqlInjection($Element['供应商名称'])."'";
        $rsT = $db->Execute($sql);
        $Element['供应商联系人']    = $rsT->fields['供应商联系人'];
        $Element['供应商联系方式']  = $rsT->fields['供应商联系方式'];
        $Element['供应商网站']      = $rsT->fields['供应商网站'];

        $Element['资产采购编码'] = $Line['资产采购编码'];
        $Element['购买方式'] = $Line['购买方式'];
        $Element['创建人'] = $GLOBAL_USER->USER_ID;
        $Element['创建时间'] = date("Y-m-d H:i:s");
        $KEYS = array_keys($Element);
        $VALUES = array_values($Element);
        $sql = "insert into data_fixedasset (".join(',',$KEYS).") values('".join("','",$VALUES)."')";
        $db->Execute($sql) or print $sql."\n";
        $sql = "update data_fixedasset_in_detail set 入库时间='".date("Y-m-d H:i:s")."',入库操作员='".$GLOBAL_USER->USER_ID."' where id='".$Line['id']."' ";
        $db->Execute($sql);
    }    
    $sql    = "select * from data_fixedasset_in_detail where 资产采购编码='$资产采购编码'";//
    $rs     = $db->Execute($sql);
    $rs_a   = $rs->GetArray();
    foreach($rs_a AS $Line)     {
        $Element['供应商名称'] = $Line['供应商名称'];
        $sql = "select * from data_fixedasset_provider where 供应商名称='".ForSqlInjection($Element['供应商名称'])."'";
        $rsT = $db->Execute($sql);
        $sql = "update data_fixedasset_in_detail set 供应商联系人='".$rsT->fields['供应商联系人']."',供应商联系方式='".$rsT->fields['供应商联系方式']."',供应商网站='".$rsT->fields['供应商网站']."' where id='".$Line['id']."' ";
        $db->Execute($sql);
    }    
    $db->CommitTrans();
}

function plugin_data_fixedasset_in_9_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_in_9_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_in_9_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_fixedasset_in_9_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_fixedasset_in_9_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>