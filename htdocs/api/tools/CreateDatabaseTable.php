<?php
/*
* 基础架构: 单点低代码开发平台
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2023
* License: GPL V3 or Commercial license
*/
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

/*
$sql = "select distinct FieldType from form_formfield order by FieldType asc";
$rs = $db->Execute($sql);
$rs_a = $rs->GetArray();
foreach($rs_a as $Line) {
    $FieldsArray = [];
    $FieldsArray['ReadableName'] = $Line['FieldType'];
    $FieldsArray['FieldType'] = $Line['FieldType'];
    $sql = "update form_formfield_logictype set ReadableName='".$FieldsArray['ReadableName']."' where FieldType='".$FieldsArray['FieldType']."' and ReadableName=''";
    //$db->Execute($sql);
    //[$rs,$sql] = InsertOrUpdateTableByArray("form_formfield_logictype",$FieldsArray,"FieldType",0,"Insert");
    print $sql."<BR>";
}
exit;
*/

/*
$filePath = "全国职业教育智慧大脑院校中台 中职数据标准及接口规范-2023.06-数据字典.xls";
//Read Data From Excel
$spreadsheet    = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
$worksheet      = $spreadsheet->getActiveSheet();
$highestRow     = $worksheet->getHighestRow();
$highestColumn  = $worksheet->getHighestColumn();
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
$Header             = $data[0];
$FieldToIndex       = array_flip($Header);
for($i=1;$i<sizeof($data);$i++)                 {
    $FieldsArray                    = [];
    $FieldsArray['DictMark']        = $data[$i][0];
    $FieldsArray['EnglishName']     = $data[$i][1];
    $FieldsArray['ChineseName']     = $data[$i][2];
    $FieldsArray['Code']            = $data[$i][3];
    $FieldsArray['SortNumber']      = $data[$i][4];
    if($data[$i][0]!="" && 0)    {
        [$rs,$sql] = InsertOrUpdateTableByArray("form_formdict",$FieldsArray,"DictMark,ChineseName",0);
        if(!$rs->EOF&&!$rs) {
            print "<font color=red>".$FieldsArray['ChineseName']." ".$sql."</font><BR>";
            exit;
        }
        else {
            print "$i 插入成功:".$FieldsArray['ChineseName']."<BR>";
        }
    }
    if($data[$i][0]!="")  {
        $字典标识[$FieldsArray['DictMark']] = $FieldsArray['Code'];
    }
}
foreach($字典标识 as $Label=>$Value) {
    $FieldsArray                        = [];
    $FieldsArray['Name']                = "中职标准:".$Label;
    $FieldsArray['LIST']                = "autocomplete:form_formdict:4:3:".$Value.":DictMark:".$Label;
    $FieldsArray['ADD']                 = $FieldsArray['LIST'];
    $FieldsArray['EDIT']                = $FieldsArray['LIST'];
    $FieldsArray['VIEW']                = $FieldsArray['LIST'];
    $FieldsArray['SortNumber']          = 999;
    if(0)    {
        [$rs,$sql] = InsertOrUpdateTableByArray("form_formfield_showtype",$FieldsArray,"Name",0,"Insert");
        if(!$rs->EOF&&!$rs) {
            print "<font color=red>".$FieldsArray['Name']." ".$sql."</font><BR>";
            exit;
        }
        else {
            print "$i 插入成功:".$FieldsArray['Name']."<BR>";
        }
    }
}
*/



