<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$msg = "";
	$insert = "";
	$GLOBALS['msg']= '';
	$GLOBALS["err_new"] = "";
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
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function loadReserve($inp)
	{
		$inp = (int)$inp;
		$out = "";
		/*
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
                        $hotel = '';
			$khadamat = '';
                        mysql_class::ex_sql("select `lname` from `hotel_reserve` where `reserve_id` = $reserve_id",$q);
                        if($r = mysql_fetch_array($q))
                                $lname = $r['lname'];
			$q = null;
			mysql_class::ex_sql("select * from `khadamat_det` where `reserve_id` = $reserve_id group by `khadamat_id`",$q);
			while($rk=mysql_fetch_array($q))
			{
				$kh = new khadamat_class((int)$rk['khadamat_id']);
				$khname = $kh->name;
				$khadamat .= (($khadamat=='')?' با خدمات '.$khname:','.$khname);
			}
                        $q = null;
			mysql_class::ex_sql("select * from `room_det` where `reserve_id` = $reserve_id",$q);
                        if($r = mysql_fetch_array($q))
                        {
                                $aztarikh = jdate("d / m / Y",strtotime($r['aztarikh']));
                                $tatarikh = jdate("d / m / Y",strtotime($r['tatarikh']));
                                $nafar = $r['nafar'];
                                $room = new room_class((int)$r['room_id']);
                                $hotel = new hotel_class((int)$room->hotel_id);
				$hotel = $hotel->name;
                        }
                        $out = "$lname تعداد نفرات $nafar نفر از $aztarikh تا $tatarikh جهت ".$hotel.$khadamat." شماره رزرو $reserve_id";
                }
                else
                {
		*/
                mysql_class::ex_sql("select `tozihat` from `sanad` where `id`='$inp'",$q);
                if ($r = mysql_fetch_array($q))
                {
                        $out = "<u><span style=\"color:Blue;cursor:pointer;\" onclick=\"wopen('edit_toz.php?sanad=$inp&','',900,300);\">".(($r["tozihat"]=="")?"---":$r["tozihat"])."</span></u>";
                }
                //}
		return($out);
	}
	function loadShomareSand()
	{
		$out = 1;
		mysql_class::ex_sql("select (MAX(`shomare_sanad`)+1) as `ss` from `sanad`",$q);
		if($r = mysql_fetch_array($q))
		{
			$out = (int)$r["ss"];
		}
		return($out);
	}
	function loadHesab($inp)
	{
		$out = '';
		$pageSelector = 0;
		$onclick = 'alert(\'عملیات مجاز نیست\')';
		$se = security_class::auth((int)$_SESSION['user_id']);
		$pageCount = $GLOBALS['grid']->pageCount;
		if(isset($_REQUEST['pageSelector']))
			$pageSelector = $_REQUEST['pageSelector'];
		if(isset($_REQUEST['pageCount_'.$GLOBALS['grid']->gridName]))
			$pageCount = $_REQUEST['pageCount_'.$GLOBALS['grid']->gridName];

		if($se->detailAuth('all') || ($se->detailAuth('hesabdar')  || $se->detailAuth('all_hesabdar') && (sanadzan_class::checkSanad($GLOBALS["shomare_sanad"])) ) )
			$onclick = "window.location ='select_hesab.php?sel_id=$inp&form_shomare_sanad=".$GLOBALS["shomare_sanad"]."&gridName=".$GLOBALS['grid']->gridName."&pageSelector=$pageSelector&pageCount=$pageCount';";
		if($se->detailAuth('all_hesabdar'))
		{
			$checkSanad = sanad_class::checkTime_sanad($GLOBALS["shomare_sanad"]);
			if (!$checkSanad)
				$out = "";
			else
				$out = "<input type='button' value='انتخاب حساب' class='inp' onclick=\"$onclick\"  >";
		}
		else
			$out = "<input type='button' value='انتخاب حساب' class='inp' onclick=\"$onclick\"  >";			
		return $out;
	}
	function idToCode($id,$tb)
        {
                $out = -1;
                $id = (int)$id;
                mysql_class::ex_sql("select `code`,`name` from `$tb` where `id` = '$id'",$q);
                if($r = mysql_fetch_array($q))
                {
                        $out =(int)$r['code'];
                }
                return($out);
        }
        function idToCodeName($id,$tb)
        {
                $out = -1;
                $id = (int)$id;
                mysql_class::ex_sql("select `code`,`name` from `$tb` where `id` = '$id'",$q);
                if($r = mysql_fetch_array($q))
                {
                        $out =$r['name'];
                }
                return($out);
        }
	function idToCodeGroup($id)
        {
                $out = '';
                $id = (int)$id;
		$tb = 'grooh';
                mysql_class::ex_sql("select `code`,`name` from `$tb` where `id` = '$id'",$q);
                if($r = mysql_fetch_array($q))
                {
			$out .=$r['name'];
       	                $out .='<br/>';
                        $out .= $r["code"];
                }
                return($out);
        }
	function idToCodeKol($id)
        {
                $out = '';
                $id = (int)$id;
		$tb = "kol";
                mysql_class::ex_sql("select `code`,`name` from `$tb` where `id` = '$id'",$q);
                if($r = mysql_fetch_array($q))
                {	
			$out .=$r['name'];
                        $out .='<br/>';
                        $out .= $r["code"];
                }
                return($out);
        }
	function idToCodeMoeen($id)
        {
		$tb = "moeen";
                $out = '';
                $id = (int)$id;
                mysql_class::ex_sql("select `code`,`name` from `$tb` where `id` = '$id'",$q);
                if($r = mysql_fetch_array($q))
                {
			$out .=$r['name'];
                        $out .='<br/>';
                        $out .= $r["code"];
                }
                return($out);
        }
	function idToCodeTafzili($id)
        {
		$tb = "tafzili";
                $out = '';
                $id = (int)$id;
                mysql_class::ex_sql("select `code` from `$tb` where `id` = '$id'",$q);
                if($r = mysql_fetch_array($q))
                {
                        $out = (int)$r["code"];
                }
                return($out);
        }
	function idToCodeTafzili2($id,$tb="tafzili2")
        {
                $out = -1;
                $id = (int)$id;
                mysql_class::ex_sql("select `code` from `$tb` where `id` = '$id'",$q);
                if($r = mysql_fetch_array($q))
                {
                        $out = (int)$r["code"];
                }
                return($out);
        }
	function idToCodeTafzilishenavar($id,$tb="tafzilishenavar")
        {
                $out = -1;
                $id = (int)$id;
                mysql_class::ex_sql("select `code` from `$tb` where `id` = '$id'",$q);
                if($r = mysql_fetch_array($q))
                {
                        $out = (int)$r["code"];
                }
                return($out);
        }
	function idToCodeTafzilishenavar2($id,$tb="tafzilishenavar2")
        {
                $out = -1;
                $id = (int)$id;
                mysql_class::ex_sql("select `code` from `$tb` where `id` = '$id'",$q);
                if($r = mysql_fetch_array($q))
                {
                        $out = (int)$r["code"];
                }
                return($out);
        }

	function codeToId($code,$tb,$ptb,$pval)
	{
		$out = -1;
		$tmp = explode(" ",$code);
		if(count($tmp)>1)
			$code = $tmp[0];	
                $code = (int)$code;
		$wer =""; 
		if($ptb!="")
		{
			$wer = " and `$ptb"."_id`='$pval'";
		}
		
                mysql_class::ex_sql("select `id` from `$tb` where `code` = '$code' $wer  ",$q);
		if($r = mysql_fetch_array($q))
                {
                        $out = (int)$r["id"];
                }
                return($out);

	}
	function loadMablagh($inp)
        {
                $out =$inp;
                return monize($out);
        }
	function delete_item($id)
	{
		mysql_class::ex_sql("select `sanad_record` from `sanad_reserve` where `sanad_record`=$id",$q);
		if($r = mysql_fetch_array($q))
			$GLOBALS["err_new"]= 'رکورد سند مورد نظر اتوماتیک تولید شده است لذا قابل حذف نمی‌باشد';
		else
		{
			mysql_class::ex_sqlx("delete from `sanad` where `id`=$id and `id` not in (select `sanad_record` from `sanad_reserve` where `sanad_record`=$id) ");
			mysql_class::ex_sqlx("delete from `upload` where `sanad_record_id`='$id'");
		}
	}
	function add_item()
        {
		
		 $conf = new conf;
		if (isset ($_REQUEST["form_tarikh_sanad"]))
		{
			$tarikh_sanad=$_REQUEST["form_tarikh_sanad"];
		}
		else
		{
			$tarikh_sanad=-1;
		}
                $fields = null;
		
                foreach($_REQUEST as $key => $value)
		{
                        if(substr($key,0,4)=="new_")
			{
                                if($key != "new_en" && $key != "new_id" )
				{
                                        $fields[substr($key,4)] =perToEnNums($value);
                                }
                        }
		}
			$shmore_sanad = $GLOBALS["shomare_sanad"];
			if($shmore_sanad==-1)
			{
				$GLOBALS["err_new"] = "سند جدید انتخاب نشده است";
			}
			else
			{
				$fields["shomare_sanad"] = $shmore_sanad;
				$query = '';
				$hesab  = $conf->hesabKol();
	        	        foreach($hesab as $meghdar=>$value)
        	        	{
                	        	if($value==null)
                        		{
                          			unset($hesab[$meghdar]);
                	  		}
                		}
				$j= 0;
				$old_value= -1;
				$old_tb = "";
				foreach($hesab as $key=>$value)	
				{
					$key_no_id = substr($key,0,-3);
					$fields[$key] = codeToId($fields[$key],$key_no_id,$old_tb,$old_value);
					$old_tb = $key_no_id;
					$old_value = $fields[$key];
				}
				$fields["tarikh"]=$tarikh_sanad;
				$fields["tarikh"] = hamed_pdateBack(audit_class::perToEn($fields["tarikh"]));
				$fields["mablagh"] = umonize($fields["mablagh"]);
				$fields["user_id"] = (int)$_SESSION['user_id'];
				$fields["id"] = null;
		                $fi = "(";
        		        $valu="(";
				foreach ($fields as $field => $value)
		        	{
                		        $fi.="`$field`,";
		                        $valu .="'$value',";
		                }
	               		$fi=substr($fi,0,-1);
		                $valu=substr($valu,0,-1);
               			$fi.=")";
	                	$valu.=")";
	                	$query.="insert into `sanad` $fi values $valu";
				if(!($fields["kol_id"] == -1 || $fields["moeen_id"] == -1))
				{
           				mysql_class::ex_sqlx($query);
					log_class::add("sanad_new",$fields["user_id"],"ثبت سند به شماره $shmore_sanad");
					
				}
				else
					echo '<div align="center" ><span style="color:red;" >کد حساب کل یا معین وارد نشده است</span></div>';
			}
	}
	function loadUser($user_id)
	{
		$user_id = (int)$user_id;
		$out = new user_class($user_id);
		$out = $out->fname . ' ' . $out->lname;
		$out = (($out==' ')?'نامشخص':$out);
		return($out);
	}
	function loadUsers($user_id='')
	{
		$out = array();
		mysql_class::ex_sql("select `id`,`lname`,`fname` from `user` where `user`<>'mehrdad' order by `lname`,`fname`",$q);
		while($r=mysql_fetch_array($q))
			$out[$r['lname'].' '.$r['fname']] = $r['id'];
		return $out;
	}
	function edit_item($id,$feild,$value)
	{
		//if($feild == 'kol_id')
	}
	function loadUpload($id)
	{
		$out = "<a target=\"_blank\" href=\"upload_pic.php?sanad_record_id=$id&\">مشاهده</a>";
		return $out;
	}
	if ($se->detailAuth('dafater'))
	{
		mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `sh_sanad` from `sanad`",$q);
		if($r = mysql_fetch_array($q))
			$GLOBALS["shomare_sanad"] = $r['sh_sanad'];
		else
			$GLOBALS["shomare_sanad"] = -1;
	}
	else
		$GLOBALS["shomare_sanad"] = -1;
	$sabt_style='';
	if(isset($_REQUEST["sel_id"]))
	{
		$hesab  = $conf->hesabKol();
		$GLOBALS["shomare_sanad"] = (int)$_REQUEST["form_shomare_sanad"];
	        foreach($hesab as $meghdar=>$value)
        	{
                	if($value==null)
                	{
                        	unset($hesab[$meghdar]);
                	}
        	}
		$id = (int)$_REQUEST["sel_id"];
		if($id==-1)
		{
			$insert = "<script>";
            $i=1;
			foreach($hesab as $meghdar=>$value)
                        {
                $new[$i]['code'] = idToCode((int)$_REQUEST["$meghdar"],substr($meghdar,0,-3));
                $new[$i]['name'] = idToCodeName((int)$_REQUEST["$meghdar"],substr($meghdar,0,-3));
				$insert .="document.getElementById('new_$meghdar').value ='".idToCode((int)$_REQUEST["$meghdar"],substr($meghdar,0,-3))." (".idToCodeName((int)$_REQUEST["$meghdar"],substr($meghdar,0,-3)).")';\n";
				$insert .="document.getElementById('new_$meghdar').style.width = \"auto\";\n";
                $i++;
			}
			$insert .= "</script>";
		}
		else
		{
			$q = null;
                        mysql_class::ex_sql("select `shomare_sanad` from `sanad` where `id`='$id'",$q);
                        if($r = mysql_fetch_array($q))
                        {
                                $GLOBALS["shomare_sanad"] =(int)$r["shomare_sanad"];
                        }
			foreach($hesab as $meghdar=>$value)
			{
				mysql_class::ex_sqlx("update `sanad` set `". $meghdar."`='".$_REQUEST["$meghdar"]."' where `id` = $id");
				log_class::add("sanad_new",$fields["user_id"],"ا�صلاح مبلغ سند ".$GLOBALS["shomare_sanad"]);
			}
		}
	}
	if(isset($_POST['form_shomare_sanad']))
	{
		$GLOBALS["shomare_sanad"] = (int)$_POST['form_shomare_sanad'];
	}
	$shomare_sanad = $GLOBALS["shomare_sanad"];
	if (isset ($_REQUEST["form_tarikh_sanad"]))
                {
                        $tarikh_sanad=$_REQUEST["form_tarikh_sanad"];
                }
                else
                {
                        $q = null;
			mysql_class::ex_sql("select `tarikh` from sanad where `shomare_sanad`= '$shomare_sanad'",$q);
			if($r = mysql_fetch_array($q))
			{
				$tarikh_sanad =enToPerNums( hamed_pdate($r["tarikh"]));
			}
			else
                        	$tarikh_sanad=enToPerNums(hamed_pdate(date("Y-m-d")));
                }
	if(isset($_REQUEST['mod']))
	{
		$mod = $_REQUEST['mod'];
		if($mod == "add")
		{
			$shomare_sanad = sanadzan_class::getNewShomareSanad(date("Y-m-d"));
			$tarikh_sanad = enToPerNums(hamed_pdate(date("Y-m-d")));
		}
		else if($mod == "edit")
		{
			if($se->detailAuth('all') || ($se->detailAuth('hesabdar') || $se->detailAuth('all_hesabdar') && (sanadzan_class::checkSanad($shomare_sanad) || $conf->limit_sanad_time==-1 ) ) )
				mysql_class::ex_sqlx("update `sanad` set `en` = '0' where `shomare_sanad` = '$shomare_sanad'");
			$q = null;
			mysql_class::ex_sql("select `tarikh` from sanad where `shomare_sanad`= '$shomare_sanad'",$q);
			if($r = mysql_fetch_array($q))
				$tarikh_sanad =enToPerNums( hamed_pdate($r["tarikh"]));
		}
		else if($mod == "sabt")
                {
			$q = null;
			mysql_class::ex_sql("select sum(`mablagh`*`typ`) as jam from `sanad`  where `shomare_sanad`= '$shomare_sanad' ",$q);
			if($r = mysql_fetch_array($q))
			{
				if((int)$r["jam"]===0)
				{
					$tmp=hamed_pdateBack(audit_class::perToEn($tarikh_sanad));
					mysql_class::ex_sqlx("update `sanad` set `en` = '1',`tarikh` = '$tmp' where `shomare_sanad` = '$shomare_sanad'");

				}
				else
					$msg = "<script>alert('سند شما تراز نمی‌باشد');</script>";
			}
                }

	}
	//var_dump($_REQUEST);
	$status=array();
	$status["موقت"]=0;
	$hesab_type = array();
        $hesab_type["بستانکار"]=1;
	$hesab_type["بدهکار"]=-1;
