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
		$out[4] = 'در حال تعمیر';
		$out[5] = 'خارج از سرویس';
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
	$tarikh = (isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):date("Y-m-d");
	$tarikh = explode(' ',$tarikh);
	$tarikh = $tarikh[0];
        $combo = "";
	$combo .= "<form name=\"selHotel\" id=\"selHotel\" method=\"GET\">";
	$combo .= "هتل : <select class='inp' id=\"hotel_id\" name=\"hotel_id\"  style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
	mysql_class::ex_sql("select * from `hotel_daftar`  where `daftar_id`=".$_SESSION['daftar_id'],$q);
	$shart = null;
	while($r = mysql_fetch_array($q))
		$shart[] = $r['hotel_id'];
	$shart = (isset($shart))?'where `id` in ('.implode(',',$shart).')':'';
	$q = null;
	mysql_class::ex_sql("select * from `hotel` $shart order by `name`",$q);
        while($r = mysql_fetch_array($q))
        {
		if((int)$r["id"]== (int)$hotel_id)
                {
                        $select = "selected='selected'";
                }
                else
                {
                        $select = "";
                }
                $combo .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
                $combo .= $r["name"]."\n";
                $combo .= "</option>\n";
        }
        $combo .="</select>";
	$room_typ_id=-1;
	if (isset($_REQUEST["room_typ_id"]))
                $room_typ_id=$_REQUEST["room_typ_id"];
	$combo_room_typ = "";
	$combo_room_typ .= "نوع اتاق : <select class='inp' id=\"room_typ_id\" name=\"room_typ_id\"  style=\"width:auto;\">\n<option value=\"-1\">\nهمه\n</option>\n";
	mysql_class::ex_sql("select * from `room_typ` order by `name`",$q);
        while($r = mysql_fetch_array($q))
        {
		if((int)$r["id"]== (int)$room_typ_id)
                {
                        $select = "selected='selected'";
                }
                else
                {
                        $select = "";
                }
                $combo_room_typ .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
                $combo_room_typ .= $r["name"]."\n";
                $combo_room_typ .= "</option>\n";
        }
        $combo_room_typ .="</select>";
	$combo_room_typ .= "<button onclick=\"document.getElementById('selHotel').submit();\">جستجو</buttom>";
	$combo_room_typ .= "</form>";
	$out = hotel_class::getRack($hotel_id,room_typ_id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
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
			<br/>
			<?php 
				echo $combo."   ".$combo_room_typ;
				echo "<br/>";
				echo $out;
			?>
		</div>
	</body>
</html>
