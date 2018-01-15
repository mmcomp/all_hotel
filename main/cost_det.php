<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadKala()
        {
		$out = null;
		mysql_class::ex_sql('select `name`,`id` from `kala` order by `name`',$q);
		while($r = mysql_fetch_array($q))
			$out[$r['name']] = (int)$r['id'];
		return($out);
        }
	if(!isset($_REQUEST['cost_kala_id']))
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
                                        $fields[substr($key,4)] = perToEnNums($value);
                                }
                        }
                }
		$fields['cost_kala_id'] = (int)$_REQUEST['cost_kala_id'];
                $query = '';
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
                $query.="insert into `cost_det` $fi values $valu";
                mysql_class::ex_sqlx($query);

        }
	$cost_kala_id = (int)$_REQUEST['cost_kala_id'];
	$grid = new jshowGrid_new("cost_det","grid1");
	$grid->whereClause=" `cost_kala_id` = $cost_kala_id order by `kala_id`";
	$grid->columnHeaders[0] = null;
       	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = 'کالا';
	$grid->columnHeaders[3] = 'تعداد';
	$grid->columnLists[2] = loadKala();
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
