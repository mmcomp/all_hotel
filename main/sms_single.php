<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
        	die(lang_fa_class::access_deny);
	$matn = (isset($_REQUEST['message']))?$_REQUEST['message']:'';
	$mobile = (isset($_REQUEST['mobile']))?$_REQUEST['mobile']:'';
	$mobiles = explode(',',$mobile);
	$msg = '';
	if($mobile != '' && $matn != '' && count($mobiles) >= 1)
	{
		if(sms_class::send_sms($matn,$mobiles))
			$msg = 'ارسال با موفقیت انجام شد.';
		else
			$msg = 'خطا در ارسال.';
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
		<title>
			ارسال پیامک	
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
				<?php echo $msg; ?>
			<br/>
			<form id='frm1'  method='GET' >
				شماره موبایل‌ها به تفکیک کاما انگلیسی : (09131245656,09153007771):
				<textarea id="mobile" name="mobile"></textarea>
				متن پیام:
				<textarea id="message" name="message"></textarea>
				<input type="submit" value="ارسال" class="inp"/>
			</form>
			<br/>
		</div>
	</body>
</html>
