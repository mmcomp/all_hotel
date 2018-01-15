<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadHotels($id = -1)
	{
		$out = "";
		$id = (int)$id;
		$wer = '';
		if($_SESSION['daftar_id']!=49){
			$hl = daftar_class::hotelList($_SESSION['daftar_id']);
			$wer = 'where 1=0';
			if(count($hl)>0){
				$wer = 'where id in ('.implode(',',$hl).')';
			}
		}
		mysql_class::ex_sql("select * from `hotel` $wer order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$out .= '<option value="'.(int)$r['id'].'"'.(($id == (int)$r['id'])?'selected="selected"':'').' >';
			$out .= $r['name'];
			$out .= '</option>';
		}
		return($out);
	}
	function hpdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function hpdateback($inp)
	{
		return(audit_class::hamed_pdateBack(perToEnNums($inp)));
	}
        function add_item()
        {
                $fields = null;

                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id" && $key != "new_en" )
                                {
                                        $fields[substr($key,4)] = $value;
                                }
                        }
                }
                if (isset($_REQUEST["hotel_id"]))
                {
                        $hotel_id=$_REQUEST["hotel_id"];
                }
                else
                {
                        $hotel_id=-1;
                }
                $fields["hotel_id"] = $hotel_id;
		$fields["aztarikh"] = audit_class::hamed_pdateBack($fields["aztarikh"]);
		$fields["tatarikh"] = audit_class::hamed_pdateBack($fields["tatarikh"]);
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
                $query="insert into `hotel_working_date` $fi values $valu";
                mysql_class::ex_sqlx($query);
        }
	$hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);

$hwout1="";

$hwout1.="

 <table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">از تاریخ</th>
                                        <th style=\"text-align:right;\">تا تاریخ</th>
                                        <th style=\"text-align:right;\">نوع</th>
                                        <th style=\"text-align:right;\">قیمت</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>

";
   mysql_class::ex_sql("select * from `hotel_working_date` where  `hotel_id` = $hotel_id order by `hotel_id`",$ss);
