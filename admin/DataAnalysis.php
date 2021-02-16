<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>可视化分析队员做题数目</title>
    <!-- 引入 echarts.js -->
    <script src="https://cdn.bootcss.com/echarts/4.1.0.rc2/echarts.min.js"></script>
</head>
<body>
<?php
/**
 * 修改人：王春祥
 * 修改日期：2021/1/28
 * 修改目的：可视化分析队员做题数据
 * 主要修改内容：php动态生成js代码，然后js本地渲染
 */
require_once("admin-header.php");
if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
echo "<a href='../loginpage.php'>Please Login First!</a>";
exit(1);
}
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
echo "<hr>";
echo "<center><h3>队员做题数据分析</h3></center>";
include_once("kindeditor.php");
?>

<?php
require_once("../include/db_info.inc.php");
require_once("../lang/$OJ_LANG.php");
require_once("../include/const.inc.php");
$user_name=[];
$training_ac=[];
$training_wa=[];
$ftraining_ac=[];
$ftraining_wa=[];
$U=[];
$row_cnt=0;
$contest_id="1007,1008";
?>
<?php
	class TM{
        var $solved=0;  ///训练赛ac题目
		var $fsolved=0; ///补题赛ac题目
		var $wa=0;  ///训练赛wa题目
        var $fwa=0; ///补题赛wa题目
        var $p_ac_sec;     ///训练赛ac题目
        var $fp_ac_sec;    ///补题赛ac题目
        var $user_id;
        var $nick;
        function TM(){
                $this->solved=0;
				$this->fsolved=0;
                $this->wa=0;
				$this->fwa=0;
                $this->p_ac_sec=array();
                $this->fp_ac_sec=array();
        }
        function Add($pid,$sec,$res,$ftraining_date1)
        {
                if (isset($this->p_ac_sec[$pid])&&$this->p_ac_sec[$pid]>0)
                        return;
                if (isset($this->fp_ac_sec[$pid])&&$this->fp_ac_sec[$pid]>0)
                        return;
                if($sec<=$ftraining_date1)///在训练赛时期
                {
                        if ($res*100<99)
                        {
							$this->wa++;
                        }
                        else
                        {
							$this->p_ac_sec[$pid]=1;
							$this->solved++;
                        }
                }
                else                                ///在补题赛时间
                {
                        if ($res*100<99)
                        {
                            $this->fwa++;
                        }
                        else
                        {
							$this->fp_ac_sec[$pid]=1;
							$this->fsolved++;
                        }
                }
        }
}

function s_cmp($A,$B){
//      echo "Cmp....<br>";
		if($A->solved!=$B->solved)
			return $A->solved>$B->solved;
		else
			return $A->fsolved>$B->fsolved;
}
?>
<?php
function clear()
{
	global $user_name,$training_ac,$training_wa,$ftraining_ac,$ftraining_wa,$U,$row_cnt;
	$user_name=[];
	$training_ac=[];
	$training_wa=[];
	$ftraining_ac=[];
	$ftraining_wa=[];
	$ftraining_date=[];
	$U=[];
	$row_cnt=0;
}
function sql($contest_id)
{
	global $ftraining_date;
	$ftraining_date=array();
	$sql="SELECT `contest_id`,`ftraining_date` FROM `contest` WHERE `contest_id` in (".$contest_id.")";
	$result = pdo_query($sql);
	foreach ($result as $row)
	{
		$ftraining_date[$row['contest_id']]=strtotime($row['ftraining_date']);
	}
}
function sql_solution($contest_id,$ftraining_date)
{
	global $U;
	$U=array();
	$sql="SELECT
	problem_id,contest_id,user_id,nick,solution.result,solution.num,solution.in_date,solution.pass_rate
			FROM
			   solution where solution.contest_id in (".$contest_id.") and num>=0 and problem_id>0 ORDER BY user_id,solution_id";
	$result = pdo_query($sql);
	$user_cnt=0;
	$user_name='';
	foreach($result as $row)
	{
        $n_user=$row['user_id'];
		if (strcmp($user_name,$n_user))
		{
                $user_cnt++;
                $U[$user_cnt]=new TM();
                $U[$user_cnt]->user_id=$row['user_id'];
                $U[$user_cnt]->nick=$row['nick'];
				$user_name=$n_user;
        }
		if($row['result']!=4 && $row['pass_rate']>=0.99) $row['pass_rate']=0;
    	$U[$user_cnt]->Add($row['problem_id'],strtotime($row['in_date']),$row['pass_rate'],$ftraining_date[$row['contest_id']]);
	}
	usort($U,"s_cmp");
}
function DataProcess()
{
	global $user_name,$training_ac,$training_wa,$ftraining_ac,$ftraining_wa,$U,$row_cnt;
	$row_cnt=count($U);
	for($i=0;$i<$row_cnt;$i++)
	{
		$user_name[$i]=$U[$i]->nick;
		$training_ac[$i]=$U[$i]->solved;
		$training_wa[$i]=$U[$i]->wa;
		$ftraining_ac[$i]=$U[$i]->fsolved;
		$ftraining_wa[$i]=$U[$i]->fwa;
	}
}
function print_array($array)
{
	echo "[";
	$flag=0;
	foreach($array as $row)
	{
		if($flag==0)
			$flag=1;
		else
			echo ',';
		if(is_string($row))
			echo "'".$row."'";
		else
			echo $row;
	}
	echo "]";
}
?>

