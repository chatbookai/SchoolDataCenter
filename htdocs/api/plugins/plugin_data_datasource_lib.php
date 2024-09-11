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

page_css("数据同步过程");

$sql    = "select count(*) AS NUM from data_datasyncedrules where 数据同步周期='每天' and 计划时间='".date('Y-m-d')."'";
$rs     = $db->Execute($sql);
if($rs->fields['NUM']==0) {
    $sql = "update data_datasyncedrules set 执行状态='未执行', 计划时间='".date('Y-m-d')."' where 数据同步周期='每天'";
    $db->Execute($sql);
    print $sql."<BR>";
}
//$sql = "update data_datasyncedrules set 执行状态='未执行', 计划时间='".date('Y-m-d')."' where 数据同步周期='每天'";$db->Execute($sql);
$id     = DecryptID($_GET['id']);
if($_GET['action']=="all") {
    $sql    = "select * from data_datasyncedrules where 数据同步周期='每天' and 执行状态='未执行' and 数据源!='' and 远程数据表!='' ";
}
else if($id>0) {
    $sql    = "select * from data_datasyncedrules where id='$id'";
}
else {
    print "数据同步没有开始,请检查URL参数是否正确";
}
$rs     = $db->Execute($sql);
$rs_a   = $rs->GetArray();
foreach($rs_a as $LineX) {
    $FormId             = $LineX['FormId'];
    $TableName          = $LineX['TableName'];
    $数据源             = $LineX['数据源'];
    $远程数据表         = $LineX['远程数据表'];
    $远程数据表主键     = $LineX['远程数据表主键'];
    $数据同步方式       = $LineX['数据同步方式'];
    $数据同步周期       = $LineX['数据同步周期'];
    $执行状态           = $LineX['执行状态'];
    $执行时间           = $LineX['执行时间'];
    $远程数据库信息      = returntablefield("data_datasource","id",$数据源,"数据库主机,数据库用户名,数据库密码,数据库名称,连接池名称");
    if($远程数据库信息['数据库用户名']!="")    {
        $db_remote = NewADOConnection($DB_TYPE='mysqli');
        $db_remote->connect($远程数据库信息['数据库主机'], $远程数据库信息['数据库用户名'], DecryptID($远程数据库信息['数据库密码']), $远程数据库信息['数据库名称']);
        $db_remote->Execute("Set names utf8;");
        $db_remote->setFetchMode(ADODB_FETCH_ASSOC);
        if($db_remote->databaseName==$远程数据库信息['数据库名称']) {
            $MetaColumnNamesTemp    = $db_remote->MetaColumnNames($远程数据表);
            $远程数据表结构          = array_values($MetaColumnNamesTemp);
            if(is_array($远程数据表结构) && $远程数据表结构[0]!="")     {

                //得到当前数据中心表的结构
                $sql            = "select * from form_formfield where FormId='$FormId' and IsEnable='1' order by SortNumber asc, id asc";
                $rs_fields      = $db->Execute($sql);
                $rs_a_fields    = $rs_fields->GetArray();
                $字段名称转中文名称 = [];
                
                //开始做远程数据表同步到本数据中心的操作
                if($数据同步方式=="全量同步")  {
                    $学校十位代码              = returntablefield("ods_zzxxgkjcsj","id",1,"XXDM")['XXDM'];
                    $数据字典同步异常数据       = [];
                    $数据字典同步异常数据Detail = [];
                    $数据必须填写项数据         = [];
                    $错误设置远程数据表关联字段  = [];
                    $新编号 = 0;
                    //第一步:清空本地表
                    $sql = "truncate table $TableName";
                    $db->Execute($sql);
                    //第二步:从远程数据表获得数据
                    $BatchSqlBody   = [];
                    $sql            = "select * from $远程数据表";
                    $rs_remote      = $db_remote->Execute($sql);
                    $rs_a_remote    = $rs_remote->GetArray();
                    $NewRecord      = [];
                    $远程数据表记录数量 = sizeof($rs_a_remote);
                    foreach($rs_a_remote as $RomoteLine)  {
                        $远程记录是否执行 = 1;
                        foreach($rs_a_fields as $Item) {
                            $字段名称转中文名称[$Item['FieldName']] = $Item['ChineseName'];
                            $FieldsSeting               = json_decode($Item['Setting'], true);
                            $RemoteRelativeField        = $FieldsSeting['RemoteRelativeField'];    
                            $LocalFieldExtraFilter      = $FieldsSeting['LocalFieldExtraFilter'];   
                            $IsMustFill                 = $FieldsSeting['IsMustFill'];                          
                            //第三步:同步远程数据表数据,并且进行数据清洗. 这一步要先做,然后才能在本地默认值处理时,可以做本地的数据表关联
                            if($RemoteRelativeField!="" && $RemoteRelativeField!="None" && in_array($RemoteRelativeField, $远程数据表结构))  {
                                $FieledShowTypeArray = explode(':',$FieldsSeting['ShowType']);
                                switch($FieledShowTypeArray[0]) {
                                    case 'Input':
                                        $NewRecord[$FieldsSeting['FieldName']] = $RomoteLine[$RemoteRelativeField];
                                        break;
                                    case '中职标准':
                                        //数据字典的名称转代码
                                        $ADDTYPE        = returntablefield("form_formfield_showtype","Name",$FieldsSeting['ShowType'],"`ADD`")['ADD'];
                                        $ADDTYPE_ARRAY  = explode(':',$ADDTYPE);
                                        if(sizeof($ADDTYPE_ARRAY)==7 && $ADDTYPE_ARRAY[1]=="form_formdict") {
                                            $Temp_MetaColumnNames = GLOBAL_MetaColumnNames($ADDTYPE_ARRAY[1]);
                                            $sql        = "select ".$Temp_MetaColumnNames[$ADDTYPE_ARRAY[2]]." AS CODE,".$Temp_MetaColumnNames[$ADDTYPE_ARRAY[3]]." AS NAME, OtherPossibleValues from ".$ADDTYPE_ARRAY[1]." where ".$ADDTYPE_ARRAY[5]."='".$ADDTYPE_ARRAY[6]."'";
                                            $rs_temp    = $db->Execute($sql);
                                            $rs_a_temp  = $rs_temp->GetArray();
                                            $数据字典合集 = [];
                                            foreach($rs_a_temp as $LineTemp) {
                                                $OtherPossibleValuesArray = explode(',',$OtherPossibleValues);
                                                foreach($OtherPossibleValues as $TEMP) {
                                                    $数据字典合集[$TEMP] = $LineTemp['NAME'];
                                                }
                                                $数据字典合集[$LineTemp['CODE']] = $LineTemp['CODE'];
                                                $数据字典合集[$LineTemp['NAME']] = $LineTemp['CODE'];
                                            }
                                            if($ADDTYPE_ARRAY[6]=="XQDM（学期代码）")  {
                                                if(strpos($RomoteLine[$RemoteRelativeField],"第一学期")) {
                                                    $RomoteLine[$RemoteRelativeField] = "秋季学期";
                                                }
                                                else if(strpos($RomoteLine[$RemoteRelativeField],"第二学期")) {
                                                    $RomoteLine[$RemoteRelativeField] = "春季学期";
                                                }
                                            }
                                            else if($ADDTYPE_ARRAY[6]=="XQDM（学期代码）")  {
                                                if(strpos($RomoteLine[$RemoteRelativeField],"第一学期")) {
                                                    $RomoteLine[$RemoteRelativeField] = "秋季学期";
                                                }
                                                else if(strpos($RomoteLine[$RemoteRelativeField],"第二学期")) {
                                                    $RomoteLine[$RemoteRelativeField] = "春季学期";
                                                }
                                            }
                                            if($数据字典合集[$RomoteLine[$RemoteRelativeField]]!="")  {
                                                $NewRecord[$FieldsSeting['FieldName']] = $数据字典合集[$RomoteLine[$RemoteRelativeField]];
                                            }
                                            elseif(1) {
                                                //记录数据字典同步时,是哪一个记录出的问题
                                                $数据字典同步异常数据Detail[$RomoteLine[$RemoteRelativeField]] = ["远程数据记录的值"=>"<font color=red>".$RomoteLine[$RemoteRelativeField]."</font><BR>在本地库匹配不到", "本地数据字典"=>$ADDTYPE_ARRAY[6], "远程数据表字段"=>$RemoteRelativeField,"允许值列表"=>join(",",array_keys($数据字典合集)),"涉及数据"=>$数据字典同步异常数据Detail[$RomoteLine[$RemoteRelativeField]]['涉及数据']+1];
                                                $远程记录是否执行 = 0;
                                            }
                                            else {
                                                $数据字典同步异常数据[$FieldsSeting['FieldName']][$RemoteRelativeField][$RomoteLine[$RemoteRelativeField]] += 1;
                                            }
                                        }
                                        break;
                                    
                                }
                            }
                            //第四步:不使用远程数据表使用,而是使用本地数据的配置来产生一个默认值
                            else if($RemoteRelativeField=="Default")  {
                                switch($FieldsSeting['ShowType']) {
                                    case 'Hidden:Createandupdatetime':
                                        if($FieldsSeting['DateTimeFormat']!="") {
                                            $NewRecord[$FieldsSeting['FieldName']] = date($FieldsSeting['DateTimeFormat']);    
                                        }
                                        else {
                                            $NewRecord[$FieldsSeting['FieldName']] = date("Y-m-d H:i:s");  
                                        }
                                        break;
                                    case '32位全局唯一编码字符串':
                                        $唯一编码前缀   = $学校十位代码.$FieldsSeting['Placeholder'];
                                        $剩余位数       = 32-strlen($唯一编码前缀);
                                        $新编号         += 1;
                                        $补齐0数量      = $剩余位数-strlen($新编号);
                                        while($补齐0数量>0) {
                                            $唯一编码前缀 .= "0";
                                            $补齐0数量 --;
                                        }
                                        $NewRecord[$FieldsSeting['FieldName']] = $唯一编码前缀.$新编号;
                                        break;
                                    default:
                                        $ADDTYPE        = returntablefield("form_formfield_showtype","Name",$FieldsSeting['ShowType'],"`ADD`")['ADD'];
                                        $ADDTYPE_ARRAY  = explode(':',$ADDTYPE);
                                        if(sizeof($ADDTYPE_ARRAY)==4) {
                                            $Temp_MetaColumnNames = GLOBAL_MetaColumnNames($ADDTYPE_ARRAY[1]);
                                            $sql        = "select ".$Temp_MetaColumnNames[$ADDTYPE_ARRAY[2]]." AS NAME from ".$ADDTYPE_ARRAY[1]."";
                                            $rs_temp    = $db->CacheExecute(180, $sql);
                                            $NewRecord[$FieldsSeting['FieldName']] = $rs_temp->fields['NAME'];
                                            //print_R($NewRecord);;exit;
                                        }
                                        else if($ADDTYPE_ARRAY[1]=="form_formdict" && sizeof($ADDTYPE_ARRAY)==7) {
                                            //$Temp_MetaColumnNames = GLOBAL_MetaColumnNames($ADDTYPE_ARRAY[1]);
                                            //$sql        = "select ".$Temp_MetaColumnNames[$ADDTYPE_ARRAY[2]]." AS NAME from ".$ADDTYPE_ARRAY[1]."";
                                            //$rs_temp    = $db->CacheExecute(180, $sql);
                                            //$NewRecord[$FieldsSeting['FieldName']] = $rs_temp->fields['NAME'];
                                            print_R($ADDTYPE_ARRAY);
                                            print_R($NewRecord);;exit;
                                        }
                                        else {
                                            $错误设置远程数据表关联字段[$FieldsSeting['FieldName']] = ['本地数据表字段'=>$FieldsSeting['FieldName'],'本地数据表字段类型'=>$FieldsSeting['ShowType'],'关联远程数据表'=>$RemoteRelativeField,'解决办法'=>'在使用默认值时,需要有指定的一个默认值,目前没有获得到指定的默认值,请修改[远程数据表关联字段]的值'];
                                        }
                                        break;
                                }
                            }
                            //第五步:LocalFieldExtraFilter 必须要先过滤有效的值,才能在这一步拿到值
                            else if($RemoteRelativeField=="LocalFieldExtraFilter"&&$LocalFieldExtraFilter!=""&&sizeof(explode(":",$LocalFieldExtraFilter))==4)  {
                                $LocalFieldExtraFilterArray = explode(":",$LocalFieldExtraFilter);
                                $Temp_MetaColumnNames = GLOBAL_MetaColumnNames($LocalFieldExtraFilterArray[1]);
                                if(in_array($LocalFieldExtraFilterArray[2],$Temp_MetaColumnNames) && in_array($LocalFieldExtraFilterArray[3],$Temp_MetaColumnNames) && $LocalFieldExtraFilterArray[0]!="" && $NewRecord[$LocalFieldExtraFilterArray[0]]!="") {
                                    $NewRecord[$FieldsSeting['FieldName']] = returntablefield($LocalFieldExtraFilterArray[1],$LocalFieldExtraFilterArray[2],$NewRecord[$LocalFieldExtraFilterArray[0]],$LocalFieldExtraFilterArray[3])[$LocalFieldExtraFilterArray[3]];
                                    //print_R($NewRecord[$LocalFieldExtraFilterArray[0]]);
                                    //print_R($Temp_MetaColumnNames);;exit;
                                }
                                elseif($LocalFieldExtraFilterArray[0]!="" && $NewRecord[$LocalFieldExtraFilterArray[0]]!="") {
                                    //在本地数据表的数据过滤中,没有命中
                                    print "在本地数据表的数据过滤中,没有命中";
                                    print_R($LocalFieldExtraFilterArray);
                                }
                                else {
                                    //为空,无需过滤
                                }
                            }
                            //第五步:LocalFieldExtraFilter 自定义的数据同步和转换规则
                            else if($RemoteRelativeField=="LocalFieldExtraFilter"&&$LocalFieldExtraFilter!=""&&sizeof(explode(":",$LocalFieldExtraFilter))==2)  {
                                $LocalFieldExtraFilterArray = explode(":",$LocalFieldExtraFilter);
                                $自定义的数据同步和转换规则  = $LocalFieldExtraFilterArray[0];
                                $有值的远程字段             = $LocalFieldExtraFilterArray[1];
                                $有值的远程字段的值         = $RomoteLine[$有值的远程字段];
                                if($有值的远程字段的值!="") {
                                    switch($自定义的数据同步和转换规则) {
                                        case '班级转系部名称':
                                            $sql        = "select 所属系 from edu_banji where 班级名称='$有值的远程字段的值'";
                                            $RST        = $db_remote->Execute($sql);
                                            $所属系      = $RST->fields['所属系'];
                                            if($所属系!="")   {
                                                $sql        = "select 系名称 from edu_xi where 系代码='$所属系'";
                                                $RST        = $db_remote->Execute($sql);
                                                $NewRecord[$FieldsSeting['FieldName']]  = $RST->fields['系名称'];                                            
                                            }
                                            else {
                                                $NewRecord[$FieldsSeting['FieldName']]  = "";
                                            }
                                            break;
                                        case '班级转年级':
                                            $sql        = "select 入学年份 from edu_banji where 班级名称='$有值的远程字段的值'";
                                            $RST        = $db_remote->Execute($sql);
                                            $NewRecord[$FieldsSeting['FieldName']]  = $RST->fields['入学年份'];
                                            break;
                                        case '班级转专业名称':
                                            $sql        = "select 所属专业 from edu_banji where 班级名称='$有值的远程字段的值'";
                                            $RST        = $db_remote->Execute($sql);
                                            $所属专业      = $RST->fields['所属专业'];
                                            if($所属专业!="")   {
                                                $sql        = "select 专业名称 from edu_zhuanye where 专业代码='$所属专业'";
                                                $RST        = $db_remote->Execute($sql);
                                                $NewRecord[$FieldsSeting['FieldName']]  = $RST->fields['专业名称'];                                            
                                            }
                                            else {
                                                $NewRecord[$FieldsSeting['FieldName']]  = "";
                                            }
                                            break;
                                        case '班级转专业代码':
                                            $sql        = "select 所属专业 from edu_banji where 班级名称='$有值的远程字段的值'";
                                            $RST        = $db_remote->Execute($sql);
                                            $NewRecord[$FieldsSeting['FieldName']]  = $RST->fields['所属专业'];
                                            break;
                                        default:
                                            print_R($LocalFieldExtraFilterArray);
                                            exit;
                                            break;
                                    }
                                }
                            }

                            //必填写项
                            if($IsMustFill && $NewRecord[$FieldsSeting['FieldName']]=="" && $RemoteRelativeField!="None")  {
                                if($RemoteRelativeField=="LocalFieldExtraFilter")   {
                                    $数据必须填写项数据[$FieldsSeting['FieldName']."____".$FieldsSeting['ChineseName']."____数据转换 ".$FieldsSeting['LocalFieldExtraFilter']] += 1;
                                }
                                else {
                                    $数据必须填写项数据[$FieldsSeting['FieldName']."____".$FieldsSeting['ChineseName']."____".$RemoteRelativeField] += 1;
                                }
                                $远程记录是否执行 = 0;
                            }
                            //print_R($FieldsSeting);//exit;
                        }
                        if(sizeof(array_keys($NewRecord))>0 && $远程记录是否执行==1) {
                            $BatchSqlBody[] = "('".join("','",array_values($NewRecord))."')";
                        }
                    }
                    $所有异常数据                           = [];
                    $所有异常数据['数据源']                 = $远程数据库信息['连接池名称'];
                    $所有异常数据['远程数据表']             = $远程数据表;
                    $所有异常数据['数据字典同步异常数据']   = $数据字典同步异常数据;
                    $所有异常数据['同步异常数据解决方法']   = "把异常数据的数据字典重新跟标准库的数据字典做一下关联.";
                    $数据字典同步异常数据Array  = [];
                    foreach($数据字典同步异常数据 as $本地字段=>$详细信息) {
                        foreach($详细信息 as $远程字段=>$字典信息) {
                            foreach($字典信息 as $字典名称=>$字典数量) {
                                $FieldElement = [];
                                $FieldElement['数据源']     = $远程数据库信息['连接池名称'];
                                $FieldElement['本地字段']   = $本地字段;
                                $FieldElement['远程数据表'] = $远程数据表;
                                $FieldElement['远程字段']   = $远程字段;
                                $FieldElement['字典名称']   = $字典名称;
                                $FieldElement['异常记录数量'] = $字典数量;
                                $数据字典同步异常数据Array[] = $FieldElement;
                            }
                        }
                    }
                    $所有异常数据['数据字典同步异常数据Array']   = $数据字典同步异常数据Array;

                    $数据必须填写项数据Array = [];
                    foreach($数据必须填写项数据 as $必须填写项字段=>$必须填写项数量) {
                        $FieldElement = [];
                        $必须填写项字段ARRAY = explode('____',$必须填写项字段);
                        $FieldElement['本地数据表字段名称']     = $必须填写项字段ARRAY[0];
                        $FieldElement['本地数据表字段描述']     = $必须填写项字段ARRAY[1];
                        if($必须填写项字段ARRAY[2]=="") {
                            $FieldElement['远程数据表字段']         = "<font color=red>没有配置,请先在[设计表单]中配置远程数据表关联字段</font>";
                        }
                        else {
                            $FieldElement['远程数据表字段']         = $必须填写项字段ARRAY[2];
                        }
                        $FieldElement['空值记录数量']           = $必须填写项数量;
                        if($必须填写项字段ARRAY[2]=="") {
                            $FieldElement['修复数据使用SQL模板,仅供参数,不要乱用']    = "";
                        }
                        else {
                            $FieldElement['修复数据使用SQL模板,仅供参数,不要乱用']    = "update $远程数据表 set ".$FieldElement['远程数据表字段']." = '?' where ".$FieldElement['远程数据表字段']." = '';";
                        }
                        $数据必须填写项数据Array[]              = $FieldElement;
                    }
                    //print_R($数据必须填写项数据Array);
                    $所有异常数据['数据必须填写项数据Array']   = $数据必须填写项数据Array;
                    //每次插入的记录数
                    $batchSize = 50;
                    //将SQL语句拆分成批次进行插入
                    if(count($BatchSqlBody)>0)  {
                        for ($i = 0; $i < count($BatchSqlBody); $i += $batchSize) {
                            $batch = array_slice($BatchSqlBody, $i, $batchSize);
                            $insertSql = "INSERT INTO $TableName (".join(',',array_keys($NewRecord)).") VALUES " . implode(', ', $batch);
                            $rs_temp = $db->Execute($insertSql);
                            if(!$rs_temp->EOF)  {
                                print $insertSql;
                            }
                            else {
                                //print " $i 插入成功";
                            }
                        }
                        $sql = "update data_datasyncedrules set 执行时间='".date('Y-m-d H:i:s')."',执行状态='已完成',执行明细='".json_encode($所有异常数据)."',异常数据='".json_encode($数据字典同步异常数据Array)."' where id='".$LineX['id']."'";
                        $db->Execute($sql);
                        //print $sql."<BR>";
                        if($远程数据表记录数量==count($BatchSqlBody)) {
                            $Color = "blue";
                            $Status = "数据全部同步完成";
                        }
                        else {
                            $Color = "red";
                            $Status = "数据部分同步完成";
                        }
                        $同步结果数据['远程数据表记录数量'] = "<font color=$Color>$远程数据表记录数量</font>";
                        $同步结果数据['本次同步数据']       = "<font color=$Color>".count($BatchSqlBody)."</font>";
                        $同步结果数据['本地数据表']         = "<font color=$Color>$TableName</font>";
                        $同步结果数据['远程数据表']         = "<font color=$Color>$远程数据表</font>";
                        $同步结果数据['未同步数据记录数']    = "<font color=$Color>".($远程数据表记录数量-count($BatchSqlBody))."</font>";
                        $同步结果数据['同步状态']           = "<font color=$Color>$Status</font>";
                        $同步结果数据Array[]                = $同步结果数据;
                        print RSA2HTML($同步结果数据Array,$width='95%',"数据同步结果");  
                        print RSA2HTML(array_values($错误设置远程数据表关联字段),$width='95%',"错误设置远程数据表关联字段");
                        print RSA2HTML($数据字典同步异常数据Array,$width='95%',"数据字典同步异常数据");  
                        print RSA2HTML($数据必须填写项数据Array,$width='95%',"数据必须填写项数据校验");    
                        print RSA2HTML(array_values($数据字典同步异常数据Detail),$width='95%',"数据字典同步异常数据");
                        //显示最近200条记录
                        $sql    = "select * from $TableName order by id desc limit 200";
                        $rsX    = $db->Execute($sql);
                        $rsX_a  = $rsX->GetArray();  
                        $NewElement = [];
                        foreach($rsX_a[0] as $FieldName=>$FieldName) {
                            $NewElement[$FieldName] = $字段名称转中文名称[$FieldName];
                        }
                        $NewElement['id'] = "编号";
                        array_unshift($rsX_a,$NewElement);
                        print RSA2HTML($rsX_a,$width='95%',"过滤以后的数据显示,只显示最近200条数据");
                    }
                    else {
                        $sql = "update data_datasyncedrules set 执行时间='".date('Y-m-d H:i:s')."',执行状态='已执行,但没有获取任何数据',执行明细='".json_encode($所有异常数据)."' where id='".$LineX['id']."'";
                        $db->Execute($sql);
                        //print $sql."<BR>";
                        //print_R($所有异常数据);
                        $同步结果数据['远程数据表记录数量'] = "<font color=red>$远程数据表记录数量</font>";
                        $同步结果数据['本次同步数据']       = "<font color=red>".count($BatchSqlBody)."</font>";
                        $同步结果数据['本地数据表']         = "<font color=red>$TableName</font>";
                        $同步结果数据['远程数据表']         = "<font color=red>$远程数据表</font>";
                        $同步结果数据['未同步数据记录数']    = "<font color=red>".($远程数据表记录数量-count($BatchSqlBody))."</font>";
                        $同步结果数据['同步状态']           = "<font color=red>已执行,但没有获取任何数据</font>";
                        $同步结果数据Array[]                = $同步结果数据;
                        print RSA2HTML($同步结果数据Array,$width='95%',"数据同步结果");  
                        print RSA2HTML(array_values($错误设置远程数据表关联字段),$width='95%',"错误设置远程数据表关联字段");
                        print RSA2HTML($数据字典同步异常数据Array,$width='95%',"数据字典同步异常数据");  
                        print RSA2HTML($数据必须填写项数据Array,$width='95%',"数据必须填写项数据校验");    
                        print RSA2HTML(array_values($数据字典同步异常数据Detail),$width='95%',"数据字典同步异常数据");
                    }
                    //print_R($所有异常数据);
                }
                exit;
            }
        }
    }
    else {
        print "没有完成数据同步配置";
    }
    //每次只执行一次计划任务
    exit;
}


?>