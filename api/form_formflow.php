<?php
header("Content-Type: application/json");
require_once('cors.php');
require_once('include.inc.php');

CheckAuthUserLoginStatus();
CheckAuthUserRoleHaveMenu(0, "/form/formname");

$TableName      = "form_formflow";

$externalId     = intval($_REQUEST['externalId']);
$id             = ParamsFilter($_REQUEST['id']);
$selectedRows   = ParamsFilter($_REQUEST['selectedRows']);
if($externalId==""&&$id!="")    {
    $externalId = returntablefield("form_formflow","id",$id,"FormId")['FormId'];
}
if($externalId==""&&$selectedRows!="")    {
    $selectedRowsArray = explode(',',$selectedRows);
    $externalId = returntablefield("form_formflow","id",$selectedRowsArray[0],"FormId")['FormId'];
}
$FormInfor = returntablefield("form_formname","id",$externalId,"TableName,FullName");
$TableNameTarget = $FormInfor['TableName'];
$ShortNameTarget = $FormInfor['FullName'];
if($TableNameTarget=="")  {
    $RS = [];
    $RS['init_default']['data'] = [];
    $RS['init_default']['total'] = [];
    $RS['init_default']['params'] = [];
    $RS['init_default']['filter'] = [];
    $RS['init_default']['button_search']    = "";
    $RS['init_default']['button_add']       = "";
    $RS['init_default']['columns'] = [];
    $RS['add_default'] = [];
    $RS['edit_default'] = [];
    $RS['view_default'] = [];
    $RS['export_default'] = [];
    $RS['import_default'] = [];
    $RS['status'] = "ERROR";
    $RS['msg'] = "Missing externalId(FormId) value";
    print json_encode($RS);
    exit;
}

$columnNames = [];
$sql = "show columns from form_formflow";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $columnNames[] = $Line['Field'];
}

$ShowTypeMap = [];
$sql = "select * from form_formfield where FormId='$externalId' order by SortNumber asc, id asc";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $ShowTypeMap[$Line['FieldName']] = $Line['ShowType'];
}

//新建页面时的启用字段列表
$FaceToOptions = [];
$FaceToOptions[] = ['value'=>'AuthUser', 'label'=>__('AuthUser')];
$FaceToOptions[] = ['value'=>'AnonymousUser', 'label'=>__('AnonymousUser')];
$FaceToOptions[] = ['value'=>'Student', 'label'=>__('Student')];
//$FaceToOptions[] = ['value'=>'Parent', 'label'=>__('Parent')];
$allFieldsAdd = [];
$allFieldsAdd['Default'][] = ['name' => 'FlowName', 'show'=>true, 'type'=>'input', 'label' => __('FlowName'), 'value' => '', 'placeholder' => 'FlowName', 'helptext' => 'FlowName', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12, 'disabled' => false]];
$allFieldsAdd['Default'][] = ['name' => 'FaceTo', 'show'=>true, 'type'=>'select', 'options'=>$FaceToOptions, 'label' => __('FaceTo'), 'value' => 'AuthUser', 'placeholder' => '', 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>12]];

foreach($allFieldsAdd as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValues[$ITEM['name']] = $ITEM['value'];
    }
}

//编辑页面时的启用字段列表
$allFieldsEdit = $allFieldsAdd;

if( ($_GET['action']=="add_default_data") && $_POST['FlowName']!="" && $externalId!="")  {
    $MetaColumnNames    = $db->MetaColumnNames($TableName);
    $MetaColumnNames    = array_values($MetaColumnNames);
    $FieldsArray                    = [];
    $FieldsArray['FormId']          = $externalId;
    $sql        = "select max(Step) as Step from $TableName where FormId='".$externalId."'";
    $rsf        = $db->Execute($sql);
    $Step       = $rsf->fields['Step'];
    $Step       = $Step + 1;
    $FieldsArray['Step']            = $Step;
    $FieldsArray['FlowName']        = $_POST['FlowName'];
    $FieldsArray['FaceTo']          = $_POST['FaceTo'];
    $FieldsArray['Setting']         = base64_encode(serialize(['FaceTo'=>$_POST['FaceTo']]));
    $FieldsArray['Creator']         = "admin";
    $FieldsArray['CreateTime']      = date("Y-m-d H:i:s");
    if(1)   {
        [$rs,$sql] = InsertOrUpdateTableByArray($TableName,$FieldsArray,"FormId,Step",0,"Insert");
        if($rs->EOF) {
            $RS['status'] = "OK";
            $RS['msg'] = __("Submit Success");
            print json_encode($RS);
            exit;
        }
        else {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['msg'] = __("sql execution failed");
            $RS['sql'] = $sql;
            $RS['_POST'] = $_POST;
            print json_encode($RS);
            exit;
        }
    }
}
//#########################################################################################################################
//Edit Define Initial######################################################################################################
//#########################################################################################################################
$MetaColumnNamesTarget    = $db->MetaColumnNames($TableNameTarget);
$MetaColumnNamesTarget    = array_values($MetaColumnNamesTarget);
$MetaColumnNamesOptions = [];
$MetaColumnNamesOptionsOnlyShowStatus = [];
$MetaColumnNamesOptionsOnlyShowStatus[] = ['value'=>"Disabled", 'label'=>__("Disabled")];
$MetaColumnNamesOptionsOnlyshowPerson = [];
$MetaColumnNamesOptionsOnlyshowPerson[] = ['value'=>"Disabled", 'label'=>__("Disabled")];
$MetaColumnNamesOptionsOnlyShowDateTime = [];
$MetaColumnNamesOptionsOnlyShowDateTime[] = ['value'=>"Disabled", 'label'=>__("Disabled")];
$MetaColumnNamesOptionsOnlyShowOpinion = [];
$MetaColumnNamesOptionsOnlyShowOpinion[] = ['value'=>"Disabled", 'label'=>__("Disabled")];
$MetaColumnNamesOptionsAll = [];
$MetaColumnNamesOptionsAll[] = ['value'=>"Disabled", 'label'=>__("Disabled")];
foreach($MetaColumnNamesTarget AS $Item) {
    if($Item!="id")   {
        $MetaColumnNamesOptions[] = ['value'=>$Item, 'label'=>$Item];
    }
    $MetaColumnNamesOptionsAll[] = ['value'=>$Item, 'label'=>$Item];
    if(strpos($Item,'提交状态')!==false)   {
        $MetaColumnNamesOptionsOnlyShowStatus[] = ['value'=>$Item, 'label'=>$Item];
    }
    if(strpos($Item,'审核状态')>0)   {
        $MetaColumnNamesOptionsOnlyShowStatus[] = ['value'=>$Item, 'label'=>$Item];
    }
    if(strpos($Item,'审核人')>0 || strpos($Item,'用户名')>0)   {
        $MetaColumnNamesOptionsOnlyshowPerson[] = ['value'=>$Item, 'label'=>$Item];
    }
    if(strpos($Item,'审核时间')>0)   {
        $MetaColumnNamesOptionsOnlyShowDateTime[] = ['value'=>$Item, 'label'=>$Item];
    }
    if(strpos($Item,'审核意见')>0)   {
        $MetaColumnNamesOptionsOnlyShowOpinion[] = ['value'=>$Item, 'label'=>$Item];
    }
}
$YesOrNotOptions = [];
$YesOrNotOptions[] = ['value'=>'Yes', 'label'=>__('Yes')];
$YesOrNotOptions[] = ['value'=>'No', 'label'=>__('No')];

//#########################################################################################################################
//Field Type###############################################################################################################
//#########################################################################################################################
if($_GET['action']=="edit_default_1"&&$id!='')         {
    $sql    = "select * from form_formflow where id = '$id'";
    $rs     = $db->Execute($sql);
    $FlowName   = $rs->fields['FlowName'];
    $Setting    = $rs->fields['Setting'];
    $SettingMap = unserialize(base64_decode($Setting));
}

$FormFieldSelectOptions = [];
$FormFieldSelectOptions[] = ['value'=>'FieldTypeFollowByFormSetting', 'label'=>__('FieldTypeFollowByFormSetting')];
$FormFieldSelectOptions[] = ['value'=>'List_Use_AddEditView_NotUse', 'label'=>__('List_Use_AddEditView_NotUse')];
$FormFieldSelectOptions[] = ['value'=>'ListView_Use_AddEdit_NotUse', 'label'=>__('ListView_Use_AddEdit_NotUse')];
$FormFieldSelectOptions[] = ['value'=>'View_Use_ListAddEdit_NotUse', 'label'=>__('View_Use_ListAddEdit_NotUse')];
$FormFieldSelectOptions[] = ['value'=>'ListAddView_Use_Edit_Readonly', 'label'=>__('ListAddView_Use_Edit_Readonly')];
$FormFieldSelectOptions[] = ['value'=>'ListView_Use_AddEdit_Readonly', 'label'=>__('ListView_Use_AddEdit_Readonly')];
$FormFieldSelectOptions[] = ['value'=>'ListAddEdit_Use_View_NotUse', 'label'=>__('ListAddEdit_Use_View_NotUse')];
$FormFieldSelectOptions[] = ['value'=>'Disable', 'label'=>__('Disable')];
$FormFieldSelectOptions[] = ['value'=>'HiddenUserID', 'label'=>__('HiddenUserID')];
$FormFieldSelectOptions[] = ['value'=>'HiddenUsername', 'label'=>__('HiddenUsername')];
$FormFieldSelectOptions[] = ['value'=>'HiddenDeptID', 'label'=>__('HiddenDeptID')];
$FormFieldSelectOptions[] = ['value'=>'HiddenDeptName', 'label'=>__('HiddenDeptName')];
$FormFieldSelectOptions[] = ['value'=>'HiddenStudentID', 'label'=>__('HiddenStudentID')];
$FormFieldSelectOptions[] = ['value'=>'HiddenStudentName', 'label'=>__('HiddenStudentName')];
$FormFieldSelectOptions[] = ['value'=>'HiddenStudentClass', 'label'=>__('HiddenStudentClass')];
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
        //Check the default from the first column value
        //当第一次建立流程的时候,什么数据都是空的,这个时候需要默认为启用,如果是已经有数据,而新增加进入的字段,这个时候需要默认为禁用
        if($SettingMap["FieldType_".$MetaColumnNamesTarget[1]]!="Disable" && $SettingMap["FieldType_".$MetaColumnNamesTarget[1]]!="")  {
            $FormFieldDefaultValue = $FormFieldSelectOptions[7]['value'];
        }
        else {
            //First initial, default enable
            $FormFieldDefaultValue = $FormFieldSelectOptions[0]['value'];
        }
        //print_R($FormFieldDefaultValue);
        //$edit_default_1['Default'][] = ['FieldName'=>$FieldName,'FieldType'=>"FieldTypeFollowByFormSetting",'FieldGoup'=>"No",'FieldSearch'=>"No",'FieldImport'=>"No"];
        //$FormFieldDefaultValue = $FormFieldSelectOptions[0]['value'];
        if(strpos($FieldName,"审核状态")>0) {
            $FormFieldDefaultValue = "ListView_Use_AddEdit_NotUse";
        }
        elseif(strpos($FieldName,"审核时间")>0) {
            $FormFieldDefaultValue = "ListView_Use_AddEdit_NotUse";
        }
        elseif(strpos($FieldName,"审核人")>0) {
            $FormFieldDefaultValue = "ListView_Use_AddEdit_NotUse";
        }
        elseif(strpos($FieldName,"审核意见")>0) {
            $FormFieldDefaultValue = "ListView_Use_AddEdit_NotUse";
        }
        elseif(strpos($FieldName,"申请人")>0) {
            $FormFieldDefaultValue = "ListView_Use_AddEdit_NotUse";
        }
        elseif(strpos($FieldName,"申请状态")>0) {
            $FormFieldDefaultValue = "ListView_Use_AddEdit_NotUse";
        }
        elseif(strpos($FieldName,"提交状态")>0) {
            $FormFieldDefaultValue = "ListView_Use_AddEdit_NotUse";
        }
        elseif(strpos($FieldName,"申请时间")>0) {
            $FormFieldDefaultValue = "ListView_Use_AddEdit_NotUse";
        }


        $defaultValues_1["FieldType_".$FieldName] = $FormFieldDefaultValue;
        $edit_default_1['Default'][] = ['name' => "FieldType_".$FieldName, 'show'=>true, 'type'=>'select', 'options'=>$FormFieldSelectOptions, 'label' => $FieldName, 'value' => $FormFieldSelectOptions[7]['value'], 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

        if(in_array($FieldName,["学期","学期名称","班级","班级名称","课程","课程名称"])) {
            $defaultValues_1["FieldGroup_".$FieldName] = true;
        }
        else {
            $defaultValues_1["FieldGroup_".$FieldName] = false;
        }
        $edit_default_1['Default'][] = ['name' => "FieldGroup_".$FieldName, 'show'=>true, 'type'=>'Switch', 'label' => __("Field Group"), 'value' => false, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>4, 'sm'=>2]];

        $defaultValues_1["FieldSearch_".$FieldName] = true;
        $edit_default_1['Default'][] = ['name' => "FieldSearch_".$FieldName, 'show'=>true, 'type'=>'Switch', 'label' => __("Search"), 'value' => false, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>4, 'sm'=>1]];

        $defaultValues_1["FieldImport_".$FieldName] = true;
        $edit_default_1['Default'][] = ['name' => "FieldImport_".$FieldName, 'show'=>true, 'type'=>'Switch', 'label' => __("Import"), 'value' => false, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>4, 'sm'=>1]];

        $defaultValues_1["FieldExport_".$FieldName] = true;
        $edit_default_1['Default'][] = ['name' => "FieldExport_".$FieldName, 'show'=>true, 'type'=>'Switch', 'label' => __("Export"), 'value' => false, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>4, 'sm'=>1]];

        $defaultValues_1["FieldEditable_".$FieldName] = false;
        $edit_default_1['Default'][] = ['name' => "FieldEditable_".$FieldName, 'show'=>true, 'type'=>'Switch', 'label' => __("List Editable"), 'value' => false, 'placeholder' => $FieldName, 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>4, 'sm'=>1]];
    }
}

$edit_default_1_mode = [['value'=>"Default", 'label'=>__("")]];

