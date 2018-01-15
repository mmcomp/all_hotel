<?php
	class factor_khadamat_class
	{
		public $id=-1;
		public $name="";
		public $en = 1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `factor_khadamat` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->en=$r['en'];
			}
		}
	}
?>
