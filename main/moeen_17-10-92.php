<?php	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadKol()
        {
                $out=null;
                mysql_class::ex_sql("select * from kol order by name",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $out[$r["name"]." (".$r["code"].")"]=(int)$r["id"];
                }
                return $out;
        }
        function delete_item($id)
        {
                mysql_class::ex_sql("select `id` from `sanad` where `moeen_id` = $id",$q);
                if(!($r = mysql_fetch_array($q)))
                {
                        $q = null;
	                mysql_class::ex_sql("select `id` from `ajans` where `moeen_id` = $id",$q);
        	        if(!($r = mysql_fetch_array($q)))
			{
				$q = null;
				mysql_class::ex_sql("select `id` from `hotel` where `moeen_id` = $id",$q);
				if(!($r = mysql_fetch_array($q)))
					mysql_class::ex_sqlx("delete from `moeen` where `id` = $id");
				else
					$GLOBALS['msg'] = '<span style="color:red;">حساب معین متصل به یک هتل است.</span>';
			}
        	        else
                	        $GLOBALS['msg'] = '<span style="color:red;">حساب معین متصل به یک آژانس است.</span>';
		}
		else
			$GLOBALS['msg'] = '<span style="color:red;">حساب معین دارای سند است.</span>';
        }
        function edit_item($id,$feild,$value)
        {
                if(trim($value) != '')
                        mysql_class::ex_sqlx("update `moeen` set `$feild` = '$value' where `id` = $id");
                else
                        $GLOBALS['msg'] = '<span style="color:red;">مقدار خالی قابل قبول نیست . </span>';
        }
        function add_item()
        {
                $feilds = jshowGrid_new::loadNewFeilds($_REQUEST);
                unset($feilds['id']);
                if(trim($feilds['name']) != '' && trim($feilds['code']) != '' && trim($feilds['kol_id']) > 0)
                {
                        $r = jshowGrid_new::createAddQuery($feilds);
                        mysql_class::ex_sqlx('insert into `moeen` '.$r['fi'].' values '.$r['valu']);
                }
                else
                        $GLOBALS['msg'] = '<span style="color:red;">مقدار خالی قابل قبول نیست . </span>';
        }
	$GLOBALS['msg'] = '';
	$combo=array();
	$combo["بستانکار"]=1;
	$combo["بدهکار"]=-1;
	$combo["بستانکار/بدهکار"]=0 ;
	$grid = new jshowGrid_new("moeen","grid1");
	//$grid->whereClause=" order by `name`";
	$grid->columnHeaders[0] = null;
        $grid->columnHeaders[2] = "کد";
        $grid->columnHeaders[1] = "کد کل";
	$grid->columnFilters[1] = -1;
        $grid->columnHeaders[3] = "نام";
	$grid->columnFilters[3] = '';
        $grid->columnHeaders[4] = "نوع";
        $grid->columnLists[1]=loadKol();
	$grid->columnLists[4]=$combo;
	$grid->sortEnabled = TRUE;
	$grid->pageCount = 200;
	$grid->deleteFunction = 'delete_item';
        $grid->editFunction = 'edit_item';
        $grid->addFunction = 'add_item';	
        $grid->showAddDefault = FALSE;
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
		سامانه
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<br/>
			<?php echo $GLOBALS['msg'].'<br/>'.$out; ?>
		</div>
	</body>
</html>
