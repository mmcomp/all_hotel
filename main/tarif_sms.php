<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //if(!$se->can_view)
              //  die(lang_fa_class::access_deny);
	$shart_1 = '';
	$sabtShod = '';
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart = '';
	if($hotel_acc!=null)
	{
		if (count($hotel_acc)==1)
			$_REQUEST["hotel_id_new"] = $hotel_acc[0];
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
		$shart_1 = "where `id` in ".$shart;
	}
////////////////////
	if (isset($_REQUEST["mod"]))
                $mod = $_REQUEST["mod"];
	else
		$mod = -1;
	if (isset($_REQUEST["hotel_id_new"]))
                $hotel_id_new = $_REQUEST["hotel_id_new"];
	else
		$hotel_id_new = -1;
	if (isset($_REQUEST["sms_typ"]))
                $sms_typ = $_REQUEST["sms_typ"];
	else
		$sms_typ = -1;
	if (isset($_REQUEST["def_matn"]))
		$def_matn = $_REQUEST["def_matn"];
	else
		$def_matn = -1;
	if (isset($_REQUEST["save_sms"]))
                $save_sms = $_REQUEST["save_sms"];
	else
		$save_sms = -1;
	if (isset($_REQUEST["del_sms"]))
		$del_sms = $_REQUEST["del_sms"];
	else
		$del_sms= -1;
	if (($hotel_id_new != -1)&&($sms_typ != -1))
	{
		mysql_class::ex_sqlx("insert into `mehman_sms` (`id`, `matn`, `typ`, `hotel_id`) values (NULL, '$def_matn', '$sms_typ', '$hotel_id_new')");
		$sabtShod = "پیامک جدید ثبت شد";
	}
	$combo_hotel = "";
	$combo_hotel .= "<select class='form-control inp' id=\"hotel_id\" name=\"hotel_id_new\" ><option value=\"-1\"></option>";
	mysql_class::ex_sql("select * from `hotel` $shart_1 order by `name`",$q);
	while($r = mysql_fetch_array($q))
	{
		if((int)$r["id"]== (int)$hotel_id_new)
	        {
	                $select = "selected='selected'";
	        }
	        else
	        {
	                $select = "";
	        }
	        $combo_hotel .= "<option value=\"".(int)$r["id"]."\" $select   >";
	        $combo_hotel .= $r["name"]."";
	        $combo_hotel .= "</option>";
	}
	$combo_hotel .= "</select>";
	$combo_typ = '';
	$combo_typ .= "<select class='form-control inp' id=\"sms_typ\" name=\"sms_typ\"><option value=\"-1\"></option>";
	mysql_class::ex_sql("select * from `sms_typ`order by `typ`",$q);
	while($r = mysql_fetch_array($q))
	{
		if((int)$r["id"]== (int)$sms_typ)
	        {
	                $select = "selected='selected'";
	        }
	        else
	        {
	                $select = "";
	        }
	        $combo_typ .= "<option value=\"".(int)$r["id"]."\" $select   >";
	        $combo_typ .= $r["typ"]."";
	        $combo_typ .= "</option>";
	}
	$combo_typ .= "</select>";
	$matns = '';
	if (isset($_REQUEST['mod']))
	{
		foreach($_REQUEST as $name)
		{
			$matns .= $name.',';
		}
		$re = explode(",",$matns);
		$id = $re[0];
		$typ = $re[1];
		$matn_sms = $re[2];
		if (($id!='')&&($typ!='')&&($matn_sms!='')&&($save_sms==1))
		{
			mysql_class::ex_sqlx("update `mehman_sms` SET `matn` = '$matn_sms' WHERE `id` ='$id'");
			$sabtShod = "متن پیامک با موفقیت تغییر یافت";
		}
		elseif(($id!='')&&($typ!='')&&($matn_sms!='')&&($del_sms==2))
		{
			mysql_class::ex_sqlx("delete from `mehman_sms` where `id` = '$id'");
			$sabtShod = "پیامک مورد نظر حذف شد";
		}
		else
			$sabtShod = "";	
		
	}
	if (isset($_REQUEST['new_rec']))
	{
		foreach($_REQUEST as $name)
		{
			$matns .= $name.',';
		}
		$re = explode(",",$matns);
		$id = $re[0];
		$typ = $re[1];
		$matn_sms = $re[2];
		if (($id!='')&&($typ!='')&&($matn_sms!=''))
		{
			mysql_class::ex_sqlx("update `mehman_sms` SET `matn` = '$matn_sms' WHERE `id` ='$id'");
		}
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>تعریف پیامک</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-envelope"></i>تعریف پیامک</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                           
                            <a href="#newM"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن پیام</button></a>
                            <br/>
                            <h3><?php echo $sabtShod;?></h3>
                            <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th style="text-align:right;width:1px;">رديف</th>
                                        <th style="text-align:right;">هتل</th>
                                        <th style="text-align:right;">نوع</th>
                                        <th style="text-align:right;">متن پیامک</th>
                                        <th style="text-align:right;">عملیات</th>
                                    </tr>
                                </thead><tbody>
                                <?php
				mysql_class::ex_sql("select * from `mehman_sms`",$q);
				$i = 1;
				$noe = '';
				while($r=mysql_fetch_array($q))
				{
					$row_style = 'class="odd"';
                    $sms_id = $r['id'];
					$matn = $r['matn'];
					$noe_sms = $r['typ'];
					$hotel_id = $r['hotel_id'];
					mysql_class::ex_sql("select * from `sms_typ` where `id`='$noe_sms'",$q_sms);
					if($r_sms=mysql_fetch_array($q_sms))
						$noe = $r_sms['typ'];
					mysql_class::ex_sql("select * from `hotel` where `id`='$hotel_id'",$q_hotel);
					if($r_hotel=mysql_fetch_array($q_hotel))
						$hotel_name = $r_hotel['name'];
					else
						$hotel_name = "";
					if($i%2==0)
						$row_style = 'class="even"';
					$matn_name = "matn_".$i;
					$f_name = "frm1_".$i;
			?>
				
                    <tr <?php echo $row_style; ?> >
					<td valign="center" ><?php echo $i;?></td>
					<td valign="center" ><?php echo $hotel_name;?></td>
					<td valign="center" ><?php echo $noe;?></td>
					<td valign="top" >
						<input type='hidden' name="id_sms_<?php echo $i;?>" id="id_sms_<?php echo $i;?>" value="<?php echo $r['id'];?>" >
						<input type='hidden' name="typ_sms_<?php echo $i;?>" id="typ_sms_<?php echo $i;?>" value="<?php echo $noe_sms;?>" >
						<?php echo $matn; ?>
					</td>
					
					<td>
                        <a onclick="editMfunc('<?php echo $sms_id ?>','<?php echo $matn ?>','<?php echo $noe ?>','<?php echo $hotel_name ?>')" data-toggle="modal"><button style="margin:5px;min-width:90px;" class="btn btn-info"><i class="fa fa-pencil-square-o"></i> ویرایش</button></a>
                                            <a onclick="deletesmsfunc(<?php echo $sms_id ?>)" data-toggle="modal"><button style="margin:5px;min-width:90px;" class="btn btn-danger"><i class="fa fa-times"></i> حذف</button>
                        
                        <input type='hidden' name='mod' id='mod' value='1' >
						<!--<input type="radio" name="save_sms" value="1">ذخیره
						<input type="radio" name="del_sms" value="2">حذف
						<input type="submit" value="ارسال تغییرات" class="inp"/>	-->	
                        
					</td>
				</tr>
			
			<?php 
				$i++;
				}
			?>
                            
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
<div class="modal fade" id="newM">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن پیام</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-4">
                            <label>نام هتل:  </label>
                            <?php echo $combo_hotel; ?>
                        </div>
                        <div class="col-md-4">
                            <label>نوع پیامک:  </label>
                            <?php echo $combo_typ; ?>
                        </div>
                        <div class="col-md-4">
                            <label>متن پیش فرض:  </label>
                            <textarea id="matn" name="matn" class="form-control"></textarea>
                        </div>
                       
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalSms()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    
    <div class="modal fade" id="editM">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش پیامک</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="<?php echo $sms_id ?>" name="smsid" />
                        <div class="col-md-4">
                            <label for="name">نام هتل:  </label>
                            <input type="text" name="names" value="" class="form-control" disabled />
                        </div>
                        <div class="col-md-4">
                            <label for="name">نوع پیامک:  </label>
                            <input type="text" name="types" value="" class="form-control" disabled />
                        </div>
                        <div class="col-md-4">
                            <label for="name">متن پیش فرض:  </label>
                            <textarea id="matns" name="matns" class="form-control"></textarea>
                        </div>
                       
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalsms()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="deleteM">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف پیامک</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="<?php echo $smsid ?>" name="smsid" />
                        آیا از حذف پیامک مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalsms()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
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
        function insertFinalSms(){
            StartLoading();
            var hotel_id = $("#hotel_id option:selected" ).val();
            var sms_typ = $("#sms_typ option:selected" ).val();
            var matn = $("textarea#matn").val();            
           $.post("tarif_smsAjax.php",{hotel_id:hotel_id,sms_typ:sms_typ,matn:matn},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function editMfunc(sms_id,matn,noe_sms,hotel_id){
            StartLoading();
            $("input[name='smsid']").val(sms_id);
            $("input[name='names']").val(hotel_id);
            $("input[name='types']").val(noe_sms);
            $("textarea#matns").val(matn);
            
            $('#editM').modal('show');
            StopLoading();
        }
        function editFinalsms(){
            StartLoading();
            var sms_id = $("input[name='smsid']").val();
            var matn = $("textarea#matns").val();
           $.post("tarif_smsEditAjax.php",{sms_id:sms_id,matn:matn},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در ویرایش");
               if(data=="1"){
                   alert("ویرایش با موفقیت انجام شد");
                   location.reload();
               }
                                          
                                     
           });
        }
        function deletesmsfunc(id){
            StartLoading();
            $("input[name='smsid']").val(id);
            $('#deleteM').modal('show');
            StopLoading();
        }
        function deleteFinalsms(){
            StartLoading();
            var smsid = $("input[name='smsid']").val();
           $.post("tarif_smsDeleteAjax.php",{smsid:smsid},function(data){
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