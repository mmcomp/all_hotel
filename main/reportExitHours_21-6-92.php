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
	$msg = '';
	$isAdmin = $se->detailAuth('all');
	$is_admin =$isAdmin;
	$reserve_id = ((isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:0);
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');
	$user_id=-1;
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$curtime = strtotime(date("Y-m-d"));
	$ta = strtotime($tatarikh);
	$tatarikh = explode(" ",$tatarikh);
	$tatarikh = $tatarikh[0];
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
	if( isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else
		$h_id = -1;
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
		$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>نام </th><th>شماره رزرو</th><th>شماره اتاق</th><th>تاریخ ورود</th><th>تاریخ خروج</th><th>ساعت خروج</th></tr>';
		if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
		{
			if ($rooms_ids!=")")
			{
				mysql_class::ex_sql("SELECT * FROM  `mehman` WHERE `room_id` in $rooms_ids and DATE(`khorooj`) ='$tatarikh' order by `room_id`",$tmphelp);
				$den=0;
				$i = 1;
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
					$r_id = $r['room_id'];
					mysql_class::ex_sql("select `name` from  `room` where `id`='$r_id'",$qname);
					if($row = mysql_fetch_array($qname))
						$room=$row['name'];
					$reserve=$r['reserve_id'];
					if($i%2==0)
						$row_style = 'class="showgrid_row_even"';
					 $output .="<tr $row_style><td>$fulname</td><td>$reserve</td><td>$room</td>
					<td>$den</td><td>$ta</td><td>$vh</td></tr>";
					$i++;
				}	
		
				$mablagh = monize($mablagh);
				$mablagh_kol = monize($mablagh_kol);
			}
		}
	}
	elseif(isset($_REQUEST['rep']) && (int)$_REQUEST['rep']==2)
	{
		$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف </th><th>شماره اتاق</th><th>شماره رزرو</th><th>تاریخ مقرر نظافت</th><th>تاریخ اتمام نظافت</th><th>کاربر ثبت کننده نظافت</th></tr>';
		mysql_class::ex_sql("SELECT * FROM  `nezafat` WHERE DATE(`mani_time`) ='$tatarikh' order by `mani_time`",$tmphelp);
		$i = 1;
		while($r = mysql_fetch_array($tmphelp))
		{
			$row_style = 'class="showgrid_row_odd"';
			$room_id=$r['room_id'];
			mysql_class::ex_sql("select `name` from  `room` where `id`='$room_id'",$q_room);
			if($r_room = mysql_fetch_array($q_room))
				$room_name = $r_room["name"];
			$reserve_id=$r['reserve_id'];
			$mani_time= audit_class::hamed_pdate(date(('Y-m-d H:i'),strtotime($r['mani_time'])));
			$room_id = $r['room_id'];
			$nezafat_time = audit_class::hamed_pdate(date(('Y-m-d H:i'),strtotime($r['nezafat_time'])));
			$user_id=$r['user_id'];
			mysql_class::ex_sql("select `fname`,`lname` from  `user` where `id`='$user_id'",$q_name);
			if($r_name = mysql_fetch_array($q_name))
				$user_name = $r_name['fname'].' '.$r_name['lname'];
//echo $user_name;
			if($i%2==0)
				$row_style = 'class="showgrid_row_even"';
			 $output .="<tr $row_style><td>$i</td><td>$room_name</td><td>$reserve_id</td>
			<td>$mani_time</td><td>$nezafat_time</td><td>$user_name</td></tr>";
			$i++;
		}	
	}
	else
		echo "";
	$output .='</table>';
	$sel1 = "";
	$sel2 = "";
	if (isset($_REQUEST["rep"]))
	{
		if ($_REQUEST["rep"]==1)
			$sel1 = "selected=selected";
		elseif ($_REQUEST["rep"]==2)
			$sel2 = "selected=selected";
		else
		{
			$sel1 = "";
			$sel2 = "";
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
			if(trim(document.getElementById('datepicker7').value)=='')
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
			<form id='frm1'  method='GET' >
			<table border='1' style='font-size:12px;' >
				<tr>
					
					<th> اتنخاب تاریخ  </th>
					<th>انتخاب بر اساس</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					
         				<td>
						<input style='width:100px;' value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
					<td>	
					<select name='rep'>
						<option value='1' <?php echo $sel1;?>>ساعات خروج</option>
						<option value='2' <?php echo $sel2;?>> ساعات نظافت</option>					
					</select>
					</td>	
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
					<input type='hidden' name='h_id' id='h_id' value='<?php echo $h_id;?>' >
			<?php echo $output.' '.$msg; ?>
			</form>
		</div>
		<br/>		
	</body>
</html>
