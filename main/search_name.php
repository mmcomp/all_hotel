<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart1 = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart1.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	if (isset($_REQUEST["hotel_id"]))
                $hotel_id_new = $_REQUEST["hotel_id"];
	else
		$hotel_id_new = -1;
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
		$combo_hotel .= "<select class='form-control inp' id=\"hotel_id\" name=\"hotel_id\" onchange=\"document.getElementById('selHotel').submit();\"><option value=\"-1\">\n&nbsp</option>";
		mysql_class::ex_sql("select * from `hotel` where `id` in $shart1 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$hotel_id_new)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_hotel .= "<option value=\"".(int)$r["id"]."\" $select   >";
		        $combo_hotel .= $r["name"]."";
		        $combo_hotel .= "</option>";
		}
		$combo_hotel .= "</select>";
	$combo_hotel .= "</form>";
	$msg = '';
	$isAdmin = $se->detailAuth('all');
	$is_admin =$isAdmin;
	$reserve_id = ((isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:0);
	$f_name = ((isset($_REQUEST['f_name']))?$_REQUEST['f_name']:'');
	$l_name = ((isset($_REQUEST['l_name']))?$_REQUEST['l_name']:'');
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['aztarikh']," 00:00:00"):'0000-00-00');
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');
	$user_id=-1;
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$curtime = strtotime(date("Y-m-d"));
	$az = strtotime($aztarikh);
	$ta = strtotime($tatarikh);
	/*
	if($az - $curtime <= 24*60*60 && !$is_admin)
	{
		$aztarikh = date("Y-m-d",$curtime);
		$tatarikh = date("Y-m-d",$curtime);
	}
	else
	{
	*/
	$aztarikh = explode(" ",$aztarikh);
	$aztarikh = $aztarikh[0];
	$tatarikh = explode(" ",$tatarikh);
	$tatarikh = $tatarikh[0];
	$day = date("Y-m-d");
	//}
	
	
	//var_dump($tmp);
	//var_dump(room_det_class::loadDetByReserve_id($tmp[0]));
	//var_dump(room_det_class::loadNamesByReserve_id($tmp[0]));
	//var_dump(room_det_class::loadKhadamatByReserve_id($tmp[0]));
	$nafar = 0;
	$mablagh = 0;
	$mablagh_tmp = 0;
	$mablagh_kol = 0;
	if($se->detailAuth('super'))
		$output = '
        <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">شماره رزرو</th>
                                            <th style="text-align:right;">هتل</th>
                                            <th style="text-align:right;">نام</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
	else
		$output = '
        <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">شماره رزرو</th>
                                            <th style="text-align:right;">حساب</th>
                                            <th style="text-align:right;">هتل</th>
                                            <th style="text-align:right;">آژانس</th>
                                            <th style="text-align:right;">نام</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                            <th style="text-align:right;">تعداد نفرات</th>
                                            <th style="text-align:right;">قیمت هتل</th>
                                            <th style="text-align:right;">جمع کل</th>
                                            <th style="text-align:right;">تاریخ ورود</th>
                                            <th style="text-align:right;">تاریخ خروج</th>
                                            <th style="text-align:right;">توضیحات</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
	$styl = 'class="odd"';
	$co_room = 0;
	$sum_room = 0;
	$khorooj = '';
	$rooms_id = "(";
	$rooms_ids = "";
	if ($hotel_id_new!=-1)
	{
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$hotel_id_new' order by `name`",$q);
		while($r = mysql_fetch_array($q))
			$rooms_id .= $r["id"].',';
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
	}
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
	{
		$tmp = room_det_class::loadReserve_id($aztarikh,$tatarikh,$user_id,$isAdmin,$f_name,$l_name,$reserve_id);			
		for($i=0;$i<count($tmp);$i++)
		{
			$rese_id = $tmp[$i];
			$reserve_garanty_color = hotel_garanti_class::canViewReserve_color($rese_id);
			if (($se->detailAuth('garanti')) || ($se->detailAuth('reserve')))
			//if ($se->detailAuth('garanti'))
				$reserve_garanty = hotel_garanti_class::canViewReserve($rese_id);
			else
				$reserve_garanty = FALSE;
			if (!($reserve_garanty))
			{
				if (($rooms_ids!='')&&($rooms_ids!=')'))
				{
					mysql_class::ex_sql("select `reserve_id` from `room_det` where `room_id` in $rooms_ids and  `reserve_id`='$rese_id'",$q_res);
					if($r_res = mysql_fetch_array($q_res))
					{
						$tmp[$i] = $r_res['reserve_id'];
						/*$sanad_reserve_id = sanad_reserve_class::loadSanadId($tmp[$i]);
						$toz_sanad = sanad_class::sanadToz($sanad_reserve_id);*/
						$reserve = new reserve_class($tmp[$i]);
						//$sanad_reserve_id = sanad_reserve_class::loadSanadId($tmp[$i]);
						$toz_sanad = $reserve->hotel_reserve->extra_toz;
						$styl = 'class=""';
						mysql_class::ex_sql("select `reserve_id` from `mehman` where `reserve_id` = $tmp[$i]",$qu);
						while($row= mysql_fetch_array($qu))
						{
							$styl = 'class="odd"';
							if($i%2 == 0 )
								$styl = 'class="even"';
						}
						$horel_reserve = new hotel_reserve_class;
						$horel_reserve->loadByReserve($tmp[$i]);
						$room = room_det_class::loadDetByReserve_id($tmp[$i]);
						$rooms = '';
						$styl1 = $styl.' '.$reserve_garanty_color;
						for($j=0;$j<count($room['rooms']);$j++)
						{
							$co_room++;
							 $khorooj = ((room_det_class::loadKhoroojByReserve_id($tmp[$i],$room['rooms'][$j]['room_id']))?(room_det_class::loadKhoroojByReserve_id($tmp[$i],$room['rooms'][$j]['room_id'])):'');
							if ($khorooj!='')
								$time_kh = date("H:i",strtotime($khorooj[0]));
							else
								$time_kh='';
							$tmp_room = new room_class($room['rooms'][$j]['room_id']);
							if ((reserve_class::isPaziresh($tmp[$i],$room['rooms'][$j]['room_id']))&&((!reserve_class::isKhorooj($tmp[$i],$room['rooms'][$j]['room_id']))))
								$rooms.='<span style="color:#000000;background:#f1ca00"> '.$tmp_room->name.'</span>'.(($j<count($room['rooms'])-1)?' , ':'');
							elseif (($day == date("Y-m-d",strtotime($room['rooms'][$j]['tatarikh'])))&&(reserve_class::isKhorooj($tmp[$i],$room['rooms'][$j]['room_id'])))
								$rooms.='<span title='.$time_kh.' style="color:#ffffff;background:#0c5e06"> '.$tmp_room->name.'</span>'.(($j<count($room['rooms'])-1)?' , ':'');
							elseif ((reserve_class::isKhorooj($tmp[$i],$room['rooms'][$j]['room_id'])))
								$rooms.=' <span title='.$time_kh.' style="color:#ffffff;background:#0c5e06"> '.$tmp_room->name.'</span>'.(($j<count($room['rooms'])-1)?' , ':'');
							else
								$rooms.=$tmp_room->name.(($j<count($room['rooms'])-1)?' , ':'');
						}
						$sum_room = $sum_room + $co_room;
						$co_room = 0; 
						$nafar_det = 0;
						foreach($room['rooms'] as $r_t_m)
							$nafar_det += $r_t_m['nafar'];
						$nafar = $nafar + $nafar_det;//$room['rooms'][0]['nafar'];
						$mablagh = $mablagh + $horel_reserve->m_hotel;
						$mablagh_tmp = $horel_reserve->m_belit+$horel_reserve->m_hotel;
						$mablagh_kol = $mablagh_kol + $mablagh_tmp;
						$name = room_det_class::loadNamesByReserve_id($tmp[$i]);			
						$khadamat = room_det_class::loadKhadamatByReserve_id($tmp[$i]);
						if($se->detailAuth('super'))
						{
							$output .="<tr><td>".$tmp[$i]."</td>";
							$output .="<td>".$room['rooms'][0]['hotel']."</td><td>".$name[0]."</td><td>$rooms</td></tr>";
						}
						else
						{
							$output .="<tr><td>".$tmp[$i]."</td>";
							$ajName = hotel_reserve_class::loadAjName_habibi($tmp[$i]);
							$output .="<td><a target='_blank' href='report.php?req=".$tmp[$i].".".$_SESSION['moshtari_id']."' >مشاهده</a></td>";
							$output .="<td>".$room['rooms'][0]['hotel']."</td><td>$ajName</td><td>".$name[0]."</td><td>$rooms</td><td>".$nafar_det."</td><td>".monize($horel_reserve->m_hotel)."</td>";
							$output .="<td>".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."</td>";
							$output .="<td>".audit_class::hamed_pdate($room['rooms'][0]['aztarikh'])."</td>";
							if (($day == date("Y-m-d",strtotime($room['rooms'][0]['tatarikh'])))&&(!reserve_class::isKhorooj($tmp[$i])))
								$output .="<td style='background-color:#db4a38;'>".audit_class::hamed_pdate($room['rooms'][0]['tatarikh'])."</td></tr>";
							elseif (($day == date("Y-m-d",strtotime($room['rooms'][0]['tatarikh'])))&&(reserve_class::isKhorooj($tmp[$i])))
								$output .="<td style='color:#ffffff;background:#0c5e06;'>".audit_class::hamed_pdate($room['rooms'][0]['tatarikh'])."</td></tr>";
							else
								$output .='<td>'.audit_class::hamed_pdate($room['rooms'][0]['tatarikh']).'</td>';
							$output .='<td>'.$toz_sanad.'</td></tr>';
						}
					}
				}
			}
		}
		$mablagh = monize($mablagh);
		$mablagh_kol = monize($mablagh_kol);
		if(!($se->detailAuth('super')))
			$output .="<tr $styl ><td>جمع</td><td>--</td><td>--</td><td>--</td><td>--</td><td>$sum_room</td><td>$nafar</td><td>$mablagh</td><td>$mablagh_kol</td><td>--</td><td>--</td><td>--</td></tr>";
	}
	$output .='</tbody></table>';
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>جستجوی پیشرفته</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-search"></i>جستجوی پیشرفته</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                           
                               <div class="col-md-3" style="margin-bottom:5px;">
                                   <label class="col-md-3 control-label">هتل:</label> 
                                   <div class="col-md-9"><?php echo $combo_hotel;?></div>
                               </div>
                            <form id='frm1'  method='GET' >
                               <div class="col-md-3" style="margin-bottom:5px;">
                                   <label class="col-md-3 control-label">شماره رزرو:</label> 
                                   <div class="col-md-9"><input class='form-control inp' name='reserve_id' id='reserve_id' value="<?php echo ((isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:''); ?>" ></div>
                               </div>
                               <div class="col-md-3" style="margin-bottom:5px;">
                                   <label class="col-md-3 control-label">نام:</label> 
                                   <div class="col-md-9"><input class='form-control inp' name='f_name' id='f_name' value="<?php echo ((isset($_REQUEST['f_name']))?$_REQUEST['f_name']:''); ?>" ></div>
                               </div>
                               <div class="col-md-3" style="margin-bottom:5px;">
                                   <label class="col-md-3 control-label">نام خانوادگی:</label> 
                                   <div class="col-md-9"><input class='form-control inp' name='l_name' id='l_name' value="<?php echo ((isset($_REQUEST['l_name']))?$_REQUEST['l_name']:''); ?>" ></div>
                               </div>
                               <div class="col-md-3" style="margin-bottom:5px;">
                                   <label class="col-md-3 control-label">از تاریخ:</label> 
                                   <div class="col-md-9"><input value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh'  class='form-control inp' id="datepicker1" />	</div>
                               </div>
                               <div class="col-md-3" style="margin-bottom:5px;">
                                   <label class="col-md-3 control-label">تا تاریخ:</label> 
                                   <div class="col-md-9"><input value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='form-control inp' id="datepicker2" /></div>
                               </div>
                               <div class="col-md-3" style="margin-bottom:5px;">
                                   <input type='hidden' name='mod' id='mod' value='1' >
                                   <input type='hidden' name='hotel_id' id='hotel_id' value="<?php echo $hotel_id_new;?>">
                                   <div class="col-md-12"><button class="btn btn-info col-md-12 pull-left" onclick="send_search();">جستجو</button></div>
                                </div>
                               <?php echo $output.' '.$msg; ?>
                            </form>
                        </div>
                        <table>
				<tr>
					<td style="color:black;background-color:#f1ca00;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px;">اتاق های پذیرش شده</td>
					<td style="color:black;background-color:#0c5e06;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px;">اتاق های خارج شده(تحویل هتل گردیده)</td>
					<td style="color:black;background-color:#db4a38;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px;">خروجی های امروز</td>
					<td style="color:black;background-color:#009999;	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px;">گارانتی</td>
				</tr>
			</table>
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
    <script src="<?php echo $root ?>js/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-datepicker.fa.js"></script>
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
        
         function send_search()
		{
			if( (document.getElementById('reserve_id').value)=='' && (document.getElementById('f_name').value)=='' &&  (document.getElementById('l_name').value)=='' &&  (document.getElementById('datepicker1').value)=='' &&  (document.getElementById('datepicker2').value)=='')
			{
				alert('لطفا یکی از موارد را وارد کنید');
			}
			else
			{
				document.getElementById('mod').value= 2;
				document.getElementById('frm1').submit();
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