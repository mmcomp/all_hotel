<?php 
        session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function fixY($inp)
	{
		$tmp = str_replace('ی','ي',$inp);
		$tmp = str_replace('ک','ك',$tmp);
		return($tmp);
	}
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
	function roomName($id=-1)
	{
		mysql_class::ex_sql("select `name` from `room` where `id` = $id",$q);
		if($r = mysql_fetch_array($q))
			$out=$r['name'];
		else
			$out='نا معلوم';
		return($out);
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
		$out = fixY($out);
		return mb_convert_encoding($out, "UTF-8", "auto");
		//return iconv(mb_detect_encoding($out, mb_detect_order(), true), "UTF-8", $out);
	}
	function loadName1($id)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`fvalue` from `statics` where `id` ='$id' order by `fvalue` ",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fvalue'];
		return(fixY($out));
	}
	function loadByReserve($reserve_id = 0)
	{
		$reserve_id = (int)$reserve_id;
		$out = FALSE;
		$editable = TRUE;
		if($reserve_id != 0)
		{
			mysql_class::ex_sql("select `aztarikh` from `room_det` where `reserve_id` = $reserve_id group by `reserve_id`,`aztarikh` ",$q);
			if ($r=mysql_fetch_array($q))
				$out = $r['aztarikh'];
			
		}
		else
			$out = FALSE;
		return($out);
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
	$tarikh_mehman = '';
	$i = 1;
	$aztarikh = $day;
	$tatarikh = $day;
	$q = null;
	mysql_class::ex_sql("select `reserve_id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) <= '$aztarikh' and date(`tatarikh`) > '$aztarikh') or (date(`aztarikh`) < '$tatarikh' and date(`tatarikh`) > '$tatarikh')) group by `reserve_id`",$q);
	$tmp ='';
	while ($r = mysql_fetch_array($q))
		$tmp .=($tmp==''? '':',' ).$r['reserve_id'];
	//echo $tmp;
	$t_hamrah = 0;
	if($tmp!='')
	{
		mysql_class::ex_sql("select * from `mehman` where `reserve_id` in ($tmp) and `khorooj`='0000-00-00 00:00:00'",$qq);
//echo "select * from `mehman` where `reserve_id` in ($tmp) and `khorooj`='0000-00-00 00:00:00'";
		while($rr=mysql_fetch_array($qq))
		{
			$r_id = $rr["reserve_id"];
			mysql_class::ex_sql("select `nafar` from `room_det` where `reserve_id`='$r_id'",$qu);
			if($row=mysql_fetch_array($qu))
				$t_hamrah = $row['nafar']-1;
			$tarikh_mehman = room_det_class::loadByReserve_habibi($rr["reserve_id"],$rr["room_id"]);
			if ($tarikh_mehman!="")
			{
				$t_vorood = perToEnNums(audit_class::hamed_pdate($tarikh_mehman[0]));
				$t_khorooj = perToEnNums(audit_class::hamed_pdate($tarikh_mehman[1]));	
			}
			else
			{	
				$t_vorood = "0000-00-00";
				$t_khorooj = "0000-00-00";
			}
			foreach($rr as $key=>$value)
			{
				$rr[$key] = fixY($value);
			}
			$t_tavalod = perToEnNums(audit_class::hamed_pdate($rr["tt"]));
			$gender = loadName($rr["gender"]);
			$m_tavalod = loadName($rr["ms"]);
			$melliat = loadName($rr["melliat"]);
			$nesbat = loadName($rr["nesbat"]);
			$mabda = loadName1($rr["mabda"]);
			$maghsad =loadName1($rr["maghsad"]);
			$room = roomName($rr["room_id"]);
			$h_khorooj = date("H:i",strtotime($rr["khorooj"]));
			$h_vorood = date("H:i",strtotime($rr["vorood_h"]));
////////////////////////////////////
			$out .= "        ";
			$out .= str_pad(iconv('utf-8','windows-1256//TRANSLIT', $rr["fname"]),50," ");
			$out .= str_pad(iconv('utf-8', 'windows-1256//TRANSLIT', $rr["lname"]),50," ");
			$out .= str_pad(iconv('UTF-8', 'Windows-1256//TRANSLIT', $rr["p_name"]),50," ");
			$out .= str_pad($rr["ss"],12," "); 
			$out .= str_pad($t_tavalod,10," ");
			$out .= str_pad(iconv('utf-8', 'windows-1256', $gender),3," ");//
			$out .= str_pad(iconv('utf-8', 'windows-1256//TRANSLIT', $m_tavalod),30," "); //
			$out .= str_pad(iconv('UTF-8', 'Windows-1256//TRANSLIT', $melliat),30," "); //
			$out .= str_pad(iconv('utf-8', 'windows-1256',$rr["job"]),20," "); //
			$out .= str_pad(iconv('utf-8', 'windows-1256//TRANSLIT',$rr["safar_dalil"]),30," "); //
			$out .= str_pad($t_hamrah,4," "); //تعداد همراه
			$out .= str_pad(iconv('utf-8', 'windows-1256', $nesbat),20," ");//
			$out .= str_pad(iconv('utf-8', 'windows-1256//TRANSLIT',$mabda),30," ");//
			$out .= str_pad(iconv('utf-8', 'windows-1256',$maghsad),30," "); //
			$out .= str_pad($t_vorood,10," "); 
			$out .= str_pad($t_khorooj,10," "); 
			$out .= str_pad(iconv('utf-8', 'windows-1256',$rr["toz"]),500," "); //
			$out .= str_pad($h_vorood,5," "); 
			$out .= str_pad($h_khorooj,5," "); 
////////////////////////////
			$out .= str_pad(iconv('utf-8', 'windows-1256//TRANSLIT',$rr["toor_name"]),100," ");
			$out .= str_pad($room,50," "); 
			$out .= str_pad($room,16," "); 
			$out .= str_pad($rr["code_melli"],10," "); 
			$out .= str_pad($rr["reserve_id"],16," "); 
			$out .= str_pad($room,10," "); 
/////////////////////
			$out .= (chr(13).chr(10));

		}
	}
	$fil = fopen('../amaken/police.txt','w+');
	fwrite($fil,$out);
	fclose($fil);
	downloadFile('../amaken/police.txt');
	echo $out;
			
?>

