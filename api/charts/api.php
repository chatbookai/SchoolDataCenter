<?php
header("Content-Type: application/json"); 
require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$payload    = file_get_contents('php://input');
$_POST      = json_decode($payload, true);

if($_POST['action']=="dbsource") {
    $sql    = "select 连接池名称 as label, id as value from data_datasource where 连接状态='正常' order by id desc";
    $rs     = $db->Execute($sql);
    $rs_a   = $rs->GetArray();
    for($i=0;$i<sizeof($rs_a );$i++) {
        $rs_a[$i]['value'] = EncryptIDFixed($rs_a[$i]['value']);
    }
    $RS = [];
    $RS['status']   = "OK";
    $RS['data']     = $rs_a;
    $RS['msg']      = "获取远程数据源成功";
    print json_encode($RS);
    exit; 
}

$id         = DecryptIDFixed($_POST['id']);
if($_POST['action']=="db"&&$id>0) {
    $sql            = "select * from data_datasource where id='$id' ";
    $rs             = $db->Execute($sql);
    $远程数据库信息  = $rs->fields;
    if($远程数据库信息['数据库用户名']!="")    {
        $db_remote = NewADOConnection($DB_TYPE='mysqli');
        $db_remote->connect($远程数据库信息['数据库主机'], $远程数据库信息['数据库用户名'], DecryptIDFixed($远程数据库信息['数据库密码']), $远程数据库信息['数据库名称']);
        $db_remote->Execute("Set names utf8;");
        $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
        if($db_remote->database==$远程数据库信息['数据库名称']) {
            $MetaTables = $db_remote->MetaTables();
            $RS = [];
            $RS['status']   = "OK";
            $RS['sql']      = $sql;
            $RS['data']     = $MetaTables;
            $RS['msg']      = "获取远程数据表列表成功";
            print json_encode($RS);
            exit;
        }
    }
    $RS = [];
    $RS['status']   = "ERROR";
    $RS['db']       = $rs_a;
    $RS['msg']      = "获取远程数据表列表失败";
    print json_encode($RS);
    exit; 
}

$id         = DecryptIDFixed($_POST['id']);
$table      = ForSqlInjection($_POST['table']);
if($_POST['action']=="table"&&$id>0&&$table!="") {
    $sql            = "select * from data_datasource where id='$id' ";
    $rs             = $db->Execute($sql);
    $远程数据库信息  = $rs->fields;
    if($远程数据库信息['数据库用户名']!="")    {
        $db_remote = NewADOConnection($DB_TYPE='mysqli');
        $db_remote->connect($远程数据库信息['数据库主机'], $远程数据库信息['数据库用户名'], DecryptIDFixed($远程数据库信息['数据库密码']), $远程数据库信息['数据库名称']);
        $db_remote->Execute("Set names utf8;");
        $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
        if($db_remote->database==$远程数据库信息['数据库名称']) {
            $MetaColumnNames    = $db_remote->MetaColumnNames($table);
            if(is_array($MetaColumnNames)) {
                $MetaColumnNames    = array_values($MetaColumnNames);
                $RS = [];
                $RS['status']   = "OK";
                $RS['data']     = $MetaColumnNames;
                $RS['msg']      = "获取远程数据表结构成功";
                print json_encode($RS);
                exit;
            }
            else {
                exit;
            }
        }
    }
    $RS = [];
    $RS['status']   = "ERROR";
    $RS['db']       = $rs_a;
    $RS['msg']      = "获取远程数据表结构失败";
    print json_encode($RS);
    exit; 
}

$id         = DecryptIDFixed($_POST['id']);
$table      = ForSqlInjection($_POST['table']);
$sql        = $_POST['sql'];
$sql        = str_replace('"','',$sql);
//$sql        = str_replace("'",'',$sql);
$sql        = str_replace("#",'',$sql);
$Targetsql  = str_replace("@",'',$sql);
//{"dimensions":["积分时间","班级学生积分之和"],"source":[{"班级学生积分之和":"1.0","积分时间":"2023-06-01"},{"班级学生积分之和":"1.0","积分时间":"2023-06-06"},{"班级学生积分之和":"1.0","积分时间":"2023-06-17"},{"班级学生积分之和":"3.0","积分时间":"2023-06-18"},{"班级学生积分之和":"19.0","积分时间":"2023-06-19"},{"班级学生积分之和":"2.0","积分时间":"2023-06-21"},{"班级学生积分之和":"3.0","积分时间":"2023-06-22"},{"班级学生积分之和":"10.0","积分时间":"2023-06-29"},{"班级学生积分之和":"28.0","积分时间":"2023-07-02"}]}
if($_POST['action']=="sql"&&$id>0&&$table!=""&&$Targetsql!="") {
    $sql            = "select * from data_datasource where id='$id' ";
    $rs             = $db->Execute($sql);
    $远程数据库信息  = $rs->fields;
    if($远程数据库信息['数据库用户名']!="")    {
        $db_remote = NewADOConnection($DB_TYPE='mysqli');
        $db_remote->connect($远程数据库信息['数据库主机'], $远程数据库信息['数据库用户名'], DecryptIDFixed($远程数据库信息['数据库密码']), $远程数据库信息['数据库名称']);
        $db_remote->Execute("Set names utf8;");
        $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
        //重新过滤要执行的SQL语句
        if(strpos($Targetsql,"[当前学期]")>0) {
            $sql        = "select 学期名称 from td_edu.edu_xueqiexec where 当前学期='1'";
            $rs_remote  = $db_remote->Execute($sql);  
            $当前学期    = $rs_remote->fields['学期名称'];
            $Targetsql  = str_replace("[当前学期]","'".$当前学期."'",$Targetsql);
        }
        if($db_remote->database==$远程数据库信息['数据库名称']) {
            $rs_remote          = $db_remote->Execute($Targetsql);            
            if($rs_remote && strpos($Targetsql, "group by")!==false)        {
                $rs_a_remote        = $rs_remote->GetArray();
                if(is_array($rs_a_remote)&&count($rs_a_remote)>0) {
					$dimensions         = @array_keys(@$rs_a_remote[0]);
				}
				else {
					$dimensions = [];
				}
                $RS = [];
                //$RS['rs_a_remote']  = $rs_a_remote;
                $RS['status']       = "OK";
                $RS['data']         = ['dimensions'=>$dimensions,'source'=>$rs_a_remote];
                $RS['Targetsql']    = $Targetsql;
                $RS['msg']          = "获取远程数据成功";
                print json_encode($RS);
                exit;
            }
            if($rs_remote && strpos($Targetsql, "group by")===false)        {
                $rs_a_remote        = $rs_remote->GetArray();
                if(is_array($rs_a_remote)&&count($rs_a_remote)>0) {
					$dimensions         = @array_keys(@$rs_a_remote[0]);
				}
				else {
					$dimensions = [];
				}
                $RS         = [];
                $NewRSA     = [];
                foreach($rs_a_remote as $Line) {
                    $NewRSA[]       = array_values($Line);
                }
                //$RS['rs_a_remote']  = $rs_a_remote;
                $RS['status']       = "OK";
                $RS['data']         = $NewRSA;
                $RS['Targetsql']    = $Targetsql;
                $RS['msg']          = "获取远程数据成功";
                print json_encode($RS);
                exit;
            }
        }
    }
    $RS = [];
    $RS['status']           = "ERROR";
    $RS['Targetsql']        = $Targetsql;
    $RS['msg']              = "获取远程数据失败";
    print json_encode($RS);
    exit; 
}

?>