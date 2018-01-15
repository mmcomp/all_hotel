
<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	
	$sel_khali = "";
        $sel_grooh = "";
        $sel_kol = "";
        $sel_moeen = "";
        $sel_tafzili = "";
        $sel_tafzili2 = "";
	$sel_tafzili_shenavar="";
        $sel_tafzili_shenavar2 = "";
	$combo=array();
	$combo["بستانکار"]=1;
	$combo["بدهکار"]=-1;
	$show=true;
//	$moshtari_name = "مشتری انتخاب نشده است";
		if(isset($_REQUEST["base_inf"]))
		{
			$table = $_REQUEST["base_inf"];
			if($table=="-1")
                        {
                                $sel_khali = " selected='selected' ";
				$show=false;
                        }

			if($table=="grooh")
			{
				$sel_grooh = " selected='selected' ";
			}
			if($table=="kol")
                        {
                                $sel_kol = " selected='selected' ";
                        }
			if($table=="moeen")
                        {
                                $sel_moeen = " selected='selected' ";
                        }
			if($table=="tafzili")
                        {
                                $sel_tafzili = " selected='selected' ";
                        }
			if($table=="tafzili2")
                        {
                                $sel_tafzili2 = " selected='selected' ";
                        }
			if($table=="tafzilishenavar")
                        {
                                $sel_tafzili_shenavar = " selected='selected' ";
                        }
			if($table=="tafzilishenavar2")
                        {
                                $sel_tafzili_shenavar2 = " selected='selected' ";
                        }
			if ($show==true)
                        {
			$grid = new jshowGrid_new("$table","grid1");
	                $grid->columnHeaders[0] = null;
        	        $grid->columnHeaders[2] = "نام";
                	$grid->columnHeaders[1] = "کد";
			$grid->columnHeaders[3] = "نوع";
			$grid->columnLists[3]=$combo;
	                $grid->intial();
        	        $grid->executeQuery();
                	$out = $grid->getGrid();
			}
		}
		else
		{
			$out = "سطح را انتخاب کنید";	
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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه ارزیابی عملکرد کارکنان شرکت مدیریت تولید نیروگاه‌های گازی خراسان
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<form id="frm2" >
				انتخاب سطح:<select class="inp" name="base_inf" onchange="document.getElementById('frm2').submit();"  >
					<option value="-1" <?php echo $sel_khali ?> ></option>
					<option value="grooh" <?php echo $sel_grooh ?>  >گروه</option>
					<option value="kol" <?php echo $sel_kol ?> >کل</option>
					<option value="moeen" <?php echo $sel_moeen ?>  >معین</option>
					<option value="tafzili" <?php echo $sel_tafzili ?>  >تفضیلی ۱</option>
					<option value="tafzili2" <?php echo $sel_tafzili2 ?>  >تفضیلی ۲</option>
					<option value="tafzilishenavar" <?php echo $sel_tafzili_shenavar ?>  >تفضیلی شناور ۱</option>
					<option value="tafzilishenavar2" <?php echo $sel_tafzili_shenavar2 ?>  >تفضیلی شناور ۲</option>
				</select>
			</form>
			<br/>
			<?php 
				 if ($show==true)
                                        {
						echo $out;
					}
			?>
		</div>
	</body>
</html>
