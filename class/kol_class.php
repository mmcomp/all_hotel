<?php
	class kol_class{
		public $id = -1;
		public $name = "";
		public $grooh_id = -1;
		public function __construct($id=1){
			mysql_class::ex_sql("select * from kol where 1=1 and id=".$id,$q);
			if($r = mysql_fetch_array($q,MYSQL_ASSOC)){
				$this->id = (int)$r['id'];
				$this->name = $r["name"];
				$this->grooh_id = $r["grooh_id"];
			}
		}
		public function getId()
		{
			return $this->id;
		}
                public function loadByCode($code)
                {
                        mysql_class::ex_sql("select * from `kol` where `code`=$code",$q);
                        if($r = mysql_fetch_array($q,MYSQL_ASSOC)){
                                $this->id = (int)$r['id'];
                                $this->name = $r["name"];
                                $this->code = $r['code'];
                                $this->grooh_id = $r["grooh_id"];
                        }
                        return $this->id;
                }
		public function loadByName($name,$load = TRUE)
		{
			mysql_class::ex_sql("select * from `kol` where `name`='$name'",$q);
			if($r = mysql_fetch_array($q,MYSQL_ASSOC))
			{
				if($load)
				{
	                                $this->id = (int)$r['id'];
        	                        $this->name = $r["name"];
                	                $this->code = $r['code'];
                        	        $this->grooh_id = $r["grooh_id"];
				}
                        }
                        return((int)$r['id']);
		}
		public function loadByName_habibi($name)
		{
			mysql_class::ex_sql("select * from `kol` where `name`='$name'",$q);
			if($r = mysql_fetch_array($q,MYSQL_ASSOC))
				$id = (int)$r['id'];
                        return($id);
		}
                public function addById($name)
                {
			$conf=new conf;
                        $out = -1;
			if($conf->hesab_auto)
			{
				$out = kol_class::loadByName($name,FALSE);
				if($out <= 0)
				{
	                	        mysql_class::ex_sql('select max(`code`) as `co` from `kol` ',$q);
        	                	if($r = mysql_fetch_array($q))
	                	        {
        	                	        $code = (int)$r['co'];
                	                	$code++;
	                	                $ln = mysql_class::ex_sqlx("insert into `kol` (`code`,`name`) values ($code,'$name')",FALSE);
        	                	        $out = mysql_insert_id($ln);
                	                	mysql_close($ln);
	                        	}
				}
			}
                        return($out);
                }
	}
?>
