<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart1 = '';
	$tarikh =((isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack_1($_REQUEST['tarikh']):date("Y-m-d"));
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart1.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	if (isset($_REQUEST["hotel_id"]))
                $hotel_id_new = $_REQUEST["hotel_id"];
	else
		$hotel_id_new = -1;
	if (isset($_REQUEST["nobat"]))
                $nobat = $_REQUEST["nobat"];
	else
		$nobat = -1;
	$comb_nobat = "<select class='inp' id=\"nobat\" name=\"nobat\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
	$comb_nobat .= "</select>";
	if (isset($_REQUEST["combo_kh"]))
	{
                $kh_mehman = $_REQUEST["combo_kh"];
		if ($kh_mehman==2)
		{
			////transfer
			$select = "";
			$comb_nobat = "<select class='inp' id=\"nobat\" name=\"nobat\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
			mysql_class::ex_sql("select * from `driver` order by `id`",$q);
			while($r = mysql_fetch_array($q))
			{
				if((int)$r["id"]== (int)$nobat)
					$select = "selected='selected'";
				else
					$select = "";
				$comb_nobat .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
				$comb_nobat .= $r["name"]."\n";
				$comb_nobat .= "</option>\n";
			}
			$comb_nobat .= "</select>";	
		}
		elseif ($kh_mehman==1)
		{	
		////gasht
			$select = "";
			$comb_nobat = "<select class='inp' id=\"nobat\" name=\"nobat\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
				mysql_class::ex_sql("select * from `nobat` order by `id`",$q);
				while($r = mysql_fetch_array($q))
				{
					if((int)$r["id"]== (int)$nobat)
						$select = "selected='selected'";
					else
						$select = "";
					$comb_nobat .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
					$comb_nobat .= $r["name"]."\n";
					$comb_nobat .= "</option>\n";
				}
			$comb_nobat .= "</select>";
		}
		elseif ($kh_mehman==3)
		{	
		////cinema
			/*$comboTyp_sobh = "";
			$comboTyp_asr = "";
			$comb_nobat = "<select class='inp' id=\"nobat\" name=\"nobat\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
				if((int)$nobat==1)
					$comboTyp_sobh = "selected='selected'";
				elseif((int)$nobat==2)
					$comboTyp_asr = "selected='selected'";
				else
					echo '';
				$comb_nobat .= "<option value='1' $comboTyp_sobh>نوبت یک</option>\n";
				$comb_nobat .= "<option value='2' $comboTyp_asr>نوبت دو</option>\n";
			$comb_nobat .= "</select>";*/
			$select = "";
			$comb_nobat = "<select class='inp' id=\"nobat\" name=\"nobat\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
				mysql_class::ex_sql("select * from `nobat` order by `id`",$q);
				while($r = mysql_fetch_array($q))
				{
					if((int)$r["id"]== (int)$nobat)
						$select = "selected='selected'";
					else
						$select = "";
					$comb_nobat .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
					$comb_nobat .= $r["name"]."\n";
					$comb_nobat .= "</option>\n";
				}
			$comb_nobat .= "</select>";
		}
		elseif($kh_mehman==4)
		{
			$select = "";
			$comb_nobat = "<select class='inp' id=\"nobat\" name=\"nobat\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
				mysql_class::ex_sql("select * from `nobat` order by `id`",$q);
				while($r = mysql_fetch_array($q))
				{
					if((int)$r["id"]== (int)$nobat)
						$select = "selected='selected'";
					else
						$select = "";
					$comb_nobat .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
					$comb_nobat .= $r["name"]."\n";
					$comb_nobat .= "</option>\n";
				}
			$comb_nobat .= "</select>";
		}
		else
			$comb_nobat = '';
	}
	else
		$kh_mehman = -1;
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
		$combo_hotel .= "<select class='inp' id=\"hotel_id\" name=\"hotel_id\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel` where `id` in $shart1 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$hotel_id_new)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_hotel .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
		        $combo_hotel .= $r["name"]."\n";
		        $combo_hotel .= "</option>\n";
		}
		$combo_hotel .= "</select>";
	$combo_hotel .= "</form>";
	$select_cinema = "";
	$select_akasi = "";
        $select_gasht = "";
	$select_transfer = "";
	$comboTarget = "";
	$combo_kh = '';
	$combo_kh .= "<form name=\"selKhadamat\" id=\"selKhadamat\" method=\"POST\">";
		$combo_kh  .= "<select class='inp' id=\"combo_kh\" name=\"combo_kh\"  onchange=\"document.getElementById('selKhadamat').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
			if((int)$kh_mehman==1)
			        $select_gasht = "selected='selected'";
			elseif((int)$kh_mehman==2)
			        $select_transfer = "selected='selected'";
			elseif ((int)$kh_mehman==3)
				$select_cinema = "selected='selected'";
			elseif ((int)$kh_mehman==4)
				$select_akasi = "selected='selected'";
			else
				echo '';
			$combo_kh .= "<option value='1' $select_gasht>گشت</option>\n";
			$combo_kh .= "<option value='2' $select_transfer>ترانسفر</option>\n";
			$combo_kh .= "<option value='3' $select_cinema>سینما</option>\n";
			$combo_kh .= "<option value='4' $select_akasi>عکاسخانه</option>\n";
		$combo_kh .= "</select>";
		$combo_kh .= '<input type="hidden" name="hotel_id" id="hotel_id" value="'.$hotel_id_new.'"/>';
		$combo_kh .= '<input type="hidden" name="mod" id="mod" value="0">';
	$combo_kh .= "</form>";
	$out = '';
	if(isset($_REQUEST['mod']) and $_REQUEST['mod']==1)
	{
		if((int)$kh_mehman==1)
		{
			$i = 1;
			$nobat_kh = '--';
			$kh_tarikh = '--';
			//$tarikh = date("Y-m-d");
			//$tatarikh = date("Y-m-d");			
			$out = '<table cellpadding="0" cellspacing="0" width="98%" style="font-size:13px;border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>سرگروه</th><th>شماره اتاق</th><th>نفرات</th><th>شماره-رزرو</th><th>تلفن</th><th>دفتر</th><th>آژانس</th><th>ورود</th><th>خروج</th><th>نوبت</th><th>تاریخ</th></tr>';
			if ($nobat!=-1)
				mysql_class::ex_sql("select * from `khadamat_gasht` where `reserve_id`>0 and date(`tarikh`) = date('$tarikh') and `typ`='$nobat'",$q);
			else
				mysql_class::ex_sql("select * from `khadamat_cinema` where `reserve_id`>0 and date(`tarikh`) = date('$tarikh') ",$q);
			while($r=mysql_fetch_array($q))
			{
				$res_id = $r['reserve_id'];			
				$row_style = 'class="showgrid_row_odd"';
				$hotel_tmp = new hotel_reserve_class();
				$hotel_tmp->loadByReserve($r['reserve_id']);
				$ajans = new ajans_class($hotel_tmp->ajans_id);
				$daftar = new daftar_class($ajans->daftar_id);
				$room = room_det_class::loadDetByReserve_id((int)$r['reserve_id']);
				$rooms = '';
				for($j=0;$j<count($room['rooms']);$j++)
				{
					$tmp_room = new room_class($room['rooms'][$j]['room_id']);
					$rooms.=$tmp_room->name.(($j<count($room['rooms'])-1)?' , ':'');
				}
				$tmp_r = room_det_class::loadByReserve((int)$r['reserve_id']);
				$tmp_r = $tmp_r[0];
				$nafar = $tmp_r[0]->nafar;
				$room_aztarikh1 = audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[0]->aztarikh)));
				$room_tatarikh1 =audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh)));
				$status = '';
				$room_aztarikh = date("Y-m-d",strtotime($tmp_r[0]->aztarikh));
				$room_tatarikh = date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh));
				$kh_tarikh = date("Y-m-d",strtotime($r['tarikh']));
				if($i%2==0)
					$row_style = 'class="showgrid_row_even"';
				$kh_tarikh = audit_class::hamed_pdate(date("Y-m-d",strtotime($r['tarikh'])));
				$nobat_kh = $r['typ'];
				mysql_class::ex_sql("select `name` from `nobat` where `id`='$nobat_kh'",$q_no);
				if($r_no=mysql_fetch_array($q_no))
					$nobat_name = $r_no['name'];
				else
					$nobat_name = '--';
				$out.="<tr $row_style >";
				$out .="<td class='showgrid_row_td' >$i</td><td class='showgrid_row_td' >".$hotel_tmp->lname."</td><td class='showgrid_row_td' width='30%'>$rooms</td><td class='showgrid_row_td' >$nafar</td><td class='showgrid_row_td' >".$r['reserve_id']."</td><td class='showgrid_row_td' >".$hotel_tmp->tozih."</td><td class='showgrid_row_td' >".$daftar->name."</td><td class='showgrid_row_td' >".$ajans->name."</td><td class='showgrid_row_td'>$room_aztarikh1</td><td class='showgrid_row_td'>$room_tatarikh1</td><td class='showgrid_row_td' >$nobat_name</td><td class='showgrid_row_td' >$kh_tarikh</td>";
				$out.='</tr>';
				$i++;
			}
			$out .="</table><br/>\n";
		}
		elseif((int)$kh_mehman==2)
		{
			$i = 1;
			$nobat_kh = '--';
			$kh_tarikh = '--';
			//$tarikh = date("Y-m-d");
			$tatarikh = date("Y-m-d");
			if ($nobat>0)
				mysql_class::ex_sql("select `reserve_id` from `khadamat_transfer` where `reserve_id`>0 and date(`timeKh`) = '$tarikh' and `driverName`='$nobat'",$q_trans);
			else
				mysql_class::ex_sql("select `reserve_id` from `khadamat_transfer` where `reserve_id`>0 and date(`timeKh`) = '$tarikh'",$q_trans);
			$tmp ='';
			while ($r_trans = mysql_fetch_array($q_trans))
			{
				$r_hotel = room_class::loadHotelByReserve($r_trans['reserve_id']);
				if ($hotel_id_new==$r_hotel)
					$tmp .=($tmp==''? '':',' ).$r_trans['reserve_id'];
			}
			if ($tmp!='')
				$shart_res = "`reserve_id` in ($tmp) and ";
			else
				$shart_res = "1=0 and ";
			mysql_class::ex_sql("select `id` from `khadamat` where `name`='ترانسفر' and `hotel_id`='$hotel_id_new' order by `name`",$q);
			if ($r = mysql_fetch_array($q))
				$khadamat_id = $r['id'];
			else
				$khadamat_id = -1;
			$out = '<table cellpadding="0" cellspacing="0" width="100%" style="font-size:13px;border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>سرگروه</th><th>شماره اتاق</th><th>نفرات</th><th>شماره-رزرو</th><th>تلفن</th><th> ورود</th><th>خروج</th><th>نوع</th><th>راننده</th><th>مبدا/مقصد</th><th>تاریخ</th><th>توضیحات</th></tr>';
			if(isset($khadamat_id) && isset($tarikh))
				mysql_class::ex_sql("select * from `khadamat_det` where `reserve_id`>0 and DATE(`tarikh`) = date('$tarikh') and $shart_res `khadamat_id`=$khadamat_id order by `reserve_id`",$q);
			else
				mysql_class::ex_sql("select * from `khadamat_det` where $shart_res `reserve_id`>0 and 1=0",$q);
			while($r=mysql_fetch_array($q))
			{
				$res_id = $r['reserve_id'];
				$row_style = 'class="showgrid_row_odd"';
				$hotel_tmp = new hotel_reserve_class();
				$hotel_tmp->loadByReserve($r['reserve_id']);
				$ajans = new ajans_class($hotel_tmp->ajans_id);
				$daftar = new daftar_class($ajans->daftar_id);
				$room = room_det_class::loadDetByReserve_id((int)$r['reserve_id']);
				$rooms = '';
				for($j=0;$j<count($room['rooms']);$j++)
				{
					$tmp_room = new room_class($room['rooms'][$j]['room_id']);
					$rooms.=$tmp_room->name.(($j<count($room['rooms'])-1)?' , ':'');
				}
				$tmp_r = room_det_class::loadByReserve((int)$r['reserve_id']);
				$tmp_r = $tmp_r[0];
				$nafar = $tmp_r[0]->nafar;
				$room_aztarikh1 = audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[0]->aztarikh)));
				$room_tatarikh1 =audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh)));
				$status = '';
				$room_aztarikh = date("Y-m-d",strtotime($tmp_r[0]->aztarikh));
				$room_tatarikh = date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh));
				$kh_tarikh = date("Y-m-d",strtotime($r['tarikh']));
				if($kh_tarikh==$room_aztarikh)
				{
					$typ_kh = 1;
					$status = 'ورودی';
				}
				else if ($kh_tarikh==$room_tatarikh)
				{
					$typ_kh = 2;
					$status = 'خروجی';
				}
				else
				{
					$typ_kh = 3;
					$status = 'میانی';
				}
				mysql_class::ex_sql("select * from `khadamat_transfer` where `reserve_id`='$res_id' and `typ`='$typ_kh'",$q_kh);
				if($r_kh=mysql_fetch_array($q_kh))
				{
					$driver_id = $r_kh['driverName'];
					mysql_class::ex_sql("select * from `driver` where `id`='$driver_id'",$q_dr);
					if($r_dr=mysql_fetch_array($q_dr))
						$nameDriver = $r_dr['name'];
					else
						$nameDriver = 'ناشناخته';
					$target_id = $r_kh['target_id'];
					if ($target_id==1)
						$target = 'ترمینال';
					elseif ($target_id==2)
						$target = 'راه آهن';
					elseif ($target_id==3)
						$target = 'فرودگاه';
					else
						$target ='';
					$transferTime = audit_class::hamed_pdate($r_kh['timeKh']);
					$stat_kh = 'خدمات ارائه شده';
					$toz_tra = (($r_kh['toz']!='')?($r_kh['toz']):('ندارد'));
				}
				else
				{
					$nameDriver ='--';
					$target ='--';
					$transferTime ='--';
					$stat_kh = 'خدمات ارائه نشده';
					$toz_tra = '';
				}
				if($i%2==0)
					$row_style = 'class="showgrid_row_even"';
				$out.="<tr $row_style >";
				$out .="<td class='showgrid_row_td' >$i</td><td class='showgrid_row_td' >".$hotel_tmp->lname."</td><td class='showgrid_row_td' width='30%'>$rooms</td><td class='showgrid_row_td' >$nafar</td><td class='showgrid_row_td' >".$r['reserve_id']."</td><td class='showgrid_row_td' >".$hotel_tmp->tozih."</td><td class='showgrid_row_td'>$room_aztarikh1</td><td class='showgrid_row_td'>$room_tatarikh1</td><td class='showgrid_row_td'>$status</td><td class='showgrid_row_td' >$nameDriver</td><td class='showgrid_row_td' >$target</td><td class='showgrid_row_td' >$transferTime</td><td class='showgrid_row_td' >$toz_tra</td>";
				$out.='</tr>';
				$i++;
			}
			$out .="</table><br/>\n";
		}
		elseif ((int)$kh_mehman==3)
		{
			$i = 1;
			$nobat_kh = '--';
			$kh_tarikh = '--';
			//$tarikh = date("Y-m-d");
			//$tatarikh = date("Y-m-d");
			
			$out = '<table cellpadding="0" cellspacing="0" width="95%" style="font-size:13px;border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>سرگروه</th><th>شماره اتاق</th><th>نفرات</th><th>شماره-رزرو</th><th>تلفن</th><th>دفتر</th><th>آژانس</th><th>ورود</th><th>خروج</th><th>نوبت</th><th>تاریخ</th></tr>';
			if ($nobat!=-1)
				mysql_class::ex_sql("select * from `khadamat_cinema` where `reserve_id`>0 and date(`tarikh`) = '$tarikh' and `typ`='$nobat'",$q);
			else
				mysql_class::ex_sql("select * from `khadamat_cinema` where `reserve_id`>0 and date(`tarikh`) = '$tarikh' ",$q);
			while($r=mysql_fetch_array($q))
			{
				$res_id = $r['reserve_id'];
				$row_style = 'class="showgrid_row_odd"';
				$hotel_tmp = new hotel_reserve_class();
				$hotel_tmp->loadByReserve($r['reserve_id']);
				$ajans = new ajans_class($hotel_tmp->ajans_id);
				$daftar = new daftar_class($ajans->daftar_id);
				$room = room_det_class::loadDetByReserve_id((int)$r['reserve_id']);
				$rooms = '';
				for($j=0;$j<count($room['rooms']);$j++)
				{
					$tmp_room = new room_class($room['rooms'][$j]['room_id']);
					$rooms.=$tmp_room->name.(($j<count($room['rooms'])-1)?' , ':'');
				}
				$tmp_r = room_det_class::loadByReserve((int)$r['reserve_id']);
				$tmp_r = $tmp_r[0];
				$nafar = $tmp_r[0]->nafar;
				$room_aztarikh1 = audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[0]->aztarikh)));
				$room_tatarikh1 =audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh)));
				$status = '';
				$room_aztarikh = date("Y-m-d",strtotime($tmp_r[0]->aztarikh));
				$room_tatarikh = date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh));
				$kh_tarikh = date("Y-m-d",strtotime($r['tarikh']));
				if($i%2==0)
					$row_style = 'class="showgrid_row_even"';
				$kh_tarikh = audit_class::hamed_pdate(date("Y-m-d",strtotime($r['tarikh'])));
				$nobat_kh = $r['typ'];
				mysql_class::ex_sql("select `name` from `nobat` where `id`='$nobat_kh'",$q_no);
				if($r_no=mysql_fetch_array($q_no))
					$nobat_name = $r_no['name'];
				else
					$nobat_name = '--';
				$out.="<tr $row_style >";
				$out .="<td class='showgrid_row_td' >$i</td><td class='showgrid_row_td' >".$hotel_tmp->lname."</td><td class='showgrid_row_td' width='30%'>$rooms</td><td class='showgrid_row_td' >$nafar</td><td class='showgrid_row_td' >".$r['reserve_id']."</td><td class='showgrid_row_td' >".$hotel_tmp->tozih."</td><td class='showgrid_row_td' >".$daftar->name."</td><td class='showgrid_row_td' >".$ajans->name."</td><td class='showgrid_row_td'>$room_aztarikh1</td><td class='showgrid_row_td'>$room_tatarikh1</td><td class='showgrid_row_td' >$nobat_name</td><td class='showgrid_row_td' >$kh_tarikh</td>";
				$out.='</tr>';
				$i++;	
			}
			$out .="</table><br/>\n";
		}
		elseif ((int)$kh_mehman==4)
		{
			$i = 1;
			$nobat_kh = '--';
			$kh_tarikh = '--';
			$out = '<table cellpadding="0" cellspacing="0" width="95%" style="font-size:13px;border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>سرگروه</th><th>شماره اتاق</th><th>نفرات</th><th>شماره-رزرو</th><th>تلفن</th><th>دفتر</th><th>آژانس</th><th>ورود</th><th>خروج</th><th>نوبت</th><th>تاریخ</th></tr>';
			if ($nobat!=-1)
				mysql_class::ex_sql("select * from `khadamat_akasi` where `reserve_id`>0 and date(`tarikh`) = '$tarikh' and `typ`='$nobat'",$q);
			else
				mysql_class::ex_sql("select * from `khadamat_akasi` where `reserve_id`>0 and date(`tarikh`) = '$tarikh' ",$q);
//echo "select * from `khadamat_akasi` where `reserve_id`>0 and date(`tarikh`) = '$tarikh' ";
			while($r=mysql_fetch_array($q))
			{
				$res_id = $r['reserve_id'];
				$row_style = 'class="showgrid_row_odd"';
				$hotel_tmp = new hotel_reserve_class();
				$hotel_tmp->loadByReserve($r['reserve_id']);
				$ajans = new ajans_class($hotel_tmp->ajans_id);
				$daftar = new daftar_class($ajans->daftar_id);
				$room = room_det_class::loadDetByReserve_id((int)$r['reserve_id']);
				$rooms = '';
				for($j=0;$j<count($room['rooms']);$j++)
				{
					$tmp_room = new room_class($room['rooms'][$j]['room_id']);
					$rooms.=$tmp_room->name.(($j<count($room['rooms'])-1)?' , ':'');
				}
				$tmp_r = room_det_class::loadByReserve((int)$r['reserve_id']);
				$tmp_r = $tmp_r[0];
				$nafar = $tmp_r[0]->nafar;
				$room_aztarikh1 = audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[0]->aztarikh)));
				$room_tatarikh1 =audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh)));
				$status = '';
				$room_aztarikh = date("Y-m-d",strtotime($tmp_r[0]->aztarikh));
				$room_tatarikh = date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh));
				$kh_tarikh = date("Y-m-d",strtotime($r['tarikh']));
				if($i%2==0)
					$row_style = 'class="showgrid_row_even"';
				$kh_tarikh = audit_class::hamed_pdate(date("Y-m-d",strtotime($r['tarikh'])));
				$nobat_kh = $r['typ'];
				mysql_class::ex_sql("select `name` from `nobat` where `id`='$nobat_kh'",$q_no);
				if($r_no=mysql_fetch_array($q_no))
					$nobat_name = $r_no['name'];
				else
					$nobat_name = '--';		
				$out.="<tr $row_style >";
				$out .="<td class='showgrid_row_td' >$i</td><td class='showgrid_row_td' >".$hotel_tmp->lname."</td><td class='showgrid_row_td' width='30%'>$rooms</td><td class='showgrid_row_td' >$nafar</td><td class='showgrid_row_td' >".$r['reserve_id']."</td><td class='showgrid_row_td' >".$hotel_tmp->tozih."</td><td class='showgrid_row_td' >".$daftar->name."</td><td class='showgrid_row_td' >".$ajans->name."</td><td class='showgrid_row_td'>$room_aztarikh1</td><td class='showgrid_row_td'>$room_tatarikh1</td><td class='showgrid_row_td' >$nobat_name</td><td class='showgrid_row_td' >$kh_tarikh</td>";
				$out.='</tr>';
				$i++;	
			}
			$out .="</table><br/>\n";
		}
		else
			$out = '<table cellpadding="0" cellspacing="0" width="100%" style="font-size:13px;border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>سرگروه</th><th>شماره اتاق</th><th>نفرات</th><th>شماره-رزرو</th><th>تلفن</th><th> ورود</th><th>خروج</th><th>نوع</th><th>راننده</th><th>مبدا/مقصد</th><th>تاریخ</th></tr></table>';
	}
	
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript">
		function send_search()
		{
			document.getElementById('frm1').submit();
		}
		function getPrint()
		{
			document.getElementById('div_main').style.width = '19cm';
			$("#print-bu").hide();
			$("#search_tb").hide();
			window.print();
			document.getElementById('div_main').style.width = 'auto';
			//$("#print-bu").show();
			//$("#search_tb").show();
		}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#tarikh").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
	    	</script>
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
			$("#datepicker7").datepicker({
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
گزارش خدمات ارائه شده به میهمان
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			
			<table id='search_tb' width="80%" style='font-size:14px;font-weight:bold;'>
				<tr valign="bottom" >
					<td>	نام هتل
					</td>
					<td>	
						<?php echo $combo_hotel;?>
					</td>
					<td>
							خدمات
					</td>
					<td>	
						<?php echo $combo_kh;?>
					</td>
			<form id='frm1'  method='GET' >
					<td>
							نوبت
					</td>
					<td>	
						<?php echo $comb_nobat?>
					</td>
					<td>
						تاریخ
					</td>
					<td >
						<input class="inp" readonly="readonly" type="text" name="tarikh" id="tarikh" value="<?php echo audit_class::hamed_pdate($tarikh); ?>"  >
					</td>
					<td>
						<input type='hidden' name='hotel_id' id='hotel_id' value="<?php echo $hotel_id_new;?>">
						<input type='hidden' name='combo_kh' id='combo_kh' value="<?php echo $kh_mehman;?>">
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
						<input type='hidden' name='mod' id='mod' value="1">
					</td>	
			</form>				
				</tr>
			</table>
			<div id="div_main">
			<br/>	
			<br/>
			<?php echo $out; ?>
			<br/>
			</div>
			<input type="button" id="print-bu" value="چاپ" class="inp" onclick="getPrint();" >
		</div>
	</body>
</html>
