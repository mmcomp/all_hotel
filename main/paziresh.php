<?php
session_start();
include_once("../kernel.php");
if(!isset($_SESSION['user_id']))
    die(lang_fa_class::access_deny);
$se = security_class::auth((int)$_SESSION['user_id']);
$user = new user_class((int)$_SESSION['user_id']);
$isAdmin = ($user->user=='mehrdad')?TRUE:FALSE;
if(!$se->can_view)
    die(lang_fa_class::access_deny);
$root="";
$room_id = (isset($_GET['room_id']))?$_GET['room_id']:"";
$hotel_id = (isset($_GET['hotel_id']))?$_GET['hotel_id']:"";
$room_typ = (isset($_GET['room_typ']))?$_GET['room_typ']:"";
$reserve_id = hexdec($_REQUEST['reserve_id'])-10000;

   /* function loadKeys($fkey)
	{
		$out = '<select name="fkey" id="fkey" class="inp" onchange="frm_submit();" >';
		mysql_class::ex_sql("select `id`,`fkey` from `statics` group by `fkey`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = ($fkey==$r['fkey'])?'selected="selected"':'';
			$out .="<option $sel value='".$r['fkey']."' >".$r['fkey']."</option>\n";
		}
		$out .='</select>';
		return $out;
	}*/
	function listOtagh()
	{
		//$out = array();
		//$reserve_id = hexdec($_REQUEST['reserve_id'])-10000;
		mysql_class::ex_sql("select `room_id` from `room_det` where `reserve_id`=$reserve_id",$q);
		while($r = mysql_fetch_array($q))
	        {
			$room_id = $r["room_id"];
			mysql_class::ex_sql("select `name` from `room` where `id`=$room_id",$qq);
			while($row = mysql_fetch_array($qq))
			{
				$name_room = $row['name'];
				//$out[$name_room]= $room_id;
			}
		}
		//return $out;
	}
	function loadGender()
	{
		$tmp = statics_class::loadByKey('جنسیت');
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
	function loadMakan()
	{
		$tmp = statics_class::loadByKey('شهر');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadMakan1()
	{
		$tmp = statics_class::loadByKey('شهر');
		$out['مشهد']=1;
		return $out;
	}
	function loadNesbat()
	{
		$tmp = statics_class::loadByKey('نسبت');
		$out['سرگروه'] = '-1';
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function hpdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function hpdateback($inp)
	{
		return(audit_class::hamed_pdateBack(perToEnNums($inp)));
	}
	function hpdateback1($inp)
	{
		$out = '';
		if ($inp=='0000-00-00')
			$out = '';
		else
			$out = audit_class::hamed_pdateBack(perToEnNums($inp));
		return($out);
	}	
	function hpdate1($inp)
	{
		$out = '';
		if ($inp=='0000-00-00')
			$out = '';
		else
			$out = audit_class::hamed_pdate($inp);
		return($out);
	}
	function add_item()
	{
		$user = new user_class((int)$_SESSION['user_id']);
		$isAdmin = ($user->user=='mehrdad')?TRUE:FALSE;
		$fields = jshowGrid_new::loadNewFeilds($_REQUEST);
		$fields['reserve_id'] = hexdec($_REQUEST['reserve_id'])-10000;
		if((int)$fields['room_id']>0)
		{
			$reserve_id = $fields['reserve_id'];
			mysql_class::ex_sql("select `id`,`room_id`,`tatarikh` from `room_det` where `reserve_id`=$reserve_id and `room_id`=".(int)$fields['room_id']." order by `tatarikh` desc",$q);
		        while($r = mysql_fetch_array($q))
			        mysql_class::ex_sqlx("update `room` set `vaziat` = 0 where `id` = ".(int)$r['room_id']);
			unset($fields['id']);
			foreach($fields as $ss=>$value)
				if($value=='')
					unset($fields[$ss]);
			if(isset($fields['tt']))
				$fields['tt'] = hpdateback($fields['tt']);
			if(isset($fields['hazine']))
				$fields['hazine'] = umonize($fields['hazine']);
			if(isset($fields['hazine_extra']))
				$fields['hazine_extra'] = umonize($fields['hazine_extra']);
			$fields['vorood_h'] = date("h:i:s");
			$qu = jshowGrid_new::createAddQuery($fields);
			mysql_class::ex_sqlx("insert into `mehman` ".$qu['fi']." values ".$qu['valu']);	
		}
		else
				echo "<script>alert('شماره اتاق وارد نشده است');</script>";
	}
	function edit_item($id,$field,$value)
	{
		if($field=='hazine' || $field=='hazine_extra')
			$value = umonize($value);
		if($field=='tt')
			$value = hpdateback($value);
		if($field=='t_ezdevaj')
			$value = hpdateback($value);
		if($field == 'room_id')
		{
			mysql_class::ex_sql("select room_id from mehman where id = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$pre_room_id = $r['room_id'];
				mysql_class::ex_sql("update `room` set vaziat = 1 where id = $pre_room_id");
			}
			mysql_class::ex_sqlx("update `room` set vaziat = 0 where id = $value");
		}
		mysql_class::ex_sqlx("update `mehman` set $field='$value' where `id`=$id ");
	}	
	function statSelect($key){
		$tmp = statics_class::loadByKey($key);
		$rel = '<option value=""></option>';
		for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$rel .= "<option value='$id'>$val</option>";
		}
		return $rel;
	}
////////////////////////////
	$room_name = '';	
	$room_id = isset($_REQUEST['room_id']) ?(int)$_REQUEST['room_id']:-1;
	mysql_class::ex_sql("select `name` from `room` where `id`=$room_id",$qq);
	if($row = mysql_fetch_array($qq))
		$name_room = $row['name'];
	$reserve_tmp = isset($_REQUEST['reserve_id']) ?(int)$_REQUEST['reserve_id']:-1;
	$reserve_id = hexdec($reserve_tmp)-10000;
	/*if(isset($_REQUEST['name1']))
	{
		$num_room=$_REQUEST['name1'];
		$ma_sodor=$_REQUEST['name2'];
		$tour_name=$_REQUEST['name3'];
		$name=$_REQUEST['name4'];
		$job=$_REQUEST['name5'];
		$p_par=$_REQUEST['name6'];
		$lname=$_REQUEST['name7'];
		$d_safar=$_REQUEST['name8'];
		$cost=$_REQUEST['name9'];
		$h_enter=$_REQUEST['name10'];
		$sour=$_REQUEST['name11'];
		$ex_cost=$_REQUEST['name12'];
		$name_f=$_REQUEST['name13'];
		$des=$_REQUEST['name14'];
		$ex_person=$_REQUEST['name15'];
		$ss=$_REQUEST['name16'];
		$meli=$_REQUEST['name17'];
		//$tt=date(('Y-m-d'),strtotime($_REQUEST['name18']));
		$tt = audit_class::hamed_pdateBack($_REQUEST['name18']);
		$rel=$_REQUEST['name19'];
		$gender=$_REQUEST['name20'];
		$t_ezde=audit_class::hamed_pdateBack($_REQUEST['name21']);
		$nation=$_REQUEST['name22'];
		$mob=$_REQUEST['name23'];	
		$r_id=$_REQUEST['name24'];
		$res_id=$_REQUEST['name25'];
		$toz=$_REQUEST['name26'];	
		$query=new mysql_class;
		$query->ex_sqlx("insert into `mehman`
				(`room_id`,`reserve_id`,`fname`,`lname`,`vorood_h`,`p_name`,`ss`,`tt`,`gender`,`melliat`,
				`ms`,`job`,`safar_dalil`,`mabda`,`maghsad`,`code_melli`,`nesbat`,`t_ezdevaj`,
				`hamrah`,`toor_name`,`pish_pardakht`,`toz`,`hazine`,`hazine_extra`,`tedad_extra`,`khorooj`) 
				values ('$r_id','$res_id','$name','$lname','$h_enter','$name_f','$ss','$tt',
				'$gender','$nation','$ma_sodor','$job','$d_safar','$sour','$des','$meli','$rel','$t_ezde','$ex_person',
				'$tour_name','$p_par','$toz','$cost','$ex_cost','$ex_person', '0000-00-00 00:00:00')");
		mysql_class::ex_sql("select `id`,`room_id`,`tatarikh` from `room_det` where
		`reserve_id`='$res_id' and `room_id`='$r_id'",$qur);
	        if($row2 = mysql_fetch_array($qur))
		{
			$room=$row2['room_id'];	 	
		        mysql_class::ex_sqlx("update `room` set `vaziat`=0 where `id` =$room");
			
		} 
		
		die("ok");
	}*/
//////////////////////
	$jensiat = "";
	$tmp = statics_class::loadByKey('جنسیت');	
	$jensiat .= "<select name='name20' id='id20' class='cl form-control' tabindex=\"8\">";
	$jensiat .= "<option value='-1' > </option>";
		for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$jensiat .= "<option value='$id'>$val</option>";
		}
	$jensiat .= "</select>";

	$meli="";
	$tmp = statics_class::loadByKey('ملیت');
	$meli.="<select name='name22' id='id22' class='cl form-control' tabindex='9'>";
	$meli .="<option value='-1'> </option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$meli .= "<option value='$id'>$val</option>";
		}
	$meli.="</select>";

	$sodor="";
	$tmp = statics_class::loadByKey('شهر');

	$sodor.="<select name='name2' id='id2' class='select2-01 col-md-12 full-width-fix' tabindex='7'>";
	$sodor .="<option value='-1'> </option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$sodor .= "<option value='$id'>$val</option>";
		}
	$sodor.="</select>";


	$sour="";
	$tmp = statics_class::loadByKey('شهر');
	$sour.="<select name='name11' id='id11' class='select2-01 col-md-12 full-width-fix' tabindex='13'>";
	$sour .="<option value='-1'> </option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$sour .= "<option value='$id'>$val</option>";
		}
	$sour.="</select>";

	$de="";
	$tmp = statics_class::loadByKey('شهر');
	$de.="<select name='name14' id='id14' class='select2-01 col-md-12 full-width-fix' tabindex='14'>";
	$de .="<option value='-1'></option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$de .= "<option value='$id'>$val</option>";
		}
	$de.="</select>";

	$rel="";
	$tmp = statics_class::loadByKey('نسبت');
	$rel.="<select name='name19' id='id19' class='select2-01 col-md-12 full-width-fix' tabindex='15'>";
	$rel .="<option value='-1'></option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$rel .= "<option value='$id'>$val</option>";
		}
	$rel.="</select>";

	if(isset($_REQUEST['reserve_id']))
	{		
		$reserve_id = hexdec($_REQUEST['reserve_id'])-10000;
/*
		if (isset($room_id))
			$shart = "`room_id`='$room_id' and `reserve_id`='$reserve_id'";
		else
*/
		$shart = "`reserve_id`='$reserve_id'";
		$khorooj= isset($_REQUEST['kh'])?(int)$_REQUEST['kh']:0;
		if($khorooj==1)
		{
			$user_id=(int)$_SESSION['user_id'];
			mehman_class::khorooj($reserve_id,$room_id,$user_id);
			
			$out = "<h2>خروج مهمان با موفقیت انجام شد</h2>";
		}
		else
		{
			$q = null;
			$now = date("Y-m-d 23:59:59");
			$now_delay =date("Y-m-d 00:00:00",strtotime($now.' -'.$conf->limit_paziresh_day.' day'));
			$is_available = FALSE;
			mysql_class::ex_sql("select `id` from `room_det` where `reserve_id`=$reserve_id and `aztarikh`>='$now_delay' and `aztarikh`<='$now' ",$q);
			if($r = mysql_fetch_array($q,MYSQL_ASSOC))
				$is_available = TRUE;
            /*
			$grid = new jshowGrid_new("mehman","grid1");
			$grid->width = '99%';
			$grid->index_width = '20px';
			$grid->showAddDefault = FALSE;
			$grid->whereClause = $shart.' order by `lname`';
			$grid->columnHeaders[0] = null;			
			$grid->columnHeaders[1] = "شماره اتاق";
			$grid->columnLists[1] = listOtagh();
			$grid->columnHeaders[2] = null;
			$grid->columnHeaders[3] = 'نام';
			$grid->columnHeaders[4] = 'نام  خانوادگی';
            $grid->columnHeaders[5] ='ساعت  ورود' ;
			$grid->columnAccesses[5] = 0;
			$grid->columnHeaders[6] = 'نام  پدر';
			$grid->columnHeaders[7] = 'شماره  شناسنامه';
			$grid->columnHeaders[8] = 'تاریخ  تولد';
			$grid->columnFunctions[8] = "hpdate";
			$grid->columnCallBackFunctions[8] = "hpdateback";
			$grid->columnHeaders[9] = 'جنسیت';
			$grid->columnLists[9]=loadGender();
			$grid->columnHeaders[10] = 'ملیت';
			$grid->columnLists[10]=loadMellait();
			$grid->columnHeaders[11] = 'محل‌صدور  شناسنامه';
			$grid->columnLists[11]=loadMakan();
			$grid->columnHeaders[12] = 'شغل';
			$grid->columnHeaders[13] = 'دلیل  سفر';
			$grid->columnHeaders[14] = 'مبدأ';
			$grid->columnLists[14]=loadMakan();
			$grid->columnHeaders[15] = 'مقصد';
			$grid->columnLists[15]=loadMakan1();
			$grid->columnHeaders[16] = 'کد‌ملی';
			$grid->columnHeaders[17] = 'نسبت';
			$grid->columnLists[17]=loadNesbat();
			$grid->columnHeaders[18] = 'تاریخ ازدواج';
			$grid->columnFunctions[18] = "hpdate1";
			$grid->columnCallBackFunctions[18] = "hpdateback1";
			$grid->columnHeaders[19] = 'موبایل';
			$grid->columnHeaders[20] = 'نام تور';
			$grid->columnHeaders[21] = 'پیش پرداخت';
			$grid->columnHeaders[22] = 'توضیحات';
			$grid->columnHeaders[23] = 'هزینه';
			$grid->columnJavaScript[23] ='onkeyup="monize(this);"';
			$grid->columnHeaders[24] = 'هزینه اضافی';
			$grid->columnJavaScript[24] ='onkeyup="monize(this);"';
			$grid->columnHeaders[25] = 'نفراضافه';
			$grid->columnHeaders[26] = null;			
			//$grid->sortEnabled = TRUE;
			$grid->hideIndex = 10;
			$b = !(reserve_class::isKhorooj($reserve_id,$room_id) && !$se->detailAuth('all')) && ($is_available || $se->detailAuth('all'));
			$grid->canEdit = $b;
			$grid->canAdd = FALSE;
			$grid->canDelete = $b;
			$grid->addFunction = 'add_item';
			$grid->editFunction = 'edit_item';
			$grid->intial();
		   	$grid->executeQuery();
			$out = $grid->getGrid();*/
		}
	}
	else
		$out ='خطا در اطلاعات';