//	$status["دائمی"]=1;
	$grid = new jshowGrid_new("sanad","grid1");
	/***************Headers*****************************/
	$grid->columnHeaders[0] = "انتخاب حساب";
	$grid->columnHeaders[1] = null;//shomare_sanad
	$grid->columnHeaders[2] = $conf->hesab("group_id");
	$grid->columnHeaders[3] = $conf->hesab("kol_id");
	$grid->columnHeaders[4] = $conf->hesab("moeen_id");
	$grid->columnHeaders[5] = $conf->hesab("tafzili_id");
	$grid->columnHeaders[6] = $conf->hesab("tafzili2_id");
	$grid->columnHeaders[7] = $conf->hesab("tafzilishenavar_id");
	$grid->columnHeaders[8] = $conf->hesab("tafzilishenavar2_id");
	$grid->columnHeaders[9] = null;
	$grid->columnHeaders[10] = 'کاربر ثبت کننده';//user_id
	$grid->columnHeaders[11] = "بستانکار/بدهکار";
	$grid->columnHeaders[12] = "توضیحات";
	$grid->columnFilters[12] =((isset($_REQUEST['sfrase']) && $_REQUEST['sfrase']!='')?$_REQUEST['sfrase']:TRUE);
	$grid->columnHeaders[13] = "وضعیت";
	$grid->columnHeaders[14] = "مبلغ";
	$rec["form_shomare_sanad"] = $shomare_sanad;
	$grid->setERequest($rec);
	$rec["form_tarikh_sanad"] = $tarikh_sanad;
        $grid->setERequest($rec);
	if(isset($_REQUEST['sfrase']))
	{
		$rec["sfrase"] =$_REQUEST['sfrase'];
		$grid->setERequest($rec);
	}
	$colspan = 4;
	$tmp_werc = '';
	if($se->detailAuth('all'))
	{
		$grid->whereClause = "`en` = '0' and `shomare_sanad` = '$shomare_sanad' order by `id`";
		$grid->columnFilters[10] = TRUE;
		$grid->columnLists[10] = loadUsers();
		if($conf->zamaiem)
		{	
			$colspan = 5;
			$grid->addFeild('id');
			$grid->columnAccesses[15] = 0;
			$grid->columnHeaders[15] = 'ضمایم';
			$grid->columnFunctions[15] = 'loadUpload';
		}
	}
	elseif($se->detailAuth('dafater'))
	{
		$daftar_id = $_SESSION['daftar_id'];
		mysql_class::ex_sql("select `kol_id` from `daftar` where `id`='$daftar_id' order by `id`",$q_kol);
		if($r_kol=mysql_fetch_array($q_kol,MYSQL_ASSOC))
		{
			$kol_id = $r_kol['kol_id'];
			$tmp_werc = " and `kol_id`='$kol_id ' ";
		}
		$grid->whereClause = "`en` = '0' and `shomare_sanad` = '$shomare_sanad' $tmp_werc order by `id`";
		$grid->columnFilters[10] = TRUE;
		$grid->columnLists[10] = loadUsers();
		if($conf->zamaiem)
		{	
			$colspan = 5;
			$grid->addFeild('id');
			$grid->columnAccesses[15] = 0;
			$grid->columnHeaders[15] = 'ضمایم';
			$grid->columnFunctions[15] = 'loadUpload';
		}
	}
	else if($se->detailAuth('all_hesabdar'))
	{
		$daftar_id = 35;
		mysql_class::ex_sql("select `kol_id` from `daftar` where `id`='$daftar_id' order by `id`",$q_kol);
		if($r_kol=mysql_fetch_array($q_kol,MYSQL_ASSOC))
		{
			$kol_id = $r_kol['kol_id'];
			$tmp_werc = " and `kol_id`!='$kol_id ' ";
		}
		$grid->whereClause = "`en` = '0' and `shomare_sanad` = '$shomare_sanad' $tmp_werc order by `id`";
		$grid->columnFilters[10] = TRUE;
		$grid->columnLists[10] = loadUsers();
		if($conf->zamaiem)
		{	
			$colspan = 5;
			$grid->addFeild('id');
			$grid->columnAccesses[15] = 0;
			$grid->columnHeaders[15] = 'ضمایم';
			$grid->columnFunctions[15] = 'loadUpload';
		}
		$checkSanad = sanad_class::checkTime_sanad($shomare_sanad);
		if ($checkSanad==1)
		{
			$grid->canEdit = TRUE;
			$grid->canDelete = TRUE;
			$grid->canAdd = TRUE;
		}
		else
		{
			$grid->canEdit = FALSE;
			$grid->canDelete = FALSE;
			$grid->canAdd = FALSE;
		}
		
		
	}
	else if($se->detailAuth('reserve'))
	{
		$daftar_id = 35;
		mysql_class::ex_sql("select `kol_id` from `daftar` where `id`='$daftar_id' order by `id`",$q_kol);
		if($r_kol=mysql_fetch_array($q_kol,MYSQL_ASSOC))
		{
			$kol_id = $r_kol['kol_id'];
			$tmp_werc = " and `kol_id`!='$kol_id ' ";
		}
		$q = null;
		$arr = array();
		mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad` = '$shomare_sanad' ",$q);
		while($r = mysql_fetch_array($q))
			$arr[] = $r['id'];
		$arr = implode(",",$arr);
		$arr = (($arr=='')?-1:$arr);
		$q = null;
		mysql_class::ex_sql("select `sanad_record` from `sanad_reserve` where `sanad_record` in ($arr) $tmp_werc ",$q);
		$arr = array();
		while($r = mysql_fetch_array($q))
			$arr[] = $r['sanad_record'];
		$arr = implode(",",$arr);
		$arr = (($arr=='')?-1:$arr);
		$grid->whereClause = " `id` in ($arr) order by `id`";
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
		$grid->columnFunctions[10] = 'loadUser';
	}
	else if($se->detailAuth('hesabdar'))
	{
		$daftar_id = 35;
		mysql_class::ex_sql("select `kol_id` from `daftar` where `id`='$daftar_id' order by `id`",$q_kol);
		if($r_kol=mysql_fetch_array($q_kol,MYSQL_ASSOC))
		{
			$kol_id = $r_kol['kol_id'];
			$tmp_werc = " and `kol_id`!='$kol_id ' ";
		}
		$q = null;
		$arr = array();
		mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad` = '$shomare_sanad' ",$q);
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
		$grid->whereClause = " `en`=0 and `shomare_sanad` = '$shomare_sanad' and `id` not in ($arr) $tmp_werc order by `id`";
		$grid->columnLists[10] = loadUsers();
		$grid->columnFilters[10] = TRUE;
                if($conf->zamaiem)
                {
                        $colspan = 4;
                        $grid->addFeild('id');
                        $grid->columnAccesses[15] = 0;
                        $grid->columnHeaders[15] = 'ضمایم';
                        $grid->columnFunctions[15] = 'loadUpload';
                }
	//	if($conf->limit_sanad_time>0) // ---- وجود شرط در کانف سیستم
	//	{
			$bool = sanadzan_class::checkSanad($shomare_sanad);
			if(!$bool)
			{
				$grid->whereClause = " `shomare_sanad` = '$shomare_sanad' and `id` not in ($arr) order by `id`";
				$grid->canAdd = FALSE;
				$grid->canEdit = FALSE;
				$grid->canDelete = FALSE;
				$status["نهایی"]=1;
				$sabt_style = 'style="display:none;"';
			}
		//}
	}
	else
		$grid->columnFunctions[10] = 'loadUser';
