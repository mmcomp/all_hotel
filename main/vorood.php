<?php	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$takePic = FALSE;
	$vorood = TRUE;
	$user_id = (isset($_REQUEST['user_id'])) ? (int)audit_class::perToEn($_REQUEST['user_id']):(int)$_SESSION['user_id'];
	$vorood = (isset($_REQUEST['vorood'])) ? FALSE:TRUE;
	if(isset($_REQUEST['user_id']))
	{
		$takePic = TRUE;
		$_SESSION['user_vorood'] = $user_id;
		$_SESSION['vorood'] = $vorood;
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
		ورود پرسنل
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<center>
				<form method="post">
					کد پرسنلی : <input type="text" class="inp" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
					خروج : <input type="checkbox" name="vorood" id=vorood" <?php echo (($vorood)?'':"checked=\"checked\""); ?>/>
					<br/>
					<input type="submit" value="ادامه" class="inp" />
				</form>
				<?php
					if($takePic)
					{
				?>
					درصورتی که پنجره زیر فعال نمی باشد و flash player  برای شما نصب نیست ، <a href="download/flash_firefox.exe" target="_blank">این فایل</a> جهت نصب برای مرورگر firefox می‌باشد.<br/>
					<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="download/flash_firefox.exe" width="400" height="500"  id="mymoviename"> 
						<param name="movie" value="croflash.swf" /> 
						<param name="quality" value="high" /> 
						<param name="bgcolor" value="#ffffff" /> 
						<embed src="croflash.swf" quality="high" bgcolor="#ffffff" width="400" height="500" name="mymoviename" align="" type="application/x-shockwave-flash" pluginspage="download/flash_firefox.exe"> 
						</embed> 
					</object> 
				<?php
					}
				?>
			</center>
			<br/>
		</div>
	</body>
</html>
