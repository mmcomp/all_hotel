<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);





$id2 = (isset($_POST['id2']))?$_POST['id2']:"";
$khid2 = (isset($_POST['khid2']))?$_POST['khid2']:"";


$query = mysql_class::ex_sqlx("update `sandogh_khadamat` set `khadamat_id`='$khid2' where `id` ='$id2'");
if($query)
 echo "1";  
else
    echo "0";


?>