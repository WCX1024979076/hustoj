<?php
$OJ_CACHE_SHARE = true;
$cache_time = 10;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");

$view_title = $MSG_CONTEST.$MSG_RANKLIST;

$title = "";

class TM {
	var $time = 0;
	var $solved = 0;
	var $fsolved = 0;
	var $p_wa_num;
	var $p_ac_sec;
	var $fp_wa_num;
	var $fp_ac_sec;
	var $user_id;
	var $nick;

	function TM() {
		$this->solved = 0;
		$this->time = 0;
		$this->p_wa_num = array(0);
		$this->p_ac_sec = array(0);
	}

	function Add($pid,$sec,$res,$is_traning) {
		global $OJ_CE_PENALTY;
		//echo "Add $pid $sec $res<br>";

		if($is_traning) {
			if (isset($this->p_ac_sec[$pid]) && $this->p_ac_sec[$pid]>0)
				return;
			if ($res!=4) {
				if (isset($OJ_CE_PENALTY)&&!$OJ_CE_PENALTY&&$res==11)
					return;  // ACM WF punish no ce 
	
				if (isset($this->p_wa_num[$pid])) {
					$this->p_wa_num[$pid]++;
				}
				else {
					$this->p_wa_num[$pid] = 1;
				}
			}
			else {
				$this->p_ac_sec[$pid] = $sec;
				$this->solved++;
				$this->fp_ac_sec[$pid] = $sec;
				$this->fsolved++;
	
				if (!isset($this->p_wa_num[$pid]))
					$this->p_wa_num[$pid] = 0;
	
				$this->time += $sec+$this->p_wa_num[$pid]*1200;
				//echo "Time:".$this->time."<br>";
				//echo "Solved:".$this->solved."<br>";
			}
		}
		else
		{
			if (isset($this->fp_ac_sec[$pid]) && $this->fp_ac_sec[$pid]>0)
				return;
			
			if ($res!=4) {
				if (isset($OJ_CE_PENALTY)&&!$OJ_CE_PENALTY&&$res==11)
					return;  // ACM WF punish no ce 
	
				if (isset($this->p_wa_num[$pid])) {
					$this->fp_wa_num[$pid]++;
				}
				else {
					$this->fp_wa_num[$pid] = 1;
				}
			}
			else {
				$this->fp_ac_sec[$pid] = $sec;
				$this->fsolved++;
	
				if (!isset($this->fp_wa_num[$pid]))
					$this->fp_wa_num[$pid] = 0;
	
				//echo "Time:".$this->time."<br>";
				//echo "Solved:".$this->solved."<br>";
			}
		}
	}
}

function s_cmp($A,$B) {
	//echo "Cmp....<br>";
	if ($A->solved!=$B->solved)
		return $A->solved<$B->solved;
	else
		return $A->time>$B->time;
}

// contest start time
if (!isset($_GET['cid']))
	die("No Such Contest!");

$f_cid = intval($_GET['cid']);

$page=1;
if (isset($_GET['page'])) $page=intval($_GET['page']);
$num_each_page=200;

$start_time = 0;
$end_time = 0;

$tstart_time = 0;
$tend_time = 0;

if ($OJ_MEMCACHE) {
	$sql = "SELECT `start_time`,`title`,`end_time`,`training_id` FROM `contest` WHERE `contest_id`=$f_cid";
	require("./include/memcache.php");
	$result = mysql_query_cache($sql);

	if ($result)
		$rows_cnt = count($result);
	else
		$rows_cnt = 0;
}
else {
	$sql = "SELECT `start_time`,`title`,`end_time`,`training_id` FROM `contest` WHERE `contest_id`=?";
	$result = pdo_query($sql,$f_cid);

	if($result)
		$rows_cnt = count($result);
	else
		$rows_cnt = 0;
}
if ($rows_cnt > 0) {
//       $row=$result[0];

		if ($OJ_MEMCACHE) {
				$row = $result[0];
		} else {
				$row = $result[0];
		}
		$start_time = strtotime($row['start_time']);
		$end_time = strtotime($row['end_time']);
		$title = $row['title'];
		$t_cid = $row['training_id'];
		if($t_cid == "")
				$t_cid = $f_cid;
}

if ($OJ_MEMCACHE) {
	$sql = "SELECT `start_time`,`title`,`end_time`,`training_id` FROM `contest` WHERE `contest_id`='$t_cid'";
	require "./include/memcache.php";
	$result = mysql_query_cache($sql);
	if ($result) {
			$rows_cnt = count($result);
	} else {
			$rows_cnt = 0;
	}

} else {
	$sql = "SELECT `start_time`,`title`,`end_time`,`training_id`  FROM `contest` WHERE `contest_id`=?";
	$result = pdo_query($sql, $t_cid);
	if ($result) {
			$rows_cnt = count($result);
	} else {
			$rows_cnt = 0;
	}
}

