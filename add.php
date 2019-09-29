<?php

include_once './Controller/func.php';
if (!$login = checkLogin()){
    jump('false','请登陆！','login.php');
}
$user = $_SESSION['username'];
if (isset($_POST['name'])       && !empty($_POST['name'])
    && isset($_POST['price'])   && !empty($_POST['price'])
    && isset($_POST['des'])     && !empty($_POST['des'])
    && isset($_POST['content']) && !empty($_POST['content'])){
    //处理接收的数据
    if ($_FILES['file']['error'] !== 0){
        jump('false','图片上传错误','add.php');
    }
    $file = $_FILES['file'];
    $img = imgUpload($file);
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $des = trim($_POST['des']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['id'];
    $create_time = date('H-m-d H:i:s');
    $update_time = $create_time;
    if (!$img){
        jump('false','图片上传错误','add.php');
    }
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
    //入库操作
    $dsn = 'mysql:host=127.0.0.1;dbname=painting';
    $username = $password = 'root';
    $sql = "INSERT INTO `painting_product`
            (name, price, img, `desc`, content, user_id, create_time, update_time,state) VALUES 
            ('{$name}','{$price}','{$img}','{$des}','{$content}','{$user_id}','{$create_time}','{$update_time}',1)";
    $res = pdo_insert($dsn,$username,$password,$sql);
    if (!$res){
        jump('false','添加失败，请重试','add.php');
    }
    jump('true','添加成功','index.php');
}

include_once './View/add.html';


