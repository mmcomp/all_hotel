<?php 
// var_dump($_POST);
session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = $se->detailAuth('all');
	$isTasisat = $se->detailAuth('tasisat');
	$out='';
	if (isset($_SESSION['user_id']))
		$user_id = (int)$_SESSION['user_id'];
	else
		$user_id = -1;
$room_id = (isset($_POST['oid']))?$_POST['oid']:"";
$name = (isset($_POST['oname']))?$_POST['oname']:"";
$ajans = (isset($_POST['oajans']))?$_POST['oajans']:"";
$reserve_id = (isset($_POST['oreserve_id']))?$_POST['oreserve_id']:"";
$rname = (isset($_POST['orname']))?$_POST['orname']:"";
$hoid = (isset($_POST['ohoid']))?$_POST['ohoid']:"";
$nafar = (isset($_POST['onafar']))?$_POST['onafar']:"";
$rooms1 = (isset($_POST['orooms']))?$_POST['orooms']:"";
$tarikh_v = (isset($_POST['otarikh_v']))?$_POST['otarikh_v']:"";
$t_v=jdate('Y/n/j',strtotime($tarikh_v));
$tarikh_kh = (isset($_POST['otarikh_kh']))?$_POST['otarikh_kh']:"";
$t_kh=jdate('Y/n/j',strtotime($tarikh_kh));
$hotelPrice = (isset($_POST['ohotelPrice']))?$_POST['ohotelPrice']:"";
$tozih = (isset($_POST['otozih']))?$_POST['otozih']:"";
$mfname = (isset($_POST['omfname']))?$_POST['omfname']:"";
$mlname = (isset($_POST['omlname']))?$_POST['omlname']:"";
$mmeliat = (isset($_POST['ommeliat']))?$_POST['ommeliat']:"";
$mnesbat = (isset($_POST['omnesbat']))?$_POST['omnesbat']:"";
$vaziat = (isset($_POST['ovaziat']))?$_POST['ovaziat']:"";
$hotel_id = (isset($_POST['ohotel_id']))?$_POST['ohotel_id']:"";
$room_typ = (isset($_POST['oroom_typ']))?$_POST['oroom_typ']:"";
$roomsn = (isset($_POST['oroomsn']))?$_POST['oroomsn']:"";
$service_ezafe = (isset($_POST['service_ezafe']))?$_POST['service_ezafe']:"";

if(isset($_REQUEST['ch_room_id']))
	{
		
		$room_id = (int)$_REQUEST['ch_room_id'];
		$vaziat = (int)$_REQUEST['vaziat'];
		$tarikh = ($_REQUEST['tarikh']!='') ?audit_class::hamed_pdateBack($_REQUEST['tarikh']):'0000-00-00 00:00:00';
		$tozih = $_REQUEST['tozih'];
		$date = date("Y-m-d H:i:s");		
		$tarikh_qu = ",`end_fix_date`='$tarikh'";
// 		mysql_class::ex_sql("select `vaziat` from `room` where `id`='$room_id'",$q);
// 		if($r = mysql_fetch_array($q))
// 		{
// 			if ($r['vaziat']==1)
// 			{
// 				mysql_class::ex_sql("select `room_id`,`en`,max(`id`) as `max_id` from `nezafat` where `room_id`='$room_id'",$q_nezafat);
// 				if($r_nezafat = mysql_fetch_array($q_nezafat))
// 				{	
// 					$today = date("Y-m-d H:i:s");
// 					$room_id = $r_nezafat['room_id'];
// 					$max_id = $r_nezafat['max_id'];
// 					mysql_class::ex_sqlx("update `nezafat` set `nezafat_time` = '$today', `user_nezafat` = '$user_id',`en`='1'  where `room_id` = '$room_id' and `en`='0' and `id`='$max_id'");
// 				}
// 			}
// 		}
		if($room_id>0 && $vaziat>=0)
		{
			if ($vaziat<=3)
			{
				//echo '1';
				mysql_class::ex_sqlx("update `room` set `vaziat` = $vaziat $tarikh_qu where `id` = $room_id");
				if ($vaziat==1)
					mysql_class::ex_sqlx("insert into `nezafat` (`id`, `room_id`, `reserve_id`, `mani_time`, `nezafat_time`, `user_id`, `user_nezafat`, `en`) VALUES (NULL, '$room_id', '-1', '$date', '0000-00-00 00:00:00', '$user_id', '-1', '0')");
				die('ok');
			}
			elseif ($vaziat>5)
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
				die('اطلاعات را تکمیل نمایید');			
		}
		else
		{
			die('nok');
		}
	}


