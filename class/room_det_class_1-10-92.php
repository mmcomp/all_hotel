<?php
	class room_det_class
	{
		public $id=-1;
		public $room_id=-1;
		public $room_typ=-1;
		public $aztarikh=-1;
		public $tatarikh=-1;
		public $user_id=-1;
		public $reserve_id=-1;
		public $ghimat = 0;
		public $nafar = 0;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `room_det` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->room_id=$r['room_id'];
				$this->aztarikh=$r['aztarikh'];
				$this->tatarikh=$r['tatarikh'];
				$this->ghimat=$r['ghimat'];
				$this->user_id=$r['user_id'];
				$this->reserve_id=$r['reserve_id'];
				$this->nafar = $r['nafar'];
				$room = new room_class((int)$r['room_id']);
				$this->room_typ = $room->room_typ_id;
			}
		}
		
		public function loadByReserve($reserve_id = 0)
		{
			$reserve_id = (int)$reserve_id;
			$out = FALSE;
			$editable = TRUE;
			if($reserve_id != 0)
			{
				mysql_class::ex_sql("select `aztarikh` from `room_det` where `reserve_id` = $reserve_id group by `reserve_id`,`aztarikh` ",$q);
				if (mysql_num_rows($q)>1)
					$editable = FALSE;
				$q = null;
				mysql_class::ex_sql("select * from `room_det` where `reserve_id` = $reserve_id",$q);
        	                while($r = mysql_fetch_array($q))
                	        {
					$tmp = new room_det_class;
                                	$tmp->id=$r['id'];
	                                $tmp->room_id=$r['room_id'];
        	                        $tmp->aztarikh=$r['aztarikh'];
                	                $tmp->tatarikh=$r['tatarikh'];
                        	        $tmp->ghimat=$r['ghimat'];
                                	$tmp->user_id=$r['user_id'];
	                                $tmp->reserve_id=$r['reserve_id'];
        	                        $tmp->nafar = $r['nafar'];
                	                $room = new room_class((int)$r['room_id']);
                        	        $tmp->room_typ = $room->room_typ_id;
					$out[] = $tmp;
	                        }
			}
			else
				$editable = FALSE;
			return(array($out,$editable));
		}
		public function loadByReserve_habibi($reserve_id = 0,$room_id = 0)
		{
			$out = '';
			$reserve_id = (int)$reserve_id;
			$room_id = (int)$room_id;
			if($reserve_id != 0)
			{
				$q = null;
				mysql_class::ex_sql("select * from `room_det` where `reserve_id` = '$reserve_id' and `room_id`='$room_id'",$q);
        	                while($r = mysql_fetch_array($q))
                	        {
					$out[0] = $r['aztarikh'];
					$out[1] = $r['tatarikh'];
	                        }
			}
			return($out);
		}
		public function deleteReserve($reserve_id,$sanad,$rooms=TRUE)
		{
			$reserve_id = (int)$reserve_id;
			mysql_class::ex_sqlx('delete from `hotel_reserve` where `reserve_id` = '.$reserve_id);
			if($rooms)
				mysql_class::ex_sqlx('delete from `room_det` where `reserve_id` = '.$reserve_id);
			mysql_class::ex_sqlx('delete from `khadamat_det` where `reserve_id` = '.$reserve_id);
			if($sanad)
			{
				mysql_class::ex_sqlx('delete from `sanad` where `id` in (select `sanad_record` from `sanad_reserve` where `reserve_id` = '.$reserve_id.')');
				mysql_class::ex_sqlx('delete from `sanad_reserve` where `reserve_id` = '.$reserve_id);
			}
		}
		public function loadNameByReserve($res)
		{
			$out = '';
			$reserve_id = (int)$res;
			if($reserve_id != 0)
			{
				$q = null;
				mysql_class::ex_sql("select `fname`,`lname` from `mehman` where `reserve_id` = '$reserve_id'",$q);
        	                if($r = mysql_fetch_array($q))
					$out = $r['fname'].' '.$r['lname'];
				else
					$out = '--';
			}
			return($out);
		}
		public function loadTypByReserve($res,$tarikh)
		{
			$out = '';
			
			$reserve_id = (int)$res;
			if($reserve_id != 0)
			{
				$q = null;
				mysql_class::ex_sql("select `aztarikh`,`tatarikh` from `room_det` where `reserve_id` = '$reserve_id' group by `reserve_id`",$q);
        	                if($r = mysql_fetch_array($q))
				{
					if (date("Y-m-d",strtotime($r['aztarikh']))==$tarikh)
						$out = '(ورودی)';
					elseif (date("Y-m-d",strtotime($r['tatarikh']))==$tarikh)
						$out = '(خروجی)';
					else
						$out = '--';
				}
				else
					$out = '--';
			}
			return($out);
		}
		public function combArray($arr1,$arr2)
		{
			$out = array();
			foreach($arr1 as $cell)
				$out[] = $cell;
			foreach($arr2 as $cell)
				$out[] = $cell;
			return($out);
		}
		public function reReserve($reserve_id,$hotel_id,$ajans_id,$room_typ_id,$ghimat,$startDate,$delay,$tedad,$voroodi,$khorooji,$nafar,$khadamat=null)
		{
			$conf = new conf;
			$rooms = array();
			$tedad = (int)$tedad;
			$delay = (int)$delay;
			$hotel_id = (int)$hotel_id;
			$ajans_id = (int)$ajans_id;
			$reserve_id = (int)$reserve_id;
			$reserve = new reserve_class($reserve_id);
			$tour = FALSE;
			$limitChange = FALSE;
			$pre_tour = (($reserve->hotel_reserve->m_belit==0)?FALSE:TRUE);
			if(is_array($ghimat))
			{
				$tour = TRUE;
                                $ghimat_tour = audit_class::perToEn($ghimat['ghimat_tour']);
                                $ghimat_blit1 = audit_class::perToEn($ghimat['ghimat_belit1']);
                                $other_id = $ghimat['other_moeen_id1'];
                                $other_kol_id = $ghimat['other_kol_id1'];
                                $ghimat_blit2 = audit_class::perToEn($ghimat['ghimat_belit2']);
                                $other_id2 = $ghimat['other_moeen_id2'];
                                $other_kol_id2 = $ghimat['other_kol_id2'];
				$ghimat_blit3 = audit_class::perToEn($ghimat['ghimat_belit3']);
                                $other_id3 = $ghimat['other_moeen_id3'];
                                $other_kol_id3 = $ghimat['other_kol_id3'];
                                $ghimat = $ghimat_tour - ($ghimat_blit1+$ghimat_blit2+$ghimat_blit3);
				$other_id_arr = array('ghimat_belit1'=>$ghimat_blit1,'other_moeen_id1'=>$other_id,'other_kol_id1'=>$other_kol_id,'ghimat_belit2'=>$ghimat_blit2,'other_moeen_id2'=>$other_id2,'other_kol_id2'=>$other_kol_id2,'ghimat_belit3'=>$ghimat_blit3,'other_moeen_id3'=>$other_id3,'other_kol_id3'=>$other_kol_id3);
			}
			else
			{
				$ghimat_tour = audit_class::perToEn($ghimat);
				$ghimat_blit1 = 0;
				$other_id = -1;
				$other_kol_id = -1;
				$ghimat_blit2 = 0;
                                $other_id2 = -1;
                                $other_kol_id2 = -1;
                                $ghimat_blit3 = 0;
                                $other_id3 = -1;
                                $other_kol_id3 = -1;
			}
			//$ghimat_darad = FALSE;
//			if($ghimat_tour != 0 || $ghimat_blit1 != 0 || $ghimat_blit2 != 0 || $ghimat_blit3 != 0)			
			$limitChange = conf::limitChange();
			$ghimat_darad = TRUE;
			$endDate = date("Y-m-d 00:00:00",strtotime($startDate." + $delay days"));
                        if($voroodi)
                                $startDate = date("Y-m-d 00:00:00",strtotime($startDate));
			else
				$startDate = date("Y-m-d 14:00:00",strtotime($startDate));
                        if($khorooji)
                                $endDate = date("Y-m-d 21:00:00",strtotime($endDate));	
			else
				$endDate = date("Y-m-d 14:00:00",strtotime($endDate));
                        $limit_day = TRUE;
                        if(jdate("m/d",strtotime($startDate)) == audit_class::enToPer($conf->limitDate) || jdate("m/d",strtotime($endDate)) == audit_class::enToPer($conf->limitDate))
                                $limit_day = FALSE;
			$user_id = (int)$_SESSION["user_id"];
			$out = FALSE;
			$hotel = new hotel_class($hotel_id);
			$room_count = 1;
			$out = FALSE;
			$last_tedad = 0;
			$room_ok = (is_array($room_typ_id) && (int)$room_typ_id[0]>0);
			$r_det = room_det_class::loadByReserve($reserve->id);
			$r_det = $r_det[0];
                        if($room_ok)
                                for($j = 0;$j < count($room_typ_id);$j++)
				{
					$rm = new room_class($room_typ_id[$j]);
					$shi = room_det_class::roomIdAvailable($room_typ_id[$j],$startDate,$endDate);
                                        if($shi!=null)
					{
						$is_last = FALSE;
						for($sag = 0;$sag < count($r_det);$sag++)
						{
							if($room_typ_id[$j] == $r_det[$sag]->room_id)
							{
								$is_last = TRUE;
								$last_tedad++;
							}
						}
						if ($limitChange=="1")
						{//echo 'limited';
							if(!$is_last)
			                                        $room_ok = FALSE;
							//else if(in_array($reserve->id,$shi) && $room_ok)
							else if(in_array($reserve->id,$shi) && $room_ok and sizeof($shi)<2)
							{
								if(in_array($reserve->id,$shi) && $room_ok and sizeof($shi)>=2)
									$bug = bug_reserve_class::insertReserve($user_id,$reserve_id);
								$room_ok = TRUE;
							}
							else
								$room_ok = FALSE;
						}
						else
						{//echo 'NoLimited';
							if(!$is_last)
			                                        $room_ok = FALSE;
							else if(in_array($reserve->id,$shi) && $room_ok)
							//else if(in_array($reserve->id,$shi) && $room_ok and sizeof($shi)<2)
							{
								if(in_array($reserve->id,$shi) && $room_ok and sizeof($shi)>=2)
									$bug = bug_reserve_class::insertReserve($user_id,$reserve_id);
								$room_ok = TRUE;
							}
							else
								$room_ok = FALSE;
						}
					}
				}
			//echo "h = ".var_export($hotel->hotelAvailableBetween($startDate,$endDate),TRUE).",gh = ".var_export($ghimat_darad,TRUE).",del = $delay,roomok = ".var_export($room_ok,TRUE).",limit_day=".var_export($limit_day,TRUE);
			if($hotel->hotelAvailableBetween($startDate,$endDate) && $ghimat_darad && $delay>0 && $room_ok && $limit_day)
			{
				$last_room_typ_id = $reserve->room_det[0]->room_id;
                                //$last_tedad = (($last_room_typ_id == $room_typ_id)?count($reserve->room_det):0);
                                if(is_array($room_typ_id))
                                {
                                        $rooms = $room_typ_id;
                                        $tedad = count($rooms)+$last_tedad;
                                }
                                else
                                {
					$rooms = room_class::loadOpenRoomArray($startDate,$delay,$voroodi,$khorooji,$hotel_id,$room_typ_id);
				}
				$out = array();
				$tedad -= $last_tedad;
				if($tedad <= count($rooms))
				{
					$lother_id = room_det_class::getOthers($reserve_id);
/*
					if(!is_array($lother_id) || count($lother_id)!=6)
						$lother_id = array(-1,-1,-1,-1,-1,-1);
*/
					$reserve_id = (int)$reserve_id;
					room_det_class::deleteReserve($reserve_id,FALSE,$reserve->editable);
					if(!is_array($room_typ_id))
						$rooms = room_class::loadOpenRoomArray($startDate,$delay,$voroodi,$khorooji,$hotel_id,$room_typ_id);
					//----------------KHADAMAT-------------------
					$khadamat_ghimat = 0;
					if(is_array($khadamat))
					{
						for($ind = 0;$ind < count($khadamat);$ind++)
						{
							room_det_class::sabtKhadamat($hotel_id,$reserve_id,$ajans_id,$khadamat[$ind]["id"],$khadamat[$ind]["ghimat"],$khadamat[$ind]["tedad"],$khadamat[$ind]["voroodi"],$khadamat[$ind]["khorooji"],$startDate,$endDate);
							$khadamat_ghimat += (int)$khadamat[$ind]["ghimat"];
						}
					}
					//-------------------------------------------
					for($i = 0;$i < $tedad && $reserve->editable;$i++)
						if((int)$rooms[$i]>0)
							mysql_class::ex_sqlx("insert into `room_det` (`room_id`, `aztarikh`, `tatarikh`, `user_id` , `reserve_id` , `ghimat` ,`nafar`) values ('".$rooms[$i]."','$startDate','$endDate','$user_id',$reserve_id,".(int)($ghimat/(($tedad+$last_tedad)*$delay)).",$nafar)");
					if(!$reserve->editable)
						mysql_class::ex_sqlx("update `room_det` set `nafar` = $nafar where `reserve_id` = $reserve_id");
					$shomare_sanad = array();
					$toz = '';
					$sanad_record_sabti =array();
					$mabchanged = ($reserve->hotel_reserve->m_hotel!=($ghimat_tour-($ghimat_blit1+$ghimat_blit2))) || ($reserve->hotel_reserve->m_belit1!=$ghimat_blit1 || $reserve->hotel_reserve->m_belit2!=$ghimat_blit2);
					$otherchanged = TRUE;
					if($lother_id[0]==$other_id && $lother_id[2] ==$other_id2 && $lother_id[4] ==$other_id3)
						$otherchanged = FALSE;
					$mabchanged = ($mabchanged || $otherchanged);
                                    //    if($mabchanged)
                                   //     {
                                                $senad_record_ids = new sanad_reserve_class($reserve_id);
                                                $senad_record_ids = $senad_record_ids->sanad_record;
                                                mysql_class::ex_sqlx("delete from `sanad_reserve` where `reserve_id`='$reserve_id'");
                                                //---------------ﺡﺬﻓ ﺍﺯ ﺝﺩﻮﻟ sanad
                                                $q = null;
                                                $shomare_sanad_sabti = -1;
                                                if(count($senad_record_ids)>0)
                                                {
                                                        mysql_class::ex_sql("select `shomare_sanad` from `sanad` where `id`=".$senad_record_ids[0],$q);
                                                        if($r=mysql_fetch_array($q))
                                                                $shomare_sanad_sabti = (int)$r['shomare_sanad'];
                                                }

                                                $senad_record_ids = implode(",",$senad_record_ids);
                                                mysql_class::ex_sqlx("delete from `sanad` where `id` in ($senad_record_ids)");
                                                if($tour)
                                                        $sanad_record_sabti = sanadzan_class::newTourReserveSanad($hotel_id,$ajans_id,array($other_id,$other_id2),array($other_kol_id,$other_kol_id2),$ghimat_tour,array($ghimat_blit1,$ghimat_blit2),$shomare_sanad_sabti,$toz);
                                                else
							{
                                                        $sanad_record_sabti = sanadzan_class::newHotelReserveSanad($hotel_id,$ajans_id,$ghimat,$shomare_sanad_sabti,$toz);
                                                $shomare_sanad = $sanad_record_sabti;}
                                      //  }
					$out = array("shomare_sanad"=>$shomare_sanad,"reserve_id"=>$reserve_id);
					
				}
			}
			return($out);
		}
		public function roomIdAvailable($room_id,$startDate,$endDate)
		{
			$out = null;
			$q = null;
			mysql_class::ex_sql("select `id`,`reserve_id` from `room_det` where `reserve_id`>0 and `room_id` = $room_id and ((`aztarikh` < '$startDate' and `tatarikh` > '$startDate') or (`aztarikh` < '$endDate' and `tatarikh` > '$endDate') or (`aztarikh` >= '$startDate' and `tatarikh` <= '$endDate'))",$q);
//echo "select `id`,`reserve_id` from `room_det` where `reserve_id`>0 and `room_id` = $room_id and ((`aztarikh` < '$startDate' and `tatarikh` > '$startDate') or (`aztarikh` < '$endDate' and `tatarikh` > '$endDate') or (`aztarikh` >= '$startDate' and `tatarikh` <= '$endDate'))".'<br/>';
			while($r = mysql_fetch_array($q))
				$out[] = $r['reserve_id'];
			return($out);
		}
		public function preReserve($hotel_id,$ajans_id,$room_typ_id,$ghimat,$startDate,$delay,$tedad,$voroodi,$khorooji,$nafar,$khadamat,$user_id=-1)
		{
			$conf = new conf;
			$rooms = array();
			$tedad = (int)$tedad;
			$delay = (int)$delay;
			$hotel_id = (int)$hotel_id;
			$ajans_id = (int)$ajans_id;
			$tour = FALSE;
			$other_id_arr = array();
			/*$ta_gasht1 = ((isset($_REQUEST['ta_gasht']))?audit_class::hamed_pdateBack($ta_gasht):'');
			$tmp_gasht = explode(' ',$ta_gasht1);
			$ta_gasht = $tmp_gasht[0];
			$ta_axe1 = ((isset($_REQUEST['ta_axe']))?audit_class::hamed_pdateBack($ta_axe):'');
			$tmp_axe = explode(' ',$ta_axe1);
			$ta_axe = $tmp_axe[0];*/
                        if(is_array($ghimat))
                        {
                                $tour = TRUE;
                                $ghimat_tour = audit_class::perToEn($ghimat['ghimat_tour']);
                                $ghimat_blit1 = audit_class::perToEn($ghimat['ghimat_belit1']);
                                $other_id = $ghimat['other_moeen_id1'];
                                $other_kol_id = $ghimat['other_kol_id1'];
                                $ghimat_blit2 = audit_class::perToEn($ghimat['ghimat_belit2']);
                                $other_id2 = $ghimat['other_moeen_id2'];
                                $other_kol_id2 = $ghimat['other_kol_id2'];
                                $ghimat_blit3 = audit_class::perToEn($ghimat['ghimat_belit3']);
                                $other_id3 = $ghimat['other_moeen_id3'];
                                $other_kol_id3 = $ghimat['other_kol_id3'];
                                $ghimat = $ghimat_tour - ($ghimat_blit1+$ghimat_blit2+$ghimat_blit3);
				$other_id_arr = array('ghimat_belit1'=>$ghimat_blit1,'other_moeen_id1'=>$other_id,'other_kol_id1'=>$other_kol_id,'ghimat_belit2'=>$ghimat_blit2,'other_moeen_id2'=>$other_id2,'other_kol_id2'=>$other_kol_id2,'ghimat_belit3'=>$ghimat_blit3,'other_moeen_id3'=>$other_id3,'other_kol_id3'=>$other_kol_id3);
                        }
                        else
                        {
                                $ghimat_tour = audit_class::perToEn($ghimat);
                                $ghimat_blit1 = 0;
                                $other_id = -1;
                                $other_kol_id = -1;
                                $ghimat_blit2 = 0;
                                $other_id2 = -1;
                                $other_kol_id2 = -1;
				$ghimat_blit3 = 0;
                                $other_id3 = -1;
                                $other_kol_id3 = -1;
                        }
			$ghimat_darad = FALSE;
                        if($ghimat_tour != 0 || $ghimat_blit1 != 0 || $ghimat_blit2 != 0 || $ghimat_blit3 != 0)
                                $ghimat_darad = TRUE;
			$endDate = date("Y-m-d H:i:s",strtotime($startDate." + $delay days"));
                        if($voroodi)
                                $startDate = date("Y-m-d 00:00:00",strtotime($startDate));
			else
				$startDate = date("Y-m-d 14:00:00",strtotime($startDate));
                        if($khorooji)
                                $endDate = date("Y-m-d 21:00:00",strtotime($endDate));	
			else
				$endDate = date("Y-m-d 14:00:00",strtotime($endDate));
                        $limit_day = TRUE;
                        if(jdate("m/d",strtotime($startDate)) == audit_class::enToPer($conf->limitDate) || jdate("m/d",strtotime($endDate)) == audit_class::enToPer($conf->limitDate))
                                $limit_day = FALSE;
			$user_id = $user_id <= 0 ? (int)$_SESSION["user_id"] : $user_id;
			$out = FALSE;
			$hotel = new hotel_class($hotel_id);
			$room_count = 1;
			$room_ok = (is_array($room_typ_id) && (int)$room_typ_id[0]>0);
			if($room_ok)
				for($j = 0;$j < count($room_typ_id);$j++)
				{
					$ttt = room_det_class::roomIdAvailable($room_typ_id[$j],$startDate,$endDate);
					if($ttt!=null)
						$room_ok = FALSE;
				}
			
			if($hotel->hotelAvailableBetween($startDate,$endDate) && $ghimat_darad && $delay>=0 && $room_ok && $limit_day)
			{
				if(is_array($room_typ_id))
				{
					$rooms = $room_typ_id;
					$tedad = count($rooms);
				}
				else
				{
					$rooms = room_class::loadOpenRoomArray($startDate,$delay,$voroodi,$khorooji,$hotel_id,$room_typ_id);
				}
				$out = array();
				if($tedad <= count($rooms))
				{
					$reserve_id = 0;
					$q = null;
					mysql_class::ex_sql("select MAX(abs(`reserve_id`)) as `mrs` from `room_det` ",$q);
					if($r = mysql_fetch_array($q))
						$reserve_id = (int)$r["mrs"];
					$reserve_id++;
					$reserve_id =(($reserve_id<=0)?1:$reserve_id);
					if($tour)
						$shomare_sanad = sanadzan_class::newTourReserveSanad($hotel_id,$ajans_id,array($other_id,$other_id2,$other_id3),array($other_kol_id,$other_kol_id2,$other_kol_id3),$ghimat_tour,array($ghimat_blit1,$ghimat_blit2,$ghimat_blit3),-1);
					else
						$shomare_sanad = sanadzan_class::newHotelReserveSanad($hotel_id,$ajans_id,$ghimat,-1,'',$user_id);
					for($i = 0;$i < $tedad;$i++)
					{
						$gh_tmp=0;
						if($delay!=0)
							$gh_tmp = (int)($ghimat/($tedad*$delay));
/*
						$conf = new conf;
						if($conf->front_office_enabled && $i == 0)
							sanadzan_class::roomReserve($rooms[$i],$startDate,$reserve_id,$user_id,$ghimat);
*/
						mysql_class::ex_sqlx("insert into `room_det` (`room_id`, `aztarikh`, `tatarikh`, `user_id` , `reserve_id` , `ghimat` ,`nafar`) values ('".$rooms[$i]."','$startDate','$endDate','$user_id',$reserve_id,$gh_tmp,$nafar)");
					}
					//----------------KHADAMAT-------------------
					$khadamat_ghimat = 0;
					if(is_array($khadamat))
					{
						for($ind = 0;$ind < count($khadamat);$ind++)
						{
							room_det_class::sabtKhadamat($hotel_id,$reserve_id,$ajans_id,$khadamat[$ind]["id"],$khadamat[$ind]["ghimat"],$khadamat[$ind]["tedad"],$khadamat[$ind]["voroodi"],$khadamat[$ind]["khorooji"],$startDate,$endDate);
							$khadamat_ghimat += (int)$khadamat[$ind]["ghimat"];
						}
					}
					//-------------------------------------------
					$out = array("shomare_sanad"=>$shomare_sanad,"reserve_id"=>$reserve_id,"other_id"=>$other_id_arr);
				}
			}
			return($out);
		}
		public function sabtKhadamat($hotel_id,$reserve_id,$ajans_id,$khadamat_id,$ghimat,$tedad,$voroodi,$khorooji,$aztarikh,$tatarikh)
		{
			//$to_date = date("Y-m-d", strtotime(' + 1 day'));
			$khadamat = new khadamat_class($khadamat_id);
			if ($khadamat->motefareghe==1)
			{
				$to_date = date('Y-m-d', strtotime($aztarikh .' +1 day'));
				$id = $khadamat->id;
				mysql_class::ex_sql("select count(`id`) as `count_id` from `khadamat_det` where `khadamat_id` = '$id' and `tarikh` = '$to_date'",$q_co);
				if($r_co = mysql_fetch_array($q_co))
					$count_id = $r_co['count_id'];
				if ($count_id>=100)
				{
					$to_date = date('Y-m-d', strtotime($aztarikh .' +2 day'));
				}
				if($voroodi && ((int)$tedad>0 || $khadamat->typ==1))
					mysql_class::ex_sqlx("insert into `khadamat_det` (`khadamat_id`,`ghimat`,`reserve_id`,`tedad`,`tarikh`) values ('$khadamat_id','$ghimat','$reserve_id','$tedad','$to_date') ");
				$tmp = strtotime($to_date);
				$endt = strtotime($to_date);
			}
			else
			{
				if($voroodi && ((int)$tedad>0 || $khadamat->typ==1))
					mysql_class::ex_sqlx("insert into `khadamat_det` (`khadamat_id`,`ghimat`,`reserve_id`,`tedad`,`tarikh`) values ('$khadamat_id','$ghimat','$reserve_id','$tedad','$aztarikh') ");
				$tmp = strtotime($aztarikh.' + 1 day');
				$endt = strtotime($tatarikh.' - 1 day');
			}
			while($tmp <= $endt)
			{
				if((int)$tedad>0 && $khadamat->typ==0)
					mysql_class::ex_sqlx("insert into `khadamat_det` (`khadamat_id`,`ghimat`,`reserve_id`,`tedad`,`tarikh`) values ('$khadamat_id','$ghimat','$reserve_id','$tedad','".date("Y-m-d 14:00:00",$tmp)."') ");
				$tmp = strtotime(date("Y-m-d",$tmp).' + 1 day');
			}
			$tatarikh = date("Y-m-d H:i:s",strtotime($tatarikh));
			if ($khadamat->motefareghe==1)
			{
				$id = $khadamat->id;
				mysql_class::ex_sql("select count(`id`) as `count_id` from `khadamat_det` where `khadamat_id` = '$id' and `tarikh` = '$to_date'",$q_co);
				if($r_co = mysql_fetch_array($q_co))
					$count_id = $r_co['count_id'];
				if ($count_id>=100)
				{
					$to_date = date("Y-m-d", strtotime(' + 2 day'));
				}
				if($khorooji && ((int)$tedad>0 || $khadamat->typ==1))
		                        mysql_class::ex_sqlx("insert into `khadamat_det` (`khadamat_id`,`ghimat`,`reserve_id`,`tedad`,`tarikh`) values ('$khadamat_id','$ghimat','$reserve_id','$tedad','$to_date') ");
			}
			else
			{
				if($khorooji && ((int)$tedad>0 || $khadamat->typ==1))
		                        mysql_class::ex_sqlx("insert into `khadamat_det` (`khadamat_id`,`ghimat`,`reserve_id`,`tedad`,`tarikh`) values ('$khadamat_id','$ghimat','$reserve_id','$tedad','$tatarikh') ");
			}
			$name_khadamat = khadamat_class::loadKhadamat_name($khadamat_id);
			if ($name_khadamat=='نهار')
			{
				mysql_class::ex_sql("select `id` from `khadamat_det` where `khadamat_id` = '$khadamat_id' and `reserve_id` = '$reserve_id' and `tarikh`='$aztarikh'",$q_tmp);
				if($r_tmp = mysql_fetch_array($q_tmp))
				{
					$kh_id = $r_tmp['id'];
					mysql_class::ex_sql("select `id` from `sandogh_item` where  `name` LIKE '%چلو جوجه%'",$q_sKh);
					if($r_sKh = mysql_fetch_array($q_sKh))
						$sa_id = $r_sKh['id'];
					$room = room_det_class::loadDetByReserve_id($reserve_id);
					for($j=0;$j<count($room['rooms']);$j++)
					{
						$room_id = $room['rooms'][$j]['room_id'];
						mysql_class::ex_sqlx("insert into `khadamat_det_front` (`khadamat_det_id`, `sandogh_item_id`, `tedad_kol`, `tedad_used`, `room_id`) values ('$kh_id','$sa_id','$tedad','0','$room_id') ");
					}
				}				
			}
		}
		public function sabtReserveHotel($reserve_id,$shomare_sanad,$other_ids,$fname,$lname,$toz,$ajans_id,$mhotel,$time_now,$isAdd=TRUE)
		{
			if($time_now==null || $time_now=='')
				$time_now = date("Y-m-d H:i:s");
			$mblit1 = 0;
			$mblit2 = 0;
			$mblit3 = 0;
			$other_id_sabti = null;
			if(is_array($other_ids))
			{
				$other_id_sabti['other_moeen_id1'] = $other_ids['other_moeen_id1'];
				$other_id_sabti['other_kol_id1'] = $other_ids['other_kol_id1'];
				$other_id_sabti['other_moeen_id2'] = $other_ids['other_moeen_id2'];
				$other_id_sabti['other_kol_id2'] = $other_ids['other_kol_id2'];
				$other_id_sabti['other_moeen_id3'] = $other_ids['other_moeen_id3'];
				$other_id_sabti['other_kol_id3'] = $other_ids['other_kol_id3'];
				$other_id_sabti  = serialize($other_id_sabti);
				$mblit1 =(int)$other_ids['ghimat_belit1'];
				$mblit2 =(int)$other_ids['ghimat_belit2'];
				$mblit3 =(int)$other_ids['ghimat_belit3'];
			}
			if(is_array($toz) && count($toz)==2)
			{
				$extra_toz = $toz['extra_toz'];
				$toz = $toz['toz'];
			}
			mysql_class::ex_sqlx("insert into `hotel_reserve` (`fname`,`lname`, `tozih`, `reserve_id`,`ajans_id`,`m_belit1`,`m_belit2`,`m_belit3`,`m_hotel`,`regdat`,`other_id`".((isset($extra_toz))?",`extra_toz`":"").") values ('$fname','$lname','$toz',$reserve_id,$ajans_id,$mblit1,$mblit2,$mblit3,$mhotel,'$time_now','$other_id_sabti'".((isset($extra_toz))?",'$extra_toz'":"").")");
			mysql_class::ex_sqlx("insert into `sms_vaz` (`id`, `reserve_id`, `sms_vorud`, `sms_khoroooj`, `sms_gasht`) values (NULL, '$reserve_id', '-1', '-1', '-1')");
			//mysql_class::ex_sqlx("insert into `hotel_reserve` (`fname`,`lname`, `tozih`, `reserve_id`,`ajans_id`,`m_belit1`,`m_belit2`,`m_hotel`,`regdat`,`other_id`) values ('$fname','$lname','$toz',$reserve_id,$ajans_id,$mblit1,$mblit2,$mhotel,'$time_now','$other_id_sabti')");
			$toz =  room_det_class::loadReserve($reserve_id);
			for($i = 0;$i < count($shomare_sanad) && $isAdd;$i++)
			{
				mysql_class::ex_sqlx("insert into `sanad_reserve` (`sanad_record`,`reserve_id`) values (".$shomare_sanad[$i].",$reserve_id)");
				mysql_class::ex_sqlx("update `sanad` set `tozihat`='$toz' where `id` =".$shomare_sanad[$i]);
			}
		}
                public function sabtOnlineReserveHotel($reserve_id,$shomare_sanad,$other_ids,$fname,$lname,$toz,$ajans_id,$mhotel,$time_now,$isAdd=TRUE)
                {
                        if($time_now==null || $time_now=='')
                                $time_now = date("Y-m-d H:i:s");
                        $mblit1 = 0;
                        $mblit2 = 0;
                        $other_id_sabti = null;
                        if(is_array($other_ids))
                        {
                                $other_id_sabti['other_moeen_id1'] = $other_ids['other_moeen_id1'];
                                $other_id_sabti['other_kol_id1'] = $other_ids['other_kol_id1'];
                                $other_id_sabti['other_moeen_id2'] = $other_ids['other_moeen_id2'];
                                $other_id_sabti['other_kol_id2'] = $other_ids['other_kol_id2'];
                                $other_id_sabti  = serialize($other_id_sabti);
                                $mblit1 =(int)$other_ids['ghimat_belit1'];
                                $mblit2 =(int)$other_ids['ghimat_belit2'];
                        }
                        if(is_array($toz) && count($toz)==2)
                        {
                                $extra_toz = $toz['extra_toz'];
                                $toz = $toz['toz'];
                        }
                        mysql_class::ex_sqlx("insert into `hotel_reserve` (`fname`,`lname`, `tozih`, `reserve_id`,`ajans_id`,`m_belit1`,`m_belit2`,`m_hotel`,`regdat`,`other_id`".((isset($extra_toz))?",`extra_toz`":"").",`isOnline`) values ('$fname','$lname','$toz',$reserve_id,$ajans_id,$mblit1,$mblit2,$mhotel,'$time_now','$other_id_sabti'".((isset($extra_toz))?",'$extra_toz'":"").",1)");
			$toz =  room_det_class::loadReserve($reserve_id);
                        for($i = 0;$i < count($shomare_sanad) && $isAdd;$i++)
			{
                                mysql_class::ex_sqlx("insert into `sanad_reserve` (`sanad_record`,`reserve_id`) values (".$shomare_sanad[$i].",$reserve_id)");
				mysql_class::ex_sqlx("update `sanad` set `tozihat`=CONCAT('$toz ',`tozihat`) where `id` =".$shomare_sanad[$i]);
			}
                }
		public function loadReserve_id($aztarikh,$tatarikh,$user_id,$isAdmin,$fname,$lname,$reserve_id,$just_date=TRUE)
		{
			$out = array();
			$bigger = ($just_date)?'':'>';
			$smaller = ($just_date)?'':'<';
			$user_id = (int)$user_id;
			$aztar_shart = ($aztarikh!='0000-00-00' && $aztarikh!='' )?"AND ( DATE(`aztarikh`) $bigger= '$aztarikh')":'';
			$tatar_shart = ($tatarikh!='0000-00-00' && $tatarikh!='' )?"AND ( DATE(`tatarikh`) $smaller= '$tatarikh')":'';
			$shart = '';
			$name_shart='';
			$reserve_id_shart = ((int)$reserve_id>0)?" and `room_det`.`reserve_id` LIKE '%$reserve_id%'":'';
			if(!$isAdmin)
			{
				$user = new user_class($user_id);
				$daftar_id = $user->daftar_id;
				$hotelList=daftar_class::hotelList($daftar_id);
				if($hotelList)
					$shart='and `room_id` in (select `id` from `room` where `hotel_id` in('.implode(",",$hotelList)."))";
				if($fname != '' || $lname != '')
				{
					$arr = array();				
					mysql_class::ex_sql("select `reserve_id` from `hotel_reserve` where `lname` like '%$lname%' and '%$fname%' and `reserve_id`>0",$p);
					while($r = mysql_fetch_array($p))
						$arr[]=$r['reserve_id'];
					$arr = implode(',',$arr);
					$arr = (($arr=='')?-1:$arr);
					$name_shart = "and `hotel_reserve`.`reserve_id` in ($arr)";
				}

				//mysql_class::ex_sql("select `reserve_id`,`aztarikh`,`tatarikh` from `room_det` where (`reserve_id`>0 and `aztarikh` >= '$aztarikh' and `tatarikh` <= '$tatarikh' $name_shart ) $shart group by `reserve_id`",$q);
			}
			mysql_class::ex_sql("SELECT `hotel_reserve`.`reserve_id` , `aztarikh` , `tatarikh` 
FROM `hotel_reserve` 
LEFT JOIN `room_det` ON ( `hotel_reserve`.`reserve_id` = `room_det`.`reserve_id` ) 
WHERE (
`fname` LIKE '%$fname%'
AND `lname` LIKE '%$lname%'
)
AND `room_det`.`reserve_id` >0 $aztar_shart $tatar_shart $shart $name_shart $reserve_id_shart group by `hotel_reserve`.`reserve_id`",$q);

			while($r = mysql_fetch_array($q))
				$out[] = $r['reserve_id']; 
			return $out;
		}
		public function loadDetByReserve_id($reserve_id)
		{
			$reserve_id = (int)$reserve_id;
			$out= array();
			mysql_class::ex_sql("select sum(`ghimat`) as jam_ghimat from `room_det` where `reserve_id`=$reserve_id ",$q);
			if ($r = mysql_fetch_array($q))
				$out['ghimat'] = $r['jam_ghimat'];
			$q = null;
			mysql_class::ex_sql("select `ajans_id`,`m_hotel`,(`m_belit1`+`m_belit2`) as `m_belit` from `hotel_reserve` where `reserve_id`=$reserve_id",$q);
			if ($r = mysql_fetch_array($q))
			{
				$out['ajans_id'] =(int)$r['ajans_id'];
				$out['m_hotel'] = (int)$r['m_hotel'];
				$out['m_belit'] = (int)$r['m_belit'];
			}
			$q = null;
			$rooms = array();
			mysql_class::ex_sql("select min(`aztarikh`) as `minaz` from `room_det` where `reserve_id`=$reserve_id",$q);
			$azta = '';
			if($r = mysql_fetch_array($q))
				$azta = $r['minaz'];
			$q = null;
			$tata = '';
			mysql_class::ex_sql("select max(`tatarikh`) as `maxta` from `room_det` where `reserve_id`=$reserve_id",$q);
			if($r = mysql_fetch_array($q))
				$tata = $r['maxta'];
			$q = null;
			mysql_class::ex_sql("select date(max(`tatarikh`)) as `mta` from `room_det` where `reserve_id`=$reserve_id ",$q);
			if($r = mysql_fetch_array($q))
			{
				$mta = $r['mta'];
				$q = null;
				mysql_class::ex_sql("select `nafar`,`room_id` from `room_det` where `reserve_id`=$reserve_id and date(`tatarikh`) = '$mta'",$q);
				while($r = mysql_fetch_array($q))
				{
					$rooms['nafar'] = $r['nafar'];
					$room = new room_class($r['room_id']);
					$room_typ = new room_typ_class($room->room_typ_id);
					$hotel = new hotel_class($room->hotel_id);
					$rooms['room_typ'] = $room_typ->name;
					$rooms['hotel'] = $hotel->name;
					$rooms['hotel_id'] = $room->hotel_id;
					$rooms['room_id'] = (int)$r['room_id'];
					$rooms['aztarikh'] = $azta;//$r['aztarikh'];
					$rooms['tatarikh'] = $tata;//$r['tatarikh'];
					$out['rooms'][] = $rooms;
					$rooms = array();
				}
			}
			return $out;
		}
		public function loadNamesByReserve_id($reserve_id)
		{
			$reserve_id = (int)$reserve_id;
			$out= array();
			mysql_class::ex_sql("select `id`,`lname` from `hotel_reserve` where `reserve_id`=$reserve_id order by `id`",$q);
			while ($r = mysql_fetch_array($q))
			{
				$out[] = $r['lname'];
			}
			return $out;
		}
		public function loadKhoroojByReserve_id($reserve_id,$room_id)
		{
			$reserve_id = (int)$reserve_id;
			$room_id = (int)$room_id;
			$out= array();
			mysql_class::ex_sql("select `id`,`khorooj` from `mehman` where `reserve_id`=$reserve_id and `room_id`=$room_id order by `id`",$q);
			while ($r = mysql_fetch_array($q))
			{
				$out[] = $r['khorooj'];
			}
			return $out;
		}
		public function loadKhadamatByReserve_id($reserve_id)
		{
			$out= array();
			$reserve_id = (int)$reserve_id;
			mysql_class::ex_sql("select sum(`ghimat`) as jam_ghimat from `khadamat_det` where `reserve_id`=$reserve_id ",$q);
			if ($r = mysql_fetch_array($q))
				$out['ghimat'] = $r['jam_ghimat'];
			return $out;
		}
		public function getOthers($reserve_id)
		{
			$reserve_id = (int)$reserve_id;
			$reserve = new reserve_class($reserve_id);
			$out = $reserve->hotel_reserve->other_id;
			$out1[] = (isset($out['other_moeen_id1'])?$out['other_moeen_id1']:-1);
			$out1[] = (isset($out['other_kol_id1'])?$out['other_kol_id1']:-1);
			$out1[] = (isset($out['other_moeen_id2'])?$out['other_moeen_id2']:-1);
			$out1[] = (isset($out['other_kol_id2'])?$out['other_kol_id2']:-1);
			$out1[] = (isset($out['other_moeen_id3'])?$out['other_moeen_id3']:-1);
			$out1[] = (isset($out['other_kol_id3'])?$out['other_kol_id3']:-1);
			return $out1;
		}
		public function loadReserve($reserve_id,$reserve=null)
		{
			$out = "";
	                $reserve_id = (int)$reserve_id;
			if($reserve==null)
				$reserve = new reserve_class($reserve_id);	
			$room = new room_class($reserve->room_det[0]->room_id);
			$hotel = new hotel_class($room->hotel_id);
			$khadamat='';
			if($reserve->khadamat_det!=null)
				foreach($reserve->khadamat_det as $rk)
				{
					$kh = new khadamat_class((int)$rk['khadamat_id']);
					$khname = $kh->name;
					$khadamat .= (($khadamat=='')?' با خدمات '.$khname.' '.$rk['count']:','.$khname.' '.$rk['count']);
				}
	                $out = $reserve->hotel_reserve->lname." تعداد نفرات ".$reserve->room_det[0]->nafar." نفر از ".audit_class::hamed_pdate($reserve->room_det[0]->aztarikh)." تا ".audit_class::hamed_pdate($reserve->room_det[0]->tatarikh)." جهت ".$hotel->name." ".$khadamat." شماره رزرو $reserve_id";
			return($out);
		}
		public function Mini($arr)
		{
			$out = ((isset($arr[0]))?$arr[0]:0);
			for($i=0;$i < count($arr);$i++)
				$out = (($arr[$i]<$out)?$arr[$i]:$out);
			return($out);
		}	
		public function changeDate($r1,$r2,$jday,$res1)
		{
			$user_id = $_SESSION['user_id'];
			$out = FALSE;
			$rooms1 = array();
			$rooms2 = null;
			$jtom = date("Y-m-d H:i:s",strtotime($jday.' + 1 day'));
			$reses = room_class::getAnyReserve($jday,$r1);
			if($reses != null)
			{
				$rese1 = 0;
				foreach($reses as $ress)
					if((int)$ress['reserve_id']>0 &&reserve_class::isPaziresh($ress['reserve_id']) && $rese1 == 0)
						$rese1 = (int)$ress['reserve_id'];
				$res1 = ($rese1 > 0)?$rese1:$res1;
				$sourceOk = FALSE;
				mysql_class::ex_sql("select `id`,`reserve_id`,`room_id`,`aztarikh`,`tatarikh` from `room_det` where `room_id` = '$r1' and date(`tatarikh`)>'$jday' and date(`aztarikh`) <= '$jday' and `reserve_id` = $res1",$q);
				if($row = mysql_fetch_array($q))
				{
					$sourceOk = TRUE;
					$rooms1[] = array('id'=>$row['id'],'room_id'=>$row['room_id'],'azt'=>date("Y-m-d",strtotime($row['aztarikh'])),'tat'=>date("Y-m-d",strtotime($row['tatarikh'])),'reserve_id'=>$row['reserve_id']);
				}
				else
					return $out;
				$q = null;
				mysql_class::ex_sql("select `id`,`reserve_id`,`room_id`,`aztarikh`,`tatarikh` from `room_det` where `room_id` = '$r2' and ((date(`tatarikh`)<='".$rooms1[0]['tat']."' and date(`tatarikh`) > '$jday') or (date(`aztarikh`)<'".$rooms1[0]['tat']."' and date(`aztarikh`) >= '$jday') or (date(`aztarikh`)<'$jday' and date(`tatarikh`)>'".$rooms1[0]['tat']."')) and `reserve_id`>0",$q);
				while($row = mysql_fetch_array($q))
					$rooms2[] = array('id'=>$row['id'],'room_id'=>$row['room_id'],'azt'=>date("Y-m-d",strtotime($row['aztarikh'])),'tat'=>date("Y-m-d",strtotime($row['tatarikh'])),'reserve_id'=>$row['reserve_id']);
				if($rooms1[0]['azt']<$jday && $rooms1[0]['tat']>$jday && $sourceOk)
				{
					mysql_class::ex_sqlx("update `room_det` set `tatarikh` = '$jday' where `id` = ".$rooms1[0]['id']);
					mysql_class::ex_sqlx("insert into `room_det` (`room_id`,`reserve_id`,`aztarikh`,`tatarikh`,`user_id`) values ($r2,".$rooms1[0]['reserve_id'].",'$jday','".$rooms1[0]['tat']."',$user_id)");
					if($rooms2 == null)
						$out = TRUE;
				}
				else if($rooms1[0]['azt']==$jday && $sourceOk)
				{
					mysql_class::ex_sqlx("update `room_det` set `room_id` = $r2 where `id` = ".$rooms1[0]['id']);
					if($rooms2 == null)
						$out = TRUE;
				}
				for($i = 0;$sourceOk && $rooms2!=null && $i < count($rooms2);$i++)
				{
					if($rooms2[$i]['azt']<=$jday && $rooms2[$i]['tat']>$jday)
					{
						if($rooms2[$i]['azt']!=$jday)
						{
							mysql_class::ex_sqlx("update `room_det` set `tatarikh` = '$jday' where `id` = ".$rooms2[$i]['id']);
							mysql_class::ex_sqlx("insert into `room_det` (`room_id`,`reserve_id`,`aztarikh`,`tatarikh`,`user_id`) values ($r1,".$rooms2[$i]['reserve_id'].",'$jday','".room_det_class::Mini(array($rooms1[0]['tat'],$rooms2[$i]['tat']))."',$user_id)");
						}
						else
						{
							
							mysql_class::ex_sqlx("update `room_det` set `room_id` = '$r1',`tatarikh`='".room_det_class::Mini(array($rooms1[0]['tat'],$rooms2[$i]['tat']))."' where `id` = ".$rooms2[$i]['id']);
						}
						if($rooms1[0]['tat']<$rooms2[$i]['tat'])
						{
							mysql_class::ex_sqlx("insert into `room_det` (`room_id`,`reserve_id`,`aztarikh`,`tatarikh`,`user_id`) values ($r2,".$rooms2[$i]['reserve_id'].",'".$rooms1[0]['tat']."','".$rooms2[$i]['tat']."',$user_id)");
						}
						$out = TRUE;
					}
					else if($rooms2[$i]['azt']>$jday && $rooms2[$i]['azt']<$rooms1[0]['tat'])
					{
						mysql_class::ex_sqlx("update `room_det` set `tatarikh` = '".room_det_class::Mini(array($rooms1[0]['tat'],$rooms2[$i]['tat']))."',`room_id` = $r1 where `id` = ".$rooms2[$i]['id']);
						if($rooms1[0]['tat']<$rooms2[$i]['tat'])
						{
							mysql_class::ex_sqlx("insert into `room_det` (`room_id`,`reserve_id`,`aztarikh`,`tatarikh`,`user_id`) values ($r2,".$rooms2[$i]['reserve_id'].",'".$rooms1[0]['tat']."','".$rooms2[$i]['tat']."',$user_id)");
						}
						$out = TRUE;
					}
				}
				if($out)
				{
					mysql_class::ex_sqlx("update `mehman` set `room_id` = '-100' where `room_id` = '$r2'");
					mysql_class::ex_sqlx("update `mehman` set `room_id` = '$r2' where `room_id` = '$r1'");
					mysql_class::ex_sqlx("update `mehman` set `room_id` = '$r1' where `room_id` = '-100'");
					mysql_class::ex_sqlx("update `hotel_reserve` set `jabejayi_count` = `jabejayi_count`+1 where `reserve_id` = $res1");
					for($i = 0;$i < count($rooms2);$i++)
						mysql_class::ex_sqlx("update `hotel_reserve` set `jabejayi_count` = `jabejayi_count`+1 where `reserve_id` = ".$rooms2[$i]['reserve_id']);		
				}
				if($rooms2 == null)
				{
					room_class::setVaziat($r1,1);
					room_class::setVaziat($r2,0);
				}
			}
			return($out);
		}
		public function refundReserve($reserve_id,$tozihat='')
                {
			$room_id = '';
			if($tozihat=='')
				$toz = 'کنسلی شماره رزرو '.$reserve_id;
			else
				$toz = 'کنسلی شماره رزرو '.$reserve_id.' به علت '.$tozihat;
			$reserve_id = (int)$reserve_id;
			$reserve = new reserve_class($reserve_id);
			$shomare_sanad = array();
			if($reserve->id > 0)
			{
				$tour = FALSE;
				$pre_tour = (($reserve->hotel_reserve->m_belit==0)?FALSE:TRUE);
				$lother_id = room_det_class::getOthers($reserve_id);
				if(!is_array($lother_id) || count($lother_id)!=4)
					$lother_id = array(-1,-1,-1,-1);
			//-----------------------------------reverse reserve_id
				mysql_class::ex_sql("select `room_id` from `room_det` where `reserve_id`= $reserve_id",$qref);
				if($rref = mysql_fetch_array($qref))
					$room_id = $rref["room_id"];
				mysql_class::ex_sqlx("update `room` set `vaziat`='1' where `id`= $room_id");
				mysql_class::ex_sqlx("update `hotel_reserve` set `reserve_id`=-1*`reserve_id` where `reserve_id`= $reserve_id");
				mysql_class::ex_sqlx("update `khadamat_det` set `reserve_id`=-1*`reserve_id` where `reserve_id`= $reserve_id");
				mysql_class::ex_sqlx("update `room_det` set `reserve_id`=-1*`reserve_id` where `reserve_id`= $reserve_id");				
				mysql_class::ex_sqlx("update `sanad_reserve` set `reserve_id`=-1*`reserve_id` where `reserve_id`= $reserve_id");
			//--------------------------------------------------------------------------------------------
				if($pre_tour)
				{
					$shomare_sanad = sanadzan_class::newInverseTourReserveSanad($reserve->hotel_id,$reserve->hotel_reserve->ajans_id,array($lother_id[0],$lother_id[2]),array($lother_id[1],$lother_id[3]),$reserve->hotel_reserve->m_hotel+$reserve->hotel_reserve->m_belit,array($reserve->hotel_reserve->m_belit1,$reserve->hotel_reserve->m_belit2),$toz);
				}
				else
				{
					$shomare_sanad = sanadzan_class::newHotelRefundSanad($reserve->hotel_id,$reserve->hotel_reserve->ajans_id,$reserve->hotel_reserve->m_hotel,$toz);
				}
			}
                        return($shomare_sanad);
                }
		public function killReserve($reserve_id)
		{
			mysql_class::ex_sqlx("delete from `hotel_reserve` where `reserve_id`= $reserve_id");
			mysql_class::ex_sqlx("delete from `khadamat_det` where `reserve_id`= $reserve_id");
			mysql_class::ex_sqlx("delete from `room_det` where `reserve_id`= $reserve_id");
			mysql_class::ex_sqlx("delete from `mehman` where `reserve_id` = $reserve_id");
			$in = null;
			mysql_class::ex_sql("select `sanad_record` from `sanad_reserve` where `reserve_id`=$reserve_id",$q);
			while($r = mysql_fetch_array($q))
				$in[] = (int)$r['sanad_record'];
			$in = ($in == null)?'':' where `id` in ('.implode(',',$in).')';
			if($in != '')
				mysql_class::ex_sqlx("delete from `sanad` $in");
			mysql_class::ex_sqlx("delete from `sanad_reserve` where `reserve_id`= $reserve_id");
		}
	        public function isPick($room_id,$aztarikh,$shab)
	        {
                	$t = $room_id;
        	        $room_id = (count($t)>1)?$t:$room_id;
	                if(count($t)>1)
                        	$room = new room_class((int)$t[0]);
                	else
        	                $room = new room_class((int)$room_id);
	                $hotel = new hotel_class($room->hotel_id);
                	$tmp = $aztarikh;
        	        $is_pick = FALSE;
	                for($i = 0;$i < (int)$shab;$i++)
                	{
	                        if($hotel->isPick($tmp))
                                	$is_pick = TRUE;
                        	$tmp = date("Y-m-d 14:00:00",strtotime($tmp.' + 1 day'));
                	}
        	        return($is_pick);
	        }
		public function loadReserve_id_habibi($aztarikh,$tatarikh,$room_id)
		{
			//$out = array();
			$shart = '';
			$room_id = $room_id;
			$out = '';
			$aztarikh = ($aztarikh!='')?$aztarikh:'';
			$tatarikh = ($tatarikh!='')?$tatarikh:'';
			$aztarikh = date("Y-m-d",strtotime($aztarikh));
			$tatarikh = date("Y-m-d",strtotime($tatarikh));
			if ($room_id!=-1)
			{
				if (($aztarikh=='1970-01-01') && ($tatarikh=='1970-01-01'))
				{
					mysql_class::ex_sql("select `reserve_id` from `mehman` where `room_id`='$room_id' and `reserve_id`>0",$q);
//echo "select `reserve_id` from `mehman` where `room_id`='$room_id' and `reserve_id`>0";
					while($r = mysql_fetch_array($q))
						$out .=($out==''? '':',' ).$r['reserve_id'];
				}
				elseif (($aztarikh=='1970-01-01') || ($tatarikh=='1970-01-01'))
				{
					$out = '';
					echo "لطفا بازه تاریخی را درست وارد نمایید";
				}
				else 
				{
					mysql_class::ex_sql(" SELECT `reserve_id`,`aztarikh`,`tatarikh` FROM `room_det` WHERE `room_id`=$room_id and ((date(`aztarikh`) >= '$aztarikh' and date(`tatarikh`) <= '$tatarikh')||(date(`aztarikh`)< '$aztarikh' and date(`tatarikh`) <= '$tatarikh' and date(`tatarikh`)> '$tatarikh')||(date(`aztarikh`)>= '$aztarikh' and date(`tatarikh`) > '$tatarikh' and date(`aztarikh`)< '$aztarikh')||(date(`aztarikh`)< '$aztarikh' and date(`tatarikh`) > '$tatarikh')) and `reserve_id`>0 group by `reserve_id`",$q);
					while($r = mysql_fetch_array($q))
						$out .=($out==''? '':',' ).$r['reserve_id'];
				}
			}
			else 
			{
				if (($aztarikh=='') || ($tatarikh==''))
				{
					$out = '';
					echo "لطفا بازه تاریخی را درست وارد نمایید";
				}
				else 
				{
					mysql_class::ex_sql(" SELECT `reserve_id`,`aztarikh`,`tatarikh` FROM `room_det` WHERE ((date(`aztarikh`) >= '$aztarikh' and date(`tatarikh`) <= '$tatarikh')||(date(`aztarikh`)< '$aztarikh' and date(`tatarikh`) <= '$tatarikh' and date(`tatarikh`)> '$tatarikh')||(date(`aztarikh`)>= '$aztarikh' and date(`tatarikh`) > '$tatarikh' and date(`aztarikh`)< '$aztarikh')||(date(`aztarikh`)< '$aztarikh' and date(`tatarikh`) > '$tatarikh')) and `reserve_id`>0 group by `reserve_id`",$q);
					while($r = mysql_fetch_array($q))
						$out .=($out==''? '':',' ).$r['reserve_id'];
				}
			}
			return $out;
		}
		public function onlinePreReserve($hotel_id,$ajans_id,$comision,$room_typ_id,$ghimat,$startDate,$delay,$tedad,$voroodi,$khorooji,$nafar,$khadamat,$user_id=-1)
		{
			$conf = new conf;
			$rooms = array();
			$tedad = (int)$tedad;
			$delay = (int)$delay;
			$hotel_id = (int)$hotel_id;
			$ajans_comision_id = (int)$ajans_id['ajans_comision_id'];
			$ajans_id = (int)$ajans_id['ajans_id'];
			$com = ((room_det_class::isPick($room_typ_id,$startDate,$delay))?(int)$comision['pick']/100:(int)$comision['nopick']/100);
			$takh = (int)$comision['takhfif']/100;
			$tour = FALSE;
			$other_id_arr = array();
                        if(is_array($ghimat))
                        {
                                $tour = TRUE;
                                $ghimat_tour = audit_class::perToEn($ghimat['ghimat_tour']);
                                $ghimat_blit1 = audit_class::perToEn($ghimat['ghimat_belit1']);
                                $other_id = $ghimat['other_moeen_id1'];
                                $other_kol_id = $ghimat['other_kol_id1'];
                                $ghimat_blit2 = audit_class::perToEn($ghimat['ghimat_belit2']);
                                $other_id2 = $ghimat['other_moeen_id2'];
                                $other_kol_id2 = $ghimat['other_kol_id2'];
				$ghimat_blit2 = 0;
				$other_id3 = -1;
                                $other_kol_id3 = -1;
                                $ghimat = $ghimat_tour - ($ghimat_blit1+$ghimat_blit2+$ghimat_blit3);
				$other_id_arr = array('ghimat_belit1'=>$ghimat_blit1,'other_moeen_id1'=>$other_id,'other_kol_id1'=>$other_kol_id,'ghimat_belit2'=>$ghimat_blit2,'other_moeen_id2'=>$other_id2,'other_kol_id2'=>$other_kol_id2,'ghimat_belit3'=>$ghimat_blit3,'other_moeen_id3'=>$other_id3,'other_kol_id3'=>$other_kol_id3);
                        }
                        else
                        {
                                $ghimat_tour = audit_class::perToEn($ghimat);
                                $ghimat_blit1 = 0;
                                $other_id = -1;
                                $other_kol_id = -1;
                                $ghimat_blit2 = 0;
                                $other_id2 = -1;
                                $other_kol_id2 = -1;
				$ghimat_blit3 = 0;
                                $other_id3 = -1;
                                $other_kol_id3 = -1;
                        }
			$ghimat_darad = FALSE;
                        if($ghimat_tour != 0 || $ghimat_blit1 != 0 || $ghimat_blit2 != 0 || $ghimat_blit3 != 0)
                                $ghimat_darad = TRUE;
			$endDate = date("Y-m-d H:i:s",strtotime($startDate." + $delay days"));
                        if($voroodi)
                                $startDate = date("Y-m-d 00:00:00",strtotime($startDate));
			else
				$startDate = date("Y-m-d 14:00:00",strtotime($startDate));
                        if($khorooji)
                                $endDate = date("Y-m-d 21:00:00",strtotime($endDate));	
			else
				$endDate = date("Y-m-d 14:00:00",strtotime($endDate));
                        $limit_day = TRUE;
                        if(jdate("m/d",strtotime($startDate)) == audit_class::enToPer($conf->limitDate) || jdate("m/d",strtotime($endDate)) == audit_class::enToPer($conf->limitDate))
                                $limit_day = FALSE;
			$user_id = $user_id <= 0 ? (int)$_SESSION["user_id"] : $user_id;
			$out = FALSE;
			$hotel = new hotel_class($hotel_id);
			$room_count = 1;
			$room_ok = (is_array($room_typ_id) && (int)$room_typ_id[0]>0);
			if($room_ok)
				for($j = 0;$j < count($room_typ_id);$j++)
				{
					$ttt = room_det_class::roomIdAvailable($room_typ_id[$j],$startDate,$endDate);
					if($ttt!=null)
						$room_ok = FALSE;
				}
			
			if($hotel->hotelAvailableBetween($startDate,$endDate) && $ghimat_darad && $delay>=0 && $room_ok && $limit_day)
			{
				if(is_array($room_typ_id))
				{
					$rooms = $room_typ_id;
					$tedad = count($rooms);
				}
				else
				{
					$rooms = room_class::loadOpenRoomArray($startDate,$delay,$voroodi,$khorooji,$hotel_id,$room_typ_id);
				}
				$out = array();
				if($tedad <= count($rooms))
				{
					$reserve_id = 0;
					$q = null;
					mysql_class::ex_sql("select MAX(abs(`reserve_id`)) as `mrs` from `room_det` ",$q);
					if($r = mysql_fetch_array($q))
						$reserve_id = (int)$r["mrs"];
					$reserve_id++;
					$reserve_id =(($reserve_id<=0)?1:$reserve_id);
					//----------------KHADAMAT-------------------
					$khadamat_ghimat = 0;
					if(is_array($khadamat))
					{
						for($ind = 0;$ind < count($khadamat);$ind++)
						{
							room_det_class::sabtKhadamat($hotel_id,$reserve_id,$ajans_id,$khadamat[$ind]["id"],$khadamat[$ind]["ghimat"],$khadamat[$ind]["tedad"],$khadamat[$ind]["voroodi"],$khadamat[$ind]["khorooji"],$startDate,$endDate);
							$khadamat_ghimat += (int)$khadamat[$ind]["ghimat"];
						}
					}
					//-------------------------------------------
					if($tour)
						$shomare_sanad = sanadzan_class::newTourReserveSanad($hotel_id,$ajans_id,array($other_id,$other_id2,$other_id3),array($other_kol_id,$other_kol_id2,$other_kol_id3),$ghimat_tour,array($ghimat_blit1,$ghimat_blit2,$ghimat_blit3),-1);
					else
					{
						$comision = $com*$ghimat;
						$takhfif = $takh*$ghimat;
						//$shomare_sanad = sanadzan_class::newHotelReserveSanad($hotel_id,$ajans_id,$ghimat,-1,'',$user_id);
						$shomare_sanad = sanadzan_class::newOnlineHotelReserveSanad($hotel_id,$ajans_id,$ajans_comision_id,$ghimat,$comision,$takhfif,-1,$user_id);
					}
					for($i = 0;$i < $tedad;$i++)
					{
						$gh_tmp=0;
						if($delay!=0)
							$gh_tmp = (int)($ghimat/($tedad*$delay));
						mysql_class::ex_sqlx("insert into `room_det` (`room_id`, `aztarikh`, `tatarikh`, `user_id` , `reserve_id` , `ghimat` ,`nafar`) values ('".$rooms[$i]."','$startDate','$endDate','$user_id',$reserve_id,$gh_tmp,$nafar)");
					}
					$out = array("shomare_sanad"=>$shomare_sanad,"reserve_id"=>$reserve_id,"other_id"=>$other_id_arr);
				}
			}
			return($out);
		}
	}
?>
