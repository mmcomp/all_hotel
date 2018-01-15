<?php
//	include_once("../kernel.php");
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadName($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `fname`,`lname` from `hotel_reserve` where `reserve_id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return ($out);
	}
	function loadNobat_gasht($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `typ` from `khadamat_gasht` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$typ = $r['typ'];
			mysql_class::ex_sql("select `name` from `nobat` where `id`='$typ'",$q_nobat);
			if($r_nobat = mysql_fetch_array($q_nobat))
				$out = $r_nobat['name'];
		}
		return ($out);
	}
	function loadTarikh_gasht($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `tarikh` from `khadamat_gasht` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
				$out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r['tarikh'])));
		return ($out);
	}
	function loadNobat_akasi($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `typ` from `khadamat_akasi` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$typ = $r['typ'];
			mysql_class::ex_sql("select `name` from `nobat` where `id`='$typ'",$q_nobat);
			if($r_nobat = mysql_fetch_array($q_nobat))
				$out = $r_nobat['name'];
		}
		return ($out);
	}
	function loadTarikh_akasi($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `tarikh` from `khadamat_akasi` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
				$out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r['tarikh'])));
		return ($out);
	}
	function loadNobat_cinema($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `typ` from `khadamat_cinema` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$typ = $r['typ'];
			mysql_class::ex_sql("select `name` from `nobat` where `id`='$typ'",$q_nobat);
			if($r_nobat = mysql_fetch_array($q_nobat))
				$out = $r_nobat['name'];
		}
		return ($out);
	}
	function loadTarikh_cinema($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `tarikh` from `khadamat_cinema` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
				$out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r['tarikh'])));
		return ($out);
	}
	function loadName_transfer($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `reserve_id` from `khadamat_transfer` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$res = $r['reserve_id'];
			mysql_class::ex_sql("select `fname`,`lname` from `hotel_reserve` where `reserve_id`='$res'",$q_res);
			if($r_res = mysql_fetch_array($q_res))
				$out = $r_res['fname'].' '.$r_res['lname'];
		}
		return ($out);
	}
	function loadDriver($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `name` from `driver` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return ($out);
	}
	function loadTarikh_tra($inp)
	{
		$out = audit_class::hamed_pdate_2($inp);
		return ($out);
	}
	function loadTarget($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `name` from `target` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return ($out);
	}
	function loadTyp($inp)
	{
		if($inp==1)
			$out = 'ورودی';
		elseif ($inp==2)
			$out = 'خروجی';
		else
			$out = '--';
		return ($out);
	}
	$today = date('Y-m-d');
	$sandogh_id = (isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1;
	$khadamat_sandogh = sandogh_khadamat_class::loadKhadamatById($sandogh_id);	
	mysql_class::ex_sql("select * from `khadamat` where `id`='$khadamat_sandogh'",$q);
	if ($r = mysql_fetch_array($q))
	{
		if ($r['name']=='گشت')
		{
			$grid = new jshowGrid_new("khadamat_gasht","grid1");
			$grid->whereClause = " date(`tarikh`)='$today'";
			$grid->columnHeaders[0] = null;
			$grid->columnHeaders[1]= null;
			$grid->columnHeaders[2]= 'شماره رزرو';
			$grid->columnHeaders[3]= null;
			$grid->columnHeaders[4]= null;
			$grid->addFeild('reserve_id');
			$grid->columnHeaders[5] = 'نام میهمان';
			$grid->columnFunctions[5]='loadName';
			$grid->addFeild('id');
			$grid->columnHeaders[6] = 'نوبت';
			$grid->columnFunctions[6]='loadNobat_gasht';
			$grid->addFeild('id');
			$grid->columnHeaders[7] = 'تاریخ';
			$grid->columnFunctions[7]='loadTarikh_gasht';
			$grid->canAdd = FALSE;
			$grid->canDelete = FALSE;
			$grid->canEdit = FALSE;
			$grid->intial();
			$grid->executeQuery();
			$out = $grid->getGrid();
		}
		elseif ($r['name']=='ترانسفر')
		{
			$grid = new jshowGrid_new("khadamat_transfer","grid1");
			$grid->whereClause = " date(`timeKh`)='$today'";
			$grid->columnHeaders[0] = 'نام میهمان';
			$grid->columnFunctions[0]='loadName_transfer';
			$grid->columnHeaders[1]= null;
			$grid->columnHeaders[2]= 'شماره رزرو';
			$grid->columnHeaders[3]= 'نام راننده';
			$grid->columnFunctions[3]='loadDriver';
			$grid->columnHeaders[4]= 'مقصد';
			$grid->columnFunctions[4]='loadTarget';
			$grid->columnHeaders[5]= 'تاریخ';
			$grid->columnFunctions[5]='loadTarikh_tra';
			$grid->columnHeaders[6]= 'نوع';
			$grid->columnFunctions[6]='loadTyp';
			$grid->columnHeaders[7] = 'توضیحات';
			$grid->canAdd = FALSE;
			$grid->canDelete = FALSE;
			$grid->canEdit = FALSE;
			$grid->intial();
			$grid->executeQuery();
			$out = $grid->getGrid();
		}
		elseif ($r['name']=='سینما')
		{
			$grid = new jshowGrid_new("khadamat_cinema","grid1");
			$grid->whereClause = " date(`tarikh`)='$today'";
			$grid->columnHeaders[0] = null;
			$grid->columnHeaders[1]= null;
			$grid->columnHeaders[2]= 'شماره رزرو';
			$grid->columnHeaders[3]= null;
			$grid->columnHeaders[4]= null;
			$grid->addFeild('reserve_id');
			$grid->columnHeaders[5] = 'نام میهمان';
			$grid->columnFunctions[5]='loadName';
			$grid->addFeild('id');
			$grid->columnHeaders[6] = 'نوبت';
			$grid->columnFunctions[6]='loadNobat_cinema';
			$grid->addFeild('id');
			$grid->columnHeaders[7] = 'تاریخ';
			$grid->columnFunctions[7]='loadTarikh_cinema';
			$grid->canAdd = FALSE;
			$grid->canDelete = FALSE;
			$grid->canEdit = FALSE;
			$grid->intial();
			$grid->executeQuery();
			$out = $grid->getGrid();
		}
		elseif ($r['name']=='عکاسخانه')
		{
			$grid = new jshowGrid_new("khadamat_akasi","grid1");
			$grid->whereClause = " date(`tarikh`)='$today'";
			$grid->columnHeaders[0] = null;
			$grid->columnHeaders[1]= null;
			$grid->columnHeaders[2]= 'شماره رزرو';
			$grid->columnHeaders[3]= null;
			$grid->columnHeaders[4]= null;
			$grid->columnHeaders[5]= null;
			$grid->addFeild('reserve_id');
			$grid->columnHeaders[6] = 'نام میهمان';
			$grid->columnFunctions[6]='loadName';
			$grid->addFeild('id');
			$grid->columnHeaders[7] = 'نوبت';
			$grid->columnFunctions[7]='loadNobat_akasi';
			$grid->addFeild('id');
			$grid->columnHeaders[8] = 'تاریخ';
			$grid->columnFunctions[8]='loadTarikh_akasi';
			$grid->canAdd = FALSE;
			$grid->canDelete = FALSE;
			$grid->canEdit = FALSE;
			$grid->intial();
			$grid->executeQuery();
			$out = $grid->getGrid();
		}
		else
		{
			$tb_grid = 'khadamat_det_front';
			$wer = '1=1';
		}
	}
	
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
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
لیست میهمانان دارای خدمات
		</title>
		<script type="text/javascript" >
			function filter_frm()
			{
				document.getElementById('frm1').submit();
			}
		</script>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center" style="margin:10px;padding:5px;" >
			<?php echo $out;  ?>
		</div>
	</body>
</html>
