<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$out = '';
	$q = ((isset($_REQUEST['q']))?$_REQUEST['q']:'');
	$moshtari_id = ((isset($_REQUEST['moshtari_id']))?(int)$_REQUEST['moshtari_id']:-1);
	if($moshtari_id <= 0)
		die('مشتری نا مشخص');
	if($q != '')
	{
		$my_moshtari = (int)$_SESSION['moshtari_id'];
		$all = ((isset($_REQUEST['all']))?TRUE:FALSE);
		$q = explode("\n",$q);
		if(!$all)
		{
			$conf->setMoshtari($moshtari_id);
			for($i =0;$i < count($q);$i++)
				$out = mysql_class::ex_sqlx($q[$i]);
			$conf->setMoshtari($my_moshtari);
		}
		else
		{
                        $db = $conf->db;
                        $sql = "select `id` from  `moshtari` ";
                        $conn = mysql_connect($conf->host,$conf->user,$conf->pass);
                        if(!($conn==FALSE)){
                                if(!(mysql_select_db($db,$conn)==FALSE)){
                                        mysql_query("SET NAMES 'utf8'");
                                        $qq = mysql_query($sql,$conn);
                                        mysql_close($conn);
                                }
                        }
			while($r = mysql_fetch_array($qq))
			{
				$moshtari_id = (int)$r['id'];
				$conf->setMoshtari($moshtari_id);
				for($i =0;$i < count($q);$i++)
		                        $out .= mysql_class::ex_sqlx($q[$i]);
			}
			$conf->setMoshtari($my_moshtari);
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
		سامانه ارزیابی عملکرد کارکنان شرکت مدیریت تولید نیروگاه‌های گازی خراسان
		</title>
<style type="text/css" media="screen">
</style>

	</head>
	<body style="direction:ltr;">
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<form id="frm1">
				<h3>ex_sqlx(<textarea type="text" name="q" id="q" ><?php echo implode("\n",$q); ?></textarea>)</h3>
				All <input type="checkbox" id="all" name="all" checked='checked' />
				<input type="hidden" id="moshtari_id" name="moshtari_id" value="<?php echo $moshtari_id; ?>" />
				<input type="submit" value="exec" /> 
			</form>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>

</html>
