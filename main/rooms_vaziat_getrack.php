<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	include_once("../simplejson.php");
	$log_user_id = $_SESSION["user_id"];
	mysql_class::ex_sql("select count(`id`) as `t_payam` from `payam` where `rec_user_id`='$log_user_id' and `en`='-1'",$q_payam);
	if ($r_payam = mysql_fetch_array($q_payam))
		$showPayam = $r_payam['t_payam'];
	else
		$showPayam = 0;
	function loadHotel()
        {
				$tmp_hotel_id = array();
				mysql_class::ex_sql("select `hotel_id` from `hotel_daftar` where `daftar_id`=".$_SESSION['daftar_id'],$q);			
				while($r = mysql_fetch_array($q))
					$tmp_hotel_id[]= $r['hotel_id'];
				$out = 'عدم دسترسی کاربر به هتل';
				if(count($tmp_hotel_id))
				{
					$out=null;
					$tmp_hotel_ids = implode(',',$tmp_hotel_id);
					mysql_class::ex_sql("select `id`,`name` from hotel  where `id` in ($tmp_hotel_ids) order by name",$q);
					while($r=mysql_fetch_array($q,MYSQL_ASSOC))
							$out[$r['name']]=(int)$r['id'];
				}
                return $out;
        }
	function loadRoom()
        {
                $out = null;
                mysql_class::ex_sql("select * from room_typ order by zarfiat",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out[$r['name']]=(int)$r['id'];
                return $out;
        }
	function loadPic($id)
	{
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('loadPic.php?room_id=$id&','',500,600);\">ادامه</span></u>";
		return($out);
	}
	function room_status($stat)
	{
		$out[0] = 'اشغال شده';
		$out[1] = 'خالی اما نظافت نشده';
		$out[2] = 'خالی و نظافت شده';
		$out[3] = 'درحال نظافت';
		$out[4] = 'پشتیبان';
		$out[5] = 'در حال تعمیر';
		return($out[$stat]);
	}
	function room_status_icon($stat)
	{
		$out = "<img height=\"30px\" src = \"../img/$stat.png\" title=\"".room_status($stat)."\" alt=\"".room_status($stat)."\"/>";
		return($out);
	}
	$shart_1 = ' where 1=0 ';
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart = '';
	if($hotel_acc!=null)
	{
		if (count($hotel_acc)==1)
			$_REQUEST["hotel_id_new"] = $hotel_acc[0];
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
		$shart_1 = "where `id` in ".$shart;
	}
////////////////////
	if (isset($_REQUEST["hotel_id_new"]))
                $hotel_id_new = $_REQUEST["hotel_id_new"];
	else
		$hotel_id_new = -1;
	$global_prob = FALSE;
	mysql_class::ex_sql("select count(id) as cid from tasisat_tmp where room_id < 0 and en=-1",$qall);
	if($r = mysql_fetch_array($qall))
		$global_prob = ((int)$r['cid']>0);
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"get\">";
		$combo_hotel .= "هتل : <select class='inp' id=\"hotel_id\" name=\"hotel_id_new\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel`$shart_1 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$hotel_id_new)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_hotel .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
		        $combo_hotel .= $r["name"]."\n";
		        $combo_hotel .= "</option>\n";
		}
		$combo_hotel .= "</select>";
	$combo_hotel .= "</form>";
	$hotel_id = $hotel_id_new;
	$links = "";
	$links .="<table style='background-color:#ffffff;-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;width:90%;'>";
			$links .="<tr>";
				$links .="<td ><a target='_blank' href='hotel_gozaresh.php?h_id=$hotel_id&'>گزارش خدمات</a></td>";
				$links .="<td ><a target='_blank' href='search_name.php?hotel_id=$hotel_id&'>جستجوی پیشرفته</a></td>";
				$links .="<td ><a target='_blank' href='gaant.php?hotel_id=$hotel_id&'>شیت هتل</a> </td>";		
				$links .="<td ><span onclick=\"wopen('change_paziresh.php?h_id=$hotel_id&','',800,500);\"  style='text-decoration:underline;cursor:pointer;' >جابجایی </span></td>";		
				$links .="<td ><a href='mehman.php?h_id=$hotel_id&' target='_blank' style='text-decoration:underline;cursor:pointer;' >لیست مهمانان مقیم</a></td>";	
				$links .="<td ><a href='mehman_grooh.php?h_id=$hotel_id&' target='_blank' style='text-decoration:underline;cursor:pointer;' >لیست مهمانان گروه</a></td>";
				$links .="<td ><a href='mehman_all.php?h_id=$hotel_id&' target='_blank' style='text-decoration:underline;cursor:pointer;' >لیست  کلیه مهمانان </a></td>";
