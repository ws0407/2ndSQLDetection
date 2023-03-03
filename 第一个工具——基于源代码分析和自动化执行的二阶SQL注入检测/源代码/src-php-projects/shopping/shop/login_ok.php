<?php
	include("database.php");
	session_start();
?>

<?php
header("Content-Type:text/html;charset=utf-8");
	$n=$_POST["username"];
	$p=$_POST["password"];
	$sql="select * from customer where c_name='$n' and c_pass='$p' ";
	$link=new mysqli("localhost","root","123456", "shopping");
	$rs=$link->query($sql);
	if($rs->num_rows > 0){
		$r=mysqli_fetch_array($rs);
		$_SESSION['n']=$r['c_name'];
		echo "<script language='javascript'> ";
		echo "alert('登陆成功');";
		echo "window.location.href='customer.php'; ";
		echo "</script>";
	}else{
		echo "<script language='javascript'> ";
		echo "alert('登陆失败');";
		echo "window.location.href='index.php'; ";
		echo "</script>";
	}

?>