if($room_id)
	{
// 		echo "room_id = $room_id<br/>\n";
		if(!isset($tarikh))
			$tarikh = date("Y-m-d H:i:s");	
		$r_tmp = new room_class($room_id);/*
		$hotel_id = $r_tmp->hotel_id;*/
		//$vaziat = $r_tmp->vaziat;
// 		var_dump($r_tmp);
// 		var_dump($tarikh);
		$res = $r_tmp->getAnyReserve($tarikh);
// 		var_dump($res);
		/*$reserve_id = $res[0]['reserve_id'];
		$khadamat1 = khadamat_det_class::loadByReserve_habibi($reserve_id);
		$kh_name = '';
		$sharj = '';
		$clean_onclick="";
		$dirty_onclick="";
		$tmpFull_onclick="";
		$full_onclick="";
		*////eshghal
		if(!$se->detailAuth('garanti'))
		{
			if ($vaziat==0)
			{
				$vazeat_name = " اتاق".' '.$r_tmp->name.' '."در وضعیت اشغال";
				$clean_onclick="sabt_dec();";
				$add_pic_clean = "../img/deactive.png";
				$dirty_onclick="sabt_dec();";
				$add_pic_dirty = "../img/deactive.png";	
				$tmpFull_onclick="sabt_dec();";
				$add_pic_tmpFull = "../img/deactive.png";
				$full_onclick="sabt(0);";
				$add_pic_full = "../img/fullRoom.png";
				$poshtiban_onclick = "statusCH(5);";
				$tamir_onclick = "statusCH(4);";
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
				$poshtiban_onclick = "statusCH(5);";
				$tamir_onclick = "statusCH(4);";
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
				$poshtiban_onclick = "statusCH(5);";
				$tamir_onclick = "statusCH(4);";
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
				$poshtiban_onclick = "statusCH(5);";
				$tamir_onclick = "statusCH(4);";
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
				$poshtiban_onclick = "statusCH(5);";
				$tamir_onclick = "statusCH(4);";
			}
			elseif ($vaziat==5)
			{
				$clean_onclick="sabt_dec();";
				$dirty_onclick="sabt(1);";
				$tmpFull_onclick="sabt_dec();";
				$full_onclick="sabt_dec();";
				$poshtiban_onclick = "statusCH(5);";
				$tamir_onclick = "statusCH(4);";
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
				$poshtiban_onclick = "statusCH(5);";
				$tamir_onclick = "statusCH(4);";
				$vazeat_name = "اتاق".' '.$r_tmp->name.' '."در وضعیت نامعلوم ";
				$add_pic_clean = "../img/deactive.png";
				$add_pic_dirty = "../img/deactive.png";	
				$add_pic_tmpFull = "../img/deactive.png";
				$add_pic_full = "../img/deactive.png";
			}	
		}
		else
		{
			if ($vaziat==0)
			{
				$vazeat_name = " اتاق".' '.$r_tmp->name.' '."در وضعیت اشغال";
				$clean_onclick="sabt_dec();";
				$add_pic_clean = "../img/deactive.png";
				$dirty_onclick="sabt_dec();";
				$add_pic_dirty = "../img/deactive.png";	
				$tmpFull_onclick="sabt_dec();";
				$add_pic_tmpFull = "../img/deactive.png";
				$full_onclick="sabt_dec();";
				$add_pic_full = "../img/fullRoom.png";
				$poshtiban_onclick = "sabt_dec();";
				$tamir_onclick = "sabt_dec();";
			}
		///nezafatNashode
			elseif ($vaziat==1)
			{
				$clean_onclick="sabt_dec();";
				$dirty_onclick="sabt_dec();";
				$tmpFull_onclick="sabt_dec();";
				$full_onclick="sabt_dec();";
				$vazeat_name = " اتاق".' '.$r_tmp->name.' '."در وضعیت نظافت نشده ";
				$add_pic_clean = "../img/readuRoom.png";
				$add_pic_dirty = "../img/dirtyRoom.png";	
				$add_pic_tmpFull = "../img/deactive.png";
				$add_pic_full = "../img/deactive.png";
				$poshtiban_onclick = "sabt_dec();";
				$tamir_onclick = "sabt_dec();";
			}
		///nezafatShode
			elseif ($vaziat==2)
			{
				$clean_onclick="sabt_dec();";
				$dirty_onclick="sabt_dec();";
				$tmpFull_onclick="sabt_dec();";
				$full_onclick="sabt_dec();";
				$vazeat_name = " اتاق ".' '.$r_tmp->name.' '." در وضعیت آزاد و نظافت شده ";
				$add_pic_clean = "../img/readuRoom.png";
				$add_pic_dirty = "../img/deactive.png";	
				$add_pic_tmpFull = "../img/tempFull.png";
				$add_pic_full = "../img/fullRoom.png";
				$poshtiban_onclick = "sabt_dec();";
				$tamir_onclick = "sabt_dec();";
			}
		///eshghalMovaghat
			elseif ($vaziat==3)
			{
				$clean_onclick="sabt_dec();";
				$dirty_onclick="sabt_dec();";
				$tmpFull_onclick="sabt_dec();";
				$full_onclick="sabt_dec();";
				$vazeat_name = " اتاق".' '.$r_tmp->name.' '."در وضعیت اشغال موقت ";
				$add_pic_clean = "../img/deactive.png";
				$add_pic_dirty = "../img/dirtyRoom.png";	
				$add_pic_tmpFull = "../img/tempFull.png";
				$add_pic_full = "../img/fullRoom.png";
				$poshtiban_onclick = "sabt_dec();";
				$tamir_onclick = "sabt_dec();";
			}
			elseif ($vaziat==4)
			{
				$clean_onclick="sabt_dec();";
				$dirty_onclick="sabt_dec();";
				$tmpFull_onclick="sabt_dec();";
				$full_onclick="sabt_dec();";
				$vazeat_name = " اتاق".' '.$r_tmp->name.' '."در وضعیت تعمیرات ";
				$add_pic_clean = "../img/deactive.png";
				$add_pic_dirty = "../img/dirtyRoom.png";	
				$add_pic_tmpFull = "../img/deactive.png";
				$add_pic_full = "../img/deactive.png";
				$poshtiban_onclick = "sabt_dec();";
				$tamir_onclick = "sabt_dec();";
			}
			elseif ($vaziat==5)
			{
				$clean_onclick="sabt_dec();";
				$dirty_onclick="sabt_dec();";
				$tmpFull_onclick="sabt_dec();";
				$full_onclick="sabt_dec();";
				$vazeat_name = " اتاق".' '.$r_tmp->name.' '." در وضعیت پشتیبان ";
				$add_pic_clean = "../img/deactive.png";
				$add_pic_dirty = "../img/dirtyRoom.png";	
				$add_pic_tmpFull = "../img/deactive.png";
				$add_pic_full = "../img/deactive.png";
				$poshtiban_onclick = "sabt_dec();";
				$tamir_onclick = "sabt_dec();";
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
				$poshtiban_onclick = "sabt_dec();";
				$tamir_onclick = "sabt_dec();";
			}	
		}	
        
		/*mysql_class::ex_sql("select min(`aztarikh`) as `min_az`,max(`tatarikh`) as `max_ta` from `room_det` where `reserve_id`='$reserve_id' and `room_id`='$room_id'",$q);
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
		//var_dump($khadamat1);
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
		$kh_name .= $name_kh.','.$sharj.',';*/
    $RakModal="<div class='modal-content'>
					<div class=\"modal-header\" style=\"background-color:#5e87b0;border-top-left-radius:5px;border-top-right-radius:5px;color:white\">
                       
					  <div class=\"col-md-6 col-sm-5 pull-right\">
					  
                           
                           <h5 style=\"float:right\" class=\"modal-title\">$vazeat_name</h5>
                      </div>
                        <div class=\"col-md-6 col-sm-7 pull-left\" style=\"text-align:left\">
				       <div class=\"col-md-12\">
                           <label style=\"float:right\" class=\"control-label\" for=\"status\">تغییر وضعیت:</label> 
											 <div style=\"\" class=\"col-md-6 col-sm-5 status\">";
    
												$RakModal=$RakModal."
                                                <input type=\"hidden\" value=\"$hotel_id\" id=\"hotel_id\" />
                                                <input type=\"hidden\" value=\"$room_type\" id=\"room_typ\" />
                                                <input type=\"hidden\" value=\"$room_id\" id=\"rroom_id\" />
                                                <select id=\"vaz\" onchange=\"changeVaz()\" name=\"status\" id=\"status\" class=\"select2-01 col-md-12\" style=\"border-radius:3px;margin-top:-3px;\">
                                                  
                                                <option id=\"1\" value=\"$dirty_onclick\" ";if ($vaziat==1) $RakModal=$RakModal. "selected='selected'";$RakModal=$RakModal." >نظافت نشده</option>           
                                                <option id=\"2\" value=\"$clean_onclick\" ";if ($vaziat==2) $RakModal=$RakModal. "selected='selected'";$RakModal=$RakModal.">آزاد</option>
                                                <option id=\"3\" value=\"$tmpFull_onclick\" ";if ($vaziat==3) $RakModal=$RakModal. "selected='selected'";$RakModal=$RakModal.">اشغال موقت</option>
                                                <option id=\"0\" value=\"$full_onclick\" ";if ($vaziat==0) $RakModal=$RakModal. "selected='selected'";$RakModal=$RakModal.">اشغال</option>
                                                <option id=\"5\" value=\"$poshtiban_onclick\" ";if ($vaziat==5) $RakModal=$RakModal. "selected='selected'";$RakModal=$RakModal.">پشتیبان</option>
                                                <option id=\"4\" value=\"$tamir_onclick\" ";if ($vaziat==4) $RakModal=$RakModal. "selected='selected'";$RakModal=$RakModal.">در دست تعمیر</option>
                                   </select>";
    $RakModal=$RakModal." 
											 </div>
                           
                       <span id=\"div_tarikh_ta\" style=\"display:none;\" ><input class=\"form-control inp\" name=\"tarikh_ta\" placeholder=\"تاریخ را وارد کنید\" id=\"datepicker22\"  >
				<textarea class=\"form-control inp\" name=\"tozih_ta\" id=\"tozih_ta\" rows=\"2\" cols=\"50\" >";

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
								

$RakModal=$RakModal." </textarea> ";			
				
					if (!$se->detailAuth('resturant'))
					{
				
						$RakModal=$RakModal."<input type='button' id='emal_4' style=\"display:none\" value='اعمال' class='btn btn-info inp' onclick='sabt(4);'>";					}
			$RakModal=$RakModal."	</span>
                
				<span id=\"div_tarikh_po\" style=\"display:none;\" ><input type=\"text\" name=\"tarikh_po\" class=\"form-control\" value=\"\" placeholder=\"تاریخ را وارد کنید\" id=\"datepicker11\">
				<textarea class=\"form-control inp\" name=\"tozih_po\" id=\"tozih_po\" rows=\"2\" cols=\"50\" >";
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
				$RakModal=$RakModal."</textarea>";
				
					if (!$se->detailAuth('resturant'))
					{
						$RakModal=$RakModal."<input type='button' id='emal_5' style=\"display:none\" value='اعمال' class='btn btn-info inp' onclick='sabt(5);'>";
				}
			$RakModal=$RakModal."	</span>
                            <a style=\"margin-left:5px;\" href=\"history.php?room_id=$room_id\" target=\"_blank\"><i style=\"font-size:25px;color:white\" class=\"fa fa-calendar\" title=\"تاریخچه اتاق\"></i></a>
                            <a style=\"margin-left:5px;\" href=\"ravabet.php?room_id=$room_id&reserve_id=$reserve_id\" target=\"_blank\"><i style=\"font-size:25px;color:white\" class=\"fa fa-bar-chart-o\" title=\"نظرسنجی\"></i></a>
                       </div>
                            </div>
                       <br/>
                       <br/>
					</div>
					<div class=\"modal-body\" style=\"max-height:400px;overflow-y:scroll\" id=\"modBody\">
						<div style=\"height:300px;overflow:auto;\">";
                    
		if ($se->detailAuth('modir'))
		{
			$RakModal=$RakModal."<div class=\"row\" style=\"margin:0px;\">
                                <div class=\"col-lg-12\">
							<div class=\"panel panel-default\">
                        <div class=\"panel-heading\" style=\"color:#FFFFFF;background-color:#ffb848;border-bottom: 1px solid #ffae2e;\">
                            اطلاعات پذیرنده
                        </div>
                        
                        <div class=\"panel-body\">
                            <div style=\"overflow:scroll;height:200px;\" class=\"dataTable_wrapper\">
                                <table style=\"width:2000px;\" class=\"table table-striped table-bordered table-hover\" id=\"\">
                                    <thead>
                                        <tr>
                                            <th style=\"text-align:right;width:220px;\">پذیرش</th>
                                            <th style=\"text-align:right;width:80px;\">شماره رزرو</th>
                                            <th style=\"text-align:right;width:120px;\">آژانس رزرو گیرنده</th>
                                            <th style=\"text-align:right;\">نام</th>
                                            <th style=\"text-align:right;overflow:hidden;\">شماره اتاق</th>
                                            <th style=\"text-align:right;width:85px;\">تعداد نفرات</th>
                                            <th style=\"text-align:right;width:85px;\">سرویس اضافه</th>
                                            <th style=\"text-align:right;width:75px;\">قیمت هتل</th>
                                            <th style=\"text-align:right;\">جمع کل</th>
                                            <th style=\"text-align:right;width:170px;\">قیمت تمام شده برای هر نفر</th>
                                            <th style=\"text-align:right;\">تاریخ ورود</th>
                                            <th style=\"text-align:right;\">تاریخ خروج</th>
                                            <th style=\"text-align:right;\">خدمات</th>
                                            <th style=\"text-align:right;\">توضیحات</th>
                                            
                                            
                                        </tr>
                                    </thead>";}
		else{
			$RakModal=$RakModal."<div class=\"row\" style=\"margin:0px;\">
                                <div class=\"col-lg-12\">
							<div class=\"panel panel-default\">
                        <div class=\"panel-heading\" style=\"color:#FFFFFF;background-color:#ffb848;border-bottom: 1px solid #ffae2e;\">
                            اطلاعات پذیرنده
                        </div>
                        
                        <div class=\"panel-body\">
                            <div style=\"overflow:scroll;height:200px;\" class=\"dataTable_wrapper\">
                                <table style=\"width:2000px;height:200px\" class=\"table table-striped table-bordered table-hover\" id=\"\">
                                    <thead>
                                        <tr>
                                            <th style=\"text-align:right;width:220px;\">پذیرش</th>
                                            <th style=\"text-align:right;width:80px;\">شماره رزرو</th>
                                            <th style=\"text-align:right;width:120px;\">آژانس رزرو گیرنده</th>
                                            <th style=\"text-align:right;\">نام</th>
                                            <th style=\"text-align:right;overflow:hidden;\">شماره اتاق</th>
                                            <th style=\"text-align:right;width:85px;\">تعداد نفرات</th>
                                            <th style=\"text-align:right;width:85px;\">سرویس اضافه</th>
                                            <th style=\"text-align:right;\">تاریخ ورود</th>
                                            <th style=\"text-align:right;\">تاریخ خروج</th>
                                            <th style=\"text-align:right;\">خدمات</th>
                                            <th style=\"text-align:right;\">توضیحات</th>
                                            
                                            
                                        </tr>
                                    </thead>";	}	
                        
                          
                                   
    
//     var_dump($res);
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
		$reserve = new reserve_class($reserve_id);
		$rname = $reserve->hotel_reserve->lname;
		$ajans = new ajans_class($reserve->hotel_reserve->ajans_id);
		$ajans = $ajans->name;
		$nafar = 0;
		foreach($reserve->room_det as $rrrr){
			if($rrrr->room_id==$room_id){
				$nafar = $rrrr->nafar;
				$t_v = jdate("Y/m/d",strtotime($rrrr->aztarikh));
				$t_kh = jdate("Y/m/d",strtotime($rrrr->tatarikh));
			}
		}
		$hotelPrice = $reserve->hotel_reserve->m_hotel;
		$tozih = $reserve->hotel_reserve->tozih;
// 		var_dump($reserve);
// 		echo $reserve_id;
		$r_tmp = new room_class($room_id);
		//-----s جدول پذیرش
		/*$styl = 'class="showgrid_row_odd"';
		$horel_reserve = new hotel_reserve_class;
		$horel_reserve->loadByReserve($reserve_id);
		$ajans = new ajans_class($horel_reserve->ajans_id);
		$aj_name = hotel_class::loadAjans($reserve_id);
		*///--------------------------
		$reserve_id_code =dechex($reserve_id+10000);
		$khorooj = '';
		$is_khorooj = reserve_class::isKhorooj($reserve_id,$room_id);
		//if(reserve_class::isKhorooj($reserve_id))
			//$khorooj = "<sapn style='color:green'>خارج شده</span>";		
		$day = date("Y-m-d");
		if (!$se->detailAuth('resturant'))
		{
// 			echo "select `tatarikh`,`reserve_id`,`room_id` from `room_det` where `reserve_id`='$reserve_id' and `room_id`='$room_id'";
			mysql_class::ex_sql("select `tatarikh`,`reserve_id`,`room_id` from `room_det` where `reserve_id`='$reserve_id' and `room_id`='$room_id'",$q);			
			if($r = mysql_fetch_array($q))
			{
// 				var_dump(reserve_class::isPaziresh($reserve_id,$room_id));
// 				echo $day.' - '.$r['tatarikh'];
				if ((reserve_class::isPaziresh($reserve_id,$room_id))&&(date("Y-m-d",strtotime($r['tatarikh']))==$day))
				{
					var_dump($is_khorooj);
					if(!$is_khorooj && reserve_class::isPaziresh($reserve_id))
						$khorooj = "<a target='_blank' href='report.php?req=$reserve_id&room_id=$room_id&' ><button  style='margin-top:3px;' class=\"btn btn-warning\"><i class=\"fa fa-bell\"></i> حساب</button></a>"." <a onclick=\"khoroojM('$reserve_id_code','$room_id','1')\"><button class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> خروج اتاق</button></a>";
                    mysql_class::ex_sql("select count(distinct(room_id)) as cnt from `mehman` where `reserve_id`='$reserve_id' group by 'room_id'",$ss);
                        $rr = mysql_fetch_array($ss);
                        $cnt = $rr['cnt'];
                        if($cnt>1)
                            $khorooj.="<a onclick=\"khoroojM('$reserve_id_code','-1','1')\"><button class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> خروج کل رزرو</button></a>";
				}
				else
				{
					if(!$is_khorooj && reserve_class::isPaziresh($reserve_id))
						$khorooj = "<a target='_blank' href='report.php?req=$reserve_id&room_id=$room_id&' ><button  style='margin-top:3px;' class=\"btn btn-warning\"><i class=\"fa fa-bell\"></i> حساب</button></a>";
				}
			}
			$paziresh ="<a target='_blank' href='paziresh.php?reserve_id=$reserve_id_code&room_id=$room_id&hotel_id=$hotel_id&room_typ=$room_typ&kh=0' ><button class=\"btn btn-success\"><i class=\"fa fa-sign-in\"></i> پذیرش</button></a>&nbsp;<a href=\"#\" onclick=\"editNafar('$reserve_id','$rooms1','$roomsn')\" ><button class=\"btn btn-primary\"><i class=\"fa fa-edit\"></i> ویرایش نفرات</button></a>&nbsp;$khorooj";
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
			//$room = room_det_class::loadDetByReserve_id($reserve_id );
			$rooms = '';
            
            $rooms2 = explode("|",$rooms1);
            $roomsn2 = explode("|",$roomsn);
            $i=0;
			if($rooms2[0])/*isset($room['rooms']))*/
			{
				//for($j=0;$j<count($room['rooms']);$j++)
				//{
					//$tmp_room = new room_class($room['rooms'][$j]['room_id']);
                foreach ($rooms2 as $room20){
                    $room2 = explode("_",$room20);
                    if($room2[0]!=null){
                    $rooms='<span class="label label-info" style="margin:3px;">'.$room2[0].'('.$roomsn2[$i].')</span>'.$rooms;   
                    $i++;}
                    //(($j<count($room['rooms'])-1)?' , ':'');
				//}
                }
				//$name = room_det_class::loadNamesByReserve_id($reserve_id );
				$khadamat = room_det_class::loadKhadamatByReserve_id($reserve_id );
				$is_paziresh = reserve_class::isPaziresh($reserve_id,$room_id);
				if ($is_paziresh)
					$voroodH = mehman_class::loadSaatVByReserveId($reserve_id);
				else
					$voroodH = 'میهمان پذیرش نشده است';
//var_dump($voroodH);
				if(!$is_khorooj)
				{
					if ((count($rooms2))-1>0)
					{
                        $RakModal=$RakModal."<tbody>
                                        
                                        <tr class='odd'>
                                            <td>$paziresh</td>
                                            <td>$reserve_id</td>";
                        
                       
						if ($se->detailAuth('modir'))
						{
							
							//$nafar = $room['rooms'][0]['nafar'];
							/*$nafar = 0;
							foreach($room['rooms'] as $rmm)
								//if($rmm['nafar'] > 0)
									$nafar += $rmm['nafar'];*/
							$mablagh_Nafar = $hotelPrice/$nafar;
                            $RakModal=$RakModal."<td>$ajans</td>
                                            <td>$rname</td>
                                            <td>$rooms</td>
                                            <td>$nafar</td>
                                            <td>$service_ezafe</td>
                                            <td>$hotelPrice</td>
                                            <td>".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."</td>
                                            <td>$mablagh_Nafar</td>";						
						}
						else
							$RakModal=$RakModal."<td>$ajans</td><td>$rname</td><td>$rooms</td><td>$nafar</td>";
						$RakModal=$RakModal."<td>$t_v</td>";
						if (substr($kh_name, 0, -1)!="")
							$RakModal=$RakModal."<td>$t_kh</td><td>".substr($kh_name, 0, -1)."</td>";
						else
							$RakModal=$RakModal."<td>$t_kh</td><td>ندارد</td>";
						if($tozih!='')
							$RakModal=$RakModal."<td>$tozih<br/>'.' ساعت ورود:".$voroodH."</td></tr>";
						else
							$RakModal=$RakModal."<td> ساعت ورود:".$voroodH."</td><tr>";
					}
			}
		}
		else
			$RakModal=$RakModal.'';
             
                
                                     
                                       
                                   $RakModal=$RakModal."</tbody>
                                </table>
                            </div>
                            
                          
                        </div>
                        
                    </div>
                   
                      </div>
                                </div>";
                           
                          $RakModal=$RakModal."<!-- <div class=\"row\" style=\"margin:0px;\">
                                <div class=\"col-lg-12\">
							<div class=\"panel panel-default\">
                        <div class=\"panel-heading\" style=\"color:#FFFFFF;background-color:#ffb848;border-bottom: 1px solid #ffae2e;\">
                            تاسیسات
                        </div>
                        
                        <div class=\"panel-body\">
                            <div style=\"overflow-x:scroll\" class=\"dataTable_wrapper\">
                                <table style=\"width:100%\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                    <thead>
                                        <tr>
                                            <th style=\"text-align:right;width:1px\">رديف</th>
                                            <th style=\"text-align:right;\">ثبت کننده مشکل</th>
                                            <th style=\"text-align:right;\">برطرف کننده مشکل</th>
                                            <th style=\"text-align:right;\">توضیح مشکل</th>
                                            <th style=\"text-align:right;\">توضیح رفع مشکل</th>
                                            <th style=\"text-align:right;\">تاریخ ثبت</th>
                                            <th style=\"text-align:right;\">تاریخ رفع مشکل</th>
                                            <th style=\"text-align:right;\">وضعیت</th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <tr class='odd'>
                                            <td>1</td>
                                            <td>تقی</td>
                                            <td>من</td>
                                            <td>خاص نبود</td>
                                            <td>درست شد</td>
                                            <td>1395-05-03</td>
                                            <td>1395-05-04</td>
                                            <td><label style=\"font-weight:normal\" class=\"checkbox\"> <input type=\"radio\" class=\"uniform\" style=\"float:right;\" value=\"1\" name=\"states\" checked /> حل شده</label>
                            <label style=\"font-weight:normal\" class=\"checkbox\"> <input type=\"radio\" class=\"uniform\" style=\"float:right;\" value=\"2\" name=\"states\" /> حل نشده</label>
                     
                                            </td>
                                            
                                        </tr>
                
                     <tr class='even'>
                                            <td>2</td>
                                            <td>تقی</td>
                                            <td>من</td>
                                            <td>خاص نبود</td>
                                            <td>درست شد</td>
                                            <td>1395-05-03</td>
                                            <td>1395-05-04</td>
                                            <td><input type=\"radio\" name=\"state\" value=\"1\" /> حل شد
                                            <input type=\"radio\" name=\"state\" value=\"2\" /> حل نشد
                                            </td>
                                            
                                        </tr>
                
                                     
                                       
                                    </tbody>
                                </table>
                            </div>
                            
                          
                        </div>
                    </div>
                      </div>
                                </div>-->";
                            
                            if($mfname || $mlname){
                            $RakModal=$RakModal."<div class=\"row\" style=\"margin:0px;\">
                                <div class=\"col-lg-12\">
							<div class=\"panel panel-default\">
                        <div class=\"panel-heading\" style=\"color:#FFFFFF;background-color:#ffb848;border-bottom: 1px solid #ffae2e;\">
                            اطلاعات مهمان ها
                        </div>
                        <div class=\"panel-body\">
                            <div style=\"overflow-x:scroll\" class=\"dataTable_wrapper\">
                                <table style=\"width:100%\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example1\">
                                    <thead>
                                        <tr>
                                            <th style=\"text-align:right;width:1px;\">رديف</th>
                                            <th style=\"text-align:right\">نام</th>
                                            <th style=\"text-align:right\">نام خانوادگی</th>
                                            <th style=\"text-align:right\">ملیت</th>
                                            <th style=\"text-align:right\">نسبت</th>                                      
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ";
                                $mfnames=explode("|",$mfname);
                                $mlnames=explode("|",$mlname);
                                $mmeliats=explode("|",$mmeliat);
                                $mnesbats=explode("|",$mnesbat);
                                $i=1;
                                $in=0;
                                foreach ($mfnames as $mfname){
                                    if($mmeliats[$in]==2)
                                        $mel="ایرانی";
                                    else if($mmeliats[$in]==7)
                                        $mel="غیر ایرانی";
                                    else
                                        $mel="";
                                    $nes = $mnesbats[$in];
                                    mysql_class::ex_sql("select * from `statics` where `fkey` = 'نسبت' and `id` = '$nes' ",$n_id);
                                    $n_id1 = mysql_fetch_array($n_id);
                                    $nname = $n_id1['fvalue'];
                                     if(fmod($i,2)!=0){
                                        
                                          $RakModal=$RakModal. "<tr class='odd'>
                                            <td>$i</td>
                                            <td>$mfnames[$in]</td>
                                            <td>$mlnames[$in]</td>
                                            <td>$mel</td>
                                            <td>$nname</td>
                                            
                                        </tr>";
                                        $i++;
                                        $in++; 
                                     }
                                   else {
                                         
                                         $RakModal=$RakModal. "<tr class='even'>
                                            <td>$i</td>
                                            <td>$mfnames[$in]</td>
                                            <td>$mlnames[$in]</td>
                                            <td>$mel</td>
                                            <td>$nname</td>
                                            
                                        </tr>";
                                        $in++;
                                       $i++;
                                     }}
            $RakModal=$RakModal. "
                                    </tbody>
                                </table>
                            </div>
                          
                        </div>
                    </div>
                      </div>
                                </div>";}
						$RakModal=$RakModal."</div>
					</div>
					<div class=\"modal-footer\">
						<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\" >بستن</button>
					</div>
				  </div>
				</div>";
            //echo $vazeat_name."|".$reserve_id."|".$room['rooms'][0]['hotel']."|".$aj_name."|".$name[0]."|".$rooms."|".$room['rooms'][0]['nafar']."|".monize($horel_reserve->m_hotel)."|".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."|".monize($mablagh_Nafar)."|".audit_class::hamed_pdate($room['rooms'][0]['aztarikh'])."|".audit_class::hamed_pdate($room['rooms'][0]['tatarikh'])."|".substr($kh_name, 0, -1)."|".$horel_reserve->extra_toz;
            echo $RakModal;
			//-----------------------------------------
			if(!$is_khorooj)
			{
				/*$grid = new jshowGrid_new("mehman","grid1");
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
				$out = $grid->getGrid();*/
			}
		}
		$tarikh_view = ($vaziat==4 or $vaziat==5) ? audit_class::hamed_pdate($r_tmp->end_fix_date):'';
	}
	else
		die('اطلاعات ناقض است');
?>
<script>
	
        jQuery(document).ready(function() {
            $("#datepicker0").datepicker();
            
                $("#datepicker11").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                    
                });
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
                $("#datepicker22").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });
        });

</script>        
            