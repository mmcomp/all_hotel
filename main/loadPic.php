<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadPic($inp)
	{
        mysql_class::ex_sql("select * from `room_pic` where `room_id` = '$inp' ",$ss);
        $ss1 = mysql_fetch_array($ss);
        $pics = $ss1['pic'];
        
		$out = "<img height=\"60px\"  src=\"$pics\" style=\"cursor:pointer;\" onclick=\"window.open('$pics','',500,500);\" />";
		return($out);
	}
	function delete_item($inp)
	{
		$pic = new room_pic_class($inp);
		mysql_class::ex_sqlx("delete from `room_pic` where `id`=$inp");
		unlink($pic->pic);
	}
	if(isset($_REQUEST['id'])){
		$ou = mysql_class::ex_sqlx("delete from `room_pic` where `id` = ".$_REQUEST['id']);
		die($ou);
	}
	$out = '';
	$room_id = isset($_REQUEST['room_id']) ? (int)$_REQUEST['room_id'] : -1;
	if($room_id <= 0)
		die("<script language=\"javascript\">window.close();</script>");
	$rec['room_id'] = $room_id;
        if(isset($_FILES['uploadedfile']))
        {
                $target_path = "room_pic/";
                $target_path = $target_path .$conf->getMoshtari().'_'.$room_id . '_' .  basename( $_FILES['uploadedfile']['name']);
		$ext = explode('.',basename( $_FILES['uploadedfile']['name']));
		$ext = $ext[count($ext)-1];
		$ext = strtolower($ext);
		if(!file_exists($target_path) && ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif'))
		{
	                if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
				mysql_class::ex_sqlx("insert into `room_pic` (`room_id`,`pic`) values ($room_id,'$target_path')");
        	                $out = "<script> alert('تصویر اضافه شد'); </script>";
                	} else{
                        	$out =  "<script> alert('خطا در ارسال تصویر'); </script>";
	                }
		}
		else
			$out = "<script> alert('نام فایل تکراری است و یا فایل تصویر نمی‌باشد');</script>";
        }

   /*     $grid = new jshowGrid_new("room_pic","grid1");
	$grid->whereClause = " `room_id` = $room_id";
	$grid->setERequest($rec);
        $grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = 'تصویر';
	$grid->columnFunctions[2] = 'loadPic';
	$grid->deleteFunction = 'delete_item';
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
        $grid->intial();
        $grid->executeQuery();*/
	$out .= '<form enctype="multipart/form-data" method="POST">
                        <input  type="hidden" name="MAX_FILE_SIZE" value="999999999" />
                        آدرس تصویر : <input name="uploadedfile" class="form-control inp" type="file" />
                       
                        <input style="margin:5px" class="btn btn-info col-md-3" type="submit" value="ارسال" />
                </form><br/>';
       // $out .= $grid->getGrid();
//$out='';
$out.='<div class="box border orange">
									<div class="box-title">
										<h4><i class="fa fa-camera"></i>تصویر اتاق</h4>
									
									</div>
									<div class="box-body">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">تصویر</th>
												<th style="text-align:right">عملیات</th>
											  </tr>
											</thead>
											<tbody>';
        mysql_class::ex_sql("select * from `room_pic` where `room_id` = $room_id ",$ss);
    $i=1;
		while($r = mysql_fetch_array($ss))
		{
			$id = $r['id'];
			$out.='
			<tr class="'.((fmod($i,2)!=0)?'odd':'even').'">
			<td>'.$i.'</td>
			<td><img src="'.$r['pic'].'" height="60px"  style="cursor:pointer;" onclick="window.open(\''.$r['pic'].'\',\'\',500,500);"/></td>
			<td><a class="btn btn-danger" href="javascript:deletePic('.$id.');">
			<i class="fa fa-times"></i>
			حذف
			</a></td>
			</tr>
			';
			$i++;
		}
$out.='</tbody></table></div></div>';
        /*$grid = new jshowGrid_new("room_pic","grid1");
	$grid->whereClause = " `room_id` = $room_id";
        $grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = 'تصویر';
	$grid->columnFunctions[2] = 'loadPic';
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
        $grid->intial();
        $grid->executeQuery();
        $out = $grid->getGrid();	*/
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
				function deletePic(id){
					if(confirm('آیا حذف انجام شود؟')){
						StartLoading();
						$.get('',{id:id},function(result){
							location.reload();
						});
					}
				}
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
    
	
	<!-- PAGE -->
	<section id="page">
			<!-- SIDEBAR -->
			
			<!-- /SIDEBAR -->
		<div id="main-content">
			<div class="container">
				
                
                <div class="row" style="margin-right:0px;margin-left:0px;">
                <div class="col-lg-12" style="padding:0px;">
                    <div class="panel panel-default" style="border: 1px solid #ffae2e;">
                        
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                            <?php 
                                echo $out;
                            ?>
                          
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