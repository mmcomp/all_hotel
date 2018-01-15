<?php
	class msg_class
	{
		public $id=-1;
		public $user_id=-1;
		public $head='';
		public $body='';
		public $read=0;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `msg` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->user_id=(int)$r['user_id'];
				$this->head=$r['head'];
				$this->body=$r['body'];
				$this->read=$r['read'];
			}
		}
		public function sendMsg($head,$body,$daftar_ids)
		{
			$out = FALSE;
			if(is_array($daftar_ids))
			{
				for($i=0;$i<count($daftar_ids);$i++)
				{
					$users = daftar_class::loadUsers((int)$daftar_ids[$i]);
					for($j = 0;$j < count($users);$j++)
						mysql_class::ex_sqlx("insert into `msg` (`user_id`,`head`,`body`) values ('".$users[$j]."','$head','$body') ");
				}
			$out = TRUE;
			}
			return $out;
		}
		public function sendUserMsg($head,$body,$user_ids)
		{
			$out = FALSE;
			if(is_array($user_ids))
			{
					for($j = 0;$j < count($user_ids);$j++)
					{
						mysql_class::ex_sqlx("insert into `msg` (`user_id`,`head`,`body`) values ('".$user_ids[$j]."','$head','$body') ");
					}
			}
			return $out;
		}
		public function setRead($msg_id=-1)
		{
			mysql_class::ex_sqlx("update `msg` set `read`=1 where `id`=$msg_id");
		}
		public function unReadMsg($user_id)
		{
			$out = 0;
			$user_id = (int)$user_id;
			mysql_class::ex_sql("select count(`id`) as `tedad` from `msg` where `read`=0 and `user_id` =$user_id",$q);
			if($r = mysql_fetch_array($q))
				$out = (int)$r['tedad'];
			return $out;
		}
		public function loadMsgs($user_id)
		{
			$out = FALSE;
			$user_id = (int)$user_id;
			/*
			mysql_class::ex_sql("select * from `msg` where `user_id` = $user_id and `read` = 0 order by `id` desc",$q);
			while($r = mysql_fetch_array($q))
			{
				$out[] = array('head'=>$r['head'],'body'=>$r['body'],(int)$r['id']);
			}
			return($
			*/
			mysql_class::ex_sql("select * from `msg` where `user_id` = $user_id and `read` = 0 order by `id` desc",$q);
			if(mysql_num_rows($q)>0)
			{
			        $grid = new jshowGrid_new("msg","grid1");
			        $grid->whereClause="`user_id` = $user_id and `read` = 0 order by `id` desc";
			        $grid->columnHeaders[0] = null;
				$grid->columnHeaders[1] = null;
				$grid->columnHeaders[2] = 'موضوع';
				$grid->columnHeaders[3] = 'متن';
				$grid->columnHeaders[4] = null;
/*
			        $grid->columnHeaders[2] = "کﺩ";
			        $grid->columnHeaders[1] =null ;
			        $grid->columnHeaders[3] = "ﻥﺎﻣ";
			        $grid->columnHeaders[4] = "ﻥﻮﻋ";
			        $grid->columnLists[1]=loadGrooh();
			        $grid->columnLists[4]=$combo;
			        $grid->deleteFunction = 'delete_item';
*/
				$grid->canEdit = FALSE;
				$grid->canDelete = FALSE;
				$grid->canAdd = FALSE;
				$grid->pageCount = 99999;
			        $grid->intial();
			        $grid->executeQuery();
				$out = '<div id="overlay" class="fadeMe"><br/><br/><br/><br/><br/><br/><br/><center>';
			        $out .= $grid->getGrid();
				$out .= '<input type="button" class="inp" value="خروج" onclick="window.location=window.location;"/></center></div>';
				mysql_class::ex_sqlx("update `msg` set `read` = 1 where `user_id` = $user_id and `read` = 0");
			}
			return($out);
		}
	}
?>
