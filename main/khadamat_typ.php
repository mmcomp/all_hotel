<?php
/*	session_start();
	include_once("../kernel.php");
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
	$khadamat_id= isset($_REQUEST['khadamat_id']) ?(int)$_REQUEST['khadamat_id']:-1;
		
        $grid = new jshowGrid_new("khadamat","grid1");
	$rec["khadamat_id"] = $khadamat_id;
        $grid->setERequest($rec);
	$grid->whereClause=" `khadamat_id`='$khadamat_id' ";

        $grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = "خدمات";
	$grid->columnHeaders[3] = "قیمت پیش فرض";
	$grid->columnHeaders[4] = 'تعداددارد';
	$grid->columnLists[4] = loadtyp();
	$grid->columnLists[6] = loadLogicalTyp();
	$grid->columnLists[7] = loadLogicalTyp();
	$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6] = 'ورودی دارد';
        $grid->columnHeaders[7] = 'خروجی دارد';
        $grid->columnHeaders[8] = 'وعده اختیاری';
	$grid->columnLists[8] = loadVade();
	$grid->addFunction = "add_item";
	$grid->deleteFunction = "delete_item";
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
			سامانه رزرواسیون هتل	
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php 
				echo $combo;
				echo "<br/>";
				echo $out;
			?>
		</div>
	</body>
</html>
