<?php
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

global $GLOBAL_USER;

$payload    = file_get_contents('php://input');
$_POST      = json_decode($payload, true);

function GetRedisKeyWithCache($Field) {
  global $redis;
  $GetContent = $redis->hget("DDCenter_API_Cache", $Field);
  if($GetContent == "")   {
    return false;
  }
  $GetContentArray = json_decode($GetContent, true);
  if($GetContentArray['ExpireTime'] < time())   {
    //过期,需要更新
    return false;
  }
  $GetContentArray['Source'] = "Redis";
  return $GetContentArray;
}

function SetRedisKeyWithCache($Field, $Content) {
  global $redis;
  $redis->hset("DDCenter_API_Cache", $Field, $Content);
}

if($_POST['action']=="dbsource") {
    $缓存中的值 = GetRedisKeyWithCache($Field='dbsource');
    if($缓存中的值 == false)   {
      $sql    = "select 连接池名称 as label, id as value from data_datasource where 连接状态='正常' order by id desc";
      $rs     = $db->Execute($sql);
      $rs_a   = $rs->GetArray();
      for($i=0;$i<sizeof($rs_a );$i++) {
          $rs_a[$i]['value'] = EncryptIDFixed($rs_a[$i]['value']);
      }
      $缓存中的值               = [];
      $缓存中的值['Content']    = $rs_a;
      $缓存中的值['ExpireTime'] = time() + rand(120, 180);
      SetRedisKeyWithCache($Field='dbsource', json_encode($缓存中的值));
      $缓存中的值['Source'] = "Db";
    }
    $RS = [];
    $RS['status']   = "OK";
    $RS['action']   = $_POST['action'];
    $RS['data']     = $缓存中的值['Content'];
    $RS['Source']   = $缓存中的值['Source'];
    $RS['msg']      = "获取远程数据源成功";
    print json_encode($RS);
    exit;
}

$id         = DecryptIDFixed($_POST['dbId']);
if($_POST['action']=="db" && $id>0) {
    $sql            = "select * from data_datasource where id='$id' ";
    $rs             = $db->Execute($sql);
    $远程数据库信息  = $rs->fields;
    if($远程数据库信息['数据库用户名']!="")    {
      $缓存中的值 = GetRedisKeyWithCache($Field='db_'.$id);
      if($缓存中的值 == false)   {
        $db_remote = NewADOConnection($DB_TYPE='mysqli');
        $db_remote->connect($远程数据库信息['数据库主机'], $远程数据库信息['数据库用户名'], DecryptIDFixed($远程数据库信息['数据库密码']), $远程数据库信息['数据库名称']);
        $db_remote->Execute("Set names utf8;");
        $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
        if($db_remote->database==$远程数据库信息['数据库名称']) {
            $MetaTables = $db_remote->MetaTables();

            $缓存中的值               = [];
            $缓存中的值['Content']    = $MetaTables;
            $缓存中的值['ExpireTime'] = time() + 180;
            SetRedisKeyWithCache($Field='db_'.$id, json_encode($缓存中的值));
            $缓存中的值['Source'] = "Db";

            $RS = [];
            $RS['status']   = "OK";
            $RS['action']   = $_POST['action'];
            $RS['data']     = $MetaTables;
            $RS['Source']   = $缓存中的值['Source'];
            $RS['msg']      = "获取远程数据表列表成功";
            print json_encode($RS);
            exit;
        }
      }
      else {
        $RS = [];
        $RS['status']   = "OK";
        $RS['action']   = $_POST['action'];
        $RS['data']     = $缓存中的值['Content'];
        $RS['Source']   = $缓存中的值['Source'];
        $RS['msg']      = "获取远程数据表列表成功";
        print json_encode($RS);
        exit;
      }
    }
    $RS = [];
    $RS['status']   = "ERROR";
    $RS['action']   = $_POST['action'];
    $RS['db']       = $rs_a;
    $RS['msg']      = "获取远程数据表列表失败";
    print json_encode($RS);
    exit;
}

