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
		mysql_class::ex_sql('select `id`,`name` from `hotel` where `moeen_id` > 0 order by `name` ',$q);
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
		for($i=1;$i<10;$i++)
		{
			$sel = (($i==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='$i' >$i</option>\n";
		}
		return $out;
	}
	function loadDaftar($inp)
	{
		$inp = (int)$inp;
		$out = "<select name=\"daftar_id\" id=\"daftar_id\" class=\"inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
		if($_SESSION["typ"] ==0)
			mysql_class::ex_sql('select `id`,`name` from `daftar` order by `name` ',$q);
		if($_SESSION["typ"] !=0)
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `id`='.$inp.' order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;	
	}
	function loadDaftarBelit($inp,$typ)
	{
		$inp = (int)$inp;
		$out = "<select name=\"daftar_idBelit_$typ\" id=\"daftar_idBelit_$typ\" class=\"inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
		if($_SESSION["typ"] ==0)
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `kol_id` > 0 order by `name` ',$q);
		if($_SESSION["typ"] !=0)
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `id`='.$inp.' and `kol_id` > 0 order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;	
	}
	function loadAjans($daftar_id=-1)
	{
		$daftar_id = (int)$daftar_id;
		$out = "<select name=\"ajans_id\" class=\"inp\" style=\"width:auto;\"  >";
		mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$daftar_id)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadAjansBelit($daftar_id,$sel_aj,$typ)
	{
		$daftar_id = (int)$daftar_id;
		$sel_aj = (int)$sel_aj;
		$out = "<select id='ajans_idBelit_$typ' name=\"ajans_idBelit_$typ\" class=\"inp\" style=\"width:auto;\"  >";
		mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$sel_aj)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadKhadamat1($hotel_id)
	{
		$out = '';
		$hotel_id = (int) $hotel_id;
		$kh = khadamat_class::loadKhadamats($hotel_id);
		for($i=0;$i<count($kh );$i++)
		{
			$inp = $inp = 	"<input type='checkbox' name='kh_ch_".$kh[$i]['id']."' >";
			if($kh[$i]['typ']==0)
			{
				$inp = 	"تعداد:<input type='text' class='inp' style='width:30px;' name='kh_txt_".$kh[$i]['id']."' value='".((isset($_REQUEST['kh_'.$kh[$i]['id']]))?$_REQUEST['kh_'.$kh[$i]['id']]:0)."'  >";
			}
			$ghimat = "<div style='display:none;' >قیمت‌واحد:<input class='inp' style='width:70px' name='kh_gh_".$kh[$i]['id']."' value='".((isset($_REQUEST['kh_'.$kh[$i]['ghimat']]))?$_REQUEST['kh_'.$kh[$i]['ghimat']]:$kh[$i]['ghimat'])."' > </div>";
			if(($i % 2) == 0)
				$out .="<tr>";
			$out .="<td>".$kh[$i]['name'].":</td><td>$inp $ghimat</td>";
			if(($i % 2) == 1)
				$out .="</tr>";
		}
		return $out;
	}
	function loadRoomTyp($h_id,$room_typ_def)
	{
		$h_id = (int)$h_id;
		$out = "<select name=\"room_typ_id\" class=\"inp\" style=\"width:auto;\"  >";
		mysql_class::ex_sql("select `room_typ_id`,`room_typ`.`name` from `room`  left join `room_typ` on (`room_typ`.`id`=`room_typ_id`) where `hotel_id`='$h_id' group by `room_typ_id` order by `room_typ`.`name`  ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['room_typ_id']==$room_typ_def)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['room_typ_id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadShabReserve($aztarikh)
	{
		$out = '';
		$h = date("H",strtotime($aztarikh));
		if((int)$h == 0)
			$out = 'checked="checked"';
		return $out;
	}
	function loadRoozReserve($tatarikh)
	{
		$out = '';
		$h = date("H",strtotime($tatarikh));
		if((int)$h == 21)
			$out = 'checked="checked"';
		return $out;
	}
	function loadKhadamat($h_id,$khadamat)
	{
		$out = '';
		$h_id = (int) $h_id;
		$khad = khadamat_class::loadKhadamats($h_id);
		for($i = 0;$i<count($khad);$i++)
		{
			$selected = FALSE;
			$voroodi = '';
			$khorooji = '';
			$tedad = 0;
			$khadamat_found = FALSE;
			for($j = 0;$j<count($khadamat);$j++)
			{
				if($khadamat[$j]['khadamat_id']==$khad[$i]['id'])
				{
					$khadamat_found = TRUE;
					$selected = TRUE;
					$tedad = $khadamat[$j]['count'];
					$jtick = '';
					if($khad[$i]['typ']==1)
						$jtick = "onclick='kh_check(\"".$khad[$i]['id']."\")'";
					if($khad[$i]['voroodi'])
					{
						$voroodi = "اول<input $jtick  type='checkbox' id='khadamat_v_".$khadamat[$j]['khadamat_id']."' name='khadamat_v_".$khadamat[$j]['khadamat_id']."' ".(($khadamat[$j]['voroodi'])?'checked="checked"':'')." >";
					}
					if($khad[$i]['khorooji'])
					{
						$khorooji = "آخر<input $jtick  type='checkbox' id='khadamat_kh_".$khadamat[$j]['khadamat_id']."' name='khadamat_kh_".$khadamat[$j]['khadamat_id']."' ".(($khadamat[$j]['khorooji'])?'checked="checked"':'')." >";
					}
				}
			}
			if(!$khadamat_found)
			{
				$jtick = '';
				if($khad[$i]['typ']==1)
					$jtick = "onclick='kh_check(\"".$khad[$i]['id']."\")'";
				if($khad[$i]['voroodi'])
				{
					$voroodi = "اول<input $jtick  type='checkbox' id='khadamat_v_".$khad[$i]['id']."' name='khadamat_v_".$khad[$i]['id']."'  >";
				}
				if($khad[$i]['khorooji'])
				{
					$khorooji = "آخر<input $jtick  type='checkbox' id='khadamat_kh_".$khad[$i]['id']."' name='khadamat_kh_".$khad[$i]['id']."' >";
				}
			}
			$inp = $khad[$i]['name']." <input style='display:none;' id='khadamat_id_".$khad[$i]['id']."' name='khadamat_id_".$khad[$i]['id']."' type='checkbox' value='1' ".(($selected)?'checked="checked"':'')." >";
			if($khad[$i]['typ']==0)
			{
				$inp = $khad[$i]['name']." روزانه<input class='inp' style='width:30px;' type='text' name='khadamat_id_". $khad[$i]['id']."' value='$tedad' >";
			}
			if($i%2 == 0)
				$out .= "<tr><td colspan='2' style='border-style:solid;border-width:1px;' >$inp $voroodi $khorooji</td>";
			else
				$out .= "<td colspan='2' style='border-style:solid;border-width:1px;' >$inp $voroodi $khorooji</td></tr>";

		}
		return $out;
	}
	function showHide($rooms,$room_selected)
	{
		$out = 'none';
		for($j=0;$j<count($room_selected);$j++)
			for($i = 0;$i < count($rooms);$i++)
				if($room_selected[$j]->getId() ==(int)$rooms[$i])
					$out = '';
		return($out);
	}
	$mode1 = ((isset($_REQUEST['mode1']))?(int)$_REQUEST['mode1']:0);
	$msg = '';
	$show_sabt = '';
        $output = '';
	//-----newstart-----
	$h_id = ((isset($_REQUEST['h_id']))?(int)$_REQUEST['h_id']:-1);	
        $d = ((isset($_REQUEST['d']))?$_REQUEST['d']:perToEnNums(jdate("m")));
        $month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
        $da = audit_class::hamed_pdateBack(jdate("Y/$d/d"));
        $tmp = explode(" ",$da);
        $da = $tmp[0];
	if ($mode1!=1)
        {
	//-----newend------
		if($_SESSION["typ"] ==0 )
		{
			$daftar_id = ((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
			$daftar_idBelit = ((isset($_REQUEST['daftar_idBelit']))?(int)$_REQUEST['daftar_idBelit']:-1);
		}
		if($_SESSION["typ"] !=0)
		{
			$daftar_id = (int)$_SESSION["daftar_id"] ;
			$daftar_idBelit = (int)$_SESSION["daftar_id"] ;
		}
		if($h_id>0 && isset($_REQUEST['reserve_id']) && $_REQUEST['mod']==301 )
		{
			$reserve_id = (int)$_REQUEST['reserve_id'];
			$reserve = new reserve_class($reserve_id);
			$agency = new ajans_class($reserve->hotel_reserve->ajans_id);
			$daftar_reserve = new daftar_class($agency->daftar_id);
			$lname = ((isset($_REQUEST['lname']))?$_REQUEST['lname']:'');
			$toz = ((isset($_REQUEST['toz']))?$_REQUEST['toz']:'');
			$m_belit_sabti_1 = ((isset($_REQUEST['m_belit_1']))?$_REQUEST['m_belit_1']:0);
			$m_belit_sabti_2 = ((isset($_REQUEST['m_belit_2']))?$_REQUEST['m_belit_2']:0);
			$m_hotel_sabti = ((isset($_REQUEST['m_hotel']))?$_REQUEST['m_hotel']:0);
			$room_typ_id = ((isset($_REQUEST['room_typ_id']))?$_REQUEST['room_typ_id']:-1);
			$tedad_nafarat = ((isset($_REQUEST['tedad_nafarat']))?$_REQUEST['tedad_nafarat']:-1);
			//$tedad_otagh = ((isset($_REQUEST['tedad_otagh']))?$_REQUEST['tedad_otagh']:0);
			$tedad_otagh = ((isset($_REQUEST['tedad_otagh']))?(int)$_REQUEST['tedad_otagh']:0);
			foreach($_REQUEST as $key=>$value)
			{
				$tmp = explode('_',$key);
				if($tmp[0]=='otagh')
					$room_ids[] = (int)$tmp[1];

			}
			if($tedad_otagh==0)
				$tedad_otagh = count($room_ids);
			$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):'');
			$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):'');
			$shab_reserve = ((isset($_REQUEST['shab_reserve']))?TRUE:FALSE);
			$rooz_reserve = ((isset($_REQUEST['rooz_reserve']))?TRUE:FALSE);
			$daftar_idbelit_1 = ((isset($_REQUEST['daftar_idBelit_1']))?$_REQUEST['daftar_idBelit_1']:-1);
			$ajans_idbelit_1 = ((isset($_REQUEST['ajans_idBelit_1']))?$_REQUEST['ajans_idBelit_1']:-1);
			$daftar_idbelit_2 = ((isset($_REQUEST['daftar_idBelit_2']))?$_REQUEST['daftar_idBelit_2']:-1);
			$ajans_idbelit_2 = ((isset($_REQUEST['ajans_idBelit_2']))?$_REQUEST['ajans_idBelit_2']:-1);
			if($shab_reserve)
			{
				$tmp = explode(' ',$aztarikh);
				$aztarikh = $tmp[0].' 00:00:00';
			}
			if($rooz_reserve)
                        {
                                $tmp = explode(' ',$tatarikh);
                                $tatarikh = $tmp[0].' 21:00:00';
                        }
			$shab = (int)((strtotime($tatarikh) - strtotime($aztarikh))/(24*60*60)) ;
			if(($m_belit_sabti_1 + $m_belit_sabti_2)!=0)
			{
				$ghimat[]= $m_belit_sabti_1 +$m_belit_sabti_2+ $m_hotel_sabti;
				$ghimat[] = $m_belit_sabti_1;
				$ajans_belit_1 = new ajans_class($ajans_idbelit_1) ; 
				$ghimat[] =$ajans_belit_1->moeen_id;
				$daftar_belit_1 = new daftar_class($daftar_idbelit_1);
				$ghimat[] =$daftar_belit_1->kol_id;

				$ghimat[] = $m_belit_sabti_2;
				$ajans_belit_2 = new ajans_class($ajans_idbelit_2) ; 
				$ghimat[] =$ajans_belit_2->moeen_id;
				$daftar_belit_2 = new daftar_class($daftar_idbelit_2);
				$ghimat[] =$daftar_belit_2->kol_id;
			}
			else
			{
				$ghimat= $m_hotel_sabti; 
			}
			//var_dump($ghimat);
			$khadamat_sabti = null;
			$m_tour = 0;
			foreach($_REQUEST as $key => $value)
			{
				$tmp = explode('_',$key);
				if($tmp[0] == 'khadamat' && $tmp[1] == 'id' && $value>0)
					$khadamat_sabti_id[(int)$tmp[2]] =(int)$value ;
				else if($tmp[0] == 'khadamat' && $tmp[1] == 'v' )
					$khadamat_sabti_v[(int)$tmp[2]] =TRUE ;
				else if($tmp[0] == 'khadamat' && $tmp[1] == 'kh' ) 
					$khadamat_sabti_kh[(int)$tmp[2]] =TRUE ;
			}
			if(isset($khadamat_sabti_id))
			{
				foreach($khadamat_sabti_id as $id =>$tedad)
				{
					$tmp_voroodi = ((isset($khadamat_sabti_v[$id]))?TRUE:FALSE);
					$tmp_khorooji = ((isset($khadamat_sabti_kh[$id]))?TRUE:FALSE);
					$khadamat_sabti[] = array('id'=>$id , 'tedad'=>$tedad , 'ghimat'=>0,'voroodi'=>$tmp_voroodi , 'khorooji' => $tmp_khorooji);
				}
			}
			$tmp_room =$room_typ_id;
			if(isset($room_ids) && count($room_ids)>0)
				$tmp_room = $room_ids;
			$reserveid_shomaresanad = room_det_class::reReserve($reserve_id,$h_id,$reserve->hotel_reserve->ajans_id,$tmp_room,$ghimat,$aztarikh,$shab,$tedad_otagh,$shab_reserve,$rooz_reserve,$tedad_nafarat,$khadamat_sabti);
			//var_dump($reserveid_shomaresanad);
			room_det_class::sabtReserveHotel($reserve_id,$reserveid_shomaresanad['shomare_sanad'],'',$lname,$toz,$reserve->hotel_reserve->ajans_id,array($m_belit_sabti_1,$m_belit_sabti_2),$m_hotel_sabti);
			for($l=0;$l<count($reserveid_shomaresanad['shomare_sanad']);$l++)
			{
				$tozih_sabti = room_det_class::loadReserve($reserveid_shomaresanad['reserve_id']);
				mysql_class::ex_sqlx("update `sanad` set `tozihat`='اصلاحیه $tozih_sabti' where `id`=".$reserveid_shomaresanad['shomare_sanad'][$l]);
				//echo "update `sanad` set `tozihat`='اصلاحیه $tozih_sabti' where `id`=".$reserveid_shomaresanad['shomare_sanad'][$l].'<br/>';
			}
		}
		if($h_id>0 && isset($_REQUEST['reserve_id']) )
		{
			$reserve_id = (int)$_REQUEST['reserve_id'];
			$c_user = new user_class((int)$_SESSION['user_id']);
			$bool =($reserve = new reserve_class($reserve_id));
			$agency = new ajans_class($reserve->hotel_reserve->ajans_id);
			$daftar_reserve = new daftar_class($agency->daftar_id);
			if(isset($reserve->room_det[0]->aztarikh) && date("Y-m-d") > date("Y-m-d",strtotime($reserve->room_det[0]->aztarikh." - 1 day")) && $_SESSION["typ"] !=0 )
				$show_sabt = 'disabled="disabled"';
			if($agency->daftar_id!=$c_user->daftar_id && $_SESSION["typ"] !=0)
				$bool = FALSE;
				//die("<script language='javascript' >window.location = 'showreserve.php?h_id=$h_id&';</script>");
			if($bool)
			{
				$output='<br/><table border="1" style="border-style:dashed;width:80%;" >';
				$output .='<tr><th>نام و نام خانوادگی سرگروه</th><th>توضیحات</th><th>آژانس رزرو کننده</th><th>مبلغ بلیت</th><th>مبلغ هتل</th></tr>';
				$output .= "<tr><td><input class='inp' type='text' name='lname' id='lname' value='".$reserve->hotel_reserve->lname."' ></td>";
				$output .= "<td><input class='inp' type='text' name='toz' id='toz' value='".$reserve->hotel_reserve->tozih."' ></td>";
				$hotel_sabti = new hotel_class($h_id);
				$agency = new ajans_class($reserve->hotel_reserve->ajans_id);
				$daftar_reserve = new daftar_class($agency->daftar_id);
				$output .= "<td><input class='inp' readonly='readonly' type='text' name='ajans' id='ajans' value='".$agency->name."(".$daftar_reserve->name.")' ><input class='inp' readonly='readonly' type='hidden' name='ajans_id' id='ajans_id' value='".$reserve->hotel_reserve->ajans_id.")' ></td>";
				$output .= "<td>رفت:<input  class='inp' type='text' name='m_belit_1' id='m_belit_1' value='".$reserve->hotel_reserve->m_belit1."' >برگشت:<input class='inp' type='text' name='m_belit_2' id='m_belit_2' value='".$reserve->hotel_reserve->m_belit2."' ></td>";
				$m_hotel = $reserve->hotel_reserve->m_hotel;
				$output .= "<td><input class='inp' type='text' name='m_hotel' id='m_hotel' value='$m_hotel' ></td></tr>\n";
				if(!$conf->room_select)
				{
					$output .= '<tr><th>نوع اتاق</th><th>تعداد نفرات</th><th>تعداد اتاق درخواستی</th><th>تاریخ‌ورود</th><th>تاریخ‌خروج</th></tr>'."\n";
					$output .= '<tr>';
					$output .= "<td>".loadRoomTyp($h_id,$reserve->room_det[0]->room_typ)."</td>";
					$output .= "<td><input class='inp' type='text' name='tedad_nafarat' id='tedad_nafarat' value='".$reserve->room_det[0]->nafar."' ></td>";
					$output .= "<td><input class='inp' type='text' name='tedad_otagh' id='tedad_otagh' value='".count($reserve->room_det)."' ></td>";
					$output .= "<td><input class='inp' type='text' name='aztarikh' id='aztarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->aztarikh)."' ></td>";
					$output .= "<td><input class='inp' type='text' name='tatarikh' id='tatarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->tatarikh)."' ></td>";
					$output .= '</tr>';
				}
				else
				{
					$rooms = room_class::loadRooms($h_id);
					$outRoom = '<table border="1" style="width:100%" >';
					for($i=0;$i<count($rooms);$i++)
					{
						$room_numbers = $rooms[$i]['room_ids'];
						$outRoom .= "<tr><td id='show_$i' onmouseover='change_color(this,\"in\");' onmouseout='change_color(this,\"out\");' onclick='show_hide(\"tr_$i\",this);' style='cursor:pointer' > ".((showHide($room_numbers,$reserve->room)=='none')?"مشاهده":"عدم مشاهده")."</td>";
						$outRoom .= "<td>".$rooms[$i]['name']."</td>\n";
						$outRoom .= "<td>".$rooms[$i]['count']."</td>\n";
						$outRoom .= "<td>بطور خودکار محاسبه می شود<input style='display:none;' class='inp' name='room_gh_".$rooms[$i]['room_typ_id']."' value='".$rooms[$i]['ghimat']."' > </td></tr><tr style='display:".showHide($room_numbers,$reserve->room).";' id='tr_$i' >";
						$outRoom .= "<td style='text-align:right;' colspan='40' width='99%' > ";
						for($j=0;$j<count($room_numbers);$j++)
						{
							$shomare_otagh = new room_class($room_numbers[$j]);
							$check_box = (($reserve->roomIsReserved($room_numbers[$j]))?'checked="checked"':'');
							$outRoom .= $shomare_otagh->name."<input type='checkbox' $check_box name='otagh_".$room_numbers[$j]."' id='otagh_".$room_numbers[$j]."' value='".$room_numbers[$j]."' >&nbsp;&nbsp;&nbsp;";
						}
						$output .= "</td>\n</tr>";
					}
					$outRoom .= "</table>";
					$output .= "<tr><th colspan='5' style='border-style:dashed;border-width:1px;' > اتاق</th></tr><tr><td colspan='5' style='width:80%'  >$outRoom</td></tr>";
					$output .= '<tr><th>تعداد نفرات</th><th colspan="2" >تاریخ‌ورود</th><th colspan="2" >تاریخ‌خروج</th></tr>';
					$output .= '<tr>';
					$output .= "<td><input class='inp' type='text' name='tedad_nafarat' id='tedad_nafarat' value='".$reserve->room_det[0]->nafar."' ></td>";
					$output .= "<td colspan='2' ><input class='inp' type='text' name='aztarikh' id='aztarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->aztarikh)."' ></td>";
					$output .= "<td colspan='2' ><input class='inp' type='text' name='tatarikh' id='tatarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->tatarikh)."' ></td>";
					$output .= '</tr>';
					
				}
				$output .= '<tr>';
				$output .= '<td colspan="5"></tr> ';
				$output .='<table width="80%"><tr><th>انتخاب حساب کل بلیت</th><th>انتخاب حساب معین بلیت</th><th>شب-رزرو(نیم شارژ ورودی)</th><th>روز-رزرو(نیم‌شارژ خروجی)</th></tr>';

				//----------------------load kardane daftar va ajans ghabli-------
				$lother_id = array();
				$lother_kol_id = array();
				$q = null;
				mysql_class::ex_sql('select `moeen_id`,`kol_id` from `sanad` where `id` in (select `sanad_record` from `sanad_reserve` where `reserve_id`='.$reserve_id.') and `moeen_id` <> '.$hotel_sabti->moeen_id.' and `moeen_id` <> '.$agency->moeen_id.' order by `id` desc limit 2',$q);
				while($r = mysql_fetch_array($q))
				{
					$lother_id[] = $r['moeen_id'];
					$lother_kol_id[] = $r['kol_id'];
				}
				$belit_ghabli_tmp = array();
				for($h = 0;$h<count($lother_id);$h++)
				{
					$daftar_ghabli = new daftar_class;
					$daftar_ghabli->loadByKol($lother_kol_id[$h]);
					$ajans_ghabli = new ajans_class;
					$ajans_ghabli->loadByMoeen($lother_id[$h]);
					$belit_ghabli_tmp[] = array('daftar_ghabli'=>$daftar_ghabli,'ajans_ghabli'=>$ajans_ghabli);
				}
				$belit_ghabli_daftar= array(-1,-1,-1);
				$belit_ghabli_ajans= array(-1,-1,-1);
				for($o = 0;$o<count($belit_ghabli_tmp);$o++)
				{
					$belit_ghabli_daftar[$o] = $belit_ghabli_tmp[$o]['daftar_ghabli']->id;
					$belit_ghabli_ajans[$o] = $belit_ghabli_tmp[$o]['ajans_ghabli']->id;
				}
				//-------------------------------------------------
				$req_daftar_id_1 = ((isset($_REQUEST['daftar_idBelit_1']) && (int)$_REQUEST['daftar_idBelit_1']>=0)?$_REQUEST['daftar_idBelit_1']:$belit_ghabli_daftar[0]);
				$req_daftar_id_2 = ((isset($_REQUEST['daftar_idBelit_2']) && (int)$_REQUEST['daftar_idBelit_2']>=0)?$_REQUEST['daftar_idBelit_2']:$belit_ghabli_daftar[1]);
				$sel_aj_1 = ((isset($_REQUEST['ajans_idBelit_1']) && (int)$_REQUEST['ajans_idBelit_1']>=0)?$_REQUEST['ajans_idBelit_1']:$belit_ghabli_ajans[0]);
				$sel_aj_2 = ((isset($_REQUEST['ajans_idBelit_2']) && (int)$_REQUEST['ajans_idBelit_2']>=0)?$_REQUEST['ajans_idBelit_2']:$belit_ghabli_ajans[1]);
				$output .="<tr><td>رفـــت:".loadDaftarBelit($req_daftar_id_1,1)."</td><td>".loadAjansBelit($req_daftar_id_1,$sel_aj_1,1)."</td>";
				$output .="<td><input type='checkbox' name='shab_reserve' id='shab_reserve' ".loadShabReserve($reserve->room_det[0]->aztarikh)." > </td>";
				$output .="<td><input type='checkbox' name='rooz_reserve' id='rooz_reserve' ".loadRoozReserve($reserve->room_det[0]->tatarikh)." > </td></tr>";

				$output .="<tr><td>برگشت:".loadDaftarBelit($req_daftar_id_2,2)."</td><td>".loadAjansBelit($req_daftar_id_2,$sel_aj_2,2)."</td>";

				$output .="<td colspan='2' >&nbsp;</td></tr>";
				$output .='<tr><th colspan="5" style="border-style:dashed;border-width:1px;" >خدمات</th></tr>'.loadKhadamat($h_id,$reserve->khadamat_det);
				$output .='</table>';
				$output .= "</td>";
				$output .= '</tr>';
				$output .= "<tr><td colspan='5' ><input class='inp' type='button' $show_sabt value='ثبت' onclick='submit_frm();' ></td></tr>";
				$output .= '</table>';
			}
			else
			{
				$output .= "چنین شماره رزروی وجود ندارد یا شما به آن دسترسی ندارید";
			}
		}
	}
        $hotel1 = new hotel_class($h_id);
        $outvazeat = $hotel1->loadRooms($da,'send_reserve');      
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
		<script  type="text/javascript" >	
			function radioChecked()
			{
				var out = false;
				var inps = document.getElementsByTagName('input');
				for(var i=0;i < inps.length;i++)
					if(inps[i].type=='radio' && inps[i].checked)
						out = true;
				if(document.getElementById('daftar_id').selectedIndex <= 0 )
					out = false;
				return(out);
			}
			function send_search()
			{
				if(document.getElementById('daftar_idBelit_1'))
                                        document.getElementById('daftar_idBelit_1').selectedIndex = -1;
                                if(document.getElementById('ajans_idBelit_1'))
                                        document.getElementById('ajans_idBelit_1').selectedIndex = -1;
				if(document.getElementById('daftar_idBelit_2'))
                                        document.getElementById('daftar_idBelit_2').selectedIndex = -1;
                                if(document.getElementById('ajans_idBelit_2'))
                                        document.getElementById('ajans_idBelit_2').selectedIndex = -1;
				document.getElementById('mod').value=1;
				document.getElementById('frm1').submit();
			}
			function send_reserve(reserve_id)
			{
				if(document.getElementById('daftar_idBelit_1'))
                                        document.getElementById('daftar_idBelit_1').selectedIndex = -1;
                                if(document.getElementById('ajans_idBelit_1'))
                                        document.getElementById('ajans_idBelit_1').selectedIndex = -1;
				if(document.getElementById('daftar_idBelit_2'))
                                        document.getElementById('daftar_idBelit_2').selectedIndex = -1;
                                if(document.getElementById('ajans_idBelit_2'))
                                        document.getElementById('ajans_idBelit_2').selectedIndex = -1;
				document.getElementById('reserve_id').value=reserve_id;
				document.getElementById('mod').value=1;
				document.getElementById('frm1').submit();	
			}
			function submit_frm()
			{
				//alert(document.getElementById('daftar_idBelit_2').selectedIndex);
				if( (parseInt(document.getElementById('m_belit_1').value,10)==0 && document.getElementById('daftar_idBelit_1').selectedIndex >0) || (parseInt(document.getElementById('m_belit_1').value,10)>0 && document.getElementById('daftar_idBelit_1').selectedIndex <=0) || (parseInt(document.getElementById('m_belit_2').value,10)==0 && document.getElementById('daftar_idBelit_2').selectedIndex >0) || (parseInt(document.getElementById('m_belit_2').value,10)>0 && document.getElementById('daftar_idBelit_2').selectedIndex <=0) )
				{
					alert('اطلاعات مربوط به بلیت را وارد کنید');
				}
				else
				{     
					document.getElementById("mod").value = 301;
					document.getElementById('frm1').submit();
				}
			}
			function kh_check(inp)
			{
				var mainObj = document.getElementById('khadamat_id_'+inp);
				var vObj = document.getElementById('khadamat_v_'+inp);
				var khObj = document.getElementById('khadamat_kh_'+inp);
				if(vObj.checked || khObj.checked )
					mainObj.checked = true;
				else
					mainObj.checked = false;
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
			td{text-align:center;}
		</style>
		<title>
			سامانه رزرواسیون	
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<form id="frm2" method="get">
                                وضعیت رزرو <?php echo $hotel1->name; ?> ﺩﺭ :
                                <select name="d" class="inp" onchange="document.getElementById('frm2').submit();">
                                <?php
                                        for($i=1;$i<=count($month);$i++)
                                                echo "<option value=\"$i\"".(($i==$d)?"selected=\"selected\"":"").">\n".$month[$i-1]."\n</option>\n";
                                ?>
                                </select>
                               ماه 
                                <input type="hidden" id="h_id" name="h_id" value="<?php echo $h_id; ?>" />
				<input type="hidden" id="mode1" name="mode1" value="1"/>
                        </form>
			<?php
                                //echo jdate("F",strtotime($da));
                                echo $outvazeat ;
                        ?>

			<br/>
			<br/>
			<form id='frm1'  method='GET' >
			<table border='1' >
				<tr>
					<th>شماره رزرو</th>
					<th>جستجو</th>
				</tr>
				<tr>
					<td>
						<input class="inp" name="reserve_id" id="reserve_id" type="text" value="<?php echo ((isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:0); ?>" >
					</td>
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='hidden' name='mode1' id='mode1' value='0' >
						<input type='hidden' name='d' value="<?php echo $d;?>"/>
						<input type='hidden' name="h_id" id="h_id" value="<?php echo ((isset($_REQUEST['h_id']))?(int)$_REQUEST['h_id']:0); ?>" >
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
			<?php echo $output.' '.$msg; ?>
			</form>
		</div>
	</body>
</html>
