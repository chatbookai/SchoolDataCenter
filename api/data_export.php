<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');

//$externalId = 16;

//CheckAuthUserLoginStatus();
$DATA = DecryptID($_GET['DATA']);
$DATA = unserialize($DATA);

$Action         = $DATA['Action'];
$TableName      = $DATA['TableName'];
$FileName       = $DATA['FileName'];
$FormId         = $DATA['FormId'];
$FlowId         = $DATA['FlowId'];

if($Action=="export_template"&&$FormId!=""&&$FlowId!="")              {
    $sql        = "select * from form_formflow where id='$FlowId'";
    $rs         = $db->CacheExecute(10, $sql);
    $FromInfo   = $rs->fields;
    $FormId  	= $FromInfo['FormId'];
    $FlowId  	= $FromInfo['id'];
    $FlowName  	= $FromInfo['FlowName'];
    $Step  		= $FromInfo['Step'];
    $Setting  	= $FromInfo['Setting'];
    $SettingMap = unserialize(base64_decode($Setting));

    $sql        = "select * from form_formfield where FormId='$FormId' and IsEnable='1' order by SortNumber asc, id asc";
    $rs         = $db->Execute($sql);
    $AllFieldsFromTable   = $rs->GetArray();
    $AllFields = [];
    foreach($AllFieldsFromTable as $Item)  {        
        if($SettingMap["FieldImport_".$Item['FieldName']]=="true" || $SettingMap["FieldImport_".$Item['FieldName']]=="1")   {
            $AllFields[0][] = $Item['ChineseName'];
            $AllFields[1][] = "";
            $AllFields[2][] = "";
        }
    }

    $filename = $FileName."-".__("ImportTemplate").".xlsx";
    $filetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $worksheet = $spreadsheet->getActiveSheet();

    //Make Body Data
    $row = 1;
    foreach ($AllFields as $rowData) {
        $col = 1;
        foreach ($rowData as $value) {
            //$worksheet->setCellValueByColumnAndRow($col, $row, $value);
            $cell = $worksheet->getCellByColumnAndRow($col, $row);
            $cell->setValue($value);
            $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $cell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $cell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);   
            if($row==1)  {
                $worksheet->getColumnDimensionByColumn($col)->setWidth(15);
                $cell->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $cell->getStyle()->getFill()->getStartColor()->setRGB('D2EAF2');
            }
            $col++;
        }
        $worksheet->getRowDimension($row)->setRowHeight(20);
        $row++;
    }

    
    $worksheet->getColumnDimensionByColumn(1)->setWidth(15);

    header('Content-Type: ' . $filetype);
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

