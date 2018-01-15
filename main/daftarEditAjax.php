<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);


$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$daftar2 = (isset($_POST['daftar2']))?$_POST['daftar2']:"";
$toz2 = (isset($_POST['toz2']))?$_POST['toz2']:"";
$takhf2 = (isset($_POST['takhf2']))?$_POST['takhf2']:"";
$kol2 = (isset($_POST['kol2']))?$_POST['kol2']:"";
$cg2 = (isset($_POST['cg2']))?$_POST['cg2']:"";

$query = mysql_class::ex_sqlx("update `daftar` set `name`='$daftar2',`kol_id`='$kol2',`toz`='$toz2',`css_class`='$cg2',`takhfif`='$takhf2' where `id` ='$id2'");
if($query) 
 echo "1";  
else
    echo "0";


?>



