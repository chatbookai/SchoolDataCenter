<?php
require_once("include.inc.php");

$_POST['数据库地址']    = "localhost:3386";
$_POST['数据库用户名']   = "root";
$_POST['数据库密码']     = "6jF0^#12x6^S2zQ#t";
plugin_data_datasource_2_edit_default_configsetting_data($id);

function plugin_data_datasource_2_edit_default_configsetting_data($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    $数据库地址     = $_POST['数据库地址'];
    $数据库用户名   = $_POST['数据库用户名'];
    $数据库密码     = $_POST['数据库密码'];
    $数据库名称     = "TD_OA";
    $db_remote = NewADOConnection($DB_TYPE);
    $db_remote->connect($数据库地址, $数据库用户名, $数据库密码, $数据库名称);
    $db_remote->Execute("Set names utf8;");
    $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
    if($db_remote->databaseName!="" && $db_remote->databaseName==$数据库名称) {
        $sql = "select * from td_user where LIMIT_LOGIN=0 and NOT_LOGIN=0";
        $rsR = $db_remote->Execute($sql);
        $rs_a = $rsR->GetArray();
        for($i=0;$i<sizeof($rs_a);$i++) {
            $Element = [];
            $Element['USER_ID']         = $rs_a[$i]['USER_ID'];
            $Element['USER_NAME']       = $rs_a[$i]['USER_NAME'];
            $Element['DEPT_ID']         = $rs_a[$i]['DEPT_ID'];
            $Element['USER_NO']         = $rs_a[$i]['USER_NO'];
            $Element['BIRTHDAY']        = $rs_a[$i]['BIRTHDAY'];
            $Element['EMAIL']           = $rs_a[$i]['EMAIL'];
            $Element['USER_PRIV_OTHER']         = $rs_a[$i]['USER_PRIV_OTHER'];
            //$Element['DEPT_ID_OTHER']           = $rs_a[$i]['DEPT_ID_OTHER'];
            $Element['TEL_NO_DEPT']             = $rs_a[$i]['TEL_NO_DEPT'];
            $Element['USER_PRIV']               = $rs_a[$i]['USER_PRIV'];
            $Element['USER_PRIV_OTHER']         = $rs_a[$i]['USER_PRIV_OTHER'];
            $Element['USER_PRIV']               = $rs_a[$i]['USER_PRIV'];
            $RS = InsertOrUpdateTableByArray($TableName="data_user",$Element,"USER_ID",0,"Insert");  
        }
        $sql = "select * from user_priv";
        $rs = $db_remote->Execute($sql);
        $rs_a = $rs->GetArray();
        for($i=0;$i<sizeof($rs_a);$i++) {
            $Element = [];
            $Element['id']          = $rs_a[$i]['USER_PRIV'];
            $Element['name']        = $rs_a[$i]['PRIV_NAME'];
            $Element['content']     = $rs_a[$i]['FUNC_ID_STR'];
            $Element['CreateTime']  = date("Y-m-d H:i:s");
            $Element['Creator']     = "admin";
            $RS = InsertOrUpdateTableByArray($TableName="data_role",$Element,"USER_ID",0,"Insert");  
        }
        $sql = "select * from department";
        $rs = $db_remote->Execute($sql);
        $rs_a = $rs->GetArray();
        for($i=0;$i<sizeof($rs_a);$i++) {
            $Element = [];
            $Element['id']         = $rs_a[$i]['DEPT_ID'];
            $Element['DEPT_NAME']       = $rs_a[$i]['DEPT_NAME'];
            $Element['TEL_NO']          = $rs_a[$i]['TEL_NO'];
            $Element['DEPT_NO']          = $rs_a[$i]['DEPT_NO'];
            //$Element['DEPT_PARENT']      = $rs_a[$i]['DEPT_PARENT'];
            $Element['MANAGER']          = $rs_a[$i]['MANAGER'];
            $Element['LEADER1']          = $rs_a[$i]['LEADER1'];
            $Element['LEADER2']          = $rs_a[$i]['LEADER2'];
            //$Element['DEPT_FUNC']        = $rs_a[$i]['DEPT_FUNC'];
            $RS = InsertOrUpdateTableByArray($TableName="data_department",$Element,"id",0,"Insert");  
            //print_R($RS);
        }
        //print_R($Element);exit;
    }
    else {
    }
    //print_R($db_remote->databaseName);
}
?>