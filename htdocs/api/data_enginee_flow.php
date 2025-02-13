<?php
header("Content-Type: application/json");
$TIME_BEGIN = time();
require_once('cors.php');
require_once('include.inc.php');
require_once('data_enginee_function.php');

//print "TIME EXCEUTE 0:".(time()-$TIME_BEGIN)."<BR>\n";
//$FormId = 16;
//$Step = 1;

global $GLOBAL_EXEC_KEY_SQL;
$GLOBAL_EXEC_KEY_SQL = [];
$AdditionalPermissionsSQL = "";

//Get Form Flow Setting
if($FlowId!="")  {
	$sql    = "select * from form_formflow where id='$FlowId'";
}
else {
	$sql    = "select * from form_formflow where FormId='$FormId' and Step='$Step'";
}
$rs         = $db->Execute($sql);
$FromInfo   = $rs->fields;
$FormId  	  = $FromInfo['FormId'];
$FlowId  	  = $FromInfo['id'];
$FlowName  	= $FromInfo['FlowName'];
$Step  		  = $FromInfo['Step'];
$Setting  	= $FromInfo['Setting'];
$FaceTo  	  = $FromInfo['FaceTo'];

//print "TIME EXCEUTE 1:".(time()-$TIME_BEGIN)."<BR>\n";
$rowHeight = 38;
$sqlList = [];

global $SettingMap;
$SettingMap = unserialize(base64_decode($Setting));
$Actions_In_List_Row_Array = explode(',',$SettingMap['Actions_In_List_Row']);
$Actions_In_List_Header_Array = explode(',',$SettingMap['Actions_In_List_Header']);
//print_R($SettingMap);exit;
//print "TIME EXCEUTE 2:".(time()-$TIME_BEGIN)."<BR>\n";


if($FaceTo=="AuthUser")         {
  //Check User Login or Not
  CheckAuthUserLoginStatus();
  CheckAuthUserRoleHaveMenu($FlowId);
  CheckCsrsToken();
}
if($FaceTo=="Student")         {
  //Check User Login or Not
  CheckAuthUserLoginStatus();
  //CheckAuthUserRoleHaveMenu($FlowId);
  CheckCsrsToken();
}

//Get Table Infor
$sql        = "select * from form_formname where id='$FormId'";
$rs         = $db->Execute($sql);
$FromInfo   = $rs->fields;
$TableName  = $FromInfo['TableName'];
global $FormName;
$FormName   = $FromInfo['FullName'];

//EnablePluginsForIndividual
if($SettingMap['EnablePluginsForIndividual']=="Enable" && $TableName!="" && $Step>0 && is_file("../plugins/plugin_".$TableName."_".$Step.".php"))    {
    require_once("../plugins/plugin_".$TableName."_".$Step.".php");
}

//Get form_formfield_showtype
$sql        = "select * from form_formfield_showtype";
$rs         = $db->Execute($sql);
$AllShowTypes   = $rs->GetArray();
$AllShowTypesArray = [];
foreach($AllShowTypes as $Item)  {
    $AllShowTypesArray[$Item['Name']] = $Item;
}
//print "TIME EXCEUTE 3:".(time()-$TIME_BEGIN)."<BR>\n";

//Get All Fields
$sql        = "select * from form_formfield where FormId='$FormId' and IsEnable='1' order by SortNumber asc, id asc";
$rs         = $db->Execute($sql);
$AllFieldsFromTable   = $rs->GetArray();
$AllFieldsMap = [];
foreach($AllFieldsFromTable as $Item)  {
    $Item['Setting']                    = json_decode($Item['Setting'],true);
    $AllFieldsMap[$Item['FieldName']]   = $Item;
    $LocaleFieldArray[$Item['EnglishName']] = $Item['FieldName'];
    $LocaleFieldArray[$Item['ChineseName']] = $Item['FieldName'];
}
//print "TIME EXCEUTE 4:".(time()-$TIME_BEGIN)."<BR>\n";

$MetaColumnNames    = GLOBAL_MetaColumnNames($TableName);
$UniqueKey          = $MetaColumnNames[1];

//Extra Role
$AddSql = " where 1=1 ";
require_once('data_enginee_filter_role.php');

//print "TIME EXCEUTE 6:".(time()-$TIME_BEGIN)."<BR>\n";

global $InsertOrUpdateFieldArrayForSql;
$InsertOrUpdateFieldArrayForSql['ADD']  = [];
$InsertOrUpdateFieldArrayForSql['EDIT'] = [];

$defaultValuesAdd  = [];
$defaultValuesEdit = [];


$allFieldsAdd   = getAllFields($AllFieldsFromTable, $AllShowTypesArray, 'ADD', true, $SettingMap);
foreach($allFieldsAdd as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValuesAdd[$ITEM['name']] = $ITEM['value'];
        if($ITEM['code']!="") {
            $defaultValuesAdd[$ITEM['code']] = $ITEM['value'];
        }
    }
}

$allFieldsEdit  = getAllFields($AllFieldsFromTable, $AllShowTypesArray, 'EDIT', true, $SettingMap);
foreach($allFieldsEdit as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValuesEdit[$ITEM['name']] = $ITEM['value'];
    }
}

$allFieldsView  = getAllFields($AllFieldsFromTable, $AllShowTypesArray, 'VIEW', true, $SettingMap);
foreach($allFieldsView as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $allFieldsView[$ITEM['name']] = $ITEM['value'];
    }
}

//Import Page Structure
$Import_Rule_Method = [];
$Import_Rule_Method[] = ['value'=>"BothInsertAndUpdate", 'label'=>__("BothInsertAndUpdate")];
$Import_Rule_Method[] = ['value'=>"OnlyUpdate", 'label'=>__("OnlyUpdate")];
$Import_Rule_Method[] = ['value'=>"OnlyInsert", 'label'=>__("OnlyInsert")];
$allFieldsImport['Default'][] = ['name' => "Import_Rule_Method", 'show'=>true, 'type'=>'select', 'options'=>$Import_Rule_Method, 'label' => __("Step1_Choose_Import_Rule"), 'value' => $Import_Rule_Method[0]['value'], 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => false, 'disabled' => false, 'xs'=>12, 'sm'=>12]];

$Import_Fields          = [];
$Import_Fields_Default  = [];
foreach($AllFieldsFromTable as $Item)  {
    if($SettingMap["FieldImport_".$Item['FieldName']]=="true" || $SettingMap["FieldImport_".$Item['FieldName']]=="1")   {
        $Import_Fields[]            = ['value'=>$Item['FieldName'], 'label'=>$Item['ChineseName']];
        $Import_Fields_Default[]    = $Item['FieldName'];
    }
}
$allFieldsImport['Default'][] = ['name' => "Import_Fields", 'show'=>true, 'type'=>'checkbox', 'options'=>$Import_Fields, 'label' => __("Step2_Choose_Import_Fields"), 'value' => join(',', $Import_Fields_Default), 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => true, 'disabled' => false, 'xs'=>12, 'sm'=>12, 'row'=>true]];

$TEMPARRAY                      = [];
$TEMPARRAY['TableName']         = $TableName;
$TEMPARRAY['Action']            = "export_template";
$TEMPARRAY['FormId']            = $FormId;
$TEMPARRAY['FlowId']            = $FlowId;
$TEMPARRAY['FileName']          = $FormName;
$TEMPARRAY['Time']              = time();
$DATATEMP                       = EncryptID(serialize($TEMPARRAY));
$URLTEMP                        = "data_export_json.php?DATA=".$DATATEMP;
$allFieldsImport['Default'][] = ['name' => "Import_Template", 'show'=>true, 'FieldTypeArray'=>[], 'type'=>'buttonurl', 'label' => __("Import_Template_File"), 'value' => $URLTEMP, 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false] ];

$allFieldsImport['Default'][] = ['name' => "Import_File", 'show'=>true, 'FieldTypeArray'=>[], 'type'=>'xlsx', 'label' => __("Step3_Upload_Excel_File"), 'value' => "", 'placeholder' => "", 'helptext' => __(""), 'rules' => ['required' => true,'xs'=>12, 'sm'=>12, 'disabled' => false] ];


foreach($allFieldsImport as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValuesImport[$ITEM['name']] = $ITEM['value'];
    }
}

$allFieldsExport        = [];
foreach($allFieldsView as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        if($SettingMap["FieldExport_".$ITEM['name']]=="true" || $SettingMap["FieldExport_".$ITEM['name']]=="1" || $SettingMap["FieldExport_".$ITEM['code']]=="true" || $SettingMap["FieldExport_".$ITEM['code']]=="1")   {
            $allFieldsExport[$ModeName][] = $ITEM;
        }
    }
}

//print "TIME EXCEUTE 7:".(time()-$TIME_BEGIN)."<BR>\n";
//UpdateOtherTableFieldAfterFormSubmit($id);

if($_GET['action']=="option_multi_approval")  {
    $option_multi_approval = option_multi_approval_exection($_POST['selectedRows'], $_POST['multiReviewInputValue'], $Reminder=1, $UpdateOtherTableField=1);
    print $option_multi_approval;
    exit;
}

if($_GET['action']=="option_multi_refuse")  {
    $option_multi_refuse = option_multi_refuse_exection($_POST['selectedRows'], $_POST['multiReviewInputValue'], $Reminder=1, $UpdateOtherTableField=1);
    print $option_multi_refuse;
    exit;
}

if($_GET['action']=="option_multi_cancel")  {
    $option_multi_cancel = option_multi_cancel_exection($_POST['selectedRows'], $_POST['multiReviewInputValue'], $Reminder=1, $UpdateOtherTableField=1);
    print $option_multi_cancel;
    exit;
}

if($_GET['action']=="option_multi_setting_one")  {
    $option_multi_setting_one = option_multi_setting_one_exection($_POST['selectedRows'], $_POST['multiReviewInputValue'], $Reminder=1, $UpdateOtherTableField=1);
    print $option_multi_setting_one;
    exit;
}

if($_GET['action']=="option_multi_setting_two")  {
    $option_multi_setting_two = option_multi_setting_two_exection($_POST['selectedRows'], $_POST['multiReviewInputValue'], $Reminder=1, $UpdateOtherTableField=1);
    print $option_multi_setting_two;
    exit;
}

if( $_GET['action']=="import_default_data" && in_array('Import',$Actions_In_List_Header_Array) && $TableName!="")  {

    //Filter data when do add save operation
    require_once('data_enginee_filter_post.php');
    $MetaColumnNames    = GLOBAL_MetaColumnNames($TableName);

    $filePath = $_FILES['Import_File']['tmp_name']['0'];
    if(!is_file($filePath))  {
        $RS             = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("Upload File Not Exist");
        $RS['data']     = $data;
        print json_encode($RS);
        exit;
    }

    //Read Data From Excel
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    $data = [];
    for ($row = 1; $row <= $highestRow; $row++) {
        $rowData = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            $rowData[] = trim($cellValue);
        }
        $data[] = $rowData;
    }
    $Header         = $data[0];
    $FieldToIndex   = array_flip($Header);

    //Import Parse Data
    $Import_Fields_Unique_1 = $SettingMap['Import_Fields_Unique_1'];
    $Import_Fields_Unique_2 = $SettingMap['Import_Fields_Unique_2'];
    $Import_Fields_Unique_3 = $SettingMap['Import_Fields_Unique_3'];
    $ImportUniqueFields = [];
    if($Import_Fields_Unique_1!="Disabled" && $Import_Fields_Unique_1!="" && $Import_Fields_Unique_1!="id")  {
        $ImportUniqueFields[] = $Import_Fields_Unique_1;
    }
    if($Import_Fields_Unique_2!="Disabled" && $Import_Fields_Unique_2!="" && $Import_Fields_Unique_2!="id")  {
        $ImportUniqueFields[] = $Import_Fields_Unique_2;
    }
    if($Import_Fields_Unique_3!="Disabled" && $Import_Fields_Unique_3!="" && $Import_Fields_Unique_3!="id")  {
        $ImportUniqueFields[] = $Import_Fields_Unique_3;
    }
    if(sizeof($ImportUniqueFields)==0)   {
        $RS             = [];
        $RS['status']   = "OK";
        $RS['msg']      = __("Import Unique Fields Not Config");
        print json_encode($RS);
        exit;
    }
    //Body Data
    $Import_Fields_Array = explode(',',$_POST['Import_Fields']);
    for ($row = 1; $row < sizeof($data); $row++) {
        $Element        = [];
        $IsExecutionSQL = 0;
        for ($column = 0; $column < sizeof($Header); $column++)         {
            $FieldName  = $LocaleFieldArray[$Header[$column]];
            if( in_array($FieldName, $MetaColumnNames) && in_array($FieldName,$Import_Fields_Array))  {
                $Element[$FieldName] = trim($data[$row][$column]);
                if($Element[$FieldName]!="")   {
                    $IsExecutionSQL = 1;
                }
                //Decrypt Field Value
                $SettingTempMap                 = $AllFieldsMap[$FieldName]['Setting'];
                $DataFieldEncryptMethod         = $SettingTempMap['DataFieldEncryptMethod'];
                $DataFieldEncryptKey            = $SettingTempMap['DataFieldEncryptKey'];
                if($DataFieldEncryptMethod==1&&$DataFieldEncryptKey!="") {
                    $Element[$FieldName]        = EncryptIDStorage($Element[$FieldName], $DataFieldEncryptKey);
                }
            }
        }
        if(sizeof(array_keys($Element))<=sizeof($ImportUniqueFields)) {
            $RS             = [];
            $RS['status']   = "ERROR";
            $RS['msg']      = __("Import Fields Is Too Less");
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_GET']     = $_GET;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_POST']    = $_POST;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_FILES']   = $_FILES;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql']      = $sqlList;
            print json_encode($RS);
            exit;
        }
        if($IsExecutionSQL)    {
            //functionNameIndividual
            $functionNameIndividual = "plugin_".$TableName."_".$Step."_import_default_data_before_submit";
            if(function_exists($functionNameIndividual))  {
                $Element = $functionNameIndividual($Element);
            }

            $Import_Rule_Method = $_POST['Import_Rule_Method'];
            switch($Import_Rule_Method) {
                case 'BothInsertAndUpdate':
                    [$rs,$sql] = InsertOrUpdateTableByArray($TableName,$Element,join(',',$ImportUniqueFields),0,'InsertOrUpdate');
                    $sqlList[] = $sql;
                    break;
                case 'OnlyUpdate':
                    [$rs,$sql] = InsertOrUpdateTableByArray($TableName,$Element,join(',',$ImportUniqueFields),0,'Update');
                    $sqlList[] = $sql;
                    break;
                case 'OnlyInsert':
                    [$rs,$sql] = InsertOrUpdateTableByArray($TableName,$Element,join(',',$ImportUniqueFields),0,'Insert');
                    $sqlList[] = $sql;
                    break;
            }
            if($rs->EOF) {
            }
        }
        else {
            //Empty Row
        }
    }

    //functionNameIndividual
    $functionNameIndividual = "plugin_".$TableName."_".$Step."_import_default_data_after_submit";
    if(function_exists($functionNameIndividual))  {
        $functionNameIndividual();
    }

    if(1)   {
        $RS             = [];
        $RS['status']   = "OK";
        $RS['msg']      = __("Import Data Success");
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_GET']     = $_GET;
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_POST']    = $_POST;
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_FILES']   = $_FILES;
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql']      = $sqlList;
        $RS['counter']  = sizeof($data);
        print json_encode($RS);
        exit;
    }

}

