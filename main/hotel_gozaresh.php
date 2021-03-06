<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);

	function loadGrp($inp)
	{
		$out = "";
		if($inp!="")
		{
		$out = hesab_class::idToName("grooh",$inp);
		}
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	
	function loadKol($inp)
        {
                $out = hesab_class::idToName("kol",$inp);
                return $out;
        }
        function loadMoeen($inp)
        {
                $out = hesab_class::idToName("moeen",$inp);
                return $out;
        }
        function loadTafzili($inp)
        {
                $out = hesab_class::idToName("tafzili",$inp);
                return $out;
        }
        function loadTafzili2($inp)
        {
                $out = hesab_class::idToName("tafzili2",$inp);
                return $out;
        }
        function loadTafzilishenavar($inp)
        {
                $out = hesab_class::idToName("tafzilishenavar",$inp);
                return $out;
        }
	function loadTafzilishenavar2($inp)
        {
                $out = hesab_class::idToName("tafzilishenavar2",$inp);
                return $out;
        }

	function loadBes($inp)
	{
		$out = (int)$inp;
             	$out =(( $out>0)?abs( $out):"---");
		return monize($out);
	}
	function loadBed($inp)
	{
                $out = (int)$inp;
             	$out =(( $out<0)?abs( $out):"---");
		return monize($out);
	}
	function echoer($id)
	{
		echo "id = '$id'<br/>\n";
		return($id);
	}
	function loadMande($inp)
	{
                $out = (int)$inp;
		if($out == 0)
			$out = "۰";
		if($out>0)
			$out = "بستانکار <br/>".enToPerNums(monize(abs($out)));
		else if($out<0)
			$out = "بدهکار <br/>".enToPerNums(monize(abs($out)));
                return($out);
	}
	function hamed_pdateBack($inp)
	{
		$out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
                        $y=(int)$tmp[2];
                        $m=(int)$tmp[1];
                        $d=(int)$tmp[0];
                        if ($d>$y)
                        {
                                $tmp=$y;
                                $y=$d;
                                $d=$tmp;
                        }
                        if ($y<1000)
                        {
                                $y=$y+1300;
                        }
                        $inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out." 12:00:00";
	}
	function loadReserve($inp)
	{
		$out="---";
		$color="blue";
		mysql_class::ex_sql("select `id` from `reserve` where `sanad_id`='$inp'",$q);
		if (mysql_num_rows($q)>0)
		{
			$out="<u><span style=\"color:$color;cursor:pointer;\" onclick=\"window.open('sanad_cia.php?sanad=$inp&','',800,300);\" >مشاهده </span></u>";
		}
		return $out;
	}
	function loadKhad($sel=0)
	{
		$out = '';
		$sel = (int)$sel;
		mysql_class::ex_sql("select `id`,`name` from `khadamat` where `en`=1 and `hotel_id`=".(int)$_REQUEST['h_id'],$q);
		while($r = mysql_fetch_array($q))
		{
			$select='';
			if((int)$r['id']==$sel)
				$select='selected="selected"';
			$out.="<option $select value='".$r['id']."' >".$r['name']."</option>\n";
		}
		return $out;
	}
	function loadKalaTarkibi()
	{
		$out ='<select class="inp" name="kala_cost" id="kala_cost" >';
		mysql_class::ex_sql("select `id`,`name` from `cost_kala` where `is_personal`=0 order by `name`",$q);
		while($r=mysql_fetch_array($q))
			$out .="<option value='".$r['id']."' >".$r['name']."</option>\n";
		$out .='</select>';
		return $out;
	}
	function loadAnbar()
	{
		$out = '<select class="inp" name="anbar_id" id="anbar_id" >';
		mysql_class::ex_sql('select `name`,`id` from `anbar` where `en`<>2 order by `name`',$q);
		while($r = mysql_fetch_array($q))
			$out.= "<option  value='".$r['id']."' >".$r['name']."</option>\n";
		$out .='</select>';
		return($out);
	}
	function loadUsers()
	{
		$out = '<select class="inp" name="gUser_id" id="gUser_id" >';
		mysql_class::ex_sql('select `lname`,`fname`,`id` from `user` where `user`<>\'mehrdad\' order by `lname`,`fname`',$q);
		while($r = mysql_fetch_array($q))
			$out.= "<option  value='".$r['id']."' >".$r['lname'].' '.$r['fname']."</option>\n";
		$out .='</select>';
		return($out);
	}
	function loadCost($inp)
	{
		$cost = new cost_kala_class($inp);
		return $cost->name;
	}
	$khadamat_id =((isset($_REQUEST['khadamat_id']))?(int) $_REQUEST['khadamat_id']:0)  ;
	$cost_tedad = ((isset($_REQUEST['cost_tedad']))?(int) $_REQUEST['cost_tedad']:0)  ;	
	$khadamat = new khadamat_class($khadamat_id);
	$is_transfer = (strpos($khadamat->name,'ترانسفر')!==FALSE);
	if( isset($_REQUEST['h_id']))
        {
		$h_id = $_REQUEST['h_id'];
		$h_id = (int) $_REQUEST['h_id'];
		$hotel = new hotel_class($h_id);
		$hotel_name =$hotel->name;
		$tarikh =((isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):date("Y-m-d"));
		$tarikh1 = date("Y-m-d",strtotime($tarikh));
		$tmp = explode(' ',$tarikh);
		$tarikh = $tmp[0];
                $frm="";
		$sum = 0;
		$out = '
        <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">نام سرگروه</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                            <th style="text-align:right;">نفرات</th>
                                            <th style="text-align:right;">شماره-رزرو</th>
                                            <th style="text-align:right;">اطلاعات بیشتر</th>
                                            <th style="text-align:right;">دفتر</th>
                                            <th style="text-align:right;">آژانس</th>
                                            <th style="text-align:right;">وضعیت</th>
                                            <th style="text-align:right;">تعداد</th>
                                            <th style="text-align:right;">تاریخ ورود</th>
                                            <th style="text-align:right;">تاریخ خروج</th>
                                            '.(($is_transfer)?'<th style="text-align:right;">جزئیات</th>':'').'
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
        ';
		$q=null;
		if(isset($khadamat_id) && isset($tarikh))
		{
			//echo "select * from `khadamat_det` where `reserve_id`>0 and DATE(`tarikh`) = '$tarikh' and `khadamat_id`=$khadamat_id <br/>";
			$query = "select * from `khadamat_det` where `reserve_id`>0 and DATE(`tarikh`) = '$tarikh' and `khadamat_id`=$khadamat_id ";
// 			echo $query;
			mysql_class::ex_sql($query,$q);
		}
		else
			mysql_class::ex_sql("select * from `khadamat_det` where `reserve_id`>0 and 1=0",$q);
		$i=0;
		$nafar_kol=0;
		$day = date("Y-m-d");
		while($r=mysql_fetch_array($q))
		{
			$room = room_det_class::loadDetByReserve_id((int)$r['reserve_id']);

			$rooms = '';
			for($j=0;$j<count($room['rooms']);$j++)
			{
//var_dump(reserve_class::isKhorooj($tmp[$i],$room['rooms'][$j]['room_id'])).'<br/>';
				$tmp_room = new room_class($room['rooms'][$j]['room_id']);
				$rooms.=$tmp_room->name.'['.$room['rooms'][$j]['nafar'].']'.(($j<count($room['rooms'])-1)?' , ':'');
			}	
			$i++;
			$status = '';
			if ($se->detailAuth('garanti'))
				$reserve_garanty = hotel_garanti_class::canViewReserve((int)$r['reserve_id']);
			else
				$reserve_garanty = FALSE;
			if (!($reserve_garanty))
			{
				$tmp_r = room_det_class::loadByReserve((int)$r['reserve_id']);
				$tmp_r = $tmp_r[0];
				if($tmp_r !== FALSE)
				{
					$room_aztarikh = date("Y-m-d",strtotime($tmp_r[0]->aztarikh));
					$room_tatarikh = date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh));
					$room_aztarikh1 = audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[0]->aztarikh)));
					$room_tatarikh1 =audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh)));
					$kh_tarikh = date("Y-m-d",strtotime($r['tarikh']));
					if($kh_tarikh==$room_aztarikh)
						$status = 'ورودی';
					else if ($kh_tarikh==$room_tatarikh)
						$status = 'خروجی';
					else
						$status = 'میانی';
					$nafar = 0;
					foreach($tmp_r as $tmp_rr)
						$nafar += $tmp_rr->nafar;
					$nafar_kol+= $nafar;
					$hotel_tmp = new hotel_reserve_class();
					$hotel_tmp->loadByReserve($r['reserve_id']);
					$ajans = new ajans_class($hotel_tmp->ajans_id);
					$daftar = new daftar_class($ajans->daftar_id);
					$row_style = 'class="odd"';
					if($status != '')
						$sum+=(int)$r['tedad'];
					else
					{
						$status = '<span style="color:red;">رزرو بررسی شود</span>';
						$r['tedad'] = 0;
					}
					if($i%2==0)
						$row_style = 'class="even"';
					$out.="<tr $row_style >";
					$out .="<td>$i</td><td>".$hotel_tmp->lname."</td><td>$rooms</td><td>$nafar</td><td>".$r['reserve_id']."</td><td>".$hotel_tmp->tozih."</td><td>".$daftar->name."</td><td>".$ajans->name."</td><td>$status</td><td>".$r['tedad']."</td><td>$room_aztarikh1</td><td>$room_tatarikh1</td>";
					if($is_transfer){
						$out .= '<td>'.$hotel_tmp->extra_toz.'</td>';
					}
					$out.='</tr>';
				}
			}
		}
		$out.='<tr class="odd" ><td></td><td></td><td>جمع نفرات : </td><td>'.$nafar_kol.'</td><td></td><td></td><td></td><td></td><td>جمع : </td><td>'.$sum.'</td><td></td><td></td>';
		if($is_transfer){
			$out .= '<td></td>';
		}
		$out .= '</tr>';
		$khad = new khadamat_class($khadamat_id);
		$hotel_kh = new hotel_class($khad->hotel_id);
		if( $conf->cost_control && ($khad->typ==0 && $sum>0 && ($se->detailAuth('all') || $se->detailAuth('anbar_dari'))) )
		{
			$disable = '';
			$pm = '';
			if($hotel_kh->ghaza_moeen_id<0)
			{
				$disable = 'disabled="disabled"';
				$pm = '<span style="color:red" >حساب معین هزینه غذا برای هتل ثبت نشده است</span>';
			}
			mysql_class::ex_sql("select sum(`tedad`) as `jam` from `cost_anbar` where `khadamat_id`='$khadamat_id' and date(`tarikh`)='$tarikh1'",$q);
			if($r = mysql_fetch_array($q))
				$jam_kol = (int)$r['jam'];
			$out .="<tr class='even' ><td style='text-align:left;'>خروج از انبار : </td><td>".loadAnbar()."</td><td>تحویل گیرنده:</td><td>".loadUsers()."</td><td align='left' >تعداد:</td><td><input name='cost_tedad' id='cost_tedad' class='inp' style='width:40px;' value='$cost_tedad'>از: ".($sum - $jam_kol)."</td><td>".loadKalaTarkibi()."</td><td><button class='btn btn-info' $disable onclick='send_info($khadamat_id,$sum);'><i class='fa fa-print' ></i>چاپ رسید خروج از انبار</button>$pm</td><td></td><td></td><td></td><td></td>";
			if($is_transfer){
				$out .= '<td></td>';
			}
			$out .= '</tr>';
			$out .="</tbody></table>";
			//--------------------------------
             mysql_class::ex_sql("select * from `cost_anbar` where date(`tarikh`)= '$tarikh1' and `khadamat_id`='$khadamat_id'",$ss);
			/*$grid = new jshowGrid_new("cost_anbar","grid1");
			$grid->whereClause=" date(`tarikh`)= '$tarikh1' and `khadamat_id`=$khadamat_id";
			$grid->columnHeaders[0] = null;
			$grid->columnHeaders[1] = "کالای ترکیبی";
		       	$grid->columnHeaders[2] =null ;
			$grid->columnHeaders[3] = null;
			$grid->columnFunctions[1]='loadCost';
			$grid->columnHeaders[4] = 'تعداد خارج شده';
			$grid->canAdd = FALSE;
			$grid->canEdit = FALSE;
			$grid->canDelete = FALSE;
			$grid->intial();
		   	$grid->executeQuery();
			$out .= $grid->getGrid();
*/		}
		else
			$out .="</tbody></table>";
        }
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart1 = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart1.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
$combo_hotel = "";
$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"GET\">";
$combo_hotel .= "<select class='form-control inp' id=\"hotel_id\" name=\"h_id\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel` where `id` in $shart1 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$h_id)
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
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش خدمات</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>گزارش خدمات</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">هتل:</label> 
                                    <div class="col-md-9"><?php echo $combo_hotel;?></div>
                                </div>
                                 <form id='frm1'  method='GET' >
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">خدمات:</label> 
                                    <div class="col-md-9">
                                        <select name="khadamat_id" id="khadamat_id" class="form-control inp">
                                            <?php echo loadKhad($khadamat_id); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">تاریخ:</label> 
                                    <div class="col-md-9"><input class="form-control inp" type="text" name="tarikh" id="datepicker1" value="<?php echo audit_class::hamed_pdate($tarikh); ?>"  >
                                    <input type="hidden" name="tarikh1" id="tarikh1" value="<?php echo audit_class::hamed_pdate($tarikh); ?>"  >
                                    </div>
                                </div>
                                <input type='hidden' name='h_id' id='h_id' value='<?php echo $h_id;?>' >
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <div class="col-md-12">
																			<a class="btn btn-info pull-left" href="javascript:window.print()"><i class="fa fa-print"></i></a>
																			<button class="btn btn-info col-md-8 pull-left" onclick="sbtFrm()">جستجو</button>
																		</div>
                                </div>
                            </div>
                          </form>
                            
                            
                            <div class="dataTable_wrapper" id="myTable" style="overflow-x:scroll">
                                
                                
                               <?php echo $out;  ?> 
                                      
                                      
  
                            </div>
                            <!-- /.table-responsive -->
                          
                        </div>
                       
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php
               

    $kala='';
