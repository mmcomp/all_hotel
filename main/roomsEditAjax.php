<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$rrid1 = (isset($_POST['rrid1']))?$_POST['rrid1']:"";
$rname2 = (isset($_POST['rname2']))?$_POST['rname2']:"";
$rtozih2 = (isset($_POST['rtozih2']))?$_POST['rtozih2']:"";
$rtype2 = (isset($_POST['rtype2']))?$_POST['rtype2']:"";
$rtabaghe2 = (isset($_POST['rtabaghe2']))?$_POST['rtabaghe2']:"";
// $rghimat2 = (isset($_POST['rghimat2']))?$_POST['rghimat2']:"";



// echo "update `room` set `room_typ_id`='$rtype2',`name`='$rname2',`tabaghe`='$rtabaghe2',`tozih`='$rtozih2',`ghimat`=$rghimat2 where `id` ='$rrid1'";
 $query = mysql_class::ex_sqlx("update `room` set `room_typ_id`='$rtype2',`name`='$rname2',`tabaghe`='$rtabaghe2',`tozih`='$rtozih2' where `id` ='$rrid1'");
if($query) 
 echo "1";  
else
    echo "0";


?>