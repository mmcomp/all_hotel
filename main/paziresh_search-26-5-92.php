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
	/*
	if($az - $curtime <= 24*60*60 && !$is_admin)
	{
		$aztarikh = date("Y-m-d",$curtime);
		$tatarikh = date("Y-m-d",$curtime);
	}
	else
	{
	*/
	$aztarikh = explode(" ",$aztarikh);
	$aztarikh = $aztarikh[0];
	$tatarikh = explode(" ",$tatarikh);
	$tatarikh = $tatarikh[0];
	$tedad_kol = 0;
	$jame_kol = 0;
	$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="95%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>پذیرش</th><th>هتل</th><th>نام</th><th>شماره اتاق</th><th>شماره رزرو</th><th>تعداد نفرات</th><th>قیمت هتل</th><th>جمع کل</th><th>تاریخ ورود</th><th>تاریخ خروج</th></tr>';
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
	{
		$tmp = room_det_class::loadReserve_id($aztarikh,$tatarikh,$user_id,$isAdmin,'','',-1);
		mysql_class::ex_sql("SELECT `hotel_reserve`.`reserve_id` , `aztarikh` , `tatarikh` 
FROM `hotel_reserve` 
LEFT JOIN `room_det` ON ( `hotel_reserve`.`reserve_id` = `room_det`.`reserve_id` ) 
WHERE `room_det`.`reserve_id` >0 AND ( (`aztarikh` = '$aztarikh') or ( `tatarikh` >= '$aztarikh' and `aztarikh`<'$aztarikh') ) group by `hotel_reserve`.`reserve_id` order by `room_det`.`aztarikh` DESC",$q);
		while($r = mysql_fetch_array($q))
			$tmp[] = $r['reserve_id']; 
		for($i=0;$i<count($tmp);$i++)
		{
			$styl = 'class="showgrid_row_odd"';
			if($i%2 == 0 )
				$styl = 'class="showgrid_row_even"';
			$horel_reserve = new hotel_reserve_class;
			$horel_reserve->loadByReserve($tmp[$i]);
			
			
			$room = room_det_class::loadByReserve($tmp[$i]);
			$room = $room[0];
			$rooms= '';
			for($j=0;$j<count($room);$j++)
			{
				$tmp_room = new room_class($room[$j]->room_id);
				$rooms.=$tmp_room->name.(($j<count($room)-1)?' , ':'');
			}
			$name =$horel_reserve->fname.' '.$horel_reserve->lname;
			//$khadamat = room_det_class::loadKhadamatByReserve_id($tmp[$i]);
			$hotel = new hotel_class($tmp_room->hotel_id);
			$reserve_id_code =dechex($tmp[$i]+10000);
			$khorooj = '';
			$hotel_id = $tmp_room->hotel_id;
			$troom_id = $tmp_room->id;
			if(reserve_class::isKhorooj($tmp[$i],$troom_id))
				$khorooj = "<div class='msg' >خارج شده</div>";
			else if(reserve_class::isPaziresh($tmp[$i]))
				//$khorooj = "<div class='msg' ><a target='_blank' href='ghazaList.php?reserve_id=".$tmp[$i]."&kh=1&hotel_id=".$hotel_id."&' >لیست غذا</a></div><div class='msg' ><a target='_blank' href='report.php?req=".$tmp[$i]."&' >حساب میهمان</a></div><div class='notice' ><a target='_blank' href='paziresh.php?reserve_id=$reserve_id_code&kh=1' >خروج</a></div>";
				$khorooj = "<div class='msg' ><a target='_blank' href='ghazaList.php?reserve_id=".$tmp[$i]."&kh=1&hotel_id=".$hotel_id."&' >لیست غذا</a></div><div class='msg' ><a target='_blank' href='report.php?req=".$tmp[$i]."&' >حساب میهمان</a></div>";
			if ($khorooj=='')
			//$output .="<tr $styl ><td><div class='pointer msg' onclick='paziresh(\"$reserve_id_code\",".$tmp[$i].");' >پذیرش</div>&nbsp;$khorooj</td>";
				$output .="<tr $styl ><td><div class='notice'>پذیرش نشده</div>&nbsp;</td>";
			if ($khorooj!='')
				$output .="<tr $styl ><td>&nbsp;$khorooj</td>";
			$output .="<td>".$hotel->name."</td><td>".$name."</td><td>$rooms</td><td>".$tmp[$i]."</td><td>".$room[0]->nafar."</td><td>".monize($horel_reserve->m_hotel)."</td>";
			$output .="<td>".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."</td>";
			$output .='<td>'.audit_class::hamed_pdate($room[0]->aztarikh).'</td>';
			$output .='<td>'.audit_class::hamed_pdate($room[0]->tatarikh).'</td></tr>';
			$tedad_kol = $tedad_kol + $room[0]->nafar;
			$tmp_mablagh = $horel_reserve->m_belit+$horel_reserve->m_hotel;
			$jame_kol = $jame_kol + $tmp_mablagh;
		}
	$output .= '<tr class="showgrid_end"><th style="border-style:none;"></th><th style="border-style:none;"></th><th style="border-style:none;"></th><th style="border-style:none;"></th><th style="border-style:none;">جمع کل</th><th style="border-style:none;">'.$tedad_kol.'</th><th style="border-style:none;"></th><th style="border-style:none;">'.monize($jame_kol).'</th><th style="border-style:none;"></th><th style="border-style:none;"></th></tr>';
	}
	$output .='</table>';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
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
         					 <!--  <input value="<?php echo audit_class::hamed_pdate($aztarikh); ?>" type="text" name='aztarikh'  class='inp' style='direction:ltr;' id="aztarikh" />	-->
						<?php echo  loadAztarikh($aztarikh); ?>
					</td>
					<td style='display:none;' >
						<input value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
					<td>
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
