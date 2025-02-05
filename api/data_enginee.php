<?php
header("Content-Type: application/json");
require_once('cors.php');
require_once('include.inc.php');

//$externalId = 16;

CheckAuthUserLoginStatus();

//Get Table Infor
$externalId = intval($externalId);
$sql        = "select * from form_formname where id='$externalId'";
$rs         = $db->Execute($sql);
$FromInfo   = $rs->fields;
$TableName  = $FromInfo['TableName'];

//Get form_formfield_showtype
$sql        = "select * from form_formfield_showtype";
$rs         = $db->Execute($sql);
$AllShowTypes   = $rs->GetArray();
$AllShowTypesArray = [];
foreach($AllShowTypes as $Item)  {
    $AllShowTypesArray[$Item['Name']] = $Item;
}

//Get All Fields
$sql        = "select * from form_formfield where FormId='$externalId' and IsEnable='1' order by SortNumber asc, id asc";
$rs         = $db->Execute($sql);
$AllFieldsFromTable   = $rs->GetArray();
$AllFieldsMap = [];
foreach($AllFieldsFromTable as $Item)  {
    $AllFieldsMap[$Item['FieldName']] = $Item;
}

$MetaColumnNames    = $db->MetaColumnNames($TableName);
$MetaColumnNames    = array_values($MetaColumnNames);
$UniqueKey          = $MetaColumnNames[1];

//新建页面时的启用字段列表
function getAllFields($AllFieldsFromTable, $AllShowTypesArray, $actionType)  {
    global $db;
    $allFieldsMap = [];
    foreach($AllFieldsFromTable as $Item)  {
        $FieldName      = $Item['FieldName'];
        $ShowType       = $Item['ShowType'];
        $IsSearch       = $Item['IsSearch'];
        $IsAdvSearch    = $Item['IsAdvSearch'];
        $FieldDefault   = $Item['FieldDefault'];
        $IsGroupFilter  = $Item['IsGroupFilter'];
        $IsHiddenGroupFilter    = $Item['IsHiddenGroupFilter'];
        $IsMustFill             = $Item['IsMustFill'];
        $IsFullWidth            = $Item['IsFullWidth'];
        $EnglishName   = $Item['EnglishName'];
        $Placeholder    = $Item['Placeholder'];
        $Helptext       = $Item['Helptext'];
        $Max            = intval($Item['Max']);
        $Min            = intval($Item['Min']);
        $Setting        = json_decode($Item['Setting'],true);
        $CurrentFieldType = $AllShowTypesArray[$ShowType][$actionType];
        $CurrentFieldTypeArray = explode(':',$CurrentFieldType);
        switch($CurrentFieldTypeArray[0])   {
            case 'input':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max]];
                break;
            case 'readonly':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => true,'min'=>$Min,'max'=>$Max]];
                break;
            case 'password':
            case 'EncryptField':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => true,'min'=>$Min,'max'=>$Max]];
                break;
            case 'confirmpassword':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => true,'min'=>$Min,'max'=>$Max]];
                break;
            case 'email':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false]];
                break;
            case 'url':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false]];
                break;
            case 'number':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max]];
                break;
            case 'date':
            case 'date1':
            case 'date2':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false],'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false, 'dateFormat' => 'yyyy-MM-dd','timeZone'=>'America/Los_Angeles','StartDate'=>$Setting['StartDate'],'EndDate'=>$Setting['EndDate']];
                break;
            case 'year':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false],'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false, 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles','StartYear'=>$Setting['StartYear'],'EndYear'=>$Setting['EndYear']];
                break;
            case 'yearrange':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false],'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false, 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
                break;
            case 'month':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false],'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false, 'dateFormat' => 'yyyy-MM','timeZone'=>'America/Los_Angeles','StartMonth'=>$Setting['StartMonth'],'EndMonth'=>$Setting['EndMonth']];
                break;
            case 'monthrange':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false],'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false, 'dateFormat' => 'yyyy-MM','timeZone'=>'America/Los_Angeles'];
                break;
            case 'quarter':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false],'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false, 'dateFormat' => 'yyyy-QQQ','timeZone'=>'America/Los_Angeles'];
                break;
            case 'datetime':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false],'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false, 'dateFormat' => 'yyyy-MM-dd HH:mm','timeZone'=>'America/Los_Angeles','StartDateTime'=>$Setting['StartDateTime'],'EndDateTime'=>$Setting['EndDateTime']];
                break;
            case 'time':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false],'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false, 'dateFormat' => 'HH:mm','timeZone'=>'America/Los_Angeles','StartTime'=>$Setting['StartTime'],'EndTime'=>$Setting['EndTime']];
                break;
            case 'textarea':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max],'TextareaRows'=>$Setting['TextareaRows']];
                break;
            case 'editor':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false]];
                break;
            case 'slider':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),  "min"=>$Min, "max"=>$Max, "step"=>5,"marks"=>[["value"=>0,"label"=>"0°"],["value"=>30,"label"=>"50°"],["value"=>50,"label"=>"50°"],["value"=>100,"label"=>"100°"] ]]];
                break;
            case 'Switch':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),  "min"=>$Min, "max"=>$Max, "step"=>5,"marks"=>[["value"=>0,"label"=>"0°"],["value"=>30,"label"=>"50°"],["value"=>50,"label"=>"50°"],["value"=>100,"label"=>"100°"] ]]];
                break;
            case 'select':
            case 'autocomplete':
                $sql = "select `FieldType` as value, `FieldType` as label from form_formfield_logictype order by SortNumber asc, id asc";
                $rs = $db->Execute($sql);
                $FieldType = $rs->GetArray();
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'options'=>$FieldType, 'label' => $EnglishName, 'value' => $FieldType[2]['value'], 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),'disabled' => false]];
                break;
            case 'radiogroup':
            case 'tablefilter':
            case 'radiogroupcolor':
            case 'tablefiltercolor':
                $TableNameTemp      = $CurrentFieldTypeArray[1];
                $KeyField           = $CurrentFieldTypeArray[2];
                $ValueField         = $CurrentFieldTypeArray[3];
                $DefaultValue       = $CurrentFieldTypeArray[4];
                $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);
                if(sizeof($CurrentFieldTypeArray)==7)   {
                    $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' order by SortNumber asc, `".$MetaColumnNamesTemp[$ValueField]."` asc";
                }
                elseif(sizeof($CurrentFieldTypeArray)==5)   {
                    $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp order by `".$MetaColumnNamesTemp[$ValueField]."` asc, id asc";
                }
                else {
                    break;
                }
                $rs = $db->Execute($sql) or print($sql);
                $FieldType = $rs->GetArray();
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'options'=>$FieldType, 'label' => $EnglishName, 'value' => $DefaultValue, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),'disabled' => false], 'sql'=>$sql, 'CurrentFieldTypeArray'=>$CurrentFieldTypeArray, 'MetaColumnNamesTemp'=>$MetaColumnNamesTemp];
                break;
            case 'avatar':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false]];
                break;
            case 'images':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false]];
                break;
            default:
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'label' => $EnglishName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max]];
                break;
        }
    }
    return $allFieldsMap;
}

