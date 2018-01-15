<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function hamed_pdateBack($inp)
	{
		$out = '';
                $out = audit_class::hamed_pdate($inp);
		return ($out);
	}
	function listStatus()
	{
		$out = array();
		$out["خدمات ارائه شده"] = 1;
		$out["خدمات ارائه نشده"] = 0;
		return $out;
	}
	function loadName($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `lname` from `hotel_reserve` where `reserve_id` = $inp",$q);
                if($r = mysql_fetch_array($q))
			$out = $r["lname"];
		return $out;
	}
	function loadTozih($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `tozih` from `hotel_reserve` where `reserve_id` = $inp",$q);
                if($r = mysql_fetch_array($q))
			$out = $r["tozih"];
		return $out;
	}
	function loadRooms($inp)
	{
		$out ='';
		mysql_class::ex_sql("select `room_id` from `room_det` where `reserve_id` = $inp",$q);
		$rooms = '(';
		while($r = mysql_fetch_array($q))
                {
			$r_id = $r['room_id'];
			mysql_class::ex_sql("select `name` from `room` where `id` = '$r_id'",$q_name);
			if($r_name = mysql_fetch_array($q_name))
				$rooms.=$r_name['name'].',';
		}
		
		$out = $rooms.')';
		return $out;
	}
	function loadazTarikh($inp)
	{
		$out ='';
		mysql_class::ex_sql("select `aztarikh` from `room_det` where `reserve_id` = $inp",$q);
		if($r = mysql_fetch_array($q))
			$aztarikh = $r['aztarikh'];
		$out = audit_class::hamed_pdate($aztarikh);
		return $out;
	}
	
	function loadTaTarikh($inp)
	{
		$out ='';
		mysql_class::ex_sql("select `tatarikh` from `room_det` where `reserve_id` = $inp",$q);
		if($r = mysql_fetch_array($q))
			$tatarikh = $r['tatarikh'];
		$out = audit_class::hamed_pdate($tatarikh);
		return $out;
	}
	if( isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else
		$h_id = -1;
	mysql_class::ex_sql("select `id` from `khadamat` where `name`='عکاسخانه' and `hotel_id` = '$h_id' ",$q);
	if ($r=mysql_fetch_array($q))
		$khadamat_id = $r['id'];
	else
		$khadamat_id = -1;
	$tarikh =((isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):date("Y-m-d"));
	if ($h_id !='-1')
		$grid_shart = " `khadamat_id`='$khadamat_id' and date(`tarikh`)=date('$tarikh') and `reserve_id`>0 order by `reserve_id`";
	else
		$grid_shart = "1=0";	
	$grid = new jshowGrid_new("khadamat_det","grid1");
	$grid->width = '95%';
	$grid->whereClause = $grid_shart;
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= null;
	$grid->columnHeaders[2]= null;
	$grid->columnHeaders[3]= null;
	$grid->columnHeaders[4]= null;
	//$grid->columnFunctions[4]= "hamed_pdateBack";
	$grid->columnHeaders[5]= null;
	$grid->columnHeaders[6]= null;
	$grid->columnHeaders[7]= 'وضعیت خدمات';
	$grid->columnLists[7] = listStatus();
	$grid->addFeild('reserve_id');
	$grid->columnHeaders[8] = 'شماره رزرو';
	$grid->addFeild('reserve_id');
	$grid->columnHeaders[9] = 'نام سرگروه';
	$grid->columnFunctions[9]='loadName';
	$grid->addFeild('reserve_id');
	$grid->columnHeaders[10] = 'شماره اتاق';
	$grid->columnFunctions[10]='loadRooms';
	$grid->addFeild('reserve_id');
	$grid->columnHeaders[11] = 'اطلاعات بیشتر';
	$grid->columnFunctions[11]='loadTozih';
	$grid->addFeild('reserve_id');
	$grid->columnHeaders[12] = 'تاریخ ورود';
	$grid->columnFunctions[12]='loadazTarikh';
	$grid->addFeild('reserve_id');
	$grid->columnHeaders[13] = 'تاریخ خروج';
	$grid->columnFunctions[13]='loadTaTarikh';
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	$grid->canEdit = TRUE;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart1 = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart1.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	$combo_hotel = "";
		$combo_hotel .= "هتل : <select class='inp' name=\"h_id\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
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
		<script>
			function sbtFrm()
			{
				document.getElementById('frm1').submit();
			}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#tarikh").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
	    	</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
ثبت خدمات عکاسخانه برای میهمان
		</title>
	</head>
	<body>
		<br/>
		<br/>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" id="div_main" >
			<br/>
			<form id="frm1" method="GET">
				<table id='combo_table' >
					<tr>
						<td>
							<?php echo $combo_hotel; ?>
						</td>
						<td>
							<label> تاریخ</label>
						</td>
						<td>
							<input class="inp" readonly="readonly" type="text" name="tarikh" id="tarikh" value="<?php echo audit_class::hamed_pdate($tarikh); ?>"  >
							<input type="hidden" name="tarikh1" id="tarikh1" value="<?php echo audit_class::hamed_pdate($tarikh); ?>"  >
						</td>
						<td>
		                                        <input class="inp" type="button" value="جستجو" onclick="sbtFrm();"  >
                                                </td>
					</tr>
				</table>		
			</form>
			<br/>				
			<?php echo $out;  ?>
			<br/>
			<input type="button" value="چاپ" class="inp" onclick="getPrint();" >
		</div>
	</body>
</html>
