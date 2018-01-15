<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$daftar2 = (isset($_POST['daftar2']))?$_POST['daftar2']:"";
$ajans2 = (isset($_POST['ajans2']))?$_POST['ajans2']:"";
$fname2 = (isset($_POST['fname2']))?$_POST['fname2']:"";
$lname2 = (isset($_POST['lname2']))?$_POST['lname2']:"";
$user2 = (isset($_POST['user2']))?$_POST['user2']:"";
$pass2 = (isset($_POST['pass2']))?$_POST['pass2']:"";
$group2 = (isset($_POST['group2']))?$_POST['group2']:"";
$ccart2 = (isset($_POST['ccart2']))?$_POST['ccart2']:"";
$vh2 = (isset($_POST['vh2']))?$_POST['vh2']:"";
$vh22 = (isset($_POST['vh22']))?$_POST['vh22']:"";
$kh2 = (isset($_POST['kh2']))?$_POST['kh2']:"";
$kh22 = (isset($_POST['kh22']))?$_POST['kh22']:"";
$zh2 = (isset($_POST['zh2']))?$_POST['zh2']:"";

$query = mysql_class::ex_sqlx("update `user` set `daftar_id`='$daftar2',`ajans_id`='$ajans2',`fname`='$fname2',`lname`='$lname2',`user`='$user2',`pass`='$pass2',`typ`='$group2',`num_card`='$ccart2',`vorood`='$vh2',`khorooj`='$kh2',`vorood1`='$vh22',`khorooj1`='$kh22',`zaman_hozur`='$zh2' where `id` ='$id2'");

if($query)
    echo "1";
else
    echo "0";
?>