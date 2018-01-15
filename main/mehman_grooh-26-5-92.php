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
	$msg = 'میهمانی برای این پذیرش ثبت نگردیده است';
	$reserve_id = (isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:'';
	$name_mehman = '';
	$tedad = '';
	mysql_class::ex_sql("select count(`id`) as `tedad` from `room_det` where `reserve_id`='$reserve_id'",$qu);
	if($row = mysql_fetch_array($qu))
		$tedad = $row['tedad'];
	mysql_class::ex_sql("select `fname`,`lname` from `hotel_reserve` where `reserve_id`='$reserve_id'",$qu);
	if($row = mysql_fetch_array($qu))
		$name_mehman = $row['fname'].$row['lname'];	
	$msg = '';
	$GLOBALS['msg'] = '';
	$user = new user_class((int)$_SESSION['user_id']);
	$grid = new jshowGrid_new("mehman","grid1");
	$grid->index_width = '20px';
	$grid->width = '95%';
	$grid->showAddDefault = FALSE;
	$grid->whereClause="`reserve_id`='$reserve_id' order by `room_id`";
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
	$grid->columnHeaders[10] = null;
	//$grid->columnLists[10]=loadMellait();
	$grid->columnHeaders[11] = null;
	//$grid->columnLists[11]=loadMakan();
	$grid->columnHeaders[12] = null;
	$grid->columnHeaders[13] = null;
	$grid->columnHeaders[14] = null;
	//$grid->columnLists[14]=loadMakan();
	$grid->columnHeaders[15] = null;
	//$grid->columnLists[15]=loadMakan();
	$grid->columnHeaders[16] = null;
	$grid->columnHeaders[17] = null;
	//$grid->columnLists[17]=loadNesbat();
	$grid->columnHeaders[18] = null;
	$grid->columnHeaders[19] = null;
	$grid->columnHeaders[20] = null;
	$grid->columnHeaders[21] = null;
	$grid->columnHeaders[22] = null;
	//$grid->columnJavaScript[22] ='onkeyup="monize(this);"';
	$grid->columnHeaders[23] = null;
	//$grid->columnJavaScript[23] ='onkeyup="monize(this);"';
	$grid->columnHeaders[24] = null;
	$grid->columnHeaders[25] = null;	
	$grid->pageCount = 500;	
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
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
			<form id='frm1'  method='GET' >
			<table border='1' style='font-size:12px;' >
				<tr>
					<th>شماره رزرو</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					<td>	
						<input class='inp' style='width:50px;' name='reserve_id' id='reserve_id' value="<?php echo ((isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:''); ?>" >
					</td>					
					<td>
						<input type='submit' value='جستجو' class='inp'>
					</td>					
				</tr>
			</table>
			</form>
			<br/>
			<?php echo $msg.'<br/>'.$GLOBALS['msg']; ?>
			<table style='font-size:12px;' >
				<tr valign="bottom" >
					<td colspan='5'>	
						سرگروه :(<?php echo $name_mehman; ?>)
					</td>					
					<td>
						تعداد اتاق :(<?php echo $tedad; ?>)
					</td>					
				</tr>
			</table>
			<?php echo $out;  ?>
			<br/>			
		</div>
	</body>
</html>
