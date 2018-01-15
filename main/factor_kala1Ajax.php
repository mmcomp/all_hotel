<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);
$user_id = $_SESSION['user_id'];


$cname = (isset($_POST['cname']))?$_POST['cname']:"";
$toz = (isset($_POST['toz']))?$_POST['toz']:"";
$tarikh = (isset($_POST['tarikh']))?$_POST['tarikh']:"";
$moeen_id = (isset($_POST['moeen_id']))?$_POST['moeen_id']:"";
$tarikh1 =  audit_class::hamed_pdateBack($tarikh);


$query = mysql_class::ex_sqlx("insert into `anbar_factor` (`name`,`tozihat`,`moeen_id`,`tarikh_resid`,`anbar_typ_id`,`user_id`) values ('$cname','$toz','$moeen_id','$tarikh1','2','$user_id')");
if($query) 
    echo "1";  
else
    echo "0";

?>