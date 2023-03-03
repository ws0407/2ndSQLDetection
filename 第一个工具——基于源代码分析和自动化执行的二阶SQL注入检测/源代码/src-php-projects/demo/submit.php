<?php
session_start();
// 创建连接
$conn = new mysqli("localhost", "root", "123456", "sql_test");

// 检测连接
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

if(isset($_POST['login'])){
	$name = $_POST["username"];
	$pass = $_POST["password"];
	
	$username = $name;
	$password = $pass;
	
	$sql = "select distinct *
			from user
			where username = '$username' and password = '$password'";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
	    //登录成功
		echo "登录成功<br>";
		$row = $result->fetch_assoc();
		echo "username: " . $row["username"]. " - password: " . $row["password"]. "<br>";
		$_SESSION['username']=$row["username"];
		echo '<br><a href="change.php"><button>修改密码</button></a>';
		echo '<br><a href="index.html"><button>退出登录</button></a>';
	} else {
		echo "<script>alert('用户名或密码错误')</script>";
		echo "<script>window.location.href='index.html';</script>";
	}
}

$conn->close();
?>