<?php
	session_start();
	include("../kernel.php");
	include_once('../simplejson.php');
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = $se->detailAuth('all_sabt');
	$isPast = FALSE;
	$msg_done='';
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$out = '<select name="hotel_id" class="form-control inp" style="width:auto;" >';
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
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = $se->detailAuth('all');
		$out = "<select name=\"daftar_id\" id=\"daftar_id\" class=\"form-control inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
		if($isAdmin)
			mysql_class::ex_sql('select `id`,`name` from `daftar` order by `name` ',$q);
		else
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
		$selected = 'selected="selected"';
		$reserve = new reserve_class((int)$_REQUEST['s_reserve_id']);
		$str = "m_belit$typ";
		if($reserve->hotel_reserve->$str==0)
			;	
		$user = new user_class((int)$_SESSION['user_id']);
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = $se->detailAuth('all');
		$out = "<select name=\"daftar_idBelit_$typ\" id=\"daftar_idBelit_$typ\" class=\"form-control inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
		if($isAdmin)
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `kol_id` > 0 order by `name` ',$q);
		else
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `id`='.$user->daftar_id.' and `kol_id` > 0 order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?$selected:'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;	
	}
	function loadDaftarBelitMirror($inp,$typ)
	{
		$inp = (int)$inp;
		$selected = 'selected="selected"';
		$reserve = new reserve_class((int)$_REQUEST['s_reserve_id']);
		$str = "m_belit$typ";
		if($reserve->hotel_reserve->$str==0)
			;
		$user = new user_class((int)$_SESSION['user_id']);
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = $se->detailAuth('all');
		$out_mirror = "<select  id=\"mirror_daftar_idBelit_$typ\"  style=\"display:none;\" ><option value='0' ></option>";
		if($isAdmin)
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `kol_id` > 0 order by `name` ',$q);
		else
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `id`='.$user->daftar_id.' and `kol_id` > 0 order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?$selected:'');
			$out_mirror.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out_mirror.='</select>';
		return $out_mirror;	
	}
	function loadAjans($daftar_id=-1)
	{
		$conf = new conf;
		$daftar_id = (int)$daftar_id;
		$out = "<select name=\"ajans_id\" class=\"form-control inp\" style=\"width:auto;\"  ><option value='0' ></option>";
		$shart_saghf = '';
		if($conf->ajans_saghf_mande ===TRUE)
			$shart_saghf = "and `saghf_kharid`>=".$conf->min_saghf_kharid;	
		mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 $shart_saghf order by `name`",$q);
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
		$conf = new conf;
		$daftar_id = (int)$daftar_id;
		$selected = 'selected="selected"';
		$reserve = new reserve_class((int)$_REQUEST['s_reserve_id']);
		$str = "m_belit$typ";
		if($reserve->hotel_reserve->$str==0)
			;
		$sel_aj = (int)$sel_aj;
		$out = "<select id='ajans_idBelit_$typ' name=\"ajans_idBelit_$typ\" class=\"form-control inp\" style=\"width:auto;\"  >";
		$shart_saghf = '';
		if($conf->ajans_saghf_mande ===TRUE)
			$shart_saghf = "and `saghf_kharid`>=".$conf->min_saghf_kharid;
		mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 $shart_saghf order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$sel_aj)?$selected:'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadAjansBelitMirror($daftar_id,$sel_aj,$typ)
	{
		$conf = new conf;
		$daftar_id = (int)$daftar_id;
		$selected = 'selected="selected"';
		$reserve = new reserve_class((int)$_REQUEST['s_reserve_id']);
		$str = "m_belit$typ";
		if($reserve->hotel_reserve->$str==0)
			;
		$sel_aj = (int)$sel_aj;
		$out_mirror = "<select id='mirror_ajans_idBelit_$typ'  style=\"display:none;\"  >";
		$shart_saghf = '';
		if($conf->ajans_saghf_mande ===TRUE)
			$shart_saghf = "and `saghf_kharid`>=".$conf->min_saghf_kharid;
		mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 $shart_saghf order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$sel_aj)?$selected:'');
			$out_mirror.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out_mirror.='</select>';
		return $out_mirror;
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
		$out = "<select name=\"room_typ_id\" class=\"form-control inp\" style=\"width:auto;\"  >";
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
	function loadKhadamat($h_id,$khadamat,$res=-1)
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
//echo $khad[$i]['id'].','.$khad[$i]['typ'].','.$khadamat[$j]['voroodi'].'<br/>';
					$khadamat_found = TRUE;
					$selected = TRUE;
					$tedad = $khadamat[$j]['count'];
					$jtick = '';
					if($khad[$i]['typ']==1)
						$jtick = "onclick='kh_check(\"".$khad[$i]['id']."\")'";
					if($khad[$i]['voroodi'])
					{
						if($khad[$i]['typ']==1)
						{
							//echo "ورودی $jtick  type='checkbox' id='khadamat_v_".$khadamat[$j]['khadamat_id']."' name='khadamat_v_".$khadamat[$j]['khadamat_id']."' ".(($khadamat[$j]['voroodi'])?'checked="checked"':'')." >"."<br/>";
							$voroodi = "ورودی<input $jtick  type='checkbox' id='khadamat_v_".$khadamat[$j]['khadamat_id']."' name='khadamat_v_".$khadamat[$j]['khadamat_id']."' ".(($khadamat[$j]['voroodi'])?'checked="checked"':'')." ><input $jtick  type='checkbox' id='mirror_khadamat_v_".$khadamat[$j]['khadamat_id']."'  ".(($khadamat[$j]['voroodi'])?'checked="checked"':'')." style='display:none;' >";
						}
						else
							$voroodi = "اول<input $jtick  type='checkbox' id='khadamat_v_".$khadamat[$j]['khadamat_id']."' name='khadamat_v_".$khadamat[$j]['khadamat_id']."' ".(($khadamat[$j]['voroodi'])?'checked="checked"':'')." ><input $jtick  type='checkbox' id='mirror_khadamat_v_".$khadamat[$j]['khadamat_id']."'  ".(($khadamat[$j]['voroodi'])?'checked="checked"':'')." style='display:none;' >";
					}
					if($khad[$i]['khorooji'])
					{
						if($khad[$i]['typ']==1)
							$khorooji = "خروجی<input $jtick  type='checkbox' id='khadamat_kh_".$khadamat[$j]['khadamat_id']."' name='khadamat_kh_".$khadamat[$j]['khadamat_id']."' ".(($khadamat[$j]['khorooji'])?'checked="checked"':'')." ><input $jtick  type='checkbox' id='mirror_khadamat_kh_".$khadamat[$j]['khadamat_id']."' ".(($khadamat[$j]['khorooji'])?'checked="checked"':'')." style='display:none;' >";
						else
							$khorooji = "آخر<input $jtick  type='checkbox' id='khadamat_kh_".$khadamat[$j]['khadamat_id']."' name='khadamat_kh_".$khadamat[$j]['khadamat_id']."' ".(($khadamat[$j]['khorooji'])?'checked="checked"':'')." ><input $jtick  type='checkbox' id='mirror_khadamat_kh_".$khadamat[$j]['khadamat_id']."' ".(($khadamat[$j]['khorooji'])?'checked="checked"':'')." style='display:none;' >";
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
					if($khad[$i]['typ']==1)
						$voroodi = "ورودی<input $jtick  type='checkbox' id='khadamat_v_".$khad[$i]['id']."' name='khadamat_v_".$khad[$i]['id']."'  ><input $jtick  type='checkbox' id='mirror_khadamat_v_".$khad[$i]['id']."' style='display:none;'  >";
					else
						$voroodi = "اول<input $jtick  type='checkbox' id='khadamat_v_".$khad[$i]['id']."' name='khadamat_v_".$khad[$i]['id']."'  ><input $jtick  type='checkbox' id='mirror_khadamat_v_".$khad[$i]['id']."' style='display:none;'  >";
				}
				if($khad[$i]['khorooji'])
				{
					if($khad[$i]['typ']==1)
						$khorooji = "خروجی<input $jtick  type='checkbox' id='khadamat_kh_".$khad[$i]['id']."' name='khadamat_kh_".$khad[$i]['id']."' ><input $jtick  type='checkbox' id='mirror_khadamat_kh_".$khad[$i]['id']."' style='display:none;' >";
					else
						$khorooji = "آخر<input $jtick  type='checkbox' id='khadamat_kh_".$khad[$i]['id']."' name='khadamat_kh_".$khad[$i]['id']."' ><input $jtick  type='checkbox' id='mirror_khadamat_kh_".$khad[$i]['id']."' style='display:none;' >";
				}
			}
			$inp = $khad[$i]['name']." <input style='display:none;' id='khadamat_id_".$khad[$i]['id']."' name='khadamat_id_".$khad[$i]['id']."' type='checkbox' value='1' ".(($selected)?'checked="checked"':'')." ><input style='display:none;' id='mirror_khadamat_id_".$khad[$i]['id']."'  type='checkbox' value='1' ".(($selected)?'checked="checked"':'')." >";
			if($khad[$i]['typ']==0)
			{
				$inp = $khad[$i]['name']." روزانه<input class='form-control inp' type='text' name='khadamat_id_". $khad[$i]['id']."' id='khadamat_id_". $khad[$i]['id']."' value='$tedad' ><input  style='display:none;' type='text' id='mirror_khadamat_id_". $khad[$i]['id']."' value='$tedad' >";
			}
			$gahst_tarikh = '';
			$axe_tarikh = '';
			$kh_id = $khad[$i]['id'];
		/*	mysql_class::ex_sql("select `gashtAst`,`axeAst` from `khadamat` where `id` = '$kh_id'",$p);
			if($rp = mysql_fetch_array($p))
			{
				if($rp['gashtAst']==1)
				{
					mysql_class::ex_sql("select `tarikh` from `khadamat_det` where `khadamat_id` = '$kh_id' and `reserve_id` = '$res'",$qp);
					if ($rop = mysql_fetch_array($qp))
					{
						$ta = audit_class::hamed_pdate(date("Y-m-d",strtotime($rop['tarikh'])));			
						$gahst_tarikh = "<input value='$ta' type='text' name='ta_gasht' class='inp' style='direction:ltr;' id='datepicker7' />";
					}
				}
				else
					$gahst_tarikh = "";
			}
			mysql_class::ex_sql("select `axeAst` from `khadamat` where `id` = '$kh_id'",$p_axe);
			if($rp_axe = mysql_fetch_array($p_axe))
			{
				if($rp_axe['axeAst']==1)
				{
					mysql_class::ex_sql("select `tarikh` from `khadamat_det` where `khadamat_id` = '$kh_id' and `reserve_id` = '$res'",$qp);
					if ($rop = mysql_fetch_array($qp))
					{
						$ta_axe = audit_class::hamed_pdate(date("Y-m-d",strtotime($rop['tarikh'])));			
						$axe_tarikh = "<input value='$ta_axe' type='text' name='ta_axe' class='inp' style='direction:ltr;' id='datepicker8' />";
					}
				}
				else
					$axe_tarikh = "";
			}
			if($i%2 == 0)
				$out .= "<tr><td colspan='2' style='border-style:solid;border-width:1px;' >$inp $voroodi $khorooji $gahst_tarikh $axe_tarikh</td>";
			else
				$out .= "<td colspan='2' style='border-style:solid;border-width:1px;' >$inp $voroodi $khorooji $gahst_tarikh $axe_tarikh</td></tr>";*/
			if($i%2 == 0)
				$out .= "<tr><td colspan='2' style='border-style:solid;border-width:1px;' >$inp $voroodi $khorooji</td>";
			else
				$out .= "<td colspan='2' style='border-style:solid;border-width:1px;' >$inp $voroodi $khorooji </td></tr>";
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
	$show_sabt = (($se->detailAuth('all_sabt'))?'':'disabled="disabled"');
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
		if($isAdmin )
		{
			$daftar_id = ((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
			$daftar_idBelit = ((isset($_REQUEST['daftar_idBelit']))?(int)$_REQUEST['daftar_idBelit']:-1);
		}
		else
		{
			$daftar_id = (int)$_SESSION["daftar_id"] ;
			$daftar_idBelit = (int)$_SESSION["daftar_id"] ;
		}
		if($h_id>0 && isset($_REQUEST['reserve_id']) && $_REQUEST['mod']==301 )
		{
			$reserve_id = (int)$_REQUEST['reserve_id'];
			$reserve = new reserve_class($reserve_id);
                        $h_id = $reserve->hotel_id;
			$agency = new ajans_class($reserve->hotel_reserve->ajans_id);
			$daftar_reserve = new daftar_class($agency->daftar_id);
			$lname = ((isset($_REQUEST['lname']))?$_REQUEST['lname']:'');
			
			$toz = ((isset($_REQUEST['toz']))?$_REQUEST['toz']:'');
			$extra_toz = ((isset($_REQUEST['extra_toz']))?$_REQUEST['extra_toz']:'');
			$m_belit_sabti_1 = ((isset($_REQUEST['m_belit_1']))?audit_class::perToEn($_REQUEST['m_belit_1']):0);
			$m_belit_sabti_2 = ((isset($_REQUEST['m_belit_2']))?audit_class::perToEn($_REQUEST['m_belit_2']):0);
			$m_belit_sabti_3 = ((isset($_REQUEST['m_belit_3']))?audit_class::perToEn($_REQUEST['m_belit_3']):0);
			$m_hotel_sabti = ((isset($_REQUEST['m_hotel']))?audit_class::perToEn($_REQUEST['m_hotel']):0);
			$room_typ_id = ((isset($_REQUEST['room_typ_id']))?$_REQUEST['room_typ_id']:-1);
			$tedad_nafarat = ((isset($_REQUEST['tedad_nafarat']))?$_REQUEST['tedad_nafarat']:-1);
			//$tedad_otagh = ((isset($_REQUEST['tedad_otagh']))?$_REQUEST['tedad_otagh']:0);
			$tedad_otagh = ((isset($_REQUEST['tedad_otagh']))?(int)audit_class::perToEn($_REQUEST['tedad_otagh']):0);
			foreach($_REQUEST as $key=>$value)
			{
				$tmp = explode('_',$key);
				if($tmp[0]=='otagh')
					$room_ids[] = (int)$tmp[1];

			}
			if($tedad_otagh==0)
				$tedad_otagh = ($room_ids);
			$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):'');
			$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):'');
			$shab_reserve = ((isset($_REQUEST['shab_reserve']))?TRUE:FALSE);
			$rooz_reserve = ((isset($_REQUEST['rooz_reserve']))?TRUE:FALSE);
			$daftar_idbelit_1 = ((isset($_REQUEST['daftar_idBelit_1']))?$_REQUEST['daftar_idBelit_1']:-1);
			$ajans_idbelit_1 = ((isset($_REQUEST['ajans_idBelit_1']))?$_REQUEST['ajans_idBelit_1']:-1);
			$daftar_idbelit_2 = ((isset($_REQUEST['daftar_idBelit_2']))?$_REQUEST['daftar_idBelit_2']:-1);
			$ajans_idbelit_2 = ((isset($_REQUEST['ajans_idBelit_2']))?$_REQUEST['ajans_idBelit_2']:-1);
			$daftar_idbelit_3 = ((isset($_REQUEST['daftar_idBelit_3']))?$_REQUEST['daftar_idBelit_3']:-1);
			$ajans_idbelit_3 = ((isset($_REQUEST['ajans_idBelit_3']))?$_REQUEST['ajans_idBelit_3']:-1);
			$shab = audit_class::upint((strtotime($tatarikh) - strtotime($aztarikh))/(24*60*60)) ;
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
			if(($m_belit_sabti_1 + $m_belit_sabti_2 + $m_belit_sabti_3)!=0)
			{
				$ghimat = array();
				$ghimat['ghimat_tour'] = $m_belit_sabti_1 +$m_belit_sabti_2+ $m_belit_sabti_3 + $m_hotel_sabti;
				$ghimat['ghimat_belit1'] =  $m_belit_sabti_1;
				$ajans_belit_1 = new ajans_class($ajans_idbelit_1) ; 
				$ghimat['other_moeen_id1'] =(int)$ajans_belit_1->moeen_id;

				$daftar_belit_1 = new daftar_class($daftar_idbelit_1);
				$ghimat['other_kol_id1'] =$daftar_belit_1->kol_id;
				$ghimat['ghimat_belit2'] = $m_belit_sabti_2;
				$ajans_belit_2 = new ajans_class($ajans_idbelit_2) ; 
				$ghimat['other_moeen_id2'] =(int)$ajans_belit_2->moeen_id;
				$daftar_belit_2 = new daftar_class($daftar_idbelit_2);
				$ghimat['other_kol_id2'] =$daftar_belit_2->kol_id;

				$ghimat['ghimat_belit3'] = $m_belit_sabti_3;
				$ajans_belit_3 = new ajans_class($ajans_idbelit_3) ; 
				$ghimat['other_moeen_id3'] =(int)$ajans_belit_3->moeen_id;
				$daftar_belit_3 = new daftar_class($daftar_idbelit_3);
				$ghimat['other_kol_id3'] =$daftar_belit_3->kol_id;
			}
			else
			{
				$ghimat= $m_hotel_sabti; 
			}
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
			$rooms_arr = room_class::loadOpenRooms($aztarikh,$shab,$shab_reserve,$rooz_reserve,$h_id,$_SESSION['daftar_id']);
			$reserveid_shomaresanad=FALSE;
