<?php

$fName = $_GET['fname'];
$uId = $_GET['uId'];

$conn = mysqli_connect("localhost","root","123456", "netdisk");
$sql3 = "select distinct * from file where uid = '$uId' and fname = '$fName'";
$result3 = mysqli_query($conn, $sql3);
while(@$set=mysqli_fetch_row($result3)){
    header('content-disposition:attachment;filename='.basename($set[5]));
    header('content-length:'.filesize($set[5]));
    readfile($set[5]);
    break;
}

mysqli_close($conn);

