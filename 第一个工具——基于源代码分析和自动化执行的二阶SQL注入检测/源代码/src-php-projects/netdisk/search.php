<?php

	require("conn.php");
            $conn = mysqli_connect("localhost","root","123456", "netdisk");
    //获取用户
    $fName = $_GET['fName'];
	$uId = $_GET['uId'];
	$uName = $_GET['uName'];
    $uPwd = $_GET['uPwd'];


    //准备sql语句
    $sql1 = "select fname from file where  fname like '%$fName%' and uId = '$uId'" ;
    //执行sql语句
    $result = mysqli_query($conn, $sql1);
    $rows=mysqli_fetch_row($result);


	if(!empty($rows[0])) {
		$newUrl="fileIndex.php"."?"."uName=".$uName."&"."uPwd=".$uPwd."&"."fName=".$rows[0];
		echo "<script>location='$newUrl'</script>";
    }
    else{
        echo "<script>history.go(-1);alert('未查询到');</script>";
    }
?>
