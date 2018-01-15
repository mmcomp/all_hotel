<?php
	class cost_kala_class
	{
		public $id=-1;
		public $name="";
		public $toz="";
		public $det = array();
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `cost_kala` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->toz=$r['toz'];
				mysql_class::ex_sql('select * from `cost_det` where `cost_kala_id` = '.$r['id'],$qq);
				while($rr = mysql_fetch_array($qq))
					$this->det[] = array('kala_id'=>$rr['kala_id'],'tedad'=>$rr['tedad']);
			}
		}
		public function cost_anbar_sabt($khadamat_id,$tedad,$max_tedad,$kala_cost,$tarikh,$is_insert=FALSE)
		{
			$bool = TRUE;
			$out = -1;
			mysql_class::ex_sql("select sum(`tedad`) as `jam` from `cost_anbar` where `khadamat_id`='$khadamat_id' and date(`tarikh`)='$tarikh'",$q);
			if($r = mysql_fetch_array($q))
			{
				if($tedad>=($max_tedad - (int)$r['jam']))
					$bool = FALSE;
			}
			if($bool && $is_insert)
			{
				mysql_class::ex_sqlx("insert into `cost_anbar` (`cost_kala_id`,`khadamat_id`,`tarikh`,`tedad`) values ('$kala_cost','$khadamat_id','$tarikh','$tedad') ");
				$out = mysql_insert_id();
			}
			return $bool;
		}
		public function cost_factor_khorooj($ghaza_moeen_id,$hotel_name,$tarikh,$toz,$user_id)
		{
			mysql_class::ex_sqlx("insert into `anbar_factor` (`factor_id`,`name`,`tozihat`,`moeen_id`,`tarikh_resid`,`anbar_typ_id`,`user_id`) values ('کالای ترکیبی ','$hotel_name','$toz','$ghaza_moeen_id','$tarikh','2','$user_id')");
			$out = mysql_insert_id();
			return $out;
		}
	}
?>
