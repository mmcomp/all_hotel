<?php
session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$hotelList=daftar_class::hotelList((int)$_SESSION['daftar_id']);
		$shart = '';
		if($hotelList)
			$shart=' and ( `id`='.implode(" or `id`=",$hotelList).")";
		$out = '<select name="hotel_id" id="hotel_id" class="form-control inp" style="width:auto;" ><option value="-1">همه</option>';
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `moeen_id` > 0 $shart order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadTyp_se($typ)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`typ` from `sms_typ`",$q);
		while($r = mysql_fetch_array($q))
		{			
			$typ_1 = $r['typ'];
			$id = $r['id'];
			$sel = (($r['id']==$typ)?'selected="selected"':'');
			$out .="<option value='$id' $sel >$typ_1</option>\n";
		}
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function loadStat($inp)
	{
		$out = "";
		if ($inp==1)
			$out = 'ارسال موفقیت آمیز';
		else
			$out = 'ارسال نشده';
		return $out;
	}
	function loadTyp($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`typ` from `sms_typ` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['typ'];
		return $out;
	}
	$hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
	$typ = (isset($_REQUEST['typ']))?(int)$_REQUEST['typ']:-1;
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):date('Y-m-d H:i:s'));
	$aztarikh = date("Y-m-d 00:00:00",strtotime($aztarikh));
	$tatarikh = date("Y-m-d 23:59:59",strtotime($tatarikh));
	mysql_class::ex_sql("select `reserve_id`,`room_id` from `room_det` where `aztarikh`>='$aztarikh' and `tatarikh`<='$tatarikh'",$q);
	$res = '';
	while($r = mysql_fetch_array($q))
	{
		$r_id = $r['room_id'];
		mysql_class::ex_sql("select `hotel_id` from `room` where `id`='$r_id'",$q_room);
		if($r_room = mysql_fetch_array($q_room))
		{
			if ($hotel_id!=-1)
			{
				if (($r_room['hotel_id']==$hotel_id)&&($r['reserve_id']>0))
					$res .=($res==''? '':',' ).$r['reserve_id'];
			}
			else
			{
				if ($r['reserve_id']>0)
					$res .=($res==''? '':',' ).$r['reserve_id'];
			}
		}
	}
	if ($res!='')
		$shart = "`reserve_id` in ($res) and `sms_typ`='$typ'";
	else
		$shart = '1=0';
mysql_class::ex_sql("select * from `sms_send` where $shart order by `reserve_id`",$ss);
$i=1;
$out1 = '
        <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">شماره-رزرو</th>
                                            <th style="text-align:right;">متن پیامک</th>
                                            <th style="text-align:right;">وضعیت ارسال</th>
                                            <th style="text-align:right;">نوع پیامک</th>
                                            <th style="text-align:right;">تاریخ ارسال</th>
                                            <th style="text-align:right;">تعداد مرتبه ارسال</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
while($r = mysql_fetch_array($ss))
{
    $reserve_id = $r['reserve_id'];
    $sms_matn = $r['sms_matn'];
    $sms_vaz = $r['sms_vaz'];
    $sms_typ = $r['sms_typ'];
    $date_send = $r['date_send'];
    $cnt = $r['cnt'];
    if(fmod($i,2)!=0){
        $i++;
        $out1 .= "
        <tr class=\"odd\"><td>$i</td><td>$reserve_id</td><td>$sms_matn</td><td>$sms_vaz</td><td>$sms_typ</td><td>$date_send</td><td>$cnt</td></tr>
        ";
    }
    else{
        $i++;
        $out1 .= "
        <tr class=\"even\"><td>$i</td><td>$reserve_id</td><td>$sms_matn</td><td>$sms_vaz</td><td>$sms_typ</td><td>$date_send</td><td>$cnt</td></tr>
        ";
        
    }
}
$out1.="</tbody></table>";	
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش پیامک ارسال شده</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-envelope"></i>گزارش پیامک ارسال شده</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body"  style="overflow-x:scroll">
                           <form id='frm1'  method='GET' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">هتل:</label> 
                                    <div class="col-md-9"><?php echo loadHotel($hotel_id); ?></div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">نوع:</label> 
                                    <div class="col-md-8">
                                        <select class="form-control inp" name="typ" id="typ" >
                                            <?php echo loadTyp_se($typ); ?>	
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
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left" onclick="send_search();">جستجو</button></div>
                                </div>
                            </div>
                          </form>
                             <?php echo $out1; ?>
                          
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