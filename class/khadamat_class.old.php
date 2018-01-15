<?php
	class khadamat_class
	{
		public $id=-1;
		public $hotel_id=-1;
		public $name="";
		public $ghimat_def=-1;
		public $typ = 0;
		public $en = 1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `khadamat` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->hotel_id=$r['hotel_id'];
				$this->name=$r['name'];
				$this->ghimat_def=$r['ghimat_def'];
				$this->typ = (int)$r['typ'];
				$this->en = (int)$r['en'];
			}
		}
		public function loadKhadamats($hotel_id)
		{
			$out = array();
			$hotel_id =(int)$hotel_id;
			mysql_class::ex_sql("select `name`,`id`,`typ`,`ghimat_def` from `khadamat` where `en` = 1 and `hotel_id` = $hotel_id",$q);
			while($r = mysql_fetch_array($q))
				$out[] = array('name'=>$r['name'],'id'=>(int)$r['id'],'typ'=>(int)$r['typ'],'ghimat'=>(int)$r['ghimat_def']);
			return($out);
		}
	}
?>
