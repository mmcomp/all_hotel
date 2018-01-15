<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$hotelList=daftar_class::hotelList((int)$_SESSION['daftar_id']);
		$shart = '';
		if($hotelList)
			$shart=' and ( `id`='.implode(" or `id`=",$hotelList).")";
		$out = '<select name="hotel_id" id="hotel_id" class="inp" style="width:auto;" ><option value="-1">همه</option>';
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `moeen_id` > 0 $shart order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function  loadDaftar($inp=-1)
	{
		$inp = (int)$inp;
		$out = '<select name="daftar_id" id="daftar_id" class="inp" style="width:auto;" ><option value="-1">همه</option>';
		mysql_class::ex_sql("select `id`,`name` from `daftar` where `kol_id` > 0 order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	
	
	function loadTyp($typ)
	{
		$sel1 =($typ==1)?'selected="selected"':'';
		$sel2 =($typ==2)?'selected="selected"':'';
		$out="<option value='1' $sel1 >رضایت میهمان</option>\n";
		$out.="<option value='2' $sel2 >مغایرت مبلغ رزرو</option>\n";
		return $out;
	}
	function getPic($datay,$us_id,$k)
	{
		if($k>0)
		{
			$datay =array($GLOBALS['noReply']*100/$k,$GLOBALS['excellent']*100/$k,$GLOBALS['good']*100/$k,$GLOBALS['meduim']*100/$k,$GLOBALS['low']*100/$k);
			$datax = array('1','2','3','4','5','6','7');
			$graph = new Graph(750,300,'auto');
		    	$graph->img->SetMargin(40,40,40,40);
			$graph->img->SetAntiAliasing();
			$graph->SetScale("textlin",0,100);
		    	$graph->SetShadow();
		    	$graph->title->Set(" ");
		    	$p1 = new BarPlot($datay);
		    	$abplot = new AccBarPlot(array($p1));
			$abplot->SetShadow();
			$abplot->value->Show();
		    	$p1->SetColor("blue");
		    	$p1->SetCenter();
			$graph->SetMargin(40,10,40,20);
			$graph->xaxis->SetTickSide(SIDE_BOTTOM);
			$graph->xaxis->SetTickLabels($datax);
			$graph->xaxis->SetLabelAngle(90);
		    	$graph->Add($abplot);
		    	$addr = "chart/$us_id.png";
		    	$graph->Stroke($addr);
		}
		else
			$addr = '';
		return $addr;
	}
	function loadNazar($inp)
	{
		switch ($inp)
		{
			case -2:
				$out ='پیامک‌ارسال‌نشده‌است';
				$GLOBALS['notSent']++;
				break;
			case -1:
				$out ='میهمان‌پاسخ‌نداده‌است';
				$GLOBALS['noReply']++;
				break;
			case 1:
				$out ='عالی';
				$GLOBALS['excellent']++;
				break;
			case 2:
				$out ='خوب';
				$GLOBALS['good']++;
				break;
			case 3:
				$out ='متوسط';
				$GLOBALS['meduim']++;
				break;
			case 4:
				$out ='ضعیف';
				$GLOBALS['low']++;
				break;
			default :
				$out = 'نا معلوم';
				$GLOBALS['unknown']++;
				break;
		}
		return ($out);
	}
	function loadMogh($inp,$bool)
	{
		switch ($inp)
		{
			case -2:
				$out ='پیامک‌ارسال‌نشده‌است';
				break;
			case -1:
				$out ='میهمان‌پاسخ‌نداده‌است';
				break;
			
			default :
				$out =monize($inp);
				break;
		}
		if($bool==1)
			$out ="<span style='color:red' >$out</span>\n";
		return ($out);
	}
	$GLOBALS['notSent'] = 0;
	$GLOBALS['noReply'] = 0;
	$GLOBALS['excellent'] = 0;
	$GLOBALS['good'] = 0;
	$GLOBALS['meduim'] = 0;
	$GLOBALS['low'] = 0;
	$GLOBALS['unknown'] = 0;
	$hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
	$daftar_id = (isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1;
	$typ = (isset($_REQUEST['typ']))?(int)$_REQUEST['typ']:-1;
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):date('Y-m-d H:i:s'));
	$aztarikh = date("Y-m-d 00:00:00",strtotime($aztarikh));
	$tatarikh = date("Y-m-d 23:59:59",strtotime($tatarikh));
	$user_id=(int)$_SESSION['user_id'];
	$out = '';
	$datay = array();
	$addr = '';
	$legend ='';
	$k = 0;
	$sms_tmp = 0;
	$daftar_shart = '';
	if($daftar_id>0)
	{
		$arr = mysql_class::getInArray('id','ajans',"daftar_id=$daftar_id");
		$daftar_shart = " and `hotel_reserve`.`ajans_id` in ($arr) ";
	}
	if($typ==1)
	{
		$out = '<br/><table cellspacing="10" border="1" style="background-color:#fff;width:50%; font-size: 13px; border-width: 1px; border-collapse: collapse;" ><tr><th>ردیف</th><th>از تاریخ</th><th>تا تاریخ</th><th>نام میهمان</th><th>تلفن</th><th>شماره رزرو</th><th>آژانس</th><th>نظر</th></tr>';
		mysql_class::ex_sql("SELECT  `hotel_reserve`.`tozih`,`hotel_reserve`.`fname` ,  `hotel_reserve`.`lname` ,  `hotel_reserve`.`reserve_id` ,  `hotel_reserve`.`ajans_id` ,  `hotel_reserve`.`sms_vaz` ,  `room_det`.`aztarikh` ,  `room_det`.`tatarikh`
FROM  `hotel_reserve`
LEFT JOIN  `room_det` ON (  `hotel_reserve`.`reserve_id` =  `room_det`.`reserve_id` )
WHERE  `room_det`.`aztarikh` >=  '$aztarikh'
AND  `room_det`.`tatarikh` <=  '$tatarikh'
AND  `hotel_reserve`.`reserve_id` >0 $daftar_shart
GROUP BY  `hotel_reserve`.`reserve_id`",$q);
		while($r = mysql_fetch_array($q))
		{
			$k++;
			$style='';
			if($k%2==1)
				$style = 'style="background-color:#fbdee3"';
			if((int)$r['sms_vaz']!=-2)
				$sms_tmp++;
			$ajans = new ajans_class((int)$r['ajans_id']);
			$daftar = new daftar_class($ajans->daftar_id);
			$out.="<tr $style ><td>".audit_class::enToPer($k)."</td><td>".audit_class::hamed_pdate($r['aztarikh'])."</td><td>".audit_class::hamed_pdate($r['tatarikh'])."</td><td>".$r['fname'].' '.$r['lname']."</td><td>".$r['tozih']."</td><td>".$r['reserve_id']."</td><td>".$ajans->name.'('.$daftar->name.")</td><td>".loadNazar((int)$r['sms_vaz'])."</td></tr>\n";
		}
		$out .='</table>';
		$addr = getPic($datay,$user_id,$sms_tmp);
		$legend = '<table style="background-color:yellow;font-size:10px;border-style:solid;border-collapse: collapse;width:700px;" border="1">
				<tr>
					<td>1->میهمان پاسخ نداده است</td>
					<td>2->عالی</td>
					<td>3->خوب</td>
					<td>4->متوسط</td>
					<td>5->ضعیف</td>
				</tr>
				<tr>
					<td colspan="5" >
						اعداد به صورت تقریبی می‌باشد
					</td>
				</tr>
			</table>';
	}
	else if($typ==2)
	{
		$out = '<br/><table cellspacing="10" border="1" style="background-color:#fff;width:50%; font-size: 13px; border-width: 1px; border-collapse: collapse;" ><tr><th>ردیف</th><th>از تاریخ</th><th>تا تاریخ</th><th>نام میهمان</th><th>تلفن</th><th>شماره رزرو</th><th>آژانس</th><th>مبلغ رزرو</th><th>مبلغ پیامک شده</th></tr>';
		mysql_class::ex_sql("SELECT  (`hotel_reserve`.`m_hotel`+`hotel_reserve`.`m_belit1`+`hotel_reserve`.`m_belit2`) as `m_tour`,`hotel_reserve`.`tozih`,`hotel_reserve`.`fname` ,  `hotel_reserve`.`lname` ,  `hotel_reserve`.`reserve_id` ,  `hotel_reserve`.`ajans_id` ,  `hotel_reserve`.`sms_ghimat` ,  `room_det`.`aztarikh` ,  `room_det`.`tatarikh`
FROM  `hotel_reserve`
LEFT JOIN  `room_det` ON (  `hotel_reserve`.`reserve_id` =  `room_det`.`reserve_id` )
WHERE  `room_det`.`aztarikh` >=  '$aztarikh'
AND  `room_det`.`tatarikh` <=  '$tatarikh'
AND  `hotel_reserve`.`reserve_id` >0 $daftar_shart
GROUP BY  `hotel_reserve`.`reserve_id`",$q);
		while($r = mysql_fetch_array($q))
		{
			$k++;
			$style='';
			if($k%2==1)
				$style = 'style="background-color:#fbdee3"';
			$ajans = new ajans_class((int)$r['ajans_id']);
			$daftar = new daftar_class($ajans->daftar_id);
			$bool = 0;
			if((int)$r['m_tour']!= (int)$r['sms_ghimat'] && (int)$r['sms_ghimat']>1000)
				$bool = 1;
			$out.="<tr $style ><td>".audit_class::enToPer($k)."</td><td>".audit_class::hamed_pdate($r['aztarikh'])."</td><td>".audit_class::hamed_pdate($r['tatarikh'])."</td><td>".$r['fname'].' '.$r['lname']."</td><td>".$r['tozih']."</td><td>".$r['reserve_id']."</td><td>".$ajans->name.'('.$daftar->name.")</td><td>".loadMogh((int)$r['m_tour'],$bool)."</td><td>".loadMogh((int)$r['sms_ghimat'],$bool)."</td></tr>\n";
		}
		$out .='</table>';
	}
		$pic = '';
		if($addr!='')
			$pic ="<img src='$addr' width='700px' style='cursor:pointer;'>".'<br/>'.$legend;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript">
		function send_search()
		{
			document.getElementById('mod').value= 2;
			document.getElementById('frm1').submit();
		}
		function set_value(inp)
		{
			document.getElementById('mablagh').value = document.getElementById(inp).innerHTML;
		}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#datepicker6").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#datepicker7").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
	    	</script>
		<style type="text/css" >
			td{text-align:center;}
		</style>
		<title>
			گزارش پیامک ها	
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<br/>
			<form id='frm1'  method='GET' >
			<table border='1' style='font-size:12px;width:95%;' >
				<tr>
					<th>نام هتل</th>
					<th>نام دفتر</th>
					<th>نوع</th>
					<th>از تاریخ</th>
					<th>تا تاریخ</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					<td>	
						<?php echo loadHotel($hotel_id); ?>
					</td>
					<td>	
						<?php echo loadDaftar($daftar_id); ?>
					</td>
					<td>
						<select class="inp" name="typ" id="typ" >
							<?php echo loadTyp($typ); ?>	
						</select>	
					</td>
					<td>	
         					   <input value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td>
						<input value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
			</form>
			<?php echo $pic; ?>
			<br/>
			<?php echo $out; ?>
		</div>
	</body>
</html>
