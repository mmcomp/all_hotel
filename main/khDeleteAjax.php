<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$khid3 = (isset($_POST['khid3']))?$_POST['khid3']:"";



 $query = mysql_class::ex_sqlx("delete from `khadamat` where `id` ='$khid3'");
if($query) 
 echo "1";  
else
    echo "0";


?>