/***************Functions***************************/
//echo $grid->whereClause;
	$grid->columnFunctions[0] = "loadHesab";
	$grid->columnFunctions[2] = "idToCodeGroup";
        $grid->columnFunctions[3] = "idToCodeKol";
        $grid->columnFunctions[4] = "idToCodeMoeen";
        $grid->columnFunctions[5] = "idToCodeTafzili";
        $grid->columnFunctions[6] = "idToCodeTafzili2";
        $grid->columnFunctions[7] = "idToCodeTafzilishenavar";
        $grid->columnFunctions[8] = "idToCodeTafzilishenavar2";
	$grid->columnFunctions[9]="hamed_pdate";
        $grid->columnCallBackFunctions[9]="hamed_pdateBack";
	$grid->columnFunctions[14]="loadMablagh";
	$grid->columnCallBackFunctions[14] = "umonize";	
	$grid->addFunction = "add_item";
//---------------------------javaScript-----------------
	$grid->columnJavaScript[14] = 'onkeyup="monize(this);"';
//	$grid->editFunction = "edit_item";
	$grid->deleteFunction = "delete_item";
/***************************************************/
/**********************LISTS************************/
	$grid->columnLists[13]=$status;
	$grid->columnLists[11]=$hesab_type;
	$grid->divProperty = "overflow:'';";
	$grid->columnAccesses[0] = 0;
	$grid->columnAccesses[2] = 0;
	$grid->columnAccesses[3] = 0;
	$grid->columnAccesses[4] = 0;
	$grid->columnAccesses[5] = 0;
	$grid->columnAccesses[6] = 0;
	$grid->columnAccesses[7] = 0;
	$grid->columnAccesses[8] = 0;
	$grid->columnAccesses[10] = 0;
	$grid->columnAccesses[13] = 0;
	$grid->pageCount=5;
	$grid->gotoLast = TRUE;
	$grid->index_width = '50px';
	$grid->width = '99%';
	if (isset($_REQUEST['tedad']))              
	{
		if (($_REQUEST['tedad'])==10)                                
	        {
	       	        $grid->pageCount=10;
		}
		if (($_REQUEST['tedad'])==50)  
                {
        	        $grid->pageCount=50;
                }
		if (($_REQUEST['tedad'])==-1)  
                {
			mysql_class::ex_sql("select count(`id`) from `sanad` where `shomare_sanad` = '$shomare_sanad'",$co);
			$outq=mysql_fetch_array($co);
                	$grid->pageCount=(int)$outq[0]+1;
                }
	}
	$grid->intial();
	$grid->executeQuery();
	//-----------------mohasebe taraz , majmoo-----------
	mysql_class::ex_sql("select sum(`mablagh`*`typ`) as jam from `sanad` where  `shomare_sanad` ='$shomare_sanad'" ,$gg);
	mysql_class::ex_sql("select sum(`mablagh`) as jam from `sanad` where  `typ`=1 and `shomare_sanad` ='$shomare_sanad'" ,$qbes);
		mysql_class::ex_sql("select sum(`mablagh`) as jam from `sanad` where `typ`=-1 and `shomare_sanad` ='$shomare_sanad'" ,$qbed);
	//echo "select sum(`mablagh`*`typ`) as jam from `sanad` where `en` = '0' and `shomare_sanad` ='$shomare_sanad'";
	if($r = mysql_fetch_array($gg))
	{
		$jam =(($r['jam']>0)?monize(abs($r['jam']))."&nbsp;بستانکار":monize(abs($r['jam']))."&nbsp;بدهکار") ;
		if((int)$r['jam']==0 )
			$jam = $r['jam']."&nbsp;تراز";
	}
	if($r = mysql_fetch_array($qbes))
		$jam_bes = monize($r['jam']);
	if($r = mysql_fetch_array($qbed))
		$jam_bed = monize($r['jam']);
	//-----------------------------
	$GLOBAS['grid'] =$grid; 
	$selected_tedad = ((isset($_REQUEST["tedad"]))?(int)$_REQUEST["tedad"]:-3);
	$grid->footer = "<tr class='showgrid_insert_row'  ><td><form method=\"get\" id=\"frmtedad\"><select name=\"tedad\" id=\"tedad\"><option ".(($selected_tedad==-3)?"selected=\"selected\"":"")." value=\"-3\"></option><option ".(($selected_tedad==10)?"selected=\"selected\"":"")."  value=\"10\">10</option><option ".(($selected_tedad==50)?"selected=\"selected\"":"")." value=\"50\">50</option><option ".(($selected_tedad==-1)?"selected=\"selected\"":"")." value=\"-1\">همه</option> </select><input class=\"inp\" type=\"submit\" name=\"sutedad\" value=\"نمایش\"/></form></td><td>&nbsp;</td><td align='center' ><input type='button' value='انتخاب حساب' class='inp' onclick=\"window.location ='select_hesab.php?sel_id=-1&form_shomare_sanad=".$shomare_sanad."&tedad=$selected_tedad&';\"  ></td><td colspan='$colspan' >&nbsp;</td><td>جمع بدهکار:$jam_bed&nbsp;&nbsp;&nbsp;جمع بستانکار: $jam_bes&nbsp;&nbsp;&nbsp;مجموع:</td><td align='center' >$jam</td><td>&nbsp;</td></tr>";
