<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);


$user = $_SESSION['user_id'];
$hotel1 = (isset($_POST['hotel1']))?$_POST['hotel1']:"";
$room1 = (isset($_POST['room1']))?$_POST['room1']:"";
$state1 = (isset($_POST['state1']))?$_POST['state1']:"";
$toz1 = (isset($_POST['toz1']))?$_POST['toz1']:"";
$ta = date("Y-m-d H:i:s");

 $query = mysql_class::ex_sqlx("insert into `tasisat_tmp` (`hotel_id`,`room_id`,`user_reg`,`toz`,`regdate`,`en`) values ('$hotel1','$room1','$user','$toz1','$ta','$state1')");
if($query) 
 echo "1";  
else
    echo "0";


?>