/*$ta_gasht = ((isset($_REQUEST['ta_gasht']))?audit_class::hamed_pdateBack($_REQUEST['ta_gasht']):'');
$tmp_gasht = explode(' ',$ta_gasht);
$ta_axe = ((isset($_REQUEST['ta_axe']))?audit_class::hamed_pdateBack($_REQUEST['ta_axe']):'');
$tmp_axe = explode(' ',$ta_axe);*/
			$reserveid_shomaresanad = room_det_class::reReserve($reserve_id,$h_id,$reserve->hotel_reserve->ajans_id,$tmp_room,$ghimat,$aztarikh,$shab,$tedad_otagh,$shab_reserve,$rooz_reserve,$tedad_nafarat,$khadamat_sabti);
			if($reserveid_shomaresanad===FALSE)
				$msg_done = 'ثبت با موفقیت انجام نشد ، هتل فعال نمی باشد و یا جابجایی اتاق مقدور نیست';
			else
			{
				//------a تغییر رنگ برروی رک در واقع تغییر وضعیت اتاق
				$befor_rooms[]=array();
				foreach($reserve->room as $room_id_t)
				{
					$befor_rooms[]=$room_id_t->id;
					if(!in_array($room_id_t->id,$tmp_room))
					{
						$r_id = $room_id_t->id;
						mysql_class::ex_sql("select `vaziat` from `room` where `id`= $r_id",$q_room);
						if($r_room = mysql_fetch_array($q_room))
						{
							$vaziat = $r_room["vaziat"];
							if(reserve_class::isPaziresh($reserve_id))
							{
								if ($vaziat=='2')				
									room_class::setVaziat($r_id,2);
								else
									room_class::setVaziat($r_id,1);
							}
						}
					}
						//room_class::setVaziat($room_id_t->id,1);
				}
				/*
				//----a اتاق هایی که جدید اضافه می شوند به حالت اشغال 
				foreach($tmp_room as $rt_id)
					if(!in_array($rt_id,$befor_rooms))
						room_class::setVaziat($rt_id,0);
				//------------------------------------------------
				*/
				$toz_arr = $toz;
				if($extra_toz!='')
				{
					$toz_arr = null;
					$toz_arr['toz'] = $toz;
					$toz_arr['extra_toz'] = $extra_toz;
				}
				room_det_class::sabtReserveHotel($reserve_id,$reserveid_shomaresanad['shomare_sanad'],$ghimat,'',$lname,$toz_arr,$reserve->hotel_reserve->ajans_id,$m_hotel_sabti,$reserve->hotel_reserve->regdat,TRUE);
				for($l=0;$l<count($reserveid_shomaresanad['shomare_sanad']);$l++)
				{
					$tozih_sabti = room_det_class::loadReserve($reserveid_shomaresanad['reserve_id']);
					mysql_class::ex_sqlx("update `sanad` set `tozihat`='اصلاحیه $tozih_sabti' where `id`=".$reserveid_shomaresanad['shomare_sanad'][$l]);
				}
				$msg_done = 'ثبت با موفقیت انجام شد';
			}
		}
		if($h_id>0 && isset($_REQUEST['s_reserve_id']) )
		{
			$reserve_id = (int)$_REQUEST['s_reserve_id'];
			/*$reserve_garanty = FALSE;
			$reserve_daftar_id = hotel_reserve_class::loadDaftarByReserveId($reserve_id);
			if ($_SESSION['daftar_id']!=$reserve_daftar_id)
			{
				$tabaghes = hotel_garanti_class::loadGarantyDaftar($_SESSION['daftar_id']);
				$room_res = room_det_class::loadRoomByReserve($reserve_id);
				foreach ($room_res as $i)
				{
					$tabagheId = room_class::loadTabagheByRoomId($i);
					$garantiOtagh = hotel_garanti_class::loadTabagheGaranty($tabagheId);
//echo $tabagheId.'<br/>';
					$res_garanti = !((in_array($tabagheId,$tabaghes) && count($tabaghes)>0) || (count($tabaghes)==0));
					$reserve_garanty = ($res_garanti || $garantiOtagh);
				}
			}*/
			if (!($se->detailAuth('admin')))
				$reserve_garanty = hotel_garanti_class::canViewReserve($reserve_id);
			else
				$reserve_garanty = FALSE;
			if (!($reserve_garanty))
			{
				$c_user = new user_class((int)$_SESSION['user_id']);
				$reserve = new reserve_class($reserve_id);
				$disable = (($reserve->editable)?'':'onclick="return false" onkeydown="return false"');
				$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):'');
				$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):'');
				$shab = audit_class::upint((strtotime($tatarikh) - strtotime($aztarikh))/(24*60*60)) ;
				$h_id = $reserve->hotel_id;
				$bool = $reserve->out;
				if($bool!==FALSE)
				{
					$agency = new ajans_class($reserve->hotel_reserve->ajans_id);
					$daftar_reserve = new daftar_class($agency->daftar_id);
					if(!$isAdmin && ((isset($reserve->room_det[0]->aztarikh) && date("Y-m-d") <= date("Y-m-d",strtotime($reserve->room_det[0]->aztarikh." - 1 day"))) || (strtotime(date("Y-m-d H:i:s")." - 3 day")>strtotime($reserve->hotel_reserve->regdat))))
						$show_sabt = '';
					$tmp_aj = new ajans_class($reserve->hotel_reserve->ajans_id);
					//if($isAdmin && (date("Y-m-d") > date("Y-m-d",strtotime($reserve->room_det[0]->aztarikh))) )
						//$show_sabt = 'disabled="disabled"';
					if($agency->daftar_id!=$c_user->daftar_id && !$isAdmin)
						$bool = FALSE;
					$grop = new grop_class($c_user->typ);
					if($agency->daftar_id==$c_user->daftar_id && $grop->name == 'هتلدار' && date("Y-m-d H:i:s") <= date("Y-m-d H:i:s",strtotime($reserve->room_det[0]->tatarikh)))
						$show_sabt = '';
				}
