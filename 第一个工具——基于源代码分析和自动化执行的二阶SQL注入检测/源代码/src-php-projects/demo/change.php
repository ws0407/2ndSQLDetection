<?php
session_start();
$username = $_SESSION["username"];
echo $username;
echo '<form method="post">
		  原来的密码<input type="password" name="old_password" required="required">
		  修改后的密码<input type="password" name="new_password" required="required">
		  <input type="submit" name="change" value="修改">
		</form>';


if(isset($_POST['change'])){
	// 创建连接
	$conn = new mysqli("10.122.241.50", "root", "123456", "sql_test");
	
	// 检测连接
	if ($conn->connect_error) {
	    die("数据库连接失败: " . $conn->connect_error);
	}
	
	$old_pass = $_POST["old_password"];
	$new_pass = $_POST["new_password"];
	
	$old_password = mysqli_real_escape_string($conn, $old_pass);
	$new_password = mysqli_real_escape_string($conn, $new_pass);
	
	$sql = "select distinct *
			from user
			where username = '$username' and password = '$old_password'";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
	    mysqli_query($conn,"UPDATE user SET password='$new_password'
	    WHERE username = '$username'");
		echo "<script>alert('修改成功')</script>";
		session_destroy();
		echo "<script>window.location.href='index.html';</script>";
	} else {
		echo "<script>alert('原密码错误')</script>";
	}
    $conn->close();
}
?>