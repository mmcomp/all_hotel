<?php   
// var_dump($_POST);
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);
$mid1 = (isset($_POST['omid1']))?$_POST['omid1']:"";
$room_id1 = (isset($_POST['oroom_id1']))?$_POST['oroom_id1']:"";
$fname1 = (isset($_POST['ofname1']))?$_POST['ofname1']:"";
$lname1 = (isset($_POST['olname1']))?$_POST['olname1']:"";
$pname1 = (isset($_POST['opname1']))?$_POST['opname1']:"";
$ss1 = (isset($_POST['oss1']))?$_POST['oss1']:"";
$tt1 = (isset($_POST['ott1']))?$_POST['ott1']:"";
$gender1 = (isset($_POST['ogender1']))?$_POST['ogender1']:"";
$code_melli = (isset($_POST['code_melli']))?$_POST['code_melli']:"";
$mobile = (isset($_POST['mobile']))?$_POST['mobile']:"";
$mabda = (isset($_POST['mabda']))?$_POST['mabda']:"";
$ms = (isset($_POST['ms']))?$_POST['ms']:"";
$meliat = (isset($_POST['meliat']))?$_POST['meliat']:"";
$nesbat = (isset($_POST['nesbat']))?$_POST['nesbat']:"";
$sql = "update `mehman` set `gender`='$gender1',`fname`='$fname1',`lname`='$lname1',`p_name`='$pname1',`ss`='$ss1',`tt`='$tt1',`room_id`='$room_id1',`code_melli`='$code_melli',`mobile`='$mobile',`mabda`='$mabda',`ms`='$ms',`melliat`='$meliat',`nesbat`='$nesbat' where `id` ='$mid1'";
// echo $sql."\n";
$query = mysql_class::ex_sqlx($sql,$query);
if($query){ 
 echo "1";
}else
 echo "0";


?>