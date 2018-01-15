<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$reserve_id = isset($_REQUEST['reserve_id']) ? (int)$_REQUEST['reserve_id']:-1;
	function tarikh($str)
        {
                $out=jdate('H:i:s Y/n/j',strtotime($str));
                return $out;
        }
	function loadKhad($inp)
	{
		//$kh = new khadamat_det_class((int)$inp);
		$k = new khadamat_class($inp);
		return($k->name);
	}
	function loadMenu1($inp)
	{
		global $reserve_id;
		$ghaza = "";
		mysql_class::ex_sql("select * from `khadamat_det_front` where `khadamat_det_id` = $inp",$q);
		while($r= mysql_fetch_array($q))
		{
			$sandogh_id = $r["sandogh_item_id"];
			$tedad = $r["tedad_kol"];
			mysql_class::ex_sql("select `id`,`name` from `sandogh_item` where `id` = $sandogh_id",$qu);
			if($row= mysql_fetch_array($qu))
			{
				$name_ghaza = $row["name"];
			
			}
			$ghaza .= $name_ghaza.'(تعداد:'.$tedad.')';
		}
		$out =$ghaza.'<br/><div class="msg pointer" onclick=\'window.open("ghazaListDet.php?res='.$reserve_id.'&khadamat_det_id='.$inp.'");\' >تعریف منو</div>';
		return($out);
	}	
	
	$shart = " `reserve_id` =  '$reserve_id' AND  `khadamat_id` IN (SELECT  `id` FROM  `khadamat` WHERE `ghazaAst` =  '1' and `name`!='صبحانه') order by `tarikh`,`khadamat_id`";
	$grid = new jshowGrid_new('khadamat_det','grid2');
	$grid->index_width='30px';
	$grid->whereClause= $shart;
	$grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = 'وعده';
	$grid->columnFunctions[1] = 'loadKhad';
       	$grid->columnHeaders[2] =null ;
	$grid->columnHeaders[3] = null;
	$grid->columnHeaders[4] = 'تاریخ';
	$grid->columnFunctions[4] = 'tarikh';
	$grid->columnHeaders[5] = 'تعداد';
	$grid->columnHeaders[6] = null;
	$grid->columnHeaders[7] = 'وضعیت';
	$grid->addfeild('id');
	$grid->columnHeaders[8] = 'منو غذایی';
	$grid->columnFunctions[8] = 'loadMenu1';
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->sortEnabled = TRUE;
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
		غذای مهمان
		</title>
<style type="text/css" media="screen">
.square {
    width: 144px;
    height: 144px;
    background: #f0f;
    margin-right: 48px;
    float: left;
}

.transformed {
    -webkit-transform: rotate(90deg) scale(1, 1);
    -moz-transform: rotate(90deg) scale(1, 1);
    -ms-transform: rotate(90deg) scale(1, 1);
    transform: rotate(90deg) scale(1, 1);
}
</style>

	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>

</html>
