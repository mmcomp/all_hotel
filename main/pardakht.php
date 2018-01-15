<?php   
	include_once('../kernel.php');
        function search($ws_user,$ws_pass,$aztarikh,$shab,$tedad,$aj_id=-1)
        {
		$conf = new conf;
                $out = null;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$out = '';
                        if($aztarikh >= date("Y-m-d H:i:s"))
                        {
                        	$out = array();
                                $tatarikh = date("Y-m-d 14:00:00",strtotime($aztarikh.' + '.$shab.' day'));
                                $hotels = hotel_class::getHotels();
                                for($i = 0;$i < count($hotels);$i++)
                                {
                                        $tmp = null;
                                        $gh = 0;
                                        $hotel = new hotel_class((int)$hotels[$i]['id']);
                                        if($hotel->hotelAvailableBetween($aztarikh,$tatarikh,FALSE))
                                        {
                                                $dat_tmp = $aztarikh;
                                                for($j = 0;$j < (int)$shab;$j++)
                                                {
                                                        $gh += $hotel->getGhimat($dat_tmp);
                                                        $dat_tmp = date("Y-m-d 14:00:00",strtotime($dat_tmp.' + 1 day'));
                                                }
                                                if($aj_id > 0)
                                                {
                                                        $ajans = new ajans_class($aj_id);
                                                        $gh = (int)(100-$ajans->poorsant)*$gh / 100;
                                                }
                                                $tmp['ghimat'] = $gh;
                                                $tmp['hotel'] = $hotels[$i];
                                                $tmp['room_typs'] = room_class::loadOpenRooms($aztarikh,$shab,FALSE,FALSE,$hotels[$i]['id'],0);
                                                $tmp['khadamat'] = khadamat_class::loadKhadamats($hotels[$i]['id']);
                                                if(count($tmp['room_typs']) > 0)
                                                        $out[] = $tmp;
                                        }
                                }
	                        $out = serialize($out);
                        }
                }
                return($out);
        }
        function tedadOk($ws_user,$ws_pass,$rooms,$tedad)
        {
                $conf = new conf;
                $out = null;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$out = 'ghalat';
			$rt=$rooms;
                        $t = explode(',',$rooms);
                        $rooms = $t;
			$room_zar = 0;
			for($i = 0;$i < count($rooms);$i++)
			{
				$r = new room_class((int)$rooms[$i]);
				$r = new room_typ_class($r->room_typ_id);
				$room_zar+=$r->zarfiat;
			}
			if($room_zar > 0)
			{
	                        $tmp = ceil($room_zar/5);
        	                $out = ($tedad <= ($tmp+$room_zar))?'dorost':'ghalat';
			}
                }
                return($out);
        }
	function getGhimat($ws_user,$ws_pass,$aztarikh,$shab,$room_id,$tedad,$khadamat,$aj_id=-1)
	{
		$out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$out = 0;
			$t = explode(',',$room_id);
			$room_id = (count($t)>1)?$t:$room_id;
			$room_zar = 0;
			if(is_array($room_id))
			{
				for($i = 0;$i < count($room_id); $i++)
				{
					$room = new room_class($room_id[$i]);
					$rt = new room_typ_class($room->room_typ_id);
					$room_zar += $rt->zarfiat;
				}
			}
			else
			{
				$room = new room_class($room_id);
				$rt = new room_typ_class($room->room_typ_id);
				$room_zar = $rt->zarfiat;
			}
			$hotel = new hotel_class($room->hotel_id);
			$tmp = $aztarikh;
			$is_pick = FALSE;
       	                for($i = 0;$i < (int)$shab;$i++)
                        {			
                       	        $out += $hotel->getGhimat($tmp);
				if($hotel->isPick($tmp)) 
					$is_pick = TRUE;
               	                $tmp = date("Y-m-d 14:00:00",strtotime($tmp.' + 1 day'));
       	                }
			//$out *= ($is_pick)?$room_zar:$tedad;
			$out *= max($room_zar,$tedad);
			$khad = 0;
			$khadamat = unserialize($khadamat);
			$kh_tedad = $shab - 2;
			if($kh_tedad < 0)
				$kh_tedad = 0;
			if(is_array($khadamat))
			{
				for($i = 0;$i < count($khadamat);$i++)
				{
					$tmp_khadamat = new khadamat_class((int)$khadamat[$i]['id']);
					$khad += $tmp_khadamat->ghimat_def * (int)$khadamat[$i]['tedad'] * ($kh_tedad + (($khadamat[$i]['voroodi'])?1:0) + (($khadamat[$i]['khorooji'])?1:0));
				}
			}
			$out += $khad;
			if($aj_id > 0)
			{
				$ajans = new ajans_class($aj_id);
				$out = (int)(100-$ajans->poorsant)*$out / 100;
			}
		}
		return($out);
	}
	function isPick($room_id,$aztarikh,$shab)
	{
		$t = explode(',',$room_id);
		$room_id = (count($t)>1)?$t:$room_id;
		if(count($t)>1)
			$room = new room_class((int)$t[0]);
		else
			$room = new room_class((int)$room_id);
		$hotel = new hotel_class($room->hotel_id);
		$tmp = $aztarikh;
		$is_pick = FALSE;
		for($i = 0;$i < (int)$shab;$i++)
		{
			$out += $hotel->getGhimat($tmp);
			if($hotel->isPick($tmp))
				$is_pick = TRUE;
			$tmp = date("Y-m-d 14:00:00",strtotime($tmp.' + 1 day'));
		}
		return($is_pick);
	}
	function register_reserve($ws_user,$ws_pass,$ajans_id,$aztarikh,$shab,$room_id,$tedad,$khadamat,$fname,$lname,$tel,$user_id,$extra_toz,$comision,$aj_id)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
