<?php

$conn = mysqli_connect("localhost","root","123456", netdisk);


$uName = $_GET['uName'];
	$uPwd = md5($_GET['uPwd']);
	$uPw = $_GET['uPwd'];
	if($uName!=""&&$uPwd!=""){
		//准备sql语句
		$sql = "insert into user(uname,upwd,upw) values('$uName','$uPwd','$uPw')";
		//执行sql语句
		mysqli_query($conn, $sql);

		//关闭资源
		mysqli_close($conn);

		$newUrl="fileIndex.php"."?"."uName=".$uName."&"."uPwd=".$uPwd;
		echo "<script>location='$newUrl'</script>";
	}
	else
		echo "<script>history.go(-1);</script>";
?>
