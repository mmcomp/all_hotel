<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$name2 = (isset($_POST['name2']))?$_POST['name2']:"";
$kala2 = (isset($_POST['kala2']))?$_POST['kala2']:"";
$tedad2 = (isset($_POST['tedad2']))?$_POST['tedad2']:"";




 $query = mysql_class::ex_sqlx("update `cost_det` set `cost_kala_id`='$name2',`kala_id`='$kala2',`tedad`='$tedad2' where `id` ='$id2'");
if($query) 
 echo "1";  
else
    echo "0";


?>