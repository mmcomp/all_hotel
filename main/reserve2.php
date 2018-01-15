<?php
	session_start();
	include("../kernel.php");
	if(!isset($_SESSION['user_id']))
					die(lang_fa_class::access_deny);
	$se = security_class::auth((int)$_SESSION['user_id']);
	if(!$se->can_view)
					die(lang_fa_class::access_deny);
	$user = new user_class((int)$_SESSION['user_id']);
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$out = '<select name="hotel_id" class="inp" style="width:auto;" >';
		mysql_class::ex_sql('select `id`,`name` from `hotel` order by `name` ',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadNumber($inp=-1)
	{
		$out = '';
		$inp = (int)$inp;
		for($i=1;$i<5;$i++)
		{
			$sel = (($i==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='$i' >$i</option>\n";
		}
		return $out;
	}
	function loadMoeenByAjans_id($ajans_id)
	{
		$ajans_id = (int) $ajans_id;
		$moeen  = new ajans_class($ajans_id);
		return $moeen->moeen_id;
	}
	$msg = '';
	$room_ids = array();
	$room_ezafe = array();
	$hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$shab = ((isset($_REQUEST['shab']))?(int)$_REQUEST['shab']:-1);
	$shab_reserve = ((isset($_REQUEST['shabreserve_gh']))?TRUE:FALSE);
	if($shab == 0)
		$shab_reserve = TRUE;
	$shab_reserve_gh = ((isset($_REQUEST['shabreserve_gh']))?(int)$_REQUEST['shabreserve_gh']:0);
	$rooz_reserve = ((isset($_REQUEST['roozreserve_gh']))?TRUE:FALSE);
	$rooz_reserve_gh = ((isset($_REQUEST['roozreserve_gh']))?(int)$_REQUEST['roozreserve_gh']:0);
	$room_typ_id = ((isset($_REQUEST['room_typ_id']))?(int)$_REQUEST['room_typ_id']:-1);
	$tedad_otagh = ((isset($_REQUEST['tedad_otagh']))?(int)$_REQUEST['tedad_otagh']:0);
	foreach($_REQUEST as $key=>$value)
	{
		$tmp = explode('_',$key);
		if($tmp[0]=='otagh')
			$room_ids[] = (int)$tmp[1];
		else if($tmp[0]=='ezafe'){
			$room_ezafe[] = array(
				"room_id" => $tmp[2],
				"tedad" => $value
			);
		}
	}
	if(count($room_ids)==0)
		$room_ids = explode(',',$_REQUEST['room_id_tmp']);
	$room_ezafe = explode(',',$_REQUEST['room_id_tmp_ezafe']);
	if($tedad_otagh==0)
		$tedad_otagh = count($room_ids);
	$tedad_nafarat = ((isset($_REQUEST['tedad_nafarat']))?(int)$_REQUEST['tedad_nafarat']:0);
	$ajans_id = ((isset($_REQUEST['ajans_id']))?(int)$_REQUEST['ajans_id']:-1);
	if($ajans_id<=0)
	{
		die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><center>اطلاعات درست وارد نشده است مجدد اقدام به رزرو کنید<br/><a href="reserve1.php?h_id=4&mode1=1&" >بازگشت</a></center></html>');
	}	
	$daftar_id = ((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
	$daftar_idBelit_1 = ((isset($_REQUEST['daftar_idBelit_1']))?(int)$_REQUEST['daftar_idBelit_1']:-1);
	$ajans_idBelit_1 = ((isset($_REQUEST['ajans_idBelit_1']))?(int)$_REQUEST['ajans_idBelit_1']:-1);
	$daftar_idBelit_2 = ((isset($_REQUEST['daftar_idBelit_2']))?(int)$_REQUEST['daftar_idBelit_2']:-1);
	$ajans_idBelit_2 = ((isset($_REQUEST['ajans_idBelit_2']))?(int)$_REQUEST['ajans_idBelit_2']:-1);
	$daftar_idBelit_3 = ((isset($_REQUEST['daftar_idBelit_3']))?(int)$_REQUEST['daftar_idBelit_3']:-1);
	$ajans_idBelit_3 = ((isset($_REQUEST['ajans_idBelit_3']))?(int)$_REQUEST['ajans_idBelit_3']:-1);
	$sargrooh = ((isset($_REQUEST['sargrooh']))?$_REQUEST['sargrooh']:'');
	$tour_mablagh = ((isset($_REQUEST['tour_mablagh']))?(int)umonize($_REQUEST['tour_mablagh']):-1);
	$belit_mablagh_1 = ((isset($_REQUEST['belit_mablagh_1']))?(int)umonize($_REQUEST['belit_mablagh_1']):0);
	$belit_mablagh_2 = ((isset($_REQUEST['belit_mablagh_2']))?(int)umonize($_REQUEST['belit_mablagh_2']):0);
	$belit_mablagh_3 = ((isset($_REQUEST['belit_mablagh_3']))?(int)umonize($_REQUEST['belit_mablagh_3']):0);
	$m_hotel = $tour_mablagh - $belit_mablagh_1- $belit_mablagh_2 - $belit_mablagh_3;
	$mob = ((isset($_REQUEST['toz']))?$_REQUEST['toz']:'');
	$extra_toz = ((isset($_REQUEST['extra_toz']))?$_REQUEST['extra_toz']:'');
	$checkbox= array();
	$textbox = array();
	$ghimat = array();
	$room_gh = array();
	foreach($_REQUEST as $key => $value)
	{
		$tmp = explode('_',$key);
		if($tmp[0]=='kh' && $tmp[1] =='ch')
			$checkbox[(int)$tmp[2]] = $value;
		else if ($tmp[0]=='kh' && $tmp[1] =='txt')
			$textbox[(int)$tmp[2]] =$value; 
		else if ($tmp[0]=='kh' && $tmp[1] =='gh')
			$ghimat[(int)$tmp[2]] = $value;
		else if ($tmp[0]=='kh' && $tmp[1] =='v')
			$voroodi[(int)$tmp[2]] = TRUE;
		else if ($tmp[0]=='kh' && $tmp[1] =='kh')
			$khorooji[(int)$tmp[2]] = TRUE;
		if($tmp[0]=='room' && $tmp[1] =='gh')
			$room_gh[(int)$tmp[2]] = $value;
	}
	$output = '';
	//echo "$hotel_id>0 && ($room_typ_id>0 || ".var_export($conf->room_select,TRUE)." ) && $tedad_otagh>0 && $tedad_nafarat>0";
	if($hotel_id>0 && ($room_typ_id>0 || $conf->room_select ) && $tedad_otagh>0 && $tedad_nafarat>0)
	{
		$hotel = new hotel_class($hotel_id);
		if(!$conf->room_select)
			$room_typ = new room_typ_class($room_typ_id);
		else
		{
			$otagh_no = room_class::loadTypDetails($room_ids);
		}
		$output='<form method="POST" id="frm123" >
        <div class="box border orange">
									
									<div class="box-body">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">هتل</th>
												<th style="text-align:right">نوع اتاق</th>
												<th style="text-align:right">تعداد اتاق</th>
                                                <th style="text-align:right">تعداد نفرات</th>
											  </tr>
											</thead>
											<tbody>';
		$output.="<tr><td>".$hotel->name."</td>";
		if(!$conf->room_select)
			$output.="<td>".$room_typ->name."</td>";
		else
			$output.="<td>$otagh_no</td>";
                $output.="<td> $tedad_otagh</td>";
 		$output.="<td> $tedad_nafarat</td>";
		$room_ghimat = 0;
		$hotel_ghimat = $tour_mablagh - $belit_mablagh_1 - $belit_mablagh_2 - $belit_mablagh_3;
		$output.='</tr></tbody></table></div></div>';
		$output.='
        <div class="box border orange">
									
									<div class="box-body">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">خدمات</th>
												<th style="text-align:right">تعداد-روزانه</th>
											  </tr>
											</thead>
											<tbody>';
	/*	if (isset($_REQUEST['ta_gasht']))
			$ta_gasht = $_REQUEST['ta_gasht'];
		else
			$ta_gasht ="";
		if (isset($_REQUEST['ta_axe']))
			$ta_axe = $_REQUEST['ta_axe'];
		else
			$ta_axe ="";*/
		$jam_ghi_khadamat = 0;
		foreach($ghimat as $id => $ghi)
		{
			if(isset($checkbox[$id]) || (isset($textbox[$id]) && (int)$textbox[$id] > 0))
			{
				$khedmat = new khadamat_class((int)$id);
				$output .= '<tr>';
				/*if ($khedmat->gashtAst)
					$output .= '<td>'.$khedmat->name.' ('.$ta_gasht.')</td>';
				elseif ($khedmat->axeAst)
					$output .= '<td>'.$khedmat->name.' ('.$ta_axe.')</td>';
				else*/
					$output .= '<td>'.$khedmat->name.'</td>';
				$is_voroodi = '';
				$is_khorooji = '';
				if(isset($voroodi[$id]))
					$is_voroodi = ' , اول-دارد ';
				if(isset($khorooji[$id]))
					$is_khorooji = ' , آخر-دارد';
				if(isset($checkbox[$id]))
				{
					$output .= "<td>دارد $is_voroodi $is_khorooji</td>";
					//$output .= '<td>'.monize($ghi).'</td>';
					$jam_ghi_khadamat +=$ghi;
				}
				else if(isset($textbox[$id]))
				{
					$output .= '<td>'.$textbox[$id]."$is_voroodi $is_khorooji </td>";
					//$output .= '<td>'.monize($ghi*$textbox[$id]).'</td>'; 
					$jam_ghi_khadamat += $ghi*$textbox[$id];
				}
				$output .= '</tr>';
			}
		}
		if($shab_reserve)
		{
			$output.= "<tr><td colspan=3'>شب-رزرو دارد:".monize($shab_reserve_gh)."</td></tr>";
		}
		if($rooz_reserve)
		{
			$output.= "<tr><td colspan=3'>روز-رزرو دارد:".monize($rooz_reserve_gh)."</td></tr>";
		}
		$output.="<tr><td colspan='1'>نام سرگروه:<input id='sargrooh' name='sargrooh' type='text' value='$sargrooh' class='form-control inp' ></td><td>تلفن:<input id='toz' name='toz' type='text' value='$mob' class='form-control inp'></td></tr>";
		$output .="<tr><td style='text-align:right;' colspan='2'>توضیحات اضافی:<input class='form-control inp' name='extra_toz' id='extra_toz'  value='$extra_toz' ></td></tr>";
		$output.='<tr><td>جمع کل:'.monize($tour_mablagh).'</td><td colspan="2"><button id="sabt_nahaee" class="btn btn-info col-md-4" onclick="sendfrm(this);">ثبت نهایی</button><input type="hidden" name="mod1" value="0" ></td></tr></tbody></table></div></div></form>';
	}
	$tmp1 = array();
	if(isset($_REQUEST['mod1']) && $_REQUEST['mod1']==5 )
	{
		//$hotel_ghimat+= $shab_reserve_gh + $rooz_reserve_gh;
		if(($belit_mablagh_1+$belit_mablagh_2+$belit_mablagh_3)==0)
			$hotel_ghimat= $tour_mablagh ;
		else
		{
			$hotel_ghimat = array();
			$hotel_ghimat['ghimat_tour'] = $tour_mablagh;
			$hotel_ghimat['ghimat_belit1'] = $belit_mablagh_1;
			if($belit_mablagh_1>0)
			{
				$hotel_ghimat['other_moeen_id1'] = loadMoeenByAjans_id($ajans_idBelit_1);
				$daftar_class_1 = new daftar_class($daftar_idBelit_1);
				$hotel_ghimat['other_kol_id1'] = $daftar_class_1->kol_id;
			}
			else
			{
				$hotel_ghimat['other_moeen_id1'] = -1;
				$hotel_ghimat['other_kol_id1'] = -1;
			}
			$hotel_ghimat['ghimat_belit2'] = $belit_mablagh_2;
			if( $belit_mablagh_2>0)
			{
				$hotel_ghimat['other_moeen_id2'] = loadMoeenByAjans_id($ajans_idBelit_2);
				$daftar_class_2 = new daftar_class($daftar_idBelit_2);
				$hotel_ghimat['other_kol_id2'] = $daftar_class_2->kol_id;
			}
			else
			{
				$hotel_ghimat['other_moeen_id2'] = -1;
				$hotel_ghimat['other_kol_id2'] = -1;
			}
			$hotel_ghimat['ghimat_belit3'] = $belit_mablagh_3;
			if( $belit_mablagh_3>0)
			{
				$hotel_ghimat['other_moeen_id3'] = loadMoeenByAjans_id($ajans_idBelit_3);
				$daftar_class_3 = new daftar_class($daftar_idBelit_3);
				$hotel_ghimat['other_kol_id3'] = $daftar_class_3->kol_id;
			}
			else
			{
				$hotel_ghimat['other_moeen_id3'] = -1;
				$hotel_ghimat['other_kol_id3'] = -1;
			}
		}
		$khadamat_arr=null;
		foreach($ghimat as $id => $ghi)
		{
			if(isset($checkbox[$id]) || isset($textbox[$id]))
			{
				$khedmat = new khadamat_class((int)$id);
				$tmp_voroodi = ((isset($voroodi[$id]))?TRUE:FALSE);
				$tmp_khorooji = ((isset($khorooji[$id]))?TRUE:FALSE);
				$khadamat_arr[] =array('id'=>$id,'tedad'=>((isset($textbox[$id]))?$textbox[$id]:1),'ghimat'=>$ghi ,'voroodi'=>$tmp_voroodi ,'khorooji'=>$tmp_khorooji ) ;
			}
		}
		$tmp_room =$room_typ_id;
		if(count($room_ids)>0)
			$tmp_room = $room_ids;
		$rooms_arr = room_class::loadOpenRooms($aztarikh,$shab,$shab_reserve,$rooz_reserve,$hotel_id,$_SESSION['daftar_id']);
		$tmp1=FALSE;
		foreach($rooms_arr as $rrr)
			for($i=0;$i<count($tmp_room);$i++)
				if(in_array($tmp_room[$i],$rrr['room_ids']))
					$tmp1=TRUE;
		//---------------------برای خالی نزدن سند حسابها چک می شود--------------------
		$check_reserve = sanadzan_class::checkHesab($hotel_ghimat);
		$is_reserve = $check_reserve['is_reserve'];
		$msg .=$check_reserve['msg'];

		//-------------------پایان چک کردن حسابها ------------------------------------
		if($is_reserve)
		{
			/*if (isset($_REQUEST['ta_gasht']))
				$ta_gasht = $_REQUEST['ta_gasht'];
			else
				$ta_gasht ="";
			if (isset($_REQUEST['ta_axe']))
				$ta_axe = $_REQUEST['ta_axe'];
			else
				$ta_axe ="";*/

			if($tmp1)
				//$tmp1 = room_det_class::preReserve($hotel_id,$ajans_id,$tmp_room,$hotel_ghimat,$aztarikh,$shab,$tedad_otagh,$shab_reserve,$rooz_reserve,$tedad_nafarat,$khadamat_arr,$ta_gasht,$ta_axe);
				$tmp1 = room_det_class::preReserveEzafe($hotel_id,$ajans_id,$tmp_room,$room_ezafe,$hotel_ghimat,$aztarikh,$shab,$tedad_otagh,$shab_reserve,$rooz_reserve,$tedad_nafarat,$khadamat_arr);
			else
				$msg = 'لحظاتی قبل اتاق مورد نظر شما توسط شخص دیگری رزرو شد';
			if($tmp1!==FALSE)
			{
				$extra_toz = ((isset($_REQUEST['extra_toz']))?$_REQUEST['extra_toz']:'');
				$today = date("Y-m-d H:i:s");
				$toz = $mob;
				if($extra_toz!='')
				{
					$toz = null;
					$toz['toz'] = $mob;
					$toz['extra_toz'] = $extra_toz;
				}
				room_det_class::sabtReserveHotel($tmp1['reserve_id'],$tmp1['shomare_sanad'],$hotel_ghimat,'',$sargrooh,$toz,$ajans_id,$m_hotel,$today);
				log_class::add("resreve2",$user->id,"ثبت رزرو شماره ".$tmp1['reserve_id']);
				sms_class::reserve_text_sms($tmp1['reserve_id'],$mob,$tour_mablagh,$hotel_id);
				for($l=0;$l<count($tmp1['shomare_sanad']);$l++)
				{
					$tozih_sabti = room_det_class::loadReserve($tmp1['reserve_id']);
					mysql_class::ex_sqlx("update `sanad` set `tozihat`='$tozih_sabti' where `id`=".$tmp1['shomare_sanad'][$l]);
				}
				$msg = 'شماره رزرو شما '.$tmp1['reserve_id'].'<br>';
				//$msg .= 'ثبت توسط '.$user->fname.' '.$user->lname.' انجام شد<script>document.getElementById("sabt_nahaee").style.display="none";window.print();</script>';
                
				$msg .= 'ثبت توسط '.$user->fname.' '.$user->lname.' انجام شد<script>document.getElementById("sabt_nahaee").style.display="none";window.location="reserve3.php?reserve_id='.$tmp1['reserve_id'].'&r="+Math.random()+"&";</script>';

			}
			else
				$msg = 'رزرو با مشکل مواجه شد دوباره تلاش فرمایید';
		}
		else 
			$msg.='<br/><span style="color:red;" >اطلاعات وارد شده جهت ثبت کافی نبوده است ،لذا ثبت انجام نشد ،مجدد اقدام کنید</span>';
	}
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>سامانه رزرواسیون</title>
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
          function sendfrm(Obj)
			{
				//Obj.disabled = true;
				var sargrooh = document.getElementById('sargrooh');
				var toz = document.getElementById('toz');
				if(sargrooh.value !='' && toz.value !=''  ){
                    $("input[name='mod1']").val("5");
					document.getElementById('frm123').submit();
                    }
				else{
                    alert('نام سرگروه و تلفن را وارد کنید');
                    //return;
                }
					
			}
     
    </script>
    <script type="text/javascript" src="../js/tavanir.js"></script>
	
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-bell"></i>سامانه رزرواسیون</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                           
                             <?php
					$ajans = new ajans_class($ajans_id);
					$daftar = new daftar_class($daftar_id);
				?>
				<h5>دفتر <?php echo $daftar->name; ?> آژانس <?php echo $ajans->name; ?> از تاریخ <?php echo jdate("d / m / Y",strtotime($aztarikh)); ?> به مدت <?php echo enToPerNums($shab); ?> شب</h5>
			<br/>
			<?php echo $output.' '.$msg; ?>
                          
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