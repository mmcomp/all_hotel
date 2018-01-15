<?php
ini_set('display_errors','off');
$url_main =  "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
session_start();
$_SESSION['hpass']='hotel724';
include('lib/nusoap.php');
include("../kernel.php");
$conf->setMoshtari(1);
$_SESSION['user_id'] = $conf->online_user_id;
$_SESSION['daftar_id'] = $conf->online_daftar_id;
$user_id = $conf->online_user_id;
$daftar_id = $conf->online_daftar_id;
$hotel_id = isset($_REQUEST['hotel_id'])?(int)$_REQUEST['hotel_id']:$conf->online_hotel_id;
$ajans_id = $conf->online_ajans_id;
$nafar = 0;
$_SESSION['online'] = TRUE;
$is_transfer = FALSE;
$result_ready = FALSE;
// var_dump($conf);
// die();
function loadPic($room_ids){
	$out = '';
	foreach($room_ids as $room){
		$pics = room_pic_class::loadRoom($room);
		if(count($pics)>0 && $out ==''){
			$out = '../main/'.$pics[0]->pic;
		}
	}
	return $out;
}
function loadKhadamat(){
	global $hotel_id,$is_transfer;
	$kh = khadamat_class::loadKhadamats($hotel_id);
	if(count($kh)==0){
		return '';
	}
	$out = '<div class="row khadamat-header">';
	$out .= '<h3 class="section-title" style="text-align: center;margin: 30px;color: #000;">خدمات هتل</h3>';
	$out .= '</div>';
	$out .= '<div class="row khadamat-block"  style="text-align: center;color: #000;margin:0px auto 0px auto;">';
	$is_transfer = FALSE;
	$i=0;
	foreach($kh as $khadamat){
		$bg_color=($i%2==1)?'#f78536':'#01ad7c';
		$out .= '<div class="col-md-3" style="background:'.$bg_color.';color:#fff;">';
		$out .= $khadamat['name'];
		if(strpos($khadamat['name'],'&#1578;&#1585;&#1575;&#1606;&#1587;&#1601;&#1585;')!==FALSE || strpos($khadamat['name'],'transfer')!==FALSE){
			$is_transfer = TRUE;
		}
		$out .= '</div>';
		$i++;
	}
	$out .= '</div>';
	return $out;
}
function createResult($rooms,$adult,$child,$aztarikh,$paztarikh,$shab,$ghimat){
	global $is_transfer,$hotel_id;
	$hotel = new hotel_class($hotel_id);
	$hotel_name = $hotel->name;
	$out = '<form method="post" id="frm1"><div class="panel1" style="margin-top:30px;margin-bottom:30px;">';
	$out .= '<input type="hidden" name="hotel_id" value="'.$hotel_id.'" />';
	$out .= '<input type="hidden" name="adult_r" value="'.$adult.'" />';
	$out .= '<input type="hidden" name="child_r" value="'.$child.'" />';
	$out .= '<input type="hidden" name="aztarikh_r" id="aztarikh_r" value="'.$aztarikh.'" />';
	$out .= '<input type="hidden" name="shab_r" value="'.$shab.'" />';
	/////////////////////////////////////////////////////////////////
// 	$out.='<div id="fh5co-tours" class="fh5co-section-gray"><div class="container">';
// 	$out.='<div class="col-md-6 col-sm-12">';
// 	$count_room=1;
// 	$rk = 1;
// 	foreach($rooms as $i=>$room){
// 		if(count($room['room_ids'])>0){
// // 			echo "loading<br/>";
// 			$room_picture = loadPic($room['room_ids']);
// 			if($room_picture==''){
// 				$room_picture = 'images/place-1.jpg';
// 			}
// 			if($count_room % 2 == 1)
// 				$out.='<div class="row">';
// 				/////////////////////////////////////////////////////////////
// 			$rcount = (int)$room['count'];
// 			$displayed_rcount = min(3,$rcount);
// 			//cs-select cs-skin-border 
// 			$room_select= '<select id="room-select-'.$i.'" name="room-select[]" onchange="calcGhimatMain('.$i.')" class="room-selection" style="color:#000;" data-ghimat="'.((int)$room['ghimat']).'" data-ghimat_ezafe="'.((int)$room['ghimat_ezafe']).'" data-zarfiat="'.((int)$room['zarfiat']).' date-rcount="'.$displayed_rcount.'" >';
// 			$room_select .= '<option value="0">0</option>';
// 			$room_select .= '<option value="1">1</option>';
// 			for($j = 2;$j<=$displayed_rcount;$j++){
// 				$room_select .= '<option value="'.$j.'">'.$j.'</option>';
// 			}
// 			$room_select .= '</select>';
// 			//////////////////////////////////////////////////////////
// 			$out.='<div class="col-md-6 col-sm-12 fh5co-tours animate-box" data-animate-effect="fadeIn">';
// 			$out.='<div href="#">';
// 			$out.='<img src="'.$room_picture.'" alt="datis" class="img-responsive" >';
// 			$out.='<div class="desc">';
// 			$out.='<span></span>';
// 			$out.='<h3>'.$room['name'].'</h3>';
// 			$out.='<div><span class="price col-md-7">'.((int)$room['ghimat']).'</span>';
// 			$out.='<div class="col-md-5">'.$room_select.'</div></div>';
// 			if($room['ghimat_ezafe']>0 && (int)$room['zarfiat_ezafe']>0){
// 				$room_select_ezafe= '<select id="room-select-'.$i.'-ezafe" name="room-select-ezafe[]" onchange="calcGhimat()" class="room-selection-ezafe"  style="color:#000;" data-ghimat="'.((int)$room['ghimat']).'" data-ghimat_ezafe="'.((int)$room['ghimat_ezafe']).'" data-zarfiat="'.((int)$room['zarfiat']).'" data-zarfiat_ezafe="'.((int)$room['zarfiat_ezafe']).'" >';
// 				$room_select_ezafe .= '<option value="0">0</option>';
// 				$room_select_ezafe .= '</select>';

// 				$out.='<h3 >سرویس اضافه:</h3>';
// 				$out.='<span  class="price col-md-7">'.((int)$room['ghimat_ezafe']).'</span>';
// 				$out.='<span class="col-md-5">'.$room_select_ezafe.'</span>';
// 				//$out.='<a class="btn btn-primary btn-outline" href="#">Book Now <i class="icon-arrow-right22"></i></a>';
// 			}
// 			$out.='</div>';
// 			$out.='</div>';
// 			$out.="</div>";
// 			//////////////////////////////////////////////////////
// 			if($count_room % 2 == 0)
// 			{
// 				$out.='</div>';
// 			}
// 			$count_room++;
// 			$out .= '<input type="hidden" name="room-type[]" value="'.$room['room_typ_id'].'" />';
// 			$out .= '<input type="hidden" name="room-ids[]" value="'.implode(',',$room['room_ids']).'" />';
// 		}
// 	}
// 	if($count_room % 2 == 0)
// 	{
// 		$out.='</div>';
// 	}
// 	$out.="</div>";
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	$out.='<div id="fh5co-tours"><div class="container">';//class="fh5co-section-gray"
	//////////////////////////////////////////////////////////////////////////////////////////////////
	$max_size=($count_room/2)*200;
	$height=($count_room<=5)?"inherit":$max_size."px";
	$out.='<div class="col-md-6 col-sm-12 fh5co-section-gray" style="background:rgba(220, 216, 213, 0.23);height:'.$height.';text-align:right;direction:rtl;color:#fff;font-size:bold;">';
	$class="row col-md-12";
	$class_min="col-md-6 form-group";
		$out.='<div style="">';
		$out.='<h3 class="section-title" style="text-align: center;margin: 30px;color: #000;">'.$hotel_name.'</h3>';
			$out.='<div class="col-md-12 animate-box">';
				$out.='<div class="flight-book">';
					$out.='<div class="plane-name">';
						$out.='<span class="p-flight">بزرگسال: '.$adult.' نفر'.'</span></br>';
						$out.='<span class="p-flight">کودک: '.$child.' نفر'.'</span>';
					$out.='</div>';
					$out.='<div class="desc">';
						$out.='<div class="left" style="float:right;">';
							$out.=' <h4>'.$shab.' شب، از تاریخ : '.'</h4>';
							$out.='<span>'.jdate("Y/m/d",strtotime($aztarikh)).'</span>';
						$out.='</div>';
						$out.='<div class="right" style="float:left;">';
							$out.='<span class="price ghimat-class">';
								$out.=$ghimat.' ریال';
							$out.='</span>';
						$out.='</div>';
					$out.='</div>';
				$out.='</div>';
// 				$out.='<div class="row">';
// 					$out.='<div class="col-md-5 p_10">';
// 					$out.='از تاریخ: '.$paztarikh;
// 					$out.='</div>';
// 					$out.='<div class="col-md-5 p_10">';
// 					$out.='به مدت: '.$shab.' شب';
// 					$out.='</div>';
// 				$out.='</div>';
// 				$out.='<div class="row">';
// 					$out.='<div class="col-md-10 p_10">';
// 					$out.='جمع کل: '.$ghimat;
// 					$out.='</div>';
// 				$out.='</div>';
			$out.='</div>';
			$out.='<hr style="padding-top:70px;">';
			$out.=loadKhadamat();
			$out.='<hr>';
			$out.='<h3 class="section-title" style="text-align: center;margin: 30px;color: #000;">اطلاعات سرگروه</h3>';
			$out.='<div class="'.$class.'">';
				$out.='<div class="'.$class_min.'">';
					$out .= '<input name="lname" class="form-control" placeholder="نام خانوادگی" required />';
				$out.='</div>';
				$out.='<div class="'.$class_min.'">';
					$out .= '<input name="fname" class="form-control" placeholder="نام" />';
				$out.='</div>';
			$out.='</div>';
			$out.='<div class="'.$class.'">';
				$out.='<div class="'.$class_min.'">';
					$out .= '<input name="smelli" class="form-control" placeholder="شماره ملی"  required />';
				$out.='</div>';
				$out.='<div class="'.$class_min.'">';
					$out .= '<input name="tell" class="form-control" placeholder="شماره تماس" required />';
				$out.='</div>';
			$out.='</div>';
			$out.='<div class="'.$class.'">';
				$out.='<div class="form-group col-md-12">';
					$out .= '<input name="toz" class="form-control"  placeholder="توضیحات" />';
				$out.='</div>';
			$out.='</div>';
			$out.='<div class="'.$class.'">';
				$out.='<div class="form-group col-md-12">';
					$out .= '<textarea name="transfer" class="form-control"  placeholder="اطلاعات ترانسفر" required ></textarea>';
				$out.='</div>';
			$out.='</div>';
			$out.='<div class="'.$class.'">';
				$out.='<div class="form-group col-md-6">';
					$out .= '<input name="email" class="form-control"  placeholder="ایمیل" />';
				$out.='</div>';
				$out.='<div class="form-group col-md-6">';
					$out.='<label class="label_checkbox right"  style="float:right;color:#F78536;"> همکار هستم </label>';
					$out.='<div class="checkbox">';
					$out .= '<input type="checkbox" style="float:right;margin:0px 20px;" onclick="showPanelUser(this.checked);"   />';
					$out.='</div>';
				$out.='</div>';
			$out.='</div>';
			$out.='<div class="'.$class.'" id="PanelUser" style="display:none;">';
				$out.='<div class="form-group col-md-6">';
					$out .= '<input name="userName" class="form-control"  placeholder="نام کاربری" />';
				$out.='</div>';
				$out.='<div class="form-group col-md-6">';
					$out .= '<input type="password" name="password" class="form-control"  placeholder="رمز عبور" />';
				$out.='</div>';
			$out.='</div>';
			
	
			$out.='<div class="'.$class.'">';
				$out.='<div class="form-group col-md-6">';
					$out .= '<input type="button" onclick="nextPhase();" value="انتقال به درگاه بانک" class="btn btn-info" />';
				$out.='</div>';
			$out.='</div>';
		$out.="</div>";
	$out.="</div>";
	////////////////////////////////////////////////////////////////////////////////////////////////////////
	$out.='<div class="col-md-6 col-sm-12">';
	$out.='<div id="fh5co-car" class="fh5co-section-gray" style="margin-top:0px;padding-top: 10px;">';
// 	$out.='<div class="container">';
	$out.='<div class="row row-bottom-padded-md">';
	$count_room=1;
	$rk = 1;
	foreach($rooms as $i=>$room){
		if(count($room['room_ids'])>0){
			////////////////////////////////////////////////////////////
			$room_picture = loadPic($room['room_ids']);
			if($room_picture==''){
				$room_picture = 'images/car-2.jpg';
			}
			/////////////////////////////////////////////////////////////
			$rcount = (int)$room['count'];
			$displayed_rcount = min(3,$rcount);
			//cs-select cs-skin-border 
			$room_select= '<select id="room-select-'.$i.'" name="room-select[]" onchange="calcGhimatMain('.$i.')" class="room-selection" style="color:#000;" data-ghimat="'.((int)$room['ghimat']).'" data-ghimat_ezafe="'.((int)$room['ghimat_ezafe']).'" data-zarfiat="'.((int)$room['zarfiat']).' date-rcount="'.$displayed_rcount.'" >';
			$room_select .= '<option value="0">0</option>';
			$room_select .= '<option value="1">1</option>';
			for($j = 2;$j<=$displayed_rcount;$j++){
				$room_select .= '<option value="'.$j.'">'.$j.'</option>';
			}
			$room_select .= '</select>';
			//////////////////////////////////////////////////////////
			$out.='<div class="col-md-12 animate-box">';
			$out.='<div class="car">';
			$out.='<div class="one-4"  style="padding:10px 30px 10px 30px;">';
			$out.='<h4  style="margin-bottom:10px;text-align:center;">'.$room['name'].'</h4>';
			$out.='<span class="price">'.((int)$room['ghimat']).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.'<small>'.$room_select.'</small></span>';
			//////////////////////////////////////////////////////////////
			if($room['ghimat_ezafe']>0 && (int)$room['zarfiat_ezafe']>0){
				$room_select_ezafe= '<select id="room-select-'.$i.'-ezafe" name="room-select-ezafe[]" onchange="calcGhimat()" class="room-selection-ezafe"  style="color:#000;" data-ghimat="'.((int)$room['ghimat']).'" data-ghimat_ezafe="'.((int)$room['ghimat_ezafe']).'" data-zarfiat="'.((int)$room['zarfiat']).'" data-zarfiat_ezafe="'.((int)$room['zarfiat_ezafe']).'" >';
				$room_select_ezafe .= '<option value="0">0</option>';
				$room_select_ezafe .= '</select>';
			$out.='<h4  style="margin-bottom:10px;text-align:center;"> سرویس اضافه</h4>';
			$out.='<span class="price">'.((int)$room['ghimat_ezafe']).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.'<small>'.$room_select_ezafe.'</small></span>';
			}
			//////////////////////////////////////////////////////////////
			$out.='</div>';
			$out.='<div class="one-1" style="background-image: url('.$room_picture.');">';
			$out.='</div>';
			$out.='</div>';
			$out.='</div>';
			//////////////////////////////////////////////////////
			$count_room++;
			$out .= '<input type="hidden" name="room-type[]" value="'.$room['room_typ_id'].'" />';
			$out .= '<input type="hidden" name="room-ids[]" value="'.implode(',',$room['room_ids']).'" />';
		}
	}
	$out.="</div>";
	$out.="</div>";
// 	$out.="</div>";
	$out.="</div>";
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$out.="</div></div>";
	$out.="</div>";
	$out .= '</form>';
  return $out;
}
function getGhimat($shab,$rooms,$rooms_ezf,$aztarikh){
	global $hotel_id,$daftar_id,$ajans_id,$nafar;
	$out = 0;
	$aj = new ajax_responser('getghimat',$hotel_id,$aztarikh,$shab,implode(',',$rooms),$nafar,$daftar_id,$ajans_id,array(),implode(',',$rooms_ezf));
	$outmp = explode(',',$aj->getOutput);
	$out = $outmp[0];
  return $out;
}
class pardakht_class{
	private $my;
	public function __construct($pardakht_id=-1){
		$this->my = new mysql_class;
		$sql = "select * from `pardakht` where `id` = $pardakht_id";
		$this->my->ex_sql($sql,$q);
		if($r = mysql_fetch_array($q)){
			foreach($r as $key=>$value){
				$this->$key = $value;
			}
		}
	}
	public function settleSep(){
		$out = array(
			"error"=>'',
			"amount"=>-20
		);
		$client = new SoapClient('https://acquirer.samanepay.com/payments/referencepayment.asmx?WSDL'); //nusoap_client('https://acquirer.samanepay.com/payments/referencepayment.asmx?WSDL');
		$terminalId = '31035713';
// 		echo "pardakht: ".$this->id." verify ".$this->peygiri."|".$terminalId;
		$result = $client->verifyTransaction($this->peygiri,$terminalId);//$client->call('‫‪‫‪verifyTransaction‬‬‬‬', $parameters);//, $namespace);
		$out['amount'] = $result;
		return $out;
	}
	public function add($amount,$resdata){
		$resdata['fname'] = urlencode($resdata['fname']);
		$resdata['lname'] = urlencode($resdata['lname']);
		$resdata = json_encode($resdata);
		$sql = "insert into `pardakht` (`amount`,`resdata`,`tarikh_ersal`) values ($amount,'".$resdata."','".date("Y-m-d H:i:s")."')";
		$o = $this->my->ex_sqlx($sql);
		$pardakht_id = $this->my->insert_id;
		$sql = "select * from `pardakht` where `id` = $pardakht_id";
		$this->my->ex_sql($sql,$q);
		if($r = mysql_fetch_array($q)){
			foreach($r as $key=>$value){
				$this->$key = $value;
			}
		}
		return($pardakht_id);
	}
	public function bankBack($peygiri,$bank_data){
		$this->my->ex_sqlx("update `pardakht` set `peygiri`='$peygiri',`bank_data` = '".json_encode($bank_data,TRUE)."',`tarikh_daryaft` = '".date("Y-m-d H:i:s")."' where `id` = ".$this->id);
	}
	public function update($reserve_id){
		$sql = "update `pardakht` set `reserve_id` = '$reserve_id' where `id` = ".$this->id;
		$this->my->ex_sqlx($sql);
	}
	public function reserve(){
		$req = json_decode($this->resdata,TRUE);
		global $hotel_id,$ajans_id;
		$hotel_id = $req['hotel_id'];
		$adult = $req['adult_r'];
		$child = $req['child_r'];
		$aztarikh = $req['aztarikh_r'];
		$fname = urldecode($req['fname']);
		$lname = urldecode($req['lname']);
		$shab = (int)$req['shab_r'];
		$room_selected = $req['room-select'];
		$room_selected_ezafe = $req['room-select-ezafe'];
		$room_type = $req['room-type'];
		$room_id = $req['room-ids'];
		$tozihat = '&#1585;&#1586;&#1585;&#1608; &#1570;&#1606;&#1604;&#1575;&#1740;&#1606;'."\n";
		$tozihat .= '&#1578;&#1604;&#1601;&#1606; : '.$req['tell'];
		$tozihat .= '&#1578;&#1608;&#1590;&#1581;&#1740;&#1575;&#1578; : '.$req['toz'];
		$tozihat .= '&#1588;&#1605;&#1575;&#1585;&#1607; &#1605;&#1604;&#1740; : ' . $req['smelli'];
		if(isset($req['transfer'])){
			$tozihat .= '&#1578;&#1585;&#1575;&#1606;&#1587;&#1601;&#1585; : '.$req['transfer'];
		}
		$to_reserve_rooms_id = array();
		$to_reserve_rooms_ezafe = array();
		foreach($room_selected as $i=>$rselect){
			if((int)$rselect>0){
				$tmp_room = explode(',',$room_id[$i]);
				$rty = new room_typ_class($room_type[$i]);
				$ze = $rty->zarfiat_ezafe;
				$ezaf = (int)$room_selected_ezafe[$i];
				for($j = 0;$j < (int)$rselect;$j++){
					$to_reserve_rooms_id[] = $tmp_room[$j];
					if($ezaf>0){
						$tmp_ezaf = ($ezaf>$ze)?$ze:$ezaf;
						$to_reserve_rooms_ezafe[] = $tmp_room[$j].'|'.$tmp_ezaf;
						$ezaf -= $tmp_ezaf;
					}
				}
			}
		}
		$nafar = $adult+$child;
		$tedad = count($to_reserve_rooms_id);
		$sargrooh = $fname.' '.$lname;
		$ghimat = getGhimat($shab,$to_reserve_rooms_id,$to_reserve_rooms_ezafe,$aztarikh);
		$preRes = room_det_class::preReserveEzafe($hotel_id,$ajans_id,$to_reserve_rooms_id,$to_reserve_rooms_ezafe,$ghimat,
															 $aztarikh,$shab,$tedad,FALSE,FALSE,
															 $nafar,array(),$user_id);
		if($preRes!==FALSE){
			$tmp = room_det_class::sabtReserveHotel($preRes['reserve_id'],$preRes['shomare_sanad'],null,$fname,$lname,$tozihat,$ajans_id,$ghimat,null);
			$this->update($preRes['reserve_id']);
		}
		return $preRes;
	}
}
$scrollTo = '';
$msg ='';
$pardakht_id = '';
$ghimat = '';
//BANK BACK
$is_bank_back = FALSE;
if(isset($_REQUEST['StateCode'])){
  $scrollTo = <<<SC
  <script>
    $('html, body').animate({
        scrollTop: $("#reserve_section").offset().top
    }, 2000);
  </script>
SC;
	$StateCode = (int)$_REQUEST['StateCode'];
// 	var_dump($_REQUEST);
	if($StateCode!='0'){
  	$msg = 'در پرداخت شما خطایی رخ داده است;';
	}else{
		//Bank DONE
		$is_bank_back = TRUE;
		$pardakht_id = $_REQUEST['ResNum'];
		$pardakht = new pardakht_class($pardakht_id);
		$preRes = $pardakht->reserve();
		$pardakht->bankBack($_REQUEST['RefNum'],$_REQUEST);
		$pardakht->peygiri = $_REQUEST['RefNum'];
		if($preRes!==FALSE){
// 			echo "settle";
			$vercount = 1;
			$tmmp = $pardakht->settleSep();
			while($tmmp['amount']==-1 && $vercount<6){
				$tmmp = $pardakht->settleSep();
				$vercount++;
			}
// 			var_dump($tmmp);
			if($tmmp['amount']!=$pardakht->amount){
				room_det_class::refundReserve($preRes['reserve_id'],'pardakht_error:'.$pardakht_id);
				$preRes = FALSE;
			}
		}
		$scrollTo = <<<SC
		<script>
			$('html, body').animate({
					scrollTop: $("#reserve_section").offset().top
			}, 2000);
		</script>
SC;
	}/*else{		
		$msg = '&#1576;&#1575;&#1586;&#1711;&#1588;&#1578; &#1575;&#1586; &#1576;&#1575;&#1606;&#1705; &#1576;&#1575; &#1575;&#1591;&#1604;&#1575;&#1593;&#1575;&#1578; &#1582;&#1585;&#1740;&#1583; &#1584;&#1740;&#1604;';
		$msg .= '<pre>';
		$msg .= var_export($_REQUEST,TRUE);
		$msg .= '</pre>';
	}*/
}
//SEARCH
if(isset($_REQUEST['aztarikh'])){
  $scrollTo = <<<SC
  <script>
    $('html, body').animate({
        scrollTop: $("#reserve_section").offset().top
    }, 2000);
  </script>
SC;
  $paztarikh = $_REQUEST['aztarikh'];
	if($paztarikh==''){
		$paztarikh = jdate("Y/m/d");
	}
  $aztarikh = audit_class::hamed_pdateBack($paztarikh);
	if(strtotime($aztarikh)<strtotime(date("Y-m-d")))
	{
		$msg = 'زمان گذشته است';
	}else{
		$shab = (int)$_REQUEST['shab'];
		$hotel = new hotel_class($hotel_id);
		$tatarikh = $hotel->addDay($aztarikh,$shab);
		$adult = $_REQUEST['adult'];
		$child = $_REQUEST['child'];
		$nafar = $adult+$child;

		if ($hotel->hotelAvailableBetween($aztarikh,$tatarikh)){
// 			echo "$aztarikh,$shab,$hotel_id,$daftar_id";
			$rooms = room_class::loadOpenRooms($aztarikh,$shab,FALSE,FALSE,$hotel_id,$daftar_id);
			$result_ready = (count($rooms)>0);
			if($result_ready){
				if($hotel->is_shab_nafar!=1){
					$newrooms = array();
					foreach($rooms as $ri=>$room){
						if(count($room['room_ids'])>0){
							$rt = new room_typ_class($room['room_typ_id']);
							$gh = $rt->getGhimat($hotel_id,$aztarikh,$shab);
							if($gh!=NULL){
								$room['ghimat'] = $gh['ghimat'];
								$room['ghimat_ezafe'] = $gh['ghimat_ezafe'];
								$room['zarfiat_ezafe'] = $rt->zarfiat_ezafe;
								$newrooms[] = $room;
							}
						}
					}
					$rooms = $newrooms;
				}else{
					$daftar = new daftar_class($daftar_id);
					$yeknafarshab = 0;
					$tmp = $aztarikh;
					for($i = 0;$i < (int)$shab;$i++)
					{
						$yeknafarshab += $hotel->getGhimat($tmp);
						$tmp = date("Y-m-d 14:00:00",strtotime($tmp.' + 1 day'));
					}
					$newrooms = array();
					foreach($rooms as $ri=>$room){
						if(count($room['room_ids'])>0){
							$rt = new room_typ_class($room['room_typ_id']);
							$room['ghimat'] = $rt->zarfiat*$yeknafarshab;
							$room['ghimat_ezafe'] = $yeknafarshab;
							$room['zarfiat_ezafe'] = $rt->zarfiat_ezafe;
							$newrooms[] = $room;
						}
					}
					$rooms = $newrooms;
				}
				$ghimat = "0";
			}else{
				$msg = '&#1575;&#1578;&#1575;&#1602;&#1740; &#1576;&#1585;&#1575;&#1740; &#1585;&#1586;&#1585;&#1608; &#1662;&#1740;&#1583;&#1575; &#1606;&#1588;&#1583;';
			}
		}else{

			$msg =  "&#1583;&#1585;&#1576;&#1575;&#1586;&#1607; &#1578;&#1575;&#1585;&#1740;&#1582;&#1740; &#1605;&#1608;&#1585;&#1583; &#1606;&#1592;&#1585; ".$hotel->name." &#1601;&#1593;&#1575;&#1604; &#1606;&#1605;&#1740; &#1576;&#1575;&#1588;&#1583;";
		}
	}

}elseif(isset($_REQUEST['room-select'])){
//RESERVE
	$pardakht = new pardakht_class();
	$aztarikh = $_REQUEST['aztarikh_r'];
	$shab = (int)$_REQUEST['shab_r'];
	$room_selected = $_REQUEST['room-select'];
	$room_selected_ezafe = $_REQUEST['room-select-ezafe'];
	$room_type = $_REQUEST['room-type'];
	$room_id = $_REQUEST['room-ids'];
	$to_reserve_rooms_id = array();
	$to_reserve_rooms_ezafe = array();
	foreach($room_selected as $i=>$rselect){
		if((int)$rselect>0){
			$tmp_room = explode(',',$room_id[$i]);
			$rty = new room_typ_class($room_type[$i]);
			$ze = $rty->zarfiat_ezafe;
			$ezaf = (int)$room_selected_ezafe[$i];
			for($j = 0;$j < (int)$rselect;$j++){
				$to_reserve_rooms_id[] = $tmp_room[$j];
				if($ezaf>0){
					$tmp_ezaf = ($ezaf>$ze)?$ze:$ezaf;
					$to_reserve_rooms_ezafe[] = $tmp_room[$j].'|'.$tmp_ezaf;
					$ezaf -= $tmp_ezaf;
				}
			}
		}
	}
	$ghimat = getGhimat($shab,$to_reserve_rooms_id,$to_reserve_rooms_ezafe,$aztarikh);
	$pardakht_id = $pardakht->add($ghimat,$_REQUEST);
}
$lkhadamat = loadKhadamat();
$alert = '';
if(isset($preRes)){
	$clas = 'danger';
	if($preRes!==FALSE){
		$tex = '&#1585;&#1586;&#1585;&#1608; &#1576;&#1575; &#1605;&#1608;&#1601;&#1602;&#1740;&#1578; &#1575;&#1606;&#1580;&#1575;&#1605; &#1588;&#1583;';
		$tex .= '<br/>';
		$tex .= '&#1588;&#1605;&#1575;&#1585;&#1607; &#1585;&#1586;&#1585;&#1608; &#1588;&#1605;&#1575; : ';
		$tex .= $preRes['reserve_id'];
		$tex .= '<br/>';
		$tex.='<a href="voucher.php?q='.rand(1000000000,9999999999).$preRes['reserve_id'].rand(1000000,9999999).'"  target="_blank">دریافت واچر هتل</a>';
		$clas = 'success';
	}else{
		$tex = '&#1582;&#1591;&#1575; &#1583;&#1585; &#1579;&#1576;&#1578; &#1585;&#1586;&#1585;&#1608;';
	}
	$alert =<<<AL
		<div class="alert alert-$clas" style="text-align: center; margin-top: 50px;">
			$tex
		</div>

AL;
}
$shab_combo = '';
for($i = 1;$i < 31;$i++){
	$shab_combo .= "<option value=\"$i\"".((isset($shab) && $shab == $i)?'selected':'').">$i</option>";
}
$adult_combo = '';
for($i = 1;$i < 10;$i++){
	$adult_combo .= "<option value=\"$i\"".((isset($adult) && $adult == $i)?'selected':'').">$i</option>\n";
}
$child_combo = '';
for($i = 0;$i < 6;$i++){
	$child_combo .=  "<option value=\"$i\"".((isset($child) && $child == $i)?'selected':'').">$i</option>\n";
}
$hotels = '';
$my = new mysql_class();
$sql = "select name , id from hotel order by name";
$my->ex_sql($sql,$q);
while($r = mysql_fetch_array($q)){
	$hotels .= '<option value="'.$r['id'].'"'.(((int)$r['id']==$hotel_id)?'selected':'').'>'.$r['name'].'</option>';
}
$result = '';
if($msg!=''){
	$result .= '<div class="alert alert-danger" style="text-align:right">'.$msg.'</div>';
}
if($result_ready){
	$result .= createResult($rooms,$adult,$child,$aztarikh,$paztarikh,$shab,$ghimat,$is_transfer);
} 
// if($result_ready){
// 	$result .= $lkhadamat;
// }
$tem = file_get_contents('index.template.html');
$out = str_replace("#alert#",$alert,$tem);
$out = str_replace("#hotels#",$hotels,$out);
$out = str_replace("#shab_combo#",$shab_combo,$out);
/////////////////////////////////////////////////////
$out= str_replace("#aztarik#",jdate("Y/m/d",strtotime($aztarikh)),$out);
/////////////////////////////////////////////////////
$out = str_replace("#adult_combo#",$adult_combo,$out);
$out = str_replace("#child_combo#",$child_combo,$out);
$out = str_replace("#result#",$result,$out);
$out = str_replace("#scrollTo#",$scrollTo,$out);
$out = str_replace("#url_main#",$url_main,$out);
if($is_bank_back){
	$out = str_replace("#pardakht_id#",'',$out);
}else{
	$out = str_replace("#pardakht_id#",$pardakht_id,$out);
}
$out = str_replace("#ghimat#",$ghimat,$out);
// $out = str_replace("#ghimat#",'1000',$out);
echo $out;
?>
