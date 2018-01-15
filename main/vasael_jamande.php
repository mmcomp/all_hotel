<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
	//$admin=security_class::auth((int)$_SESSION['admin']);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if (isset($_SESSION['user_id']))
		$user_id = $_SESSION['user_id'];
	else
		$user_id = -1;
	function ppdate($inp)
	{		
		$out = "";
		$t = explode(" ",$inp);
		$d = $t[0];
		$ti = $t[1];
		$t_shamsi = audit_class::hamed_pdate($d);
		$out = $t_shamsi.' '.$ti;
		return $out;
	}
	function ppdate1($inp)
	{
		
		return(audit_class::hamed_pdate($inp));
		
	}
	function room_name()
	{
		$out = array();
		mysql_class::ex_sql("select `id` , `name` from `room`",$q);
		while($r = mysql_fetch_array($q))
			$out[$r['name']]=$r['id'];
		return($out);
	}
	function loadVazeat()
	{	
		$out = array();
		$out["ارسال نشده"] = 0;	
		$out["ارسال شده"] = 1;		
		return($out);
	}
	function loadVazeat_norm($inp)
	{	
		$out = "";
		if ($inp==0)
			$out = "ارسال نشده";
		else
			$out = "ارسال شده";
		return $out;
	}
	function loadUser($user_id)
	{
		$out = "";
		mysql_class::ex_sql("select `fname` , `lname` from `user` where `id`='$user_id'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return $out;
	}
	function change($num)
	{
		if($num==0)
			return ('ارسال نشده');
		else 
			return('ارسال شده');
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
		$reserve_id = (int)$fields['reserve_id'];
		mysql_class::ex_sql("SELECT `lname` FROM `hotel_reserve` WHERE `reserve_id`=$reserve_id",$q2);
		while($r2 = mysql_fetch_array($q2))
			$n_sargorooh=$r2['lname'];
		$fields['n_sargorooh' ] =$n_sargorooh;
		$fields['time_sabt'] = date("Y-m-d H:i:s");
		$fields['time_ersal']=audit_class::hamed_pdateBack($fields['time_ersal']);
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
		if($fields['time_sabt'])
	       		$query="INSERT INTO `v_jamande` $fi VALUES $valu";
		//echo $query;
	        mysql_class::ex_sqlx($query);
        }
	if (isset($_REQUEST["hotel_id_new"]))
                $hotel_id_new = $_REQUEST["hotel_id_new"];
	else
		$hotel_id_new = -1;
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
		$combo_hotel .= "هتل : <select class='inp' id=\"hotel_id\" name=\"hotel_id_new\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel` order by `name`",$q);
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
	$grid = new jshowGrid_new("v_jamande","grid1");
	$grid->width = '99%';
	$grid->index_width = '20px';
	/*$grid->whereClause=" 1=1 order by `regdate` DESC";*/
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = 'شماره اتاق';
	$grid->columnLists[1] = room_name();
	$grid->columnHeaders[2] = 'شماره رزرو';
	$grid->columnAccesses[2] = 0;
	$grid->columnHeaders[3] = 'سرگروه';
	$grid->columnAccesses[3] = 0;
	$grid->columnHeaders[4] = 'وسایل';
	$grid->columnHeaders[5] = 'زمان ارسال';
	$grid->columnFunctions[5]='ppdate1';
	$grid->columnAccesses[5] = 0;
	$grid->columnHeaders[6] = 'نام تحویل گیرنده';
	$grid->columnHeaders[7] = 'تاریخ ثبت';
	$grid->columnFunctions[7]='ppdate';
	$grid->columnAccesses[7] = 0;
	$grid->columnHeaders[8] = 'وضعیت';
	if ($se->detailAuth('modir'))
	{
		$grid->columnLists[8] = loadVazeat();
		$grid->addFunction = 'add_item';
		$grid->canEdit = TRUE;
		$grid->canDelete = TRUE;
	}
	elseif ($se->detailAuth('super'))
	{
		$grid->columnLists[8] = loadVazeat();
		$grid->addFunction = 'add_item';
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
	}
	else
	{
		$grid->columnFunctions[8] = 'loadVazeat_norm';
		$grid->columnAccesses[8] = 0;
		$grid->canAdd = FALSE;
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
	}
	//$grid->columnFunctions[8] =array(0=>'ارسال نشده',1=>'ارسال شده');	
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
				$("#new_n_sargorooh").hide();
			});
	
		</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
		وسایل جامانده میهمان
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<?php //	echo $combo_hotel;?>
		</div>
			
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<?php echo $out; ?>
		</div>
	</body>
</html>
