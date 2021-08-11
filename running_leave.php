<?php
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/my_func.inc.php');
require_once('./include/const.inc.php');
require_once('./include/setlang.php');
if(isset($_GET['cid']))
{
    $cid=$_GET['cid'];
    $user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
    $sql="SELECT `start_time` FROM `contest` where `contest_id`=?";
    $now=time();
    $result=pdo_query($sql,$cid)[0]['start_time'];
    $start_time=strtotime($result);
    if($now>=$start_time)
    {
        echo "训练赛已经开始，无法请假。";
        exit();
    }
    $sql="SELECT * FROM `contest_login_time` where `contest_id`=? AND `user_id`=?";
    $result=pdo_query($sql,$cid,$user_id);
    if(count($result)==0)
    {
        $is_leave=false;
    }
    else
    {
        $is_leave=true;
        $reason=$result[0]['reason'];
    }
}
if(isset($_POST['submit_submit']))
{
    $cid=$_POST['cid'];
    $user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
    $contents = $_POST['contents'];
    $sql="INSERT INTO `contest_login_time`(`contest_id`,`user_id`,`reason`) values(?,?,?)";
    $result=pdo_query($sql,$cid,$user_id,$contents);
    echo "请假成功！";
    echo "3s后将关闭本窗口";
    echo "<script>setTimeout(function (){window.close();}, 3000); </script>";
    exit();
}
if(isset($_POST['submit_change']))
{
    $cid=$_POST['cid'];
    $user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
    $contents = $_POST['contents'];
    $sql="UPDATE `contest_login_time` set `reason`=? where `contest_id`=? AND `user_id`=?";
    $result=pdo_query($sql,$contents,$cid,$user_id);
    echo "修改成功！";
    echo "3s后将关闭本窗口";
    echo "<script>setTimeout(function (){window.close();}, 3000); </script>";
    exit();
}
if(isset($_POST['submit_delate']))
{
    $cid=$_POST['cid'];
    $user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
    $contents = $_POST['contents'];
    $sql="DELETE FROM `contest_login_time` where `contest_id`=? AND `user_id`=?";
    $result=pdo_query($sql,$cid,$user_id);
    echo "销假成功！";
    echo "3s后将关闭本窗口";
    echo "<script>setTimeout(function (){window.close();}, 3000); </script>";
    exit();
}
?>
<head>
</head>
<body>
<div style="float:left; width:500px; margin-left:50px">
<h2>训练赛请假界面</h2>
<span>注：<br>1. 请假后允许不参加训练赛并可以参加补题赛。<br>2. 如果未请假且未参加训练赛，则不允许参加补题赛。<br>3. 需填写请假理由并且经由管理员审核。</text> </span>
<form action="running_leave.php" method="post" >
<br>
<text >请假理由：</text>
<br>
<textarea cols="30" rows="10"  name="contents"  style="width:500px;height:150px;margin-top:20px"><?php echo $reason; ?></textarea>
<?php if(!$is_leave) { ?>
<input type="submit" name="submit_submit" value="提交" style="margin-left:250px;margin-top:20px">
<?php } else { ?>
<input type="submit" name="submit_change" value="修改" style="margin-left:210px;margin-top:20px">
<input type="submit" name="submit_delate" value="销假" style="margin-left:20px;margin-top:20px">
<?php } ?>
<input type="text" name="cid" value="<?php echo $cid ?>" style="display:none;">
</form>
</div>
</body>