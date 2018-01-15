<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$is_admin = FALSE;
	if($se->detailAuth('all'))
		$is_admin = TRUE;
	$grid = new jshowGrid_new("tasisat_checklist_template","grid1");
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->whereClause = " `en` = 1 ";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]= "چک لیست";
	$grid->columnHeaders[2] = null;
/*
	$grid->columnHeaders[3]="نام";
	$grid->columnFilters[3] = TRUE;
	$grid->columnHeaders[4]="نام خانوادگی";
	$grid->columnFilters[4] = TRUE;
	$grid->columnHeaders[5]="نام کاربری";
	$grid->columnFilters[5] = TRUE;
	$grid->columnAccesses[5] = 1;
	$grid->columnHeaders[6]="رمز عبور";
	if(!$is_admin)
	{
		$grid->columnFunctions[6] = "hidePass";
		$grid->columnCallBackFunctions[6] = "hidePassAll";
	}
	$grid->columnHeaders[7]="گروه کاربری";
	$grid->columnFilters[7] = TRUE;
	$grid->columnLists[7]=$groups;
	$grid->columnHeaders[8]="شماره کارت";
	$grid->columnHeaders[9]="ساعت موظف ورود";
	$grid->columnHeaders[10]="ساعت موظف خروج";
	$grid->columnHeaders[11]="ساعت موظف ورود<br/>شیفت دو";
	$grid->columnHeaders[12]="ساعت موظف خروج<br/>شیفت دو";
	$grid->columnHeaders[13]=null;
	$grid->columnHeaders[14]= "زمان حضور";
	$grid->columnLists[1]=loadDaftar();
	$grid->columnLists[2] = loadAjans();
	$grid->editFunction = 'edit_item';
	for($i = 0;$i < count($grid->columnHeaders);$i++)
		$grid->columnAccesses[$i] = 0;
	$grid->columnAccesses[6] = 1;
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	if($is_admin)
	{
		for($i = 1;$i < count($grid->columnHeaders);$i++)
        	        $grid->columnAccesses[$i] = 1;
	        $grid->canAdd = TRUE;
		$grid->addFunction = 'add_item';
	}
	else if($se->detailAuth('middle_manager'))
	{	
		$grid->columnFunctions[6] = 'hidePass_all';
		$grid->canAdd = TRUE;
		$grid->addFunction = 'add_item';
		//$grid->columnFunctions[6] = 'hidePass';
	}
	else
	{
		$grid->columnFunctions[6] = 'hidePass_all';
		$grid->canAdd = FALSE;
	}
*/
	$grid->intial();
	$grid->executeQuery();
	if($grid->canAdd)
	{
		$grid->canAdd = FALSE;
		if($grid->getRowCount()<$conf->limit_kol_user)
			$grid->canAdd = TRUE;
	}
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

