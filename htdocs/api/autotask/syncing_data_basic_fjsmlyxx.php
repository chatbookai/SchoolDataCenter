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
      $sql = "select `td_oa`.`td_user`.`UID` AS `id`,`td_oa`.`td_user`.`BYNAME` AS `USER_ID`,`td_oa`.`td_user`.`USER_NAME` AS `USER_NAME`,`td_oa`.`td_user`.`BYNAME` AS `NICKNAME`,`td_oa`.`td_user`.`USEING_KEY` AS `USEING_KEY`,`td_oa`.`td_user`.`PASSWORD` AS `PASSWORD`,`td_oa`.`td_user`.`USER_PRIV` AS `USER_PRIV`,`td_oa`.`td_user`.`DEPT_ID` AS `DEPT_ID`,`td_oa`.`td_user`.`SEX` AS `GENDER`,`td_oa`.`td_user`.`BIRTHDAY` AS `BIRTHDAY`,`td_oa`.`td_user`.`TEL_NO_DEPT` AS `TEL_NO_DEPT`,`td_oa`.`td_user`.`MOBIL_NO` AS `MOBILE_NO`,`td_oa`.`td_user`.`EMAIL` AS `EMAIL`,`td_oa`.`td_user`.`AVATAR` AS `AVATAR`,`td_oa`.`td_user`.`LAST_VISIT_TIME` AS `LAST_VISIT_TIME`,`td_oa`.`td_user`.`LAST_PASS_TIME` AS `LAST_PASS_TIME`,`td_oa`.`td_user`.`THEME` AS `THEME`,`td_oa`.`td_user`.`USER_PRIV` AS `USER_PRIV_OTHER`,`td_oa`.`td_user`.`USER_NO` AS `USER_NO`,`td_oa`.`td_user`.`NOT_LOGIN` AS `NOT_LOGIN`,`td_oa`.`td_user`.`NOT_SEARCH` AS `NOT_SEARCH`,`td_oa`.`td_user`.`BIND_IP` AS `BIND_IP`,`td_oa`.`td_user`.`LAST_VISIT_IP` AS `LAST_VISIT_IP`,`td_oa`.`td_user`.`WEATHER_CITY` AS `WEATHER_CITY`,`td_oa`.`td_user`.`MENU_EXPAND` AS `MENU_EXPAND`,`td_oa`.`td_user`.`LIMIT_LOGIN` AS `LIMIT_LOGIN`,`td_oa`.`td_user`.`NOT_MOBILE_LOGIN` AS `NOT_MOBILE_LOGIN` from `td_oa`.`td_user`";
      清空原有表并按原结构复制所有数据($数据表="data_user", $sql);

      $sql = "select `td_oa`.`department`.`DEPT_ID` AS `id`,`td_oa`.`department`.`DEPT_NAME` AS `DEPT_NAME`,`td_oa`.`department`.`TEL_NO` AS `TEL_NO`,`td_oa`.`department`.`DEPT_ADDRESS` AS `DEPT_ADDRESS`,`td_oa`.`department`.`DEPT_NO` AS `DEPT_NO`,`td_oa`.`department`.`DEPT_NO` AS `MANAGER`,`td_oa`.`department`.`DEPT_NO` AS `LEADER1`,`td_oa`.`department`.`DEPT_NO` AS `LEADER2`,`td_oa`.`department`.`DEPT_FUNC` AS `DESCRIPTION`,`td_oa`.`department`.`FAX_NO` AS `MANAGER2` from `td_oa`.`department`";
      清空原有表并按原结构复制所有数据($数据表="data_department", $sql);


      #################################################################################
      $sql = "select * from user_priv";
      $rs = $db_remote->Execute($sql);
      $rs_a = $rs->GetArray();
      for($i=0;$i<sizeof($rs_a);$i++) {
          $Element = [];
          $Element['id']          = $rs_a[$i]['USER_PRIV'];
          $Element['name']        = $rs_a[$i]['PRIV_NAME'];
          $Element['content']     = $rs_a[$i]['FUNC_ID_STR'];
          $Element['CreateTime']  = date("Y-m-d H:i:s");
          $Element['Creator']     = "admin";
          [$Record,$sql]  = InsertOrUpdateTableByArray($TableName="data_role",$Element,"USER_ID",1);
          //print $sql."<BR>";
      }
      #################################################################################


      $sql = "select `td_edu`.`edu_planexec`.`编号` AS `id`,`td_edu`.`edu_planexec`.`级别` AS `级别`,`td_edu`.`edu_planexec`.`专业名称` AS `专业名称`,`td_edu`.`edu_planexec`.`班级名称` AS `班级名称`,`td_edu`.`edu_planexec`.`班级人数` AS `班级人数`,`td_edu`.`edu_planexec`.`开课教师` AS `教师姓名`,`td_edu`.`edu_planexec`.`开课学期` AS `学期名称`,`td_edu`.`edu_planexec`.`教师用户名` AS `教师用户名`,`td_edu`.`edu_planexec`.`课程名称` AS `课程名称`,`td_edu`.`edu_planexec`.`学分` AS `学分`,`td_edu`.`edu_planexec`.`课程类别` AS `课程类别`,`td_edu`.`edu_planexec`.`考核` AS `考核`,`td_edu`.`edu_planexec`.`总学时` AS `总学时`,`td_edu`.`edu_planexec`.`讲课学时` AS `讲课学时`,`td_edu`.`edu_planexec`.`实验学时` AS `实验学时`,`td_edu`.`edu_planexec`.`上机学时` AS `上机学时`,`td_edu`.`edu_planexec`.`起止周` AS `起止周`,`td_edu`.`edu_planexec`.`本学期学时` AS `本学期总学时`,`td_edu`.`edu_planexec`.`课程类型` AS `课程类型`,`td_edu`.`edu_planexec`.`体育学时` AS `体育学时`,`td_edu`.`edu_planexec`.`是否录入成绩` AS `是否录入成绩`,`td_edu`.`edu_planexec`.`是否课堂评价` AS `是否课堂评价`,`td_edu`.`edu_planexec`.`课程代码` AS `课程代码` from `td_edu`.`edu_planexec`";
      清空原有表并按原结构复制所有数据($数据表="data_execplan", $sql);
      exit;

      $sql = "select `td_edu`.`edu_classroom`.`教室编号` AS `id`,`td_edu`.`edu_classroom`.`教室名称` AS `房间编号`,`td_edu`.`edu_classroom`.`教室名称` AS `房间名称`,`td_edu`.`edu_classroom`.`类型名称` AS `房间类型`,`td_edu`.`edu_classroom`.`建筑物名称` AS `所属建筑`,`td_edu`.`edu_classroom`.`房间楼层` AS `房间楼层`,`td_edu`.`edu_classroom`.`座位数` AS `座位数`,`td_edu`.`edu_classroom`.`教室设备情况` AS `设备情况`,`td_edu`.`edu_classroom`.`教室用途面向专业` AS `用途`,`td_edu`.`edu_classroom`.`管理部门` AS `管理部门`,`td_edu`.`edu_classroom`.`管理员` AS `管理员`,`td_edu`.`edu_classroom`.`使用面积` AS `使用面积`,`td_edu`.`edu_classroom`.`备注` AS `备注` from `td_edu`.`edu_classroom`";
      清空原有表并按原结构复制所有数据($数据表="data_room", $sql);

      //td_edu edu_banji
      $sql = "select `edu_banji`.`编号` AS `id`,`edu_banji`.`班级代码` AS `班级代码`,`edu_banji`.`班级名称` AS `班级名称`,`edu_banji`.`所属专业` AS `所属专业`,`edu_banji`.`所属系` AS `所属系部`,`edu_banji`.`入学年份` AS `入学年份`,`edu_banji`.`所属校区` AS `所属校区`,`edu_banji`.`固定教室` AS `固定教室`,`edu_banji`.`班主任` AS `班主任用户名`,`edu_banji`.`班主任姓名` AS `班主任姓名`,`edu_banji`.`班主任联系方式` AS `班主任联系方式`,`edu_banji`.`毕业时间` AS `毕业时间`,`edu_banji`.`是否毕业标记` AS `是否毕业`,`edu_banji`.`实习班主任` AS `实习班主任`,`edu_banji`.`是否教学班` AS `班级类型`,`edu_banji`.`班级简称` AS `班级简介` from `td_edu`.`edu_banji` where (`edu_banji`.`是否毕业标记` = '0')";
      清空原有表并按原结构复制所有数据($数据表="data_banji", $sql);

      //td_edu edu_building
      $sql = "select `td_edu`.`edu_building`.`流水号` AS `id`,`td_edu`.`edu_building`.`教学楼编号` AS `建筑编号`,`td_edu`.`edu_building`.`教学楼名称` AS `建筑名称`,`td_edu`.`edu_building`.`备注` AS `建筑属性`,`td_edu`.`edu_building`.`备注` AS `其它信息`,`td_edu`.`edu_building`.`所属校区` AS `所属校区`,`td_edu`.`edu_building`.`备注` AS `备注` from `td_edu`.`edu_building`";
      清空原有表并按原结构复制所有数据($数据表="data_building", $sql);

      //td_edu edu_course
      $sql = "select `td_edu`.`edu_course`.`编号` AS `id`,`td_edu`.`edu_course`.`课程代码` AS `课程代码`,`td_edu`.`edu_course`.`课程名称` AS `课程名称`,`td_edu`.`edu_course`.`开课教研室` AS `教研室`,`td_edu`.`edu_course`.`课程类型` AS `课程类型`,`td_edu`.`edu_course`.`课程类别` AS `课程类别`,`td_edu`.`edu_course`.`学分` AS `学分`,`td_edu`.`edu_course`.`总学时` AS `总学时`,`td_edu`.`edu_course`.`实践学时` AS `实践学时`,`td_edu`.`edu_course`.`考试总分` AS `考试总分`,`td_edu`.`edu_course`.`及格分数` AS `及格分数`,`td_edu`.`edu_course`.`备注` AS `备注` from `td_edu`.`edu_course`";
      清空原有表并按原结构复制所有数据($数据表="data_course", $sql);

      $sql = "select `td_edu`.`dorm_building`.`编号` AS `id`,`td_edu`.`dorm_building`.`宿舍楼名称` AS `宿舍楼名称`,`td_edu`.`dorm_building`.`宿舍总数` AS `宿舍总数`,`td_edu`.`dorm_building`.`楼层数` AS `楼层数`,`td_edu`.`dorm_building`.`学生性别` AS `学生性别`,`td_edu`.`dorm_building`.`备注` AS `备注`,`td_edu`.`dorm_building`.`生管老师一` AS `生管老师一`,`td_edu`.`dorm_building`.`管理范围一` AS `管理范围一`,`td_edu`.`dorm_building`.`生管老师二` AS `生管老师二`,`td_edu`.`dorm_building`.`管理范围二` AS `管理范围二`,`td_edu`.`dorm_building`.`生管老师三` AS `生管老师三`,`td_edu`.`dorm_building`.`管理范围三` AS `管理范围三`,`td_edu`.`dorm_building`.`生管老师四` AS `生管老师四`,`td_edu`.`dorm_building`.`管理范围四` AS `管理范围四`,`td_edu`.`dorm_building`.`生管老师五` AS `生管老师五`,`td_edu`.`dorm_building`.`管理范围五` AS `管理范围五`,`td_edu`.`dorm_building`.`生管老师六` AS `生管老师六`,`td_edu`.`dorm_building`.`管理范围六` AS `管理范围六`,`td_edu`.`dorm_building`.`生管老师七` AS `生管老师七`,`td_edu`.`dorm_building`.`管理范围七` AS `管理范围七`,`td_edu`.`dorm_building`.`生管老师八` AS `生管老师八`,`td_edu`.`dorm_building`.`管理范围八` AS `管理范围八`,`td_edu`.`dorm_building`.`生管老师九` AS `生管老师九`,`td_edu`.`dorm_building`.`管理范围九` AS `管理范围九`,`td_edu`.`dorm_building`.`生管老师十` AS `生管老师十`,`td_edu`.`dorm_building`.`管理范围十` AS `管理范围十`,`td_edu`.`dorm_building`.`编号` AS `排序号` from `td_edu`.`dorm_building`";
      清空原有表并按原结构复制所有数据($数据表="data_dorm_building", $sql);

      $sql = "select `td_edu`.`dorm_room`.`编号` AS `id`,`td_edu`.`dorm_room`.`宿舍楼` AS `宿舍楼`,`td_edu`.`dorm_room`.`房间名称` AS `宿舍房间`,`td_edu`.`dorm_room`.`房间性质` AS `房间性质`,`td_edu`.`dorm_room`.`房间床位数` AS `床位数`,`td_edu`.`dorm_room`.`所属班级` AS `所属班级`,`td_edu`.`dorm_room`.`性别` AS `性别`,`td_edu`.`dorm_room`.`楼层数` AS `楼层数` from `td_edu`.`dorm_room`";
      清空原有表并按原结构复制所有数据($数据表="data_dorm_room", $sql);


      $sql = "select `td_edu`.`edu_student`.`编号` AS `id`,`td_edu`.`edu_student`.`学号` AS `学号`,`td_edu`.`edu_student`.`姓名` AS `姓名`,`td_edu`.`edu_student`.`班号` AS `班级`,`td_edu`.`edu_student`.`专业名称` AS `专业`,`td_edu`.`edu_student`.`院系名称` AS `系部`,`td_edu`.`edu_student`.`身份证号` AS `身份证件号`,`td_edu`.`edu_student`.`口令` AS `密码`,`td_edu`.`edu_student`.`出生日期` AS `出生日期`,`td_edu`.`edu_student`.`性别` AS `性别`,`td_edu`.`edu_student`.`座号` AS `座号`,`td_edu`.`edu_student`.`学生宿舍` AS `学生宿舍`,`td_edu`.`edu_student`.`床位号` AS `床位号`,`td_edu`.`edu_student`.`学生状态` AS `学生状态`,`td_edu`.`edu_student`.`就读方式` AS `就读方式`,`td_edu`.`edu_student`.`入学时间` AS `入学学期`,`td_edu`.`edu_student`.`学生电话` AS `学生手机`,`td_edu`.`edu_student`.`父亲姓名` AS `家长姓名`,`td_edu`.`edu_student`.`父亲电话` AS `家长电话`,`td_edu`.`edu_student`.`民族` AS `民族`,`td_edu`.`edu_student`.`毕业学校` AS `之前就读学校`,`td_edu`.`edu_student`.`入学方式` AS `入学方式`,`td_edu`.`edu_student`.`备注1` AS `所在省`,`td_edu`.`edu_student`.`备注1` AS `所在市`,`td_edu`.`edu_student`.`备注1` AS `所在区县`,`td_edu`.`edu_student`.`备注1` AS `行政区划代码`,`td_edu`.`edu_student`.`家庭住址` AS `家庭住址`,`td_edu`.`edu_student`.`家庭邮编` AS `家庭邮编`,`td_edu`.`edu_student`.`身份证件类型` AS `身份证件类型`,`td_edu`.`edu_student`.`备注1` AS `姓名拼音`,`td_edu`.`edu_student`.`备注1` AS `年龄`,`td_edu`.`edu_student`.`备注1` AS `国籍地区`,`td_edu`.`edu_student`.`备注1` AS `港澳台侨外`,`td_edu`.`edu_student`.`政治面貌` AS `政治面貌`,`td_edu`.`edu_student`.`健康状况` AS `健康状况`,`td_edu`.`edu_student`.`备注1` AS `籍贯所在省`,`td_edu`.`edu_student`.`备注1` AS `籍贯所在市`,`td_edu`.`edu_student`.`备注1` AS `籍贯所在区县`,`td_edu`.`edu_student`.`备注1` AS `籍贯行政区划代码`,`td_edu`.`edu_student`.`备注1` AS `出生地所在省`,`td_edu`.`edu_student`.`备注1` AS `出生地所在市`,`td_edu`.`edu_student`.`备注1` AS `出生地所在区县`,`td_edu`.`edu_student`.`备注1` AS `出生地行政区划代码`,`td_edu`.`edu_student`.`备注1` AS `户口所在地所在省`,`td_edu`.`edu_student`.`备注1` AS `户口所在地所在市`,`td_edu`.`edu_student`.`备注1` AS `户口所在地所在区县`,`td_edu`.`edu_student`.`备注1` AS `户口所在地行政区划代码`,`td_edu`.`edu_student`.`备注1` AS `生源所在地所在省`,`td_edu`.`edu_student`.`备注1` AS `生源所在地所在市`,`td_edu`.`edu_student`.`备注1` AS `生源所在地所在区县`,`td_edu`.`edu_student`.`备注1` AS `生源所在地行政区划代码`,`td_edu`.`edu_student`.`备注1` AS `户口详细地址`,`td_edu`.`edu_student`.`备注1` AS `所属派出所`,`td_edu`.`edu_student`.`备注1` AS `户口性质`,`td_edu`.`edu_student`.`备注1` AS `学生居住地类型`,`td_edu`.`edu_student`.`备注1` AS `家庭现地址`,`td_edu`.`edu_student`.`备注1` AS `寄信地址`,`td_edu`.`edu_student`.`备注1` AS `邮政编码`,`td_edu`.`edu_student`.`备注1` AS `联系人`,`td_edu`.`edu_student`.`备注1` AS `联系电话`,`td_edu`.`edu_student`.`备注1` AS `家庭电话`,`td_edu`.`edu_student`.`备注1` AS `学生手机号码`,`td_edu`.`edu_student`.`备注1` AS `特长`,`td_edu`.`edu_student`.`备注1` AS `毕业学校`,`td_edu`.`edu_student`.`备注1` AS `考试总分`,`td_edu`.`edu_student`.`备注1` AS `是否随迁子女`,`td_edu`.`edu_student`.`备注1` AS `是否建档立卡贫困户`,`td_edu`.`edu_student`.`备注1` AS `乘火车区间`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1姓名`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1关系`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1是否监护人`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1身份证件类型`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1身份证件号`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1年龄`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1民族`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1出生年月`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1政治面貌`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1健康状况`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1联系电话`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1工作或学习单位`,`td_edu`.`edu_student`.`备注1` AS `家庭成员1职务`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2姓名`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2关系`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2身份证件类型`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2身份证件号`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2年龄`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2民族`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2出生年月`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2是否监护人`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2政治面貌`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2健康状况`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2联系电话`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2工作或学习单位`,`td_edu`.`edu_student`.`备注1` AS `家庭成员2职务` from `td_edu`.`edu_student`";
      清空原有表并按原结构复制所有数据($数据表="data_student", $sql);

      $sql = "select `td_edu`.`edu_xi`.`编号` AS `id`,`td_edu`.`edu_xi`.`系代码` AS `系部代码`,`td_edu`.`edu_xi`.`系名称` AS `系部名称`,`td_edu`.`edu_xi`.`负责人姓名` AS `系部负责人1`,`td_edu`.`edu_xi`.`负责人姓名` AS `系部负责人2`,`td_edu`.`edu_xi`.`负责人姓名` AS `系部负责人3`,`td_edu`.`edu_xi`.`教学秘书` AS `教学秘书`,`td_edu`.`edu_xi`.`系简介` AS `系部简介`,`td_edu`.`edu_xi`.`备注` AS `备注`,`td_edu`.`edu_xi`.`学籍二级管理` AS `学籍二级管理`,`td_edu`.`edu_xi`.`备注` AS `学生请假二级管理`,`td_edu`.`edu_xi`.`奖惩补助二级管理` AS `奖惩补助二级管理`,`td_edu`.`edu_xi`.`备注` AS `教学计划二级管理`,`td_edu`.`edu_xi`.`备注` AS `量化考核二级管理`,`td_edu`.`edu_xi`.`备注` AS `岗位实习二级管理`,`td_edu`.`edu_xi`.`学生考勤二级管理` AS `学生考勤二级管理`,`td_edu`.`edu_xi`.`备注` AS `学生成绩二级管理`,`td_edu`.`edu_xi`.`备注` AS `班级事务二级管理` from `td_edu`.`edu_xi`";
      清空原有表并按原结构复制所有数据($数据表="data_xi", $sql);

      $sql = "select `td_edu`.`edu_xueqiexec`.`流水号` AS `id`,`td_edu`.`edu_xueqiexec`.`学期名称` AS `学期名称`,`td_edu`.`edu_xueqiexec`.`当前学期` AS `当前学期`,`td_edu`.`edu_xueqiexec`.`学年` AS `学年`,`td_edu`.`edu_xueqiexec`.`开始时间` AS `开始时间`,`td_edu`.`edu_xueqiexec`.`结束时间` AS `结束时间`,`td_edu`.`edu_xueqiexec`.`备注` AS `备注`,`td_edu`.`edu_xueqiexec`.`备注` AS `其它` from `td_edu`.`edu_xueqiexec`";
      清空原有表并按原结构复制所有数据($数据表="data_xueqi", $sql);

      $sql = "select `td_edu`.`edu_zhuanye`.`编号` AS `id`,`td_edu`.`edu_zhuanye`.`专业代码` AS `专业代码`,`td_edu`.`edu_zhuanye`.`专业名称` AS `专业名称`,`td_edu`.`edu_zhuanye`.`所属学院` AS `所属系部`,`td_edu`.`edu_zhuanye`.`学制` AS `学制`,`td_edu`.`edu_zhuanye`.`学位` AS `学位`,`td_edu`.`edu_zhuanye`.`备注` AS `专业简介`,`td_edu`.`edu_zhuanye`.`专业秘书` AS `专业秘书1`,`td_edu`.`edu_zhuanye`.`专业秘书二` AS `专业秘书2`,`td_edu`.`edu_zhuanye`.`今年是否招生` AS `是否招生`,`td_edu`.`edu_zhuanye`.`备注` AS `图片介绍` from `td_edu`.`edu_zhuanye`";
      清空原有表并按原结构复制所有数据($数据表="data_zhuanye", $sql);

  }
  else {
  }
  //print_R($db_remote->databaseName);
}


?>
