<?php
	class room_class
	{
		public $id=-1;
		public $hotel_id=-1;
		public $room_typ_id=-1;
		public $name="";
		public $tozih="";
		public $en = 1;
		public $zarfiat_max = 0;
		public $vaziat = 2;
		public $moeen_id = -1;
		public $tabaghe = -1;
		public function __construct($id=-1)
		{
			$id = (int)$id;
			if ($id>0)
			{
				mysql_class::ex_sql("select * from `room` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id=$r['id'];
					$this->hotel_id=$r['hotel_id'];
					$this->room_typ_id=$r['room_typ_id'];
					$this->name=$r['name'];
					$this->tozih=$r['tozih'];
					$this->en = (int)$r['en'];
					$this->zarfiat_max = (int)$r['zarfiat_max'];
					$this->vaziat = (int)$r['vaziat'];
					$this->moeen_id = (int)$r['moeen_id'];
					if(isset($r['tabaghe']))
						$this->tabaghe = (int)$r['tabaghe'];
					$this->end_fix_date = $r['end_fix_date'];
				}
			}
		}
		public function loadTypById($room_id = 0)
		{
			$out = '';
			mysql_class::ex_sql("select `name` from `room_typ` where `id` = $room_id",$q);
			while($r = mysql_fetch_array($q))
				$out = $r['name'];
			return($out);
		}
		public function loadByReserve($reserve_id = 0)
		{
			$out = FALSE;
			$reserve_id = (int)$reserve_id;
			mysql_class::ex_sql("select `room_id` from `room_det` where `reserve_id` = $reserve_id",$q);
			while($r = mysql_fetch_array($q))
			{
				$out[] = new room_class((int)$r['room_id']);
			}
			return($out);
		}
		public function loadById($id = -1)
		{
			$out = FALSE;
			$id = (int)$id;
			mysql_class::ex_sql("select `name` from `room` where `id` = $id",$q);
			while($r = mysql_fetch_array($q))
			{
				$out = $r['name'];
			}
			return($out);
		}
		public function loadHotelByReserve($reserve_id = 0)
		{
			$out = "";
			$reserve_id = (int)$reserve_id;
			mysql_class::ex_sql("select `room_id` from `room_det` where `reserve_id` = $reserve_id",$q);
			while($r = mysql_fetch_array($q))
			{
				$id = $r["room_id"];
				mysql_class::ex_sql("select `hotel_id` from `room` where `id` = $id",$q);
				if ($row = mysql_fetch_array($q))
					$out = $row["hotel_id"];
			}
			return($out);
		}
		public function loadHotelName($hotel_id = 0)
		{
			$out = "";
			mysql_class::ex_sql("select `name` from `hotel` where `id` = $hotel_id",$q);
			if ($row = mysql_fetch_array($q))
				$out = $row["name"];
			return($out);
		}
		public function isProblem($hotel_id,$room_id)
		{
			$out = "";
			mysql_class::ex_sql("select `toz` from `tasisat_tmp` where `hotel_id` = '$hotel_id' and `room_id`='$room_id' and `en`!='1'",$q);
			if ($row = mysql_fetch_array($q))
				$out = $row["toz"];
			else
				$out = -1;
			return($out);
		}
		public function isReq($hotel_id,$room_id)
		{
			$out = "";
			mysql_class::ex_sql("select `toz` from `guest_req` where `hotel_id` = '$hotel_id' and `room_id`='$room_id' and `en`!='1'",$q);
			if ($row = mysql_fetch_array($q))
				$out = $row["toz"];
			else
				$out = -1;
			return($out);
		}
		public function loadOpenRoomsByTyp($startDate,$delay,$voroodi,$khorooji,$hotel_id,$typ)
		{
			$out = array();
                        $delay = (int)$delay;
                        $hotel_id = (int)$hotel_id;
                        $room_frase = "";
                        $endDate = date("Y-m-d H:i:s",strtotime($startDate." + $delay days"));
                        if($voroodi)
                                $startDate = date("Y-m-d 00:00:00",strtotime($startDate));
                        if($khorooji)
                                $endDate = date("Y-m-d 21:00:00",strtotime($endDate));
                        if($hotel_id <= 0)
                        {
                                $hotel_id = $this->hotel_id;
                                $room_frase = " and `id` = ".$this->id;
                        }
			$arr = mysql_class::getInArray("room_id","room_det","(('$startDate'>`aztarikh` and '$startDate' <`tatarikh`) or ('$endDate'>`aztarikh` and '$endDate' <`tatarikh`) or ('$startDate' <= `aztarikh` and '$endDate' >=`tatarikh`)) and `reserve_id`>0");
			$in_query = ($arr!=null)?"not(`id` in ($arr)) and":'';
                        mysql_class::ex_sql("SELECT  `id` FROM `room` WHERE `hotel_id` = $hotel_id $room_frase and $in_query `en` = 1 and `room_typ_id`=$typ and `vaziat` <> 5 order by `room`.`id`",$q);
                        while($r = mysql_fetch_array($q))
                        {
                                $out[] = (int)$r['id'];
                        }
			return($out);
		}
                public function loadRoomsByTyp($hotel_id,$typ,$shart = '')
                {
                        $out = array();
                        $hotel_id = (int)$hotel_id;
                        $room_frase = "";
                        if($hotel_id <= 0)
                        {
                                $hotel_id = $this->hotel_id;
                                $room_frase = " and `id` = ".$this->id;
                        }
                        mysql_class::ex_sql("SELECT  `id` FROM `room` WHERE `hotel_id` = $hotel_id $room_frase and `en` = 1 and `room_typ_id`=$typ and (`vaziat` <> 5 $shart) order by `room`.`id`",$q);
                        while($r = mysql_fetch_array($q))
                                $out[] = (int)$r['id'];
                        return($out);
                }
		public function loadTypDetails($room_ids)
		{
			$out = '';
			$tmp = array();
			for($i = 0;$i < count($room_ids);$i++)
			{
				$r = new room_class((int)$room_ids[$i]);
				if(isset($tmp[$r->room_typ_id]))
					$tmp[$r->room_typ_id]=$tmp[$r->room_typ_id]+1;
				else
					$tmp[$r->room_typ_id] = 1;
			}
			foreach($tmp as $room_typ_id => $count)
			{
				$r = new room_typ_class($room_typ_id);
				$out .= (($out != '')?',':'').$r->name.' '.$count.' ';
			}
			return($out);
		}
		public function loadOpenRooms($startDate,$delay,$voroodi,$khorooji,$hotel_id,$zarfiat=0)
		{
			$out = array();
			$delay = (int)$delay;
			$hotel_id = (int)$hotel_id;
			$room_frase = '';
			$endDate = date("Y-m-d H:i:s",strtotime($startDate." + $delay days"));
			if($voroodi)
				$startDate = date("Y-m-d 00:00:00",strtotime($startDate));
			if($khorooji)
				$endDate = date("Y-m-d 21:00:00",strtotime($endDate));
			if($hotel_id <= 0)
			{
				$hotel_id = $this->hotel_id;
				$room_frase = " and `id` = ".$this->id;
			}
			$arr = mysql_class::getInArray("room_id","room_det","(('$startDate'>`aztarikh` and '$startDate' <`tatarikh`) or ('$endDate'>`aztarikh` and '$endDate' <`tatarikh`) or ('$startDate' <= `aztarikh` and '$endDate' >=`tatarikh`)) and `reserve_id`>0");
			$in_query = ($arr!=null)?"not(`id` in ($arr)) and":'';
			mysql_class::ex_sql("SELECT `room_typ_id` , count( `id` ) as `co`,`ghimat` FROM `room` WHERE `zarfiat_max`>=$zarfiat and `hotel_id` = $hotel_id $room_frase and $in_query `en` = 1  and `vaziat` <> 5  group by `room_typ_id`",$q);
			while($r = mysql_fetch_array($q))
			{
				$tmp = new room_typ_class((int)$r["room_typ_id"]);
				$out[] = array("room_typ_id"=>(int)$r["room_typ_id"],"count"=>(int)$r["co"],"name"=>$tmp->name,"ghimat"=>(int)$r["ghimat"],'zarfiat'=>$tmp->zarfiat,'room_ids'=>room_class::loadOpenRoomsByTyp($startDate,$delay,$voroodi,$khorooji,$hotel_id,(int)$r["room_typ_id"]));
			}
			return($out);
		}
		public function loadRooms($hotel_id,$reserve_id = 0)
		{
                        $out = array();
                        $hotel_id = (int)$hotel_id;
			$room_frase = "";
                        if($hotel_id <= 0)
                        {
                                //$hotel_id = $this->hotel_id;
                                //$room_frase = " and `id` = ".$this->id;
                        }
			$shart = '';
			$reserve_id = (int)$reserve_id;
			if($reserve_id != 0)
			{
				$room_det = new room_det_class;
				$room_det = $room_det->loadByReserve($reserve_id);
				$room_det = $room_det[0];
				$rooms = null;
				for($i = 0;$i < count($room_det);$i++)
					$rooms[] = $room_det[$i]->room_id;
				if($rooms != null && is_array($rooms))
					$shart = ' or (`id` in ('.implode(',',$rooms).'))';
			}
                        mysql_class::ex_sql("SELECT `room_typ_id` , count( `id` ) as `co`,`ghimat` FROM `room` WHERE `hotel_id` = $hotel_id $room_frase and `en` = 1 and (`vaziat` <> 5 $shart) group by `room_typ_id`",$q);
                        while($r = mysql_fetch_array($q))
                        {
                                $tmp = new room_typ_class((int)$r["room_typ_id"]);
                                $out[] = array("room_typ_id"=>(int)$r["room_typ_id"],"count"=>(int)$r["co"],"name"=>$tmp->name,"ghimat"=>(int)$r["ghimat"],'zarfiat'=>$tmp->zarfiat,'room_ids'=>room_class::loadRoomsByTyp($hotel_id,(int)$r["room_typ_id"],$shart));
                        }
                        return($out);
		}	
                public function loadOpenRoomArray($startDate,$delay,$voroodi,$khorooji,$hotel_id ,$room_typ_id=-1)
                {
                        $out = array();
                        $delay = (int)$delay;
                        $hotel_id = (int)$hotel_id;
                        $room_frase = "";
                        $endDate = date("Y-m-d H:i:s",strtotime($startDate." + $delay days"));
                        if($voroodi)
                                $startDate = date("Y-m-d 00:00:00",strtotime($startDate));
                        if($khorooji)
                                $endDate = date("Y-m-d 20:00:00",strtotime($endDate));
                        if($hotel_id <= 0)
                        {
                                $hotel_id = $this->hotel_id;
                                $room_frase = " and `id` = ".$this->id;
                        }
			$shart = '';
			if($room_typ_id>0)
				$shart = "and `room_typ_id`=$room_typ_id";
                        mysql_class::ex_sql("SELECT `id` FROM `room` WHERE `hotel_id` = $hotel_id $room_frase and not(`id` in (select `room_id` from `room_det` where ('$startDate'>`aztarikh` and '$startDate' <`tatarikh`) or ('$endDate'>`aztarikh` and '$endDate' <`tatarikh`) or ('$startDate' <= `aztarikh` and '$endDate' >=`tatarikh`) and `reserve_id`>0)) and `en`=1 and (`vaziat` <> 5) $shart",$q);
                        while($r = mysql_fetch_array($q))
                        {
                                $out[] = (int)$r['id'];
                        }
                        return($out);
                }
		public function getId()
		{
			return($this->id);
		}
		
		public function getReserve($tarikh,$room_id=-1)
		{
			$room_id = (int)$room_id;
			$aztarikh = date("Y-m-d 00:00:00",strtotime($tarikh));
			$tatarikh = date("Y-m-d 23:59:59",strtotime($tarikh));
			if($room_id <= 0)
				$room_id = $this->id;
			$out = room_det_class::roomIdAvailable($room_id,$aztarikh,$tatarikh);
			$tmp = null;
			for($i = 0;$i < count($out);$i++)
				if(reserve_class::isPaziresh($out[$i]) && !reserve_class::isKhorooj($out[$i],$room_id))
				{
					$hr = new hotel_reserve_class;
					$hr->loadByReserve((int)$out[$i]);
					$tmp[] = array('reserve_id'=>$out[$i],'fname'=>$hr->fname,'lname'=>$hr->lname,'tel'=>$hr->tozih);
				}
			return($tmp);
		}
		public function getAnyReserve($tarikh,$room_id=-1)
                {
                        $room_id = (int)$room_id;
                        $aztarikh = date("Y-m-d 00:00:00",strtotime($tarikh));
                        $tatarikh = date("Y-m-d 23:59:59",strtotime($tarikh));
//echo  strtotime($tarikh).'<br/>';
                        if($room_id <= 0)
                                $room_id = $this->id;
                        $out = room_det_class::roomIdAvailable($room_id,$aztarikh,$tatarikh);
                        $tmp = null;
                        for($i = 0;$i < count($out);$i++)
			{
                        	$hr = new hotel_reserve_class;
	                        $hr->loadByReserve((int)$out[$i]);
        	                $tmp[] = array('reserve_id'=>$out[$i],'fname'=>$hr->fname,'lname'=>$hr->lname,'tel'=>$hr->tozih);
			}
			$tmp1 = $tmp;
			$ee =array();
			if(count($tmp)>1)
			{
				$q=null;
				$sq = $tmp[0]['reserve_id'].','.$tmp[1]['reserve_id'];	
				mysql_class::ex_sql("select `reserve_id` from `room_det` where `reserve_id` in($sq) order by `aztarikh` desc limit 1",$q);
				if($r=mysql_fetch_array($q))
					if($tmp[1]['reserve_id']==$r['reserve_id'])
						$ee = $tmp;
					else
					{
						$ee[]=$tmp[1];
						$ee[]=$tmp[0];
					}
					$tmp1 = $ee;
			}
//var_dump($tmp);
                        return($tmp1);
                }
		public function getMoeenId($add = TRUE)
		{
			$out = -1;
			$hot = new hotel_class($this->hotel_id);
                        $hot_kol = $hot->moeen_id;
                        $hot_kol = new moeen_class($hot_kol);
                        $hot_kol = $hot_kol->kol_id;
			$hot = $hot->name;
			$name = "حساب میهمان $hot اتاق ".$this->name;
			if($this->moeen_id<=0 && $add)
				$out = moeen_class::addById($hot_kol,$name);
			if($out > 0)
				$this->moeen_id = $out;
			mysql_class::ex_sqlx("update `room` set `moeen_id` = $out where `id` = ".$this->id);
			return($out);
		}
		public function add($hotel_id,$room_typ_id,$tedad,$aztarikh,$tatarikh)
		{
			$room_name = 99;
			$hotel_id = (int)$hotel_id;
			$tedad = (int)$tedad;
			$room_typ_id = (int)$room_typ_id;
			$id = array();
			mysql_class::ex_sql("select `name` from `room` where `hotel_id` = $hotel_id and `en`=1",$q);
			while($r = mysql_fetch_array($q))
				if((int)$r['name'] > $room_name)
					$room_name = (int)$r['name'];
			$q = null;
			$cr_count = 0;
			$av_rooms = null;
			mysql_class::ex_sql("select `id` from `room` where `room_typ_id` = $room_typ_id and `en`= 1 and `hotel_id` = $hotel_id",$q);
			while($r = mysql_fetch_array($q))
				if(room_det_class::roomIdAvailable((int)$r['id'],$aztarikh,$tatarikh) === null && $cr_count < $tedad)
				{
					$id[] = (int)$r['id'];
					$cr_count++;
				}
			for($i = 0;$i < $tedad-$cr_count;$i++)
			{
				$room_name++;
				$ln = mysql_class::ex_sqlx("insert into `room` (`hotel_id`, `room_typ_id`, `name`, `en`, `vaziat`) values ($hotel_id,$room_typ_id,'$room_name',1,2)",FALSE);
				$id[] = mysql_insert_id($ln);
				mysql_close($ln);
			}
			$out = implode(',',$id);
			return($out);
		}
		public function setVaziat($room_id,$inp,$tarikh='')
		{
			$qu = ($tarikh!='')? ",`end_fix_date`='$tarikh'": '';
			mysql_class::ex_sqlx("update `room` set `vaziat`=$inp $qu where `id`=$room_id");
		}
	}
?>
