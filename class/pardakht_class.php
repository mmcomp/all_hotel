<?php
	class pardakht_class
	{
		public $id=-1;
		public $moshtari_id=-1;
		public $tarikh=-1;
		public $mablagh=-1;
		public $bank_out='';
		public function __construct($id=-1)
		{
                        $conf = new conf;
                        if($id>0)
                        {
                                $db = $conf->db;
                                $sql = "select * from `pardakht` where `id` = $id";
                                $conn = mysql_connect($conf->host,$conf->user,$conf->pass);
                                if(!($conn==FALSE)){
                                        if(!(mysql_select_db($db,$conn)==FALSE)){
                                                mysql_query("SET NAMES 'utf8'");
                                                $q = mysql_query($sql,$conn);
                                                mysql_close($conn);
                                        }
                                }
				if($r = mysql_fetch_array($q))
				{
					$this->id=$r['id'];
					$this->moshtari_id=(int)$r['moshtari_id'];
					$this->tarikh=$r['tarikh'];
					$this->mablagh=(int)$r['mablagh'];
					$this->bank_out=$r['bank_out'];
					if($r['bank_out'] != '')
						$this->bank_out=unserialize($r['bank_out']);
				}
			}
		}
		public function add($moshtari_id,$tarikh,$mablagh,$bank_out='')
		{
			$out = -1;
			$conf = new conf;
			$db = $conf->db;
			$sql = "insert into `pardakht` (`moshtari_id`,`tarikh`,`mablagh`,`bank_out`) values ($moshtari_id,'$tarikh',$mablagh,'$bank_out')";
			$conn = mysql_connect($conf->host,$conf->user,$conf->pass);
			if(!($conn==FALSE)){
	                        if(!(mysql_select_db($db,$conn)==FALSE)){
        	                        mysql_query("SET NAMES 'utf8'");
                                        mysql_query($sql,$conn);
					$out = mysql_insert_id($conn);
                                        mysql_close($conn);
                                }
                       	}
			return($out);
		}
		public function update()
		{
                        $conf = new conf;
                        $db = $conf->db;
                        $sql = "update `pardakht` set `bank_out` = '".$this->bank_out."' where `id` = ".$this->id;
                        $conn = mysql_connect($conf->host,$conf->user,$conf->pass);
                        if(!($conn==FALSE)){
                                if(!(mysql_select_db($db,$conn)==FALSE)){
                                        mysql_query("SET NAMES 'utf8'");
                                        mysql_query($sql,$conn);
                                        mysql_close($conn);
                                }
                        }
			
		}
	}
?>
