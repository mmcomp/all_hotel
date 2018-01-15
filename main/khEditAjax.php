<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);

$khid = (isset($_POST['khid']))?$_POST['khid']:"";
$khad2 = (isset($_POST['khad2']))?$_POST['khad2']:"";
$cost2 = (isset($_POST['cost2']))?$_POST['cost2']:"";
$tedadR2 = (isset($_POST['tedadR2']))?$_POST['tedadR2']:"";
$tedad2 = (isset($_POST['tedad2']))?$_POST['tedad2']:"";
$voroo2 = (isset($_POST['voroo2']))?$_POST['voroo2']:"";
$khoroo2 = (isset($_POST['khoroo2']))?$_POST['khoroo2']:"";
$ekht2 = (isset($_POST['ekht2']))?$_POST['ekht2']:"";
$ghaza2 = (isset($_POST['ghaza2']))?$_POST['ghaza2']:"";
$mo2 = (isset($_POST['mo2']))?$_POST['mo2']:"";

  
 $query = mysql_class::ex_sqlx("update `khadamat` set `name`='$khad2',`ghimat_def`='$cost2',`typ`='$tedad2',`name`='$khad2',`ghimat_def`='$cost2',`voroodi_darad`='$voroo2',`khorooji_darad`='$khoroo2',`aval_ekhtiari`='$ekht2',`ghazaAst`='$ghaza2',`motefareghe`='$mo2',`tedadDarRuz`='$tedadR2' where `id` ='$khid'");
if($query) 
 echo "1";  
else
    echo "0";


?>