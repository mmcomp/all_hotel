<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$sms_id = (isset($_POST['sms_id']))?$_POST['sms_id']:"";
$matn = (isset($_POST['matn']))?$_POST['matn']:"";



 $query = mysql_class::ex_sqlx("update `mehman_sms` set `matn`='$matn' where `id` ='$sms_id'");
if($query) 
 echo "1";  
else
    echo "0";


?>