$links .="<td ><a href='reportExitHours.php?h_id=$hotel_id&' target='_blank' style='text-decoration:underline;cursor:pointer;' >گزارشات </a></td>";
				$links .="<td ><a href='list_inOut.php?h_id=$hotel_id&' target='_blank' style='text-decoration:underline;cursor:pointer;' >لیست ورود و خروج میهمان </a></td>";
			$links .="</tr>";
			$links .="</tr>";
		$links .="</table>";
	$tarikh = (isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):date("Y-m-d");
	$room_typ = (isset($_REQUEST['room_typ']))?$_REQUEST['room_typ']:-1;
	$tarikh = explode(' ',$tarikh);
	$tarikh = $tarikh[0];
	
	$sday = date("Y-m-d 00:00:00");
	$eday = date("Y-m-d 23:59:59");
	//$eday = date("Y-m-d H:i:s");
	$day = Date("Y-m-d 14:00:00 ");
	$today_khoruj = 0; 
	$count_mehman = 0;
	$count_room_khali = 0;
	$tedad_mehman = 0;
	$full_room = 0;
	$full_room1 = 0;
	$free_room = 0;
	$dirty_room = 0;
	$out_serviceRoom = 0;
	$tedad_mehman_moghim = 0;
	$poshtiban = 0;
	$tmp_full =0;
	$y = Date("Y");
	$m = Date("m");
	$d = Date("d");	
	$day1 =mktime("14","00","00",$m,$d,$y);
	$shart ='';
	$t_vorudi = 0;
	$rooms_id = "(";
	if ($hotel_id_new!=-1)
	{
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$hotel_id' order by `name`",$query2);
		while($res2 = mysql_fetch_array($query2))
		{
			$rooms_id .= $res2["id"].',';
		}
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
		if ($rooms_ids!="")
			$shart = " `room_id` in $rooms_ids ";
		mysql_class::ex_sql("select `nafar`,`reserve_id`,`tatarikh`,`room_id` from `room_det` where $shart",$qr);
		while($rr = mysql_fetch_array($qr))
		{
			$tatarikh= $rr["tatarikh"];
			$res = $rr["reserve_id"];
			$room_id = $rr["room_id"];
			$ye = substr($tatarikh,0,4);
			$mo = substr($tatarikh,5,2);
			$da = substr($tatarikh,8,2);
			$tmp_tatarikh =mktime("14","00","00",$mo,$da,$ye);
			if (($tmp_tatarikh == $day1)&&($res>0)&&(!reserve_class::isKhorooj($res,$room_id)))
				$today_khoruj ++;
		}
		mysql_class::ex_sql("select `id`,`vaziat`,`name` from `room` where `en`='1' and `hotel_id`='$hotel_id'",$q);
		while($r = mysql_fetch_array($q))
		{
			$id = $r["id"];
			$rooms = room_det_class::roomIdAvailable($id,$sday,$eday);
			if (!(count($rooms)==0))
			{
				$tedad_mehman .= (($tedad_mehman=='')?'':',').$id;
				$full_room ++;
			}
		}
		$today = date("Y-m-d");
		mysql_class::ex_sql("select count(`id`) as `tedad_v` from `room_det` where date(`aztarikh`)='$today' and `room_id` in $rooms_ids and `reserve_id`>0",$q);
		if($r = mysql_fetch_array($q))
		{
			$t_vorudi = $r['tedad_v'];
		}
	}
	$q=null;
	mysql_class::ex_sql("select `id`,`vaziat`,`name` from `room` where `en`='1' and `hotel_id`='$hotel_id'",$q);
	while($r = mysql_fetch_array($q))
	{
		if ($r["vaziat"] == 0)
			$full_room1 ++;
		if ($r["vaziat"] == 1)
			$dirty_room ++;
		if ($r["vaziat"] == 2)
			$free_room ++;
		if ($r["vaziat"] == 4)
			$out_serviceRoom ++;
		if ($r["vaziat"] == 5)
			$poshtiban ++;
		if ($r["vaziat"] == 3)
			$tmp_full ++;
	}
	$day = date("Y-m-d");
	$i = 1;
	$aztarikh = $day;
	$tatarikh = $day;
	$q=null;
