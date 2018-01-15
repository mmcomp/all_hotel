<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function show_status($inp)
	{
		$inp = (int)$inp;
		$out = '<img src="../img/check.png" width="15px" alt="نهایی" >';
		if($inp==0)
			$out = '<img src="../img/deny.png" width="15px" alt="موقت" >';
		return $out;
	}
	$grid = new jshowGrid_new("sanad","grid1");
	$grid->query = 'SELECT  `shomare_sanad`,`tarikh`,`en` from  `sanad` GROUP BY `shomare_sanad`';
	$grid->pageCount = 50;
	
	/*
	$grid->columnHeaders[0] = null;
	
	$grid->columnHeaders[2] = null;
	$grid->columnHeaders[3] = null;
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6] = null;
	$grid->columnHeaders[7] = null;
	$grid->columnHeaders[8] = null;
	
	$grid->columnHeaders[10] = null;
	$grid->columnHeaders[11] = null;
	$grid->columnHeaders[12] = null;
	$grid->columnHeaders[13] = null;
	$grid->columnHeaders[14] = null;
*/
	$grid->loadQueryField = TRUE;
	for($i=0;$i<count($grid->columnHeaders);$i++)
		$grid->columnHeaders[$i] = null;
        $grid->columnHeaders[0]="شماره سند";
        $grid->columnHeaders[1]="تاریخ";
        $grid->columnFunctions[1] = 'hamed_pdate';
	 $grid->columnHeaders[2]="وضعیت";
        $grid->columnFunctions[2] = 'show_status';
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

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه حسابداری
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
			<?php echo $out;  ?>
		</div>
	</body>
</html>
