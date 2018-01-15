<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(!$se->detailAuth('all') && !isset($_REQUEST['sandogh_id']))
		die(lang_fa_class::access_deny);
	function loadRoom($room_id)
	{
		$out = '&nbsp;';
		$r = new room_class((int)$room_id);
		if($r->id > 0)
		{
			$h = new hotel_class($r->hotel_id);
			$out = $r->name.'('.$h->name.')';
		}
		return($out);
	}
	function factorResid($inp)
	{
		$out = '&nbsp;';
		if((int)$inp == 1)
			$out = 'فاکتور';
		else if((int)$inp == -1)
			$out = 'رسید';
		return($out);
	}
	function loadFactor($id)
	{
		$out = '&nbsp;';
		$id = (int)$id;
		$sandogh_id = ((isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1);
		mysql_class::ex_sql("select `factor_shomare`,`sandogh_item_id`,`room_id`,`reserve_id`,`typ`,`en` from `sandogh_factor` where `id`=$id",$q);
		if($r = mysql_fetch_array($q))
		{
			$out = '<button class="inp" onclick="wopen(\'sandogh_factor.php?factor_shomare_req='.$r['factor_shomare'].'&canChange='.(($r['en']==0)?1:0).'&sandogh_id='.$sandogh_id.'&room_id='.$r['room_id'].'&reserve_id='.$r['reserve_id'].'&isFactor='.$r['typ'].'&get_type='.(((int)$r['room_id']>0)?'1':'-1').'&r=\'+Math.random(),\'\',600,500);" >مشاهده '.(((int)$r['typ']==1)?'فاکتور':'رسید').' شماره '.$r['factor_shomare'].'</button>';
		}
		return($out);
	}
	function loadTarikh($tarikh)
	{
		$out = $tarikh;
		if($tarikh != '')
			$out = jdate("H:i d / m / Y",strtotime($tarikh));
		return($out);
	}
	function loadReserve($id)
	{
		$out = 'نامعلوم';
		$id = (int)$id;
		mysql_class::ex_sql("select `typ`,`reserve_id` from `sandogh_factor` where `id` = $id",$q);
		if($r = mysql_fetch_array($q))
		{
			$reserve_id = (int)$r['reserve_id'];
			if($reserve_id <= 0 && (int)$r['typ'] == 1)
				$out = 'نقدی';
			else if((int)$r['typ'] == -1)
				$out = 'رسید';
			else
				$out = $reserve_id;
		}
		return($out);
	}
	$user_id = (int)$_SESSION['user_id'];
	$sandogh = user_class::loadSondogh($user_id,$se->detailAuth('all'));
	$sandogh_id = ((isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1);
	$isFactor = ((isset($_REQUEST['isFactor']) && (int)$_REQUEST['isFactor']==1)?TRUE:FALSE);
	$isHamedFactor = ((isset($_REQUEST['isFactor']) && (int)$_REQUEST['isFactor']==0)?TRUE:FALSE);
	$en = ((isset($_REQUEST['en']) && (int)$_REQUEST['en']==0)?0:1);
	if($se->detailAuth('all'))
	{
		$combo = "<option value=\"-1\"></option>\n";
		for($i = 0;$i < count($sandogh); $i++)
		{
			$s = new sandogh_class((int)$sandogh[$i]);
			if($s->id>0)
			{
				$h = new hotel_class($s->hotel_id);
				$sel = '';
				if($s->id == $sandogh_id)
				{
					$sandogh_items = null;
					mysql_class::ex_sql("select `id` from `sandogh_item` where `sandogh_id` = $sandogh_id",$q);
					while($r = mysql_fetch_array($q))
						$sandogh_items[] = (int)$r['id'];
					$sel = 'selected="selected"';
				}
				$combo .= "<option value=\"".$s->id."\" $sel>".$s->name."(".$h->name.")"."</option>\n";
			}
		}
	}
	else
	{
		$sandogh_items = null;
		$ssss = new sandogh_class($sandogh_id);
		$combo = "<option value=\"".$ssss->id."\" >".$ssss->name."</option>";
	}
	$factor = ($isFactor)?'selected="selected"':'';
	$resid = (!$isFactor)?'selected="selected"':'';
	$dayemi = ($en == 1)?'selected="selected"':'';
	$movaghat = ($en == 0)?'selected="selected"':'';
	$hame_factor = ($isHamedFactor)?'selected="selected"':'';;
	$hame = '';
	$out = "<form id=\"frm\">صندوق:<select class='inp' id=\"sandogh_id\" onchange=\"refresh_frm();\" name=\"sandogh_id\">\n$combo</select>\nسند:<select class='inp' onchange=\"refresh_frm();\" name=\"isFactor\" id=\"isFactor\">\n<option $factor value = '1'>فاکتور</option>\n<option $resid value = '-1'>رسید</option></select>\nوضعیت:<select class='inp' onchange=\"refresh_frm();\" name=\"en\" id=\"en\">\n<option $dayemi value = '1'>دائمی</option>\n<option $movaghat value = '0'>موقت</option></select>\n</form>\n<br/>";
	if($sandogh_id > 0 && ($sandogh_items != null || !$isFactor))
	{
		if($isFactor && $sandogh_items != null)
			$sandogh_items = ' and `sandogh_item_id` in ('.implode(',',$sandogh_items).') and `typ` = 1 ';
		else if($isFactor && $sandogh_items == null)
			$sandogh_items = ' 1=0 ';
		else if(!$isFactor)
			$sandogh_items = " and `sandogh_item_id` = $sandogh_id and `typ` = -1";
	        $grid = new jshowGrid_new("sandogh_factor","grid1");
		$grid->index_width = "20px";
	      	$grid->whereClause = "`en` = $en $sandogh_items  ".((!$se->detailAuth('all'))?"and `user_id` = $user_id":'')." group by `factor_shomare`";
	        $grid->columnHeaders[0] = null;
		$grid->columnHeaders[1] = 'رزرو';
		$grid->columnHeaders[2] = 'اتاق';
		$grid->columnHeaders[3] = null;
		$grid->columnHeaders[4] = null;
		$grid->columnHeaders[5] = null;
		$grid->columnHeaders[6] = null;
		$grid->columnHeaders[7] = 'شماره';
		$grid->columnHeaders[8] = null;
		$grid->columnHeaders[9] = 'فاکتور/رسید';
		$grid->columnHeaders[10] = null;
		$grid->columnHeaders[11] = 'تاریخ/ساعت';
		$grid->columnHeaders[12] = null;
		$grid->addFeild('id');
		$grid->columnHeaders[13] = 'جزئیات';
		$grid->fieldList[1] = 'id';
		$grid->columnFunctions[1] = 'loadReserve';
		$grid->columnFunctions[2] = 'loadRoom';
		$grid->columnFunctions[9] = 'factorResid';
		$grid->columnFunctions[13] = 'loadFactor';
		$grid->columnFunctions[11] = 'loadTarikh';
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
        	$grid->intial();
	        $grid->executeQuery();
        	$out .= $grid->getGrid();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>

		</title>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script language="javascript">
			function refresh_frm()
			{
				document.getElementById('frm').submit();
			}
		</script>
	<script>
		function st()
		{
		week= new Array("يكشنبه","دوشنبه","سه شنبه","چهارشنبه","پنج شنبه","جمعه","شنبه")
		months = new Array("فروردين","ارديبهشت","خرداد","تير","مرداد","شهريور","مهر","آبان","آذر","دي","بهمن","اسفند");
		a = new Date();
		d= a.getDay();
		day= a.getDate();
		var h=a.getHours();
      		var m=a.getMinutes();
  		var s=a.getSeconds();
		month = a.getMonth()+1;
		year= a.getYear();
		year = (year== 0)?2000:year;
		(year<1000)? (year += 1900):true;
		year -= ( (month < 3) || ((month == 3) && (day < 21)) )? 622:621;
		switch (month) 
		{
			case 1: (day<21)? (month=10, day+=10):(month=11, day-=20); break;
			case 2: (day<20)? (month=11, day+=11):(month=12, day-=19); break;
			case 3: (day<21)? (month=12, day+=9):(month=1, day-=20); break;
			case 4: (day<21)? (month=1, day+=11):(month=2, day-=20); break;
			case 5:
			case 6: (day<22)? (month-=3, day+=10):(month-=2, day-=21); break;
			case 7:
			case 8:
			case 9: (day<23)? (month-=3, day+=9):(month-=2, day-=22); break;
			case 10:(day<23)? (month=7, day+=8):(month=8, day-=22); break;
			case 11:
			case 12:(day<22)? (month-=3, day+=9):(month-=2, day-=21); break;
			default: break;
		}
		//document.write(" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s);
			var total=" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s;
			    document.getElementById("tim").innerHTML=total;
   			    setTimeout('st()',500);
		}
		</script>
	</head>
	<body onload='st()'>
		<center>
		<span id='tim' >test2
		</span>
		</center>
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
