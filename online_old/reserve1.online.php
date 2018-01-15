<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
session_start();
include("../kernel.php");
$conf->setMoshtari(1);
$_SESSION['user_id'] = $conf->online_user_id;
$_SESSION['daftar_id'] = $conf->online_daftar_id;
$user_id = $conf->online_user_id;
$daftar_id = $conf->online_daftar_id;
$hotel_id = $conf->online_hotel_id;
$ajans_id = $conf->online_ajans_id;
$_SESSION['online'] = TRUE;
$result_ready = FALSE;
function createResult($rooms,$adult,$child,$aztarikh,$paztarikh,$shab,$ghimat){
  $out = '<form method="post"><div class="panel1">';
  $out .= '<input type="hidden" name="adult_r" value="'.$adult.'" />';
  $out .= '<input type="hidden" name="child_r" value="'.$child.'" />';
  $out .= '<input type="hidden" name="aztarikh_r" value="'.$aztarikh.'" />';
  $out .= '<input type="hidden" name="shab_r" value="'.$shab.'" />';
  $out .= '<div>';
  $out .= 'بزرگسال : '.$adult.' کودک : '.$child.' از '.$paztarikh.' به مدت '.$shab.' شب';
  $out .= '<br/>';
  $out .= 'جمع کل : '.$ghimat. ' ریال';
  $out .= '</div>';
  foreach($rooms as $i=>$room){
    $out .= '<div class="room-block">';
    $out .= '<img src="hotel-bed-icon.png" />';
    $out .= $room['name'];
    $out .= '['.$room['ghimat'].' ریال'.']';
    $out .= '<select id="room-select-'.$i.'" name="room-select[]">';
    $out .= '<option value="0">0</option>';
    $out .= '<option value="1">1</option>';
    $out .= '<option value="2">2</option>';
    $out .= '<option value="3">3</option>';
    $out .= '</select>';
    $out .= '<input type="hidden" name="room-type[]" value="'.$room['room_typ_id'].'" />';
    $out .= '<input type="hidden" name="room-ids[]" value="'.implode(',',$room['room_ids']).'" />';
    $out .= '</div>';
  }
  $out .='</div>';
  $out .='<div class="panel1">';
  $out .= 'اطلاعات سرگروه';
  $out .= '<br/>';
  $out .= 'نام'.'<input name="fname" class="form-control1" placeholder="نام" />';
  $out .= 'نام خانوادگی'.'<input name="lname" class="form-control1" placeholder="نام خانوادگی" />';
  $out .='<button>ادامه</button>';
  $out .='</div></form>';
  return $out;
}
function getGhimat($aztarikh,$shab,$nafar){
  global $hotel_id;
  $hot = new hotel_class((int)$hotel_id);
  $out = 0;
  $tmp = $aztarikh;
  for($i = 0;$i < (int)$shab;$i++)
  {
    $out += $hot->getGhimat($tmp);
    $tmp = date("Y-m-d 14:00:00",strtotime($tmp.' + 1 day'));
  }
  $out *= $nafar;
  return $out;
}
//SEARCH
if(isset($_REQUEST['aztarikh'])){
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
      $ghimat = getGhimat($aztarikh,$shab,$nafar);
    }
  }else{
    echo "HOTEL NOT AVILABLE<br/>\n";
  }
}elseif(isset($_REQUEST['room-select'])){
//RESERVE
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
  $to_reserve_rooms_id = array();
  foreach($room_selected as $i=>$rselect){
    if((int)$rselect>0){
      $tmp_room = explode(',',$room_id[$i]);
      for($j = 0;$j < (int)$rselect;$j++){
        $to_reserve_rooms_id[] = $tmp_room[$j];
      }
    }
  }
  $nafar = $adult+$child;
  $tedad = count($to_reserve_rooms_id);
  $sargrooh = $fname.' '.$lname;
  $ghimat = getGhimat($aztarikh,$shab,$nafar);
  $preRes = room_det_class::preReserve($hotel_id,$ajans_id,$to_reserve_rooms_id,$ghimat,
                             $aztarikh,$shab,$tedad,FALSE,FALSE,
                             $nafar,array(),$user_id);
  if($preRes!==FALSE){
    $tmp = room_det_class::sabtReserveHotel($preRes['reserve_id'],$preRes['shomare_sanad'],null,$fname,$lname,'رزرو آنلاین',$ajans_id,$ghimat,null);
  }
}
?>
  <!DOCTYPE html>
  <html lang="fa">

  <head>

    <meta charset="utf-8" />
    <title>سامانه رزرواسیون</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <!-- JS -->
    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/persian-date.js"></script>
    <script src="../js/persian-datepicker-0.4.5.min.js"></script>
    <script type="text/javascript" src="../js/tavanir.js"></script>
    <!-- CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../css/persian-datepicker-0.4.5.min.css" rel="stylesheet" type="text/css" />
    <style>
      .panel1 {
        border: 1px solid #eaeaea;
        margin: 5px;
        padding: 5px;
      }
      
      .pdate {
        margin-bottom: 5px;
        width: 200px;
      }
      
      select.form-control {
        width: 200px;
      }
      
      .room-block{
        padding: 5px;
        margin: 5px;
        border: 1px solid #eaeaea;
        cursor: pointer;
       
      }
      .room-block:hover{
        background-color:#0093ff1a;
      }
      .room-block img{
        height : 50px;
        border:1px solid #eaeaea;
        padding:5px;
        margin:10px;
        background-color:#fff;
      }
      .room-block select{
        
      }
    </style>
    <script>
      $(document).ready(function() {
      });
    </script>
  </head>

  <body dir="rtl">
    <?php
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
      ?>
    <div class="alert alert-<?php echo $clas; ?>">
      <?php echo $tex; ?>
    </div>
    <?php
    }
    ?>
    <div class="panel1">
      <form method="post">
        تاریخ ورود
        <input class="form-control1 pdate" name="aztarikh" id="aztarikh" placeholder="تاریخ ورود"  />
        تعداد شب اقامت
        <select class="form-control1" name="shab" id="shab">
          <option value="1">1</option>  
          <option value="2">2</option>  
          <option value="3">3</option>  
          <option value="4">4</option>  
          <option value="5">5</option>  
        </select>
        بزرگسال
        <select class="form-control1" name="adult" id="adult">
          <option value="1">1</option>  
          <option value="2">2</option>  
          <option value="3">3</option>  
          <option value="4">4</option>  
          <option value="5">5</option>  
        </select>
        کودک
        <select class="form-control1" name="child" id="child">
          <option value="0">0</option>  
          <option value="1">1</option>  
          <option value="2">2</option>  
          <option value="3">3</option>  
          <option value="4">4</option>  
          <option value="5">5</option>  
        </select>
        <button class="form-control1">
          جستجو  
        </button>
      </form>
    </div>
    <?php if($result_ready) echo createResult($rooms,$adult,$child,$aztarikh,$paztarikh,$shab,$ghimat); ?>
    <script>
        $(".pdate").persianDatepicker({
          timePicker: {
            enabled: false
          },
          format: "YYYY/MM/DD"
        });    
    </script>
  </body>