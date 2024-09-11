<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');

$USER_ID    = ForSqlInjection($GLOBAL_USER->USER_ID);
$USER_NAME    = ForSqlInjection($GLOBAL_USER->USER_NAME);
$DEPT_ID    = ForSqlInjection($GLOBAL_USER->DEPT_ID);

$Page_Role_Name = $SettingMap['Page_Role_Name'];
global $AdditionalPermissionsSQL;

switch($Page_Role_Name)  {
    case 'Student':
        if(in_array('学号',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 学号 = '".$USER_ID."' ";
        }
        elseif(in_array('学生学号',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 学生学号 = '".$USER_ID."' ";
        }
        elseif(in_array('班级',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 班级 = '".ForSqlInjection($GLOBAL_USER->班级)."' ";
        }
        elseif(in_array('班级名称',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 班级名称 = '".ForSqlInjection($GLOBAL_USER->班级)."' ";
        }
        elseif(in_array('所属班级',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 所属班级 = '".ForSqlInjection($GLOBAL_USER->班级)."' ";
        }
        $AddSql .= $AdditionalPermissionsSQL;
        break;
    case 'ClassMaster':
    case '班主任':
        $sql = "select 班级名称 from data_banji where 是否毕业='否' and (find_in_set('$USER_NAME',实习班主任) or find_in_set('$USER_ID',实习班主任) or (班主任用户名='$USER_ID'))";
        $rs = $db->CacheExecute(10,$sql);
        $rs_a = $rs->GetArray();
        $班级名称Array = [];
        foreach($rs_a as $Line) {
            $班级名称Array[] = ForSqlInjection($Line['班级名称']);
        }
        if(sizeof($班级名称Array)==0) {
            $班级名称Array[] = "额外权限过滤";
        }
        if(in_array('班级',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 班级 in ('".join("','",$班级名称Array)."')";
        }
        elseif(in_array('班级名称',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 班级名称 in ('".join("','",$班级名称Array)."')";
        }
        elseif(in_array('学生班级',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 学生班级 in ('".join("','",$班级名称Array)."')";
        }
        elseif(in_array('所属班级',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 所属班级 in ('".join("','",$班级名称Array)."')";
        }
        $AddSql .= $AdditionalPermissionsSQL;
        global $班级表额外过滤条件;
        $班级表额外过滤条件 = $班级名称Array;
        break;
    case 'Faculty':
    case '院系':
        $Faculty_Filter_Field = $SettingMap['Faculty_Filter_Field'];
        if($Faculty_Filter_Field=="" || $Faculty_Filter_Field=="None" || $Faculty_Filter_Field=="无") {
            break;
        }
        $sql = "select 系部名称 from data_xi where find_in_set('$USER_ID',$Faculty_Filter_Field)";
        $rs = $db->CacheExecute(10,$sql);
        $rs_a = $rs->GetArray();
        $系部名称Array = [];
        foreach($rs_a as $Line) {
            $系部名称Array[] = ForSqlInjection($Line['系部名称']);
        }
        $sql = "select 班级名称 from data_banji where 是否毕业='否' and 所属系部 in ('".join("','",$系部名称Array)."')";
        $rs = $db->CacheExecute(10,$sql);
        $rs_a = $rs->GetArray();
        $班级名称Array = [];
        foreach($rs_a as $Line) {
            $班级名称Array[] = ForSqlInjection($Line['班级名称']);
        }
        if(sizeof($班级名称Array)==0) {
            $班级名称Array[] = "额外权限过滤";
        }
        if(in_array('班级',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 班级 in ('".join("','",$班级名称Array)."')";
        }
        elseif(in_array('班级名称',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 班级名称 in ('".join("','",$班级名称Array)."')";
        }
        elseif(in_array('学生班级',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 学生班级 in ('".join("','",$班级名称Array)."')";
        }
        elseif(in_array('所属班级',$MetaColumnNames))  {
            $AdditionalPermissionsSQL .= " and 所属班级 in ('".join("','",$班级名称Array)."')";
        }
        $AddSql .= $AdditionalPermissionsSQL;
        global $班级表额外过滤条件;
        $班级表额外过滤条件 = $班级名称Array;
        break;
    case 'Dormitory':
    case '宿舍管理员':
        $sql = "select * from data_dorm_building where find_in_set('$USER_ID',生管老师一) or find_in_set('$USER_ID',生管老师二) or find_in_set('$USER_ID',生管老师三) or find_in_set('$USER_ID',生管老师四) or find_in_set('$USER_ID',生管老师五) or find_in_set('$USER_ID',生管老师六) or find_in_set('$USER_ID',生管老师七) or find_in_set('$USER_ID',生管老师八) or find_in_set('$USER_ID',生管老师九) or find_in_set('$USER_ID',生管老师十) or find_in_set('$USER_NAME',生管老师一) or find_in_set('$USER_NAME',生管老师二) or find_in_set('$USER_NAME',生管老师三) or find_in_set('$USER_NAME',生管老师四) or find_in_set('$USER_NAME',生管老师五) or find_in_set('$USER_NAME',生管老师六) or find_in_set('$USER_NAME',生管老师七) or find_in_set('$USER_NAME',生管老师八) or find_in_set('$USER_NAME',生管老师九) or find_in_set('$USER_NAME',生管老师十)";
        $rs = $db->CacheExecute(10,$sql);
        $rs_a = $rs->GetArray();
        $宿舍房间Array = [];
        $宿舍楼名称Array = [];
        foreach($rs_a as $Line) {
            $宿舍楼名称         = $Line['宿舍楼名称'];
            $管理楼层           = [];
            $管理楼层[]         = $Line['管理范围一'];
            $管理楼层[]         = $Line['管理范围二'];
            $管理楼层[]         = $Line['管理范围三'];
            $管理楼层[]         = $Line['管理范围四'];
            $管理楼层[]         = $Line['管理范围五'];
            $管理楼层[]         = $Line['管理范围六'];
            $管理楼层[]         = $Line['管理范围七'];
            $管理楼层[]         = $Line['管理范围八'];
            $管理楼层[]         = $Line['管理范围九'];
            $管理楼层[]         = $Line['管理范围十'];
            $管理楼层TEXT       = join(',',$管理楼层);
            $管理楼层ARRAY      = explode(',',$管理楼层TEXT);
            $管理楼层FLIP       = array_flip($管理楼层ARRAY);
            $管理楼层FLIP       = array_keys($管理楼层FLIP);
            $sql = "select 宿舍房间 from data_dorm_dorm where 宿舍楼='$宿舍楼名称' and 楼层数 in ('".join("','",$管理楼层FLIP)."')";
            $rs = $db->CacheExecute(10,$sql);
            $rsX_a = $rs->GetArray();
            foreach($rsX_a as $LineX) {
                $宿舍房间Array[] = ForSqlInjection($LineX['宿舍房间']);
            }
            $宿舍楼名称Array[] = $宿舍楼名称;
        }
        $Dormitory_Filter_Field = $SettingMap['Dormitory_Filter_Field'];
        if($Dormitory_Filter_Field=="" || $Dormitory_Filter_Field=="None" || $Dormitory_Filter_Field=="无") {
            break;
        }
        if($Dormitory_Filter_Field=="宿舍楼") {
            if(sizeof($宿舍楼名称Array)==0) {
                $宿舍楼名称Array[] = "额外权限过滤";
            }
            if(in_array('楼房名称',$MetaColumnNames))  {
                $AdditionalPermissionsSQL .= " and 楼房名称 in ('".join("','",$宿舍楼名称Array)."')";
            }
            if(in_array('宿舍楼',$MetaColumnNames))  {
                $AdditionalPermissionsSQL .= " and 宿舍楼 in ('".join("','",$宿舍楼名称Array)."')";
            }
        }
        else {
            if(sizeof($宿舍房间Array)==0) {
                $宿舍房间Array[] = "额外权限过滤";
            }
            if(in_array('宿舍房间',$MetaColumnNames))  {
                $AdditionalPermissionsSQL .= " and 宿舍房间 in ('".join("','",$宿舍房间Array)."')";
            }
            elseif(in_array('学生宿舍',$MetaColumnNames))  {
                $AdditionalPermissionsSQL .= " and 学生宿舍 in ('".join("','",$宿舍房间Array)."')";
            }
            elseif(in_array('所属宿舍',$MetaColumnNames))  {
                $AdditionalPermissionsSQL .= " and 所属宿舍 in ('".join("','",$宿舍房间Array)."')";
            }
            elseif(in_array('房间名称',$MetaColumnNames))  {
                $AdditionalPermissionsSQL .= " and 房间名称 in ('".join("','",$宿舍房间Array)."')";
            }
            elseif(in_array('楼房地址',$MetaColumnNames))  {
                $AdditionalPermissionsSQL .= " and 楼房地址 in ('".join("','",$宿舍房间Array)."')";
            }
            elseif(in_array('房间',$MetaColumnNames))  {
                $AdditionalPermissionsSQL .= " and 房间 in ('".join("','",$宿舍房间Array)."')";
            }
            elseif(in_array('宿舍号',$MetaColumnNames))  {
                $AdditionalPermissionsSQL .= " and 宿舍号 in ('".join("','",$宿舍房间Array)."')";
            }
        }
        $AddSql .= $AdditionalPermissionsSQL;
        break;
}

//Add HiddenUser Sql Filter
foreach($AllFieldsFromTable as $Item)  {
    $FieldName      = $Item['FieldName'];
    $FieldTypeInFlow = $SettingMap['FieldType_'.$FieldName];
    switch($FieldTypeInFlow)   {
        case 'HiddenUserID':
            $AdditionalPermissionsSQL .= " and $FieldName = '".$USER_ID."' ";
            $AddSql .= $AdditionalPermissionsSQL;
            break;
        case 'HiddenUsername':
            $AdditionalPermissionsSQL .= " and $FieldName = '".$USER_NAME."' ";
            $AddSql .= $AdditionalPermissionsSQL;
            break;
        case 'HiddenDeptID':
            $AdditionalPermissionsSQL .= " and $FieldName = '".$DEPT_ID."' ";
            $AddSql .= $AdditionalPermissionsSQL;
            break;
        case 'HiddenDeptName':
            $DEPT_NAME = returntablefield("data_department","id",$DEPT_ID,"DEPT_NAME")['DEPT_NAME'];
            $AdditionalPermissionsSQL .= " and $FieldName = '".$DEPT_NAME."' ";
            $AddSql .= $AdditionalPermissionsSQL;
            break;
            break;
        case 'HiddenStudentID':
            $AdditionalPermissionsSQL .= " and $FieldName = '".$USER_ID."' ";
            $AddSql .= $AdditionalPermissionsSQL;
            break;
        case 'HiddenStudentName':
            $AdditionalPermissionsSQL .= " and $FieldName = '".$USER_NAME."' ";
            $AddSql .= $AdditionalPermissionsSQL;
            break;
        case 'HiddenStudentClass':
            $AdditionalPermissionsSQL .= " and $FieldName = '".$USER_CLASS."' ";
            $AddSql .= $AdditionalPermissionsSQL;
            break;
    }
}


?>