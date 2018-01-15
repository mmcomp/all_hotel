<?php
	class hotel_garanti_class
	{
		public $id=-1;
		public $hotel_id=-1;
		public $daftar_id=-1;
		public $tabaghe=-1;
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				mysql_class::ex_sql("select * from `hotel_garanti` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id=$r['id'];
					$this->hotel_id=$r['hotel_id'];
					$this->daftar_id=$r['daftar_id'];
					$this->tabaghe=$r['tabaghe'];
				}
			}
		}
		public function loadField($id,$field)
		{
			$out = FALSE;
			if((int)$id > 0 && is_array($field) && count($field) > 0)
			{
				$field_txt = '';
				for($i = 0;$i < count($field);$i++)
					$field_txt .= '`'.$field[$i].'`'.(($i < count($field)-1)?',':'');
				mysql_class::ex_sql("select $field_txt from `hotel_garanti` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id=$id;
					for($i = 0;$i < count($field);$i++)
					{
						$this->{$field[$i]} = $r[$field[$i]];
						$out[$field[$i]] = $r[$field[$i]];
					}
				}
			}
			return($out);
		}
		public function loadByDaftarId($daftar_id)
		{
			$out = array();
			mysql_class::ex_sql("select `tabaghe` from `hotel_garanti` where `daftar_id` = '$daftar_id'",$q);
			while($r = mysql_fetch_array($q))
				$out[] = $r['tabaghe'];
			return $out;
		}
		public function loadGaranty()
		{
			$out = array();
			mysql_class::ex_sql("select `tabaghe` from `hotel_garanti`",$q);
			while($r = mysql_fetch_array($q))
				$out[] = $r['tabaghe'];
			return $out;
		}
		public function loadTabagheGaranty($tabaghe)
		{
			$out = FALSE;
			mysql_class::ex_sql("select `id` from `hotel_garanti` where `tabaghe`='$tabaghe'",$q);
			if($r = mysql_fetch_array($q))
				$out = TRUE;
			return $out;
		}
		public function loadGarantyDaftar($daftar_id)
		{
			$out = array();
			mysql_class::ex_sql("select `tabaghe` from `hotel_garanti` where `daftar_id`='$daftar_id'",$q);
			while($r = mysql_fetch_array($q))
				$out[] = $r['tabaghe'];
			return $out;
		}
		public function canViewReserve($reserve_id)
		{
			$out = FALSE;
			$reserve_daftar_id = hotel_reserve_class::loadDaftarByReserveId($reserve_id);
			if ($_SESSION['daftar_id']!=$reserve_daftar_id)
			{
				$tabaghes = hotel_garanti_class::loadGarantyDaftar($_SESSION['daftar_id']);
				$room_res = room_det_class::loadRoomByReserve($reserve_id);
				foreach ($room_res as $i)
				{
					$tabagheId = room_class::loadTabagheByRoomId($i);
					$garantiOtagh = hotel_garanti_class::loadTabagheGaranty($tabagheId);
					$res_garanti = !((in_array($tabagheId,$tabaghes) && count($tabaghes)>0) || (count($tabaghes)==0));
					$out = ($res_garanti || $garantiOtagh);
				}
			}
			return ($out);
		}
		public function loadTabagheByHotelId($hotel_id)
		{
			$out = array();
			mysql_class::ex_sql("select `tabaghe` from `hotel_garanti` where `hotel_id`='$hotel_id'",$q);
			while($r = mysql_fetch_array($q))
				$out[] = $r['tabaghe'];
			return $out;
		}
		public function canViewReserve_color($reserve_id)
		{
			$out = '';
			//$reserve_daftar_id = hotel_reserve_class::loadDaftarByReserveId($reserve_id);
		//	if ($_SESSION['daftar_id']!=$reserve_daftar_id)
		//	{
				$tabaghes = hotel_garanti_class::loadGarantyDaftar($_SESSION['daftar_id']);
				$room_res = room_det_class::loadRoomByReserve($reserve_id);
				foreach ($room_res as $i)
				{
					$tabagheId = room_class::loadTabagheByRoomId($i);
					$garantiOtagh = hotel_garanti_class::loadTabagheGaranty($tabagheId);
					$res_garanti = !((in_array($tabagheId,$tabaghes) && count($tabaghes)>0) || (count($tabaghes)==0));
					if ($res_garanti || $garantiOtagh)
						$out = 'style="background-color:#009999;"';
				}
			//}
			return ($out);
		}
		public function loadIsGarantyByRoomId($room_id)
		{
			$out = FALSE;
			mysql_class::ex_sql("select `tabaghe` from `room` where `id`='$room_id'",$q);
			if($r = mysql_fetch_array($q))
				$out = hotel_garanti_class::loadTabagheGaranty($r['tabaghe']);
			return $out;
		}
	}
?>
