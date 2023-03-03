<?php

$username = generateRandomString(10);

$sploitname1 = $username."'#";
$selectname1 = $username."''#";

$sploitname2 = $username."'-- ";
$selectname2 = $username."''-- ";


$url = 'http://localhost:63342/SQL%E6%B3%A8%E5%85%A5%E7%8E%AF%E5%A2%83/register.php?_ijt=cs7qvt28thdpvur0v7mnbrdno8';
$cookie = dirname(__FILE__) . '/cookie_curl.txt';
$info1 = array(
    'username' => $sploitname1,
    'password' => 'shit'
);
$info2 = array(
    'username' => $sploitname2,
    'password' => 'shit'
);

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function regist($url, $info, $cookie){
    $curl = curl_init();//初始化curl模块
    curl_setopt($curl, CURLOPT_URL, $url);//登录提交的地址
    curl_setopt($curl, CURLOPT_HEADER, 0);//是否显示头信息
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);//是否自动显示返回的信息
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); //设置Cookie信息保存在指定的文件中
    curl_setopt($curl, CURLOPT_POST, 1);//post方式提交
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($info));//要提交的信息
    curl_exec($curl);//执行cURL
    curl_close($curl);//关闭cURL资源，并且释放系统资源
}

function search($selectname, $sploitname){
    $conn = new mysqli("127.0.0.1", "root", "906726617", "sql_test");

    if ($conn->connect_error) {
        die("数据库连接失败: " . $conn->connect_error);
    }

    $sql = "select distinct * from user
        where username = '$selectname'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        echo "存在用户名为$sploitname 的用户";
    }
    else{
        echo "未找到用户名为$sploitname 的用户";
    }
    $conn->close();
}



regist($url, $info1, $cookie);
regist($url, $info2, $cookie);
search($selectname1, $sploitname1);
search($selectname2, $sploitname2);




