<?php

//FlowName: 接口API

function plugin_data_api_1_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_api_1_init_default_filter_RS($RS)  {
  global $db;
  global $SettingMap;
  global $MetaColumnNames;
  global $GLOBAL_USER;
  global $TableName;
  //Here is your write code

  global $FormId;
  $ShowTypeMap = [];
  $sql = "select * from form_formfield where FormId='$FormId' order by SortNumber asc, id asc";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  foreach ($rs_a as $Line) {
      $ShowTypeMap[$Line['FieldName']] = $Line['ShowType'];
  }

  $FormFieldSelectOptions = [];
  $FormFieldSelectOptions[] = ['value'=>'FieldTypeFollowByFormSetting', 'label'=>__('FieldTypeFollowByFormSetting')];
  $FormFieldSelectOptions[] = ['value'=>'List_Use_AddEditView_NotUse', 'label'=>__('List_Use_AddEditView_NotUse')];
  $YesOrNotOptions = [];
  $YesOrNotOptions[] = ['value'=>'Yes', 'label'=>__('Yes')];
  $YesOrNotOptions[] = ['value'=>'No', 'label'=>__('No')];
  $edit_default_1 = [];
  $defaultValues_1 = [];
  //for($i=1;$i<sizeof($MetaColumnNamesTarget);$i++)   {
  foreach($ShowTypeMap as $FieldName=>$ShowTypeMapItem) {
      //$FieldName = $MetaColumnNamesTarget[$i];
      //$ShowTypeMapItem = $ShowTypeMap[$FieldName];
      if($ShowTypeMapItem!="Disable")  {
          $defaultValues_1["FieldType_".$FieldName] = $FormFieldDefaultValue;
          $edit_default_1['Default'][] = ['name' => "FieldType_".$FieldName, 'show'=>true, 'type'=>'select', 'options'=>$FormFieldSelectOptions, 'label' => $FieldName, 'value' => $FormFieldSelectOptions[7]['value'], 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

          $defaultValues_1["FieldGroup_".$FieldName] = false;
          $edit_default_1['Default'][] = ['name' => "FieldGroup_".$FieldName, 'show'=>true, 'type'=>'Switch', 'label' => __("Field Group"), 'value' => false, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>4, 'sm'=>2]];

      }
  }

  $edit_default_1_mode = [['value'=>"Default", 'label'=>__("")]];

  $RS['edit_default_1']['allFields']      = $edit_default_1;
  $RS['edit_default_1']['allFieldsMode']  = $edit_default_1_mode;
  $RS['edit_default_1']['defaultValues']  = $defaultValues_1;
  $RS['edit_default_1']['dialogContentHeight']  = "850px";
  $RS['edit_default_1']['submitaction']  = "edit_default_1_data";
  $RS['edit_default_1']['componentsize'] = "small";
  $RS['edit_default_1']['submittext']    = __("Submit");
  $RS['edit_default_1']['canceltext']    = __("Cancel");
  $RS['edit_default_1']['titletext']    = "设置API接口返回字段信息";
  $RS['edit_default_1']['titlememo']    = "可以根据需要自主设定每个字段是否显示";
  $RS['edit_default_1']['tablewidth']   = 650;

  $RS['init_action']['IsGetStructureFromEditDefault'] = 1;

  return $RS;
}


