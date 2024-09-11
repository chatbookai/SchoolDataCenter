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

$USER_ID    = ForSqlInjection($GLOBAL_USER->USER_ID);

$sql        = "select * from data_deyu_banji_gradeone";
$rs         = $db->CacheExecute(10,$sql);
$rs_a       = $rs->GetArray();
$图标和颜色 = [];
foreach($rs_a as $Line) {
    $图标和颜色[$Line['名称']]['颜色'] = $Line['颜色'];
    $图标和颜色[$Line['名称']]['图标'] = $Line['图标'];
}

$sql        = "select 班级名称 from data_banji where 是否毕业='否' and (find_in_set('$USER_ID',实习班主任) or (班主任用户名='$USER_ID'))";
$sql        = "select 班级名称 from data_banji where 是否毕业='否'";
$rs         = $db->CacheExecute(10,$sql);
$rs_a       = $rs->GetArray();
$班级名称Array = [];
$TopRightOptions = [];
foreach($rs_a as $Line) {
    $班级名称Array[]    = ForSqlInjection($Line['班级名称']);
    $TopRightOptions[] = ['name'=>ForSqlInjection($Line['班级名称']),'code'=>ForSqlInjection($Line['班级名称']), 'code'=>ForSqlInjection($Line['班级名称']), 'url'=>'/tab/apps_180','fieldname'=>'班级'];
}
if($_GET['className']!="")   {
    $班级 = ForSqlInjection($_GET['className']);
}
elseif($班级名称Array[0]!="") {
    $班级 = $班级名称Array[0];
}
else {
    $班级 = "计算机三班";
}
if(sizeof($TopRightOptions)==0)  {
    $TopRightOptions[] = ['name'=>ForSqlInjection($班级), 'code'=>ForSqlInjection($班级),'code'=>ForSqlInjection($班级), 'url'=>'/tab/apps_180','fieldname'=>'班级'];
}

