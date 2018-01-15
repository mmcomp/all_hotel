<?php
	class vorood_class
	{
		public $id = -1;
		public $user_id = -1;
		public $dat = '';
		public $img = '';
		public $toz = '';
		public $vorood = TRUE;
		public function __construct($user_id,$dat,$img,$vorood = TRUE)
		{
			$user_id = (int)$user_id;
			$nohour = date("Y-m-d 00:00:00",strtotime($dat));
			$nohourend = date("Y-m-d 23:59:59",strtotime($dat));
			mysql_class::ex_sql("select * from `vorood` where `user_id` = $user_id and `dat` >= '$nohour' and `dat` <= '$nohourend' and `typ` = ".($vorood ? '1': '0'),$q);
			$firs_count = mysql_num_rows($q);
			mysql_class::ex_sql("select * from `vorood` where `user_id` = $user_id and `dat` >= '$nohour' and `dat` <= '$nohourend' and `typ` = ".(!$vorood ? '1': '0'),$q2);
			$second_count = mysql_num_rows($q2);
			if(($vorood && $firs_count == $second_count) || (!$vorood && abs($firs_count-$second_count)==1))
			//$canvorood = TRUE;
			//mysql_class::ex_sql("select * from `vorood` where `user_id` = $user_id order by `dat` desc limit 1",$q);
			//if($r = mysql_fetch_array($q))
				//$canvorood = (((int)$r['typ'] == 1 )? FALSE:TRUE);
			//if(($vorood && $canvorood) || (!$vorood && !$canvorood))
			{
                                mysql_class::ex_sqlx("insert into `vorood` (`user_id`,`dat`,`img`,`typ`) values ($user_id,'$dat','$img',".($vorood ? '1': '0').")");
                                $q = null;
                                mysql_class::ex_sql("select * from `vorood` where `user_id` = $user_id and `dat` >= '$nohour' and `dat` <= '$nohourend' and `typ` = ".($vorood ? '1': '0'),$q);
                                if($r = mysql_fetch_array($q))
                                {
                                        $this->dat = $r['dat'];
                                        $this->user_id = $user_id;
                                        $this->vorood = $vorood;
                                        $this->id = $r['id'];
                                        $this->img = $r['img'];
					$this->toz = $r['toz'];
                                }

			}
		}
		public function sabtGhayeb($dat,$user_id)
		{
			$user_id = (int)$user_id;
			$dat = date("Y-m-d 23:59:59",strtotime($dat));
			$dat1 = date("Y-m-d 00:00:00",strtotime($dat));
			echo "select `dat` from `vorood` where `dat`<='$dat' and `dat` >= '$dat1' and `user_id` = $user_id ";
			mysql_class::ex_sql("select `dat` from `vorood` where `dat`<='$dat' and `dat` >= '$dat1' and `user_id` = $user_id ",$q);
			if(!($r = mysql_fetch_array($q)))
			{
				echo ("insert into `vorood` (`user_id`,`dat`,`typ`,`img`) values ($user_id,'$dat',1,'../img/ghayeb.jpg')");
				mysql_class::ex_sqlx("insert into `vorood` (`user_id`,`dat`,`typ`,`img`) values ($user_id,'$dat',1,'../img/ghayeb.jpg')");
				echo ("insert into `vorood` (`user_id`,`dat`,`typ`,`img`) values ($user_id,'$dat',0,'../img/ghayeb.jpg')");
				mysql_class::ex_sqlx("insert into `vorood` (`user_id`,`dat`,`typ`,`img`) values ($user_id,'$dat',0,'../img/ghayeb.jpg')");
			}
		}
		public function sabtGheybat($dat,$user_id)
		{
/*
			$conf = new conf;
			$user_id = (int)$user_id;
			$tmp = strtotime(date("Y-m-d H:i:s",strtotime($dat.' -1 day')));
			while($tmp >= strtotime(audit_class::hamed_pdateBack($conf->start_vorood)))
			{
				vorood_class::sabtGhayeb(date("Y-m-d H:i:s",$tmp),$user_id);
				$tmp = strtotime(date("Y-m-d H:i:s",$tmp).' -1 day');
			}
*/
		}
	}
?>
