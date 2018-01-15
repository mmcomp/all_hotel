<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$mname1 = (isset($_POST['mname1']))?$_POST['mname1']:"";
$sandogh1 = (isset($_POST['sandogh1']))?$_POST['sandogh1']:"";
$cost1 = (isset($_POST['cost1']))?$_POST['cost1']:"";
$type1 = (isset($_POST['type1']))?$_POST['type1']:"";



if(mysql_class::ex_sqlx("insert into `sandogh_item` (`name`,`sandogh_id`,`mablagh_det`,`en`) values ('$mname1','$sandogh1','$cost1','$type1')"))
    echo "1";  
else
    echo "0";


?>