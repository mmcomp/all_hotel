<?php	session_start();
	unset($_SESSION['factor_shomare']);
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
               die(lang_fa_class::access_deny);
	$GLOBALS['jam_kol']=0;
	$GLOBALS['jam_bes'] = 0;
	$GLOBALS['jam_bed'] = 0;
	function loadRoom()
	{
		$reserve_id = (isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:-1;
		$tmp_room = room_det_class::loadByReserve($reserve_id);
		$tmp_room = $tmp_room[0];
		$tmp_room = $tmp_room[0]->room_id;
		$tmp_room = new room_class($tmp_room);
		$hotel_id = $tmp_room->hotel_id;
		$hotel = new hotel_class($hotel_id);
		$out = array();
		mysql_class::ex_sql("select `id`,`name`  from `room` where `hotel_id`=$hotel_id order by `room`.`name` ",$q);
		while($r = mysql_fetch_array($q,MYSQL_ASSOC))
			$out[$r['name'].'('.$hotel->name.')'] = (int)$r['id'];
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
	function loadTarikh($tarikh)
	{
		$out = $tarikh;
		if($tarikh != '')
			$out = jdate("H:i d / m / Y",strtotime($tarikh));
		return($out);
	}
	function loadSandogh($inp)
	{
		$tmp = new sandogh_factor_class($inp);
		if($tmp->typ==1)
		{
			$out = new sandogh_item_class($tmp->sandogh_item_id);
			$out = new sandogh_class($out->sandogh_id);
			$out = $out->name;
		}
		else if($tmp->typ=-1)
		{
			$out = new sandogh_class($tmp->sandogh_item_id);
			$out = $out->name;
		}
		return $out;
	}
	function loadMablagh($inp)
        {
                $out =$inp;
                return monize($out);
        }
	function loadUser($inp)
	{
		$us = new user_class($inp);
		return ($us->fname.' '.$us->lname);
	}
	function loadMablaghKolBes($inp)
	{
		$out=0;
		mysql_class::ex_sql("select (`mablagh`*`tedad`) as `jam` from `sandogh_factor` where `typ`=1 and `id`=$inp  ",$q);
		if($r = mysql_fetch_array($q))
		{
			$out = $r['jam'];
			$GLOBALS['jam_bes'] +=$out;
		}
		return monize($out);
	}
	function loadMablaghKolBed($inp)
	{
		$out= 0;
		mysql_class::ex_sql("select (`mablagh`*`tedad`) as `jam` from `sandogh_factor` where `typ`=-1 and `id`=$inp  ",$q);
		if($r = mysql_fetch_array($q))
		{
			$out =$r['jam'];
			$GLOBALS['jam_bed'] +=$out;
		}
		return monize($out);
	}
	$out ='';
	$reserve_id = (isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:-1;
	//reserve_class::loadFactors($reserve_id);
	$grid = new jshowGrid_new("sandogh_factor","grid1");
      	$grid->whereClause = "`en` = 1 and `reserve_id`=$reserve_id order by `factor_shomare`";
	$grid->pageCount = 200;
	$grid->index_width = '20px';
	$grid->divProperty = '';
        $grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = 'رزرو';
	$grid->columnHeaders[2] = 'اتاق';
	$grid->columnLists[2] = loadRoom();
	$grid->columnFilters[2] = TRUE;
	$grid->columnHeaders[3] = null;
	$grid->columnHeaders[4] = 'توضیحات';
	$grid->columnHeaders[5] = 'تعداد';
	$grid->columnHeaders[6] = 'مبلغ - ریال';
	$grid->columnFunctions[6] = 'loadMablagh';
	$grid->columnHeaders[7] = 'شماره';
	$grid->columnHeaders[8] = null;
	$grid->columnHeaders[9] = 'فاکتور/رسید';
	$grid->columnFunctions[9] = 'factorResid';
	$grid->columnHeaders[10] = 'ثبت کننده';
	$grid->columnFunctions[10] = 'loadUser';
	$grid->columnHeaders[11] = 'تاریخ/ساعت';
	$grid->columnFunctions[11] = 'loadTarikh';
	$grid->addFeild('id',3);
	$grid->columnHeaders[3] = 'صندوق';
	$grid->columnFunctions[3] = 'loadSandogh';
	$grid->addFeild('id',8);
	$grid->columnHeaders[8] = 'مبلغ کل بس';
	$grid->columnFunctions[8] = 'loadMablaghKolBed';
	$grid->addFeild('id',9);
	$grid->columnHeaders[9] = 'مبلغ کل بد';
	$grid->columnFunctions[9] = 'loadMablaghKolBes';
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->footer = "
	<tr class='showgrid_row_odd'>
		<td colspan='7' >
			&nbsp;
		</td>
		<th id='jam_bed'>
		</th>
		<th id='jam_bes'>
		</th>
		<th id='jam_kol'>
		</th>
		<td colspan='3' >
			&nbsp;
		</td>
	</tr>
";
	$grid->intial();
        $grid->executeQuery();
	$out .= $grid->getGrid();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<title>
		گزارش فرانت آفیس
		</title>
		<script language="javascript" >
			function post_back()
			{
				document.getElementById('frm1').submit();
			}
			function jam_kol()
			{
				<?php $GLOBALS['jam_kol']= $GLOBALS['jam_bed']-$GLOBALS['jam_bes']; ?>
				var jam_kol = <?php echo $GLOBALS['jam_kol']; ?>;
				var jam_bes = <?php echo $GLOBALS['jam_bes']; ?>;
				var jam_bed = <?php echo $GLOBALS['jam_bed']; ?>;
				var toz;
				if(jam_kol>0)
					toz = 'یستانکار';
				else if(jam_kol<0)
					toz= 'بدهکار';
				else
					toz = 'تسفیه حساب';
				document.getElementById('jam_kol').innerHTML = FixNums(monize2(Math.abs(jam_kol)))+' ریال'+' '+toz;
				document.getElementById('jam_bes').innerHTML = FixNums(monize2(jam_bes))+' ریال';
				document.getElementById('jam_bed').innerHTML = FixNums(monize2(jam_bed))+' ریال';
			}
		</script>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" style="margin:10px;padding:5px;">
			<form id="frm1" >
				<table style="width:99%;"  >
					<tr>
						<th>
							جهت مشاهده حساب مهمان شماره رزرو را وارد کنید:
						
							<input value="<?php echo $reserve_id; ?>" type="text" name="reserve_id" id="reserve_id" class="inp">
							<input type="button" class="inp" value="جستجو" onclick="post_back();">
						</th>
					</tr>
				</table>
			</form>
			<?php	echo $out;?>
		</div>
		<script language="javascript" >
			jam_kol();
		</script>
	</body>

</html>
