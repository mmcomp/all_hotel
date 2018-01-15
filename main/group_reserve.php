<?php
	session_start();
	include_once("../kernel.php");
	function register_reserve($res_arr)
        {
		$ajans_id = (int)trim($res_arr[0]);
		$aztarikh = audit_class::hamed_pdateBack(trim(audit_class::perToEn($res_arr[1])));
		$shab = (int)trim($res_arr[2]);
		$room_id = trim($res_arr[3]);
		$tedad = (int)trim($res_arr[4]);
		$fname = trim($res_arr[5]);
		$lname = trim($res_arr[6]);
		$tel = trim($res_arr[7]);
		$ghimat = (int)trim($res_arr[8]);
                $out = null;
                $conf = new conf;
                $t = explode(',',$room_id);
                $room_id = (count($t)>1)?$t:$room_id;
		if(is_array($room_id))
                        $room = new room_class($room_id[0]);
		else			
			$room = new room_class($room_id);
		$aj = new ajans_class($ajans_id);
		if($ghimat <= $aj->saghf_kharid || $aj->saghf_kharid < 0 || $conf->ajans_saghf_mande)
		{
			$sanad_recs = room_det_class::preReserve($room->hotel_id,$ajans_id,(is_array($room_id))?$room_id:array($room_id),$ghimat,$aztarikh,$shab,1,FALSE,FALSE,$tedad,NULL,-2);
			if($sanad_recs !== FALSE)
			{
				$toz["toz"] = $tel;
				$toz["extra_toz"] = 'ثبت گروهی';
				room_det_class::sabtReserveHotel($sanad_recs['reserve_id'],$sanad_recs['shomare_sanad'],null,'',$fname.' '.$lname,$toz,$ajans_id,$ghimat,'');
				$sanad_recs['reserve_timeout'] = 0;
				$sanad_recs['query'] = $aj->decSaghf($ghimat);
				$sanad_recs['ghimat'] = $ghimat;
			}
                       	$out = $sanad_recs;
		}
		else
		{
			$out = FALSE;
		}
                return($out);
        }
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$sq = new mysql_class;
	$db = $sq->db;
	$out = "";
	if(isset($_FILES['uploadedfile']))
	{
		$target_path = "download/restore/";
		$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
			//$out = shell_exec("mv ../download/restore/".basename( $_FILES['uploadedfile']['name'])." ../download/restore/res.sql.gz;gunzip ../download/restore/res.sql.gz;mysql -u root tavanir -p'123456'<../download/restore/res.sql;");
			//$out = shell_exec("mysql -u root $db -p'123456' < download/restore/".basename( $_FILES['uploadedfile']['name']).";");
			$reserves = file($target_path);
			$oks = array();
			foreach($reserves as $i => $reserve)
			{
				$reserve_arr = explode('|',$reserve);
				if(count($reserve_arr) == 9)
				{
					$res_out = register_reserve($reserve_arr);
					if($res_out === FALSE && $res_out != NULL)
						$oks[] = $i;
				}
			}
			$oks_str = implode(',',$oks);
			$out = "<script> alert('بروزرسانی رزرو گروهی جهت ردیفهای $oks_str با موفقیت انجام گرفت');window.close(); </script>";
		} else{
			$out =  "در بروزرسانی فایل مشکلی پیش آمد ، لطفاًً مجدداً ارسال نمایید .";
		}
	}
?>
<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link type="text/css" href="../css/style.css" rel="stylesheet" />      
</head>
<body
	<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
        <center>
		<br/><br/><br/>
		<form enctype="multipart/form-data" method="POST">
			<input  type="hidden" name="MAX_FILE_SIZE" value="999999999" />
			فایل رزرو گروهی را انتخاب نمایید : <input name="uploadedfile" class="inp" type="file" /><br />
			<input class="inp" type="submit" value="بروزرسانی" />
		</form>
		<?php echo $out; ?>
        </center>
</body>
</html>
