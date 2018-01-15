<?php
	session_start();
	include_once('../kernel.php');
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$reserve_id = ((isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:-1);
	if(isset($_REQUEST['del']) && $reserve_id>0)
	{
		room_det_class::killReserve($reserve_id);
		//die("<script language=\"javascript\" >alert('deleted!');window.parent.location='index.php';</script>");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="../css/style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript">
			
    </script>
		<title>
		</title>
	</head>
	<body>	
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<br/>
		<div align="center" >
			<form id="frm1">
				شماره رزرو : <input type="text" class="inp" id="reserve_id" name="reserve_id" value="<?php echo $reserve_id; ?>" />
				<input type="hidden" name="del" />
				<input type="button" class="inp" onclick="if(confirm('حذف کامل رزرو انجام شود؟')){document.getElementById('frm1').submit();}" value="حذف کامل" />
			</form>
		</div>
	</body>
</html>
