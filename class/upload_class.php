<?php
	class upload_class
	{
		public $id=-1;
		public $daftar_id=-1;
		public $user_id=-1;
		public $toz="";
		public $pic_addr="";
		public $tarikh=-1;
		public $sanad_record_id=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `upload` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->daftar_id=$r['daftar_id'];
				$this->user_id=$r['user_id'];
				$this->toz=$r['toz'];
				$this->pic_addr=$r['pic_addr'];
				$this->tarikh=$r['tarikh'];
				$this->sanad_record_id=$r['sanad_record_id'];
			}
		}
		public function isUpload($sanad_record_id)
		{
			$out = FALSE;
			mysql_class::ex_sql("select `id` from `upload` where `sanad_record_id`=$sanad_record_id",$q);
			if($r= mysql_fetch_array($q))
				$out = TRUE;
			return $out;
		}
	}
?>
