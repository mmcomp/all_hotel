<?php
	include_once('class/conf.php');
	include_once('class/mysql_class.php');
	if(isset($_REQUEST['mobile']) && isset($_REQUEST['message']))
		mysql_class::ex_sqlx("insert into `in_sms` (`mobile`,`message`) values ('".$_REQUEST['mobile']."','".$_REQUEST['message']."')");
?>