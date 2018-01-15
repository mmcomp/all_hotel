<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
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
					mysql_class::ex_sql("select * from `hotel` order by name",$q);
					while($r=mysql_fetch_array($q,MYSQL_ASSOC))
					{
									$out[$r["name"]]=(int)$r["id"];
					}
					return $out;
	}
	function loadRoom()
        {
                $out = null;
                mysql_class::ex_sql("select * from room_typ order by zarfiat",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $out[$r["name"]]=(int)$r["id"];
                }
                return $out;
        }
	function loadKhadamat()
        {
                $out = null;
                mysql_class::ex_sql("select * from `khadamat` order by zarfiat",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $out[$r["name"]]=(int)$r["id"];
                }
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
		$query="insert into `khadamat` $fi values $valu";
		mysql_class::ex_sqlx($query);
	}
	function delete_item($id)
	{
		mysql_class::ex_sqlx("update `khadamat` set `en` = 0 where `id` = $id");
	}
        $combo = "";
	$combo .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
	$combo .= "هتل : <select class='form-control inp' id=\"hotel_id\" name=\"hotel_id\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
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
	function loadtyp()
	{
		$out['دارد'] = 0;
		$out['ندارد'] = 1;
		return $out;
	}
	function loadLogicalTyp()
	{
		$out['ندارد'] = 0;
		$out['دارد'] = 1;
		return($out);
	}
	function loadVade()
	{
		$out['هردووعده‌اجباری'] = 0;
		$out['روز خروج اجباری'] = 1;
		$out['روز ورود اجباری'] = 2;
		return($out);
	}
	function loadtyp_ghaza()
	{
		$out['خیر'] = 0;
		$out['بله'] = 1;
		return($out);
	}
	function loadtyp_mot()
	{
		$out['خیر'] = 0;
		$out['بله'] = 1;
		return($out);
	}
        $combo .="</select>";
	$combo .= "</form>";

$rsout1="";

$rsout1.="

 <table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">خدمات</th>
                                        <th style=\"text-align:right;\">قیمت پیش فرض</th>
                                        <th style=\"text-align:right;\">تعداد دارد</th>
                                        <th style=\"text-align:right;\">ورودی دارد</th>
                                        <th style=\"text-align:right;\">خروجی دارد</th>
                                        <th style=\"text-align:right;\">وعده اختیاری</th>
                                        <th style=\"text-align:right;\">خدمات به عنوان غذا است؟</th>
                                        <th style=\"text-align:right;\">خدمات متفرقه است؟</th>
                                        <th style=\"text-align:right;\">تعداد در روز</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>

";
   mysql_class::ex_sql("select * from `khadamat` where `hotel_id`='$hotel_id' and `en`='1' order by `name`",$ss);
