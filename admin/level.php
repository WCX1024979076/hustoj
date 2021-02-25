<?php
   header("Cache-control:private"); 
   /**
    * 修改人：王春祥
    * 修改日期：2021/2/18
    * 修改目的：增加段位管理界面
    */
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8"/>
    <title>段位管理</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="style.css"/>
	
    <link rel="stylesheet" type="text/css" href="https://cdn.bootcss.com/twitter-bootstrap/4.2.1/css/bootstrap.min.css"/>
	
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/4.5.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
	
</head>

<?php 
  require_once("../include/db_info.inc.php");
  require_once("../lang/$OJ_LANG.php");
  require_once("../include/const.inc.php");
  require_once('../include/memcache.php');
  require_once('../include/my_func.inc.php');
  require_once('../include/const.inc.php');
  require_once('../include/setlang.php');
  if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter']))){
    echo "<a href='../loginpage.php'>Please Login First!</a>";
    exit(1);
  }
  include_once("kindeditor.php") ;
?>
<?php 
  $sql = "SELECT * FROM `level_list`";
  $level_arr=pdo_query($sql);
  $td_array=array();
  foreach($level_arr as $row)
  {
    $tem_arr=array();
    $tem_arr['level_id']=$row['level_id'];
    $tem_arr['level_name']=$row['level_name'];
    $tem_arr['level_problem_min']=$row['level_problem_min'];
    $plist = "";
    $sql = "SELECT `problem_id` FROM `level_problem` WHERE `level_id`=? and `type`=1 ORDER BY `num`";
    $result_easy=pdo_query($sql,$row['level_id']);
    foreach($result_easy as $row_easy){
        if($plist) $plist .= ",";
        $plist.=$row_easy[0];
    }
    $tem_arr['easy_pro']=$plist;

    $plist = "";
    $sql = "SELECT `problem_id` FROM `level_problem` WHERE `level_id`=? and `type`=2 ORDER BY `num`";
    $result_hard=pdo_query($sql,$row['level_id']);

    foreach($result_hard as $row_hard){
        if($plist) $plist .= ",";
        $plist.=$row_hard[0];
    }
    $tem_arr['hard_pro']=$plist;

    $td_array[]=$tem_arr;
  }
?>
<body>
<div class="box">
    <div class="content">
		<h3> 段位管理 </h3>
        <h5>注：</h5>
        <h6>1.段位编号从1开始，且必须连续。</h6>
        <h6>2.题目编号之间用','（英）来分隔。</h6>
        <h6>3.最低题目要求为基础题目数目和拓展题目数目总和。</h6>
        <!--添加按钮及bootstrap的模态框-->
        <div class="export">
            <button id="new_add" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#renyuan">
                <img src="add_two.png"/>
                <span>添加</span>
            </button>
            <div class="modal fade" id="renyuan">
                <div class="modal-dialog modal-lg modal_position">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">添加</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <table id="xztb" class="table">
                                <!--新修改弹窗的样式-->
                                <tbody>
                                <tr>
                                    <td width=150px class="tb_bg"><label for=""><font style="font-size: 14px; color: red;">*</font>段位等级</label>
                                    </td>
                                    <td><input class="level_id" type="number" placeholder="请输入段位等级"/></td>
                                </tr>
                                <tr>
                                    <td class="tb_bg"><label for=""><font style="font-size: 14px; color: red;">*</font>段位名称</label>
                                    </td>
                                    <td><input class="level_name" type="text" placeholder="请输入段位名称"/></td>
                                </tr>
                                <tr>
                                    <td class="tb_bg"><label for=""><font style="font-size: 14px; color: red;">*</font>段位最低题目要求</label>
                                    </td>
                                    <td><input class="level_problem_min" type="number" placeholder="请输入段位最低题目要求"/></td>

                                </tr>
                                <tr>
									<td class="tb_bg"><label for="">段位基础题目列表</label></td>
                                    <td><input type="text" placeholder="请输入段位基本题目列表"/></td>
                                </tr>
								<tr>
									<td class="tb_bg"><label for="">段位拓展题目列表</label></td>
                                    <td><input type="text" placeholder="请输入段位拓展题目列表"/></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                            <button id="add_btn" type="button" class="btn btn-secondary">确定</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--表格列表-->
        <table id="tb" class="table">
            <thead>
            <tr>
                <th>段位等级</th>
                <th>段位名称</th>
                <th>最低题目要求</th>
                <th>基础题目</th>
                <th>拓展题目</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="show_tbody">
            <?php 
                foreach($td_array as $row_td)
                {
                  echo "<tr>";
                  echo "<td>".$row_td['level_id']."</td>";
                  echo "<td>".$row_td['level_name']."</td>";
                  echo "<td>".$row_td['level_problem_min']."</td>";
                  echo "<td>".$row_td['easy_pro']."</td>";
                  echo "<td>".$row_td['hard_pro']."</td>";
                  echo '<td> <a href="#" class="edit">编辑</a> <a href="#" class="del">删除</a></td>';
                  echo "</tr>";
                }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script src="mejs.js"></script>
</body>
</html>
