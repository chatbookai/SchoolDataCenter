<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');

CheckAuthUserLoginStatus();

global $GLOBAL_LANGUAGE;

//编辑页面时的启用字段列表
$allFieldsEdit = [];
$allFieldsEdit[] = ['name' => 'USER_NAME', 'show'=>true, 'type'=>'readonly', 'label' => __('USER_NAME'), 'value' => $GLOBAL_USER->USER_NAME, 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => true]];
$allFieldsEdit[] = ['name' => 'Old_Password', 'show'=>true, 'type'=>'password', 'label' => __('Old_Password'), 'value' => '', 'placeholder' => '', 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false]];
$allFieldsEdit[] = ['name' => 'New_Password', 'show'=>true, 'type'=>'comfirmpassword', 'label' => __('New_Password'), 'value' => '', 'placeholder' => __('New_Password'), 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false,'format'=>'passwordweak','invalidtext'=>__('PasswordMustIncludeNumberAndLetter')]];
$allFieldsEdit[] = ['name' => 'Confirm_Password', 'show'=>true, 'type'=>'comfirmpassword', 'label' => __('Confirm_Password'), 'value' => '', 'placeholder' => __('Confirm_Password'), 'helptext' => '', 'rules' => ['required' => true,'xs'=>12, 'sm'=>12,'disabled' => false,'format'=>'passwordweak','invalidtext'=>__('PasswordMustIncludeNumberAndLetter')]];

foreach($allFieldsEdit as $ITEM) {
    $defaultValues[$ITEM['name']] = $ITEM['value'];
}

if($_GET['action']=="edit_default")  {
    $RS     = [];
    $RS['status'] = "OK";
    $RS['data'] = [];
    $RS['msg'] = __("Get Data Success");
    print json_encode($RS);
    exit;  
}

$USER_ID = $GLOBAL_USER->USER_ID;