function plugin_data_api_1_edit_default_1($id)  {
  global $db;
  global $SettingMap;
  global $MetaColumnNames;
  global $GLOBAL_USER;
  global $TableName;
  //Here is your write code

  $id     = intval(DecryptID($_GET['id']));
  $ShowTypeMap = [];
  $sql = "select * from data_api where id='$id'";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  $FormId = $rs_a[0]['FormId'];
  $Setting = $rs_a[0]['Setting'];
  $SettingArray = explode(',', $Setting);

  $sql = "select * from form_formfield where FormId='$FormId' order by SortNumber asc, id asc";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  foreach ($rs_a as $Line) {
      $ShowTypeMap[$Line['FieldName']] = $Line;
  }

  $YesOrNotOptions = [];
  $YesOrNotOptions[] = ['value'=>'Yes', 'label'=>__('Yes')];
  $YesOrNotOptions[] = ['value'=>'No', 'label'=>__('No')];
  $edit_default_1 = [];
  $defaultValues_1 = [];
  foreach($ShowTypeMap as $FieldName=>$ShowTypeMapItem) {
      //$FieldName = $MetaColumnNamesTarget[$i];
      //$ShowTypeMapItem = $ShowTypeMap[$FieldName];
      if($ShowTypeMapItem['ShowType']!="Disable")  {
          //Check the default from the first column value
          //当第一次建立流程的时候,什么数据都是空的,这个时候需要默认为启用,如果是已经有数据,而新增加进入的字段,这个时候需要默认为禁用

          $defaultValues_1["FieldName_".$FieldName] = $FieldName;
          $edit_default_1['Default'][] = ['name' => "FieldName_".$FieldName, 'show'=>true, 'type'=>'readonly', 'label' => __("字段名称"), 'value' => $FieldName, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => true, 'xs'=>4, 'sm'=>4]];

          $defaultValues_1["ShowName_".$FieldName] = $ShowTypeMapItem['ChineseName'];
          $edit_default_1['Default'][] = ['name' => "ShowName_".$FieldName, 'show'=>true, 'type'=>'readonly', 'label' => __("显示名称"), 'value' => $ShowTypeMapItem['ChineseName'], 'placeholder' => $ShowTypeMapItem['ChineseName'], 'helptext' => "", 'rules' => ['required' => true, 'disabled' => true, 'xs'=>4, 'sm'=>4]];

          $defaultValues_1["FieldEnable_".$FieldName] = in_array($FieldName, $SettingArray) ? true : false;
          $edit_default_1['Default'][] = ['name' => "FieldEnable_".$FieldName, 'show'=>true, 'type'=>'Switch', 'label' => __("是否启用"), 'value' => false, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>6, 'sm'=>4]];

      }
  }
  $defaultValues_1["Refresh_Token"] = false;
  $edit_default_1['Default'][] = ['name' => "Refresh_Token", 'show'=>true, 'type'=>'Switch', 'label' => __("刷新访问Token"), 'value' => false, 'placeholder' => __("刷新访问Token"), 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>12]];

  $edit_default_1_mode = [['value'=>"Default", 'label'=>__("")]];

  $RS = [];
  $RS['status'] = "OK";
  $RS['data']   = $defaultValues_1;
  $RS['edit_default']['allFields']      = $edit_default_1;
  $RS['edit_default']['allFieldsMode']  = $edit_default_1_mode;
  $RS['edit_default']['defaultValues']  = $defaultValues_1;
  $RS['edit_default']['dialogContentHeight']  = "850px";
  $RS['edit_default']['submitaction']  = "edit_default_1_data";
  $RS['edit_default']['componentsize'] = "small";
  $RS['edit_default']['submittext']    = __("Submit");
  $RS['edit_default']['canceltext']    = __("Cancel");
  $RS['edit_default']['titletext']    = "设置API接口返回字段信息";
  $RS['edit_default']['titlememo']    = "可以根据需要自主设定每个字段是否显示";
  $RS['edit_default']['tablewidth']   = 650;
  $RS['forceuse'] = true; //强制使用当前结构数据来渲染表单

  print json_encode($RS);
  exit;
}

function plugin_data_api_1_edit_default_1_data($id)  {
  global $db;
  global $SettingMap;
  global $MetaColumnNames;
  global $GLOBAL_USER;
  global $TableName;
  //Here is your write code

  $id     = intval(DecryptID($_GET['id']));
  $ShowTypeMap = [];
  $sql = "select * from data_api where id='$id'";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  $FormId = $rs_a[0]['FormId'];

  $FormName = "";
  $sql = "select * from form_formfield where FormId='$FormId' order by SortNumber asc, id asc";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  foreach ($rs_a as $Line) {
      $ShowTypeMap[$Line['FieldName']] = $Line['FormName'];
      $FormName = $Line['FormName'];
  }

  $需要返回的字段列表 = [];
  $字段列表 = array_keys($ShowTypeMap);
  foreach($字段列表 as $字段名称) {
    if($_POST['FieldEnable_'.$字段名称] == 1) {
      $需要返回的字段列表[] = $字段名称;
    }
  }

  //print_R($需要返回的字段列表);
  $sql = "update data_api set Setting='".join(',',$需要返回的字段列表)."' where id='$id'";
  $rs = $db->Execute($sql);

  if($_POST['Refresh_Token'] == 1)  {
    $sql = "update data_api set Token='".strtoupper(md5(date('Y-m-d H:i:s')."Dandian.net"))."' where id='$id'";
    $rs = $db->Execute($sql);
  }

  $RS = [];
  $RS['status'] = "OK";
  $RS['msg'] = __("保存成功");
  $RS['_GET'] = $_GET;
  $RS['_POST'] = $_POST;
  print json_encode($RS);
  exit;

}

