<?php
	session_start();
	include("../kernel.php");
	include("../simplejson.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$is_admin = FALSE;
	if($se->detailAuth('all'))
		$is_admin = TRUE;
	function loadQues($ques_id)
	{
		$out = '----';
		$ques_id = (int)$ques_id;
		mysql_class::ex_sql("select name from ravabet_ques where id = $ques_id",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return($out);
	}
	function loadNatayej($q_id)
	{
		$q_id = (int)$q_id;
		$out = '<table width="100%" border="1"><tr>';
		mysql_class::ex_sql("SELECT `answer`,count(`ravabet_det`.`id`) as `co`  FROM `ravabet_det` left join `ravabet` on (`ravabet`.`id`=`ravabet_id`) where `ravabet_ques_id` = $q_id group by `answer` ORDER BY  `answer` ",$q);
		
		$answers_db = array(0,0,0,0,0);
		while($r = mysql_fetch_array($q))
			$answers_db[(int)$r['answer']] = (int)$r['co'];
		foreach($answers_db as $answer)
			$out .= '<th width="20%">'.$answer.'</th>';
		//$out .= "<th>salam</th>";
		$out .= '</tr></table>';
		return($out);
	}
	$nat_header ="
		<table width='100%' border='1'><tr>
			<th width='20%'>
				هیچ
			</th>
			<th width='20%'>
				بد
			</th>
			<th width='20%'>
				متوسط
			</th>
			<th width='20%'>
				خوب
			</th>
			<th width='20%'>
				عالی
			</th>
		</tr></table>
";
	$user_id = (int)$_SESSION['user_id'];
	$GLOBALS['msg'] = '';
	$ravabet_id = -1;
	$tarikh = '----';
	$grid = new jshowGrid_new("ravabet_ques","grid1");
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->fieldList[0] = 'name';
	$grid->fieldList[1] = 'id';
	$grid->columnHeaders[0] ='سوال';
	$grid->columnHeaders[1] =$nat_header;
	$grid->columnFunctions[1] = 'loadNatayej';
/*
	$grid->whereClause = " ravabet_id = $ravabet_id order by `id`";
	$grid->columnHeaders[0] ='';
	$grid->columnHeaders[1] ="";
	$grid->columnHeaders[2] ="سوال";
	$grid->columnAccesses[2] = 0;
	$grid->columnFunctions[2] = "loadQues";
	$grid->columnHeaders[3] ="پاسخ";
	$grid->columnLists[3] = array(
					'بد'=>1,
					'متوسط'=>2,
					'خوب'=>3,
					'عالی'=>4
				);
	$grid->columnAccesses[3] = $se->detailAuth('ravabet') || $is_admin;
	$grid->columnHeaders[4] ="توضیحات";
	$grid->columnAccesses[4] = $se->detailAuth('ravabet') || $is_admin;
*/
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	$grid->canEdit = FALSE;
	$grid->pageCount = 0;
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
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه نرم افزاری رزرو آنلاین بهار
		</title>
	</head>
	<body>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<?php echo '<h2>'.$GLOBALS['msg'].'</h2>' ?>
			<br/>
			<?php echo $out;  ?>
		</div>
		<script language="javascript">
			document.getElementById('new_id').style.display = 'none';
		</script>
	</body>
</html>

