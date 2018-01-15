<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);
$user_id = $_SESSION['user_id'];

$daftar1 = (isset($_POST['daftar1']))?$_POST['daftar1']:"";
$toz1 = (isset($_POST['toz1']))?$_POST['toz1']:"";
$ajans1 = (isset($_POST['ajans1']))?$_POST['ajans1']:"";
$send1 = (isset($_POST['send1']))?$_POST['send1']:"";
$tell1 = (isset($_POST['tell1']))?$_POST['tell1']:"";
$protected1 = (isset($_POST['protected1']))?$_POST['protected1']:"";
$moeen_id = -1;
$daftar = new daftar_class((int)$daftar1);
$kol_tmp = new kol_class($daftar->kol_id);
if($kol_tmp->id>0)
{
  $moeen_id = moeen_class::addById($daftar->kol_id,$ajans1);
}
$sql = "insert into `ajans` (`name`,`daftar_id`,`tozihat`,`ersal_moshtari`,`tell`,`protected`,`moeen_id`) values ('$ajans1','$daftar1','$toz1','$send1','$tell1','$protected1','$moeen_id')";
// echo $sql;
// exit();
$query = mysql_class::ex_sqlx($sql);
if($query) 
    echo "1";  
else
    echo "0";

?>