<?php
	class tasisat_checklist_template_class
	{
		public $id=-1;
		public $name="";
		public $en=-1;
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				mysql_class::ex_sql("select * from `tasisat_checklist_template` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id=$r['id'];
					$this->name=$r['name'];
					$this->en=$r['en'];
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
				mysql_class::ex_sql("select $field_txt from `tasisat_checklist_template` where `id` = $id",$q);
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
	}
?>