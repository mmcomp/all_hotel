<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$hotelName = (isset($_POST['hotelName']))?$_POST['hotelName']:"";
$DaftarName = (isset($_POST['DaftarName']))?$_POST['DaftarName']:"";
$tabagheh = (isset($_POST['tabagheh']))?$_POST['tabagheh']:"";



 $query = mysql_class::ex_sqlx("insert into `hotel_garanti` (`hotel_id`,`daftar_id`,`tabaghe`) values ('$hotelName','$DaftarName','$tabagheh')");
if($query) 
 echo "1";  
else
    echo "0";


?>