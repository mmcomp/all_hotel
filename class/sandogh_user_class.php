<?php
	class sandogh_user_class
	{
		public $id=-1;
		public $user_id=-1;
		public $sandogh_id=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `sandogh_user` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->user_id=$r['user_id'];
				$this->sandogh_id=$r['sandogh_id'];
			}
		}
	}
?>