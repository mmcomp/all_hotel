<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
               die(lang_fa_class::access_deny);
	$user_id = (int)$_SESSION['user_id'];
	$msg = '';
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==1 && $_REQUEST['name']!='')
	{
		$name = $_REQUEST['name'];
		if(mysql_class::copyAndEmptyTable('sanad',"sanad_$name"))
			if(mysql_class::copyAndEmptyTable('sanad_reserve',"sanad_reserve_$name"))
				if(mysql_class::copyAndEmptyTable('sanad_daftar',"sanad_daftar_$name"))
					if(mysql_class::copyAndEmptyTable('sanad_sandogh',"sanad_sandogh_$name"))
						if(mysql_class::copyAndEmptyTable('sanad_anbar',"sanad_anbar_$name"))
						{
							if(mysql_class::startSanad($name,$user_id))
								$msg = ' بستن سال مالی '.$name.' با موفقیت انجام شد';
							else
								$msg = 'خطا در ثبت سند افتتاحیه';
						}
						else
						{
							mysql_class::ex_sqlx('drop table `sanad_sandogh`');
							mysql_class::copyAndEmptyTable("sanad_sandogh_$name",'sanad_sandogh');
							mysql_class::ex_sqlx("drop table `sanad_sandogh_$name`");
							mysql_class::ex_sqlx('drop table `sanad_daftar`');
							mysql_class::copyAndEmptyTable("sanad_daftar_$name",'sanad_daftar');
							mysql_class::ex_sqlx("drop table `sanad_daftar_$name`");
							mysql_class::ex_sqlx('drop table `sanad_reserve`');
							mysql_class::copyAndEmptyTable("sanad_reserve_$name",'sanad_reserve');
							mysql_class::ex_sqlx("drop table `sanad_reserve_$name`");
							mysql_class::ex_sqlx('drop table `sanad`');
							mysql_class::copyAndEmptyTable("sanad_$name",'sanad');
							mysql_class::ex_sqlx("drop table `sanad_$name`");
							$msg = 'خطا در بستن سال مالی';
						}
					else
					{
						mysql_class::ex_sqlx('drop table `sanad_daftar`');
						mysql_class::copyAndEmptyTable("sanad_daftar_$name",'sanad_daftar');
						mysql_class::ex_sqlx("drop table `sanad_daftar_$name`");
						mysql_class::ex_sqlx('drop table `sanad_reserve`');
						mysql_class::copyAndEmptyTable("sanad_reserve_$name",'sanad_reserve');
						mysql_class::ex_sqlx("drop table `sanad_reserve_$name`");
						mysql_class::ex_sqlx('drop table `sanad`');
						mysql_class::copyAndEmptyTable("sanad_$name",'sanad');
						mysql_class::ex_sqlx("drop table `sanad_$name`");
						$msg = 'خطا در بستن سال مالی';
					}
				else
				{
					mysql_class::ex_sqlx('drop table `sanad_reserve`');
					mysql_class::copyAndEmptyTable("sanad_reserve_$name",'sanad_reserve');
					mysql_class::ex_sqlx("drop table `sanad_reserve_$name`");
					mysql_class::ex_sqlx('drop table `sanad`');
					mysql_class::copyAndEmptyTable("sanad_$name",'sanad');
					mysql_class::ex_sqlx("drop table `sanad_$name`");
					$msg = 'خطا در بستن سال مالی';
				}
			else
			{
				mysql_class::ex_sqlx('drop table `sanad`');
				mysql_class::copyAndEmptyTable("sanad_$name",'sanad');
				mysql_class::ex_sqlx("drop table `sanad_$name`");
				$msg = 'خطا در بستن سال مالی';
			}
		else
			$msg = 'خطا در بستن سال مالی';
		
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link type="text/css" href="../css/style.css" rel="stylesheet" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
بستن سال مالی
		</title>

		<script language="javascript" >
			function sendFrm()
			{
				if(confirm('آیا سال مالی بسته شود؟'))
				{
					if( document.getElementById('name').value!='')
					{
						document.getElementById('mod').value = 1;
						document.getElementById('frm1').submit();
					}
					else
						alert('نام سال مالی را وارد کنید');
				}

			}
		</script>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center" >
			<?php echo $msg; ?>
			<div align="center" style="border:1px solid;width:400px;margin:10px;padding:10px;" >
				<form id="frm1" method="post" >
					<span  style="font-size:50px;height:100px;width:350px;" >
					نام سال مالی
					</span><br />
					<input type="text" name="name" id="name" style="font-size:50px;height:100px;width:350px;">
					<br/>
					<input type="button" value="بستن سال مالی" style="font-family:tahoma;font-size:22px;height:100px;width:350px;" onclick="sendFrm();" >
					<input type="hidden" id="mod" name="mod" value=0 >
				</form>
			</div>
		</div>
	</body>

</html>
