<?php	
	session_start();
	include_once("../kernel.php");
	//$out = sms_class::send_sms("سلام \n  چطوری؟",'09159229759');
	$out = sms_class::send_sms("hello",'09155193104');
	var_dump($out);
/*class HttpSample 
{
	public $USERNAME = "mmcomp";  // your username (fill it with your username)
	public $PASSWORD = "tammar"; // your password (fill it with your password)
	// base http url
	private  $BASE_HTTP_URL = "http://www.payam-resan.com.com/APISend.aspx?";
	public function enqueueSample() 
	{
		$USERNAME = "mmcomp";  // your username (fill it with your username)
		$PASSWORD = "tammar"; // your password (fill it with your password)        
		$senderNumber = "30007546000296"; // [FILL] sender number ; which is your 3000xxx number
		$recipientNumber = "09158809819"; // [FILL] recipient number; the mobile number which will receive the message (e.g 0912XXXXXXX)
		$message = urlencode("payam-resan.com http-enqueue test"); // [FILL] the content of the message; (in url-encoded format !)      
        // creating the url based on the information above
		$url = "http://www.payam-resan.com.com/APISend.aspx?" .
                "Username=" . $USERNAME . "&Password=" . $PASSWORD . 
                "&From=" . $senderNumber . "&To=" . $recipientNumber .
                "&Text=" . $message  ;
        // sending the request via http call
		$result = file_get_contents($url);
		$send = SendMessage‬‬($USERNAME,$PASSWORD,$message,$recipientNumber,$senderNumber,1,0);
return($send);
        // Now you can compare the response with 0 or 1
	}
    // this method provides a simple way of calling a url
	private function call($url)
	{
		return file_get_contents($url);
	}
}
	$result = HttpSample::enqueueSample();
	var_dump($result);*/
?>
