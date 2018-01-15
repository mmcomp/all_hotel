<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
	$user = new user_class((int)$_SESSION['user_id']);
	$isAdmin = ($user->user=='mehrdad')?TRUE:FALSE;
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	
	$room_id = isset($_REQUEST['room_id']) ?(int)$_REQUEST['room_id']:-1;
	
	function loadKeys($fkey)
	{
		$out = '<select name="fkey" id="fkey" class="inp" onchange="frm_submit();" >';
		mysql_class::ex_sql("select `id`,`fkey` from `statics` group by `fkey`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = ($fkey==$r['fkey'])?'selected="selected"':'';
			$out .="<option $sel value='".$r['fkey']."' >".$r['fkey']."</option>\n";
		}
		$out .='</select>';
		return $out;
	}
	function listOtagh()
	{
		$out = array();
		$reserve_id = hexdec($_REQUEST['reserve_id'])-10000;
		mysql_class::ex_sql("select `room_id` from `room_det` where `reserve_id`=$reserve_id",$q);
		while($r = mysql_fetch_array($q))
	        {
			$room_id = $r["room_id"];
			mysql_class::ex_sql("select `name` from `room` where `id`=$room_id",$qq);
			while($row = mysql_fetch_array($qq))
			{
				$name_room = $row['name'];
				$out[$name_room]= $room_id;
			}
		}
		return $out;
	}
	function loadGender()
	{
		$tmp = statics_class::loadByKey('جنسیت');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadMellait()
	{
		$tmp = statics_class::loadByKey('ملیت');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadMakan()
	{
		$tmp = statics_class::loadByKey('شهر');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadNesbat()
	{
		$tmp = statics_class::loadByKey('نسبت');
		$out['سرگروه'] = '-1';
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function hpdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function hpdateback($inp)
	{
		return(audit_class::hamed_pdateBack(perToEnNums($inp)));
	}	
	function add_item()
	{
		$user = new user_class((int)$_SESSION['user_id']);
		$isAdmin = ($user->user=='mehrdad')?TRUE:FALSE;
		$fields = jshowGrid_new::loadNewFeilds($_REQUEST);
		$fields['reserve_id'] = hexdec($_REQUEST['reserve_id'])-10000;
		if((int)$fields['room_id']>0)
		{
			$reserve_id = $fields['reserve_id'];
			mysql_class::ex_sql("select `id`,`room_id`,`tatarikh` from `room_det` where `reserve_id`=$reserve_id and `room_id`=".(int)$fields['room_id']." order by `tatarikh` desc",$q);
		        while($r = mysql_fetch_array($q))
			        mysql_class::ex_sqlx("update `room` set `vaziat` = 0 where `id` = ".(int)$r['room_id']);
			unset($fields['id']);
			foreach($fields as $ss=>$value)
				if($value=='')
					unset($fields[$ss]);
			if(isset($fields['tt']))
				$fields['tt'] = hpdateback($fields['tt']);
			if(isset($fields['hazine']))
				$fields['hazine'] = umonize($fields['hazine']);
			if(isset($fields['hazine_extra']))
				$fields['hazine_extra'] = umonize($fields['hazine_extra']);
			$qu = jshowGrid_new::createAddQuery($fields);
			mysql_class::ex_sqlx("insert into `mehman` ".$qu['fi']." values ".$qu['valu']);
		}
	}
	function edit_item($id,$field,$value)
	{
		if($field=='hazine' || $field=='hazine_extra')
			$value = umonize($value);
		if($field=='tt')
			$value = hpdateback($value);
		mysql_class::ex_sqlx("update `mehman` set $field='$value' where `id`=$id ");
	}	
	if(isset($_REQUEST['reserve_id']))
	{		
		$reserve_id = hexdec($_REQUEST['reserve_id'])-10000;
		if (isset($room_id))
			$shart = "`room_id`='$room_id' and `reserve_id`='$reserve_id'";
		else
			$shart = "`reserve_id`='$reserve_id'";
		$khorooj= isset($_REQUEST['kh'])?(int)$_REQUEST['kh']:0;
		if($khorooj==1)
		{
			mehman_class::khorooj($reserve_id,$room_id);
			$out = "<h2>خروج مهمان با موفقیت انجام شد</h2>";
		}
		else
		{
			$q = null;
			$now = date("Y-m-d 23:59:59");
			$now_delay =date("Y-m-d 00:00:00",strtotime($now.' -'.$conf->limit_paziresh_day.' day'));
			$is_available = FALSE;
			mysql_class::ex_sql("select `id` from `room_det` where `reserve_id`=$reserve_id and `aztarikh`>='$now_delay' and `aztarikh`<='$now' ",$q);
			if($r = mysql_fetch_array($q,MYSQL_ASSOC))
				$is_available = TRUE;
			$grid = new jshowGrid_new("mehman","grid1");
			$grid->width = '99%';
			$grid->index_width = '20px';
			$grid->showAddDefault = FALSE;
			$grid->whereClause = $shart;
			$grid->columnHeaders[0] = null;			
			$grid->columnHeaders[1] = "شماره اتاق";
			$grid->columnLists[1] = listOtagh();
			$grid->columnHeaders[2] = null;
			$grid->columnHeaders[3] = 'نام';
			$grid->columnHeaders[4] = 'نام  خانوادگی';
		       	$grid->columnHeaders[5] ='ساعت  ورود' ;
			$grid->columnHeaders[6] = 'نام  پدر';
			$grid->columnHeaders[7] = 'شماره  شناسنامه';
			$grid->columnHeaders[8] = 'تاریخ  تولد';
			$grid->columnFunctions[8] = "hpdate";
			$grid->columnCallBackFunctions[8] = "hpdateback";
			$grid->columnHeaders[9] = 'جنسیت';
			$grid->columnLists[9]=loadGender();
			$grid->columnHeaders[10] = 'ملیت';
			$grid->columnLists[10]=loadMellait();
			$grid->columnHeaders[11] = 'محل‌صدور  شناسنامه';
			$grid->columnLists[11]=loadMakan();
			$grid->columnHeaders[12] = 'شغل';
			$grid->columnHeaders[13] = 'دلیل  سفر';
			$grid->columnHeaders[14] = 'مبدأ';
			$grid->columnLists[14]=loadMakan();
			$grid->columnHeaders[15] = 'مقصد';
			$grid->columnLists[15]=loadMakan();
			$grid->columnHeaders[16] = 'کد‌ملی';
			$grid->columnHeaders[17] = 'نسبت';
			$grid->columnLists[17]=loadNesbat();
			$grid->columnHeaders[18] = 'موبایل';
			$grid->columnHeaders[19] = 'نام تور';
			$grid->columnHeaders[20] = 'پیش پرداخت';
			$grid->columnHeaders[21] = 'توضیحات';
			$grid->columnHeaders[22] = 'هزینه';
			$grid->columnJavaScript[22] ='onkeyup="monize(this);"';
			$grid->columnHeaders[23] = 'هزینه اضافی';
			$grid->columnJavaScript[23] ='onkeyup="monize(this);"';
			$grid->columnHeaders[24] = 'نفراضافه';
			$grid->columnHeaders[25] = null;			
			//$grid->sortEnabled = TRUE;
			$grid->hideIndex = 10;
			$b = !(reserve_class::isKhorooj($reserve_id,$room_id) && !$se->detailAuth('all')) && ($is_available || $se->detailAuth('all'));
			$grid->canEdit = $b;
			$grid->canAdd = $b;
			$grid->canDelete = $b;
			$grid->addFunction = 'add_item';
			$grid->editFunction = 'edit_item';
			$grid->intial();
		   	$grid->executeQuery();
			$out = $grid->getGrid();
		}
	}
	else
		$out ='خطا در اطلاعات';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		تعریف ها
		</title>
		<script langauge="javascript">
		function frm_submit()
		{
			document.getElementById('frm1').submit();
		}
		</script> 

	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" class="general_div" >
			<?php
				echo $out;
			?>
		</div>
	</body>

</html>
