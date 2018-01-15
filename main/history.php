<?php
session_start();
include("../kernel.php");
if(!isset($_SESSION['user_id']))
    die(lang_fa_class::access_deny);
$se = security_class::auth((int)$_SESSION['user_id']);	
if(!$se->can_view)
    die(lang_fa_class::access_deny);
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
$combo_state = '';
$state_id = (isset($_REQUEST['state_id'])?$_REQUEST['state_id']:-1);
$combo_state .= "<form name=\"selState\" id=\"selState\" method=\"POST\">";
$combo_state .= "<select class='form-control inp' id=\"hotel_id\" name=\"state_id\" onchange=\"document.getElementById('selState').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
$select_all = ((int)$state_id==-1)?"selected='selected'":"";
$select1 = ((int)$state_id==1)?"selected='selected'":"";
$select2 = ((int)$state_id==2)?"selected='selected'":"";
$select3 = ((int)$state_id==3)?"selected='selected'":"";
$select4 = ((int)$state_id==4)?"selected='selected'":"";
$select5 = ((int)$state_id==5)?"selected='selected'":"";
$combo_state .= "<option value='-1' $select_all>همه</option>\n";
$combo_state .= "<option value='1' $select1>اشغال</option>\n";
$combo_state .= "<option value='2' $select2>نظافت</option>\n";
$combo_state .= "<option value='3' $select3>مشکلات تاسیساتی</option>\n";
$combo_state .= "<option value='4' $select4>مشکلات ثبت شده</option>\n";
$combo_state .= "<option value='5' $select5>درخواست های ثبت شده</option>\n";
$combo_state .= "</select>";
$combo_state .= "</form>";
$out_1 = '';
$out_2 = '';
$out_3 = '';
$out_4 = '';
if( isset($_REQUEST['room_id']))
    $room_id = $_REQUEST['room_id'];
else
    $room_id = -1;	
$i=1;
$room_name = 'اتاق شماره '.room_class::loadById($room_id);
$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['aztarikh'],"23:59:59"):'0000-00-00');
$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');	
///eshghal
$out_1 = '<div class="box border pink">
									<div class="box-title">
										<h4><i class="fa fa-lock"></i>اتاق در وضعیت اشغال</h4>
									
									</div>
									<div class="box-body">';
$out_1 .= '
<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">کاربر ثبت کننده</th>
												<th style="text-align:right">نام میهمان</th>
                                                <th style="text-align:right">شماره رزرو</th>
                                                <th style="text-align:right">از تاریخ</th>
                                                <th style="text-align:right">تا تاریخ</th>
                                                <th style="text-align:right">ساعت ورود</th>
                                                <th style="text-align:right">ساعت خروج</th>
                                                <th style="text-align:right">وضعیت</th>
											  </tr>
											</thead>
											<tbody>';
mysql_class::ex_sql("select `aztarikh`,`tatarikh`,`reserve_id`,`user_id` from `room_det` where date(`aztarikh`)>= '$aztarikh' and date(`tatarikh`)<='$tatarikh' and `room_id`='$room_id' order by `reserve_id`,`aztarikh`",$q);
while($r = mysql_fetch_array($q))
{
    $vorood_h = '';
    $khorooj = '';
    $res_id = $r['reserve_id'];
    $is_paziresh = reserve_class::isPaziresh($res_id,$room_id);
    if ($is_paziresh)
    {
        mysql_class::ex_sql("select `vorood_h`,`khorooj` from `mehman` where `reserve_id`='$res_id'",$q_mehman);
        while($r_mehman = mysql_fetch_array($q_mehman))
        {
            $vorood_h = $r_mehman['vorood_h'];
            $khorooj = $r_mehman['khorooj'];
        }
        $tmp = explode(" ",$khorooj);
        $ti_khorooj = $tmp[1];
        $row_style = 'class="odd"';
        if($i%2==0)
            $row_style = 'class="even"';
		      	
        $user_sabt = loadNameByUser($r['user_id']);
        $name_mehman = loadNameByReserve($res_id);
        $aztarikh_tb = audit_class::hamed_pdate($r['aztarikh']);
        $tatarikh_tb = audit_class::hamed_pdate($r['tatarikh']);
        $stat = 'اشغال';
        if ($res_id<0)
            $stat = 'کنسل شده';
        $out_1 .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$name_mehman</td><td>$res_id</td><td>$aztarikh_tb</td><td>$tatarikh_tb</td><td>$vorood_h</td><td>$ti_khorooj</td><td>$stat</td>	</tr>";
        $i++;
    }
}
$out_1 .= '</tbody></table></div></div>';
/////
$out_1 .= '<br/>'; 
///nezafat
$out_2 = '
<div class="box border orange">
									<div class="box-title">
										<h4><i class="fa fa-unlock-o"></i>اتاق در وضعیت نظافت</h4>
									
									</div>
									<div class="box-body">';
