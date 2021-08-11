<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title><?php echo $OJ_NAME?></title>  
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
      <br><br>
      <table align=center width=80%>
        <tr align='center'>
          <td>
              <?php if($view_title== "训练赛"){ ?>
            <form class=form-inline method=post action=running.php>
              <input class="form-control" name=keyword value="<?php if(isset($_POST['keyword'])) echo htmlentities($_POST['keyword'],ENT_QUOTES,"UTF-8")?>" placeholder="<?php echo $MSG_CONTEST_NAME?>">
              <button class="form-control" type=submit><?php echo $MSG_SEARCH?></button>
            </form>
            <?php } else { ?>
            <form class=form-inline method=post action=running2.php>
              <input class="form-control" name=keyword value="<?php if(isset($_POST['keyword'])) echo htmlentities($_POST['keyword'],ENT_QUOTES,"UTF-8")?>" placeholder="<?php echo $MSG_CONTEST_NAME?>">
              <button class="form-control" type=submit><?php echo $MSG_SEARCH?></button>
            </form>
            <?php } ?>
          </td>
        </tr>
      </table>
      <br>

      <center><h3><?php echo $MSG_SERVER_TIME?> <span id=nowdate></span></h3></center><br>

      <table class='table table-striped' width=90%>
        <thead>
          <tr class="toprow training" align=center>
            <td><?php echo $MSG_CONTEST_ID?></td>
            <td><?php echo $MSG_CONTEST_NAME?></td>
            <td><?php echo $MSG_CONTEST_STATUS?></td>
            <td><?php echo $MSG_CONTEST_OPEN?></td>
            <td><?php echo $MSG_CONTEST_CREATOR?></td>
          </tr>
        </thead>
        <tbody align='center'>
          <?php
          $cnt=0;
          foreach($view_contest as $row){
            if ($cnt)
              echo "<tr class='oddrow training'>";
            else
              echo "<tr class='evenrow training'>";
            $i=0;
            foreach($row as $table_cell){
              if($i==2) echo "<td class=text-left>";
              else echo "<td>";
              echo "\t".$table_cell;
              echo "</td>";
              $i++;
            }
            echo "</tr>";
            $cnt=1-$cnt;
          }
          ?>
        </tbody>
      </table>

      <nav class="center">
        <small>
        <ul class="pagination">
          <li class="page-item"><a href="contest.php?page=1">&lt;&lt;</a></li>
          <?php
          if(!isset($page)) $page=1;
          $page=intval($page);
          $section=8;
          $start=$page>$section?$page-$section:1;
          $end=$page+$section>$view_total_page?$view_total_page:$page+$section;
          for ($i=$start;$i<=$end;$i++){
            echo "<li class='".($page==$i?"active ":"")."page-item'><a title='go to page' href='contest.php?page=".$i.(isset($_GET['my'])?"&my":"")."'>".$i."</a></li>";
          }
          ?>
          <li class="page-item"><a href="contest.php?page=<?php echo $view_total_page?>">&gt;&gt;</a></li>
        </ul>
        </small>
      </nav>
      </center>

    </div>
  </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  <?php include("template/$OJ_TEMPLATE/js.php");?>      
  <script>
  function training_leave()
  {
    var tr=document.getElementsByClassName("training");
    var newtd = document.createElement("td");
    newtd.innerHTML="请假";
    tr[0].append(newtd);
    for(var i=1;i<tr.length;i++)
    {
        newtd = document.createElement("td");
        newtd.innerHTML="<button onclick=ask_leave("+tr[i].children[0].textContent.trim()+")>请假</button>";
        tr[i].append(newtd);
    }
  }
  function ask_leave(id)
  {
    var width1=(window.screen.width-10-600)/2;
    var top1=(window.screen.height-10-420)/2;
    window.open('running_leave.php?cid='+id,"",'width=600,height=420,top='+top1+',left='+width1);
  }
  <?php if($view_title== "训练赛")
  echo "training_leave();";
  ?>
</script>
  <script>
  var diff=new Date("<?php echo date("Y/m/d H:i:s")?>").getTime()-new Date().getTime();
  //alert(diff);
  function clock()
  {
    var x,h,m,s,n,xingqi,y,mon,d;
    var x = new Date(new Date().getTime()+diff);
    y = x.getYear()+1900;
    if (y>3000) y-=1900;
    mon = x.getMonth()+1;
    d = x.getDate();
    xingqi = x.getDay();
    h=x.getHours();
    m=x.getMinutes();
    s=x.getSeconds();
    n=y+"-"+(mon>=10?mon:"0"+mon)+"-"+(d>=10?d:"0"+d)+" "+(h>=10?h:"0"+h)+":"+(m>=10?m:"0"+m)+":"+(s>=10?s:"0"+s);
    //alert(n);
    document.getElementById('nowdate').innerHTML=n;
    setTimeout("clock()",1000);
  }

  clock();
  </script>
</body>
</html>
