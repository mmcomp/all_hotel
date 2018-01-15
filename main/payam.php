<?php
session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);	
	if (isset($_REQUEST["group_id_new"]))
                $group_id_new = $_REQUEST["group_id_new"];
	else
		$group_id_new = -1;
	if (isset($_REQUEST["users_id"]))
                $users_id = $_REQUEST["users_id"];
	else
		$users_id = -1;
	$_SESSION['grop'] = $group_id_new;
	$log_user_id = $_SESSION["user_id"];
	$showPayam = FALSE;
	mysql_class::ex_sql("select `id` from `payam` where `rec_user_id`='$log_user_id' and `en`='-1'",$q_payam);
	while ($r_payam = mysql_fetch_array($q_payam))
	{
		$id = $r_payam['id'];
		mysql_class::ex_sqlx("update `payam` set `en`='1' where `id` = $id");
	}
	function loadUser($user_id)
	{
		$out = "";
		mysql_class::ex_sql("select `fname` , `lname` from `user` where `id`='$user_id'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function loadPayam($inp)
        {
                $out = '';
		mysql_class::ex_sql("select * from `payam_toz` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out= $r['toz'];
		return($out);
        }
	$combo_group = "";
	$combo_group.= "<form name=\"selGroup\" id=\"selGroup\" method=\"POST\">";
	$combo_group .= "<select class='form-control inp' id=\"group_id\" name=\"group_id_new\" onchange=\"document.getElementById('selGroup').submit();\"><option value=\"-1\">\nهمه\n</option>\n";
		mysql_class::ex_sql("select * from `grop` where `en`>0 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$group_id_new)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_group .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
		        $combo_group .= $r["name"]."\n";
		        $combo_group .= "</option>\n";
		}
		$combo_group .= "</select>";
	$combo_group .= "</form>";	
	$combo_user = "";
	$combo_user .= "<form name=\"selUser\" id=\"selUser\" method=\"POST\">";
		$combo_user .= "<select class='form-control inp' id=\"users_id\" name=\"users_id\" onchange=\"document.getElementById('selUser').submit();\" ><option value=\"-1\">\nهمه\n</option>\n";
		if ($group_id_new==-1)
			mysql_class::ex_sql("select `id`,`fname`,`lname` from `user` order by `lname`",$q);
		else
			mysql_class::ex_sql("select `id`,`fname`,`lname` from `user` where `typ`='$group_id_new' order by `lname`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$users_id)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_user .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
		        $combo_user .= $r["lname"]."\n";
		        $combo_user .= "</option>\n";
		}
		$combo_user .= "</select>";
		$combo_user .= '<input type="hidden" name="group_id_new" value="'.$group_id_new.'" >';
	$combo_user .= "</form>";
	$user_id = (int)$_SESSION['user_id'];
	if (isset($_REQUEST['matn_payam']))
	{
		$tarikh_now = date("Y-m-d");
		$payam_group = $_REQUEST['group_id_new'];
		$payam_user = $_REQUEST['users_id'];
		$matn_payam = $_REQUEST['matn_payam'];
		if (($payam_group==-1)&&($payam_user==-1))
		{
			$ln = mysql_class::ex_sqlx("insert into `payam_toz` (`id`, `toz`) values (NULL, '$matn_payam')",FALSE);
			$payam_id = mysql_insert_id($ln);
			mysql_close($ln);
			$query = "insert into `payam_toz` (`id`, `toz`) values (NULL, '$matn_payam')";
			mysql_class::ex_sql("select `id` from `user`",$q);
			while($r = mysql_fetch_array($q))
			{
				$rec_id = $r['id'];
				mysql_class::ex_sqlx("insert into `payam` (`id`, `se_user_id`, `rec_user_id`, `payam_id`,`tarikh`,`en`) values (NULL, '$user_id', '$rec_id', '$payam_id','$tarikh_now','-1')");
			}
		}
		elseif (($payam_group==-1)&&($payam_user!=-1))
		{
			$ln = mysql_class::ex_sqlx("insert into `payam_toz` (`id`, `toz`) values (NULL, '$matn_payam')",FALSE);
			$payam_id = mysql_insert_id($ln);
			mysql_close($ln);
			mysql_class::ex_sqlx("insert into `payam` (`id`, `se_user_id`, `rec_user_id`, `payam_id`,`tarikh`,`en`) values (NULL, '$user_id', '$payam_user', '$payam_id','$tarikh_now','-1')");
		}
		elseif (($payam_group!=-1)&&($payam_user==-1))
		{
			$ln = mysql_class::ex_sqlx("insert into `payam_toz` (`id`, `toz`) values (NULL, '$matn_payam')",FALSE);
			$payam_id = mysql_insert_id($ln);
			mysql_close($ln);
			$query = "insert into `payam_toz` (`id`, `toz`) values (NULL, '$matn_payam')";
			mysql_class::ex_sql("select `id` from `user` where `typ`='$payam_group'",$q);
			while($r = mysql_fetch_array($q))
			{
				$rec_id = $r['id'];
				mysql_class::ex_sqlx("insert into `payam` (`id`, `se_user_id`, `rec_user_id`, `payam_id`,`tarikh`,`en`) values (NULL, '$user_id', '$rec_id', '$payam_id','$tarikh_now','-1')");
			}
		}
		elseif (($payam_group!=-1)&&($payam_user!=-1))
		{
			$ln = mysql_class::ex_sqlx("insert into `payam_toz` (`id`, `toz`) values (NULL, '$matn_payam')",FALSE);
			$payam_id = mysql_insert_id($ln);
			mysql_close($ln);
			mysql_class::ex_sqlx("insert into `payam` (`id`, `se_user_id`, `rec_user_id`, `payam_id`,`tarikh`,`en`) values (NULL, '$user_id', '$payam_user', '$payam_id','$tarikh_now','-1')");
		}
		else
			echo '';
	}	
	if ($se->detailAuth('admin'))
		$shart = "1=1 order by `tarikh` DESC, `payam_id` DESC";
	else		
		$shart = "(`se_user_id`='$user_id' or `rec_user_id`='$user_id') order by `tarikh` DESC, `payam_id` DESC";
	$grid = new jshowGrid_new("payam","grid1");
	$grid->whereClause= $shart;
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= 'ارسال کننده پیام';
	$grid->columnFunctions[1] = 'loadUser';
	$grid->columnHeaders[2]= 'دریافت کننده پیام';
	$grid->columnFunctions[2] = 'loadUser';
	$grid->columnHeaders[3]= 'متن پیام';
	$grid->columnFunctions[3] = 'loadPayam';
	$grid->columnHeaders[4]= 'تاریخ';
	$grid->columnFunctions[4] = 'hamed_pdate';
	$grid->columnHeaders[5]= null;
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->intial();
	$grid->executeQuery();
	//$out = $grid->getGrid();
    $out = '
        <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">ارسال کننده پیام</th>
                                            <th style="text-align:right;">دریافت کننده پیام</th>
                                            <th style="text-align:right;">متن پیام</th>
                                            <th style="text-align:right;">تاریخ</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
    mysql_class::ex_sql("select * from `payam` where $shart",$ss);
	$i=1;
    while ($r = mysql_fetch_array($ss))
	{
        $se_user_id = $r['se_user_id'];
        
        mysql_class::ex_sql("select * from `user` where `id` = '$se_user_id' ",$h_id);
        $h_id1 = mysql_fetch_array($h_id);
        $fname1 = $h_id1['fname'];
        $lname1 = $h_id1['lname'];
        $uname1 = $fname1." ".$lname1;
        
        $rec_user_id = $r['rec_user_id'];
        
        mysql_class::ex_sql("select * from `user` where `id` = '$rec_user_id' ",$h_id5);
        $h_id6 = mysql_fetch_array($h_id5);
        $fname2 = $h_id6['fname'];
        $lname2 = $h_id6['lname'];
        $uname2 = $fname2." ".$lname2;
        
        $payam_id = $r['payam_id'];
        mysql_class::ex_sql("select * from `payam_toz` where `id` = '$payam_id' ",$h_id10);
        $h_id11 = mysql_fetch_array($h_id10);
        $toz = $h_id11['toz'];
        
        $tarikh = $r['tarikh'];
        if(fmod($i,2)!=0){
            $out.="
            <tr class='odd'>
            <td>".$i."</td>
            <td>".$uname1."</td>
            <td>".$uname2."</td>
            <td>".$toz."</td>
            <td>".$tarikh."</td>
            </tr>
            ";
            $i++;
        }
        else{
            $out.="
            <tr class='even'>
            <td>".$i."</td>
            <td>".$uname1."</td>
            <td>".$uname2."</td>
            <td>".$toz."</td>
            <td>".$tarikh."</td>
            </tr>
            ";
            $i++;
        }
        
    }
$out.="</tbody></table>";

$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>پیام ها</title>
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
        $("#new_user_id").hide();
				$("#new_rec_grop").hide();
				$("#new_user_pasokh").hide();
				$("#new_toz_pasokh").hide();
				$("#new_en").hide();
    
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-envelope"></i>پیام ها</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">گروه کاربری:</label> 
                                    <div class="col-md-8"><?php echo $combo_group;?></div>
                                </div>
                                 
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">کاربر:</label> 
                                    <div class="col-md-8">
                                            <?php echo $combo_user;?>
                                    </div>
                                </div>
                                <form id="payam_frm"  method='POST' >
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">متن پیام:</label> 
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="matn_payam" id="matn_payam" >                                         </textarea>
                                    </div>
                                </div>
                                
                               <input type='hidden' name="group_id_new" id="hi_group_id" value="<?php echo $group_id_new;?>" >
						       <input type='hidden' name="users_id" id="hi_users_id" value="<?php echo $users_id;?>" >
						      			
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <div class="col-md-12"><input type="submit" value="ارسال پیام" class="btn btn-info col-md-8 pull-left inp"/></div>
                                </div>
                                    </form>
                            </div>

                           <?php echo '<br/>'.$out; ?>
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