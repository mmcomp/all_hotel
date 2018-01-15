<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$rtid = (isset($_POST['rtid3']))?$_POST['rtid3']:"";



 $query = mysql_class::ex_sqlx("delete from `room_typ` where `id` ='$rtid'");
if($query) 
 echo "1";  
else
    echo "0";


?>