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
		mysql_class::ex_sql("select `id`,`name` from `khadamat` where `en`=1 and `hotel_id`=".(int)$_REQUEST['h_id'],$q);
		while($r = mysql_fetch_array($q))
		{
			$select='';
			if((int)$r['id']==$sel)
				$select='selected="selected"';
			$out.="<option $select value='".$r['id']."' >".$r['name']."</option>\n";
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
	$khadamat_id =((isset($_REQUEST['khadamat_id']))?(int) $_REQUEST['khadamat_id']:0)  ;
	$cost_tedad = ((isset($_REQUEST['cost_tedad']))?(int) $_REQUEST['cost_tedad']:0)  ;	
	if( isset($_REQUEST['h_id']))
        {
		$h_id = $_REQUEST['h_id'];
		$h_id = (int) $_REQUEST['h_id'];
		$hotel = new hotel_class($h_id);
		$hotel_name =$hotel->name;
		$tarikh =((isset($_REQUEST['tarikh']))?audit_class::hamed_pdateBack($_REQUEST['tarikh']):date("Y-m-d"));
		$tarikh1 = date("Y-m-d",strtotime($tarikh));
		$tmp = explode(' ',$tarikh);
		$tarikh = $tmp[0];
                $frm="";
		$sum = 0;
		$out = '<table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>نام سرگروه</th><th>شماره اتاق</th><th>نفرات</th><th>شماره-رزرو</th><th>اطلاعات بیشتر</th><th>دفتر</th><th>آژانس</th><th>وضعیت</th><th>تعداد</th><th>تاریخ ورود</th><th>تاریخ خروج</th></tr>';
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
//var_dump(reserve_class::isKhorooj($tmp[$i],$room['rooms'][$j]['room_id'])).'<br/>';
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
			/*-----------------------------------------------
			mysql_class::ex_sql("select `aztarikh` , `tatarikh`,`nafar` from `room_det` where `reserve_id`>0 and `reserve_id` = ".$r['reserve_id'],$qt);
			$nafar = -1;
			if($rr = mysql_fetch_array($qt))
			{
				$kh_tarikh = date("Y-m-d",strtotime($r['tarikh']));
				$room_aztarikh = date("Y-m-d",strtotime($rr['aztarikh']));
				$room_tatarikh = date("Y-m-d",strtotime($rr['tatarikh']));
				$status = '';
				if($kh_tarikh==$room_aztarikh)
					$status = 'اول';
				else if ($kh_tarikh==$room_tatarikh)
					$status = 'آخر';
				else
					$status = 'میانی';
				$nafar = $rr['nafar'];
			}
			*///-----------------------------------------------
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
			if($i%2==0)
				$row_style = 'class="showgrid_row_even"';
			$out.="<tr $row_style >";
			$out .="<td class='showgrid_row_td' >$i</td><td class='showgrid_row_td' >".$hotel_tmp->lname."</td><td class='showgrid_row_td' width='30%'>$rooms</td><td class='showgrid_row_td' >$nafar</td><td class='showgrid_row_td' >".$r['reserve_id']."</td><td class='showgrid_row_td' >".$hotel_tmp->tozih."</td><td class='showgrid_row_td' >".$daftar->name."</td><td class='showgrid_row_td' >".$ajans->name."</td><td class='showgrid_row_td' >$status</td><td class='showgrid_row_td' >".$r['tedad']."</td><td class='showgrid_row_td'>$room_aztarikh1</td><td class='showgrid_row_td'>$room_tatarikh1</td>";
			$out.='</tr>';
		}
		$out.='<tr class="showgrid_row_odd" ><td colspan="3" style="text-align:left;">جمع نفرات : </td><td align="center"  >'.$nafar_kol.'</td><td colspan="5" style="text-align:left;">جمع : </td><td align="center"  >'.$sum.'</td><td></td><td></td></tr>';
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
			$out .="<tr class='showgrid_row_even' ><td style='text-align:left;'>خروج از انبار : </td><td>".loadAnbar()."</td><td>تحویل گیرنده:</td><td>".loadUsers()."</td><td align='left' >تعداد:</td><td><input name='cost_tedad' id='cost_tedad' class='inp' style='width:40px;' value='$cost_tedad' onkeypress='return numbericOnKeypress(event);' >از: ".($sum - $jam_kol)."</td><td colspan='6'>".loadKalaTarkibi()."<input type='button' value='چاپ رسید خروج از انبار' class='inp' $disable onclick='send_info($khadamat_id,$sum);'>$pm</td></tr>\n";
			$out .="</table><br/>\n";
			//--------------------------------
			$grid = new jshowGrid_new("cost_anbar","grid1");
			$grid->whereClause=" date(`tarikh`)= '$tarikh1' and `khadamat_id`=$khadamat_id";
			$grid->columnHeaders[0] = null;
			$grid->columnHeaders[1] = "کالای ترکیبی";
		       	$grid->columnHeaders[2] =null ;
			$grid->columnHeaders[3] = null;
			$grid->columnFunctions[1]='loadCost';
			$grid->columnHeaders[4] = 'تعداد خارج شده';
			$grid->canAdd = FALSE;
			$grid->canEdit = FALSE;
			$grid->canDelete = FALSE;
			$grid->intial();
		   	$grid->executeQuery();
			$out .= $grid->getGrid();
		}
		else
			$out .="</table><br/>\n";
        }
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"GET\">";
		$combo_hotel .= "هتل : <select class='inp' id=\"hotel_id\" name=\"h_id\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel` order by `name`",$q);
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
		خدمات هتل		
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
			<?php echo $combo_hotel; ?>
			<form id="frm1" method="GET">
				<table id='combo_table' >
					<tr>
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
