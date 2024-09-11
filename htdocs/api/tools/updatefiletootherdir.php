<?php

$开发库 	= "D:\MYEDUDEV\icampus-student-affairs-manage";

$子目录 	= [];
$子目录[] 	= "/api";
$子目录[] 	= "/api/plugins";
$子目录[] 	= "/api/apps";
$子目录[] 	= "/api/database";
$子目录[] 	= "/api/exam";
$子目录[] 	= "/api/charts";
$子目录[] 	= "/api/tools";
$子目录[] 	= "/api/goview";
$子目录[] 	= "/api/wechat";

$子目录[] 	= "/src/pages";
$子目录[] 	= "/src/pages/Enginee";
$子目录[] 	= "/src/pages/form";
$子目录[] 	= "/src/pages/login";
$子目录[] 	= "/src/pages/tab";
$子目录[] 	= "/src/pages/user";
$子目录[] 	= "/src/pages/apps";
$子目录[] 	= "/src/pages/dashboards";
$子目录[] 	= "/src/views/dashboards/analytics";
$子目录[] 	= "/src/views/charts/apex-charts";
$子目录[] 	= "/src/views/pages/dialog-examples/create-app-tabs";

$子目录[] 	= "/public/images/screenshot";


同步库($开发库, $目标库="G:\icampus-exam", $子目录);
同步库($开发库, $目标库="G:\chives-react-admin", $子目录);
同步库($开发库, $目标库="D:\MYEDUDEV\Gitee-icampus-student-affairs-manage", $子目录);
同步库($开发库, $目标库="D:\MYEDUDEV\datacenter", $子目录);


function 同步库($开发库, $目标库, $子目录=[])  {
	foreach($子目录 as $ITEM)   {
		$dir = $开发库 . $ITEM;
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false ) 		{
					if( $file!="." && $file!=".." && is_file($dir ."/". $file) )    	{
						$ModifyTime = filemtime ($dir ."/". $file);
						$SectionTime = time() - $ModifyTime;
						$SectionTime = intval($SectionTime/3600);
						if($SectionTime<8)   {
							print $dir ."/". $file . " $SectionTime<BR>";
							if (!is_dir($目标库 . $ITEM)) {
								mkdir($目标库 . $ITEM);
							}
							copy($dir ."/". $file, $目标库 . $ITEM ."/". $file);
						}
					}
				}
				closedir($dh);
			}
		}
	}
}



?>