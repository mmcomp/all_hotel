<?php
	class ajax_responser
	{
		public $command = '';
		public $hotel_id = -1;
		public $test_date='0000-00-00 00:00:00';
		public $delay=0;
		public $rooms = null;
		public $nafar = 0;
		public $daftar_id = -1;
		public $getOutput=FALSE;
		public $ajans_id = -1;
		public $khadamat = array();
		public $room_ezf = NULL;
		public function __construct($command,$hotel_id,$test_date,$delay,$rooms,$nafar,$daftar_id,$ajans_id,$kh,$room_ezf=NULL)
		{
			$this->command = $command;
			$this->hotel_id = (int)$hotel_id;
			$this->test_date = $test_date;
			$this->delay = (int)$delay;
			$this->rooms = $rooms;
			$this->nafar = (int)$nafar;
			$this->daftar_id = (int)$daftar_id;
			$this->ajans_id = $ajans_id;
			$this->khadamat = $kh;
			$this->room_ezf = $room_ezf;
			switch($command)
			{
				case 'roomcheck':
					$this->getOutput = $this->roomcheck();
					break;
				case 'ispick':
					$this->getOutput = $this->isPick();
					break;
				case 'getghimat':
					$this->getOutput = $this->getGhimat();
					break;
				case 'khghimat':
					$this->getOutput = $this->khGhimat();
                                        break;
				case 'roompick':
					$this->getOutput = $this->roomPick();
					break;
			}
		}
		public function isPick()
		{
			$hot = new hotel_class($this->hotel_id);
			$out = 'FALSE';
			$tmp = $this->test_date;
			for($i = 0;$i < $this->delay;$i++)
			{
							if($hot->isPick($tmp))
											$out = 'TRUE';
							$tmp = date("Y-m-d 14:00:00",strtotime($tmp.' + 1 day'));
			}
			return($out);
		}
		public function getGhimat()
		{
			$conf =new conf;
			$out = 0;
			$aj = -1;
			$hot = new hotel_class((int)$this->hotel_id);
			$rs = explode(',',$this->rooms);
// 			var_dump($this->room_ezf);
			$rzf = ($this->room_ezf!=NULL)?explode(',',$this->room_ezf):array();
			$tmp_ezf = array();
			foreach($rzf as $rzf_tmp){
				$ttt = explode('|',$rzf_tmp);
				if(count($ttt)==2){
					$tmp_ezf[$ttt[0]] = $ttt[1];
				}
			}
			$rzf = $tmp_ezf;
// 			var_dump($hot);
			$daftar = new daftar_class($this->daftar_id);
			if($hot->is_shab_nafar!=1){
				foreach($rs as $ro){
					$r = new room_class($ro);
					$rt = new room_typ_class($r->room_typ_id);
					$gh = $rt->getGhimat($this->hotel_id,$this->test_date,$this->delay);
					if($gh!=NULL){
						$out += (int)$gh['ghimat'];
						if(isset($rzf[$ro])){
							$out += ((int)$gh['ghimat_ezafe'])*((int)$rzf[$ro]);
						}
					}
				}
				$out = ceil($out *(1-($daftar->takhfif/100)));
			}else{
				$tmp = $this->test_date;
				$yeknafarshab = 0;
				for($i = 0;$i < (int)$this->delay;$i++)
				{
					$yeknafarshab += $hot->getGhimat($tmp);
					$tmp = date("Y-m-d 14:00:00",strtotime($tmp.' + 1 day'));
				}
				foreach($rs as $ro){
					$r = new room_class($ro);
					$rt = new room_typ_class($r->room_typ_id);
					$out += $rt->zarfiat*$yeknafarshab;
					$gh = $rt->getGhimat($this->hotel_id,$this->test_date,$this->delay);
					if($gh!=NULL){
						if(isset($rzf[$ro])){
							$out += ((int)$gh['ghimat_ezafe'])*((int)$rzf[$ro]);
						}
					}
				}
				$out = ceil($out *(1-($daftar->takhfif/100)));
				if($this->ajans_id>0)
				{
					$aj = new ajans_class($this->ajans_id);
					if($aj->id>0)
						$aj = $aj->saghf_kharid;
					else
						$aj = -1;
				}				
			}
			$out = "$out,$aj";
			return($out);
		}
		public function khGhimat()
		{
			$kh = $this->khadamat;
			$out = 0;
			foreach($kh as $id => $khad)
			{
				$tmp_out = 0;
				if($khad != null)
				{
					$delay = ($this->delay >= 2) ? $this->delay - 2 : 0;
					if (isset($khad->voroodi))
						$voroodi = $khad->voroodi;
					else
						$voroodi = 0;
					if (isset($khad->khorooji))
						$khorooji = $khad->khorooji;
					else
						$khorooji = 0;
					if($voroodi)
						$delay++;
					if($khorooji)
						$delay++;
					$tedad = (int)$khad->tedad;
					$kh_tmp = new khadamat_class((int)$id);
					if($kh_tmp->id >0 && ($tedad > 0 || $voroodi || $khorooji))
					{
						$kh_ghimat = $kh_tmp->ghimat_def;
						if($tedad > 0)
							$tmp_out += $kh_ghimat*$tedad;
						else
							$tmp_out = $kh_ghimat;
						$tmp_out *= $delay;
					}
				}
				$out += $tmp_out;
			}
			return($out);	
		}
		public function roomcheck()
		{
			$out = FALSE;
			if($this->rooms != null)
			{
				$hot = new hotel_class($this->hotel_id);
				$rooms = explode(',',$this->rooms);
				$rooms_zarfiat = 0;
				for($i=0;$i < count($rooms);$i++)
				{
					$tmp_room = new room_class($rooms[$i]);
					$tmp_room = new room_typ_class($tmp_room->room_typ_id);
					$rooms_zarfiat += $tmp_room->zarfiat;
				}
				$out = TRUE;
// 				if($this->isPick() && $rooms_zarfiat > $this->nafar)
// 					$out = FALSE2;
			}
			return($out);
		}
		public function roomPick()
		{
			$out = FALSE;
			if($this->rooms != null)
			{
				$hot = new hotel_class($this->hotel_id);
				$rooms = explode(',',$this->rooms);
				$out = TRUE;
				$before_error = FALSE;
				$after_error = FALSE;
				for($i = 0;$i < count($rooms); $i++)
				{
					if(room_det_class::roomIdAvailable($rooms[$i],date("Y-m-d 14:00:00",strtotime($this->test_date.'-1 day')),1)==null && room_det_class::roomIdAvailable($rooms[$i],date("Y-m-d 14:00:00",strtotime($this->test_date.'-2 day')),2)!=null)
						$before_error = TRUE;
					if(room_det_class::roomIdAvailable($rooms[$i],date("Y-m-d 14:00:00",strtotime($this->test_date.' + '.($this->delay).' day')),1)==null && room_det_class::roomIdAvailable($rooms[$i],date("Y-m-d 14:00:00",strtotime($this->test_date.' + '.($this->delay).' day')),2)!=null)
						$after_error = TRUE;
				}
				if($this->isPick() && ($before_error || $after_error))
					$out = FALSE;
			}
			return($out);
		}
	}
?>