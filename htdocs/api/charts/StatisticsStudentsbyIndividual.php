<?php
header("Content-Type: application/json"); 
require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$optionsMenuItem = $_GET['optionsMenuItem'];
if($optionsMenuItem=="")  {
    $optionsMenuItem = "当前学期";
}

$学期 = returntablefield("data_xueqi","当前学期","是","学期名称")['学期名称'];

$sql        = "select * from data_deyu_geren_gradeone";
$rs         = $db->CacheExecute(10,$sql);
$rs_a       = $rs->GetArray();
$图标和颜色 = [];
foreach($rs_a as $Line) {
    $图标和颜色[$Line['名称']]['颜色'] = $Line['颜色'];
    $图标和颜色[$Line['名称']]['图标'] = $Line['图标'];
}

$sql        = "select 学号,姓名,班级 from data_student where 学生状态='正常状态'";
$rs         = $db->CacheExecute(10,$sql);
$rs_a       = $rs->GetArray();
$学生姓名Aarray = [];
$TopRightOptions = [];
$学号转姓名 = [];
$学号转班级 = [];
foreach($rs_a as $Line) {
    $学生学号Aarray[]    = ForSqlInjection($Line['学号']);
    $学生姓名Aarray[]    = ForSqlInjection($Line['姓名']);
    $学号转姓名[ForSqlInjection($Line['学号'])] = ForSqlInjection($Line['姓名']);
    $学号转班级[ForSqlInjection($Line['学号'])] = ForSqlInjection($Line['班级']);
    $TopRightOptions[] = ['name'=>ForSqlInjection($Line['姓名']), 'code'=>ForSqlInjection($Line['学号']), 'url'=>'/apps/177','fieldname'=>'学号'];
}
if($_GET['className']!="")   {
    $学号 = ForSqlInjection($_GET['className']);
    $姓名 = $学号转姓名[$学号];
}
elseif($学生姓名Aarray[0]!="")          {
    $学号 = $学生学号Aarray[0];
    $姓名 = $学生姓名Aarray[0];
}
else {
    $姓名 = "XXXXX";
    $学号 = "XXXXX";
}
if(sizeof($TopRightOptions)==0)         {
    $TopRightOptions[] = ['name'=>ForSqlInjection($姓名), 'url'=>'/apps/177','fieldname'=>'学号'];
}

switch($optionsMenuItem)                {
    case '最近一周':
        $whereSql = " and 积分时间 >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
        break;
    case '最近一月':
        $whereSql = " and 积分时间 >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case '当前学期':
        $whereSql = " and 学期='$学期'";
        break;
    case '所有学期':
        $whereSql = "";
        break;
}

//奖杯模块
$sql = "select SUM(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql";
$rs = $db->CacheExecute(180,$sql);
$AnalyticsTrophy['Welcome']     = "您好，".$姓名."！🥳";
$AnalyticsTrophy['SubTitle']    = $姓名." 总积分";
$AnalyticsTrophy['TotalScore']  = intval($rs->fields['NUM']);
$AnalyticsTrophy['ViewButton']['name']  = "查看明细";
$AnalyticsTrophy['ViewButton']['url']   = "/apps/177";
$AnalyticsTrophy['TopRightOptions']     = $TopRightOptions;
$AnalyticsTrophy['grid']        = 4;
$AnalyticsTrophy['type']        = "AnalyticsTrophy";
$AnalyticsTrophy['sql']         = $sql;
/*
$sql = "select SUM(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql";
$rs = $db->CacheExecute(180,$sql);
$AnalyticsTrophy['Welcome']     = "您好,".$GLOBAL_USER->USER_NAME."!🥳";
$AnalyticsTrophy['SubTitle']    = "个人总积分";
$AnalyticsTrophy['TotalScore']  = $rs->fields['NUM'];
$AnalyticsTrophy['ViewButton']['name']  = "查看明细";
$AnalyticsTrophy['ViewButton']['url']   = "/apps/177";
$AnalyticsTrophy['grid']        = 4;
$AnalyticsTrophy['type']        = "AnalyticsTrophy";
*/

//按一级指标统计积分
$sql = "select 一级指标 AS title, SUM(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql group by 一级指标 order by 一级指标 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$Item = [];
$data = [];
$Index = 0;
foreach($rs_a as $Element)   {
    $data[] = ['title'=>$Element['title'],'stats'=>$Element['NUM'],'color'=>$图标和颜色[$Element['title']]['颜色'],'icon'=>"mdi:".$图标和颜色[$Element['title']]['图标']];
    $Index ++;
}
$AnalyticsTransactionsCard['Title']       = "德育量化";
$AnalyticsTransactionsCard['SubTitle']    = "按一级指标统计";
$AnalyticsTransactionsCard['data']        = $data;
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$AnalyticsTransactionsCard['grid']                 = 8;
$AnalyticsTransactionsCard['type']                 = "AnalyticsTransactionsCard";


