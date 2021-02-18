<?php

if(isset($_POST['keyword']))
  $cache_time = 1;
else
  $cache_time = 10;

$OJ_CACHE_SHARE = false;//!(isset($_GET['cid'])||isset($_GET['my']));
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/my_func.inc.php');
require_once('./include/const.inc.php');
require_once('./include/setlang.php');
$view_title= "段位赛";

////////////////////////Template
require("template/".$OJ_TEMPLATE."/level.php");
/////////////////////////Common foot/
if (file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>
