<?php
$root="";
	session_start();
	include_once("../kernel.php");
	if(isset($_SESSION['online'])){
		die("<script>window.location = 'login.php';</script>");
	}
	$pass=((isset($_REQUEST['pass']))?$_REQUEST['pass']:"");
	$user=((isset($_REQUEST['user']))?$_REQUEST['user']:"");
	$kelid = ((isset($_REQUEST['kelid']))?$_REQUEST['kelid']:-1);
	date_default_timezone_set("Asia/Tehran");
	$firstVisit = (isset($_SESSION["login"]) && ($_SESSION["login"] == 1) && isset($_REQUEST["user"]));
	if($firstVisit ||(isset($_SESSION["user_id"]))){	
		function loadUserById($id){
			$out = 'تعریف نشده';
			mysql_class::ex_sql("select fname,lname from user where id=$id",$qq);
			if($r= mysql_fetch_array($qq,MYSQL_ASSOC))
			{
				$out = $r["fname"]." ".$r["lname"];
			}
			return $out;
		}
		function isOdd($inp){
			$out = TRUE;
			if((int)$inp % 2==0){
				$out = FALSE;
			}
			return $out;
		}
		$now_tarikh1 = Date ("Y-m-d 00:00:00");
		$now_tarikh2 = Date ("Y-m-d 23:59:59");	
		if($firstVisit){
	//		$matn_sharj = $_SESSION["matn_sharj"];
	//		echo $matn_sharj;
			//echo "+++++++first+++++++";
			if(!$conf->setMoshtari((int)moshtari_class::getKey($kelid)))
				die("<script>window.location = 'login.php?stat=wrong_pass&';</script>");
			$is_modir  = FALSE;
			mysql_class::ex_sql("select * from user where user = '".$user."'",$q);

			if($r_u = mysql_fetch_array($q,MYSQL_ASSOC))
			{
				if($pass == $r_u["pass"])// && (int)$r_u['ajans_id'] == -1)
				{
					$is_modir =(($r_u["typ"]==0)?0:1);
					$_SESSION["user_id"] = (int)$r_u["id"];
					$_SESSION["daftar_id"] = (int)$r_u["daftar_id"];
					$_SESSION["typ"] = (int)$is_modir;
					$user1_id = $_SESSION["user_id"];
					$user1 = new user_class((int)$user1_id);
					$user_grop = $r_u["typ"];
					$user_id = $_SESSION["user_id"];
					if(method_exists($user1,'sabt_vorood'))
						$user1->sabt_vorood();
	//////////////////////////////////

	/////////////////////////////////

	////////////////////////////////
				}
				else
				{
					die("<script//>window.location = 'login.php?stat=wrong_pass&1';</script>");
				}
			}
			else
			{
				die("<script>//window.location = 'login.php?stat=wrong_user&2';</script>");
			}
		}
	}
	if(!isset($_SESSION['user_id']))
		die("<script>window.location = 'login.php';</script>");
	$se = security_class::auth((int)$_SESSION['user_id']);
	//if(!$se->can_view)
		//die("3<script>//window.location = 'login.php?stat=wrong_pass&';</script>");

	$log_user_id = $_SESSION["user_id"];
	mysql_class::ex_sql("select count(`id`) as `t_payam` from `payam` where `rec_user_id`='$log_user_id' and `en`='-1'",$q_payam);
	if ($r_payam = mysql_fetch_array($q_payam))
		$showPayam = $r_payam['t_payam'];
	else
		$showPayam = 0;
	function loadHotel()
	{
			$tmp_hotel_id = array();
			mysql_class::ex_sql("select `hotel_id` from `hotel_daftar` where `daftar_id`=".$_SESSION['daftar_id'],$q);			
			while($r = mysql_fetch_array($q))
				$tmp_hotel_id[]= $r['hotel_id'];
			$out = 'عدم دسترسی کاربر به هتل';
			if(count($tmp_hotel_id))
			{
				$out=null;
				$tmp_hotel_ids = implode(',',$tmp_hotel_id);
				mysql_class::ex_sql("select `id`,`name` from hotel where `id` in ($tmp_hotel_ids) order by name",$q);
				while($r=mysql_fetch_array($q,MYSQL_ASSOC))
						$out[$r['name']]=(int)$r['id'];
			}
			return $out;
	}
	function loadRoom()
	{
			$out = null;
			mysql_class::ex_sql("select * from room_typ order by zarfiat",$q);
			while($r=mysql_fetch_array($q,MYSQL_ASSOC))
					$out[$r['name']]=(int)$r['id'];
			return $out;
	}
	function loadPic($id)
	{
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('loadPic.php?room_id=$id&','',500,600);\">ادامه</span></u>";
		return($out);
	}
	function room_status($stat)
	{
		$out[0] = 'اشغال شده';
		$out[1] = 'خالی اما نظافت نشده';
		$out[2] = 'خالی و نظافت شده';
		$out[3] = 'درحال نظافت';
		$out[4] = 'پشتیبان';
		$out[5] = 'در حال تعمیر';
		return($out[$stat]);
	}
	function room_status_icon($stat)
	{
		$out = "<img height=\"30px\" src = \"../img/$stat.png\" title=\"".room_status($stat)."\" alt=\"".room_status($stat)."\"/>";
		return($out);
	}
	$shart_1 = ' where 1=0 ';
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart = '';
	if($hotel_acc!=null)
	{
		if (count($hotel_acc)==1)
			$_REQUEST["hotel_id_new"] = $hotel_acc[0];
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
		$shart_1 = "where `id` in ".$shart;
	}
////////////////////
	if (isset($_REQUEST["hotel_id_new"]))
                $hotel_id_new = $_REQUEST["hotel_id_new"];
	else
		$hotel_id_new = -1;
	$global_prob = FALSE;
	mysql_class::ex_sql("select count(id) as cid from tasisat_tmp where room_id < 0 and en=-1",$qall);
	if($r = mysql_fetch_array($qall))
		$global_prob = ((int)$r['cid']>0);
	$combo_hotel = "";
    $combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"get\">";
    $combo_hotel .="<div class='col-lg-2 col-md-3 col-sm-4 col-xs-12'>";
    $combo_hotel .= "<label>هتل :</label> <select class='form-control selectO' id=\"hotel_id\" name=\"hotel_id_new\" onchange=\"document.getElementById('selHotel').submit();\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel`$shart_1 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$hotel_id_new)
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
    $combo_hotel .= "</div>";
	$combo_hotel .= "</form>";
