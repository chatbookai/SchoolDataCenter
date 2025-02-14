<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, cache-control, Authorization, X-Requested-With, satoken");
header("Content-type: text/html; charset=utf-8");
header('Cache-Control: no-cache');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../include.inc.php');

$sql      = "select * from data_schoolnewsgroup where 启用菜单 = '是' order by 排序号 asc";
$rs       = $db->Execute($sql);
$rs_a     = (array)$rs->GetArray();

$Header   = [];
foreach($rs_a as $Item)  {
  $一级分组 = $Item['一级分组'];
  $二级分组 = $Item['二级分组'];
  $页面类型 = $Item['页面类型'];
  $外部链接 = $Item['外部链接'];
  $特殊页面 = $Item['特殊页面'];
  $Header[$一级分组][$二级分组] = $Item;
}

$一级分组Array = array_keys($Header);

$Menus    = [];
$Menus[]  = [ 'title'=>'首页', 'target'=>'', 'href'=>'/home', 'default'=>true ];

foreach($Header as $一级分组 => $二级分组) {
  $子菜单  = [];
  foreach($二级分组 as $二级分组名称 => $菜单信息) {
    $子菜单[] = [ 'title'=>$二级分组名称, 'target'=>'', 'href'=>$菜单信息['外部链接'] ];
  }
  $Menus[]  = [ 'title'=>$一级分组, 'target'=>'', 'href'=>'', 'default'=>false, 'children' => $子菜单 ];
}

print_R(json_encode($Menus));

?>
