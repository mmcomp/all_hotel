<?php
	session_start();
	include_once ("../kernel.php");
	$pass=((isset($_REQUEST['pass']))?$_REQUEST['pass']:"");
	$user=((isset($_REQUEST['user']))?$_REQUEST['user']:"");
	date_default_timezone_set("Asia/Tehran");
	$firstVisit = (isset($_SESSION["login"]) && ($_SESSION["login"] == 1) && isset($_REQUEST["user"]));
	if($firstVisit ||(isset($_SESSION["user_id"]))){	
	$now_tarikh1 = Date ("Y-m-d 00:00:00");
	$now_tarikh2 = Date ("Y-m-d 23:59:59");
	if($conf->sms)
	{
		mysql_class::ex_sql("select * from `room_det` where `tatarikh` >= '$now_tarikh1' and `tatarikh` <= '$now_tarikh2'",$q);
		while ($r = mysql_fetch_array($q))
		{
			$reserve_id = $r["reserve_id"];
			mysql_class::ex_sql("select * from `hotel_reserve` where `reserve_id` = '$reserve_id'",$qu);
			if ($row = mysql_fetch_array($qu))
			{
				$shomare = $row["tozih"];
			}
			$send_sms = sms_class::khoruj_text_sms($reserve_id,$shomare);
		}
		mysql_class::ex_sql("select * from `room_det` where `aztarikh` >= '$now_tarikh1' and `aztarikh` <= '$now_tarikh2'",$q);
	        while ($r = mysql_fetch_array($q))
        	{
                	$reserve_id = $r["reserve_id"];
	                mysql_class::ex_sql("select * from `hotel_reserve` where `reserve_id` = '$reserve_id'",$qu);
        	        if ($row = mysql_fetch_array($qu))
                	{
                        	$shomare = $row["tozih"];
	                }
        	        $send_sms = sms_class::vorud_text_sms($reserve_id,$shomare);
	        }
		sms_class::recive_Ajanssms();
		sms_class::recive_Peoplesms();
	}
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
		//echo "+++++++first+++++++";
		$is_modir  = FALSE;
		mysql_class::ex_sql("select * from user where user = '".$user."'",$q);
		
		if($r_u = mysql_fetch_array($q,MYSQL_ASSOC)){
			if($pass == $r_u["pass"] ){
				$is_modir =(($r_u["typ"]==0)?0:1);
				$_SESSION["user_id"] = (int)$r_u["id"];
				$_SESSION["daftar_id"] = (int)$r_u["daftar_id"];
				$_SESSION["typ"] = (int)$is_modir;
			}else{
				die("<script>window.location = 'login.php?stat=wrong_pass&';</script>");
			}
		}else{
			die("<script>window.location = 'login.php?stat=wrong_user&';</script>");
		}
	}
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
	
		
	$tmp1 = array('main_param'=>'اطلاعات پایه','dafater'=>'مدیریت دفاتر','agency'=>'مدیریت‌آژانس‌ها','user_manage'=>'مدیریت کاربران','hozoor_ghiab'=>'حضور و غیاب','m_hotel'=>'مدیریت هتل','hotel_daftar'=>'دسترسی هتل','send_file'=>'ارسال فایل','exit'=>'خروج','accu'=>'حسابداری','h_kol'=>'حساب کل','h_moeen'=>'حساب معین','sanad_new'=>'ثبت سند','sanad_daftar'=>'ثبت دریافتی/پرداختی','msg'=>'پیامها '.(($unRead==0)?'':"<span style='color:red;'>($unRead)</span>"),'send_msg'=>'ارسال پیام','load_msg'=>'مشاهده پیامها','anbar_dari'=>'انبارداری','m_anbar'=>'مدیریت انبار','kala_no'=>'نوع‌کالا','kala'=>'کالا','vorood_anbar'=>'ورود به انبار','khorooj_anbar'=>'خروج از انبار','bazgasht_anbar'=>'بازگشت به انبار','kala_vahed'=>'تعریف واحد','cost'=>'قیمت تمام‌شده','sabt_kala'=>'کالای ترکیبی','sabt_jozeeat_kala'=>'جزئیات کالای ترکیبی','reports'=>'گزارش‌ها','sanad_gozaresh'=>'گزارش اسناد ','gozareshmande'=>'گزارش مانده','tarikh_gozaresh'=>'گزارش تاریخ اسناد','gozaresh_eshghal'=>'گزارش درصد اشغال','gozaresh_sms'=>'گزارش پیامک‌ها','backup'=>'پشتیبان','back'=>'پشتیبان گیری','restor'=>'بروز رسانی پشتیبان','ticket'=>'انتقاد و پیشنهاد');
	$tmp2=array('main_param'=>array('dafater'=>null,'agency'=>null,'user_manage'=>null,'hozoor_ghiab'=>null,'m_hotel'=>null,'hotel_daftar'=>null,'send_file'=>null,'ticket'=>null,'exit'=>null),'accu'=>array('h_kol'=>null,'h_moeen'=>null,'sanad_new'=>null,'sanad_daftar'=>null),'msg'=>array('send_msg'=>null,'load_msg'=>null),'anbar_dari'=>array('m_anbar'=>null,'kala_no'=>null,'kala'=>null,'vorood_anbar'=>null,'khorooj_anbar'=>null,'bazgasht_anbar'=>null,'kala_vahed'=>null),'cost'=>array('sabt_kala'=>null,'sabt_jozeeat_kala'=>null),'reports'=>array('sanad_gozaresh'=>null,'gozareshmande'=>null,'tarikh_gozaresh'=>null,'gozaresh_eshghal'=>null,'gozaresh_sms'=>null),'backup'=>array('back'=>null,'restor'=>null)  );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo lang_fa_class::title; ?></title>
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
</head>
<body>
	<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
    <div id="header">
	<table width="100%" border="0">
	<tr>
		<td>
			&nbsp;
		</td>
		<td rowspan="2" align="left" style="vertical-align:top;">
	<img src="../img/salno.jpg" width="95px"/>
		</td>
	</tr>
	<tr>
		<td width="65%" style="vertical-align:top;">
        <h2 align="left" style="color:#FFF;" ><?php echo lang_fa_class::title; ?></h2>
		</td>
	</tr>
	</table>
    </div><!-- header -->
    	
    <div id="main"><div id="main2">
	<div class="body">
	<?php 
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
	                		<td  align="center"  >
					 
					 </td>
	                		<td id="progress"  align="center" style="display:none;" >
                                       		 <img src='../img/progress.gif' width='64' ></img>
                                        </td> 
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
		title: "<?php  echo lang_fa_class::ajans; ?>",
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