$allFieldsAdd   = getAllFields($AllFieldsFromTable, $AllShowTypesArray, 'ADD');
foreach($allFieldsAdd as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValuesAdd[$ITEM['name']] = $ITEM['value'];
    }
}

$allFieldsEdit  = getAllFields($AllFieldsFromTable, $AllShowTypesArray, 'EDIT');
foreach($allFieldsEdit as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValuesEdit[$ITEM['name']] = $ITEM['value'];
    }
}


//编辑页面时的启用字段列表
if( ($_GET['action']=="add_default_data") && $TableName!="")  {

    $MetaColumns    = $db->MetaColumns($TableName);
    $MetaColumns    = array_values($MetaColumns);
    $MetaColumnsInDb = [];
    foreach($MetaColumns as $Item)  {
        $MetaColumnsInDb[$Item->name]       = $Item->type;
    }
    $MetaColumnNames    = $db->MetaColumnNames($TableName);
    $MetaColumnNames    = array_values($MetaColumnNames);
    $FieldsArray        = [];
    $IsExecutionSQL     = 0;
    foreach($AllFieldsFromTable as $Item)  {
        if($_POST[$Item['FieldName']]!="") {
            $IsExecutionSQL = 1;
        }
        // Give a default value for date and number
        $FieldType = $MetaColumnsInDb[$Item['FieldName']];
        if($_POST[$Item['FieldName']]=="") {
            switch($FieldType)  {
                case 'int':
                    $_POST[$Item['FieldName']] = 0;
                    break;
                case 'date':
                    $_POST[$Item['FieldName']] = "1971-01-01";
                    break;
                case 'datetime':
                    $_POST[$Item['FieldName']] = "1971-01-01 00:00:00";
                    break;
            }
            $CurrentFieldType = $AllShowTypesArray[$AllFieldsMap[$Item['FieldName']]['ShowType']]['ADD'];
            switch($CurrentFieldType) {
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
            }
        }
        $FieldsArray[$Item['FieldName']]       = $_POST[$Item['FieldName']];
    }
    if($IsExecutionSQL)   {
        $KEYS			= array_keys($FieldsArray);
        $VALUES			= array_values($FieldsArray);
        $sql	        = "insert into $TableName(`".join('`,`',$KEYS)."`) values('".join("','",$VALUES)."')";
        $rs             = $db->Execute($sql);
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
            $RS['_GET'] = $_GET;
            $RS['_POST'] = $_POST;
            print json_encode($RS);
            exit;
        }
    }
}

