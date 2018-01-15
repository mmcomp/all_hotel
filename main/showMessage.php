<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);	
	if (isset($_REQUEST["group_id_new"]))
                $group_id_new = $_REQUEST["group_id_new"];
	else
		$group_id_new = -1;
	$_SESSION['grop'] = $group_id_new;
	function loadUser($group_id_new)
        {
                $out = array();
		if ($group_id_new!=-1)
		{
			mysql_class::ex_sql("select `id` , `fname`, `lname` from `user` where `typ`='$group_id_new' and `user`!='mehrdad'",$q);
			$out['همه']= -1;
			while($r = mysql_fetch_array($q))
			{
				$name = $r['fname'].' '.$r['lname'];
				$out[$name]=$r['id'];
			}
		}
		else
		{
			$out['همه']= -1;
			mysql_class::ex_sql("select `id` , `fname`, `lname` from `user` where `user`!='mehrdad'",$q);
			while($r = mysql_fetch_array($q))
			{
				$name = $r['fname'].' '.$r['lname'];
				$out[$name]=$r['id'];
			}
		}
		return($out);
        }
	function loadGropUser($inp)
        {
                $out = '';
		if ($inp==-1)
			$out = 'همه';
		else
		{
			mysql_class::ex_sql("select `name` from `grop` where `id`='$inp'",$q);
			if($r = mysql_fetch_array($q))
				$out= $r['name'];
		}
		return($out);
        }
	function loadUserPasMatn($inp)
        {
                $out = '';
		if ($inp==-1)
			$out = '---';
		else
			$out= $inp;
		return($out);
        }
	function loadUserPas($inp)
        {
                $out = '';
		if ($inp==-1)
			$out = '---';
		else
		{
			mysql_class::ex_sql("select `id` , `fname`, `lname` from `user` where `id`='$inp'",$q);
			if($r = mysql_fetch_array($q))
				$out= $r['fname'].' '.$r['lname'];
		}
		return($out);
        }
	function loadVaz($inp)
        {
                $out = '';
		if ($inp==-1)
			$out = 'پاسخ داده نشده';
		else
			$out = 'پاسخ داده شده';
		return($out);
        }
	function edit_item($id,$feild,$value)
	{
		$conf = new conf;
		$user_id = $_SESSION['user_id'];
		$today = date("Y-m-d h:i:s");
		if($feild=='toz_pasokh')
		{
			mysql_class::ex_sqlx("update `payam` set `toz_pasokh`='$value',`user_pasokh`='$user_id',`en`='1' where `id`=$id");
		}
		
	}
	if (isset($_REQUEST['user_id']))
		$user_rec_id = $_REQUEST['user_id'];
	else
		$user_rec_id = -1;
	if (isset($_REQUEST['rec_grop']))
		$rec_grop_id = $_REQUEST['rec_grop'];
	else
		$rec_grop_id = -1;
	$grid = new jshowGrid_new("payam","grid1");
	$grid->whereClause= " (`rec_user`='-1' or `rec_user`='$user_rec_id') and `rec_grop`='$rec_grop_id' order by `en`";
	$grid->setERequest(array('user_id'=>$user_rec_id,'rec_grop'=>$rec_grop_id));
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= 'کاربر ثبت کننده';
	$grid->columnFunctions[1] = 'loadUserPas';
	$grid->columnAccesses[1] = 0;
	$grid->columnHeaders[2]= 'متن پیام';
	$grid->columnAccesses[2] = 0;
	$grid->columnHeaders[3]= 'گروه کاربری دریافت کننده پیام';
	$grid->columnFunctions[3] = 'loadGropUser';
	$grid->columnAccesses[3] = 0;
	$grid->columnHeaders[4]= 'کاربر دریافت کننده پیام';
	$grid->columnLists[4] = loadUser($group_id_new);
	$grid->columnAccesses[4] = 0;
	$grid->columnHeaders[5]= 'کاربر پاسخ دهنده';
	$grid->columnFunctions[5] = 'loadUserPas';
	$grid->columnAccesses[5] = 0;
	$grid->columnHeaders[6]= 'متن پاسخ';
	$grid->columnFunctions[6] = 'loadUserPasMatn';
	$grid->columnHeaders[7]= 'وضعیت';
	$grid->columnFunctions[7] = 'loadVaz';
	$grid->columnAccesses[7] = 0;
	$grid->editFunction = 'edit_item';
	$grid->canAdd = FALSE;
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
				$("#new_user_id").hide();
				$("#new_rec_grop").hide();
				$("#new_user_pasokh").hide();
				$("#new_toz_pasokh").hide();
				$("#new_en").hide();
			});
		</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
			پاسخ به پیام	
		</title>
	</head>
	<body>
		<br/>
		<br/>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="index.php" target="_blank"><img src="../img/home.png"/></a>
		</div>
		<div align="center">
			<?php echo $out; ?>
		</div>
	</body>
</html>