//编辑页面时的启用字段列表
if( $_GET['action']=="add_default_data" && in_array('Add',$Actions_In_List_Header_Array) && $TableName!="")  {

    //Filter data when do add save operation
    require_once('data_enginee_filter_post.php');
    $MetaColumns    = $db->MetaColumns($TableName);
    $MetaColumns    = array_values($MetaColumns);
    $MetaColumnsInDb = [];
    foreach($MetaColumns as $Item)  {
        $MetaColumnsInDb[$Item->name]       = $Item->type;
    }
    $MetaColumnNames    = GLOBAL_MetaColumnNames($TableName);

    //functionNameIndividual
    $functionNameIndividual = "plugin_".$TableName."_".$Step."_add_default_data_before_submit";
    if(function_exists($functionNameIndividual))  {
        $functionNameIndividual();
    }

    $FieldsArray        = [];
    $IsExecutionSQL     = 0;
    foreach($AllFieldsFromTable as $Item)  {
        if($_POST[$Item['FieldName']]!="") {
            $IsExecutionSQL = 1;
        }
        if($_POST[$Item['FieldName']]=="undefined") {
            $_POST[$Item['FieldName']] = "";
        }
        // Give a default value for date and number
        $FieldType = $MetaColumnsInDb[$Item['FieldName']];
        if($_POST[$Item['FieldName']]=="") {
            switch($FieldType)  {
                case 'int':
                    $_POST[$Item['FieldName']] = 0;
                    break;
                case 'date':
                    //$_POST[$Item['FieldName']] = "1971-01-01";
                    break;
                case 'datetime':
                    //$_POST[$Item['FieldName']] = "1971-01-01 00:00:00";
                    break;
            }
            $CurrentFieldType = $AllShowTypesArray[$AllFieldsMap[$Item['FieldName']]['ShowType']]['ADD'];
            switch($CurrentFieldType) {
                case '32位全局唯一编码字符串':
                    $学校十位代码   = returntablefield("ods_zzxxgkjcsj","id",1,"XXDM")['XXDM'];
                    $数据项编号     = $AllFieldsMap[$Item['FieldName']]['Placeholder'];
                    $唯一编码前缀   = $学校十位代码.$数据项编号;
                    $剩余位数       = 32-strlen($唯一编码前缀);
                    $sql = "select max(id) as NUM from $TableName";
                    $rs  = $db->Execute($sql);
                    $NUM = intval($rs->fields['NUM']);
                    $NUM += 1;
                    $补齐0数量      = $剩余位数-strlen($NUM);
                    while($补齐0数量>0) {
                        $唯一编码前缀 .= "0";
                        $补齐0数量 --;
                    }
                    $_POST[$Item['FieldName']] = $唯一编码前缀.$NUM;
                    break;
                case 'autoincrement':
                    $sql = "select max(id) as NUM from $TableName";
                    $rs  = $db->Execute($sql);
                    $NUM = intval($rs->fields['NUM']);
                    $NUM += 1;
                    $FROM = 100000;
                    $NUM += $FROM;
                    $_POST[$Item['FieldName']] = $NUM;
                    break;
                case 'autoincrementdate':
                    $sql = "select max(id) as NUM from $TableName";
                    $rs  = $db->Execute($sql);
                    $NUM = $rs->fields['NUM'];
                    $NUM += 1;
                    $FROM = date('Ymd');
                    if(strlen($NUM)==1) {
                        $NUM = $FROM."000".$NUM;
                    }
                    else if(strlen($NUM)==2) {
                        $NUM = $FROM."00".$NUM;
                    }
                    else if(strlen($NUM)==3) {
                        $NUM = $FROM."0".$NUM;
                    }
                    $_POST[$Item['FieldName']] = $NUM;
                    break;
                case 'avatar':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        ImageUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    elseif(strpos($_POST[$Item['FieldName']], "data_image.php?")!==false)  {
                        //Delete this Key from FieldsArray
                        $FieldsArray = array_diff_key($FieldsArray,[$Item['FieldName']=>""]);
                    }
                    break;
                case 'images':
                case 'images2':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        FilesUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    elseif(strpos($_POST[$Item['FieldName']], "data_image.php?")!==false)  {
                        //Delete this Key from FieldsArray
                        $FieldsArray = array_diff_key($FieldsArray,[$Item['FieldName']=>""]);
                    }
                    break;
                case 'files':
                case 'files2':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        FilesUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    elseif(strpos($_POST[$Item['FieldName']], "data_image.php?")!==false)  {
                        //Delete this Key from FieldsArray
                        $FieldsArray = array_diff_key($FieldsArray,[$Item['FieldName']=>""]);
                    }
                    break;
                case 'file':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        FilesUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    elseif(strpos($_POST[$Item['FieldName']], "data_image.php?")!==false)  {
                        //Delete this Key from FieldsArray
                        $FieldsArray = array_diff_key($FieldsArray,[$Item['FieldName']=>""]);
                    }
                    break;
                case 'xlsx':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        FilesUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    elseif(strpos($_POST[$Item['FieldName']], "data_image.php?")!==false)  {
                        //Delete this Key from FieldsArray
                        $FieldsArray = array_diff_key($FieldsArray,[$Item['FieldName']=>""]);
                    }
                    break;
            }
        }
        $FieldsArray[$Item['FieldName']]        = addslashes($_POST[$Item['FieldName']]);
        //To check need encrypt field value
        $FieldName                      = $Item['FieldName'];
        $SettingTempMap                 = $AllFieldsMap[$FieldName]['Setting'];
        $DataFieldEncryptMethod         = $SettingTempMap['DataFieldEncryptMethod'];
        $DataFieldEncryptKey            = $SettingTempMap['DataFieldEncryptKey'];
        if($DataFieldEncryptMethod==1&&$DataFieldEncryptKey!="") {
            $FieldsArray[$FieldName]            = EncryptIDStorage($FieldsArray[$FieldName], $DataFieldEncryptKey);
        }
    }
    if($IsExecutionSQL)   {
        global $InsertOrUpdateFieldArrayForSql; //Define in data_enginee_function.php
            foreach($InsertOrUpdateFieldArrayForSql['ADD'] as $FieldName=>$FieldValue)  {
                if($FieldValue=="EncryptField"&&$_POST[$FieldName]=="") {
                    //Not Need To Update Field Value
                }
                else if($FieldValue=="EncryptField"&&$_POST[$FieldName]!="") {
                    $FieldsArray[$FieldName]       = addslashes($_POST[$FieldName]);
                }
                else if($FieldValue!="")   {
                    $FieldsArray[$FieldName]        = $FieldValue;
                }
        }

        //Split Multi Records
        $Add_Page_Split_Multi_Records_Value_Array = [];
        $Add_Page_Split_Multi_Records = $SettingMap['AddPageSplitMultiRecords'];
        if($Add_Page_Split_Multi_Records!="" && $Add_Page_Split_Multi_Records!="None" && in_array($Add_Page_Split_Multi_Records,$MetaColumnNames) )      {
            $Add_Page_Split_Multi_Records_Value_Array = explode(',', $FieldsArray[$Add_Page_Split_Multi_Records]);
        }
        else {
            //Default a Value for Not Need To Split
            $Add_Page_Split_Multi_Records = "id";
            $Add_Page_Split_Multi_Records_Value_Array = [NULL];
        }
        //Begin to Split Multi Records
        foreach($Add_Page_Split_Multi_Records_Value_Array as $Add_Page_Split_Multi_Records_Value)    {
            $FieldsArray[$Add_Page_Split_Multi_Records] = $Add_Page_Split_Multi_Records_Value;
            //Syncing To Other Fields
            if($Add_Page_Split_Multi_Records=="学号" || $Add_Page_Split_Multi_Records=="学生学号") {
                $sql     = "select * from data_student where 学号 = '".ForSqlInjection($Add_Page_Split_Multi_Records_Value)."'";
                $rsf     = $db->Execute($sql);
                in_array("系部",$MetaColumnNames) ? $FieldsArray['系部'] = $rsf->fields['系部'] : '';
                in_array("专业",$MetaColumnNames) ? $FieldsArray['专业'] = $rsf->fields['专业'] : '';
                in_array("班级",$MetaColumnNames) ? $FieldsArray['班级'] = $rsf->fields['班级'] : '';
                in_array("姓名",$MetaColumnNames) ? $FieldsArray['姓名'] = $rsf->fields['姓名'] : '';
                in_array("学生班级",$MetaColumnNames) ? $FieldsArray['学生班级'] = $rsf->fields['学生班级'] : '';
                in_array("学生姓名",$MetaColumnNames) ? $FieldsArray['学生姓名'] = $rsf->fields['学生姓名'] : '';
                in_array("身份证号",$MetaColumnNames) ? $FieldsArray['身份证号'] = $rsf->fields['身份证号'] : '';
                in_array("出生日期",$MetaColumnNames) ? $FieldsArray['出生日期'] = $rsf->fields['出生日期'] : '';
                in_array("性别",$MetaColumnNames) ? $FieldsArray['性别'] = $rsf->fields['性别'] : '';
                in_array("座号",$MetaColumnNames) ? $FieldsArray['座号'] = $rsf->fields['座号'] : '';
                in_array("学生宿舍",$MetaColumnNames) ? $FieldsArray['学生宿舍'] = $rsf->fields['学生宿舍'] : '';
                in_array("学生状态",$MetaColumnNames) ? $FieldsArray['学生状态'] = $rsf->fields['学生状态'] : '';
                in_array("学生手机",$MetaColumnNames) ? $FieldsArray['学生手机'] = $rsf->fields['学生手机'] : '';
            }
            //Unique Fields
            $SQL_Unique_Fields = ['1=1'];
            if($SettingMap['Unique_Fields_1']!="" && $SettingMap['Unique_Fields_1']!="None" && in_array($SettingMap['Unique_Fields_1'],$MetaColumnNames) ) {
                $SQL_Unique_Fields[] = $SettingMap['Unique_Fields_1']." = '".$FieldsArray[$SettingMap['Unique_Fields_1']]."' ";
            }
            if($SettingMap['Unique_Fields_2']!="" && $SettingMap['Unique_Fields_2']!="None" && in_array($SettingMap['Unique_Fields_2'],$MetaColumnNames) ) {
                $SQL_Unique_Fields[] = $SettingMap['Unique_Fields_2']." = '".$FieldsArray[$SettingMap['Unique_Fields_2']]."' ";
            }
            if($SettingMap['Unique_Fields_3']!="" && $SettingMap['Unique_Fields_3']!="None" && in_array($SettingMap['Unique_Fields_3'],$MetaColumnNames) ) {
                $SQL_Unique_Fields[] = $SettingMap['Unique_Fields_3']." = '".$FieldsArray[$SettingMap['Unique_Fields_3']]."' ";
            }
            if(sizeof($SQL_Unique_Fields)>1) {
                $sql    = "select COUNT(*) AS NUM from $TableName where ".join(" and ", $SQL_Unique_Fields)."";
                $rsTemp = $db->Execute($sql);
                if($rsTemp->fields['NUM']>=1) {
                    $RS = [];
                    $RS['status'] = "ERROR";
                    $RS['msg'] = $SettingMap['Unique_Fields_Repeat_Text']?$SettingMap['Unique_Fields_Repeat_Text']:__('Unique_Fields_Repeat_Text');
                    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
                    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_GET'] = $_GET;
                    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_POST'] = $_POST;
                    print json_encode($RS);
                    exit;
                }
            }

            //Execute Insert SQL
            $KEYS			= array_keys($FieldsArray);
            $VALUES			= array_values($FieldsArray);
            $sql	        = "insert into $TableName(`".join('`,`',$KEYS)."`) values('".join("','",$VALUES)."')";
            $rs             = $db->Execute($sql);
        }
        if($rs->EOF) {
            $NewId = $db->Insert_ID();
            UpdateOtherTableFieldAfterFormSubmit($NewId);
            $Msg_Reminder_Object_From_Add_Or_Edit_Result = Msg_Reminder_Object_From_Add_Or_Edit($TableName, $NewId);
            $RS['status'] = "OK";
            $RS['msg'] = $SettingMap['Tip_When_Add_Success'];
            $RS['Msg_Reminder_Object_From_Add_Or_Edit_Result'] = $Msg_Reminder_Object_From_Add_Or_Edit_Result;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes")  {
                $RS['sql'] = $sql;
                global $GLOBAL_EXEC_KEY_SQL;
                $RS['GLOBAL_EXEC_KEY_SQL'] = $GLOBAL_EXEC_KEY_SQL;
            }

            //Relative Child Table Support
            $Relative_Child_Table                   = $SettingMap['Relative_Child_Table'];
            $Relative_Child_Table_Field_Name        = $SettingMap['Relative_Child_Table_Field_Name'];
            $Relative_Child_Table_Parent_Field_Name = $SettingMap['Relative_Child_Table_Parent_Field_Name'];
            if($Relative_Child_Table>0 && $Relative_Child_Table_Parent_Field_Name!="" && in_array($Relative_Child_Table_Parent_Field_Name,$MetaColumnNames)) {
                $ChildSettingMap = returntablefield("form_formflow",'id',$Relative_Child_Table,'Setting')['Setting'];
                $ChildSettingMap = unserialize(base64_decode($ChildSettingMap));
                $ChildFormId                = returntablefield("form_formflow",'id',$Relative_Child_Table,'FormId')['FormId'];
                $ChildTableName             = returntablefield("form_formname",'id',$ChildFormId,'TableName')['TableName'];
                $ChildMetaColumnNames       = GLOBAL_MetaColumnNames($ChildTableName);
                if($Relative_Child_Table_Field_Name!="" && in_array($Relative_Child_Table_Field_Name, $ChildMetaColumnNames) &&strpos($ChildSettingMap['Actions_In_List_Row'],'Edit')!==false) {
                    //Get All Fields
                    $db->BeginTrans();
                    $MultiSql                   = [];
                    $sql                        = "delete from $ChildTableName where $Relative_Child_Table_Parent_Field_Name = '".$FieldsArray[$Relative_Child_Table_Parent_Field_Name]."';";
                    $db->Execute($sql);
                    $MultiSql[]                 = $sql;
                    $sql                        = "select * from form_formfield where FormId='$ChildFormId' and IsEnable='1' order by SortNumber asc, id asc";
                    $rs                         = $db->Execute($sql);
                    $ChildAllFieldsFromTable    = $rs->GetArray();
                    $ChildAllFieldsMap          = [];
                    $ChildItemCounter           = $_POST['ChildItemCounter'];
                    for($X=0;$X<$ChildItemCounter;$X++)                    {
                        $ChildElement = [];
                        foreach($ChildAllFieldsFromTable as $Item)  {
                            $ChildFieldName = $Item['FieldName'];
                            switch($Item['ShowType']) {
                                case 'Hidden:Createtime':
                                    $ChildElement[$ChildFieldName] = date('Y-m-d H:i:s');
                                    break;
                                case 'Hidden:CurrentUserIdAdd':
                                case 'Hidden:CurrentUserIdAddEdit':
                                    $ChildElement[$ChildFieldName] = $GLOBAL_USER->USER_ID;
                                    break;
                                case 'Hidden:CurrentStudentCodeAdd':
                                case 'Hidden:CurrentStudentCodeAddEdit':
                                    if($GLOBAL_USER->学号=="") $GLOBAL_USER->学号 = $GLOBAL_USER->USER_ID;
                                    $ChildElement[$ChildFieldName] = $GLOBAL_USER->学号;
                                    break;
                                default:
                                    $ChildElement[$ChildFieldName] = ForSqlInjection($_POST['ChildTable____'.$X.'____'.$ChildFieldName]);
                                    break;
                            }
                        }
                        $deleteChildTableItemArray = explode(',',$_POST['deleteChildTableItemArray']);
                        if(!in_array($X, $deleteChildTableItemArray)) {
                            $ChildElement[$Relative_Child_Table_Parent_Field_Name] = $FieldsArray[$Relative_Child_Table_Parent_Field_Name];
                            $ChildKeys      = array_keys($ChildElement);
                            $ChildValues    = array_values($ChildElement);
                            $sql            = "insert into $ChildTableName (".join(',',$ChildKeys).") values('".join("','",$ChildValues)."');";
                            $db->Execute($sql);
                            $MultiSql[]     = $sql;
                        }
                    }
                    $db->CommitTrans();
                    $RS['MultiSql'] = $MultiSql;
                }
            }

            //functionNameIndividual
            $functionNameIndividual = "plugin_".$TableName."_".$Step."_add_default_data_after_submit";
            if(function_exists($functionNameIndividual))  {
                $functionNameIndividual($NewId);
            }
            //SystemLogRecord
            if(in_array($SettingMap['OperationLogGrade'],["AddEditAndDeleteOperation","AllOperation"]))  {
                $sql    = "select * from $TableName where ".$MetaColumnNames[0]." = '$NewId'";
                $Record = $db->Execute($sql);
                SystemLogRecord("add_default_data", '', json_encode($Record->fields));
            }
            print json_encode($RS);
            exit;
        }
        else {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['msg'] = __("sql execution failed");
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_GET'] = $_GET;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_POST'] = $_POST;
            print json_encode($RS);
            exit;
        }
    }
    else {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("No POST Infor");
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_GET'] = $_GET;
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }
}

if( $_GET['action']=="edit_default_data" && in_array('Edit',$Actions_In_List_Row_Array) && $_GET['id']!="" && $TableName!="")  {
    if($TableName=="data_user" && $SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']=="email") {
        $EMAIL  = $GLOBAL_USER->email;
        $id     = returntablefield($TableName,"EMAIL",$EMAIL,"id")["id"];
    }
    else if($TableName=="data_user" && $SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']=="USER_ID") {
        $USER_ID  = $GLOBAL_USER->USER_ID;
        $id     = returntablefield($TableName,"USER_ID",$USER_ID,"id")["id"];
    }
    else if($TableName=="data_xiaoyou_member" && $SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']=="USER_ID") {
        $USER_ID  = $GLOBAL_USER->USER_ID;
        $id     = returntablefield($TableName,"学生学号",$USER_ID,"id")["id"];
    }
    else if($TableName=="data_student" && $SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']=="学号") {
        $学号    = $GLOBAL_USER->学号;
        $id     = returntablefield($TableName,"学号",$学号,"id")["id"];
    }
    else if($SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']!="") {
        $id     = intval($SettingMap['Init_Action_FilterValue']);
    }
    else {
        $id     = intval(DecryptID($_GET['id']));
    }
    if($id==0)   {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("Error Id Value");
        $RS['_GET'] = $_GET;
        $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }
    $MetaColumnNames    = GLOBAL_MetaColumnNames($TableName);
    $FieldsArray        = [];
    $FieldsArray['id']  = $id;
    $IsExecutionSQL     = 0;
    $IsExecutionSQLChildTable     = 0;
    //Filter data when do edit save operation
    require_once('data_enginee_filter_post.php');

    //functionNameIndividual
    $functionNameIndividual = "plugin_".$TableName."_".$Step."_edit_default_data_before_submit";
    if(function_exists($functionNameIndividual))  {
        $functionNameIndividual($id);
    }

    global $InsertOrUpdateFieldArrayForSql; //Define in data_enginee_function.php
    //print_R($InsertOrUpdateFieldArrayForSql);exit;
    foreach($InsertOrUpdateFieldArrayForSql['EDIT'] as $FieldName=>$FieldValue)  {
        if($FieldValue=="EncryptField"&&$_POST[$FieldName]=="") {
            //Not Need To Update Field Value
        }
        else if($FieldValue=="EncryptField"&&$_POST[$FieldName]!="") {
            $FieldsArray[$FieldName]       = addslashes($_POST[$FieldName]);
        }
        else if($FieldValue==""&&is_string($_POST[$FieldName]))   {
            $FieldsArray[$FieldName]       = addslashes($_POST[$FieldName]);
            //To check need encrypt field value
            $SettingTempMap                 = $AllFieldsMap[$FieldName]['Setting'];
            $DataFieldEncryptMethod         = $SettingTempMap['DataFieldEncryptMethod'];
            $DataFieldEncryptKey            = $SettingTempMap['DataFieldEncryptKey'];
            if($DataFieldEncryptMethod==1&&$DataFieldEncryptKey!="") {
                $FieldsArray[$FieldName]    = EncryptIDStorage($FieldsArray[$FieldName], $DataFieldEncryptKey);
            }
        }
        else if($FieldValue==""&&is_array($_POST[$FieldName]))   {
            $FieldsArray[$FieldName]       = $_POST[$FieldName];
        }
        else {
            $FieldsArray[$FieldName]       = $FieldValue;
        }
        if($_POST[$FieldName]!="") {
            $IsExecutionSQL = 1;
        }
        if($_POST['ChildItemCounter']>0) {
            $IsExecutionSQLChildTable = 1;
        }
    }
    //Check Permission For This Record
    //LimitEditAndDelete
    $sql            = "select * from $TableName where ".$MetaColumnNames[0]." = '$id'";
    $RecordOriginal = $db->Execute($sql);
    if($SettingMap['LimitEditAndDelete_Edit_Field_One']!="" && $SettingMap['LimitEditAndDelete_Edit_Field_One']!="None" && in_array($SettingMap['LimitEditAndDelete_Edit_Field_One'], $MetaColumnNames)) {
        $LimitEditAndDelete_Edit_Value_One_Array = explode(',',$SettingMap['LimitEditAndDelete_Edit_Value_One']);
        if(in_array($RecordOriginal->fields[$SettingMap['LimitEditAndDelete_Edit_Field_One']],$LimitEditAndDelete_Edit_Value_One_Array)) {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['msg'] = __("LimitEditAndDelete");
            $RS['_GET'] = $_GET;
            $RS['_POST'] = $_POST;
            print json_encode($RS);
            exit;
        }
    }
    if($SettingMap['LimitEditAndDelete_Edit_Field_Two']!="" && $SettingMap['LimitEditAndDelete_Edit_Field_Two']!="None" && in_array($SettingMap['LimitEditAndDelete_Edit_Field_Two'], $MetaColumnNames)) {
        $LimitEditAndDelete_Edit_Value_Two_Array = explode(',',$SettingMap['LimitEditAndDelete_Edit_Value_Two']);
        if(in_array($RecordOriginal->fields[$SettingMap['LimitEditAndDelete_Edit_Field_Two']],$LimitEditAndDelete_Edit_Value_Two_Array)) {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['msg'] = __("LimitEditAndDelete");
            $RS['_GET'] = $_GET;
            $RS['_POST'] = $_POST;
            print json_encode($RS);
            exit;
        }
    }
    foreach($AllFieldsFromTable as $Item)  {
        $CurrentFieldType = $AllShowTypesArray[$AllFieldsMap[$Item['FieldName']]['ShowType']]['EDIT'];
        $AllowEditFiledInEditMode = array_keys($InsertOrUpdateFieldArrayForSql['EDIT']);
        if(in_array($Item['FieldName'], $AllowEditFiledInEditMode)) {
            switch($CurrentFieldType) {
                case 'avatar':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        ImageUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    elseif(strpos($_POST[$Item['FieldName']], "data_image.php?")!==false)  {
                        //Delete this Key from FieldsArray
                        $FieldsArray = array_diff_key($FieldsArray,[$Item['FieldName']=>""]);
                    }
                    break;
                case 'images':
                case 'images2':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        FilesUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    if(is_array($_POST[$Item['FieldName']."_OriginalFieldValue"]))  {
                        $OriginalValue = $RecordOriginal->fields[$Item['FieldName']];
                        $FieldsArray[$Item['FieldName']]    = AttachValueMinusOneFile($OriginalValue, $_POST[$Item['FieldName']."_OriginalFieldValue"], $FieldsArray[$Item['FieldName']]);
                    }
                    if(!is_array($_FILES[$Item['FieldName']]) && !is_array($_POST[$Item['FieldName']."_OriginalFieldValue"]))    {
                        $FieldsArray[$Item['FieldName']]    = "";
                    }
                    break;
                case 'files':
                case 'files2':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        FilesUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    if(is_array($_POST[$Item['FieldName']."_OriginalFieldValue"]))  {
                        $OriginalValue = $RecordOriginal->fields[$Item['FieldName']];
                        $FieldsArray[$Item['FieldName']]    = AttachValueMinusOneFile($OriginalValue, $_POST[$Item['FieldName']."_OriginalFieldValue"], $FieldsArray[$Item['FieldName']]);
                    }
                    if(!is_array($_FILES[$Item['FieldName']]) && !is_array($_POST[$Item['FieldName']."_OriginalFieldValue"]))    {
                        $FieldsArray[$Item['FieldName']]    = "";
                    }
                    break;
                case 'file':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        FilesUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    if(is_array($_POST[$Item['FieldName']."_OriginalFieldValue"]))  {
                        $OriginalValue = $RecordOriginal->fields[$Item['FieldName']];
                        $FieldsArray[$Item['FieldName']]    = AttachValueMinusOneFile($OriginalValue, $_POST[$Item['FieldName']."_OriginalFieldValue"], $FieldsArray[$Item['FieldName']]);
                    }
                    if(!is_array($_FILES[$Item['FieldName']]) && !is_array($_POST[$Item['FieldName']."_OriginalFieldValue"]))    {
                        $FieldsArray[$Item['FieldName']]    = "";
                    }
                    break;
                case 'xlsx':
                    if(is_array($_FILES[$Item['FieldName']]))    {
                        FilesUploadToDisk($Item['FieldName']);
                        $FieldsArray[$Item['FieldName']]    = addslashes($_POST[$Item['FieldName']]);
                    }
                    if(is_array($_POST[$Item['FieldName']."_OriginalFieldValue"]))  {
                        $OriginalValue = $RecordOriginal->fields[$Item['FieldName']];
                        $FieldsArray[$Item['FieldName']]    = AttachValueMinusOneFile($OriginalValue, $_POST[$Item['FieldName']."_OriginalFieldValue"], $FieldsArray[$Item['FieldName']]);
                    }
                    if(!is_array($_FILES[$Item['FieldName']]) && !is_array($_POST[$Item['FieldName']."_OriginalFieldValue"]))    {
                        $FieldsArray[$Item['FieldName']]    = "";
                    }
                    break;
            }
        }
    }

    if($IsExecutionSQL || $IsExecutionSQLChildTable)   {
        [$Record,$sql]  = InsertOrUpdateTableByArray($TableName, $FieldsArray, 'id', 0, "Update");
        if($Record->EOF) {
            UpdateOtherTableFieldAfterFormSubmit($FieldsArray['id']);
            $Msg_Reminder_Object_From_Add_Or_Edit_Result = Msg_Reminder_Object_From_Add_Or_Edit($TableName, $FieldsArray['id']);
            $RS['status'] = "OK";
            $RS['msg'] = $SettingMap['Tip_When_Edit_Success'];
            $RS['Msg_Reminder_Object_From_Add_Or_Edit_Result'] = $Msg_Reminder_Object_From_Add_Or_Edit_Result;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes")  {
                global $GLOBAL_EXEC_KEY_SQL;
                $RS['sql'] = $sql;
                $RS['GLOBAL_EXEC_KEY_SQL'] = $GLOBAL_EXEC_KEY_SQL;
            }
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_POST'] = $_POST;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_FILES'] = $_FILES;
            //Batch_Approval
            $Batch_Approval_Status_Field    = $SettingMap['Batch_Approval_Status_Field'];
            $Batch_Approval_Status_Value    = $SettingMap['Batch_Approval_Status_Value'];
            if($Batch_Approval_Status_Value!="" && $_POST[$Batch_Approval_Status_Field]==$Batch_Approval_Status_Value)  {
                option_multi_approval_exection($FieldsArray['id'], $multiReviewInputValue='', $Reminder=0, $UpdateOtherTableField=0);
            }
            //Batch_Cancel
            $Batch_Cancel_Status_Field    = $SettingMap['Batch_Cancel_Status_Field'];
            $Batch_Cancel_Status_Value    = $SettingMap['Batch_Cancel_Status_Value'];
            if($Batch_Cancel_Status_Value!="" && $_POST[$Batch_Cancel_Status_Field]==$Batch_Cancel_Status_Value)  {
                option_multi_cancel_exection($FieldsArray['id'], $multiReviewInputValue='', $Reminder=0, $UpdateOtherTableField=0);
            }
            //Batch_Refuse
            $Batch_Refuse_Status_Field    = $SettingMap['Batch_Refuse_Status_Field'];
            $Batch_Refuse_Status_Value    = $SettingMap['Batch_Refuse_Status_Value'];
            if($Batch_Refuse_Status_Value!="" && $_POST[$Batch_Refuse_Status_Field]==$Batch_Refuse_Status_Value)  {
                option_multi_refuse_exection($FieldsArray['id'], $multiReviewInputValue='', $Reminder=0, $UpdateOtherTableField=0);
            }
            //Relative Child Table Support
            $Relative_Child_Table                   = $SettingMap['Relative_Child_Table'];
            $Relative_Child_Table_Field_Name        = $SettingMap['Relative_Child_Table_Field_Name'];
            $Relative_Child_Table_Parent_Field_Name = $SettingMap['Relative_Child_Table_Parent_Field_Name'];
            if($Relative_Child_Table>0 && $Relative_Child_Table_Parent_Field_Name!="" && in_array($Relative_Child_Table_Parent_Field_Name,$MetaColumnNames)) {
                $ChildSettingMap = returntablefield("form_formflow",'id',$Relative_Child_Table,'Setting')['Setting'];
                $ChildSettingMap = unserialize(base64_decode($ChildSettingMap));
                $ChildFormId                = returntablefield("form_formflow",'id',$Relative_Child_Table,'FormId')['FormId'];
                $ChildTableName             = returntablefield("form_formname",'id',$ChildFormId,'TableName')['TableName'];
                $ChildMetaColumnNames       = GLOBAL_MetaColumnNames($ChildTableName);
                if($Relative_Child_Table_Field_Name!="" && in_array($Relative_Child_Table_Field_Name, $ChildMetaColumnNames) &&strpos($ChildSettingMap['Actions_In_List_Row'],'Edit')!==false) {
                    //Get All Fields
                    $readonlyIdArray            = explode(',',ForSqlInjection($_POST['readonlyIdArray']));
                    $db->BeginTrans();
                    $MultiSql                   = [];
                    $sql                        = "delete from $ChildTableName where $Relative_Child_Table_Parent_Field_Name = '".$RecordOriginal->fields[$Relative_Child_Table_Parent_Field_Name]."' and id not in ('".join("','",$readonlyIdArray)."');";
                    $db->Execute($sql);
                    $MultiSql[]                 = $sql;
                    $sql                        = "select * from form_formfield where FormId='$ChildFormId' and IsEnable='1' order by SortNumber asc, id asc";
                    $rs                         = $db->Execute($sql);
                    $ChildAllFieldsFromTable    = $rs->GetArray();
                    $ChildAllFieldsMap          = [];
                    $ChildItemCounter           = $_POST['ChildItemCounter'];
                    for($X=0;$X<$ChildItemCounter;$X++)                    {
                        $ChildElement = [];
                        foreach($ChildAllFieldsFromTable as $Item)  {
                            $ChildFieldName = $Item['FieldName'];
                            switch($Item['ShowType']) {
                                case 'Hidden:Createtime':
                                    $ChildElement[$ChildFieldName] = date('Y-m-d H:i:s');
                                    break;
                                case 'Hidden:CurrentUserIdAdd':
                                case 'Hidden:CurrentUserIdAddEdit':
                                    $ChildElement[$ChildFieldName] = $GLOBAL_USER->USER_ID;
                                    break;
                                case 'Hidden:CurrentStudentCodeAdd':
                                case 'Hidden:CurrentStudentCodeAddEdit':
                                    if($GLOBAL_USER->学号=="") $GLOBAL_USER->学号 = $GLOBAL_USER->USER_ID;
                                    $ChildElement[$ChildFieldName] = $GLOBAL_USER->学号;
                                    break;
                                default:
                                    $ChildElement[$ChildFieldName] = ForSqlInjection($_POST['ChildTable____'.$X.'____'.$ChildFieldName]);
                                    break;
                            }
                        }
                        $deleteChildTableItemArray = explode(',',$_POST['deleteChildTableItemArray']);
                        if(!in_array($X, $deleteChildTableItemArray)) {
                            $ChildElement[$Relative_Child_Table_Parent_Field_Name] = $RecordOriginal->fields[$Relative_Child_Table_Parent_Field_Name];
                            $ChildKeys      = array_keys($ChildElement);
                            $ChildValues    = array_values($ChildElement);
                            $sql            = "insert into $ChildTableName (".join(',',$ChildKeys).") values('".join("','",$ChildValues)."');";
                            $db->Execute($sql);
                            $MultiSql[]     = $sql;
                        }
                    }
                    $db->CommitTrans();
                    $RS['MultiSql'] = $MultiSql;
                }
            }

            //functionNameIndividual
            $functionNameIndividual = "plugin_".$TableName."_".$Step."_edit_default_data_after_submit";
            if(function_exists($functionNameIndividual))  {
                $functionNameIndividual($id);
            }
            //SystemLogRecord
            if(in_array($SettingMap['OperationLogGrade'],["EditAndDeleteOperation","AddEditAndDeleteOperation","AllOperation"]))  {
                $sql            = "select * from $TableName where ".$MetaColumnNames[0]." = '$id'";
                $Record         = $db->Execute($sql);
                SystemLogRecord("edit_default_data", json_encode($RecordOriginal->fields), json_encode($Record->fields));
            }
            print_R(EncryptApiData($RS, $GLOBAL_USER));
            exit;
        }
        else {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['msg'] = __("sql execution failed");
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_GET'] = $_GET;
            if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_POST'] = $_POST;
            print_R(EncryptApiData($RS, $GLOBAL_USER));
            exit;
        }
    }
    else {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("No POST Infor");
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_GET'] = $_GET;
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_POST'] = $_POST;
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['IsExecutionSQL'] = $IsExecutionSQL;
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['IsExecutionSQLChildTable'] = $IsExecutionSQLChildTable;
        print_R(EncryptApiData($RS, $GLOBAL_USER));
        exit;
    }
}

