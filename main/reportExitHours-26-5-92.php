<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
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
	/*
	if($az - $curtime <= 24*60*60 && !$is_admin)
	{
		$aztarikh = date("Y-m-d",$curtime);
		$tatarikh = date("Y-m-d",$curtime);
	}
	else
	{
	*/
	$tatarikh = explode(" ",$tatarikh);
	$tatarikh = $tatarikh[0];
	$day = date("Y-m-d");
	//}
	
	
	//var_dump($tmp);
	//var_dump(room_det_class::loadDetByReserve_id($tmp[0]));
	//var_dump(room_det_class::loadNamesByReserve_id($tmp[0]));
	//var_dump(room_det_class::loadKhadamatByReserve_id($tmp[0]));
	$nafar = 0;
	$mablagh = 0;
	$mablagh_tmp = 0;
	$mablagh_kol = 0;
	$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>نام </th><th>شماره رزرو</th><th>شماره اتاق</th><th>تاریخ ورود</th><th>ساعت خروج</th></tr>';
	$styl = 'class="showgrid_row_odd"';
	$co_room = 0;
	$sum_room = 0;
	$khorooj = '';
	if(isset($_REQUEST['rep']) && (int)$_REQUEST['rep']==1)
	{
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
	{
		//echo "($aztarikh,$tatarikh,$user_id,$isAdmin,$f_name,$l_name,$reserve_id)<br/>\n";
		mysql_class::ex_sql("SELECT * FROM  `mehman` WHERE DATE(`khorooj`) ='$tatarikh' order by `room_id`",$tmphelp);
		//$tmp = mehman_class::allFetch($tatarikh);
		//("SELECT * FROM  `mehman` WHERE DATE(`khorooj`) ='2013-08-05'");
		$reserve=$reserve_id;
		$den=0;
		$i = 1;
		while($r = mysql_fetch_array($tmphelp))
		{
			$row_style = 'class="showgrid_row_odd"';
			mysql_class::ex_sql("SELECT `aztarikh` FROM  `room_det` where `reserve_id`=$reserve",$tmphelp2);
			while($r2 = mysql_fetch_array($tmphelp2))
				$den=audit_class::hamed_pdate($r2['aztarikh']);	
			$fname=$r['fname'];
			$lname=$r['lname'];
			$fulname=$fname .' ' .$lname;
			$vh=$r['vorood_h'];
			$r_id = $r['room_id'];
			mysql_class::ex_sql("select `name` from  `room` where `id`='$r_id'",$qname);
			if($row = mysql_fetch_array($qname))
				$room=$row['name'];
			$reserve=$r['reserve_id'];
			if($i%2==0)
				$row_style = 'class="showgrid_row_even"';
			 $output .="<tr $row_style><td>$fulname</td><td>$reserve</td><td>$room</td>
			<td>$den</td><td>$vh</td></tr>";
			$i++;
		}	
		
		$mablagh = monize($mablagh);
		$mablagh_kol = monize($mablagh_kol);
		
	}
	}
	else
	{
		

	}
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
			<?php echo $output.' '.$msg; ?>
			</form>
		</div>
		<br/>		
	</body>
</html>