//-------------------------------------------------------------------------------------------------------------------------
			$ajans_id = unserialize($ajans_id);
			$comision = unserialize($comision);
			if(count($comision) != 3 || count($ajans_id) != 2)
				return(FALSE);
			$ajans_array = $ajans_id;
			$ajans_id = $ajans_id['ajans_id'];
//-------------------------------------------------------------------------------------------------------------------------
			$ghimat = getGhimat($ws_user,$ws_pass,$aztarikh,$shab,$room_id,$tedad,$khadamat,$aj_id);
                        $t = explode(',',$room_id);
                        $room_id = (count($t)>1)?$t:$room_id;
			if(is_array($room_id))
                                $room = new room_class($room_id[0]);
			else			
				$room = new room_class($room_id);
			$aj = new ajans_class($aj_id);
			$khad = unserialize($khadamat);
			if($ghimat <= $aj->saghf_kharid || $aj->saghf_kharid < 0 || $aj_id == -1)
			{
				if($aj_id == -1)
				{
					$sanad_recs = room_det_class::onlinePreReserve($room->hotel_id,$ajans_array,$comision,(is_array($room_id))?$room_id:array($room_id),$ghimat,$aztarikh,$shab,1,FALSE,FALSE,$tedad,$khad,$user_id);
				}
				else
					$sanad_recs = room_det_class::preReserve($room->hotel_id,$aj_id,(is_array($room_id))?$room_id:array($room_id),$ghimat,$aztarikh,$shab,1,FALSE,FALSE,$tedad,$khad,$user_id);
				if($sanad_recs !== FALSE)
				{
					$toz["toz"] = $tel;
					$toz["extra_toz"] = $extra_toz;
					if($aj_id == -1)
						room_det_class::sabtOnlineReserveHotel($sanad_recs['reserve_id'],$sanad_recs['shomare_sanad'],null,'',$fname.' '.$lname,$toz,$ajans_id,$ghimat,'');
					else
						room_det_class::sabtReserveHotel($sanad_recs['reserve_id'],$sanad_recs['shomare_sanad'],null,'',$fname.' '.$lname,$toz,$aj_id,$ghimat,'');
					if($aj_id==-1)
					{
						$tmp = reserve_tmp_class::add($sanad_recs['reserve_id']);
						$sanad_recs['reserve_timeout'] = $conf->reserve_timeout;
						$sanad_recs['regtime'] = $tmp['tarikh'];
					}
					else
					{
						$sanad_recs['reserve_timeout'] = 0;
						$sanad_recs['query'] = $aj->decSaghf($ghimat);
					}
					$sanad_recs['ghimat'] = $ghimat;
				}
                        	$out = serialize($sanad_recs);
			}
			else
			{
				$out = 'FALSE';
			}
                }
                return($out);
        }
	function reserve_timeout($ws_user,$ws_pass)
	{
		$out = FALSE;
                $conf = new conf;
		if($conf->checkWsdl($ws_user,$ws_pass))
                {
		        $tarikh = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s").' - '.$conf->reserve_timeout.' minute'));
		        mysql_class::ex_sql("select `reserve_id` from `reserve_tmp`  where `tarikh` < '$tarikh'",$q);
			while($r = mysql_fetch_array($q))
				room_det_class::deleteReserve((int)$r['reserve_id'],TRUE,TRUE);
			mysql_class::ex_sqlx("delete from `reserve_tmp`  where `tarikh` < '$tarikh'");
			$out = TRUE;
		}
		return($out);
	}
	function kill_reserve($ws_user,$ws_pass,$reserve_id)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$out = FALSE;
			$reserve = new reserve_class((int)$reserve_id);
			if($reserve->id>0)
			{
				room_det_class::killReserve((int)$reserve_id);
	                        $out = TRUE;
			}
                }
                return($out);
        }
        function reserve_tmpList($ws_user,$ws_pass)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$out = array();
                        $tarikh = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s").' - '.$conf->reserve_timeout.' minute'));
                        mysql_class::ex_sql("select `reserve_id` from `reserve_tmp`  where `tarikh` > '$tarikh'",$q);
                        while($r = mysql_fetch_array($q))
                                $out[] = (int)$r['reserve_id'];
                        $out = serialize($out);
                }
                return($out);
        }
        function reserve_verify($ws_user,$ws_pass,$reserve_id)
        {
                $out = FALSE;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$resrve_id = (int)$reserve_id;
			mysql_class::ex_sql("select `id` from `reserve_tmp`  where `reserve_id` = $reserve_id",$q);
			if($r = mysql_fetch_array($q))
			{
	                        mysql_class::ex_sqlx("delete from `reserve_tmp`  where `reserve_id` = $reserve_id");
				$out = TRUE;
			}
                }
                return($out);
        }
	function ajans_login($ws_user,$ws_pass,$aj_user,$aj_pass)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
                        $out = array();
                        mysql_class::ex_sql("select `id`,`user`,`pass`,`ajans_id`,`typ`,`fname`,`lname` from `user`  where `user` = '$aj_user'",$q);
                        if($r = mysql_fetch_array($q))
                                if(hash('md5',$r['pass']) == $aj_pass && (int)$r['ajans_id'] > 0)
				{
					$out['id'] = $r['id'];
					$out['ajans_id'] = $r['ajans_id'];
					$out['fname'] = $r['fname'];
					$out['lname'] = $r['lname'];
					$out['typ'] = $r['typ'];
				}
                        $out = serialize($out);
                }
                return($out);
        }
        function ajans_data($ws_user,$ws_pass,$aj_id)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
                        $out = array();
			$ajans = new ajans_class($aj_id);
			if($ajans->id > 0)
			{
				$out['name'] = $ajans->name;
				$out['saghf_kharid'] = $ajans->saghf_kharid;
				$out['poorsant'] = $ajans->poorsant;
			}
                        $out = serialize($out);
                }
                return($out);
        }
	function sanad_gozaresh($ws_user,$ws_pass,$ajans_id,$aztarikh,$tatarikh,$frase)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$out = FALSE;
			$ajans = new ajans_class($ajans_id);
			if($ajans->id > 0)
			{
				$out = array();
				$moeen_id = $ajans->moeen_id;
				$mo = new moeen_class($moeen_id);
				$kol_id = $mo->kol_id;
				$shart = " `moeen_id` = $moeen_id and `kol_id` = $kol_id and ";
				$out['mande_az_ghabl'] = 0;
				$out['mande_az_ghabl_bes'] = 0;
				$out['mande_az_ghabl_bed'] = 0;
		                mysql_class::ex_sql("select sum(`typ`*`mablagh`) as `man` from `sanad` where $shart `tarikh` < '$aztarikh'",$qmand);
		                if($r = mysql_fetch_array($qmand))
		                        $out['mande_az_ghabl'] = (int)$r['man'];
		                $qmand = null;
		                mysql_class::ex_sql("select sum(`mablagh`) as `man` from `sanad` where $shart `typ`=1 and `tarikh` < '$aztarikh'",$qmand);
		                if($r = mysql_fetch_array($qmand))
		                        $out['mande_az_ghabl_bes'] = (int)$r['man'];
		                $qmand = null;
		                mysql_class::ex_sql("select sum(`mablagh`) as `man` from `sanad` where $shart `typ`=-1 and `tarikh` < '$aztarikh'",$qmand);
		                if($r = mysql_fetch_array($qmand))
		                        $out['mande_az_ghabl_bed'] = (int)$r['man'];
				$out1 = array();
				$query = "select `shomare_sanad`,`kol_id`,`moeen_id`,`tarikh`,`mablagh`,`typ`,`id`,`tozihat` from `sanad` where $shart `tarikh`>='$aztarikh' and `tarikh`<='$tatarikh' ".(($frase!='')?" and `tozihat` like '%$frase%' ":'')." order by `shomare_sanad`,`tarikh`,`id`";
				mysql_class::ex_sql($query,$q);
				while($r = mysql_fetch_array($q))
				{
					$tmp = array();
					for($i = 0;$i <  mysql_num_fields($q);$i++)
					{
						$fi = mysql_fetch_field($q,$i);
						$tmp[$fi->name] = $r[$fi->name];
					}
					$tmp['kol_id'] = hesab_class::idToName('kol',$tmp['kol_id']);
					$tmp['moeen_id'] = hesab_class::idToName('moeen',$tmp['moeen_id']);
					$out1[] = $tmp;
				}
				$out['data'] = $out1;
				$out = serialize($out);
			}
                }
                return($out);
        }
        function hotel_data($ws_user,$ws_pass,$ht_id)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
                        $hotel = new hotel_class($ht_id);
                        $out = serialize($hotel);
                }
                return($out);
        }
        function object_data($ws_user,$ws_pass,$class_name,$id)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$out = FALSE;
			if(class_exists($class_name))
			{
	                        $out = new $class_name($id);
        	                $out = serialize($out);
			}
                }
                return($out);
        }
        function room_typ_data($ws_user,$ws_pass,$rt_id)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
                        $room_typ = new room_typ_class($rt_id);
                        $out = serialize($room_typ);
                }
                return($out);
        }
	function load_reserve($ws_user,$ws_pass,$reserve_id)
	{
		$out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$r = new reserve_class($reserve_id);
			$out = serialize($r);
		}
		return($out);
	}
        function load_reserves($ws_user,$ws_pass,$tarikh,$ajans_id,$key)
        {
                $out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$feild = ' 1=0 ';
			$tarikh = date("Y-m-d",strtotime($tarikh));
			switch($key)
			{
				case 0:
					$feild = " date(`regdat`) = '$tarikh' ";
					break;
				case 1:
					$feild = " date(`aztarikh`) = '$tarikh' ";
                                        break;
				case 2:
					$feild = " date(`tatarikh`) = '$tarikh' ";
                                        break;
			}
			$ajans_id = (int)$ajans_id;
			mysql_class::ex_sql("select `hotel_reserve`.`reserve_id`,`fname`,`lname`,`tozih`,`m_hotel`,`regdat`,`room_id`,`aztarikh`,`tatarikh`,`user_id` from `hotel_reserve` left join `room_det` on (`hotel_reserve`.`reserve_id`=`room_det`.`reserve_id`) where `hotel_reserve`.`reserve_id` > 0 and `ajans_id` = $ajans_id and $feild",$q);
			while($r = mysql_fetch_array($q))
			{
				$tmp = array();
                                for($i = 0;$i <  mysql_num_fields($q);$i++)
                                {
	                                $fi = mysql_fetch_field($q,$i);
        	                        $tmp[$fi->name] = $r[$fi->name];
                                }
				$rtmp = new room_class((int)$tmp['room_id']);
				$rtmp = new hotel_class($rtmp->hotel_id);
				$tmp['hotel'] = $rtmp->name;
				$rtmp = new user_class((int)$tmp['user_id']);
				$tmp['user'] = $rtmp->fname . " " . $rtmp->lname;
                                $out[] = $tmp;
			}
			if($out == null)
				$out = FALSE;
			else
	                        $out = serialize($out);
                }
                return($out);
        }
	function idToName($ws_user,$ws_pass,$tbname,$id)
        {
		$out = null;
                $conf = new conf;
                if($conf->checkWsdl($ws_user,$ws_pass))
                {
			$out = hesab_class::idToName($tbname,$id);
		}
		return($out);
	}
	$server = new soap_server();
	$server->configureWSDL('test_wsdl', 'urn:test_wsdl');
	$server->soap_defencoding = 'UTF-8';
        $server->register('tedadOk',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','rooms'=>'xsd:string','tedad'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#tedadOk',
            'rpc',
            'encoded',
            'Returns TRUE if tedad is ok in a list of rooms with sum of capacity as room_zar.');
        $server->register('kill_reserve',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','reserve_id'=>'xsd:int'),array('return'=>'xsd:boolean'),
            'test_wsdl',
            'test_wsdl#kill_reserve',
            'rpc',
            'encoded',
            'Kills a reserve if exist and returns TRUE , else returns FALSE');
        $server->register('search',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','aztarikh'=>'xsd:string','shab'=>'xsd:int','tedad'=>'xsd:int','aj_id'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#search',
            'rpc',
            'encoded',
            'search open rooms . . .');
        $server->register('idToName',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','tbname'=>'xsd:string','id'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#idToName',
            'rpc',
            'encoded',
            'hesab_class::idToName');
        $server->register('load_reserve',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','reserve_id'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#load_reserve',
            'rpc',
            'encoded',
            'Load Reserve Object . . .');
	$server->register('load_reserves',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','tarikh'=>'xsd:string','ajans_id'=>'xsd:int','key'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#load_reserves',
            'rpc',
            'encoded',
            'Load All Reserves  . . .');
        $server->register('register_reserve',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','ajans_id'=>'xsd:string','aztarikh'=>'xsd:string','shab'=>'xsd:int','room_id'=>'xsd:string','tedad'=>'xsd:int','khadamat'=>'xsd:string','fname'=>'xsd:string','lname'=>'xsd:string','tel'=>'xsd:string','user_id'=>'xsd:int','extra_toz'=>'xsd:string','comision'=>'xsd:string','aj_id'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#register_reserve',
            'rpc',
            'encoded',
            'Register new room . . .');
	$server->register('getGhimat',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','aztarikh'=>'xsd:string','shab'=>'xsd:int','room_id'=>'xsd:string','tedad'=>'xsd:int','khadamat'=>'xsd:string','aj_id'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#getGhimat',
            'rpc',
            'encoded',
            'Get mony amouint for Registering new room . . .');
	$server->register('reserve_timeout',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string'),array('return'=>'xsd:boolean'),
            'test_wsdl',
            'test_wsdl#reserve_timeout',
            'rpc',
            'encoded',
            'Removes Timeout Reserves . . .');
        $server->register('ajans_login',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','aj_user'=>'xsd:string','aj_pass'=>'xsd:string'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#ajans_login',
            'rpc',
            'encoded',
            'Authenticating ajans users. Returns ID , FNAME , LNAME , TYP of the user .');
