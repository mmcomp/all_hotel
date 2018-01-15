<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$user = (isset($_POST['user']))?$_POST['user']:"";
$sandogh = (isset($_POST['sandogh']))?$_POST['sandogh']:"";



if(mysql_class::ex_sqlx("insert into `sandogh_user` (`user_id`,`sandogh_id`) values ('$user','$sandogh')"))
    echo "1";  
else
    echo "0";


?>