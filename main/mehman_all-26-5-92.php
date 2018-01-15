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
	function loadVorood($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`reserve_id`,`aztarikh` from `room_det` where `reserve_id`=$inp ",$q);
                if($r = mysql_fetch_array($q))
	               $out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r["aztarikh"])));
		return $out;
	}
	function loadKhorooj($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`reserve_id`,`tatarikh` from `room_det` where `reserve_id`=$inp ",$q);
                if($r = mysql_fetch_array($q))
	               $out = audit_class::hamed_pdate(date("Y-m-d",strtotime($r["tatarikh"])));
		return $out;
	}
	function hpdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function hpdate1($inp)
	{
		$out = '';
		if ($inp != "0000-00-00 00:00:00")
			$out = audit_class::hamed_pdate($inp);
		else
			$out = 'میهمان خارج نشده است';
		return($out);
	}
	function hpdate_ez($inp)
	{
		$out = '';
		if ($inp != "0000-00-00")
			$out = audit_class::hamed_pdate($inp);
		else
			$out = '----';
		return($out);
	}
	function loadMobile($inp)
	{
		$out = '';
		if ($inp == '0000-00-00')
			$out = '---';
		else
			$out = $inp;
		return($out);
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
	$room_id = (isset($_REQUEST['room_id']))?$_REQUEST['room_id']:-1;
	$combo = "";
	$combo .= "<form name=\"selRoom\" id=\"selRoom\" method=\"GET\">";
	$combo .= "<select class='inp' id=\"room_id\" name=\"room_id\" onchange=\"document.getElementById('selRoom').submit();\" style=\"width:auto;\">\n";
	$combo .= "<option selected='selected' value=\"-1\">\n";
        $combo .= "همه"."\n";
        $combo .= "</option>\n";
	mysql_class::ex_sql("select * from room where `en`=1 order by name",$q);
        while($r = mysql_fetch_array($q))
        {
		if((int)$r["id"]== (int)$room_id)
                        $select = 'selected="selected"';
                else
                        $select = "";
                $combo .= "<option value=\"".(int)$r["id"]."\" $select   >".$r["name"];
                $combo .= "</option>\n";
        }
        $combo .="</select>";
	$combo .= "</form>";	
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['aztarikh']," 00:00:00"):'0000-00-00');
	$tatarikh = ((isset($_REQUEST['tatarikh']) && $_REQUEST['tatarikh']!='' )?audit_class::hamed_pdateBack($_REQUEST['tatarikh'],"23:59:59"):'0000-00-00');
	$az = strtotime($aztarikh);
	$ta = strtotime($tatarikh);
	$isAdmin = $se->detailAuth('all');
	$is_admin =$isAdmin;
	$user_id=-1;
	$tmp = '';
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$tmp = room_det_class::loadReserve_id_habibi($aztarikh,$tatarikh,$room_id);
//var_dump ($tmp);
	if(($tmp!='') && ($room_id!=-1))
		$shart = " `reserve_id` in ($tmp) and `room_id`='$room_id' order by `room_id`";
	elseif(($tmp!='') && ($room_id==-1))
		$shart = " `reserve_id` in ($tmp) order by `room_id`";
	else
		$shart = "0=1 order by `room_id`";
	$out = '';
	$msg = 'میهمانی یافت نشد';
//echo $shart;	
//	$shart = '1=1 order by `reserve_id` ASC';
//	if(count($reserves)>0)
//	{
		$msg = '';
		$GLOBALS['msg'] = '';
		$user = new user_class((int)$_SESSION['user_id']);
		$grid = new jshowGrid_new("mehman","grid1");
		$grid->index_width = '20px';
		$grid->width = '100%';
		$grid->showAddDefault = FALSE;
		$grid->whereClause=$shart;
		$grid->columnHeaders[0] = null;			
		$grid->columnHeaders[1] = "شماره اتاق";
		$grid->columnFunctions[1] = "listOtagh";
		//$grid->columnFilters[1] = TRUE;
		//$grid->columnLists[1] = listOtagh();
		$grid->columnHeaders[2] = 'شماره رزرو';
		$grid->columnFilters[2] = TRUE;
		$grid->columnHeaders[3] = 'نام';
		$grid->columnFilters[3] = TRUE;
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
		$grid->columnHeaders[18] = 'تاریخ ازدواج';
		$grid->columnFunctions[18] = "hpdate_ez";
		$grid->columnHeaders[19] = 'موبایل';
		$grid->columnHeaders[20] = 'نام تور';
		$grid->columnHeaders[21] = 'پیش پرداخت';
		$grid->columnHeaders[22] = 'توضیحات';
		$grid->columnHeaders[23] = 'هزینه';
		$grid->columnJavaScript[23] ='onkeyup="monize(this);"';
		$grid->columnHeaders[24] = 'هزینه اضافی';
		$grid->columnJavaScript[24] ='onkeyup="monize(this);"';
		$grid->columnHeaders[25] = 'نفراضافه';
		$grid->columnHeaders[26] = 'خروج';
		$grid->columnFunctions[26] = 'hpdate1';
		$grid->addFeild('reserve_id',26);
		$grid->columnHeaders[26] = 'تاریخ ورود';
		$grid->columnFunctions[26] = 'loadVorood';
		$grid->addFeild('reserve_id',27);
		$grid->columnHeaders[27] = 'تاریخ خروج';
		$grid->columnFunctions[27] = 'loadKhorooj';
		$grid->pageCount = 500;	
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
		$grid->intial();
		$grid->executeQuery();
		$out = $grid->getGrid();
//	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
		<link type="text/css" href="../css/style.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>	
		<title>
لیست میهمانان
		</title>
		<script type="text/javascript">
		function send_search()
		{
			if( trim(document.getElementById('room_id').value)=='' &&  trim(document.getElementById('datepicker6').value)=='' &&  trim(document.getElementById('datepicker7').value)=='')
			{
				alert('لطفا یکی از موارد را وارد کنید');
			}
			else
			{
				document.getElementById('mod').value= 2;
				document.getElementById('frm1').submit();
			}
		}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#datepicker6").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#datepicker7").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
	    	</script>
		<style>
			td{text-align:center;}
		</style>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;display:none;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<br/>
			<form id='frm1'  method='GET' >
			<table border='1' style='font-size:12px;' >
				<tr>
					<th>شماره اتاق</th>
					<th>از تاریخ </th>
					<th>تا تاریخ</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					<td>	
						<?php echo $combo;?>
					</td>	
					<td>	
         					   <input style='width:100px;' value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh'  class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td>
						<input style='width:100px;' value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh'  class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
			</form>
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
