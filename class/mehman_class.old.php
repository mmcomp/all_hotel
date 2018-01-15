<?php
	class mehman_class
	{
		public $id=-1;
		public $reserve_id=-1;
		public $vorood_h='';
		public $fname="";
		public $lname="";
		public $p_name="";
		public $ss="";
		public $tt="";
		public $gender=0;
		public $melliat=-1;
		public $ms=-1;
		public $job="";
		public $safar_dalil="";
		public $mabda=-1;
		public $maghsad=-1;
		public $code_melli="";
		public $nesbat=-1;
		public $hamrah='';
		public $toor_name="";
		public $pish_pardakht=0;
		public $toz="";
		public $hazine=0;
		public $hazine_extra=0;
		public $tedad_extra=0;
		public $khorooj = '0000-00-00 00:00:00';
		public $room_id = -1;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `mehman` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->reserve_id=$r['reserve_id'];
				$this->vorood_h=$r['vorood_h'];
				$this->fname=$r['fname'];
				$this->lname=$r['lname'];
				$this->p_name=$r['p_name'];
				$this->ss=$r['ss'];
				$this->tt=$r['tt'];
				$this->gender=$r['gender'];
				$this->melliat=$r['melliat'];
				$this->ms=$r['ms'];
				$this->job=$r['job'];
				$this->safar_dalil=$r['safar_dalil'];
				$this->mabda=$r['mabda'];
				$this->maghsad=$r['maghsad'];
				$this->code_melli=$r['code_melli'];
				$this->nesbat=$r['nesbat'];
				$this->hamrah=$r['hamrah'];
				$this->toor_name=$r['toor_name'];
				$this->pish_pardakht=$r['pish_pardakht'];
				$this->toz=$r['toz'];
				$this->hazine=$r['hazine'];
				$this->hazine_extra=$r['hazine_extra'];
				$this->tedad_extra=$r['tedad_extra'];
				$this->khorooj=$r['khorooj'];
				$this->room_id=(int)$r['room_id'];
			}
		}
		public function loadByReserveId($reserve_id)
		{
			$out = array();
			mysql_class::ex_sql("select `id` from `mehman` where `reserve_id` = $reserve_id",$q);
			while($r = mysql_fetch_array($q))
			{
				$tmp = new mehman_class((int)$r['id']);
				$out[] = $tmp;
			}
			return($out);
		}
		public function khorooj($reserve_id,$room_id=-1)
		{
			$conf = new conf;
			$reserve_id = (int)$reserve_id;
			$tarikh = date("Y-m-d H:i:s");
			if($room_id>0)
					$room_shart = " and `room_id`=$room_id";
			mysql_class::ex_sqlx("update `mehman` set `khorooj` = '$tarikh' where `reserve_id` = $reserve_id $room_shart");
			//continued
			mysql_class::ex_sql("select `id`,`room_id`,`tatarikh` from `room_det` where `reserve_id`=$reserve_id $room_shart order by `tatarikh` desc",$q);
			$first = TRUE;
			while($r = mysql_fetch_array($q))
			{
				if(strtotime($r['tatarikh'])>strtotime($tarikh) && $first)
					mysql_class::ex_sqlx("update `room_det` set `tatarikh` = '$tarikh' where `id` =".(int)$r['id']);
				$first = FALSE;
				mysql_class::ex_sqlx("update `room` set `vaziat` = 1 where `id` = ".(int)$r['room_id']);
			}
			if($conf->front_office_enabled)
				mysql_class::ex_sqlx("delete from `sandogh_factor` where `reserve_id` = $reserve_id $room_shart and `en`= 0");	
		}
		public function pazireshDate()
		{
			$conf = new conf;
			$cur = strtotime(date("Y-m-d"));
			$out[] = jdate("d / m / Y",$cur);
			for($i = 1;$i <= $conf->limit_paziresh_day;$i++)
			{
				$cur = strtotime(date("Y-m-d",$cur).' - 1 day');
				$out[] = jdate("d / m / Y",$cur);
			}
			return($out);
		}
                public function canPaziresh($aztarikh)
                {
                        $out = FALSE;
                        $az = strtotime($aztarikh);
                        $no = strtotime(date("Y-m-d 23:59:59"));
			$no_e  = strtotime(date("Y-m-d 00:00:00"));
			$conf = new conf;
                        $limit = $conf->limit_paziresh_day;
                        if(($no_e-$limit*24*3600) < $az && $az <=$no)
                                $out = TRUE;
                        return($out);
                }
		public function loadByMehman($reserve_id = 0)
		{
			$out = FALSE;
			$reserve_id = (int)$reserve_id;
			mysql_class::ex_sql("select count( `id` ) as `count_mehman` from `mehman` where `reserve_id` = $reserve_id",$q);
			while($r = mysql_fetch_array($q))
			{
				$out = (int)$r['count_mehman'];
			}
			return($out);
		}
	}
?>
