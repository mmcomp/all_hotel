<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$hid = (isset($_POST['hid']))?$_POST['hid']:"";
$khad = (isset($_POST['khad']))?$_POST['khad']:"";
$cost = (isset($_POST['cost']))?$_POST['cost']:"";
$tedadR = (isset($_POST['tedadR']))?$_POST['tedadR']:"";
$tedad = (isset($_POST['tedad']))?$_POST['tedad']:"";
$voroo = (isset($_POST['voroo']))?$_POST['voroo']:"";
$khoroo = (isset($_POST['khoroo']))?$_POST['khoroo']:"";
$ekht = (isset($_POST['ekht']))?$_POST['ekht']:"";
$ghaza = (isset($_POST['ghaza']))?$_POST['ghaza']:"";
$mot = (isset($_POST['mot']))?$_POST['mot']:"";



 $query = mysql_class::ex_sqlx("insert into `khadamat` (`hotel_id`,`name`,`ghimat_def`,`typ`,`voroodi_darad`,`khorooji_darad`,`aval_ekhtiari`,`ghazaAst`,`motefareghe`,`tedadDarRuz`) values ('$hid','$khad','$cost','$tedad','$voroo','$khoroo','$ekht','$ghaza','$mot','$tedadR')");
if($query) 
 echo "1";  
else
    echo "0";


?>