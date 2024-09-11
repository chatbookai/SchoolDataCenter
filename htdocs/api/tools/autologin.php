<?php
/*
* 基础架构: 单点低代码开发平台
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2023
* License: GPL V3 or Commercial license
*/
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');
ini_set('max_execution_time', 7200);

session_start();

function DecryptIDForUserName($data) {
	$data 		  = base64_safe_decode($data);
	$dataArray 	= explode("::",$data);
	$data 		= $dataArray[0];
	$iv 		  = base64_safe_decode($dataArray[1]);
	$cipher 	= "AES-256-CBC";
  $options 	= OPENSSL_RAW_DATA;
	$EncryptAESKey	= "DDKJ2023";
  $decrypted 	= openssl_decrypt(base64_safe_decode($data), $cipher, $EncryptAESKey, $options, $iv);
  return $decrypted;
}

//$_SESSION['LOGIN_USER_EDUID'] = "admin";
//print_R($_SESSION);
if($_POST['ddsso']!="")			{
	$ddsso			= str_replace(' ','+',$_POST['ddsso']);
	$ddsso_id		= DecryptIDForUserName($ddsso);
	$ddsso_id		= iconv("GB18030//ignore","UTF-8", $ddsso_id);
	$ddsso_id_array	= explode('||||',$ddsso_id);
	$_SESSION['LOGIN_USER_EDUID']	= $ddsso_id_array[0];
	$sql 	= "select * from data_user where USER_ID='".$_SESSION['LOGIN_USER_EDUID']."'";
	$rs		= $db->Execute($sql);
	$UserInfo = $rs->fields;
	if($UserInfo['USER_ID']=="")  { 
		$sql = "INSERT INTO data_user (USER_ID,USER_NAME,NICKNAME,USEING_KEY,PASSWORD,USER_PRIV,DEPT_ID,GENDER,BIRTHDAY,TEL_NO_DEPT,MOBILE_NO,EMAIL,AVATAR,LAST_VISIT_TIME,LAST_PASS_TIME,THEME,USER_PRIV_OTHER,USER_NO,NOT_LOGIN,NOT_SEARCH,BIND_IP,LAST_VISIT_IP,WEATHER_CITY,MENU_EXPAND,LIMIT_LOGIN,NOT_MOBILE_LOGIN,SKIN,MODE,PRIMARYCOLOR,CONTENTWIDTH,APPBARBLUR,FOOTERTYPE,MENUTOGGLE,MENUCOLLAPSED,MENUHIDDEN,STATUS,`LANGUAGE`) VALUES ('".$ddsso_id_array[0]."','".$ddsso_id_array[1]."','".$ddsso_id_array[1]."','','','5','3','','','','','','','1971-01-01 00:00:00','1971-01-01 00:00:00','','',0,1,0,'','','',1,1,1,'Default','Light','Primary','Full','Full','Static','1','1','1','1','Chinese');";
		$db->Execute($sql);
	}
	//print_R($ddsso_id_array);
	//$json_encode	= json_encode(array("RESULT1"=>"OK","RESULT2"=>"OK"));
	//print ''.$_GET['callback'].'(';
	//print $json_encode;
	//print ");";
}
	
