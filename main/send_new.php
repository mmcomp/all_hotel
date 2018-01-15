<?php
	define('SMS_NO_ERROR', 0);
	define('SMS_ERROR_AUTHENTICATION_FAILED', 1);
	define('SMS_ERROR_INVALID_FROM_NUMBER', 2);
	define('SMS_ERROR_INVALID_TO_NUMBER', 3);
	define('SMS_ERROR_NOT_ENOUGHT_CREDIT', 4);
	define('SMS_ERROR_INTERNAL_SERVER_ERROR', 5);
	define('SMS_ERROR_NULL_MESSAGE', 6);
	define('SMS_ERROR_TOO_LONG_MESSAGE', 7);
	define('SMS_ERROR_MAGFA_SERVER_ERROR', 8);
	define('SMS_ERROR_INVALID_RECEIVE', 9);

	define('SMS_ERROR_YOU_ARENT_RESELLER', 51);
	define('SMS_ERROR_RESELLER_NOTENOGHT_MONY', 52);
	define('SMS_ERROR_RESELLER_USER_NOTEXIST', 53);
	define('SMS_ERROR_RESELLER_SHARJ', 54);
$sms_error_messages = array(
		SMS_NO_ERROR => 'بدون خطا',
		SMS_ERROR_AUTHENTICATION_FAILED => 'نام کاربری و رمز عبور نامعتبر است',
		SMS_ERROR_INVALID_FROM_NUMBER => 'شماره فرستنده نا معتبر است',
		SMS_ERROR_INVALID_TO_NUMBER => 'شماره گیرنده نامعتبر است',
		SMS_ERROR_NOT_ENOUGHT_CREDIT => 'اعتبار حساب کافی نمی باشد',
		SMS_ERROR_INTERNAL_SERVER_ERROR => 'خطا در ارتباط با سرور',
		SMS_ERROR_NULL_MESSAGE => 'پیام نامعتبر است',
		SMS_ERROR_TOO_LONG_MESSAGE => 'متن پیام بیش از حد طولانی است',
		SMS_ERROR_MAGFA_SERVER_ERROR => 'خطا در برقراری ارتباط با سوئیچ مخابرات',
		SMS_ERROR_INVALID_RECEIVE => 'پیام دریافتی معتبر نمی باشد',
		);


require_once('../class/nusoap.php');
$client = new soapclient_nu('http://sms-ir.ir/webservice/?wsdl', 'wsdl');
$result = $client->call('multiSend', array(
	'username'=>'smssaaed',	'password'=>'159753',
	'message'=>array("تست ارسال",'به محمدل'),
	'to'=>array('09153068145','09159229759'),
	'from'=>"30001825000145"
	));



?>
