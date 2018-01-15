<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$id3 = (isset($_POST['id3']))?$_POST['id3']:"";



$query = mysql_class::ex_sqlx("update `anbar` set `en`='0' where `id` ='$id3'");
if($query) 
 echo "1";  
else
    echo "0";


?>