<?php
/*
* 基础架构: 单点低代码开发平台
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2023
* License: GPL V3 or Commercial license
*/
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

exit;

生成学生积分测试数据();
function 生成学生积分测试数据() {
    global $db;

    $学期       = returntablefield("data_xueqi","当前学期","是","学期名称")['学期名称'];

    $sql        = "select * from data_student";
    $rs         = $db->CacheExecute(10,$sql);
    $学生       = $rs->GetArray();

    $sql        = "select * from data_deyu_geren_gradethree";
    $rs         = $db->CacheExecute(10,$sql);
    $积分项目    = $rs->GetArray();

    for($i=0;$i<90;$i++)  {
        $某个学生 = $学生[rand(0,sizeof($学生))];
        $某个项目 = $积分项目[rand(0,sizeof($积分项目))];
        $Element = [];
        $Element['学期']        = $学期;
        $Element['学号']        = $某个学生['学号'];
        $Element['姓名']        = $某个学生['姓名'];
        $Element['班级']        = $某个学生['班级'];

        $Element['一级指标']    = $某个项目['一级指标'];
        $Element['二级指标']    = $某个项目['二级指标'];
        $Element['积分项目']    = $某个项目['积分项目'];
        $Element['积分编码']    = $某个项目['积分编码'];
        $Element['积分分值']    = $某个项目['积分分值'];
        $Element['积分原因']    = $某个项目['积分原因'];
        $Element['积分时间']    = date('Y-m-d', strtotime(rand(-30,1).' day'));
        $Element['创建人']      = "admin";
        $Element['创建时间']    = date("Y-m-d H:i:s");
        $Element['数据录入']    = "手动输入";
        if($Element['学号']!=""&&$Element['一级指标']!="")  {
            [$Record,$sql] = InsertOrUpdateTableByArray("data_deyu_geren_record", $Element, '积分编码,积分时间,学号', 0);
            print $sql."<BR>";
        }
    }

}

生成班级评价测试数据();
function 生成班级评价测试数据() {
    global $db;

    $学期       = returntablefield("data_xueqi","当前学期","是","学期名称")['学期名称'];

    $sql        = "select * from data_banji";
    $rs         = $db->CacheExecute(10,$sql);
    $班级       = $rs->GetArray();

    $sql        = "select * from data_deyu_banji_gradethree";
    $rs         = $db->CacheExecute(10,$sql);
    $积分项目    = $rs->GetArray();

    for($i=0;$i<60;$i++)  {
        $某个班级 = $班级[rand(0,sizeof($班级))];
        $某个项目 = $积分项目[rand(0,sizeof($积分项目))];
        $Element = [];
        $Element['学期']        = $学期;
        $Element['班级']        = $某个班级['班级名称'];

        $Element['一级指标']    = $某个项目['一级指标'];
        $Element['二级指标']    = $某个项目['二级指标'];
        $Element['积分项目']    = $某个项目['积分项目'];
        $Element['积分编码']    = $某个项目['积分编码'];
        $Element['积分分值']    = $某个项目['积分分值'];
        $Element['积分原因']    = $某个项目['积分原因'];
        $Element['积分时间']    = date('Y-m-d', strtotime(rand(-30,5).' day'));
        $Element['创建人']      = "admin";
        $Element['创建时间']    = date("Y-m-d H:i:s");
        $Element['数据录入']    = "手动输入";
        if($Element['班级']!=""&&$Element['一级指标']!="")  {
            [$Record,$sql] = InsertOrUpdateTableByArray("data_deyu_banji_record", $Element, '积分项目,积分编码,积分时间,班级', 0);
            print $sql."<BR>";
        }
    }

}

?>