if( $_GET['action']=="edit_default_configsetting_data" && $SettingMap['Init_Action_Value']=="edit_default_configsetting" && $FlowId!="")  {
    //functionNameIndividual
    $functionNameIndividual = "plugin_".$TableName."_".$Step."_edit_default_configsetting_data";
    if(function_exists($functionNameIndividual))  {
        $functionNameIndividual($NewId);
    }
    //$id = DecryptID($_GET['id']);
    $ConfigSetting = base64_encode(serialize($_POST));
    $sql = "update form_formflow set ConfigSetting='$ConfigSetting' where id='$FlowId'";
    $db->Execute($sql);
    $RS = [];
    $RS['status'] = "OK";
    $RS['msg'] = $SettingMap['Tip_When_Edit_Success'];
    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_GET'] = $_GET;
    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['_POST'] = $_POST;
    print json_encode($RS);
    exit;
}

if( ( ($_GET['action']=="edit_default"&&in_array('Edit',$Actions_In_List_Row_Array))  ) && $_GET['id']!="")  {
    if($TableName=="data_user" && $SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']=="email") {
        $EMAIL  = $GLOBAL_USER->email;
        $id     = returntablefield("data_user","EMAIL",$EMAIL,"id")["id"];
    }
    else if($TableName=="data_user" && $SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']=="USER_ID") {
        $USER_ID  = $GLOBAL_USER->USER_ID;
        $id     = returntablefield($TableName,"USER_ID",$USER_ID,"id")["id"];
    }
    else if($TableName=="data_xiaoyou_member" && $SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']=="USER_ID") {
        $USER_ID  = $GLOBAL_USER->USER_ID;
        $id     = returntablefield($TableName,"学生学号",$USER_ID,"id")["id"];
    }
    else if($TableName=="data_student" && $SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']=="学号") {
        $学号    = $GLOBAL_USER->学号;
        $id     = returntablefield($TableName,"学号",$学号,"id")["id"];
    }
    else if($SettingMap['Init_Action_Value']=="edit_default" && $SettingMap['Init_Action_FilterValue']!="") {
        $id     = intval($SettingMap['Init_Action_FilterValue']);
    }
    else {
        $id     = intval(DecryptID($_GET['id']));
    }
    if($id==0)   {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("Error Id Value");
        $RS['_GET'] = $_GET;
        $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }

    //functionNameIndividual
    $functionNameIndividual = "plugin_".$TableName."_".$Step."_edit_default";
    if(function_exists($functionNameIndividual))  {
        $functionNameIndividual($id);
    }

    //Get Row Data
    $sql    = "select * from `$TableName` where id = '$id'";
    $rsf    = $db->Execute($sql);
    $data   = $rsf->fields;

    foreach($AllFieldsFromTable as $Item)  {
        $CurrentFieldType = $AllShowTypesArray[$AllFieldsMap[$Item['FieldName']]['ShowType']]['EDIT'];
        if(array_key_exists($Item['FieldName'], $defaultValuesEdit)) {
            switch($CurrentFieldType) {
                case 'avatar':
                    $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'avatar',$data[$Item['FieldName']]);
                    break;
                case 'images':
                case 'images2':
                    $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'images',$data[$Item['FieldName']]);
                    break;
                case 'files':
                case 'files2':
                    $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'files',$data[$Item['FieldName']]);
                    break;
                case 'file':
                    $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'file',$data[$Item['FieldName']]);
                    break;
                case 'xlsx':
                    $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'xlsx',$data[$Item['FieldName']]);
                    break;
                case 'password':
                    $data[$Item['FieldName']] = "******";
                    break;
                case 'ProvinceAndCity':
                    //行政区 三级
                    global $微信小程序_省市区_子选项;
                    $sql	= "select * from edu_xingzhengdaima where length(代码)='12' order by 代码";
                    $rs		= $db->Execute($sql);
                    $rs_a	= $rs->GetArray();
                    $微信小程序_省市区_子选项     = [];
                    $Element           = [];
                    $默认行政代码         = $data[$Item['FieldName']];
                    if($默认行政代码=="") {
                        $默认行政代码 = "110000000000";
                    }
                    for($R=0;$R<sizeof($rs_a);$R++)							{
                        $行政区	= $rs_a[$R]['行政区'];
                        $代码	= $rs_a[$R]['代码'];
                        if(substr($代码,2,10)=='0000000000')			{
                            $省 			= $行政区;
                            $市             = '';
                            //$微信小程序_省市区_子选项[$省] 	= $省
                            if($省=='台湾省' || $省=='香港特别行政区' || $省=='澳门特别行政区')
                                $微信小程序_省市区_子选项[$省][$省][] 	= array("id"=>(STRING)$代码,"name"=>(STRING)$省);
                            if(substr($代码,0,2)==substr($默认行政代码,0,2))		{
                                $微信小程序_用户选择名称['省'] = $行政区;
                            }
                            //处理北京市-北京市-东城区这样的情况
                            if(substr($代码,4,8)=='00000000')			{
                                $市 = str_replace($省,'',$行政区);
                                if($市=="") $市 = $行政区;
                                if(substr($代码,0,4)==substr($默认行政代码,0,4))		{
                                    $微信小程序_用户选择名称['市'] = $市;
                                }
                            }
                        }
                        elseif(substr($代码,4,8)=='00000000')			{
                            $市 = str_replace($省,'',$行政区);
                            if($市=="") $市 = $行政区;
                            if(substr($代码,0,4)==substr($默认行政代码,0,4))		{
                                $微信小程序_用户选择名称['市'] = $市;
                            }
                        }
                        else		{
                            if($市=='') $市 = $省;
                            $区名称 = str_replace($市,'',$行政区);
                            $微信小程序_省市区_子选项[$省][$市][] 	= array("id"=>(STRING)$代码,"name"=>(STRING)$区名称);
                            if(substr($代码,0,6)==substr($默认行政代码,0,6))		{
                                $微信小程序_用户选择名称['区'] = $区名称;
                            }
                        }
                    }
                    $微信小程序_用户选择索引			= array();
                    $微信小程序_左右下拉数组			= array();
                    $左侧数组 						   = array_keys($微信小程序_省市区_子选项);;
                    $微信小程序_左右下拉数组[0] 		= $左侧数组;
                    if(is_array($微信小程序_省市区_子选项[$微信小程序_用户选择名称['省']])) {
                        $微信小程序_左右下拉数组[1] 	= array_keys($微信小程序_省市区_子选项[$微信小程序_用户选择名称['省']]);
                    }
                    else {
                        $微信小程序_左右下拉数组[1]     = [];
                    }
                    if(is_array($微信小程序_省市区_子选项[$微信小程序_用户选择名称['省']][$微信小程序_用户选择名称['市']])) {
                        $微信小程序_左右下拉数组[2] 	= idname_array_get_namelist($微信小程序_省市区_子选项[$微信小程序_用户选择名称['省']][$微信小程序_用户选择名称['市']]);
                    }
                    else {
                        $微信小程序_左右下拉数组[2]     = [];
                    }
                    //把省市区中的值转化为索引.
                    $ARRY_FLIP 						  = array_flip($微信小程序_左右下拉数组[0]);
                    $微信小程序_用户选择索引['省']	    = $ARRY_FLIP[$微信小程序_用户选择名称['省']];
                    $ARRY_FLIP 						  = array_flip($微信小程序_左右下拉数组[1]);
                    $微信小程序_用户选择索引['市']	    = $ARRY_FLIP[$微信小程序_用户选择名称['市']];
                    $ARRY_FLIP 						  = array_flip($微信小程序_左右下拉数组[2]);
                    $微信小程序_用户选择索引['区']	    = $ARRY_FLIP[$微信小程序_用户选择名称['区']];
                    $微信小程序_用户选择索引	        = array_values($微信小程序_用户选择索引);

                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_用户选择索引']      = $微信小程序_用户选择索引;
                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_用户选择名称']      = $微信小程序_用户选择名称;
                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_省选项']            = array_keys($微信小程序_省市区_子选项);
                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_省市区_子选项']     = $微信小程序_省市区_子选项;
                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_左右下拉数组']      = $微信小程序_左右下拉数组;
                    $data["ProvinceAndCity_".$Item['id']]['行政区代码']                  = $默认行政代码;
                    $data["ProvinceAndCity_".$Item['id']]['FieldName']                  = $Item['FieldName'];
                    break;
                case 'ProvinceAndCityOneLine':
                    //行政区 三级
                    global $微信小程序_省市区_子选项;
                    $sql	= "select * from edu_xingzhengdaima where length(代码)='12' order by 代码";
                    $rs		= $db->Execute($sql);
                    $rs_a	= $rs->GetArray();
                    $微信小程序_省市区_子选项     = [];
                    $Element           = [];
                    $默认省市区         = $data[$Item['FieldName']];
                    if($默认省市区=="") {
                        $默认省市区 = "北京市-北京市-东城区";
                    }
                    $默认省市区Array    = explode('-',$默认省市区);
                    for($R=0;$R<sizeof($rs_a);$R++)							{
                        $行政区	= $rs_a[$R]['行政区'];
                        $代码	= $rs_a[$R]['代码'];
                        if(substr($代码,2,10)=='0000000000')			{
                            $省 			= $行政区;
                            $市             = '';
                            //$微信小程序_省市区_子选项[$省] 	= $省
                            if($省=='台湾省' || $省=='香港特别行政区' || $省=='澳门特别行政区')
                                $微信小程序_省市区_子选项[$省][$省][] 	= array("id"=>(STRING)$代码,"name"=>(STRING)$省);
                            if($省==$默认省市区Array[0])		{
                                $微信小程序_用户选择名称['省'] = $行政区;
                            }
                            //处理北京市-北京市-东城区这样的情况
                            if(substr($代码,4,8)=='00000000')			{
                                $市 = str_replace($省,'',$行政区);
                                if($市=="") $市 = $行政区;
                                if($市==$默认省市区Array[1])		{
                                    $微信小程序_用户选择名称['市'] = $市;
                                }
                            }
                        }
                        elseif(substr($代码,4,8)=='00000000')			{
                            $市 = str_replace($省,'',$行政区);
                            if($市=="") $市 = $行政区;
                            if($市==$默认省市区Array[1])		{
                                $微信小程序_用户选择名称['市'] = $市;
                            }
                        }
                        else		{
                            if($市=='') $市 = $省;
                            $区名称 = str_replace($市,'',$行政区);
                            $微信小程序_省市区_子选项[$省][$市][] 	= array("id"=>(STRING)$代码,"name"=>(STRING)$区名称);
                            if($区名称==$默认省市区Array[2])		{
                                $微信小程序_用户选择名称['区'] = $区名称;
                            }
                        }
                    }
                    $微信小程序_用户选择索引			= array();
                    $微信小程序_左右下拉数组			= array();
                    $左侧数组 						   = array_keys($微信小程序_省市区_子选项);;
                    $微信小程序_左右下拉数组[0] 		= $左侧数组;
                    if(is_array($微信小程序_省市区_子选项[$微信小程序_用户选择名称['省']])) {
                        $微信小程序_左右下拉数组[1] 	= array_keys($微信小程序_省市区_子选项[$微信小程序_用户选择名称['省']]);
                    }
                    else {
                        $微信小程序_左右下拉数组[1]     = [];
                    }
                    if(is_array($微信小程序_省市区_子选项[$微信小程序_用户选择名称['省']][$微信小程序_用户选择名称['市']])) {
                        $微信小程序_左右下拉数组[2] 	= idname_array_get_namelist($微信小程序_省市区_子选项[$微信小程序_用户选择名称['省']][$微信小程序_用户选择名称['市']]);
                    }
                    else {
                        $微信小程序_左右下拉数组[2]     = [];
                    }
                    //把省市区中的值转化为索引.
                    $ARRY_FLIP 						  = array_flip($微信小程序_左右下拉数组[0]);
                    $微信小程序_用户选择索引['省']	    = $ARRY_FLIP[$微信小程序_用户选择名称['省']];
                    $ARRY_FLIP 						  = array_flip($微信小程序_左右下拉数组[1]);
                    $微信小程序_用户选择索引['市']	    = $ARRY_FLIP[$微信小程序_用户选择名称['市']];
                    $ARRY_FLIP 						  = array_flip($微信小程序_左右下拉数组[2]);
                    $微信小程序_用户选择索引['区']	    = $ARRY_FLIP[$微信小程序_用户选择名称['区']];
                    $微信小程序_用户选择索引	        = array_values($微信小程序_用户选择索引);

                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_用户选择索引']      = $微信小程序_用户选择索引;
                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_用户选择名称']      = $微信小程序_用户选择名称;
                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_省选项']            = array_keys($微信小程序_省市区_子选项);
                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_省市区_子选项']     = $微信小程序_省市区_子选项;
                    $data["ProvinceAndCity_".$Item['id']]['微信小程序_左右下拉数组']      = $微信小程序_左右下拉数组;
                    $data["ProvinceAndCity_".$Item['id']]['FieldName']                  = $Item['FieldName'];
                    break;
            }
        }
        //Decrypt Field Value
        $FieldName                      = $Item['FieldName'];
        $SettingTempMap                 = $AllFieldsMap[$FieldName]['Setting'];
        $DataFieldEncryptMethod         = $SettingTempMap['DataFieldEncryptMethod'];
        $DataFieldEncryptKey            = $SettingTempMap['DataFieldEncryptKey'];
        if($DataFieldEncryptMethod==1&&$DataFieldEncryptKey!="") {
            $data[$FieldName]           = DecryptIDStorage($data[$FieldName], $DataFieldEncryptKey);
        }
    }

    $RS = [];
    $RS['status'] = "OK";
    $RS['data'] = $data;
    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    if($_GET['IsGetStructureFromEditDefault']==1)  {
        $edit_default['allFields']      = $allFieldsEdit;
        $edit_default['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
        $edit_default['defaultValues']  = $defaultValuesEdit;
        $edit_default['dialogContentHeight']  = "90%";
        $edit_default['submitaction']   = "edit_default_data";
        $edit_default['submittext']     = __("Submit");
        $edit_default['componentsize']  = "small";
        $edit_default['canceltext']     = "";
        $edit_default['titletext']      = "";
        $edit_default['titlememo']      = "";
        $edit_default['tablewidth']     = 650;
    }
    //Relative Child Table Support
    $Relative_Child_Table                   = $SettingMap['Relative_Child_Table'];
    $Relative_Child_Table_Field_Name        = $SettingMap['Relative_Child_Table_Field_Name'];
    $Relative_Child_Table_Parent_Field_Name = $SettingMap['Relative_Child_Table_Parent_Field_Name'];
    if($Relative_Child_Table>0 && $Relative_Child_Table_Parent_Field_Name!="" && in_array($Relative_Child_Table_Parent_Field_Name,$MetaColumnNames)) {
        $ChildSettingMap = returntablefield("form_formflow",'id',$Relative_Child_Table,'Setting')['Setting'];
        $ChildSettingMap = unserialize(base64_decode($ChildSettingMap));
        $ChildFormId                = returntablefield("form_formflow",'id',$Relative_Child_Table,'FormId')['FormId'];
        $ChildTableName             = returntablefield("form_formname",'id',$ChildFormId,'TableName')['TableName'];
        $ChildMetaColumnNames       = GLOBAL_MetaColumnNames($ChildTableName);
        if($Relative_Child_Table_Field_Name!="" && in_array($Relative_Child_Table_Field_Name, $ChildMetaColumnNames) ) {
            //Get All Fields
            $sql        = "select * from $ChildTableName where $Relative_Child_Table_Parent_Field_Name = '".$data[$Relative_Child_Table_Parent_Field_Name]."';";
            $rs         = $db->Execute($sql);
            $rs_a       = $rs->GetArray();
            $readonlyIdArray            = [];
            $deleteChildTableItemArray  = [];
            $RS['childtable']['sql']    = $sql;
            $RS['childtable']['data']   = $rs_a;
            $RS['childtable']['ChildItemCounter'] = sizeof($rs_a);
            for($X=0;$X<sizeof($rs_a);$X++) {
                $Line = $rs_a[$X];
                foreach($Line AS $LineKey=>$LineValue) {
                    $data['ChildTable____'.$X.'____'.$LineKey] = $LineValue;
                }
                //LimitEditAndDelete
                if($ChildSettingMap['LimitEditAndDelete_Edit_Field_One']!="" && $ChildSettingMap['LimitEditAndDelete_Edit_Field_One']!="None" && in_array($ChildSettingMap['LimitEditAndDelete_Edit_Field_One'], $ChildMetaColumnNames)) {
                    $LimitEditAndDelete_Edit_Value_One_Array = explode(',',$ChildSettingMap['LimitEditAndDelete_Edit_Value_One']);
                    if(in_array($Line[$ChildSettingMap['LimitEditAndDelete_Edit_Field_One']],$LimitEditAndDelete_Edit_Value_One_Array)) {
                        $readonlyIdArray[] = $Line['id'];
                        $deleteChildTableItemArray[] = $X;
                    }
                }
            }
            $RS['childtable']['readonlyIdArray']                = $readonlyIdArray;
            $RS['childtable']['deleteChildTableItemArray']      = $deleteChildTableItemArray;
            $RS['data']  = $data;
        }
    }
    $RS['edit_default'] = $edit_default;

    //Filter Data For Readonly Edit
    //编辑只读的时候,把用户名转为用户姓名
    foreach($allFieldsEdit as $ModeName=>$allFieldItemTemp) {
        $CounterTemp = 0;
        $allFieldItem = $allFieldsView[$ModeName];
        foreach($allFieldItem as $ITEM) {
            if($allFieldItemTemp[$CounterTemp]['type']=='readonly')  {
                $FieldName              = $ITEM['name'];
                $CurrentFieldTypeArray  = $ITEM['FieldTypeArray'];
                switch($CurrentFieldTypeArray[0])   {
                    case 'autocomplete':
                        $FieldName              = $ITEM['code'];
                    case 'radiogroup':
                    case 'radiogroupcolor':
                    case 'tablefilter':
                    case 'tablefiltercolor':
                        $TableNameTemp      = $CurrentFieldTypeArray[1];
                        $KeyField           = $CurrentFieldTypeArray[2];
                        $ValueField         = $CurrentFieldTypeArray[3];
                        $DefaultValue       = $CurrentFieldTypeArray[4];
                        $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                        $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                        $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);
                        if($WhereField!="" && $WhereValue!="" && $MetaColumnNamesTemp[$KeyField]!="" && $RS['data'][$FieldName]!="") {
                            $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' and `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($RS['data'][$FieldName])."' ;";
                            $rs = $db->Execute($sql) or print($sql);
                            $RS['data'][$FieldName] = $rs->fields['label'];
                        }
                        elseif($MetaColumnNamesTemp[$KeyField]!="" && $RS['data'][$FieldName]!="")    {
                            $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($RS['data'][$FieldName])."' ;";
                            $rs = $db->Execute($sql) or print($sql);
                            $RS['data'][$FieldName] = $rs->fields['label'];
                        }
                        break;
                    case 'autocompletemulti':
                        //print_R($CurrentFieldTypeArray);
                        $TableNameTemp      = $CurrentFieldTypeArray[1];
                        $KeyField           = $CurrentFieldTypeArray[2];
                        $ValueField         = $CurrentFieldTypeArray[3];
                        $DefaultValue       = $CurrentFieldTypeArray[4];
                        $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                        $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                        $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);
                        $MultiValueArray        = explode(',',$RS['data'][$FieldName]);
                        $MultiValueRS           = [];
                        foreach($MultiValueArray as $MultiValue) {
                            if($WhereField!="" && $WhereValue!="" && $MetaColumnNamesTemp[$KeyField]!="" && $MultiValue!="") {
                                $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' and `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($MultiValue)."' ;";
                                $rs = $db->Execute($sql) or print($sql);
                                $MultiValueRS[] = $rs->fields['label'];
                            }
                            elseif($MetaColumnNamesTemp[$KeyField]!="" && $MultiValue!="")    {
                                $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($MultiValue)."' ;";
                                $rs = $db->Execute($sql) or print($sql);
                                $MultiValueRS[] = $rs->fields['label'];
                            }
                        }
                        $RS['data'][$FieldName] = join(',',$MultiValueRS);
                        break;
                    case 'password':
                        $RS['data'][$FieldName] = "******";
                        break;
                    default:
                        break;
                }
            }
            $CounterTemp ++;
        }
    }

    print json_encode($RS);
    exit;
}

