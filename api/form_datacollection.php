<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');

CheckAuthUserLoginStatus();
CheckAuthUserRoleHaveMenu(0, "/form/formname");


$columnNames = [];
$sql = "show columns from form_formname";
$rs = $db->CacheExecute(10, $sql);
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $columnNames[] = $Line['Field'];
}
/*
//新建页面时的启用字段列表
$allFieldsAdd = [];
$allFieldsAdd[] = ['name' => 'TableName', 'show'=>true, 'type'=>'input', 'label' => __('TableName'), 'value' => '', 'placeholder' => 'Input your table name', 'helptext' => 'Only accepted lower case letters', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false,'min'=>2,'max'=>50,'format'=>'onlylowerletter','invalidtext'=>__('Only accepted lower case letters')]];
$allFieldsAdd[] = ['name' => 'FullName', 'show'=>true, 'type'=>'input', 'label' => __('FullName'), 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false,'min'=>2,'max'=>50]];
$DataSource = [];
$sql    = "select * from data_datasource where 连接状态='正常' order by id asc";
$rs = $db->Execute($sql) or print $sql;
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $DataSource[] = ['value'=>$Line['id'], 'label'=>$Line['连接池名称']];
}
$allFieldsEdit[] = ['name' => 'datasource', 'show'=>true, 'type'=>'select', 'options'=>$DataSource, 'label' => __('datasource'), 'value' => $DataSource[0]['id'], 'placeholder' => '', 'helptext' => '选择一个数据源', 'rules' => ['required' => true,'disabled' => false]];
foreach($allFieldsAdd as $ITEM) {
    $defaultValues[$ITEM['name']] = $ITEM['value'];
}
*/

//编辑页面时的启用字段列表
$allFieldsEdit = [];
$allFieldsEdit[] = ['name' => 'TableName', 'show'=>true, 'type'=>'input', 'label' => __('TableName'), 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => true]];
$allFieldsEdit[] = ['name' => 'FullName', 'show'=>true, 'type'=>'input', 'label' => __('FullName'), 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$DataSource     = [];
$DataSource[]   = ['value'=>0, 'label'=>"手动管理数据"];
$sql    = "select * from data_datasource where 连接状态='正常' order by id asc";
$rs = $db->Execute($sql) or print $sql;
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $DataSource[] = ['value'=>$Line['id'], 'label'=>$Line['连接池名称']];
}
$allFieldsEdit[] = ['name' => '数据源', 'show'=>true, 'type'=>'select', 'options'=>$DataSource, 'label' => __('datasource'), 'value' => $DataSource[0]['value'], 'placeholder' => '', 'helptext' => '选择一个数据源', 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];

$数据同步方式   = [];
$数据同步方式[] = ['value'=>'增量同步', 'label'=>'增量同步'];
$数据同步方式[] = ['value'=>'全量同步', 'label'=>'全量同步'];
$allFieldsEdit[] = ['name' => '数据同步方式', 'show'=>true, 'type'=>'select', 'options'=>$数据同步方式, 'label' => "数据同步方式", 'value' => $数据同步方式[0]['value'], 'placeholder' => '', 'helptext' => '数据同步方式', 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];

$数据同步周期   = [];
$数据同步周期[] = ['value'=>'每天', 'label'=>'每天'];
$数据同步周期[] = ['value'=>'每小时', 'label'=>'每小时'];
$allFieldsEdit[] = ['name' => '数据同步周期', 'show'=>true, 'type'=>'select', 'options'=>$数据同步周期, 'label' => "数据同步周期", 'value' => $数据同步周期[0]['value'], 'placeholder' => '', 'helptext' => '数据同步周期', 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false]];

$allFieldsEdit[] = ['name' => '远程数据表', 'show'=>true, 'type'=>'input', 'label' => "远程数据表", 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsEdit[] = ['name' => '远程数据表主键', 'show'=>true, 'type'=>'input', 'label' => "远程数据表主键", 'value' => '', 'placeholder' => '如果没有主键,则不用填写.', 'helptext' => '', 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false]];