$combo_hotel2 = "";
    $combo_hotel2 .= "<form name=\"selHotel2\" id=\"selHotel2\" method=\"get\">";
    $combo_hotel2 .="<div>";
    $combo_hotel2 .= "<label>هتل :</label> <select class='form-control selectO' id=\"hotel_id\" name=\"hotel_id_new\" onchange=\"document.getElementById('selHotel2').submit();\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel`$shart_1 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$hotel_id_new)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_hotel2 .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
		        $combo_hotel2 .= $r["name"]."\n";
		        $combo_hotel2 .= "</option>\n";
		}
	$combo_hotel2 .= "</select>";
    $combo_hotel2 .= "</div>";
	$combo_hotel2 .= "</form>";
	$hotel_id = $hotel_id_new;
	$tarikh = (isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):date("Y-m-d");
	$room_typ = (isset($_REQUEST['room_typ']))?$_REQUEST['room_typ']:-1;
	$tarikh = explode(' ',$tarikh);
	$tarikh = $tarikh[0];
	
	$sday = date("Y-m-d 00:00:00");
	$eday = date("Y-m-d 23:59:59");
	//$eday = date("Y-m-d H:i:s");
	$day = Date("Y-m-d 14:00:00 ");
	$today_khoruj = 0; 
	$count_mehman = 0;
	$count_room_khali = 0;
	$tedad_mehman = 0;
	$full_room = 0;
	$full_room1 = 0;
	$free_room = 0;
	$dirty_room = 0;
	$out_serviceRoom = 0;
    $roomsCounter=0;
	$tedad_mehman_moghim = 0;
	$poshtiban = 0;
	$tmp_full =0;
	$y = Date("Y");
	$m = Date("m");
	$d = Date("d");	
	$day1 =mktime("14","00","00",$m,$d,$y);
	$shart ='';
	$t_vorudi = 0;
	$rooms_id = "(";
	if ($hotel_id_new!=-1)
	{
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$hotel_id' order by `name`",$query2);
		while($res2 = mysql_fetch_array($query2))
		{
			$rooms_id .= $res2["id"].',';
		}
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
		if ($rooms_ids!="")
			$shart = " `room_id` in $rooms_ids ";
		mysql_class::ex_sql("select `nafar`,`reserve_id`,`tatarikh`,`room_id` from `room_det` where $shart",$qr);
		while($rr = mysql_fetch_array($qr))
		{
			$tatarikh= $rr["tatarikh"];
			$res = $rr["reserve_id"];
			$room_id = $rr["room_id"];
			$ye = substr($tatarikh,0,4);
			$mo = substr($tatarikh,5,2);
			$da = substr($tatarikh,8,2);
			$tmp_tatarikh =mktime("14","00","00",$mo,$da,$ye);
			if (($tmp_tatarikh == $day1)&&($res>0)&&(!reserve_class::isKhorooj($res,$room_id)))
				$today_khoruj ++;
		}
		mysql_class::ex_sql("select `id`,`vaziat`,`name` from `room` where `en`='1' and `hotel_id`='$hotel_id'",$q);
		while($r = mysql_fetch_array($q))
		{
			$id = $r["id"];
			$rooms = room_det_class::roomIdAvailable($id,$sday,$eday);
			if (!(count($rooms)==0))
			{
				$tedad_mehman .= (($tedad_mehman=='')?'':',').$id;
				$full_room ++;
			}
		}
		$today = date("Y-m-d");
		mysql_class::ex_sql("select count(`id`) as `tedad_v` from `room_det` where date(`aztarikh`)='$today' and `room_id` in $rooms_ids and `reserve_id`>0",$q);
		if($r = mysql_fetch_array($q))
		{
			$t_vorudi = $r['tedad_v'];
		}
	}

$q=null;
	mysql_class::ex_sql("select `id`,`vaziat`,`name` from `room` where `en`='1' and `hotel_id`='$hotel_id'",$q);
	while($r = mysql_fetch_array($q))
	{
		if ($r["vaziat"] == 0)
			$full_room1 ++;
		if ($r["vaziat"] == 1)
			$dirty_room ++;
		if ($r["vaziat"] == 2)
			$free_room ++;
		if ($r["vaziat"] == 4)
			$out_serviceRoom ++;
		if ($r["vaziat"] == 5)
			$poshtiban ++;
		if ($r["vaziat"] == 3)
			$tmp_full ++;
	}
	$day = date("Y-m-d");
	$i = 1;
	$aztarikh = $day;
	$tatarikh = $day;
	$q=null;
//echo count($tedad_mehman);
	$shart ='';
	$rooms_id = "(";
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$hotel_id' order by `name`",$query2);
		while($res2 = mysql_fetch_array($query2))
		{
			$rooms_id .= $res2["id"].',';
		}
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
		if ($rooms_ids!="")
			$shart = " `room_id` in $rooms_ids ";
		$day = date("Y-m-d");
		$i = 1;
		$aztarikh = $day;
		$tatarikh = $day;
		$q = null;
		mysql_class::ex_sql("select `reserve_id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) <= '$aztarikh' and date(`tatarikh`) >= '$aztarikh') or (date(`aztarikh`) < '$tatarikh' and date(`tatarikh`) > '$tatarikh')) group by `reserve_id`",$q);

		$tmp ='';
		if(isset($_REQUEST['hotel_id_new']))
			$h_id = $_REQUEST['hotel_id_new'];
		else
			$h_id = -1;
		while ($r = mysql_fetch_array($q))
		{
			$r_hotel = room_class::loadHotelByReserve($r['reserve_id']);
			if ($h_id==$r_hotel)
				$tmp .=($tmp==''? '':',' ).$r['reserve_id'];
		}
		if ($tmp!='') 
		{
			mysql_class::ex_sql("select count(`id`) as `tedad` from `mehman` where `reserve_id` in ($tmp) and `khorooj`='0000-00-00 00:00:00'",$q_hotel);
			if ($r_hotel = mysql_fetch_array($q_hotel))
				$tedad_mehman_moghim = $r_hotel["tedad"];
		}
		$combo = "";
		$combo .= "<form name=\"selRoom\" id=\"selRoom\" method=\"GET\">";
		$combo .= "نوع اتاق : <select class='inp' id=\"room_typ\" name=\"room_typ\" onchange=\"document.getElementById('selRoom').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		$combo .= "<option selected='selected' value=\"-1\">\n";
       		$combo .= "همه"."\n";
      		$combo .= "</option>\n";
	$tedad = 0;
	$name_typ = "";
	mysql_class::ex_sql("select `id`,`name`,`room_typ_id` from room where `hotel_id`='$hotel_id' group by `room_typ_id`",$q);
        while($r = mysql_fetch_array($q))
        {
		$typ_room = $r["room_typ_id"];
		$id = $r["id"];
		mysql_class::ex_sql("select `id`,`name` from room_typ where `id`='$typ_room'order by zarfiat",$q_typ);
//echo "select `id`,`name` from room_typ where `id`='$typ_room'order by zarfiat";
       		if($r_typ = mysql_fetch_array($q_typ))
			$name_typ = $r_typ["name"];
		mysql_class::ex_sql("select count(`id`) as `tedad` from `room` where `room_typ_id`='$typ_room' and `hotel_id`='$hotel_id' group by `room_typ_id`",$qu);
		if($row = mysql_fetch_array($qu)){
			$tedad = $row['tedad'];
            $roomsCounter=$tedad+$roomsCounter;
        }
		if($typ_room== (int)$room_typ)
            $select = 'selected="selected"';
        else
            $select = "";
            $combo .= "<option value=\"".(int)$typ_room."\" $select   >\n";
            $combo .= $name_typ.'('.$tedad.')'."\n";
            $combo .= "</option>\n";
        }
