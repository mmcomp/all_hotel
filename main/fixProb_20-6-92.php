<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$out = '';
	if ((isset($_REQUEST['hotel_id']))&&(isset($_REQUEST['room_id'])))
	{
		$h_id = $_REQUEST['hotel_id'];
		$r_id = $_REQUEST['room_id'];
		mysql_class::ex_sql("select `id` from `tasisat_tmp` where `hotel_id` = '$h_id' and `room_id`='$r_id'",$q);
		if($r = mysql_fetch_array($q))
		{
			$id = $r['id'];
			mysql_class::ex_sqlx("update `tasisat_tmp` set `en` = '1' where `id` ='$id'");
			$out = "مشکل گزارش داده شده رفع شد";
		}
	}
	
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
				$("#new_regdate").hide();
				$("#new_user_reg").hide();
			});
		</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
			رفع مشکل اتاق
		</title>
	</head>
	<body>
		<center><h2><?php echo $out;?></h2></center>
	</body>
</html>
