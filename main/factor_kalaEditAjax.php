<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$cname2 = (isset($_POST['cname2']))?$_POST['cname2']:"";
$factor_id2 = (isset($_POST['factor_id2']))?$_POST['factor_id2']:"";
$toz2 = (isset($_POST['toz2']))?$_POST['toz2']:"";
$tarikh2 = (isset($_POST['tarikh2']))?$_POST['tarikh2']:"";
$moeen_id2 = (isset($_POST['moeen_id2']))?$_POST['moeen_id2']:"";
$tarikh1 =  audit_class::hamed_pdateBack($tarikh2);

 $query = mysql_class::ex_sqlx("update `anbar_factor` set `name`='$cname2',`factor_id`='$factor_id2',`tozihat`='$toz2',`tarikh_resid`='$tarikh1',`moeen_id`='$moeen_id2' where `id` ='$id2'");
if($query) 
 echo "1";  
else
    echo "0";


?>