$allFieldsEdit[] = ['name' => '数据类', 'show'=>true, 'type'=>'input', 'label' => "数据类", 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsEdit[] = ['name' => '数据子类', 'show'=>true, 'type'=>'input', 'label' => "数据子类", 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsEdit[] = ['name' => '数据表序号', 'show'=>true, 'type'=>'input', 'label' => "数据表序号", 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsEdit[] = ['name' => '数据表英文名称', 'show'=>true, 'type'=>'input', 'label' => "数据表英文名称", 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsEdit[] = ['name' => '数据表中文名称', 'show'=>true, 'type'=>'input', 'label' => "数据表中文名称", 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsEdit[] = ['name' => '主要来源', 'show'=>true, 'type'=>'input', 'label' => "主要来源", 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsEdit[] = ['name' => '上报频率', 'show'=>true, 'type'=>'input', 'label' => "上报频率", 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsEdit[] = ['name' => '上报范围', 'show'=>true, 'type'=>'input', 'label' => "上报范围", 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];

foreach($allFieldsEdit as $ITEM) {
    $defaultValues[$ITEM['name']] = $ITEM['value'];
}

if($_GET['action']=="add_default_data"&&$_POST['TableName']!=""&&0)  {
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
    
    $FieldNameArray             = $allFieldsEdit;
    $ApprovalNodeFieldsHidden   = [];
    for($X=0;$X<sizeof($FieldNameArray);$X=$X+2)        {
        $FieldName1 = $FieldNameArray[$X]['name'];
        if($FieldNameArray[$X]['type']=="autocomplete" && $FieldNameArray[$X]['code']!="") {
            $FieldName1 = $FieldNameArray[$X]['code'];
        }
        $FieldName2 = $FieldNameArray[$X+1]['name'];
        if($FieldNameArray[$X+1]['type']=="autocomplete" && $FieldNameArray[$X+1]['code']!="") {
            $FieldName2 = $FieldNameArray[$X+1]['code'];
        }
        $RowData = [];
        if(!in_array($FieldName1,$ApprovalNodeFieldsHidden) && $FieldName1!="") {
            $RowData[0]['Name']     = $FieldName1;
            $RowData[0]['Value']    = $rsf->fields[$FieldName1];
            $RowData[0]['FieldArray']   = $FieldNameArray[$X];
        }
        if(!in_array($FieldName2,$ApprovalNodeFieldsHidden) && $FieldName2!="") {
            $RowData[1]['Name']     = $FieldName2;
            $RowData[1]['Value']    = $rsf->fields[$FieldName2];
            $RowData[1]['FieldArray']   = $FieldNameArray[$X+1];
        }
        if(sizeof($RowData)>0) {
            $NewTableRowData[] = $RowData;
        }
    }
    $RS = [];
    $RS['newTableRowData']          = $NewTableRowData;

    $RS['status'] = "OK";
    $RS['data'] = $rsf->fields;
    $RS['sql'] = $sql;
    $RS['msg'] = __("Get Data Success");
    print json_encode($RS);
    exit;  
}


if($_GET['action']=="edit_default_data"&&$_GET['id']!="")  {
    $数据源             = intval($_POST['数据源']);
    $远程数据表         = ForSqlInjection($_POST['远程数据表']);
    $远程数据表主键     = ForSqlInjection($_POST['远程数据表主键']);
    $数据同步方式       = ForSqlInjection($_POST['数据同步方式']);
    $远程数据库信息     = returntablefield("data_datasource","id",$数据源,"数据库主机,数据库用户名,数据库密码,数据库名称");
    if($远程数据库信息['数据库用户名']!="")    {
        $db_remote = NewADOConnection($DB_TYPE='mysqli');
        $db_remote->connect($远程数据库信息['数据库主机'], $远程数据库信息['数据库用户名'], DecryptID($远程数据库信息['数据库密码']), $远程数据库信息['数据库名称']);
        $db_remote->Execute("Set names utf8;");
        $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
        if($db_remote->database==$远程数据库信息['数据库名称']) {
            $MetaColumnNamesTemp    = $db_remote->MetaColumnNames($远程数据表);
            if(!$MetaColumnNamesTemp) {
                $RS = [];
                $RS['status'] = "ERROR";
                $RS['msg'] = "【远程数据表】在远程数据库中不存在";
                print json_encode($RS);
                exit;  
            }
            //print_R($MetaColumnNamesTemp);
    	    $远程数据表结构      = array_values($MetaColumnNamesTemp);
            //print_R($远程数据表结构);
            if(is_array($远程数据表结构) && $远程数据表结构[0]!="")     {
                if($远程数据表主键!="" && !in_array($远程数据表主键, $远程数据表结构))     {
                    $RS = [];
                    $RS['status'] = "ERROR";
                    $RS['远程数据表主键'] = $远程数据表主键;
                    $RS['远程数据表结构'] = $远程数据表结构;
                    $RS['msg'] = "您输入的【远程数据表主键】在【远程数据表】中不存在,请输入正确的【远程数据表主键】";
                    print json_encode($RS);
                    exit;  
                }
                if($数据同步方式=="增量同步" && $远程数据表主键=='')     {
                    $RS = [];
                    $RS['status'] = "ERROR";
                    $RS['msg'] = "增量同步时,要求必须设置【远程数据表主键】";
                    print json_encode($RS);
                    exit;  
                }
            }
            else {
                $RS = [];
                $RS['status'] = "ERROR";
                $RS['msg'] = "您输入的数据表名称在指定数据源中不存在";
                print json_encode($RS);
                exit;  
            }
        }
        else {

        }
    }
    $NewArray = [];
    foreach($allFieldsEdit as $Line) {
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
        //加入数据同步规则数据表
        $sql    = "select * from form_formname where ID = '$id'";
        $rs     = $db->Execute($sql);
        $Element = [];
        $Element['数据源']          = ForSqlInjection($_POST['数据源']);
        $Element['远程数据表']      = ForSqlInjection($_POST['远程数据表']);
        $Element['远程数据表主键']  = ForSqlInjection($_POST['远程数据表主键']);
        $Element['数据同步方式']    = ForSqlInjection($_POST['数据同步方式']);
        $Element['数据同步周期']    = ForSqlInjection($_POST['数据同步周期']);
        $Element['同步程序']        = '/plugins/plugin_data_datasource_lib.php?id=[id]';
        $Element['TableName']       = $rs->fields['TableName'];
        $Element['FullName']        = $rs->fields['FullName'];
        $Element['FormId']          = $rs->fields['id'];
        InsertOrUpdateTableByArray("data_datasyncedrules",$Element,"FormId",0);        
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

if($_GET['action']=="edit_default_1_data"&&$_GET['id']!="")  {
    $NewArray = [];
    foreach($allFieldsAdd as $Line) {
        $NewArray[] = $Line['name']."='".str_replace("'","&#39",$_POST[$Line['name']])."'";
        if($_POST[$Line['name']]!="")  {
            $IsExecutionSQL = 1;
        }
    }
    $id     = ForSqlInjection($_GET['id']);
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


$AddSql = " where 1=1 and FormGroup='中职数据标准' ";

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

$columnsactions     = [];
$columnsactions[]   = ['action'=>'view_default','text'=>__('View'),'mdi'=>'mdi:eye-outline'];
$columnsactions[]   = ['action'=>'edit_default','text'=>__('Edit'),'mdi'=>'mdi:pencil-outline'];
$init_default_columns[]        = ['flex' => 0.1, 'minWidth' => 150, 'sortable' => false, 'field' => "actions", 'headerName' => __("Actions"), 'show'=>true, 'type'=>'actions', 'actions' => $columnsactions];

$columnName = "id";             $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 80, 'maxWidth' => 80, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "TableName";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 250, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "FullName";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 250, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'editable'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "DesignDataSource";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'actionInRow', 'action' => "edit_default", "urlmdi"=>"mdi:database",'urlcolor'=>'info.main', "target"=>"", 'renderCell' => NULL];
$columnName = "DesignForm";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'url', 'href' => "formname/dataformfield/?FormId=", "urlmdi"=>"mdi:chart-donut",'urlcolor'=>'success.main', "target"=>"", 'renderCell' => NULL];
$columnName = "DesignFlow";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'url', 'href' => "formname/formflow/?FormId=", "urlmdi"=>"mdi:cog-outline",'urlcolor'=>'warning.main', "target"=>"", 'renderCell' => NULL];
$columnName = "数据源";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => $columnName, 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];
$columnName = "远程数据表";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => $columnName, 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];
$columnName = "数据子集";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => $columnName, 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];
$columnName = "数据类";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => $columnName, 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];
$columnName = "数据子类";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => $columnName, 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];
$columnName = "数据表中文名称";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => $columnName, 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];
$columnName = "主要来源";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => $columnName, 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];
$columnName = "上报频率";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => $columnName, 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];
$columnName = "上报范围";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 380, 'field' => $columnName, 'headerName' => $columnName, 'show'=>true, 'type'=>'input', 'renderCell' => NULL, "color"=>$ColumnColor];
$columnName = "TableName";      $searchField[] = ['label' => __($columnName), 'value' => $columnName];
$columnName = "FullName";      $searchField[] = ['label' => __($columnName), 'value' => $columnName];

