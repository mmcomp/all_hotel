<?php
	session_start();
	include_once('../kernel.php');
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$is_admin = $se->detailAuth('all'); 
	$h_id = ((isset($_REQUEST['h_id']))?(int)$_REQUEST['h_id']:-1);	
	$r1 = ((isset($_REQUEST['r1']))?(int)$_REQUEST['r1']:'');
        $r2 = ((isset($_REQUEST['r2']))?(int)$_REQUEST['r2']:'');
	//echo "<br>r1=$r1<br/>r2=$r2";
	$reserver1 = '';
	$reserver2 = '';
	$azt1 = ((isset($_REQUEST['azt1']))?audit_class::hamed_pdateBack($_REQUEST['azt1']):'');
	$azt2 = ((isset($_REQUEST['azt2']))?audit_class::hamed_pdateBack($_REQUEST['azt2']):'');
	$tat1 = ((isset($_REQUEST['tat1']))?audit_class::hamed_pdateBack($_REQUEST['tat1']):'');
	$tat2 = ((isset($_REQUEST['tat2']))?audit_class::hamed_pdateBack($_REQUEST['tat2']):'');
	$jday = ((isset($_REQUEST['jday']))?audit_class::hamed_pdateBack($_REQUEST['jday']):date("Y-m-d H:i:s"));
	$mod =  ((isset($_REQUEST['mod']))?$_REQUEST['mod']:'');
	$room_id1 = ((isset($_REQUEST['room_id1']))?(int)$_REQUEST['room_id1']:-1);
	$room_id2 = ((isset($_REQUEST['room_id2']))?(int)$_REQUEST['room_id2']:-1);
	$room_name1 = '';
	$room_name2 = '';
	$can_change = FALSE;
	$mg = '';
	$msg = '';
	switch($mod)
	{
		case 'select':
			if($r1 >0)
			{
				$re1 = new hotel_reserve_class;
				$re1->loadByReserve($r1);
				$reserver1 = $re1->lname;
				$re1 = new room_det_class;
				$re1 = $re1->loadByReserve($r1);
				$azt1 = $re1[0][0]->aztarikh;
                	        $tat1 = $re1[0][0]->tatarikh;
			}
			if($r2 >0)
			{
	                        $re1 = new room_det_class;
        	                $re1 = $re1->loadByReserve($r2);
                	        $azt2 = $re1[0][0]->aztarikh;
                        	$tat2 = $re1[0][0]->tatarikh;
				$re1 = new hotel_reserve_class;
        	                $re1->loadByReserve($r2);
				$reserver2 = $re1->lname;
			}
			$room1 = new room_class($room_id1);
			$room_name1 = $room1->name;
			$room1 = new room_class($room_id2);
                        $room_name2 = $room1->name;
			if($room_name1 != '' && $room_name2 != '')
				$can_change = TRUE;
			break;
		case 'change':
			$done = room_det_class::changeDate($room_id1,$room_id2,$jday,$r1);
			if($done)
				$msg = "unchange();alert('جابجایی با موفقیت انجام پذیرفت');";
			else
				$msg = "alert('جابجایی امکان پذیر نیست');";
			break;
	}
        $d = ((isset($_REQUEST['d']))?$_REQUEST['d']:perToEnNums(jdate("m")));
        $month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
        $da = audit_class::hamed_pdateBack(jdate("Y/$d/d"));
        $tmp = explode(" ",$da);
        $da = $tmp[0];
        $hotel1 = new hotel_class($h_id);
	$hotel1->setRoomJavaScript = TRUE;
        $outvazeat = $hotel1->loadRooms($da,$is_admin,'select_reserve');
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>جابجایی</title>
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
    <link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
<!-- DataTables JavaScript -->
    <!-- JQUERY -->
