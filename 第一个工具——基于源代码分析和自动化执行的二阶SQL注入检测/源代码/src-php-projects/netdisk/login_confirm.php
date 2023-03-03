<?php

$conn = mysqli_connect("localhost","root","123456", netdisk);

//获取用户
    $uName = $_GET['uName'];
    $uPwd = md5($_GET['uPwd']);

    //准备sql语句
    $sql1 = "select upwd from user where  uname  = '$uName'";
    //执行sql语句
    $result = mysqli_query($conn, $sql1);
    $rows=mysqli_fetch_row($result);

	$newUrl="fileIndex.php"."?"."uName=".$uName."&"."uPwd=".$uPwd;
	if($uPwd === $rows[0]){
        echo "<script>location='$newUrl'</script>";
    }
    else{
        echo "<script>history.go(-1);alert('密码错误');</script>";
    }

?>
