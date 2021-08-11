<?php
 /**  
  * 修改人：王春祥
  * 修改日期：2021/1/18
  * 修改目的：增加比赛确认界面
  */
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
$view_title= $MSG_CONTEST;
require("template/".$OJ_TEMPLATE."/error.php");
if (file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>
<html> 
<head> 
<meta charset="utf-8" /> 
<style> 
body{ text-align:left} 
.div{ margin:0 auto; width:850px; height:200px; border:1px solid #F00} 
/* css注释：为了观察效果设置宽度 边框 高度等样式 */ 
</style> 
</head> 
<body> 
<h3  style="text-align:center;">
<?php
$view_cid=$_GET['view_cid'];
$view_cid=intval($view_cid);
if($view_cid<=0)
	echo "多IP登陆警告";
else
	echo "训练赛确认界面";
?>
<?php
$cid=abs($view_cid);
$user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
$sql="SELECT * FROM contest_login_time where user_id=? and contest_id=?";
$result=pdo_query($sql,$user_id,$cid)[0];
$ip = ($_SERVER['REMOTE_ADDR']);
?>
</h3>	
<div class="div" style="<?php if($view_cid<=0) echo "display:none" ?>"> 
您还没确认参加本次比赛！<br>
在确认之前，请您仔细阅读下列要求：<br>
1.为防止训练赛作弊，在本次训练赛当中只允许用一个ip地址来登录，您现在的IP地址为<?php echo $ip ?><br>
2.点击确定，将代表您开始参加训练赛。点击取消，则返回比赛列表界面。<br>
3.本文本框文字待修改完善，欢迎提出意见。
</div> 
<div class="div" style="<?php if($view_cid>0) echo "display:none" ?>"> 
你不能用本IP地址来参加本次训练赛！<br>
1.您已于<?php echo $result['login_time']?> 在IP地址为 <?php echo $result['login_ip']?> 确认过参加训练赛。<br>
2.您目前的IP地址为<?php echo $ip ?><br>
3.本文本框文字待修改完善，欢迎提出意见。
</div> 
<br>
<div align="center">
<?php if($view_cid>0)
		echo '<button name="btn" type="submit" id="btn" style="width:100px;  ">确定</button>';
	else
		echo '<button onclick="window.location.href=\'running.php\'" name="submit" type="submit" style="width:100px;">确定</button>';
?>
<?php for($i=0;$i<=5;$i++) 
	echo "&nbsp"; 
?>
<button onclick="window.location.href='./contest.php'" name="submit" type="submit" style="width:100px;">取消</button>
</div>
<br>
</body> 
<script src="https://s3.pstatp.com/cdn/expire-1-M/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
		function current() {
				var d = new Date(),
					str = '';
				str += d.getFullYear() + '-';
				str += d.getMonth() + 1 + '-';
				str += d.getDate() + ' ';
				str += d.getHours() + ':';
				str += d.getMinutes() + ':';
				str += d.getSeconds() + '';
				return str;
		}
		var oBtn=document.getElementById("btn");
		oBtn.onclick=function()
		{				//给按钮设置点击事件
			var jsonObj = {"time":current(),"cid":<?php echo $cid; ?> };
			myajax=$.ajax({
				type:"post",						//提交方式
				url:"running_login_post.php",		//执行的url(控制器/操作方法)
				async:true,							//是否异步
				data:jsonObj,						//获取form表单的数据
				datatype:'json',					//数据格式
				success:function(data){
					console.log(data);				//打印
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					alert(XMLHttpRequest.status);
					alert(XMLHttpRequest.readyState);
					alert(textStatus);
				}
			});
			$.when(myajax).done(function () {
				window.location.href = './running.php?cid=<?php echo $cid; ?>';
        	});
			return false;
		}
	</script>
</html> 