/***************************************************/
	
	//$out = $grid->getGrid();

$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">کل</th>
                                        <th style=\"text-align:right;\">معین</th>
                                        <th style=\"text-align:right;\">کاربر ثبت کننده</th>
                                        <th style=\"text-align:right;\">بستانکار / بدهکار</th>
                                        <th style=\"text-align:right;\">توضیحات</th>
                                        <th style=\"text-align:right;\">وضعیت</th>
                                        <th style=\"text-align:right;\">مبلغ</th>
                                        <th style=\"text-align:right;\">ضمائم</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";
mysql_class::ex_sql("select * from `sanad` where ".$grid->whereClause." ",$q);
$i=1;
		while($r = mysql_fetch_array($q))
		{
            $id = $r['id'];
            $kol_id = $r['kol_id'];
            mysql_class::ex_sql("select `name` from `kol` where `id` = '$kol_id' ",$k_id);
            $k_id1 = mysql_fetch_array($k_id);
            $kname = $k_id1['name'];
            $moeen_id = $r['moeen_id'];
            mysql_class::ex_sql("select `name` from `moeen` where `id` = '$moeen_id' ",$m_id);
            $m_id1 = mysql_fetch_array($m_id);
            $mname = $m_id1['name'];
            $user_id = $r['user_id'];
            mysql_class::ex_sql("select * from `user` where `id` = '$user_id' ",$u_id);
            $u_id1 = mysql_fetch_array($u_id);
            $ufname = $u_id1['fname'];
            $ulname = $u_id1['lname'];
            $uname = $ufname." ".$ulname;
            $typ = $r['typ'];
            $type="";
            if($typ==1)
                $type="بستانکار";
            if($typ==-1)
                $type="بدهکار";
            $tozihat = $r['tozihat'];
            $en = $r['en'];
            $ena="";
            if($en==0)
                $ena="موقت";
            else if($en==1)
                $ena="ثبت نهایی";
            $mablagh = $r['mablagh'];
            if(fmod($i,2)!=0){
        $out.="
       <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$kname</td>
                                        <td>$mname</td>
                                        <td>$uname</td>
                                        <td>$type</td>
                                        <td>$tozihat</td>
                                        <td>$ena</td>
                                        <td>$mablagh</td>
                                        <td>".loadUpload($id)."</td>
                                        <td>";
                                            if($en==0) {$out.="
                                            <a onclick=\"editGfunc('".$id."','".$typ."','".$mablagh."','".$tozihat."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>";}$out.="
                                        </td>
                                    </tr>
        ";
        $i++;
    }
    else{
        $out.="
        <tr class=\"even\">
                                        <td>$i</td>
                                        <td>$kname</td>
                                        <td>$mname</td>
                                        <td>$uname</td>
                                        <td>$type</td>
                                        <td>$tozihat</td>
                                        <td>$ena</td>
                                        <td>$mablagh</td>
                                        <td>".loadUpload($id)."</td>
                                        <td>";
                                            if($en==0) {$out.="
                                            <a onclick=\"editGfunc('".$id."','".$typ."','".$mablagh."','".$tozihat."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>";}$out.="
                                        </td>
                                    </tr>
        ";
        $i++;
    }
        }
