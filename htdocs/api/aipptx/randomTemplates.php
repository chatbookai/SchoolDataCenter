<?php
header("Content-Type: application/json");
require_once('../cors.php');
require_once('../include.inc.php');
ini_set('max_execution_time', 7200);


$sql = "select 编码 as id, 类型 as type, 目录 as category, 风格 as style, 皮肤颜色 as themeColor, 语言 as lang, 动画 as animation, 主题 as subject, 封面URL as coverUrl, 排序 as sort, 页码 as num, 创建时间 as createTime, 创建人 as createUser  from data_ai_pptx_templates";
$rs = $db->CacheExecute(60, $sql);
$rs_a = $rs->GetArray();

$RS = [];
$RS['data'] = $rs_a;
$RS['code'] = 0;
$RS['message'] = 'ok';

print json_encode($RS);

?>
