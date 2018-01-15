<?php
	class anbar_det_class
	{
		public $id=-1;
		public $kala_id=-1;
		public $tarikh='0000-00-00 00:00:00';
		public $user_id=-1;
		public $tedad=0;
		public $ghimat=0;
		public $other_user_id=-1;
		public $anbar_id=-1;
		public $anbar_typ_id=-1;
		public $anbar_factor_id=-1;
		public $en=0;
		public $tedad_kh = 0;
/*		public $other_name=-1;
                public $other_moeen_id=-1;
                public $other_factor_shomare=-1;
                public $tozihat=-1;
		public $other_tarikh=-1;*/
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `anbar_det` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->kala_id=$r['kala_id'];
				$this->tarikh=$r['tarikh'];
				$this->user_id=$r['user_id'];
				$this->tedad=$r['tedad'];
				$this->tedad_kh=$r['tedad_kh'];
				$this->ghimat=$r['ghimat'];
				$this->other_user_id=$r['other_user_id'];
				$this->anbar_id=$r['anbar_id'];
				$this->anbar_typ_id=$r['anbar_typ_id'];
				$this->anbar_factor_id=$r['anbar_factor_id'];
				$this->en=$r['en'];
/*				$this->other_name=$r['other_name'];
		                $this->other_moeen_id=$r['other_moeen_id'];
		                $this->other_factor_shomare=$r['other_factor_shomare'];
		                $this->tozihat=$r['tozihat'];
		                $this->other_tarikh=$r['other_tarikh'];
*/
			}
		}
		public function voroodKala($anbar_id,$kala_id,$anbardar_user_id,$user_id)
		{
			$factor_id = 'ورود به انبار';
			$tozihat = 'جهت انبار گردانی';
			$name = '---';
			$moeen_id= -3;
			$tarikh_resid = date("Y-m-d H:i:s");
			$ln = mysql_class::ex_sqlx("insert into `anbar_factor` (`factor_id`,`name`,`tozihat`,`moeen_id`,`tarikh_resid`,`user_id`) values ('$factor_id','$name','$tozihat ','$moeen_id','$tarikh_resid','$user_id') ",FALSE);
			$anbar_factor_id = mysql_insert_id($ln);
			mysql_close($ln);
			for($i=0;$i<count($kala_id);$i++)
			{
				$tedad = (int)$kala_id[$i]['tedad'];
				$tedad_new = (int)$kala_id[$i]['tedad_new'];
				$tedad = abs($tedad_new-$tedad);
				$kala_tmp = new kala_class($kala_id[$i]['id']);
				$ghimat = anbar_det_class::calcGhimatGardani($kala_id[$i]['id'],$tedad,FALSE,TRUE);
				if($kala_id[$i]['tedad']<$kala_id[$i]['tedad_new'])
				{
					if($ghimat>0)
						mysql_class::ex_sqlx("insert into `anbar_det` (`kala_id`,`tarikh`,`user_id`,`tedad`,`ghimat`,`other_user_id`,`anbar_id`,`anbar_typ_id`,`anbar_factor_id`,`en`,`tedad_kh`) values('".$kala_id[$i]['id']."','$tarikh_resid','$user_id','$tedad','$ghimat','$anbardar_user_id','$anbar_id','1','$anbar_factor_id','1','0')");
				}
				else if($kala_id[$i]['tedad']>$kala_id[$i]['tedad_new'])
				{
					$ghimat = anbar_det_class::calcGhimatGardani($kala_id[$i]['id'],$tedad,TRUE);
					if($ghimat>0)
						anbar_det_class::khorooj($anbar_id,2,$anbar_factor_id,$kala_id[$i]['id'],$tedad,$user_id,$anbardar_user_id,$ghimat,$tarikh_resid);
				}
			}
			
		}
		public function loadResid()
		{
/*
			$out = 1;
			mysql_class::ex_sql('select max(`resid_havale_no`) as `resid` from `anbar_det` ',$q);
			if($r = mysql_fetch_array($q))
				$out = (int)$r['resid']+1;
			return($out);
*/
		}
		public function calcGhimat($kala_id,$tedad,$update = TRUE)
		{
			$kala_id = (int)$kala_id;
			$tedad = (float)$tedad;
			$out = 0;
			$gh = 0;
			$done = FALSE;
			$moj = anbar_det_class::getMojoodi($kala_id);
			$moj = $moj['out'];
			$mojoodiOk = ($moj>=$tedad);
			mysql_class::ex_sql("select `id`,`tedad`,`tedad_kh`,`ghimat` from `anbar_det` where `anbar_typ_id` in (select `id` from `anbar_typ` where `typ` = 1) and `kala_id` = $kala_id and `tedad`+`tedad_kh` > 0 and `en` = 1 order by `id` desc",$q);
			while($r = mysql_fetch_array($q))
			{
				$tmp_tedad = (float)$r['tedad']+(float)$r['tedad_kh'];
				$tmp_ghimat = (float)$r['ghimat'];
				$gh += $tmp_tedad;
				if(!$done && $mojoodiOk)
				{
					if($gh<$tedad)
					{
						$out += $tmp_ghimat*$tmp_tedad/(float)$r['tedad'];
						if($update)
						{
							mysql_class::ex_sqlx('update `anbar_det` set `tedad_kh`=`tedad_kh`-'.$tmp_tedad.' where `id`='.$r['id']);
						}
					}
					else
					{
						$out += ($tedad+$tmp_tedad-$gh)*$tmp_ghimat/(float)$r['tedad'];
						if($update)
						{
							mysql_class::ex_sqlx('update `anbar_det` set `tedad_kh`=`tedad_kh`-'.($tedad+$tmp_tedad-$gh).' where `id`='.$r['id']);
						}
					}
				}
				if($gh >= $tedad)
					$done = TRUE;
			}
			if(!$done || !$mojoodiOk)
				$out = -1;
			return($out);
		}
                public function calcGhimatGardani($kala_id,$tedad,$update,$justGhimat = FALSE)
                {
                        $kala_id = (int)$kala_id;
                        $tedad = (float)$tedad;
                        $out = 0;
                        $gh = 0;
                        $done = FALSE;
			$moj = anbar_det_class::getMojoodi($kala_id);
                        $moj = $moj['out'];
                        $mojoodiOk = ($moj>=$tedad);
			if(!$mojoodiOk && $justGhimat)
			{
				mysql_class::ex_sql("select `ghimat`,`tedad` from `anbar_det` where `anbar_typ_id` in (select `id` from `anbar_typ` where `typ` = 1) and `kala_id` = $kala_id and `tedad`+`tedad_kh` >= 0 and `en` = 1 order by `id` desc limit 1",$q);
				if($r = mysql_fetch_array($q))
				{
					$out = (int)$r['ghimat'];
					$out = $out * $tedad / $r['tedad'];
				}
				return($out);
			}
                        mysql_class::ex_sql("select `id`,`tedad`,`tedad_kh`,`ghimat` from `anbar_det` where `anbar_typ_id` in (select `id` from `anbar_typ` where `typ` = 1) and `kala_id` = $kala_id and `tedad`+`tedad_kh` >= 0 and `en` = 1 order by `id` desc",$q);
                        while($r = mysql_fetch_array($q))
                        {
       	                        $tmp_tedad = (float)$r['tedad']+(float)$r['tedad_kh'];
               	                $tmp_ghimat = (float)$r['ghimat'];
                       	        $gh += $tmp_tedad;
                               	if(!$done && $mojoodiOk)
                                {
               	                        if($gh<$tedad)
                       	                {
                                	        $out += $tmp_ghimat*$tmp_tedad/(float)$r['tedad'];
                                               	if($update)
                                                       	mysql_class::ex_sqlx('update `anbar_det` set `tedad_kh`=`tedad_kh`-'.$tmp_tedad.' where `id`='.$r['id']);
                                        }
       	                                else
               	                        {
                               	                $out += ($tedad+$tmp_tedad-$gh)*$tmp_ghimat/(float)$r['tedad'];
                                       	        if($update)
                                               	        mysql_class::ex_sqlx('update `anbar_det` set `tedad_kh`=`tedad_kh`-'.($tedad+$tmp_tedad-$gh).' where `id`='.$r['id']);
                                        }
       	                        }
               	                if($gh >= $tedad)
                       	                $done = TRUE;
                        }
			$done = ($done || !$update);
                        if(!$done || !$mojoodiOk)
                                $out = -1;
                        return($out);
                }

		public function getMojoodi($kala_id)
		{
			$out = 0;
			$msg = '';
			$v = 0;
			$k = 0;
			$kala_hast = FALSE;
			mysql_class::ex_sql("select `id` from `anbar_det` where `kala_id` = $kala_id and `en` = 1",$q);
                        if($r = mysql_fetch_array($q))
				$kala_hast = TRUE;
			$q = null;
			mysql_class::ex_sql("select SUM(`tedad`) as `v`, SUM(`tedad_kh`) as `k` from `anbar_det` where `anbar_typ_id` in (select `id` from `anbar_typ` where `typ` = 1) and `kala_id` = $kala_id and `en` = 1",$q);
			if($r = mysql_fetch_array($q))
			{
				$v = (float)$r['v'];
				$k = -1 * (float)$r['k'];
			}
/*
			$q = null;
			mysql_class::ex_sql("select SUM(`tedad`) as `k` from `anbar_det` where `anbar_typ_id` in (select `id` from `anbar_typ` where `typ` = -1) and `kala_id` = $kala_id and `en` = 1",$q);
                        if($r = mysql_fetch_array($q))
                                $k = (float)$r['k'];
*/
			if($v>=$k && $kala_hast)
				$out = $v-$k;
			else if($kala_hast)
			{
				$out = -1;
				$msg = 'ورودی کالا کمتر از خروجی آن است.';
			}
			else if(!$kala_hast)
			{
				$out = 0;
				$msg = 'کالا در انبار موجود نیست.';
			}
			return(array('out'=>$out,'msg'=>$msg,'kala_hast'=>$kala_hast));
		}
		public function loadByFactorId($factor_id)
		{
			$factor_id = (int)$factor_id;
			$out = null;
			mysql_class::ex_sql("select `id` from `anbar_det` where `anbar_factor_id` = $factor_id and `en` = 0",$q);
			while($r = mysql_fetch_array($q))
				$out[] = new anbar_det_class((int)$r['id']);
			return($out);
		}
                public function khorooj($anbar_id,$anbar_typ_id,$anbar_factor_id,$kala_id,$tedad,$user_id,$other_user_id,$ghimat,$tarikh='')
                {
                        $tarikh = ($tarikh=='') ? date("Y-m-d H:i:s") : $tarikh;
                        mysql_class::ex_sqlx("insert into `anbar_det` (`anbar_id`,`anbar_typ_id`,`anbar_factor_id`,`kala_id`,`tedad`,`user_id`,`other_user_id`,`ghimat`,`tarikh`) values ($anbar_id,$anbar_typ_id,$anbar_factor_id,$kala_id,$tedad,$user_id,$other_user_id,$ghimat,'$tarikh') ");
                        
                }
	}
?>
