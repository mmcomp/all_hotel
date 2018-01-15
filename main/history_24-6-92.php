<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
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
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if( isset($_REQUEST['room_id']))
		$room_id = $_REQUEST['room_id'];
	else
		$room_id = -1;	
	$i=1;
	$room_name = 'اتاق شماره '.room_class::loadById($room_id);
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['aztarikh'],"23:59:59"):'0000-00-00');
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');	
	///eshghal
	$out = '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</th><th>کاربر ثبت کننده</th><th>نام میهمان</th><th>شماره رزرو</th><th>از تاریخ</th><th>تا تاریخ</th><th>وضعیت</th></tr>';
		mysql_class::ex_sql("select `aztarikh`,`tatarikh`,`reserve_id`,`user_id` from `room_det` where date(`aztarikh`)>= '$aztarikh' and date(`tatarikh`)<='$tatarikh' and `room_id`='$room_id' order by `reserve_id`,`aztarikh`",$q);
		while($r = mysql_fetch_array($q))
		{
			$row_style = 'class="showgrid_row_odd_eshghal"';
			if($i%2==0)
				$row_style = 'class="showgrid_row_even_eshghal"';
			$res_id = $r['reserve_id'];
			$user_sabt = loadNameByUser($r['user_id']);
			$name_mehman = loadNameByReserve($res_id);
			$aztarikh_tb = audit_class::hamed_pdate($r['aztarikh']);
			$tatarikh_tb = audit_class::hamed_pdate($r['tatarikh']);
			$stat = 'اشغال';
			if ($res_id<0)
				$stat = 'کنسل شده';
			$out .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$name_mehman</td><td>$res_id</td><td>$aztarikh_tb</td><td>$tatarikh_tb</td><td>$stat</td></tr>";
			$i++;
		}
	$out .= '</table>';
	/////
	$out .= '<br/>'; 
	///nezafat
	$out .= '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</th><th>کاربر ثبت کننده</th><th>نام میهمان</th><th>شماره رزرو</th><th>تاریخ</th><th>وضعیت</th></tr>';
		mysql_class::ex_sql("select * from `nezafat` where date(`mani_time`)>= '$aztarikh' and date(`mani_time`)<='$tatarikh' and `room_id`='$room_id' order by `reserve_id`,`mani_time`",$q);
		$i =1;
		while($r = mysql_fetch_array($q))
		{
			$row_style = 'class="showgrid_row_odd_nezafat"';
			if($i%2==0)
				$row_style = 'class="showgrid_row_even_nezafat"';
			$res_id = $r['reserve_id'];
			$name_mehman = loadNameByReserve($res_id);
			$user_sabt = loadNameByUser($r['user_id']);
			$aztarikh_tb = audit_class::hamed_pdate($r['mani_time']);
			$out .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$name_mehman</td><td>$res_id</td><td>$aztarikh_tb</td><td>نظافت نشده</td></tr>";
			$i++;
		}
		
	$out .= '</table>';
	/////
	$out .= '<br/>'; 
	///tamir
	$out .= '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</th><th>کاربر ثبت کننده</th><th>توضیحات</th><th>تاریخ ثبت</th><th>کاربر رفع کننده</th><th>توضیحات</th><th>تاریخ رفع خرابی</th><th>وضعیت</th></tr>';
		mysql_class::ex_sql("select * from `tasisat` where date(`regdate`)>= '$aztarikh' and date(`regdate`)<='$tatarikh' and `room_id`='$room_id' order by `regdate`",$q);
		$i =1;
		$user_sab = '--';
		$user_answer = '--';
		$toz = '--';
		$answer = '--';
		while($r = mysql_fetch_array($q))
		{
			$row_style = 'class="showgrid_row_odd_tamir"';
			if($i%2==0)
				$row_style = 'class="showgrid_row_even_tamir"';
			if ($r['isFixed']=='1')
				$stat = 'بر طرف شده';
			else
				$stat = 'برطرف نشده';
			$user_sabt = loadNameByUser($r['user_reg']);
			$user_answer = loadNameByUser($r['user_answer']);	
			$toz = ($r['toz']!='')?$r['toz']:'--';
			$answer = $r['answer'];
			$tarikh_sabt = audit_class::hamed_pdate($r['regdate']);
			$answerdate = audit_class::hamed_pdate($r['answerdate']);
			$out .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$toz</td><td>$tarikh_sabt</td><td>$user_answer</td><td>$answer</td><td>$answerdate</td><td>$stat</td></tr>";
			$i++;
		}
		
	$out .= '</table>';
	/////
	$out .= '<br/>'; 
	///problem
	$out .= '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</th><th>کاربر ثبت کننده</th><th>توضیحات</th><th>تاریخ ثبت</th><th>کاربر رفع کننده مشکل</th><th>توضیحات</th><th>تاریخ رفع مشکل</th><th>وضعیت</th></tr>';
		mysql_class::ex_sql("select * from `tasisat_tmp` where date(`regdate`)>= '$aztarikh' and date(`regdate`)<='$tatarikh' and `room_id`='$room_id' order by `regdate`",$q);
		$i =1;
		while($r = mysql_fetch_array($q))
		{
			$row_style = 'class="showgrid_row_odd_prob"';
			if($i%2==0)
				$row_style = 'class="showgrid_row_even_prob"';
			if ($r['en']=='1')
				$stat = 'بر طرف شده';
			else
				$stat = 'برطرف نشده';
			$user_sabt = loadNameByUser($r['user_reg']);
			if ($user_sab=='')
				$user_sab = '--';
			$user_answer = loadNameByUser($r['user_fixed']);
			if ($user_answer=='')
				$user_answer = '--';			
			$toz = $r['toz'];
			if ($toz=='')
				$toz = '--';
			$answer = $r['toz_fix'];
			if ($answer=='')
				$answer = '--';
			$tarikh_sabt = audit_class::hamed_pdate($r['regdate']);
			if ($r['date_fix']!='0000-00-00 00:00:00')
				$answerdate = audit_class::hamed_pdate($r['date_fix']);
			else
				$answerdate = '--';
			$out .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$toz</td><td>$tarikh_sabt</td><td>$user_answer</td><td>$answer</td><td>$answerdate</td><td>$stat</td></tr>";
			$i++;
		}
		
	$out .= '</table>';
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
			function send_search()
			{
			
				if(trim(document.getElementById('datepicker6').value)=='')
				{
					alert('لطفا تاریخ را وارد کنید.');
				}
				else
				{
					document.getElementById('frm1').submit();
				}
			}
	    	</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
		گزارش تاریخچه اتاق
		</title>
	</head>
	<body>
		<center>
			<br/>
			<br/>
				<h3><?php echo $room_name;?></h3>
		        <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
			<br/>
			<br/>
			<form id='frm1'  method='post' >
			<table border='1' style='font-size:12px;' >
				<tr>
					<th> از تاریخ</th>
					<th> تا تاریخ</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
         				<td>
						<input style='width:100px;' value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh'  class='inp' style='direction:ltr;' id="datepicker6" />
					</td>
					<td>
						<input style='width:100px;' value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
			</form>
			<br/>
			<br/>
			<div align="center">				
				<?php echo $out; ?>
			</div>
		</center>
	</body>
</html>
