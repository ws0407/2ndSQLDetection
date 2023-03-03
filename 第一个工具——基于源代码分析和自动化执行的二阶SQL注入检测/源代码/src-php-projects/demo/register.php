<?php
// 创建连接
$conn = new mysqli("localhost", "root", "123456", "sql_test");

// 检测连接
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

$name = $_POST["username"];
$pass = $_POST["password"];

$username = mysqli_real_escape_string($conn, $name);
$password = mysqli_real_escape_string($conn, $pass);

$sql = "select distinct *
		from user
		where username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0){
	echo "<script>alert('用户名已被注册！')</script>";
	echo "<script>window.location.href='register.html';</script>";
}

$sql1 = "INSERT INTO user (username, password)
VALUES ('$username', '$password')";

if ($conn->query($sql1) === TRUE) {
    echo "<script>alert('注册成功')</script>";
	echo "<script>window.location.href='index.html';</script>";
} else {
    echo "Error: " . $sql1 . "<br>" . $conn->error;
}

$conn->close();
?>