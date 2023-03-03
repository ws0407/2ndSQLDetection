<?php
	include("database.php");
	session_start();
?>

<?php
header("Content-Type:text/html;charset=utf-8");
	$user=$_SESSION["c_fn"];
	$newpass=$_POST["newpass"];
	$sql="select * from customer where c_name='$user' ";
	$link=new mysqli("localhost","root","123456", "shopping");
	$rs = $link->query($sql);
	if($rs->num_rows > 0)
	{
		$sql1="update customer set c_pass='$newpass' where c_name='$user' ";
		$link=new mysqli("localhost","root","123456", "shopping");
$rs1 = $link->query($sql1);
		echo "<script language='javascript'> ";
		echo "alert('修改成功');";
		echo "window.location.href='index.php'; ";
		echo "</script>";
	}else{
		echo "<script language='javascript'> ";
		echo "alert('修改失败');";
		echo "window.location.href='forget_mima.php'; ";
		echo "</script>";
	}

?>