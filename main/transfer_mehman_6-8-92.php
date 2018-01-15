<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	////ENTEKHAB HOTEL
	$user_id = (int)$_SESSION['user_id'];
	if( isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else
		$h_id = -1;
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart1 = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart1.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	$select ='';
	$combo_hotel = "";
	$combo_hotel .= "هتل : <select class='inp' name=\"h_id\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
	mysql_class::ex_sql("select * from `hotel` where `id` in $shart1 order by `name`",$q);
	while($r = mysql_fetch_array($q))
	{
		if((int)$r["id"]== (int)$h_id)
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
/////END HOTEL
	mysql_class::ex_sql("select `id` from `khadamat` where `name`='ترانسفر' and `hotel_id`='$h_id' order by `name`",$q);
	if ($r = mysql_fetch_array($q))
		$khadamat_id = $r['id'];
	else
		$khadamat_id = -1;
	$tarikh =((isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack_1($_REQUEST['tarikh']):date("Y-m-d"));
	if( isset($_REQUEST['stat_id']))
		$stat_id = $_REQUEST['stat_id'];
	else
		$stat_id = -1;
	if( isset($_REQUEST['target_id']))
		$target_id = $_REQUEST['target_id'];
	else
		$target_id = -1;
	if( isset($_REQUEST['sargrooh_id']))
		$sargrooh_id = $_REQUEST['sargrooh_id'];
	else
		$sargrooh_id = -1;
	if( isset($_REQUEST['comboTyp']))
		$comboTyp = $_REQUEST['comboTyp'];
	else
		$comboTyp = -1;
	$nameDriver = '';
	$transferTime ='';
	if( isset($_REQUEST['sargrooh_id']))
	{
		$sargrooh_id = $_REQUEST['sargrooh_id'];
		if( isset($_REQUEST['nameDriver']))
			$nameDriver = $_REQUEST['nameDriver'];
		else
			$nameDriver = -1;
		if( isset($_REQUEST['toz']))
			$toz= $_REQUEST['toz'];
		else
			$toz = 'ندارد';
		if( isset($_REQUEST['target_id']))
			$target_id = $_REQUEST['target_id'];
		else
			$target_id = -1;
		if( isset($_REQUEST['transferTime']))
			$transferTime = $_REQUEST['transferTime'];
		else
			$transferTime = -1;
		$time_kh = $tarikh.' '.$transferTime;
		if ($comboTyp==1)
		{
			mysql_class::ex_sql("select `aztarikh` from `room_det` where `reserve_id`='$sargrooh_id'",$q);
			if ($r = mysql_fetch_array($q))
			{
				$aztarikh = $r['aztarikh'];
				mysql_class::ex_sqlx("UPDATE `khadamat_det` SET `isUsed` = '1' WHERE `khadamat_id`='$khadamat_id' and `tarikh`='$aztarikh'");
				mysql_class::ex_sqlx("INSERT INTO `khadamat_transfer` (`id`, `khadamat_id`, `reserve_id`, `driverName`, `target_id`, `timeKh`,`typ`,`toz`) VALUES (NULL, '$khadamat_id', '$sargrooh_id', '$nameDriver', '$target_id', '$time_kh','$comboTyp','$toz')");
				$shomare_sanad = sanadzan_class::getShomareSanad();
				$sanad_driver = sanadzan_class::sanad_transfer($shomare_sanad,$nameDriver,$sargrooh_id,$user_id);
			}
		}
		elseif ($comboTyp==2)
		{
			mysql_class::ex_sql("select `tatarikh` from `room_det` where `reserve_id`='$sargrooh_id'",$q);
			if ($r = mysql_fetch_array($q))
			{
				$tatarikh = $r['tatarikh'];
				mysql_class::ex_sqlx("UPDATE `khadamat_det` SET `isUsed` = '1' WHERE `khadamat_id`='$khadamat_id' and `tarikh`=$tatarikh");
				mysql_class::ex_sqlx("INSERT INTO `khadamat_transfer` (`id`, `khadamat_id`, `reserve_id`, `driverName`, `target_id`, `timeKh`,`typ`,`toz`) VALUES (NULL, '$khadamat_id', '$sargrooh_id', '$nameDriver', '$target_id', '$time_kh','$comboTyp','$toz')");
				$shomare_sanad = sanadzan_class::getShomareSanad();
				$sanad_driver = sanadzan_class::sanad_transfer($shomare_sanad,$nameDriver,$sargrooh_id,$user_id);
			}
		}
		else
			echo '';
		
	}
	else
		$stat_id = -1;

///////START sargrooh
	$aztarikh = date("Y-m-d");
	$tatarikh = date("Y-m-d");
	mysql_class::ex_sql("select `reserve_id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) <= '$aztarikh' and date(`tatarikh`) > '$aztarikh') or (date(`aztarikh`) < '$tatarikh' and date(`tatarikh`) > '$tatarikh')) group by `reserve_id`",$q);

	$tmp ='';
	if(isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else
		$h_id = -1;
	while ($r = mysql_fetch_array($q))
	{
		$r_hotel = room_class::loadHotelByReserve($r['reserve_id']);
		if ($h_id==$r_hotel)
			$tmp .=($tmp==''? '':',' ).$r['reserve_id'];
	}
	if ($tmp!='')
		$shart_res = "`reserve_id` in ($tmp) and ";
	else
		$shart_res = "1=0 and ";
	$select_sargrooh ='';
	$c_id = 0;
	$now = date('Y/m/d');
	$combo_sargrooh = "";
	$combo_sargrooh .= "<select class='inp' name=\"sargrooh_id\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
	//mysql_class::ex_sql("select `reserve_id`,`id` from `khadamat_det` where `reserve_id`>0 and DATE(`tarikh`) = date('$tarikh') and `khadamat_id`=$khadamat_id order by `reserve_id`",$q);
	//mysql_class::ex_sql("select `reserve_id`,`id` from `khadamat_det` where `reserve_id`>0 and $shart_res `khadamat_id`=$khadamat_id group by `reserve_id` order by `reserve_id`",$q);
	mysql_class::ex_sql("select * from `khadamat_det` where `reserve_id`>0 and DATE(`tarikh`) = date('$tarikh') and `khadamat_id`=$khadamat_id order by `reserve_id`",$q);
	while($r = mysql_fetch_array($q))
	{
		$res_id = $r['reserve_id'];
		mysql_class::ex_sql("select count(`id`) as `cid`from `khadamat_transfer` where `reserve_id`='$res_id' and `khadamat_id`=$khadamat_id order by `reserve_id`",$q_tra);
		if($r_tra = mysql_fetch_array($q_tra))
			$cid = $r_tra['cid'];
		if ($cid<2)		
			{
				$hotel_tmp = new hotel_reserve_class();
				$hotel_tmp->loadByReserve($r['reserve_id']);
				if((int)$r["id"]== (int)$sargrooh_id)
				{
					$select_sargrooh = "selected='selected'";
				}
				else
				{
					$select_sargrooh = "";
				}
				$combo_sargrooh .= "<option value=\"".(int)$r["reserve_id"]."\" $select_sargrooh   >\n";
				$combo_sargrooh .= $hotel_tmp->lname."\n";
				$combo_sargrooh .= "</option>\n";
			}
	}
	$combo_sargrooh .= "</select>";
///////END sargrooh
///////START Target
	$select_bus = "";
        $select_train = "";
	$select_plain = "";
	$comboTarget = "";
	$comboTarget .= "<select class='inp' id=\"target_id\" name=\"target_id\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		if((int)$target_id==1)
	                $select_bus = "selected='selected'";
	        elseif((int)$target_id==2)
	                $select_train = "selected='selected'";
		elseif ((int)$target_id==3)
			$select_plain = "selected='selected'";
		else
			echo '';
	        $comboTarget .= "<option value='1' $select_bus>ترمینال</option>\n";
		$comboTarget .= "<option value='2' $select_train>راه آهن</option>\n";
		$comboTarget .= "<option value='3' $select_plain>فرودگاه</option>\n";
	$comboTarget .= "</select>";
///////END Target
	$comboTyp_v = "";
	$comboTyp_kh = "";
	$comboTyp = "<select class='inp' id=\"comboTyp\" name=\"comboTyp\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		if((int)$comboTyp==1)
	                $comboTyp_v = "selected='selected'";
	        elseif((int)$comboTyp==2)
	                $comboTyp_kh = "selected='selected'";
		else
			echo '';
	        $comboTyp .= "<option value='1' $comboTyp_v>ورودی</option>\n";
		$comboTyp .= "<option value='2' $comboTyp_kh>خروجی</option>\n";
	$comboTyp .= "</select>";
	$select = "";
	$comboDriver = "<select class='inp' id=\"nameDriver\" name=\"nameDriver\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
	mysql_class::ex_sql("select * from `driver` order by `id`",$q);
	while($r = mysql_fetch_array($q))
	{
		if((int)$r["id"]== (int)$nameDriver)
	                $select = "selected='selected'";
	        else
	                $select = "";
		$comboDriver .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
	        $comboDriver .= $r["name"]."\n";
	        $comboDriver .= "</option>\n";
	}
	$comboDriver .= "</select>";
	
	
	$i = 1;
	//$out = '<table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>نام سرگروه</th><th>شماره اتاق</th><th>نفرات</th><th>شماره-رزرو</th><th>اطلاعات بیشتر</th><th>دفتر</th><th>آژانس</th><th>تعداد</th><th>تاریخ ورود</th><th>تاریخ خروج</th><th>نام راننده</th><th>مبدا/مقصد</th><th>ساعت</th><th>وضعیت</th></tr>';
	$out = '<table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>نام سرگروه</th><th>شماره اتاق</th><th>نفرات</th><th>شماره-رزرو</th><th>اطلاعات بیشتر</th><th>تاریخ ورود</th><th>تاریخ خروج</th><th>نوع</th><th>نام راننده</th><th>مبدا/مقصد</th><th>تاریخ</th><th>وضعیت</th><th>توضیحات</th></tr>';
	if(isset($khadamat_id) && isset($tarikh))
			//mysql_class::ex_sql("select * from `khadamat_det` where `reserve_id`>0 and $shart_res `khadamat_id`=$khadamat_id order by `reserve_id`",$q);
			mysql_class::ex_sql("select * from `khadamat_det` where `reserve_id`>0 and DATE(`tarikh`) = date('$tarikh') and `khadamat_id`=$khadamat_id order by `reserve_id`",$q);
		else
			mysql_class::ex_sql("select * from `khadamat_det` where `reserve_id`>0 and 1=0",$q);
	while($r=mysql_fetch_array($q))
	{
		$res_id = $r['reserve_id'];
		$row_style = 'class="showgrid_row_odd"';
		//mysql_class::ex_sql("select * from `khadamat_transfer` where `reserve_id`='$res_id' and `khadamat_id`='$khadamat_id'",$q_trans);
		//if (!($r_trans=mysql_fetch_array($q_trans)))
		//{
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
				$transferTime = audit_class::hamed_pdate_2($r_kh['timeKh']);
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
			//$out .="<td class='showgrid_row_td' >$i</td><td class='showgrid_row_td' >".$hotel_tmp->lname."</td><td class='showgrid_row_td' width='30%'>$rooms</td><td class='showgrid_row_td' >$nafar</td><td class='showgrid_row_td' >".$r['reserve_id']."</td><td class='showgrid_row_td' >".$hotel_tmp->tozih."</td><td class='showgrid_row_td' >".$daftar->name."</td><td class='showgrid_row_td' >".$ajans->name."</td><td class='showgrid_row_td' >".$r['tedad']."</td><td class='showgrid_row_td'>$room_aztarikh1</td><td class='showgrid_row_td'>$room_tatarikh1</td><td class='showgrid_row_td' >$nameDriver</td><td class='showgrid_row_td' >$target</td><td class='showgrid_row_td' >$transferTime</td><td class='showgrid_row_td' >$status</td>";
			$out .="<td class='showgrid_row_td' >$i</td><td class='showgrid_row_td' >".$hotel_tmp->lname."</td><td class='showgrid_row_td' width='30%'>$rooms</td><td class='showgrid_row_td' >$nafar</td><td class='showgrid_row_td' >".$r['reserve_id']."</td><td class='showgrid_row_td' >".$hotel_tmp->tozih."</td><td class='showgrid_row_td'>$room_aztarikh1</td><td class='showgrid_row_td'>$room_tatarikh1</td><td class='showgrid_row_td'>$status</td><td class='showgrid_row_td' >$nameDriver</td><td class='showgrid_row_td' >$target</td><td class='showgrid_row_td' >$transferTime</td><td class='showgrid_row_td' >$stat_kh</td><td class='showgrid_row_td' >$toz_tra</td>";
			$out.='</tr>';
			$i++;
	//	}
	}
	$out .="</table><br/>\n";
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
ثبت خدمات ترانسفر برای میهمان
		</title>
		<script type="text/javascript">
		function sbtFrm()
		{
			document.getElementById('frm1').submit();
		}
		function sendKh()
		{
			document.getElementById('frm2').submit();
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
	</head>
	<body>
               <br/>
		<br/>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" id="div_main" >
			<br/>
			<form id="frm1" method="GET">
				<table id='combo_table' >
					<tr>
						<td>
							<?php echo $combo_hotel; ?>
						</td>
						<td>
												تاریخ: <input class="inp" readonly="readonly" type="text" name="tarikh" id="tarikh" value="<?php echo audit_class::hamed_pdate($tarikh); ?>"  >
						</td>
						<td>
		                                        <input class="inp" type="button" value="جستجو" onclick="sbtFrm();"  >
                                                </td>
					</tr>
				</table>		
			</form>
			
			<br/>	
				<form id='frm2' method='GET'>

					میهمان:<?php echo $combo_sargrooh;?>
					راننده:
					<?php echo $comboDriver;?>
					مسیر:<?php echo $comboTarget;?>	
										ساعت:
					<input type='text' name='transferTime' id='transferTime'/>
					<input type='hidden' name='h_id' id='h_id' value="<?php echo $h_id?>"/>
					نوع:
					<?php echo $comboTyp;?>
					توضیحات:
					<textarea rows="1" cols="15 " name='toz'>ندارد</textarea> 
					<input class="inp" readonly="readonly" type="hidden" name="tarikh" id="tarikh" value="<?php echo audit_class::hamed_pdate($tarikh); ?>"  >
					<input type="button" value="ارائه خدمات" class="inp" onclick="sendKh();" >
				</form>	
				<br/>
				<?php echo $out;  ?>
				<input type="button" value="چاپ" class="inp" onclick="getPrint();" >
	
			<br/>
			
		</div>
		
	</body>
</html>
