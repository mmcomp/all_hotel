<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$grid = new jshowGrid_new("nobat","grid1");
	$grid->width = '85%';
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= 'نوبت';
	$grid->canAdd = TRUE;
	$grid->canDelete = TRUE;
	$grid->canEdit = TRUE;
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
	    	</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
ثبت نوبت
		</title>
	</head>
	<body>
		<br/>
		<br/>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" id="div_main" >
			<br/>
			<br/>				
			<?php echo $out;  ?>
			<br/>
		</div>
	</body>
</html>
