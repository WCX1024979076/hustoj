<?php
    /**
     * 修改人：王春祥
    * 修改日期：2021/1/18
    * 修改目的：Ajax发送post接收用
    */
	require_once('./include/cache_start.php');
    require_once('./include/db_info.inc.php');
	require_once('./include/setlang.php');
	header('content-type:text/html;charset="utf-8"');
    $time=$_POST['time'];
    $cid=(int)$_POST['cid'];
    $ip = ($_SERVER['REMOTE_ADDR']);
    $now=date('Y-m-d H:i:s', time());  
    $time=$now;                     ///防止恶意提交错误时间到数据库中
    $user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
    $sql = "UPDATE contest_login_time set `login_ip`=?,`login_time`=? where `user_id`=? and `contest_id`=? ";
	$resultsql1 = pdo_query($sql,$ip,$time,$user_id,$cid);
    
?>