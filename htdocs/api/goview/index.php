<?php
/*
* 基础架构: 单点低代码开发平台
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2007-2025
* License: GPL V3 or Commercial license
*/
header("Pragma: no-cache");
header("Expires: 0");
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');

$param1 = ForSqlInjection($_GET['param1']);
$param2 = ForSqlInjection($_GET['param2']);
$param3 = ForSqlInjection($_GET['param3']);

$BackEndApi = "https://fdzz.dandian.net:8443/api/goview/image";

if($param1=="sys" && $param2=="login")  {

}
else if($param1=="sys" && $param2=="getOssInfo")  {
    $RS = [];
    $RS['msg'] = "操作成功";
    $RS['code'] = 200;
    $RS['data']['bucketURL'] = "/api/goview/bucket/";
    print_R(json_encode($RS));
    exit;
}
else if($param1=="project" && $param2=="create")  {
    CheckAuthUserLoginStatus();
    $payload                    = file_get_contents('php://input');
    $_POST                      = json_decode($payload,true);
    $Element                    = [];
    $Element['indexImage']      = ForSqlInjection($_POST['indexImage']);
    $Element['projectName']     = ForSqlInjection($_POST['projectName']);
    $Element['remarks']         = ForSqlInjection($_POST['remarks']);
    $Element['createTime']      = date("Y-m-d H:i:s");
    [$rs,$sql] = InsertOrUpdateTableByArray($TableName="data_goview_project",$Element,"projectName,createTime",0,'Insert');
    if($rs->EOF) {
        $NewId = $db->Insert_ID();
        $sql = "select * from data_goview_project where id='$NewId'";
        $rsf = $db->Execute($sql);
        $RS = [];
        $RS['msg'] = "操作成功";
        $RS['code'] = 200;
        $RS['data'] = $rsf->fields;
        $RS['data']['id'] = EncryptID($RS['data']['id']);
        //$RS['_SERVER']  = $_SERVER;
        //$RS['_POST']  = $_POST;
        print_R(json_encode($RS));
        exit;
    }
    else {
        $RS = [];
        $RS['msg'] = "创建数据失败";
        $RS['code'] = 219;
        print_R(json_encode($RS));
        exit;
    }
}
else if($param1=="project" && $param2=="getData")  {
    $projectId      = intval(DecryptIDFixed($_GET['projectId']));
    $sql    = "select * from data_goview_project where id='$projectId'";
    $rs     = $db->Execute($sql);
    $rs->fields['state'] = intval($rs->fields['state']);
    $RS     = [];
    $RS['msg']      = "操作成功";
    $RS['code']     = 200;
    //$RS['sql']      = $sql;
    $RS['_GET']     = $_GET;
    $RS['data']                 = $rs->fields;
    $RS['data']['id']           = EncryptID($RS['data']['id']);
    $content = "ew0KICAiZWRpdENhbnZhc0NvbmZpZyI6IHsNCiAgICAicHJvamVjdE5hbWUiOiAiNTVra2QyYnh1ZW8wMDAiLA0KICAgICJ3aWR0aCI6IDE5MjAsDQogICAgImhlaWdodCI6IDEwODAsDQogICAgImZpbHRlclNob3ciOiBmYWxzZSwNCiAgICAiaHVlUm90YXRlIjogMCwNCiAgICAic2F0dXJhdGUiOiAxLA0KICAgICJjb250cmFzdCI6IDEsDQogICAgImJyaWdodG5lc3MiOiAxLA0KICAgICJvcGFjaXR5IjogMSwNCiAgICAicm90YXRlWiI6IDAsDQogICAgInJvdGF0ZVgiOiAwLA0KICAgICJyb3RhdGVZIjogMCwNCiAgICAic2tld1giOiAwLA0KICAgICJza2V3WSI6IDAsDQogICAgImJsZW5kTW9kZSI6ICJub3JtYWwiLA0KICAgICJiYWNrZ3JvdW5kIjogbnVsbCwNCiAgICAiYmFja2dyb3VuZEltYWdlIjogbnVsbCwNCiAgICAic2VsZWN0Q29sb3IiOiB0cnVlLA0KICAgICJjaGFydFRoZW1lQ29sb3IiOiAiZGFyayIsDQogICAgImNoYXJ0Q3VzdG9tVGhlbWVDb2xvckluZm8iOiBudWxsLA0KICAgICJjaGFydFRoZW1lU2V0dGluZyI6IHsNCiAgICAgICJ0aXRsZSI6IHsNCiAgICAgICAgInNob3ciOiB0cnVlLA0KICAgICAgICAidGV4dFN0eWxlIjogew0KICAgICAgICAgICJjb2xvciI6ICIjQkZCRkJGIiwNCiAgICAgICAgICAiZm9udFNpemUiOiAxOA0KICAgICAgICB9LA0KICAgICAgICAic3VidGV4dFN0eWxlIjogew0KICAgICAgICAgICJjb2xvciI6ICIjQTJBMkEyIiwNCiAgICAgICAgICAiZm9udFNpemUiOiAxNA0KICAgICAgICB9DQogICAgICB9LA0KICAgICAgInhBeGlzIjogew0KICAgICAgICAic2hvdyI6IHRydWUsDQogICAgICAgICJuYW1lIjogIiIsDQogICAgICAgICJuYW1lR2FwIjogMTUsDQogICAgICAgICJuYW1lVGV4dFN0eWxlIjogew0KICAgICAgICAgICJjb2xvciI6ICIjQjlCOENFIiwNCiAgICAgICAgICAiZm9udFNpemUiOiAxMg0KICAgICAgICB9LA0KICAgICAgICAiaW52ZXJzZSI6IGZhbHNlLA0KICAgICAgICAiYXhpc0xhYmVsIjogew0KICAgICAgICAgICJzaG93IjogdHJ1ZSwNCiAgICAgICAgICAiZm9udFNpemUiOiAxMiwNCiAgICAgICAgICAiY29sb3IiOiAiI0I5QjhDRSIsDQogICAgICAgICAgInJvdGF0ZSI6IDANCiAgICAgICAgfSwNCiAgICAgICAgInBvc2l0aW9uIjogImJvdHRvbSIsDQogICAgICAgICJheGlzTGluZSI6IHsNCiAgICAgICAgICAic2hvdyI6IHRydWUsDQogICAgICAgICAgImxpbmVTdHlsZSI6IHsNCiAgICAgICAgICAgICJjb2xvciI6ICIjQjlCOENFIiwNCiAgICAgICAgICAgICJ3aWR0aCI6IDENCiAgICAgICAgICB9LA0KICAgICAgICAgICJvblplcm8iOiB0cnVlDQogICAgICAgIH0sDQogICAgICAgICJheGlzVGljayI6IHsNCiAgICAgICAgICAic2hvdyI6IHRydWUsDQogICAgICAgICAgImxlbmd0aCI6IDUNCiAgICAgICAgfSwNCiAgICAgICAgInNwbGl0TGluZSI6IHsNCiAgICAgICAgICAic2hvdyI6IGZhbHNlLA0KICAgICAgICAgICJsaW5lU3R5bGUiOiB7DQogICAgICAgICAgICAiY29sb3IiOiAiIzQ4NDc1MyIsDQogICAgICAgICAgICAid2lkdGgiOiAxLA0KICAgICAgICAgICAgInR5cGUiOiAic29saWQiDQogICAgICAgICAgfQ0KICAgICAgICB9DQogICAgICB9LA0KICAgICAgInlBeGlzIjogew0KICAgICAgICAic2hvdyI6IHRydWUsDQogICAgICAgICJuYW1lIjogIiIsDQogICAgICAgICJuYW1lR2FwIjogMTUsDQogICAgICAgICJuYW1lVGV4dFN0eWxlIjogew0KICAgICAgICAgICJjb2xvciI6ICIjQjlCOENFIiwNCiAgICAgICAgICAiZm9udFNpemUiOiAxMg0KICAgICAgICB9LA0KICAgICAgICAiaW52ZXJzZSI6IGZhbHNlLA0KICAgICAgICAiYXhpc0xhYmVsIjogew0KICAgICAgICAgICJzaG93IjogdHJ1ZSwNCiAgICAgICAgICAiZm9udFNpemUiOiAxMiwNCiAgICAgICAgICAiY29sb3IiOiAiI0I5QjhDRSIsDQogICAgICAgICAgInJvdGF0ZSI6IDANCiAgICAgICAgfSwNCiAgICAgICAgInBvc2l0aW9uIjogImxlZnQiLA0KICAgICAgICAiYXhpc0xpbmUiOiB7DQogICAgICAgICAgInNob3ciOiB0cnVlLA0KICAgICAgICAgICJsaW5lU3R5bGUiOiB7DQogICAgICAgICAgICAiY29sb3IiOiAiI0I5QjhDRSIsDQogICAgICAgICAgICAid2lkdGgiOiAxDQogICAgICAgICAgfSwNCiAgICAgICAgICAib25aZXJvIjogdHJ1ZQ0KICAgICAgICB9LA0KICAgICAgICAiYXhpc1RpY2siOiB7DQogICAgICAgICAgInNob3ciOiB0cnVlLA0KICAgICAgICAgICJsZW5ndGgiOiA1DQogICAgICAgIH0sDQogICAgICAgICJzcGxpdExpbmUiOiB7DQogICAgICAgICAgInNob3ciOiB0cnVlLA0KICAgICAgICAgICJsaW5lU3R5bGUiOiB7DQogICAgICAgICAgICAiY29sb3IiOiAiIzQ4NDc1MyIsDQogICAgICAgICAgICAid2lkdGgiOiAxLA0KICAgICAgICAgICAgInR5cGUiOiAic29saWQiDQogICAgICAgICAgfQ0KICAgICAgICB9DQogICAgICB9LA0KICAgICAgImxlZ2VuZCI6IHsNCiAgICAgICAgInNob3ciOiB0cnVlLA0KICAgICAgICAidHlwZSI6ICJzY3JvbGwiLA0KICAgICAgICAieCI6ICJjZW50ZXIiLA0KICAgICAgICAieSI6ICJ0b3AiLA0KICAgICAgICAiaWNvbiI6ICJjaXJjbGUiLA0KICAgICAgICAib3JpZW50IjogImhvcml6b250YWwiLA0KICAgICAgICAidGV4dFN0eWxlIjogew0KICAgICAgICAgICJjb2xvciI6ICIjQjlCOENFIiwNCiAgICAgICAgICAiZm9udFNpemUiOiAxOA0KICAgICAgICB9LA0KICAgICAgICAiaXRlbUhlaWdodCI6IDE1LA0KICAgICAgICAiaXRlbVdpZHRoIjogMTUsDQogICAgICAgICJwYWdlVGV4dFN0eWxlIjogew0KICAgICAgICAgICJjb2xvciI6ICIjQjlCOENFIg0KICAgICAgICB9DQogICAgICB9LA0KICAgICAgImdyaWQiOiB7DQogICAgICAgICJzaG93IjogZmFsc2UsDQogICAgICAgICJsZWZ0IjogIjEwJSIsDQogICAgICAgICJ0b3AiOiAiNjAiLA0KICAgICAgICAicmlnaHQiOiAiMTAlIiwNCiAgICAgICAgImJvdHRvbSI6ICI2MCINCiAgICAgIH0sDQogICAgICAiZGF0YXNldCI6IG51bGwsDQogICAgICAicmVuZGVyZXIiOiAic3ZnIg0KICAgIH0sDQogICAgInByZXZpZXdTY2FsZVR5cGUiOiAiZml0Ig0KICB9LA0KICAiY29tcG9uZW50TGlzdCI6IFtdLA0KICAicmVxdWVzdEdsb2JhbENvbmZpZyI6IHsNCiAgICAicmVxdWVzdERhdGFQb25kIjogW10sDQogICAgInJlcXVlc3RPcmlnaW5VcmwiOiAiIiwNCiAgICAicmVxdWVzdEludGVydmFsIjogMzAsDQogICAgInJlcXVlc3RJbnRlcnZhbFVuaXQiOiAic2Vjb25kIiwNCiAgICAicmVxdWVzdFBhcmFtcyI6IHsNCiAgICAgICJCb2R5Ijogew0KICAgICAgICAiZm9ybS1kYXRhIjoge30sDQogICAgICAgICJ4LXd3dy1mb3JtLXVybGVuY29kZWQiOiB7fSwNCiAgICAgICAgImpzb24iOiAiIiwNCiAgICAgICAgInhtbCI6ICIiDQogICAgICB9LA0KICAgICAgIkhlYWRlciI6IHt9LA0KICAgICAgIlBhcmFtcyI6IHt9DQogICAgfQ0KICB9DQp9";
    if($rs->fields['content']=="") {
        $rs->fields['content'] = $content;
    }
    $RS['data']['content']      = base64_decode($rs->fields['content']);
    $RS['content']              = json_decode(base64_decode($rs->fields['content']),true);
    print_R(json_encode($RS));
    exit;
}
else if($param1=="project" && $param2=="delete")  {
    CheckAuthUserLoginStatus();
    $id     = DecryptID($_GET['ids']);
    $sql    = "delete from data_goview_project where id='$id'";
    $rs     = $db->Execute($sql);
    $RS     = [];
    $RS['msg']  = "操作成功";
    $RS['code'] = 200;
    $RS['id']   = $_GET['ids'];
    print_R(json_encode($RS));

    global $FileStorageLocation;
    $FileStorageLocation = $FileStorageLocation."/GoView";
    $FilePath = $FileStorageLocation."/".$id."_index_preview.png";
    if (file_exists($FilePath))             {
        unlink($FilePath);
    }
    $FilePath = $FileStorageLocation."/".$id."_index_background.png";
    if (file_exists($FilePath))             {
        unlink($FilePath);
    }
    exit;
}
else if($param1=="project" && $param2=="copy")  {
    CheckAuthUserLoginStatus();
    $id     = DecryptID($_GET['ids']);
    $sql    = "INSERT INTO data_goview_project (`projectName`, `indexImage`, `remarks`, `isDelete`, `createUserId`, `createTime`, `content`) SELECT `projectName`, `indexImage`, `remarks`, `isDelete`, `createUserId`, `createTime`, `content` FROM data_goview_project WHERE id = '$id'";
    $rs     = $db->Execute($sql);
    $NewId  = $db->Insert_ID();
    global $FileStorageLocation;
    $FileStorageLocation = $FileStorageLocation."/GoView";
    if(is_file($FileStorageLocation."/".$id."_index_preview.png") && $NewId>0)  {
        copy($FileStorageLocation."/".$id."_index_preview.png", $FileStorageLocation."/".$NewId."_index_preview.png");
    }
    if(is_file($FileStorageLocation."/".$id."_index_background.png") && $NewId>0)  {
        copy($FileStorageLocation."/".$id."_index_background.png", $FileStorageLocation."/".$NewId."_index_background.png");
    }
    if(is_file($FileStorageLocation."/".$id."_index_preview.png") && $NewId>0)  {
        //$修改创建人和创建时间
        $createUserId = $GLOBAL_USER->USER_ID;
        $sql = "update data_goview_project set createUserId='$createUserId',createTime='".date('Y-m-d H:i:s')."' where id ='$NewId'";
        $db->Execute($sql);
    }
    $RS     = [];
    $RS['msg']  = "操作成功";
    $RS['code'] = 200;
    $RS['id']   = $_GET['ids'];
    print_R(json_encode($RS));
    exit;
}
else if($param1=="project" && $param2=="edit")  {
    CheckAuthUserLoginStatus();
    $payload        = file_get_contents('php://input');
    $_POST          = json_decode($payload,true);
    $id             = intval(DecryptID($_POST['id']));
    $projectName    = ForSqlInjection($_POST['projectName']);
    $sql    = "update data_goview_project set projectName='$projectName' where id='$id'";
    $rs     = $db->Execute($sql);
    $RS     = [];
    $RS['msg'] = "操作成功";
    $RS['code'] = 200;
    //$RS['sql'] = $sql;
    print_R(json_encode($RS));
    exit;
}
else if($param1=="project" && $param2=="list")  {
    CheckAuthUserLoginStatus();
    if($GLOBAL_USER->USER_ID!="admin" && $GLOBAL_USER->USER_PRIV!="1" && 0) {
        $RS['msg']      = "权限认证失败";
        $RS['code']     = 200;
        print_R(json_encode($RS));
        exit;
    }
    $page = intval($_GET['page']);
    if($page<1) $page = 1;
    $limit = intval($_GET['limit']);
    if($limit<6) $limit = 6;
    $from = ($page-1)*$limit;
    $sql    = "select id,projectName,state,indexImage,remarks,isDelete,createUserId,createTime from data_goview_project where projectName!='' and isDelete='-1' order by id desc limit ".$from.", ".$limit."";
    $rs     = $db->Execute($sql);
    $rs_a   = $rs->GetArray();
    $Counter = 0;
    foreach($rs_a as $Line) {
        $rs_a[$Counter]['id']           = EncryptIDFixed($Line['id']);
        $rs_a[$Counter]['indexImage']   = $BackEndApi."/".$rs_a[$Counter]['id'];
        $rs_a[$Counter]['state']        = intval($Line['state']);
        $rs_a[$Counter]['createUserId'] = intval($Line['createUserId']);
        $Counter ++;
    }
    $RS             = [];
    $RS['msg']      = "操作成功";
    $RS['code']     = 200;
    $RS['count']    = count($rs_a);
    $RS['data']     = $rs_a;
    //$RS['sql']      = $sql;
    $RS['_GET']     = $_GET;
    $RS['time']     = time();
    print_R(json_encode($RS));
    exit;
}
else if($param1=="project" && $param2=="myproject")  {
    CheckAuthUserLoginStatus();
    $page = intval($_GET['page']);
    if($page<1) $page = 1;
    $limit = intval($_GET['limit']);
    if($limit<6) $limit = 6;
    $from = ($page-1)*$limit;
    $createUserId   = $GLOBAL_USER->USER_ID;
    $sql    = "select id,projectName,state,indexImage,remarks,isDelete,createUserId,createTime from data_goview_project where projectName!='' and isDelete='-1' and createUserId='$createUserId' order by id desc limit ".$from.", ".$limit."";
    $rs     = $db->Execute($sql);
    $rs_a   = $rs->GetArray();
    $Counter = 0;
    foreach($rs_a as $Line) {
        $rs_a[$Counter]['id']           = EncryptIDFixed($Line['id']);
        $rs_a[$Counter]['indexImage']   = $BackEndApi."/".$rs_a[$Counter]['id'];
        $rs_a[$Counter]['state']        = intval($Line['state']);
        $rs_a[$Counter]['createUserId'] = intval($Line['createUserId']);
        $Counter ++;
    }
    $RS     = [];
    $RS['msg']      = "操作成功";
    $RS['code']     = 200;
    $RS['count']    = count($rs_a);
    $RS['data']     = $rs_a;
    //$RS['sql']      = $sql;
    $RS['_GET']     = $_GET;
    print_R(json_encode($RS));
    exit;
}
else if($param1=="project" && $param2=="listtemplate")  {
    $page = intval($_GET['page']);
    if($page<1) $page = 1;
    $limit = intval($_GET['limit']);
    if($limit<6) $limit = 6;
    $from = ($page-1)*$limit;
    $sql    = "select id,projectName,state,indexImage,remarks,isDelete,createUserId,createTime from data_goview_project where projectName!='' and isDelete='-1' and `state`='1' order by id desc limit ".$from.", ".$limit."";
    $rs     = $db->Execute($sql);
    $rs_a   = $rs->GetArray();
    $Counter = 0;
    foreach($rs_a as $Line) {
        $rs_a[$Counter]['id']           = EncryptID($Line['id']);
        $rs_a[$Counter]['indexImage']   = $BackEndApi."/".$rs_a[$Counter]['id'];
        $rs_a[$Counter]['state']        = intval($Line['state']);
        $rs_a[$Counter]['createUserId'] = intval($Line['createUserId']);
        $Counter ++;
    }
    $RS     = [];
    $RS['msg']      = "操作成功";
    $RS['code']     = 200;
    $RS['count']    = count($rs_a);
    $RS['data']     = $rs_a;
    //$RS['sql']      = $sql;
    $RS['_GET']     = $_GET;
    print_R(json_encode($RS));
    exit;
}
else if($param1=="project" && $param2=="publish")  {
    CheckAuthUserLoginStatus();
    $payload        = file_get_contents('php://input');
    $_POST          = json_decode($payload,true);
    $id             = intval(DecryptID($_POST['id']));
    $state          = intval($_POST['state']);
    $sql    = "update data_goview_project set state='$state' where id='$id'";
    $rs     = $db->Execute($sql);
    $RS     = [];
    $RS['msg'] = "操作成功";
    $RS['code'] = 200;
    //$RS['sql'] = $sql;
    print_R(json_encode($RS));
    exit;
}
else if($param1=="project" && $param2=="save" && $param3=="data")  {
    CheckAuthUserLoginStatus();
    $id             = intval(DecryptIDFixed($_POST['projectId']));
    $content        = base64_encode($_POST['content']);
    $sql    = "update data_goview_project set content='$content' where id='$id'";
    $rs     = $db->Execute($sql);
    if($rs->EOF) {
        $RS     = [];
        $RS['projectId'] = $id;
        $RS['msg'] = "操作成功";
        $RS['code'] = 200;
        $RS['_POST'] = $_POST;
        $RS['content'] = json_decode($_POST['content'],true);
        //$RS['sql'] = $sql;
        print_R(json_encode($RS));
        exit;
    }
    else {
        $RS     = [];
        $RS['projectId'] = $id;
        $RS['msg'] = "保存失败";
        $RS['code'] = 200;
        $RS['_POST'] = $_POST;
        $RS['content'] = json_decode($_POST['content'],true);
        //$RS['sql'] = $sql;
        print_R(json_encode($RS));
        exit;
    }
}
else if($param1=="image" && $param2!="")  {
    $ID = DecryptID($param2);
    global $FileStorageLocation;
    $FileStorageLocation = $FileStorageLocation."/GoView";
    $FilePath = $FileStorageLocation."/".$ID."_index_preview.png";
    if (!file_exists($FilePath))             {
        $FilePath = $FileStorageLocation."/0_index_preview.png";
    }
    $imageType = exif_imagetype($FilePath);
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            header('Content-Type: image/jpeg');
            break;
        case IMAGETYPE_PNG:
            header('Content-Type: image/png');
            break;
        case IMAGETYPE_GIF:
            header('Content-Type: image/gif');
            break;
        default:
            header('Content-Type: image/png');
            break;
    }
    readfile($FilePath);
    exit;
}
else if($param1=="bucket" && $param2!="")  {
    $param2 = ForSqlInjection($param2);
    global $FileStorageLocation;
    $FileStorageLocation = $FileStorageLocation."/GoView";
    $FilePath = $FileStorageLocation."/".$param2;
    if (file_exists($FilePath))             {
        $imageType = exif_imagetype($FilePath);
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                header('Content-Type: image/jpeg');
                break;
            case IMAGETYPE_PNG:
                header('Content-Type: image/png');
                break;
            case IMAGETYPE_GIF:
                header('Content-Type: image/gif');
                break;
            default:
                header('Content-Type: image/png');
                break;
        }
        readfile($FilePath);
        exit;
    }
    exit;
}
else if($param1=="project" && $param2=="upload")  {
    CheckAuthUserLoginStatus();
    global $FileStorageLocation;
    $FileStorageLocation = $FileStorageLocation."/GoView";
    if(!is_dir($FileStorageLocation)) {
        mkdir($FileStorageLocation);
    }
    if(strpos($_FILES['object']['name'],"_index_preview.png")>0 && isset($_FILES) && isset($_FILES['object']) && is_file($_FILES['object']['tmp_name']) ) {
        $EncryptID  = str_replace("_index_preview.png","",$_FILES['object']['name']);
        $ID         = DecryptID($EncryptID);
        if($ID>0)    {
            $NewFilePath = $FileStorageLocation."/".$ID."_index_preview.png";
            //print $NewFilePath;
            copy($_FILES['object']['tmp_name'], $NewFilePath);
            $Element = [];
            $Element['bucketName'] = NULL;
            $Element['createTime'] = date("Y-m-d H:i:s");
            $Element['createUserId'] = 'admin';
            $Element['createUserName'] = 'admin';
            $Element['fileName']    = $ID."_index_preview.png";
            $Element['fileSize']    = filesize($ID."_index_preview.png");
            $Element['fileSuffix']  = "image/png";
            $Element['id']          = $ID;
            $Element['updateTime'] = NULL;
            $Element['updateUserId'] = NULL;
            $Element['updateUserName'] = NULL;
            print_R(json_encode($Element));
        }
        else {
            $Element = [];
            $Element['code'] = 218;
            $Element['msg'] = "ID值不合法";
            print_R(json_encode($Element));
        }
    }
    else if(strpos($_FILES['object']['name'],"_index_background.png")>0 && isset($_FILES) && isset($_FILES['object']) && is_file($_FILES['object']['tmp_name']) ) {
        $EncryptID  = str_replace("_index_background.png","",$_FILES['object']['name']);
        $ID         = DecryptID($EncryptID);
        if($ID>0)    {
            $NewFilePath = $FileStorageLocation."/".$ID."_index_background.png";
            //print $NewFilePath;
            copy($_FILES['object']['tmp_name'], $NewFilePath);
            $Element = [];
            $Element['bucketName'] = NULL;
            $Element['createTime'] = date("Y-m-d H:i:s");
            $Element['createUserId'] = 'admin';
            $Element['createUserName'] = 'admin';
            $Element['fileName']    = $ID."_index_background.png";
            $Element['fileSize']    = filesize($ID."_index_background.png");
            $Element['fileSuffix']  = "image/png";
            $Element['id']          = $ID;
            $Element['updateTime'] = NULL;
            $Element['updateUserId'] = NULL;
            $Element['updateUserName'] = NULL;
            $RS = [];
            $RS['code'] = 200;
            $RS['data'] = $Element;
            print_R(json_encode($RS));
        }
        else {
            $Element = [];
            $Element['code'] = 218;
            $Element['msg'] = "ID值不合法";
            print_R(json_encode($Element));
        }
    }
    else {
        $Element = [];
        $Element['code']    = 217;
        $Element['_FILES']  = $_FILES;
        $Element['msg']     = "UPLOAD时上传数据不合法";
        print_R(json_encode($Element));
    }
    exit;
}
else if($param1=="sys" && $param2=="login")  {

}
else if($param1=="sys" && $param2=="logout")  {
    $RS     = [];
    $RS['msg'] = "退出成功!";
    $RS['code'] = 200;
    $RS['_POST'] = $_POST;
    print_R(json_encode($RS));
    exit;
}

?>
