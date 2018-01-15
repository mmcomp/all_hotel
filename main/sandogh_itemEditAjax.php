<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$siid2 = (isset($_POST['siid2']))?$_POST['siid2']:"";
$mname2 = (isset($_POST['mname2']))?$_POST['mname2']:"";
$cost2 = (isset($_POST['cost2']))?$_POST['cost2']:"";
$loadSan2 = (isset($_POST['loadSan2']))?$_POST['loadSan2']:"";
$type2 = (isset($_POST['type2']))?$_POST['type2']:"";


$query = mysql_class::ex_sqlx("update `sandogh_item` set `name`='$mname2',`sandogh_id`='$loadSan2',`mablagh_det`='$cost2',`en`='$type2' where `id` ='$siid2'");
if($query)
 echo "1";  
else
    echo "0";


?>