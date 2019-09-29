<?php
/**
 * Created by PhpStorm.
 * User: Mr.何
 * Date: 2018/3/11
 * Time: 16:28
 */

include_once './Controller/func.php';

if (!checkLogin()){
    jump('false','请登陆','login.php');
}

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']):'';
if (!$id){
    jump('false','参数非法','index.php');
}

$dsn = 'mysql:host=127.0.0.1;dbname=painting';
$pdoUsername = config('mysql_username');
$pdoPasswrod = config('mysql_password');
$sql = "select count(*) from `painting_product` WHERE id='{$id}'";
$res = pdo_select($dsn,$pdoUsername,$pdoPasswrod,$sql);
$res = $res['0']['count(*)'];
if ($res == 0){
    jump('false','数据消失了','index.php');
    return;
}

$sql = "update `painting_product` set state=0 WHERE id='{$id}' LIMIT 1;";
$res = pdo_update($dsn,$pdoUsername,$pdoPasswrod,$sql);
if (!$res){
    jump('false','删除失败','index.php');
}
$sql = "ALTER TABLE `painting_product` AUTO_INCREMENT=1";
$res = pdo_query($dsn,$pdoUsername,$pdoPasswrod,$sql);
if (!$res){
    jump('false','数据库错误','index.php');
}
jump('true','删除成功','index.php');