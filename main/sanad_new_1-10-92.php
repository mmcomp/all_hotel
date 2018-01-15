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
		$pageSelector = 0;
		$onclick = 'alert(\'عملیات مجاز نیست\')';
		$se = security_class::auth((int)$_SESSION['user_id']);
		$pageCount = $GLOBALS['grid']->pageCount;
		if(isset($_REQUEST['pageSelector']))
			$pageSelector = $_REQUEST['pageSelector'];
		if(isset($_REQUEST['pageCount_'.$GLOBALS['grid']->gridName]))
			$pageCount = $_REQUEST['pageCount_'.$GLOBALS['grid']->gridName];

		if($se->detailAuth('all') || ($se->detailAuth('hesabdar') && (sanadzan_class::checkSanad($GLOBALS["shomare_sanad"]) || $conf->limit_sanad_time==-1 ) ) )
			$onclick = "window.location ='select_hesab.php?sel_id=$inp&form_shomare_sanad=".$GLOBALS["shomare_sanad"]."&gridName=".$GLOBALS['grid']->gridName."&pageSelector=$pageSelector&pageCount=$pageCount';";
		return  "<input type='button' value='انتخاب حساب' class='inp' onclick=\"$onclick\"  >";
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
           				mysql_class::ex_sqlx($query);
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
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('upload_pic.php?sanad_record_id=$id&','',600,400);\">مشاهده</span></u>";
		return $out;
	}
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
			foreach($hesab as $meghdar=>$value)
                        {
				$insert .="document.getElementById('new_$meghdar').value ='".idToCode((int)$_REQUEST["$meghdar"],substr($meghdar,0,-3))." (".idToCodeName((int)$_REQUEST["$meghdar"],substr($meghdar,0,-3)).")';\n";
				$insert .="document.getElementById('new_$meghdar').style.width = \"auto\";\n";
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
			if($se->detailAuth('all') || ($se->detailAuth('hesabdar') && (sanadzan_class::checkSanad($shomare_sanad) || $conf->limit_sanad_time==-1 ) ) )
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
	else if($se->detailAuth('reserve'))
	{
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
		$grid->whereClause = " `id` in ($arr) order by `id`";
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
		$grid->columnFunctions[10] = 'loadUser';
	}
	else if($se->detailAuth('hesabdar'))
	{
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
		$grid->whereClause = " `en`=0 and `shomare_sanad` = '$shomare_sanad' and `id` not in ($arr) order by `id`";
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
		if($conf->limit_sanad_time>0) // ---- وجود شرط در کانف سیستم
		{
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
		}
	}
	else
		$grid->columnFunctions[10] = 'loadUser';
/***************Functions***************************/
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
	
	$out = $grid->getGrid();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <!-- Style Includes -->

                <link type="text/css" href="../css/style.css" rel="stylesheet" />

                <!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/tavanir.js"></script>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>
                ثبت سند
                </title>
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
	<script>
		function st()
		{
		week= new Array("يكشنبه","دوشنبه","سه شنبه","چهارشنبه","پنج شنبه","جمعه","شنبه")
		months = new Array("فروردين","ارديبهشت","خرداد","تير","مرداد","شهريور","مهر","آبان","آذر","دي","بهمن","اسفند");
		a = new Date();
		d= a.getDay();
		day= a.getDate();
		var h=a.getHours();
      		var m=a.getMinutes();
  		var s=a.getSeconds();
		month = a.getMonth()+1;
		year= a.getYear();
		year = (year== 0)?2000:year;
		(year<1000)? (year += 1900):true;
		year -= ( (month < 3) || ((month == 3) && (day < 21)) )? 622:621;
		switch (month) 
		{
			case 1: (day<21)? (month=10, day+=10):(month=11, day-=20); break;
			case 2: (day<20)? (month=11, day+=11):(month=12, day-=19); break;
			case 3: (day<21)? (month=12, day+=9):(month=1, day-=20); break;
			case 4: (day<21)? (month=1, day+=11):(month=2, day-=20); break;
			case 5:
			case 6: (day<22)? (month-=3, day+=10):(month-=2, day-=21); break;
			case 7:
			case 8:
			case 9: (day<23)? (month-=3, day+=9):(month-=2, day-=22); break;
			case 10:(day<23)? (month=7, day+=8):(month=8, day-=22); break;
			case 11:
			case 12:(day<22)? (month-=3, day+=9):(month-=2, day-=21); break;
			default: break;
		}
		//document.write(" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s);
			var total=" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s;
			    document.getElementById("tim").innerHTML=total;
   			    setTimeout('st()',500);
		}
		</script>
        </head>
        <body onload='st()'>
		<center>
		<span id='tim' >test2
		</span>
		</center>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
                <div align="center">
                        <br/>
			<span style="color:red;font-size:16px;"  ><?php echo $GLOBALS["err_new"];  ?></span>
			<form id="frm1" method="POST"  > 
				شماره سند : <input class="inp"  id="form_shomare_sanad" name="form_shomare_sanad" value="<?php echo $shomare_sanad; ?>" />
				تاریخ : <input class="inp" readonly="readonly"  id="form_tarikh_sanad" name="form_tarikh_sanad" value="<?php echo $tarikh_sanad; ?>"/>
				<input type="button" value="جدید" class="inp"  onclick="document.getElementById('mod').value = 'add';document.getElementById('frm1').submit();" />
				<input type="button" class="inp"  value="ویرایش" onclick="send_edit();" />
				<br/>
				جستجو در توضیحات:
				<input class="inp" type="text" value="<?php echo ((isset($_REQUEST['sfrase']))?$_REQUEST['sfrase']:''); ?>" name="sfrase" id="sfrase" >
				<input type="button" class="inp"  value="جستجو" onclick="send_edit();" />				
				<input type="hidden" id="mod" name="mod" value="add" />
			</form>
                        <br/> 
                        <?php echo $out;  ?>
			<?php if($se->detailAuth('all') || $se->detailAuth('hesabdar')) {?>
			<input type="button" value="ثبت نهایی" class="inp" onclick="send_frm();" <?php echo $sabt_style; ?>  >
			<?php } ?>
                </div>
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
        </body>
</html>

