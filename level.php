<?php
/**
 * 修改人：王春祥
 * 修改日期：2021/2/19
 * 修改目的：段位赛前台界面
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


function check_level_ac($level_id,$problem_id){
  //require_once("./include/db_info.inc.php");
  global $OJ_NAME;
  $sql="SELECT count(*) FROM `solution` WHERE `level_id`=? AND `problem_id`=? AND `result`='4' AND `user_id`=?";
  $result=pdo_query($sql,$level_id,$problem_id,$_SESSION[$OJ_NAME.'_'.'user_id']);
  $row=$result[0];
  $ac=intval($row[0]);
  if ($ac>0) return "<div class='label label-success'>Y</div>";
  
  $sql="SELECT count(*) FROM `solution` WHERE `level_id`=? AND `problem_id`=? AND `result`!=4 and `problem_id`!=0  AND `user_id`=?";
  $result=pdo_query($sql,$level_id,$problem_id,$_SESSION[$OJ_NAME.'_'.'user_id']);
  $row=$result[0];
  $sub=intval($row[0]);
  
  if ($sub>0) return "<div class='label label-danger'>N</div>";
  else return "";
}

$view_title= "段位赛";

$sql="SELECT * FROM `level_list`";
$result_level=pdo_query($sql);
$view_total_level=count($result_level);
$user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
$sql="SELECT `level_id` FROM `users` where `user_id`=?";
$user_level=pdo_query($sql,$user_id)[0]['level_id'];


if(isset($_GET['level_up']))   ///晋级选项
{
  $sql="SELECT COUNT(distinct problem_id) FROM level_problem lp WHERE type=? AND level_id=? AND EXISTS (SELECT * FROM solution sol WHERE sol.problem_id=lp.problem_id AND `result`=4 and lp.level_id=sol.level_id AND user_id=?)";
  $easy_ac=pdo_query($sql,1,$user_level,$_SESSION[$OJ_NAME.'_'.'user_id'])[0][0];
  $hard_ac=pdo_query($sql,2,$user_level,$_SESSION[$OJ_NAME.'_'.'user_id'])[0][0];
  if($easy_ac+$hard_ac>=$result_level[$user_level-1]['level_problem_min'] && $user_level!=$view_total_level)
  {
    $sql="UPDATE users set level_id=level_id+1 where user_id=?";
    pdo_query($sql,$user_id);
    header("Location: level.php");
  }
}


if (isset($_GET['level_id']))
  $level_id = intval($_GET['level_id']);
else
  $level_id = $user_level;
if($level_id>$user_level)
{
  $error_msg="未达到段位要求，请返回。<a href="."/level.php?level_id=".$user_level.">点击跳转</a>";
}

if(isset($_GET['type']))
  $type = intval($_GET['type']);
else
  $type=1;
$sql="SELECT `problem_id` FROM level_problem WHERE level_id=? AND type=?";
$result_problem=pdo_query($sql,$level_id,$type);
foreach($result_problem as $row_problem_id)
{
  $p_id=$row_problem_id[0];
  $sql="UPDATE `level_problem` SET `c_accepted`=(SELECT count(*) FROM `solution` WHERE `problem_id`=? AND `result`=4 and level_id=?) WHERE `problem_id`=? and level_id=?";
  pdo_query($sql,$p_id,$level_id,$p_id,$level_id);
  $sql="UPDATE `level_problem` SET `c_submit`=(SELECT count(*) FROM `solution` WHERE `problem_id`=? and level_id=?) WHERE `problem_id`=? and level_id=?";
  pdo_query($sql,$p_id,$level_id,$p_id,$level_id);
}
/// 手动更新数据，由于难以改变judge.cc里面的内容，暂手动更新
$sql = "SELECT p.title, p.problem_id, p.source, lp.num AS pnum, lp.c_accepted AS accepted , lp.c_submit AS submit FROM problem p INNER JOIN level_problem lp ON p.problem_id = lp.problem_id AND lp.level_id = $level_id AND lp.type=$type ORDER BY lp.num";
$result = mysql_query_cache($sql);
$view_problemset = Array();
$cnt=0;
foreach($result as $row)   //加载问题
{
 $view_problemset[$cnt][0] = "";
 if (isset($_SESSION[$OJ_NAME.'_'.'user_id']))
   $view_problemset[$cnt][0] = check_level_ac($level_id,$row['problem_id']);
 else
   $view_problemset[$cnt][0] = "";
  $view_problemset[$cnt][1] = "<a href='problem.php?level_id=$level_id&problem_id=".$row['problem_id']."'>".$row['problem_id']."</a>";
  $view_problemset[$cnt][2] = "<a href='problem.php?level_id=$level_id&problem_id=".$row['problem_id']."'>".$row['title']."</a>"; 
  $view_problemset[$cnt][3] = $row['source'];
  $view_problemset[$cnt][4] = $row['accepted'];
  $view_problemset[$cnt][5] = $row['submit'];
  $cnt++;
}

///检查基础题目是否全部完成
$sql="SELECT COUNT(distinct problem_id) FROM level_problem lp WHERE type=? AND level_id=? AND EXISTS (SELECT * FROM solution sol WHERE sol.problem_id=lp.problem_id AND `result`=4 and lp.level_id=sol.level_id AND user_id=?)";
$easy_ac=pdo_query($sql,1,$level_id,$_SESSION[$OJ_NAME.'_'.'user_id'])[0][0];
$hard_ac=pdo_query($sql,2,$level_id,$_SESSION[$OJ_NAME.'_'.'user_id'])[0][0];
$sql="SELECT COUNT(problem_id) FROM level_problem WHERE level_id=? AND type=1";
$easy_size=pdo_query($sql,$level_id)[0][0];
if($type==2&&$easy_ac!=$easy_size&&!isset($error_msg))
{
  $error_msg="请先完成所有的基础题目！";
}
////////////////////////Template
require("template/".$OJ_TEMPLATE."/level.php");
/////////////////////////Common foot/
if (file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>
