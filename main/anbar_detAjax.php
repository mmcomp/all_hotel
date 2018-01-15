<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);


$kala1 = (isset($_POST['kala1']))?$_POST['kala1']:"";
$anbar_id = (isset($_POST['anbar_id']))?$_POST['anbar_id']:"";
$anbar_factor_id = (isset($_POST['anbar_factor_id']))?$_POST['anbar_factor_id']:"";
$other1 = (isset($_POST['other1']))?$_POST['other1']:"";
$tarikh1 = (isset($_POST['tarikh1']))?$_POST['tarikh1']:"";
$tarikh = audit_class::hamed_pdateBack(perToEnNums($tarikh1));
$tedad1 = (isset($_POST['tedad1']))?$_POST['tedad1']:"";
$ghimat1 = (isset($_POST['ghimat1']))?$_POST['ghimat1']:"";
$user_id = $_SESSION['user_id'];

$query = mysql_class::ex_sqlx("insert into `anbar_det` (`kala_id`,`tarikh`,`user_id`,`tedad`,`ghimat`,`other_user_id`,`anbar_id`,`anbar_typ_id`,`anbar_factor_id`,`en`) values ('$kala1','$tarikh','$user_id','$tedad1','$ghimat1','$other1','$anbar_id','1','$anbar_factor_id','0')");
if($query) 
 echo "1";  
else
    echo "0";


?>