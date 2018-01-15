<?php
	session_start();
	include("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if($se->detailAuth('paziresh') && isset($_REQUEST['reserve_id']))
	{
		$cod = dechex((int)$_REQUEST['reserve_id']+10000);
		die("<script> window.location = 'paziresh.php?reserve_id=$cod&'; </script>");
	}
	$msg = '';
	$output = '';
	$reserve_id = 0;
	$bool = FALSE;
	if(isset($_REQUEST['peigiri']) && isset($_REQUEST['mob']) )
	{
		$reserve_id = hexdec($_REQUEST['peigiri'])-10000;
		if((int)$reserve_id > 0)
		{
			$reserve = new reserve_class((int)$reserve_id);
			if($reserve->reserve_id>0)
				$_REQUEST['reserve_id'] = dechex($reserve->reserve_id+10000);
		}
	}
	if(isset($_REQUEST['reserve_id']))
	{
		$out = '';
		$bool = TRUE;
		$edame = FALSE;
		$mob = (isset($_REQUEST['mob']))?$_REQUEST['mob']:-1;
		$reserve_id =$_REQUEST['reserve_id'];
		$reserve =new reserve_class($reserve_id);
		if($reserve!==FALSE)
		{
			$tell =$reserve->hotel_reserve->tozih;
			if(($mob !=-1 && $tell==$_REQUEST['mob']) || $mob==-1)
				$edame = TRUE;
		}
		if($edame)
		{
			$otagh_arr = array();
			$tedad_otagh = '';
			$tedad_kol = 0;
			for($h=0;$h<count($reserve->room);$h++)
				if(isset($otagh_arr[$reserve->room[$h]->room_typ_id]))
					$otagh_arr[$reserve->room[$h]->room_typ_id] ++ ;
				else
					$otagh_arr[$reserve->room[$h]->room_typ_id]=1;
			foreach($otagh_arr as $room_typ_id=>$tedad_room_typ)
			{
				$room_typ_tmp =new room_typ_class($room_typ_id);
				$tedad_otagh .=(($tedad_otagh=='')?'':',').$room_typ_tmp->name.' '.$tedad_room_typ;
				$tedad_kol += $tedad_room_typ;
			}
			$ajans = new ajans_class($reserve->hotel_reserve->ajans_id);
			$ajans_name = $ajans->name;
			$hotel = new hotel_class($reserve->room[0]->hotel_id);
			$hotel_name = $hotel->name;
			$aztarikh = $reserve->room_det[0]->aztarikh;
			$shab = ceil((strtotime($reserve->room_det[0]->tatarikh)-strtotime($reserve->room_det[0]->aztarikh)) / (24*60*60));
			$room_typ_ids = array();
			for($i=0;$i<count($reserve->room_det[0]);$i++)
				$room_typ_ids[] = $reserve->room_det[0]->room_typ;
			$room_name = room_typ_class::loadTypDetails($room_typ_ids);
			$nafar = $reserve->room_det[0]->nafar;
			$sargrooh =$reserve->hotel_reserve->fname.' '.$reserve->hotel_reserve->lname;
			$hotel_mablagh = $reserve->hotel_reserve->m_hotel;
			//$khadamat = unserialize(webservice_class::prepairKhadamat($_REQUEST['khadamat']));
			$khadamat =($reserve->khadamat_det);
			$output = "
			<h3>آژانس $ajans_name از تاریخ ".(jdate("d / m / Y",strtotime($aztarikh)))." به مدت ".(enToPerNums($shab))." شب</h3>
			<br/>";
			$output.='<table style="color:#000;width:90%;" >';
			$output.= '<tr><th style="text-align: center;border:solid 1px;padding:5px;" >هتل</th><th style="text-align: center;border:solid 1px;padding:5px;" >نوع اتاق</th><th style="text-align: center;border:solid 1px;padding:5px;" >تعداد اتاق</th><th style="text-align: center;border:solid 1px;padding:5px;" >تعداد نفرات</th></tr>';
			$output.="<tr><td style='text-align: center;border:solid 1px;padding:5px;' >$hotel_name</td>";
			$output.="<td style='text-align: center;border:solid 1px;padding:5px;'>$tedad_otagh</td>";
		        $output.="<td style='text-align: center;border:solid 1px;padding:5px;'> $tedad_kol</td>";
	 		$output.="<td style='text-align: center;border:solid 1px;padding:5px;'>$nafar</td>";
			$output.='</tr></table><br/>';
			if(count($khadamat)>0)
				$output.='<table style="color:#000;width:80%;" ><tr><th style="text-align: center;border:solid 1px;padding:5px;" >خدمات</th><th style="text-align: center;border:solid 1px;padding:5px;">تعداد-روزانه</th><th style="text-align: center;border:solid 1px;padding:5px;">جزئیات</th></tr>';
			else
				$output.='<table style="color:#000;width:80%;" >';
			$jam_ghi_khadamat = 0;
			for($i=0;$i<count($khadamat);$i++)
			{
					$khadamat_cl= new khadamat_class($khadamat[$i]['khadamat_id']);
					$khadamat_name = $khadamat_cl->name;
					$output .= '<tr>';
					$output .= '<td style="text-align: center;border:solid 1px;padding:5px;" >'.$khadamat_name.'</td>';
					$is_voroodi = '';
					$is_khorooji = '';
					if(($khadamat[$i]['voroodi']))
						$is_voroodi = '  روز ورود-دارد ';
					if(($khadamat[$i]['khorooji']))
						$is_khorooji = '  روز خروج-دارد';
					/*
					if(isset($checkbox[$id]))
					{
						$output .= "<td>دارد $is_voroodi $is_khorooji</td>";
						$jam_ghi_khadamat +=$ghi;
					}
					*/
					//else if(isset($textbox[$id]))
					//{
					$output .= '<td style="text-align: center;border:solid 1px;padding:5px;" >'.$khadamat[$i]['count']."</td><td style=\"text-align: center;border:solid 1px;padding:5px;\" > $is_voroodi $is_khorooji </td>";
					//$jam_ghi_khadamat += $khadamat[$i]['ghimat']*$khadamat[$i]['tedad'];
					//}
					$output .= '</tr>';
			}
			$extra_toz = '';
			if($reserve->hotel_reserve->extra_toz!='')
				$extra_toz = 'توضیحات: '.$reserve->hotel_reserve->extra_toz;
			$output.="<tr>\n<td style=\"text-align: center;border:solid 1px;padding:5px;\" colspan='1'>\nنام سرگروه:\n $sargrooh\n</td>\n<td style='text-align: center;border:solid 1px;padding:5px;' colspan='2' >\nتلفن:\n $tell $extra_toz\n</td>\n</tr>\n";
			$output.='<tr><td style="text-align: center;border:solid 1px;padding:5px;" > شماره‌رزرو و پیگیری:<span style="font-weight:bold;background-color:white;">&nbsp;&nbsp;'.$reserve_id.'&nbsp;&nbsp;</span></td><td style="text-align: center;border:solid 1px;padding:5px;" colspan="2">'.$hotel->name.' '.((isset($hotel->info['properties']))?$hotel->info['properties']:'').'</td></tr>';
			$user_name = new user_class($reserve->room_det[0]->user_id);
			$user_name =$user_name->fname.' '.$user_name->lname;
			$user_printer_name = new user_class($_SESSION['user_id']);
			$user_printer_name =$user_printer_name->fname.' '.$user_printer_name->lname;
			$chapDate = audit_class::hamed_pdate(date("Y-m-d"));
			$output .="<tr><td style='text-align: center;border:solid 1px;padding:5px;' >".(($conf->vacher_mablagh)?"مبلغ: ".monize($reserve->hotel_reserve->m_hotel+$reserve->hotel_reserve->m_belit1+$reserve->hotel_reserve->m_belit2):'&nbsp;')."</td><td colspan='2' style='text-align: center;border:solid 1px;padding:5px;' >رزرو شده توسط $user_name</td></tr>";
			$output .="<tr><td style='text-align: center;border:solid 1px;padding:5px;' colspan='3'>چاپ شده در تاریخ $chapDate صادر شده توسط $user_printer_name</td></tr>";
			$fullView = TRUE;
			if(isset($_REQUEST['fullview']) && $_REQUEST['fullview']=='FALSE')
				$fullView = FALSE;
			$view = (($fullView)?"<input type='button' class='inp' value='نمایش ساده' onclick='loadSimple();' >":"<input type='button' class='inp' value='نمایش کامل' onclick='loadFull();' />");
			$output .="<tr id='last_row' ><td style='text-align: center;border:solid 1px;padding:5px;' ><input type='button' class='inp' value='چاپ' onclick='page_print();' ></td><td style='text-align: center;border:solid 1px;padding:5px;' >$view</td><td style='text-align: center;border:solid 1px;padding:5px;' ><input type='button' class='inp' value='خروج' onclick='bastan();' ></td></tr></table>";
			$reserve->loadWatcher();
			$reserve->watcherAdd('table',$output);
			if($conf->watcher != '' && $fullView)
				$output = $reserve->watcherCompile($conf->watcher);
		}
		else
			$msg ='چنین رزروی وجود ندارد';
	}
	else
	{
		$msg = ("اطلاعات ارسال شده جهت صدور واچر کافی نیست <br/><a href='index.php'>بازگشت</a>");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />	
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<title>
			سامانه رزرواسیون بهار
		</title>
		<script language="javascript" >
		function page_print()
		{
			document.getElementById('last_row').style.display = 'none';
			window.print();
			document.getElementById('last_row').style.display = '';
		}
		function bastan()
		{
			window.close();
		}
		function loadSimple()
		{
			var form = document.createElement("form");
			form.setAttribute("method", "POST");
			form.setAttribute("target", "_self");
			var hiddenField = document.createElement("input");
			hiddenField.setAttribute("name", "fullview");
			hiddenField.setAttribute("value", "FALSE");
			form.appendChild(hiddenField);
			hiddenField = document.createElement("input");
                        hiddenField.setAttribute("name", "reserve_id");
                        hiddenField.setAttribute("value", "<?php echo $reserve_id; ?>");
                        form.appendChild(hiddenField);
			document.body.appendChild(form);
			form.submit();
			document.body.removeChild(form);
		}
		function loadFull()
		{
			var form = document.createElement("form");
			form.setAttribute("method", "POST");
			form.setAttribute("target", "_self");
			var hiddenField = document.createElement("input");
			hiddenField.setAttribute("name", "fullview");
			hiddenField.setAttribute("value", "TRUE");
			form.appendChild(hiddenField);
			hiddenField = document.createElement("input");
                        hiddenField.setAttribute("name", "reserve_id");
                        hiddenField.setAttribute("value", "<?php echo $reserve_id; ?>");
                        form.appendChild(hiddenField);
			document.body.appendChild(form);
			form.submit();
			document.body.removeChild(form);
		}
		</script>
	</head>
	<body style="background: #B5D3FF;padding-bottom: 0px;">
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<center>
			<div align="center" style="width:17cm;background: #B5D3FF;" id="main_div" >
				<?php echo $output.' '.$msg; ?>
			</div>
		</center>
	</body>
</html>