//echo $show_sabt;
				if($bool!==FALSE)
				{
					if($conf->tour_enabled)
					{
						$tour_mab_view = 'مبلغ تور:';
						$raft_sherkat = 'شرکت بلیت رفت';
						$m_belit_view = 'مبلغ بلیت';
						$m_belit1_view = '  رفــــت:';
						$m_belit2_view = 'برگـشـت:';
						$m_belit3_view = 'کمیسیون:';
						$m_belit2_style = '';
					}
					else
					{
						$tour_mab_view = 'مبلغ کل هتل:';
						$raft_sherkat = 'حساب کمیسیون';
						$m_belit_view = 'کمیسیون';
						$m_belit1_view = '';
						$m_belit2_view = '';
						$m_belit3_view = '';
						$m_belit2_style = 'style="display:none;"';
					}
					$output='<br/><input type="hidden" name="reserve_id" id="reserve_id" value="'.$reserve_id.'" >
                    
                    
                    
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">نام و نام خانوادگی سرگروه</th>
												<th style="text-align:right">تلفن</th>
												<th style="text-align:right">آژانس رزرو کننده</th>
                                                <th style="text-align:right">'.$m_belit_view.'</th>
                                                <th style="text-align:right">مبلغ هتل</th>
											  </tr>
                                              </thead>
                                            <tbody>';
                    
					$output .= "<tr><td><input class='form-control inp' type='text' name='lname' id='lname' value='".$reserve->hotel_reserve->lname."' ><input style='display:none;' type='text' id='mirror_lname' value='".$reserve->hotel_reserve->lname."' ></td>";
					$output .= "<td><input class='form-control inp' type='text' name='toz' id='toz' value='".$reserve->hotel_reserve->tozih."' ><input style='display:none;' type='text' id='mirror_toz' value='".$reserve->hotel_reserve->tozih."' ></td>";
					$hotel_sabti = new hotel_class($h_id);
					$agency = new ajans_class($reserve->hotel_reserve->ajans_id);
					$daftar_reserve = new daftar_class($agency->daftar_id);
					$output .= "<td><input readonly='readonly' type='text' name='ajans' id='ajans' value='".$agency->name."(".$daftar_reserve->name.")'  class=\"form-control new_inp\" /><input class='inp' readonly='readonly' type='hidden' name='ajans_id' id='ajans_id' value='".$reserve->hotel_reserve->ajans_id.")' ></td>";
					$ghimat_disable =  ($reserve->isOnline)?'readonly="readonly"':'';
					$output .= "<td>$m_belit1_view<input $ghimat_disable class='form-control inp' type='text' name='m_belit_1' id='m_belit_1' value='".$reserve->hotel_reserve->m_belit1."' onblur=\"checkNumber(this);\" ><input  style='display:none;' type='text' id='mirror_m_belit_1' value='".$reserve->hotel_reserve->m_belit1."' >
					$m_belit2_view<input $m_belit2_style $ghimat_disable class='inp' type='text' name='m_belit_2' id='m_belit_2' value='".$reserve->hotel_reserve->m_belit2."' onblur=\"checkNumber(this);\" >
					<input style='display:none;' type='text' id='mirror_m_belit_2' value='".$reserve->hotel_reserve->m_belit2."' >
					$m_belit3_view<input $m_belit2_style $ghimat_disable class='inp' type='text' name='m_belit_3' id='m_belit_3' value='".$reserve->hotel_reserve->m_belit3."' onblur=\"checkNumber(this);\" >
					<input style='display:none;' type='text' id='mirror_m_belit_3' value='".$reserve->hotel_reserve->m_belit3."' >
					</td>";
					$m_hotel = $reserve->hotel_reserve->m_hotel;
					$output .= "<td><input $ghimat_disable class='form-control inp' type='text' onblur=\"checkNumber(this);\" name='m_hotel' id='m_hotel' value='$m_hotel' ><input style='display:none;' type='text' id='mirror_m_hotel' value='$m_hotel' >";
					if($reserve->hotel_reserve->sms_ghimat==-2)
						$sms_ghimat= '<b>پیامک فرستاده‌نشده</b>';
					else if($reserve->hotel_reserve->sms_ghimat==-1)
						$sms_ghimat='<b>پاسخ داده نشده</b>';
					else if($reserve->hotel_reserve->sms_ghimat>1000)
						$sms_ghimat=monize($reserve->hotel_reserve->sms_ghimat);
					else
						$sms_ghimat = '<b>مشخص نیست</b>';
					$output.="<tr><td colspan='5' ><b>توضیحات:</b><input class='form-control inp' style='width:80%' type='text' name='extra_toz' id='extra_toz' value='".$reserve->hotel_reserve->extra_toz."' ><input style='display:none;' type='text' id='mirror_extra_toz' value='".$reserve->hotel_reserve->extra_toz."' ></td></tr>";
					$output.="مبلغ پیامک شده:$sms_ghimat</td></tr>";
					if(!$conf->room_select)
					{
						$output .= '<tr><th>نوع اتاق</th><th>تعداد نفرات</th><th>تعداد اتاق درخواستی</th><th>تاریخ‌ورود</th><th>تاریخ‌خروج</th></tr>'."\n";
						$output .= '<tr>';
						$output .= "<td>".loadRoomTyp($h_id,$reserve->room_det[0]->room_typ)."</td>";
						$naf = 0;
						foreach($reserve->room_det as $rrrr)
						{
							$naf += $rrrr->nafar;
						}
						//$naf = $reserve->room_det[0]->nafar;
						$output .= "<td><input class='form-control inp' disbaled type='text' name='tedad_nafarat' id='tedad_nafarat' value='".$naf."' ></td>";
						$output .= "<td><input class='form-control inp' type='text' name='tedad_otagh' id='tedad_otagh' value='".count($reserve->room_det)."' ></td>";
						$output .= "<td><input class='form-control inp' type='text' name='aztarikh' id='aztarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->aztarikh)."'  onblur='mehrdad_pdate(this);' ></td>";
						$output .= "<td><input class='form-control inp' type='text' name='tatarikh' id='tatarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->tatarikh)."' onblur='mehrdad_pdate(this);' ></td>";
						$output .= '</tr>';
					}
					else
					{
						$rooms = room_class::loadRooms($h_id,$reserve_id,$_SESSION['daftar_id']);
						$outRoom = '<table border="1" style="width:100%" >';
						$tmp_check='';
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
								$outRoom .= $shomare_otagh->name."<input type='checkbox' $check_box name='otagh_".$room_numbers[$j]."' id='otagh_".$room_numbers[$j]."' value='".$room_numbers[$j]."' $disable onclick=\"addNumber(this,".$room_numbers[$j].");\" ><input $disable style='display:none'  type='checkbox' $check_box id='mirror_otagh_".$room_numbers[$j]."' value='".$room_numbers[$j]."' >&nbsp;&nbsp;&nbsp;";
								$tmp_check .= (($check_box=='checked="checked"')?(($tmp_check!='')?',':'').$room_numbers[$j]:'');
							
							}
							$output .= "</td>\n</tr>";
						}
						$outRoom .= "</tbody></table>";
						$output .= "<tr><th colspan='5' style='border-style:dashed;border-width:1px;text-align:center;' > اتاق</th></tr><tr><td colspan='5' style='width:80%'  >$outRoom</td></tr>";
						$output .= '<tr><th style="text-align:right;">تعداد نفرات</th><th style="text-align:right;" colspan="2" >تاریخ‌ورود</th><th colspan="2" style="text-align:right;" >تاریخ‌خروج</th></tr>';
						$output .= '<tr>';
						$naf = 0;
						foreach($reserve->room_det as $rrrr)
						{
							$naf += $rrrr->nafar;
						}
						//$naf = $reserve->room_det[0]->nafar;
						//$output .= "<td><input class='inp' disbaled type='text' name='tedad_nafarat' id='tedad_nafarat' value='".$naf."' ></td>";
						$output .= "<td><input class='form-control inp' type='text' name='tedad_nafarat' id='tedad_nafarat' value='".$naf."' ><input style='display:none' type='text' id='mirror_tedad_nafarat' value='".$naf."' ></td>";
						$output .= "<td colspan='2' ><input $disable class='form-control inp' type='text' name='aztarikh' id='aztarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->aztarikh)."' onblur='mehrdad_pdate(this);' ><input style='display:none' type='text' id='mirror_aztarikh' value='".audit_class::hamed_pdate($reserve->room_det[0]->aztarikh)."' ></td>";
						$output .= "<td colspan='2' ><input $disable class='form-control inp' type='text' name='tatarikh' id='tatarikh' value='".audit_class::hamed_pdate($reserve->room_det[count($reserve->room_det)-1]->tatarikh)."' onblur='mehrdad_pdate(this);' ><input style='display:none' type='text' id='mirror_tatarikh' value='".audit_class::hamed_pdate($reserve->room_det[count($reserve->room_det)-1]->tatarikh)."' ></td>";
						$output .= '</tr>';
						if(strtotime($reserve->room_det[count($reserve->room_det)-1]->tatarikh) < strtotime(date("Y-m-d H:i:s")))
							$isPast = TRUE;
					}
					$output .= '<tr>';
					$output .= '<td colspan="5"></tr> ';
					$output .="<table width='100%' ><tr><th style=\"text-align:right;\">انتخاب حساب کل $m_belit_view</th><th style=\"text-align:right;\">انتخاب حساب معین $m_belit_view</th><th style=\"text-align:right;\">شب-رزرو(نیم شارژ ورودی)</th><th style=\"text-align:right;\">روز-رزرو(نیم‌شارژ خروجی)</th></tr>";

					//----------------------load kardane daftar va ajans ghabli-------
					$lother_id = array();
					$lother_kol_id = array();
					$daftar_ghabli = new daftar_class;
					$ajans_ghabli = new ajans_class;
					if(isset($reserve->hotel_reserve->other_id['other_kol_id1']))
					{
						$daftar_ghabli->loadByKol($reserve->hotel_reserve->other_id['other_kol_id1']); 
						$belit_ghabli_tmp['daftar_belit1'] = $daftar_ghabli->id;
					}
					else
						$belit_ghabli_tmp['daftar_belit1'] = -1;
					if(isset($reserve->hotel_reserve->other_id['other_moeen_id1']))
					{
						$ajans_ghabli->loadByMoeen($reserve->hotel_reserve->other_id['other_moeen_id1']);
						$belit_ghabli_tmp['ajans_belit1'] = $ajans_ghabli->id;
					}
					else
						$belit_ghabli_tmp['ajans_belit1'] = -1;
					$daftar_ghabli= null;
					$daftar_ghabli = new daftar_class;
					if(isset($reserve->hotel_reserve->other_id['other_kol_id2']))
					{
						$daftar_ghabli->loadByKol($reserve->hotel_reserve->other_id['other_kol_id2']);
						$belit_ghabli_tmp['daftar_belit2'] =$daftar_ghabli->id;
					}
					else
						$belit_ghabli_tmp['daftar_belit2'] = -1;
					$ajans_ghabli = null;
					$ajans_ghabli = new ajans_class;
					if(isset($reserve->hotel_reserve->other_id['other_moeen_id2']))
					{
					$ajans_ghabli->loadByMoeen($reserve->hotel_reserve->other_id['other_moeen_id2']);
					$belit_ghabli_tmp['ajans_belit2'] =$ajans_ghabli->id;
					}
					else
						$belit_ghabli_tmp['ajans_belit2'] = -1;

					$daftar_ghabli= null;
					$daftar_ghabli = new daftar_class;
					if(isset($reserve->hotel_reserve->other_id['other_kol_id3']))
					{
						$daftar_ghabli->loadByKol($reserve->hotel_reserve->other_id['other_kol_id3']);
						$belit_ghabli_tmp['daftar_belit3'] =$daftar_ghabli->id;
					}
					else
						$belit_ghabli_tmp['daftar_belit3'] = -1;
					$ajans_ghabli = null;
					$ajans_ghabli = new ajans_class;
					if(isset($reserve->hotel_reserve->other_id['other_moeen_id3']))
					{
						$ajans_ghabli->loadByMoeen($reserve->hotel_reserve->other_id['other_moeen_id3']);
						$belit_ghabli_tmp['ajans_belit3'] =$ajans_ghabli->id;
					}
					else
						$belit_ghabli_tmp['ajans_belit3'] = -1;
				//-------------------------------------------------
					$req_daftar_id_1 = ((isset($_REQUEST['daftar_idBelit_1']) && (int)$_REQUEST['daftar_idBelit_1']>=0)?$_REQUEST['daftar_idBelit_1']:$belit_ghabli_tmp['daftar_belit1']);
					$req_daftar_id_2 = ((isset($_REQUEST['daftar_idBelit_2']) && (int)$_REQUEST['daftar_idBelit_2']>=0)?$_REQUEST['daftar_idBelit_2']:$belit_ghabli_tmp['daftar_belit2']);
					$req_daftar_id_3 = ((isset($_REQUEST['daftar_idBelit_3']) && (int)$_REQUEST['daftar_idBelit_3']>=0)?$_REQUEST['daftar_idBelit_3']:$belit_ghabli_tmp['daftar_belit3']);
					$sel_aj_1 = ((isset($_REQUEST['ajans_idBelit_1']) && (int)$_REQUEST['ajans_idBelit_1']>=0)?$_REQUEST['ajans_idBelit_1']:$belit_ghabli_tmp['ajans_belit1']);
					$sel_aj_2 = ((isset($_REQUEST['ajans_idBelit_2']) && (int)$_REQUEST['ajans_idBelit_2']>=0)?$_REQUEST['ajans_idBelit_2']:$belit_ghabli_tmp['ajans_belit2']);
					$sel_aj_3 = ((isset($_REQUEST['ajans_idBelit_3']) && (int)$_REQUEST['ajans_idBelit_3']>=0)?$_REQUEST['ajans_idBelit_3']:$belit_ghabli_tmp['ajans_belit3']);
					$output .="<tr>
							<td>$m_belit1_view ".loadDaftarBelit($req_daftar_id_1,1).loadDaftarBelitMirror($belit_ghabli_tmp['daftar_belit1'],1)."</td>
							<td>".loadAjansBelit($req_daftar_id_1,$sel_aj_1,1).loadAjansBelitMirror($belit_ghabli_tmp['daftar_belit1'],$belit_ghabli_tmp['ajans_belit1'],1)."</td>";

					$output .="<td>
							<input type='checkbox' name='shab_reserve' id='shab_reserve' ".loadShabReserve($reserve->room_det[0]->aztarikh)." >
							<input style='display:none;' type='checkbox' id='mirror_shab_reserve' ".loadShabReserve($reserve->room_det[0]->aztarikh)." > 
						</td>";
					$output .="<td>
							<input type='checkbox' name='rooz_reserve' id='rooz_reserve' ".loadRoozReserve($reserve->room_det[0]->tatarikh)." >
							<input  style='display:none;' type='checkbox' id='mirror_rooz_reserve' ".loadRoozReserve($reserve->room_det[0]->tatarikh)." >
						</td>
						</tr>";
					$output .="<tr $m_belit2_style ><td>برگـشت:".loadDaftarBelit($req_daftar_id_2,2).loadDaftarBelitMirror($belit_ghabli_tmp['daftar_belit2'],2)."</td><td>".loadAjansBelit($req_daftar_id_2,$sel_aj_2,2).loadAjansBelitMirror($belit_ghabli_tmp['daftar_belit2'],$belit_ghabli_tmp['ajans_belit2'],2)."</td>";

					$output .="<td colspan='2' >&nbsp;</td></tr>";
					$output .="<tr>
							<td>$m_belit3_view ".loadDaftarBelit($req_daftar_id_3,3).loadDaftarBelitMirror($belit_ghabli_tmp['daftar_belit3'],3)."</td>
							<td>".loadAjansBelit($req_daftar_id_3,$sel_aj_3,3).loadAjansBelitMirror($belit_ghabli_tmp['daftar_belit3'],$belit_ghabli_tmp['ajans_belit3'],3)."</td>
							<td colspan='2' >&nbsp;</td>
						</tr>";
					$output .='<tr><th colspan="5" style="text-align:center;border-style:dashed;border-width:1px;" >خدمات</th></tr>'.loadKhadamat($h_id,$reserve->khadamat_det,$reserve_id);
					$output .='</tbody></table>';
					$output .= "</td>";
					$output .= '</tr>';
					$output .= "<tr><td colspan='5' ><a style=\"margin:5px;min-width:90px;\" onclick='getAjaxInfo();'  $show_sabt class=\"btn btn-success\"><i class=\"fa fa-check\"></i> ثبت</a><a style=\"margin:5px;min-width:90px;\" onclick='getVocher($reserve_id);' class=\"btn btn-info\"><i class=\"fa fa-print\"></i>چاپ واچر</a><input value='$tmp_check' name='room_id_tmp' id='room_id_tmp' type='hidden' ></td></tr>";
					$output .= '</tbody></table>';
				}
				else
				{
					$output .= "چنین شماره رزروی وجود ندارد یا شما به آن دسترسی ندارید";
				}
			}
			else
				$output .= "شما به این شماره رزرو دسترسی ندارید";
		}
	}
	$outvazeat = '';
	/*
        $hotel1 = new hotel_class($h_id);
        $outvazeat = $hotel1->loadRooms($da,$isAdmin,'send_reserve');
	*/
	if(isset($_REQUEST['json_string']) && isset($_REQUEST['reserve_id']) ) 
		changeLog_class::add((int)$_REQUEST['reserve_id'],(int)$_SESSION['user_id'],$_REQUEST['json_string']);
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>اصلاح کردن رزرو</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
	<meta name="description" content="" />
	<meta name="author" content="" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/cloud-admin.css" />
	<!-- Clock -->
	<link href="<?php echo $root ?>inc/digital-clock/assets/css/style.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?php echo $root ?>js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>js/colorbox/colorbox.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/animatecss/animate.min.css" />
    <!-- DataTables CSS -->
    <link href="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="<?php echo $root ?>datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">
