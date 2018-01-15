<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = $se->detailAuth('all');	
	$msg = "";
	if(isset($_REQUEST['tarikh_sakht']))
	{
		$hotel_id = $_REQUEST['hotel_id'];
		$hotel = new hotel_class($hotel_id);
		$sql = array();
		foreach($_REQUEST as $key=>$value){
			if($key!='hotel_id' && isset($hotel->$key)){
				$sql[] = " `$key` = '$value' ";
			}
		}
// 		var_dump($sql);
// 		die();
		if(count($sql)>0){
			$sql = 'update `emkanat_hotel_extra` set '.implode(',',$sql).' where `hotel_id` = '.$hotel_id;
// 			echo $sql;
			mysql_class::ex_sqlx($sql);
			$msg = "<script language=\"javascript\">alert('ثبت با موفقیت انجام شد.');</script>";
		}else{
			$msg = "<script language=\"javascript\">alert('ثبت با موفقیت انجام نشد.');</script>";
		}
	}
	if(isset($_REQUEST['set_prop'])){
		$hotel_id = $_REQUEST['hotel_id'];
		$found = FALSE;
		$sql = "insert into `emkanat_hotel_det` (`hotel_id`,`emkanat_hotel_id`) values ";
		foreach($_REQUEST as $key=>$value){
			if(strpos($key,'prop_')===0){
				$tmp = explode('_',$key);
				$sql .= ($found?',':'')."($hotel_id,".$tmp[1].")";
				$found = TRUE;
			}
		}
		mysql_class::ex_sqlx("delete from `emkanat_hotel_det` where `hotel_id` = $hotel_id");
		mysql_class::ex_sqlx($sql);
		$msg = "<script language=\"javascript\">alert('ثبت با موفقیت انجام شد.');</script>";
	}
	function loadMoeen($inp)
	{
		$inp = (int)$inp;
		$aj = new hotel_class($inp);
		if($aj->moeen_id!=0)
		{
			$moeen = new moeen_class($aj->moeen_id);
			$nama = $moeen->name.'('.$moeen->code.')';
		}
		else
		{
			$nama = 'انتخاب';
		}
		$out = $nama;
		//$out = "<u><span onclick=\"window.location =('select_hesab_hotel.php?sel_id=$inp');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
		return $out;
	}
	function loadMoeenGhaza($inp)
	{
		$inp = (int)$inp;
		$aj = new hotel_class($inp);
		if($aj->ghaza_moeen_id!=0)
		{
			$moeen = new moeen_class($aj->ghaza_moeen_id);
			$nama = $moeen->name.'('.$moeen->code.')';
		}
		else
		{
			$nama = 'انتخاب';
		}
		
		//$out = "<u><span onclick=\"window.location =('select_hesab.php?sel_id=$inp&return_name=sel_id_ghaza&refPage=hotel.php');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
		$out = $nama;
		return $out;
	}
	function loadWork($inp)
	{
		$out = "<a target=\"_blank\" style=\"cursor:pointer;\" href=\"hotelwork.php?hotel_id=$inp\">ادامه</a>";
		return($out);
	}
	function loadRoomTypWork($inp)
	{
		$out = "<a target=\"_blank\" style=\"cursor:pointer;\" href=\"roomtypwork.php?hotel_id=$inp\">ادامه</a>";
		return($out);
	}
	function loadReserve($inp)
	{
		$out = "<a target=\"_blank\" href=\"reserve1.php?h_id=$inp&mode1=1\">رزرو</a><br/><a target=\"_blank\" href=\"gaant.php?hotel_id=$inp\">شیت</a>";
                return($out);
	}
	function loadRoom($inp)
	{
                $out = "<a href=\"rooms.php?hotel_id=$inp\" target=\"_blank\">اتـــاق</a><br/><a href=\"khadamat.php?hotel_id=$inp\"  target=\"_blank\" >خدمات</a>";
                return($out);
        }
	function loadMalek()
	{
		$out['دیگران'] = 0;
		$out['اختصاصی'] = 1;
		$out['شناور'] = 2;
		return $out;
	}
	function loadCancel($inp)
	{
		$out = "<a href=\"refund.php\" target=\"_blank\" >کنسلی</a>";
                return($out);
	}
	function loadEdit($inp)
	{
		$out = "<a href=\"showreserve.php?h_id=$inp\" target=\"_blank\" >اصلاحیه</a>";
                return($out);
	}
	function loadRep($inp)
	{
		$out = "<a href=\"hotel_gozaresh.php?h_id=$inp\" target=\"_blank\" >گزارش‌ خدمات</a>";
                return($out);
	}
	function loadPic($inp)
	{
//		$out = "<u><span onclick=\"window.open('upload_pic.php?h_id=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >ارسال</span></u>";
		$out = "<a target=\"_blank\" href=\"total_upload.php?h_id=$inp&\">ارسال</a>";
                return($out);
	}
	function loadAdRep($inp)
	{
		$out = "<u><span onclick=\"window.open('search_name.php?hotel_id=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >جستجو ‌پیشرفته</span></u>";
		
                return($out);
	}
