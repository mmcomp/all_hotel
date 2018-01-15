<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);
$user_id = $_SESSION['user_id'];


$type1 = (isset($_POST['type1']))?$_POST['type1']:"";
$code1 = (isset($_POST['code1']))?$_POST['code1']:"";
$name1 = (isset($_POST['name1']))?$_POST['name1']:"";

$query = mysql_class::ex_sqlx("insert into `kol` (`name`,`code`,`typ`,`user_daftar_id`) values ('$name1','$code1','$type1','".$_SESSION['daftar_id']."')");
if($query) 
    echo "1";  
else
    echo "0";

?>