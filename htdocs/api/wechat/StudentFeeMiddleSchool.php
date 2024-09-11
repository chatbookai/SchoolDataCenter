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
require_once('StudentFeeMiddleSchool.lib.php');


$StudentFeeMiddleSchool = new StudentFeeMiddleSchool();
$学生信息 = $StudentFeeMiddleSchool->学生信息('411324198307194251');
//print_R($学生信息);

$学生缴费标准 = $StudentFeeMiddleSchool->学生缴费标准($学生信息);
//print_R($学生缴费标准);

$学生应缴费 = $StudentFeeMiddleSchool->学生应缴费($学生信息);
//print_R($学生应缴费);

$学生学期已缴费信息 = $StudentFeeMiddleSchool->学生学期已缴费信息("2023-2024-第一学期", $学生信息);
//print_R($学生学期已缴费信息);

$生成新的缴费单号 = $StudentFeeMiddleSchool->生成新的缴费单号();
//print_R($生成新的缴费单号);

//$微信小程序学生应缴费接口输出 = $StudentFeeMiddleSchool->微信小程序学生应缴费接口输出('411324198307194251');
//print_R($微信小程序学生应缴费接口输出);

$_POST['xueqi']     = "2023-2024-第一学期";
$_POST['stucode']   = '411324198307194251';
$_POST['trade_no']  = $生成新的缴费单号;

$_POST['input_2023-2024-第一学期_学费'] = "2.6";

$学生缴费保存到数据库 = $StudentFeeMiddleSchool->学生缴费保存到数据库($缴费状态='缴费成功',$数据来源='手工缴费');
print_R($学生缴费保存到数据库);


?>