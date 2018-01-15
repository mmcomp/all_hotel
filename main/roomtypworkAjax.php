<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);







$hid1 = (isset($_POST['hid1']))?$_POST['hid1']:"";
$azta1 = (isset($_POST['azta1']))?$_POST['azta1']:"";
$azta=  audit_class::hamed_pdateBack($azta1);
$tata1 = (isset($_POST['tata1']))?$_POST['tata1']:"";
$tata=  audit_class::hamed_pdateBack($tata1);
$type1 = (isset($_POST['type1']))?$_POST['type1']:"";
$cost1 = (isset($_POST['cost1']))?$_POST['cost1']:"";
$room_typ_id1 = (isset($_POST['room_typ_id1']))?$_POST['room_typ_id1']:"";
$ghimat_ezafe1 = (isset($_POST['ghimat_ezafe1']))?$_POST['ghimat_ezafe1']:"";



$query = mysql_class::ex_sqlx("insert into `room_typ_working_date` (`hotel_id`,`aztarikh`,`tatarikh`,`typ`,`ghimat`,`ghimat_ezafe`,`room_typ_id`) values ('$hid1','$azta','$tata','$type1','$cost1','$ghimat_ezafe1','$room_typ_id1')");
if($query) 
 echo "1";  
else
    echo "0";


?>