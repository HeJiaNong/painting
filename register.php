<?php


//判定是否提交数据
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['repassword'])){
    date_default_timezone_set('PRC');
    include_once './Controller/func.php';
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);
    if (mb_strlen($username,'utf8') >= 50){
        return false;
    }
    if ($password !== $repassword){
        return false;
    }
    try{
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=painting",config('mysql_username'),config('mysql_password'));
    }catch (PDOException $e){
        echo $e->getMessage();
    }
    //验证用户名重复
    $sql = "select `username` from `painting_users` WHERE username='{$username}'";
    $stmt = $pdo->query($sql);
    $res = $stmt->rowCount();
    if ($res >= 1){
        jump('false','用户已存在','register.php');
    }


    //插入数据
    $password = encryption($password);
    $create_time = date('Y-m-d H:i:s');
    $sql = "insert into `painting_users`(username, password, create_time) VALUES ('{$username}','{$password}','{$create_time}')";
    $stmt = $pdo->query($sql);
    $res = $stmt->rowCount();
    if ($res >= 1){
        jump('true','注册成功','login.php');
    }
}

//引用模板文件
include_once './View/register.html';
//啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦啦