<?php
	class sandogh_item_class
	{
		public $id=-1;
		public $name="";
		public $sandogh_id=-1;
		public $mablagh_det="";
		public $en=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `sandogh_item` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->sandogh_id=$r['sandogh_id'];
				$this->mablagh_det=$r['mablagh_det'];
				$this->en=$r['en'];
			}
		}
	}
?>