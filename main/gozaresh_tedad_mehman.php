<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //if(!$se->can_view)
                //die(lang_fa_class::access_deny);
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$hotelList=daftar_class::hotelList((int)$_SESSION['daftar_id']);
		$shart = '';
		if($hotelList)
			$shart=' and ( `id`='.implode(" or `id`=",$hotelList).")";
		$out = '<select name="hotel_id" class="form-control inp" style="width:auto;" >';
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `moeen_id` > 0 $shart order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadTyp($typ)
	{
		$sel1 =($typ==1)?'selected="selected"':'';
		$sel2 =($typ==2)?'selected="selected"':'';
		$out="<option value='1' $sel1 >تعداد میهمان</option>\n";
		$out.="<option value='2' $sel2 >تعداد اتاق</option>\n";
		return $out;
	}
	function getPic($datay,$us_id)
	{
		if(count($datay)==0)
			$datay = array(0);
		else if(count($datay)>31)
			{
				$datay1 =array();
				$k = 0;
				foreach($datay as $tar=>$val)
				{
					$datay1[$tar] = $val;
					$k++;
					if($k>30)
						break;
				}
			}
		else if(count($datay)<=31)
			$datay1= $datay;
		$datay =array();
		$datax = array();
		foreach($datay1 as $tarikh=>$value)
		{
			$datax[] = $tarikh;
			$datay[] = $value;
		}
		$graph = new Graph(750,300,'auto');
	    	$graph->img->SetMargin(40,40,40,40);
		$graph->img->SetAntiAliasing();
		$graph->SetScale("textlin",0,200);
	    	$graph->SetShadow();
	    	$graph->title->Set(" ");
	    	$p1 = new BarPlot($datay);
	    	$abplot = new AccBarPlot(array($p1));
		$abplot->SetShadow();
		$abplot->value->Show();
	    	$p1->SetColor("blue");
	    	$p1->SetCenter();
		$graph->SetMargin(40,10,40,80);
		$graph->xaxis->SetTickSide(SIDE_BOTTOM);
		$graph->xaxis->SetTickLabels($datax);
		$graph->xaxis->SetLabelAngle(90);
	    	$graph->Add($abplot);
	    	$addr = "chart/$us_id.png";
	    	$graph->Stroke($addr);
		return $addr;
	}
	$hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
	$typ = (isset($_REQUEST['typ']))?(int)$_REQUEST['typ']:-1;
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):date('Y-m-d H:i:s'));
	$aztarikh = date("Y-m-d 00:00:00",strtotime($aztarikh));
	$tatarikh = date("Y-m-d 23:59:59",strtotime($tatarikh));
	$user_id=(int)$_SESSION['user_id'];
	$out = '';
	$datay = array();
	$addr = '';
	$k = 0;
	if($typ==1)
	{
		$zarfiat_kol = hotel_class::getKolZarfiat($hotel_id );
		$tedad_now = hotel_class::getFullTedad($hotel_id,$aztarikh,$tatarikh);
		$tedad_jam =0;
		$zarfiat_kol_jam = 0;
		$out = '
             <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">تاریخ</th>
                                            <th style="text-align:right;">تعداد میهمانان</th>
                                            <th style="text-align:right;">ظرفیت کل</th>
                                            <th style="text-align:right;">درصد اشغال</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        
		foreach($tedad_now as $tarikh=>$tedad)
		{
			$k++;
			$darsad_eshghal = ($zarfiat_kol!=0)?(string)number_format($tedad/$zarfiat_kol,2)*100 .'%':'ظرفیت کل تعریف نشده است';
			$out.="<tr><td>$k</td><td>$tarikh</td><td>$tedad</td><td>$zarfiat_kol</td><td>$darsad_eshghal</td></tr>";
			$tedad_jam += $tedad;
			$zarfiat_kol_jam += $zarfiat_kol;
			$datay[$tarikh] = $darsad_eshghal;
		}
		$darsad_eshghal_jam = ($zarfiat_kol_jam!=0)?(string)number_format($tedad_jam/$zarfiat_kol_jam,2)*100 .'%':'ظرفیت کل تعریف نشده است';
		$out .="<tr style='font-weight:bold;' ><td></td><td>مجموع</td><td>$tedad_jam</td><td>$zarfiat_kol_jam</td><td>$darsad_eshghal_jam</td></tr>";
		$out .='</tbody></table>';
		$addr = getPic($datay,$user_id);
	}
	else if($typ==2)
	{
		$eshghal_jam=0;
		$kol_jam = 0;
		$room_now = hotel_class::getFullRoom($hotel_id,$aztarikh,$tatarikh);
		$out = '
        <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">تاریخ</th>
                                            <th style="text-align:right;">جزئیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
		foreach($room_now as $tarikh=>$room)
		{
			$eshghal_kol = 0;
			$kol_kol = 0;
			$k++;
			$out.="<tr><td>$k</td><td>$tarikh</td><td>";
			$out .='
            <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">نوع</th>
                                            <th style="text-align:right;">اشغال</th>
                                            <th style="text-align:right;">کل</th>
                                            <th style="text-align:right;">درصد اشغال</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
			foreach($room as $name=>$vaz)
			{
				$darsad_esh = ($vaz['kol']!=0)?(string)number_format($vaz['eshghal']/$vaz['kol'],2)*100 .'%':'نامعلوم';
				$out .="<tr><td>$name</td><td>".$vaz['eshghal']."</td><td>".$vaz['kol']."</td><td>$darsad_esh</td></tr>";
				$eshghal_kol += $vaz['eshghal'];
				$kol_kol += $vaz['kol'];
			}
			$darsad_esh_kol =  ($kol_kol!=0)?(string)number_format($eshghal_kol/$kol_kol,2)*100 .'%':'نامعلوم';
			$datay[$tarikh] = ($kol_kol!=0)?(int)(($eshghal_kol/$kol_kol)*100):0;
			$out .="<tr><td>جمع</td><td>$eshghal_kol</td><td>$kol_kol</td><td>$darsad_esh_kol</td></tr>";
			$out .= "</table></td>";
			$eshghal_jam+=$eshghal_kol;
			$kol_jam+=$kol_kol;
		}
		$darsad_esh_jam = ($kol_jam!=0)?(string)number_format($eshghal_jam/$kol_jam,2)*100 .'%':'ظرفیت کل تعریف نشده است';
		$out .="<tr><td></td><td>جمع کل</td><td>
        
         <table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                    <thead>
                                        <tr>
                                            <th style=\"text-align:right;width:1px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th style=\"text-align:right;\">اشغال:$eshghal_jam</th>
                                            <th style=\"text-align:right;\">کل:$kol_jam</th>
                                            <th style=\"text-align:right;\">درصداشغال:$darsad_esh_jam</th>
                                        </tr>
                                    </thead>
                                    <tbody></table></td></tr>";
        
		$out .='</tbody></table>';
		$addr = getPic($datay,$user_id);
	}
		$pic = '';
		if($addr!='')
			$pic ="<img src='$addr' width='700px' style='cursor:pointer;'>";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش تعداد مهمان</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-user"></i>گزارش تعداد مهمان</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body"  style="overflow-x:scroll">
                           <form id='frm1'  method='GET' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">هتل:</label> 
                                    <div class="col-md-9"><?php echo loadHotel($hotel_id); ?></div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">نوع:</label> 
                                    <div class="col-md-8">
                                        <select class="form-control inp" name="typ" id="typ" >
                                            <?php echo loadTyp($typ); ?>	
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">از تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control inp" type="text" name="aztarikh" id="datepicker1" value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>"  >
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">تا تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control inp" type="text" name="tatarikh" id="datepicker2" value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>"  >
                                    
                                    </div>
                                </div>
                                <input type='hidden' name='mod' id='mod' value='1' >
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left" onclick="send_search();">جستجو</button>
                                    </div>
                                </div>
                               </div>
                            </form>
                            <div style="text-align:center">
                             <?php echo $pic; ?>
                            </div>
                            <br/>
                            <?php echo $out; ?>
                          
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