$out_2 .= '
<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">کاربر ثبت کننده</th>
												<th style="text-align:right">نام میهمان</th>
                                                <th style="text-align:right">شماره رزرو</th>
                                                <th style="text-align:right">تاریخ ثبت</th>
                                                <th style="text-align:right">ساعت ثبت</th>
                                                <th style="text-align:right">کاربر نظافت کننده</th>
                                                <th style="text-align:right">تاریخ نظافت</th>
                                                <th style="text-align:right">ساعت نظافت</th>
                                                <th style="text-align:right">وضعیت</th>
											  </tr>
											</thead>
											<tbody>';
mysql_class::ex_sql("select * from `nezafat` where date(`mani_time`)>= '$aztarikh' and date(`mani_time`)<='$tatarikh' and `room_id`='$room_id' order by `mani_time`,`en`",$q);
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
    $name_mehman = loadNameByReserve($r['reserve_id']);
    $user_sabt = loadNameByUser($r['user_id']);
    $user_nezafat = loadNameByUser($r['user_nezafat']);
    $aztarikh_tb = audit_class::hamed_pdate($r['mani_time']);			
    if ($r['nezafat_time']!='0000-00-00 00:00:00')
        $tarikh_nezafat = audit_class::hamed_pdate($r['nezafat_time']);
    else
        $tarikh_nezafat = '--';
    $tmp_n = explode(" ",$r['nezafat_time']);
    $saat_n = $tmp_n[1];
    if ($r['en']==0)
        $stat = 'نظافت نشده';
    elseif ($r['en']==1)
        $stat = 'نظافت شده';
    else
        $stat = 'نامشخص';
    $out_2 .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$name_mehman</td><td>$res_id</td><td>$aztarikh_tb</td><td>$saat</td><td>$user_nezafat</td><td>$tarikh_nezafat</td><td>$saat_n</td><td>$stat</td></tr>";
    $i++;
}
		
$out_2 .= '</tbody></table></div></div>';
	/////
$out_2 .= '<br/>'; 
///tamir
$out_3 = '
<div class="box border blue">
									<div class="box-title">
										<h4><i class="fa fa-wrench"></i>اتاق در وضعیت تعمیرات تاسیساتی</h4>
									
									</div>
									<div class="box-body">';
$out_3 .= '
<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">کاربر ثبت کننده</th>
												<th style="text-align:right">توضیحات</th>
                                                <th style="text-align:right">تاریخ ثبت</th>
                                                <th style="text-align:right">کاربر رفع کننده</th>
                                                <th style="text-align:right">توضیحات</th>
                                                <th style="text-align:right">تاریخ رفع خرابی</th>
                                                <th style="text-align:right">وضعیت</th>
											  </tr>
											</thead>
											<tbody>';
