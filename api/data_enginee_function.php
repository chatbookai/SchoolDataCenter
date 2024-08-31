<?php

function SaveWechatAvatar($FieldName='Avatar', $id) {
    global $FileStorageLocation;
    global $SettingMap;
    global $_POST;
    $FileStorageLocation = $FileStorageLocation."/WechatAvatar";
    if(!is_dir($FileStorageLocation)) {
        mkdir($FileStorageLocation);
    }
    $ImageInfor                 = $_FILES[$FieldName];
    switch($ImageInfor['type']) {
        case 'image/jpeg':
            $NewFileName = $id.".jpg";
            break;
        case 'image/png':
            $NewFileName = $id.".png";
            break;
        case 'image/gif':
            $NewFileName = $id.".gif";
            break;
        default:
            $NewFileName = $id.".png";
            break;
    }
    if(is_file($ImageInfor['tmp_name']))    {
        copy($ImageInfor['tmp_name'], $FileStorageLocation."/".$NewFileName);
    }
}

function FilesUploadToDisk($FieldName='图片') {
    global $FileStorageLocation;
    global $SettingMap;
    global $_POST;
    $FileStorageLocation = $FileStorageLocation."/".date("ym");
    if(!is_dir($FileStorageLocation)) {
        mkdir($FileStorageLocation);
    }
    $ImageInfor                 = $_FILES[$FieldName];
    if(is_array($ImageInfor) && is_array($ImageInfor['name']))    {
        $ATTACHMENT_NAME_LIST = [];
        $ATTACHMENT_ID_LIST = [];
        for($i=0;$i<sizeof($ImageInfor['name']);$i++)    {
            $ATTACHMENT_NAME            = ParamsFilter($ImageInfor['name'][$i]);
            $ATTACHMENT_NAME		    = str_replace("=","",$ATTACHMENT_NAME);
            $ATTACHMENT_NAME		    = str_replace(",","",$ATTACHMENT_NAME);
            $ATTACHMENT_NAME		    = str_replace(" ","",$ATTACHMENT_NAME);
            $ATTACHMENT_ID              = time();
            $NewFileName                = $ATTACHMENT_ID.".".$ATTACHMENT_NAME;
            $copyValue                  = copy($ImageInfor['tmp_name'][$i], $FileStorageLocation."/".$NewFileName);
            if($copyValue)  {
                $ATTACHMENT_NAME_LIST[] = $ATTACHMENT_NAME;
                $ATTACHMENT_ID_LIST[]   = date("ym")."_".$ATTACHMENT_ID;
            }
        }
        if($copyValue)  {
            $_POST[$FieldName]          = join("*",$ATTACHMENT_NAME_LIST)."||".join(",",$ATTACHMENT_ID_LIST)."||".join(",",$ImageInfor['size']);
        }
    }
}

function AttachValueMinusOneFile($OriginalValue, $ExistFileNameArray, $UploadFiles)      {
    //Only Add Exists
    $FieldValueArray        = explode("||",$OriginalValue);
    $FieldNameArray         = explode("*",$FieldValueArray[0]);
    $FieldIdArray           = explode(",",$FieldValueArray[1]);
    $FieldSizeArray         = explode(",",$FieldValueArray[2]);
    $FieldNameArrayNew = [];
    $FieldIdArrayNew = [];
    $FieldSizeArrayNew = [];
    for($i=0;$i<sizeof($FieldNameArray);$i++)               {
        if(in_array($FieldNameArray[$i],$ExistFileNameArray))       {
            $FieldNameArrayNew[]    = $FieldNameArray[$i];
            $FieldIdArrayNew[]      = $FieldIdArray[$i];
            $FieldSizeArrayNew[]    = $FieldSizeArray[$i];
        }
    }
    //Upload Files
    $FieldValueArray        = explode("||",$UploadFiles);
    $FieldNameArray         = explode("*",$FieldValueArray[0]);
    $FieldIdArray           = explode(",",$FieldValueArray[1]);
    $FieldSizeArray         = explode(",",$FieldValueArray[2]);
    for($i=0;$i<sizeof($FieldNameArray);$i++)               {
        $FieldNameArrayNew[]    = $FieldNameArray[$i];
        $FieldIdArrayNew[]      = $FieldIdArray[$i];
        $FieldSizeArrayNew[]    = $FieldSizeArray[$i];
    }
    return join("*",$FieldNameArrayNew)."||".join(",",$FieldIdArrayNew)."||".join(",",$FieldSizeArrayNew);;
}

function AttachFieldValueToUrl($TableName,$Id,$FieldName,$Type,$FieldValue) {
    global $FileStorageLocation;
    global $SettingMap;
    global $_POST;
    if($FieldValue=="")    {
        return "";
    }
    $FieldValueArray        = explode("||",$FieldValue);
    $FieldNameArray         = explode("*",$FieldValueArray[0]);
    $FieldSizeArray         = explode(",",$FieldValueArray[2]);
    if($FieldNameArray[0]!="")  {
        $Element    = [];
        $Index      = 0;
        foreach($FieldNameArray as $Item) {
            $RS                     = [];
            $RS['FieldName']        = $FieldName;
            $RS['TableName']        = $TableName;
            $RS['Id']               = $Id;
            $RS['Index']            = $Index;
            $RS['Type']             = $Type;
            $RS['Time']             = time();
            $DATA   = EncryptID(serialize($RS));
            $URL    = "data_image.php?DATA=".$DATA;

            //Return Avatar 
            if($Type=="avatar")  {
                return $URL;
            }

            if(in_array(substr($Item,-4),[".png",".gif",".jpg","jpeg","webm"])) {
                $TypeItem = "image";
            }
            else if(in_array(substr($Item,-4),[".pdf"])) {
                $TypeItem = "pdf";
            }
            else if(in_array(substr($Item,-4),[".xls","xlsx"])) {
                $TypeItem = "Excel";
            }
            else if(in_array(substr($Item,-4),[".doc","docx"])) {
                $TypeItem = "Word";
            }
            else if(in_array(substr($Item,-4),[".ppt","pptx"])) {
                $TypeItem = "PowerPoint";
            }
            else {
                $TypeItem = "file";
            }
            $FieldRow = [];
            $FieldRow['name']   = $Item;
            $FieldRow['webkitRelativePath']    = $URL;
            $FieldRow['type']   = $TypeItem;
            $FieldRow['size']   = $FieldSizeArray[$Index];
            if($Item!="")  {
                $Element[]      = $FieldRow;
            }
            $Index ++;
        }
        return $Element;
    }
}

function ImageUploadToDisk($FieldName='图片') {
    global $FileStorageLocation;
    global $SettingMap;
    global $_POST;
    $FileStorageLocation = $FileStorageLocation."/".date("ym");
    if(!is_dir($FileStorageLocation)) {
        mkdir($FileStorageLocation);
    }
    $ImageInfor                 = $_FILES[$FieldName];
    if(is_array($ImageInfor))    {
        $ATTACHMENT_NAME            = ParamsFilter($ImageInfor['name']);
        $ATTACHMENT_NAME		    = str_replace("=","",$ATTACHMENT_NAME);
        $ATTACHMENT_NAME		    = str_replace(",","",$ATTACHMENT_NAME);
        $ATTACHMENT_NAME		    = str_replace(" ","",$ATTACHMENT_NAME);
        $ATTACHMENT_ID              = time();
        $NewFileName                = $ATTACHMENT_ID.".".$ATTACHMENT_NAME;
        $copyValue                  = copy($ImageInfor['tmp_name'], $FileStorageLocation."/".$NewFileName);
        if($copyValue)  {
            $_POST[$FieldName]      = $ATTACHMENT_NAME."||".date("ym")."_".$ATTACHMENT_ID;
        }
    }
}

