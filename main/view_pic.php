<?php
	session_start();
	include_once('../kernel.php');
	/*
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	*/
	if(isset($_REQUEST['pic_addr']))
	{
		$pic_addr =$_REQUEST['pic_addr'];
		$out = "<img src='$pic_addr' width='18cm' />";
	}
	else
		$out = 'آدرس تصویر موجود نیست';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
                <title>
                </title>
        </head>
        <body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" >
			<?php
				echo $out;
			?>
		</div>
        </body>
</html>