<div class="container">
	<?php
		if(isset($_POST['contest_id']))
		{
			require_once("../include/check_post_key.php");
			$contest_id=$_POST['contest_id'];
			clear();
			sql($contest_id);
			sql_solution($contest_id,$ftraining_date);
			DataProcess();
		}
	?>
	<form method=POST action=DataAnalysis.php>
		<p align=left>
			<label class="col control-label"><?php echo "比赛编号"?></label>
				<input type=text name='contest_id' size=71 style="width:600px;" value='<?php echo $contest_id?>'>
			<br>
			<text> 注：比赛ID之间用','（英）来分隔 </text>
		</p>
		<?php require_once("../include/set_post_key.php");?>
		<p>
			<center>
				<input type=submit value='<?php echo "提交"?>' name=submit>
			</center>
		</p>
	</form>
</div>
<text> </text>

<div class="panel-body" style="height: 400px; overflow-y:scroll">
	<div style="border: 1px  #000000; width: 90%; margin: 0 auto;">
		<span>
		<?php
			echo '<div id="main" style="width: 940px;height:'.($row_cnt*100+50);
			echo 'px;"></div>';
		?>
		</span>
	</div>
</div>


<script type="text/javascript">
	var myChart = echarts.init(document.getElementById('main'));
	var option;
	option = {
		tooltip : {
			trigger: 'axis',
			axisPointer : {            // 坐标轴指示器，坐标轴触发有效
				type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
			}
		},
		legend: {
			data:['训练赛AC数目','补题赛AC数目','训练赛WA数目','补题赛WA数目']
		},
    toolbox: {
        show : true,
        orient: 'vertical',
        x: 'right',
        y: 'center',
        feature : {
            mark : {show: true},
            dataView : {show: true, readOnly: true},
            saveAsImage : {show: true}
			}
		},
		calculable : true,
		yAxis : [
			{
				type : 'category',
				data : <?php print_array($user_name); ?>
			}
		],
		xAxis : [
			{
				type : 'value'
			}
		],
		series : [
			{
				name:'训练赛AC数目',
				type:'bar',
				stack: '训练赛',
				barWidth:20,
				data:<?php print_array($training_ac); ?>,
				itemStyle:{
						normal:
						{
							color:'#C0C0C0'
						}
					 }
			},
			{
				name:'训练赛WA数目',
				type:'bar',
				stack: '训练赛',
				barWidth:20,
				data:<?php print_array($training_wa); ?>,
				itemStyle:{
						normal:
						{
							color:'#708090'
						}
					 }
			},
			{
				name:'补题赛AC数目',
				type:'bar',
				stack: '补题赛',
				barWidth:20,
				data:<?php print_array($ftraining_ac); ?>,
				itemStyle:{
						normal:
						{
							color:'#6495ED'
						}
					 }
			},
			{
				name:'补题赛WA数目',
				type:'bar',
				stack: '补题赛',
				barWidth:20,
				data:<?php print_array($ftraining_wa); ?>,
				itemStyle:{
						normal:
						{
							color:'#4169E1'
						}
				}
			}
		]
	};
	myChart.setOption(option);
</script>
</body>
</html>