$combo .="</select>";
$combo .='<input class="inp" type="hidden" name="hotel_id_new" id="hotel_id_new" value="'.$hotel_id_new.'"  >';
$combo .= "</form>";
$out = hotel_class::getRack1($hotel_id,$room_typ,$se);
// var_dump($out);
// exit();
if (isset($_REQUEST['seName']))
{
    $re = array();
    $seName = $_REQUEST['seName'];
    $res_se = hotel_reserve_class::loadByName($seName);
    foreach($res_se as $res_id)
        if ($res_id > 0)
        $re[] = room_det_class::loadByReserve_rack($res_id);
    die(toJSON($re));
}


?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>RAHA</title>
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
	<script type="text/javascript">
//window.setTimeout(function(){ document.location.reload(true); }, 30000);
</script>
	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
	
</head>
<body>
    
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
				
                 <!-- clock -->
                <div class="row">
<!-- 					<div id="" class="col-lg-12">
						<div class="tab-content">
								   <div class="tab-pane active" id="tab1">
									  <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
							<div class="panel-heading box border orange">
								<h4 class="panel-title"> <a class="accordion-toggle box-title" data-toggle="collapse" data-parent="#accordion" href="#collapse1_1"><h4><i class="fa fa-calendar"></i> تقویم و ساعت </h4> </a> </h4>
							</div>
							<div id="collapse1_1" class="panel-collapse collapse">
								<div class="panel-body"><div class="row">
											<div class="col-md-6">
												<div id="cal-pr" style="margin: auto;"></div>
											</div>
											<div class="col-md-6">
												<div id="clock" class="light" dir="ltr">
													<div class="display">
														<div class="weekdays"></div>
														<div class="ampm"></div>
														<div class="alarm"></div>
														<div class="digits"></div>
													</div>
												</div>
											</div>
										</div>
                                </div>
							</div>
				        </div>
                                          </div>
                                       </div>
                            </div>
				
                        
					</div> -->
				</div>
               <!-- end clock -->


      
                <!-- GALLERY -->
						<div class="row">
							<div class="col-md-12">
								<!-- BOX -->
								<div class="box">
									
									<div class="box-body clearfix">
									   <div id="filter-controls" class="btn-group" style="width:100%">
										  <div class="hidden-xs">
                                              
                                              <a target="_blank" href="mehman.php?h_id=<?php echo $hotel_id ?>">
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-default">
                                                          <div class="panel-heading">
                                                              <div class="row">
                                                                  <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                      <i class="fa fa-male fa-3x"></i>
                                                                  </div>
                                                                  <div class="col-xs-9 text-right">
                                                                      <div class="huge"><?php echo $tedad_mehman_moghim; ?></div>
                                                                      <div>مهمان حاضر</div>
                                                                  </div>
                                                              </div>
                                                          </div>
           
                                                      </div>
                     
                                                </div> 
                                              </a>
                                             
											  <a href="#" data-filter="*">
                                              
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-mdb">
                                                          <div class="panel-heading">
                                                              <div class="row">
                                                                  <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                      <i class="fa fa-home fa-3x"></i>
                                                                  </div>
                                                                  <div class="col-xs-9 text-right">
                                                                      <div class="huge"><?php echo $roomsCounter; ?></div>
                                                                      <div>کل اتاق ها</div>
                                                                  </div>
                                                              </div>
                                                        </div>
           
                                                      </div>

                                                      </div> 
                                                
                                              </a>
                                              
                                              <a href="#" data-filter=".enter">
                                              
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-mdb-teal">
                                                        <div class="panel-heading">
                                                            <div class="row">
                                                                <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                    <i class="fa fa-sign-in fa-3x"></i>
                                                                </div>
                                                                <div class="col-xs-9 text-right">
                                                                    <div class="huge"><?php echo $t_vorudi; ?></div>
                                                                    <div>ورودی امروز</div>
                                                                </div>
                                                            </div>
                                                          </div>
           
                                                      </div>
                     
                                                  </div> 
                                              
                                              </a>
                                              <a href="#" data-filter=".exit">
                                              
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-mdb-pink">
                                                          <div class="panel-heading">
                                                              <div class="row">
                                                                  <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                      <i class="fa fa-plane fa-3x"></i>
                                                                  </div>
                                                                  <div class="col-xs-9 text-right">
                                                                    <div class="huge"><?php echo $today_khoruj; ?></div>
                                                                    <div>خروجی امروز</div>
                                                                  </div>
                                                              </div>
                                                          </div>
           
                                                      </div>
                     
                                                  </div> 
                                              
                                              </a>
                                              <a href="#" data-filter=".service">
                                              
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-mdb-brown">
                                                        <div class="panel-heading">
                                                            <div class="row">
                                                                <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                    <i class="fa fa-wrench fa-3x"></i>
                                                                </div>
                                                                <div class="col-xs-9 text-right">
                                                                    <div class="huge"><?php echo $out_serviceRoom; ?></div>
                                                                    <div>در دست تعمیر</div>
                                                                </div>
                                                            </div>
                                                          </div>
           
                                                      </div>
                     
                                                  </div> 
                                              
                                              </a>
                                              <a href="#" data-filter=".full">
                                              
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-mdb-red">
                                                          <div class="panel-heading">
                                                              <div class="row">
                                                                  <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                      <i class="fa fa-lock fa-3x"></i>
                                                                  </div>
                                                                  <div class="col-xs-9 text-right">
                                                                      <div class="huge"><?php echo $full_room1; ?></div>
                                                                      <div>اتاق اشغال</div>
                                                                  </div>
                                                              </div>
                                                          </div>
           
                                                    </div>
                     
                                                  </div> 
                                              
                                              </a>
                                              <a href="#" data-filter=".free">
                                              
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-mdb-green">
                                                          <div class="panel-heading">
                                                              <div class="row">
                                                                  <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                      <i class="fa fa-unlock fa-3x"></i>
                                                                </div>
                                                                <div class="col-xs-9 text-right">
                                                                    <div class="huge"><?php echo $free_room;?></div>
                                                                    <div>اتاق خالی</div>
                                                                  </div>
                                                              </div>
                                                          </div>
           
                                                      </div>
                     
                                                  </div> 
                                              
                                              </a>
                                              <a href="#" data-filter=".dirty">
                                              
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-mdb-yellow">
                                                          <div class="panel-heading">
                                                              <div class="row">
                                                                  <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                      <i class="fa fa-trash-o fa-3x"></i>
                                                                </div>
                                                                <div class="col-xs-9 text-right">
                                                                    <div class="huge"><?php echo $dirty_room;?></div>
                                                                    <div>نظافت نشده</div>
                                                                </div>
                                                              </div>
                                                          </div>
           
                                                    </div>
                     
                                                </div> 
                                              
                                              </a>
                                              
                                              
                                              <a href="#" data-filter=".poshtiban">
                                              
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-mdb-blue">
                                                        <div class="panel-heading">
                                                            <div class="row">
                                                                <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                    <i class="fa fa-gear fa-3x"></i>
                                                                </div>
                                                                <div class="col-xs-9 text-right">
                                                                    <div class="huge"><?php echo $poshtiban;?></div>
                                                                    <div>پشتیبان</div>
                                                                </div>
                                                            </div>
                                                          </div>
           
                                                      </div>
                     
                                                  </div> 
                                              
                                              </a>
                                              <a href="#" data-filter=".temporary">
                                              
                                                  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-top:10px;">
                                                      <div class="panel panel-mdb-orange">
                                                          <div class="panel-heading">
                                                              <div class="row">
                                                                  <div class="col-xs-3 pull-left" style="padding-right:0px;">
                                                                      <i class="fa fa-user fa-3x"></i>
                                                                  </div>
                                                                  <div class="col-xs-9 text-right">
                                                                      <div class="huge"><?php echo $tmp_full ?></div>
                                                                      <div>اشغال موقت</div>
                                                                  </div>
                                                              </div>
                                                          </div>
           
                                                      </div>
                         
                                                  </div> 
                                              
                                              </a>
                                              
                                              
                                             
											<!-- list of hotels -->	                                        	
                                            <?php echo $combo_hotel; ?>
                                               
                                            <!-- list of room types -->	                                        	
                                            <?php //echo $combo; ?>
                                             
                                              
                                               <form name="selRoom" id="selRoom" method="GET">
                                                <div class='col-lg-2 col-md-3 col-sm-4 col-xs-6'>
												    <div id='e1'>                                        	
                                                        <label>نوع اتاق : </label>
		                                                <select class='form-control selectO' id="room_typ" name="room_typ">
                                                            <option value='*'>همه</option>
                                              <?php
        mysql_class::ex_sql("select `id`,`name`,`room_typ_id` from room where `hotel_id`='$hotel_id' group by `room_typ_id`",$q);
        while($r = mysql_fetch_array($q))
        {
		$typ_room = $r["room_typ_id"];
		$id = $r["id"];
		mysql_class::ex_sql("select `id`,`name` from room_typ where `id`='$typ_room'order by zarfiat",$q_typ);
       		if($r_typ = mysql_fetch_array($q_typ)){
                $name_typ = $r_typ["name"];
                $id_type = $r_typ["id"];
            }
			
		mysql_class::ex_sql("select count(`id`) as `tedad` from `room` where `room_typ_id`='$typ_room' and `hotel_id`='$hotel_id' group by `room_typ_id`",$qu);
		if($row = mysql_fetch_array($qu))
			$tedad = $row['tedad'];
		if($typ_room== (int)$room_typ)
                        $select = 'selected="selected"';
                else
                        $select = "";
                echo "<option value='.roomtype$id_type' $select>$name_typ($tedad)</option>";
        }



                                       ?>       

                                           </select>
                                                        </div>
                                                    </div>
                                                </form>

										  </div>
										  <div class="visible-xs">
                                                <div class='col-xs-12'>
                                                  <div id='e1'> 
											   <?php echo $combo_hotel2; ?>
        </div>
                                                      </div>
										  </div>
                                           
                                          <div class="visible-xs">
                                               <form name="selRoom" id="selRoom" method="GET">
                                                <div class='col-xs-12'>
												    <div id='e2'>                                      	
                                                        <label>نوع اتاق : </label>
		                                                <select class='form-control selectO' id="room_typ" name="room_typ">
                                                            <option value='*'>همه</option>
                                              <?php
		$hotel_id = (int)$hotel_id;
		mysql_class::ex_sql("select tabaghe,daftar_id,daftar.name from hotel_garanti left join daftar on (daftar.id=daftar_id) where hotel_id = $hotel_id",$qgaranti);
		$gtabaghat = array();
		while($row_gara = mysql_fetch_array($qgaranti)){
			$gtabaghat[(int)$row_gara['tabaghe']] = $row_gara['name'];
		}
