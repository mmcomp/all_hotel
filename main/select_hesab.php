<?php
session_start();
	include_once("../kernel.php");
	function loadHesab($tbname,$upper_id,$upper_v,$first)
	{
		$se = security_class::auth((int)$_SESSION['user_id']);
		$out = array();
		$tmp_werc = '';
		$upper_tb = substr($upper_id,0,-3);
		$cur_v = (isset($_REQUEST[$tbname.'_id']))?(int)$_REQUEST[$tbname.'_id']:-1;
		if($cur_v==-1 && $upper_v == -1 && !$first)
			$cur_v = ' and 1=0 ';
		else if($cur_v>0 && $upper_v == -1 && !$first)
			$cur_v = " and `id`=$cur_v ";
		else
			$cur_v = "";
		if ($tbname=='kol')
		{
			if(/*$se->detailAuth('dafater')*/$_SESSION['daftar_id']!=49)
			{
				$daftar_id = $_SESSION['daftar_id'];
				mysql_class::ex_sql("select `kol_id` from `daftar` where `id`='$daftar_id' order by `id`",$q_kol);
				if($r_kol=mysql_fetch_array($q_kol,MYSQL_ASSOC))
				{
					$kol_id = $r_kol['kol_id'];
					$tmp_werc = " and `id`='$kol_id ' ";
				}
			}
			else
				$tmp_werc = "";
		}
		mysql_class::ex_sql("select * from `$tbname` where 1=1 $tmp_werc $cur_v".(($upper_v == -1)?"":" and `$upper_id` = '$upper_v' order by `name`"),$q);

		while($r = mysql_fetch_array($q))
		{
			$out[] = array("id"=>(int)$r["id"],"name"=>$r["name"]);
		}
		return($out);
	}
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$refPage = (isset($_REQUEST["refPage"])?$_REQUEST["refPage"]:'sanad_new.php');
	if (!isset($_REQUEST["sel_id"]))
			die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
	$level = ((isset($_REQUEST['level']))?(int)$_REQUEST['level']:4);
	$return_name = ((isset($_REQUEST['return_name']))?$_REQUEST['return_name']:'sel_id');
        $hesab  = $conf->hesabKol();  
	if(isset($_REQUEST['gridName'])) 
		$gridName = $_REQUEST['gridName'];
        foreach($hesab as $meghdar=>$value)
                if($value==null)
                        unset($hesab[$meghdar]);
	$ta_name =substr($meghdar,0,-3);
	$out = "";
	$p_tb = "";
	$p_val = -1;
	$postBack = FALSE;
	$action = "";
	if(isset($_REQUEST["tb"]))
	{
		$postBack = TRUE;
		$p_val = $_REQUEST["val"];
		$s_tb = substr($_REQUEST["tb"],0,-3);
	}
	$first = TRUE;
	$cur_index = 1;
	foreach($hesab as $meghdar=>$value)
        {
		if($cur_index <= $level)
		{
			$ta_name =substr($meghdar,0,-3);
			$var_tb = ((isset($_REQUEST[$meghdar]))?(int)$_REQUEST[$meghdar]:-1);
			$out .= "<div class='col-md-4'><select class=\"form-control inp\"  id=\"$meghdar\" name=\"$meghdar\" onchange=\"selectTb('$meghdar',this);\">\n";
			if($postBack && ($p_tb == $s_tb))
				$tmp = loadHesab($ta_name,$p_tb."_id",$p_val,$first);
			else
				$tmp = loadHesab($ta_name,$p_tb."_id",-1,$first);
			$out .= "<option value = \"-1\" >\n \n</option>\n";
			for($i = 0;$i<count($tmp);$i++)
				$out .= "<option value=\"".$tmp[$i]["id"]."\" ".(($tmp[$i]["id"]===$var_tb)?"selected=\"selected\"":"").">\n".$tmp[$i]["name"]."\n</option>\n";
			$out .= "</select></div>";
			$p_tb = $ta_name;
			if($first) $first = FALSE;
		}
		$cur_index++;
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>انتخاب حساب</title>
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
    <script language="javascript">
			function selectTb(tb,obj)
			{
				var val = obj.options[obj.selectedIndex].value;
				document.getElementById("tb").value = tb;
				document.getElementById("val").value = val;
				document.getElementById("selfrm").submit();
			}
			function sendBack()
			{
				var combs = document.getElementsByTagName("select");
				var ok = true;
				for(var i=0;i < combs.length-<?php echo(4-$level); ?>;i++)
				{
					if(combs[i].selectedIndex<=0)
						ok = false;
				}
				if(ok)
				{
					document.getElementById("selfrm").action = "<?php echo $refPage; ?>";
					document.getElementById("selfrm").submit();
				}
				else
				{
					alert('لطفاً حساب را بطور کامل انتخاب کنید');
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>انتخاب حساب</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <form id="selfrm" method="POST" <?php echo $action; ?>>
				<h4>انتخاب حساب جهت سند مورد نظر</h4>
				<br/>
				<?php echo $out;?>
				<input type="button" class="inp btn btn-info col-md-4"  value="انتخاب" onclick="sendBack();" />
				<input id="tb" name="tb"  type="hidden" />
				<input id="val" name="val" type="hidden" />
				<input type="hidden" id="<?php echo $return_name; ?>" name="<?php echo $return_name; ?>" value="<?php echo ((isset($_REQUEST["sel_id"]))?(int)$_REQUEST["sel_id"]:"-1"); ?>" />
				<input type="hidden" id="form_shomare_sanad" name="form_shomare_sanad" value="<?php echo ((isset($_REQUEST["form_shomare_sanad"]))?(int)$_REQUEST["form_shomare_sanad"]:"-1"); ?>" />
				<input type="hidden" id="tedad" name="tedad" value="<?php echo ((isset($_REQUEST["tedad"]))?(int)$_REQUEST["tedad"]:"-3"); ?>" />
				<input type="hidden" id="refPage"  name="refPage" value="<?php echo $refPage; ?>" />
				<?php 
				if (isset($_REQUEST['pageSelector']) && isset($_REQUEST['gridName']) ) 
					echo "<input type='hidden' id='pageSelector'  name='pageSelector' value='".$_REQUEST['pageSelector']."' >";
				if (isset($_REQUEST['pageCount']) && isset($_REQUEST['gridName']) ) 
					echo "<input type='hidden' id='pageCount_$gridName'  name='pageCount_$gridName' value='".$_REQUEST['pageCount']."' >";

				?>
			</form>
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