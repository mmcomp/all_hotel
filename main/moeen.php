<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadKol()
        {
                $out="";
		$se = security_class::auth((int)$_SESSION['user_id']);
		if(/*$se->detailAuth('dafater')*/$_SESSION['daftar_id']!=49)
		{
			$daftar_id = $_SESSION['daftar_id'];
			mysql_class::ex_sql("select `kol_id` from `daftar` where `id`='$daftar_id' order by `id`",$q_kol);
			if($r_kol=mysql_fetch_array($q_kol,MYSQL_ASSOC))
		        {
				$kol_id = $r_kol['kol_id'];
				mysql_class::ex_sql("select * from kol where (`id`='$kol_id' or `user_daftar_id`=$daftar_id) order by name",$q);
				while($r=mysql_fetch_array($q,MYSQL_ASSOC))
				{
				    $out.="<option value='".$r["id"]."'>".$r["name"]." (".$r["code"].")"."</option>";    
                    //$out[$r["name"]." (".$r["code"].")"]=(int)$r["id"];
				}
			}			
		}
		else
		{
		        mysql_class::ex_sql("select * from kol order by name",$q);
		        while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		        {
                    $out.="<option value='".$r["id"]."'>".$r["name"]." (".$r["code"].")"."</option>";
		                //$out[$r["name"]." (".$r["code"].")"]=(int)$r["id"];
		        }
		       
		}
		 return $out;
        }

        function delete_item($id)
        {
                mysql_class::ex_sql("select `id` from `sanad` where `moeen_id` = $id",$q);
                if(!($r = mysql_fetch_array($q)))
                {
                        $q = null;
	                mysql_class::ex_sql("select `id` from `ajans` where `moeen_id` = $id",$q);
        	        if(!($r = mysql_fetch_array($q)))
			{
				$q = null;
				mysql_class::ex_sql("select `id` from `hotel` where `moeen_id` = $id",$q);
				if(!($r = mysql_fetch_array($q)))
					mysql_class::ex_sqlx("delete from `moeen` where `id` = $id");
				else
					$GLOBALS['msg'] = '<span style="color:red;">حساب معین متصل به یک هتل است.</span>';
			}
        	        else
                	        $GLOBALS['msg'] = '<span style="color:red;">حساب معین متصل به یک آژانس است.</span>';
		}
		else
			$GLOBALS['msg'] = '<span style="color:red;">حساب معین دارای سند است.</span>';
        }
        function edit_item($id,$feild,$value)
        {
                if(trim($value) != '')
                        mysql_class::ex_sqlx("update `moeen` set `$feild` = '$value' where `id` = $id");
                else
                        $GLOBALS['msg'] = '<span style="color:red;">مقدار خالی قابل قبول نیست . </span>';
        }
        function add_item()
        {
                $feilds = jshowGrid_new::loadNewFeilds($_REQUEST);
                unset($feilds['id']);
                if(trim($feilds['name']) != '' && trim($feilds['code']) != '' && trim($feilds['kol_id']) > 0)
                {
                        $r = jshowGrid_new::createAddQuery($feilds);
                        mysql_class::ex_sqlx('insert into `moeen` '.$r['fi'].' values '.$r['valu']);
                }
                else
                        $GLOBALS['msg'] = '<span style="color:red;">مقدار خالی قابل قبول نیست . </span>';
        }
	function add_item_auto()
        {
                $feilds = jshowGrid_new::loadNewFeilds($_REQUEST);
//var_dump($feilds);
                unset($feilds['id']);
                if(trim($feilds['name']) != '' && trim($feilds['kol_id']) > 0)
                {
			$kol_code = $feilds['kol_id'];
			$name = $feilds['name'];
                        moeen_class::newByCode_habibi($kol_code,$name);
                }
                else
                        $GLOBALS['msg'] = '<span style="color:red;">مقدار خالی قابل قبول نیست . </span>';
        }
	$GLOBALS['msg'] = '';
	$combo=array();
	$combo["بستانکار"]=1;
	$combo["بدهکار"]=-1;
	$combo["بستانکار/بدهکار"]=0 ;
	$grid = new jshowGrid_new("moeen","grid1");
	//$grid->whereClause=" order by `name`";
	$grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = "کد کل";
	$grid->columnFilters[1] = -1;
	if (!($se->detailAuth('modir')))
		$grid->columnAccesses[1] = 0;
        $grid->columnHeaders[2] = 'کد';
	$grid->columnAccesses[2] = 0;
        $grid->columnHeaders[3] = "نام";
	$grid->columnFilters[3] = '';
        $grid->columnHeaders[4] = "نوع";
        $grid->columnLists[1]=loadKol();
	$grid->columnLists[4]=$combo;
	$grid->sortEnabled = TRUE;
	$grid->pageCount = 200;
	$grid->deleteFunction = 'delete_item';
        $grid->editFunction = 'edit_item';
        $grid->addFunction = 'add_item_auto';	
        $grid->showAddDefault = FALSE;
        $grid->intial();
  	$grid->executeQuery();
       //	$out = $grid->getGrid();
$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">کد کل</th>
                                        <th style=\"text-align:right;\">کد</th>
                                        <th style=\"text-align:right;\">نام</th>
                                        <th style=\"text-align:right;\">نوع</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";
