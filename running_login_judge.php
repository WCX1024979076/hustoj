<?php
/**
* 修改人：王春祥
* 修改日期：2021/1/18
* 修改目的：判断是否已经确定参加比赛
* 主要修改内容：判断sql中是否有数据，如果没有，跳转cntest_login
*/
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/my_func.inc.php');
require_once('./include/const.inc.php');
require_once('./include/setlang.php');

$login_ok=false;
$user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];

$ip = ($_SERVER['REMOTE_ADDR']);

$sql = "SELECT * FROM `contest_login_time` WHERE `contest_id`=? and `user_id`=?";
$resultsql_login= pdo_query($sql,$cid,$user_id);
$login_ip=$resultsql_login[0]["login_ip"];

$sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
$resultsql = pdo_query($sql,$cid);

$login_ip_limit=$resultsql[0]['login_ip_limit']; //登陆ip限制
if(!$login_ip_limit)
    $login_ok=true;
else
{
    if($login_ip==NULL)
        $login_ok=false;
    else if($ip==$login_ip)
        $login_ok=true;
    else if($ip!=$login_ip)
    {
        $cid=-$cid;
        header("Location: ./running_login.php?view_cid=".$cid);
    }
}


if(!$login_ok)
   header('Location: ./running_login.php?view_cid='.$cid);
    
//结束
?>