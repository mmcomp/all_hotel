<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script>
			function loadMenu(url)
			{
				 window.open(url)
			}
		</script>
		<title>
ثبت خدمات برای میهمان
		</title>
	</head>
	<body>
		<br/>
		<br/>
		<br/>
		<br/>
                <div id="main_div" align="center" style="font-family:tahoma;font-size:12px;" >
		
			<table width="500px" border="0" >
				<tr>
					<td>
						<div class="pointer" onclick="loadMenu('gasht_mehman.php');" >
							<center>
								<div>
									<img src='../img/gasht.png' />
								</div>
						
								خدمات گشت
							</center>
						</div>
						<br/>
					</td>
					<td>
						<div class="pointer" onclick="loadMenu('transfer_mehman.php');" >
							<center>
								<div>
									<img src='../img/transfer.png' />
								</div>
						
								خدمات ترانسفر
							</center>
						</div>
						<br/>
					</td>
					<td>
						<div class="pointer"  onclick="loadMenu('cinema_mehman.php');" >
							<center>
								<div>
									<img src='../img/film.png' />
								</div>
							
								 خدمات سینما
							</center>
						</div>
						<br/>
					</td>
					<td>
						<div class="pointer" onclick="loadMenu('akasi_mehman.php');" >
							<center>
								<div>
									<img src='../img/akaskhane.png' />
								</div>
								خدمات عکاسخانه
							</center>
						</div>
						<br/>
					</td>			
				</tr>
			</table>
			<div class="pointer" onclick="loadMenu('gozaresh_khadamt_mehman.php');" >
				<center>
					<div>
						<img src='../img/gozaresh_kh.png' />
					</div>
					گزارشات
				</center>
			</div>
		</div>
	</body>
</html>