//得到最新加分或是扣分的几条记录
$sql = "select 一级指标,二级指标,积分项目,积分分值 from data_deyu_geren_record where 学号='$学号' $whereSql and 积分分值>0 order by id desc limit 5";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
for($i=0;$i<sizeof($rs_a);$i++) {
    $rs_a[$i]['项目图标'] = "mdi:".$图标和颜色[$rs_a[$i]['一级指标']]['图标'];
    $rs_a[$i]['图标颜色'] = $图标和颜色[$rs_a[$i]['一级指标']]['颜色'];
}
$AnalyticsDepositWithdraw['加分']['Title']             = "加分";
$AnalyticsDepositWithdraw['加分']['TopRightButton']    = ['name'=>'查看所有','url'=>'/apps/177'];
$AnalyticsDepositWithdraw['加分']['data']              = $rs_a;

$sql = "select 一级指标,二级指标,积分项目,积分分值 from data_deyu_geren_record where 学号='$学号' $whereSql and 积分分值<0 order by id desc limit 5";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
for($i=0;$i<sizeof($rs_a);$i++) {
    $rs_a[$i]['项目图标'] = "mdi:".$图标和颜色[$rs_a[$i]['一级指标']]['图标'];
    $rs_a[$i]['图标颜色'] = $图标和颜色[$rs_a[$i]['一级指标']]['颜色'];
}
$AnalyticsDepositWithdraw['扣分']['Title']              = "扣分";
$AnalyticsDepositWithdraw['扣分']['TopRightButton']     = ['name'=>'查看所有','url'=>'/apps/177'];
$AnalyticsDepositWithdraw['扣分']['data']               = $rs_a;
$AnalyticsDepositWithdraw['grid']                       = 8;
$AnalyticsDepositWithdraw['type']                       = "AnalyticsDepositWithdraw";


//本班积分排行 
$colorArray = ['primary','success','warning','info','info'];
$iconArray  = ['mdi:trending-up','mdi:account-outline','mdi:cellphone-link','mdi:currency-usd','mdi:currency-usd','mdi:currency-usd'];
$sql    = "select 学号, 姓名, SUM(积分分值) AS 积分分值 from data_deyu_geren_record where 班级='".$学号转班级[$学号]."' $whereSql group by 学号 order by 积分分值 desc limit 5";
$rs     = $db->CacheExecute(180,$sql);
$rs_a   = $rs->GetArray();
$Item   = [];
$Index  = 0;
for($i=0;$i<sizeof($rs_a);$i++) {
    $rs_a[$i]['图标颜色'] = $colorArray[$i];
    $rs_a[$i]['头像']       = '/images/avatars/'.(($rs_a[$i]['学号']%8)+1).'.png';
}
$AnalyticsSalesByCountries['Title']       = "本班排行";
$AnalyticsSalesByCountries['SubTitle']    = "本班积分最高学生";
$AnalyticsSalesByCountries['data']        = $rs_a;
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$AnalyticsSalesByCountries['grid']                 = 4;
$AnalyticsSalesByCountries['type']                 = "AnalyticsSalesByCountries";

/*
//ApexAreaChart
$sql = "select 一级指标,积分时间,sum(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql group by 一级指标,积分时间 order by 积分时间 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
$一级指标Array = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['积分时间']][$rs_a[$i]['一级指标']] = $rs_a[$i]['NUM'];
    $一级指标Array[$rs_a[$i]['一级指标']] = $rs_a[$i]['一级指标'];
}
$dataY = [];
$dataX = array_keys($输出数据);
$一级指标Array = array_keys($一级指标Array);
foreach($一级指标Array as $一级指标)  {
    $ItemY = [];
    $ItemYDate = [];
    foreach($dataX as $Date) {
        $ItemYDate[] = intval($输出数据[$Date][$一级指标]);
    }
    $dataY[] = ["name"=>$一级指标,"data"=>$ItemYDate];
}

$ApexAreaChart['Title']       = "学生积分之和";
$ApexAreaChart['SubTitle']    = "按天统计学生积分之和";
$ApexAreaChart['dataX']       = $dataX;
$ApexAreaChart['dataY']       = $dataY;
$ApexAreaChart['sql']       = $sql;
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
*/

//ApexAreaChart
$sql = "select 积分时间,sum(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql and 积分分值>0 group by 积分时间 order by 积分时间 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['积分时间']] = $rs_a[$i]['NUM'];
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"班级总积分","data"=>array_values($输出数据)];

