<?php
	class sanadzan_class
	{
		public function newHotelReserveSanad($hotel_id,$ajans_id,$ghimat,$shomare_sanad,$toz,$user_id = -1)
		{
			$user_id = $user_id <= 0 ?(int)$_SESSION["user_id"] : $user_id;
			$hotel = new hotel_class((int)$hotel_id);
			$h_moeen = new moeen_class($hotel->moeen_id);
			$ajans = new ajans_class((int)$ajans_id);
			$a_moeen = new moeen_class($ajans->moeen_id);
                        $mxs = 0;
			mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
			if($r = mysql_fetch_array($q))
				$mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
			$q = null;
			$tarikh = date("Y-m-d");
			mysql_class::ex_sql("SELECT `tarikh` from `sanad` where `shomare_sanad`=$mxs",$q);
                        if($r = mysql_fetch_array($q))
				$tarikh = $r["tarikh"];
			$tarikh = date("Y-m-d",strtotime($tarikh));
			if($shomare_sanad<=0)
			{
				$shomare_sanad = $mxs;
				if(strtotime($tarikh)<strtotime(date("Y-m-d")))
					$shomare_sanad++;
			}
			$tarikh = date("Y-m-d");
			$sanad_record = array();
			if((int)$ghimat>0)
			{
				mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$h_moeen->kol_id.",".$hotel->moeen_id.",'$tarikh',$user_id,1,1,$ghimat,'$toz')");
//echo "insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$h_moeen->kol_id.",".$hotel->moeen_id.",'$tarikh',$user_id,1,1,$ghimat,'$toz')".'<br/>';
				$q = null;
				mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=".$h_moeen->kol_id." and `moeen_id`=".$hotel->moeen_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=1 and `en`=1 and `mablagh`=$ghimat",$q);
				if($r = mysql_fetch_array($q))
					$sanad_record[] = (int)$r['id'];
				mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$a_moeen->kol_id.",".$ajans->moeen_id.",'$tarikh',$user_id,-1,1,$ghimat,'$toz')");
//echo "insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$a_moeen->kol_id.",".$ajans->moeen_id.",'$tarikh',$user_id,-1,1,$ghimat,'$toz')".'<br/>';
                        	$q = null;
                	        mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=".$a_moeen->kol_id." and `moeen_id`=".$ajans->moeen_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=-1 and `en`=1 and `mablagh`=$ghimat",$q);
        	                if($r = mysql_fetch_array($q))
	                                $sanad_record[] = (int)$r['id'];
			}
			return($sanad_record);
		}
		public function newTourReserveSanad($hotel_id,$ajans_id,$other_id,$other_kol_id,$ghimat_tour,$ghimat_blit,$shomare_sanad,$toz = '')
		{
			//echo "تور<br/>";
			$mxs = 0;
			//$shomare_sanad = -1;
                        $user_id = (int)$_SESSION["user_id"];
                        $hotel = new hotel_class((int)$hotel_id);
                        $h_moeen = new moeen_class($hotel->moeen_id);
                        $ajans = new ajans_class((int)$ajans_id);
                        $a_moeen = new moeen_class($ajans->moeen_id);
			$other_id2 = -1;
			$other_kol_id2 = -1;
			$ghimat_blit2 = 0;
			$ghimat_blit3 = 0;
			if(is_array($other_id))
			{
				$other_id3 = isset($other_id[2])?$other_id[2]:-1;
                                $other_kol_id3 = isset($other_kol_id[2])?$other_kol_id[2]:-1;
				$other_id2 = $other_id[1];
				$other_kol_id2 = $other_kol_id[1];
				$other_id = $other_id[0];
				$other_kol_id = $other_kol_id[0];
				$ghimat_blit3 = isset($ghimat_blit[2])?$ghimat_blit[2]:-1;
				$ghimat_blit2 = $ghimat_blit[1];
				$ghimat_blit1 = $ghimat_blit[0];
			}
                        mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
                        if($r = mysql_fetch_array($q))
                                $mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
                        $q = null;
                        $tarikh = date("Y-m-d");
                        mysql_class::ex_sql("SELECT `tarikh` from `sanad` where `shomare_sanad`=$mxs",$q);
                        if($r = mysql_fetch_array($q))
                                $tarikh = $r["tarikh"];
                        $tarikh = date("Y-m-d",strtotime($tarikh));
                        if($shomare_sanad<=0)
                        {
                                $shomare_sanad = $mxs;
                                if(strtotime($tarikh)<strtotime(date("Y-m-d")))
                                        $shomare_sanad++;
                        }
                        $tarikh = date("Y-m-d");
                        $sanad_record = array();
			$ghimat = $ghimat_tour - ($ghimat_blit1+$ghimat_blit2);
//echo '$ghimat'.$ghimat.'<br>';
			if((int)$ghimat > 0)
			{
	                        $ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$h_moeen->kol_id.",".$hotel->moeen_id.",'$tarikh',$user_id,1,1,$ghimat,'$toz')",FALSE);
				$sanadId = (int)mysql_insert_id($ln);
				mysql_close($ln);
		               /* $q = null;
                	        mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=".$h_moeen->kol_id." and `moeen_id`=".$hotel->moeen_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=1 and `en`=1 and `mablagh`=$ghimat order by `id` desc limit 1",$q);
                        	if($r = mysql_fetch_array($q))
                                	$sanad_record[] = (int)$r['id'];*/
				if ($sanadId>0)
					$sanad_record[] = $sanadId;
			}
