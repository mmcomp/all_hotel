<?php
	class kala_no_class
	{
		public $id=-1;
		public $name="";
		public $code="";
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `kala_no` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->code=$r['code'];
			}
		}
	}
?>