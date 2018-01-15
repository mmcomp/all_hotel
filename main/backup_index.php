<?php
	session_start();
	include_once ("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$pass=((isset($_REQUEST['pass']))?$_REQUEST['pass']:"");
	$user=((isset($_REQUEST['user']))?$_REQUEST['user']:"");
	if (((isset($_SESSION['user_id']) && isset($_SESSION['typ']))))
	{
//	 die("<script>window.location='login.php';</script>");

	}	
	date_default_timezone_set("Asia/Tehran");
	$firstVisit = (isset($_SESSION["login"]) && ($_SESSION["login"] == 1) && isset($_REQUEST["user"]));
	if($firstVisit ||(isset($_SESSION["user_id"]))){	
	function loadUserById($id){
		$out = 'تعریف نشده';
		mysql_class::ex_sql("select fname,lname from user where id=$id",$qq);
		if($r= mysql_fetch_array($qq,MYSQL_ASSOC))
		{
			$out = $r["fname"]." ".$r["lname"];
			
		}
		return $out;
	}
	function isOdd($inp){
		$out = TRUE;
		if((int)$inp % 2==0){
			$out = FALSE;
		}
		return $out;
	}

	if($firstVisit){
		//echo "+++++++first+++++++";
		$is_modir  = FALSE;
		mysql_class::ex_sql("select * from user where user = '".$user."'",$q);
		
		if($r_u = mysql_fetch_array($q,MYSQL_ASSOC)){
			if($pass == $r_u["pass"] ){
				$is_modir =(($r_u["typ"]==0)?0:1);
				$_SESSION["user_id"] = (int)$r_u["id"];
				$_SESSION["typ"] = (int)$is_modir;
			}else{
				die("<script>window.location = 'login.php?stat=wrong_pass&';</script>");
			}
		}else{
			die("<script>window.location = 'login.php?stat=wrong_user&';</script>");
		}
	}
	$user_id = (int)$_SESSION["user_id"];
	if(isset($_SESSION["typ"]) && $_SESSION["typ"]!=""  )
	{
		$is_modir = $_SESSION["typ"];
	}
	$forms = null;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo lang_fa_class::title; ?></title>
    <link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
	<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
    <link href="../css/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
	<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script> 
	<style>
		tr{cursor: pointer;}
	</style>
    <script type="text/javascript" >
	function sendState(inp)
	{
		if(confirm("<?php echo lang_fa_class::change_state_confirm;  ?> "))
		{
			document.getElementById('progress').style.display='';
			window.location ="index.php?state="+inp+"&";
		}
	}
   </script>
</head>
<body>
    <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
    <div id="header">
        
        <h1 align="center" ><a href="#"><?php echo lang_fa_class::title; ?></a></h1>
        <div align="center"><br></br><img  src="../img/logo.gif" alt="" /></div>
    </div><!-- header -->
    
    <div id="main"><div id="main2">	
            <div id="sidebar">
                <h2 align="center" ><?php echo lang_fa_class::all_manegment; ?></h2>
                <div align="center">
	                <table width="95%" border="0">
				
	                	<tr>
	                		<td id="grp_manage" align="center" >
	                			<table>
	                			<tr>
	                				<td align="center">
	                					<img src="../img/agent.png" width="64" ></img>	
	                				</td>
	                			</tr>
	                			<tr>
	                				<th>
									<?php  echo lang_fa_class::grp_user; ?>
									</th>
								</tr>
								</table>
	                		</td>
	                		<td id="user_manage" align="center"  >
	                			<table>
		                			<tr>
		                				<td align="center">
		                					<img src="../img/user-icon.png" width="64" ></img>	
		                				</td>
		                			</tr>
		                			<tr>
		                				<th>
										<?php  echo lang_fa_class::user; ?>
										</th>
									</tr>
								</table>          		
	                		</td>
	                		
					
	                		<td id="bandwidth" align="center" >
	                			<table>
		                			<tr>
		                				<td align="center">
		                					<img src="../img/plane.png" width="75" ></img>	
		                				</td>
		                			</tr>
		                			<tr>
		                				<th>
										<?php  echo lang_fa_class::bandwidth; ?>
										</th>
									</tr>
								</table>    
	                		
	                		</td>
	                	</tr>
				<tr>
					<td id="m_hotel" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/hotel.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::hotel; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>

		<!--			 <td id="h_grooh" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/account.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::grooh; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>-->
					<td id="h_kol" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/kol.png" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::kol; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
					<td id="h_moeen" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/moeen.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::moeen; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>

				</tr>
				<tr>
<!--					<td id="h_moeen" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/moeen.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::moeen; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
					<td id="h_tafzili1" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/tafzili1.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::tafzili1; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
					<td id="h_tafzili2" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/tafzili2.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::tafzili2; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
				</tr>
	                	<tr>
					<td id="h_tafzilishenavar1" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/shenavar1.png" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                             <?php  echo lang_fa_class::shenavar1; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>

					<td id="h_tafzilishenavar2" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/shenavar2.png" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::shenavar2; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>-->
	                		<td id="filter" align="center" >
	                			<table>
		                			<tr>
		                				<td align="center">
		                					<img src="../img/sabt.png" width="64" ></img>	
		                				</td>
		                			</tr>
		                			<tr>
		                				<th>
										<?php  echo lang_fa_class::filter; ?>
										</th>
									</tr>
								</table>	                			                		
	                		</td>
	                	</tr>
				<tr>
					<td id="download" align="center"  >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/gozaresh.png" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                        <?php  echo lang_fa_class::download; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>
                                        </td>
					<td id="sanad_gozaresh" align="center"  >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/total_report.png" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                       گزارش اسناد
                                                                                </th>
                                                                        </tr>
                                                                </table>
                                        </td>
					 <td id="exit" align="center">
                                        <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/Log-Out-icon.png" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                        <?php  echo lang_fa_class::logout; ?>
                                                                                </th>
                                                                        </tr>
                                        </table>
                                        </td>
				</tr>
	                	<tr>
	                		<td  align="center"  >
					 
					 </td>
	                		<td id="progress"  align="center" style="display:none;" >
                                       		 <img src='../img/progress.gif' width='64' ></img>
                                        </td> 
	                	</tr>
	                </table>
                </div>
            </div>  	              
                                
            <div class="clearing">&nbsp;</div>   
    </div></div><!-- main --><!-- main2 -->
    <div id="footer" >
	<center>
        طراحی و ساخت <a href="http://www.gcom.ir/">گستره ارتباطات شرق</a><img src="../img/gcom.png" width="32" >
	</center>
    </div>
<script>
$(document).ready(function(){

    $("#grp_manage").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::grp_user; ?>",
                width: 900,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "daftar.php"
        });
    });

    $("#user_manage").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت کاربران",
		showModal: true,
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "user.php"
	});
    });

    $("#time").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::filter; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "sanad_new.php"
        });
    });

    $("#bandwidth").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "<?php  echo lang_fa_class::bandwidth; ?>",
		width: 1000,
         	height: 500,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 50,
	        y: 30,
		url: "ajans.php"
	});
    });

	$("#m_hotel").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::hotel; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "hotel.php"
        });
    });

	$("#h_grooh").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::grooh; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "grooh.php"
        });
    });

	$("#h_kol").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::kol; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "kol.php"
        });
    });

	$("#h_moeen").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::moeen; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "moeen.php"
        });
    });
	$("#h_tafzili1").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::tafzili1; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "tafzili.php"
        });
    });

	$("#h_tafzili2").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::tafzili2; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "tafzili2.php"
        });
    });
	$("#h_tafzilishenavar1").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::shenavar1; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "tafzili_shenavar.php"
        });
    });

	$("#h_tafzilishenavar2").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::shenavar2; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "tafzili_shenavar2.php"
        });
    });
    $("#filter").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::filter; ?>",
                width: 1000,
                height: 650,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "sanad_new.php"
        });
    });

    $("#download").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "گزارش",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 50,
	        y: 30,
		url: "gozaresh.php"
	});
    });
    $("#sanad_gozaresh").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "گزارش اسناد",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 50,
	        y: 30,
		url: "sanad_gozaresh.php"
	});
    });
    $("#modiryate_isargar").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت وضعیت ایثارگر",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 50,
	        y: 30,
		url: "isargar.php"
	});
    });
    $("#modiryate_sotoohe_arzyabi").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت سطوح ارزیابی",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 100,
	        y: 60,
		url: "level.php"
	});
    });
    $("#modiryate_parameter").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت پارامترهای ارزیابی",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 150,
	        y: 90,
		url: "parameter.php"
	});
    });
    $("#city").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت شهر",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 100,
	        y: 60,
		url: "city.php"
	});
    });
    $("#state").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت استان",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 100,
	        y: 60,
		url: "state.php"
	});
    });
    $("#form_asli").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "فرم ارزشیابی",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		/*url: "variable.php"*/
		url: "arzeshyabi.php"
	});
    });
    //modiryate_masadigh
    $("#modiryate_masadigh").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت مصادیق پارامترهای ارزیابی",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "masadigh.php"
	});
    });    
    //parameter_weight_admin
    $("#parameter_weight_admin").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت وزن تأثیر پارامترهای عمومی",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 200,
	        y: 120,
		url: "vazn.php"
	});
    });
   //  تعریف دوره ارزشیابی  
  $("#dore_arzeshyabi").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت دوره‌ارزشیابی",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 200,
	        y: 120,
		url: "dore_arzeshyabi.php"
	});
    });
	//گزارش  
  $("#gozaresh").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "گزارش",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "gozaresh.php"
	});
    });
 	$("#natije").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "نتیجه",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "natije.php"
	});
    });
    $("#restore").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "بروزرسانی نسخه پشتیبان",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "restore.php"
        });
    });
    $("#changepass").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "تغییر رمز عبور",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "changepass.php"
        });
    });
    $("#backup").click(function () {
	window.open("backup.php");
    });
    $("#exit").click(function () {
    		if(confirm("آیا مایل به خروج هستید؟")){window.location = "login.php?stat=exit&";}
    });

  });
</script>
<?php 
	}
	else
	{
		header("Location: login.php");
	}
?>
</body>
</html>
