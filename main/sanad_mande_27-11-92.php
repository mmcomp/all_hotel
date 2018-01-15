<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
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
		$inp = (int)$inp;
		$out = 0;
		mysql_class::ex_sql("select * from `sanad` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$mab = (int)$r["typ"] * (int)$r["mablagh"];
			$out =(($mab>0)?$mab:"---");
			$GLOBALS["jam_bes"] = $GLOBALS["jam_bes"] + (int)$out;
			$GLOBALS["jam_man"] = $GLOBALS["jam_bed"] - $GLOBALS["jam_bes"];
		}
		return monize($out);
	}
	function loadBed($inp)
	{
                $inp = (int)$inp;
                $out = 0;
                mysql_class::ex_sql("select * from `sanad` where `id`='$inp'",$q);
                if($r = mysql_fetch_array($q))
                {
			$mab = (int)$r["typ"] * (int)$r["mablagh"];
			$out =(($mab<0)?abs($mab):"---");
			$GLOBALS["jam_bed"] = $GLOBALS["jam_bed"] + abs((int)$out);
 		}
		return monize($out);
	}
	function echoer($id)
	{
		echo "id = '$id'<br/>\n";
		return($id);
	}
	function loadMande($inp)
	{
                $inp = (int)$inp;
                $out = 0;
		$tmp_1="kol_id";
		if (isset($GLOBALS["tmpid"]))
		{
			$tmp_1=$GLOBALS["tmpid"];
		}
		$sdate = hamed_pdateBack($_REQUEST['sdate']);
                $edate = hamed_pdateBack($_REQUEST['edate']);
                mysql_class::ex_sql("select * from `sanad` where `id`='$inp'",$q);
                if($r = mysql_fetch_array($q))
                {
			mysql_class::ex_sql("select `$tmp_1` from `sanad` where `id`='$inp'",$query);
			if($row = mysql_fetch_array($query))
	                {
				$moeen_id=(int)$row[$tmp_1];
			}
			$out=hesab_class::getMande($moeen_id,substr($tmp_1,0,-3),$sdate,$edate);
                }
		if($out == 0)
			$out = "۰";
		if($out>0)
			$out = "بستانکار <br/>".enToPerNums(monize(abs($out)));
		else if($out<0)
			$out = "بدهکار <br/>".enToPerNums(monize(abs($out)));
                return($out);
	}
	function loadMoeenMande($moeen_id)
	{
		$out = hesab_class::getMandeFromFirst($moeen_id,'moeen',hamed_pdateBack($_GET["edate"]));
		return simpleMande($out);
	}
	function loadKolMande($kol_id)
	{
		$out = hesab_class::getMandeFromFirst($kol_id,'kol',hamed_pdateBack($_GET["edate"]));
		return simpleMande($out);
	}
	function simpleMande($inp)
	{
		$out = "&nbsp;";
		$inp = (int)$inp;
		if($inp>0)
			$out = enToPerNums(monize($inp))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;بستانکار";
		else if($inp<0)
			$out = enToPerNums(monize(abs($inp)))."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;بدهکار";
		else
			$out = enToPerNums(monize($inp));
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
		$out="---";
		$color="blue";
		mysql_class::ex_sql("select * from `reserve` where `sanad_id`='$inp'",$q);
		if ($r = mysql_fetch_array($q))
		{
			$out=$r["family"]." "."تعداد نفرات"." ".$r["nafarat"]." "."نفر"." "."مدت اقامت"." ".$r["shab"]."شب "."از تاریخ:".hamed_pdate($r["aztarikh"])."تا تاریخ:".hamed_pdate($r["tatarikh"]);
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
	function loadKolCombo($kol_id)
	{
		$se = security_class::auth((int)$_SESSION['user_id']);
		$out = '<select name="kol_id" id="kol_id" class="inp" onchange="kol_submit();" >';
		if($se->detailAuth('all'))
		{
			$out .='<option value="-1" >همه</option>';
			mysql_class::ex_sql("select `id`,`name` from `kol` order by `name`",$q);
			while($r = mysql_fetch_array($q))
			$out .='<option '.(($r['id']==$kol_id)?'selected="selected"':'').' value="'.$r['id'].'">'.$r['name'].'</option>'."\n";
		}
		else
		{
			$daf = new daftar_class((int)$_SESSION['daftar_id']);
			$kol = new kol_class($daf->kol_id);
			$out .='<option value="'.$daf->kol_id.'" >'.$kol->name.'</option>';
		}
		$out .='</select>';
		return $out;
	}
	function loadMoeenCombo($kol_id,$moeen_id)
	{
		$se = security_class::auth((int)$_SESSION['user_id']);
		$out = '<select name="moeen_id" id="moeen_id" class="inp" >';
		
		$out .='<option value="-1" >همه</option>';
		$wer = 'where `kol_id`='.$kol_id;
		if($se->detailAuth('all'))
			$wer = ($kol_id==-1)?'':$wer;
		mysql_class::ex_sql("select `id`,`name` from `moeen` $wer order by `name`",$q);
		while($r = mysql_fetch_array($q))
		$out .='<option '.(($r['id']==$moeen_id)?'selected="selected"':'').' value="'.$r['id'].'">'.$r['name'].'</option>'."\n";
	
		$out .='</select>';
		return $out;
	}
	$hamed_wer = '1=0';
	$hes  = new hesab_class();
	$qu = "1=0";
	$tmp_1=-100;
	$kol_id = ((isset($_REQUEST["kol_id"]))?(int)$_REQUEST["kol_id"]:-1);
	$moeen_id = ((isset($_REQUEST["moeen_id"]))?(int)$_REQUEST["moeen_id"]:-1);
	$mande_kol = 0;
	mysql_class::ex_sql("select count(`id`) from `sanad` where 1=0",$co);
	if(isset($_GET["check"]) && (int)$_GET["check"]==2  )
	{
		$he = new hesab_class();
		$he = $he->hesab ;
		$shart = "";
		$sdate = hamed_pdateBack($_GET["sdate"]);
		$edate = hamed_pdateBack($_GET["edate"]);
		$kol_id = (int)$_REQUEST["kol_id"];
		$moeen_id = (int)$_REQUEST["moeen_id"];
		if($kol_id != -1 && $moeen_id == -1)
		{
			//echo "salam";
			//$new_query = "SELECT * FROM `sanad` where `kol_id` = '$kol_id' and  en=1 group by `moeen_id` ";
			$hamed_wer = "`kol_id` = '$kol_id' and  en=1 ";
			mysql_class::ex_sql("select `id` from `moeen` where `kol_id` = '$kol_id' order by `name` ",$qn);
			$moeen_ids = '';
			while($t = mysql_fetch_array($qn))
				$moeen_ids .=(($moeen_ids=='')?'':',').$t['id'];
			$moeenreq = ($moeen_ids!='')?"and `moeen_id` in ($moeen_ids)".' group by `moeen_id` '."order by FIELD(`moeen_id`,$moeen_ids)":' and 1=0';
			$hamed_wer .= $moeenreq;
			//$hamed_wer .= " group by `moeen_id`";
			$qq = null;
			mysql_class::ex_sql("SELECT sum(`mablagh`*`typ`) as `mande_kol` from `sanad` where `kol_id` = '$kol_id' and `en`=1 and `tarikh` <= '$edate'",$qq);
			if($r=mysql_fetch_array($qq))
				$mande_kol = (int)$r["mande_kol"];
		}
		else if($kol_id != -1 && $moeen_id != -1)
		{
			$hamed_wer = "`kol_id` = '$kol_id' and `moeen_id`=$moeen_id and en=1 ";
			mysql_class::ex_sql("select `id` from `moeen` where `kol_id` = '$kol_id' and `id`=$moeen_id order by `name`",$qn);
			$moeen_ids = '';
			while($t = mysql_fetch_array($qn))
				$moeen_ids .=(($moeen_ids=='')?'':',').$t['id'];
			$moeenreq = ($moeen_ids!='')?"and `moeen_id` in ($moeen_ids)".' group by `moeen_id` '." order by FIELD(`moeen_id`,$moeen_ids)":' and 1=0';
			$hamed_wer .= $moeenreq;
			$qq = null;
                        mysql_class::ex_sql("SELECT sum(`mablagh`*`typ`) as `mande_kol` from `sanad` where `kol_id` = '$kol_id' and `moeen_id` = '$moeen_id' and `en`=1 and `tarikh` <= '$edate'",$qq);
                        if($r=mysql_fetch_array($qq))
                                $mande_kol = (int)$r["mande_kol"];
		}
		else if($kol_id == -1 )
		{
			$hamed_wer = "en=1 group by `kol_id`";
			mysql_class::ex_sql("select `id` from `moeen` order by `name`",$qn);
			$moeen_ids = '';
			while($t = mysql_fetch_array($qn))
				$moeen_ids .=(($moeen_ids=='')?'':',').$t['id'];
			$moeenreq = ($moeen_ids!='')?"and `moeen_id` in ($moeen_ids) order by FIELD(`moeen_id`,$moeen_ids)":' and 1=0';
			$hamed_wer .= $moeenreq;
			$hamed_wer .= " order by `kol_id`";
			$qq = null;
                        mysql_class::ex_sql("SELECT sum(`mablagh`*`typ`) as `mande_kol` from `sanad` where `en`=1 and `tarikh` >= '$sdate' and `tarikh` <= '$edate'",$qq);
                        if($r=mysql_fetch_array($qq))
                                $mande_kol = (int)$r["mande_kol"];
		}
		mysql_class::ex_sql("select count(`id`) from `sanad` where `en`=1 and `tarikh`<='$edate'",$co);
	}
	if( (int) $_SESSION["typ"]!=0)
        {
                $vals = null;
                $daftar_id = $_SESSION['daftar_id'];
		$kol_daftar = new daftar_class($daftar_id);
		$vals[] = $kol_daftar->kol_id;
		//$vals[] = $_REQUEST["kol_id"];
		if(isset($_REQUEST["moeen_id"]))
		{
			$vals[] = (int)$_REQUEST["moeen_id"];
		}
                $hes->load($vals);
        }
	
	/*
	if(isset($_REQUEST["kol_id"]) && (int) $_SESSION["typ"]==0  )
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

                if($se->detailAuth('all'))
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
		else if($tb == "kol" && !$se->detailAuth('all'))
		{
			$daftar_id = $_SESSION['daftar_id'];
			$kol_daftar = new daftar_class($daftar_id);
			$kol = new kol_class($kol_daftar->kol_id);
			if(isset($_REQUEST["kol_id"]))
			{
				$sel = "selected='selected'";
			}
			else
			{
				$sel = "";
			}
                        $combo.="<option value=\"-1\"> </option><option $sel  value='".$kol_daftar->kol_id."' >\n".$kol->name."\n</option>\n";
		}
	      $combo .= "</select></td>\n";
	}
	*/
	$frm="";
	$combo ='حساب کل:'.loadKolCombo($kol_id).' حساب معین:'.loadMoeenCombo($kol_id,$moeen_id);
	$frm.='<form method="POST" name="frmtedad" id ="frmtedad">';
	$frm.='<input name="txttedad" id="txttedad" type="hidden" value="1"/>';
//	$frm.='<input class="inp" type="submit" name="tedad" value="همه">';
	$frm.='</form>';
	$grid = new jshowGrid_new("sanad","grid1");
	//$grid->query = $new_query;//." order by `kol_name`,`moeen_name`";
	$grid->whereClause = $hamed_wer;
	/*
	$grid->addFeild("kol");
	$grid->addFeild("moeen");
	$grid->addFeild("kol_name");
	$grid->addFeild("moeen_name");
	$grid->addFeild("mande");
	$foot_co = 0;
        for($i = 0;$i < count($grid->columnHeaders);$i++)
                $grid->columnHeaders[$i] = null;
	$grid->columnHeaders[17] = "حساب کل";
	$grid->columnHeaders[18] = "حساب معین";
	$grid->columnHeaders[19] = "مانده";
	$grid->columnFunctions[19] = "simpleMande";
	*/
        $foot_co = 2;
        
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = $conf->hesab("group_id");
	$grid->columnHeaders[3] = $conf->hesab("kol_id");
	$grid->columnHeaders[4] = $conf->hesab("moeen_id");
	$grid->columnHeaders[5] = $conf->hesab("tafzili_id");
	$grid->columnHeaders[6] = $conf->hesab("tafzili2_id");
	$grid->columnHeaders[7] = $conf->hesab("tafzilishenavar_id");
	$grid->columnHeaders[8] = $conf->hesab("tafzilishenavar2_id");
	$grid->columnHeaders [9] = null;
	$grid->columnHeaders[10] = null;
	$grid->columnHeaders[11] = null;
	$grid->columnHeaders[12] =null;
	$grid->columnHeaders[13] = null;
	$grid->columnHeaders[14] = null;
	$grid->columnFunctions[2] = "loadGrp";
	$grid->columnFunctions[3] = "loadKol";
        $grid->columnFunctions[4] = "loadMoeen";
        $grid->columnFunctions[5] = "loadTafzili";
        $grid->columnFunctions[6] = "loadTafzili2";
        $grid->columnFunctions[7] = "loadTafzilishenavar";
        $grid->columnFunctions[8] = "loadTafzilishenavar2";
	$grid->columnFunctions[9] = "hamed_pdate";
	if($kol_id == -1)
        {
		$grid->columnHeaders[4] = null;
		$grid->addFeild("kol_id");
		$grid->columnHeaders[15] = "مانده";
		$grid->columnFunctions[15] = "loadKolMande";
                //$grid->columnHeaders[18] = null;
                $foot_co = 1;
        }
	else
	{
		$grid->addFeild("moeen_id");
		$grid->columnHeaders[15] = "مانده";
		$grid->columnFunctions[15] = "loadMoeenMande";
	}
//	$grid->columnFunctions[15] = "loadBed";
//	$grid->columnFunctions[16] = "loadBes";
	//------------------------------------
	//$grid->whereClause = $qu." order by `shomare_sanad`";
/*
	$grid->whereClause = $qu;
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->width="99%";
	$grid->pageCount=10;
	$mod=0;
	if (isset($_REQUEST['txttedad']) && ($_REQUEST['txttedad']==-1))
	{ 
		$outq=mysql_fetch_array($co);
		$grid->pageCount=(int)$outq[0];
	}
*/
	$mande_kol = simpleMande($mande_kol);
	$grid->footer = "<td class=\"showgrid_row_odd\"><input style=\"display:none;\" class=\"inp\" type=\"button\" name=\"tedad\" value=\"همه\" onclick=\"document.getElementById('txttedad').value=-1;document.getElementById('frmtedad').submit();\"></td><td colspan='$foot_co' class=\"showgrid_row_odd\" align='left' >جمع کل:</td><td id=\"tafazol\" align=\"center\" class=\"showgrid_row_odd\">$mande_kol</td>";
	if ((isset($_REQUEST['tedad']))&&(($_REQUEST['tedad'])==1))           
		$grid->pageCount=1000;
	if($kol_id != -1)
		$grid->footer .= "<tr class='showgrid_insert_row'><td><input type='button' value='همه' class='inp' onclick=\"sendfrm();\"  ></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	else
		$grid->footer .= "<tr class='showgrid_insert_row'><td><input type='button' value='همه' class='inp' onclick=\"sendfrm();\"  ></td><td>&nbsp;</td><td>&nbsp;</td></tr>";
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
        $grid->intial();
        $grid->executeQuery();
	$out = $grid->getGrid();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../css/style.css" rel="stylesheet" />
		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		بیلان حساب		
		</title>
		<script type="text/javascript">
		function sendfrm()
		{
			document.getElementById('jam_bed').value=0;
                        document.getElementById('jam_bes').value=0;
			document.getElementById('check').value=2;
			document.getElementById('tedad').value=1;
			document.getElementById('frm1').submit();
		}
		function sbtFrm()
		{
			document.getElementById('jam_bed').value=0;
                        document.getElementById('jam_bes').value=0;
			document.getElementById('check').value=2;
			document.getElementById('frm1').submit();
		}
		function kol_submit()
		{
			document.getElementById('jam_bed').value=0;
                        document.getElementById('jam_bes').value=0;
			document.getElementById('frm1').submit();
		}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#edate").datepicker({
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
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<form method="POST" name="frmtedad" id ="frmtedad">
        	<input name="txttedad" id="txttedad" type="hidden" value="1"/>
	        </form>

		<div align="center">
			<br/>
			<form id="frm1" method="GET">
				<table>
					<tr>
						<td><?php echo $combo; ?></td>
						<td style="display:none;" >
						<label>از تاریخ</label>
						</td>
						<td style="display:none;" >
						<input class="inp" type="text" name="sdate" id="sdate" value="<?php echo ((isset($_GET["sdate"]))?$_GET["sdate"]:"")  ?>"  >
						</td>
						<td>
                                                <label>تا تاریخ</label>
						</td>
						<td>
						<input class="inp" type="text" name="edate" id="edate" value="<?php echo ((isset($_GET["edate"]))?$_GET["edate"]:"")  ?>" >
                                                </td>
						<td>
                                                <input class="inp" type="button" value="جستجو" onclick="sbtFrm();"  >
						<input type="hidden" name="check" id="check" value="-1"  > 
						<input type="hidden" id="jam_bed" name="jam_bed" value="<?php echo $GLOBALS["jam_bed"]; ?>" />
						<input type="hidden" id="jam_bes" name="jam_bes" value="<?php echo $GLOBALS["jam_bes"]; ?>" />
						<input type="hidden" id="tedad" name="tedad" value="-1" />
                                                </td>
					</tr>
				</table>		
			</form>
			<br/>
			<?php echo $out;  ?>
		</div>
		<script>
/*
			var bed = parseInt(document.getElementById('jam_bed').value,10);
			var bes = parseInt(document.getElementById('jam_bes').value,10);
			var tafazol = bed-bes;
			var stat = ((tafazol>0)?"بدهکار":"بستانکار");
			if(tafazol == 0)
				stat = "";
			document.getElementById('jam_bed1').innerHTML="&nbsp;"+FixNums(monize2(bed))+"&nbsp;&nbsp;";
			document.getElementById('jam_bes1').innerHTML="&nbsp;"+FixNums(monize2(bes))+"&nbsp;&nbsp;";
			document.getElementById('tafazol').innerHTML ="&nbsp;"+FixNums(monize2(Math.abs(tafazol)))+" "+stat;
*/
		</script>
	</body>
</html>