if($ss){
$kala.='
<div class="box border orange">
									<div class="box-title">
										<h4><i class="fa fa-glass"></i>کالای خارج شده</h4>
									
									</div>
									<div class="box-body">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">کالای ترکیبی</th>
												<th style="text-align:right">تعداد خارج شده</th>
											  </tr>
											</thead>
											<tbody>
';
$i=1;
    while($r = mysql_fetch_array($ss))
		{
        $kid = $r['cost_kala_id'];
        mysql_class::ex_sql("select `name` from `cost_kala` where `id` = '$kid' ",$h_id);
    $h_id1 = mysql_fetch_array($h_id);
    $ckname = $h_id1['name'];
    
     $kala.='
     <tr>
     <td>'.$i.'</td>
     <td>'.$ckname.'</td>
     <td>'.$r[tedad].'</td>
     </tr>
     
     ';
       $i++; 
    }

$kala.='	</tbody>
										  </table>
									</div>
								</div>';

} ?>
                
                
											  
                <?php echo $kala; ?>
    <button onclick="getPrint();" class="btn btn-success col-md-2 pull-left"><i class="fa fa-print"></i> چاپ</button>
    
                
               
			</div>
		</div>
	</section>
	<!--/PAGE -->
    	<!-- Modal -->
    <!-- Modal : anbar modal -->
    <div class="modal fade" id="anbar-modal">
	
    </div>
			<!--/Modal : anbar modal-->
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
                    showOtherMonths: true,
                    selectOtherMonths: true
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
                                StopLoading();
                                alert(data);

                        });
                        
					}
				}
			}
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