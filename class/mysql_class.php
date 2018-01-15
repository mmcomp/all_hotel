<?php
	class mysql_class
	{
		public $host = '';
		public $db = '';
		public $user = '';
		public $pass = '';
		public $ex_db = FALSE;
		public $insert_id = -1;
		public function ex_sql($sql,&$q)
		{
			/*
			توجه شود در ورژن لوکال چهار قسمت از این کلاس با ورژن وبی فرق دارد
			دو قسمت مربوط به یوزر دیتابیس است که در ورژن لوکال به همین شکل است و در ورژن 
			وبی می بایست قسمت کامنت شده به آن اضافه شود.
			دو قسمت دیگر مربطو به نام دیتابیس است که در لوکال با آندرلاین می باشد و در وب با خط تیره.
			میرسمیع
			*/
			$conf = new conf;
			$host = $conf->host;
			$user = $conf->user;//.(($conf->getMoshtari()!='')?'-'.$conf->getMoshtari():'');
			$pass = $conf->pass;
			$db =$conf->db.(($conf->getMoshtari()!='')?$conf->db_eliminator.$conf->getMoshtari():'');
// 			echo "DB = $db<br/>\n";
			if(isset($this) && get_class($this) == __CLASS__)
			{
				if(isset($this->ex_db) && $this->ex_db)
				{
					$host = $this->host;
					$user = $this->user;
					$pass = $this->pass;
					$db = $this->db;
				}
			}
			$out = "ok";
			$q = NULL;
// 			echo "DB = $db<br/>";
// 			echo "user = $user<br/>";
// 			echo $sql."<br/>";
			$conn = mysql_connect($host,$user,$pass);
			if(!($conn==FALSE)){
				if(!(mysql_select_db($db,$conn)==FALSE)){
					mysql_query("SET NAMES 'utf8'");
					$q = mysql_query($sql,$conn);
					mysql_close($conn);
				}else
					$out = "Select DB Error.";
			}else
				$out = "Connect MySql Error.";
// 			echo $out."<br/>";
			return($out);
		}
		public function ex_sqlx($sql,$close=TRUE)
		{
                        $conf = new conf;
       	                $host = $conf->host;
               	        $db =$conf->db.(($conf->getMoshtari()!='')?$conf->db_eliminator.$conf->getMoshtari():'');
                       	$user = $conf->user;//.(($conf->getMoshtari()!='')?'-'.$conf->getMoshtari():'');
                        $pass = $conf->pass;
			if(isset($this) && get_class($this) == __CLASS__)
                        {
	                        if(isset($this->ex_db) && $this->ex_db)
        	                {
                	                $host = $this->host;
                        	        $user = $this->user;
                                	$pass = $this->pass;
	                                $db = $this->db;
        	                }
			}
			$out = "ok";
			$q = NULL;
			$conn = mysql_connect($host,$user,$pass);
			if(!($conn==FALSE)){
				if(!(mysql_select_db($db,$conn)==FALSE)){
					mysql_query("SET NAMES 'utf8'");
					mysql_query($sql,$conn);
					if(isset($this)){
						$this->insert_id = mysql_insert_id();
					}
					if($close)
						mysql_close($conn);
					else
						$out = $conn;
				}else
					$out = "Select DB Error.";
			}else
				$out = "Connect MySql Error.";
			return($out);
		}
		public function getInArray($feild,$table,$wer)
		{
			$arr = null;
			mysql_class::ex_sql("select `$feild` from `$table` where $wer",$q);
			while($r = mysql_fetch_array($q))
				$arr[] = $r[$feild];
			if($arr!=null)
				$arr = implode(',',$arr);
			return $arr;		
		}
		public function copyAndEmptyTable($tableName,$newTable)
		{
			$out = FALSE;
			if($tableName != '' && $newTable != 'daftar' && $newTable != 'anbar' && $newTable != 'reserve' && strpos('_',$newTable)===FALSE)
			{
				$out = (mysql_class::ex_sqlx("create table if not exists `$newTable` select * from `$tableName`")=='ok');
				if($out)
				{
					if(mysql_class::ex_sqlx("alter table `$newTable` change column `id` int(11) auto_increment not null ")=='ok')
						if(mysql_class::ex_sqlx("alter table `$newTable` add primary key (`id`)")=='ok')
							$out = (mysql_class::ex_sqlx("truncate `$tableName` ")=='ok');
						else
							$out = FALSE;
					else
						$out = FALSE;
				}
			}
			return($out);
		}
		public function startSanad($saleMali,$user_id = -1)
		{
			$out = FALSE;
			if($saleMali != '')
			{
				$user_id = (int)$user_id;
				$user_id = (($user_id <= 0)?(int)$_SESSION['user_id']:$user_id);
				$moeens = null;
				mysql_class::ex_sql("select  `kol_id`,`moeen_id`,SUM(`mablagh` * `typ`) as `mande` from `sanad_$saleMali` group by `moeen_id`",$q);
				while($r = mysql_fetch_array($q))
				{
					mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`, `moeen_id`, `tarikh`, `user_id`, `typ`, `tozihat`, `en`, `mablagh`) values (1,".$r['kol_id'].",".$r['moeen_id'].",'".date("Y-m-d H:i:s")."','$user_id',".(((int)$r['mande']<0)?'-1':'1').",'سند افتتاحیه $saleMali',1,".abs($r['mande']).")");
					$moeens[] = $r['moeen_id'];
				}
				$q = null;
				mysql_class::ex_sql('select `id`,`kol_id` from `moeen` where `id` not in ('.(implode(',',$moeens)).')',$q);
				while($r = mysql_fetch_array($q))
					mysql_class::ex_sqlx("insert into `sanad` (`shomare_sanad`,`kol_id`, `moeen_id`, `tarikh`, `user_id`, `typ`, `tozihat`, `en`, `mablagh`) values (1,".$r['kol_id'].",".$r['id'].",'".date("Y-m-d H:i:s")."','$user_id',1,'ﺲﻧﺩ ﺎﻔﺘﺗﺎﺣیﻩ $saleMali',1,0)");
				$out = TRUE;
			}
			return($out);
		}
		public function loadSaleMali()
		{
			$out = array();
			mysql_class::ex_sql("show tables;",$q);
			while($r = mysql_fetch_array($q))
			{
				$tmp = explode('_',$r[0]);
				if(count($tmp) >= 2 && $tmp[0]=='sanad' && $tmp[1] != 'daftar' && $tmp[1] != 'anbar' && $tmp[1] != 'reserve' && $tmp[1] != 'sandogh')
				{
					unset($tmp[0]);
					$out[] = implode('_',$tmp);
				}
			}
			return($out);
		}
	}
?>