<script src="<?php echo $root ?>js/jquery/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $root ?>datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/bootstrap-datepicker.fa.min.js"></script>
    
    <link type="text/css" href="<?php echo $root ?>window/css/jquery.window.css" rel="stylesheet" />

                <!-- JavaScript Includes -->
    <script type="text/javascript" src="../js/tavanir.js"></script>
    <script type="text/javascript" src="<?php echo $root ?>window/jquery.window.js"></script> 
    <script language="JavaScript">
			function addReserve(rid,room_id)
			{
				if(document.getElementById('r1').value == '' && document.getElementById('r2').value!=rid)
				{
					document.getElementById('r1').value = rid;
					document.getElementById('room_id1').value = room_id;
					document.getElementById('d1').style.display = '';
				}
				else if(document.getElementById('r2').value == '' && document.getElementById('r1').value!=rid)
				{
					document.getElementById('r2').value = rid;
                                        document.getElementById('room_id2').value = room_id;
					document.getElementById('d2').style.display = '';
				}
				if(parseInt(document.getElementById('room_id1').value,10)>0 && parseInt(document.getElementById('room_id2').value,10)>0)
					return(true);
				else
					return(false);
			}
			function select_reserve(rid,room_id)
			{
				if(addReserve(rid,room_id))
				{
					document.getElementById('loading2').style.display='';
					document.getElementById('frm1').submit();
				}
			}
			function unchange()
			{
				document.getElementById('r1').value = '';
				document.getElementById('r2').value = '';
                                document.getElementById('room_id1').value = '';
                                document.getElementById('room_id2').value = '';
				document.getElementById('d1').style.display = 'none';
				document.getElementById('d2').style.display = 'none';
				document.getElementById('mod').value = 'select';
				if(document.getElementById('sub'))
					document.getElementById('sub').style.display='none';
                                if(document.getElementById('toz'))
                                        document.getElementById('toz').style.display='none';
			}
			function change()
			{
				document.getElementById('mod').value = 'change';
				document.getElementById('frm1').submit();
			}
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-building"></i>جابجایی</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                             <?php
                                echo $outvazeat ;
                        ?>
                <?php echo (($can_change)?'<span id="toz" >جابجایی '.$reserver1.'</span>':''); ?>
                            <form id='frm1' method="post">
				<input type='hidden' id='h_id' name='h_id' value='<?php echo $h_id; ?>' /><br/>
				<input type='hidden' id='mod' name='mod' value='select' /><br/>
				<table width='100%'>
					<tr id='d1' style='display:<?php echo (($can_change)?'':'none');?>;'>
						<td>
							شماره رزرو اول : <input type='text' id='r1' name='r1' readonly='readonly' class='form-control inp' value='<?php echo $r1; ?>' />
						</td>
						<td style='display:<?php echo (($can_change)?'':'none');?>;'>
                                                        شماره اتاق اول : <input type='text' class='form-control inp' readonly='readonly' id='room_name1' value='<?php echo $room_name1; ?>' /><input type='hidden' id='room_id1' name='room_id1' readonly='readonly' class='form-control inp' value='<?php echo $room_id1; ?>' />
                                                </td>
						<td style='display:<?php echo (($can_change)?'':'none');?>;'>
							از تاریخ : <input type='text' id='azt1' name='azt1' readonly='readonly' class='form-control inp' value='<?php echo audit_class::hamed_pdate($azt1); ?>' />
						</td>
						<td style='display:<?php echo (($can_change)?'':'none');?>;'>
                	                                تا تاریخ : <input type='text' id='tat1' name='tat1' readonly='readonly' class='form-control inp' value='<?php echo audit_class::hamed_pdate($tat1); ?>' />
        	                                </td>
					</tr>
					<tr id='d2' style='display:<?php echo (($can_change)?'':'none');?>;'>
						<td>
							<input type='text' id='r2' name='r2' readonly='readonly' style='display:;' class='form-control inp' value='<?php echo $r2; ?>' />
							&nbsp;
						</td>
                                                <td style='display:<?php echo (($can_change)?'':'none');?>;'>
                                                        شماره اتاق دوم : <input type='text' class='form-control inp' id='room_name2' readonly='readonly' value='<?php echo $room_name2; ?>' /><input type='hidden' id='room_id2' name='room_id2' readonly='readonly' class='form-control inp' value='<?php echo $room_id2 ?>' />
                                                </td>
                                	        <td style='display:<?php echo (($can_change)?'':'none');?>;'>
                        	                        <input type='text' id='azt2' name='azt2' readonly='readonly' style='display:;' class='form-control inp' value='<?php echo audit_class::hamed_pdate($azt2); ?>' />
							&nbsp;
                	                        </td>
        	                                <td style='display:<?php echo (($can_change)?'':'none');?>;'>
	                                                <input type='text' id='tat2' name='tat2' readonly='readonly' style='display:;' class='form-control inp' value='<?php echo audit_class::hamed_pdate($tat2); ?>' />
                                        	</td>
					</tr>
					<tr>
						<td colspan='4' align='center'>
							تاریخ جابجایی : <input type='text' id='jday' name='jday' readonly='readonly' class='form-control inp' value='<?php echo audit_class::hamed_pdate($jday); ?>' />
						</td>
					</tr>
					<tr id='loading2' style='display:none;'>
						<td colspan='4' align='center'>
							<img src="../class/wait.gif" width='30px' alt="در حال بروزرسانی"/>
						</td>
					</tr>
					<tr>
						<td colspan='4' align='center'>
				<?php
					if($can_change)
						echo "<input id='sub' type='button' value='جابجایی' onclick='change();' class='form-control inp'/>";
				?>
							<input type='button' value='جدید' onclick='unchange();' class='btn btn-info inp'/>
						</td>
					</tr>
				</table>
			</form>
                
			
                </div>
	
<script language="javascript">
			<?php echo $msg; ?>
		</script>

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
            
                $("#openw").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "جزئیات",
                width: 500,
                height: 150,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "gaantinfo.php?reserve_id="+res_id
        });
    });
            
            $("#loading").hide(); 
            App.setPage("gallery");  //Set current page
			//App.setPage("widgets_box");  //Set current page
			App.init(); //Initialise plugins and elements
			getofflist();
            
            
             $('#dataTables-example').DataTable({
                responsive: true
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