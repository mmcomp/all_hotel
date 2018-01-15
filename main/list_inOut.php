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
	$output = '';
	$i = 1;
	$t_voroodi = 0;	
	$t_khorooji = 0;
	$day = date("Y-m-d");
	$month_late = date('Y-m-d', strtotime($day .' +30 day'));
	$rooms_id = "(";
	$rooms_ids = "";	
	$sum_v = 0;
	$sum_kh = 0;
	$stat_kol_day = 0;
	$sum_stat_m = 0;
	if(isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else
		$h_id = 1;
	if ($h_id!=-1) 
	{
		mysql_class::ex_sql("select `name` from `hotel` where `id`='$h_id'",$q);
		if($r = mysql_fetch_array($q))
			$hotel_name = $r['name'];
	}
	else
		$hotel_name = 'هتل انتخاب نشده است';
	if ($h_id!=-1)
	{
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$h_id' order by `name`",$q);
		while($r = mysql_fetch_array($q))
			$rooms_id .= $r["id"].',';
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
	}
	if ($rooms_ids!='')
		$room_shart = "`room_id` in ".$rooms_ids;
	else
		$room_shart = "1=1";

	$output = '<table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th class="sorting" style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">تاریخ</th>
                                            <th style="text-align:right;">تعداد ورودی</th>
                                            <th style="text-align:right;">تعداد خروجی</th>
                                            <th style="text-align:right;">وضعیت روز</th>
                                            <th style="text-align:right;">وضعیت کل</th>
                                            
                                        </tr>
                                    </thead><tbody>';
	while($day<=$month_late)
	{
		$day_jalali = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($day)));
		mysql_class::ex_sql("SELECT count(`id`) as `t_voroodi` FROM  `room_det` WHERE $room_shart and  DATE(`aztarikh`) ='$day' and `reserve_id`>0 order by `room_id`",$tmphelp);
		if($r = mysql_fetch_array($tmphelp))
			$t_voroodi = $r['t_voroodi'];
		mysql_class::ex_sql("SELECT count(`id`) as `t_khorooji` FROM  `room_det` WHERE $room_shart and DATE(`tatarikh`) ='$day' and `reserve_id`>0 order by `room_id`",$tmphelp);
		if($r = mysql_fetch_array($tmphelp))
			$t_khorooji = $r['t_khorooji'];
		$stat = (int)$t_voroodi - (int)$t_khorooji;
		$stat_kol_day = $stat_kol_day+$stat;
		if ($stat>0)
			$back_g = "style='background-color:#ec3b3d;'";
		elseif ($stat<0)
			$back_g = "style='background-color:#60de4e;'";
		else
			$back_g = "style='background-color:#ffffff;'";
		if ($stat_kol_day>0)
			$back_g_kol = "style='background-color:#ec3b3d;'";
		elseif ($stat_kol_day<0)
			$back_g_kol = "style='background-color:#60de4e;'";
		else
			$back_g_k = "style='background-color:#ffffff;'";
        if(fmod($i,2)!=0){
            $output .="<tr class='odd'>
                                            <td>$i</td>
                                            <td>$day_jalali</td>
                                            <td>$t_voroodi</td>
                                            <td>$t_khorooji</td>
                                            <td>$stat</td>
                                            <td>$stat_kol_day</td>
                                           
                                        </tr>";
            $i++;
        }
        else{
            $output .="<tr class='even'>
                                            <td>$i</td>
                                            <td>$day_jalali</td>
                                            <td>$t_voroodi</td>
                                            <td>$t_khorooji</td>
                                            <td>$stat</td>
                                            <td>$stat_kol_day</td>
                                           
                                        </tr>";
            $i++;
        }
		$sum_v = (int)$t_voroodi+$sum_v;
		$sum_kh = (int)$t_khorooji+$sum_kh;
		$sum_stat_m = $sum_stat_m + $stat_kol_day;
		$day = date('Y-m-d', strtotime($day .' +1 day'));
	}
	$stat_kol = (int)$sum_v - (int)$sum_kh;
	if ($stat_kol>0)
		$back_g = "style='background-color:#ec3b3d;'";
	elseif ($stat_kol<0)
		$back_g = "style='background-color:#60de4e;'";
	else
		$back_g = "style='background-color:#ffffff;'";
	if ($sum_stat_m>0)
		$back_g_sum = "style='background-color:#ec3b3d;'";
	elseif ($sum_stat_m<0)
		$back_g_sum = "style='background-color:#60de4e;'";
	else
		$back_g_sum = "style='background-color:#ffffff;'";
	$output .="<tr class='odd'><td>---</td><td>---</td><td>$sum_v</td><td>$sum_kh</td><td>$stat_kol</td><td>$sum_stat_m</td></tr> </tbody></table>";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>لیست ورودی و خروجی</title>
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
            
                $("#datepicker1").datepicker();
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
                $("#datepicker2").datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-sign-in"></i>لیست ورودی و خروجی <?php echo $hotel_name;?></h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                        
                          
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
	
	<!-- DATE RANGE PICKER -->
    <script src="<?php echo $root ?>inc/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>inc/bootstrap-datepicker.fa.js"></script>
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
            responsive: true,
            "order": [[ 1, "asc" ]]
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