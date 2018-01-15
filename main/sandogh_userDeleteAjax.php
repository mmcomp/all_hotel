<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$sssid3 = (isset($_POST['sssid3']))?$_POST['sssid3']:"";



 $query = mysql_class::ex_sqlx("delete from `sandogh_user` where `id` ='$sssid3'");
if($query) 
 echo "1";  
else
    echo "0";


?>