$i=1;
		while($r = mysql_fetch_array($ss))
		{
            $hwid = $r['id'];
            $aztarikh = $r['aztarikh'];
            $azta=jdate('Y/n/j',strtotime($aztarikh));
            $tatarikh = $r['tatarikh'];
            $tata=jdate('Y/n/j',strtotime($tatarikh));
            $typ = $r['typ'];
            $ntyp="";
            if($typ==0)
                $ntyp="معمولی";
            if($typ==1)
                $ntyp="پیک";
            $ghimat = $r['ghimat'];
            
            if(fmod($i,2)!=0){
                $hwout1.="
                    <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$azta</td>
                                        <td>$tata</td>
                                        <td>$ntyp</td>
                                        <td>$ghimat</td>
                                        <td>
                                            <a onclick=\"edithotelworkfunc('".$hwid."','".$azta."','".$tata."','".$typ."','".$ghimat."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deletehotelworkfunc(".$hwid.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
                ";
                $i++;
            }
            else{
                $hwout1.="
                
                <tr class=\"even\">
                                        <td>$i</td>
                                        <td>$azta</td>
                                        <td>$tata</td>
                                        <td>$ntyp</td>
                                        <td>$ghimat</td>
                                        <td>
                                            <a onclick=\"edithotelworkfunc('".$hwid."','".$azta."','".$tata."','".$typ."','".$ghimat."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deletehotelworkfunc(".$hwid.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
                
                ";
                $i++;
            }
            
            
        } 

        
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>زمان فعالیت</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-clock-o"></i>زمان فعالیت</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                          
                               
                            <a href="#newHotelWork"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن اطلاعیه جدید</button></a>
                            <br/>
                             <form class="row" id="frm1" method="get">
                                 <div class="col-md-4" style="margin:5px 0">
                               هتل : <select class="form-control inp" name="hotel_id" onchange="document.getElementById('frm1').submit();">
						<?php
							echo loadHotels($hotel_id);
						?>
					</select>
                                     </div>
                                 </form>
                            
                            
                            
                        <?php echo $hwout1; ?>
                           
                            
                            
                            
                                    
                                </tbody>
                            </table>
                               
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
<div class="modal fade" id="newHotelWork">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن اطلاعیه</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="<?php echo $hotel_id ?>" name="hid1" />
                        <div class="col-md-6">
                            <label>از تاریخ:  </label>
                            <input type="text" id="datepicker1" name="azta1" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label>تا تاریخ:  </label>
                            <input type="text" id="datepicker2" name="tata1" value="" class="form-control" />
                        </div>
                        
                        <div class="col-md-6">
                            <label>نوع: </label>
                            <select class='form-control' id="type1" name="type1">
                                <option value="0">معمولی</option>
                                <option value="1">پیک</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>قیمت: </label>
                            <input type="text" name="cost1" value="" class="form-control" />
                        </div>
                       
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalhw()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="editHotelWork">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش اطلاعیه</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="hwid2" value="" class="form-control" />
                       <div class="col-md-6">
                            <label for="name">از تاریخ:  </label>
                            <input type="text" id="datepicker3" name="azta2" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label for="name">تا تاریخ:  </label>
                            <input type="text" id="datepicker4" name="tata2" value="" class="form-control" />
                        </div>
                        
                        <div class="col-md-6">
                            <label for="name">نوع: </label>
                            <select class='form-control' id="type2" name="type2">
                                <option value="0">معمولی</option>
                                <option value="1">پیک</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="name">قیمت: </label>
                            <input type="text" name="cost2" value="" class="form-control" />
                        </div>
                       
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalhotelwork()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteHotelWork">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف اطلاعیه</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="hwid3" value="" class="form-control" />
                        آیا از حذف اطلاعیه مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalhotelwork()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
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
	

	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.fa.min.js"></script>
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
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker4").datepicker({
                    dateFormat: "yy/mm/dd",
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
        function insertFinalhw(){
            StartLoading();
            var hid1 = $("input[name='hid1']").val(); 
            var azta1 = $("input[name='azta1']").val(); 
            var tata1 = $("input[name='tata1']").val(); 
            var type1 = $("#type1 option:selected" ).val(); 
            var cost1 = $("input[name='cost1']").val(); 
            $.post("hotelworkAjax.php",{hid1:hid1,azta1:azta1,tata1:tata1,type1:type1,cost1:cost1},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در افزودن");
               if(data=="1"){
                   alert("افزودن با موفقیت انجام شد");
                   location.reload();
               }                          
           });
        }
        function edithotelworkfunc(hwid,aztarikh,tatarikh,typ,ghimat){
            StartLoading();
            $("input[name='hwid2']").val(hwid);
            $("input[name='azta2']").val(aztarikh);
            $("input[name='tata2']").val(tatarikh);
            $("#type2 option[value="+typ+"]").attr('selected','selected');
            $("input[name='cost2']").val(ghimat);
            
            $('#editHotelWork').modal('show');
            StopLoading();
        }
        function editFinalhotelwork(){
            StartLoading();
            var hwid2 = $("input[name='hwid2']").val();
            var azta2 = $("input[name='azta2']").val();
            var tata2 = $("input[name='tata2']").val();
            var type2 = $("#type2 option:selected" ).val();
            var cost2 = $("input[name='cost2']").val();
             
            $.post("hotelworkEditAjax.php",{hwid2:hwid2,azta2:azta2,tata2:tata2,type2:type2,cost2:cost2},function(data){
                StopLoading();
                if(data=="0")
                    alert("خطا در ویرایش");
                if(data=="1"){
                    alert("ویرایش با موفقیت انجام شد");
                    location.reload();
                }
                                          
                                     
            });
        }
        function deletehotelworkfunc(hwid){
            StartLoading();
            $("input[name='hwid3']").val(hwid);
            $('#deleteHotelWork').modal('show');
            StopLoading();
        }
        function deleteFinalhotelwork(){
            StartLoading();
            var hwid3 = $("input[name='hwid3']").val();
            $.post("hotelworkDeleteAjax.php",{hwid3:hwid3},function(data){
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