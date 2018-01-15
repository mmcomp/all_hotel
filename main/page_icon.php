<?php
//	include_once("../kernel.php");
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$msg = '';
	function loadGroup()
        {
                $out=null;
		$tmp = "-1";
		$out["همه"]= $tmp;
                mysql_class::ex_sql("select `name`,`id` from `grop` order by `id`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out[$r["name"]]=(int)$r["id"];
                return $out;
        }
	$islink ["ندارد"]= 0;
	$islink ["دارد"]= 1;
	$grid = new jshowGrid_new("page_icons","grid1");
	$grid->whereClause="1=1 order by `id`";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="آدرس آیکن";
	$grid->columnHeaders[2]="نام آیکن";
	$grid->columnFilters[2] = TRUE;
	$grid->columnHeaders[3] = "کد جاوا آیکن";
	$grid->columnHeaders[4] = "آدرس صفحه آیکن";
	$grid->columnHeaders[5] = "گروه مورد دسترسی به آیکن";
	$grid->columnLists[5]=loadGroup();
	$grid->columnHeaders[6] = "عرض";
	$grid->columnHeaders[7] = "ارتفاع";
	$grid->columnHeaders[8] = "آدرس صفحه";
	$grid->columnLists[8]=$islink;
	$grid->showAddDefault = FALSE;
	$grid->echoQuery = FALSE;
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
مدیریت آیکن
		</title>
	</head>
	<body>
		<div align="center">
			<?php echo $out;  ?>
		</div>
		
	</body>
</html>
