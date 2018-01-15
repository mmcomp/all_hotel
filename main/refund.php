<?php

	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$msg = '';
	$is_admin = $se->detailAuth('all');
	$f_name = ((isset($_REQUEST['f_name']))?$_REQUEST['f_name']:'');
	$l_name = ((isset($_REQUEST['l_name']))?$_REQUEST['l_name']:'');
	$reserve_id = ((isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:0);
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):'0000-00-00');
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):'0000-00-00');
	$user_id=-1;
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$curtime = strtotime(date("Y-m-d"));
	$az = strtotime($aztarikh);
	$ta = strtotime($tatarikh);
	if(!$is_admin && $az - $curtime <= 24*60*60)
	{
		$aztarikh = date("Y-m-d",$curtime);
                $tatarikh = date("Y-m-d",$curtime);
	}
	$output = '
    
     <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            
                                            <th style="text-align:right;">شماره-رزرو</th>
                                            <th style="text-align:right;">هتل</th>
                                            <th style="text-align:right;">نام و نام خانوداگی</th>
                                            <th style="text-align:right;">تعداد نفرات</th>
                                            <th style="text-align:right;">اتاق</th>
                                            <th style="text-align:right;">قیمت هتل</th>
                                            <th style="text-align:right;">جمع کل</th>
                                            <th style="text-align:right;">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>';


	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
	{
		
		//($aztarikh,$tatarikh,$user_id,$isAdmin,$fname,$lname,$reserve_id,$just_date=TRUE)
		$aztarikh = explode(" ",$aztarikh);
		$aztarikh = $aztarikh[0];
		$tatarikh = explode(" ",$tatarikh);
		$tatarikh = $tatarikh[0];
		$tmp = room_det_class::loadReserve_id($aztarikh,$tatarikh,$user_id,($_SESSION['daftar_id']==49),$f_name,$l_name,$reserve_id);
//var_dump($tmp);
		for($i=0;$i<count($tmp);$i++)
		{
			//$room = room_det_class::loadDetByReserve_id($tmp[$i]);
			//$name = room_det_class::loadNamesByReserve_id($tmp[$i]);
			//$khadamat = room_det_class::loadKhadamatByReserve_id($tmp[$i]);
			$room_det_new = room_det_class::loadByReserve($tmp[$i]);
			$room_det_new = $room_det_new[0];
			$horel_reserve = new hotel_reserve_class;
			$horel_reserve->loadByReserve($tmp[$i]);
			//for($j=0;$j<count($room['rooms']);$j++)
			for($j=0;$j<count($room_det_new);$j++)
			{
				//$reserve_garanty = hotel_garanti_class::canViewReserve($tmp[$i]);
				if (!($se->detailAuth('admin')))
					$reserve_garanty = hotel_garanti_class::canViewReserve($reserve_id);
				else
					$reserve_garanty = FALSE;
				if (!($reserve_garanty))
				{
					$room_det_tmp = new room_class($room_det_new[$j]->room_id);
					$room_det_typ = new room_typ_class($room_det_tmp->room_typ_id);
					$hotel_tmp = new hotel_class($room_det_tmp->hotel_id);
					$reserve_id = $tmp[$i];
					$output .="<tr>";
					$output .="<td>".$tmp[$i]."</td>";
					$output .="<td>".$hotel_tmp->name."</td><td>".$horel_reserve->fname.' '.$horel_reserve->lname."</td><td>".$room_det_new[$j]->nafar."</td><td>".$room_det_tmp->name.' '.$room_det_typ->name."</td><td>".monize($horel_reserve->m_hotel)."</td>";
					$output .="<td ".(($j==0)?"id='td_".$tmp[$i]."'":'')." >".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."</td>";                    
                    if($j==0)
						$output .="<td><a onclick=\"deleteCancel(".$reserve_id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> کنسل</button></a></td>";
					else
						$output .='<td>&nbsp;</td>';
                    
                    $output .="</tr>";
				}
				else
					//$output .= "<tr><td></td><td colspan='7'>"."شما به این شماره رزرو دسترسی ندارید"."</td></tr>";
					$output .= "";
			}
		}
	}
