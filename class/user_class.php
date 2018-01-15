<?php
	class user_class{
		public $id = -1;
		public $daftar_id = -1;
		public $ajans_id = -1;
		public $fname = "";
		public $lname = "";
		public $user = "";
		public $pass = "";
		public $typ = 1;
		public $vorood = '7:00';
		public $khorooj = '14:00';
                public $vorood1 = '00:00';
                public $khorooj1 = '00:00';
		public $online_date = '0000-00-00 00:00:00';
		public $zaman_hozoor = 0;
		public function __construct($id=-1){
			mysql_class::ex_sql("select * from `user` where 1=1 and `id`=".$id,$q);
			if($r = mysql_fetch_array($q,MYSQL_ASSOC)){
// 				var_dump($r);
				$this->id =$r["id"];
				$this->fname = $r["fname"];
				$this->lname = $r["lname"];
				$this->user = $r["user"];
				$this->pass = $r["pass"];
				$this->typ = (int)$r["typ"];
				$this->daftar_id = (int)$r["daftar_id"];
				$this->ajans_id = $r['ajans_id'];
				$this->num_card = $r['num_card'];
				$this->vorood = $r['vorood'];
				$this->khorooj = $r['khorooj'];
				$this->vorood1 = $r['vorood1'];
                                $this->khorooj1 = $r['khorooj1'];
				$this->online_date = $r['online_date'];
				$this->zaman_hozur = $r['zaman_hozur'];
			}
		}
                public function refresh()
                {
                        $today = date("Y-m-d H:i:s");
                        mysql_class::ex_sqlx("update `user` set `online_date`='$today' where `id`='".$this->id."'");
                }
		public function ajansUserCount()
		{
			$out = 0;
			mysql_class::ex_sql("select count(`id`) as `auser` from `user` where `ajans_id` > 0",$q);
			if($r = mysql_fetch_array($q))
				$out = (int)$r['auser'];
			return($out);
		}
		public function expire()
                {
                        $today = date("Y-m-d H:i:s");
/*
			mysql_class::ex_sql("select `online_date` from `user`",$q);
	                while($r = mysql_fetch_array($q))
         	        {
				$onDate = $r["online_date"];
				$sumDate = $ondate + "0000-00-00 00:15:00";
                        	mysql_class::ex_sqlx("update `user` set `online_date`='$today' where `id`='$id'");
			}
*/
                }
		public function logout()
                {
			//Modfied by M.Mirsamie from '00:00:00' to '0000-00-00 00:00:00'.
			$temp_date = "0000-00-00 00:00:00";
                        mysql_class::ex_sqlx("update `user` set `online_date`='$temp_date' where `id`='".$this->id."'");
//			session_destroy();
                }
		public function loadTyp($user_id)
                {	
			$out = '';
			mysql_class::ex_sql("select `typ` from `user` where `id`='$user_id'",$qu);
			if($r = mysql_fetch_array($qu))
				$out = $r['typ'];
			return($out);
                }
		public function sabt_vorood()
                {
			$today = date("Y-m-d H:i:s");
			if (!empty($_SERVER['HTTP_CLIENT_IP']))
			{
				$ip=$_SERVER['HTTP_CLIENT_IP'];
			}
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else
			{
				$ip=$_SERVER['REMOTE_ADDR'];
			}
			//$ip = ip2long($ip);
			mysql_class::ex_sqlx("insert into `user_ip` (`id`,`user_id`,`user_ip`,`tarikh`,`en`) values (NULL,'".$this->id."','$ip','$today','1')");
			mysql_class::ex_sqlx("update `user` set `online_date`='$today' where `id`=".$this->id);
                }

		public function sabt_khorooj()
                {
                        $today = date("Y-m-d H:i:s");
                        if (!empty($_SERVER['HTTP_CLIENT_IP']))
                        {
                                $ip=$_SERVER['HTTP_CLIENT_IP'];
                        }
                        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                        {
                                $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
                        }
                        else
                        {
                                $ip=$_SERVER['REMOTE_ADDR'];
                        }
                        //$ip = ip2long($ip);
                        mysql_class::ex_sqlx("insert into `user_ip` (`id`,`user_id`,`user_ip`,`tarikh`,`en`) values (NULL,'".$this->id."','$ip','$today','-1')");
			$this->logout();
                }
		public function killUser($id_user)
                {
			$out = TRUE;
                        $today = date("Y-m-d H:i:s");
/*
                        $str_time = strtotime($today);
                        $str_ago = $str_time - 600;
                        mysql_class::ex_sql("select `id`,`online_date` from `user` where `id`='$id_user' ",$q);
                        if($r=mysql_fetch_array($q))
                        {
                                $onDate=$r["online_date"];
                                $str_onDate = strtotime($onDate);
                                if (($str_onDate >= $str_ago) && ($str_onDate <= $str_time))
                                {
                                        $out = TRUE;
                                }
                                else
                                {
///					$this->logout();			
					session_destroy();
                                        $out = FALSE;
                                }
			}
*/
//			mysql_class::ex_sql("
			$online_date = strtotime($this->online_date.' + 15 minute');
			if(strtotime(date("Y-m-d H:i:s")) >= $online_date)
			{
				$this->logout();
				if(session_id() != '') {
					session_destroy();
				}
				$out = FALSE;
			}
			return $out;
                }
		public function logout_user()
                {
/*
			mysql_class::ex_sql("select `online_date` from `user` where `id`='$this->id' ",$q);
                        if($r=mysql_fetch_array($q))
                        {
				$onDate=$r["online_date"];
                                $str_onDate = strtotime($onDate);
				$str_ago = $str_onDate - 700;
				$nowtime= date("Y-m-d H:i:s",$str_ago);
				mysql_class::ex_sqlx("update `user` set `online_date`='$nowtime' where `id`=".$this->id);	
			}
*/
		}
		public function getId()
		{
			return $this->id;
		}
		public function loadBetweenUsers($user_id0,$user_id)
		{
			$out = array();
			$u1 = ((int)$user_id0 < (int)$user_id) ? (int)$user_id0 : (int)$user_id;
			$u2 = ((int)$user_id0 < (int)$user_id) ? (int)$user_id : (int)$user_id0;
			for($i = $u1+1;$i < $u2;$i++)
			{
				$user = new user_class($i);
				if($user->getId()>0)
					$out[] = $i;
			}
			return($out);
		}
		public function hasSondogh($user_id=-1)
		{
			$out = FALSE;
			$user_id = (int)$user_id;
			if($user_id <= 0)
				$user_id = $this->id;
			mysql_class::ex_sql("select `id` from `sandogh_user` where `user_id` = $user_id limit 1",$q);
			if($r = mysql_fetch_array($q))
				$out = TRUE;
			return($out);
		}
		public function loadSondogh($user_id,$isAdmin = FALSE,$h_id=1)
                {	
			if ($isAdmin)
			{
				mysql_class::ex_sql("select * from `sandogh`",$q);

				while($r= mysql_fetch_array($q))
				{	
					$id=$r['id'];
		                	$out = array();
		                	$user_id = (int)$user_id;
		               		 if($user_id <= 0)
		                     		   $user_id = $this->id;
					if($h_id=-1)
		                	mysql_class::ex_sql("select `sandogh_id` from `sandogh_user`".(($isAdmin)?'':" where  `user_id` ='$user_id'")." group by `sandogh_id`",$q2);
					else
					mysql_class::ex_sql("select * from `sandogh_user` where `sandogh_id`='$id'",$q2);
		               		 while($r2 = mysql_fetch_array($q2))
		                    	    $out[] = $r2['sandogh_id'];
					//var_dump($out);
				}
			}
			else
			{
			//	mysql_class::ex_sql("select * from `sandogh` where `hotel_id`='$h_id'",$q);
				mysql_class::ex_sql("select * from `sandogh`",$q);
				while($r= mysql_fetch_array($q))
				{	
					$id=$r['id'];
		                	$out = array();
		                	$user_id = (int)$user_id;
		               		 if($user_id <= 0)
		                     		   $user_id = $this->id;
					if($h_id=-1)
		                	mysql_class::ex_sql("select `sandogh_id` from `sandogh_user`".(($isAdmin)?'':" where  `user_id` ='$user_id'")." group by `sandogh_id`",$q2);
//echo "select `sandogh_id` from `sandogh_user`".(($isAdmin)?'':" where  `user_id` ='$user_id'")." group by `sandogh_id`";
					else
					mysql_class::ex_sql("select * from `sandogh_user` where `sandogh_id`='$id'",$q2);
		               		 while($r2 = mysql_fetch_array($q2))
		                    	    $out[] = $r2['sandogh_id'];
					//var_dump($out);
				}
			}
			if(isset($out))
                        	return($out);
                }
	}
?>
