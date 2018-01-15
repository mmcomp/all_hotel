<?php
$root="";
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(isset($_REQUEST['kick_user']))
	{
		$kick_user = (int)$_REQUEST['kick_user'];
		$ku = new user_class($kick_user);
		$ku->logout_user();
	}
	$today = date("Y-m-d H:i:s");	
	$str_time = strtotime($today); 
	$str_ago = $str_time - 600;
	$count_user = 0;
	$out_table = "
    <table class=\"table table-hover\">
                            <thead>
                                <tr>
                                    <th style=\"text-align:right\">ردیف</th>
                                    <th style=\"text-align:right\">نام خانوداگی</th>
                                    <th style=\"text-align:right\">دفتر</th>
                                    <th style=\"text-align:right\">خروج</th>
                                </tr>
                            </thead>
                            <tbody>
                                
    
   ";
//<table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;"><
        mysql_class::ex_sql("select `id`,`fname`,`daftar_id`,`online_date` from `user` where `user`!='mehrdad' ",$q);
	$style1_table = "showgrid_row_odd";
	$style2_table = "showgrid_row_even";
        while($r=mysql_fetch_array($q))
        {
        	$onDate=$r["online_date"];
		$str_onDate = strtotime($onDate);
		if (($str_onDate >= $str_ago) && ($str_onDate <= $str_time))
		{
			$temp = $style1_table;
			$style1_table = $style2_table;
			$count_user++;
			$name = $r["fname"];
                        $daftar = $r["daftar_id"];
			$temp_daftar = new daftar_class($daftar);
			$daftar_name = $temp_daftar->name;
			$out_table .= "
            <tr>
                                    <td>$count_user</td>
                                    <td>$name <u><span style=\"color:blue;cursor:pointer;display:none;\" onclick=\"document.getElementById('kick_user').value='".(int)$r['id']."';document.getElementById('frm1').submit();\">خروج</span></u></td>
                                    <td>$daftar_name</td>
                                    <td><button class=\"btn btn-danger\" onclick=\"document.getElementById('kick_user').value='".(int)$r['id']."';document.getElementById('frm1').submit();\"><i class=\"fa fa-times\"></i> خروج</button></td>
                                </tr>";

		}
        }
	$out_table .= "    </tbody>
                        </table>";

?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>تعداد کاربران آنلاین</title>
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
                <div class="col-md-2">
                    <a class="btn btn-danger btn-icon input-block-level">
                        <i class="fa fa-group fa-2x"></i>
                        <div>تعداد کاربران آنلاین</div>
                        <span class="label label-left label-primary"><?php echo $count_user;?></span>
                    </a>
                </div>
                <div class="col-md-2 pull-left">
                    <a class="btn btn-light-grey btn-icon input-block-level" onclick="window.location=window.location ;">
                        <i class="fa fa-repeat fa-2x"></i>
                        <div>بروزرسانی</div>
                    </a>
                </div>
                <div class="box border orange">
                    <div class="box-title">
                        <h4><i class="fa fa-male"></i>لیست کاربران آنلاین</h4>	  				
                    </div>
                    <div class="box-body" style="overflow-x:scroll">
                        
                        <?php echo $out_table ?>
                        <form id="frm1" method="post">
				<input type="hidden" id="kick_user" name="kick_user" value="" />
			</form>
                        
                    </div>
                </div>
			</div>
        </div>
    </section>
	<!--/PAGE -->
   

    
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