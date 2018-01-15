<?php	session_start();
	//include('../kernel.php');
	include('mywsdl.php');
	$comision =  serialize(array('nopick'=>15,'pick'=>10));
	reserve_verify(hash('md5','mehrdad_5'),hash('md5','31048145_5'),134,1,$comision);
	//echo (sms_class::send_sms('test','09153068145'));
	//$h = new hotel_reserve_class(6647);
	//sanadzan_class::factorSabt(5,6,130);
	//var_dump($_SESSION);
	/*
	include('mywsdl.php');
	$ajans_id = serialize(array('ajans_comision_id'=>9,'ajans_id'=>8)) ;
	$aztarikh = '2012-06-20 14:00:00';
	$shab = 2;
	$room = 30;
	$tedad = 1 ;
	$khadamat =serialize(array());
	$fname = 'hamed';
	$lname= 'shakery';
	$tel = '09153068145'; 
	$user_id = 59;
	$extratoz = '';
	$comision = serialize(array('pick'=>10,'nopick'=>15,'takhfif'=>1));
	$aj_id = 15;
	*/
	//echo pay_class::getPishfactorDate('2012-04-14',2);
	//echo hash('md5','mehrdad_5')."<br/>";
	//echo hash('md5','31048145_5');
	//var_dump(search(hash('md5','mehrdad_1'),hash('md5','31048145_1'),$aztarikh,$shab,$tedad,$aj_id));
	//var_dump(register_reserve(hash('md5','mehrdad_1'),hash('md5','31048145_1'),$ajans_id,$aztarikh,$shab,$room,$tedad,$khadamat,$fname,$lname,$tel,$user_id,$extratoz,$comision,$aj_id));
	//var_dump(register_reserve('512dca51e32f7fdbd426fda23561b108','d9636b1a7653623a931ff03cc841ddc5','a:2:{s:8:"ajans_id";s:3:"366";s:17:"ajans_comision_id";i:384;}','2012-06-21 14:00:00',1,'13',2,'a:20:{i:0;a:6:{s:2:"id";i:17;s:6:"ghimat";i:0;s:5:"tedad";s:1:"1";s:7:"voroodi";b:1;s:8:"khorooji";b:1;s:4:"name";s:14:"ترانسفر";}i:1;a:6:{s:2:"id";i:18;s:6:"ghimat";i:0;s:5:"tedad";s:1:"1";s:7:"voroodi";b:1;s:8:"khorooji";b:1;s:4:"name";s:6:"گشت";}i:2;a:6:{s:2:"id";i:19;s:6:"ghimat";i:0;s:5:"tedad";s:1:"2";s:7:"voroodi";b:0;s:8:"khorooji";b:1;s:4:"name";s:12:"صبحانه";}i:3;a:6:{s:2:"id";i:20;s:6:"ghimat";i:0;s:5:"tedad";s:1:"2";s:7:"voroodi";b:0;s:8:"khorooji";b:1;s:4:"name";s:10:"ناهار";}i:4;a:6:{s:2:"id";i:21;s:6:"ghimat";i:0;s:5:"tedad";s:1:"2";s:7:"voroodi";b:1;s:8:"khorooji";b:0;s:4:"name";s:6:"شام";}i:5;a:6:{s:2:"id";i:28;s:6:"ghimat";i:0;s:5:"tedad";s:1:"1";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:14:"ترانسفر";}i:6;a:6:{s:2:"id";i:29;s:6:"ghimat";i:0;s:5:"tedad";s:1:"1";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:6:"گشت";}i:7;a:6:{s:2:"id";i:30;s:6:"ghimat";i:0;s:5:"tedad";s:1:"0";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:12:"صبحانه";}i:8;a:6:{s:2:"id";i:31;s:6:"ghimat";i:0;s:5:"tedad";s:1:"0";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:10:"ناهار";}i:9;a:6:{s:2:"id";i:32;s:6:"ghimat";i:0;s:5:"tedad";s:1:"0";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:6:"شام";}i:10;a:6:{s:2:"id";i:61;s:6:"ghimat";i:0;s:5:"tedad";s:1:"1";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:14:"ترانسفر";}i:11;a:6:{s:2:"id";i:62;s:6:"ghimat";i:0;s:5:"tedad";s:1:"1";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:6:"گشت";}i:12;a:6:{s:2:"id";i:63;s:6:"ghimat";i:0;s:5:"tedad";s:1:"2";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:12:"صبحانه";}i:13;a:6:{s:2:"id";i:64;s:6:"ghimat";i:0;s:5:"tedad";s:1:"2";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:10:"ناهار";}i:14;a:6:{s:2:"id";i:65;s:6:"ghimat";i:0;s:5:"tedad";s:1:"2";s:7:"voroodi";b:0;s:8:"khorooji";b:0;s:4:"name";s:6:"شام";}i:15;a:6:{s:2:"id";i:6;s:6:"ghimat";i:0;s:5:"tedad";s:1:"1";s:7:"voroodi";b:1;s:8:"khorooji";b:1;s:4:"name";s:14:"ترانسفر";}i:16;a:6:{s:2:"id";i:7;s:6:"ghimat";i:0;s:5:"tedad";s:1:"2";s:7:"voroodi";b:0;s:8:"khorooji";b:1;s:4:"name";s:10:"ناهار";}i:17;a:6:{s:2:"id";i:8;s:6:"ghimat";i:0;s:5:"tedad";s:1:"2";s:7:"voroodi";b:0;s:8:"khorooji";b:1;s:4:"name";s:12:"صبحانه";}i:18;a:6:{s:2:"id";i:9;s:6:"ghimat";i:0;s:5:"tedad";s:1:"2";s:7:"voroodi";b:1;s:8:"khorooji";b:0;s:4:"name";s:6:"شام";}i:19;a:6:{s:2:"id";i:16;s:6:"ghimat";i:0;s:5:"tedad";s:1:"1";s:7:"voroodi";b:1;s:8:"khorooji";b:1;s:4:"name";s:6:"گشت";}}','09354102957','09354102957','09354102957',121,'','a:3:{s:7:"takhfif";i:0;s:4:"pick";i:10;s:6:"nopick";i:10;}',-1));
	//var_dump(unserialize('a:3:{s:7:"takhfif";i:0;s:4:"pick";i:15;s:6:"nopick";i:15;}'));
//	$p = pardakht_class::add(3,'2012-08-12 08:57',12000);
//	var_dump($p);
	//echo sms_class::send_sms('salam','09155193104');
/*
if(isset($_POST['mobile']))
{
	$cn=curl_init($conf->wsdl);
	curl_setopt($cn, CURLOPT_POSTFIELDS, "UserNumberID=".$conf->login."&Mobile=".$_POST['mobile']."&Message=".$_POST['message']."&Password=".$conf->password);

	$data = curl_exec($cn);
}
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
                <link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
                <link type="text/css" href="../css/style.css" rel="stylesheet" />
                <link href="../css/ih_style.css" rel="stylesheet" type="text/css" />
                <script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script> 
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>
                </title>
        </head>
        <body >
		<div align="center">
			<form method="POST">
				<input name="mobile" value="09155193104" />
				<input name="message" value="tEsT" />
				<input type="submit" />
			</form>
		</div>
        </body>
</html>