?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>پذیرش</title>
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
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>js/select2/select2.min.css" />
<!-- DataTables JavaScript -->
    <!-- JQUERY -->
<script src="<?php echo $root ?>js/jquery/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $root ?>datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
    
    
	
	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
	
</head>
<body>
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
				
                    
                
                
                
                <div class="box border orange">
									<div class="box-title">
										<h4><i class="fa fa-list-alt"></i>فرم اطلاعات پذیرش</h4>
										<div class="tools" style="margin-left:10px;">
																						
											<a href="javascript:;" class="collapse">
												<i class="fa fa-chevron-up"></i>
											</a>
											
										</div>
									</div>
									<div class="box-body big">
										<form onsubmit="event.preventDefault();" class="form-horizontal row-border">
										  <div class="form-group col-md-12">
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">شماره اتاق:</label> 
											     <div class="col-md-9"><input tabindex="1" type="text" name="name1" value="<?php echo $name_room;?>" class="form-control"></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">نام:</label> 
											     <div class="col-md-9"><input tabindex="2" type="text" name="name4" class="form-control" ></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">نام خانوادگی:</label> 
											     <div class="col-md-9"><input tabindex="3" type="text" name="name7" class="form-control" ></div>
                                              </div>
										  </div>
                                            <div class="form-group col-md-12">
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">نام پدر:</label> 
											     <div class="col-md-9"><input  tabindex="4" type="text" name="name13" class="form-control" ></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">ش شناسنامه:</label> 
											     <div class="col-md-9"><input tabindex="5" type="text" name="name16" class="form-control" ></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">تاریخ تولد:</label> 
											     <div class="col-md-9"><input tabindex="6" id="datepicker1" type="text" name="name18" class="form-control" placeholder="1361/01/14"></div>
                                              </div>
										  </div>
                                            <div class="form-group col-md-12">
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">ص شناسنامه:</label> 
											     <div class="col-md-9"><?php echo $sodor; ?></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">جنسیت:</label> 
											     <div class="col-md-9"><?php echo $jensiat;?></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">ملیت:</label> 
											     <div class="col-md-9"><?php echo $meli;?></div>
                                              </div>
										  </div>
                                             <div class="form-group col-md-12">
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">کد ملی:</label> 
											     <div class="col-md-9"><input tabindex="10" type="text" name="name17" class="form-control" ></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">شغل:</label> 
											     <div class="col-md-9"><input tabindex="11" value="آزاد" type="text" name="name5" class="form-control" ></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">دلیل سفر:</label> 
											     <div class="col-md-9"><input tabindex="12" type="text" name="name8" class="form-control" ></div>
                                              </div>
										  </div>
                                            <div class="form-group col-md-12">
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">مبدا:</label> 
											     <div class="col-md-9">
                                                     
                                                     
												<!--<select id="input4" class="select2-01 col-md-12 full-width-fix" multiple="" size="5">-->
                                                    <?php echo $sour;?>
												   
												<!--</select>-->
												
                                                  </div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">مقصد:</label> 
											     <div class="col-md-9"><?php echo $de;?></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">نسبت:</label> 
											     <div class="col-md-9"><?php echo $rel;?></div>
                                              </div>
										  </div>
                                            <div class="form-group col-md-12">
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">موبایل:</label> 
											     <div class="col-md-9"><input tabindex="16" type="text" name="mobile" class="form-control" ></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">ت ازدواج:</label> 
											     <div class="col-md-9"><input tabindex="17" id="datepicker3" type="text" name="name21" class="form-control" placeholder="1361/01/14"></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">نام تور:</label> 
											     <div class="col-md-9"><input tabindex="18" value="ندارد" type="text" name="name3" class="form-control" ></div>
                                              </div>
										  </div>
                                            <div class="form-group col-md-12">
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">پیش پرداخت:</label> 
											     <div class="col-md-9"><input tabindex="19" value="0" type="text" name="name6" class="form-control" ></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">هزینه:</label> 
											     <div class="col-md-9"><input tabindex="20" value="0" type="text" name="name9" class="form-control" ></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">هزینه اضافی:</label> 
											     <div class="col-md-9"><input tabindex="21" value="0" type="text" name="name12" class="form-control" ></div>
                                              </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">نفر اضافی:</label> 
											     <div class="col-md-9"><input tabindex="22" value="0" type="text" name="name15" class="form-control" ></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">ساعت ورود:</label> 
											     <div class="col-md-9"><input tabindex="23" type="text" value="<?php echo(date("H:i:s"));?>" name="name10" class="form-control" disabled></div>
                                              </div>
                                              <div class="col-md-4">
											     <label class="col-md-3 control-label">توضیح:</label> 
											     <div class="col-md-9"><textarea tabindex="24" id="id26" rows="3" cols="5" name="name26" class="form-control" >ندارد</textarea></div>
                                              </div>
                                            </div>
                                            <input type="hidden" name="name24" id="id24" class="cl" value="<?php echo $room_id;?>">
                                            <input type="hidden" name="name25" id="id25" class="cl" value="<?php echo $reserve_id;?>">
                                            <div class="col-md-3">
										          <button onclick="val();" class="btn btn-block btn-success">ذخیره</button>
                                            
                                            
                                            </div>
                                            <br/>
										</form>										
									</div>
								</div>
                
                <div class="row" style="margin-right:0px;margin-left:0px;">
                <div class="col-lg-12" style="padding:0px;">
                    <div class="panel panel-default" style="border: 1px solid #ffae2e;">
                        <div class="panel-heading" style="background-color:#ffae2e;color:white;padding:1px;">
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-male"></i>اطلاعات مهمان ها</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper" id="myTable" style="overflow-x:scroll">
                                <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                            <th style="text-align:right;">نام</th>
                                            <th style="text-align:right;">نام خانوادگی</th>
                                            <th style="text-align:right;">ساعت ورود</th>
                                            <th style="text-align:right;">نام پدر</th>
                                            <th style="text-align:right;">شماره شناسنامه</th>
                                            <th style="text-align:right;">تاریخ تولد</th>
                                            <th style="text-align:right;">جنسیت</th>
                                            <th style="text-align:right;">عملیات</th>                                        
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
$out = hotel_class::getRack1($hotel_id,$room_typ,$se);
foreach ($out as $rooms){
    foreach ($rooms as $rak){
        if($rak['name']==$name_room){
            $mehmans=$rak['info'][0]['mehman'];
            if($mehmans){
                $i=1;
                foreach ($mehmans as $mehman){
// 										var_dump($mehman);
                    $mmid=$mehman->id;
                    $mmroom_id=$mehman->room_id;
                    mysql_class::ex_sql("select `name` from `room` where `id` = '$mmroom_id' ",$h_id);
                    $h_id1 = mysql_fetch_array($h_id);
                    $mmrrrname = $h_id1['name'];
                    $mmfname=$mehman->fname;
                    $mmlname=$mehman->lname;
                    $mmvh=$mehman->vorood_h;
                    $mmp_n=$mehman->p_name;
                    $mmss=$mehman->ss;
                    $mmtt=$mehman->tt;
                    $mmgender=$mehman->gender;
                    $gen="";
                    if($mmgender=="11")
                        $gen="مرد";
                    else if($mmgender=="12")
                        $gen="زن";
                    else
                        $gen="نامعلوم";
                    if(fmod($i,2)!=0){
                        echo "<tr class='odd'>
                                            <td>$i</td>
                                            <td>$mmrrrname</td>
                                            <td>$mmfname</td>
                                            <td>$mmlname</td>
                                            <td>$mmvh</td>
                                            <td>$mmp_n</td>
                                            <td>$mmss</td>
                                            <td>$mmtt</td>
                                            <td>$gen</td>
                                            <td><a onclick=\"editMehman('$mmid','$room_id','$mmfname','$mmlname','$mmp_n','$mmss','$mmtt','$mmgender','{$mehman->code_melli}','{$mehman->mobile}','{$mehman->mabda}','{$mehman->ms}','{$mehman->melliat}','{$mehman->gender}','{$mehman->nesbat}')\" data-toggle='modal' title=\"ویرایش\"><i style=\"color:green;font-size:18px;cursor:pointer\" class=\"fa fa-edit\"></i></a></td>

                                            
                                        </tr>";
                        $i++;
                    }
                    else{
                        echo"<tr class='even'>
                                            <td>$i</td>
                                            <td>$mmrrrname</td>
                                            <td>$mmfname</td>
                                            <td>$mmlname</td>
                                            <td>$mmvh</td>
                                            <td>$mmp_n</td>
                                            <td>$mmss</td>
                                            <td>$mmtt</td>
                                            <td>$gen</td>
                                            <td><a onclick=\"editMehman('$mmid','$room_id','$mmfname','$mmlname','$mmp_n','$mmss','$mmtt','$mmgender','{$mehman->code_melli}','{$mehman->mobile}','{$mehman->mabda}','{$mehman->ms}','{$mehman->melliat}','{$mehman->gender}','{$mehman->nesbat}')\" data-toggle='modal' title=\"ویرایش\"><i style=\"color:green;font-size:18px;cursor:pointer\" class=\"fa fa-edit\"></i></a></td>

                                            
                                        </tr>";
                        $i++;
                    }
                }
            }
            $rooms=$rak['info'][0]['reserve']->room;
            $roomss="";
            foreach ($rooms as $room){
                $roomss=$room->name."_".$room->id."|".$roomss;
                
            }
        }
    }
}
?>
                                       
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                          
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
    <!-- Modal edit (Long Modal)-->
