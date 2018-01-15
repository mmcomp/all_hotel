<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadNesbat()
	{
		$tmp = statics_class::loadByKey('نسبت');
		$out['سرگروه'] = '-1';
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadMellait()
	{
		$tmp = statics_class::loadByKey('ملیت');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function listOtagh($inp)
	{
		$out = "";
		mysql_class::ex_sql("select `name` from `room` where `id`=$inp",$qq);
		while($row = mysql_fetch_array($qq))
			$out = $row['name'];
		return $out;
	}
	$msg = '';
	$out = '';
	$scr = '';
	$vaziat = 0;
	$room_id = -1;
	$reserve_id = -1;
	$isAdmin = $se->detailAuth('all');
	$output = '<br/><table border="1" cellpadding="0" cellspacing="0" width="95%" style="font-size:12px;border-style:solid;border-width:1px;border-color:Black;margin:10px;cell-padding:50px;" ><tr class="showgrid_header" ><th>پذیرش</th><th>شماره رزرو</th><th>هتل</th><th>نام</th><th>شماره اتاق</th><th>تعداد نفرات</th><th>قیمت هتل</th><th>جمع کل</th><th>تاریخ ورود</th><th>تاریخ خروج</th></tr>';
	$changed = FALSE;
	$room_loded = FALSE;
	$msg = '';
	$tarikh_true = TRUE;
	//var_dump($_REQUEST);
	if(!isset($_REQUEST['reserve_id']) && isset($_REQUEST['room_id']))
	{
		$tarikh = ((isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):'0000-00-00 00:00:00');
		$r_tmp = new room_class((int)$_REQUEST['room_id']);
		$room_id = (int)$_REQUEST['room_id'];
		$vaziat = $r_tmp->vaziat;
		$res = $r_tmp->getAnyReserve($tarikh);
		if ($vaziat>=5)
		{
			if(isset($_REQUEST['tarikh']) )
				if(strtotime(date("Y-m-d H:i:s")) > strtotime($tarikh))
				{
					$tarikh_true = FALSE;
					$msg = 'تاریخ درست وارد نشده است';
				}
		}
//var_dump($res);			
/*
		$_REQUEST['vaziat'] = $r_tmp->vaziat;
		$reserve_id = $reserve_id[0]['reserve_id'];
		$_REQUEST['reserve_id'] = $reserve_id;
*/
                if(isset($_REQUEST['vaziat']) && $res==null && $tarikh_true)
                {
                        $vaziat = (int)$_REQUEST['vaziat'];
                        $room_id = (int)$_REQUEST['room_id'];
                        mysql_class::ex_sqlx("update `room` set `vaziat` = $vaziat,`end_fix_date`='$tarikh' where `id` = $room_id");
                        $scr = "<script language='javascript'> window.parent.location = window.parent.location; </script>";
                }
		if($res!=null)
		{
			$_REQUEST['vaziat'] = $r_tmp->vaziat;
			$reserve_id = $res[0]['reserve_id'];
			$_REQUEST['reserve_id'] = $reserve_id;
			$room_id = (int)$_REQUEST['room_id'];
		}
		$room_loded = TRUE;
		$reserve_rooms = '<option selected="selected" value="'.$room_id.'">'.$r_tmp->name.'</option>'."\n";
	}
	else if(isset($_REQUEST['reserve_id']))
		$changed = TRUE;
	if($se->detailAuth('tasisat'))
		die('<script>window.location="tasisat.php?room_id='.$room_id.'"</script>');
	if(isset($_REQUEST['reserve_id']) && (int)$_REQUEST['reserve_id']>0)
	{
		$tarikh = date("Y-d-m h:i:s");
		$r_tmp = new room_class((int)$_REQUEST['room_id']);
		$room_id = (int)$_REQUEST['room_id'];
		$res1 = $r_tmp->getAnyReserve($tarikh);
		$res1_count = count($res1);
//var_dump($res1);
	}
	$output .='</table><br/><h3>اطلاعات پذیرش</h3>'.$out;
	$dis = ($vaziat==4 or $vaziat==5) ? '':'none';
	 $tarikh_view = ($vaziat==4 or $vaziat==5) ? audit_class::hamed_pdate($r_tmp->end_fix_date):'';
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
		function sendback()
		{
			if($("#tarikh").is(":visible"))
			{
				if($("#tarikh").val()!='')
					document.getElementById('frm1').submit();
				else
					alert('تاریخ را وارد کنید');
			}
			else
				document.getElementById('frm1').submit();
		}
		function statusCH()
		{
			var vaziat = $("#vaziat").val();
			if(vaziat=="4" || vaziat=="5")
				$("#div_tarikh").show('slow');
			else
				$("#div_tarikh").hide('slow');
		}
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
			سامانه رزرواسیون	
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<form id="frm1">
				تغییر وضعیت اتاق : 
				<select id="vaziat" class="inp" name="vaziat" onchange="statusCH()" >
					<option value="0" <?php echo (($vaziat == 0)?'selected="selected"':''); ?>>
						اشغال
					</option>
                                        <option value="1" <?php echo (($vaziat == 1)?'selected="selected"':''); ?>>
						آزاد و نظافت نشده
                                        </option>
                                        <option value="2" <?php echo (($vaziat == 2)?'selected="selected"':''); ?>>
						آزاد و نظافت شده
                                        </option>
                                        <option value="3" <?php echo (($vaziat == 3)?'selected="selected"':''); ?>>
						درحال نظافت
                                        </option>
                                        <option value="4" <?php echo (($vaziat == 4)?'selected="selected"':''); ?>>
						دردست تعمیر
                                        </option>
                                        <option value="5" <?php echo (($vaziat == 5)?'selected="selected"':''); ?>>
						خارج از سرویس
                                        </option>
				</select>
				<span id="div_tarikh" style="display:<?php echo $dis; ?>;" ><input readonly="readonly" class="inp" name="tarikh" id="tarikh" placeholder="تاریخ را وارد کنید" value="<?php echo $tarikh_view; ?>" ></span>
				<span id="div_tarikh" style="display:<?php echo $dis; ?>;" ><input readonly="readonly" class="inp" name="tarikh" id="tarikh" placeholder="تاریخ را وارد کنید" value="<?php echo $tarikh_view; ?>" ></span>
				<select id="room_id" class="inp" name="room_id" onchange="sendback();">
					<?php echo $reserve_rooms; ?>
				</select>
				<input type='button' value='اعمال' class="inp" onclick="sendback('vaziat');">
				<?php
					if($se->detailAuth('super') && $vaziat == 4)
						echo '<a href="tasisat.php?room_id='.$room_id.'&" target="_blank">تاسیسات</a>';
					if($reserve_id > 0)
						echo "<input type=\"hidden\" id=\"reserve_id\" name=\"reserve_id\" value=\"$reserve_id\" />";
				?>
			</form>
			<?php echo $output.' '.$msg; ?>
		</div>
		<?php echo $scr; ?>
	</body>
</html>
