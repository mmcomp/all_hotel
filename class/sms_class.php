<?php
	class sms_class
	{
		public function checkMobile($shomare)
                {
			$out = FALSE;
			$len = strlen($shomare);
			$tell1 = substr($shomare,0,2);
			$t = -1;
			$j = 2;
			for ($i = 0 ;$i <=9 ; $i ++)
			{
				if ((substr($shomare,$j,1)>0) || (substr($shomare,$j,1)<9))
				{
					$t = 1;
				}
				$j++;
			}
			if (($tell1 == "09") && ($len == 11) && ($t == 1))
			{
				$out = TRUE;
			}
			else
			{
				$out = FALSE;
			}
                        return($out);
                }
		public function vorud_text_sms($reserve_id,$shomare,$hotel_id)
                {
                        $out = FALSE;
			$typ_sms = '1';
			if ($reserve_id>0)
			{
                        if (sms_class::checkMobile($shomare))
                        {
                                mysql_class::ex_sql("select * from `sms_send` where `reserve_id` = '$reserve_id' and `sms_typ`='$typ_sms' and `sms_vaz`='1'",$q);
                                if(!($r = mysql_fetch_array($q)))
                                {
						mysql_class::ex_sql("select `user_id` from `room_det` where `reserve_id` = '$reserve_id'",$q2);
	                                	if($r2 = mysql_fetch_array($q2))
        	                        	{
							$user_id = $r2["user_id"];
							mysql_class::ex_sql("select `typ` from `user` where `id` = '$user_id'",$q3);
		                         	       if($r3 = mysql_fetch_array($q3))
                		         	       {
								$typ = $r3["typ"];
								mysql_class::ex_sql("select `name` from `grop` where `id` = '$typ'",$q4);
			                    	            if($r4 = mysql_fetch_array($q4))
			                    	            {
									$user_name = $r4["name"];
									$hoteldar = "هتلدار";
									if (strcmp($user_name, $hoteldar) != 0)
									{
										$text_sms_database = sms_class::text_sms($typ_sms,$hotel_id);
										if ($text_sms_database != "")
										{
											$text_hotel = urlencode(sms_class::text_sms($typ_sms,$hotel_id));	
				                        	                	$send_vaz = sms_class::send_sms($text_hotel,$shomare,$reserve_id,$typ_sms,$text_sms_database);
											if ($send_vaz)
						                	                {
						                	                        $out = TRUE;
							                                }
										}
									}
								}
							}
						}
                	               // }
                	        }
			}
			}
                        return($out);
                }
		public function text_sms($inp,$hotel_id)
                {
                        $out = '';
			mysql_class::ex_sql("select `matn` from `mehman_sms` where `typ`='$inp' and `hotel_id`='$hotel_id'",$q);
                        if($r = mysql_fetch_array($q))
				$out = $r['matn'];
                        return($out);
                }
		public function gasht_text_sms($reserve_id,$shomare,$hotel_id)
                {
                        $out = FALSE;
			$typ = '3';
			$hotel = room_class::loadHotelByReserve($reserve_id);
			$hotel_name = room_class::loadHotelName($hotel);
			if ($reserve_id>0)
			{
			if (sms_class::checkMobile($shomare))
                        {
				mysql_class::ex_sql("select * from `sms_send` where `reserve_id` = '$reserve_id' and `sms_typ`='$typ' and `sms_vaz`='1'",$q);
                                if(!($r = mysql_fetch_array($q)))
                                {	
					$text_sms_database = sms_class::text_sms($typ,$hotel_id);
					if ($text_sms_database != "")
					{
			                        $text =  urlencode(sms_class::text_sms($typ,$hotel_id));
		        	                $send_vaz = sms_class::send_sms($text,$shomare,$reserve_id,$typ,$text_sms_database);
						if ($send_vaz)
		                                {
		                                        $out = TRUE;
		                                }
					}
                	        }
			}
			}
                        return($out);
                }
		public function khoruj_text_sms($reserve_id,$shomare,$hotel_id)
                {
                        $out = FALSE;
			$typ = '2';			
			$hotel = room_class::loadHotelByReserve($reserve_id);
			$hotel_name = room_class::loadHotelName($hotel);
			if ($reserve_id>0)
			{
			if (sms_class::checkMobile($shomare))
                        {
				mysql_class::ex_sql("select * from `sms_send` where `reserve_id` = '$reserve_id' and `sms_typ`='$typ' and `sms_vaz`='1'",$q);
                                if(!($r = mysql_fetch_array($q)))
                                {	
					$text_sms_database = sms_class::text_sms($typ,$hotel_id);
//echo $text_sms_database;
	                                $text =  urlencode(sms_class::text_sms($typ,$hotel_id));
        	                        mysql_class::ex_sqlx("UPDATE `sms_vaz` SET `sms_khoroooj` = '1' WHERE `reserve_id` =$reserve_id");
                	                $send_vaz = sms_class::send_sms($text,$shomare,$reserve_id,$typ,$text_sms_database);
				        if ($send_vaz)
                                        {
                                                $out = TRUE;
                                        }
                	        }
			}
			}
                        return($out);
                }

		public function reserve_text_sms($reserve_id,$shomare,$gheimat,$hotel_id)
		{
			$typ = '6';
			$out = FALSE;
			$ersal_moshtari = -1;
			$ajans_tell = 0;
			$matn_ajans = "";
			if ($reserve_id>0)
			{
			if (sms_class::checkMobile($shomare))
			{
				mysql_class::ex_sql("select `ajans_id` from `hotel_reserve` where `reserve_id` = '$reserve_id'",$q);
                                if($r = mysql_fetch_array($q))
                                {
					$ajans_id = $r["ajans_id"];
					mysql_class::ex_sql("select * from `ajans` where `id` = '$ajans_id'",$qu);
	                                if($row = mysql_fetch_array($qu))
        	                        {
                	                        $ersal_moshtari = $row["ersal_moshtari"];
						$ajans_tell = $row["tell"];
        	                        }
                                }
				if ($ersal_moshtari == 1 )
					$shomare = $ajans_tell;
				$moshtari = new moshtari_class($_SESSION['moshtari_id']);
				$hotel = room_class::loadHotelByReserve($reserve_id);
				$hotel_name = room_class::loadHotelName($hotel);
				$text =  sms_class::text_sms($typ,$hotel_id);
				$text .= " شماره رزرو شما ".$reserve_id;
				$text_sms_database = $text;
				$matn_sms =  urlencode($text);
				mysql_class::ex_sqlx("UPDATE `hotel_reserve` SET `sms_ghimat` = '-1' WHERE `reserve_id` =$reserve_id",$q);
				$send_vaz = sms_class::send_sms($matn_sms,$shomare,$reserve_id,$typ,$text_sms_database);
				if ($send_vaz)
				{
					$out = TRUE;
				}
			}
			}
			return($out);
		}
		public function send_sms($text_sms,$shomare,$reserve_id,$typ_sms,$text_sms_database)
                {
			$conf = new conf;
			$today = date("Y-m-d h:s:i");
			if($conf->sms)
			{
				//Saboori Version
				$out = TRUE;
				$cnt = 0;
				mysql_class::ex_sql("select `cnt`,`sms_vaz` from `sms_send` where `reserve_id` = '$reserve_id' and `sms_typ`='$typ_sms' ",$q);
	                        while($r = mysql_fetch_array($q))
					$cnt = $r['cnt'];
				if ($cnt<2)
				{
					
					if(is_array($shomare))
					{					
						for($i = 0;$i < count($shomare);$i++)
						{
							$cn=curl_init($conf->wsdl);
			                                curl_setopt($cn, "Username=".$conf->login."&Password=".$conf->password."&From=".$conf->from."&To=".$shomare[$i]."&Text=".$text_sms);

			                                $data = curl_exec($cn);
		        	                        $out = (((int)$data==1 && $out)?TRUE:FALSE);
							$vaz = (int)$data;
							$cnt++;
							mysql_class::ex_sqlx("insert into `sms_send` (`id`, `reserve_id`, `sms_matn`, `sms_vaz`, `sms_typ`, `date_send`,`cnt`) values (NULL, '$reserve_id', '$text_sms_database', '$vaz', '$typ_sms', '$today','$cnt')");	
						}
					}
					else
					{
						$cnt++;
						$cn=curl_init($conf->wsdl);
						curl_setopt($cn, CURLOPT_POSTFIELDS,"Username=".$conf->login."&Password=".$conf->password."&From=".$conf->from."&To=".$shomare."&Text=".$text_sms);
						$data = curl_exec($cn);
						$out = (((int)$data==1)?TRUE:FALSE);
						$vaz = (int)$data;
						mysql_class::ex_sqlx("insert into `sms_send` (`id`, `reserve_id`, `sms_matn`, `sms_vaz`, `sms_typ`, `date_send`,`cnt`) values (NULL, '$reserve_id', '$text_sms_database', '$vaz', '$typ_sms', '$today','$cnt')");
					}
				}
			}
			else
				$out =FALSE;
                        return($out);
                }
		public function get_sms()
		{
			$out = null;
			$conf = new conf;
			mysql_class::ex_sql("select `id`,`req` from `tmp_req` where `en` = 0",$q);
                        if($r = mysql_fetch_array($q))
                        {
				$out = $r['req'];
				$id = $r['req'];
				mysql_class::ex_sqlx("UPDATE `tmp_req` SET `en` = '1' WHERE `id` =$id");
			}	
			return($out);
		}
		public function fetchAjansSms()
		{
			$out = array();
			$smss = sms_class::get_sms();
			if ($smss != null && $smss["status"]==0)	
			{
				$smss = $smss["messages"];
				foreach ($smss as $sms)
				{
					$tmp = audit_class::perToEn(trim($sms['message']));
					$tmp = explode(',',$tmp);
					if(count($tmp) == 2)
						$out[] = array('reserve_id'=>(int)trim($tmp[0]),'ghimat'=>(int)trim($tmp[1]),'number'=>$sms['from']);
				}
			}
			return($out);
		}
		public function fetchPeopleSms()
		{
			$out = array();
			$smss = sms_class::get_sms();
			if ($smss != null && $smss["status"]==0)
                        {
                                $smss = $smss["messages"];
	                        foreach($smss as $sms)
        	                {
                	                $tmp = audit_class::perToEn(trim($sms['message']));
                        	        $tmp = explode(',',$tmp);
					$is_ghimat = ((int)trim($tmp[0])>1000) ? TRUE : FALSE;
	                                if(count($tmp) == 1)
        	                                $out[] = array('reserve_id'=>reserve_class::reserveByTel($sms['from'],$is_ghimat),'ghimat'=>(int)trim($tmp[0]),'number'=>$sms['from'],'is_ghimat'=>$is_ghimat);
				}
                        }
                        return($out);
		}
		public function send_sms_group($name,$shomare,$ajans_id)
                {
                        $out = FALSE;
			if (sms_class::checkMobile($shomare))
                        {
                                mysql_class::ex_sqlx("insert into `sms_people` (`id` ,`name` ,`tell` ,`ajans_id`)VALUES (NULL , '$name', '$shomare', '$ajans_id')",$q);
				$out = TRUE;
                        }
                        return($out);
                }
		public function recive_Peoplesms()
                {
                        $out = "";
                        $tmp_sms = array();
                        $tmp_sms = sms_class::fetchPeopleSms();
                        for ($i=0;$i<count($tmp_sms);$i++)
                        {
				$isgheimat = $tmp_sms[$i]["is_ghimat"];
                                $reserve_id = (int)$tmp_sms[$i]["reserve_id"];
                                $gheimat = (int)$tmp_sms[$i]["ghimat"];
				$ghimat_ok = FALSE;
				mysql_class::ex_sql("select `id` from `hotel_reserve` where `reserve_id` =$reserve_id and `sms_ghimat` = -1",$q);
				if($r = mysql_fetch_array($q))
					$ghimat_ok = TRUE;
				$q = null;
				$vaz_ok = FALSE;
				mysql_class::ex_sql("select `id` from `hotel_reserve` where `reserve_id` =$reserve_id and `sms_vaz` = -1",$q);
                                if($r = mysql_fetch_array($q))
                                        $vaz_ok = TRUE;
				if ($isgheimat && $reserve_id > 0 && $ghimat_ok)
				{
		                        mysql_class::ex_sqlx("UPDATE `hotel_reserve` SET `sms_ghimat` = '$gheimat' WHERE `reserve_id` =$reserve_id");
					sanadzan_class::sabtSmsToz($reserve_id,$gheimat);
				}
				else if($gheimat >=1 && $gheimat <=4 && $reserve_id > 0 && $vaz_ok)
				{
					mysql_class::ex_sqlx("UPDATE `hotel_reserve` SET `sms_vaz` = '$gheimat' WHERE `reserve_id` =$reserve_id");
				}
                        }
                        return($out);
                }

		public function recive_Ajanssms()
                {
                        $out = "";
			$tmp_sms = array();
			$tmp_sms = sms_class::fetchAjansSms();
			for ($i=0;$i<count($tmp_sms);$i++)
			{
				$reserve_id = $tmp_sms[$i]["reserve_id"];	
				$gheimat = $tmp_sms[$i]["ghimat"];
				mysql_class::ex_sqlx("UPDATE `hotel_reserve` SET `sms_ghimat` = '$gheimat' WHERE `reserve_id` =$reserve_id");
			}
                        return($out);
                }
	}
?>
