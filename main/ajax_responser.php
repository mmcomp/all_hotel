<?php
	session_start();
	include('../kernel.php');
	include_once '../simplejson.php';
	$command = ((isset($_REQUEST['command']))?$_REQUEST['command']:'none');
	$out = '';
	if($command != 'none')
	{
		$rooms = isset($_REQUEST['rooms']) ? $_REQUEST['rooms'] : null;
		$rooms_ezf = isset($_REQUEST['rooms_ezf']) ? $_REQUEST['rooms_ezf'] : null;
		$ajans_id = isset($_REQUEST['ajans_id']) ? (int)$_REQUEST['ajans_id'] : -1;
		$kh = array();
		if(isset($_REQUEST['kh']))
		{
			$kh = str_replace("\\",'',$_REQUEST['kh']);
			$kh = fromJSON($kh);
		}
		$ajr = new ajax_responser($command,(int)$_REQUEST['hotel_id'],$_REQUEST['test_date'],(int)$_REQUEST['delay'],$rooms,$_REQUEST['nafar'],$_REQUEST['daftar_id'],$ajans_id,$kh,$rooms_ezf);
		$out = $ajr->getOutput;
		if($out === TRUE)
			$out = 'TRUE';
		else if($out === FALSE)
			$out = 'FALSE';
	}
	die("$out");
?>
