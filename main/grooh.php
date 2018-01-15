<?php
/*	session_start();
/*	include_once("../kernel.php");
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
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);

        $combo=array();
        $combo["بستانکار"]=1;
        $combo["بدهکار"]=-1;
        $grid = new jshowGrid_new("grooh","grid1");
        $grid->columnHeaders[0] = null;
        $grid->columnHeaders[2] = "نام";
        $grid->columnHeaders[1] = "کد";
        $grid->columnHeaders[3] = "نوع";
        $grid->columnLists[3]=$combo;
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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه ارزیابی عملکرد کارکنان شرکت مدیریت تولید نیروگاه‌های گازی خراسان
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php echo $out;
			?>
		</div>
	</body>
</html>
