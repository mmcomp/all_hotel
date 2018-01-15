<?php
	session_start();
	include_once('../kernel.php');
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$tabs = array(
		'hotel_reserve',
		'room_det',
		'sanad_reserve',
		'khadamat_det',
		'bug_reserve',
		'changeLog',
		'guest_req',
		'khadamat_akasi',
		'khadamat_cinema',
		'khadamat_gasht',
		'khadamat_transfer',
		'mehman',
		'nezafat',
		'ravabet',
		'reserve_tmp',
		'sandogh_factor',
		'sms_send',
		'sms_vaz',
		'user_DelGhaza',
		'v_jamande'
	);
	$reserve_id = ((isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:-1);
	$reserve_id_new = ((isset($_REQUEST['reserve_id_new']))?(int)$_REQUEST['reserve_id_new']:-1);
	if(isset($_REQUEST['del']) && $reserve_id>0 && $reserve_id_new>0)
	{
		//room_det_class::killReserve($reserve_id);
		//die("<script language=\"javascript\" >alert('deleted!');window.parent.location='index.php';</script>");
		$my = new mysql_class;
		foreach($tabs as $tab)
			$my->ex_sqlx("update $tab set reserve_id = $reserve_id_new where reserve_id =$reserve_id");
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
				شماره رزرو موجود: <input type="text" class="inp" id="reserve_id" name="reserve_id" value="<?php echo $reserve_id; ?>" />
				شماره رزرو جدید: <input type="text" class="inp" id="reserve_id_new" name="reserve_id_new" value="<?php echo $reserve_id_new; ?>" />
				<input type="hidden" name="del" />
				<input type="button" class="inp" onclick="if(confirm('تغییر کامل رزرو انجام شود؟')){document.getElementById('frm1').submit();}" value="تغییر کامل" />
			</form>
		</div>
	</body>
</html>
