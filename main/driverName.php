<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function add_item()
        {
                $fields = null;
		$moeen_id = -1;
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
		$name = $fields["name"];
		mysql_class::ex_sql("select `id`,`name` from `kol` where `code`='6' order by `name` ",$q);
		if($r = mysql_fetch_array($q))
		{
			$kol_id=$r['id'];
			$moeen_id = moeen_class::addById($kol_id,$name);
		}
		$fields["moeen_driver"] = $moeen_id;
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
	        $query="insert into `driver` $fi values $valu";
	        mysql_class::ex_sqlx($query);
		
	}
	$grid = new jshowGrid_new("driver","grid1");
	$grid->width = '85%';
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= 'نام راننده';
	$grid->columnHeaders[2]= null;
	$grid->addFunction = 'add_item';
	$grid->canAdd = TRUE;
	$grid->canDelete = TRUE;
	$grid->canEdit = TRUE;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
	
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
	    	</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
ثبت نام راننده
		</title>
	</head>
	<body>
		<br/>
		<br/>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" id="div_main" >
			<br/>
			<br/>				
			<?php echo $out;  ?>
			<br/>
		</div>
	</body>
</html>
