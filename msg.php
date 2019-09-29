<?php
/**
 * Created by PhpStorm.
 * User: Mr.何
 * Date: 2018/2/28
 * Time: 19:23
 */

include_once './Controller/func.php';
if (empty($_GET)){
    jump('false','登陆信息有误,请重新输入','login.php');
}
$type = $_GET['type'];
$msg = $_GET['msg'];
$url = $_GET['url'];
switch ($type){
    case 'false';
        include_once './View/error.html';
        break;
    case 'true';
        include_once './View/done.html';
        break;
    default:
        jump('false','登陆信息有误,请重新输入','login.php');
}
?>