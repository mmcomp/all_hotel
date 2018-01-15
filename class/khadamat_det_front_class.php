<?php
	class khadamat_det_front_class
	{
		public $id=-1;
		public $khadamat_det_id=-1;
		public $sandogh_item_id= -1;
		public $tedad_kol=0;
		public $tedad_used=0;
		public function __construct($id=-1)
		{
			if($id>0)
			{
				mysql_class::ex_sql("select * from `khadamat_det_front` where `id` = $id",$q);
				if($r = mysql_fetch_array($q))
				{
					$this->id =(int)$r['id'];
					$this->khadamat_det_id =$r['khadamat_det_id'];
					$this->sandogh_item_id =$r['sandogh_item_id'];
					$this->tedad_kol =$r['tedad_kol'];
					$this->tedad_used =$r['tedad_used'];
				}
			}
		}
		public function getMaxTedad($khadamat_det_id,$sandogh_item_id)
		{
			$out = 0;
			mysql_class::ex_sql("select (`tedad_kol`-`tedad_used`) as `tedad` from `khadamat_det_front` where `khadamat_det_id` = $khadamat_det_id and `sandogh_item_id` = $sandogh_item_id",$q);
			if($r = mysql_fetch_array($q))
				$out = (int)$r['tedad'];
			return($out);
		}
		public function setTedad_habibi($khadamat_det_id,$sandogh_item_id,$value,$room_id=-1)
                {
			$sum_val = 0;			
			$isUsed = TRUE;
			mysql_class::ex_sql("select `id`,`tedad_used` from `khadamat_det_front` where `khadamat_det_id`=$khadamat_det_id and `tedad_used`<`tedad_kol`",$q);
			while($r = mysql_fetch_array($q))
			{
				$isUsed = FALSE;
				$t_used = $r["tedad_used"];
			}
			$sum_val = $t_used+$value;
			mysql_class::ex_sqlx("update `khadamat_det_front` set `tedad_used` = '$sum_val' where `khadamat_det_id` = $khadamat_det_id and `room_id`='$room_id'");
echo "update `khadamat_det_front` set `tedad_used` = '$sum_val' where `khadamat_det_id` = $khadamat_det_id and `room_id`='$room_id'";
			if($isUsed)
				mysql_class::ex_sqlx("update `khadamat_det` set `isUsed` = 1 where `id` = $khadamat_det_id");
                }
		public function setTedad($khadamat_det_id)
                {
			$isUsed = TRUE;
			mysql_class::ex_sql("select `id` from `khadamat_det_front` where `khadamat_det_id`=$khadamat_det_id and `tedad_used`<`tedad_kol`",$q);
			while($r = mysql_fetch_array($q))
				$isUsed = FALSE;
			if($isUsed)
				mysql_class::ex_sqlx("update `khadamat_det` set `isUsed` = 1 where `id` = $khadamat_det_id");
                }
		public function setFactor($shfactor,$khadamat_det_id)
		{
			mysql_class::ex_sql("select `sandogh_item_id`,`tedad` from `sandogh_factor` where `factor_shomare` = $shfactor",$q);
			while($r = mysql_fetch_array($q))
				mysql_class::ex_sqlx("update `khadamat_det_front` set `tedad_used` = `tedad_used`+".$r['tedad']." where `khadamat_det_id`=$khadamat_det_id and `sandogh_item_id` = ".$r['sandogh_item_id']);
		}
		public function loadCountById($kh_id)
                {
                        $out = '';
			if ($kh_id!='')
			{
		        	mysql_class::ex_sql("select count(`id`) as `cnt` from `khadamat_det_front` where `khadamat_det_id` in ($kh_id) ",$q);
			        if($r = mysql_fetch_array($q))
					$out =(int)$r['cnt'];
			}
			else
				$out = 0;
                        return($out);
                }
	}
?>
