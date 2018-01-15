<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$is_admin = FALSE;
        if($se->detailAuth('all'))
                $is_admin = TRUE;
	$user_typ = '';
	$hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);
	$hotel = new hotel_class($hotel_id);
	$d = ((isset($_REQUEST['d']))?$_REQUEST['d']:perToEnNums(jdate("m")));
	$y = ((isset($_REQUEST['y']))?$_REQUEST['y']:perToEnNums(jdate("Y")));
	//$month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
	if($se->detailAuth('garanti'))
	{
		$user_typ = 'garanti';
		$month = array('بهمن','اسفند');
	}
	elseif($se->detailAuth('dafater'))
	{
		$user_typ = 'dafater';
		$month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
	}
	else
		$month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
	$da = audit_class::hamed_pdateBack(jdate("$y/$d/1"));
	$tmp = explode(" ",$da);
	$da = $tmp[0];
	$hotel = new hotel_class($hotel_id);
	$hotel->setRoomJavaScript = TRUE;
	//$out = $hotel->loadRooms($da,$is_admin,'f1');
	$out = $hotel->loadRooms($da,$is_admin,'f1',$user_typ);
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
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script> 
		<script language="JavaScript">
			var res_id;
			var room;
			function f1(reserve_id,room_id)
			{
				//alert(reserve_id+','+room_id);
				res_id  = reserve_id;
				room = room_id;
				if(document.getElementById('openw'))
				{
					$.window({
						title: "جزئیات",
						width: 600,
						height: 400,
						content: $("#window_block8"),
						containerClass: "my_container",
						headerClass: "my_header",
						frameClass: "my_frame",
						footerClass: "my_footer",
						selectedHeaderClass: "my_selected_header",
						createRandomOffset: {x:0, y:0},
						showFooter: false,
						showRoundCorner: true,
						x: 0,
						y: 0,
						url: "gaantinfo.origin.php?reserve_id="+reserve_id+"&room_id="+room_id+"&"
					});
				}
				else
					alert('not');
			}
			function resizeText(multiplier) 
			{
				if (document.body.style.fontSize == "") 
				{
				    document.body.style.fontSize = "1.0em";
				}
				document.body.style.fontSize = parseFloat(document.body.style.fontSize) + (multiplier * 0.2) + "em";
			}
			function resizeDef() 
                        {
                                    document.body.style.fontSize = "0.8em";
                        }
			$(document).ready(function(){
				$(window).scroll(function(inp,inp1){
					//console.log(inp1);
					//console.log('windowHeight = '+$(window).height()+',sc='+$(window).scrollTop());
				});
				$("tr").mouseover(function(evt){
					$(".moveHeader").remove();
					var ht = '<tr class="moveHeader">'+$("#first_tr").html()+'</tr>';
					$(evt.currentTarget).before(ht);
				});
			});
		</script>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>
                </title>
		<style>
			tr:hover
			{
				border:solid 1px red;
			}
		</style>
        </head>
        <body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
                <div align="center">
			<form id="frm1" method="get">
				وضعیت رزرو <?php echo $hotel->name; ?> در :
				سال :
				<select name="y" class="inp" onchange="document.getElementById('frm1').submit();">
				<?php
                                        for($i=1390;$i<=$conf->upYear;$i++)
                                                echo "<option value=\"$i\"".(($i==$y)?"selected=\"selected\"":"").">\n$i\n</option>\n";
                                ?>
				</select>
				<select name="d" class="inp" onchange="document.getElementById('frm1').submit();">
				<?php
					for($i=1;$i<=count($month);$i++)
						echo "<option value=\"$i\"".(($i==$d)?"selected=\"selected\"":"").">\n".$month[$i-1]."\n</option>\n";
				?>
				</select>
				ماه
				<input type="hidden" id="hotel_id" name="hotel_id" value="<?php echo $hotel_id; ?>" />
			</form>
			<br/>
			<br/>
			<input type='button' value='بزرگ نمایی' class='inp' onclick='resizeText(1);' >
                        <input type='button' value='کوچک نمایی' class='inp' onclick='resizeText(-1);' >
			<input type='button' value='حالت پیش فرض' class='inp' onclick='resizeDef();' >
                        <?php
				//echo jdate("F",strtotime($da));
				echo $out;
                        ?>
                </div>
		<div id="openw" >
		</div>
<script>
$(document).ready(function(){

    $("#openw").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "جزئیات",
                width: 500,
                height: 150,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "gaantinfo.php?reserve_id="+res_id
        });
    });

  });
</script>
        </body>
</html>

