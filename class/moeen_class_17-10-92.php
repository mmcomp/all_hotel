<?php
	class moeen_class
	{
		public $id=-1;
		public $kol_id=-1;
		public $code=-1;
		public $name="";
		public $typ=-1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `moeen` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->kol_id=$r['kol_id'];
				$this->code=$r['code'];
				$this->name=$r['name'];
				$this->typ=$r['typ'];
			}
		}
                public function newByCode($kol_code,$name)
                {
                        $kol = new kol_class;
                        $kol->loadByCode($kol_code);
                        mysql_class::ex_sql('select max(`code`) as `co` from `moeen` where `kol_id` = '.$kol->id,$q);
                        if($r = mysql_fetch_array($q))
                        {
                                $code = (int)$r['co'];
                                $code++;
                                mysql_class::ex_sqlx("insert into `moeen` (`kol_id`,`code`,`name`) values (".$kol->id.",$code,'$name')");
                                mysql_class::ex_sql("select * from `moeen` where `code` = $code",$q);
                                if($r = mysql_fetch_array($q))
                                {
                                        $this->id=$r['id'];
                                        $this->kol_id=$r['kol_id'];
                                        $this->code=$r['code'];
                                        $this->name=$r['name'];
                                        $this->typ=$r['typ'];
                                }
                        }
                }
                public function addById($kol_id,$name,$code_global = TRUE)
                {
			$out = -1;
			$conf = new conf;
			if($conf->hesab_auto)
                        {
	                        mysql_class::ex_sql('select max(`code`) as `co` from `moeen` '.((!$code_global)?'where `kol_id` = '.$kol_id:''),$q);
        	                if($r = mysql_fetch_array($q))
                	        {
                        	        $code = (int)$r['co'];
                                	$code++;
	                                $ln = mysql_class::ex_sqlx("insert into `moeen` (`kol_id`,`code`,`name`) values ($kol_id,$code,'$name')",FALSE);
					$out = mysql_insert_id($ln);
					mysql_close($ln);
                        	}
			}
			return($out);
                }
	}
?>
