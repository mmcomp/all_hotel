<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(isset($_REQUEST['is_ajax']))
	{
		$search_frase = $_REQUEST['search_frase'];
		$object_id = $_REQUEST['object_id'];
		$moeen_id = (int)$_REQUEST['moeen_id'];
		$kol_id = (int)$_REQUEST['kol_id'];
		$out1 = '';
		$kol = " `kol_id` = $kol_id and ";
		if($object_id == 'aj_moeen_value')
		{
			mysql_class::ex_sql("select `name`,`id` from `moeen` where $kol `name` like '%$search_frase%'",$q);
                        while($r = mysql_fetch_array($q))
                                $out1 .= "<span class=\"ajax_combo_items\" onclick=\"ajax_combo_clicked('aj_moeen_id','aj_moeen_value','".$r['id']."','".$r['name']."');\">".$r['name']."</span><br/>\n";
		}
		else if($object_id == 'aj_kol_value')
		{
			mysql_class::ex_sql("select `name`,`id` from `kol` where `name` like '%$search_frase%'",$q);
			while($r = mysql_fetch_array($q))
				$out1 .= "<span class=\"ajax_combo_items\" onclick=\"ajax_combo_clicked('aj_kol_id','aj_kol_value','".$r['id']."','".$r['name']."');\">".$r['name']."</span><br/>\n";
		}
		die($out1);
	}
	if(isset($_REQUEST['record_ids']) && $_REQUEST['record_ids']!='' )
	{
		$sanad_records = explode(',',$_REQUEST['record_ids']);
		$aj_kol_id = $_REQUEST['aj_kol_id'];
		$aj_moeen_id = $_REQUEST['aj_moeen_id'];
		if(sanad_class::editSanadRecord($sanad_records,$aj_moeen_id,$aj_kol_id))
			$msg = 'انتقال با موفقیت انجام شد';
		else
			$msg = 'انتقال با شکست مواجه گردید';
	}
	$hesab_enteghal = '';
	if(($se->detailAuth('all') || $se->detailAuth('enteghal') ) && $conf->canArchive )
		$hesab_enteghal = '<tr>
	<td colspan="2" >
	کل:
	</td>
	<td>
	<input class="inp" autocomplete="off" type="text" id="aj_kol_value"  name="aj_kol_value" value="" onkeyup="search(this);"/>
	<input type="hidden" id="aj_kol_id" name="aj_kol_id" value="-1" readonly="readonly"/>
	</td>
	<td>
	معین:
	</td>
	<td>
		<input class="inp" autocomplete="off" type="text" id="aj_moeen_value" name="aj_moeen_value" value="" onkeyup="search(this);"/>
		<input type="hidden" id="aj_moeen_id" name="aj_moeen_id" value="-1" readonly="readonly"/>
		<input type="hidden" id="record_ids" name="record_ids" >
		<input type="button" value="انتقال" style="width:70px;" class="inp" onclick="enteghal();" >
	</td>
