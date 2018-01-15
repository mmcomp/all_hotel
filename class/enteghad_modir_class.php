<?php
	class enteghad_modir_class
	{
		public $id=-1;
		public $typ=-1;
		public $reply_id=-1;
		public $mozoo="";
		public $matn=-1;
		public $user_id=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `enteghad_modir` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->typ=$r['typ'];
				$this->reply_id=$r['reply_id'];
				$this->mozoo=$r['mozoo'];
				$this->matn=$r['matn'];
				$this->user_id=$r['user_id'];
			}
		}
	}
?>