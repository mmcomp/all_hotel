<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
               die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
       if(!$se->can_view)
              die(lang_fa_class::access_deny);
	if($se->detailAuth('all'))
		$is_admin = TRUE;
	function loadDaftar()
	{
		$out=null;
		$out['همه'] = -1;
		mysql_class::ex_sql("select `name`,`id` from `daftar` order by `name`",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$out[$r["name"]]=(int)$r["id"];
		}
		return $out;
	}
	function hamed_pdate($inp)
	{
		return (audit_class::hamed_pdate($inp));
	}
	function loadRoom($inp)
	{
		$ro = new room_class($inp);
		$ho = new hotel_class($ro->hotel_id);
		return ($ro->name.'('.$ho->name.')');
	}
	$grid = new jshowGrid_new("room_det","grid1");
	$grid->loadQueryField = TRUE;
	for($i=0;$i<count($grid->columnHeaders);$i++)
	{
		$grid->columnHeaders[$i] = null;
	}
	$grid->pageCount = 100;
	$grid->query = "SELECT 
`room_det`.`reserve_id`,`room_det`.`room_id`,`room_det`.`aztarikh`,`room_det`.`tatarikh` FROM `room_det` join `room_det` as `r_det` on (`room_det`.`room_id`=`r_det`.`room_id` and `room_det`.`reserve_id`<>`r_det`.`reserve_id` and `r_det`.`reserve_id`>0 and `room_det`.`reserve_id`>0 and ((`room_det`.`aztarikh` > `r_det`.`aztarikh` and `room_det`.`aztarikh` < `r_det`.`tatarikh`) or (`room_det`.`tatarikh` > `r_det`.`aztarikh` and `room_det`.`tatarikh` < `r_det`.`tatarikh`) or (`room_det`.`aztarikh` < `r_det`.`aztarikh` and `room_det`.`tatarikh`>`r_det`.`tatarikh`))) where `room_det`.`room_id`>0 group by `room_det`.`room_id`,`room_det`.`reserve_id` order by `room_det`.`room_id`,`room_det`.`reserve_id`";
	$grid->columnHeaders[0] = "شماره رزرو";
	$grid->columnHeaders[1]="شماره اتاق";
	$grid->columnFunctions[1] = 'loadRoom';
	$grid->columnHeaders[2]="از تاریخ";
	$grid->columnFunctions[2] ='hamed_pdate';
	$grid->columnHeaders[3]="تاتاریخ";
	$grid->columnFunctions[3] ='hamed_pdate';
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
		سامانه ارزیابی عملکرد کارکنان شرکت مدیریت تولید نیروگاه‌های گازی خراسان
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php echo $out;  ?>
		</div>
	</body>
</html>
