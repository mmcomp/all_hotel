<?php	session_start();
	unset($_SESSION['factor_shomare']);
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
               die(lang_fa_class::access_deny);
	isset($_REQUEST['hotel_id'])? $h_id=$_REQUEST['hotel_id']:$h_id=-1; 
	//echo $h_id;
	function loadSandogh($sandogh_id)
	{	
		global $h_id;
		$user_id = (int)$_SESSION['user_id'];
		$se = security_class::auth($user_id);
		$out = '<select   id="sandogh_id"  name="sandogh_id" class="inp long_width" onchange="post_back();" ><option value="-1" ></option>'."\n";
		$tmp = user_class::loadSondogh($user_id,$se->detailAuth('all'),$h_id);
		for($i=0;$i<count($tmp);$i++)
		{
			$sandogh = new sandogh_class($tmp[$i]);
			$hotel = new hotel_class($sandogh->hotel_id);
			$out .="<option ".(($tmp[$i]==$sandogh_id)?'selected="selected"':'')." value='".$tmp[$i]."'>".$sandogh->name.'('.$hotel->name.')'."</option>\n";
		}
		$out .='</select>';
		return $out;
	}
	function loadRooms($sandogh_id,$room_id,$h_id)
	{
		$sandogh = new sandogh_class($sandogh_id);
		$out = '<select id="room_id" name="room_id" class="inp short_width" onchange="loadReserves();" ><option value="-1"></option>'."\n";
		$tmp = hotel_class::getRooms($h_id,TRUE);
		for($i=0;$i<count($tmp);$i++)
			$out .="<option ".(($tmp[$i]['id']==$room_id)?'selected="selected"':'')." value='".$tmp[$i]['id']."'>".$tmp[$i]['name']."</option>\n";
		$out .='</select>';
		return $out;
	}
	$mod1 = (isset($_REQUEST['mod1']))?(int)$_REQUEST['mod1']:-1;
	$sandogh_id = (isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1;
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
			$reserve_nafar = '<select name="reserve_id" id="reserve_id" class="inp shfont_lowidth" >';
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
				$reserve_nafar .='<select class="inp short_width" id="sandogh_khadamat_id" name="sandogh_khadamat_id" >';
				$reserve_nafar .='<option value="-1">&nbsp;</option>';
				foreach($copon as $khadamat_det_id)
				{
					$s_kh = '';
					$kh_det = new khadamat_det_class($khadamat_det_id);
					$kh = new khadamat_class($kh_det->khadamat_id);
					$today_time = date("H");
					if (($today_time>=7)&&($today_time<=10))
					{
						if ($kh->name == "صبحانه")
							$s_kh="selected='selected'";	
					}
					else if (($today_time>=13)&&($today_time<17))
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
						$meal= "وعده غذایی برای این ساعت در نظر گرفته نشده است";
					$reserve_nafar.='<option '.$s_kh.' value="'.$khadamat_det_id.'" >'.$kh->name.'</option>'."\n";
				
				}
				$reserve_nafar .='</select>';
				//if (strlen($meal)>0)
					//echo $meal;
			}
		}
		else
			$str_khadamat = "برای این میهمان خدماتی ثبت نشده است";
	}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<title>
		فرانت آفیس
		</title>
		<script language="javascript" >
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
						if(document.getElementById('reserve_id') && document.getElementById('reserve_id').options.length>0)
							document.getElementById('factor').style.display = '';
					}
				}
			}
			function setFactor(inp)
			{
				//wopen('sandogh_factor.php?isFactor='+inp,'',600,400);
				document.getElementById('isFactor').value = inp;
				document.getElementById('frm1').action = 'sandogh_factor.php';
				document.getElementById('frm1').submit();
			}
			function listGhaza()
			{
				var sandogh_id = parseInt($("#sandogh_id").val(),10);
				if(sandogh_id > 0)
				{
					window.open("ghazaReport.php?sandogh_id="+sandogh_id+"&");
				}
			}
			function showClock(tim)
			{
				document.getElementById('tim').innerHTML = tim;
				setTimeout("mehrdad_ajaxFunction(showClock);",1000);
			}
		</script>
		<style>
			.sandogh{

				font:3em arial,tahoma,sans-serif;
			}
			.long_width{

				font:1em arial,tahoma,sans-serif;
				width:10em;
				height:2em;
			}
			.radio{

				width:4em;
				height:4em;
			}
			.short_width{

				font:25px arial,tahoma,sans-serif;
				width:3em;
				height:2em;
			}
			.shfont_lowidth{

				font:25px arial,tahoma,sans-serif;
				width:20em;
				height:2em;
			}
		</style>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<?php if ($mod1==3)
			{
		?>
			<div align="center" style="margin:10px;padding:5px;">
			<form id="frm1" >
				
				<input type="hidden" name="hotel_id"  value="<?php echo $h_id;?>">
				<table style="width:100%" border="0" height='500px' >
					<tr class='sandogh'>
						<td align='center' colspan='2'>انتخاب صندوق:</td>
						<td width="650" ><?php echo loadSandogh($sandogh_id) ;?></td>
						<td>
							<?php
								$today_time = date("H");
								if (($today_time>=7)&&($today_time<=10))
									echo "صبحانه";
								elseif (($today_time>=13)&&($today_time<=17))
									echo "ناهار";
								elseif (($today_time>=17)&&($today_time<=24))
									echo "شام";
								else
									//echo "وعده غذایی برای این ساعت در نظر گرفته نشده است";
							?>
						</td>
					</tr>
					<tr class="sandogh">
						<td  align='left'>
		<input onclick="loadButtons();" class="radio" checked="checked" type="radio" name="get_type" id="get_type1" value="1">
						</td>
						<td>مهمان: </td>
						<td colspan='2'>
											
							<?php
								$s = new sandogh_class($sandogh_id);
								if(strpos($s->name,'رستوران')!==FALSE || strpos($s->name,'کافی')!==FALSE)
								{
								$str="<select class='inp short_width'  style='width:50px'>";
								for($i=1;$i<=40;$i++)
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
					<tr <?php echo $style; ?> >
						<td colspan="2" >
							<input onclick="loadButtons();" type="hidden" name="get_type" id="get_type2" value="-1">
						</td>
					</tr>
					<tr id="factor" style="display:none;"  >
						<td colspan="10" align="center" >
							<input type="button" value="صدور فاکتور" onclick="setFactor(1);" style="font-family:tahoma;font-size:22px;height:150px;width:250px;" >
							<input id="resid" type="button" value="صدور رسید" onclick="setFactor(-1)" style="font-family:tahoma;font-size:22px;height:150px;width:250px;display:<?php echo $display; ?>" >
							<input type="hidden" name="mod1" id="mod1" value="<?php echo $mod1;?>" >
							<input type="hidden" name="isFactor" id="isFactor" >
							<div id="msg" ></div>
						</td>
					</tr>
				</table>
			</form>
			<?php	echo $out;?>
		</div>
		<?php
			}
			else
			{
		?>
		<div align="center" style="margin:10px;padding:5px;">
			<form id="frm1" >
				
				<input type="hidden" name="hotel_id"  value="<?php echo $h_id;?>">
				<table style="width:100%;" border="0" height="500" >
					<tr class='sandogh'>
						<td align='center' colspan='2'>انتخاب صندوق:</td>
						<td width='600'>
						<?php echo loadSandogh($sandogh_id) ;?>
						</td>
						<td >
	<input type="button"  class='short_width' value="لیست" onclick ="listGhaza();" />
	<input type="button"  class='short_width' value="گزارش" onclick ="window.open('sandogh_factors.php?sandogh_id=<?php echo $sandogh_id; ?>&');" />
						</td>
					</tr>
					<tr class='sandogh'>
						<td  align='left'>
	<input onclick="loadButtons();" class='radio' checked="checked" type="radio" name="get_type" id="get_type1" value="1">
						</td>
						<td >مهمان:</td>
						<td >
						
							<?php
								$s = new sandogh_class($sandogh_id);
								if(strpos($s->name,'رستوران')!==FALSE || strpos($s->name,'کافی')!==FALSE)
								{
								$str="<select class='inp short_width'  style='width:50px'>";
								for($i=1;$i<=40;$i++)
								{
								$str.= "<option value=$i>$i";
								$str.="</option>";
								}
								$str.= "</select>";
								echo $str;
								}
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
							<input type="button" value="صدور فاکتور" onclick="setFactor(1);" style="font-family:tahoma;font-size:22px;height:150px;width:250px;" >
							<input id="resid" type="button" value="صدور رسید" onclick="setFactor(-1)" style="font-family:tahoma;font-size:22px;height:150px;width:250px;display:<?php echo $display; ?>" >
							<input type="hidden" name="mod1" id="mod1" value="<?php echo $mod1;?>" >
							<input type="hidden" name="isFactor" id="isFactor" >
							<div id="msg" ></div>
						</td>
					</tr>
				</table>
			</form>
			<?php	echo $out;?>
		</div>
		<?php } ?>
		<script language="javascript" >
		loadButtons();
		</script>
	</body>

</html>
