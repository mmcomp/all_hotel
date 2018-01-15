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
	$msg = '';
	$logOutBu = '<a title="خروج" href="login.php"><img src="../img/Log-Out-icon.png"/>خروج</a>';
	function ppdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
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
		$now = date("Y-m-d");
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
		if (isset($_REQUEST['tarikh']))
			$fields['tarikh'] = $_REQUEST['tarikh'];
		else
			$fields['tarikh'] = $now;
		$fields['user_id' ] =$_SESSION['user_id'];
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
		if (($fields['subject']!='موضوع')&&($fields['matnGozaresh' ]!='متن'))
		{
			$query="insert into `gozareshRozane` $fi values $valu";
			mysql_class::ex_sqlx($query);
		}
		else
			echo '<br/><center><h3>'."لطفا تمامی اطلاعات درخواستی را با دقت و کامل وارد نمایید".'</h3></center>';
        }
	if ($se->detailAuth('admin'))
	{
		mysql_class::ex_sql("select `id` from `user` order by `lname`",$qn);
		$user_ids = '';
		while($t = mysql_fetch_array($qn))
			$user_ids .=(($user_ids=='')?'':',').$t['id'];
		$shart = "1=1 order by FIELD(`user_id`,$user_ids),`tarikh`";
	}
	else
		$shart = " `user_id`='$user_id'";
	$grid = new jshowGrid_new("gozareshRozane","grid1");
	$grid->width = '99%';
	$grid->whereClause= $shart;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = 'نام کاربری';
	$grid->columnFunctions[1] = 'loadUser';
	$grid->columnHeaders[2] = 'تاریخ';
	$grid->columnFunctions[2] = 'ppdate';
	$grid->columnHeaders[3] = 'موضوع';
	$grid->columnHeaders[4] = 'متن';
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
گزارش روزانه
		</title>
		<script>
			$(document).ready(function(){
				$("#new_tarikh").hide();
				$("#new_user_id").hide();
			});
		</script>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
		<br/>
		<a title="خروج" href="login.php"><img src="../img/Log-Out-icon.png"/></a>
			<br/>
			<?php 
				echo $msg.'<br/>';
				echo $out; ?>
		</div>		
	</body>
</html>
