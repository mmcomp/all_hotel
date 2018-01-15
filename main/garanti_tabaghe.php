<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
            die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadHotel()
	{
		$out = array();
		mysql_class::ex_sql("select `id`,`name` from `hotel`",$q);
        $i=0;
		while($r=mysql_fetch_array($q,MYSQL_ASSOC)) {
            $out[$i]['name']=$r["name"];
            $out[$i]['id']=(int)$r["id"];
            $i++;
        }
		return $out;
            
	}
	function loadDaftar()
	{
		$out = array();
		mysql_class::ex_sql("select `id`,`name` from `daftar`",$q);	
        $i=0;
		while($r=mysql_fetch_array($q,MYSQL_ASSOC)){
            $out[$i]['name']=$r["name"];
            $out[$i]['id']=(int)$r["id"];
            $i++;
        }
                        //$out[$r["name"]]=(int)$r["id"];
		return $out;
	}
	function loadTabaghe()
	{
		$out = array();
		for($j=1;$j<7;$j++)
		{
			$out[$j] = $j;
		}
		return $out;
	}

$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">هتل</th>
                                        <th style=\"text-align:right;\">دفتر</th>
                                        <th style=\"text-align:right;\">طبقه</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";

 mysql_class::ex_sql("select * from hotel_garanti",$ss);
$i=1;
while($r=mysql_fetch_array($ss)){
    $gid = $r['id'];
    $hotel_id = $r['hotel_id'];
    mysql_class::ex_sql("select `name` from `hotel` where `id` = '$hotel_id' ",$h_id);
    $h_id1 = mysql_fetch_array($h_id);
    $hname = $h_id1['name'];
    $daftar_id = $r['daftar_id'];
    mysql_class::ex_sql("select `name` from `daftar` where `id` = '$daftar_id' ",$d_id);
    $d_id1 = mysql_fetch_array($d_id);
    $dname = $d_id1['name'];
    $tabaghe = $r['tabaghe'];
    
    
    if(fmod($i,2)!=0){
        $out.="
       <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$hname</td>
                                        <td>$dname</td>
                                        <td>$tabaghe</td>
                                        <td>
                                            <a onclick=\"editGfunc(".$gid.",".$hotel_id.",".$daftar_id.",".$tabaghe.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$gid.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
        ";
        $i++;
    }
    else{
        $out.="
        <tr class=\"even\">
                                        <td>$i</td>
                                        <td>$hname</td>
                                        <td>$dname</td>
                                        <td>$tabaghe</td>
                                        <td>
                                            <a onclick=\"editGfunc(".$gid.",".$hotel_id.",".$daftar_id.",".$tabaghe.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$gid.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
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
	<title>لیست گارانتی ها</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>لیست گارانتی ها</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                          <?php
				echo '<h2 style="color:red">'.$GLOBALS['msg'].'</h2>';
			?>
                               
                            <a href="#newG"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن گارانتی جدید</button></a>
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
                    <h4 class="modal-title">افزودن گارانتی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="hid" />
                        <div class="col-md-4">
                            <label>هتل: </label>
                            <select name="hotelName1" id="hotelName1" class="form-control">
                            <?php
                            $ss = loadHotel();
                            $i=0;
                           $cnt = (intval(count($ss)/2))+1;
                            for($i;$i<$cnt;$i++){
                                $id = $ss[$i]['id'];
                                $name = $ss[$i]['name'];
                                echo "
                                    <option value=".$id.">".$name."</option>
                                ";
                            }

                            ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>دفتر: </label>
                            <select name="DaftarName1" id="DaftarName1" class="form-control">
                            <?php $dd = loadDaftar();
                            
                            $i=0;
                            $cnt = (intval(count($dd)/2))+1;
                            for($i;$i<$cnt;$i++){
                                $id = $dd[$i]['id'];
                                $name = $dd[$i]['name'];
                                echo "
                                    <option value=".$id.">".$name."</option>
                                ";
                            }
                            
                            ?>
                                </select>
                        </div>
                        <div name="tabagheh1" id="tabagheh1" class="col-md-4">
                            <label>طبقه: </label>
                            <select class="form-control">
                                <?php $tt = loadTabaghe();
                                foreach ($tt as $t)
                                echo "
                                    <option value=".$t.">".$t."</option>
                                ";
                            
                            ?>
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
                    <h4 class="modal-title">ویرایش گارانتی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="<?php echo $gid ?>" name="gid" />
                        <div class="col-md-4">
                            <label>هتل: </label>
                           <select name="hotelName" id="hotelName" class="form-control">
                            <?php
                            //$selected="";
                            $ss = loadHotel();
                            $i=0;
                           $cnt = (intval(count($ss)/2))+1;
                            for($i;$i<$cnt;$i++){
                                $id = $ss[$i]['id'];
                                $name = $ss[$i]['name'];
                                //if($id==$hotel_id)
                                    //$selected='selected=selected';
                               // else
                                   // $selected="";
                                echo "
                                    <option value=".$id.">".$name."</option>
                                ";
                            }

                            ?>
                          </select>
                        </div>
                        <div class="col-md-4">
                            <label>دفتر: </label>
                            <select name="DaftarName" id="DaftarName" class="form-control">
                            <?php $dd = loadDaftar();
                            //$selected="";
                            $i=0;
                           $cnt = (intval(count($dd)/2))+1;
                            for($i;$i<$cnt;$i++){
                                $id = $dd[$i]['id'];
                                $name = $dd[$i]['name'];
                                //if($id==$daftar_id)
                                    //$selected='selected=selected';
                                //else
                                    //$selected="";

                                echo "
                                    <option value=".$id.">".$name."</option>
                                ";
                            }
                            
                            ?>
                                </select>
                        </div>
                        <div class="col-md-4">
                            <label>طبقه: </label>
                            <select name="tabagheh" id="tabagheh" class="form-control">
                           <?php $tt = loadTabaghe();
                            //$selected="";
                           foreach ($tt as $t){
                               //if($t==$tabaghe)
                                    //$selected='selected=selected';
                               // else
                                   // $selected="";
                               echo "
                                    <option value=".$t.">".$t."</option>
                                ";
                           }
                            ?>
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
                    <h4 class="modal-title">حذف گارانتی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="<?php echo $gid ?>" name="gid" />
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
            var hotelName = $("#hotelName1 option:selected" ).val();
            var DaftarName = $("#DaftarName1 option:selected" ).val();
            var tabagheh = $("#tabagheh1 option:selected" ).val();
           $.post("garanti_tabagheAjax.php",{hotelName:hotelName,DaftarName:DaftarName,tabagheh:tabagheh},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function editGfunc(gid,hotel_id,daftar_id,tabaghe){
            StartLoading();
            $("input[name='gid']").val(gid);
            $("#hotelName option[value="+hotel_id+"]").attr('selected','selected');
            $("#DaftarName option[value="+daftar_id+"]").attr('selected','selected');
            $("#tabagheh option[value="+tabaghe+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var hotelName = $("#hotelName option:selected" ).val();
            var DaftarName = $("#DaftarName option:selected" ).val();
            var tabagheh = $("#tabagheh option:selected" ).val();
            var gid = $("input[name='gid']").val();
           $.post("garanti_tabagheEditAjax.php",{hotelName:hotelName,DaftarName:DaftarName,tabagheh:tabagheh,gid:gid},function(data){
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
            $("input[name='gid']").val(gid);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var gid = $("input[name='gid']").val();
           $.post("garanti_tabagheDeleteAjax.php",{gid:gid},function(data){
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