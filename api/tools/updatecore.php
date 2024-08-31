<?php
/*
* 基础架构: 单点低代码开发平台
* 版权所有: 郑州单点科技软件有限公司
* Email: reactchivesgmail.com
* Copyright (c) 2023
* License: GPL V3 or Commercial license
*/
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

set_time_limit(120000);

$action 	= $_GET['action'];

$目标文件名 	= "update_core.zip";

if($action=="upload"&&$_FILES['td_form_upload']['name']!="")			{
	$文件名 = explode('.',$_FILES['td_form_upload']['name']);
	$文件名 = $文件名[0];
	$文件名中包含的时间 = date("Y-m-d-H",$文件名);
	//if($文件名中包含的时间!=date("Y-m-d-H"))					{
		//print "文件上传非法!";exit;
	//}
	if(is_file($_FILES['td_form_upload']['tmp_name']))				{
		copy($_FILES['td_form_upload']['tmp_name'],$目标文件名);
		ob_start();
		print_R($_GET);
		print_R($_POST);
		print_R($_FILES);
		print $目标文件名;
		$string 	= ob_get_contents();
		ob_end_clean();
		file_put_contents("update_core.html", $string);
		print "2 上传文件成功 $目标文件名";
	}
}

if($action=="extractFile")			{
		if(is_file($目标文件名))		{
			print "3 解压文件并校验成功";
			ExactFile($目标文件名);
			unlink($目标文件名);
		}
		else	{
			print "3 目标文件名:$目标文件名 不存在";
		}
}

if($action=="uploadAndExtractFile"&&$_FILES['td_form_upload']['name']!="")			{
	$目标文件名 	= "update_core_".date("Y_m_d_H_i_s").".zip";
	$文件名 		= explode('.',$_FILES['td_form_upload']['name']);
	$文件名 		= $文件名[0];
	if(is_file($_FILES['td_form_upload']['tmp_name']))				{
		copy($_FILES['td_form_upload']['tmp_name'],$目标文件名);
		ob_start();
		print_R($_GET);
		print_R($_POST);
		print_R($_FILES);
		print $目标文件名;
		$string 	= ob_get_contents();
		ob_end_clean();
		
		ExactFile($目标文件名);
		unlink($目标文件名);
		
		file_put_contents("update_core.html", $string);
		print "2 上传成功 $目标文件名";
	}
}

if($action=="extractFile")			{
		if(is_file($目标文件名))		{
			print "3 解压文件并校验成功";
			ExactFile($目标文件名);
			unlink($目标文件名);
		}
		else	{
			print "3 目标文件名:$目标文件名 不存在";
		}
}

function ExactFile($fileName)        				{
        global $dir,$key;
		$dir        = '.';                  //文件地址所在
		$TargetExtractDir = '../../';            //解压文件目录
        echo "<font color=green size=1>以下为文件的复制进度，编号代表文件复制的个数(第一步)：</font><BR>";
        $zipfile	= $dir."/".$fileName;
        if(is_file($zipfile))  {
            // 创建一个ZipArchive对象
            $zip = new ZipArchive();
            // 打开要解压缩的ZIP文件
            if ($zip->open($zipfile) === true) {
                // 解压缩文件到指定目标文件夹
                $zip->extractTo($TargetExtractDir);
                // 关闭ZipArchive对象
                $zip->close();
                echo '解压缩成功！';
            } else {
                echo '无法打开ZIP文件或解压缩失败。';
            }
        }
}

?>