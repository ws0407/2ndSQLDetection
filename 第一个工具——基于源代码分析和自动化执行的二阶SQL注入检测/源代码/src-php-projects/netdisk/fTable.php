<?php

	$conn = mysqli_connect("localhost","root","123456", netdisk);
	@$uName = $_GET['uName'];
	$sql3 = "select id from user where uname = '$uName'";
	$result3 = mysqli_query($conn, $sql3);
	$rows3=mysqli_fetch_row($result3);
	$uId = $rows3[0];

	$sql4 = "select * from file where uid = '$uId'";
	$result4 = mysqli_query($conn, $sql4);

	echo "<table   width='600px' align='center'>";

	while(@$set=mysqli_fetch_row($result4)){
			 echo "<tr>";
			 echo "<td id='fTd' style='font-size:16px; font-family:幼圆; border-bottom:1px solid #eee; text-align:center; height:30px; line-height:30px; background-color:#eee;'>{$set[1]}</td>";
			 echo "<td style='font-size:16px; font-family:幼圆; border-bottom:1px solid #eee; text-align:center;  height:30px; line-height:30px; background-color:#eee;'>{$set[2]}</td>";
			 echo "<td style='font-size:16px; font-family:幼圆; border-bottom:1px solid #eee; text-align:center;  height:30px; line-height:30px; background-color:#eee;'>{$set[3]}K</td>";
			 echo "<td style='font-size:16px; font-family:幼圆; border-bottom:1px solid #eee; text-align:center;  height:30px; line-height:30px; background-color:#eee;'><a style='display:block;' href='download.php?fname={$set[1]}&uId={$uId}'><img src='images/download.jpg'></a></td>";
			 echo "<td style='font-size:16px; font-family:幼圆; border-bottom:1px solid #eee; text-align:center;  height:30px; line-height:30px; background-color:#eee;'>{$set[4]}</td>";
      	 echo "</tr>";
	}

	echo "</table>";
?>
