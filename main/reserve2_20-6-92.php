<?php
	session_start();
	include("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$user = new user_class((int)$_SESSION['user_id']);
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$out = '<select name="hotel_id" class="inp" style="width:auto;" >';
		mysql_class::ex_sql('select `id`,`name` from `hotel` order by `name` ',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadNumber($inp=-1)
	{
		$out = '';
		$inp = (int)$inp;
		for($i=1;$i<5;$i++)
		{
			$sel = (($i==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='$i' >$i</option>\n";
		}
		return $out;
	}
	function loadMoeenByAjans_id($ajans_id)
	{
		$ajans_id = (int) $ajans_id;
		$moeen  = new ajans_class($ajans_id);
		return $moeen->moeen_id;
	}
	$msg = '';
	$room_ids = array();
	$hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$shab = ((isset($_REQUEST['shab']))?(int)$_REQUEST['shab']:-1);
	$shab_reserve = ((isset($_REQUEST['shabreserve_gh']))?TRUE:FALSE);
	if($shab == 0)
		$shab_reserve = TRUE;
	$shab_reserve_gh = ((isset($_REQUEST['shabreserve_gh']))?(int)$_REQUEST['shabreserve_gh']:0);
	$rooz_reserve = ((isset($_REQUEST['roozreserve_gh']))?TRUE:FALSE);
	$rooz_reserve_gh = ((isset($_REQUEST['roozreserve_gh']))?(int)$_REQUEST['roozreserve_gh']:0);
	$room_typ_id = ((isset($_REQUEST['room_typ_id']))?(int)$_REQUEST['room_typ_id']:-1);
	$tedad_otagh = ((isset($_REQUEST['tedad_otagh']))?(int)$_REQUEST['tedad_otagh']:0);
	foreach($_REQUEST as $key=>$value)
	{
		$tmp = explode('_',$key);
		if($tmp[0]=='otagh')
			$room_ids[] = (int)$tmp[1];

	}
	if(count($room_ids)==0)
		$room_ids = explode(',',$_REQUEST['room_id_tmp']);
	if($tedad_otagh==0)
		$tedad_otagh = count($room_ids);
	$tedad_nafarat = ((isset($_REQUEST['tedad_nafarat']))?(int)$_REQUEST['tedad_nafarat']:0);
	$ajans_id = ((isset($_REQUEST['ajans_id']))?(int)$_REQUEST['ajans_id']:-1);
	if($ajans_id<=0)
	{
		die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><center>اطلاعات درست وارد نشده است مجدد اقدام به رزرو کنید<br/><a href="reserve1.php?h_id=4&mode1=1&" >بازگشت</a></center></html>');
	}	
	$daftar_id = ((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
	$daftar_idBelit_1 = ((isset($_REQUEST['daftar_idBelit_1']))?(int)$_REQUEST['daftar_idBelit_1']:-1);
	$ajans_idBelit_1 = ((isset($_REQUEST['ajans_idBelit_1']))?(int)$_REQUEST['ajans_idBelit_1']:-1);
	$daftar_idBelit_2 = ((isset($_REQUEST['daftar_idBelit_2']))?(int)$_REQUEST['daftar_idBelit_2']:-1);
	$ajans_idBelit_2 = ((isset($_REQUEST['ajans_idBelit_2']))?(int)$_REQUEST['ajans_idBelit_2']:-1);
	$daftar_idBelit_3 = ((isset($_REQUEST['daftar_idBelit_3']))?(int)$_REQUEST['daftar_idBelit_3']:-1);
	$ajans_idBelit_3 = ((isset($_REQUEST['ajans_idBelit_3']))?(int)$_REQUEST['ajans_idBelit_3']:-1);
	$sargrooh = ((isset($_REQUEST['sargrooh']))?$_REQUEST['sargrooh']:'');
	$tour_mablagh = ((isset($_REQUEST['tour_mablagh']))?(int)umonize($_REQUEST['tour_mablagh']):-1);
	$belit_mablagh_1 = ((isset($_REQUEST['belit_mablagh_1']))?(int)umonize($_REQUEST['belit_mablagh_1']):0);
	$belit_mablagh_2 = ((isset($_REQUEST['belit_mablagh_2']))?(int)umonize($_REQUEST['belit_mablagh_2']):0);
	$belit_mablagh_3 = ((isset($_REQUEST['belit_mablagh_3']))?(int)umonize($_REQUEST['belit_mablagh_3']):0);
	$m_hotel = $tour_mablagh - $belit_mablagh_1- $belit_mablagh_2 - $belit_mablagh_3;
	$mob = ((isset($_REQUEST['toz']))?$_REQUEST['toz']:'');
	$extra_toz = ((isset($_REQUEST['extra_toz']))?$_REQUEST['extra_toz']:'');
	$checkbox= array();
	$textbox = array();
	$ghimat = array();
	$room_gh = array();
	foreach($_REQUEST as $key => $value)
	{
		$tmp = explode('_',$key);
		if($tmp[0]=='kh' && $tmp[1] =='ch')
			$checkbox[(int)$tmp[2]] = $value;
		else if ($tmp[0]=='kh' && $tmp[1] =='txt')
			$textbox[(int)$tmp[2]] =$value; 
		else if ($tmp[0]=='kh' && $tmp[1] =='gh')
			$ghimat[(int)$tmp[2]] = $value;
		else if ($tmp[0]=='kh' && $tmp[1] =='v')
			$voroodi[(int)$tmp[2]] = TRUE;
		else if ($tmp[0]=='kh' && $tmp[1] =='kh')
			$khorooji[(int)$tmp[2]] = TRUE;
		if($tmp[0]=='room' && $tmp[1] =='gh')
			$room_gh[(int)$tmp[2]] = $value;
	}
	$output = '';
	//echo "$hotel_id>0 && ($room_typ_id>0 || ".var_export($conf->room_select,TRUE)." ) && $tedad_otagh>0 && $tedad_nafarat>0";
	if($hotel_id>0 && ($room_typ_id>0 || $conf->room_select ) && $tedad_otagh>0 && $tedad_nafarat>0)
	{
		$hotel = new hotel_class($hotel_id);
		if(!$conf->room_select)
			$room_typ = new room_typ_class($room_typ_id);
		else
		{
			$otagh_no = room_class::loadTypDetails($room_ids);
		}
		$output='<form method="POST" id="frm123" ><table border="1" style="color:#000;width:90%;" >';
		$output.= '<tr><th>هتل</th><th>نوع اتاق</th><th>تعداد اتاق</th><th>تعداد نفرات</th></tr>';
		$output.="<tr><td>".$hotel->name."</td>";
		if(!$conf->room_select)
			$output.="<td>".$room_typ->name."</td>";
		else
			$output.="<td>$otagh_no</td>";
                $output.="<td> $tedad_otagh</td>";
 		$output.="<td> $tedad_nafarat</td>";
		$room_ghimat = 0;
		$hotel_ghimat = $tour_mablagh - $belit_mablagh_1 - $belit_mablagh_2 - $belit_mablagh_3;
		$output.='</tr></table><br/>';
		$output.='<table border="1" style="color:#000;width:80%;" ><tr><th>خدمات</th><th>تعداد-روزانه</th></tr>';
	/*	if (isset($_REQUEST['ta_gasht']))
			$ta_gasht = $_REQUEST['ta_gasht'];
		else
			$ta_gasht ="";
		if (isset($_REQUEST['ta_axe']))
			$ta_axe = $_REQUEST['ta_axe'];
		else
			$ta_axe ="";*/
		$jam_ghi_khadamat = 0;
		foreach($ghimat as $id => $ghi)
		{
			if(isset($checkbox[$id]) || (isset($textbox[$id]) && (int)$textbox[$id] > 0))
			{
				$khedmat = new khadamat_class((int)$id);
				$output .= '<tr>';
				/*if ($khedmat->gashtAst)
					$output .= '<td>'.$khedmat->name.' ('.$ta_gasht.')</td>';
				elseif ($khedmat->axeAst)
					$output .= '<td>'.$khedmat->name.' ('.$ta_axe.')</td>';
				else*/
					$output .= '<td>'.$khedmat->name.'</td>';
				$is_voroodi = '';
				$is_khorooji = '';
				if(isset($voroodi[$id]))
					$is_voroodi = ' , اول-دارد ';
				if(isset($khorooji[$id]))
					$is_khorooji = ' , آخر-دارد';
				if(isset($checkbox[$id]))
				{
					$output .= "<td>دارد $is_voroodi $is_khorooji</td>";
					//$output .= '<td>'.monize($ghi).'</td>';
					$jam_ghi_khadamat +=$ghi;
				}
				else if(isset($textbox[$id]))
				{
					$output .= '<td>'.$textbox[$id]."$is_voroodi $is_khorooji </td>";
					//$output .= '<td>'.monize($ghi*$textbox[$id]).'</td>'; 
					$jam_ghi_khadamat += $ghi*$textbox[$id];
				}
				$output .= '</tr>';
			}
		}
		if($shab_reserve)
		{
			$output.= "<tr><td colspan=3'>شب-رزرو دارد:".monize($shab_reserve_gh)."</td></tr>";
		}
		if($rooz_reserve)
		{
			$output.= "<tr><td colspan=3'>روز-رزرو دارد:".monize($rooz_reserve_gh)."</td></tr>";
		}
		$output.="<tr>\n<td colspan='1'>\nنام سرگروه:\n<input id='sargrooh' name='sargrooh' type='text' value='$sargrooh' class='inp' style='width:70%;' >\n</td>\n<td>\nتلفن:\n<input id='toz' name='toz' type='text' value='$mob' class='inp' style='width:70%;' >\n</td>\n</tr>\n";
		$output .="<tr><td style='text-align:right;' colspan='2'>توضیحات اضافی:<input class='inp' name='extra_toz' id='extra_toz' style='width:80%;' value='$extra_toz' ></td></tr>\n";
		$output.='<tr><td>جمع کل:'.monize($tour_mablagh).'</td><td colspan="2"><input type="button" onclick="sendfrm(this);" class="inp" id="sabt_nahaee" value="ثبت نهایی" > <input type="hidden" name="mod1" value="5" ></td></tr></table></form>';
	}
	$tmp1 = array();
	if(isset($_REQUEST['mod1']) && $_REQUEST['mod1']==5 )
	{
		//$hotel_ghimat+= $shab_reserve_gh + $rooz_reserve_gh;
		if(($belit_mablagh_1+$belit_mablagh_2+$belit_mablagh_3)==0)
			$hotel_ghimat= $tour_mablagh ;
		else
		{
			$hotel_ghimat = array();
			$hotel_ghimat['ghimat_tour'] = $tour_mablagh;
			$hotel_ghimat['ghimat_belit1'] = $belit_mablagh_1;
			if($belit_mablagh_1>0)
			{
				$hotel_ghimat['other_moeen_id1'] = loadMoeenByAjans_id($ajans_idBelit_1);
				$daftar_class_1 = new daftar_class($daftar_idBelit_1);
				$hotel_ghimat['other_kol_id1'] = $daftar_class_1->kol_id;
			}
			else
			{
				$hotel_ghimat['other_moeen_id1'] = -1;
				$hotel_ghimat['other_kol_id1'] = -1;
			}
			$hotel_ghimat['ghimat_belit2'] = $belit_mablagh_2;
			if( $belit_mablagh_2>0)
			{
				$hotel_ghimat['other_moeen_id2'] = loadMoeenByAjans_id($ajans_idBelit_2);
				$daftar_class_2 = new daftar_class($daftar_idBelit_2);
				$hotel_ghimat['other_kol_id2'] = $daftar_class_2->kol_id;
			}
			else
			{
				$hotel_ghimat['other_moeen_id2'] = -1;
				$hotel_ghimat['other_kol_id2'] = -1;
			}
			$hotel_ghimat['ghimat_belit3'] = $belit_mablagh_3;
			if( $belit_mablagh_3>0)
			{
				$hotel_ghimat['other_moeen_id3'] = loadMoeenByAjans_id($ajans_idBelit_3);
				$daftar_class_3 = new daftar_class($daftar_idBelit_3);
				$hotel_ghimat['other_kol_id3'] = $daftar_class_3->kol_id;
			}
			else
			{
				$hotel_ghimat['other_moeen_id3'] = -1;
				$hotel_ghimat['other_kol_id3'] = -1;
			}
		}
		$khadamat_arr=null;
		foreach($ghimat as $id => $ghi)
		{
			if(isset($checkbox[$id]) || isset($textbox[$id]))
			{
				$khedmat = new khadamat_class((int)$id);
				$tmp_voroodi = ((isset($voroodi[$id]))?TRUE:FALSE);
				$tmp_khorooji = ((isset($khorooji[$id]))?TRUE:FALSE);
				$khadamat_arr[] =array('id'=>$id,'tedad'=>((isset($textbox[$id]))?$textbox[$id]:1),'ghimat'=>$ghi ,'voroodi'=>$tmp_voroodi ,'khorooji'=>$tmp_khorooji ) ;
			}
		}
		$tmp_room =$room_typ_id;
		if(count($room_ids)>0)
			$tmp_room = $room_ids;
		$rooms_arr = room_class::loadOpenRooms($aztarikh,$shab,$shab_reserve,$rooz_reserve,$hotel_id);
		$tmp1=FALSE;
		foreach($rooms_arr as $rrr)
			for($i=0;$i<count($tmp_room);$i++)
				if(in_array($tmp_room[$i],$rrr['room_ids']))
					$tmp1=TRUE;
		//---------------------برای خالی نزدن سند حسابها چک می شود--------------------
		$check_reserve = sanadzan_class::checkHesab($hotel_ghimat);
		$is_reserve = $check_reserve['is_reserve'];
		$msg .=$check_reserve['msg'];

		//-------------------پایان چک کردن حسابها ------------------------------------
		if($is_reserve)
		{
			/*if (isset($_REQUEST['ta_gasht']))
				$ta_gasht = $_REQUEST['ta_gasht'];
			else
				$ta_gasht ="";
			if (isset($_REQUEST['ta_axe']))
				$ta_axe = $_REQUEST['ta_axe'];
			else
				$ta_axe ="";*/

			if($tmp1)
				//$tmp1 = room_det_class::preReserve($hotel_id,$ajans_id,$tmp_room,$hotel_ghimat,$aztarikh,$shab,$tedad_otagh,$shab_reserve,$rooz_reserve,$tedad_nafarat,$khadamat_arr,$ta_gasht,$ta_axe);
				$tmp1 = room_det_class::preReserve($hotel_id,$ajans_id,$tmp_room,$hotel_ghimat,$aztarikh,$shab,$tedad_otagh,$shab_reserve,$rooz_reserve,$tedad_nafarat,$khadamat_arr);
			else
				$msg = 'لحظاتی قبل اتاق مورد نظر شما توسط شخص دیگری رزرو شد';
			if($tmp1!==FALSE)
			{
				$extra_toz = ((isset($_REQUEST['extra_toz']))?$_REQUEST['extra_toz']:'');
				$today = date("Y-m-d H:i:s");
				$toz = $mob;
				if($extra_toz!='')
				{
					$toz = null;
					$toz['toz'] = $mob;
					$toz['extra_toz'] = $extra_toz;
				}
				room_det_class::sabtReserveHotel($tmp1['reserve_id'],$tmp1['shomare_sanad'],$hotel_ghimat,'',$sargrooh,$toz,$ajans_id,$m_hotel,$today);
				sms_class::reserve_text_sms($tmp1['reserve_id'],$mob,$tour_mablagh);
				for($l=0;$l<count($tmp1['shomare_sanad']);$l++)
				{
					$tozih_sabti = room_det_class::loadReserve($tmp1['reserve_id']);
					mysql_class::ex_sqlx("update `sanad` set `tozihat`='$tozih_sabti' where `id`=".$tmp1['shomare_sanad'][$l]);
				}
				$msg = 'شماره رزرو شما '.$tmp1['reserve_id'].'<br>';
				//$msg .= 'ثبت توسط '.$user->fname.' '.$user->lname.' انجام شد<script>document.getElementById("sabt_nahaee").style.display="none";window.print();</script>';
				$msg .= 'ثبت توسط '.$user->fname.' '.$user->lname.' انجام شد<script>document.getElementById("sabt_nahaee").style.display="none";window.location="reserve3.php?reserve_id='.$tmp1['reserve_id'].'&r="+Math.random()+"&";</script>';

			}
			else
				$msg = 'رزرو با مشکل مواجه شد دوباره تلاش فرمایید';
		}
		else 
			$msg.='<br/><span style="color:red;" >اطلاعات وارد شده جهت ثبت کافی نبوده است ،لذا ثبت انجام نشد ،مجدد اقدام کنید</span>';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript">
			function sendfrm(Obj)
			{
				Obj.disabled = true;
				var sargrooh = document.getElementById('sargrooh');
				var toz = document.getElementById('toz');
				if(sargrooh.value !='' && toz.value !=''  )
					document.getElementById('frm123').submit();
				else
					alert('نام سرگروه و تلفن را وارد کنید');
			}
		</script>


	
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript">
	    $(function() {
	        //-----------------------------------
	        // انتخاب با کلیک بر روی عکس
	        $("#datepicker6").datepicker({
	            showOn: 'button',
		    dateFormat: 'yy/mm/dd',
	            buttonImage: '../js/styles/images/calendar.png',
	            buttonImageOnly: true
	        });
	    });
    </script>
		<style>
		td { text-align: center; }
		</style>
		<title>
			سامانه رزرواسیون	
		</title>
	</head>
	<body >
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<center>
		<div align="center" style="width:21cm;height:29cm;">
			<img src="../img/banner.jpg" ><br/>
			<br/>
				<?php
					$ajans = new ajans_class($ajans_id);
					$daftar = new daftar_class($daftar_id);
				?>
				<h3>دفتر <?php echo $daftar->name; ?> آژانس <?php echo $ajans->name; ?> از تاریخ <?php echo jdate("d / m / Y",strtotime($aztarikh)); ?> به مدت <?php echo enToPerNums($shab); ?> شب</h3>
			<br/>
			<?php echo $output.' '.$msg; ?>
		</div>
		</center>
	</body>
</html>
