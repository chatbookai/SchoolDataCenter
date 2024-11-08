<?php
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');
require_once('../data_enginee_function.php');

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING & ~E_NOTICE);

$_REQUEST['JSON'] = 1;
global $微信小程序_全局编号;
global $微信小程序_TableName;
global $HTTP_HOST;

$微信小程序_全局编号 = 3;
$微信小程序_英文标识 = "campus_xiaoyouhui";
$SYSTEM_IS_CLOUD    = 0;

$SYSTEM_CACHE_SECOND_TDFORMICAMPUS = 10;

$action = ForSqlInjection($_POST['action']);
$domain = ForSqlInjection($_POST['domain']);
$status = ForSqlInjection($_POST['status']);
$openid = ForSqlInjection($_POST['openid']);
$id		= DecryptID($_POST['id']);
$输出数据   = [];

//保存用户头像
if($action=="Avatar"&&$openid!="")     {
    //CheckAuthUserLoginStatus();
	$sql    = "select id from data_xiaoyou_member where openid='".$openid."'";
	$rs     = $db->Execute($sql);
	$id     = intval($rs->fields['id']);
	SaveWechatAvatar($FieldName='Avatar', $id);
	$RS             = [];
	$RS['status']   = "OK";
	$RS['msg']      = "头像上传成功";
	print json_encode($RS);
	exit;
}

//活动报名
if($action=="Enroll"&&$domain=="data_xiaoyou_activity"&&$id>0)     {
    //CheckAuthUserLoginStatus();
	$sql    = "select COUNT(*) AS NUM from data_xiaoyou_activity_record where 活动ID='".intval($id)."' and 用户ID='".$GLOBAL_USER->USER_ID."' ";
	$rs     = $db->Execute($sql);
	$NUM    = intval($rs->fields['NUM']);
	if($NUM==0) {
		$sql = "insert into data_xiaoyou_activity_record(活动ID,用户ID,报名时间) values('".intval($id)."','$GLOBAL_USER->USER_ID','".date("Y-m-d H:i:s")."');";
		$db->Execute($sql);
	}
	$RS             = [];
	$RS['status']   = "OK";
	$RS['msg']      = "成功报名";
	$RS['sql']   	= $sql;
	print json_encode($RS);
	exit;
}

//活动取消报名
if($action=="EnrollCancel"&&$domain=="data_xiaoyou_activity"&&$id>0)     {
    //CheckAuthUserLoginStatus();
	$sql    = "delete from data_xiaoyou_activity_record where 活动ID='".intval($id)."' and 用户ID='".$GLOBAL_USER->USER_ID."' ";
	$rs     = $db->Execute($sql);
	$RS             = [];
	$RS['status']   = "OK";
	$RS['msg']      = "成功取消";
	$RS['sql']   	= $sql;
	print json_encode($RS);
	exit;
}

