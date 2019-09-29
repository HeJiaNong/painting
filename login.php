<?php
/**
 * Created by PhpStorm.
 * User: Mr.何
 * Date: 2018/2/28
 * Time: 11:32
 */

include_once './Controller/func.php';
if (checkLogin()){
    header('Location:index.php');
}
//验证用户是否提交数据
if (isset($_POST['username']) && isset($_POST['password'])){
    include_once './Controller/func.php';
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    //连接数据库
    if (class_exists('PDO')){
        try{
            $pdo = new PDO("mysql:host=127.0.0.1;dbname=painting",config('mysql_username'),config('mysql_password'));
        }catch (PDOException $e){
            echo $e->getMessage();
        }
    }
    $sql = "SELECT `id`,`password` FROM `painting_users` WHERE username='{$username}'";
    $stmt = $pdo->query($sql);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $password_1 = $res['0']['password'];
    $id = $res['0']['id'];
    if (encryption($password) !== $password_1){
        jump('false','密码错误!','login.php');
        return;
    }


    $_SESSION['username'] = $username;
    $_SESSION['id'] = $id;


    header('Location:index.php');
}

//引用模板文件
include_once './View/login.html';
