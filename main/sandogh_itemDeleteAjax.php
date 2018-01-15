<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$siid3 = (isset($_POST['siid3']))?$_POST['siid3']:"";



 $query = mysql_class::ex_sqlx("delete from `sandogh_item` where `id` ='$siid3'");
if($query) 
 echo "1";  
else
    echo "0";


?>