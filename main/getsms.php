<?php
	include_once("../kernel.php");
	conf::setMoshtari(1);
	if (isset($_REQUEST['from']))
		$number = '0'.$_REQUEST['from'];
	else
		$number = '000000000000';
	if (isset($_REQUEST['text']))
		$matn = $_REQUEST['text'];
	else
		$matn = '-2';
	mysql_class::ex_sqlx("insert into `in_sms` (`id`, `mobile`,`message`,`en`)VALUES (NULL,'$number','$matn','0')");
?>