function Msg_Reminder_Object_From_Add_Or_Edit($TableName, $id) {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    $InsertSQLALL = [];
    $id     = intval($id);
    $sql    = "select * from `$TableName` where id = '$id'";
    $rsf    = $db->Execute($sql);
    $RS     = $rsf->fields;
    $MaxMsgSections = 3; // other setting in form_formflow.php
    for($i=1; $i<=$MaxMsgSections; $i++)     {
        $Msg_Reminder_Rule_Field_Name1 = $SettingMap["Msg_Reminder_Rule_Field_Name_{$i}_1"];
        $Msg_Reminder_Rule_Field_Method1 = $SettingMap["Msg_Reminder_Rule_Field_Method_{$i}_1"];
        $Msg_Reminder_Rule_Field_Value1 = $SettingMap["Msg_Reminder_Rule_Field_Value_{$i}_1"];        
        $Msg_Reminder_Rule_Field_Name2 = $SettingMap["Msg_Reminder_Rule_Field_Name_{$i}_2"];
        $Msg_Reminder_Rule_Field_Method2 = $SettingMap["Msg_Reminder_Rule_Field_Method_{$i}_2"];
        $Msg_Reminder_Rule_Field_Value2 = $SettingMap["Msg_Reminder_Rule_Field_Value_{$i}_2"];
        $Msg_Reminder_Rule_Content = $SettingMap["Msg_Reminder_Rule_Content_{$i}"];
        $Msg_Reminder_Object_Select_Users = $SettingMap["Msg_Reminder_Object_Select_Users_{$i}"];
        $Msg_Reminder_Rule_Storage_StudentCode = $SettingMap["Msg_Reminder_Rule_Storage_StudentCode_{$i}"];
        $Msg_Reminder_Rule_Storage_StudentClass = $SettingMap["Msg_Reminder_Rule_Storage_StudentClass_{$i}"];
        $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Reminder_Object = $SettingMap["Msg_Reminder_Rule_Storage_StudentCodeAndClass_Reminder_Object_{$i}"];
        $Msg_Reminder_Rule_Strorage_User = $SettingMap["Msg_Reminder_Rule_Strorage_User_{$i}"];
        $Msg_Reminder_Rule_Strorage_OtherStudentCode = $SettingMap["Msg_Reminder_Rule_Strorage_OtherStudentCode_{$i}"];
        $Msg_Reminder_Rule_Strorage_DeptID = $SettingMap["Msg_Reminder_Rule_Strorage_DeptID_{$i}"];
        $Msg_Reminder_Rule_Strorage_Dept_Object = $SettingMap["Msg_Reminder_Rule_Strorage_Dept_Object_{$i}"];
        $Msg_Reminder_Rule_Strorage_FacultyID = $SettingMap["Msg_Reminder_Rule_Strorage_FacultyID_{$i}"];
        $Msg_Reminder_Rule_Strorage_Faculty_Object = $SettingMap["Msg_Reminder_Rule_Strorage_Faculty_Object_{$i}"];
        $Msg_Reminder_Rule_Reminder_Method = $SettingMap["Msg_Reminder_Rule_Reminder_Method_{$i}"];
        $Msg_Reminder_Rule_Reminder_Method_Array = explode(',',$Msg_Reminder_Rule_Reminder_Method);

        //Begin to check the reminder result(yes or not reminder)
        $Msg_Reminder_Condition = 0;
        if($Msg_Reminder_Rule_Content!="" && $Msg_Reminder_Rule_Reminder_Method_Array[0]!="" && $Msg_Reminder_Rule_Field_Value1!="" && in_array($Msg_Reminder_Rule_Field_Name1, $MetaColumnNames) ) {
            switch($Msg_Reminder_Rule_Field_Method1) {
                case '=':
                    if($Msg_Reminder_Rule_Field_Value1=="*" && $RS[$Msg_Reminder_Rule_Field_Name1]!="")   {
                        $Msg_Reminder_Condition = 1;
                    }
                    else if($Msg_Reminder_Rule_Field_Value1=="NULL" && $RS[$Msg_Reminder_Rule_Field_Name1]=="")   {
                        $Msg_Reminder_Condition = 1;
                    }
                    else if($Msg_Reminder_Rule_Field_Value1==$RS[$Msg_Reminder_Rule_Field_Name1])   {
                        $Msg_Reminder_Condition = 1;
                    }
                    break;
                case 'in':
                    $Msg_Reminder_Rule_Field_Value1_Array = explode(',',$Msg_Reminder_Rule_Field_Value1);
                    if($RS[$Msg_Reminder_Rule_Field_Name1]!="" && in_array($RS[$Msg_Reminder_Rule_Field_Name1], $Msg_Reminder_Rule_Field_Value1_Array))   {
                        $Msg_Reminder_Condition = 1;
                    }
                    break;
                case 'not in':
                    $Msg_Reminder_Rule_Field_Value1_Array = explode(',',$Msg_Reminder_Rule_Field_Value1);
                    if($RS[$Msg_Reminder_Rule_Field_Name1]!="" && !in_array($RS[$Msg_Reminder_Rule_Field_Name1], $Msg_Reminder_Rule_Field_Value1_Array))   {
                        $Msg_Reminder_Condition = 1;
                    }
                    break;
            }
        }
        if($Msg_Reminder_Rule_Content!="" && $Msg_Reminder_Rule_Reminder_Method_Array[0]!="" && $Msg_Reminder_Rule_Field_Value2!="" && in_array($Msg_Reminder_Rule_Field_Name2, $MetaColumnNames) ) {
            switch($Msg_Reminder_Rule_Field_Method2) {
                case '=':
                    if($Msg_Reminder_Rule_Field_Value2=="*" && $RS[$Msg_Reminder_Rule_Field_Name2]!="")   {
                        $Msg_Reminder_Condition = 2;
                    }
                    else if($Msg_Reminder_Rule_Field_Value2=="NULL" && $RS[$Msg_Reminder_Rule_Field_Name2]=="")   {
                        $Msg_Reminder_Condition = 2;
                    }
                    else if($Msg_Reminder_Rule_Field_Value2==$RS[$Msg_Reminder_Rule_Field_Name2])   {
                        $Msg_Reminder_Condition = 2;
                    }
                    break;
                case 'in':
                    $Msg_Reminder_Rule_Field_Value2_Array = explode(',',$Msg_Reminder_Rule_Field_Value2);
                    if($RS[$Msg_Reminder_Rule_Field_Name2]!="" && in_array($RS[$Msg_Reminder_Rule_Field_Name2], $Msg_Reminder_Rule_Field_Value2_Array))   {
                        $Msg_Reminder_Condition = 2;
                    }
                    break;
                case 'not in':
                    $Msg_Reminder_Rule_Field_Value2_Array = explode(',',$Msg_Reminder_Rule_Field_Value2);
                    if($RS[$Msg_Reminder_Rule_Field_Name2]!="" && !in_array($RS[$Msg_Reminder_Rule_Field_Name2], $Msg_Reminder_Rule_Field_Value2_Array))   {
                        $Msg_Reminder_Condition = 2;
                    }
                    break;
            }
        }
        //print_R($SettingMap);
        //Need to reminder
        $Need_To_Reminder_Object = [];
        $Need_To_Reminder_Object['User'] = [];
        $Need_To_Reminder_Object['Student'] = [];
        if($Msg_Reminder_Condition>0)   {
            //Reminder Choose Users
            if($Msg_Reminder_Object_Select_Users!="")   {
                $Msg_Reminder_Object_Select_Users_Array = explode(',',$Msg_Reminder_Object_Select_Users);
                foreach($Msg_Reminder_Object_Select_Users_Array as $Item) {
                    if($Item!="") {
                        $Need_To_Reminder_Object['User'][] = $Item;
                    }
                }
            }
            //Reminder Relative Student Scope Object
            if($Msg_Reminder_Rule_Storage_StudentCode!="" && $Msg_Reminder_Rule_Storage_StudentCode!="None" && in_array($Msg_Reminder_Rule_Storage_StudentCode,$MetaColumnNames)) {
                if($RS[$Msg_Reminder_Rule_Storage_StudentCode]!="")  {
                    $StudentCode = $RS[$Msg_Reminder_Rule_Storage_StudentCode];
                    $Msg_Reminder_Rule_Storage_StudentCodeAndClass_Reminder_Object_Array = explode(',',$Msg_Reminder_Rule_Storage_StudentCodeAndClass_Reminder_Object);
                    foreach($Msg_Reminder_Rule_Storage_StudentCodeAndClass_Reminder_Object_Array as $Item) {
                        switch($Item)  {
                            case '学生':
                                $Need_To_Reminder_Object['Student'][] = $StudentCode;
                                break;
                            case '家长':
                                $Need_To_Reminder_Object['Parent'][] = $StudentCode;
                                break;
                            case '班主任':
                                if($RS['班级']!="") {
                                    $sql = "select 班主任用户名 from data_banji where 班级名称='".ForSqlInjection($RS['班级'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    if($rs->fields['班主任用户名']!="")  {
                                        $Need_To_Reminder_Object['User'][] = $rs->fields['班主任用户名'];
                                    }
                                }
                                if($RS['班级名称']!="") {
                                    $sql = "select 班主任用户名 from data_banji where 班级名称='".ForSqlInjection($RS['班级名称'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    if($rs->fields['班主任用户名']!="")  {
                                        $Need_To_Reminder_Object['User'][] = $rs->fields['班主任用户名'];
                                    }
                                }
                                break;
                            case '年段长':
                                break;
                            case '宿管员':
                                break;
                            case '系部':
                                if($RS['班级']!="" && $Msg_Reminder_Rule_Strorage_Faculty_Object!="" && $Msg_Reminder_Rule_Strorage_Faculty_Object!="None") {
                                    $sql = "select 所属系部 from data_banji where 班级名称='".ForSqlInjection($RS['班级'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $sql = "select ".$Msg_Reminder_Rule_Strorage_Faculty_Object." from data_xi where 系部名称='".ForSqlInjection($rs->fields['所属系部'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $Temp = $rs->fields[$Msg_Reminder_Rule_Strorage_Faculty_Object];
                                    $TempArray = explode(',',$Temp);
                                    foreach($TempArray as $Item) {
                                        if($Item!="")  {
                                            $Need_To_Reminder_Object['User'][] = $Item;
                                        }
                                    }
                                }
                                if($RS['班级名称']!="" && $Msg_Reminder_Rule_Strorage_Faculty_Object!="" && $Msg_Reminder_Rule_Strorage_Faculty_Object!="None") {
                                    $sql = "select 所属系部 from data_banji where 班级名称='".ForSqlInjection($RS['班级名称'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $sql = "select ".$Msg_Reminder_Rule_Strorage_Faculty_Object." from data_xi where 系部名称='".ForSqlInjection($rs->fields['所属系部'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $Temp = $rs->fields[$Msg_Reminder_Rule_Strorage_Faculty_Object];
                                    $TempArray = explode(',',$Temp);
                                    foreach($TempArray as $Item) {
                                        if($Item!="")  {
                                            $Need_To_Reminder_Object['User'][] = $Item;
                                        }
                                    }
                                }
                                break;
                            case '专业':
                                $Msg_Reminder_Rule_Strorage_Faculty_Object = "专业秘书1";
                                if($RS['班级']!="" && $Msg_Reminder_Rule_Strorage_Faculty_Object!="" && $Msg_Reminder_Rule_Strorage_Faculty_Object!="None") {
                                    $sql = "select 所属专业 from data_banji where 班级名称='".ForSqlInjection($RS['班级'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $sql = "select ".$Msg_Reminder_Rule_Strorage_Faculty_Object." from data_zhuanye where 专业名称='".ForSqlInjection($rs->fields['所属专业'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $Temp = $rs->fields[$Msg_Reminder_Rule_Strorage_Faculty_Object];
                                    $TempArray = explode(',',$Temp);
                                    foreach($TempArray as $Item) {
                                        if($Item!="")  {
                                            $Need_To_Reminder_Object['User'][] = $Item;
                                        }
                                    }
                                }
                                if($RS['班级名称']!="" && $Msg_Reminder_Rule_Strorage_Faculty_Object!="" && $Msg_Reminder_Rule_Strorage_Faculty_Object!="None") {
                                    $sql = "select 所属专业 from data_banji where 班级名称='".ForSqlInjection($RS['班级名称'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $sql = "select ".$Msg_Reminder_Rule_Strorage_Faculty_Object." from data_zhuanye where 专业名称='".ForSqlInjection($rs->fields['所属专业'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $Temp = $rs->fields[$Msg_Reminder_Rule_Strorage_Faculty_Object];
                                    $TempArray = explode(',',$Temp);
                                    foreach($TempArray as $Item) {
                                        if($Item!="")  {
                                            $Need_To_Reminder_Object['User'][] = $Item;
                                        }
                                    }
                                }
                                break;
                            case '本班所有学生':
                                if($RS['班级']!="") {
                                    $sql = "select 学号 from data_student where 学生状态='正常状态' and 班级='".ForSqlInjection($RS['班级'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $rs_a = $rs->GetArray();
                                    foreach($rs_a as $Element) {
                                        $Need_To_Reminder_Object['Student'][] = $Element['学号'];
                                    }
                                }
                                if($RS['班级名称']!="") {
                                    $sql = "select 学号 from data_student where 学生状态='正常状态' and 班级='".ForSqlInjection($RS['班级名称'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    $rs_a = $rs->GetArray();
                                    foreach($rs_a as $Element) {
                                        $Need_To_Reminder_Object['Student'][] = $Element['学号'];
                                    }
                                }
                                break;
                            case '本专业所有学生':
                                if($RS['班级']!="") {
                                    $sql = "select 所属专业 from data_banji where 班级名称='".ForSqlInjection($RS['班级'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    if($rs->fields['所属专业']!="")  {
                                        $sql = "select 班级名称 from data_banji where 所属专业='".ForSqlInjection($rs->fields['所属专业'])."'";
                                        $rs = $db->CacheExecute(10,$sql);
                                        $rs_a = $rs->GetArray();
                                        $班级名称Array = [];
                                        foreach($rs_a as $Element) {
                                            $班级名称Array[] = ForSqlInjection($Element['班级名称']);
                                        }
                                        $sql = "select 学号 from data_student where 学生状态='正常状态' and 班级 in ('".join("','",$班级名称Array)."')";
                                        $rs = $db->CacheExecute(10,$sql);
                                        $rs_a = $rs->GetArray();
                                        foreach($rs_a as $Element) {
                                            $Need_To_Reminder_Object['Student'][] = $Element['学号'];
                                        }
                                    }
                                }
                                if($RS['班级名称']!="") {
                                    $sql = "select 所属专业 from data_banji where 班级名称='".ForSqlInjection($RS['班级名称'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    if($rs->fields['所属专业']!="")  {
                                        $sql = "select 班级名称 from data_banji where 所属专业='".ForSqlInjection($rs->fields['所属专业'])."'";
                                        $rs = $db->CacheExecute(10,$sql);
                                        $rs_a = $rs->GetArray();
                                        $班级名称Array = [];
                                        foreach($rs_a as $Element) {
                                            $班级名称Array[] = ForSqlInjection($Element['班级名称']);
                                        }
                                        $sql = "select 学号 from data_student where 学生状态='正常状态' and 班级 in ('".join("','",$班级名称Array)."')";
                                        $rs = $db->CacheExecute(10,$sql);
                                        $rs_a = $rs->GetArray();
                                        foreach($rs_a as $Element) {
                                            $Need_To_Reminder_Object['Student'][] = $Element['学号'];
                                        }
                                    }
                                }
                                break;
                            case '本系所有学生':
                                if($RS['班级']!="") {
                                    $sql = "select 所属系部 from data_banji where 班级名称='".ForSqlInjection($RS['班级'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    if($rs->fields['所属系部']!="")  {
                                        $sql = "select 班级名称 from data_banji where 所属系部='".ForSqlInjection($rs->fields['所属系部'])."'";
                                        $rs = $db->CacheExecute(10,$sql);
                                        $rs_a = $rs->GetArray();
                                        $班级名称Array = [];
                                        foreach($rs_a as $Element) {
                                            $班级名称Array[] = ForSqlInjection($Element['班级名称']);
                                        }
                                        $sql = "select 学号 from data_student where 学生状态='正常状态' and 班级 in ('".join("','",$班级名称Array)."')";
                                        $rs = $db->CacheExecute(10,$sql);
                                        $rs_a = $rs->GetArray();
                                        foreach($rs_a as $Element) {
                                            $Need_To_Reminder_Object['Student'][] = $Element['学号'];
                                        }
                                    }
                                }
                                if($RS['班级名称']!="") {
                                    $sql = "select 所属系部 from data_banji where 班级名称='".ForSqlInjection($RS['班级名称'])."'";
                                    $rs = $db->CacheExecute(10,$sql);
                                    if($rs->fields['所属系部']!="")  {
                                        $sql = "select 班级名称 from data_banji where 所属系部='".ForSqlInjection($rs->fields['所属系部'])."'";
                                        $rs = $db->CacheExecute(10,$sql);
                                        $rs_a = $rs->GetArray();
                                        $班级名称Array = [];
                                        foreach($rs_a as $Element) {
                                            $班级名称Array[] = ForSqlInjection($Element['班级名称']);
                                        }
                                        $sql = "select 学号 from data_student where 学生状态='正常状态' and 班级 in ('".join("','",$班级名称Array)."')";
                                        $rs = $db->CacheExecute(10,$sql);
                                        $rs_a = $rs->GetArray();
                                        foreach($rs_a as $Element) {
                                            $Need_To_Reminder_Object['Student'][] = $Element['学号'];
                                        }
                                    }
                                }
                                break;
                            case '本校所有学生':
                                $sql = "select 学号 from data_student where 学生状态='正常状态'";
                                $rs = $db->CacheExecute(10,$sql);
                                $rs_a = $rs->GetArray();
                                foreach($rs_a as $Element) {
                                    $Need_To_Reminder_Object['Student'][] = $Element['学号'];
                                }
                                break;
                        }
                    }                    
                }
            }
            //Reminder Other Student
            if($Msg_Reminder_Rule_Strorage_OtherStudentCode!="" && in_array($Msg_Reminder_Rule_Strorage_OtherStudentCode, $MetaColumnNames))   {
                if($RS[$Msg_Reminder_Rule_Strorage_OtherStudentCode]!="")  {
                    $Need_To_Reminder_Object['Student'][] = $RS[$Msg_Reminder_Rule_Strorage_OtherStudentCode];
                }
            }
            //Reminder User In Table Row
            if($Msg_Reminder_Rule_Strorage_User!="" && in_array($Msg_Reminder_Rule_Strorage_User, $MetaColumnNames))   {
                if($RS[$Msg_Reminder_Rule_Strorage_User]!="")  {
                    $Need_To_Reminder_Object['User'][] = $RS[$Msg_Reminder_Rule_Strorage_User];
                }
            }
            //Reminder Department Manager or Leaders
            if($Msg_Reminder_Rule_Strorage_DeptID!="" && in_array($Msg_Reminder_Rule_Strorage_DeptID, $MetaColumnNames) && $Msg_Reminder_Rule_Strorage_Dept_Object!="" && $Msg_Reminder_Rule_Strorage_Dept_Object!="None")   {
                if($RS[$Msg_Reminder_Rule_Strorage_DeptID]!="")  {
                    $sql = "select ".$Msg_Reminder_Rule_Strorage_Dept_Object." from data_department where DEPT_ID='".ForSqlInjection($RS[$Msg_Reminder_Rule_Strorage_DeptID])."'";
                    $rs  = $db->CacheExecute(10,$sql);
                    $Temp = $rs->fields[$Msg_Reminder_Rule_Strorage_Dept_Object];
                    $TempArray = explode(',',$Temp);
                    foreach($TempArray as $Item) {
                        if($Item!="")  {
                            $Need_To_Reminder_Object['User'][] = $Item;
                        }
                    }
                }
            }
            //Reminder Faculty Managers
            if($Msg_Reminder_Rule_Strorage_FacultyID!="" && $Msg_Reminder_Rule_Strorage_FacultyID!="None" && in_array($Msg_Reminder_Rule_Strorage_FacultyID,$MetaColumnNames) && $RS[$Msg_Reminder_Rule_Strorage_FacultyID]!="") {
                $sql = "select ".$Msg_Reminder_Rule_Strorage_Faculty_Object." from data_xi where 系部名称='".ForSqlInjection($RS[$Msg_Reminder_Rule_Strorage_FacultyID])."'";
                $rs = $db->CacheExecute(10,$sql);
                $Temp = $rs->fields[$Msg_Reminder_Rule_Strorage_Faculty_Object];
                $TempArray = explode(',',$Temp);
                foreach($TempArray as $Item) {
                    if($Item!="")  {
                        $Need_To_Reminder_Object['User'][] = $Item;
                    }
                }
            }
            
        }

        //Begin to Reminder
        if(sizeof($Need_To_Reminder_Object['User'])>0 || sizeof($Need_To_Reminder_Object['Student'])>0)  {
            foreach($RS as $FieldName=>$FieldValue)  {
                $Msg_Reminder_Rule_Content = str_replace("[".$FieldName."]","[".$FieldValue."]",$Msg_Reminder_Rule_Content);
            }

            //Reminder Users
            $Users = $Need_To_Reminder_Object['User'];
            $Users = array_flip($Users);
            $Users = array_keys($Users);
            if(sizeof($Users)>0)  {
                $From = $GLOBAL_USER->USER_ID;
                $MaxMsgSendNumber = 30;
                $SendTimes = ceil(sizeof($Users)/$MaxMsgSendNumber);
                for($X=0;$X<$SendTimes;$X++) {
                    $InsertSQL  = [];
                    for($XX=$X*$MaxMsgSendNumber;$XX<($X+1)*$MaxMsgSendNumber&&$XX<sizeof($Users);$XX++)  {
                        $TO     = $Users[$XX];
                        $URL    = "";
                        $InsertSQLALL[] = $InsertSQL[] = "('User','".$TO."','".$From."','".$Msg_Reminder_Rule_Content."','".date('Y-m-d H:i:s')."','".$URL."',0)";
                    }
                    $sql = "insert into data_msgreminder(MSG_TYPE,MSG_TO,MSG_FROM,MSG_CONTENT,MSG_TIME,MSG_URL,MSG_ISREAD) values ".join(',',$InsertSQL);
                    //print_R($InsertSQL);
                    $db->Execute($sql);
                    global $GLOBAL_EXEC_KEY_SQL;
                    $GLOBAL_EXEC_KEY_SQL['Msg_Reminder_Object_From_Add_Or_Edit'][] = $sql;
                }
            }

            //Reminder Students            
            $Students = $Need_To_Reminder_Object['Student'];
            $Students = array_flip($Students);
            $Students = array_keys($Students);
            if(sizeof($Students)>0)  {
                $From = $GLOBAL_USER->USER_ID;
                $MaxMsgSendNumber = 30;
                $SendTimes = ceil(sizeof($Students)/$MaxMsgSendNumber);
                for($X=0;$X<$SendTimes;$X++) {
                    $InsertSQL  = [];
                    for($XX=$X*$MaxMsgSendNumber;$XX<($X+1)*$MaxMsgSendNumber&&$XX<sizeof($Students);$XX++)  {
                        $TO     = $Students[$XX];
                        $URL    = "";
                        $InsertSQLALL[] = $InsertSQL[] = "('Student','".$TO."','".$From."','".$Msg_Reminder_Rule_Content."','".date('Y-m-d H:i:s')."','".$URL."',0)";
                    }
                    $sql = "insert into data_msgreminder(MSG_TYPE,MSG_TO,MSG_FROM,MSG_CONTENT,MSG_TIME,MSG_URL,MSG_ISREAD) values ".join(',',$InsertSQL);
                    //print_R($InsertSQL);
                    $db->Execute($sql);
                    global $GLOBAL_EXEC_KEY_SQL;
                    $GLOBAL_EXEC_KEY_SQL['Msg_Reminder_Object_From_Add_Or_Edit'][] = $sql;
                }
            }
        }
        //exit;
        
    }
    return $InsertSQLALL;
}

function getAllFields($AllFieldsFromTable, $AllShowTypesArray, $actionType, $FilterFlowSetting=true, $SettingMap)  {
    global $db;
    global $InsertOrUpdateFieldArrayForSql;
    global $GLOBAL_USER;
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
        $EnglishName            = $Item['EnglishName'];
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
        $Placeholder    = $Item['Placeholder'];
        $Format         = $Item['Format'];
        $Helptext       = $Item['Helptext'];
        $Max            = intval($Item['Max']);
        $Min            = intval($Item['Min']);
        $Setting        = json_decode($Item['Setting'],true);
        $CurrentFieldType = $AllShowTypesArray[$ShowType][$actionType];
        $CurrentFieldTypeArray = explode(':',$CurrentFieldType);
        //Filter Field Type
        $FieldTypeInFlow = $SettingMap['FieldType_'.$FieldName];
        $FieldTypeInFlow_Map = [];        
        if($FilterFlowSetting==false)   {
            $FieldTypeInFlow = 'FieldTypeFollowByFormSetting';
        }
        //print_R($FieldName.$FieldTypeInFlow);print "\n";
        //print_R($CurrentFieldTypeArray)."\n";
        switch($FieldTypeInFlow)   {
            case 'FieldTypeFollowByFormSetting':
                //Do Nothing
                break;
            case 'List_Use_AddEditView_NotUse':
                $FieldTypeInFlow_Map['add_default'] = "Disable";
                $FieldTypeInFlow_Map['edit_default'] = "Disable";
                $FieldTypeInFlow_Map['view_default'] = "Disable";
                break;
            case 'ListView_Use_AddEdit_NotUse':
                $FieldTypeInFlow_Map['add_default'] = "Disable";
                $FieldTypeInFlow_Map['edit_default'] = "Disable";
                break;
            case 'View_Use_ListAddEdit_NotUse':
                if($CurrentFieldTypeArray[0]=="avatar")  {
                    $FieldTypeInFlow_Map['add_default'] = "Disable";
                    $FieldTypeInFlow_Map['edit_default'] = "Disable";
                    $FieldTypeInFlow_Map['view_default'] = "avatar";
                }
                else if($CurrentFieldTypeArray[0]=="images" || $CurrentFieldTypeArray[0]=="images2")  {
                    $FieldTypeInFlow_Map['add_default'] = "Disable";
                    $FieldTypeInFlow_Map['edit_default'] = "Disable";
                    $FieldTypeInFlow_Map['view_default'] = "images";
                }
                else if($CurrentFieldTypeArray[0]=="files" || $CurrentFieldTypeArray[0]=="files2")  {
                    $FieldTypeInFlow_Map['add_default'] = "Disable";
                    $FieldTypeInFlow_Map['edit_default'] = "Disable";
                    $FieldTypeInFlow_Map['view_default'] = "files";
                }
                else {
                    if($actionType=="LIST"||$actionType=="ADD"||$actionType=="EDIT") {
                        $FieldTypeInFlow_Map['add_default'] = "Disable";
                        $FieldTypeInFlow_Map['edit_default'] = "Disable"; 
                    }
                    else {
                        $FieldTypeInFlow_Map['add_default'] = "readonly";
                        $FieldTypeInFlow_Map['edit_default'] = "readonly"; 
                    }                   
                }
                break;
            case 'ListAddView_Use_Edit_Readonly':
                if($CurrentFieldTypeArray[0]=="avatar")  {
                    $FieldTypeInFlow_Map['edit_default'] = "readonlyavatar";
                    $FieldTypeInFlow_Map['view_default'] = "avatar";
                }
                elseif($CurrentFieldTypeArray[0]=="files" || $CurrentFieldTypeArray[0]=="files2")  {
                    $FieldTypeInFlow_Map['edit_default'] = "readonlyfiles";
                    $FieldTypeInFlow_Map['view_default'] = "files";
                }
                elseif($CurrentFieldTypeArray[0]=="images" || $CurrentFieldTypeArray[0]=="images2")  {
                    $FieldTypeInFlow_Map['edit_default'] = "readonlyimages";
                    $FieldTypeInFlow_Map['view_default'] = "images";
                }
                elseif(in_array($CurrentFieldTypeArray[0], ['tablefilter','tablefiltercolor','radiogroup','radiogroupcolor','autucomplete']))  {
                    $FieldTypeInFlow_Map['edit_default'] = "readonly".$CurrentFieldTypeArray[0];
                    //$FieldTypeInFlow_Map['view_default'] = "input";  
                    $CurrentFieldTypeArray[0] = "readonly".$CurrentFieldTypeArray[0];
                }
                else {
                    $FieldTypeInFlow_Map['edit_default'] = "readonly";
                    $FieldTypeInFlow_Map['view_default'] = "input";                  
                }
                break;
            case 'ListView_Use_AddEdit_Readonly':
                if($CurrentFieldTypeArray[0]=="avatar")  {
                    $FieldTypeInFlow_Map['add_default'] = "readonlyavatar";
                    $FieldTypeInFlow_Map['edit_default'] = "readonlyavatar";
                }
                else if($CurrentFieldTypeArray[0]=="images" || $CurrentFieldTypeArray[0]=="images2")  {
                    $FieldTypeInFlow_Map['add_default'] = "readonlyimages";
                    $FieldTypeInFlow_Map['edit_default'] = "readonlyimages";
                }
                else if($CurrentFieldTypeArray[0]=="files" || $CurrentFieldTypeArray[0]=="files2")  {
                    $FieldTypeInFlow_Map['add_default'] = "readonlyfiles";
                    $FieldTypeInFlow_Map['edit_default'] = "readonlyfiles";
                }
                elseif(in_array($CurrentFieldTypeArray[0], ['tablefilter','tablefiltercolor','radiogroup','radiogroupcolor','autucomplete']))  {
                    $FieldTypeInFlow_Map['add_default'] = "readonly".$CurrentFieldTypeArray[0];
                    $FieldTypeInFlow_Map['edit_default'] = "readonly".$CurrentFieldTypeArray[0];  
                    $CurrentFieldTypeArray[0] = "readonly".$CurrentFieldTypeArray[0];
                }
                else {
                    $FieldTypeInFlow_Map['add_default'] = "readonly";
                    $FieldTypeInFlow_Map['edit_default'] = "readonly";                    
                }
                break;
            case 'ListAddEdit_Use_View_NotUse':
                $FieldTypeInFlow_Map['view_default'] = "readonly";
                break;
            case 'Disable':
            case '':
                $FieldTypeInFlow_Map['add_default'] = "Disable";
                $FieldTypeInFlow_Map['edit_default'] = "Disable";
                $FieldTypeInFlow_Map['view_default'] = "Disable";
                $FieldTypeInFlow_Map['import_default'] = "Disable";
                $FieldTypeInFlow_Map['export_default'] = "Disable";
                $FieldTypeInFlow_Map['init_default'] = "Disable";
                break;
            case 'HiddenUserID':
            case 'HiddenUsername':
            case 'HiddenDeptID':
            case 'HiddenDeptName':
            case 'HiddenStudentID':
            case 'HiddenStudentName':
            case 'HiddenStudentClass':
                $FieldTypeInFlow_Map['add_default'] = "Disable";
                $FieldTypeInFlow_Map['edit_default'] = "Disable";
                $FieldTypeInFlow_Map['import_default'] = "Disable";
                //Do Nothing
                break;
        }
        if($actionType=="ADD") {
            $FieldTypeInFlow_Value = $FieldTypeInFlow_Map['add_default'];
            if($FieldTypeInFlow_Value != "") {
                $CurrentFieldTypeArray[0] = $FieldTypeInFlow_Value;
            }
        }
        if($actionType=="EDIT") {
            $FieldTypeInFlow_Value = $FieldTypeInFlow_Map['edit_default'];
            if($FieldTypeInFlow_Value != "") {
                $CurrentFieldTypeArray[0] = $FieldTypeInFlow_Value;
            }
        }
        if($actionType=="VIEW") {
            $FieldTypeInFlow_Value = $FieldTypeInFlow_Map['view_default'];
            if($FieldTypeInFlow_Value != "") {
                $CurrentFieldTypeArray[0] = $FieldTypeInFlow_Value;
            }
        }
        //print_R($FieldName." ".$FieldTypeInFlow_Map['edit_default']." ".$FieldTypeInFlow_Value." ".$CurrentFieldTypeArray[0]);print "\n";
        $disabledItem = false;
        switch($CurrentFieldTypeArray[0])   {
            case 'Disable':
                //Do Nothing
                break;
            case 'hidden':
                if($actionType=="ADD"||$actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                break;
            case 'system_datetime':
                if($actionType=="ADD"||$actionType=="EDIT")  {
                    //print_R($Item);print "\n";
                    if($Setting['DateTimeFormat']!="") {
                        $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = date($Setting['DateTimeFormat']);    
                    }
                    else {
                        $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = date("Y-m-d H:i:s");  
                    }
                }
                break;
            case 'CurrentUserIdAddEdit':
                if($actionType=="ADD"||$actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = $GLOBAL_USER->USER_ID;
                break;
            case 'CurrentUserIdAdd':
                if($actionType=="ADD") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = $GLOBAL_USER->USER_ID;
                break;
            case 'CurrentStudentCodeAddEdit':
                if($GLOBAL_USER->学号=="") $GLOBAL_USER->学号 = $GLOBAL_USER->USER_ID;
                if($actionType=="ADD"||$actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = $GLOBAL_USER->学号;
                break;
            case 'CurrentStudentCodeAdd':
                if($GLOBAL_USER->学号=="") $GLOBAL_USER->学号 = $GLOBAL_USER->USER_ID;
                if($actionType=="ADD") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = $GLOBAL_USER->学号;
                break;
            case 'CurrentDeptNameAddEdit':
                if($actionType=="ADD"||$actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = $GLOBAL_USER->DEPT_NAME;
                break;
            case 'CurrentDeptNameAdd':
                if($actionType=="ADD") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = $GLOBAL_USER->DEPT_NAME;
                break;
            case 'CurrentDeptIDAddEdit':
                if($actionType=="ADD"||$actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = $GLOBAL_USER->DEPT_ID;
                break;
            case 'CurrentDeptIDAdd':
                if($actionType=="ADD") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = $GLOBAL_USER->DEPT_ID;
                break;
            case 'CurrentWeek':
                $CurrentWeek = date("w");
                if($actionType=="ADD"||$actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = date("w");
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>'readonly', 'label' => $ShowTextName, 'value' => $CurrentWeek, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => true,'min'=>$Min,'max'=>$Max]];
                break;
            case 'CurrentWeekEndDays':
                $TodayWeek = date('w');
                $today  = date('Y-m-d');
                $Day6   = date('Y-m-d', strtotime($today . ' +'.(6-$TodayWeek).' day'));
                $Day5   = date('Y-m-d', strtotime($today . ' +'.(5-$TodayWeek).' day'));
                $CurrentWeekEndDays = [];
                $CurrentWeekEndDays[] = ['value'=>strval($Day6), 'label'=>strval($Day6)];
                $CurrentWeekEndDays[] = ['value'=>strval($Day5), 'label'=>strval($Day5)];
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'type'=>'checkbox', 'options'=>$CurrentWeekEndDays, 'label' => $ShowTextName, 'value' => $Day5.",".$Day6, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false, 'row'=>true, 'min'=>$Min, 'max'=>$Max]];
                break;
            case 'CurrentUnitName':
                if($actionType=="ADD"||$actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = $GLOBAL_USER->UNIT_NAME;
                break;

            case 'input':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max]];
                break;
            case 'readonly':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => true,'min'=>$Min,'max'=>$Max]];
                break;
            case 'password':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => true,'min'=>$Min,'max'=>$Max]];
                break;
            case 'EncryptField':
                if($actionType=="ADD"||$actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "EncryptField";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => true,'min'=>$Min,'max'=>$Max]];
                break;
            case 'confirmpassword':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => true,'min'=>$Min,'max'=>$Max]];
                break;
            case 'email':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false]];
                break;
            case 'url':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false]];
                break;
            case 'ChiaBankCard':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>'input', 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'format'=>'bankcard','invalidtext'=>'银行卡号不合乎要求,必须是19位数字'], 'mobilekeyboard'=>'number'];
                break;
            case 'ChinaIDCard':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>'input', 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'format'=>'chinaidcard','invalidtext'=>'身份证号不合乎要求','出生日期'=>str_replace("身份证件号","出生日期",$FieldName),'出生年月'=>str_replace("身份证件号","出生年月",$FieldName),'性别'=>str_replace("身份证件号","性别",$FieldName),'年龄'=>str_replace("身份证件号","年龄",$FieldName)], 'mobilekeyboard'=>'idcard'];
                break;
            case 'ChinaMobile':
                global $GLOBAL_USER;
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>'input', 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'mobilekeyboard'=>'number'];
                break;
            case 'UserMobile':
                if($GLOBAL_USER->type=="User") {
                    $MOBILE_NO = returntablefield("data_user","USER_ID",$GLOBAL_USER->USER_ID,"MOBILE_NO")['MOBILE_NO'];
                }
                else {
                    $MOBILE_NO = returntablefield("data_student","学号",$GLOBAL_USER->学号,"学生手机号码")['学生手机号码'];
                }
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>'input', 'label' => $ShowTextName, 'value' => $MOBILE_NO, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'format'=>'chinamobile','invalidtext'=>'手机号不合乎要求'], 'mobilekeyboard'=>'number'];
                break;
            case 'ChinaPassport':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>'input', 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'format'=>'chinapassport','invalidtext'=>'护照号码不合乎要求']];
                break;
            case 'number':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>'number', 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max],'inputProps'=>['step'=>1,'min'=>$Min,'max'=>$Max], 'Formula'=>['FormulaMethod'=>$Setting['FormulaMethod'],'FormulaMethodField'=>$Setting['FormulaMethodField'],'FormulaMethodTarget'=>$Setting['FormulaMethodTarget']], 'mobilekeyboard'=>'number' ];
                break;
            case 'float':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>['number'], 'type'=>'number', 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max],'inputProps'=>['step'=>0.01,'min'=>$Min,'max'=>$Max], 'Formula'=>['FormulaMethod'=>$Setting['FormulaMethod'],'FormulaMethodField'=>$Setting['FormulaMethodField'],'FormulaMethodTarget'=>$Setting['FormulaMethodTarget']], 'mobilekeyboard'=>'digit' ];
                break;
            case 'date':
            case 'date1':
            case 'date2':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'dateFormat' => $Setting['DateFormat']!=""?$Setting['DateFormat']:'yyyy-MM-dd','timeZone'=>'America/Los_Angeles','StartDate'=>$Setting['StartDate'],'EndDate'=>$Setting['EndDate']];
                break;
            case 'year':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles','StartYear'=>$Setting['StartYear'],'EndYear'=>$Setting['EndYear']];
                break;
            case 'yearrange':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'dateFormat' => 'yyyy','timeZone'=>'America/Los_Angeles'];
                break;
            case 'month':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'dateFormat' => $Setting['MonthFormat']!=""?$Setting['MonthFormat']:'yyyy-MM','timeZone'=>'America/Los_Angeles','StartMonth'=>$Setting['StartMonth'],'EndMonth'=>$Setting['EndMonth']];
                break;
            case 'monthrange':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'dateFormat' => $Setting['MonthFormat']!=""?$Setting['MonthFormat']:'yyyy-MM','timeZone'=>'America/Los_Angeles'];
                break;
            case 'quarter':
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'dateFormat' => 'yyyy-QQQ','timeZone'=>'America/Los_Angeles'];
                break;
            case 'datetime':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'dateFormat' => $Setting['DateTimeFormat']!=""?$Setting['DateTimeFormat']:'yyyy-MM-dd HH:mm:ss','timeZone'=>'America/Los_Angeles','StartDateTime'=>$Setting['StartDateTime'],'EndDateTime'=>$Setting['EndDateTime']];
                break;
            case 'time':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'dateFormat' => $Setting['TimeFormat']!=""?$Setting['TimeFormat']:'HH:mm:ss','timeZone'=>'America/Los_Angeles','StartTime'=>$Setting['StartTime'],'EndTime'=>$Setting['EndTime']];
                break;
            case 'textarea':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max]];
                break;
            case 'editor':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false]];
                break;
            case 'slider':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),  "min"=>$Min, "max"=>$Max, "step"=>5,"marks"=>[["value"=>0,"label"=>"0°"],["value"=>30,"label"=>"50°"],["value"=>50,"label"=>"50°"],["value"=>100,"label"=>"100°"] ]]];
                break;
            case 'Switch':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),  "min"=>$Min, "max"=>$Max, "step"=>5,"marks"=>[["value"=>0,"label"=>"0°"],["value"=>30,"label"=>"50°"],["value"=>50,"label"=>"50°"],["value"=>100,"label"=>"100°"] ]]];
                break;
            case 'select':
            case 'autocomplete0':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $sql = "select `FieldType` as value, `FieldType` as label from form_formfield_logictype order by SortNumber asc, id asc";
                $rs = $db->CacheExecute(10, $sql);
                $FieldType = $rs->GetArray();
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'options'=>$FieldType, 'label' => $ShowTextName, 'value' => $FieldType[2]['value'], 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),'disabled' => false]];
                break;
                
            case 'autocompletemdi':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max]];
                break;
            case 'autocompleteicons':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max]];
                break;
            case 'readonlyautocomplete':
                $disabledItem = true;
                $CurrentFieldTypeArray[0] = "autocomplete";
            case 'autocomplete':
            case 'autocompletemulti':
                if($actionType=="EDIT"&&$disabledItem == false) $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $TableNameTemp      = $CurrentFieldTypeArray[1];
                $KeyField           = $CurrentFieldTypeArray[2];
                $ValueField         = $CurrentFieldTypeArray[3];
                $DefaultValue       = $CurrentFieldTypeArray[4];
                $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);   
                $AddSqlTemp         = "";
                switch($TableNameTemp) {
                    case 'data_banji':
                        global $班级表额外过滤条件;
                        if(is_array($班级表额外过滤条件) && count($班级表额外过滤条件)>0 ) {
                            $AddSqlTemp = " and 班级名称 in ('".join("','",$班级表额外过滤条件)."')";
                        }
                        break;
                    case 'data_student':
                        global $班级表额外过滤条件;
                        if(is_array($班级表额外过滤条件) && count($班级表额外过滤条件)>0 ) {
                            $AddSqlTemp = " and 班级 in ('".join("','",$班级表额外过滤条件)."')";
                        }
                        break;
                    case 'data_student_jiangxuejin':
                        global $班级表额外过滤条件;
                        if(in_array("申请开始时间", $MetaColumnNamesTemp) && in_array("申请结束时间", $MetaColumnNamesTemp)) {
                            $AddSqlTemp = " and 申请结束时间 >= '".date('Y-m-d')."'  and 申请开始时间 <= '".date('Y-m-d')."' ";
                        }
                        break;
                    case 'data_student_zhuxuejin':
                        global $班级表额外过滤条件;
                        if(in_array("申请开始时间", $MetaColumnNamesTemp) && in_array("申请结束时间", $MetaColumnNamesTemp)) {
                            $AddSqlTemp = " and 申请结束时间 >= '".date('Y-m-d')."'  and 申请开始时间 <= '".date('Y-m-d')."' ";
                        }
                        break;
                    case 'data_student_qingongjianxue':
                        global $班级表额外过滤条件;
                        if(in_array("申请开始时间", $MetaColumnNamesTemp) && in_array("申请结束时间", $MetaColumnNamesTemp)) {
                            $AddSqlTemp = " and 申请结束时间 >= '".date('Y-m-d')."'  and 申请开始时间 <= '".date('Y-m-d')."' ";
                        }
                        break;
                }
                if($TableNameTemp=="form_formdict" && sizeof($CurrentFieldTypeArray)==7)   {
                    $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label,ExtraControl from $TableNameTemp where 1=1 and $WhereField = '".$WhereValue."' order by SortNumber asc, `".$MetaColumnNamesTemp[$ValueField]."` asc";
                }
                elseif(sizeof($CurrentFieldTypeArray)==7 && in_array("排序号",$MetaColumnNamesTemp))   {
                    $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where 1=1 $AddSqlTemp and $WhereField = '".$WhereValue."' order by 排序号 asc, `".$MetaColumnNamesTemp[$ValueField]."` asc";
                }
                elseif(sizeof($CurrentFieldTypeArray)==7 && in_array("SortNumber",$MetaColumnNamesTemp))   {
                    $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where 1=1 $AddSqlTemp and $WhereField = '".$WhereValue."' order by SortNumber asc, `".$MetaColumnNamesTemp[$ValueField]."` asc";
                }
                elseif(sizeof($CurrentFieldTypeArray)==7 && !in_array("SortNumber",$MetaColumnNamesTemp))   {
                    $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where 1=1 $AddSqlTemp and $WhereField = '".$WhereValue."' order by `".$MetaColumnNamesTemp[$ValueField]."` asc";
                }
                elseif( (sizeof($CurrentFieldTypeArray)==5||sizeof($CurrentFieldTypeArray)==4) && in_array("排序号",$MetaColumnNamesTemp) )   {
                    $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where 1=1 $AddSqlTemp order by `排序号` asc, id asc";
                }
                elseif( (sizeof($CurrentFieldTypeArray)==5||sizeof($CurrentFieldTypeArray)==4) && !in_array("排序号",$MetaColumnNamesTemp) )   {
                    $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where 1=1 $AddSqlTemp order by `".$MetaColumnNamesTemp[$ValueField]."` asc, id asc";
                }
                else {
                    break;
                }
                if($TableNameTemp=="data_wygl_peijian") {
                    $sql = "select 物品名称 as value, CONCAT(物品名称, '[', 物品类别, ']', '(', 单价, '元)') as label from data_wygl_peijian order by 物品名称";
                }
                $rs = $db->CacheExecute(10, $sql) or print($sql);
                $FieldType = $rs->GetArray();
                if($CurrentFieldTypeArray[0]=="autocomplete"&&$DefaultValue!="") {
                    $DefaultValueTemp = $DefaultValue;
                }
                else {
                    $DefaultValueTemp = "";
                }
                //一次性同时赋值名称和代码
                if($TableNameTemp=="data_student") {
                    $FieldCodeName = str_replace("姓名","学号",$FieldName);
                }
                else if($TableNameTemp=="data_user"&&$KeyField=="1"&&$ValueField=="2") {
                    $FieldCodeName = str_replace("姓名","用户名",$FieldName);
                }
                else if($TableNameTemp=="data_course"&&$KeyField=="1"&&$ValueField=="2") {
                    $FieldCodeName = str_replace("名称","代码",$FieldName);
                }
                else    {
                    $FieldCodeName = $FieldName;
                }
                if($FieldCodeName==$FieldName) {
                    $FieldName = $FieldName."_名称";
                }
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'code' => $FieldCodeName, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'show'=>true, 'type'=>$CurrentFieldTypeArray[0], 'options'=>$FieldType, 'label' => $ShowTextName, 'value' => $DefaultValueTemp, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),'disabled' => $disabledItem==true?true:false]];
                break;
            case 'readonlyradiogroup':
                $CurrentFieldTypeArray[0] = "radiogroup";
            case 'readonlytablefilter':
                $CurrentFieldTypeArray[0] = "radiogroupcolor";
            case 'readonlyradiogroupcolor':
                $CurrentFieldTypeArray[0] = "autocomplete";
            case 'readonlytablefiltercolor':
                $CurrentFieldTypeArray[0] = "tablefiltercolor";
                $disabledItem = true;
            case 'radiogroup':
            case 'tablefilter':
            case 'radiogroupcolor':
            case 'tablefiltercolor':
                if($actionType=="EDIT"&&$disabledItem == false) $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $TableNameTemp      = $CurrentFieldTypeArray[1];
                $KeyField           = $CurrentFieldTypeArray[2];
                $ValueField         = $CurrentFieldTypeArray[3];
                $DefaultValue       = $CurrentFieldTypeArray[4];
                $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                $OrderByDesc        = ForSqlInjection($CurrentFieldTypeArray[7]);
                $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($TableNameTemp);               
                if(sizeof($CurrentFieldTypeArray)==7)   {
                    if(in_array("SortNumber",$MetaColumnNamesTemp))  {
                        $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' order by SortNumber asc, `".$MetaColumnNamesTemp[$ValueField]."` asc";
                    }
                    else {
                        $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' order by `".$MetaColumnNamesTemp[$ValueField]."` asc";
                    }
                }
                else if(sizeof($CurrentFieldTypeArray)==8)   {
                    if($WhereField == '') {
                        $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp order by `".$MetaColumnNamesTemp[$OrderByDesc]."` desc";
                    }
                    else {
                        $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp where $WhereField = '".$WhereValue."' order by `".$MetaColumnNamesTemp[$OrderByDesc]."` desc";
                    }
                }
                elseif(sizeof($CurrentFieldTypeArray)==5||sizeof($CurrentFieldTypeArray)==4)   {
                    $sql = "select `".$MetaColumnNamesTemp[$KeyField]."` as value, `".$MetaColumnNamesTemp[$ValueField]."` as label from $TableNameTemp order by `".$MetaColumnNamesTemp[$ValueField]."` asc, id asc";
                }
                else {
                    break;
                }

                if($CurrentFieldTypeArray[4]=="CurrentTerm")   {
                    $DefaultValue = returntablefield("data_xueqi","当前学期","1","学期名称")['学期名称'];
                    if($DefaultValue=="") {
                        $DefaultValue = returntablefield("data_xueqi","当前学期","是","学期名称")['学期名称'];
                    }
                    $sql .= " limit 10";
                }
                $rs = $db->CacheExecute(10, $sql) or print($sql);
                $FieldType = $rs->GetArray();
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'options'=>$FieldType, 'label' => $ShowTextName, 'value' => $DefaultValue, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),'disabled' => $disabledItem==true?true:false, 'row'=>true], 'sql'=>$sql, 'CurrentFieldTypeArray'=>$CurrentFieldTypeArray, 'MetaColumnNamesTemp'=>$MetaColumnNamesTemp];
                break;
            case 'avatar':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'Reset'=>__('Reset'), 'AvatarFormatTip'=>__('AvatarFormatTip'), 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false] ];
                break;
            case 'images':
            case 'images2':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'RemoveAll'=>__('RemoveAll') ];
                break;
            case 'files':
            case 'files2':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'RemoveAll'=>__('RemoveAll') ];
                break;
            case 'ProvinceAndCity':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $所在省     = str_replace("行政区划代码","所在省",$FieldName);
                $所在市     = str_replace("行政区划代码","所在市",$FieldName);
                $所在区县   = str_replace("行政区划代码","所在区县",$FieldName);
                $行政区划   = $FieldName;
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, '所在省'=>$所在省, '所在市'=>$所在市, '所在区县'=>$所在区县, '行政区划'=>$行政区划, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'ProvinceAndCityData'=>"ProvinceAndCity_".$Item['id']];
                break;
            case 'ProvinceAndCityOneLine':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $所在省     = $FieldName."所在省";
                $所在市     = $FieldName."所在市";
                $所在区县   = $FieldName."所在区县";
                $行政区划   = $FieldName;
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, '所在省'=>$所在省, '所在市'=>$所在市, '所在区县'=>$所在区县, '行政区划'=>$行政区划, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>4, 'disabled' => false], 'ProvinceAndCityData'=>"ProvinceAndCity_".$Item['id']];
                break;
            case 'SelectBuilding':
                //其它建筑
                $其它建筑 = [];
                $sql	= "select 建筑名称 as 名称 from data_buiding_others order by 建筑名称";
                $rs		= $db->CacheExecute(180,$sql);
                $rs_a	= $rs->GetArray();
                for($R=0;$R<sizeof($rs_a);$R++)							{
                    $其它建筑[] = $rs_a[$R]['名称'];
                }
                //宿舍楼
                $宿舍楼 = [];
                $sql	= "select 宿舍楼名称 as 名称 from data_dorm_building order by 宿舍楼名称";
                $rs		= $db->CacheExecute(180,$sql);
                $rs_a	= $rs->GetArray();
                for($R=0;$R<sizeof($rs_a);$R++)							{
                    $宿舍楼[] = $rs_a[$R]['名称'];
                }
                //教学楼
                $教学楼 = [];
                $sql	= "select 建筑名称 as 名称 from data_building order by 建筑名称";
                $rs		= $db->CacheExecute(180,$sql);
                $rs_a	= $rs->GetArray();
                for($R=0;$R<sizeof($rs_a);$R++)							{
                    $教学楼[] = $rs_a[$R]['名称'];
                }
                $GroupTwoMap = [];
                $二级菜单MAP = [];
                $二级菜单MAP['教学办公']     = $教学楼;
                $二级菜单MAP['学生宿舍']     = $宿舍楼;
                $二级菜单MAP['其它建筑']     = $其它建筑;
                $一级菜单 = array_keys($二级菜单MAP);
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'GroupOneMenuName'=>"楼房属性", 'GroupTwoMenuName'=>"楼房名称", 'GroupOneMenuValue'=>$一级菜单[2], 'GroupTwoMenuValue'=>$二级菜单MAP[$一级菜单[2]][0], 'GroupOneMenuArray'=>$一级菜单, 'GroupTwoMenuArray'=>$二级菜单MAP, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'SelectBuildingData'=>"SelectBuilding_".$Item['id'], 'GroupTwoMap'=>$GroupTwoMap];
                break;
                case '报修故障分类':
                    //报修故障分类
                    $其它建筑 = [];
                    $sql	= "select 内容,项目 from data_wygl_biaoxiucontent order by 项目,内容";
                    $rs		= $db->CacheExecute(180,$sql);
                    $rs_a	= $rs->GetArray();
                    $GroupTwoMap = [];
                    $二级菜单MAP = [];
                    $二级菜单MAP['请选择'][] = '请选择';
                    for($R=0;$R<sizeof($rs_a);$R++)							{
                        $项目 = $rs_a[$R]['项目'];
                        $内容 = $rs_a[$R]['内容'];
                        $二级菜单MAP[$项目][] = $内容;
                        $GroupTwoMap[$内容] = $项目;
                    }
                    $一级菜单 = array_keys($二级菜单MAP);
                    if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                    $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>['SelectBuilding'], 'type'=>'SelectBuilding', 'label' => $ShowTextName, 'value' => $FieldDefault, 'GroupOneMenuName'=>"故障分组", 'GroupTwoMenuName'=>"故障分类", 'GroupOneMenuValue'=>$一级菜单[0], 'GroupTwoMenuValue'=>$二级菜单MAP[$一级菜单[0]][0], 'GroupOneMenuArray'=>$一级菜单, 'GroupTwoMenuArray'=>$二级菜单MAP, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false], 'SelectBuildingData'=>"SelectBuilding_".$Item['id'], 'GroupTwoMap'=>$GroupTwoMap];
                    break;
            case '维修人员':
                if($actionType=="EDIT"&&$disabledItem == false) $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                global $USER_ID;
                $FieldType  = [];
                $sql        = "select 维修人员 from data_wygl_biaoxiuxiangmu where find_in_set('$USER_ID',负责人)";
                $rs         = $db->CacheExecute(10,$sql);
                $rs_a       = $rs->GetArray();
                $维修人员    = $rs_a[0]['维修人员'];
                $维修人员Array  = explode(',',$维修人员);
                $sql 	        = "select USER_ID as value, USER_NAME as label from data_user where USER_ID in ('".join("','",$维修人员Array)."')";
	            $rs		        = $db->Execute($sql);
                $rs_a           = $rs->GetArray();
                if(!in_array($DefaultValue, $维修人员Array)) {
                    $DefaultValue = "";
                }
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>['tablefilter'], 'type'=>'tablefilter', 'options'=>$rs_a, 'label' => $ShowTextName, 'value' => $DefaultValue, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth),'disabled' => $disabledItem==true?true:false, 'row'=>true], 'sql'=>$sql, 'CurrentFieldTypeArray'=>['tablefilter'], 'MetaColumnNamesTemp'=>$MetaColumnNamesTemp];
                break;
            case 'UserRoleMenuDetail':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                //Menu From Database
                $sql    = "select * from data_menuone order by SortNumber asc, MenuOneName asc";
                $rsf    = $db->Execute($sql);
                $MenuOneRSA  = $rsf->GetArray();
                //$sql    = "select * from data_menutwo where FaceTo='AnonymousUser' order by MenuOneName asc,SortNumber asc";
                $sql    = "select * from data_menutwo where FaceTo='AuthUser' order by SortNumber asc, id asc";
                $rsf    = $db->Execute($sql);
                $MenuTwoRSA  = $rsf->GetArray();
                $MenuTwoArray1 = [];
                $MenuTwoArray2 = [];
                $MenuTwoCount = [];
                $TabMap = [];
                foreach($MenuTwoRSA as $Item)  {
                    if($Item['MenuTab']=="Yes"||$Item['MenuTab']=="是") {
                        $TabMap[$Item['MenuOneName']][$Item['MenuTwoName']] = "Tab";
                    }
                    if($Item['MenuThreeName']!="")   {
                        $MenuTwoArray1[$Item['MenuOneName']][$Item['MenuTwoName']][] = $Item;
                    }
                    else { 
                        $MenuTwoArray1[$Item['MenuOneName']]['SystemMenuTwo_'.$Item['id']][] = $Item;
                    }
                    $MenuTwoCount[$Item['MenuOneName']] += 1;
                }                
                foreach($MenuOneRSA as $Item)  {
                    if(isset($MenuTwoArray1[$Item['MenuOneName']])) {
                        $MenuTwoArray2[$Item['MenuOneName']] = $MenuTwoArray1[$Item['MenuOneName']];
                    }
                }
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max], 'MenuTwoArray'=>$MenuTwoArray2, 'MenuTwoCount'=>$MenuTwoCount, 'SelectAll'=>__("SelectAll") ];
                break;
            case 'jumpwindow':
                if($actionType=="EDIT") $InsertOrUpdateFieldArrayForSql[$actionType][$FieldName] = "";
                $TableNameTemp      = $CurrentFieldTypeArray[1];
                $KeyField           = $CurrentFieldTypeArray[2];
                $ValueField         = $CurrentFieldTypeArray[3];
                $DefaultValue       = $CurrentFieldTypeArray[4];
                $WhereField         = ForSqlInjection($CurrentFieldTypeArray[5]);
                $WhereValue         = ForSqlInjection($CurrentFieldTypeArray[6]);
                //一次性同时赋值名称和代码
                if($TableNameTemp=="data_student") {
                    $FieldCodeName = str_replace("姓名","学号",$FieldName);
                    $jumpWindowTitle            = "学生信息查询";
                    $jumpWindowSubTitle         = "输入关键字查询";
                    $jumpWindowSearchFiledText  = "学生姓名或学号";
                    $jumpWindowSearchFiledPlaceholder   = "请输入关键字";
                }
                else if($TableNameTemp=="data_user"&&$KeyField=="1"&&$ValueField=="2") {
                    $FieldCodeName = str_replace("姓名","用户名",$FieldName);
                    $jumpWindowTitle            = "用户信息查询";
                    $jumpWindowSubTitle         = "输入关键字查询";
                    $jumpWindowSearchFiledText  = "用户名或用户姓名";
                    $jumpWindowSearchFiledPlaceholder   = "请输入关键字";
                }
                else if($TableNameTemp=="data_course"&&$KeyField=="1"&&$ValueField=="2") {
                    $FieldCodeName = str_replace("名称","代码",$FieldName);
                    $jumpWindowTitle            = "课程信息查询";
                    $jumpWindowSubTitle         = "输入关键字查询";
                    $jumpWindowSearchFiledText  = "课程名称或代码";
                    $jumpWindowSearchFiledPlaceholder   = "请输入关键字";
                }
                else if($TableNameTemp=="data_fixedasset_classification") {
                    $FieldCodeName              = str_replace("名称","代码",$FieldName);
                    $jumpWindowTitle            = "固定资产分类查询";
                    $jumpWindowSubTitle         = "输入关键字查询";
                    $jumpWindowSearchFiledText  = "固定资产分类";
                    $jumpWindowSearchFiledPlaceholder   = "请输入关键字";
                }
                else    {
                    $FieldCodeName = str_replace("名称","代码",$FieldName);
                    $jumpWindowTitle            = "查询";
                    $jumpWindowSubTitle         = "输入关键字查询.";
                    $jumpWindowSearchFiledText  = "分类";
                    $jumpWindowSearchFiledPlaceholder   = "请输入关键字";
                }
                if($FieldCodeName==$FieldName) {
                    $FieldName = $FieldName."_名称";
                }
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'code' => $FieldCodeName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false],'jumpWindowTitle'=>$jumpWindowTitle,'jumpWindowSubTitle'=>$jumpWindowSubTitle,'jumpWindowSearchFiledText'=>$jumpWindowSearchFiledText,'jumpWindowSearchFiledPlaceholder'=>$jumpWindowSearchFiledPlaceholder,'TableNameTemp'=>$TableNameTemp];
                break;
            default:
                $allFieldsMap['Default'][] = ['name' => $FieldName, 'show'=>true, 'FieldTypeArray'=>$CurrentFieldTypeArray, 'type'=>$CurrentFieldTypeArray[0], 'label' => $ShowTextName, 'value' => $FieldDefault, 'placeholder' => $Placeholder, 'helptext' => $Helptext, 'rules' => ['required' => $IsMustFill==1?true:false,'xs'=>12, 'sm'=>intval($IsFullWidth), 'disabled' => false,'min'=>$Min,'max'=>$Max]];
                break;
        }
    }
    //print_R($InsertOrUpdateFieldArrayForSql);
    return $allFieldsMap;
}