$RS['init_default']['button_search']    = __("Search");
//$RS['init_default']['button_add']       = __("Add");
$RS['init_default']['columns']          = $init_default_columns;
$RS['init_default']['columnsactions']   = $columnsactions;

$RS['init_action']['action']        = "init_default";
$RS['init_action']['id']            = 999; //NOT USE THIS VALUE IN FRONT END

$RS['init_default']['searchFieldArray'] = $searchField;
$RS['init_default']['searchFieldText']  = __("Search Item");

$searchFieldName     = ForSqlInjection($_REQUEST['searchFieldName']);
$searchFieldValue    = ForSqlInjection($_REQUEST['searchFieldValue']);
if ($searchFieldName != "" && $searchFieldValue != "" && in_array($searchFieldName, $columnNames) ) {
    $AddSql .= " and ($searchFieldName like '%" . $searchFieldValue . "%')";
}

$sql        = "select count(*) as NUM from form_formname";
$rs         = $db->CacheExecute(10, $sql);
$ALL_NUM    = intval($rs->fields['NUM']);

$RS['init_default']['filter'] = [];

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
$rs     = $db->CacheExecute(10, $sql);
$RS['init_default']['total'] = intval($rs->fields['NUM']);
$RS['init_default']['searchtitle']  = "设计数据集";
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
    $Line['id']                 = intval($Line['id']);
    $Line['DesignDataSource']   = __("DesignDataSource");
    $Line['DesignForm']         = __("Design Form");
    $Line['DesignFlow']         = __("Design Flow");
    if($Line["数据源"]==0) {
        $Line["数据源"] = "手动管理数据";
    }
    else {
        $Line["数据源"] = returntablefield("data_datasource","id",$Line["数据源"],"连接池名称")['连接池名称'];
    }
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

