<?php
	class hotel_reserve_class
	{
		public $id=-1;
		public $fname="";
		public $lname="";
		public $tozih="";
		public $reserve_id=-1;
		public $ajans_id=-1;
		public $m_belit=0;
		public $m_belit1=0;
		public $m_belit2=0;
		public $m_belit3=0;
		public $m_hotel = 0;
		public $regdat = '0000-00-00 00:00:00';
		public $other_id = array();
		public $sms_ghimat = -2;
                public $sms_vaz = -2;
		public $extra_toz = '';
		public $isOnline = FALSE;
		public $jabejayi_count = 0;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `hotel_reserve` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->fname=$r['fname'];
				$this->lname=$r['lname'];
				$this->tozih=$r['tozih'];
				$this->reserve_id=$r['reserve_id'];
				$this->ajans_id=$r['ajans_id'];
				$this->m_belit=(int)$r['m_belit1']+(int)$r['m_belit2'];
				$this->m_belit1=(int)$r['m_belit1'];
                                $this->m_belit2=(int)$r['m_belit2'];
				$this->m_belit3=(int)$r['m_belit3'];
                                $this->m_belit=$r['m_hotel'];
				$this->regdat = $r['regdat'];
				$this->other_id = (($r['other_id']!=null)?unserialize($r['other_id']):array());
				$this->sms_ghimat = $r['sms_ghimat'];
		                $this->sms_vaz = $r['sms_vaz'];
				if(isset($r['extra_toz']))
					$this->extra_toz = $r['extra_toz'];
				if(isset($r['isOnline']))
                                        $this->isOnline = (((int)$r['isOnline']==1)?TRUE:FALSE);
				$this->jabejayi_count = (int)$r['jabejayi_count'];
			}
		}
		public function loadByReserve($reserve_id=-1)
		{
			$out = FALSE;
			$reserve_id = (int)$reserve_id;
			if($reserve_id != 0)
			{
				mysql_class::ex_sql("select * from `hotel_reserve` where `reserve_id` = $reserve_id",$q);
        	                if($r = mysql_fetch_array($q))
                	        {
                        	        $this->id=$r['id'];
                                	$this->fname=$r['fname'];
	                                $this->lname=$r['lname'];
        	                        $this->tozih=$r['tozih'];
                	                $this->reserve_id=$r['reserve_id'];
                        	        $this->ajans_id=$r['ajans_id'];
                                	$this->m_belit=(int)$r['m_belit1']+(int)$r['m_belit2'];
	                                $this->m_belit1=(int)$r['m_belit1'];
        	                        $this->m_belit2=(int)$r['m_belit2'];
					$this->m_belit3=(int)$r['m_belit3'];
					$this->m_hotel=$r['m_hotel'];
					$this->regdat = $r['regdat'];
					$this->other_id = (($r['other_id']!=null)?unserialize($r['other_id']):array());
					$this->sms_ghimat = $r['sms_ghimat'];
        	                        $this->sms_vaz = $r['sms_vaz'];
					if(isset($r['extra_toz']))
						$this->extra_toz = $r['extra_toz'];
					if(isset($r['isOnline']))
	                                        $this->isOnline = (((int)$r['isOnline']==1)?TRUE:FALSE);
					$this->jabejayi_count = (int)$r['jabejayi_count'];
					$out = TRUE;
	                        }
			}
			return($out);
		}
		public function loadAjName($reserve_id=-1)
		{
			$out = '';
			$reserve_id = (int)$reserve_id;
			if($reserve_id != 0)
			{
				mysql_class::ex_sql("select `ajans_id` from `hotel_reserve` where `reserve_id` = $reserve_id",$q);
        	                if($r = mysql_fetch_array($q))
                        	        $out=$r['ajans_id'];
			}
			return($out);
		}
		public function loadAjName_habibi($reserve_id=-1)
		{
			$out = '';
			$reserve_id = (int)$reserve_id;
			if($reserve_id != 0)
			{
				mysql_class::ex_sql("select `ajans_id` from `hotel_reserve` where `reserve_id` = $reserve_id",$q);
        	                if($r = mysql_fetch_array($q))
				{
					$ajans_id=$r['ajans_id'];
					mysql_class::ex_sql("select `name` from `ajans` where `id` = '$ajans_id'",$q_aj);
        	                	if($r_aj = mysql_fetch_array($q_aj))
						$out = $r_aj['name'];
				}
			}
			return($out);
		}
		public function loadByName($name)
		{
			$out = array();
			if($name != '')
			{
				mysql_class::ex_sql("select `reserve_id` from `hotel_reserve` where `lname` like '%$name%'",$q);
        	                while($r = mysql_fetch_array($q))
                        	        $out[]=$r['reserve_id'];
			}
			return($out);
		}
		public function loadDaftarByReserveId($reserve_id)
		{
			$out = '';
			if($reserve_id != '')
			{
				$ajId = hotel_reserve_class::loadAjName($reserve_id);
				if ($ajId != '')
				{
					mysql_class::ex_sql("select `daftar_id` from `ajans` where `id` = $ajId",$q);
			                if($r = mysql_fetch_array($q))
						$out = $r['daftar_id'];
				}
			}
			return($out);
		}		
	}
?>
