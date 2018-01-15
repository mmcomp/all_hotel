<?php
session_start();
include("../kernel.php");
$reserve_id = (int)$_REQUEST['rid'];
$ghimat = umonize($_REQUEST['mablagh']);
$toz = $_REQUEST['tozih'];
// echo "res = $reserve_id\n";
$refunded = room_det_class::refundReserve($reserve_id,$toz);
// var_dump($refunded);
// die();
$reserve_id = abs($reserve_id) * (-1);
for($i=0;$i<count($refunded);$i++)
{
  mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$refunded[$i]."') ");
}
if(count($refunded)>0){
  echo "1";
}else{
  echo "0";
}