/*
$filePath = "全国职业教育智慧大脑院校中台 中职数据标准及接口规范-2023.06-分表.xlsx";
//Read Data From Excel
$spreadsheet    = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
$worksheet      = $spreadsheet->getActiveSheet();
$highestRow     = $worksheet->getHighestRow();
$highestColumn  = $worksheet->getHighestColumn();
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
$Header             = $data[0];
$FieldToIndex       = array_flip($Header);

//需要修改的数据
$TableName          = "ODS_ZZYKTRZSJ";
$MetaColumnNames    = $db->MetaColumnNames($TableName);
$MetaColumnNames    = array_values($MetaColumnNames);

print "FormName: $TableName FormId:".returntablefield("form_formname","TableName",$TableName,"id")['id']." ".returntablefield("form_formname","TableName",$TableName,"FullName")['FullName']."<BR>";;
for($i=1;$i<sizeof($data);$i++)   {
    print "$i ".$data[$i][1]." ".$data[$i][2]."<BR>";
}
print "<BR><input type=button onclick=\"location='?action=doit'\" value='确认生成表字段结构'>";

//print_R($data);

//中断执行
if($_GET['action']!='doit') {
    exit;
}
//继续执行
for($i=1;$i<sizeof($data);$i++)   {
    $FieldsArray                    = [];
    $FieldsArray['FormId']          = returntablefield("form_formname","TableName",$TableName,"id")['id'];
    $FieldsArray['FormName']        = $TableName;
    $FieldsArray['FieldName']       = $data[$i][1];
    if($data[$i][5]=='M') {
        $FieldsArray['IsMustFill']      = 1;
    }
    else {
        $FieldsArray['IsMustFill']      = 0;                
    }
    switch($data[$i][3]) {
        case 'C':            
            $FieldType = "varchar(".$data[$i][4].")";
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldType." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci default '' NOT NULL ;";
            break;
        case 'N':
            $FloatArray = explode('.',$data[$i][4]);
            if($FloatArray[1]!="")  {
                $FieldType = "float(".$FloatArray[0].",".$FloatArray[1].")";
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldType." NOT NULL ;";
            }
            else {
                $FieldType = "int(".$data[$i][4].")";
                $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldType." NOT NULL ;";
            }
            break;
        case 'M':
            $FieldType = "float(10,4)";
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldType." NOT NULL ;";
            break;
        case 'T':
            $FieldType = "mediumtext";
            $sql    = "ALTER TABLE `".$TableName."` ADD `".$FieldsArray['FieldName']."` ".$FieldType." NOT NULL ;";
            break;
        default:
            print "不支持数据类型,需要修改代码";
            print_R($data[$i]);
            exit;
            break;
    }
    $FieldsArray['FieldType']       = $FieldType;
    if($data[$i][6]!="") {
        $ShowType = "中职标准:".$data[$i][6];
    }
    else {
        $ShowType = "Input:input";
    }
    $FieldsArray['ShowType']        = $ShowType;
    $FieldsArray['FieldDefault']    = $_POST['FieldDefault'];
    
    $FieldsArray['IsFullWidth']     = 6;
    $FieldsArray['Max']             = "";
    $FieldsArray['Min']             = "";
    $FieldsArray['IsSearch']        = 1;
    $FieldsArray['IsGroupFilter']   = 0;
    $FieldsArray['IsDbIndex']       = 0;
    $FieldsArray['IsEnable']        = 1;
    $FieldsArray['SortNumber']      = intval($i);
    $FieldsArray['EnglishName']     = $data[$i][1];
    $FieldsArray['ChineseName']     = $data[$i][2];
    $FieldsArray['Placeholder']     = $data[$i][0];
    $FieldsArray['Helptext']        = $data[$i][7];
    $FieldsArray['ColumnWidth']     = 200;
    $FieldsArray['Setting']         = json_encode($FieldsArray, JSON_UNESCAPED_UNICODE);
    if(!in_array($FieldsArray['FieldName'], $MetaColumnNames)&&$data[$i][4]>0) {
        $rs     = $db->Execute($sql);
        if(!$rs->EOF) {      
            print "<font color=red>".$sql."</font><BR>";
            exit;
        }
        else {
            print "$i 字段创建成功:".$FieldsArray['FieldName']."<BR>";
        }
    }
    if(1)   {
        //print_R($FieldsArray);
        [$rs,$sql] = InsertOrUpdateTableByArray("form_formfield",$FieldsArray,"FormId,FieldName",0,"Insert");
        if(!$rs->EOF&&!$rs) {
            print "<font color=red>".$FieldsArray['FieldName']." ".$sql."</font><BR>";
            exit;
        }
        else {
            print "$i 字段插入成功:".$FieldsArray['FieldName']."<BR>";
        }
    }

}
print "<meta http-equiv=\"refresh\" content=\"5; url=?\">";
print "执行完毕";
*/

/*
$filePath = "全国职业教育智慧大脑院校中台 中职数据标准及接口规范-2023.06-数据表.xls";
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

for($i=1;$i<sizeof($data);$i++)   {
    $TableName = trim($data[$i][4]);
    $FullName = trim($data[$i][5]);
    $sql = "CREATE TABLE `".$TableName."` ( `id` int(11) NOT NULL AUTO_INCREMENT,PRIMARY KEY (`id`)) ENGINE=Innodb  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='".$FullName."' AUTO_INCREMENT=1 ;";
    $rs = $db->Execute($sql);
    if(!$rs->EOF) {
        print "".$sql." Table Create Failed <BR>";
    }
    $NewArray = [];
    $NewArray['数据子集']   = trim($data[$i][0]);
    $NewArray['数据类']     = trim($data[$i][1]);
    $NewArray['数据子类']   = trim($data[$i][2]);
    $NewArray['数据表序号']  = trim($data[$i][3]);
    $NewArray['数据表英文名称']  = trim($data[$i][4]);
    $NewArray['数据表中文名称']  = trim($data[$i][5]);
    $NewArray['主要来源']   = trim($data[$i][6]);
    $NewArray['上报频率']   = trim($data[$i][7]);
    $NewArray['上报范围']   = trim($data[$i][8]);
    $NewArray['上报范围']   = str_replace(" ","",$NewArray['上报范围']);
    $NewArray['TableName']  = $TableName;
    $NewArray['FullName']   = $FullName;
    $NewArray['FormGroup']  = "中职数据标准";
    $NewArray['Creator']    = "admin";
    $NewArray['CreateTime'] = date("Y-m-d H:i:s");
    $KEYS = array_keys($NewArray);
    $VALUES = array_values($NewArray);
    if(1)   {
        $sql = "insert into form_formname(".join(',',$KEYS).") values('".join("','",$VALUES)."');";
        $rs = $db->Execute($sql);
        if(!$rs->EOF) {
            print "".$TableName." Table Insert FormName Failed <BR>";        
        }
    }
    exit;
}
*/


?>