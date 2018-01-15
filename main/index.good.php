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
	if($firstVisit){
//		$matn_sharj = $_SESSION["matn_sharj"];
//		echo $matn_sharj;
		//echo "+++++++first+++++++";
		if(!$conf->setMoshtari((int)moshtari_class::getKey($kelid)))
			die("<script>window.location = 'login.php?stat=wrong_pass&';</script>");
		$is_modir  = FALSE;
		mysql_class::ex_sql("select * from user where user = '".$user."'",$q);
		
		if($r_u = mysql_fetch_array($q,MYSQL_ASSOC)){
			if($pass == $r_u["pass"] && (int)$r_u['ajans_id'] == -1){
				$is_modir =(($r_u["typ"]==0)?0:1);
				$_SESSION["user_id"] = (int)$r_u["id"];
				$_SESSION["daftar_id"] = (int)$r_u["daftar_id"];
				$_SESSION["typ"] = (int)$is_modir;
				$user1_id = $_SESSION["user_id"];
				$user1 = new user_class((int)$user1_id);	
				if(method_exists($user1,'sabt_vorood'))
					$user1->sabt_vorood();
			}else{
				die("<script>window.location = 'login.php?stat=wrong_pass&';</script>");
			}
		}else{
			die("<script>window.location = 'login.php?stat=wrong_user&';</script>");
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
	$tmp1 = array('main_param'=>'پرونده','dafater'=>'مدیریت دفاتر','agency'=>$ajans_title,'user_manage'=>'مدیریت کاربران','hozoor_ghiab'=>'حضور و غیاب','m_hotel'=>'مدیریت هتل','hotel_daftar'=>'دسترسی هتل','paziresh_info'=>'اطلاعات پذیرش','send_file'=>'ارسال فایل','exit'=>'خروج','accu'=>'حسابداری','h_kol'=>'حساب کل','h_moeen'=>'حساب معین','sanad_new'=>'ثبت سند','sanad_daftar'=>'ثبت دریافتی/پرداختی','_belit'=>'ثبت سند بلیت','msg'=>'پیامها '.(($unRead==0)?'':"<span style='color:red;'>($unRead)</span>"),'send_msg'=>'ارسال پیام','load_msg'=>'مشاهده پیامها','anbar_dari'=>'انبارداری','m_anbar'=>'مدیریت انبار','kala_no'=>'نوع‌کالا','kala'=>'کالا','vorood_anbar'=>'ورود به انبار','khorooj_anbar'=>'خروج از انبار','bazgasht_anbar'=>'بازگشت به انبار','kala_vahed'=>'تعریف واحد','cost'=>'کاست','sabt_kala'=>'کالای ترکیبی','sabt_jozeeat_kala'=>'جزئیات کالای ترکیبی','reports'=>'گزارش‌ها','sanad_gozaresh'=>'گزارش اسناد ','gozareshmande'=>'گزارش مانده','tarikh_gozaresh'=>'گزارش تاریخ اسناد','gozaresh_eshghal'=>'گزارش درصد اشغال','gozaresh_sms'=>'گزارش پیامک‌ها','gozaresh_ajans'=>'گزارش آژانس ها','backup'=>'پشتیبان','back'=>'پشتیبان گیری','restor'=>'بروز رسانی پشتیبان','ticket'=>'ارتباط با پشتیبانی','onUser'=>'‌کاربران','listUser'=>'لیست کاربران آنلاین','listInOut'=>'لیست ورود و خروج کاربران','paziresh_main'=>'پذیرش','paziresh'=>'پذیرش','room_status'=>'وضعیت اتاق‌ها','sanad_belit'=>'ثبت سند بلیت','enteghad_modir'=>'انتقاد و پیشنهاد به مدیر','hesab_close'=>'بستن سال مالی');
	if($conf->sms)
		$tmp1['sms_groohi']='ارسال پیامک هوشمند';
	if($conf->room_vaziat)
	{
		$tmp1['room_vaziat_3']='نظافت اتاق';
		$tmp1['room_vaziat_4']='تعمیر اتاق';
		$tmp1['room_vaziat_5']='خروج از سرویس اتاق';
		$tmp1['room_vaziat_2']='اتاق نظافت شده';
	}
//	$tmp2=array('main_param'=>array('dafater'=>null,'agency'=>null,'user_manage'=>null,'hozoor_ghiab'=>null,'m_hotel'=>null,'hotel_daftar'=>null,'paziresh_info'=>null,'send_file'=>null,'ticket'=>null,'enteghad_modir'=>null),'accu'=>array('h_kol'=>null,'h_moeen'=>null,'sanad_new'=>null,'sanad_daftar'=>null,'sanad_belit'=>null,'hesab_close'=>null),'msg'=>array('send_msg'=>null,'load_msg'=>null),'anbar_dari'=>array('m_anbar'=>null,'kala_no'=>null,'kala'=>null,'vorood_anbar'=>null,'khorooj_anbar'=>null,'bazgasht_anbar'=>null,'kala_vahed'=>null),'cost'=>array('sabt_kala'=>null,'sabt_jozeeat_kala'=>null),'reports'=>array('sanad_gozaresh'=>null,'gozareshmande'=>null,'tarikh_gozaresh'=>null,'gozaresh_eshghal'=>null,'gozaresh_sms'=>null),'backup'=>array('back'=>null,'restor'=>null),'onUser'=>array('listUser'=>null,'listInOut'=>null),'paziresh_main'=>array('paziresh'=>null,'room_status'=>null)  );
        $tmp2=array('main_param'=>array('dafater'=>null,'agency'=>null,'user_manage'=>null,'hozoor_ghiab'=>null,'m_hotel'=>null,'hotel_daftar'=>null,'paziresh_info'=>null,'send_file'=>null,'ticket'=>null,'enteghad_modir'=>null),'accu'=>array('h_kol'=>null,'h_moeen'=>null,'sanad_new'=>null,'sanad_daftar'=>null,'sanad_belit'=>null,'hesab_close'=>null),'msg'=>array('send_msg'=>null,'load_msg'=>null));
        if($conf->anbar)
        {
                $tmp2['anbar_dari'] = array('m_anbar'=>null,'kala_no'=>null,'kala'=>null,'vorood_anbar'=>null,'khorooj_anbar'=>null,'bazgasht_anbar'=>null,'kala_vahed'=>null);
                $tmp2['cost'] = array('sabt_kala'=>null,'sabt_jozeeat_kala'=>null);
        }
        $tmp2['reports'] = array('sanad_gozaresh'=>null,'gozareshmande'=>null,'tarikh_gozaresh'=>null,'gozaresh_eshghal'=>null,'gozaresh_sms'=>null);
        $tmp2['backup'] = array('back'=>null,'restor'=>null);
        $tmp2['onUser'] = array('listUser'=>null,'listInOut'=>null);
        $tmp2['paziresh_main'] = array('paziresh'=>null,'room_status'=>null);
	if($conf->sms)
		$tmp2['main_param']['sms_groohi'] = null;
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
		$tmp1['sandogh_user'] = 'دسترسی فرانت';
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
	$tmp2['main_param']['exit'] = null;
	$user_sandoghs = user_class::loadSondogh($user_id,($se->detailAuth('all') || $se->detailAuth('frontOffice')));
	//var_dump($user_sandoghs);
	$sandogh_icons = '';
	$sandogh_scripts = '';
	for($i = 0;$i < count($user_sandoghs);$i++)
	{
		$tmp_sandogh = new sandogh_class((int)$user_sandoghs[$i]);
		if($tmp_sandogh->icon != '')
		{
			$sandogh_icons .= "
                                <td id=\"sandogh_".(int)$user_sandoghs[$i]."\" align=\"center\" >
                                        <table>
                                                <tr>
                                                        <td align=\"center\">
                                                                <img src=\"".$tmp_sandogh->icon."\" width=\"75\" ></img>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th>
                                                                ".$tmp_sandogh->name."
                                                        </th>
                                                </tr>
                                        </table>

                                </td>";
			if($i % 2 == 0 && $i < count($user_sandoghs) && $i > 0)
				$sandogh_icons .= "</tr><tr>";
			$sandogh_scripts .= "
$(\"#sandogh_".(int)$user_sandoghs[$i]."\").click(function () {
$.window({
        title: \"فرانت آفیس\",
        width: 800,
        height: 500,
        content: $(\"#window_block8\"),
        containerClass: \"my_container\",
        headerClass: \"my_header\",
        frameClass: \"my_frame\",
        footerClass: \"my_footer\",
        selectedHeaderClass: \"my_selected_header\",
        createRandomOffset: {x:0, y:0},
        showFooter: false,
        showRoundCorner: true,
        x: 0,
        y: 0,
        url: \"sandogh_det.php?sandogh_id=".(int)$user_sandoghs[$i]."&\"
});
});";
		}
	}
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
			&nbsp;
		</td>
		<td rowspan="2" align="left" style="vertical-align:top;">
	&nbsp;
		</td>
	</tr>
	<tr>
		<td width="65%" style="vertical-align:top;">
        <h2 align="left" style="color:#FFF;" ><?php echo $conf->title; ?></h2>
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
<!--			<ul id="nav">
			<li id="main_param" ><a href="#"> مدیریت پارامترهای پایه </a>
				<ul>
					<li id="dafater" ><a href="#">مدیریت دفاتر</a></li>
					<li id="agency" ><a href="#">مدیریت‌آژانس‌ها </a></li>
					<li id="user_manage" ><a href="#">مدیریت کاربران</a></li>
					<li id="m_hotel" ><a href="#">مدیریت هتل</a></li>
					<li id="send_file" ><a href="#">ارسال فایل</a></li>
					<li id="exit" ><a href="#">خروج</a></li>
				</ul>
			</li>
			<li id="accu" ><a href="#">حسابداری</a>
				<ul>
					<li id="h_kol" ><a href="#">حساب کل </a></li>
					<li id="h_moeen" ><a href="#">حساب معین</a></li>
					<li id="sanad_new" ><a href="#">ثبت سند</a></li>
				</ul>
			</li>
			<li id="msg" ><a href="#">پیامها</a>
				<ul>
					<li id="send_msg" ><a href="#">ارسال پیام</a></li>
					<li id="load_msg" ><a href="#">‌مشاهده پیامها</a></li>
				</ul>
			</li>
			<li id="reports" ><a href="#">گزارش‌ها</a>
				<ul>
					<li id="sanad_gozaresh" ><a href="#">گزارش اسناد </a></li>
					<li id="gozareshmande" ><a href="#">گزارش مانده</a></li>
					<li id="tarikh_gozaresh" ><a href="#">گزارش تاریخ اسناد</a></li>
				</ul>
			</li>
			<li id="backup" ><a href="#">بروز رسانی و پشتیبان گیری</a>
				<ul>
					<li  id="back" ><a href="#">پشتیبان گیری </a></li>
					<li id="restor" ><a href="#">بروز رسانی پشتیبان</a></li>
				</ul>
			</li>
			</ul>
-->
		</div>	
            <div id="sidebar">
                <div align="center">
	                <table width="100%" border="0">
	                	<tr>
	                		<td id="user_manage1" align="center"  >
	                			<table>
		                			<tr>
		                				<td align="center">
		                					<img src="../img/user-icon.png" width="64" ></img>	
		                				</td>
		                			</tr>
		                			<tr>
		                				<th>
										<?php  echo lang_fa_class::user; ?>
								</th>
							</tr>
						</table>          		
	                		</td>
					<?php
					if($se->detailAuth('m_hotel') || $se->detailAuth('all'))
					{
					?>
					<td id="m_hotel1" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/hotel.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                	<?php  echo lang_fa_class::hotel; ?>
                                                                </th>
                                                        </tr>
                                                </table>

                                        </td>
					<?php
					}
					?>
                                        <td id="sanad_gozaresh1" align="center"  >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/total_report.png" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                     گزارش اسناد 
                                                                </th>
                                                        </tr>
                                                </table>
                                        </td>
				</tr>
				<tr>
				<?php
				if($conf->enableTafzili)
				{
				?>
                                        <td id="h_tafzili1" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/tafzili1.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::tafzili1; ?>
                                                                </th>
                                                        </tr>
                                                </table>
                                        </td>
				</tr>
				<tr>
					
				<?php
				}
				if($se->detailAuth('all') || $se->detailAuth('sanad_daftar'))
                                {
				?>
					<td id="sanad_daftar1" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/sabt.png" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                     ثبت<br> دریافتی/پرداختی
                                                                </th>
                                                        </tr>
                                                </table>
	
					</td>
				<?php
				}
				if($conf->vorood)
				{
				?>
					<td id="vorood" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/vorood.png" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                     ثبت ورود/خروج
                                                                </th>
                                                        </tr>
                                                </table>
	
					</td>
				<?php
					}
				?>
					 <td id="exit1" align="center">
                                        <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/Log-Out-icon.png" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                        <?php  echo lang_fa_class::logout; ?>
                                                                                </th>
                                                                        </tr>
                                        </table>
                                        </td>
				</tr>
				<tr>
					<?php echo $sandogh_icons; ?>
				</tr>
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
<script>
showClock('');
$(document).ready(function(){

    $("#dafater").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::grp_user; ?>",
                width: 900,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "daftar.php"
        });
    });
