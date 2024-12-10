<?php
header("Content-Type: application/json");
require_once('../include.inc.php');

if($_GET['SERVER_NAME']!="dsj.fjsmlyxx.com" && $_GET['action'] != "do")   {
  exit;
}

$sql  = "select * from data_datasource";
$rs   = $db->Execute($sql);
$rs_a = $rs->GetArray();
$数据库信息 = [];
foreach($rs_a as $Item)   {
  $Item['数据库密码'] = DecryptIDFixed($Item['数据库密码']);
  $数据库信息[$Item['数据库名称']] = $Item;
}
$数据库连接池 = $数据库信息['td_edu'];
if($数据库连接池['数据库名称']!="")  {
  开始同步基础数据($数据库连接池);
}
//print_R($数据库连接池);

function 清空原有表并按原结构复制所有数据($数据表, $sql) {
  global $db;
  global $db_remote;
  if($db_remote && substr($数据表, 0 , 5) == "data_" && $sql)  {
    $rsR = $db_remote->Execute($sql);
    $rs_a = $rsR->GetArray();
    //print_R($rs_a);
    $VALUES_LIST        = [];
    foreach($rs_a as $Item) {
        $KEYS           = array_keys($Item);
        $VALUES         = array_values($Item);
        $VALUES_LIST[]  = "('".join("','",$VALUES)."')";
    }
    $sql = "TRUNCATE TABLE $数据表;";
    $db->Execute($sql);
    for ($i = 0; $i < sizeof($VALUES_LIST); $i += 100) {
      $VALUES_MULTI = array_slice($VALUES_LIST, $i, 100);
      $sql = "insert into $数据表(".join(',',$KEYS).") values " . join(",", $VALUES_MULTI) . " ; ";
      //print_r($sql . "\n");
      $db->Execute($sql);
      $Counter++;
    }
  }
}

