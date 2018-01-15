<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if (($se->detailAuth('all')) || ($se->detailAuth('daftar')))
		echo '';
	else
		echo "<script>window.location.href='sanad_gozaresh_notAdmin.php';</script>";
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
		$out='<select name="salMali" id="salMali" class="form-control inp" >
				<option value="" '.(($sal=='')?'selected="selected"':'').' >جاری</option>';
		for($i=0;$i<count($y);$i++)
		{
			$sel = ('_'.$y[$i]==$sal)?'selected="selected"':'';
			$out .='<option '.$sel.' value="_'.$y[$i].'">'.$y[$i].'</option>'."\n";
		}
		$out .='</select>';
		return $out;
	}
	if(isset($_REQUEST['no_taraz']))
	{
		$out = '<center><h3>سندهای فاقد تراز</h3><table width="80%" border="1"><tr>';
		mysql_class::ex_sql("select shomare_sanad,sum(mablagh*typ) as sss FROM `sanad` group by shomare_sanad having sum(mablagh*typ) <>0",$q);
		$i = 1;
		while($r = mysql_fetch_array($q))
		{
			$out .= '<td>'.$r['shomare_sanad'].'</td><td>'.'['.monize($r['sss']).']</td>';
			if($i % 5 == 0)
				$out .= '</tr><tr>';
			$i++;
		}
		$out .= '</tr></table></center>';
		die($out);
	}
	$sal = (isset($_REQUEST['salMali']))?$_REQUEST['salMali']:'';
	$hes  = new hesab_class();
	$qu = "1=0";
	//mysql_class::ex_sql("select count(`id`) from `sanad$sal` where 1=0",$co);
	$canNotGo = FALSE;
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
	      $colspan = '';
	      if($i==0)
		 $colspan = 'colspan="2"';
	      $combo .= "
          <div class=\"col-md-3\" style=\"margin-bottom:5px;\">
           <label class=\"col-md-3 control-label\">$name:</label> <div class=\"col-md-9\">
          
            <select class='form-control inp' id='$tb"."_id' name='$tb"."_id' $onch>";

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
                        $combo.="<option value=\"-2\"> </option><option $sel  value='".$kol_id."' >".$kol->name."</option>";
                }
                else
                {
		      $combo .= "<option value=\"-1\">همه</option>";
		      for($j = 0;$j < count($val);$j++)
		      {
				if(isset($vals[$i]) && $vals[$i]==$val[$j]["id"])
                		{
					$combo .="<option selected=\"selected\" value='".$val[$j]["id"]."' >".$val[$j]["name"]."</option>";
	               		}
				else
               			{
                        		$combo .="<option value='".$val[$j]["id"]."' >".$val[$j]["name"]."</option>";
	               		}
		      }
		}
	      $combo .= "</select></div></div>";
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
//echo $shart;
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
		$kol_column = '<th style="text-align:right;">کل</th>';
		$moeen_column = '<th style="text-align:right;">معین</th>';
	}
	else if($kol_id>0 && $moeen_id==-1)
		$moeen_column = '<th style="text-align:right;">معین</th>';
	$out = '
     <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th><input type="checkbox" onclick="check_all(this);"></th>
                                            <th style="text-align:right;">شماره سند</th>
                                            '.$kol_column.''.$moeen_column.'
                                            <th style="text-align:right;">تاریخ</th>
                                            <th style="text-align:right;">بدهکار</th>
                                            <th style="text-align:right;">بستانکار</th>
                                            <th style="text-align:right;">مانده</th>
                                            <th style="text-align:right;">توضیحات</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
    
	$q=null;
	mysql_class::ex_sql("select * from `sanad$sal` where 1=0",$q);
	if(isset($sdate) && isset($edate) && !$canNotGo)
	{//echo $switch;
		if($se->detailAuth('all'))
		{
			mysql_class::ex_sql("select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad$sal` where $shart  $switch ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
//echo "select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad$sal` where $shart  $switch ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`";
		}
		else if($se->detailAuth('reserve'))
		{
			$q = null;
			mysql_class::ex_sql("select `id` from `sanad$sal` where $shart $switch ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
//echo "select `id` from `sanad$sal` where $shart $switch ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`".'<br/>';
			$arr = array();
			while($r = mysql_fetch_array($q))
				$arr[] = $r['id'];
			$arr = implode(",",$arr);
			$arr = (($arr=='')?-1:$arr);
			$q = null;
			mysql_class::ex_sql("select `sanad_record` from `sanad_reserve$sal` where `sanad_record` in ($arr) ",$q);
			$arr = array();
			while($r = mysql_fetch_array($q))
				$arr[] = $r['sanad_record'];
			$arr = implode(",",$arr);
			$arr = (($arr=='')?-1:$arr);
			$q = null;
			mysql_class::ex_sql("select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad$sal` where `id` in ($arr) ",$q);
		}
		else if($se->detailAuth('hesabdar'))
		{
			$q = null;
			mysql_class::ex_sql("select `id` from `sanad$sal` where $shart $switch ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
			$arr = array();
			while($r = mysql_fetch_array($q))
				$arr[] = $r['id'];
			$arr = implode(",",$arr);
			$arr = (($arr=='')?-1:$arr);
			$q = null;
			mysql_class::ex_sql("select `sanad_record` from `sanad_reserve$sal` where `sanad_record` in ($arr) ",$q);
			$arr = array();
			while($r = mysql_fetch_array($q))
				$arr[] = $r['sanad_record'];
			$arr = implode(",",$arr);
			$arr = (($arr=='')?-1:$arr);
			$q = null;
			mysql_class::ex_sql("select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad$sal` where $shart `id` not in ($arr) and $switch ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
		}
		else
		{
			mysql_class::ex_sql("select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad$sal` where $shart $switch ".(($sfrase!='')?" and `tozihat` like '%$sfrase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`",$q);
		}
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
		$row_style = 'class="odd"';
		if($i%2==0)
			$row_style = 'class="even"';
		$kol_column = '';
		$moeen_column = '';
		if($kol_id==-1)
		{
			$kol_column = "<td>$kol_name</td>";
			$moeen_column = "<td>$moeen_name</td>";
		}
		else if($kol_id>0 && $moeen_id==-1)
			$moeen_column = "<td>$moeen_name</td>";
		$out.="<tr $row_style >";
		$out .="<td>$i</td>
<td><input type='checkbox' id='check_".$r['id']."' name='check_".$r['id']."' ></td>
<td>".$r['shomare_sanad']."</td>$kol_column"."$moeen_column<td>".hamed_pdate($r['tarikh'])."</td><td>".loadBed($mizan)."</td><td>".loadBes($mizan)."</td><td>".$mande_kol."</td><td>".$r['tozihat']." <u><span style=\"color:blue;cursor:pointer;".(($conf->zamaiem)?'':'display:none;')."\" onclick=\"window.open('upload_pic.php?sanad_record_id=".$r['id']."&','',600,400);\">ضمایم</span></u>"."</td>";
		$out.="</tr>\n";
	}
	$mande_kol = loadMande($mande_tmp);
	$kol_column = '';
	$moeen_column = '';
	if($kol_id==-1)
	{
		$kol_column = "<td>--</td>";
		$moeen_column = "<td>--</td>";
	}
	else if($kol_id>0 && $moeen_id==-1)
		$moeen_column = "<td>--</td>";
	//--------------------مانده محدوده جاری -------------------------------------
	$out .="<tr><td></td><td>--</td><td>--</td>$kol_column"."$moeen_column<td>جمع محدوده<br/> جاری</td><td>".enToPerNums(monize($jam_bed))."</td><td>".enToPerNums(monize($jam_bes))."</td><td>".loadMande($jam_bes-$jam_bed)."</td><td>--</td></tr>";
	//---------------------------------------------مانده کل ---------------------
	$out .="<t><td></td><td>--</td><td>--</td>$kol_column"."$moeen_column<td>جمع کل</td><td>".enToPerNums(monize($jam_bed + $mande_az_ghabl_bed))."</td><td>".enToPerNums(monize($jam_bes+ $mande_az_ghabl_bes))."</td><td>$mande_kol</td><td>--</td></tr>";
	$out.='<tbody></table>';
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
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>گزارش اسناد</title>
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
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/bootstrap-datepicker.fa.min.js"></script>
    
      <script>
    $(document).ready(function(){
    
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
        
        
    });
     
    </script>
    
	
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>گزارش اسناد</h4>
                                
                        </div>
                         
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                           
                           <form id='frm1' method='GET' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="row" style="margin-bottom:10px" >
                                       
																		<div class=col-md-2>
																		<a style=margin:0px onclick=loadNoTaraz(); class=btn btn-danger btn-icon input-block-level>
																		<div>سندهای تراز نشده</div>

																		</a>
																		</div>
                                    <div class="col-md-2 pull-left">
                                        <a style="margin:0px" onclick="sbtFrm();" class="btn btn-info btn-icon input-block-level">
            
                                            <div>جستجو</div>
                                        </a>
                                    </div>
                                    
                                </div>
                         
                                 
                                <div class="row" style="margin-bottom:10px">
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">سال مالی:</label> 
                                    <div class="col-md-9"><?php echo loadSalMaliCombo($sal); ?></div>
                                </div>
                                 
                                <?php echo $combo; ?>
                                <div class="row" style="margin-bottom:10px">
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">کلمه کلیدی توضیحات:</label> 
                                    <div class="col-md-9"><input class="form-control inp" type="text" name="sfrase" id="sfrase" value="<?php echo ((isset($_GET["sfrase"]))?$_GET["sfrase"]:"")  ?>"  ></div>
                                </div>
                                </div>
                                <div class="row" style="margin-bottom:20px">
                                    <div class="col-md-1" id="al">
                                    <label class="radio-inline"> 
                                        <div class="">
                                            <span class="checked">
                                                <input type="radio" class="uniform" name="smod" value="kol" <?php echo $ch_kol; ?> />
                                            </span>
                                        </div>
                                        کل حساب 
                                    </label> 
                                        </div>
                                    <div class="col-md-1" id="ta">
                                    <label class="radio-inline"> 
                                        <div class="">
                                            <span class="checked">
                                                <input type="radio" class="uniform" name="smod" value="tarikh" <?php echo $ch_tarikh; ?> />
                                            </span>
                                        </div>
                                       تاریخ 
                                    </label> 
                                    </div>
                                    <div class="col-md-2" id="sa">
                                    <label class="radio-inline"> 
                                        <div class="">
                                            <span class="checked">
                                                <input type="radio" class="uniform" name="smod" value="sanad" <?php echo $ch_sanad; ?> />
                                            </span>
                                        </div>
                                        شماره سند
                                    </label> 
                                    </div>
                                    </div>
                               
                                    <div class="row" style="margin-bottom:10px" id="ta2">
                                <div class="col-md-12">
                                        <div class="col-md-3">
                                        <label class="col-md-3 control-label">از تاریخ:</label> 
                                    <div class="col-md-9"><input class="form-control inp" type="text" name="sdate" id="datepicker1" value="<?php echo ((isset($_GET["sdate"]))?$_GET["sdate"]:"")  ?>"  >
                                    </div>
                                            </div>
                                        <div class="col-md-3">
                                        <label class="col-md-3 control-label">تا تاریخ:</label> 
                                    <div class="col-md-9"><input class="form-control inp" type="text" name="edate" id="datepicker2" value="<?php echo ((isset($_GET["edate"]))?$_GET["edate"]:"")  ?>"  >
                                    </div>
                                         </div>
                                    </label>
                                </div>
                                </div>  
                               
                                <div class="row" style="margin-bottom:10px" id="sa2">
                                <div class="col-md-12">
                                        <div class="col-md-3">
                                        <label class="col-md-3 control-label">از شماره سند:</label> 
                                    <div class="col-md-9"><input class="form-control inp" type="text" name="azshomare" id="datepicker1" value="<?php echo ((isset($_GET["azshomare"]))?$_GET["azshomare"]:"")  ?>"  >
                                    </div>
                                            </div>
                                        <div class="col-md-3">
                                        <label class="col-md-3 control-label">تا شماره سند:</label> 
                                    <div class="col-md-9"><input class="form-control inp" type="text" name="tashomare" id="datepicker2" value="<?php echo ((isset($_GET["tashomare"]))?$_GET["tashomare"]:"")  ?>"  >
                                    </div>
                                         </div>
                                    </label>
                                </div>
                                </div>
                               <?php if($msg){?><button class="btn btn-block btn-yellow"><?php echo $msg; ?></button><?php }?>
                    <input type="hidden" name="check" id="check" value="-1"  > 
						<input type="hidden" id="jam_bed" name="jam_bed" value="<?php echo $GLOBALS["jam_bed"]; ?>" />
						<input type="hidden" id="jam_bes" name="jam_bes" value="<?php echo $GLOBALS["jam_bes"]; ?>" />
                                <input type='hidden' name='mod' id='mod' value='1' >
                               
                            </div>
                          </form>

                            
                            
                            
                        </div>
                        <?php echo $out; ?>
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
    <div id="no_taraz" dir="ltr"></div>	
    	<!-- Modal -->
    <!-- Modal : anbar modal -->
    <div class="modal fade" id="anbar-modal">
	
    </div>
			<!--/Modal : anbar modal-->
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
	
	<!-- DATE RANGE PICKER -->
    <script src="<?php echo $root ?>js/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-datepicker.fa.js"></script>
	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    
	<script>
	
		var i=0;
		var SSmsg = null;
	
		jQuery(document).ready(function() {
            
            $("#ta2").slideUp();
            $("#ta").click(function(){
                $("#sa2").slideUp(function(){
                $("#ta2").slideDown();
            });
                
            })
            $("#sa2").slideUp();
            $("#sa").click(function(){
                $("#ta2").slideUp(function(){
                $("#sa2").slideDown();
            });
                
            });
            $("#al").click(function(){
               $("#sa2").slideUp();
                $("#ta2").slideUp();
            });
            $("#loading").hide(); 
            App.setPage("gallery");  //Set current page
			//App.setPage("widgets_box");  //Set current page
			App.init(); //Initialise plugins and elements
			getofflist();
            
            
             $('#dataTables-example').DataTable({
                responsive: true
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
        function loadNoTaraz()
		    {
			$("#no_taraz").html('<img src="../img/status_fb.gif" />');
			$.get("sanad_gozaresh.php",{'no_taraz':'n'},function(result){
				$("#no_taraz").html(result);
			});
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