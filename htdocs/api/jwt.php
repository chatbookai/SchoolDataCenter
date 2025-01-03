<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('cors.php');
require_once('include.inc.php');

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */

if($_GET['action']=="login")                {
    JWT::$leeway    = $NEXT_PUBLIC_JWT_EXPIRATION;
    $payload        = file_get_contents('php://input');
    $_POST          = json_decode($payload,true);
    $Data           = $_POST['Data'];
    $Data           = decodeBase58(decodeBase58($Data));
    $_POST          = json_decode($Data, true);
    $EMAIL          = ForSqlInjection($_POST['email']);
    $USER_ID        = ForSqlInjection($_POST['username']);
    $password       = ForSqlInjection($_POST['password']);
    $rememberMe     = ForSqlInjection($_POST['rememberMe']);
    $UserType       = ForSqlInjection($_POST['UserType']);
    if($USER_ID!="")   {
        if($EMAIL!="")   {
            $sql = "select * from data_user where EMAIL='$EMAIL'";
        }
        else {
            $sql = "select * from data_user where USER_ID='$USER_ID'";
        }
        $rs		= $db->Execute($sql);
        $UserInfo = $rs->fields;
        if($UserInfo['USER_ID']==""&&$UserType!="校友")  {
            $sql    = "select * from data_student where 学号='$USER_ID'";
            $rs		= $db->Execute($sql);
            $StudentInfo = $rs->fields;
            if($StudentInfo['学号']=="")  {
                $RS = [];
                $RS['status']   = "ERROR";
                $RS['msg']      = $RS['email']    = __("USER NOT EXIST OR PASSWORD IS ERROR!");
                //$RS['sql']      = $sql;
                //$RS['_POST']    = $_POST;
                SystemLogRecord("Login", __('USER NOT EXIST'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
                print json_encode($RS);
                exit;
            }
            $PASSWORD_IN_DB         = $StudentInfo['密码'];
            if($password!=""&&$PASSWORD_IN_DB!=""&&password_check($password,$PASSWORD_IN_DB))  {
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
                $userData['ExpireTime'] = time() + (3 * 60 * 30);
                $accessToken            = EncryptID(JWT::encode($userData, $NEXT_PUBLIC_JWT_SECRET, 'HS256'));
                $RS['accessToken']      = $accessToken;
                $RS['accessKey']        = GetAccessKey($userData['USER_ID']);
                $RS['userData']         = $userData;
                //形成个人信息展示页面的数据列表
                $USER_PROFILE 	    = array();
                $USER_PROFILE[] 	= array("左边"=>"用户类型","右边"=>"学生");
                $USER_PROFILE[] 	= array("左边"=>"学号","右边"=>$userData['USER_ID']);
                $USER_PROFILE[] 	= array("左边"=>"姓名","右边"=>$userData['USER_NAME']);
                $USER_PROFILE[] 	= array("左边"=>"班级","右边"=>$userData['班级']);
                $USER_PROFILE[] 	= array("左边"=>"专业","右边"=>$userData['专业']);
                $USER_PROFILE[] 	= array("左边"=>"系部","右边"=>$userData['系部']);
                $RS['USER_PROFILE'] = $USER_PROFILE;
                print_r(json_encode($RS));
                SystemLogRecord("Login", __("Success"), __("Success"),$USER_ID);
                exit;
            }
            $RS = [];
            $RS['status']   = "ERROR";
            $RS['msg']      = $RS['email']    = __("USER NOT EXIST OR PASSWORD IS ERROR!");
            //$RS['sql']      = $sql;
            //$RS['_POST']    = $_POST;
            SystemLogRecord("Login", __('PASSWORD IS ERROR'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
            print json_encode($RS);
            exit;
        }
        if($UserInfo['USER_ID']==""&&$UserType=="校友")  {
            $sql    = "select * from data_xiaoyou_member where 学生姓名='$USER_ID'";
            $rs		= $db->Execute($sql);
            $StudentInfo = $rs->fields;
            if($StudentInfo['学生姓名']=="")  {
                $RS = [];
                $RS['status']   = "ERROR";
                $RS['msg']      = $RS['email']    = __("USER NOT EXIST OR PASSWORD IS ERROR!");
                //$RS['sql']      = $sql;
                //$RS['_POST']    = $_POST;
                SystemLogRecord("Login", __('USER NOT EXIST'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
                print json_encode($RS);
                exit;
            }
            $PASSWORD_IN_DB         = $StudentInfo['身份证件号'];
            if($password!=""&&$PASSWORD_IN_DB!=""&&$password==$PASSWORD_IN_DB)  {
                //Reform userData
                $userData = [];
                $userData['id']         = $StudentInfo['id'];
                $userData['USER_ID']    = $StudentInfo['学生学号'];
                $userData['USER_NAME']  = $StudentInfo['学生姓名'];
                $userData['学号']       = $StudentInfo['学生学号'];
                $userData['姓名']       = $StudentInfo['学生姓名'];
                $userData['班级']       = $StudentInfo['班级'];
                $userData['专业']       = $StudentInfo['专业'];
                $userData['系部']       = $StudentInfo['院系'];
                $userData['PRIV_NAME']  = "校友";
                $userData['avatar']     = '/images/avatars/1.png';
                $userData['username']   = $StudentInfo['学生学号'];
                $userData['role']       = "校友";
                $userData['type']       = "Schoolmate";
                $userData['ExpireTime'] = time() + (3 * 60 * 30);
                $accessToken            = EncryptID(JWT::encode($userData, $NEXT_PUBLIC_JWT_SECRET, 'HS256'));
                $RS['accessToken']      = $accessToken;
                $RS['accessKey']        = GetAccessKey($userData['USER_ID']);
                $RS['userData']         = $userData;
                $RS['status']           = "OK";
                $RS['msg']              = __("Success");
                //形成个人信息展示页面的数据列表
                $USER_PROFILE 	    = array();
                $USER_PROFILE[] 	= array("左边"=>"用户类型","右边"=>"校友");
                $USER_PROFILE[] 	= array("左边"=>"学号","右边"=>$userData['USER_ID']);
                $USER_PROFILE[] 	= array("左边"=>"姓名","右边"=>$userData['USER_NAME']);
                $USER_PROFILE[] 	= array("左边"=>"班级","右边"=>$userData['班级']);
                $USER_PROFILE[] 	= array("左边"=>"专业","右边"=>$userData['专业']);
                $USER_PROFILE[] 	= array("左边"=>"系部","右边"=>$userData['系部']);
                $RS['USER_PROFILE'] = $USER_PROFILE;
                print_r(json_encode($RS));
                SystemLogRecord("Login", __("Success"), __("Success"),$USER_ID);
                $LOGIN_USER_OPENID = $_POST['LOGIN_USER_OPENID'];
                if($LOGIN_USER_OPENID!="")   {
                    $sql = "update data_xiaoyou_member set OPENID='$LOGIN_USER_OPENID' where id='".$StudentInfo['id']."'";
                    $db->Execute($sql);
                }
                exit;
            }
            $RS = [];
            $RS['status']   = "ERROR";
            $RS['msg']      = $RS['email']    = __("USER NOT EXIST OR PASSWORD IS ERROR!");
            //$RS['sql']      = $sql;
            //$RS['_POST']    = $_POST;
            SystemLogRecord("Login", __('PASSWORD IS ERROR'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
            print json_encode($RS);
            exit;
        }
        $PASSWORD_IN_DB         = $UserInfo['PASSWORD'];
        if($password!=""&&$PASSWORD_IN_DB!=""&&password_check($password,$PASSWORD_IN_DB))  {
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
            $userData['ExpireTime'] = time() + (3 * 60 * 30);
            $accessToken            = EncryptID(JWT::encode($userData, $NEXT_PUBLIC_JWT_SECRET, 'HS256'));
            $RS['accessToken']      = $accessToken;
            $RS['accessKey']        = GetAccessKey($userData['USER_ID']);
            $RS['userData']         = $userData;

            $GO_SYSTEM                          = [];
            $userInfoX                          = [];
            $userInfoX['userToken']             = $accessToken;
            $userInfoX['tokenName']             = "satoken";
            $userInfoX['userId']                = $UserInfo['id'];
            $userInfoX['userName']              = $UserInfo['USER_ID'];
            $userInfoX['nickName']              = $UserInfo['USER_NAME'];
            $userInfoX['t']                     = "function H(...q){return $(re=>Reflect.apply(er.translate,null,[re,...q]),()=>er.parseTranslateArgs(...q),\"translate\",re=>Reflect.apply(re.t,re,[...q]),re=>re,re=>Re.isString(re))}";
            $GO_SYSTEM['userInfo']              = $userInfoX;
            $GO_SYSTEM['fetchInfo']['OSSUrl']   = "/api/goview/bucket/";
            $RS['GO_SYSTEM']  = $GO_SYSTEM;

            $RS['status']     = "OK";
            //形成个人信息展示页面的数据列表
            $USER_PROFILE 	  = array();
            $USER_PROFILE[] 	= array("左边"=>"用户类型","右边"=>"教职工");
            $USER_PROFILE[] 	= array("左边"=>"用户名","右边"=>$userData['USER_ID']);
            $USER_PROFILE[] 	= array("左边"=>"姓名","右边"=>$userData['USER_NAME']);
            $USER_PROFILE[] 	= array("左边"=>"部门","右边"=>$userData['DEPT_NAME']);
            $USER_PROFILE[] 	= array("左边"=>"角色","右边"=>$userData['PRIV_NAME']);
            $RS['USER_PROFILE'] = $USER_PROFILE;
            SystemLogRecord("Login", __("Success"), __("Success"),$USER_ID);
            print_r(json_encode($RS));
            exit;
        }
        else {
            $RS = [];
            $RS['status']   = "ERROR";
            $RS['msg']      = $RS['email']    = __("USER NOT EXIST OR PASSWORD IS ERROR!");
            //$RS['sql']      = $sql;
            //$RS['_POST']    = $_POST;
            print json_encode($RS);
            SystemLogRecord("Login", __('PASSWORD IS ERROR'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
            exit;
        }
    }
    else {
        $RS = [];
        $RS['status']   = "ERROR";
        //$RS['_POST']    = $_POST;
        print json_encode($RS);
        SystemLogRecord("Login", __('USER NOT EXIST'), __("USER NOT EXIST"),"");
        exit;
    }
}

if($_GET['action']=="refresh")                {
    $CheckAuthUserLoginStatus               = CheckAuthUserLoginStatus();
    $CheckAuthUserLoginStatus->ExpireTime   = time() + (3 * 60 * 30);
    $accessToken                = EncryptID(JWT::encode((array) $CheckAuthUserLoginStatus, $NEXT_PUBLIC_JWT_SECRET, 'HS256'));
    $RS['status']               = 'ok';
    $RS['accessToken']          = $accessToken;
    $RS['accessKey']            = GetAccessKey($CheckAuthUserLoginStatus->USER_ID);
    $RS['userData']             = (array) $CheckAuthUserLoginStatus;
    print_r(json_encode($RS));
    exit;
}

if($_GET['action']=="Logout")                {
    $USER_ID        = ForSqlInjection($_GET['USER_ID']);
    $RS             = [];
    $RS['status']   = "ERROR";
    //$RS['_POST']    = $_POST;
    print json_encode($RS);
    SystemLogRecord("Logout", __('Logout'), __("USER NOT EXIST"),$USER_ID);
    exit;
}

function decodeBase58($base58) {
    $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
    $base = strlen($alphabet);
    $indexes = array_flip(str_split($alphabet));
    $chars = str_split($base58);
    $decimal = $indexes[$chars[0]];
    for($i = 1, $l = count($chars); $i < $l; $i++) {
        $decimal = bcmul($decimal, $base);
        $decimal = bcadd($decimal, $indexes[$chars[$i]]);
    }
    $output = '';
    while($decimal > 0) {
        $byte = (int)bcmod($decimal, 256);
        $output = pack('C', $byte).$output;
        $decimal = bcdiv($decimal, 256, 0);
    }
    foreach($chars as $char) {
        if($indexes[$char] === 0) {
            $output = "\x00".$output;
            continue;
        }
        break;
    }
    return $output;
}

?>