// 		var_dump($gtabaghat);
		mysql_class::ex_sql("select `id`,`name`,`room_typ_id` from room where `hotel_id`='$hotel_id' group by `room_typ_id`",$q);
		while($r = mysql_fetch_array($q))
		{
		$typ_room = $r["room_typ_id"];
		$id = $r["id"];
		mysql_class::ex_sql("select `id`,`name` from room_typ where `id`='$typ_room'order by zarfiat",$q_typ);
       		if($r_typ = mysql_fetch_array($q_typ)){
                $name_typ = $r_typ["name"];
                $id_type = $r_typ["id"];
            }
			
		mysql_class::ex_sql("select count(`id`) as `tedad` from `room` where `room_typ_id`='$typ_room' and `hotel_id`='$hotel_id' group by `room_typ_id`",$qu);
		if($row = mysql_fetch_array($qu))
			$tedad = $row['tedad'];
		if($typ_room== (int)$room_typ)
                        $select = 'selected="selected"';
                else
                        $select = "";
                echo "<option value='.roomtype$id_type' $select>$name_typ($tedad)</option>";
        }



                                       ?>       

                                           </select>
                                                       </div>
                                                    </div>
                                                </form>
                                              <!--
											   <select id="e3" class="form-control">
													<option value="" />اتاق
                                                        <option value=".category_11" />یک تخته
                                                        <option value=".category_12" />دو تخته
                                                        <option value=".category_111" />طبقه اول
                                                        <option value=".category_222" />طبقه دوم
                                                        <option value=".category_333" />طبقه سوم
												</select>-->
										  </div>
                                           
                                           <div class="visible-xs">
                                              <div class='col-xs-12'>
                                                  <div id='e3'> 
                                                      <label>آمار : </label> 
                                                      <select class="form-control selectO">
                                                          <option value="*" />کل اتاق ها
                                                          <option value=".enter" />ورودی امروز
                                                          <option value=".full" />اتاق اشغال
                                                          <option value=".free" />اتاق خالی
                                                          <option value=".dirty" />نظافت نشده
                                                          <option value=".service" />در دست تعمیر
                                                          <option value=".poshtiban" />پشتیبان
                                                          <option value=".temporary" />اشغال موقت
                                                        <option value=".exit" />خروجی امروز
                                                      </select>
                                                  </div>
                                               </div>
                                           </div>
                                        </div>
