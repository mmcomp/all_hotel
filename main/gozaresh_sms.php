<?php
session_start();
	include("../kernel.php");
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
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$hotelList=daftar_class::hotelList((int)$_SESSION['daftar_id']);
		$shart = '';
		if($hotelList)
			$shart=' and ( `id`='.implode(" or `id`=",$hotelList).")";
		$out = '<select name="hotel_id" id="hotel_id" class="form-control inp" ><option value="-1">همه</option>';
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `moeen_id` > 0 $shart order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>";
		}
		$out.='</select>';
		return $out;
	}
	function  loadDaftar($inp=-1)
	{
		$inp = (int)$inp;
		$out = '<select name="daftar_id" id="daftar_id" class="form-control inp" ><option value="-1">همه</option>';
		mysql_class::ex_sql("select `id`,`name` from `daftar` where `kol_id` > 0 order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>";
		}
		$out.='</select>';
		return $out;
	}
	
	
	function loadTyp($typ)
	{
		$sel1 =($typ==1)?'selected="selected"':'';
		$sel2 =($typ==2)?'selected="selected"':'';
		$out="<option value='1' $sel1 >رضایت میهمان</option>";
		$out.="<option value='2' $sel2 >مغایرت مبلغ رزرو</option>";
		return $out;
	}
	function getPic($datay,$us_id,$k)
	{
		if($k>0)
		{
			$datay =array($GLOBALS['noReply']*100/$k,$GLOBALS['excellent']*100/$k,$GLOBALS['good']*100/$k,$GLOBALS['meduim']*100/$k,$GLOBALS['low']*100/$k);
			$datax = array('1','2','3','4','5','6','7');
			$graph = new Graph(750,300,'auto');
		    	$graph->img->SetMargin(40,40,40,40);
			$graph->img->SetAntiAliasing();
			$graph->SetScale("textlin",0,100);
		    	$graph->SetShadow();
		    	$graph->title->Set(" ");
		    	$p1 = new BarPlot($datay);
		    	$abplot = new AccBarPlot(array($p1));
			$abplot->SetShadow();
			$abplot->value->Show();
		    	$p1->SetColor("blue");
		    	$p1->SetCenter();
			$graph->SetMargin(40,10,40,20);
			$graph->xaxis->SetTickSide(SIDE_BOTTOM);
			$graph->xaxis->SetTickLabels($datax);
			$graph->xaxis->SetLabelAngle(90);
		    	$graph->Add($abplot);
		    	$addr = "chart/$us_id.png";
		    	$graph->Stroke($addr);
		}
		else
			$addr = '';
		return $addr;
	}
	function loadNazar($inp)
	{
		switch ($inp)
		{
			case -2:
				$out ='پیامک‌ارسال‌نشده‌است';
				$GLOBALS['notSent']++;
				break;
			case -1:
				$out ='میهمان‌پاسخ‌نداده‌است';
				$GLOBALS['noReply']++;
				break;
			case 1:
				$out ='عالی';
				$GLOBALS['excellent']++;
				break;
			case 2:
				$out ='خوب';
				$GLOBALS['good']++;
				break;
			case 3:
				$out ='متوسط';
				$GLOBALS['meduim']++;
				break;
			case 4:
				$out ='ضعیف';
				$GLOBALS['low']++;
				break;
			default :
				$out = 'نا معلوم';
				$GLOBALS['unknown']++;
				break;
		}
		return ($out);
	}
	function loadMogh($inp,$bool)
	{
		switch ($inp)
		{
			case -2:
				$out ='پیامک‌ارسال‌نشده‌است';
				break;
			case -1:
				$out ='میهمان‌پاسخ‌نداده‌است';
				break;
			
			default :
				$out =monize($inp);
				break;
		}
		if($bool==1)
			$out ="<span style='color:red' >$out</span>";
		return ($out);
	}
	$GLOBALS['notSent'] = 0;
	$GLOBALS['noReply'] = 0;
	$GLOBALS['excellent'] = 0;
	$GLOBALS['good'] = 0;
	$GLOBALS['meduim'] = 0;
	$GLOBALS['low'] = 0;
	$GLOBALS['unknown'] = 0;
	$hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
	$daftar_id = (isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1;
	$typ = (isset($_REQUEST['typ']))?(int)$_REQUEST['typ']:-1;
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):date('Y-m-d H:i:s'));
	$aztarikh = date("Y-m-d 00:00:00",strtotime($aztarikh));
	$tatarikh = date("Y-m-d 23:59:59",strtotime($tatarikh));
	$user_id=(int)$_SESSION['user_id'];
	$out = '';
	$datay = array();
	$addr = '';
	$legend ='';
	$k = 0;
	$sms_tmp = 0;
	$daftar_shart = '';
	if($daftar_id>0)
	{
		$arr = mysql_class::getInArray('id','ajans',"daftar_id=$daftar_id");
		$daftar_shart = " and `hotel_reserve`.`ajans_id` in ($arr) ";
	}
	if($typ==1)
	{
		
		$out = '
        <table style="width:100%;margin-right:10px;overflow-x:scroll" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">نام میهمان</th>
                                            <th style="text-align:right;">تلفن</th>
                                            <th style="text-align:right;">شماره-رزرو</th>
                                            <th style="text-align:right;">آژانس</th>
                                            <th style="text-align:right;">نظر</th>
                                            <th style="text-align:right;">تاریخ ارسال</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
		/*mysql_class::ex_sql("SELECT  `hotel_reserve`.`tozih`,`hotel_reserve`.`fname` ,  `hotel_reserve`.`lname` ,  `hotel_reserve`.`reserve_id` ,  `hotel_reserve`.`ajans_id` ,  `hotel_reserve`.`sms_vaz` ,  `room_det`.`aztarikh` ,  `room_det`.`tatarikh`
FROM  `hotel_reserve`
LEFT JOIN  `room_det` ON (  `hotel_reserve`.`reserve_id` =  `room_det`.`reserve_id` )
WHERE  `room_det`.`aztarikh` >=  '$aztarikh'
AND  `room_det`.`tatarikh` <=  '$tatarikh'
AND  `hotel_reserve`.`reserve_id` >0 $daftar_shart
GROUP BY  `hotel_reserve`.`reserve_id`",$q);*/
		mysql_class::ex_sql("select * from `in_sms` where `tarikh`>='$aztarikh' and `tarikh`<='$tatarikh'",$q);
		while($r = mysql_fetch_array($q))
		{
			$mobile = $r['mobile'];
			$message = $r['message'];
			$tarikh = $r['tarikh'];
			$k++;
			$style='';
			if($k%2==1)
				$style = 'class="odd"';
            else 
                $style = 'class="even"';
			mysql_class::ex_sql("select `fname`,`lname`,`reserve_id`,`ajans_id` from `hotel_reserve` where `tozih`='$mobile'",$q_res);
			if($r_res = mysql_fetch_array($q_res))
			{
				$name = $r_res['fname'].' '.$r_res['lname'];
				$reserve_id = $r_res['reserve_id'];
				$ajans = new ajans_class((int)$r_res['ajans_id']);
				$out.="<tr $style ><td>".audit_class::enToPer($k)."</td><td>$name</td><td>$mobile</td><td>$reserve_id</td><td>".$ajans->name."</td><td>".loadNazar($message)."</td><td>".audit_class::hamed_pdate($tarikh)."</td></tr>";
			}
		}
		$out .='</tbody></table>';
		
		$out .= '
        
        <div class="box border orange">
									<div class="box-title">
										<h4><i class="fa fa-bar-chart-o"></i>مشاهده نظرسنجی</h4>
									
									</div>
									<div class="box-body" style="overflow-x:scroll">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">میهمان پاسخ نداده است</th>
												<th style="text-align:right">عالی</th>
												<th style="text-align:right">خوب</th>
                                                <th style="text-align:right">متوسط</th>
                                                <th style="text-align:right">ضعیف</th>
											  </tr>
											</thead>
											<tbody>
 
				<tr>
					<td>'.$GLOBALS['noReply'].'</td>
					<td>'.$GLOBALS['excellent'].'</td>
					<td>'.$GLOBALS['good'].'</td>
					<td>'.$GLOBALS['meduim'].'</td>
					<td>'.$GLOBALS['low'].'</td>
				</tr>
			</tbody></table>';
		$addr = getPic($datay,$user_id,$sms_tmp);
		$legend = '
        
        <div class="box border orange">
									<div class="box-title">
										<h4><i class="fa fa-glass"></i>کالای خارج شده</h4>
									
									</div>
									<div class="box-body" style="overflow-x:scroll">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">1->میهمان پاسخ نداده است</th>
												<th style="text-align:right">2->عالی</th>
												<th style="text-align:right">3->خوب</th>
                                                <th style="text-align:right">4->متوسط</th>
                                                <th style="text-align:right">5->ضعیف</th>
											  </tr>
                                              <tr>
					<td colspan="5" >
						اعداد به صورت تقریبی می‌باشد
					</td>
				</tr>
											</thead>
											<tbody>
 
				
			</tbody></table>';
	}
	else if($typ==2)
	{
		$out = '
        <table style="width:100%;margin-right:10px;overflow-x:scroll" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">از تاریخ</th>
                                            <th style="text-align:right;">تا تاریخ</th>
                                            <th style="text-align:right;">نام میهمان</th>
                                            <th style="text-align:right;">تلفن</th>
                                            <th style="text-align:right;">شماره-رزرو</th>
                                            <th style="text-align:right;">آژانس</th>
                                            <th style="text-align:right;">مبلغ رزرو</th>
                                            <th style="text-align:right;">مبلغ پیامک شده</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        
		mysql_class::ex_sql("SELECT  (`hotel_reserve`.`m_hotel`+`hotel_reserve`.`m_belit1`+`hotel_reserve`.`m_belit2`) as `m_tour`,`hotel_reserve`.`tozih`,`hotel_reserve`.`fname` ,  `hotel_reserve`.`lname` ,  `hotel_reserve`.`reserve_id` ,  `hotel_reserve`.`ajans_id` ,  `hotel_reserve`.`sms_ghimat` ,  `room_det`.`aztarikh` ,  `room_det`.`tatarikh`
FROM  `hotel_reserve`
LEFT JOIN  `room_det` ON (  `hotel_reserve`.`reserve_id` =  `room_det`.`reserve_id` )
WHERE  `room_det`.`aztarikh` >=  '$aztarikh'
AND  `room_det`.`tatarikh` <=  '$tatarikh'
AND  `hotel_reserve`.`reserve_id` >0 $daftar_shart
GROUP BY  `hotel_reserve`.`reserve_id`",$q);
		while($r = mysql_fetch_array($q))
		{
			$k++;
			$style='';
			if($k%2==1)
				$style = 'class="odd"';
            else
                $style = 'class="even"';
			$ajans = new ajans_class((int)$r['ajans_id']);
			$daftar = new daftar_class($ajans->daftar_id);
			$bool = 0;
			if((int)$r['m_tour']!= (int)$r['sms_ghimat'] && (int)$r['sms_ghimat']>1000)
				$bool = 1;
			$out.="<tr $style ><td>".audit_class::enToPer($k)."</td><td>".audit_class::hamed_pdate($r['aztarikh'])."</td><td>".audit_class::hamed_pdate($r['tatarikh'])."</td><td>".$r['fname'].' '.$r['lname']."</td><td>".$r['tozih']."</td><td>".$r['reserve_id']."</td><td>".$ajans->name.'('.$daftar->name.")</td><td>".loadMogh((int)$r['m_tour'],$bool)."</td><td>".loadMogh((int)$r['sms_ghimat'],$bool)."</td></tr>";
		}
		$out .='</tbody></table>';
	}
		$pic = '';
		if($addr!='')
			$pic ="<img src='$addr' width='700px' style='cursor:pointer;'>".'<br/>'.$legend;
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش پیامک</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-envelope"></i>گزارش پیامک</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <form id='frm1'  method='GET' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">هتل:</label> 
                                    <div class="col-md-8"><?php echo loadHotel($hotel_id); ?></div>
                                </div>
                                 
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">نام دفتر:</label> 
                                    <div class="col-md-8">
                                            <?php echo loadDaftar($daftar_id); ?>
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">نوع:</label> 
                                    <div class="col-md-8">
                                        <select class="form-control inp" name="typ" id="typ" >
                                            <?php echo loadTyp($typ); ?>
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">از تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control inp" type="text" name="aztarikh" id="datepicker1" value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>"  >
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">تا تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control inp" type="text" name="tatarikh" id="datepicker2" value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>"  >
                                    
                                    </div>
                                </div>
                                <input type='hidden' name='mod' id='mod' value='1' >
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left" onclick="send_search();">جستجو</button></div>
                                </div>
                            </div>
                          </form>

                            <?php echo $pic; ?>
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
    <script src="<?php echo $root ?>js/bootstrap-datepicker.fa.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.min.js"></script>
    
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