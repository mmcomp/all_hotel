<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(!isset($_REQUEST['isFactor']))
		die(lang_fa_class::access_deny);
	if(!isset($_SESSION['factor_shomare']))
		$_SESSION['factor_shomare']=-2;
	if(isset($_REQUEST['factor_shomare_req']))
		$_SESSION['factor_shomare']=(int)$_REQUEST['factor_shomare_req'];
	function loadSandogh_item($sandogh_id) 
	{
		$out = null;
		mysql_class::ex_sql("select `id`,`name`,`en`,`mablagh_det` from `sandogh_item` where `sandogh_id` = $sandogh_id",$q);
		while($r = mysql_fetch_array($q))
			$out[$r['name'] . '(' . (((int)$r['en']==1)?'مبلغ قابل تغییر نیست ':'').monize($r['mablagh_det']) . ' ریال' . ')'] = (int)$r['id'];
		return($out);
	}
	function add_item()
	{
		if((int)$_SESSION['factor_shomare'] <= 0)
			$_SESSION['factor_shomare'] = sandogh_factor_class::getShomareFactor();
		$miz = isset($_REQUEST['miz']) ?(int)$_REQUEST['miz']:-1;
		$factor_shom = $_SESSION['factor_shomare'];
		$isFactor = ((int)$_REQUEST['isFactor'] == 1)?TRUE:FALSE;
		$reserve_id = (isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:-1;
		$room_id =  (isset($_REQUEST['room_id']))?(int)$_REQUEST['room_id']:-1;
		$sandogh_id = (int)$_REQUEST['sandogh_id'];
		$user_id = (int)$_SESSION['user_id'];
		$fields = jshowGrid_new::loadNewFeilds($_REQUEST);
		if($isFactor)
		{
			$si = new sandogh_item_class($fields['sandogh_item_id']);
			if($si->en ==1 )
				$fields['mablagh'] = $si->mablagh_det;
			$fields['mablagh'] = ((int)umonize($fields['mablagh'])==0)?$si->mablagh_det:umonize($fields['mablagh']);
		}
		else
		{
			$fields['tedad'] = 1;
			$fields['sandogh_item_id'] =  $sandogh_id ;
		}
		$fields['mablagh'] = abs(umonize($fields['mablagh']));
		unset($fields['id']);
		$fields['reserve_id'] = $reserve_id;
		$fields['room_id'] = $room_id;
		$fields['typ'] = (int)$_REQUEST['isFactor'];
		$fields['factor_shomare'] = $factor_shom;
		$fields['user_id'] = $user_id;
		$fields['tarikh'] = date("Y-m-d H:i:s");
		$fields['tedad'] = ($fields['tedad']==0)?1:$fields['tedad'];
		$fields['miz'] = $miz;
		$qu = jshowGrid_new::createAddQuery($fields);
		mysql_class::ex_sqlx("insert into `sandogh_factor` ".$qu['fi']." values ".$qu['valu']);
	}
	function loadUser($inp)
	{
		$us = new user_class($inp);
		return ($us->fname.' '.$us->lname);
	}
	function hdate($inp)
	{
		return audit_class::hamed_pdate($inp);
	}
	function loadMablagh($inp)
        {
                $out =$inp;
                return monize($out);
        }
	function edit($id,$field,$value)
	{
		if(isset($_REQUEST['sandogh_khadamat_id']) && (int)$_REQUEST['sandogh_khadamat_id']>0 && $field=='tedad')
		{
			$san_fac = new sandogh_factor_class($id);
			$khadamat_det_id = (int)$_REQUEST['sandogh_khadamat_id'];
			$sandogh_item_id = $san_fac->sandogh_item_id;
			//echo khadamat_det_front_class::getMaxTedad($khadamat_det_id,$sandogh_item_id);
			if($value<=khadamat_det_front_class::getMaxTedad($khadamat_det_id,$sandogh_item_id))
			{
				mysql_class::ex_sqlx("update `sandogh_factor` set `$field`='$value' where `id`=".$id);
				
				khadamat_det_front_class::setTedad_habibi($khadamat_det_id,$sandogh_item_id,$value);
			}
		}
		else
			mysql_class::ex_sqlx("update `sandogh_factor` set `$field`='$value' where `id`=".$id); 
	}
	$factor_shom = (isset($_SESSION['factor_shomare']))?(int)$_SESSION['factor_shomare']:-2;
	$isFactor = ((int)$_REQUEST['isFactor'] == 1)?TRUE:FALSE;
	$typ = (int)$_REQUEST['isFactor'];
	if($isFactor)
		$title = 'صدور فاکتور';
	else
		$title = 'صدور رسید';
	$reserve_id = (isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:-1;
	$room_id =  (isset($_REQUEST['room_id']))?(int)$_REQUEST['room_id']:-1;
	$sandogh_id = (int)$_REQUEST['sandogh_id'];
	$get_type = (int)$_REQUEST['get_type'];
	$user_id = (int)$_SESSION['user_id'];
	//var_dump($se->detailAuth('all'));
	//$canChange= (!sandogh_factor_class::isJaari($factor_shom) || !$se->detailAuth('all') )?FALSE:TRUE;
	$en = (isset($_REQUEST['canChange']))?(int)$_REQUEST['canChange']:-1;
	$canChange= (((sandogh_factor_class::isJaari($factor_shom) || $en==1 ) && $se->detailAuth('all') ) || $en==-1);
	$msg = '';
	$request_factor = ($factor_shom==-2 && isset($_REQUEST['factor_shomare']))?(int)isset($_REQUEST['factor_shomare']):$factor_shom;
	$khadamat_det_id = isset($_REQUEST['sandogh_khadamat_id']) ?(int)$_REQUEST['sandogh_khadamat_id']:-1;
	$isCopon = $khadamat_det_id>0;
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==1)
	{
		
		$ghimat = array();
		$toz_arr = array();
		$toz_total= '';
		$toz_arr1=array();
		$moeen_sandogh = new sandogh_class($sandogh_id);
		$moeen_sandogh_id = $moeen_sandogh->moeen_id;
		$moshtari_moeen = -1;
		if($moeen_sandogh_id>0)
		{
			if($get_type==1)
			{
				$moshtari_moeen = new room_class($room_id);
				if($moshtari_moeen->moeen_id<=0)
					$moshtari_moeen = $moshtari_moeen->getMoeenId();
				else
					$moshtari_moeen = $moshtari_moeen->moeen_id;
			}
			else if($get_type==-1 && $moeen_sandogh->can_cash)
				$moshtari_moeen = $moeen_sandogh->moeen_cash_id;
			if(!$isCopon)
			{
echo 'hh';
				if($moshtari_moeen>0 )
				{
					mysql_class::ex_sql("select (`mablagh`*`tedad`) as `ghimat` ,`toz`,`sandogh_item_id`,`tedad` from `sandogh_factor` where `mablagh`>0 and `factor_shomare`=$factor_shom ",$q);
					while($r = mysql_fetch_array($q))
					{
						$sandogh_item = new sandogh_item_class((int)$r['sandogh_item_id']);
						$ghimat[] = $r['ghimat'];
						$toz_tmp = ($isFactor)?' بابت فاکتور شماره  ':' بابت رسید شماره';
						$toz_arr[] = $toz_tmp.$factor_shom.' از صندوق '.$moeen_sandogh->name.'. '.$sandogh_item->name.' به تعداد '.$r['tedad'].' '.$r['toz'] ;
						$toz_arr1[]= $sandogh_item->name.' به تعداد '.$r['tedad'].' ';
					}
					//$toz_total = $factor_shom.' از صندوق '.$moeen_sandogh->name.' '.$sandogh_item->name.' به تعداد '.$r['tedad'].' ';
					$toz_total =$factor_shom.' از صندوق '.$moeen_sandogh->name.implode('و',$toz_arr1);
					$san = -1;
					$tedad = 0;
					if(count($ghimat)>0 && count($toz_arr)>0 && $isFactor )
					{
						$toz_total = 'بابت فاکتور شماره '.$toz_total;
						sandogh_factor_class::removeSanads($factor_shom);
						$moshtariorhotel_moeen = $moshtari_moeen ;
						if($khadamat_det_id>0)
						{
							$tkh_det = new khadamat_det_class($khadamat_det_id);
							$khad = new khadamat_class($tkh_det->khadamat_id);
							$hot = new hotel_class($khad->hotel_id);
							$moshtariorhotel_moeen = $hot->moeen_id;
						}
						if(sanadzan_class::sondoghFactor($moeen_sandogh_id,$moshtariorhotel_moeen,$ghimat,$toz_arr,$toz_total,$factor_shom,$user_id))
						{

							if($khadamat_det_id>0)
							{
								khadamat_det_front_class::setFactor($factor_shom,$khadamat_det_id);
								khadamat_det_front_class::setTedad($khadamat_det_id);
							}
							mysql_class::ex_sql("select `id`,`tedad` from `sandogh_factor` where `factor_shomare`=$factor_shom",$qu);
							if($row = mysql_fetch_array($qu))
								$tedad = $row["tedad"];
							mysql_class::ex_sqlx("update `sandogh_factor` set `en`=1,`user_id`=$user_id where `factor_shomare`=$factor_shom and `typ`=$typ");
							mysql_class::ex_sqlx("update `khadamat_det_front` set `tedad_used` = '$tedad' where `khadamat_det_id` = $khadamat_det_id");
							$request_factor = $factor_shom;
							unset($_SESSION['factor_shomare']);
							//if(!$se->detailAuth('all'))
							$canChange = FALSE;
echo 'h1';
							$msg = 'ثبت نهایی با موفقیت انجام گرفت';
						}
						else
							$msg = 'خطا در ثبت سند';
					}
					else if(count($ghimat)>0 && count($toz_arr)>0 && !$isFactor )
					{
						mysql_class::ex_sql("select `id`,`tedad` from `sandogh_factor` where `factor_shomare`=$factor_shom",$qu);
							if($row = mysql_fetch_array($qu))
								$tedad = $row["tedad"];
							mysql_class::ex_sqlx("update `khadamat_det_front` set `tedad_used` = '$tedad' where `khadamat_det_id` = $khadamat_det_id");
						$toz_total = 'بابت رسید شماره '.$toz_total;
						sandogh_factor_class::removeSanads($factor_shom);
						if(sanadzan_class::sondoghResid($moeen_sandogh_id,$moshtari_moeen,$ghimat,$toz_arr,$toz_total,$factor_shom,$user_id))
						{
							mysql_class::ex_sqlx("update `sandogh_factor` set `en`=1,`user_id`=$user_id  where `factor_shomare`=$factor_shom and `typ`=$typ");
							$request_factor = $factor_shom;
							unset($_SESSION['factor_shomare']);
							//if(!$se->detailAuth('all'))
							$canChange = FALSE;
echo 'h2';
							$msg = 'ثبت نهایی با موفقیت انجام گرفت';
						}

					}
					else
						$msg = 'خطا در اطلاعات ورودی';
				}
				else
					$msg = 'صندوق امکان فروش نقدی ندارد';
			}
			else
			{
				$factor_shom = (isset($_SESSION['factor_shomare']))?(int)$_SESSION['factor_shomare']:-2;
				mysql_class::ex_sql("select `id`,`tedad` from `sandogh_factor` where `factor_shomare`=$factor_shom",$qu);
				if($row = mysql_fetch_array($qu))
					$tedad = $row["tedad"];
				mysql_class::ex_sqlx("update `khadamat_det_front` set `tedad_used` = '$tedad' where `khadamat_det_id` = $khadamat_det_id");
				$msg = 'ثبت نهایی با موفقیت انجام گرفت';
			}
		}
		else
			$msg = 'صندوق فاقد حساب معین است';
	}
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']=='new')
	{
		$factor_shom = (isset($_SESSION['factor_shomare']))?(int)$_SESSION['factor_shomare']:$request_factor;
		mysql_class::ex_sqlx("delete from `sandogh_factor` where `en`=0 and `factor_shomare`=$factor_shom");
		unset($_SESSION['factor_shomare']);
		$msg="<script>window.location='sandogh_det.php?sandogh_id=$sandogh_id';</script>";
	}
	if(!isset($_REQUEST['mod_grid1']) && !isset($_REQUEST['mod']) && $isCopon)
	{
		$khad_det = khadamat_det_class::createCopon($sandogh_id,$reserve_id,$room_id,$user_id,$khadamat_det_id);
		//var_dump($khad_det);
	}
	$grid = new jshowGrid_new("sandogh_factor","grid1");	
	$grid->width = '95%';
	$grid->setERequest(array('sandogh_khadamat_id'=>$khadamat_det_id,'miz'=>isset($_REQUEST['miz']) ?(int)$_REQUEST['miz']:-1));
	$grid->index_width = '20px';
	$grid->divProperty = 'style="height:auto;"';
	$grid->showAddDefault = FALSE;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = null;
	if($isFactor)
	{
		$grid->columnHeaders[3] = 'خدمات/جنس';
		$grid->columnAccesses[3] = 0;
	}
	else
		$grid->columnHeaders[3] =null;
	$grid->columnLists[3] = loadSandogh_item($sandogh_id) ;
	$grid->columnHeaders[4] = 'توضیحات';
	if($isFactor)
	{
		$grid->columnHeaders[5] = 'تعداد';
		$grid->columnJavaScript[5] = "onkeypress='return numbericOnKeypress(event);'";
		$grid->columnAccesses[6] = ($canChange)?1:0;
	}
	else
		$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6] = $isCopon ? null :'مبلغ ریال';
	$grid->columnFunctions[6]="loadMablagh";
	$grid->columnCallBackFunctions[6] = "umonize";	
	$grid->columnAccesses[6] = ($canChange)?1:0;
	$grid->columnJavaScript[6] = 'onkeyup="monize(this);"';
	if($isFactor)
		$grid->columnHeaders[7] = 'شماره فاکتور';
	else
		$grid->columnHeaders[7] = 'شماره رسید';
	$grid->columnAccesses[7] = 0;
	$grid->columnHeaders[8] = null;
	$grid->columnHeaders[9] = null;
	$grid->columnHeaders[10] = 'کاربر ثبت کننده';
	$grid->columnFunctions[10] = 'loadUser';
	$grid->columnAccesses[10] = 0;
	$grid->columnHeaders[11] = 'تاریخ';
	$grid->columnFunctions[11] = 'hdate';
	$grid->columnAccesses[11] = 0;
	if($isFactor && $isCopon)
	{
		for($i = 0;$i < count($grid->columnHeaders);$i++)
			$grid->columnAccesses[$i] = 0;
		$grid->columnAccesses[5] = 1;
	}
	//$grid->columnHeaders[12] = null;
	$q = null;
	//----a در صورت عدم وجود مورد برای صندوق اجاز ثبت ندارد
	mysql_class::ex_sql("select count(`id`) as `cid` from `sandogh_item` where `sandogh_id` = $sandogh_id",$q);
	$noItem=FALSE;
	if($r = mysql_fetch_array($q))
		$noItem = (int)$r['cid']>0;
	$grid->canAdd = $canChange && !$isCopon && $noItem;
	$grid->canEdit = $canChange;
	$grid->editFunction = 'edit';
	$grid->canDelete = $canChange && !$isCopon;
	$script = "
		var isFactor = ".(($isFactor)?'true':'false')." ;
		if(document.getElementById('new_tedad') && document.getElementById('new_tedad').value=='' && isFactor)
		{
			alert('تعداد را وارد کنید');
			return (false);	
		}
		if(document.getElementById('new_mablagh') && document.getElementById('new_mablagh').value=='' && !isFactor )
		{
			alert('مبلغ را وارد کنید');
			return (false);	
		}
		";
	$grid->addButtonScript = $script;
	$grid->addFunction = 'add_item';
	$grid->showAddDefault = FALSE;
	$colspan = ($isFactor)?4:2;
	$colspan -= ((!$canChange)?1:0);
	$grid->footer = "<tr class='showgrid_row_even' ><td colspan='$colspan' >&nbsp;</td><td align='center'  id='jam_tedad' ></td><td align='center'  id='jam_mab' ></td><td colspan='3' ></td></tr>";
	$grid->intial();
	$factor_shom = (isset($_SESSION['factor_shomare']))?(int)$_SESSION['factor_shomare']:$request_factor;
	if(isset($khad_det))
	{
		$factor_shom = $khad_det['factor_shomare'];
		$_SESSION['factor_shomare'] = $factor_shom;
	}
	//echo " `factor_shomare`=$factor_shom and `typ`=$typ";
	$grid->whereClause = " `factor_shomare`=$factor_shom and `typ`=$typ";
