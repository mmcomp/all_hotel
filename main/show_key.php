<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadKey($inp)
	{
		return moshtari_class::generateKey($inp);
	}
	function hamed_pdateBack($inp)
        {
                $out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
                        $y=(int)$tmp[2];
                        $m=(int)$tmp[1];
                        $d=(int)$tmp[0];
                        if ($d>$y)
                        {
                                $tmp=$y;
                                $y=$d;
                                $d=$tmp;
                        }
                        if ($y<1000)
                        {
                                $y=$y+1300;
                        }
                        $inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out." 12:00:00";
        }
        function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function change_enToPer($inp)
        {
		$out = "";
		$out = audit_class::enToPer($inp);	
                return $out;
	}
        function add_item($id)
        {
		$conf = new conf;
                $fields = null;
                foreach($_REQUEST as $key => $value)
                        if(substr($key,0,4)=="new_")
                                if($key != "new_id")
                                        $fields[substr($key,4)] =perToEnNums($value);
/*
		$query = jshowGrid_new::createAddQuery($fields);
		$query = 'insert into `moshtari` ('.$query['fi'].') values ('.$query['valu'].')';
                mysql_class::ex_sqlx($query);
*/
		moshtari_class::createMoshtari($fields['name'],$conf->user,$conf->pass,$fields['mob']);
        }
	function loadConf($id)
        {
		$out = "<button class=\"inp\" onclick=\"window.open('loadConf.php?moshtari_id=$id&r='+Math.random()+'&');\">اصلاح</button>";
		return($out);
	}
	function loadQuery($id)
	{
		$out = "<button class=\"inp\" onclick=\"window.open('ex_sqlx.php?moshtari_id=$id&r='+Math.random()+'&');\">اجرا </button>";
                return($out);
	}
	function loadUser($id)
        {
                $out = "<button class=\"inp\" onclick=\"window.open('loadUser.php?moshtari_id=$id&r='+Math.random()+'&');\">مشاهده</button>";
                return($out);
        }
	$grid = new jshowGrid_new("moshtari","grid1");
	$grid->columnHeaders[0] = 'کلید';
	$grid->columnFunctions[0] = 'loadKey';
	$grid->sortEnabled = TRUE;
	$grid->columnAccesses[0] = 0;
/*	$grid->columnAccesses[3] = 0;
	$grid->columnAccesses[4] = 0;*/
	$grid->addFeild('id');
	$grid->addFeild('id');
	$grid->columnHeaders[1] = 'نام';
	$grid->columnHeaders[2] = 'شماره همراه';
	$grid->columnHeaders[3] = 'تاریخ عقد قرارداد';
	$grid->columnFunctions[3]="hamed_pdate";
        $grid->columnCallBackFunctions[3]="hamed_pdateBack";
	$grid->columnHeaders[4] = 'تعداد پرداختی';
	$grid->columnFunctions[4]="change_enToPer";
	$grid->columnHeaders[5] = 'مبلغ پایه';
	$grid->columnFunctions[5]="change_enToPer";
	$grid->columnHeaders[7] = 'تنظیمات';
	$grid->columnFunctions[7] = 'loadConf';
	$grid->columnAccesses[7] = 0;
	$grid->columnHeaders[8] = 'اجرای query';
        $grid->columnFunctions[8] = 'loadQuery';
        $grid->columnAccesses[8] = 0;
	$grid->canDelete= FAlSE;
/*
	$grid->canEdit= FAlSE;
	$grid->canAdd= FAlSE;
*/
	$grid->addFunction = 'add_item';
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

		</title>
<style type="text/css" media="screen">
.square {
    width: 144px;
    height: 144px;
    background: #f0f;
    margin-right: 48px;
    float: left;
}

.transformed {
    -webkit-transform: rotate(90deg) scale(1, 1);
    -moz-transform: rotate(90deg) scale(1, 1);
    -ms-transform: rotate(90deg) scale(1, 1);
    transform: rotate(90deg) scale(1, 1);
}
</style>

	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>

</html>
