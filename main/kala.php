<?php
session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view || !$conf->anbar)
                die(lang_fa_class::access_deny);
	function loadKala()
	{
		$out = null;
		mysql_class::ex_sql('select `name`,`id` from `kala_no` order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$out[$r['name']] = (int)$r['id'];
		}
		return($out);
	}
function loadKala1()
	{
		$out = "";
		mysql_class::ex_sql('select `name`,`id` from `kala_no` order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$out.="<option value='".$r['id']."'>".$r['name']."</option>";
		}
		return($out);
	}
	function loadVahed()
	{
		$out = null;
		mysql_class::ex_sql('select `name`,`id` from `kala_vahed` order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$out[$r['name']] = (int)$r['id'];
		}
		return($out);
	}
function loadVahed1()
	{
		$out = "";
		mysql_class::ex_sql('select `name`,`id` from `kala_vahed` order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$out.="<option value='".$r['id']."'>".$r['name']."</option>";
		}
		return($out);
	}
	function loadMojoodi($inp)
	{
		$out=anbar_det_class::getMojoodi($inp);
		if($out['out']<=0)
			$out = $out['msg'];
		else
		{
			$out = $out['out'];
			$out = "<a href='kala_gozaresh.php?kala_id=$inp' target='_blank'>$out</a>";
		}
		
		return $out;
	}
	function add_item()
	{
		foreach($_REQUEST as $key => $value)
                        if(substr($key,0,4)=="new_")
                                if($key != "new_en" && $key != "new_id")
                                        $fields[substr($key,4)] =perToEnNums($value);
                $fi = "(";
	        $valu="(";
		foreach ($fields as $field => $value)
        	{
		        $fi.="`$field`,";
                        $valu .="'$value',";
                }
       		$fi=substr($fi,0,-1);
                $valu=substr($valu,0,-1);
		$fi.=")";
        	$valu.=")";
        	$query="insert into `kala` $fi values $valu";
		mysql_class::ex_sqlx($query);
	}
