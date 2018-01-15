<?php
	session_start();
	include_once ("../kernel.php");
	if(isset($_SESSION["user_id"]))
	{
        $user1_id = $_SESSION["user_id"];
		$user1 = new user_class((int)$user1_id);	
		$user1->sabt_khorooj();
	}
	session_destroy();
	session_start();
	$_SESSION["login"] = "1";
	include_once('../kernel.php');
?>
<html>
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	        <title><?php echo $conf->title; ?> </title>
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
		<link type="text/css" href="../css/style.css" rel="stylesheet" />	
		<style>
		</style>	
		<script language="javascript">
			function onEnterpress(e)

				{
				    var KeyPress  ;
				    if(e && e.which)
				    {
					e = e;		     
					KeyPress = e.which ;
				    }

				    else
				    {
					e = event;
					KeyPress = e.keyCode;
				    }
				    if(KeyPress == 13)
				    {
					document.getElementById('frm1').submit();
					return false     
				    }
				    else
				    {
					return true
				    }

				}
			
		</script>
	</head>
	<body dir="rtl"  style="color:#000000;background:#ddd">
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="../help/help_login.php.html" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<form action="index.php" id="frm1" method="post">
		<center>
			<br/>
			<br/>
			<br/>
			<br/>
			<br/>
			<table border="0" style="vertical-align:center;background-image: url('../img/login_bg.png');font-size:12px;" cellpadding="0" cellspacing="0" width="342px" height="232px">
				<tr>
					<td colspan="3">
						&nbsp;
					</td>
				</tr>
				<tr>
				<td align="center" colspan="3" >
					<b><?php echo $conf->title; ?></b>
					<br/>
					v3.02
				</td>								
<!--
				<td>
					&nbsp;
				</td>								
				<td>
					&nbsp;
				</td>
-->
				</tr>
				<tr>
				<td rowspan="3">
					<img src="../img/Login.png" width="100px" />
				</td>								
				<td style="text-align:left;">
					نام کاربری : &nbsp;&nbsp;
				</td>
				<td>
					<input name="user" id="uname" type="text" value="" class="inp" style="width:117px;"  >
				</td>
				</tr>
				<tr>
<!--
					<td>
						&nbsp;
					</td>
-->
					<td style="text-align:left;">
						گذرواژه : &nbsp;&nbsp;
					</td>
					<td >
						<input name="pass" id="pass" type="password" value="" class="inp" style="width:117px;" >
					</td>
				</tr>
				<tr style="display:none;" >
<!--
					<td>
						&nbsp;
					</td>
-->
					<td style="text-align:left;">
						کلید : &nbsp;&nbsp;
					</td>
					<td >
						<input name="kelid" id="kelid" type="password" value="186a2" class="inp" style="width:117px;" />
					</td>
				</tr>
				<tr>
					<td align="left">
						<input type="submit" value="ورود" class="inp" style="display:none;"/>
						<img src="../img/enter.png" alt="ورود" style="cursor:pointer;" onclick="if(document.getElementById('uname').value!='' && document.getElementById('pass').value!=''){document.getElementById('frm1').submit();}else{alert('لطفاً نام کاربری و رمز عبور را وارد کنید');}"/>
					</td>
					<td colspan="2" align="center">
						<?php 
							if(isset($_REQUEST["stat"])){
								switch($_REQUEST["stat"]){
									case "wrong_user":
									case "wrong_pass":						
										echo "<span style=\"color:#C00000;\">نام کاربری یا رمز عبور اشتباه است</span>";						
										break;
									case "session_error":			                        
			                        				echo "<span style=\"color:#C00000;\">نشست کاربری شما خاتمه یافته است ، لطفاً مجدداً وارد شوید</span>";			                        
										break;
									case "exit":
										
			                        				echo "<span style=\"color:#C00000;\">خروج با موفقیت انجام شد</span>";
										break;
								}
							}
						?>
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
					<td colspan="2">
						&nbsp;
					</td>
				</tr>
			</table>
		
			<br/><br/><br/>
			<span>طراحی و ساخت گستره ارتباطات شرق <a target="_blank" href="http://www.gcom.ir/" >www.gcom.ir</a> </span>
		</center>
		</form>
		<script language="javascript">
			document.getElementById("uname").focus();
		</script>
	</body>
</html>