//loadCost
        function loadCost($inp)
        {
                $out = "<a target='_blank' href='cost_khorooj.php?hotel_id=$inp'>غذا</a>";
                return($out);
        }
	function loadChange($inp)
	{
		$out = "<a href=\"change.php?h_id=$inp&\" target=\"_blank\" >جابجایی</a>";		
                return($out);
	}
	//-------------------functions End---------------------
	if(isset($_REQUEST['sel_id']))
	{
		$moeen_id = (int)$_REQUEST['moeen_id'];
		$sel_id = $_REQUEST['sel_id'];
		mysql_class::ex_sqlx("update `hotel` set `moeen_id`=$moeen_id where `id`=$sel_id");
		//echo "update `hotel` set `moeen_id`=$moeen_id where `id`=$sel_id";
	}
	if(isset($_REQUEST['sel_id_ghaza']))
	{
		$moeen_id = (int)$_REQUEST['moeen_id'];
		$sel_id = $_REQUEST['sel_id_ghaza'];
		mysql_class::ex_sqlx("update `hotel` set `ghaza_moeen_id`=$moeen_id where `id`=$sel_id");
		//echo "update `hotel` set `moeen_id`=$moeen_id where `id`=$sel_id";
	}
	$combo=array();
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
//var_dump($hotel_acc);
	$shart = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? ' and (' : ' or').' `id`='.$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	else
		$shart = ' and 1=0 ';
	function add_item()
	{
		$fields = null;
                foreach($_REQUEST as $key => $value)
		{
                        if(substr($key,0,4)=="new_")
			{
                                if($key != "new_id" && $key != "new_moeen_id" )
				{
                                        $fields[substr($key,4)] =perToEnNums($value);
                                }
                        }
		}
		$kol_id = kol_class::addById($fields['name']);
		$moeen_id = moeen_class::addById($kol_id,'درآمد رزرواسیون '.$fields['name']);
		$moeen_hazine_id = moeen_class::addById($kol_id,'هزینه غذای '.$fields['name']);
		$fields['moeen_id'] = $moeen_id;
		$fields['ghaza_moeen_id'] = $moeen_hazine_id;
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
        	$query.="insert into `hotel` $fi values $valu";
		mysql_class::ex_sqlx($query);
	}
	function infoLink($inp)
	{
// 		$out = '';
// 		$hotel = new hotel_class($inp);
// 		$out = ((isset($hotel->info['properties']))?$hotel->info['properties']:'');
		$out = "
        <a style=\"cursor:pointer;\" onclick=\"hotelInfo($inp)\" data-toggle=\"modal\">مشخصات</a>"; 
		$out .= "<br/><a style=\"cursor:pointer;\" onclick=\"hotelProperties($inp)\" data-toggle=\"modal\">ویژگی ها</a>";
		return($out);
	}

$out1="";
$out1.="
<table style=\"width:1500px;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">نام</th>
                                        <th style=\"text-align:right;width:100px;\">نوع مالکیت</th>
                                        <th style=\"text-align:right;\">اطلاعات هتل</th>
                                        <th style=\"text-align:right;width:200px\">حساب معین</th>
                                        <th style=\"text-align:right;\">نحوه محاسبه</th>
                                        <th style=\"text-align:right;\">زمان های فعالیت</th>
                                        <th style=\"text-align:right;\">قیمت اتاق</th>
                                        <th style=\"text-align:right;\">مدیریت اطلاعات اتاق</th>
                                        <th style=\"text-align:right;\">رزرو هتل</th>
                                        <th style=\"text-align:right;\">کنسلی</th>
                                        <th style=\"text-align:right;\">اصلاحیه</th>
                                        <th style=\"text-align:right;\">گزارش خدمات</th>
                                        <th style=\"text-align:right;\">گزارش پیشرفته</th>
                                        <th style=\"text-align:right;\">جابجایی</th>
                                        <th style=\"text-align:right;width:200px\">حساب معین هزینه غذا</th>
                                        
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>

