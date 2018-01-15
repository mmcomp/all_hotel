<?php
	class moshtari_class
	{
		public $id=-1;
		public $name="";
		public $mob="";
		public $aztarikh="";
                public $tedadpardakhti=-1;
                public $mablagh=-1;
		public function __construct($id=-1)
		{
			$conf = new conf;
			if($id>0)
			{
	                        $db = $conf->db;
        	                $sql = "select * from `moshtari` where `id` = $id";
                	        $conn = mysql_connect($conf->host,$conf->user,$conf->pass);
                        	if(!($conn==FALSE)){
                                	if(!(mysql_select_db($db,$conn)==FALSE)){
                                        	mysql_query("SET NAMES 'utf8'");
	                                        $q = mysql_query($sql,$conn);
        	                                mysql_close($conn);
                	                }
                        	}
				//mysql_class::ex_sql("select * from `moshtari` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id=$r['id'];
					$this->name=$r['name'];
					$this->mob=$r['mob'];
					$this->aztarikh=$r['aztarikh'];
                                        $this->tedadpardakhti=(int)$r['tedadpardakhti'];
                                        $this->mablagh=(int)$r['mablagh'];
				}
			}
		}
		public function update()
		{
			$conf = new conf;
			$db = $conf->db;
			$sql = "update `moshtari` set `name` = '".$this->name."' , `mob` = '".$this->mob."' , `aztarikh` = '".$this->aztarikh."', `tedadpardakhti` = ".$this->tedadpardakhti." , `mablagh` = ".$this->mablagh." where `id` = ".$this->id;
			$conn = mysql_connect($conf->host,$conf->user,$conf->pass);
			if(!($conn==FALSE)){
				if(!(mysql_select_db($db,$conn)==FALSE)){
					mysql_query("SET NAMES 'utf8'");
					mysql_query($sql,$conn);
					mysql_close($conn);
				}
			}
		}
		public function generateKey($id)
		{
			$id = (int)$id;
			$out =dechex(($id+50000)*2);
			return $out;
		}
		public function getKey($inp)
		{
			if($inp!=-1)
				$inp = (hexdec($inp)/2) - 50000;
			else
				$inp = -1;
			return $inp;
		}
                public function createMoshtari($name,$user,$pass,$mob='')
                {
                        $conf = new conf;
                        if($conf->getMoshtari()=='')
                        {
                                $ln = mysql_class::ex_sqlx("insert into `moshtari` (`name`,`mob`) values ('$name','$mob')",FALSE);
                                $moshtari_id = mysql_insert_id($ln);
                                mysql_close($ln);
                               // mysql_class::ex_sqlx("create database `".$conf->db."_$moshtari_id` character set utf8 collate utf8_persian_ci");
				moshtari_class::createDb("`".$conf->db."-$moshtari_id`");
                                //shell_exec("mysql -u $user ".$conf->db."_$moshtari_id -p'$pass' < ".$conf->db."_empty.sql");
                        }
                }
		public function createDb($name)
		{
			$sock = new HTTPSocket;
			$sock->connect('localhost',2222);

			$sock->set_login("gcom","Tammar666");

			$sock->set_method('GET');

			$sock->query('/CMD_API_DATABASES',
				array(
					'action' => 'create',
					'name' => 'gcom-hassan',
					'user' => 'gcom-hassan',
					'passwd' => 'Tammar666',
					'passwd2' => 'Tammar666'
			    ));

			$result = $sock->fetch_body();
			var_dump($result);
		}
	}
?>
