<?php
	class sanad_reserve_class
	{
		public $reserve_id = -1;
		public $sanad_record = array();
		public function __construct($reserve_id=-1)
		{
			$out = FALSE;
			mysql_class::ex_sql("select `sanad_record` from `sanad_reserve` where `reserve_id` = $reserve_id group by `sanad_record`",$q);
//echo "select `sanad_record` from `sanad_reserve` where `reserve_id` = $reserve_id group by `sanad_record`".'<br/>';
			while($r = mysql_fetch_array($q))
			{
				$this->reserve_id = $reserve_id;
				$this->sanad_record[] = (int)$r['sanad_record'];
				$out = TRUE;
			}
			return($out);
		}
		public function loadSanadId($reserve_id=-1)
		{
			$out = '';
			mysql_class::ex_sql("select `sanad_record` from `sanad_reserve` where `reserve_id` = $reserve_id group by `sanad_record`",$q);
			while($r = mysql_fetch_array($q))
				$out = (int)$r['sanad_record'];
			return($out);
		}
	}
?>
