<?php	session_start();
	unset($_SESSION['factor_shomare']);
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);	
        if(!$se->can_view)
               die(lang_fa_class::access_deny);
	$user_id = (int)$_SESSION['user_id'];
	mysql_class::ex_sql("select `typ` from `user` where `id`='$user_id'",$q);
	if($r = mysql_fetch_array($q))
	{
		if ($r["typ"]=='21')
			$user = "order";
		else
			$user = "admin";
	}
	$sandogh_id = (isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<title>
		فرانت آفیس
		</title>
		<script language="javascript" >			
			function showModir()
			{
				
				document.getElementById('modir').submit();
			}
			function showOrder()
			{
				
				document.getElementById('order').submit();
			}
		</script>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<form id="modir" action="sandogh_det.php" >
			<input type="hidden" name="mod1" id="mod1" value="2">
			<input type="hidden" name="sandogh_id" id="sandogh_id" value="<?php echo $sandogh_id;?>">
		</form>
		<form id="order" action="sandogh_det.php">
			<input type="hidden" name="mod1" id="mod1" value="3">
			<input type="hidden" name="sandogh_id" id="sandogh_id" value="<?php echo $sandogh_id;?>">
		</form>
		<div  id='f_vorood' style="margin:40px;padding:5px;">
			<table style="width:100%"  >
				<tr>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>			
					<?php 
						if ($user=="order")
						{
					?>
						<td valign='top'><img  src='../img/order.png' onclick='showOrder();'/></td>
					<?php
						}
						if ($user=="admin")
						{
					?>
						<td align='left'><img src='../img/order.png' onclick='showOrder();'/></td>
						<td ><img src='../img/admin_res.png' onclick='showModir();'/></td>
					<?php
						}
					?>
				</tr>
			</table>
		</div>
			
	</body>

</html>
