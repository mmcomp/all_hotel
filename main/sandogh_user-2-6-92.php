<?php
//	include_once("../kernel.php");
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadUser()
        {
                $out=null;
                mysql_class::ex_sql("select `fname`,`lname`,`id`,`daftar_id` from `user` where `user`<>'mehrdad' order by `fname`,`lname`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$daftar = new daftar_class($r["daftar_id"]);
                        $out[$r["fname"].' '.$r['lname'].'('.$daftar->name.')']=(int)$r["id"];
		}
                return $out;
        }
	function loadSandogh()
        {
                $out=null;
		$hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
		if($hotel_id>0)
		{
			$san = hotel_class::getSondogh($hotel_id,FALSE);
			$out = $san;
		}
                return $out;
        }
	function loadHotels($hotel_id)
	{
		$out = '<select name="hotel_id" id="hotel_id" class="inp" onchange="filter_frm();" ><option value="-1" ></option>'."\n";
		$hot = hotel_class::getHotels();
		for($i=0;$i<count($hot);$i++)
			$out .="<option ".(($hotel_id==$hot[$i]['id'])?'selected="selected"':'')." value='".$hot[$i]['id']."' >".$hot[$i]['name']."</option>\n";
		$out .='</select>';
		return $out;
	}
	
	$hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
	$grid = new jshowGrid_new("sandogh_user","grid1");
	$grid->setERequest(array('hotel_id'=>$hotel_id));
	$wer = '1=0';
	if($hotel_id>0)
	{
		$tmp = implode(',',hotel_class::getSondogh($hotel_id));
		//var_dump(hotel_class::getSondogh($hotel_id));
		if($tmp!='')
			$wer = " `sandogh_id` in ($tmp)";
	}	
	$grid->whereClause = $wer;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="نام کاربر";
	$grid->columnHeaders[2]="نام صندوق";
	$grid->columnLists[1]=loadUser();
	$sandogh = loadSandogh();
	$grid->columnLists[2]= $sandogh ;
	$grid->canAdd = ($hotel_id>0 && $sandogh!=null);
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

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
دسترسی فرانت آفیس
		</title>
		<script type="text/javascript" >
			function filter_frm()
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
		<div align="center" style="margin:10px;padding:5px;" >
			<form id="frm1" >
					نام هتل:
				<?php echo loadHotels($hotel_id); ?>
			</form>
			<?php echo $out;  ?>
		</div>
	</body>
</html>
