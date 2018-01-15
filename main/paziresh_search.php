<?php
	session_start();
	include("../kernel.php");
	include("../simplejson.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadAztarikh($aztar)
	{
		$out = '<select class="inp" name="aztarikh" id="aztarikh">';
		$tmp = mehman_class::pazireshDate();
		for($i=0;$i<count($tmp);$i++)
		{
			$tmp_date = explode(' ',audit_class::hamed_pdateBack($tmp[$i]));
			$tmp_date = $tmp_date[0];
			$sel =(strtotime($tmp_date)==strtotime($aztar))?'selected="selected"':'';
			$out .="<option $sel value='".$tmp[$i]."' >".$tmp[$i]."</option>\n";
		}
		$out .='</select>';
		return $out;
	}
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart1 = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart1.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	if (isset($_REQUEST["hotel_id_new"]))
                $hotel_id_new = $_REQUEST["hotel_id_new"];
	else
		$hotel_id_new = -1;
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
		$combo_hotel .= "هتل : <select class='inp' id=\"hotel_id\" name=\"hotel_id_new\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		if($shart1=='')
			$shart1='(-1)';
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
	if(isset($_REQUEST['reserve_paziresh_id']))
	{
		$reserve_id = (int)$_REQUEST['reserve_paziresh_id'];
		$reserve_code = $_REQUEST['reserve_code'];
		$s = reserve_class::canPaziresh($reserve_id);
		$out = array('reserve_code'=>$reserve_code,'res'=>$s);
		die(toJSON($out));
	}
	$msg = '';
	$isAdmin = $se->detailAuth('all');
	$is_admin =$isAdmin;
	$p_name = ((isset($_REQUEST['p_name']))?$_REQUEST['p_name']:'');
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date("Y-m-d"));
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):'0000-00-00');
	$user_id=-1;
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$curtime = strtotime(date("Y-m-d"));
	$az = strtotime($aztarikh);
	$ta = strtotime($tatarikh);
	$aztarikh = explode(" ",$aztarikh);
	$aztarikh = $aztarikh[0];
	$tatarikh = explode(" ",$tatarikh);
	$tatarikh = $tatarikh[0];
	$tedad_kol = 0;
	$jame_kol = 0;
	$room_ids = "(";
	if($se->detailAuth('khadamat'))
		$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="95%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>پذیرش</th><th>هتل</th><th>نام</th><th>شماره اتاق</th><th>شماره رزرو</th><th>تعداد نفرات</th><th>تاریخ ورود</th><th>تاریخ خروج</th><th>آژانس</th></tr>';
	else
		$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="95%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>پذیرش</th><th>هتل</th><th>نام</th><th>شماره اتاق(نظرسنجی)</th><th>شماره رزرو</th><th>تعداد نفرات</th><th>قیمت هتل</th><th>جمع کل</th><th>تاریخ ورود</th><th>تاریخ خروج</th><th>آژانس</th></tr>';
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
	{
		$tmp = room_det_class::loadReserve_id($aztarikh,$tatarikh,$user_id,$isAdmin,'','',-1,FALSE);
		//$tmp = ($tmp==''?array(-1):$tmp);
		//echo "tmp : <br/>\n";
		//var_dump($tmp);
		/*
		echo "SELECT `hotel_reserve`.`reserve_id` , `aztarikh` , `tatarikh`,`ajans`.`name` as `ajans_name` 
FROM `hotel_reserve` 
left join `ajans` on (`ajans_id` = `ajans`.`id`)
LEFT JOIN `room_det` ON ( `hotel_reserve`.`reserve_id` = `room_det`.`reserve_id` ) 
WHERE (`room_det`.`reserve_id` >0 AND ( (`aztarikh` = '$aztarikh') or ( `tatarikh` >= '$aztarikh' and `aztarikh`<'$aztarikh'))  ) group by `hotel_reserve`.`reserve_id`  order by `room_det`.`aztarikh` DESC<br/>\n";
		*/
		mysql_class::ex_sql("SELECT `hotel_reserve`.`reserve_id` , `aztarikh` , `tatarikh`,`ajans`.`name` as `ajans_name` 
FROM `hotel_reserve` 
left join `ajans` on (`ajans_id` = `ajans`.`id`)
LEFT JOIN `room_det` ON ( `hotel_reserve`.`reserve_id` = `room_det`.`reserve_id` ) 
WHERE (`room_det`.`reserve_id` >0 AND ( (`aztarikh` = '$aztarikh') or ( `tatarikh` >= '$aztarikh' and `aztarikh`<'$aztarikh'))  ) group by `hotel_reserve`.`reserve_id`  order by `room_det`.`aztarikh` DESC",$q);


		$ajans_tmp = array();
		//$tmp = array();
		$tmp = array();
		while($r = mysql_fetch_array($q))
		{
			//var_dump($r);
			$tmp[] = $r['reserve_id']; 
			$ajans_tmp[$r['reserve_id']] = $r['ajans_name'];
			//echo room_class::loadHotelByReserve($r['reserve_id']).'<br/>';
		}
		//var_dump($tmp);
		for($i=0;$i<count($tmp);$i++)
		{
			//echo room_class::loadHotelByReserve($tmp[$i]).'=='.$hotel_id_new."<br/>\n";
			if (room_class::loadHotelByReserve($tmp[$i])==$hotel_id_new)
			{
				$styl = 'class="showgrid_row_odd"';
				if($i%2 == 0 )
					$styl = 'class="showgrid_row_even"';
				$horel_reserve = new hotel_reserve_class;
				$horel_reserve->loadByReserve($tmp[$i]);	
				$khadamat_mehman = khadamat_det_class::loadIdByReserve($tmp[$i]);
//echo $khadamat_mehman.'<br/>';
				$kh_list = khadamat_det_front_class::loadCountById($khadamat_mehman);
				if ($kh_list>0)
				{
					$msg_class = 'msg';
					$title = 'غذا برای این میهمان ثبت شده است';
				}
				else
				{
					$msg_class = 'msg_1';
					$title = 'غذا برای این میهمان ثبت نشده است';
				}
				$room = room_det_class::loadByReserve($tmp[$i]);
				$room = $room[0];
				$rooms= '';
				for($j=0;$j<count($room);$j++)
				{
					$tmp_room = new room_class($room[$j]->room_id);
					$rooms.='<a target="_blank" title="نظر سنجی" href="ravabet.php?room_id='.$tmp_room->id.'&reserve_id='.$tmp[$i].'&">'.$tmp_room->name.'</a>'.(($j<count($room)-1)?' , ':'');
				}
				$name =$horel_reserve->fname.' '.$horel_reserve->lname;
				$hotel = new hotel_class($tmp_room->hotel_id);
				$reserve_id_code =dechex($tmp[$i]+10000);
				$khorooj = '';
				$hotel_id = $tmp_room->hotel_id;
				$troom_id = $tmp_room->id;
				$kh_id = -1;
				if(reserve_class::isKhorooj($tmp[$i],$troom_id))
					$khorooj = "<div class='msg' >خارج شده</div>";
				else if(reserve_class::isPaziresh($tmp[$i]))
				{
					if ($se->detailAuth('modir_paziresh'))
						$khorooj = "<div class='msg' >پذیرش شده</div>";
					else
						//$khorooj = "<div title='$title' class='$msg_class' ><a target='_blank' href='ghazaList.php?reserve_id=".$tmp[$i]."&kh=1&hotel_id=".$hotel_id."&' >لیست غذا</a></div><div class='msg' ><a target='_blank' href='report.php?req=".$tmp[$i]."&' >حساب میهمان</a></div>";
						$khorooj = "<div title='$title' class='$msg_class' ><a target='_blank' href='ghazaList.php?reserve_id=".$tmp[$i]."&kh=1&hotel_id=".$hotel_id."&' >لیست غذا</a></div>";
				}
				//$ravabet = "<div class='$msg_class'><a target='_blank' href='ravabet.php?room_id=&reserve_id=&'>اطلاعات نظرسنجی</a></div>";
				if ($khorooj=='')
					$output .="<tr $styl ><td><div class='notice'>پذیرش نشده</div>&nbsp;</td>";
				else
					$output .="<tr $styl ><td>&nbsp;$khorooj</td>";
				$naf = 0;
				foreach($room as $r_tmp)
					$naf += $r_tmp->nafar;
				if($se->detailAuth('khadamat'))
					$output .="<td>".$hotel->name."</td><td>".$name."</td><td>$rooms</td><td>".$tmp[$i]."</td><td>".$naf."</td>";
				else
				{
					$output .="<td>".$hotel->name."</td><td>".$name."</td><td>$rooms</td><td>".$tmp[$i]."</td><td>".$naf."</td><td>".monize($horel_reserve->m_hotel)."</td>";
					$output .="<td>".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."</td>";
				}
				$output .='<td>'.audit_class::hamed_pdate($room[0]->aztarikh).'</td>';
				$output .='<td>'.audit_class::hamed_pdate($room[0]->tatarikh).'</td>';
				$output .= '<td>'.((isset($ajans_tmp[$tmp[$i]]))?$ajans_tmp[$tmp[$i]]:'----').'</td></tr>';
				$tedad_kol = $tedad_kol + $naf;
				$tmp_mablagh = $horel_reserve->m_belit+$horel_reserve->m_hotel;
				$jame_kol = $jame_kol + $tmp_mablagh;
			}
		}
	if (!($se->detailAuth('khadamat')))
		$output .= '<tr class="showgrid_end"><th style="border-style:none;"></th><th style="border-style:none;"></th><th style="border-style:none;"></th><th style="border-style:none;"></th><th style="border-style:none;">جمع کل</th><th style="border-style:none;">'.$tedad_kol.'</th><th style="border-style:none;"></th><th style="border-style:none;">'.monize($jame_kol).'</th><th style="border-style:none;"></th><th style="border-style:none;"></th><th style="border-style:none;"></th></tr>';
	}
	$output .='</table>';
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
			document.getElementById('mod').value = 2;
			document.getElementById('frm1').submit();
		}
		function paziresh(reserve_code,reserve_id)
		{
			$.getJSON("paziresh_search.php?reserve_code="+reserve_code+"&reserve_paziresh_id="+reserve_id+"&",function(result){
				if(result.res.length>0)
				{
					var tout=result.res.join();
					alert('متأسفانه شماه رزرو(های) '+tout+' خارج نشده است.');	
				}
				else
					window.open("paziresh.php?reserve_id="+result.reserve_code+"&kh=0");
			});
		}
		$(function() {
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
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<br/>
			<?php 
				echo $combo_hotel;
				echo '<br/>';
			?>
			<form id='frm1'  method='GET' >
			<table border='1' style='font-size:12px;' >
				<tr>
					<th style='display:none;' >نام</th>
					<th>تاریخ ورود</th>
					<th style='display:none;' >تاریخ خروج</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					<td style='display:none;' >	
						<input class='inp' name='p_name' id='p_name' value="<?php echo ((isset($_REQUEST['p_name']))?$_REQUEST['p_name']:''); ?>" >
					</td>
					<td>	
						<?php //echo  loadAztarikh($aztarikh); ?>
						<input value="<?php echo ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdate($aztarikh):audit_class::hamed_pdate(date("Y-m-d H:i:s"))); ?>" type="text" name='aztarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td style='display:none;' >
						<input value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
					<td>
						<input type='hidden' name='hotel_id_new' id='hotel_id_new_tmp' value="<?php echo $hotel_id_new;?>" >
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
			<?php echo $output.' '.$msg; ?>
			</form>
		</div>
	</body>
</html>
