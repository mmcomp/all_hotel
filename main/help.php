<?php	session_start();
	include_once("../kernel.php");
/*
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(!isset($_SERVER['HTTP_REFERER']))
		die("<script> alert('خطا'); window.close(); </script>");
*/
	$out = "راهنمایی پیدا نشد.";
	$h_ref = $_SERVER['HTTP_REFERER'];
	$h_ref = explode('?',$h_ref);
	$h_ref = $h_ref[0];
	$h_ref = explode('/',$h_ref);
	$h_ref = $h_ref[count($h_ref)-1];
	$h_ref = explode('.',$h_ref);
	if(count($h_ref)>1 && $h_ref[count($h_ref)-1] == 'php')
	{
		$h_ref = implode('.',$h_ref);
		if(file_exists("../help/help_$h_ref.html"))
		{
			$out = '';
			$tmp = file("../help/help_$h_ref.html");
			for($i = 0;$i < count($tmp);$i++)
				$out .= $tmp[$i]."\n";
		}
	}
	echo $out;
?>
