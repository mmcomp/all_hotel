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
	function loadRoom()
	{
		if (isset($_REQUEST["hotel_id_new"]))
			$hotel_id_new = $_REQUEST["hotel_id_new"];
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
		$fields['en'] = '-1';
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
	$shart_1 = '';
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart = '';
	if($hotel_acc!=null)
	{
		if (count($hotel_acc)==1)
			$_REQUEST["hotel_id_new"] = $hotel_acc[0];
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
		$shart_1 = "where `id` in ".$shart;
	}
////////////////////
	if (isset($_REQUEST["hotel_id_new"]))
                $hotel_id_new = $_REQUEST["hotel_id_new"];
	else
		$hotel_id_new = -1;
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
		$combo_hotel .= "هتل : <select class='inp' id=\"hotel_id\" name=\"hotel_id_new\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel`$shart_1 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$hotel_id_new)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_hotel .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
		        $combo_hotel .= $r["name"]."\n";
		        $combo_hotel .= "</option>\n";
		}
		$combo_hotel .= "</select>";
	$combo_hotel .= "</form>";
	$grid = new jshowGrid_new("tasisat_tmp","grid1");
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->whereClause= "`en`='-1' and `hotel_id`=".$hotel_id_new;
	$grid->setERequest(array('hotel_id_new'=>$hotel_id_new));
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = 'هتل';
	$grid->columnLists[1] = loadHotel();
	$grid->columnHeaders[2] = 'شماره اتاق';
	$grid->columnLists[2] = loadRoom();
	//$grid->columnFunctions[4] = 'ppdate';
	$grid->columnHeaders[3] = 'ثبت کننده ';
	$grid->columnFunctions[3] = 'loadUser';
	$grid->columnAccesses[3] = 0;
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[5] = 'توضیحات';
	$grid->columnHeaders[6] = null;
	$grid->columnHeaders[7] = 'تاریخ ثبت';
	$grid->columnFunctions[7] = 'ppdate';
	$grid->columnAccesses[7] = 0;
	$grid->columnHeaders[8] = null;
	$grid->columnHeaders[9] = null;
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
			<?php echo $combo_hotel.'<br/>'.$out; ?>
		</div>
	</body>
</html>