if( $_GET['action']=="edit_default_1" && $_GET['id']!="" )  {
  $functionNameIndividual = "plugin_".$TableName."_".$Step."_edit_default_1";
  if(function_exists($functionNameIndividual))  {
      $RS = $functionNameIndividual($_GET['id']);
  }
  exit;
}
if( $_GET['action']=="edit_default_1_data" && $_GET['id']!="" )  {
  $functionNameIndividual = "plugin_".$TableName."_".$Step."_edit_default_1_data";
  if(function_exists($functionNameIndividual))  {
      $RS = $functionNameIndividual($_GET['id']);
  }
  exit;
}

if( $_GET['action']=="edit_default_2" && $_GET['id']!="" )  {
  $functionNameIndividual = "plugin_".$TableName."_".$Step."_edit_default_2";
  if(function_exists($functionNameIndividual))  {
      $RS = $functionNameIndividual($_GET['id']);
  }
  exit;
}
if( $_GET['action']=="edit_default_2_data" && $_GET['id']!="" )  {
  $functionNameIndividual = "plugin_".$TableName."_".$Step."_edit_default_2_data";
  if(function_exists($functionNameIndividual))  {
      $RS = $functionNameIndividual($_GET['id']);
  }
  exit;
}

if( ( ($_GET['action']=="view_default"&&in_array('View',$Actions_In_List_Row_Array))  ) && $_GET['id']!="")  {
    $id     = intval(DecryptID($_GET['id']));
    if($id==0)   {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("Error Id Value");
        $RS['_GET'] = $_GET;
        $RS['_POST'] = $_POST;
        print_R(EncryptApiData($RS, $GLOBAL_USER));
        exit;
    }

    //functionNameIndividual
    $functionNameIndividual = "plugin_".$TableName."_".$Step."_view_default";
    if(function_exists($functionNameIndividual))  {
        $functionNameIndividual($id);
    }

    //Read Counter ++
    if(in_array("浏览次数", $MetaColumnNames))  {
        $sql    = "update `$TableName` set 浏览次数=浏览次数+1 where id = '$id'";
        $db->Execute($sql);
    }
    else if(in_array("阅读次数", $MetaColumnNames))  {
        $sql    = "update `$TableName` set 阅读次数=阅读次数+1 where id = '$id'";
        $db->Execute($sql);
    }
    $sql    = "select * from `$TableName` where id = '$id'";
    $rsf    = $db->Execute($sql);
    $data   = $rsf->fields;

    foreach($AllFieldsFromTable as $Item)  {
        $CurrentFieldType = $AllShowTypesArray[$AllFieldsMap[$Item['FieldName']]['ShowType']]['EDIT'];
        switch($CurrentFieldType) {
            case 'avatar':
                $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'avatar',$data[$Item['FieldName']]);
                break;
            case 'images':
            case 'images2':
                $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'images',$data[$Item['FieldName']]);
                break;
            case 'files':
            case 'files2':
                $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'files',$data[$Item['FieldName']]);
                break;
            case 'file':
                $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'file',$data[$Item['FieldName']]);
                break;
            case 'xlsx':
                $data[$Item['FieldName']] = AttachFieldValueToUrl($TableName,$id,$Item['FieldName'],'xlsx',$data[$Item['FieldName']]);
                break;
        }
        //Decrypt Field Value
        $FieldName                      = $Item['FieldName'];
        $SettingTempMap                 = $AllFieldsMap[$FieldName]['Setting'];
        $DataFieldEncryptMethod         = $SettingTempMap['DataFieldEncryptMethod'];
        $DataFieldEncryptKey            = $SettingTempMap['DataFieldEncryptKey'];
        if($DataFieldEncryptMethod==1&&$DataFieldEncryptKey!="") {
            $data[$FieldName]           = DecryptIDStorage($data[$FieldName], $DataFieldEncryptKey);
        }
    }

    $RS = [];
    $RS['status'] = "OK";
    $RS['data'] = $data;
    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    $view_default = [];
    if($_GET['IsGetStructureFromEditDefault']==1)  {
        $view_default['allFields']      = $allFieldsView;
        $view_default['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
        $view_default['defaultValues']  = $defaultValuesEdit;
        $view_default['dialogContentHeight']  = "90%";
        $view_default['componentsize']  = "small";
        $view_default['canceltext']     = "";
        $view_default['titletext']      = "";
        $view_default['titlememo']      = "";
        $view_default['tablewidth']     = 650;
    }
    //$RS['_SERVER'] = $_SERVER;
    $RS['view_default'] = $view_default;

    //Filter Data For View
    foreach($allFieldsView as $ModeName=>$allFieldItem) {
        foreach($allFieldItem as $ITEM) {
            $FieldName              = $ITEM['name'];
            $CurrentFieldTypeArray  = $ITEM['FieldTypeArray'];
            switch($CurrentFieldTypeArray[0])   {
                case 'autocomplete':
                    $FieldName              = $ITEM['code'];
                case 'radiogroup':
                case 'radiogroupcolor':
                case 'tablefilter':
                case 'tablefiltercolor':
                    $TableNameTemp      = $CurrentFieldTypeArray[1];
                    $KeyField           = $CurrentFieldTypeArray[2];
                    $ValueField         = $CurrentFieldTypeArray[3];
                    $DefaultValue       = $CurrentFieldTypeArray[4];
                    $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                    $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                    $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);
                    if($WhereField!="" && $WhereValue!="" && $MetaColumnNamesTemp[$KeyField]!="" && $RS['data'][$FieldName]!="") {
                        $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' and `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($RS['data'][$FieldName])."' ;";
                        $rs = $db->Execute($sql) or print($sql);
                        $RS['data'][$FieldName] = $rs->fields['label'];
                    }
                    elseif($MetaColumnNamesTemp[$KeyField]!="" && $RS['data'][$FieldName]!="")    {
                        $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($RS['data'][$FieldName])."' ;";
                        $rs = $db->Execute($sql) or print($sql);
                        $RS['data'][$FieldName] = $rs->fields['label'];
                    }
                    break;
                case 'autocompletemulti':
                    //print_R($CurrentFieldTypeArray);
                    $TableNameTemp      = $CurrentFieldTypeArray[1];
                    $KeyField           = $CurrentFieldTypeArray[2];
                    $ValueField         = $CurrentFieldTypeArray[3];
                    $DefaultValue       = $CurrentFieldTypeArray[4];
                    $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                    $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                    $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);
                    $MultiValueArray        = explode(',',$RS['data'][$FieldName]);
                    $MultiValueRS           = [];
                    foreach($MultiValueArray as $MultiValue) {
                        if($WhereField!="" && $WhereValue!="" && $MetaColumnNamesTemp[$KeyField]!="" && $MultiValue!="") {
                            $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' and `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($MultiValue)."' ;";
                            $rs = $db->Execute($sql) or print($sql);
                            $MultiValueRS[] = $rs->fields['label'];
                        }
                        elseif($MetaColumnNamesTemp[$KeyField]!="" && $MultiValue!="")    {
                            $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($MultiValue)."' ;";
                            $rs = $db->Execute($sql) or print($sql);
                            $MultiValueRS[] = $rs->fields['label'];
                        }
                    }
                    $RS['data'][$FieldName] = join(',',$MultiValueRS);
                    break;
                case 'password':
                    $RS['data'][$FieldName] = "******";
                    break;
                default:
                    break;
            }
        }
    }

    //Rerest the layout in View Model
    $LayoutWidth = [];
    foreach($allFieldsView as $ModeName=>$allFieldItem) {
        if(is_array($allFieldItem)) {
            for($i=0;$i<sizeof($allFieldItem);$i+=2)        {
                $FieldName              = $allFieldItem[$i]['name'];
                $Width                  = $allFieldItem[$i]['rules']['sm'];
                if($allFieldItem[$i]['rules']['sm']==12) {
                    $LayoutWidth[] = [$allFieldItem[$i]['name']];
                    $i -= 1;
                }
                else {
                    $LayoutWidth[] = [$allFieldItem[$i]['name'],$allFieldItem[$i+1]['name']];
                }
            }
        }
    }
    $RS['LayoutWidth']          = $LayoutWidth;

    //Convert data to Table
    $ApprovalNodeFieldsArray    = explode(',',$SettingMap['ApprovalNodeFields']);
    $ApprovalNodeFieldsHidden   = [];
    $ApprovalNodeFieldsStatus   = [];
    foreach($ApprovalNodeFieldsArray as $TempField) {
        $ApprovalNodeFieldsHidden[] = $TempField."审核状态";
        //$ApprovalNodeFieldsHidden[] = $TempField."申请时间";
        //$ApprovalNodeFieldsHidden[] = $TempField."申请人";
        $ApprovalNodeFieldsHidden[] = $TempField."审核时间";
        $ApprovalNodeFieldsHidden[] = $TempField."审核人";
        $ApprovalNodeFieldsHidden[] = $TempField."审核意见";
        $ApprovalNodeFieldsStatus[$TempField."审核状态"] = $TempField."审核状态";
    }
    $ApprovalNodeFieldsStatus = array_keys($ApprovalNodeFieldsStatus);
    $NewTableRowData    = [];
    $NewTableRowItem    = [];
    $FieldNameArray     = $allFieldsView['Default'];
    for($X=0;$X<sizeof($FieldNameArray);$X=$X+2)        {
        if($FieldNameArray[$X]['rules']['sm']==12) {
            $FieldName1     = $FieldNameArray[$X]['name'];
            if($FieldNameArray[$X]['type']=="autocomplete" && $FieldNameArray[$X]['code']!="") {
                $FieldName1 = $FieldNameArray[$X]['code'];
            }
            $RowData = [];
            if(!in_array($FieldName1,$ApprovalNodeFieldsHidden) && $FieldName1!="") {
                $RowData[0]['Name']     = $FieldName1;
                $RowData[0]['Value']    = $RS['data'][$FieldName1];
                $RowData[0]['FieldArray']   = $FieldNameArray[$X];
            }
            $NewTableRowItem[] = [$RowData];
            $X -= 1;
        }
        else {
            $FieldName1 = $FieldNameArray[$X]['name'];
            if($FieldNameArray[$X]['type']=="autocomplete" && $FieldNameArray[$X]['code']!="") {
                $FieldName1 = $FieldNameArray[$X]['code'];
            }
            $FieldName2 = $FieldNameArray[$X+1]['name'];
            if($FieldNameArray[$X+1]['type']=="autocomplete" && $FieldNameArray[$X+1]['code']!="") {
                $FieldName2 = $FieldNameArray[$X+1]['code'];
            }
            $RowData = [];
            $RowData1 = [];
            $RowData2 = [];
            if(!in_array($FieldName1,$ApprovalNodeFieldsHidden) && $FieldName1!="") {
                $RowData1['Name']     = $FieldName1;
                $RowData1['Value']    = $RS['data'][$FieldName1];
                $RowData1['FieldArray']     = $FieldNameArray[$X];
                $RowData[0]                 = $RowData1;
                $NewTableRowItem[]          = [$RowData1];
            }
            if(!in_array($FieldName2,$ApprovalNodeFieldsHidden) && $FieldName2!="") {
                $RowData2['Name']     = $FieldName2;
                $RowData2['Value']    = $RS['data'][$FieldName2];
                $RowData2['FieldArray']     = $FieldNameArray[$X+1];
                $RowData[1]                 = $RowData2;
                $NewTableRowItem[]          = [$RowData2];
            }

        }
        if(sizeof($RowData)>0) {
            $NewTableRowData[] = $RowData;
        }
    }
    if($_GET['isMobileData']=="true") {
        $RS['newTableRowData']          = $NewTableRowItem;
    }
    else {
        $RS['newTableRowData']          = $NewTableRowData;
    }
    $RS['_GET']          = $_GET;
    $RS['ApprovalNodes']['Nodes']   = $ApprovalNodeFieldsArray[0]!=""?$ApprovalNodeFieldsArray:[];
    $RS['ApprovalNodes']['Fields']  = ['审核结点','审核状态','审核时间','审核人','审核意见'];

    $RS['print']['text']            = __("Print");

    //Relative Child Table Support
    $Relative_Child_Table                   = $SettingMap['Relative_Child_Table'];
    $Relative_Child_Table_Field_Name        = $SettingMap['Relative_Child_Table_Field_Name'];
    $Relative_Child_Table_Parent_Field_Name = $SettingMap['Relative_Child_Table_Parent_Field_Name'];
    if($Relative_Child_Table>0 && $Relative_Child_Table_Parent_Field_Name!="" && in_array($Relative_Child_Table_Parent_Field_Name,$MetaColumnNames)) {
        $ChildSettingMap = returntablefield("form_formflow",'id',$Relative_Child_Table,'Setting')['Setting'];
        $ChildSettingMap = unserialize(base64_decode($ChildSettingMap));
        $ChildFormId                = returntablefield("form_formflow",'id',$Relative_Child_Table,'FormId')['FormId'];
        $ChildTableName             = returntablefield("form_formname",'id',$ChildFormId,'TableName')['TableName'];
        $ChildMetaColumnNames       = GLOBAL_MetaColumnNames($ChildTableName);
        if($Relative_Child_Table_Field_Name!="" && in_array($Relative_Child_Table_Field_Name, $ChildMetaColumnNames) ) {
            //Get All Fields
            $sql        = "select * from $ChildTableName where $Relative_Child_Table_Parent_Field_Name = '".$data[$Relative_Child_Table_Parent_Field_Name]."';";
            $rs         = $db->Execute($sql);
            $rs_a       = $rs->GetArray();
            $RS['childtable']['sql']    = $sql;
            $RS['childtable']['data']   = $rs_a;
            $RS['childtable']['ChildItemCounter'] = sizeof($rs_a);

            //Get All Fields
            $sql                        = "select * from form_formfield where FormId='$ChildFormId' and IsEnable='1' order by SortNumber asc, id asc";
            $rs                         = $db->Execute($sql);
            $ChildAllFieldsFromTable    = $rs->GetArray();
            $allFieldsView   = getAllFields($ChildAllFieldsFromTable, $AllShowTypesArray, 'VIEW', true, $ChildSettingMap);
            foreach($allFieldsView as $ModeName=>$allFieldItem) {
                $allFieldItemIndex = 0;
                foreach($allFieldItem as $ITEM) {
                    //if(strpos($ChildSettingMap['Actions_In_List_Row'],'Edit')===false) {
                        //$allFieldsView[$ModeName][$allFieldItemIndex]['rules']['disabled'] = true;
                    //}
                    //$allFieldItemIndex ++;
                }
            }
            $RS['childtable']['allFields']  = $allFieldsView;

        }
    }

    if(in_array($SettingMap['MobileEndShowType'],["NewsTemplate1","ZiXun","Activity","Schoolmate","NotificationTemplate1","NotificationTemplate2"]))           {
        //News Template
        $RS['MobileEnd']['MobileEndNewsTitle']                = strval($data[$SettingMap['MobileEndNewsTitle']]);
        $RS['MobileEnd']['MobileEndNewsGroup']                = strval($data[$SettingMap['MobileEndNewsGroup']]);
        $RS['MobileEnd']['MobileEndNewsContent']              = strval($data[$SettingMap['MobileEndNewsContent']]);
        $RS['MobileEnd']['MobileEndNewsReadCounter']          = strval($data[$SettingMap['MobileEndNewsReadCounter']]);
        $RS['MobileEnd']['MobileEndNewsLikeCounter']          = strval($data[$SettingMap['MobileEndNewsLikeCounter']]);
        $RS['MobileEnd']['MobileEndNewsFavoriteCounter']      = strval($data[$SettingMap['MobileEndNewsFavoriteCounter']]);
        $RS['MobileEnd']['MobileEndNewsReadUsers']            = strval($data[$SettingMap['MobileEndNewsReadUsers']]);

        $RS['MobileEnd']['MobileEndSchoolmateCity']           = strval($data[$SettingMap['MobileEndSchoolmateCity']]);
        $RS['MobileEnd']['MobileEndSchoolmateCompany']        = strval($data[$SettingMap['MobileEndSchoolmateCompany']]);
        $RS['MobileEnd']['MobileEndSchoolmateIndustry']       = strval($data[$SettingMap['MobileEndSchoolmateIndustry']]);
        $RS['MobileEnd']['MobileEndSchoolmateFirstYear']      = strval($data[$SettingMap['MobileEndSchoolmateFirstYear']]);
        $RS['MobileEnd']['MobileEndSchoolmateLastYear']       = strval($data[$SettingMap['MobileEndSchoolmateLastYear']]);
        $RS['MobileEnd']['MobileEndSchoolmateAcademic']       = strval($data[$SettingMap['MobileEndSchoolmateAcademic']]);
        $RS['MobileEnd']['MobileEndSchoolmateLastActivity']   = strval($data[$SettingMap['MobileEndSchoolmateLastActivity']]);

        $MobileEndNewsCreator = strval(returntablefield("data_user","USER_ID",$data[$SettingMap['MobileEndNewsCreator']],"USER_NAME")["USER_NAME"]);;
        if($MobileEndNewsCreator!="") {
            $RS['MobileEnd']['MobileEndNewsCreator']          = $MobileEndNewsCreator;
        }
        else {
            $RS['MobileEnd']['MobileEndNewsCreator']          = $data[$SettingMap['MobileEndNewsCreator']];
        }
        $RS['MobileEnd']['MobileEndNewsCreatorGroup']         = strval($data[$SettingMap['MobileEndNewsCreatorGroup']]);
        $RS['MobileEnd']['MobileEndActivityFee']              = strval($data[$SettingMap['MobileEndActivityFee']]);
        $RS['MobileEnd']['MobileEndActivityContact']          = strval($data[$SettingMap['MobileEndActivityContact']]);
        $RS['MobileEnd']['MobileEndNewsEnrollment']           = strval($data[$SettingMap['MobileEndNewsEnrollment']]);
        $RS['MobileEnd']['MobileEndNewsLocation']             = strval($data[$SettingMap['MobileEndNewsLocation']]);
        $RS['MobileEnd']['MobileEndNewsLocation2']            = strval($data[$SettingMap['MobileEndNewsLocation2']]);
        $RS['MobileEnd']['MobileEndNewsCreateTime']           = substr($data[$SettingMap['MobileEndNewsCreateTime']],5,11);
        if($RS['MobileEnd']['MobileEndNewsLocation']!="") {
            $TempArray = explode('-', $RS['MobileEnd']['MobileEndNewsLocation']);
            $RS['MobileEnd']['MobileEndNewsLocation']         = $TempArray[1]." ".$TempArray[2];
            $RS['MobileEnd']['MobileEndNewsCreateTime']       = substr($data[$SettingMap['MobileEndNewsCreateTime']],5,5);
        }
        $RS['MobileEnd']['MobileEndNewsProcess']                = strval($data[$SettingMap['MobileEndNewsProcess']]);
        $RS['MobileEnd']['MobileEndNewsTopAvator']              = strval($data[$SettingMap['MobileEndNewsTopAvator']]);
        $RS['MobileEnd']['MobileEndActivityEnrollEndDate']      = strval($data[$SettingMap['MobileEndActivityEnrollEndDate']]);
        $RS['MobileEnd']['MobileEndActivityDate']               = strval($data[$SettingMap['MobileEndActivityDate']]);
        if($RS['MobileEnd']['MobileEndActivityEnrollEndDate']!="") {
            if($RS['MobileEnd']['MobileEndActivityEnrollEndDate']<date("Y-m-d")) {
                $RS['MobileEnd']['MobileEndActivityStatus'] = "结束";
            }
            else {
                $RS['MobileEnd']['MobileEndActivityStatus'] = "报名中";
            }
        }

        if($RS['MobileEnd']['MobileEndActivityDate']!="") {
            if($RS['MobileEnd']['MobileEndActivityDate']==date("Y-m-d")) {
                $RS['MobileEnd']['MobileEndActivityStatus'] = "进行中";
            }
        }

        if($SettingMap['MobileEndIconType']=="ImageField") {
            $data[$SettingMap['MobileEndNewsLeftImage']] = $data[$SettingMap['MobileEndIconField']];
        }
        if($SettingMap['MobileEndIconType']=="UserAvator") {

        }
        if($data[$SettingMap['MobileEndNewsLeftImage']]=="") {
            $data[$SettingMap['MobileEndNewsLeftImage']] = "/images/wechat/logo_icampus_left.png";
        }
        $RS['MobileEnd']['MobileEndNewsLeftImage']            = AttachFieldValueToUrl($TableName,$data['id'],$SettingMap['MobileEndNewsLeftImage'],'avatar',strval($data[$SettingMap['MobileEndNewsLeftImage']]));

        //Extra Logic
        if($SettingMap['MobileEndShowType']=="Activity") {
            $sql    = "select COUNT(*) AS NUM from data_xiaoyou_activity_record where 活动ID='".intval($data['id'])."' ";
            $rs     = $db->Execute($sql);
            $NUM    = intval($rs->fields['NUM']);
            $RS['MobileEnd']['MobileEndActivityHaveEnrollNumber'] = $NUM;
            $sql    = "select COUNT(*) AS NUM from data_xiaoyou_activity_record where 活动ID='".intval($data['id'])."' and 用户ID='".$GLOBAL_USER->USER_ID."' ";
            $rs     = $db->Execute($sql);
            $NUM    = intval($rs->fields['NUM']);
            $RS['MobileEnd']['MobileEndActivityMyEnrollStatus'] = $NUM;
        }
        $RS['MobileEnd']['MobileEndNewsEnableEnroll']               = $SettingMap['MobileEndNewsEnableEnroll'];
        $RS['MobileEnd']['MobileEndActionType']                     = $TableName;

        //Field Name
        $RS['MobileEnd']['MobileEndActivityFeeName']                = $SettingMap['MobileEndActivityFee'];
        $RS['MobileEnd']['MobileEndActivityContactName']            = $SettingMap['MobileEndActivityContact'];
        $RS['MobileEnd']['MobileEndNewsEnrollmentName']             = $SettingMap['MobileEndNewsEnrollment'];
        $RS['MobileEnd']['MobileEndNewsLocationName']               = $SettingMap['MobileEndNewsLocation'];
        $RS['MobileEnd']['MobileEndNewsLocation2Name']              = $SettingMap['MobileEndNewsLocation2'];
        $RS['MobileEnd']['MobileEndNewsCreateTimeName']             = $SettingMap['MobileEndNewsCreateTime'];
        $RS['MobileEnd']['MobileEndActivityDateName']               = $SettingMap['MobileEndActivityDate'];
        $RS['MobileEnd']['MobileEndActivityEnrollEndDateName']      = $SettingMap['MobileEndActivityEnrollEndDate'];
    }

    print_R(EncryptApiData($RS, $GLOBAL_USER));
    exit;
}

