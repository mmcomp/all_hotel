<?php
$url_main =  "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
session_start();
include("../kernel.php");
$conf->setMoshtari(1);
$_SESSION['user_id'] = $conf->online_user_id;
$_SESSION['daftar_id'] = $conf->online_daftar_id;
$user_id = $conf->online_user_id;
$daftar_id = $conf->online_daftar_id;
$hotel_id = 113;//$conf->online_hotel_id;
$ajans_id = $conf->online_ajans_id;
$_SESSION['online'] = TRUE;
$is_transfer = FALSE;
$result_ready = FALSE;
function loadKhadamat(){
	global $hotel_id,$is_transfer;
	$kh = khadamat_class::loadKhadamats($hotel_id);
	if(count($kh)==0){
		return '';
	}
	$out = '<div class="khadamat-header">';
	$out .= 'خدمات هتل';
	$out .= '</div>';
	$out .= '<div class="row khadamat-block">';
	$is_transfer = FALSE;
	foreach($kh as $khadamat){
		$out .= '<div class="col-sm-3">';
		$out .= $khadamat['name'];
		if(strpos($khadamat['name'],'ترانسفر')!==FALSE || strpos($khadamat['name'],'transfer')!==FALSE){
			$is_transfer = TRUE;
		}
		$out .= '</div>';
	}
	$out .= '</div>';
	return $out;
}
function createResult($rooms,$adult,$child,$aztarikh,$paztarikh,$shab,$ghimat){
	global $is_transfer;
	$out = '<div class="row">';
	$out .= '<div style="background: #FF5722;width: 100%;top: 1em;z-index: 98;position: relative;display: inline-block;margin-top: 0;margin-bottom: 0;-webkit-box-shadow: 1px 2px 2px 0px rgba(0, 0, 0, 0.2);-moz-box-shadow: 1px 2px 2px 0px rgba(0, 0, 0, 0.2);-ms-box-shadow: 1px 2px 2px 0px rgba(0, 0, 0, 0.2);-o-box-shadow: 1px 2px 2px 0px rgba(0, 0, 0, 0.2);box-shadow: 1px 2px 2px 0px rgba(0, 0, 0, 0.2);">';
  $out .= '<form method="post" id="frm1"><div class="panel1">';
  $out .= '<input type="hidden" name="adult_r" value="'.$adult.'" />';
  $out .= '<input type="hidden" name="child_r" value="'.$child.'" />';
  $out .= '<input type="hidden" name="aztarikh_r" value="'.$aztarikh.'" />';
  $out .= '<input type="hidden" name="shab_r" value="'.$shab.'" />';
  $out .= '<div class="result-header">';
// 	$out .= '<table width="100%">';
// 	$out .= '<tr>';
// 	$out .= '<td>';
//   $out .= 'بزرگسال : <span class="adult-class">'.$adult.'</span>';
// 	$out .= '</td>';
// 	$out .= '<td>';	
// 	$out .= 'کودک : <span class="child-class">'.$child.'</span>';
// 	$out .= '</td>';
// 	$out .= '<td>';	
// 	$out .= 'از '.$paztarikh;
// 	$out .= '</td>';
// 	$out .= '<td>';	
// 	$out .= ' به مدت '.$shab.' شب';
// 	$out .= '</td>';
//   $out .= '</tr>';
// 	$out .= '<tr>';
// 	$out .= '<td colspan="2" style="text-align: left;width: 50%;">';
  $out .= 'جمع کل : ';
// 	$out .= '</td>';
// 	$out .= '<td colspan="2" style="text-align : right">';
	$out .= '<span class="ghimat-class">'.$ghimat.'</span>';
	$out .= ' ریال';
// 	$out .= '</td>';
//   $out .= '</tr>';
// 	$out .= '</table>';
  $out .= '</div>';
  $out .= '<div class="row">';
	$rk = 1;
  foreach($rooms as $i=>$room){
		if(count($room['room_ids'])>0){
			$out .= '<div class="room-block col-sm-3 col-md-3">';
			$out .= '<img src="hotel-bed-icon.png" />';
			$rcount = (int)$room['count'];
			$displayed_rcount = min(3,$rcount);
			$out .= '<select id="room-select-'.$i.'" name="room-select[]" onchange="calcGhimatMain('.$i.')" class="room-selection" data-ghimat="'.((int)$room['ghimat']).'" data-ghimat_ezafe="'.((int)$room['ghimat_ezafe']).'" data-zarfiat="'.((int)$room['zarfiat']).' date-rcount="'.$displayed_rcount.'" >';
			$out .= '<option value="0">0</option>';
			$out .= '<option value="1">1</option>';
			for($j = 2;$j<=$displayed_rcount;$j++){
				$out .= '<option value="'.$j.'">'.$j.'</option>';
			}
			$out .= '</select>';
			$out .= '<br/>';
			$out .= $room['name'];
			$out .= '<br/>';
			$out .= '['.((int)$room['ghimat']).' ریال'.']';
			if($room['ghimat_ezafe']>0 && (int)$room['zarfiat_ezafe']>0){
				$out .= '<br/>سرویس اضافه:<br/>';
				$out .= '['.((int)$room['ghimat_ezafe']).' ریال'.']';
				$out .= '<select id="room-select-'.$i.'-ezafe" name="room-select-ezafe[]" onchange="calcGhimat()" class="room-selection-ezafe" data-ghimat="'.((int)$room['ghimat']*$shab).'" data-ghimat_ezafe="'.((int)$room['ghimat_ezafe']*$shab).'" data-zarfiat="'.((int)$room['zarfiat']).'" data-zarfiat_ezafe="'.((int)$room['zarfiat_ezafe']).'" >';
				$out .= '<option value="0">0</option>';
				$out .= '</select>';
			}
			$out .= '<input type="hidden" name="room-type[]" value="'.$room['room_typ_id'].'" />';
			$out .= '<input type="hidden" name="room-ids[]" value="'.implode(',',$room['room_ids']).'" />';
			$out .= '</div>';
		}
  }
  $out .= '</div>';
  $out .='</div>';
  $out .='<div class="panel1">';
	$out .= '<div style="text-align:center;background-color: #000;">';
  $out .= 'اطلاعات سرگروه';
  $out .= '</div>';
	$out .= '<table>';
	$out .= '<tr>';
	$out .= '<td>';
  $out .= 'نام';
	$out .= '</td>';
	$out .= '<td>';
	$out .= '<input name="fname" class="form-control1" placeholder="نام" />';
	$out .= '</td>';
	$out .= '<td>';
  $out .= 'نام خانوادگی';
	$out .= '</td>';
	$out .= '<td>';
	$out .= '<input name="lname" class="form-control1" placeholder="نام خانوادگی" required />';
	$out .= '</td>';
	$out .= '</tr>';
	$out .= '<tr>';
	$out .= '<td>';
  $out .= 'شماره تماس';
	$out .= '</td>';
	$out .= '<td>';
	$out .= '<input name="tell" class="form-control1" placeholder="شماره تماس" required />';
	$out .= '</td>';
	$out .= '<td>';
  $out .= 'شماره ملی';
	$out .= '</td>';
	$out .= '<td>';
	$out .= '<input name="smelli" class="form-control1" placeholder="شماره ملی"  required />';
	$out .= '</td>';
	$out .= '</td>';
	$out .= '</tr>';
	$out .= '<td>';	
  $out .= 'توضیحات';
	$out .= '</td>';
	$out .= '<td colspan="3">';
	$out .= '<input name="toz" class="form-control1" style="width:100% !important;" placeholder="توضیحات" />';
	$out .= '</td>';
	$out .= '</tr>';
	if($is_transfer){
		$out .= '<tr>';
		$out .= '<td colspan="2">';
		$out .= 'وسیله سفر(اطلاعات کامل مثل شماره پرواز و..) ، مبدا ، ساعت رسیدن ، محل رسیدن ';
		$out .= '</td>';
		$out .= '<td colspan="2">';
		$out .= '<textarea name="transfer" class="form-control1" style="width: 100%;padding: 5px;" placeholder="اطلاعات ترانسفر" required ></textarea>';
		$out .= '</td>';
		$out .= '</tr>';
	}
	$out .= '</table>';
	$out .= '<div style="text-align:left">';
  $out .= '<input type="button" onclick="nextPhase();" value="انتقال به درگاه بانک" />';
	$out .= '</div>';
	$out .= '</form>';
  $out .= '</div>';
  $out .= '</div>';
  return $out;
}
function getGhimat($shab,$rooms){
	$out = 0;
	foreach($rooms as $room){
		$rs = new room_class($room);
		$out += $rs->ghimat;
	}
  $out *= $shab;
  return $out;
}
$scrollTo = '';
$msg ='';
//BANK BACK
if(isset($_REQUEST['StateCode'])){
  $scrollTo = <<<SC
  <script>
	$(document).ready(function(){
		setTimeout(function(){
			$('html, body').animate({
					scrollTop: $("#availability").offset().top
			}, 2000)
		},1000);
	});
  </script>
SC;
	$StateCode = (int)$_REQUEST['StateCode'];
	if($StateCode==-1){
  	$msg = 'پرداخت توسط شما کنسل شد';
	}
}
//SEARCH
$paztarikh = '';
if(isset($_REQUEST['aztarikh'])){
  $scrollTo = <<<SC
  <script>
	$(document).ready(function(){
		setTimeout(function(){
			$('html, body').animate({
					scrollTop: $("#availability").offset().top
			}, 2000)
		},1000);
	});
  </script>
SC;
  $paztarikh = $_REQUEST['aztarikh'];
  $aztarikh = audit_class::hamed_pdateBack($paztarikh);
  $shab = (int)$_REQUEST['shab'];
  $hotel = new hotel_class($hotel_id);
  $tatarikh = $hotel->addDay($aztarikh,$shab);
  $adult = $_REQUEST['adult'];
  $child = $_REQUEST['child'];
  $nafar = $adult+$child;

  if ($hotel->hotelAvailableBetween($aztarikh,$tatarikh)){
    $rooms = room_class::loadOpenRooms($aztarikh,$shab,FALSE,FALSE,$hotel_id,$daftar_id);
    $result_ready = (count($rooms)>0);
    if($result_ready){
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
      $ghimat = "0";
    }else{
			$msg = 'اتاقی برای رزرو پیدا نشد';
		}
  }else{
		
    $msg =  "دربازه تاریخی مورد نظر ".$hotel->name." فعال نمی باشد";
  }
}elseif(isset($_REQUEST['room-select'])){
//RESERVE
  $scrollTo = <<<SC
  <script>
	$(document).ready(function(){
		setTimeout(function(){
			$('html, body').animate({
					scrollTop: $("#availability").offset().top
			}, 2000)
		},1000);
	});
  </script>
SC;
  $adult = $_REQUEST['adult_r'];
  $child = $_REQUEST['child_r'];
  $paztarikh = $_REQUEST['aztarikh_r'];
  $fname = $_REQUEST['fname'];
  $lname = $_REQUEST['lname'];
  $aztarikh = audit_class::hamed_pdateBack($paztarikh);
  $shab = (int)$_REQUEST['shab_r'];
  $room_selected = $_REQUEST['room-select'];
  $room_type = $_REQUEST['room-type'];
  $room_id = $_REQUEST['room-ids'];
	$tozihat = 'رزرو آنلاین'."\n";
	$tozihat .= 'تلفن : '.$_REQUEST['tell'];
	$tozihat .= 'توضحیات : '.$_REQUEST['toz'];
	$tozihat .= 'شماره ملی : ' . $_REQUEST['smelli'];
	if(isset($_REQUEST['transfer'])){
		$tozihat .= 'ترانسفر : '.$_REQUEST['transfer'];
	}
  $to_reserve_rooms_id = array();
  foreach($room_selected as $i=>$rselect){
    if((int)$rselect>0){
			var_dump($room_id[$i]);
      $tmp_room = explode(',',$room_id[$i]);
      for($j = 0;$j < (int)$rselect;$j++){
        $to_reserve_rooms_id[] = $tmp_room[$j];
      }
    }
  }
  $nafar = $adult+$child;
  $tedad = count($to_reserve_rooms_id);
  $sargrooh = $fname.' '.$lname;
  $ghimat = getGhimat($shab,$to_reserve_rooms_id);
  $preRes = room_det_class::preReserve($hotel_id,$ajans_id,$to_reserve_rooms_id,$ghimat,
                             $aztarikh,$shab,$tedad,FALSE,FALSE,
                             $nafar,array(),$user_id);
  if($preRes!==FALSE){
    $tmp = room_det_class::sabtReserveHotel($preRes['reserve_id'],$preRes['shomare_sanad'],null,$fname,$lname,$tozihat,$ajans_id,$ghimat,null);
  }
}
$lkhadamat = loadKhadamat();
$alert = '';
if(isset($preRes)){
	$clas = 'danger';
	if($preRes!==FALSE){
		$tex = 'رزرو با موفقیت انجام شد';
		$tex .= '<br/>';
		$tex .= 'شماره رزرو شما : ';
		$tex .= $preRes['reserve_id'];
		$clas = 'success';
	}else{
		$tex = 'خطا در ثبت رزرو';
	}
	$alert =<<<AL
		<div class="alert alert-$clas">
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
$result = '';
if($msg!=''){
	$result .= '<div class="alert alert-danger" style="text-align:right">'.$msg.'</div>';
}
if($result_ready){
	$result .= createResult($rooms,$adult,$child,$aztarikh,$paztarikh,$shab,$ghimat,$is_transfer);
} 
if($result_ready){
	$result .= $lkhadamat;
}
$tem = file_get_contents('index.template.html');
$out = str_replace("#alert#",$alert,$tem);
$out = str_replace("#shab_combo#",$shab_combo,$out);
$out = str_replace("#adult_combo#",$adult_combo,$out);
$out = str_replace("#child_combo#",$child_combo,$out);
$out = str_replace("#result#",$result,$out);
$out = str_replace("#scrollTo#",$scrollTo,$out);
$out = str_replace("#url_main#",$url_main,$out);
$out = str_replace("#aztarikh#",$paztarikh,$out);
echo $out;
?>