/*
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
*/


$RS['edit_default'] = $RS['add_default'];
$RS['edit_default']['allFields']['Default']  = $allFieldsEdit;
$RS['edit_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default']['defaultValues']  = $defaultValues;
$RS['edit_default']['dialogContentHeight']  = "90%";
$RS['edit_default']['submitaction']  = "edit_default_data";
$RS['edit_default']['submittext']    = __("Submit");
$RS['edit_default']['canceltext']    = __("Cancel");
$RS['edit_default']['componentsize'] = "small";
$RS['edit_default']['titletext']    = "管理数据集";
$RS['edit_default']['tablewidth']  = 550;
$RS['edit_default']['submitloading']    = __("SubmitLoading");
$RS['edit_default']['loading']          = __("Loading");

$allFieldsEdit1 = [];
$allFieldsEdit1[] = ['name' => 'TableName', 'show'=>true, 'type'=>'input', 'label' => __('TableName'), 'value' => '', 'placeholder' => '', 'helptext' => __('Input new table name'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>12, 'disabled' => false]];
$allFieldsEdit1[] = ['name' => 'FullName', 'show'=>true, 'type'=>'input', 'label' => __('FullName'), 'value' => '', 'placeholder' => '', 'helptext' => __('New short name'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>12, 'disabled' => false]];
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


$RS['view_default']                     = $RS['edit_default'];
$RS['view_default']['defaultValues']    = $defaultValues;
$RS['view_default']['componentsize']    = "small";
$RS['view_default']['titletext']        = "查看数据集";

$RS['export_default'] = [];
$RS['import_default'] = [];

$RS['init_default']['rowHeight']        = 38;
$RS['init_default']['dialogMaxWidth']   = "md";
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



