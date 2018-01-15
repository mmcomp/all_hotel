<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
/*
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
*/
/*
	$is_admin = FALSE;
	if($se->detailAuth('all'))
		$is_admin = TRUE;
*/
	function loadUser($id)
	{
		$out = '----';
		$u = new user_class((int)$id);
		if(isset($u->id) && $u->id > 0)
			$out = $u->fname.' '.$u->lname;
		return($out);
	}
	function loadDate($inp)
	{
		$out = '----';
		if($inp != '' && $inp != '0000-00-00 00:00:00')
			$out = jdate("Y/m/d",strtotime($inp));
		return($out);
	}
	function loadU()
        {
                $out=null;
                mysql_class::ex_sql("select `fname`,`lname`,`id`,`daftar_id` from `user` where `user`<>'mehrdad' order by `fname`,`lname`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$daftar = new daftar_class($r["daftar_id"]);
                        $out[$r["fname"].' '.$r['lname'].'('.$daftar->name.')']=(int)$r["id"];
		}
                return $out;
        }
	$sdate = isset($_REQUEST['sdate'])?trim($_REQUEST['sdate']):'';
	$tdate = isset($_REQUEST['tdate'])?trim($_REQUEST['tdate']):'';
	$wer = '';
	if($sdate!='')
	{
		$smdate = hamed_pdateBack2($sdate);
		$wer = "date(tarikh) >= '$smdate' ";
	}
	if($tdate!='')
	{
		$tmdate = hamed_pdateBack2($tdate);
		$wer .= (($wer != '')?" and ":"")."date(tarikh) <= '$tmdate'";
	}
	if($wer=='')
		$wer = '1=1';
	$pages = array(
		"ثبت سند" => "sanad_new",
		"ثبت رزرو" => "resreve2",
		"ثبت دریافتی پرداختی" => "sanad_new_daftar",
		"کنسل رزرو" => "refund",
		"اصلاح رزرو" => "showreserve"
	);
	$grid = new jshowGrid_new("log","grid1");
	$grid->whereClause = "$wer order by `tarikh` desc";
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->columnHeaders[0] = '';
	$grid->columnHeaders[1] = 'صفحه';
	$grid->columnLists[1] = $pages;
	$grid->columnFilters[1] =TRUE;
	$grid->columnHeaders[2] = "کاربر";
	$grid->columnLists[2]= loadU();
	$grid->columnFilters[2] =TRUE;
	$grid->columnHeaders[3] = "توضیحات";
	$grid->columnFilters[3] =TRUE;
	$grid->columnHeaders[4] = "تاریخ";
	$grid->columnFunctions[4] = "loadDate";
/*
	$grid->whereClause = " `user` != 'mehrdad' $wer order by `id`";
	$grid->columnHeaders[0] ='کد پرسنلی';
	$grid->columnAccesses[0] = 0;
	$grid->columnHeaders[1]="دفتر";
	$grid->columnFilters[1] = TRUE;
	$grid->columnHeaders[2] = "آژانس";
	$grid->columnHeaders[3]="نام";
	$grid->columnFilters[3] = TRUE;
	$grid->columnHeaders[4]="نام خانوادگی";
	$grid->columnFilters[4] = TRUE;
	$grid->columnHeaders[5]="نام کاربری";
	$grid->columnFilters[5] = TRUE;
	$grid->columnAccesses[5] = 1;
	$grid->columnHeaders[6]="رمز عبور";
	if(!$is_admin)
	{
		$grid->columnFunctions[6] = "hidePass";
		$grid->columnCallBackFunctions[6] = "hidePassAll";
	}
	$grid->columnHeaders[7]="گروه کاربری";
	$grid->columnFilters[7] = TRUE;
	$grid->columnLists[7]=$groups;
	$grid->columnHeaders[8]="شماره کارت";
	$grid->columnHeaders[9]="ساعت موظف ورود";
	$grid->columnHeaders[10]="ساعت موظف خروج";
	$grid->columnHeaders[11]="ساعت موظف ورود<br/>شیفت دو";
	$grid->columnHeaders[12]="ساعت موظف خروج<br/>شیفت دو";
	$grid->columnHeaders[13]=null;
	$grid->columnHeaders[14]= "زمان حضور";
	$grid->columnLists[1]=loadDaftar();
	$grid->columnLists[2] = loadAjans();
	$grid->editFunction = 'edit_item';
	for($i = 0;$i < count($grid->columnHeaders);$i++)
		$grid->columnAccesses[$i] = 0;
	$grid->columnAccesses[6] = 1;
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	if($is_admin)
	{
		for($i = 1;$i < count($grid->columnHeaders);$i++)
        	        $grid->columnAccesses[$i] = 1;
	        $grid->canAdd = TRUE;
		$grid->addFunction = 'add_item';
	}
	else if($se->detailAuth('middle_manager'))
	{	
		$grid->columnFunctions[6] = 'hidePass_all';
		$grid->canAdd = TRUE;
		$grid->addFunction = 'add_item';
		//$grid->columnFunctions[6] = 'hidePass';
	}
	else
	{
		$grid->columnFunctions[6] = 'hidePass_all';
		$grid->canAdd = FALSE;
	}
*/
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	$grid->canEdit = FALSE;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
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

		<script type="text/javascript" src="../js/tavanir.js"></script>
		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه نرم افزاری رزرو آنلاین بهار
		</title>
		<script>
		$(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#sdate").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
			$("#tdate").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
		</script>
	</head>
	<body>
		<div align="center">
			از تاریخ:
		<form>
		<input class="inp" type="text" name="sdate" id="sdate" value="<?php echo $sdate;  ?>"  >
		تا تاریخ:
		<input class="inp" type="text" name="tdate" id="tdate" value="<?php echo $tdate;  ?>"  >
		<br/>
		<button>جستجو</button>
		</form>
			<br/>
			<?php echo $out;  ?>
		</div>
	</body>
</html>

