<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$toz2 = (isset($_POST['toz2']))?$_POST['toz2']:"";
$fixtoz2 = (isset($_POST['fixtoz2']))?$_POST['fixtoz2']:"";
$hotel2 = (isset($_POST['hotel2']))?$_POST['hotel2']:"";
$room2 = (isset($_POST['room2']))?$_POST['room2']:"";
$state2 = (isset($_POST['state2']))?$_POST['state2']:"";
$user = $_SESSION['user_id'];
$ta = date("Y-m-d H:i:s");

if($state2==1){
    $query = mysql_class::ex_sqlx("update `tasisat_tmp` set `hotel_id`='$hotel2',`room_id`='$room2',`user_fixed`='$user',`toz`='$toz2',`toz_fix`='$fixtoz2',`date_fix`='$ta',`en`='$state2' where `id` ='$id2'");
}

if($state2==-1){
    $query = mysql_class::ex_sqlx("update `tasisat_tmp` set `hotel_id`='$hotel2',`room_id`='$room2',`toz`='$toz2',`en`='$state2' where `id` ='$id2'");
}

if($query)
    echo "1";
else
    echo "0";
?>