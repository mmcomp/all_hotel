<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);


$hotel_id = (isset($_POST['hotel_id']))?$_POST['hotel_id']:"";
$sms_typ = (isset($_POST['sms_typ']))?$_POST['sms_typ']:"";
$matn = (isset($_POST['matn']))?$_POST['matn']:"";


 $query = mysql_class::ex_sqlx("insert into `mehman_sms` (`hotel_id`,`typ`,`matn`) values ('$hotel_id','$sms_typ','$matn')");
if($query) 
 echo "1";  
else
    echo "0";


?>