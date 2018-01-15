<?php
	session_start();
	include("../kernel.php");
	include("../simplejson.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadAztarikh($aztar)
	{
		$out = '<select class="inp" name="aztarikh" id="aztarikh">';
		$tmp = mehman_class::pazireshDate();
		for($i=0;$i<count($tmp);$i++)
		{
			$tmp_date = explode(' ',audit_class::hamed_pdateBack($tmp[$i]));
			$tmp_date = $tmp_date[0];
			$sel =(strtotime($tmp_date)==strtotime($aztar))?'selected="selected"':'';
			$out .="<option $sel value='".$tmp[$i]."' >".$tmp[$i]."</option>\n";
		}
		$out .='</select>';
		return $out;
	}
	function loadGhaza($inp)
        {
                $out = null;
                mysql_class::ex_sql("select `id`,`name` from sandogh_item where `id`='$inp' order by name",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out =$r['name'];
                return $out;
        }
	$msg = '';
	$isAdmin = $se->detailAuth('all');
	$is_admin =$isAdmin;;
	$aztarikh = ((isset($_REQUEST['aztarikh']) && $_REQUEST['aztarikh']!='')?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date("Y-m-d"));
	$user_id=-1;
	if((int)$_SESSION['typ']==1)
		$user_id = (int)$_SESSION['user_id'];
	$curtime = strtotime(date("Y-m-d"));
	$az = strtotime($aztarikh);
	//$ta = strtotime($tatarikh);
	/*
	if($az - $curtime <= 24*60*60 && !$is_admin)
	{
		$aztarikh = date("Y-m-d",$curtime);
		$tatarikh = date("Y-m-d",$curtime);
	}
	else
	{
	*/
	$aztarikh = explode(" ",$aztarikh);
	$aztarikh = $aztarikh[0];
	$tedad_kol = 0;
	$jame_kol = 0;
	$grid = new jshowGrid_new('khadamat_det_front','grid2');
//	$grid->setERequest(array("khadamat_det_id"=>$khadamat_det_id));
//	$grid->whereClause=" `khadamat_det_id`=$khadamat_det_id ";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[2] = "غذا";
	$grid->columnFunction[2] = "loadGhaza";
	$grid->columnHeaders[1] =null ;
	$grid->columnHeaders[3] = "تعداد";
	$grid->columnHeaders[4] = null;
//	$grid->columnLists[2]=$combo;
	$grid->addFunction = 'add_item';
	$grid->canEdit = FALSE;
	$grid->sortEnabled = TRUE;
	$grid->showAddDefault = FALSE;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript">
		function send_search()
		{
			document.getElementById('mod').value = 2;
			document.getElementById('frm1').submit();
		}
		function paziresh(reserve_code,reserve_id)
		{
			$.getJSON("paziresh_search.php?reserve_code="+reserve_code+"&reserve_paziresh_id="+reserve_id+"&",function(result){
				if(result.res.length>0)
				{
					var tout=result.res.join();
					alert('متأسفانه شماه رزرو(های) '+tout+' خارج نشده است.');	
				}
				else
					window.open("paziresh.php?reserve_id="+result.reserve_code+"&kh=0");
			});
		}
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
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<br/>
			<form id='frm1'  method='GET' >
			<table border='1' style='font-size:12px;' >
				<tr>
					<th style='display:none;' >نام</th>
					<th>تاریخ ورود</th>
					<th style='display:none;' >تاریخ خروج</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					<td style='display:none;' >		-->
						<?php echo  loadAztarikh($aztarikh); ?>
					</td>
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
			</form>
			<?php echo $out;?>
		</div>
	</body>
</html>
