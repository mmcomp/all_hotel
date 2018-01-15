<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$rrid2 = (isset($_POST['rrid2']))?$_POST['rrid2']:"";



 $query = mysql_class::ex_sqlx("delete from `room` where `id` ='$rrid2'");
if($query) 
 echo "1";  
else
    echo "0";


?>