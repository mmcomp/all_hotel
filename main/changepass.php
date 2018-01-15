<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(isset($_POST['pass']))
	{
		$pass = $_POST['pass'];
		mysql_class::ex_sqlx("update `user` set `pass` = '$pass' where `id` = ".$_SESSION['user_id']);
		die("<script language=\"javascript\">alert('رمز با موفقیت تغییر یافت.');window.close();</script>");
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
		<script language="javascript">
			function changepass()
			{
				if(document.getElementById('pass1').value == document.getElementById('pass').value)
					document.getElementById('frm1').submit();
				else
					alert('اطلاعات را درست وارد کنید.');
			}
		</script>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<center>
				<form method="post" id="frm1">
					رمز عبور جدید خود را وارد کنید:
					<br/>
					<input type="password" class="inp" id="pass1" /><br/>
					رمز عبور جدید را مجدداً وارد کنید :<br/>
					<input type="password" class="inp" id="pass" name="pass" /><br/>
					<input type="button" class="inp" value="ثبت" onclick="changepass();" />
				</form>
			</center>
			<br/>
		</div>
	</body>
</html>
