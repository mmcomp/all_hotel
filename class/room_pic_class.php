<?php
	class room_pic_class
	{
		public $id=-1;
		public $room_id=-1;
		public $pic="";
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `room_pic` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->room_id=$r['room_id'];
				$this->pic=$r['pic'];
			}
		}
		public function loadRoom($room_id)
		{
			$out = null;
			mysql_class::ex_sql("select * from `room_pic` where `room_id` = $room_id",$q);
                        while($r = mysql_fetch_array($q))
                        {
				$ths = new room_pic_class;
                                $ths->id=$r['id'];
                                $ths->room_id=$r['room_id'];
                                $ths->pic=$r['pic'];
				$out[] = $ths;
                        }
			return($out);
		}
	}
?>
