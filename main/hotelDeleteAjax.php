<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$hotelid = (isset($_POST['hotelid']))?$_POST['hotelid']:"";



 $query = mysql_class::ex_sqlx("delete from `hotel` where `id` ='$hotelid'");
if($query) 
 echo "1";  
else
    echo "0";


?>