if($_GET['action']=="updateone")  {
    $id     = intval(DecryptID($_POST['id']));
    $field  = ParamsFilter($_POST['field']);
    $value  = ParamsFilter($_POST['value']);
    $primary_key = $MetaColumnNames[0];
    //Check Field Valid
    if($id>0&&$field!=""&&in_array($field,$MetaColumnNames)&&$primary_key!=$field&&($SettingMap['FieldEditable_'.$field]=='true' || $SettingMap['FieldEditable_'.$field]=='1')) {
        $sql    = "update $TableName set $field = '$value' where $primary_key = '$id'";
        $db->Execute($sql);
        //functionNameIndividual
        $functionNameIndividual = "plugin_".$TableName."_".$Step."_updateone";
        if(function_exists($functionNameIndividual))  {
            $functionNameIndividual($id);
        }
        //SystemLogRecord
        if(in_array($SettingMap['OperationLogGrade'],["EditAndDeleteOperation","AddEditAndDeleteOperation","AllOperation"]))  {
            SystemLogRecord("updateone", '', json_encode([$sql]));
        }
        $RS = [];
        $RS['status'] = "OK";
        $RS['msg'] = __("Update Success");
        print json_encode($RS);
        exit;
    }
    else {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("Params Error");
        $RS['_GET'] = $_GET;
        $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }
}

if($_GET['action']=="delete_array")  {
    $selectedRows  = ForSqlInjection($_POST['selectedRows']);
    $selectedRows = explode(',',$selectedRows);
    $primary_key = $MetaColumnNames[0];
    foreach($selectedRows as $id) {
        $id     = intval(DecryptID($id));
        if($id>0)  {
            //Check Permission For This Record
            //LimitEditAndDelete
            $sql            = "select * from $TableName where ".$MetaColumnNames[0]." = '$id'";
            $RecordOriginal = $db->Execute($sql);
            if($SettingMap['LimitEditAndDelete_Delete_Field_One']!="" && $SettingMap['LimitEditAndDelete_Delete_Field_One']!="None" && in_array($SettingMap['LimitEditAndDelete_Delete_Field_One'], $MetaColumnNames)) {
                $LimitEditAndDelete_Delete_Value_One_Array = explode(',',$SettingMap['LimitEditAndDelete_Delete_Value_One']);
                if(in_array($RecordOriginal->fields[$SettingMap['LimitEditAndDelete_Delete_Field_One']],$LimitEditAndDelete_Delete_Value_One_Array)) {
                    $RS = [];
                    $RS['status'] = "ERROR";
                    $RS['msg'] = __("Error Id Value");
                    $RS['_GET'] = $_GET;
                    $RS['_POST'] = $_POST;
                    print json_encode($RS);
                    exit;
                }
            }
            if($SettingMap['LimitEditAndDelete_Delete_Field_Two']!="" && $SettingMap['LimitEditAndDelete_Delete_Field_Two']!="None" && in_array($SettingMap['LimitEditAndDelete_Delete_Field_Two'], $MetaColumnNames)) {
                $LimitEditAndDelete_Delete_Value_Two_Array = explode(',',$SettingMap['LimitEditAndDelete_Delete_Value_Two']);
                if(in_array($RecordOriginal->fields[$SettingMap['LimitEditAndDelete_Delete_Field_Two']],$LimitEditAndDelete_Delete_Value_Two_Array)) {
                    $RS = [];
                    $RS['status'] = "ERROR";
                    $RS['msg'] = __("Error Id Value");
                    $RS['_GET'] = $_GET;
                    $RS['_POST'] = $_POST;
                    print json_encode($RS);
                    exit;
                }
            }
            if(in_array($SettingMap['OperationLogGrade'],["DeleteOperation","EditAndDeleteOperation","AddEditAndDeleteOperation","AllOperation"]))  {
                SystemLogRecord("delete_array", '', json_encode($RecordOriginal->fields));
            }

            $db->BeginTrans();
            $MultiSql   = [];
            $sql        = "delete from $TableName where $primary_key = '$id'";
            $db->Execute($sql);
            $MultiSql[] = $sql;
            //Relative Child Table Support
            $Relative_Child_Table                   = $SettingMap['Relative_Child_Table'];
            $Relative_Child_Table_Field_Name        = $SettingMap['Relative_Child_Table_Field_Name'];
            $Relative_Child_Table_Parent_Field_Name = $SettingMap['Relative_Child_Table_Parent_Field_Name'];
            if($Relative_Child_Table>0 && $Relative_Child_Table_Parent_Field_Name!="" && in_array($Relative_Child_Table_Parent_Field_Name,$MetaColumnNames)) {
                $ChildSettingMap = returntablefield("form_formflow",'id',$Relative_Child_Table,'Setting')['Setting'];
                $ChildSettingMap = unserialize(base64_decode($ChildSettingMap));
                $ChildFormId                = returntablefield("form_formflow",'id',$Relative_Child_Table,'FormId')['FormId'];
                $ChildTableName             = returntablefield("form_formname",'id',$ChildFormId,'TableName')['TableName'];
                $ChildMetaColumnNames       = GLOBAL_MetaColumnNames($ChildTableName);
                if($Relative_Child_Table_Field_Name!="" && in_array($Relative_Child_Table_Field_Name, $ChildMetaColumnNames) &&strpos($ChildSettingMap['Actions_In_List_Row'],'Edit')!==false) {
                    //Get All Fields

                    $sql                    = "delete from $ChildTableName where $Relative_Child_Table_Parent_Field_Name = '".$RecordOriginal->fields[$Relative_Child_Table_Parent_Field_Name]."';";
                    $db->Execute($sql);
                    $MultiSql[]             = $sql;
                }
            }
            $db->CommitTrans();

            //functionNameIndividual
            $functionNameIndividual = "plugin_".$TableName."_".$Step."_delete_array";
            if(function_exists($functionNameIndividual))  {
                $functionNameIndividual($id);
            }
        }
    }
    $RS = [];
    $RS['status']   = "OK";
    $RS['MultiSql'] = $MultiSql;
    $RS['msg']      = __("Drop Item Success");
    print json_encode($RS);
    exit;
}

if($_GET['action']=="Reset_Password_123654")  {
    $selectedRows  = ForSqlInjection($_POST['selectedRows']);
    $selectedRows = explode(',',$selectedRows);
    $primary_key = $MetaColumnNames[0];
    foreach($selectedRows as $id) {
        $id     = intval(DecryptID($id));
        if($id>0)  {
            if(1)  {
                $sql    = "select * from $TableName where $primary_key = '$id'";
                $rs     = $db->Execute($sql);
                SystemLogRecord("Reset_Password_123654", '', json_encode($rs->fields));
            }
            $密码       = password_make("123654");
            if(in_array("密码",$MetaColumnNames)) {
                $sql        = "update $TableName set 密码='$密码' where $primary_key = '$id'";
                $db->Execute($sql);
            }
            if(in_array("PASSWORD",$MetaColumnNames)) {
                $sql        = "update $TableName set `PASSWORD`='$密码' where $primary_key = '$id'";
                $db->Execute($sql);
            }
            //functionNameIndividual
            //$functionNameIndividual = "plugin_".$TableName."_".$Step."_delete_array";
            //if(function_exists($functionNameIndividual))  {
            //    $functionNameIndividual($id);
            //}
        }
    }
    $RS = [];
    $RS['status'] = "OK";
    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
    $RS['msg'] = __("Change Password Success");
    print json_encode($RS);
    exit;
}

if($_GET['action']=="Reset_Password_ID_Last6")  {
    $selectedRows  = ForSqlInjection($_POST['selectedRows']);
    $selectedRows = explode(',',$selectedRows);
    $primary_key = $MetaColumnNames[0];
    foreach($selectedRows as $id) {
        $id     = intval(DecryptID($id));
        if($id>0)  {
            $sql    = "select * from $TableName where $primary_key = '$id'";
            $rs     = $db->Execute($sql);
            SystemLogRecord("Reset_Password_ID_Last6", '', json_encode($rs->fields));
            $身份证件号 = $rs->fields['身份证件号'];
            if(strlen($身份证件号)>6) {
                $身份证件号6 = substr($身份证件号,-6);
            }
            else {
                $身份证件号6 = "123654";
            }
            $密码       = password_make($身份证件号6);
            if(in_array("密码",$MetaColumnNames)) {
                $sql        = "update $TableName set 密码='$密码' where $primary_key = '$id'";
                $db->Execute($sql);
            }
            if(in_array("PASSWORD",$MetaColumnNames)) {
                $sql        = "update $TableName set `PASSWORD`='$密码' where $primary_key = '$id'";
                $db->Execute($sql);
            }
            //functionNameIndividual
            //$functionNameIndividual = "plugin_".$TableName."_".$Step."_delete_array";
            //if(function_exists($functionNameIndividual))  {
            //    $functionNameIndividual($id);
            //}
        }
    }
    $RS = [];
    $RS['status'] = "OK";
    if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  $RS['sql'] = $sql;
    $RS['msg'] = __("Change Password Success");
    print json_encode($RS);
    exit;
}


//列表页面时的启用字段列表
$init_default_columns   = [];
$columnsactions         = [];
if(in_array('View',$Actions_In_List_Row_Array)) {
    $columnsactions[]   = ['action'=>'view_default','text'=>__('View'),'mdi'=>'mdi:eye-outline'];
}
if(in_array('Edit',$Actions_In_List_Row_Array)) {
    $columnsactions[]   = ['action'=>'edit_default','text'=>$SettingMap['Rename_List_Edit_Button'],'mdi'=>'mdi:pencil-outline'];
}
if(in_array('Delete',$Actions_In_List_Row_Array)) {
    $columnsactions[]   = ['action'=>'delete_array','text'=>$SettingMap['Rename_List_Delete_Button'],'mdi'=>'mdi:delete-outline','double_check'=>__('Do you want to delete this item?')];
}
$init_default_columns[] = ['flex' => 0.1, 'minWidth' => 120, 'sortable' => false, 'field' => "actions", 'headerName' => __("Actions"), 'show'=>true, 'type'=>'actions', 'actions' => $columnsactions];


$ApprovalNodeFieldsArray        = explode(',',$SettingMap['ApprovalNodeFields']);
$ApprovalNodeFieldsArrayFlip    = array_flip($ApprovalNodeFieldsArray);
if($SettingMap['ApprovalNodeTitle']!="")   {
    $RS['init_default']['ApprovalNodeFields']['AllNodes']       = $ApprovalNodeFieldsArray;
    $RS['init_default']['ApprovalNodeFields']['CurrentNode']    = $SettingMap['ApprovalNodeCurrentField'];
    $RS['init_default']['ApprovalNodeFields']['ActiveStep']     = $ApprovalNodeFieldsArrayFlip[$SettingMap['ApprovalNodeCurrentField']];
    $RS['init_default']['ApprovalNodeFields']['ApprovalNodeTitle']  = $SettingMap['ApprovalNodeTitle'];
}
else    {
    $RS['init_default']['ApprovalNodeFields']['AllNodes']       = [];
    $RS['init_default']['ApprovalNodeFields']['CurrentNode']    = "";
    $RS['init_default']['ApprovalNodeFields']['ActiveStep']     = 0;
    $RS['init_default']['ApprovalNodeFields']['ApprovalNodeTitle']  = "";
}



$ApprovalNodeFieldsArray = explode(',',$SettingMap['ApprovalNodeFields']);
$ApprovalNodeFieldsHidden = [];
$ApprovalNodeFieldsStatus = [];
foreach($ApprovalNodeFieldsArray as $TempField) {
    //$ApprovalNodeFieldsHidden[] = $TempField."审核状态";
    //$ApprovalNodeFieldsHidden[] = $TempField."申请时间";
    //$ApprovalNodeFieldsHidden[] = $TempField."申请人";
    $ApprovalNodeFieldsHidden[] = $TempField."审核时间";
    $ApprovalNodeFieldsHidden[] = $TempField."审核人";
    $ApprovalNodeFieldsHidden[] = $TempField."审核意见";
    $ApprovalNodeFieldsStatus[$TempField."审核状态"] = $TempField."审核状态";
}
$ApprovalNodeFieldsStatus = array_keys($ApprovalNodeFieldsStatus);
$searchField = [];
$groupField = [];
$FieldNameToType = [];
$UpdateFields = [];
foreach($AllFieldsFromTable as $Item)  {
    $FieldName      = $Item['FieldName'];
    $EnglishName    = $Item['EnglishName'];
    $ShowType       = $Item['ShowType'];
    $IsSearch       = $Item['IsSearch'];
    $IsGroupFilter  = $Item['IsGroupFilter'];
    $ColumnWidth    = intval($Item['ColumnWidth']);
    $IsHiddenGroupFilter = $Item['IsHiddenGroupFilter'];
    $CurrentFieldType = $AllShowTypesArray[$ShowType]['LIST'];
    $CurrentFieldTypeArray = explode(':',$CurrentFieldType);
    $FieldNameToType[$FieldName] = $CurrentFieldType;
    //print $FieldName.":".$ShowType.":".$CurrentFieldType.'<BR>';
    if(in_array($FieldName,$ApprovalNodeFieldsHidden)) {
        continue;
    }

    global $GLOBAL_LANGUAGE;
    switch($GLOBAL_LANGUAGE) {
        case 'zhCN':
            $ShowTextName    = $Item['ChineseName'];
            break;
        case 'enUS':
            $ShowTextName    = $Item['EnglishName'];
            break;
        default:
            $ShowTextName    = $Item['EnglishName'];
            break;
    }

    $editable = false;
    if($SettingMap['FieldEditable_'.$FieldName]=='true' || $SettingMap['FieldEditable_'.$FieldName]=='1')   {
        $editable = true;
        $UpdateFields[] = $FieldName;
    }

    //Filter Field Type
    $FieldTypeInFlow = $SettingMap['FieldType_'.$FieldName];
    $FieldTypeInFlow_Map = [];
    switch($FieldTypeInFlow)   {
        case 'View_Use_ListAddEdit_NotUse':
        case 'Disable':
        case '':
            $CurrentFieldTypeArray[0] = "Disable";
            break;
    }
    //print $FieldName.":".$FieldTypeInFlow." ".$CurrentFieldTypeArray[0]."\n";

    switch($CurrentFieldTypeArray[0])   {
        case 'Disable':
        case '':
            break;
        case 'api1':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>'api', 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable,'apimdi'=>'mdi:chart-donut','apicolor'=>'info.main', 'apiaction' => "edit_default_1"];
            break;
        case 'api2':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>'api', 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable,'apimdi'=>'mdi:cog-outline','apicolor'=>'info.main', 'apiaction' => "edit_default_2"];
            break;
        case 'api3':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>'api', 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable,'apimdi'=>'mdi:border-bottom','apicolor'=>'info.main', 'apiaction' => "edit_default_3"];
            break;
        case 'api4':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>'api', 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable,'apimdi'=>'mdi:cellphone','apicolor'=>'info.main', 'apiaction' => "edit_default_4"];
            break;
        case 'api5':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>'api', 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable,'apimdi'=>'mdi:message-bulleted','apicolor'=>'info.main', 'apiaction' => "edit_default_5"];
            break;
        case 'api6':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>'api', 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable,'apimdi'=>'mdi:chart-donut','apicolor'=>'info.main', 'apiaction' => "edit_default_6"];
            break;
        case 'tablefilter':
        case 'tablefiltercolor':
        case 'autocomplete':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
        case 'autocompletemulti':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+200, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
        case 'radiogroup':
        case 'radiogroupcolor':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
        case 'avatar':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
        case 'images':
        case 'images2':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
        case 'files':
        case 'files2':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
        case 'file':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
        case 'xlsx':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
        case 'ExternalUrl':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], "href"=>"", "target"=>"_blank", 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
        default:
            $FieldType = "string";
            if(in_array($FieldName,$ApprovalNodeFieldsStatus))  {
                $FieldType = "approvalnode";
                $ColumnWidth = 265;
                $rowHeight = 45;
            }
            //print_R($FieldName);
            //print_R($ApprovalNodeFieldsStatus);
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$FieldType, 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $ShowTextName, 'show'=>true, 'renderCell' => NULL, 'editable'=>$editable];
            break;
    }
    if($IsSearch==1&&($SettingMap['FieldSearch_'.$FieldName]=='true'||$SettingMap['FieldSearch_'.$FieldName]=='1'))   {
        $searchField[] = ['label' => $ShowTextName, 'value' => $FieldName];
    }
    if($SettingMap['FieldGroup_'.$FieldName]=='true'||$SettingMap['FieldGroup_'.$FieldName]=='1')   { //$IsGroupFilter==1&&
        $groupField[] = $FieldName;
    }

}

