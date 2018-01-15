<?php
	class sanad_class
	{
		public $id=-1;
		public $shomare_sanad=-1;
		public $group_id=-1;
		public $kol_id=-1;
		public $moeen_id=-1;
		public $tafzili_id=-1;
		public $tafzili2_id=-1;
		public $tafzilishenavar_id=-1;
		public $tafzilishenavar2_id=-1;
		public $tarikh='0000-00-00 00:00:00';
		public $user_id=-1;
		public $typ=0;
		public $tozihat="";
		public $en=1;
		public $mablagh=0;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `sanad` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->shomare_sanad=$r['shomare_sanad'];
				$this->group_id=$r['group_id'];
				$this->kol_id=$r['kol_id'];
				$this->moeen_id=$r['moeen_id'];
				$this->tafzili_id=$r['tafzili_id'];
				$this->tafzili2_id=$r['tafzili2_id'];
				$this->tafzilishenavar_id=$r['tafzilishenavar_id'];
				$this->tafzilishenavar2_id=$r['tafzilishenavar2_id'];
				$this->tarikh=$r['tarikh'];
				$this->user_id=$r['user_id'];
				$this->typ=$r['typ'];
				$this->tozihat=$r['tozihat'];
				$this->en=$r['en'];
				$this->mablagh=$r['mablagh'];
			}
		}
		public function editSanadRecord($sanad_records,$moeen_id)
		{
			$out = FALSE;
			$moeen_id = (int)$moeen_id;
			if(is_array($sanad_records) and count($sanad_records) > 0)
			{
				$tmp = implode(',',$sanad_records);
				if($tmp != '')
				{
					mysql_class::ex_sqlx("update `sanad` set `moeen_id` = $moeen_id where `id` in ($tmp)");
					$out = TRUE;
				}
			}
			return($out);
		}
	}
?>
