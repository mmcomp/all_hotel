<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
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
                                        $fields[substr($key,4)] =perToEnNums($value);
                                }
                        }
                }
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
                $query.="insert into `cost_kala` $fi values $valu";
                mysql_class::ex_sqlx($query);
		
        }
	function delete_item($id)
	{
		mysql_class::ex_sql("select `id` from `cost_det` where `cost_kala_id` = $id",$q);
		if(!($r = mysql_fetch_array($q)))
		{
			mysql_class::ex-sqlx("delete from `cost_kala` where `id`=$id");
		}
		else
			$GLOBALS['msg'] = 'امکان حذف به علت وجود جزئیات محصول ، نمی‌باشد.';
	}
	function loadJoziat($id)
	{
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('cost_det.php?cost_kala_id=$id&','',500,600);\" >جزئیات</span></u>";
		return($out);
	}
	$GLOBALS['msg'] = '';
	$grid = new jshowGrid_new("cost_kala","grid1");
	$grid->whereClause="1=1 order by `name`";
	$grid->columnHeaders[0] = null;
       	$grid->columnHeaders[1] = 'نام محصول' ;
	$grid->columnHeaders[2] = 'توضیحات';
	$grid->addFeild('id');
	$grid->columnHeaders[3] = 'جزئیات';
	$grid->columnFunctions[3] = 'loadJoziat';
	$grid->columnAccesses[3] = 0;
        $grid->intial();
   	$grid->executeQuery();
	$grid->addFunction = 'add_item';
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
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<?php echo $GLOBALS['msg'].'<br/>'.$out; ?>
		</div>
	</body>
</html>