if( ($_GET['action']=="edit_default_data") && $_GET['id']!="" && $TableName!="")  {
    $MetaColumnNames    = $db->MetaColumnNames($TableName);
    $MetaColumnNames    = array_values($MetaColumnNames);
    $FieldsArray        = [];
    $FieldsArray['id']  = $_GET['id'];
    $IsExecutionSQL     = 0;
    foreach($AllFieldsFromTable as $Item)  {
        $FieldsArray[$Item['FieldName']]       = $_POST[$Item['FieldName']];
        if($_POST[$Item['FieldName']]!="") {
            $IsExecutionSQL = 1;
        }
    }
    if($IsExecutionSQL)   {
        [$rs,$sql] = InsertOrUpdateTableByArray($TableName,$FieldsArray,'id',0,"Update");
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
            $RS['_GET'] = $_GET;
            $RS['_POST'] = $_POST;
            print json_encode($RS);
            exit;
        }
    }
}

if(($_GET['action']=="edit_default"||$_GET['action']=="view_default")&&$_GET['id']!="")  {
    $id     = ForSqlInjection($_GET['id']);
    $sql    = "select * from `$TableName` where id = '$id'";
    $rsf     = $db->Execute($sql);
    $RS = [];
    $RS['status'] = "OK";
    $RS['data'] = $rsf->fields;
    $RS['sql'] = $sql;
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
    $RS['edit_default'] = $edit_default;
    print json_encode($RS);
    exit;
}

if($_GET['action']=="updateone")  {
    $id     = ForSqlInjection($_POST['id']);
    $field  = ParamsFilter($_POST['field']);
    $value  = ParamsFilter($_POST['value']);
    $primary_key = $MetaColumnNames[0];
    if($id!=""&&$field!=""&&in_array($field,$MetaColumnNames)&&$primary_key!=$field) {
        $sql    = "update $TableName set $field = '$value' where $primary_key = '$id'";
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
    $selectedRows  = ForSqlInjection($_POST['selectedRows']);
    $selectedRows = explode(',',$selectedRows);
    $primary_key = $MetaColumnNames[0];
    foreach($selectedRows as $id) {
        $sql    = "delete from $TableName where $primary_key = '$id'";
        $db->Execute($sql);
    }
    $RS = [];
    $RS['status'] = "OK";
    $RS['msg'] = "Drop Item Success!";
    print json_encode($RS);
    exit;
}

$AddSql = " where 1=1 ";

//列表页面时的启用字段列表
$init_default_columns = [];
$columnsactions     = [];
$columnsactions[]   = ['action'=>'view_default','text'=>__('View'),'mdi'=>'mdi:eye-outline'];
$columnsactions[]   = ['action'=>'edit_default','text'=>__('Edit'),'mdi'=>'mdi:pencil-outline'];
$columnsactions[]   = ['action'=>'delete_array','text'=>__('Delete'),'mdi'=>'mdi:delete-outline','double_check'=>__('Do you want to delete this item?')];
$init_default_columns[] = ['flex' => 0.1, 'minWidth' => 120, 'sortable' => false, 'field' => "actions", 'headerName' => __("Actions"), 'show'=>true, 'type'=>'actions', 'actions' => $columnsactions];

$searchField = [];
$FieldNameToType = [];
foreach($AllFieldsFromTable as $Item)  {
    $FieldName      = $Item['FieldName'];
    $EnglishName   = $Item['EnglishName'];
    $ShowType       = $Item['ShowType'];
    $IsSearch       = $Item['IsSearch'];
    $IsGroupFilter  = $Item['IsGroupFilter'];
    $ColumnWidth    = intval($Item['ColumnWidth']);
    $IsHiddenGroupFilter = $Item['IsHiddenGroupFilter'];
    $CurrentFieldType = $AllShowTypesArray[$ShowType]['LIST'];
    $CurrentFieldTypeArray = explode(':',$CurrentFieldType);
    $FieldNameToType[$FieldName] = $CurrentFieldType;
    switch($CurrentFieldTypeArray[0])   {
        case 'tablefilter':
        case 'tablefiltercolor':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $EnglishName, 'show'=>true, 'renderCell' => NULL];
            break;
        case 'radiogroup':
        case 'radiogroupcolor':
            $init_default_columns[] = ['flex' => 0.1, 'type'=>$CurrentFieldTypeArray[0], 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $EnglishName, 'show'=>true, 'renderCell' => NULL];
            break;
        default:
            $init_default_columns[] = ['flex' => 0.1, 'type'=>'string', 'minWidth' => $ColumnWidth, 'maxWidth' => $ColumnWidth+100, 'field' => $FieldName, 'headerName' => $EnglishName, 'show'=>true, 'renderCell' => NULL];
            break;
    }
    if($IsSearch==1)   {
        $searchField[] = ['label' => $FieldName, 'value' => $FieldName];
    }

}

