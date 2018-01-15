<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view || !$conf->anbar)
                die(lang_fa_class::access_deny);
	function loadMoeen($inp)
        {
                $inp = (int)$inp;
                $aj = new anbar_class($inp);
//		if (isset($_REQUEST["moeen_id"])?$_REQUEST["moeen_id"]:-1);
                if($aj->moeen_id>0)
                {
                        $moeen = new moeen_class($aj->moeen_id);
                        //if($moeen->id>0)
                        $nama = $moeen->name.'('.$moeen->code.')';
                        //else
                                //$nama = 'ﺎﻨﺘﺧﺎﺑ';
                }
                else
                {
                        $nama = 'انتخاب';
                }
                $out = "<u><span onclick=\"window.location =('select_hesab.php?refPage=anbar.php&sel_id=$inp');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
                return $out;
        }
        function loadSimpleMoeen($inp)
        {
                $inp = (int)$inp;
                $out = new moeen_class($inp);
                return $out->name;
        }
	function add_item()
        {
                $fields = null;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id")
                                {
                                        $fields[substr($key,4)] = $value;
                                }
                        }
                }
		$fields["en"] = 1;
                $kol_id = kol_class::addById($fields['name']);
                $moeen_id = moeen_class::addById($kol_id,'صندوق '.$fields['name']);
                $moeen_anbardar_id = moeen_class::addById($kol_id,'انباردار '.$fields['name']);
		$fields['moeen_id'] = $moeen_id;
		$fields['moeen_anbardar_id'] = $moeen_anbardar_id;
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
                $query="insert into `anbar` $fi values $valu";
                mysql_class::ex_sqlx($query);
        }
	function delete_item($id)
	{
		mysql_class::ex_sqlx("update `anbar` set `en`=0 where `id` = $id");
	}
	if(isset($_REQUEST['sel_id']))
        {
                $moeen_id = (int)$_REQUEST['moeen_id'];
                $sel_id = $_REQUEST['sel_id'];
                mysql_class::ex_sqlx("update `anbar` set `moeen_id`=$moeen_id where `id`=$sel_id");
        }
	function loadGardesh($inp)
	{
		$anb = new anbar_class((int)$inp);
		if($anb->en==2)
			$out = '<a target=\'_blank\' href=\'anbar_gardani.php?anbar_id='.$inp.'\'>انبارگردانی</a>';
		else
			$out = '<span style="color:#999;">انبارگردانی</span>';
		return $out;
	}
    $out='<div class="box border orange">
									
									<div class="box-body" style="overflow-x:scroll">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">گردش</th>
												<th style="text-align:right">انبار</th>
                                                <th style="text-align:right">آدرس</th>
                                                <th style="text-align:right">وضعیت</th>
                                                <th style="text-align:right">حساب معین انبار</th>
                                                <th style="text-align:right">حساب معین انبار دار</th>
                                                <th style="text-align:right">عملیات</th>
											  </tr>
											</thead>
											<tbody>';
	$grid = new jshowGrid_new("anbar","grid1");
	$grid->whereClause="`en` in (1,2) order by `name`";
	$grid->index_width = '20px';
	$grid->columnHeaders[0] = 'گردش';
	$grid->columnFunctions[0] = 'loadGardesh';
	$grid->columnAccesses[0] = 0;
    $grid->columnHeaders[1] = "انبار";
    $grid->columnHeaders[2] = "آدرس";
	$grid->columnHeaders[3] = 'وضعیت';
	$grid->columnLists[3] = array('انبارگردانی'=>2,'عادی'=>1);
	$grid->columnHeaders[4] = 'حساب معین انبار';
	$grid->columnFunctions[4] = 'loadSimpleMoeen';
	$grid->columnAccesses[4] = 0;
	$grid->columnHeaders[5] = 'حساب معین انباردار';
	$grid->columnFunctions[5] = 'loadSimpleMoeen';
	$grid->columnAccesses[5] = 0;
	$grid->deleteFunction = 'delete_item';
	$grid->addFunction = "add_item";
	//$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	$grid->showAddDefault = FALSE;
    $grid->intial();
   	$grid->executeQuery();
    //$out = $grid->getGrid();
    mysql_class::ex_sql("select * from `anbar` where `en` in (1,2) order by `name`",$ss);
    $i=1;
    while($r=mysql_fetch_array($ss)){
        $id = $r['id'];
        $name = $r['name'];
        $location = $r['location'];
        $en = $r['en'];
        $type="";
        if($en==0)
            $type="پاک شده";
        if($en==1)
            $type="عادی";
        if($en==2)
            $type="انبارگردانی";
        
        $moeen_id = $r['moeen_id'];
        mysql_class::ex_sql("select * from `moeen` where `id`='$moeen_id'",$sss);
        $rr=mysql_fetch_array($sss);
        $moeen_name = $rr['name'];
        $moeen_anbar_id = $r['moeen_anbar_id'];
        mysql_class::ex_sql("select * from `moeen_anbar` where `id`='$moeen_anbar_id'",$ssss);
        $rrr=mysql_fetch_array($ssss);
        $moeen_anbar_name = $rrr['name'];
    $out.="
    <tr>
        <td>".$i."</td>
         <td>".loadGardesh($id)."</td>
         <td>".$name."</td>
         <td>".$location."</td>
         <td>".$type."</td>
         <td>".$moeen_name."</td>
         <td>".$moeen_anbar_name."</td>
         <td><a onclick=\"editGfunc('".$id."','".$name."','".$location."','".$en."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a></td>
         </tr>"; 
        $i++;    
    }
