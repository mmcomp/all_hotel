<?php
	session_start();
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
	$room_id=(isset($_REQUEST['factor_shomare_req']))?$_REQUEST['room_id']:-1;
	$item_id='';
	function loadSandogh_item($sandogh_item_id)
	{
		$out1 = "";
		if(mysql_class::ex_sql("select `id` , `name`,`mablagh_det` from `sandogh_item` where `id` = '$sandogh_item_id'",$ss)){
            $r = mysql_fetch_array($ss);
            $name = $r['name']; 
            $mablagh_det = $r['mablagh_det'];
            $out1 .="<select class='form-control' disabled><option value='".$r['id']."'>".$r['name'] . '(' . (((int)$r['en']==1)?'مبلغ قابل تغییر نیست ':'').monize($r['mablagh_det']) . ' ریال' . ')'."</option></select>";
        }
        else{
            $out1 .="<select class='form-control'><option value=''></option></select>";
            $out1.="";
        }
		
		return $out1;
	}
function loadSandogh_item1($sandogh_id)
	{
		$out1 = "";
        mysql_class::ex_sql("select `id` , `name`,`mablagh_det` from `sandogh_item` where `sandogh_id` = '$sandogh_id'",$ss);
		while($r = mysql_fetch_array($ss)){
            $name = $r['name']; 
            $mablagh_det = $r['mablagh_det'];
            $out1 .="<option value='".$r['id']."'>".$r['name'] . '(' . (((int)$r['en']==1)?'مبلغ قابل تغییر نیست ':'').monize($r['mablagh_det']) . ' ریال' . ')'."</option>";
        }
		
		return $out1;
	}
	function add_item()
	{
		if((int)$_SESSION['factor_shomare'] <= 0)
			$_SESSION['factor_shomare'] = sandogh_factor_class::getShomareFactor();
        $factor_shomare1 = sandogh_factor_class::getShomareFactor();
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
		//$fields['miz'] = $miz;
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
		$room_id =  (isset($_REQUEST['room_id']))?(int)$_REQUEST['room_id']:-1;
		if(isset($_REQUEST['sandogh_khadamat_id']) && (int)$_REQUEST['sandogh_khadamat_id']>0 && $field=='tedad')
		{
			$san_fac = new sandogh_factor_class($id);
			$khadamat_det_id = (int)$_REQUEST['sandogh_khadamat_id'];
			$sandogh_item_id = $san_fac->sandogh_item_id;
			$item_id=$sandogh_item_id;
			if($value<=khadamat_det_front_class::getMaxTedad($khadamat_det_id,$sandogh_item_id))
				mysql_class::ex_sqlx("update `sandogh_factor` set `$field`='$value' where `id`=".$id);
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
	if ((isset($_REQUEST['reserve_id']))&&($_REQUEST['reserve_id']!=-1))
		$reserve_id = (int)$_REQUEST['reserve_id'];
	elseif ((!isset($_REQUEST['reserve_id']))||($_REQUEST['reserve_id']==-1))
		$reserve_id = (isset($_REQUEST['mehman_list_n']))?(int)$_REQUEST['mehman_list_n']:-1;
	else
		$reserve_id = -1;
	//$reserve_id = (isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:-1;
	$room_id =  (isset($_REQUEST['room_id']))?(int)$_REQUEST['room_id']:-1;
	$sandogh_id = (int)$_REQUEST['sandogh_id'];
	$get_type = (int)$_REQUEST['get_type'];
	$user_id = (int)$_SESSION['user_id'];
	$en = (isset($_REQUEST['canChange']))?(int)$_REQUEST['canChange']:-1;
	$canChange= (((sandogh_factor_class::isJaari($factor_shom) || $en==1 ) && $se->detailAuth('all') ) || $en==-1);
	$msg = '';
	$request_factor = ($factor_shom==-2 && isset($_REQUEST['factor_shomare']))?(int)isset($_REQUEST['factor_shomare']):$factor_shom;
	$khadamat_det_id = isset($_REQUEST['sandogh_khadamat_id']) ?(int)$_REQUEST['sandogh_khadamat_id']:-1;
	$isCopon = $khadamat_det_id>0;
// echo $factor_shom;
// die();
	//echo("iscopon:$isCopon");
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==1)
	{
// 		var_dump($_REQUEST);
// 		echo "1";
// 		die();
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
			//die($moshtari_moeen);
			if(!$isCopon)
			{
				if($moshtari_moeen>0 )
				{
					$query = "select (`mablagh`*`tedad`) as `ghimat` ,`toz`,`sandogh_item_id`,`tedad` from `sandogh_factor` where `mablagh`>0 and `factor_shomare`=$factor_shom ";
// 					echo $query;
					mysql_class::ex_sql($query,$q);

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
// 					echo count($ghimat).'|'.count($toz_arr).'|'.var_export($isFactor,TRUE)."<br/>\n";
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
							//echo("select `id`,`tedad` from `sandogh_factor` where `factor_shomare`=$factor_shom");
							if($row = mysql_fetch_array($qu))
								$tedad = $row["tedad"];
							
							mysql_class::ex_sqlx("update `sandogh_factor` set `en`=1,`user_id`=$user_id where `factor_shomare`=$factor_shom and `typ`=$typ");
							mysql_class::ex_sqlx("update `khadamat_det_front` set `tedad_used` = '$tedad' where `khadamat_det_id` = $khadamat_det_id");
							$request_factor = $factor_shom;
							unset($_SESSION['factor_shomare']);
							//if(!$se->detailAuth('all'))
							$canChange = FALSE;
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
				
				/*$tedad = 0;
				$factor_shom = (isset($_SESSION['factor_shomare']))?(int)$_SESSION['factor_shomare']:-2;
				mysql_class::ex_sql("select `id`,`tedad` from `sandogh_factor` where `factor_shomare`=$factor_shom",$qu);
				if($row = mysql_fetch_array($qu))
					$tedad = $row["tedad"];
				mysql_class::ex_sqlx("update `khadamat_det_front` set `tedad_used` = '$tedad' where `khadamat_det_id` = $khadamat_det_id");*/
	mysql_class::ex_sqlx("update `sandogh_factor` set `en`=1,`user_id`=$user_id  where `factor_shomare`=$factor_shom and `typ`=$typ");
	$t_used = 0;
	mysql_class::ex_sqlx("DELETE FROM `sandogh_factor` WHERE (`en`='1' and `tedad`='0') or (`en`='0')");
	$query = "SELECT * FROM  `sandogh_factor` WHERE  `reserve_id` ='$reserve_id' AND  `room_id` ='$room_id' AND  `factor_shomare`='$factor_shom'";
// 	echo $query;
	mysql_class::ex_sql($query,$que3);
//echo("SELECT * FROM  `sandogh_factor` WHERE  `reserve_id` ='$reserve_id' AND  `room_id` ='$room_id' AND  `factor_shomare`='$factor_shom'");
		while($res3=mysql_fetch_array($que3))
		{	
			//mysql_class::ex_sqlx("INSERT INTO `sandogh_factor2` SELECT * FROM  `sandogh_factor` WHERE  `reserve_id` ='$reserve_id' AND  `room_id` ='$room_id' AND  `factor_shomare`='$factor_shom'");		
			$sid=$res3['sandogh_item_id'];
			$tedad_used_new=$res3['tedad'];
			
			mysql_class::ex_sql("SELECT * FROM `khadamat_det_front` WHERE `khadamat_det_id`='$khadamat_det_id' and  `sandogh_item_id`='$sid' and  `room_id`='$room_id'",$qutmp);
 //echo("<br>SELECT * FROM `khadamat_det_front` WHERE `khadamat_det_id`='$khadamat_det_id' and  `sandogh_item_id`='$sid' and  `room_id`='$room_id'");
			if($restmp=mysql_fetch_array($qutmp))
			{
				$ted_used_old=(int)$restmp['tedad_used'];
				$kol=(int)$restmp['tedad_kol'];
		
			//$ted_used_new=$kol-$tedad_remain;
			$tedad_used_kol=$tedad_used_new+$ted_used_old;
			if($kol>=$tedad_used_kol)
			mysql_class::ex_sqlx("UPDATE `khadamat_det_front` SET `tedad_used`='$tedad_used_kol'  WHERE `khadamat_det_id`='$khadamat_det_id' and `sandogh_item_id`='$sid' and `room_id`='$room_id'");

			else echo " خطا در اعمال";
 //echo("<br>UPDATE `khadamat_det_front` SET `tedad_used`='$tedad_used_kol'  WHERE `khadamat_det_id`='$khadamat_det_id' and `sandogh_item_id`='$sid' and `room_id`='$room_id'");
			}
		}
		
				$msg = 'ثبت نهایی با موفقیت انجام گرفت';
			}
		}
		else
			$msg = 'صندوق فاقد حساب معین است';
	}
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']=='new')
	{
		$factor_shom = (isset($_SESSION['factor_shomare']))?(int)$_SESSION['factor_shomare']:$request_factor;
		mysql_class::ex_sqlx("delete from `sandogh_factor` where `en`=0 and `factor_shomare`='$factor_shom' and `room_id`=$room_id");
		unset($_SESSION['factor_shomare']);
		$msg="<script>window.location='sandogh_det.php?sandogh_id=$sandogh_id';</script>";
	}
	if(!isset($_REQUEST['mod_grid1']) && !isset($_REQUEST['mod']) && $isCopon)
		$khad_det = khadamat_det_class::createCopon($sandogh_id,$reserve_id,$room_id,$user_id,$khadamat_det_id,$room_id);
	
	/*$grid = new jshowGrid_new("sandogh_factor","grid1");	
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
	$grid->whereClause = " `factor_shomare`=$factor_shom and `room_id`='$room_id' and `typ`=$typ";
	if(isset($_REQUEST['miz']) && $isCopon)
		mysql_class::ex_sqlx("update `sandogh_factor` set `miz`=".$_REQUEST['miz']." where ".$grid->whereClause);
  	$grid->executeQuery();
    	//$out = $grid->getGrid();*/
    $out='<div class="box border orange">
									
									<div class="box-body" style="overflow-x:scroll">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">خدمات / جنس</th>
												<th style="text-align:right">توضیحات</th>
                                                <th style="text-align:right">تعداد</th>
                                                <th style="text-align:right">مبلغ / ریال</th>
                                                <th style="text-align:right">شماره فاکتور</th>
                                                <th style="text-align:right">کاربر ثبت کننده</th>
                                                <th style="text-align:right">تاریخ</th>
											  </tr>
											</thead>
											<tbody>';
$query = "select * from `sandogh_factor` where `factor_shomare`='$factor_shom' and `room_id`='$room_id' and `typ`='$typ' ";
// echo $query;
    mysql_class::ex_sql($query,$ss);
    $i=1;
	while($r=mysql_fetch_array($ss)){
        $sandogh_item_id = $r['sandogh_item_id'];
        $toz = $r['toz'];
        $tedad = $r['tedad'];
        $mablagh = $r['mablagh'];
        $factor_shomare = $r['factor_shomare'];
        $user_id = $r['user_id'];
        mysql_class::ex_sql("select * from `user` where `id` = '$user_id' ",$h_id);
        $h_id1 = mysql_fetch_array($h_id);
        $fname = $h_id1['fname'];
        $lname = $h_id1['lname'];
        $uname = $fname." ".$lname;
        $tarikh = $r['tarikh'];
        $tar=jdate('Y/n/j',strtotime($tarikh));
        $out.="<tr>
        <td>".$i."</td>
        <td>".loadSandogh_item($sandogh_item_id)."</td>
        <td>".$toz."</td>
        <td>".$tedad."</td>
        <td>".$mablagh."</td>
        <td>".$factor_shomare."</td>
        <td>".$uname."</td>
        <td>".$tar."</td>
        </tr>";
        $i++;
    }
$out.="</tbody></table></div></div>";


	$sum_mablagh = 0;
	$q = null;
	mysql_class::ex_sql("select sum(`mablagh`*`tedad`) as `jam` from `sandogh_factor` where `factor_shomare`=$factor_shom ",$q);
	if($r=mysql_fetch_array($q))
		$sum_mablagh = (int)$r['jam'];
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>صدور فاکتور / رسید</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
	<meta name="description" content="" />
	<meta name="author" content="" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/cloud-admin.css" />
	<!-- Clock -->
	<link href="<?php echo $root ?>inc/digital-clock/assets/css/style.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?php echo $root ?>js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>js/colorbox/colorbox.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/animatecss/animate.min.css" />
    <!-- DataTables CSS -->
    <link href="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="<?php echo $root ?>datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">
<!-- DataTables JavaScript -->
    <!-- JQUERY -->
<script src="<?php echo $root ?>js/jquery/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $root ?>datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/bootstrap-datepicker.fa.min.js"></script>
    
      <script>
    $(document).ready(function(){
    
    $("#datepicker0").datepicker();
            
                $("#datepicker1").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                    
                });
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
                $("#datepicker2").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker3").datepicker({
                    numberOfMonths: 3,
                    showButtonPanel: true
                });
            
                $("#datepicker4").datepicker({
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker5").datepicker({
                    minDate: 0,
                    maxDate: "+14D"
                });
            
                $("#datepicker6").datepicker({
                    isRTL: true,
                    dateFormat: "d/m/yy"
                });                
        
        
    });
          function reset_factor()
			{
				document.getElementById('mod').value='new';
				document.getElementById('frm1').submit();
			}
			function FixNums(inp){
				return inp;
			}
			function monize2(inp){
				return inp;
			}
			function loadJam()
			{
// 				var sum_mab =FixNums(monize2(<?php echo $sum_mablagh; ?>));
// 				var isFactor = <?php echo (($isFactor)?'true':'false');?>;
// 				document.getElementById('jam_tedad').innerHTML = '';
// 				if(document.getElementById('sum_tedad') && isFactor)
// 					document.getElementById('jam_tedad').innerHTML =FixNums(document.getElementById('sum_tedad').innerHTML);
// 				document.getElementById('jam_mab').innerHTML = sum_mab+' ریال';
			}
			function prepare()
			{
				$(".prtcls").hide();
				window.print();
				
				
			}
     
    </script>
    
	
	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
	