function 开始同步基础数据($数据库连接池)  {
  global $db;
  global $SettingMap;
  global $MetaColumnNames;
  global $GLOBAL_USER;
  global $TableName;
  global $db_remote;
  //Here is your write code
  $数据库主机     = $数据库连接池['数据库主机'];
  $数据库用户名   = $数据库连接池['数据库用户名'];
  $数据库密码     = $数据库连接池['数据库密码'];
  $数据库名称     = "TD_OA";
  $db_remote = NewADOConnection($DB_TYPE);
  $db_remote->connect($数据库主机, $数据库用户名, $数据库密码, $数据库名称);
  $db_remote->Execute("Set names utf8;");
  $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
  if($db_remote->databaseName!="") {
      $sql = "select `td_houqin`.`edu_jifen_geren_function`.`编号` AS `id`,`td_houqin`.`edu_jifen_geren_function`.`名称` AS `名称`,`td_houqin`.`edu_jifen_geren_function`.`描述` AS `描述`,`td_houqin`.`edu_jifen_geren_function`.`排序号` AS `排序号`,`td_houqin`.`edu_jifen_geren_function`.`涉及数据` AS `涉及数据`,`td_houqin`.`edu_jifen_geren_function`.`最近更新` AS `最近更新`,`td_houqin`.`edu_jifen_geren_function`.`数据表名称` AS `数据表`,`td_houqin`.`edu_jifen_geren_function`.`SQL名称` AS `SQL名称`,`td_houqin`.`edu_jifen_geren_function`.`类型字段` AS `类型字段`,`td_houqin`.`edu_jifen_geren_function`.`类型的值` AS `类型的值`,`td_houqin`.`edu_jifen_geren_function`.`最近更新` AS `创建人`,`td_houqin`.`edu_jifen_geren_function`.`最近更新` AS `创建时间` from `td_houqin`.`edu_jifen_geren_function`";
      清空原有表并按原结构复制所有数据($数据表="data_deyu_geren_function", $sql);

      $sql = "select `td_houqin`.`edu_jifen_geren_gradeone`.`编号` AS `id`,`td_houqin`.`edu_jifen_geren_gradeone`.`名称` AS `名称`,`td_houqin`.`edu_jifen_geren_gradeone`.`描述` AS `描述`,`td_houqin`.`edu_jifen_geren_gradeone`.`最高分值` AS `最高分值`,`td_houqin`.`edu_jifen_geren_gradeone`.`最低分值` AS `最低分值`,`td_houqin`.`edu_jifen_geren_gradeone`.`排序号` AS `排序号`,`td_houqin`.`edu_jifen_geren_gradeone`.`图标` AS `图标`,`td_houqin`.`edu_jifen_geren_gradeone`.`颜色` AS `颜色` from `td_houqin`.`edu_jifen_geren_gradeone`";
      清空原有表并按原结构复制所有数据($数据表="data_deyu_geren_gradeone", $sql);

      $sql = "select `td_houqin`.`edu_jifen_geren_gradethree`.`编号` AS `id`,`td_houqin`.`edu_jifen_geren_gradethree`.`二级指标` AS `一级指标`,`td_houqin`.`edu_jifen_geren_gradethree`.`二级指标` AS `二级指标`,`td_houqin`.`edu_jifen_geren_gradethree`.`积分项目` AS `积分项目`,`td_houqin`.`edu_jifen_geren_gradethree`.`积分编码` AS `积分编码`,`td_houqin`.`edu_jifen_geren_gradethree`.`积分分值` AS `积分分值`,`td_houqin`.`edu_jifen_geren_gradethree`.`排序号` AS `排序号`,`td_houqin`.`edu_jifen_geren_gradethree`.`管理人员列表` AS `管理人员`,`td_houqin`.`edu_jifen_geren_gradethree`.`是否班主任录入` AS `是否班主任录入`,`td_houqin`.`edu_jifen_geren_gradethree`.`数据来源` AS `数据来源`,`td_houqin`.`edu_jifen_geren_gradethree`.`备注` AS `备注`,`td_houqin`.`edu_jifen_geren_gradethree`.`备注` AS `创建人`,`td_houqin`.`edu_jifen_geren_gradethree`.`备注` AS `创建时间`,`td_houqin`.`edu_jifen_geren_gradethree`.`是否班主任录入` AS `是否班主任审核`,`td_houqin`.`edu_jifen_geren_gradethree`.`是否班主任录入` AS `是否学工审核`,`td_houqin`.`edu_jifen_geren_gradethree`.`是否班主任录入` AS `是否系部审核`,`td_houqin`.`edu_jifen_geren_gradethree`.`是否班主任录入` AS `是否学校审核` from `td_houqin`.`edu_jifen_geren_gradethree`";
      清空原有表并按原结构复制所有数据($数据表="data_deyu_geren_gradethree", $sql);

      $sql = "select `td_houqin`.`edu_jifen_geren_gradetwo`.`编号` AS `id`,`td_houqin`.`edu_jifen_geren_gradetwo`.`一级指标` AS `一级指标`,`td_houqin`.`edu_jifen_geren_gradetwo`.`二级指标` AS `二级指标`,`td_houqin`.`edu_jifen_geren_gradetwo`.`备注` AS `备注`,`td_houqin`.`edu_jifen_geren_gradetwo`.`排序号` AS `排序号`,`td_houqin`.`edu_jifen_geren_gradetwo`.`备注` AS `创建人`,`td_houqin`.`edu_jifen_geren_gradetwo`.`备注` AS `创建时间` from `td_houqin`.`edu_jifen_geren_gradetwo`";
      清空原有表并按原结构复制所有数据($数据表="data_deyu_geren_gradetwo", $sql);

      $sql = "select `td_houqin`.`edu_jifen_geren_record`.`编号` AS `id`,`td_houqin`.`edu_jifen_geren_record`.`学期名称` AS `学期`,`td_houqin`.`edu_jifen_geren_gradetwo`.`一级指标` AS `一级指标`,`td_houqin`.`edu_jifen_geren_record`.`二级指标` AS `二级指标`,`td_houqin`.`edu_jifen_geren_record`.`学号` AS `学号`,`td_houqin`.`edu_jifen_geren_record`.`姓名` AS `姓名`,`td_houqin`.`edu_jifen_geren_record`.`班级` AS `班级`,`td_houqin`.`edu_jifen_geren_record`.`积分项目` AS `积分项目`,`td_houqin`.`edu_jifen_geren_record`.`积分编码` AS `积分编码`,`td_houqin`.`edu_jifen_geren_record`.`积分分值` AS `积分分值`,`td_houqin`.`edu_jifen_geren_record`.`积分原因` AS `积分原因`,`td_houqin`.`edu_jifen_geren_record`.`积分时间` AS `积分时间`,`td_houqin`.`edu_jifen_geren_record`.`备注` AS `备注`,`td_houqin`.`edu_jifen_geren_record`.`附件` AS `附件`,`td_houqin`.`edu_jifen_geren_record`.`创建人` AS `创建人`,`td_houqin`.`edu_jifen_geren_record`.`创建时间` AS `创建时间`,`td_houqin`.`edu_jifen_geren_record`.`数据录入` AS `数据录入`,`td_houqin`.`edu_jifen_geren_record`.`其它说明` AS `学生代录`,`td_houqin`.`edu_jifen_geren_record`.`班主任审核状态` AS `班主任审核状态`,`td_houqin`.`edu_jifen_geren_record`.`创建时间` AS `班主任审核时间`,`td_houqin`.`edu_jifen_geren_record`.`班主任` AS `班主任审核人`,`td_houqin`.`edu_jifen_geren_record`.`班主任审核状态` AS `班主任审核意见`,`td_houqin`.`edu_jifen_geren_record`.`跟踪管理` AS `系部审核状态`,`td_houqin`.`edu_jifen_geren_record`.`跟踪管理` AS `系部审核时间`,`td_houqin`.`edu_jifen_geren_record`.`跟踪管理` AS `系部审核人`,`td_houqin`.`edu_jifen_geren_record`.`跟踪管理` AS `系部审核意见`,`td_houqin`.`edu_jifen_geren_record`.`学工审核状态` AS `学工审核状态`,`td_houqin`.`edu_jifen_geren_record`.`创建时间` AS `学工审核时间`,`td_houqin`.`edu_jifen_geren_record`.`学工审核人` AS `学工审核人`,`td_houqin`.`edu_jifen_geren_record`.`学工审核意见` AS `学工审核意见`,`td_houqin`.`edu_jifen_geren_record`.`校级审核状态` AS `学校审核状态`,`td_houqin`.`edu_jifen_geren_record`.`创建时间` AS `学校审核时间`,`td_houqin`.`edu_jifen_geren_record`.`校级审核人` AS `学校审核人`,`td_houqin`.`edu_jifen_geren_record`.`校级审核状态` AS `学校审核意见` from (`td_houqin`.`edu_jifen_geren_record` join `td_houqin`.`edu_jifen_geren_gradetwo`) where (`td_houqin`.`edu_jifen_geren_record`.`学期名称` = '2024-2025-第一学期' and `td_houqin`.`edu_jifen_geren_gradetwo`.`二级指标` = `td_houqin`.`edu_jifen_geren_record`.`二级指标`)";
      清空原有表并按原结构复制所有数据($数据表="data_deyu_geren_record", $sql);



  }
  else {
  }
  //print_R($db_remote->databaseName);
}


?>
