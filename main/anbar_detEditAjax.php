<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);


$kala2 = (isset($_POST['kala2']))?$_POST['kala2']:"";
$other2 = (isset($_POST['other2']))?$_POST['other2']:"";
$tarikh2 = (isset($_POST['tarikh2']))?$_POST['tarikh2']:"";
$tarikh = audit_class::hamed_pdateBack(perToEnNums($tarikh2));
$tedad2 = (isset($_POST['tedad2']))?$_POST['tedad2']:"";
$ghimat2 = (isset($_POST['ghimat2']))?$_POST['ghimat2']:"";
$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$user_id = $_SESSION['user_id'];
 $query = mysql_class::ex_sqlx("update `anbar_det` set `kala_id`='$kala2',`tarikh`='$tarikh',`user_id`='$user_id',`tedad`='$tedad2',`ghimat`='$ghimat2',`other_user_id`='$other2' where `id` ='$id2'");
if($query){ 
 echo "1";
 }    
else
    echo "0";


?>