$wer = "";
if($_SESSION['daftar_id']!=49){
	$kols = array();
	mysql_class::ex_sql("select `kol_id` from `daftar` where `id` = ".$_SESSION['daftar_id'],$ss);
	while($r=mysql_fetch_array($ss)){
		$kols[] = $r['kol_id'];
	}
	mysql_class::ex_sql("select `id` from `kol` where `user_daftar_id` = ".$_SESSION['daftar_id'],$ss);
	while($r=mysql_fetch_array($ss)){
		$kols[] = $r['id'];
	}
	$wer = 'where 1=0';
	if(count($kols)>0){
		$wer = 'where `kol_id` in ('.implode(',',$kols).')';
	}
}
 mysql_class::ex_sql("select * from `moeen` $wer",$ss);
$i=1;
while($r=mysql_fetch_array($ss)){
    $id = $r['id'];
    
    $kol_id = $r['kol_id'];
    mysql_class::ex_sql("select `name` from `kol` where `id` = '$kol_id' ",$k_id);
    $k_id1 = mysql_fetch_array($k_id);
    $kname = $k_id1['name'];

    $name = $r['name'];
    
    $code = $r['code'];
    
    $typ = $r['typ'];
    $type="";
    if($typ==0)
        $type="بستانکار / بدهکار";
    else if($typ==1)
        $type="بستانکار";
    else if($typ==-1)
        $type="بدهکار";
    else
        $type="";
    
    
    if(fmod($i,2)!=0){
        $out.="
        <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$kname</td>
                                        <td>$code</td>
                                        <td>$name</td>
                                        <td>$type</td>
                                        <td>
                                
            <a onclick=\"editGfunc('".$id."','".$kol_id."','".$name."','".$typ."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
        
      
                                           <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
        ";
        $i++;
    }
    else{
        $out.="
        <tr class=\"even\">
                                        <td>$i</td>
                                        <td>$kname</td>
                                        <td>$code</td>
                                        <td>$name</td>
                                        <td>$type</td>
                                        <td>
                                
            <a onclick=\"editGfunc('".$id."','".$kol_id."','".$name."','".$typ."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
        
      
                                           <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
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
	<title>حساب معین</title>
    
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
				$("#new_code").hide();
			});
		</script>
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
    <span id='tim' >test2
		</span>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-money"></i>حساب معین</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                          <?php
				echo '<h2 style="color:red">'.$GLOBALS['msg'].'</h2>';
			?>
                               
                            <a href="#newG"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن مورد جدید</button></a>
                            <br/>
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
<div class="modal fade" id="newG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن مورد</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-4">
                            <label>کد کل: </label>
                            <select class="form-control" name="kol1" id="kol1">
                             <?php echo loadKol(); ?>           
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>نام: </label>
                            <input type="text" name="name1" class="form-control" />
                        </div>
                        <div class="col-md-4">
                            <label>نوع: </label>
                            <select class="form-control" name="type1" id="type1">
                                <option value="1">بستانکار</option>
                                <option value="-1">بدهکار</option>
                                <option value="0">بستانکار / بدهکار</option>
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
                    <h4 class="modal-title">ویرایش مورد</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id2" />
                        <div class="col-md-4">
                            <label>کد کل: </label>
                            <select class="form-control" name="kol2" id="kol2">
                             <?php echo loadKol(); ?>           
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>نام: </label>
                            <input type="text" name="name2" class="form-control" />
                        </div>
                        <div class="col-md-4">
                            <label>نوع: </label>
                            <select class="form-control" name="type2" id="type2">
                                <option value="1">بستانکار</option>
                                <option value="-1">بدهکار</option>
                                <option value="0">بستانکار / بدهکار</option>
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
                    <h4 class="modal-title">حذف مورد</h4>
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
			<?php if($conf->hesab_auto){ ?>
			if(document.getElementById('new_kol_id'))
				document.getElementById('new_kol_id').style.display = 'none';
			<?php } ?>
			if(document.getElementById('new_css_class'))
				document.getElementById('new_css_class').style.fontFamily = 'tahoma';
			var inp = document.getElementsByName('new_id');
			for(var i=0;i<inp.length;i++)
				inp[i].style.display = 'none';

			
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
        
       
        function insertFinalG(){
            StartLoading();
            var type1 = $("#type1 option:selected" ).val();
            var kol1 = $("#kol1 option:selected" ).val();
            var name1 = $("input[name='name1']").val();

            $.post("moeenAjax.php",{type1:type1,kol1:kol1,name1:name1},function(data){
                StopLoading();
                if(data=="0")
                    alert("خطا در افزودن");
                if(data=="1"){
                    alert("افزودن با موفقیت انجام شد");
                    location.reload();
                }                                            
            });
        }
        function editGfunc(id,kol_id,name,type){
            StartLoading();
            $("input[name='id2']").val(id);
            $("#kol2 option[value="+kol_id+"]").attr('selected','selected');
            $("#type2 option[value="+type+"]").attr('selected','selected');
            $("input[name='name2']").val(name);
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var kol2 = $("#kol2 option:selected" ).val();
            var type2 = $("#type2 option:selected" ).val();
            var name2 = $("input[name='name2']").val();
            $.post("moeenEditAjax.php",{id2:id2,type2:type2,name2:name2,kol2:kol2},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                   // location.reload();
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
            $.post("moeenDeleteAjax.php",{id3:id3},function(data){
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