//sanad_gozaresh($ws_user,$ws_pass,$ajans_id,$aztarikh,$tatarikh,$frase)
	$server->register('sanad_gozaresh',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','ajans_id'=>'xsd:int','aztarikh'=>'xsd:string','tatarikh'=>'xsd:string','frase'=>'xsd:string'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#sanad_gozaresh',
            'rpc',
            'encoded',
            'Returns Sanad Gozaresh Query');
        $server->register('hotel_data',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','ht_id'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#hotel_data',
            'rpc',
            'encoded',
            'Loaids Hotel Data.');
        $server->register('object_data',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','class_name'=>'xsd:string','id'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#object_data',
            'rpc',
            'encoded',
            'Loaids An class name Object`s Data.');
        $server->register('room_typ_data',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','rt_id'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#room_typ_data',
            'rpc',
            'encoded',
            'Loads Room Type Data.');
        $server->register('ajans_data',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','aj_id'=>'xsd:int'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#ajans_data',
            'rpc',
            'encoded',
            'Loading ajans DATA. Returns NAME , SAGHFE_KHARID , POORSANT of the ajans .');
        $server->register('reserve_tmpList',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string'),array('return'=>'xsd:string'),
            'test_wsdl',
            'test_wsdl#reserve_tmpList',
            'rpc',
            'encoded',
            'Lists Temp Reserves . . .');
        $server->register('reserve_verify',array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','reserve_id'=>'xsd:int'),array('return'=>'xsd:boolean'),
            'test_wsdl',
            'test_wsdl#reserve_verify',
            'rpc',
            'encoded',
            'Verify Temp Reserves . . .');
	$request = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service($request);
?>
