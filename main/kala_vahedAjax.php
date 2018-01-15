<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$name1 = (isset($_POST['name1']))?$_POST['name1']:"";



$query = mysql_class::ex_sqlx("insert into `kala_vahed` (`name`) values ('$name1')");
if($query) 
    echo "1";  
else
    echo "0";


?>