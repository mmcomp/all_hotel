<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$sname = (isset($_POST['sname']))?$_POST['sname']:"";
$kolcode = (isset($_POST['kolcode']))?$_POST['kolcode']:"";
$kolname = (isset($_POST['kolname']))?$_POST['kolname']:"";
mysql_class::ex_sql("select `id` from `kol` where `code`='$kolcode' and `name`='$kolname' ",$k);
		if($r = mysql_fetch_array($k))
		{
			$kid = $r["id"];
		}
$moeencode = (isset($_POST['moeencode']))?$_POST['moeencode']:"";
$moeenname = (isset($_POST['moeenname']))?$_POST['moeenname']:"";
mysql_class::ex_sql("select `id` from `moeen` where `code`='$moeencode' and `name`='$moeenname' ",$m);
		if($rr = mysql_fetch_array($m))
		{
			$mid = $rr["id"];
		}
$cost1 = (isset($_POST['cost1']))?$_POST['cost1']:"";
$toz1 = (isset($_POST['toz1']))?$_POST['toz1']:"";
$bes1 = (isset($_POST['bes1']))?$_POST['bes1']:"";
$tarikh = date("Y-m-d");
$user_id = $_SESSION['user_id'];


$query = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`user_id`,`typ`,`tozihat`,`en`,`mablagh`) values ('$sname','$kid','$mid','$tarikh','$user_id','$bes1','$toz1','0','$cost1')");
if($query) 
    echo "1";  
else
    echo "0";


?>