<?php
header("Content-Type: application/json");
require_once('cors.php');
require_once('include.inc.php');

CheckAuthUserLoginStatus();
CheckAuthUserRoleHaveMenu(0, "/form/formname");

$externalId = intval($_REQUEST['externalId']);
$FlowInfo   = returntablefield("form_formflow","id",$externalId,"FormId,FlowName,FaceTo");
$FormId     = $FlowInfo['FormId'];

$sql        = "select * from form_formname where id='$FormId'";
$rs         = $db->Execute($sql);
$FormInfo   = $rs->fields;
$FullName  = $FormInfo['FullName'];
$TableName  = $FormInfo['TableName'];

$TitleName  = $FullName."-".$FlowInfo['FlowName'];

$columnNames = [];
$sql = "show columns from form_configsetting";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $columnNames[] = $Line['Field'];
}

//Force the SortNumber to a default value
$sql = "update form_configsetting set SortNumber=id where SortNumber='0'";
$db->Execute($sql);

//新建页面时的启用字段列表
$allFieldsAdd = [];
$allFieldsAdd[] = ['name' => 'FieldName', 'show'=>true, 'type'=>'input', 'label' => __('Field Name'), 'value' => '', 'placeholder' => __('Field Name in Database'), 'helptext' => __('Field Name in Database'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>12, 'disabled' => false,'min'=>1]];

$sql = "select `Name` as value, `Name` as label, EnableFields, DisableFields from form_formfield_showtype order by SortNumber asc, id asc";
$rs = $db->Execute($sql);
$ShowType = $rs->GetArray();
$EnableFields = [];
$DisableFields = [];
foreach ($ShowType as $Line) {
    $EnableFields[$Line['value']] = explode(',',$Line['EnableFields']);
    $DisableFields[$Line['value']] = explode(',',$Line['DisableFields']);
}
$allFieldsAdd[] = ['name' => 'ShowType_名称', 'code' => 'ShowType', 'show'=>true, 'type'=>'autocomplete', 'options'=>$ShowType, 'label' => __('Show Type'), 'value' => $ShowType[0]['value'], 'placeholder' => __('Show Type in UI'), 'helptext' => __('Show Type in UI'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>8,'disabled' => false], 'freeSolo'=>false, 'EnableFields'=>$EnableFields, 'DisableFields'=>$DisableFields];
$allFieldsAdd[] = ['name' => 'SortNumber', 'show'=>true, 'type'=>'number', 'label' => __('SortNumber'), 'value' => '0', 'placeholder' => __('Sort number in form'), 'helptext' => __('Sort number'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>2,'disabled' => false]];
$allFieldsAdd[] = ['name' => 'FieldDefault', 'show'=>true, 'type'=>'input', 'label' => __('Default'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>2,'disabled' => false]];

