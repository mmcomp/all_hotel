<?php	session_start();
	include_once("../kernel.php");
	$li = file("help_files.txt");
	$out = '';
	foreach($li as $i => $ff)
	{
		$out .= "\t\t<li>\n";
		$out .= "\t\t\t<a href=\"$ff\" target=\"_blank\">\n";
		$out .= "\t\t\t\t$ff\n";
		$out .= "\t\t\t</a>\n";
		$out .= "\t\t</li>\n";
	}
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
		<?php
echo $out;
		?>
	</body>

</html>
