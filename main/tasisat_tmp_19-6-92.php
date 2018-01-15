<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if (isset($_SESSION['user_id']))
		$user_id = $_SESSION['user_id'];
	else
		$user_id = -1;
	function ppdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function room_name()
	{
		$out = array();
		mysql_class::ex_sql("select `id` , `name` from `room`",$q);
		while($r = mysql_fetch_array($q))
			$out[$r['name']]=$r['id'];
		return($out);
	}
	function loadUser($user_id)
	{
		$out = "";
		mysql_class::ex_sql("select `fname` , `lname` from `user` where `id`='$user_id'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return $out;
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
		$fields['room_id'] = (int)$fields['room_id'];
		$fields['user_reg' ] =$_SESSION['user_id'];
		$fields['toz' ] = $fields['toz'];
		$fields['regdate'] = date("Y-m-d H:i:s");
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
	        $query="insert into `tasisat_tmp` $fi values $valu";
	        mysql_class::ex_sqlx($query);
        }
	$grid = new jshowGrid_new("tasisat_tmp","grid1");
	$grid->width = '70%';
	$grid->index_width = '20px';
	$grid->whereClause=" 1=1 order by `regdate` DESC";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = 'شماره اتاق';
	$grid->columnLists[1] = room_name();
	//$grid->columnFunctions[4] = 'ppdate';
	$grid->columnHeaders[2] = 'کاربر ثبت کننده';
	$grid->columnFunctions[2] = 'loadUser';
	$grid->columnAccesses[2] = 0;
	$grid->columnHeaders[3] = 'توضیحات';
	$grid->columnHeaders[4] = 'تاریخ ثبت';
	$grid->columnFunctions[4] = 'ppdate';
	$grid->columnAccesses[4] = 0;
	$grid->addFunction = 'add_item';
	$grid->canEdit = FALSE;
	$grid->canDelete = TRUE;
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
		<script>
			$(document).ready(function(){
				$("#new_regdate").hide();
				$("#new_user_reg").hide();
			});
		</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
			اتاق های دارای مشکل	
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php echo $out; ?>
		</div>
	</body>
</html>
