<?php
	session_start();
	include_once ("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$user=$conf->user;
	$db=$conf->db;
	$pass=$conf->pass;
	$now_p=pdate("Y-m-d_H-i-s",time());
	exec("rm -rf download/*.*");
	exec("mysqldump -u $user $db -p'$pass' > download/backup-$db-$now_p.gz");
//	if(file_exists('download/backup-$db-$now_p.gz'))
		$out="<a href='download/backup-$db-$now_p.gz' >دانلود</a>";
//	else
//		$out = "<a href='http://www.gcom.ir' >خطا در پشتیبانگیری ، با شرکت گستره ارتباطات شرق تماس حاصل نمایید.<br/>www.gcom.ir</a>";
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
		دانلود
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<h1> <?php echo $out; ?></h1>
			<br/>
			
		</div>
	</body>
</html>