$allFieldsAdd[] = ['name' => 'Max', 'show'=>false, 'type'=>'number', 'label' => __('Max'), 'value' => '', 'placeholder' => __('Maximum input'), 'helptext' => __('Value length maximum, or leave it blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>6, 'disabled' => false,'min'=>0,'max'=>4]];
$allFieldsAdd[] = ['name' => 'Min', 'show'=>false, 'type'=>'number', 'label' => __('Min'), 'value' => '', 'placeholder' => __('Minimum input'), 'helptext' => __('Value length maximum, or leave it blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>6, 'disabled' => false,'min'=>0,'max'=>4]];

$allFieldsAdd[] = ['name' => 'StartDate', 'show'=>false, 'type'=>'date', 'label' => __('Start Date'), 'value' => '', 'placeholder' => __('Start Date'), 'helptext' => __('Start Date'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndDate', 'show'=>false, 'type'=>'date', 'label' => __('End Date'), 'value' => '', 'placeholder' => __('End Date'), 'helptext' => __('End Date'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultDate', 'show'=>false, 'type'=>'date', 'label' => __('Field Default Date'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'StartDateTime', 'show'=>false, 'type'=>'datetime', 'label' => __('Start DateTime'), 'value' => '', 'placeholder' => __('Start DateTime'), 'helptext' => __('Start DateTime'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd HH:mm','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndDateTime', 'show'=>false, 'type'=>'datetime', 'label' => __('End DateTime'), 'value' => '', 'placeholder' => __('End DateTime'), 'helptext' => __('End DateTime'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd HH:mm','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultDateTime', 'show'=>false, 'type'=>'datetime', 'label' => __('Field Default Date Time'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd HH:mm','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'StartMonth', 'show'=>false, 'type'=>'month', 'label' => __('Start Month'), 'value' => '', 'placeholder' => __('Start Month'), 'helptext' => __('Start Month'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndMonth', 'show'=>false, 'type'=>'month', 'label' => __('End Month'), 'value' => '', 'placeholder' => __('End Month'), 'helptext' => __('End Month'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultMonth', 'show'=>false, 'type'=>'month', 'label' => __('Field Default Month'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'StartYear', 'show'=>false, 'type'=>'year', 'label' => __('Start Year'), 'value' => '', 'placeholder' => __('Start Year'), 'helptext' => __('Start Year'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndYear', 'show'=>false, 'type'=>'year', 'label' => __('End Year'), 'value' => '', 'placeholder' => __('End Year'), 'helptext' => __('End Year'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultYear', 'show'=>false, 'type'=>'year', 'label' => __('Field Default Year'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'StartQuarter', 'show'=>false, 'type'=>'quarter', 'label' => __('Start Quarter'), 'value' => '', 'placeholder' => __('Start Quarter'), 'helptext' => __('Start Quarter'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndQuarter', 'show'=>false, 'type'=>'quarter', 'label' => __('End Quarter'), 'value' => '', 'placeholder' => __('End Quarter'), 'helptext' => __('End Quarter'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultQuarter', 'show'=>false, 'type'=>'quarter', 'label' => __('Field Default Quarter'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-QQQ','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'Placeholder', 'show'=>true, 'type'=>'input', 'label' => __('Place holder'), 'value' => '', 'placeholder' => __('Place holder tip infor'), 'helptext' => __('Place holder tip infor'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$allFieldsAdd[] = ['name' => 'Helptext', 'show'=>true, 'type'=>'input', 'label' => __('Help text'), 'value' => '', 'placeholder' => __('Help text tip infor'), 'helptext' => __('Help text tip infor'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>6, 'disabled' => false]];


$IsFullWidth = [];
$IsFullWidth[] = ['value'=>'12', 'label'=>'12'];
$IsFullWidth[] = ['value'=>'6', 'label'=>'6'];
$IsFullWidth[] = ['value'=>'5', 'label'=>'5'];
$IsFullWidth[] = ['value'=>'4', 'label'=>'4'];
$IsFullWidth[] = ['value'=>'3', 'label'=>'3'];
$IsFullWidth[] = ['value'=>'2', 'label'=>'2'];
$allFieldsAdd[] = ['name' => 'IsFullWidth', 'show'=>true, 'type'=>'radiogroup', 'options'=>$IsFullWidth, 'label' => __('Field Width (Total 12)'), 'value' => $IsFullWidth[1]['value'], 'placeholder' => __(''), 'helptext' => __(''), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];

$IsMustFill = [];
$IsMustFill[] = ['value'=>'1', 'label'=>__('Enable')];
$IsMustFill[] = ['value'=>'0', 'label'=>__('Disable')];
$allFieldsAdd[] = ['name' => 'IsMustFill', 'show'=>true, 'type'=>'radiogroup', 'options'=>$IsMustFill, 'label' => __('Must Fill'), 'value' => $IsMustFill[1]['value'], 'placeholder' => __('Must Fill'), 'helptext' => __('Must Fill'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];

$IsEnable = [];
$IsEnable[] = ['value'=>'1', 'label'=>__('Enable')];
$IsEnable[] = ['value'=>'0', 'label'=>__('Disable')];
$allFieldsAdd[] = ['name' => 'IsEnable', 'show'=>true, 'type'=>'radiogroup', 'options'=>$IsEnable, 'label' => __('Is Enable'), 'value' => $IsEnable[0]['value'], 'placeholder' => __('Is Enable for this field'), 'helptext' => __('Is Enable for this field'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];


foreach($allFieldsAdd as $ITEM) {
    $defaultValues[$ITEM['name']] = $ITEM['value'];
}
//编辑页面时的启用字段列表
$allFieldsEdit = $allFieldsAdd;

if( ($_GET['action']=="add_default_data") && $_POST['FieldName']!="" && $externalId!="")  {
    $FieldNameArray         = explode(',',trim($_POST['FieldName']));
    $EnglishNameArray       = explode(',',trim($_POST['FieldName']));
    $ChineseNameArray       = explode(',',trim($_POST['FieldName']));
    $Exec_Total             = 1;
    for($i=0;$i<sizeof($FieldNameArray);$i++)    {
        $FieldName                      = trim($FieldNameArray[$i]);
        $FieldsArray                    = [];
        $FieldsArray['FlowId']          = $externalId;
        $FieldsArray['FlowName']        = $TitleName;
        $FieldsArray['FieldName']       = $FieldName;
        $FieldsArray['FieldType']       = $_POST['FieldType'];
        $FieldsArray['ShowType']        = $_POST['ShowType'];
        $FieldsArray['FieldDefault']    = $_POST['FieldDefault'];
        $FieldsArray['IsMustFill']      = $_POST['IsMustFill'];
        $FieldsArray['IsFullWidth']     = $_POST['IsFullWidth'];
        $FieldsArray['Max']             = $_POST['Max'];
        $FieldsArray['Min']             = $_POST['Min'];
        $FieldsArray['IsSearch']        = $_POST['IsSearch'];
        $FieldsArray['IsGroupFilter']   = $_POST['IsGroupFilter'];
        $FieldsArray['IsDbIndex']       = $_POST['IsDbIndex'];
        $FieldsArray['IsEnable']        = $_POST['IsEnable'];
        $FieldsArray['SortNumber']      = intval($_POST['SortNumber']);
        $FieldsArray['EnglishName']     = $EnglishNameArray[$i];
        $FieldsArray['ChineseName']     = $ChineseNameArray[$i];
        $FieldsArray['Placeholder']     = $_POST['Placeholder'];
        $FieldsArray['Helptext']        = $_POST['Helptext'];
        $FieldsArray['ColumnWidth']     = $_POST['ColumnWidth'];
        $FieldsArray['Setting']         = json_encode($_POST);
        if(1)   {
            [$rs,$sql] = InsertOrUpdateTableByArray("form_configsetting",$FieldsArray,"FlowId,FieldName",0,"Insert");
            if(!$rs->EOF) {
                $Exec_Total   = 0;
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
    if($Exec_Total)  {
        $RS['status'] = "OK";
        $RS['msg'] = __("Submit Success");
        print json_encode($RS);
        exit;
    }
}

if( ($_GET['action']=="edit_default_data") && $_GET['id']!="" && $externalId!="" && $_POST['FieldName']!="")  {
    $FieldsArray                    = [];
    $FieldsArray['id']              = $_GET['id']; // for primary key to update
    $FieldsArray['FlowId']          = $externalId;
    $FieldsArray['FlowName']        = $TitleName;
    $FieldsArray['FieldName']       = $_POST['FieldName'];
    $FieldsArray['FieldType']       = $_POST['FieldType'];
    $FieldsArray['ShowType']        = $_POST['ShowType'];
    $FieldsArray['FieldDefault']    = $_POST['FieldDefault'];
    $FieldsArray['IsMustFill']      = $_POST['IsMustFill'];
    $FieldsArray['IsFullWidth']      = $_POST['IsFullWidth'];
    $FieldsArray['Max']             = $_POST['Max'];
    $FieldsArray['Min']             = $_POST['Min'];
    $FieldsArray['IsSearch']        = $_POST['IsSearch'];
    $FieldsArray['IsGroupFilter']   = $_POST['IsGroupFilter'];
    $FieldsArray['IsDbIndex']       = $_POST['IsDbIndex'];
    $FieldsArray['IsEnable']        = $_POST['IsEnable'];
    $FieldsArray['SortNumber']      = intval($_POST['SortNumber']);
    $FieldsArray['EnglishName']    = $_POST['FieldName'];
    $FieldsArray['ChineseName']    = $_POST['FieldName'];
    $FieldsArray['Placeholder']     = $_POST['Placeholder'];
    $FieldsArray['Helptext']        = $_POST['Helptext'];
    $FieldsArray['ColumnWidth']     = $_POST['ColumnWidth'];
    $FieldsArray['Setting']         = json_encode($_POST);

    $sql = "select FieldName from form_configsetting where id='".$_GET['id']."'";
    $rs = $db->Execute($sql);
    $FieldNameOld = $rs->fields['FieldName'];

    if(1)   {
        [$rs,$sql] = InsertOrUpdateTableByArray("form_configsetting",$FieldsArray,"id",0,"Update");
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

if(($_GET['action']=="edit_default")&&$_GET['id']!="")  {
    $id     = ForSqlInjection($_GET['id']);
    $sql    = "select * from form_configsetting where ID = '$id'";
    $rsf     = $db->Execute($sql);
    $EditValue = [];
    $Setting = $rsf->fields['Setting'];
    if($Setting!="")    {
        $Setting = json_decode($Setting,true);
        foreach($Setting as $FieldName=>$FieldValue)  {
            $EditValue[$FieldName] = $FieldValue;
        }
        $EditValue['Setting'] = '';
    }
    foreach($rsf->fields as $FieldName=>$FieldValue)  {
        $EditValue[$FieldName] = $FieldValue;
    }
    $EnableFields = returntablefield("form_formfield_showtype","Name",$EditValue['ShowType'],"EnableFields");
    $RS = [];
    $RS['status'] = "OK";
    $RS['data'] = $EditValue;
    $RS['EnableFields'] = explode(",",$EnableFields['EnableFields']);
    $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    print json_encode($RS);
    exit;
}

if(($_GET['action']=="view_default")&&$_GET['id']!="")  {
    $id     = ForSqlInjection($_GET['id']);
    $sql    = "select * from form_configsetting where ID = '$id'";
    $rsf     = $db->Execute($sql);
    $EditValue = [];
    $Setting = $rsf->fields['Setting'];
    if($Setting!="")    {
        $Setting = json_decode($Setting,true);
        foreach($Setting as $FieldName=>$FieldValue)  {
            $EditValue[$FieldName] = $FieldValue;
        }
        $EditValue['Setting'] = '';
    }
    foreach($rsf->fields as $FieldName=>$FieldValue)  {
        $EditValue[$FieldName] = $FieldValue;
    }
    $EnableFields = returntablefield("form_formfield_showtype","Name",$EditValue['ShowType'],"EnableFields");
    $RS = [];
    $RS['status'] = "OK";
    $RS['data'] = $EditValue;
    $RS['EnableFields'] = explode(",",$EnableFields['EnableFields']);
    $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");

    $FieldNameArray             = array_keys($EditValue);
    $ApprovalNodeFieldsHidden   = [];
    for($X=0;$X<sizeof($FieldNameArray);$X=$X+2)        {
        $FieldName1 = $FieldNameArray[$X];
        $RowData = [];
        if(!in_array($FieldName1,$ApprovalNodeFieldsHidden) && $FieldName1!="") {
            $RowData[0]['Name']         = $FieldName1;
            $RowData[0]['Value']        = $EditValue[$FieldName1];
            $RowData[0]['FieldArray']   = ['name'=>$FieldName1,'label'=>__($FieldName1),'value'=>$EditValue[$FieldName1],'type'=>'input'];
        }
        $FieldName2 = $FieldNameArray[$X+1];
        if(!in_array($FieldName2,$ApprovalNodeFieldsHidden) && $FieldName2!="") {
            $RowData[1]['Name']         = $FieldName2;
            $RowData[1]['Value']        = $EditValue[$FieldName2];
            $RowData[1]['FieldArray']   = ['name'=>$FieldName1,'label'=>__($FieldName2),'value'=>$EditValue[$FieldName2],'type'=>'input'];
        }
        if(sizeof($RowData)>0) {
            $NewTableRowData[] = $RowData;
        }
    }
    $RS['newTableRowData']          = $NewTableRowData;

    print json_encode($RS);
    exit;
}

if($_GET['action']=="updateone")  {
    $id     = ForSqlInjection($_POST['id']);
    $field  = ParamsFilter($_POST['field']);
    $value  = ParamsFilter($_POST['value']);
    $primary_key = $columnNames[0];
    if($id!=""&&$field!=""&&in_array($field,$columnNames)&&$primary_key!=$field) {
        $sql    = "update form_configsetting set $field = '$value' where $primary_key = '$id'";
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
    $primary_key = $columnNames[0];
    if($selectedRows[0]!="") {
        $sql    = "select FlowName,FieldName from form_configsetting where $primary_key = '".$selectedRows[0]."'";
        $rs     = $db->Execute($sql);
        $FlowName = $rs->fields['FlowName'];
        $FieldName = $rs->fields['FieldName'];
        if($FlowName!="")  {
            foreach($selectedRows as $id) {
                $sql    = "delete from form_configsetting where $primary_key = '$id'";
                $db->Execute($sql);
            }
            $RS = [];
            $RS['status'] = "OK";
            $RS['msg'] = __("Drop Table Field Success");
            print json_encode($RS);
            exit;
        }
        else {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['msg'] = __("FlowName is null");
            print json_encode($RS);
            exit;
        }
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

$AddSql = " where 1=1 and FlowId='$externalId'";

$columnsactions = [];
$columnsactions[] = ['action'=>'view_default','text'=>__('View'),'mdi'=>'mdi:eye-outline'];
$columnsactions[] = ['action'=>'edit_default','text'=>__('Edit'),'mdi'=>'mdi:pencil-outline'];
$columnsactions[] = ['action'=>'delete_array','text'=>__('Delete'),'mdi'=>'mdi:delete-outline','double_check'=>__('Do you want to delete this item?')];
$init_default_columns[] = ['flex' => 0.1, 'minWidth' => 120, 'sortable' => false, 'field' => "actions", 'headerName' => __("Actions"), 'show'=>true, 'type'=>'actions', 'actions' => $columnsactions];
//$columnName = "id";             $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 80, 'maxWidth' => 80, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "FieldName";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 300, 'maxWidth' => 500, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "ShowType";       $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 300, 'maxWidth' => 500, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL, 'editable'=>true];
$columnName = "SortNumber";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'editable'=>true, 'renderCell' => NULL];

$columnName = "FieldName";      $searchField[] = ['label' => __($columnName), 'value' => $columnName];
$columnName = "ShowType";       $searchField[] = ['label' => __($columnName), 'value' => $columnName];

$RS['init_default']['button_search']    = __("Search");
$RS['init_default']['button_add']       = __("Add");
$RS['init_default']['columns']          = $init_default_columns;
$RS['init_default']['columnsactions']   = $columnsactions;

$RS['init_action']['action']        = "init_default";
$RS['init_action']['actionValue']   = "";
$RS['init_action']['id']            = 999; //NOT USE THIS VALUE IN FRONT END

$RS['init_default']['searchFieldArray'] = $searchField;
$RS['init_default']['searchFieldText'] = __("Search Item");

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

$sql    = "select count(*) AS NUM from form_configsetting " . $AddSql . "";
$rs     = $db->Execute($sql);
$RS['init_default']['total'] = intval($rs->fields['NUM']);
if($TitleName!=""&&$TitleName!="-")   {
    $RS['init_default']['searchtitle']  = $TitleName;
}
else {
    $RS['init_default']['searchtitle']  = "Unknown Config Setting";
}
$RS['init_default']['primarykey'] = $columnNames[0];

if($_REQUEST['sortColumn']=="")   {
    $_REQUEST['sortColumn'] = "SortNumber";
}
if(!in_array($_REQUEST['sortColumn'], $columnNames)) {
    $_REQUEST['sortColumn'] = $columnNames[0];
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
$sql    = "select * from form_configsetting " . $AddSql . " $orderby limit $fromRecord,$pageSize";
//print $sql;
$NewRSA = [];
$rs = $db->Execute($sql) or print $sql;
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $Line['id']         = intval($Line['id']);
    $NewRSA[] = $Line;
    if(in_array($Line['TableName'],['data_user','data_department','role','form_configsetting'])) {
        $ForbiddenSelectRow[] = $Line['id'];
        //$ForbiddenViewRow[] = $Line['id'];
        //$ForbiddenEditRow[] = $Line['id'];
        $ForbiddenDeleteRow[] = $Line['id'];
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


$RS['add_default']['allFields']['Default']  = $allFieldsAdd;
$RS['add_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['add_default']['defaultValues']  = $defaultValues;
$RS['add_default']['dialogContentHeight']  = "850px";
$RS['add_default']['submitaction']  = "add_default_data";
$RS['add_default']['componentsize'] = "medium";
$RS['add_default']['submittext']    = __("Submit");
$RS['add_default']['canceltext']    = __("Cancel");
$RS['add_default']['titletext']  = __("Create Config Setting");
$RS['add_default']['titlememo']  = __("Manage All Config Setting Fields");
$RS['add_default']['tablewidth']  = 650;
$RS['add_default']['submitloading'] = __("SubmitLoading");
$RS['add_default']['loading']       = __("Loading");

$RS['edit_default'] = $RS['add_default'];

$RS['edit_default']['allFields']['Default']  = $allFieldsEdit;
$RS['edit_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default']['defaultValues']  = $defaultValues;
$RS['edit_default']['dialogContentHeight']  = "850px";
$RS['edit_default']['submitaction']  = "add_default_data";
$RS['edit_default']['componentsize'] = "medium";
$RS['edit_default']['submittext']    = __("Submit");
$RS['edit_default']['canceltext']    = __("Cancel");
$RS['edit_default']['titletext']  = __("Edit Config Setting");
$RS['edit_default']['titlememo']  = __("Manage All Config Setting Fields");
$RS['edit_default']['tablewidth']  = 650;
$RS['edit_default']['submitloading']    = __("SubmitLoading");
$RS['edit_default']['loading']          = __("Loading");


$RS['view_default'] = $RS['add_default'];
$RS['view_default']['titletext']  = __("View Config Setting");
$RS['view_default']['titlememo']  = __("View All Config Setting Fields");

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



