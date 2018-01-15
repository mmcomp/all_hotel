<?php
//	include_once("../kernel.php");
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$msg = '';
	if(isset($_REQUEST['h_id']))
		$h_id = $_REQUEST['h_id'];
	else{
		$h_id = -1;
		$h_ids = array();
		mysql_class::ex_sql("select hotel_id from hotel_daftar where daftar_id = ".$_SESSION['daftar_id'],$q);
		while($r = mysql_fetch_assoc($q)){
			$h_ids[] = $r['hotel_id'];
		}
		$h_id = $h_ids[0];
	}
	$wer2=' and 1=0';
	if($h_id>0)
	{
		global $wer2;
		$selected_hotel=array();
		$str='';
		mysql_class::ex_sql("select * from `hotel_user` where `hotel_id`='$h_id'",$q);
		while($r = mysql_fetch_array($q))
			$selected_hotel[]=$r['user_id'];
		$str=implode(',',$selected_hotel);
		$wer2='and `user_id` in ('.$str.')';
	//echo $wer2;
	}
	function loadName()
        {
                $out=null;
                mysql_class::ex_sql("select `lname`,`fname`,`id` from `user`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
//			$tmp = $r["lname"].$r["fname"];
			$out[$r["fname"]]=(int)$r["id"];
                }
                return $out;
        }
	function loadvazeeat($inp)
        {
                $out=null;
		if ($inp == 1)
		{
			$out = "ورود";
		}
		if ($inp == -1)
                {
                        $out = "خروج";
                }
                return $out;
        }
	function hamed_pdate($str)
        {
                $out=jdate('H:i:s Y/n/j',strtotime($str));
                return $out;
        }
	function hamed_pdateBack($inp)
        {
                $out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
                        $y=(int)$tmp[2];
                        $m=(int)$tmp[1];
                        $d=(int)$tmp[0];
                        if ($d>$y)
                        {
                                $tmp=$y;
                                $y=$d;
                                $d=$tmp;
                        }
                        if ($y<1000)
                        {
                                $y=$y+1300;
                        }
                        $inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out;
        }
	if (isset($_REQUEST["sdate"]))
		$sdate = hamed_pdateBack($_REQUEST["sdate"]);
	else
		$sdate = "";
	if (isset($_REQUEST["edate"]))
        {
                $edate = hamed_pdateBack($_REQUEST["edate"]);
        }
        else
        {
                $edate = "";
        }
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}


////////////////////
	if (isset($_REQUEST["hotel_id_new"]))
                $hotel_id_new = $_REQUEST["hotel_id_new"];
	else
		$hotel_id_new = -1;
	$combo_hotel = "";
	$combo_hotel .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
		$combo_hotel .= "هتل : <select class='inp' id=\"hotel_id\" name=\"hotel_id_new\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
		mysql_class::ex_sql("select * from `hotel` where `id` in $shart order by `name`",$q);
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
	$vazeeat["ورود"] = 1;
	$vazeeat["خروج"] = -1;
	$grid = new jshowGrid_new("user_ip","grid1");
	$grid->width = '95%';
	mysql_class::ex_sql("select `id` from `user` where `user`='mehrdad'",$q);
	if ($r=mysql_fetch_array($q,MYSQL_ASSOC))
		$id_admin = $r["id"];
	
	$grid->whereClause=" `user_id`<>'$id_admin' $wer2 and `tarikh`>='$sdate 00:00:00' and `tarikh`<='$edate 23:59:59' ORDER BY `tarikh` DESC";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="نام";
	$grid->columnHeaders[2]="آدرس IP";
	$grid->columnHeaders[3] = "تاریخ";
	$grid->columnHeaders[4] = "وضعیت";
//	$grid->columnFilters[1] = TRUE;
	$grid->columnLists[1] = loadName();
	$grid->columnFunctions[3]="hamed_pdate";
	$grid->columnLists[4] = $vazeeat;
	$grid->columnFilters[1] = TRUE;
	$grid->columnFilters[4] = TRUE;
	$grid->pageCount=30;	
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
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
		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
                <script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
                <script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
                <script type="text/javascript" src="../js/tavanir.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		
		</title>
		<script type="text/javascript">
                    $(function() {
                        //-----------------------------------
                        // ﺎﻨﺘﺧﺎﺑ ﺏﺍ کﻝیک ﺏﺭ ﺭﻭی ﻉکﺱ
                        $("#datepicker6").datepicker({
                            showOn: 'button',
                            dateFormat: 'yy/mm/dd',
                            buttonImage: '../js/styles/images/calendar.png',
                            buttonImageOnly: true
                        });
                    });
                    $(function() {
                        //-----------------------------------
                        // ﺎﻨﺘﺧﺎﺑ ﺏﺍ کﻝیک ﺏﺭ ﺭﻭی ﻉکﺱ
                        $("#datepicker7").datepicker({
                            showOn: 'button',
                            dateFormat: 'yy/mm/dd',
                            buttonImage: '../js/styles/images/calendar.png',
                            buttonImageOnly: true
                        });
                    });
                function sbtFrm()
                {
                        document.getElementById('frm1').submit();
                }
                </script>

	</head>
	<body>
		
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<center>
			<?php echo $combo_hotel; ?>
			</center>
			<form id="frm1" method="GET">
				<label>از تاریخ</label>
                                <input class="inp" type="text" name="sdate" id="datepicker6" value="<?php echo ((isset($_GET["sdate"]))?$_GET["sdate"]:"")  ?>" readonly="readonly" >
                                <label>تا تاریخ</label>
                                <input class="inp" type="text" name="edate" id="datepicker7" value="<?php echo ((isset($_GET["edate"]))?$_GET["edate"]:"")  ?>" readonly="readonly">
				<input class="inp" type="hidden" name="h_id" value="<?php echo $h_id;?>" readonly="readonly">
                                <input class="inp" type="button" value="جستجو" onclick="sbtFrm();"  >
                        </form>
			<?php echo $msg; ?>
			<br/>
			<?php echo $out;  ?>
		</div>
	</body>
</html>
