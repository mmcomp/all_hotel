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
	$room_name1 = ((isset($_REQUEST['room_1']))?$_REQUEST['room_1']:-1);
	$room_name2 = ((isset($_REQUEST['room_2']))?$_REQUEST['room_2']:-1);	
	$r1 = ((isset($_REQUEST['r1']))?(int)$_REQUEST['r1']:'');
	$hotels = NULL;
	if($_SESSION['daftar_id']!=49){
		$hotels = daftar_class::hotelList($_SESSION['daftar_id']);
	}
	$combo = "";
	$combo .= "<form name=\"selRoom\" id=\"selRoom\" method=\"GET\">";
	$combo .= "
    <div class=\"col-md-3\" style=\"margin-bottom:5px;\">
                                    <label class=\"col-md-4 control-label\">اتاق اول:</label> 
                                    <div class=\"col-md-8\">
                                    <select class='form-control inp' id=\"room_1\" name=\"room_1\">";
	mysql_class::ex_sql("select `id`,`name` from `room` where (`vaziat` = 2 || `vaziat` = 0) and  `en`='1' ".(($hotels!=NULL)?' and hotel_id in ('.implode(',',$hotels).') ':'')."  order by `name`",$q);
        while($r = mysql_fetch_array($q))
        {
		if((int)$r["id"]== (int)$room_name1)
                {
                        $select = "selected='selected'";
                }
                else
                {
                        $select = "";
                }
                $combo .= "<option value=\"".(int)$r["id"]."\" $select   >";
                $combo .= $r["name"]."";
                $combo .= "</option>";
        }
        $combo .="</select>";
                                   $combo .= " </div>
                                </div>";
$combo .= "
    <div class=\"col-md-3\" style=\"margin-bottom:5px;\">
                                    <label class=\"col-md-4 control-label\">اتاق دوم:</label> 
                                    <div class=\"col-md-8\">
                                    <select class='form-control inp' id=\"room_2\" name=\"room_2\">";
	mysql_class::ex_sql("select `id`,`name` from `room` where (`vaziat` = 2 || `vaziat` = 0) and `en`='1' ".(($hotels!=NULL)?' and hotel_id in ('.implode(',',$hotels).') ':'')." order by `name`",$q);
        while($r = mysql_fetch_array($q))
        {
		if((int)$r["id"]== (int)$room_name2)
                {
                        $select = "selected='selected'";
                }
                else
                {
                        $select = "";
                }
                $combo .= "<option value=\"".(int)$r["id"]."\" $select   >";
                $combo .= $r["name"]."";
                $combo .= "</option>";
        }
        $combo .="</select>";
                                   $combo .= " </div>
                                </div>";
    
   
	
	//$combo .= "شماره رزرو:";
	$combo .= "<input type='text' id='r1' name='r1' style='display:none;' value='0'/>";
	$combo .= "<button id='sub' class=\"btn btn-info col-md-3 pull-left\" onclick=\"document.getElementById('selRoom').submit();\">جابجایی</button>";
	$combo .= "</form>";
	$room_id1 = -1;
	$room_id2 = -1;
	$jday = date("Y-m-d");
	$msg = '';
	$room_id1 = isset($_REQUEST['room_1'])?(int)$_REQUEST['room_1']:-1;
	$room_id2 = isset($_REQUEST['room_2'])?(int)$_REQUEST['room_2']:-1;
	if($room_id1>0)
	{
		$done = room_det_class::changeDate($room_id1,$room_id2,$jday,$r1);
		if($done)
			$msg = "alert('جابجایی با موفقیت انجام پذیرفت');opener.location=opener.location;";
		else
			$msg = "alert('جابجایی امکان پذیر نیست');";
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>جابجایی اتاق ها</title>
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
					document.getElementById('loading').style.display='';
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
				//document.getElementById('mod').value = 'change';
				document.getElementById('frm1').submit();
			}
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-exchange"></i>جابجایی اتاق ها</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <?php
                                echo $combo ;
                        ?>
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