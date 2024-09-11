<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');


if( $_GET['action']=="add_default_data" || $_GET['action']=="edit_default_data") {
    foreach($AllFieldsFromTable as $Item)  {
        $FieldTypeInFlow = $SettingMap['FieldType_'.$Item['FieldName']];
        if($_POST[$Item['FieldName']]!="")    {
            //print $Item['ShowType']."<BR>";
            switch($Item['ShowType']) {
                case 'Banji:Name':
                    $sql     = "select * from data_banji where 班级名称 = '".ForSqlInjection($_POST[$Item['FieldName']])."'";
                    $rsf     = $db->CacheExecute(10,$sql);
                    if($_POST['专业']=="")          $_POST['专业']      = $rsf->fields['所属专业'];
                    if($_POST['专业名称']=="")      $_POST['专业名称']   = $rsf->fields['所属专业'];
                    if($_POST['系部']=="")          $_POST['系部']      = $rsf->fields['所属系部'];
                    if($_POST['系部名称']=="")      $_POST['系部名称']   = $rsf->fields['所属系部'];
                    $_POST['入学年份']  = $rsf->fields['入学年份'];
                    $_POST['级别']      = $rsf->fields['入学年份'];
                    $_POST['校区']      = $rsf->fields['所属校区'];
                    $_POST['固定教室']   = $rsf->fields['固定教室'];
                    $sql                = "select count(*) AS NUM from data_student where 班级='".ForSqlInjection($_POST[$Item['FieldName']])."' and 学生状态='正常状态'";
                    $rsf                = $db->CacheExecute(10,$sql);
                    $_POST['班级人数']   = $rsf->fields['NUM'];
                    //print_R($rsf->fields);
                    //print_R($sql);
                    break;
                case 'Student:SelectOneID':
                case 'Student:SelectOneName':
                    if($_GET['action']=="add_default_data")  {
                        $sql     = "select * from data_student where 学号 = '".ForSqlInjection($_POST['学号'])."'";
                        $rsf     = $db->Execute($sql);
                        $_POST['姓名']      = $rsf->fields['姓名'];
                        $_POST['学号']      = $rsf->fields['学号'];
                        $_POST['系部']      = $rsf->fields['系部'];
                        $_POST['专业']      = $rsf->fields['专业'];
                        $_POST['班级']      = $rsf->fields['班级'];
                        $_POST['身份证号']  = $rsf->fields['身份证号'];
                        $_POST['出生日期']  = $rsf->fields['出生日期'];
                        $_POST['性别']      = $rsf->fields['性别'];
                        $_POST['座号']      = $rsf->fields['座号'];
                        $_POST['学生宿舍']  = $rsf->fields['学生宿舍'];
                        $_POST['床位号']    = $rsf->fields['床位号'];
                        $_POST['学生状态']  = $rsf->fields['学生状态'];
                        $_POST['学生手机']  = $rsf->fields['学生手机'];
                    }
                    //print_R($rsf->fields);
                    //print_R($_POST);
                    break;
                case 'Course:Name':
                    $sql     = "select * from data_course where 课程名称 = '".ForSqlInjection($_POST[$Item['FieldName']])."'";
                    $rsf     = $db->CacheExecute(10,$sql);
                    $_POST['课程类型']      = $rsf->fields['课程类型'];
                    $_POST['课程类别']      = $rsf->fields['课程类别'];
                    $_POST['教研室']        = $rsf->fields['教研室'];
                    break;
                case 'Input:Password':
                    if($_POST[$Item['FieldName']]!="")   {
                        $_POST[$Item['FieldName']] = password_make($_POST[$Item['FieldName']]);
                    }
                    break;
                case 'Input:EncryptField':
                    if($_POST[$Item['FieldName']]!="")   {
                        $_POST[$Item['FieldName']] = EncryptID($_POST[$Item['FieldName']]);
                    }
                    break;
                case '德育量化:积分项目':
                    $sql     = "select * from data_deyu_geren_gradethree where 积分编码 = '".ForSqlInjection($_POST[$Item['FieldName']])."'";
                    $rsf     = $db->CacheExecute(10,$sql);
                    $_POST['积分编码']      = $rsf->fields['积分编码'];
                    $_POST['积分项目']      = $rsf->fields['积分项目'];
                    $_POST['一级指标']      = $rsf->fields['一级指标'];
                    $_POST['二级指标']      = $rsf->fields['二级指标'];
                    $_POST['积分分值']      = $rsf->fields['积分分值'];
                    break;
                case '班级评价:积分项目':
                    $sql     = "select * from data_deyu_banji_gradethree where 积分编码 = '".ForSqlInjection($_POST[$Item['FieldName']])."'";
                    $rsf     = $db->CacheExecute(10,$sql);
                    $_POST['积分编码']      = $rsf->fields['积分编码'];
                    $_POST['积分项目']      = $rsf->fields['积分项目'];
                    $_POST['一级指标']      = $rsf->fields['一级指标'];
                    $_POST['二级指标']      = $rsf->fields['二级指标'];
                    $_POST['积分分值']      = $rsf->fields['积分分值'];
                    break;
            }
        }
        //Reset Value By System Setting
        switch($FieldTypeInFlow) {
            case 'HiddenUserID':
                $_POST[$Item['FieldName']] = $GLOBAL_USER->USER_ID;
                break;
            case 'HiddenUsername':
                $_POST[$Item['FieldName']] = $GLOBAL_USER->USER_NAME;
                break;
            case 'HiddenDeptID':
                $_POST[$Item['FieldName']] = $GLOBAL_USER->DEPT_ID;
                break;
            case 'HiddenDeptName':
                $_POST[$Item['FieldName']] = $GLOBAL_USER->DEPT_NAME;
                break;
            case 'HiddenStudentID':
                $_POST[$Item['FieldName']] = $GLOBAL_USER->USER_ID;
                if($_GET['action']=="add_default_data")  {
                    $sql     = "select * from data_student where 学号 = '".ForSqlInjection($GLOBAL_USER->USER_ID)."'";
                    $rsf     = $db->Execute($sql);
                    $_POST['姓名']      = $rsf->fields['姓名'];
                    $_POST['学号']      = $rsf->fields['学号'];
                    $_POST['系部']      = $rsf->fields['系部'];
                    $_POST['专业']      = $rsf->fields['专业'];
                    $_POST['班级']      = $rsf->fields['班级'];
                    $_POST['身份证号']  = $rsf->fields['身份证号'];
                    $_POST['出生日期']  = $rsf->fields['出生日期'];
                    $_POST['性别']      = $rsf->fields['性别'];
                    $_POST['座号']      = $rsf->fields['座号'];
                    $_POST['学生宿舍']  = $rsf->fields['学生宿舍'];
                    $_POST['床位号']    = $rsf->fields['床位号'];
                    $_POST['学生状态']  = $rsf->fields['学生状态'];
                    $_POST['学生手机']  = $rsf->fields['学生手机'];
                }
                //print $sql;
                //print_R($rsf->fields);
                break;
            case 'HiddenStudentName':
                $_POST[$Item['FieldName']] = $GLOBAL_USER->USER_NAME;
                break;
            case 'HiddenStudentClass':
                $_POST[$Item['FieldName']] = $GLOBAL_USER->班级;
                break;
        }
        //print_R($_POST);
    }
}

?>