//Search Field
$RS['init_default']['searchFieldArray'] = $searchField;
$RS['init_default']['searchFieldText'] = __("Search Item");
if($_REQUEST['searchFieldName']=="") $_REQUEST['searchFieldName'] = $MetaColumnNames[1];
$RS['init_default']['searchFieldName'] = ForSqlInjection($_REQUEST['searchFieldName']);

$searchFieldName     = ForSqlInjection($_REQUEST['searchFieldName']);
$searchFieldValue    = ForSqlInjection($_REQUEST['searchFieldValue']);
if ($searchFieldName != "" && $searchFieldValue != "" && in_array($searchFieldName, $MetaColumnNames) ) {
    $AddSql .= " and ($searchFieldName like '%" . $searchFieldValue . "%')";
}
$RS['init_default']['searchFieldValue'] = ForSqlInjection($_REQUEST['searchFieldValue']);

//Extra_Priv_Filter_Field
Extra_Priv_Filter_Field_To_SQL();

$functionNameIndividual = "plugin_".$TableName."_".$Step."_init_default";
if(function_exists($functionNameIndividual))  {
    $functionNameIndividual($id);
}

//Group Filter
$RS['init_default']['filter'] = [];
foreach($groupField as $FieldName) {
    $sql    = "select $FieldName as name, $FieldName as value, count(*) AS num from $TableName $AddSql group by $FieldName order by $FieldName desc";
    $rs     = $db->Execute($sql) or print $sql;
    $rs_a   = $rs->GetArray();
    $ShowType   = $AllFieldsMap[$FieldName]['ShowType'];
    $FieldType  = $AllShowTypesArray[$ShowType]['LIST'];
    $FieldTypeArray = explode(":",$FieldType);
    switch($FieldTypeArray[0]) {
        case 'tablefilter':
        case 'tablefiltercolor':
        case 'radiogroup':
        case 'radiogroupcolor':
            $TempTableName      = $FieldTypeArray[1];
            $TempKeyIndex       = $FieldTypeArray[2];
            $TempValueIndex     = $FieldTypeArray[3];
            if($TempKeyIndex!=$TempValueIndex)  {
                $TempColumnNames    = GLOBAL_MetaColumnNames($TempTableName);
                for($i=0;$i<sizeof($rs_a);$i++)  {
                    if($rs_a[$i]['value']!="")   {
                        $rs_a[$i]['name'] = returntablefield($TempTableName,$TempColumnNames[$TempKeyIndex],$rs_a[$i]['value'],$TempColumnNames[$TempValueIndex])[$TempColumnNames[$TempValueIndex]];
                    }
                    else {
                        $rs_a[$i]['name']   = __("NULL");
                        $rs_a[$i]['value']  = "NULL";
                    }
                }
            }
            break;
    }
    for($i=0;$i<sizeof($rs_a);$i++)  {
        if($rs_a[$i]['value']=="")   {
            $rs_a[$i]['name']   = __("NULL");
            $rs_a[$i]['value']  = "NULL";
        }
    }
    $ALL_NUM = 0;
    foreach($rs_a as $Item) {
        $ALL_NUM += $Item['num'];
    }
    global $GLOBAL_LANGUAGE;
    switch($GLOBAL_LANGUAGE) {
        case 'zhCN':
            $ShowTextName    = $AllFieldsMap[$FieldName]['ChineseName'];
            break;
        case 'enUS':
            $ShowTextName    = $AllFieldsMap[$FieldName]['EnglishName'];
            break;
        default:
            $ShowTextName    = $AllFieldsMap[$FieldName]['EnglishName'];
            break;
    }
    array_unshift($rs_a,['name'=>__('All Data'), 'value'=>'All Data', 'num'=>$ALL_NUM]);
    if($_POST[$FieldName]!="") {
        $selected = ForSqlInjection($_POST[$FieldName]);
    }
    else if(strpos($FieldName, "学期") !== false) {
        $selected = getCurrentXueQi();
        global $AddSql;
        if(in_array("当前学期", $MetaColumnNames) && !isset($_GET['当前学期']) ) {
            $AddSql .= " and 当前学期='$selected'";
        }
        elseif(in_array("学期", $MetaColumnNames) && !isset($_GET['学期']) ) {
            $AddSql .= " and 学期='$selected'";
        }
        elseif(in_array("学期名称", $MetaColumnNames) && !isset($_GET['学期名称']) ) {
            $AddSql .= " and 学期名称='$selected'";
        }
    }
    else {
        $selected = "All Data";
    }
    $RS['init_default']['filter'][] = ['name' => $FieldName, 'text' => $ShowTextName, 'list' => $rs_a, 'selected' => $selected];
    //Sql Filter
    if(is_array($_REQUEST[$FieldName]))         {
        $TempArray = $_REQUEST[$FieldName];
        $NewFilterArray = [];
        $IsHaveAllData  = 0;
        foreach($TempArray as $Temp) {
            if ($Temp != "" && $Temp != "NULL" && $Temp != "All Data") {
                $NewFilterArray[] = ForSqlInjection($Temp);
            }
            else if ($Temp == "NULL") {
                $NewFilterArray[] = '';
            }
            else if ($Temp == "All Data") {
                $IsHaveAllData = 1;
            }
        }
        if($IsHaveAllData) {
            //Get All Data
        }
        elseif(sizeof($NewFilterArray)==1) {
            $AddSql .= " and `$FieldName` = '".$NewFilterArray[0]."'";
        }
        elseif(sizeof($NewFilterArray)>0) {
            $AddSql .= " and `$FieldName` in ('".join("','",$NewFilterArray)."')";
        }
    }
    else    {
        $SqlFilterValue = ForSqlInjection($_REQUEST[$FieldName]);
        if ($SqlFilterValue != "" && $SqlFilterValue != "NULL" && $SqlFilterValue != "All Data") {
            $AddSql .= " and (`$FieldName` = '" . $SqlFilterValue . "')";
        }
        else if ($SqlFilterValue == "NULL") {
            $AddSql .= " and (`$FieldName` = '')";
        }
        else {
            //Get All Data
        }
    }
}

//print "TIME EXCEUTE 8:".(time()-$TIME_BEGIN)."<BR>\n";

$pageNumberArray = $SettingMap['pageNumberArray'];
if($pageNumberArray=="" || true) {
    $pageNumberArray = [10,15,20,30,40,50,100];
}
$page       = intval($_REQUEST['page']);
$pageSize   = intval($_REQUEST['pageSize']);
if(!in_array($pageSize,$pageNumberArray) || $pageSize == 10)  {
	$pageSize = intval($SettingMap['Page_Number_In_List']);
}
$fromRecord = $page * $pageSize;


//print "TIME EXCEUTE 9:".(time()-$TIME_BEGIN)."<BR>\n";
if($FromInfo['TableName']!="")   {
    $RS['init_default']['searchtitle']  = $FromInfo['FullName'];
}
else {
    $RS['init_default']['searchtitle']  = "Unknown Form";
}
$RS['init_default']['searchtitle']  = $SettingMap['List_Title_Name'];

$RS['init_default']['primarykey']   = $MetaColumnNames[0];


//print "TIME EXCEUTE 10:".(time()-$TIME_BEGIN)."<BR>\n";

if($_REQUEST['sortColumn']=="")   {
    //order default
    $order_by_array = [];
    $Default_Order_Method_By_Field_One = $SettingMap['Default_Order_Method_By_Field_One'];
    $Default_Order_Method_By_Desc_One = $SettingMap['Default_Order_Method_By_Desc_One'];
    if(in_array($Default_Order_Method_By_Field_One, $MetaColumnNames))  {
        $order_by_array[] = "".$Default_Order_Method_By_Field_One." ".$Default_Order_Method_By_Desc_One;
    }
    $Default_Order_Method_By_Field_Two = $SettingMap['Default_Order_Method_By_Field_Two'];
    $Default_Order_Method_By_Desc_Two = $SettingMap['Default_Order_Method_By_Desc_Two'];
    if(in_array($Default_Order_Method_By_Field_Two, $MetaColumnNames))  {
        $order_by_array[] = "".$Default_Order_Method_By_Field_Two." ".$Default_Order_Method_By_Desc_Two;
    }
    $Default_Order_Method_By_Field_Three = $SettingMap['Default_Order_Method_By_Field_Three'];
    $Default_Order_Method_By_Desc_Three = $SettingMap['Default_Order_Method_By_Desc_Three'];
    if(in_array($Default_Order_Method_By_Field_Three, $MetaColumnNames))  {
        $order_by_array[] = "".$Default_Order_Method_By_Field_Three." ".$Default_Order_Method_By_Desc_Three;
    }
    if(sizeof($order_by_array)>0) {
        $orderby = "order by ".join(',',$order_by_array)."";
    }
}
else {
    if($_REQUEST['sortMethod']=="desc"&&in_array($_REQUEST['sortColumn'], $MetaColumnNames)) {
        $orderby = "order by `".$_REQUEST['sortColumn']."` desc";
    }
    elseif(in_array($_REQUEST['sortColumn'], $MetaColumnNames)) {
        $orderby = "order by `".$_REQUEST['sortColumn']."` asc";
    }
}


$ForbiddenSelectRow = [];
$ForbiddenViewRow   = [];
$ForbiddenEditRow   = [];
$ForbiddenDeleteRow = [];
$ForbiddenSelectRowOriginal = [];
$ForbiddenViewRowOriginal   = [];
$ForbiddenEditRowOriginal   = [];
$ForbiddenDeleteRowOriginal = [];

//Get Total Records Number
$sql    = "select count(*) AS NUM from $TableName " . $AddSql . "";
$sqlList[] = $sql;
$rs     = $db->Execute($sql);
$RS['init_default']['total'] = intval($rs->fields['NUM']);

