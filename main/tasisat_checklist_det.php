<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$is_admin = FALSE;
	if($se->detailAuth('write'))
		$is_admin = TRUE;
	function ppdate($inp)
	{
		return($inp!='0000-00-00 00:00:00'?audit_class::hamed_pdate($inp):'----');
	}
	function loadChecklistTemp($checkListTemp_id)
	{
		$checkListTemp_id = (int)$checkListTemp_id;
		$t = new tasisat_checklist_template_class($checkListTemp_id);
		if($t->id > 0)
			$out = $t->name;
		else
			$out = '----';
		return($out);
	}
	function loadUser($user_id)
	{
		$out = "";
		mysql_class::ex_sql("select `fname` , `lname` from `user` where `id`='$user_id'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return $out;
	}
	function addDet()
	{
		$tarikh = date("Y-m-d H:i:s");
		$user_id =(int)$_SESSION['user_id'];
		$id = -1;
		mysql_class::ex_sql("select id from `tasisat_checklist` where date(tarikh) = '".date('Y-m-d')."' and user_id = $user_id",$q);
		if($r = mysql_fetch_array($q))
			$id = (int)$r['id'];
		if($id <= 0)
		{
			$ln = mysql_class::ex_sqlx("insert into tasisat_checklist (user_id,tarikh) values ($user_id,'$tarikh')",FALSE);
			$id = mysql_insert_id($ln);
			mysql_close($ln);
			mysql_class::ex_sql("select id from `tasisat_checklist_template` order by id",$q);
			while($r = mysql_fetch_array($q))
				mysql_class::ex_sqlx("insert into tasisat_checklist_det (tasisat_checklist_id,tasisat_temp_id) values ($id,".$r['id'].")");
		}
		return($id);
	}
	$id = -1;
	$is_edit = (isset($_REQUEST['id']) && (int)$_REQUEST['id']>0);
	if(!$is_edit && $is_admin)
		$id = addDet();
	else if($is_edit)
		$id = (int)$_REQUEST['id'];
	$grid = new jshowGrid_new("tasisat_checklist_det","grid1");
	$grid->whereClause = " tasisat_checklist_id = $id ";
	$grid->setERequest(array("id"=>$id));
	//$grid->echoQuery = TRUE;
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = "مورد";
	$grid->columnFunctions[2] = 'loadChecklistTemp';
	$grid->columnAccesses[2] = FALSE;
	$grid->columnHeaders[3] = "بررسی";
	$grid->columnLists[3] = array(
					"نشده"=>0,
					"شده"=>1
				);
	$grid->columnHeaders[4] = "توضیحات";
	$grid->canDelete = FALSE;
	$grid->canAdd = FALSE;
	$grid->canEdit = $is_admin;
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
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه نرم افزاری رزرو آنلاین بهار
		</title>
		<script>
		function st()
		{
		week= new Array("يكشنبه","دوشنبه","سه شنبه","چهارشنبه","پنج شنبه","جمعه","شنبه")
		months = new Array("فروردين","ارديبهشت","خرداد","تير","مرداد","شهريور","مهر","آبان","آذر","دي","بهمن","اسفند");
		a = new Date();
		d= a.getDay();
		day= a.getDate();
		var h=a.getHours();
      		var m=a.getMinutes();
  		var s=a.getSeconds();
		month = a.getMonth()+1;
		year= a.getYear();
		year = (year== 0)?2000:year;
		(year<1000)? (year += 1900):true;
		year -= ( (month < 3) || ((month == 3) && (day < 21)) )? 622:621;
		switch (month) 
		{
			case 1: (day<21)? (month=10, day+=10):(month=11, day-=20); break;
			case 2: (day<20)? (month=11, day+=11):(month=12, day-=19); break;
			case 3: (day<21)? (month=12, day+=9):(month=1, day-=20); break;
			case 4: (day<21)? (month=1, day+=11):(month=2, day-=20); break;
			case 5:
			case 6: (day<22)? (month-=3, day+=10):(month-=2, day-=21); break;
			case 7:
			case 8:
			case 9: (day<23)? (month-=3, day+=9):(month-=2, day-=22); break;
			case 10:(day<23)? (month=7, day+=8):(month=8, day-=22); break;
			case 11:
			case 12:(day<22)? (month-=3, day+=9):(month-=2, day-=21); break;
			default: break;
		}
		//document.write(" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s);
			var total=" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s;
			    document.getElementById("tim").innerHTML=total;
   			    setTimeout('st()',500);
		}
		</script>
	</head>
	<body onload='st()'>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<?php echo $out;  ?>
		</div>
		<script language="javascript">
			document.getElementById('new_id').style.display = 'none';
		</script>
	</body>
	<center>
		<span id='tim' >test2
		</span>
		</center>
</html>

