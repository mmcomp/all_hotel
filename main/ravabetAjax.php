<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);


$qid = (isset($_POST['qid']))?$_POST['qid']:"";
$room_id = (isset($_POST['room_id']))?$_POST['room_id']:"";
$reserve_id = (isset($_POST['reserve_id']))?$_POST['reserve_id']:"";
$user_id = (isset($_POST['user_id']))?$_POST['user_id']:"";
$tarikh = (isset($_POST['tarikh']))?$_POST['tarikh']:"";
$ans = (isset($_POST['ans']))?$_POST['ans']:"";


$query = mysql_class::ex_sqlx("");
if($query) 
 echo "1";  
else
    echo "0";


?>