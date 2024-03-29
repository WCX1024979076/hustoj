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
</head>

<body>
<div class="container">
  <?php include("template/$OJ_TEMPLATE/nav.php");?>
  <!-- Main component for a primary marketing message or call to action -->
  <div class="jumbotron">
    <center>
    <div>
      <h3><?php echo $MSG_CONTEST_ID?> : <?php echo $view_cid?> - <?php echo $view_title ?></h3>
      <p>
        <?php echo $view_description?>
      </p>
      <br>
      <?php echo $MSG_SERVER_TIME?> : <span id=nowdate > <?php echo date("Y-m-d H:i:s")?></span>
      <br>
      
      <?php if (isset($OJ_RANK_LOCK_PERCENT)&&$OJ_RANK_LOCK_PERCENT!=0) { ?>
      Lock Board Time: <?php echo date("Y-m-d H:i:s", $view_lock_time) ?><br/>
      <?php } ?>
      
      <?php if ($now>$end_time) {
        echo "<span class=text-muted>$MSG_Ended</span>";
      }
      else if ($now<$start_time) {
        echo "<span class=text-success>$MSG_Start&nbsp;</span>";
        echo "<span class=text-success>$MSG_TotalTime</span>"." ".formatTimeLength($end_time-$start_time);
      }
      else {
        echo "<span class=text-danger>$MSG_Running</span>&nbsp;";
        echo "<span class=text-danger>$MSG_LeftTime</span>"." ".formatTimeLength($end_time-$now);
      }
      ?>

      <br><br>

      <?php echo $MSG_CONTEST_STATUS?> : 
      
      <?php
      if ($now>$end_time)
        echo "<span class=text-muted>".$MSG_End."</span>";
      else if ($now<$start_time)
        echo "<span class=text-success>".$MSG_Start."</span>";
      else
        echo "<span class=text-danger>".$MSG_Running."</span>";
      ?>
      &nbsp;&nbsp;

      <?php echo $MSG_CONTEST_OPEN?> : 

      <?php if ($view_private=='0')
        echo "<span class=text-primary>".$MSG_Public."</span>";
      else
        echo "<span class=text-danger>".$MSG_Private."</span>";
      ?>

      <br>

      <!--
        * 修改人：王春祥
        * 修改日期：2021/1/18
        * 修改目的：修改题目列表界面
      -->
      
      <?php echo "比赛".$MSG_START_TIME?> : <?php echo $view_start_time?>
      <br>

      <?php 
        if($now<=$ftraining_date)
        {
          if( (isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])))
          {
            echo "训练赛时长：管理员<br>";
            echo "登陆时间：管理员<br>";
            echo "训练赛结束时间：管理员";
          }
          else
          {
            // echo "训练赛时长：".$training_length."<br>";
            echo "训练赛开始时间：".date("Y-m-d H:i:s",$login_time)."<br>";
            echo "训练赛结束时间：";
            $time1=(int)((intval(substr($training_length,0,2))*60*60+intval(substr($training_length,3,2))*60+$login_time));
            $time2=(int)($ftraining_date);
            $time_finish=min($time1,$time2);
            echo date("Y-m-d H:i:s",$time_finish);
            echo '<div id="showtime"></div>';
          }
        }
        else
        {
          echo "训练赛已结束，补题赛开始";
        }
      ?>
      <br>
      <?php echo "补题赛开始时间"?> : <?php echo date("Y-m-d H:i:s",$ftraining_date)?>
      <br>
      <?php echo "比赛".$MSG_END_TIME?> : <?php echo $view_end_time?>
      <br><br>
      <!-- 结束-->

      
      <div class="btn-group">
        <a href="contest.php?cid=<?php echo $cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_PROBLEMS?></a>
        <a href="status.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_SUBMIT?></a>


        
        <!--
           <a href="contestrank.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo $MSG_STANDING?></a> 
        -->
        <a href="contestrank-ioi.php?cid=<?php echo $view_cid?>" class="btn btn-primary btn-sm"><?php echo "IOI".$MSG_STANDING?></a>
        <?php 
          if($now>$ftraining_date)
          echo '<a href="conteststatistics.php?cid='.$view_cid.'" class="btn btn-primary btn-sm">'.$MSG_STATISTICS.'</a>';
          ?>


        <a href="suspect_list.php?cid=<?php echo $view_cid?>" class="btn btn-warning btn-sm"><?php echo $MSG_IP_VERIFICATION?></a>
        <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])) {?>
          <a href="user_set_ip.php?cid=<?php echo $view_cid?>" class="btn btn-success btn-sm"><?php echo $MSG_SET_LOGIN_IP?></a>
          <a target="_blank" href="../../admin/contest_edit.php?cid=<?php echo $view_cid?>" class="btn btn-success btn-sm"><?php echo "EDIT"?></a>
        <?php } ?>
      </div>
    </div>

    <table id='problemset' class='table table-striped'  width='90%'>
      <thead>
        <tr align=center class='toprow'>
          <td></td>
          <td style="cursor:hand" onclick="sortTable('problemset', 1, 'int');" ><?php echo $MSG_PROBLEM_ID?></td>
          <td><?php echo $MSG_TITLE?></td>
          <td><?php echo $MSG_SOURCE?></td>
          <td style="cursor:hand" onclick="sortTable('problemset', 4, 'int');"><?php echo $MSG_SOVLED?></td>
          <td style="cursor:hand" onclick="sortTable('problemset', 5, 'int');"><?php echo $MSG_SUBMIT?></td>
        </tr>
      </thead>
      <tbody align='center'>
        <?php
        $cnt=0;
        foreach ($view_problemset as $row) {
          if ($cnt)
            echo "<tr class='oddrow'>";
          else
            echo "<tr class='evenrow'>";
          
          foreach ($row as $table_cell) {
            echo "<td>";
            echo "\t".$table_cell;
            echo "</td>";
          }
          echo "</tr>";
          $cnt=1-$cnt;
        }
        ?>
      </tbody>
    </table>
    </center>
  </div>