//echo count($tedad_mehman);
	$shart ='';
	$rooms_id = "(";
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$hotel_id' order by `name`",$query2);
		while($res2 = mysql_fetch_array($query2))
		{
			$rooms_id .= $res2["id"].',';
		}
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
		if ($rooms_ids!="")
			$shart = " `room_id` in $rooms_ids ";
		$day = date("Y-m-d");
		$i = 1;
		$aztarikh = $day;
		$tatarikh = $day;
		$q = null;
		mysql_class::ex_sql("select `reserve_id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) <= '$aztarikh' and date(`tatarikh`) >= '$aztarikh') or (date(`aztarikh`) < '$tatarikh' and date(`tatarikh`) > '$tatarikh')) group by `reserve_id`",$q);

		$tmp ='';
		if(isset($_REQUEST['hotel_id_new']))
			$h_id = $_REQUEST['hotel_id_new'];
		else
			$h_id = -1;
		while ($r = mysql_fetch_array($q))
		{
			$r_hotel = room_class::loadHotelByReserve($r['reserve_id']);
			if ($h_id==$r_hotel)
				$tmp .=($tmp==''? '':',' ).$r['reserve_id'];
		}
		if ($tmp!='') 
		{
			mysql_class::ex_sql("select count(`id`) as `tedad` from `mehman` where `reserve_id` in ($tmp) and `khorooj`='0000-00-00 00:00:00'",$q_hotel);
			if ($r_hotel = mysql_fetch_array($q_hotel))
				$tedad_mehman_moghim = $r_hotel["tedad"];
		}
		$combo = "";
		$combo .= "<form name=\"selRoom\" id=\"selRoom\" method=\"GET\">";
		$combo .= "نوع اتاق : <select class='inp' id=\"room_typ\" name=\"room_typ\" onchange=\"document.getElementById('selRoom').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		$combo .= "<option selected='selected' value=\"-1\">\n";
       		$combo .= "همه"."\n";
      		$combo .= "</option>\n";
	$tedad = 0;
	$name_typ = "";
	mysql_class::ex_sql("select `id`,`name`,`room_typ_id` from room where `hotel_id`='$hotel_id' group by `room_typ_id`",$q);
        while($r = mysql_fetch_array($q))
        {
		$typ_room = $r["room_typ_id"];
		$id = $r["id"];
		mysql_class::ex_sql("select `id`,`name` from room_typ where `id`='$typ_room'order by zarfiat",$q_typ);
//echo "select `id`,`name` from room_typ where `id`='$typ_room'order by zarfiat";
       		if($r_typ = mysql_fetch_array($q_typ))
			$name_typ = $r_typ["name"];		
		mysql_class::ex_sql("select count(`id`) as `tedad` from `room` where `room_typ_id`='$typ_room' and `hotel_id`='$hotel_id' group by `room_typ_id`",$qu);
		if($row = mysql_fetch_array($qu))
			$tedad = $row['tedad'];
		if($typ_room== (int)$room_typ)
                        $select = 'selected="selected"';
                else
                        $select = "";
                $combo .= "<option value=\"".(int)$typ_room."\" $select   >\n";
                $combo .= $name_typ.'('.$tedad.')'."\n";
                $combo .= "</option>\n";
        }
        $combo .="</select>";
	$combo .='<input class="inp" type="hidden" name="hotel_id_new" id="hotel_id_new" value="'.$hotel_id_new.'"  >';
	$combo .= "</form>";
	$out = hotel_class::getRack1($hotel_id,$room_typ,$se);