$ApexAreaChart['Title']       = "学生积分之和";
$ApexAreaChart['SubTitle']    = "按天统计学生积分之和";
$ApexAreaChart['dataX']       = $dataX;
$ApexAreaChart['dataY']       = $dataY;
$ApexAreaChart['sql']       = $sql;
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexAreaChart['grid']                  = 8;
$ApexAreaChart['type']                  = "ApexAreaChart";

$ApexLineChart['Title']         = "学生积分之和";
$ApexLineChart['SubTitle']      = "按天统计学生积分之和";
$ApexLineChart['dataX']         = $dataX;
$ApexLineChart['dataY']         = $dataY;
$ApexLineChart['sql']           = $sql;
$ApexLineChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexLineChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexLineChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexLineChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexLineChart['grid']                  = 8;
$ApexLineChart['type']                  = "ApexLineChart";


//AnalyticsPerformance
$sql = "select 一级指标,sum(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql group by 一级指标 order by 一级指标 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['一级指标']] = $rs_a[$i]['NUM'];
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"学生总积分","data"=>array_values($输出数据)];

$AnalyticsPerformance['Title']       = "按一级指标统计积分之和";
$AnalyticsPerformance['SubTitle']    = "按一级指标统计学生积分之和";
$AnalyticsPerformance['dataX']       = $dataX;
$AnalyticsPerformance['dataY']       = $dataY;
$AnalyticsPerformance['sql']         = $sql;
$AnalyticsPerformance['colors']      = ['#fdd835','#32baff','#00d4bd','#7367f0','#FFA1A1'];
$AnalyticsPerformance['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$AnalyticsPerformance['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$AnalyticsPerformance['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$AnalyticsPerformance['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$AnalyticsPerformance['grid']                 = 4;
$AnalyticsPerformance['type']                 = "AnalyticsPerformance";


//AnalyticsWeeklyOverview
$sql = "select 积分时间,sum(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql group by 积分时间 order by 积分时间 desc limit 7";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['积分时间']] = $rs_a[$i]['NUM'];
}
ksort($输出数据);
$dataY = [];
$dataX = array_keys($输出数据);
$dataYItem = array_values($输出数据);
$dataY[] = ["name"=>"积分合计","data"=>$dataYItem];

$AnalyticsWeeklyOverview0['Title']         = "学生积分合计周报";
$AnalyticsWeeklyOverview0['SubTitle']      = "最近一周学生积分合计";
$AnalyticsWeeklyOverview0['dataX']         = $dataX;
$AnalyticsWeeklyOverview0['dataY']         = $dataY;
$AnalyticsWeeklyOverview0['sql']           = $sql;
$AnalyticsWeeklyOverview0['TopRightOptions'][]       = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];

$AnalyticsWeeklyOverview0['BottomText']['Left']      = array_sum($dataYItem);
$AnalyticsWeeklyOverview0['BottomText']['Right']     = "最近一周总积分为".array_sum($dataYItem)."";

$AnalyticsWeeklyOverview0['ViewButton']['name']  = "明细";
$AnalyticsWeeklyOverview0['ViewButton']['url']   = "/apps/177";
$AnalyticsWeeklyOverview0['grid']                = 4;
$AnalyticsWeeklyOverview0['type']                = "AnalyticsWeeklyOverview";

//AnalyticsWeeklyOverview
$sql = "select 积分时间,sum(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql and 积分分值>0 group by 积分时间 order by 积分时间 desc limit 7";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['积分时间']] = $rs_a[$i]['NUM'];
}
ksort($输出数据);
$dataY = [];
$dataX = array_keys($输出数据);
$dataYItem = array_values($输出数据);
$dataY[] = ["name"=>"加分之和","data"=>$dataYItem];

$AnalyticsWeeklyOverview1['Title']         = "学生加分周报";
$AnalyticsWeeklyOverview1['SubTitle']      = "最近一周学生加分之和";
$AnalyticsWeeklyOverview1['dataX']         = $dataX;
$AnalyticsWeeklyOverview1['dataY']         = $dataY;
$AnalyticsWeeklyOverview1['sql']           = $sql;
$AnalyticsWeeklyOverview1['TopRightOptions'][]       = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];

$AnalyticsWeeklyOverview1['BottomText']['Left']      = array_sum($dataYItem);
$AnalyticsWeeklyOverview1['BottomText']['Right']     = "最近一周学生加分之和".array_sum($dataYItem)."";

