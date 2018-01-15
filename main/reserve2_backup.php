<?php
	session_start();
	include("../kernel.php");
        if (isset($_SESSION['user_id']) && isset($_SESSION['typ']))
        {
               //
        }
        else
        {
                        die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
        }
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
	$hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$shab = ((isset($_REQUEST['shab']))?(int)$_REQUEST['shab']:-1);
	$shab_reserve = ((isset($_REQUEST['shabreserve_gh']))?TRUE:FALSE);
	$shab_reserve_gh = ((isset($_REQUEST['shabreserve_gh']))?(int)$_REQUEST['shabreserve_gh']:0);
	$rooz_reserve = ((isset($_REQUEST['roozreserve_gh']))?TRUE:FALSE);
	$rooz_reserve_gh = ((isset($_REQUEST['roozreserve_gh']))?(int)$_REQUEST['roozreserve_gh']:0);
	$room_typ_id = ((isset($_REQUEST['room_typ_id']))?(int)$_REQUEST['room_typ_id']:-1);
	$tedad_otagh = ((isset($_REQUEST['tedad_otagh']))?(int)$_REQUEST['tedad_otagh']:0);
	$tedad_nafarat = ((isset($_REQUEST['tedad_nafarat']))?(int)$_REQUEST['tedad_nafarat']:0);
	$ajans_id = ((isset($_REQUEST['ajans_id']))?(int)$_REQUEST['ajans_id']:-1);
	$daftar_id = ((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
	$daftar_idBelit = ((isset($_REQUEST['daftar_idBelit']))?(int)$_REQUEST['daftar_idBelit']:-1);
	$ajans_idBelit = ((isset($_REQUEST['ajans_idBelit']))?(int)$_REQUEST['ajans_idBelit']:-1);
	$sargrooh = ((isset($_REQUEST['sargrooh']))?$_REQUEST['sargrooh']:'');
	$tour_mablagh = ((isset($_REQUEST['tour_mablagh']))?(int)$_REQUEST['tour_mablagh']:-1);
	$belit_mablagh = ((isset($_REQUEST['belit_mablagh']))?(int)$_REQUEST['belit_mablagh']:-1);
	$toz = ((isset($_REQUEST['toz']))?$_REQUEST['toz']:'');
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
		if($tmp[0]=='room' && $tmp[1] =='gh')
			$room_gh[(int)$tmp[2]] = $value;
	}
	$output = '';
	if($hotel_id>0 && $room_typ_id>0 && $tedad_otagh>0 && $tedad_nafarat>0)
	{
		$hotel = new hotel_class($hotel_id);
		$room_typ = new room_typ_class($room_typ_id);
		$output='<form method="POST" id="frm123" ><table border="1" style="color:#000;width:90%;" >';
		$output.= '<tr><th>هتل</th><th>نوع اتاق</th><th>تعداد اتاق</th><th>تعداد نفرات</th><th>جمع قیمت</th></tr>';
		$output.="<tr><td>".$hotel->name."</td>";
		$output.="<td>".$room_typ->name."</td>";
                $output.="<td> $tedad_otagh</td>";
 		$output.="<td> $tedad_nafarat</td>";
		$room_ghimat =$room_gh[$room_typ_id]; 
		$output.="<td>".monize($room_ghimat * $tedad_otagh)."</td>";
		//$hotel_ghimat = $room_ghimat * $tedad_otagh;
		$hotel_ghimat = $tour_mablagh - $belit_mablagh;
		$output.='</tr></table><br/>';
		$output.='<table border="1" style="color:#000;width:80%;" ><tr><th>خدمات</th><th>تعداد</th><th>جمع قیمت(ریال)</th></tr>';
		$jam_ghi_khadamat = 0;
		foreach($ghimat as $id => $ghi)
		{
			if(isset($checkbox[$id]) || (isset($textbox[$id]) && $textbox[$id] > 0))
			{
				$khedmat = new khadamat_class((int)$id);
				$output .= '<tr>';
				$output .= '<td>'.$khedmat->name.'</td>';
				if(isset($checkbox[$id]))
				{
					$output .= '<td>1</td>';
					$output .= '<td>'.monize($ghi).'</td>';
					$jam_ghi_khadamat +=$ghi;
				}
				else if(isset($textbox[$id]))
				{
					$output .= '<td>'.$textbox[$id].'</td>';
					$output .= '<td>'.monize($ghi*$textbox[$id]).'</td>'; 
					$jam_ghi_khadamat += $ghi*$textbox[$id];
				}
				$output .= '</tr>';
			}
		}
		if($shab_reserve)
		{
			$output.= "<tr><td colspan=4'>شب-رزرو دارد:".monize($shab_reserve_gh)."</td></tr>";
		}
		if($rooz_reserve)
		{
			$output.= "<tr><td colspan=4'>روز-رزرو دارد:".monize($rooz_reserve_gh)."</td></tr>";
		}
		$output.="<tr>\n<td colspan='2'>\nنام سرگروه:\n<input id='sargrooh' name='sargrooh' type='text' value='$sargrooh' class='inp' style='width:70%;' >\n</td>\n<td>\nتوضیحات:\n<input id='toz' name='toz' type='text' value='$toz' class='inp' style='width:70%;' >\n</td>\n</tr>\n";
		$output.='<tr><td>جمع کل:'.monize($jam_ghi_khadamat+$hotel_ghimat+$shab_reserve_gh+$rooz_reserve_gh).'</td><td colspan="2"><input type="button" onclick="sendfrm();" class="inp" id="sabt_nahaee" value="ثبت نهایی" > <input type="hidden" name="mod1" value="5" ></td></tr></table></form>';
	}
	$tmp1 = array();
	if(isset($_REQUEST['mod1']) && $_REQUEST['mod1']==5 )
	{
		$hotel_ghimat+= $shab_reserve_gh + $rooz_reserve_gh;
		$khadamat_arr=null;
		foreach($ghimat as $id => $ghi)
		{
			if(isset($checkbox[$id]) || isset($textbox[$id]))
			{
				$khedmat = new khadamat_class((int)$id);
				$khadamat_arr[] =array('id'=>$id,'tedad'=>((isset($textbox[$id]))?$textbox[$id]:1),'ghimat'=>$ghi) ;
				//$khadamat_arr[]['tedad'] = ;
				//$khadamat_arr[]['ghimat'] = $ghi;
				//room_det_class::sabtKhadamat($hotel_id,$tmp1['reserve_id'],$ajans_id,$khadamat_id,$khadamat_ghimat,$tmp1['shomare_sanad'],$khadamat_tedad);
			}
		}
		$tmp1 = room_det_class::preReserve($hotel_id,$ajans_id,$room_typ_id,$hotel_ghimat,$aztarikh,$shab,$tedad_otagh,$shab_reserve,$rooz_reserve,$tedad_nafarat,$khadamat_arr);
		if($tmp1!==FALSE)
		{
			room_det_class::sabtReserveHotel($tmp1['reserve_id'],$tmp1['shomare_sanad'],'',$sargrooh,$toz,$ajans_id);
			$msg = 'شماره رزرو شما '.$tmp1['reserve_id'].'<br>';
			$msg .= 'ثبت انجام شد<script>document.getElementById("sabt_nahaee").style.display="none";window.print();window.close();</script>';
		}
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
			function sendfrm()
			{
				var sargrooh = document.getElementById('sargrooh');
				if(sargrooh.value !='')
					document.getElementById('frm123').submit();
				else
					alert('نام سرگروه وارد شود');
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
		<center>
		<div align="center" style="width:21cm;height:29cm;">
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
