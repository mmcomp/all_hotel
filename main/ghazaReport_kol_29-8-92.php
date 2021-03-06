<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);

	function loadGrp($inp)
	{
		$out = "";
		if($inp!="")
		{
		$out = hesab_class::idToName("grooh",$inp);
		}
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	
	function loadKol($inp)
        {
                $out = hesab_class::idToName("kol",$inp);
                return $out;
        }
        function loadMoeen($inp)
        {
                $out = hesab_class::idToName("moeen",$inp);
                return $out;
        }
        function loadTafzili($inp)
        {
                $out = hesab_class::idToName("tafzili",$inp);
                return $out;
        }
        function loadTafzili2($inp)
        {
                $out = hesab_class::idToName("tafzili2",$inp);
                return $out;
        }
        function loadTafzilishenavar($inp)
        {
                $out = hesab_class::idToName("tafzilishenavar",$inp);
                return $out;
        }
	function loadTafzilishenavar2($inp)
        {
                $out = hesab_class::idToName("tafzilishenavar2",$inp);
                return $out;
        }

	function loadBes($inp)
	{
		$out = (int)$inp;
             	$out =(( $out>0)?abs( $out):"---");
		return monize($out);
	}
	function loadBed($inp)
	{
                $out = (int)$inp;
             	$out =(( $out<0)?abs( $out):"---");
		return monize($out);
	}
	function echoer($id)
	{
		echo "id = '$id'<br/>\n";
		return($id);
	}
	function loadMande($inp)
	{
                $out = (int)$inp;
		if($out == 0)
			$out = "۰";
		if($out>0)
			$out = "بستانکار <br/>".enToPerNums(monize(abs($out)));
		else if($out<0)
			$out = "بدهکار <br/>".enToPerNums(monize(abs($out)));
                return($out);
	}
	function hamed_pdateBack($inp)
	{
		$out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
                        $y=(int)$tmp[2];
                        $m=(int)$tmp[1];
                        $d=(int)$tmp[0];
                        if ($d>$y)
                        {
                                $tmp=$y;
                                $y=$d;
                                $d=$tmp;
                        }
                        if ($y<1000)
                        {
                                $y=$y+1300;
                        }
                        $inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out." 12:00:00";
	}
	function loadReserve($inp)
	{
		$out="---";
		$color="blue";
		mysql_class::ex_sql("select `id` from `reserve` where `sanad_id`='$inp'",$q);
		if (mysql_num_rows($q)>0)
		{
			$out="<u><span style=\"color:$color;cursor:pointer;\" onclick=\"wopen('sanad_cia.php?sanad=$inp&','',800,300);\" >مشاهده </span></u>";
		}
		return $out;
	}
	function loadKhad($sel=0)
	{
		$out = '';
		$sel = (int)$sel;
		mysql_class::ex_sql("select `id`,`name`,`ghazaAst` from `khadamat` where `en`=1 and `hotel_id`=".(int)$_REQUEST['h_id'],$q);
		while($r = mysql_fetch_array($q))
		{
			$select='';
			if((int)$r['id']==$sel)
				$select='selected="selected"';
		
			if($r['ghazaAst']!=1) 
				continue;
			else $out.="<option $select value='".$r['id']."' >".$r['name']."</option>\n";
		}
		return $out;
	}
	function loadKalaTarkibi()
	{
		$out ='<select class="inp" name="kala_cost" id="kala_cost" >';
		mysql_class::ex_sql("select `id`,`name` from `cost_kala` where `is_personal`=0 order by `name`",$q);
		while($r=mysql_fetch_array($q))
			$out .="<option value='".$r['id']."' >".$r['name']."</option>\n";
		$out .='</select>';
		return $out;
	}
	function loadAnbar()
	{
		$out = '<select class="inp" name="anbar_id" id="anbar_id" >';
		mysql_class::ex_sql('select `name`,`id` from `anbar` where `en`<>2 order by `name`',$q);
		while($r = mysql_fetch_array($q))
			$out.= "<option  value='".$r['id']."' >".$r['name']."</option>\n";
		$out .='</select>';
		return($out);
	}
	function loadUsers()
	{
		$out = '<select class="inp" name="gUser_id" id="gUser_id" >';
		mysql_class::ex_sql('select `lname`,`fname`,`id` from `user` where `user`<>\'mehrdad\' order by `lname`,`fname`',$q);
		while($r = mysql_fetch_array($q))
			$out.= "<option  value='".$r['id']."' >".$r['lname'].' '.$r['fname']."</option>\n";
		$out .='</select>';
		return($out);
	}
	function loadCost($inp)
	{
		$cost = new cost_kala_class($inp);
		return $cost->name;
	}
	
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	if (isset($_REQUEST["h_id"]))
                $h_id = $_REQUEST["h_id"];
	else
		$h_id = -1;
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
		$combo_hotel .= "هتل : <select class='inp' id=\"hotel_id\" name=\"h_id\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel` where `id` in $shart order by `name`",$q);
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
	$combo_hotel .= "</form>";
	$hotel_name = '';
	$out = '';
	$tarikh = date('Y-m-d');
	$khadamat_id =((isset($_REQUEST['khadamat_id']))?(int) $_REQUEST['khadamat_id']:0)  ;
	$cost_tedad = ((isset($_REQUEST['cost_tedad']))?(int) $_REQUEST['cost_tedad']:0)  ;
	if( isset($_REQUEST['h_id']))
        {
		$h_id = (int) $_REQUEST['h_id'];
		$hotel = new hotel_class($h_id);
		$hotel_name =$hotel->name;
		$tarikh =((isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):date("Y-m-d"));
		$tarikh1 = date("Y-m-d",strtotime($tarikh));
		$tmp = explode(' ',$tarikh);
		$tarikh = $tmp[0];
                $frm="";
		$sum = 0;
		$out = '<table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>نام سرگروه</th><th>شماره اتاق</th><th>نفرات</th><th>شماره-رزرو</th><th>اطلاعات بیشتر</th></tr>';
		$q=null;
		if(isset($khadamat_id) && isset($tarikh))
			mysql_class::ex_sql("select * from `khadamat_det` where `reserve_id`>0 and DATE(`tarikh`) = '$tarikh' and `khadamat_id`=$khadamat_id ",$q);
		else
			mysql_class::ex_sql("select * from `khadamat_det` where `reserve_id`>0 and 1=0",$q);
		$i=0;
		$nafar_kol=0;
		$day = date("Y-m-d");
		while($r=mysql_fetch_array($q))
		{
			$room = room_det_class::loadDetByReserve_id((int)$r['reserve_id']);
			$rooms = '';
			for($j=0;$j<count($room['rooms']);$j++)
			{
				$tmp_room = new room_class($room['rooms'][$j]['room_id']);
				$rooms.=$tmp_room->name.(($j<count($room['rooms'])-1)?' , ':'');
			}
			$i++;
			$status = '';
			$tmp_r = room_det_class::loadByReserve((int)$r['reserve_id']);
			$tmp_r = $tmp_r[0];
			$room_aztarikh = date("Y-m-d",strtotime($tmp_r[0]->aztarikh));
			$room_tatarikh = date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh));
			$room_aztarikh1 = audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[0]->aztarikh)));
			$room_tatarikh1 =audit_class::hamed_pdate(date("Y-m-d",strtotime($tmp_r[count($tmp_r)-1]->tatarikh)));
			$kh_tarikh = date("Y-m-d",strtotime($r['tarikh']));
			if($kh_tarikh==$room_aztarikh)
				$status = 'ورودی';
			else if ($kh_tarikh==$room_tatarikh)
				$status = 'خروجی';
			else
				$status = 'میانی';
			$nafar = $tmp_r[0]->nafar;
			$nafar_kol+= $nafar;
			$hotel_tmp = new hotel_reserve_class();
			$hotel_tmp->loadByReserve($r['reserve_id']);
			$ajans = new ajans_class($hotel_tmp->ajans_id);
			$daftar = new daftar_class($ajans->daftar_id);
			$row_style = 'class="showgrid_row_odd"';
			if($status != '')
				$sum+=(int)$r['tedad'];
			else
			{
				$status = '<span style="color:red;">رزرو بررسی شود</span>';
				$r['tedad'] = 0;
			}
			$ghaza = "";
			$r_res = $r['reserve_id'];
			mysql_class::ex_sql("select `id` from `khadamat_det` where `khadamat_id` = $khadamat_id and `reserve_id`='$r_res' and DATE(`tarikh`) = '$tarikh'",$q1);
			while($r1= mysql_fetch_array($q1))
			{
				$kh_det_id = $r1["id"];
				mysql_class::ex_sql("select `sandogh_item_id`,`tedad_kol` from `khadamat_det_front` where `khadamat_det_id` = $kh_det_id",$qu);
				while($row= mysql_fetch_array($qu))
				{
					$sandogh_id = $row["sandogh_item_id"];
					$tedad = $row["tedad_kol"];
					mysql_class::ex_sql("select `id`,`name` from `sandogh_item` where `id` = $sandogh_id",$quu);
					if($ro= mysql_fetch_array($quu))
					{
						$name_ghaza = $ro["name"];
			
					}
					$ghaza .= $name_ghaza.'(تعداد:'.$tedad.')';
				}
			}
			if($i%2==0)
				$row_style = 'class="showgrid_row_even"';
			$out.="<tr $row_style >";
			$out .="<td class='showgrid_row_td' >$i</td><td class='showgrid_row_td' >".$hotel_tmp->lname."</td><td class='showgrid_row_td' width='30%'>$rooms</td><td class='showgrid_row_td' >$nafar</td><td class='showgrid_row_td' >".$r['reserve_id']."</td><td class='showgrid_row_td' >".$ghaza."</td>";
			$out.='</tr>';
		}
		$out.='<tr class="showgrid_row_odd" ><td colspan="3" style="text-align:left;">جمع نفرات : </td><td align="center"  >'.$nafar_kol.'</td><td colspan="4" style="text-align:left;"></td></tr>';
		$khad = new khadamat_class($khadamat_id);
		$hotel_kh = new hotel_class($khad->hotel_id);
		if( $conf->cost_control && ($khad->typ==0 && $sum>0 && ($se->detailAuth('all') || $se->detailAuth('anbar_dari'))) )
		{
			$disable = '';
			$pm = '';
			if($hotel_kh->ghaza_moeen_id<0)
			{
				$disable = 'disabled="disabled"';
				$pm = '<span style="color:red" >حساب معین هزینه غذا برای هتل ثبت نشده است</span>';
			}
			mysql_class::ex_sql("select sum(`tedad`) as `jam` from `cost_anbar` where `khadamat_id`='$khadamat_id' and date(`tarikh`)='$tarikh1'",$q);
			if($r = mysql_fetch_array($q))
				$jam_kol = (int)$r['jam'];
			
			$out .="</table><br/>\n";
			//--------------------------------
		}
		else
			$out .="</table><br/>\n";
		$out .= "<br/>";
		$out .= "<table>";
		$out .= '<table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" >
		<th>ردیف</th><th>نام غذا </th><th>تعداد رزرو شده</th><th>تعداد صرف شده</th><th>تعداد بافیمانده</th>
		</tr>';
		$b = array();
		$i = 1;
		mysql_class::ex_sql("select `id` from `khadamat_det` where `khadamat_id` = $khadamat_id and DATE(`tarikh`) = '$tarikh'",$q1);
		while($r1= mysql_fetch_array($q1))
		{
			$kh_det_id = $r1["id"];
			$b[] = $kh_det_id;
		}
		if(count($b)>0)
		{
			mysql_class::ex_sql("select `sandogh_item_id`,sum(`tedad_used`) as `us`,sum(`tedad_kol`) as `tkol` from `khadamat_det_front` where `khadamat_det_id` in (".implode(',',$b).") group by `sandogh_item_id` ",$qu);
			$total_tedad=0;
			$total_used=0;
			$total_remain=0;
			$used=0;
			$tkol=0;
			while($row= mysql_fetch_array($qu))
			{
				if($i%2==0)
					$row_style = 'class="showgrid_row_even"';
				$sandogh_id = $row["sandogh_item_id"];
				$tedad = $row["tkol"];
				$used=$row['us'];
				$tkol=$tedad-$used;
				$total_tedad+=$tedad;
				$total_used+=$used;
				$total_remain+=$tkol;
				mysql_class::ex_sql("select `id`,`name` from `sandogh_item` where `id` = $sandogh_id",$quu);
				if($ro= mysql_fetch_array($quu))
					$name_ghaza = $ro["name"];
				$out .= "<tr $row_style><td class='showgrid_row_td'>$i</td>
				<td class='showgrid_row_td'>$name_ghaza</td><td class='showgrid_row_td'>$tedad</td>
				<td align='center'>$used</td>
				<td align='center'>$tkol</td>
				</tr>";
				$row_style = 'class="showgrid_row_odd"';
				$i++;
			}
		$out.="<tr bgcolor='yellow' align='center'>
		<td></td><td>جمع</td>
		<td>$total_tedad</td><td>$total_used</td><td>$total_remain</td>
		</tr>";
		}
		$out .= "</table>";
        }

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
				گزارش غذا		
		</title>
		<script type="text/javascript">
		function sbtFrm()
		{
			document.getElementById('frm1').submit();
		}
		function getPrint()
		{
			document.getElementById('div_main').style.width = '18cm';
			window.print();
			document.getElementById('div_main').style.width = 'auto';
		}
		function send_info(khadamat,cost_jam)
		{
			var cost_tedad = document.getElementById('cost_tedad').value;
			if(cost_tedad==0)
				alert('تعداد را وارد کنید');
			else
			{
				if(cost_jam<cost_tedad)
						alert('تعداد وارد شده بیش از مجموع  است');
				else
				{
					if(confirm('آیا کالا با جزئیات از انبار خارج شود؟'))
					{
				
						var gUser_id = document.getElementById('gUser_id').options[document.getElementById('gUser_id').selectedIndex].value;
						var anbar_id = document.getElementById('anbar_id').options[document.getElementById('anbar_id').selectedIndex].value;
						var tarikh = document.getElementById('tarikh1').value;
						var kala_cost = document.getElementById('kala_cost').options[document.getElementById('kala_cost').selectedIndex].value;
						wopen('cost_anbar.php?khadamat_id='+khadamat+'&max_tedad='+cost_jam+'&cost_tedad='+cost_tedad+'&kala_cost='+kala_cost+'&tarikh='+tarikh+'&anbar_id='+anbar_id+'&gUser_id='+gUser_id+'&','',600,400);
					}
				}
			}
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
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<form method="POST" name="frmtedad" id ="frmtedad">
        	<input name="txttedad" id="txttedad" type="hidden" value="1"/>
	        </form>

		<div align="center" id="div_main" >
			<br/>
			<?php echo $combo_hotel;?>
			<br/>
			<form id="frm1" method="GET">
				<table id='combo_table' >
					</tr>
						<td>
							<label> <?php echo $hotel_name; ?> </label>
						</td>
						<td>
							<select name="khadamat_id" id="khadamat_id" class="inp">
								<?php echo loadKhad($khadamat_id); ?>
							</select>
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
							<input class="inp" type="hidden" name="h_id" id="h_id" value="<?php echo $h_id; ?>"  >
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
