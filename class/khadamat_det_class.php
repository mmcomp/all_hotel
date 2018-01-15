<?php
	class khadamat_det_class
	{
		public $id=-1;
		public $khadamat_id=-1;
		public $ghimat=0;
		public $reserve_id=0;
		public $tarikh=-1;
		public $tedad=1;
		public $isUsed = 0;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `khadamat_det` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->khadamat_id=$r['khadamat_id'];
				$this->ghimat=$r['ghimat'];
				$this->reserve_id=$r['reserve_id'];
				$this->tarikh=$r['tarikh'];
				$this->tedad=$r['tedad'];
				$this->isUsed=$r['isUsed'];
			}
		}
		public function hasCopon($sandogh_id,$reserve_id,$getId = FALSE)
		{
			if($getId)
				$out = array();
			else
				$out = FALSE;
                        $reserve_id=(int)$reserve_id;
			$dat = date("Y-m-d");
			$san = new sandogh_class((int)$sandogh_id);
                        if($reserve_id != 0)
                        {
				foreach($san->khadamat as $id=>$name)
				{
					$q = null;
					mysql_class::ex_sql("select `id` from `khadamat_det` where `reserve_id` = $reserve_id and `isUsed` = 0 and date(`tarikh`) = '$dat' and  `khadamat_id` = $id",$q);
					if($r = mysql_fetch_array($q))
					{
						if($getId)
							$out[] = (int)$r['id'];
						else
							$out = TRUE;
					}
				}
			}
			return($out);
		}
		public function createCopon($sandogh_id,$reserve_id,$room_id,$user_id,$khid,$room_id=-1)
		{
			
			$out = array("factor_shomare"=>-1,"id"=>array());
			$reserve_id=(int)$reserve_id;
			if($reserve_id != 0)
                        {
				$shomare_factor = sandogh_factor_class::getShomareFactor();
                                mysql_class::ex_sql("select * from `khadamat_det_front` where `khadamat_det_id`=$khid and `tedad_used` < `tedad_kol` and `room_id`='$room_id'",$q);
                                while($r = mysql_fetch_array($q))
				{
					
					$sandogh_item_id = (int)$r['sandogh_item_id'];
					$room_id = (int)$r['room_id'];
					$sand = new sandogh_item_class($sandogh_item_id);
					$tedad_remain = (int)$r['tedad_kol']-(int)$r['tedad_used'];
					
					$ln = mysql_class::ex_sqlx("insert into `sandogh_factor` (`reserve_id`, `room_id`, `sandogh_item_id`, `toz`, `tedad`, `mablagh`, `factor_shomare`, `en`, `typ`, `user_id`, `tarikh`) values ($reserve_id,$room_id,$sandogh_item_id,'','$tedad_remain',".$sand->mablagh_det.",$shomare_factor,0,1,$user_id,'".date("Y-m-d")."')",FALSE);
					
					$id = mysql_insert_id($ln);
					mysql_close($ln);
					$out["id"][] = $id;
				}
                        }
			$out['factor_shomare'] = $shomare_factor;
			return($out);
		}
                public function loadByReserve($reserve_id=0)
                {
                        $out = FALSE;
                        $reserve_id=(int)$reserve_id;
			if($reserve_id != 0)
			{
                        	mysql_class::ex_sql("select *,`tedad` as `kc` from `khadamat_det` where `reserve_id` = $reserve_id group by `khadamat_id`",$q);
                	        while($r = mysql_fetch_array($q))
        	                {
	                                $this->id=$r['id'];
                                	$this->khadamat_id=(int)$r['khadamat_id'];
                        	        $this->ghimat=(int)$r['ghimat'];
                	                $this->reserve_id=(int)$r['reserve_id'];
					$tmpk = new khadamat_class($this->khadamat_id);
					$id = $this->khadamat_id;
					$voroodi = FALSE;
					$khorooji = FALSE;
					$tars = array();
					$qt = null;
					$isMotefareghe = khadamat_class::isMotefareghe($id);
					//$gashtAst = '';
					/*mysql_class::ex_sql("select `gashtAst` from `khadamat` where `id` = $id",$q_id);
					if($r_id = mysql_fetch_array($q_id))
					{
						$gashtAst = $r_id['gashtAst'];
					}*/
					mysql_class::ex_sql("select `tarikh` from `khadamat_det` where `reserve_id` = $reserve_id and `khadamat_id` = ".$r['khadamat_id']." order by `tarikh`",$qt);
					while($rr = mysql_fetch_array($qt))
					{
						$tm = explode(' ',$rr['tarikh']);
						$tm = $tm[0];
						$tars[] = $tm;
					}
					$qt = null;
					mysql_class::ex_sql("select `aztarikh` , `tatarikh` from `room_det` where `reserve_id` = $reserve_id",$qt);
					if($rr = mysql_fetch_array($qt))
					{
						$aztarikh = explode(' ',$rr['aztarikh']);
						$aztarikh = $aztarikh[0];
						$tatarikh = explode(' ',$rr['tatarikh']);
                        	                $tatarikh = $tatarikh[0];
//echo $gashtAst;
						if(count($tars)>=2)
						{
							if($tars[0] == $aztarikh && $tmpk->voroodi_darad)
								$voroodi = TRUE;
							if($tars[count($tars)-1] == $tatarikh && $tmpk->khorooji_darad)
								$khorooji = TRUE;
						}

						else if(count($tars)==1)
						{
							if ($isMotefareghe)
							{
								if(($tars[0] >= $aztarikh && $tmpk->voroodi_darad))
                                                	        	$voroodi = TRUE;
							}
							else
							{
								if(($tars[0] == $aztarikh && $tmpk->voroodi_darad))
		                                        	        $voroodi = TRUE;
								else if(($tars[0] == $tatarikh && $tmpk->khorooji_darad))
									$khorooji = TRUE;
								else
									echo '';
							}
						}
						else
							echo '';
					}
        	                        $out[] = array('khadamat_id'=>$this->khadamat_id,'count'=>$r['kc'],'voroodi'=>$voroodi,'khorooji'=>$khorooji);
	                        }
			}
                        return($out);
                }
		public function loadByReserve_habibi($reserve_id=0)
                {
                        $out = FALSE;
                        $reserve_id=(int)$reserve_id;
			if($reserve_id != 0)
			{
                        	mysql_class::ex_sql("select *,`tedad` as `kc` from `khadamat_det` where `reserve_id` = $reserve_id group by `khadamat_id`",$q);
                	        while($r = mysql_fetch_array($q))
        	                {
	                                $id=$r['id'];
                                	$khadamat_id=(int)$r['khadamat_id'];
                        	        $ghimat=(int)$r['ghimat'];
                	                $reserve_id=(int)$r['reserve_id'];
					$tmpk = new khadamat_class($khadamat_id);
					$voroodi = FALSE;
					$khorooji = FALSE;
					$tars = array();
					$qt = null;
					mysql_class::ex_sql("select `tarikh` from `khadamat_det` where `reserve_id` = $reserve_id and `khadamat_id` = ".$r['khadamat_id']." order by `tarikh`",$qt);
					while($rr = mysql_fetch_array($qt))
					{
						$tm = explode(' ',$rr['tarikh']);
						$tm = $tm[0];
						$tars[] = $tm;
					}
					$qt = null;
					mysql_class::ex_sql("select min(`aztarikh`) as `az` , max(`tatarikh`) as `ta` from `room_det` where `reserve_id` = $reserve_id",$qt);
					if($rr = mysql_fetch_array($qt))
					{
						$aztarikh = explode(' ',$rr['az']);
						$aztarikh = $aztarikh[0];
						$tatarikh = explode(' ',$rr['ta']);
                        	                $tatarikh = $tatarikh[0];
						if(count($tars)>=2)
						{
							if($tars[0] == $aztarikh && $tmpk->voroodi_darad)
								$voroodi = TRUE;
							if($tars[count($tars)-1] == $tatarikh && $tmpk->khorooji_darad)
								$khorooji = TRUE;
						}
						else if(count($tars)==1)
						{
							if($tars[0] == $aztarikh && $tmpk->voroodi_darad)
                                                	        $voroodi = TRUE;
							else if($tars[0] == $tatarikh && $tmpk->khorooji_darad)
								$khorooji = TRUE;
						}
					}
        	                        $out[] = array('khadamat_id'=>$khadamat_id,'count'=>$r['kc'],'voroodi'=>$voroodi,'khorooji'=>$khorooji);
	                        }
			}
                        return($out);
                }
		public function loadIdByReserve($reserve_id=0)
                {
                        $out = '';
                        $reserve_id=(int)$reserve_id;
			if($reserve_id != 0)
			{
                        	mysql_class::ex_sql("select `id` from `khadamat_det` where `reserve_id` = $reserve_id ",$q);
                	        while($r = mysql_fetch_array($q))
					$out .=($out==''? '':',' )."'".(int)$r['id']."'";
			}
                        return($out);
                }
	}
?>
