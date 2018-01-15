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
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart1.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	if (isset($_REQUEST["hotel_id"]))
                $hotel_id_new = $_REQUEST["hotel_id"];
	else
		$hotel_id_new = -1;
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
		$combo_hotel .= "هتل : <select class='inp' id=\"hotel_id\" name=\"hotel_id\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
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
	$msg = '';
	$isAdmin = $se->detailAuth('all');
	$is_admin =$isAdmin;
	$reserve_id = ((isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:0);
	$f_name = ((isset($_REQUEST['f_name']))?$_REQUEST['f_name']:'');
	$l_name = ((isset($_REQUEST['l_name']))?$_REQUEST['l_name']:'');
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['aztarikh']," 00:00:00"):'0000-00-00');
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');
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
	$day = date("Y-m-d");
	//}
	
	
	//var_dump($tmp);
	//var_dump(room_det_class::loadDetByReserve_id($tmp[0]));
	//var_dump(room_det_class::loadNamesByReserve_id($tmp[0]));
	//var_dump(room_det_class::loadKhadamatByReserve_id($tmp[0]));
	$nafar = 0;
	$mablagh = 0;
	$mablagh_tmp = 0;
	$mablagh_kol = 0;
	if($se->detailAuth('super'))
		$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>شماره رزرو</th><th>هتل</th><th>نام</th><th>شماره اتاق</th></tr>';
	else
		$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>شماره رزرو</th><th>حساب</th><th>هتل</th><th>نام</th><th>شماره اتاق</th><th>تعداد نفرات</th><th>قیمت هتل</th><th>جمع کل</th><th>تاریخ ورود</th><th>تاریخ خروج</th></tr>';
	$styl = 'class="showgrid_row_odd"';
	$co_room = 0;
	$sum_room = 0;
	$khorooj = '';
	$rooms_id = "(";
	$rooms_ids = "";
	if ($hotel_id_new!=-1)
	{
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$hotel_id_new' order by `name`",$q);
		while($r = mysql_fetch_array($q))
			$rooms_id .= $r["id"].',';
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
	}
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
	{
		$tmp = room_det_class::loadReserve_id($aztarikh,$tatarikh,$user_id,$isAdmin,$f_name,$l_name,$reserve_id);		
		for($i=0;$i<count($tmp);$i++)
		{
			$rese_id = $tmp[$i];
			if (($rooms_ids!='')&&($rooms_ids!=')'))
			{
				mysql_class::ex_sql("select `reserve_id` from `room_det` where `room_id` in $rooms_ids and  `reserve_id`='$rese_id'",$q_res);
				if($r_res = mysql_fetch_array($q_res))
				{
					$tmp[$i] = $r_res['reserve_id'];
					$styl = 'class="showgrid_pazereshNashode"';
					mysql_class::ex_sql("select `reserve_id` from `mehman` where `reserve_id` = $tmp[$i]",$qu);
					while($row= mysql_fetch_array($qu))
					{
						$styl = 'class="showgrid_row_odd"';
						if($i%2 == 0 )
							$styl = 'class="showgrid_row_even"';
					}
					$horel_reserve = new hotel_reserve_class;
					$horel_reserve->loadByReserve($tmp[$i]);
					$room = room_det_class::loadDetByReserve_id($tmp[$i]);
					$rooms = '';
					for($j=0;$j<count($room['rooms']);$j++)
					{
						$co_room++;
						 $khorooj = ((room_det_class::loadKhoroojByReserve_id($tmp[$i],$room['rooms'][$j]['room_id']))?(room_det_class::loadKhoroojByReserve_id($tmp[$i],$room['rooms'][$j]['room_id'])):'');
						if ($khorooj!='')
							$time_kh = date("H:i",strtotime($khorooj[0]));
						else
							$time_kh='';
						$tmp_room = new room_class($room['rooms'][$j]['room_id']);
						if ((reserve_class::isPaziresh($tmp[$i],$room['rooms'][$j]['room_id']))&&((!reserve_class::isKhorooj($tmp[$i],$room['rooms'][$j]['room_id']))))
							$rooms.='<span style="color:#000000;background:#f1ca00"> '.$tmp_room->name.'</span>'.(($j<count($room['rooms'])-1)?' , ':'');
						elseif (($day == date("Y-m-d",strtotime($room['rooms'][$j]['tatarikh'])))&&(reserve_class::isKhorooj($tmp[$i],$room['rooms'][$j]['room_id'])))
							$rooms.='<span title='.$time_kh.' style="color:#ffffff;background:#0c5e06"> '.$tmp_room->name.'</span>'.(($j<count($room['rooms'])-1)?' , ':'');
						elseif ((reserve_class::isKhorooj($tmp[$i],$room['rooms'][$j]['room_id'])))
							$rooms.=' <span title='.$time_kh.' style="color:#ffffff;background:#0c5e06"> '.$tmp_room->name.'</span>'.(($j<count($room['rooms'])-1)?' , ':'');
						else
							$rooms.=$tmp_room->name.(($j<count($room['rooms'])-1)?' , ':'');
					}
					$sum_room = $sum_room + $co_room;
					$co_room = 0; 
					$nafar = $nafar + $room['rooms'][0]['nafar'];
					$mablagh = $mablagh + $horel_reserve->m_hotel;
					$mablagh_tmp = $horel_reserve->m_belit+$horel_reserve->m_hotel;
					$mablagh_kol = $mablagh_kol + $mablagh_tmp;
					$name = room_det_class::loadNamesByReserve_id($tmp[$i]);			
					$khadamat = room_det_class::loadKhadamatByReserve_id($tmp[$i]);
					if($se->detailAuth('super'))
					{
						$output .="<tr $styl ><td>".$tmp[$i]."</td>";
						$output .="<td>".$room['rooms'][0]['hotel']."</td><td>".$name[0]."</td><td>$rooms</td></tr>";
					}
					else
					{
						$output .="<tr $styl ><td>".$tmp[$i]."</td>";
						$output .="<td><a target='_blank' href='report.php?req=".$tmp[$i].".".$_SESSION['moshtari_id']."' >مشاهده</a></td>";
						$output .="<td>".$room['rooms'][0]['hotel']."</td><td>".$name[0]."</td><td>$rooms</td><td>".$room['rooms'][0]['nafar']."</td><td>".monize($horel_reserve->m_hotel)."</td>";
						$output .="<td>".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."</td>";
						$output .="<td>".audit_class::hamed_pdate($room['rooms'][0]['aztarikh'])."</td>";
						if (($day == date("Y-m-d",strtotime($room['rooms'][0]['tatarikh'])))&&(!reserve_class::isKhorooj($tmp[$i])))
							$output .="<td style='background-color:#db4a38;'>".audit_class::hamed_pdate($room['rooms'][0]['tatarikh'])."</td></tr>";
						elseif (($day == date("Y-m-d",strtotime($room['rooms'][0]['tatarikh'])))&&(reserve_class::isKhorooj($tmp[$i])))
							$output .="<td style='color:#ffffff;background:#0c5e06;'>".audit_class::hamed_pdate($room['rooms'][0]['tatarikh'])."</td></tr>";
						else
							$output .='<td>'.audit_class::hamed_pdate($room['rooms'][0]['tatarikh']).'</td></tr>';
					}
				}
			}
		}
		$mablagh = monize($mablagh);
		$mablagh_kol = monize($mablagh_kol);
		if(!($se->detailAuth('super')))
			$output .="<tr $styl ><td>جمع</td><td>--</td><td>--</td><td>--</td><td>$sum_room</td><td>$nafar</td><td>$mablagh</td><td>$mablagh_kol</td><td>--</td><td>--</td></tr>";
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
			if( trim(document.getElementById('reserve_id').value)=='' && trim(document.getElementById('f_name').value)=='' &&  trim(document.getElementById('l_name').value)=='' &&  trim(document.getElementById('datepicker6').value)=='' &&  trim(document.getElementById('datepicker7').value)=='')
			{
				alert('لطفا یکی از موارد را وارد کنید');
			}
			else
			{
				document.getElementById('mod').value= 2;
				document.getElementById('frm1').submit();
			}
		}
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
			سامانه رزرواسیون	
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php echo $combo_hotel;?>
			<br/>
			<br/>
			<form id='frm1'  method='GET' >
			<table border='1' style='font-size:12px;' >
				<tr>
					<th>شماره رزرو</th>
					<th>نام</th>
					<th>نام خانوادگی</th>
					<th>تاریخ ورود</th>
					<th>تاریخ خروج</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					<td>	
						<input class='inp' style='width:50px;' name='reserve_id' id='reserve_id' value="<?php echo ((isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:''); ?>" >
					</td>
					<td>	
						<input class='inp' name='f_name' id='f_name' value="<?php echo ((isset($_REQUEST['f_name']))?$_REQUEST['f_name']:''); ?>" >
					</td>
					<td>	
						<input class='inp' name='l_name' id='l_name' value="<?php echo ((isset($_REQUEST['l_name']))?$_REQUEST['l_name']:''); ?>" >
					</td>
					<td>	
         					   <input style='width:100px;' value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh'  class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td>
						<input style='width:100px;' value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='hidden' name='hotel_id' id='hotel_id' value="<?php echo $hotel_id_new;?>">
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
			<?php echo $output.' '.$msg; ?>
			</form>
		</div>
		<br/>
		<center>
			<table>
				<tr>
					<td style="background-color:#f1ca00;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">اتاق های پذیرش شده</td>
					<td style="background-color:#0c5e06;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">اتاق های خارج شده(تحویل هتل گردیده)</td>
					<td style="background-color:#db4a38;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">خروجی های امروز</td>
				</tr>
			</table>
		</center>
	</body>
</html>
