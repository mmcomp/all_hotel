<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadNesbat()
	{
		$tmp = statics_class::loadByKey('نسبت');
		$out['سرگروه'] = '-1';
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadNesbatNew($ind,$key='نسبت')
	{
		$tmp = statics_class::loadByKey($key);
		$out = '';
		for($i=0;$i<count($tmp);$i++)
			if($ind == $tmp[$i]->id)
				$out = $tmp[$i]->fvalue;
		return $out;
	}
	function loadMellait()
	{
		$tmp = statics_class::loadByKey('ملیت');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	$msg = '';
	$out = '';
	$scr = '';
	$vaziat = 0;
	$room_id = -1;
	$reserve_id = -1;
	$isAdmin = $se->detailAuth('all');
	$output = '
    <div class="box border orange">
									<div class="box-body" style="overflow-x:scroll;">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">پذیرش</th>
												<th style="text-align:right">شماره رزرو</th>
												<th style="text-align:right">هتل</th>
                                                <th style="text-align:right">نام</th>
                                                <th style="text-align:right">شماره اتاق</th>
                                                <th style="text-align:right">تعداد نفرات</th>
                                                <th style="text-align:right">قیمت هتل</th>
                                                <th style="text-align:right">جمع کل</th>
                                                <th style="text-align:right">تاریخ ورود</th>
                                                <th style="text-align:right">تاریخ خروج</th>
											  </tr>
											</thead>
											<tbody>';
	$changed = FALSE;
	$room_loded = FALSE;
	$msg = '';
	$tarikh_true = TRUE;
	if(!isset($_REQUEST['reserve_id']) && isset($_REQUEST['room_id']))
	{
		$tarikh = ((isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):'0000-00-00 00:00:00');
		$r_tmp = new room_class((int)$_REQUEST['room_id']);
		$room_id = (int)$_REQUEST['room_id'];
		$vaziat = $r_tmp->vaziat;
		$res = $r_tmp->getAnyReserve($tarikh);
		if(isset($_REQUEST['tarikh']) )
		if(strtotime(date("Y-m-d H:i:s")) > strtotime($tarikh))
		{
			$tarikh_true = FALSE;
			$msg = 'تاریخ درست وارد نشده است';
		}
			
/*
		$_REQUEST['vaziat'] = $r_tmp->vaziat;
		$reserve_id = $reserve_id[0]['reserve_id'];
		$_REQUEST['reserve_id'] = $reserve_id;
*/
                if(isset($_REQUEST['vaziat']) && $res==null && $tarikh_true)
                {
                        $vaziat = (int)$_REQUEST['vaziat'];
                        $room_id = (int)$_REQUEST['room_id'];
                        mysql_class::ex_sqlx("update `room` set `vaziat` = $vaziat,`end_fix_date`='$tarikh' where `id` = $room_id");
                        $scr = "<script language='javascript'> window.parent.location = window.parent.location; </script>";
                }
		if($res!=null)
		{
			$_REQUEST['vaziat'] = $r_tmp->vaziat;
			$reserve_id = $res[0]['reserve_id'];
			$_REQUEST['reserve_id'] = $reserve_id;
			$room_id = (int)$_REQUEST['room_id'];
		}
		$room_loded = TRUE;
		$reserve_rooms = '<option selected="selected" value="'.$room_id.'">'.$r_tmp->name.'</option>'."\n";
	}
	else if(isset($_REQUEST['reserve_id']))
		$changed = TRUE;
	if($se->detailAuth('tasisat'))
		die('<script>window.location="tasisat.php?room_id='.$room_id.'"</script>');
	if(isset($_REQUEST['reserve_id']) && (int)$_REQUEST['reserve_id']>0)
	{
		$reserve_id = (int)$_REQUEST['reserve_id'];
		$vaziat = 0;
		$room_id = -1;
		if(isset($_REQUEST['vaziat']))
		{
			$vaziat = (int)$_REQUEST['vaziat'];
			$room_id = (int)$_REQUEST['room_id'];
			mysql_class::ex_sqlx("update `room` set `vaziat` = $vaziat where `id` = $room_id");
			if($changed)
				$scr = "<script language='javascript'> window.parent.location = window.parent.location; </script>";
		}
		$reserve_rooms = '';
		$reserve_room_det = new room_det_class;
		$reserve_room_det = $reserve_room_det->loadByReserve($reserve_id);
		$reserve_room_det = $reserve_room_det[0];
		for($ind = 0;$ind < count($reserve_room_det);$ind++)
		{
			$r_tmp = new room_class($reserve_room_det[$ind]->room_id);
			$reserve_rooms .= '<option '.(($reserve_room_det[$ind]->room_id==$room_id)?'selected="selected"':'').' value="'.$reserve_room_det[$ind]->room_id.'">'.$r_tmp->name.'</option>'."\n";
		}
		$styl = 'class=""';
		$horel_reserve = new hotel_reserve_class;
		$horel_reserve->loadByReserve($reserve_id);
		$ajans = new ajans_class($horel_reserve->ajans_id);
		//--------------------------
		$reserve_id_code =dechex($reserve_id+10000);
		$khorooj = '';
		/*
		if(reserve_class::isKhorooj($reserve_id))
			$khorooj = "<sapn style='color:green'>خارج شده</span>";
		else if(reserve_class::isPaziresh($reserve_id))
			$khorooj = "<a style='color:red' target='_blank' href='paziresh.php?reserve_id=$reserve_id_code&kh=1' >خروج</a>";
		*/
		//$paziresh ="<td><a target='_blank' href='paziresh.php?reserve_id=$reserve_id_code&kh=0' >پذیرش</a>&nbsp;$khorooj</td>";
		$paziresh ='<td></td>';
		//--------------------------
		if(($_SESSION['daftar_id']==$ajans->daftar_id || $isAdmin) && !$se->detailAuth('super'))
		{
			$room = room_det_class::loadDetByReserve_id($reserve_id );
			$rooms = '';
			for($j=0;$j<count($room['rooms']);$j++)
			{
				$tmp_room = new room_class($room['rooms'][$j]['room_id']);
				$rooms.=$tmp_room->name.(($j<count($room['rooms'])-1)?' , ':'');
			}
			$name = room_det_class::loadNamesByReserve_id($reserve_id );
			$khadamat = room_det_class::loadKhadamatByReserve_id($reserve_id );
			$output .="<tr $styl >$paziresh<td>$reserve_id</td>";
			$output .="<td>".$room['rooms'][0]['hotel']."</td><td>".$name[0]."</td><td>$rooms</td><td>".$room['rooms'][0]['nafar']."</td><td>".monize($horel_reserve->m_hotel)."</td>";
			$output .="<td>".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."</td>";
			$output .="<td>".audit_class::hamed_pdate($room['rooms'][0]['aztarikh'])."</td>";
			$output .="<td>".audit_class::hamed_pdate($room['rooms'][0]['tatarikh'])."</td></tr>";
			if($horel_reserve->extra_toz!='')
				$output .="<tr $styl ><td>توضیحات : </td><td colspan='10'>".$horel_reserve->extra_toz."</td></tr>";
			//-----------------------------------------
            
            
            $out='<table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">نام</th>
                                            <th style="text-align:right;">نام خانوادگی</th>
                                            <th style="text-align:right;">ملیت</th>
                                            <th style="text-align:right;">نسبت</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
            mysql_class::ex_sql("select * from `mehman` where `reserve_id`='$reserve_id'",$ss);
            $i=1;
            while($r = mysql_fetch_array($ss))
            {
                $fname = $r['fname'];
                $lname = $r['lname'];
                $melliat = loadNesbatNew($r['melliat'],'ملیت');;
                $nesbat = loadNesbatNew($r['nesbat']);
                if(fmod($i,2)!=0){
                    $out.='
                    <tr class="odd">
                    <td>'.$i.'</td>
                    <td>'.$fname.'</td>
                    <td>'.$lname.'</td>
                    <td>'.$melliat.'</td>
                    <td>'.$nesbat.'</td>
                    </tr>
                    
                    ';
                    $i++;
                }
                else{
                    $out.='
                    <tr class="even">
                    <td>'.$i.'</td>
                    <td>'.$fname.'</td>
                    <td>'.$lname.'</td>
                    <td>'.$melliat.'</td>
                    <td>'.$nesbat.'</td>
                    </tr>
                    ';
                    $i++;
                }
            }
            $out.='</tbody></table>';
            /*
			$grid = new jshowGrid_new("mehman","grid1");
			$grid->width = '99%';
			$grid->index_width = '20px';
			$grid->showAddDefault = FALSE;
			$grid->whereClause = "`reserve_id`='$reserve_id' ";
			for($i=0;$i<count($grid->columnHeaders);$i++)
				$grid->columnHeaders[$i] = null;
			$grid->columnHeaders[2] = 'نام';
			$grid->columnHeaders[3] = 'نام  خانوادگی';
			$grid->columnHeaders[9] = 'ملیت';
			$grid->columnLists[9]=loadMellait();
			$grid->columnHeaders[16] = 'نسبت';
			$grid->columnLists[16]=loadNesbat();
			//$grid->sortEnabled = TRUE;
			$b = FALSE;
			$grid->canEdit = $b;
			$grid->canAdd = $b;
			$grid->canDelete = $b;
			$grid->intial();
		   	$grid->executeQuery();
			$out = $grid->getGrid();*/
		}
		else
			$output='شما به این رزرو دسترسی ندارید';
	}
	$output .='</tbody></table></div></div>'.$out;
	$dis = ($vaziat==4 or $vaziat==5) ? '':'none';
	$tarikh_view = ($vaziat==4 or $vaziat==5) ? audit_class::hamed_pdate($r_tmp->end_fix_date):'';
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>میرسمیعی</title>
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
    
	
	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
	
</head>
<body>
    <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
    
			<!-- /SIDEBAR -->
		<div id="main-content">
			<div class="container">
				
                
                <div class="row" style="margin-right:0px;margin-left:0px;">
                <div class="col-lg-12" style="padding:0px;">
                    <div class="panel panel-default" style="border: 1px solid #ffae2e;">
                        <div class="panel-heading" style="background-color:#ffae2e;color:white;padding:1px;">
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-group"></i>اطلاعات پذیرنده و مهمان ها</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <?php echo $output.' '.$msg; ?>
                            <?php echo $scr; ?>
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