//echo '$ghimat_blit1:'.$ghimat_blit1.'<br/>';
			if((int)$ghimat_blit1>0)
			{
	                        $ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$other_kol_id.",".$other_id.",'$tarikh',$user_id,1,1,$ghimat_blit1,'$toz')",FALSE);
				$sanadId = (int)mysql_insert_id($ln);
				mysql_close($ln);
        	               /* $q = null;
                	        mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$other_kol_id and `moeen_id`=".$other_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=1 and `en`=1 and `mablagh`=$ghimat_blit1 order by `id` desc limit 1",$q);
                        	if($r = mysql_fetch_array($q))
                                	$sanad_record[] = (int)$r['id'];*/
				if ($sanadId>0)
					$sanad_record[] = $sanadId;
			}
                        if((int)$ghimat_blit2>0)
                        {
                                mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$other_kol_id2.",".$other_id2.",'$tarikh',$user_id,1,1,$ghimat_blit2,'$toz')");
                                $q = null;
                                mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$other_kol_id2 and `moeen_id`=".$other_id2." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=1 and `en`=1 and `mablagh`=$ghimat_blit2 order by `id` desc limit 1",$q);
                                if($r = mysql_fetch_array($q))
                                        $sanad_record[] = (int)$r['id'];
                        }
                        if((int)$ghimat_blit3>0)
                        {
                                mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$other_kol_id3.",".$other_id3.",'$tarikh',$user_id,1,1,$ghimat_blit3,'$toz')");
                                $q = null;
                                mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$other_kol_id3 and `moeen_id`=".$other_id3." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=1 and `en`=1 and `mablagh`=$ghimat_blit3 order by `id` desc limit 1",$q);
                                if($r = mysql_fetch_array($q))
                                        $sanad_record[] = (int)$r['id'];
                        }
			if((int)$ghimat_tour>0)
			{
	                        mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$a_moeen->kol_id.",".$ajans->moeen_id.",'$tarikh',$user_id,-1,1,$ghimat_tour,'$toz')");
        	                $q = null;
                	        mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=".$a_moeen->kol_id." and `moeen_id`=".$ajans->moeen_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=-1 and `en`=1 and `mablagh`=$ghimat_tour order by `id` desc limit 1",$q);
                        	if($r = mysql_fetch_array($q))
                                	$sanad_record[] = (int)$r['id'];
			}