$id         = DecryptIDFixed($_POST['dbId']);
$table      = ForSqlInjection($_POST['table']);
if($_POST['action']=="table" && $id!="" && $table!="") {
    $sql            = "select * from data_datasource where id='$id' ";
    $rs             = $db->Execute($sql);
    $远程数据库信息  = $rs->fields;
    if($远程数据库信息['数据库用户名']!="")    {
      $缓存中的值 = GetRedisKeyWithCache($Field='table_'.$id."_".$table);
      if($缓存中的值 == false)   {
        $db_remote = NewADOConnection($DB_TYPE='mysqli');
        $db_remote->connect($远程数据库信息['数据库主机'], $远程数据库信息['数据库用户名'], DecryptIDFixed($远程数据库信息['数据库密码']), $远程数据库信息['数据库名称']);
        $db_remote->Execute("Set names utf8;");
        $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
        if($db_remote->database==$远程数据库信息['数据库名称']) {
            $MetaColumnNames    = $db_remote->MetaColumnNames($table);
            if(is_array($MetaColumnNames)) {
                $MetaColumnNames    = array_values($MetaColumnNames);

                $缓存中的值               = [];
                $缓存中的值['Content']    = $MetaColumnNames;
                $缓存中的值['ExpireTime'] = time() + 180;
                SetRedisKeyWithCache($Field='table_'.$id."_".$table, json_encode($缓存中的值));
                $缓存中的值['Source']     = "Db";

                $RS = [];
                $RS['status']   = "OK";
                $RS['action']   = $_POST['action'];
                $RS['data']     = $MetaColumnNames;
                $RS['Source']   = $缓存中的值['Source'];
                $RS['msg']      = "获取远程数据表结构成功";
                print json_encode($RS);
                exit;
            }
            else {
                exit;
            }
        }
      }
      else {
        $RS = [];
        $RS['status']   = "OK";
        $RS['action']   = $_POST['action'];
        $RS['data']     = $缓存中的值['Content'];
        $RS['Source']   = $缓存中的值['Source'];
        $RS['msg']      = "获取远程数据表列表成功";
        print json_encode($RS);
        exit;
      }
    }
    $RS = [];
    $RS['status']   = "ERROR";
    $RS['action']   = $_POST['action'];
    $RS['db']       = $rs_a;
    $RS['msg']      = "获取远程数据表结构失败";
    print json_encode($RS);
    exit;
}

