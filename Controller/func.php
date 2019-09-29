<?php
/**
 * Created by PhpStorm.
 * User: Mr.何
 * Date: 2018/2/26
 * Time: 20:24
 */
//设置字符集
header('content-type:text/html;charset=utf8');

//设置时区
date_default_timezone_set('PRC');

//开启SEESSION
session_start();

/**
 * 验证用户是否登陆
 * @return bool 如果登陆返回true，反之返回false
 */
function checkLogin(){
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])){
        return false;
    }
    return true;
}

/**
 * 跳转消息链接
 * @param string $type         消息类型
 * @param null $msg     消息提示内容
 * @param null $url     要跳转的链接
 */
function jump($type,$msg=null,$url=null){
//    var_dump("Location:msg.php?type={$type}&msg={$msg}&url={$url}");die;
    header("Location:msg.php?type={$type}&msg={$msg}&url={$url}");

    return;
}

/**
 * 对密码进行加密函数
 * @param string $password     需加密的字符串
 * @return bool|string  返回加密后的字符串
 */
function encryption($password){
    if (!isset($password)){
        return false;
    }
    $res = md5(md5($password.'HeJiang'));
    return $res;
}

/**
 * 上传图片函数
 * @param array $file         文件信息
 * @return bool|string  成功返回图片地址|或者false
 */
