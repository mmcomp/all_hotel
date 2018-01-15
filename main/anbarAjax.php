<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$name1 = (isset($_POST['name1']))?$_POST['name1']:"";
$location1 = (isset($_POST['location1']))?$_POST['location1']:"";
$state1 = (isset($_POST['state1']))?$_POST['state1']:"";



 $query = mysql_class::ex_sqlx("insert into `anbar` (`name`,`location`,`en`) values ('$name1','$location1','$state1')");
if($query) 
 echo "1";  
else
    echo "0";


?>