<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$typ = array();
	$typ["روز ورود"]=1;
	$typ["روز خروج"]=2;
	$typ["خدمات"]=3 ;
	$grid = new jshowGrid_new("mehman_sms","grid1");
	$grid->columnHeaders[0] = null;
       	$grid->columnHeaders[1] = "متن پیامک خدمات";
	$grid->columnHeaders[2] = "نوع";
	$grid->columnLists[2]= $typ;
	mysql_class::ex_sql("select count(`id`) as `tedad` from `mehman_sms`",$q);
	if($r = mysql_fetch_array($q))
	{
		if ($r['tedad']>=3)
			$grid->canAdd = FALSE;
	}
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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
			سامانه رزرواسیون بهار
		</title>
<style type="text/css" media="screen">
.square {
    width: 144px;
    height: 144px;
    background: #f0f;
    margin-right: 48px;
    float: left;
}

.transformed {
    -webkit-transform: rotate(90deg) scale(1, 1);
    -moz-transform: rotate(90deg) scale(1, 1);
    -ms-transform: rotate(90deg) scale(1, 1);
    transform: rotate(90deg) scale(1, 1);
}
</style>

	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>

</html>