$out.="</tbody></table>";
$kolk = $new[1]['code'];
mysql_class::ex_sql("select `name` from `kol` where `id` = '$kolk' ",$kk_id);
$kk_id1 = mysql_fetch_array($kk_id);
$kkname = $kk_id1['name'];
$moeenk = $new[2]['code'];
mysql_class::ex_sql("select `name` from `moeen` where `id` = '$moeenk' ",$km_id);
$km_id1 = mysql_fetch_array($km_id);
$kmname = $km_id1['name'];
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>ثبت سند</title>
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
    <script type="text/javascript" src="../js/tavanir.js"></script>
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
    <script>
		function setEmpty(Obj)
		{
			Obj.value= '';
		}
		function send_frm()
		{
			document.getElementById('mod').value = 'sabt';
			document.getElementById('frm1').submit();
		}
		function send_edit()
		{
			document.getElementById('form_shomare_sanad').value =unFixNums(document.getElementById('form_shomare_sanad').value) ;
			document.getElementById('mod').value = 'edit';
			document.getElementById('frm1').submit();
		}
		</script>
	
	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
	
</head>
<body>
    <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
    
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-money"></i>ثبت سند</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                             <a onclick="newGG()"  data-toggle="modal"><button style="margin:5px;" class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن سند جدید</button></a>
                            <span style="color:red;font-size:16px;"  ><?php echo $GLOBALS["err_new"];  ?></span>
                        <br/>
                           <form id="frm1" method="POST"  >
                               <input type="hidden" class="form-control" value="<?php echo $new[1][code]; ?>" name="kkkolcode" disabled />
                            <input type="hidden" class="form-control" value="<?php echo $new[2][code]; ?>" name="mmmoeencode" disabled />
                                <div class="col-md-12 row">
                                   <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">شماره سند:</label> 
                                    <div class="col-md-8">
                                            <input class="form-control inp"  id="form_shomare_sanad" name="form_shomare_sanad" value="<?php echo $shomare_sanad; ?>" />
                                    </div>
                                </div>
                                 <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">تاریخ:</label> 
                                    <div class="col-md-8">
                                            <input class="form-control inp" readonly="readonly"  id="form_tarikh_sanad" name="form_tarikh_sanad" value="<?php echo $tarikh_sanad; ?>"/>
                                    </div>
                                </div>  
                              <div class="col-md-3" style="margin-bottom:5px;">
                                   <input type='button' value='انتخاب حساب' class='btn btn-success inp col-md-12' onclick="window.location ='select_hesab.php?sel_id=-1&form_shomare_sanad=<?php echo $shomare_sanad ?>&tedad=<?php echo $selected_tedad ?>&'">
                                       </div>
				<?php if (!($se->detailAuth('dafater'))) {?>
                               <div class="col-md-3" style="margin-bottom:5px;">
				<input type="button" value="جدید" class="inp btn btn-info col-md-12"  onclick="document.getElementById('mod').value = 'add';document.getElementById('frm1').submit();">
                                   </div>
				<?php }?>
                               <div class="col-md-3" style="margin-bottom:5px;">
				<input type="button" class="inp btn btn-warning col-md-12"  value="ویرایش" onclick="send_edit();">
                                   </div>
                                   </div>
				
               
						
				                <input type="hidden" id="mod" name="mod" value="add" >
                               
                                
			</form>
                        <br/> 
                        <?php echo $out;  ?>
			<?php if($se->detailAuth('all') || $se->detailAuth('hesabdar') || $se->detailAuth('all_hesabdar')) {?>
			<input type="button" value="ثبت نهایی" class="inp btn btn-info col-md-2" onclick="send_frm();" <?php echo $sabt_style; ?>  >
			<?php } ?>
                           
               
		<?php echo $msg;echo $insert; ?>
		<script language="javascript" >
			if(document.getElementById('new_user_id'))
				document.getElementById('new_user_id').style.display= 'none';
			var ids = document.getElementsByName("new_id");
			for(var i=0;i<ids.length;i++)
				ids[i].style.display="none";
			if(document.getElementById('grid1_filter_12'))
				document.getElementById('grid1_filter_12').style.display = 'none';
		</script>
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
    <!-- Modal : anbar modal -->
    <div class="modal fade" id="anbar-modal">
	
    </div>
			<!--/Modal : anbar modal-->
    <div class="modal fade" id="newG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن سند</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        
                        <div class="col-md-4">
                            <label>شماره سند: </label>
                            <input type="text" class="form-control" value="" name="sname" disabled />
                        </div>
                        <div class="col-md-4">
                            <label>کل: </label>
                            <input type="hidden" class="form-control" value="<?php echo $new[1][code]; ?>" name="kolcode" disabled />
                            <input type="text" class="form-control" value="<?php echo $new[1][name]; ?>" name="kolname" disabled />
                            
                        </div>
                        <div class="col-md-4">
                            <label>معین: </label>
                            <input type="hidden" class="form-control" value="<?php echo $new[2][code]; ?>" name="moeencode" disabled />
                           <input type="text" class="form-control" value="<?php echo $new[2][name]; ?>" name="moeenname" disabled />
                        </div>
                        <div class="col-md-4">
                            <label>بستانکار / بدهکار: </label>
                            <select class="form-control" id="bes1">
                                <option value="1">بستانکار</option>  
                                <option value="-1">بدهکار</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>مبلغ: </label>
                            <input type="text" class="form-control" value="" name="cost1" />
                        </div>
                        <div class="col-md-4">
                            <label>توضیحات: </label>
                            <input type="text" class="form-control" value="" name="toz1" />
                        </div>
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalG()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="editG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش سند</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id2" />
                        
                        <div class="col-md-4">
                            <label>بستانکار / بدهکار: </label>
                            <select class="form-control" id="bes2">
                                <option value="1">بستانکار</option>  
                                <option value="-1">بدهکار</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>مبلغ: </label>
                            <input type="text" class="form-control" value="" name="cost2" />
                        </div>
                        <div class="col-md-4">
                            <label>توضیحات: </label>
                            <input type="text" class="form-control" value="" name="toz2" />
                        </div>
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalG()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف سند</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id3" />
                        آیا از حذف مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalG()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
                </div>
            
        </div>
    </div>
