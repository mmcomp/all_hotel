<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = ($se->detailAuth('all') || $se->detailAuth('reserve') || $se->detailAuth('hesabdar') );
	$GLOBALS["jam_bed"] = ((isset($_REQUEST["jam_bed"]))?(int)$_REQUEST["jam_bed"]:0);
	$GLOBALS["jam_bes"] = ((isset($_REQUEST["jam_bes"]))?(int)$_REQUEST["jam_bes"]:0);
	$GLOBALS["jam_man"] = 0;
        
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
	function loadTozih($inp)
	{
		$inp = (int)$inp;
		$out = "---";
		mysql_class::ex_sql("select * from `sanad_reserve` where  `sanad_record` = $inp",$q);
		if($r = mysql_fetch_array($q))
		{
			$reserve_id = (int)$r['reserve_id'];
			$q = null;
			$lname = '';
			$nafar = 0;
			$shab = 0;
			$aztarikh = '';
			$tatarikh = '';
			$room = null;
			$hotel = "";
			mysql_class::ex_sql("select `lname` from `hotel_reserve` where `reserve_id` = $reserve_id",$q);
			if($r = mysql_fetch_array($q))
				$lname = $r['lname'];
			$q = null;mysql_class::ex_sql("select * from `room_det` where `reserve_id` = $reserve_id",$q);
                        if($r = mysql_fetch_array($q))
			{
				$aztarikh = jdate("d / m / Y",strtotime($r['aztarikh']));
				$tatarikh = jdate("d / m / Y",strtotime($r['tatarikh']));
				$nafar = $r['nafar'];
				$room = new room_class((int)$r['room_id']);
				$hotel = new hotel_class((int)$room->hotel_id);
				$hotel=$hotel->name;
			}
			$out = "$lname تعداد نفرات $nafar نفر از $aztarikh تا $tatarikh جهت ".$hotel;
		}
		else
                {
                        mysql_class::ex_sql("select `tozihat` from `sanad` where `id`='$inp'",$q);
                        if ($r = mysql_fetch_array($q))
                        {
                                $out = $r["tozihat"];
                        }
                }
		return $out;
	}
	$hes  = new hesab_class();
	$qu = "1=0";
	mysql_class::ex_sql("select count(`id`) from `sanad` where 1=0",$co);
	if(isset($_GET["check"]) && (int)$_GET["check"]==2  )
	{
		$he = new hesab_class();
		$he = $he->hesab ;
		$shart = "";
		$sdate = hamed_pdateBack($_GET["sdate"]);
		$edate = hamed_pdateBack($_GET["edate"]);
		$sfrase = $_REQUEST["sfrase"];
		foreach($he as $key=>$value)
		{
			if((int)$_GET[$key]!==-1)
			{
				$shart .= " $key=".$_GET[$key]." and ";
			}
		}
		$qu = "$shart  `en`=1 and `tarikh`>='$sdate' and `tarikh`<='$edate'".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'');
		mysql_class::ex_sql("select count(`id`) from `sanad` where `en`=1 and `tarikh`>='$sdate' and `tarikh`<='$edate' ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `name`",$co);
	}
	if(!$isAdmin)
        {
                $vals = null;
		$daftar_id = $_SESSION['daftar_id'];
		$kol_daftar = new daftar_class($daftar_id);
                //$vals[] = $_SESSION["kol_id"];
		$vals[] = $kol_daftar->kol_id;
		if(isset($_REQUEST["moeen_id"]))
		{
			$vals[] = (int)$_REQUEST["moeen_id"];
		}
                $hes->load($vals);
        }

	if(isset($_REQUEST["kol_id"]) && $isAdmin  )
	{
		$vals[] = (int)$_REQUEST["kol_id"];
		$vals[] = (int)$_REQUEST["moeen_id"];
		$hes->load($vals);
	}
	$arr = $hes->getOutput();
	$combo ="";
	for($i=0;$i<count($arr);$i++)
	{
	      $tb = $arr[$i]["table"];
	      $val = $arr[$i]["value"];
	      $name = $arr[$i]["name"];	
	      if($i<count($arr)-1)
		{
			$onch = "onchange=\"document.getElementById('frm1').submit();\"";
		}
	      $combo .= "<td>$name:</td><td><select class='inp' id='$tb"."_id' name='$tb"."_id' $onch>\n";

                if (!$isAdmin && $tb == "kol")
                {
			$daftar_id = $_SESSION['daftar_id'];
			$kol_daftar = new daftar_class($daftar_id);
		        $kol_id  = $kol_daftar->kol_id;
                        $kol=new kol_class($kol_id);
			if(isset($_REQUEST["kol_id"]))
			{
				$sel = "selected='selected'";
			}
			else
			{
				$sel = "";
			}
                        $combo.="<option value=\"-2\"> </option><option $sel  value='".$kol_id."' >\n".$kol->name."\n</option>\n";
                }
                else
                {
		      $combo .= "<option value=\"-1\">\nهمه\n</option>\n";
		      for($j = 0;$j < count($val);$j++)
		      {
				if(isset($vals[$i]) && $vals[$i]==$val[$j]["id"])
                		{
					$combo .="<option selected=\"selected\" value='".$val[$j]["id"]."' >\n".$val[$j]["name"]."\n</option>\n";
	               		}
				else
               			{
                        		$combo .="<option value='".$val[$j]["id"]."' >\n".$val[$j]["name"]."\n</option>\n";
	               		}
		      }
		}
	      $combo .= "</select></td>\n";
	}
	$frm="";
