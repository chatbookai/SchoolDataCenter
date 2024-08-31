<?php
header("Content-Type: application/json"); 
require_once('../cors.php');
require_once('../include.inc.php');

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING & ~E_NOTICE);

$_REQUEST['JSON'] = 1; 
global $微信小程序_全局编号;
global $微信小程序_TableName;
global $HTTP_HOST;


global $掌上校园接口访问状态;
global $SYSTEM_FORCE_TO_BIND_USER_STATUS;
global $SYSTEM_CACHE_SECOND_TDFORMICAMPUS;
$学校ID 	= $_REQUEST['LOGIN_SCHOOL_ID'];

$SYSTEM_CACHE_SECOND_TDFORMICAMPUS = 10;

$sql 		= "select * from data_miniprogram where 是否启用='是' and id='$微信小程序_全局编号'";
$rs 		= $db->CacheExecute($SYSTEM_CACHE_SECOND_TDFORMICAMPUS,$sql);
$rs_a 		= $rs->GetArray();
$小程序名称 = $rs_a[0]['小程序名称'];
$TableName 	= $rs_a[0]['TableName'];
$基础构架 	= $rs_a[0]['基础构架'];
$业务表单 	= $rs_a[0]['业务表单'];
$角色列表 	= $rs_a[0]['角色列表'];
$表单列表 	= $基础构架.$业务表单;
$表单列表 	= explode(',',$表单列表);
$缓存周期	= $rs_a[0]['缓存周期'];
$开放对像	= $rs_a[0]['开放对像'];
switch($开放对像)				{
	case "关联用户名或学号以后才能使用":
		$SYSTEM_FORCE_TO_BIND_USER_STATUS = 1;
		break;
	case "等需要时再来关联用户名或学号":
		$SYSTEM_FORCE_TO_BIND_USER_STATUS = 2;
		break;
	case "任何一个模块都不需要关联用户名":
		$SYSTEM_FORCE_TO_BIND_USER_STATUS = 3;
		break;
}
$小程序首页面介绍	= $rs_a[0]['小程序首页面介绍'];
$小程序认证页介绍	= $rs_a[0]['小程序认证页介绍'];
$用户使用协议		= $rs_a[0]['用户使用协议'];
$使用前说明			= $rs_a[0]['使用前说明'];
$服务内容			= $rs_a[0]['服务内容'];


$MainImageList	= array("/images/wechat/logo_18.png","/images/wechat/logo_icampus.png");//三张滚动截图


if($_GET['action']=='update')							{
	$RESULT = [];
	$RESULT['status']			    = "OK";
    $RESULT['version']			    = date("Ymd_His",time()+$缓存周期);
	$RESULT['学校ID']			    = $学校ID;
	$RESULT['小程序名称']		    = $小程序名称;
	$RESULT['APP_SELF_NAME']	    = $小程序名称;
	$RESULT['小程序首页面介绍']	    = $小程序首页面介绍;
	$RESULT['小程序认证页介绍']	    = $小程序认证页介绍;
	$RESULT['小程序使用协议']	    = $用户使用协议;
	$RESULT['小程序服务内容']	    = $服务内容;
	$RESULT['小程序使用前说明']	    = $使用前说明;
	$RESULT['小程序LOGO']		    = "/images/wechat/logo.png";	
	print_R(json_encode($RESULT));
    exit;
}


