<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">

	<title>
		<?php echo $OJ_NAME ?>
	</title>
	<?php include("template/$OJ_TEMPLATE/css.php"); ?>


	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<!-- 
		* 修改人：王春祥
		* 修改日期：2021/2/19
		* 修改目的：段位赛前台界面
	-->
</head>

<body>

	<div class="container">
		<?php include("template/$OJ_TEMPLATE/nav.php"); ?>
		<!-- Main component for a primary marketing message or call to action -->
		<div class="jumbotron">
			<center>
				<nav id="page_my" class="center">
					<small>
						<ul class="pagination">
							<li class="page-item"><a href="level.php?level_id=1">&lt;&lt;</a>
							</li>
							<?php
							if (!isset($level_id)) $level_id = 0;
							$level_id = intval($level_id);
							$section = 8;
							$start = $level_id > $section ? $level_id - $section : 1;
							$end = $level_id + $section > $view_total_level ? $view_total_level : $level_id + $section;
							for ($i = $start; $i <= $end; $i++) {
								echo "<li class='" . ($level_id == $i ? "active " : "") . "page-item'> <a href='level.php?level_id=" . $i . "'>" . $result_level[$i - 1]['level_name'] . "</a></li>";
							}
							?>
							<li class="page-item"><a href="level.php?level_id=<?php echo $view_total_level ?>">&gt;&gt;</a>
							</li>
						</ul>
					</small>
				</nav>
				<ul class="pagination">
					<?php
					if ($easy_cid['ac_num'] + $hard_cid['ac_num'] >= $result_level[$level_id - 1]['level_problem_min'] && $level_id == $user_level && $user_level != $view_total_level)
						echo "<li class='" . "page-item'> <a href='level.php?level_up=1	'>" . 可晋级 . "</a></li>";
					else
						echo "<li class='" . "page-item'> <a href=''>" . 不可晋级 . "</a></li>";
					?>

				</ul>
				<?php echo "<br>当前段位：" . $result_level[$user_level - 1]['level_name'] . "，\t"; ?>
				<?php echo $result_level[$level_id - 1]['level_name'] . "AC数目：" . ($easy_cid['ac_num'] + $hard_cid['ac_num']) . "，\t"; ?>
				<?php echo $result_level[$level_id - 1]['level_name'] . "晋级数目要求：" . $result_level[$level_id - 1]['level_problem_min'] . "<br>"; ?>
				<?php if (!isset($error_msg)) : ?>

					<table class='table table-striped' width=90%>
						<tbody align='center'>
							<thead>
								<tr class='toprow'>
									<td class='hidden-xs center'>
										<?php echo "比赛编号" ?>
									</td>
									<td class='center'>
										<?php echo "作业比赛名称" ?>
									</td>
									<td class='hidden-xs center'>
										<?php echo "已完成题目数量" ?>
									</td>
									<td style="cursor:hand" class='center'>
										<?php echo "总题目数量" ?>
									</td>
								</tr>
							</thead>
						<tbody align='center'>
							<?php
							echo "<tr class='oddrow'>";
							echo "<td>{$easy_cid['contest_id']}</td>";
							echo "<td>	<a href='running.php?cid={$easy_cid['contest_id']}'>{$easy_cid['title']}</a></td>";
							echo "<td>{$easy_cid['ac_num']}</td>";
							echo "<td>{$easy_cid['all_num']}</td>";
							echo "</tr>";
							echo "<tr class='oddrow'>";
							echo "<td>{$hard_cid['contest_id']}</td>";
							echo "<td> <a href='running.php?cid={$hard_cid['contest_id']}'>{$hard_cid['title']}</a></td>";
							echo "<td>{$hard_cid['ac_num']}</td>";
							echo "<td>{$hard_cid['all_num']}</td>";
							echo "</tr>";
							?>
						</tbody>
					</table>
				<?php else : ?>
					<h3><?php echo $error_msg; ?> </h3>
				<?php endif; ?>
			</center>
		</div>

	</div>
	<!-- /container -->


	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php include("template/$OJ_TEMPLATE/js.php"); ?>
	<script type="text/javascript" src="include/jquery.tablesorter.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#problemset").tablesorter();
			$("#problemset").after($("#page").prop("outerHTML"));
		});
	</script>
</body>

</html>