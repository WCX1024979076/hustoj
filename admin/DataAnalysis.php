<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>可视化分析队员做题数目</title>
	<!-- 引入 echarts.js -->
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
	<?php
	/**
	 * 修改人：王春祥
	 * 修改日期：2021/1/28
	 * 修改目的：可视化分析队员做题数据
	 * 主要修改内容：php动态生成js代码，然后js本地渲染
	 */
	//require_once("admin-header.php");
	require_once("../include/db_info.inc.php");
	require_once("../include/my_func.inc.php");
	if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))){
		echo "<a href='../loginpage.php'>Please Login First!</a>";
		exit(1);
	  }
	echo "<hr>";
	echo "<center><h3>队员做题数据分析</h3></center>";
	include_once("kindeditor.php");
	?>

	<?php
	require_once("../include/db_info.inc.php");
	require_once("../lang/$OJ_LANG.php");
	require_once("../include/const.inc.php");
	$user_name = [];
	$U = [];
	$row_cnt = 0;
	$contest_id = "1007,1008";
	?>
	<?php
	class TM
	{
		var $solved = 0;
		var $solve_array;
		var $user_id;
		var $nick;
		function TM()
		{
			$this->solved = 0;
			$this->solve_array = array();
			$this->user_id = "";
			$this->nick = "";
		}
		function Add($cid, $ac_num)
		{
			$this->solved += $ac_num;
			$this->solve_array[$cid] = $ac_num;
		}
	}

	function s_cmp($A, $B)
	{
		return $A->solved < $B->solved;
	}
	?>
	<?php
	function clear()
	{
		global $user_name, $U, $row_cnt;
		$user_name = [];
		$U = [];
		$row_cnt = 0;
	}
	function sql_solution($contest_id)
	{
		global $U;
		$U = array();
		$sql = "SELECT `contest_id`,`user_id`,count(distinct problem_id),`nick`
	FROM solution
	where contest_id in (" . $contest_id . ") and result=4 and num>=0 and problem_id>0
	group by contest_id,user_id,nick  ORDER BY user_id";
		$result = pdo_query($sql);
		$user_cnt = 0;
		$user_name = '';
		foreach ($result as $row) {
			$n_user = $row['user_id'];
			if (strcmp($user_name, $n_user)) {
				$user_cnt++;
				$U[$user_cnt] = new TM();
				$U[$user_cnt]->user_id = $row['user_id'];
				$U[$user_cnt]->nick = $row['nick'];
				$user_name = $n_user;
			}
			$U[$user_cnt]->Add($row['contest_id'], $row["count(distinct problem_id)"]);
		}
		usort($U, "s_cmp");
	}
	?>

	<div class="container">
		<?php
		if (isset($_POST['contest_id'])) {
			require_once("../include/check_post_key.php");
			$contest_id = $_POST['contest_id'];
			clear();
			sql_solution($contest_id);
			$contest_list = trim($contest_id);
			$contest_pieces = explode(",", $contest_list);
			$sql = "SELECT title FROM contest where contest_id=?";
			foreach ($contest_pieces as $row) {
				$contest_content[] = array("title" => pdo_query($sql, $row)[0][0], "contest_id" => $row);
			}
		}
		?>
		<form method=POST action=DataAnalysis.php>
			<p align=left>
				<label class="col control-label"><?php echo "比赛编号" ?></label>
				<input type=text name='contest_id' size=71 style="width:600px;" value='<?php echo $contest_id ?>'>
				<br>
				<text> 注：比赛ID之间用','（英）来分隔。</text>
			</p>
			<?php require_once("../include/set_post_key.php"); ?>
			<p>
				<center>
					<input type=submit value='<?php echo "提交" ?>' name=submit>
				</center>
			</p>
		</form>
	</div>
	<text> </text>

	<div class="panel-body">
		<table class="table table-hover" style="width:80%;margin-left:10%">
			<thead>
				<tr>
					<th>用户</th>
					<th>昵称</th>
					<th>解决</th>
					<?php
					foreach ($contest_content as $row)
						echo "<th><a href='../running.php?cid={$row["contest_id"]}' class='tooltip-test' data-toggle='tooltip' title='{$row["title"]}'>{$row["contest_id"]}</a></th>";
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$row_cnt = count($U);
				for ($i = 0; $i < $row_cnt; $i++) {
					$user_name[$i] = $U[$i]->nick;
					$training_ac[$i] = $U[$i]->solved;
					echo "<tr>";
					echo "<td><a href='../userinfo.php?user={$U[$i]->user_id}'>{$U[$i]->user_id}</a></td>";
					echo "<td>{$U[$i]->nick}</td>";
					echo "<td>{$U[$i]->solved}</td>";
					foreach ($contest_content as $row) {
						echo "<td>{$U[$i]->solve_array[$row['contest_id']]}</td>";
					}
					echo "</tr>";
				}
				?>
			</tbody>
		</table>
	</div>
	<script>
		$(function() {
			$("[data-toggle='tooltip']").tooltip();
		});
	</script>
</body>

</html>