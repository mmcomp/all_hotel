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
		$out = "اتاق های اشغال موقت";
		return $out;
	}
	$grid = new jshowGrid_new("room","grid1");
	$grid->whereClause = " `vaziat`='3' and `en`='1' order by `name`";
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= null;
	$grid->columnHeaders[2]= null;
	$grid->columnHeaders[3]= 'شماره اتاق';
	$grid->columnHeaders[4]= null;
	$grid->columnHeaders[5]= null;
	$grid->columnHeaders[6]= null;
	$grid->columnHeaders[7]= null;
	$grid->columnHeaders[8]= 'وضعیت';
	$grid->columnFunctions[8]= "loadVazeiat";
	$grid->columnHeaders[9]= null;
	$grid->columnHeaders[10]= null;
	$grid->columnHeaders[11]= null;
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
			اتاق های اشغال موقت	
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
