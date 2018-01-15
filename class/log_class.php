<?php
	class log_class
	{
		public $id=-1;
		public $page_location="";
		public $user_id=-1;
		public $dec="";
		public $tarikh=-1;
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				mysql_class::ex_sql("select * from `log` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id=$r['id'];
					$this->page_location=$r['page_location'];
					$this->user_id=$r['user_id'];
					$this->dec=$r['dec'];
					$this->tarikh=$r['tarikh'];
				}
			}
		}
		public function loadField($id,$field)
		{
			$out = FALSE;
			if((int)$id > 0 && is_array($field) && count($field) > 0)
			{
				$field_txt = '';
				for($i = 0;$i < count($field);$i++)
					$field_txt .= '`'.$field[$i].'`'.(($i < count($field)-1)?',':'');
				mysql_class::ex_sql("select $field_txt from `log` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id=$id;
					for($i = 0;$i < count($field);$i++)
					{
						$this->{$field[$i]} = $r[$field[$i]];
						$out[$field[$i]] = $r[$field[$i]];
					}
				}
			}
			return($out);
		}
		public function add($page_location,$user_id,$dec)
		{
			$tarikh = date("Y-m-d H:i:s");
			mysql_class::ex_sqlx("insert into log (`page_location`,`user_id`,`desc`,`tarikh`) values ('$page_location',$user_id,'$dec','$tarikh')");
		}
	}
?>