$RS['init_action']['action']        = "init_default";
$RS['init_action']['actionValue']   = "";
$RS['init_action']['id']            = 999; //NOT USE THIS VALUE IN FRONT END

$searchFieldName = ForSqlInjection($_REQUEST['searchFieldName']);
$searchFieldValue = ForSqlInjection($_REQUEST['searchFieldValue']);
if ($searchFieldName != "" && $searchFieldValue != "" && in_array($searchFieldName, $MetaColumnNames) ) {
    $AddSql .= " and ($searchFieldName like '%" . $searchFieldValue . "%')";
}

$RS['init_default']['filter'] = [];

$page       = intval($_REQUEST['page']);
$pageSize   = intval($_REQUEST['pageSize']);
if(!in_array($pageSize,[10,15,20,30,40,50,100]))  {
	$pageSize = 30;
}
$fromRecord = $page * $pageSize;

$sql    = "select count(*) AS NUM from $TableName " . $AddSql . "";
$rs     = $db->Execute($sql);
$RS['init_default']['total'] = intval($rs->fields['NUM']);
if($FromInfo['TableName']!="")   {
    $RS['init_default']['searchtitle']  = $FromInfo['FullName'];
}
else {
    $RS['init_default']['searchtitle']  = "Unknown Form";
}
$RS['init_default']['primarykey'] = $MetaColumnNames[0];
if(!in_array($_REQUEST['sortColumn'], $MetaColumnNames)) {
    $_REQUEST['sortColumn'] = $MetaColumnNames[0];
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
$ForbiddenViewRow = [];
$ForbiddenEditRow = [];
$ForbiddenDeleteRow = [];
$sql    = "select * from $TableName " . $AddSql . " $orderby limit $fromRecord,$pageSize";
//print $sql;
$NewRSA = [];
$rs = $db->Execute($sql) or print $sql;
$rs_a = $rs->GetArray();
$FieldDataColorValue = [];
foreach ($rs_a as $Line) {
    $Line['id']         = intval($Line['id']);
    foreach($Line as $FieldName=>$FieldValue) {
        if($FieldValue=="1971-01-01" || $FieldValue=="1971-01-01 00:00:00" || $FieldValue=="1971-01")  {
            $Line[$FieldName] = "";
        }
        // filter data to show on the list page -- begin
        $CurrentFieldType = $FieldNameToType[$FieldName];
        $CurrentFieldTypeArray = explode(':',$CurrentFieldType);
        switch($CurrentFieldTypeArray[0])   {
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
                if($WhereField!="" && $WhereValue!="") {
                    $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' and `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($Line[$FieldName])."' ;";
                }
                else    {
                    $sql = "select `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where `".$MetaColumnNamesTemp[$KeyField]."`='".ForSqlInjection($Line[$FieldName])."' ;";
                }
                $rs = $db->Execute($sql) or print($sql);
                $Line[$FieldName] = $rs->fields['label'];
                $FieldDataColorValue[$FieldName][$Line[$FieldName]] = "#";
                break;
        }
        // filter data to show on the list page -- begin
    }
    $NewRSA[] = $Line;
    if(in_array($Line['TableName'],['data_user','data_department','role','form_formfield'])) {
        $ForbiddenSelectRow[] = $Line['id'];
        //$ForbiddenViewRow[] = $Line['id'];
        //$ForbiddenEditRow[] = $Line['id'];
        $ForbiddenDeleteRow[] = $Line['id'];
    }
}

function ArrayToColorStyle1($Array)                  {
    $ColorArray[] = "success";
    $ColorArray[] = "primary";
    $ColorArray[] = "error";
    $ColorArray[] = "info";
    $ColorArray[] = "warning";
    if(!is_array($Array)) return [];
    $RS = [];
    for($i=0;$i<sizeof($Array);$i++)    {
        $Value = $Array[$i];
        $RS[$Value] = $ColorArray[$i%5];
    }
    return $RS;
}

function ArrayToColorStyle2($Array)                  {
    $ColorArray[] = ['icon'=>'mdi:laptop','color'=>'error.main'];
    $ColorArray[] = ['icon'=>'mdi:cog-outline','color'=>'warning.main'];
    $ColorArray[] = ['icon'=>'mdi:pencil-outline','color'=>'info.main'];
    $ColorArray[] = ['icon'=>'mdi:chart-donut','color'=>'success.main'];
    $ColorArray[] = ['icon'=>'mdi:account-outline','color'=>'primary.main'];
    if(!is_array($Array)) return [];
    $RS = [];
    for($i=0;$i<sizeof($Array);$i++)    {
        $Value = $Array[$i];
        $RS[$Value] = $ColorArray[$i%5];
    }
    return $RS;
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
$RS['init_default']['button_add']       = __("Add");
$RS['init_default']['columns']          = $init_default_columns;
$RS['init_default']['columnsactions']= $columnsactions;

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
$RS['add_default']['defaultValues'] = $defaultValuesAdd;
$RS['add_default']['dialogContentHeight']  = "90%";
$RS['add_default']['submitaction']  = "add_default_data";
$RS['add_default']['componentsize'] = "medium";
$RS['add_default']['submittext']    = __("Submit");
$RS['add_default']['canceltext']    = __("Cancel");
$RS['add_default']['titletext']     = __("Create Form");
$RS['add_default']['titlememo']     = __("Manage All Form Fields in Table");
$RS['add_default']['tablewidth']    = 650;
$RS['add_default']['submitloading'] = __("SubmitLoading");
$RS['add_default']['loading']       = __("Loading");

$RS['edit_default'] = $RS['add_default'];

$RS['edit_default']['allFields']  = $allFieldsEdit;
$RS['edit_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default']['defaultValues']  = $defaultValuesEdit;
$RS['edit_default']['dialogContentHeight']  = "90%";
$RS['edit_default']['submitaction']  = "add_default_data";
$RS['edit_default']['componentsize'] = "small";
$RS['edit_default']['submittext']    = __("Submit");
$RS['edit_default']['canceltext']    = __("Cancel");
$RS['edit_default']['titletext']  = __("Edit Form");
$RS['edit_default']['titlememo']  = __("Manage All Form Fields in Table");
$RS['edit_default']['tablewidth']  = 650;
$RS['edit_default']['submitloading']    = __("SubmitLoading");
$RS['edit_default']['loading']          = __("Loading");


$RS['view_default'] = $RS['add_default'];
$RS['view_default']['titletext']  = __("View Form");
$RS['view_default']['titlememo']  = __("View All Form Fields in Table");
$RS['view_default']['componentsize'] = "small";

$RS['export_default'] = [];
$RS['import_default'] = [];

$RS['init_default']['rowHeight']        = 38;
$RS['init_default']['dialogContentHeight']  = "90%";
$RS['init_default']['dialogMaxWidth']   = "md";// xl lg md sm xs
$RS['init_default']['timeline']         = time();
$RS['init_default']['pageNumber']       = $pageSize;
$RS['init_default']['pageCount']        = ceil($RS['init_default']['total']/$pageSize);
$RS['init_default']['pageNumberArray']  = [10,15,20,30,40,50,100];

if(sizeof($MetaColumnNames)>5) {
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



