<?php
/**
 * Created by PhpStorm.
 * User: Mr.何
 * Date: 2018/3/9
 * Time: 9:01
 */
include_once './Controller/func.php';
if ($login = checkLogin()){
    $user = $_SESSION['username'];
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']):'';
if (!$id){
    jump('false','参数非法','index.php');
}

$sql = "select count(*) from `painting_product` WHERE state=1";

$username = config('mysql_username');
$password = config('mysql_password');

$res = pdo_select('mysql:host=127.0.0.1;dbname=painting',$username,$password,$sql);
$total = $res['0']['count(*)'];
//
//if ($id > $total){
//    jump('false','请通过点击链接进入','index.php');
//    die;
//}


$sql = "UPDATE `painting_product` set page_view=page_view+1 WHERE id={$id}";
$res = pdo_update('mysql:host=127.0.0.1;dbname=painting',$username,$password,$sql);
if (!$res){
    jump('false','服务器错误','index.php');
}


$sql = "SELECT id,img,name,user_id,create_time,update_time,page_view,price,content FROM `painting_product` WHERE id={$id}";
$res = pdo_select('mysql:host=127.0.0.1;dbname=painting',$username,$password,$sql);
$row = $res['0'];
$user_id = $row['user_id'];
$img = $row['img'];
$name = $row['name'];
$create_time = $row['create_time'];
$update_time = $row['update_time'];
$update_time = $row['update_time'];
$page_view = $row['page_view'];
$price = $row['price'];
$content = $row['content'];

$sql = "SELECT username FROM painting.`painting_users` WHERE id={$user_id}";
$res = pdo_select('mysql:host=127.0.0.1;dbname=painting',$username,$password,$sql);
$username = $res['0']['username'];


include_once './View/detail.html';