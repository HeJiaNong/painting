<?php
/**
 * Created by PhpStorm.
 * User: Mr.何
 * Date: 2018/3/9
 * Time: 10:44
 */

include_once './Controller/func.php';
if (isset($_POST['name']) && !empty($_POST['name'])){
    $id = trim($_POST['id']);
    if (!is_int($id)){
        jump('false','参数非法','index.php');
    }
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $des = trim($_POST['des']);
    $content = trim($_POST['content']);
    if ($_FILES['file']['error'] === 0){
        if (mb_strlen($name,'utf8') < 1 || mb_strlen($name,'utf8') > 100){
            jump('false','商品名称应在1-255字符之内');
        }
        if ($price <=0 || $price > 999999999){
            jump('false','请输入正确价格','add.php');
        }
        if (mb_strlen($des,'utf8') <=0 || mb_strlen($des,'utf8') > 100){
            jump('false','画品简介应在1-100字符之内');
        }
        if (empty($content)){
            jump('false','画品详情不能为空','add.php');
        }
        $imgUrl = imgUpload($_FILES['file']);
        $update_time = date('H-m-d H:i:s');
        $dsn = 'mysql:host=127.0.0.1;dbname=painting';
        $pdoUsername = config('mysql_username');
        $pdoPassword = config('mysql_password');
        $sql = "UPDATE `painting_product` SET name='{$name}',price='{$price}',img='{$imgUrl}',`desc`='{$des}',content='{$content}',update_time='{$update_time}' WHERE id='{$id}'";
        $res = pdo_update($dsn,$pdoUsername,$pdoPassword,$sql);
        if (!$res){
            jump('false','更新失败',"edit.php?id={$id}");
        }
        jump('true','更新成功','index.php');
        return;
    }else{
        if (mb_strlen($name,'utf8') < 1 || mb_strlen($name,'utf8') > 100){
            jump('false','商品名称应在1-255字符之内');
        }
        if ($price <=0 || $price > 999999999){
            jump('false','请输入正确价格','add.php');
        }
        if (mb_strlen($des,'utf8') <=0 || mb_strlen($des,'utf8') > 100){
            jump('false','画品简介应在1-100字符之内');
        }
        if (empty($content)){
            jump('false','画品详情不能为空','add.php');
        }
        $update_time = date('H-m-d H:i:s');
        $dsn = 'mysql:host=127.0.0.1;dbname=painting';
        $pdoUsername = config('mysql_username');
        $pdoPassword = config('mysql_password');;
        $sql = "UPDATE `painting_product` SET name='{$name}',price='{$price}',`desc`='{$des}',content='{$content}',update_time='{$update_time}' WHERE id='{$id}'";
        $res = pdo_update($dsn,$pdoUsername,$pdoPassword,$sql);
        if (!$res){
            jump('false','更新失败',"edit.php?id={$id}");
        }
        jump('true','更新成功','index.php');
        return;
    }
}
if (!checkLogin()){
    jump('false','请登陆','login.php');
}
if (!isset($_GET['id']) || is_float($_GET['id']) || empty($_GET['id'])){
    jump('false','非法操作!','index.php');
}
//根据商品id查询商品信息
$dsn = 'mysql:host=127.0.0.1;dbname=painting';
$pdoUsername = config('mysql_username');
$pdoPassword = config('mysql_password');
$sql = "SELECT * FROM `painting_product` WHERE id={$_GET['id']}";
$res = pdo_select($dsn,$pdoUsername,$pdoPassword,$sql);
if (!$res){
    jump('false','画品不存在!','index.php');
}
$username   = $_SESSION['username'];
$row        = $res['0'];
$id         = $_GET['id'];
$name       = $row['name'];
$price      = $row['price'];
$img        = $row['img'];
$desc       = $row['desc'];
$content    = $row['content'];



include_once './View/edit.html';