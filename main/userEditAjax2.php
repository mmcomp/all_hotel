<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);

$id2 = (isset($_POST['id2']))?$_POST['id2']:"";

$pass2 = (isset($_POST['pass2']))?$_POST['pass2']:"";

$query = mysql_class::ex_sqlx("update `user` set `pass`='$pass2' where `id` ='$id2'");

if($query)
    echo "1";
else
    echo "0";
?>