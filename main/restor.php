<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$sq = new mysql_class;
	$db = $sq->db;
	$out = "";
	if(isset($_FILES['uploadedfile']))
	{
		$target_path = "download/restore/";
		$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
			//$out = shell_exec("mv ../download/restore/".basename( $_FILES['uploadedfile']['name'])." ../download/restore/res.sql.gz;gunzip ../download/restore/res.sql.gz;mysql -u root tavanir -p'123456'<../download/restore/res.sql;");
			$out = shell_exec("mysql -u root $db -p'123456' < download/restore/".basename( $_FILES['uploadedfile']['name']).";");
			$out = "<script> alert('بروزرسانی پشتیبان با موفقیت انجام گرفت');window.close(); </script>";
		} else{
			$out =  "در بروزرسانی نسخه پشتیبان مشکلی پیش آمده ، لطفاًً مجدداً ارسال نمایید .";
		}
	}
?>
<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link type="text/css" href="../css/style.css" rel="stylesheet" />      
</head>
<body>
	<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
	<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
        <center>
		<br/><br/><br/>
		<form enctype="multipart/form-data" method="POST">
			<input  type="hidden" name="MAX_FILE_SIZE" value="999999999" />
			نسخه پشتیبان مورد نظر را انتخاب نمایید ( توجه داشته باشید که بهتر است قبل از بروزرسانی یک نسخه پشتیان از سیستم گرفته شود) : <input name="uploadedfile" class="inp" type="file" /><br />
			<input class="inp" type="submit" value="بروزرسانی" />
		</form>
		<?php echo $out; ?>
        </center>
</body>
</html>


