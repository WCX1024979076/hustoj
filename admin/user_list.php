<?php
require("admin-header.php");
require_once("../include/set_get_key.php");

if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

if(isset($OJ_LANG)){
  require_once("../lang/$OJ_LANG.php");
}
?>

<title>User List</title>
<hr>
<center><h3><?php echo $MSG_USER."-".$MSG_LIST?></h3></center>

<div class='container'>

<?php
$sql = "SELECT COUNT('user_id') AS ids FROM `users`";
$result = pdo_query($sql);
$row = $result[0];

$ids = intval($row['ids']);

$idsperpage = 25;
$pages = intval(ceil($ids/$idsperpage));

if(isset($_GET['page'])){ $page = intval($_GET['page']);}
else{ $page = 1;}

$pagesperframe = 5;
$frame = intval(ceil($page/$pagesperframe));

$spage = ($frame-1)*$pagesperframe+1;
$epage = min($spage+$pagesperframe-1, $pages);

$sid = ($page-1)*$idsperpage;

$sql = "";
if(isset($_GET['keyword']) && $_GET['keyword']!=""){
  $keyword = $_GET['keyword'];
  $keyword = "%$keyword%";
  $sql = "SELECT `user_id`,`nick`,`accesstime`,`reg_time`,`ip`,`school`,`defunct`,`level_id` FROM `users` WHERE (user_id LIKE ?) OR (nick LIKE ?) OR (school LIKE ?) ORDER BY `user_id` DESC";
  $result = pdo_query($sql,$keyword,$keyword,$keyword);
}else{
  $sql = "SELECT `user_id`,`nick`,`accesstime`,`reg_time`,`ip`,`school`,`defunct`,`level_id` FROM `users` ORDER BY `reg_time` DESC LIMIT $sid, $idsperpage";
  $result = pdo_query($sql);
}

$sql="SELECT * FROM level_list";
$result_level=pdo_query($sql);
?>

<center>
<form action=user_list.php class="form-search form-inline">
  <input type="text" name=keyword class="form-control search-query" placeholder="<?php echo $MSG_USER_ID.', '.$MSG_NICK.', '.$MSG_SCHOOL?>">
  <button type="submit" class="form-control"><?php echo $MSG_SEARCH?></button>
</form>
</center>

<center>
  <table width=100% border=1 style="text-align:center;">
    <tr>
      <td>ID</td>
      <td>NICK</td>
      <td>SCHOOL</td>
      <td>LOGIN</td> 
      <td>SIGN UP</td> 
      <td>USE</td>
      <td>P/W</td>
      <td>PRIVILEGE</td>
      <td>段位</td>
      </tr>
    <?php
    foreach($result as $row){
      echo "<tr>";
        echo "<td><a href='../userinfo.php?user=".$row['user_id']."'>".$row['user_id']."</a></td>";
        echo "<td>".$row['nick']."</td>";
        echo "<td>".$row['school']."</td>";
        echo "<td>".$row['accesstime']."</td>";
        echo "<td>".$row['reg_time']."</td>";
      if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])){
        echo "<td><a href=user_df_change.php?cid=".$row['user_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">".($row['defunct']=="N"?"<span class=green>Available</span>":"<span class=red>Locked</span>")."</a></td>";
      }
      else {
        echo "<td>".($row['defunct']=="N"?"<span>Available</span>":"<span>Locked</span>")."</td>";        
      }
        echo "<td><a href=changepass.php?uid=".$row['user_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">"."Reset"."</a></td>";
        echo "<td><a href=privilege_add.php?uid=".$row['user_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">"."Add"."</a></td>";
      

        /**
         * 修改人：王春祥
         * 修改日期：2021/2/20
         * 修改目的：增加段位修改标签
         */
        echo "<td>";
        echo "<select   style='width:90px;' id='".$row['user_id']."' onchange=\"submitForm('".$row['user_id']."');\" > ";
        foreach($result_level as $row_level)
        {
          echo "<option value='".$row_level['level_id']."'";
          if($row['level_id']==$row_level['level_id'])
          {
            echo "selected";
          }
          echo ">".$row_level['level_name']."</option>";
        }
        echo "</select>";
        echo "<td>";
      echo "</tr>";
    } ?>
  </table>
</center>

<?php
if(!(isset($_GET['keyword']) && $_GET['keyword']!=""))
{
  echo "<div style='display:inline;'>";
  echo "<nav class='center'>";
  echo "<ul class='pagination pagination-sm'>";
  echo "<li class='page-item'><a href='user_list.php?page=".(strval(1))."'>&lt;&lt;</a></li>";
  echo "<li class='page-item'><a href='user_list.php?page=".($page==1?strval(1):strval($page-1))."'>&lt;</a></li>";
  for($i=$spage; $i<=$epage; $i++){
    echo "<li class='".($page==$i?"active ":"")."page-item'><a title='go to page' href='user_list.php?page=".$i.(isset($_GET['my'])?"&my":"")."'>".$i."</a></li>";
  }
  echo "<li class='page-item'><a href='user_list.php?page=".($page==$pages?strval($page):strval($page+1))."'>&gt;</a></li>";
  echo "<li class='page-item'><a href='user_list.php?page=".(strval($pages))."'>&gt;&gt;</a></li>";
  echo "</ul>";
  echo "</nav>";
  echo "</div>";
}
?>
<script>
function submitForm(name){
    var myselect=document.getElementById(name);
    var index=myselect.selectedIndex ; 
    var value=myselect.options[index].value;
     var data={"type":3,"name_id":name,"value":value};
    console.log(data);
   	ajax(data);/// 异步提交数据
}
function ajax(jsonObj)
{
    myajax=$.ajax({
        type:"post",						//提交方式
        url:"/admin/level_ajax.php",		//执行的url(控制器/操作方法)
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
        ;
    });
}
</script>
</div>
