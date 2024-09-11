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
ini_set('max_execution_time', 7200);
	

global $SourceDir,$TargetDir,$JumpFile;

$WEB_ROOT 	= $_SERVER['DOCUMENT_ROOT']."";
$API_ROOT 	= $_SERVER['DOCUMENT_ROOT']."../api";
$SourceDir		= $API_ROOT;
$TargetDir		= $API_ROOT;

核心代码($_GET['id']);

function 核心代码($id) { 
    global $得到数据表名称,$得到数据表结构,$SYSTEM_ADD_SQL,$只读属性; global $当前流程的参数设置,$当前表单的参数设置; global $当前流程的RS,$当前表单的RS; global $当前表单所有字段信息,$英文标识,$当前表单中文名称; 
    session_commit();
    $LOGIN_USER_EDUID = $_SESSION['LOGIN_USER_EDUID'];
    if(strpos($LOGIN_USER_EDUID,"admin")===false)		{
        page_css("本页面仅限管理员使用");
        print("本页面仅限管理员使用.");
        exit;
    }
    $id 		= intval($id);
    $单行记录 	= returntablefield("data_updatesystem","id",$id,"学校名称,API地址,用户名,密码");
    $学校名称 	= $单行记录['学校名称'];
    $API地址    = $单行记录['API地址'];
    $登录地址	= $单行记录['登录地址'];
    $用户名 	= $单行记录['用户名'];
    $密码 		= $单行记录['密码'];
    $密码加密 	= $单行记录['密码加密'];
    if($_GET['action']=="uploadSingleFile")					{
        //print_R($_GET);
        $RootDir 		= $_GET['RootDir'];
        if($RootDir==""||$RootDir==".."||$RootDir=="../..")   $RootDir = "../../";
        if($_GET['ChooseDir']!="")					{
            $RootDir 			= $RootDir."/".$_GET['ChooseDir'];
            $_GET['ChooseDir'] 	= "";
        }
        
        $RootDir = str_replace('//','/',$RootDir);
        if($_GET['LastDir']=="LastDir"&&strlen($RootDir)>6)			{
            $RootDirARRAY = explode('/',$RootDir);
            array_pop($RootDirARRAY);
            $RootDir = join('/',$RootDirARRAY);
        }
        //print $RootDir;exit;
        
        $RootDir显示 = str_replace('../../','',$RootDir);
        $RootDir显示 = str_replace('../..','',$RootDir);
        
        page_css("单个文件在线上传");
        print "<BR><FORM name=form1 action=\"?adsfa&action=uploadSingleFileDataDeal&pageid=1\" method=post encType=multipart/form-data>";
        
        
        table_begin("1100");
        print "<tr class=TableHeader><td colspan=2>&nbsp;手动指定文件进行上传【".$学校名称."】【".$API地址."】【注意尽量不要短时间内快速点击上传,每一个单个文件上传尽量间隔1-2秒】(如果提示上传失败,请重新上传.)</td></tr>";
        print " <TR>
                    <TD class=TableHeader colspan=2>&nbsp;常用目录:
                    <input type=button class='layui-btn layui-btn-xs layui-btn' name='Api' value='Api' Onclick=\"location='?".strval("asdfa&id=".$_GET['id']."&action=".$_GET['action']."&ChooseDir=api&RootDir=../../&ds")."'\">
                    <input type=button class='layui-btn layui-btn-xs layui-btn' name='Apps' value='Apps' Onclick=\"location='?".strval("asdfa&id=".$_GET['id']."&action=".$_GET['action']."&ChooseDir=apps&RootDir=../../api&ds")."'\">
                    <input type=button class='layui-btn layui-btn-xs layui-btn' name='Plugins' value='Plugins' Onclick=\"location='?".strval("asdfa&id=".$_GET['id']."&action=".$_GET['action']."&ChooseDir=plugins&RootDir=../../api&ds")."'\">
                    <input type=button class='layui-btn layui-btn-xs layui-btn' name='Tools' value='Tools' Onclick=\"location='?".strval("asdfa&id=".$_GET['id']."&action=".$_GET['action']."&ChooseDir=tools&RootDir=../../api&ds")."'\">
                    <input type=button class='layui-btn layui-btn-xs layui-btn' name='Webroot' value='Webroot' Onclick=\"location='?".strval("asdfa&id=".$_GET['id']."&action=".$_GET['action']."&ChooseDir=Webroot&RootDir=../../&ds")."'\">
                    <input type=button class='layui-btn layui-btn-xs layui-btn' name='Goview' value='Goview' Onclick=\"location='?".strval("asdfa&id=".$_GET['id']."&action=".$_GET['action']."&ChooseDir=Goview&RootDir=../../&ds")."'\">
                    </TD>
                </TR>
                ";
        print " <TR>
                    <TD class=TableHeader noWrap width='10%'>&nbsp;文件夹</TD>
                    <TD class=TableHeader noWrap width='90%'>&nbsp;
                        <font color=red>文件路径:".$RootDir显示."</font>
                    </TD>
                </TR>
                ";

        print "<TR><TD class=TableData noWrap width='10%' valign=top><table width=100% border=0>";
        
        $URL 	= strval("asdfa&id=".$_GET['id']."&action=".$_GET['action']."&LastDir=LastDir&RootDir=".$RootDir."&ds");
        print " <TR class=TableData>
                                <TD class=TableData noWrap style='border-left:1px #dddddd solid;cursor:hand' Onclick=\"location='?".$URL."'\" >&nbsp;
                                    <input type=button class='layui-btn layui-btn-xs layui-btn' name='LastDir' value='LastDir' Onclick=\"location='?".$URL."'\">
                                </TD>
                            </TR>
                            ";
        if ($handle = opendir($RootDir)) 					{
            while (false !== ($file = readdir($handle))) {
                //print $RootDir."/".$file."<BR>";
                if ($file != "." && $file != ".." && substr($file,0,1) != "." && is_dir($RootDir."/".$file)) {
                    $URL 	= strval("asdfa&id=".$_GET['id']."&action=".$_GET['action']."&ChooseDir=".$file."&RootDir=".$RootDir."&ds");
                    if($_GET['ChooseDir']==$file)			{
                        $class = "TableHeader";
                    }
                    else	{
                        $class = "TableData";
                    }
                    print " <TR class=$class>
                                <TD class=TableData noWrap style='border-left:1px #dddddd solid;cursor:hand;cursor:pointer;' Onclick=\"location='?".$URL."'\" >&nbsp;
                                    <font size='3px;'>".$file."</font>
                                </TD>
                            </TR>
                            ";
                }
            }
            closedir($handle);
        }
        print "</table>";
        print "</td><TD class=TableData noWrap width='90%' valign=top><table width=100% border=0>";
        //<input type=checkbox name='文件名称[]' value='$file' id='文件名称'/>
        $排除文件列表[] = "CryptField.php";
        $排除文件列表[] = "version.php";
        $排除文件列表[] = "CryptField.php";
        $排除文件列表[] = "CryptField.php";
        $排除文件列表[] = "CryptField.php";
        $排除文件列表[] = "CryptField.php";
        $排除文件列表[] = "system_config.php";
        $排除文件列表[] = "cache.inc.php";
        $排除文件列表[] = "SCHOOL_MODEL.ini";
        if ($handle = opendir($RootDir)) {
            $文件按最近修改时间排序 = array();
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file,0,1) != "." && is_file($RootDir."/".$file) && !in_array($file,$排除文件列表)) {
                    $filemtime = filemtime($RootDir."/".$file);
                    $文件按最近修改时间排序[$file] = $filemtime;
                }
            }
            closedir($handle);				
            arsort($文件按最近修改时间排序);
            $URL 	= strval("asdfa&id=".$_GET['id']."&action=uploadSingleDirDataDeal&LastDir=LastDir&RootDir=".$RootDir."&ChooseFile=".$file."&ds");
            print " <TR class=TableData>
                            <TD class=TableData noWrap style='border-left:1px #dddddd solid'>&nbsp;
                                <input type=button id='压缩当前文件夹并且上传' name='压缩当前文件夹并且上传' value='压缩当前文件夹并且上传' class='layui-btn layui-btn-sm layui-btn-default' onclick=\"javascript:ZIP压缩当前文件夹并且上传('".$file."');\" />".date("Y-m-d H:i:s",$MTIME)."
                                <span id='压缩当前文件夹并且上传显示文本_'><span>
                                <script>
                                    function ZIP压缩当前文件夹并且上传(filename)			{
                                        jQuery(\"#压缩当前文件夹并且上传\").attr(\"class\",\"layui-btn layui-btn-sm layui-btn-normal\");
                                        jQuery(\"#压缩当前文件夹并且上传显示文本_\").delay(0).show(0);
                                        jQuery(\"#压缩当前文件夹并且上传显示文本_\").html(\"<font color=green>正在压缩文件夹并上传中,文件较多,请耐心等待.....(通常需1-5分钟)</font>\");
                                        jQuery(\"#压缩当前文件夹并且上传显示文本_\").delay(0).show(0);
                                        //处理上传文件-开始
                                        jQuery.ajax({
                                            type: \"get\",
                                            url: \"?$URL\",
                                            dataType:\"json\",
                                            success: function (res) {
                                                var content			= res['content'];
                                                var DirectShowText 	= '';
                                                DirectShowText 		= content.substring(0,4);
                                                if(DirectShowText=='3目标文' || DirectShowText=='')				{
                                                    jQuery(\"#压缩当前文件夹并且上传显示文本_\").delay(0).show(0);
                                                    jQuery(\"#压缩当前文件夹并且上传显示文本_\").html(\"<font color=red>上传状态:失败,请重新上传!</font>\");
                                                    jQuery(\"#压缩当前文件夹并且上传显示文本_\").delay(30000).hide(0);
                                                    jQuery(\"#压缩当前文件夹并且上传\").attr(\"class\",\"layui-btn layui-btn-sm layui-btn-primary\");
                                                }
                                                else		{
                                                    jQuery(\"#压缩当前文件夹并且上传显示文本_\").delay(0).show(0);
                                                    jQuery(\"#压缩当前文件夹并且上传显示文本_\").html(\"<font color=blue>上传状态:\"+DirectShowText+\"</font>\");
                                                    jQuery(\"#压缩当前文件夹并且上传显示文本_\").delay(4000).hide(0);
                                                    jQuery(\"#压缩当前文件夹并且上传\").attr(\"class\",\"layui-btn layui-btn-sm layui-btn-primary\");
                                                }
                                            }
                                        })
                                        
                                        //处理上传文件-结束
                                        
                                    }
                                </script>
                            </TD>
                        </TR>
                        ";
            foreach($文件按最近修改时间排序 AS $file => $MTIME)		{
                $FILE_COUNTER ++;
                $URL 	= strval("asdfa&id=".$_GET['id']."&action=uploadSingleFileDataDeal&LastDir=LastDir&RootDir=".$RootDir."&ChooseFile=".$file."&ds");
                print " <TR class=TableData>
                            <TD class=TableData noWrap style='border-left:1px #dddddd solid'>&nbsp;
                                <font size='3px;'>".$file."</font>
                                <input type=button id='单个上传选中文件_".$FILE_COUNTER."' name='单个上传选中文件_".$FILE_COUNTER."' value='单个上传选中文件' class='layui-btn layui-btn-sm layui-btn-primary' onclick=\"javascript:UPLOAD_SINGLE_FILE_".$FILE_COUNTER."('".$file."','".$FILE_COUNTER."');\" />
                                ".date("Y-m-d H:i:s",$MTIME)."
                                <span id='单个上传选中文件_显示文本_".$FILE_COUNTER."'><span>
                                <script>
                                    function UPLOAD_SINGLE_FILE_".$FILE_COUNTER."(filename,FILE_COUNTER)			{
                                        jQuery(\"#单个上传选中文件_\"+FILE_COUNTER).attr(\"class\",\"layui-btn layui-btn-sm layui-btn-normal\");
                                        jQuery(\"#单个上传选中文件_显示文本_\"+FILE_COUNTER).delay(0).show(0);
                                        jQuery(\"#单个上传选中文件_显示文本_\"+FILE_COUNTER).html(\"<font color=green>正在压缩文件并上传中.....(通常需3-6秒)</font>\");
                                        jQuery(\"#单个上传选中文件_显示文本_\"+FILE_COUNTER).delay(0).show(0);
                                        //处理上传文件-开始
                                        jQuery.ajax({
                                            type: \"get\",
                                            url: \"?$URL\",
                                            dataType:\"json\",
                                            success: function (res) {
                                                var content			= res['content'];
                                                var DirectShowText 	= '';
                                                DirectShowText 		= content.substring(0,4);
                                                if(DirectShowText=='3目标文' || DirectShowText=='')				{
                                                    jQuery(\"#单个上传选中文件_显示文本_\"+FILE_COUNTER).delay(0).show(0);
                                                    jQuery(\"#单个上传选中文件_显示文本_\"+FILE_COUNTER).html(\"<font color=red>上传状态:失败,请重新上传!</font>\");
                                                    jQuery(\"#单个上传选中文件_显示文本_\"+FILE_COUNTER).delay(30000).hide(0);
                                                    jQuery(\"#单个上传选中文件_\"+FILE_COUNTER).attr(\"class\",\"layui-btn layui-btn-sm layui-btn-primary\");
                                                }
                                                else		{
                                                    jQuery(\"#单个上传选中文件_显示文本_\"+FILE_COUNTER).delay(0).show(0);
                                                    jQuery(\"#单个上传选中文件_显示文本_\"+FILE_COUNTER).html(\"<font color=blue>上传状态:\"+DirectShowText+\"</font>\");
                                                    jQuery(\"#单个上传选中文件_显示文本_\"+FILE_COUNTER).delay(4000).hide(0);
                                                    jQuery(\"#单个上传选中文件_\"+FILE_COUNTER).attr(\"class\",\"layui-btn layui-btn-sm layui-btn-primary\");
                                                }
                                            }
                                        })
                                        
                                        //处理上传文件-结束
                                        
                                    }
                                </script>
                            </TD>
                        </TR>
                        ";
                //location='?$URL'
                //
            }
        }
        print "</TD></TR>";
        table_end();
        form_end();
        
        exit;
    }
    
    function zipSingleFiles($上传文件ARRAY)					{
        global $FileCacheDir;
        global $API_ROOT;
        $filename	= $FileCacheDir."/".date("Y-m-d-H-i-s")."_".rand(1111,9999)."_".rand(1111,9999).".zip";
        if(file_exists($filename))		{
            unlink($filename);
        }
        if(!file_exists($filename))		{
            $zip = new ZipArchive();
            if ($zip->open($filename, ZipArchive::CREATE)==TRUE) {
                foreach($上传文件ARRAY AS $FULLNAME)			{
                    $上传文件 			= str_replace($API_ROOT,"",$FULLNAME);
                    $上传文件 			= str_replace("D:/MYEDUDEV/icampus-student-affairs-manage/webroot../api/../../","",$FULLNAME);
                    $zip->addFile($FULLNAME, $上传文件);						
                }
                $zip->close();
            }
            return $filename;	
        }					
    }

    
    if($_GET['action']=="uploadSingleFileDataDeal")			{
        //print_R($_GET);
        //$打包文件时间 	= $_POST['打包文件时间'];
        //$Module 			= $_POST['Module'];
        global $SourceDir;
        $上传文件 			= $SourceDir."/".$_GET['RootDir']."/".$_GET['ChooseFile'];
        //$上传文件 			= str_replace("../../","",$上传文件);
        $上传文件ARRAY[] 	= $上传文件;
        $filename 			= zipSingleFiles($上传文件ARRAY);
        $url 			    = $API地址."/tools/updatecore.php?action=uploadAndExtractFile";
        $urllog			    = $API地址."/tools/updatecore.html";
        $PHP_VERSION 	    = substr(PHP_VERSION,0,3);
        $post_data 	        = array("td_form_upload" => new CURLFile(realpath($filename)),"timeline" => time());
        $ch 		        = curl_init();
        curl_setopt($ch , CURLOPT_URL , $url);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch , CURLOPT_POST, 1);
        curl_setopt($ch , CURLOPT_TIMEOUT, 500);
        if(substr($API地址,0,5)=="https")		{
            curl_setopt($ch , CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch , CURLOPT_SSL_VERIFYHOST, FALSE);
            //print_R($post_data);
        }
        curl_setopt($ch , CURLOPT_POSTFIELDS, $post_data);
        $output 			= curl_exec($ch);
        curl_close($ch);

        $output = strip_tags($output);
        if(strpos($output,"上传成功")!="")		{
            $content = "上传成功";
        }
        else		{
            $content = $output;
        }
        $RS['result'] 		= "ok";
        $RS['content'] 		= $content;
        $RS['output'] 		= $output;
        print json_encode($RS);
        //@unlink($filename);
        exit;
    }

    if($_GET['action']=="uploadSingleDirDataDeal")			{
        //print_R($_GET);
        //$打包文件时间 	= $_POST['打包文件时间'];
        //$Module 			= $_POST['Module'];
        global $SourceDir,$FileCacheDir;
        $sourceDir 			= $SourceDir."/".$_GET['RootDir']."/"; // 要压缩的源目录路径
        $zipFilePath	    = $FileCacheDir."/".date("Y-m-d-H-i-s")."_".rand(1111,9999)."_".rand(1111,9999).".zip";// 压缩后的ZIP文件路径

        // 创建一个ZipArchive对象
        $zip = new ZipArchive();
        // 打开或创建要输出的ZIP文件
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // 递归地将目录中的文件添加到ZIP文件中
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir));
            foreach ($files as $file) {
                // 跳过目录本身和父目录
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    //$relativePath = substr($filePath, strlen($sourceDir) + 1); // 获取相对于源目录的路径                    
                    $relativePath   = str_replace("D:\MYEDUDEV\icampus-student-affairs-manage\\","",$filePath);
                    //print $filePath."<BR>";
                    //print $relativePath."<BR>";
                    $zip->addFile($filePath, $relativePath);
                }
            }
            // 关闭ZipArchive对象
            $zip->close();
            //echo '压缩成功！';
        } else {
            //echo '无法创建或打开ZIP文件。';
        }
        $url 			    = $API地址."/tools/updatecore.php?action=uploadAndExtractFile";
        $urllog			    = $API地址."/tools/updatecore.html";
        $PHP_VERSION 	    = substr(PHP_VERSION,0,3);
        $post_data 	        = array("td_form_upload" => new CURLFile(realpath($zipFilePath)),"timeline" => time());
        $ch 		        = curl_init();
        curl_setopt($ch , CURLOPT_URL , $url);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch , CURLOPT_POST, 1);
        curl_setopt($ch , CURLOPT_TIMEOUT, 500);
        if(substr($API地址,0,5)=="https")		{
            curl_setopt($ch , CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch , CURLOPT_SSL_VERIFYHOST, FALSE);
            //print_R($post_data);
        }
        curl_setopt($ch , CURLOPT_POSTFIELDS, $post_data);
        $output 			= curl_exec($ch);
        curl_close($ch);

        $output = strip_tags($output);
        if(strpos($output,"上传成功")!="")		{
            $content = "上传成功";
        }
        else		{
            $content = $output;
        }
        $RS['result'] 		= "ok";
        $RS['content'] 		= $content;
        $RS['output'] 		= $output;
        print json_encode($RS);
        exit;
    }
    
}


$JumpFile[] = "config_mssql.php";
$JumpFile[] = "config_mssql_studentkaoqin.php";
$JumpFile[] = "config_mssql_teacherkaoqin.php";
$JumpFile[] = "version.php";
$JumpFile[] = "access_register_newai.php";
$JumpFile[] = "access_newai.php";
$JumpFile[] = "Thumbs.db";
$JumpFile[] = "cache.inc.php";
$JumpFile[] = "config.php";
$JumpFile[] = "CryptField.php";
$JumpFile[] = "system_config.php";

function ziplocalfiles()					{
	global $SourceDir,$TargetDir,$JumpFile;
	
	//先清空上次缓存的文件夹
	deldir($TargetDir);
	$ReadZipFileList .= ReadZipFileList("Development/",$TargetDatetime);
	$ReadZipFileList .= ReadZipFileList("Enginee/",$TargetDatetime);
	$ReadZipFileListArray 	= explode('::',$ReadZipFileList);
	$ReadZipFileListArray[] = "Development/main.php";
	$ReadZipFileListArray[] = "Development/function.php";
	//print_R($_POST['掌上校园新版图标']);print_R($ReadZipFileListArray);exit;
	
	
	$datalist = array();
	for($i=0;$i<sizeof($ReadZipFileListArray);$i++)								{
		$Element = $ReadZipFileListArray[$i];
		if($Element!="")													{
			$文件数组	= explode('/',$Element);
			$文件名		= array_pop($文件数组);
			if($文件数组[0]!="")										{
				if(!is_dir($TargetDir."/".$文件数组[0]))			{
					mkdir($TargetDir."/".$文件数组[0]);
					$datalist [] = $文件数组[0]; 
				}
			}
			if($文件数组[1]!="")										{
				if(!is_dir($TargetDir."/".$文件数组[0]."/".$文件数组[1]))			{
					mkdir($TargetDir."/".$文件数组[0]."/".$文件数组[1]);
					$datalist [] = $文件数组[0]."/".$文件数组[1]; 
				}
			}
			if($文件数组[2]!="")										{
				if(!is_dir($TargetDir."/".$文件数组[0]."/".$文件数组[1]."/".$文件数组[2]))			{
					mkdir($TargetDir."/".$文件数组[0]."/".$文件数组[1]."/".$文件数组[2]);
					$datalist [] = $文件数组[0]."/".$文件数组[1]."/".$文件数组[2]; 
				}
			}
			if($文件数组[3]!="")										{
				if(!is_dir($TargetDir."/".$文件数组[0]."/".$文件数组[1]."/".$文件数组[2]."/".$文件数组[3]))			{
					mkdir($TargetDir."/".$文件数组[0]."/".$文件数组[1]."/".$文件数组[2]."/".$文件数组[3]);
					$datalist [] = $文件数组[0]."/".$文件数组[1]."/".$文件数组[2]."/".$文件数组[3]; 
				}
			}
			
			
			$filectime	= filemtime($SourceDir.$Element);
			$curtime	= date("Y-m-d H:i:s",$filectime);
			//print $curtime;print "<BR>";
			$打包文件时间 	= $_POST['打包文件时间']." 00:00:01";
			if( $curtime!="2025-01-10 01:01:00" && ($curtime>$打包文件时间 || strpos($Element,"images2020")>0) )				{
				$时间判断 = 1;
			}
			else		{
				$时间判断 = 0;
			}
			
			if($_POST['指定文件全部上传']=="是")			{
				$时间判断 = 1;
			}
			
			//print_R($文件数组);
			if(in_array($文件名,$JumpFile))						{
				//print $Element."例外文件,不复制<BR>";
			}
			else												{
				if(substr($Element,-4)==".ini"||substr($Element,-4)==".php"||substr($Element,-4)==".css"||substr($Element,-3)==".js"||substr($Element,-4)==".exe"||substr($Element,-4)==".png"||substr($Element,-4)==".jpg")			{
					if($时间判断==1)				{
						//只复制PHP文件
						//print $SourceDir.$Element."||||||||||".$TargetDir.$Element."<BR>";
						copy($SourceDir.$Element  ,  $TargetDir.$Element);
						$datalist[] = $TargetDir.$Element;
						//print $curtime."###".$时间判断."###".$SourceDir.$Element;print "<BR>";
					}
				}
			}
		}
	}
	
	//print_R($ReadZipFileListArray);	print_R($时间判断);	print_R($datalist);exit;
	
	$TargetDir2 = str_replace("/EDU","",$TargetDir);
	$filename	= $TargetDir2.date("Y-m-d-H").".zip";
	if(file_exists($filename))		{
		unlink($filename);
	}
	if(!file_exists($filename))		{
		$zip = new ZipArchive();
		if ($zip->open($filename, ZipArchive::CREATE)==TRUE) {
			addFolderToZip($TargetDir, $zip);
			$zip->close();
		}
	}
	
	return $filename;
	
}

function deldir($path)					{
	//如果是目录则继续
	if(is_dir($path))				{
		//扫描一个文件夹内的所有文件夹和文件并返回数组
		$p = scandir($path);
		foreach($p as $val)			{
			//排除目录中的.和..
			if($val !="." && $val !="..")	{
				//如果是目录则递归子目录，继续操作
				if(is_dir($path.$val))	{
					//子目录中操作删除文件夹和文件
					deldir($path.$val.'/');
					//目录清空后删除空文件夹
					@rmdir($path.$val.'/');
				}else{
					//如果是文件直接删除
					unlink($path.$val);
				}
			}
		}
	}
}
	 
function addFolderToZip($dir, $zipArchive)	{
    global $TargetDir;
    if (is_dir($dir)) 					{
        if ($dh = opendir($dir)) {
			//Add the directory
			$NewDir 	= str_replace($TargetDir,"",$dir);
			if($NewDir!="")			{
				$zipArchive->addEmptyDir($NewDir);
			}
			//print $dir."<BR>";
			//print $TargetDir."<BR>";
			//print $NewDir."<BR>";
            
            // Loop through all the files
            while (($file = readdir($dh)) !== false) {
            
                //If it's a folder, run the function again!
                if(!is_file($dir . $file)){
                    // Skip parent and root directories
                    if( ($file !== ".") && ($file !== "..")){
                        addFolderToZip($dir . $file . "/", $zipArchive);
                    }
                    
                }else{
                    // Add the files
                    $zipArchive->addFile($dir . $file, $NewDir.$file);
                    
                }
            }
        }
    }
}


function 得到指定的表单下面的所有文件($testdir,$TargetDatetime,$英文标识)					{
	global $SYSTEMDOCLINK,$TargetDir,$SourceDir,$JumpFile;
	$testdir2 		= $SourceDir.$testdir;
	$打包文件时间 	= $_POST['打包文件时间']." 00:00:01";
	//print $TargetDir.$testdir;exit;
	$d				= opendir($testdir2."/");
	$dirList		= array();
	$fileSizeList	= array();
	while($file=readdir($d)){
		if($file!='.'&&$file!='..'&&$file!='')						{
			$path	= $testdir2."".$file;
			//print $遍历."<BR>";
			if(is_file($path)&&strpos($file,$英文标识)>0)		{
				//print $path."DIR<BR>";
				$filectime	= filemtime($path);
				$curtime	= date("Y-m-d H:i:s",$filectime);
				//print $curtime;print "<BR>";
				//&&$file!="update.txt"
				$文件数组	= explode('/',$filectime);
				$文件名		= array_pop($文件数组);
				if($_GET['指定文件全部上传']=="是")			{
					$ReturnText .= $testdir."".$file."::";
				}
				else		{
					if( $curtime!="2025-01-10 01:01:00" && $curtime>$打包文件时间 && (substr($file,-4)==".ini"||substr($file,-4)==".php"||substr($file,-4)==".css"||substr($file,-3)==".js") &&!in_Array($file,$JumpFile))			{
						$ReturnText .= $testdir."".$file."::";
					}					
				}
			}
		}
	}//end while
	//print $curtime;exit;
	return $ReturnText;
}


function ReadZipFileList($testdir,$TargetDatetime,$遍历="")					{
	global $SYSTEMDOCLINK,$TargetDir,$SourceDir,$JumpFile;
	$testdir2 		= $SourceDir.$testdir;
	$打包文件时间 	= $_POST['打包文件时间']." 00:00:01";
	//print $TargetDir.$testdir;exit;
	$d				= opendir($testdir2."/");
	$dirList		= array();
	$fileSizeList	= array();
	while($file=readdir($d)){
		if($file!='.'&&$file!='..'&&$file!='')		{
			$path	= $testdir2."".$file;
			//print $遍历."<BR>";
			if(is_file($path))		{
				//print $path."DIR<BR>";
				$filectime	= filemtime($path);
				$curtime	= date("Y-m-d H:i:s",$filectime);
				//print $curtime;print "<BR>";
				//&&$file!="update.txt"
				$文件数组	= explode('/',$filectime);
				$文件名		= array_pop($文件数组);
				if( $curtime!="2025-01-10 01:01:00" && $curtime>$打包文件时间 && (substr($file,-4)==".ini"||substr($file,-4)==".php"||substr($file,-4)==".css"||substr($file,-3)==".js") &&!in_Array($file,$JumpFile))			{
					$ReturnText .= $testdir."".$file."::";
				}
			}
			if($遍历=="是")										{
				if(is_dir($path))				{
					//print $file."<BR>";
					$ReturnText .= ReadZipFileList2($testdir.$file."/",$TargetDatetime);
				}
			}
		}
	}//end while
	//print $curtime;exit;
	return $ReturnText;
}

function ReadZipFileList2($testdir,$TargetDatetime)					{
	global $SYSTEMDOCLINK,$TargetDir,$SourceDir,$JumpFile;
	$testdir2 		= $SourceDir.$testdir;
	$d				= opendir($testdir2."/");
	$dirList		= array();
	$fileSizeList	= array();
	while($file=readdir($d)){
		if($file!='.'&&$file!='..'&&$file!='')		{
			$path	= $testdir2.$file;
			//print $path."<BR>";
			if(is_file($path))		{
				$filectime	= filemtime($path);
				$curtime	= date("Y-m-d H:i:s",$filectime);
				//print $curtime;print "<BR>";
				//&&$file!="update.txt"
				$文件数组	= explode('/',$filectime);
				$文件名		= array_pop($文件数组);
				if( (substr($file,-4)==".ini"||substr($file,-4)==".php"||substr($file,-4)==".css"||substr($file,-3)==".js") &&!in_Array($file,$JumpFile))			{
					$ReturnText .= $testdir.$file."::";
					//print $ReturnText;exit;
				}
			}
		}
	}//end while
	//print $curtime;exit;
	return $ReturnText;
}


?>