if($_GET['action']=="edit_default_1"&&$id!='')         {
    $sql    = "select * from form_formflow where id = '$id'";
    $rs     = $db->Execute($sql);
    $FlowName   = $rs->fields['FlowName'];
    $Setting    = $rs->fields['Setting'];
    $SettingMap = unserialize(base64_decode($Setting));
    if(is_array($SettingMap))   {
        $defaultValues_1_keys = array_keys($defaultValues_1);
        foreach($SettingMap as $value => $label)  {
            // && substr($value,0,strlen("FieldImport_"))!="FieldImport_"
            if(in_array($value, $defaultValues_1_keys))  {
                $defaultValues_1[$value] = $label;
            }
        }
    }
    $edit_default['allFields']      = $edit_default_1;
    $edit_default['allFieldsMode']  = $edit_default_1_mode;
    $edit_default['defaultValues']  = $defaultValues_1;
    $edit_default['dialogContentHeight']  = "90%";
    $edit_default['componentsize']  = "small";
    $edit_default['submitaction']   = "edit_default_1_data";
    $edit_default['submittext']     = __("Submit");
    $edit_default['canceltext']     = __("Cancel");
    $edit_default['titletext']      = __("Design Form Field Type");
    $edit_default['titlememo']      = __("Manage All Form Fields in Table");
    $edit_default['tablewidth']     = 550;

    $RS['edit_default'] = $edit_default;
    $RS['status'] = "OK";
    $RS['data'] = $defaultValues_1;
    $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    print_R(json_encode($RS));
    exit;
}

//#########################################################################################################################
//Interface################################################################################################################
//#########################################################################################################################
$edit_default_2 = [];

