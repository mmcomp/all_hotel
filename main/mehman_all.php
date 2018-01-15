<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadKeys($fkey)
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
	}
	function listOtagh($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `room` where `en` = 1 and `id`='$inp'",$qq);
		while($row = mysql_fetch_array($qq))
			$out = $row['name'];
		return $out;
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
	function loadNesbat()
	{
		$tmp = statics_class::loadByKey('نسبت');
		$out['سرگروه'] = '-1';
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadVorood($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`reserve_id`,`aztarikh` from `room_det` where `reserve_id`=$inp ",$q);
                if($r = mysql_fetch_array($q))
	               $out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r["aztarikh"])));
		return $out;
	}
	function loadKhorooj($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`reserve_id`,`tatarikh` from `room_det` where `reserve_id`=$inp ",$q);
                if($r = mysql_fetch_array($q))
	               $out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r["tatarikh"])));
		return $out;
	}
	function hpdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function hpdate1($inp)
	{
		$out = '';
		if ($inp != "0000-00-00 00:00:00")
			$out = audit_class::hamed_pdate($inp);
		else
			$out = 'میهمان خارج نشده است';
		return($out);
	}
	function hpdate_ez($inp)
	{
		$out = '';
		if ($inp != "0000-00-00")
			$out = audit_class::hamed_pdate($inp);
		else
			$out = '----';
		return($out);
	}
	function loadMobile($inp)
	{
		$out = '';
		if ($inp == '0000-00-00')
			$out = '---';
		else
			$out = $inp;
		return($out);
	}
	function hpdateback($inp)
	{
		return(audit_class::hamed_pdateBack(perToEnNums($inp)));
	}	
	function add_item()
	{
		$user = new user_class((int)$_SESSION['user_id']);
		$isAdmin = ($user->user=='mehrdad')?TRUE:FALSE;
		$fields = jshowGrid_new::loadNewFeilds($_REQUEST);
		$fields['reserve_id'] = hexdec($_REQUEST['reserve_id'])-10000;
		$reserve_id = $fields['reserve_id'];
		mysql_class::ex_sql("select `id`,`room_id`,`tatarikh` from `room_det` where `reserve_id`=$reserve_id order by `tatarikh` desc",$q);
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
		$qu = jshowGrid_new::createAddQuery($fields);
		mysql_class::ex_sqlx("insert into `mehman` ".$qu['fi']." values ".$qu['valu']);
		//echo "insert into `mehman` ".$qu['fi']." values ".$qu['valu'];
	}
	function edit_item($id,$field,$value)
	{
		if($field=='hazine' || $field=='hazine_extra')
			$value = umonize($value);
		if($field=='tt')
			$value = hpdateback($value);
		mysql_class::ex_sqlx("update `mehman` set $field='$value' where `id`=$id ");
	}
	if( isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else	
		$h_id = -1;
	$h_ids = array();
	mysql_class::ex_sql("select hotel_id from hotel_daftar where daftar_id = ".$_SESSION['daftar_id'],$q);
	while($r = mysql_fetch_assoc($q)){
		$h_ids[] = $r['hotel_id'];
	}
	$room_id = (isset($_REQUEST['room_id']))?$_REQUEST['room_id']:-1;
	$combo = "";
	$combo .= "<form name=\"selRoom\" id=\"selRoom\" method=\"GET\">";
	$combo .= "<select class='inp form-control' id=\"room_id\" name=\"room_id\" onchange=\"document.getElementById('selRoom').submit();\" style=\"width:auto;\">\n";
	$combo .= "<option selected='selected' value=\"-1\">\n";
        $combo .= "همه"."\n";
        $combo .= "</option>\n";
	mysql_class::ex_sql("select * from room where `hotel_id` in (".implode(',',$h_ids).") and `en`=1 order by name",$q);
        while($r = mysql_fetch_array($q))
        {
		if((int)$r["id"]== (int)$room_id)
                        $select = 'selected="selected"';
                else
                        $select = "";
                $combo .= "<option value=\"".(int)$r["id"]."\" $select   >".$r["name"];
                $combo .= "</option>\n";
        }
        $combo .="</select>";
	$combo .= "</form>";	
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['aztarikh']," 00:00:00"):'0000-00-00');
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');
	$az = strtotime($aztarikh);
	$ta = strtotime($tatarikh);
	$isAdmin = $se->detailAuth('all');
	$is_admin =$isAdmin;
	$user_id=-1;
	$tmp = '';
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$tmp = room_det_class::loadReserve_id_habibi($aztarikh,$tatarikh,$room_id);
//var_dump ($tmp);
	if(($tmp!='') && ($room_id!=-1))
		$shart = " `reserve_id` in ($tmp) and `room_id`='$room_id' and `room_id`>0  order by `room_id`";
	elseif(($tmp!='') && ($room_id==-1))
		$shart = " `reserve_id` in ($tmp) and `room_id`>0  order by `room_id`";
	else
		$shart = "0=1 order by `room_id`";
	$out = '';
	$msg = 'میهمانی یافت نشد';
