<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);


$res_id1 = (isset($_POST['res_id1']))?$_POST['res_id1']:"";
$reserve_id = hexdec($res_id1)-10000;

$room_id = (isset($_POST['rm_id1']))?$_POST['rm_id1']:"";
$kh = (isset($_POST['kh1']))?$_POST['kh1']:"";
if($kh==1){
    $user_id=(int)$_SESSION['user_id'];
    mehman_class::khorooj($reserve_id,$room_id,$user_id);
    echo "1";

}
else{
    $q = null;
    $now = date("Y-m-d 23:59:59");
    $now_delay =date("Y-m-d 00:00:00",strtotime($now.' -'.$conf->limit_paziresh_day.' day'));
    $is_available = FALSE;
    mysql_class::ex_sql("select `id` from `room_det` where `reserve_id`=$reserve_id and `aztarikh`>='$now_delay' and `aztarikh`<='$now' ",$q);
    if($r = mysql_fetch_array($q,MYSQL_ASSOC))
        $is_available = TRUE;
}



?>