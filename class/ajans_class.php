<?php
	class ajans_class
	{
		public $id=-1;
		public $daftar_id=-1;
		public $name="";
		public $tozihat="";
		public $moeen_id=-1;
		public $tell=0;
                public $ersal_moshtari=-1;
		public $saghf_kharid = 0;
		public $poorsant = 0;
		public $protected = 0;
		public function __construct($id=-1,$isMande=TRUE)
		{
			$conf = new conf;
			$id = (int)$id;
			mysql_class::ex_sql("select * from `ajans` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->daftar_id=$r['daftar_id'];
				$this->name=$r['name'];
				$this->tozihat=$r['tozihat'];
				$this->moeen_id=$r['moeen_id'];
				$this->tell=0;
		                $this->ersal_moshtari=-1;
				$this->saghf_kharid = (int)$r['saghf_kharid'];
				if($conf->ajans_saghf_mande && $isMande )
				{
					$mande = hesab_class::getMandeFromFirst((int)$r['moeen_id'],'moeen');
					$this->saghf_kharid += $mande;
				}
				$this->poorsant = (int)$r['poorsant'];
				$this->protected = (int)$r['protected'];
			}
		}
		public function getScore($aztarikh,$tatarikh,$daftar_id,$loadZero = TRUE)
		{
			$out = array();
			if($tatarikh == '')
				$tatarikh = $aztarikh;
			$daf = (($daftar_id > 0)?" where `daftar_id` = $daftar_id ":'');
			mysql_class::ex_sql("select `id`,`name`,`daftar_id` from `ajans` $daf order by `name`",$ajq);
			while($ajr = mysql_fetch_array($ajq))
			{
				$q = null;
				$ajans_id = (int)$ajr['id'];
				$name = $ajr['name'];
				$daftar_id_counter = (int)$ajr['daftar_id'];
				$nafarshab = 0;
				mysql_class::ex_sql("SELECT `nafar`,DATEDIFF(`tatarikh`,`aztarikh`) as `dated` FROM `room_det` left join `hotel_reserve` on (`room_det`.`reserve_id`=`hotel_reserve`.`reserve_id`) where date(`aztarikh`) >= '$aztarikh' and date(`tatarikh`) <= '$tatarikh' and `ajans_id` = $ajans_id and `room_det`.`reserve_id`>0  group by `room_det`.`reserve_id`",$q);
				while($r = mysql_fetch_array($q))
				{
					$dated = (int)$r['dated'];
					$nafar = (int)$r['nafar'];
					$nafarshab = $dated*$nafar;	
				}
				if($loadZero || $nafarshab > 0)
					$out[] = array('ajans_id' => $ajans_id ,'ajans_name' => $name ,'daftar_id' => $daftar_id_counter , 'nafarshab' => $nafarshab);
			}
			$tmp = $out;
			$ten = array();
			$con = min(10,count($tmp)) ;
			$out = array();
			for($i = 0;$i < count($tmp);$i++)
			{
				$mx = array('ajans_id'=>-1,'nafarshab'=>0);
				$mx_j = -1;
				foreach($tmp as $j => $toongelang)
					if((int)$mx['nafarshab'] < (int)$tmp[$j]['nafarshab'])
					{
						$mx = $tmp[$j];
						$mx_j = $j;
					}
				if((int)$mx['ajans_id'] > 0)
				{
					if($i < $con)
						$ten[] = $mx;
					$out[] = $mx;
					unset($tmp[$mx_j]);
				}
			}		
			return(array('data'=>$out,'top10'=>$ten));
		}
		public function loadByMoeen($moeen_id)
		{
			mysql_class::ex_sql("select * from `ajans` where `moeen_id` = $moeen_id",$q);
                        if($r = mysql_fetch_array($q))
                        {
                                $this->id=$r['id'];
                                $this->daftar_id=$r['daftar_id'];
                                $this->name=$r['name'];
                                $this->tozihat=$r['tozihat'];
                                $this->moeen_id=$r['moeen_id'];
				$this->tell=0;
                                $this->ersal_moshtari=-1;
				$this->protected = (int)$r['protected'];
                        }
		}
		public function getId()
		{
			return($this->id);
		}
		public function decSaghf($ghimat)
		{
			$ghimat  = (int)$ghimat;
			mysql_class::ex_sqlx("update `ajans` set `saghf_kharid` =  (`saghf_kharid`-$ghimat) where `id` = ".$this->id);
			return("update `ajans` set `saghf_kharid` =  (`saghf_kharid`-$ghimat) where `id` = ".$this->id);
		}
		public function loadByDaftar($daftar_id,$loadAll=FALSE)
		{
			$conf = new conf;
			$out = array();
			$daftar_id = (int)$daftar_id;
			mysql_class::ex_sql("select `id`,`name`,`saghf_kharid`,`moeen_id` from `ajans` where `daftar_id` = $daftar_id and `moeen_id`>0 order by `name`",$q);
			while($r = mysql_fetch_array($q))
			{
				$man = 0;
				if(!$loadAll)
					$man = (int)$r['saghf_kharid']+hesab_class::getMandeFromFirst((int)$r['moeen_id'],'moeen');
				if($loadAll || ($man>=$conf->min_saghf_kharid))
					$out[] = array('id'=>(int)$r['id'],'name'=>$r['name']);
			}
			return($out);
		}
		public function loadById($id)
		{
			$out = '';
			mysql_class::ex_sql("select `id`,`name` from `ajans` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
				$out = $r['name'];
			return($out);
		}
		public function loadByDafterId($id)
		{
			$out = array();
			mysql_class::ex_sql("select `id` from `ajans` where `daftar_id` = $id",$q);
			while($r = mysql_fetch_array($q))
				$out[] = $r['id'];
			return($out);
		}
	}
?>