</div>

<!-- /container -->
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<?php include("template/$OJ_TEMPLATE/js.php");?>      

<script src="include/sortTable.js"></script>
<style>
#showtime {font-weight:600; font-size:18px;}
</style>
<script>
 
/**
 * 修改人：王春祥
 * 修改日期：2021/2/28
 * 修改目的：倒计时js控制
 */
var showtime = function () {
    var nowtime = new Date(),  //获取当前时间
        endtime = new Date("<?php echo date("Y-m-d H:i:s",$time_finish); ?>");  //定义结束时间
    var lefttime = endtime.getTime() - nowtime.getTime(),  //距离结束时间的毫秒数
        lefth = Math.floor(lefttime/(1000*60*60)),  //计算小时数
        leftm = Math.floor(lefttime/(1000*60)%60),  //计算分钟数
        lefts = Math.floor(lefttime/1000%60);  //计算秒数
    var n = (lefth>=10?lefth:"0"+lefth)+":"+(leftm>=10?leftm:"0"+leftm)+":"+(lefts>=10?lefts:"0"+lefts);
    return "训练赛剩余时间："+n;
}
var div = document.getElementById("showtime");
div.innerHTML = showtime(); ///预先执行一次，显示文字
setInterval (function () {
    div.innerHTML = showtime();
}, 1000);  //反复执行函数本身
</script>

<script>
  var diff = new Date("<?php echo date("Y/m/d H:i:s")?>").getTime()-new Date().getTime();
  //alert(diff);
  function clock() {
    var x,h,m,s,n,xingqi,y,mon,d;
    var x = new Date(new Date().getTime()+diff);
    y = x.getYear()+1900;

    if (y>3000)
      y -= 1900;

    mon = x.getMonth()+1;
    d = x.getDate();
    xingqi = x.getDay();
    h = x.getHours();
    m = x.getMinutes();
    s = x.getSeconds();
    n = y+"-"+(mon>=10?mon:"0"+mon)+"-"+(d>=10?d:"0"+d)+" "+(h>=10?h:"0"+h)+":"+(m>=10?m:"0"+m)+":"+(s>=10?s:"0"+s);

    //alert(n);
    document.getElementById('nowdate').innerHTML = n;
    setTimeout("clock()",1000);
  }
  clock();
</script>

</body>
</html>