$i=1;
		while($r = mysql_fetch_array($ss))
		{
            $id = $r['id'];
            $name = $r['name'];
            $ghimat_def = $r['ghimat_def'];
            $ghim="";
            if($ghimat_def==0)
                $ghim="ندارد";
            else
                $ghim=$ghimat_def;
            $typ = $r['typ'];
            $typss="";
            if($typ==0)
                $typss="دارد";
            if($typ==1)
                $typss="ندارد";
            $voroodi_darad = $r['voroodi_darad'];
            $vorood="";
            if($voroodi_darad==1)
                $vorood="دارد";
            if($voroodi_darad==0)
                $vorood="ندارد";
            $khorooji_darad = $r['khorooji_darad'];
            $khoroo="";
            if($khorooji_darad==1)
                $khoroo="دارد";
            if($khorooji_darad==0)
                $khoroo="ندارد";
            $aval_ekhtiari = $r['aval_ekhtiari'];
            $ekhtiari="";
            if($aval_ekhtiari==0)
                $ekhtiari="هر دو وعده اجباری";
            if($aval_ekhtiari==1)
                $ekhtiari="روز خروج اجباری";
            if($aval_ekhtiari==2)
                $ekhtiari="روز ورود اجباری";
            $ghazaAst = $r['ghazaAst'];
            $ghaza="";
            if($ghazaAst==1)
                $ghaza="بله";
            if($ghazaAst==0)
                $ghaza="خیر";
            $motefareghe = $r['motefareghe'];
            $mote="";
            if($motefareghe==1)
                $mote="بله";
            if($motefareghe==0)
                $mote="خیر";
            $tedadDarRuz = $r['tedadDarRuz'];
            
            
            if(fmod($i,2)!=0){
                $rsout1.="
                    <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$name</td>
                                        <td>$ghim</td>
                                        <td>$typss</td>
                                        <td>$vorood</td>
                                        <td>$khoroo</td>
                                        <td>$ekhtiari</td>
                                        <td>$ghaza</td>
                                        <td>$mote</td>
                                        <td>$tedadDarRuz</td>
                                        <td>
                                            <a onclick=\"editkhfunc('".$id."','".$name."','".$ghimat_def."','".$typ."','".$voroodi_darad."','".$khorooji_darad."','".$aval_ekhtiari."','".$ghazaAst."','".$motefareghe."','".$tedadDarRuz."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deletekhfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
                ";
                $i++;
            }
            else{
                $rsout1.="
                
                <tr class=\"even\">
                                        <td>$i</td>
                                        <td>$name</td>
                                        <td>$ghim</td>
                                        <td>$typss</td>
                                        <td>$vorood</td>
                                        <td>$khoroo</td>
                                        <td>$ekhtiari</td>
                                        <td>$ghaza</td>
                                        <td>$mote</td>
                                        <td>$tedadDarRuz</td>
                                        <td>
                                            <a onclick=\"editkhfunc('".$id."','".$name."','".$ghimat_def."','".$typ."','".$voroodi_darad."','".$khorooji_darad."','".$aval_ekhtiari."','".$ghazaAst."','".$motefareghe."','".$tedadDarRuz."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deletekhfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
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
	<title>خدمات هتل</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>خدمات هتل</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                
                               
                            <a href="#newKH"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن خدمات جدید</button></a>
                            <br/>
                            <?php 
				echo $combo;
				echo "<br/>";
				echo $rsout1;
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
<div class="modal fade" id="newKH">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن خدمات</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="<?php echo $hotel_id ?>" name="hid" />
                        <div class="col-md-6">
                            <label for="khad">خدمات: </label>
                            <input type="text" name="khad" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label for="cost">قیمت پیش فرض: </label>
                            <input type="text" name="cost" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label for="tedad">تعداد: </label>
                            <select class='form-control' id="tedad" name="tedad">
                                <option value=""></option>
                                <option value="0">دارد</option>
                                <option value="1">ندارد</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="voroo">ورودی: </label>
                            <select class='form-control' id="voroo" name="voroo">
                                <option value=""></option>
                                <option value="1">دارد</option>
                                <option value="0">ندارد</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="khoroo">خروجی: </label>
                            <select class='form-control' id="khoroo" name="khoroo">
                                <option value=""></option>
                                <option value="1">دارد</option>
                                <option value="0">ندارد</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ekht">وعده اختیاری: </label>
                            <select class='form-control' id="ekht" name="ekht">
                                <option value=""></option>
                                <option value="0">هر دو وعده اجباری</option>
                                <option value="1">روز خروج اجباری</option>
                                <option value="2">روز ورود اجباری</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ghaza">خدمات به عنوان غذا: </label>
                            <select class='form-control' id="ghaza" name="ghaza">
                                <option value=""></option>
                                <option value="1">بله</option>
                                <option value="0">حیر</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="mot">خدمات متفرقه: </label>
                            <select class='form-control' id="mot" name="mot">
                                <option value=""></option>
                                <option value="1">بله</option>
                                <option value="0">خیر</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tedadR">تعداد در روز: </label>
                            <input type="text" name="tedadR" value="" class="form-control" />
                        </div>
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalkh()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="editKH">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش خدمات</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="khid" />
                        <div class="col-md-6">
                            <label for="khad">خدمات: </label>
                            <input type="text" name="khad2" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label for="cost">قیمت پیش فرض: </label>
                            <input type="text" name="cost2" value="" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label for="tedad">تعداد: </label>
                            <select class='form-control' id="tedad2" name="tedad2">
                                <option value=""></option>
                                <option value="0">دارد</option>
                                <option value="1">ندارد</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="voroo">ورودی: </label>
                            <select class='form-control' id="voroo2" name="voroo2">
                                <option value=""></option>
                                <option value="1">دارد</option>
                                <option value="0">ندارد</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="khoroo">خروجی: </label>
                            <select class='form-control' id="khoroo2" name="khoroo2">
                                <option value=""></option>
                                <option value="1">دارد</option>
                                <option value="0">ندارد</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ekht">وعده اختیاری: </label>
                            <select class='form-control' id="ekht2" name="ekht2">
                                <option value=""></option>
                                <option value="0">هر دو وعده اجباری</option>
                                <option value="1">روز خروج اجباری</option>
                                <option value="2">روز ورود اجباری</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ghaza">خدمات به عنوان غذا: </label>
                            <select class='form-control' id="ghaza2" name="ghaza2">
                                <option value=""></option>
                                <option value="1">بله</option>
                                <option value="0">حیر</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="mot">خدمات متفرقه: </label>
                            <select class='form-control' id="mo2" name="mot2">
                                <option value=""></option>
                                <option value="1">بله</option>
                                <option value="0">خیر</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tedadR">تعداد در روز: </label>
                            <input type="text" name="tedadR2" value="" class="form-control" />
                        </div>
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalKH()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteKH">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف خدمات</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="khid3" value="" class="form-control" />
                        آیا از حذف خدمات مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalKH()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
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
        function insertFinalkh(){
            StartLoading();
            var hid = $("input[name='hid']").val();
            var khad = $("input[name='khad']").val();
            var cost = $("input[name='cost']").val();
            var tedadR = $("input[name='tedadR']").val();
            var tedad = $("#tedad option:selected" ).val();
            var voroo = $("#voroo option:selected" ).val();
            var khoroo = $("#khoroo option:selected" ).val();
            var ekht = $("#ekht option:selected" ).val();
            var ghaza = $("#ghaza option:selected" ).val();
            var mot = $("#mot option:selected" ).val();
            $.post("khAjax.php",{hid:hid,khad:khad,cost:cost,tedadR:tedadR,tedad:tedad,voroo:voroo,khoroo:khoroo,ekht:ekht,ghaza:ghaza,mot:mot},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در افزودن");
               if(data=="1"){
                   alert("افزودن با موفقیت انجام شد");
                   location.reload();
               }                         
           });
        }
        function editkhfunc(id,name,ghimat_def,typ,voroodi_darad,khorooji_darad,aval_ekhtiari,ghazaAst,motefareghe,tedadDarRuz){
            StartLoading();
            $("input[name='khid']").val(id);
            $("input[name='khad2']").val(name);
            $("input[name='cost2']").val(ghimat_def);
            $("input[name='tedadR2']").val(tedadDarRuz);
            $("#tedad2 option[value="+typ+"]").attr('selected','selected');
            $("#voroo2 option[value="+voroodi_darad+"]").attr('selected','selected');
            $("#khoroo2 option[value="+khorooji_darad+"]").attr('selected','selected');
            $("#ekht2 option[value="+aval_ekhtiari+"]").attr('selected','selected');
            $("#ghaza2 option[value="+ghazaAst+"]").attr('selected','selected');
            $("#mo2 option[value="+motefareghe+"]").attr('selected','selected');
            $('#editKH').modal('show');
            StopLoading();
        
        }
        function editFinalKH(){
            StartLoading();
            var khid = $("input[name='khid']").val();
            var khad2 = $("input[name='khad2']").val();
            var cost2 = $("input[name='cost2']").val();
            var tedadR2 = $("input[name='tedadR2']").val();
            var tedad2 = $("#tedad2 option:selected" ).val();
            var voroo2 = $("#voroo2 option:selected" ).val();
            var khoroo2 = $("#khoroo2 option:selected" ).val();
            var ekht2 = $("#ekht2 option:selected" ).val();
            var ghaza2 = $("#ghaza2 option:selected" ).val();
            var mo2 = $("#mo2 option:selected" ).val();
           $.post("khEditAjax.php",{khid:khid,khad2:khad2,cost2:cost2,tedadR2:tedadR2,tedad2:tedad2,voroo2:voroo2,khoroo2:khoroo2,ekht2:ekht2,ghaza2:ghaza2,mo2:mo2},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function deletekhfunc(id){
            StartLoading();
            $("input[name='khid3']").val(id);
            $('#deleteKH').modal('show');
            StopLoading();
        }
        function deleteFinalKH(){
            StartLoading();
            var khid3 = $("input[name='khid3']").val();
           $.post("khDeleteAjax.php",{khid3:khid3},function(data){
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