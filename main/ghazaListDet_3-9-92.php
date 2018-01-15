<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$reserve_id=isset($_REQUEST['res']) ? (int)$_REQUEST['res']:-1;
	$room_tedad = -1;
	function shRoom()
	{
		$room = null;
		$reserve_id=isset($_REQUEST['res']) ? (int)$_REQUEST['res']:-1;
		mysql_class::ex_sql("SELECT `room_id` FROM `room_det` WHERE `reserve_id` =$reserve_id",$qures);
		//$room="<select name='room'>";
		while($resul=mysql_fetch_array($qures))
		{
			$tmp=$resul['room_id'];
			mysql_class::ex_sql("SELECT `id`,`name` FROM `room` WHERE `id` =$tmp",$qures2);
			if($resul2=mysql_fetch_array($qures2))
			{
				$tmp2=$resul2['name'];
				$tmp3=(int)$resul2['id'];
				//$room.="<option value='$tmp3' >".$tmp2."<option>";
				$room[$tmp2] = $tmp3;
			}
		}
		//$room.="</select>";
		return($room);
	}
	function shRoom_name($inp)
	{
		$room = '';
		mysql_class::ex_sql("SELECT `name` FROM `room` WHERE `id` ='$inp'",$qures2);
		if($resul2=mysql_fetch_array($qures2))
			$room = $resul2['name'];
		return($room);
	}
	function add_item()
	{
		$fields = jshowGrid_new::loadNewFeilds($_REQUEST);
		$fields["khadamat_det_id"] = (int)$_REQUEST['khadamat_det_id'];
		$fields["id"] = null;
		$fields["tedad_used"] = 0;
		$k = new khadamat_det_class((int)$_REQUEST['khadamat_det_id']);
		$nowTedad = 0;
		mysql_class::ex_sql("select sum(`tedad_kol`) as `stedad` from `khadamat_det_front` where `khadamat_det_id` = ".(int)$_REQUEST['khadamat_det_id'],$q);
		if($r = mysql_fetch_array($q))
			$nowTedad = (int)$r['stedad'];
		$tekrari = FALSE;
		$q = null;
/*
		mysql_class::ex_sql("select `id` from `khadamat_det_front` where `khadamat_det_id` = ".(int)$_REQUEST['khadamat_det_id']." and `sandogh_item_id` = ".$fields['sandogh_item_id'],$q);
		if($r = mysql_fetch_array($q))
			$tekrari = TRUE;
*/
		if(($nowTedad + (int)$fields['tedad_kol'] <= (int)$k->tedad) )
		{
			$qu = jshowGrid_new::createAddQuery($fields);
			mysql_class::ex_sqlx("insert into `khadamat_det_front` ".$qu['fi']." values ".$qu['valu']);
		}
	}
	function add_item_1room()
	{
		$reserve_id=isset($_REQUEST['res']) ? (int)$_REQUEST['res']:-1;
		$fields = jshowGrid_new::loadNewFeilds($_REQUEST);
		$fields["khadamat_det_id"] = (int)$_REQUEST['khadamat_det_id'];
		$fields["id"] = null;
		$fields["tedad_used"] = 0;
		$k = new khadamat_det_class((int)$_REQUEST['khadamat_det_id']);
		mysql_class::ex_sql("SELECT `room_id` FROM `room_det` WHERE `reserve_id` =$reserve_id",$q_room);
		if($r_room= mysql_fetch_array($q_room))
			$fields["room_id"] = $r_room['room_id'];
		$nowTedad = 0;
		mysql_class::ex_sql("select sum(`tedad_kol`) as `stedad` from `khadamat_det_front` where `khadamat_det_id` = ".(int)$_REQUEST['khadamat_det_id'],$q);
		if($r = mysql_fetch_array($q))
			$nowTedad = (int)$r['stedad'];
		$tekrari = FALSE;
		$q = null;
		if(($nowTedad + (int)$fields['tedad_kol'] <= (int)$k->tedad) )
		{
			$qu = jshowGrid_new::createAddQuery($fields);
			mysql_class::ex_sqlx("insert into `khadamat_det_front` ".$qu['fi']." values ".$qu['valu']);
		}
	}
	$out = 'خطا';
	$khadamat_det_id = isset($_REQUEST['khadamat_det_id']) ? (int)$_REQUEST['khadamat_det_id']:-1;
	if($khadamat_det_id > 0)
	{
		
		$k = new khadamat_det_class($khadamat_det_id);
		$sandogh_id = -1;
		mysql_class::ex_sql("select `sandogh_id` from `sandogh_khadamat` where `khadamat_id` = ".$k->khadamat_id,$q);
		if($r = mysql_fetch_array($q))
			$sandogh_id = (int)$r['sandogh_id'];
		if($sandogh_id > 0)
		{
			$q = null;
			$combo = null;
			mysql_class::ex_sql("select `id`,`name` from `sandogh_item` where `sandogh_id` = $sandogh_id order by `name`",$q);
			while($r= mysql_fetch_array($q))
				$combo[$r['name']] = $r['id'];// .= "<option value='".$r['id']."' >".$r['name']."</option>\n";
			
			$grid = new jshowGrid_new('khadamat_det_front','grid2');
			$grid->setERequest(array("khadamat_det_id"=>$khadamat_det_id));
			$grid->whereClause=" `khadamat_det_id`=$khadamat_det_id ";
			$grid->columnHeaders[0] = null;
			$grid->columnHeaders[2] = "غذا";
			$grid->columnLists[2]=$combo;
			$grid->columnHeaders[1] =null ;
			$grid->columnHeaders[3] = "تعداد";
			$grid->columnHeaders[4] = null;
			mysql_class::ex_sql("SELECT count(`room_id`) as `c_room` FROM `room_det` WHERE `reserve_id` =$reserve_id",$q_room);
			if($r_room= mysql_fetch_array($q_room))
			{
				if($r_room['c_room']>=2)
				{echo '2';
					$room_tedad = 2;
					$grid->columnHeaders[5]='شماره اتاق';
					$grid->columnLists[5] = shRoom();
					//$grid->columnFunctions[5] = 'shRoom';
					$grid->addFunction = 'add_item';
				}
				else
				{
					$room_tedad = 1;
					$grid->columnHeaders[5]='شماره اتاق';
					$grid->columnFunctions[5] = 'shRoom_name';
					$grid->addFunction = 'add_item_1room';				
				}
			}
			//$grid->canEdit = FALSE;
			$grid->canDelete = FALSE;
			$grid->sortEnabled = TRUE;
			$grid->showAddDefault = FALSE;
			$grid->intial();
			$grid->executeQuery();
			$out = $grid->getGrid();
		}
		else
			$out = 'خدمات مورد نظر صندوق ندارد';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
                <link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
                <link type="text/css" href="../css/style.css" rel="stylesheet" />
                <link href="../css/ih_style.css" rel="stylesheet" type="text/css" />
                <script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script> 
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/raphael-min.js"></script>
		<script type="text/javascript" src="js/clock.js"></script>
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
			<?php	
				echo $out;
				if ($room_tedad==1)
					echo "<script>document.getElementById('new_room_id').style.display='none';</script>";
			?>
		</div>
	</body>

</html>