</head>
<body>
  <?php 	
	$name = "--";
	$lname = "--";
	//echo $room_id;
	if ($room_id>0)
	{
		mysql_class::ex_sql("SELECT * FROM `mehman` WHERE `reserve_id`=$reserve_id and `room_id`=$room_id",$q);
		if($result=mysql_fetch_array($q))
		{
			$name=$result['fname'];
			$lname=$result['lname'];
		}
	}
	else
	{
		mysql_class::ex_sql("SELECT * FROM `hotel_reserve` WHERE `reserve_id`='$reserve_id' ",$q);
		if($result=mysql_fetch_array($q))
		{
			$name=$result['fname'];
			$lname=$result['lname'];
		}
	}
	mysql_class::ex_sql("SELECT * FROM `room` WHERE `id`=$room_id",$q2);		
	if($result2=mysql_fetch_array($q2))
		$room_name=$result2['name'];
	$str="res=$reserve_id&rom=$room_id&name=$name&lname=$lname";
	$m = 'وعده غذایی برای این ساعت در نظر گرفته نشده است';
	$today_time = date("H:i:s");
	mysql_class::ex_sql("select * from `vadeGhaza` where `azSaat`<'$today_time' and `taSaat`>'$today_time'",$q_va);
	while ($r_va = mysql_fetch_array($q_va))
		$m = $r_va['name'];

	/*
		$today_time = date("H");
		$m='';	
		if (($today_time>=7)&&($today_time<10))
			$m="صبحانه";
		elseif (($today_time>=13)&&($today_time<17))
			$m="ناهار";
		elseif (($today_time>=17)&&($today_time<24))
			$m="شام";
		else
			$m= "وعده غذایی برای این ساعت در نظر گرفته نشده است";*/
	
