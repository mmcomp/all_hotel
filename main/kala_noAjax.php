<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$kala1 = (isset($_POST['kala1']))?$_POST['kala1']:"";
$code1 = (isset($_POST['code1']))?$_POST['code1']:"";



$query = mysql_class::ex_sqlx("insert into `kala_no` (`name`,`code`) values ('$kala1','$code1')");
if($query) 
    echo "1";  
else
    echo "0";


?>