$sql    = "select MenuOneName from data_menuone order by SortNumber asc, MenuOneName asc";
$rsf    = $db->Execute($sql);
$rsf_a  = $rsf->GetArray();
$MenuOneNameArray = [];
foreach($rsf_a as $Item)  {
    $MenuOneNameArray[] = ['value'=>$Item['MenuOneName'],'label'=>$Item['MenuOneName']];
}
$edit_default_2['Menu_Location'][] = ['name' => "Menu_One", 'show'=>true, 'type'=>'select', 'options'=>$MenuOneNameArray, 'label' => __("Menu_One"), 'value' => $MetaColumnNamesOptionsAll[1]['value'], 'placeholder' => "", 'helptext' => __("Allow_Repeat"), 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Menu_Location'][] = ['name' => "Menu_Two", 'show'=>true, 'type'=>"input", 'label' => __("Menu_Two"), 'value' => "", 'placeholder' => "", 'helptext' => __("Allow_Repeat"), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['Menu_Location'][] = ['name' => "Menu_Three", 'show'=>true, 'type'=>"input", 'label' => __("Menu_Three"), 'value' => "", 'placeholder' => "", 'helptext' => __("Optional"), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['Menu_Location'][] = ['name' => "FaceTo", 'show'=>true, 'type'=>'select', 'options'=>$FaceToOptions, 'label' => __("Face_To"), 'value' => "AuthUser", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Menu_Location'][] = ['name' => "Menu_Three_Icon", 'show'=>true, 'type'=>'autocompletemdi', 'options'=>[], 'label' => __("Menu_Three_Icon"), 'value' => "account-outline", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$MenuTab_Options = [];
$MenuTab_Options[] = ['value'=>'Yes', 'label'=>__('Yes')];
$MenuTab_Options[] = ['value'=>'No', 'label'=>__('No')];
$edit_default_2['Menu_Location'][] = ['name' => "MenuTab", 'show'=>true, 'type'=>'select', 'options'=>$MenuTab_Options, 'label' => __("Menu_Tab"), 'value' => "Yes", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];


$edit_default_2['Tip_In_Interface'][] = ['name' => "List_Title_Name", 'show'=>true, 'type'=>"input", 'label' => __("List_Title_Name"), 'value' => $ShortNameTarget.__("List"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Import_Title_Name", 'show'=>true, 'type'=>"input", 'label' => __("Import_Title_Name"), 'value' => __("Import")."".$ShortNameTarget, 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Export_Title_Name", 'show'=>true, 'type'=>"input", 'label' => __("Export_Title_Name"), 'value' => __("Export")."".$ShortNameTarget, 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Add_Title_Name", 'show'=>true, 'type'=>"input", 'label' => __("Add_Title_Name"), 'value' => __("Add")."".$ShortNameTarget, 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Add_Subtitle_Name", 'show'=>true, 'type'=>"input", 'label' => __("Add_Subtitle_Name"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>8, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Edit_Title_Name", 'show'=>true, 'type'=>"input", 'label' => __("Edit_Title_Name"), 'value' => __("Edit")."".$ShortNameTarget, 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Edit_Subtitle_Name", 'show'=>true, 'type'=>"input", 'label' => __("Edit_Subtitle_Name"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>8, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "View_Title_Name", 'show'=>true, 'type'=>"input", 'label' => __("View_Title_Name"), 'value' => __("View")."".$ShortNameTarget, 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "View_Subtitle_Name", 'show'=>true, 'type'=>"input", 'label' => __("View_Subtitle_Name"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>8, 'disabled' => false]];

$edit_default_2['Tip_In_Interface'][] = ['name' => "Rename_Add_Submit_Button", 'show'=>true, 'type'=>"input", 'label' => __("Rename_Add_Submit_Button"), 'value' => __("Submit"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>3, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Rename_Edit_Submit_Button", 'show'=>true, 'type'=>"input", 'label' => __("Rename_Edit_Submit_Button"), 'value' => __("Submit"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>3, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Rename_List_Add_Button", 'show'=>true, 'type'=>"input", 'label' => __("Rename_List_Add_Button"), 'value' => __("Add"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Rename_List_Edit_Button", 'show'=>true, 'type'=>"input", 'label' => __("Rename_List_Edit_Button"), 'value' => __("Edit"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Rename_List_Delete_Button", 'show'=>true, 'type'=>"input", 'label' => __("Rename_List_Delete_Button"), 'value' => __("Delete"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Rename_List_Import_Button", 'show'=>true, 'type'=>"input", 'label' => __("Rename_List_Import_Button"), 'value' => __("Import"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Rename_List_Export_Button", 'show'=>true, 'type'=>"input", 'label' => __("Rename_List_Export_Button"), 'value' => __("Export"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Rename_Import_Submit_Button", 'show'=>true, 'type'=>"input", 'label' => __("Rename_Import_Submit_Button"), 'value' => __("Import"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_2['Tip_In_Interface'][] = ['name' => "Tip_When_Add_Success", 'show'=>true, 'type'=>"input", 'label' => __("Tip_When_Add_Success"), 'value' => __("Add Data Success!"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Tip_When_Edit_Success", 'show'=>true, 'type'=>"input", 'label' => __("Tip_When_Edit_Success"), 'value' => __("Edit Data Success!"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Tip_When_Delete_Success", 'show'=>true, 'type'=>"input", 'label' => __("Tip_When_Delete_Success"), 'value' => __("Delete Data Success!"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Tip_Title_When_Delete", 'show'=>true, 'type'=>"input", 'label' => __("Tip_Title_When_Delete"), 'value' => __("Delete Item"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Tip_Content_When_Delete", 'show'=>true, 'type'=>"input", 'label' => __("Tip_Content_When_Delete"), 'value' => __("Do you really want to delete this item? This operation will delete table and data in Database."), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Tip_Button_When_Delete", 'show'=>true, 'type'=>"input", 'label' => __("Tip_Button_When_Delete"), 'value' => __("Confirm Delete"), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];

$Rules_When_Import = [];
$Rules_When_Import[] = ['value'=>"Import_And_Export", 'label'=>__("Import_And_Export")];
$Rules_When_Import[] = ['value'=>"Only_Import", 'label'=>__("Only_Import")];
$Rules_When_Import[] = ['value'=>"Only_Export", 'label'=>__("Only_Export")];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Rules_When_Import", 'show'=>true, 'type'=>'select', 'options'=>$Rules_When_Import, 'label' => __("Rules_When_Import"), 'value' => $Rules_When_Import[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

$Page_Number_In_List = [];
$Page_Number_In_List[] = ['value'=>10, 'label'=>10];
$Page_Number_In_List[] = ['value'=>20, 'label'=>20];
$Page_Number_In_List[] = ['value'=>30, 'label'=>30];
$Page_Number_In_List[] = ['value'=>40, 'label'=>40];
$Page_Number_In_List[] = ['value'=>50, 'label'=>50];
$Page_Number_In_List[] = ['value'=>100, 'label'=>100];
$Page_Number_In_List[] = ['value'=>200, 'label'=>200];
$Page_Number_In_List[] = ['value'=>500, 'label'=>500];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Page_Number_In_List", 'show'=>true, 'type'=>'select', 'options'=>$Page_Number_In_List, 'label' => __("Page_Number_In_List"), 'value' => $Page_Number_In_List[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

$Actions_In_List_Header = [];
$Actions_In_List_Header[] = ['value'=>"Add", 'label'=>__("Add")];
$Actions_In_List_Header[] = ['value'=>"Export", 'label'=>__("Export")];
$Actions_In_List_Header[] = ['value'=>"Import", 'label'=>__("Import")];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Actions_In_List_Header", 'show'=>true, 'type'=>'checkbox', 'options'=>$Actions_In_List_Header, 'label' => __("Actions_In_List_Header"), 'value' => "Add,Import,Export", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'row'=>true, 'xs'=>12, 'sm'=>6]];

$Actions_In_List_Row = [];
$Actions_In_List_Row[] = ['value'=>"Edit", 'label'=>__("Edit")];
$Actions_In_List_Row[] = ['value'=>"Delete", 'label'=>__("Delete")];
$Actions_In_List_Row[] = ['value'=>"View", 'label'=>__("View")];
$edit_default_2['Tip_In_Interface'][] = ['name' => "Actions_In_List_Row", 'show'=>true, 'type'=>'checkbox', 'options'=>$Actions_In_List_Row, 'label' => __("Actions_In_List_Row"), 'value' => "Edit,Delete,View", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'row'=>true, 'xs'=>12, 'sm'=>6]];


$Page_Role_Array = [];
$Page_Role_Array[] = ['value'=>"None", 'label'=>__("None")];
$Page_Role_Array[] = ['value'=>"Student", 'label'=>__("Student")];
//$Page_Role_Array[] = ['value'=>"Parent", 'label'=>__("Parent")];
$Page_Role_Array[] = ['value'=>"ClassMaster", 'label'=>__("ClassMaster")];
$Page_Role_Array[] = ['value'=>"ClassTeacher", 'label'=>__("ClassTeacher")];
$Page_Role_Array[] = ['value'=>"Faculty", 'label'=>__("Faculty")];
$Page_Role_Array[] = ['value'=>"Dormitory", 'label'=>__("Dormitory")];
$Page_Role_Array[] = ['value'=>"Department", 'label'=>__("Department")];
$Page_Role_Array[] = ['value'=>"Vice-president", 'label'=>__("Vice-president")];
$Page_Role_Array[] = ['value'=>"President", 'label'=>__("President")];

$Extra_Priv_Filter_Method = [];
$Extra_Priv_Filter_Method[] = ['value'=>"=", 'label'=>__("=")];
$Extra_Priv_Filter_Method[] = ['value'=>"!=", 'label'=>__("!=")];
$Extra_Priv_Filter_Method[] = ['value'=>">", 'label'=>__(">")];
$Extra_Priv_Filter_Method[] = ['value'=>">=", 'label'=>__(">=")];
$Extra_Priv_Filter_Method[] = ['value'=>"<", 'label'=>__("<")];
$Extra_Priv_Filter_Method[] = ['value'=>"<=", 'label'=>__("<=")];
$Extra_Priv_Filter_Method[] = ['value'=>"in", 'label'=>__("in")];
$Extra_Priv_Filter_Method[] = ['value'=>"not in", 'label'=>__("not in")];
$Extra_Priv_Filter_Method[] = ['value'=>"like", 'label'=>__("like")];
$Extra_Priv_Filter_Method[] = ['value'=>"Today", 'label'=>__("Today")];
$Extra_Priv_Filter_Method[] = ['value'=>"<->", 'label'=>__("<->")];
$Extra_Priv_Filter_Method[] = ['value'=>"BeforeDays", 'label'=>__("BeforeDays")];
$Extra_Priv_Filter_Method[] = ['value'=>"AfterDays", 'label'=>__("AfterDays")];
$Extra_Priv_Filter_Method[] = ['value'=>"BeforeAndAfterDays", 'label'=>__("BeforeAndAfterDays")];
$Extra_Priv_Filter_Method[] = ['value'=>"CurrentSemester", 'label'=>__("CurrentSemester")];

$Faculty_Filter_Field = [];
$Faculty_Filter_Field[] = ['value'=>"None", 'label'=>__("None")];
$Faculty_Filter_Field[] = ['value'=>"学籍二级管理", 'label'=>__("学籍二级管理")];
$Faculty_Filter_Field[] = ['value'=>"学生请假二级管理", 'label'=>__("学生请假二级管理")];
$Faculty_Filter_Field[] = ['value'=>"奖惩补助二级管理", 'label'=>__("奖惩补助二级管理")];
$Faculty_Filter_Field[] = ['value'=>"教学计划二级管理", 'label'=>__("教学计划二级管理")];
$Faculty_Filter_Field[] = ['value'=>"量化考核二级管理", 'label'=>__("量化考核二级管理")];
$Faculty_Filter_Field[] = ['value'=>"岗位实习二级管理", 'label'=>__("岗位实习二级管理")];
$Faculty_Filter_Field[] = ['value'=>"学生考勤二级管理", 'label'=>__("学生考勤二级管理")];
$Faculty_Filter_Field[] = ['value'=>"学生成绩二级管理", 'label'=>__("学生成绩二级管理")];
$Faculty_Filter_Field[] = ['value'=>"班级事务二级管理", 'label'=>__("班级事务二级管理")];
$Faculty_Filter_Field[] = ['value'=>"固定资产二级管理", 'label'=>__("固定资产二级管理")];

$Dormitory_Filter_Field = [];
$Dormitory_Filter_Field[] = ['value'=>"None", 'label'=>__("None")];
$Dormitory_Filter_Field[] = ['value'=>"宿舍楼", 'label'=>__("宿舍楼")];
$Dormitory_Filter_Field[] = ['value'=>"宿舍房间", 'label'=>__("宿舍房间")];

$EnableFields = [];
//$EnableFields['Faculty'] = ["Faculty_Filter_Field"];
$DisableFields = [];
$edit_default_2['Page_Role'][] = ['name' => "Page_Role_Name", 'show'=>true, 'type'=>'select', 'options'=>$Page_Role_Array, 'label' => __("Page_Role_Name"), 'value' => $Page_Role_Array[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>12], 'EnableFields'=>$EnableFields, 'DisableFields'=>$DisableFields];
$edit_default_2['Page_Role'][] = ['name' => "Faculty_Filter_Field", 'show'=>true, 'type'=>'select', 'options'=>$Faculty_Filter_Field, 'label' => __("Faculty_Filter_Field"), 'value' => $Faculty_Filter_Field[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_2['Page_Role'][] = ['name' => "Dormitory_Filter_Field", 'show'=>true, 'type'=>'select', 'options'=>$Dormitory_Filter_Field, 'label' => __("Dormitory_Filter_Field"), 'value' => $Dormitory_Filter_Field[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Field_One", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Extra_Priv_Filter_Field_One"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Method_One", 'show'=>true, 'type'=>'select', 'options'=>$Extra_Priv_Filter_Method, 'label' => __("Extra_Priv_Filter_Method_One"), 'value' => $Extra_Priv_Filter_Method[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Value_One", 'show'=>true, 'type'=>"input", 'label' => __("Extra_Priv_Filter_Value_One"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Field_Two", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Extra_Priv_Filter_Field_Two"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Method_Two", 'show'=>true, 'type'=>'select', 'options'=>$Extra_Priv_Filter_Method, 'label' => __("Extra_Priv_Filter_Method_Two"), 'value' => $Extra_Priv_Filter_Method[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Value_Two", 'show'=>true, 'type'=>"input", 'label' => __("Extra_Priv_Filter_Value_Two"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Field_Three", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Extra_Priv_Filter_Field_Three"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Method_Three", 'show'=>true, 'type'=>'select', 'options'=>$Extra_Priv_Filter_Method, 'label' => __("Extra_Priv_Filter_Method_Three"), 'value' => $Extra_Priv_Filter_Method[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Value_Three", 'show'=>true, 'type'=>"input", 'label' => __("Extra_Priv_Filter_Value_Three"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Field_Four", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Extra_Priv_Filter_Field_Four"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Method_Four", 'show'=>true, 'type'=>'select', 'options'=>$Extra_Priv_Filter_Method, 'label' => __("Extra_Priv_Filter_Method_Four"), 'value' => $Extra_Priv_Filter_Method[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Value_Four", 'show'=>true, 'type'=>"input", 'label' => __("Extra_Priv_Filter_Value_Four"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Field_Five", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Extra_Priv_Filter_Field_Five"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Method_Five", 'show'=>true, 'type'=>'select', 'options'=>$Extra_Priv_Filter_Method, 'label' => __("Extra_Priv_Filter_Method_Five"), 'value' => $Extra_Priv_Filter_Method[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Role'][] = ['name' => "Extra_Priv_Filter_Value_Five", 'show'=>true, 'type'=>"input", 'label' => __("Extra_Priv_Filter_Value_Five"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];


$edit_default_2['LimitEditAndDelete'][] = ['name' => "LimitEditAndDelete_Edit_Field_One", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("LimitEditAndDelete_Edit_Field_One"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];
$edit_default_2['LimitEditAndDelete'][] = ['name' => "LimitEditAndDelete_Edit_Value_One", 'show'=>true, 'type'=>'input', 'label' => __("LimitEditAndDelete_Edit_Value_One"), 'value' => '', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];
$edit_default_2['LimitEditAndDelete'][] = ['name' => "LimitEditAndDelete_Edit_Field_Two", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("LimitEditAndDelete_Edit_Field_Two"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];
$edit_default_2['LimitEditAndDelete'][] = ['name' => "LimitEditAndDelete_Edit_Value_Two", 'show'=>true, 'type'=>'input', 'label' => __("LimitEditAndDelete_Edit_Value_Two"), 'value' => '', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];
$edit_default_2['LimitEditAndDelete'][] = ['name' => "LimitEditAndDelete_Delete_Field_One", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("LimitEditAndDelete_Delete_Field_One"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];
$edit_default_2['LimitEditAndDelete'][] = ['name' => "LimitEditAndDelete_Delete_Value_One", 'show'=>true, 'type'=>'input', 'label' => __("LimitEditAndDelete_Delete_Value_One"), 'value' => '', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];
$edit_default_2['LimitEditAndDelete'][] = ['name' => "LimitEditAndDelete_Delete_Field_Two", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("LimitEditAndDelete_Delete_Field_Two"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];
$edit_default_2['LimitEditAndDelete'][] = ['name' => "LimitEditAndDelete_Delete_Value_Two", 'show'=>true, 'type'=>'input', 'label' => __("LimitEditAndDelete_Delete_Value_Two"), 'value' => '', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];

$edit_default_2['OperationAfterSubmit'][] = ['name' => "OperationAfterSubmit_Which_Field_Name", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("OperationAfterSubmit_Which_Field_Name"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_2['OperationAfterSubmit'][] = ['name' => "OperationAfterSubmit_Which_Field_Value", 'show'=>true, 'type'=>"input", 'label' => __("OperationAfterSubmit_Which_Field_Value"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>6, 'disabled' => false]];

$edit_default_2['OperationAfterSubmit'][] = ['name' => "OperationAfterSubmit_Need_Update_Table_Name", 'show'=>true, 'type'=>"input", 'label' => __("OperationAfterSubmit_Need_Update_Table_Name"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['OperationAfterSubmit'][] = ['name' => "OperationAfterSubmit_Need_Update_Table_Field_Name", 'show'=>true, 'type'=>"input", 'label' => __("OperationAfterSubmit_Need_Update_Table_Field_Name"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['OperationAfterSubmit'][] = ['name' => "OperationAfterSubmit_Need_Update_Table_Field_Value", 'show'=>true, 'type'=>"input", 'label' => __("OperationAfterSubmit_Need_Update_Table_Field_Value"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_2['OperationAfterSubmit'][] = ['name' => "OperationAfterSubmit_SameField_This_Table", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("OperationAfterSubmit_SameField_This_Table"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['OperationAfterSubmit'][] = ['name' => "OperationAfterSubmit_SameField_Other_Table", 'show'=>true, 'type'=>"input", 'label' => __("OperationAfterSubmit_SameField_Other_Table"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$OperationAfterSubmit_Update_Mode = [];
$OperationAfterSubmit_Update_Mode[] = ['value'=>"Update One Record", 'label'=>__("Update One Record")];
$OperationAfterSubmit_Update_Mode[] = ['value'=>"Update All Records", 'label'=>__("Update All Records")];
$edit_default_2['OperationAfterSubmit'][] = ['name' => "OperationAfterSubmit_Update_Mode", 'show'=>true, 'type'=>'select', 'options'=>$OperationAfterSubmit_Update_Mode, 'label' => __("OperationAfterSubmit_Update_Mode"), 'value' => $OperationAfterSubmit_Update_Mode[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

$EnablePluginsForIndividual = [];
$EnablePluginsForIndividual[] = ['value'=>"Disable", 'label'=>__("Disable")];
$EnablePluginsForIndividual[] = ['value'=>"Enable", 'label'=>__("Enable")];
$edit_default_2['OperationAfterSubmit'][] = ['name' => "EnablePluginsForIndividual", 'show'=>true, 'type'=>'select', 'options'=>$EnablePluginsForIndividual, 'label' => __("EnablePluginsForIndividual"), 'value' => $EnablePluginsForIndividual[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

$OperationLogGrade = [];
$OperationLogGrade[] = ['value'=>"None", 'label'=>__("None")];
$OperationLogGrade[] = ['value'=>"DeleteOperation", 'label'=>__("DeleteOperation")];
$OperationLogGrade[] = ['value'=>"EditAndDeleteOperation", 'label'=>__("EditAndDeleteOperation")];
$OperationLogGrade[] = ['value'=>"AddEditAndDeleteOperation", 'label'=>__("AddEditAndDeleteOperation")];
$OperationLogGrade[] = ['value'=>"AllOperation", 'label'=>__("AllOperation")];
$edit_default_2['OperationAfterSubmit'][] = ['name' => "OperationLogGrade", 'show'=>true, 'type'=>'select', 'options'=>$OperationLogGrade, 'label' => __("OperationLogGrade"), 'value' => $OperationLogGrade[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

$edit_default_2['OperationAfterSubmit'][] = ['name' => "AddPageSplitMultiRecords", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Add_Page_Split_Multi_Records"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];


$Default_Order_Method_By_Desc = [];
$Default_Order_Method_By_Desc[] = ['value'=>"Desc", 'label'=>__("Desc")];
$Default_Order_Method_By_Desc[] = ['value'=>"Asc", 'label'=>__("Asc")];

$MetaColumnNamesOptionsAllForPinned = $MetaColumnNamesOptions;
array_unshift($MetaColumnNamesOptionsAllForPinned,['value'=>"actions", 'label'=>__("actions")]);
array_unshift($MetaColumnNamesOptionsAllForPinned,['value'=>"Disabled", 'label'=>__("Disabled")]);

$edit_default_2['Columns_Pinned'][] = ['name' => "Columns_Pinned_Left_Field_One", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAllForPinned, 'label' => __("Columns_Pinned_Left_Field_One"), 'value' => $MetaColumnNamesOptionsAllForPinned[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_2['Columns_Pinned'][] = ['name' => "Columns_Pinned_Left_Field_Two", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAllForPinned, 'label' => __("Columns_Pinned_Left_Field_Two"), 'value' => $MetaColumnNamesOptionsAllForPinned[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_2['Columns_Pinned'][] = ['name' => "Columns_Pinned_Left_Field_Three", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAllForPinned, 'label' => __("Columns_Pinned_Left_Field_Three"), 'value' => $MetaColumnNamesOptionsAllForPinned[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_2['Columns_Pinned'][] = ['name' => "Columns_Pinned_Left_Field_Four", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAllForPinned, 'label' => __("Columns_Pinned_Left_Field_Four"), 'value' => $MetaColumnNamesOptionsAllForPinned[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_2['Columns_Pinned'][] = ['name' => "Columns_Pinned_Right_Field_One", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAllForPinned, 'label' => __("Columns_Pinned_Right_Field_One"), 'value' => $MetaColumnNamesOptionsAllForPinned[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_2['Columns_Pinned'][] = ['name' => "Columns_Pinned_Right_Field_Two", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAllForPinned, 'label' => __("Columns_Pinned_Right_Field_Two"), 'value' => $MetaColumnNamesOptionsAllForPinned[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_2['Columns_Pinned'][] = ['name' => "Columns_Pinned_Right_Field_Three", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAllForPinned, 'label' => __("Columns_Pinned_Right_Field_Three"), 'value' => $MetaColumnNamesOptionsAllForPinned[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_2['Columns_Pinned'][] = ['name' => "Columns_Pinned_Right_Field_Four", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAllForPinned, 'label' => __("Columns_Pinned_Right_Field_Four"), 'value' => $MetaColumnNamesOptionsAllForPinned[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

$edit_default_2['Page_Sort'][] = ['name' => "Default_Order_Method_By_Field_One", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Default_Order_Method_By_Field_One"), 'value' => $MetaColumnNamesOptionsAll[1]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Sort'][] = ['name' => "Default_Order_Method_By_Desc_One", 'show'=>true, 'type'=>'select', 'options'=>$Default_Order_Method_By_Desc, 'label' => __("Desc_Or_Asc_One"), 'value' => 'Desc', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>2]];

$edit_default_2['Page_Sort'][] = ['name' => "Default_Order_Method_By_Field_Two", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Default_Order_Method_By_Field_Two"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Sort'][] = ['name' => "Default_Order_Method_By_Desc_Two", 'show'=>true, 'type'=>'select', 'options'=>$Default_Order_Method_By_Desc, 'label' => __("Desc_Or_Asc_Two"), 'value' => 'Desc', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>2]];

$edit_default_2['Page_Sort'][] = ['name' => "Default_Order_Method_By_Field_Three", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Default_Order_Method_By_Field_Three"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Page_Sort'][] = ['name' => "Default_Order_Method_By_Desc_Three", 'show'=>true, 'type'=>'select', 'options'=>$Default_Order_Method_By_Desc, 'label' => __("Desc_Or_Asc_Three"), 'value' => 'Desc', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>2]];

$edit_default_2['Page_Sort'][] = ['name' => "Debug_Sql_Show_On_Api", 'show'=>true, 'type'=>'select', 'options'=>$YesOrNotOptions, 'label' => __("Debug_Sql_Show_On_Api"), 'value' => 'No', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>3]];

$Except_CSRF_Actions = [];
$Except_CSRF_Actions[] = ['value'=>"None", 'label'=>__("None")];
$Except_CSRF_Actions[] = ['value'=>"add_default", 'label'=>__("add_default")];
$Except_CSRF_Actions[] = ['value'=>"edit_default", 'label'=>__("edit_default")];
$Except_CSRF_Actions[] = ['value'=>"view_default", 'label'=>__("view_default")];
$Except_CSRF_Actions[] = ['value'=>"edit_view", 'label'=>__("edit_view")];
$Except_CSRF_Actions[] = ['value'=>"add_edit_view", 'label'=>__("add_edit_view")];
$Except_CSRF_Actions[] = ['value'=>"add_edit_view_delete", 'label'=>__("add_edit_view_delete")];
$Except_CSRF_Actions[] = ['value'=>"edit_default_configsetting", 'label'=>__("edit_default_configsetting")];
$edit_default_2['Page_Sort'][] = ['name' => "Except_CSRF_Actions", 'show'=>true, 'type'=>'select', 'options'=>$Except_CSRF_Actions, 'label' => __("Except_CSRF_Actions"), 'value' => 'None', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>3]];

$Init_Action_Value = [];
$Init_Action_Value[] = ['value'=>"init_default", 'label'=>__("init_default")];
$Init_Action_Value[] = ['value'=>"add_default", 'label'=>__("add_default")];
$Init_Action_Value[] = ['value'=>"edit_default", 'label'=>__("edit_default")];
$Init_Action_Value[] = ['value'=>"view_default", 'label'=>__("view_default")];
$Init_Action_Value[] = ['value'=>"edit_default_configsetting", 'label'=>__("edit_default_configsetting")];
$Init_Action_Value[] = ['value'=>"AiChatList", 'label'=>__("AiChatList")];
$Init_Action_Value[] = ['value'=>"SoulChatList", 'label'=>__("SoulChatList")];
$Init_Action_Value[] = ['value'=>"AiQuestionList", 'label'=>__("AiQuestionList")];

$edit_default_2['Init_Action'][] = ['name' => "Init_Action_Value", 'show'=>true, 'type'=>'select', 'options'=>$Init_Action_Value, 'label' => __("Init_Action_Value"), 'value' => 'init_default', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Init_Action'][] = ['name' => "Init_Action_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Init_Action_Field"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Init_Action'][] = ['name' => "Init_Action_FilterValue", 'show'=>true, 'type'=>"input", 'label' => __("Init_Action_FilterValue"), 'value' => __(""), 'placeholder' => "", 'helptext' => __("Advanced operation, please do not operate if you do not understand"), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_2['Init_Action'][] = ['name' => "Init_Action_Memo", 'show'=>true, 'type'=>"input", 'label' => __("Init_Action_Memo"), 'value' => __(""), 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$Init_Action_AddEditWidth = [];
$Init_Action_AddEditWidth[] = ['value'=>"xs", 'label'=>__("Extra Small")];
$Init_Action_AddEditWidth[] = ['value'=>"sm", 'label'=>__("Small")];
$Init_Action_AddEditWidth[] = ['value'=>"md", 'label'=>__("Medium")];
$Init_Action_AddEditWidth[] = ['value'=>"lg", 'label'=>__("Large")];
$Init_Action_AddEditWidth[] = ['value'=>"xl", 'label'=>__("Extra Large")];
$edit_default_2['Init_Action'][] = ['name' => "Init_Action_AddEditWidth", 'show'=>true, 'type'=>'select', 'options'=>$Init_Action_AddEditWidth, 'label' => __("Init_Action_AddEditWidth"), 'value' => 'md', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

$edit_default_2['Init_Action'][] = ['name' => "Init_Action_Page_ConfigSettingUrl", 'show'=>true, 'type'=>'buttonrouter', 'label' => __("ConfigSetting"), 'value' => '/form/configsetting/?FlowId='.$id, 'placeholder' => "", 'helptext' => "", 'target'=>'_blank', 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

//Unique_Fields
$edit_default_2['Unique_Fields'][] = ['name' => "Unique_Fields_1", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Unique_Fields_1"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Unique_Fields'][] = ['name' => "Unique_Fields_2", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Unique_Fields_2"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Unique_Fields'][] = ['name' => "Unique_Fields_3", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Unique_Fields_3"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Unique_Fields'][] = ['name' => "Unique_Fields_Repeat_Text", 'show'=>true, 'type'=>"input", 'label' => __("Unique_Fields_Repeat_Text"), 'value' => __(""), 'placeholder' => "", 'helptext' => __("If exist, show text in the user end"), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];

//Import_Fields_Unique
$edit_default_2['Import_Fields_Unique'][] = ['name' => "Import_Fields_Unique_1", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Import_Fields_Unique_1"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Import_Fields_Unique'][] = ['name' => "Import_Fields_Unique_2", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Import_Fields_Unique_2"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Import_Fields_Unique'][] = ['name' => "Import_Fields_Unique_3", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Import_Fields_Unique_3"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

//Relative_Child_Table
$sql    = "select id,FlowName from form_formflow where FlowName!='' and FaceTo='AuthUser'";
$rsT    = $db->Execute($sql);
$rsA    = $rsT->GetArray();
$Relative_Child_Table = [];
$Relative_Child_Table[] = ['value'=>0, 'label'=>__("None")];
foreach($rsA as $Line) {
    $Relative_Child_Table[] = ['value'=>$Line['id'], 'label'=>$Line['FlowName']];
}
$edit_default_2['Relative_Child_Table'][] = ['name' => "Relative_Child_Table", 'show'=>true, 'type'=>'select', 'options'=>$Relative_Child_Table, 'label' => __("Relative_Child_Table"), 'value' => $Relative_Child_Table[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Relative_Child_Table'][] = ['name' => "Relative_Child_Table_Field_Name", 'show'=>true, 'type'=>'input', 'label' => __("Relative_Child_Table_Field_Name"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_2['Relative_Child_Table'][] = ['name' => "Relative_Child_Table_Parent_Field_Name", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Relative_Child_Table_Parent_Field_Name"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];



$defaultValues_2 = [];
foreach($edit_default_2 as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValues_2[$ITEM['name']] = $ITEM['value'];
    }
}

$edit_default_2_mode[] = ['value'=>"Menu_Location", 'label'=>__("Menu_Location")];
$edit_default_2_mode[] = ['value'=>"Tip_In_Interface", 'label'=>__("Tip_In_Interface")];
$edit_default_2_mode[] = ['value'=>"Page_Role", 'label'=>__("Page_Role")];
$edit_default_2_mode[] = ['value'=>"LimitEditAndDelete", 'label'=>__("LimitEditAndDelete")];
$edit_default_2_mode[] = ['value'=>"OperationAfterSubmit", 'label'=>__("OperationAfterSubmit")];
$edit_default_2_mode[] = ['value'=>"Columns_Pinned", 'label'=>__("Columns_Pinned")];
$edit_default_2_mode[] = ['value'=>"Page_Sort", 'label'=>__("Page_Sort")];
$edit_default_2_mode[] = ['value'=>"Init_Action", 'label'=>__("Init_Action")];
$edit_default_2_mode[] = ['value'=>"Unique_Fields", 'label'=>__("Unique_Fields")];
$edit_default_2_mode[] = ['value'=>"Import_Fields_Unique", 'label'=>__("Import_Fields_Unique")];
$edit_default_2_mode[] = ['value'=>"Relative_Child_Table", 'label'=>__("Relative_Child_Table")];

if($_GET['action']=="edit_default_2"&&$id!='')         {
    $sql    = "select * from form_formflow where id = '$id'";
    $rs     = $db->Execute($sql);
    $FlowName   = $rs->fields['FlowName'];
    $Setting    = $rs->fields['Setting'];
    $SettingMap = unserialize(base64_decode($Setting));
    $FlowName   = $rs->fields['FlowName'];
    if(is_array($SettingMap))   {
        $defaultValues_2_keys = array_keys($defaultValues_2);
        foreach($SettingMap as $value => $label)  {
            if(in_array($value, $defaultValues_2_keys))  {
                if($value=="Menu_Three" && strpos($label,"班主任")!==false) {
                    $SettingMap['Menu_Three_Icon'] = "account-search";
                }
                else if($value=="Menu_Three" && strpos($label,"系部")!==false) {
                    $SettingMap['Menu_Three_Icon'] = "account-settings";
                }
                else if($value=="Menu_Three" && strpos($label,"教务")!==false) {
                    $SettingMap['Menu_Three_Icon'] = "account-multiple-plus";
                }
                else if($value=="Menu_Three" && strpos($label,"学工")!==false) {
                    $SettingMap['Menu_Three_Icon'] = "account-settings-variant";
                }
                else if($value=="Menu_Three" && strpos($label,"分管校长")!==false) {
                    $SettingMap['Menu_Three_Icon'] = "account-box-outline";
                }
                else if($value=="Menu_Three" && strpos($label,"校长")!==false) {
                    $SettingMap['Menu_Three_Icon'] = "account-box";
                }
                else if($value=="Menu_Three" && strpos($label,"所有")!==false) {
                    $SettingMap['Menu_Three_Icon'] = "table";
                }
            }
        }
        foreach($SettingMap as $value => $label)  {
            if(in_array($value, $defaultValues_2_keys) && $value!="Init_Action_Page_ConfigSettingUrl")  {
                $defaultValues_2[$value] = $label;
            }
        }
    }

    $EnableFields = [];
    switch($defaultValues_2['Page_Role_Name']) {
        case '院系':
            $EnableFields[] = "Faculty_Filter_Field";
            break;
    }

    //临时启用
    //$defaultValues_2['Menu_One'] = $SettingMap['Menu_One'];
    //$defaultValues_2['Menu_Two'] = $SettingMap['Menu_Two'];
    //$defaultValues_2['Menu_Three'] = $SettingMap['Menu_Three'];
    //$defaultValues_2['FaceTo'] = $SettingMap['FaceTo'];
    $edit_default['allFields']      = $edit_default_2;
    $edit_default['allFieldsMode']  = $edit_default_2_mode;
    $edit_default['defaultValues']  = $defaultValues_2;
    $edit_default['dialogContentHeight']  = "90%";
    $edit_default['componentsize']  = "small";
    $edit_default['submitaction']   = "edit_default_2_data";
    $edit_default['submittext']     = __("Submit");
    $edit_default['canceltext']     = __("Cancel");
    $edit_default['titletext']      = __("Design Form Field Type");
    $edit_default['titlememo']      = __("Manage All Form Fields in Table");
    $edit_default['tablewidth']     = 550;

    $RS['edit_default'] = $edit_default;
    $RS['EnableFields'] = $EnableFields;
    $RS['status'] = "OK";
    $RS['data'] = $defaultValues_2;
    $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    print_R(json_encode($RS));
    exit;
}

//#########################################################################################################################
//Bottom Button############################################################################################################
//#########################################################################################################################
$edit_default_3 = [];

$Bottom_Button_Actions = [];
//$Bottom_Button_Actions[] = ['value'=>"Edit", 'label'=>__("Edit")];
$Bottom_Button_Actions[] = ['value'=>"Delete", 'label'=>__("Delete")];
$Bottom_Button_Actions[] = ['value'=>"Batch_Approval", 'label'=>__("Batch_Approval")];
$Bottom_Button_Actions[] = ['value'=>"Batch_Cancel", 'label'=>__("Batch_Cancel")];
$Bottom_Button_Actions[] = ['value'=>"Batch_Reject", 'label'=>__("Batch_Reject")];
$Bottom_Button_Actions[] = ['value'=>"Reset_Password_123654", 'label'=>__("Reset_Password_123654")];
$Bottom_Button_Actions[] = ['value'=>"Reset_Password_ID_Last6", 'label'=>__("Reset_Password_ID_Last6")];
$Bottom_Button_Actions[] = ['value'=>"Batch_Setting_One", 'label'=>__("Batch_Setting_One")];
$Bottom_Button_Actions[] = ['value'=>"Batch_Setting_Two", 'label'=>__("Batch_Setting_Two")];
$edit_default_3['Setting_Buttons'][] = ['name' => "Bottom_Button_Actions", 'show'=>true, 'type'=>'checkbox', 'options'=>$Bottom_Button_Actions, 'label' => __("Bottom_Button_Actions"), 'value' => "Edit,Delete", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'row'=>true, 'xs'=>12, 'sm'=>12]];

$edit_default_3['Setting_Buttons'][] = ['name' => "ApprovalNodeFields", 'show'=>true, 'type'=>"input", 'label' => __("Approval Node Fields"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];
$edit_default_3['Setting_Buttons'][] = ['name' => "ApprovalNodeCurrentField", 'show'=>true, 'type'=>"input", 'label' => __("Approval Node Current Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_3['Setting_Buttons'][] = ['name' => "ApprovalNodeTitle", 'show'=>true, 'type'=>"input", 'label' => __("Approval Node Title"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>8, 'disabled' => false]];

$edit_default_3['Setting_Buttons'][] = ['name' => "Batch_Setting_One_Name", 'show'=>true, 'type'=>"input", 'label' => __("Batch_Setting_One_Name"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_3['Setting_Buttons'][] = ['name' => "Batch_Setting_One_Change_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Batch_Setting_One_Change_Field"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Setting_Buttons'][] = ['name' => "Batch_Setting_One_Additional_Display_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Batch_Setting_One_Additional_Display_Field"), 'value' => $MetaColumnNamesOptions[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

$edit_default_3['Setting_Buttons'][] = ['name' => "Batch_Setting_Two_Name", 'show'=>true, 'type'=>"input", 'label' => __("Batch_Setting_Two_Name"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_3['Setting_Buttons'][] = ['name' => "Batch_Setting_Two_Change_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Batch_Setting_Two_Change_Field"), 'value' => '', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Setting_Buttons'][] = ['name' => "Batch_Setting_Two_Additional_Display_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Batch_Setting_Two_Additional_Display_Field"), 'value' => $MetaColumnNamesOptions[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

$edit_default_3['Setting_Buttons'][] = ['name' => "Which_Field_Store_Password_When_Enable_Change_Password", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Which_Field_Store_Password_When_Enable_Change_Password"), 'value' => $MetaColumnNamesOptions[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>12]];


//$edit_default_3['Batch_Approval'][] = ['name' => "Divider1", 'show'=>true, 'type'=>"divider", 'label' => __("Divider"), 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>12]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Batch_Approval_Status_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Batch_Approval_Status_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Batch_Approval_Status_Value", 'show'=>true, 'type'=>"input", 'label' => __("Batch_Approval_Status_Value"), 'value' => __("Approval"), 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Batch_Approval_DateTime_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowDateTime, 'label' => __("Batch_Approval_DateTime_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$Batch_Approval_DateTime_Format = [];
$Batch_Approval_DateTime_Format[] = ['value'=>"DateTime", 'label'=>__("DateTime")];
$Batch_Approval_DateTime_Format[] = ['value'=>"Date", 'label'=>__("Date")];
$edit_default_3['Batch_Approval'][]  = ['name' => "Batch_Approval_DateTime_Format", 'show'=>true, 'type'=>'radiogroup', 'options'=>$Batch_Approval_DateTime_Format, 'label' => __("Batch_Approval_DateTime_Format"), 'value' => "DateTime", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Batch_Approval_User_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyshowPerson, 'label' => __("Batch_Approval_User_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$Batch_Approval_User_Format = [];
$Batch_Approval_User_Format[] = ['value'=>"UserID", 'label'=>__("UserID")];
$Batch_Approval_User_Format[] = ['value'=>"UserName", 'label'=>__("UserName")];
$edit_default_3['Batch_Approval'][]  = ['name' => "Batch_Approval_User_Format", 'show'=>true, 'type'=>'radiogroup', 'options'=>$Batch_Approval_User_Format, 'label' => __("Batch_Approval_User_Format"), 'value' => "DateTime", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4, 'row'=>true]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Batch_Approval_Review_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowOpinion, 'label' => __("Batch_Approval_Review_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Batch_Approval_Review_Opinion", 'show'=>true, 'type'=>"input", 'label' => __("Batch_Approval_Review_Opinion"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Field_When_Batch_Approval_1", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Change_Field_When_Batch_Approval_1"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Into_Value_When_Batch_Approval_1", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Field_When_Batch_Approval_2", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Change_Field_When_Batch_Approval_2"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Into_Value_When_Batch_Approval_2", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Field_When_Batch_Approval_3", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Change_Field_When_Batch_Approval_3"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Into_Value_When_Batch_Approval_3", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Field_When_Batch_Approval_4", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Approval_4"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Into_Value_When_Batch_Approval_4", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Field_When_Batch_Approval_5", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Approval_5"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Into_Value_When_Batch_Approval_5", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Field_When_Batch_Approval_6", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Approval_6"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Into_Value_When_Batch_Approval_6", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Field_When_Batch_Approval_7", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Approval_7"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Into_Value_When_Batch_Approval_7", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Field_When_Batch_Approval_8", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Approval_8"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Approval'][]  = ['name' => "Change_Into_Value_When_Batch_Approval_8", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

//$edit_default_3['Batch_Approval'][]  = ['name' => "Divider1", 'show'=>true, 'type'=>"divider", 'label' => __("Divider"), 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>12]];

##################################################################################################################################
$edit_default_3['Batch_Refuse'][]  = ['name' => "Batch_Refuse_Status_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Batch_Refuse_Status_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Batch_Refuse_Status_Value", 'show'=>true, 'type'=>"input", 'label' => __("Batch_Refuse_Status_Value"), 'value' => __("Refuse"), 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_3['Batch_Refuse'][]  = ['name' => "Batch_Refuse_DateTime_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowDateTime, 'label' => __("Batch_Refuse_DateTime_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$Batch_Refuse_DateTime_Format = [];
$Batch_Refuse_DateTime_Format[] = ['value'=>"DateTime", 'label'=>__("DateTime")];
$Batch_Refuse_DateTime_Format[] = ['value'=>"Date", 'label'=>__("Date")];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Batch_Refuse_DateTime_Format", 'show'=>true, 'type'=>'radiogroup', 'options'=>$Batch_Refuse_DateTime_Format, 'label' => __("Batch_Refuse_DateTime_Format"), 'value' => "DateTime", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4, 'row'=>true]];

$edit_default_3['Batch_Refuse'][]  = ['name' => "Batch_Refuse_User_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyshowPerson, 'label' => __("Batch_Refuse_User_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$Batch_Refuse_User_Format = [];
$Batch_Refuse_User_Format[] = ['value'=>"UserID", 'label'=>__("UserID")];
$Batch_Refuse_User_Format[] = ['value'=>"UserName", 'label'=>__("UserName")];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Batch_Refuse_User_Format", 'show'=>true, 'type'=>'radiogroup', 'options'=>$Batch_Refuse_User_Format, 'label' => __("Batch_Refuse_User_Format"), 'value' => "DateTime", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4, 'row'=>true]];

$edit_default_3['Batch_Refuse'][]  = ['name' => "Batch_Refuse_Review_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowOpinion, 'label' => __("Batch_Refuse_Review_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Batch_Refuse_Review_Opinion", 'show'=>true, 'type'=>"input", 'label' => __("Batch_Refuse_Review_Opinion"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Field_When_Batch_Refuse_1", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Change_Field_When_Batch_Refuse_1"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Into_Value_When_Batch_Refuse_1", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Field_When_Batch_Refuse_2", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Change_Field_When_Batch_Refuse_2"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Into_Value_When_Batch_Refuse_2", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Field_When_Batch_Refuse_3", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Change_Field_When_Batch_Refuse_3"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Into_Value_When_Batch_Refuse_3", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Field_When_Batch_Refuse_4", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Refuse_4"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Into_Value_When_Batch_Refuse_4", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Field_When_Batch_Refuse_5", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Refuse_5"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Into_Value_When_Batch_Refuse_5", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Field_When_Batch_Refuse_6", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Refuse_6"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Refuse'][]  = ['name' => "Change_Into_Value_When_Batch_Refuse_6", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

//$edit_default_3['Batch_Refuse'][]  = ['name' => "Divider2", 'show'=>true, 'type'=>"divider", 'label' => __("Divider"), 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>12]];

##################################################################################################################################
$edit_default_3['Batch_Cancel'][]  = ['name' => "Batch_Cancel_Status_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Batch_Cancel_Status_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Batch_Cancel_Status_Value", 'show'=>true, 'type'=>"input", 'label' => __("Batch_Cancel_Status_Value"), 'value' => __("Redo"), 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Batch_Cancel_DateTime_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowDateTime, 'label' => __("Batch_Cancel_DateTime_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$Batch_Cancel_DateTime_Format = [];
$Batch_Cancel_DateTime_Format[] = ['value'=>"DateTime", 'label'=>__("DateTime")];
$Batch_Cancel_DateTime_Format[] = ['value'=>"Date", 'label'=>__("Date")];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Batch_Cancel_DateTime_Format", 'show'=>true, 'type'=>'radiogroup', 'options'=>$Batch_Cancel_DateTime_Format, 'label' => __("Batch_Cancel_DateTime_Format"), 'value' => "DateTime", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4, 'row'=>true]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Batch_Cancel_User_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyshowPerson, 'label' => __("Batch_Cancel_User_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$Batch_Cancel_User_Format = [];
$Batch_Cancel_User_Format[] = ['value'=>"UserID", 'label'=>__("UserID")];
$Batch_Cancel_User_Format[] = ['value'=>"UserName", 'label'=>__("UserName")];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Batch_Cancel_User_Format", 'show'=>true, 'type'=>'radiogroup', 'options'=>$Batch_Cancel_User_Format, 'label' => __("Batch_Cancel_User_Format"), 'value' => "DateTime", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4, 'row'=>true]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Batch_Cancel_Review_Field", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowOpinion, 'label' => __("Batch_Cancel_Review_Field"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>8]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Batch_Cancel_Review_Opinion", 'show'=>true, 'type'=>"input", 'label' => __("Batch_Cancel_Review_Opinion"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Field_When_Batch_Cancel_1", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Change_Field_When_Batch_Cancel_1"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Into_Value_When_Batch_Cancel_1", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Field_When_Batch_Cancel_2", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Change_Field_When_Batch_Cancel_2"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Into_Value_When_Batch_Cancel_2", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Field_When_Batch_Cancel_3", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsOnlyShowStatus, 'label' => __("Change_Field_When_Batch_Cancel_3"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Into_Value_When_Batch_Cancel_3", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Field_When_Batch_Cancel_4", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Cancel_4"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Into_Value_When_Batch_Cancel_4", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Field_When_Batch_Cancel_5", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Cancel_5"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Into_Value_When_Batch_Cancel_5", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Field_When_Batch_Cancel_6", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Cancel_6"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Into_Value_When_Batch_Cancel_6", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Field_When_Batch_Cancel_7", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Cancel_7"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Into_Value_When_Batch_Cancel_7", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];


$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Field_When_Batch_Cancel_8", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Change_Field_When_Batch_Cancel_8"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_3['Batch_Cancel'][]  = ['name' => "Change_Into_Value_When_Batch_Cancel_8", 'show'=>true, 'type'=>"input", 'label' => __("Change_Into_Value"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['false' => true,'xs'=>12, 'sm'=>2, 'disabled' => false]];

$defaultValues_3 = [];
foreach($edit_default_3 as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValues_3[$ITEM['name']] = $ITEM['value'];
    }
}

$edit_default_3_mode[] = ['value'=>"Setting_Buttons", 'label'=>__("Setting_Buttons")];
$edit_default_3_mode[] = ['value'=>"Batch_Approval", 'label'=>__("Batch_Approval")];
$edit_default_3_mode[] = ['value'=>"Batch_Cancel", 'label'=>__("Batch_Cancel")];
$edit_default_3_mode[] = ['value'=>"Batch_Refuse", 'label'=>__("Batch_Refuse")];


//#########################################################################################################################
//Bottom Button############################################################################################################
//#########################################################################################################################
$edit_default_4 = [];

$Msg_Reminder_Rule_Method = [];
$Msg_Reminder_Rule_Method[] = ['value'=>"=", 'label'=>__("=")];
$Msg_Reminder_Rule_Method[] = ['value'=>"in", 'label'=>__("in")];
$Msg_Reminder_Rule_Method[] = ['value'=>"not in", 'label'=>__("not in")];

$MaxMsgSections = 3; // other setting in data_enginee_function.php
for($i=1;$i<=$MaxMsgSections;$i++)     {
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Field_Name_{$i}_1", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Msg_Reminder_Rule_Field_Name_1"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Field_Method_{$i}_1", 'show'=>true, 'type'=>'select', 'options'=>$Msg_Reminder_Rule_Method, 'label' => __("Msg_Reminder_Rule_Field_Method_1"), 'value' => $Msg_Reminder_Rule_Method[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Field_Value_{$i}_1", 'show'=>true, 'type'=>"input", 'label' => __("Msg_Reminder_Rule_Field_Value_1"), 'value' => "", 'placeholder' => "", 'helptext' => __("E.g.: *, NULL, or other value"), 'rules' => ['required' => false,'xs'=>12, 'sm'=>5, 'disabled' => false]];

    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Field_Name_{$i}_2", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Msg_Reminder_Rule_Field_Name_2"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Field_Method_{$i}_2", 'show'=>true, 'type'=>'select', 'options'=>$Msg_Reminder_Rule_Method, 'label' => __("Msg_Reminder_Rule_Field_Method_2"), 'value' => $Msg_Reminder_Rule_Method[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>3]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Field_Value_{$i}_2", 'show'=>true, 'type'=>"input", 'label' => __("Msg_Reminder_Rule_Field_Value_2"), 'value' => "", 'placeholder' => "", 'helptext' => __("E.g.: *, NULL, or other value"), 'rules' => ['required' => false,'xs'=>12, 'sm'=>5, 'disabled' => false]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Content_{$i}", 'show'=>true, 'type'=>"textarea", 'label' => __("Msg_Reminder_Rule_Content"), 'value' => "", 'placeholder' => "", 'helptext' => __("[FieldName] will replace the real value"), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];

    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "divider", 'show'=>true, 'type'=>"divider", 'label' => __("divider"), 'value' => "", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];
    $FieldName = "";
    $CurrentFieldTypeArray = explode(":","autocompletemulti:data_user:1:2:admin");
    $TableNameTemp      = $CurrentFieldTypeArray[1];
    $KeyField           = $CurrentFieldTypeArray[2];
    $ValueField         = $CurrentFieldTypeArray[3];
    $DefaultValue       = $CurrentFieldTypeArray[4];
    $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
    $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
    $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);
    if($TableNameTemp=="form_formdict" && sizeof($CurrentFieldTypeArray)==7)   {
        $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label,ExtraControl from $TableNameTemp where $AddSqlTemp $WhereField = '".$WhereValue."' order by SortNumber asc, `".$MetaColumnNamesTemp[$ValueField]."` asc";
    }
    elseif(sizeof($CurrentFieldTypeArray)==7)   {
        $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $AddSqlTemp $WhereField = '".$WhereValue."' order by SortNumber asc, `".$MetaColumnNamesTemp[$ValueField]."` asc";
    }
    elseif(sizeof($CurrentFieldTypeArray)==5||sizeof($CurrentFieldTypeArray)==4)   {
        $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where 1=1 $AddSqlTemp order by `".$MetaColumnNamesTemp[$ValueField]."` asc, id asc";
    }
    else {
        print "autocompletemulti para error!";exit;
    }
    $rs = $db->Execute($sql) or print($sql);
    $FieldType = $rs->GetArray();
    $DefaultValueTemp = $SettingMap["Msg_Reminder_Object_Select_Users_{$i}"];
    $FieldCodeName = $FieldName;
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Object_Select_Users_{$i}", 'code' => "Msg_Reminder_Object_Select_Users_{$i}", 'FieldTypeArray'=>$CurrentFieldTypeArray, 'show'=>true, 'type'=>"autocompletemulti", 'options'=>$FieldType, 'label' => __("Msg_Reminder_Object_Select_Users"), 'value' => $DefaultValueTemp, 'placeholder' => __(""), 'helptext' => __(""), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "divider", 'show'=>true, 'type'=>"divider", 'label' => __("divider"), 'value' => "", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];

    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Storage_StudentCode_{$i}", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Msg_Reminder_Rule_Storage_StudentCode"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Storage_StudentClass_{$i}", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Msg_Reminder_Rule_Storage_StudentClass"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object = [];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"学生", 'label'=>__("学生")];
    //$Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"家长", 'label'=>__("家长")];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"班主任", 'label'=>__("班主任")];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"年段长", 'label'=>__("年段长")];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"宿管员", 'label'=>__("宿管员")];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"系部", 'label'=>__("系部")];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"专业", 'label'=>__("专业")];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"本班所有学生", 'label'=>__("本班所有学生")];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"本专业所有学生", 'label'=>__("本专业所有学生")];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"本系所有学生", 'label'=>__("本系所有学生")];
    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object[] = ['value'=>"本校所有学生", 'label'=>__("本校所有学生")];

    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Storage_StudentCodeAndClass_Reminder_Object_{$i}", 'show'=>true, 'type'=>'checkbox', 'options'=>$Msg_Reminder_Rule_Storage_StudentCodeAndClass_Object, 'label' => __("Msg_Reminder_Rule_Storage_StudentCodeAndClass_Reminder_Object"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => true, 'disabled' => false, 'row'=>true, 'xs'=>12, 'sm'=>12]];


    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "divider", 'show'=>true, 'type'=>"divider", 'label' => __("divider"), 'value' => "", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Strorage_User_{$i}", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Msg_Reminder_Rule_Strorage_User"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Strorage_OtherStudentCode_{$i}", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Msg_Reminder_Rule_Strorage_OtherStudentCode"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "divider", 'show'=>true, 'type'=>"divider", 'label' => __("divider"), 'value' => "", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Strorage_DeptID_{$i}", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Msg_Reminder_Rule_Strorage_DeptID"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
    $Msg_Reminder_Rule_Strorage_Dept_Object = [];
    $Msg_Reminder_Rule_Strorage_Dept_Object[] = ['value'=>"MANAGER", 'label'=>__("MANAGER")];
    $Msg_Reminder_Rule_Strorage_Dept_Object[] = ['value'=>"LEADER1", 'label'=>__("LEADER1")];
    $Msg_Reminder_Rule_Strorage_Dept_Object[] = ['value'=>"LEADER2", 'label'=>__("LEADER2")];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Strorage_Dept_Object_{$i}", 'show'=>true, 'type'=>'select', 'options'=>$Msg_Reminder_Rule_Strorage_Dept_Object, 'label' => __("Msg_Reminder_Rule_Strorage_Dept_Object"), 'value' => $Msg_Reminder_Rule_Strorage_Dept_Object[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "divider", 'show'=>true, 'type'=>"divider", 'label' => __("divider"), 'value' => "", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Strorage_FacultyID_{$i}", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("Msg_Reminder_Rule_Strorage_FacultyID"), 'value' => $MetaColumnNamesOptionsAll[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Strorage_Faculty_Object_{$i}", 'show'=>true, 'type'=>'select', 'options'=>$Faculty_Filter_Field, 'label' => __("Msg_Reminder_Rule_Strorage_Faculty_Object"), 'value' => $Faculty_Filter_Field[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "divider", 'show'=>true, 'type'=>"divider", 'label' => __("divider"), 'value' => "", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];
    $Msg_Reminder_Rule_Reminder_Method = [];
    $Msg_Reminder_Rule_Reminder_Method[] = ['value'=>"PC", 'label'=>__("PC")];
    $edit_default_4['Msg_Reminder_Rule_'.$i][] = ['name' => "Msg_Reminder_Rule_Reminder_Method_{$i}", 'show'=>true, 'type'=>'select', 'options'=>$Msg_Reminder_Rule_Reminder_Method, 'label' => __("Msg_Reminder_Rule_Strorage_Faculty_Object"), 'value' => $Msg_Reminder_Rule_Reminder_Method[0]['value'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>12]];

    $edit_default_4_mode[] = ['value'=>"Msg_Reminder_Rule_{$i}", 'label'=>__("Msg_Reminder_Rule_{$i}")];
}

//#########################################################################################################################
//MobileEnd################################################################################################################
//#########################################################################################################################
$edit_default_5 = [];
$edit_default_5_mode[] = ['value'=>"MenuAndIcon", 'label'=>__("MenuAndIcon")];
$edit_default_5_mode[] = ['value'=>"ListTemplate1", 'label'=>__("ListTemplate1")];
$edit_default_5_mode[] = ['value'=>"ListTemplate2", 'label'=>__("ListTemplate2")];
//$edit_default_5_mode[] = ['value'=>"NewsTemplate1", 'label'=>__("NewsTemplate1")];
//$edit_default_5_mode[] = ['value'=>"ZiXun", 'label'=>__("ZiXun")];
//$edit_default_5_mode[] = ['value'=>"Activity", 'label'=>__("Activity")];
//$edit_default_5_mode[] = ['value'=>"Schoolmate", 'label'=>__("Schoolmate")];


$MobileEnd = [];
$MobileEnd[] = ['value'=>'Yes', 'label'=>__('Yes')];
$MobileEnd[] = ['value'=>'No', 'label'=>__('No')];
$edit_default_5['MenuAndIcon'][] = ['name' => "MobileEnd", 'show'=>true, 'type'=>'select', 'options'=>$MobileEnd, 'label' => __("MobileEnd"), 'value' => "No", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

$MobileEndShowType = [];
$MobileEndShowType[] = ['value'=>"ListTemplate1", 'label'=>__("ListTemplate1")];
$MobileEndShowType[] = ['value'=>"ListTemplate2", 'label'=>__("ListTemplate2")];
//$MobileEndShowType[] = ['value'=>"NewsTemplate1", 'label'=>__("NewsTemplate1")];
//$MobileEndShowType[] = ['value'=>"ZiXun", 'label'=>__("ZiXun")];
//$MobileEndShowType[] = ['value'=>"Activity", 'label'=>__("Activity")];
//$MobileEndShowType[] = ['value'=>"Schoolmate", 'label'=>__("Schoolmate")];
//$MobileEndShowType[] = ['value'=>"NotificationTemplate1", 'label'=>__("NotificationTemplate1")];
//$MobileEndShowType[] = ['value'=>"NotificationTemplate2", 'label'=>__("NotificationTemplate2")];
$edit_default_5['MenuAndIcon'][] = ['name' => "MobileEndShowType", 'show'=>true, 'type'=>'select', 'options'=>$MobileEndShowType, 'label' => __("MobileEndShowType"), 'value' => 'List', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

$edit_default_5['MenuAndIcon'][] = ['name' => "MobileEndShowSearch", 'show'=>true, 'type'=>'select', 'options'=>$YesOrNotOptions, 'label' => __("MobileEndShowSearch"), 'value' => 'Yes', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['MenuAndIcon'][] = ['name' => "MobileEndShowGroupFilter", 'show'=>true, 'type'=>'select', 'options'=>$YesOrNotOptions, 'label' => __("MobileEndShowGroupFilter"), 'value' => 'Yes', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
//$edit_default_5['MenuAndIcon'][] = ['name' => "MobileEndShowBanners", 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>'files', 'label' => __("MobileEndShowBanners"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false], 'RemoveAll'=>__('RemoveAll') ];

$edit_default_5['MenuAndIcon'][] = ['name' => "MobileEndIconName", 'show'=>true, 'type'=>"input", 'label' => __("IconName"), 'value' => $SettingMap['Menu_Three'], 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>6, 'disabled' => false]];
// Loading all images as the wechat app icons
$ImagesList = [];
$readdir = "./images/wechatIcon";
if (is_dir($readdir)) {
    if ($dh = opendir($readdir)) {
        while (($file = readdir($dh)) !== false) {
            if($file!='.' && $file!='..')  {
                $ImagesList[] = ['value'=>substr($file,0,-4), 'label'=>"/images/wechatIcon/".$file];
            }
        }
        closedir($dh);
    }
}
$edit_default_5['MenuAndIcon'][] = ['name' => "MobileEndIconImage", 'show'=>true, 'type'=>"autocompleteicons", 'options'=>$ImagesList, 'label' => __("Icon"), 'value' => '', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>6, 'disabled' => false]];

$edit_default_5['MenuAndIcon'][] = ['name' => "MobileEndTitleName", 'show'=>true, 'type'=>"input", 'label' => __("TitleName"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>6, 'disabled' => false]];

$MobileEndIconType = [];
$MobileEndIconType[] = ["value"=>"Disable", "label"=>"禁用"];
$MobileEndIconType[] = ["value"=>"UserAvator", "label"=>"用户头像"];
$MobileEndIconType[] = ["value"=>"ImageField", "label"=>"图片字段"];
$edit_default_5['ListTemplate1'][] = ['name' => "MobileEndIconType", 'show'=>true, 'type'=>'select', 'options'=>$MobileEndIconType, 'label' => __("MobileEndIconType"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>6, 'sm'=>4]];
$edit_default_5['ListTemplate1'][] = ['name' => "MobileEndIconField", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndIconField"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>6, 'sm'=>4]];
$edit_default_5['ListTemplate1'][] = ['name' => "MobileEndFirstLine", 'show'=>true, 'type'=>"input", 'label' => __("FirstLine"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];
$edit_default_5['ListTemplate1'][] = ['name' => "MobileEndSecondLineLeft", 'show'=>true, 'type'=>"input", 'label' => __("SecondLineLeft"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_5['ListTemplate1'][] = ['name' => "MobileEndSecondLineLeftColorField", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndSecondLineLeftColorField"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate1'][] = ['name' => "MobileEndSecondLineLeftColorRule", 'show'=>true, 'type'=>"input", 'label' => __("MobileEndSecondLineLeftColorRule"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_5['ListTemplate1'][] = ['name' => "MobileEndSecondLineRight", 'show'=>true, 'type'=>"input", 'label' => __("SecondLineRight"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$edit_default_5['ListTemplate1'][] = ['name' => "MobileEndSecondLineRightColorField", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndSecondLineRightColorField"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate1'][] = ['name' => "MobileEndSecondLineRightColorRule", 'show'=>true, 'type'=>"input", 'label' => __("MobileEndSecondLineRightColorRule"), 'value' => "", 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];


$edit_default_5['ListTemplate2'][] = ['name' => "MobileEndField1", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndField1"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate2'][] = ['name' => "MobileEndField2", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndField2"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate2'][] = ['name' => "MobileEndField3", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndField3"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate2'][] = ['name' => "MobileEndField4", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndField4"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate2'][] = ['name' => "MobileEndField5", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndField5"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate2'][] = ['name' => "MobileEndField6", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndField6"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate2'][] = ['name' => "MobileEndField7", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndField7"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate2'][] = ['name' => "MobileEndField8", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndField8"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];
$edit_default_5['ListTemplate2'][] = ['name' => "MobileEndField9", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndField9"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>4]];

/*
$edit_default_5['NewsTemplate1'][] = ['name' => "MobileEndNewsTitle", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsTitle"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['NewsTemplate1'][] = ['name' => "MobileEndNewsGroup", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsGroup"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['NewsTemplate1'][] = ['name' => "MobileEndNewsContent", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsContent"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['NewsTemplate1'][] = ['name' => "MobileEndNewsReadCounter", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsReadCounter"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['NewsTemplate1'][] = ['name' => "MobileEndNewsReadUsers", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsReadUsers"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['NewsTemplate1'][] = ['name' => "MobileEndNewsCreator", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsCreator"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['NewsTemplate1'][] = ['name' => "MobileEndNewsCreateTime", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsCreateTime"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['NewsTemplate1'][] = ['name' => "MobileEndNewsLeftImage", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsLeftImage"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];



$edit_default_5['ZiXun'][] = ['name' => "MobileEndNewsTitle", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsTitle"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['ZiXun'][] = ['name' => "MobileEndNewsGroup", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsGroup"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['ZiXun'][] = ['name' => "MobileEndNewsContent", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsContent"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['ZiXun'][] = ['name' => "MobileEndNewsReadCounter", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsReadCounter"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['ZiXun'][] = ['name' => "MobileEndNewsLikeCounter", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsLikeCounter"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['ZiXun'][] = ['name' => "MobileEndNewsFavoriteCounter", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsFavoriteCounter"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['ZiXun'][] = ['name' => "MobileEndNewsCreator", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsCreator"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['ZiXun'][] = ['name' => "MobileEndNewsCreateTime", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsCreateTime"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['ZiXun'][] = ['name' => "MobileEndNewsLeftImage", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsLeftImage"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];



$edit_default_5['Activity'][] = ['name' => "MobileEndNewsTitle", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsTitle"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsGroup", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsGroup"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsContent", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsContent"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsEnrollment", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsEnrollment"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsLocation", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsLocation"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsLocation2", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsLocation2"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsProcess", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsProcess"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsTopAvator", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsTopAvator"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

$edit_default_5['Activity'][] = ['name' => "MobileEndActivityEnrollEndDate", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndActivityEnrollEndDate"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndActivityDate", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndActivityDate"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndActivityFee", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndActivityFee"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndActivityContact", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndActivityContact"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];

$edit_default_5['Activity'][] = ['name' => "MobileEndNewsReadCounter", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsReadCounter"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsLikeCounter", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsLikeCounter"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsFavoriteCounter", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsFavoriteCounter"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsLeftImage", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsLeftImage"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsCreator", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsCreator"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsCreatorGroup", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsCreatorGroup"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsCreateTime", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsCreateTime"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Activity'][] = ['name' => "MobileEndNewsEnableEnroll", 'show'=>true, 'type'=>'select', 'options'=>$YesOrNotOptions, 'label' => __("MobileEndNewsEnableEnroll"), 'value' => 'Yes', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];


$edit_default_5['Schoolmate'][] = ['name' => "MobileEndNewsTopAvator", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsTopAvator"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Schoolmate'][] = ['name' => "MobileEndNewsCreator", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsCreator"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Schoolmate'][] = ['name' => "MobileEndNewsCreatorGroup", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndNewsCreatorGroup"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Schoolmate'][] = ['name' => "MobileEndSchoolmateCity", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndSchoolmateCity"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Schoolmate'][] = ['name' => "MobileEndSchoolmateCompany", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndSchoolmateCompany"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Schoolmate'][] = ['name' => "MobileEndSchoolmateIndustry", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndSchoolmateIndustry"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Schoolmate'][] = ['name' => "MobileEndSchoolmateFirstYear", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndSchoolmateFirstYear"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Schoolmate'][] = ['name' => "MobileEndSchoolmateLastYear", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndSchoolmateLastYear"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Schoolmate'][] = ['name' => "MobileEndSchoolmateAcademic", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndSchoolmateAcademic"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
$edit_default_5['Schoolmate'][] = ['name' => "MobileEndSchoolmateLastActivity", 'show'=>true, 'type'=>'select', 'options'=>$MetaColumnNamesOptionsAll, 'label' => __("MobileEndSchoolmateLastActivity"), 'value' => 'Disable', 'placeholder' => "", 'helptext' => "", 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>6]];
*/

$defaultValues_4 = [];
foreach($edit_default_4 as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValues_4[$ITEM['name']] = $ITEM['value'];
    }
}

$defaultValues_5 = [];
foreach($edit_default_5 as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValues_5[$ITEM['name']] = $ITEM['value'];
    }
}

if($_GET['action']=="edit_default_3"&&$id!='')         {
    $sql    = "select * from form_formflow where id = '$id'";
    $rs     = $db->Execute($sql);
    $FlowName   = $rs->fields['FlowName'];
    $Setting    = $rs->fields['Setting'];
    $SettingMap = unserialize(base64_decode($Setting));
    $FlowName   = $rs->fields['FlowName'];
    if(is_array($SettingMap))   {
        $defaultValues_3_keys = array_keys($defaultValues_3);
        foreach($SettingMap as $value => $label)  {
            if(in_array($value, $defaultValues_3_keys))  {
                $defaultValues_3[$value] = $label;
            }
        }
    }
    $edit_default['allFields']      = $edit_default_3;
    $edit_default['allFieldsMode']  = $edit_default_3_mode;
    $edit_default['defaultValues']  = $defaultValues_3;
    $edit_default['dialogContentHeight']  = "90%";
    $edit_default['componentsize']  = "small";
    $edit_default['submitaction']   = "edit_default_3_data";
    $edit_default['submittext']     = __("Submit");
    $edit_default['canceltext']     = __("Cancel");
    $edit_default['titletext']      = __("Design Form Field Type");
    $edit_default['titlememo']      = __("Manage All Form Fields in Table");
    $edit_default['tablewidth']     = 550;

    $RS['edit_default'] = $edit_default;
    $RS['status'] = "OK";
    $RS['data'] = $defaultValues_3;
    $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    print_R(json_encode($RS));
    exit;
}

if($_GET['action']=="edit_default_4"&&$id!='')         {
    $sql    = "select * from form_formflow where id = '$id'";
    $rs     = $db->Execute($sql);
    $FlowName   = $rs->fields['FlowName'];
    $Setting    = $rs->fields['Setting'];
    $SettingMap = unserialize(base64_decode($Setting));
    $FlowName   = $rs->fields['FlowName'];
    if(is_array($SettingMap))   {
        $defaultValues_4_keys = array_keys($defaultValues_4);
        foreach($SettingMap as $value => $label)  {
            if(in_array($value, $defaultValues_4_keys))  {
                $defaultValues_4[$value] = $label;
            }
        }
    }
    $edit_default['allFields']      = $edit_default_4;
    $edit_default['allFieldsMode']  = $edit_default_4_mode;
    $edit_default['defaultValues']  = $defaultValues_4;
    $edit_default['dialogContentHeight']  = "90%";
    $edit_default['componentsize']  = "small";
    $edit_default['submitaction']   = "edit_default_4_data";
    $edit_default['submittext']     = __("Submit");
    $edit_default['canceltext']     = __("Cancel");
    $edit_default['titletext']      = __("Design Form Field Type");
    $edit_default['titlememo']      = __("Manage All Form Fields in Table");
    $edit_default['tablewidth']     = 550;

    $RS['edit_default'] = $edit_default;
    $RS['status'] = "OK";
    $RS['data'] = $defaultValues_4;
    $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    print_R(json_encode($RS));
    exit;
}



if($_GET['action']=="edit_default_5"&&$id!='')         {
    $sql    = "select * from form_formflow where id = '$id'";
    $rs     = $db->Execute($sql);
    $FormId     = $rs->fields['FormId'];
    $Step       = $rs->fields['Step'];
    $FlowName   = $rs->fields['FlowName'];
    $Setting    = $rs->fields['Setting'];
    $SettingMap = unserialize(base64_decode($Setting));
    $FlowName   = $rs->fields['FlowName'];
    if(is_array($SettingMap))   {
        $defaultValues_5_keys = array_keys($defaultValues_5);
        foreach($SettingMap as $value => $label)  {
            if(in_array($value, $defaultValues_5_keys))  {
                $defaultValues_5[$value] = $label;
            }
        }
        if($defaultValues_5['MobileEndIconName']=="" || $defaultValues_5['MobileEndIconName']==null)     {
            $defaultValues_5['MobileEndIconName'] = $SettingMap['Menu_Three']!=""?$SettingMap['Menu_Three']:$SettingMap['Menu_Two'];
        }
        if($defaultValues_5['MobileEndTitleName']=="" || $defaultValues_5['MobileEndTitleName']==null)     {
            $defaultValues_5['MobileEndTitleName'] = $SettingMap['Menu_Three']!=""?$SettingMap['Menu_Three']:$SettingMap['Menu_Two'];
        }
    }
    if($FormId!="" && $Step>1)   {
        $sql        = "select * from form_formflow where FormId = '$FormId' and Step='".($Step-1)."'";
        $rs         = $db->Execute($sql);
        $SettingPrevious    = $rs->fields['Setting'];
        $SettingMapPrevious = unserialize(base64_decode($SettingPrevious));
        $SettingMapKeys     = array_keys($SettingMap);
        if($defaultValues_5['MobileEndIconName']==""&&isset($SettingMapPrevious['MobileEndIconName']))               $defaultValues_5['MobileEndIconName'] = $SettingMapPrevious['MobileEndIconName'];
        if($defaultValues_5['MobileEndTitleName']==""&&isset($SettingMapPrevious['MobileEndTitleName']))              $defaultValues_5['MobileEndTitleName'] = $SettingMapPrevious['MobileEndTitleName'];
        if($defaultValues_5['MobileEndFirstLine']==""&&isset($SettingMapPrevious['MobileEndFirstLine']))              $defaultValues_5['MobileEndFirstLine'] = $SettingMapPrevious['MobileEndFirstLine'];
        if($defaultValues_5['MobileEndSecondLineLeft']==""&&isset($SettingMapPrevious['MobileEndSecondLineLeft']))         $defaultValues_5['MobileEndSecondLineLeft'] = $SettingMapPrevious['MobileEndSecondLineLeft'];
        if($defaultValues_5['MobileEndSecondLineLeftColorField']==""&&isset($SettingMapPrevious['MobileEndSecondLineLeftColorField']))         $defaultValues_5['MobileEndSecondLineLeftColorField'] = $SettingMapPrevious['MobileEndSecondLineLeftColorField'];
        if($defaultValues_5['MobileEndSecondLineLeftColorRule']==""&&isset($SettingMapPrevious['MobileEndSecondLineLeftColorRule']))         $defaultValues_5['MobileEndSecondLineLeftColorRule'] = $SettingMapPrevious['MobileEndSecondLineLeftColorRule'];


        if($defaultValues_5['MobileEndSecondLineRight']==""&&isset($SettingMapPrevious['MobileEndSecondLineRight']))         $defaultValues_5['MobileEndSecondLineRight'] = $SettingMapPrevious['MobileEndSecondLineRight'];
        if($defaultValues_5['MobileEndSecondLineRightColorField']==""&&isset($SettingMapPrevious['MobileEndSecondLineRightColorField']))         $defaultValues_5['MobileEndSecondLineRightColorField'] = $SettingMapPrevious['MobileEndSecondLineRightColorField'];
        if($defaultValues_5['MobileEndSecondLineRightColorRule']==""&&isset($SettingMapPrevious['MobileEndSecondLineRightColorRule']))         $defaultValues_5['MobileEndSecondLineRightColorRule'] = $SettingMapPrevious['MobileEndSecondLineRightColorRule'];

    }
    if($defaultValues_5['MobileEndFirstLine']=="undefined")                           $defaultValues_5['MobileEndFirstLine'] = "";
    if($defaultValues_5['MobileEndSecondLineLeft']=="undefined")                      $defaultValues_5['MobileEndSecondLineLeft'] = "";
    if($defaultValues_5['MobileEndSecondLineLeftColorField']=="undefined")            $defaultValues_5['MobileEndSecondLineLeftColorField'] = "";
    if($defaultValues_5['MobileEndSecondLineLeftColorRule']=="undefined")             $defaultValues_5['MobileEndSecondLineLeftColorRule'] = "";
    if($defaultValues_5['MobileEndSecondLineRight']=="undefined")                     $defaultValues_5['MobileEndSecondLineRight'] = "";
    if($defaultValues_5['MobileEndSecondLineRightColorField']=="undefined")           $defaultValues_5['MobileEndSecondLineRightColorField'] = "";
    if($defaultValues_5['MobileEndSecondLineRightColorRule']=="undefined")            $defaultValues_5['MobileEndSecondLineRightColorRule'] = "";
    if($defaultValues_5['MobileEndSecondLineRightColorRule']=="undefined")            $defaultValues_5['MobileEndSecondLineRightColorRule'] = "";

    $edit_default['allFields']      = $edit_default_5;
    $edit_default['allFieldsMode']  = $edit_default_5_mode;
    $edit_default['defaultValues']  = $defaultValues_5;
    $edit_default['dialogContentHeight']  = "90%";
    $edit_default['componentsize']  = "small";
    $edit_default['submitaction']   = "edit_default_5_data";
    $edit_default['submittext']     = __("Submit");
    $edit_default['canceltext']     = __("Cancel");
    $edit_default['titletext']      = __("Mobile End Setting");
    $edit_default['titlememo']      = __("");
    $edit_default['tablewidth']     = 550;

    $RS['edit_default'] = $edit_default;
    $RS['status'] = "OK";
    $RS['data'] = $defaultValues_5;
    $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    print_R(json_encode($RS));
    exit;
}

if(($_GET['action']=="edit_default_6_data") && $id!="")     {
    $sql        = "select * from form_formflow where id = '$id'";
    $rs         = $db->Execute($sql);
    $FieldsArray                = $rs->fields;
    $FieldsArray['id']          = NULL;
    $FieldsArray['FlowName']    = ForSqlInjection($_POST['FlowName']);
    $FieldsArray['FaceTo']      = ForSqlInjection($_POST['FaceTo']);

    $sql        = "select Max(Step) AS Step from form_formflow where FormId = '".$FieldsArray['FormId']."'";
    $rs         = $db->Execute($sql);
    $FieldsArray['Step']        = $rs->fields['Step']+1;
    [$rs,$sql] = InsertOrUpdateTableByArray("form_formflow",$FieldsArray,'FormId,Step',0);
    if($rs->EOF) {
        $RS['status'] = "OK";
        $RS['_POST'] = $_POST;
        $RS['msg'] = __("Submit Success");
        print json_encode($RS);
        exit;
    }
    else {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("sql execution failed");
        $RS['sql'] = $sql;
        $RS['_GET'] = $_GET;
        $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }
}

if(($_GET['action']=="edit_default_1_data" || $_GET['action']=="edit_default_2_data" || $_GET['action']=="edit_default_3_data" || $_GET['action']=="edit_default_4_data" || $_GET['action']=="edit_default_5_data") && $id!="")     {

    if($_POST['Menu_One']!=""&&$_POST['Menu_Two']!=""&&$id!="")   {
        $FieldsArray = [];
        $FieldsArray['MenuOneName']    = $_POST['Menu_One'];
        $FieldsArray['MenuTwoName']    = $_POST['Menu_Two'];
        $FieldsArray['MenuThreeName']  = $_POST['Menu_Three'];
        $FieldsArray['FaceTo']         = $_POST['FaceTo'];
        $FieldsArray['MenuTab']        = $_POST['MenuTab'];
        $FieldsArray['Menu_Three_Icon']= $_POST['Menu_Three_Icon'];
        $FieldsArray['FlowId']         = $id;
        $FieldsArray['MenuType']       = "Flow";
        $FieldsArray['SortNumber']     = $id;
        $FieldsArray['Creator']        = "admin";
        $FieldsArray['CreateTime']     = date("Y-m-d H:i:s");
        [$rs,$sql] = InsertOrUpdateTableByArray("data_menutwo",$FieldsArray,'FlowId',0);
        //Write Interface File In Apps Dir
        $sql        = "select id from data_menutwo where FlowId = '$id'";
        $rs         = $db->Execute($sql);
        $MenuTwoId  = $rs->fields['id'];
        $MenuTwoInterfaceFilePath = "apps/apps_".$MenuTwoId.".php";
        if($MenuTwoId>0&&!is_file($MenuTwoInterfaceFilePath)||1)   {
            $Content = '<?php
/*
* 基础架构: 单点低代码开发平台
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2023
* License: GPL V3 or Commercial license
*/
header("Content-Type: application/json");
require_once("../cors.php");
require_once("../include.inc.php");
$FlowId   = '.$id.';
require_once("../data_enginee_flow.php");
?>';
            $rs = file_put_contents($MenuTwoInterfaceFilePath,$Content);
            if($rs==false) {
                $RS = [];
                $RS['status'] = "ERROR";
                $RS['msg'] = "Failed to create PHP interface file. Please check whether the 'apps' directory has corresponding write permissions.";
                print json_encode($RS);
                exit;
            }
        }
    }

    if($_POST['MobileEnd']!=""&&$id!="")   {
      $FieldsArray                        = [];
      $FieldsArray['FlowId']              = $id;
      $FieldsArray['IsMobile']            = $_POST['MobileEnd'] == "No" ? "否" : "是";
      $FieldsArray['MobileEndIconImage']  = $_POST['MobileEndIconImage'];
      $FieldsArray['Creator']             = "admin";
      $FieldsArray['CreateTime']          = date("Y-m-d H:i:s");
      [$rs,$sql] = InsertOrUpdateTableByArray("data_menutwo",$FieldsArray,'FlowId',0);
    }

    //Make Plugin File
    if($_POST['EnablePluginsForIndividual']=='Enable')   {
        $FormId     = returntablefield("form_formflow","id",$id,"FormId")['FormId'];;
        $Step       = returntablefield("form_formflow","id",$id,"Step")['Step'];
        $FlowName   = returntablefield("form_formflow","id",$id,"FlowName")['FlowName'];
        $TableName  = returntablefield("form_formname","id",$FormId,"TableName")['TableName'];
        $EnablePluginsForIndividual = "plugins/plugin_".$TableName."_".$Step.".php";
        if($Step>0 && !is_file($EnablePluginsForIndividual) && $TableName!="")   {
            $Content    = file_get_contents("plugins/plugin_tablename_step.php");
            $Content    = str_replace("tablename",$TableName,$Content);
            $Content    = str_replace("step",$Step,$Content);
            $Content    = str_replace("[FlowName]",$FlowName,$Content);
            $rs         = file_put_contents($EnablePluginsForIndividual,$Content);
            if($rs==false) {
                $RS = [];
                $RS['status'] = "ERROR";
                $RS['msg'] = "Failed to Plugin file. Please check whether the 'plugins' directory has corresponding write permissions.";
                print json_encode($RS);
                exit;
            }
        }
    }

    $sql        = "select * from form_formflow where id = '$id'";
    $rs         = $db->Execute($sql);
    $FormId     = intval($rs->fields['FormId']);
    $Step       = intval($rs->fields['Step']);
    $FlowName   = $rs->fields['FlowName'];
    $Setting    = $rs->fields['Setting'];
    $SettingMap = unserialize(base64_decode($Setting));
    $FlowName   = $rs->fields['FlowName'];
    foreach($_POST as $value => $label)  {
        $SettingMap[$value] = $label;
    }
    $FieldsArray = [];
    //$FieldsArray['FlowName']  = $SettingMap['FlowName'];
    $FieldsArray['FormId']      = $FormId;
    $FieldsArray['Step']        = $Step;
    if($_POST['FaceTo']!="")   {
        $FieldsArray['FaceTo']  = $_POST['FaceTo'];
    }
    if($_POST['Init_Action_Value']=="edit_default_configsetting")   {
        $FieldsArray['PageType']  = "ConfigSetting";
    }
    else {
        $FieldsArray['PageType']  = "FunctionPage";
    }
    if($_POST['MobileEnd']!="")  {
        $FieldsArray['MobileEnd']   = $_POST['MobileEnd'];
    }
    $FieldsArray['Setting']     = base64_encode(serialize($SettingMap));
    $FieldsArray['Creator']     = "admin";
    $FieldsArray['CreateTime'] = date("Y-m-d H:i:s");
    [$rs,$sql] = InsertOrUpdateTableByArray("form_formflow",$FieldsArray,'FormId,Step',0);
    if($rs->EOF) {
        $RS['status'] = "OK";
        $RS['_POST'] = $_POST;
        $RS['msg'] = __("Submit Success");
        print json_encode($RS);
        exit;
    }
    else {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("sql execution failed");
        $RS['sql'] = $sql;
        $RS['_GET'] = $_GET;
        $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }
}

if($_GET['action']=="updateone")  {
    $id     = ForSqlInjection($_POST['id']);
    $field  = ParamsFilter($_POST['field']);
    $value  = ParamsFilter($_POST['value']);
    $primary_key = $columnNames[0];
    if($id!=""&&$field!=""&&in_array($field,$columnNames)&&$primary_key!=$field) {
        $sql    = "update form_formflow set $field = '$value' where $primary_key = '$id'";
        $db->Execute($sql);
        $RS = [];
        $RS['status'] = "OK";
        $RS['msg'] = __("Update Success");
        print json_encode($RS);
        exit;
    }
    else {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("Params Error!");
        $RS['_GET'] = $_GET;
        $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }
}

if($_GET['action']=="delete_array")  {
    $MetaColumnNames    = $db->MetaColumnNames($TableName);
    $MetaColumnNames    = array_values($MetaColumnNames);
    $selectedRows  = ForSqlInjection($_POST['selectedRows']);
    $selectedRows = explode(',',$selectedRows);
    $primary_key = $MetaColumnNames[0];
    foreach($selectedRows as $id) {
        $sql    = "delete from $TableName where $primary_key = '$id'";
        $db->Execute($sql);
    }
    $RS = [];
    $RS['sql'] = $sql;
    $RS['status'] = "OK";
    $RS['msg'] = "Drop Item Success!";
    print json_encode($RS);
    exit;
}

$AddSql = " where 1=1 and FormId='$externalId'";

$columnsactions     = [];
$columnsactions[]   = ['action'=>'delete_array','text'=>__('Delete'),'mdi'=>'mdi:delete-outline','double_check'=>__('Do you want to delete this item?')];
$columnsactions[]   = ['action'=>'edit_default_6','text'=>__('Copy'),'mdi'=>'mdi:content-copy','double_check'=>'Do you want to copy this item?'];
$init_default_columns[] = ['flex' => 0.1, 'minWidth' => 120, 'sortable' => false, 'field' => "actions", 'headerName' => __("Actions"), 'show'=>true, 'type'=>'actions', 'actions' => $columnsactions];
$columnName = "TableName";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>false, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
//$columnName = "FullName";    $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>false, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "Step";           $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 50, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>false, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "FlowName";       $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "Field Type";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 130, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>false, 'show'=>true, 'type'=>'api','apimdi'=>'mdi:chart-donut','apicolor'=>'success.main', 'apiaction' => "edit_default_1", 'renderCell' => NULL ];
$columnName = "Interface";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 130, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>false, 'show'=>true, 'type'=>'api','apimdi'=>'mdi:cog-outline','apicolor'=>'warning.main', 'apiaction' => "edit_default_2", 'renderCell' => NULL ];
$columnName = "Batch Approval";  $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 130, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>false, 'show'=>true, 'type'=>'api','apimdi'=>'mdi:border-bottom','apicolor'=>'info.main', 'apiaction' => "edit_default_3", 'renderCell' => NULL ];
$columnName = "MobileEnd";  $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 100, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>false, 'show'=>true, 'type'=>'api','apimdi'=>'mdi:cellphone','apicolor'=>'info.main', 'apiaction' => "edit_default_5", 'renderCell' => NULL ];
$columnName = "Msg Reminder";  $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 130, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>false, 'show'=>true, 'type'=>'api','apimdi'=>'mdi:message-bulleted','apicolor'=>'info.main', 'apiaction' => "edit_default_4", 'renderCell' => NULL ];
$columnName = "FaceTo";         $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 100, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];


$RS['init_default']['button_search']    = __("Search");
$RS['init_default']['button_add']       = __("Add");
$RS['init_default']['columns']          = $init_default_columns;
$RS['init_default']['columnsactions']   = $columnsactions;

$columnName = "FlowName";        $searchField[] = ['label' => __($columnName), 'value' => $columnName];

$RS['init_action']['action']            = "init_default";
$RS['init_action']['actionValue']       = "";
$RS['init_action']['id']                = 999; //NOT USE THIS VALUE IN FRONT END

$RS['init_default']['searchFieldArray'] = $searchField;
$RS['init_default']['searchFieldText']  = __("Search Item");

$searchFieldName     = ForSqlInjection($_REQUEST['searchFieldName']);
$searchFieldValue    = ForSqlInjection($_REQUEST['searchFieldValue']);
if ($searchFieldName != "" && $searchFieldValue != "" && in_array($searchFieldName, $columnNames) ) {
    $AddSql .= " and ($searchFieldName like '%" . $searchFieldValue . "%')";
}

$RS['init_default']['filter'] = [];

$page       = intval($_REQUEST['page']);
$pageSize   = intval($_REQUEST['pageSize']);
if(!in_array($pageSize,[10,20,30,40,50,100,200,500]))  {
	$pageSize = 30;
}
$fromRecord = $page * $pageSize;

$sql    = "select count(*) AS NUM from form_formflow " . $AddSql . "";
$rs     = $db->Execute($sql);
$RS['init_default']['total']        = intval($rs->fields['NUM']);
$RS['init_default']['searchtitle']  = __("Design Form Flow");
$RS['init_default']['primarykey']   = $columnNames[0];
if(!in_array($_REQUEST['sortColumn'], $columnNames)) {
    $_REQUEST['sortColumn']         = $columnNames[0];
}
if($_REQUEST['sortColumn']=="")   {
    $_REQUEST['sortColumn'] = "id";
}
if($_REQUEST['sortMethod']=="desc") {
    $orderby = "order by `".$_REQUEST['sortColumn']."` desc";
}
else {
    $orderby = "order by `".$_REQUEST['sortColumn']."` asc";
}

$ForbiddenSelectRow = [];
$ForbiddenViewRow   = [];
$ForbiddenEditRow   = [];
$ForbiddenDeleteRow = [];
$sql    = "select * from form_formflow " . $AddSql . " $orderby limit $fromRecord,$pageSize";
//print $sql;
$NewRSA = [];
$rs     = $db->Execute($sql) or print $sql;
$rs_a   = $rs->GetArray();
foreach ($rs_a as $Line)            {
    $Line['id']             = intval($Line['id']);
    $Line['TableName']      = returntablefield("form_formname","id",$Line['FormId'],"TableName")['TableName'];
    $Line['FullName']      = returntablefield("form_formname","id",$Line['FormId'],"FullName")['FullName'];
    $NewRSA[]               = $Line;
    if(in_array($Line['TableName'],['data_user','data_department','data_role','data_unit','data_interface','data_menuone','data_menutwo','form_formflow'])) {
        $ForbiddenSelectRow[] = $Line['id'];
        //$ForbiddenViewRow[] = $Line['id'];
        //$ForbiddenEditRow[] = $Line['id'];
        //$ForbiddenDeleteRow[] = $Line['id'];
    }
}
$RS['init_default']['data'] = $NewRSA;
$RS['init_default']['ForbiddenSelectRow'] = $ForbiddenSelectRow;
$RS['init_default']['ForbiddenViewRow'] = $ForbiddenViewRow;
$RS['init_default']['ForbiddenEditRow'] = $ForbiddenEditRow;
$RS['init_default']['ForbiddenDeleteRow'] = $ForbiddenDeleteRow;

$RS['init_default']['params'] = ['FormGroup' => '', 'role' => '', 'status' => '', 'q' => ''];

$RS['init_default']['sql'] = $sql;
$RS['init_default']['ApprovalNodeFields']['DebugSql']   = "";
$RS['init_default']['ApprovalNodeFields']['Memo']       = "";


$RS['init_default']['rowdelete'] = [];
$RS['init_default']['rowdelete'][] = ["text"=>__("Delete Item"),"action"=>"delete_array","title"=>__("Delete Item"),"content"=>__("Do you really want to delete this item? This operation will delete table and data in Database."),"memoname"=>"","inputmust"=>false,"inputmusttip"=>"","submit"=>__("Confirm Delete"),"cancel"=>__("Cancel")];


$RS['add_default']['allFields']     = $allFieldsAdd;
$RS['add_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['add_default']['defaultValues'] = $defaultValues;
$RS['add_default']['dialogContentHeight']  = "850px";
$RS['add_default']['submitaction']  = "add_default_data";
$RS['add_default']['componentsize'] = "small";
$RS['add_default']['componentsize'] = "small";
$RS['add_default']['submittext']    = __("Submit");
$RS['add_default']['canceltext']    = __("Cancel");
$RS['add_default']['titletext']     = __("Create Form");
$RS['add_default']['titlememo']     = __("Manage All Form Fields in Table");
$RS['add_default']['tablewidth']    = 650;

$RS['edit_default_1']['allFields']      = $edit_default_1;
$RS['edit_default_1']['allFieldsMode']  = $edit_default_1_mode;
$RS['edit_default_1']['defaultValues']  = $defaultValues_1;
$RS['edit_default_1']['dialogContentHeight']  = "850px";
$RS['edit_default_1']['submitaction']  = "edit_default_1_data";
$RS['edit_default_1']['componentsize'] = "small";
$RS['edit_default_1']['submittext']    = __("Submit");
$RS['edit_default_1']['canceltext']    = __("Cancel");
$RS['edit_default_1']['titletext']  = __("Design Flow Field Type");
$RS['edit_default_1']['titlememo']  = __("Manage All Field Show Types in Flow");
$RS['edit_default_1']['tablewidth']  = 650;

$RS['edit_default_2']['allFields']      = $edit_default_2;
$RS['edit_default_2']['allFieldsMode']  = $edit_default_2_mode;
$RS['edit_default_2']['defaultValues']  = $defaultValues_2;
$RS['edit_default_2']['dialogContentHeight']  = "850px";
$RS['edit_default_2']['submitaction']  = "edit_default_2_data";
$RS['edit_default_2']['componentsize'] = "small";
$RS['edit_default_2']['submittext']    = __("Submit");
$RS['edit_default_2']['canceltext']    = __("Cancel");
$RS['edit_default_2']['titletext']  = __("Design Flow Interface");
$RS['edit_default_2']['titlememo']  = __("Manage All Interface Attributes in Flow");
$RS['edit_default_2']['tablewidth']  = 650;

$RS['edit_default_3']['allFields']      = $edit_default_3;
$RS['edit_default_3']['allFieldsMode']  = $edit_default_3_mode;
$RS['edit_default_3']['defaultValues']  = $defaultValues_3;
$RS['edit_default_3']['dialogContentHeight']  = "850px";
$RS['edit_default_3']['submitaction']  = "edit_default_3_data";
$RS['edit_default_3']['componentsize'] = "small";
$RS['edit_default_3']['submittext']    = __("Submit");
$RS['edit_default_3']['canceltext']    = __("Cancel");
$RS['edit_default_3']['titletext']  = __("Design Form Bottom Button");
$RS['edit_default_3']['titlememo']  = __("Manage All Bottom Button Related Attributes in Flow");
$RS['edit_default_3']['tablewidth']  = 650;

$RS['edit_default_4']['allFields']      = $edit_default_4;
$RS['edit_default_4']['allFieldsMode']  = $edit_default_4_mode;
$RS['edit_default_4']['defaultValues']  = $defaultValues_4;
$RS['edit_default_4']['dialogContentHeight']  = "850px";
$RS['edit_default_4']['submitaction']  = "edit_default_4_data";
$RS['edit_default_4']['componentsize'] = "small";
$RS['edit_default_4']['submittext']    = __("Submit");
$RS['edit_default_4']['canceltext']    = __("Cancel");
$RS['edit_default_4']['titletext']  = __("Design Form Bottom Button");
$RS['edit_default_4']['titlememo']  = __("Manage All Bottom Button Related Attributes in Flow");
$RS['edit_default_4']['tablewidth']  = 650;
$RS['edit_default_4']['submitloading'] = __("SubmitLoading");
$RS['edit_default_4']['loading']       = __("Loading");

$RS['edit_default']                 = $RS['add_default'];
$RS['edit_default']['allFields']    = $allFieldsEdit;
$RS['edit_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default']['defaultValues']  = $defaultValues;
$RS['edit_default']['dialogContentHeight']  = "850px";
$RS['edit_default']['submitaction']  = "edit_default_data";
$RS['edit_default']['componentsize'] = "small";
$RS['edit_default']['componentsize'] = "small";
$RS['edit_default']['submittext']    = __("Submit");
$RS['edit_default']['canceltext']    = __("Cancel");
$RS['edit_default']['titletext']  = __("Edit Form");
$RS['edit_default']['titlememo']  = __("Manage All Form Fields in Table");
$RS['edit_default']['tablewidth']  = 650;
$RS['edit_default']['submitloading']    = __("SubmitLoading");
$RS['edit_default']['loading']          = __("Loading");

$RS['edit_default_5']['allFields']      = $edit_default_5;
$RS['edit_default_5']['allFieldsMode']  = $edit_default_5_mode;
$RS['edit_default_5']['defaultValues']  = $defaultValues_4;
$RS['edit_default_5']['dialogContentHeight']  = "850px";
$RS['edit_default_5']['submitaction']  = "edit_default_5_data";
$RS['edit_default_5']['componentsize'] = "small";
$RS['edit_default_5']['submittext']    = __("Submit");
$RS['edit_default_5']['canceltext']    = __("Cancel");
$RS['edit_default_5']['titletext']  = __("Design Form Bottom Button");
$RS['edit_default_5']['titlememo']  = __("Manage All Bottom Button Related Attributes in Flow");
$RS['edit_default_5']['tablewidth']  = 650;
$RS['edit_default_5']['submitloading'] = __("SubmitLoading");
$RS['edit_default_5']['loading']       = __("Loading");

$RS['edit_default_6']['allFields']      = $allFieldsAdd;
$RS['edit_default_6']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default_6']['defaultValues']  = $defaultValues;
$RS['edit_default_6']['dialogContentHeight']  = "90%";
$RS['edit_default_6']['submitaction']   = "edit_default_6_data";
$RS['edit_default_6']['submittext']     = __("Submit");
$RS['edit_default_6']['canceltext']     = __("Cancel");
$RS['edit_default_6']['titletext']      = __("Copy Item");
$RS['edit_default_6']['tablewidth']     = 550;
$RS['edit_default_6']['submitloading']  = __("SubmitLoading");
$RS['edit_default_6']['loading']        = __("Loading");

$RS['view_default'] = $RS['add_default'];
$RS['view_default']['titletext']  = __("View Form");
$RS['view_default']['titlememo']  = __("View All Form Fields in Table");

$RS['export_default'] = [];
$RS['import_default'] = [];

$RS['init_default']['returnButton']['status']  = true;
$RS['init_default']['returnButton']['text']  = __("return");
$RS['init_default']['rowHeight']        = 38;
$RS['init_default']['dialogContentHeight']  = "850px";
$RS['init_default']['dialogMaxWidth']   = "md";// xl lg md sm xs
$RS['init_default']['timeline']         = time();
$RS['init_default']['pageNumber']       = $pageSize;
$RS['init_default']['pageCount']        = ceil($RS['init_default']['total']/$pageSize);
$RS['init_default']['pageNumberArray']  = [10,20,30,40,50,100,200,500];

if(sizeof($columnNames)>5) {
    $pinnedColumns = ['left'=>[],'right'=>['Actions']];
}
else {
    $pinnedColumns = [];
}
$RS['init_default']['pinnedColumns']  = $pinnedColumns;

$RS['init_default']['dataGridLanguageCode']  = $GLOBAL_LANGUAGE;
$RS['init_default']['checkboxSelection']  = false;

$RS['_GET']     = $_GET;
$RS['_POST']    = $_POST;
print_R(json_encode($RS, true));



