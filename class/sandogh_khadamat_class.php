<?php
	class sandogh_khadamat_class
	{
		public $id=-1;
		public $sandogh_id=-1;
		public $khadamat_id=-1;
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				mysql_class::ex_sql("select * from `sandogh_khadamat` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id=$r['id'];
					$this->sandogh_id=$r['sandogh_id'];
					$this->khadamat_id=$r['khadamat_id'];
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
				mysql_class::ex_sql("select $field_txt from `sandogh_khadamat` where `id` = $id",$q);
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
		public function loadKhadamatById($id)
		{
			$out = -1;
			if((int)$id > 0)
			{
				mysql_class::ex_sql("select `khadamat_id` from `sandogh_khadamat` where `sandogh_id` = $id",$q);
				if($r = mysql_fetch_array($q))
					$out =$r['khadamat_id'];
			}
			return($out);
		}
	}
?>