$out.="</tbody></table></div></div>";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>مدیریت انبار</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-shopping-cart"></i>مدیریت انبار</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                               
                            <a href="#newG"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن انبار جدید</button></a>
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
                    <h4 class="modal-title">افزودن انبار</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-4">
                            <label>انبار: </label>
                             <input class="form-control" type="text" value="" name="name1" />
                        </div>
                        <div class="col-md-4">
                            <label>آدرس: </label>
                            <input class="form-control" type="text" value="" name="location1" />
                        </div>
                        <div class="col-md-4">
                            <label>وضعیت: </label>
                            <select name="state1" id="state1" class="form-control">
                                <option value=""></option>
                                <option value="1">عادی</option> 
                                <option value="2">انبارگردانی</option> 
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
                    <h4 class="modal-title">ویرایش انبار</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id2" />
                        <div class="col-md-4">
                            <label>انبار: </label>
                             <input class="form-control" type="text" value="" name="name2" />
                        </div>
                        <div class="col-md-4">
                            <label>آدرس: </label>
                            <input class="form-control" type="text" value="" name="location2" />
                        </div>
                        <div class="col-md-4">
                            <label>وضعیت: </label>
                            <select name="state2" id="state2" class="form-control">
                                <option value=""></option>
                                <option value="1">عادی</option> 
                                <option value="2">انبارگردانی</option> 
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
                    <h4 class="modal-title">حذف انبار</h4>
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
            var name1 = $("input[name='name1']").val();
            var location1 = $("input[name='location1']").val();
            var state1 = $("#state1 option:selected" ).val();
           $.post("anbarAjax.php",{name1:name1,location1:location1,state1:state1},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function editGfunc(id,name,location,type){
            StartLoading();
            $("input[name='id2']").val(id);
            $("input[name='name2']").val(name);
            $("input[name='location2']").val(location);
            $("#state2 option[value="+type+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var name2 = $("input[name='name2']").val();
            var location2 = $("input[name='location2']").val();
            var state2 = $("#state2 option:selected" ).val();
            $.post("anbarEditAjax.php",{id2:id2,name2:name2,location2:location2,state2:state2},function(data){
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
           $.post("anbarDeleteAjax.php",{id3:id3},function(data){
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