function Extra_Priv_Filter_Field_To_SQL() {
    global $AddSql;
    $AddSql .= Extra_Priv_Filter_Field_To_SQL_Item('One');
    $AddSql .= Extra_Priv_Filter_Field_To_SQL_Item('Two');
    $AddSql .= Extra_Priv_Filter_Field_To_SQL_Item('Three');
    $AddSql .= Extra_Priv_Filter_Field_To_SQL_Item('Four');
    $AddSql .= Extra_Priv_Filter_Field_To_SQL_Item('Five');
}

function Extra_Priv_Filter_Field_To_SQL_Item($Item) {
    global $db, $SettingMap, $MetaColumnNames;
    $tempsql_One = "";
    $Extra_Priv_Filter_Field_One  = $SettingMap['Extra_Priv_Filter_Field_'.$Item];
    $Extra_Priv_Filter_Method_One = $SettingMap['Extra_Priv_Filter_Method_'.$Item];
    $Extra_Priv_Filter_Value_One  = ForSqlInjection($SettingMap['Extra_Priv_Filter_Value_'.$Item]);
    if(in_array($Extra_Priv_Filter_Field_One, $MetaColumnNames))  {
        switch($Extra_Priv_Filter_Method_One)   {
            case '=':
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One = '".$Extra_Priv_Filter_Value_One."'";
                break;
            case '!=':
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One != '".$Extra_Priv_Filter_Value_One."'";
                break;
            case '>':
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One > '".$Extra_Priv_Filter_Value_One."'";
                break;
            case '>=':
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One >= '".$Extra_Priv_Filter_Value_One."'";
                break;
            case '<':
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One < '".$Extra_Priv_Filter_Value_One."'";
                break;
            case '<=':
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One <= '".$Extra_Priv_Filter_Value_One."'";
                break;
            case 'in':
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One in ('".join("','",explode(',',$Extra_Priv_Filter_Value_One))."')";
                break;
            case 'not in':
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One not in ('".join("','",explode(',',$Extra_Priv_Filter_Value_One))."')";
                break;
            case 'like':
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One like '".$Extra_Priv_Filter_Value_One."'";
                break;
            case '<->':
                $Extra_Priv_Filter_Value_One_Array = explode('-',$Extra_Priv_Filter_Value_One);
                $tempsql_One .= " and ($Extra_Priv_Filter_Field_One >= '".$Extra_Priv_Filter_Value_One_Array[0]."' and $Extra_Priv_Filter_Field_One <= '".$Extra_Priv_Filter_Value_One_Array[1]."')";
                break;
            case 'Today':
                $tempsql_One .= " and DATE_FORMAT($Extra_Priv_Filter_Field_One,'%Y-%m-%d') = '".Date("Y-m-d")."'";
                break;
            case 'BeforeDays':
                $tempsql_One .= " and DATE_FORMAT($Extra_Priv_Filter_Field_One,'%Y-%m-%d') >= '".Date("Y-m-d", strtotime(intval(0-$Extra_Priv_Filter_Value_One).' days'))."'";
                break;
            case 'AfterDays':
                $tempsql_One .= " and DATE_FORMAT($Extra_Priv_Filter_Field_One,'%Y-%m-%d') <= '".Date("Y-m-d", strtotime(intval(0+$Extra_Priv_Filter_Value_One).' days'))."'";
                break;
            case 'BeforeAndAfterDays':
                $Extra_Priv_Filter_Value_One_Array = explode('-',$Extra_Priv_Filter_Value_One);
                $tempsql_One .= " and DATE_FORMAT($Extra_Priv_Filter_Field_One,'%Y-%m-%d') >= '".Date("Y-m-d", strtotime(intval(0-$Extra_Priv_Filter_Value_One_Array[0]).' days'))."' and DATE_FORMAT($Extra_Priv_Filter_Field_One,'%Y-%m-%d') <= '".Date("Y-m-d", strtotime(intval(0+$Extra_Priv_Filter_Value_One_Array[1]).' days'))."'";
                break;
            case 'CurrentSemester':
                global $CurrentSemester;
                $tempsql_One .= " and $Extra_Priv_Filter_Field_One = '".$CurrentSemester."'";
                break;
        }
    }
    return $tempsql_One;
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

function option_multi_approval_exection($selectedRows, $multiReviewInputValue, $Reminder=1, $UpdateOtherTableField=1) {
    global $db, $SettingMap, $MetaColumnNames, $TableName, $GLOBAL_USER;
    $selectedRows   = ForSqlInjection($selectedRows);
    $selectedRows   = explode(',',$selectedRows);
    $primary_key    = $MetaColumnNames[0];
    $Batch_Approval_Status_Field    = $SettingMap['Batch_Approval_Status_Field'];
    $Batch_Approval_Status_Value    = $SettingMap['Batch_Approval_Status_Value'];
    $Batch_Approval_DateTime_Field  = $SettingMap['Batch_Approval_DateTime_Field'];
    $Batch_Approval_DateTime_Format = $SettingMap['Batch_Approval_DateTime_Format'];
    $Batch_Approval_User_Field      = $SettingMap['Batch_Approval_User_Field'];
    $Batch_Approval_User_Format     = $SettingMap['Batch_Approval_User_Format'];
    $Batch_Approval_Review_Field    = $SettingMap['Batch_Approval_Review_Field'];
    $Batch_Approval_Review_Opinion  = $SettingMap['Batch_Approval_Review_Opinion'];
    $updateSQL = [];
    if(in_array($Batch_Approval_Status_Field,$MetaColumnNames))   {
        $updateSQL[] = " $Batch_Approval_Status_Field = '".$Batch_Approval_Status_Value."' ";
    }
    if(in_array($Batch_Approval_DateTime_Field,$MetaColumnNames)&&$Batch_Approval_DateTime_Format=="Date")   {
        $updateSQL[] = " $Batch_Approval_DateTime_Field = '".date("Y-m-d")."' ";
    }
    if(in_array($Batch_Approval_DateTime_Field,$MetaColumnNames)&&$Batch_Approval_DateTime_Format=="DateTime")   {
        $updateSQL[] = " $Batch_Approval_DateTime_Field = '".date("Y-m-d H:i:s")."' ";
    }
    if(in_array($Batch_Approval_User_Field,$MetaColumnNames)&&$Batch_Approval_User_Format=="UserID")   {
        $updateSQL[] = " $Batch_Approval_User_Field = '".$GLOBAL_USER->USER_ID."' ";
    }
    if(in_array($Batch_Approval_Review_Field,$MetaColumnNames)&&$multiReviewInputValue!='')   {
        $updateSQL[] = " $Batch_Approval_Review_Field = '".$multiReviewInputValue."' ";
    }    
    $Change_Field_When_Batch_Approval_1         = $SettingMap['Change_Field_When_Batch_Approval_1'];
    $Change_Into_Value_When_Batch_Approval_1    = $SettingMap['Change_Into_Value_When_Batch_Approval_1'];
    if(in_array($Change_Field_When_Batch_Approval_1,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Approval_1 = '".$Change_Into_Value_When_Batch_Approval_1."' ";
    }
    $Change_Field_When_Batch_Approval_2         = $SettingMap['Change_Field_When_Batch_Approval_2'];
    $Change_Into_Value_When_Batch_Approval_2    = $SettingMap['Change_Into_Value_When_Batch_Approval_2'];
    if(in_array($Change_Field_When_Batch_Approval_2,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Approval_2 = '".$Change_Into_Value_When_Batch_Approval_2."' ";
    }
    $Change_Field_When_Batch_Approval_3         = $SettingMap['Change_Field_When_Batch_Approval_3'];
    $Change_Into_Value_When_Batch_Approval_3    = $SettingMap['Change_Into_Value_When_Batch_Approval_3'];
    if(in_array($Change_Field_When_Batch_Approval_3,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Approval_3 = '".$Change_Into_Value_When_Batch_Approval_3."' ";
    }
    $Change_Field_When_Batch_Approval_4         = $SettingMap['Change_Field_When_Batch_Approval_4'];
    $Change_Into_Value_When_Batch_Approval_4    = $SettingMap['Change_Into_Value_When_Batch_Approval_4'];
    if(in_array($Change_Field_When_Batch_Approval_4,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Approval_4 = '".$Change_Into_Value_When_Batch_Approval_4."' ";
    }
    $Change_Field_When_Batch_Approval_5         = $SettingMap['Change_Field_When_Batch_Approval_5'];
    $Change_Into_Value_When_Batch_Approval_5    = $SettingMap['Change_Into_Value_When_Batch_Approval_5'];
    if(in_array($Change_Field_When_Batch_Approval_5,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Approval_5 = '".$Change_Into_Value_When_Batch_Approval_5."' ";
    }
    $Change_Field_When_Batch_Approval_6         = $SettingMap['Change_Field_When_Batch_Approval_6'];
    $Change_Into_Value_When_Batch_Approval_6    = $SettingMap['Change_Into_Value_When_Batch_Approval_6'];
    if(in_array($Change_Field_When_Batch_Approval_6,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Approval_6 = '".$Change_Into_Value_When_Batch_Approval_6."' ";
    }
    $Change_Field_When_Batch_Approval_7         = $SettingMap['Change_Field_When_Batch_Approval_7'];
    $Change_Into_Value_When_Batch_Approval_7    = $SettingMap['Change_Into_Value_When_Batch_Approval_7'];
    if(in_array($Change_Field_When_Batch_Approval_7,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Approval_7 = '".$Change_Into_Value_When_Batch_Approval_7."' ";
    }
    $Change_Field_When_Batch_Approval_8         = $SettingMap['Change_Field_When_Batch_Approval_8'];
    $Change_Into_Value_When_Batch_Approval_8    = $SettingMap['Change_Into_Value_When_Batch_Approval_8'];
    if(in_array($Change_Field_When_Batch_Approval_8,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Approval_8 = '".$Change_Into_Value_When_Batch_Approval_8."' ";
    }
    
    $sqlArray = [];
    if($selectedRows[0]!=""&&count($updateSQL)>0) {
        $RS             = [];
        foreach($selectedRows as $id) {
            if(strlen($id)>30) {
                $id         = intval(DecryptID($id));
            }
            $sql        = "update $TableName set ".join(',',$updateSQL)." where $primary_key = '$id'";
            $db->Execute($sql);
            $sqlArray[] = $sql;
            if($Reminder)   $RS['Msg_Reminder'][$id] = Msg_Reminder_Object_From_Add_Or_Edit($TableName, $id);
            if($UpdateOtherTableField) UpdateOtherTableFieldAfterFormSubmit($id);
        }  
        //SystemLogRecord
        if(in_array($SettingMap['OperationLogGrade'],["EditAndDeleteOperation","AddEditAndDeleteOperation","AllOperation"]))  {
            SystemLogRecord("option_multi_approval", '', json_encode($sqlArray));
        }
        $RS['status']   = "OK";
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  {
            $RS['sqlArray'] = $sqlArray;
            global $GLOBAL_EXEC_KEY_SQL;
            $RS['GLOBAL_EXEC_KEY_SQL'] = $GLOBAL_EXEC_KEY_SQL;
        }
        $RS['msg']      = __("Update Success");
        return json_encode($RS);
    }
    else {
        $RS             = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("Params Error");
        $RS['updateSQL']= $updateSQL;
        $RS['_GET']     = $_GET;
        $RS['_POST']    = $_POST;
        return json_encode($RS);
    }  
}

function option_multi_refuse_exection($selectedRows, $multiReviewInputValue, $Reminder=1, $UpdateOtherTableField=1) {
    global $db, $SettingMap, $MetaColumnNames, $TableName, $GLOBAL_USER;
    $selectedRows   = ForSqlInjection($selectedRows);
    $selectedRows   = explode(',',$selectedRows);
    $primary_key    = $MetaColumnNames[0];
    $Batch_Refuse_Status_Field    = $SettingMap['Batch_Refuse_Status_Field'];
    $Batch_Refuse_Status_Value    = $SettingMap['Batch_Refuse_Status_Value'];
    $Batch_Refuse_DateTime_Field  = $SettingMap['Batch_Refuse_DateTime_Field'];
    $Batch_Refuse_DateTime_Format = $SettingMap['Batch_Refuse_DateTime_Format'];
    $Batch_Refuse_User_Field      = $SettingMap['Batch_Refuse_User_Field'];
    $Batch_Refuse_User_Format     = $SettingMap['Batch_Refuse_User_Format'];
    $Batch_Refuse_Review_Field    = $SettingMap['Batch_Refuse_Review_Field'];
    $Batch_Refuse_Review_Opinion  = $SettingMap['Batch_Refuse_Review_Opinion'];
    $updateSQL = [];
    if(in_array($Batch_Refuse_Status_Field,$MetaColumnNames))   {
        $updateSQL[] = " $Batch_Refuse_Status_Field = '".$Batch_Refuse_Status_Value."' ";
    }
    if(in_array($Batch_Refuse_DateTime_Field,$MetaColumnNames)&&$Batch_Refuse_DateTime_Format=="Date")   {
        $updateSQL[] = " $Batch_Refuse_DateTime_Field = '".date("Y-m-d")."' ";
    }
    if(in_array($Batch_Refuse_DateTime_Field,$MetaColumnNames)&&$Batch_Refuse_DateTime_Format=="DateTime")   {
        $updateSQL[] = " $Batch_Refuse_DateTime_Field = '".date("Y-m-d H:i:s")."' ";
    }
    if(in_array($Batch_Refuse_User_Field,$MetaColumnNames)&&$Batch_Refuse_User_Format=="UserID")   {
        $updateSQL[] = " $Batch_Refuse_User_Field = '".$GLOBAL_USER->USER_ID."' ";
    }
    if(in_array($Batch_Refuse_Review_Field,$MetaColumnNames)&&$multiReviewInputValue!='')   {
        $updateSQL[] = " $Batch_Refuse_Review_Field = '".$multiReviewInputValue."' ";
    }    
    $Change_Field_When_Batch_Refuse_1         = $SettingMap['Change_Field_When_Batch_Refuse_1'];
    $Change_Into_Value_When_Batch_Refuse_1    = $SettingMap['Change_Into_Value_When_Batch_Refuse_1'];
    if(in_array($Change_Field_When_Batch_Refuse_1,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Refuse_1 = '".$Change_Into_Value_When_Batch_Refuse_1."' ";
    }
    $Change_Field_When_Batch_Refuse_2         = $SettingMap['Change_Field_When_Batch_Refuse_2'];
    $Change_Into_Value_When_Batch_Refuse_2    = $SettingMap['Change_Into_Value_When_Batch_Refuse_2'];
    if(in_array($Change_Field_When_Batch_Refuse_2,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Refuse_2 = '".$Change_Into_Value_When_Batch_Refuse_2."' ";
    }
    $Change_Field_When_Batch_Refuse_3         = $SettingMap['Change_Field_When_Batch_Refuse_3'];
    $Change_Into_Value_When_Batch_Refuse_3    = $SettingMap['Change_Into_Value_When_Batch_Refuse_3'];
    if(in_array($Change_Field_When_Batch_Refuse_3,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Refuse_3 = '".$Change_Into_Value_When_Batch_Refuse_3."' ";
    }
    $Change_Field_When_Batch_Refuse_4         = $SettingMap['Change_Field_When_Batch_Refuse_4'];
    $Change_Into_Value_When_Batch_Refuse_4    = $SettingMap['Change_Into_Value_When_Batch_Refuse_4'];
    if(in_array($Change_Field_When_Batch_Refuse_4,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Refuse_4 = '".$Change_Into_Value_When_Batch_Refuse_4."' ";
    }
    $Change_Field_When_Batch_Refuse_5         = $SettingMap['Change_Field_When_Batch_Refuse_5'];
    $Change_Into_Value_When_Batch_Refuse_5    = $SettingMap['Change_Into_Value_When_Batch_Refuse_5'];
    if(in_array($Change_Field_When_Batch_Refuse_5,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Refuse_5 = '".$Change_Into_Value_When_Batch_Refuse_5."' ";
    }
    $Change_Field_When_Batch_Refuse_6         = $SettingMap['Change_Field_When_Batch_Refuse_6'];
    $Change_Into_Value_When_Batch_Refuse_6    = $SettingMap['Change_Into_Value_When_Batch_Refuse_6'];
    if(in_array($Change_Field_When_Batch_Refuse_6,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Refuse_6 = '".$Change_Into_Value_When_Batch_Refuse_6."' ";
    }
    
    $sqlArray = [];
    if($selectedRows[0]!=""&&count($updateSQL)>0) {
        $RS             = [];
        foreach($selectedRows as $id) {
            if(strlen($id)>30) {
                $id         = intval(DecryptID($id));
            }
            $sql        = "update $TableName set ".join(',',$updateSQL)." where $primary_key = '$id'";
            $db->Execute($sql);
            $sqlArray[] = $sql;
            if($Reminder)   $RS['Msg_Reminder'][$id] = Msg_Reminder_Object_From_Add_Or_Edit($TableName, $id);
            if($UpdateOtherTableField) UpdateOtherTableFieldAfterFormSubmit($id);
        }
        //SystemLogRecord
        if(in_array($SettingMap['OperationLogGrade'],["EditAndDeleteOperation","AddEditAndDeleteOperation","AllOperation"]))  {
            SystemLogRecord("option_multi_refuse", '', json_encode($sqlArray));
        }
        $RS['status']   = "OK";
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes")  {
            $RS['sqlArray'] = $sqlArray;
            global $GLOBAL_EXEC_KEY_SQL;
            $RS['GLOBAL_EXEC_KEY_SQL'] = $GLOBAL_EXEC_KEY_SQL;
        }
        $RS['msg']      = __("Update Success");
        return json_encode($RS);
    }
    else {
        $RS             = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("Params Error");
        $RS['updateSQL']= $updateSQL;
        $RS['_GET']     = $_GET;
        $RS['_POST']    = $_POST;
        return json_encode($RS);
    } 
}

function option_multi_cancel_exection($selectedRows, $multiReviewInputValue, $Reminder=1, $UpdateOtherTableField=1) {
    global $db, $SettingMap, $MetaColumnNames, $TableName, $GLOBAL_USER;
    $selectedRows   = ForSqlInjection($selectedRows);
    $selectedRows   = explode(',',$selectedRows);
    $primary_key    = $MetaColumnNames[0];
    $Batch_Cancel_Status_Field    = $SettingMap['Batch_Cancel_Status_Field'];
    $Batch_Cancel_Status_Value    = $SettingMap['Batch_Cancel_Status_Value'];
    $Batch_Cancel_DateTime_Field  = $SettingMap['Batch_Cancel_DateTime_Field'];
    $Batch_Cancel_DateTime_Format = $SettingMap['Batch_Cancel_DateTime_Format'];
    $Batch_Cancel_User_Field      = $SettingMap['Batch_Cancel_User_Field'];
    $Batch_Cancel_User_Format     = $SettingMap['Batch_Cancel_User_Format'];
    $Batch_Cancel_Review_Field    = $SettingMap['Batch_Cancel_Review_Field'];
    $Batch_Cancel_Review_Opinion  = $SettingMap['Batch_Cancel_Review_Opinion'];
    $updateSQL = [];
    if(in_array($Batch_Cancel_Status_Field,$MetaColumnNames))   {
        $updateSQL[] = " $Batch_Cancel_Status_Field = '".$Batch_Cancel_Status_Value."' ";
    }
    if(in_array($Batch_Cancel_DateTime_Field,$MetaColumnNames)&&$Batch_Cancel_DateTime_Format=="Date")   {
        $updateSQL[] = " $Batch_Cancel_DateTime_Field = '".date("Y-m-d")."' ";
    }
    if(in_array($Batch_Cancel_DateTime_Field,$MetaColumnNames)&&$Batch_Cancel_DateTime_Format=="DateTime")   {
        $updateSQL[] = " $Batch_Cancel_DateTime_Field = '".date("Y-m-d H:i:s")."' ";
    }
    if(in_array($Batch_Cancel_User_Field,$MetaColumnNames)&&$Batch_Cancel_User_Format=="UserID")   {
        $updateSQL[] = " $Batch_Cancel_User_Field = '".$GLOBAL_USER->USER_ID."' ";
    }
    if(in_array($Batch_Cancel_Review_Field,$MetaColumnNames)&&$multiReviewInputValue!='')   {
        $updateSQL[] = " $Batch_Cancel_Review_Field = '".$multiReviewInputValue."' ";
    }    
    $Change_Field_When_Batch_Cancel_1         = $SettingMap['Change_Field_When_Batch_Cancel_1'];
    $Change_Into_Value_When_Batch_Cancel_1    = $SettingMap['Change_Into_Value_When_Batch_Cancel_1'];
    if(in_array($Change_Field_When_Batch_Cancel_1,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Cancel_1 = '".$Change_Into_Value_When_Batch_Cancel_1."' ";
    }
    $Change_Field_When_Batch_Cancel_2         = $SettingMap['Change_Field_When_Batch_Cancel_2'];
    $Change_Into_Value_When_Batch_Cancel_2    = $SettingMap['Change_Into_Value_When_Batch_Cancel_2'];
    if(in_array($Change_Field_When_Batch_Cancel_2,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Cancel_2 = '".$Change_Into_Value_When_Batch_Cancel_2."' ";
    }
    $Change_Field_When_Batch_Cancel_3         = $SettingMap['Change_Field_When_Batch_Cancel_3'];
    $Change_Into_Value_When_Batch_Cancel_3    = $SettingMap['Change_Into_Value_When_Batch_Cancel_3'];
    if(in_array($Change_Field_When_Batch_Cancel_3,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Cancel_3 = '".$Change_Into_Value_When_Batch_Cancel_3."' ";
    }
    $Change_Field_When_Batch_Cancel_4         = $SettingMap['Change_Field_When_Batch_Cancel_4'];
    $Change_Into_Value_When_Batch_Cancel_4    = $SettingMap['Change_Into_Value_When_Batch_Cancel_4'];
    if(in_array($Change_Field_When_Batch_Cancel_4,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Cancel_4 = '".$Change_Into_Value_When_Batch_Cancel_4."' ";
    }
    $Change_Field_When_Batch_Cancel_5         = $SettingMap['Change_Field_When_Batch_Cancel_5'];
    $Change_Into_Value_When_Batch_Cancel_5    = $SettingMap['Change_Into_Value_When_Batch_Cancel_5'];
    if(in_array($Change_Field_When_Batch_Cancel_5,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Cancel_5 = '".$Change_Into_Value_When_Batch_Cancel_5."' ";
    }
    $Change_Field_When_Batch_Cancel_6         = $SettingMap['Change_Field_When_Batch_Cancel_6'];
    $Change_Into_Value_When_Batch_Cancel_6    = $SettingMap['Change_Into_Value_When_Batch_Cancel_6'];
    if(in_array($Change_Field_When_Batch_Cancel_6,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Cancel_6 = '".$Change_Into_Value_When_Batch_Cancel_6."' ";
    }
    $Change_Field_When_Batch_Cancel_7         = $SettingMap['Change_Field_When_Batch_Cancel_7'];
    $Change_Into_Value_When_Batch_Cancel_7    = $SettingMap['Change_Into_Value_When_Batch_Cancel_7'];
    if(in_array($Change_Field_When_Batch_Cancel_7,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Cancel_7 = '".$Change_Into_Value_When_Batch_Cancel_7."' ";
    }
    $Change_Field_When_Batch_Cancel_8         = $SettingMap['Change_Field_When_Batch_Cancel_8'];
    $Change_Into_Value_When_Batch_Cancel_8    = $SettingMap['Change_Into_Value_When_Batch_Cancel_8'];
    if(in_array($Change_Field_When_Batch_Cancel_8,$MetaColumnNames))   {
        $updateSQL[] = " $Change_Field_When_Batch_Cancel_8 = '".$Change_Into_Value_When_Batch_Cancel_8."' ";
    }
    
    $sqlArray = [];
    if($selectedRows[0]!=""&&count($updateSQL)>0) {
        $RS             = [];
        foreach($selectedRows as $id) {
            if(strlen($id)>30) {
                $id         = intval(DecryptID($id));
            }
            $sql        = "update $TableName set ".join(',',$updateSQL)." where $primary_key = '$id'";
            $db->Execute($sql);
            $sqlArray[] = $sql;
            if($Reminder)   $RS['Msg_Reminder'][$id] = Msg_Reminder_Object_From_Add_Or_Edit($TableName, $id);
            if($UpdateOtherTableField) UpdateOtherTableFieldAfterFormSubmit($id);
        }
        //SystemLogRecord
        if(in_array($SettingMap['OperationLogGrade'],["EditAndDeleteOperation","AddEditAndDeleteOperation","AllOperation"]))  {
            SystemLogRecord("option_multi_cancel", '', json_encode($sqlArray));
        }
        $RS['status']   = "OK";
        if($SettingMap['Debug_Sql_Show_On_Api']=="Yes" && 1)  {
            $RS['sqlArray'] = $sqlArray;
            global $GLOBAL_EXEC_KEY_SQL;
            $RS['GLOBAL_EXEC_KEY_SQL'] = $GLOBAL_EXEC_KEY_SQL;
        }
        $RS['msg']      = __("Update Success");
        return json_encode($RS);
    }
    else {
        $RS             = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("Params Error");
        $RS['updateSQL']= $updateSQL;
        $RS['_GET']     = $_GET;
        $RS['_POST']    = $_POST;
        return json_encode($RS);
    } 
}

function UpdateOtherTableFieldAfterFormSubmit($id)  {
    global $db, $SettingMap, $MetaColumnNames, $TableName, $GLOBAL_USER;
    $GLOBAL_MetaTables = GLOBAL_MetaTables();
    $OperationAfterSubmit = $SettingMap['OperationAfterSubmit'];
    $OperationAfterSubmit_Which_Field_Name = $SettingMap['OperationAfterSubmit_Which_Field_Name'];
    $OperationAfterSubmit_Which_Field_Value = $SettingMap['OperationAfterSubmit_Which_Field_Value'];
    $OperationAfterSubmit_Need_Update_Table_Name = $SettingMap['OperationAfterSubmit_Need_Update_Table_Name'];
    $OperationAfterSubmit_Need_Update_Table_Field_Name = $SettingMap['OperationAfterSubmit_Need_Update_Table_Field_Name'];
    $OperationAfterSubmit_Need_Update_Table_Field_Value = $SettingMap['OperationAfterSubmit_Need_Update_Table_Field_Value'];
    $OperationAfterSubmit_SameField_This_Table = $SettingMap['OperationAfterSubmit_SameField_This_Table'];
    $OperationAfterSubmit_SameField_Other_Table = $SettingMap['OperationAfterSubmit_SameField_Other_Table'];
    $OperationAfterSubmit_Update_Mode = $SettingMap['OperationAfterSubmit_Update_Mode'];
    if($OperationAfterSubmit_Which_Field_Value!="" && $OperationAfterSubmit_Which_Field_Name!="" && $OperationAfterSubmit_Which_Field_Name!="None" && in_array($OperationAfterSubmit_Which_Field_Name, $MetaColumnNames))  {
        $CompareValueArray = returntablefield($TableName,"id",$id,"".$OperationAfterSubmit_Which_Field_Name.",".$OperationAfterSubmit_SameField_This_Table."");
        $OperationAfterSubmit_Which_Field_Value_Array = explode(',',$OperationAfterSubmit_Which_Field_Value);
        if( $CompareValueArray[$OperationAfterSubmit_Which_Field_Name]!="" 
            && in_array($CompareValueArray[$OperationAfterSubmit_Which_Field_Name],$OperationAfterSubmit_Which_Field_Value_Array)
            && in_array($OperationAfterSubmit_Need_Update_Table_Name,$GLOBAL_MetaTables)
            )  {
                $MetaColumnNamesTemp    = GLOBAL_MetaColumnNames($OperationAfterSubmit_Need_Update_Table_Name); 
                if($OperationAfterSubmit_SameField_Other_Table!="" && in_array($OperationAfterSubmit_SameField_Other_Table,$MetaColumnNamesTemp))    {
                    $sql    = "select * from $TableName where ".$MetaColumnNames[0]." = '$id'";
                    $rs     = $db->Execute($sql);
                    $Line   = $rs->fields;
                    $OperationAfterSubmit_Need_Update_Table_Field_Value = ParamsFilter($OperationAfterSubmit_Need_Update_Table_Field_Value);
                    foreach($Line as $TempField=>$TempValue)  {
                        $OperationAfterSubmit_Need_Update_Table_Field_Value = str_replace("[$TempField]",$TempValue,$OperationAfterSubmit_Need_Update_Table_Field_Value);
                    }
                    $sql = "update `".$OperationAfterSubmit_Need_Update_Table_Name."` set `".$OperationAfterSubmit_Need_Update_Table_Field_Name."`='".$OperationAfterSubmit_Need_Update_Table_Field_Value."' where `".$OperationAfterSubmit_SameField_Other_Table."` = '".$CompareValueArray[$OperationAfterSubmit_SameField_This_Table]."' ";
                    if($OperationAfterSubmit_Update_Mode=="Update One Record")   {
                        $sql .= "limit 1";
                    }
                    $db->Execute($sql);
                    global $GLOBAL_EXEC_KEY_SQL;
                    $GLOBAL_EXEC_KEY_SQL['UpdateOtherTableFieldAfterFormSubmit'][] = $sql;
                }
        }
    }
}

?>