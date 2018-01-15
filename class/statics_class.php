<?php
	class statics_class
	{
		public $id=-1;
		public $fkey="";
		public $fvalue="";
		public function __construct($id,$key='')
		{
			if($key!='')
				mysql_class::ex_sql("select * from `statics` where `id` = $id and `fkey`='f$key'",$q);
			else
				mysql_class::ex_sql("select * from `statics` where `id` = $id ",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->fkey=$r['fkey'];
				$this->fvalue=$r['fvalue'];
			}
		}
		public function loadByKey($fkey)
		{
			$out = null;
			mysql_class::ex_sql("select * from `statics` where `fkey` ='$fkey' order by `fvalue` ",$q);
			while($r = mysql_fetch_array($q))
			{
				$new = new statics_class($r['id']);
				$out[] = $new;
			}
			return $out;
		}
	}
?>