<div class="modal fade" id="edit-guest-list">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			
			<div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
				<button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">ویرایش اطلاعات مهمان ها</h4>
			</div>
			<div class="modal-body" style="max-height:400px;overflow-y:scroll">
				 <form class="form-horizontal row-border" action="#">
					<input type="hidden" value="" name="mid" /> 
				  <div class="form-group col-md-12">
					<div class="col-md-4">
					<label class="col-md-4 control-label">نام:</label> 
					<div class="col-md-8"><input type="text" name="fname" class="form-control"></div>
					</div>
					<div class="col-md-4">
					<label class="col-md-4 control-label">نام خانوادگی:</label> 
					<div class="col-md-8"><input type="text" name="lname" class="form-control"></div>
					</div>
					<div class="col-md-4">
					<label class="col-md-4 control-label">نام پدر:</label> 
					<div class="col-md-8"><input type="text" name="pname" class="form-control"></div>
					</div>
				  </div> 
						 <div class="form-group col-md-12">
								 <div class="col-md-4">
					<label class="col-md-4 control-label">شماره اتاق:</label> 
					<div class="col-md-8" id="roomname"><select name='roomname' class='cl form-control'>
													<?php $rms = explode("|",$roomss);
															foreach ($rms as $rm){
																	$nameid = explode("_",$rm);
																	echo "<option value=".$nameid[1].">".$nameid[0]."</option>";
															}
													?>


													</select></div>
									</div> 
								 <div class="col-md-4">
					<label class="col-md-4 control-label">ش شناسنامه:</label> 
					<div class="col-md-8"><input type="text" name="ss" class="form-control"></div>
									</div>
									<div class="col-md-4">
					<label class="col-md-4 control-label">تاریخ تولد:</label> 
					<div class="col-md-8"><input id="datepicker2" type="text" name="tt" class="form-control" placeholder="1361/01/14"></div>
									</div>
						 </div> 
						<div class="form-group col-md-12">
							<div class="col-md-4">
								<label class="col-md-4 control-label">کدملی:</label> 
								<div class="col-md-8"><input type="text" name="code_melli" class="form-control"></div>
							</div>
							<div class="col-md-4">
								<label class="col-md-4 control-label">موبایل:</label> 
								<div class="col-md-8"><input type="text" name="mobile" class="form-control"></div>
							</div>
							<div class="col-md-4">
								<label class="col-md-4 control-label">مبدا:</label> 
								<div class="col-md-8"><select name="mabda" class="form-control"><?php echo statSelect('شهر'); ?></select></div>
							</div>
						</div> 
						<div class="form-group col-md-12">
							<div class="col-md-4">
								<label class="col-md-4 control-label">محل صدور شناسنامه:</label> 
								<div class="col-md-8"><select name="ss_sodur" class="form-control"><?php echo statSelect('شهر'); ?></select></div>
							</div>
							<div class="col-md-4">
								<label class="col-md-4 control-label">ملیت:</label> 
								<div class="col-md-8"><select name="meliat" class="form-control"><?php echo statSelect('ملیت'); ?></select></div>
							</div>
							<div class="col-md-4">
								<label class="col-md-4 control-label">جنسیت:</label> 
								<div class="col-md-8"><select name="gender" class="form-control"><option value="-1"> </option><option value="12">مؤنث</option><option value="11">مذکر</option></select></div>
							</div>
						</div> 
						<div class="form-group col-md-12">
							<div class="col-md-4">
								<label class="col-md-4 control-label">نسبت:</label> 
								<div class="col-md-8"><select name="nesbat" class="form-control"><?php echo statSelect('نسبت'); ?></select></div>
							</div>
					 	</div>
					</form>	
			 </div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                <button onclick="editFinal()" type="button" class="btn btn-warning">بروزرسانی</button>
			</div>
                </form>
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
	

	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    <script type="text/javascript" src="<?php echo $root ?>js/select2/select2.min.js"></script>
	<script type="text/javascript">
        $('#id11').select2();
        $('#id14').select2();
        $('#id19').select2();
        $('#id2').select2();
    </script>
    <script>
	
		var i=0;
		var SSmsg = null;
	

        
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
        
        function rakModal(rakId){
            StartLoading();
            var id=rakId;
            
            $.post("gaantinfo.php",{oid:id},function(data){
                StopLoading();
                $("#rk").html(data);
                $('#rak-modal').modal('show');             

                             });
        }
        function editFinal(){
            StartLoading();
            var room_id1 = $("div#roomname select").val();
            var fname1 = $("input[name='fname']").val();
             var mid1 = $("input[name='mid']").val();
            var lname1 = $("input[name='lname']").val();
            var pname1 = $("input[name='pname']").val();
            var ss1 = $("input[name='ss']").val();
            var tt1 = $("input[name='tt']").val();
            var gender1 = $("select[name='gender']").val();
						var code_melli = $("input[name='code_melli']").val();
						var mobile = $("input[name='mobile']").val();
						var mabda = $("select[name='mabda']").val();
						var ms = $("select[name='ss_sodur']").val();
						var meliat = $("select[name='meliat']").val();
						var nesbat = $("select[name='nesbat']").val();
            StopLoading();
						var pp = {
							omid1:mid1,
							oroom_id1:room_id1,
							ofname1:fname1,
							olname1:lname1,
							opname1:pname1,
							oss1:ss1,
							ott1:tt1,
							ogender1:gender1,
							code_melli:code_melli,
							mobile:mobile,
							mabda:mabda,
							ms:ms,
							meliat:meliat,
							nesbat:nesbat
						};
						console.log(pp);
            $.post("pazireshEditAjax.php",pp,function(data){
							console.log(data);
                StopLoading();
                if(data=="0"){
                    alert("اشکال در ثبت");
                }
                else if(data=="1"){
                    alert("ویرایش مهمان با موفقیت انجام شد");
                }
                else 
                    alert(data);
                location.reload();
            
            });
        }
        

	function StartLoading(){
        
        $("#loading").show();    
		
    }
    function StopLoading(){
        $("#loading").hide(); 
    }
					
        function editMehman(id,room_id,mmfname,mmlname,mmp_n,mmss,mmtt,mmgender,code_melli,mobile,mabda,ms,meliat,gender,nesbat){
            StartLoading();
            $("div#roomname select").val(room_id);
            $("input[name='mid']").val(id);
            $("input[name='fname']").val(mmfname);
            $("input[name='lname']").val(mmlname);
// 					console.log(code_melli,mobile,mabda,ms,meliat,gender,nesbat);
            $("input[name='code_melli']").val(code_melli);
						$("select[name='mabda']").val(mabda);
						$("select[name='ss_sodur']").val(ms);
// 						alert(meliat);
						$("select[name='meliat']").val(meliat);
						$("select[name='gender']").val(gender);
						$("select[name='nesbat']").val(nesbat);
            $("input[name='mobile']").val(mobile);
            $("input[name='pname']").val(mmp_n);
            $("input[name='ss']").val(mmss);
            $("input[name='tt']").val(mmtt);
            $("input[name='gender']").val(mmgender);
            StopLoading();
            $('#edit-guest-list').modal('show');
        }
