<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadTyp()
        {
		$out['پاسخ']=2;
                $out['پرسش']=1;
                return $out;
        }
	function add_item()
	{
		$user = new user_class((int)$_SESSION['user_id']);
		//$isAdmin = ($user->user=='mehrdad')?TRUE:FALSE;
		$fields = jshowGrid_new::loadNewFeilds($_REQUEST);
		$fields['user_id'] = $user->id;
		unset($fields['id']);
		if($fields['typ']==2 && $fields['reply_id']==0)
			$GLOBALS['msg'] = '<h2>مشخص کنید پاسخ به کدام پرسش می دهید</h2><br/>';
		else if($fields['typ']==0)
			$GLOBALS['msg'] = '<h2>نوع نظر خود را مشخص کنید</h2><br/>';
		else
		{
			$fields['tarikh'] = date("Y-m-d H:i:s");
			$fields['matn'] = str_replace("\n",'<br />',$fields['matn']);
			$qu = jshowGrid_new::createAddQuery($fields);
			mysql_class::ex_sqlx('insert into `enteghad_modir` '.$qu['fi'].' values '.$qu['valu']);
		}
	}
	function loadUser($inp)
	{
		$user = new user_class($inp);
		return $user->fname.' '.$user->lname;
	}
	function loadPasokh($inp)
	{
		if($inp==0)
			$out = '---';
		else
			$out = $inp;
		return $out;
	}
	function loadReply_id($inp)
	{
		$sel = new enteghad_modir_class($inp);
		$out = $inp;
		if($sel->typ==1)
			$out = "<span style=\"text-decoration: underline;cursor:pointer;color:blue;\" onclick=\"loadReply_id($inp);\">$inp</span>";
		return $out;
	}
	function loadTimep($inp)
	{
		if($inp=='0000-00-00 00:00:00')
			$out = 'نامشخص';
		else
			$out = jdate("H:i d / m / Y ",strtotime($inp));
		return $out;
	}
	$GLOBALS['msg'] = '';
	$combo["بستانکار"]=1;
	$combo["بدهکار"]=-1;
	$grid = new jshowGrid_new("enteghad_modir","grid1");
	$grid->index_width = '20px';
	$grid->width ='99%';
	$grid->columnHeaders[0] = 'شماره';
	$grid->columnFunctions[0] = 'loadReply_id';
        $grid->columnHeaders[1] = 'نوع';
	$grid->columnFilters[1] = TRUE;
       	$grid->columnHeaders[2] = 'شماره پرسش' ;
	$grid->columnFunctions[2] = 'loadPasokh';
	$grid->columnFilters[2] = TRUE;
	$grid->columnHeaders[3] = "موضوع";
	$grid->columnHeaders[4] = "متن";
	$grid->columnHeaders[5] = 'کاربر ثبت کننده';
	$grid->columnFunctions[5] = 'loadUser';
	$grid->columnHeaders[6] = 'تاریخ و ساعت';
	$grid->columnFunctions[6] = 'loadTimep';
	$grid->columnLists[1]=loadtyp();
	$grid->sortEnabled = TRUE;
	$grid->canDelete = FALSE;
	$grid->canEdit = FALSE;
	$grid->addFunction = 'add_item';
	$grid->gotoLast = TRUE;
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
		انتقاد و پیشنهاد
		</title>
		
		<script language="javascript" >
			function set_typ()
			{
				if(document.getElementById('new_typ').options[document.getElementById('new_typ').options.selectedIndex].value==2)
					document.getElementById('new_reply_id').style.display = '';
				if(document.getElementById('new_typ').options[document.getElementById('new_typ').options.selectedIndex].value==1)
				{
					document.getElementById('new_reply_id').style.display = 'none';
					document.getElementById('new_reply_id').value = 0;
				}
			}
			function loadReply_id(inp)
			{
				if(document.getElementById('new_typ').options[document.getElementById('new_typ').options.selectedIndex].value==2)
					document.getElementById('new_reply_id').value = inp;
			}
		</script>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
                <div align="center">
                        <br/>
                        <p style="width : 90%;color:red;font-family:b titr;font-size:15px;align:justify;">
                        <u>قابل توجه همکاران عزیز :</u> از این پس برای طرح سؤال از مسئولین محترم گروه هتل‌های گوهرشاد ، کافیست در این صفحه پرسش خود را ثبت نمایید و یا در صورت مشاهده پرسش مشابه ، پاسخی جهت پیگیری آن با انتخاب شماره پرسش ثبت نمایید.
<br/>
لازم به ذکر است که جهت مشاهده راحت تر پرسش ها و پاسخ ها ، امکان فیلتر کردن بر مبنای نوع (پرسش یا پاسخ) و یا شماره پرسش (کد پرسش) در اینجا می باشد.
                        </p>
                        <p dir="ltr" style="width : 90%;color:red;font-family:b titr;font-size:15px;">
                        باتشکر
                        </br/>
                        میرسمیع
                        </p>
                        <br/>
			<?php	echo $GLOBALS['msg'].'<br/>'.$out;?>
		</div>
		<script language="javascript" >
			document.getElementById('new_id').style.display = 'none';
			document.getElementById('new_user_id').style.display = 'none';
			document.getElementById('new_reply_id').style.display = 'none';
			document.getElementById('new_tarikh').style.display = 'none';
			document.getElementById('new_reply_id').readOnly = true;
			document.getElementById('new_typ').onchange = set_typ;
			document.getElementById('new_typ').style.fontFamily = 'tahoma';
			if(document.getElementById('grid1_filter_2').value=='---')
				document.getElementById('grid1_filter_2').value = '';
		</script>
	</body>

</html>
