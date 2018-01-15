<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$sssid2 = (isset($_POST['sssid2']))?$_POST['sssid2']:"";
$Use = (isset($_POST['Use']))?$_POST['Use']:"";
$Sand = (isset($_POST['Sand']))?$_POST['Sand']:"";



$query = mysql_class::ex_sqlx("update `sandogh_user` set `user_id`='$Use',`sandogh_id`='$Sand' where `id` ='$sssid2'");
if($query)
 echo "1";  
else
    echo "0";


?>