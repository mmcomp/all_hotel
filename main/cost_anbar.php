<?php
	session_start();
	include("../kernel.php");
         if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
               die(lang_fa_class::access_deny);
	$msg = '';

$khadamat_id = (isset($_POST['khadamat_id']))?$_POST['khadamat_id']:"";
$max_tedad = (isset($_POST['max_tedad']))?$_POST['max_tedad']:"";
$cost_tedad = (isset($_POST['cost_tedad']))?$_POST['cost_tedad']:"";
$kala_cost = (isset($_POST['kala_cost']))?$_POST['kala_cost']:"";
$tarikh = (isset($_POST['tarikh']))?$_POST['tarikh']:"";
$anbar_id = (isset($_POST['anbar_id']))?$_POST['anbar_id']:"";
$gUser_id = (isset($_POST['gUser_id']))?$_POST['gUser_id']:"";

	if(isset($tarikh) && isset($khadamat_id) && isset($cost_tedad) && isset($kala_cost))
	{
		$tarikh =audit_class::hamed_pdateBack($tarikh);
		$tarikh = date("Y-m-d",strtotime($tarikh));
		$tedad = (int)$cost_tedad;
		$max_tedad = (int)$max_tedad;
		$khadamat_id = (int)$khadamat_id;
		$kala_cost = $kala_cost;
		$kala_co = new cost_kala_class($kala_cost);
		$anbar_id = (int)$anbar_id;
		$user_id = (int)$user_id;
		$gUser_id = (int)$gUser_id;
		$anbar = new anbar_class($anbar_id);
		$can_kh = cost_kala_class::cost_anbar_sabt($khadamat_id,$tedad,$max_tedad,$kala_cost,$tarikh,FALSE);
		if($can_kh)
		{
			$khadamat = new khadamat_class($khadamat_id);
			$hotel = new hotel_class($khadamat->hotel_id);
			$ghaza_moeen_id = $hotel->ghaza_moeen_id;
			$hotel_name = $hotel->name;
			$usr = new user_class($user_id);
			$toz = 'خروج کالای ترکیبی جهت  '.$hotel_name.' در '.$tarikh.' توسط '.$usr->fname.' '.$usr->lname;
			$isCost_kala = TRUE;
			$kala_nist = array();
			for($i = 0;$i<count($kala_co->det);$i++)
				if(anbar_det_class::calcGhimat($kala_co->det[$i]['kala_id'],$kala_co->det[$i]['tedad']*$tedad,FALSE)<=0)
				{
					$isCost_kala = FALSE;
					$kala_ni = new kala_class( $kala_co->det[$i]['kala_id']);
					$kala_nist[] =$kala_ni->name;
				}
			if(count($kala_nist)>0)
				for($k = 0;$k<count($kala_nist);$k++)
					$msg .="موجودی کالای ".$kala_nist[$k]." کافی نیست<br/>";
			if($isCost_kala)
				$factor_id = cost_kala_class::cost_factor_khorooj($ghaza_moeen_id,$hotel_name,$tarikh,$toz,$user_id);
			for($i = 0;$isCost_kala && $i<count($kala_co->det);$i++)
			{
				$ghimat = anbar_det_class::calcGhimat($kala_co->det[$i]['kala_id'],$kala_co->det[$i]['tedad']*$tedad);
				if($ghimat>0)
				{
					anbar_det_class::khorooj($anbar_id,2,$factor_id,$kala_co->det[$i]['kala_id'],$kala_co->det[$i]['tedad']*$tedad,$user_id,$gUser_id,$ghimat);
					$sanad_rec = sanadzan_class::anbarSabt($factor_id,$kala_co->det[$i]['kala_id'],-1,$anbar_id,$kala_co->det[$i]['tedad']*$tedad,$ghimat,$ghaza_moeen_id,$anbar->moeen_id,$gUser_id);
					if($sanad_rec[0]>0)
						mysql_class::ex_sqlx("insert into `sanad_anbar` (`sanad_record_id`,`anbar_factor_id`) values ('".$sanad_rec[0]."','$factor_id')");
				}
			}
			if($isCost_kala)
			{
				cost_kala_class::cost_anbar_sabt($khadamat_id,$tedad,$max_tedad,$kala_cost,$tarikh,TRUE);
				mysql_class::ex_sqlx("update `anbar_det` set `en`='1' where `anbar_factor_id` = $factor_id");
				mysql_class::ex_sql("select *,sum(`ghimat`) as `gheimat_kol` from `anbar_det` where `anbar_factor_id` = $factor_id",$q);
				if($r = mysql_fetch_array($q))
				{
					$anbar_factor_id=$r["anbar_factor_id"];
					$gheimat = $r["gheimat_kol"];
					$anbar_typ_id = $r["anbar_typ_id"];		
				}
				$sanad_rec = sanadzan_class::anbarSabtTak($factor_id,$gheimat,$anbar_typ_id,$user_id);
				if($sanad_rec[0]>0)
					mysql_class::ex_sqlx("insert into `sanad_anbar` (`sanad_record_id`,`anbar_factor_id`) values ('".$sanad_rec[0]."','$factor_id')");
				$msg = '1'.'_'.$factor_id.'|'.$kala_cost.'|'.$tedad;
                echo $msg;
			}
		}
			else	{$msg = 'این وعده غذایی قبلا از انبار خارج شده است و یا تعداد درخواستی بیش از کل لیست است';
                    echo $msg;
                    }
	}
?>
