<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
function loadUser()
{
	$out="<select class=\"form-control\" id=\"loaduser\">";
	$wer = '';
	if($_SESSION['daftar_id']!=49){
		$wer = "and daftar_id = ".$_SESSION['daftar_id'];
	}
	mysql_class::ex_sql("select `fname`,`lname`,`id`,`daftar_id` from `user` where `user`<>'mehrdad' $wer order by `fname`,`lname`",$q);
	while($r=mysql_fetch_array($q,MYSQL_ASSOC))
	{
		$daftar = new daftar_class($r["daftar_id"]);
		$out.="<option value=\"".$r["id"]."\">".$r["fname"]." ".$r['lname']."(".$daftar->name.")"."<option>";    
		//$out[$r["fname"].' '.$r['lname'].'('.$daftar->name.')']=(int)$r["id"];
	}
	$out.="</select>";
	return $out;
}
function loadUser1($id)
{
    $uid = $id;
    mysql_class::ex_sql("select `fname`,`lname`,`id`,`daftar_id` from `user` where `id`='$uid' order by `fname`,`lname`",$q);
    while($r=mysql_fetch_array($q,MYSQL_ASSOC))
    {
        $daftar = new daftar_class($r["daftar_id"]);
        $out.="".$r["fname"]." ".$r['lname']."(".$daftar->name.")";    
        //$out[$r["fname"].' '.$r['lname'].'('.$daftar->name.')']=(int)$r["id"];
    }
    return $out;
}
function loadUser2()
{
	$out="<select class=\"form-control\" id=\"Use\">";
	$wer = '';
	if($_SESSION['daftar_id']!=49){
		$wer = "and daftar_id = ".$_SESSION['daftar_id'];
	}
	mysql_class::ex_sql("select `fname`,`lname`,`id`,`daftar_id` from `user` where `user`<>'mehrdad' $wer order by `fname`,`lname`",$q);
	while($r=mysql_fetch_array($q,MYSQL_ASSOC))
	{
		$daftar = new daftar_class($r["daftar_id"]);
		$out.="<option value=\"".$r["id"]."\">".$r["fname"]." ".$r['lname']."(".$daftar->name.")"."<option>";    
		//$out[$r["fname"].' '.$r['lname'].'('.$daftar->name.')']=(int)$r["id"];
	}
	$out.="</select>";
	return $out;
}
	function loadSandogh()
        {
        $out="";
		$hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
		if($hotel_id>0)
		{
            $out.="<select class='form-control' id='loadSan'>";
			mysql_class::ex_sql("select * from `sandogh` where `hotel_id`='$hotel_id'",$ss);
            while($r=mysql_fetch_array($ss,MYSQL_ASSOC)){
                $out.='<option value="'.$r["id"].'">'.$r["name"].'</option>';
            }
            $out.="</select>";
			
		}
                return $out;
        }
function loadSandogh1($id)
        {
        $sid = $id;
        $out="";
		$hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
		if($hotel_id>0)
		{
			mysql_class::ex_sql("select * from `sandogh` where `hotel_id`='$hotel_id' and `id` = '$sid'",$ss);
            while($r=mysql_fetch_array($ss,MYSQL_ASSOC)){
                $rname = $r['name'];
                $out.="".$rname."";
            }
			
		}
                return $out;
        }
function loadSandogh2()
        {
        $out="";
		$hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
		if($hotel_id>0)
		{
            $out.="<select class='form-control' id='Sand'>";
			mysql_class::ex_sql("select * from `sandogh` where `hotel_id`='$hotel_id'",$ss);
            while($r=mysql_fetch_array($ss,MYSQL_ASSOC)){
                $out.='<option value="'.$r["id"].'">'.$r["name"].'</option>';
            }
            $out.="</select>";
			
		}
                return $out;
        }
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	if (isset($_REQUEST["hotel_id"]))
                $hotel_id = $_REQUEST["hotel_id"];
	else
		$hotel_id = -1;
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
		$combo_hotel .= "<select style='margin:5px' class='form-control inp' id=\"hotel_id\" name=\"hotel_id\" onchange=\"document.getElementById('selHotel').submit();\"><option value=\"-1\"></option>";
		mysql_class::ex_sql("select * from `hotel` where `id` in $shart order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$hotel_id)
			{
			        $select = "selected='selected'";
			}
			else
			{
			        $select = "";
			}
			$combo_hotel .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
			$combo_hotel .= $r["name"]."\n";
			$combo_hotel .= "</option>\n";
		}
		$combo_hotel .= "</select>";
	$combo_hotel .= "</form>";
    
