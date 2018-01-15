<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);
$user_id = $_SESSION['user_id'];

$daftar1 = (isset($_POST['daftar1']))?$_POST['daftar1']:"";
$hotel1 = (isset($_POST['hotel1']))?$_POST['hotel1']:"";

$query = mysql_class::ex_sqlx("insert into `hotel_daftar` (`hotel_id`,`daftar_id`) values ('$hotel1','$daftar1')");
if($query) 
    echo "1";  
else
    echo "0";

?>