//Get All Data
$sql         = "select * from $TableName " . $AddSql . " $orderby limit $fromRecord,$pageSize";
$sqlList[]   = $sql;
//print $sql;
$NewRSA = [];
$rs     = $db->Execute($sql) or print $sql;
$rs_a   = $rs->GetArray();
$FieldDataColorValue = [];
$GetAllIDList = [];
$MobileEndData = [];
foreach ($rs_a as $Line) {
    $Line2              = $Line;
    $OriginalID         = $Line['id'];
    $GetAllIDList[]     = $Line['id'];
    $Line['id2']        = $Line['id'];
    $Line['id']         = EncryptID($Line['id']);

    $MobileEndItem                                      = [];
    //List Template
    $MobileEndItem['MobileEndFirstLine']                    = strval($SettingMap['MobileEndFirstLine']);
    $MobileEndItem['MobileEndSecondLineLeft']               = strval($SettingMap['MobileEndSecondLineLeft']);
    $MobileEndItem['MobileEndSecondLineLeftColorField']     = strval($SettingMap['MobileEndSecondLineLeftColorField']);
    $MobileEndItem['MobileEndSecondLineLeftColorRule']      = strval($SettingMap['MobileEndSecondLineLeftColorRule']);
    $MobileEndItem['MobileEndSecondLineRight']              = strval($SettingMap['MobileEndSecondLineRight']);
    $MobileEndItem['MobileEndSecondLineRightColorField']    = strval($SettingMap['MobileEndSecondLineRightColorField']);
    $MobileEndItem['MobileEndSecondLineRightColorRule']     = strval($SettingMap['MobileEndSecondLineRightColorRule']);
    //News Template
    $MobileEndItem['MobileEndNewsTitle']                = strval($Line[$SettingMap['MobileEndNewsTitle']]);
    $MobileEndItem['MobileEndNewsGroup']                = strval($Line[$SettingMap['MobileEndNewsGroup']]);
    $MobileEndItem['MobileEndNewsContent']              = strip_tags($Line[$SettingMap['MobileEndNewsContent']]);
    $MobileEndItem['MobileEndNewsReadCounter']          = strval($Line[$SettingMap['MobileEndNewsReadCounter']]);
    $MobileEndItem['MobileEndNewsLikeCounter']          = strval($Line[$SettingMap['MobileEndNewsLikeCounter']]);
    $MobileEndItem['MobileEndNewsFavoriteCounter']      = strval($Line[$SettingMap['MobileEndNewsFavoriteCounter']]);
    $MobileEndItem['MobileEndNewsReadUsers']            = strval($Line[$SettingMap['MobileEndNewsReadUsers']]);

    $MobileEndItem['MobileEndSchoolmateCity']           = strval($Line[$SettingMap['MobileEndSchoolmateCity']]);
    $MobileEndItem['MobileEndSchoolmateCompany']        = strval($Line[$SettingMap['MobileEndSchoolmateCompany']]);
    $MobileEndItem['MobileEndSchoolmateIndustry']       = strval($Line[$SettingMap['MobileEndSchoolmateIndustry']]);
    $MobileEndItem['MobileEndSchoolmateFirstYear']      = strval($Line[$SettingMap['MobileEndSchoolmateFirstYear']]);
    $MobileEndItem['MobileEndSchoolmateLastYear']       = strval($Line[$SettingMap['MobileEndSchoolmateLastYear']]);
    $MobileEndItem['MobileEndSchoolmateAcademic']       = strval($Line[$SettingMap['MobileEndSchoolmateAcademic']]);
    $MobileEndItem['MobileEndSchoolmateLastActivity']   = strval($Line[$SettingMap['MobileEndSchoolmateLastActivity']]);

    $MobileEndNewsCreator = strval(returntablefield("data_user","USER_ID",$Line[$SettingMap['MobileEndNewsCreator']],"USER_NAME")["USER_NAME"]);;
    if($MobileEndNewsCreator!="") {
        $MobileEndItem['MobileEndNewsCreator']          = $MobileEndNewsCreator;
    }
    else {
        $MobileEndItem['MobileEndNewsCreator']          = $Line[$SettingMap['MobileEndNewsCreator']];
    }
    $MobileEndItem['MobileEndNewsCreatorGroup']         = strval($Line[$SettingMap['MobileEndNewsCreatorGroup']]);
    $MobileEndItem['MobileEndActivityFee']              = strval($Line[$SettingMap['MobileEndActivityFee']]);
    $MobileEndItem['MobileEndActivityContact']          = strval($Line[$SettingMap['MobileEndActivityContact']]);
    $MobileEndItem['MobileEndNewsEnrollment']           = strval($Line[$SettingMap['MobileEndNewsEnrollment']]);
    $MobileEndItem['MobileEndNewsLocation']             = strval($Line[$SettingMap['MobileEndNewsLocation']]);
    $MobileEndItem['MobileEndNewsLocation2']            = strval($Line[$SettingMap['MobileEndNewsLocation2']]);
    $MobileEndItem['MobileEndNewsCreateTime']           = substr($Line[$SettingMap['MobileEndNewsCreateTime']],5,11);
    if($MobileEndItem['MobileEndNewsLocation']!="") {
        $TempArray = explode('-', $MobileEndItem['MobileEndNewsLocation']);
        $MobileEndItem['MobileEndNewsLocation']         = $TempArray[1]." ".$TempArray[2];
        $MobileEndItem['MobileEndNewsCreateTime']       = substr($Line[$SettingMap['MobileEndNewsCreateTime']],5,5);
    }
    $MobileEndItem['MobileEndNewsProcess']              = strval($Line[$SettingMap['MobileEndNewsProcess']]);
    $MobileEndItem['MobileEndNewsTopAvator']            = strval($Line[$SettingMap['MobileEndNewsTopAvator']]);
    $MobileEndItem['MobileEndActivityEnrollEndDate']    = strval($Line[$SettingMap['MobileEndActivityEnrollEndDate']]);
    $MobileEndItem['MobileEndActivityDate']             = strval($Line[$SettingMap['MobileEndActivityDate']]);
    if($MobileEndItem['MobileEndActivityEnrollEndDate']!="") {
        if($MobileEndItem['MobileEndActivityEnrollEndDate']<date("Y-m-d")) {
            $MobileEndItem['MobileEndActivityStatus'] = "报名结束";
        }
        else {
            $MobileEndItem['MobileEndActivityStatus'] = "报名中";
        }
    }
    if($MobileEndItem['MobileEndActivityDate']!="") {
        if($MobileEndItem['MobileEndActivityDate']==date("Y-m-d")) {
            $MobileEndItem['MobileEndActivityStatus'] = "进行中";
        }
    }

    if($SettingMap['MobileEndIconType']=="ImageField" && $Line[$SettingMap['MobileEndIconField']] != "") {
        $TempValue = AttachFieldValueToUrl($TableName,$OriginalID,$SettingMap['MobileEndIconField'],'images',$Line[$SettingMap['MobileEndIconField']]);
        $Line[$SettingMap['MobileEndIconField']]    = $TempValue[0]['webkitRelativePath'];
    }
    else if($SettingMap['MobileEndIconType']=="UserAvator" && $Line[$SettingMap['MobileEndIconField']] != "") {
        //$TempValue = AttachFieldValueToUrl($TableName,$OriginalID,$SettingMap['MobileEndIconField'],'avatar',$Line[$SettingMap['MobileEndIconField']]);
        //$Line[$SettingMap['MobileEndIconField']]    = $TempValue[0]['webkitRelativePath'];
    }
    else if($Line[$SettingMap['MobileEndIconField']]=="") {
        $Line[$SettingMap['MobileEndIconField']]    = "/images/wechat/logo_icampus_left.png";
    }
    $MobileEndItem['MobileEndNewsLeftImage']            = strval($Line[$SettingMap['MobileEndIconField']]);
    //Notification Template

    foreach($Line as $FieldName=>$FieldValue) {
        if($FieldValue=="1971-01-01" || $FieldValue=="1971-01-01 00:00:00" || $FieldValue=="1971-01")  {
            $Line[$FieldName] = "";
        }
        //Decrypt Field Value
        $SettingTempMap                 = $AllFieldsMap[$FieldName]['Setting'];
        $DataFieldEncryptMethod         = $SettingTempMap['DataFieldEncryptMethod'];
        $DataFieldEncryptKey            = $SettingTempMap['DataFieldEncryptKey'];
        if($DataFieldEncryptMethod==1&&$DataFieldEncryptKey!="") {
            $Line[$FieldName]           = DecryptIDStorage($Line[$FieldName], $DataFieldEncryptKey);
        }
        // filter data to show on the list page -- begin
        $CurrentFieldType = $FieldNameToType[$FieldName];
        $CurrentFieldTypeArray = explode(':',$CurrentFieldType);
        switch($CurrentFieldTypeArray[0])   {
            case 'radiogroup':
            case 'radiogroupcolor':
            case 'tablefilter':
            case 'tablefiltercolor':
            case 'autocomplete':
                $TableNameTemp      = $CurrentFieldTypeArray[1];
                $KeyField           = $CurrentFieldTypeArray[2];
                $ValueField         = $CurrentFieldTypeArray[3];
                $DefaultValue       = $CurrentFieldTypeArray[4];
                $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);
                if($WhereField!="" && $WhereValue!="" && $MetaColumnNamesTemp[$KeyField]!="" && $Line[$FieldName]!="") {
                    $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' and `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($Line[$FieldName])."' ;";
                    $rs = $db->Execute($sql) or print($sql);
                    $Line[$FieldName] = $rs->fields['label'];
                    if($Line[$FieldName]=="") $Line[$FieldName] = $WhereValue;
                    $FieldDataColorValue[$FieldName][$Line[$FieldName]] = "#";
                    //print "TIME EXCEUTE 12:".(time()-$TIME_BEGIN)." ".$Line[$FieldName]." $sql <BR>\n";
                }
                elseif($MetaColumnNamesTemp[$KeyField]!="" && $Line[$FieldName]!="")    {
                    $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($Line[$FieldName])."' ;";
                    $rs = $db->Execute($sql) or print($sql);
                    if($rs->fields['label']!="")  {
                        $Line[$FieldName] = $rs->fields['label'];
                    }
                    $FieldDataColorValue[$FieldName][$Line[$FieldName]] = "#";
                    //print "TIME EXCEUTE 13:".(time()-$TIME_BEGIN)." ".$Line[$FieldName]." $sql <BR>\n";
                }
                break;
            case 'autocompletemulti':
                $TableNameTemp      = $CurrentFieldTypeArray[1];
                $KeyField           = $CurrentFieldTypeArray[2];
                $ValueField         = $CurrentFieldTypeArray[3];
                $DefaultValue       = $CurrentFieldTypeArray[4];
                $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);
                $MultiValueArray        = explode(',',$Line[$FieldName]);
                $MultiValueRS           = [];
                foreach($MultiValueArray as $MultiValue) {
                    if($WhereField!="" && $WhereValue!="" && $MetaColumnNamesTemp[$KeyField]!="" && $MultiValue!="") {
                        $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' and `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($MultiValue)."' ;";
                        $rs = $db->Execute($sql) or print($sql);
                        $MultiValueRS[] = $rs->fields['label'];
                    }
                    elseif($MetaColumnNamesTemp[$KeyField]!="" && $MultiValue!="")    {
                        $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($MultiValue)."' ;";
                        $rs = $db->Execute($sql) or print($sql);
                        $MultiValueRS[] = $rs->fields['label'];
                    }
                }
                $Line[$FieldName] = join(',',$MultiValueRS);
                $FieldDataColorValue[$FieldName][$Line[$FieldName]] = "#";
                //print "TIME EXCEUTE 13:".(time()-$TIME_BEGIN)."<BR>\n";
                break;
            case 'avatar':
                $Line[$FieldName] = AttachFieldValueToUrl($TableName,$OriginalID,$FieldName,'avatar',$Line2[$FieldName]);
                break;
            case 'images':
            case 'images2':
                $ImagesValue = AttachFieldValueToUrl($TableName,$OriginalID,$FieldName,'images',$Line2[$FieldName]);
                $Line[$FieldName] = $ImagesValue;
                break;
            case 'files':
            case 'files2':
                $Line[$FieldName] = AttachFieldValueToUrl($TableName,$OriginalID,$FieldName,'files',$Line2[$FieldName]);
                break;
            case 'file':
                $Line[$FieldName] = AttachFieldValueToUrl($TableName,$OriginalID,$FieldName,'file',$Line2[$FieldName]);
                break;
            case 'xlsx':
                $Line[$FieldName] = AttachFieldValueToUrl($TableName,$OriginalID,$FieldName,'xlsx',$Line2[$FieldName]);
                break;
            case 'password':
                $Line[$FieldName] = "******";
                break;
            case 'datetime':
                $Line[$FieldName] = substr($FieldValue, 5, 11);
                break;
        }
        //Data Mask
        $SettingTempMap = $AllFieldsMap[$FieldName]['Setting'];
        $DataMask       = $SettingTempMap['DataMask'];
        switch($DataMask) {
            case 'Last6digitsPlusStar':
                if(strlen($Line[$FieldName])>6) {
                    $Line[$FieldName] = "******".substr($Line[$FieldName],-6);
                }
                break;
            case 'Pre6digitsPlusStar':
                if(strlen($Line[$FieldName])>6) {
                    $Line[$FieldName] = substr($Line[$FieldName],0,-6)."******";
                }
                break;
        }
        // filter data to show on the list page -- End
        // Mobile End Data Filter
        // List Template 1
        $MobileEndItem['MobileEndFirstLine']            = str_replace("[".$FieldName."]", (String)$Line[$FieldName], $MobileEndItem['MobileEndFirstLine']);
        $MobileEndItem['MobileEndSecondLineLeft']       = str_replace("[".$FieldName."]", (String)$Line[$FieldName], $MobileEndItem['MobileEndSecondLineLeft']);
        $MobileEndItem['MobileEndSecondLineRight']      = str_replace("[".$FieldName."]", (String)$Line[$FieldName], $MobileEndItem['MobileEndSecondLineRight']);
        $MobileEndItem['MobileEndSecondLineLeft']       = str_replace("()","",$MobileEndItem['MobileEndSecondLineLeft']);
        $MobileEndItem['MobileEndSecondLineRight']      = str_replace("()","",$MobileEndItem['MobileEndSecondLineRight']);
        $MobileEndItem['MobileEndSecondLineRight']      = str_replace(":0",":",$MobileEndItem['MobileEndSecondLineRight']);

        $MobileEndSecondLineLeftColor = [];
        $MobileEndSecondLineLeftColorArray = explode(',', $MobileEndItem['MobileEndSecondLineLeftColorRule']);
        foreach($MobileEndSecondLineLeftColorArray as $MobileEndSecondLineLeftColorItem) {
            $MobileEndSecondLineLeftColorItemArray = explode(':', $MobileEndSecondLineLeftColorItem);
            $MobileEndSecondLineLeftColor[$MobileEndSecondLineLeftColorItemArray[0]] = $MobileEndSecondLineLeftColorItemArray[1];
        }
        $MobileEndItem['MobileEndSecondLineLeftColor'] = $MobileEndSecondLineLeftColor[$Line[$MobileEndItem['MobileEndSecondLineLeftColorField']]];

        $MobileEndSecondLineRightColor = [];
        $MobileEndSecondLineRightColorArray = explode(',', $MobileEndItem['MobileEndSecondLineRightColorRule']);
        foreach($MobileEndSecondLineRightColorArray as $MobileEndSecondLineRightColorItem) {
            $MobileEndSecondLineRightColorItemArray = explode(':', $MobileEndSecondLineRightColorItem);
            $MobileEndSecondLineRightColor[$MobileEndSecondLineRightColorItemArray[0]] = $MobileEndSecondLineRightColorItemArray[1];
        }
        $MobileEndItem['MobileEndSecondLineRightColor'] = $MobileEndSecondLineRightColor[$Line[$MobileEndItem['MobileEndSecondLineRightColorField']]];
        if($MobileEndItem['MobileEndSecondLineRightColor'] == null) {
            $MobileEndItem['MobileEndSecondLineRightColor'] = "primary";
        }

        /*
        $MobileEndItem['MobileEndSecondLineRightColor']  = $SettingMap['MobileEndSecondLineRightColor'];
        if($FieldName == $SettingMap['MobileEndWhenField1'] && $SettingMap['MobileEndWhenFieldIsEqual1'] == $FieldValue) {
            $MobileEndItem['MobileEndSecondLineRightColor'] = $SettingMap['MobileEndWhenFieldShowColor1'];
        }
        if($FieldName == $SettingMap['MobileEndWhenField2'] && $SettingMap['MobileEndWhenFieldIsEqual2'] == $FieldValue) {
            $MobileEndItem['MobileEndSecondLineRightColor'] = $SettingMap['MobileEndWhenFieldShowColor2'];
        }
        */

        $MobileEndItem['MobileEndIconImage']        = "/images/wechatIcon/".$SettingMap['MobileEndIconImage'].".png";
        //print_R($SettingMap);exit;
    }
    $MobileEndItem['MobileEndSecondLineLeftColorField']     = $Line[$MobileEndItem['MobileEndSecondLineLeftColorField']];
    $MobileEndItem['MobileEndSecondLineRightColorField']    = $Line[$MobileEndItem['MobileEndSecondLineRightColorField']];
    if($SettingMap['MobileEndField1']!=""&&$SettingMap['MobileEndField1']!="Disabled")  {
        $MobileEndItem['MobileEndField1Name']     = $SettingMap['MobileEndField1'];
        $MobileEndItem['MobileEndField1Value']    = $Line[$SettingMap['MobileEndField1']];
        $MobileEndItem['MobileEndField1Colspan']  = 2;
    }
    if($SettingMap['MobileEndField2']!=""&&$SettingMap['MobileEndField2']!="Disabled")  {
        $MobileEndItem['MobileEndField2Name']     = $SettingMap['MobileEndField2'];
        $MobileEndItem['MobileEndField2Value']    = $Line[$SettingMap['MobileEndField2']];
        $MobileEndItem['MobileEndField2Colspan']  = 2;
    }
    if($SettingMap['MobileEndField3']!=""&&$SettingMap['MobileEndField3']!="Disabled")  {
        $MobileEndItem['MobileEndField3Name']     = $SettingMap['MobileEndField3'];
        $MobileEndItem['MobileEndField3Value']    = $Line[$SettingMap['MobileEndField3']];
        $MobileEndItem['MobileEndField3Colspan']  = 2;
    }
    if($SettingMap['MobileEndField4']!=""&&$SettingMap['MobileEndField4']!="Disabled")  {
        $MobileEndItem['MobileEndField4Name']     = $SettingMap['MobileEndField4'];
        $MobileEndItem['MobileEndField4Value']    = $Line[$SettingMap['MobileEndField4']];
        $MobileEndItem['MobileEndField4Colspan']  = 2;
    }
    if($SettingMap['MobileEndField5']!=""&&$SettingMap['MobileEndField5']!="Disabled")  {
        $MobileEndItem['MobileEndField5Name']     = $SettingMap['MobileEndField5'];
        $MobileEndItem['MobileEndField5Value']    = $Line[$SettingMap['MobileEndField5']];
        $MobileEndItem['MobileEndField5Colspan']  = 2;
    }
    if($SettingMap['MobileEndField6']!=""&&$SettingMap['MobileEndField6']!="Disabled")  {
        $MobileEndItem['MobileEndField6Name']     = $SettingMap['MobileEndField6'];
        $MobileEndItem['MobileEndField6Value']    = $Line[$SettingMap['MobileEndField6']];
        $MobileEndItem['MobileEndField6Colspan']  = 2;
    }
    if($SettingMap['MobileEndField7']!=""&&$SettingMap['MobileEndField7']!="Disabled")  {
        $MobileEndItem['MobileEndField7Name']     = $SettingMap['MobileEndField7'];
        $MobileEndItem['MobileEndField7Value']    = $Line[$SettingMap['MobileEndField7']];
        $MobileEndItem['MobileEndField7Colspan']  = 2;
    }
    if($SettingMap['MobileEndField8']!=""&&$SettingMap['MobileEndField8']!="Disabled")  {
        $MobileEndItem['MobileEndField8Name']     = $SettingMap['MobileEndField8'];
        $MobileEndItem['MobileEndField8Value']    = $Line[$SettingMap['MobileEndField8']];
        $MobileEndItem['MobileEndField8Colspan']  = 2;
    }
    if($SettingMap['MobileEndField9']!=""&&$SettingMap['MobileEndField9']!="Disabled")  {
        $MobileEndItem['MobileEndField9Name']     = $SettingMap['MobileEndField9'];
        $MobileEndItem['MobileEndField9Value']    = $Line[$SettingMap['MobileEndField9']];
        $MobileEndItem['MobileEndField9Colspan']  = 2;
    }

    //LimitEditAndDelete
    if($SettingMap['LimitEditAndDelete_Edit_Field_One']!="" && $SettingMap['LimitEditAndDelete_Edit_Field_One']!="None" && in_array($SettingMap['LimitEditAndDelete_Edit_Field_One'], $MetaColumnNames)) {
        $LimitEditAndDelete_Edit_Value_One_Array = explode(',',$SettingMap['LimitEditAndDelete_Edit_Value_One']);
        if(in_array($Line[$SettingMap['LimitEditAndDelete_Edit_Field_One']],$LimitEditAndDelete_Edit_Value_One_Array)) {
            $ForbiddenEditRow[$Line['id']] = $Line['id'];
            $ForbiddenSelectRow[$Line['id']] = $Line['id'];
            $ForbiddenEditRowOriginal[$OriginalID] = $OriginalID;
            $ForbiddenSelectRowOriginal[$OriginalID] = $OriginalID;
        }
    }
    if($SettingMap['LimitEditAndDelete_Edit_Field_Two']!="" && $SettingMap['LimitEditAndDelete_Edit_Field_Two']!="None" && in_array($SettingMap['LimitEditAndDelete_Edit_Field_Two'], $MetaColumnNames)) {
        $LimitEditAndDelete_Edit_Value_Two_Array = explode(',',$SettingMap['LimitEditAndDelete_Edit_Value_Two']);
        if(in_array($Line[$SettingMap['LimitEditAndDelete_Edit_Field_Two']],$LimitEditAndDelete_Edit_Value_Two_Array)) {
            $ForbiddenEditRow[$Line['id']] = $Line['id'];
            $ForbiddenSelectRow[$Line['id']] = $Line['id'];
            $ForbiddenEditRowOriginal[$OriginalID] = $OriginalID;
            $ForbiddenSelectRowOriginal[$OriginalID] = $OriginalID;
        }
    }
    if($SettingMap['LimitEditAndDelete_Delete_Field_One']!="" && $SettingMap['LimitEditAndDelete_Delete_Field_One']!="None" && in_array($SettingMap['LimitEditAndDelete_Delete_Field_One'], $MetaColumnNames)) {
        $LimitEditAndDelete_Delete_Value_One_Array = explode(',',$SettingMap['LimitEditAndDelete_Delete_Value_One']);
        if(in_array($Line[$SettingMap['LimitEditAndDelete_Delete_Field_One']],$LimitEditAndDelete_Delete_Value_One_Array)) {
            $ForbiddenDeleteRow[$Line['id']] = $Line['id'];
            $ForbiddenSelectRow[$Line['id']] = $Line['id'];
            $ForbiddenDeleteRowOriginal[$OriginalID] = $OriginalID;
            $ForbiddenSelectRowOriginal[$OriginalID] = $OriginalID;
        }
    }
    if($SettingMap['LimitEditAndDelete_Delete_Field_Two']!="" && $SettingMap['LimitEditAndDelete_Delete_Field_Two']!="None" && in_array($SettingMap['LimitEditAndDelete_Delete_Field_Two'], $MetaColumnNames)) {
        $LimitEditAndDelete_Delete_Value_Two_Array = explode(',',$SettingMap['LimitEditAndDelete_Delete_Value_Two']);
        if(in_array($Line[$SettingMap['LimitEditAndDelete_Delete_Field_Two']],$LimitEditAndDelete_Delete_Value_Two_Array)) {
            $ForbiddenDeleteRow[$Line['id']] = $Line['id'];
            $ForbiddenSelectRow[$Line['id']] = $Line['id'];
            $ForbiddenDeleteRowOriginal[$OriginalID] = $OriginalID;
            $ForbiddenSelectRowOriginal[$OriginalID] = $OriginalID;
        }
    }
    $NewRSA[] = $Line;
    if(in_array($Line['TableName'],['data_user','data_department','data_role','form_formfield'])) {
        $ForbiddenSelectRow[$Line['id']] = $Line['id'];
        //$ForbiddenViewRow[$Line['id']] = $Line['id'];
        //$ForbiddenEditRow[$Line['id']] = $Line['id'];
        $ForbiddenDeleteRow[$Line['id']] = $Line['id'];
        $ForbiddenDeleteRowOriginal[$OriginalID] = $OriginalID;
        $ForbiddenSelectRowOriginal[$OriginalID] = $OriginalID;
    }
    if($ForbiddenEditRow[$Line['id']]=="" && in_array('Edit',$Actions_In_List_Row_Array)) {
        $MobileEndItem['EditUrl']   = "?action=edit_default&pageid=$page&id=".$Line['id'];
    }
    if($ForbiddenDeleteRow[$Line['id']]=="" && in_array('Delete',$Actions_In_List_Row_Array)) {
        $MobileEndItem['DeleteUrl'] = "?action=delete_array&pageid=$page";
    }
    if($ForbiddenViewRow[$Line['id']]=="" && in_array('View',$Actions_In_List_Row_Array)) {
        $MobileEndItem['ViewUrl']   = "?action=view_default&pageid=$page&id=".$Line['id'];
    }
    $MobileEndItem['EditIcon']  = "mdi:pencil-outline";
    $MobileEndItem['PageId']    = $page;
    $MobileEndItem['Id']        = $Line['id'];
    $MobileEndItem['Id2']       = md5("Dandian_".$Line['id2']);
    $MobileEndItem['Template']  = "List";
    $functionNameIndividual = "plugin_".$TableName."_".$Step."_init_default_mobile_line_control";
    if(function_exists($functionNameIndividual))  {
      $MobileEndItem = $functionNameIndividual($MobileEndItem);
    }
    foreach($MobileEndItem as $MobileEndItemKey=>$MobileEndItemValue)  {
      if($MobileEndItemValue == "undefined")  {
        $MobileEndItem[$MobileEndItemKey] = "";
      }
    }
    $MobileEndData[] = $MobileEndItem;

}

// Add List Page Data Color Array
for($i=0;$i<sizeof($init_default_columns);$i++)    {
    $Item = $init_default_columns[$i];
    if($Item['type']=="radiogroupcolor" && is_array($FieldDataColorValue[$Item['field']]))   {
        $FieldItemAll = @array_keys(@$FieldDataColorValue[$Item['field']]);
        $Color = ArrayToColorStyle1($FieldItemAll);
        $init_default_columns[$i]['color'] = $Color;
        //print_R($init_default_columns[$i]);
    }
    elseif($Item['type']=="tablefiltercolor" && is_array($FieldDataColorValue[$Item['field']]))   {
        $FieldItemAll = @array_keys(@$FieldDataColorValue[$Item['field']]);
        $Color = ArrayToColorStyle2($FieldItemAll);
        $init_default_columns[$i]['color'] = $Color;
        //print_R($init_default_columns[$i]);
    }
}

$RS['init_default']['button_search']    = __("Search");
$RS['init_default']['button_add']       = $SettingMap['Rename_List_Add_Button'];
$RS['init_default']['button_import']    = $SettingMap['Rename_List_Import_Button']?$SettingMap['Rename_List_Import_Button']:__("Import");
$RS['init_default']['button_export']    = $SettingMap['Rename_List_Export_Button']?$SettingMap['Rename_List_Export_Button']:__("Export");
$RS['init_default']['columns']          = $init_default_columns;
$RS['init_default']['columnsactions']   = $columnsactions;

if($SettingMap['OperationLogGrade']=="AllOperation")  {
    SystemLogRecord("init_default", $BeforeRecord='', $AfterRecord='');
}

$RS['init_default']['data']                     = $NewRSA;
$RS['init_default']['MobileEndData']            = $MobileEndData;
$RS['init_default']['MobileEndShowType']        = $SettingMap['MobileEndShowType'];
$RS['init_default']['MobileEndShowSearch']      = $SettingMap['MobileEndShowSearch'];
$RS['init_default']['MobileEndShowGroupFilter'] = $SettingMap['MobileEndShowGroupFilter'];
$RS['init_default']['MainImageList']	        = array("/images/wechat/logo_18.png","/images/wechat/logo_icampus.png");

$RS['init_default']['ForbiddenSelectRow']   = array_keys($ForbiddenSelectRow);
$RS['init_default']['ForbiddenViewRow']     = array_keys($ForbiddenViewRow);
$RS['init_default']['ForbiddenEditRow']     = array_keys($ForbiddenEditRow);
$RS['init_default']['ForbiddenDeleteRow']   = array_keys($ForbiddenDeleteRow);

if($SettingMap['Init_Action_Value'] == "") {
    $RS['init_action']['action']      = "init_default";
    $RS['init_action']['actionValue'] = "";
}
else if($SettingMap['Init_Action_Value'] == "SoulChatList")  {
  $RS['init_action']['action']        = "init_default";
  $RS['init_action']['actionValue']   = "SoulChatList";
}
else if($SettingMap['Init_Action_Value'] == "AiChatList")  {
  $RS['init_action']['action']        = "init_default";
  $RS['init_action']['actionValue']   = "AiChatList";
}
else if($SettingMap['Init_Action_Value'] == "AiQuestionList")  {
  $RS['init_action']['action']        = "init_default";
  $RS['init_action']['actionValue']   = "AiQuestionList";
}
else {
  $RS['init_action']['actionValue']   = "";
  $RS['init_action']['action']        = $SettingMap['Init_Action_Value'];
}
$RS['init_action']['id']                            = EncryptID($GetAllIDList[0]); //USE THIS VALUE IN EDIT_DEFAULT SINGLE RECORD
$RS['init_action']['IsGetStructureFromEditDefault'] = 0;

$currentUrlAccessFileName                           = basename($_SERVER['PHP_SELF']);
$currentUrlAccessFileName                           = str_replace("apps_", "", $currentUrlAccessFileName);
$currentUrlAccessFileName                           = str_replace(".php", "", $currentUrlAccessFileName);
$RS['init_action']['AppId']                         = EncryptID(intval($currentUrlAccessFileName));

$CSRF_DATA                          = [];
$CSRF_DATA['GetAllIDList']          = $GetAllIDList;
$CSRF_DATA['ForbiddenSelectRow']    = $ForbiddenSelectRowOriginal;
$CSRF_DATA['ForbiddenViewRow']      = $ForbiddenViewRowOriginal;
$CSRF_DATA['ForbiddenEditRow']      = $ForbiddenEditRowOriginal;
$CSRF_DATA['ForbiddenDeleteRow']    = $ForbiddenDeleteRowOriginal;
$CSRF_DATA['UpdateFields']          = $UpdateFields;
$CSRF_DATA['Actions_In_List_Row_Array'] = $Actions_In_List_Row_Array;
$CSRF_DATA['Bottom_Button_Actions_Array'] = explode(',',$SettingMap['Bottom_Button_Actions']);
$CSRF_DATA['Time']                  = time();
$RS['init_default']['CSRF_TOKEN']   = EncryptID(serialize($CSRF_DATA));
$RS['init_default']['CSRF_DATA']    = $CSRF_DATA;

$RS['init_default']['params']   = ['FormGroup' => '', 'role' => '', 'status' => '', 'q' => ''];

$RS['init_default']['rowdelete']    = [];
$RS['init_default']['rowdelete'][]  = ["text"=>$SettingMap['Tip_Title_When_Delete'],"action"=>"delete_array","title"=>$SettingMap['Tip_Title_When_Delete'],"content"=>$SettingMap['Tip_Content_When_Delete'],"memoname"=>"","inputmust"=>false,"inputmusttip"=>"","submit"=>$SettingMap['Tip_Button_When_Delete'],"cancel"=>__("Cancel")];