$output.="</tbody></table>";	
	if(isset($_REQUEST['mablagh']) && isset($_REQUEST['reserve_id']) && (int)$_REQUEST['mod']==1 )
	{
		$reserve_id = (int)$_REQUEST['reserve_id'];
		$ghimat = umonize($_REQUEST['mablagh']);
		$toz = $_REQUEST['toz'];
		$refunded = room_det_class::refundReserve($reserve_id,$toz);
		$reserve_id = abs($reserve_id) * (-1);
		for($i=0;$i<count($refunded);$i++)
		{
			mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$refunded[$i]."') ");
		}
		$msg = '<script type="text/javascript" >alert("کنسلی با موفقیت انجام شد");//window.location = "refund.php";</script>';
		log_class::add("refund",(int)$_SESSION['user_id'],"کنسلی رزرو ".$reserve_id);
	}
	


	if(isset($_REQUEST['mablagh']) && isset($_REQUEST['reserve_id']) && (int)$_REQUEST['mod']==1 )
	{
		$reserve_id = (int)$_REQUEST['reserve_id'];
		$ghimat = umonize($_REQUEST['mablagh']);
		$toz = $_REQUEST['toz'];
		$refunded = room_det_class::refundReserve($reserve_id,$toz);
		$reserve_id = abs($reserve_id) * (-1);
		for($i=0;$i<count($refunded);$i++)
		{
			mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$refunded[$i]."') ");
		}
		$msg = '<script type="text/javascript" >alert("کنسلی با موفقیت انجام شد");//window.location = "refund.php";</script>';
		log_class::add("refund",(int)$_SESSION['user_id'],"کنسلی رزرو ".$reserve_id);
	}
	




$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>کنسل کردن رزرو</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-times"></i>کنسل کردن رزرو</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <form id='frm1'  method='GET' >
                               <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">شماره رزرو:</label> 
                                    <div class="col-md-8"><input class='form-control inp' name='reserve_id' id='reserve_id' value="<?php echo ((isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:''); ?>" >
                                    </div>
                                </div>
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">نام:</label> 
                                    <div class="col-md-8"><input class='form-control inp' name='f_name' id='f_name' value="<?php echo $f_name; ?>" >
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">نام خانوادگی:</label> 
                                    <div class="col-md-8"><input class='form-control inp' name='l_name' id='l_name' value="<?php echo $l_name; ?>" >
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">از تاریخ:</label> 
                                    <div class="col-md-8"><input value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh' class='form-control inp' style='direction:ltr;' id="datepicker1" />	
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">تا تاریخ:</label> 
                                    <div class="col-md-8"><input value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='form-control inp' style='direction:ltr;' id="datepicker2" />
                                    
                                    </div>
                                </div>
                                <input type='hidden' name='mod' id='mod' value='1' >
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left" onclick="send_search();">جستجو</button>
                                    </div>
                                </div>
                            </div>
                              
                          </form>
                              <?php echo $output; ?>
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

<div class="modal fade" id="deleteCancel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">کنسلی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input class="form-control inp" type="hidden" name="rid" id="rid" >
												توضیحات : <input name="tozih" class="form-control inp" style='width:300px;' />         
												مبلغ : <input name="mablagh" class="form-control inp" style='width:300px;' />
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinal()" type="button" class="btn btn-danger" data-dismiss="modal">کنسل</button>
                </div>
            
        </div>
    </div>
</div>    
    
    
	
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
        function deleteCancel(reserve_id){
            StartLoading();
            $("input[name='rid']").val(reserve_id);
            $('#deleteCancel').modal('show');
            StopLoading();
            
        }
        function deleteFinal(){
            StartLoading();
            var rid = $("input[name='rid']").val();
            var tozih = $("input[name='tozih']").val();
            var mablagh = $("input[name='mablagh']").val();
           $.post("refundDeleteAjax.php",{rid:rid,tozih:tozih,mablagh:mablagh},function(data){
               StopLoading();
						 
               if(data=="0")
                   alert("خطا در حذف");
               if(data=="1"){
                   alert("حذف با موفقیت انجام شد");
                   location.reload();
               }
							 
 						 console.log(data);
                                          
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