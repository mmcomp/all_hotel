<?php
	class ticket_class
	{
		public $id=-1;
		public $subject="";
		public $body="";
		public $tarikh=-1;
		public $from_user=-1;
		public $submitter_user=-1;
		public $tarikh_su=-1;
		public $answer="";
		public $tarikh_an=-1;
		public $stat=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `ticket` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->subject=$r['subject'];
				$this->body=$r['body'];
				$this->tarikh=$r['tarikh'];
				$this->from_user=$r['from_user'];
				$this->submitter_user=$r['submitter_user'];
				$this->tarikh_su=$r['tarikh_su'];
				$this->answer=$r['answer'];
				$this->tarikh_an=$r['tarikh_an'];
				$this->stat=$r['stat'];
			}
		}
	}
?>