<!-- DataTables JavaScript -->
    <!-- JQUERY -->
<script src="<?php echo $root ?>js/jquery/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $root ?>datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript" src="../js/tavanir.js"></script>
    
      <script>
    
          var isJustRoom = <?php echo ($se->detailAuth('room_edit'))?'true':'false'; ?>;
			var roomTagIds = ['otagh_'];
			var isPast = <?php echo (!$se->detailAuth('all') && $isPast)?'true':'false'; ?>;
			$(document).ready(function(){
                
                 
                
                
                
				if(isJustRoom)
				{
					$("input,select").each(function(id,field){
						for(i in roomTagIds)
						{
							if(String(field.id).indexOf(roomTagIds[i])!=0 && $(field).attr('type')!='button' && field.id != 's_reserve_id' && field.id != 'extra_toz')
							{
								if($(field).is('select') || $(field).is(':checkbox'))
									if($(field).attr('id').split('_')[0] === 'khadamat')// && $(field).attr('readonly')===true)
										//console.log($(field).attr('id'),$(field).attr('readonly'));
										$(field).click(function(){
											return(false);
										});
									//$(field).attr('disabled',true);
								$(field).attr('readonly',true);
							}
						}
					});
				}
				if(isPast)
				{
					$("input,select").each(function(id,field){
						//if($(field).is('select') || $(field).is(':checkbox'))
							//$(field).attr('disabled',true);
						$(field).attr('readonly',true);
					});
				}
				
			});

			function mehrdad_ajaxFunction(func,command,hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id){
				var ajaxRequest;  // The variable that makes Ajax possible!
	
				try{
					// Opera 8.0+, Firefox, Safari
					ajaxRequest = new XMLHttpRequest();
				} catch (e){
					// Internet Explorer Browsers
					try{
						ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try{
							ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
						} catch (e){
							// Something went wrong
							alert("مرورگر شما قابلیت آژاکس را ندارد لطفاً از مرورگر جدیدتر و پیشرفته تری مانند فایرفاکس استفاده کنید");
							return false;
						}
					}
				}
				// Create a function that will receive data sent from the server
				ajaxRequest.onreadystatechange = function(){
					if(ajaxRequest.readyState == 4){
						func(command,ajaxRequest.responseText);
					}
				};
				var queryString = "?r="+Math.random()+"&command="+command+"&hotel_id="+hotel_id+"&test_date="+aztarikh+"&delay="+shab+"&rooms="+room+"&nafar="+nafar+"&daftar_id="+daftar_id+"&ajans_id="+ajans_id+'&';
				//alert(queryString);
				ajaxRequest.open("GET", "ajax_responser.php" + queryString, true);
				ajaxRequest.send(null); 
			}
			var check_otagh = false;
			var check_ispick = false;
			var daftar_id =<?php echo $_SESSION['daftar_id']; ?>;
			var ajans_id= <?php echo (isset($reserve->hotel_reserve->ajans_id) && $reserve->hotel_reserve->ajans_id>0 )?$reserve->hotel_reserve->ajans_id:0; ?>;
			var isPick = false;
			var isAdmin = <?php echo ($se->detailAuth("all") || $se->detailAuth("all_sabt"))?'true':'false'; ?> ;
			function getAjaxInfo()
			{
				if(!isPast)
				{
					check_otagh = false;
					check_ispick = false;
					var hotel_id = "<?php echo $h_id; ?>";
					var aztarikh = "<?php echo ((isset($aztarikh))?$aztarikh:''); ?>" ;
					var shab = "<?php echo ((isset($shab))?$shab:''); ?>" ; 
					var nafar = parseInt(document.getElementById('tedad_nafarat').value,10);
					var room = document.getElementById('room_id_tmp').value;
					if(isAdmin)
					{
						alert('بعلت دسترسی شما به این صفحه به عنوان مدیر هیچ کدام از شروط رزرو بررسی نشد');
						submit_frm();
					}
					else
						mehrdad_ajaxFunction(check_room,'roomcheck',hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id);
				}
				else
					alert('امکان بروزرسانی برای شما نمی باشد');
			}
			function check_room(command,response)
			{
				out = false;
				var hotel_id = "<?php echo $h_id; ?>";
				var aztarikh = "<?php echo ((isset($aztarikh))?$aztarikh:''); ?>" ;
				var shab = "<?php echo ((isset($shab))?$shab:''); ?>" ; 
				var nafar = parseInt(document.getElementById('tedad_nafarat').value,10);
				var room = document.getElementById('room_id_tmp').value;
				if(command=='roomcheck' && trim(response)=='TRUE')
					out = true;
				check_otagh = out;
				mehrdad_ajaxFunction(check_room_pick,'roompick',hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id);
				return out;
			}
			function check_room_pick(command,response)
			{
				out = false;
				var hotel_id = "<?php echo $h_id; ?>";
				var aztarikh = "<?php echo ((isset($aztarikh))?$aztarikh:''); ?>" ;
				var shab = "<?php echo ((isset($shab))?$shab:''); ?>" ; 
				var nafar = parseInt(document.getElementById('tedad_nafarat').value,10);
				var room = document.getElementById('room_id_tmp').value;
				if(command=='roompick' && trim(response)=='TRUE')
					out = true;
				check_roompick = out;
				mehrdad_ajaxFunction(is_pick,'ispick',hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id);
				return out;
			}
			function is_pick(command,response)
			{
				out = false;
				var hotel_id = "<?php echo $h_id; ?>";
				var aztarikh = "<?php echo ((isset($aztarikh))?$aztarikh:''); ?>" ;
				var shab = "<?php echo ((isset($shab))?$shab:''); ?>" ; 
				var nafar = parseInt(document.getElementById('tedad_nafarat').value,10);
				var room = document.getElementById('room_id_tmp').value;
				if(command=='roompick' && trim(response)=='TRUE')
					out = true;
				isPick = out;
				mehrdad_ajaxFunction(check_ghimat,'getghimat',hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id);
				return out;
			}
			function check_ghimat(command,response)
			{
				out = 0;
				var ghimat_az_ghabl = <?php echo (isset($reserve->hotel_reserve->m_hotel) && $reserve->hotel_reserve->m_hotel>0 )?$reserve->hotel_reserve->m_hotel:0;?>;
				var khadamat = checkKhadamat();
				var m_hotel = parseInt(unFixNums(umonize(document.getElementById('m_hotel').value)),10);
				var ghimat = m_hotel-ghimat_az_ghabl;
				response = response.split(',');
				var ghimat_limit = parseInt(trim(response[0]),10);
				var saghf_kharid = (response[1])?parseInt(trim(response[1]),10):-2;
				if( check_otagh || !isPick)
					if(ghimat<=saghf_kharid && saghf_kharid>=0)
					{
						if(ghimat>=ghimat_limit || !khadamat || !isPick)
							submit_frm();
						else
							alert('مبلغ وارد شده از حد مجاز کمتر است');
					}
					else if(saghf_kharid==-1)
						alert('آژانس وجود ندارد');
					else
						alert('مبلغ کل از سقف خرید بیشتر است');
				else
					alert('تعداد نفرات از حد مجاز کمتر است');	
				
				return out;
			}
			function checkKhadamat()
			{
				var out = false;
				var inps = document.getElementsByTagName('input');
				for(var i=0;i < inps.length;i++)
				{
					var x = inps[i].name.split('_');
					if(x[0]=='khadamat')
					{
						if(x[1]=='id')
						{
							if(inps[i].type=='checkbox' && inps[i].checked==true)
								out = true;
							if(inps[i].type=='text' && parseInt(inps[i].value,10)>0)
								out = true;
						}
					}
				}
				return out;
			}
			function check_pick(command,response)
			{
				out = false;
				if(command=='ispick' && trim(response)=='TRUE')
					out = true;
				check_ispick = out;
				return out;
			}
			function addNumber(obj,inp)
			{
				if(obj.checked==true)
					document.getElementById('room_id_tmp').value = document.getElementById('room_id_tmp').value + ((document.getElementById('room_id_tmp').value !='')?',':'')+inp;
				else
				{
					var tmp = document.getElementById('room_id_tmp').value;
					tmp = tmp.split(',');
					var indx = 0;
					var new_tmp= Array();
					for(i= 0 ;i<tmp.length;i++)
					{
						if(tmp[i]!=inp)
						{
							new_tmp[indx]= tmp[i] ;
							indx++;
						}
					}
					document.getElementById('room_id_tmp').value = new_tmp.toString();
				}
			}
			function checkboxChecked()
			{
                                var out = false;
                                var tmp;
                                var inps = document.getElementsByTagName('input');
                                for(var i=0;i < inps.length;i++)
                                {
                                        tmp = String(inps[i].id).split('_');
                                        if(tmp[0]=='otagh' && inps[i].type=='checkbox' && inps[i].checked)
                                                out = true;
                                }
				return(out);
			}
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
				
				if(document.getElementById('daftar_idBelit_3'))
                                        document.getElementById('daftar_idBelit_3').selectedIndex = -1;
                                if(document.getElementById('ajans_idBelit_3'))
                                        document.getElementById('ajans_idBelit_3').selectedIndex = -1;				

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
				/*if( (parseInt(document.getElementById('m_belit_1').value,10)==0 && document.getElementById('daftar_idBelit_1').selectedIndex >0) ||
 (parseInt(document.getElementById('m_belit_1').value,10)>0 && document.getElementById('daftar_idBelit_1').selectedIndex <=0) || 
(parseInt(document.getElementById('m_belit_2').value,10)==0 && document.getElementById('daftar_idBelit_2').selectedIndex >0) ||
 (parseInt(document.getElementById('m_belit_2').value,10)>0 && document.getElementById('daftar_idBelit_2').selectedIndex <=0) ||
(parseInt(document.getElementById('m_belit_3').value,10)==0 && document.getElementById('daftar_idBelit_3').selectedIndex >0) ||
 (parseInt(document.getElementById('m_belit_3').value,10)>0 && document.getElementById('daftar_idBelit_3').selectedIndex <=0) || 
 (parseInt(document.getElementById('m_hotel').value,10)==0) ||
 (parseInt(document.getElementById('m_belit_1').value,10)!=0 && document.getElementById('ajans_idBelit_1').selectedIndex<0) ||
 (parseInt(document.getElementById('m_belit_2').value,10)!=0 && document.getElementById('ajans_idBelit_2').selectedIndex<0) ||
 (parseInt(document.getElementById('m_belit_3').value,10)!=0 && document.getElementById('ajans_idBelit_3').selectedIndex<0)  )
				{
					alert('اطلاعات مربوط به مبالغ را وارد کنید');
				}
				else */if(!checkboxChecked())
					alert('هیچ اتاقی انتخاب نشده است');
				else if( parseInt(document.getElementById('tedad_nafarat').value,10)==0 || document.getElementById('tedad_nafarat').value=='' )
					alert('تعداد نفرات وارد نشده است');
				else if(document.getElementById('aztarikh').value=='' || document.getElementById('tatarikh').value=='' )
					alert('تاریخ را وارد کنید');
				else
				{     
					document.getElementById("mod").value = 301;
					//----Creating Change Log-----------------
					if(document.getElementById('json_string'))
						document.getElementById('json_string').value = fetchJSON();
					//----------------------------------------
					document.getElementById('frm1').submit();
				}
			}
			function kh_check(inp)
			{
				var mainObj = document.getElementById('khadamat_id_'+inp);
				var khObj=null;
				var vObj=null;
				if (document.getElementById('khadamat_v_'+inp))
					vObj = document.getElementById('khadamat_v_'+inp).checked;
				if(document.getElementById('khadamat_kh_'+inp))
					khObj =  document.getElementById('khadamat_kh_'+inp).checked;
				if(khObj!=null && vObj!=null )
					if(vObj || khObj)
						mainObj.checked = true;
					else
						mainObj.checked = false;
				else if(khObj==null && vObj!=null)
					if(vObj)
						mainObj.checked = true;
					else
						mainObj.checked = false;
				else if(khObj!=null && vObj==null)
					if(khObj)
						mainObj.checked = true;
					else
						mainObj.checked = false;
			}
			function getVocher(reserve_id)
			{
				wopen('reserve3.php?reserve_id='+reserve_id,'',800,500);
			}
          

     
    </script>
    
	
	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
	