if($Action=="export_data"&&$FormId!=""&&$FlowId!=""&&$DATA['AddSql']!=""&&$DATA['orderby']!="")              {

    //Get All Fields
    $sql        = "select * from form_formfield where FormId='$FormId' and IsEnable='1' order by SortNumber asc, id asc";
    $rs         = $db->Execute($sql);
    $AllFieldsFromTable   = $rs->GetArray();
    $AllFieldsMap = [];
    foreach($AllFieldsFromTable as $Item)  {
        $Item['Setting']                    = json_decode($Item['Setting'],true);
        $AllFieldsMap[$Item['FieldName']]   = $Item;
    }

    $AddSql     = $DATA['AddSql'];
    $orderby    = $DATA['orderby'];

    $sql        = "select * from form_formflow where id='$FlowId'";
    $rs         = $db->CacheExecute(180, $sql);
    $FromInfo   = $rs->fields;
    $FormId  	= $FromInfo['FormId'];
    $FlowId  	= $FromInfo['id'];
    $FlowName  	= $FromInfo['FlowName'];
    $Step  		= $FromInfo['Step'];
    $Setting  	= $FromInfo['Setting'];
    $SettingMap = unserialize(base64_decode($Setting));

    $sql        = "select * from form_formfield where FormId='$FormId' and IsEnable='1' order by SortNumber asc, id asc";
    $rs         = $db->CacheExecute(180, $sql);
    $AllFieldsFromTable   = $rs->GetArray();
    $AllFields      = [];
    $FieldNameArray = [];
    foreach($AllFieldsFromTable as $Item)  {        
        if($SettingMap["FieldExport_".$Item['FieldName']]=="true" || $SettingMap["FieldExport_".$Item['FieldName']]=="1")   {
            $FieldNameArray[]   = $Item['FieldName'];
            $AllFields[0][]     = $Item['ChineseName'];
        }
    }

    //Make Data
    $AddSql = str_replace("INSERT INTO ","",$AddSql);
    $AddSql = str_replace("UPDATE ","",$AddSql);
    $AddSql = str_replace("DELETE FROM","",$AddSql);
    $AddSql = str_replace("CREATE TABLE","",$AddSql);
    $AddSql = str_replace("ALTER TABLE","",$AddSql);
    $AddSql = str_replace("DROP TABLE","",$AddSql);
    $AddSql = str_replace("GROUP BY","",$AddSql);
    $AddSql = str_replace("HAVING","",$AddSql);
    $AddSql = str_replace("UNION","",$AddSql);
    $orderby = str_replace("INSERT INTO ","",$orderby);
    $orderby = str_replace("UPDATE ","",$orderby);
    $orderby = str_replace("DELETE FROM","",$orderby);
    $orderby = str_replace("CREATE TABLE","",$orderby);
    $orderby = str_replace("ALTER TABLE","",$orderby);
    $orderby = str_replace("DROP TABLE","",$orderby);
    $orderby = str_replace("GROUP BY","",$orderby);
    $orderby = str_replace("HAVING","",$orderby);
    $orderby = str_replace("UNION","",$orderby);
    if(sizeof($FieldNameArray)>0 && $TableName!="")    {
        $sql    = "select ".join(",",$FieldNameArray)." from $TableName $AddSql $orderby";
        $rs     = $db->CacheExecute(180, $sql) or print $sql;
        $rs_a   = $rs->GetArray();
    }
    else {
        $rs_a   = [];
    }

    $filename = $FileName."-".__("Export").".xlsx";
    $filetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $worksheet = $spreadsheet->getActiveSheet();

    //Make Body Data
    $row = 1;
    foreach ($AllFields as $rowData) {
        $col = 1;
        foreach ($rowData as $value) {
            //$worksheet->setCellValueByColumnAndRow($col, $row, $value);
            $cell = $worksheet->getCellByColumnAndRow($col, $row);
            $cell->setValue($value);
            $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $cell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $cell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);   
            if($row==1)  {
                $worksheet->getColumnDimensionByColumn($col)->setWidth(15);
                $cell->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $cell->getStyle()->getFill()->getStartColor()->setRGB('D2EAF2');
            }
            $col++;
        }
        $worksheet->getRowDimension($row)->setRowHeight(20);
        $row++;
    }
    foreach ($rs_a as $rowData) {
        $col = 1;
        foreach ($rowData as $FieldName=>$value) {            
            //Decrypt Field Value
            $SettingTempMap                 = $AllFieldsMap[$FieldName]['Setting'];
            $DataFieldEncryptMethod         = $SettingTempMap['DataFieldEncryptMethod'];
            $DataFieldEncryptKey            = $SettingTempMap['DataFieldEncryptKey'];
            if($DataFieldEncryptMethod==1&&$DataFieldEncryptKey!="") {
                $value                      = DecryptIDStorage($value, $DataFieldEncryptKey);
            }
            //$worksheet->setCellValueByColumnAndRow($col, $row, $value);
            $cell = $worksheet->getCellByColumnAndRow($col, $row);
            $cell->setValue($value);
            $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $cell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $cell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);   
            if($row==1)  {
                $worksheet->getColumnDimensionByColumn($col)->setWidth(15);
                $cell->getStyle()->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $cell->getStyle()->getFill()->getStartColor()->setRGB('D2EAF2');
            }
            $col++;
        }
        $worksheet->getRowDimension($row)->setRowHeight(20);
        $row++;
    }

    
    $worksheet->getColumnDimensionByColumn(1)->setWidth(15);

    header('Content-Type: ' . $filetype);
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

}
        

?>