<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$GLOBALS['msg'] = '';
	if (isset($_REQUEST["hotel_id"]))
        {
                $hotel_id=$_REQUEST["hotel_id"];
        }
        else
        {
                $hotel_id=-1;
        }
	function loadHotel()
        {
                $out=null;
                mysql_class::ex_sql("select * from hotel order by name",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out[$r["name"]]=(int)$r["id"];
                return $out;
        }
	function loadRoom()
        {
                $out = null;
                mysql_class::ex_sql("select * from room_typ order by zarfiat",$q);
                $i=0;
                while($r=mysql_fetch_array($q,MYSQL_ASSOC)){
                    $out[$i]['name']=$r["name"];
                    $out[$i]['id']=(int)$r["id"];
                    $out['index']=$i;
                    $i++;
                }
                        //$out[$r["name"]]=(int)$r["id"];
                return $out;
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
		$fields["vaziat"] = '2';
		if($fields['room_typ_id']!='' && $fields['name']!='' )
		{
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
			$query="insert into `room` $fi values $valu";
			mysql_class::ex_sqlx($query);
		}
		else
		{
			$GLOBALS['msg'] = 'نام اتاق یا نوع آن را وارد کنید';
		}
	}
	function delete_item($inp)
	{
		mysql_class::ex_sqlx("update `room` set `en`=0 where `id`=$inp");
	}
        $combo = "";
	$combo .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
	$combo .= "هتل : <select class='form-control inp' id=\"hotel_id\" name=\"hotel_id\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
	mysql_class::ex_sql("select * from `hotel` order by `name`",$q);
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
                $combo .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
                $combo .= $r["name"]."\n";
                $combo .= "</option>\n";
        }
	function loadPic($id)
	{
		$out = "<a href=\"loadPic.php?room_id=$id\" target=\"_blank\">ادامه</a>";
		return($out);
	}
        $combo .="</select>";
	$combo .= "</form>";
//$grid = new jshowGrid_new("room","grid1");
//$rec["hotel_id"] = $hotel_id;
//$grid->setERequest($rec);



$rsout1="";

$rsout1.="

 <table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">نوع اتاق</th>
                                        <th style=\"text-align:right;\">نام</th>
                                        <th style=\"text-align:right;\">توضیحات</th>
                                        <th style=\"text-align:right;\">شماره طبقه</th>
                                        <th style=\"text-align:right;\">قیمت(ریال)</th>
                                        <th style=\"text-align:right;\">تصویر اتاق</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>

";
   mysql_class::ex_sql("select * from `room` where  `hotel_id`='$hotel_id' and `en`= 1 order by `name`,`room_typ_id`",$ss);
$i=1;
		while($r = mysql_fetch_array($ss))
		{
            $id = $r['id'];
            $room_typ_id = $r['room_typ_id'];
            mysql_class::ex_sql("select `name` from `room_typ` where `id` = '$room_typ_id' ",$rtyp_id);
            $rtyp_id1 = mysql_fetch_array($rtyp_id);
            $rtypname = $rtyp_id1['name'];
            $name = $r['name'];
            $tozih = $r['tozih'];
            $tabaghe = $r['tabaghe'];
            $ghimat = $r['ghimat'];
            
            if(fmod($i,2)!=0){
                $rsout1.="
                    <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$rtypname</td>
                                        <td>$name</td>
                                        <td>$tozih</td>
                                        <td>$tabaghe</td>
																				<td>$ghimat</td>
                                        <td>".loadPic($id)."</td>
                                        <td>
                                            <a onclick=\"editroomsfunc('".$id."','".$room_typ_id."','".$name."','".$tabaghe."','".$tozih."',$ghimat)\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteroomsfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
                ";
                $i++;
            }
            else{
                $rsout1.="
                
                <tr class=\"even\">
                                        <td>$i</td>
                                        <td>$rtypname</td>
                                        <td>$name</td>
                                        <td>$tozih</td>
                                        <td>$tabaghe</td>
																				<td>$ghimat</td>
                                        <td>".loadPic($id)."</td>
                                        <td>
                                            <a onclick=\"editroomsfunc('".$id."','".$room_typ_id."','".$name."','".$tabaghe."','".$tozih."',$ghimat)\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteroomsfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
                
                ";
                $i++;
            }
            
            
        } 

$rsout1.="  </tbody></table>";

