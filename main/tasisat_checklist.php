<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$is_admin = FALSE;
	if($se->detailAuth('all'))
		$is_admin = TRUE;
	function ppdate($inp)
	{
		return($inp!='0000-00-00 00:00:00'?audit_class::hamed_pdate($inp):'----');
	}
	function loadChecklist($checkList_id)
	{
		$checkList_id = (int)$checkList_id;
		$out = "<button onclick=\"window.open('tasisat_checklist_det.php?id=$checkList_id&');\">مشاهده</button>";
		return($out);
	}
	function loadUser($user_id)
	{
		$out = "";
		mysql_class::ex_sql("select `fname` , `lname` from `user` where `id`='$user_id'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return $out;
	}
	$wer = "1=1";
	if(isset($_REQUEST['aztarikh']))
	{
		$wer = '';
		if(trim($_REQUEST['aztarikh'])!='')
			$wer = " tarikh >= '".audit_class::hamed_pdateBack(trim($_REQUEST['aztarikh']))."'";
		if(trim($_REQUEST['tatarikh'])!='')
			$wer .= (($wer!="")?' and ':'')." tarikh <= '".audit_class::hamed_pdateBack(trim($_REQUEST['tatarikh']))."'";
		if($wer == '')
			$wer = "1=1";
	}
	$grid = new jshowGrid_new("tasisat_checklist","grid1");
	$grid->whereClause = " $wer order by tarikh desc";
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->columnHeaders[0] = "چک لیست";
	$grid->columnFunctions[0] = 'loadChecklist';
	$grid->columnHeaders[1]= "کاربر";
	$grid->columnFunctions[1] = 'loadUser';
	$grid->columnHeaders[2] = "تاریخ";
	$grid->columnFunctions[2] = 'ppdate';
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->pageCount = 0;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
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
		<script>
			$(document).ready(function(){
				$("#aztarikh").datepicker({
				    showOn: 'button',
				    dateFormat: 'yy/mm/dd',
				    buttonImage: '../js/styles/images/calendar.png',
				    buttonImageOnly: true
				});
				$("#tatarikh").datepicker({
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
			اتاق های دارای مشکل	
		</title>
	</head>
	<body onload='st()'>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<form method="post">
				<input id="aztarikh" name="aztarikh" value="<?= isset($_REQUEST['aztarikh'])?$_REQUEST['aztarikh']:''; ?>" />
				<input id="tatarikh" name="tatarikh" value="<?= isset($_REQUEST['tatarikh'])?$_REQUEST['tatarikh']:''; ?>" />
				<button>جستجو</button>
			</form>
			<?php echo $out;  ?>
		</div>
		<script language="javascript">
			document.getElementById('new_id').style.display = 'none';
		</script>
	</body>
</html>

