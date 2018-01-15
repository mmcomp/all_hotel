<?php
	class factor_khadamat_det_class
	{
		public $id=-1;
		public $anbar_factor_id=-1;
		public $factor_khadamat_id=-1;
		public $tedad=-1;
		public $ghimat=-1;
		public $toz="";
		public $en=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `factor_khadamat_det` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->anbar_factor_id=$r['anbar_factor_id'];
				$this->factor_khadamat_id=$r['factor_khadamat_id'];
				$this->tedad=$r['tedad'];
				$this->ghimat=$r['ghimat'];
				$this->toz=$r['toz'];
				$this->en=$r['en'];
			}
		}
	}
?>