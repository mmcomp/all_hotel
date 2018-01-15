<?php
        session_start();
        include_once("../kernel.php");
	function loadGrps($grps,$selected=-1)
	{
		$out = '';
		$selected = (int)$selected;
		foreach($grps as $id=>$name)
			$out .= "<option value='$id' ".(($id==$selected)?"selected":'').">$name</option>\n";
		return($out);
	}
	$grps = array();
	$my = new mysql_class;
	$my->ex_sql("select id,name from grop order by name",$q);
	while($r = mysql_fetch_array($q))
		$grps[(int)$r['id']] =  $r['name'];
	$srcGrp = -1;
	$desGrp = -1;
	if(isset($_REQUEST['srcGrp']))
	{
		$srcGrp = (int)$_REQUEST['srcGrp'];
		$desGrp = (int)$_REQUEST['desGrp'];
		$fraseCopy = (isset($_REQUEST['fraseCopy']) && $_REQUEST['fraseCopy']="yes");
		$q = null;
		$my->ex_sql("select * from access where group_id = $srcGrp and is_group=1",$q);
		while($r = mysql_fetch_array($q))
		{
			echo "insert into access (page_name,group_id,is_group) values ('".$r['page_name']."',$desGrp,1)\n";
			$ln = $my->ex_sqlx("insert into access (page_name,group_id,is_group) values ('".$r['page_name']."',$desGrp,1)",FALSE);
			$acc_id = mysql_insert_id($ln);
			mysql_close($ln);
			if($fraseCopy)
			{
				$qdet = null;
				$my->ex_sql("select frase from access_det where acc_id = ".(int)$r['id'],$qdet);
				while($rdet = mysql_fetch_array($qdet))
				{
					$my->ex_sqlx("insert into access_det (acc_id,frase) values ($acc_id,'".$rdet['frase']."')");
					echo "insert into access_det (acc_id,frase) values ($acc_id,'".$rdet['frase']."')\n";
				}
			}
		}
		die();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
                <link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
                <script type="text/javascript" src="../js/jquery/jquery.js"></script>
                <script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
                <script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
			کپی گروه کاربری
		</title>
		<script>
			function transGrp()
			{
				var srcGrp = $("#srcGrp").val();
				var desGrp = $("#desGrp").val();
				var fraseCopy = $("#fraseCopy").attr("checked")?"yes":"no";
				$.get("copyGropAccess.php",{"srcGrp":srcGrp,"desGrp":desGrp,"fraseCopy":fraseCopy},function(result){
					console.log(result);
				});
			}
		</script>
	</head>
	<body dir = "rtl">
		<select id="srcGrp">
			<?php echo loadGrps($grps,$srcGrp); ?>
		</select>
		مقصد : 
		<select id="desGrp">
			<?php echo loadGrps($grps,$desGrp); ?>
		</select>
		کپی کلیدها
		<input id="fraseCopy" type="checkbox" value="yes" />
		<button onclick="transGrp();">
			انتقال دسترسی
		</button>
	</body>
</html>
