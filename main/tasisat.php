<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view || !isset($_REQUEST['room_id']))
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
	$room_id = (int)$_REQUEST['room_id'];
	$isTasisat = $se->detailAuth('tasisat');
	$isSuper = $se->detailAuth('super');
	$grid = new jshowGrid_new("tasisat","grid1");
	$grid->setERequest(array("room_id"=>$room_id));
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->showAddDefault = FALSE;
	$grid->whereClause = "`room_id`=$room_id ".(($isTasisat)?"and `isFixed` = 0":'')." order by `regdate`";
	for($i=0;$i<count($grid->columnHeaders);$i++)
		$grid->columnHeaders[$i] = null;
	$grid->columnHeaders[3] = 'شرح';
	$grid->columnAccesses[3] = 0;
	$grid->columnHeaders[4] = 'زمان اعلام';
	$grid->columnAccesses[4] = 0;
	$grid->columnFunctions[4] = 'ppdate';
	$grid->columnHeaders[6] = 'پاسخ';
	$grid->columnHeaders[8] = 'اصلاح شد؟';
	$grid->columnLists[8] = array("خیر"=>0,"بله"=>1);
	if($isSuper)
	{
		$grid->columnAccesses[6] = 0;
		$grid->columnAccesses[8] = 0;
	}
	$grid->canAdd = $isSuper || $se->detailAuth('all');
	$grid->canDelete = $isSuper || $se->detailAuth('all');
	$grid->canEdit = $isSuper || $isTasisat || $se->detailAuth('all');
	$grid->addFunction = 'add_item';
	$grid->deleteFunction = 'delete_item';
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
				$("#new_answer").hide();
				$("#new_isFixed").hide();
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
