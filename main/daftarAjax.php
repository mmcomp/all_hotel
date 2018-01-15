<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);
$user_id = $_SESSION['user_id'];


$daftar1 = (isset($_POST['daftar1']))?$_POST['daftar1']:"";
$toz1 = (isset($_POST['toz1']))?$_POST['toz1']:"";
$takhf1 = (isset($_POST['takhf1']))?$_POST['takhf1']:"";
$cg1 = (isset($_POST['cg1']))?$_POST['cg1']:"";
$kol1 = (isset($_POST['kol1']))?$_POST['kol1']:"";

$query = mysql_class::ex_sqlx("insert into `daftar` (`name`,`kol_id`,`toz`,`css_class`,`takhfif`) values ('$daftar1','$kol1','$toz1','$cg1','$takhf1')");
if($query) 
    echo "1";  
else
    echo "0";

?>