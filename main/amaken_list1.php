<?php 
        session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function hamed_pdateBack($inp)
	{
		$out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
                        $y=(int)$tmp[2];
                        $m=(int)$tmp[1];
                        $d=(int)$tmp[0];
                        if ($d>$y)
                        {
                                $tmp=$y;
                                $y=$d;
                                $d=$tmp;
                        }
                        if ($y<1000)
                        {
                                $y=$y+1300;
                        }
                        $inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out." 12:00:00";
	}
	function createFile($filename, $mod, $encode = ''){
   		$f = fopen($filename, $mod);
		if ($encode == 'utf8') {
		        fwrite($f, pack("CCC",0xef,0xbb,0xbf));
		}
		return $f;
	}
	function putText($resource, $content, $encode = '') {
	    if ($encode == 'utf8') { 
        	$content = utf8_encode($content);
	    }
	    fputs($resource,$content);
	}

	function readExistingfile($file, $encode = '') {
	    $content = @file_get_contents($file);
	    return $encode == 'utf8' ? utf8_decode($content) : $content;
	}
	function loadName($id)
	{
		$out = '';
		if ($id=='-1')
			$out = 'سرگروه';
		else
		{
			mysql_class::ex_sql("select `id`,`fvalue` from `statics` where `id` ='$id' order by `fvalue` ",$q);
			if($r = mysql_fetch_array($q))
				$out = $r['fvalue'];
		}
		return $out;
	}
	function downloadFile( $fullPath ) {
	    if( headers_sent() ) {
 	      die('Headers Sent'); 
  	  }
  	  if( file_exists($fullPath) ) {
	        $fsize = filesize($fullPath);
        	$path_parts = pathinfo($fullPath);
	        $ext = strtolower($path_parts["extension"]);
        	switch ($ext) {
	            case "pdf": $ctype="application/pdf"; break;
        	    case "exe": $ctype="application/octet-stream"; break;
	            case "zip": $ctype="application/zip"; break;
        	    case "doc": $ctype="application/msword"; break;
	            case "xls": $ctype="application/vnd.ms-excel"; break;
        	    case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
	            case "gif": $ctype="image/gif"; break;
        	    case "png": $ctype="image/png"; break;
	            case "jpeg":
        	    case "jpg": $ctype="image/jpg"; break;
	            default: $ctype="application/force-download";
        	}
        	header("Pragma: public");
	        header("Expires: 0");
        	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	        header("Cache-Control: private",false); // required for certain browsers
        	header("Content-Type: $ctype");
	        header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
        	header("Content-Transfer-Encoding: binary");
	        header("Content-Length: ".$fsize);
        	ob_clean();
	        flush();
        	readfile( $fullPath );
	        exit;
	    } else {
        	die('File Not Found');
	    }
	} 

	$out = "";
	$day = date("Y-m-d");
	$i = 1;
	$aztarikh = $day;
	$tatarikh = $day;
	$q = null;
	mysql_class::ex_sql("select `reserve_id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) < '$aztarikh' and date(`tatarikh`) > '$aztarikh') or (date(`aztarikh`) < '$tatarikh' and date(`tatarikh`) > '$tatarikh') or (date(`aztarikh`) >= '$aztarikh' and date(`tatarikh`) <= '$tatarikh'))",$q);
	while ($r = mysql_fetch_array($q,MYSQL_ASSOC))
	{
		$sh_reserve = (int)$r["reserve_id"];
		$mehmans = mehman_class::loadByReserveId($sh_reserve);
		foreach($mehmans as $mehman)
		{
			$t_tavalod = perToEnNums(audit_class::hamed_pdate($mehman->tt));
			$gender = loadName($mehman->gender);
			$m_tavalod = loadName($mehman->ms);
			$melliat = loadName($mehman->melliat);
			$hamrah_tedad = count($mehmans)-1;
			$nesbat = loadName($mehman->nesbat);
			$mabda = loadName($mehman->mabda);
			$maghsad =loadName($mehman->maghsad);
			$t_vorood = room_det_class::loadByReserve($sh_reserve);
			$t_vorood = $t_vorood[0];
			$room = new room_class($t_vorood[0]->room_id);
			$room = $room->name;
			$t_khorooj = perToEnNums(audit_class::hamed_pdate($mehman->khorooj));
			$t_vorood = perToEnNums(audit_class::hamed_pdate($t_vorood[0]->aztarikh));
			$h_khorooj = date("H:i",strtotime($mehman->khorooj));
		        $out .= str_pad($mehman->fname,50," ");
		        $out .= str_pad($mehman->lname,50," ");
			$out .= str_pad($mehman->p_name,50," ");
			$out .= str_pad($mehman->ss,12," "); //شماره شناسنامه یا پاسپورت
			$out .= str_pad($t_tavalod,12," "); //تاریخ تولد
			$out .= str_pad($gender,3," ");//
			$out .= str_pad(' '.$m_tavalod,30," "); //
			$out .= str_pad($melliat,30," "); //
			$out .= str_pad($mehman->job,20," "); //
			$out .= str_pad($mehman->safar_dalil,30," ") ; //
			$out .= str_pad($hamrah_tedad,4," "); //تعداد همراهان
			$out .= str_pad($nesbat,20," ") ;//
			$out .= str_pad($mabda,30," ") ;//
			$out .= str_pad($maghsad,30," "); //
			$out .= str_pad($t_vorood,12," "); //تارخ ورود
			$out .= str_pad($t_khorooj,12," "); //تاریخ خروج
			$out .= str_pad($mehman->toz,500," "); //
			$out .= str_pad($mehman->vorood_h,5," "); //زمان ورود
			$out .= str_pad($h_khorooj,5," "); //زمان خروج
			$out .= str_pad($mehman->toor_name,100," ");
			$out .= str_pad($room,50," "); //شماره اتاق
			$out .= str_pad($room,16," "); //شماره میهمان در هتل
			$out .= str_pad($mehman->code_melli,10," "); //کد ملی
			$out .= str_pad($sh_reserve,16," "); //شماره پذیرش
			$out .= str_pad($sh_reserve,10," "); //شماره رجیستر
			$out .= ("\n");
			$i++;
		}	
	}
	$fil = fopen('../amaken/police.txt','w+');
	fwrite($fil,$out);
	fclose($fil);
	downloadFile('../amaken/police.txt');
?>

