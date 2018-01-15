<?php
session_start();
include_once("../kernel.php");
function pdate1($inp)
{
	return(audit_class::hamed_pdate($inp));
}
function bedBes($inp)
{
	return(((int)$inp == -1)?'بدهکار':'بستانکار');
}
$GLOBALS['msg'] = '';
$out = '';
$req = ((isset($_REQUEST['req']))?$_REQUEST['req']:'');
$room_id = ((isset($_REQUEST['room_id']))?$_REQUEST['room_id']:'');
if($room_id==''){
	$req_tmp = explode('.',$req);
	if(count($req_tmp)==2){
		$room_id = (int)$req_tmp[1];
	}
}
$daftar = new daftar_class($_SESSION['daftar_id']);
$bestakar_moeen = $daftar->sandogh_moeen_id;
// var_dump($daftar);
// die();
$bestakari = 0;
$jam_koll = 0;
if($req != '')
{
	$reserve_id = (int)$req;
// 	$query = "select `id` from `moeen` where `name`='دریافت نقدی و کارت خوان'";
// 	mysql_class::ex_sql($query,$q);
// 	if($r = mysql_fetch_array($q)){
// 		$bestakar_moeen = $r['id'];
	if($bestakar_moeen>0){
	// 	echo $bestakar_moeen;
	// 	die();
		$query = "select SUM(`mablagh`) as `jam` from `sanad` where `tozihat` like '%رزرو $reserve_id%' and `moeen_id` = $bestakar_moeen and `typ` = -1";
// 		echo $query;
// 		die();
		mysql_class::ex_sql($query,$q);
		if($r = mysql_fetch_array($q)){
			$bestakari = (int)$r['jam'];
		}
	}
	$reserve = new reserve_class($reserve_id);
// 		var_dump($reserve);
	$hotel = new hotel_class($reserve->hotel_id);
	if($reserve->id>0)
	{
		$GLOBALS['msg'] = '<h2>آقا/خانم '.$reserve->hotel_reserve->fname.' '.$reserve->hotel_reserve->lname.' اطلاعات مالی یافت نشد.</h2>';
		$room_shart = '';
		$az = '';
		$ta = '';
		$query = "SELECT date(min(aztarikh)) az,date(max(tatarikh)) ta FROM `room_det` WHERE `reserve_id`=$reserve_id";
		mysql_class::ex_sql($query,$q);
		if($r = mysql_fetch_array($q)){
			$az = jdate('Y/m/d',strtotime($r['az']));
			$ta = jdate('Y/m/d',strtotime($r['ta']));
		}
		$addr = '';
		$tell = '';
		$query = "SELECT address,tel FROM `emkanat_hotel_extra` WHERE `hotel_id`=".$reserve->hotel_id;
		mysql_class::ex_sql($query,$q);
		if($r = mysql_fetch_array($q)){
			$addr = $r['address'];
			$tell = $r['tel'];
		}
		//for($i = 0;$i < count($reserve->room_det);$i++)
		//{
			//$room_tmp = new room_class($reserve->room_det[$i]->room_id);
// 			$room_tmp = new room_class($room_id);
// 			$moeen_id = $room_tmp->moeen_id;
// 			if($moeen_id>0)
// 			{
// 				$aztarikh = date("Y-m-d",strtotime($reserve->room_det[0]->aztarikh));
// 				$tatarikh = date("Y-m-d",strtotime($reserve->room_det[0]->tatarikh));
// 				$room_shart .="( `moeen_id` = $moeen_id and DATE(`tarikh`)>='$aztarikh' and DATE(`tarikh`) <= '$tatarikh' )";
// 			}
		//}
// 			if($room_shart == '')
// 				$room_shart = '1=0';
// 			$q = null;
// 			$query = "select SUM(`mablagh`*`typ`) as `jam` from `sanad` where $room_shart";
		$query = "select SUM(`mablagh`) as `jam` from `sanad` where `tozihat` like '%رزرو $reserve_id%' /*and `tozihat` like '%{$reserve->hotel_reserve->lname}%'*/ and `typ`=-1 and moeen_id!=$bestakar_moeen";
// 		echo $query."\n";
		$jam_stat = 'تسویه';
		mysql_class::ex_sql($query,$q);
		if($r = mysql_fetch_array($q))
			$jam_koll = (int)$r['jam']-$bestakari;
		if($jam_koll>0){
			$jam_stat = -1;
		}else if($jam_koll<0){
			$jam_stat = 1;
		}
//       echo "JAM = $jam_kol<br/>";     


					$out.='<div style="direction:rtl;padding:5px;border: solid 3px #000;">
									<div class="center" style="font-size: 20px;font-weight: bold;margin: 5px;">
									فاکتور چک اوت
									</div>
									<div>
										<table width="100%">
											<tr>
												<td style="text-align:right">
												هتل : '.$hotel->name.'
												</td>
												<td style="text-align:center">
												شماره رزرو : '.$reserve_id.'
												</td>
												<td style="text-align:left">
												تاریخ صدور : '.jdate('Y/m/d').'
												</td>
											</tr>
											<tr>
												<td>
												نام مسافر : '.$reserve->hotel_reserve->fname.' '.$reserve->hotel_reserve->lname.'
												</td>
												<td style="text-align:center">
												تاریخ ورود : '.$az.'
												</td>
												<td style="text-align:left">
												تاریخ خروج : '.$ta.'
												</td>
											</tr>
										</table>
									</div>
									<div>
									<table class="checkout" cellspacing="0">
										<thead>
											<tr>
											<th style="text-align:center">ردیف</th>
											<th style="text-align:center">تاریخ</th>
											<th style="text-align:right">توضیحات</th>
											<th style="text-align:center">مبلغ</th>
											</tr>
										</thead>
										<tbody>';
		$query = "select shomare_sanad,sanad.typ,tozihat,tarikh,name,mablagh,moeen.name mn from `sanad` left join moeen on (moeen.id=moeen_id) where `tozihat` like '%رزرو $reserve_id%' /*and `tozihat` like '%".$reserve->hotel_reserve->lname."%'*/ and `sanad`.`typ`=-1 and sanad.moeen_id!=$bestakar_moeen";
// 						echo $query."\n";
		mysql_class::ex_sql($query,$ss);
		$i=1;
		while($r = mysql_fetch_array($ss)){
			$tarikh = jdate("Y/m/d",strtotime($r['tarikh']));
			$tozihat = $r['tozihat'];
			$mablagh = $r['mablagh'];//number_format($r['mablagh']);
			$jam_kol = '-';
			if(fmod($i,2)!=0){
				$out.='<tr class="odd">
						<td class="center">'.$i.'</td>
						<td>'.$tarikh.'</td>
						<td>'.$tozihat.'</td>
						<td>('.monize($mablagh).')</td>
				</tr>';
				$i++;
			}
			else{
				$out.='
				<tr class="even">
						<td class="center">'.$i.'</td>
						<td>'.$tarikh.'</td>
						<td>'.$tozihat.'</td>
						<td>('.monize($mablagh).')</td>
				</tr>
				';
				$i++;
			}
		}
		$query = "select shomare_sanad,sanad.typ,tozihat,tarikh,name,mablagh,moeen.name mn from `sanad` left join moeen on (moeen.id=moeen_id) where  `sanad`.`tozihat` like '%رزرو $reserve_id%' and `sanad`.`moeen_id` = $bestakar_moeen and `sanad`.`typ` =-1";
// 		echo $query."\n";
// 		die();
		
		mysql_class::ex_sql($query,$ss);
// 		$i=1;
		while($r = mysql_fetch_array($ss)){
			$tarikh = jdate("Y/m/d",strtotime($r['tarikh']));
			$tozihat = $r['tozihat'];
			$mablagh = $r['mablagh'];//number_format($r['mablagh']);
			$jam_kol = ($r['typ']==1)?'':'-';
			if(fmod($i,2)!=0){
				$out.='<tr class="odd">
						<td class="center">'.$i.'</td>
						<td>'.$tarikh.'</td>
						<td>'.$tozihat.'</td>
						<td>'.monize($mablagh).'</td>
				</tr>';
				$i++;
			}
			else{
				$out.='
				<tr class="even">
						<td class="center">'.$i.'</td>
						<td>'.$tarikh.'</td>
						<td>'.$tozihat.'</td>
						<td>'.monize($mablagh).'</td>
				</tr>
				';
				$i++;
			}
		}
		$out.='<tr><td colspan="3" style="text-align:left">جمع</td><td style="text-align:left">';
		if($jam_stat==-1)
			$out.='(';
		$out.=monize($jam_koll);
		if($jam_stat==-1)
			$out.=')';
		$out.= '</td></tr>';
		$out.='<tr><td colspan="4" >';
		$out.='آدرس : '.$addr.'<br/>تلفن : '.$tell;
		$out.= '</td></tr>';
		$out.='</tbody></table></div></div>';
		$GLOBALS['msg'] = '<h4>آقا/خانم '.$reserve->hotel_reserve->fname.' '.$reserve->hotel_reserve->lname.' جمع حساب شما : '.monize($jam_koll).' ریال '.$jam_stat;
		$GLOBALS['msg'] .= '<button class="btn btn-info pull-left" onclick="window.open(\'report_print.php?req='.$req.'\', \'\', \'width=820,height=100\');"><i class="fa fa-print"></i></button></h4>';
	}
	else
		$GLOBALS['msg'] = 'کد مشتری صحیح نمی باشد.';
}
else
	die($conf->access_deny);
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش حساب</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
	<style>
		.checkout{
			padding: 5px;
			direction: rtl;
		}
		.checkout td,th{
			border: 1px solid #000;
			padding : 5px;
		}
		.center{
			text-align:center;
		}
	</style>
</head>
<body>
	<?php	echo $out;?>
	<script>
		window.print();
	</script>
</body> 
</html>