switch($optionsMenuItem) {
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
$sql = "select SUM(积分分值) AS NUM from data_deyu_banji_record where 班级='$班级' $whereSql";
$rs = $db->CacheExecute(180,$sql);
$AnalyticsTrophy['Welcome']     = "您好，".$班级."！🥳";
$AnalyticsTrophy['SubTitle']    = $班级."总积分";
$AnalyticsTrophy['TotalScore']  = $rs->fields['NUM'];
$AnalyticsTrophy['ViewButton']['name']  = "查看明细";
$AnalyticsTrophy['ViewButton']['url']   = "/tab/apps_180";
$AnalyticsTrophy['TopRightOptions']     = $TopRightOptions;
$AnalyticsTrophy['grid']        = 4;
$AnalyticsTrophy['type']        = "AnalyticsTrophy";

//按一级指标统计积分
$sql = "select 一级指标 AS title, SUM(积分分值) AS NUM from data_deyu_banji_record where 班级='$班级' $whereSql group by 一级指标 order by 一级指标 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$Item = [];
$data = [];
$Index = 0;
foreach($rs_a as $Element)   {
    $data[] = ['title'=>$Element['title'],'stats'=>$Element['NUM'],'color'=>$图标和颜色[$Element['title']]['颜色'],'icon'=>"mdi:".$图标和颜色[$Element['title']]['图标']];
    $Index ++;
}
$AnalyticsTransactionsCard['Title']       = "班级考核";
$AnalyticsTransactionsCard['SubTitle']    = "按一级指标统计";
$AnalyticsTransactionsCard['data']        = $data;
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$AnalyticsTransactionsCard['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$AnalyticsTransactionsCard['grid']                 = 8;
$AnalyticsTransactionsCard['type']                 = "AnalyticsTransactionsCard";


//得到最新加分或是扣分的几条记录
$sql = "select 一级指标,二级指标,积分项目,积分分值 from data_deyu_banji_record where 班级='$班级' $whereSql and 积分分值>0 order by id desc limit 5";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
for($i=0;$i<sizeof($rs_a);$i++) {
    $rs_a[$i]['项目图标'] = "mdi:".$图标和颜色[$rs_a[$i]['一级指标']]['图标'];
    $rs_a[$i]['图标颜色'] = $图标和颜色[$rs_a[$i]['一级指标']]['颜色'];
}
$AnalyticsDepositWithdraw['加分']['Title']             = "加分";
$AnalyticsDepositWithdraw['加分']['TopRightButton']    = ['name'=>'查看所有','url'=>'/tab/apps_180'];
$AnalyticsDepositWithdraw['加分']['data']              = $rs_a;

$sql = "select 一级指标,二级指标,积分项目,积分分值 from data_deyu_banji_record where 班级='$班级' $whereSql and 积分分值<0 order by id desc limit 5";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
for($i=0;$i<sizeof($rs_a);$i++) {
    $rs_a[$i]['项目图标'] = "mdi:".$图标和颜色[$rs_a[$i]['一级指标']]['图标'];
    $rs_a[$i]['图标颜色'] = $图标和颜色[$rs_a[$i]['一级指标']]['颜色'];
}
$AnalyticsDepositWithdraw['扣分']['Title']              = "扣分";
$AnalyticsDepositWithdraw['扣分']['TopRightButton']     = ['name'=>'查看所有','url'=>'/tab/apps_180'];
$AnalyticsDepositWithdraw['扣分']['data']               = $rs_a;
$AnalyticsDepositWithdraw['grid']                       = 8;
$AnalyticsDepositWithdraw['type']                       = "AnalyticsDepositWithdraw";


//本班积分排行 
$colorArray = ['primary','success','warning','info','info'];
$iconArray  = ['mdi:trending-up','mdi:account-outline','mdi:cellphone-link','mdi:currency-usd','mdi:currency-usd','mdi:currency-usd'];
$sql    = "select 班级 AS 姓名, SUM(积分分值) AS 积分分值 from data_deyu_banji_record where 1=1 $whereSql group by 班级 order by 积分分值 desc limit 5";
$rs     = $db->CacheExecute(180,$sql);
$rs_a   = $rs->GetArray();
$Item   = [];
$Index  = 0;
for($i=0;$i<sizeof($rs_a);$i++) {
    $rs_a[$i]['图标颜色']   = $colorArray[$i];
    $rs_a[$i]['学号']       = returntablefield("data_banji","班级名称",$rs_a[$i]['姓名'],"所属系部")['所属系部'];
    $rs_a[$i]['头像']       = '/images/avatars/'.(($i%8)+1).'.png';
}
$AnalyticsSalesByCountries['Title']       = "全校班级排行";
$AnalyticsSalesByCountries['SubTitle']    = "按班级总积分进行排行";
$AnalyticsSalesByCountries['data']        = $rs_a;
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$AnalyticsSalesByCountries['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$AnalyticsSalesByCountries['grid']                 = 4;
$AnalyticsSalesByCountries['type']                 = "AnalyticsSalesByCountries";

/*
//ApexAreaChart
$sql = "select 一级指标,积分时间,sum(积分分值) AS NUM from data_deyu_banji_record where 班级='$班级' $whereSql group by 一级指标,积分时间 order by 积分时间 asc";
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

$ApexAreaChart['Title']       = "班级考核积分之和";
$ApexAreaChart['SubTitle']    = "按天统计班级考核积分之和";
$ApexAreaChart['dataX']       = $dataX;
$ApexAreaChart['dataY']       = $dataY;
$ApexAreaChart['sql']       = $sql;
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
*/

//ApexAreaChart
$sql = "select 积分时间,sum(积分分值) AS NUM from data_deyu_banji_record where 班级='$班级' $whereSql and 积分分值>0 group by 积分时间 order by 积分时间 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['积分时间']] = $rs_a[$i]['NUM'];
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"班级总积分","data"=>array_values($输出数据)];

$ApexAreaChart['Title']       = "班级考核积分之和";
$ApexAreaChart['SubTitle']    = "按天统计班级考核积分之和";
$ApexAreaChart['dataX']       = $dataX;
$ApexAreaChart['dataY']       = $dataY;
$ApexAreaChart['sql']       = $sql;
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexAreaChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexAreaChart['grid']                  = 8;
$ApexAreaChart['type']                  = "ApexAreaChart";

$ApexLineChart['Title']         = "班级考核积分之和";
$ApexLineChart['SubTitle']      = "按天统计班级考核积分之和";
$ApexLineChart['dataX']         = $dataX;
$ApexLineChart['dataY']         = $dataY;
$ApexLineChart['sql']           = $sql;
$ApexLineChart['TopRightOptions'][]    = ['name'=>'最近一周','selected'=>$optionsMenuItem=='最近一周'?true:false];
$ApexLineChart['TopRightOptions'][]    = ['name'=>'最近一月','selected'=>$optionsMenuItem=='最近一月'?true:false];
$ApexLineChart['TopRightOptions'][]    = ['name'=>'当前学期','selected'=>$optionsMenuItem=='当前学期'?true:false];
$ApexLineChart['TopRightOptions'][]    = ['name'=>'所有学期','selected'=>$optionsMenuItem=='所有学期'?true:false];
$ApexLineChart['grid']                  = 8;
$ApexLineChart['type']                  = "ApexLineChart";

//输出GoView结构
$ApexLineChart['GoView']['dimensions']      = ["积分时间",$ApexLineChart['Title']];
$GoViewSource = [];
foreach($输出数据 as $输出数据X=>$输出数据Y)  {
    $GoViewSource[] = [$ApexLineChart['Title']=>$输出数据Y,'积分时间'=>$输出数据X];
}
$ApexLineChart['GoView']['source']    = $GoViewSource;

//额外一个班级的统计数据 -- 开始
$额外一个班级的统计数据 = $班级名称Array[1];
$sql = "select 积分时间,sum(积分分值) AS NUM from data_deyu_banji_record where 班级='$额外一个班级的统计数据' $whereSql and 积分分值>0 group by 积分时间 order by 积分时间 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据T = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据T[$rs_a[$i]['积分时间']] = $rs_a[$i]['NUM'];
}
$dataY = [];
$dataX = array_keys($输出数据T);
$dataY[] = ["name"=>"班级总积分","data"=>array_values($输出数据T)];
//输出GoView结构
$ApexLineChart['GoView2']['dimensions']      = ["积分时间",$班级,$额外一个班级的统计数据];
$GoViewSource = [];
foreach($输出数据T as $输出数据X=>$输出数据Y)  {
    $GoViewSource[] = [$班级=>$输出数据Y, '积分时间'=>$输出数据X, $额外一个班级的统计数据=>rand(1,20)];
}
$ApexLineChart['GoView2']['source']    = $GoViewSource;
//额外一个班级的统计数据 -- 结束

//AnalyticsPerformance
$sql = "select 一级指标,sum(积分分值) AS NUM from data_deyu_banji_record where 班级='$班级' $whereSql group by 一级指标 order by 一级指标 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['一级指标']] = $rs_a[$i]['NUM'];
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"班级总积分","data"=>array_values($输出数据)];

