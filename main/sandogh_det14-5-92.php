<?php	session_start();
	unset($_SESSION['factor_shomare']);
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
               die(lang_fa_class::access_deny);
	function loadSandogh($sandogh_id)
	{
		$user_id = (int)$_SESSION['user_id'];
		$se = security_class::auth($user_id);
		$out = '<select id="sandogh_id" name="sandogh_id" class="inp" onchange="post_back();" ><option value="-1"></option>'."\n";
		$tmp = user_class::loadSondogh($user_id,$se->detailAuth('all'));
		for($i=0;$i<count($tmp);$i++)
		{
			$sandogh = new sandogh_class($tmp[$i]);
			$hotel = new hotel_class($sandogh->hotel_id);
			$out .="<option ".(($tmp[$i]==$sandogh_id)?'selected="selected"':'')." value='".$tmp[$i]."'>".$sandogh->name.'('.$hotel->name.')'."</option>\n";
		}
		$out .='</select>';
		return $out;
	}
	function loadRooms($sandogh_id,$room_id)
	{
		$sandogh = new sandogh_class($sandogh_id);
		$out = '<select id="room_id" name="room_id" class="inp" onchange="loadReserves();" ><option value="-1"></option>'."\n";
		$tmp = hotel_class::getRooms($sandogh->hotel_id,TRUE);
		for($i=0;$i<count($tmp);$i++)
			$out .="<option ".(($tmp[$i]['id']==$room_id)?'selected="selected"':'')." value='".$tmp[$i]['id']."'>".$tmp[$i]['name']."</option>\n";
		$out .='</select>';
		return $out;
	}
	$out ='';
	$sandogh_id = (isset($_REQUEST['sandogh_id']))?(int)$_REQUEST['sandogh_id']:-1;
	$miz = (isset($_REQUEST['miz']))?$_REQUEST['miz']:'';
	$sandogh = new sandogh_class($sandogh_id);
	$style = '';
	if(!$sandogh->can_cash)
		$style='style="display:none;"';
	$room_id = (isset($_REQUEST['room_id']))?(int)$_REQUEST['room_id']:-1;
	$reserve_nafar = '';
	if($sandogh_id>0 && $room_id>0)
	{
		$reserve_info = room_class::getReserve(date("Y-m-d"),$room_id);
		
		$reserve_nafar = '<select name="reserve_id" id="reserve_id" class="inp" >';
		for($i=0;$i<count($reserve_info);$i++)
			$reserve_nafar .="<option value='".$reserve_info[$i]['reserve_id']."' >".$reserve_info[$i]['fname'].' '.$reserve_info[$i]['lname'].' '.$reserve_info[$i]['tel'].'('.$reserve_info[$i]['reserve_id'].') '."</option>\n";
		$reserve_nafar .= '</select>';
		
		//$reserve_nafar = '<span class="msg" >'.$reserve_info[0]['fname'].' '.$reserve_info[0]['lname'].' '.$reserve_info[0]['tel'].'('.$reserve_info[0]['reserve_id'].')</span>';
		$copon = khadamat_det_class::hasCopon($sandogh_id,$reserve_info[0]['reserve_id'],TRUE);
		$display = $copon ?'none':'';
		if(count($copon)>0)
		{
			$san = new sandogh_class($sandogh_id);
			$reserve_nafar .='<select class="inp" id="sandogh_khadamat_id" name="sandogh_khadamat_id" >';
			$reserve_nafar .='<option value="-1">&nbsp;</option>';
			foreach($copon as $khadamat_det_id)
			{
				$kh_det = new khadamat_det_class($khadamat_det_id);
				$kh = new khadamat_class($kh_det->khadamat_id);
				$reserve_nafar.='<option value="'.$khadamat_det_id.'" >'.$kh->name.'</option>'."\n";
			}
			$reserve_nafar .='</select>';
		}
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
		</script>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center" style="margin:10px;padding:5px;">
			<form id="frm1" >
				<table style="width:99%;" border="1" >
					<tr>
						<th>
			انتخاب صندوق:
						<?php echo loadSandogh($sandogh_id) ;?>
						</th>
						<th>
							<input type="button" value="لیست" onclick ="listGhaza();" />
							<input type="button" value="گزارش" onclick ="window.open('sandogh_factors.php?sandogh_id=<?php echo $sandogh_id; ?>&');" />
						</th>
					</tr>
					<tr valign="top" >
						<td>
							<input onclick="loadButtons();" checked="checked" type="radio" name="get_type" id="get_type1" value="1">
							مهمان:
						</td>
						<td>
						
							<?php
								$s = new sandogh_class($sandogh_id);
								if(strpos($s->name,'رستوران')!==FALSE || strpos($s->name,'کافی')!==FALSE)
									echo 'میز : <input type="text" class="inp" style="width:30px;" id="miz" name="miz" value="'.$miz.'" />';
								echo loadRooms($sandogh_id,$room_id);
								echo $reserve_nafar;	
							 ?>
						</td>
					</tr>
					<tr <?php echo $style; ?> >
						<td colspan="2" >
							<input onclick="loadButtons();" type="radio" name="get_type" id="get_type2" value="-1">
						نقدی:
						</td>
					</tr>
					<tr id="factor" style="display:none;"  >
						<td colspan="2" align="center" >
							<input type="button" value="صدور فاکتور" onclick="setFactor(1);" style="font-family:tahoma;font-size:22px;height:100px;width:250px;" >
							<input id="resid" type="button" value="صدور رسید" onclick="setFactor(-1)" style="font-family:tahoma;font-size:22px;height:100px;width:250px;display:<?php echo $display; ?>" >
							<input type="hidden" name="isFactor" id="isFactor" >
							<div id="msg" ></div>
						</td>
					</tr>
				</table>
			</form>
			<?php	echo $out;?>
		</div>
		<script language="javascript" >
		loadButtons();
		</script>
	</body>

</html>
