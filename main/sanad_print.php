<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$id = ((isset($_REQUEST['id']))?(int)$_REQUEST['id']:-1);
	$now = audit_class::hamed_pdate(date("Y-m-d"));
	$sanad = new sanad_class($id);
	$resid = (($sanad->typ ==1)?'دریافت':'پرداخت');
	$moeen = new moeen_class($sanad->moeen_id);
	$moeen = $moeen->name;
	$user = new user_class((int)$_SESSION['user_id']);
	$user = $user->fname.' '.$user->lname;
	$moshtari = new moshtari_class((int)$_SESSION['moshtari_id']);
?>
<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link type="text/css" href="../css/style.css" rel="stylesheet" /> 
	<script type="text/javascript" src="../js/tavanir.js"></script>     
	<style>
	td {text-align:center; }
	</style>
</head>
<body>
	<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
        <div align="center" style="width:18cm;">
		<table border='1' style="width:95%;font-size:14px;height:250px;">
			<tr>
				<th colspan="3" width="80%" >
				<h2><?php echo $moshtari->name; ?></h2><br/>
				</th>
				<td rowspan="2" valign="top" >
					تاریخ چاپ:
						<?php echo $now; ?><br/><br/>
					شماره سند:
						<?php echo audit_class::enToPer($sanad->shomare_sanad); ?>
				</td>
			</tr>
			<tr>
				<th colspan="3">
				<h3>
						رسید
					<?php echo $resid; ?> 
				</h3>
				</th>
			</tr>
			<tr>
				<td>
					حساب
				</td>
				<td>
					توضیحات
				</td>
				<td>
					مبلغ
				</td>
				<td>
					صادر کننده
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $moeen; ?>
				</td>
				<td>
					<?php echo $sanad->tozihat; ?>
				</td>
				<td>
					<?php echo audit_class::enToPer(monize($sanad->mablagh)); ?>
				</td>
				<td>
					<?php echo $user.'&nbsp;'; ?>
				</td>
			</tr>
			<tr height="80px" >
				<td colspan="3" >
					<?php //echo $conf->title; ?>
					&nbsp;
				</td>
				<td style="text-align:right;" rowspan="2">
					امضاء
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:right;font-size:10px;" >
<!-- 					طراحی و ساخت گستره ارتباطات شرق www.gcom.ir -->
					&nbsp;
				</td>
			</tr>
		</table>
        </div>
	<script language="javascript" >
		window.print();
	</script>
</body>
</html>


