<?php
header("Content-Type: application/json");
require_once('cors.php');
require_once('include.inc.php');

CheckAuthUserLoginStatus();
CheckAuthUserRoleHaveMenu(0, "/form/formfieldshowtype");

$columnNames = [];
$sql = "show columns from form_formfield_showtype";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $columnNames[] = $Line['Field'];
}

//新建页面时的启用字段列表
$allFieldsAdd = [];
$allFieldsAdd[] = ['name' => 'Name', 'show'=>true, 'type'=>'input', 'label' => __('Name'), 'value' => '', 'placeholder' => __('Name'), 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsAdd[] = ['name' => 'LIST', 'show'=>true, 'type'=>'input', 'label' => __('LIST'), 'value' => '', 'placeholder' => __('LIST'), 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsAdd[] = ['name' => 'ADD', 'show'=>true, 'type'=>'input', 'label' => __('ADD'), 'value' => '', 'placeholder' => __('ADD'), 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsAdd[] = ['name' => 'EDIT', 'show'=>true, 'type'=>'input', 'label' => __('EDIT'), 'value' => '', 'placeholder' => __('EDIT'), 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsAdd[] = ['name' => 'VIEW', 'show'=>true, 'type'=>'input', 'label' => __('VIEW'), 'value' => '', 'placeholder' => __('VIEW'), 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsAdd[] = ['name' => 'SortNumber', 'show'=>true, 'type'=>'number', 'label' => __('SortNumber'), 'value' => '999', 'placeholder' => __('SortNumber'), 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsAdd[] = ['name' => 'EnableFields', 'show'=>true, 'type'=>'textarea', 'label' => __('EnableFields'), 'value' => '', 'placeholder' => __('EnableFields'), 'helptext' => '', 'rules' => ['required' => false,'xs'=>12, 'sm'=>12]];

foreach($allFieldsAdd as $ITEM) {
    $defaultValues[$ITEM['name']] = $ITEM['value'];
}

//编辑页面时的启用字段列表
$allFieldsEdit = $allFieldsAdd;

if( ($_GET['action']=="add_default_data"||$_GET['action']=="edit_default_1_data") && $_POST['Name']!="")  {
    $FieldsArray                = [];
    $FieldsArray['Name']        = $_POST['Name'];
    $FieldsArray['LIST']        = $_POST['LIST'];
    $FieldsArray['ADD']         = $_POST['ADD'];
    $FieldsArray['EDIT']        = $_POST['EDIT'];
    $FieldsArray['VIEW']        = $_POST['VIEW'];
    $FieldsArray['SortNumber']  = $_POST['SortNumber'];
    $FieldsArray['EnableFields'] = $_POST['EnableFields'];
    [$rs,$sql] = InsertOrUpdateTableByArray("form_formfield_showtype",$FieldsArray,"Name",0,"Insert");
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

if( ($_GET['action']=="edit_default_data") && $_GET['id']!="")  {
    $FieldsArray                = [];
    $FieldsArray['id']          = $_GET['id'];
    $FieldsArray['Name']        = $_POST['Name'];
    $FieldsArray['LIST']        = $_POST['LIST'];
    $FieldsArray['ADD']         = $_POST['ADD'];
    $FieldsArray['EDIT']        = $_POST['EDIT'];
    $FieldsArray['VIEW']        = $_POST['VIEW'];
    $FieldsArray['SortNumber']  = $_POST['SortNumber'];
    $FieldsArray['EnableFields'] = $_POST['EnableFields'];
    [$rs,$sql] = InsertOrUpdateTableByArray("form_formfield_showtype",$FieldsArray,"id",0,"Update");
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

if(($_GET['action']=="edit_default"||$_GET['action']=="edit_default_1")&&$_GET['id']!="")  {
    $id     = ForSqlInjection($_GET['id']);
    $sql    = "select * from form_formfield_showtype where id = '$id'";
    $rsf     = $db->Execute($sql);
    $RS = [];
    $RS['status'] = "OK";
    $RS['data'] = $rsf->fields;
    $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    print json_encode($RS);
    exit;
}

if(($_GET['action']=="view_default")&&$_GET['id']!="")  {
    $id     = ForSqlInjection($_GET['id']);
    $sql    = "select * from form_formfield_showtype where ID = '$id'";
    $rsf     = $db->Execute($sql);
    $EditValue = $rsf->fields;
    $RS = [];
    $RS['status'] = "OK";
    $RS['data'] = $EditValue;
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
        $sql    = "update form_formfield_showtype set `$field` = '$value' where `$primary_key` = '$id'";
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
    foreach($selectedRows as $id) {
        $sql    = "delete from form_formfield_showtype where id = '$id'";
        $db->Execute($sql);
    }
    $RS = [];
    $RS['status'] = "OK";
    $RS['sql'] = $sql;
    $RS['msg'] = "Drop Item Success!";
    print json_encode($RS);
    exit;
}

$AddSql = " where 1=1 ";

$init_default_columns = [];

$columnsactions = [];
$columnsactions[] = ['action'=>'view_default','text'=>__('View'),'mdi'=>'mdi:eye-outline'];
$columnsactions[] = ['action'=>'edit_default','text'=>__('Edit'),'mdi'=>'mdi:pencil-outline'];
$columnsactions[] = ['action'=>'delete_array','text'=>__('Delete'),'mdi'=>'mdi:delete-outline','double_check'=>__('Do you want to delete this item?')];
$columnsactions[] = ['action'=>'edit_default_1','text'=>__('Copy'),'mdi'=>'mdi:content-copy','double_check'=>'Do you want to copy this item?'];
$init_default_columns[] = ['flex' => 0.1, 'minWidth' => 120, 'sortable' => false, 'field' => "actions", 'headerName' => __("Actions"), 'show'=>true, 'type'=>'actions', 'actions' => $columnsactions];

//$columnName = "id";           $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 80, 'maxWidth' => 80, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'input', 'renderCell' => NULL];
$columnName = "Name";           $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 250, 'maxWidth' => 400, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "LIST";           $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 250, 'maxWidth' => 400, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "ADD";            $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 400, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL ];
$columnName = "EDIT";           $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 400, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL ];
$columnName = "VIEW";           $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 400, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL ];
$columnName = "SortNumber";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "EnableFields";    $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 250, 'maxWidth' => 400, 'field' => $columnName, 'headerName' => __($columnName), 'editable'=>true, 'show'=>true, 'type'=>'string', 'renderCell' => NULL ];

$RS['init_default']['button_search']    = __("Search");
$RS['init_default']['button_add']       = __("Add");
$RS['init_default']['columns']          = $init_default_columns;
$RS['init_default']['columnsactions']   = $columnsactions;

$columnName = "Name";       $searchField[] = ['label' => __($columnName), 'value' => $columnName];
$columnName = "LIST";       $searchField[] = ['label' => __($columnName), 'value' => $columnName];
$columnName = "ADD";        $searchField[] = ['label' => __($columnName), 'value' => $columnName];
$columnName = "EDIT";       $searchField[] = ['label' => __($columnName), 'value' => $columnName];

$RS['init_action']['action']        = "init_default";
$RS['init_action']['actionValue']   = "";
$RS['init_action']['id']            = 999; //NOT USE THIS VALUE IN FRONT END

$RS['init_default']['searchFieldArray'] = $searchField;
$RS['init_default']['searchFieldText'] = __("Search Item");

$searchFieldName     = ForSqlInjection($_REQUEST['searchFieldName']);
$searchFieldValue    = ForSqlInjection($_REQUEST['searchFieldValue']);
if ($searchFieldName != "" && $searchFieldValue != "" && in_array($searchFieldName, $columnNames) ) {
    $AddSql .= " and (`$searchFieldName` like '%" . $searchFieldValue . "%')";
}

$RS['init_default']['filter'] = [];

$page       = intval($_REQUEST['page']);
$pageSize   = intval($_REQUEST['pageSize']);
if(!in_array($pageSize,[10,15,20,30,40,50,100]))  {
	$pageSize = 30;
}
$fromRecord = $page * $pageSize;

$sql    = "select count(*) AS NUM from form_formfield_showtype " . $AddSql . "";
$rs     = $db->Execute($sql);
$RS['init_default']['total'] = intval($rs->fields['NUM']);
$RS['init_default']['searchtitle']  = __("Field Show Type");
$RS['init_default']['primarykey'] = $columnNames[0];
if(!in_array($_REQUEST['sortColumn'], $columnNames)) {
    $_REQUEST['sortColumn'] = $columnNames[0];
}
if($_REQUEST['sortColumn']=="")   {
    $_REQUEST['sortColumn'] = "id";
}
if($_REQUEST['sortMethod']=="asc") {
    $orderby = "order by `".$_REQUEST['sortColumn']."` asc";
}
else {
    $orderby = "order by `".$_REQUEST['sortColumn']."` desc";
}

$ForbiddenSelectRow = [];
$ForbiddenViewRow   = [];
$ForbiddenEditRow   = [];
$ForbiddenDeleteRow = [];
$sql    = "select * from form_formfield_showtype " . $AddSql . " $orderby limit $fromRecord,$pageSize";
//print $sql;
$NewRSA = [];
$rs = $db->Execute($sql) or print $sql;
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $Line['id']         = intval($Line['id']);
    $NewRSA[]           = $Line;
    if(in_array($Line['TableName'],['data_user','data_department','role','form_formfield_showtype'])) {
        $ForbiddenSelectRow[] = $Line['id'];
        $ForbiddenDeleteRow[] = $Line['id'];
    }
}

$RS['init_default']['data'] = $NewRSA;
$RS['init_default']['ForbiddenSelectRow']   = $ForbiddenSelectRow;
$RS['init_default']['ForbiddenViewRow']     = $ForbiddenViewRow;
$RS['init_default']['ForbiddenEditRow']     = $ForbiddenEditRow;
$RS['init_default']['ForbiddenDeleteRow']   = $ForbiddenDeleteRow;

$RS['init_default']['params']   = ['FormGroup' => '', 'role' => '', 'status' => '', 'q' => ''];

$RS['init_default']['sql']      = $sql;
$RS['init_default']['ApprovalNodeFields']['DebugSql']   = "";
$RS['init_default']['ApprovalNodeFields']['Memo']       = "";


$RS['init_default']['rowdelete'] = [];
$RS['init_default']['rowdelete'][] = ["text"=>__("Delete Item"),"action"=>"delete_array","title"=>__("Delete Item"),"content"=>__("Do you really want to delete this item? This operation will delete table and data in Database."),"memoname"=>"","inputmust"=>false,"inputmusttip"=>"","submit"=>__("Confirm Delete"),"cancel"=>__("Cancel")];


$RS['add_default']['allFields']['Default']         = $allFieldsAdd;
$RS['add_default']['allFieldsMode']     = [['value'=>"Default", 'label'=>__("")]];
$RS['add_default']['defaultValues']     = $defaultValues;
$RS['add_default']['submitaction']      = "add_default_data";
$RS['add_default']['submittext']        = __("Submit");
$RS['add_default']['canceltext']        = __("Cancel");
$RS['add_default']['titletext']         = __("Create Field Show Type");
$RS['add_default']['titlememo']         = __("It is only provided for developers to use, if you do not understand the meaning, please do not modify it.");
$RS['add_default']['tablewidth']        = 550;
$RS['add_default']['submitloading'] = __("SubmitLoading");
$RS['add_default']['loading']       = __("Loading");

$RS['edit_default'] = $RS['add_default'];
$RS['edit_default']['allFields']['Default']        = $allFieldsEdit;
$RS['edit_default']['allFieldsMode']    = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default']['defaultValues']    = $defaultValues;
$RS['edit_default']['submitaction']     = "add_default_data";
$RS['edit_default']['submittext']       = __("Submit");
$RS['edit_default']['canceltext']       = __("Cancel");
$RS['edit_default']['titletext']        = __("Edit Field Show Type");
$RS['edit_default']['titlememo']        = __("It is only provided for developers to use, if you do not understand the meaning, please do not modify it.");
$RS['edit_default']['tablewidth']       = 550;
$RS['edit_default']['submitloading']    = __("SubmitLoading");
$RS['edit_default']['loading']          = __("Loading");


$RS['edit_default_1']['allFields']['Default']  = $allFieldsEdit;
$RS['edit_default_1']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default_1']['defaultValues']  = $defaultValues;
$RS['edit_default_1']['dialogContentHeight']  = "90%";
$RS['edit_default_1']['submitaction']  = "edit_default_1_data";
$RS['edit_default_1']['submittext']    = __("Submit");
$RS['edit_default_1']['canceltext']    = __("Cancel");
$RS['edit_default_1']['titletext']    = __("Copy Item");
$RS['edit_default_1']['tablewidth']  = 550;

$RS['view_default'] = $RS['add_default'];
$RS['view_default']['titletext']        = __("View Field Show Type");

$RS['export_default'] = [];
$RS['import_default'] = [];

$RS['init_default']['rowHeight']    = 38;
$RS['init_default']['dialogMaxWidth']   = "md";
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



