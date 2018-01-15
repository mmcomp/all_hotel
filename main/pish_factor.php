<?php
	session_start();
	include_once('../kernel.php');
//	var_dump($_SESSION);
	//if(!isset($_SESSION['user_id']))
                //die(lang_fa_class::access_deny);
        //$se = security_class::auth((int)$_SESSION['user_id']);
        //if(!$se->can_view)
                //die(lang_fa_class::access_deny);
	$moshtari = "";
	$name = "";
        $aztarikh = "";
        $mablagh = 0;
	if (isset($_SESSION['moshtari_id']))
               	$moshtari_id = $_SESSION['moshtari_id'];
        else
               die(lang_fa_class::access_deny);
	$out = "";
	$moshtari = new moshtari_class($moshtari_id);
	$name = $moshtari->name;
        $aztarikh = $moshtari->aztarikh;
//	$aztarikh = pay_class::getPishFactorDate($aztarikh,$moshtari->tedadpardakhti);
//        $mablagh =(int) $moshtari->mablagh;
//	$bedehi = $_SESSION["bedehi"];//مقدار بدهس ؟
	$t_pardakhti = $moshtari->tedadpardakhti;
	$mablagh = $moshtari->mablagh;
	$today = date("Y-m-d H:i:s");
//echo $aztarikh;
	$diff = strtotime($today) - strtotime($aztarikh);
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $modat_gharardad = $months;
//echo $modat_gharardad;
                //      $sharj_mablagh = $modat_gharardad * $mablagh;
                //      $tedad_pardakht = $sharj_mablagh / $mablagh;
	$aztarikh = pay_class::getPishFactorDate($aztarikh,$moshtari->tedadpardakhti);
        $bedehi = ($modat_gharardad - $t_pardakhti)+1;
        if ($bedehi > 0)
        	$matn_sharj =(int) $bedehi * $mablagh;
        else
        	$matn_sharj = 0;
	if($bedehi <= 0)
		$out = 'مبلغ قابل پرداختی موجود نیست';
	else
	{
		$out = '
		<h2>پیش فاکتور پرداخت شارژ معوقه سرور</h2>
		<br/>
		<br/>
		<table width="80%" border="1px" style="border-style:solid;border-color:#000000;">
			<tr ><td >نام</td><td>تاریخ آخرین پرداختی</td><td>مبلغ</td></tr>
			<tr><td>'.$name.'</td><td>'.$aztarikh.'</td><td>'.$matn_sharj.'</td></tr>
		</table>
		<br/>
		<br/>
		
		<form id="frm1" onsubmit="document.getElementById(\'pardakht\').disabled = true;" >
			<input type="hidden" id="mablagh" name="mablagh" value="'.$matn_sharj.'"/>
			<input type="hidden" id="moshtari_id" name="moshtari_id" value=" '.$moshtari_id.'"/>
			<input class="inp" type="submit" value="انتقال به بانک" id="pardakht" />
			<input class="inp" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />
		</form>
';
		if (isset($_REQUEST["moshtari_id"]))
		{
			$moshtari_id = $_REQUEST["moshtari_id"];
			if (isset($_REQUEST["mablagh"]))
			{
				$mablagh_pardakhti = $_REQUEST["mablagh"];
				$id_pardakhti = pardakht_class::add($moshtari_id,$today,$mablagh_pardakhti);
				$pay_code = pay_class::pay((int)$id_pardakhti,$mablagh_pardakhti);
				$tmp = explode(',',$pay_code);
				if(count($tmp)==2 && $tmp[0]==0)
				{
					$banck_code = $tmp[1];
					$out = "<script language='javascript' >postRefId('$banck_code');</script>";
				}
				else
					$out ="در ارتباط با بانک مشکلی پیش آمده است <br/><input class='inp' type='button' value='بازگشت' onclick='window.location=\"index.php\";' />";
			}
		}
	}
		
?>
<html>
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	        <title>پیش فاکتور </title>
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
		<link type="text/css" href="../css/style.css" rel="stylesheet" />	
		<style>
		</style>
		<script language="javascript" >
			function postRefId (refIdValue)
			{
				var form = document.createElement("form");
				form.setAttribute("method", "POST");
				form.setAttribute("action", "<?php echo $conf->mellat_payPage; ?>");         
				form.setAttribute("target", "_self");
				var hiddenField = document.createElement("input");              
				hiddenField.setAttribute("name", "RefId");
				hiddenField.setAttribute("value", refIdValue);
				form.appendChild(hiddenField);
				document.body.appendChild(form);         
				form.submit();
				document.body.removeChild(form);
			}	
		</script>	
	</head>
	<body dir="rtl"  style="color:#000000;background:#b5d3ff">

		<center>
		<br/>
		<br/>
		<?php echo $out;?>
	</body>
</html>
