<?php
	class hesab_class
	{
		private $out = array();
		public $hesab = null;
	        public function loadHesab($tbname,$upper_id,$upper_v = -1)
        	{
                	$out = array();
	                //$upper_tb = substr($upper_id,0,-3);
						$wer = ' where 1=1 ';
						if($_SESSION['daftar_id']!=49){
							$kols = array();
							mysql_class::ex_sql("select `kol_id` from `daftar` where `id` = ".$_SESSION['daftar_id'],$ss);
							while($r=mysql_fetch_array($ss)){
								$kols[] = $r['kol_id'];
							}
							if($tbname=='kol'){
								$wer = ' where id in ('.implode(',',$kols).')';
							}else{
								$wer = ' where kol_id in ('.implode(',',$kols).')';
							}
						}
        	        mysql_class::ex_sql("select * from `$tbname` $wer ".(($upper_v == -1 || $upper_id=="_id"  )?"":" and `$upper_id` = '$upper_v' order by `name`"),$q);
                	while($r = mysql_fetch_array($q))
	                {
        	                $out[] = array("id"=>(int)$r["id"],"name"=>$r["name"]);
                	}
	                return($out);
        	}
		public function __construct($hesab = null)
		{	
			$conf = new conf;		
	        	$hesab  = (($hesab == null)?$conf->hesabKol():$hesab);
			$this->hesab = $hesab;
			$p_tb = "";
			$ta_name = "";
	        	foreach($hesab as $key => $value)
        		{
		                if($value==null)
                		{
                	        unset($hesab[$key]);
        		        }
		        }
			$this->hesab = $hesab;			
		        foreach($hesab as $key=>$value)
		        {
	                	$ta_name =substr($key,0,-3);
	        	        $tmp = $this->loadHesab($ta_name,$p_tb."_id",-1);
                		$p_tb = $ta_name;
				$this->out[] = array("table"=>$ta_name,"value"=>$tmp,"name"=>$value);
		        }
		}
		public function load($values = null)
		{
			$hesab  = $this->hesab;
			$this->out = array();
			$p_tb = "";
                        $ta_name = "";
                        foreach($hesab as $key => $value)
                        {
                                if($value==null)
                                {
                                unset($hesab[$key]);
                                }
                        }
			$i = 0;
                        foreach($hesab as $key=>$value)
                        {
                                $ta_name =substr($key,0,-3);
				if(isset($values[$i-1]))
				{
	                                $tmp = $this->loadHesab($ta_name,$p_tb."_id",(int)$values[$i-1]);
				}
				else
				{
					$tmp = $this->loadHesab($ta_name,$p_tb."_id",-1);
				}
                                $p_tb = $ta_name;
                                $this->out[] = array("table"=>$ta_name,"value"=>$tmp,"name"=>$value);
				$i++;
                        }
		}
		public function getOutput()
		{
			return($this->out);
		}
		public function idToName($tbname,$id = -1)
		{
			$id = (int)$id;
			$out = "";
			mysql_class::ex_sql("select `name` from `$tbname` where `id` = '$id' order by `name`",$q);
			if($r = mysql_fetch_array($q))
			{
				$out = $r["name"];
			}
			return($out);
		}
		public function getMande($hesab_id,$hesab_tb,$from_date,$till_date = '')
		{
			mysql_class::ex_sql("select SUM(`mablagh` * `typ`) as `mande` from `sanad` where `$hesab_tb"."_id` = '$hesab_id' and tarikh>='$from_date'  and `tarikh` <= ".(($till_date!='')?"'$till_date'":"NOW()"),$q);
			$out = 0;
			if($r = mysql_fetch_array($q))
			{
				$out = (int)$r["mande"];
			}
			return($out);
		}
		public function getMandeFromFirst($hesab_id,$hesab_tb,$till_date = '')
		{
			$now = date("Y-m-d H:i:s");
			mysql_class::ex_sql("select SUM(`mablagh` * `typ`) as `mande` from `sanad` where `$hesab_tb"."_id` = '$hesab_id' and `tarikh` <= ".(($till_date!='')?"'$till_date'":"'$now'"),$q);
			$out = 0;
			if($r = mysql_fetch_array($q))
				$out = (int)$r["mande"];
			return($out);
		}
	}
?>