$AnalyticsPerformance['Title']       = "按一级指标统计积分之和";
$AnalyticsPerformance['SubTitle']    = "按一级指标统计班级考核积分之和";
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


//ApexDonutChart
$sql = "select 一级指标,sum(积分分值) AS NUM from data_deyu_banji_record where 班级='$班级' $whereSql group by 一级指标 order by 一级指标 asc";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['一级指标']] = intval($rs_a[$i]['NUM']);
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"班级总积分百分比","data"=>array_values($输出数据)];

$ApexDonutChart['Title']       = "按一级指标统计百分比";
$ApexDonutChart['SubTitle']    = "按一级指标统计积分之和的百分比";
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
$sql = "select 一级指标,sum(积分分值) AS NUM from data_deyu_banji_record where 班级='$班级' $whereSql and 积分分值>0 group by 一级指标 order by 一级指标 asc limit 5";
$rs = $db->CacheExecute(180,$sql);
$rs_a = $rs->GetArray();
$输出数据 = [];
for($i=0;$i<sizeof($rs_a);$i++) {
    $输出数据[$rs_a[$i]['一级指标']] = intval($rs_a[$i]['NUM']);
}
$dataY = [];
$dataX = array_keys($输出数据);
$dataY[] = ["name"=>"班级总积分百分比","data"=>array_values($输出数据)];

$ApexRadialBarChart['Title']       = "按一级指标统计百分比";
$ApexRadialBarChart['SubTitle']    = "按一级指标统计加分之和的百分比";
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
$RS['defaultValue']             = $班级;
$RS['optionsMenuItem']          = $optionsMenuItem;

$RS['charts'][]       = $AnalyticsTrophy;
$RS['charts'][]       = $AnalyticsTransactionsCard;
$RS['charts'][]       = $AnalyticsPerformance;
$RS['charts'][]       = $AnalyticsDepositWithdraw;
//$RS['charts'][]       = $AnalyticsWeeklyOverview;
//$RS['charts'][]       = $ApexAreaChart;
//$RS['charts'][]       = $ApexDonutChart;
$RS['charts'][]       = $AnalyticsSalesByCountries;
$RS['charts'][]       = $ApexLineChart;
//$RS['charts'][]       = $ApexRadialBarChart;


print_R(json_encode($RS));



?>