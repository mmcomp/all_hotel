<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$name2 = (isset($_POST['name2']))?$_POST['name2']:"";

  
$query = mysql_class::ex_sqlx("update `kala_vahed` set `name`='$name2' where `id` ='$id2'");
if($query) 
    echo "1";  
else
    echo "0";


?>