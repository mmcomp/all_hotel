<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function ppdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function  loadHotel($inp=-1)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return $out;
	}
	function loadRoom($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`name` from `room` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return $out;
	}
	function loadUser($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`fname`,`lname` from `user` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function loadStat($inp)
	{
		$out = "";
		if ($inp==1)
			$out = 'برطرف شده';
		else
			$out = 'برطرف نشده';
		return $out;
	}
	function loadVorood($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`reserve_id`,`aztarikh` from `room_det` where `reserve_id`=$inp ",$q);
                if($r = mysql_fetch_array($q))
	               $out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r["aztarikh"])));
		return $out;
	}
	function loadKhorooj($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`reserve_id`,`tatarikh` from `room_det` where `reserve_id`=$inp ",$q);
                if($r = mysql_fetch_array($q))
	               $out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r["tatarikh"])));
		return $out;
	}
	function listOtagh($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `room` where `en` = 1 and `id`='$inp'",$qq);
		while($row = mysql_fetch_array($qq))
			$out = $row['name'];
		return $out;
	}
	$msg = '';
	$isAdmin = $se->detailAuth('all');
	$is_admin =$isAdmin;
	$reserve_id = ((isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:0);
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');
	$tatarikh2 = ((isset($_REQUEST['tatarikh2']) && $_REQUEST['tatarikh2']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh2'],"23:59:59"):'0000-00-00');
	$user_id=-1;
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$curtime = strtotime(date("Y-m-d"));
	$ta = strtotime($tatarikh);
	$tatarikh = explode(" ",$tatarikh);
	$tatarikh = $tatarikh[0];
	$tatarikh2 = explode(" ",$tatarikh2);
	$tatarikh2 = $tatarikh2[0];
	$day = date("Y-m-d");
	$nafar = 0;
	$mablagh = 0;
	$mablagh_tmp = 0;
	$mablagh_kol = 0;
	
	$styl = 'class="showgrid_row_odd"';
	$co_room = 0;
	$sum_room = 0;
	$khorooj = '';
	$output ='';
	$rooms_id = '';
	$rooms_ids = '';
	
	isset($_REQUEST['room_names'])?$rm_names=$_REQUEST['room_names']:$rm_names=-1;
	$wer='';
	if( isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else
		$h_id = 1;
	$select="<select name='room_names'><option value=-1>همه </option> ";
	mysql_class::ex_sql("select `id`,`name` from `room` where `hotel_id`='$h_id'  order by `name` ",$qu);
	while($r = mysql_fetch_array($qu))
	{
		$room_name=$r['name'];
		$room_id=$r['id'];
		if($rm_names!=-1)
			if($room_id==$rm_names)
				$sel_def='selected=selected';
			else
				$sel_def='';
		else 
			$sel_def='';
		
		$select.="<option  value='$room_id' $sel_def>$room_name</option>";
	}		
	$select.="</select>";
	
	if ($h_id!=-1)
	{
		$rooms_id = "(";
		mysql_class::ex_sql("select `id` from `room` where `hotel_id`='$h_id' order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$rooms_id .= $r["id"].',';
		}
		$rooms_ids = substr($rooms_id, 0, -1);
		$rooms_ids = $rooms_ids .')';
	}
	if(isset($_REQUEST['rep']) && ((int)$_REQUEST['rep']==1) && ($rooms_ids!=""))
	{
		if ($rm_names!=-1)
			$room_shart = " `room_id`='".$rm_names."' and ";
		else
			$room_shart = "";
		$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>نام </th><th>شماره رزرو</th><th>شماره اتاق</th><th>تاریخ ورود</th><th>تاریخ خروج</th><th>ساعت خروج</th></tr>';
		if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
		{
			mysql_class::ex_sql("SELECT * FROM  `mehman` WHERE $room_shart DATE(`khorooj`) >='$tatarikh' and DATE(`khorooj`) <='$tatarikh2' order by `room_id`",$tmphelp);
			$den=0;
			$i = 1;
			$room = -1;
			while($r = mysql_fetch_array($tmphelp))
			{
				$row_style = 'class="showgrid_row_odd"';
				$res_id = $r["reserve_id"];
				mysql_class::ex_sql("SELECT `aztarikh`,`tatarikh` FROM  `room_det` where `reserve_id`=$res_id",$tmphelp2);
				if($r2 = mysql_fetch_array($tmphelp2))
				{
					$den = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($r2['aztarikh'])));	
					$ta = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($r2['tatarikh'])));
				}
				$fname=$r['fname'];
				$lname=$r['lname'];
				$fulname=$fname .' ' .$lname;
				$vh=date(('H:i'),strtotime($r['khorooj']));
				$vorood_h=date(('H:i'),strtotime($r['vorood_h']));
				$r_id = $r['room_id'];
				mysql_class::ex_sql("select `name` from  `room` where `id`='$r_id'",$qname);
				if($row = mysql_fetch_array($qname))
					$room=$row['name'];
				$reserve=$r['reserve_id'];
				if($i%2==0)
					$row_style = 'class="showgrid_row_even"';
				 $output .="<tr $row_style><td>$fulname</td><td>$reserve</td><td>$room</td>
				<td>$den</td><td>$ta</td><td>$vorood_h</td><td>$vh</td></tr>";
				$i++;
			}	
		
			$mablagh = monize($mablagh);
			$mablagh_kol = monize($mablagh_kol);
		
		}
	}
	elseif(isset($_REQUEST['rep']) && ((int)$_REQUEST['rep']==2) && ($rooms_ids!=""))
	{
		if ($rm_names!=-1)
			$room_shart = " `room_id`='".$rm_names."' and ";
		else
			$room_shart = "";
	//	$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف </th><th>شماره اتاق</th><th>نام میهمان</th><th>تاریخ مقرر نظافت</th><th>تاریخ اتمام نظافت</th><th>کاربر ثبت کننده نظافت</th></tr>';
		$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف </th><th>شماره اتاق</th><th>نام میهمان</th><th>تاریخ مقرر نظافت</th><th>تاریخ اتمام نظافت</th></tr>';
		mysql_class::ex_sql("SELECT * FROM  `nezafat` WHERE $room_shart DATE(`mani_time`) >='$tatarikh' and DATE(`mani_time`) <='$tatarikh2'order by `room_id`,`mani_time`",$tmphelp);
		$i = 1;
		$mehman_name = '';
		while($r = mysql_fetch_array($tmphelp))
		{
			$reserve_id=$r['reserve_id'];
			mysql_class::ex_sql("select `fname`,`lname` from  `hotel_reserve` where `reserve_id`='$reserve_id'",$q_res);
			if($r_res = mysql_fetch_array($q_res))
				$mehman_name = $r_res['fname'].' '.$r_res['lname'];
			$row_style = 'class="showgrid_row_odd"';
			$room_id=$r['room_id'];
			mysql_class::ex_sql("select `name` from  `room` where `id`='$room_id'",$q_room);
			if($r_room = mysql_fetch_array($q_room))
				$room_name = $r_room["name"];
			
			$mani_time= audit_class::hamed_pdate(date(('Y-m-d H:i'),strtotime($r['mani_time'])));
			$room_id = $r['room_id'];
			$nezafat_time = audit_class::hamed_pdate(date(('Y-m-d H:i'),strtotime($r['nezafat_time'])));
			$user_id=$r['user_id'];
			mysql_class::ex_sql("select `fname`,`lname` from  `user` where `id`='$user_id'",$q_name);
			if($r_name = mysql_fetch_array($q_name))
				$user_name = $r_name['fname'].' '.$r_name['lname'];
			if($i%2==0)
				$row_style = 'class="showgrid_row_even"';
			/* $output .="<tr $row_style><td>$i</td><td>$room_name</td><td>$mehman_name</td>
			<td>$mani_time</td><td>$nezafat_time</td><td>$user_name</td></tr>";*/
			$output .="<tr $row_style><td>$i</td><td>$room_name</td><td>$mehman_name</td>
			<td>$mani_time</td><td>$nezafat_time</td></tr>";
			$i++;
		}	
	}
	else if(isset($_REQUEST['rep']) && (int)$_REQUEST['rep']==3 )
	{
		$rep = (int)$_REQUEST['rep'];
		if($rm_names<>-1)
			$wer='`room_id`='.$rm_names.' and ';
		$shart = "$wer DATE(`regdate`) >='$tatarikh' and DATE(`regdate`) <='$tatarikh2'";		
		$grid = new jshowGrid_new("tasisat_tmp","grid1");
		$grid->whereClause = " $shart order by `room_id`";
		$grid->setERequest(array('tatarikh'=>$tatarikh,'tatarikh2'=>$tatarikh2,'rep'=>$rep));
		$grid->columnHeaders[0]= null;
		$grid->columnHeaders[1]= 'نام هتل';
		$grid->columnFunctions[1]= 'loadHotel';
		$grid->columnHeaders[2]= 'شماره اتاق';
		$grid->columnFunctions[2]= 'loadRoom';
		$grid->columnHeaders[3]= 'کاربر ثبت کننده ';
		$grid->columnFunctions[3]= 'loadUser';
		$grid->columnHeaders[4]= 'کاربر رفع کننده مشکل';
		$grid->columnFunctions[4]= 'loadUser';
		$grid->columnHeaders[5]= 'توضیح مشکل';
		$grid->columnHeaders[6]= 'توضیح رفع مشکل';
		$grid->columnHeaders[7]= 'تاریخ ثبت مشکل';
		$grid->columnFunctions[7]= 'hamed_pdate';
		$grid->columnHeaders[8]= 'تاریخ رفع مشکل';
		$grid->columnFunctions[8]= 'hamed_pdate';
		$grid->columnHeaders[9]= 'وضعیت';
		$grid->columnFunctions[9]= 'loadStat';
		$grid->canAdd = FALSE;
		$grid->canDelete = FALSE;
		$grid->canEdit = FALSE;
		$grid->intial();
		$grid->executeQuery();
		$output = $grid->getGrid();
	}
	if(isset($_REQUEST['rep']) && ((int)$_REQUEST['rep']==4) && ($rooms_ids!=""))
	{
		if ($rm_names!=-1)
			$room_shart = " and `room_id`='".$rm_names."'";
		else
			$room_shart = "";
		$tmp = '';
		$rep = (int)$_REQUEST['rep'];
		mysql_class::ex_sql("select `reserve_id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) <= '$tatarikh' and date(`tatarikh`) > '$tatarikh') or (date(`aztarikh`) < '$tatarikh2' and date(`tatarikh`) > '$tatarikh')) group by `reserve_id`",$q);
		while ($r = mysql_fetch_array($q))
		{
			$r_hotel = room_class::loadHotelByReserve($r['reserve_id']);
			if ($h_id==$r_hotel)
				$tmp .=($tmp==''? '':',' ).$r['reserve_id'];
		}
		if($tmp!='')
			$shart = " `reserve_id` in ($tmp)";
		else
			$shart = "1=0";
		$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?$_REQUEST['tatarikh']:'0000-00-00');
		$tatarikh2 = ((isset($_REQUEST['tatarikh2']) && $_REQUEST['tatarikh2']!='' )?$_REQUEST['tatarikh2']:'0000-00-00');
		$user = new user_class((int)$_SESSION['user_id']);
		$grid = new jshowGrid_new("mehman","grid1");
		$grid->setERequest(array('tatarikh'=>$tatarikh,'tatarikh2'=>$tatarikh2,'rep'=>$rep));
		$grid->index_width = '20px';
		$grid->height = '95%';
		$grid->showAddDefault = FALSE;
		$grid->whereClause=$shart.' '.$room_shart.' and `room_id`>0 order by `room_id`,`reserve_id`';
