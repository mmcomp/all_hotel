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
	function loadNameByroom_id($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `name` from `room` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
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
		function loadNameByReserve($res=-1)
	{
		$out = '';
		mysql_class::ex_sql("select `fname`,`lname` from `hotel_reserve` where `reserve_id` = $res",$q);
                if($r = mysql_fetch_array($q))
	        {
			$out = $r['fname'].' '.$r['lname'];
		}
		else
			$out = '--';
		return($out);
	}
	function loadNameByUser($user=-1)
	{
		$out = '';
		mysql_class::ex_sql("select `fname`,`lname` from `user` where `id` = $user",$q);
                if($r = mysql_fetch_array($q))
	        {
			$out = $r['fname'].' '.$r['lname'];
		}
		else
			$out = '--';
		return($out);
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
	if ($h_id!=-1) 
	{
		mysql_class::ex_sql("select `name` from `hotel` where `id`='$h_id'",$q);
		if($r = mysql_fetch_array($q))
			$hotel_name = $r['name'];
	}
	else
		$hotel_name = 'هتل انتخاب نشده است';
	$tedad_v = 0;
	$tedad_kh = 0;
	$radif = 1;
	if($rooms_ids!="")
	{
		if ($rm_names!=-1)
			$room_shart = " `room_id`='".$rm_names."' and ";
		else
			$room_shart = " `room_id` in ".$rooms_ids." and ";
		$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</t><th>کاربر رزرو گیرنده </th><th>نام </th><th>شماره رزرو</th><th>شماره اتاق</th><th>تاریخ ورود</th><th>تاریخ خروج</th><th>وضعیت</th></tr>';
		if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
		{
			while($tatarikh<=$tatarikh2)
			{
				////voroodi dar rouz
				mysql_class::ex_sql("SELECT * FROM  `room_det` WHERE $room_shart DATE(`aztarikh`) ='$tatarikh' and `reserve_id`>0 order by `room_id`",$tmphelp);
				$den=0;
				$i = 1;
				$room = -1;
				while($r = mysql_fetch_array($tmphelp))
				{
					$tedad_v++;
					$row_style = 'class="showgrid_row_odd"';
					$res_id = $r["reserve_id"];
					if ($res_id>0)
					{
						$reserve = new reserve_class($res_id);
						$reserve_user=new user_class($reserve->room_det[0]->user_id);
						$reserver_user =$reserve_user->fname.' '.$reserve_user->lname;
					}
					else
						$reserver_user = 'نامشخص';
						$den = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($r['aztarikh'])));	
						$ta = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($r['tatarikh'])));
					/*$fname=$r['fname'];
					$lname=$r['lname'];
					$fulname=$fname .' ' .$lname;*/
					
					$fulname= room_det_class::loadNameByReserve($res_id);
					/*$vh=date(('H:i'),strtotime($r['khorooj']));
					$vorood_h=date(('H:i'),strtotime($r['vorood_h']));*/
					$r_id = $r['room_id'];
					mysql_class::ex_sql("select `name` from  `room` where `id`='$r_id'",$qname);
					if($row = mysql_fetch_array($qname))
						$room=$row['name'];
					$reserve=$r['reserve_id'];
					if($i%2==0)
						$row_style = 'class="showgrid_row_even"';
					// $output .="<tr $row_style><td>$reserver_user</td><td>$fulname</td><td>$reserve</td><td>$room</td><td>$den</td><td>$ta</td><td>$vorood_h</td><td>$vh</td><td>ورودی</td></tr>";
					$output .="<tr $row_style><td>$radif</td><td>$reserver_user</td><td>$fulname</td><td>$reserve</td><td>$room</td><td>$den</td><td>$ta</td><td style='background-color:#60de4e;'>ورودی</td></tr>";
					$i++;
					$radif++;
				}
				////khorouji da rouz
				mysql_class::ex_sql("SELECT * FROM  `room_det` WHERE $room_shart DATE(`tatarikh`) ='$tatarikh' and `reserve_id`>0 order by `room_id`",$tmphelp);
				$den=0;
				$i = 1;
				$room = -1;
				while($r = mysql_fetch_array($tmphelp))
				{
					$tedad_kh++;
					$row_style = 'class="showgrid_row_odd"';
					$res_id = $r["reserve_id"];
					if ($res_id>0)
					{
						$reserve = new reserve_class($res_id);
						$reserve_user=new user_class($reserve->room_det[0]->user_id);
						$reserver_user =$reserve_user->fname.' '.$reserve_user->lname;
					}
					else
						$reserver_user = 'نامشخص';
						$den = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($r['aztarikh'])));	
						$ta = audit_class::hamed_pdate(date(('Y-m-d'),strtotime($r['tatarikh'])));
				/*	$fname=$r['fname'];
					$lname=$r['lname'];
					$fulname=$fname .' ' .$lname;*/
					$fulname= room_det_class::loadNameByReserve($res_id);
					$r_id = $r['room_id'];
					mysql_class::ex_sql("select `name` from  `room` where `id`='$r_id'",$qname);
					if($row = mysql_fetch_array($qname))
						$room=$row['name'];
					$reserve=$r['reserve_id'];
					if($i%2==0)
						$row_style = 'class="showgrid_row_even"';
					// $output .="<tr $row_style><td>$reserver_user</td><td>$fulname</td><td>$reserve</td><td>$room</td><td>$den</td><td>$ta</td><td>$vorood_h</td><td>$vh</td><td>خروجی</td></tr>";
					$output .="<tr $row_style><td>$radif</td><td>$reserver_user</td><td>$fulname</td><td>$reserve</td><td>$room</td><td>$den</td><td>$ta</td><td style='background-color:#ec3b3d;'>خروجی</td></tr>";
					$i++;
					$radif++;
				}
				$tatarikh = date('Y-m-d', strtotime($tatarikh .' +1 day'));
				
			}	
		
		}
		$output .="</table></br>";
		$kol_meh = $tedad_v-$tedad_kh;
		$output .= '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>تعداد ورودی </th><th >تعداد خروجی </th><th>تعداد کل</th></tr>';
		$output .="<tr><td style='background-color:#60de4e;'>$tedad_v</td><td style='background-color:#ec3b3d;'>$tedad_kh</td><td style='background-color:#ffffff;'>$kol_meh</td></tr>";
		$output .="</table></br>";
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
لیست میهمانان ورودی و خروجی
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
				
				<h2><?php echo $hotel_name;?></h2>
			<br/>
			<form id='frm1'  method='post' >
			<table border='1' style='font-size:12px;' >
				<tr>
					<th>شماره اتاق  </th>
					<th> انتخاب تاریخ  </th>
					<th> تا  تاریخ  </th>
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