/*	$frm.='<form method="POST" name="frmtedad" id ="frmtedad">';
	$frm.='<input name="txttedad" id="txttedad" type="hidden" value="1"/>';
//	$frm.='<input class="inp" type="submit" name="tedad" value="همه">';
	$frm.='</form>';*/
	$mande_az_ghabl = 0;
	$mande_az_ghabl_bed = 0;
	$mande_az_ghabl_bes = 0;
	$mande_kol = 0;
	if((isset($shart)))
	{
		mysql_class::ex_sql("select sum(`typ`*`mablagh`) as `man` from `sanad` where $shart `en`=1 and `tarikh` < '$sdate'",$qmand);
		if($r = mysql_fetch_array($qmand))
			$mande_az_ghabl = (int)$r['man'];
		$qmand = null;
		mysql_class::ex_sql("select sum(`mablagh`) as `man` from `sanad` where $shart `en`=1 and `typ`=1 and `tarikh` < '$sdate'",$qmand);
		if($r = mysql_fetch_array($qmand))
			$mande_az_ghabl_bes = (int)$r['man'];
		$qmand = null;
		mysql_class::ex_sql("select sum(`mablagh`) as `man` from `sanad` where $shart `en`=1 and `typ`=-1 and `tarikh` < '$sdate'",$qmand);
		if($r = mysql_fetch_array($qmand))
			$mande_az_ghabl_bed = (int)$r['man'];
		
	}
	else
	{
		$shart = '0=1 and';	
	}
	//echo $shart;
	$out = '<table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th>شماره سند</th><th>کل</th><th>تاریخ</th><th>معین</th><th>بدهکار</th><th>بستانکار</th><th>مانده</th><th>توضیحات</th></tr>';
	$q=null;
	mysql_class::ex_sql("select * from `sanad` where 1=0",$q);
	if(isset($sdate) && isset($edate))
	{
		if($se->detailAuth('all'))
			mysql_class::ex_sql("select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad` where $shart `en`=1 and `tarikh`>='$sdate' and `tarikh`<='$edate' ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
		else if($se->detailAuth('reserve'))
		{
			$q = null;
			mysql_class::ex_sql("select `id` from `sanad` where $shart `en`=1 and `tarikh`>='$sdate' and `tarikh`<='$edate' ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
			$arr = array();
			while($r = mysql_fetch_array($q))
				$arr[] = $r['id'];
			$arr = implode(",",$arr);
			$arr = (($arr=='')?-1:$arr);
			$q = null;
			mysql_class::ex_sql("select `sanad_record` from `sanad_reserve` where `sanad_record` in ($arr) ",$q);
			$arr = array();
			while($r = mysql_fetch_array($q))
				$arr[] = $r['sanad_record'];
			$arr = implode(",",$arr);
			$arr = (($arr=='')?-1:$arr);
			$q = null;
			mysql_class::ex_sql("select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad` where `id` in ($arr) ",$q);
		}
		else if($se->detailAuth('hesabdar'))
		{
			$q = null;
			mysql_class::ex_sql("select `id` from `sanad` where $shart `en`=1 and `tarikh`>='$sdate' and `tarikh`<='$edate' ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
			$arr = array();
			while($r = mysql_fetch_array($q))
				$arr[] = $r['id'];
			$arr = implode(",",$arr);
			$arr = (($arr=='')?-1:$arr);
			$q = null;
			mysql_class::ex_sql("select `sanad_record` from `sanad_reserve` where `sanad_record` in ($arr) ",$q);
			$arr = array();
			while($r = mysql_fetch_array($q))
				$arr[] = $r['sanad_record'];
			$arr = implode(",",$arr);
			$arr = (($arr=='')?-1:$arr);
			$q = null;
			mysql_class::ex_sql("select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad` where $shart `id` not in ($arr) and `en`=1 and `tarikh`>='$sdate' and `tarikh`<='$edate' ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
		}
		else
		{
			mysql_class::ex_sql("select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad` where $shart `en`=1 and `tarikh`>='$sdate' and `tarikh`<='$edate' ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
		}
	}
	$i=0;
	$mande_tmp = $mande_az_ghabl;
	$jam_bed = 0;
	$jam_bes = 0;
	while($r=mysql_fetch_array($q))
	{
		$i++;
		$kol_name =loadKol($r['kol_id']);
		$moeen_name = loadMoeen($r['moeen_id']);
		$mizan = $r['mablagh']*$r['typ'];
		if((int)$r['typ']==1)
			$jam_bes += $r['mablagh'];
		else if((int)$r['typ']==-1)
			$jam_bed += $r['mablagh'];
		$mande_tmp += $mizan;
		$mande_kol = loadMande($mande_tmp);
		$row_style = 'class="showgrid_row_odd"';
		if($i%2==0)
			$row_style = 'class="showgrid_row_even"';
		$out.="<tr $row_style >";
		$out .="<td class='showgrid_row_td' >$i</td><td class='showgrid_row_td' >".$r['shomare_sanad']."</td><td class='showgrid_row_td' >$kol_name</td><td class='showgrid_row_td' >$moeen_name</td><td class='showgrid_row_td' >".hamed_pdate($r['tarikh'])."</td><td class='showgrid_row_td' >".loadBed($mizan)."</td><td class='showgrid_row_td' >".loadBes($mizan)."</td><td class='showgrid_row_td' >".$mande_kol."</td><td class='showgrid_row_td' >".$r['tozihat']."</td>";
		$out.="</tr>\n";
	}
	//--------------------مانده محدوده جاری -------------------------------------
	$out .="<tr class='showgrid_row_odd' ><td class='showgrid_row_td' >--</td><td class='showgrid_row_td' >--</td><td class='showgrid_row_td' >--</td><td class='showgrid_row_td' >--</td><td class='showgrid_row_td' >جمع محدوده<br/> جاری</td><td class='showgrid_row_td' >".enToPerNums(monize($jam_bed))."</td><td class='showgrid_row_td' >".enToPerNums(monize($jam_bes))."</td><td class='showgrid_row_td' >".loadMande($jam_bes-$jam_bed)."</td><td class='showgrid_row_td' >--</td></tr>\n";
	//---------------------------------------------مانده کل ---------------------
	$out .="<tr class='showgrid_row_even' ><td class='showgrid_row_td' >--</td><td class='showgrid_row_td' >--</td><td class='showgrid_row_td' >--</td><td class='showgrid_row_td' >--</td><td class='showgrid_row_td' >جمع کل</td><td class='showgrid_row_td' >".enToPerNums(monize($jam_bed + $mande_az_ghabl_bed))."</td><td class='showgrid_row_td' >".enToPerNums(monize($jam_bes+ $mande_az_ghabl_bes))."</td><td class='showgrid_row_td' >$mande_kol</td><td class='showgrid_row_td' >--</td></tr>\n";
	$out.='</table>';
/*
	$grid = new jshowGrid_new("sanad","grid1");
	$qu = str_replace("kol_id","`sanad`.`kol_id`",$qu);
	$qu = str_replace("moeen_id","`sanad`.`moeen_id`",$qu);
	//$grid->query="SELECT `sanad`.`id` as `sagid`,`sanad`.`tarikh` as `starikh`,`sanad`.`shomare_sanad` as `shom`,`kol`.`name` as `kolname`,`moeen`.`name` as `moeenname`,`sanad`.`tarikh`,`sanad`.`typ`*`sanad`.`mablagh` as `bed`,`sanad`.`typ`*`sanad`.`mablagh` as `bes`,(select sum(`typ`*`mablagh`) from `sanad` where `id` in (select `id` from `sanad` where `shomare_sanad` < `shom` and $shart `en`=1) )+(select sum(`typ`*`mablagh`) from `sanad` where `id` in (select `id` from `sanad` where `shomare_sanad` = `shom` and `id` <= `sagid` and $shart `en`=1) )as `mande`,`sanad`.`id` FROM `sanad` left join `kol` on (`kol`.`id`=`kol_id`) left join `moeen` on (`moeen`.`id`=`moeen_id`) where $qu order by `sanad`.`tarikh`,`sanad`.`id`";
	$grid->loadQueryField = TRUE;
	$grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = 'شماره سند';
	$grid->columnHeaders[3] = 'کل';
	$grid->columnHeaders[4] = 'معین';
	$grid->columnHeaders[5] = 'تاریخ';
	$grid->columnHeaders[6] = 'بدهکار';
	$grid->columnHeaders[7] = "بستانکار";
        $grid->columnHeaders[8] = "مانده";
	$grid->columnHeaders[9] = "توضیحات";
	$grid->columnHeaders[10] = null;
	$grid->columnHeaders[11] = null;
	$grid->columnHeaders[12] = null;
	$grid->columnHeaders[13] = null;
	$grid->columnHeaders[14] = null;
	$grid->columnFunctions[5] = 'hamed_pdate';
	$grid->columnFunctions[6] = 'loadBed';
	$grid->columnFunctions[7] = 'loadBes';
	$grid->columnFunctions[8] = 'loadMande';
	$grid->columnFunctions[9] = 'loadTozih';
	$grid->width="99%";
	$grid->index_width = "20px";
	$grid->pageCount=10;
	$mod=0;
	if (isset($_REQUEST['txttedad']) && ($_REQUEST['txttedad']==1))
	{ 
		$grid->pageCount=200;
	}

	$grid->footer = "<td colspan='9' class=\"showgrid_row_odd\"><input class=\"inp\" type=\"button\" name=\"tedad\" value=\"همه\" onclick=\"document.getElementById('frmtedad').submit();\"></td>";
//	$grid->pageCount=10;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		بیلان حساب		
		</title>
		<script type="text/javascript">
		function sbtFrm()
		{
			document.getElementById('jam_bed').value=0;
                        document.getElementById('jam_bes').value=0;
			document.getElementById('check').value=2;
			document.getElementById('frm1').submit();
			document.getElementById('txttedad').value=document.getElementById('seltedad').options[document.getElementById('seltedad').selectedIndex].value;
		}
		function getPrint()
		{
			document.getElementById('combo_table').style.display='none';
			document.getElementById('div_main').style.width = '18cm';
			window.print();
			document.getElementById('combo_table').style.display='';
			document.getElementById('div_main').style.width = 'auto';
		}
		</script>
	</head>
	<body>
		<form method="POST" name="frmtedad" id ="frmtedad">
        	<input name="txttedad" id="txttedad" type="hidden" value="1"/>
	        </form>

		<div align="center" id="div_main" >
			<br/>
			<form id="frm1" method="GET">
				<table id='combo_table' >
					<tr>
						<?php echo $combo; ?>
					<tr>
					</tr>
						<td>
					
						<label>از تاریخ</label>
						</td>
						<td>
						<input class="inp" type="text" name="sdate" id="sdate" value="<?php echo ((isset($_GET["sdate"]))?$_GET["sdate"]:"")  ?>"  >
						</td>
						<td>
                                                <label>تا تاریخ</label>
						</td>
						<td>
						<input class="inp" type="text" name="edate" id="edate" value="<?php echo ((isset($_GET["edate"]))?$_GET["edate"]:"")  ?>" >
                                                </td>
					</tr>
					<tr>
						<td>
							کلمه کلیدی توضیحات : 
						</td>
						<td>
							<input type="text" class="inp" name="sfrase" id="sfrase" value="<?php echo ((isset($_GET["sfrase"]))?$_GET["sfrase"]:"")  ?>" />
						</td>
						<td colspan="2" align="left">
                                                <input class="inp" type="button" value="جستجو" onclick="sbtFrm();"  >
						<input type="hidden" name="check" id="check" value="-1"  > 
						<input type="hidden" id="jam_bed" name="jam_bed" value="<?php echo $GLOBALS["jam_bed"]; ?>" />
						<input type="hidden" id="jam_bes" name="jam_bes" value="<?php echo $GLOBALS["jam_bes"]; ?>" />
                                                </td>
					</tr>
				</table>		
			</form>
			<br/>
				<table cellspacing='0' cellpadding='0' width='95%'>
					<tr class='showgrid_row_odd'  >
						<td style='text-align:left' width='50%' >
							مانده از قبل:&nbsp;
						</td>
						<td  style='text-align:right' width='50%'>
							<b>
								<?php 
									$mand = loadMande($mande_az_ghabl);
									echo str_replace("<br/>","&nbsp;",$mand); 
								?>
							</b>
						</td>
					</tr>
				</table>
			<?php echo $out;  ?>
			<br/>
			<input type="button" value="چاپ" class="inp" onclick="getPrint();" >
		</div>
		
	</body>
</html>