?>

    <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
	<!-- HEADER -->
	<?php include_once "headermodul.php"; ?>
	<!--/HEADER -->
	
	<!-- PAGE -->
	<section id="page">
			<!-- SIDEBAR -->
			<?php include_once "menubarmodul.php"; ?>
			<!-- /SIDEBAR -->
		<div id="main-content">
			<div class="container">
				
                
                <div class="row" style="margin-right:0px;margin-left:0px;">
                <div class="col-lg-12" style="padding:0px;">
                    <div class="panel panel-default" style="border: 1px solid #ffae2e;">
                        <div class="panel-heading" style="background-color:#ffae2e;color:white;padding:1px;">
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i><?php echo $title ?></h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                            <a href="#newG"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن مورد جدید</button></a>
                            <br/>

                                <center>
	<table   dir='rtl' width="100%" >
			
			<td id='tim' colspan='8' align='center'></td>
		<tr>
			<tr><br></tr>
			<td> <strong> نام و نام خانوادگی :  </strong></td>
			<td><?php echo $lname;  ?></td>
			<?php if($reserve_id>0){ ?>
			<td> <strong>شماره ی رزرو : </strong> </td>
			<td><?php echo $reserve_id;  ?></td>
			<?php if ($room_id>0)
				{
				echo "<td><strong>شماره اتاق : </strong></td><td>$room_name</td>";
				echo "<td><strong> وعده غذا: </strong></td><td>$m</td>";
				}
														 }
			?>
		</tr>
	</table>
	</center>
                            <?php	echo "".$msg.'<br />';echo $out;?>
                            <div style="general_div" >
                            <form id='frm1'  method='POST' >
                                
                                <input type="hidden" name="isFactor" id="isFactor" value="<?php echo $_REQUEST['isFactor']; ?>" >
                                <input type="hidden" name="mod" id="mod" value="1" >
                                <input type="hidden" name="shomare_factor" id="shomare_factor" value="<?php echo $request_factor; ?>" >
                                <input type="submit" class="btn btn-info col-md-2 pull-right prtcls"  value="ثبت نهایی" <?php  echo ((!$canChange)?'style="display:none;"':''); ?> >
					<!--<input type="button" class="inp prtcls" value="صدور جدید" onclick="reset_factor();" >-->