if ($rows_cnt>0) {
	//$row=$result[0];

	if($OJ_MEMCACHE)
		$row = $result[0];
	else
		$row = $result[0];

	$tstart_time = strtotime($row['start_time']);
	$tend_time = strtotime($row['end_time']);
}

if (!$OJ_MEMCACHE)
	if ($start_time==0) {
		$view_errors = "No Such Contest";
		require("template/".$OJ_TEMPLATE."/error.php");
		exit(0);
	}

if ($start_time>time()) {
	$view_errors = "Contest Not Started!";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}

	$noip = (time()<$end_time) && (stripos($title,$OJ_NOIP_KEYWORD)!==false);
	if(isset($_SESSION[$OJ_NAME.'_'."administrator"])||
		isset($_SESSION[$OJ_NAME.'_'."m$cid"])||
		isset($_SESSION[$OJ_NAME.'_'."source_browser"])||
		isset($_SESSION[$OJ_NAME.'_'."contest_creator"])
	   ) $noip=false;
if ($noip) {
	$view_errors =  "<h2>$MSG_NOIP_WARNING</h2>";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}

if (!isset($OJ_RANK_LOCK_PERCENT))
	$OJ_RANK_LOCK_PERCENT = 0;

$lock = $end_time-($end_time-$start_time)*$OJ_RANK_LOCK_PERCENT;

//echo $lock.'-'.date("Y-m-d H:i:s",$lock);
$view_lock_time = $start_time+($end_time-$start_time)*(1-$OJ_RANK_LOCK_PERCENT);
$locked_msg = "";

if (time()>$view_lock_time && time()<$end_time+$OJ_RANK_LOCK_DELAY) {
	$locked_msg = "The board has been locked.";
}

if ($OJ_MEMCACHE) {
	$sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`='$f_cid'";
	//require("./include/memcache.php");
	$result = mysql_query_cache($sql);
	
	if ($result)
		$rows_cnt = count($result);
	else
		$rows_cnt = 0;
}
else {
	$sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`=?";
	$result = pdo_query($sql,$f_cid);

	if ($result)
		$rows_cnt = count($result);
	else
		$rows_cnt = 0;
}

if ($OJ_MEMCACHE)
	$row = $result[0];
else
	$row = $result[0];

//$row=$result[0];
$pid_cnt = intval($row['pbc']);

if ($OJ_MEMCACHE) {
    $sql = "SELECT
      user_id,nick,solution.result,solution.num,solution.in_date,solution.pass_rate,solution.contest_id
              FROM
                 solution where (solution.contest_id='$f_cid' or solution.contest_id='$t_cid') and num>=0 and problem_id>0
      ORDER BY user_id,solution_id";
    $result = mysql_query_cache($sql);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }

} else {
    $sql = "SELECT
      user_id,nick,solution.result,solution.num,solution.in_date,solution.pass_rate,solution.contest_id
              FROM
                 solution where (solution.contest_id=? or solution.contest_id=?) and num>=0 and problem_id>0
      ORDER BY user_id,solution_id";
    $result = pdo_query($sql, $t_cid, $f_cid);
    if ($result) {
        $rows_cnt = count($result);
    } else {
        $rows_cnt = 0;
    }

}

$user_cnt = 0;
$user_name = '';
$U = array();

//$U[$user_cnt]=new TM();
for ($i=0; $i<$rows_cnt; $i++) {
	$row = $result[$i];
	$n_user = $row['user_id'];

	if (strcmp($user_name,$n_user)) {
		$user_cnt++;
		$U[$user_cnt] = new TM();

		$U[$user_cnt]->user_id = $row['user_id'];
		$U[$user_cnt]->nick = $row['nick'];

		$user_name = $n_user;
	}

	if (time()<$tend_time+$OJ_RANK_LOCK_DELAY && $lock<strtotime($row['in_date'])) {
		if($row["contest_id"] == $t_cid)
			$U[$user_cnt]->Add($row['num'],strtotime($row['in_date'])-$tstart_time,0,$row["contest_id"] == $t_cid);
		else
			$U[$user_cnt]->Add($row['num'],strtotime($row['in_date'])-$start_time,0,$row["contest_id"] == $t_cid);
	}
	else {
		if($row["contest_id"] == $t_cid)
			$U[$user_cnt]->Add($row['num'],strtotime($row['in_date'])-$tstart_time,intval($row['result']),$row["contest_id"] == $t_cid);
		else
			$U[$user_cnt]->Add($row['num'],strtotime($row['in_date'])-$start_time,intval($row['result']),$row["contest_id"] == $t_cid);
	}
}

usort($U,"s_cmp");
$view_total_page=$user_cnt/$num_each_page;
if($user_cnt%$num_each_page!=0) $view_total_page++;

/////////////////////////Template
require("template/".$OJ_TEMPLATE."/contestrank-1.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>
