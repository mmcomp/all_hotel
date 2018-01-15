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
		$out = '<select name="kol_id" id="kol_id" class="form-control inp" onchange="kol_submit();" >';
		$out .='<option value="-1" >همه</option>';
		if($se->detailAuth('all') && $_SESSION['daftar_id']==49)
		{
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
		$out = '<select name="moeen_id" id="moeen_id" class="form-control inp" >';
		
		$out .='<option value="-1" >همه</option>';
		$wer = 'where `kol_id`='.$kol_id;
		if($se->detailAuth('all') && $_SESSION['daftar_id']==49)
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
			$hamed_wer = "`kol_id` = '$kol_id' ";
			mysql_class::ex_sql("select `id` from `moeen` where `kol_id` = '$kol_id' order by `name` ",$qn);
			$moeen_ids = '';
			while($t = mysql_fetch_array($qn))
				$moeen_ids .=(($moeen_ids=='')?'':',').$t['id'];
			$moeenreq = ($moeen_ids!='')?"and `moeen_id` in ($moeen_ids)".' group by `moeen_id` '."order by FIELD(`moeen_id`,$moeen_ids)":' and 1=0';
			//$hamed_wer .= $moeenreq;
			$hamed_wer .= " group by `moeen_id`";
			$qq = null;
			mysql_class::ex_sql("SELECT sum(`mablagh`*`typ`) as `mande_kol` from `sanad` where `kol_id` = '$kol_id' and `tarikh` <= '$edate'",$qq);
			if($r=mysql_fetch_array($qq))
				$mande_kol = (int)$r["mande_kol"];
		}
		else if($kol_id != -1 && $moeen_id != -1)
		{
			$hamed_wer = "`kol_id` = '$kol_id' and `moeen_id`=$moeen_id ";
			mysql_class::ex_sql("select `id` from `moeen` where `kol_id` = '$kol_id' and `id`=$moeen_id order by `name`",$qn);
			$moeen_ids = '';
			while($t = mysql_fetch_array($qn))
				$moeen_ids .=(($moeen_ids=='')?'':',').$t['id'];
			$moeenreq = ($moeen_ids!='')?"and `moeen_id` in ($moeen_ids)".' group by `moeen_id` '." order by FIELD(`moeen_id`,$moeen_ids)":' and 1=0';
			$hamed_wer .= $moeenreq;
			$qq = null;
                        mysql_class::ex_sql("SELECT sum(`mablagh`*`typ`) as `mande_kol` from `sanad` where `kol_id` = '$kol_id' and `moeen_id` = '$moeen_id' and `tarikh` <= '$edate'",$qq);
                        if($r=mysql_fetch_array($qq))
                                $mande_kol = (int)$r["mande_kol"];
		}
		else if($kol_id == -1 )
		{
			$hamed_wer = " group by `kol_id`";
			mysql_class::ex_sql("select `id` from `moeen` order by `name`",$qn);
			$moeen_ids = '';
			while($t = mysql_fetch_array($qn))
				$moeen_ids .=(($moeen_ids=='')?'':',').$t['id'];
			$moeenreq = ($moeen_ids!='')?"and `moeen_id` in ($moeen_ids) order by FIELD(`moeen_id`,$moeen_ids)":' and 1=0';
			$hamed_wer .= $moeenreq;
			$hamed_wer .= " order by `kol_id`";
			$qq = null;
                        mysql_class::ex_sql("SELECT sum(`mablagh`*`typ`) as `mande_kol` from `sanad` where `tarikh` >= '$sdate' and `tarikh` <= '$edate'",$qq);
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
	$combo =loadKolCombo($kol_id);
    $combo2 =loadMoeenCombo($kol_id,$moeen_id);

	$frm.='<form method="POST" name="frmtedad" id ="frmtedad">';
	$frm.='<input name="txttedad" id="txttedad" type="hidden" value="1"/>';
//	$frm.='<input class="inp" type="submit" name="tedad" value="همه">';
	$frm.='</form>';

$out1 = '
        <table style="width:100%;margin-right:10px;overflow-x:scroll" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">کل</th>
                                            <th style="text-align:right;">معین</th>
                                            <th style="text-align:right;">مانده</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
mysql_class::ex_sql("select * from `sanad` where $hamed_wer ",$ss);
$mande_kol = simpleMande($mande_kol);
$i=1;
while($r = mysql_fetch_array($ss))
{
    $group_id = $r['kol_id'];
    mysql_class::ex_sql("select `name` from `kol` where `id` = '$group_id' ",$h_id);
    $h_id1 = mysql_fetch_array($h_id);
    $kname = $h_id1['name'];
    $moeen_id = $r['moeen_id'];
    mysql_class::ex_sql("select `name` from `moeen` where `id` = '$moeen_id' ",$m_id);
    $m_id1 = mysql_fetch_array($m_id);
    $moname = $m_id1['name'];
    $mablagh = $r['mablagh'];
    if(fmod($i,2)!=0){
        
        $out1 .= "
        <tr class=\"odd\"><td>$i</td><td>$kname</td><td>$moname</td><td>$mablagh</td></tr>
        ";$i++;
    }
    else{
        
        $out1 .= "
         <tr class=\"even\"><td>$i</td><td>$kname</td><td>$moname</td><td>$mablagh</td></tr>
        ";
        $i++;
    }
            
        }
$out1 .= "
         <tr><td></td><td></td><td>جمع کل</td><td>$mande_kol</td></tr>
        ";
$out1.="</tbody></table>";	

	///////////$grid = new jshowGrid_new("sanad","grid1");
	//$grid->query = $new_query;//." order by `kol_name`,`moeen_name`";
	///////////$grid->whereClause = $hamed_wer;
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
 /*       $foot_co = 2;
        
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
	//////////$mande_kol = simpleMande($mande_kol);
	///////////$grid->footer = "<td class=\"showgrid_row_odd\"><input style=\"display:none;\" class=\"inp\" type=\"button\" name=\"tedad\" value=\"همه\" onclick=\"document.getElementById('txttedad').value=-1;document.getElementById('frmtedad').submit();\"></td><td colspan='$foot_co' class=\"showgrid_row_odd\" align='left' >جمع کل:</td><td id=\"tafazol\" align=\"center\" class=\"showgrid_row_odd\">$mande_kol</td>";
//////////	if ((isset($_REQUEST['tedad']))&&(($_REQUEST['tedad'])==1))           
	////////////	$grid->pageCount=1000;
//////////	if($kol_id != -1)
	///////////	$grid->footer .= "<tr class='showgrid_insert_row'><td><input type='button' value='همه' class='inp' onclick=\"sendfrm();\"  ></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
///////////	else
//////////		$grid->footer .= "<tr class='showgrid_insert_row'><td><input type='button' value='همه' class='inp' onclick=\"sendfrm();\"  ></td><td>&nbsp;</td><td>&nbsp;</td></tr>";
//////////	$grid->canAdd = FALSE;
//////////	$grid->canEdit = FALSE;
///////////	$grid->canDelete = FALSE;
 //////////       $grid->intial();
 ////////////       $grid->executeQuery();
	/////////////$out = $grid->getGrid();
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش مانده</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
	<meta name="description" content="" />
	<meta name="author" content="" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/cloud-admin.css" />
	<!-- Clock -->
	<link href="<?php echo $root ?>inc/digital-clock/assets/css/style.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?php echo $root ?>js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>js/colorbox/colorbox.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/animatecss/animate.min.css" />
    <!-- DataTables CSS -->
    <link href="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="<?php echo $root ?>datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">
<!-- DataTables JavaScript -->
    <!-- JQUERY -->
<script src="<?php echo $root ?>js/jquery/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $root ?>datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

    

	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
	
</head>
<body>
    <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
    <form method="POST" name="frmtedad" id ="frmtedad">
        <input name="txttedad" id="txttedad" type="hidden" value="1"/>
    </form>
	<!-- HEADER -->
	<?php include_once "headermodul.php"; ?>
	<!--/HEADER -->
	
	<!-- PAGE -->
	<section id="page">
			<!-- SIDEBAR -->
			<?php include_once "menubarmodul.php"; ?>
			<!-- /SIDEBAR -->
		<div id="main-content">
			<div class="container">
				
                
                <div class="row" style="margin-right:0px;margin-left:0px;">
                <div class="col-lg-12" style="padding:0px;">
                    <div class="panel panel-default" style="border: 1px solid #ffae2e;">
                        <div class="panel-heading" style="background-color:#ffae2e;color:white;padding:1px;">
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>گزارش مانده</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                           <form id='frm1'  method='GET' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">حساب کل:</label> 
                                    <div class="col-md-9"><?php echo $combo ?></div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">حساب معین:</label> 
                                    <div class="col-md-8">
                                       
                                            <?php echo $combo2 ?>
                                        
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;display:none">
                                    <label class="col-md-4 control-label">از تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control inp" type="text" name="sdate" id="datepicker1" value="<?php echo ((isset($_GET["sdate"]))?$_GET["sdate"]:"")  ?>"  >
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">تا تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control inp" type="text" name="edate" id="datepicker2" value="<?php echo ((isset($_GET["edate"]))?$_GET["edate"]:"")  ?>"  >
                                    
                                    </div>
                                </div>
                                <input type='hidden' name='mod' id='mod' value='1' >
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left" onclick="sbtFrm();">جستجو</button></div>
                                    <input type="hidden" name="check" id="check" value="-1"  > 
                                    <input type="hidden" id="jam_bed" name="jam_bed" value="<?php echo $GLOBALS["jam_bed"]; ?>" />
                                    <input type="hidden" id="jam_bes" name="jam_bes" value="<?php echo $GLOBALS["jam_bes"]; ?>" />
                                    <input type="hidden" id="tedad" name="tedad" value="-1" />
                                </div>
                            </div>
                          </form>
                            <!-- echo -->
                           <?php echo $out1 ?>
                        </div>
                       
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
        </div>
    </section>
	<!--/PAGE -->
    	<!-- Modal -->

	<!-- FOOTER -->

    <!-- Loading -->
<div id="loading">
    <div class="container1">
	   <div class="content1">
        <div class="circle"></div>
        <div class="circle1"></div>
        </div>
    </div>
</div>    
	<!-- GLOBAL JAVASCRIPTS -->
	<?php include_once "inc/footinclude.php" ?>
	
	<!-- Clock -->
	<script src="<?php echo $root ?>inc/digital-clock/assets/js/script.js"></script>
	
	<!-- news ticker -->
	
	<!-- DATE RANGE PICKER -->
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/moment.min.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/daterangepicker.min.js"></script>
	

	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
        <script src="<?php echo $root ?>js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.fa.min.js"></script>
	<script>
	
		var i=0;
		var SSmsg = null;
	
		jQuery(document).ready(function() {
            
            
            $("#loading").hide(); 
            App.setPage("gallery");  //Set current page
			//App.setPage("widgets_box");  //Set current page
			App.init(); //Initialise plugins and elements
			getofflist();
            
             $(document).ready(function() {
                 $("#datepicker0").datepicker();
            
                $("#datepicker1").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                    
                });
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
                $("#datepicker2").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker3").datepicker({
                    numberOfMonths: 3,
                    showButtonPanel: true
                });
            
                $("#datepicker4").datepicker({
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker5").datepicker({
                    minDate: 0,
                    maxDate: "+14D"
                });
            
                $("#datepicker6").datepicker({
                    isRTL: true,
                    dateFormat: "d/m/yy"
                });    
                 
        $('#dataTables-example').DataTable({
                responsive: true
        });
        
       
        
    });
            
            
		});
        
		function aa(x){
            $.post("<?php echo $root ?>modules/CalendarGetOFFAjax.php",{},function (data){
                if(data){
                    i=data;
                }else{
                    alert("Error!");
                }
            });
        }
		
        function getofflist(){
            $("#cal-pr").html("<img align=\"middle\" class=\"img-responsive\" style=\"margin: auto;\" src=\"<?php echo $root ?>img/loaders/17.gif\">");
            $.post("<?php echo $root ?>modules/CalendarGetOFFAjax.php",{},function (data){
                if(data){
                    i=data;
                    $("#cal-pr").html("");
                    $("#cal-pr").datepicker({changeMonth: true});
                }else{
                    $("#cal-pr").html("<p class=\"fa fa-exclamation-circle text-danger\"> عدم برقراری ارتباط با پایگاه داده</p>");
                }
            });
        }
        
        function rakModal(rakId){
            StartLoading();
            var id=rakId;
            
            $.post("gaantinfo.php",{oid:id},function(data){
                StopLoading();
                $("#rk").html(data);
                $('#rak-modal').modal('show');             

                             });
        }
        function send_search()
		{
			document.getElementById('mod').value= 2;
			document.getElementById('frm1').submit();
		}
        function sbtFrm()
		{
			document.getElementById('jam_bed').value=0;
            document.getElementById('jam_bes').value=0;
			document.getElementById('check').value=2;
			document.getElementById('frm1').submit();
		}
        function getPrint()
		{
			document.getElementById('panel-body').style.width = '18cm';
			window.print();
			document.getElementById('panel-body').style.width = 'auto';
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
                        StartLoading();
				
						var gUser_id = document.getElementById('gUser_id').options[document.getElementById('gUser_id').selectedIndex].value;
						var anbar_id = document.getElementById('anbar_id').options[document.getElementById('anbar_id').selectedIndex].value;
						var tarikh = document.getElementById('tarikh1').value;
						var kala_cost = document.getElementById('kala_cost').options[document.getElementById('kala_cost').selectedIndex].value;
                        
                        $.post("cost_anbar.php",{khadamat_id:khadamat,max_tedad:cost_jam,cost_tedad:cost_tedad,kala_cost:kala_cost,tarikh:tarikh,anbar_id:anbar_id,gUser_id:gUser_id},function(data){
                            
                            arr = data.split("_");
                            if(arr[0]=="1"){
                                var brr = arr[1].split("|");
                                var id = brr[0];
                                var cost_kala_id = brr[1];
                                var cost_tedad = brr[2];
                                alert("کالا ثبت شد");
                                $.post("anbar_print.php",{id:id,cost_kala_id:cost_kala_id,cost_tedad:cost_tedad},function(data){
                                    $("#anbar-modal").html(data);
                                    StopLoading();
                                    $('#anbar-modal').modal('show');
                                    
                                });
                            
                            }
                            else 
                                alert(data);

                        });
                        
					}
				}
			}
		}
        function kol_submit()
		{
			document.getElementById('jam_bed').value=0;
            document.getElementById('jam_bes').value=0;
			document.getElementById('frm1').submit();
		}

	function StartLoading(){
        
        $("#loading").show();    
		
    }
    function StopLoading(){
        $("#loading").hide(); 
    }
					


		
	</script>


	<?php include_once "footermodul.php"; ?>
	<!--/FOOTER -->
	

</body> 
</html>