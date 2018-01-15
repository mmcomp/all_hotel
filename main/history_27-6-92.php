<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);	
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
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
	$combo_state = '';
	$state_id = (isset($_REQUEST['state_id'])?$_REQUEST['state_id']:-1);
	$combo_state .= "<form name=\"selState\" id=\"selState\" method=\"POST\">";
		$combo_state .= "وضعیت : <select class='inp' id=\"hotel_id\" name=\"state_id\" onchange=\"document.getElementById('selState').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		$select_all = ((int)$state_id==-1)?"selected='selected'":"";
		$select1 = ((int)$state_id==1)?"selected='selected'":"";
		$select2 = ((int)$state_id==2)?"selected='selected'":"";
		$select3 = ((int)$state_id==3)?"selected='selected'":"";
		$select4 = ((int)$state_id==4)?"selected='selected'":"";
	        $combo_state .= "<option value='-1' $select_all>همه</option>\n";
		$combo_state .= "<option value='1' $select1>اشغال</option>\n";
		$combo_state .= "<option value='2' $select2>نظافت</option>\n";
		$combo_state .= "<option value='3' $select3>مشکلات تاسیساتی</option>\n";
		$combo_state .= "<option value='4' $select4>مشکلات ثبت شده</option>\n";
		$combo_state .= "</select>";
	$combo_state .= "</form>";
	$out_1 = '';
	$out_2 = '';
	$out_3 = '';
	$out_4 = '';
	if( isset($_REQUEST['room_id']))
		$room_id = $_REQUEST['room_id'];
	else
		$room_id = -1;	
	$i=1;
	$room_name = 'اتاق شماره '.room_class::loadById($room_id);
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['aztarikh'],"23:59:59"):'0000-00-00');
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');	
	///eshghal
	$out_1 = '<center><h3>اتاق در وضعیت اشغال</h3></center>';
	$out_1 .= '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</th><th>کاربر ثبت کننده</th><th>نام میهمان</th><th>شماره رزرو</th><th>از تاریخ</th><th>تا تاریخ</th><th>ساعت ورود</th><th>ساعت خروج</th><th>وضعیت</th></tr>';
		mysql_class::ex_sql("select `aztarikh`,`tatarikh`,`reserve_id`,`user_id` from `room_det` where date(`aztarikh`)>= '$aztarikh' and date(`tatarikh`)<='$tatarikh' and `room_id`='$room_id' order by `reserve_id`,`aztarikh`",$q);
		while($r = mysql_fetch_array($q))
		{
			$vorood_h = '';
			$khorooj = '';
			$res_id = $r['reserve_id'];
			$is_paziresh = reserve_class::isPaziresh($res_id,$room_id);
			if ($is_paziresh)
			{
				mysql_class::ex_sql("select `vorood_h`,`khorooj` from `mehman` where `reserve_id`='$res_id'",$q_mehman);
				while($r_mehman = mysql_fetch_array($q_mehman))
				{
					$vorood_h = $r_mehman['vorood_h'];
					$khorooj = $r_mehman['khorooj'];
				}
				$tmp = explode(" ",$khorooj);
				$ti_khorooj = $tmp[1];
				$row_style = 'class="showgrid_row_odd_eshghal"';
				if($i%2==0)
					$row_style = 'class="showgrid_row_even_eshghal"';
			
				$user_sabt = loadNameByUser($r['user_id']);
				$name_mehman = loadNameByReserve($res_id);
				$aztarikh_tb = audit_class::hamed_pdate($r['aztarikh']);
				$tatarikh_tb = audit_class::hamed_pdate($r['tatarikh']);
				$stat = 'اشغال';
				if ($res_id<0)
					$stat = 'کنسل شده';
				$out_1 .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$name_mehman</td><td>$res_id</td><td>$aztarikh_tb</td><td>$tatarikh_tb</td><td>$vorood_h</td><td>$ti_khorooj</td><td>$stat</td>	</tr>";
				$i++;
			}
		}
	$out_1 .= '</table>';
	/////
	$out_1 .= '<br/>'; 
	///nezafat
	$out_2 = '<center><h3>اتاق در وضعیت نظافت</h3></center>';
	$out_2 .= '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</th><th>کاربر ثبت کننده</th><th>نام میهمان</th><th>شماره رزرو</th><th>تاریخ ثبت </th><th>ساعت ثبت</th><th>کاربر نظافت کننده</th><th>تاریخ نظافت </th><th>ساعت نظافت</th><th>وضعیت</th></tr>';
		mysql_class::ex_sql("select * from `nezafat` where date(`mani_time`)>= '$aztarikh' and date(`mani_time`)<='$tatarikh' and `room_id`='$room_id' order by `mani_time`,`en`",$q);
		$i =1;
		while($r = mysql_fetch_array($q))
		{
			$row_style = 'class="showgrid_row_odd_nezafat"';
			if($i%2==0)
				$row_style = 'class="showgrid_row_even_nezafat"';
			$res_id = $r['reserve_id'];
			if ($res_id==-1)
				$res_id = '--';
			$tmp = explode(" ",$r['mani_time']);
			$saat = $tmp[1];
			$name_mehman = loadNameByReserve($r['reserve_id']);
			$user_sabt = loadNameByUser($r['user_id']);
			$user_nezafat = loadNameByUser($r['user_nezafat']);
			$aztarikh_tb = audit_class::hamed_pdate($r['mani_time']);
			$tarikh_nezafat = audit_class::hamed_pdate($r['nezafat_time']);
			$tmp_n = explode(" ",$r['nezafat_time']);
			$saat_n = $tmp_n[1];
			if ($r['en']==0)
				$stat = 'نظافت نشده';
			elseif ($r['en']==1)
				$stat = 'نظافت شده';
			else
				$stat = 'نامشخص';
			$out_2 .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$name_mehman</td><td>$res_id</td><td>$aztarikh_tb</td><td>$saat</td><td>$user_nezafat</td><td>$tarikh_nezafat</td><td>$saat_n</td><td>$stat</td></tr>";
			$i++;
		}
		
	$out_2 .= '</table>';
	/////
	$out_2 .= '<br/>'; 
	///tamir
	$out_3 = '<center><h3>اتاق در وضعیت تعمیرات تاسیساتی</h3></center>';
	$out_3 .= '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</th><th>کاربر ثبت کننده</th><th>توضیحات</th><th>تاریخ ثبت</th><th>کاربر رفع کننده</th><th>توضیحات</th><th>تاریخ رفع خرابی</th><th>وضعیت</th></tr>';
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
			$out_3 .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$toz</td><td>$tarikh_sabt</td><td>$user_answer</td><td>$answer</td><td>$answerdate</td><td>$stat</td></tr>";
			$i++;
		}
		
	$out_3 .= '</table>';
	/////
	$out_3 .= '<br/>'; 
	///problem
	$out_4 = '<center><h3>اتاق در وضعیت مشکل دار</h3></center>';
	$out_4 .= '<br/><table border="1" cellpadding="0" cellspacing="0" width="80%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;" ><tr class="showgrid_header" ><th>ردیف</th><th>کاربر ثبت کننده</th><th>توضیحات</th><th>تاریخ ثبت</th><th>ساعت ثبت</th><th>کاربر رفع کننده مشکل</th><th>توضیحات</th><th>تاریخ رفع مشکل</th><th>ساعت رفع مشکل</th><th>وضعیت</th></tr>';
		mysql_class::ex_sql("select * from `tasisat_tmp` where date(`regdate`)>= '$aztarikh' and date(`regdate`)<='$tatarikh' and `room_id`='$room_id' order by `regdate`",$q);
		$i =1;
		while($r = mysql_fetch_array($q))
		{
			$saat_answer = '--';
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
			$tmp_reg = explode(" ",$r['regdate']);
			$saat_sabt = $tmp_reg[1];
			$tmp_ans = explode(" ",$r['date_fix']);
			$saat_answer = $tmp_ans[1];
			$tarikh_sabt = audit_class::hamed_pdate($r['regdate']);
			if ($r['date_fix']!='0000-00-00 00:00:00')
				$answerdate = audit_class::hamed_pdate($r['date_fix']);
			else
				$answerdate = '--';
			$out_4 .="<tr $row_style><td>$i</td><td>$user_sabt</td><td>$toz</td><td>$tarikh_sabt</td><td>$saat_sabt</td><td>$user_answer</td><td>$answer</td><td>$answerdate</td><td>$saat_answer</td><td>$stat</td></tr>";
			$i++;
		}
		
	$out_4 .= '</table>';
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
			<?php echo $combo_state;?>
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
						<input type='hidden' name='state_id' id='state_id' value="<?php echo $state_id;?>" >	 
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
			</form>
			<br/>
			<br/>
			<div align="center">				
				<?php 
					if ($state_id==1)
						echo $out_1; 
					elseif ($state_id==2)
						echo $out_2;
					elseif ($state_id==3)
						echo $out_3;  
					elseif ($state_id==4)
						echo $out_4; 
					elseif ($state_id==-1)
						echo $out_1.$out_2.$out_3.$out_4; 
					else
						echo '';
				?>
			</div>
		</center>
	</body>
</html>
