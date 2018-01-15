<?php
	class bug_reserve_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				mysql_class::ex_sql("select * from `bug_reserve` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
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
				mysql_class::ex_sql("select $field_txt from `bug_reserve` where `id` = $id",$q);
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
		public function insertReserve($user_id,$res_id)
		{
			$out = FALSE;
			$now = date('Y-m-d h:i:s');
			if (isset($user_id) && isset($res_id))
			{
				mysql_class::ex_sqlx("INSERT INTO `bug_reserve` (`id` ,`user_id` ,`reserve_id` ,`tarikh`)VALUES (NULL , '$user_id', '$res_id', '$now')");
				$out = TRUE;
			}
			return ($out);
		}
	}
?>
