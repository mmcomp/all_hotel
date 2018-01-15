<?php
session_start();
	unset($_SESSION['factor_shomare']);
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
               die(lang_fa_class::access_deny);
	$h_id = isset($_REQUEST['hotel_id'])? $_REQUEST['hotel_id']:-1; 
	if($h_id<=0){
		$hlist = daftar_class::hotelList($_SESSION['daftar_id']);
		$h_id = $hlist[0];
	}
	//echo $h_id;
	function loadSandogh($sandogh_id)
	{
		global $h_id;
		$user_id = (int)$_SESSION['user_id'];
		$query = "select `sandogh`.`id`,`sandogh`.`name` `sname`,`hotel`.`name` `hname` from `sandogh` left join `sandogh_user` on (`sandogh`.`id`=`sandogh_id`) left join `hotel` on (`hotel`.`id`=`hotel_id`) where `user_id` = $user_id";
		mysql_class::ex_sql($query,$q);
		$out = '<select   id="sandogh_id"  name="sandogh_id" class="form-control long_width" onchange="post_back();" ><option value="-1" ></option>'."\n";
		while($r = mysql_fetch_assoc($q)){
			$out .="<option ".(($r['id']==$sandogh_id)?'selected="selected"':'')." value='".$r['id']."'>".$r['sname'].'('.$r['hname'].')'."</option>\n";
		}
// 		$se = security_class::auth($user_id);
// 		$out = '<select   id="sandogh_id"  name="sandogh_id" class="form-control long_width" onchange="post_back();" ><option value="-1" ></option>'."\n";
// 		$tmp = user_class::loadSondogh($user_id,$se->detailAuth('all'),$h_id);
// 		for($i=0;$i<count($tmp);$i++)
// 		{
// 			$sandogh = new sandogh_class($tmp[$i]);
// 			$hotel = new hotel_class($sandogh->hotel_id);
// 			$out .="<option ".(($tmp[$i]==$sandogh_id)?'selected="selected"':'')." value='".$tmp[$i]."'>".$sandogh->name.'('.$hotel->name.')'."</option>\n";
// 		}
// 		$out .='</select>';
		return $out;
	}
	function loadRooms($sandogh_id,$room_id,$h_id)
	{
		$sandogh = new sandogh_class($sandogh_id);
		$out = '<select id="room_id" name="room_id" class="form-control inp col-md-4" onchange="loadReserves();" ><option value="-1"></option>'."";
		$tmp = hotel_class::getRooms($h_id,TRUE);
		for($i=0;$i<count($tmp);$i++)
			$out .="<option ".(($tmp[$i]['id']==$room_id)?'selected="selected"':'')." value='".$tmp[$i]['id']."'>".$tmp[$i]['name']."</option>\n";
		$out .='</select>';
		return $out;
	}
	$today = date('Y-m-d');
	$mod1 = (isset($_REQUEST['mod1']))?(int)$_REQUEST['mod1']:-1;
	$sandogh_id = (isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1;
	$khadamat_sandogh = sandogh_khadamat_class::loadKhadamatById($sandogh_id);
////list mehmanan daraye khadamat
	$tmp_list_mehman = '<select style="display:none;" id="mehman_list" name="reserve_id_1" class="form-control inp"><option value="-1"></option>'."\n";
	$tmp_list_mehman .='</select>';
	$list_mehman = '<select id="mehman_list" name="reserve_id_1" class="form-control inp"><option value="-1"></option>'."\n";
	$list_mehman .='</select>';
	mysql_class::ex_sql("select * from `khadamat` where `id`='$khadamat_sandogh'",$q);
	if ($r = mysql_fetch_array($q))
	{
		if ($r['name']=='گشت')
		{
			$list_mehman = '<select id="mehman_list" name="reserve_id" class="form-control inp"><option value="-1"></option>'."\n";
			mysql_class::ex_sql("select * from `khadamat_gasht` where date(`tarikh`)='$today'",$q_ga);
			while ($r_ga = mysql_fetch_array($q_ga))
			{
				$hotel_tmp = new hotel_reserve_class();
				$hotel_tmp->loadByReserve($r_ga['reserve_id']);
				$list_mehman .="<option value='".$r_ga['reserve_id']."'>".$hotel_tmp->lname."</option>\n";
			}
			$list_mehman .='</select>';
		}
		elseif ($r['name']=='ترانسفر')
		{
			$list_mehman = '<select id="mehman_list" name="reserve_id" class="form-control inp"><option value="-1"></option>'."\n";
			mysql_class::ex_sql("select * from `khadamat_transfer` where date(`timeKh`)='$today'",$q_ga);
			while ($r_ga = mysql_fetch_array($q_ga))
			{
				$hotel_tmp = new hotel_reserve_class();
				$hotel_tmp->loadByReserve($r_ga['reserve_id']);
				$list_mehman .="<option value='".$r_ga['reserve_id']."'>".$hotel_tmp->lname."</option>\n";
			}
			$list_mehman .='</select>';
		}
		elseif ($r['name']=='سینما')
		{
			$list_mehman = '<select id="mehman_list" name="reserve_id" class="form-control inp"><option value="-1"></option>'."\n";
			mysql_class::ex_sql("select * from `khadamat_cinema` where date(`tarikh`)='$today'",$q_ga);
			while ($r_ga = mysql_fetch_array($q_ga))
			{
				$hotel_tmp = new hotel_reserve_class();
				$hotel_tmp->loadByReserve($r_ga['reserve_id']);
				$list_mehman .="<option value='".$r_ga['reserve_id']."'>".$hotel_tmp->lname."</option>\n";
			}
			$list_mehman .='</select>';
		}
		elseif ($r['name']=='عکاسخانه')
		{
			$list_mehman = '<select id="mehman_list" name="reserve_id" class="form-control inp"><option value="-1"></option>'."\n";
			mysql_class::ex_sql("select * from `khadamat_akasi` where date(`tarikh`)='$today'",$q_ga);
			while ($r_ga = mysql_fetch_array($q_ga))
			{
				$hotel_tmp = new hotel_reserve_class();
				$hotel_tmp->loadByReserve($r_ga['reserve_id']);
				$list_mehman .="<option value='".$r_ga['reserve_id']."'>".$hotel_tmp->lname."</option>\n";
			}
			$list_mehman .='</select>';
		}
		else
		{
		}
	}
/////endList
/////listNaghdi
	$aztarikh = date("Y-m-d");
	$list_mehman_naghdi = '<select id="mehman_list_n" name="mehman_list_n" class="inp form-control"><option value="-1"></option>'."\n";
	//mysql_class::ex_sql("select * from `khadamat_gasht` where date(`tarikh`)='$today'",$q_ga);
	mysql_class::ex_sql("select `reserve_id`,`id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) < '$aztarikh' and date(`tatarikh`) > '$aztarikh')) group by `reserve_id`",$q_ga);
	while ($r_ga = mysql_fetch_array($q_ga))
	{
		$hotel_tmp = new hotel_reserve_class();
		$hotel_tmp->loadByReserve($r_ga['reserve_id']);
		$list_mehman_naghdi .="<option value='".$r_ga['reserve_id']."'>".$hotel_tmp->lname."</option>\n";
	}
/////endList
	$s = new sandogh_class($sandogh_id);
	if((strpos($s->name,'رستوران')!==FALSE || strpos($s->name,'کافی')!==FALSE)&&($mod1==-1))
		die("<script>window.location.href = 'resturan.php?sandogh_id=$sandogh_id&'; </script>");
	$out ='';	
	$miz = (isset($_REQUEST['miz']))?$_REQUEST['miz']:'';
	$sandogh = new sandogh_class($sandogh_id);
	$style = '';
	if(!$sandogh->can_cash)
		$style='style="display:none;"';
	$room_id = (isset($_REQUEST['room_id']))?(int)$_REQUEST['room_id']:-1;
	$reserve_nafar = '';	
	$str_khadamat = "";
	if($sandogh_id>0 && $room_id>0)
	{
		$reserve_info = room_class::getReserve(date("Y-m-d"),$room_id);
		//$info=room_det_class::showNafar($reserve_info[$i]['reserve_id'])
		$res=$reserve_info[0]['reserve_id'];
		//echo ("SELECT `nafar` FROM `room_det` WHERE `reserve_id`=$res and `room_id`=$room_id ");
		$copon = khadamat_det_class::hasCopon($sandogh_id,$reserve_info[0]['reserve_id'],TRUE);
// 		var_dump($copon);
		if (!empty($copon))
		{
			foreach($copon as $khadamat_det_id1)
			{
				$khadamat_det_id = $khadamat_det_id1;
			}
		}
		else
			$khadamat_det_id = -1;
		if ($khadamat_det_id != -1)
		{
			$kh_det = new khadamat_det_class($khadamat_det_id);
			/*if ($kh_det->id==-1)
				var_dump($kh_det);*/
			$kh = new khadamat_class($kh_det->khadamat_id);
			$meal=$kh->name;
			mysql_class::ex_sql("SELECT `nafar` FROM `room_det` WHERE `reserve_id`=$res and `room_id`=$room_id",$q1);
			mysql_class::ex_sql("SELECT `id` FROM `khadamat` WHERE `name`='$meal' ",$q2);
			if($re2=mysql_fetch_array($q2))
			{
			$khid=$re2['id'];
			mysql_class::ex_sql("SELECT `tedad` FROM `khadamat_det` WHERE `khadamat_id`=$khid and `reserve_id`=$res",$q3);
			}
			if($re3=mysql_fetch_array($q3))
			$tedad=$re3['tedad'];
			else $tedad=0;
			$reserve_nafar = '<select name="reserve_id" id="reserve_id" class="form-control inp" >';
			if($re=mysql_fetch_array($q1))
			for($i=0;$i<count($reserve_info);$i++)
			$reserve_nafar .="<option value='".$reserve_info[$i]['reserve_id']."' >".'نفرات:'.$re['nafar'].' تعداد وعده:'.$tedad.
			$reserve_info[$i]['fname'].' '.$reserve_info[$i]['lname'].' '.$reserve_info[$i]['tel'].
			'('.$reserve_info[$i]['reserve_id'].') '."</option>\n";
			$reserve_nafar .= '</select>';
	
			//$reserve_nafar = '<span class="msg" >'.$reserve_info[0]['fname'].' '.$reserve_info[0]['lname'].' '.$reserve_info[0]['tel'].'('.$reserve_info[0]['reserve_id'].')</span>';
			$copon = khadamat_det_class::hasCopon($sandogh_id,$reserve_info[0]['reserve_id'],TRUE);
			$display = $copon ?'none':'';
			if(count($copon)>0)
			{
				$san = new sandogh_class($sandogh_id);
				$reserve_nafar .='<select class="form-control inp" id="sandogh_khadamat_id" name="sandogh_khadamat_id" onchange="loadButtons();">';
				$reserve_nafar .='<option value="-1">&nbsp;</option>';
				foreach($copon as $khadamat_det_id)
				{
					$s_kh = '';
					$kh_det = new khadamat_det_class($khadamat_det_id);
					$kh = new khadamat_class($kh_det->khadamat_id);
					/*$today_time = date("H");
					if (($today_time>=7)&&($today_time<=10))
					{
						if ($kh->name == "صبحانه")
							$s_kh="selected='selected'";	
					}
					else if (($today_time>10)&&($today_time<17))
					{
						if ($kh->name == "نهار")
							$s_kh="selected='selected'";
					}
					else if (($today_time>=17)&&($today_time<=24))
					{
						if ($kh->name == "شام")
							$s_kh="selected='selected'";
					}
					else
						$meal= "وعده غذایی برای این ساعت در نظر گرفته نشده است";*/
					//$reserve_nafar.='<option '.$s_kh.' value="'.$khadamat_det_id.'" >'.$kh->name.'</option>'."\n";
					$reserve_nafar.='<option value="'.$khadamat_det_id.'" >'.$kh->name.'</option>'."\n";
				
				}
				$reserve_nafar .='</select>';
				//if (strlen($meal)>0)
					//echo $meal;
			}
		}
		else
			$str_khadamat = "برای این میهمان خدماتی ثبت نشده است";
	}	
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>فرانت آفیس</title>
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
          function post_back()
			{
				document.getElementById('frm1').submit();
			}
			function loadReserves()
			{
				document.getElementById('frm1').submit();
			}
			function loadButtons()
			{
				document.getElementById('factor').style.display = 'none';
				if(document.getElementById('sandogh_id').options[document.getElementById('sandogh_id').selectedIndex].value>0)
				{
					if(document.getElementById('get_type2').checked)
						document.getElementById('factor').style.display = '';
					else if(document.getElementById('get_type1').checked)
					{
						//alert(parseInt(document.getElementById('reserve_id').options[document.getElementById('reserve_id').selectedIndex],10)>0);
// 						console.log(document.getElementById('reserve_id'),document.getElementById('reserve_id').options.length);
						if(document.getElementById('reserve_id') && document.getElementById('reserve_id').options.length>0)
							document.getElementById('factor').style.display = '';
					}
				}
			}
			function loadButtons_1()
			{
				if(document.getElementById('get_type2').checked)
				{
					document.getElementById('factor').style.display = '';
					document.getElementById('mehman_factor').style.display = 'none';
					document.getElementById('mehman_list_n').style.display = '';
					document.getElementById('mehman_list').style.display = 'none';
				}
				else if(document.getElementById('get_type1').checked)
				{
					document.getElementById('factor').style.display = 'none';
					document.getElementById('mehman_factor').style.display = '';
					document.getElementById('mehman_list_n').style.display = 'none';
					document.getElementById('mehman_list').style.display = '';
				}
				else
					a=1;
			}
			function setFactor(inp)
			{
				//wopen('sandogh_factor.php?isFactor='+inp,'',600,400);
				document.getElementById('isFactor').value = inp;
				document.getElementById('frm1').action = 'sandogh_factor.php';
				document.getElementById('frm1').submit();
			}
			function listKhadamt()
			{
				var sandogh_id = parseInt($("#sandogh_id").val(),10);
				if(sandogh_id > 0)
				{
					window.open("report_khadamat.php?sandogh_id="+sandogh_id+"&");
				}
			}
			function gazaRep()
			{
				var sandogh_id = parseInt($("#sandogh_id").val(),10);
				if(sandogh_id > 0)
				{
					window.open("ghazaList.php?sandogh_id="+sandogh_id+"&");
				}
			}
     
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-desktop"></i>فرانت آفیس</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                         
                            <?php if ($mod1==3)
			{
		?>
			<div align="center" style="margin:10px;padding:5px;">
			<form id="frm1" >
				
				<input type="hidden" name="hotel_id"  value="<?php echo $h_id;?>">
                <div class="box border orange">
									
									<div class="box-body">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">انتخاب صندوق</th>
												<th style="text-align:right"><?php $san_id = (isset($_GET['sandogh_id']))?(int)$_GET['sandogh_id']:-1; echo loadSandogh($san_id) ;?></th>
												<th style="text-align:right"><?php
								$today_time = date("H:i:s");
								mysql_class::ex_sql("select * from `vadeGhaza` where `azSaat`<'$today_time' and `taSaat`>'$today_time'",$q_va);
								while ($r_va = mysql_fetch_array($q_va))
									echo $r_va['name'];
								/*$today_time = date("H");
								if (($today_time>=7)&&($today_time<=10))
									echo "صبحانه";
								elseif (($today_time>10)&&($today_time<=17))
									echo "ناهار";
								elseif (($today_time>=17)&&($today_time<=24))
									echo "شام";
								else
									//echo "وعده غذایی برای این ساعت در نظر گرفته نشده است";*/
							?></th>
											  </tr>
											</thead>
											<tbody>
				
					
					<tr class="sandogh">
						<td style="width:100px;"  align='left'>
		<input onclick="loadButtons();" class="radio" checked="checked" type="radio" name="get_type" id="get_type1" value="1">
						</td>
						<td style="width:240px;">مهمان: </td>
						<td colspan='2'>
											
							<?php
								$s = new sandogh_class($sandogh_id);
								if(strpos($s->name,'رستوران')!==FALSE || strpos($s->name,'کافی')!==FALSE)
								{
								$str="<select class='form-control inp col-md-2'>";
								for($i=1;$i<=50;$i++)
								{
								$str.= "<option value=$i> $i";
								$str.="</option>"; 
								}
								$str.= "</select>";
								echo $str;
								}
						
								echo loadRooms($sandogh_id,$room_id,$h_id);
								echo $reserve_nafar;
								echo ("<br>  $str_khadamat");
							 ?>
						</td>
					</tr>
					
					<tr id="factor" style="display:none;"  >
						<td colspan="10" align="center" >
                            
                            <button onclick="setFactor(1);" class="btn btn-success btn-lg"><i class="fa fa-camera"></i> صدور فاکتور</button>
                            <button style="display:<?php echo $display; ?>" id="resid" onclick="setFactor(-1);" class="btn btn-warning btn-lg"><i class="fa fa-book"></i>صدور رسید</button>
                
							<input type="hidden" name="mod1" id="mod1" value="<?php echo $mod1;?>" >
							<input type="hidden" name="isFactor" id="isFactor" >
							<div id="msg" ></div>
						</td>
					</tr>
                                                </tbody>
				</table>
                                        </div>
                    </div>
			</form>
			<?php	echo $out;?>
		</div>
		<?php
			}
			elseif ($mod1==2)
			{
		?>
		<div align="center" style="margin:10px;padding:5px;">
			<form id="frm1" >
				
				<input type="hidden" name="hotel_id"  value="<?php echo $h_id;?>">
                <div class="box border orange">
									
									<div class="box-body">
										<table class="table table-hover">
                                            <thead>
                                                <tr>
												    <th style="text-align:right">انتخاب صندوق</th>
                                                    <th style="text-align:right"><?php $san_id = (isset($_GET['sandogh_id']))?(int)$_GET['sandogh_id']:-1; echo loadSandogh($san_id) ;?></th>
                                                    <th style="text-align:right">
                                                        <!--<button style="margin:5px;" onclick ="listKhadamt();" class="btn btn-pink col-md-4" onclick="gazaRep();">لیست</button>-->
                                        <button  style="margin:5px;" class="btn btn-info col-md-4" onclick="window.open('sandogh_factors.php?sandogh_id=<?php echo $sandogh_id; ?>&');">گزارش</button>
                                                        
                                                    </th>
						
					</tr>
                                                </thead>
                                            <tbody>
					<tr class='sandogh'>
						<td  align='left'>
	<input onclick="loadButtons();" class='radio' checked="checked" type="radio" name="get_type" id="get_type1" value="1">
						</td>
						<td >مهمان:</td>
						<td >
						
							<?php
								echo loadRooms($sandogh_id,$room_id,$h_id);
								echo $reserve_nafar;	
							 ?>
						</td>
					</tr>
					<tr <?php echo $style; ?> class='sandogh' >
						<td align='left' >
				<input onclick="loadButtons();" type="radio" class='radio' name="get_type" id="get_type2" value="-1">
						</td>
						<td align='right'>نقدی:</td>
					</tr>
					<tr id="factor" style="display:none;"  >
						<td colspan="12" align="center" >
                            <button onclick="setFactor(1);" class="btn btn-success btn-lg"><i class="fa fa-list-alt"></i> صدور فاکتور</button>
                            <button style="display:<?php echo $display; ?>" id="resid" onclick="setFactor(-1);" class="btn btn-warning btn-lg"><i class="fa fa-book"></i>صدور رسید</button>
							
							<input type="hidden" name="mod1" id="mod1" value="<?php echo $mod1;?>" >
							<input type="hidden" name="isFactor" id="isFactor" >
							<div id="msg" ></div>
						</td>
					</tr>
                                                </tbody>
				</table>
                                        </div>
                    </div>
			</form>
			<?php	echo $out;?>
		</div>
		<?php } 
			else
			{?>
			<div align="center" style="margin:10px;padding:5px;">
				<form id="frm1" >
					<input type="hidden" name="hotel_id"  value="<?php echo $h_id;?>">
                     <div class="box border orange">
									
									<div class="box-body">
										<table class="table table-hover">
                                            <thead>
                                                <tr>
												    <th class="col-md-2" style="text-align:right">انتخاب صندوق</th>
                                                    <th class="col-md-4" style="text-align:right"><?php $san_id = (isset($_GET['sandogh_id']))?(int)$_GET['sandogh_id']:-1; echo loadSandogh($san_id) ;?></th>
                                                    <th class="col-md-6" style="text-align:right">
                                                         <!--<button style="margin:5px;" onclick ="listKhadamt();" class="btn btn-pink col-md-4" onclick="gazaRep();">لیست</button>-->
                                        <button  style="margin:5px;" class="btn btn-info col-md-4" onclick="window.open('sandogh_factors.php?sandogh_id=<?php echo $sandogh_id; ?>&');">گزارش</button>
                                                        
                                                    </th>
						
					</tr>
                                                </thead>
                                            <tbody>
                                                 
                                                <tr>
                                                    <td style="width:100px;"  align='left'>
		<input onclick="loadButtons_1();" class="radio" checked="checked" type="radio" name="get_type" id="get_type1" value="1">
						</td>
						<td style="width:240px;">مهمان: </td>
                                                
								<td>
									<?php echo $list_mehman;?>
								</td>
								<td>
                                    <div class="footer" id='mehman_factor' >
									
                                        <button onclick="setFactor(1);" class="btn btn-success btn-lg col-md-12"><i class="fa fa-list-alt"></i> صدور فاکتور</button>
                                        </div>
									
								</td>
							</tr>
						
						
							<tr>
								<td style="width:100px;"  align='left'>
		<input onclick="loadButtons_1();" class="radio" type="radio" name="get_type" id="get_type2" value="-1">
						</td>
						<td style="width:240px;">نقدی: </td>
								
								<td>
									<?php echo $list_mehman_naghdi;?>
								</td>
								<td>
									<div class="footer" id="factor" style="display:none;">
                                        <button style="margin:5px" onclick="setFactor(1);" class="btn btn-success btn-lg col-md-12"><i class="fa fa-camera"></i> صدور فاکتور</button>
                                        <button style="margin:5px;display:<?php echo $display; ?>" id="resid" onclick="setFactor(-1);" class="btn btn-warning btn-lg col-md-12"><i class="fa fa-camera"></i>صدور رسید</button>
										
										<input type="hidden" name="mod1" id="mod1" value="<?php echo $mod1;?>" >
										<input type="hidden" name="isFactor" id="isFactor" >
										<div id="msg" ></div>
									</div>
								</td>
							</tr>
                                                </tbody>
						
					</table>
                                        </div>
                         </div>

	
<!--





				<table style="width:95%;" border="1" height="500" >
					<tr class='sandogh'>
						<td align='center' colspan='2'>انتخاب صندوق:</td>
						<td width='600'>
						<?php echo loadSandogh($sandogh_id) ;?>
						</td>
						<td >
	<input type="button"  class='short_width' value="لیست" onclick ="listKhadamt();" />
	<input type="button"  class='short_width' value="گزارش" onclick ="window.open('sandogh_factors.php?sandogh_id=<?php echo $sandogh_id; ?>&');" />
						</td>
					</tr>
					<tr class='sandogh'>
						<td  align='left'>
	<input onclick="loadButtons_1();" class='radio' checked="checked" type="radio" name="get_type" id="get_type1" value="1">
						</td>
						<td >مهمان:</td>
						<td>
						
							<?php
								echo $list_mehman;
								//echo $reserve_nafar;	
							 ?>
						</td>
						
						<td id='mehman_factor' style="display:none">
							<input type="button" value="صدور فاکتور" onclick="setFactor(1);" style="font-family:tahoma;font-size:22px;height:150px;width:250px;" >
						</td>
					</tr>
					<tr <?php echo $style; ?> class='sandogh' >
						<td align='left' >
				<input onclick="loadButtons_1();" type="radio" class='radio' name="get_type" id="get_type2" value="-1">
						</td>
						<td align='right'>نقدی:</td>
					</tr>
					<tr id="factor" style="display:none;"  >
						<td colspan="12" align="center" >
							<input type="button" value="صدور فاکتور" onclick="setFactor(1);" style="font-family:tahoma;font-size:22px;height:150px;width:250px;" >
							<input id="resid" type="button" value="صدور رسید" onclick="setFactor(-1)" style="font-family:tahoma;font-size:22px;height:150px;width:250px;display:<?php echo $display; ?>" >
							<input type="hidden" name="mod1" id="mod1" value="<?php echo $mod1;?>" >
							<input type="hidden" name="isFactor" id="isFactor" >
							<div id="msg" ></div>
						</td>
					</tr>
				</table>-->
			</form>
			<?php	echo $out;?>
		</div>
		<?php } ?>
                          
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