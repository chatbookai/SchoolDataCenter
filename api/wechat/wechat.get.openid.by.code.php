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

/*
$_POST['SYSTEM_APPSTORE_ID'] 	= 1;
$_POST['SYSTEM_IS_CLOUD'] 		= 0;
$_POST['LOGIN_SCHOOL_ID'] 		= 0;
$_POST['SYSTEM_FORCE_TO_BIND_USER_STATUS'] 		= 1;
$_POST['code'] 						= '';
*/

$SYSTEM_RUNNING_SYSTEM				= $_POST['SYSTEM_RUNNING_SYSTEM'];
$SYSTEM_IS_CLOUD					= $_POST['SYSTEM_IS_CLOUD'];
$SYSTEM_APPSTORE_ID 				= $_POST['SYSTEM_APPSTORE_ID'];
$LOGIN_SCHOOL_ID					= $_POST['LOGIN_SCHOOL_ID'];
$SYSTEM_FORCE_TO_BIND_USER_STATUS	= $_POST['SYSTEM_FORCE_TO_BIND_USER_STATUS'];
$code								= $_POST['code'];

if($code!=""&&$LOGIN_SCHOOL_ID!==""&&$SYSTEM_APPSTORE_ID!=""&&$SYSTEM_IS_CLOUD!=="")				{
	得到微信小程序的OPENID($LOGIN_SCHOOL_ID,$SYSTEM_APPSTORE_ID,$code,$SYSTEM_IS_CLOUD,$SYSTEM_FORCE_TO_BIND_USER_STATUS,$SYSTEM_RUNNING_SYSTEM);
}