<!-- 										<div id="filter-items" style="width:100%"> -->
                                            <?php
// 																						var_dump($out);
																						$rak_data = $out;
                                            foreach ($out as $rooms)
                                            {
																							if(isset($gtabaghat[(int)$rooms[0]['tabaghe']])){
																								echo '<div style="text-align: center;background-color: #dcb2a9;padding: 5px;margin: 10px;">';
																								echo 'گارانتی ' . $gtabaghat[(int)$rooms[0]['tabaghe']] . " [طبقه ".(($rooms[0]['tabaghe']!=0)?$rooms[0]['tabaghe']:'همکف')."]";																							
																							}else{
																								echo '<div style="text-align: center;background-color: #b2dfdb;padding: 5px;margin: 10px;">';
																								echo "طبقه ".(($rooms[0]['tabaghe']!=0)?$rooms[0]['tabaghe']:'همکف');
																							}
																							echo '</div>';
																							echo '<div class="filter-items" style="width:100%">';
                                                foreach ($rooms as $rak)
                                                {
																									
                                                    $databases = (isset($_POST['odatabases']))?$_POST['odatabases']:"";
// 																										var_dump($rak);
                                                    if($rak['info']!=null){
																											
																												if(isset($rak['info'][1])){
// 																													if($rak['info'][0]['is_khorooj'])
// 																														$rak['info'][0] = $rak['info'][1];
																												}
																												
                                                        $ajans=$rak['info'][0]['ajans'];
                                                        $ajans =(isset($ajans))?$ajans:"";
                                                        $reserve_id=$rak['info'][0]['reserve_id'];
                                                        $reserve_id =(isset($reserve_id))?$reserve_id:"";
                                                        $rname=$rak['info'][0]['lname'];
                                                        $rname =(isset($rname))?$rname:"";
                                                        $rnafar=$rak['info'][0]['nafar'];
                                                        $rnafar =(isset($rnafar))?$rnafar:"";
                                                        $hoid=$rak['info'][0]['reserve']->hotel_reserve->id;
                                                        $hoid =(isset($hoid))?$hoid:"";
                                                        $hotelPrice=$rak['info'][0]['reserve']->hotel_reserve->m_hotel;
                                                        $hotelPrice =(isset($hotelPrice))?$hotelPrice:"";
                                                        $tozih=$rak['info'][0]['reserve']->hotel_reserve->extra_toz;
                                                        $tozih =(isset($tozih))?$tozih:"";
                                                        $roomsname=$rak['info'][0]['reserve']->room;
																												$service_ezafe=$rak['info'][0]['service_ezafe'];
                                                        if($roomsname){
                                                            $rooms="";
                                                            foreach ($roomsname as $roomname){
                                                                $rooms = $roomname->name."_".$roomname->id."|".$rooms;
                                                            }
                                                        }
                                                        $roomsnafars=$rak['info'][0]['reserve']->room_det;
                                                        if($roomsnafars){
                                                            $roomsn="";
                                                            foreach ($roomsnafars as $roomsnafar){
                                                                $roomsn = $roomsnafar->nafar."|".$roomsn;
                                                            }
                                                        }
                                                        $tarikh_v=$rak['info'][0]['tarikh_mehman'][0];
                                                        $tarikh_v =(isset($tarikh_v))?$tarikh_v:"";
                                                        $tarikh_kh=$rak['info'][0]['tarikh_mehman'][1];
                                                        $tarikh_kh =(isset($tarikh_kh))?$tarikh_kh:"";
                                                        $mehmans=$rak['info'][0]['mehman'];
                                                        if($mehmans){
                                                            $mmfname="";
                                                            $mmlname="";
                                                            $mmmeliat="";
                                                            $mmnesbat="";
                                                            foreach ($mehmans as $mehman){
                                                                $mmfname=$mehman->fname."|".$mmfname;
                                                                $mmlname=$mehman->lname."|".$mmlname;
                                                                $mmmeliat=$mehman->melliat."|".$mmmeliat;
                                                                $mmnesbat=$mehman->nesbat."|".$mmnesbat;
                                                            }
                                                        }
                                                    }         
                                                    else{
                                                        $ajans ="";
                                                        $reserve_id ="";
                                                        $rname ="";
                                                        $rnafar ="";
                                                        $hoid ="";
                                                        $hotelPrice ="";
                                                        $tozih ="";
                                                        $rooms="";
                                                        $tarikh_v ="";
                                                        $tarikh_kh ="";
                                                        $mmfname="";
                                                        $mmlname="";
                                                        $mmmeliat="";
                                                        $mmnesbat="";
																												$service_ezafe="";
                                                    }
																										$info = $rak['info'];
																									$is_khorooj = FALSE;
																									$is_paziresh = FALSE;
																										foreach($info as $inf){
																											if($inf['is_khorooj']===TRUE){
																												$is_khorooj = TRUE;
																											}
																											if($inf['is_paziresh']===TRUE){
																												$is_paziresh = TRUE;
																											}
																										}
																										foreach($info as $inf_i=> $inf){
																											$info[$inf_i]['is_khorooj']= $is_khorooj;
																											$info[$inf_i]['is_paziresh']= $is_paziresh;
																										}
																									$rak['info'] = $info;
                                                    if($rak['vaziat']==0){
                                                        $vaziat=0;
                                                        
                                                        echo "<div class='roomtype".$rak['room_typ_id']." full item";if($rak['info'][0]['is_khorooj']==true) {echo" exit";} if($rak['info'][0]['is_paziresh']==true) {echo" enter";} echo"'>
                                                <i style='position:absolute;z-index:1;color:white;right:5px;top:2px;'";if($rak['info'][0]['is_khorooj']==true) {echo" class='fa fa-plane'";}if($rak['info'][0]['is_paziresh']==true) {echo" class='fa fa-sign-in'";}if($rak['show_prob']!=-1) {echo" class='fa fa-wrench'";} echo"></i>
												<div class='filter-content'>
                                                    <a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','$mmfname','$mmlname','$mmmeliat','$mmnesbat','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>
													   <button style='font-size:11px;padding:11px;margin:2px;' class='btn mdb-red btn-lg'>$rak[name]</button>
												   </a>
                                                    
												</div>
											</div>";
                                                    }
                                                    if($rak['vaziat']==1){
                                                        $vaziat=1;
                                                        echo "<div class='roomtype".$rak['room_typ_id']." dirty item";if($rak['info'][0]['is_khorooj']==true) {echo" exit";}if($rak['info'][0]['is_paziresh']==true) {echo" enter";} echo"'>
                                                <i style='position:absolute;z-index:1;color:white;right:5px;top:2px;'";if($rak['info'][0]['is_khorooj']==true) {echo" class='fa fa-plane'";}if($rak['info'][0]['is_paziresh']==true) {echo" class='fa fa-sign-in'";}if($rak['show_prob']!=-1) {echo" class='fa fa-wrench'";} echo"></i>
												<div class='filter-content'>
                                                    <!--<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','$mmfname','$mmlname','$mmmeliat','$mmnesbat','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>-->
																										<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','','','','','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>
													   <button style='font-size:11px;padding:11px;margin:2px;' class='btn mdb-yellow btn-lg'>$rak[name]</button>
												   </a>
                                                    
												</div>
											</div>";
                                                    }
                                                    if($rak['vaziat']==2){
                                                        $vaziat=2;
                                                        echo "<div class='roomtype".$rak['room_typ_id']." free item";if($rak['info'][0]['is_khorooj']==true) {echo" exit";}if($rak['info'][0]['is_paziresh']==true) {echo" enter";} echo"'>
                                                <i style='position:absolute;z-index:1;color:white;right:5px;top:2px;'";if($rak['info'][0]['is_khorooj']==true) {echo" class='fa fa-plane'";}if($rak['info'][0]['is_paziresh']==true) {echo" class='fa fa-sign-in'";}if($rak['show_prob']!=-1) {echo" class='fa fa-wrench'";} echo"></i>
												<div class='filter-content'>
                                                    <!--<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','$mmfname','$mmlname','$mmmeliat','$mmnesbat','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>-->
																										<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','','','','','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>
													   <button style='font-size:11px;padding:11px;margin:2px;' class='btn mdb-green btn-lg'>$rak[name]</button>
												   </a>
                                                    
												</div>
											</div>";
                                                    }
                                                    if($rak['vaziat']==3){
                                                        $vaziat=3;
                                                        echo "<div class='roomtype".$rak['room_typ_id']." temporary item";if($rak['info'][0]['is_khorooj']==true) {echo" exit";}if($rak['info'][0]['is_paziresh']==true) {echo" enter";} echo"'>
                                                <i style='position:absolute;z-index:1;color:white;right:5px;top:2px;'";if($rak['info'][0]['is_khorooj']==true) {echo" class='fa fa-plane'";}if($rak['info'][0]['is_paziresh']==true) {echo" class='fa fa-sign-in'";}if($rak['show_prob']!=-1){ echo" class='fa fa-wrench'";}echo"></i>
												<div class='filter-content'>
                                                    <!--<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','$mmfname','$mmlname','$mmmeliat','$mmnesbat','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>-->
																										<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','','','','','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>
													   <button style='font-size:11px;padding:11px;margin:2px;' class='btn mdb-orange btn-lg'>$rak[name]</button>
												   </a>
                                                    
												</div>
											</div>";
                                                    }
                                                    if($rak['vaziat']==4){
                                                        $vaziat=4;
                                                        echo "<div class='roomtype".$rak['room_typ_id']." service item";if($rak['info'][0]['is_khorooj']==true) {echo" exit";}if($rak['info'][0]['is_paziresh']==true) {echo" enter";} echo"'>
                                                <i style='position:absolute;z-index:1;color:white;right:5px;top:2px;'";if($rak['info'][0]['is_khorooj']==true) {echo" class='fa fa-plane'";}if($rak['info'][0]['is_paziresh']==true) {echo" class='fa fa-sign-in'";}if($rak['show_prob']!=-1){ echo" class='fa fa-wrench'";} echo"></i>
												<div class='filter-content'>
                                                    <!--<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','$mmfname','$mmlname','$mmmeliat','$mmnesbat','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>-->
																										<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','','','','','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>
													   <button style='font-size:11px;padding:11px;margin:2px;' class='btn mdb-brown btn-lg'>$rak[name]</button>
												   </a>
                                                    
												</div>
											</div>";
                                                    }
                                                    if($rak['vaziat']==5){
                                                        $vaziat=5;
                                                        echo "<div class='roomtype".$rak['room_typ_id']." poshtiban item";if($rak['info'][0]['is_khorooj']==true) {echo" exit";}if($rak['info'][0]['is_paziresh']==true) {echo" enter";} echo"'>
                                                <i style='position:absolute;z-index:1;color:white;right:5px;top:2px;'";if($rak['info'][0]['is_khorooj']==true) {echo" class='fa fa-plane'";}if($rak['info'][0]['is_paziresh']==true) {echo" class='fa fa-sign-in'";}if($rak['show_prob']!=-1){ echo" class='fa fa-wrench'";}echo"></i>
												<div class='filter-content'>
                                                    <!--<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','$mmfname','$mmlname','$mmmeliat','$mmnesbat','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>-->
																										<a id='$rak[id]' onclick=\"rakModal('$rak[id]','$rak[name]','$ajans','$reserve_id','$rname','$hoid','$rnafar','$rooms','$tarikh_v','$tarikh_kh','$hotelPrice','$tozih','','','','','$vaziat','$hotel_id','$room_typ','$roomsn','$service_ezafe')\" data-toggle='modal' title='$rak[room_typ]";if($rak['show_prob']!=-1){echo "+ $rak[show_prob]";} echo"'>
													   <button style='font-size:11px;padding:11px;margin:2px;' class='btn mdb-blue btn-lg'>$rak[name]</button>
												   </a>
                                                    
												</div>
											</div>";
                                                    }
                                                }
																							echo "</div>";
                                            }

                                           /* for ($i=1;count($out);$i++)
                                            {
                                                
                                               for ($j=0;count($out[$i]);$j++)
                                                {
                                                    echo "
                                                    
                                                    <div class='roomtype".$out[$i][$j]['room_typ_id']." item'>
                                                <i style='position:absolute;z-index:1;color:white;right:5px;top:2px;' class='fa fa-wrench'></i>
												<div class='filter-content'>
                                                    <a href='#rak-modal' data-toggle='modal' title='$out[$i][$j][room_typ]'>
													   <button style='font-size:11px;padding:11px;margin:2px;' class='btn mdb-teal btn-lg'>$out[$i][$j][name]</button>
												   </a>
                                                    
												</div>
											</div>
                                                    ";
                                                }
                                            foreach ($out[$i] as $raks)
                                                {
                                                    echo $raks['name'];
                                                    echo "
                                                        
                                                        <div class='roomtype".$raks['room_typ_id']." item'>
                                                <i style='position:absolute;z-index:1;color:white;right:5px;top:2px;' class='fa fa-wrench'></i>
												<div class='filter-content'>
                                                    <a href='#rak-modal' data-toggle='modal' title='$raks[room_typ]'>
													   <button style='font-size:11px;padding:11px;margin:2px;' class='btn mdb-teal btn-lg'>$raks[name]</button>
												   </a>
                                                    
												</div>
											</div>
                                                    
                                                    ";
                                                }
                                            }*/

                                           
                                            
                                            ?>
                                <!--
											<div class="category_1 category_222 item">
                                                <i style="position:absolute;z-index:1;color:white;right:5px;top:2px;" class="fa fa-wrench"></i>
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal" title="سوئیت 2 تخت">
													   <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">200</button>
												   </a>
                                                    
												</div>
											</div>
                                            <div class="category_4 category_222 item">
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-yellow btn-lg">200</button>
												    </a>
												</div>
											</div>
                                            <div class="category_1 category_222 item">
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">200</button>
												</a>
												</div>
											</div>
                                            <div class="category_2 category_222 category_11 item">
                                                <i style="position:absolute;z-index:1;color:white;right:5px;top:2px;" class="fa fa-plane"></i>
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-red btn-lg">200</button>
												</a>
												</div>
											</div>
                                            
                                            <div class="category_3 category_111 item">
                                                <i style="position:absolute;z-index:1;color:white;right:5px;top:2px;" class="fa fa-sign-in"></i>
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_1 item">
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												
												</div>
											</div>
                                            <div class="category_1 item">
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_4 item">
												<div class="filter-content">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-yellow btn-lg">101</button>
												
												</div>
											</div>
                                            <div class="category_1 item">
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_5 item">
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-brown btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_6 item">
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-blue btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_2 item">
												<div class="filter-content">
                                                    <a href="#rak-modal" data-toggle="modal">
													<button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-red btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_7 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-orange btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_2 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-red btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_4 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-yellow btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            
                                            
                                              
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
									
                                           
                                            
											<div class="category_1 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_4 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-yellow btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_1 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_2 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-red btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_1 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_1 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_4 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-yellow btn-lg">101</button>
												</a>
												</div>
											</div>
                                            
                                            <div class="category_1 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_1 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_2 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-red btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_1 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-teal btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_2 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-red btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_4 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-yellow btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            
                                            
                                              
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>
                                            <div class="category_3 item">
												<div class="filter-content">
													<a href="#rak-modal" data-toggle="modal">
                                                    <button style="font-size:11px;padding:11px;margin:2px;" class="btn mdb-green btn-lg">101</button>
												</a>
												</div>
											</div>-->
									
