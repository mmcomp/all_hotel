<?php
session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(!$se->detailAuth('all') && !isset($_REQUEST['sandogh_id']))
		die(lang_fa_class::access_deny);
	function loadRoom($room_id)
	{
		$out = '&nbsp;';
		$r = new room_class((int)$room_id);
		if($r->id > 0)
		{
			$h = new hotel_class($r->hotel_id);
			$out = $r->name.'('.$h->name.')';
		}
		return($out);
	}
	function factorResid($inp)
	{
		$out = '&nbsp;';
		if((int)$inp == 1)
			$out = 'فاکتور';
		else if((int)$inp == -1)
			$out = 'رسید';
		return($out);
	}
	function loadFactor($id)
	{
		$out = '&nbsp;';
		$id = (int)$id;
		$sandogh_id = ((isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1);
		mysql_class::ex_sql("select `factor_shomare`,`sandogh_item_id`,`room_id`,`reserve_id`,`typ`,`en` from `sandogh_factor` where `id`=$id",$q);
		if($r = mysql_fetch_array($q))
		{
			$out = '<button class="inp" onclick="wopen(\'sandogh_factor.php?factor_shomare_req='.$r['factor_shomare'].'&canChange='.(($r['en']==0)?1:0).'&sandogh_id='.$sandogh_id.'&room_id='.$r['room_id'].'&reserve_id='.$r['reserve_id'].'&isFactor='.$r['typ'].'&get_type='.(((int)$r['room_id']>0)?'1':'-1').'&r=\'+Math.random(),\'\',600,500);" >مشاهده '.(((int)$r['typ']==1)?'فاکتور':'رسید').' شماره '.$r['factor_shomare'].'</button>';
		}
		return($out);
	}
	function loadTarikh($tarikh)
	{
		$out = $tarikh;
		if($tarikh != '')
			$out = jdate("H:i d / m / Y",strtotime($tarikh));
		return($out);
	}
	function loadReserve($id)
	{
		$out = 'نامعلوم';
		$id = (int)$id;
		mysql_class::ex_sql("select `typ`,`reserve_id` from `sandogh_factor` where `id` = $id",$q);
		if($r = mysql_fetch_array($q))
		{
			$reserve_id = (int)$r['reserve_id'];
			if($reserve_id <= 0 && (int)$r['typ'] == 1)
				$out = 'نقدی';
			else if((int)$r['typ'] == -1)
				$out = 'رسید';
			else
				$out = $reserve_id;
		}
		return($out);
	}
	$user_id = (int)$_SESSION['user_id'];
	$sandogh = user_class::loadSondogh($user_id,$se->detailAuth('all'));
	$sandogh_id = ((isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1);
	$isFactor = ((isset($_REQUEST['isFactor']) && (int)$_REQUEST['isFactor']==1)?TRUE:FALSE);
	$isHamedFactor = ((isset($_REQUEST['isFactor']) && (int)$_REQUEST['isFactor']==0)?TRUE:FALSE);
	$en = ((isset($_REQUEST['en']) && (int)$_REQUEST['en']==0)?0:1);
	if($se->detailAuth('all'))
	{
		$combo = "<option value=\"-1\"></option>\n";
		for($i = 0;$i < count($sandogh); $i++)
		{
			$s = new sandogh_class((int)$sandogh[$i]);
			if($s->id>0)
			{
				$h = new hotel_class($s->hotel_id);
				$sel = '';
				if($s->id == $sandogh_id)
				{
					$sandogh_items = null;
					mysql_class::ex_sql("select `id` from `sandogh_item` where `sandogh_id` = $sandogh_id",$q);
					while($r = mysql_fetch_array($q))
						$sandogh_items[] = (int)$r['id'];
					$sel = 'selected="selected"';
				}
				$combo .= "<option value=\"".$s->id."\" $sel>".$s->name."(".$h->name.")"."</option>\n";
			}
		}
	}
	else
	{
		$sandogh_items = null;
		$ssss = new sandogh_class($sandogh_id);
		$combo = "<option value=\"".$ssss->id."\" >".$ssss->name."</option>";
	}
	$factor = ($isFactor)?'selected="selected"':'';
	$resid = (!$isFactor)?'selected="selected"':'';
	$dayemi = ($en == 1)?'selected="selected"':'';
	$movaghat = ($en == 0)?'selected="selected"':'';
	$hame_factor = ($isHamedFactor)?'selected="selected"':'';;
	$hame = '';
	$out = "<form id=\"frm\">صندوق:<select class='form-control inp' id=\"sandogh_id\" onchange=\"refresh_frm();\" name=\"sandogh_id\">$combo</select>سند:<select class='form-control inp' onchange=\"refresh_frm();\" name=\"isFactor\" id=\"isFactor\"><option $factor value = '1'>فاکتور</option><option $resid value = '-1'>رسید</option></select>وضعیت:<select class='form-control inp' onchange=\"refresh_frm();\" name=\"en\" id=\"en\"><option $dayemi value = '1'>دائمی</option><option $movaghat value = '0'>موقت</option></select></form><br/>";
	if($sandogh_id > 0 && ($sandogh_items != null || !$isFactor))
	{
		if($isFactor && $sandogh_items != null)
			$sandogh_items = ' and `sandogh_item_id` in ('.implode(',',$sandogh_items).') and `typ` = 1 ';
		else if($isFactor && $sandogh_items == null)
			$sandogh_items = ' 1=0 ';
		else if(!$isFactor)
			$sandogh_items = " and `sandogh_item_id` = $sandogh_id and `typ` = -1";
	        $grid = new jshowGrid_new("sandogh_factor","grid1");
		$grid->index_width = "20px";
	      	$grid->whereClause = "`en` = $en $sandogh_items  ".((!$se->detailAuth('all'))?"and `user_id` = $user_id":'')." group by `factor_shomare`";
	        $grid->columnHeaders[0] = null;
		$grid->columnHeaders[1] = 'رزرو';
		$grid->columnHeaders[2] = 'اتاق';
		$grid->columnHeaders[3] = null;
		$grid->columnHeaders[4] = null;
		$grid->columnHeaders[5] = null;
		$grid->columnHeaders[6] = null;
		$grid->columnHeaders[7] = 'شماره';
		$grid->columnHeaders[8] = null;
		$grid->columnHeaders[9] = 'فاکتور/رسید';
		$grid->columnHeaders[10] = null;
		$grid->columnHeaders[11] = 'تاریخ/ساعت';
		$grid->addFeild('id');
		$grid->columnHeaders[12] = 'جزئیات';
		$grid->fieldList[1] = 'id';
		$grid->columnFunctions[1] = 'loadReserve';
		$grid->columnFunctions[2] = 'loadRoom';
		$grid->columnFunctions[9] = 'factorResid';
		$grid->columnFunctions[12] = 'loadFactor';
		$grid->columnFunctions[11] = 'loadTarikh';
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
        	$grid->intial();
	        $grid->executeQuery();
        	//$out .= $grid->getGrid();
        $out.='<table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">رزرو</th>
                                            <th style="text-align:right;">اتاق</th>
                                            <th style="text-align:right;">شماره</th>
                                            <th style="text-align:right;">فاکتور / رسید</th>
                                            <th style="text-align:right;">تاریخ / ساعت</th>
                                            <th style="text-align:right;">جزئیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        mysql_class::ex_sql("select * from `sandogh_factor` where `en` = $en $sandogh_items  ".((!$se->detailAuth('all'))?"and `user_id` = $user_id":'')." group by `factor_shomare`",$ss);
        $i=1;
		while($r = mysql_fetch_array($ss)){
            $reserve_id = ((int)$r['reserve_id']<=0)?'نقد':$r['reserve_id'];
            $room_id = $r['room_id'];
            mysql_class::ex_sql("select * from `room` where `id` = '$room_id'",$sss);
            $p = mysql_fetch_array($sss);
            $roomname = $p['name'];
            $type="";
            $typ = $r['typ'];
            if($typ==1)
                $type="فاکتور";
            if($typ==-1)
                $type="رسید";
            $tarikh = $r['tarikh'];
            $tar=jdate('Y/n/j',strtotime($tarikh));
            $factor_shomare = $r['factor_shomare'];
            if(fmod($i,2)!=0){
                $out.="<tr class='odd'>
                <td>".$i."</td>
                <td>".$reserve_id."</td>
                <td>".$roomname."</td>
                <td>".$factor_shomare."</td>
                <td>".$type."</td>
                <td>".$tar."</td>
                <td><a href='sandogh_factor.php?factor_shomare_req=".$factor_shomare."&canChange=".(($r['en']==0)?1:0)."&sandogh_id=".$sandogh_id."&room_id=".$room_id."&reserve_id=".$reserve_id."&isFactor=".$typ."&get_type=".(((int)$r['room_id']>0)?'1':'-1')."' target='_blank'> <button class='form-control inp' \">مشاهده ".(((int)$r['typ']==1)?"فاکتور":"رسید")." شماره ".$factor_shomare."</button></a></td>
                </tr>";
                $i++;
            }
            else{
                $out.="<tr class='even'>
                <td>".$i."</td>
                <td>".$reserve_id."</td>
                <td>".$roomname."</td>
                <td>".$factor_shomare."</td>
                <td>".$type."</td>
                <td>".$tar."</td>
                <td><a href='sandogh_factor.php?factor_shomare_req=".$factor_shomare."&canChange=".(($r['en']==0)?1:0)."&sandogh_id=".$sandogh_id."&room_id=".$room_id."&reserve_id=".$reserve_id."&isFactor=".$typ."&get_type=".(((int)$r['room_id']>0)?'1':'-1')."' target='_blank'> <button class='form-control inp' \">مشاهده ".(((int)$r['typ']==1)?"فاکتور":"رسید")." شماره ".$factor_shomare."</button></a></td>
                </tr>";
                $i++;
            }
        }
        $out.="</tbody></table>";
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش</title>
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
          function refresh_frm()
			{
				document.getElementById('frm').submit();
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>گزارش</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                           <?php	echo $out;?>
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