";
mysql_class::ex_sql("select * from `hotel` where 1=1 $shart order by `name`",$ss);
$i=1;
while($r = mysql_fetch_array($ss))
        {
    $h_id = $r['id'];
    $hname = $r['name'];
    $htype = $r['is_our'];
    $other="";
    if($htype==0)
        $other="دیگران";
    if($htype==1)
        $other="اختصاصی";
    if($htype==2)
        $other="شناور";
    if(fmod($i,2)!=0){
			$hazf = ($_SESSION['daftar_id']!=49?'':"<a onclick=\"deletehotelfunc(".$h_id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>");
        $out1.="
            <tr class=\"odd\">
                <td>$i</td>
                <td>$hname</td>
                <td>$other</td>
                <td>".infoLink($h_id)."</td>
                <td>".loadMoeen($h_id)."</td>
								<td>".(($r['is_shab_nafar']=='1')?'شبی نفری':'اتاقی')."</td>
                <td>".loadWork($h_id)."</td>
                <td>".loadRoomTypWork($h_id)."</td>
                <td>".loadRoom($h_id)."</td>
                <td>".loadReserve($h_id)."</td>
                <td>".loadCancel($h_id)."</td>
                <td>".loadEdit($h_id)."</td>
                <td>".loadRep($h_id)."</td>
                <td>".loadPic($h_id)."</td>
                <td>".loadChange($h_id)."</td>
                <td>".loadMoeenGhaza($h_id)."</td>
                
                <td>
                                            <a onclick=\"editHotelfunc('".$h_id."','".$hname."','".$htype."',".$r['is_shab_nafar'].")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            $hazf
                                        </td>
            </tr>
        ";
        
        $i++;
    }
    else{
        $out1.="
        <tr class=\"even\">
                <td>$i</td>
                <td>$hname</td>
                <td>$other</td>
                <td>".infoLink($h_id)."</td>
                <td>".loadMoeen($h_id)."</td>
								<td>".(($r['is_shab_nafar']=='1')?'شبی نفری':'اتاقی')."</td>
                <td>".loadWork($h_id)."</td>
                <td>".loadRoomTypWork($h_id)."</td>
                <td>".loadRoom($h_id)."</td>
                <td>".loadReserve($h_id)."</td>
                <td>".loadCancel($h_id)."</td>
                <td>".loadEdit($h_id)."</td>
                <td>".loadRep($h_id)."</td>
                <td>".loadPic($h_id)."</td>
                <td>".loadChange($h_id)."</td>
                <td>".loadCost($h_id)."</td>
                <td>
                                            <a onclick=\"editHotelfunc('".$h_id."','".$hname."','".$htype."',".$r['is_shab_nafar'].")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            $hazf
                                        </td>
            </tr>
        ";
        
        $i++;
    }
    
    
}
$out1.="</tbody></table>";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>مدیریت هتل</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-home"></i>مدیریت هتل</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                        <?php if($_SESSION['daftar_id']==49){ ?> 
                            <a href="#newHotel"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-home"></i> تعریف هتل</button></a>
                         <?php   }if($_SESSION['daftar_id']==49 ) { ?>
                            <a href="room.php" target="_blank"><button class="btn btn-info btn-lg"><i class="fa fa-cutlery"></i> تعریف اتاق</button></a><?php } ?>
                            <br/>
                            
                            <div style="overflow-x:scroll">
                            <?php echo $out1 ?>
                             </div>
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
<div class="modal fade" id="newHotel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن هتل</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-6">
                            <label for="name">نام هتل:  </label>
                            <input type="text" name="Hname" value="" class="form-control" required />
                        </div>
                        
                        <div class="col-md-6">
                            <label for="name">نوع مالکیت: </label>
                            <select class='form-control' id="HStype">
                                <option value=""></option>
                                <option value="0">دیگران</option>
                                <option value="1">اختصاصی</option>
                                <option value="2">شناور</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="name">نوع محاسبه: </label>
                            <select class='form-control' id="HSshabnafar">
                                <option value="1">شبی نفری</option>
                                <option value="2">اتاق</option>
                            </select>
                        </div>
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertHotel();" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
             </form>	
        </div>
    </div>