mysql_class::ex_sql("select * from `tasisat` where date(`regdate`)>= '$aztarikh' and date(`regdate`)<='$tatarikh' and `room_id`='$room_id' order by `regdate`",$q);
$i =1;
$user_sab = '--';
$user_answer = '--';
$toz = '--';
$answer = '--';
while($r = mysql_fetch_array($q))
{
    $row_style = 'class="odd"';
    if($i%2==0)
        $row_style = 'class="even"';
    if ($r['isFixed']=='1')
        $stat = 'بر طرف شده';
    else
        $stat = 'برطرف نشده';
    $user_sabt = loadNameByUser($r['user_reg']);
    $user_answer = loadNameByUser($r['user_answer']);	
    $toz = ($r['toz']!='')?$r['toz']:'--';
    $answer = $r['answer'];
    $tarikh_sabt = audit_class::hamed_pdate($r['regdate']);			
    if ($r['answerdate']!='0000-00-00 00:00:00')
        $answerdate = audit_class::hamed_pdate($r['answerdate']);
    else
        $answerdate = '--';
    $out_3 .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$toz</td><td>$tarikh_sabt</td><td>$user_answer</td><td>$answer</td><td>$answerdate</td><td>$stat</td></tr>";
    $i++;
}
		
$out_3 .= '</tbody></table></div></div>';
/////
$out_3 .= '<br/>'; 
///problem
$out_4 = '
<div class="box border inverse">
									<div class="box-title">
										<h4><i class="fa fa-gear"></i>اتاق در وضعیت مشکل دار</h4>
									
									</div>
									<div class="box-body">';
$out_4 .= '
<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">کاربر ثبت کننده</th>
												<th style="text-align:right">توضیحات</th>
                                                <th style="text-align:right">تاریخ ثبت</th>
                                                <th style="text-align:right">ساعت ثبت</th>
                                                <th style="text-align:right">کاربر رفع کننده مشکل</th>
                                                <th style="text-align:right">توضیحات</th>
                                                <th style="text-align:right">تاریخ رفع مشکل</th>
                                                <th style="text-align:right">ساعت رفع مشکل</th>
                                                <th style="text-align:right">وضعیت</th>
											  </tr>
											</thead>
											<tbody>';
mysql_class::ex_sql("select * from `tasisat_tmp` where date(`regdate`)>= '$aztarikh' and date(`regdate`)<='$tatarikh' and `room_id`='$room_id' order by `regdate`",$q);
$i =1;
while($r = mysql_fetch_array($q))
{
    $saat_answer = '--';
    $row_style = 'class="odd"';
    if($i%2==0)
        $row_style = 'class="even"';
    if ($r['en']=='1')
        $stat = 'بر طرف شده';
    else
        $stat = 'برطرف نشده';
    $user_sabt = loadNameByUser($r['user_reg']);
    if ($user_sab=='')
        $user_sab = '--';
    $user_answer = loadNameByUser($r['user_fixed']);
    if ($user_answer=='')
        $user_answer = '--';			
    $toz = $r['toz'];
    if ($toz=='')
        $toz = '--';
    $answer = $r['toz_fix'];
    if ($answer=='')
        $answer = '--';
    $tmp_reg = explode(" ",$r['regdate']);
    $saat_sabt = $tmp_reg[1];
    $tmp_ans = explode(" ",$r['date_fix']);
    $saat_answer = $tmp_ans[1];
    $tarikh_sabt = audit_class::hamed_pdate($r['regdate']);
    if ($r['date_fix']!='0000-00-00 00:00:00')
        $answerdate = audit_class::hamed_pdate($r['date_fix']);
    else
        $answerdate = '--';
    $out_4 .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$toz</td><td>$tarikh_sabt</td><td>$saat_sabt</td><td>$user_answer</td><td>$answer</td><td>$answerdate</td><td>$saat_answer</td><td>$stat</td></tr>";
    $i++;
}

$out_4 .= '</tbody></table></div></div>';
/////
$out_4 .= '<br/>'; 
///request
$out_5 = '
<div class="box border green">
									<div class="box-title">
										<h4><i class="fa fa-user"></i>درخواست های میهمان</h4>
									
									</div>
									<div class="box-body">';
$out_5 .= '