$AnalyticsWeeklyOverview1['ViewButton']['name']  = "明细";
$AnalyticsWeeklyOverview1['ViewButton']['url']   = "/apps/177";
$AnalyticsWeeklyOverview1['grid']                = 4;
$AnalyticsWeeklyOverview1['type']                = "AnalyticsWeeklyOverview";

//AnalyticsWeeklyOverview
$sql = "select 积分时间,sum(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql and 积分分值<0 group by 积分时间 order by 积分时间 desc limit 7";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['积分时间']] = $rs_a[$i]['NUM'];
}
ksort($输出数据);
$dataY = [];
$dataX = array_keys($输出数据);
$dataYItem = array_values($输出数据);
$dataY[] = ["name"=>"扣分之和","data"=>$dataYItem];

$AnalyticsWeeklyOverview2['Title']         = "学生扣分周报";
$AnalyticsWeeklyOverview2['SubTitle']      = "最近一周学生扣分之和";
$AnalyticsWeeklyOverview2['dataX']         = $dataX;
$AnalyticsWeeklyOverview2['dataY']         = $dataY;
$AnalyticsWeeklyOverview2['sql']           = $sql;
$AnalyticsWeeklyOverview2['TopRightOptions'][]       = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];

$AnalyticsWeeklyOverview2['BottomText']['Left']      = array_sum($dataYItem);
$AnalyticsWeeklyOverview2['BottomText']['Right']     = "最近一周学生扣分之和".array_sum($dataYItem)."";

$AnalyticsWeeklyOverview2['ViewButton']['name']  = "明细";
$AnalyticsWeeklyOverview2['ViewButton']['url']   = "/apps/177";
$AnalyticsWeeklyOverview2['grid']                = 4;
$AnalyticsWeeklyOverview2['type']                = "AnalyticsWeeklyOverview";


//ApexDonutChart
$sql = "select 一级指标,sum(积分分值) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql and 积分分值>0 group by 一级指标 order by 一级指标 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['一级指标']] = intval($rs_a[$i]['NUM']);
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"学生总积分百分比","data"=>array_values($输出数据)];

$ApexDonutChart['Title']       = "按一级指标统计百分比";
$ApexDonutChart['SubTitle']    = "按一级指标统计加分之和的百分比";
$ApexDonutChart['dataX']       = $dataX;
$ApexDonutChart['dataY']       = $dataY;
$ApexDonutChart['sql']         = $sql;
$ApexDonutChart['colors']      = ['#fdd835','#32baff','#00d4bd','#7367f0','#FFA1A1'];
$ApexDonutChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexDonutChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexDonutChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexDonutChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexDonutChart['grid']                 = 4;
$ApexDonutChart['type']                 = "ApexDonutChart";


//ApexRadialBarChart
$sql = "select 一级指标,abs(sum(积分分值)) AS NUM from data_deyu_geren_record where 学号='$学号' $whereSql and 积分分值<0 group by 一级指标 order by 一级指标 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['一级指标']] = intval($rs_a[$i]['NUM']);
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"学生总积分百分比","data"=>array_values($输出数据)];

$ApexRadialBarChart['Title']       = "按一级指标统计百分比";
$ApexRadialBarChart['SubTitle']    = "按一级指标统计扣分之和的百分比";
$ApexRadialBarChart['dataX']       = $dataX;
$ApexRadialBarChart['dataY']       = $dataY;
$ApexRadialBarChart['sql']         = $sql;
$ApexRadialBarChart['colors']      = ['#fdd835','#32baff','#00d4bd','#7367f0','#FFA1A1'];
$ApexRadialBarChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexRadialBarChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexRadialBarChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexRadialBarChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexRadialBarChart['grid']                 = 4;
$ApexRadialBarChart['type']                 = "ApexRadialBarChart";


$RS                             = [];
$RS['defaultValue']             = $学号;
$RS['optionsMenuItem']          = $optionsMenuItem;

$RS['charts'][]       = $AnalyticsTrophy;
$RS['charts'][]       = $AnalyticsTransactionsCard;
$RS['charts'][]       = $AnalyticsPerformance;
$RS['charts'][]       = $AnalyticsDepositWithdraw;
$RS['charts'][]       = $ApexDonutChart;
$RS['charts'][]       = $ApexLineChart;
$RS['charts'][]       = $AnalyticsWeeklyOverview0;
$RS['charts'][]       = $AnalyticsWeeklyOverview1;
$RS['charts'][]       = $AnalyticsWeeklyOverview2;
//$RS['charts'][]       = $AnalyticsSalesByCountries;
//$RS['charts'][]       = $ApexAreaChart;
//$RS['charts'][]       = $ApexRadialBarChart;


print_R(json_encode($RS));



?>