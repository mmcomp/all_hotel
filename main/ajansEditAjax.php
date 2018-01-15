<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);

$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$daftar2 = (isset($_POST['daftar2']))?$_POST['daftar2']:"";
$toz2 = (isset($_POST['toz2']))?$_POST['toz2']:"";
$ajans2 = (isset($_POST['ajans2']))?$_POST['ajans2']:"";
$send2 = (isset($_POST['send2']))?$_POST['send2']:"";
$tell2 = (isset($_POST['tell2']))?$_POST['tell2']:"";
$protected2 = (isset($_POST['protected2']))?$_POST['protected2']:"";

$query = mysql_class::ex_sqlx("update `ajans` set `name`='$ajans2',`daftar_id`='$daftar2',`tozihat`='$toz2',`ersal_moshtari`='$send2',`tell`='$tell2',`protected`='$protected2' where `id` ='$id2'");
if($query) 
 echo "1";  
else
    echo "0";






?>