if($_SESSION['LOGIN_USER_EDUID']!="")  { 
    JWT::$leeway    = $NEXT_PUBLIC_JWT_EXPIRATION;
    if(1)   {
        $sql 	= "select * from data_user where USER_ID='".$_SESSION['LOGIN_USER_EDUID']."'";
        $rs		= $db->Execute($sql);
        $UserInfo = $rs->fields;
        if($UserInfo['USER_ID']=="")  {  
            $sql    = "select * from data_student where 学号='".$_SESSION['LOGIN_USER_EDUID']."'";
            $rs		= $db->Execute($sql);
            $StudentInfo = $rs->fields;
            if($StudentInfo['学号']=="")  {  
                $RS = [];
                $RS['status']   = "ERROR";
                $RS['msg']      = $RS['email']    = __("USER NOT EXIST OR PASSWORD IS ERROR!");
                $RS['sql']      = $sql;
                $RS['_POST']    = $_POST;
                SystemLogRecord("Login", __('USER NOT EXIST'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
                print json_encode($RS);
                exit;
            }
            $PASSWORD_IN_DB         = $StudentInfo['密码'];
            if($PASSWORD_IN_DB!="")  {
                //Reform userData
                $userData = [];
                $userData['id']         = $StudentInfo['id'];
                $userData['USER_ID']    = $StudentInfo['学号'];
                $userData['USER_NAME']  = $StudentInfo['姓名'];
                $userData['学号']       = $StudentInfo['学号'];
                $userData['姓名']       = $StudentInfo['姓名'];
                $userData['班级']       = $StudentInfo['班级'];
                $userData['专业']       = $StudentInfo['专业'];
                $userData['系部']       = $StudentInfo['系部'];
                $userData['PRIV_NAME']  = "学生";
                $userData['avatar']     = '/images/avatars/1.png';        
                $userData['username']   = $StudentInfo['学号'];
                $userData['role']       = "学生";
                $userData['type']       = "Student";
                $accessToken            = EncryptID(JWT::encode($userData, $NEXT_PUBLIC_JWT_SECRET, 'HS256'));
                $RS['accessToken']      = $accessToken;
                $RS['userData']         = $userData;
                print_r(json_encode($RS));
                SystemLogRecord("Login", __("Success"), __("Success"),$USER_ID);
                exit;
            }
        }
        if($UserInfo['USER_ID']!="")  {
            //Reform userData
            $userData = [];
            $userData['id']         = $UserInfo['id'];
            $userData['USER_ID']    = $UserInfo['USER_ID'];
            $userData['USER_NAME']  = $UserInfo['USER_NAME'];
            $userData['EMAIL']      = $UserInfo['EMAIL'];
            $userData['DEPT_ID']    = $UserInfo['DEPT_ID'];
            $userData['DEPT_NAME']  = returntablefield("data_department","id",$UserInfo['DEPT_ID'],"DEPT_NAME")['DEPT_NAME'];
            $userData['PRIV_NAME']  = returntablefield("data_role","id",$UserInfo['USER_PRIV'],"name")['name'];
            $userData['USER_PRIV']  = $UserInfo['USER_PRIV'];
            $userData['avatar']     = '/images/avatars/1.png';        
            $userData['username']   = $UserInfo['USER_ID'];
            $userData['email']      = $UserInfo['EMAIL'];
            $userData['role']       = $userData['PRIV_NAME'];
            $userData['type']       = "User";
            $accessToken            = EncryptID(JWT::encode($userData, $NEXT_PUBLIC_JWT_SECRET, 'HS256'));
            $RS['accessToken']      = $accessToken;
            $RS['userData']         = $userData;
            $RS['status']           = "OK";
            //形成个人信息展示页面的数据列表
            $USER_PROFILE 	= array();
            $USER_PROFILE[] 	= array("左边"=>"用户类型","右边"=>"教职工");
            $USER_PROFILE[] 	= array("左边"=>"用户名","右边"=>$UserInfo['USER_ID']);
            $USER_PROFILE[] 	= array("左边"=>"姓名","右边"=>$UserInfo['USER_NAME']);
            $USER_PROFILE[] 	= array("左边"=>"部门","右边"=>$userData['DEPT_NAME']);
            $USER_PROFILE[] 	= array("左边"=>"角色","右边"=>$userData['PRIV_NAME']);
            $RS['USER_PROFILE'] = $USER_PROFILE;
            SystemLogRecord("Login", __("Success"), __("Success"),$USER_ID);
            print "<script>
            var data = ".json_encode($UserInfo).";
            localStorage.setItem('userData', JSON.stringify(data));
            localStorage.setItem('accessToken', '".$accessToken."');
            localStorage.setItem('i18nextLng', 'zh');
            setTimeout(function() {
                window.location.href = '/';
            }, 1000);            
            </script>";
        }
    }
}
else {
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
  }

  .message-box {
    text-align: center;
    padding: 50px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .success-message {
    font-size: 28px;
    margin-bottom: 10px;
    color: rgb(128, 75, 223);
  }

  .loading-spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-top: 4px solid rgb(128, 75, 223);
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin: 15px auto; /* 居中显示 */
    animation: spin 2s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>
<title>认证成功</title>
</head>
<body>
  <div class="message-box">
    <div class="success-message">系统认证成功</div>
    <div style="margin-bottom: 10px; color:rgb(128, 75, 223);">正在为您跳转中...</div>
    <div class="loading-spinner"></div>
  </div>
</body>
</html>
