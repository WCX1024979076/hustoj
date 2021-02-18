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
    }
    else if($type==1)
    {
        $data=$_POST["data"];	
        $data_array=json_decode($data);
        echo "添加信息";
        print_r($data_array);
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
    }
}
