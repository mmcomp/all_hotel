<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
        mysql_class::ex_sql("select `id` from `access` order by id",$q);
	while($r = mysql_fetch_array($q))
	{
		$qt = null;
		mysql_class::ex_sql("select `id` from `access_det` where `frase` = 'block' and `acc_id` = ".$r['id'],$qt);
		if($rt = mysql_fetch_array($qt))
			$qt = null;
		else
		{
			echo "insert into `access_det` (`acc_id`,`frase`) values ('".$r['id'].",'block')<br/>\n";
			//mysql_class::ex_sqlx("insert into `access_det` (`acc_id`,`frase`) values ('".$r['id']."','block')");
		}
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
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
		</div>
	</body>

</html>
