<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$hotelName = (isset($_POST['hotelName']))?$_POST['hotelName']:"";
$DaftarName = (isset($_POST['DaftarName']))?$_POST['DaftarName']:"";
$tabagheh = (isset($_POST['tabagheh']))?$_POST['tabagheh']:"";
$gid = (isset($_POST['gid']))?$_POST['gid']:"";



 $query = mysql_class::ex_sqlx("update `hotel_garanti` set `hotel_id`='$hotelName',`daftar_id`='$DaftarName',`tabaghe`='$tabagheh' where `id` ='$gid'");
if($query) 
 echo "1";  
else
    echo "0";


?>