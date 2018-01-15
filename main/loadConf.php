<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(!isset($_REQUEST['moshtari_id']))
		die(lang_fa_class::access_deny);
	$moshtari_id = (int)$_REQUEST['moshtari_id'];
	$moshtari = new moshtari_class($moshtari_id);
	$conf->setMoshtari($moshtari_id);
	$grid = new jshowGrid_new("conf","grid1");
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = 'کلید';
	$grid->columnHeaders[2] = 'مقدار';
	$grid->columnFilters[1] = TRUE;
	$grid->columnFilters[2] = TRUE;
	$grid->showAddDefault = FALSE;
	$grid->sortEnabled = TRUE;
	$grid->pageCount = 100;
        $grid->intial();
   	$grid->executeQuery();
        $out = $grid->getGrid();
	unset($_SESSION['moshtari_id']);
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
		مدیریت تنظیمات مشتریان
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php	echo '<h1>تنظیمات '.$moshtari->name.'</h1><br/>'.$out;?>
		</div>
	</body>

</html>
