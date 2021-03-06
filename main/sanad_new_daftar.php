<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
	$user_id = (int)$_SESSION['user_id'];
	$user = new user_class($user_id);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = ($se->detailAuth('all') || $se->detailAuth('reserve'));
	$kol_id2 = new user_class((int)$_SESSION['user_id']);
// var_dump($kol_id2);
	$GLOBALS['daftar_id'] = $kol_id2->daftar_id;
	$kol_id1 = $kol_id2->daftar_id;
	$kol_id1 = new daftar_class($GLOBALS['daftar_id']);
	$GLOBALS['kol_id'] = $kol_id1->kol_id;
	if( $kol_id1->sandogh_moeen_id<=0 )
		die('<div align="center" >جهت دفتر شما حساب صندوق تعریف نشده است</div>');
	$msg = "";
	$GLOBALS['msg']= '';
	$GLOBALS["err_new"] = "";
	
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function loadReserve($inp)
	{
		$inp = (int)$inp;
		$out = "";
		
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
		$pageCount = $GLOBALS['grid']->pageCount;
		if(isset($_REQUEST['pageSelector']))
			$pageSelector = $_REQUEST['pageSelector'];
		if(isset($_REQUEST['pageCount_'.$GLOBALS['grid']->gridName]))
			$pageCount = $_REQUEST['pageCount_'.$GLOBALS['grid']->gridName];
		return  "<input type='button' value='انتخاب حساب' class='inp' onclick=\"window.location ='select_hesab.php?sel_id=$inp&form_shomare_sanad=".$GLOBALS["shomare_sanad"]."&gridName=".$GLOBALS['grid']->gridName."&pageSelector=$pageSelector&pageCount=$pageCount';\"  >";
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
		mysql_class::ex_sqlx("delete from `sanad` where `id`=$id and `id` not in (select `sanad_record` from `sanad_reserve` where `sanad_record`=$id) ");
		$nums = (int)mysql_affected_rows();
		if($nums==0)
			$GLOBALS["err_new"]= 'رکورد سند مورد نظر اتوماتیک تولید شده است لذا قابل حذف نمی‌باشد';
	}
	function add_item()
        {
                $fields = null;
		$user_id = (int)$_SESSION['user_id'];
		$user = new user_class($user_id);
                foreach($_REQUEST as $key => $value)
		{
                        if(substr($key,0,4)=="new_")
			{
                                if($key != "new_en" )
				{
                                        $fields[substr($key,4)] =perToEnNums($value);
                                }
                        }
		}
		$shomare_sanad = $GLOBALS["shomare_sanad"];
		if($shomare_sanad==-1)
		{
			$GLOBALS["err_new"] = "سند جدید انتخاب نشده است";
		}
		else
		{
			$fields["shomare_sanad"] = $shomare_sanad;
			$query = '';
			$now = date("Y-m-d H:i:s");
			$fields["tarikh"]=$now;
			$kol_id_sabti = new moeen_class($fields["moeen_id"]);
			$fields['kol_id']= $kol_id_sabti->kol_id;
			if($fields["typ"]!='')
			{
				if($fields["tozihat"]!='')
				{
					$fields["tozihat"] .= 'رزرو '.$_REQUEST['res_id'].' '.' ثبت شده توسط '.$user->fname.' '.$user->lname;
					$fields["user_id"] = $user_id;
					$fields["mablagh"] = umonize($fields["mablagh"]);
					var_dump($fields);
					if($fields["mablagh"] != 0 )
					{
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
					/***********ctreate invers query ********************/
						$daftar = new daftar_class($GLOBALS['daftar_id']);
						$fields_inv = $fields;
						$fields_inv['moeen_id'] = $daftar->sandogh_moeen_id;
						$fields_inv['kol_id']= new moeen_class($fields_inv['moeen_id']);
						$fields_inv['kol_id'] = $fields_inv['kol_id']->kol_id;
						$fields_inv['typ'] = -1 * $fields_inv['typ'] ;
						$fi = "(";
						$valu="(";
						foreach ($fields_inv as $field => $value)
						{
							$fi.="`$field`,";
							$valu .="'$value',";
						}
				       		$fi=substr($fi,0,-1);
						$valu=substr($valu,0,-1);
			       			$fi.=")";
						$valu.=")";
						$invers_query = "insert into `sanad` $fi values $valu";
					/****************************************************/
						if($fields["kol_id"] >0 && $fields["moeen_id"]>0 )
						{
							//echo $query.'<br/>';
							//echo $invers_query;
			   				mysql_class::ex_sqlx($query);
							mysql_class::ex_sqlx($invers_query);
							mysql_class::ex_sqlx("update `sanad` set `en` = '0' where `shomare_sanad` = '$shomare_sanad'");
							$q = null;
							mysql_class::ex_sql("select `id` from `sanad` where `kol_id`='".$fields["kol_id"]."' and `moeen_id`='".$fields["moeen_id"]."' and `shomare_sanad`='".$fields["shomare_sanad"]."' and `tarikh`='".$fields["tarikh"]."' and `mablagh`='".$fields["mablagh"]."' and `typ`='".$fields["typ"]."'",$q);
							if($r = mysql_fetch_array($q))
							{	
								$q = null;
								mysql_class::ex_sql("select `id` from `sanad` where `kol_id`='".$fields_inv["kol_id"]."' and `moeen_id`='".$fields_inv["moeen_id"]."' and `shomare_sanad`='".$fields_inv["shomare_sanad"]."' and `tarikh`='".$fields_inv["tarikh"]."' and `mablagh`='".$fields_inv["mablagh"]."' and `typ`='".$fields_inv["typ"]."'",$q);
								if($t = mysql_fetch_array($q))
									mysql_class::ex_sqlx("insert into `sanad_daftar` (`sanad_record_id`,`daftar_id`,`user_id`,`regdat`,`sanad_record_id2`) values ('".(int)$r['id']."','".$GLOBALS['daftar_id']."','$user_id','$now','".(int)$t['id']."')");
							}
						}
						else
							$GLOBALS["err_new"] ='<span style="color:red;" >کد حساب معین وارد نشده است</span>';
					}
					else
						$GLOBALS["err_new"] = '<span style="color:red;" >  مبلغ صفر وارد شده است</span>';
				}
				else
					$GLOBALS["err_new"] = '<span style="color:red;" >توضیحات وارد نشده است</span>';
			}
			else
				$GLOBALS["err_new"] = '<span style="color:red;" > نوع دریافتی یا پرداختی مشخص نشده است</span>';
			
		}
	}
	function loadMoeen()
        {
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = ($se->detailAuth('all') || $se->detailAuth('reserve'));
                $out=null;
		//$shart = (($isAdmin)?'':"where `kol_id`=".$GLOBALS['kol_id']);
		$shart = (($isAdmin)?'':'');
                mysql_class::ex_sql("select `name`,`id` from `moeen` $shart order by `name`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out[$r["name"]]=(int)$r["id"];
                return $out;
        }
	function loadMoeenSelect()
        {
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = ($se->detailAuth('all') || $se->detailAuth('reserve'));
                $out=null;
		$shart = (($isAdmin)?'':'');
		if($_SESSION['daftar_id']!=49){
			$kols = array();
			mysql_class::ex_sql("select `kol_id` from `daftar` where `id` = ".$_SESSION['daftar_id'],$ss);
			while($r=mysql_fetch_array($ss)){
				$kols[] = $r['kol_id'];
			}
			$shart = 'where 1=0';
			if(count($kols)>0){
				$shart = 'where `kol_id` in ('.implode(',',$kols).')';
			}
		}
                mysql_class::ex_sql("select `name`,`id` from `moeen` $shart order by `name`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
			$out .='<option value="'.(int)$r["id"].'">'.$r["name"].'</option>';
                return $out;
        }
	function getShomare_sanad()
	{
		$now = date("Y-m-d");
		mysql_class::ex_sql("select max(`shomare_sanad`) as `shomare_sanad` from `sanad` where `tarikh`<='$now 23:29:29' and `tarikh`>='$now 00:00:00' ",$q);
//echo "select max(`shomare_sanad`) as `shomare_sanad` from `sanad` where `tarikh`<='$now 23:29:29' and `tarikh`>='$now 00:00:00' ";
		if($r = mysql_fetch_array($q))
			if((int)$r['shomare_sanad']!=0)
				$shomare_sanad = (int)$r['shomare_sanad'];
			else
				$shomare_sanad = loadShomareSand();
		return $shomare_sanad;
	}
	function loadPrint($inp)
	{
		$out = "<span style='color:green;cursor:pointer;' onclick=\"wopen('sanad_print.php?id=$inp&','',850,500);\" ><u>چاپ رسید</u></span>";
		return $out;
	}
	function loadZamaiem($inp)
	{
		$daftar_id = (int)$_SESSION['daftar_id'];
		$upl = new sanad_class($inp);
		$tarikh = explode(' ',$upl->tarikh);
		$aztarikh = $tarikh[0].' 00:00:00';
		$tatarikh = $tarikh[0].' 23:59:59';
		$color = (upload_class::isUpload($inp))?'green':'red';
		$out = "<span style='color:$color;cursor:pointer;' onclick=\"wopen('upload_pic.php?daftar_id=$daftar_id&aztarikh=$aztarikh&tatarikh=$tatarikh&sanad_record_id=$inp&show=1&','',850,500);\" ><u>ضمایم</u></span>";
		return $out;
	}
	function loadDafater($id_daftar)
	{
		$out = '';
		$se = security_class::auth((int)$_SESSION['user_id']);
		if($se->detailAuth('all') || $se->detailAuth('reserve'))
		{
			$sel = (($id_daftar==-2)?'selected="selected"':'');
			$out = "<option $sel value='-2'>همه</option>";
		}
		$wer = '';
		if($_SESSION['daftar_id']!=49){
			$wer = 'where `id` = '.$_SESSION['daftar_id'];
		}
		mysql_class::ex_sql("select `id`,`name` from `daftar` $wer order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$id_daftar)?'selected="selected"':'');
			$out .="<option $sel value='".$r['id']."' >".$r['name']."</option>\n";
		}
		return $out;
	}
	function getBedBes($inp)
	{
		$out = 'نا معلوم';
		if($inp>0)
			$out = monize($inp) .' بدهکار ';
		else if ($inp<0)
			$out =monize(-1 * $inp) .' بستانکار ';
		else
			$out = '۰ تراز';
		return $out;
	}
	$GLOBALS["shomare_sanad"] =  getShomare_sanad();
	$shomare_sanad = $GLOBALS["shomare_sanad"];
//echo '<br/>'.$shomare_sanad;
	$tarikh_sanad = audit_class::hamed_pdateBack(date("Y-m-d"));
	$shart = ' and `id`=-2 ';
	$msg = '';
	if(isset($_REQUEST['mod']) && $_REQUEST['mod'] == "edit")
	{
		$q = null;
		$now = date("Y-m-d H:i:s");
		if(isset($_REQUEST["form_tarikh_sanad"]))
			$tarikh_sanad = audit_class::hamed_pdateBack($_REQUEST["form_tarikh_sanad"]);
		
	}
//--------------------------new_sabt-------------------------------
	if(isset($_REQUEST['sabt_moeen_id']) && isset($_REQUEST['sabt_mablagh']) && (int)$_REQUEST['sabt_mablagh']>0 )
	{
		if($_FILES['sabt_file']['name']!='')
		{
			$tmp_target_path = "../upload";
			$ext = explode('.',basename($_FILES['sabt_file']['name']));
			$ext = $ext[count($ext)-1];
			if(strtolower($ext)=='jpg' || strtolower($ext)=='png' || strtolower($ext)=='gif' || strtolower($ext)=='tif' || strtolower($ext)=='jpeg' || strtolower($ext)=='pdf' || strtolower($ext)=='bmp' )
			{
				$target_path =$tmp_target_path.'/'.$conf->getMoshtari().'_'.$user->daftar_id.'_'.$user_id.'_'.date("Y-m-d-H-i-s").'.'.$ext; 
				if(move_uploaded_file($_FILES['sabt_file']['tmp_name'], $target_path))
				{
					$shomare_sanad = $GLOBALS["shomare_sanad"];
					if($shomare_sanad==-1)
					{
						$GLOBALS["err_new"] = "سند جدید انتخاب نشده است";
						delete($target_path);
					}
					else
					{
						$fields = array();
						$fields["shomare_sanad"] = $shomare_sanad;
						$query = '';
						$now = date("Y-m-d H:i:s");
						$fields["tarikh"]=$now;
						$fields['moeen_id'] = $_REQUEST['sabt_moeen_id'];
						$kol_id_sabti = new moeen_class($_REQUEST['sabt_moeen_id']);
						$fields['kol_id']= $kol_id_sabti->kol_id;
						$fields["tozihat"] = 'رزرو '.$_REQUEST['res_id'].' '.$_REQUEST['sabt_toz'].' ثبت شده توسط '.$user->fname.' '.$user->lname;
						$fields["user_id"] = $user_id;
						$fields["mablagh"] = umonize($_REQUEST['sabt_mablagh']);
						$fields["typ"] = (int)$_REQUEST['sabt_typ'];
						if($fields["mablagh"] != 0 )
						{
							unset($fields["id"]);
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
						/***********ctreate invers query ********************/
							$daftar = new daftar_class($GLOBALS['daftar_id']);
							$fields_inv = $fields;
							$fields_inv['moeen_id'] = $daftar->sandogh_moeen_id;
							$fields_inv['kol_id']= new moeen_class($fields_inv['moeen_id']);
							$fields_inv['kol_id'] = $fields_inv['kol_id']->kol_id;
							$fields_inv['typ'] = -1 * $fields_inv['typ'] ;
							$fi = "(";
							$valu="(";
							foreach ($fields_inv as $field => $value)
							{
								$fi.="`$field`,";
								$valu .="'$value',";
							}
							$fi=substr($fi,0,-1);
							$valu=substr($valu,0,-1);
							$fi.=")";
							$valu.=")";
							$invers_query = "insert into `sanad` $fi values $valu";
						/****************************************************/
							if($fields["kol_id"] >0 && $fields["moeen_id"]>0 )
							{
					
								$dbl = mysql_class::ex_sqlx($query,FALSE);
								$sanad_record_id_1 = mysql_insert_id($dbl);
								mysql_close($dbl);
								$dbl = mysql_class::ex_sqlx($invers_query,FALSE);
								$sanad_record_id_2 = mysql_insert_id($dbl);
								mysql_close($dbl);
								mysql_class::ex_sqlx("update `sanad` set `en` = '0' where `shomare_sanad` = '$shomare_sanad'");
								mysql_class::ex_sqlx("insert into `sanad_daftar` (`sanad_record_id`,`daftar_id`,`user_id`,`regdat`,`sanad_record_id2`) values ('$sanad_record_id_1','".$GLOBALS['daftar_id']."','$user_id','$now','$sanad_record_id_2')");

								mysql_class::ex_sqlx("insert into `upload` (`daftar_id`,`user_id`,`toz`,`pic_addr`,`tarikh`,`sanad_record_id`) values ('".$GLOBALS['daftar_id']."','$user_id','".$fields["tozihat"]."','$target_path','$now',$sanad_record_id_1) ");//طرف اول
								mysql_class::ex_sqlx("insert into `upload` (`daftar_id`,`user_id`,`toz`,`pic_addr`,`tarikh`,`sanad_record_id`) values ('".$GLOBALS['daftar_id']."','$user_id','".$fields["tozihat"]."','$target_path','$now',$sanad_record_id_2) ");//طرف دوم
								$GLOBALS['msg'] = "<script> alert('ثبت با موفقیت انجام شد'); </script>";
								$daf = new daftar_class($GLOBALS['daftar_id']);
								log_class::add("sanad_new_daftar",$user_id,"ثبت دریافتی پرداختی برای سند ".$shomare_sanad." دفتر ".$daf->name);
							}
							else
								$GLOBALS["err_new"] ='<span style="color:red;" >کد حساب معین وارد نشده است</span>';
						}
					}

				}
				else
					$GLOBALS["err_new"] ='<span style="color:red;" >در ارسال تصویر مشکلی پیش آمده است مجدد تلاش فرمایید</span>';
			}
			else
				$GLOBALS["err_new"] = '<span style="color:red;" >فرمت فایل ارسال شده مجاز نمی باشد</span>';
		}
		else
		{
			$shomare_sanad = $GLOBALS["shomare_sanad"];
			if($shomare_sanad==-1)
				$GLOBALS["err_new"] = "سند جدید انتخاب نشده است";
			else
			{
				$fields = array();
				$fields["shomare_sanad"] = $shomare_sanad;
				$query = '';
				$now = date("Y-m-d H:i:s");
				$fields["tarikh"]=$now;
				$fields['moeen_id'] = $_REQUEST['sabt_moeen_id'];
				$kol_id_sabti = new moeen_class($_REQUEST['sabt_moeen_id']);
				$fields['kol_id']= $kol_id_sabti->kol_id;
				$fields["tozihat"] = 'رزرو '.$_REQUEST['res_id'].' '.$_REQUEST['sabt_toz'].' ثبت شده توسط '.$user->fname.' '.$user->lname;
				$fields["user_id"] = $user_id;
				$fields["mablagh"] = umonize($_REQUEST['sabt_mablagh']);
				$fields["typ"] = (int)$_REQUEST['sabt_typ'];
				if($fields["mablagh"] != 0 )
				{
					unset($fields["id"]);
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
				/***********ctreate invers query ********************/
					$daftar = new daftar_class($GLOBALS['daftar_id']);
					$fields_inv = $fields;
					$fields_inv['moeen_id'] = $daftar->sandogh_moeen_id;
					$fields_inv['kol_id']= new moeen_class($fields_inv['moeen_id']);
					$fields_inv['kol_id'] = $fields_inv['kol_id']->kol_id;
					$fields_inv['typ'] = -1 * $fields_inv['typ'] ;
					$fi = "(";
					$valu="(";
					foreach ($fields_inv as $field => $value)
					{
						$fi.="`$field`,";
						$valu .="'$value',";
					}
					$fi=substr($fi,0,-1);
					$valu=substr($valu,0,-1);
					$fi.=")";
					$valu.=")";
					$invers_query = "insert into `sanad` $fi values $valu";
				/****************************************************/
					if($fields["kol_id"] >0 && $fields["moeen_id"]>0 )
					{
			
						$dbl = mysql_class::ex_sqlx($query,FALSE);
						$sanad_record_id_1 = mysql_insert_id($dbl);
						mysql_close($dbl);
						$dbl = mysql_class::ex_sqlx($invers_query,FALSE);
						$sanad_record_id_2 = mysql_insert_id($dbl);
						mysql_close($dbl);
						mysql_class::ex_sqlx("update `sanad` set `en` = '0' where `shomare_sanad` = '$shomare_sanad'");
						mysql_class::ex_sqlx("insert into `sanad_daftar` (`sanad_record_id`,`daftar_id`,`user_id`,`regdat`,`sanad_record_id2`) values ('$sanad_record_id_1','".$GLOBALS['daftar_id']."','$user_id','$now','$sanad_record_id_2')");

						mysql_class::ex_sqlx("insert into `upload` (`daftar_id`,`user_id`,`toz`,`pic_addr`,`tarikh`,`sanad_record_id`) values ('".$GLOBALS['daftar_id']."','$user_id','".$fields["tozihat"]."','$target_path','$now',$sanad_record_id_1) ");//طرف اول
						mysql_class::ex_sqlx("insert into `upload` (`daftar_id`,`user_id`,`toz`,`pic_addr`,`tarikh`,`sanad_record_id`) values ('".$GLOBALS['daftar_id']."','$user_id','".$fields["tozihat"]."','$target_path','$now',$sanad_record_id_2) ");//طرف دوم
						$GLOBALS['msg'] = "<script> alert('ثبت با موفقیت انجام شد'); </script>";
					}
					else
						$GLOBALS["err_new"] ='<span style="color:red;" >کد حساب معین وارد نشده است</span>';
				}
			}
		}
	}
//----------------------------end new_sabt-------------------------
	$status=array();
	$status["موقت"]=0;
	$hesab_type = array();
        $hesab_type["دریافتی"]=1;
	$hesab_type["پرداختی"]=-1;
	$grid = new jshowGrid_new("sanad","grid1");
	$rec["form_tarikh_sanad"] = hamed_pdate($tarikh_sanad);
	$rec["mod"] = 'edit';
        $grid->setERequest($rec);
	$grid->divProperty = "overflow:'';";
/***************WhereClause*************************/

/***************Add*Feild***************************/
	//$grid->addFeild("id");
/***************Headers*****************************/
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;//shomare_sanad
	$grid->columnHeaders[2] = $conf->hesab("group_id");
	$grid->columnHeaders[3] = ($isAdmin)?$conf->hesab("kol_id"):null;
	$grid->columnHeaders[4] = $conf->hesab("moeen_id");
	$grid->columnHeaders[5] = $conf->hesab("tafzili_id");
	$grid->columnHeaders[6] = $conf->hesab("tafzili2_id");
	$grid->columnHeaders[7] = $conf->hesab("tafzilishenavar_id");
	$grid->columnHeaders[8] = $conf->hesab("tafzilishenavar2_id");
	$grid->columnHeaders[9] = null;
	$grid->columnHeaders[10] = null;//user_id
	$grid->columnHeaders[11] = "پرداختی/دریافتی";
	$grid->columnHeaders[12] = "توضیحات";
	//$grid->columnFilters[12] =((isset($_REQUEST['sfrase']) && $_REQUEST['sfrase']!='')?$_REQUEST['sfrase']:TRUE);
	$grid->columnHeaders[13] = null;
	$grid->columnHeaders[14] = "مبلغ(ریال)";
	$grid->addFeild('id');
	$grid->columnHeaders[15] = "چاپ رسید";
	$grid->addFeild('id');
	$grid->columnHeaders[16] = "ضمایم";
	//$grid->columnHeaders[15] = "توضیحات";
/***************Functions***************************/
	$grid->columnFunctions[0] = "loadHesab";
	$grid->columnFunctions[2] = "idToCodeGroup";
        $grid->columnFunctions[3] = "idToCodeKol";
        //$grid->columnFunctions[4] = "idToCodeMoeen";
	$grid->columnLists[4] = loadMoeen();
        $grid->columnFunctions[5] = "idToCodeTafzili";
        $grid->columnFunctions[6] = "idToCodeTafzili2";
        $grid->columnFunctions[7] = "idToCodeTafzilishenavar";
        $grid->columnFunctions[8] = "idToCodeTafzilishenavar2";
	//$grid->columnFunctions[9]="hamed_pdate";
        $grid->columnCallBackFunctions[9]="hamed_pdateBack";
	$grid->columnFunctions[14]="loadMablagh";
	$grid->columnCallBackFunctions[14] = "umonize";
	$grid->columnJavaScript[14] = 'onkeyup="monize(this);"';	
	$grid->columnFunctions[15]="loadPrint";
	$grid->columnFunctions[16]="loadZamaiem";
	//$grid->columnFunctions[15] = "loadReserve";
	//$grid->columnJavaScripts[15] = "onkeyup = \"findOther(this);\"";
	$grid->addFunction = "add_item";
//---------------------------javaScript-----------------
	$grid->columnJavaScript[14] = 'onkeyup="monize(this);"';
//	$grid->editFunction = "edit_item";
	$grid->deleteFunction = "delete_item";
/***************************************************/
/**********************LISTS************************/
	$grid->columnLists[13]=$status;
	$grid->columnLists[11]=$hesab_type;
	$grid->columnAccesses[0] = 0;
	$grid->columnAccesses[2] = 0;
	$grid->columnAccesses[3] = 0;
	$grid->columnAccesses[4] = 0;
	$grid->columnAccesses[5] = 0;
	$grid->columnAccesses[6] = 0;
	$grid->columnAccesses[7] = 0;
	$grid->columnAccesses[8] = 0;
	$grid->columnAccesses[13] = 0;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->canAdd = FALSE;
	$grid->pageCount=15;
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
	$daft = -2;
	if($isAdmin && $_SESSION['daftar_id']!=49)
	{
		$daft = (isset($_REQUEST['sel_dafatar_id']))?(int)$_REQUEST['sel_dafatar_id']:-2;
	}
	$daftar_search = (($isAdmin)?$daft:$GLOBALS['daftar_id']);
	//$daftar_search = (($isAdmin)?$daft:-2);
	$sanad_records = new sanad_daftar_class($daftar_search,$tarikh_sanad);
	//$canAccess = $sanad_records->canAccess;
	$canAccess =TRUE;
	if($canAccess && is_array($sanad_records->sanad_record) && count($sanad_records->sanad_record)>0)
	{
		$sanad_records =(count($sanad_records->sanad_record)>0)?implode(',',$sanad_records->sanad_record):-1;
		$shart = " and `id` in ($sanad_records) ";
	}
	else if(!$canAccess)
	{
		$grid->canAdd = FALSE;
		$msg = '<span style="color:red" >سند روز قبل  کامل وارد نشده است</span>';
	}
	
	$tarikh_sanad =enToPerNums(hamed_pdate($tarikh_sanad));
	$rec = array('sel_dafatar_id'=>$daft,'form_tarikh_sanad'=>$tarikh_sanad,'mod'=>(isset($_REQUEST['mod']))?$_REQUEST['mod']:'');
	$grid->setERequest($rec);
	$grid->whereClause = " 1=1 $shart order by `id`";
	//----------------------jam sandogh----------------------
	mysql_class::ex_sql("select sum(`mablagh`) as `jam` from `sanad` where 1=1 and `typ`=1 $shart",$sq);
	if($rs = mysql_fetch_array($sq))
		$sum_bes = monize($rs['jam']);
	$sq = null;
	mysql_class::ex_sql("select sum(`mablagh`) as `jam` from `sanad` where 1=1 and `typ`=-1 $shart",$sq);
	if($rs = mysql_fetch_array($sq))
		$sum_bed = monize($rs['jam']);
	$sq = null;
	mysql_class::ex_sql("select sum(`mablagh`*`typ`) as `jam` from `sanad` where 1=1 $shart",$sq);
	if($rs = mysql_fetch_array($sq))
		$sum_sandogh = $rs['jam'];
	$grid->footer = "<tr style='background-color:#eee;' ><td align='left' colspan='4'>جمع کل دریافتی: $sum_bes جمع کل پرداختی: $sum_bed</td><td align='left' >مجموع کل:</td><td align='center' ><b>".getBedBes($sum_sandogh)."</b></td><td colspan='2' >&nbsp;</td></tr>";
	//-----------------------End jam sandogh-----------------
	$grid->executeQuery();
	$out = $grid->getGrid();
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
		function send_add()
		{
			document.getElementById('mod').value = 'add';
			document.getElementById('frm1').submit();
		}
		function send_edit()
		{
			document.getElementById('mod').value = 'edit';
			document.getElementById('frm1').submit();
		}
		function getPrint()
		{
			document.getElementById('print_btn').style.display='none';
			document.getElementById('view_btn').style.display='none';
			document.getElementById('div_main').style.width = '18cm';
			window.print();
			document.getElementById('print_btn').style.display='';
			document.getElementById('view_btn').style.display='';
			document.getElementById('div_main').style.width = 'auto';
		}
		function send_sabt()
		{
			if(document.getElementById('sabt_typ').value=='' || document.getElementById('sabt_toz').value=='' || document.getElementById('sabt_mablagh').value=='' ||  document.getElementById('sabt_moeen_id').value=='')
				alert('جهت ثبت تمامی موارد را وارد کنید');
			else
				document.getElementById('sabt_frm').submit();
		}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#form_tarikh_sanad").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
	    	</script>
		<style type="text/css" >
			#new_moeen_id {inherit: color_red;font-family:Tahoma,tahoma;font-size:12px;width:auto;background-color:#fdfacd;}
			#new_typ {inherit: color_red;font-family:Tahoma,tahoma;font-size:12px;width:auto;background-color:#fdfacd;}
		</style>
	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
        </head>
        <body>
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
													<span style="color:red;font-size:16px;"  ><?php echo $msg; ?></span>
			
			<form id="frm1" method="GET"  >
			
			<?php 
			if($isAdmin)
			{
				echo 'دفتر:<select class="inp" name="sel_dafatar_id">'.loadDafater($daft).'</select>';
			}
			?>
			 تاریخ : <input class="inp" readonly="readonly" id="form_tarikh_sanad" name="form_tarikh_sanad" value="<?php echo $tarikh_sanad; ?>"/>
				<!-- <input type="button" value="جدید" class="inp"  onclick="send_add();" /> -->
				<input type="button" id="view_btn" id="view_btn" class="inp"  value="مشاهده" onclick="send_edit();" />
				<input type="button"  id="print_btn" name="print_btn" class="inp"  value="چاپ" onclick="getPrint();" />
				<br/>
				<input type="hidden" id="mod" name="mod" value="add" />
			</form>
                        <br/> 
                        <?php echo $out;  ?>
			<form id='sabt_frm' method="POST" enctype="multipart/form-data" >
				<table>
				<tr>
					<td>
				<?php 
					echo 'حساب معین:<select class="inp" id="sabt_moeen_id" name="sabt_moeen_id">'.loadMoeenSelect().'</select>';
				?>
					</td>
					<td>
				نوع:
						<select id="sabt_typ" name="sabt_typ" class="inp" >
							<option></option>
							<option value="1" >دریافتی</option>
							<option value="-1">پرداختی</option>
						</select>
					</td>
					<td>
				شماره رزرو:
						<input type="text" id="res_id" name="res_id" class="inp" />
					</td>
					<td>
				توضیحات:
						<input type="text" id="sabt_toz" name="sabt_toz" class="inp" />
					</td>
					<td>
				مبلغ:
						<input type="text" onkeyup="monize(this);" id="sabt_mablagh" name="sabt_mablagh" class="inp" />
					</td>
				</tr>
				<tr>
					<td colspan='2' >
				
				ضمیمه:
						<input type="file" id="sabt_file" name="sabt_file" class="inp" />
						<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
					</td>
					<td align="left" >
						<input style="width:100px;" type="button" id="sabt_sub" id="sabt_sub" class="inp"  value="ثبت" onclick="send_sabt();" />
					</td>
				</tr>
				</table>
			</form>
                </div>
									</div>
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
	
		<script language="javascript" >
			//if(document.getElementById('grid1_filter_12'))
				//document.getElementById('grid1_filter_12').style.display = 'none';
		</script>
        </body>
</html>

