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
		$shart = '';
		if($hotelList)
			$shart=' and ( `id`='.implode(" or `id`=",$hotelList).")";
		$out = '<select name="hotel_id" class="inp" style="width:auto;" >';
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `moeen_id` > 0 $shart order by `name` ",$q);
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
		$out = "<select name=\"daftar_id\" id=\"daftar_id\" class=\"inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
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
		$out = "<select name=\"daftar_idBelit_$typ\" id=\"daftar_idBelit_$typ\" class=\"inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
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
		$out = "<select id='ajans_id' name=\"ajans_id\" class=\"inp\" style=\"width:auto;\"  >";
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
		$out = "<select id='ajans_idBelit_$typ' name=\"ajans_idBelit_$typ\" class=\"inp\" style=\"width:auto;\"  >";
		mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 and `saghf_kharid`>=".$conf->min_saghf_kharid." order by `name`",$q);
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
		$out = '<tr><th colspan="4">خدمات به صورت فول برد <input type="checkbox" onclick="resetOrNotKh(this);" ></th></tr>'."\n";
		$hotel_id = (int) $hotel_id;
		$kh = khadamat_class::loadKhadamats($hotel_id);
		$kh_js = '';
		for($i=0;$i<count($kh );$i++)
		{
			$voroodi = '';
			$jtick = '';
			$khorooji = '';
			if($kh[$i]['typ']==1)
				$jtick = "onclick='kh_check(\"".$kh[$i]['id']."\");'";
			if($kh[$i]['voroodi'] )
			{
				$view1 = ($kh[$i]['aval_ekhtiari']==1)?'':'checked="checked"';
				$voroodi = "<span>اول</span><input $view1  $jtick type='checkbox' name='kh_v_".$kh[$i]['id']."' id='kh_v_".$kh[$i]['id']."' >";
				$kh_js .="\ndocument.getElementById('kh_v_".$kh[$i]['id']."').checked=".(($kh[$i]['aval_ekhtiari']==1)?'false;':'true;');
				if($kh[$i]['typ']==1)
					$kh_js .="\nkh_check(\"".$kh[$i]['id']."\");";
			}
			if($kh[$i]['khorooji'])
			{
				$view2 = ($kh[$i]['aval_ekhtiari']==2)?'':'checked="checked"';
				$khorooji = "<span>آخر</span><input $view2 $jtick type='checkbox' name='kh_kh_".$kh[$i]['id']."' id='kh_kh_".$kh[$i]['id']."' >";
				$kh_js .="\ndocument.getElementById('kh_kh_".$kh[$i]['id']."').checked=".(($kh[$i]['aval_ekhtiari']==2)?'false;':'true;');
				if($kh[$i]['typ']==1)
					$kh_js .="\nkh_check(\"".$kh[$i]['id']."\");";
			}
			$view = '';
			if($view1=='checked="checked"' || $view2=='checked="checked"')
				$view = 'checked="checked"';
			$inp = 	"<input $view style='display:none;' type='checkbox' name='kh_ch_".$kh[$i]['id']."' id='kh_ch_".$kh[$i]['id']."' > $voroodi $khorooji";
			if($kh[$i]['typ']==0)
			{
				$inp = 	"تعدادروزانه:<input type='text' class='inp' style='width:30px;' name='kh_txt_".$kh[$i]['id']."' id='kh_txt_".$kh[$i]['id']."'  value='".((isset($_REQUEST['kh_'.$kh[$i]['id']]))?$_REQUEST['kh_'.$kh[$i]['id']]:0)."' > $voroodi $khorooji";
			}
			$ghimat = "<div style='display:none;' >قیمت‌واحد:<input class='inp' style='width:70px' name='kh_gh_".$kh[$i]['id']."' value='".((isset($_REQUEST['kh_'.$kh[$i]['ghimat']]))?$_REQUEST['kh_'.$kh[$i]['ghimat']]:$kh[$i]['ghimat'])."' > </div>";
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
	$h_id = ((isset($_REQUEST['hotel_id']))?$hotel_id:(int)$_REQUEST['h_id']);
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
	if($isAdmin)
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
			$rooms = room_class::loadOpenRooms($aztarikh,$shab,$shab_reserve,$rooz_reserve,$hotel_id);
	}
	if($hotel_id>0 && count($rooms)>0)
	{
		
		$output='<br/><table border="1" style="width:80%;" >';
		$output .='<tr><th>انتخاب</th><th>ظرفیت</th><th>موجود</th> <th>قیمت</th></tr>';
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
					$output .= "<td>بطور خودکار محاسبه می شود<input style='display:none;' class='inp' name='room_gh_".$rooms[$i]['room_typ_id']."' value='".$rooms[$i]['ghimat']."' > </td><tr>";
				}
				$output .= "<tr><td colspan='3'>تعداد درخواستی:<input type='text' value='".((isset($_REQUEST['tedad_otagh']))?$_REQUEST['tedad_otagh']:1)."'  name='tedad_otagh' class='inp' ></td>";
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
					$output .= "<td>بطور خودکار محاسبه می شود<input style='display:none;' class='inp' name='room_gh_".$rooms[$i]['room_typ_id']."' value='".$rooms[$i]['ghimat']."' > </td></tr><tr id='tr_$i' style='display:".showHide($room_numbers).";' >";
					$output .= "<td style='text-align:right;' colspan='40' width='99%' > ";
					for($j=0;$j<count($room_numbers);$j++)
					{
						$shomare_otagh = new room_class($room_numbers[$j]);
						$check_box = ((isset($_REQUEST['otagh_'.$room_numbers[$j]]))?'checked="checked"':'');
						$tmp_check .= ((isset($_REQUEST['otagh_'.$room_numbers[$j]]))?(($tmp_check!='')?',':'').$room_numbers[$j]:'');
						$output .= $shomare_otagh->name."<input type='checkbox' $check_box name='otagh_".$room_numbers[$j]."' id='otagh_".$room_numbers[$j]."' value='".$room_numbers[$j]."'  onclick='addNumber(this,".$room_numbers[$j].")' >&nbsp;&nbsp;&nbsp;";
					}
					$output .= "</td>\n</tr>";
					
				}
				$output .= "<tr><td colspan='3'>تعداد درخواستی بصورت خودکار محاسبه می شود</td>";
			}
			$output .="<td >تعدادنفرات:<input onkeypress='return numbericOnKeypress(event);' type='text' id='tedad_nafarat' name='tedad_nafarat' value='".((isset($_REQUEST['tedad_nafarat']))?$_REQUEST['tedad_nafarat']:1)."' class='inp' onblur='calculate_nafar();' ></td></tr>";
			$output .= "<tr><td colspan='3' >مبلغ تور:<input onkeyup='monize(this);' class='inp' type='text' name='tour_mablagh' id='tour_mablagh' value='".((isset($_REQUEST['tour_mablagh']))?$_REQUEST['tour_mablagh']:"")."' ></td><td>مبلغ  بلیــت  رفت:<input onkeyup='monize(this);' class='inp' type='text' name='belit_mablagh_1' id='belit_mablagh_1' value='".((isset($_REQUEST['belit_mablagh_1']))?$_REQUEST['belit_mablagh_1']:"")."' ><br/>مبلغ‌بلیت‌برگشت:<input onkeyup='monize(this);' class='inp' type='text' name='belit_mablagh_2' id='belit_mablagh_2' value='".((isset($_REQUEST['belit_mablagh_2']))?$_REQUEST['belit_mablagh_2']:"")."' ></td></tr>";
			$sel_aj = ((isset($_REQUEST['ajans_id']))?$_REQUEST['ajans_id']:0);
			$output .= "<tr><td>نام دفتر</td><td>".loadDaftar($daftar_id)."</td><td>نام آژانس</td><td colspan='1' >". loadAjans($daftar_id,$sel_aj)."</td>";
			$output .= "</tr>";
			$sel_ajBelit_1 = ((isset($_REQUEST['ajans_idBelit_1']))?$_REQUEST['ajans_idBelit_1']:0);
			$output .= "<tr><td>دفتر </td><td>".loadDaftarBelit($daftar_idBelit_1,1)."</td><td>شرکت بلیت رفت</td><td colspan='1' >". loadAjansBelit($daftar_idBelit_1,$sel_ajBelit_1,1)."</td>";
			$output .= "</tr>";
			$sel_ajBelit_2 = ((isset($_REQUEST['ajans_idBelit_2']))?$_REQUEST['ajans_idBelit_2']:0);
			$output .= "<tr><td>دفتر </td><td>".loadDaftarBelit($daftar_idBelit_2,2)."</td><td>شرکت بلیت برگشت</td><td colspan='1' >". loadAjansBelit($daftar_idBelit_2,$sel_ajBelit_2,2)."</td>";
			$output .= "</tr>";
			//$output .= "</tr>";
			$output .=loadKhadamat($hotel_id);
			//$output .="<td>ناهار ورودی<input type='checkbox' <input class='inp'  name=\"nahar_voroodi\" id=\"nahar_voroodi\" ".((isset($_REQUEST['nahar_voroodi']))?'checked="checked"':'')." ></td><td>ناهار خروجی<input type='checkbox' <input class='inp'  name=\"nahar_khorooji\" id=\"nahar_khorooji\" ".((isset($_REQUEST['nahar_khorooji']))?'checked="checked"':'')." ></td>";
			if($shab_reserve || ((int)$_REQUEST['mod']==2 && isset($_REQUEST['shabreserve_gh']) )  )
			{
				$output .= "<tr><td colspan='4' >شب-رزرودارد <input style='display:none;' class='inp'  name=\"shabreserve_gh\" id=\"shabreserve_gh\" type=\"text\"".((isset($_REQUEST['shabreserve_gh']))?$_REQUEST['shabreserve_gh']:0)." >";
			}
			else
			{
				$output .= '<td colspan="4" >شب-رزروندارد';
			}
			if($rooz_reserve || ((int)$_REQUEST['mod']==2 && isset($_REQUEST['roozreserve_gh']) ) )
			{
				$output .= "&nbsp;&nbsp;&nbsp;&nbsp;روز-رزرودارد <input style='display:none;'  class='inp' name=\"roozreserve_gh\" id=\"roozreserve_gh\" type=\"text\"".((isset($_REQUEST['roozreserve_gh']))?$_REQUEST['roozreserve_gh']:0)." > </td>";
			}
			else
			{
				$output .= '&nbsp;&nbsp;&nbsp;&nbsp;روز-رزروندارد</td></tr>';
			}
			$jcheck = 'radioChecked';
			if($conf->room_select)
				$jcheck = 'checkboxChecked';
			$output .= "<tr><td colspan='4' ><input id='reserve_but' name='reserve_but' type='button' onclick=\"if($jcheck()){getAjaxInfo();}else{alert('لطفاً اطلاعات را کامل وارد کنید');}\" value='رزرو' id='reserve_btn'  class='inp' ><input value='$tmp_check' name='room_id_tmp' id='room_id_tmp' type='hidden' ></td></tr>";
			
			$output .= '</table>';
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
			var check_otagh = false;
			var check_roompick = false;
			var isPick = false;
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
				check_otagh = out;
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
				check_roompick = out;

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
					mehrdad_ajaxFunction(check_ghimat,'getghimat',hotel_id,aztarikh,shab,nafar,room,daftar_id,ajans_id);
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
						var ghimat = tour-(belit1+belit2);
						if(ghimat>0)
						{
							response = response.split(',');
							var ghimat_limit = parseInt(trim(response[0]),10);
							var saghf_kharid = (response[1])?parseInt(trim(response[1]),10):-2;
							//alert(saghf_kharid);
							if(check_otagh || !isPick)
								if(ghimat<=saghf_kharid && saghf_kharid>0)
								{
									if( !isPick || ghimat>=ghimat_limit || !khadamat)
									{
										document.getElementById('frm1').action='reserve2.php';
										document.getElementById('frm1').submit();
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
				if(document.getElementById('shab').options[document.getElementById('shab').selectedIndex].text=='0.5')
				{
					 bool = false;
					if (document.getElementById('shabreserve').checked)
						if(!document.getElementById('roozreserve').checked)
							bool = true;
						else
							alert('در این حالت روز رزرو نباید انتخاب شود');
					else
						alert('در این حالت شب رزرو باید انتخاب شود');
				}
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
					if(document.getElementById('datepicker6').value!='')
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
			<?php echo $GLOBALS['KH_JS']; ?>
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
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
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
                        </form>
			<?php
                                //echo jdate("F",strtotime($da));
                                //echo $outvazeat ;
                        ?>

			<br/>
			<br/>
			<form id='frm1'  method='GET' >
			<table border='1' >
				<tr>
					<th>نام هتل</th>
					<th>تاریخ</th>
					<th>مدت اقامت</th>
					<th>شب-‌رزرو<br/>(نیم شارژ ورودی)</th>
					<th>روز-‌رزرو<br/>(نیم شارژ خروجی)</th>
					<th>جستجو</th>
				</tr>
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
         					   <input value="<?php echo ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdate($aztarikh):audit_class::hamed_pdate(date("Y-m-d H:i:s"))); ?>" type="text" name='aztarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td>
						<select  class='inp' name='shab' id='shab' >
							<?php  echo loadNumber((isset($_REQUEST['shab']))?$_REQUEST['shab']:1); ?>
						</select>
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
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
						
					</td>					
				</tr>
			</table>
			<?php echo $output.' '.$msg; ?>
			</form>
			<script language="javascript" >
				calculate_nafar();
			</script>
		</div>
	</body>
</html>
