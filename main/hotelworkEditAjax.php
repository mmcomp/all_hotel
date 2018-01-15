<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$hwid2 = (isset($_POST['hwid2']))?$_POST['hwid2']:"";
$azta2 = (isset($_POST['azta2']))?$_POST['azta2']:"";
$azta= audit_class::hamed_pdateBack($azta2);
$tata2 = (isset($_POST['tata2']))?$_POST['tata2']:"";
$tata=  audit_class::hamed_pdateBack($tata2);
$type2 = (isset($_POST['type2']))?$_POST['type2']:"";
$cost2 = (isset($_POST['cost2']))?$_POST['cost2']:"";



 $query = mysql_class::ex_sqlx("update `hotel_working_date` set `aztarikh`='$azta',`tatarikh`='$tata',`typ`='$type2',`ghimat`='$cost2' where `id` ='$hwid2'");
if($query) 
 echo "1";  
else
    echo "0";


?>