// echo $shart;	
//	$shart = '1=1 order by `reserve_id` ASC';
//	if(count($reserves)>0)
//	{
		$msg = '';
		$GLOBALS['msg'] = '';
		$user = new user_class((int)$_SESSION['user_id']);
		/*$grid = new jshowGrid_new("mehman","grid1");
		$grid->index_width = '20px';
		$grid->width = '100%';
		$grid->showAddDefault = FALSE;
		$grid->whereClause=$shart;
		$grid->columnHeaders[0] = null;			
		$grid->columnHeaders[1] = "شماره اتاق";
		$grid->columnFunctions[1] = "listOtagh";
		//$grid->columnFilters[1] = TRUE;
		//$grid->columnLists[1] = listOtagh();
		$grid->columnHeaders[2] = 'شماره رزرو';
		$grid->columnFilters[2] = TRUE;
		$grid->columnHeaders[3] = 'نام';
		$grid->columnFilters[3] = TRUE;
		$grid->columnHeaders[4] = 'نام  خانوادگی';
		$grid->columnFilters[4] = TRUE;
		$grid->columnHeaders[5] ='ساعت  ورود' ;
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
		$grid->columnLists[15]=loadMakan();
		$grid->columnHeaders[16] = 'کد‌ملی';
		$grid->columnHeaders[17] = 'نسبت';
		$grid->columnLists[17]=loadNesbat();
		$grid->columnHeaders[18] = 'تاریخ ازدواج';
		$grid->columnFunctions[18] = "hpdate_ez";
		$grid->columnHeaders[19] = 'موبایل';
		$grid->columnHeaders[20] = 'نام تور';
		$grid->columnHeaders[21] = 'پیش پرداخت';
		$grid->columnHeaders[22] = 'توضیحات';
		$grid->columnHeaders[23] = 'هزینه';
		$grid->columnJavaScript[23] ='onkeyup="monize(this);"';
		$grid->columnHeaders[24] = 'هزینه اضافی';
		$grid->columnJavaScript[24] ='onkeyup="monize(this);"';
		$grid->columnHeaders[25] = 'نفراضافه';
		$grid->columnHeaders[26] = 'خروج';
		$grid->columnFunctions[26] = 'hpdate1';
		$grid->addFeild('reserve_id',26);
		$grid->columnHeaders[26] = 'تاریخ ورود';
		$grid->columnFunctions[26] = 'loadVorood';
		$grid->addFeild('reserve_id',27);
		$grid->columnHeaders[27] = 'تاریخ خروج';
		$grid->columnFunctions[27] = 'loadKhorooj';
		$grid->pageCount = 500;	
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
		$grid->intial();
		$grid->executeQuery();
		$out = $grid->getGrid();*/
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>لیست کلیه مهمان ها</title>
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
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "yy/mm/dd"
                });
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
                $("#datepicker2").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "yy/mm/dd"
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
     
    </script>
    
	
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
				
                
                <div class="row" style="margin-right:0px;margin-left:0px;">
                <div class="col-lg-12" style="padding:0px;">
                    <div class="panel panel-default" style="border: 1px solid #ffae2e;">
                        <div class="panel-heading" style="background-color:#ffae2e;color:white;padding:1px;">
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-male"></i>لیست کلیه مهمان ها</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <form id='frm1'  method='GET' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">شماره اتاق:</label> 
                                    <div class="col-md-9"><?php echo $combo;?></div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">از تاریخ:</label> 
                                    <div class="col-md-9"><input id="datepicker1" type="text" name="aztarikh" class="form-control" value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>"></div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">تا تاریخ:</label> 
                                    <div class="col-md-9"><input id="datepicker2" type="text" name="tatarikh" class="form-control" value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>"></div>
                                </div>
                                <input type='hidden' name='h_id' id='h_id' value='<?php echo $h_id;?>' >
                                <input type='hidden' name='mod' id='mod' value='1' >
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left" onclick="send_search()">جستجو</button></div>
                                </div>
                            </div>
                          </form>
                            <?php echo $msg.'<br/>'.$GLOBALS['msg']; ?>
                            <div class="dataTable_wrapper" id="myTable" style="overflow-x:scroll">
                                <table style="width:3000px;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                            <th style="text-align:right;">شماره رزرو</th>
                                            <th style="text-align:right;">نام</th>
                                            <th style="text-align:right;">نام خانوادگی</th>
                                            <th style="text-align:right;">ساعت ورود</th>
                                            <th style="text-align:right;">نام پدر</th>
                                            <th style="text-align:right;">شماره شناسنامه</th>
                                            <th style="text-align:right;">تاریخ تولد</th>
                                            <th style="text-align:right;">جنسیت</th>
                                            <th style="text-align:right;">ملیت</th>
                                            <th style="text-align:right;">ص شناسنامه</th>
                                            <th style="text-align:right;">شغل</th>
                                            <th style="text-align:right;">دلیل سفر</th>
                                            <th style="text-align:right;">مبدا</th>
                                            <th style="text-align:right;">مقصد</th>
                                            <th style="text-align:right;">کد ملی</th>
                                            <th style="text-align:right;">نسبت</th>
                                            <th style="text-align:right;">ت ازدواج</th>
                                            <th style="text-align:right;">موبایل</th>
                                            <th style="text-align:right;">نام تور</th>
                                            <th style="text-align:right;">پیش پرداخت</th>
                                            <th style="text-align:right;">توضیحات</th>
                                            <th style="text-align:right;">هزینه</th>
                                            <th style="text-align:right;">هزینه اضافی</th>
                                            <th style="text-align:right;">نفر اضافی</th>
                                            <th style="text-align:right;">تاریخ ورود</th>
                                            <th style="text-align:right;">تاریخ خروج</th>
                                            <th style="text-align:right;">خروج</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
    <?php
