<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadVazeiat($inp)
	{
		$today = date("Y-m-d");
		mysql_class::ex_sql("select `id`,`reserve_id` from `room_det` where `id`='$inp'",$q);
		while($r = mysql_fetch_array($q))
		{
			$reserve_id = $r['reserve_id'];
			mysql_class::ex_sql("select `id` from `mehman` where `reserve_id`='$reserve_id'",$qu);
			if($row = mysql_fetch_array($qu))
				$out = "<span style='background-color:#b72b13;'>"."پذیرش شده"."</span>";
			else
				$out = "<span style='background-color:#0c5e06;'>"."پذیرش نشده"."</span>";
		}
		
		return $out;
	}
	function loadOtagh($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `room` where `id`='$inp'",$q);
		while($r = mysql_fetch_array($q))
			$out = $r['name'];
		return $out;
	}
	$tmp = "(";
	if(isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else
		$h_id = -1;
	mysql_class::ex_sql("select `id`,`hotel_id` from `room` where `hotel_id`='$h_id'",$q);
	while ($r = mysql_fetch_array($q))
	{
		if ($h_id==$r["hotel_id"])
			$tmp .= $r['id'].',';
	}
	$tmp1 = substr($tmp, 0, -1);
	$tmp1 .= ")";
	if ($tmp1!=")")
		$shart = "`room_id` in $tmp1";
	else
		$shart = "1=0";
	$today = date("Y-m-d");
	$grid = new jshowGrid_new("room_det","grid1");
	$grid->whereClause = " $shart and date(`aztarikh`)='$today' order by `room_id`";
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= 'شماره اتاق';
	$grid->columnFunctions[1]='loadOtagh';
	$grid->columnHeaders[2]= null;
	$grid->columnHeaders[3]= null;
	$grid->columnHeaders[4]= 'شماره رزرو';
	$grid->columnHeaders[5]= null;
	$grid->columnHeaders[6]= null;
	$grid->columnHeaders[7]= null;
	$grid->addFeild('id');
        $grid->columnHeaders[8] = 'وضعیت';
        $grid->columnFunctions[8]='loadVazeiat';
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	$grid->canEdit = FALSE;
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
//				$("#new_regdate").hide();
//				$("#new_answer").hide();
//				$("#new_isFixed").hide();
			});
		</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
			اتاق های پشتیبان	
		</title>
	</head>
	<body>
		<br/>
		<br/>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<?php echo $out; ?>
		</div>
	</body>
</html>
