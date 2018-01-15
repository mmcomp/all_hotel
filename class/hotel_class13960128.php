<?php
	class hotel_class
	{
		public $id=-1;
		public $name="";
		public $moeen_id = 0;
		public $setRoomJavaScript = FALSE;
		public $info = array();
		public $ghaza_moeen_id = -1;
		public $is_our = 1;
		public $is_shab_nafar = 1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `hotel` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->moeen_id = (int)$r['moeen_id'];
				/*if($r['info'] != null)
					$this->info = unserialize($r['info']);*/
				$this->ghaza_moeen_id = (int)$r['ghaza_moeen_id'];
				$this->is_our = (int)$r['is_our'];
				$this->is_shab_nafar = (int)$r['is_shab_nafar'];
			}
		}
		public function hotelAvailableOn($curdate)
		{
                        $curdate_f = date("Y-m-d 23:59:59",strtotime($curdate));
                        $out = FALSE;
												$my = new mysql_class;
                        //mysql_class::ex_sql("select `id` from `hotel_working_date` where `aztarikh` <= '$curdate_f' and `tatarikh` >= '$curdate' and `hotel_id` = ".$this->id,$q);
												$my->ex_sql("select `id` from `hotel_working_date` where `aztarikh` <= '$curdate_f' and `tatarikh` >= '$curdate' and `hotel_id` = ".$this->id,$q);
                        //if($r = mysql_fetch_array($q))
			if(mysql_num_rows($q) > 0)
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
			$rooms = $room_id;
			if($room_id == -1)
				$rooms = mysql_class::getInArray('id','room',"`hotel_id` = ".$this->id);
			mysql_class::ex_sql("select `room_id`,`aztarikh`,`tatarikh`,`reserve_id` from `room_det` where (('$startDate'>`aztarikh` and '$startDate' <`tatarikh`) or ('$endDate'>`aztarikh` and '$endDate' <`tatarikh`) or ('$startDate' <= `aztarikh` and '$endDate' >=`tatarikh`)) and `room_id` in ($rooms) and `reserve_id`>0 order by `aztarikh`,`tatarikh`",$q);
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
			//mysql_class::ex_sql('select `id` from `room` where `hotel_id` = '.$this->id.' and `en` = 1	order by `room_typ_id`,`name`',$q);
			mysql_class::ex_sql('SELECT  `room`.`id` ,  `room`.`name` AS  `rname` ,  `room_typ`.`name` AS  `rtname` FROM  `room` LEFT JOIN  `room_typ` ON (  `room_typ_id` =  `room_typ`.`id` ) WHERE `en` = 1 and  `hotel_id` ='.$this->id.' order by `room_typ_id`,`room`.`name`',$q);
			while($r = mysql_fetch_array($q))
				$out[] = array('room_id'=>(int)$r['id'],'rname'=>$r['rname'],'rtname'=>$r['rtname']);
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
		public function loadDay($currentDate,$aztarikh,$tatarikh,$reserve_id,$next_aztarikh,$next_res,$oncl,$room_closed_class,&$indx0,$room_id,$isAdmin,$user_typ='')
		{
			$room_id_show = '';
			if($this->setRoomJavaScript)
				$room_id_show = ",$room_id";
			$is_admin = $isAdmin;
			$q = null;
			$next_res = (int)$next_res;
			$reserve_id = (int)$reserve_id;
			$t1 = '';
			$t2 = '';
			$css_class = 'room_closed';
			$ncss_class = 'room_closed';
			$my_daftar = (int)$_SESSION['daftar_id'];
			$q = null;
			$oncll = $oncl;
			$oncl1 = '';
			if($reserve_id > 0)
			{
				mysql_class::ex_sql("select `daftar`.`css_class`,`daftar`.`id` as `dafid` from `hotel_reserve` left join `ajans` on (`ajans_id` = `ajans`.`id`) left join `daftar` on (`daftar`.`id` = `ajans`.`daftar_id`) where `reserve_id` = $reserve_id",$q);
// 				echo "select `daftar`.`css_class`,`daftar`.`id` as `dafid` from `hotel_reserve` left join `ajans` on (`ajans_id` = `ajans`.`id`) left join `daftar` on (`daftar`.`id` = `ajans`.`daftar_id`) where `reserve_id` = $reserve_id".'<br/>';
				if($r = mysql_fetch_array($q))
{//echo 'd_id:'.$my_daftar.'<br/>';
					if((int)$r['dafid'] == $my_daftar || $is_admin || $user_typ=='dafater')
					{
						$css_class = $r['css_class'];
						if((int)$r['dafid'] == $my_daftar || $is_admin)	
							$oncl = (($oncl != '')?"onclick=\"$oncl($reserve_id,$room_id);\"":$oncl);
//echo '$reserve_id:'.$reserve_id.',$css_class:'.$css_class.'<br/>';
					}}
			}
			$q = null;
			if($next_res > 0)
                        {
                                mysql_class::ex_sql("select `daftar`.`css_class`,`daftar`.`id` as `dafid` from `hotel_reserve` left join `ajans` on (`ajans_id` = `ajans`.`id`) left join `daftar` on (`daftar`.`id` = `ajans`.`daftar_id`) where `reserve_id` = $next_res",$q);
                                if($r = mysql_fetch_array($q))
					if((int)$r['dafid'] == $my_daftar || $is_admin || $user_typ=='dafater')
					{
	                                        $ncss_class = $r['css_class'];
						if((int)$r['dafid'] == $my_daftar || $is_admin)
							$oncl1 = (($oncll != '')?"onclick=\"$oncll($next_res $room_id_show);\"":$oncll);
//echo '$reserve_id:'.$reserve_id.',$css_class:'.$css_class.'<br/>';
					}
                        }
			$out = "<table style=\"font-size:4px;width:100%;\" cellspacing=\"0\" cellpadding=\"0\">\n<tr>\n";
			$cd = strtotime($currentDate);
			$cd = strtotime(date("Y-m-d 00:00:00",$cd));
			$lcd = strtotime(date("Y-m-d 23:59:59",$cd));
			$cd14 = strtotime(date("Y-m-d 14:00:00",$cd));
			$az = strtotime($aztarikh);
			$ta = strtotime($tatarikh);
			$nz = strtotime($next_aztarikh);
			$first_paziresh = FALSE;
			$next_paziresh = FALSE;
			if($reserve_id > 0)
				$first_paziresh = reserve_class::isPaziresh($reserve_id);
			if($next_res > 0)
				$next_paziresh = reserve_class::isPaziresh($next_res);
			$indx1 = (($indx0 < count($room_closed_class)-1)?$indx0+1:0);
			/*$fill_char = "<span style=\"width:100%;background-color:#ffffff;color:#000;\">XXXXXX</span>";
			$fill_char2 = "<span style=\"width:100%;background-color:#ffffff;color:#000;\">XXXXXX</span>";*/
			$fill_char = "<span style=\"width:100%;background-color:#ffffff;color:".(hotel_class::classInversColor($css_class)).";\">XXXXXX</span>";
			$fill_char2 = "<span style=\"width:100%;background-color:#ffffff;color:".(hotel_class::classInversColor($ncss_class)).";\">XXXXXX</span>";
			$fill_char1 = "background-color:#ffffff;";
			if($lcd > $az and $cd < $ta)
			{
				$cdd = date("Y-m-d",$cd);
	                        $azz = date("Y-m-d",$az);
        	                $taa = date("Y-m-d",$ta);
				if($cd > $az && $lcd < $ta )
					$out .= "<td class = \"$css_class\"  $oncl >".(($first_paziresh)?$fill_char:'&nbsp;')."</td>";									
				else
				{
					if($az < $cd14)
					{
						$out .= "<td class = \"$css_class\" $oncl >".(($first_paziresh)?$fill_char:'&nbsp;')."</td>";
						if($ta <= $cd14 && $next_res <= 0)
							$out .= "<td class = \"room_opened\" width=\"50%\" >&nbsp;</td>";
						else if($ta <= $cd14 && $next_res > 0)
							$out .= "<td class = \"$ncss_class\" style=\"border-right: solid 1px;border-color: #fff;\" $oncl1 width=\"50%\" >".(($next_paziresh)?$fill_char2:'&nbsp;')."</td>";
					}
					else
					{
						$out .= "<td class = \"room_opened\" >&nbsp;</td>";
						$out .= "<td class = \"$css_class\" style=\"border-right: solid 1px;border-color: #fff;\" $oncl width=\"50%\">".(($first_paziresh)?$fill_char:'&nbsp;')."</td>";
					}
				}
			}
			else
				$out .= "<td class = \"room_opened\" $oncll >&nbsp;</td>";
			$out .= "</tr>\n</table>\n";
			return($out);
		}
		public function loadDates($startDate,$endDate,$room_id,$oncl,$room_closed_class,$isAdmin,$user_typ='')
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
					$out .= $this->loadDay(date("Y-m-d 00:00:00",$tmp),$aztarikh,$tatarikh,$reserve_id,$next_aztarikh,$next_res,$oncl,$room_closed_class,$indx,$room_id,$isAdmin,$user_typ);
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
		public function loadRooms($tarikh,$isAdmin,$oncl='',$user_typ='')
		{
			$conf = new conf;
			$out = "<table border=\"1\" style=\"width:99%;\" cellspacing=\"0\" cellpadding=\"0\">";
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
			if (!($isAdmin))
			{
				if ($user_typ == 'dafater')
				{
					$rooms_tmp = $this->loadTotalRoomsArray($startDate,$endDate);
					$rooms = array();
					foreach ($rooms_tmp as $i)
					{
						$r_id = $i['room_id'];
						$isGaranti = hotel_garanti_class::loadIsGarantyByRoomId($r_id);
						if (!($isGaranti))
							$rooms[] = array('room_id'=>(int)$i['room_id'],'rname'=>$i['rname'],'rtname'=>$i['rtname']);
					}
				}
				elseif ($user_typ == 'garanti')
				{
					$rooms_tmp = $this->loadTotalRoomsArray($startDate,$endDate);
					$rooms = array();
					foreach ($rooms_tmp as $i)
					{
						$r_id = $i['room_id'];
						$isGaranti = hotel_garanti_class::loadIsGarantyByRoomId($r_id);
						if ($isGaranti)
							$rooms[] = array('room_id'=>(int)$i['room_id'],'rname'=>$i['rname'],'rtname'=>$i['rtname']);
					}
				}
			}
			else
				$rooms = $this->loadTotalRoomsArray($startDate,$endDate);
			$out .= "<tr id='first_tr' >\n<th>اتاق</th>\n<th>نوع</th>\n".$this->loadShamsis($startDate,$endDate)."</tr>\n";
			$room_closed_class = array('room_closed','room_closed2');
			for($i = 0;$i < count($rooms);$i++)
			{
				//$room = new room_class($rooms[$i]['room_id']);
				//$room_typ = new room_typ_class($room->room_typ_id);
				$out .= "<tr>\n";
				//$out .= "<td title=\"جهت مشاهده تصاویر اتاق برروی نام آن کلیک کنید\"><u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('view_roompic.php?room_id=".$rooms[$i]['room_id']."&','',500,500);\" >".$room->name."</span></u></td>\n";
				$out .= "<td title=\"ﺞﻬﺗ ﻢﺷﺎﻫﺪﻫ ﺖﺻﺍﻭیﺭ ﺎﺗﺎﻗ ﺏﺭﺭﻭی ﻥﺎﻣ ﺂﻧ کﻝیک کﻥیﺩ\"><u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('view_roompic.php?room_id=".$rooms[$i]['room_id']."&','',500,500);\" >".$rooms[$i]['rname']."</span></u></td>\n";
				$out .= "<td>".$rooms[$i]['rtname']."</td>\n";
				$out .= $this->loadDates($startDate,$endDate,$rooms[$i]['room_id'],$oncl,$room_closed_class,$isAdmin,$user_typ);
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
			$aztarikh = $tarikh.' 14:00:00';
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
				$res_det = room_det_class::loadByReserve($reserves[$j]);
				$res_det =$res_det[0];
				$tedad+=$res_det[0]->nafar;
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
			mysql_class::ex_sql('select * from `hotel` where `is_our`>0 order by `name`',$q);
			while($r = mysql_fetch_array($q))
				$out[] = array('id'=>(int)$r['id'],'name'=>$r['name'],'info'=>((isset($r['info']) && ($r['info']!=null) && (trim($r['info'])!=''))?unserialize($r['info']):null));
			return($out);
		}
		public function colorInvers($color)
		{
			if($color!='' && $color != null && is_string($color))
			{
				$color = str_replace('#','',$color);
				$color = hex2bin($color) ;
				$color = bin2hex($color);
			}
			else
				$color = '000000';
			return '#'.$color;
		}
		public function classInversColor($cssClass)
		{
			$out = '';
			$color_tmp = explode("_",$cssClass);
			if (count($color_tmp)>2)
				$color = $color_tmp[2];
			elseif($cssClass=='room_closed')
				$color = '0b6049';
			elseif($cssClass=='room_closed2')
				$color = '49FE70';
			elseif($cssClass=='room_opened')
				$color = '0BEB49';
			else
				$color = '000000';
			//$color = daftar_class::loadRoomCss($cssClass);
			$out = hotel_class::colorInvers($color);
			return($out);
		}
		public function getSondogh($hotel_id,$getId = TRUE)
		{
			$hotel_id = (int)$hotel_id;
			if($hotel_id <= 0)
				$hotel_id = $this->id;
			$out = array();
			mysql_class::ex_sql("select `id`,`name` from `sandogh` where `hotel_id` = $hotel_id",$q);
			while($r = mysql_fetch_array($q))
				if($getId)
					$out[] = (int)$r['id'];
				else
					$out[$r['name']] = (int)$r['id'];
			return($out);
		}
		public function getRooms($hotel_id,$just_reserved=FALSE)
		{
			$out = array();
			$hotel_id = (int)$hotel_id;
			$tarikh = date("Y-m-d H:i:s");
			mysql_class::ex_sql("select `id`,`name` from `room` where `hotel_id` = $hotel_id",$q);
			while($r = mysql_fetch_array($q))
				if(room_class::getReserve($tarikh,(int)$r['id'])!=null || !$just_reserved)
					$out[] = array('id'=>(int)$r['id'],'name'=>$r['name']);
			return($out);
		}
		public function getRack1($hotel_id, $room_typ, $se, $tarikh = '') {
		    $out = array();
		    $hotel_id = (int) $hotel_id;
		    $room_typ = (int) $room_typ;
		    $allRooms = array();
		    $tabaghe = array();
		    if ($tarikh == '')
		        $tarikh = date("Y-m-d");

		    $day = date('Y-m-d');
		    if ($se->detailAuth('garanti')) {
		        $tabaghe_garanti = hotel_garanti_class::loadTabagheByHotelId($hotel_id);
		        $tabaghe = hotel_garanti_class::loadGarantyDaftar($_SESSION['daftar_id']);
		        if (count($tabaghe) == 0) {
		            mysql_class::ex_sql("select `tabaghe`,count(`id`) as `cid` from `room` where `en`=1 and `hotel_id`=$hotel_id group by `tabaghe` order by `tabaghe` desc", $q);
		            while ($r = mysql_fetch_array($q)) {
		                if (!(in_array($r['tabaghe'], $tabaghe_garanti))) {
		                    $tabaghe[] = $r['tabaghe'];
		                    $out[$r['tabaghe']] = array();
		                }
		            }
		        }
		    } else {
		        mysql_class::ex_sql("select `tabaghe`,count(`id`) as `cid` from `room` where `en`=1 and `hotel_id`=$hotel_id group by `tabaghe` order by `tabaghe` desc", $q);
		        while ($r = mysql_fetch_array($q)) {
		            $tabaghe[] = $r['tabaghe'];
		            $out[$r['tabaghe']] = array();
		        }
		    }
		    for ($i = count($tabaghe) - 1; $i >= 0; $i--) {
		        $j = 1;
		        if ($room_typ == -1)
		            mysql_class::ex_sql("select `id`,`name`,`tabaghe`,`vaziat`,`room_typ_id` from `room` where `en`=1 and `hotel_id`=$hotel_id and `tabaghe`=" . $tabaghe[$i] . " order by `name` ", $q);
		        else
		            mysql_class::ex_sql("select `id`,`name`,`tabaghe`,`vaziat`,`room_typ_id` from `room` where `en`=1 and `hotel_id`=$hotel_id and `tabaghe`=" . $tabaghe[$i] . " and `room_typ_id`='$room_typ' order by `name` ", $q);
		        while ($r = mysql_fetch_array($q)) {
		        	$show_prob = room_class::isProblem($hotel_id,$r['id']);
		        	$show_req = room_class::isReq($hotel_id,$r['id']);
		        	$typ_room = room_class::loadTypById($r["room_typ_id"]);
		            $r['show_prob'] = $show_prob;
		            $r['show_req'] = $show_req;
		            $r['room_typ'] = $typ_room;
		            $r['state'] = hotel_class::room_status_icon($r['vaziat']);
		            $info = room_class::getAnyReserve(date("Y-m-d"), $r['id']);
		            if ($info != null) {
		                for ($j = 0; $j < count($info); $j++) {
		                    $aj_name = hotel_class::loadAjans($info[$j]['reserve_id']);
		                    $info[$j]['reserve'] = new reserve_class($info[$j]['reserve_id']);
		                    $meh = new mehman_class();
		                    $info[$j]['mehman'] = $meh->loadByReserveId($info[$j]['reserve_id']);
		                    $info[$j]['ajans'] = $aj_name;
		                    $info[$j]['nafar'] = 0;
		                    if (!reserve_class::isKhorooj($info[$j]['reserve_id'], (int) $r['id'])) {
		                        $nafar = hotel_class::loadT_nafar($info[$j]['reserve_id']);
		                        $info[$j]['nafar'] = $nafar;
		                    }
		                    $tarikh_mehman = room_det_class::loadByReserve_habibi($info[$j]['reserve_id'], (int) $r['id']);
		                    $info[$j]['tarikh_mehman'] = $tarikh_mehman;
		                    $info[$j]['is_paziresh'] = FALSE;
		                    $info[$j]['is_khorooj'] = FALSE;
		                    if (isset($tarikh_mehman)) {
		                        $aztarikh = date(('Y-m-d'), strtotime($tarikh_mehman[0]));
		                        $tatarikh = date(('Y-m-d'), strtotime($tarikh_mehman[1]));
		                        if ((!reserve_class::isPaziresh($info[$j]['reserve_id'], (int) $r['id'])) && ($aztarikh == $day))
		                            $info[$j]['is_paziresh'] = TRUE;
		                        if ((strtotime($tatarikh) == strtotime($day)) && (!reserve_class::isKhorooj($info[$j]['reserve_id'], (int) $r['id']))) {
		                            $info[$j]['is_khorooj'] = TRUE;
		                        }
		                    }
		                }
		            }
		            $r['info'] = $info;
		            $out[$tabaghe[$i]][] = $r;
		            $j++;
		        }
		    }
		    return $out;
		}
		public function getRack_new($hotel_id,$room_typ,$se,$tarikh='')
		{
			$out = '';
			$hotel_id = (int) $hotel_id ;
			$room_typ = (int) $room_typ;
			$allRooms = array();
			$tabaghe =array();
			if($tarikh=='')
				$tarikh=date("Y-m-d");
			$out ="
<script language='javascript' >
function openRoomDet(room_id)

{
	wopen(\"gaantinfo.php?room_id=\"+room_id+\"&r=\"+Math.random()+\"&\",\"\",800,600);
	
}

function openRoomTamir(room_id)
{
	wopen(\"gaantinfo.php?room_id=\"+room_id+\"&r=\"+Math.random()+\"&\",\"\",800,600);
}
function fixProb(hotel_id,room_id)
{
	wopen(\"fixProb.php?hotel_id=\"+hotel_id+\"&room_id=\"+room_id+\"&r=\"+Math.random()+\"&\",\"\",800,600);
}
function fixReq(hotel_id,room_id)
{
	wopen(\"fixReq.php?hotel_id=\"+hotel_id+\"&room_id=\"+room_id+\"&r=\"+Math.random()+\"&\",\"\",800,600);

}
</script>
";
			$cid = 0;
			$day = date('Y-m-d');
			if ($se->detailAuth('garanti'))
			{
				$tabaghe_garanti = hotel_garanti_class::loadTabagheByHotelId($hotel_id);
				$tabaghe = hotel_garanti_class::loadGarantyDaftar($_SESSION['daftar_id']);
				if (count($tabaghe)==0)
				{
					mysql_class::ex_sql("select `tabaghe`,count(`id`) as `cid` from `room` where `en`=1 and `hotel_id`=$hotel_id group by `tabaghe` order by `tabaghe`",$q);
					while($r = mysql_fetch_array($q))
					{
						if (!(in_array($r['tabaghe'],$tabaghe_garanti)))
							$tabaghe[] = $r['tabaghe'];
						$cid = ($cid<(int)$r['cid'])?(int)$r['cid']:$cid;
					}
				}
			}
			else
			{
				mysql_class::ex_sql("select `tabaghe`,count(`id`) as `cid` from `room` where `en`=1 and `hotel_id`=$hotel_id group by `tabaghe` order by `tabaghe`",$q);
				while($r = mysql_fetch_array($q))
				{
					$tabaghe[] = $r['tabaghe'];
					$cid = ($cid<(int)$r['cid'])?(int)$r['cid']:$cid;
				}
			}	
			$out .= '<table  style="font-size:15px;font-weight:bold;">';
				for($i=count($tabaghe)-1;$i>=0;$i--)
				{					
					$j = 1;
					$out .= "<tr>";
					if ($room_typ==-1)
						mysql_class::ex_sql("select `id`,`name`,`tabaghe`,`vaziat`,`room_typ_id` from `room` where `en`=1 and `hotel_id`=$hotel_id and `tabaghe`=".$tabaghe[$i]." order by `name` ",$q);
					else
						mysql_class::ex_sql("select `id`,`name`,`tabaghe`,`vaziat`,`room_typ_id` from `room` where `en`=1 and `hotel_id`=$hotel_id and `tabaghe`=".$tabaghe[$i]." and `room_typ_id`='$room_typ' order by `name` ",$q);
					while($r = mysql_fetch_array($q))
					{
						$prob = '';
						$req = '';
						$fixed ='';
						$color = '#000000';
						$icon = '';
						$title = '';
						if($se->detailAuth('tasisat') || $se->detailAuth('modir')) 
						{
							$fixed = "onclick=\"fixProb('".$hotel_id."','".$r['id']."');\"";
						}
						else
							$fixed = "onclick=\"alert('شما مجاز به حل مشکل اتاق نیستید');\"";
						if($se->detailAuth('supervizer') || $se->detailAuth('modir')) 

						{
							$fixed_req = "onclick=\"fixReq('".$hotel_id."','".$r['id']."');\"";
						}
						else
							$fixed_req = "onclick=\"alert('شما مجاز به پاسخ به درخواست اتاق نیستید');\"";
						$show_prob = room_class::isProblem($hotel_id,$r['id']);
						if ($show_prob!=-1)
							$prob = "<img title='$show_prob' $fixed src='../img/alarm.gif'/>";
						$show_req = room_class::isReq($hotel_id,$r['id']);
						if ($show_req!=-1)
							$req = "<img title='$show_req' $fixed_req src='../img/guest_req.gif'/>";
						$typ_room = room_class::loadTypById($r["room_typ_id"]);
						$title .= $typ_room;	
						$state = hotel_class::room_status_icon($r['vaziat']);
						$room_name = $r['name'];
						$info = room_class::getAnyReserve(date("Y-m-d"),$r['id']);
						if ($r['vaziat']==4)
							$title = ','.hotel_class::loadTasisat($r['id']);
						if ($r['vaziat']==5)
							$title = ','.hotel_class::loadPoshtiban($r['id']);
						if($info!=null)
						{
							for($j=0;$j<count($info);$j++)
							{
								$aj_name = hotel_class::loadAjans($info[$j]['reserve_id']);
								if (!reserve_class::isKhorooj($info[$j]['reserve_id'],(int)$r['id']))
								{
									$color = '#ffffff';
									$nafar = hotel_class::loadT_nafar($info[$j]['reserve_id']);
									$title .= ','.'شماره رزرو:('.$info[$j]['reserve_id'].')'.'نام و نام خانوادگی:('.$info[$j]['fname'].$info[$j]['lname'].')'.'آژانس:('.$aj_name.')'.'شماره تلفن('.$info[$j]['tel'].')'.'نعداد نفرات('.$nafar.')';
								}
								$tarikh_mehman = room_det_class::loadByReserve_habibi($info[$j]['reserve_id'],(int)$r['id']);
								if (isset($tarikh_mehman))
								{
									$aztarikh = date(('Y-m-d'),strtotime($tarikh_mehman[0]));
									$tatarikh = date(('Y-m-d'),strtotime($tarikh_mehman[1]));
									if ((!reserve_class::isPaziresh($info[$j]['reserve_id'],(int)$r['id']))&&($aztarikh==$day))
										$icon .= "<img src='../img/e_icon.png'/>";
									if ((strtotime($tatarikh)==strtotime($day))&&(!reserve_class::isKhorooj($info[$j]['reserve_id'],(int)$r['id'])))
{
										$icon .= "<img src='../img/exit_i.png'/>";
								}
}
							}
						}
						$func = ((int)$r['vaziat']!=5)?"openRoomDet('".$r['id']."');":"openRoomTamir('".$r['id']."');";
						$out .= "<td align='center' style='-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; padding:3px;border-top: #000000 0px dashed;cursor:pointer;width:300px;height:50px;background-color:$state;font-size:12px;color:$color;'>".$prob.$req."<div title=\"$title\" onclick=\"$func\"><div style=\"position:relative;  z-index:5; display:inline;\">$room_name".'<br/>'."$icon</div></div></td>";
						$j++;
					}
					$out .= "</tr>";
				}
			$out .= '</table>';
			return $out;
		}
		public function getRack_khanedar($hotel_id,$room_typ,$tarikh='')
		{
			$out = '';
			$hotel_id = (int) $hotel_id ;
			$room_typ = (int) $room_typ;
			$allRooms = array();
			$tabaghe =array();
			if($tarikh=='')
				$tarikh=date("Y-m-d");
			$out ="
<script language='javascript' >
function openRoomDet_khanedar(room_id)

{
	wopen(\"gaantinfo_khanedar.php?room_id=\"+room_id+\"&r=\"+Math.random()+\"&\",\"\",800,600);
	
}
</script>";
			mysql_class::ex_sql("select `tabaghe`,count(`id`) as `cid` from `room` where `en`=1 and `hotel_id`=$hotel_id group by `tabaghe` order by `tabaghe`",$q);
			$cid = 0;
			$day = date('Y-m-d');
			while($r = mysql_fetch_array($q))
			{
				$tabaghe[] = $r['tabaghe'];
				$cid = ($cid<(int)$r['cid'])?(int)$r['cid']:$cid;
			}
			$out .= '<table  style="font-size:15px;font-weight:bold;">';
				for($i=count($tabaghe)-1;$i>=0;$i--)
				{					
					$j = 1;
					$out .= "<tr>";
					if ($room_typ==-1)
						mysql_class::ex_sql("select `id`,`name`,`tabaghe`,`vaziat` from `room` where `en`=1 and `hotel_id`=$hotel_id and `tabaghe`=".$tabaghe[$i]." order by `name` ",$q);
					else
						mysql_class::ex_sql("select `id`,`name`,`tabaghe`,`vaziat` from `room` where `en`=1 and `hotel_id`=$hotel_id and `tabaghe`=".$tabaghe[$i]." and `room_typ_id`='$room_typ' order by `name` ",$q);
					while($r = mysql_fetch_array($q))
					{
						$color = '#000000';
						$icon = '';
						$title = '';						
						$state = hotel_class::room_status_icon($r['vaziat']);
						$room_name = $r['name'];
						$info = room_class::getAnyReserve(date("Y-m-d"),$r['id']);
//var_dump($info);
						if($info!=null)
						{
							$room_id = $r['id'];
							for($j=0;$j<count($info);$j++)
							{
								mysql_class::ex_sql("select max(`mani_time`) as `ta` from `nezafat` where `room_id`='$room_id'",$q_room);			
								if($r_room = mysql_fetch_array($q_room))	
									$title = audit_class::hamed_pdate($r_room["ta"]);
								$tarikh_mehman = room_det_class::loadByReserve_habibi($info[$j]['reserve_id'],(int)$r['id']);
								if (isset($tarikh_mehman))
								{
									$aztarikh = date(('Y-m-d'),strtotime($tarikh_mehman[0]));
									$tatarikh = date(('Y-m-d'),strtotime($tarikh_mehman[1]));
									if ((!reserve_class::isPaziresh($info[$j]['reserve_id'],(int)$r['id']))&&($aztarikh==$day))
										$icon .= "<img src='../img/e_icon.png'/>";
									if ((strtotime($tatarikh)==strtotime($day))&&(!reserve_class::isKhorooj($info[$j]['reserve_id'],(int)$r['id'])))
{
										$icon .= "<img src='../img/exit_i.png'/>";
								}
}
							}
						}
						$func = "openRoomDet_khanedar('".$r['id']."');";
						$out .= "<td title='$title' onclick=\"$func\" align='center' style='	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; padding:3px;border-top: #000000 0px dashed;cursor:pointer;width:300px;height:50px;background-color:$state;font-size:12px;color:$color;'><div style=\"position:relative;  z-index:5; display:inline;\">$room_name".'<br/>'."$icon</div></td>";
						$j++;
					}
					$out .= "</tr>";
				}
			$out .= '</table>';
			return $out;
		}
		public function loadAjans($resereve_id)
		{
			$aj_name = '';
			$resereve_id = (int)$resereve_id;
			mysql_class::ex_sql("select `ajans_id` from `hotel_reserve` where `reserve_id` = $resereve_id",$q);
			if($row = mysql_fetch_array($q))
			{
				$aj_id = $row["ajans_id"];
				mysql_class::ex_sql("select `id`,`name` from `ajans` where `id` = $aj_id",$qj);
				if($rj = mysql_fetch_array($qj))
					$aj_name = $rj["name"];
			}
			return $aj_name;
		}
		public function loadT_nafar($resereve_id)
		{
			$t_nafar = '';
			$resereve_id = (int)$resereve_id;
			mysql_class::ex_sql("select `nafar`,`reserve_id` from `room_det` where `reserve_id` = $resereve_id",$q);
			if($row = mysql_fetch_array($q))
				$t_nafar = $row["nafar"];
			return $t_nafar;
		}
		public function loadTasisat($room_id)
		{
			$title= '';
			mysql_class::ex_sql("select `id`,`room_id`,`toz` from `tasisat` where `room_id`='$room_id' order by `regdate` DESC",$qtasis);
			if($rtasis = mysql_fetch_array($qtasis))
			{
				if($rtasis['toz']!="")
					$title = $rtasis['toz'];
				else
					$title = 'دلیلی برای تعمیرات ثبت نگردیده است';
			}
			else
				$title = 'دلیلی برای تعمیرات ثبت نگردیده است';
			return $title;
		}
		public function loadPoshtiban($room_id)
		{
			$title= '';
			mysql_class::ex_sql("select `id`,`room_id`,`toz` from `poshtiban` where `room_id`='$room_id' order by `regdate` DESC",$qtasis);
			if($rtasis = mysql_fetch_array($qtasis))
			{
				if($rtasis['toz']!="")
					$title = $rtasis['toz'];
				else
					$title = 'دلیلی برای پشتیبان ثبت نگردیده است';
			}
			else
				$title = 'دلیلی برای پشتیبان ثبت نگردیده است';
			return $title;
		}
		public function getRack($hotel_id,$tarikh='')
		{
			$hotel_id = (int) $hotel_id ;
			$allRooms = array();
			$tabaghe =array();
			if($tarikh=='')
				$tarikh=date("Y-m-d");
			mysql_class::ex_sql("select `tabaghe`,count(`id`) as `cid` from `room` where `en`=1 and `hotel_id`=$hotel_id group by `tabaghe` order by `tabaghe`",$q);
			$cid = 0;
			while($r = mysql_fetch_array($q))
			{
				$tabaghe[] = $r['tabaghe'];
				$cid = ($cid<(int)$r['cid'])?(int)$r['cid']:$cid;
			}
			$out ="
<script language='javascript' >
function openRoomDet(room_id)
{
	$.window({
                title: \"اطلاعات اتاق\",
                width: 700,
                height: 400,
                content: $(\"#window_block8\"),
                containerClass: \"my_container\",
                headerClass: \"my_header\",
                frameClass: \"my_frame\",
                footerClass: \"my_footer\",
                selectedHeaderClass: \"my_selected_header\",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: \"gaantinfo.php?room_id=\"+room_id+\"&r=\"+Math.random()+\"&\"
        });
}
function openRoomTamir(room_id)
{
	$.window({
                title: \"اطلاعات تأسیسات\",
                width: 700,
                height: 400,
                content: $(\"#window_block8\"),
                containerClass: \"my_container\",
                headerClass: \"my_header\",
                frameClass: \"my_frame\",
                footerClass: \"my_footer\",
                selectedHeaderClass: \"my_selected_header\",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: \"gaantinfo.php?room_id=\"+room_id+\"&r=\"+Math.random()+\"&\"
        });
}
</script>
";
			$today = Date("Y-m-d 00:00:00 ");
			$today1 = Date("Y-m-d 23:59:59 ");
			$day2 = Date("Y-m-d 14:00:00 ");
			$aj_name = "";
			$y = Date("Y");
			$m = Date("m");
			$d = Date("d");
			$reserve_kh = "";
			$day =mktime("14","00","00",$m,$d,$y);
			$out .= '<table  style="font-size:15px;font-weight:bold;">';
			//$out .="<tr><th>طبقه</th><th colspan='$cid' >اتاق‌ها</th></tr>\n";
			for($i=count($tabaghe)-1;$i>=0;$i--)
			{
				$out.='<tr>';
				$q= null;
				mysql_class::ex_sql("select `id`,`name`,`tabaghe`,`vaziat` from `room` where `en`=1 and `hotel_id`=$hotel_id and `tabaghe`=".$tabaghe[$i]." order by `name` ",$q);
				$crow = mysql_num_rows($q);
				$crow = $cid - $crow ;
$nafar = 0;
				while($r = mysql_fetch_array($q))
				{
$kh = '';
$kh1 = '';
					$id = $r["id"];
				/*	mysql_class::ex_sql("select `id`,`room_id`,`aztarikh`,`tatarikh`,`nafar` from `room_det` where `room_id`='$id' and `aztarikh`='$today' and `aztarikh`<='$today1'",$qq);
//echo "select `id`,`room_id`,`aztarikh`,`tatarikh`,`nafar` from `room_det` where `room_id`='$id' and `aztarikh`>='$today' and `aztarikh`<='$today1'"."<br/>";
					if ($row = mysql_fetch_array($qq))
					//	$nafar = $row["nafar"];
echo "hh";*/
					$info = room_class::getAnyReserve(date("Y-m-d"),$r['id']);
					//var_dump($info);
					$title = '';
					$color = 'black';					
					$state = hotel_class::room_status_icon($r['vaziat']);
					if($info!=null)
					{
						$color = '#6ff3f8';
						for($j=0;$j<count($info);$j++)
						{
							$r_id = $info[$j]['reserve_id'];
							mysql_class::ex_sql("select * from `hotel_reserve` where `reserve_id` = $r_id",$qf);
							if($rf = mysql_fetch_array($qf))
							{
								$aj_id = $rf["ajans_id"];
								mysql_class::ex_sql("select `id`,`name` from `ajans` where `id` = $aj_id",$qj);
								if($rj = mysql_fetch_array($qj))
									$aj_name = $rj["name"];
							}
							mysql_class::ex_sql("select `nafar`,`reserve_id`,`aztarikh`,`tatarikh` from `room_det` where `reserve_id` = $r_id",$qr);

							if($rr = mysql_fetch_array($qr))
							{
								mysql_class::ex_sql("select `id`,`reserve_id`,`khorooj` from `mehman` where `reserve_id` = $r_id",$qm);
								if($rm = mysql_fetch_array($qm))
								{
									if ($rm['khorooj']!='0000-00-00 00:00:00')
										$reserve_kh = $rm['reserve_id'];
								}
								$nafar= $rr["nafar"];
								$tatarikh= $rr["tatarikh"];
								$aztarikh= $rr["aztarikh"];
								$ta_ye = substr($tatarikh,0,4);
								$ta_mo = substr($tatarikh,5,2);
								$ta_da = substr($tatarikh,8,2);
								$tmp_tatarikh =mktime("14","00","00",$ta_mo,$ta_da,$ta_ye);
								$az_ye = substr($aztarikh,0,4);
								$az_mo = substr($aztarikh,5,2);
								$az_da = substr($aztarikh,8,2);
								$tmp_aztarikh =mktime("14","00","00",$az_mo,$az_da,$az_ye);
								if ($tmp_aztarikh == $day)
									$kh .= "<img src='../img/e_icon.png'/>";
								if (($tmp_tatarikh == $day)&&($reserve_kh!=$r_id))
									$kh .= "<img src='../img/exit_i.png'/>";
							}
							if ($reserve_kh!=$r_id)
								$title .= 'شماره رزرو:('.$info[$j]['reserve_id'].')  نام و نام خانوادگی: ('.$info[$j]['lname'].') تلفن: ('.$info[$j]['tel'].')نام آژانس:('.$aj_name.') '.'تعداد نفرات:('.$nafar.')  ';		
							else
								$color = 'black';		
						}
					}
					$func = ((int)$r['vaziat']!=4)?"openRoomDet('".$r['id']."');":"openRoomTamir('".$r['id']."');";
					$out .= "<td title='$title' onclick=\"$func\" align='center' style='	-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; padding:3px;border-top: #000000 0px dashed;cursor:pointer;color:$color;width:300px;height:50px;background-color:$state;font-size:12px;'><div style=\"position:relative;  z-index:5; display:inline;\">".$r['name'].'<br/>'.$kh."</div></td>";
				}
				$out.=($crow>0)?"<td style='border-top: #000000 0px dashed;' colspan='$crow'>&nbsp;</td>":'';
				$out.='</tr>';
			}
			$out .='</table>';
			return $out;
		}
		public function room_status($stat)
		{
			$out[0] = 'اشغال شده';
			$out[1] = 'خالی اما نظافت نشده';
			$out[2] = 'خالی و نظافت شده';
			$out[3] = 'درحال نظافت';
			$out[4] = 'در حال تعمیر';
			$out[5] = 'خارج از سرویس';
			return($out[$stat]);
		}
		public function room_status_icon($stat)
		{
			if ($stat == 0)
				$out = "#b72b13";
			if ($stat == 1)
				$out = "#f1ca00";
			if ($stat == 2)
				$out = "#0c5e06";
			if ($stat == 3)
				$out = "#034da2";
			if ($stat == 4)
				$out = "#a38fb3";
			if ($stat == 5)
				$out = "#ff7103";
				
			//$out = "<img style='margin:5px;' height=\"20px\" src = \"../img/$stat.png\" title=\"".hotel_class::room_status($stat)."\" alt=\"".hotel_class::room_status($stat)."\"/>";
			return($out);
		}
		public function add($name='')
		{
			$out = array();
			mysql_class::ex_sql("select `id` from `hotel` where `name` = '$name'",$q);
			if($r = mysql_fetch_array($q))
			{
				$out['hotel_id'] =  (int)$r['id'];
				$q = null;
				$i = 0;
				mysql_class::ex_sql("select `id` from `khadamat` where `hotel_id` = ".$out['hotel_id']." and `en` = 1",$q);
				while($r = mysql_fetch_array($q))
				{
					switch($i)
					{
						case 0:
							$out['sobhane'] = (int)$r['id'];
							break;
						case 1:
							$out['nahar'] = (int)$r['id'];
                                                        break;
						case 2:
							$out['sham'] = (int)$r['id'];
                                                        break;
						case 3:
							$out['transfer'] = (int)$r['id'];
                                                        break;
					}
					$i++;
				}
			}
			else
			{
		                $kol_id = kol_class::addById($name);
        		        $moeen_id = moeen_class::addById($kol_id,'درآمد رزرواسیون '.$name);
                		$moeen_hazine_id = moeen_class::addById($kol_id,'هزینه غذای '.$name);
                		$query="insert into `hotel` (`name`, `moeen_id`, `is_our`, `ghaza_moeen_id`) values ('$name',$moeen_id,2,$moeen_hazine_id)";
		                $ln = mysql_class::ex_sqlx($query,FALSE);
				$out['hotel_id'] =  mysql_insert_id($ln);
				$id = $out['hotel_id'];
				mysql_close($ln);
				mysql_class::ex_sqlx("insert into `hotel_working_date` (`hotel_id`, `aztarikh`, `tatarikh`, `ghimat`) values ($id,'2010-01-01 00:00:00','2020-01-01 23:59:59',1)");
				$ln = mysql_class::ex_sqlx("insert into `khadamat` (`hotel_id`, `name`,`typ`, `aval_ekhtiari`) values ($id,'صبحانه',0,1)",FALSE);
				$out['sobhane'] = mysql_insert_id($ln);
				mysql_close($ln);
				$ln = mysql_class::ex_sqlx("insert into `khadamat` (`hotel_id`, `name`,`typ`, `aval_ekhtiari`) values ($id,'ناهار',0,1)",FALSE);
				$out['nahar'] = mysql_insert_id($ln);
				mysql_close($ln);
				$ln = mysql_class::ex_sqlx("insert into `khadamat` (`hotel_id`, `name`,`typ`, `aval_ekhtiari`) values ($id,'شام',0,2)",FALSE);
				$out['sham'] = mysql_insert_id($ln);
				mysql_close($ln);
				$ln = mysql_class::ex_sqlx("insert into `khadamat` (`hotel_id`, `name`,`typ`, `aval_ekhtiari`) values ($id,'ترانسفر',1,0)",FALSE);
				$out['transfer'] = mysql_insert_id($ln);
				mysql_close($ln);
			}
			return($out);
		}
		public function getFloatHotel()
		{
			$out = array();
			mysql_class::ex_sql("select `id`,`name` from `hotel` where `is_our` = 2",$q);
			while($r = mysql_fetch_array($q))
				$out[(int)$r['id']] = $r['name'];
			return($out);
		}
	}
?>