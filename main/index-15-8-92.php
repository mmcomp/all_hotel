<?php

	session_start();
	include_once ("../kernel.php");
	$pass=((isset($_REQUEST['pass']))?$_REQUEST['pass']:"");
	$user=((isset($_REQUEST['user']))?$_REQUEST['user']:"");
	$kelid = ((isset($_REQUEST['kelid']))?$_REQUEST['kelid']:-1);
	date_default_timezone_set("Asia/Tehran");
	$firstVisit = (isset($_SESSION["login"]) && ($_SESSION["login"] == 1) && isset($_REQUEST["user"]));
	if($firstVisit ||(isset($_SESSION["user_id"]))){	
	function loadUserById($id){
		$out = 'تعریف نشده';
		mysql_class::ex_sql("select fname,lname from user where id=$id",$qq);
		if($r= mysql_fetch_array($qq,MYSQL_ASSOC))
		{
			$out = $r["fname"]." ".$r["lname"];
		}
		return $out;
	}
	function isOdd($inp){
		$out = TRUE;
		if((int)$inp % 2==0){
			$out = FALSE;
		}
		return $out;
	}
	$now_tarikh1 = Date ("Y-m-d 00:00:00");
	$now_tarikh2 = Date ("Y-m-d 23:59:59");	
	if($firstVisit){
//		$matn_sharj = $_SESSION["matn_sharj"];
//		echo $matn_sharj;
		//echo "+++++++first+++++++";
		if(!$conf->setMoshtari((int)moshtari_class::getKey($kelid)))
			die("<script>window.location = 'login.php?stat=wrong_pass&';</script>");
		$is_modir  = FALSE;
		mysql_class::ex_sql("select * from user where user = '".$user."'",$q);
		
		if($r_u = mysql_fetch_array($q,MYSQL_ASSOC))
		{
			if($pass == $r_u["pass"] && (int)$r_u['ajans_id'] == -1)
			{
				$is_modir =(($r_u["typ"]==0)?0:1);
				$_SESSION["user_id"] = (int)$r_u["id"];
				$_SESSION["daftar_id"] = (int)$r_u["daftar_id"];
				$_SESSION["typ"] = (int)$is_modir;
				$user1_id = $_SESSION["user_id"];
				$user1 = new user_class((int)$user1_id);
				$user_grop = $r_u["typ"];
				$user_id = $_SESSION["user_id"];
				if(method_exists($user1,'sabt_vorood'))
					$user1->sabt_vorood();
//////////////////////////////////
				mysql_class::ex_sql("select * from `payam` where (`rec_grop` = '$user_grop' or `rec_user`='$user_id') and `en`='-1'",$qu);
				if($r_g = mysql_fetch_array($qu))
				{
					//echo "<script>alert('شما پیام خوانده نشده دارید. هم اکنون به صفحه مشاهده پیام ها هدایت می شوید');</script>";
					die("<script>window.location = 'showMessage.php?user_id=$user_id&rec_grop=$user_grop&';</script>");
				}
/////////////////////////////////
			}
			else
			{
				die("<script>window.location = 'login.php?stat=wrong_pass&';</script>");
			}
		}
		else
		{
			die("<script>window.location = 'login.php?stat=wrong_user&';</script>");
		}
	}
	if(!$conf->sms)
	{
		mysql_class::ex_sql("select * from `room_det` where `tatarikh` >= '$now_tarikh1' and `tatarikh` <= '$now_tarikh2'",$q);
		while ($r = mysql_fetch_array($q))
		{	
			$reserve_id = $r["reserve_id"];
			$hotel_id = room_class::loadHotelByReserve($reserve_id);
			mysql_class::ex_sql("select * from `hotel_reserve` where `reserve_id` = '$reserve_id'",$qu);
			if ($row = mysql_fetch_array($qu))
			{
				$shomare = $row["tozih"];
			}
			$send_sms = sms_class::khoruj_text_sms($reserve_id,$shomare,$hotel_id);
		}
		mysql_class::ex_sql("select * from `room_det` where `aztarikh` >= '$now_tarikh1' and `aztarikh` <= '$now_tarikh2'",$q);
	        while ($r = mysql_fetch_array($q))
        	{
                	$reserve_id = $r["reserve_id"];
			$hotel_id = room_class::loadHotelByReserve($reserve_id);
	                mysql_class::ex_sql("select * from `hotel_reserve` where `reserve_id` = '$reserve_id'",$qu);
        	        if ($row = mysql_fetch_array($qu))
                	{
                        	$shomare = $row["tozih"];
	                }
        	        $send_sms = sms_class::vorud_text_sms($reserve_id,$shomare,$hotel_id);
	        }
		mysql_class::ex_sql("select `id`,`name` from `khadamat` where `motefareghe`='1'",$q);
	        while ($r = mysql_fetch_array($q))
        	{
			$kh_id = $r['id'];
			$hotel_id = room_class::loadHotelByReserve($reserve_id);
			mysql_class::ex_sql("select `reserve_id` from `khadamat_det` where `reserve_id`>0 and DATE(`tarikh`) >= '$now_tarikh1' and (`tarikh`) <= '$now_tarikh2' and `khadamat_id`='$kh_id'",$qu);
			while ($r_u = mysql_fetch_array($qu))
        		{
				$reserve_id = $r_u["reserve_id"];
			        mysql_class::ex_sql("select * from `hotel_reserve` where `reserve_id` = '$reserve_id'",$qu1);
			        if ($row = mysql_fetch_array($qu1))
		        	{
		                	$shomare = $row["tozih"];
			        }
			        $send_sms = sms_class::gasht_text_sms($reserve_id,$shomare,$hotel_id);
			}
		}
	}
	$tmp_bedehi="";
	if ((isset($_SESSION["moshtari_id"])) && ($_SESSION["moshtari_id"]>0))
	{
		$moshtari_id = $_SESSION["moshtari_id"];
		$moshtari = new moshtari_class($moshtari_id);
                $today = date("Y-m-d");
                $aztarikh = $moshtari->aztarikh;
                $t_pardakhti = abs($moshtari->tedadpardakhti);
                $mablagh = $moshtari->mablagh;
		$aztarikh = explode(' ',$aztarikh);
		$aztarikh = $aztarikh[0];
                $diff = strtotime($today) - strtotime($aztarikh);
                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $modat_gharardad = $months;
                $bedehi = ($modat_gharardad - $t_pardakhti)+1;
                if ($bedehi > 0)
                	$matn_sharj =(int) $bedehi * $mablagh;
                else
                	$matn_sharj = 0;
/*		$matn_sharj = (int)$_SESSION["matn_sharj"];
		$bedehi = $_SESSION["bedehi"];
		$moshtari_id = $_SESSION["moshtari_id"];*/
		if ($bedehi >0 )
                        $bedehi_bu = "<button type=\"button\" class=\"inp\" onclick=\"window.location = 'pish_factor.php?moshtari_id=$moshtari_id&';\">پرداخت</button>";
		else
			$bedehi_bu = "";

		if ($bedehi >= 2 )
			$tmp_bedehi = "<div style=\"color:red;\">میزان شارژ معوقه: $matn_sharj ریال $bedehi_bu</div>";
		if ($bedehi < 2 )
                	$tmp_bedehi = "<div style=\"color:green;\">میزان شارژ معوقه : $matn_sharj ریال $bedehi_bu</div>";
	}
//var_dump($_SESSION);
	if (isset($_SESSION['user_id']))
	{
      		$se = security_class::auth((int)$_SESSION['user_id']);
		if(!$se->can_view)
			die("<script language='javascript' >window.location = 'login.php'; </script>");
		// -------a جهت ارسال کاربر تاسیسات و سوئر وایزر به صفحه رک
		if($se->detailAuth('tasisat') || $se->detailAuth('super'))
			die("<script language='javascript' >window.location = 'rooms_vaziat.php'; </script>");
		$isAdmin = $se->detailAuth('all');
	}
	$user_id = (int)$_SESSION["user_id"];
	if(isset($_SESSION["typ"]) && $_SESSION["typ"]!=""  )
	{
		$is_modir = $_SESSION["typ"];
	}
	$forms = null;
	$unRead = msg_class::unReadMsg($user_id);
	
	$ajans_title = (($conf->is_hesabdari !== '')?'مدیریت مشتریان':'مدیریت آژانس ها');	
	$tmp1 = array('main_param'=>'پرونده','dafater'=>'مدیریت دفاتر','agency'=>$ajans_title,'user_manage'=>'مدیریت کاربران','hozoor_ghiab'=>'حضور و غیاب','m_hotel'=>'مدیریت هتل','hotel_daftar'=>'دسترسی هتل','watcher'=>'تعریف واچر','mehman_sms'=>'تعریف پیامک','paziresh_info'=>'اطلاعات پذیرش','send_file'=>'ارسال فایل','shenavar'=>'رزرو شناور','exit'=>'خروج','accu'=>'حسابداری','h_kol'=>'حساب کل','h_moeen'=>'حساب معین','sanad_new'=>'ثبت سند','sanad_daftar'=>'ثبت دریافتی/پرداختی','_belit'=>'ثبت سند بلیت','msg'=>'پیامها '.(($unRead==0)?'':"<span style='color:red;'>($unRead)</span>"),'send_msg'=>'ارسال پیام','load_msg'=>'مشاهده پیامها','anbar_dari'=>'انبارداری','m_anbar'=>'مدیریت انبار','kala_no'=>'نوع‌کالا','kala'=>'کالا','vorood_anbar'=>'ورود به انبار','khorooj_anbar'=>'خروج از انبار','bazgasht_anbar'=>'بازگشت به انبار','kala_vahed'=>'تعریف واحد','cost'=>'کاست','sabt_kala'=>'کالای ترکیبی','sabt_jozeeat_kala'=>'جزئیات کالای ترکیبی','reports'=>'گزارش‌ها','sanad_gozaresh'=>'گزارش اسناد ','gozareshmande'=>'گزارش مانده','tarikh_gozaresh'=>'گزارش تاریخ اسناد','gozaresh_eshghal'=>'گزارش درصد اشغال','gozaresh_sms'=>'گزارش پیامک‌ها','gozaresh_sms_send'=>'گزارش پیامک های ارسال شده','sms_unread'=>'پیامک های خوانده نشده','gozaresh_ghaza'=>'گزارش غذا','backup'=>'پشتیبان','back'=>'پشتیبان گیری','restor'=>'بروز رسانی پشتیبان','ticket'=>'ارتباط با پشتیبانی','onUser'=>'‌کاربران','listUser'=>'لیست کاربران آنلاین','listInOut'=>'لیست ورود و خروج کاربران','paziresh_main'=>'پذیرش','paziresh'=>'پذیرش هتل','check_factors'=>'چاپ خودکار','room_status'=>'رک','rack_khanedar'=>'رک خانه داری','room_prob'=>'ثبت مشکلات اتاق','vasael_jamande'=>'اقلام جامانده میهمان','sanad_belit'=>'ثبت سند بلیت','enteghad_modir'=>'انتقاد و پیشنهاد به مدیر','hesab_close'=>'بستن سال مالی', 'taraz'=>'گزارش تراز سود و زیان','bank'=>'نقد و بانک','sms_single'=>'ارسال پیامک تبلیغاتی','mehmanList'=>'لیست میهمانان','driverName'=>'نام راننده','targetName'=>'ثبت مسیر ترانسفر','nobatName'=>'ثبت نوبت');
	if($conf->sms)
		$tmp1['sms_groohi']='ارسال پیامک هوشمند';
	if($conf->room_vaziat)
	{
		$tmp1['room_vaziat_3']='نظافت اتاق';
		$tmp1['room_vaziat_4']='تعمیر اتاق';
		$tmp1['room_vaziat_5']='خروج از سرویس اتاق';
		$tmp1['room_vaziat_2']='اتاق نظافت شده';
	}
	$tmp2=array('main_param'=>array('dafater'=>null,'agency'=>null,'user_manage'=>null,'hozoor_ghiab'=>null,'m_hotel'=>null,'hotel_daftar'=>null,'watcher'=>null,'mehman_sms'=>null,'paziresh_info'=>null,'ticket'=>null,'enteghad_modir'=>null,'shenavar'=>null),'accu'=>array('h_kol'=>null,'h_moeen'=>null,'sanad_new'=>null,'sanad_daftar'=>null,'sanad_belit'=>null,'hesab_close'=>null,'taraz'=>null,'bank'=>null));
        if($conf->anbar)
        {
                $tmp2['anbar_dari'] = array('m_anbar'=>null,'kala_no'=>null,'kala'=>null,'vorood_anbar'=>null,'khorooj_anbar'=>null,'bazgasht_anbar'=>null,'kala_vahed'=>null);
                $tmp2['cost'] = array('sabt_kala'=>null,'sabt_jozeeat_kala'=>null);
        }
        $tmp2['reports'] = array('sanad_gozaresh'=>null,'gozareshmande'=>null,'tarikh_gozaresh'=>null,'gozaresh_eshghal'=>null,'gozaresh_sms'=>null,'gozaresh_sms_send'=>null,'sms_unread'=>null,'gozaresh_ghaza'=>null);
        $tmp2['backup'] = array('back'=>null,'restor'=>null);
        $tmp2['onUser'] = array('listUser'=>null,'listInOut'=>null);
        $tmp2['paziresh_main'] = array('paziresh'=>null,'check_factors'=>null,'room_status'=>null,'rack_khanedar'=>null,'room_prob'=>null,'vasael_jamande'=>null);
	if($conf->sms)
	{
		//$tmp2['main_param']['sms_groohi'] = null;
		$tmp2['main_param']['sms_single'] = null;
	}
	if($conf->getMoshtari()=='')
	{
		$tmp1['moshtari'] = 'مدیریت مشتریان';
		$tmp2['main_param']['moshtari'] = '';
	}
	if($conf->room_vaziat)
	{	
		
		$tmp2['paziresh_main']['room_vaziat_3']= null;
		$tmp2['paziresh_main']['room_vaziat_4']= null;
		$tmp2['paziresh_main']['room_vaziat_5']= null;
		$tmp2['paziresh_main']['room_vaziat_2']= null;
	}
	if($conf->front_office_enabled)
	{
		$tmp1['sandogh_def'] = 'تعریف صندوق';
		$tmp2['main_param']['sandogh_def']= null;
		$tmp1['sandogh_user'] = 'دسترسی فرانت آفیس';
		$tmp2['main_param']['sandogh_user']= null;

		$tmp1['sandogh'] = 'موارد فرانت آفیس';
		$tmp2['main_param']['sandogh']= null;
	}
	if((user_class::hasSondogh($user_id) || $se->detailAuth('all') || $se->detailAuth('frontOffice')) && $conf->front_office_enabled)
	{
		$tmp1['sandogh_det']='فرانت آفیس';
		$tmp2['main_param']['sandogh_det']= null;
		$tmp1['sandogh_factors'] = 'عملیات فرانت آفیس';
		$tmp2['main_param']['sandogh_factors']= null;
	}
	if($conf->is_hesabdari!=='')
	{
		$tmp1['factor'] = 'صدور فاکتور';
		$tmp1['factor_khadamat'] = 'تعریف خدمات فاکتور';
		$tmp1['gozaresh_factor_khadamat'] = 'گزارش فاکتورهای دستی';
		$tmp2['main_param']['factor'] = null;
		$tmp2['main_param']['factor_khadamat'] = null;
		$tmp2['main_param']['gozaresh_factor_khadamat'] = null;
	}
	$tmp2['main_param']['mehmanList'] = null;
	$tmp2['main_param']['driverName'] = null;
	$tmp2['main_param']['targetName'] = null;
	$tmp2['main_param']['nobatName'] = null;
	$tmp2['main_param']['exit'] = null;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $conf->title; ?></title>
    <link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
	<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <link href="../css/style.css" rel="stylesheet" type="text/css" />
    <link href="../css/ih_style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
	<script type="text/javascript" src="../js/tavanir.js"></script>
	<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script> 
	<style>
		tr{cursor: pointer;}
	</style>
    <script type="text/javascript" >
                        function mehrdad_ajaxFunction(func){
                                var ajaxRequest;  // The variable that makes Ajax possible!
        
                                try{
                                        // Opera 8.0+, Firefox, Safari
                                        ajaxRequest = new XMLHttpRequest();
                                } catch (e){
                                        // Internet Explorer Browsers
                                        try{
                                                ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
                                        } catch (e) {
                                                try{
                                                        ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
                                                } catch (e){
                                                        // Something went wrong
                                                        alert("ﻡﺭﻭﺭگﺭ ﺶﻣﺍ ﻕﺎﺒﻟیﺕ ﺁژﺍکﺱ ﺭﺍ ﻥﺩﺍﺭﺩ ﻞﻄﻓﺍً ﺍﺯ ﻡﺭﻭﺭگﺭ ﺝﺩیﺪﺗﺭ ﻭ پیﺵﺮﻔﺘﻫ ﺕﺭی ﻡﺎﻨﻧﺩ ﻑﺍیﺮﻓﺍکﺱ ﺎﺴﺘﻓﺍﺪﻫ کﻥیﺩ");
                                                        return false;
                                                }
                                        }
                                }
                                // Create a function that will receive data sent from the server
                                ajaxRequest.onreadystatechange = function(){
                                        if(ajaxRequest.readyState == 4){
                                                func(ajaxRequest.responseText);
                                        }
                                };
                                var queryString = "?r="+Math.random()+"&";
                                //alert(queryString);
                                ajaxRequest.open("GET", "time.php" + queryString, true);
                                ajaxRequest.send(null); 
                        }
	function showClock(tim)
	{
		document.getElementById('tim').innerHTML = tim;
		setTimeout("mehrdad_ajaxFunction(showClock);",1000);
	}
	function sendState(inp)
	{
		if(confirm("<?php echo lang_fa_class::change_state_confirm;  ?> "))
		{
			document.getElementById('progress').style.display='';
			window.location ="index.php?state="+inp+"&";
		}
	}
   </script>
   <style>
	div.fadeMe 
	{
		opacity:0.8; 
		background-color:#000; 
		width:100%; 
		height:100%; 
		z-index:10;
		top:0; 
		left:0; 
		position:fixed; 
	}
   </style>
</head>
<body style="width:100%;height:100%;">
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		
	<?php
		$msgs = msg_class::loadMsgs($user_id);
		if($msgs !== FALSE)
			echo $msgs;
	?>
    <div id="header">
	<table width="100%" border="0">
	<tr>
		<td>
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</td>
		<td width="90%" style="vertical-align:center;">
       			<h2 align="center" style="color:#FFF;" ><?php echo $conf->title; ?>&nbsp;&nbsp;&nbsp;&nbsp;</h2>
		</td>
	</tr>
	</table>
    </div><!-- header -->
    	
    <div id="main"><div id="main2">
	<div class="body">
	<?php 
		if($conf->bedehi == '')
			echo "<center>$tmp_bedehi</center>";
		$me = new menu_class($tmp1,$tmp2,$se);
		echo $me->output;
	?>

		</div>	
            <div id="sidebar">
                <div align="center">
	                <table width="100%" border="0">
				<?php
					$us = new user_class($user_id);				
					echo page_icons_class::getIcons($us->typ,$user_id,($se->detailAuth('all') || $se->detailAuth('frontoffice')));	
				?>
	                </table>
                </div>
            </div>  	              
                                
            <div class="clearing">&nbsp;</div>   
    </div></div><!-- main --><!-- main2 -->
    <div id="footer" >
	<center>
        طراحی و ساخت <a href="http://www.gcom.ir/">گستره ارتباطات شرق</a><img src="../img/gcom.png" width="32" ><br/>
	<span id="tim">TEST</span>
	</center>
    </div>
	<script language="javascript">
	showClock('');
	</script>
<?php 
	}
	else
	{
		//header("Location: login.php");
?>
	<script language="javascript" >
		window.location = 'login.php';
	</script>
<?php
	}
?>
</body>
</html>
