<?php
header("Content-Type: application/json");
require_once('cors.php');
require_once('include.inc.php');

CheckAuthUserLoginStatus();
CheckAuthUserRoleHaveMenu(0, "/form/formname");

$externalId = intval($_REQUEST['externalId']);
$sql        = "select * from form_formname where id='$externalId'";
$rs         = $db->Execute($sql);
$FromInfo   = $rs->fields;
$TableName  = $FromInfo['TableName'];
$FullName   = $FromInfo['FullName'];

$sql            = "SELECT TABLE_TYPE FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$DB_DATABASE' AND  TABLE_NAME = '$TableName';";
$rsTableType    = $db->Execute($sql);
if($rsTableType->fields['TABLE_TYPE']=="BASE TABLE")  {
    $IsBaseTable = true;
}
else {
    $IsBaseTable = false;
}

$columnNames = [];
$sql = "show columns from form_formfield";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $columnNames[] = $Line['Field'];
}

//Force the SortNumber to a default value
$sql = "update form_formfield set SortNumber=id where SortNumber='0'";
$db->Execute($sql);

//新建页面时的启用字段列表
$allFieldsAdd = [];
$allFieldsAdd[] = ['name' => 'FieldName', 'show'=>true, 'type'=>'input', 'label' => __('Field Name'), 'value' => '', 'placeholder' => __('Field Name in Database'), 'helptext' => __('Field Name in Database'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4, 'disabled' => false,'min'=>1]];
$allFieldsAdd[] = ['name' => 'EnglishName', 'show'=>true, 'type'=>'input', 'label' => __('English Name'), 'value' => '', 'placeholder' => __('English Name. E.g. English Description'), 'helptext' => __('Readable Name.'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$allFieldsAdd[] = ['name' => 'ChineseName', 'show'=>true, 'type'=>'input', 'label' => __('Chinese Name'), 'value' => '', 'placeholder' => __('Chinese Name. E.g. Chinese Description'), 'helptext' => __('Readable Name.'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$sql = "select `FieldType` as value, `FieldType` as label from form_formfield_logictype order by SortNumber asc, id asc";
$rs = $db->Execute($sql);
$FieldType = $rs->GetArray();
$allFieldsAdd[] = ['name' => 'FieldType', 'show'=>true, 'type'=>'select', 'options'=>$FieldType, 'label' => __('Field Type'), 'value' => $FieldType[2]['value'], 'placeholder' => __('Field Type in Database'), 'helptext' => __('Field Type in Database'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>3,'disabled' => false]];

$sql            = "select `Name` as value, `Name` as label, EnableFields, DisableFields from form_formfield_showtype order by SortNumber asc, id asc";
$rs             = $db->Execute($sql);
$ShowType       = $rs->GetArray();
array_unshift($ShowType, ['value'=>'NewDict','label'=>__('NewDict'),'EnableFields'=>'NewDict','DisableFields'=>'']);
$EnableFields   = [];
$DisableFields  = [];
foreach ($ShowType as $Line) {
    $EnableFields[$Line['value']] = explode(',',$Line['EnableFields']);
    $DisableFields[$Line['value']] = explode(',',$Line['DisableFields']);
}
$allFieldsAdd[] = ['name' => 'ShowType_名称', 'code' => 'ShowType', 'show'=>true, 'type'=>'autocomplete', 'options'=>$ShowType, 'label' => __('Show Type'), 'value' => $ShowType[1]['value'], 'placeholder' => __('Show Type in UI'), 'helptext' => __('Show Type in UI'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>5,'disabled' => false], 'freeSolo'=>false, 'EnableFields'=>$EnableFields, 'DisableFields'=>$DisableFields];
$allFieldsAdd[] = ['name' => 'SortNumber', 'show'=>true, 'type'=>'number', 'label' => __('SortNumber'), 'value' => '0', 'placeholder' => __('Sort number in form'), 'helptext' => __('Sort number'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>2,'disabled' => false]];
$allFieldsAdd[] = ['name' => 'FieldDefault', 'show'=>true, 'type'=>'input', 'label' => __('Default'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>2,'disabled' => false]];

$allFieldsAdd[] = ['name' => 'NewDict', 'show'=>false, 'type'=>'input', 'label' => __('NewDict'), 'value' => '', 'placeholder' => __('Create New Dict Type From Here, use comma to split'), 'helptext' => __(''), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false,'min'=>0,'max'=>999]];

$allFieldsAdd[] = ['name' => 'TextareaRows', 'show'=>false, 'type'=>'number', 'label' => __('TextareaRows'), 'value' => '5', 'placeholder' => __('TextareaRows'), 'helptext' => __(''), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false,'min'=>5,'max'=>40]];

$allFieldsAdd[] = ['name' => 'Min', 'show'=>false, 'type'=>'number', 'label' => __('Min'), 'value' => '', 'placeholder' => __('Minimum input'), 'helptext' => __(''), 'rules' => ['required' => false,'xs'=>12, 'sm'=>2, 'disabled' => false,'min'=>0,'max'=>4]];
$allFieldsAdd[] = ['name' => 'Max', 'show'=>false, 'type'=>'number', 'label' => __('Max'), 'value' => '', 'placeholder' => __('Maximum input'), 'helptext' => __(''), 'rules' => ['required' => false,'xs'=>12, 'sm'=>2, 'disabled' => false,'min'=>0,'max'=>4]];
$FormulaMethod = [];
$FormulaMethod[] = ['value'=>'None', 'label'=>__('None')];
$FormulaMethod[] = ['value'=>'+', 'label'=>'+'];
$FormulaMethod[] = ['value'=>'-', 'label'=>'-'];
$FormulaMethod[] = ['value'=>'*', 'label'=>'*'];
$FormulaMethod[] = ['value'=>'/', 'label'=>'/'];
$allFieldsAdd[] = ['name' => 'FormulaMethod', 'show'=>false, 'type'=>'select', 'options'=>$FormulaMethod, 'label' => __('FormulaMethod'), 'value' => $FormulaMethod[0]['value'], 'placeholder' => __(''), 'helptext' => __(''), 'rules' => ['required' => false,'xs'=>12, 'sm'=>2,'disabled' => false]];
$allFieldsAdd[] = ['name' => 'FormulaMethodField', 'show'=>false, 'type'=>'input', 'label' => __('FormulaMethodField'), 'value' => '', 'placeholder' => __('FormulaMethodField'), 'helptext' => __(''), 'rules' => ['required' => false,'xs'=>12, 'sm'=>3, 'disabled' => false]];
$allFieldsAdd[] = ['name' => 'FormulaMethodTarget', 'show'=>false, 'type'=>'input', 'label' => __('FormulaMethodTarget'), 'value' => '', 'placeholder' => __('FormulaMethodTarget'), 'helptext' => __(''), 'rules' => ['required' => false,'xs'=>12, 'sm'=>3, 'disabled' => false]];

$allFieldsAdd[] = ['name' => 'StartDate', 'show'=>false, 'type'=>'date', 'label' => __('Start Date'), 'value' => '', 'placeholder' => __('Start Date'), 'helptext' => __('Start Date'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndDate', 'show'=>false, 'type'=>'date', 'label' => __('End Date'), 'value' => '', 'placeholder' => __('End Date'), 'helptext' => __('End Date'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultDate', 'show'=>false, 'type'=>'date', 'label' => __('Field Default Date'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'DateFormat', 'show'=>false, 'type'=>'input', 'label' => __('DateFormat'), 'value' => 'yyyyMMdd', 'placeholder' => __('DateFormat'), 'helptext' => __('DateFormat').":yyyy-MM-dd", 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'StartTime', 'show'=>false, 'type'=>'Time', 'label' => __('Start Time'), 'value' => '', 'placeholder' => __('Start Time'), 'helptext' => __('Start Time'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'HH:mm:ss','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndTime', 'show'=>false, 'type'=>'Time', 'label' => __('End Time'), 'value' => '', 'placeholder' => __('End Time'), 'helptext' => __('End Time'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'HH:mm:ss','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultTime', 'show'=>false, 'type'=>'Time', 'label' => __('Field Default Date Time'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'HH:mm:ss','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'TimeFormat', 'show'=>false, 'type'=>'input', 'label' => __('TimeFormat'), 'value' => 'HHmmss', 'placeholder' => __('TimeFormat'), 'helptext' => __('TimeFormat').":HH:mm:ss", 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'HH:mm:ss','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'StartDateTime', 'show'=>false, 'type'=>'datetime', 'label' => __('Start DateTime'), 'value' => '', 'placeholder' => __('Start DateTime'), 'helptext' => __('Start DateTime'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd HH:mm','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndDateTime', 'show'=>false, 'type'=>'datetime', 'label' => __('End DateTime'), 'value' => '', 'placeholder' => __('End DateTime'), 'helptext' => __('End DateTime'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd HH:mm','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultDateTime', 'show'=>false, 'type'=>'datetime', 'label' => __('Field Default Date Time'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd HH:mm:ss','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'DateTimeFormat', 'show'=>false, 'type'=>'input', 'label' => __('DateTimeFormat'), 'value' => 'Y-m-d H:i:s', 'placeholder' => __('DateTimeFormat'), 'helptext' => __('DateTimeFormat').":yyyy-MM-dd HH:mm:ss", 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM-dd HH:mm:ss','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'StartMonth', 'show'=>false, 'type'=>'month', 'label' => __('Start Month'), 'value' => '', 'placeholder' => __('Start Month'), 'helptext' => __('Start Month'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndMonth', 'show'=>false, 'type'=>'month', 'label' => __('End Month'), 'value' => '', 'placeholder' => __('End Month'), 'helptext' => __('End Month'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultMonth', 'show'=>false, 'type'=>'month', 'label' => __('Field Default Month'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'MonthFormat', 'show'=>false, 'type'=>'input', 'label' => __('MonthFormat'), 'value' => 'yyyyMM', 'placeholder' => __('MonthFormat'), 'helptext' => __('MonthFormat').":yyyy-MM", 'rules' => ['required' => false,'xs'=>12, 'sm'=>12, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-MM','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'StartYear', 'show'=>false, 'type'=>'year', 'label' => __('Start Year'), 'value' => '', 'placeholder' => __('Start Year'), 'helptext' => __('Start Year'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndYear', 'show'=>false, 'type'=>'year', 'label' => __('End Year'), 'value' => '', 'placeholder' => __('End Year'), 'helptext' => __('End Year'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultYear', 'show'=>false, 'type'=>'year', 'label' => __('Field Default Year'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'StartQuarter', 'show'=>false, 'type'=>'quarter', 'label' => __('Start Quarter'), 'value' => '', 'placeholder' => __('Start Quarter'), 'helptext' => __('Start Quarter'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'EndQuarter', 'show'=>false, 'type'=>'quarter', 'label' => __('End Quarter'), 'value' => '', 'placeholder' => __('End Quarter'), 'helptext' => __('End Quarter'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
$allFieldsAdd[] = ['name' => 'FieldDefaultQuarter', 'show'=>false, 'type'=>'quarter', 'label' => __('Field Default Quarter'), 'value' => '', 'placeholder' => __('Field default value, you can leave it blank'), 'helptext' => __('Default value, or blank'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4,'disabled' => false, 'nullable'=>true], 'dateFormat' => 'yyyy-QQQ','timeZone'=>'America/Los_Angeles'];

$allFieldsAdd[] = ['name' => 'Placeholder', 'show'=>true, 'type'=>'input', 'label' => __('Place holder'), 'value' => '', 'placeholder' => __('Place holder tip infor'), 'helptext' => __('Place holder tip infor'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];
$allFieldsAdd[] = ['name' => 'Helptext', 'show'=>true, 'type'=>'input', 'label' => __('Help text'), 'value' => '', 'placeholder' => __('Help text tip infor'), 'helptext' => __('Help text tip infor'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4, 'disabled' => false]];

$ColumnWidth = [];
$ColumnWidth[] = ['value'=>100, 'label'=>'100px'];
$ColumnWidth[] = ['value'=>110, 'label'=>'110px'];
$ColumnWidth[] = ['value'=>120, 'label'=>'120px'];
$ColumnWidth[] = ['value'=>130, 'label'=>'130px'];
$ColumnWidth[] = ['value'=>140, 'label'=>'140px'];
$ColumnWidth[] = ['value'=>150, 'label'=>'150px'];
$ColumnWidth[] = ['value'=>160, 'label'=>'160px'];
$ColumnWidth[] = ['value'=>170, 'label'=>'170px'];
$ColumnWidth[] = ['value'=>180, 'label'=>'180px'];
$ColumnWidth[] = ['value'=>190, 'label'=>'190px'];
$ColumnWidth[] = ['value'=>200, 'label'=>'200px'];
$ColumnWidth[] = ['value'=>210, 'label'=>'210px'];
$ColumnWidth[] = ['value'=>220, 'label'=>'220px'];
$ColumnWidth[] = ['value'=>240, 'label'=>'240px'];
$ColumnWidth[] = ['value'=>260, 'label'=>'260px'];
$ColumnWidth[] = ['value'=>280, 'label'=>'280px'];
$ColumnWidth[] = ['value'=>300, 'label'=>'300px'];
$ColumnWidth[] = ['value'=>320, 'label'=>'320px'];
$allFieldsAdd[] = ['name' => 'ColumnWidth', 'show'=>true, 'type'=>'select', 'options'=>$ColumnWidth, 'label' => __('Column Width'), 'value' => $ColumnWidth[5]['value'], 'placeholder' => __(''), 'helptext' => __('Column Width In List Page'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false]];


//$allFieldsAdd[] = ['name' => 'ColumnWidth', 'show'=>true, 'type'=>'number', 'label' => __('Column Width'), 'value' => '200', 'placeholder' => __('Column Width'), 'helptext' => __('Column Width In List Page'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4, 'disabled' => false,'min'=>0,'max'=>4]];

$IsFullWidth = [];
$IsFullWidth[] = ['value'=>'12', 'label'=>'12'];
$IsFullWidth[] = ['value'=>'6', 'label'=>'6'];
$IsFullWidth[] = ['value'=>'5', 'label'=>'5'];
$IsFullWidth[] = ['value'=>'4', 'label'=>'4'];
$IsFullWidth[] = ['value'=>'3', 'label'=>'3'];
$IsFullWidth[] = ['value'=>'2', 'label'=>'2'];
$IsFullWidth[] = ['value'=>'1.5', 'label'=>'1.5'];
$IsFullWidth[] = ['value'=>'1', 'label'=>'1'];
$allFieldsAdd[] = ['name' => 'IsFullWidth', 'show'=>true, 'type'=>'radiogroup', 'options'=>$IsFullWidth, 'label' => __('Field Width (Total 12)'), 'value' => $IsFullWidth[1]['value'], 'placeholder' => __(''), 'helptext' => __(''), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];

$IsMustFill = [];
$IsMustFill[] = ['value'=>'1', 'label'=>__('Enable')];
$IsMustFill[] = ['value'=>'0', 'label'=>__('Disable')];
$allFieldsAdd[] = ['name' => 'IsMustFill', 'show'=>true, 'type'=>'radiogroup', 'options'=>$IsMustFill, 'label' => __('Must Fill'), 'value' => $IsMustFill[1]['value'], 'placeholder' => __('Must Fill'), 'helptext' => __('Must Fill'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];

$IsSearch = [];
$IsSearch[] = ['value'=>'1', 'label'=>__('Enable')];
$IsSearch[] = ['value'=>'0', 'label'=>__('Disable')];
$allFieldsAdd[] = ['name' => 'IsSearch', 'show'=>true, 'type'=>'radiogroup', 'options'=>$IsSearch, 'label' => __('Enable Search'), 'value' => $IsSearch[0]['value'], 'placeholder' => __('Is search in list page'), 'helptext' => __('Is search in list page'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];

$IsGroupFilter = [];
$IsGroupFilter[] = ['value'=>'1', 'label'=>__('Enable')];
$IsGroupFilter[] = ['value'=>'0', 'label'=>__('Disable')];
$allFieldsAdd[] = ['name' => 'IsGroupFilter', 'show'=>true, 'type'=>'radiogroup', 'options'=>$IsGroupFilter, 'label' => __('Group Filter'), 'value' => $IsGroupFilter[1]['value'], 'placeholder' => __('Is group filter in list page'), 'helptext' => __('Is group filter in list page'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];

$IsDbIndex = [];
$IsDbIndex[] = ['value'=>'1', 'label'=>__('Enable')];
$IsDbIndex[] = ['value'=>'0', 'label'=>__('Disable')];
$allFieldsAdd[] = ['name' => 'IsDbIndex', 'show'=>true, 'type'=>'radiogroup', 'options'=>$IsDbIndex, 'label' => __('Is DbIndex'), 'value' => $IsDbIndex[1]['value'], 'placeholder' => __('Is db index in database'), 'helptext' => __('Is db index in database'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];

$IsEnable = [];
$IsEnable[] = ['value'=>'1', 'label'=>__('Enable')];
$IsEnable[] = ['value'=>'0', 'label'=>__('Disable')];
$allFieldsAdd[] = ['name' => 'IsEnable', 'show'=>true, 'type'=>'radiogroup', 'options'=>$IsEnable, 'label' => __('Is Enable'), 'value' => $IsEnable[0]['value'], 'placeholder' => __('Is Enable for this field'), 'helptext' => __('Is Enable for this field'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];

$DataMask = [];
$DataMask[] = ['value'=>'None', 'label'=>__('None')];
$DataMask[] = ['value'=>'Pre6digitsPlusStar', 'label'=>__('Pre6digitsPlusStar')];
$DataMask[] = ['value'=>'Last6digitsPlusStar', 'label'=>__('Last6digitsPlusStar')];
$allFieldsAdd[] = ['name' => 'DataMask', 'show'=>true, 'type'=>'tablefilter', 'options'=>$DataMask, 'label' => __('DataMask'), 'value' => $DataMask[0]['value'], 'placeholder' => __('Data Mask for this field'), 'helptext' => __('Data Mask for this field'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];


$DataFieldEncryptMethod = [];
$DataFieldEncryptMethod[] = ['value'=>'0', 'label'=>__('Disable')];
$DataFieldEncryptMethod[] = ['value'=>'1', 'label'=>__('Enable')];
$allFieldsAdd[] = ['name' => 'DataFieldEncryptMethod', 'show'=>true, 'type'=>'tablefilter', 'options'=>$DataFieldEncryptMethod, 'label' => __('DataFieldEncryptMethod'), 'value' => $DataFieldEncryptMethod[0]['value'], 'placeholder' => '', 'helptext' => __('Encrypt: SM4-CBC'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];
$allFieldsAdd[] = ['name' => 'DataFieldEncryptKey', 'show'=>true, 'type'=>'input', 'label' => __('DataFieldEncryptKey'), 'value' => '', 'placeholder' => '', 'helptext' => __('Encrypt Key, only change when no record'), 'rules' => ['required' => false,'xs'=>12, 'sm'=>4,'disabled' => false, 'row'=>true]];

//判断是否需要显示远程数据表字段列表
$远程数据表字段列表 = [];
$远程数据表字段列表[] = ['value'=>'None', 'label'=>__('None')];
$远程数据表字段列表[] = ['value'=>'Default', 'label'=>__('UseDefault')];
$远程数据表字段列表[] = ['value'=>'LocalFieldExtraFilter', 'label'=>__('LocalFieldExtraFilter')];

$数据源         = $FromInfo['数据源'];
$远程数据表     = $FromInfo['远程数据表'];
$远程数据表主键  = $FromInfo['远程数据表主键'];
$数据同步方式   = $FromInfo['数据同步方式'];
$数据同步周期   = $FromInfo['数据同步周期'];
$远程数据库信息     = returntablefield("data_datasource","id",$数据源,"数据库主机,数据库用户名,数据库密码,数据库名称");
if($远程数据库信息['数据库用户名']!="")    {
    $db_remote = NewADOConnection($DB_TYPE='mysqli');
    $db_remote->connectTimeout = 2;
    $db_remote->connect($远程数据库信息['数据库主机'], $远程数据库信息['数据库用户名'], DecryptID($远程数据库信息['数据库密码']), $远程数据库信息['数据库名称']);
    $db_remote->Execute("Set names utf8;");
    $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
    if($db_remote->database==$远程数据库信息['数据库名称']) {
        $MetaColumnNamesTemp    = $db_remote->MetaColumnNames($远程数据表);
        $远程数据表结构          = array_values($MetaColumnNamesTemp);
        //print_R($远程数据表结构);
        if(is_array($远程数据表结构) && $远程数据表结构[0]!="")     {
            if($远程数据表主键!="" && in_array($远程数据表主键, $远程数据表结构))     {
                foreach($远程数据表结构 as $字段名称) {
                    $远程数据表字段列表[] = ['value'=>$字段名称, 'label'=>$字段名称];
                }
            }
            if($远程数据表主键=="")     {
                foreach($远程数据表结构 as $字段名称) {
                    $远程数据表字段列表[] = ['value'=>$字段名称, 'label'=>$字段名称];
                }
            }
        }
    }
    $allFieldsAdd[] = ['name' => 'RemoteRelativeField', 'show'=>true, 'type'=>'select', 'options'=>$远程数据表字段列表, 'label' => __('RemoteRelativeField'), 'value' => $远程数据表字段列表[0]['value'], 'placeholder' => __('RemoteRelativeField'), 'helptext' => __('RemoteRelativeField'), 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false, 'row'=>true]];
    $allFieldsAdd[] = ['name' => 'LocalFieldExtraFilter', 'show'=>true, 'type'=>'input', 'label' => __('LocalFieldExtraFilter'), 'value' => '', 'placeholder' => __('LocalFieldExtraFilter'), 'helptext' => '远程数据表额外过滤, 如: KCH:ods_zzkcxxsj:KCDM:KCXZM', 'rules' => ['required' => false,'xs'=>12, 'sm'=>12,'disabled' => false, 'row'=>true]];
}


foreach($allFieldsAdd as $ITEM) {
    $defaultValues[$ITEM['name']] = $ITEM['value'];
}
//编辑页面时的启用字段列表
$allFieldsEdit = $allFieldsAdd;

if( ($_GET['action']=="add_default_data") && $_POST['FieldName']!="" && $externalId!="" && $TableName!="")  {
    $MetaColumnNames    = $db->MetaColumnNames($TableName);
    $MetaColumnNames    = array_values($MetaColumnNames);
    $FieldNameArray         = explode(',',trim($_POST['FieldName']));
    $EnglishNameArray       = explode(',',trim($_POST['EnglishName']));
    $ChineseNameArray       = explode(',',trim($_POST['ChineseName']));
    $Exec_Total             = 1;
    for($i=0;$i<sizeof($FieldNameArray);$i++)    {
        $FieldName                      = trim($FieldNameArray[$i]);
        $FieldsArray                    = [];
        $FieldsArray['FormId']          = $externalId;
        $FieldsArray['FormName']        = $TableName;
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
        $FieldsArray['Setting']         = json_encode($_POST, JSON_UNESCAPED_UNICODE);
        if(!in_array($FieldsArray['FieldName'], $MetaColumnNames) && $IsBaseTable == true) {
            if(substr($FieldsArray['FieldType'],0,3)=="int")   {
                $FieldsArray['FieldDefault'] = intval($FieldsArray['FieldDefault']);
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '".$FieldsArray['FieldDefault']."' NOT NULL ;";
            }
            elseif(substr($FieldsArray['FieldType'],0,5)=="float")   {
                $FieldsArray['FieldDefault'] = floatval($FieldsArray['FieldDefault']);
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '".$FieldsArray['FieldDefault']."' NOT NULL ;";
            }
            elseif(substr($FieldsArray['FieldType'],0,4)=="date")   {
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '1971-01-01' NOT NULL ;";
            }
            elseif(substr($FieldsArray['FieldType'],0,4)=="time")   {
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '00:00:00' NOT NULL ;";
            }
            elseif(substr($FieldsArray['FieldType'],0,8)=="datetime")   {
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default CURRENT_TIMESTAMP NOT NULL ;";
            }
            elseif(substr($FieldsArray['FieldType'],0,4)=="text")   {
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." NOT NULL ;";
            }
            elseif(substr($FieldsArray['FieldType'],0,10)=="mediumtext")   {
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." NOT NULL ;";
            }
            else {
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci default '' NOT NULL ;";
            }
            $rs     = $db->Execute($sql);
            if(!$rs->EOF) {
                $RS = [];
                $RS['status'] = "ERROR";
                $RS['sql'] = $sql;
                $RS['msg'] = "Update Error!";
                print json_encode($RS);
                exit;
            }
        }
        if(1)   {
            [$rs,$sql] = InsertOrUpdateTableByArray("form_formfield",$FieldsArray,"FormId,FieldName",0,"Insert");
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

if( ($_GET['action']=="edit_default_data") && $_GET['id']!="" && $externalId!="" && $_POST['FieldName']!="" && $TableName!="")  {
    $MetaColumnNames    = $db->MetaColumnNames($TableName);
    $MetaColumnNames    = array_values($MetaColumnNames);
    $FieldsArray                    = [];
    $FieldsArray['id']              = $_GET['id']; // for primary key to update
    $FieldsArray['FormId']          = $externalId;
    $FieldsArray['FormName']        = $TableName;
    $FieldsArray['FieldName']       = $_POST['FieldName'];
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
    $FieldsArray['EnglishName']     = $_POST['EnglishName'];
    $FieldsArray['ChineseName']     = $_POST['ChineseName'];
    $FieldsArray['Placeholder']     = $_POST['Placeholder'];
    $FieldsArray['Helptext']        = $_POST['Helptext'];
    $FieldsArray['ColumnWidth']     = $_POST['ColumnWidth'];
    $FieldsArray['Setting']         = json_encode($_POST, JSON_UNESCAPED_UNICODE);

    //处理额外数据字典同步生成的功能
    if($FieldsArray['ShowType']=="NewDict"&&$_POST['NewDict']!="")     {
        $NewDictArray   = explode(',',$_POST['NewDict']);
        $SortNumber     = 0;
        $DefaultValue   = "";
        foreach($NewDictArray as $Item) {
            $ElementX   = [];
            $ElementX['DictMark']       = $FullName."_".$FieldsArray['FieldName'];
            $ElementX['EnglishName']    = $Item;
            $ElementX['ChineseName']    = $Item;
            $ElementX['Code']           = $SortNumber;
            $ElementX['SortNumber']     = $SortNumber;
            $ElementX['IsEnable']       = 1;
            if($SortNumber==0) {
                $DefaultValue = $Item;
            }
            if($Item!="")   {
                InsertOrUpdateTableByArray("form_formdict",$ElementX,"DictMark,ChineseName",0);
                $SortNumber ++;
            }
        }
        if($ElementX['DictMark']!="")   {
            $ElementT                   = [];
            $ElementT['Name']           = $FullName.":".$FieldsArray['FieldName'];;
            $ElementT['LIST']           = "autocomplete:form_formdict:3:3:".$DefaultValue.":DictMark:".$ElementX['DictMark'];
            $ElementT['ADD']            = $ElementT['LIST'];
            $ElementT['EDIT']           = $ElementT['LIST'];
            $ElementT['VIEW']           = $ElementT['LIST'];
            $ElementT['SortNumber']     = 999;
            InsertOrUpdateTableByArray("form_formfield_showtype",$ElementT,"Name",0);
            $FieldsArray['ShowType']    = $ElementT['Name'];
        }
    }

    if($IsBaseTable) {
        if($FieldsArray['IsDbIndex']==1)    {
            $sql = "ALTER TABLE `".$TableName."` ADD INDEX `".$FieldsArray['FieldName']."`(`".$FieldsArray['FieldName']."`);";
            $db->Execute($sql);
        }
        else {
            $sql = "ALTER TABLE `".$TableName."` DROP INDEX `".$FieldsArray['FieldName']."`;";
            $db->Execute($sql);
        }
    }



    $sql = "select FieldName from form_formfield where id='".$_GET['id']."'";
    $rs = $db->Execute($sql);
    $FieldNameOld = $rs->fields['FieldName'];
    if(in_array($FieldNameOld, $MetaColumnNames) && $IsBaseTable == true) {
        if(substr($FieldsArray['FieldType'],0,3)=="int")   {
            $FieldsArray['FieldDefault'] = intval($FieldsArray['FieldDefault']);
            $sql    = "ALTER TABLE `".$TableName."` CHANGE `".$FieldNameOld."` `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '".$FieldsArray['FieldDefault']."' NOT NULL;";
        }
        elseif(substr($FieldsArray['FieldType'],0,5)=="float")   {
            $FieldsArray['FieldDefault'] = floatval($FieldsArray['FieldDefault']);
            $sql    = "ALTER TABLE `".$TableName."` CHANGE `".$FieldNameOld."` `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '".$FieldsArray['FieldDefault']."' NOT NULL;";
        }
        elseif(substr($FieldsArray['FieldType'],0,4)=="date")   {
            $sql    = "ALTER TABLE `".$TableName."` CHANGE `".$FieldNameOld."` `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '1971-01-01' NOT NULL;";
        }
        elseif(substr($FieldsArray['FieldType'],0,8)=="time")   {
            $sql    = "ALTER TABLE `".$TableName."` CHANGE `".$FieldNameOld."` `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default CURRENT_TIMESTAMP NOT NULL;";
        }
        elseif(substr($FieldsArray['FieldType'],0,8)=="datetime")   {
            $sql    = "ALTER TABLE `".$TableName."` CHANGE `".$FieldNameOld."` `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '00:00:00' NOT NULL;";
        }
        elseif(substr($FieldsArray['FieldType'],0,4)=="text")   {
            $sql    = "ALTER TABLE `".$TableName."` CHANGE `".$FieldNameOld."` `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;";
        }
        elseif(substr($FieldsArray['FieldType'],0,10)=="mediumtext")   {
            $sql    = "ALTER TABLE `".$TableName."` CHANGE `".$FieldNameOld."` `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;";
        }
        else {
            $sql    = "ALTER TABLE `".$TableName."` CHANGE `".$FieldNameOld."` `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci default '' NOT NULL;";
        }
        $alterSql = $sql;
        $rs     = $db->Execute($sql);
        if(!$rs->EOF) {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['sql'] = $sql;
            $RS['msg'] = "Update Error!";
            print json_encode($RS);
            exit;
        }
    }
    if(!in_array($FieldNameOld, $MetaColumnNames) && $IsBaseTable == true) {
        if(substr($FieldsArray['FieldType'],0,3)=="int")   {
            $FieldsArray['FieldDefault'] = intval($FieldsArray['FieldDefault']);
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '".$FieldsArray['FieldDefault']."' NOT NULL COMMENT '".$FieldsArray['ChineseName']."';";
        }
        elseif(substr($FieldsArray['FieldType'],0,5)=="float")   {
            $FieldsArray['FieldDefault'] = floatval($FieldsArray['FieldDefault']);
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '".$FieldsArray['FieldDefault']."' NOT NULL COMMENT '".$FieldsArray['ChineseName']."';";
        }
        elseif(substr($FieldsArray['FieldType'],0,4)=="date")   {
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '1971-01-01' NOT NULL COMMENT '".$FieldsArray['ChineseName']."';";
        }
        elseif(substr($FieldsArray['FieldType'],0,4)=="time")   {
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default '00:00:00' NOT NULL COMMENT '".$FieldsArray['ChineseName']."';";
        }
        elseif(substr($FieldsArray['FieldType'],0,8)=="datetime")   {
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." default CURRENT_TIMESTAMP NOT NULL COMMENT '".$FieldsArray['ChineseName']."';";
        }
        elseif(substr($FieldsArray['FieldType'],0,4)=="text")   {
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '".$FieldsArray['ChineseName']."';";
        }
        elseif(substr($FieldsArray['FieldType'],0,10)=="mediumtext")   {
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '".$FieldsArray['ChineseName']."';";
        }
        else {
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldsArray['FieldType']." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci default '' NOT NULL COMMENT '".$FieldsArray['ChineseName']."';";
        }
        $alterSql = $sql;
        $rs     = $db->Execute($sql);
        if(!$rs->EOF) {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['sql'] = $sql;
            $RS['msg'] = "Update Error!";
            print json_encode($RS);
            exit;
        }
    }

    if(1)   {
        [$rs,$sql] = InsertOrUpdateTableByArray("form_formfield",$FieldsArray,"id",0,"Update");
        if($rs->EOF) {
            $RS['status'] = "OK";
            $RS['sql'] = $sql;
            $RS['alterSql'] = $alterSql;
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
    $sql    = "select * from form_formfield where ID = '$id'";
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
    $sql    = "select * from form_formfield where ID = '$id'";
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
        $sql    = "update form_formfield set $field = '$value' where $primary_key = '$id'";
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
        $sql    = "select FormName,FieldName from form_formfield where $primary_key = '".$selectedRows[0]."'";
        $rs     = $db->Execute($sql);
        $FormName = $rs->fields['FormName'];
        $FieldName = $rs->fields['FieldName'];
        if($FormName!="")  {
            $MetaColumnNames    = $db->MetaColumnNames($FormName);
            $MetaColumnNames    = array_values($MetaColumnNames);
            if($FormName!="" && $FieldName!="" && in_array($FieldName,$MetaColumnNames) && $IsBaseTable == true) {
                $sql = "ALTER TABLE `".$FormName."` DROP `".$FieldName."`";
                $rs = $db->Execute($sql);
                if(!$rs->EOF) {
                    $RS = [];
                    $RS['status'] = "ERROR";
                    $RS['msg'] = __("Field Drop Failed");
                    $RS['sql'] = $sql;
                    print json_encode($RS);
                    exit;
                }
            }
            foreach($selectedRows as $id) {
                $sql    = "delete from form_formfield where $primary_key = '$id'";
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
            $RS['msg'] = __("FormName is null");
            $RS['sql'] = $sql;
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

$AddSql = " where 1=1 and FormId='$externalId'";

$columnsactions = [];
$columnsactions[] = ['action'=>'view_default','text'=>__('View'),'mdi'=>'mdi:eye-outline'];
$columnsactions[] = ['action'=>'edit_default','text'=>__('Edit'),'mdi'=>'mdi:pencil-outline'];
$columnsactions[] = ['action'=>'delete_array','text'=>__('Delete'),'mdi'=>'mdi:delete-outline','double_check'=>__('Do you want to delete this item?')];
$init_default_columns[] = ['flex' => 0.1, 'minWidth' => 120, 'sortable' => false, 'field' => "actions", 'headerName' => __("Actions"), 'show'=>true, 'type'=>'actions', 'actions' => $columnsactions];
//$columnName = "id";             $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 80, 'maxWidth' => 80, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "FieldName";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "EnglishName";    $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL, 'editable'=>true];
$columnName = "ChineseName";    $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL, 'editable'=>true];
$columnName = "FieldType";      $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL];
$columnName = "ShowType";       $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 200, 'maxWidth' => 300, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'renderCell' => NULL, 'editable'=>true];
$columnName = "SortNumber";     $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'string', 'editable'=>true, 'renderCell' => NULL];
$columnName = "IsSearch";       $init_default_columns[] = ['flex' => 0.1, 'minWidth' => 150, 'maxWidth' => 250, 'field' => $columnName, 'headerName' => __($columnName), 'show'=>true, 'type'=>'actions', 'renderCell' => NULL];

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

$sql        = "select count(*) as NUM from form_formfield $AddSql ";
$rs         = $db->Execute($sql);
$ALL_NUM    = intval($rs->fields['NUM']);

$sql = "select FieldType as name, FieldType as value, count(*) AS num from form_formfield $AddSql group by FieldType";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
array_unshift($rs_a,['name'=>__('All Data'), 'value'=>'All Data', 'num'=>$ALL_NUM]);
$RS['init_default']['filter'][] = ['name' => 'FieldType', 'text' => __('FieldType'), 'list' => $rs_a, 'selected' => "All Data"];

$FieldType = ForSqlInjection($_REQUEST['FieldType']);
if ($FieldType != "" && $FieldType != "All Data") {
    $AddSql .= " and (FieldType = '" . $FieldType . "')";
}
else if ($FieldType == "") {
    //$AddSql .= " and (FormGroup = '" . $rs_a[1]['name'] . "')";
}

$page       = intval($_REQUEST['page']);
$pageSize   = intval($_REQUEST['pageSize']);
if(!in_array($pageSize,[10,20,30,40,50,100,200,500]))  {
	$pageSize = 30;
}
$fromRecord = $page * $pageSize;

$sql    = "select count(*) AS NUM from form_formfield " . $AddSql . "";
$rs     = $db->Execute($sql);
$RS['init_default']['total'] = intval($rs->fields['NUM']);
if($FromInfo['TableName']!="")   {
    $RS['init_default']['searchtitle']  = $FromInfo['FullName'];
}
else {
    $RS['init_default']['searchtitle']  = "Unknown Form";
}
$RS['init_default']['primarykey'] = $columnNames[0];

if($_REQUEST['sortColumn']=="")   {
    $_REQUEST['sortColumn'] = "SortNumber";
}
if(!in_array($_REQUEST['sortColumn'], $columnNames)) {
    $_REQUEST['sortColumn'] = $columnNames[0];
}
if($_REQUEST['sortMethod']=="desc") {
    $orderby = "order by `".$_REQUEST['sortColumn']."` desc, id desc";
}
else {
    $orderby = "order by `".$_REQUEST['sortColumn']."` asc, id desc";
}

$ForbiddenSelectRow = [];
$ForbiddenViewRow = [];
$ForbiddenEditRow = [];
$ForbiddenDeleteRow = [];
$sql    = "select * from form_formfield " . $AddSql . " $orderby limit $fromRecord,$pageSize";
//print $sql;
$NewRSA = [];
$rs = $db->Execute($sql) or print $sql;
$rs_a = $rs->GetArray();
foreach ($rs_a as $Line) {
    $Line['id']         = intval($Line['id']);
    $NewRSA[] = $Line;
    if(in_array($Line['TableName'],['data_user','data_department','role','form_formfield'])) {
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
$RS['add_default']['titletext']  = __("Create Form");
$RS['add_default']['titlememo']  = __("Manage All Form Fields in Table");
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



