<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);


$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$daftar2 = (isset($_POST['daftar2']))?$_POST['daftar2']:"";
$hotel2 = (isset($_POST['hotel2']))?$_POST['hotel2']:"";

$query = mysql_class::ex_sqlx("update `hotel_daftar` set `daftar_id`='$daftar2',`hotel_id`='$hotel2' where `id` ='$id2'");
if($query) 
 echo "1";  
else
    echo "0";


?>



