<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$name2 = (isset($_POST['name2']))?$_POST['name2']:"";
$code2 = (isset($_POST['code2']))?$_POST['code2']:"";
$type2 = (isset($_POST['type2']))?$_POST['type2']:"";
$vahed2 = (isset($_POST['vahed2']))?$_POST['vahed2']:"";

  
$query = mysql_class::ex_sqlx("update `kala` set `name`='$name2',`code`='$code2',`kala_no_id`='$type2',`vahed_id`='$vahed2' where `id` ='$id2'");
if($query) 
    echo "1";  
else
    echo "0";


?>