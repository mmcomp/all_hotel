<?php
session_start();
include_once("../kernel.php");
function pdate1($inp)
{
	return(audit_class::hamed_pdate($inp));
}
function bedBes($inp)
{
	return(((int)$inp == -1)?'بدهکار':'بستانکار');
}
$GLOBALS['msg'] = '';
$out = '';
$req = ((isset($_REQUEST['req']))?$_REQUEST['req']:'');
$room_id = ((isset($_REQUEST['room_id']))?$_REQUEST['room_id']:'');
if($room_id==''){
	$req_tmp = explode('.',$req);
	if(count($req_tmp)==2){
		$room_id = (int)$req_tmp[1];
	}
}
$daftar = new daftar_class($_SESSION['daftar_id']);
$bestakar_moeen = $daftar->sandogh_moeen_id;
// var_dump($daftar);
// die();
$bestakari = 0;
$jam_koll = 0;
if($req != '')
{
	$reserve_id = (int)$req;
// 	$query = "select `id` from `moeen` where `name`='دریافت نقدی و کارت خوان'";
// 	mysql_class::ex_sql($query,$q);
// 	if($r = mysql_fetch_array($q)){
// 		$bestakar_moeen = $r['id'];
	if($bestakar_moeen>0){
	// 	echo $bestakar_moeen;
	// 	die();
		$query = "select SUM(`mablagh`) as `jam` from `sanad` where `tozihat` like '%رزرو $reserve_id%' and `moeen_id` = $bestakar_moeen and `typ` = -1";
// 		echo $query;
// 		die();
		mysql_class::ex_sql($query,$q);
		if($r = mysql_fetch_array($q)){
			$bestakari = (int)$r['jam'];
		}
	}
	$reserve = new reserve_class($reserve_id);
// 		var_dump($reserve);
	$hotel = new hotel_class($reserve->hotel_id);
	if($reserve->id>0)
	{
		$GLOBALS['msg'] = '<h2>آقا/خانم '.$reserve->hotel_reserve->fname.' '.$reserve->hotel_reserve->lname.' اطلاعات مالی یافت نشد.</h2>';
		$room_shart = '';
		//for($i = 0;$i < count($reserve->room_det);$i++)
		//{
			//$room_tmp = new room_class($reserve->room_det[$i]->room_id);
// 			$room_tmp = new room_class($room_id);
// 			$moeen_id = $room_tmp->moeen_id;
// 			if($moeen_id>0)
// 			{
// 				$aztarikh = date("Y-m-d",strtotime($reserve->room_det[0]->aztarikh));
// 				$tatarikh = date("Y-m-d",strtotime($reserve->room_det[0]->tatarikh));
// 				$room_shart .="( `moeen_id` = $moeen_id and DATE(`tarikh`)>='$aztarikh' and DATE(`tarikh`) <= '$tatarikh' )";
// 			}
		//}
// 			if($room_shart == '')
// 				$room_shart = '1=0';
// 			$q = null;
// 			$query = "select SUM(`mablagh`*`typ`) as `jam` from `sanad` where $room_shart";
		$query = "select SUM(`mablagh`) as `jam` from `sanad` where `tozihat` like '%رزرو $reserve_id%' /*and `tozihat` like '%{$reserve->hotel_reserve->lname}%'*/ and `typ`=-1 and moeen_id!=$bestakar_moeen";
// 		echo $query."\n";
		$jam_stat = 'تسویه';
		mysql_class::ex_sql($query,$q);
		if($r = mysql_fetch_array($q))
			$jam_koll = (int)$r['jam']-$bestakari;
		if($jam_koll>0){
			$jam_stat = 'بدهکار';
		}else if($jam_koll<0){
			$jam_stat = 'بستانکار';
		}
//       echo "JAM = $jam_kol<br/>";     


					$out.='<div class="box border orange">

								<div class="box-body">
									<table class="table table-hover">
										<thead>
											<tr>
											<th style="text-align:right">ردیف</th>
											<th style="text-align:right">تاریخ</th>
											<th style="text-align:right">حساب</th>
											<th style="text-align:right">بدهکار / بستانکار</th>
																							<th style="text-align:right">توضیحات</th>
																							<th style="text-align:right">مبلغ</th>
											</tr>
										</thead>
										<tbody>';
		$query = "select shomare_sanad,sanad.typ,tozihat,tarikh,name,mablagh,moeen.name mn from `sanad` left join moeen on (moeen.id=moeen_id) where `tozihat` like '%رزرو $reserve_id%' /*and `tozihat` like '%".$reserve->hotel_reserve->lname."%'*/ and `sanad`.`typ`=-1 and sanad.moeen_id!=$bestakar_moeen";
// 						echo $query."\n";
		mysql_class::ex_sql($query,$ss);
		$i=1;
		while($r = mysql_fetch_array($ss)){
			$tarikh = jdate("Y/m/d",strtotime($r['tarikh']));
			$tozihat = $r['tozihat'];
			$mablagh = number_format($r['mablagh']);
			$jam_kol = ($r['typ']==1)?'بستانکار':'بدهکار';
			if(fmod($i,2)!=0){
				$out.='<tr class="odd">
						<td>'.$i.'</td>
						<td>'.$tarikh.'</td>
						<td>'.$r['mn'].'</td>
						<td>'.$jam_kol.'</td>
						<td>'.$tozihat.'</td>
						<td>'.$mablagh.'</td>
				</tr>';
				$i++;
			}
			else{
				$out.='
				<tr class="even">
						<td>'.$i.'</td>
						<td>'.$tarikh.'</td>
						<td>'.$r['mn'].'</td>
						<td>'.$jam_kol.'</td>
						<td>'.$tozihat.'</td>
						<td>'.$mablagh.'</td>
				</tr>
				';
				$i++;
			}
		}
		$query = "select shomare_sanad,sanad.typ,tozihat,tarikh,name,mablagh,moeen.name mn from `sanad` left join moeen on (moeen.id=moeen_id) where  `sanad`.`tozihat` like '%رزرو $reserve_id%' and `sanad`.`moeen_id` = $bestakar_moeen and `sanad`.`typ` =-1";
// 		echo $query."\n";
// 		die();
		
		mysql_class::ex_sql($query,$ss);
// 		$i=1;
		while($r = mysql_fetch_array($ss)){
			$tarikh = jdate("Y/m/d",strtotime($r['tarikh']));
			$tozihat = $r['tozihat'];
			$mablagh = number_format($r['mablagh']);
			$jam_kol = 'بستانکار';//($r['typ']==1)?'بستانکار':'بدهکار';
			if(fmod($i,2)!=0){
				$out.='<tr class="odd">
						<td>'.$i.'</td>
						<td>'.$tarikh.'</td>
						<td>'.$r['mn'].'</td>
						<td>'.$jam_kol.'</td>
						<td>'.$tozihat.'</td>
						<td>'.$mablagh.'</td>
				</tr>';
				$i++;
			}
			else{
				$out.='
				<tr class="even">
						<td>'.$i.'</td>
						<td>'.$tarikh.'</td>
						<td>'.$r['mn'].'</td>
						<td>'.$jam_kol.'</td>
						<td>'.$tozihat.'</td>
						<td>'.$mablagh.'</td>
				</tr>
				';
				$i++;
			}
		}
		$out.='</tbody></table></div></div>';
		
		/*$grid = new jshowGrid_new("sanad","grid1");
		$grid->setERequest(array('req'=>$req));
		$grid->whereClause = " $room_shart";
		$grid->columnHeaders[0] = null;
		$grid->columnHeaders[1] = null;
		$grid->columnHeaders[2] = null;
		$grid->columnHeaders[3] = null;
		$grid->columnHeaders[4] = null;
		$grid->columnHeaders[5] = null;
		$grid->columnHeaders[6] = null;
		$grid->columnHeaders[7] = null;
		$grid->columnHeaders[8] = null;
		$grid->columnHeaders[9] = 'تاریخ';
		$grid->columnHeaders[10] = null;
		$grid->columnHeaders[11] = 'بدهکار/بستانکار';
		$grid->columnHeaders[12] = 'توضیحات';
		$grid->columnHeaders[13] = null;
		$grid->columnHeaders[14] = 'مبلغ';
		$grid->columnFunctions[9] = 'pdate1';
		$grid->columnFunctions[11] = 'bedBes';
		$grid->columnFunctions[14] = 'monize';
		$grid->sortEnabled = TRUE;
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
		$grid->intial();
		$grid->executeQuery();
		$out = $grid->getGrid();*/
		$GLOBALS['msg'] = '<h4>آقا/خانم '.$reserve->hotel_reserve->fname.' '.$reserve->hotel_reserve->lname.' جمع حساب شما : '.monize($jam_koll).' ریال '.$jam_stat;
		$GLOBALS['msg'] .= '<button class="btn btn-info pull-left" onclick="window.open(\'report_print.php?req='.$req.'\', \'\', \'width=820,height=1000\');"><i class="fa fa-print"></i></button></h4>';
	}
	else
		$GLOBALS['msg'] = 'کد مشتری صحیح نمی باشد.';
}
else
	die($conf->access_deny);
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش حساب</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>گزارش حساب</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <!--
			<form id="frm1">
				کد مشتری خود را وارد کنید : <input type="text" id="req" name="req" value="<?php echo $req; ?>" />
				<input type="submit" value="انتخاب" class='inp'/>
			</form>
			-->
			<br/>
			<?php	echo $GLOBALS['msg'].'<br/>'.$out;?>
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