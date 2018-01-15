<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function ppdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function  loadHotel($inp=-1)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return $out;
	}
	function loadRoom($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`name` from `room` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return $out;
	}
	function loadUser($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`fname`,`lname` from `user` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return $out;
	}	
	function loadNameByroom_id($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `name` from `room` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function loadStat($inp)
	{
		$out = "";
		if ($inp==1)
			$out = 'برطرف شده';
		else
			$out = 'برطرف نشده';
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
	function listOtagh($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `room` where `en` = 1 and `id`='$inp'",$qq);
		while($row = mysql_fetch_array($qq))
			$out = $row['name'];
		return $out;
	}
		function loadNameByReserve($res=-1)
	{
		$out = '';
		mysql_class::ex_sql("select `fname`,`lname` from `hotel_reserve` where `reserve_id` = $res",$q);
                if($r = mysql_fetch_array($q))
	        {
			$out = $r['fname'].' '.$r['lname'];
		}
		else
			$out = '--';
		return($out);
	}
	function loadNameByUser($user=-1)
	{
		$out = '';
		mysql_class::ex_sql("select `fname`,`lname` from `user` where `id` = $user",$q);
                if($r = mysql_fetch_array($q))
	        {
			$out = $r['fname'].' '.$r['lname'];
		}
		else
			$out = '--';
		return($out);
	}
	$msg = '';
	$isAdmin = $se->detailAuth('all');
	$is_admin =$isAdmin;
	$reserve_id = ((isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:0);
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');
	$tatarikh2 = ((isset($_REQUEST['tatarikh2']) && $_REQUEST['tatarikh2']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh2'],"23:59:59"):'0000-00-00');
	$user_id=-1;
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$curtime = strtotime(date("Y-m-d"));
	$ta = strtotime($tatarikh);
	$tatarikh = explode(" ",$tatarikh);
	$tatarikh = $tatarikh[0];
	$tatarikh2 = explode(" ",$tatarikh2);
	$tatarikh2 = $tatarikh2[0];
	$day = date("Y-m-d");
	$nafar = 0;
	$mablagh = 0;
	$mablagh_tmp = 0;
	$mablagh_kol = 0;
	
	$styl = 'class="showgrid_row_odd"';
	$co_room = 0;
	$sum_room = 0;
	$khorooj = '';
	$output ='';
	$rooms_id = '';
	$rooms_ids = '';
	
	isset($_REQUEST['room_names'])?$rm_names=$_REQUEST['room_names']:$rm_names=-1;
	$wer='';
	if( isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else
		$h_id = 1;
	$select="<select class='form-control' name='room_names'><option value=-1>همه </option> ";
	mysql_class::ex_sql("select `id`,`name` from `room` where `hotel_id`='$h_id'  order by `name` ",$qu);
	while($r = mysql_fetch_array($qu))
	{
		$room_name=$r['name'];
		$room_id=$r['id'];
		if($rm_names!=-1)
			if($room_id==$rm_names)
				$sel_def='selected=selected';
			else
				$sel_def='';
		else 
			$sel_def='';
		
		$select.="<option  value='$room_id' $sel_def>$room_name</option>";
	}		
	$select.="</select>";
	
	if ($h_id!=-1)
	{
		$rooms_id = "(";
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$h_id' order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$rooms_id .= $r["id"].',';
		}
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
	}
	if(isset($_REQUEST['rep']) && ((int)$_REQUEST['rep']==1) && ($rooms_ids!=""))
	{
		if ($rm_names!=-1)
			$room_shart = " `room_id`='".$rm_names."' and ";
		else
			$room_shart = "";
		$output = '
        <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">کاربر رزرو گیرنده </th>
                                            <th style="text-align:right;">نام </th>
                                            <th style="text-align:right;">شماره رزرو</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                            <th style="text-align:right;">تاریخ ورود</th>
                                            <th style="text-align:right;">تاریخ خروج</th>
                                            <th style="text-align:right;">ساعت ورود</th>
                                            <th style="text-align:right;">ساعت خروج</th>
                                            
                                            
                                        </tr>
                                    </thead><tbody>';
		if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
		{
			mysql_class::ex_sql("SELECT * FROM  `mehman` WHERE $room_shart DATE(`khorooj`) >='$tatarikh' and DATE(`khorooj`) <='$tatarikh2' order by `room_id`,`reserve_id`",$tmphelp);
			$den=0;
			$i = 1;
			$room = -1;
			while($r = mysql_fetch_array($tmphelp))
			{
				$row_style = 'class="odd"';
				$res_id = $r["reserve_id"];
				if ($res_id>0)
				{
					$reserve = new reserve_class($res_id);
					$reserve_user=new user_class($reserve->room_det[0]->user_id);
					$reserver_user =$reserve_user->fname.' '.$reserve_user->lname;
				}
				else
					$reserver_user = 'نامشخص';
				mysql_class::ex_sql("SELECT `aztarikh`,`tatarikh` FROM  `room_det` where `reserve_id`=$res_id",$tmphelp2);
				if($r2 = mysql_fetch_array($tmphelp2))
				{
					$den = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($r2['aztarikh'])));	
					$ta = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($r2['tatarikh'])));
				}
				$fname=$r['fname'];
				$lname=$r['lname'];
				$fulname=$fname .' ' .$lname;
				$vh=date(('H:i'),strtotime($r['khorooj']));
				$vorood_h=date(('H:i'),strtotime($r['vorood_h']));
				$r_id = $r['room_id'];
				mysql_class::ex_sql("select `name` from  `room` where `id`='$r_id'",$qname);
				if($row = mysql_fetch_array($qname))
					$room=$row['name'];
                $reserve=$r['reserve_id'];
				if($i%2==0)
					$row_style = 'class="even"';
				 $output .="
                    <tr $row_style>
                    <td>$reserver_user</td>
                    <td>$fulname</td>
                    <td>$reserve</td>
                    <td>$room</td>
                    <td>$den</td>
                    <td>$ta</td>
                    <td>$vorood_h</td>
                    <td>$vh</td>
                    </tr>
                    ";
				$i++;
			}
            
		
			$mablagh = monize($mablagh);
			$mablagh_kol = monize($mablagh_kol);
		
		}
        $output .="</tbody></table>";
	}
	elseif(isset($_REQUEST['rep']) && ((int)$_REQUEST['rep']==2) && ($rooms_ids!=""))
	{
		///nezafat
	$output = '
    <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">ردیف</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                            <th style="text-align:right;">کاربر ثبت کننده</th>
                                            <th style="text-align:right;">نام میهمان</th>
                                            <th style="text-align:right;">شماره رزرو</th>
                                            <th style="text-align:right;">تاریخ ثبت</th>
                                            <th style="text-align:right;">ساعت ثبت</th>
                                            <th style="text-align:right;">کاربر نظافت کننده</th>
                                            <th style="text-align:right;">تاریخ نظافت</th>
                                            <th style="text-align:right;">ساعت نظافت</th>
                                            <th style="text-align:right;">وضعیت</th>
                                            
                                        </tr>
                                    </thead><tbody>';
        
        
		mysql_class::ex_sql("select * from `nezafat` where date(`mani_time`)>= '$tatarikh' and date(`mani_time`)<='$tatarikh2' and `en`='1' order by `nezafat_time`,`en`",$q);
		$i =1;
		while($r = mysql_fetch_array($q))
		{
			$row_style = 'class="odd"';
			if($i%2==0)
				$row_style = 'class="even"';
			$res_id = $r['reserve_id'];
			if ($res_id==-1)
				$res_id = '--';
			$tmp = explode(" ",$r['mani_time']);
			$saat = $tmp[1];
			$room_name = loadNameByroom_id($r['room_id']);
			$name_mehman = loadNameByReserve($r['reserve_id']);
			$user_sabt = loadNameByUser($r['user_id']);
			$user_nezafat = loadNameByUser($r['user_nezafat']);
			$aztarikh_tb = audit_class::hamed_pdate($r['mani_time']);
			if ($r['nezafat_time']!='0000-00-00 00:00:00')
				$tarikh_nezafat = audit_class::hamed_pdate($r['nezafat_time']);
			else
				$tarikh_nezafat = '0000-00-00';
			$tmp_n = explode(" ",$r['nezafat_time']);
			$saat_n = $tmp_n[1];
			if ($r['en']==0)
				$stat = 'نظافت نشده';
			elseif ($r['en']==1)
				$stat = 'نظافت شده';
			else
				$stat = 'نامشخص';
			$output .="
            <tr $row_style>
            <td>$i</td>
            <td>$room_name</td>
            <td>$user_sabt</td>
            <td>$name_mehman</td>
            <td>$res_id</td>
            <td>$aztarikh_tb</td>
            <td>$saat</td>
            <td>$user_nezafat</td>
            <td>$tarikh_nezafat</td>
            <td>$saat_n</td>
            <td>$stat</td>
            </tr>";
			$i++;
		}
		
	$output .= '</tbody></table>';
	/////
	}
	else if(isset($_REQUEST['rep']) && (int)$_REQUEST['rep']==3 )
	{
		$rep = (int)$_REQUEST['rep'];
		if($rm_names<>-1)
			$wer='`room_id`='.$rm_names.' and ';
		$shart = "$wer DATE(`regdate`) >='$tatarikh' and DATE(`regdate`) <='$tatarikh2'";
        
        $output = '
    <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">نام هتل</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                            <th style="text-align:right;">کاربر ثبت کننده</th>
                                            <th style="text-align:right;">کاربر رفع کننده مشکل</th>
                                            <th style="text-align:right;">توضیح مشکل</th>
                                            <th style="text-align:right;">توضیح رفع مشکل</th>
                                            <th style="text-align:right;">تاریخ ثبت مشکل</th>
                                            <th style="text-align:right;">تاریخ رفع مشکل</th>
                                            <th style="text-align:right;">وضعیت</th>
                                            
                                        </tr>
                                    </thead><tbody>';
        
        
   
		mysql_class::ex_sql("select * from `tasisat_tmp` where $shart order by `room_id",$tas);
		$i =1;
		while($r = mysql_fetch_array($tas))
		{
			$row_style = 'class="odd"';
			if($i%2==0)
				$row_style = 'class="even"';
			$hotel_id = $r['hotel_id'];
            $room_id = $r['room_id'];
            $user_reg = $r['user_reg'];
            $user_fixed = $r['user_fixed'];
            $toz = $r['toz'];
            $toz_fix = $r['toz_fix'];
            $regdate = $r['regdate'];
            $date_fix = $r['date_fix'];
            $state = $r['en'];
            if($state==1)
                $state="برطرف شده";
            if($state==-1)
                $state="برطرف نشده";
			$output .="
            <tr $row_style>
            <td>$hotel_id</td>
            <td>$room_id</td>
            <td>$user_reg</td>
            <td>$user_fixed</td>
            <td>$toz</td>
            <td>$toz_fix</td>
            <td>$regdate</td>
            <td>$date_fix</td>
            <td>$state</td>
            </tr>";
			$i++;
		}
		
	$output .= '</tbody></table>';
	}
	if(isset($_REQUEST['rep']) && ((int)$_REQUEST['rep']==4) && ($rooms_ids!=""))
	{
		$rep = (int)$_REQUEST['rep'];
		if($rm_names<>-1)
			$wer='`room_id`='.$rm_names.' and ';
		$shart = "$wer DATE(`regdate`) >='$tatarikh' and DATE(`regdate`) <='$tatarikh2'";	
        
        $output = '
    <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">ردیف</th>
                                            <th style="text-align:right;width:1px;">نام هتل</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                            <th style="text-align:right;">شماره رزرو</th>
                                            <th style="text-align:right;">کاربر ثبت کننده</th>
                                            <th style="text-align:right;">کاربر رفع کننده مشکل</th>
                                            <th style="text-align:right;">توضیح مشکل</th>
                                            <th style="text-align:right;">توضیح رفع مشکل</th>
                                            <th style="text-align:right;">تاریخ ثبت مشکل</th>
                                            <th style="text-align:right;">تاریخ رفع مشکل</th>
                                            <th style="text-align:right;">وضعیت</th>
                                        </tr>
                                    </thead><tbody>';
        
        
        mysql_class::ex_sql("select * from `guest_req` where $shart order by `room_id",$gre);
        $i =1;
		while($r = mysql_fetch_array($gre))
		{
			$row_style = 'class="odd"';
			if($i%2==0)
				$row_style = 'class="even"';
			$hotel_id = $r['hotel_id'];
            $room_id = $r['room_id'];
            $reserve_id = $r['reserve_id'];
            $user_reg = $r['user_reg'];
            $user_fixed = $r['user_fixed'];
            $toz = $r['toz'];
            $toz_fixed = $r['toz_fix'];
            $regdate = $r['regdate'];
            $date_fixed = $r['date_fix'];
            $state = $r['en'];
            if($state==1)
                $state="برطرف شده";
            if($state==-1)
                $state="برطرف نشده";
			$output .="
            <tr $row_style>
            <td>$i</td>
            <td>$hotel_id</td>
            <td>$room_id</td>
            <td>$reserve_id</td>
            <td>$user_reg</td>
            <td>$user_fixed</td>
            <td>$toz</td>
            <td>$toz_fixed</td>
            <td>$regdate</td>
            <td>$date_fixed</td>
            <td>$state</td>
            </tr>";
			$i++;
		}
		
	$output .= '</tbody></table>';
        
	}
	else
		echo "";
	//$output .='</table>';
	$sel1 = "";
	$sel2 = "";
	$sel3 = "";
	$sel4 = "";
	if (isset($_REQUEST["rep"]))
	{
		if ($_REQUEST["rep"]==1)
			$sel1 = "selected=selected";
		elseif ($_REQUEST["rep"]==2)
			$sel2 = "selected=selected";
		elseif ($_REQUEST["rep"]==3)
			$sel3 = "selected=selected";
		elseif ($_REQUEST["rep"]==4)
			$sel4 = "selected=selected";
		else
		{
			$sel1 = "";
			$sel2 = "";
			$sel3 = "";
			$sel4 = "";
		}
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش اتاق ها</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-key"></i>گزارش اتاق ها</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <form id='frm1'  method='GET' >
                                <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                    <div class="col-md-2" style="margin-bottom:5px;">
                                        <label class="col-md-4 control-label">اتاق:</label> 
                                        <div class="col-md-8"><?php echo $select;?></div>
                                    </div>
                                    <div class="col-md-2" style="margin-bottom:5px;">
                                        <label class="col-md-4 control-label">از تاریخ:</label> 
                                        <div class="col-md-8"><input id="datepicker1" type="text" name="tatarikh" class="form-control" value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>"></div>
                                    </div>
                                    <div class="col-md-2" style="margin-bottom:5px;">
                                        <label class="col-md-4 control-label">تا تاریخ:</label> 
                                        <div class="col-md-8"><input id="datepicker2" type="text" name="tatarikh2" class="form-control" value="<?php echo ((isset($_REQUEST['tatarikh2']))?$_REQUEST['tatarikh2']:''); ?>"></div>
                                    </div>
                                    <div class="col-md-3" style="margin-bottom:5px;">
                                        <label class="col-md-4 control-label">فیلتر:</label> 
                                        <div class="col-md-8">
                                            <select class="form-control" name='rep'>
                                                <option value='1' <?php echo $sel1;?>>ساعت ورود/خروج</option>
                                                <option value='2' <?php echo $sel2;?>>ساعات نظافت</option>
                                                <option value='3' <?php echo $sel3;?>>مشکلات اتاق</option>				
                                                <option value='4' <?php echo $sel4;?>>درخواست های میهمان</option>	
                                            </select>
                                        </div>
                                    </div>
                                    <input type='hidden' name='h_id' id='h_id' value='<?php echo $h_id;?>' >
                                    <input type='hidden' name='mod' id='mod' value='1' >
                                    <div class="col-md-3" style="margin-bottom:5px;">
                                        <div class="col-md-12"><input type="button" value="جستجو" class="inp btn btn-info col-md-8 pull-left" onclick="send_search()" /></div>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="dataTable_wrapper" id="myTable" style="overflow-x:scroll">
                                
                               <?php echo $output.' '.$msg; ?>
                                
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
     <script src="<?php echo $root ?>js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.fa.min.js"></script>
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
			if((document.getElementById('datepicker1').value)=='')
			{
				alert('لطفا تاریخ را وارد کنید.');
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