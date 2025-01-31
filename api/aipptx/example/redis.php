<?php
require_once('../config.inc.php');

$Data = $redis->hGetAll("PPTX_CONTENT_".date('Ymd'));
print_R($Data);

$Data = $redis->hGetAll("PPTX_DOWNLOAD");
print_R($Data);




?>
