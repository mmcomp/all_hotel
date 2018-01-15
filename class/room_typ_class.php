<?php
	class room_typ_class
	{
		public $id=-1;
		public $name="";
		public $zarfiat=0;
		public $zarfiat_ezafe=0;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `room_typ` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->zarfiat=$r['zarfiat'];
				$this->zarfiat_ezafe=$r['zarfiat_ezafe'];
			}
		}
		public function loadRoomTyps()
		{
			$out = array();
			mysql_class::ex_sql("select * from `room_typ` order by `name`",$q);
			while($r = mysql_fetch_array($q))
			{
				$thi = null;
				$thi['id']=(int)$r['id'];
				$thi['name']=$r['name'];
				$thi['zarfiat']=(int)$r['zarfiat'];
				$out[] = $thi;
			}
			return($out);
		}
		public function loadTypDetails($room_typ_ids)
		{
			$out = '';
			$tmp = array();
			for($i = 0;$i < count($room_typ_ids);$i++)
			{
				if(isset($tmp[$room_typ_ids[$i]]))
					$tmp[$room_typ_ids[$i]]++;
				else
					$tmp[$room_typ_ids[$i]] = 1;
			}
			foreach($tmp as $room_typ_id => $count)
			{
				$r = new room_typ_class($room_typ_id);
				$out .= (($out != '')?',':'').$r->name.' '.$count.' ';
			}
			return($out);
		}
		public function getOneDayGhimat($hotel_id,$curdate){
			$out = NULL;
			$my = new mysql_class();
			$query = "select ghimat,ghimat_ezafe from room_typ_working_date where hotel_id = $hotel_id and room_typ_id = ".$this->id." and ('$curdate' BETWEEN date(aztarikh) and date(tatarikh))";
// 			echo $query."<br/>";
			$my->ex_sql($query,$q);
			while($r = mysql_fetch_array($q)){
				$out = array("ghimat"=>(int)$r['ghimat'],"ghimat_ezafe"=>(int)$r['ghimat_ezafe']);
			}
			return $out;
		}
		public function getGhimat($hotel_id,$aztarikh,$shab){
			$out = array("ghimat"=>0,"ghimat_ezafe"=>0);
			$working = TRUE;
			for($i=0;$i < $shab;$i++){
				$curdate = date("Y-m-d",strtotime($aztarikh.' + '.$i.' day'));
				$tmp = $this->getOneDayGhimat($hotel_id,$curdate);
				if($tmp == NULL){
					$working = FALSE;
				}else{
					$out['ghimat'] += $tmp['ghimat'];
					$out['ghimat_ezafe'] += $tmp['ghimat_ezafe'];
				}
			}
			if(!$working){
				$out = NULL;
			}
			return $out;
		}
	}
?>