function imgUpload($file){
    $now = $_SERVER['REQUEST_TIME'];
    if(!is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    //图片类型验证
    if (!in_array($file['type'],array('image/png','image/gif','image/jpeg','image/jpg'))){
        return false;
    }
    //上传目录
    $uploadPath = './View/uploads/';
    //上传目录访问URL
    $uploadUrl = 'View/uploads/';
    //上传文件夹
    $fileDir = date('Y-m-d',$now).'/';
    //检查上传目录是否存在
    if (!is_dir($uploadPath.$fileDir)){
        mkdir($uploadPath.$fileDir,0777,true);  //创建目录
    }
    //拿到上传文件的扩展名
    $fileExt = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
    //利用时间微妙和随机数字生成一个唯一的文件名
    $imgName = uniqid().mt_rand(1,9999).'.'.$fileExt;
    //文件物理地址
    $imgPath = $uploadPath.$fileDir.$imgName;
    //文件URL地址
    $imgUrl = '/'.$uploadUrl.$fileDir.$imgName;
    //检测文件是否移动成功,如果操作失败，就需要去查看目录的权限，是否是可读可写的
    if (!move_uploaded_file($file['tmp_name'],$imgPath)){
        return false;
    }
    //上传成功，返回URL地址
    return $imgUrl;

}

/**
 * 分页显示
 * @param int $total        数据记录总数
 * @param int $currentPage  当前页
 * @param int $pageSize     每页显示条数
 * @param int $show         显示按钮数
 * @return string           返回div
 */
function pages($total,$currentPage,$pageSize,$show=6){
    $pageStr = '';
    //仅当总数大于每页显示条数，才进行分页处理
    if ($total>$pageSize){
        $totalPage = ceil($total/$pageSize);    //向上取整获取总页数
        //当用户输入的页码大于总页数时，直接显示最后一页
        $currentPage = $currentPage>$totalPage?$totalPage:$currentPage;
        //分页起始显示页面
        $from = max(1,$currentPage - intval($show/2));
        //分页结束页
        $to = $from+$show-1;

        $pageStr .= "<div class='page-nav'>";
        $pageStr .= "<ul>";
        //仅当当前也=页大于1，存在首页和上一页按钮
        if ($currentPage>1){
            $pageStr .= "<li><a href='index.php?page=1'>首页</a></li>";
            $pageStr .= "<li><a href='index.php?page=".($currentPage-1)."'>上一页</a></li>";
        }

        //当结束页大于总页
        if ($to>$totalPage){
            $to = $totalPage;
            $from = max(1,$to-$show+1);
        }
        if ($from > 1){
            $pageStr .= "<li>...</li>";
        }
        for ($i=$from;$i<=$to;$i++){
            if ($i!=$currentPage){
                $pageStr .= "<li><a href='index.php?page=".$i."'>{$i}</a></li>";
            }else{
                $pageStr .= "<li><span class='first-page'>{$i}</span></li>";
            }
        }
        if ($to < $totalPage){
            $pageStr .= "<li>...</li>";
        }

        if ($currentPage<$totalPage){
            $pageStr .= "<li><a href='index.php?page=".($currentPage+1)."'>下一页</a></li>";
            $pageStr .= "<li><a href='index.php?page=".$totalPage."'>尾页</a></li>";
        }
        $pageStr .= "</ul>";
        $pageStr .= "</div>";

    }
    return $pageStr;
}

/**
 * @return string   返回URL链接地址
 */
function getUrl(){
    $url = '';
    $url .= $_SERVER['SERVER_PORT'] == 443?'https://':'http://';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= $_SERVER['REQUEST_URI'];
    return $url;
}

/**
 * @param $page
 * @param $url
 * @return string
 */
function pageUrl($page,$url){
    $url = empty($url)?getUrl():$url;
    $pos = strpos($url,'?');
    if ($pos === false){
        $url .= '?page='.$page;
    }else{
        $getString = substr($url,$pos+1);
        parse_str($getString,$getArray);
        if (isset($getArray['page'])){
            unset($getArray['page']);
        }
        $getArray['page'] = $page;
        $str = http_build_query($getArray);
        $url = substr($url,0,$pos).'?'.$str;
    }
    return $url;
}

/**
 * 连接数据库，执行插入数据的语句，返回受影响的条数
 * @param string $dsn          数据库dsn
 * @param string $username     数据库用户名
 * @param string $password     数据库密码
 * @param string $sql          要执行的sql
 * @return bool|int     成功返回受影响的条数，失败返回false
 */
function pdo_insert($dsn,$username,$password,$sql){
    try{
        $pdo = new PDO($dsn,$username,$password);
        $stmt = $pdo->query($sql);
        $res = $stmt->rowCount();
        if (!$res){
            return false;
        }
        unset($pdo);
        return $res;
    }catch (PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * 连接数据库，执行查询数据操作，返回结果集
 * @param string $dsn          数据库dsn
 * @param string $username     数据库用户名
 * @param string $password     数据库密码
 * @param string $sql          要执行的sql
 * @return int                 返回受影响的记录条数
 */
function pdo_select($dsn,$username,$password,$sql){
    try{
        $pdo = new PDO($dsn,$username,$password);
//        var_dump($sql);die;
        $stmt = $pdo->query($sql);
//        var_dump($stmt);die;
        $count = $stmt->rowCount();
        if (!$count){
            return false;
        }
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($pdo);
        return $row;
    }catch (PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * 连接数据库，执行更新数据操作，返回受影响的记录条数
 * @param string $dsn          数据库dsn
 * @param string $username     数据库用户名
 * @param string $password     数据库密码
 * @param string $sql          要执行的sql
 * @return bool|int
 */
function pdo_update($dsn,$username,$password,$sql){
    try{
        $pdo = new PDO($dsn,$username,$password);
        $stmt = $pdo->query($sql);
        $res = $stmt->rowCount();
        if (!$res){
            return false;
        }
        unset($pdo);
        return $res;
    }catch (PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * 连接数据库，执行更新数据操作，返回受影响的记录条数
 * @param string $dsn          数据库dsn
 * @param string $username     数据库用户    名
 * @param string $password     数据库密码
 * @param string $sql          要执行的sql
 * @return bool|int
 */
function pdo_delete($dsn,$username,$password,$sql){
    try{
        $pdo = new PDO($dsn,$username,$password);
        $stmt = $pdo->query($sql);
        $res = $stmt->rowCount();
        if (!$res){
            return false;
        }
        unset($pdo);
        return $res;
    }catch (PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * 执行一条SQL语句，返回受影响记录条数
 * @param string $dsn          数据库dsn
 * @param string $username     数据库用户名
 * @param string $password     数据库密码
 * @param string $sql          要执行的sql
 * @return bool|int         返回false或者受影响的条数
 */
function pdo_query($dsn,$username,$password,$sql){
    try{
        $pdo = new PDO($dsn,$username,$password);

        $stmt = $pdo->query($sql);
        $a = $stmt->rowCount();
        if (!$a){
            return false;
        }
        unset($pdo);
        return $a;
    }catch (PDOException $e){
        echo $e->getMessage();
    }
}

function config($name){
    $a = include __DIR__.'/config.php';

    return $a[$name];
}