<!--                                             </div> -->
                                        
                                    	<?php //echo count($out[1][1]);//$out[1][1]['id']; ?>
                                        <?php //echo $out[1][1]['name']; ?>
                                        <?php //echo $out[1][1]['tabaghe']; ?>
                                        <?php// echo $out[1][1]['vaziat']; ?>
                                        <?php// echo $out[1][1]['room_typ_id']; ?>
                                        <?php// echo $out[1][1]['show_prob']; ?>
                                        <?php// echo $out[1][1]['show_req']; ?>
                                        <?php// echo $out[1][1]['room_typ']; ?>
                                        <?php //echo $out[1][1]['state']; ?>
                                        <?php// echo $out[1][1]['info']; ?>
                                       
											<!--
                                            <div class="col-sm-4 col-md-3 col-lg-2 category_4 item">
												<div class="filter-content">
													<img src="<?php echo $root ?>img/gallery/2.jpg" alt="" class="img-responsive" />
													<div class="hover-content">
														<h4>Image Title</h4>
														<a class="btn btn-success hover-link">
															<i class="fa fa-edit fa-1x"></i>
														</a>
														<a class="btn btn-warning hover-link colorbox-button" href="img/gallery/2.jpg" title="Image Title">
															<i class="fa fa-search-plus fa-1x"></i>
														</a>
													</div>
												</div>
											</div>-->
										
									</div>
								</div>
								<!-- /BOX -->
							</div>
						</div>
						<!-- /GALLERY -->
               
			</div>
        </div>
    </section>
	<!--/PAGE -->
     
    	<!-- Modal : rak modal -->
    <div class="modal fade" id="rak-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div id="rk" class="modal-dialog modal-lg">
				  
              </div>
    </div>
			<!--/Modal : rak modal-->
    <div class="modal fade" id="deleteM">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">خروج مهمان</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="res_id1" />
                        <input type="hidden" value="" name="rm_id1" />
                        <input type="hidden" value="" name="kh1" />
                        آیا از خروج مهمان مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalM()" type="button" class="btn btn-danger" data-dismiss="modal">خروج</button>
                </div>
            
        </div>
    </div>
