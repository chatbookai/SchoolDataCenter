<?php
header("Content-Type: application/json");
require_once('cors.php');
require_once('include.inc.php');

CheckAuthUserLoginStatus();
CheckAuthUserRoleHaveMenu(0, "/form/formname");


$columnNames = [];
$sql = "show columns from form_formname";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $columnNames[] = $Line['Field'];
}

//新建页面时的启用字段列表
$allFieldsAdd = [];
$allFieldsAdd[] = ['name' => 'TableName', 'show'=>true, 'type'=>'input', 'label' => __('TableName'), 'value' => '', 'placeholder' => 'Input your table name', 'helptext' => 'Only accepted lower case letters', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false,'min'=>2,'max'=>50,'format'=>'onlylowerletter','invalidtext'=>__('Only accepted lower case letters')]];
$allFieldsAdd[] = ['name' => 'FullName', 'show'=>true, 'type'=>'input', 'label' => __('FullName'), 'value' => '', 'placeholder' => 'Readable name, e.g. Chinese name', 'helptext' => 'Readable name, e.g. Chinese name', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false,'min'=>2,'max'=>50]];
$FormGroup = [];
$FormGroup[] = ['value'=>'System', 'label'=>__('System')];
$FormGroup[] = ['value'=>'User Create', 'label'=>__('User Create')];
$FormGroup[] = ['value'=>'Student', 'label'=>__('Student')];
$FormGroup[] = ['value'=>'中职数据标准', 'label'=>__('中职数据标准')];
$FormGroup[] = ['value'=>'高职数据标准', 'label'=>__('高职数据标准')];
$allFieldsAdd[] = ['name' => 'FormGroup', 'show'=>true, 'type'=>'select', 'options'=>$FormGroup, 'label' => __('FormGroup'), 'value' => $FormGroup[0], 'placeholder' => '', 'helptext' => 'Form group', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
foreach($allFieldsAdd as $ITEM) {
    $defaultValues[$ITEM['name']] = $ITEM['value'];
}

//编辑页面时的启用字段列表
$allFieldsEdit = [];
$allFieldsEdit[] = ['name' => 'TableName', 'show'=>true, 'type'=>'input', 'label' => __('TableName'), 'value' => '', 'placeholder' => '', 'helptext' => 'Readonly for tablename', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => true]];
$allFieldsEdit[] = ['name' => 'FullName', 'show'=>true, 'type'=>'input', 'label' => __('FullName'), 'value' => '', 'placeholder' => 'Readable name, e.g. Chinese name', 'helptext' => 'Readable name, e.g. Chinese name', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$FormGroup = [];
$FormGroup[] = ['value'=>'System', 'label'=>'System'];
$FormGroup[] = ['value'=>'User Create', 'label'=>'User Create'];
$FormGroup[] = ['value'=>'Student', 'label'=>'Student'];
$FormGroup[] = ['value'=>'中职数据标准', 'label'=>__('中职数据标准')];
$FormGroup[] = ['value'=>'高职数据标准', 'label'=>__('高职数据标准')];
$allFieldsEdit[] = ['name' => 'FormGroup', 'show'=>true, 'type'=>'select', 'options'=>$FormGroup, 'label' => __('FormGroup'), 'value' => $FormGroup[0], 'placeholder' => 'Form group', 'helptext' => 'Form group', 'rules' => ['required' => true,'disabled' => false]];
foreach($allFieldsEdit as $ITEM) {
    $defaultValues[$ITEM['name']] = $ITEM['value'];
}
if($_GET['action']=="add_default_data"&&$_POST['TableName']!="")  {
    $MetaTables = $db->MetaTables();
    $TableName = strtolower($_POST['TableName']);
    $FullName = $_POST['FullName'];
    if(substr($TableName,0,5)!="data_")   {
        $TableName = "data_".$TableName;
    }
    $_POST['TableName'] = $TableName;
    if($TableName!="" && $TableName!="data_" && in_array($TableName,$MetaTables)) {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = "Table exists";
        print json_encode($RS);
        exit;
    }
    $sql = "CREATE TABLE `".$TableName."` ( `id` int(11) NOT NULL AUTO_INCREMENT,PRIMARY KEY (`id`)) ENGINE=Innodb  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='".$FullName."' AUTO_INCREMENT=1 ;";
    $rs = $db->Execute($sql);
    if(!$rs->EOF) {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = "Table Create Failed";
        $RS['sql'] = $sql;
        print json_encode($RS);
        exit;
    }
    $IsExecutionSQL = 0;
    $NewArray = [];
    foreach($allFieldsAdd as $Line) {
        $NewArray[$Line['name']] = $_POST[$Line['name']];
        if($_POST[$Line['name']]!="")  {
            $IsExecutionSQL = 1;
        }
    }
    $NewArray['Creator'] = "admin";
    $NewArray['CreateTime'] = date("Y-m-d H:i:s");
    $KEYS = array_keys($NewArray);
    $VALUES = array_values($NewArray);
    if($IsExecutionSQL)   {
        $sql = "insert into form_formname(".join(',',$KEYS).") values('".join("','",$VALUES)."');";
        $rs = $db->Execute($sql);
        if($rs->EOF) {
            $RS['status'] = "OK";
            $RS['msg'] = "Add Data Success!";
            $RS['sql'] = $sql;
            print json_encode($RS);
            exit;
        }
        else {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['msg'] = __("sql execution failed");
            $RS['db'] = $db;
            $RS['sql'] = $sql;
            $RS['_GET'] = $_GET;
            $RS['_POST'] = $_POST;
            print json_encode($RS);
            exit;
        }
    }
}

if(($_GET['action']=="edit_default"||$_GET['action']=="edit_default_1")&&$_GET['id']!="")  {
    $id     = ForSqlInjection($_GET['id']);
    $sql    = "select * from form_formname where ID = '$id'";
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
    $sql    = "select * from form_formname where ID = '$id'";
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

if($_GET['action']=="edit_default_data"&&$_GET['id']!="")  {
    $NewArray = [];
    foreach($allFieldsAdd as $Line) {
        $NewArray[] = $Line['name']."='".str_replace("'","&#39",$_POST[$Line['name']])."'";
        if($_POST[$Line['name']]!="")  {
            $IsExecutionSQL = 1;
        }
    }
    $id     = ForSqlInjection($_GET['id']);
    $sql    = "update form_formname set ".join(',', $NewArray)." where ID = '$id' ";
    $rs     = $db->Execute($sql);
    if($rs->EOF) {
        $RS = [];
        $RS['status'] = "OK";
        $RS['data'] = $rs->fields;
        $RS['sql'] = $sql;
        $RS['msg'] = __("Update Success");
        print json_encode($RS);
        exit;
    }
    else {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['sql'] = $sql;
        $RS['msg'] = "Update Error!";
        print json_encode($RS);
        exit;
    }
}

//CopyFormAndFlowByID($ID=33);exit;
function CopyFormAndFlowByID($ID)  {
    global $db;
    $db->StartTrans();
    //F494-DF8C
    if($_POST['TableName']=="" || $_POST['FullName']=="") {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['_POST'] = $_POST;
        $RS['msg'] = __("TableName and FullName Can Not Be Empty");
        print json_encode($RS);
        exit;
    }
    $MetaTables = $db->MetaTables();
    $TableName = strtolower($_POST['TableName']);
    $FullName = $_POST['FullName'];
    if(substr($TableName,0,5)!="data_")   {
        $TableName = "data_".$TableName;
    }
    $_POST['TableName'] = $TableName;
    if($TableName!="" && $TableName!="data_" && in_array($TableName,$MetaTables)) {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['msg'] = "Table exists";
        print json_encode($RS);
        exit;
    }
    //form_formname
    $sql    = "select * from form_formname where id='$ID'";
    $rs     = $db->Execute($sql);
    $Element = $rs->fields;

    //Copy Table Structure
    $sql = "CREATE TABLE ".$_POST['TableName']." LIKE ".$Element['TableName'].";";
    $db->Execute($sql) or print $sql;
    $FullName = $Element['FullName'];

    //Copy form_formname
    $Element['id'] = null;
    $Element['TableName'] = $_POST['TableName'];
    $Element['FullName'] = $_POST['FullName'];
    $Element['FormGroup'] = $_POST['FormGroup'];
    [$rs,$sql] = InsertOrUpdateTableByArray("form_formname",$Element,'id',0,"Insert");
    if($rs->EOF) {
        //OK
        $NewFormId = $db->Insert_ID();
        //form_formfield
        $sql    = "select * from form_formfield where FormId='$ID'";
        $rs     = $db->Execute($sql);
        $rs_a   = $rs->GetArray();
        foreach($rs_a as $Element)  {
            $Element['id']          = null;
            $Element['FormId']      = $NewFormId;
            $Element['FormName']    = $_POST['TableName'];
            [$rs,$sql] = InsertOrUpdateTableByArray("form_formfield",$Element,'id',0,"Insert");
            if($rs->EOF) {
                //OK
            }
        }
        //form_formflow
        $sql    = "select * from form_formflow where FormId='$ID'";
        $rs     = $db->Execute($sql);
        $rs_a = $rs->GetArray();
        foreach($rs_a as $Element)  {
            $Setting = $Element['Setting'];
            $SettingMap = unserialize(base64_decode($Setting));
            foreach($SettingMap as $SettingKey=>$SettingValue) {
                $SettingMap[$SettingKey] = str_replace($FullName,$_POST['FullName'],$SettingValue);
            }
            $Element['Setting']     = base64_encode(serialize($SettingMap));
            $Element['id']          = null;
            $Element['FormId']      = $NewFormId;
            [$rs,$sql]  = InsertOrUpdateTableByArray("form_formflow",$Element,'id',0,"Insert");
            if($rs->EOF) {
                //OK
            }
        }

    }
    if ($db->HasFailedTrans()) {
        $db->FailTrans();
        //print_R("HasFailedTrans");
        return false;
    } else {
        $db->CompleteTrans();
        //print_R("CompleteTrans");
        return true;
    }

}

if($_GET['action']=="edit_default_1_data"&&$_GET['id']!="")  {
    $NewArray = [];
    foreach($allFieldsAdd as $Line) {
        $NewArray[] = $Line['name']."='".str_replace("'","&#39",$_POST[$Line['name']])."'";
        if($_POST[$Line['name']]!="")  {
            $IsExecutionSQL = 1;
        }
    }
    $id     = ForSqlInjection($_GET['id']);
    if($IsExecutionSQL)  {
        $CopyFormAndFlowByID = CopyFormAndFlowByID($id);
    }
    if($CopyFormAndFlowByID) {
        $RS = [];
        $RS['status'] = "OK";
        $RS['data'] = $rs->fields;
        $RS['sql'] = $sql;
        $RS['msg'] = __("Copy Form And Flow Success");
        print json_encode($RS);
        exit;
    }
    else {
        $RS = [];
        $RS['status'] = "ERROR";
        $RS['sql'] = $sql;
        $RS['msg'] = __("Copy Form And Flow Failed");
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
        $sql    = "update form_formname set $field = '$value' where $primary_key = '$id'";
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
        $sql    = "select TableName from form_formname where $primary_key = '".$selectedRows[0]."'";
        $rs = $db->Execute($sql);
        $TableName = $rs->fields['TableName'];
        $MetaTables = $db->MetaTables();
        if($TableName!="" && in_array($TableName,$MetaTables)) {
            $sql = "DROP TABLE `".$TableName."`;";
            $rs = $db->Execute($sql);
            if(!$rs->EOF) {
                $RS = [];
                $RS['status'] = "ERROR";
                $RS['msg'] = "Table Drop Failed";
                $RS['sql'] = $sql;
                print json_encode($RS);
                exit;
            }
        }
        foreach($selectedRows as $id) {
            $sql    = "delete from form_formname where $primary_key = '$id'";
            $db->Execute($sql);
            $sql    = "select * from form_formflow where FormId='$id'";
            $rs     = $db->Execute($sql);
            $rs_a   = $rs->GetArray();
            foreach($rs_a as $Element)  {
                $sql    = "delete from data_menutwo where FlowId = '".$Element['id']."'";
                $db->Execute($sql);
            }
            $sql    = "delete from form_formflow where FormId = '$id'";
            $db->Execute($sql);
        }
        $RS = [];
        $RS['status'] = "OK";
        $RS['msg'] = "Drop Form and Table Success!";
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

if($_GET['action']=="option_multi_approval"||$_GET['action']=="option_multi_refuse"||$_GET['action']=="option_multi_change_status")  {
    $selectedRows  = ForSqlInjection($_POST['selectedRows']);
    $selectedRows = explode(',',$selectedRows);
    $primary_key = $columnNames[0];
    $multiReviewInputValue = $_POST['multiReviewInputValue'];
    if($selectedRows[0]!="") {
        foreach($selectedRows as $id) {
            $sql    = "update form_formname set FormGroup='Approval',MLS_BOARD='".$multiReviewInputValue."' where $primary_key = '$id'";
            $db->Execute($sql);
        }
        $RS = [];
        $RS['status'] = "OK";
        $RS['sql'] = $sql;
        $RS['msg'] = __("Submit Success");
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


$AddSql = " where 1=1 ";

$ColorArray[] = "success";
$ColorArray[] = "primary";
$ColorArray[] = "error";
$ColorArray[] = "info";
$ColorArray[] = "warning";
$ColumnColor['System']      = $ColorArray[0];
$ColumnColor['User Create'] = $ColorArray[1];

$ColorArray = [];
$ColorArray[] = ['icon'=>'mdi:laptop','color'=>'error.main'];
$ColorArray[] = ['icon'=>'mdi:cog-outline','color'=>'warning.main'];
$ColorArray[] = ['icon'=>'mdi:pencil-outline','color'=>'info.main'];
$ColorArray[] = ['icon'=>'mdi:chart-donut','color'=>'success.main'];
$ColorArray[] = ['icon'=>'mdi:account-outline','color'=>'primary.main'];
$ColumnColor['System']      = $ColorArray[2];
$ColumnColor['User Create'] = $ColorArray[3];
$ColumnColor['Student']     = $ColorArray[3];
$ColumnColor['中职数据标准'] = $ColorArray[4];
$ColumnColor['高职数据标准'] = $ColorArray[5];

$columnsactions = [];
$columnsactions[] = ['action'=>'edit_default','text'=>__('Edit'),'mdi'=>'mdi:pencil-outline'];
$columnsactions[] = ['action'=>'delete_array','text'=>__('Delete'),'mdi'=>'mdi:delete-outline','double_check'=>__('Do you want to delete this item?')];
$columnsactions[] = ['action'=>'edit_default_1','text'=>__('Copy'),'mdi'=>'mdi:content-copy','double_check'=>'Do you want to copy this form and flow?'];
$init_default_columns[]        = ['flex' => 0.1, 'minWidth' => 150, 'sortable' => false, 'field' => "actions", 'headerName' => __("Actions"), 'show'=>true, 'type'=>'actions', 'actions' => $columnsactions];

$columnName = "id";             $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 80, 'maxWidth' => 80, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "TableName";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 250, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "FullName";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'editable'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "DesignForm";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'url', 'href' => "formname/formfield/?FormId=", "urlmdi"=>"mdi:chart-donut",'urlcolor'=>'success.main', "target"=>"", 'renderCell' => NULL];
$columnName = "DesignFlow";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'url', 'href' => "formname/formflow/?FormId=", "urlmdi"=>"mdi:cog-outline",'urlcolor'=>'warning.main', "target"=>"", 'renderCell' => NULL];
$columnName = "FormGroup";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];

$columnName = "TableName";      $searchField[] = ['label' => __($columnName), 'value' => $columnName];
$columnName = "FullName";      $searchField[] = ['label' => __($columnName), 'value' => $columnName];

$RS['init_default']['button_search']    = __("Search");
$RS['init_default']['button_add']       = __("Add");
$RS['init_default']['columns']          = $init_default_columns;
$RS['init_default']['columnsactions']   = $columnsactions;

$RS['init_action']['action']        = "init_default";
$RS['init_action']['actionValue']   = "";
$RS['init_action']['id']            = 999; //NOT USE THIS VALUE IN FRONT END

$RS['init_default']['searchFieldArray'] = $searchField;
$RS['init_default']['searchFieldText']  = __("Search Item");

$searchFieldName     = ForSqlInjection($_REQUEST['searchFieldName']);
$searchFieldValue    = ForSqlInjection($_REQUEST['searchFieldValue']);
if ($searchFieldName != "" && $searchFieldValue != "" && in_array($searchFieldName, $columnNames) ) {
    $AddSql .= " and ($searchFieldName like '%" . $searchFieldValue . "%')";
}

$sql        = "select count(*) as NUM from form_formname";
$rs         = $db->Execute($sql);
$ALL_NUM    = intval($rs->fields['NUM']);

$sql = "select FormGroup as name, FormGroup as value, count(*) AS num from form_formname where FormGroup!='' group by FormGroup";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
array_unshift($rs_a,['name'=>__('All Data'), 'value'=>'All Data', 'num'=>$ALL_NUM]);
$RS['init_default']['filter'][] = ['name' => 'FormGroup', 'text' => __('FormGroup'), 'list' => $rs_a, 'selected' => "All Data"];

$FormGroup = ForSqlInjection($_REQUEST['FormGroup']);
if ($FormGroup != "" && $FormGroup != "All Data") {
    $AddSql .= " and (FormGroup = '" . $FormGroup . "')";
}
else if ($FormGroup == "") {
    //$AddSql .= " and (FormGroup = '" . $rs_a[1]['name'] . "')";
}


$page       = intval($_REQUEST['page']);
$pageSize   = intval($_REQUEST['pageSize']);
if(!in_array($pageSize,[10,20,30,40,50,100,200,500]))  {
	$pageSize = 30;
}
$fromRecord = $page * $pageSize;

$sql    = "select count(*) AS NUM from form_formname " . $AddSql . "";
$rs     = $db->Execute($sql);
$RS['init_default']['total'] = intval($rs->fields['NUM']);
$RS['init_default']['searchtitle']  = __("Form Management");
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
$ForbiddenViewRow = [];
$ForbiddenEditRow = [];
$ForbiddenDeleteRow = [];
$sql    = "select * from form_formname " . $AddSql . " $orderby limit $fromRecord,$pageSize";
//print $sql;
$NewRSA = [];
$rs = $db->Execute($sql) or print $sql;
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $Line['id']         = intval($Line['id']);
    $Line['DesignForm'] = __("Design Form");
    $Line['DesignFlow'] = __("Design Flow");
    $NewRSA[] = $Line;
    if(in_array($Line['TableName'],['data_user','data_department','data_role','data_unit','data_interface','data_menuone','data_menutwo','form_formflow'])) {
        $ForbiddenSelectRow[] = $Line['id'];
        //$ForbiddenViewRow[] = $Line['id'];
        //$ForbiddenEditRow[] = $Line['id'];
        $ForbiddenDeleteRow[] = $Line['id'];
    }
}

$RS['init_default']['data'] = $NewRSA;
$RS['init_default']['ForbiddenSelectRow']   = $ForbiddenSelectRow;
$RS['init_default']['ForbiddenViewRow']     = $ForbiddenViewRow;
$RS['init_default']['ForbiddenEditRow']     = $ForbiddenEditRow;
$RS['init_default']['ForbiddenDeleteRow']   = $ForbiddenDeleteRow;

$RS['init_default']['params'] = ['FormGroup' => '', 'role' => '', 'status' => '', 'q' => ''];

$RS['init_default']['sql'] = $sql;
$RS['init_default']['ApprovalNodeFields']['DebugSql']   = "";
$RS['init_default']['ApprovalNodeFields']['Memo']       = "";


$RS['init_default']['rowdelete'] = [];
$RS['init_default']['rowdelete'][] = ["text"=>__("Delete Item"),"action"=>"delete_array","title"=>__("Delete Item"),"content"=>__("Do you really want to delete this item? This operation will delete table and data in Database."),"memoname"=>"","inputmust"=>false,"inputmusttip"=>"","submit"=>__("Confirm Delete"),"cancel"=>__("Cancel")];


$RS['add_default']['allFields']['Default']  = $allFieldsAdd;
$RS['add_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['add_default']['defaultValues']  = $defaultValues;
$RS['add_default']['dialogContentHeight']  = "90%";
$RS['add_default']['submitaction']  = "add_default_data";
$RS['add_default']['componentsize'] = "medium";
$RS['add_default']['submittext']    = __("Submit");
$RS['add_default']['canceltext']    = __("Cancel");
$RS['add_default']['titletext']   = __("Create Form");
$RS['add_default']['tablewidth']  = 550;
$RS['add_default']['submitloading'] = __("SubmitLoading");
$RS['add_default']['loading']       = __("Loading");



$RS['edit_default'] = $RS['add_default'];
$RS['edit_default']['allFields']['Default']  = $allFieldsEdit;
$RS['edit_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default']['defaultValues']  = $defaultValues;
$RS['edit_default']['dialogContentHeight']  = "90%";
$RS['edit_default']['submitaction']  = "edit_default_data";
$RS['edit_default']['submittext']    = __("Submit");
$RS['edit_default']['canceltext']    = __("Cancel");
$RS['edit_default']['titletext']    = __("Edit Form");
$RS['edit_default']['titlememo']    = __("Manage All Forms in Table");
$RS['edit_default']['tablewidth']  = 550;
$RS['edit_default']['submitloading']    = __("SubmitLoading");
$RS['edit_default']['loading']          = __("Loading");

$allFieldsEdit1 = [];
$allFieldsEdit1[] = ['name' => 'TableName', 'show'=>true, 'type'=>'input', 'label' => __('TableName'), 'value' => '', 'placeholder' => '', 'helptext' => __('Input new table name'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>12, 'disabled' => false]];
$allFieldsEdit1[] = ['name' => 'FullName', 'show'=>true, 'type'=>'input', 'label' => __('FullName'), 'value' => '', 'placeholder' => 'Readable name, e.g. Chinese name', 'helptext' => __('New short name'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>12, 'disabled' => false]];
$FormGroup = [];
$FormGroup[] = ['value'=>'System', 'label'=>'System'];
$FormGroup[] = ['value'=>'User Create', 'label'=>'User Create'];
$FormGroup[] = ['value'=>'Student', 'label'=>'Student'];
$FormGroup[] = ['value'=>'中职数据标准', 'label'=>__('中职数据标准')];
$FormGroup[] = ['value'=>'高职数据标准', 'label'=>__('高职数据标准')];
$allFieldsEdit1[] = ['name' => 'FormGroup', 'show'=>true, 'type'=>'select', 'options'=>$FormGroup, 'label' => __('FormGroup'), 'value' => $FormGroup[0], 'placeholder' => 'Form group', 'helptext' => 'Form group', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12, 'disabled' => false]];
foreach($allFieldsEdit1 as $ITEM) {
    $defaultValues1[$ITEM['name']] = $ITEM['value'];
}
$RS['edit_default_1']['allFields']['Default']  = $allFieldsEdit1;
$RS['edit_default_1']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default_1']['defaultValues']  = $defaultValues1;
$RS['edit_default_1']['dialogContentHeight']  = "90%";
$RS['edit_default_1']['submitaction']  = "edit_default_1_data";
$RS['edit_default_1']['submittext']    = __("Submit");
$RS['edit_default_1']['canceltext']    = __("Cancel");
$RS['edit_default_1']['titletext']    = __("Copy Form And Flow");
$RS['edit_default_1']['tablewidth']  = 550;


$RS['view_default'] = $RS['add_default'];
$RS['view_default']['titletext']  = __("View Form");
$RS['view_default']['titlememo']  = __("View All Forms in Table");

$RS['export_default'] = [];
$RS['import_default'] = [];

$RS['init_default']['rowHeight']    = 38;
$RS['init_default']['dialogMaxWidth']   = "md";
$RS['init_default']['timeline']     = time();
$RS['init_default']['pageNumber']   = $pageSize;
$RS['init_default']['pageCount']    = ceil($RS['init_default']['total']/$pageSize);
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



