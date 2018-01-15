<?php
	session_start();
	include("../kernel.php");
	require("../class/nusoap.php");
	$client = new soapclient_nu('http://192.168.1.11/h_hamze/main/mywsdl.php?wsdl', 'wsdl');
        $result = $client->call('testfunction', array('input'=>'mehrdad'));
	var_dump($result);
	
?>
