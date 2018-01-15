<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);





$hotelid = (isset($_POST['hotelid2']))?$_POST['hotelid2']:"";
$hotelname = (isset($_POST['hotelname2']))?$_POST['hotelname2']:"";
$hoteltype = (isset($_POST['hoteltype2']))?$_POST['hoteltype2']:"";
$is_shab_nafar = (isset($_POST['is_shab_nafar']))?$_POST['is_shab_nafar']:"";
// var_dump($_REQUEST);
$sql = "update `hotel` set `name`='$hotelname',`is_our`='$hoteltype',`is_shab_nafar`=$is_shab_nafar where `id` ='$hotelid'";
// echo $sql;
$query = mysql_class::ex_sqlx($sql);
if($query) 
 echo "1";  
else
    echo "0";


?>