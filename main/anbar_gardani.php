<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view || !$conf->anbar )
                die(lang_fa_class::access_deny);
	function loadKala($inp)
	{
		$out = null;
		$out = new kala_class($inp);
		$out = $out->name.'('.$out->code.')';
		return($out);
	}
	function loadMojoodi($inp)
	{
		$moj = anbar_det_class::getMojoodi($inp);
		return audit_class::enToPer($moj['out']);
	}
	function loadAnbardarUsers()
	{
		$out ='<select name="anbardar_user" id="anbardar_user" class="form-control inp" >' ;
		mysql_class::ex_sql("select `id`,`fname`,`lname` from `user` where `typ` in (select `id` from `grop` where `name` like '%انبار%') ",$q);
		while($r = mysql_fetch_array($q))
			$out .='<option value="'.$r['id'].'" >'.$r['fname'].' '.$r['lname']."</option>\n";
		$out .='</select>';
		return $out;
	}
	mysql_class::ex_sql("select `id`,`fname`,`lname` from `user` where `typ` in (select `id` from `grop` where `name` like '%انبار%') ",$qanbar);
	if(mysql_num_rows($qanbar)<=0)
		die('<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link type="text/css" href="../css/style.css" rel="stylesheet" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body>
		<center><h2>کابر انبار دار تعریف نشده است</h2></center>
	<body>
	</html>
');
	$anbar_id = (isset($_REQUEST['anbar_id']))?(int)$_REQUEST['anbar_id']:-1;
	$msg = '';
	$isGardani = FALSE;
	$user_id = (int)$_SESSION['user_id'];
	if(isset($_POST['calc']) && $_POST['calc']=='sabt' )
	{
		$anbar_user_id = $_POST['anbardar_user_id'];
		$kala_ids = kala_class::anbarGardaniArray($anbar_id);
		if($kala_ids!=null)
			$isGardani = anbar_det_gardani_class::sabtKalaGardani($kala_ids,$anbar_id,$anbar_user_id,$user_id);
		if($isGardani)
			$msg = "محاسبه حساب انبار دار انجام گردید";
	}
	/*
	var_dump($_REQUEST);
	$anbar_id = (isset($_REQUEST['anbar_id']))?(int)$_REQUEST['anbar_id']:-1;
	$out = '<form id="frm1" ><table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>نام کالا</th><th> موجودی سیستم</th><th>موجودی واقعی</th></tr>';
	mysql_class::ex_sql("select `kala_id` from `anbar_det` where `anbar_id`=$anbar_id ",$q);
	$i = 1;
	while($r = mysql_fetch_array($q))
	{
		$row_style = 'class="showgrid_row_odd"';
		if($i%2==0)
			$row_style = 'class="showgrid_row_even"';
		$kala_mojoodi = anbar_det_class::getMojoodi($r['kala_id']);
		$out.="<tr $row_style ><td>$i</td><td>".loadKala($r['kala_id'])."<input type='hidden' name='kala_id[]'  value='".$r['kala_id']."' ></td><td>".$kala_mojoodi['out']."<input type='hidden' name='mojoodi[]'  value='".$kala_mojoodi['out']."' ></td><td><input type='text' name='mojoodi_new[]'  class='inp' style='width:100px;' ></td></tr>";
		$i++;
	}
	$out .='<tr><td colspan="4" ><input class="inp" type="submit" value="محاسبه بدهکاری انبار دار"></td></tr>';
	$out .='</table></form>';
	*/
$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">نام</th>
                                        <th style=\"text-align:right;\">کد</th>
                                        <th style=\"text-align:right;\">موجودی واقعی</th>
                                        <th style=\"text-align:right;\">موجودی سیستم</th>
                                    </tr>
                                </thead>
                                <tbody>";
	$grid = new jshowGrid_new("kala","grid1");
	$mojood_kala = new anbar_class($anbar_id);
	$mojood_kala = $mojood_kala->loadKala();
	$grid->whereClause = " `id` in ($mojood_kala)";
	$grid->columnHeaders[0] = null;
       	$grid->columnHeaders[1] ='نام' ;
	$grid->columnHeaders[2] = "کد";
	$grid->columnHeaders[3] = null;
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[5] = 'موجودی واقعی';
	$grid->addFeild('id');
	$grid->columnHeaders[6] = 'موجودی سیستم';
	$grid->columnFunctions[6] = 'loadMojoodi';
	$grid->footer = '
<tr class="showgrid_row_odd" ><td colspan="5" >
کاربر انباردار:'.loadAnbardarUsers().'
<input class="inp" type="button" value="محاسبه بدهکاری انبار دار" onclick="submit_frm();" >
</td></tr>
';
	$grid->sortEnabled = TRUE;
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
        $grid->intial();
   	$grid->executeQuery();
        //$out = $grid->getGrid();
mysql_class::ex_sql("select * from `kala` where `id` in ($mojood_kala) ",$ss);
$i=1;
while($r=mysql_fetch_array($ss)){
    $id = $r['id'];
    $name = $r['name'];
    $code = $r['code'];
    $tedad_dasti = $r['tedad_dasti'];    
    
    if(fmod($i,2)!=0){
        $out.="
       <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$name</td>
                                        <td>$code</td>
                                        <td>$tedad_dasti</td>
                                        <td>$loadMojoodi($id)</td>
                                    </tr>
        ";
        $i++;
    }
    else{
        $out.="
        <tr class=\"even\">
                                        <td>$i</td>
                                        <td>$name</td>
                                        <td>$code</td>
                                        <td>$tedad_dasti</td>
                                        <td>$loadMojoodi($id)</td>
                                    </tr>
        ";
        $i++;
    }
    
}
$out.="<tr class=\"showgrid_row_odd\" ><td></td><td></td><td></td><td></td><td>
کاربر انباردار:".loadAnbardarUsers()."
<input class=\"btn btn-info inp\" type=\"button\" value=\"محاسبه بدهکاری انبار دار\" onclick=\"submit_frm();\" >
</td></tr></tbody></table>";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>انبار گردانی</title>
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
          function submit_frm()
			{
				document.getElementById('anbardar_user_id').value = document.getElementById('anbardar_user').options[document.getElementById('anbardar_user').selectedIndex].value;
				//alert(document.getElementById('anbardar_user_id').value);
				document.getElementById('calc').value = 'sabt';
				document.getElementById('frm1').submit();
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-shopping-cart"></i>انبار گردانی</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                          
                               
                            <?php echo $msg; ?>
                           <?php echo $out; ?>
                           <form id="frm1" method="POST">
				<input type="hidden" name="calc" id="calc" >
				<input type="hidden" name="anbardar_user_id" id="anbardar_user_id" >
			</form>
                            
                            
                            
                                    
                              
                               
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
        function insertFinalG(){
            StartLoading();
            var hotelName = $("#hotelName1 option:selected" ).val();
            var DaftarName = $("#DaftarName1 option:selected" ).val();
            var tabagheh = $("#tabagheh1 option:selected" ).val();
           $.post("garanti_tabagheAjax.php",{hotelName:hotelName,DaftarName:DaftarName,tabagheh:tabagheh},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function editGfunc(gid,hotel_id,daftar_id,tabaghe){
            StartLoading();
            $("input[name='gid']").val(gid);
            $("#hotelName option[value="+hotel_id+"]").attr('selected','selected');
            $("#DaftarName option[value="+daftar_id+"]").attr('selected','selected');
            $("#tabagheh option[value="+tabaghe+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var hotelName = $("#hotelName option:selected" ).val();
            var DaftarName = $("#DaftarName option:selected" ).val();
            var tabagheh = $("#tabagheh option:selected" ).val();
            var gid = $("input[name='gid']").val();
           $.post("garanti_tabagheEditAjax.php",{hotelName:hotelName,DaftarName:DaftarName,tabagheh:tabagheh,gid:gid},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
            
        }
        function deleteGfunc(gid){
            StartLoading();
            $("input[name='gid']").val(gid);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var gid = $("input[name='gid']").val();
           $.post("garanti_tabagheDeleteAjax.php",{gid:gid},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در حذف");
               if(data=="1"){
                   alert("حذف با موفقیت انجام شد");
                   location.reload();
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