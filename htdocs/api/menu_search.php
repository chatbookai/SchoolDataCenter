<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');

$RS = [];

print_R(json_encode($RS));

?>