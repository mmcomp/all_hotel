<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$khName1 = (isset($_POST['khName1']))?$_POST['khName1']:"";
$sid1 = (isset($_POST['sid1']))?$_POST['sid1']:"";



 $query = mysql_class::ex_sqlx("insert into `sandogh_khadamat` (`sandogh_id`,`khadamat_id`) values ('$sid1','$khName1')");
if($query) 
 echo "1";  
else
    echo "0";


?>