</div>
    
        <!-- Modal edit (Long Modal)-->
    <div class="modal fade" id="edit-nafar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش تعداد نفرات</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                    <div class="form-group" id="editnafar">
                        
                        </div>
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="finalNafarEdit()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <!-- edit nafar modal -->
    
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
    <script src="<?php echo $root ?>js/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-datepicker.fa.js"></script>
	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
    <script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-datepicker.fa.js"></script>
    
    <script>
	
        var i=0;
        var SSmsg = null;
	
        jQuery(document).ready(function() {
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
                $(".datepicker1").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                    
                });
                $(".datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
                $(".datepicker2").datepicker({
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
        
        function rakModal(rakId,rakName,rakAjans,reserveId,reserverName,hoid,nafar,rooms,tarikh_v,tarikh_kh,hotelPrice,tozih,mfname,mlname,mmeliat,mnesbat,vaziat,hotel_id,room_typ,roomsn,service_ezafe){
            StartLoading();
            var id=rakId;
            var name=rakName;
            var ajans=rakAjans;
            var reserve_id=reserveId;
            var rname=reserverName;
            var hoid=hoid;
            var nafar=nafar;
            var rooms=rooms;
            var tarikh_v=tarikh_v;
            var tarikh_kh=tarikh_kh;
            var hotelPrice=hotelPrice;
            var tozih=tozih;
            var mfname=mfname;
            var mlname=mlname;
            var mmeliat=mmeliat;
            var mnesbat=mnesbat;
            var vaziat=vaziat;
            var hotel_id=hotel_id;
            var room_typ=room_typ;
            var roomsn=roomsn;
            $.post("gaantinfo.php",{oid:id,oname:name,oajans:ajans,oreserve_id:reserve_id,orname:rname,ohoid:hoid,onafar:nafar,orooms:rooms,otarikh_v:tarikh_v,otarikh_kh:tarikh_kh,ohotelPrice:hotelPrice,otozih:tozih,omfname:mfname,omlname:mlname,ommeliat:mmeliat,omnesbat:mnesbat,ovaziat:vaziat,ohotel_id:hotel_id,oroom_typ:room_typ,oroomsn:roomsn,service_ezafe:service_ezafe},function(data){
                StopLoading();
               //alert(data);
                $("#rk").html(data);
                $('#rak-modal').modal('show');             
            });
        }
        
        function editNafar(resId,rooms1,roomsn){
            StartLoading();
            $("#editnafar").empty();
            var reserve_id=resId;
            var rooms1 = rooms1;
            $("#editnafar").append('<input name="roomthis" id="'+reserve_id+'" type="hidden" value="'+rooms1+'" />');
            var rooms2 = rooms1.split("|");
            //alert(rooms2.length);
            var roomsn = roomsn;
            var roomsn2 = roomsn.split("|");
            var i;
            var count = (rooms2.length)-1;
            for(i=0;i<count;i++){
                var rooms20 = rooms2[i].split("_");
                $("#editnafar").append('<div class="col-md-3" style="margin-bottom:5px;"><label class="col-md-4 control-label">اتاق:'+rooms20[0]+' </label><div class="col-md-8"><input type="text" id="'+rooms20[1]+'" name="'+rooms20[0]+'" value="'+roomsn2[i]+'" class="form-control"></div></div>');
            }
            StopLoading();
             
            $('#edit-nafar').modal('show');                
        }
        function finalNafarEdit(){
            var roomthis = $("input[name='roomthis']").val();
            var reserve_id = $("input[name='roomthis']").attr('id');
            var roomsthis = roomthis.split("|");
            var count = (roomsthis.length)-1;
            var j;
            var getinput=[];
            for(j=0;j<count;j++){
                var roomsthis2 = roomsthis[j].split("_");
                getinput[j] = $("input[name='"+roomsthis2[0]+"']").val();
            }
            $.post("gaantinfo_nafar.php",{oroomthis:roomthis,ogetinput:getinput,oreserve_id:reserve_id},function(data){
                StopLoading();
                if(data=="0"){
                    alert("اشکال در تغییر نفرات");
                }
                else if(data=="1"){
                    alert("تغییر نفرات با موفقیت انجام شد");
                }
                else 
                    alert(data);
                
            
        });
        }
        function sabt(id)
		{
            StartLoading();
			//var vaziat = $("#vaziat").val();
			var vaziat = id;
			var tarikh;
			var tozih;
			var room_id= $("#rroom_id").val();
			if (id==4)
			{
				tarikh= $("input[name='tarikh_ta']").is(":visible") ? $("input[name='tarikh_ta']").val():'';
				tozih = $("#tozih_ta").val();
                
			}
			else if(id==5)
			{
				tarikh= $("input[name='tarikh_po']").is(":visible") ? $("input[name='tarikh_po']").val():'';
				tozih = $("#tozih_po").val();
			}
			else
			{
				tarikh= '0000-00-00 00:00:00';
				tozih = '';
			}
			//$("#khoon").html('<img src="../img/status_fb.gif" >');
// 			console.log("gaantinfo.php?ch_room_id="+room_id+"&vaziat="+vaziat+"&tarikh="+tarikh+"&tozih="+tozih+"&");
			$.get("gaantinfo.php?ch_room_id="+room_id+"&vaziat="+vaziat+"&tarikh="+tarikh+"&tozih="+tozih+"&",function(result){
// 				console.log(result);
				
				$("#khoon").html('');
                StopLoading();
				if(result=="ok")
				{
					alert('تغییر با موفقیت انجام شد');
					window.location = window.location;
					window.opener.location = window.opener.location;
				}
				else
					//alert('خطا در اعمال تغییرات');
					alert(result);
				
			});
		}
        function statusCH(id)
		{
			var vaziat = id;
			if(vaziat=="4")
			{
				$("#div_tarikh_ta").show('slow');
				$("#div_tarikh_po").hide('slow');
				$("#emal_5").hide('slow');
				$("#emal_4").show('slow');				
			}
			else if(vaziat=="5")
            {
                $("#div_tarikh_po").show('slow');
                $("#div_tarikh_ta").hide('slow');
                $("#emal_5").hide('slow');
                $("#emal_5").show('slow');				
            } 
			else
                $("#div_tarikh_ta").hide('slow');
				//$("#div_tarikh_po").hide('slow');
		}
        function sabt_dec()
		{
			alert('تغییر حالت اتاق به حالت درخواستی امکان پذیر نمی باشد');
		}
        function changeVaz(){
            var vaz = document.getElementById("vaz");
            var vazopt = vaz.options[vaz.selectedIndex].value;
            var tmpFunc = new Function(vazopt);
					console.log(tmpFunc);
            tmpFunc();
        }
        function khoroojM(reserve_id,room_id,kh){
            StartLoading();
            $("input[name='res_id1']").val(reserve_id);
            $("input[name='rm_id1']").val(room_id);
            $("input[name='kh1']").val(kh);
            StopLoading();
            $('#deleteM').modal('show');  
        }
        function deleteFinalM(){
            StartLoading();
            var res_id1 = $("input[name='res_id1']").val();
            var rm_id1 = $("input[name='rm_id1']").val();
            var kh1 = $("input[name='kh1']").val();
            
           $.post("MehmanKhoroojAjax.php",{res_id1:res_id1,rm_id1:rm_id1,kh1:kh1},function(data){
               StopLoading();
               if(data=="1"){
                   alert("خروج با موفقیت انجام شد");
                   location.reload();
               }
               else
                   alert("خطا در خروج");
                                          
           });
            
        }

        function StartLoading(){
            $("#loading").show();
        }
        
        function StopLoading(){
            $("#loading").hide();
        }
				var rooms = <?php echo json_encode($rak_data); ?>;
	</script>
    
	<?php include_once "footermodul.php"; ?>
	<!--/FOOTER -->

</body> 
</html>