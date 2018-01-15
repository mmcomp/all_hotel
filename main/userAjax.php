<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$kh12 = (isset($_POST['kh12']))?$_POST['kh12']:"";
$zh1 = (isset($_POST['zh1']))?$_POST['zh1']:"";
$fname1 = (isset($_POST['fname1']))?$_POST['fname1']:"";
$lname1 = (isset($_POST['lname1']))?$_POST['lname1']:"";
$user1 = (isset($_POST['user1']))?$_POST['user1']:"";
$pass1 = (isset($_POST['pass1']))?$_POST['pass1']:"";
$vh12 = (isset($_POST['vh12']))?$_POST['vh12']:"";
$ccart1 = (isset($_POST['ccart1']))?$_POST['ccart1']:"";
$vh1 = (isset($_POST['vh1']))?$_POST['vh1']:"";
$kh1 = (isset($_POST['kh1']))?$_POST['kh1']:"";
$daftar1 = (isset($_POST['daftar1']))?$_POST['daftar1']:"";
$ajans1 = (isset($_POST['ajans1']))?$_POST['ajans1']:"";
$group1 = (isset($_POST['group1']))?$_POST['group1']:"";



 $query = mysql_class::ex_sqlx("insert into `user` (`daftar_id`,`ajans_id`,`fname`,`lname`,`user`,`pass`,`typ`,`num_card`,`vorood`,`khorooj`,`vorood1`,`khorooj1`,`zaman_hozur`) values ('$daftar1','$ajans1','$fname1','$lname1','$user1','$pass1','$group1','$ccart1','$vh1','$kh1','$vh12','$kh12','$zh1')");
if($query) 
 echo "1";  
else
    echo "0";


?>