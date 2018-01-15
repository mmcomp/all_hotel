<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadDate($inp)
	{
		return($inp != '0000-00-00 00:00:00' ? audit_class::hamed_pdate($inp) : '');
	}
	function loadUser($id)
	{
		$user = new user_class((int)$id);
		$out = $user->fname.' '.$user->lname;
		return($out);
	}
	function loadStat($stat)
	{
		$out = 'نامعلوم';
		switch($stat)
		{
			case -2:
				$out = 'در دست بررسی';
				break;
			case -1:
				$out = 'پاسخ داده شده';
				break;
			case 0:
                                $out = 'تأیید شده';
                                break;
			case 1:
                                $out = 'تأیید نشده';
                                break;
		}
		return($out);
	}
	function loadSubmit($user_id)
	{
		$out['تأیید نشده'] = -1;
		$out['تأیید شده'] = $user_id;
		return($out);
	}
	function loadAdvStat()
	{
		$out['در دست بررسی'] = -2;
		$out['پاسخ داده شده'] = -1;
		$out['تأیید شده'] = 0;
		$out['تأیید نشده'] = 1;
		return($out);
	}
        function add_item()
        {
                $fields = null;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id" && $key != "new_stat" && $key != "new_from_user" && $key != "new_submitter_user" && $key != "new_tarikh_su" && $key != "new_answer" && $key != "new_tarikh_an")
                                {
                                        $fields[substr($key,4)] = $value;
                                }
                        }
                }
		$fields['tarikh'] = date("Y-m-d H:i:s");
		$fields['from_user'] = (int)$_SESSION['user_id'];
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
                $query="insert into `ticket` $fi values $valu";
                mysql_class::ex_sqlx($query);
        }
	function edit_item($id,$feild,$value)
	{
		if($feild == 'answer')
		{
			mysql_class::ex_sqlx("update `ticket` set `tarikh_an` = '".date("Y-m-d H:i:s")."',`stat`= -1 where `id` = $id");
			mysql_class::ex_sqlx("update `ticket` set `$feild` = '$value' where `id` = $id");
		}
		else if($feild == 'submitter_user')
		{
			if((int)$value > 0)
			{
				mysql_class::ex_sqlx("update `ticket` set `tarikh_su` = '".date("Y-m-d H:i:s")."',`stat`= 0 where `id` = $id");
				mysql_class::ex_sqlx("update `ticket` set `$feild` = '$value' where `id` = $id");
			}
		}
		else
			mysql_class::ex_sqlx("update `ticket` set `$feild` = '$value' where `id` = $id");
	
	}
	$user_id = $_SESSION['user_id'];
	$user = new user_class((int)$user_id);
	if($user->user == 'mehrdad')
		$se_all = TRUE;
	else
		$se_all = $se->detailAuth('all');
	$grid = new jshowGrid_new("ticket","grid1");
	if($se->detailAuth('submit') || $se_all || $se->detailAuth('view'))
		$grid->whereClause="1 = 1 order by `tarikh` desc";
	else
		$grid->whereClause="`from_user` = $user_id order by `tarikh` desc";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = "موضوع";
	$grid->columnHeaders[2] = "متن";
	$grid->columnHeaders[3] = "تاریخ ثبت";
	$grid->columnHeaders[4] = "کاربر ثبت کننده";
	$grid->columnHeaders[5] = "کاربر تأیید کننده";
	$grid->columnHeaders[6] = 'تاریخ تأیید';
	$grid->columnHeaders[7] = "پاسخ";
	$grid->columnHeaders[8] = "تاریخ پاسخ";
	$grid->columnHeaders[9] = "وضعیت";
	$grid->columnFunctions[3] = "loadDate";
	$grid->columnFunctions[4] = "loadUser";
	$grid->columnFunctions[5] = "loadUser";
	$grid->columnFunctions[6] = "loadDate";
	$grid->columnFunctions[8] = "loadDate";
	$grid->columnFunctions[9] = "loadStat";
	$grid->canDelete = FALSE;
	for($i = 0;$i < count($grid->columnHeaders);$i++)
		$grid->columnAccesses[$i] = 0;

	if($se_all)
	{
		$grid->columnAccesses[7] = 1;
		$grid->columnAccesses[9] = 1;
		$grid->columnFunctions[9] = null;
		$grid->columnLists[9] = loadAdvStat();
		$grid->canAdd = FALSE;
	}
	else if($se->detailAuth('submit'))
	{
		$grid->columnLists[5] = loadSubmit($user_id);
		$grid->columnHeaders[5] = "تأیید";
		$grid->columnAccesses[5] = 1;
		$grid->canDelete = TRUE;
	}
	$grid->addFunction = 'add_item';
	$grid->editFunction = 'edit_item';
	$grid->width = "100%";
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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سیستم ثبت انتقاد و پیشنهاد
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
		<center>
		<span id='tim' >test2
		</span>
		</center>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<p style="width : 90%;color:red;font-family:b titr;font-size:15px;align:justify;">
			<u>قابل توجه همکاران عزیز :</u> ثبت انتقاد و پیشنهاد در این قسمت جهت سامانه بوده لذا لطفا انتقادات خارج از بحث نرم‌افزار را در این بخش ثبت نفرمایید. لازم به ذکر است که ثبت انتقاد به منزله اجرایی بودن آن نبوده و از تاریخ تأیید مدیریت محترم دوستان در شرکت گستره ارتباطات اقدام به بررسی مورد کرده و درصورت توافق و عملی بودن آن را اجرا خواهند نمود.
			</p>
			<p dir="ltr" style="width : 90%;color:red;font-family:b titr;font-size:15px;">
			باتشکر
			</br/>
			میرسمیع
			</p>
			<br/>
			<?php	echo $out;?>
		</div>
		<script language="javascript">
			document.getElementById('new_from_user').style.display = 'none';
			document.getElementById('new_tarikh').style.display = 'none';
			document.getElementById('new_submitter_user').style.display = 'none';
			document.getElementById('new_tarikh_su').style.display = 'none';
			document.getElementById('new_tarikh_an').style.display = 'none';
			document.getElementById('new_answer').style.display = 'none';
			document.getElementById('new_stat').style.display = 'none';

		</script>
	</body>
</html>
