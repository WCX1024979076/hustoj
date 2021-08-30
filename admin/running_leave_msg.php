<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>训练赛参加信息</title>
</head>
<body>

<?php
require_once("admin-header.php");
if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))){
    echo "<a href='../loginpage.php'>Please Login First!</a>";
    exit(1);
  }
require_once("../include/db_info.inc.php");
require_once("../lang/$OJ_LANG.php");
require_once("../include/const.inc.php");
require_once('../include/memcache.php');
require_once('../include/my_func.inc.php');
require_once('../include/const.inc.php');
require_once('../include/setlang.php');
$contest_id=1000;
?>
<center>
<h2>训练赛参加信息</h2>
<center>
<div class="container">
	<form method=POST action=running_leave_msg.php>
		<p align=left>
			<label class="col control-label"><?php echo "比赛编号"?></label>
				<input type=text name='contest_id' size=71 style="width:400px;" value='<?php echo $contest_id?>'>
			<br>
			<text> 只能输入一场训练赛编号 </text>
		</p>
		<?php require_once("../include/set_post_key.php");?>
		<p>
			<center>
				<input type=submit value='<?php echo "提交"?>' name=submit>
			</center>
		</p>
	</form>
    <?php
		if(isset($_POST['contest_id']))
		{
			//require_once("../include/check_post_key.php");
			$cid=$_POST['contest_id'];
			$sql="SELECT * FROM `contest_login_time`,`users` where users.user_id=contest_login_time.user_id AND contest_id=? AND `login_time` is not null";
            $result=pdo_query($sql,$cid);
            echo "<span style='float:left;font-size:15px;clear:both;'>训练赛首次登陆时间：</span>";
            echo '<br><br>';
            echo "<table border='1' style='float:left;clear:both;'>";
            echo "<tr><th width=100px>姓名</th><th width=300px>登陆时间</th></tr>";
            foreach($result as $row)
            {
                echo "<tr><td>".$row['nick']."</td><td>".$row['login_time']."</td></tr>";
            }
            echo "</table>";

            $sql="SELECT * FROM `contest_login_time`,`users` where users.user_id=contest_login_time.user_id AND contest_id=? AND `login_time` is null";
            $result=pdo_query($sql,$cid);
            echo "<span style='float:left;font-size:15px;clear:both;'><br>训练赛请假人员及原因：<br><br></span>";
            echo '<br><br>';
            echo "<table border='1' style='float:left;clear:both;'>";
            echo "<tr><th width=100px>姓名</th><th width=300px>请假原因</th></tr>";
            foreach($result as $row)
            {
                echo "<tr><td>".$row['nick']."</td><td>".$row['reason']."</td></tr>";
            }
            echo "</table>";
		}
	?>
</div>
</body>
</html>