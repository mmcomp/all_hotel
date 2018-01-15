<?php	session_start();
	include_once("../kernel.php");
	$str = <<<SS
		سلام #ajans# خوبی؟ <br/>\n
		#hotel# خوبه؟ <br/>\n
		#test# ... <br/>\n
SS;
	$reserve = new reserve_class(4011);
	$reserve->loadWatcher();
	$reserve->watcherAdd('test','خداحافظ');
	$out = $reserve->watcherCompile($str);
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
		</title>

	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>

</html>
