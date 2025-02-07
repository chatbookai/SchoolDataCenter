<?php

//FlowName: 德育量化数据同步

function plugin_data_datasource_3_init_default()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_3_add_default_data_before_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_3_add_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    /*
    $sql        = "select * from `$TableName` where id = '$id'";
    $rs         = $db->Execute($sql);
    $rs_a       = $rs->GetArray();
    foreach($rs_a as $Line)  {
        //
    }
    */
}

function plugin_data_datasource_3_edit_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_3_edit_default_data_before_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_3_edit_default_data_after_submit($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_3_edit_default_configsetting_data($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;

	  $数据库地址     = $_POST['数据库地址'];
    $数据库用户名   = $_POST['数据库用户名'];
    $数据库密码     = $_POST['数据库密码'];
    $数据库名称     = "td_form";
    $db_remote = NewADOConnection($DB_TYPE='mysqli');
    $db_remote->connect($数据库地址, $数据库用户名, $数据库密码, $数据库名称);
    $db_remote->Execute("Set names utf8;");
    $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
    if($db_remote->databaseName!="" && $db_remote->databaseName==$数据库名称) {
		// 一级指标
		// ###########################################################################
		/*$sql    = "select * from td_form.data_deyu_geren_gradeone";
        $rs     = $db_remote->Execute($sql);
        $rs_a   = $rs->GetArray();
		for($i=0;$i<sizeof($rs_a);$i++) {
			$Element = [];
			$Element['名称']              = $rs_a[$i]['名称'];
			$Element['描述']              = $rs_a[$i]['描述'];
			$Element['最高分值']              = $rs_a[$i]['最高分值'];
			$Element['最低分值']              = $rs_a[$i]['最低分值'];
			$Element['排序号']              = $rs_a[$i]['排序号'];
			$RS = InsertOrUpdateTableByArray($TableName="data_deyu_geren_gradeone",$Element,"名称",0);
		}*/
		// ###########################################################################

		// 二级指标
		// ###########################################################################
		/*$sql    = "select * from td_form.data_deyu_geren_gradetwo";
		$rs     = $db_remote->Execute($sql);
		$rs_a   = $rs->GetArray();
		for($i=0;$i<sizeof($rs_a);$i++) {
			$Element = [];
			$Element['一级指标']              = $rs_a[$i]['一级指标'];
			$Element['二级指标']              = $rs_a[$i]['二级指标'];
			//$Element['积分编码']              = $rs_a[$i]['积分编码'];
			$Element['排序号']              = $rs_a[$i]['排序号'];
			$Element['备注']              = $rs_a[$i]['备注'];
			$Element['创建人']              = $rs_a[$i]['创建人'];
			$Element['创建时间']              = $rs_a[$i]['创建时间'];
			$RS = InsertOrUpdateTableByArray($TableName="data_deyu_geren_gradetwo",$Element,"排序号",0);
		}*/
		// ###########################################################################

		// 积分项目
		// ###########################################################################
		/*$sql    = "select * from td_form.data_deyu_geren_gradethree";
		$rs     = $db_remote->Execute($sql);
		$rs_a   = $rs->GetArray();
		for($i=0;$i<sizeof($rs_a);$i++) {
			$Element = [];
			$Element['一级指标']              = $rs_a[$i]['一级指标'];
			$Element['二级指标']              = $rs_a[$i]['二级指标'];
			$Element['积分项目']              = $rs_a[$i]['积分项目'];
			$Element['积分编码']              = $rs_a[$i]['积分编码'];
			$Element['积分分值']              = $rs_a[$i]['积分分值'];
			$Element['排序号']              = $rs_a[$i]['排序号'];
			$Element['备注']              = $rs_a[$i]['备注'];
			$Element['管理人员']              = $rs_a[$i]['管理人员姓名列表'];
			$Element['是否班主任录入']              = $rs_a[$i]['是否班主任录入'];
			$Element['数据来源']              = $rs_a[$i]['数据来源'];
			$Element['创建人']              = $rs_a[$i]['创建人'];
			$Element['创建时间']              = $rs_a[$i]['创建时间'];

			$RS = InsertOrUpdateTableByArray($TableName="data_deyu_geren_gradethree",$Element,"积分编码",0);
		}*/
		// ###########################################################################

		// 个人积分
		// ###########################################################################
		$sql    = "select * from td_form.data_deyu_geren_record where 学期名称='2021-2022-第二学期'";
		$rs     = $db_remote->Execute($sql);
		$rs_a   = $rs->GetArray();
		for($i=0;$i<sizeof($rs_a);$i++) {
			$Element = [];
			//$Element['编号'] 			= $rs_a[$i]['编号'];
			$Element['学期'] 			= $rs_a[$i]['学期名称'];
			$Element['一级指标']              = $rs_a[$i]['一级指标'];
			$Element['二级指标']              = $rs_a[$i]['二级指标'];
			$Element['学号'] 			= $rs_a[$i]['学号'];
			$Element['姓名'] 			= $rs_a[$i]['姓名'];
			$Element['班级'] 			= $rs_a[$i]['班级'];
			$Element['积分项目']         = $rs_a[$i]['积分项目'];
			$Element['积分编码']         = $rs_a[$i]['积分编码'];
			$Element['积分分值']         = $rs_a[$i]['积分分值'];
			$Element['积分原因']         = $rs_a[$i]['积分原因'];
			$Element['积分时间']         = $rs_a[$i]['积分时间'];
			$Element['备注']            	= $rs_a[$i]['备注'];
			$Element['创建人']           = $rs_a[$i]['创建人'];
			$Element['创建时间']              = $rs_a[$i]['创建时间'];
			$Element['数据录入']              = $rs_a[$i]['数据录入'];
			$Element['学生代录']              = $rs_a[$i]['学生代录'];

			$Element['班主任审核状态']              = $rs_a[$i]['班主任审核状态'];
			$Element['班主任审核时间']              = $rs_a[$i]['班主任审核时间'];
			$Element['班主任审核人']              = $rs_a[$i]['班主任审核人'];
			$Element['班主任审核意见']              = $rs_a[$i]['班主任审核意见'];

			$Element['系部审核状态']              = $rs_a[$i]['院系审核状态'];
			$Element['系部审核时间']              = $rs_a[$i]['院系审核时间'];
			$Element['系部审核人']              = $rs_a[$i]['院系审核人'];
			$Element['系部审核意见']              = $rs_a[$i]['院系审核意见'];

			$Element['学工审核状态']              = $rs_a[$i]['学工审核状态'];
			$Element['学工审核时间']              = $rs_a[$i]['学工审核时间'];
			$Element['学工审核人']              = $rs_a[$i]['学工审核人'];
			$Element['学工审核意见']              = $rs_a[$i]['学工审核意见'];

			$Element['学校审核状态']              = $rs_a[$i]['校级审核状态'];
			$Element['学校审核时间']              = $rs_a[$i]['校级审核时间'];
			$Element['学校审核人']              = $rs_a[$i]['校级审核人'];
			$Element['学校审核意见']              = $rs_a[$i]['校级审核意见'];

			$RS = InsertOrUpdateTableByArray($TableName="data_deyu_geren_record",$Element,"学号,积分编码,积分时间",0);
		}
		// ###########################################################################
	}else {
        $RS = [];
        $RS['status']   = "ERROR";
        $RS['data']     = $data;
        $RS['sql']      = $sql;
        $RS['msg']      = "您输入的数据库连接信息错误,请重新输入.";
        print json_encode($RS);
        exit;
    }

}

function plugin_data_datasource_3_view_default($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_3_delete_array($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_3_updateone($id)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

function plugin_data_datasource_3_import_default_data_before_submit($Element)  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
    return $Element;
}

function plugin_data_datasource_3_import_default_data_after_submit()  {
    global $db;
    global $SettingMap;
    global $MetaColumnNames;
    global $GLOBAL_USER;
    global $TableName;
    //Here is your write code
}

?>