function val()
		{
			StartLoading();

            var num_room = $("input[name='name1']").val();
            var ma_sodor=$("#id2 option:selected" ).val();
            var tour_name=$("input[name='name3']").val();
            var name=$("input[name='name4']").val();
            var job=$("input[name='name5']").val();
            var p_par=$("input[name='name6']").val();
            var lname=$("input[name='name7']").val();
            var d_safar=$("input[name='name8']").val();
            var cost=$("input[name='name9']").val();
            var h_enter=$("input[name='name10']").val();
            var sour=$("#id11 option:selected" ).val();
            var ex_cost=$("input[name='name12']").val();
            var name_f=$("input[name='name13']").val();
            var des=$("#id14 option:selected" ).val();
            var ex_person=$("input[name='name15']").val();
            var ss=$("input[name='name16']").val();
            var meli=$("input[name='name17']").val();
            //$tt=date(('Y-m-d'),strtotime($_REQUEST['name18']));
            var tt = $("input[name='name18']").val();
            var rel=$("#id19 option:selected" ).val();
            var gender=$("#id20 option:selected" ).val();
            var t_ezde=$("input[name='name21']").val();
            var nation=$("#id22 option:selected" ).val();
            var mob=$("input[name='mobile']").val();	
            var r_id=$("input[name='name24']").val();
            var res_id=$("input[name='name25']").val();
            var toz=$('textarea#id26').val();
            $.post("pazireshAjax.php",{onum_room:num_room,oma_sodor:ma_sodor,otour_name:tour_name,oname:name,ojob:job,op_par:p_par,olname:lname,od_safar:d_safar,ocost:cost,oh_enter:h_enter,osour:sour,oex_cost:ex_cost,oname_f:name_f,odes:des,oex_person:ex_person,oss:ss,omeli:meli,ott:tt,orel:rel,ogender:gender,ot_ezde:t_ezde,onation:nation,omob:mob,or_id:r_id,ores_id:res_id,otoz:toz},function(data){
								console.log(data);
                StopLoading();
                if(data=="0"){
                    $("#id2 option[value='-1']").attr('selected','selected');
                    $("input[name='name3']").val("ندارد");
                    $("input[name='name4']").val("");
                    $("input[name='name6']").val("0");
                    $("input[name='name7']").val("");
                    $("input[name='name8']").val("");
                    $("input[name='name9']").val("0");
                    $("#id11 option[value='-1']").attr('selected','selected');
                    $("input[name='name12']").val("0");
                    $("input[name='name13']").val("");
                    $("#id14 option[value='-1']").attr('selected','selected');
                    $("input[name='name15']").val("0");
                    $("input[name='name16']").val("");
                    $("input[name='name17']").val("");
                    $("input[name='name18']").val("");
                    $("#id19 option[value='-1']").attr('selected','selected');
                    $("#id20 option[value='-1']").attr('selected','selected');
                    $("input[name='name21']").val("");
                    $("#id22 option[value='-1']").attr('selected','selected');
                    $("input[name='mobile']").val("");	
                    $("input[name='name24']").val("");
                    $("input[name='name25']").val("");
                    $('textarea#id26').val("ندارد");
                    alert("اشکال در ثبت");
                    location.reload();
                }
                else if(data=="1"){
                    $("#id2 option[value='-1']").attr('selected','selected');
                    $("input[name='name3']").val("ندارد");
                    $("input[name='name4']").val("");
                    $("input[name='name6']").val("0");
                    $("input[name='name7']").val("");
                    $("input[name='name8']").val("");
                    $("input[name='name9']").val("0");
                    $("#id11 option[value='-1']").attr('selected','selected');
                    $("input[name='name12']").val("0");
                    $("input[name='name13']").val("");
                    $("#id14 option[value='-1']").attr('selected','selected');
                    $("input[name='name15']").val("0");
                    $("input[name='name16']").val("");
                    $("input[name='name17']").val("");
                    $("input[name='name18']").val("");
                    $("#id19 option[value='-1']").attr('selected','selected');
                    $("#id20 option[value='-1']").attr('selected','selected');
                    $("input[name='name21']").val("");
                    $("#id22 option[value='-1']").attr('selected','selected');
                    $("input[name='mobile']").val("");	
                    $("input[name='name24']").val("");
                    $("input[name='name25']").val("");
                    $('textarea#id26').val("ندارد");
                    alert("ثبت مهمان با موفقیت انجام شد");
                    location.reload();
                }
                else {
                    alert(data);
                location.reload();
                }
            
        }).fail(function(){
					console.log('FAILED');			
					StopLoading();
					alert('خطا در ارتباط با سرور');
				});
				//$.post("pazireshAjax.php",par,function(result){
				//	if (result=='ok')
				//	{
					//	alert('اطلاعات با موفقیت ثبت گردید');
				//		location.reload(); 
					//}
				//	else
					//	alert('ثبت اطلاعات با مشکل مواجه است');
						
					
			//	});

			
		}
		
	</script>
<script src="<?php echo $root ?>js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.fa.min.js"></script>
    
    
      <script>
    $(document).ready(function(){
        
        $("#loading").hide(); 
            App.setPage("gallery");  //Set current page
			//App.setPage("widgets_box");  //Set current page
        
			App.init(); //Initialise plugins and elements
        
			getofflist();
            
            $('#dataTables-example').DataTable({
                responsive: true
        });
    
    $("#datepicker0").datepicker();
            
//                 $("#datepicker1").datepicker({
//                     changeMonth: true,
//                     changeYear: true,
//                     dateFormat: "yy/mm/dd"
//                 }
                
                
//                 );
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
//                 $("#datepicker2").datepicker({
//                     changeMonth: true,
//                     changeYear: true,
//                     dateFormat: "yy/mm/dd"
//                 });
            		/*
                $("#datepicker3").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "yy/mm/dd"
                });
            		*/
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
     /*function frm_submit()
		{
			document.getElementById('frm1').submit();
		}
		*/
		
    </script>

	<?php include_once "footermodul.php"; ?>
	<!--/FOOTER -->
	

</body> 
</html>