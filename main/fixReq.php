<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadRoom()
	{
		if (isset($_REQUEST["hotel_id"]))
			$hotel_id_new = $_REQUEST["hotel_id"];
		else
			$hotel_id_new = -1;
		$out = array();
		mysql_class::ex_sql("select `id` , `name` from `room` where `hotel_id`='$hotel_id_new'",$q);
		while($r = mysql_fetch_array($q))
			$out[$r['name']]=$r['id'];
		return($out);
	}
	function loadHotel()
        {
                $out = array();
		mysql_class::ex_sql("select `id` , `name` from `hotel`",$q);
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
	function ppdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function edit_item($id,$feild,$value)
	{
		$conf = new conf;
		$user_id = $_SESSION['user_id'];
		$today = date("Y-m-d h:i:s");
		if($feild=='en')
		{
			mysql_class::ex_sql("select `toz_fix` from `guest_req` where `id`='$id'",$q);
			if($r = mysql_fetch_array($q))	
			{
				if ($r['toz_fix']!='')
					mysql_class::ex_sqlx("update `guest_req` set `user_fixed`='$user_id',`en`='1',`date_fix`='$today' where `id`=$id ");
				else
					echo '<br/><center><h3>'.'لطفا برای رفع درخواست توضیح را وارد نمایید'.'</h3></center>';
			}
		}
		elseif($feild=='toz')
		{
			mysql_class::ex_sqlx("update `guest_req` set `user_fixed`='$user_id',`toz`='$value',`date_fix`='$today' where `id`=$id ");
		}
		elseif($feild=='toz_fix')
		{
			mysql_class::ex_sqlx("update `guest_req` set `user_fixed`='$user_id',`toz_fix`='$value',`date_fix`='$today' where `id`=$id ");
		}
		else
			$c = '1=1';
	}
	if ((isset($_REQUEST['hotel_id']))&&(isset($_REQUEST['room_id'])))
	{
		$h_id = $_REQUEST['hotel_id'];
		$r_id = $_REQUEST['room_id'];
		/*mysql_class::ex_sql("select `id` from `tasisat_tmp` where `hotel_id` = '$h_id' and `room_id`='$r_id'",$q);
		if($r = mysql_fetch_array($q))
		{
			$id = $r['id'];
			mysql_class::ex_sqlx("update `tasisat_tmp` set `en` = '1' where `id` ='$id'");
			$out = "درخواست گزارش داده شده رفع شد";
		}*/
	}
	else
	{
		$h_id = -1;
		$r_id = -1;
	}
	$stat["حل نشد"]=-1;
	$stat["حل شد"]=1;
	$grid = new jshowGrid_new("guest_req","grid1");
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->whereClause= " `hotel_id`='".$h_id."' and `room_id`='".$r_id."'";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = 'هتل';
	$grid->columnLists[1] = loadHotel();
	$grid->columnAccesses[1] = 0;
	$grid->columnHeaders[2] = 'شماره اتاق';
	$grid->columnLists[2] = loadRoom();
	$grid->columnAccesses[2] = 0;
	$grid->columnHeaders[3] = 'شماره رزرو';
	$grid->columnAccesses[3] = 0;
	//$grid->columnFunctions[4] = 'ppdate';
	$grid->columnHeaders[4] = 'ثبت کننده درخواست';
	$grid->columnFunctions[4] = 'loadUser';
	$grid->columnAccesses[4] = 0;
	$grid->columnHeaders[5] = 'برطرف کننده درخواست ';
	$grid->columnFunctions[5] = 'loadUser';
	$grid->columnAccesses[5] = 0;
	$grid->columnHeaders[6] = 'توضیح درخواست';
	$grid->columnAccesses[6] = 0;
	$grid->columnHeaders[7] = 'توضیح رفع درخواست';
	$grid->columnHeaders[8] = 'تاریخ ثبت';
	$grid->columnFunctions[8] = 'ppdate';
	$grid->columnAccesses[8] = 0;
	$grid->columnHeaders[9] = 'تاریخ رفع درخواست';
	$grid->columnFunctions[9] = 'ppdate';
	$grid->columnAccesses[9] = 0;
	$grid->columnHeaders[10] = 'وضعیت';
	$grid->columnLists[10] = $stat;
	$grid->editFunction = 'edit_item';
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
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
			رفع درخواست اتاق
		</title>
	</head>
	<body>
		<br/>
		<br/>
		<center><h2><?php echo $out;?></h2></center>
	</body>
</html>
