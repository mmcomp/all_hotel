<?php
	class sandogh_class
	{
		public $id=-1;
		public $name="";
		public $hotel_id = -1;
		public $moeen_id=-1;
		public $moeen_cash_id=-1;
		public $can_cash=FALSE;
		public $icon='';
		public $khadamat = array();
		public $khadamat_ids = array();
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `sandogh` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id =(int)$r['id'];
				$this->name =$r['name'];
				$this->hotel_id =$r['hotel_id'];
				$this->moeen_id =$r['moeen_id'];
				$this->moeen_cash_id =$r['moeen_cash_id'];
				$this->can_cash =((int)$r['can_cash']==1)?TRUE:FALSE;
				$this->icon =$r['icon'];
				mysql_class::ex_sql("select `khadamat_id` from `sandogh_khadamat` where `sandogh_id` = $id",$qt);
				while($rt = mysql_fetch_array($qt))	
				{
					$this->khadamat_ids[] = (int)$rt['khadamat_id'];
					$khad = new khadamat_class((int)$rt['khadamat_id']);
					$this->khadamat[(int)$rt['khadamat_id']] = $khad->name;
				}
			}
		}
		public function loadHotelById($id=-1)
		{
			$out = -1;
			mysql_class::ex_sql("select `hotel_id` from `sandogh` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
				$out =$r['hotel_id'];
			return($out);
		}
	}
?>