</div>
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
    <script src="<?php echo $root ?>inc/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>inc/bootstrap-datepicker.fa.js"></script>
	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    
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
        function insertFinalG(){
            StartLoading();
            var sname = $("input[name='sname']").val();
            var kolcode = $("input[name='kolcode']").val();
            var kolname = $("input[name='kolname']").val();
            var moeencode = $("input[name='moeencode']").val();
            var moeenname = $("input[name='moeenname']").val();
            var cost1 = $("input[name='cost1']").val();
            var toz1 = $("input[name='toz1']").val();
            var bes1 = $("#bes1 option:selected" ).val();
            $.post("sanad_newAjax.php",{sname:sname,kolcode:kolcode,kolname:kolname,moeencode:moeencode,moeenname:moeenname,cost1:cost1,toz1:toz1,bes1:bes1},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
             
                                        
                                    
                                });
        }
        function editGfunc(id,type,mablagh,tozihat){
            StartLoading();
            $("input[name='id2']").val(id);
            $("#bes2 option[value="+type+"]").attr('selected','selected');
            $("input[name='cost2']").val(mablagh);
            $("input[name='toz2']").val(tozihat);
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var cost2 = $("input[name='cost2']").val();
            var toz2 = $("input[name='toz2']").val();
            var bes2 = $("#bes2 option:selected" ).val();
            
           $.post("sanad_newEditAjax.php",{id2:id2,cost2:cost2,toz2:toz2,bes2:bes2},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
            
        }
        function deleteGfunc(id){
            StartLoading();
            $("input[name='id3']").val(id);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var id3 = $("input[name='id3']").val();
           $.post("sanad_newDeleteAjax.php",{id3:id3},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در حذف");
               if(data=="1"){
                   alert("حذف با موفقیت انجام شد");
                   location.reload();
               }
                                          
           });
            
        }
        function newGG(){
            StartLoading();
            
            var snumber = $("input[name='form_shomare_sanad']").val();
            var kkkolcode = $("input[name='kkkolcode']").val();
            var mmmoeencode = $("input[name='mmmoeencode']").val();
            if(snumber==-1){
                alert("شماره سند به درستی انتخاب نشده است");
                StopLoading();
            }
                
            else{
                if(kkkolcode=="" || mmmoeencode==""){
                    alert("حساب معین یا کل وارد نشده نشده است");
                    StopLoading();
                    }
                else{
            $("input[name='sname']").val(snumber);
            StopLoading();
            $('#newG').modal('show'); } 
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