//MultiReview
$multireview = [];
$Bottom_Button_Actions_Array = explode(',',$SettingMap['Bottom_Button_Actions']);
$multireview['input']['placeholder'] = __("Review Opinion");
if(in_array('Delete',$Bottom_Button_Actions_Array))   {
    $multireview['multireview'][] = ["text"=>__("Delete Selected"),"action"=>"delete_array","title"=>__("Delete multi items one time"),"content"=>__("Do you really want to delete this item? This operation will delete table and data in Database."),"memoname"=>"","inputmust"=>false,"inputmusttip"=>"","submit"=>__("Submit"),"cancel"=>__("Cancel")];
}
if(in_array('Batch_Approval',$Bottom_Button_Actions_Array))   {
    $multireview['multireview'][] = ["text"=>__("Multi Approval"),"action"=>"option_multi_approval","title"=>__("Approval multi items one time"),"content"=>__("Do you really want to approval multi items at this time?"),"memoname"=>$SettingMap['Batch_Approval_Review_Field'],"inputmust"=>$SettingMap['Batch_Approval_Review_Field']?true:false,"inputmusttip"=>__("Opinion must input"),"submit"=>__("Submit"),"cancel"=>__("Cancel")];
}
if(in_array('Batch_Cancel',$Bottom_Button_Actions_Array))   {
    $multireview['multireview'][] = ["text"=>__("Multi Cancel"),"action"=>"option_multi_cancel","title"=>__("Cancel multi items one time"),"content"=>__("Do you really want to cancel multi items at this time?"),"memoname"=>$SettingMap['Batch_Approval_Review_Field'],"inputmust"=>$SettingMap['Batch_Approval_Review_Field']?true:false,"inputmusttip"=>__("Opinion must input"),"submit"=>__("Submit"),"cancel"=>__("Cancel")];
}
if(in_array('Batch_Reject',$Bottom_Button_Actions_Array))   {
    $multireview['multireview'][] = ["text"=>__("Multi Refuse"),"action"=>"option_multi_refuse","title"=>__("Refuse multi items one time"),"content"=>__("Do you really want to approval multi items at this time?"),"memoname"=>$SettingMap['Batch_Approval_Review_Field'],"inputmust"=>$SettingMap['Batch_Approval_Review_Field']?true:false,"inputmusttip"=>__("Opinion must input"),"submit"=>__("Submit"),"cancel"=>__("Cancel")];
}
if(in_array('Reset_Password_123654',$Bottom_Button_Actions_Array))   {
    $multireview['multireview'][] = ["text"=>__("Reset_Password_123654"),"action"=>"Reset_Password_123654","title"=>__("Modify user passwords in batches"),"content"=>__("Modify the password of the selected record at one time to 123654"),"memoname"=>"","inputmust"=>false,"inputmusttip"=>"","submit"=>__("Submit"),"cancel"=>__("Cancel")];
}
if(in_array('Reset_Password_ID_Last6',$Bottom_Button_Actions_Array))   {
    $multireview['multireview'][] = ["text"=>__("Reset_Password_ID_Last6"),"action"=>"Reset_Password_ID_Last6","title"=>__("Modify user passwords in batches"),"content"=>__("Modify the password of the selected record to the last six digits of the ID number, if no ID number is set, the password is 123654"),"memoname"=>"","inputmust"=>false,"inputmusttip"=>"","submit"=>__("Submit"),"cancel"=>__("Cancel")];
}
if(in_array('Batch_Setting_One',$Bottom_Button_Actions_Array))   {
    $multireview['multireview'][] = ["text"=>$SettingMap["Batch_Setting_One_Name"],"action"=>"option_multi_setting_one","title"=>__("Change multiple item values one time"),"content"=>__("Do you really want to change multiple item values at this time?")."\n批量把[".$SettingMap['Batch_Setting_Two_Change_Field']."]列修改为:".$SettingMap['Batch_Setting_Two_Change_Value']."","memoname"=>$SettingMap['Batch_Approval_Review_Field'],"inputmust"=>$SettingMap['Batch_Approval_Review_Field']?true:false,"inputmusttip"=>__("Opinion must input"),"submit"=>__("Submit"),"cancel"=>__("Cancel")];
}
if(in_array('Batch_Setting_Two',$Bottom_Button_Actions_Array))   {
    $multireview['multireview'][] = ["text"=>$SettingMap["Batch_Setting_Two_Name"],"action"=>"option_multi_setting_two","title"=>__("Change multiple item values one time"),"content"=>__("Do you really want to change multiple item values at this time?")."\n批量把[".$SettingMap['Batch_Setting_Two_Change_Field']."]列修改为:".$SettingMap['Batch_Setting_Two_Change_Value']."","memoname"=>$SettingMap['Batch_Approval_Review_Field'],"inputmust"=>$SettingMap['Batch_Approval_Review_Field']?true:false,"inputmusttip"=>__("Opinion must input"),"submit"=>__("Submit"),"cancel"=>__("Cancel")];
}
$multireview['Bottom_Button_Actions_Array'] = $Bottom_Button_Actions_Array;
//$multireview['multireview'][] = ["text"=>"Multi Change Status","action"=>"option_multi_change_status","title"=>"option_multi_change_status Item","content"=>"Do you really to delete this item?Do you really to delete this item?","memoname"=>"审核意见3","inputmust"=>false,"inputmusttip"=>"","submit"=>"Submit","cancel"=>__("Cancel")];
$RS['init_default']['multireview'] = $multireview;
$RS['init_default']['checkboxSelection']  = is_array($multireview['multireview']) && count($multireview['multireview'])>0 ? true : false;

$RS['import_default']['allFields']        = $allFieldsImport;
$RS['import_default']['allFieldsMode']    = [['value'=>"Default", 'label'=>__("")]];
$RS['import_default']['defaultValues']    = $defaultValuesImport;
$RS['import_default']['dialogContentHeight']  = "90%";
$RS['import_default']['submitaction']     = "import_default_data";
$RS['import_default']['componentsize']    = "small";
$RS['import_default']['submittext']       = $SettingMap['Rename_Import_Submit_Button'];
$RS['import_default']['canceltext']       = __("Cancel");
$RS['import_default']['titletext']        = $SettingMap['Import_Title_Name'];
$RS['import_default']['titlememo']        = $SettingMap['Import_Subtitle_Name'];
$RS['import_default']['tablewidth']       = 650;
$RS['import_default']['submitloading']    = __("SubmitLoading");
$RS['import_default']['loading']          = __("Loading");
$RS['import_default']['ImportLoading']    = __("ImportLoading");


$TEMPARRAY                      = [];
$TEMPARRAY['TableName']         = $TableName;
$TEMPARRAY['Action']            = "export_data";
$TEMPARRAY['FormId']            = $FormId;
$TEMPARRAY['FlowId']            = $FlowId;
$TEMPARRAY['FileName']          = $FormName;
$TEMPARRAY['AddSql']            = $AddSql;
$TEMPARRAY['orderby']           = $orderby;
$TEMPARRAY['Time']              = time();
$DATATEMP                       = EncryptID(serialize($TEMPARRAY));
$exportUrl                      = "data_export_json.php?DATA=".$DATATEMP;
$RS['export_default']['allFields']        = $allFieldsExport;
$RS['export_default']['allFieldsMode']    = [['value'=>"Default", 'label'=>__("")]];
$RS['export_default']['defaultValues']    = [];
$RS['export_default']['dialogContentHeight']  = "90%";
$RS['export_default']['submitaction']     = "export_default_data";
$RS['export_default']['componentsize']    = "small";
$RS['export_default']['submittext']       = $SettingMap['Rename_Export_Submit_Button'];
$RS['export_default']['canceltext']       = __("Cancel");
$RS['export_default']['titletext']        = $SettingMap['Export_Title_Name'];
$RS['export_default']['titlememo']        = $SettingMap['Export_Subtitle_Name'];
$RS['export_default']['tablewidth']       = 650;
$RS['export_default']['submitloading']    = __("SubmitLoading");
$RS['export_default']['ExportLoading']    = __("ExportLoading");
$RS['export_default']['loading']          = __("Loading");
if(sizeof(array_keys($allFieldsExport))>0 && in_array('Export',$Actions_In_List_Header_Array)) {
    $RS['export_default']['exportUrl']        = $exportUrl;
}

$RS['add_default']['allFields']     = $allFieldsAdd;
$RS['add_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['add_default']['defaultValues'] = $defaultValuesAdd;
$RS['add_default']['dialogContentHeight']  = "90%";
$RS['add_default']['submitaction']  = "add_default_data";
$RS['add_default']['componentsize'] = "small";
$RS['add_default']['submittext']    = $SettingMap['Rename_Add_Submit_Button'];
$RS['add_default']['canceltext']    = __("Cancel");
$RS['add_default']['titletext']     = $SettingMap['Add_Title_Name'];
$RS['add_default']['titlememo']     = $SettingMap['Add_Subtitle_Name'];
$RS['add_default']['tablewidth']    = 650;
$RS['add_default']['submitloading'] = __("SubmitLoading");
$RS['add_default']['loading']       = __("Loading");

$RS['edit_default']['allFields']        = $allFieldsEdit;
$RS['edit_default']['allFieldsMode']    = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default']['defaultValues']    = $defaultValuesEdit;
$RS['edit_default']['dialogContentHeight']  = "90%";
$RS['edit_default']['submitaction']     = "edit_default_data";
$RS['edit_default']['componentsize']    = "small";
$RS['edit_default']['submittext']       = $SettingMap['Rename_Edit_Submit_Button'];
$RS['edit_default']['canceltext']       = __("Cancel");
$RS['edit_default']['titletext']        = $SettingMap['Edit_Title_Name'];
$RS['edit_default']['titlememo']        = $SettingMap['Edit_Subtitle_Name'];
$RS['edit_default']['tablewidth']       = 650;
$RS['edit_default']['submitloading']    = __("SubmitLoading");
$RS['edit_default']['loading']          = __("Loading");

$RS['view_default']               = $RS['add_default'];
$RS['view_default']['allFields']  = $allFieldsView;
$RS['view_default']['titletext']  = $SettingMap['View_Title_Name'];
$RS['view_default']['titlememo']  = $SettingMap['View_Subtitle_Name'];
$RS['view_default']['componentsize'] = "small";

//Relative Child Table Support
$Relative_Child_Table                   = $SettingMap['Relative_Child_Table'];
$Relative_Child_Table_Field_Name        = $SettingMap['Relative_Child_Table_Field_Name'];
$Relative_Child_Table_Parent_Field_Name = $SettingMap['Relative_Child_Table_Parent_Field_Name'];
if($Relative_Child_Table>0 && $Relative_Child_Table_Parent_Field_Name!="" && in_array($Relative_Child_Table_Parent_Field_Name,$MetaColumnNames)) {
    $ChildSettingMap = returntablefield("form_formflow",'id',$Relative_Child_Table,'Setting')['Setting'];
    $ChildSettingMap = unserialize(base64_decode($ChildSettingMap));
    $ChildFormId                = returntablefield("form_formflow",'id',$Relative_Child_Table,'FormId')['FormId'];
    $ChildTableName             = returntablefield("form_formname",'id',$ChildFormId,'TableName')['TableName'];
    $ChildMetaColumnNames       = GLOBAL_MetaColumnNames($ChildTableName);
    if($Relative_Child_Table_Field_Name!="" && in_array($Relative_Child_Table_Field_Name, $ChildMetaColumnNames) ) {
        //Get All Fields
        $sql                        = "select * from form_formfield where FormId='$ChildFormId' and IsEnable='1' order by SortNumber asc, id asc";
        $rs                         = $db->Execute($sql);
        $ChildAllFieldsFromTable    = $rs->GetArray();
        $ChildAllFieldsMap = [];
        foreach($ChildAllFieldsFromTable as $Item)  {
            $ChildAllFieldsMap[$Item['FieldName']] = $Item;
            $ChildLocaleFieldArray[$Item['EnglishName']] = $Item['FieldName'];
            $ChildLocaleFieldArray[$Item['ChineseName']] = $Item['FieldName'];
        }
        $defaultValuesAddChild  = [];
        $defaultValuesEditChild = [];
        $allFieldsAdd   = getAllFields($ChildAllFieldsFromTable, $AllShowTypesArray, 'ADD', true, $ChildSettingMap);
        foreach($allFieldsAdd as $ModeName=>$allFieldItem) {
            foreach($allFieldItem as $ITEM) {
                $defaultValuesAddChild[$ITEM['name']] = $ITEM['value'];
                if($ITEM['code']!="") {
                    $defaultValuesAddChild[$ITEM['code']] = $ITEM['value'];
                }
            }
        }
        $RS['add_default']['childtable']['allFields']        = $allFieldsAdd;
        $RS['add_default']['childtable']['defaultValues']    = $defaultValuesAddChild;
        $RS['add_default']['childtable']['submittext']       = __("NewItem");
        $RS['add_default']['childtable']['Add']                = strpos($ChildSettingMap['Actions_In_List_Header'],'Add')===false?false:true;
        $RS['add_default']['childtable']['Edit']               = strpos($ChildSettingMap['Actions_In_List_Row'],'Edit')===false?false:true;
        $RS['add_default']['childtable']['Delete']             = strpos($ChildSettingMap['Actions_In_List_Row'],'Delete')===false?false:true;

        $allFieldsEdit   = getAllFields($ChildAllFieldsFromTable, $AllShowTypesArray, 'EDIT', true, $ChildSettingMap);
        foreach($allFieldsEdit as $ModeName=>$allFieldItem) {
            $allFieldItemIndex = 0;
            foreach($allFieldItem as $ITEM) {
                $defaultValuesEditChild[$ITEM['name']] = $ITEM['value'];
                if($ITEM['code']!="") {
                    $defaultValuesEditChild[$ITEM['code']] = $ITEM['value'];
                }
                if(strpos($ChildSettingMap['Actions_In_List_Row'],'Edit')===false) {
                    $allFieldsEdit[$ModeName][$allFieldItemIndex]['rules']['disabled'] = true;
                }
                $allFieldItemIndex ++;
            }
        }
        if(is_array($ChildSettingMap))   {
            foreach($ChildSettingMap as $ModeName=>$allFieldItem) {
                $defaultValuesEditChild[$ModeName] = $allFieldItem;
            }
        }
        $RS['edit_default']['childtable']['allFields']          = $allFieldsEdit;
        $RS['edit_default']['childtable']['defaultValues']      = $defaultValuesEditChild;
        $RS['edit_default']['childtable']['submittext']         = __("NewItem");
        $RS['edit_default']['childtable']['Add']                = strpos($ChildSettingMap['Actions_In_List_Header'],'Add')===false?false:true;
        $RS['edit_default']['childtable']['Edit']               = strpos($ChildSettingMap['Actions_In_List_Row'],'Edit')===false?false:true;
        $RS['edit_default']['childtable']['Delete']             = strpos($ChildSettingMap['Actions_In_List_Row'],'Delete')===false?false:true;

    }
}

$RS['init_default']['delete_dialog_title']      = $SettingMap['Tip_Title_When_Delete'];
$RS['init_default']['delete_dialog_content']    = $SettingMap['Tip_Content_When_Delete'];
$RS['init_default']['delete_dialog_button']     = $SettingMap['Tip_Button_When_Delete'];

$RS['init_default']['rowHeight']        = $rowHeight;
$RS['init_default']['dialogContentHeight']  = "90%";
$RS['init_default']['dialogMaxWidth']   = $SettingMap['Init_Action_AddEditWidth']?$SettingMap['Init_Action_AddEditWidth']:'md';// xl lg md sm xs
$RS['init_default']['timeline']         = time();
$RS['init_default']['pageNumber']       = $pageSize;
$RS['init_default']['pageCount']        = ceil($RS['init_default']['total']/$pageSize);
$RS['init_default']['pageId']           = $page;
$RS['init_default']['pageNumberArray']  = $pageNumberArray;
if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  {
    $RS['init_default']['sql']                              = $sqlList;
    $RS['init_default']['ApprovalNodeFields']['DebugSql']   = $sqlList;
}
$RS['init_default']['ApprovalNodeFields']['Memo']           = $SettingMap['Init_Action_Memo'];


if($SettingMap['Init_Action_Value']=="edit_default_configsetting")   {
    //Get All Fields
    $sql                    = "select * from form_configsetting where FlowId='$FlowId' and IsEnable='1' order by SortNumber asc, id asc";
    $rs                     = $db->Execute($sql);
    $AllFieldsFromTable     = $rs->GetArray();
    $defaultValuesEdit      = [];
    $allFieldsEdit          = getAllFields($AllFieldsFromTable, $AllShowTypesArray, 'EDIT', $FilterFlowSetting=false, $SettingMap);
    foreach($allFieldsEdit as $ModeName=>$allFieldItem) {
        foreach($allFieldItem as $ITEM) {
            $defaultValuesEdit[$ITEM['name']] = $ITEM['value'];
        }
    }
    $ConfigSettingMap = returntablefield("form_formflow",'id',$FlowId,'ConfigSetting',$cache=0)['ConfigSetting'];
    $ConfigSettingMap = unserialize(base64_decode($ConfigSettingMap));
    if(is_array($ConfigSettingMap))   {
        foreach($ConfigSettingMap as $ModeName=>$allFieldItem) {
            $defaultValuesEdit[$ModeName] = $allFieldItem;
        }
    }
    //print_R($AllShowTypesArray);
    //print $sql;
    $RS['edit_default_configsetting']['ConfigSettingMap'] = $ConfigSettingMap;
    $RS['edit_default_configsetting']['allFields']        = $allFieldsEdit;
    $RS['edit_default_configsetting']['allFieldsMode']    = [['value'=>"Default", 'label'=>__("")]];
    $RS['edit_default_configsetting']['defaultValues']    = $defaultValuesEdit;
    $RS['edit_default_configsetting']['dialogContentHeight']  = "90%";
    $RS['edit_default_configsetting']['submitaction']     = "edit_default_configsetting_data";
    $RS['edit_default_configsetting']['componentsize']    = "small";
    $RS['edit_default_configsetting']['submittext']       = $SettingMap['Rename_Edit_Submit_Button'];
    $RS['edit_default_configsetting']['canceltext']       = __("Cancel");
    $RS['edit_default_configsetting']['titletext']        = $SettingMap['Edit_Title_Name'];
    $RS['edit_default_configsetting']['titlememo']        = $SettingMap['Edit_Subtitle_Name'];
    $RS['edit_default_configsetting']['tablewidth']       = 650;
    $RS['edit_default_configsetting']['submitloading']    = __("SubmitLoading");
    $RS['edit_default_configsetting']['loading']          = __("Loading");
}


if(sizeof($MetaColumnNames)>=5) {
    $pinnedColumnsLeft = [];
    $pinnedColumnsRight = [];
    if($SettingMap['Columns_Pinned_Left_Field_One']!="" && $SettingMap['Columns_Pinned_Left_Field_One']!="Disabled") {
        $pinnedColumnsLeft[$SettingMap['Columns_Pinned_Left_Field_One']] = $SettingMap['Columns_Pinned_Left_Field_One'];
    }
    if($SettingMap['Columns_Pinned_Left_Field_Two']!="" && $SettingMap['Columns_Pinned_Left_Field_Two']!="Disabled") {
        $pinnedColumnsLeft[$SettingMap['Columns_Pinned_Left_Field_Two']] = $SettingMap['Columns_Pinned_Left_Field_Two'];
    }
    if($SettingMap['Columns_Pinned_Left_Field_Three']!="" && $SettingMap['Columns_Pinned_Left_Field_Three']!="Disabled") {
        $pinnedColumnsLeft[$SettingMap['Columns_Pinned_Left_Field_Three']] = $SettingMap['Columns_Pinned_Left_Field_Three'];
    }
    if($SettingMap['Columns_Pinned_Left_Field_Four']!="" && $SettingMap['Columns_Pinned_Left_Field_Four']!="Disabled") {
        $pinnedColumnsLeft[$SettingMap['Columns_Pinned_Left_Field_Four']] = $SettingMap['Columns_Pinned_Left_Field_Four'];
    }
    if($SettingMap['Columns_Pinned_Right_Field_One']!="" && $SettingMap['Columns_Pinned_Right_Field_One']!="Disabled") {
        $pinnedColumnsRight[$SettingMap['Columns_Pinned_Right_Field_One']] = $SettingMap['Columns_Pinned_Right_Field_One'];
    }
    if($SettingMap['Columns_Pinned_Right_Field_Two']!="" && $SettingMap['Columns_Pinned_Right_Field_Two']!="Disabled") {
        $pinnedColumnsRight[$SettingMap['Columns_Pinned_Right_Field_Two']] = $SettingMap['Columns_Pinned_Right_Field_Two'];
    }
    if($SettingMap['Columns_Pinned_Right_Field_Three']!="" && $SettingMap['Columns_Pinned_Right_Field_Three']!="Disabled") {
        $pinnedColumnsRight[$SettingMap['Columns_Pinned_Right_Field_Three']] = $SettingMap['Columns_Pinned_Right_Field_Three'];
    }
    if($SettingMap['Columns_Pinned_Right_Field_Four']!="" && $SettingMap['Columns_Pinned_Right_Field_Four']!="Disabled") {
        $pinnedColumnsRight[$SettingMap['Columns_Pinned_Right_Field_Four']] = $SettingMap['Columns_Pinned_Right_Field_Four'];
    }
    $pinnedColumnsLeft  = array_keys($pinnedColumnsLeft);
    $pinnedColumnsRight = array_keys($pinnedColumnsRight);
    $pinnedColumns = ['left'=>$pinnedColumnsLeft,'right'=>$pinnedColumnsRight];
}
else {
    $pinnedColumns = ['left'=>[],'right'=>[]];
}
$RS['init_default']['pinnedColumns']  = $pinnedColumns;

$RS['init_default']['dataGridLanguageCode']  = $GLOBAL_LANGUAGE;

//Check Add Action In List Header
if(!in_array('Import',$Actions_In_List_Header_Array))  {
    $RS['import_default'] = [];
}
if(!in_array('Add',$Actions_In_List_Header_Array))  {
    $RS['add_default'] = [];
}
if(!in_array('Edit',$Actions_In_List_Row_Array))  {
    $RS['edit_default'] = [];
}

$RS['_GET']     = $_GET;
$RS['_POST']    = $_POST;

//functionNameIndividual
$functionNameIndividual = "plugin_".$TableName."_".$Step."_init_default_filter_RS";
if(function_exists($functionNameIndividual))  {
    $RS = $functionNameIndividual($RS);
}

print_R(EncryptApiData($RS, $GLOBAL_USER));
exit;



