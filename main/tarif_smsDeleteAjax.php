<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$smsid = (isset($_POST['smsid']))?$_POST['smsid']:"";



 $query = mysql_class::ex_sqlx("delete from `mehman_sms` where `id` ='$smsid'");
if($query) 
 echo "1";  
else
    echo "0";


?>