<?php 
	$name='';
	$lname='';
	$query = "SELECT * FROM `mehman` WHERE `reserve_id`=$reserve_id and `room_id`=$room_id";
	mysql_class::ex_sql($query,$q);
// 	echo $query;
	if($result=mysql_fetch_array($q))
	{
		$name=$result['fname'];
		$lname=$result['lname'];
	}
// 	die($lname);
	$str="res=$reserve_id&rom=$room_id&name=$name&lname=$lname";
?>
		<button onclick="prepare();" class="prtcls btn btn-success col-md-2 pull-left"><i class="fa fa-print"></i> چاپ</button>                          </form>
                               

                            <?php 
// 				if(!$noItem)
// 					echo "به علت عدم وجود آیتم برای صندوق،ثبت غیر فعال است";
				?> 
                            </div>
                        </div>
                        
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
        </div>
    </section>
	<!--/PAGE -->
    	<!-- Modal -->
    <!-- Modal : anbar modal -->
    <div class="modal fade" id="anbar-modal">
	
    </div>
			<!--/Modal : anbar modal-->
    
<div class="modal fade" id="newG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن مورد</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="factor" value="<?php echo $factor_shomare1 ?>" class="form-control" />
                        <input type="hidden" name="reserve_id" value="<?php echo $reserve_id ?>" class="form-control" />
                        <input type="hidden" name="room_id" value="<?php echo $room_id ?>" class="form-control" />
                        <input type="hidden" name="user_id" value="<?php echo $user_id ?>" class="form-control" />
                        <input type="hidden" name="isFactor" value="<?php echo $isFactor ?>" class="form-control" />
                        <div class="col-md-4">
                            <label>خدمات / جنس: </label>
                            <select name="khadamat1" id="khadamat1" class="form-control" >
                                <?php echo loadSandogh_item1($sandogh_id) ?>
                            </select>
                        </div>
												<?php if($reserve_id>0 || TRUE){ ?>
                        <div class="col-md-4">
                            <label>شماره رزرو : </label>
                            <input type="text" name="toz1" class="form-control" value="<?php echo ($reserve_id>0)?$reserve_id:'';  ?>" />
                        </div>
												<?php } ?>
                        <div class="col-md-4">
                            <label>تعداد: </label>
                            <input type="text" name="tedad1" class="form-control" />
                        </div>
                        <div class="col-md-4">
                            <label>مبلغ (ریال): </label>
                            <input type="text" name="cost1" class="form-control" />
                        </div>
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalG()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="editG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش مورد</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="gid" />
                        <div class="col-md-4">
                            <label>هتل: </label>
                           <select name="hotelName" id="hotelName" class="form-control">
                           
                          </select>
                        </div>
                        <div class="col-md-4">
                            <label>دفتر: </label>
                            <select name="DaftarName" id="DaftarName" class="form-control">
                            
                                </select>
                        </div>
                        <div class="col-md-4">
                            <label>طبقه: </label>
                            <select name="tabagheh" id="tabagheh" class="form-control">
                          
                            </select>
                        </div>
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalG()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف گارانتی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="gid" />
                        آیا از حذف مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalG()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
                </div>
            
        </div>
    </div>