$i=1;
         mysql_class::ex_sql("select * from `mehman` where $shart ",$s); 
 while ($ss = mysql_fetch_array($s))
	{
           $rid = $ss['room_id'];
           mysql_class::ex_sql("select `name` from `room` where `id` = '$rid'",$qqqq);
           while($row = mysql_fetch_array($qqqq))
            $rname = $row['name'];
     $gender = $ss['gender'];
           $gen="";
               if($gender==11)
                    $gen="مرد";
                if($gender==12)
                    $gen="زن";
     $meliat = $ss['melliat'];
           $mel="";
               if($meliat==2)
                    $mel="ایرانی";
                if($meliat==7)
                    $mel="غیرایرانی";
     $msodoors = $ss['ms'];
     mysql_class::ex_sql("select * from `statics` where `fkey`='شهر' and `id` = '$msodoors'",$ms);
     $rrr = mysql_fetch_array($ms);
     $mms = $rrr['fvalue'];
     
     $mab = $ss['mabda'];
     mysql_class::ex_sql("select * from `statics` where `fkey`='شهر' and `id` = '$mab'",$mabd);
     $rrrr = mysql_fetch_array($mabd);
     $mabda = $rrrr['fvalue'];
     
     $magh = $ss['maghsad'];
     mysql_class::ex_sql("select * from `statics` where `fkey`='شهر' and `id` = '$magh'",$mg);
     $rrrrr = mysql_fetch_array($mg);
     $magh = $rrrrr['fvalue'];
     
     $nes = $ss['nesbat'];
     mysql_class::ex_sql("select * from `statics` where `fkey`='نسبت' and `id` = '$nes'",$nesb);
     $rrrrrr = mysql_fetch_array($nesb);
     $nesba = $rrrrrr['fvalue'];
           if(fmod($i,2)!=0){
              echo "
               <tr class='odd'>
               <td>$i</td>
               <td>$rname</td>
               <td>$ss[reserve_id]</td>
               <td>$ss[fname]</td>
               <td>$ss[lname]</td>
               <td>$ss[vorood_h]</td>
               <td>$ss[p_name]</td>
               <td>$ss[ss]</td>
               <td>$ss[tt]</td>
               <td>$gen</td>
               <td>$mel</td>
               <td>$mms</td>
               <td>$ss[job]</td>
               <td>$ss[safar_dalili]</td>
               <td>$mabda</td>
               <td>$magh</td>
               <td>$ss[code_melli]</td>
               <td>$nesba</td>
               <td>$ss[t_ezdevaj]</td>
               <td>$ss[hamrah]</td>
               <td>$ss[toor_name]</td>
               <td>$ss[pish_pardakht]</td>
               <td>$ss[toz]</td>
               <td>$ss[hazine]</td>
               <td>$ss[hazine_extra]</td>
               <td>$ss[tedad_extra]</td>
               <td>$ss[vorood]</td>
               <td>$ss[khorooj]</td>
               <td></td>
               </tr>
        ";
            $i++;}
     else {echo "
               <tr class='even'>
               <td>$i</td>
               <td>$rname</td>
               <td>$ss[reserve_id]</td>
               <td>$ss[fname]</td>
               <td>$ss[lname]</td>
               <td>$ss[vorood_h]</td>
               <td>$ss[p_name]</td>
               <td>$ss[ss]</td>
               <td>$ss[tt]</td>
               <td>$gen</td>
               <td>$mel</td>
               <td>$mms</td>
               <td>$ss[job]</td>
               <td>$ss[safar_dalili]</td>
               <td>$mabda</td>
               <td>$magh</td>
               <td>$ss[code_melli]</td>
               <td>$nesba</td>
               <td>$ss[t_ezdevaj]</td>
               <td>$ss[hamrah]</td>
               <td>$ss[toor_name]</td>
               <td>$ss[pish_pardakht]</td>
               <td>$ss[toz]</td>
               <td>$ss[hazine]</td>
               <td>$ss[hazine_extra]</td>
               <td>$ss[tedad_extra]</td>
               <td>$ss[vorood]</td>
               <td>$ss[khorooj]</td>
               <td></td>
               </tr>
        ";
            $i++;}}
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
                
                <a target='_blank' href='amaken_list.php?h_id=<?php echo $h_id;?>&'><button class="btn btn-pink">لیست اماکن</button></a>
               
			</div>
		</div>
	</section>
	<!--/PAGE -->
    	<!-- Modal -->
   
    
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
    <script src="<?php echo $root ?>js/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-datepicker.fa.js"></script>
	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    
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
        
        function rakModal(rakId){
            StartLoading();
            var id=rakId;
            
            $.post("gaantinfo.php",{oid:id},function(data){
                StopLoading();
                $("#rk").html(data);
                $('#rak-modal').modal('show');             

                             });
        }
        function send_search()
		{
			if( trim(document.getElementById('room_id').value)=='' &&  trim(document.getElementById('datepicker6').value)=='' &&  trim(document.getElementById('datepicker7').value)=='')
			{
				alert('لطفا یکی از موارد را وارد کنید');
			}
			else
			{
				document.getElementById('mod').value= 2;
				document.getElementById('frm1').submit();
			}
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