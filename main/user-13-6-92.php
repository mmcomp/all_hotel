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
	$GLOBALS['msg'] = '';
	function loadDaftar()
	{
		$out=null;
		mysql_class::ex_sql("select `name`,`id` from `daftar` order by `name`",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$out[$r["name"]]=(int)$r["id"];
		}
		return $out;
	}
        function loadAjans()
        {
                $out['همه']=-1;
                mysql_class::ex_sql("select `name`,`id` from `ajans` order by `name`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $out[$r["name"]]=(int)$r["id"];
                }
                return $out;
        }
	function loadType()
	{
		$out=array();
		$out['مدیر']=0;
		$out['عادی']=1;
		return $out;
	}
        function loadGroups($se)
        {
                $out = null;
                mysql_class::ex_sql('select `name`,`id` from `grop` where `en`=1 order by `name`',$q);
                while($r = mysql_fetch_array($q))
			if($se->detailAuth('all') || $se->detailAuth($r['name']) || (int)$r['id'] == (int)$_SESSION['typ'])
	                        $out[$r['name']] = (int)$r['id'];
                return($out);
        }
        function add_item($f)
        {
		$conf = new conf;
                $fields = null;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id")
                                {
                                        $fields[substr($key,4)] = $value;
                                }
                        }
                }
		$canAdd = TRUE;
		if($fields['ajans_id']!=-1 && user_class::ajansUserCount()>=$conf->limit_ajans_user )
			$canAdd = FALSE;
		if($canAdd)
		{
		        $fi = "(";
		        $valu="(";
		        foreach ($fields as $field => $value)
		        {
		                $fi.="`$field`,";
		                $valu .="'$value',";
		        }
		        $fi=substr($fi,0,-1);
		        $valu=substr($valu,0,-1);
		        $fi.=")";
		        $valu.=")";
		        $query="insert into `user` $fi values $valu";
		        mysql_class::ex_sqlx($query);
		}
		else
			$GLOBALS['msg'] = 'با توجه به ظرفیت سرور امکان تعریف کاربر آژانسی بیش از تعداد موجود نمی باشد';
		/*
		var_dump($f);
		var_dump(jshowGrid_new::createAddQuery($f));
		*/
        }
	function edit_item($id,$feild,$value)
	{
		$conf = new conf;
		$canAdd = TRUE;
		if($feild=='ajans_id')
		{
			$pre_val = -1;
			mysql_class::ex_sql("select `ajans_id` from `user` where `id`=$id ",$q);
			if($r=mysql_fetch_array($q))
				$pre_val = $r['ajans_id'];
			if($pre_val==-1 && $value!=-1)
				if(user_class::ajansUserCount()>=$conf->limit_ajans_user )
					$canAdd = FALSE;
		}
		if($canAdd)
			mysql_class::ex_sqlx("update `user` set `$feild`='$value' where `id`=$id ");
		else
			$GLOBALS['msg'] = 'با توجه به ظرفیت سرور امکان تعریف کاربر آژانسی بیش از تعداد موجود نمی باشد';
	}
	function hidePass($inp)
	{
		if($inp != (int)$_SESSION['user_id'])
			$out = "*****";
		else
			$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('changepass.php','',200,200);\" >تغییر کلمه عبور</span></u>";
		return($out);
	}
	function loadHozoor($id)
	{
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('hozoor.php?p_id=$id&','',700,400);\" >$id</span></u>";
                return($out);
	}
	$wer = '';
	if(!$is_admin && !$se->detailAuth('middle_manager'))
	{
		$wer = ' and `id` = '.(int)$_SESSION['user_id'];
	}
	else if($se->detailAuth('middle_manager'))
	{
		$werr = '';
		for($i = 0;$i < count($se->allDetails);$i++)
			$werr .= (($werr != '')?' or ':' where ')." `name`='".$se->allDetails[$i]."' ";
		mysql_class::ex_sql("select `id`,`name` from `grop` $werr  order by `name`",$qq);
		while($rr = mysql_fetch_array($qq))
		{
			$wer .= (($wer != '')?' or ':' and (').' `typ` = '.$rr['id'];
		}
		if($wer == '')
			$wer = ' and `id` = '.(int)$_SESSION['user_id'];
		else
			$wer .= ' or `id` = '.(int)$_SESSION['user_id'].')';
	}
	$groups = loadGroups($se);
	$grid = new jshowGrid_new("user","grid1");
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->whereClause = " `user` != 'mehrdad' $wer";
	$grid->columnHeaders[0] ='کد پرسنلی';
	$grid->columnAccesses[0] = 0;
	$grid->columnHeaders[1]="دفتر";
	$grid->columnFilters[1] = TRUE;
	$grid->columnHeaders[2] = "آژانس";
	$grid->columnHeaders[3]="نام";
	$grid->columnFilters[3] = TRUE;
	$grid->columnHeaders[4]="نام خانوادگی";
	$grid->columnFilters[4] = TRUE;
	$grid->columnHeaders[5]="نام کاربری";
	$grid->columnFilters[5] = TRUE;
	$grid->columnAccesses[5] = 1;
	$grid->columnHeaders[6]="رمز عبور";
	$grid->columnHeaders[7]="گروه کاربری";
	//$grid->columnFilters[7] = TRUE;
	$grid->columnHeaders[8]="شماره کارت";
	$grid->columnHeaders[9]="ساعت موظف ورود";
	$grid->columnHeaders[10]="ساعت موظف خروج";
	$grid->columnHeaders[11]="ساعت موظف ورود<br/>شیفت دو";
	$grid->columnHeaders[12]="ساعت موظف خروج<br/>شیفت دو";
	$grid->columnHeaders[13]=null;
	$grid->columnLists[1]=loadDaftar();
	$grid->columnLists[2] = loadAjans();
	$grid->editFunction = 'edit_item';
	for($i = 0;$i < count($grid->columnHeaders);$i++)
		$grid->columnAccesses[$i] = 0;
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	if($is_admin && !$se->detailAuth('middle_manager'))
        {
	        for($i = 1;$i < count($grid->columnHeaders);$i++)
        	        $grid->columnAccesses[$i] = 1;
        	$grid->canAdd = TRUE;
	        $grid->canDelete = TRUE;
        	$grid->columnLists[7]=$groups;
		$grid->addFunction = 'add_item';
		$grid->columnFunctions[0] = 'loadHozoor';
	}
	else if(!$is_admin && !$se->detailAuth('middle_manager'))
	{
		$grid->columnAccesses[6] = 1;
		$grid->columnHeaders[7]=null;
	}
	else if($se->detailAuth('middle_manager') && !$is_admin)
	{
		for($i = 1;$i < count($grid->columnHeaders);$i++)
                        $grid->columnAccesses[$i] = 1;
                $grid->canAdd = TRUE;
                $grid->columnLists[7]=$groups;
                $grid->addFunction = 'add_item';
		$grid->fieldList[7] = 'id';
		$grid->columnFunctions[6] = 'hidePass';
		$grid->columnAccesses[6] = 0;
		$grid->columnFunctions[0] = 'loadHozoor';
	}
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
			<br/>
			<?php echo '<h2>'.$GLOBALS['msg'].'</h2>' ?>
			<br/>
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

