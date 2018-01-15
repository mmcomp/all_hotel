<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view || !isset($_REQUEST['sandogh_id']))
                die(lang_fa_class::access_deny);
	function ppdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function add_item()
	{
		$fields = jshowGrid_new::loadNewFeilds($_REQUEST);
		$fields['room_id'] = (int)$_REQUEST['room_id'];
		$fields['regdate' ] =null;
		$fields['answer'] ='';
		$fields['isFixed'] = 0;
		$fields['regdate'] = date("Y-m-d H:i:s");
		$q = jshowGrid_new::createAddQuery($fields);
		mysql_class::ex_sqlx("insert into `tasisat` ".$q['fi']." values ".$q['valu']);
	}
	function delete_item($id)
	{
		mysql_class::ex_sql("select `answer` , `isFixed` from `tasisat` where `id` = $id",$q);
		if($r = mysql_fetch_array($q))
			if((int)$r['isFixed'] == 0 && trim($r['answer']) == '')
				mysql_class::ex_sqlx("delete from `tasisat` where `id` = $id");
	}
	function loadItem($id)
	{
		$out = '----';
		mysql_class::ex_sql("select `name` from `sandogh_item` where `id` = $id",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return($out);
	}
	function loadTedad($sid)
	{
		$out = 0;
		$sandogh_id = (int)$_REQUEST['sandogh_id'];
		$sandogh = new sandogh_class($sandogh_id);
		$tarikh = isset($_REQUEST['tarikh'])?$_REQUEST['tarikh']:date("Y-m-d");
		$khids = array(-1000);
		mysql_class::ex_sql("select `id` from `khadamat_det` where date(`tarikh`) = '$tarikh' and `khadamat_id` in (".implode(',',$sandogh->khadamat_ids).")",$q);
		while($r = mysql_fetch_array($q))
			$khids[] = (int)$r['id'];
		if(count($sandogh->khadamat_ids)>0)
		{
			$q = null;
			mysql_class::ex_sql("select sum(`tedad_kol`-`tedad_used`) as `s` from `khadamat_det_front` where `sandogh_item_id` = $sid and `khadamat_det_id` in (".implode(',',$khids).")",$q);
			if($r = mysql_fetch_array($q))
				$out = (int)$r['s'];
		}
		return($out);
	}
	$sandogh_id = (int)$_REQUEST['sandogh_id'];
	$sandogh = new sandogh_class($sandogh_id);
	$tarikh = isset($_REQUEST['tarikh'])?$_REQUEST['tarikh']:date("Y-m-d");
	$sitem = array(-1000);
	mysql_class::ex_sql("select `id` from `sandogh_item` where `sandogh_id` = $sandogh_id",$q);
	while($r = mysql_fetch_array($q))
		$sitem[] = (int)$r['id'];
	$khids = array(-1000);
	if(count($sandogh->khadamat_ids)>0)
	{
		$q=null;
		mysql_class::ex_sql("select `id` from `khadamat_det` where date(`tarikh`) = '$tarikh' and `khadamat_id` in (".implode(',',$sandogh->khadamat_ids).")",$q);
		while($r = mysql_fetch_array($q))
			$khids[] = (int)$r['id'];
	}
	$grid = new jshowGrid_new("khadamat_det_front","grid1");
	$grid->setERequest(array("sandogh_id"=>$sandogh_id));
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->showAddDefault = FALSE;
	$grid->whereClause = "`sandogh_item_id` in (".implode(',',$sitem).") and `khadamat_det_id` in (".implode(',',$khids).")  group by `sandogh_item_id` ";
	for($i=0;$i<count($grid->columnHeaders);$i++)
		$grid->columnHeaders[$i] = null;
	$grid->columnHeaders[2] = 'غذا';
	$grid->columnFunctions[2] = 'loadItem';
	$grid->addFeild("sandogh_item_id");
	$grid->columnHeaders[5] = 'تعداد';
	$grid->columnFunctions[5] = 'loadTedad';
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	$grid->canEdit = FALSE;
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
//				$("#new_regdate").hide();
//				$("#new_answer").hide();
//				$("#new_isFixed").hide();
			});
		</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
			سامانه رزرواسیون	
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<?php echo $out; ?>
		</div>
	</body>
</html>
