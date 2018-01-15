<?php
	class kala_vahed_class
	{
		public $id=-1;
		public $name="";
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `kala_vahed` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
			}
		}
	}
?>