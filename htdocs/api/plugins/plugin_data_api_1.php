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

  $sql = "select * from form_formfield where FormId='$FormId' order by SortNumber asc, id asc";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  foreach ($rs_a as $Line) {
      $ShowTypeMap[$Line['FieldName']] = $Line['ShowType'];
  }

  $YesOrNotOptions = [];
  $YesOrNotOptions[] = ['value'=>'Yes', 'label'=>__('Yes')];
  $YesOrNotOptions[] = ['value'=>'No', 'label'=>__('No')];
  $edit_default_1 = [];
  $defaultValues_1 = [];
  foreach($ShowTypeMap as $FieldName=>$ShowTypeMapItem) {
      //$FieldName = $MetaColumnNamesTarget[$i];
      //$ShowTypeMapItem = $ShowTypeMap[$FieldName];
      if($ShowTypeMapItem!="Disable")  {
          //Check the default from the first column value
          //当第一次建立流程的时候,什么数据都是空的,这个时候需要默认为启用,如果是已经有数据,而新增加进入的字段,这个时候需要默认为禁用

          $defaultValues_1["FieldEnable_".$FieldName] = false;
          $defaultValues_1["FieldName_".$FieldName]   = $FieldName;
          $edit_default_1['Default'][] = ['name' => "FieldName_".$FieldName, 'show'=>true, 'type'=>'Readonly', 'label' => __("Field Name"), 'value' => false, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => true, 'xs'=>6, 'sm'=>4]];
          $edit_default_1['Default'][] = ['name' => "FieldEnable_".$FieldName, 'show'=>true, 'type'=>'Switch', 'label' => __("Field Enable"), 'value' => false, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>6, 'sm'=>4]];

      }
  }

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
