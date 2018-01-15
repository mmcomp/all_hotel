<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);
$user_id = $_SESSION['user_id'];

$kol1 = (isset($_POST['kol1']))?$_POST['kol1']:"";
$name1 = (isset($_POST['name1']))?$_POST['name1']:"";
$type1 = (isset($_POST['type1']))?$_POST['type1']:"";
//moeen_class::newByCode_habibi($kol1,$name1);
mysql_class::ex_sql("select max(code) as maxi from `moeen`",$q_kol);
$r_kol=mysql_fetch_array($q_kol);
$maxi = (int)$r_kol['maxi'];
$maxi = $maxi+1;
$query = mysql_class::ex_sqlx("insert into `moeen` (`kol_id`,`name`,`typ`,`code`) values ('$kol1','$name1','$type1','$maxi')");
if($query) 
    echo "1";  
else
    echo "0";

?>