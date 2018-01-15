<?php   
session_start();
include_once("../kernel.php");
//if(!isset($_SESSION['user_id']))
 //  die(lang_fa_class::access_deny);
$se = security_class::auth((int)$_SESSION['user_id']);
//if(!$se->can_view)
  //  die(lang_fa_class::access_deny);
$num_room = (isset($_POST['onum_room']))?$_POST['onum_room']:"";
$ma_sodor = (isset($_POST['oma_sodor']))?$_POST['oma_sodor']:"";
$tour_name = (isset($_POST['otour_name']))?$_POST['otour_name']:"";
$name = (isset($_POST['oname']))?$_POST['oname']:"";
$job = (isset($_POST['ojob']))?$_POST['ojob']:"";
$p_par = (isset($_POST['op_par']))?$_POST['op_par']:"";
$lname = (isset($_POST['olname']))?$_POST['olname']:"";
$d_safar = (isset($_POST['od_safar']))?$_POST['od_safar']:"";
$cost = (isset($_POST['ocost']))?$_POST['ocost']:"";
$h_enter = (isset($_POST['oh_enter']))?$_POST['oh_enter']:"";
$sour = (isset($_POST['osour']))?$_POST['osour']:"";
$ex_cost = (isset($_POST['oex_cost']))?$_POST['oex_cost']:"";
$name_f = (isset($_POST['oname_f']))?$_POST['oname_f']:"";
$des = (isset($_POST['odes']))?$_POST['odes']:"";
$ex_person = (isset($_POST['oex_person']))?$_POST['oex_person']:"";
$ss = (isset($_POST['oss']))?$_POST['oss']:"";
$meli = (isset($_POST['omeli']))?$_POST['omeli']:"";
$tt = (isset($_POST['ott']))?$_POST['ott']:"";
$rel = (isset($_POST['orel']))?$_POST['orel']:"";
$gender = (isset($_POST['ogender']))?$_POST['ogender']:"";
$t_ezde = (isset($_POST['ot_ezde']))?$_POST['ot_ezde']:"";
$nation = (isset($_POST['onation']))?$_POST['onation']:"";
$mob = (isset($_POST['omob']))?$_POST['omob']:"";
$r_id = (isset($_POST['or_id']))?$_POST['or_id']:"";
$res_id = (isset($_POST['ores_id']))?$_POST['ores_id']:"";
$toz = (isset($_POST['otoz']))?$_POST['otoz']:"";
$mobile = (isset($_POST['omob'])?$_POST['omob']:'');
// die('salam');
/*
mysql_class::ex_sqlx("insert into `mehman` (`room_id`,`reserve_id`,`fname`,`lname`,`vorood_h`,`p_name`,`ss`,`tt`,`gender`,`melliat`,
				`ms`,`job`,`safar_dalil`,`mabda`,`maghsad`,`code_melli`,`nesbat`,`t_ezdevaj`,
				`hamrah`,`toor_name`,`pish_pardakht`,`toz`,`hazine`,`hazine_extra`,`tedad_extra`,`khorooj`) 
				values ('$r_id','$res_id','$name','$lname','$h_enter','$name_f','$ss','$tt',
				'$gender','$nation','$ma_sodor','$job','$d_safar','$sour','$des','$meli','$rel','$t_ezde','$ex_person',
				'$tour_name','$p_par','$toz','$cost','$ex_cost','$ex_person', '0000-00-00 00:00:00')");
*/
//OR
$query = new mysql_class;
$query1 = "insert into `mehman` (`room_id`,`reserve_id`,`fname`,`lname`,`vorood_h`,`p_name`,`ss`,`tt`,`gender`,`melliat`,
				`ms`,`job`,`safar_dalil`,`mabda`,`maghsad`,`code_melli`,`nesbat`,`t_ezdevaj`,
				`hamrah`,`toor_name`,`pish_pardakht`,`toz`,`hazine`,`hazine_extra`,`tedad_extra`,`khorooj`,`mobile`) 
				values ('$r_id','$res_id','$name','$lname','$h_enter','$name_f','$ss','$tt',
				'$gender','$nation','$ma_sodor','$job','$d_safar','$sour','$des','$meli','$rel','$t_ezde','$ex_person',
				'$tour_name','$p_par','$toz','$cost','$ex_cost','$ex_person', '0000-00-00 00:00:00','$mobile')";
// echo $query;
$query->ex_sqlx($query1);
$query->ex_sql("select `id`,`room_id`,`tatarikh` from `room_det` where `reserve_id`='$res_id' and `room_id`='$r_id'",$qur);
if($row2 = mysql_fetch_array($qur))
{
    $room=$row2['room_id'];	 	
    mysql_class::ex_sqlx("update `room` set `vaziat`=0 where `id` ='$room'");
			
}
if($row2)
    echo "1";
else
    echo "0";


?>