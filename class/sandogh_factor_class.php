<?php
	class sandogh_factor_class
	{
		public $id=-1;
		public $reserve_id=-1;
		public $room_id=-1;
		public $sandogh_item_id=-1;
		public $toz="";
		public $tedad=-1;
		public $mablagh=-1;
		public $factor_shomare=-1;
		public $en=-1;
		public $typ=-1;
		public $user_id = -1;
		public $tarikh = '0000-00-00 00:00:00';
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `sandogh_factor` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->reserve_id=$r['reserve_id'];
				$this->room_id=$r['room_id'];
				$this->sandogh_item_id=$r['sandogh_item_id'];
				$this->toz=$r['toz'];
				$this->tedad=$r['tedad'];
				$this->mablagh=$r['mablagh'];
				$this->factor_shomare=$r['factor_shomare'];
				$this->en=$r['en'];
				$this->typ=$r['typ'];
				$this->user_id = $r['user_id'];
				$this->tarikh = $r['tarikh'];  
			}
		}
		public function getShomareFactor($isFactor = 1)
		{
			$out = 1;
			$isFactor = (int)$isFactor;
			mysql_class::ex_sql("select MAX(`factor_shomare`)+1 as `fs` from `sandogh_factor`",$q);
			if($r = mysql_fetch_array($q))
				$out = ((int)$r['fs'] > 0) ? (int)$r['fs'] : 1;
			return($out);
		}
                public function isJaari($factor_shomare)
                {
                        $out = FALSE;
                        $factor_id = (int)$factor_shomare;
                        mysql_class::ex_sql("select `id` from `sanad_sandogh` where `shomare_factor` = $factor_id",$q);
                        if($r=mysql_fetch_array($q))
                                $out = TRUE;
                        return($out);
                }
		public function removeSanads($factor_shomare)
                {
                        $factor_id = (int)$factor_shomare;
			$sanad_recs = null;
                        mysql_class::ex_sql("select `sanad_record` from `sanad_sandogh` where `shomare_factor` = $factor_id",$q);
                        while($r=mysql_fetch_array($q))
                                $sanad_recs[] = (int)$r['sanad_record'];
			$out = ($sanad_recs != null);
			if($out)
			{
				$sanad_recs = implode(',',$sanad_recs);
				mysql_class::ex_sqlx("delete from `sanad` where `id` in ($sanad_recs)");
				mysql_class::ex_sqlx("delete from `sanad_sandogh` where `shomare_factor` = $factor_id");
			}
                        return($out);
                }
	}
?>