function plugin_data_api_1_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_api_1_add_default_data_after_submit($id)  {
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

function plugin_data_api_1_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_api_1_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_api_1_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_api_1_edit_default_configsetting_data($id)  {
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

function plugin_data_api_1_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code

    //$id     = intval(DecryptID($_GET['id']));
    $ShowTypeMap = [];
    $sql = "select * from data_api where id='$id'";
    $rs = $db->Execute($sql);
    $rs_a = $rs->GetArray();
    $FormId = $rs_a[0]['FormId'];
    $Setting = $rs_a[0]['Setting'];
    $SettingArray = explode(',', $Setting);
    $EditValue = $rs_a[0];

    $RS = [];
    $RS['status'] = "OK";
    $RS['data']   = $EditValue;
    $RS['sql']    = $sql;
    $RS['msg']    = __("Get Data Success");

    $FieldNameArray             = array_keys($EditValue);
    $ApprovalNodeFieldsHidden   = ['id','FormId','ErrorAccess','TotalAccess',];
    for($X=0;$X<sizeof($FieldNameArray);$X=$X+1)        {
        $FieldName1 = $FieldNameArray[$X];
        $RowData = [];
        if(!in_array($FieldName1,$ApprovalNodeFieldsHidden) && $FieldName1!="") {
            $RowData[0]['Name']         = $FieldName1;
            $RowData[0]['Value']        = $EditValue[$FieldName1];
            $RowData[0]['FieldArray']   = ['name'=>$FieldName1,'label'=>__($FieldName1),'value'=>$EditValue[$FieldName1],'type'=>'input'];
        }
        if(sizeof($RowData)>0) {
            $NewTableRowData[] = $RowData;
        }
    }

    $Datetime = time();

    $NewTableRowData    = [];
    $NewTableRowData[][0]  = ['Name'=>'API接口名称', 'Value'=>$EditValue['ApiName'], 'FieldArray'=>['name'=>'ApiName','label'=>'API接口名称','value'=>$EditValue['ApiName'],'type'=>'input']];
    $NewTableRowData[][0]  = ['Name'=>'API接口URL', 'Value'=>"https://".$_SERVER['HTTP_HOST']."/api/api.php", 'FieldArray'=>['name'=>'API接口URL','label'=>'API接口URL','value'=>$_SERVER['HTTP_HOST'],'type'=>'input']];
    $NewTableRowData[][0]  = ['Name'=>'Token', 'Value'=>$EditValue['Token'], 'FieldArray'=>['name'=>'Token','label'=>'Token','value'=>$EditValue['Token'],'type'=>'input']];
    $NewTableRowData[][0]  = ['Name'=>'Post提交字段1', 'Value'=>"字段: Model 固定值: ".base64_encode(base64_encode($EditValue['id'])), 'FieldArray'=>['name'=>'Token','label'=>'Post提交字段1','value'=>"",'type'=>'input']];
    $NewTableRowData[][0]  = ['Name'=>'Post提交字段2', 'Value'=>"字段: Page 默认值: 0, 获取下一页数据把这个值加1", 'FieldArray'=>['name'=>'Page','label'=>'Post提交字段2','value'=>"字段: Page 默认值: 0, 获取下一页数据把这个值加1",'type'=>'input']];
    $NewTableRowData[][0]  = ['Name'=>'Authorization', 'Value'=>"Http Header 中增加 Authorization的变量,来做为API授权. 这个值就是签名, 具体计算方法如下: md5(\$Datetime.\"|\".\$Token.\"|\".\$Model.\"|\".\$Page) \$Datetime: 为当前时间戳,精确到秒, \$Page: 指定页码, 从0开始.", 'FieldArray'=>['name'=>'Authorization','label'=>'Header Authorization','value'=>"Http Header 中增加 Authorization的变量,来做为API授权.",'type'=>'input']];
    $NewTableRowData[][0]  = ['Name'=>'签名计算过程DEMO', 'Value'=>"\$Datetime: ".$Datetime."\n \$Token: ".$EditValue['Token']."\n \$Model: ".base64_encode(base64_encode($EditValue['id']))."\n \$Page: 0\n 签名计算算法: md5('".$Datetime."|".$EditValue['Token']."|".base64_encode(base64_encode($EditValue['id']))."|0')\n 签名计算结果: ".md5($Datetime."|".$EditValue['Token']."|".base64_encode(base64_encode($EditValue['id']))."|0"), 'FieldArray'=>['name'=>'签名计算过程DEMO','label'=>'签名计算过程DEMO','value'=>"Http Header 中增加 Authorization的变量,来做为API授权.",'type'=>'input']];
    $NewTableRowData[][0]  = ['Name'=>'ExpireTime', 'Value'=>$EditValue['ExpireTime'], 'FieldArray'=>['name'=>'ExpireTime','label'=>'Authorization过期时间','value'=>$EditValue['ExpireTime'],'type'=>'input']];
    //$NewTableRowData[][0]  = ['Name'=>'IP白名单', 'Value'=>$EditValue['IpWhiteList'], 'FieldArray'=>['name'=>'IP白名单','label'=>'IP白名单','value'=>$EditValue['IpWhiteList'],'type'=>'input']];
    //$NewTableRowData[][0]  = ['Name'=>'IP黑名单', 'Value'=>$EditValue['IpBlackList'], 'FieldArray'=>['name'=>'IP黑名单','label'=>'IP黑名单','value'=>$EditValue['IpBlackList'],'type'=>'input']];
    $NewTableRowData[][0]  = ['Name'=>'示例代码', 'Value'=>"https://".$_SERVER['HTTP_HOST']."/api/api_demo.php", 'FieldArray'=>['name'=>'示例代码','label'=>'示例代码','value'=>'','type'=>'input']];

    $RS['newTableRowData']          = $NewTableRowData;

    print json_encode($RS);
    exit;
}

function plugin_data_api_1_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_api_1_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_api_1_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_api_1_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>