</div>
    <div class="modal fade" id="editHotel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش هتل</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="hotelid2" value="" class="form-control" />
                        <div class="col-md-6">
                            <label for="name">نام هتل:  </label>
                            <input type="text" name="hotelname2" value="" class="form-control" />
                        </div>  
                        <div class="col-md-6">
                            <label for="name">نوع مالکیت: </label>
                            <select class='form-control' id="hoteltype2" name="type">
                                <option value=""></option>
                                <option value="0">دیگران</option>
                                <option value="1">اختصاصی</option>
                                <option value="2">شناور</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="name">نوع محاسبه: </label>
                            <select class='form-control' id="HSshabnafar2" name="is_shab_nafar">
                                <option value="1">شبی نفری</option>
                                <option value="2">اتاق</option>
                            </select>
                        </div>
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalhotel()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="deleteHotel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف هتل</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="hotelid3" value="" class="form-control" />
                        آیا از حذف هتل مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalhotel()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="hotelInfo">
       
    </div>
    <div class="modal fade" id="hotelProperties">
       
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
        function hotelInfo(h_id){
            StartLoading();
            var hotel_id=h_id;
            $.post("info.php",{hotel_id:hotel_id},function(data){
                StopLoading();
                $("#hotelInfo").html(data);
                $('#hotelInfo').modal('show');             

            });
        }
        function hotelProperties(h_id){
            StartLoading();
            var hotel_id=h_id;
            $.post("properties.php",{hotel_id:hotel_id},function(data){
                StopLoading();
                $("#hotelProperties").html(data);
                $('#hotelProperties').modal('show');             

            });
        }
        function insertHotel(){
            StartLoading();
            var Hname = $("input[name='Hname']").val();
            var Htype = $( "#HStype option:selected" ).val();
						var Hshabnafare = $( "#HSshabnafar option:selected" ).val();
            $.post("hotelAjax.php",{Hname:Hname,Htype:Htype,Hshabnafare:Hshabnafare},function(data){
                StopLoading();
               if(data=="0")
                   alert("خطا در افزودن");
                else if(data=="1"){
                    alert("افزودن با موفقیت انجام شد");
                    location.reload();
                }    
                else alert(data)

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
function editHotelfunc(h_id,hname,htype,shabnafar){
    StartLoading();
    $("input[name='hotelid2']").val(h_id);
    $("input[name='hotelname2']").val(hname);
    $("#hoteltype2 option[value="+htype+"]").attr('selected','selected');
    $("#HSshabnafar2 option[value="+shabnafar+"]").attr('selected','selected');
    $('#editHotel').modal('show');
    StopLoading();
}
        function editFinalhotel(){
            StartLoading();
            var hotelid2 = $("input[name='hotelid2']").val();
            var hotelname2 = $("input[name='hotelname2']").val();
            var hoteltype2 = $("#hoteltype2 option:selected" ).val();
						var is_shab_nafar = $("#HSshabnafar2 option:selected" ).val();
            $.post("hotelEditAjax.php",{hotelid2:hotelid2,hotelname2:hotelname2,hoteltype2:hoteltype2,is_shab_nafar:is_shab_nafar},function(data){
// 							console.log(data);
                StopLoading();
							
                if(data=="0")
                    alert("خطا در ویرایش");
                if(data=="1"){
                    alert("ویرایش با موفقیت انجام شد");
                    location.reload();
               }
							 
           });
        }
        function deletehotelfunc(h_id){
            StartLoading();
            $("input[name='hotelid3']").val(h_id);
            $('#deleteHotel').modal('show');
            StopLoading();
        }
        function deleteFinalhotel(){
            StartLoading();
            var hotelid = $("input[name='hotelid3']").val();
           $.post("hotelDeleteAjax.php",{hotelid:hotelid},function(data){
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
	
	<?php echo $msg; ?>
</body> 
</html>