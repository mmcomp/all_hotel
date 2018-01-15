<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = $se->detailAuth('all');
	$out='';
	if (isset($_SESSION['user_id']))
		$user_id = (int)$_SESSION['user_id'];
	else
		$user_id = -1;
	
	function loadNesbat()
	{
		$tmp = statics_class::loadByKey('نسبت');
		$out['سرگروه'] = '-1';
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadMellait()
	{
		$tmp = statics_class::loadByKey('ملیت');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	if(isset($_REQUEST['ch_room_id']))
	{
		$room_id = (int)$_REQUEST['ch_room_id'];
		$vaziat = (int)$_REQUEST['vaziat'];
		$tarikh = ($_REQUEST['tarikh']!='') ?audit_class::hamed_pdateBack($_REQUEST['tarikh']):'0000-00-00 00:00:00';
		$tozih = $_REQUEST['tozih'];
		$date = date("Y-m-d H:i:s");		
		$tarikh_qu = ",`end_fix_date`='$tarikh'";
		mysql_class::ex_sql("select `vaziat` from `room` where `id`='$room_id'",$q);
		if($r = mysql_fetch_array($q))
		{
			if ($r['vaziat']==1)
			{
				mysql_class::ex_sql("select `room_id`,`en`,max(`id`) as `max_id` from `nezafat` where `room_id`='$room_id'",$q_nezafat);
				if($r_nezafat = mysql_fetch_array($q_nezafat))
				{	
					$today = date("Y-m-d H:i:s");
					$room_id = $r_nezafat['room_id'];
					$max_id = $r_nezafat['max_id'];
					mysql_class::ex_sqlx("update `nezafat` set `nezafat_time` = '$today', `user_nezafat` = '$user_id',`en`='1'  where `room_id` = '$room_id' and `en`='0' and `id`='$max_id'");
				}
			}
		}
		if($room_id>0 && $vaziat>=0)
		{
			if (($vaziat<=3) && ($vaziat!=4))
			{
				//echo '1';
				mysql_class::ex_sqlx("update `room` set `vaziat` = $vaziat $tarikh_qu where `id` = $room_id");
				if ($vaziat==1)
					mysql_class::ex_sqlx("insert into `nezafat` (`id`, `room_id`, `reserve_id`, `mani_time`, `nezafat_time`, `user_id`, `user_nezafat`, `en`) VALUES (NULL, '$room_id', '-1', '$date', '0000-00-00 00:00:00', '$user_id', '-1', '0')");
				die('ok');
			}
			elseif (($vaziat>3) && ($vaziat!=4) && ($vaziat!=5))
			{				
//echo '2';
				mysql_class::ex_sqlx("update `room` set `vaziat` = $vaziat $tarikh_qu where `id` = $room_id");
				die('ok');
			}
			elseif (($vaziat==4)&&($tozih!='')&&($tarikh!=='0000-00-00 00:00:00'))
			{
				mysql_class::ex_sqlx("update `room` set `vaziat` = $vaziat $tarikh_qu where `id` = $room_id");
				mysql_class::ex_sqlx("insert into `tasisat` (`room_id`,`user_reg`,`toz`,`regdate`) values ($room_id,".(int)$_SESSION['user_id'].",'$tozih','$date')");
				die('ok');				
			}
			elseif (($vaziat==5)&&($tozih!='')&&($tarikh!=='0000-00-00 00:00:00'))
			{
				mysql_class::ex_sqlx("update `room` set `vaziat` = $vaziat $tarikh_qu where `id` = $room_id");
				mysql_class::ex_sqlx("insert into `poshtiban` (`room_id`,`user_reg`,`toz`,`regdate`) values ($room_id,".(int)$_SESSION['user_id'].",'$tozih','$date')");
				die('ok');				
			}
			else
				die('hhh');			
		}
		else
			die('nok');
	}
	if(isset($_REQUEST['room_id']))
	{		
		$room_id = (int)$_REQUEST['room_id'];		
		$tarikh = date("Y-m-d H:i:s");	
		$r_tmp = new room_class($room_id);
		$vaziat = $r_tmp->vaziat;
		$res = $r_tmp->getAnyReserve($tarikh);
		$reserve_id = $res[0]['reserve_id'];
		$khadamat1 = khadamat_det_class::loadByReserve_habibi($reserve_id);
		$kh_name = '';
		$sharj = '';
		$clean_onclick="";
		$dirty_onclick="";
		$tmpFull_onclick="";
		$full_onclick="";
		///eshghal
		if ($vaziat==0)
		{
			$vazeat_name = " اتاق".' '.$r_tmp->name.' '."در وضعیت اشغال";
			$clean_onclick="sabt_dec();";
			$add_pic_clean = "../img/deactive.png";
			//$dirty_onclick="sabt(1);";
			//$add_pic_dirty = "../img/dirtyRoom.png";
			$dirty_onclick="sabt_dec();";
			$add_pic_dirty = "../img/deactive.png";	
			$tmpFull_onclick="sabt_dec();";
			$add_pic_tmpFull = "../img/deactive.png";
			$full_onclick="sabt(0);";
			$add_pic_full = "../img/fullRoom.png";
		}
		///nezafatNashode
		elseif ($vaziat==1)
		{
			$clean_onclick="sabt(2);";
			$dirty_onclick="sabt(1);";
			$tmpFull_onclick="sabt_dec();";
			$full_onclick="sabt_dec();";
			$vazeat_name = " اتاق".' '.$r_tmp->name.' '."در وضعیت نظافت نشده ";
			$add_pic_clean = "../img/readuRoom.png";
			$add_pic_dirty = "../img/dirtyRoom.png";	
			$add_pic_tmpFull = "../img/deactive.png";
			$add_pic_full = "../img/deactive.png";
		}
		///nezafatShode
		elseif ($vaziat==2)
		{
			$clean_onclick="sabt(2);";
			$dirty_onclick="sabt_dec();";
			$tmpFull_onclick="sabt(3);";
			$full_onclick="sabt(1);";
			$vazeat_name = " اتاق ".' '.$r_tmp->name.' '." در وضعیت آزاد و نظافت شده ";
			$add_pic_clean = "../img/readuRoom.png";
			$add_pic_dirty = "../img/deactive.png";	
			$add_pic_tmpFull = "../img/tempFull.png";
			$add_pic_full = "../img/fullRoom.png";
		}
		///eshghalMovaghat
		elseif ($vaziat==3)
		{
			$clean_onclick="sabt_dec();";
			$dirty_onclick="sabt(1);";
			$tmpFull_onclick="sabt(3);";
			$full_onclick="sabt(0);";
			$vazeat_name = " اتاق".' '.$r_tmp->name.' '."در وضعیت اشغال موقت ";
			$add_pic_clean = "../img/deactive.png";
			$add_pic_dirty = "../img/dirtyRoom.png";	
			$add_pic_tmpFull = "../img/tempFull.png";
			$add_pic_full = "../img/fullRoom.png";
		}
		elseif ($vaziat==4)
		{
			$clean_onclick="sabt_dec();";
			$dirty_onclick="sabt(1);";
			$tmpFull_onclick="sabt_dec();";
			$full_onclick="sabt_dec();";
			$vazeat_name = " اتاق".' '.$r_tmp->name.' '."در وضعیت تعمیرات ";
			$add_pic_clean = "../img/deactive.png";
			$add_pic_dirty = "../img/dirtyRoom.png";	
			$add_pic_tmpFull = "../img/deactive.png";
			$add_pic_full = "../img/deactive.png";
		}
		elseif ($vaziat==5)
		{
			$clean_onclick="sabt_dec();";
			$dirty_onclick="sabt(1);";
			$tmpFull_onclick="sabt_dec();";
			$full_onclick="sabt_dec();";
			$vazeat_name = " اتاق".' '.$r_tmp->name.' '." در وضعیت پشتیبان ";
			$add_pic_clean = "../img/deactive.png";
			$add_pic_dirty = "../img/dirtyRoom.png";	
			$add_pic_tmpFull = "../img/deactive.png";
			$add_pic_full = "../img/deactive.png";
		}
		else
		{
			$clean_onclick="sabt_dec();";
			$dirty_onclick="sabt_dec();";
			$tmpFull_onclick="sabt_dec();";
			$full_onclick="sabt_dec();";
			$vazeat_name = "اتاق".' '.$r_tmp->name.' '."در وضعیت نامعلوم ";
			$add_pic_clean = "../img/deactive.png";
			$add_pic_dirty = "../img/deactive.png";	
			$add_pic_tmpFull = "../img/deactive.png";
			$add_pic_full = "../img/deactive.png";
		}	
		mysql_class::ex_sql("select min(`aztarikh`) as `min_az`,max(`tatarikh`) as `max_ta` from `room_det` where `reserve_id`='$reserve_id' and `room_id`='$room_id'",$q);
		if($r = mysql_fetch_array($q))
		{
			$min_az = $r['min_az'];
			$max_ta = $r['max_ta'];
			if (($min_az)&&($max_ta))
			{
				$az_saat = explode(" ",$min_az);
				$ta_saat = explode(" ",$max_ta);
				$az_saat_tmp = explode(":",$az_saat[1]);
				$ta_saat_tmp = explode(":",$ta_saat[1]);
				if (($az_saat_tmp[0]!='14') && ($ta_saat_tmp[0]!='14'))
					$sharj = "نیم شارژ ورودی".','."نیم شارژ خروجی";
				elseif (($az_saat_tmp[0]=='14') && ($ta_saat_tmp[0]!='14'))
					$sharj = "نیم شارژ خروجی";
				elseif (($az_saat_tmp[0]!='14') && ($ta_saat_tmp[0]=='14'))
					$sharj = "نیم شارژ ورودی";
				else
					$sharj = '';
			}
		}
		for($i=0;$i<count($khadamat1);$i++)
		{
			$kh_v = '';
			$kh_kh = '';
			$kh_id = $khadamat1[$i]['khadamat_id'];
			$name_kh = khadamat_class::loadKhadamat_name($kh_id);
			if ($name_kh!='ترانسفر')
			{	
				if ($khadamat1[$i]['voroodi']=='1')
					$kh_v = 'اول';
				if ($khadamat1[$i]['khorooji']=='1')
					$kh_kh = 'آخر';
			}
			else
			{	
				if ($khadamat1[$i]['voroodi']=='1')
					$kh_v = 'ورودی';
				if ($khadamat1[$i]['khorooji']=='1')
					$kh_kh = 'خروجی';
			}
			if (($kh_v!='')&&($kh_kh!=''))
				$kh_name .= $name_kh.'('.$kh_v.','.$kh_kh.')'.',';
			elseif (($kh_v=='')&&($kh_kh!=''))
				$kh_name .= $name_kh.'('.$kh_kh.')'.',';
			elseif (($kh_v!='')&&($kh_kh==''))
				$kh_name .= $name_kh.'('.$kh_v.')'.',';
			else
				$kh_name .= $name_kh.',';
		}	
		$kh_name .= $name_kh.','.$sharj.',';
		if ($se->detailAuth('modir'))
		{
			$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="95%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;margin:10px;" ><tr class="showgrid_header" ><th>پذیرش</th><th>شماره رزرو</th><th>هتل</th><th>آژانس رزروگیرنده</th><th>نام</th><th>شماره اتاق</th><th>تعداد نفرات</th><th>قیمت هتل</th><th>جمع کل</th><th>قیمت تمام شده هر نفر</th><th>تاریخ ورود</th><th>تاریخ خروج</th><th>خدمات</th></tr>';}
		else{
			$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="95%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;margin:10px;" ><tr class="showgrid_header" ><th>پذیرش</th><th>شماره رزرو</th><th>هتل</th><th>آژانس رزروگیرنده</th><th>نام</th><th>شماره اتاق</th><th>تعداد نفرات</th><th>تاریخ ورود</th><th>تاریخ خروج</th><th>خدمات</th></tr>';	}			
		if (count($res)>1)
		{
			$is_khorooj = reserve_class::isKhorooj($reserve_id,$room_id);
			$is_paziresh = reserve_class::isPaziresh($reserve_id,$room_id);
			$is1_khorooj = reserve_class::isKhorooj($res[1]['reserve_id'],$room_id);
			$is1_paziresh = reserve_class::isPaziresh($res[1]['reserve_id']);
			//if ((($is_khorooj)||(!$is_paziresh))&&((!$is1_khorooj)&&($is1_paziresh)))
			if($is_khorooj && !$is1_khorooj)
			{
				$is_khorooj = reserve_class::isKhorooj($res[1]['reserve_id'],$room_id);
				$reserve_id = $res[1]['reserve_id'];
				
			}
			else if($is_khorooj && $is1_khorooj)
				$reserve_id = 0;
		}
		$r_tmp = new room_class($room_id);
		//-----s جدول پذیرش
		$styl = 'class="showgrid_row_odd"';
		$horel_reserve = new hotel_reserve_class;
		$horel_reserve->loadByReserve($reserve_id);
		$ajans = new ajans_class($horel_reserve->ajans_id);
		$aj_name = hotel_class::loadAjans($reserve_id);
		//--------------------------
		$reserve_id_code =dechex($reserve_id+10000);
		$khorooj = '';
		$is_khorooj = reserve_class::isKhorooj($reserve_id,$room_id);
		//if(reserve_class::isKhorooj($reserve_id))
			//$khorooj = "<sapn style='color:green'>خارج شده</span>";		
		$day = date("Y-m-d");
		if (!$se->detailAuth('resturant'))
		{
			mysql_class::ex_sql("select `tatarikh`,`reserve_id`,`room_id` from `room_det` where `reserve_id`='$reserve_id' and `room_id`='$room_id'",$q);			
			if($r = mysql_fetch_array($q))
			{
				if ((reserve_class::isPaziresh($reserve_id,$room_id))&&(date("Y-m-d",strtotime($r['tatarikh']))==$day))
				{
					if(!$is_khorooj && reserve_class::isPaziresh($reserve_id))
						$khorooj = "<a style='color:green;' target='_blank' href='report.php?req=$reserve_id&room_id=$room_id&' >حساب</a> <a style='color:red' target='_blank' href='paziresh.php?reserve_id=$reserve_id_code&room_id=$room_id&kh=1' >خروج</a>";
				}
				else
				{
					if(!$is_khorooj && reserve_class::isPaziresh($reserve_id))
						$khorooj = "<a style='color:green;' target='_blank' href='report.php?req=$reserve_id&room_id=$room_id&' >حساب</a>";
				}
			}
			$paziresh ="<td><a target='_blank' href='paziresh.php?reserve_id=$reserve_id_code&room_id=$room_id&kh=0' >پذیرش</a>&nbsp;$khorooj</td>";
		}
		else
		{
			$khorooj = '';
			$paziresh = "<td>پذیرش</td>";
		}
		//--------------------------
	//	if(($_SESSION['daftar_id']==$ajans->daftar_id || $isAdmin) && !$se->detailAuth('super'))
		if(($_SESSION['daftar_id']==$ajans->daftar_id || $isAdmin))
		{
			$room = room_det_class::loadDetByReserve_id($reserve_id );
			$rooms = '';
			
			if(isset($room['rooms']))
			{
				for($j=0;$j<count($room['rooms']);$j++)
				{
					$tmp_room = new room_class($room['rooms'][$j]['room_id']);
					$rooms.=$tmp_room->name.(($j<count($room['rooms'])-1)?' , ':'');
				}
				$name = room_det_class::loadNamesByReserve_id($reserve_id );
				$khadamat = room_det_class::loadKhadamatByReserve_id($reserve_id );
				$is_paziresh = reserve_class::isPaziresh($reserve_id,$room_id);
				if ($is_paziresh)
					$voroodH = mehman_class::loadSaatVByReserveId($reserve_id);
				else
					$voroodH = 'میهمان پذیرش نشده است';
//var_dump($voroodH);
				if(!$is_khorooj)
				{
					$output .="<tr $styl >$paziresh<td>$reserve_id</td>";
					if(count($room['rooms'])<0)
						$paziresh = '';
					if (count($room['rooms'])>0)
					{
						if ($se->detailAuth('modir'))
						{
							$nafar = $room['rooms'][0]['nafar'];
							$mablagh_Nafar = ($horel_reserve->m_belit+$horel_reserve->m_hotel)/$nafar;
							$output .="<td>".$room['rooms'][0]['hotel']."</td><td>$aj_name</td><td>".$name[0]."</td><td>$rooms</td><td>".$room['rooms'][0]['nafar']."</td><td>".monize($horel_reserve->m_hotel)."</td>";
							$output .="<td>".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."</td><td>".monize($mablagh_Nafar)."</td>";
						
						}
						else
							$output .="<td>".$room['rooms'][0]['hotel']."</td><td>$aj_name</td><td>".$name[0]."</td><td>$rooms</td><td>".$room['rooms'][0]['nafar']."</td>";
						$output .="<td>".audit_class::hamed_pdate($room['rooms'][0]['aztarikh'])."</td>";
						if (substr($kh_name, 0, -1)!="")
							$output .="<td>".audit_class::hamed_pdate($room['rooms'][0]['tatarikh'])."</td><td>".substr($kh_name, 0, -1)."</td></tr>";
						else
							$output .="<td>".audit_class::hamed_pdate($room['rooms'][0]['tatarikh'])."</td><td>ندارد</td></tr>";
						if($horel_reserve->extra_toz!='')
							$output .="<tr $styl ><td>توضیحات : </td><td colspan='10'>".$horel_reserve->extra_toz.'<br/>'.' ساعت ورود:'.$voroodH."</td><td></td><td></td></tr>";
						else
							$output .="<tr $styl ><td>توضیحات : </td><td colspan='10'> ساعت ورود:".$voroodH."</td><td></td><td></td></tr>";
					}
			}
		}
		else
			$output='';
			//-----------------------------------------
			if(!$is_khorooj)
			{
				$grid = new jshowGrid_new("mehman","grid1");
				$grid->width = '99%';
				$grid->index_width = '20px';
				$grid->showAddDefault = FALSE;
				$grid->whereClause = "`reserve_id`='$reserve_id' and `room_id`=".$room_id;
				for($i=0;$i<count($grid->columnHeaders);$i++)
					$grid->columnHeaders[$i] = null;
				$grid->columnHeaders[3] = 'نام';
				$grid->columnHeaders[4] = 'نام  خانوادگی';
				$grid->columnHeaders[9] = 'ملیت';
				$grid->columnLists[9]=loadMellait();
				$grid->columnHeaders[16] = 'نسبت';
				$grid->columnLists[16]=loadNesbat();
				//$grid->sortEnabled = TRUE;
				$b = FALSE;
				$grid->canEdit = $b;
				$grid->canAdd = $b;
				$grid->canDelete = $b;
				$grid->intial();
			   	$grid->executeQuery();
				$out = $grid->getGrid();
			}
		}
		$tarikh_view = ($vaziat==4 or $vaziat==5) ? audit_class::hamed_pdate($r_tmp->end_fix_date):'';
	}
	else
		die('اطلاعات ناقض است');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript">
		function sabt(id)
		{
			//var vaziat = $("#vaziat").val();
			var vaziat = id;
			var tarikh;
			var tozih;
			var room_id= $("#room_id").val();
			if (id==4)
			{
				tarikh= $("#tarikh_ta").is(":visible") ? $("#tarikh_ta").val():'';
				tozih = $("#tozih_ta").val();
			}
			else if(id==5)
			{
				tarikh= $("#tarikh_po").is(":visible") ? $("#tarikh_po").val():'';
				tozih = $("#tozih_po").val();
			}
			else
			{
				tarikh= '0000-00-00 00:00:00';
				tozih = '';
			}
			$("#khoon").html('<img src="../img/status_fb.gif" >');
			$.get("gaantinfo.php?ch_room_id="+room_id+"&vaziat="+vaziat+"&tarikh="+tarikh+"&tozih="+tozih+"&",function(result){
				$("#khoon").html('');	
				if(result=="ok")
				{
					alert('تغییر با موفقیت انجام شد');
					window.location = window.location;
					window.opener.location = window.opener.location;
				}
				else
					//alert('خطا در اعمال تغییرات');
alert(result);
			});
		}
		function statusCH(id)
		{
			var vaziat = id;
			if(vaziat=="4")
			{
				$("#div_tarikh_ta").show('slow');
				$("#div_tarikh_po").hide('slow');
				$("#emal_5").hide('slow');
				$("#emal_4").show('slow');				
			}
			else if(vaziat=="5")
			{
				$("#div_tarikh_po").show('slow');
				$("#div_tarikh_ta").hide('slow');
				$("#emal_5").hide('slow');
				$("#emal_5").show('slow');				
			} 
			else
				$("#div_tarikh_ta").hide('slow');
				//$("#div_tarikh_po").hide('slow');
		}
		function sabt_dec()
		{
			alert('تغییر حالت اتاق به حالت درخواستی امکان پذیر نمی باشد');
		}
		$(function() {
	        //-----------------------------------
	        // انتخاب با کلیک بر روی عکس
	        $("#tarikh_ta").datepicker({
	            showOn: 'button',
		    dateFormat: 'yy/mm/dd',
	            buttonImage: '../js/styles/images/calendar.png',
	            buttonImageOnly: true
	        });
		$("#tarikh_po").datepicker({
	            showOn: 'button',
		    dateFormat: 'yy/mm/dd',
	            buttonImage: '../js/styles/images/calendar.png',
	            buttonImageOnly: true
	        });

	    });
		</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
			سامانه رزرواسیون	
		</title>
	</head>
	<body>
		<div style="margin:10px;">
			<a href="history.php?room_id=<?php echo $room_id;?>&" target="_blank"><img title="تاریخچه اتاق" src="../img/history.png"/></a>			
		</div>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<h3><?php echo $vazeat_name;?></h3>
			<br/>
			<table border='0' style="font-family: Terafik,Tahoma,tahoma;font-size:12px;">
				<tr>
					<td>
						<img style="cursor:pointer" onclick="<?php echo $dirty_onclick;?>" src="<?php echo $add_pic_dirty;?>" title="آزاد و نظافت نشده"/><br/> نظافت نشده
					</td>
					<td>
						<img style="cursor:pointer" onclick="<?php echo $clean_onclick;?>" src="<?php echo $add_pic_clean;?>" title="آزاد و نظافت شده"/><br/>آزاد
					</td>
					<td>
						<img style="cursor:pointer" onclick="<?php echo $tmpFull_onclick;?>" src="<?php echo $add_pic_tmpFull;?>" title="اشغال موقت"/><br/>اشغال موقت
					</td>
					<td>
						<img style="cursor:pointer" onclick="<?php echo $full_onclick;?>" src="<?php echo $add_pic_full;?>" title="اشغال"/><br/>اشغال
					</td>
					<td>
						<img style="cursor:pointer" onclick="statusCH(5);" src="../img/poshteban.png" title="پشتیبان"/><br/>پشتیبان
					</td>
					<td>
						<img style="cursor:pointer" onclick="statusCH(4)" src="../img/tasisat.png" title="در دست تعمیر"/><br/>در دست تعمیر
					</td>
				</tr>
			</table>
			<input id="vaziat" class="inp" name="vaziat" value="<?php echo $vaziat; ?>" style="width:40px;" type="hidden">			
			</br/>
				<span id="div_tarikh_ta" style="display:none;" ><input readonly="readonly" class="inp" name="tarikh_ta" id="tarikh_ta" placeholder="تاریخ را وارد کنید" value="<?php echo $tarikh_view; ?>" >
				<textarea class="inp" name="tozih_ta" id="tozih_ta" " rows="2" cols="50" ><?php
					$r_id = $r_tmp->id;
					if ($r_tmp->vaziat=='4')
					{echo 'tas';
						mysql_class::ex_sql("select max(`regdate`) as `ta` from `tasisat` where `room_id`='$r_id'",$q);			
						if($r = mysql_fetch_array($q))	
						{
							$ta = $r['ta'];
							mysql_class::ex_sql("select `toz` from `tasisat` where `regdate`='$ta'",$qu);		
							if($row = mysql_fetch_array($qu))
								echo $row["toz"];	
						}
					}
								
				?></textarea>				
				<?php
					if (!$se->detailAuth('resturant'))
					{
				?>
						<input type='button' id='emal_4' style="display:none" value='اعمال' class='inp' onclick='sabt(4);'>
				<?php	}?>
				</span>
				<span id="div_tarikh_po" style="display:none;" ><input readonly="readonly" class="inp" name="tarikh_po" id="tarikh_po" placeholder="تاریخ را وارد کنید" value="<?php echo $tarikh_view; ?>" >
				<textarea class="inp" name="tozih_po" id="tozih_po" " rows="2" cols="50" ><?php
					if ($r_tmp->vaziat=='5')
					{
						mysql_class::ex_sql("select max(`regdate`) as `ta` from `poshtiban` where `room_id`='$r_id'",$q);			
						if($r = mysql_fetch_array($q))	
						{
							$ta = $r['ta'];
							mysql_class::ex_sql("select `toz` from `poshtiban` where `regdate`='$ta'",$qu);		
							if($row = mysql_fetch_array($qu))
								echo $row["toz"];	
						}
					}			
				?></textarea>
				<?php
					if (!$se->detailAuth('resturant'))
					{?>
						<input type='button' id='emal_5' style="display:none" value='اعمال' class='inp' onclick='sabt(5);'>
				<?php	}?>
				</span>
				
				<input id="room_id_1" class="inp" name="room_id_1" value="<?php echo $r_tmp->name; ?>" readonly="readonly" style="width:40px;" type="hidden" >
				<input id="room_id" class="inp" name="room_id" value="<?php echo $r_tmp->id; ?>" readonly="readonly" style="width:40px;" type="hidden" >
				<div id="khoon" ></div>
				<?php
					if(($isAdmin || $se->detailAuth('super')) && $vaziat == 4)
						echo '<a href="tasisat.php?room_id='.$room_id.'&" target="_blank">تاسیسات</a>';
					echo $output.'</table><br/><h3>اطلاعات پذیرش</h3>'.$out;
				?>
		</div>
	</body>
</html>
