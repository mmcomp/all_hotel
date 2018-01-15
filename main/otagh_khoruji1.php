<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadVazeiat()
	{
		$out = "اتاق خروجی امروز";
		return $out;
	}
	function loadName($inp)
	{
		$room = new room_class($inp);
		$out = $room->name;
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	$tatarikh = date("Y-m-d");
	mysql_class::ex_sql("select `room_id`,`reserve_id` from `room_det` where date(`tatarikh`)='$tatarikh' and `reserve_id`>0",$q);
	$tmp ='';
	while ($r = mysql_fetch_array($q))
	{
		$reserve_id = $r['reserve_id'];
		mysql_class::ex_sql("select `reserve_id`,`khorooj` from `mehman` where `reserve_id`='$reserve_id'",$qu);
		while ($row = mysql_fetch_array($qu))
	        {
			if ($row['khorooj']=='0000-00-00 00:00:00')
				$tmp .=($tmp==''? '':',' ).$row['reserve_id'];
		}
	}
	if($tmp!='')
		$shart = " `reserve_id` in ($tmp)";
	else
		$shart = "";
	$grid = new jshowGrid_new("room_det","grid1");
	$grid->whereClause = $shart." order by `room_id`";
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= 'شماره اتاق';
	$grid->columnFunctions[1]= "loadName";
	$grid->columnHeaders[2]= 'تاریخ ورود';
	$grid->columnFunctions[2]= "hamed_pdate";
	$grid->columnHeaders[3]= 'تاریخ خروج';
	$grid->columnFunctions[3]= "hamed_pdate";
	$grid->columnHeaders[4]= 'شماره رزرو';
	$grid->columnHeaders[5]= null;
	$grid->columnHeaders[6]= 'مبلغ';
	$grid->columnHeaders[7]= 'تعداد نفرات';
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
			اتاق های خروجی امروز	
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