if($_GET['action']=="edit_default_data"&&$_GET['id']!=""&&$GLOBAL_USER->type=="User")                       {
    $NewArray           = [];
    $Old_Password       = $_POST['Old_Password'];
    $New_Password       = $_POST['New_Password'];
    $Confirm_Password   = $_POST['Confirm_Password'];
    if($New_Password!=$Confirm_Password || $Confirm_Password=='')  {
        $RS = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("The two new passwords you entered do not match");
        print json_encode($RS);
        exit; 
    }
    if($New_Password==$Old_Password)  {
        $RS = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("The new password cannot be the same as the old password");
        print json_encode($RS);
        exit; 
    }
    $sql    = "select * from data_user where USER_ID='".$USER_ID."'";
    $rs		= $db->Execute($sql);
    $UserInfo = $rs->fields;
    if($UserInfo['USER_ID']=="")  {  
        $RS = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("USER NOT EXIST OR PASSWORD IS ERROR!");
        $RS['sql']      = $sql;
        $RS['_POST']    = $_POST;
        SystemLogRecord("Password", __('USER NOT EXIST'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
        print json_encode($RS);
        exit;
    }
    //Check Password
    $PASSWORD_IN_DB         = $UserInfo['PASSWORD'];
    if($Old_Password!=""&&$PASSWORD_IN_DB!=""&&password_check($Old_Password,$PASSWORD_IN_DB))  {
        //password is correct
        $New_Password_Crypt = password_make($New_Password);
        $sql                = "update data_user set PASSWORD='$New_Password_Crypt' where USER_ID = '$USER_ID'";
        $db->Execute($sql);
        $RS = [];
        $RS['status']   = "OK";
        $RS['msg']      = __("Change Password Success");
        $RS['sql']      = $sql;
        $RS['_POST']    = $_POST;
        SystemLogRecord("Password", __('Change Password Success'), __("Change Password Success"),$USER_ID);
        print json_encode($RS);
        exit;
    }
    else {
        $RS = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("OLD PASSWORD IS ERROR");
        $RS['sql']      = $sql;
        $RS['_POST']    = $_POST;
        SystemLogRecord("Password", __('PASSWORD IS ERROR'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
        print json_encode($RS);
        exit;
    }
}

if($_GET['action']=="edit_default_data"&&$_GET['id']!=""&&$GLOBAL_USER->type=="Student")                       {
    $NewArray           = [];
    $Old_Password       = $_POST['Old_Password'];
    $New_Password       = $_POST['New_Password'];
    $Confirm_Password   = $_POST['Confirm_Password'];
    if($New_Password!=$Confirm_Password || $Confirm_Password=='')  {
        $RS = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("The two new passwords you entered do not match");
        print json_encode($RS);
        exit; 
    }
    if($New_Password==$Old_Password)  {
        $RS = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("The new password cannot be the same as the old password");
        print json_encode($RS);
        exit; 
    }
    $sql    = "select * from data_student where 学号='".$USER_ID."'";
    $rs		= $db->Execute($sql);
    $UserInfo = $rs->fields;
    if($UserInfo['学号']=="")  {  
        $RS = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("USER NOT EXIST OR PASSWORD IS ERROR!");
        $RS['sql']      = $sql;
        $RS['_POST']    = $_POST;
        SystemLogRecord("Password", __('USER NOT EXIST'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
        print json_encode($RS);
        exit;
    }
    //Check Password
    $PASSWORD_IN_DB         = $UserInfo['密码'];
    if($Old_Password!=""&&$PASSWORD_IN_DB!=""&&password_check($Old_Password,$PASSWORD_IN_DB))  {
        //password is correct
        $New_Password_Crypt = password_make($New_Password);
        $sql                = "update data_student set 密码='$New_Password_Crypt' where 学号 = '$USER_ID'";
        $db->Execute($sql);
        $RS = [];
        $RS['status']   = "OK";
        $RS['msg']      = __("Change Password Success");
        $RS['sql']      = $sql;
        $RS['_POST']    = $_POST;
        SystemLogRecord("Password", __('Change Password Success'), __("Change Password Success"),$USER_ID);
        print json_encode($RS);
        exit;
    }
    else {
        $RS = [];
        $RS['status']   = "ERROR";
        $RS['msg']      = __("OLD PASSWORD IS ERROR");
        $RS['sql']      = $sql;
        $RS['_POST']    = $_POST;
        SystemLogRecord("Password", __('PASSWORD IS ERROR'), __("USER NOT EXIST OR PASSWORD IS ERROR!"),$USER_ID);
        print json_encode($RS);
        exit;
    }
}


$RS['init_default']['button_search']    = __("Search");
$RS['init_default']['button_add']       = __("Add");
$RS['init_default']['columns']          = [];
$RS['init_default']['columnsactions']   = [];

$RS['init_action']['action']        = "edit_default";
$RS['init_action']['id']            = 'USER_ID'; //NOT USE THIS VALUE IN FRONT END

$RS['init_default']['data'] = [];
$RS['init_default']['dataGridLanguageCode']  = $GLOBAL_LANGUAGE;


$RS['edit_default']['allFields']['Default']  = $allFieldsEdit;
$RS['edit_default']['allFieldsMode']  = [['value'=>"Default", 'label'=>__("")]];
$RS['edit_default']['defaultValues']  = $defaultValues;
$RS['edit_default']['dialogContentHeight']  = "90%";
$RS['edit_default']['submitaction']  = "edit_default_data";
$RS['edit_default']['submittext']    = __("ChangePassword");
$RS['edit_default']['canceltext']    = __("Cancel");
$RS['edit_default']['titletext']    = __("Change Your Password");
$RS['edit_default']['titlememo']    = __("Minimum 6 characters long - the more, the better");
$RS['edit_default']['tablewidth']   = 550;
$RS['edit_default']['submitloading']    = __("SubmitLoading");
$RS['edit_default']['loading']          = __("Loading");

$RS['export_default'] = [];
$RS['import_default'] = [];

$RS['_GET']     = $_GET;
$RS['_POST']    = $_POST;
print_R(json_encode($RS, true));



