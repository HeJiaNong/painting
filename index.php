<?php

/**
 * Created by PhpStorm.
 * User: Mr.何
 * Date: 2018/2/26
 * Time: 20:24
 */

include_once './Controller/func.php';

if ($login = checkLogin()){
    $user = $_SESSION['username'];
}

//检查page参数
$page = isset($_GET['page'])?intval($_GET['page']):1;

//把page与1对比，取中间最大值
$page = max($page,1);

//每页显示条数
$pageSize = 6;
$offset = ($page-1)*$pageSize;

$username = config('mysql_username');
//var_dump($username);
$password = config('mysql_password');

//获取总记录条数
$sql = "select count(*) from `painting_product` WHERE state=1 ";
$row = pdo_select('mysql:host=127.0.0.1;dbname=painting',$username,$password,$sql);
$total = $row['0']['count(*)'];

unset($sql,$row);

//查询商品数据
$sql = "SELECT id,img,name,price,`desc` FROM `painting_product` WHERE state=1 ORDER BY id ASC ,page_view DESC LIMIT $offset,$pageSize";
$row = pdo_select('mysql:host=127.0.0.1;dbname=painting',$username,$password,$sql);

//分页;
$page = pages($total,$page,$pageSize,6);

include_once './View/index.html';
