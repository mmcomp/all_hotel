<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$msg = '';
	$is_admin = $se->detailAuth('all');
	$f_name = ((isset($_REQUEST['f_name']))?$_REQUEST['f_name']:'');
	$l_name = ((isset($_REQUEST['l_name']))?$_REQUEST['l_name']:'');
	$reserve_id = ((isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:0);
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):'0000-00-00');
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):'0000-00-00');
	$user_id=-1;
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$curtime = strtotime(date("Y-m-d"));
	$az = strtotime($aztarikh);
	$ta = strtotime($tatarikh);
	if(!$is_admin && $az - $curtime <= 24*60*60)
	{
		$aztarikh = date("Y-m-d",$curtime);
                $tatarikh = date("Y-m-d",$curtime);
	}
	$output = '<table border="1" style="font-size:12px;width:80%" ><tr><th>انتخاب</th><th>شماره رزرو</th><th>هتل</th><th>نام و نام خانوادگی</th><th>تعداد نفرات</th><th>اتاق</th><th>قیمت هتل</th><th>جمع کل</th></tr>';
	if(isset($_REQUEST['mod']) && (int)$_REQUEST['mod']==2)
	{
		//($aztarikh,$tatarikh,$user_id,$isAdmin,$fname,$lname,$reserve_id,$just_date=TRUE)
		$aztarikh = explode(" ",$aztarikh);
		$aztarikh = $aztarikh[0];
		$tatarikh = explode(" ",$tatarikh);
		$tatarikh = $tatarikh[0];
		$tmp = room_det_class::loadReserve_id($aztarikh,$tatarikh,$user_id,$is_admin,$f_name,$l_name,$reserve_id);
		for($i=0;$i<count($tmp);$i++)
		{
			//$room = room_det_class::loadDetByReserve_id($tmp[$i]);
			//$name = room_det_class::loadNamesByReserve_id($tmp[$i]);
			//$khadamat = room_det_class::loadKhadamatByReserve_id($tmp[$i]);
			$room_det_new = room_det_class::loadByReserve($tmp[$i]);
			$room_det_new = $room_det_new[0];
			$horel_reserve = new hotel_reserve_class;
			$horel_reserve->loadByReserve($tmp[$i]);
			//for($j=0;$j<count($room['rooms']);$j++)
			for($j=0;$j<count($room_det_new);$j++)
			{
				$room_det_tmp = new room_class($room_det_new[$j]->room_id);
				$room_det_typ = new room_typ_class($room_det_tmp->room_typ_id);
				$hotel_tmp = new hotel_class($room_det_tmp->hotel_id);
				$output .="<tr>";
				if($j==0)
					$output .="<td  rowspan=".count($room_det_new)." ><input onclick=\"set_value('td_".$tmp[$i]."')\" type='radio' value='".$tmp[$i]."' name='reserve_id' ></td>";
				else
					$output .='&nbsp;';
				$output .="<td>".$tmp[$i]."</td>";
				$output .="<td>".$hotel_tmp->name."</td><td>".$horel_reserve->fname.' '.$horel_reserve->lname."</td><td>".$room_det_new[$j]->nafar."</td><td>".$room_det_tmp->name.' '.$room_det_typ->name."</td><td>".monize($horel_reserve->m_hotel)."</td>";
				$output .="<td ".(($j==0)?"id='td_".$tmp[$i]."'":'')." >".monize($horel_reserve->m_belit+$horel_reserve->m_hotel)."</td></tr>";
			}
		}
	}
	$output .="<tr><td colspan=\"8\" ><input class=\"inp\" type=\"hidden\" name=\"mablagh\" id=\"mablagh\" >توضیحات : <input name=\"toz\" class=\"inp\" style='width:300px;' /><input class=\"inp\" type=\"button\" value=\"ثبت کنسلی\" onclick=\"document.getElementById('frm1').submit();\" ></td></tr></table>";
	if(isset($_REQUEST['mablagh']) && isset($_REQUEST['reserve_id']) && (int)$_REQUEST['mod']==1 )
	{
		$reserve_id = (int)$_REQUEST['reserve_id'];
		$ghimat = umonize($_REQUEST['mablagh']);
		$toz = $_REQUEST['toz'];
		$refunded = room_det_class::refundReserve($reserve_id,$toz);
		$reserve_id = abs($reserve_id) * (-1);
		for($i=0;$i<count($refunded);$i++)
		{
			mysql_class::ex_sqlx("insert into `sanad_reserve` (`reserve_id`,`sanad_record`) values ('$reserve_id','".$refunded[$i]."') ");
		}
		$msg = '<script type="text/javascript" >alert("کنسلی با موفقیت انجام شد");//window.location = "refund.php";</script>';
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
			<?php
				if(!$is_admin)
				{
			?>
تذکر: کلیه رزروهایی که بیشتر از ۲۴ ساعت به ورود آنها باقیست قابل کنسل شدن می‌باشند
			<?php
				}
			?>
			<br/>
			<form id='frm1'  method='GET' >
			<table border='1' style='font-size:12px;' >
				<tr>
					<th>شماره رزرو</th>
					<th>نام</th>
					<th>نام خانوادگی</th>
					<th>تاریخ ورود</th>
					<th>تاریخ خروج</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					<td>	
						<input class='inp' name='reserve_id' id='reserve_id' value="<?php echo ((isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:''); ?>" >
					</td>
					<td>
                                                <input class='inp' name='f_name' id='f_name' value="<?php echo $f_name; ?>" >
                                        </td>
					<td>	
						<input class='inp' name='l_name' id='l_name' value="<?php echo $l_name; ?>" >
					</td>
					<td>	
         					   <input value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh' class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td>
						<input value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='inp' style='direction:ltr;' id="datepicker7" />
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
	</body>
</html>
