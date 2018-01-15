<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadBes($inp)
	{
		$inp = (int)$inp;
		$out = 0;
		mysql_class::ex_sql("select * from `sanad` where `id`='$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$mab = (int)$r["typ"] * (int)$r["mablagh"];
			$out =(($mab>0)?$mab:"---");
			$GLOBALS["jam_bes"] = $GLOBALS["jam_bes"] + (int)$out;
			$GLOBALS["jam_man"] = $GLOBALS["jam_bed"] - $GLOBALS["jam_bes"];
		}
		return monize($out);
	}
	function loadBed($inp)
	{
                $inp = (int)$inp;
                $out = 0;
                mysql_class::ex_sql("select * from `sanad` where `id`='$inp'",$q);
                if($r = mysql_fetch_array($q))
                {
			$mab = (int)$r["typ"] * (int)$r["mablagh"];
			$out =(($mab<0)?abs($mab):"---");
			$GLOBALS["jam_bed"] = $GLOBALS["jam_bed"] + abs((int)$out);
 		}
		return monize($out);
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

                return $out." 12:00:00";
	}
	function loadReserve($inp)
	{
		$out="---";
		$color="blue";
		mysql_class::ex_sql("select `id` from `reserve` where `sanad_id`='$inp'",$q);
		if (mysql_num_rows($q)>0)
		{
			$out="<u><span style=\"color:$color;cursor:pointer;\" onclick=\"wopen('sanad_cia.php?sanad=$inp&','',800,300);\" >مشاهده </span></u>";
		}
		return $out;
	}
	function loadTozih($inp)
	{
		$inp = (int)$inp;
		$out="---";
		$color="blue";
		mysql_class::ex_sql("select * from `reserve` where `sanad_id`='$inp'",$q);
		if ($r = mysql_fetch_array($q))
		{
			$out=$r["family"]." "."تعداد نفرات"." ".$r["nafarat"]." "."نفر"." "."مدت اقامت"." ".$r["shab"]."شب "."از تاریخ:".hamed_pdate($r["aztarikh"])."تا تاریخ:".hamed_pdate($r["tatarikh"]);
		}
		else
		{
			mysql_class::ex_sql("select `tozihat` from `sanad` where `id`='$inp'",$q);
			if ($r = mysql_fetch_array($q))
			{
				$out = $r["tozihat"];
			}
		}
		return $out;
	}
	function hamed_pdate($inp)
	{
		return audit_class::hamed_pdate($inp);
	}
	function loadMoeen()
	{
		$out = array();
		mysql_class::ex_sql("select `id`,`name` from `moeen`",$q);
		while($r = mysql_fetch_array($q))
			$out[$r['id']]=$r['name'];
		return $out;
	}
	function loadUser($user_id)
	{
		$user_id = (int)$user_id;
		$out = new user_class($user_id);
		$out = $out->fname . ' ' . $out->lname;
		$out = (($out==' ')?'نامشخص':$out);
		return($out);
	}
	function loadFactor($inp)
	{
		$out= "<a href=\"new_factor.php?anbar_factor_id=$inp&\"  target=\"_blank\" >$inp</a>";
		return $out;
	}
	$anbar_factor_id = (isset($_REQUEST['anbar_factor_id']) && (int)$_REQUEST['anbar_factor_id']!=0 )?(int)$_REQUEST['anbar_factor_id']:-1;
	$sdate = (isset($_REQUEST['sdate']) && $_REQUEST['sdate']!='' )?hamed_pdateBack($_REQUEST['sdate']):date("Y-m-d");
	$sdate = explode(' ',$sdate);
	$sdate = $sdate[0];
	$edate = (isset($_REQUEST['edate']) && $_REQUEST['edate']!='' )?hamed_pdateBack($_REQUEST['edate']):date("Y-m-d");
	$edate = explode(' ',$edate);
	$edate = $edate[0];
	$grid = new jshowGrid_new("anbar_factor","grid1");
	if($anbar_factor_id>0)
		$wer = "`id`=$anbar_factor_id";
	else
		$wer = "DATE(`tarikh_resid`)>='$sdate' and DATE(`tarikh_resid`)<='$edate'";
	//$grid->query = $new_query;//." order by `kol_name`,`moeen_name`";
	$grid->whereClause = "`anbar_typ_id`=4 and $wer";
	$grid->columnHeaders[0] = 'شماره فاکتور';
	$grid->columnFunctions[0] = 'loadFactor';
	$grid->list2 = TRUE;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = null;
	$grid->columnHeaders[3] = 'توضیحات فاکتور';
	$grid->columnHeaders[4] = 'حساب معین';
	$grid->columnLists[4] = loadMoeen();
	$grid->columnHeaders[5] = 'تاریخ';
	$grid->columnFunctions[5] = 'hamed_pdate';
	$grid->columnHeaders[6] = null;
	$grid->columnHeaders[7] = 'کاربر ثبت کننده';
	$grid->columnFunctions[7] = 'loadUser';
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
		<link type="text/css" href="../css/style.css" rel="stylesheet" />
		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		گزارش فاکتور های صادر شده		
		</title>
		<script type="text/javascript">
		function sbtFrm()
		{
			document.getElementById('check').value=2;
			document.getElementById('frm1').submit();
		}
		function kol_submit()
		{
			document.getElementById('frm1').submit();
		}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#edate").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
			$(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#sdate").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
	    	</script>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<form method="POST" name="frmtedad" id ="frmtedad">
        	<input name="txttedad" id="txttedad" type="hidden" value="1"/>
	        </form>

		<div align="center">
			<br/>
			<form id="frm1" method="GET">
				<table>
					<tr>
						<td >
						<label>شماره فاکتور</label>
						</td>
						<td><input class="inp" type="text" class="inp" name="anbar_factor_id" id="anbar_factor_id" ></td>
						<td >
						<label>از تاریخ</label>
						</td>
						<td >
						<input class="inp" type="text" name="sdate" id="sdate" value="<?php echo ((isset($_REQUEST['sdate']))?$_REQUEST['sdate']:''); ?>"  >
						</td>
						<td>
                                                <label>تا تاریخ</label>
						</td>
						<td>
						<input class="inp" type="text" name="edate" id="edate" value="<?php echo ((isset($_REQUEST['edate']))?$_REQUEST['edate']:''); ?>" >
                                                </td>
						<td>
                                                <input class="inp" type="button" value="جستجو" onclick="sbtFrm();"  >
						<input type="hidden" name="check" id="check" value="-1"  > 
                                                </td>
					</tr>
				</table>		
			</form>
			<br/>
			<?php echo $out;  ?>
		</div>
		<script>
/*
			var bed = parseInt(document.getElementById('jam_bed').value,10);
			var bes = parseInt(document.getElementById('jam_bes').value,10);
			var tafazol = bed-bes;
			var stat = ((tafazol>0)?"بدهکار":"بستانکار");
			if(tafazol == 0)
				stat = "";
			document.getElementById('jam_bed1').innerHTML="&nbsp;"+FixNums(monize2(bed))+"&nbsp;&nbsp;";
			document.getElementById('jam_bes1').innerHTML="&nbsp;"+FixNums(monize2(bes))+"&nbsp;&nbsp;";
			document.getElementById('tafazol').innerHTML ="&nbsp;"+FixNums(monize2(Math.abs(tafazol)))+" "+stat;
*/
		</script>
	</body>
</html>
