<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$rtzarfiat_ezafe = (isset($_POST['rtcapa']))?$_POST['rtzarfiat_ezafe']:"";
$rtcapa = (isset($_POST['rtcapa']))?$_POST['rtcapa']:"";
$rtname = (isset($_POST['rtname']))?$_POST['rtname']:"";


// echo   "insert into `room_typ` (`name`,`zarfiat`,`zarfiat_ezafe`) values ('$rtname','$rtcapa','$rtzarfiat_ezafe')";
 $query = mysql_class::ex_sqlx("insert into `room_typ` (`name`,`zarfiat`,`zarfiat_ezafe`) values ('$rtname','$rtcapa','$rtzarfiat_ezafe')");
if($query) 
 echo "1";  
else
    echo "0";


?>