$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>لیست اتاق ها</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>لیست اتاق ها</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                          <?php
				echo '<h2 style="color:red">'.$GLOBALS['msg'].'</h2>';
			?>
                               
                            <a href="#newRooms"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن اتاق جدید</button></a>
                            <br/>
                            <?php  echo $combo; ?>
                            
                            
                         <?php echo $rsout1; ?>
                        
                           
                            
                            
                            
                                    
                              
                               
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
<div class="modal fade" id="newRooms">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن اتاق</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="<?php echo $hotel_id ?>" name="hid1" />
                        <div class="col-md-6">
                            <label>نوع اتاق: </label>
                            <select class='form-control' id="rtype1" name="rtype1">
                                <?php
                            $ss = loadRoom();
                            $cnt2 = $ss['index'];
                            $cnt2++;
                            $i=0;
                            for($i;$i<$cnt2;$i++){
                                $id = $ss[$i]['id'];
                                $name = $ss[$i]['name'];
                                echo "
                                    <option value=".$id.">".$name."</option>
                                ";
                            }

                            ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="rname">نام: </label>
                            <input type="text" name="rname1" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label>توضیحات: </label>
                            <input type="text" name="rtozih1" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label>شماره طبقه: </label>
                            <input type="text" name="rtabaghe1" value="" class="form-control" />
                        </div>
                        <div class="col-md-12">
                            <label>قیمت (ریال) : </label>
                            <input type="text" name="rghimat1" value="" class="form-control" />
                        </div>
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalrooms()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="editRooms">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش اتاق</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="rrid1" value="" class="form-control" />
                        <div class="col-md-6">
                            <label>نوع اتاق: </label>
                            <select class='form-control' id="rtype2" name="rtype2">
                                <?php
                            $ss = loadRoom();
                            $cnt2 = $ss['index'];
                            $cnt2++;
                            $i=0;
                            for($i;$i<$cnt2;$i++){
                                $id = $ss[$i]['id'];
                                $name = $ss[$i]['name'];
                                echo "
                                    <option value=".$id.">".$name."</option>
                                ";
                            }

                            ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>نام: </label>
                            <input type="text" name="rname2" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label>توضیحات: </label>
                            <input type="text" name="rtozih2" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label>شماره طبقه: </label>
                            <input type="text" name="rtabaghe2" value="" class="form-control" />
                        </div>
                        <div class="col-md-12">
                            <label>قیمت (ریال) : </label>
                            <input type="text" name="rghimat2" value="" class="form-control" />
                        </div>
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalrooms()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteRooms">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف اتاق</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                         <input type="hidden" name="rrid2" value="" class="form-control" />
                        آیا از حذف اتاق مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalrooms()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
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
        function insertFinalrooms(){
            StartLoading();
            


            var hid1 = $("input[name='hid1']").val();
            var rtype1 = $("#rtype1 option:selected" ).val();
            var rname1 = $("input[name='rname1']").val();
            var rtozih1 = $("input[name='rtozih1']").val();
            var rtabaghe1 = $("input[name='rtabaghe1']").val();
            var rghimat1 = $("input[name='rghimat1']").val();
            $.post("roomsAjax.php",{hid1:hid1,rtype1:rtype1,rname1:rname1,rtozih1:rtozih1,rtabaghe1:rtabaghe1,rghimat1:rghimat1},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در افزودن");
               if(data=="1"){
                   alert("افزودن با موفقیت انجام شد");
                   location.reload();
               }                     
            });
        }
        function editroomsfunc(id,room_typ_id,name,tabaghe,tozih,ghimat){
            StartLoading();

            $("input[name='rrid1']").val(id);
            $("#rtype2 option[value="+room_typ_id+"]").attr('selected','selected');
            $("input[name='rname2']").val(name);
            $("input[name='rtozih2']").val(tozih);
            $("input[name='rtabaghe2']").val(tabaghe);
            $("input[name='rghimat2']").val(ghimat);
            $('#editRooms').modal('show');
            StopLoading();
        
        }
        function editFinalrooms(){
            StartLoading();
            var rrid1 = $("input[name='rrid1']").val();
            var rname2 = $("input[name='rname2']").val();
            var rtozih2 = $("input[name='rtozih2']").val();
            var rtype2 = $("#rtype2 option:selected" ).val();
            var rtabaghe2 = $("input[name='rtabaghe2']").val();
            var rghimat2 = $("input[name='rghimat2']").val();
           $.post("roomsEditAjax.php",{rrid1:rrid1,rname2:rname2,rtozih2:rtozih2,rtype2:rtype2,rtabaghe2:rtabaghe2,rghimat2:rghimat2},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در ویرایش");
               if(data=="1"){
                   alert("ویرایش با موفقیت انجام شد");
                   location.reload();
               }
                                        
                                    
           });
        }
        function deleteFinalrooms(){
            StartLoading();
            var rrid2 = $("input[name='rrid2']").val();
           $.post("roomsDeleteAjax.php",{rrid2:rrid2},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در حذف");
               if(data=="1"){
                   alert("حذف با موفقیت انجام شد");
                   location.reload();
               }
                                          
           });
        }
        function deleteroomsfunc(id){
            StartLoading();
            $("input[name='rrid2']").val(id);
            $('#deleteRooms').modal('show');
            StopLoading();            
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
	
<script>
	$("input,select").css("z-index","1000");
	$("input,select").parent().css("z-index","100");
</script>
</body> 
</html>