//___---------------------------------
<?php echo $sandogh_scripts; ?>
//___---------------------------------

    $("#moshtari").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "مدیریت مشتریان",
                width: 900,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "show_key.php"
        });
    });
    $("#user_manage").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت کاربران",
		showModal: true,
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "user.php"
	});
    });
    $("#user_manage1").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت کاربران",
		showModal: true,
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "user.php"
	});
    });
    $("#sandogh_factors").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "مشاهده عملیات فرانت آفیس",
                showModal: true,
                width: 1000,
                height: 650,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sandogh_factors.php"
        });
    });
    $("#ticket").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "انتقادها و پیشنهادها",
                showModal: true,
                width: 1000,
                height: 650,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "ticket.php"
        });
    });
    $("#vorood").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "ثبت ورود / خروج",
                showModal: true,
                width: 1000,
                height: 650,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "vorood.php"
        });
    });

    $("#sanad_new").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::filter; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sanad_new.php"
        });
    });
	$("#agency").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "<?php  echo (($conf->is_hesabdari==='')?lang_fa_class::ajans:'مدیریت مشتریان'); ?>",
		width: 1000,
         	height: 500,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "ajans.php"
	});
    });
	$("#m_hotel").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::hotel; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "hotel.php"
        });
    });
	$("#m_hotel1").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::hotel; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "hotel.php"
        });
    });
	$("#send_file").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "ارسال فایل",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "total_upload.php"
        });
    });
	$("#reserve_back").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "بازگشت رزرو",
                width: 500,
                height: 100,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "reserve_back.php"
        });
    });
	$("#hotel_daftar").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "بازگشت رزرو",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "hotel_daftar.php"
        });
    });
	$("#h_kol").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::kol; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "kol.php"
        });
    });
	$("#h_moeen").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::moeen; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "moeen.php"
        });
    });
	$("#send_msg").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::moeen; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "send_msg.php"
        });
    });
	$("#load_msg").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::moeen; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "load_msg.php"
        });
    });
    $("#tarikh_gozaresh").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "گزارش تاریخ اسناد",
                width: 1000,
                height: 650,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sanad_tarikh_rep.php"
        });
    });
    $("#download").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "گزارش",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "gozaresh.php"
	});
    });
    $("#sanad_gozaresh").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "گزارش اسناد",
		width: 900,
         	height: 400,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "sanad_gozaresh.php"
	});
    });
	$("#sanad_gozaresh1").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "گزارش اسناد",
		width: 900,
         	height: 400,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "sanad_gozaresh.php"
	});
    });
	$("#m_anbar").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: " مدیریت انبار",
                width: 900,
                height: 400,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "anbar.php"
        });
    });

	$("#kala_no").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: " نوع کالا",
		width: 900,
         	height: 400,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "kala_no.php"
	});
    });
	$("#kala").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: " کالا",
		width: 900,
         	height: 400,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "kala.php"
	});
    });
	$("#anbar_det").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: " ورود/خروج کالا",
		width: 900,
         	height: 400,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "#"
	});
    });
	$("#vorood_anbar").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: " ورود کالا",
		width: 900,
         	height: 400,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "factor_kala.php?anbar_typ_id=1&"
	});
    });
	$("#sabt_kala").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: " ورود کالای ترکیبی",
                width: 900,
                height: 400,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sabt_kala.php"
        });
    });
	$("#sabt_jozeeat_kala").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: " ورود جزئیات کالای ترکیبی",
                width: 900,
                height: 400,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sabt_jozeeat_kala.php"
        });
    });

	$("#khorooj_anbar").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: " خروج کالا",
		width: 900,
         	height: 400,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "factor_kala.php?anbar_typ_id=2&"
	});
    });
	$("#bazgasht_anbar").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: " بازگشت کالا",
		width: 900,
         	height: 400,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "factor_kala.php?anbar_typ_id=3&"
	});
    });
	$("#kala_vahed").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: " تعریف واحد کالا",
		width: 400,
         	height: 250,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "kala_vahed.php"
	});
    });
    $("#gozareshmande").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "گزارش مانده",
                width: 900,
                height: 400,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sanad_mande.php"
        });
    });
	$("#gozaresh_eshghal").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "گزارش درصد اشغال",
                width: 900,
                height: 400,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "gozaresh_tedad_mehman.php"
        });
    });
	$("#gozaresh_sms").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "گزارش پیامک‌ها",
                width: 900,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "gozaresh_sms.php"
        });
    });
    $("#sanad_daftar").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "ثبت دریافتی / پرداختی",
		width: 900,
         	height: 600,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "sanad_new_daftar.php"
	});
    });
    $("#sanad_daftar1").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "ثبت دریافتی / پرداختی",
		width: 900,
         	height: 600,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "sanad_new_daftar.php"
	});
    });
    $("#restore").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "بروزرسانی نسخه پشتیبان",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "restore.php"
        });
    });
	$("#listUser").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "لیست کاربران آنلاین",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "onUser.php"
        });
    });
