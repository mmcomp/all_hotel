<?php
//	include_once("../kernel.php");
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        $GLOBALS['msg'] = '';
	$user = new user_class((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);

        function add_item()
        {
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
		$daftar = new daftar_class($fields['daftar_id']);
		$kol_tmp = new kol_class($daftar->kol_id);
		if($kol_tmp->id>0)
		{
			$moeen_id = moeen_class::addById($daftar->kol_id,$fields['name']);
			$fields['moeen_id'] = $moeen_id;
		}
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
                $query="insert into `ajans` $fi values $valu";
                mysql_class::ex_sqlx($query);
        }


	function loadDaftar()
	{
		$out=null;
		mysql_class::ex_sql("select `name`,`id` from `daftar` order by `id`",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$out[$r["name"]]=(int)$r["id"];
		}
		return $out;
	}
	function loadMoeen($inp)
	{
		$inp = (int)$inp;
		$aj = new ajans_class($inp);
		if($aj->moeen_id>0)
		{
			$moeen = new moeen_class($aj->moeen_id);
			$nama = $moeen->name.'('.$moeen->code.')';
		}
		else
		{
			$nama = 'انتخاب';
		}
		
		$out = "<u><span onclick=\"window.location =('select_hesab.php?refPage=ajans.php&sel_id=$inp');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
		return $out;
	}
	function del_item($id)
	{
		$ag = new ajans_class((int)$id);
		if($ag->protected == 1)
			$GLOBALS['msg'] = 'امکان حذف این آژانس نمی باشد';
		else
		{
			mysql_class::ex_sqlx("update `moeen` set `name` ='".$ag->name."_پاک‌شده_$id' where `id`=".$ag->moeen_id);
			mysql_class::ex_sqlx("delete from `ajans` where `id`=$id ");
		}
	}
	if(isset($_REQUEST['sel_id']))
	{
		$moeen_id = (int)$_REQUEST['moeen_id'];
		$sel_id = $_REQUEST['sel_id'];
		mysql_class::ex_sqlx("update `ajans` set `moeen_id`=$moeen_id where `id`=$sel_id");
	}
	$ersal ["شود"]= 0;
	$ersal ["نشود"]= 1;
	$grid = new jshowGrid_new("ajans","grid1");
	$grid->whereClause="1=1 order by `daftar_id`,`name`";
	$grid->showAddDefault = FALSE;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="نام دفتر";
	$grid->columnFilters[1] = TRUE;
	if($conf->is_hesabdari !== '')
		$grid->columnHeaders[2]="نام مشتری";
	else
		$grid->columnHeaders[2]="نام آژانس";
	$grid->columnFilters[2] = TRUE;
	$grid->columnHeaders[3]="توضیحات";
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[5] = null;
	if($conf->sms)
        	$grid->columnHeaders[5] = "ارسال پیام کوتاه به مشتری";
	if($conf->is_hesabdari !== '')
		$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6]="شماره همراه";
	$grid->columnHeaders[7] = null;
	if($conf->ajans_saghf_mande)
		$grid->columnHeaders[7] = "سقف خرید";
	if($conf->is_hesabdari !== '')
		$grid->columnHeaders[7] = null;
	$grid->columnJavaScript[7] = 'onkeyup="monize(this);"';
	$grid->columnCallBackFunctions[7] = "umonize";
	$grid->columnHeaders[8] = null;
	if($conf->ajans_saghf_mande)
		$grid->columnHeaders[8] = 'کمیسیون (درصد)';
	if($conf->is_hesabdari !== '')
		$grid->columnHeaders[8] = null;
	$grid->columnLists[5]=$ersal;
	$grid->columnHeaders[9]=null;
	if($user->user='mehrdad')
		$grid->columnHeaders[9]='protected';
	$grid->addFeild('id');
	$grid->columnHeaders[10] = 'حساب معین';
	$grid->columnLists[1]=loadDaftar();
	$grid->columnFunctions[10]='loadMoeen';
//	$grid->columnHeaders[1] = "نام مشتری";
//	$grid->deleteFunction = "delete_item";
//	$grid->pageCount = 500;
	$grid->addFunction = 'add_item';
	$grid->deleteFunction = 'del_item';
	$grid->canDelete = TRUE;
	$grid->intial();
	$grid->executeQuery();
	$grid->canAdd = FALSE;
	if($grid->getRowCount()<$conf->limit_ajans)
		$grid->canAdd = TRUE;
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
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
<?php
	if($conf->is_hesabdari !== '')
		echo "مشتری";
	else
		echo "آژانس";
?>
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
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
				<h2 style='color:red' ><?php echo $GLOBALS['msg']; ?></h2>
			<br/>
			<?php echo $out;  ?>
		</div>
		<script language="javascript">
                        var ids = document.getElementsByName("new_id");
			for(var i=0;i<ids.length;i++)
				ids[i].style.display="none";
		</script>
	</body>
	<center>
		<span id='tim' >test2
		</span>
		</center>
</html>