$out="
<div class=\"box border orange\">
									
									<div class=\"box-body\" style=\"overflow-x:scroll\">
										<table class=\"table table-hover\">
											<thead>
											  <tr>
												<th style=\"text-align:right\">ردیف</th>
												<th style=\"text-align:right\">نام کاربر</th>
												<th style=\"text-align:right\">نام صندوق</th>
                                                <th style=\"text-align:right\">عملیات</th>
											  </tr>
											</thead>
											<tbody>
";

    
	$grid = new jshowGrid_new("sandogh_user","grid1");
	$grid->setERequest(array('hotel_id'=>$hotel_id));
	$wer = '1=0';
	if($hotel_id>0)
	{
		$tmp = implode(',',hotel_class::getSondogh($hotel_id));
		//var_dump(hotel_class::getSondogh($hotel_id));
		if($tmp!='')
			$wer = " `sandogh_id` in ($tmp)";
	}	
	$grid->whereClause = $wer;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="نام کاربر";
	$grid->columnHeaders[2]="نام صندوق";
	$grid->columnLists[1]=loadUser();
	$sandogh = loadSandogh();
	$grid->columnLists[2]= $sandogh ;
	$grid->canAdd = ($hotel_id>0 && $sandogh!=null);
	$grid->intial();
	$grid->executeQuery();
	//$out = $grid->getGrid();
mysql_class::ex_sql("select * from `sandogh_user` where $wer",$ss);
$i=1;
while($r = mysql_fetch_array($ss)){
    $sssid = $r['id'];
    $user_id = $r['user_id'];
    $sandogh_id = $r['sandogh_id'];
    $out.="
    <tr>
        <td>".$i."</td>
        <td>".loadUser1($user_id)."</td>
        <td>".loadSandogh1($sandogh_id)."</td>
        <td><a onclick=\"editssfunc('$sssid','$user_id','$sandogh_id')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deletessfunc('$sssid')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a></td>
    </tr>
    ";
    $i++;
}

$out.="</tbody></table></div></div>";




$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>دسترسی فرانت آفیس</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-desktop"></i>دسترسی فرانت آفیس</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           
                            <a href="#newAcc"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن دسترسی</button></a>
                            <br/>
                            <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">هتل:</label> 
                                    <div class="col-md-9">
                                             <?php echo $combo_hotel; ?>
                                    </div>
                                </div>
                            
                           
			<?php echo $out;  ?>
                            
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
    <div class="modal fade" id="newAcc">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن دسترسی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-6">
                            <label>نام کاربر:  </label>
                            <?php echo loadUser(); ?>
                        </div>
                        
                        <div class="col-md-6">
                            <label>نام صندوق: </label>
                            <?php echo loadSandogh(); ?>
                        </div>
                    </form>	
                </div>
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalss()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="editss">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش دسترسی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="sssid2" value="" class="form-control" />
                        <div class="col-md-6">
                            <label>نام کاربر:  </label>
                            <?php echo loadUser2(); ?>
                        </div>
                        
                        <div class="col-md-6">
                            <label>نام صندوق: </label>
                            <?php echo loadSandogh2(); ?>
                        </div>
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalss()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deletess">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف دسترسی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="sssid3" value="" class="form-control" />
                        آیا از حذف دسترسی مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalss()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
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
        
    function insertFinalss(){
        StartLoading();
        var user = $("#loaduser option:selected" ).val();
        var sandogh = $("#loadSan option:selected" ).val();
        $.post("sandogh_userAjax.php",{user:user,sandogh:sandogh},function(data){
            StopLoading();
            if(data=="0")
                alert("خطا در افزودن");
            if(data=="1"){
                alert("افزودن با موفقیت انجام شد");
                location.reload();
            }
                                            
                                    
        });
    }    
        function editssfunc(id,user,sandogh){
            StartLoading();
            $("#Use option[value="+user+"]").attr('selected','selected');
            $("#Sand option[value="+sandogh+"]").attr('selected','selected');
            $("input[name='sssid2']").val(id);
            $('#editss').modal('show');
            StopLoading(); 
        }
        function editFinalss(){
            StartLoading();
            var sssid2 = $("input[name='sssid2']").val();
            var Use = $("#Use option:selected" ).val();
            var Sand = $("#Sand option:selected" ).val();
           $.post("sandogh_userEditAjax.php",{sssid2:sssid2,Use:Use,Sand:Sand},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در ویرایش");
               if(data=="1"){
                   alert("ویرایش با موفقیت انجام شد");
                   location.reload();
               }                      
           });
        }
        function deletessfunc(ssid){
            StartLoading();
            $("input[name='sssid3']").val(ssid);
            $('#deletess').modal('show');
            StopLoading();
        }
        function deleteFinalss(){
            StartLoading();
            var sssid3 = $("input[name='sssid3']").val();
           $.post("sandogh_userDeleteAjax.php",{sssid3:sssid3},function(data){
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