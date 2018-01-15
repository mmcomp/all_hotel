<?php
session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
	if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$GLOBALS['ftyp']='';
	function loadCHange($inp)
	{
		$out = $inp;
		$GLOBALS['ftyp']=$inp;
		$tmp = explode('_',$inp);
		switch ($tmp[0])
		{
			case 'lname':
				$out = 'نام و نام خانوادگی';
				break;
			case 'tedad':
				$out = 'تعداد نفرات';
				break;
			case 'aztarikh':
				$out = 'تاریخ ورود';
				break;
			case 'tatarikh':
				$out = 'تاریخ خروج';
				break;
			case 'shab':
				$out = 'شب رزرو';
				break;
			case 'rooz':
				$out = 'روز رزرو';
				break;
			case 'm':
				switch ($tmp[1])
				{
					case 'hotel':
						$out = 'مبلغ  هتل';
						break;
					case 'belit':
						if($tmp[2]=='1')
							$out = 'مبلغ بلیت رفت';
						else if($tmp[2]=='2')
							$out = 'مبلغ بلیت برگشت';
						break;
				}
				break;
			case 'ajans':
				switch ($tmp[2])
				{
					case '2':
						$out = 'حساب معین بلیت برگشت';
						break;
					case '1':
						$out = 'حساب معین بلیت رفت';
						break;
				}
				break;
			case 'daftar':
				switch ($tmp[2])
				{
					case '2':
						$out = 'حساب کل بلیت برگشت';
						break;
					case '1':
						$out = 'حساب کل بلیت رفت';
						break;
				}
				break;
			case 'otagh':
				$room = new room_class((int)$tmp[1]);
				$out  ='اتاق'.$room->name;
				break;
			case 'khadamat':
				switch ($tmp[1])
				{
					case 'id':
						$khadamat = new khadamat_class((int)$tmp[2]);
						$out = 'خدمات '.$khadamat->name;
						break;
					case 'v':
						$khadamat = new khadamat_class((int)$tmp[2]);
						$out = 'ورودی خدمات '.$khadamat->name;
						break;
					case 'kh':
						$khadamat = new khadamat_class((int)$tmp[2]);
						$out = 'خروجی خدمات '.$khadamat->name;
						break;
				}
				break;

		}
		return $out;
	}
	function loadDate($inp)
	{
		$out=jdate("H:j:s d / m / Y  ",strtotime($inp));
                return $out;
	}
	function loadVal($inp)
	{
		$out = $inp;
		$ftyp = $GLOBALS['ftyp'];
		$tmp = explode('_',$ftyp);
		switch ($tmp[0])
		{
			case 'm':
				$out = monize($inp);
				break;
			case 'ajans':
				$aj = new ajans_class((int)$inp);
				$out = $aj->name;
				break;
			case 'daftar':
				$daf = new daftar_class((int)$inp);
				$out = $daf->name;
				break;
		}
		return $out; 
	}
	function loadUser($inp)
	{
		$user = new user_class((int)$inp);
		$daftar = new daftar_class($user->daftar_id);
		return $user->fname.' '.$user->lname.' ( '.$daftar->name.' ) ';
	}
	$out = '';
        if(isset($_REQUEST['reserve_id']))
	{
		$reserve_id = (int)$_REQUEST['reserve_id'];
            
            $out ="<div class=\"box border orange\">
									
									<div class=\"box-body\">
										<table class=\"table table-hover\">
											<thead>
											  <tr>
												<th style=\"text-align:right\">ردیف</th>
												<th style=\"text-align:right\">تغییرات</th>
												<th style=\"text-align:right\">مقدار قبلی</th>
                                                <th style=\"text-align:right\">مقدار اصلاح شده</th>
                                                <th style=\"text-align:right\">تاریخ</th>
                                                <th style=\"text-align:right\">شماره رزرو</th>
                                                <th style=\"text-align:right\">کاربر</th>
											  </tr>
											</thead>
											<tbody>";
            mysql_class::ex_sql("select * from `changeLog` where `reserve_id`='$reserve_id' order by `tarikh`,`id`",$q);
            $i=1;
		while($r = mysql_fetch_array($q))
		{
            $field_name = $r['feild_name'];
            $pvalue = $r['pvalue'];
            $value = $r['value'];
            $tarikh = $r['tarikh'];
            $reserve_id = $r['reserve_id'];
            $user_id = $r['user_id'];
            mysql_class::ex_sql("select `fname`,`lname` from `user` where `id` = '$user_id' ",$user_id);
            $user_id1 = mysql_fetch_array($user_id);
            $fname = $user_id1['fname'];
            $lname = $user_id1['lname'];
            $name = $r['name'];
            if(fmod($i,2)!=0){
                $out.="<tr class='odd'>
                <td>$i</td>
                <td>$field_name</td>
                <td>$pvalue </td>
                <td>$value</td>
                <td>$tarikh</td>
                <td>$reserve_id</td>
                <td>$fname $lname</td>
                </tr>";
                $i++;
            }
            else{
                $out.="<tr class='even'>
                <td>$i</td>
                <td>$field_name</td>
                <td>$pvalue </td>
                <td>$value</td>
                <td>$tarikh</td>
                <td>$reserve_id</td>
                <td>$fname $lname</td>
                </tr>";
                $i++;
            }
        }
            $out.="</tbody></table></div></div>";
            
		/*$grid = new jshowGrid_new("changeLog","grid1");
		$grid->whereClause=" `reserve_id`=$reserve_id order by `tarikh`,`id`";
		$grid->pageCount = 20;
		$grid->width = '95%';
		$grid->index_width = '20px';
		$grid->columnHeaders[0] = null;
		$grid->columnHeaders[1] = null;
	       	$grid->columnHeaders[2] ='تغییرات' ;
		$grid->columnFunctions[2] = "loadCHange";
		$grid->columnHeaders[3] = "مقدار قبلی";
		$grid->columnFunctions[3] = "loadVal";
		$grid->columnHeaders[4] = "مقدار اصلاح شده";
		$grid->columnFunctions[4] = "loadVal";
		$grid->columnHeaders[5] = "تاریخ";
		$grid->columnFunctions[5] = "loadDate";
		$grid->columnHeaders[6] = "شماره رزرو";
		$grid->columnHeaders[7] = "کاربر ";
		$grid->columnFunctions[7] = "loadUser";
		$grid->canAdd = FALSE;
		$grid->canDelete = FALSE;
		$grid->canEdit = FALSE;
		$grid->intial();
	   	$grid->executeQuery();
		$out = $grid->getGrid();*/
	}
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-repeat"></i>تغییرات رزرو</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           <?php echo $out; ?>
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