//echo $grid->whereClause;
	if(isset($_REQUEST['miz']) && $isCopon)
		mysql_class::ex_sqlx("update `sandogh_factor` set `miz`=".$_REQUEST['miz']." where ".$grid->whereClause);
  	$grid->executeQuery();
    	$out = $grid->getGrid();
	$sum_mablagh = 0;
	$q = null;
	mysql_class::ex_sql("select sum(`mablagh`*`tedad`) as `jam` from `sandogh_factor` where `factor_shomare`=$factor_shom ",$q);
	if($r=mysql_fetch_array($q))
		$sum_mablagh = (int)$r['jam'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>

		</title>
		<script language="javascript" >
			function reset_factor()
			{
				document.getElementById('mod').value='new';
				document.getElementById('frm1').submit();
			}
			function loadJam()
			{
				var sum_mab =FixNums(monize2(<?php echo $sum_mablagh; ?>));
				var isFactor = <?php echo (($isFactor)?'true':'false');?>;
				document.getElementById('jam_tedad').innerHTML = '';
				if(document.getElementById('sum_tedad') && isFactor)
					document.getElementById('jam_tedad').innerHTML =FixNums(document.getElementById('sum_tedad').innerHTML);
				document.getElementById('jam_mab').innerHTML = sum_mab+' ریال';
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
		<center>
		<span id='tim' >test2
		</span>
		</center>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" >
			<br/>
			<br/>
			<?php	echo "<h2>$title</h2><br/>".$msg.'<br />';echo $out;?>
			<div style="general_div" >
				<form id="frm1" method="POST" >
					<input type="hidden" name="isFactor" id="isFactor" value="<?php echo $_REQUEST['isFactor']; ?>" >
					<input type="hidden" name="mod" id="mod" value="1" >
					<input type="hidden" name="shomare_factor" id="shomare_factor" value="<?php echo $request_factor; ?>" >
					<input type="submit" class="inp" value="ثبت نهایی" <?php echo ((!$canChange)?'style="display:none;"':''); ?> >
					<input type="button" class="inp" value="صدور جدید" onclick="reset_factor();" >
				</form>
				<?php 
				if(!$noItem)
					echo "به علت عدم وجود آیتم برای صندوق،ثبت غیر فعال است";
				?>
			</div>
		</div>
		<script language="javascript" >
			if(document.getElementById('new_factor_shomare'))
				document.getElementById('new_factor_shomare').style.display = 'none';	
			if(document.getElementById('new_user_id'))
				document.getElementById('new_user_id').style.display = 'none';
			if(document.getElementById('new_tarikh'))
				document.getElementById('new_tarikh').style.display = 'none';
			<?php if(!$isCopon) {?>
			loadJam();
			<?php } ?>
		</script>
	</body>

</html>
