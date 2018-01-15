<?php
	class anbar_class
	{
		public $id=-1;
		public $name="";
		public $location="";
		public $en=-1;
		public $moeen_id=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `anbar` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->location=$r['location'];
				$this->en=$r['en'];
				$this->moeen_id=$r['moeen_id'];
			}
		}
	}
?>