echo $grid->whereClause;
		$grid->columnHeaders[0] = null;			
		$grid->columnHeaders[1] = "شماره اتاق";
		$grid->columnFunctions[1] = "listOtagh";
		$grid->columnHeaders[2] = 'شماره رزرو';
		$grid->columnHeaders[3] = 'نام';
		$grid->columnHeaders[4] = 'نام  خانوادگی';
		$grid->columnHeaders[5] ='ساعت  ورود' ;
		$grid->columnHeaders[6] = null;	
		$grid->columnHeaders[7] = null;	
		$grid->columnHeaders[8] = null;	
		$grid->columnHeaders[9] = null;	
		$grid->columnHeaders[10] = null;	
		$grid->columnHeaders[11] = null;	
		$grid->columnHeaders[12] = null;
		$grid->columnHeaders[13] = null;	
		$grid->columnHeaders[14] = null;
		$grid->columnHeaders[15] = null;
		$grid->columnHeaders[16] = null;	
		$grid->columnHeaders[17] = null;	
		$grid->columnHeaders[18] = null;
		$grid->columnHeaders[19] = null;	
		$grid->columnHeaders[20] = null;	
		$grid->columnHeaders[21] = null;	
		$grid->columnHeaders[22] = null;	
		$grid->columnHeaders[23] = null;	
		$grid->columnHeaders[24] = null;	
		$grid->columnHeaders[25] = null;	
		$grid->columnHeaders[26] = null;	
		$grid->columnHeaders[27] = null;
		$grid->addFeild('reserve_id',26);
		$grid->columnHeaders[26] = 'تاریخ ورود';
		$grid->columnFunctions[26] = 'loadVorood';
		$grid->addFeild('reserve_id',27);
		$grid->columnHeaders[27] = 'تاریخ خروج';
		$grid->columnFunctions[27] = 'loadKhorooj';
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
		$grid->intial();
		$grid->executeQuery();
		$output = $grid->getGrid();
	}
	else
		echo "";
	$output .='</table>';
	$sel1 = "";
	$sel2 = "";
	$sel3 = "";
	$sel4 = "";
	if (isset($_REQUEST["rep"]))
	{
		if ($_REQUEST["rep"]==1)
			$sel1 = "selected=selected";
		elseif ($_REQUEST["rep"]==2)
			$sel2 = "selected=selected";
		elseif ($_REQUEST["rep"]==3)
			$sel3 = "selected=selected";
		elseif ($_REQUEST["rep"]==4)
			$sel4 = "selected=selected";
		else
		{
			$sel1 = "";
			$sel2 = "";
			$sel3 = "";
			$sel4 = "";
		}
	}
	
	
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
			
			if(trim(document.getElementById('datepicker6').value)=='')
			{
				alert('لطفا تاریخ را وارد کنید.');
			}
			else
			{
				document.getElementById('mod').value= 2;
				document.getElementById('frm1').submit();
			}
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
		<style>
			td{text-align:center;}
		</style>
		<title>
			سامانه رزرواسیون	
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<form id='frm1'  method='post' >
			<table border='1' style='font-size:12px;' >
				<tr>
					<th>شماره اتاق  </th>
					<th> انتخاب تاریخ  </th>
					<th> تا  تاریخ  </th>
					<th>انتخاب بر اساس</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					
					<td>
					<?php echo $select; ?>
					</td>
         				<td>
						<input style='width:100px;' value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='inp' style='direction:ltr;' id="datepicker6" />
					</td>
					<td>
						<input style='width:100px;' value="<?php echo ((isset($_REQUEST['tatarikh2']))?$_REQUEST['tatarikh2']:''); ?>" type="text" name='tatarikh2'  class='inp' style='direction:ltr;' id="datepicker7" />
					</td>	
					<td>
				<select name='rep'>
					<option value='1' <?php echo $sel1;?>>ساعات خروج</option>
					<option value='4' <?php echo $sel4;?>>ساعت ورود</option>	
					<option value='2' <?php echo $sel2;?>>ساعات نظافت</option>
					<option value='3' <?php echo $sel3;?>>مشکلات اتاق</option>					
				</select>
					</td>	
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
					<input type='hidden' name='h_id' id='h_id' value='<?php echo $h_id;?>' >
			</form>
			<?php echo $output.' '.$msg; ?>
			
		</div>
		<br/>		
	</body>
</html>
