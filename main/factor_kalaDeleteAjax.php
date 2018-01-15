<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$id4 = (isset($_POST['id4']))?$_POST['id4']:"";



 $query = mysql_class::ex_sqlx("delete from `anbar_factor` where `id` ='$id4'");
if($query) 
 echo "1";  
else
    echo "0";


?>