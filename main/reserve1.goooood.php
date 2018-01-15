<?php
	session_start();
	include("../kernel.php");
	$GLOBALS['KH_JS'] = '';
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = ($se->detailAuth('all') || $se->detailAuth('reserve'));
	$isPaziresh = FALSE;
	$paziresh_ajans_id = -1;
	$paziresh_ajans_name = '';
	foreach($se->allDetails as $det)
		if(strpos($det,"paziresh_")===0)
		{
			$isPaziresh = TRUE;
			$ttmp = explode('_',$det);
			$paziresh_ajans_id = (int)$ttmp[1];
			$a_tmp = new ajans_class($paziresh_ajans_id);
			$paziresh_ajans_name = $a_tmp->name;
		}
	function showHide($rooms)
	{
		$out = 'none';
		foreach($_REQUEST as $key => $value)
			for($i = 0;$i < count($rooms);$i++)
				if($key == 'otagh_'.$rooms[$i])
					$out = '';
		return($out);
	}
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$hotelList=daftar_class::hotelList((int)$_SESSION['daftar_id']);
		$shart = ' and 1=0';
		if($hotelList)
			$shart=' and ( `id`='.implode(" or `id`=",$hotelList).")";
		$out = '<select name="hotel_id" class="form-control inp" style="width:auto;" >';
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `is_our`!=2 and `moeen_id` > 0 $shart order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadNumber($inp=2)
	{
		$out = '';
		$inp = (int)$inp;
		for($i=0;$i<32;$i++)
		{
			$sel = (($i==$inp)?'selected="selected"':'');
			$si = $i;
			if($i==0)
				$si = "0.5";
			$out.="<option $sel  value='$i' >$si</option>\n";
		}
		return $out;
	}
	function loadDaftar($inp)
	{
		$inp = (int)$inp;
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = ($se->detailAuth('all') || $se->detailAuth('reserve'));
		$out = "<select name=\"daftar_id\" id=\"daftar_id\" class=\"form-control inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
		if($isAdmin)
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `kol_id`>0 order by `name` ',$q);
		else
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `id`='.$inp.' and `kol_id`>0 order by `name`',$q);
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
		$user = new user_class((int)$_SESSION['user_id']);
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = ($se->detailAuth('all') || $se->detailAuth('reserve'));
		$out = "<select name=\"daftar_idBelit_$typ\" id=\"daftar_idBelit_$typ\" class=\"form-control inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
		if($isAdmin)
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `kol_id`>0 order by `name` ',$q);
		else
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `id`='.$user->daftar_id.' and `kol_id`>0 order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;	
	}
	function loadAjans($daftar_id,$sel_aj)
	{
		$daftar_id = (int)$daftar_id;
		$out = "<select id='ajans_id' name=\"ajans_id\" class=\"form-control inp\" style=\"width:auto;\"  >";
		//mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 and `saghf_kharid`>=".$conf->min_saghf_kharid." order by `name`",$q);
		$ajanses = ajans_class::loadByDaftar($daftar_id,TRUE);
		//var_dump($ajanses);
		for($i=0;$i<count($ajanses);$i++)
		{
			$sel = (($ajanses[$i]['id']==$sel_aj)?'selected="selected"':'');
			$out.="<option $sel  value='".$ajanses[$i]['id']."' >".$ajanses[$i]['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadAjansBelit($daftar_id,$sel_aj,$typ)
	{
		$daftar_id = (int)$daftar_id;
		$out = "<select id='ajans_idBelit_$typ' name=\"ajans_idBelit_$typ\" class=\"form-control inp\" style=\"width:auto;\"  >";
		$conf = new conf;
		if($conf->ajans_saghf_mande)
			mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 and `saghf_kharid`>=".$conf->min_saghf_kharid." order by `name`",$q);
		else
			mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$sel_aj)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadKhadamat($hotel_id)
	{
		$out = '<tr><th colspan="4">خدمات به صورت فول برد <input type="checkbox" checked="checked" onclick="resetOrNotKh(this);" ></th></tr>'."\n";
		$hotel_id = (int) $hotel_id;
		$kh = khadamat_class::loadKhadamats($hotel_id);
		$kh_js = '';
		if (isset($_REQUEST['aztarikh']))
			$ta = audit_class::hamed_pdate($_REQUEST['aztarikh']);
		else
			$ta = audit_class::hamed_pdate(date("Y-m-d H:i:s"));
		for($i=0;$i<count($kh );$i++)
		{
			$gahst_tarikh ="";
			$axe_tarikh ="";
			$voroodi = '';
			$jtick = '';
			$khorooji = '';
			$voroodi1 = '';
			$khorooji1 = '';
			$class = 'class="khadamat"';
			if($kh[$i]['typ']==1)
				$jtick = "onclick='kh_check(\"".$kh[$i]['id']."\");'";
			if($kh[$i]['voroodi'] )
			{
				$view1 = ($kh[$i]['aval_ekhtiari']==1)?'':'checked="checked"';
				$voroodi = "<span>اول</span><input $class $view1  $jtick type='checkbox' name='kh_v_".$kh[$i]['id']."' id='kh_v_".$kh[$i]['id']."' >";
				$voroodi1 = "<span>ورودی</span><input $class $view1  $jtick type='checkbox' name='kh_v_".$kh[$i]['id']."' id='kh_v_".$kh[$i]['id']."' >";
				$kh_js .="\ndocument.getElementById('kh_v_".$kh[$i]['id']."').checked=".(($kh[$i]['aval_ekhtiari']==1)?'false;':'true;');
				if($kh[$i]['typ']==1)
					$kh_js .="\nkh_check(\"".$kh[$i]['id']."\");";
			}
			if($kh[$i]['khorooji'])
			{
				$view2 = ($kh[$i]['aval_ekhtiari']==2)?'':'checked="checked"';
				$khorooji = "<span>آخر</span><input $class $view2 $jtick type='checkbox' name='kh_kh_".$kh[$i]['id']."' id='kh_kh_".$kh[$i]['id']."' >";
				$khorooji1 = "<span>خروجی</span><input $class $view2 $jtick type='checkbox' name='kh_kh_".$kh[$i]['id']."' id='kh_kh_".$kh[$i]['id']."' >";
				$kh_js .="\ndocument.getElementById('kh_kh_".$kh[$i]['id']."').checked=".(($kh[$i]['aval_ekhtiari']==2)?'false;':'true;');
				if($kh[$i]['typ']==1)
					$kh_js .="\nkh_check(\"".$kh[$i]['id']."\");";
			}
			$view = '';
			if($view1=='checked="checked"' || $view2=='checked="checked"')
				$view = 'checked="checked"';
			$inp = 	"<input $class $view style='display:none;' type='checkbox' name='kh_ch_".$kh[$i]['id']."' id='kh_ch_".$kh[$i]['id']."' > $voroodi1 $khorooji1";
			if($kh[$i]['typ']==0)
			{
				$inp = 	"تعدادروزانه:<input $class type='text' class='form-control inp' style='width:30px;' name='kh_txt_".$kh[$i]['id']."' id='kh_txt_".$kh[$i]['id']."'  value='".((isset($_REQUEST['kh_'.$kh[$i]['id']]))?$_REQUEST['kh_'.$kh[$i]['id']]:0)."' > $voroodi $khorooji";
			}
			$ghimat = "<div style='display:none;' >قیمت‌واحد:<input class='form-control inp' style='width:70px' name='kh_gh_".$kh[$i]['id']."' value='".((isset($_REQUEST['kh_'.$kh[$i]['ghimat']]))?$_REQUEST['kh_'.$kh[$i]['ghimat']]:$kh[$i]['ghimat'])."' > </div>";
		/*	if($kh[$i]['gashtAst']==1)
				$gahst_tarikh = "<input value='$ta' type='text' name='ta_gasht' readonly='readonly' class='inp' style='direction:ltr;' id='datepicker7' />";
			if($kh[$i]['axeAst']==1)
				$axe_tarikh = "<input value='$ta' type='text' name='ta_axe' readonly='readonly' class='inp' style='direction:ltr;' id='datepicker8' />";
			if(($i % 2) == 0)
				$out .="<tr>";
			$out .="<td>".$kh[$i]['name'].":</td><td>$inp $ghimat $gahst_tarikh $axe_tarikh</td>";*/
			if(($i % 2) == 0)
				$out .="<tr>";
			$out .="<td>".$kh[$i]['name'].":</td><td>$inp $ghimat</td>";
			if(($i % 2) == 1)
				$out .="</tr>";
		}
		$kh_js .="\ncalculate_nafar();";
		$GLOBALS['KH_JS'] ="function reset_full_board(){ \n $kh_js \n }";
		return $out;
	}
	$mode1 = ((isset($_REQUEST['mode1']))?(int)$_REQUEST['mode1']:0);
	$msg = '';
        $output = '';
        $hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);
	//-----newstart-----
	$h_id = ((isset($_REQUEST['h_id']))?(int)$_REQUEST['h_id']:-1);
	$h_id = ((isset($_REQUEST['hotel_id']))?$hotel_id:$h_id);
	/*	
        $d = ((isset($_REQUEST['d']))?$_REQUEST['d']:perToEnNums(jdate("m")));
        $month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
        $da = audit_class::hamed_pdateBack(jdate("Y/$d/d"));
        $tmp = explode(" ",$da);
        $da = $tmp[0];
        $hotel1 = new hotel_class($h_id);
        $outvazeat = $hotel1->loadRooms($da);
	*/
	if ($mode1!=1)
        {
	//-----newend------
	if($isAdmin )
	{
		$daftar_id = ((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
		$daftar_idBelit_1 = ((isset($_REQUEST['daftar_idBelit_1']))?(int)$_REQUEST['daftar_idBelit_1']:-1);
		$daftar_idBelit_2 = ((isset($_REQUEST['daftar_idBelit_2']))?(int)$_REQUEST['daftar_idBelit_2']:-1);
	}
	else
	{
		$daftar_id = (int)$_SESSION["daftar_id"] ;
		$daftar_idBelit_1 = (int)$_SESSION["daftar_id"] ;
		$daftar_idBelit_2 = (int)$_SESSION["daftar_id"] ;
	}
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d 14:00:00'));
	$az = strtotime($aztarikh);
	$no = strtotime(date('Y-m-d 23:59:59'));
	//if(!$isAdmin && $az < $no) مجددا طبق درخواست اصلاح شد
	if(!$se->detailAuth('all') && $az < $no) 
		$aztarikh = date("Y-m-d 14:00:00");
	$shab = ((isset($_REQUEST['shab']))?(int)$_REQUEST['shab']:-1);
	$shab_reserve = ((isset($_REQUEST['shabreserve']))?TRUE:FALSE);
	$rooz_reserve = ((isset($_REQUEST['roozreserve']))?TRUE:FALSE);
	$rooms = array();
	if(isset($_REQUEST['hotel_id']))
	{
		$hotel = new hotel_class($hotel_id);
		$tatarikh = $hotel->addDay($aztarikh,$shab);
		if ($hotel->hotelAvailableBetween($aztarikh,$tatarikh))
			$rooms = room_class::loadOpenRooms($aztarikh,$shab,$shab_reserve,$rooz_reserve,$hotel_id,$_SESSION['daftar_id']);
	}
	if($hotel_id>0 && count($rooms)>0)
	{
		
		$output='
        <div class="box border orange">
									
									<div class="box-body" style="overflow-x:scroll">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">انتخاب</th>
												<th style="text-align:right">ظرفیت</th>
												<th style="text-align:right">موجود</th>
                                                <th style="text-align:right">قیمت</th>
											  </tr>
											</thead>
											<tbody>';
		if ($hotel->hotelAvailableBetween($aztarikh,$tatarikh))
		{
			if(!$conf->room_select)
			{
				for($i=0;$i<sizeof($rooms);$i++)
				{
					$checked = ((isset($_REQUEST['room_typ_id']) && $_REQUEST['room_typ_id']==$rooms[$i]['room_typ_id'] )?'checked="checked"':'');
					$output .= "<tr><td><input type='radio' name='room_typ_id' value='".$rooms[$i]['room_typ_id']."' $checked ></td>";
					$output .= "<td>".$rooms[$i]['name']."</td>";
					$output .= "<td>".$rooms[$i]['count']."</td>";
					$output .= "<td>بطور خودکار محاسبه می شود<input style='display:none;' class='form-control inp' name='room_gh_".$rooms[$i]['room_typ_id']."' value='".$rooms[$i]['ghimat']."' > </td><tr>";
				}
				$output .= "<tr><td colspan='3'>تعداد درخواستی:<input type='text' value='".((isset($_REQUEST['tedad_otagh']))?$_REQUEST['tedad_otagh']:1)."'  name='tedad_otagh' class='form-control inp' ></td>";
			}
			if($conf->room_select)
			{
				$tmp_check = '';
				for($i=0;$i<sizeof($rooms);$i++)
				{
					$room_numbers = $rooms[$i]['room_ids'];
					$output .= "<tr><td id='show_$i' onmouseover='change_color(this,\"in\");' onmouseout='change_color(this,\"out\");' onclick='show_hide(\"tr_$i\",this);' style='cursor:pointer' >".((showHide($room_numbers)=='none')?"مشاهده":"عدم مشاهده")."</td>";
					$output .= "<td>".$rooms[$i]['name']."</td>\n";
					$output .= "<td>".$rooms[$i]['count']."</td>\n";
					//$output .= "<td>بطور خودکار محاسبه می شود<input style='display:none;' class='inp' name='room_gh_".$rooms[$i]['room_typ_id']."' value='".$rooms[$i]['ghimat']."' > </td></tr><tr id='tr_$i' style='display:".showHide($room_numbers).";' >";
					$output .= "<td><input style='display:none;' class='form-control inp' name='room_gh_".$rooms[$i]['room_typ_id']."' value='".$rooms[$i]['ghimat']."' > </td></tr><tr id='tr_$i' style='display:".showHide($room_numbers).";' >";
					$output .= "<td style='text-align:right;' colspan='4' width='99%' > ";
					for($j=0;$j<count($room_numbers);$j++)
					{
						$shomare_otagh = new room_class($room_numbers[$j]);
						$check_box = ((isset($_REQUEST['otagh_'.$room_numbers[$j]]))?'checked="checked"':'');
						$tmp_check .= ((isset($_REQUEST['otagh_'.$room_numbers[$j]]))?(($tmp_check!='')?',':'').$room_numbers[$j]:'');
						$output .= $shomare_otagh->name."<input type='checkbox' $check_box name='otagh_".$room_numbers[$j]."' id='otagh_".$room_numbers[$j]."' value='".$room_numbers[$j]."'  onclick='addNumber(this,".$room_numbers[$j].")' >&nbsp;&nbsp;&nbsp;";
					}
					$output .= "</td>\n</tr>";
					
				}
				//$output .= "<tr><td colspan='3'>تعداد درخواستی بصورت خودکار محاسبه می شود</td>";
				//
				$output .= "<tr><td colspan='2'>
                <input type='button' value='انتخاب همه اتاقها' class='btn btn-info col-md-12' onclick=\"select_all_rooms('otagh');\" >
                
                
                </td>";
			}
			$output .="<td style=\"width:50px;\" >تعدادنفرات:</td><td><input onkeypress='return numbericOnKeypress(event);' type='text' id='tedad_nafarat' name='tedad_nafarat' value='".((isset($_REQUEST['tedad_nafarat']))?$_REQUEST['tedad_nafarat']:1)."' class='form-control inp' onblur='calculate_nafar();' ></td></tr>";
			if($conf->tour_enabled)
			{
				$tour_mab_view = 'مبلغ تور:';
				$raft_sherkat = 'شرکت بلیت رفت';
				$m_belit1_view = 'مبلغ  بلیــت  رفت:';
				$m_belit2_view = 'مبلغ‌بلیت‌برگشت:';
				$m_belit2_style = '';
			}
			else
			{
				$tour_mab_view = 'مبلغ کل هتل:';
				$raft_sherkat = 'حساب کمیسیون';
				$m_belit1_view = 'مبلغ  کمیسیون:';
				$m_belit2_view = '';
				$m_belit2_style = 'style="display:none;"';
			}
			$sel_aj = ((isset($_REQUEST['ajans_id']))?$_REQUEST['ajans_id']:0);
			$output .= "<tr><td id='di1'>نام دفتر</td><td>".loadDaftar($daftar_id)."</td><td>نام آژانس</td><td colspan='1' >". (($isPaziresh)?'<select id="ajans_id" name="ajans_id" class="form-control inp" style="width:auto;"><option value="'.$paziresh_ajans_id.'">'.$paziresh_ajans_name.'</option></select>':loadAjans($daftar_id,$sel_aj))."</td>";
			$sel_ajBelit_1 = ((isset($_REQUEST['ajans_idBelit_1']))?$_REQUEST['ajans_idBelit_1']:0);
			$output .= "<tr><td>دفتر </td><td>".loadDaftarBelit($daftar_idBelit_1,1)."</td><td>$raft_sherkat</td><td colspan='1' >". loadAjansBelit($daftar_idBelit_1,$sel_ajBelit_1,1)."</td>";
			$output .= "</tr>";
			$sel_ajBelit_2 = ((isset($_REQUEST['ajans_idBelit_2']))?$_REQUEST['ajans_idBelit_2']:0);
			$output .= "<tr $m_belit2_style ><td>دفتر </td><td>".loadDaftarBelit($daftar_idBelit_2,2)."</td><td>شرکت بلیت برگشت</td><td colspan='1' >". loadAjansBelit($daftar_idBelit_2,$sel_ajBelit_2,2)."</td>";
			$output .= "</tr>";
			//$output .= "</tr>";
			$output .=loadKhadamat($hotel_id);
			if($shab_reserve || ((int)$_REQUEST['mod']==2 && isset($_REQUEST['shabreserve_gh']) )  )
			{
				$output .= "<tr><td colspan='4' >شب-رزرودارد <input style='display:none;' class='form-control inp'  name=\"shabreserve_gh\" id=\"shabreserve_gh\" type=\"text\"".((isset($_REQUEST['shabreserve_gh']))?$_REQUEST['shabreserve_gh']:0)." >";
			}
			else
			{
				$output .= '<td colspan="4" >شب-رزروندارد';
			}
			if($rooz_reserve || ((int)$_REQUEST['mod']==2 && isset($_REQUEST['roozreserve_gh']) ) )
			{
				$output .= "&nbsp;&nbsp;&nbsp;&nbsp;روز-رزرودارد <input style='display:none;'  class='form-control inp' name=\"roozreserve_gh\" id=\"roozreserve_gh\" type=\"text\"".((isset($_REQUEST['roozreserve_gh']))?$_REQUEST['roozreserve_gh']:0)." > </td>";
			}
			else
			{
				$output .= '&nbsp;&nbsp;&nbsp;&nbsp;روز-رزروندارد</td></tr>';
			}
			$canEdit = '';
			if($conf->ghimat_readonly=='TRUE' && !($se->detailAuth('dafater') || $se->detailAuth('all') || $se->detailAuth('reserve')))
				$canEdit = 'readonly="readonly"';
			$output .= "<tr><td colspan='3' >$tour_mab_view<input onkeyup='monize(this);' class='form-control inp' type='text' name='tour_mablagh' id='tour_mablagh' $canEdit value='".((isset($_REQUEST['tour_mablagh']))?$_REQUEST['tour_mablagh']:"")."' ></td><td>$m_belit1_view<input onkeyup='monize(this);' class='form-control inp' type='text' name='belit_mablagh_1' id='belit_mablagh_1' value='".((isset($_REQUEST['belit_mablagh_1']))?$_REQUEST['belit_mablagh_1']:"")."' ><br/>$m_belit2_view<input $m_belit2_style onkeyup='monize(this);' class='form-control inp' type='text' name='belit_mablagh_2' id='belit_mablagh_2' value='".((isset($_REQUEST['belit_mablagh_2']))?$_REQUEST['belit_mablagh_2']:"")."' ></td></tr>";
			$output .= "</tr>";
			$jcheck = 'radioChecked';
			if($conf->room_select)
				$jcheck = 'checkboxChecked';
			$output .= "<tr><td colspan='4' >
<button style=\"margin:5px;\" class=\"btn btn-info col-md-4\" onclick=\"send_calc();if($jcheck()){getAjaxInfo();}else{document.getElementById('wait').style.display='none';alert('لطفاً اطلاعات را کامل وارد کنید');}\">

محاسبه قیمت</button>
<img src='../class/wait.gif' style='display:none;' id='wait' >
<input type='hidden' name='ghimat_calc' id='ghimat_calc' value=0 >
<button style=\"margin:5px;\" id='reserve_but' class=\"btn btn-success col-md-4\" onclick=\"document.getElementById('ghimat_calc').value=0;if($jcheck()){getAjaxInfo();}else{alert('لطفاً اطلاعات را کامل وارد کنید');}\">

رزرو</button>
<input value='$tmp_check' name='room_id_tmp' id='room_id_tmp' type='hidden' ></td></tr>";
			
			$output .= '</tbody></table></div></div>';
		}
		else
		{
			$msg  = '<script>alert("رزرو هتل در این بازه تاریخی امکان پذیر نیست");</script>';		
		}
	}
	else
	{
		$output .= "اتاقی موجود نیست";
	}
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>سامانه رزرواسیون</title>
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
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/bootstrap-datepicker.fa.min.js"></script>
    <script type="text/javascript" src="../js/tavanir.js"></script>
      <script>
    $(document).ready(function(){
    
    $("#datepicker0").datepicker();
            
                $("#datepicker1").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                    
                });
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
                $("#datepicker2").datepicker({
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
        
        
    });
          
          
          
          var check_otagh = false;
			var check_roompick = false;
			var isPick = false;
			var isAdmin = <?php echo (($isAdmin)?'true':'false'); ?>;
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
			
			function getAjaxInfo()
			{
                StartLoading();
				document.getElementById('reserve_but').disabled = true;
				check_otagh = false;
				check_roompick = false;
				var hotel_id = "<?php echo $hotel_id; ?>";
				var aztarikh = "<?php echo ((isset($aztarikh))?$aztarikh:''); ?>" ;
				var shab = "<?php echo ((isset($shab))?$shab:''); ?>" ; 
				var nafar = parseInt(document.getElementById('tedad_nafarat').value,10);
				var room = document.getElementById('room_id_tmp').value;
				var daftar_id = <?php echo (int)$_SESSION["daftar_id"]; ?>;
				var ajans_id = -1;
				if(document.getElementById('ajans_id').options.length>0)
					ajans_id =parseInt(document.getElementById('ajans_id').options[document.getElementById('ajans_id').selectedIndex].value,10);
				if(ajans_id>0)
					mehrdad_ajaxFunction(check_room,'roomcheck',hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id);
				else
				{
					alert('آژانس خریدار را وارد کنید');
					document.getElementById('reserve_but').disabled = false;
				}
			}
			function check_room(command,response)
			{
				out = false;
				var hotel_id = "<?php echo $hotel_id; ?>";
				var aztarikh = "<?php echo ((isset($aztarikh))?$aztarikh:''); ?>" ;
				var shab = "<?php echo ((isset($shab))?$shab:''); ?>" ; 
				var nafar = parseInt(document.getElementById('tedad_nafarat').value,10);
				var room = document.getElementById('room_id_tmp').value;
				var daftar_id = <?php echo (int)$_SESSION["daftar_id"]; ?>;
				if(command=='roomcheck' && trim(response)=='TRUE')
					out = true;
				check_otagh = (out || isAdmin);
				var ajans_id =parseInt(document.getElementById('ajans_id').options[document.getElementById('ajans_id').selectedIndex].value,10);
				if(ajans_id>0)
					mehrdad_ajaxFunction(check_room_pick,'roompick',hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id);
				else
				{
					alert('آژانس خریدار را وارد کنید');
					document.getElementById('reserve_but').disabled = false;
				}
				return out;
			}
			function check_room_pick(command,response)
			{
				out = false;
				var hotel_id = "<?php echo $hotel_id; ?>";
				var aztarikh = "<?php echo ((isset($aztarikh))?$aztarikh:''); ?>" ;
				var shab = "<?php echo ((isset($shab))?$shab:''); ?>" ; 
				var nafar = parseInt(document.getElementById('tedad_nafarat').value,10);
				var room = document.getElementById('room_id_tmp').value;
				var daftar_id = <?php echo (int)$_SESSION["daftar_id"]; ?>;
				if(command=='roompick' && trim(response)=='TRUE')
					out = true;
				check_roompick = (out || isAdmin);

				var ajans_id =document.getElementById('ajans_id').options[document.getElementById('ajans_id').selectedIndex].value;
				if(ajans_id>0)
					mehrdad_ajaxFunction(is_pick,'ispick',hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id);
				else	
				{
					alert('آژانس خریدار را وارد کنید');
					document.getElementById('reserve_but').disabled = false;
				}
				return out;
			}
			var ghimat_khadamat = 0;
			function is_pick(command,response)
			{
				out = false;
				var hotel_id = "<?php echo $hotel_id; ?>";
				var aztarikh = "<?php echo ((isset($aztarikh))?$aztarikh:''); ?>" ;
				var shab = "<?php echo ((isset($shab))?$shab:''); ?>" ; 
				var nafar = parseInt(document.getElementById('tedad_nafarat').value,10);
				var room = document.getElementById('room_id_tmp').value;
				var daftar_id = <?php echo (int)$_SESSION["daftar_id"]; ?>;
				if(command=='ispick' && trim(response)=='TRUE')
					out = true;
				isPick = out;
				var ajans_id =document.getElementById('ajans_id').options[document.getElementById('ajans_id').selectedIndex].value;
				if(ajans_id>0)
				{
					var tmp_kh = {};
					var tmpId;
					var khadamat_id;
					$.each($(".khadamat"),function(id,field){
						tmpId  = field.id.split('_');
						khadamat_id = tmpId[2];
						if(tmpId.length==3 )
						{
							if(tmpId[1]=='txt')
							{
								if(!(tmp_kh[khadamat_id]))
									tmp_kh[khadamat_id] = {};
								tmp_kh[khadamat_id]["tedad"] = field.value ; 
							}
							else if(tmpId[1]=='ch')
							{
								if(!(tmp_kh[khadamat_id]))
									tmp_kh[khadamat_id] = {};
								tmp_kh[khadamat_id]["tedad"] = -1 ; 
							}
							else if(tmpId[1]=='v')
							{
								if(!(tmp_kh[khadamat_id]))
									tmp_kh[khadamat_id] = {};
								tmp_kh[khadamat_id]["voroodi"] =  field.checked;
							}
							else if(tmpId[1]=='kh')
							{
								if(!(tmp_kh[khadamat_id]))
									tmp_kh[khadamat_id] = {};
								tmp_kh[khadamat_id]["khorooji"] =  field.checked;
							}
						}
					});
					for(k in tmp_kh)
						if(tmp_kh && tmp_kh[k]['tedad']<=0 && !tmp_kh[k]['voroodi'] && !tmp_kh[k]['khorooji'])
							delete tmp_kh[k];
					var jkh = JSON.stringify(tmp_kh);
					$.get("ajax_responser.php?r="+Math.random()+"&command=khghimat&hotel_id="+hotel_id+"&test_date="+aztarikh+"&delay="+shab+"&rooms="+room+"&nafar="+nafar+"&daftar_id="+daftar_id+"&ajans_id="+ajans_id+'&kh='+jkh+'&',function(result){
						ghimat_khadamat = result;
						mehrdad_ajaxFunction(check_ghimat,'getghimat',hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id);
					});
					
				}
				else	
				{
					alert('آژانس خریدار را وارد کنید');
					document.getElementById('reserve_but').disabled = false;
				}
				return out;
			}
			function check_ghimat(command,response)
			{
				out = 0;
				var khadamat = checkKhadamat();
				var dafatr_id =document.getElementById('daftar_id').value;
				var ajans_id =document.getElementById('ajans_id').value;
				var daftar_idBelit_1 =document.getElementById('daftar_idBelit_1').value;
				var daftar_idBelit_2 =document.getElementById('daftar_idBelit_2').value;
				var ajans_idBelit_1 =document.getElementById('ajans_idBelit_1').value;
				var ajans_idBelit_2 =document.getElementById('ajans_idBelit_2').value;
				var ajans_saghf_mande = <?php echo (($conf->ajans_saghf_mande)?'false':'true');  ?>;
				if(dafatr_id>0 && ajans_id>0)
				{
					var tour = parseInt(unFixNums(umonize(document.getElementById('tour_mablagh').value)),10);
					if(isNaN(tour))
						tour =0;
					var belit1 = parseInt(unFixNums(umonize(document.getElementById('belit_mablagh_1').value)),10);
					if(isNaN(belit1))
						belit1 =0;
					var belit2 = parseInt(unFixNums(umonize(document.getElementById('belit_mablagh_2').value)),10);
					if(isNaN(belit2))
						belit2 =0;
					if( ((belit1>0 && daftar_idBelit_1>0 && ajans_idBelit_1>0) || belit1==0) && ((belit2>0 && daftar_idBelit_2>0 && ajans_idBelit_2>0) || belit2==0)  )
					{
						var ghimat = tour-(belit1+belit2) ;
						if(ghimat>0)
						{
							response = response.split(',');
							var ghimat_limit = parseInt(trim(response[0]),10) + parseInt(ghimat_khadamat,10) ;
							//alert('ghimat_limit='+ghimat_limit);
							var saghf_kharid = (response[1])?parseInt(trim(response[1]),10):-2;
							//alert(saghf_kharid);
							if(check_otagh || !isPick)
								if((ghimat<=saghf_kharid && saghf_kharid>0) || ajans_saghf_mande)
								{
									if( !isPick || ghimat>=ghimat_limit || !khadamat)
									{
										if(document.getElementById('ghimat_calc').value==0)
										{
											document.getElementById('frm1').action='reserve2.php';
											document.getElementById('frm1').submit();
										}
										else
										{
											document.getElementById('wait').style.display='none';
											document.getElementById('tour_mablagh').style.display = '';
											document.getElementById('tour_mablagh').value=monize2(ghimat_limit);
										}
									}
									else
										alert('مبلغ وارد شده از حد مجاز کمتر است');

								}
								else if(saghf_kharid==-1)
									alert('آژانس وجود ندارد');
								else
								{
									//var saghf = String(abs(saghf_kharid));
/*
									if(saghf_khard>0) 
										saghf = saghf+' بستانکار ';
									else 
										saghf = saghf+' بدهکار ';
*/
									alert("مبلغ کل از سقف خرید(موجودی آژانس) بیشتر است\nمانده حساب آژانس به اضافه سقف خریدش بررسی شود");	
								}
							else
								alert('تعداد نفرات از حد مجاز کمتر است');	
						}
						else
						{
							alert('قیمت تور درست وارد نشده است');
						}
					}
					else
						alert('حساب بلیت بدرستی انتخاب نشده است');
				}
				else
					alert('دفتر یا آژانس گیرنده انتخاب نشده است');
				document.getElementById('wait').style.display='none';
				document.getElementById('reserve_but').disabled = false;
				
				return out;
			}
			function checkKhadamat()
			{
				var out = false;
				var inps = document.getElementsByTagName('input');
				for(var i=0;i < inps.length;i++)
				{
					var x = inps[i].name.split('_');
					if(x[0]=='kh')
					{
						if(x[1]=='txt')
							if(parseInt(inps[i].value,10)>0)
								out= true;
						if(x[1]=='ch')
							if(inps[i].checked==true)
								out= true;
					}
				}
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
			function radioChecked()
			{
				var out = false;
				var inps = document.getElementsByTagName('input');
				for(var i=0;i < inps.length;i++)
					if(inps[i].type=='radio' && inps[i].checked)
						out = true;
				if(document.getElementById('daftar_id').selectedIndex <= 0 || document.getElementById('tour_mablagh').value=='' || (parseInt(document.getElementById('belit_mablagh').value,10)>0 && document.getElementById('daftar_idBelit').selectedIndex <= 0))
					out = false;
				//if(parseInt(document.getElementById('tour_mablagh').value,10)>=parseInt(document.getElementById('belit_mablagh').value,10))
					//out= false;
				return(out);
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
				if(document.getElementById('daftar_id').selectedIndex <= 0 || document.getElementById('tour_mablagh').value=='' || (parseInt(document.getElementById('belit_mablagh_1').value,10)>0 && document.getElementById('daftar_idBelit_1').selectedIndex <= 0) || (parseInt(document.getElementById('belit_mablagh_2').value,10)>0 && document.getElementById('daftar_idBelit_2').selectedIndex <= 0) || parseInt(document.getElementById('tour_mablagh').value,10)==0 )
                                        out = false;
				return(out);
			}
			function send_search()
			{
				var bool = true;
				if(bool)
				{	
					if(document.getElementById('tour_mablagh')) document.getElementById('tour_mablagh').value='';
					if(document.getElementById('belit_mablagh')) document.getElementById('belit_mablagh').value='';
					if(document.getElementById('daftar_id')) document.getElementById('daftar_id').selectedIndex = -1;
					if(document.getElementById('ajans_id')) document.getElementById('ajans_id').selectedIndex = -1;
					if(document.getElementById('daftar_idBelit_1')) document.getElementById('daftar_idBelit_1').selectedIndex = -1;
					if(document.getElementById('ajans_idBelit_1')) document.getElementById('ajans_idBelit_1').selectedIndex = -1;
					if(document.getElementById('daftar_idBelit_2')) document.getElementById('daftar_idBelit_2').selectedIndex = -1;
					if(document.getElementById('ajans_idBelit_2')) document.getElementById('ajans_idBelit_2').selectedIndex = -1;
					document.getElementById('mod').value=1;
					if(document.getElementById('datepicker1').value!='')
						document.getElementById('frm1').submit();
					else 
						alert("تاریخ را وارد کنید");
				}
			}
			function kh_check(inp)
			{
				var mainObj = document.getElementById('kh_ch_'+inp);
				var vObj = document.getElementById('kh_v_'+inp);
				var khObj = document.getElementById('kh_kh_'+inp);
				if(vObj.checked || khObj.checked )
					mainObj.checked = true;
				else
					mainObj.checked = false;
			}
			function send_calc()
			{
				document.getElementById('wait').style.display='';
				document.getElementById('ghimat_calc').value=1;
				document.getElementById('tour_mablagh').value=1000000000;
				document.getElementById('tour_mablagh').style.display = 'none';
			}
			function calculate_nafar()
			{
				if(document.getElementById('tedad_nafarat'))
				{
		                        var adult = document.getElementById('tedad_nafarat').value;   
		                        var inps =  document.getElementsByTagName('input');
		                        var tmp;
		                        adult = parseInt(adult,10);
		                        for (var i=0;i<inps.length;i++)
		                        { 
		                                tmp = inps[i].name.split('_');
		                                if (tmp.length==3 && tmp[0]=='kh' && tmp[1]=='txt')
		                                        inps[i].value = String(adult);
		                        }
				}
			}
			function clearKhadamat()
			{
				var inps = document.getElementsByTagName('input');
				var x;
				for(var i=0;i < inps.length;i++)
				{
					x = inps[i].id.split('_');
					if(x[0]=='kh' && x.length==3)
					{
						if(x[1]=='txt')
							inps[i].value=0;	
						if(x[1]=='v' || x[1]=='kh' || x[1]=='ch')
							inps[i].checked=false;
					}
				}
			}
			function resetOrNotKh(Obj)
			{
				if(Obj.checked)
					reset_full_board();
				else
					clearKhadamat();
			
			}
			function select_all_rooms(room_frase)
			{
				var inp = document.getElementsByTagName('input');
				var tmp;
				for(var i=0;i < inp.length;i++)
				{
					tmp = inp[i].id.split('_');
					if(tmp.length == 2 && tmp[0] == room_frase && inp[i].type=='checkbox')
						inp[i].checked = ((inp[i].checked)?false:true);
				}
			}
			<?php echo $GLOBALS['KH_JS']; ?>
     
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-bell"></i>سامانه رزرواسیون</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                          <form id="frm2" method="get">
                                <!-- <select name="d" class="inp" onchange="document.getElementById('frm2').submit();">
                                <?php
                                        //for($i=1;$i<=count($month);$i++)
                                               // echo "<option value=\"$i\"".(($i==$d)?"selected=\"selected\"":"").">\n".$month[$i-1]."\n</option>\n";
                                ?>
                                </select> -->
<!--                                ماه 
                               <input type="hidden" id="hotel_id" name="hotel_id" value="<?php echo $h_id; ?>" /> -->
				<input type="hidden" id="mode1" name="mode1" value="1"/>
                              <input type="hidden" id="daftar_id1" name="daftar_id1" value="<?php echo $daftar_id; ?>"/>
                        </form>
                          <form id='frm1'  method='GET' >
                               <div class="box border orange">
									
									<div class="box-body" style="overflow-x:scroll">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">نام هتل</th>
												<th style="text-align:right">تاریخ</th>
												<th style="text-align:right">مدت اقامت</th>
                                                <th style="text-align:right">شب-‌رزرو<br/>(نیم شارژ ورودی)</th>
                                                  <th style="text-align:right">روز-‌رزرو<br/>(نیم شارژ خروجی)</th>
                                                  <th style="text-align:right">جستجو</th>
											  </tr>
											</thead>
											<tbody>
				<tr>
					<td>
						<?php 
							if(isset($_GET['h_id']))
								echo loadHotel((int)$_GET['h_id']);
							else
								echo loadHotel($hotel_id); 
						?>
					</td>
					<td>	
         					   <input value="<?php echo ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdate($aztarikh):audit_class::hamed_pdate(date("Y-m-d H:i:s"))); ?>" type="text" name='aztarikh' class='form-control inp' style='direction:ltr;' id="datepicker1" />	
					</td>
					<td>
						<?php
						 if($conf->unlimited_day!=='') { ?>
						<input type="text" class="form-control inp" style="width:30px;" name="shab" id="shab" value="<?php  echo ((isset($_REQUEST['shab']))?$_REQUEST['shab']:1); ?>"  >
						<?php 
						}
						else 
						{	
						?>
						<select  class='form-control inp' name='shab' id='shab' >
							<?php  echo loadNumber((isset($_REQUEST['shab']))?$_REQUEST['shab']:1); ?>
						</select>
						<?php 
						} 
						?>
						
					</td>
					<td>
						<input name="shabreserve" id="shabreserve" type="checkbox" <?php echo ((isset($_REQUEST['shabreserve']))?'checked="checked"':''); ?> >
					</td>
					<td>
						<input name="roozreserve" id="roozreserve" type="checkbox" <?php echo ((isset($_REQUEST['roozreserve']))?'checked="checked"':''); ?> >
					</td>
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='hidden' name='mode1' id='mode1' value='0' >
						<!--<input type='hidden' name='d' value="<?php //echo $d;?>"/>-->
						<button class="btn btn-primary" onclick='send_search();'><i class="fa fa-search"></i> جستجو</button>
                        
						
					</td>					
				</tr>
                                                </tbody>
			</table>
                                        </div>
                                   </div>
			<?php echo $output.' '.$msg; ?>
			</form>
			<script language="javascript" >
				calculate_nafar();
			</script>          
                              
                               
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
<div class="modal fade" id="newG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن گارانتی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="editG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش گارانتی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف گارانتی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        آیا از حذف گارانتی طبقه مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
                </div>
            
        </div>
    </div>
</div>
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
	
	<!-- DATE RANGE PICKER -->
    <script src="<?php echo $root ?>inc/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>inc/bootstrap-datepicker.fa.js"></script>
	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    
	<script>
	
		var i=0;
		var SSmsg = null;
	
		jQuery(document).ready(function() {
            var daftar_id1 = $("input[name='daftar_id1']").val();
            if(daftar_id1>-1){
                loadScr();
            }
            $("#loading").hide(); 
            App.setPage("gallery");  //Set current page
			//App.setPage("widgets_box");  //Set current page
			App.init(); //Initialise plugins and elements
			getofflist();
            
             $(document).ready(function() {
        $('#dataTables-example').DataTable({
                responsive: true
        });
        
       
        
    });
            
            
		});
        function loadScr(){
            $("body,html").animate({
                        scrollTop: $("#di1").offset().top
                    }, 2000);
        }
        
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
        
        function rakModal(rakId){
            StartLoading();
            var id=rakId;
            
            $.post("gaantinfo.php",{oid:id},function(data){
                StopLoading();
                $("#rk").html(data);
                $('#rak-modal').modal('show');             

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