<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadHotel()
        {
        $out="";
		$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
		$shart = '';
		if($hotel_acc!=null)
		{
			for($l=0;$l<count($hotel_acc);$l++)
				$shart.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
		}
                mysql_class::ex_sql("select `name`,`id` from `hotel` where `id` in $shart order by `name`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                    $out.="<option value='".$r["id"]."'>".$r["name"]."</option>";
                        //$out[$r["name"]]=(int)$r["id"];
                return $out;
        }
	function add_item($id)
	{
		$fields = null;
                foreach($_REQUEST as $key => $value)
                        if(substr($key,0,4)=="new_")
                                if($key != "new_id" && $key != "new_moeen_id" &&  $key != "new_moeen_cash_id" )
                                        $fields[substr($key,4)] =perToEnNums($value);
		$hotel = new hotel_class((int)$fields['hotel_id']);
		$kol = new moeen_class($hotel->moeen_id);
		$moeen_id = moeen_class::addById($kol->kol_id,'درآمد صندوق '.$fields['name']);
		$moeen_cash_id = moeen_class::addById($kol->kol_id,'درآمد متفرقه '.$fields['name']);
		$fields['moeen_id'] = $moeen_id ;
		$fields['moeen_cash_id'] = $moeen_cash_id;
		$query = '';
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
        	$query.="insert into `sandogh` $fi values $valu";
		mysql_class::ex_sqlx($query);
	}
	function delete_item($id)
	{
		$id = (int)$id;
		mysql_class::ex_sqlx("delete from `sandogh` where `id` = $id");
		mysql_class::ex_sqlx("delete from `sandogh_user` where `sandogh_id` = $id");
	}
	function loadType()
	{
		$out['دریافت نقدی داشته باشد'] = 1 ;
		$out['دریافت نقدی نکند'] = -1 ;
		return $out;
	}
	function loadIcon($id)
    {
		$ls = new listBox_class;
		$ls->input = $id;
		$ls->onClick = 'f1';
		$ls->vertical  = FALSE;
		$ls->height = '100%';
		$ls->imageHeight = '30px';
		$ls->imageWidth = '';
		$ls->width = '100%';
		$san = new sandogh_class($id);
		$img = $san->icon;
		$img = explode('/',$img);
		$img = $img[count($img)-1];
		$ls->selected = $img;
		$out = $ls->getOutput();
        return '<center>'.$out.'</center>';
    }
function loadIcon1()
    {
		$ls = new listBox_class;
		//$ls->input = $id;
		//$ls->onClick = 'f1';
		$ls->vertical  = FALSE;
		$ls->height = '100%';
		$ls->imageHeight = '30px';
		$ls->imageWidth = '';
		$ls->width = '100%';
		//$san = new sandogh_class($id);
		//$img = $san->icon;
		//$img = explode('/',$img);
		//$img = $img[count($img)-1];
		//$ls->selected = $img;
		$out = $ls->getOutput();
        return $out;
    }
	function loadKhadamat($inp)
	{
		$out='<div class="msg" ><a href="sandogh_khadamat.php?sandogh_id='.$inp.'" target="_blank" >خدمات</a></div>';
		return($out);
	}
	if(isset($_REQUEST['mod']) && $_REQUEST['mod']='updateImg' )
	{
		$id= (int)$_REQUEST['id'];
		$img = $_REQUEST['img'];
		mysql_class::ex_sqlx("update `sandogh` set `icon`='../icon/$img' where `id`=$id");
	}
$out='<div class="box border orange">
									
									<div class="box-body" style="overflow-x:scroll">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">نام</th>
												<th style="text-align:right">هتل</th>
                                                <th style="text-align:right">نوع</th>
                                                <th style="text-align:right">آیکون</th>
                                                <th style="text-align:right">خدمات</th>
                                                <th style="text-align:right">عملیات</th>
											  </tr>
											</thead>
											<tbody>';
	$grid = new jshowGrid_new("sandogh","grid1");
	$grid->index_width = '30px';
	$grid->columnHeaders[0] = null;
    $grid->columnHeaders[1] = "نام";
	$grid->columnHeaders[2] = "هتل";
	$grid->columnLists[2]=loadHotel();
    $grid->columnHeaders[3] =null;
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[5] = "نوع";
	$grid->columnLists[5]=loadType();
	$grid->columnHeaders[6] = null;
	$grid->addFeild('id');
	$grid->columnHeaders[7] = 'آیکون';
	$grid->columnFunctions[7] = 'loadIcon'; 
	$grid->addFeild('id');
	$grid->columnHeaders[8] = 'خدمات';
	$grid->columnFunctions[8] = 'loadKhadamat'; 
	
	$grid->addFunction = 'add_item';
	$grid->deleteFunction = 'delete_item';
	$grid->canDelete = FALSE;
	$grid->sortEnabled = TRUE;
        $grid->intial();
   	$grid->executeQuery();
        //$out = $grid->getGrid();
		$wer ='';
		if($_SESSION['daftar_id']!=49){
			$hs = daftar_class::hotelList($_SESSION['daftar_id']);
			$wer = "where `hotel_id` in (".implode(',',$hs).") ";
		}
    mysql_class::ex_sql("select `sandogh`.`id`,`sandogh`.`name`,`hotel`.`name` `hname`,`can_cash` from `sandogh` left join `hotel` on (`hotel_id`=`hotel`.`id`)$wer",$ss);
    $i=1;
    while($r=mysql_fetch_array($ss)){
        $id = $r['id'];
        $name = $r['name'];
        $hotel_id = $r['hotel_id'];
//         mysql_class::ex_sql("select * from `hotel` where `id`='$hotel_id'",$sss);
//             $rr=mysql_fetch_array($sss);
//             $hname = $rr['name'];
				$hname = $r['hname'];
        $can_cash = $r['can_cash'];
        $type = "";
        if($can_cash==1)
            $type="دریافت نقدی دارد";
        if($can_cash==-1)
            $type="دریافت نقدی ندارد";
        $icon = $r['icon'];
        
         $out.="<tr>
         <td>".$i."</td>
         <td>".$name."</td>
         <td>".$hname."</td>
         <td>".$type."</td>
         <td>".loadIcon($id)."</td>
         <td>".loadKhadamat($id)."</td>
         <td><a onclick=\"editGfunc('".$id."','".$name."','".$hotel_id."','".$can_cash."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a></td>
         </tr>"; 
        $i++;
    }
$out.="</tbody></table";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>تعریف موارد فرانت آفیس</title>
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
    function f1(img,input)
			{
				if(confirm('آیا تغییر آیکون انجام شود؟'))
				{
					var form = document.createElement("form");
					form.setAttribute("method", "POST");
					form.setAttribute("action", "sandogh.php");         
					form.setAttribute("target", "_self");
					var hiddenField1 = document.createElement("input");              
					hiddenField1.setAttribute("name", "id");
					hiddenField1.setAttribute("value", input);
					var hiddenField2 = document.createElement("input");              
					hiddenField2.setAttribute("name", "img");
					hiddenField2.setAttribute("value", img);
					var hiddenField3 = document.createElement("input");              
					hiddenField3.setAttribute("name", "mod");
					hiddenField3.setAttribute("value", "updateImg");
					form.appendChild(hiddenField1);
					form.appendChild(hiddenField2);
					form.appendChild(hiddenField3);
					document.body.appendChild(form);         
					form.submit();
					document.body.removeChild(form);
				}
			} 
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>تعریف موارد فرانت آفیس</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                          
                               
                            <a href="#newG"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن مورد جدید</button></a>
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
                    <h4 class="modal-title">افزودن مورد</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id1" />
                        <div class="col-md-4">
                            <label>نام: </label>
                            <input type="text" class="form-control" name="name1" />
                        </div>
                        <div class="col-md-4">
                            <label>هتل: </label>
                            <select name="hotelName1" id="hotelName1" class="form-control">
                            <?php
                                echo loadHotel();
                            ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>نوع: </label>
                            <select name="type1" id="type1" class="form-control">
                                <option value=""></option>
                                <option value="1">دریافت نقدی داشته باشد</option>
                                <option value="-1">دریافت نقدی نداشته باشد</option>
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
                            <label>نام: </label>
                            <input type="text" class="form-control" name="name2" />
                        </div>
                        <div class="col-md-4">
                            <label>هتل: </label>
                            <select name="hotelName2" id="hotelName2" class="form-control">
                            <?php
                                echo loadHotel();
                            ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>نوع: </label>
                            <select name="type2" id="type2" class="form-control">
                                <option value=""></option>
                                <option value="1">دریافت نقدی داشته باشد</option>
                                <option value="-1">دریافت نقدی نداشته باشد</option>
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
            var hotelName1 = $("#hotelName1 option:selected" ).val();
            var type1 = $("#type1 option:selected" ).val();
            var name1 = $("input[name='name1']").val();
            $.post("sandoghAjax.php",{hotelName1:hotelName1,type1:type1,name1:name1},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }  
           });
        }
        function editGfunc(id,name,hotel_id,can_cash){
            StartLoading();
            $("input[name='id2']").val(id);
            $("input[name='name2']").val(name);
            $("#hotelName2 option[value="+hotel_id+"]").attr('selected','selected');
            $("#type2 option[value="+can_cash+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var name2 = $("input[name='name2']").val();
            var hotelName2 = $("#hotelName2 option:selected" ).val();
            var type2 = $("#type2 option:selected" ).val();
            
           $.post("sandoghEditAjax.php",{id2:id2,name2:name2,hotelName2:hotelName2,type2:type2},function(data){
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
            var id3 = $("input[name='id3']").val();
           $.post("sandoghDeleteAjax.php",{id3:id3},function(data){
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