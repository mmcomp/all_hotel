<?php
session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function hamed_pdateBack($inp)
	{
		$out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
                        $y=(int)$tmp[2];
                        $m=(int)$tmp[1];
                        $d=(int)$tmp[0];
                        if ($d>$y)
                        {
                                $tmp=$y;
                                $y=$d;
                                $d=$tmp;
                        }
                        if ($y<1000)
                        {
                                $y=$y+1300;
                        }
                        $inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out." 12:00:00";
	}
	function loadKalaTarkibi()
	{
		$out ='<select class="form-control inp" name="kala_cost" id="kala_cost" >';
		mysql_class::ex_sql("select `id`,`name` from `cost_kala` where `is_personal`=1 order by `name`",$q);
		while($r=mysql_fetch_array($q))
			$out .="<option value='".$r['id']."' >".$r['name']."</option>\n";
		$out .='</select>';
		return $out;
	}
	function loadAnbar()
	{
		$out = '<select class="form-control inp" name="anbar_id" id="anbar_id" >';
		mysql_class::ex_sql('select `name`,`id` from `anbar` where `en`<>2 order by `name`',$q);
		while($r = mysql_fetch_array($q))
			$out.= "<option  value='".$r['id']."' >".$r['name']."</option>\n";
		$out .='</select>';
		return($out);
	}
	function loadUsers()
	{
		$out = '<select class="form-control inp" name="gUser_id" id="gUser_id" >';
		mysql_class::ex_sql('select `lname`,`fname`,`id` from `user` where `user`<>\'mehrdad\' order by `lname`,`fname`',$q);
		while($r = mysql_fetch_array($q))
			$out.= "<option  value='".$r['id']."' >".$r['lname'].' '.$r['fname']."</option>\n";
		$out .='</select>';
		return($out);
	}
	function loadKhad($hotel_id)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `khadamat` where `en`=1 and `typ`=0 and `hotel_id`=$hotel_id",$q);
		while($r = mysql_fetch_array($q))
			$out.="<option value='".$r['id']."' >".$r['name']."</option>\n";
		return $out;
	}
	$out = '';
	$tarikh =((isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):date("Y-m-d"));
	$tmp = explode(' ',$tarikh);
	$tarikh = $tmp[0];
	if(isset($_REQUEST['hotel_id']) && $_REQUEST['hotel_id']!='')
	{
		$hotel_id = $_REQUEST['hotel_id'];
		$hotel = new hotel_class($hotel_id);
		$hotel_kh = new hotel_class($hotel_id);
		$out = '';
		if(($se->detailAuth('all') || $se->detailAuth('anbar_dari')) )
		{
			$disable = '';
			$pm = '';
			if($hotel_kh->ghaza_moeen_id<0)
			{
				$disable = 'disabled="disabled"';
				$pm = '<span style="color:red" >حساب معین هزینه غذا برای هتل ثبت نشده است</span>';
			}
			$out .="<div class=\"col-md-3\" style=\"margin-top:5px;\">
                                    <label class=\"col-md-4 control-label\">خروج از انبار</label> 
                                    <div class=\"col-md-8\">".loadAnbar()."</div>
                                </div><div class=\"col-md-3\" style=\"margin-top:5px;\">
                                    <label class=\"col-md-4 control-label\">تحویل گیرنده:</label> 
                                    <div class=\"col-md-8\">".loadUsers()."</div>
                                </div><div class=\"col-md-3\" style=\"margin-top:5px;\">
                                    <label class=\"col-md-4 control-label\">تعداد:</label> 
                                    <div class=\"col-md-8\"><input name='sum' id='sum' class='form-control inp'></div>
                                </div><div class=\"col-md-3\" style=\"margin-top:5px;\">
                                    <div class=\"col-md-12\">".loadKalaTarkibi()."</div>
                                </div><div class=\"col-md-3\" style=\"margin-top:5px;\">
                                    <div class=\"col-md-12\"><input type='button' value='چاپ رسید خروج از انبار' class='btn btn-info' $disable onclick='send_info();'>$pm</div>
                                </div>";
	
		}
	}

$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>خدمات هتل</title>
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

    <script type="text/javascript">
		function sbtFrm()
		{
			document.getElementById('frm1').submit();
		}
		function getPrint()
		{
			document.getElementById('div_main').style.width = '18cm';
			window.print();
			document.getElementById('div_main').style.width = 'auto';
		}
		function send_info()
		{
			if(parseInt(document.getElementById('sum').value,10)>0)
			{
				if(confirm('آیا کالا با جزئیات از انبار خارج شود؟'))
						document.getElementById('frm1').submit();
			}
			else
				alert('تعداد کالای خروجی را وارد کنید');
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-home"></i>خدمات هتل</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                            <div align="center" id="div_main" >
			<br/>
			<form id="frm1" method="GET" action="cost_anbar2.php">
				<div class="row col-md-12">
					<div class="col-md-4">
                                    <label class="col-md-3 control-label"><?php echo (isset($hotel))?$hotel->name:''; ?> :</label> 
                                    <div class="col-md-9"><select name="khadamat_id" id="khadamat_id" class="form-control inp">
								<?php echo loadKhad($hotel_id); ?>
							</select></div>
                                </div>
							 
						<div class="col-md-4">
                                    <label class="col-md-3 control-label">تاریخ :</label> 
                                    <div class="col-md-9">
								<input class="form-control inp" type="text" name="tarikh" id="datepicker1" value="<?php echo audit_class::hamed_pdate($tarikh); ?>"  >
							</div>
                                </div>
						</div>
                <input type="hidden" name="tarikh1" id="tarikh1" value="<?php echo audit_class::hamed_pdate($tarikh); ?>"  >
                <input type="hidden" name="hotel_id" id="hotel_id" value="<?php echo (isset($_REQUEST['hotel_id']))?$_REQUEST['hotel_id']:''; ?>"  >
						
						
                <div class="row col-md-12" >
				<?php echo $out;  ?>
                    </div>
                    
			</form>
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