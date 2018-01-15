<?php
//	include_once("../kernel.php");
	session_start();
	include_once("../kernel.php");
/*
	if (isset($_SESSION['user_id']) && isset($_SESSION['typ']))
	{
		if (!audit_class::isAdmin($_SESSION['typ']))
		{
			die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
		}
	}
	else
	{
			die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
	}*/
if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);	
	$id = ((isset($_REQUEST["sanad"]))?(int)$_REQUEST["sanad"]:-1);
	$grid = new jshowGrid_new("sanad","grid1");
	$grid->whereClause="`id`=$id";
	for($i=0;$i<15;$i++)
	{
		$grid->columnHeaders[$i] = null;
	}	
	$grid->columnHeaders[12]="توضیحات";
//	$grid->columnHeaders[2]="توضیحات";
//	$grid->columnHeaders[1] = "نام مشتری";
//grid->addFunction = "add_item";
	$grid->canDelete=FALSE;
	$grid->canAdd=FALSE;
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
		سامانه 
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php echo $out;  ?>
			<input type="button" value="خروج" class="inp" onclick="window.close(); " >
		</div>
	</body>
</html>