</head>
<body>
    <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
    <form method="POST" name="frmtedad" id ="frmtedad">
        <input name="txttedad" id="txttedad" type="hidden" value="1"/>
    </form>
	<!-- HEADER -->
	<?php include_once "headermodul.php"; ?>
	<!--/HEADER -->
	
	<!-- PAGE -->
	<section id="page">
			<!-- SIDEBAR -->
			<?php include_once "menubarmodul.php"; ?>
			<!-- /SIDEBAR -->
		<div id="main-content">
			<div class="container">
				
                
                <div class="row" style="margin-right:0px;margin-left:0px;">
                <div class="col-lg-12" style="padding:0px;">
                    <div class="panel panel-default" style="border: 1px solid #ffae2e;">
                        <div class="panel-heading" style="background-color:#ffae2e;color:white;padding:1px;">
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-repeat"></i>اصلاح کردن رزرو</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                            <?php 
				$hotel_name = new hotel_class($h_id);
				$reserver_user='';
				if(isset($reserve->room_det[0]->user_id))
				{
					$reserver_user=new user_class($reserve->room_det[0]->user_id);
					$reserver_user ='رزرو شده توسط <b>'.$reserver_user->fname.' '.$reserver_user->lname.'</b>';
				}
				echo '<div style="margin-bottom:20px;"><b>'.$hotel_name->name.'</b> '.$reserver_user.'</div>';
				if($msg_done!='') 
					echo "<script>alert('$msg_done')</script>";
			?>
                           
                            <?php
                                //echo jdate("F",strtotime($da));
                                echo $outvazeat ;
                            ?>
                           
                           <form id='frm1'  method='GET' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">شماره رزرو:</label> 
                                    <div class="col-md-8"><input class="form-control inp" name="s_reserve_id" id="s_reserve_id" type="text" value="<?php echo ((isset($_REQUEST['s_reserve_id']))?(int)$_REQUEST['s_reserve_id']:0); ?>" >
                                    </div>
                                </div>
                                
                                <input type="hidden" id="json_string" name="json_string" value="" />
                                <input type='hidden' name='mod' id='mod' value='1' >
                                <input type='hidden' name='mode1' id='mode1' value='0' >
                                <input type='hidden' name='d' value="<?php echo $d;?>"/>
                                <input type='hidden' name="h_id" id="h_id" value="<?php echo ((isset($_REQUEST['h_id']))?(int)$_REQUEST['h_id']:0); ?>" >
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left" onclick="send_search();">جستجو</button></div>
                                </div>
                            </div>
                               <div class="box border orange">
									<div class="box-title">
										<h4><i class="fa fa-book"></i>مشاهده جزئیات رزرو</h4>
									
									</div>
									<div class="box-body" style="overflow-x:scroll">
                               
                               <?php 
				echo $output.' '.$msg.' '; 

                                    

				if($isAdmin && isset($_REQUEST['reserve_id']))				
					echo '
                        <a onclick="window.open(\'log_change.php?reserve_id='.$_REQUEST['reserve_id'].'\');" style="margin:5px;min-width:90px;" class="btn btn-warning"><i class="fa fa-pencil-square-o"></i> مشاهده اصلاحات</a>';
			?>
                                   </div>
                                   </div>
                               
                          </form>

                           <?php 
				if(isset($reserve) && !$reserve->editable) 
					echo '<span style="color:red;" > به علت جابجایی اتاق‌ها و تاریخ‌ها، اصلاح اتاق مقدور نیست اما دیگر مواردقابل اصلاح است </span>';
				if(isset($reserve) && $reserve->isOnline)
					echo '<span style="color:red;" >به علت اینکه رزرو بالا بصورت آنلاین گرفته شده و جلوگیری از اختلاف حساب ، امکان اصلاح برخی آیتم‌ها امکان پذیر نمی باشد.</span>';
				if (isset($reserve) && ($reserve->hotel_reserve!=null))
				{
					if (!$isAdmin && isset($reserve) && strtotime(date("Y-m-d H:i:s")." - 3 day")>strtotime($reserve->hotel_reserve->regdat) && $show_sabt != '')
						echo '<span style="color:red;" > به علت گذشتن بیش از ۷۲ ساعت از رزرو اصلاح امکان پذیر نیست </span>';
				}
			 ?>
                        </div>
                        
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
        </div>
    </section>
	<!--/PAGE -->
    	<!-- Modal -->
    <!-- Modal : anbar modal -->
    <div class="modal fade" id="anbar-modal">
	
    </div>
			<!--/Modal : anbar modal-->
	<!-- FOOTER -->

    <!-- Loading -->