function 得到微信小程序的OPENID($LOGIN_SCHOOL_ID,$SYSTEM_APPSTORE_ID,$code,$SYSTEM_IS_CLOUD,$SYSTEM_FORCE_TO_BIND_USER_STATUS,$SYSTEM_RUNNING_SYSTEM)			{
	//单个小程序来获取OPENID
	//$URL			= "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";
	//利用第三方平台来获取OPDN
	global $db;
	//小程序信息的APPID
	$小程序信息 			     = returntablefield("data_miniprogram","id",$SYSTEM_APPSTORE_ID,"小程序名称,部署类型,component_appid,component_access_token,AppSecret");;
	$小程序名称 				 = $小程序信息['小程序名称'];
	$component_appid 			= $小程序信息['component_appid'];
	$component_access_token 	= $小程序信息['component_access_token'];
	$部署类型 					 = $小程序信息['部署类型'];
	
	if($SYSTEM_IS_CLOUD==0)				{
		//私有部署;
		//单个小程序 下面的OPEN获取
		$appid 				= $小程序信息['component_appid'];
		$AppSecret 			= $小程序信息['AppSecret'];
		$URL				= "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$AppSecret&js_code=".$code."&grant_type=authorization_code";
		$apiData			= file_get_contents($URL);
		$apiData 					= json_decode($apiData,true);
		$apiData['appid'] 			= $appid;
		$apiData['AppSecret'] 		= "最后10位:".substr($AppSecret,-10);
		$apiData['code'] 			= $code;
		$apiData['URL'] 			= $URL;
		$apiData['小程序信息'] 	     = $小程序信息;
		$apiData                    = json_encode($apiData);
		//print $apiData;
	}
		
	//print $URL;
	//print_R($小程序信息);print_R($apiData);exit;//直接输出给微信小程序使用
	$ARRAY	= json_decode($apiData);;
	
	if($ARRAY->openid!="")				{
		//正常获取到OPENID,连同数据信息一起进行判断;
		$userinfo				= str_replace('\"','"',$_POST['userinfo']);;	//print_R($_POST);print_R($userinfo);
		$userinfo				= json_decode($userinfo,true);					//print "\n<BR>";print_R($userinfo);exit;
		if(!is_array($userinfo) || $userinfo=="")					{
			$userinfo 	= base64_decode($_POST['userinfo']);
			$userinfo	= json_decode($userinfo,true);	
		}
		//print_R($userinfo);//exit;

		$Element 				= [];
		$Element['城市']		= $userinfo['city'];
		$Element['省份']		= $userinfo['province'];
		$Element['国家']		= $userinfo['country'];
		$Element['语言']		= $userinfo['language'];
		$Element['昵称']		= $userinfo['nickName'];
		$Element['性别']		= $userinfo['gender'];
		$Element['头像']		= $userinfo['avatarUrl'];
		$Element['SESSIONKEY']	= $ARRAY->session_key;
		$Element['OPENID']		= $ARRAY->openid;
		$Element['SESSIONTEMP']	= md5($Element['SESSIONKEY']."_".$Element['OPENID']).date("mdHi");

		$currentDeviceInfo		= str_replace('\"','"',$_POST['currentDeviceInfo']);;
		$当前设备信息			= json_decode($currentDeviceInfo,true);
		if(!is_array($当前设备信息) || $当前设备信息=="")					{
			$当前设备信息 	= base64_decode($_POST['currentDeviceInfo']);
			$当前设备信息	= json_decode($当前设备信息,true);	
		}

		$Element['手机型号']	= $当前设备信息['model'];
		$Element['设备像素比']	= $当前设备信息['pixelRatio'];
		$Element['屏幕宽度']	= $当前设备信息['windowWidth'];
		$Element['屏幕高度']	= $当前设备信息['windowHeight'];
		$Element['微信语言']	= $当前设备信息['language'];
		$Element['微信版本号']	= $当前设备信息['version'];
		$Element['客户端平台']	= $当前设备信息['platform'];
		$Element['操作系统版本']			= $当前设备信息['system'];
		$Element['微信客户端基础库版本']	= $当前设备信息['versSDKVersionion'];
		
		//$Element['企业微信用户名']			= $_POST['dandian_system_qiyeweixin_userid'];
		//$Element['企业微信用户类型']		    = $_POST['dandian_system_qiyeweixin_usertype'];
		//$Element['企业微信关联OA用户']		= "";

		//print_R($Element);
        [$rs,$sql] = InsertOrUpdateTableByArray("data_miniprogram_user",$Element,'OPENID',0);
		if($rs->EOF) {
            $RS['status']   = "OK";
            $RS['msg']      = "Get Data Success";
            $RS['openid']    = $ARRAY->openid;
            print json_encode($RS);
            exit;
        }
        else {
            print $sql;
            print_R($rs->EOF);
        }
		//插入推荐好友清单.
		$dandian_user_sharesource 				= $_POST['dandian_user_sharesource'];
		if($dandian_user_sharesource!=""&&$dandian_user_sharesource!="undefined")			{
			$dandian_user_sharesource 			= base64_decode(base64_decode($dandian_user_sharesource));
			$dandian_user_sharesource_array 	= explode("||||",$dandian_user_sharesource);
			$dandian_user_sharesource_openid 	= $dandian_user_sharesource_array[0];
			$dandian_user_sharesource_nickname 	= $dandian_user_sharesource_array[1];
			$ElementX 				= [];
			$ElementX['小程序名称']	= $小程序名称;
			//$ElementX['用户名']		= $用户名;
			//$ElementX['姓名']		= $姓名;
			$ElementX['OPENID']		= $ARRAY->openid;
			
			$ElementX['积分分值']	= 10;
			$ElementX['积分类型']	= "好友推荐";
			$ElementX['积分说明']	= "每推荐一个好友,会获取10个积分,好友不能重复.";
			//$ElementX['昵称']		= iconv("gbk","utf-8",$ElementX['昵称']);
			//$ElementX['昵称']		= str_replace("'","’",$ElementX['昵称']);		
			
			$ElementX['好友昵称']	= iconv("gbk","utf-8",$dandian_user_sharesource_nickname);
			$ElementX['好友昵称']	= str_replace("'","’",$ElementX['好友昵称']);
			
			$ElementX['好友OPENID']	= $dandian_user_sharesource_openid;
			$ElementX['创建时间']	= date("Y-m-d H:i:s");
			//UserArrayToInsertAndUpdateTable("data_icampus_sharetofriends",$ElementX,"OPENID,好友OPENID");
		}
	
	}
}


?>