<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function ppdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function  loadHotel($inp=-1)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return $out;
	}
	function loadRoom($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`name` from `room` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return $out;
	}
	function loadUser($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`fname`,`lname` from `user` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return $out;
	}	
	function loadNameByroom_id($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `name` from `room` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function loadStat($inp)
	{
		$out = "";
		if ($inp==1)
			$out = 'برطرف شده';
		else
			$out = 'برطرف نشده';
		return $out;
	}
	function loadVorood($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`reserve_id`,`aztarikh` from `room_det` where `reserve_id`=$inp ",$q);
                if($r = mysql_fetch_array($q))
	               $out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r["aztarikh"])));
		return $out;
	}
	function loadKhorooj($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`reserve_id`,`tatarikh` from `room_det` where `reserve_id`=$inp ",$q);
                if($r = mysql_fetch_array($q))
	               $out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r["tatarikh"])));
		return $out;
	}
	function listOtagh($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `room` where `en` = 1 and `id`='$inp'",$qq);
		while($row = mysql_fetch_array($qq))
			$out = $row['name'];
		return $out;
	}
	function loadNameByReserve($res=-1)
	{
		$out = '';
		mysql_class::ex_sql("select `fname`,`lname` from `hotel_reserve` where `reserve_id` = $res",$q);
                if($r = mysql_fetch_array($q))
	        {
			$out = $r['fname'].' '.$r['lname'];
		}
		else
			$out = '--';
		return($out);
	}
	function loadNameByUser($user=-1)
	{
		$out = '';
		mysql_class::ex_sql("select `fname`,`lname` from `user` where `id` = $user",$q);
                if($r = mysql_fetch_array($q))
	        {
			$out = $r['fname'].' '.$r['lname'];
		}
		else
			$out = '--';
		return($out);
	}
	
	$msg = '';
	$output = '';
	$i = 1;
	$t_voroodi = 0;	
	$t_khorooji = 0;
	$day = date("Y-m-d");
	$month_late = date('Y-m-d', strtotime($day .' +30 day'));
	$rooms_id = "(";
	$rooms_ids = "";	
	$sum_v = 0;
	$sum_kh = 0;
	if(isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else
		$h_id = 1;
	if ($h_id!=-1) 
	{
		mysql_class::ex_sql("select `name` from `hotel` where `id`='$h_id'",$q);
		if($r = mysql_fetch_array($q))
			$hotel_name = $r['name'];
	}
	else
		$hotel_name = 'هتل انتخاب نشده است';
	if ($h_id!=-1)
	{
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$h_id' order by `name`",$q);
		while($r = mysql_fetch_array($q))
			$rooms_id .= $r["id"].',';
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
	}
	if ($rooms_ids!='')
		$room_shart = "`room_id` in ".$rooms_ids;
	else
		$room_shart = "1=1";
	$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</t><th>تاریخ</th><th>تعداد ورودی</th><th>تعداد خروجی </th><th>وضعیت</th></tr>';
	while($day<=$month_late)
	{
		$day_jalali = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($day)));
		mysql_class::ex_sql("SELECT count(`id`) as `t_voroodi` FROM  `room_det` WHERE $room_shart and  DATE(`aztarikh`) ='$day' and `reserve_id`>0 order by `room_id`",$tmphelp);
		if($r = mysql_fetch_array($tmphelp))
			$t_voroodi = $r['t_voroodi'];
		mysql_class::ex_sql("SELECT count(`id`) as `t_khorooji` FROM  `room_det` WHERE $room_shart and DATE(`tatarikh`) ='$day' and `reserve_id`>0 order by `room_id`",$tmphelp);
		if($r = mysql_fetch_array($tmphelp))
			$t_khorooji = $r['t_khorooji'];
		$stat = (int)$t_voroodi - (int)$t_khorooji;
		if ($stat>0)
			$back_g = "style='background-color:#60de4e;'";
		elseif ($stat<0)
			$back_g = "style='background-color:#ec3b3d;'";
		else
			$back_g = "style='background-color:#ffffff;'";
		$output .="<tr><td style='background-color:#ffffff;'>$i</td><td style='background-color:#ffffff;'>$day_jalali</td><td style='background-color:#60de4e;'>$t_voroodi</td><td style='background-color:#ec3b3d;'>$t_khorooji</td><td $back_g>$stat</td></tr>";
		$sum_v = (int)$t_voroodi+$sum_v;
		$sum_kh = (int)$t_khorooji+$sum_kh;
		$day = date('Y-m-d', strtotime($day .' +1 day'));
		$i++;
	}
	$stat_kol = (int)$sum_v - (int)$sum_kh;
	if ($stat_kol>0)
		$back_g = "style='background-color:#60de4e;'";
	elseif ($stat_kol<0)
		$back_g = "style='background-color:#ec3b3d;'";
	else
		$back_g = "style='background-color:#ffffff;'";
	$output .="<tr><td style='background-color:#ffffff;'>---</td><td style='background-color:#ffffff;'>---</td><td style='background-color:#60de4e;'>$sum_v</td><td style='background-color:#ec3b3d;'>$sum_kh</td><td $back_g>$stat_kol</td></tr>";
	
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
		<style>
			td{text-align:center;}
		</style>
		<title>
لیست میهمانان ورودی و خروجی
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>				
			<h2><?php echo $hotel_name;?></h2>
			<br/>
			<?php echo $output.' '.$msg; ?>
			
		</div>
		<br/>		
	</body>
</html>
