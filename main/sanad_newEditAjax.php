<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);





$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$cost2 = (isset($_POST['cost2']))?$_POST['cost2']:"";
$toz2 = (isset($_POST['toz2']))?$_POST['toz2']:"";
$bes2 = (isset($_POST['bes2']))?$_POST['bes2']:"";




 $query = mysql_class::ex_sqlx("update `sanad` set `mablagh`='$cost2',`tozihat`='$toz2',`typ`='$bes2' where `id` ='$id2'");
if($query) 
 echo "1";  
else
    echo "0";


?>