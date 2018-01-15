<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);







$hid1 = (isset($_POST['hid1']))?$_POST['hid1']:"";
$rtype1 = (isset($_POST['rtype1']))?$_POST['rtype1']:"";
$rname1 = (isset($_POST['rname1']))?$_POST['rname1']:"";
$rtozih1 = (isset($_POST['rtozih1']))?$_POST['rtozih1']:"";
$rtabaghe1 = (isset($_POST['rtabaghe1']))?$_POST['rtabaghe1']:"";
$rghimat1 = (isset($_POST['rghimat1']))?$_POST['rghimat1']:"0";


 $query = mysql_class::ex_sqlx("insert into `room` (`hotel_id`,`room_typ_id`,`name`,`tozih`,`vaziat`,`tabaghe`,`ghimat`) values ('$hid1','$rtype1','$rname1','$rtozih1','2','$rtabaghe1',$rghimat1)");
if($query) 
 echo "1";  
else
    echo "0";


?>