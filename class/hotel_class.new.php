<?php
	class hotel_class
	{
		public $id=-1;
		public $name="";
		public $moeen_id = 0;
		public $setRoomJavaScript = FALSE;
		public $info = array();
		public $ghaza_moeen_id = -1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `hotel` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->moeen_id = (int)$r['moeen_id'];
				if($r['info'] != null)
					$this->info = unserialize($r['info']);
				$this->ghaza_moeen_id = (int)$r['ghaza_moeen_id'];
			}
		}
		public function hotelAvailableOn($curdate)
		{
			$curdate_f = date("Y-m-d 23:59:59",strtotime($curdate));
			$out = FALSE;
			mysql_class::ex_sql("select `id` from `hotel_working_date` where `aztarikh` <= '$curdate_f' and `tatarikh` >= '$curdate' and `hotel_id` = ".$this->id,$q);
			if($r = mysql_fetch_array($q))
				$out = TRUE;
			return($out);
		}
		public function isPick($curdate)
		{
			$curdate_f = date("Y-m-d 23:59:59",strtotime($curdate));
                        $out = FALSE;
                        mysql_class::ex_sql("select `id`,`typ` from `hotel_working_date` where `aztarikh` <= '$curdate_f' and `tatarikh` >= '$curdate' and `hotel_id` = ".$this->id,$q);
                        if($r = mysql_fetch_array($q))
				if((int)$r['typ']==1)
	                                $out = TRUE;
                        return($out);
		}
		public function getGhimat($curdate)
                {
                        $curdate_f = date("Y-m-d 23:59:59",strtotime($curdate));
                        $out = 0;
                        mysql_class::ex_sql("select `ghimat` from `hotel_working_date` where `aztarikh` <= '$curdate_f' and `tatarikh` >= '$curdate' and `hotel_id` = ".$this->id,$q);
                        if($r = mysql_fetch_array($q))
                                $out = (int)$r['ghimat'];
                        return($out);
                }
		public function addDay($tmp,$delay=1)
		{
			$out = date("Y-m-d H:i:s",strtotime("$tmp +$delay day"));
			return($out);
		}
		public function hotelAvailableBetween($startDate,$endDate,$ret_array=FALSE)
		{
			$out = (($ret_array)?array():TRUE);
			if($startDate > $endDate)
			{
				$tmp = $endDate;
				$endDate = $startDate;
				$startDate = $tmp;
			}
			$tmp = $startDate;
			while(strtotime($tmp)<=strtotime($endDate))
			{
				if($this->hotelAvailableOn($tmp) && $ret_array)
					$out[] = $tmp;
				else if(!$this->hotelAvailableOn($tmp) && !$ret_array)
					$out = FALSE;
				$tmp = $this->addDay($tmp);
			}
			return($out);
		}
		public function loadClosedRoomsArray($startDate,$endDate,$room_id = -1)
		{
			$out = array();
			$room_id = (int)$room_id;
			mysql_class::ex_sql("select `room_id`,`aztarikh`,`tatarikh`,`reserve_id` from `room_det` where (('$startDate'>`aztarikh` and '$startDate' <`tatarikh`) or ('$endDate'>`aztarikh` and '$endDate' <`tatarikh`) or ('$startDate' <= `aztarikh` and '$endDate' >=`tatarikh`)) and `room_id` in (select `id` from `room` where `hotel_id` = ".$this->id.(($room_id == -1)?"":" and `id` = $room_id").") and `reserve_id`>0 order by `aztarikh`,`tatarikh`",$q);
			while($r = mysql_fetch_array($q))
				$out[] = array('room_id'=>(int)$r['room_id'],'aztarikh'=>$r['aztarikh'],'tatarikh'=>$r['tatarikh'],'reserve_id'=>$r['reserve_id']);
			return($out);
			
		}
		public function isInRooms($rooms,$room_id)
		{
			$out = -1;
			for($i = 0;$i < count($rooms);$i++)
				if($rooms[$i]["room_id"] == $room_id)
					$out = $i;
			return($out);
		}
		public function loadTotalRoomsArray($startDate,$endDate)
		{
			$out = array();
			$crooms = $this->loadClosedRoomsArray($startDate,$endDate);
			mysql_class::ex_sql("select * from `room` where `hotel_id` = ".$this->id." and `en` = 1	order by `room_typ_id`,`name`",$q);
			while($r = mysql_fetch_array($q))
			{
				$out[] = array('room_id'=>(int)$r['id'],'aztarikh'=>'','tatarikh'=>'');
			}
			return($out);
		}
		public function iToTime($i)
		{
			$i = (int)$i;
			if($i>=0 && $i <=11)
				$out = (2*$i).":00 - ".(2*$i+2).":00";
			else
				$out = "";
			return($out);
		}
		public function loadDay($currentDate,$aztarikh,$tatarikh,$reserve_id,$next_aztarikh,$next_res,$oncl,$room_closed_class,&$indx0,$room_id,$isAdmin)
		{
			$room_id_show = '';
			if($this->setRoomJavaScript)
				$room_id_show = ",$room_id";
			$is_admin = $isAdmin;
			$oncll = (($this->setRoomJavaScript)?"onclick=\"$oncl(0 $room_id_show);\"":'');
			$oncl = (($oncl != '')?"onclick=\"$oncl($reserve_id $room_id_show);\"":$oncl);
			$oncl1 = (($oncl != '')?"onclick=\"$oncl($next_res $room_id_show);\"":$oncl);
			$user = new user_class((int)$_SESSION['user_id']);
			$df0 = new daftar_class($user->daftar_id);
			$reserve_id = (int)$reserve_id;
			$hr = new hotel_reserve_class;
			$hr->loadByReserve($reserve_id);
			$res1_lname = $hr->lname;
			$t1 = "$res1_lname($reserve_id)\n";
			$aj = new ajans_class($hr->ajans_id);
			$t1 .= ',آژانس : '.$aj->name.",هتل : ".monize($hr->m_hotel).",رفت : ".monize($hr->m_belit1).",برگشت :".monize($hr->m_belit2);
			$df1 = new daftar_class($aj->daftar_id);
			$css_class = (($df1->css_class!='' && ($is_admin || ($df0->id==$df1->id)))?$df1->css_class:'room_closed');
			$hr->loadByReserve($next_res);
			$res2_lname = $hr->lname;
			if(!$is_admin && $df0->id!=$df1->id)
                                $t1 = '';
			$t2 = "$res2_lname($next_res)";
                        $aj = new ajans_class($hr->ajans_id);
			$t2 .= ',آژانس : '.$aj->name.",هتل : ".monize($hr->m_hotel).",رفت : ".monize($hr->m_belit1).",برگشت :".monize($hr->m_belit2);
                        $df2 = new daftar_class($aj->daftar_id);
                        $ncss_class = (($df2->css_class!='' && ($is_admin || ($df0->id==$df1->id)))?$df2->css_class:'room_closed');
			$out = "<table style=\"font-size:4px;width:100%;\" cellspacing=\"0\" cellpadding=\"0\">\n<tr>\n";
			$cd = strtotime($currentDate);
			$cd = strtotime(date("Y-m-d 00:00:00",$cd));
			$lcd = strtotime(date("Y-m-d 23:59:59",$cd));
			$az = strtotime($aztarikh);
			$ta = strtotime($tatarikh);
			$nz = strtotime($next_aztarikh);
			if(!$is_admin && $df0->id!=$df1->id)
				$t2 = '';
			$indx1 = (($indx0 < count($room_closed_class)-1)?$indx0+1:0);
			if($lcd > $az and $cd < $ta)
			{
				$cdd = date("Y-m-d",$cd);
	                        $azz = date("Y-m-d",$az);
        	                $taa = date("Y-m-d",$ta);
				if($cd > $az && $lcd < $ta )
				{
					for($i = 0;$i <12;$i++)
						$out .= "<td class = \"$css_class\" $oncl title=\"$t1\">&nbsp;</td>";
				}
				else if($cd <= $az)
				{
/*
					$az_time = (int)date("H",$az);
					$ta_time = (int)date("H",$ta);
					$indx = ($az_time - ($az_time % 2)) / 2 + ($az_time % 2);
					$lindx = ($ta_time - ($ta_time % 2)) / 2 + ($ta_time % 2);
*/
                                        if($next_aztarikh != '')
                                                $naz_time = (int)date("H",$nz);
                                        else
                                                $naz_time = -1;
                                        $az_time = (int)date("H",$az);
                                        $indx = ($az_time - ($az_time % 2)) / 2 + ($az_time % 2);
                                        $naz_time = ($naz_time - ($naz_time % 2)) / 2 + ($naz_time % 2);
                                        $ta_time = (int)date("H",$ta);
                                        $lindx = ($ta_time - ($ta_time % 2)) / 2 + ($ta_time % 2);
					for($i = 0;$i <12;$i++)
					{
/*
						if($i<$indx )// || $i>$lindx
							$out .= "<td class = \"room_opened\" $oncll title=\"".$this->iToTime($i)."\">&nbsp;</td>";
						else
							$out .= "<td class = \"$css_class\" $oncl title=\"$t1\">&nbsp;</td>";
*/
                                                if($i>=$indx || ($naz_time<=$i && $naz_time>0) || $i<=$lindx)
                                                        if($i==$lindx)
                                                                $out .= "<td class = \"room_opened\" style=\"background-color:#ffffff;\" title=\"\">&nbsp;</td>";
                                                        else
                                                                $out .= "<td class = \"".(($lindx>=$i)?$css_class:$ncss_class)."\" ".(($lindx>=$i)?$oncl:$oncl1)." title=\"".(($lindx>=$i)?$t1:$t2)."\">&nbsp;</td>";
                                                else
                                                        $out .= "<td class = \"room_opened\" $oncll title=\"".$this->iToTime($i)."\">&nbsp;</td>";

					}
				}
				else if($lcd >= $ta)
				{
					if($next_aztarikh != '')
						$az_time = (int)date("H",$nz);
					else
						$az_time = -1;
					$az_time = ($az_time - ($az_time % 2)) / 2 + ($az_time % 2);
					$ta_time = (int)date("H",$ta);
					$indx = ($ta_time - ($ta_time % 2)) / 2 + ($ta_time % 2);
                                        for($i = 0;$i <12;$i++)
                                        {
                                                if($i<$indx || ($az_time<=$i && $az_time>0))
							if($i==$indx)
								$out .= "<td class = \"room_opened\" style=\"background-color:#ffffff;\" title=\"\">&nbsp;</td>";
							else
	                                                        $out .= "<td class = \"".(($indx>$i)?$css_class:$ncss_class)."\" ".(($indx>$i)?$oncl:$oncl1)." title=\"".(($indx>$i)?$t1:$t2)."\">&nbsp;</td>";
						else
                                                        $out .= "<td class = \"room_opened\" $oncll title=\"".$this->iToTime($i)."\">&nbsp;</td>";
                                        }
				}
			}
			else
			{
				for($i = 0;$i <12;$i++)
					$out .= "<td class = \"room_opened\" $oncll title=\"".$this->iToTime($i)."\">&nbsp;</td>";
			}
			$out .= "</tr>\n</table>\n";
			return($out);
		}
		public function loadDates($startDate,$endDate,$room_id,$oncl,$room_closed_class,$isAdmin)
		{
			if($room_closed_class == null)
				$room_closed_class[] = 'room_closed';
			$out = "";
			$tmp = strtotime($startDate);
			$tmp = strtotime(date("Y-m-d 00:00:00",$tmp));
			$cdays = $this->loadClosedRoomsArray($startDate,$endDate,$room_id);
			$i = 0;
			$indx = 0;
			$add_indx = FALSE;
			while($tmp <= strtotime($endDate))
			{
				if($this->hotelAvailableOn(date("Y-m-d 00:00:00",$tmp)))
				{
					$aztarikh = '';
					$tatarikh = '';
					$next_aztarikh = '';
					$next_res = 0;
					$reserve_id = 0;
					if($i<count($cdays))
					{
						$az = strtotime($cdays[$i]['aztarikh']);
                                		$ta = strtotime($cdays[$i]['tatarikh']);
						$ltmp = strtotime(date("Y-m-d 23:59:59",$tmp));
						if(($az < $ltmp && $ta > $tmp) || ($az == $tmp && $ta == $ltmp) )
						{
							if(isset($cdays[$i+1]))
							{
								if(date("Y-m-d",strtotime($cdays[$i]['tatarikh'])) == date("Y-m-d",strtotime($cdays[$i+1]['aztarikh'])))
								{
									$next_aztarikh = $cdays[$i+1]['aztarikh'];
									$next_res = $cdays[$i+1]['reserve_id'];
								}
							}
							$aztarikh = $cdays[$i]['aztarikh'];
							$tatarikh = $cdays[$i]['tatarikh'];
							$reserve_id = $cdays[$i]['reserve_id'];
						}
						$add_indx = FALSE;
						if($ta<=$ltmp)
						{
							$i++;
							$add_indx = TRUE;
						}
					}
					$out .= "<td align=\"center\">\n";
					$out .= $this->loadDay(date("Y-m-d 00:00:00",$tmp),$aztarikh,$tatarikh,$reserve_id,$next_aztarikh,$next_res,$oncl,$room_closed_class,$indx,$room_id,$isAdmin);
					$out .= "</td>\n";
				}
				else
				{
					$out .= "<td align=\"center\">\n&nbsp;</td>\n";
				}
				$tmp = strtotime(date("Y-m-d 00:00:00",$tmp)." + 1 day");
			}
			return($out);
		}
		public function loadShamsis($startDate,$endDate)
		{
			$out = "";
			$tmp = strtotime($startDate);
                        while($tmp <= strtotime($endDate))
                        {
                                $out .= "<th>\n";
                                $out .= jdate("d",$tmp);
                                $out .= "</th>\n";
                                $tmp = strtotime(date("Y-m-d 00:00:00",$tmp)." + 1 day");
                        }
			return($out);
		}
		public function loadRooms($tarikh,$isAdmin,$oncl='')
		{
			$out = "<table border=\"1\" style=\"width:21cm;\" cellspacing=\"0\" cellpadding=\"0\">";
			if($tarikh == '')
				$tarikh = date("Y-m-d H:i:s");
			$current_shamsi_month = perToEnNums(jdate("m",strtotime($tarikh)));
			$current_shamsi_year = perToEnNums(jdate("Y",strtotime($tarikh)));
			if($current_shamsi_month<12)
				$current_shamsi_lastday = (($current_shamsi_month<7)?31:30);
			else
				$current_shamsi_lastday = 29;
			$startDate = audit_class::hamed_pdateBack("$current_shamsi_year/$current_shamsi_month/1");
			$tmp = explode(" ",$startDate);
			$startDate = $tmp[0]." 00:00:00";
			$endDate = audit_class::hamed_pdateBack("$current_shamsi_year/$current_shamsi_month/$current_shamsi_lastday");
			$tmp = explode(" ",$endDate);
			$endDate = $tmp[0]." 23:59:59";
			$startDate = date("Y-m-d 00:00:00",strtotime("$startDate - ".$conf->decStartGaant." day"));
			$endDate = date("Y-m-d 23:59:59",strtotime("$endDate + ".$conf->addEndGaant." day"));
			$rooms = $this->loadTotalRoomsArray($startDate,$endDate);
			$out .= "<tr>\n<th>اتاق</th>\n<th>نوع</th>\n".$this->loadShamsis($startDate,$endDate)."</tr>\n";
			$room_closed_class = array('room_closed','room_closed2');
			for($i = 0;$i < count($rooms);$i++)
			{
				$room = new room_class($rooms[$i]['room_id']);
				$room_typ = new room_typ_class($room->room_typ_id);
				$out .= "<tr>\n";
				$out .= "<td title=\"جهت مشاهده تصاویر اتاق برروی نام آن کلیک کنید\"><u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('view_roompic.php?room_id=".$rooms[$i]['room_id']."&','',500,500);\" >".$room->name."</span></u></td>\n";
				$out .= "<td>".$room_typ->name."</td>\n";
				$out .= $this->loadDates($startDate,$endDate,$rooms[$i]['room_id'],$oncl,$room_closed_class,$isAdmin);
				$out .= "</tr>\n";
			}
			$out .= "</table>\n";
			if($isAdmin)
			{
				$out .= "<center>\n";
				$out .= daftar_class::legend()."\n";
				$out .= "</center>\n";
			}
			return($out);
		}
		public function loadByReserveId($reserve_id)
		{
			$out = FALSE;
			$reserve_id = (int)$reserve_id;
			$rd = new room_det_class;
			$rd->loadByReserve($reserve_id);
			if(isset($rd[0]))
			{
				$room = new room_class($rd[0]->room_id);
				$id = $room->hotel_id;
	                        mysql_class::ex_sql("select * from `hotel` where `id` = $id",$q);
	                        if($r = mysql_fetch_array($q))
        	                {
                	                $this->id=$r['id'];
                        	        $this->name=$r['name'];
                                	$this->moeen_id = (int)$r['moeen_id'];
					$out = TRUE;
	                        }

			}
			return($out);
		}
		public function getKolZarfiat($hotel_id)
		{
			$zarfiat = 0;
			mysql_class::ex_sql("select sum(`room_typ`.`zarfiat`) as `jam` from `room` left join `room_typ` on (`room`.`room_typ_id`=`room_typ`.`id`) where `room`.`hotel_id`=$hotel_id and en=1",$q);
			if($r = mysql_fetch_array($q))
                        	$zarfiat = (int)$r['jam'];
			return $zarfiat;
		}
		public function getDayTedad($hotel_id,$tarikh)
		{
			$tarikh = explode(' ',$tarikh);
			$tarikh = $tarikh[0];
			$aztarikh = $tarikh.' 00:00:00';
			$tatarikh = $tarikh.' 23:59:59';
			$reserves = array();
			$arr = mysql_class::getInArray('id','room',"`hotel_id`=$hotel_id");
			$arr = explode(',',$arr);
			for($j=0;$j<count($arr);$j++)
			{
				$room_id = $arr[$j];
				//array_unique
				$res = room_det_class::roomIdAvailable($room_id,$aztarikh,$tatarikh);
				for($i=0;$i<count($res);$i++)
					if ($res[$i]!=null) 
						$reserves[]=$res[$i];
			}
			$reserves = array_unique($reserves);
			$tedad = 0;
			foreach($reserves as $j=>$kh)
			{
				$res_det = room_det_class::loadDetByReserve_id($reserves[$j]);
				$tedad+=$res_det['rooms'][0]['nafar'];
			}
			return $tedad;
		}
		public function getFullTedad($hotel_id,$aztarikh,$tatarikh)
		{
			$tedad = array();
			$shab = audit_class::upint((strtotime($tatarikh) - strtotime($aztarikh))/(24*60*60)) ;
			
			for($i=0;$i<$shab;$i++)
				$tedad[audit_class::hamed_pdate(date("Y-m-d",strtotime($aztarikh ." + $i day")))]=hotel_class::getDayTedad($hotel_id,date("Y-m-d",strtotime($aztarikh ." + $i day")));
			
			/*
			$arr = mysql_class::getInArray('id','room',"`hotel_id`=$hotel_id");
			$arr = explode($arr);
			for($j=0;$j<count($arr);$j++)
			{
				$room_id = $arr[$j];
				for($i=0;$i<$shab;$i++)
					$tedad[audit_class::hamed_pdate(date("Y-m-d",strtotime($aztarikh ." + $i day")))] =hotel_class::getDayTedad($hotel_id,date("Y-m-d",strtotime($aztarikh ." + $i day")));
			}
			*/
			return $tedad;
		}
		public function isRoomFree($room_id,$tarikh)
		{
			$out = FALSE;
			$aztarikh = date("Y-m-d 00:00:00",strtotime($tarikh));
			$tatarikh = date("Y-m-d 23:59:59",strtotime($tarikh));
			$tmp = room_det_class::roomIdAvailable($room_id,$aztarikh,$tatarikh);
			if($tmp==null)
				$out = TRUE;
			return $out;
		}
		public function getDayRoom($hotel_id,$tarikh)
		{
			$rooms = room_class::loadRooms($hotel_id);
			$natije = null;
			for($i=0 ;$i<count($rooms);$i++)
			{
				$eshghal=0;
				for($j=0;$j<$rooms[$i]['count'];$j++)
					if(!hotel_class::isRoomFree($rooms[$i]['room_ids'][$j],$tarikh))
						$eshghal++;
				$natije[$rooms[$i]['name']]=array('eshghal'=>$eshghal,'kol'=>$rooms[$i]['count']);
			}
			return $natije;
		}
		public function getFullRoom($hotel_id,$aztarikh,$tatarikh)
		{
			$tedad = array();
			$shab = audit_class::upint((strtotime($tatarikh) - strtotime($aztarikh))/(24*60*60)) ;
			for($i=0;$i<$shab;$i++)
				$room[audit_class::hamed_pdate(date("Y-m-d",strtotime($aztarikh ." + $i day")))]=hotel_class::getDayRoom($hotel_id,date("Y-m-d",strtotime($aztarikh ." + $i day")));
			return $room;
		}
		public function getHotels()
		{
			$out = array();
			mysql_class::ex_sql('select * from `hotel` order by `name`',$q);
			while($r = mysql_fetch_array($q))
				$out[] = array('id'=>(int)$r['id'],'name'=>$r['name'],'info'=>((isset($r['info']) && ($r['info']!=null))?unserialize($r['info']):null));
			return($out);
		}
	}
?>
