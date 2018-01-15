<?php
	session_start();
	include_once("../kernel.php");
	function loadHesab($tbname,$upper_id,$upper_v = -1)
	{
		$out = array();
		$upper_tb = substr($upper_id,0,-3);
		mysql_class::ex_sql("select * from `$tbname` ".(($upper_v == -1)?"":" where `$upper_id` = '$upper_v' order by `name`"),$q);
		while($r = mysql_fetch_array($q))
		{
			$out[] = array("id"=>(int)$r["id"],"name"=>$r["name"]);
		}
		return($out);
	}
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$refPage = (isset($_REQUEST["refPage"])?$_REQUEST["refPage"]:'sanad_new.php');
	if (!isset($_REQUEST["sel_id"]))
			die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
        $hesab  = $conf->hesabKol();  
	if(isset($_REQUEST['gridName'])) 
		$gridName = $_REQUEST['gridName'];
        foreach($hesab as $meghdar=>$value)
                if($value==null)
                        unset($hesab[$meghdar]);
	$ta_name =substr($meghdar,0,-3);
	$out = "";
	$p_tb = "";
	$p_val = -1;
	$postBack = FALSE;
	$action = "";
	if(isset($_REQUEST["tb"]))
	{
		$postBack = TRUE;
		$p_val = $_REQUEST["val"];
		$s_tb = substr($_REQUEST["tb"],0,-3);
	}
	foreach($hesab as $meghdar=>$value)
        {
		$ta_name =substr($meghdar,0,-3);
		$var_tb = ((isset($_REQUEST[$meghdar]))?(int)$_REQUEST[$meghdar]:-1);
		$out .= "<select class=\"inp\"  id=\"$meghdar\" name=\"$meghdar\" onchange=\"selectTb('$meghdar',this);\">\n";
		if($postBack && ($p_tb == $s_tb))
		{
			$tmp = loadHesab($ta_name,$p_tb."_id",$p_val);
		}
		else
		{
			$tmp = loadHesab($ta_name,$p_tb."_id",-1);
		}
		$out .= "<option value = \"-1\" >\n \n</option>\n";
		for($i = 0;$i<count($tmp);$i++)
		{
			$out .= "<option value=\"".$tmp[$i]["id"]."\" ".(($tmp[$i]["id"]===$var_tb)?"selected=\"selected\"":"").">\n".$tmp[$i]["name"]."\n</option>\n";
		}
		$out .= "</select>\n";
		$p_tb = $ta_name;
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

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
			انتخاب حساب
		</title>
		<script language="javascript">
			function selectTb(tb,obj)
			{
				var val = obj.options[obj.selectedIndex].value;
				document.getElementById("tb").value = tb;
				document.getElementById("val").value = val;
				document.getElementById("selfrm").submit();
			}
			function sendBack()
			{
				var combs = document.getElementsByTagName("select");
				var ok = true;
				for(var i=0;i < combs.length;i++)
				{
					if(combs[i].selectedIndex<=0)
						ok = false;
				}
				if(ok)
				{
					document.getElementById("selfrm").action = "<?php echo $refPage; ?>";
					document.getElementById("selfrm").submit();
				}
				else
				{
					alert('لطفاً حساب را بطور کامل انتخاب کنید');
				}
			}
		</script>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<form id="selfrm" method="POST" <?php echo $action; ?>>
				<h2>انتخاب حساب جهت سند مورد نظر</h2>
				<br/>
				<?php echo $out;  ?>
				<input type="button" class="inp"  value="انتخاب" onclick="sendBack();" />
				<input id="tb" name="tb"  type="hidden" />
				<input id="val" name="val" type="hidden" />
				<input type="hidden" id="sel_id" name="sel_id" value="<?php echo ((isset($_REQUEST["sel_id"]))?(int)$_REQUEST["sel_id"]:"-1"); ?>" />
				<input type="hidden" id="form_shomare_sanad" name="form_shomare_sanad" value="<?php echo ((isset($_REQUEST["form_shomare_sanad"]))?(int)$_REQUEST["form_shomare_sanad"]:"-1"); ?>" />
				<input type="hidden" id="tedad" name="tedad" value="<?php echo ((isset($_REQUEST["tedad"]))?(int)$_REQUEST["tedad"]:"-3"); ?>" />
				<input type="hidden" id="refPage"  name="refPage" value="<?php echo $refPage; ?>" />
				<?php 
				if (isset($_REQUEST['pageSelector']) && isset($_REQUEST['gridName']) ) 
					echo "<input type='hidden' id='pageSelector'  name='pageSelector' value='".$_REQUEST['pageSelector']."' >";
				if (isset($_REQUEST['pageCount']) && isset($_REQUEST['gridName']) ) 
					echo "<input type='hidden' id='pageCount_$gridName'  name='pageCount_$gridName' value='".$_REQUEST['pageCount']."' >";

				?>
			</form>
		</div>
	</body>
</html>
