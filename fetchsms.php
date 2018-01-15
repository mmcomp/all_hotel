<?php
	include_once('kernel.php');
	if(isset($_REQUEST['mobile']) && isset($_REQUEST['message']))
		mysql_class::ex_sqlx("insert into `in_sms` (`mobile`,`message`) values ('".$_REQUEST['mobile']."','".$_REQUEST['message']."')");
?>
