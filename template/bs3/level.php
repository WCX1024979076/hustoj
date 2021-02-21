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
		<?php echo $OJ_NAME?>
	</title>
	<?php include("template/$OJ_TEMPLATE/css.php");?>


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
		<?php include("template/$OJ_TEMPLATE/nav.php");?>
		<!-- Main component for a primary marketing message or call to action -->
		<div class="jumbotron">
			<center>
				<nav id="page_my" class="center">
					<small>
					<ul class="pagination">
						<li class="page-item"><a href="level.php?level_id=1">&lt;&lt;</a>
						</li>
						<?php
						if ( !isset( $level_id ) )$level_id = 0;
						$level_id = intval( $level_id );
						$section = 8;
						$start = $level_id > $section ? $level_id - $section : 1;
						$end=$level_id+$section>$view_total_level?$view_total_level:$level_id+$section;
						for ( $i = $start; $i <= $end; $i++ ) {
							echo "<li class='" . ( $level_id == $i ? "active " : "" ) . "page-item'> <a href='level.php?level_id=" . $i . "'>" . $result_level[$i-1]['level_name'] . "</a></li>";
						}
						?>
						<li class="page-item"><a href="level.php?level_id=<?php echo $view_total_level?>">&gt;&gt;</a>
						</li>
					</ul>
					</small>
				</nav>
				<ul class="pagination">
					<?php
						echo "<li class='" . ( $type == 1 ? "active " : "" ) . "page-item'> <a href='level.php?level_id=" . $level_id . "&type=1'>" . 基础题目 . "</a></li>";
						echo "<li class='" . ( $type == 2 ? "active " : "" ) . "page-item'> <a href='level.php?level_id=" . $level_id . "&type=2'>" . 拓展题目 . "</a></li>";
						echo "<li class='" . "page-item'> <a href='status.php?user_id=" . $user_id . "'>" . 提交状态 . "</a></li>";
						if($easy_ac+$hard_ac>=$result_level[$level_id-1]['level_problem_min']&& $level_id==$user_level && $user_level!=$view_total_level)
							echo "<li class='" . "page-item'> <a href='level.php?level_up=1	'>" . 可晋级 . "</a></li>";
					?>	

				</ul>
				<?php echo "<br>当前段位：".$result_level[$user_level-1]['level_name']."，\t"; ?>
					<?php echo $result_level[$level_id-1]['level_name']."AC数目：".($easy_ac+$hard_ac)."，\t"; ?>
					<?php echo $result_level[$level_id-1]['level_name']."晋级数目要求：".$result_level[$level_id-1]['level_problem_min']."<br>"; ?>
				<?php  if(!isset( $error_msg )): ?>

				<table id='problemset' width='90%' class='table table-striped'>
					<thead>
					<tr class='toprow'>
						<td><?php echo "\t"; ?></td>
							<td class='hidden-xs center'>
								<?php echo $MSG_PROBLEM_ID?>
							</td>
							<td class='center'>
								<?php echo $MSG_TITLE?>
							</td>
							<td class='hidden-xs center'>
								<?php echo $MSG_SOURCE?>
							</td>
							<td style="cursor:hand" class='center'>
								<?php echo $MSG_SOVLED?>
							</td>
							<td style="cursor:hand" class='center'>
								<?php echo $MSG_SUBMIT?>
							</td>
						</tr>
					</thead>
					<tbody>
						<?php
						$cnt = 0;
						foreach ( $view_problemset as $row ) {
							if ( $cnt )
								echo "<tr class='oddrow'>";
							else
								echo "<tr class='evenrow'>";
							$i = 0;
							foreach ( $row as $table_cell ) {
								if ( $i == 1  )echo "<td style=\"text-align:center\"  class='hidden-xs'>";
								else echo "<td style=\"text-align:center\">";
								echo "\t" . $table_cell;
								echo "</td>";
								$i++;	
							} 
							echo "</tr>";
							$cnt = 1 - $cnt;
						}
						?>
					</tbody>
				</table>
				<?php else: ?>
				<h3><?php echo $error_msg; ?> </h3>
				<?php endif; ?>
			</center>
		</div>

	</div>
	<!-- /container -->


	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php include("template/$OJ_TEMPLATE/js.php");?>
	<script type="text/javascript" src="include/jquery.tablesorter.js"></script>
	<script type="text/javascript">
		$( document ).ready( function () {
			$( "#problemset" ).tablesorter();
			$( "#problemset" ).after( $( "#page" ).prop( "outerHTML" ) );
		} );
	</script>
</body>
</html>
