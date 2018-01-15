<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$name1 = (isset($_POST['name1']))?$_POST['name1']:"";
$kala1 = (isset($_POST['kala1']))?$_POST['kala1']:"";
$tedad1 = (isset($_POST['tedad1']))?$_POST['tedad1']:"";



$query = mysql_class::ex_sqlx("insert into `cost_det` (`cost_kala_id`,`kala_id`,`tedad`) values ('$name1','$kala1','$tedad1')");
if($query)
    echo "1";  
else
    echo "0";

?>