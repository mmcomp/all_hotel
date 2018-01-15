<?php
session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadKhadamat($hotel_id)
	{
        $out="";
		mysql_class::ex_sql("select `id`,`name` from `khadamat` where `en`=1 and `hotel_id`=".$hotel_id,$q);
		while($r = mysql_fetch_array($q))
            $out .= "<option value='".$r['id']."'>".$r['name']."</option>";
			//$out[$r['name']]=$r['id'];
		return ($out);
	}
	$sandogh_id = (isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1;
	$sandogh = new sandogh_class($sandogh_id);
	$hotel = new hotel_class($sandogh->hotel_id);
	$sandogh_n[$sandogh->name.'-'.$hotel->name] = $sandogh_id;
	$grid = new jshowGrid_new("sandogh_khadamat","grid1");
	$grid->setERequest(array('sandogh_id'=>$sandogh_id));
	$wer = '1=0';
	if($sandogh_id>0)
		$wer = " `sandogh_id` =$sandogh_id";

	$grid->whereClause = $wer;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="صندوق";
	$grid->columnHeaders[2]="خدمات";
	$grid->columnLists[1]= $sandogh_n;
	$grid->columnLists[2]= loadKhadamat($sandogh->hotel_id) ;
	$grid->intial();
	$grid->executeQuery();
	//$out = $grid->getGrid();

$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">صندوق</th>
                                        <th style=\"text-align:right;\">خدمات</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";

 mysql_class::ex_sql("select * from `sandogh_khadamat` where $wer",$ss);
$i=1;
while($r=mysql_fetch_array($ss)){
    $id = $r['id'];
    $sandogh_id = $r['sandogh_id'];
    mysql_class::ex_sql("select `name` from `sandogh` where `id` = '$sandogh_id' ",$s_id);
    $s_id1 = mysql_fetch_array($s_id);
    $sname = $s_id1['name'];
    $khadamat_id = $r['khadamat_id'];
    mysql_class::ex_sql("select `name` from `khadamat` where `id` = '$khadamat_id' ",$d_id);
    $d_id1 = mysql_fetch_array($d_id);
    $dname = $d_id1['name'];    
    
    if(fmod($i,2)!=0){
        $out.="
       <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$sname</td>
                                        <td>$dname</td>
                                        <td>
                                            <a onclick=\"editGfunc(".$id.",".$sandogh_id.",".$khadamat_id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
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
                                        <td>$sname</td>
                                        <td>$dname</td>
                                        <td>
                                            <a onclick=\"editGfunc(".$id.",".$sandogh_id.",".$khadamat_id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
        ";
        $i++;
    }
    
}
	/*$grid = new jshowGrid_new("hotel_garanti","grid1");
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= 'هتل';
	$grid->columnLists[1]= loadHotel();
	$grid->columnHeaders[2]= 'دفتر';
	$grid->columnLists[2]= loadDaftar();
	$grid->columnHeaders[3]= 'طبقه';
	$grid->columnLists[3]= loadTabaghe();
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();*/
$out.="</tbody></table></div></div>";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>خدمات صندوق</title>
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
    <script type="text/javascript" >
			function filter_frm()
			{
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>خدمات صندوق</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                          
                               
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
                        <div class="col-md-6">
                            <label>صندوق: </label>
                            <input type="text" class="form-control" value="<?php echo $sandogh->name; ?>" disabled >
                            <input name="sid1" type="hidden" class="form-control" value="<?php echo $sandogh->id; ?>" disabled >
                        </div>
                        <div class="col-md-6">
                            <label>خدمات: </label>
                            <select name="khName1" id="khName1" class="form-control">
                            <?php echo loadKhadamat($sandogh->hotel_id) ?>
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
                            <label>خدمات: </label>
                            <select name="khid2" id="khid2" class="form-control">
                            <?php echo loadKhadamat($sandogh->hotel_id) ?>
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
            var khName1 = $("#khName1 option:selected" ).val();
            var sid1 = $("input[name='sid1']" ).val();
           $.post("sandogh_khadamatAjax.php",{khName1:khName1,sid1:sid1},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function editGfunc(id,sandogh_id,khadamat_id){
            StartLoading();
            $("input[name='id2']").val(id);
            //$("#sid2 option[value="+sandogh_id+"]").attr('selected','selected');
            $("#khid2 option[value="+khadamat_id+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var khid2 = $("#khid2 option:selected" ).val();
            var id2 = $("input[name='id2']").val();
           $.post("sandogh_khadamatEditAjax.php",{khid2:khid2,id2:id2},function(data){
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
            $("input[name='id3']").val(gid);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var gid = $("input[name='id3']").val();
           $.post("sandogh_khadamatDeleteAjax.php",{gid:gid},function(data){
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