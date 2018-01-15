<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadMoeens($moeen_id)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `moeen` order by `name`",$q);
		while($r = mysql_fetch_array($q))
			$out .= "<option value='".$r['id']."' ".(((int)$r['id']==(int)$moeen_id)?'selected="selected"':'').">".$r['name']."</option>";
		return($out);
	}
	function loadFactorKhadamat()
	{
		$out = null;
		mysql_class::ex_sql("select `id`,`name` from `factor_khadamat` order by `name`",$q);
		while($r = mysql_fetch_array($q))
			$out[(int)$r['id']] = $r['name'];
		return($out);
	}
	function add_item()
	{
		$anbar_factor_id = ((isset($_REQUEST['anbar_factor_id']))?(int)$_REQUEST['anbar_factor_id']:-1);
		if($anbar_factor_id > 0 && $GLOBALS['is_edit'])
		{
			$feilds = jshowGrid_new::loadNewFeilds($_REQUEST);
			unset($feilds['id']);
			unset($feilds['en']);
			$feilds['anbar_factor_id'] = $anbar_factor_id;
			$qr = jshowGrid_new::createAddQuery($feilds);
			mysql_class::ex_sqlx(" insert into `factor_khadamat_det` ".$qr['fi']." values ".$qr['valu']);
		}
	}
	$is_edit = ((isset($_REQUEST['mod']) && $_REQUEST['mod']=='sabt')?FALSE:TRUE);
	$anbar_factor_id = ((isset($_REQUEST['anbar_factor_id']))?(int)$_REQUEST['anbar_factor_id']:-1);
	$toz = ((isset($_REQUEST['toz']))?$_REQUEST['toz']:'');
	$moeen_id = ((isset($_REQUEST['moeen_id']))?(int)$_REQUEST['moeen_id']:'-1');
	$name = '';
	$moeen = new moeen_class($moeen_id);
	if($moeen->id > 0)
		$name = $moeen->name;
	$tarikh = date("Y-m-d H:i:s");
	$user_id = (int)$_SESSION['user_id'];
	if($anbar_factor_id <= 0)
	{
		$ln = mysql_class::ex_sqlx("insert into `anbar_factor` (`factor_id`, `name`, `tozihat`, `moeen_id`, `tarikh_resid`, `anbar_typ_id`, `user_id`) values ('فاکتور دستی خروجی','$name','$toz','$moeen_id','$tarikh',4,$user_id) ",FALSE);
		$anbar_factor_id = mysql_insert_id($ln);
		mysql_close($ln);
	}
	else if(isset($_REQUEST['toz']))
		mysql_class::ex_sqlx("update `anbar_factor` set `tozihat` = '$toz' , `moeen_id` = $moeen_id , `name` = '$name' where `id` = $anbar_factor_id");
	if(!$is_edit)
	{
		mysql_class::ex_sqlx("update `factor_khadamat_det` set `en` = 1 where `anbar_factor_id` = $anbar_factor_id");
		mysql_class::ex_sqlx("update `anbar_factor` set `tozihat` = '$toz' , `moeen_id` = $moeen_id , `name` = '$name' where `id` = $anbar_factor_id");
		sanadzan_class::factorSabt($anbar_factor_id,$moeen_id,$user_id);
	}
	$q = null;
	mysql_class::ex_sql("select `en` from `factor_khadamat_det` where `anbar_factor_id` = $anbar_factor_id limit 1",$q);
	if($r = mysql_fetch_array($q))
		if((int)$r['en'] == 1)
			$is_edit = FALSE;
	$GLOBALS['is_edit'] = $is_edit;
	$grid = new jshowGrid_new("factor_khadamat_det","grid1");
	$grid->setERequest(array('anbar_factor_id'=>$anbar_factor_id,'moeen_id'=>$moeen_id,'toz'=>$toz));
	$grid->whereClause = " `anbar_factor_id` = $anbar_factor_id";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] =null ;
        $grid->columnHeaders[2] = "خدمات";
	$grid->columnLists[2] = loadFactorKhadamat();
	$grid->columnHeaders[3] = "تعداد";
	$grid->columnHeaders[4] = "قیمت(ریال)";
	$grid->columnHeaders[5] = "توضیحات";
	$grid->columnHeaders[6] =null ;
	$grid->showAddDefault = FALSE;
	$grid->addFunction = 'add_item';
	$grid->canAdd = $is_edit;
	$grid->canEdit = $is_edit;
	$grid->canDelete = $is_edit;
	$grid->list2 = TRUE;
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
		<script language="javascript">
			function sel()
			{
				document.getElementById('mod').value = 'edit';
				document.getElementById('frm1').submit();
			}
			function sabt()
			{
				document.getElementById('mod').value = 'sabt';
				document.getElementById('frm1').submit();
			}
		</script>

	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<form id="frm1" method="post">
				مشتری :
				<select class="inp" <?php echo (($is_edit)?"":'disabled="disabled"'); ?> id="moeen_id" name="moeen_id" >
					<?php
						echo loadMoeens($moeen_id);
					?>
				</select>
				توضیحات : 
				<input class="inp" type="text" <?php echo (($is_edit)?"":'readonly="readonly"'); ?>  id="toz" name="toz" value = "<?php echo $toz; ?>" />
				شماره فاکتور : 
				<input class="inp" type="text" readonly="readonly" id="anbar_factor_id" name="anbar_factor_id" value="<?php echo $anbar_factor_id; ?>" />
				<input type="hidden" id="mod" name="mod" value="<?php echo (($is_edit)?'edit':'sabt'); ?>" />
				<?php
					if($is_edit)
						echo '<input class="inp" type="button" onclick="sel();" value = "انتخاب" /><input class="inp" type="button" onclick="sabt();" value = "ثبت نهایی" />';
				?>
				
			</form>
			<br/>
			فاکتور شماره 
			<?php	echo "$anbar_factor_id <br/> $out";?>
		</div>
	</body>

</html>
