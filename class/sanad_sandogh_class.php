<?php
	class sanad_sandogh_class
	{
		public $id=-1;
		public $sanad_record=-1;
		public $shomare_factor=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `sanad_sandogh` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->sanad_record=$r['sanad_record'];
				$this->shomare_factor=$r['shomare_factor'];
			}
		}
		public function loadByFactor($factor_shomare=-1)
		{
			$out = array();
			$factor_shomare = (int)$factor_shomare;
			if($factor_shomare > 0)
			{
				mysql_class::ex_sql("select `id`,`sanad_factor` from `sanad_sandogh` where `factor_shomare` = $factor_shomare",$q);
				while($r = mysql_fetch_array($q))
					$out[] = $r['sanad_factor'];
			}
			return($out);
		}
	}
?>
