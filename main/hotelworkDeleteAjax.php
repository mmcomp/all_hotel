<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$hwid3 = (isset($_POST['hwid3']))?$_POST['hwid3']:"";



 $query = mysql_class::ex_sqlx("delete from `hotel_working_date` where `id` ='$hwid3'");
if($query) 
 echo "1";  
else
    echo "0";


?>