<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$gid = (isset($_POST['gid']))?$_POST['gid']:"";



 $query = mysql_class::ex_sqlx("delete from `hotel_garanti` where `id` ='$gid'");
if($query) 
 echo "1";  
else
    echo "0";


?>