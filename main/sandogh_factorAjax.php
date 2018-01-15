<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$khadamat1 = (isset($_POST['khadamat1']))?$_POST['khadamat1']:"";
$toz1 = (isset($_POST['toz1']) && trim($_POST['toz1'])!='')?'رزرو '.$_POST['toz1']:"نقد";
$cost1 = (isset($_POST['cost1']))?$_POST['cost1']:"";
$tedad1 = (isset($_POST['tedad1']))?$_POST['tedad1']:"";
$factor = (isset($_POST['factor']))?$_POST['factor']:"";
$reserve_id = (isset($_POST['reserve_id']))?$_POST['reserve_id']:"";
$room_id = (isset($_POST['room_id']))?$_POST['room_id']:"";
$user_id = (isset($_POST['user_id']))?$_POST['user_id']:"";
$isFactor = (isset($_POST['isFactor']))?$_POST['isFactor']:"";
$tarikh = date("Y-m-d H:i:s");
if((int)$_SESSION['factor_shomare'] <= 0)
  $_SESSION['factor_shomare'] = sandogh_factor_class::getShomareFactor();
$factor = $_SESSION['factor_shomare'];
$query = "insert into `sandogh_factor` (`reserve_id`,`room_id`,`sandogh_item_id`,`toz`,`tedad`,`mablagh`,`factor_shomare`,`en`,`typ`,`user_id`,`tarikh`) values ('$reserve_id','$room_id','$khadamat1','$toz1','$tedad1','$cost1','$factor','0','$isFactor','$user_id','$tarikh')";
// echo $query."\n";
if(mysql_class::ex_sqlx($query))
    echo "1";  
else
    echo "0";


?>