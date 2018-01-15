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
	function loadDaftarBelit($inp)
	{
		$inp = (int)$inp;
		$out = "<select name=\"daftar_idBelit\" id=\"daftar_idBelit\" class=\"inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
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
	function loadAjansBelit($daftar_id,$sel_aj)
	{
		$daftar_id = (int)$daftar_id;
		$out = "<select id='ajans_idBelit' name=\"ajans_idBelit\" class=\"inp\" style=\"width:auto;\"  >";
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
			$tedad = 0;
			for($j = 0;$j<count($khadamat);$j++)
				if($khadamat[$j]['khadamat_id']==$khad[$i]['id'])
				{
					$selected = TRUE;
					$tedad = $khadamat[$j]['count'];
				}
			$inp = $khad[$i]['name']." <input name='khadamat_id_".$khad[$i]['id']."' type='checkbox' value='1' ".(($selected)?'checked="checked"':'')." >";
			if($khad[$i]['typ']==0)
			{
				$inp = $khad[$i]['name']." <input class='inp' style='width:30px;' type='text' name='khadamat_id_". $khad[$i]['id']."' value='$tedad' >";
			}
			if($i%2 == 0)
				$out .= "<tr><td colspan='2' style='border-style:solid;border-width:1px;' >$inp</td>";
			else
				$out .= "<td colspan='2' style='border-style:solid;border-width:1px;' >$inp</td></tr>";

		}
		return $out;
	}
	$mode1 = ((isset($_REQUEST['mode1']))?(int)$_REQUEST['mode1']:0);
	$msg = '';
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
			$m_belit_sabti = ((isset($_REQUEST['m_belit']))?$_REQUEST['m_belit']:-1);
			$m_tour_sabti = ((isset($_REQUEST['m_tour']))?$_REQUEST['m_tour']:-1);
			$room_typ_id = ((isset($_REQUEST['room_typ_id']))?$_REQUEST['room_typ_id']:-1);
			$tedad_nafarat = ((isset($_REQUEST['tedad_nafarat']))?$_REQUEST['tedad_nafarat']:-1);
			$tedad_otagh = ((isset($_REQUEST['tedad_otagh']))?$_REQUEST['tedad_otagh']:-1);
			$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):'');
			$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):'');
			$shab_reserve = ((isset($_REQUEST['shab_reserve']))?TRUE:FALSE);
			$rooz_reserve = ((isset($_REQUEST['rooz_reserve']))?TRUE:FALSE);
			//$_REQUEST['daftar_idBelit']
			//ajans
			$daftar_idbelit = ((isset($_REQUEST['daftar_idBelit']))?$_REQUEST['daftar_idBelit']:-1);
			$ajans_idbelit = ((isset($_REQUEST['ajans_idBelit']))?$_REQUEST['ajans_idBelit']:-1);
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
			$khadamat_sabti = null;
			$m_tour = 0;
			mysql_class::ex_sql("select abs(sum(`typ`*`mablagh`)) as `m_tour` from `sanad` where `id` in (select `sanad_record` from `sanad_reserve` where `reserve_id`=$reserve_id) and `moeen_id` = '".$agency->moeen_id."'",$qqq);
			if($r=mysql_fetch_array($qqq))
				$m_tour = $r['m_tour'];
			foreach($_REQUEST as $key => $value)
			{
				$tmp = explode('_',$key);
				if($tmp[0] == 'khadamat' && $tmp[1] == 'id' && $value>0)
				{
					$khadamat_sabti[(int)$tmp[2]] =(int)$value ;
				}
			}
			$hotel = new hotel_class($h_id);
			$shab = (int)(strtotime($tatarikh)-strtotime($aztarikh))/(24*60*60);
			if($hotel->hotelAvailableBetween($aztarikh,$tatarikh))
			{
				$rooms_sabti = room_class::loadopenRooms($aztarikh,$shab,$shab_reserve,$rooz_reserve,$h_id);
				$room_ok = FALSE;
				for($y =0;$y<count($rooms_sabti);$y++)
				{
					if(($tedad_otagh<=$rooms_sabti[$y]['count'] && $reserve->room_det[0]->room_typ!=$room_typ_id && $rooms_sabti[$y]['room_typ_id'] ==$room_typ_id)||($reserve->room_det[0]->room_typ==$room_typ_id && (($tedad_otagh<=count($reserve->room_det) && ($rooms_sabti[$y]['room_typ_id'] !=$room_typ_id))||(($tedad_otagh<=$rooms_sabti[$y]['count']+count($reserve->room_det) && ($rooms_sabti[$y]['room_typ_id'] ==$room_typ_id))))))
					{
						$room_ok = TRUE;
					}
				}
				if($room_ok && $m_tour_sabti>=$m_belit_sabti)
				{
					mysql_class::ex_sqlx("update `hotel_reserve` set `lname`='$lname',`tozih`='$toz',`m_belit`='$m_belit_sabti' where `reserve_id`='$reserve_id' ");
					mysql_class::ex_sqlx("delete from `room_det` where `reserve_id`=$reserve_id ");
					$openrooms = room_class::loadopenRoomArray($aztarikh,$shab,$shab_reserve,$rooz_reserve,$h_id);
					$i0= 0;
					$user_id = (int)$_SESSION['user_id'];
					if($tedad_otagh>0)
						$ghimat_sabti =(int)(($m_tour_sabti - $m_belit_sabti)/$tedad_otagh);
					for($e = 0;$e<count($openrooms);$e++)
					{
						$tmp_room = new room_class($openrooms[$e]);
						if($tmp_room->room_typ_id == $room_typ_id && $i0<$tedad_otagh)
						{
							mysql_class::ex_sqlx("insert into `room_det` (`reserve_id`,`room_id`,`aztarikh`,`tatarikh`,`ghimat`,`user_id`,`nafar`) values ('$reserve_id','".$openrooms[$e]."','$aztarikh','$tatarikh','$ghimat_sabti','$user_id','$tedad_nafarat')");
							$i0++;
						}
					}
					mysql_class::ex_sqlx("delete from `khadamat_det` where `reserve_id`=$reserve_id ");
					if(is_array($khadamat_sabti))
					{
						foreach($khadamat_sabti as $khadamat_id_sabti=>$khadamat_tedad_sabti)
						{
							for($e=0;$e<$khadamat_tedad_sabti;$e++)
								mysql_class::ex_sqlx("insert into `khadamat_det` (`khadamat_id`,`reserve_id`) values ('$khadamat_id_sabti','$reserve_id') ");
						}
					}

                                        $hotel_sabti = new hotel_class((int)$h_id);
                                        $h_moeen = new moeen_class($hotel_sabti->moeen_id);
                                        if($daftar_idbelit>0)
                                        {
	                                        $daftar_idbelit = new daftar_class($daftar_idbelit);
                                                $ajans_idbelit = new ajans_class($ajans_idbelit);
                                                $other_id = $ajans_idbelit->moeen_id;
                                                $other_kol_id = $daftar_idbelit->kol_id;
                                                $lother_id = -1;
                                                $lother_kol_id = -1;
                                                $q = null;
                                                mysql_class::ex_sql('select `moeen_id`,`kol_id` from `sanad` where `id` in (select `sanad_record` from `sanad_reserve` where `reserve_id`='.$reserve_id.') and `moeen_id` <> '.$hotel_sabti->moeen_id.' and `moeen_id` <> '.$agency->moeen_id.' order by `id` desc limit 1',$q);
                                                if($r = mysql_fetch_array($q))
                                                {
        	                                        $lother_id = $r['moeen_id'];
                                                        $lother_kol_id = $r['kol_id'];
                                                }
                                        }
					if($m_tour!=$m_tour_sabti || $reserve->hotel_reserve->m_belit!=$m_belit_sabti || $lother_id != $other_id)
					{
						$diff = abs($m_tour_sabti-$m_tour);
						$diff_belit = abs($m_belit_sabti-$reserve->hotel_reserve->m_belit) ;
						if($diff_belit==0 && $lother_id == $other_id)
						{
							if($m_tour<$m_tour_sabti)
							{
								$sanad_sabti_id = sanadzan_class::newHotelReserveSanad($h_id,$reserve->hotel_reserve->ajans_id,$diff);
							}
							else if($m_tour>$m_tour_sabti)
							{
								$sanad_sabti_id = sanadzan_class::newHotelRefundSanad($h_id,$reserve->hotel_reserve->ajans_id,$diff,'');
							}
							for($e=0;$e<count($sanad_sabti_id);$e++)
                                                        {
                                                                mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$sanad_sabti_id[$e]."') ");
                                                        }
							
						}
/*
						else if($lother_id == $other_id && -1 != $other_id)
						{
							if($m_belit_sabti>$reserve->hotel_reserve->m_belit)
							{
								$sanad_sabti_id = sanadzan_class::newTourReserveSanad($h_id,$reserve->hotel_reserve->ajans_id,$other_id,$other_kol_id,$diff,$diff_belit);
								for($e=0;$e<count($sanad_sabti_id);$e++)
		                                                {
		                                                        mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$sanad_sabti_id[$e]."') ");
		                                                }
								$mxs = 0;
		                                                mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
		                                                if($r = mysql_fetch_array($q))
		                                                        $mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
		                                                $q = null;
		                                                $tarikh = date("Y-m-d");
		                                                mysql_class::ex_sql("SELECT `tarikh` from `sanad` where `shomare_sanad`=$mxs",$q);
		                                                if($r = mysql_fetch_array($q))
		                                                        $tarikh = $r["tarikh"];
		                                                $tarikh = date("Y-m-d",strtotime($tarikh));
		                                                $shomare_sanad = $mxs;
		                                                if(strtotime($tarikh)!=strtotime(date("Y-m-d")))
		                                                        $shomare_sanad++;
								$a_moeen = $agency->moeen_id;
								$tmp = new daftar_class($agency->daftar_id);
								$a_kol = $tmp->kol_id;
								if($diff == 0)
								{
									mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`) values ($shomare_sanad,$a_kol,$a_moeen,'$tarikh',$user_id,-1,1,$diff_belit)");
			                                                $q = null;
			                                                mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$a_kol and `moeen_id`=$a_moeen and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=-1 and `en`=1 and `mablagh`=$diff_belit",$q);
		        	                                        if($r = mysql_fetch_array($q))
		                	                                        $sanad_sabti_id[] = (int)$r['id'];
		                        	                        for($e=0;$e<count($sanad_sabti_id);$e++)
		                                	                {
		                                        	                mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$sanad_sabti_id[$e]."') ");
		                                                	}
								}
							}
							else
							{
								$diff = abs($diff);
								if($m_tour<$m_tour_sabti)
								{
									$sanad_sabti_id = sanadzan_class::newInverseTourReserveSanad($h_id,$reserve->hotel_reserve->ajans_id,$other_id,$other_kol_id,$diff,$diff_belit);
								}
								else if($m_tour>$m_tour_sabti)
								{
									$sanad_sabti_id = sanadzan_class::newTourReserveSanad($h_id,$reserve->hotel_reserve->ajans_id,$other_id,$other_kol_id,$diff,$diff_belit);
								}
								if(1)
								{
									for($e=0;$e<count($sanad_sabti_id);$e++)
			                                                {	
		                                                        	mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$sanad_sabti_id[$e]."') ");
		                                                	}
									$mxs = 0;
		                                	                mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
		                        	                        if($r = mysql_fetch_array($q))
		                	                                        $mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
		        	                                        $q = null;
			                                                $tarikh = date("Y-m-d");
			                                                mysql_class::ex_sql("SELECT `tarikh` from `sanad` where `shomare_sanad`=$mxs",$q);
			                                                if($r = mysql_fetch_array($q))
		                                                        	$tarikh = $r["tarikh"];
		                                                	$tarikh = date("Y-m-d",strtotime($tarikh));
		                                        	        $shomare_sanad = $mxs;
		                                	                if(strtotime($tarikh)!=strtotime(date("Y-m-d")))
		                        	                                $shomare_sanad++;
									$a_moeen = $agency->moeen_id;
									$tmp = new daftar_class($agency->daftar_id);
									$a_kol = $tmp->kol_id;
									if($diff == 0)
									{	
										mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`) values ($shomare_sanad,$a_kol,$a_moeen,'$tarikh',$user_id,1,1,$diff_belit)");
		                                        		        $q = null;
		                                		                mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$a_kol and `moeen_id`=$a_moeen and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=1 and `en`=1 and `mablagh`=$diff_belit",$q);
		                        		                        if($r = mysql_fetch_array($q))
		                		                                        $sanad_sabti_id[] = (int)$r['id'];
		        		                                        for($e=0;$e<count($sanad_sabti_id);$e++)
				                                                {
				                                                        mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$sanad_sabti_id[$e]."') ");
				                                                }
									}
								}
							}
						}
*/
						else if(/*$lother_id != $other_id &&*/ -1 != $other_id)
						{
							$sanad_sabti_id = array();

				                        $mxs = 0;
				                        mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
				                        if($r = mysql_fetch_array($q))
                                				$mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
				                        $q = null;
				                        $tarikh = date("Y-m-d");
				                        mysql_class::ex_sql("SELECT `tarikh` from `sanad` where `shomare_sanad`=$mxs",$q);
				                        if($r = mysql_fetch_array($q))
                                				$tarikh = $r["tarikh"];
				                        $tarikh = date("Y-m-d",strtotime($tarikh));
                                			$shomare_sanad = $mxs;
				                        if(strtotime($tarikh)!=strtotime(date("Y-m-d")))
                                			        $shomare_sanad++;
							if($lother_id != $other_id)
							{
								if($lother_id > 0 && $lother_kol_id > 0)
								{					
				        	                        mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`) values ($shomare_sanad,$lother_kol_id,$lother_id,'$tarikh',$user_id,-1,1,".$reserve->hotel_reserve->m_belit.")");
                        					        $q = null;
					                                mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$lother_kol_id and `moeen_id`=$lother_id and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=-1 and `en`=1 and `mablagh`=".$reserve->hotel_reserve->m_belit,$q);
        	                				        if($r = mysql_fetch_array($q))
				                	                        $sanad_sabti_id[] = (int)$r['id'];
								}
				                                mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`) values ($shomare_sanad,$other_kol_id,$other_id,'$tarikh',$user_id,1,1,$m_belit_sabti)");
        	                			        $q = null;
				                                mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$other_kol_id and `moeen_id`=$other_id and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=1 and `en`=1 and `mablagh`=$m_belit_sabti",$q);
				                                if($r = mysql_fetch_array($q))
                        				                $sanad_sabti_id[] = (int)$r['id'];
							}
							if($m_belit_sabti != $reserve->hotel_reserve->m_belit && $lother_id != $other_id)
							{

								$a_moeen = $agency->moeen_id;
                                                                $tmp = new daftar_class($agency->daftar_id);
                                                                $a_kol = $tmp->kol_id;

								$sig = (($m_belit_sabti>$reserve->hotel_reserve->m_belit)?-1:1);
								mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`) values ($shomare_sanad,$a_kol,$a_moeen,'$tarikh',$user_id,$sig,1,$diff_belit)");
                                                                $q = null;
                                                                mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$a_kol and `moeen_id`=$a_moeen and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=$sig and `en`=1 and `mablagh`=$diff_belit",$q);
                                                                if($r = mysql_fetch_array($q))
                                                                        $sanad_sabti_id[] = (int)$r['id'];
							}
							for($e=0;$e<count($sanad_sabti_id);$e++)
                                                        {
                                                                mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$sanad_sabti_id[$e]."') ");
                                                        }
							if($m_tour<$m_tour_sabti)
                                                        {
                                                                //$sanad_sabti_id = sanadzan_class::newHotelReserveSanad($h_id,$reserve->hotel_reserve->ajans_id,$diff);
								$sanad_sabti_id = sanadzan_class::newTourReserveSanad($h_id,$reserve->hotel_reserve->ajans_id,$other_id,$other_kol_id,$diff,$diff_belit);
                                                        }
                                                        else if($m_tour>$m_tour_sabti)
                                                        {
                                                                //$sanad_sabti_id = sanadzan_class::newHotelRefundSanad($h_id,$reserve->hotel_reserve->ajans_id,$diff,'');
								$sanad_sabti_id = sanadzan_class::newInverseTourReserveSanad($h_id,$reserve->hotel_reserve->ajans_id,$other_id,$other_kol_id,$diff,$diff_belit);
                                                        }
                                                        for($e=0;$e<count($sanad_sabti_id);$e++)
                                                        {
                                                                mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$sanad_sabti_id[$e]."') ");
                                                        }
						}
					}
					
				}
			}
		}
		if($h_id>0 && isset($_REQUEST['reserve_id']) )
		{
			$reserve_id = (int)$_REQUEST['reserve_id'];
			if($reserve = new reserve_class($reserve_id))
			{
				$output='<br/><table border="1" style="border-style:dashed;width:80%;" >';
				$output .='<tr><th>نام و نام خانوادگی سرگروه</th><th>توضیحات</th><th>آژانس رزرو کننده</th><th>مبلغ بلیت</th><th>مبلغ تور/ هتل</th></tr>';
				$output .= "<tr><td><input class='inp' type='text' name='lname' id='lname' value='".$reserve->hotel_reserve->lname."' ></td>";
				$output .= "<td><input class='inp' type='text' name='toz' id='toz' value='".$reserve->hotel_reserve->tozih."' ></td>";
				$hotel_sabti = new hotel_class($h_id);
				$agency = new ajans_class($reserve->hotel_reserve->ajans_id);
				$daftar_reserve = new daftar_class($agency->daftar_id);
				$output .= "<td><input class='inp' readonly='readonly' type='text' name='ajans' id='ajans' value='".$agency->name."(".$daftar_reserve->name.")' ><input class='inp' readonly='readonly' type='hidden' name='ajans_id' id='ajans_id' value='".$reserve->hotel_reserve->ajans_id.")' ></td>";
				$output .= "<td><input class='inp' type='text' name='m_belit' id='m_belit' value='".$reserve->hotel_reserve->m_belit."' ></td>";
				$m_tour = 0;
				//-------------mohasebe mablagh tour ----------------
				mysql_class::ex_sql("select sum(`mablagh`*`typ`) as `m_tour` from `sanad` where `id` in (select `sanad_record` from `sanad_reserve` where `reserve_id`=$reserve_id) and `moeen_id` = '".$agency->moeen_id."'",$qqq);
				if($r=mysql_fetch_array($qqq))
					$m_tour = abs($r['m_tour']);
				//-------------------------------------------------------
				$output .= "<td><input class='inp' type='text' name='m_tour' id='m_tour' value='$m_tour' ></td>";
				$output .= '<tr><th>نوع اتاق</th><th>تعداد نفرات</th><th>تعداد اتاق درخواستی</th><th>تاریخ‌ورود</th><th>تاریخ‌خروج</th></tr>';
				$output .= '<tr>';
				$output .= "<td>".loadRoomTyp($h_id,$reserve->room_det[0]->room_typ)."</td>";
				$output .= "<td><input class='inp' type='text' name='tedad_nafarat' id='tedad_nafarat' value='".$reserve->room_det[0]->nafar."' ></td>";
				$output .= "<td><input class='inp' type='text' name='tedad_otagh' id='tedad_otagh' value='".count($reserve->room_det)."' ></td>";
				$output .= "<td><input class='inp' type='text' name='aztarikh' id='aztarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->aztarikh)."' ></td>";
				$output .= "<td><input class='inp' type='text' name='tatarikh' id='tatarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->tatarikh)."' ></td>";
				$output .= '</tr>';
				$output .= '<tr>';
				$output .= '<td colspan="5"> ';
				$output .='<table width="100%" ><tr><th>انتخاب حساب کل بلیت</th><th>انتخاب حساب معین بلیت</th><th>شب-رزرو(نیم شارژ ورودی)</th><th>روز-رزرو(نیم‌شارژ خروجی)</th></tr>';

				//----------------------load kardane daftar va ajans ghabli-------
				$lother_id = -1;
				$lother_kol_id = -1;
				$q = null;
				mysql_class::ex_sql('select `moeen_id`,`kol_id` from `sanad` where `id` in (select `sanad_record` from `sanad_reserve` where `reserve_id`='.$reserve_id.') and `moeen_id` <> '.$hotel_sabti->moeen_id.' and `moeen_id` <> '.$agency->moeen_id.' order by `id` desc limit 1',$q);
				if($r = mysql_fetch_array($q))
				{
					$lother_id = $r['moeen_id'];
					$lother_kol_id = $r['kol_id'];
				}
				$daftar_ghabli = new daftar_class;
				$daftar_ghabli->loadByKol($lother_kol_id);
				$ajans_ghabli = new ajans_class;
				$ajans_ghabli->loadByMoeen($lother_id);
				//-------------------------------------------------
				$req_daftar_id = ((isset($_REQUEST['daftar_idBelit']) && (int)$_REQUEST['daftar_idBelit']>0)?$_REQUEST['daftar_idBelit']:$daftar_ghabli->id);
				$sel_aj = ((isset($_REQUEST['ajans_idBelit']) && (int)$_REQUEST['ajans_idBelit']>0)?$_REQUEST['ajans_idBelit']:$ajans_ghabli->id);
				$output .="<tr><td>".loadDaftarBelit($req_daftar_id)."</td><td>".loadAjansBelit($req_daftar_id,$sel_aj)."</td>";
				$output .="<td><input type='checkbox' name='shab_reserve' id='shab_reserve' ".loadShabReserve($reserve->room_det[0]->aztarikh)." > </td>";
				$output .="<td><input type='checkbox' name='rooz_reserve' id='rooz_reserve' ".loadRoozReserve($reserve->room_det[0]->tatarikh)." > </td></tr>";
				$output .='<tr><th colspan="5" style="border-style:dashed;border-width:1px;" >خدمات</th></tr>'.loadKhadamat($h_id,$reserve->khadamat_det);
				$output .='</table>';
				$output .= "</td>";
				$output .= '</tr>';
				$output .= "<tr><td colspan='5' ><input class='inp' type='button' value='ثبت' onclick='submit_frm();' ></td></tr>";
				$output .= '</table>';
			}
			else
			{
				$output .= "چنین شماره رزروی وجود ندارد";
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
				if(document.getElementById('daftar_idBelit'))
                                        document.getElementById('daftar_idBelit').selectedIndex = -1;
                                if(document.getElementById('ajans_idBelit'))
                                        document.getElementById('ajans_idBelit').selectedIndex = -1;
				document.getElementById('mod').value=1;
				document.getElementById('frm1').submit();
			}
			function send_reserve(reserve_id)
			{
				if(document.getElementById('daftar_idBelit'))
					document.getElementById('daftar_idBelit').selectedIndex = -1;
				if(document.getElementById('ajans_idBelit'))
					document.getElementById('ajans_idBelit').selectedIndex = -1;
				document.getElementById('reserve_id').value=reserve_id;
				document.getElementById('mod').value=1;
				document.getElementById('frm1').submit();	
			}
			function submit_frm()
			{
				document.getElementById("mod").value = 301;
				document.getElementById("frm1").submit();
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
