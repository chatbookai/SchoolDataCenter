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
ini_set('max_execution_time', 7200);


global $SourceDir,$TargetDir,$JumpFile;

$WEB_ROOT 	  = $_SERVER['DOCUMENT_ROOT']."";
$API_ROOT 	  = $_SERVER['DOCUMENT_ROOT']."../api";
$SourceDir		= $API_ROOT;
$TargetDir		= $API_ROOT;

if($_GET['action'] == '' && $_SERVER['SERVER_NAME'] != 'fdzz.dandian.net') {
  page_css("更新核心数据库脚本");
  table_begin("1100");
  print "<tr class=TableHeader><td colspan=5>&nbsp;更新核心数据库脚本</td></tr>";
  print " <TR>
              <TD class=TableHeader colspan=5>&nbsp;
              <input type=button class='layui-btn layui-btn-xs layui-btn' name='Goview' value='更新数据字典' Onclick=\"location='?".strval("asdfa&action=UpdateFormDict")."'\">
              </TD>
          </TR>
          ";
  $sql  = "select id, TableName, FullName, FormGroup from form_formname order by id desc";
  $rs   = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  $已有表单 = [];
  foreach($rs_a as $Item)  {
    $已有表单[$Item['id']] = $Item;
  }

  $REMOTE_SERVER  = "https://fdzz.dandian.net/api/tools/updatedatabase.php?action=GetFormList";
  $GetFormList = file_get_contents($REMOTE_SERVER);
  $GetFormList = DecryptIDFixedCORS($GetFormList);
  $GetFormList = json_decode($GetFormList, true);
  foreach($GetFormList as $Item)  {
    if($已有表单[$Item['id']]) {
      $EVENT = "<input type=button class='layui-btn layui-btn-xs layui-btn-danger' value='更新表单' Onclick=\"location='?".strval("asdfa&action=UpdateFormInfor&id=".$Item['id']."")."'\">";
    }
    else {
      $EVENT = "<input type=button class='layui-btn layui-btn-xs layui-btn' value='下载安装' Onclick=\"location='?".strval("asdfa&action=UpdateFormInfor&id=".$Item['id']."")."'\">";
    }
    $EVENT .= "&nbsp;<input type=button class='layui-btn layui-btn-xs layui-btn' value='下载数据' Onclick=\"location='?".strval("asdfa&action=UpdateFormRecords&id=".$Item['id']."")."'\">";

    print " <TR>
              <TD class=TableData noWrap >&nbsp;".$Item['id']."</TD>
              <TD class=TableData noWrap >&nbsp;".$Item['TableName']."</TD>
              <TD class=TableData noWrap >&nbsp;".$Item['FullName']."</TD>
              <TD class=TableData noWrap >&nbsp;".$Item['FormGroup']."</TD>
              <TD class=TableData noWrap >&nbsp;".$EVENT."</TD>
          </TR>
          ";
  }
  print "</table>";
}

if($_GET['action'] == 'UpdateFormDict') {
  page_css("更新核心数据库脚本");
  $REMOTE_SERVER  = "https://fdzz.dandian.net/api/tools/updatedatabase.php?action=GetFormDict";
  $FORM_DICT_DATA = file_get_contents($REMOTE_SERVER);
  $FORM_DICT_DATA = DecryptIDFixedCORS($FORM_DICT_DATA);
  $FORM_DICT_DATA = json_decode($FORM_DICT_DATA, true);
  $form_formfield_showtype  = $FORM_DICT_DATA['form_formfield_showtype'];
  foreach($form_formfield_showtype as $Item)  {
    $Item['id'] = null;
    [$rs,$sql] = InsertOrUpdateTableByArray("form_formfield_showtype",$Item,"Name",0,'Insert');
    if(!$rs->EOF&&!$rs) {
        print "<font color=red>".$Item['Name']." ".$sql."</font><BR>";
        exit;
    }
    else {
        print "$i <font color=green>插入成功</font>:".$Item['Name']." ".$sql."<BR>";
    }
  }

  print "<BR>";

  $form_formdict            = $FORM_DICT_DATA['form_formdict'];
  foreach($form_formdict as $Item)  {
    $Item['id'] = null;
    [$rs,$sql] = InsertOrUpdateTableByArray("form_formdict",$Item,"DictMark,ChineseName",0,'Insert');
    if(!$rs->EOF&&!$rs) {
        print "<font color=red>".$Item['ChineseName']." ".$sql."</font><BR>";
        exit;
    }
    else {
        print "$i <font color=green>插入成功</font>:".$Item['ChineseName']." ".$sql."<BR>";
    }
  }
  print "<META HTTP-EQUIV=REFRESH CONTENT='20;URL=?'>\n";
  exit;
}