$("#listInOut").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "لیست ورود و خروج کاربران با نشانه IP",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "listInOut.php"
        });
    });

	$("#hozoor_ghiab").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "حضور و غیاب",
                width: 700,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "hozoor.php"
        });
    });
	$("#sanad_belit").click(function () {
        $.window({
                title: "ثبت سند بلیت ",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "belit.php"
        });
    });
	$("#sms_groohi").click(function () {
        $.window({
                title: "ارسال پیامک هوشمند",
                width: 800,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sms_groohi.php"
        });
    });
	$("#back").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "عملیات پشتیبان گیری ",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "backup.php"
        });
    });
	$("#restor").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "عملیات پشتیبان گیری ",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "restor.php"
        });
    });
	$("#paziresh_info").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "اطلاعات پذیرش ",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "statics.php"
        });
    });
	$("#paziresh").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "پذیرش میهمان",
                width: 800,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "paziresh_search.php"
        });
    });
	$("#room_status").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: " وضعیت روزانه هتل",
                width: '100%',
                height: 650,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "rooms_vaziat.php"
        });
    });
	$("#room_vaziat_3").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "تغییر وضعیت اتاق",
                width: 500,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "room_change_vaziat.php?vaziat=3"
        });
    });
	$("#room_vaziat_4").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "تغییر وضعیت اتاق",
                width: 500,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "room_change_vaziat.php?vaziat=4"
        });
    });
	$("#room_vaziat_5").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "تغییر وضعیت اتاق",
                width: 500,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "room_change_vaziat.php?vaziat=5"
        });
    });
	$("#enteghad_modir").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "تغییر وضعیت اتاق",
                width: 850,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "enteghad_modir.php"
        });
    });
	$("#room_vaziat_2").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "تغییر وضعیت اتاق",
                width: 500,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "room_change_vaziat.php?vaziat=2"
        });
    });
	$("#hesab_close").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "بستن سال مالی",
                width: 500,
                height: 370,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sanad_close_year.php"
        });
    });
	$("#sandogh").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "موارد فرانت آفیس",
                width: 600,
                height: 450,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sandogh_item.php"
        });
    });
	$("#sandogh_user").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "دسترسی فرانت آفیس",
                width: 600,
                height: 450,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sandogh_user.php"
        });
    });
	$("#sandogh_det").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: " فرانت آفیس",
                width: 800,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sandogh_det.php"
        });
    });
	$("#sandogh_def").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: " فرانت آفیس",
                width: 800,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "sandogh.php"
        });
    });
	$("#factor").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "صدور فاکتور",
                width: 800,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "new_factor.php"
        });
    });
	$("#factor_khadamat").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "تعریف خدمات فاکتور",
                width: 800,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "factor_khadamat.php"
        });
    });
	$("#gozaresh_factor_khadamat").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "گزارش فاکتور",
                width: 800,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "gozaresh_factor_khadamat.php"
        });
    });
    $("#changepass").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "تغییر رمز عبور",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "changepass.php"
        });
    });
    $("#exit").click(function () {
    		if(confirm("آیا مایل به خروج هستید؟")){window.location = "login.php?stat=exit&";}
    });
    $("#exit1").click(function () {
    		if(confirm("آیا مایل به خروج هستید؟")){window.location = "login.php?stat=exit&";}
    });

  });
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
