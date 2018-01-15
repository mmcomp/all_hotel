<?php	
	session_start();
	include_once("../kernel.php");
	if ((isset($_REQUEST['num']))&&(isset($_REQUEST['matn'])))
	{
		$shomare = $_REQUEST['num'];
		$text = $_REQUEST['matn'];
		$send_vaz = sms_class::send_sms($text,$shomare);
		if ($send_vaz)
		{
			echo 'پیامک ارسال شد.';
		}
	}
	echo '<br/><br/><center>ارسال پیامک به صورت دستی';
	echo '<form name="input" action="sms.php" method="post">';
	echo 'شماره<input type="text" name="num">   <br/> ';
	echo 'متن<input type="text" name="matn">  <br/>';
	echo '<input type="submit" value="ارسال">';
	echo '</form><center>';
//	$out = sms_class::send("سلام چطوری؟",'09158809819');
//	var_dump($out);
	/*function getMess($username,$pass,$des,$num)
	{
		$out = array();
		$conf = new conf;
		$my_class = new soapclient_nu($conf->wsdl);
		$sms_list = $my_class->call('sms_receive_list_array',array('username'=>$username,'password'=>$pass,'number'=>$conf->from,'catid'=>'','start'=>'0','perpage'=>'100'));
		if( is_array( $sms_list ))
		{
			foreach( $sms_list as $sms_list_record_arr=>$sms_list_record_arrr )
			{
				$out[] = array("from"=>$sms_list_record_arrr['sender_number'],"text"=>str_replace("\\\\\\&amp;quot;","\"",$sms_list_record_arrr['note']));
			}
		}
		return ($out);
	}
	$sms = getMess('mmcomp','tammar','',1);
	var_dump($sms);*/
	
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />
	</head>
	<body>
	</body>
</html>