//私有部署
if($_GET['action']=='maindata'&&$SYSTEM_IS_CLOUD==0)															{
	
	$SYSTEM_APPSTORE_ID = $_REQUEST['SYSTEM_APPSTORE_ID'];
	$得到用户所属的FormId列表X = array();
	$SqlList = [];
	
	//第一步 得到权限
	global $systemprivate_rsa;
	$LOGIN_USER_PRIV_OTHER	= $_SESSION['LOGIN_USER_PRIV_OTHER'];
	$LOGIN_USER_PRIV		= $_SESSION['LOGIN_USER_PRIV'];
	$LOGIN_USER_PRIV_STR_ARRAY = explode(',',$LOGIN_USER_PRIV.",".$LOGIN_USER_PRIV_OTHER);
	for($i=0;$i<sizeof($LOGIN_USER_PRIV_STR_ARRAY);$i++)		{
		//$LOGIN_USER_PRIV_TEXT_ARRAY[] = returntablefield("td_edu.systemprivate","ID",$LOGIN_USER_PRIV_STR_ARRAY[$i],"CONTENT");
		$LOGIN_USER_PRIV_TEXT_ARRAY[] = $systemprivate_rsa[$LOGIN_USER_PRIV_STR_ARRAY[$i]]['CONTENT'];
	}
	$LOGIN_USER_PRIV_TEXT  = join(',',$LOGIN_USER_PRIV_TEXT_ARRAY);
	$LOGIN_USER_PRIV_TEXT .= "...";	
	$_SESSION['LOGIN_USER_PRIV_TEXT'] = $LOGIN_USER_PRIV_TEXT;
	
	//第二步 基本信息
	$sql	= "SELECT 基础架构,业务表单,小程序名称 FROM data_miniprogram WHERE id='$SYSTEM_APPSTORE_ID'";
	$SqlList[] = $sql;
	$rs		= $db->CacheExecute($SYSTEM_CACHE_SECOND_TDFORMICAMPUS,$sql);
	$rs_a 	= $rs->GetArray();
	$基础设置 = $业务表单 = "";
    $小程序名称	 					= $rs_a[0]['小程序名称'];
    $业务表单MAP[$小程序名称]	 	 = $rs_a[0]['业务表单'];
    $业务表单						= $rs_a[0]['业务表单'];
    $基础架构						= $rs_a[0]['基础架构'];
    $业务表单TEMP 					= explode(',',$rs_a[0]['业务表单']);
    for($RR=0;$RR<sizeof($业务表单TEMP);$RR++)				{
        if($业务表单TEMP[$RR]!="")		{
            $业务表单TEMPARRAY[$业务表单TEMP[$RR]] = $小程序名称;
        }
    }
	$业务表单ARRAY 	= explode(',',$业务表单);
	$所有表单ARRAY 	= explode(',',$业务表单);
	
	//print_R($业务表单TEMPARRAY);exit;
	$sql 			= "select id,FullName,TableName from form_formname where id in ('".join("','",$所有表单ARRAY)."')";
	$SqlList[] = $sql;
	$rs				= $db->CacheExecute($SYSTEM_CACHE_SECOND_TDFORMICAMPUS,$sql);
	$rs_a 			= $rs->GetArray();
	$表单编号ARRAY 	= array();	
	for($R=0;$R<sizeof($rs_a);$R++)						{
		$表单编号	 						 = $rs_a[$R]['id'];
		$表单MAP[$表单编号] 				 = $rs_a[$R];
		$FormIdLIST[]						= $表单编号;
	}

    //当前用户的权限
    CheckAuthUserLoginStatus();
    $USER_ID    	= $GLOBAL_USER->USER_ID;
    $PRIV_NAME    	= $GLOBAL_USER->PRIV_NAME;
	if($PRIV_NAME=="校友"||$PRIV_NAME=="学生")    {
		//Menu From Database
		$sql    = "select * from data_menuone order by SortNumber asc, MenuOneName asc";
		$rsf    = $db->CacheExecute(180,$sql);
		$MenuOneRSA  = $rsf->GetArray();
	
		$sql 		= "select * from form_formflow where FormId in ('".join("','",$所有表单ARRAY)."') and FaceTo='Student' and MobileEnd='Yes' order by FormId asc,Step asc";
		$SqlList[] = $sql;
		$rs			= $db->CacheExecute($SYSTEM_CACHE_SECOND_TDFORMICAMPUS,$sql);
		$rs_a 		= $rs->GetArray();
		$FlowIdMapArray = [];
		for($R=0;$R<sizeof($rs_a);$R++)				{
			$FlowIdMapArray[$rs_a[$R]['id']]  = $rs_a[$R];
		}	
		$所有菜单 	 = [];
		$sql    	= "select * from data_menutwo where FaceTo='Student' order by MenuOneName asc,SortNumber asc";
	}
	else {
		//教职工	
		$PRIV_NAME		= "教职工";
		$RS         	= returntablefield("data_user","USER_ID",$USER_ID,"USER_PRIV,USER_PRIV_OTHER");
		if($RS['USER_PRIV_OTHER']!="") {
			$USER_PRIV_Array = explode(',',$RS['USER_PRIV'].",".$RS['USER_PRIV_OTHER']);
		}
		else {		
			$USER_PRIV_Array = explode(',',$RS['USER_PRIV']);
		}
		$sql        = "select * from data_role where id in ('".join("','",$USER_PRIV_Array)."')";
		$SqlList[] = $sql;
		$rsf        = $db->CacheExecute(180,$sql);
		$RoleRSA    = $rsf->GetArray();
		$RoleArray  = "";
		foreach($RoleRSA as $Item)  {
			$RoleArray .= $Item['content'].",";
		}
		$RoleArray = explode(',',$RoleArray);
		$RoleArray = array_values($RoleArray);
	
		//Menu From Database
		$sql    = "select * from data_menuone order by SortNumber asc, MenuOneName asc";
		$rsf    = $db->CacheExecute(180,$sql);
		$MenuOneRSA  = $rsf->GetArray();
	
		$sql 		= "select * from form_formflow where FormId in ('".join("','",$所有表单ARRAY)."') and FaceTo='AuthUser' and MobileEnd='Yes' order by FormId asc,Step asc";
		$SqlList[] = $sql;
		$rs			= $db->CacheExecute($SYSTEM_CACHE_SECOND_TDFORMICAMPUS,$sql);
		$rs_a 		= $rs->GetArray();
		$FlowIdMapArray = [];
		for($R=0;$R<sizeof($rs_a);$R++)				{
			$FlowIdMapArray[$rs_a[$R]['id']]  = $rs_a[$R];
		}	
		$所有菜单 	 = [];
		//$sql    	= "select * from data_menutwo where FaceTo='AnonymousUser' order by MenuOneName asc,SortNumber asc";
		$sql    	= "select * from data_menutwo where FaceTo='AuthUser' and id in ('".join("','",$RoleArray)."') order by MenuOneName asc,SortNumber asc";
	}
	$SqlList[] 	= $sql;
    $rsf    	= $db->CacheExecute(180,$sql);
    $MenuTwoRSA  = $rsf->GetArray();
    $MenuTwoArray = [];
    $TabMap = [];
    foreach($MenuTwoRSA as $Item)  {
        if($Item['MenuTab']=="Yes"||$Item['MenuTab']=="是") {
            $TabMap[$Item['MenuOneName']][$Item['MenuTwoName']] = "Tab";
        }
        if($Item['MenuThreeName']!="")   {
            $MenuTwoArray[$Item['MenuOneName']][$Item['MenuTwoName']][] = $Item;
        }
        else { 
            $MenuTwoArray[$Item['MenuOneName']]['SystemMenuTwo_'.$Item['id']][] = $Item;
        }
        if(isset($FlowIdMapArray[$Item['FlowId']])) {
            $RS = $FlowIdMapArray[$Item['FlowId']];
            $FlowId	 					    = $RS['id'];
            $FlowName	 					= $RS['FlowName'];
            $FormId	 						= $RS['FormId'];
            $Setting                        = unserialize(base64_decode($RS['Setting']));
            $FormName						= $表单MAP[$FormId]['FullName'];
            $TableName						= $表单MAP[$FormId]['TableName'];
            $GroupName 						= $业务表单TEMPARRAY[$FormId];
            $Step	 						= $RS['Step'];
            $RS['URL']				        = "/apps/apps_".$Item['id'].".php";
            $RS['Setting']            = "";
            $RS['TableName']          = $TableName;
            $RS['GroupName']          = $GroupName;
            $RS['FlowId']             = $FlowId;
            $RS['MenuId']             = $Item['id'];
            $RS['Icon']               = "/images/wechatIcon/".$Setting['MobileEndIconImage'].".png";
            $所有菜单[$GroupName][$FlowId]     = $RS;
        }
    }

    $MainDataList = [];
	$COUNTER 	= 0;
	$分组KEYS 	= array_keys($所有菜单);
	//把[基础数据]这个值放到最下面-开始
	$分组KEYSNEW= array();
	$通知公告_是否启用 = 0;
	$基础数据_是否启用 = 0;	
	if(in_array("通知公告",$分组KEYS))		{
		$分组KEYSNEW[] 	= "通知公告";
	}
	for($i=0;$i<count($分组KEYS);$i++)			{
		$分组KEY 				= $分组KEYS[$i];
		if($分组KEY!="基础数据"&&$分组KEY!="通知公告")		{
			$分组KEYSNEW[] 		= $分组KEY;
		}
	}
	if(in_array("基础数据",$分组KEYS))		{
		$分组KEYSNEW[] 	= "基础数据";
	}
	$分组KEYS		= $分组KEYSNEW;
	//把[基础数据]这个值放到最下面-结束
	for($i=0;$i<count($分组KEYS);$i++)			{
		$分组KEY 				= $分组KEYS[$i];
		$当前分组下面的菜单 	 = $所有菜单[$分组KEY];
		$当前分组下面的菜单KEYS  = array_keys($当前分组下面的菜单);
		for($iR=0;$iR<count($当前分组下面的菜单KEYS);$iR++)			{
			$当前分组下面的菜单KEY 	 = $当前分组下面的菜单KEYS[$iR];
			$Element				= array();
			$Element['id']			= $COUNTER;
			$Element['Name']		= $当前分组下面的菜单[$当前分组下面的菜单KEY]['FlowName'];
			$Element['GroupName']	= $分组KEY;
			$Element['BackEndApi']	= $当前分组下面的菜单[$当前分组下面的菜单KEY]['URL'];
            $Element['PageType']	= $当前分组下面的菜单[$当前分组下面的菜单KEY]['PageType'];
			$Element['PathUrl']		= $当前分组下面的菜单[$当前分组下面的菜单KEY]['PageType']."_".$COUNTER."_".$COUNTER;
			$Element['ItemId']		= $COUNTER;
			$Element['Number']		= "0";//图标的右上角提示信息
			$Element['Icon']		= $当前分组下面的菜单[$当前分组下面的菜单KEY]['Icon'];
			$Element['FormId']		= $当前分组下面的菜单[$当前分组下面的菜单KEY]['FormId'];
			$Element['FlowId']		= $当前分组下面的菜单[$当前分组下面的菜单KEY]['id'];
			$Element['ForceLogin']	= 1;//图标的右上角提示信息
			$Element['UserDefineMobilePageUrl']	= strval($当前分组下面的菜单[$当前分组下面的菜单KEY]['UserDefineMobilePageUrl']);
			
			//通知公告的前面两个菜单项目,直接在微信小程序的代码中写死,需要在"校园"图标中显示出来.
			$MainDataList[$分组KEY][]       = $Element;
            $MainDataMap[$COUNTER]          = $Element;
			$COUNTER ++;
		}
	}

	$RESULT['MainDataGroup']	= $分组KEYS;                    //菜单分组
	$RESULT['MainDataList']		= $MainDataList;                //主要数据区
	$RESULT['MainDataMap']	    = $MainDataMap;                 //PAGEID=>MENU
	$RESULT['MainImageList']	= $MainImageList;               //三张滚动截图
	$RESULT['IsSearch']			= false;//是否显示搜索,目前需要关闭
	$RESULT['IsUserAvator']		= false;//是否显示用户头像
	$RESULT['UserType']		    = $PRIV_NAME;
	$RESULT['IndexLogo']		= array();
	$RESULT['SYSTEM_FORCE_TO_BIND_USER_STATUS']	= $SYSTEM_FORCE_TO_BIND_USER_STATUS;
	//$RESULT['_REQUEST']			= $_REQUEST;
	//$RESULT['_POST']			    = $_POST;
	$RESULT['所有菜单']			    	= $所有菜单;
	$RESULT['FlowIdMapArray']			= $FlowIdMapArray;
	$RESULT['MenuTwoRSA']				= $MenuTwoRSA;
	$RESULT['SqlList']					= $SqlList;
	
	print_R(json_encode($RESULT));
	exit;
}

?>