<div id="loading">
    <div class="container1">
	   <div class="content1">
        <div class="circle"></div>
        <div class="circle1"></div>
        </div>
    </div>
</div>    
	<!-- GLOBAL JAVASCRIPTS -->
	<?php include_once "inc/footinclude.php" ?>
	
	<!-- Clock -->
	<script src="<?php echo $root ?>inc/digital-clock/assets/js/script.js"></script>
	
	<!-- news ticker -->
	
	<!-- DATE RANGE PICKER -->
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/moment.min.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/daterangepicker.min.js"></script>
	

	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
        <script src="<?php echo $root ?>js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.fa.min.js"></script>
	<script>
	
		var i=0;
		var SSmsg = null;
	
		jQuery(document).ready(function() {
            
            
            $("#loading").hide(); 
            App.setPage("gallery");  //Set current page
			//App.setPage("widgets_box");  //Set current page
			App.init(); //Initialise plugins and elements
			getofflist();
            
             $(document).ready(function() {
               $("#datepicker0").datepicker();
            
                $("#aztarikh").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                    
                });
                $("#aztarikhbtn").click(function(event) {
                    event.preventDefault();
                    $("#aztarikh").focus();
                })
            
                $("#tatarikh").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker3").datepicker({
                    numberOfMonths: 3,
                    showButtonPanel: true
                });
            
                $("#datepicker4").datepicker({
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker5").datepicker({
                    minDate: 0,
                    maxDate: "+14D"
                });
            
                $("#datepicker6").datepicker({
                    isRTL: true,
                    dateFormat: "d/m/yy"
                });   
        $('#dataTables-example').DataTable({
                responsive: true
        });
        
       
        
    });
            
            
		});
        
		function aa(x){
            $.post("<?php echo $root ?>modules/CalendarGetOFFAjax.php",{},function (data){
                if(data){
                    i=data;
                }else{
                    alert("Error!");
                }
            });
        }
		
        function getofflist(){
            $("#cal-pr").html("<img align=\"middle\" class=\"img-responsive\" style=\"margin: auto;\" src=\"<?php echo $root ?>img/loaders/17.gif\">");
            $.post("<?php echo $root ?>modules/CalendarGetOFFAjax.php",{},function (data){
                if(data){
                    i=data;
                    $("#cal-pr").html("");
                    $("#cal-pr").datepicker({changeMonth: true});
                }else{
                    $("#cal-pr").html("<p class=\"fa fa-exclamation-circle text-danger\"> عدم برقراری ارتباط با پایگاه داده</p>");
                }
            });
        }
        
   
        
      

	function StartLoading(){
        
        $("#loading").show();    
		
    }
    function StopLoading(){
        $("#loading").hide(); 
    }
					


		
	</script>


	<?php include_once "footermodul.php"; ?>
	<!--/FOOTER -->
	

</body> 
</html>