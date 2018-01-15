<?php
	session_start();
	include("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if($se->detailAuth('paziresh') && isset($_REQUEST['reserve_id']))
	{
		$cod = dechex((int)$_REQUEST['reserve_id']+10000);
		die("<script> window.location = 'paziresh.php?reserve_id=$cod&'; </script>");
	}
	$msg = '';
	$output = '';
	$reserve_id = 0;
	$bool = FALSE;
	if(isset($_REQUEST['peigiri']) && isset($_REQUEST['mob']) )
	{
		$reserve_id = hexdec($_REQUEST['peigiri'])-10000;
		if((int)$reserve_id > 0)
		{
			$reserve = new reserve_class((int)$reserve_id);
			if($reserve->reserve_id>0)
				$_REQUEST['reserve_id'] = dechex($reserve->reserve_id+10000);
		}
	}
	if(isset($_REQUEST['reserve_id']))
	{
		$out = '';
		$bool = TRUE;
		$edame = FALSE;
		$mob = (isset($_REQUEST['mob']))?$_REQUEST['mob']:-1;
		$reserve_id =$_REQUEST['reserve_id'];
		$reserve =new reserve_class($reserve_id);
		if($reserve!==FALSE)
		{
			$tell =$reserve->hotel_reserve->tozih;
			if(($mob !=-1 && $tell==$_REQUEST['mob']) || $mob==-1)
				$edame = TRUE;
		}
		if($edame)
		{
			$otagh_arr = array();
			$tedad_otagh = '';
			$tedad_kol = 0;
			for($h=0;$h<count($reserve->room);$h++)
				if(isset($otagh_arr[$reserve->room[$h]->room_typ_id]))
					$otagh_arr[$reserve->room[$h]->room_typ_id] ++ ;
				else
					$otagh_arr[$reserve->room[$h]->room_typ_id]=1;
			foreach($otagh_arr as $room_typ_id=>$tedad_room_typ)
			{
				$room_typ_tmp =new room_typ_class($room_typ_id);
				$tedad_otagh .=(($tedad_otagh=='')?'':',').$room_typ_tmp->name.' '.$tedad_room_typ;
				$tedad_kol += $tedad_room_typ;
			}
			$ajans = new ajans_class($reserve->hotel_reserve->ajans_id);
			$ajans_name = $ajans->name;
			$hotel = new hotel_class($reserve->room[0]->hotel_id);
			$hotel_name = $hotel->name;
			$aztarikh = $reserve->room_det[0]->aztarikh;
			//$shab = ceil((strtotime($reserve->room_det[0]->tatarikh)-strtotime($reserve->room_det[0]->aztarikh)) / (24*60*60));
			$shab = (int)((strtotime($reserve->room_det[0]->tatarikh)-strtotime($reserve->room_det[0]->aztarikh)) / (24*60*60));
			$room_typ_ids = array();
			for($i=0;$i<count($reserve->room_det[0]);$i++)
				$room_typ_ids[] = $reserve->room_det[0]->room_typ;
			$room_name = room_typ_class::loadTypDetails($room_typ_ids);
			$nafar = $reserve->room_det[0]->nafar;
			$sargrooh =$reserve->hotel_reserve->fname.' '.$reserve->hotel_reserve->lname;
			$hotel_mablagh = $reserve->hotel_reserve->m_hotel;
			//$khadamat = unserialize(webservice_class::prepairKhadamat($_REQUEST['khadamat']));
			$khadamat =($reserve->khadamat_det);
			$output = "
			<h4>آژانس $ajans_name از تاریخ ".(jdate("d / m / Y",strtotime($aztarikh)))." به مدت ".(enToPerNums($shab))." شب</h4>
			<br/>";
			$output.='
            <div class="box border orange" style="overflow-x:scroll;">
									
									<div class="box-body">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">هتل</th>
												<th style="text-align:right">نوع اتاق</th>
												<th style="text-align:right">تعداد اتاق</th>
                                                <th style="text-align:right">تعداد نفرات</th>
											  </tr>
											</thead>
											<tbody>';
			$output.="<tr><td>$hotel_name</td>";
			$output.="<td>$tedad_otagh</td>";
		        $output.="<td> $tedad_kol</td>";
	 		$output.="<td>$nafar</td>";
			$output.='</tr></tbody></table></div></div>';
			if(count($khadamat)>0)
				$output.='
                <div class="box border orange" style="overflow-x:scroll;">
									
									<div class="box-body">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">خدمات</th>
												<th style="text-align:right">تعداد-روزانه</th>
												<th style="text-align:right">جزئیات</th>
											  </tr>
											</thead>
											<tbody>';
			else
				$output.='
                <div class="box border orange" style="overflow-x:scroll;">
									
									<div class="box-body">
										<table class="table table-hover">
											<thead>';
			$jam_ghi_khadamat = 0;
			for($i=0;$i<count($khadamat);$i++)
			{
					$khadamat_cl= new khadamat_class($khadamat[$i]['khadamat_id']);
					$khadamat_name = $khadamat_cl->name;
					$output .= '<tr>';
					$output .= '<td>'.$khadamat_name.'</td>';
					$is_voroodi = '';
					$is_khorooji = '';
					if(($khadamat[$i]['voroodi']))
						$is_voroodi = '  روز ورود-دارد ';
					if(($khadamat[$i]['khorooji']))
						$is_khorooji = '  روز خروج-دارد';
					/*
					if(isset($checkbox[$id]))
					{
						$output .= "<td>دارد $is_voroodi $is_khorooji</td>";
						$jam_ghi_khadamat +=$ghi;
					}
					*/
					//else if(isset($textbox[$id]))
					//{
					$output .= '<td>'.$khadamat[$i]['count']."</td><td> $is_voroodi $is_khorooji </td>";
					//$jam_ghi_khadamat += $khadamat[$i]['ghimat']*$khadamat[$i]['tedad'];
					//}
					$output .= '</tr>';
			}
			$extra_toz = '';
			if($reserve->hotel_reserve->extra_toz!='')
				$extra_toz = 'توضیحات: '.$reserve->hotel_reserve->extra_toz;
			$output.="<tr><td colspan='1'>نام سرگروه: $sargrooh</td><td colspan='2' >تلفن: $tell $extra_toz</td></tr>";
			$output.='<tr><td> شماره‌رزرو و پیگیری:<span style="font-weight:bold;background-color:white;">&nbsp;&nbsp;'.$reserve_id.'&nbsp;&nbsp;</span></td><td colspan="2">'.$hotel->name.' '.((isset($hotel->info['properties']))?$hotel->info['properties']:'').'</td></tr>';
			$user_name = new user_class($reserve->room_det[0]->user_id);
			$user_name =$user_name->fname.' '.$user_name->lname;
			$user_printer_name = new user_class($_SESSION['user_id']);
			$user_printer_name =$user_printer_name->fname.' '.$user_printer_name->lname;
			$chapDate = audit_class::hamed_pdate(date("Y-m-d"));
			$output .="<tr><td>".(($conf->vacher_mablagh)?"مبلغ: ".monize($reserve->hotel_reserve->m_hotel+$reserve->hotel_reserve->m_belit1+$reserve->hotel_reserve->m_belit2):'&nbsp;')."</td><td colspan='2' >رزرو شده توسط $user_name</td></tr>";
			$output .="<tr><td colspan='3'>چاپ شده در تاریخ $chapDate صادر شده توسط $user_printer_name</td></tr>";
			$fullView = TRUE;
			if(isset($_REQUEST['fullview']) && $_REQUEST['fullview']=='FALSE')
				$fullView = FALSE;
			$view = (($fullView)?"<button style=\"right: 50%;min-width: 170px;margin-right: -85px;\" class=\"btn btn-info col-md-4 pull-center\" onclick='loadSimple();'>نمایش ساده</button>":"<button style=\"right: 50%;min-width: 170px;margin-right: -85px;\" class=\"btn btn-info col-md-4\" onclick='loadFull();'>نمایش کامل</button>");
			$output .="<tr id='last_row' ><td><button style=\"min-width:170px\" class=\"btn btn-success col-md-4\" onclick='page_print();'>چاپ</button></td><td>$view</td><td><button style=\"min-width:170px\" class=\"btn btn-danger col-md-4 pull-left\" onclick='bastan();'>خروج</button></td></tr></tbody></table></div></div>";
			$reserve->loadWatcher();
			$reserve->watcherAdd('table',$output);
			if($conf->watcher != '' && $fullView)
				$output = $reserve->watcherCompile($conf->watcher);
		}
		else
			$msg ='چنین رزروی وجود ندارد';
	}
	else
	{
		$msg = ("اطلاعات ارسال شده جهت صدور واچر کافی نیست <br/><a href='index.php'>بازگشت</a>");
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>RAHA</title>
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
          function page_print()
		{
			document.getElementById('last_row').style.display = 'none';
			window.print();
			document.getElementById('last_row').style.display = '';
		}
		function bastan()
		{
			window.close();
		}
		function loadSimple()
		{
			var form = document.createElement("form");
			form.setAttribute("method", "POST");
			form.setAttribute("target", "_self");
			var hiddenField = document.createElement("input");
			hiddenField.setAttribute("name", "fullview");
			hiddenField.setAttribute("value", "FALSE");
			form.appendChild(hiddenField);
			hiddenField = document.createElement("input");
                        hiddenField.setAttribute("name", "reserve_id");
                        hiddenField.setAttribute("value", "<?php echo $reserve_id; ?>");
                        form.appendChild(hiddenField);
			document.body.appendChild(form);
			form.submit();
			document.body.removeChild(form);
		}
		function loadFull()
		{
			var form = document.createElement("form");
			form.setAttribute("method", "POST");
			form.setAttribute("target", "_self");
			var hiddenField = document.createElement("input");
			hiddenField.setAttribute("name", "fullview");
			hiddenField.setAttribute("value", "TRUE");
			form.appendChild(hiddenField);
			hiddenField = document.createElement("input");
                        hiddenField.setAttribute("name", "reserve_id");
                        hiddenField.setAttribute("value", "<?php echo $reserve_id; ?>");
                        form.appendChild(hiddenField);
			document.body.appendChild(form);
			form.submit();
			document.body.removeChild(form);
		}
     
    </script>
    <script type="text/javascript" src="../js/tavanir.js"></script>
	
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
                    <div class="panel panel-default" style="border: 1px solid #ffae2e;overflow-x:scroll">
                        <div class="panel-heading" style="background-color:#ffae2e;color:white;padding:1px;">
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-bell"></i>سامانه رزرواسیون</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           
                            <?php echo $output.' '.$msg; ?>
                          
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