//var_dump($sanad_record);
                        return($sanad_record);
		}
		public function newHotelRefundSanad($hotel_id,$ajans_id,$ghimat,$toz='')
                {
			$shomare_sanad = -1;
                        $mxs = 0;
                        $user_id = (int)$_SESSION["user_id"];
                        $hotel = new hotel_class((int)$hotel_id);
                        $h_moeen = new moeen_class($hotel->moeen_id);
                        $ajans = new ajans_class((int)$ajans_id);
                        $a_moeen = new moeen_class($ajans->moeen_id);
                        mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
                        if($r = mysql_fetch_array($q))
	                        $mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
			$q = null;
                        $tarikh = date("Y-m-d");
                        mysql_class::ex_sql("SELECT `tarikh` from `sanad` where `shomare_sanad`=$mxs",$q);
                        if($r = mysql_fetch_array($q))
                                $tarikh = $r["tarikh"];
                        $tarikh = date("Y-m-d",strtotime($tarikh));
                        if($shomare_sanad<=0)
                        {
                                $shomare_sanad = $mxs;
                                if(strtotime($tarikh)<strtotime(date("Y-m-d")))
                                        $shomare_sanad++;
                        }
                        $tarikh = date("Y-m-d");
                        $sanad_record = array();
			if($ghimat>0 )
			{
	                        mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$h_moeen->kol_id.",".$hotel->moeen_id.",'$tarikh',$user_id,-1,1,$ghimat,'$toz')");
        	                $q = null;
                	        mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=".$h_moeen->kol_id." and `moeen_id`=".$hotel->moeen_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=-1 and `en`=1 and `mablagh`=$ghimat",$q);
                        	if($r = mysql_fetch_array($q))
                                	$sanad_record[] = (int)$r['id'];
	                        mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$a_moeen->kol_id.",".$ajans->moeen_id.",'$tarikh',$user_id,1,1,$ghimat,'$toz')");
        	                $q = null;
                	        mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=".$a_moeen->kol_id." and `moeen_id`=".$ajans->moeen_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=1 and `en`=1 and `mablagh`=$ghimat",$q);
				if($r = mysql_fetch_array($q))
					$sanad_record[] = (int)$r['id'];
			}
                        return($sanad_record);
                }

		public function refundReserveSanad($hotel_moeen,$ajans_moeen,$ajans_belits,$mablaghs)
		{
			
		}
		public function newInverseTourReserveSanad($hotel_id,$ajans_id,$other_id,$other_kol_id,$ghimat_tour,$ghimat_blit,$toz='')
		{
			$shomare_sanad= -1;
			$mxs = 0;
                        $user_id = (int)$_SESSION["user_id"];
                        $hotel = new hotel_class((int)$hotel_id);
                        $h_moeen = new moeen_class($hotel->moeen_id);
                        $ajans = new ajans_class((int)$ajans_id);
                        $a_moeen = new moeen_class($ajans->moeen_id);
                        $other_id2 = -1;
                        $other_kol_id2 = -1;
			$ghimat_blit1 = 0;
                        $ghimat_blit2 = 0;
                        if(is_array($other_id))
                        {
                                $other_id2 = $other_id[1];
                                $other_kol_id2 = $other_kol_id[1];
                                $other_id = $other_id[0];
                                $other_kol_id = $other_kol_id[0];
                                $ghimat_blit1 = $ghimat_blit[0];
				$ghimat_blit2 = $ghimat_blit[1];
                        }
                        mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
                        if($r = mysql_fetch_array($q))
                                $mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
                        $q = null;
                        $tarikh = date("Y-m-d");
                        mysql_class::ex_sql("SELECT `tarikh` from `sanad` where `shomare_sanad`=$mxs",$q);
                        if($r = mysql_fetch_array($q))
                                $tarikh = $r["tarikh"];
                        $tarikh = date("Y-m-d",strtotime($tarikh));
                        if($shomare_sanad<=0)
                        {
                                $shomare_sanad = $mxs;
                                if(strtotime($tarikh)<strtotime(date("Y-m-d")))
                                        $shomare_sanad++;
                        }
                        $tarikh = date("Y-m-d");
                        $sanad_record = array();
			$ghimat = $ghimat_tour - ($ghimat_blit1+$ghimat_blit2);
			if((int)$ghimat > 0 )
			{
	                        mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$h_moeen->kol_id.",".$hotel->moeen_id.",'$tarikh',$user_id,-1,1,$ghimat,'$toz')");
        	                $q = null;
                	        mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=".$h_moeen->kol_id." and `moeen_id`=".$hotel->moeen_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=-1 and `en`=1 and `mablagh`=$ghimat",$q);
                        	if($r = mysql_fetch_array($q))
                                	$sanad_record[] = (int)$r['id'];
			}
			if((int)$ghimat_blit1>0 && $other_kol_id>0 && $other_id>0)
			{
	                        mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$other_kol_id.",".$other_id.",'$tarikh',$user_id,-1,1,$ghimat_blit1,'$toz')");
        	                $q = null;
                	        mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$other_kol_id and `moeen_id`=".$other_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=-1 and `en`=1 and `mablagh`=$ghimat_blit1",$q);
                        	if($r = mysql_fetch_array($q))
                                	$sanad_record[] = (int)$r['id'];
			}
                        if((int)$ghimat_blit2>0 && $other_kol_id2>0 && $other_id2>0)
                        {
                                mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$other_kol_id2.",".$other_id2.",'$tarikh',$user_id,-1,1,$ghimat_blit2,'$toz')");
                                $q = null;
                                mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=$other_kol_id2 and `moeen_id`=".$other_id2." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=-1 and `en`=1 and `mablagh`=$ghimat_blit2",$q);
                                if($r = mysql_fetch_array($q))
                                        $sanad_record[] = (int)$r['id'];
                        }
			if((int)$ghimat_tour>0)
			{
	                        mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$a_moeen->kol_id.",".$ajans->moeen_id.",'$tarikh',$user_id,1,1,$ghimat_tour,'$toz')");
        	                $q = null;
                	        mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`=$shomare_sanad and `kol_id`=".$a_moeen->kol_id." and `moeen_id`=".$ajans->moeen_id." and `tozihat`='$toz' and `tarikh`='$tarikh' and `user_id` = $user_id and `typ`=1 and `en`=1 and `mablagh`=$ghimat_tour",$q);
                        	if($r = mysql_fetch_array($q))
                                	$sanad_record[] = (int)$r['id'];
			}
                        return($sanad_record);
		}
		public function checkHesab($hotel_ghimat)
		{
			$is_reserve = TRUE;
			if(is_array($hotel_ghimat))
			{
				$msg = '';
				if($hotel_ghimat['ghimat_tour']-($hotel_ghimat['ghimat_belit1'] + $hotel_ghimat['ghimat_belit2']) <= 0)
				{
					$msg .= 'قیمت تور درست وارد نشده است';
					$is_reserve = FALSE;
				}
				else
				{
					if($hotel_ghimat['ghimat_belit1']>0)
						if($hotel_ghimat['other_moeen_id1']<=0 && $hotel_ghimat['other_kol_id1']<=0)
						{
							$is_reserve = FALSE;
							$msg .= 'حساب بلیت رفت درست وارد نشده است';
						}
					if($hotel_ghimat['ghimat_belit2']>0)
						if($hotel_ghimat['other_moeen_id2']<=0 && $hotel_ghimat['other_kol_id1']<=0)
						{
							$is_reserve = FALSE;
							$msg .= 'حساب بلیت رفت درست وارد نشده است';
						}
				}
				$out['is_reserve'] = $is_reserve;
				$out['msg'] = $msg .' رزرو انجام نشد'; 
			}
			else
			{
				$out['is_reserve'] = $is_reserve;
				$out['msg'] = '';
			}
			return $out ;
		}
		public function getShomareSanad()
		{
			$shomare_sanad = -1;
                        $mxs = 200;
                        mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
                        if($r = mysql_fetch_array($q))
	                        $mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
			$q = null;
                        $tarikh = date("Y-m-d");
                        mysql_class::ex_sql("SELECT `tarikh` from `sanad` where `shomare_sanad`=$mxs",$q);
                        if($r = mysql_fetch_array($q))
                                $tarikh = $r["tarikh"];
                        $tarikh = date("Y-m-d",strtotime($tarikh));
                        if($shomare_sanad<=0)
                        {
                                $shomare_sanad = $mxs;
                                if(strtotime($tarikh)<strtotime(date("Y-m-d")))
                                        $shomare_sanad++;
                        }
			return $shomare_sanad;
		}
		public function anbarSabt($anbar_factor_id,$kala_id,$anbar_typ,$anbar_id,$tedad,$ghimat,$moeen_id_moshtari,$moeen_id_anbar,$user_id)
		{
			$sanad_rec = array();
			$shomare_sanad = sanadzan_class::getShomareSanad();
			$kol_id_anbar = new moeen_class($moeen_id_anbar);
			$kol_id_anbar = $kol_id_anbar->kol_id;
			$kol_id_moshtari = new moeen_class($moeen_id_moshtari);
			$kol_id_moshtari = $kol_id_moshtari->kol_id;
			$tarikh = date("Y-m-d H:i:s");
			$kala = new kala_class($kala_id);
			$anbar = new anbar_class($anbar_id);
			$vahed = new kala_vahed_class($kala->vahed_id);
			$jahat = ($anbar_typ>0)?'ورود به':'خروج از ';
			$toz = 'تعداد '.$tedad.' '.$vahed->name.' '.$kala->name.' '.$jahat.' انبار '.$anbar->name.' به کد فاکتور '.$anbar_factor_id;
			mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,$kol_id_anbar,$moeen_id_anbar,'$tarikh',$user_id,".(-1*$anbar_typ).",1,$ghimat,'$toz')");
			$q = null;
			mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`='$shomare_sanad' and `kol_id`='$kol_id_anbar' and `moeen_id`='$moeen_id_anbar' and `tarikh`='$tarikh' and `user_id`='$user_id' and `typ`='".(-1*$anbar_typ)."' and `en`='1' and `mablagh`=$ghimat order by `id` desc limit 1",$q);
			if($r=mysql_fetch_array($q))
				$sanad_rec[] =(int) $r['id'];
			return $sanad_rec;
		}
		public function anbarSabtTak($anbar_factor_id,$ghimat,$anbar_typ_id,$user_id)
		{
			$anbar_factor = new anbar_factor_class($anbar_factor_id);
			$sanad_rec = array();
			$shomare_sanad = sanadzan_class::getShomareSanad();
			$kol_id_moshtari = new moeen_class($anbar_factor->moeen_id);
			$kol_id_moshtari = $kol_id_moshtari->kol_id;
			$anbar_typ_id = new anbar_typ_class($anbar_typ_id);
			$anbar_typ_id = $anbar_typ_id->typ;
			$toz='فاکتور '.$anbar_factor->factor_id.' به تاریخ '.audit_class::hamed_pdate($anbar_factor->tarikh_resid).' به کد فاکتور '.$anbar_factor_id;
			mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,$kol_id_moshtari,".$anbar_factor->moeen_id.",'".$anbar_factor->tarikh_resid."',$user_id,$anbar_typ_id,1,$ghimat,'$toz')");
			$q = null;
			mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`='$shomare_sanad' and `kol_id`='$kol_id_moshtari' and `moeen_id`='".$anbar_factor->moeen_id."' and `tarikh`='".$anbar_factor->tarikh_resid."' and `user_id`='$user_id' and `typ`='$anbar_typ_id' and `en`='1' and `mablagh`=$ghimat order by `id` desc limit 1 ",$q);
			if($r=mysql_fetch_array($q))
				$sanad_rec[] = (int)$r['id'];
			return $sanad_rec;
		}
		public function sabtSmsToz($reserve_id,$ghimat)
		{
			$sanad_record = new sanad_reserve_class($reserve_id);
			$sanad_record = $sanad_record->sanad_record;
			$ghimat = ' قیمت پیامک شده توسط مشتری '.monize((int)$ghimat);
			for($i=0;$i<count($sanad_record);$i++)
			{
				mysql_class::ex_sqlx("update `sanad` set `tozihat`=concat(`tozihat`,'$ghimat') where `id`=".$sanad_record[$i]);
				//echo "update `sanad` set `tozihat`=concat(`tozihat`,'$ghimat') where `id`=".$sanad_record[$i]."<br/>";
			}
		}
		public function deleteAnbarSabt($anbar_factor_id)
		{
			$arr = array();
			//mysql_class::ex_sqlx("delete from `sanad` where `id` in (select `sanad_record_id` from `sanad_anbar` where `anbar_factor_id` = $anbar_factor_id)");
			mysql_class::ex_sql("select `sanad_record_id` from `sanad_anbar` where `anbar_factor_id` = $anbar_factor_id",$q);
			while($r=mysql_fetch_array($q))
				$arr[] = $r['sanad_record_id'];
			$arr = implode(',',$arr);
			$arr = ($arr=='')?-1:$arr;
			mysql_class::ex_sqlx("delete from `sanad` where `id` in ($arr)");
			mysql_class::ex_sqlx("delete from `sanad_anbar` where `anbar_factor_id` = $anbar_factor_id");
		}
		public function getNewShomareSanad($tarikh)
		{
			$out = -1;
			$aztarikh = $tarikh.' 00:00:00';	
			$tatarikh = $tarikh.' 23:59:59';
			//mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `sh_sanad` from `sanad` where `tarikh`>='$aztarikh' and `tarikh`<='$tatarikh' ",$q);
//echo "SELECT MAX(`shomare_sanad`) as `sh_sanad` from `sanad` where `tarikh`>='$aztarikh' and `tarikh`<='$tatarikh' "
			mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `sh_sanad` from `sanad`",$q);
			if($r = mysql_fetch_array($q))
			{
				if($r['sh_sanad']!=null)
				{
					$out = $r['sh_sanad'];
					$out = $out + 1;
				}
				else
				{
					$mxs = 0;
					mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
					if($r = mysql_fetch_array($q))
						$mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
					$out = $mxs+1;
				}
			}
			return $out;
		}
		public function checkSanad($shomare_sanad)
		{
			$conf = new conf;
			$out = FALSE;
			$time_conf = $conf->limitDatelimit_sanad_time;
//echo $time_conf.'<br/>';
			$now = date("Y-m-d H:i:s");
			mysql_class::ex_sqlx("update `sanad` set `en`=1 where `en`=0 and  ADDDATE(DATE(`tarikh`),interval $time_conf hour)<='$now'");
			//mysql_class::ex_sqlx("update `sanad` set `en`=1 where `en`=0 and  ADDDATE(DATE(`tarikh`),interval $time_conf day)<='$now'");
			mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`='$shomare_sanad' and ADDDATE(DATE(`tarikh`),interval $time_conf hour)>'$now' limit 1",$q);
			//mysql_class::ex_sql("select `id` from `sanad` where `shomare_sanad`='$shomare_sanad' and ADDDATE(DATE(`tarikh`),interval $time_conf day)>'$now' limit 1",$q);
