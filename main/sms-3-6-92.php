<?php	session_start();
	include_once("../kernel.php");
	$out = sms_class::send_sms("سلام \n  چطوری؟",'09159229759');
	var_dump($out);
?>
