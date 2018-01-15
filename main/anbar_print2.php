<?php
	session_start();
	include_once("../kernel.php");
       
        $se = security_class::auth((int)$_SESSION['user_id']);
        
	$out = '';
	$id = ((isset($_REQUEST['id']))?(int)$_REQUEST['id']:-1);
	$cost_kala_id = ((isset($_REQUEST['cost_kala_id']))?(int)$_REQUEST['cost_kala_id']:-1);
	$cost_tedad = ((isset($_REQUEST['cost_tedad']))?(int)$_REQUEST['cost_tedad']:0);
	if($cost_kala_id>0)
		$cost_kala = new cost_kala_class($cost_kala_id);
	$now = audit_class::hamed_pdate(date("Y-m-d"));
	$anbar_factor = new anbar_factor_class($id);
	$anbar_typ = new anbar_typ_class($anbar_factor->anbar_typ_id);
	$resid = $anbar_typ->name;
	$user = new user_class((int)$_SESSION['user_id']);
	$user = $user->fname.' '.$user->lname;
	$moshtari = new moshtari_class((int)$_SESSION['moshtari_id']);
	//echo $anbar_factor->anbar_typ_id;
	if($anbar_typ->typ==1)
	{
	      $out.='<tr>
						    <th>
						      نام کالا
						    </th>
						    <th>
						      تاریخ
						    </th>
						    <th>
						      تعداد
						    </th>
						    <th>
 واحد
						    </th>
						    <th>
قیمت واحد
						    </th>
						    <th>
قیمت کل
						    </th>
						    <th>
تحویل دهنده
						    </th>
					      </tr>';
	      $ghimat_kol = 0;
	      mysql_class::ex_sql("select * from `anbar_det` where `anbar_factor_id`=$id",$q);
	      while($r = mysql_fetch_array($q))
	      {
		  $kala = new kala_class($r['kala_id']);
		  $tarikh =audit_class::hamed_pdate($r['tarikh']);
		  $vahed = new kala_vahed_class($kala->vahed_id);
		  $ghimat_vahed = ($r['tedad']==0)?'تعریف نشده':monize($r['ghimat']/$r['tedad']);
		  $other_user = new user_class((int)$r['other_user_id']);
		  $other_user = $other_user->fname.' '.$other_user->lname;
		  $ghimat_kol += (int)$r['ghimat'];
		  $out .='<tr>';
		  $out .='<td>'.$kala->name.'</td>';
		  $out .='<td>'.$tarikh.'</td>';
		  $out .='<td>'.(int)$r['tedad'].'</td>';
		  $out .='<td>'.$vahed->name.'</td>';
		  $out .='<td>'.$ghimat_vahed.'</td>';
		  $out .='<td>'.monize($r['ghimat']).'</td>';
		  $out .='<td>'.$other_user.'</td>';
		  $out .="</tr>\n";
	      }
	      $out .="<tr><td colspan='5' style='text-align:left;' >جمع قیمت کل:</td><td>".monize($ghimat_kol)."</td><td>&nbsp;</td></tr>\n";
	}
	else if((int)$anbar_typ->typ==-1)
	{
		$out.='<tr>
						    <th>
						      نام کالا
						    </th>
						    <th>
						      تاریخ
						    </th>
						    <th>
						      تعداد
						    </th>
						    <th>
 واحد
						    </th>

						    <th>
تحویل گیرنده
						    </th>
					      </tr>';
		mysql_class::ex_sql("select * from `anbar_det` where `anbar_factor_id`=$id",$q);
		while($r = mysql_fetch_array($q))
		{
		    $kala = new kala_class($r['kala_id']);
		    $tarikh =audit_class::hamed_pdate($r['tarikh']);
		    $vahed = new kala_vahed_class($kala->vahed_id);
		    $ghimat_vahed = ($r['tedad']==0)?'تعریف نشده':monize($r['ghimat']/$r['tedad']);
		    $other_user = new user_class((int)$r['other_user_id']);
		    $other_user = $other_user->fname.' '.$other_user->lname;
		    $out .='<tr>';
		    $out .='<td>'.$kala->name.'</td>';
		    $out .='<td>'.$tarikh.'</td>';
		    $out .='<td>'.(int)$r['tedad'].'</td>';
		    $out .='<td>'.$vahed->name.'</td>';
		    $out .='<td>'.$other_user.'</td>';
		    $out .="</tr>\n";
		}
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>چاپ فاکتور</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-print"></i>چاپ فاکتور</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <div align="center" style="width:18cm;">
		<table border='1' style="width:95%;font-size:14px;height:250px;" cellspacing="0" >
			<tr>
				<th style="text-align:center" colspan="3" width="80%" >
				<h2><?php echo $moshtari->name; ?></h2><br/>
				</th>
				<td rowspan="2" valign="top" >
					تاریخ چاپ:
						<?php echo $now; ?><br/><br/>
صادر کننده:
						<b><?php echo $user; ?></b>
				</td>
			</tr>
			<tr>
				<td style="text-align:center" colspan="3">
					<?php 
						echo '<h3> رسید '.$resid.'</h3><br/>';
						echo ($cost_kala_id==-1)?'':'جهت '.$cost_tedad.' '.$cost_kala->name;
					?>
				</td>
			</tr>
			<tr>
				<td colspan="4" >
					<table border="1" style="width:100%;font-size:12px;border-width:1px;border-collapse: collapse;" cellspacing="10" >
					      <?php echo $out; ?>
					</table>
				</td>
			</tr>
			<tr height="80px" >
				<td colspan="3" >
					<?php echo $conf->title; ?>
				</td>
				<td style="text-align:right;" rowspan="2">
					امضاء
				</td>
			</tr>

		</table>
        </div>
                             <button onclick="getPrint();" class="btn btn-success col-md-2 pull-left"><i class="fa fa-print"></i> چاپ</button>
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