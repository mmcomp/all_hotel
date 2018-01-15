<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(!isset($_REQUEST['vaziat']))
		die(lang_fa_class::access_deny);
	function loadRoomWithVaz($hotel_id,$vaziat,$room_id = -1)
	{
		$out = '';
		$hotel_id = (int)$hotel_id;
		$vaziat = (int)$vaziat;
		$room_id = (int)$room_id;
		$hot = new hotel_class($hotel_id);
		$shart = ' 1 = 0 ';
		if($hot->id > 0)
		{
			switch($vaziat)
			{
				case 0:
					$shart = " (`vaziat` = 1) or (`vaziat` = 2) ";
					break;
				case 1:
					$shart = " (`vaziat` = 4) or (`vaziat` = 5) ";
                                        break;
				case 2:
					$shart = " (`vaziat` = 1) or (`vaziat` = 3) ";
                                        break;
                                case 3:
                                        $shart = " (`vaziat` = 1) or (`vaziat` = 2) or (`vaziat` = 4) or (`vaziat` = 5) ";
                                        break;
                                case 4:
                                        $shart = " (`vaziat` = 1) or (`vaziat` = 2) or (`vaziat` = 3) or (`vaziat` = 5) ";
					break;
                                case 5:
                                        $shart = " (`vaziat` = 1) or (`vaziat` = 2) or (`vaziat` = 3) or (`vaziat` = 4) ";
                                        break;
			}
			mysql_class::ex_sql("select `id`,`name` from `room` where `hotel_id` = $hotel_id and `en` = 1 and ($shart)",$q);
			while($r = mysql_fetch_array($q))
				$out .= "<option value=\"".$r['id']."\" ".(((int)$r['id']==$room_id)?'selected="selected"':'').">\n".$r['name']."\n</option>\n";
		}
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
	$vaziat = $_REQUEST['vaziat'];
	$hotel_id = -1;
	$room_id = -1;
        if(isset($_REQUEST["hotel_id"]))
                $hotel_id = $_REQUEST["hotel_id"];
        if(isset($_REQUEST["room_id"]))
		$room_id = $_REQUEST["room_id"];
	$out = '';
	if(isset($_REQUEST["mod"]) && $_REQUEST["mod"] == 'done' && $room_id > 0)
	{
		mysql_class::ex_sqlx("update `room` set `vaziat` = $vaziat where `id` = $room_id");
		$room = new room_class($room_id);
		$hot = new hotel_class($room->hotel_id);
		$out = '<span style="color:red;">وضعیت اتاق '.$room->name.' از '.$hot->name.' به '.room_status($vaziat).' تغییر یافت .</span>';
	}
        $combo = "";
        $combo .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">\n";
	$combo .= "<input type=\"hidden\" id=\"vaziat\" name=\"vaziat\" value=\"$vaziat\" />\n";
        $combo .= "<input type=\"hidden\" id=\"mod\" name=\"mod\" value=\"hotel\" />\n";
        $combo .= "هتل : <select class='inp' id=\"hotel_id\" name=\"hotel_id\" onchange=\"document.getElementById('mod').value='hotel';document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
        mysql_class::ex_sql("select * from `hotel_daftar`  where `daftar_id`=".$_SESSION['daftar_id'],$q);
        $shart = null;
        while($r = mysql_fetch_array($q))
                $shart[] = $r['hotel_id'];
        $shart = (isset($shart))?'where `id` in ('.implode(',',$shart).')':'';
        $q = null;
        mysql_class::ex_sql("select * from `hotel` $shart order by `name`",$q);
        while($r = mysql_fetch_array($q))
        {
                $select = "";
                if((int)$r["id"]== (int)$hotel_id)
                        $select = "selected='selected'";
                $combo .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
                $combo .= $r["name"]."\n";
                $combo .= "</option>\n";
        }
        $combo .="</select>";
	$combo .= "اتاق : <select class='inp' id=\"room_id\" name=\"room_id\" style=\"width:auto;\">\n".loadRoomWithVaz($hotel_id,$vaziat,$room_id)."</select>\n";
	$combo .= "<input type=\"button\" class=\"inp\" value=\"تغییر\" onclick=\"if(confirm('آیا وضعیت اتاق تغییر کند؟')){document.getElementById('mod').value='done';document.getElementById('selHotel').submit();}\" />";
        $combo .= "</form>";
	$out .= $combo;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		تغییر وضعیت اتاق
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
			<?php	echo $out;?>
		</div>
	</body>

</html>
