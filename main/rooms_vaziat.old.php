<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadHotel()
        {
				$tmp_hotel_id = array();
				mysql_class::ex_sql("select `hotel_id` from `hotel_daftar` where `daftar_id`=".$_SESSION['daftar_id'],$q);				while($r = mysql_fetch_array($q))
					$tmp_hotel_id[]= $r['hotel_id'];
				$out = 'عدم دسترسی کاربر به هتل';
				if(count($tmp_hotel_id))
				{
					$out=null;
					$tmp_hotel_ids = implode(',',$tmp_hotel_id);
					mysql_class::ex_sql("select `id`,`name` from hotel  where `id` in ($tmp_hotel_ids) order by name",$q);
					while($r=mysql_fetch_array($q,MYSQL_ASSOC))
							$out[$r['name']]=(int)$r['id'];
				}
                return $out;
        }
	function loadRoom()
        {
                $out = null;
                mysql_class::ex_sql("select * from room_typ order by zarfiat",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out[$r['name']]=(int)$r['id'];
                return $out;
        }
	function loadPic($id)
	{
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('loadPic.php?room_id=$id&','',500,600);\">ادامه</span></u>";
		return($out);
	}
	function room_status($stat)
	{
		$out[0] = 'اشغال شده';
		$out[1] = 'خالی اما نظافت نشده';
		$out[2] = 'خالی و نظافت شده';
		$out[3] = 'درحال نظافت';
		$out[4] = 'پشتیبان';
		$out[5] = 'در حال تعمیر';
		return($out[$stat]);
	}
	function room_status_icon($stat)
	{
		$out = "<img height=\"30px\" src = \"../img/$stat.png\" title=\"".room_status($stat)."\" alt=\"".room_status($stat)."\"/>";
		return($out);
	}
	$hotel_id=-1;
	if (isset($_REQUEST["hotel_id"]))
                $hotel_id=$_REQUEST["hotel_id"];
	mysql_class::ex_sql("select * from `hotel` order by `name`",$q);
        if($r = mysql_fetch_array($q))
		$hotel_id = $r["id"];
	else
		$hotel_id = -1;
	$links = "";
	$links .="<table style='background-color:#ffffff;-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;width:40%;'>";
			$links .="<tr>";
				$links .="<td ><a target='_blank' href='hotel_gozaresh.php?h_id=$hotel_id&'>گزارش خدمات</a></td>";
				$links .="<td ><a target='_blank' href='search_name.php?hotel_id=$hotel_id&'>جستجوی پیشرفته</a></td>";
				$links .="<td ><a target='_blank' href='gaant.php?hotel_id=$hotel_id&'>شیت هتل</a> </td>";		
				$links .="<td ><span onclick=\"wopen('change_paziresh.php?h_id=$hotel_id&','',800,500);\"  style='text-decoration:underline;cursor:pointer;' >جابجایی </span></td>";		
				$links .="<td ><a href='mehman.php' target='_blank' style='text-decoration:underline;cursor:pointer;' >لیست مهمانان مقیم</a></td>";	
			$links .="</tr>";
		$links .="</table>";
	$tarikh = (isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):date("Y-m-d");
	$room_typ = (isset($_REQUEST['room_typ']))?$_REQUEST['room_typ']:-1;
	$tarikh = explode(' ',$tarikh);
	$tarikh = $tarikh[0];
	$out = hotel_class::getRack_new($hotel_id,$room_typ);
	$sday = date("Y-m-d 00:00:00");
	$eday = date("Y-m-d 23:59:59");
	//$eday = date("Y-m-d H:i:s");
	$day = Date("Y-m-d 14:00:00 ");
	$today_khoruj = 0; 
	$count_mehman = 0;
	$count_room_khali = 0;
	$tedad_mehman = 0;
	$full_room = 0;
	$free_room = 0;
	$dirty_room = 0;
	$out_serviceRoom = 0;
	$tedad_mehman_moghim = 0;
	$poshtiban = 0;
	$y = Date("Y");
	$m = Date("m");
	$d = Date("d");
	$day1 =mktime("14","00","00",$m,$d,$y);
	mysql_class::ex_sql("select `nafar`,`reserve_id`,`tatarikh`,`room_id` from `room_det` ",$qr);

	while($rr = mysql_fetch_array($qr))
	{
		$tatarikh= $rr["tatarikh"];
		$res = $rr["reserve_id"];
		$room_id = $rr["room_id"];
		$ye = substr($tatarikh,0,4);
		$mo = substr($tatarikh,5,2);
		$da = substr($tatarikh,8,2);
		$tmp_tatarikh =mktime("14","00","00",$mo,$da,$ye);
		if (($tmp_tatarikh == $day1)&&($res>0)&&(!reserve_class::isKhorooj($res,$room_id)))
			$today_khoruj ++;
	}
	mysql_class::ex_sql("select `id`,`vaziat`,`name` from `room` where `en`='1'",$q);
	while($r = mysql_fetch_array($q))
	{
		$id = $r["id"];
		$rooms = room_det_class::roomIdAvailable($id,$sday,$eday);
		if (!(count($rooms)==0))
		{
			$tedad_mehman .= (($tedad_mehman=='')?'':',').$id;
			$full_room ++;
		}
	}
	$q=null;
	mysql_class::ex_sql("select `id`,`vaziat`,`name` from `room` where `en`='1'",$q);
	while($r = mysql_fetch_array($q))
	{
		if ($r["vaziat"] == 1)
			$dirty_room ++;
		if ($r["vaziat"] == 2)
			$free_room ++;
		if ($r["vaziat"] == 5)
			$out_serviceRoom ++;
		if ($r["vaziat"] == 4)
			$poshtiban ++;
	}
	$day = date("Y-m-d");
	$i = 1;
	$aztarikh = $day;
	$tatarikh = $day;
	$q=null;
//echo count($tedad_mehman);
	mysql_class::ex_sql("select `reserve_id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) < '$aztarikh' and date(`tatarikh`) > '$aztarikh') or (date(`aztarikh`) < '$tatarikh' and date(`tatarikh`) > '$tatarikh') or (date(`aztarikh`) >= '$aztarikh' and date(`tatarikh`) <= '$tatarikh')) group by `reserve_id`",$q);
	$tmp ='';
	while ($r = mysql_fetch_array($q))
		$tmp .=($tmp==''? '':',' ).$r['reserve_id'];
	mysql_class::ex_sql("select count(`id`) as `tedad` from `mehman` where `reserve_id` in ($tmp) and `khorooj`='0000-00-00 00:00:00'",$qq);
//echo "select count(`id`) as `tedad` from `mehman` where `reserve_id` in ($tmp) and `khorooj`='0000-00-00 00:00:00'";
	while($rr = mysql_fetch_array($qq))
		$tedad_mehman_moghim = $rr['tedad']."<br/>";
	$combo = "";
	$combo .= "<form name=\"selRoom\" id=\"selRoom\" method=\"GET\">";
	$combo .= "نوع اتاق : <select class='inp' id=\"room_typ\" name=\"room_typ\" onchange=\"document.getElementById('selRoom').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
	$combo .= "<option selected='selected' value=\"-1\">\n";
        $combo .= "همه"."\n";
        $combo .= "</option>\n";
	mysql_class::ex_sql("select * from room_typ order by zarfiat",$q);
        while($r = mysql_fetch_array($q))
        {
		if((int)$r["id"]== (int)$room_typ)
                        $select = 'selected="selected"';
                else
                        $select = "";
                $combo .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
                $combo .= $r["name"]."\n";
                $combo .= "</option>\n";
        }
        $combo .="</select>";
	$combo .= "</form>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="refresh" content="300;url=rooms_vaziat.php">
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
                <link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
                <link type="text/css" href="../css/style.css" rel="stylesheet" />
                <link href="../css/ih_style.css" rel="stylesheet" type="text/css" />
                <script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script> 
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
			سامانه رزرواسیون هتل	
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
			<?php if($se->detailAuth('tasisat') || $se->detailAuth('super')) { ?><a href="login.php" >خروج</a><?php } ?>
		</div>
		<div align="center">
			<br/>
			<?php echo $combo;
				echo $links;?>
			<br/>
			<table >
				<tr>
					<td style="background-color:#b72b13;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;">تعداد میهمانان حاضر در هتل</td>
					<td style="background-color:#0c5e06;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;">تعداد اتاق های خالی</td>
					<td style="background-color:#f1ca00;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;">تعداد اتاق های نظافت نشده </td>
					<td style="background-color:#a38fb3;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;">اتاق های در دست تعمیر</td>
					<td style="background-color:#ff7103;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">پشتیبان</td>
					<td style="background-color:#ffffff;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;">اتاق های خروجی امروز</td>
					
				</tr>
				<tr>
					<th style="background-color:#b72b13;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $tedad_mehman_moghim;?></th>
					<th style="background-color:#0c5e06;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $free_room;?></th>
					<th style="background-color:#f1ca00;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $dirty_room;?></th>
					<th style="background-color:#a38fb3;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $out_serviceRoom;?></th>
					<td style="background-color:#ff7103;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $poshtiban;?></td>
					<th style="background-color:#ffffff;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $today_khoruj;?></th>
					
				</tr>
			</table>
			<br/>
			<?php
				echo $out;
				echo "<br/>";
			?>
			<br/>
			<table>
				<tr>
					<td style="background-color:#b72b13;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">اشغال شده</td>
					<td style="background-color:#f1ca00;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">نظافت نشده</td>
					<td style="background-color:#0c5e06;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">نظافت شده</td>
					<td style="background-color:#a38fb3;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">در دست تعمیر</td>
					<td style="background-color:#ffffff;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">اتاق های خروجی امروز</td>
					<td style="background-color:#034da2;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">اتاق های اشغال موقت</td>
					<td style="background-color:#ff7103;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;">پشتیبان</td>
				</tr>
			</table>
		</div>
	</body>
</html>