var_dump($out);
exit();
	if (isset($_REQUEST['seName']))
	{
		$re = array();
		$seName = $_REQUEST['seName'];
		$res_se = hotel_reserve_class::loadByName($seName);
		foreach($res_se as $res_id)
			if ($res_id > 0)
				$re[] = room_det_class::loadByReserve_rack($res_id);
		die(toJSON($re));
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="refresh" content="300;url=rooms_vaziat.php">
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
                <link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
                <link type="text/css" href="../css/style.css" rel="stylesheet" />
                <link href="../css/ih_style.css" rel="stylesheet" type="text/css" />
                <script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script> 
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/raphael-min.js"></script>
		<script type="text/javascript" src="js/clock.js"></script>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
			سامانه رزرواسیون هتل	
		</title>
		<script type="text/javascript" >
			$(document).ready(function(){
				setTimeout("locateMsgDiv();",1000);
			});
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
			function searchInRack()
			{
				var seName = $("#searchInRak").val();
				//var h_id = $("#h_id").val();
				var h_id = 1;
				if(seName!='')
				{
					$("#khoon").html("<img src='../img/status_fb.gif'>");
					$.getJSON("rooms_vaziat.php?seName="+seName+"&h_id="+h_id+"&",function(result){
						$("#khoon").html("");
						var room_id=[];
						for (var i=0;i<result.length;i++)
						{
							var cell = result[i];
							if (cell.length>0)
								for (var j=0;j<cell.length;j++)
									//room_id.push(cell[j]);
									$('#'+cell[j]).css({"background-color":"#660066","font-size":"200%"});
						}
					});
				}
			}
			function locateMsgDiv()
			{
				var msgDiv = $("#msgDiv");
				var tedadMsgDiv = $("#tedadMsgDiv");
				var t_payam = <?php echo $showPayam; ?>;
				msgDiv.hide();
				msgDiv.css("position","fixed");
				msgDiv.css("top","0px");
				msgDiv.css("left","10px");
				msgDiv.css("padding-left","20px");
				msgDiv.css("color","#ec0709");
				tedadMsgDiv.html(t_payam+" پیام خوانده نشده");
				msgDiv.slideDown('slow');
				setTimeout("locateMsgDiv();",10000);
			}
		</script>

		<script>
		function st()
		{
		week= new Array("يكشنبه","دوشنبه","سه شنبه","چهارشنبه","پنج شنبه","جمعه","شنبه")
		months = new Array("فروردين","ارديبهشت","خرداد","تير","مرداد","شهريور","مهر","آبان","آذر","دي","بهمن","اسفند");
		a = new Date();
		d= a.getDay();
		day= a.getDate();
		var h=a.getHours();
      		var m=a.getMinutes();
  		var s=a.getSeconds();
		month = a.getMonth()+1;
		year= a.getYear();
		year = (year== 0)?2000:year;
		(year<1000)? (year += 1900):true;
		year -= ( (month < 3) || ((month == 3) && (day < 21)) )? 622:621;
		switch (month) 
		{
			case 1: (day<21)? (month=10, day+=10):(month=11, day-=20); break;
			case 2: (day<20)? (month=11, day+=11):(month=12, day-=19); break;
			case 3: (day<21)? (month=12, day+=9):(month=1, day-=20); break;
			case 4: (day<21)? (month=1, day+=11):(month=2, day-=20); break;
			case 5:
			case 6: (day<22)? (month-=3, day+=10):(month-=2, day-=21); break;
			case 7:
			case 8:
			case 9: (day<23)? (month-=3, day+=9):(month-=2, day-=22); break;
			case 10:(day<23)? (month=7, day+=8):(month=8, day-=22); break;
			case 11:
			case 12:(day<22)? (month-=3, day+=9):(month-=2, day-=21); break;
			default: break;
		}
		//document.write(" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s);
			var total=" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s;
			    document.getElementById("tim").innerHTML=total;
   			    setTimeout('st()',500);
		}
		</script>
	</head>
	<body onload='st()'>
		<br/>
		
		<br/>
		<br/>
		<center>
		<span id='tim' >test2
		</span>
		</center>
		<div id="msgDiv" style="padding-right:4%;">
			<div style="color:#ffffff;width:100%;text-align:center;">
				<a href='payam.php' target='_blank'><img src='../img/inbox.png'/></a>
				<br/>
				<div id="tedadMsgDiv"></div>
			</div>
		</div>
		<div style="float:right;padding-right:30px;padding-top:10px;">
			<?php
				//var_dump($se->allDetails);
			?>
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
			<?php
				if($global_prob)
					echo '<a href="tasisat_tmp.php?omoomi=1&" target="_blank"><img width="64px" src="../img/alarm.gif" title="مشکل در فضای عمومی"/></a>';
				if($se->detailAuth('tasisat'))
					echo '<a href="tasisat_checklist_det.php" target="_blank"><img width="64px" src="../img/check.png" title="چک لیست روزانه"/></a>';
			?>
			<a href="tasisat_tmp.php" target="_blank" title="ثبت مشکلات اتاق"><img src="../img/prob.png" title="ثبت مشکلات اتاق"/></a>
			<?php
				if ($se->detailAuth('hoteldar')) {?>
					<a href="guest_req.php" target="_blank" title="ثبت درخواست میهمان"><img src="../img/guestReq.png" title="ثبت درخواست میهمان"/></a>
			<?php }
				/*if($se->detailAuth('tasisat') || $se->detailAuth('super')) 
				{ ?>
					<a href="tasisat_tmp.php" target="_blank"><img src="../img/hotel_fa.png"/></a>
			<?php 	
				}*/ ?>
			<?php 
				if($se->detailAuth('super')) 
				{ ?>
					<a title="ثبت اقلام جامانده میهمان" href="vasael_jamande.php" target="_blank"><img src="../img/suitcase.png"/></a>					
			<?php 	
				} ?>
			<a title="خروج" href="login.php"><img src="../img/Log-Out-icon.png"/></a>	
			
		</div>
	<!--	<div style="float:left;padding-right:10px;" class='eslahDiv'>
			نام میهمان
			<input type='text' name='searchInRak' id='searchInRak'>
			<input type='button' value='جستجو' class='notice2' onclick='searchInRack();' >
			<div id='khoon'></div>
		</div>-->
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
				<br/>
		<br/>
		<br/>
		<br/>
		<div align="center">
			<br/>
			<?php 
				echo $combo_hotel;
				echo '<br/>';
				echo $combo;
				echo $links;?>
			
			<table>
				<tr>
				<?php
					echo '<td style="background-color:#b5d3ff;-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;"><a target="_blank" href="mehman.php?h_id='.$hotel_id.'&">تعداد میهمانان حاضر در هتل</a></td>';
					echo '<td style="background-color:#b5d3ff;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;"><a target="_blank" href="otagh_vorodi.php?h_id='.$hotel_id.'&">ورودی های امروز</a></td>';		
				?>
				</tr>
				<tr>
					<th style="background-color:#b5d3ff;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $tedad_mehman_moghim;?></th>
					<th style="background-color:#b5d3ff;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php  echo $t_vorudi;?></th>
				</tr>
			</table>
			<br/>
			<table >
				<tr>
				<?php
					echo '<td style="background-color:#b72b13;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;"><a target="_blank" href="otagh_full.php?h_id='.$hotel_id.'&">تعداد اتاق های اشغال</a></td>';
					echo '<td style="background-color:#0c5e06;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;"><a target="_blank" href="otagh_khali.php?h_id='.$hotel_id.'&">تعداد اتاق های خالی</a></td>';
					echo '<td style="background-color:#f1ca00;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;"><a target="_blank" href="otagh_notNezafat.php?h_id='.$hotel_id.'&">تعداد اتاق های نظافت نشده </a></td>';
					echo '<td style="background-color:#a38fb3;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;"><a target="_blank" href="otagh_tamir.php?h_id='.$hotel_id.'&">اتاق های در دست تعمیر</a></td>';
					echo '<td style="background-color:#ff7103;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><a target="_blank" href="otagh_poshtiban.php?h_id='.$hotel_id.'&">پشتیبان</a></td>';
					echo '<td style="background-color:#034da2;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><a target="_blank" href="tmp_full.php?h_id='.$hotel_id.'&">اتاق های اشغال موقت</a></td>';
					echo '<td style="background-color:#ffffff;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;"><a target="_blank" href="otagh_khoruji.php?h_id='.$hotel_id.'&"> اتاق های خروجی امروز</a></td>';
					?>
				</tr>
				<tr>
					<th style="background-color:#b72b13;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $full_room1;?></th>
					<th style="background-color:#0c5e06;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $free_room;?></th>
					<th style="background-color:#f1ca00;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $dirty_room;?></th>
					<th style="background-color:#a38fb3;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $out_serviceRoom;?></th>
					<th style="background-color:#ff7103;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $poshtiban;?></td>
					<th style="background-color:#034da2;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $tmp_full;?></td>
					<th style="background-color:#ffffff;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;"><?php echo $today_khoruj;?></th>
					
				</tr>
			</table>
			<br/>
			<?php
				echo $out;
				echo "<br/>";
			?>
			<br/>
		</div>
		<br/>
		<br/>
	</body>
</html>
