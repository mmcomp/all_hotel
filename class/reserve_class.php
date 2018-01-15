<?php
	class reserve_class
	{
		public $id = 0;
		public $hotel_reserve = null;
		public $room_det = null;
		public $room = null;
		public $khadamat_det = null;
		public $sanad_reserve = null;
		public $hotel_id = -1;
		public $output = null;
		public $editable = TRUE;
		public $out = FALSE;
		public $isOnline = FALSE;
		public $watcherKeys = array();
		public function __construct($id = 0)
		{
			$id = (int)$id;
			$out = FALSE;
			if($id != 0)
			{
				$hotel_re = new hotel_reserve_class;
				if($hotel_re->loadByReserve($id))
				{
					$this->id = $id;
					$this->hotel_reserve = $hotel_re;
					$this->isOnline = $hotel_re->isOnline;
					$this->output['hotel_reserve'] = $hotel_re;
				}
				$room_d = new room_det_class;
				$room_d = $room_d->loadByReserve($id);
				if($room_d[0] !== FALSE)
				{
					$this->room_det = $room_d[0];
					$this->output['room_det'] = $room_d[0];
					$tmp_room = new room_class($room_d[0][0]->room_id);
					$this->hotel_id = $tmp_room->hotel_id;
					$this->editable = $room_d[1];
					$out = TRUE;
				}
				else
					$this->editable = FALSE;
				$room = new room_class;
				$room = $room->loadByReserve($id);
				if($room !== FALSE)
        	                {
                	                $this->room = $room;
                        	        $this->output['room'] = $room;
                                	$out = TRUE;
	                        }
				$khadamat_d = new khadamat_det_class;
				$khadamat_d = $khadamat_d->loadByReserve($id);
				if($khadamat_d!==FALSE)
				{
					$this->khadamat_det = $khadamat_d;
					$this->output['khadamat_det'] = $khadamat_d;
				}
				if($sanad_r = new sanad_reserve_class($id))
				{
					$this->sanad_reserve = $sanad_r;
					$this->output['sanad_reserve'] = $sanad_r;
				}
			}
			$this->out = $out;
		}
		public function roomIsReserved($room_id,$room=null)
		{
			$out = FALSE;
			if($room == null)
				$room = $this->room;
			if($room != null)
				for($i = 0;$i<count($room);$i++)
					if($room[$i]->id == (int)$room_id)
						$out = TRUE;
			return($out);
		}
		public function loadWatcher()
		{
			if($this->id > 0)
			{	
				$tmp_aj = new ajans_class($this->hotel_reserve->ajans_id);
				$tmp_rm = new room_class($this->room_det[0]->room_id);
				$tmp_ht = new hotel_class($tmp_rm->hotel_id);
				$this->watcherKeys['ajans'] = array('value'=>$tmp_aj->name,'help'=>'نام آژانس');
				$this->watcherKeys['reserve_id'] = array('value'=>$this->id,'help'=>'شماره رزرو');
				$this->watcherKeys['hotel_mablagh'] =array('value'=> $this->hotel_reserve->m_hotel,'help'=>'مبلغ هتل');
				$this->watcherKeys['hotel_belit1'] = array('value'=> $this->hotel_reserve->m_belit1,'help'=>'مبلغ بلیت رفت');
				$this->watcherKeys['hotel_comision'] = array('value'=> $this->hotel_reserve->m_belit1,'help'=>'مبلغ کمیسیون');
				$this->watcherKeys['hotel_belit2'] = array('value'=> $this->hotel_reserve->m_belit2,'help'=>'مبلغ بلیت برگشت');
				$this->watcherKeys['hotel'] = array('value'=> $tmp_ht->name,'help'=>'نام هتل');
				$this->watcherKeys['input'] = array('value'=> '<input type="text" class="inp value="" />','help'=>'ورود اطلاعات');
			}
			else
			{
				$this->watcherKeys['ajans'] = array('value'=>'','help'=>'نام آژانس');
                                $this->watcherKeys['reserve_id'] = array('value'=>0,'help'=>'شماره رزرو');
                                $this->watcherKeys['hotel_mablagh'] = array('value'=>0,'help'=>'مبلغ هتل');
                                $this->watcherKeys['hotel_belit1'] = array('value'=>0,'help'=>'مبلغ بلیت رفت');
                                $this->watcherKeys['hotel_comision'] = array('value'=>0,'help'=>'مبلغ کمیسیون');
                                $this->watcherKeys['hotel_belit2'] = array('value'=>0,'help'=>'مبلغ بلیت برگشت');
                                $this->watcherKeys['hotel'] = array('value'=>'','help'=>'نام هتل');
                                $this->watcherKeys['input'] = array('value'=>'','help'=>'ورود اطلاعات');
			}
		}
		public function watcherAdd($key,$value,$help='')
                {
                        $this->watcherKeys[$key] = array('value'=>$value,'help'=>$help);
                }
		public function watcherLoad($key)
		{
			$out = ((isset($this->watcherKeys[$key]))?$this->watcherKeys[$key]:' خطا ');
		}
		public function watcherCompile($str)
		{
			$out = $str;
			foreach($this->watcherKeys as $key=>$value)
				$out = str_replace("#$key#",$value['value'],$out);
			return($out);
		}
		public function get()
		{
			$reserve_id = 0;
                        $q = null;
                        mysql_class::ex_sql("select MAX(abs(`reserve_id`)) as `mrs` from `room_det` ",$q);
                        if($r = mysql_fetch_array($q))
	                        $reserve_id = (int)$r["mrs"];
                        $reserve_id++;
                        $reserve_id =(($reserve_id<=0)?1:$reserve_id);
			return($reserve_id);
		}
		public function reserveByTel($tel,$is_ghimat = TRUE)
		{
			$out = 0;
			$tmp = $tel;
			$tel = str_split($tel);
			
			if($tel[0] == '+')
			{
				$tmp = '0';
				for($i=3;$i<count($tel);$i++)
					$tmp.=$tel[$i];
			}
			$tel=$tmp;
			mysql_class::ex_sql("select `reserve_id` from `hotel_reserve` where ".($is_ghimat?'`sms_ghimat`=-1':'`sms_vaz`=-1')." and `tozih` = '$tel' order by `reserve_id` desc limit 1",$q);
			if($r = mysql_fetch_array($q))
				$out = (int)$r['reserve_id'];
			return $out;
		}
		public function canPaziresh($reserve_id)
		{
			$out = array();
			$reserve_id = (int)$reserve_id;
			if($reserve_id>0)
                        {
                                mysql_class::ex_sql("select `aztarikh`,`room_id` from `room_det` where `reserve_id`=$reserve_id  order by `aztarikh`",$q);
                                while($r = mysql_fetch_array($q))
				{
					$aztarikh = $r['aztarikh'];
					$room_id = (int)$r['room_id'];
					$res = room_class::getReserve($aztarikh,$room_id);
					//echo "aztarikh = '$aztarikh' , room_id = '$room_id' : ".var_export($res,TRUE)."<br/>\n";
					if(is_array($res))
						foreach($res as $rr)
							if((int)$rr['reserve_id']!=$reserve_id)
								if(!in_array((int)$rr['reserve_id'],$out))
									$out[] = (int)$rr['reserve_id'];
				}
                        }
                        return $out;
		}
		public function isPaziresh($reserve_id,$room_id=-1)
		{
			$out = FALSE;
			$reserve_id = (int)$reserve_id;
			if($reserve_id>0)
			{
				$room_shart='';
				if($room_id>0)
					$room_shart = " and `room_id`=$room_id";
				mysql_class::ex_sql("select `id` from `mehman` where `reserve_id`=$reserve_id $room_shart",$q);
				if($r = mysql_fetch_array($q))
					$out = TRUE;
			}
			return $out;
		}
		public function isKhorooj($reserve_id,$room_id=-1)
		{
			$out = FALSE;
			$reserve_id = (int)$reserve_id;
			if($reserve_id>0)
			{
				$room_shart='';
				if($room_id>0)
					$room_shart = " and `room_id`=$room_id";
				mysql_class::ex_sql("select `id`,`khorooj` from `mehman` where `reserve_id`=$reserve_id $room_shart",$q);
				if($r = mysql_fetch_array($q))
					if($r['khorooj']!='0000-00-00 00:00:00')
						$out = TRUE;
			}
			return $out;
		}
		public function loadFactors($reserve_id)
		{
			$reserve_id = (int)$reserve_id;
			$factor_shomares = array();
			mysql_class::ex_sql("select `factor_shomare` from `sandogh_factor` where `reserve_id` = $reserve_id and `en` = 1 group by `factor_shomare` order by `factor_shomare` desc",$q);
			while($r = mysql_fetch_array($q))
				$factor_shomares[] = $r['factor_shomare'];
			return($factor_shomares);
		}
		public function loadReservesBetween($aztarikh,$tatarikh='')
		{
			$out = array();
			if($tatarikh == '')
				$tatarikh = $aztarikh;
			mysql_class::ex_sql("select `reserve_id` from `room_det` where`reserve_id` > 0 and  date(`tatarikh`) >= '$aztarikh' and date(`tatarikh`) <= '$tatarikh' GROUP BY `reserve_id`",$q);
//echo "select `reserve_id` from `room_det` where`reserve_id` > 0 and  date(`tatarikh`) >= '$aztarikh' and date(`tatarikh`) <= '$tatarikh'";
			while($r = mysql_fetch_array($q))
				$out[] = (int)$r['reserve_id'];
			return($out);
		}	
		public function loadReservesBetween_room($aztarikh,$tatarikh='')
		{
			$out = array();
			if($tatarikh == '')
				$tatarikh = $aztarikh;
			mysql_class::ex_sql("select `room_id` from `room_det` where`reserve_id` > 0 and  date(`tatarikh`) >= '$aztarikh' and date(`tatarikh`) <= '$tatarikh' GROUP BY `reserve_id`",$q);
//echo "select `reserve_id` from `room_det` where`reserve_id` > 0 and  date(`tatarikh`) >= '$aztarikh' and date(`tatarikh`) <= '$tatarikh'";
			while($r = mysql_fetch_array($q))
				$out[] = (int)$r['room_id'];
			return($out);
		}
		public function isPick($reserve_id=0)
		{
			$out = FALSE;
			$reserve_id = (int)$reserve_id;
			if($reserve_id == 0)
				$reserve_id = $this->id;
			mysql_class::ex_sql("select MIN(`aztarikh`) as `azt` , MAX(`tatarikh`) as `tat`, `room_id` from `room_det` where `reserve_id` = $reserve_id",$q);
			if($r = mysql_fetch_array($q))
			{
				$ro = new room_class((int)$r['room_id']);
				$hotel = new hotel_class($ro->hotel_id);
				$aztarikh = $r['azt'];
				$tatarikh = $r['tat'];
				$tmp = $aztarikh;
        	                while(strtotime($tmp) <= strtotime($tatarikh))
                	        {
                                	if($hotel->isPick($tmp))
                                        	$out = TRUE;
	                                $tmp = date("Y-m-d 14:00:00",strtotime($tmp.' + 1 day'));
        	                }
			}
			return($out);
		}
	}
?>
