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
$room_typ_id2 = (isset($_POST['room_typ_id2']))?$_POST['room_typ_id2']:"";
$ghimat_ezafe2 = (isset($_POST['ghimat_ezafe2']))?$_POST['ghimat_ezafe2']:"";


$sql = "update `room_typ_working_date` set `aztarikh`='$azta',`tatarikh`='$tata',`typ`='$type2',`ghimat`='$cost2',`ghimat_ezafe`='$ghimat_ezafe2',`room_typ_id`='$room_typ_id2' where `id` ='$hwid2'";
// echo $sql."\n";
$query = mysql_class::ex_sqlx($sql);
if($query) 
 echo "1";  
else
    echo "0";


?>