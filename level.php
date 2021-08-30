<?php

/**
 * 修改人：王春祥
 * 修改日期：2021/2/19
 * 修改目的：段位赛前台界面
 */
if (isset($_POST['keyword']))
  $cache_time = 1;
else
  $cache_time = 10;

$OJ_CACHE_SHARE = false; //!(isset($_GET['cid'])||isset($_GET['my']));
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/my_func.inc.php');
require_once('./include/const.inc.php');
require_once('./include/setlang.php');


$view_title = "段位赛";

$sql = "SELECT * FROM `level_list`";
$result_level = pdo_query($sql);
$view_total_level = count($result_level);
$user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];
$sql = "SELECT `level_id` FROM `users` where `user_id`=?";
$user_level = pdo_query($sql, $user_id)[0]['level_id'];


if (isset($_GET['level_id']))
  $level_id = intval($_GET['level_id']);
else
  $level_id = $user_level;
if ($level_id > $user_level)
  $error_msg = "未达到段位要求，请返回。<a href=" . "/level.php?level_id=" . $user_level . ">点击跳转</a>";

$sql = "SELECT `easy_cid`,`hard_cid` FROM level_list WHERE level_id=?";
$result_cid = pdo_query($sql, $level_id)[0];
$easy_cid = $result_cid[0];
$hard_cid = $result_cid[1];
///检查基础题目是否全部完成

$sql = "SELECT * FROM `contest` WHERE `contest_id`=?";;
$easy_cid = pdo_query($sql, $easy_cid)[0];
$hard_cid = pdo_query($sql, $hard_cid)[0];

$sql = "SELECT count(distinct `problem_id`) FROM solution WHERE user_id=? and contest_id=? and result=4";
$easy_cid['ac_num'] = pdo_query($sql, $_SESSION[$OJ_NAME . '_user_id'], $easy_cid['contest_id'])[0][0];
$hard_cid['ac_num'] = pdo_query($sql, $_SESSION[$OJ_NAME . '_user_id'], $hard_cid['contest_id'])[0][0];

$sql = "SELECT count(distinct `problem_id`) FROM contest_problem WHERE contest_id=?";
$easy_cid['all_num'] = pdo_query($sql, $easy_cid['contest_id'])[0][0];
$hard_cid['all_num'] = pdo_query($sql, $hard_cid['contest_id'])[0][0];

if (isset($_GET['level_up']))   ///晋级选项
{
  $sql = "SELECT count(distinct `problem_id`) FROM solution WHERE user_id=? and contest_id=? and result=4";
  $easy_ac = pdo_query($sql, $_SESSION[$OJ_NAME . '_user_id'], $easy_cid['contest_id'])[0][0];
  $hard_ac = pdo_query($sql, $_SESSION[$OJ_NAME . '_user_id'], $hard_cid['contest_id'])[0][0];

  if ($easy_ac + $hard_ac >= $result_level[$user_level - 1]['level_problem_min'] && $user_level != $view_total_level) {
    $sql = "UPDATE users set level_id=level_id+1 where user_id=?";
    pdo_query($sql, $user_id);
    //header("Location: level.php");
  }
}


if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) ///管理员
  $error_msg = NULL;

////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/level.php");
/////////////////////////Common foot/
if (file_exists('./include/cache_end.php'))
  require_once('./include/cache_end.php');
