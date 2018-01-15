<?php
	class anbar_factor_class
	{
		public $id=-1;
		public $factor_id='';
		public $name="";
		public $tozihat="";
		public $moeen_id=-1;
		public $tarikh_resid='0000-00-00 00:00:00';
		public $anbar_typ_id=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `anbar_factor` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->factor_id=$r['factor_id'];
				$this->name=$r['name'];
				$this->tozihat=$r['tozihat'];
				$this->moeen_id=$r['moeen_id'];
				$this->tarikh_resid=$r['tarikh_resid'];
				$this->anbar_typ_id=$r['anbar_typ_id'];
			}
		}
		public function isJaari($factor_id)
		{
			$out = FALSE;
			$factor_id = (int)$factor_id;
			mysql_class::ex_sql("select `id` from `sanad_anbar` where `anbar_factor_id` = $factor_id",$q);
			if($r=mysql_fetch_array($q))
				$out = TRUE;
			return($out);
		}
	}
?>
