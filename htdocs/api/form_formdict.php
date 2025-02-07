<?php
header("Content-Type: application/json");
require_once('cors.php');
require_once('include.inc.php');

CheckAuthUserLoginStatus();
CheckAuthUserRoleHaveMenu(0, "/form/formdict");

$TableName  = "form_formdict";

$columnNames = [];
$sql = "show columns from form_formdict";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $columnNames[] = $Line['Field'];
}

//新建页面时的启用字段列表
$allFieldsAdd = [];
$allFieldsAdd['Default'][] = ['name' => 'DictMark', 'show'=>true, 'type'=>'input', 'label' => __('DictMark'), 'value' => '', 'placeholder' => 'DictMark', 'helptext' => 'DictMark', 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$allFieldsAdd['Default'][] = ['name' => 'EnglishName', 'show'=>true, 'type'=>'input', 'label' => __('English Name'), 'value' => '', 'placeholder' => 'English Name input', 'helptext' => 'English Name', 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$allFieldsAdd['Default'][] = ['name' => 'ChineseName', 'show'=>true, 'type'=>'input', 'label' => __('Chinese Name'), 'value' => '', 'placeholder' => 'Chinese Name input', 'helptext' => 'Chinese Name', 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$allFieldsAdd['Default'][] = ['name' => 'Code', 'show'=>true, 'type'=>'input', 'label' => __('Code'), 'value' => '', 'placeholder' => 'Code input', 'helptext' => 'Code', 'rules' => ['required' => true,'xs'=>12, 'sm'=>6, 'disabled' => false]];
$allFieldsAdd['Default'][] = ['name' => 'SortNumber', 'show'=>true, 'type'=>'number', 'label' => __('SortNumber'), 'value' => '0', 'placeholder' => 'Sort number in form', 'helptext' => 'Sort number', 'rules' => ['required' => true,'xs'=>12, 'sm'=>2,'disabled' => false]];
$allFieldsAdd['Default'][] = ['name' => 'ExtraControl', 'show'=>true, 'type'=>'textarea', 'label' => __('ExtraControl'), 'value' => '', 'placeholder' => __('ExtraControl'), 'helptext' => '', 'rules' => ['required' => false,'xs'=>12, 'sm'=>12]];


foreach($allFieldsAdd as $ModeName=>$allFieldItem) {
    foreach($allFieldItem as $ITEM) {
        $defaultValues[$ITEM['name']] = $ITEM['value'];
    }
}

//编辑页面时的启用字段列表
$allFieldsEdit = $allFieldsAdd;

if( ($_GET['action']=="add_default_data") && $_POST['DictMark']!="")  {
    $MetaColumnNames    = $db->MetaColumnNames($TableName);
    $MetaColumnNames    = array_values($MetaColumnNames);

    $EnglishNameArray   = explode(',',trim($_POST['EnglishName']));
    $ChineseNameArray   = explode(',',trim($_POST['ChineseName']));
    $CodeArray          = explode(',',trim($_POST['Code']));

    $_POST['DictMark']  = str_replace(":","_",$_POST['DictMark']);

    $Exec_Total             = 1;
    for($i=0;$i<sizeof($EnglishNameArray);$i++)    {
        $FieldsArray                    = [];
        $FieldsArray['DictMark']        = $_POST['DictMark'];
        $FieldsArray['SortNumber']      = intval($_POST['SortNumber']);
        $FieldsArray['EnglishName']     = $EnglishNameArray[$i];
        $FieldsArray['ChineseName']     = $ChineseNameArray[$i];
        if($CodeArray[$i]=="") {
            $CodeArray[$i] = $i;
        }
        $FieldsArray['Code']            = $CodeArray[$i];
        $FieldsArray['ExtraControl']    = $_POST['ExtraControl'];
        if(1)   {
            [$rs,$sql] = InsertOrUpdateTableByArray("form_formdict",$FieldsArray,"DictMark,EnglishName",0,"Insert");
        }
    }
    if($Exec_Total) {
        $RS['status'] = "OK";
        $RS['msg'] = __("Submit Success");
        print json_encode($RS);
        exit;
    }
    else {
        $Exec_Total = 0;
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = __("sql execution failed");
        $RS['sql'] = $sql;
        $RS['_POST'] = $_POST;
        print json_encode($RS);
        exit;
    }
}

if( ($_GET['action']=="edit_default_data") && $_GET['id']!="")  {
    $MetaColumnNames    = $db->MetaColumnNames($TableName);
    $MetaColumnNames    = array_values($MetaColumnNames);
    $FieldsArray                    = [];
    $FieldsArray['DictMark']        = $_POST['DictMark'];
    $FieldsArray['SortNumber']      = intval($_POST['SortNumber']);
    $FieldsArray['EnglishName']     = $_POST['EnglishName'];
    $FieldsArray['ChineseName']     = $_POST['ChineseName'];
    $FieldsArray['Code']            = $_POST['Code'];
    $FieldsArray['ExtraControl']    = $_POST['ExtraControl'];
    if(1)   {
        [$rs,$sql] = InsertOrUpdateTableByArray("form_formdict",$FieldsArray,"id",0,"Update");
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
    $sql    = "select * from form_formdict where ID = '$id'";
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
    $EnableFields = returntablefield("form_formdict_showtype","Name",$EditValue['ShowType'],"EnableFields");
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
    $sql    = "select * from form_formdict where ID = '$id'";
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
        $sql    = "update form_formdict set $field = '$value' where $primary_key = '$id'";
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
    $primary_key = $columnNames[0];
    if($selectedRows[0]!="") {
        foreach($selectedRows as $id) {
            $sql    = "delete from form_formdict where $primary_key = '$id'";
            $db->Execute($sql);
        }
        $RS = [];
        $RS['status'] = "OK";
        $RS['msg'] = __("Drop Form Dict Success");
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

$AddSql = " where 1=1";

$columnsactions = [];
$columnsactions[] = ['action'=>'view_default','text'=>__('View'),'mdi'=>'mdi:eye-outline'];
$columnsactions[] = ['action'=>'edit_default','text'=>__('Edit'),'mdi'=>'mdi:pencil-outline'];
$columnsactions[] = ['action'=>'delete_array','text'=>__('Delete'),'mdi'=>'mdi:delete-outline','double_check'=>__('Do you want to delete this item?')];
$init_default_columns[] = ['flex' => 0.1, 'minWidth' => 120, 'sortable' => false, 'field' => "actions", 'headerName' => __("Actions"), 'show'=>true, 'type'=>'actions', 'actions' => $columnsactions];
$columnName = "DictMark";       $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "EnglishName";    $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "ChineseName";    $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "Code";           $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL ];
$columnName = "SortNumber";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "ExtraControl";   $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];

$RS['init_default']['button_search']    = __("Search");
$RS['init_default']['button_add']       = __("Add");
$RS['init_default']['columns']          = $init_default_columns;
$RS['init_default']['columnsactions']   = $columnsactions;

$columnName = "EnglishName";        $searchField[] = ['label' => __($columnName), 'value' => $columnName];
$columnName = "ChineseName";        $searchField[] = ['label' => __($columnName), 'value' => $columnName];
$columnName = "DictMark";           $searchField[] = ['label' => __($columnName), 'value' => $columnName];
$columnName = "ExtraControl";       $searchField[] = ['label' => __($columnName), 'value' => $columnName];

$RS['init_default']['searchFieldArray'] = $searchField;
$RS['init_default']['searchFieldText']  = __("Search Item");

$RS['init_action']['action']        = "init_default";
$RS['init_action']['actionValue']   = "";
$RS['init_action']['id']            = 999; //NOT USE THIS VALUE IN FRONT END

$searchFieldName     = ForSqlInjection($_REQUEST['searchFieldName']);
$searchFieldValue    = ForSqlInjection($_REQUEST['searchFieldValue']);
if ($searchFieldName != "" && $searchFieldValue != "" && in_array($searchFieldName, $columnNames) ) {
    $AddSql .= " and ($searchFieldName like '%" . $searchFieldValue . "%')";
}

$sql        = "select count(*) as NUM from form_formdict $AddSql ";
$rs         = $db->Execute($sql);
$ALL_NUM    = intval($rs->fields['NUM']);

$sql = "select DictMark as name, DictMark as value, count(*) AS num from form_formdict $AddSql group by DictMark";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
array_unshift($rs_a,['name'=>__('All Data'), 'value'=>'All Data', 'num'=>$ALL_NUM]);
$RS['init_default']['filter'][] = ['name' => 'DictMark', 'text' => __('DictMark'), 'list' => $rs_a, 'selected' => "All Data"];

$DictMark = ForSqlInjection($_REQUEST['DictMark']);
if ($DictMark != "" && $DictMark != "All Data") {
    $AddSql .= " and (DictMark = '" . $DictMark . "')";
}
else if ($DictMark == "") {
    //$AddSql .= " and (FormGroup = '" . $rs_a[1]['name'] . "')";
}

$page       = intval($_REQUEST['page']);
$pageSize   = intval($_REQUEST['pageSize']);
if(!in_array($pageSize,[10,15,20,30,40,50,100]))  {
	$pageSize = 30;
}
$fromRecord = $page * $pageSize;

$sql    = "select count(*) AS NUM from form_formdict " . $AddSql . "";
$rs     = $db->Execute($sql);
$RS['init_default']['total'] = intval($rs->fields['NUM']);
$RS['init_default']['searchtitle']  = __("Form Dict");
$RS['init_default']['primarykey'] = $columnNames[0];
if(!in_array($_REQUEST['sortColumn'], $columnNames)) {
    $_REQUEST['sortColumn'] = $columnNames[0];
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
$sql    = "select * from form_formdict " . $AddSql . " $orderby limit $fromRecord,$pageSize";
//print $sql;
$NewRSA = [];
$rs = $db->Execute($sql) or print $sql;
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $Line['id']         = intval($Line['id']);
    $NewRSA[] = $Line;
    if(in_array($Line['TableName'],['data_user','data_department','role','form_formdict'])) {
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


$RS['add_default']['allFields']     = $allFieldsAdd;
$RS['add_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['add_default']['defaultValues'] = $defaultValues;
$RS['add_default']['dialogContentHeight']  = "850px";
$RS['add_default']['submitaction']  = "add_default_data";
$RS['add_default']['componentsize'] = "medium";
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
$RS['edit_default']['defaultValues']  = $defaultValues;
$RS['edit_default']['dialogContentHeight']  = "850px";
$RS['edit_default']['submitaction']  = "add_default_data";
$RS['edit_default']['componentsize'] = "medium";
$RS['edit_default']['componentsize'] = "medium";
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

$RS['export_default'] = [];
$RS['import_default'] = [];

$RS['init_default']['rowHeight']  = 38;
$RS['init_default']['dialogContentHeight']  = "850px";
$RS['init_default']['dialogMaxWidth']  = "md";// xl lg md sm xs
$RS['init_default']['timeline']     = time();
$RS['init_default']['pageNumber']   = $pageSize;
$RS['init_default']['pageCount']    = ceil($RS['init_default']['total']/$pageSize);
$RS['init_default']['pageNumberArray']  = [10,15,20,30,40,50,100];

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



