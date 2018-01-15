<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$id3 = (isset($_POST['id3']))?$_POST['id3']:"";


$sql = "delete from `user` where `id` ='$id3'";
// echo $sql."\n";
$query = mysql_class::ex_sqlx($sql);
if($query) 
 echo "1";  
else
    echo "0";


?>