<?php
	class reserve_tmp_class
	{
		public $id=-1;
		public $reserve_id=-1;
		public $tarikh='0000-00-00 00:00:00';
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `reserve_tmp` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->reserve_id=$r['reserve_id'];
				$this->tarikh=$r['tarikh'];
			}
		}
		public function add($reserve_id)
		{
			$out = null;
			$reserve_id = (int)$reserve_id;
			$tarikh = date("Y-m-d H:i:s");
			mysql_class::ex_sql("select `id`,`tarikh` from `reserve_tmp` where `reserve_id` = $reserve_id",$q);
			if($r = mysql_fetch_array($q))
				$out = array('id'=>(int)$r['id'],'tarikh'=>$r['tarikh']);
			else
			{
				$out = array();
				mysql_class::ex_sqlx("insert into `reserve_tmp` (`reserve_id`,`tarikh`) values ($reserve_id,'$tarikh')");
				mysql_class::ex_sql("select `id`,`tarikh` from `reserve_tmp` where `reserve_id` = $reserve_id",$q);
	                        if($r = mysql_fetch_array($q))
        	                        $out = array('id'=>(int)$r['id'],'tarikh'=>$r['tarikh']);
			}
			return($out);
		}
	}
?>