if($_GET['action'] == 'UpdateFormInfor'&&$_GET['id']!='') {
  page_css("更新核心数据库脚本");
  $REMOTE_SERVER  = "https://fdzz.dandian.net/api/tools/updatedatabase.php?action=GetFormInfor&id=".$_GET['id'];
  $GetFormInfor = file_get_contents($REMOTE_SERVER);
  $GetFormInfor = DecryptIDFixedCORS($GetFormInfor);
  $GetFormInfor = json_decode($GetFormInfor, true);

  $form_formname  = $GetFormInfor['form_formname'];
  [$rs,$sql]      = InsertOrUpdateTableByArray("form_formname",$form_formname,"id",0,'Insert');
  if(!$rs->EOF&&!$rs) {
    print "<font color=red>".$Item['TableName']." ".$sql."</font><BR>";
    exit;
  }
  else {
      print "$i <font color=green>插入成功</font>:".$Item['TableName']." ".$sql."<BR>";
  }
  print "<BR>";

  $form_formfield = $GetFormInfor['form_formfield'];
  foreach($form_formfield as $Item)  {
    $Item['id'] = null;
    [$rs,$sql] = InsertOrUpdateTableByArray("form_formfield",$Item,"FormId,FieldName",0,'Insert');
    if(!$rs->EOF&&!$rs) {
        print "<font color=red>".$Item['FieldName']." ".$sql."</font><BR>";
        exit;
    }
    else {
        print "$i <font color=green>插入成功</font>:".$Item['FieldName']." ".$sql."<BR>";
    }
  }
  print "<BR>";

  $form_formflow  = $GetFormInfor['form_formflow'];
  foreach($form_formflow as $Item)  {
    $Item['id'] = null;
    [$rs,$sql] = InsertOrUpdateTableByArray("form_formflow",$Item,"FormId,FlowName",0,'Insert');
    if(!$rs->EOF&&!$rs) {
        print "<font color=red>".$Item['FlowName']." ".$sql."</font><BR>";
        exit;
    }
    else {
        print "$i <font color=green>插入成功</font>:".$Item['FlowName']." ".$sql."<BR>";
    }
  }
  print "<BR>";

  $CREATETABLE    = $GetFormInfor['CREATETABLE'];
  if($CREATETABLE!="") {
    $db->Execute($CREATETABLE);
  }
  print "<BR>";

  print "<META HTTP-EQUIV=REFRESH CONTENT='10;URL=?'>\n";
  exit;
}

if($_GET['action'] == 'UpdateFormRecords'&&$_GET['id']!='') {
  page_css("更新核心数据库脚本");
  $REMOTE_SERVER  = "https://fdzz.dandian.net/api/tools/updatedatabase.php?action=GetFormRecords&id=".$_GET['id'];
  $GetFormRecords = file_get_contents($REMOTE_SERVER);
  $GetFormRecords = DecryptIDFixedCORS($GetFormRecords);
  $GetFormRecords = json_decode($GetFormRecords, true);
  $TableName  = $GetFormRecords['TableName'];
  $data       = $GetFormRecords['data'];
  foreach($data as $Item)  {
    [$rs,$sql] = InsertOrUpdateTableByArray($TableName,$Item,"id",0,'Insert');
    if(!$rs->EOF&&!$rs) {
        print "<font color=red>".$Item['id']." ".$sql."</font><BR>";
        exit;
    }
    else {
        print "$i <font color=green>插入成功</font>:".$Item['id']." ".$sql."<BR>";
    }
  }

  print "<META HTTP-EQUIV=REFRESH CONTENT='5;URL=?'>\n";
  exit;
}

if($_GET['action'] == 'GetFormDict') {
  $RS   = [];
  $sql  = "select * from form_formfield_showtype order by id asc";
  $rs   = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  $RS['form_formfield_showtype'] = $rs_a;
  $sql = "select * from form_formdict order by id asc";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  $RS['form_formdict'] = $rs_a;
  $RSTEXT = json_encode($RS);
  print EncryptIDFixedCORS($RSTEXT);
  exit;
}

if($_GET['action'] == 'GetFormInfor'&&$_GET['id']!='') {
  $id = intval($_GET['id']);
  $RS = [];
  $sql = "select * from form_formname where id = '$id' order by id asc";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  $RS['form_formname'] = $rs_a[0];
  $TableName = $rs_a[0]['TableName'];
  if($TableName != "")   {
    $sql = "select * from form_formfield where FormId = '$id' order by id asc";
    $rs = $db->Execute($sql);
    $rs_a = $rs->GetArray();
    $RS['form_formfield'] = $rs_a;

    $sql = "select * from form_formflow where FormId = '$id' order by id asc";
    $rs = $db->Execute($sql);
    $rs_a = $rs->GetArray();
    $RS['form_formflow'] = $rs_a;

    $sql = "SHOW CREATE TABLE $TableName";
    $rs = $db->Execute($sql);
    $rs_a = $rs->GetArray();
    $RS['CREATETABLE'] = $rs_a[0]['Create Table'];
  }

  $RSTEXT = json_encode($RS);
  print EncryptIDFixedCORS($RSTEXT);
  exit;
}

if($_GET['action'] == 'GetFormRecords'&&$_GET['id']!='') {
  $id = intval($_GET['id']);
  $sql = "select * from form_formname where id = '$id' order by id asc";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  $TableName = $rs_a[0]['TableName'];

  if($TableName!="")  {
    $sql  = "select * from $TableName order by id desc";
    $rs = $db->Execute($sql);
    $rs_a = $rs->GetArray();
    $RS = [];
    $RS['data']       = $rs_a;
    $RS['TableName']  = $TableName;
    $RSTEXT = json_encode($RS);
    print EncryptIDFixedCORS($RSTEXT);
  }

  exit;
}


if($_GET['action'] == 'GetFormList') {
  $sql  = "select id, TableName, FullName, FormGroup from form_formname order by id desc";
  $rs = $db->Execute($sql);
  $rs_a = $rs->GetArray();
  $RSTEXT = json_encode($rs_a);
  print EncryptIDFixedCORS($RSTEXT);
  exit;
}


?>