$projectId  = intval(DecryptIDFixed($_POST['projectId']));
$sectionId  = ForSqlInjection($_POST['id']);
//{"dimensions":["积分时间","班级学生积分之和"],"source":[{"班级学生积分之和":"1.0","积分时间":"2023-06-01"},{"班级学生积分之和":"1.0","积分时间":"2023-06-06"},{"班级学生积分之和":"1.0","积分时间":"2023-06-17"},{"班级学生积分之和":"3.0","积分时间":"2023-06-18"},{"班级学生积分之和":"19.0","积分时间":"2023-06-19"},{"班级学生积分之和":"2.0","积分时间":"2023-06-21"},{"班级学生积分之和":"3.0","积分时间":"2023-06-22"},{"班级学生积分之和":"10.0","积分时间":"2023-06-29"},{"班级学生积分之和":"28.0","积分时间":"2023-07-02"}]}
if($_POST['action']=="ListData"&&$projectId!=''&&$sectionId!="") {
    //从模板项目中得到数据库的配置信息
    $sql    = "select * from data_goview_project where id='$projectId'";
    $rs     = $db->Execute($sql);
    $RS     = json_decode(base64_decode($rs->fields['content']),true);
    $componentList = (array)$RS['componentList'];
    $指定图表配置信息 = null;
    foreach($componentList as $Item) {
      if($Item['id'] == $sectionId) {
        $指定图表配置信息 = $Item['request']['requestSQLContent'];
      }
    }
    if($指定图表配置信息 == null) {
      print "没有到指定图表的配置信息";
      exit;
    }

    $id         = DecryptIDFixed($指定图表配置信息['dbId']);
    $table      = $指定图表配置信息['table'];
    $Targetsql  = $指定图表配置信息['sql'];
    $Targetsql  = str_replace('"','',$Targetsql);
    $Targetsql  = str_replace("#",'',$Targetsql);
    $Targetsql  = str_replace("@",'',$Targetsql);
    $Targetsql  = str_replace("insert",'',$Targetsql);
    $Targetsql  = str_replace("update",'',$Targetsql);
    $Targetsql  = str_replace("delete",'',$Targetsql);
    $Targetsql  = str_replace("create",'',$Targetsql);
    $Targetsql  = str_replace("drop",'',$Targetsql);
    $Targetsql  = str_replace(" table ",'',$Targetsql);

    //print $id; print_R($Targetsql);exit;
    //之前的业务逻辑
    $sql            = "select * from data_datasource where id='$id' ";
    $rs             = $db->Execute($sql);
    $远程数据库信息  = $rs->fields;
    if($远程数据库信息['数据库用户名']!="")    {
      $缓存中的值 = GetRedisKeyWithCache($Field='ListData_'.$id."_".md5($Targetsql));
      if($缓存中的值 == false)   {
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

                $ResultData    = ['dimensions'=>$dimensions,'source'=>$rs_a_remote];

                $缓存中的值               = [];
                $缓存中的值['Content']    = $ResultData;
                $缓存中的值['ExpireTime'] = time() + 180;
                SetRedisKeyWithCache($Field='ListData_'.$id."_".md5($Targetsql), json_encode($缓存中的值));
                $缓存中的值['Source']     = "Db";

                $RS = [];
                $RS['status']   = "OK";
                $RS['data']     = $ResultData;
                if($GLOBAL_USER->USER_ID == 'admin')  {
                  $RS['action']           = $_POST['action'];
                  $RS['Targetsql']        = $Targetsql;
                  $RS['sql']              = $sql;
                }
                $RS['Source']   = $缓存中的值['Source'];
                $RS['msg']      = "获取远程数据成功";
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

                $ResultData    = $NewRSA;

                $缓存中的值               = [];
                $缓存中的值['Content']    = $ResultData;
                $缓存中的值['ExpireTime'] = time() + 180;
                SetRedisKeyWithCache($Field='ListData_'.$id."_".md5($Targetsql), json_encode($缓存中的值));
                $缓存中的值['Source']     = "Db";

                $RS = [];
                $RS['status']   = "OK";
                $RS['data']     = $ResultData;
                if($GLOBAL_USER->USER_ID == 'admin')  {
                  $RS['action']           = $_POST['action'];
                  $RS['Targetsql']        = $Targetsql;
                  $RS['sql']              = $sql;
                }
                $RS['Source']   = $缓存中的值['Source'];
                $RS['msg']      = "获取远程数据成功";
                print json_encode($RS);
                exit;
            }
        }
      }
      else {
        $RS = [];
        $RS['status']   = "OK";
        $RS['action']   = $_POST['action'];
        $RS['data']     = $缓存中的值['Content'];
        $RS['Source']   = $缓存中的值['Source'];
        $RS['msg']      = "获取远程数据表列表成功";
        print json_encode($RS);
        exit;
      }
    }
    $RS = [];
    $RS['status']           = "ERROR";
    if($GLOBAL_USER->USER_ID == 'admin')  {
      $RS['action']           = $_POST['action'];
      $RS['Targetsql']        = $Targetsql;
      $RS['sql']              = $sql;
    }
    $RS['指定图表配置信息']   = $指定图表配置信息;
    $RS['msg']              = "获取远程数据失败-没有获得到数据源记录";
    print json_encode($RS);
    exit;
}

?>