//echo "select `id` from `sanad` where `shomare_sanad`='$shomare_sanad' and ADDDATE(DATE(`tarikh`),interval $time_conf hour)>'$now' limit 1".'<br/>';
			if($r = mysql_fetch_array($q))
				$out = TRUE;
			return $out;
		}
		public function belitSanadzan2($ajans_bed_id,$ajans_bes_id,$user_id,$ghimat,$toz)
		{
			$ajans_bed = new ajans_class($ajans_bed_id);
			$moeen_id_bed = $ajans_bed->moeen_id;
			$kol_id_bed = new moeen_class($moeen_id_bed);
			$kol_id_bed = $kol_id_bed->kol_id;
		
			$ajans_bes = new ajans_class($ajans_bes_id);
			$moeen_id_bes = $ajans_bes->moeen_id;
			$kol_id_bes = new moeen_class($moeen_id_bes);
			$kol_id_bes = $kol_id_bes->kol_id;
	
			$tarikh = date("Y-m-d H:i:s" );
			$shomare_sanad = sanadzan_class::getShomareSanad();
			//----------bedehkar---------------------------------------
			$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,$kol_id_bed,$moeen_id_bed,'$tarikh',$user_id,-1,1,$ghimat,'$toz')",FALSE);
			$out['bed_id']= mysql_insert_id();
			mysql_close($ln);
			//---------bestankar --------------------------------------
			$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,$kol_id_bes,$moeen_id_bes,'$tarikh',$user_id,1,1,$ghimat,'$toz')",FALSE);
			$out['bes_id']= mysql_insert_id();
			mysql_close($ln);
			$out['shomare_sanad'] =$shomare_sanad ;
			return $out;
		}
		public function sondoghFactor($sandogh_moeen,$moshtari_moeen,$ghimat,$toz,$total_toz,$shomare_factor,$user_id)
		{
			$out = FALSE;
			$shomare_factor = (int)$shomare_factor;
			$factor_ok = TRUE;
			mysql_class::ex_sql("select `id` from `sanad_sandogh` where `shomare_factor` = $shomare_factor",$qq);
			if($r = mysql_fetch_array($qq))
				$factor_ok = FALSE;
			if(is_array($ghimat) && is_array($toz) && count($ghimat) == count($toz) && $factor_ok)
			{
				$tarikh = date("Y-m-d");
				$shomare_sanad = sanadzan_class::getNewShomareSanad($tarikh);
				$sandogh_moeen = new moeen_class((int)$sandogh_moeen);
				$moshtari_moeen = new moeen_class((int)$moshtari_moeen);
				$id1 = null;
				$total_ghimat = 0;
				for($i = 0;$i < count($ghimat); $i++)
					if((int)$ghimat>0)
					{
						$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".($sandogh_moeen->kol_id).",".($sandogh_moeen->id).",'$tarikh',$user_id,1,1,".$ghimat[$i].",'".$toz[$i]."')",FALSE);
						$id1_tmp = mysql_insert_id();
						$id1[] = $id1_tmp;
						mysql_close($ln);
						mysql_class::ex_sqlx("insert into `sanad_sandogh` (`sanad_record`,`shomare_factor`) values ($id1_tmp,$shomare_factor)");
						$total_ghimat += $ghimat[$i];
					}
				if($total_ghimat > 0)
				{
					$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".($moshtari_moeen->kol_id).",".($moshtari_moeen->id).",'$tarikh',$user_id,-1,1,$total_ghimat,'$total_toz')",FALSE);
        		                $id2 = mysql_insert_id();
                		        mysql_close($ln);
					mysql_class::ex_sqlx("insert into `sanad_sandogh` (`sanad_record`,`shomare_factor`) values ($id2,$shomare_factor)");
				}
				$out = TRUE;
			}
			return($out);
		}
                public function sondoghResid($sandogh_moeen,$moshtari_moeen,$ghimat,$toz,$total_toz,$shomare_factor,$user_id)
                {
                        $out = FALSE;
                        $shomare_factor = (int)$shomare_factor;
                        $factor_ok = TRUE;
                        mysql_class::ex_sql("select `id` from `sanad_sandogh` where `shomare_factor` = $shomare_factor",$qq);
                        if($r = mysql_fetch_array($qq))
                                $factor_ok = FALSE;
                        if(is_array($ghimat) && is_array($toz) && count($ghimat) == count($toz) && $factor_ok)
                        {
                                $tarikh = date("Y-m-d");
                                $shomare_sanad = sanadzan_class::getNewShomareSanad($tarikh);
                                $sandogh_moeen = new moeen_class((int)$sandogh_moeen);
                                $moshtari_moeen = new moeen_class((int)$moshtari_moeen);
                                $id1 = null;
                                $total_ghimat = 0;
                                for($i = 0;$i < count($ghimat); $i++)
                                        if((int)$ghimat>0)
                                        {
                                                $ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".($sandogh_moeen->kol_id).",".($sandogh_moeen->id).",'$tarikh',$user_id,-1,1,".$ghimat[$i].",'".$toz[$i]."')",FALSE);
                                                $id1_tmp = mysql_insert_id();
                                                $id1[] = $id1_tmp;
                                                mysql_close($ln);
                                                mysql_class::ex_sqlx("insert into `sanad_sandogh` (`sanad_record`,`shomare_factor`) values ($id1_tmp,$shomare_factor)");
                                                $total_ghimat += $ghimat[$i];
                                        }
                                if($total_ghimat > 0)
                                {
                                        $ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".($moshtari_moeen->kol_id).",".($moshtari_moeen->id).",'$tarikh',$user_id,1,1,$total_ghimat,'$total_toz')",FALSE);
                                        $id2 = mysql_insert_id();
                                        mysql_close($ln);
                                        mysql_class::ex_sqlx("insert into `sanad_sandogh` (`sanad_record`,`shomare_factor`) values ($id2,$shomare_factor)");
                                }
                                $out = TRUE;
                        }
                        return($out);
                }
		public function newOnlineHotelReserveSanad($hotel_id,$ajans_id,$ajans_comision_id,$ghimat,$comision,$takhfif,$shomare_sanad,$user_id)
		{
			$sanad_record = array();
			$user_id = $user_id <= 0 ?(int)$_SESSION["user_id"] : $user_id;
			$hotel = new hotel_class((int)$hotel_id);
			$h_moeen = new moeen_class($hotel->moeen_id);
			$ajans = new ajans_class((int)$ajans_id);
			$ajans_comision = new ajans_class((int)$ajans_comision_id);
			$ajans_co = new moeen_class($ajans_comision->moeen_id);
			$a_moeen = new moeen_class($ajans->moeen_id);
                        $mxs = 0;
			mysql_class::ex_sql("SELECT MAX(`shomare_sanad`) as `mxs` from `sanad`",$q);
			if($r = mysql_fetch_array($q))
				$mxs = (((int)$r["mxs"]>1)?(int)$r["mxs"]:1);
			$q = null;
			$tarikh = date("Y-m-d");
			mysql_class::ex_sql("SELECT `tarikh` from `sanad` where `shomare_sanad`=$mxs",$q);
                        if($r = mysql_fetch_array($q))
				$tarikh = $r["tarikh"];
			$tarikh = date("Y-m-d",strtotime($tarikh));
			if($shomare_sanad<=0)
			{
				$shomare_sanad = $mxs;
				if(strtotime($tarikh)<strtotime(date("Y-m-d")))
					$shomare_sanad++;
			}
			$tarikh = date("Y-m-d");
			$sanad_record = array();
			$toz = 'با کارمزد '.$comision;
			$toz_takhfif = 'کسری '.$takhfif.' از کارمزد '.$comision;
			$ghimat = (int)$ghimat;
			$comision = (int)$comision;
			$takhfif = (int)$takhfif;
			if($ghimat>0 && $ghimat>$comision && $comision>=$takhfif)
			{
				$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$h_moeen->kol_id.",".$hotel->moeen_id.",'$tarikh',$user_id,1,1,".($ghimat-$comision).",'$toz')",FALSE);
				$sanad_record[] = mysql_insert_id($ln);
				mysql_close($ln);
				$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$a_moeen->kol_id.",".$ajans->moeen_id.",'$tarikh',$user_id,-1,1,$ghimat,'$toz')",FALSE);
	                        $sanad_record[] = mysql_insert_id($ln);
				mysql_close($ln);
				if($comision>0)
				{
					$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$ajans_co->kol_id.",".$ajans_comision->moeen_id.",'$tarikh',$user_id,1,1,$comision,'$toz')",FALSE);
			                $sanad_record[] = mysql_insert_id($ln);
					mysql_close($ln);
					if($takhfif>0)
					{
						$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$h_moeen->kol_id.",".$hotel->moeen_id.",'$tarikh',$user_id,1,1,$takhfif,'$toz_takhfif')",FALSE);
					        $sanad_record[] = mysql_insert_id($ln);
						mysql_close($ln);
						$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,".$ajans_co->kol_id.",".$ajans_comision->moeen_id.",'$tarikh',$user_id,-1,1,$takhfif,'$toz_takhfif')",FALSE);
					        $sanad_record[] = mysql_insert_id($ln);
						mysql_close($ln);
					}
				}
			}
			return($sanad_record);	
		}
                public function anbarGardaniSabt($kala_id,$anbar_id,$tedad,$ghimat,$moeen_id_anbardar,$user_id,$isKala = TRUE)
                {
                        $sanad_rec = array();
                        $anbar = new anbar_class($anbar_id);
                        $moeen_id_anbar = $anbar->moeen_id;
                        $shomare_sanad = sanadzan_class::getShomareSanad();
                        $kol_id_anbar = new moeen_class($moeen_id_anbar);
                        $kol_id_anbar = $kol_id_anbar->kol_id;
                        $kol_id_anbardar = new moeen_class($moeen_id_anbardar);
                        $kol_id_anbardar = $kol_id_anbardar->kol_id;
                        $tarikh = date("Y-m-d H:i:s");
                        $kala = new kala_class($kala_id);
                        $vahed = new kala_vahed_class($kala->vahed_id);
                        $jahat = ' کسری از انبارگردانی ';
                        $ghimat = (int)$ghimat;
                        if($ghimat>0)
                        {
                                if($isKala)
                                {
                                        $toz = 'تعداد '.$tedad.' '.$vahed->name.' '.$kala->name.' '.$jahat.' انبار '.$anbar->name;
                                        $ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,$kol_id_anbar,$moeen_id_anbar,'$tarikh',$user_id,1,1,$ghimat,'$toz')",FALSE);
                                }
                                else
                                {
                                        $toz = $jahat.' انبار '.$anbar->name;
                                        $ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,$kol_id_anbardar,$moeen_id_anbardar,'$tarikh',$user_id,-1,1,$ghimat,'$toz')",FALSE);
                                }
                                $sanad_rec[] = mysql_insert_id($ln);
                                mysql_close($ln);
                        }
                        return $sanad_rec;
                }
		public function factorSabt($anbar_factor_id,$moeen_id_moshtari,$user_id)
		{
			$sanad_rec = array();
			$anbar_factor_id = (int)$anbar_factor_id;
			$shomare_sanad = sanadzan_class::getShomareSanad();
			$kol_id_moshtari = new moeen_class($moeen_id_moshtari);
			$kol_id_moshtari = $kol_id_moshtari->kol_id;
			$user = new user_class($user_id);
			$daft = new daftar_class($user->daftar_id);
			$sandogh_moeen_id = $daft->sandogh_moeen_id;
			$sandog_kol_id = $daft->kol_id;
			$tarikh = date("Y-m-d H:i:s");
			$anbar_factor = new anbar_factor_class($anbar_factor_id);
			if((int)$anbar_factor->anbar_typ_id==4)
			{
				$toz = 'بابت شماره فاکتور '.$anbar_factor_id;
				mysql_class::ex_sql("select SUM(`tedad`*`ghimat`) as `ghi` from `factor_khadamat_det` where  `anbar_factor_id`=$anbar_factor_id",$qq);
				$ghimat =0;
				if($r = mysql_fetch_array($qq))
					$ghimat = (int)$r['ghi'];
				if($ghimat>0)
				{
					$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,$kol_id_moshtari,$moeen_id_moshtari,'$tarikh',$user_id,1,1,$ghimat,'$toz')",FALSE);
					$sanad_rec[] =(int)mysql_insert_id($ln);
					mysql_close($ln);
					$ln = null;
					$ln = mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`, `user_id`, `typ`,`en`, `mablagh`,`tozihat`) values ($shomare_sanad,$sandog_kol_id,$sandogh_moeen_id,'$tarikh',$user_id,-1,1,$ghimat,'$toz')",FALSE);
					$sanad_rec[] =(int)mysql_insert_id($ln);
					mysql_close($ln);
				}
			}
			return $sanad_rec;
		}
		public function sanad_transfer($shomare_sanad,$nameDriver,$reserve_id,$user_id,$hazine)
		{
			$out = -1;
			$moeen_id = -1;
			$name_mehman = '';
			$kol_hamlonaghl = 67;
			$moeen_hamlonaghl = 1150;
			$tarikh = date('Y/m/d H:i');
			if ($shomare_sanad>0)
			{
				mysql_class::ex_sql("select * from `driver` where `id`='$nameDriver'",$q);
				if($r = mysql_fetch_array($q))
				{
					$moeen_id = $r['moeen_driver'];
					$name = $r['name'];
				}
				mysql_class::ex_sql("select * from `moeen` where `id`='$moeen_id'",$q);
				if($r = mysql_fetch_array($q))
					$kol_id = $r['kol_id'];
				mysql_class::ex_sql("select `fname`,`lname`,`ajans_id` from `hotel_reserve` where `reserve_id`='$reserve_id'",$q);
				if($r = mysql_fetch_array($q))
				{
					$name_mehman = $r['fname'].' '.$r['lname'];
					$aj_name = ajans_class::loadById($r['ajans_id']);
				}
				$aj_matn = "آژانس رزرو گیرنده:".$aj_name ;
				$tozih = "خدمات ترانسفر برای میهمان".$name_mehman.' '.' با شماره رزرو'.$reserve_id.' '.$aj_matn.' '."در تاریخ".audit_class::hamed_pdate_2($tarikh)."توسط ".$name."انجام گردید";
				$ln = mysql_class::ex_sqlx("INSERT INTO `sanad` (`id`, `shomare_sanad`, `group_id`, `kol_id`, `moeen_id`, `tafzili_id`, `tafzili2_id`, `tafzilishenavar_id`, `tafzilishenavar2_id`, `tarikh`, `user_id`, `typ`, `tozihat`, `en`, `mablagh`) VALUES (NULL, '$shomare_sanad', NULL, '$kol_id', '$moeen_id', NULL, NULL, NULL, NULL, '$tarikh', '$user_id', '1', '$tozih', '1', '$hazine')",FALSE);
				$ln_1 = mysql_class::ex_sqlx("INSERT INTO `sanad` (`id`, `shomare_sanad`, `group_id`, `kol_id`, `moeen_id`, `tafzili_id`, `tafzili2_id`, `tafzilishenavar_id`, `tafzilishenavar2_id`, `tarikh`, `user_id`, `typ`, `tozihat`, `en`, `mablagh`) VALUES (NULL, '$shomare_sanad', NULL, '$kol_hamlonaghl', '$moeen_hamlonaghl', NULL, NULL, NULL, NULL, '$tarikh', '$user_id', '-1', '$tozih', '1', '$hazine')",FALSE);
				$out = $ln;
			}
		}
		public function delete_sanad_transfer($shomare_sanad,$id_tra_del,$user_id)
		{
			$out = -1;
			$moeen_id = -1;
			$name_mehman = '';
			$kol_hamlonaghl = 67;
			$moeen_hamlonaghl = 1150;
			$tarikh = date('Y/m/d H:i');
			$hazine = 100;
			if ($shomare_sanad>0)
			{
				mysql_class::ex_sql("select * from `khadamat_transfer` where `id`='$id_tra_del'",$q_tra);
				if($r_tra = mysql_fetch_array($q_tra))
				{
					$id_driver = $r_tra['driverName'];
					$reserve_id = $r_tra['reserve_id'];
					mysql_class::ex_sql("select * from `driver` where `id`='$id_driver'",$q_dri);
					if($r_dri = mysql_fetch_array($q_dri))
					{
						$moeen_id = $r_dri['moeen_driver'];
						$name = $r_dri['name'];
					}
					$id_taget = $r_tra['target_id'];
					mysql_class::ex_sql("select * from `target` where `id`='$id_taget'",$q_tar);
					if($r_tar = mysql_fetch_array($q_tar))
						$hazine = $r_tar['hazine'];
					mysql_class::ex_sql("select * from `moeen` where `id`='$moeen_id'",$q_kol);
					if($r_kol = mysql_fetch_array($q_kol))
						$kol_id = $r_kol['kol_id'];
					mysql_class::ex_sql("select `fname`,`lname` from `hotel_reserve` where `reserve_id`='$reserve_id'",$q);
					if($r = mysql_fetch_array($q))
					{
						$name_mehman = $r['fname'].' '.$r['lname'];
						$aj_name = ajans_class::loadById($r['ajans_id']);
					}
					$aj_matn = "آژانس رزرو گیرنده:".$aj_name ;
					$tozih = "خدمات ترانسفر برای میهمان".$name_mehman.' '.' با شماره رزرو'.$reserve_id.' '.$aj_matn.' '."در تاریخ".audit_class::hamed_pdate_2($tarikh)."توسط ".$name."کنسل گردید";
					$ln = mysql_class::ex_sqlx("INSERT INTO `sanad` (`id`, `shomare_sanad`, `group_id`, `kol_id`, `moeen_id`, `tafzili_id`, `tafzili2_id`, `tafzilishenavar_id`, `tafzilishenavar2_id`, `tarikh`, `user_id`, `typ`, `tozihat`, `en`, `mablagh`) VALUES (NULL, '$shomare_sanad', NULL, '$kol_id', '$moeen_id', NULL, NULL, NULL, NULL, '$tarikh', '$user_id', '-1', '$tozih', '1', '$hazine')",FALSE);
					$ln_1 = mysql_class::ex_sqlx("INSERT INTO `sanad` (`id`, `shomare_sanad`, `group_id`, `kol_id`, `moeen_id`, `tafzili_id`, `tafzili2_id`, `tafzilishenavar_id`, `tafzilishenavar2_id`, `tarikh`, `user_id`, `typ`, `tozihat`, `en`, `mablagh`) VALUES (NULL, '$shomare_sanad', NULL, '$kol_hamlonaghl', '$moeen_hamlonaghl', NULL, NULL, NULL, NULL, '$tarikh', '$user_id', '1', '$tozih', '1', '$hazine')",FALSE);
					$out = $ln;
				}
			}
		}
/*
		public function roomReserve($room_id,$startDate,$reserve_id,$user_id,$ghimat)
		{
			$shomare_sanad = sanadzan_class::getShomareSanad();
			$room_id = (int)$room_id;
			$ghimat = (int)$ghimat;
			$room_tmp = new room_class($room_id);
			$moeen_id = $room_tmp->moeen_id;
			$kol = new moeen_class($moeen_id);
			$kol = $kol->id;
			if($ghimat>0 && $moeen_id>0)
			{
				
			}
		}
*/
	}
?>
