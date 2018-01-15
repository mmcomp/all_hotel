<?php
session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
              die(lang_fa_class::access_deny);
        function loadAjans($daftar_id,$sel_aj)
        {
                $daftar_id = (int)$daftar_id;
		if($daftar_id>0) //not Admin
                	mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 and `saghf_kharid`>=".$conf->min_saghf_kharid." order by `name`",$q);
		else//isAdmin
			mysql_class::ex_sql("select `id`,`name` from `ajans`  where `moeen_id` > 0 and `saghf_kharid`>=".$conf->min_saghf_kharid." order by `name`",$q);
                while($r = mysql_fetch_array($q))
                {
                        $sel = (($r['id']==$sel_aj)?'selected="selected"':'');
                        $out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
                }
                return $out;
        }
	$msg= '';
	$daftar_id = ($se->detailAuth('all'))?-1:$_SESSION['daftar_id'];
	$ajans_id_bes = (isset($_REQUEST['ajans_id1']))?(int)$_REQUEST['ajans_id1']:-1;
	$ajans_id_bed = (isset($_REQUEST['ajans_id2']))?(int)$_REQUEST['ajans_id2']:-1;
	$ghimat = (isset($_REQUEST['ghimat']))?umonize($_REQUEST['ghimat']):0;
	$toz = (isset($_REQUEST['toz']))?$_REQUEST['toz']:'';
	$user_id = $_SESSION['user_id'];
	if(isset($_REQUEST['mod']) && $_REQUEST['mod'] == 'add')
	{
		$out = sanadzan_class::belitSanadzan2($ajans_id_bed,$ajans_id_bes,$user_id,$ghimat,$toz);
		$msg = '<h2 style="color:red;" >ثبت با موفقیت در سند شماره'.$out['shomare_sanad'].' انجام شد</h2>';
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>ثبت بلیط تک</title>
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
    <script type="text/javascript" src="../js/tavanir.js"></script>
    <script language="javascript">
			function addBelit()
			{
				var ghimat = parseInt(document.getElementById('ghimat').value,10);
				var toz = trim(document.getElementById('toz').value);
				if(document.getElementById('ajans_id1').options[document.getElementById('ajans_id1').options.selectedIndex].value== document.getElementById('ajans_id2').options[document.getElementById('ajans_id2').options.selectedIndex].value )
					alert('آژانسها مشابه انتخاب شده اند');
				else 
				{
					if(ghimat>0)
					{
						if(toz!='')
						{
							document.getElementById('mod').value = 'add';
							document.getElementById('frm1').submit();
						}
						else
							alert('توضیحات وارد نشده است');
					}
					else 
						alert('مبلغ را درست وارد کنید');
				}
			}
		</script>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-money"></i>ثبت بلیط تک</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <form id='frm1'  method='POST' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-3">
                                    <label class="col-md-6 control-label">آژانس فروشنده بلیت:</label> 
                                    <div class="col-md-6">
                                        <select id="ajans_id1" name="ajans_id1" class="form-control inp" >
                                            <?php echo loadAjans($daftar_id,$ajans_id1); ?>
                                        </select>
                                    </div>
                                </div>
                                 
                                <div class="col-md-3">
                                    <label class="col-md-6 control-label">آژانس خریدار بلیت:</label> 
                                    <div class="col-md-6">
                                        <select id="ajans_id2" name="ajans_id2" class="form-control inp"  >
                                            <?php echo loadAjans($daftar_id,$ajans_id2); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="col-md-6 control-label">مبلغ بلیت:</label> 
                                    <div class="col-md-6">
                                        <input onkeyup="monize(this);" type="text" class="form-control inp" id="ghimat" name="ghimat" value="<?php echo $ghimat; ?>" onkeypress="return numbericOnKeypress(event);">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="col-md-6 control-label">توضیحات:</label> 
                                    <div class="col-md-6"><input type="text" class="form-control inp" name="toz" id="toz" value="<?php echo $toz; ?>" >
                                    </div>
                                </div>
                                
                                
					<input type="hidden" id="mod" name="mod" value="search" />
                                <div class="col-md-3" style="margin:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-12" onclick="addBelit();">ثبت</button></div>
                                </div>
                            </div>
                          </form>

                          <?php echo $msg; ?>
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
			document.getElementById('mod').value= 2;
			document.getElementById('frm1').submit();
		}
        function sbtFrm()
		{
			document.getElementById('frm1').submit();
		}
        function getPrint()
		{
			document.getElementById('panel-body').style.width = '18cm';
			window.print();
			document.getElementById('panel-body').style.width = 'auto';
		}
        function send_info(khadamat,cost_jam)
		{
			var cost_tedad = document.getElementById('cost_tedad').value;
			if(cost_tedad==0)
				alert('تعداد را وارد کنید');
			else
			{
				if(cost_jam<cost_tedad)
						alert('تعداد وارد شده بیش از مجموع  است');
				else
				{
					if(confirm('آیا کالا با جزئیات از انبار خارج شود؟'))
					{
                        StartLoading();
				
						var gUser_id = document.getElementById('gUser_id').options[document.getElementById('gUser_id').selectedIndex].value;
						var anbar_id = document.getElementById('anbar_id').options[document.getElementById('anbar_id').selectedIndex].value;
						var tarikh = document.getElementById('tarikh1').value;
						var kala_cost = document.getElementById('kala_cost').options[document.getElementById('kala_cost').selectedIndex].value;
                        
                        $.post("cost_anbar.php",{khadamat_id:khadamat,max_tedad:cost_jam,cost_tedad:cost_tedad,kala_cost:kala_cost,tarikh:tarikh,anbar_id:anbar_id,gUser_id:gUser_id},function(data){
                            
                            arr = data.split("_");
                            if(arr[0]=="1"){
                                var brr = arr[1].split("|");
                                var id = brr[0];
                                var cost_kala_id = brr[1];
                                var cost_tedad = brr[2];
                                alert("کالا ثبت شد");
                                $.post("anbar_print.php",{id:id,cost_kala_id:cost_kala_id,cost_tedad:cost_tedad},function(data){
                                    $("#anbar-modal").html(data);
                                    StopLoading();
                                    $('#anbar-modal').modal('show');
                                    
                                });
                            
                            }
                            else 
                                alert(data);

                        });
                        
					}
				}
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