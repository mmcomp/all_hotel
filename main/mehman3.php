<?php
//	include_once("../kernel.php");
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
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
	function listOtagh($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `room` where `en` = 1 and `id`='$inp'",$qq);
		while($row = mysql_fetch_array($qq))
			$out = $row['name'];
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
		$reserve_id = $fields['reserve_id'];
		mysql_class::ex_sql("select `id`,`room_id`,`tatarikh` from `room_det` where `reserve_id`=$reserve_id order by `tatarikh` desc",$q);
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
		//echo "insert into `mehman` ".$qu['fi']." values ".$qu['valu'];
	}
	function edit_item($id,$field,$value)
	{
		if($field=='hazine' || $field=='hazine_extra')
			$value = umonize($value);
		if($field=='tt')
			$value = hpdateback($value);
		mysql_class::ex_sqlx("update `mehman` set $field='$value' where `id`=$id ");
	}
	$tarikh = date("Y-m-d");
	$reserves = array();
	mysql_class::ex_sql("select `reserve_id` from `room_det` where date(`aztarikh`) <= '$tarikh' and date(`tatarikh`) > '$tarikh'",$q);
	while($r = mysql_fetch_array($q))
		$reserves[] = (int)$r['reserve_id'];
	$out = '';
	$msg = 'lیهمانی یافت نشد';
	$day = date("Y-m-d");
	$i = 1;
	$aztarikh = $day;
	$tatarikh = $day;
	$q = null;
	mysql_class::ex_sql("select `reserve_id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) <= '$aztarikh' and date(`tatarikh`) > '$aztarikh') or (date(`aztarikh`) < '$tatarikh' and date(`tatarikh`) > '$tatarikh')) group by `reserve_id`",$q);

	$tmp ='';
	while ($r = mysql_fetch_array($q))
		$tmp .=($tmp==''? '':',' ).$r['reserve_id'];
	//echo $tmp;
//echo "select `reserve_id` from `room_det` where `reserve_id`>0 and ((date(`aztarikh`) <= '$aztarikh' and date(`tatarikh`) > '$aztarikh') or (date(`aztarikh`) < '$tatarikh' and date(`tatarikh`) > '$tatarikh')) group by `reserve_id`";
	if($tmp!='')
		$shart = " `reserve_id` in ($tmp) and `khorooj`='0000-00-00 00:00:00'";
	else
		$shart = "";
	if(count($reserves)>0)
	{
		$msg = '';
		$GLOBALS['msg'] = '';
		$user = new user_class((int)$_SESSION['user_id']);
		$grid = new jshowGrid_new("mehman","grid1");
		$grid->index_width = '20px';
		$grid->width = '95%';
		$grid->showAddDefault = FALSE;
		$grid->whereClause=$shart.' order by `room_id`';
		$grid->columnHeaders[0] = null;			
		$grid->columnHeaders[1] = "شماره اتاق";
		$grid->columnFunctions[1] = "listOtagh";
		//$grid->columnLists[1] = listOtagh();
		$grid->columnHeaders[2] = null;
		$grid->columnHeaders[3] = 'نام';
		$grid->columnHeaders[4] = 'نام  خانوادگی';
		$grid->columnFilters[4] = TRUE;
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
		$grid->pageCount = 500;	
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
		$grid->intial();
		$grid->executeQuery();
		$out = $grid->getGrid();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
لیست میهمانان
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;display:none;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<?php echo $msg.'<br/>'.$GLOBALS['msg']; ?>
			<br/>
			<?php echo $out;  ?>
			<br/>
			<a style="background-color:#f1ca00;-webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; border:1px solid black;padding:5px;" target='_blank' href='amaken_list.php'>لیست اماکن</a>
		</div>
		<script language="javascript" >
/*
			<?php if($conf->hesab_auto){ ?>
			if(document.getElementById('new_kol_id'))
				document.getElementById('new_kol_id').style.display = 'none';
			<?php } ?>
			if(document.getElementById('new_css_class'))
				document.getElementById('new_css_class').style.fontFamily = 'tahoma';
			var inp = document.getElementsByName('new_id');
			for(var i=0;i<inp.length;i++)
				inp[i].style.display = 'none';
*/
		</script>
	</body>
</html>
