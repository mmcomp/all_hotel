<?php
	class conf
	{
		public $online_user_id = 48;
		public $online_daftar_id = 48;
		public $online_hotel_id = 80;
		public $online_ajans_id = 48;
		public $host = "localhost";
		public $db = "mirsamie_alborz";
		public $user = "mirsamie_alborz";
		public $pass = "Alb159951";
    public $date_off = "0:00";
//		public $title = "سامانه رزرواسیون هتل بهار";
		public $room_select = TRUE;
		public $enableTafzili = FALSE;
		public $decStartGaant = '5';
		public $addEndGaant = '5';
		public $limitDate = '12/29';
		public $db_eliminator = '-';
		public $mellat_wsdl='https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl';
                public $mellat_namespace='http://interfaces.core.sw.bps.com/';
                public $mellat_terminalId='847703';
                public $mellat_userName='gcom';
                public $mellat_userPassword='gcom1338';
                public $mellat_callBackUrl='http://bahar.gcom.ir/main/purchase.php';
		public $mellat_payPage = 'https://pgw.bpm.bankmellat.ir/pgwchannel/startpay.mellat';
		public $limit_sanad_time = '38';
		public $upYear = 1393;
		public $vorood = TRUE;
		public $anbar = TRUE;
		public $sms = TRUE;
		public $login = 'mmcomp';
		public $password = 'tammar';
		public $from = '30007546000296';
		public $wsdl = 'http://www.payam-resan.com/APISend.aspx?';
		public $limitDatelimit_sanad_time = '9000';
		//public $limitDatelimit_sanad_time = 100000000000000;
		/*public $sms = TRUE;
		public $login = 'smssaaed';
		public $password = '159753';
		public $from = '30001825000145';
		public $wsdl = 'http://sms-ir.ir/webservice/?wsdl';
/*		public $start_vorood = '1390/12/01';
		public $ws_user = 'mehrdad';
		public $ws_pass = '31048145';
		public $reserve_timeout = 10;
		public $limit_sanad_time = '38';//مهلت ویرایش سند پس از روز سند به ساعت منفی یک یعنی نامحدود
		public $daftar_send_date = "2012-04-10 23:59:59";
		public $min_saghf_kharid = 1;
		public $ajans_saghf_mande = TRUE;
		public $zamaiem = TRUE;
		public $vacher_mablagh = TRUE;
		public $room_vaziat = TRUE;
		public $limit_daftar = 1;
		public $limit_ajans = 10;
		public $limit_hotel = 1;
		public $limit_kol_user = 10;
		public $limit_ajans_user = 1;
		public $limit_paziresh_day = 1;
		public $hesab_auto = TRUE;
                public $cost_control = TRUE;
		public $front_office_enabled = FALSE;
*/
		public function  __get($key)
		{
			$out = '';
			if(property_exists(__CLASS__,$key))
				$out = $this->$key;
			else
			{
				mysql_class::ex_sql("select `value` from `conf` where `key` = '$key'",$q);
				if($r = mysql_fetch_array($q))
					$out = $r['value'];
				if($out == 'TRUE')
					$out = TRUE;
				else if($out == 'FALSE')
					$out = FALSE;
			}
			return($out);
		}
		public function __set($key,$value)
		{
			if($value===TRUE)
				$value = 'TRUE';
			if($value===FALSE)
                                $value = 'FALSE';
			if(property_exists(__CLASS__,$key))
				$this->key = $value;
			else
			{
				mysql_class::ex_sql("select `value` from `conf` where `key` = '$key'",$q);
                                if($r = mysql_fetch_array($q))
					mysql_class::ex_sqlx("update `conf` set `value` = '$value' where `key` = '$key'");
				else
					mysql_class::ex_sqlx("insert into `conf` (`key`,`value`) values ('$key','$value')");
			}
		}
		public function checkWsdl($ws_user,$ws_pass)
		{
			session_unset();
			$out = FALSE;
			$moshtari_id_main = -1;
                        $q = NULL;
                        $db = $this->db;
                        $sql = "select `id` from `moshtari`";
                        $conn = mysql_connect($this->host,$this->user,$this->pass);
                        if(!($conn==FALSE)){
	                        if(!(mysql_select_db($db,$conn)==FALSE)){
        		                mysql_query("SET NAMES 'utf8'");
                        		$q = mysql_query($sql,$conn);
		                        mysql_close($conn);
                	        }
                        }
			while($r = mysql_fetch_array($q))
			{
				$moshtari_id_tmp = (int)$r['id'];
	                        $qq = NULL;
				$db = $this->db.$this->db_eliminator.$moshtari_id_tmp;
				$sql = "select `id` from `conf` where `key` = 'ws_user' and MD5(`value`) = '$ws_user'";
        	                $conn = mysql_connect($this->host,$this->user,$this->pass);
                	        if(!($conn==FALSE)){
                        	        if(!(mysql_select_db($db,$conn)==FALSE)){
                                	        mysql_query("SET NAMES 'utf8'");
                                        	$qq = mysql_query($sql,$conn);
						mysql_close($conn);
        	                        }
                        	}
				if($qq != NULL)
					if($rr = mysql_fetch_array($qq))
					{
						$qqq = NULL;
						$sql = "select `id` from `conf` where `key` = 'ws_pass' and MD5(`value`) = '$ws_pass'";
		                                $conn = mysql_connect($this->host,$this->user,$this->pass);
                		                if(!($conn==FALSE)){
                                		        if(!(mysql_select_db($db,$conn)==FALSE)){
                                                		mysql_query("SET NAMES 'utf8'");
		                                                $qqq = mysql_query($sql,$conn);
                		                                mysql_close($conn);
                                		        }
                                		}
						if($qqq != NULL)
							if($rrr	= mysql_fetch_array($qqq))
							{
								$moshtari_id_main = $moshtari_id_tmp;
								$out = TRUE;
							}
					}
			}
			if($moshtari_id_main > 0)
				conf::setMoshtari($moshtari_id_main);
			return($out);
		}
		public function hesab($indx)
		{
			$h = array("group_id"=>null,"kol_id"=>"کل","moeen_id"=>"معین","tafzili_id"=>null,"tafzili2_id"=>null,"tafzilishenavar_id"=>null,"tafzilishenavar2_id"=>null);
			return($h[$indx]);
		}
		public function hesabKol()
		{
			$h = array("group_id"=>null,"kol_id"=>"کل","moeen_id"=>"معین","tafzili_id"=>null,"tafzili2_id"=>null,"tafzilishenavar_id"=>null,"tafzilishenavar2_id"=>null);
			return($h);
		}
		public function setMoshtari($moshtari_id)
		{
			$out = FALSE;
			$matn_sharj = 0;
			$moshtari_id = (int)$moshtari_id;
			$moshtari = new moshtari_class($moshtari_id);
			$today = date("Y-m-d");
			$aztarikh = $moshtari->aztarikh;
//echo $aztarikh;
			$t_pardakhti = $moshtari->tedadpardakhti;
			$mablagh = $moshtari->mablagh;
			$aztarikh = explode(' ',$aztarikh);
			$aztarikh = $aztarikh[0];
			$diff = strtotime($today) - strtotime($aztarikh);
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$modat_gharardad = $months;
//echo $modat_gharardad;
		//	$sharj_mablagh = $modat_gharardad * $mablagh;
		//	$tedad_pardakht = $sharj_mablagh / $mablagh;
			$bedehi = ($modat_gharardad - $t_pardakhti)+1;
			if ($bedehi > 0)
				$matn_sharj =(int) $bedehi * $mablagh; 
			else
				$matn_sharj = 0;
//echo $matn_sharj;
//			$modat_gharardad = 1;
//			$bedehi = ($modat_gharardad - $t_pardakhti)+1;
//			$matn_sharj = $bedehi * $mablagh;
			if($moshtari->id>0 || $moshtari_id == -1)
				$out = TRUE;
			if($out)
			{
				$_SESSION['moshtari_id'] = $moshtari_id;
				$_SESSION['matn_sharj'] =(int) $matn_sharj;
				$_SESSION['bedehi'] = $bedehi;
			}
			else if($moshtari_id == -1)
			{
				$_SESSION['moshtari_id'] = '';
				$_SESSION['matn_sharj'] = '';
				$_SESSION['bedehi'] = '';
			}
			return($out);
		}
		public function getMoshtari()
		{
			$out = '';
			if(isset($_SESSION['moshtari_id']) && (int)$_SESSION['moshtari_id'] > 0)
				$out = (int)$_SESSION['moshtari_id'];
			return($out);
		}
		public function limitChange()
		{
			$out = '';
			mysql_class::ex_sql("select * from `conf` where `id`='40'",$q);
                        if($r=mysql_fetch_array($q))
				$out = $r['value'];
			return($out);
		}
	}
?>