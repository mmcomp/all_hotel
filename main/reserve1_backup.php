<?php
	session_start();
	include("../kernel.php");
        if (isset($_SESSION['user_id']) && isset($_SESSION['typ']))
        {
               //
        }
        else
        {
                        die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
        }
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$out = '<select name="hotel_id" class="inp" style="width:auto;" >';
		mysql_class::ex_sql('select `id`,`name` from `hotel` where `moeen_id` > 0 order by `name` ',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadNumber($inp=-1)
	{
		$out = '';
		$inp = (int)$inp;
		for($i=1;$i<10;$i++)
		{
			$sel = (($i==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='$i' >$i</option>\n";
		}
		return $out;
	}
	function loadDaftar($inp)
	{
		$inp = (int)$inp;
		$out = "<select name=\"daftar_id\" id=\"daftar_id\" class=\"inp\" style=\"width:auto;\" onchange=\"document.getElementById('mod').value='2';document.getElementById('reserve').submit();\" >";
		if($_SESSION["typ"] ==0)
			mysql_class::ex_sql('select `id`,`name` from `daftar` order by `name` ',$q);
		if($_SESSION["typ"] !=0)
			mysql_class::ex_sql('select `id`,`name` from `daftar` order by `name` where `id`='.$inp,$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;	
	}
	function loadAjans($daftar_id=-1)
	{
		$daftar_id = (int)$daftar_id;
		$out = "<select name=\"ajans_id\" class=\"inp\" style=\"width:auto;\"  >";
		mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$daftar_id)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadKhadamat($hotel_id)
	{
		$out = '';
		$hotel_id = (int) $hotel_id;
		$kh = khadamat_class::loadKhadamats($hotel_id);
		for($i=0;$i<count($kh );$i++)
		{
			$inp = $inp = 	"<input type='checkbox' name='kh_ch_".$kh[$i]['id']."' >";
			if($kh[$i]['typ']==0)
			{
				$inp = 	"تعداد:<input type='text' class='inp' style='width:30px;' name='kh_txt_".$kh[$i]['id']."' value='".((isset($_REQUEST['kh_'.$kh[$i]['id']]))?$_REQUEST['kh_'.$kh[$i]['id']]:0)."'  >";
			}
			$ghimat = "قیمت‌واحد:<input class='inp' style='width:70px' name='kh_gh_".$kh[$i]['id']."' value='".((isset($_REQUEST['kh_'.$kh[$i]['ghimat']]))?$_REQUEST['kh_'.$kh[$i]['ghimat']]:$kh[$i]['ghimat'])."' >";
			if(($i % 2) == 0)
				$out .="<tr>";
			$out .="<td>".$kh[$i]['name'].":</td><td>$inp $ghimat</td>";
			if(($i % 2) == 1)
				$out .="</tr>";
		}
		return $out;
	}
	$msg = '';
	if($_SESSION["typ"] ==0)
		$daftar_id = ((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
	if($_SESSION["typ"] !=0)
		$daftar_id = (int)$_SESSION["daftar_id"] ;
	$hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$shab = ((isset($_REQUEST['shab']))?(int)$_REQUEST['shab']:-1);
	$shab_reserve = ((isset($_POST['shabreserve']))?TRUE:FALSE);
	$rooz_reserve = ((isset($_POST['roozreserve']))?TRUE:FALSE);
	$output = '';
	$rooms = array();
	if(isset($_REQUEST['hotel_id']))
	{
		$hotel = new hotel_class($hotel_id);
		$tatarikh = $hotel->addDay($aztarikh,$shab);
		if ($hotel->hotelAvailableBetween($aztarikh,$tatarikh))
			$rooms = room_class::loadOpenRooms($aztarikh,$shab,$shab_reserve,$rooz_reserve,$hotel_id);
	}
	if($hotel_id>0 && count($rooms)>0)
	{
		$output='<form metod="GET"  id="reserve" ><table border="1" style="width:80%;" >';
		$output .='<tr><th>انتخاب</th><th>ظرفیت</th><th>موجود</th> <th>قیمت</th></tr>';
		if ($hotel->hotelAvailableBetween($aztarikh,$tatarikh))
		{
			for($i=0;$i<sizeof($rooms);$i++)
			{
				$checked = ((isset($_REQUEST['room_typ_id']) && $_REQUEST['room_typ_id']==$rooms[$i]['room_typ_id'] )?'checked="checked"':'');
				$output .= "<tr><td><input type='radio' name='room_typ_id' value='".$rooms[$i]['room_typ_id']."' $checked ></td>";
				$output .= "<td>".$rooms[$i]['name']."</td>";
				$output .= "<td>".$rooms[$i]['count']."</td>";
				$output .= "<td><input class='inp' name='room_gh_".$rooms[$i]['room_typ_id']."' value='".$rooms[$i]['ghimat']."' > </td><tr>";
			}
			$output .= "<tr><td colspan='3'>تعداد درخواستی:<input type='text' value='".((isset($_REQUEST['tedad_otagh']))?$_REQUEST['tedad_otagh']:1)."'  name='tedad_otagh' class='inp' ></td>";
			$output .="<td >تعدادنفرات:<input type='text' name='tedad_nafarat' value='".((isset($_REQUEST['tedad_nafarat']))?$_REQUEST['tedad_nafarat']:1)."' class='inp' ></td></tr>";
			$output .= "<tr><td>نام دفتر</td><td>".loadDaftar($daftar_id)."</td><td>نام آژانس</td><td colspan='1' >". loadAjans($daftar_id)."</td>";
			$output .= "</tr>";
			$output .=loadKhadamat($hotel_id);
			if($shab_reserve || (isset($_REQUEST['shabreserve_gh']) && (int)$_REQUEST['mod']==2 ) )
			{
				$output .= "<tr><td>شب-رزرودارد:<input class='inp'  name=\"shabreserve_gh\" id=\"shabreserve_gh\" type=\"text\"".((isset($_REQUEST['shabreserve_gh']))?$_REQUEST['shabreserve_gh']:0)." ></td>";
			}
			else
			{
				$output .= '<tr><td>شب-رزروندارد:</td>';
			}
			if($rooz_reserve )
			{
				$output .= "<td>روز-رزرودارد<input  class='inp' name=\"roozreserve_gh\" id=\"roozreserve_gh\" type=\"text\"".((isset($_REQUEST['roozreserve_gh']))?$_REQUEST['roozreserve_gh']:0)." > </td>";
			}
			else
			{
				$output .= '<td>روز-رزروندارد</td>';
			}
			$output .= "<td colspan='2' ><input type='button' onclick=\"if(radioChecked()){document.getElementById('reserve').action='reserve2.php';document.getElementById('reserve').submit();}else{alert('لطفاً نوع اتاق یا نام دفتر را درصورت تعیین نکردن ، انتخاب نمایید');}\" value='رزرو'  class='inp' ></td></tr>";
			$output .= "<input type='hidden' name='hotel_id' value='".$hotel_id."' >";
			$output .= "<input type='hidden' name='shab' value='".$shab."' >";
			$output .= "<input type='hidden' name='aztarikh' value='".$_REQUEST['aztarikh']."' >";
			
			$output .= "<input type='hidden' name='mod' id='mod' value='1' >";
			$output .= '</table></form>';
		}
		else
		{
			$msg  = '<script>alert("رزرو هتل در این بازه تاریخی امکان پذیر نیست");</script>';		
		}
	}
	else
	{
		$output .= "اتاقی موجود نیست";
	}
        
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script  type="text/javascript" >	
			function radioChecked()
			{
				var out = false;
				var inps = document.getElementsByTagName('input');
				for(var i=0;i < inps.length;i++)
					if(inps[i].type=='radio' && inps[i].checked)
						out = true;
				if(document.getElementById('daftar_id').selectedIndex <= 0 )
					out = false;
				return(out);
			}
		</script>
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
    </script>
		<style>
			td{text-align:center;}
		</style>
		<title>
			سامانه رزرواسیون	
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<form id='frm1'  method='POST' >
			<table border='1' >
				<tr>
					<th>نام هتل</th>
					<th>تاریخ</th>
					<th>مدت اقامت</th>
					<th>شب-‌رزرو<br/>(نیم شارژ ورودی)</th>
					<th>روز-‌رزرو<br/>(نیم شارژ خروجی)</th>
					<th>جستجو</th>
				</tr>
				<tr>
					<td>
						<?php 
							if(isset($_GET['h_id']))
								echo loadHotel((int)$_GET['h_id']);
							else
								echo loadHotel($hotel_id); 
						?>
					</td>
					<td>	
         					   <input value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td>
						<select  class='inp' name='shab' >
							<?php  echo loadNumber($_REQUEST['shab']); ?>
						</select>
					</td>
					<td>
						<input name="shabreserve" id="shabreserve" type="checkbox" <?php echo ((isset($_REQUEST['shabreserve']))?'checked="checked"':''); ?> >
					</td>
					<td>
						<input name="roozreserve" id="roozreserve" type="checkbox" <?php echo ((isset($_REQUEST['roozreserve']))?'checked="checked"':''); ?> >
					</td>
					<td>
						<input type='button' value='جستجو' class='inp' onclick='if(document.getElementById("mod"))document.getElementById("mod").value=0;document.getElementById("frm1").submit();' >
					</td>					
				</tr>
			</table>
			</form>
			<?php echo $output.' '.$msg; ?>
		</div>
	</body>
</html>
