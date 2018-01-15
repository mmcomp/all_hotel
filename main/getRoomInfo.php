<?php
session_start();
include("../kernel.php");
    if(!isset($_SESSION['user_id']))
            die(lang_fa_class::access_deny);
    $se = security_class::auth((int)$_SESSION['user_id']);
    if(!$se->can_view)
            die(lang_fa_class::access_deny);
$isAdmin = $se->detailAuth('all');
$isTasisat = $se->detailAuth('tasisat');
$out='';
if (isset($_SESSION['user_id']))
	$user_id = (int)$_SESSION['user_id'];
else
	$user_id = -1;
$room_id = (isset($_POST['oid']))?$_POST['oid']:"";
if($room_id){
    $r_tmp = new room_class($room_id);
}
echo "اتاق".$r_tmp->name;
  
?>