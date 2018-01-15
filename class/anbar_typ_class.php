<?php
	class anbar_typ_class
	{
		public $id=-1;
		public $name="";
		public $typ=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `anbar_typ` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->typ=$r['typ'];
			}
		}
	}
?>