//点赞次数加1
if($action=="Like"&&$id>0)     {
    //CheckAuthUserLoginStatus();
	switch($domain) {
		case 'data_xiaoyou_news':
			if($status=="true") {
				$sql = "update data_xiaoyou_news set 点赞次数=点赞次数+1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "点赞成功";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			else {
				$sql = "update data_xiaoyou_news set 点赞次数=点赞次数-1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "取消点赞";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			break;
		case 'data_xiaoyou_activity':
			if($status=="true") {
				$sql = "update data_xiaoyou_activity set 点赞次数=点赞次数+1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "点赞成功";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			else {
				$sql = "update data_xiaoyou_activity set 点赞次数=点赞次数-1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "取消点赞";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			break;
		case 'data_xiaoyou_cooperation':
			if($status=="true") {
				$sql = "update data_xiaoyou_cooperation set 点赞次数=点赞次数+1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "点赞成功";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			else {
				$sql = "update data_xiaoyou_cooperation set 点赞次数=点赞次数-1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "取消点赞";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			break;
	}
}

//收藏次数加1
if($action=="Favorite"&&$id>0)     	{
    //CheckAuthUserLoginStatus();
	switch($domain) {
		case 'data_xiaoyou_news':
			if($status=="true") {
				$sql = "update data_xiaoyou_news set 收藏次数=收藏次数+1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "收藏成功";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			else {
				$sql = "update data_xiaoyou_news set 收藏次数=收藏次数-1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "取消收藏";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			break;
		case 'data_xiaoyou_activity':
			if($status=="true") {
				$sql = "update data_xiaoyou_activity set 收藏次数=收藏次数+1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "收藏成功";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			else {
				$sql = "update data_xiaoyou_activity set 收藏次数=收藏次数-1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "取消收藏";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			break;
		case 'data_xiaoyou_cooperation':
			if($status=="true") {
				$sql = "update data_xiaoyou_cooperation set 收藏次数=收藏次数+1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "收藏成功";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			else {
				$sql = "update data_xiaoyou_cooperation set 收藏次数=收藏次数-1 where id='$id'";
				$db->Execute($sql);
				$RS             = [];
				$RS['status']   = "OK";
				$RS['msg']      = "取消收藏";
				$RS['sql']   	= $sql;
				print json_encode($RS);
				exit;
			}
			break;
	}
	$RS             = [];
	$RS['status']   = "OK";
	$RS['msg']      = "收藏成功";
	print json_encode($RS);
	exit;
}

//校友会首页数据
if($action=="")						{
	//校友资讯
	$sql 		= "select * from data_xiaoyou_news where 类别='校友资讯' and 发布状态='是' order by id desc limit 3";
	$rs 		= $db->Execute($sql);
	$rs_a 		= $rs->GetArray();
	for($i=0;$i<sizeof($rs_a);$i++) {
		$rs_a[$i]['内容'] 		= strip_tags($rs_a[$i]['内容']);
		$rs_a[$i]['图片'] 		= AttachFieldValueToUrl("data_xiaoyou_news",$rs_a[$i]['id'],'图片','avatar',$rs_a[$i]['图片']);
		$rs_a[$i]['ViewUrl']    = "?action=view_default&id=".EncryptID($rs_a[$i]['id']);
	}
	$输出数据['ZiXun']['data']  = $rs_a;
	$输出数据['ZiXun']['title'] = "校友资讯";
	$输出数据['ZiXun']['more']  = "";

	//校友活动
	$sql 		= "select * from data_xiaoyou_activity where 审核状态='通过' order by id desc limit 3";
	$rs 		= $db->Execute($sql);
	$rs_a 		= $rs->GetArray();
	for($i=0;$i<sizeof($rs_a);$i++) {
		$rs_a[$i]['描述'] 		= strip_tags($rs_a[$i]['描述']);
		$rs_a[$i]['活动日期'] 	= substr($rs_a[$i]['活动日期'],5,11);
		$rs_a[$i]['报名截止'] 	= substr($rs_a[$i]['报名截止'],5,11);
		$rs_a[$i]['图片'] 		= AttachFieldValueToUrl("data_xiaoyou_activity",$rs_a[$i]['id'],'图片','avatar',$rs_a[$i]['图片']);
		$rs_a[$i]['ViewUrl']    = "?action=view_default&id=".EncryptID($rs_a[$i]['id']);
	}
	$输出数据['Activity']['data']   = $rs_a;
	$输出数据['Activity']['title']  = "校友活动";
	$输出数据['Activity']['more']   = "";

	//最近活动
	$最近活动 	= [];
	$最近活动[] = "https://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83eo7eYibA3gLJkRiaaXiaj27wGCXJicvSIyYPfa1cnO0t2tXMibzTFMGg9KicGg8umfbTzgvSlHCKH3LwJHw/132";
	$最近活动[] = "https://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83eo7eYibA3gLJkRiaaXiaj27wGCXJicvSIyYPfa1cnO0t2tXMibzTFMGg9KicGg8umfbTzgvSlHCKH3LwJHw/132";
	$最近活动[] = "https://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83eo7eYibA3gLJkRiaaXiaj27wGCXJicvSIyYPfa1cnO0t2tXMibzTFMGg9KicGg8umfbTzgvSlHCKH3LwJHw/132";
	$最近活动[] = "https://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83eo7eYibA3gLJkRiaaXiaj27wGCXJicvSIyYPfa1cnO0t2tXMibzTFMGg9KicGg8umfbTzgvSlHCKH3LwJHw/132";
	$最近活动[] = "https://thirdwx.qlogo.cn/mmopen/vi_32/DYAIOgq83eo7eYibA3gLJkRiaaXiaj27wGCXJicvSIyYPfa1cnO0t2tXMibzTFMGg9KicGg8umfbTzgvSlHCKH3LwJHw/132";
	$输出数据['LastActivityUser'] = $最近活动;

	//图标列表
	$图标列表 = [];
	$图标列表[] = ['name'=>'校友资讯','image'=>"/images/xiaoyou/zixun.png",'url'=>''];
	$图标列表[] = ['name'=>'各地分会','image'=>"/images/xiaoyou/qunzhushou.png",'url'=>''];
	$图标列表[] = ['name'=>'找校友','image'=>"/images/xiaoyou/zhaoxiaoyou.png",'url'=>''];
	$图标列表[] = ['name'=>'校友活动','image'=>"/images/xiaoyou/huodong.png",'url'=>''];
	$图标列表[] = ['name'=>'校友互助','image'=>"/images/xiaoyou/huzhu.png",'url'=>''];
	$图标列表[] = ['name'=>'校友相册','image'=>"/images/xiaoyou/xiangce.png",'url'=>''];
	$图标列表[] = ['name'=>'我的','image'=>"/images/xiaoyou/wode.png",'url'=>''];
	$图标列表[] = ['name'=>'关于我们','image'=>"/images/xiaoyou/guanyuwomen.png",'url'=>''];
	$输出数据['IconList']   = $图标列表;

	//相册
	$相册列表 = [];
	$相册列表[] = ['name'=>'校园风景','image'=>"data_image.php?DATA=RXZzc1ZTZGRldnZlRWYtZTNEQVNqQ2JiRGU0TGFNZWwyVWFub3U3V3I4SFg0a1Z0YW1MSGNtYkNVUnMxdXZyN2UxcFBJeHNING5XUVBubHQ5akhtNlZSRDBSeHVsRnNXcFFBRzRpZWYxZXRiUzBfczBaWi1tME5sQnVEZ1hWWHlQM2dDSm1RM0N3ZzRKQWVIeTVVcWpiOFZybmNYNDJxWFI0d3pUUHVXeS1Fa1o0OG0zSmxqTjZqMDRWTXR1elZWb0NIaURsQm1oLVRSX0x4VXpfQklPZ3x8OjpJR0VYRkFHbU5LaVZBdWNtOFFYRzhRfHw|",'url'=>''];
	$相册列表[] = ['name'=>'特色建筑','image'=>"data_image.php?DATA=RXZzc1ZTZGRldnZlRWYtZTNEQVNqQ2JiRGU0TGFNZWwyVWFub3U3V3I4SFg0a1Z0YW1MSGNtYkNVUnMxdXZyN2UxcFBJeHNING5XUVBubHQ5akhtNlZSRDBSeHVsRnNXcFFBRzRpZWYxZXRiUzBfczBaWi1tME5sQnVEZ1hWWHlQM2dDSm1RM0N3ZzRKQWVIeTVVcWpiOFZybmNYNDJxWFI0d3pUUHVXeS1Fa1o0OG0zSmxqTjZqMDRWTXR1elZWb0NIaURsQm1oLVRSX0x4VXpfQklPZ3x8OjpJR0VYRkFHbU5LaVZBdWNtOFFYRzhRfHw|",'url'=>''];
	$相册列表[] = ['name'=>'师生风采','image'=>"data_image.php?DATA=RXZzc1ZTZGRldnZlRWYtZTNEQVNqQ2JiRGU0TGFNZWwyVWFub3U3V3I4SFg0a1Z0YW1MSGNtYkNVUnMxdXZyN2UxcFBJeHNING5XUVBubHQ5akhtNlZSRDBSeHVsRnNXcFFBRzRpZWYxZXRiUzBfczBaWi1tME5sQnVEZ1hWWHlQM2dDSm1RM0N3ZzRKQWVIeTVVcWpiOFZybmNYNDJxWFI0d3pUUHVXeS1Fa1o0OG0zSmxqTjZqMDRWTXR1elZWb0NIaURsQm1oLVRSX0x4VXpfQklPZ3x8OjpJR0VYRkFHbU5LaVZBdWNtOFFYRzhRfHw|",'url'=>''];
	$相册列表[] = ['name'=>'风云人物','image'=>"data_image.php?DATA=RXZzc1ZTZGRldnZlRWYtZTNEQVNqQ2JiRGU0TGFNZWwyVWFub3U3V3I4SFg0a1Z0YW1MSGNtYkNVUnMxdXZyN2UxcFBJeHNING5XUVBubHQ5akhtNlZSRDBSeHVsRnNXcFFBRzRpZWYxZXRiUzBfczBaWi1tME5sQnVEZ1hWWHlQM2dDSm1RM0N3ZzRKQWVIeTVVcWpiOFZybmNYNDJxWFI0d3pUUHVXeS1Fa1o0OG0zSmxqTjZqMDRWTXR1elZWb0NIaURsQm1oLVRSX0x4VXpfQklPZ3x8OjpJR0VYRkFHbU5LaVZBdWNtOFFYRzhRfHw|",'url'=>''];
	$相册列表[] = ['name'=>'学术研究','image'=>"data_image.php?DATA=RXZzc1ZTZGRldnZlRWYtZTNEQVNqQ2JiRGU0TGFNZWwyVWFub3U3V3I4SFg0a1Z0YW1MSGNtYkNVUnMxdXZyN2UxcFBJeHNING5XUVBubHQ5akhtNlZSRDBSeHVsRnNXcFFBRzRpZWYxZXRiUzBfczBaWi1tME5sQnVEZ1hWWHlQM2dDSm1RM0N3ZzRKQWVIeTVVcWpiOFZybmNYNDJxWFI0d3pUUHVXeS1Fa1o0OG0zSmxqTjZqMDRWTXR1elZWb0NIaURsQm1oLVRSX0x4VXpfQklPZ3x8OjpJR0VYRkFHbU5LaVZBdWNtOFFYRzhRfHw|",'url'=>''];
	$相册列表[] = ['name'=>'名师名家','image'=>"data_image.php?DATA=RXZzc1ZTZGRldnZlRWYtZTNEQVNqQ2JiRGU0TGFNZWwyVWFub3U3V3I4SFg0a1Z0YW1MSGNtYkNVUnMxdXZyN2UxcFBJeHNING5XUVBubHQ5akhtNlZSRDBSeHVsRnNXcFFBRzRpZWYxZXRiUzBfczBaWi1tME5sQnVEZ1hWWHlQM2dDSm1RM0N3ZzRKQWVIeTVVcWpiOFZybmNYNDJxWFI0d3pUUHVXeS1Fa1o0OG0zSmxqTjZqMDRWTXR1elZWb0NIaURsQm1oLVRSX0x4VXpfQklPZ3x8OjpJR0VYRkFHbU5LaVZBdWNtOFFYRzhRfHw|",'url'=>''];
	$相册列表[] = ['name'=>'异域风情','image'=>"data_image.php?DATA=RXZzc1ZTZGRldnZlRWYtZTNEQVNqQ2JiRGU0TGFNZWwyVWFub3U3V3I4SFg0a1Z0YW1MSGNtYkNVUnMxdXZyN2UxcFBJeHNING5XUVBubHQ5akhtNlZSRDBSeHVsRnNXcFFBRzRpZWYxZXRiUzBfczBaWi1tME5sQnVEZ1hWWHlQM2dDSm1RM0N3ZzRKQWVIeTVVcWpiOFZybmNYNDJxWFI0d3pUUHVXeS1Fa1o0OG0zSmxqTjZqMDRWTXR1elZWb0NIaURsQm1oLVRSX0x4VXpfQklPZ3x8OjpJR0VYRkFHbU5LaVZBdWNtOFFYRzhRfHw|",'url'=>''];

	$输出数据['Album']['data']   	= $相册列表;
	$输出数据['Album']['title']		= "校友相册";
	$输出数据['Album']['more']   	= "/page/InterfaceIniit/360";
	//标题显示
	$输出数据['Header']   = "校友服务平台";
	print_R(json_encode($输出数据));
	exit;
}

?>
