<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);





$rtid = (isset($_POST['rtid2']))?$_POST['rtid2']:"";
$rtname = (isset($_POST['rtname2']))?$_POST['rtname2']:"";
$rtcapa = (isset($_POST['rtcapa2']))?$_POST['rtcapa2']:"";
$rtzarfiat_ezafe= (isset($_POST['rtcapa2']))?$_POST['rtzarfiat_ezafe2']:"";


 $query = mysql_class::ex_sqlx("update `room_typ` set `name`='$rtname',`zarfiat`='$rtcapa',`zarfiat_ezafe`='$rtzarfiat_ezafe' where `id` ='$rtid'");
if($query) 
 echo "1";  
else
    echo "0";


?>