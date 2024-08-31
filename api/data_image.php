<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');

//$externalId = 16;

//CheckAuthUserLoginStatus();
$DATA = DecryptID($_GET['DATA']);
$DATA = unserialize($DATA);

$AttachName = $DATA['FieldName'];
$Type       = $DATA['Type'];
$TableName  = $DATA['TableName'];
$Index      = intval($DATA['Index']);
$id         = intval($DATA['Id']);
$FilePath   = "";
$AttachValue = returntablefield($TableName,"id",$id,$AttachName)[$AttachName];


if($Type=="files")              {
    if($AttachValue!="")    {
        $AttachArray    = explode("||",$AttachValue);
        $FieldName      = explode("*",$AttachArray[0])[$Index];
        $FieldId        = explode(",",$AttachArray[1])[$Index];
        $FieldIdArray   = explode("_",$FieldId);

        $YM             = $FieldIdArray[0];
        $FileStorageLocation = $FileStorageLocation."/".$YM;
        $FilePath = $FileStorageLocation."/".$FieldIdArray[1].".".$FieldName;
    }
    if (!file_exists($FilePath)) {
        $FilePath = "./images/avatars/2.png";
    }
    $imageType = exif_imagetype($FilePath);
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            header('Content-Type: image/jpeg');
            readfile($FilePath);
            break;
        case IMAGETYPE_PNG:
            header('Content-Type: image/png');
            readfile($FilePath);
            break;
        case IMAGETYPE_GIF:
            header('Content-Type: image/gif');
            readfile($FilePath);
            break;
        default:
            //Download File
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $FieldName);
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($FilePath));
            readfile($FilePath);
            break;
    }
    exit;
}

if($Type=="images")              {
    if($AttachValue!="")    {
        $AttachArray    = explode("||",$AttachValue);
        $FieldName      = explode("*",$AttachArray[0])[$Index];
        $FieldId        = explode(",",$AttachArray[1])[$Index];
        $FieldIdArray   = explode("_",$FieldId);

        $YM             = $FieldIdArray[0];
        $FileStorageLocation = $FileStorageLocation."/".$YM;
        $FilePath = $FileStorageLocation."/".$FieldIdArray[1].".".$FieldName;
    }
    if (!file_exists($FilePath)) {
        $FilePath = "./images/avatars/2.png";
    }
    

    $imageType = exif_imagetype($FilePath);
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($FilePath));
            header('Content-Type: image/jpeg');
            readfile($FilePath);
            break;
        case IMAGETYPE_PNG:
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($FilePath));
            header('Content-Type: image/png');
            readfile($FilePath);
            break;
        case IMAGETYPE_GIF:
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($FilePath));
            header('Content-Type: image/gif');
            readfile($FilePath);
            break;
        default:
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($FilePath));
            header('Content-Type: image/jpeg');
            readfile($FilePath);
            break;
    }
    exit;
}

if($Type=="avatar")             {
    if($AttachValue!="")    {
        $AttachArray = explode("||",$AttachValue);
        $IdArray     = explode('_',$AttachArray[1]);
        
        $FileStorageLocationYM = $FileStorageLocation."/".$IdArray[0];
        $FilePath = $FileStorageLocationYM."/".$IdArray[1].".".$AttachArray[0];
    }
    if (!file_exists($FilePath)) {
        $FilePath = "./images/avatars/2.png";
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

/*
if(is_file($FilePath))  {
        $filesize = filesize($FilePath);
		$file = fopen($FilePath,"r");
		header('Pragma: no-cache');
		header("Cache-control: private");
		header("Content-type: image/jpg");
		header("Content-Length: $filesize");
		header("Content-Disposition: attachment; filename=\"".urldecode($AttachArray[1])."\"");
		header("Content-Description: ".$_SERVER['SERVER_NAME']);
		echo fread($file,$filesize);
		fclose($file);
		exit;
    }
     */

?>