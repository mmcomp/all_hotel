<?php
	class cost_det_class
	{
		public $id=-1;
		public $cost_kala_id=-1;
		public $kala_id=-1;
		public $tedad=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `cost_det` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->cost_kala_id=$r['cost_kala_id'];
				$this->kala_id=$r['kala_id'];
				$this->tedad=$r['tedad'];
			}
		}
	}
?>