$out="<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">موجودی</th>
                                        <th style=\"text-align:right;\">نام کالا</th>
                                        <th style=\"text-align:right;\">کد کالا</th>
                                        <th style=\"text-align:right;\">نوع کالا</th>
                                        <th style=\"text-align:right;\">واحد</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";
	$grid = new jshowGrid_new("kala","grid1");
	$grid->whereClause=" 1=1 order by `code`,`kala_no_id`,`name`";
	$grid->columnHeaders[0] = 'موجودی';
	$grid->columnFunctions[0] = 'loadMojoodi';
	$grid->columnAccesses[0] = 0 ;
        $grid->columnHeaders[1] = "نام کالا";
	$grid->columnFilters[1] = TRUE;
	$grid->columnHeaders[2] = "کد کالا";
	$grid->columnHeaders[3] = "نوع کالا";
	$grid->columnLists[3] = loadKala();
	$grid->columnHeaders[4] = 'واحد';
	$grid->columnLists[4] = loadVahed();
	$grid->columnHeaders[5] = null;
	$grid->gotoLast = TRUE;
	$grid->addFunction = 'add_item';
        $grid->intial();
   	$grid->executeQuery();
        //$out = $grid->getGrid();
    mysql_class::ex_sql('select * from `kala` where 1=1 order by `code`,`kala_no_id`,`name`',$ss);
    $i=1;
		while($r = mysql_fetch_array($ss)){
            $id = $r['id'];
            $name = $r['name'];
            $code = $r['code'];
            $kala_no_id = $r['kala_no_id'];
            mysql_class::ex_sql("select * from `kala_no` where `id` = '$kala_no_id'",$sss);
            $rr = mysql_fetch_array($sss);
            $kala_no_name = $rr['name'];
            $vahed_id = $r['vahed_id'];
            mysql_class::ex_sql("select * from `kala_vahed` where `id` = '$vahed_id'",$ssss);
            $rrr = mysql_fetch_array($ssss);
            $vahed_name = $rrr['name'];
            if(fmod($i,2)!=0){
                $out.="<tr class='odd'>
                <td>".$i."</td>
                <td>".loadMojoodi($id)."</td>
                <td>".$name."</td>
                <td>".$code."</td>
                <td>".$kala_no_name."</td>
                <td>".$vahed_name."</td>
                <td><a onclick=\"editGfunc('".$id."','".$name."','".$code."','".$kala_no_id."','".$vahed_id."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a></td>
                </tr>";
                $i++;
            }
            else{
                $out.="<tr class='even'>
                <td>".$i."</td>
                <td>".loadMojoodi($id)."</td>
                <td>".$name."</td>
                <td>".$code."</td>
                <td>".$kala_no_name."</td>
                <td>".$vahed_name."</td>
                <td><a onclick=\"editGfunc('".$id."','".$name."','".$code."','".$kala_no_id."','".$vahed_id."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a></td>
                </tr>";
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
	<title>کالا</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-shopping-cart"></i>کالا</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                               
                            <a href="#newG"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن کالا جدید</button></a>
                            <br/>
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
<div class="modal fade" id="newG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن کالا</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-6">
                            <label>نام کالا: </label>
                            <input class="form-control" type="text" value="" name="name1" />
                        </div>
                        <div class="col-md-6">
                            <label>کد کالا: </label>
                            <input class="form-control" type="text" value="" name="code1" />
                        </div>
                        <div class="col-md-6">
                            <label>نوع کالا: </label>
                            <select class="form-control" id="type1">
                            <?php echo loadKala1(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>واحد: </label>
                            <select class="form-control" id="vahed1">
                            <?php echo loadvahed1(); ?>
                            </select>
                        </div>
                    </form>	
                </div>
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalG()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="editG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش کالا</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id2" />
                        <div class="col-md-6">
                            <label>نام کالا: </label>
                            <input class="form-control" type="text" value="" name="name2" />
                        </div>
                        <div class="col-md-6">
                            <label>کد کالا: </label>
                            <input class="form-control" type="text" value="" name="code2" />
                        </div>
                        <div class="col-md-6">
                            <label>نوع کالا: </label>
                            <select class="form-control" id="type2">
                            <?php echo loadKala1(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>واحد: </label>
                            <select class="form-control" id="vahed2">
                            <?php echo loadvahed1(); ?>
                            </select>
                        </div>
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalG()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف کالا</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id3" />
                        آیا از حذف مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalG()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
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
	
	<!-- DATE RANGE PICKER -->
    <script src="<?php echo $root ?>inc/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>inc/bootstrap-datepicker.fa.js"></script>
	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    <script language="javascript" >
			if(document.getElementById('new_id'))
                document.getElementById('new_id').style.display= 'none';
		</script>
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
            var kala1 = $("input[name='name1']").val();
            var code1 = $("input[name='code1']").val();
            var type1 = $("#type1 option:selected" ).val();
            var vahed1 = $("#vahed1 option:selected" ).val();
           $.post("kalaAjax.php",{kala1:kala1,code1:code1,type1:type1,vahed1:vahed1},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function editGfunc(id,name,code,kala_no_id,vahed_id){
            StartLoading();
            $("input[name='id2']").val(id);
            $("input[name='name2']").val(name);
            $("input[name='code2']").val(code);
            $("#type2 option[value="+kala_no_id+"]").attr('selected','selected');
            $("#vahed2 option[value="+vahed_id+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var name2 = $("input[name='name2']").val();
            var code2 = $("input[name='code2']").val();
            var type2 = $("#type2 option:selected" ).val();
            var vahed2 = $("#vahed2 option:selected" ).val();
            $.post("kalaEditAjax.php",{id2:id2,name2:name2,code2:code2,type2:type2,vahed2:vahed2},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
            
        }
        function deleteGfunc(id){
            StartLoading();
            $("input[name='id3']").val(id);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var id3 = $("input[name='id3']").val();
           $.post("kalaDeleteAjax.php",{id3:id3},function(data){
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