</tr>
';
	$isAdmin = ($se->detailAuth('all') || $se->detailAuth('reserve') || $se->detailAuth('hesabdar') );
	//$isAdmin = ($se->detailAuth('all') || $se->detailAuth('hesabdar') );
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
		$inp = (real)$inp;
		return monize($inp>0?abs($inp):'---');
	}
	function loadBed($inp)
	{
                $out = (real)$inp;
             	$out =(( $out<0)?-1*$out:"---");
		return monize($out);
	}
	function echoer($id)
	{
		echo "id = '$id'<br/>\n";
		return($id);
	}
	function loadMande($inp)
	{
                $out = $inp;
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
		$inp = $inp;
		$out = "---";
		mysql_class::ex_sql("select * from `sanad_reserve$sal` where  `sanad_record` = $inp",$q);
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
                        mysql_class::ex_sql("select `tozihat` from `sanad$sal` where `id`='$inp'",$q);
                        if ($r = mysql_fetch_array($q))
                        {
                                $out = $r["tozihat"];
                        }
                }
		return $out;
	}
	function loadSalMaliCombo($sal)
	{
		$y = mysql_class::loadSaleMali();
		$out='<select name="salMali" id="salMali" class="inp" >
				<option value="" '.(($sal=='')?'selected="selected"':'').' >جاری</option>';
		for($i=0;$i<count($y);$i++)
		{
			$sel = ('_'.$y[$i]==$sal)?'selected="selected"':'';
			$out .='<option '.$sel.' value="_'.$y[$i].'">'.$y[$i].'</option>'."\n";
		}
		$out .='</select>';
		return $out;
	}
	$sal = (isset($_REQUEST['salMali']))?$_REQUEST['salMali']:'';
	$hes  = new hesab_class();
	$qu = "1=0";
	//mysql_class::ex_sql("select count(`id`) from `sanad$sal` where 1=0",$co);
	$canNotGo = TRUE;
	$msg= '';
	if(isset($_GET["check"]) && (int)$_GET["check"]==2  )
	{
		$he = new hesab_class();
		$he = $he->hesab ;
		$shart = "";
		$sdate = audit_class::hamed_pdateBack($_GET["sdate"]);
		$edate = audit_class::hamed_pdateBack($_GET["edate"]);
		$sdate = date("Y-m-d 00:00:00",strtotime($sdate));
		$edate = date("Y-m-d 23:59:59",strtotime($edate));
		$sfrase = $_REQUEST["sfrase"];
		foreach($he as $key=>$value)
		{
			if((int)$_GET[$key]!==-1)
			{
				$shart .= " $key=".$_GET[$key]." and ";
				$canNotGo = FALSE;
			}
		}
		//$qu = "$shart  `tarikh`>='$sdate' and `tarikh`<='$edate'".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'');
		//mysql_class::ex_sql("select count(`id`) from `sanad$sal` where  `tarikh`>='$sdate' and `tarikh`<='$edate' ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `name`",$co);
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
	$kol_id = kol_class::loadByName_habibi('رزرواسیون هتل اراد');
	$combo_kol ="حساب  کل :";
	$combo_kol .= "<select class='inp' id='kol_id' name='kol_id'>\n";
	$combo_kol .="<option value='".$kol_id."' >\nرزرواسیون هتل اراد\n</option>\n";
	$combo_kol .= "</select>";
	$combo_moeen = "حساب معین: ";
	$combo_moeen .= "<select class='inp' id='moeen_id' name='moeen_id'>\n";
	$combo_moeen .="<option value=\"-2\"> </option>";
	mysql_class::ex_sql("select * from `moeen` where `kol_id` = $kol_id order by `name`",$q);
	$moeen_id = ((isset($_REQUEST["moeen_id"]))?$_REQUEST["moeen_id"]:-1);
	while($r = mysql_fetch_array($q))
        {
		$sel = (($r['id']==$moeen_id)?"selected='selected'":'');
		$m_id = $r['id'];
		$m_name = $r['name'];
		$combo_moeen .="<option $sel value='".$m_id."' >\n".$m_name."\n</option>\n";
	}
	$combo_moeen .= "</select>";
	/*for($i=0;$i<count($arr);$i++)
	{
	      $tb = $arr[$i]["table"];
	      $val = $arr[$i]["value"];
	      $name = $arr[$i]["name"];	
	      if($i<count($arr)-1)
		{
			$onch = "onchange=\"document.getElementById('frm1').submit();\"";
			//$onch = "onchange=\"alert('hh');\"";
		}
	      $colspan = '';
	      if($i==0)
		 $colspan = 'colspan="2"';
	      $combo .= "<td  $colspan align='left' >$name:</td><td><select class='inp' id='$tb"."_id' name='$tb"."_id' $onch>\n";

                if (!$isAdmin && $tb == "kol")
                {
			$daftar_id = $_SESSION['daftar_id'];
			$kol_daftar = new daftar_class($daftar_id);
		       // $kol_id  = $kol_daftar->kol_id;
			$kol_id = 65;
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
	      $combo .= "</select></td>\n";*/
	//}
	$frm="";
/*	$frm.='<form method="POST" name="frmtedad" id ="frmtedad">';
	$frm.='<input name="txttedad" id="txttedad" type="hidden" value="1"/>';
//	$frm.='<input class="inp" type="submit" name="tedad" value="همه">';
	$frm.='</form>';*/
	$mande_az_ghabl = 0;
	$mande_az_ghabl_bed = 0;
	$mande_az_ghabl_bes = 0;
	$mande_kol = 0;
	$ch_kol = '';
	$ch_tarikh = '';
	$ch_sanad = '';
	$switch='';
	if(isset($_REQUEST['smod']))
	{
		$azshomare =(int) $_REQUEST['azshomare'];
		$tashomare =(int)$_REQUEST['tashomare'];
		$sdate = audit_class::hamed_pdateBack($_GET["sdate"]);
		$edate = audit_class::hamed_pdateBack($_GET["edate"]);
		$sdate = date("Y-m-d 00:00:00",strtotime($sdate));
		$edate = date("Y-m-d 23:59:59",strtotime($edate));
		switch ($_REQUEST['smod'])
		{
			case 'kol':
				$switch = "`tarikh`<='$edate'";
				$ch_kol = 'checked="checked"';
				break;
			case 'tarikh':
				$switch = "`tarikh`>='$sdate' and `tarikh`<='$edate'";
				$ch_tarikh = 'checked="checked"';
				break;
			case 'sanad':
				$switch = "`shomare_sanad`>=$azshomare and `shomare_sanad`<=$tashomare";
				$ch_sanad = 'checked="checked"';
				break;
		}
	}
	else
		$ch_kol = 'checked="checked"';
	if(isset($shart) && !$canNotGo)
	{
		if($ch_tarikh!='')
		{
			mysql_class::ex_sql("select sum(`typ`*`mablagh`) as `man` from `sanad$sal` where $shart `tarikh` < '$sdate'",$qmand);
			if($r = mysql_fetch_array($qmand))
				//$mande_az_ghabl = (int)$r['man'];
				$mande_az_ghabl = (real)$r['man'];
			$qmand = null;
			mysql_class::ex_sql("select sum(`mablagh`) as `man` from `sanad$sal` where $shart `typ`=1 and `tarikh` < '$sdate'",$qmand);
			if($r = mysql_fetch_array($qmand))
				//$mande_az_ghabl_bes = (int)$r['man'];
				$mande_az_ghabl_bes = (real)$r['man'];
			$qmand = null;
			mysql_class::ex_sql("select sum(`mablagh`) as `man` from `sanad$sal` where $shart `typ`=-1 and `tarikh` < '$sdate'",$qmand);
			if($r = mysql_fetch_array($qmand))
				//$mande_az_ghabl_bed = (int)$r['man'];
				$mande_az_ghabl_bed = (real)$r['man'];
//echo "select sum(`mablagh`) as `man` from `sanad$sal` where $shart `typ`=-1 and `tarikh` < '$sdate'";
		}
		else if($ch_sanad!='')
		{
			mysql_class::ex_sql("select sum(`typ`*`mablagh`) as `man` from `sanad$sal` where $shart `shomare_sanad` < '$azshomare'",$qmand);
			if($r = mysql_fetch_array($qmand))
				$mande_az_ghabl = (real)$r['man'];
			$qmand = null;
			mysql_class::ex_sql("select sum(`mablagh`) as `man` from `sanad$sal` where $shart `typ`=1 and `shomare_sanad` < '$azshomare'",$qmand);
			if($r = mysql_fetch_array($qmand))
				$mande_az_ghabl_bes = (real)$r['man'];
			$qmand = null;
			mysql_class::ex_sql("select sum(`mablagh`) as `man` from `sanad$sal` where $shart `typ`=-1 and `shomare_sanad` < '$azshomare'",$qmand);
			if($r = mysql_fetch_array($qmand))
				$mande_az_ghabl_bed = (real)$r['man'];
		}
	}
	else
	{
		if($canNotGo && isset($shart))
			$msg = 'حجم اطلاعات گزارش زیاد بوده و قابل نمایش نمی باشد';
		$shart = '0=1 ';	
	}
	$kol_id =(isset($_REQUEST['kol_id']))?$_REQUEST['kol_id']:0;
	$moeen_id = (isset($_REQUEST['moeen_id']))?$_REQUEST['moeen_id']:0;
	$kol_column = '';
	$moeen_column = '';
	if($kol_id==-1)
	{
		$kol_column = '<th>کل</th>';
		$moeen_column = '<th>معین</th>';
	}
	else if($kol_id>0 && $moeen_id==-1)
		$moeen_column = '<th>معین</th>';
	$out = '<table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;"><tr class="showgrid_header" ><th>ردیف</th><th><input type="checkbox" onclick="check_all(this);" ></th><th>شماره سند</th>'.$kol_column.$moeen_column.'<th> تاریخ</th><th>بدهکار</th><th>بستانکار</th><th>مانده</th><th>توضیحات</th></tr>';
	$q=null;
	mysql_class::ex_sql("select * from `sanad$sal` where 1=0",$q);
	if(isset($sdate) && isset($edate) && !$canNotGo)
	{//echo $switch;
		//if($se->detailAuth('all'))
		//{
			mysql_class::ex_sql("select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad$sal` where $shart  $switch ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
//echo "select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad$sal` where $shart  $switch ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`";
	}
	else if ($canNotGo && !isset($shart))
		$msg = 'حجم اطلاعات گزارش زیاد بوده و قابل نمایش نمی باشد';
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
		$kol_column = '';
		$moeen_column = '';
		if($kol_id==-1)
		{
			$kol_column = "<td class='showgrid_row_td' >$kol_name</td>";
			$moeen_column = "<td class='showgrid_row_td' >$moeen_name</td>";
		}
		else if($kol_id>0 && $moeen_id==-1)
			$moeen_column = "<td class='showgrid_row_td' >$moeen_name</td>";
		$out.="<tr $row_style >";
		$out .="<td class='showgrid_row_td' >$i</td>
<td class='showgrid_row_td'  ><input type='checkbox' id='check_".$r['id']."' name='check_".$r['id']."' ></td>
<td class='showgrid_row_td' >".$r['shomare_sanad']."</td>$kol_column"."$moeen_column<td class='showgrid_row_td' >".hamed_pdate($r['tarikh'])."</td><td class='showgrid_row_td' >".loadBed($mizan)."</td><td class='showgrid_row_td' >".loadBes($mizan)."</td><td class='showgrid_row_td' >".$mande_kol."</td><td class='showgrid_row_td' >".$r['tozihat']." <u><span style=\"color:blue;cursor:pointer;".(($conf->zamaiem)?'':'display:none;')."\" onclick=\"wopen('upload_pic.php?sanad_record_id=".$r['id']."&','',600,400);\">ضمایم</span></u>"."</td>";
		$out.="</tr>\n";
	}
	$mande_kol = loadMande($mande_tmp);
	$kol_column = '';
	$moeen_column = '';
	if($kol_id==-1)
	{
		$kol_column = "<td class='showgrid_row_td' >--</td>";
		$moeen_column = "<td class='showgrid_row_td' >--</td>";
	}
	else if($kol_id>0 && $moeen_id==-1)
		$moeen_column = "<td class='showgrid_row_td' >--</td>";
	//--------------------مانده محدوده جاری -------------------------------------
	$out .="<tr class='showgrid_row_odd' ><td class='showgrid_row_td' colspan='2' >--</td><td class='showgrid_row_td' >--</td>$kol_column"."$moeen_column<td class='showgrid_row_td' >جمع محدوده<br/> جاری</td><td class='showgrid_row_td' >".enToPerNums(monize($jam_bed))."</td><td class='showgrid_row_td' >".enToPerNums(monize($jam_bes))."</td><td class='showgrid_row_td' >".loadMande($jam_bes-$jam_bed)."</td><td class='showgrid_row_td' >--</td></tr>\n";
	//---------------------------------------------مانده کل ---------------------
	$out .="<tr class='showgrid_row_even' ><td class='showgrid_row_td' colspan='2' >--</td><td class='showgrid_row_td' >--</td>$kol_column"."$moeen_column<td class='showgrid_row_td' >جمع کل</td><td class='showgrid_row_td' >".enToPerNums(monize($jam_bed + $mande_az_ghabl_bed))."</td><td class='showgrid_row_td' >".enToPerNums(monize($jam_bes+ $mande_az_ghabl_bes))."</td><td class='showgrid_row_td' >$mande_kol</td><td class='showgrid_row_td' >--</td></tr>\n";
	$out.='</table>';
	$hesab_name = "<td width='30%' >&nbsp;</td>";
	if($kol_id>0 && $moeen_id==-1)
	{
		$tmp_ko = new kol_class($kol_id);
		$hesab_name = "<td width='30%' ><b>حساب:".$tmp_ko->name."</b></td>";
	}
	else if($kol_id>0 && $moeen_id>0)
	{
		$tmp_mo = new moeen_class($moeen_id);
		$hesab_name = "<td width='30%' ><b>حساب:".$tmp_mo->name."</b></td>";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->

		<script type="text/javascript" src="../js/tavanir.js"></script>
		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		بیلان حساب		
		</title>
		<script type="text/javascript">
			function mehrdad_ajaxFunction(func,command,obj){
		                var ajaxRequest;  // The variable that makes Ajax possible!
		                try{
		                        // Opera 8.0+, Firefox, Safari
		                        ajaxRequest = new XMLHttpRequest();
		                } catch (e){
		                        // Internet Explorer Browsers
		                        try{
		                                ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		                        } catch (e) {
		                                try{
		                                        ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
		                                } catch (e){
		                                        // Something went wrong
		                                        alert("ﻡﺭﻭﺭگﺭ ﺶﻣﺍ ﻕﺎﺒﻟیﺕ ﺁژﺍکﺱ ﺭﺍ ﻥﺩﺍﺭﺩ ﻞﻄﻓﺍً ﺍﺯ ﻡﺭﻭﺭگﺭ ﺝﺩیﺪﺗﺭ ﻭ پیﺵﺮﻔﺘﻫ ﺕﺭی ﻡﺎﻨﻧﺩ ﻑﺍیﺮﻓﺍکﺱ ﺎﺴﺘﻓﺍﺪﻫ کﻥیﺩ");
		                                        return false;
		                                }
		                        }
		                }
		                // Create a function that will receive data sent from the server
		                var ser = obj.value;
		                ajaxRequest.onreadystatechange = function(){
		                        if(ajaxRequest.readyState == 4){
		                                var ar = ajaxRequest.responseText;
		                                func(command,ar,obj);
		                        }
		                };
				var moeen_id = document.getElementById('aj_moeen_id').value;
				var kol_id = document.getElementById('aj_kol_id').value;
		                ajaxRequest.open("POST", "sanad_gozaresh.php?is_ajax=1&search_frase="+ser+"&object_id="+obj.id+"&moeen_id="+moeen_id+"&kol_id="+kol_id+"&r="+Math.random()+"&", true);
		                ajaxRequest.send(null);
		        }
		        function search_back(command,ar,obj)
		        {
		                var div = document.getElementById('ajax_result');
		                div.style.position = 'absolute';
		                div.style.borderStyle = 'solid';
		                div.style.borderWidth = '2px';
		                div.style.borderColor = 'gray';
		                div.style.textAlign = 'right';
		                div.style.backgroundColor = '#fff';
		                var pos=findPos(obj);
		                div.style.width = String(obj.clientWidth+6)+'px';
		                div.style.left = String(pos[0])+'px';
		                div.style.top = String(pos[1]+obj.clientHeight+3)+'px';
		                div.innerHTML =ar;
		        }
		        function search(obj)
		        {
		                if(obj.value)
		                        mehrdad_ajaxFunction(search_back,'search',obj);
		        }
		        function findPos(obj)
		        {
		                var curleft = curtop = 0;
		                if (obj.offsetParent) {
		                        do
		                        {
		                                curleft += obj.offsetLeft;
		                                curtop += obj.offsetTop;
		                        }while (obj = obj.offsetParent);
		                }
		                return [curleft,curtop];
		        }
		        function clear_ajax_result()
		        {
		                document.getElementById('ajax_result').innerHTML = '';
		                document.getElementById('ajax_result').style.width = 0;
		                document.getElementById('ajax_result').style.left = 0;
		                document.getElementById('ajax_result').style.top = 0;
		                document.getElementById('ajax_result').style.borderStyle = 'none';
		        }
		        function intial_body()
		        {
		                document.body.onclick = clear_ajax_result;
		        }
			function ajax_combo_clicked(id,name,value,name_value)
			{
				document.getElementById(id).value = value;
				document.getElementById(name).value = name_value;
			}
		function sbtFrm()
		{
			document.getElementById('jam_bed').value=0;
                        document.getElementById('jam_bes').value=0;
			document.getElementById('check').value=2;
			document.getElementById('frm1').submit();
			//document.getElementById('txttedad').value=document.getElementById('seltedad').options[document.getElementById('seltedad').selectedIndex].value;
		}
		function getPrint()
		{
			document.getElementById('combo_table').style.display='none';
			document.getElementById('div_main').style.width = '18cm';
			window.print();
			document.getElementById('combo_table').style.display='';
			document.getElementById('div_main').style.width = 'auto';
		}
		function loadCheckeds()
                {
                        var inps = document.getElementsByTagName('input');
                        var tmp;
                        var check_ids = '';
                        var id;
                        for(var i=0;i < inps.length;i++)
                        {
                                tmp = inps[i].id.split('_');
                                if(inps[i].type == 'checkbox' && tmp.length == 2 && tmp[0] == 'check' && inps[i].checked)
                                {
                                        id = tmp[1];
                                        check_ids = check_ids + ((check_ids!='')?',':'') + id;
                                }
                        }
                        return(check_ids);
                }
		function check_all(Obj)
                {
                        var inps = document.getElementsByTagName('input');
			for(var i=0;i < inps.length;i++)
				if(inps[i].type == 'checkbox')
					inps[i].checked = Obj.checked;
		}
		function enteghal()
		{
			if(document.getElementById('aj_kol_id').value!='-1' && document.getElementById('aj_kol_id').value!='-1' )
			{
				if(loadCheckeds()!='')
				{
					document.getElementById('record_ids').value = loadCheckeds();
					sbtFrm();
				}
				else
					alert('رکوردی انتخاب نشده است');
			}
			else
				alert('حساب انتقالی انتخاب نشده است');
		}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#sdate").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
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
	<body onclick="clear_ajax_result();" >
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div id="ajax_result">
		</div>
		<form method="POST" name="frmtedad" id ="frmtedad">
        		<input name="txttedad" id="txttedad" type="hidden" value="1"/>
	        </form>

		<div align="center" id="div_main" >
			<br/>
			<form id="frm1" method="GET">
				<table id='combo_table' border="1" style="border:1px dashed;border-collapse:collapse;padding:5px;" >
					<tr>
						<td colspan="5" align="center" >
						سال مالی
							<?php echo loadSalMaliCombo($sal); ?>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center" >
							<?php echo $combo_kol; ?>
						</td>
						<td colspan="3" align="center" >
							<?php echo $combo_moeen; ?>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" name="smod" id="tarikh" value="kol" <?php echo $ch_kol; ?> >
						</td>
						<td colspan="4" >
						<label>کل حساب</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="radio" name="smod" id="tarikh" value="tarikh" <?php echo $ch_tarikh; ?> >
						</td>
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
							<input type="radio" name="smod" id="sanad" value="sanad" <?php echo $ch_sanad; ?> >
						</td>
						<td>
						<label>از شماره سند</label>
						</td>
						<td>
						<input class="inp" type="text" name="azshomare" id="azshomare" value="<?php echo ((isset($_GET["azshomare"]))?$_GET["azshomare"]:"")  ?>"  >
						</td>
						<td>
                                                <label>تا شماره سند</label>
						</td>
						<td>
						<input class="inp" type="text" name="tashomare" id="tashomare" value="<?php echo ((isset($_GET["tashomare"]))?$_GET["tashomare"]:"")  ?>" >
                                                </td>
					</tr>
					<tr>
						<td colspan='2' >
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
					<?php echo $hesab_enteghal; ?>
				</table>
						
			</form>
			<br/>
				<table cellspacing='0' cellpadding='0' width='95%' border='0'>
					<tr class='showgrid_row_odd'  >
						<?php echo $hesab_name; ?>
						<td style='text-align:left' width='30%' >
							مانده از قبل:&nbsp;
						</td>
						<td  style='text-align:right' width='40%'>
							<b>
								<?php 
									$mand = loadMande($mande_az_ghabl);
									echo str_replace("<br/>","&nbsp;",$mand); 
								?>
							</b>
						</td>
					</tr>
				</table>
			<?php echo '<h2 style="color:red;" >'.$msg.'</h2><br/>'.$out;  ?>
			<br/>
			<input type="button" value="چاپ" class="inp" onclick="getPrint();" >
		</div>		
	</body>
</html>