</div>    
    
    
	<!-- FOOTER -->

    <!-- Loading -->
<div id="loading">
    <div class="container1">
	   <div class="content1">
        <div class="circle"></div>
        <div class="circle1"></div>
        </div>
    </div>
</div>    
	<!-- GLOBAL JAVASCRIPTS -->
	<?php include_once "inc/footinclude.php" ?>
	
	<!-- Clock -->
	<script src="<?php echo $root ?>inc/digital-clock/assets/js/script.js"></script>
	
	<!-- news ticker -->
	
	<!-- DATE RANGE PICKER -->
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/moment.min.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/daterangepicker.min.js"></script>
	
	<!-- DATE RANGE PICKER -->
    <script src="<?php echo $root ?>inc/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>inc/bootstrap-datepicker.fa.js"></script>
	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
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
	<script>
	
		var i=0;
		var SSmsg = null;
	
		jQuery(document).ready(function() {
            
            
            $("#loading").hide(); 
            App.setPage("gallery");  //Set current page
			//App.setPage("widgets_box");  //Set current page
			App.init(); //Initialise plugins and elements
			getofflist();
            
             $(document).ready(function() {
        $('#dataTables-example').DataTable({
                responsive: true
        });
        
       
        
    });
            
            
		});
        
		function aa(x){
            $.post("<?php echo $root ?>modules/CalendarGetOFFAjax.php",{},function (data){
                if(data){
                    i=data;
                }else{
                    alert("Error!");
                }
            });
        }
		
        function getofflist(){
            $("#cal-pr").html("<img align=\"middle\" class=\"img-responsive\" style=\"margin: auto;\" src=\"<?php echo $root ?>img/loaders/17.gif\">");
            $.post("<?php echo $root ?>modules/CalendarGetOFFAjax.php",{},function (data){
                if(data){
                    i=data;
                    $("#cal-pr").html("");
                    $("#cal-pr").datepicker({changeMonth: true});
                }else{
                    $("#cal-pr").html("<p class=\"fa fa-exclamation-circle text-danger\"> عدم برقراری ارتباط با پایگاه داده</p>");
                }
            });
        }
        
      function insertFinalG(){
          StartLoading();
          var khadamat1 = $("#khadamat1 option:selected").val();
          var toz1 = $("input[name='toz1']").val();
          var tedad1 = $("input[name='tedad1']").val();
          var cost1 = $("input[name='cost1']").val();
          var factor = $("input[name='factor']").val();
          var reserve_id = $("input[name='reserve_id']").val();
          var room_id = $("input[name='room_id']").val();
          var user_id = $("input[name='user_id']").val();
          var isFactor = $("input[name='isFactor']").val();
          $.post("sandogh_factorAjax.php",{khadamat1:khadamat1,toz1:toz1,tedad1:tedad1,cost1:cost1,factor:factor,reserve_id:reserve_id,room_id:room_id,user_id:user_id,isFactor:isFactor},function(data){
// 						console.log(data);
              StopLoading();
              if(data=="0")
                  alert("خطا در افزودن");
              if(data=="1"){
                  alert("افزودن با موفقیت انجام شد");
                  location.reload();
              }
          });
        }
        function editGfunc(gid,hotel_id,daftar_id,tabaghe){
            StartLoading();
            $("input[name='gid']").val(gid);
            $("#hotelName option[value="+hotel_id+"]").attr('selected','selected');
            $("#DaftarName option[value="+daftar_id+"]").attr('selected','selected');
            $("#tabagheh option[value="+tabaghe+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var hotelName = $("#hotelName option:selected" ).val();
            var DaftarName = $("#DaftarName option:selected" ).val();
            var tabagheh = $("#tabagheh option:selected" ).val();
            var gid = $("input[name='gid']").val();
           $.post("garanti_tabagheEditAjax.php",{hotelName:hotelName,DaftarName:DaftarName,tabagheh:tabagheh,gid:gid},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
            
        }
        function deleteGfunc(gid){
            StartLoading();
            $("input[name='gid']").val(gid);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var gid = $("input[name='gid']").val();
           $.post("garanti_tabagheDeleteAjax.php",{gid:gid},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در حذف");
               if(data=="1"){
                   alert("حذف با موفقیت انجام شد");
                   location.reload();
               }
                                          
           });
            
        } 

	function StartLoading(){
        
        $("#loading").show();    
		
    }
    function StopLoading(){
        $("#loading").hide(); 
    }
					


		
	</script>


	<?php include_once "footermodul.php"; ?>
	<!--/FOOTER -->
	

</body> 
</html>