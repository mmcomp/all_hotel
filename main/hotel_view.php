<?php
/*	session_start();
        include_once("../kernel.php");
	if(!isset($_SESSION['user_id']) || (int)$_SESSION['typ']!=0 || !isset($_REQUEST['hotel_id']))
		die('<script>window.close();</script>');
*/
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);
	$hotel = new hotel_class($hotel_id);
	$d = ((isset($_REQUEST['d']))?$_REQUEST['d']:perToEnNums(jdate("m")));
	$month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
	$da = audit_class::hamed_pdateBack(jdate("Y/$d/d"));
	$tmp = explode(" ",$da);
	$da = $tmp[0];
	$hotel = new hotel_class($hotel_id);
	$out = $hotel->loadRooms($da);
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
                <script type="text/javascript" src="../js/tavanir.js"></script>
                <script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>
                </title>
        </head>
        <body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
                <div align="center">
                        <br/>
                        <br/>
			<form id="frm1" method="get">
				وضعیت رزرو <?php echo $hotel->name; ?> در :
				<select name="d" class="inp" onchange="document.getElementById('frm1').submit();">
				<?php
					for($i=1;$i<=count($month);$i++)
						echo "<option value=\"$i\"".(($i==$d)?"selected=\"selected\"":"").">\n".$month[$i-1]."\n</option>\n";
				?>
				</select>
				ماه
				<input type="hidden" id="hotel_id" name="hotel_id" value="<?php echo $hotel_id; ?>" />
			</form>
                        <?php
				//echo jdate("F",strtotime($da));
				echo $out;
                        ?>
                </div>
        </body>
</html>