<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">کاربر ثبت کننده</th>
												<th style="text-align:right">توضیحات</th>
                                                <th style="text-align:right">تاریخ ثبت</th>
                                                <th style="text-align:right">ساعت ثبت</th>
                                                <th style="text-align:right">پاسخ دهنده به درخواست</th>
                                                <th style="text-align:right">توضیحات</th>
                                                <th style="text-align:right">تاریخ پاسخ به درخواست</th>
                                                <th style="text-align:right">ساعت پاسخ به درخواست</th>
                                                <th style="text-align:right">وضعیت</th>
											  </tr>
											</thead>
											<tbody>';
mysql_class::ex_sql("select * from `guest_req` where date(`regdate`)>= '$aztarikh' and date(`regdate`)<='$tatarikh' and `room_id`='$room_id' order by `regdate`",$q);
$i =1;
while($r = mysql_fetch_array($q))
{
    $saat_answer = '--';
    $row_style = 'class="odd"';
    if($i%2==0)
        $row_style = 'class="even"';
    if ($r['en']=='1')
        $stat = 'پاسخ داده شده';
    else
        $stat = 'پاسخ داده نشده';
    $user_sabt = loadNameByUser($r['user_reg']);
    if ($user_sab=='')
        $user_sab = '--';
    $user_answer = loadNameByUser($r['user_fixed']);
    if ($user_answer=='')
        $user_answer = '--';			
    $toz = $r['toz'];
    if ($toz=='')
        $toz = '--';
    $answer = $r['toz_fix'];
    if ($answer=='')
        $answer = '--';
    $tmp_reg = explode(" ",$r['regdate']);
    $saat_sabt = $tmp_reg[1];
    $tmp_ans = explode(" ",$r['date_fix']);
    $saat_answer = $tmp_ans[1];
    $tarikh_sabt = audit_class::hamed_pdate($r['regdate']);
    if ($r['date_fix']!='0000-00-00 00:00:00')
        $answerdate = audit_class::hamed_pdate($r['date_fix']);
    else
        $answerdate = '--';
    $out_5 .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$toz</td><td>$tarikh_sabt</td><td>$saat_sabt</td><td>$user_answer</td><td>$answer</td><td>$answerdate</td><td>$saat_answer</td><td>$stat</td></tr>";
    $i++;
}
		
$out_5 .= '</tbody></table></div></div>';
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>تاریخچه اتاق</title>
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

    
      <script>

          function send_search()
			{
			
				if((document.getElementById('datepicker1').value)=='')
				{
					alert('لطفا تاریخ را وارد کنید.');
				}
				else
				{
					document.getElementById('frm1').submit();
				}
			}
     
    </script>
    
	
	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
	
</head>
<body>
    <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
    <form method="POST" name="frmtedad" id ="frmtedad">
        <input name="txttedad" id="txttedad" type="hidden" value="1"/>
    </form>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>تاریخچه <?php echo $room_name;?></h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <form id='frm1'  method='post' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">وضعیت:</label> 
                                    <div class="col-md-9"><?php echo $combo_state;?></div>
                                </div>
                                 
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">از تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control inp" type="text" name="aztarikh" id="datepicker1" value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>"  >
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">تا تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control inp" type="text" name="tatarikh" id="datepicker2" value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>"  >
                                    
                                    </div>
                                </div>
                                <input type='hidden' name='mod' id='mod' value='1' >
                                <input type='hidden' name='state_id' id='state_id' value="<?php echo $state_id;?>" >	 
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-12" onclick="send_search();">جستجو</button></div>
                                </div>
                            </div>
                          </form>
<?php 
					if ($state_id==1)
						echo $out_1; 
					elseif ($state_id==2)
						echo $out_2;
					elseif ($state_id==3)
						echo $out_3;  
					elseif ($state_id==4)
						echo $out_4; 
					elseif ($state_id==5)
						echo $out_5; 
					elseif ($state_id==-1)
						echo $out_1.$out_2.$out_3.$out_4.$out_5; 
					else
						echo '';
				?>
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