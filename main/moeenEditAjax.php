<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$name2 = (isset($_POST['name2']))?$_POST['name2']:"";
$kol2 = (isset($_POST['kol2']))?$_POST['kol2']:"";
$type2 = (isset($_POST['type2']))?$_POST['type2']:"";

  
$query = mysql_class::ex_sqlx("update `moeen` set `name`='$name2',`kol_id`='$kol2',`typ`='$type2' where `id` ='$id2'");
if($query) 
    echo "1";  
else
    echo "0";


?>