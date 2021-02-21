<?php
/**
 * 修改人：王春祥
 * 修改日期：2021/2/18
 * 修改目的：接收修改信息用
 */
require_once("admin-header.php");

if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

if($_SERVER['REQUEST_METHOD']=="POST"){
	$type=$_POST["type"];	
    if($type==0)
    {
        $data=$_POST["data"];	
        echo "删除信息".$data;

        $sql="SELECT `level_id` from level_list where `level_name`=?";
        $level_id=pdo_query($sql,$data)[0][0];
        echo $level_id;
        $sql="DELETE from level_problem where `level_id`=?";
        pdo_query($sql,$level_id);
        $sql="DELETE from level_list where `level_id`=?";
        pdo_query($sql,$level_id);
        echo "删除成功";
    }
    else if($type==1)
    {
        $data=$_POST["data"];	
        $data_array=json_decode($data);
        echo "添加信息";
        print_r($data_array);

        $sql="INSERT INTO `level_list`(`level_id`,`level_name`,`level_problem_min`) VALUES(?,?,?)";
        pdo_query($sql,$data_array[0],$data_array[1],$data_array[2]);
        $sql_1 = "INSERT INTO `level_problem`(`level_id`,`problem_id`,`c_accepted`,`c_submit`,`num`,`type`) VALUES (?,?,?,?,?,?)";
        $pieces = explode(",",$data_array[3]);
        for($i=0; $i<count($pieces); $i++){
          pdo_query($sql_1,$data_array[0],$pieces[$i],0,0,$i,1);
        }
        $pieces = explode(",",$data_array[4]);
        print_r($pieces);
        for($i=0; $i<count($pieces); $i++){
          pdo_query($sql_1,$data_array[0],$pieces[$i],0,0,$i,2);
        }
        echo "添加成功";
    }
    else if($type==2)
    {
        $data_old=$_POST["data_old"];	
        $data_new=$_POST["data_new"];
        $data_old_array=json_decode($data_old);
        $data_new_array=json_decode($data_new);
        echo "修改信息，旧信息";
        print_r($data_old_array);
        echo "新信息";
        print_r($data_new_array);

        $sql="SELECT `level_id` from `level_list` where `level_name`=?";
        $level_id=pdo_query($sql,$data_old_array[1])[0][0];
        $sql="DELETE from `level_problem` where `level_id`=?";
        pdo_query($sql,$level_id);
        $sql="DELETE from `level_list` where `level_id`=?";
        pdo_query($sql,$level_id);
        echo "删除成功";
        echo $level_id;

        $sql="INSERT INTO `level_list`(`level_id`,`level_name`,`level_problem_min`) VALUES(?,?,?)";
        pdo_query($sql,$data_new_array[0],$data_new_array[1],$data_new_array[2]);
        $sql_1 = "INSERT INTO `level_problem`(`level_id`,`problem_id`,`c_accepted`,`c_submit`,`num`,`type`) VALUES (?,?,?,?,?,?)";
        $pieces = explode(",",$data_new_array[3]);
        for($i=0; $i<count($pieces); $i++){
          pdo_query($sql_1,$data_new_array[0],$pieces[$i],0,0,$i,1);
        }
        $pieces = explode(",",$data_new_array[4]);
        for($i=0; $i<count($pieces); $i++){
          pdo_query($sql_1,$data_new_array[0],$pieces[$i],0,0,$i,2);
        }
        echo "添加成功";

    }
    else if($type==3)
    {
        $name_id=$_POST["name_id"];	
        $value=intval($_POST["value"]);
        $sql="UPDATE users SET level_id=? WHERE user_id=?";
        pdo_query($sql,$value,$name_id);
        echo "修改成功";
    }
}
