<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
        $combo=array();

$out='<table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th style="text-align:right;width:1px;">رديف</th>
                                        <th style="text-align:right;">نام</th>
                                        <th style="text-align:right;">ظرفیت</th>
                                        <th style="text-align:right;">سرویس اضافه</th>
                                        <th style="text-align:right;">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    
                                
    mysql_class::ex_sql("select * from `room_typ` where 1=1 order by `name`",$ss);
$i=0;
while($r = mysql_fetch_array($ss))
        {
    $i++;
    $rtid = $r['id'];
    $name = $r['name'];
    $zarfiat = $r['zarfiat'];
    $zarfiat_ezafe = $r['zarfiat_ezafe'];
    $out.="<tr>
                                        <td>$i</td>
                                        <td>$name</td>
                                        <td>$zarfiat</td>
                                        <td>$zarfiat_ezafe</td>
                                        <td>
                                            <a onclick=\"editroomfunc('".$rtid."','".$name."','".$zarfiat."','".$zarfiat_ezafe."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteroomfunc(".$rtid.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>";
}
$out.="</tbody></table>";

$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>Raha</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-home"></i>انواع اتاق ها</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           
                            <a href="#newRoom"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i> تعریف اتاق</button></a>
                            <br/>
                            
                            <?php echo $out;
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
    <div class="modal fade" id="newRoom">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن اتاق</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-6">
                            <label for="name">نام:  </label>
                            <input type="text" name="rtname" value="" class="form-control" />
                        </div>
                        
                        <div class="col-md-6">
                            <label for="name">ظرفیت: </label>
                            <input type="text" name="rtcapa" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label for="name">سرویس اضافه: </label>
                            <input type="text" name="rtzarfiat_ezafe" value="" class="form-control" />
                        </div>
                    </form>	
                </div>
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalrt()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="editRoom">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش اتاق</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="rtid2" value="" class="form-control" />
                        <div class="col-md-6">
                            <label for="name">نام:  </label>
                            <input type="text" name="rtname2" value="" class="form-control" />
                        </div>
                        
                        <div class="col-md-6">
                            <label for="name">ظرفیت: </label>
                            <input type="text" name="rtcapa2" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label for="name">سرویس اضافه: </label>
                            <input type="text" name="rtzarfiat_ezafe2" value="" class="form-control" />
                        </div>
                       
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalrt()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteRoom">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف اتاق</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="rtid3" value="" class="form-control" />
                        آیا از حذف اتاق مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalroom()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
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
        
    function insertFinalrt(){
        StartLoading();
        var rtzarfiat_ezafe = $("input[name='rtzarfiat_ezafe']").val();  
        var rtcapa = $("input[name='rtcapa']").val();  
        var rtname = $("input[name='rtname']").val();
        $.post("roomAjax.php",{rtcapa:rtcapa,rtname:rtname,rtzarfiat_ezafe:rtzarfiat_ezafe},function(data){

            StopLoading();
            if(data=="0")
                alert("خطا در افزودن");
            if(data=="1"){
                alert("افزودن با موفقیت انجام شد");
                location.reload();
            }                         
                                    
        });
    }    
        function editroomfunc(rtid,name,zarfiat,zarfiat_ezafe){
            StartLoading();
            $("input[name='rtid2']").val(rtid);
            $("input[name='rtname2']").val(name);
            $("input[name='rtcapa2']").val(zarfiat);
            $("input[name='rtzarfiat_ezafe2']").val(zarfiat_ezafe);
            $('#editRoom').modal('show');
            StopLoading();
            
        }
        function editFinalrt(){
            StartLoading();
            var rtid2 = $("input[name='rtid2']").val();
            var rtname2 = $("input[name='rtname2']").val();
            var rtcapa2 = $("input[name='rtcapa2']").val();
            var rtzarfiat_ezafe2 = $("input[name='rtzarfiat_ezafe2']").val();
           $.post("roomEditAjax.php",{rtid2:rtid2,rtname2:rtname2,rtcapa2:rtcapa2,rtzarfiat_ezafe2:rtzarfiat_ezafe2},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در ویرایش");
               if(data=="1"){
                   alert("ویرایش با موفقیت انجام شد");
                   location.reload();
               }                      
           });
        }
        function deleteroomfunc(rtid){
            StartLoading();
            $("input[name='rtid3']").val(rtid);
            $('#deleteRoom').modal('show');
            StopLoading();
        }
        function deleteFinalroom(){
            StartLoading();
            var rtid3 = $("input[name='rtid3']").val();
           $.post("roomDeleteAjax.php",{rtid3:rtid3},function(data){
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