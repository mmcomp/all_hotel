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
$root="";
?>
	<!DOCTYPE html>
	<html lang="en">

	<head>

		<meta charset="utf-8" />
		<title>صفحه ورود</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
		<meta name="description" content="" />
		<meta name="author" content="" />
		<link rel="shortcut icon" href="<?php echo $root ?>img/favicon.png">
		<!-- STYLESHEETS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/cloud-admin.css" />

		<link href="<?php echo $root ?>font-awesome/css/font-awesome.min.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/fa-style.css" />

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script language="javascript">
			function onEnterpress(e)

			{
				var KeyPress;
				if (e && e.which) {
					e = e;
					KeyPress = e.which;
				} else {
					e = event;
					KeyPress = e.keyCode;
				}
				if (KeyPress == 13) {
					document.getElementById('frm1').submit();
					return false
				} else {
					return true
				}

			}
		</script>
	</head>

	<body class="login">
		<!-- PAGE -->
		<section id="page">
			<!-- HEADER -->
			<header>

			</header>
			<!--/HEADER -->
			<!-- LOGIN -->
			<section id="login" class="visible">
				<div class="container">
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<div class="login-box-plain">
								<div id="logo" style="margin-top:0;">
									<a href="login.php">
<!-- 								<img src="<?php //echo $root ?>img/photo_2016-07-16_16-06-59.jpg" style="margin-bottom:-60px;display:inline;" class="img-responsive" alt="2020 tckh"> -->
										<?php if($is_qeshm){ ?>
										<img src="<?php echo $root ?>img/qeshm.jpg" style="margin-bottom:-60px;display:inline;" class="img-responsive" alt="2020 tckh">
										<?php }else if($is_tourism){ ?>
										<img src="<?php echo $root ?>img/tourism.jpg" style="margin-bottom:-60px;display:inline;" class="img-responsive" alt="2020 tckh">
										<?php }else{ ?>
										<img src="<?php echo $root ?>img/rahad.jpg" style="margin-bottom:-60px;display:inline;" class="img-responsive" alt="2020 tckh">
										<?php } ?>
									</a>
								</div>
								<br/>
								<div class="divide-40"></div>
								<form action="rooms_vaziat.php" id="frm1" method="post">
									<div class="form-group">
										<label for="exampleInputEmail1">نام کاربری</label>
										<i class="fa fa-envelope"></i>
										<input value="" name="user" type="text" class="form-control inp" id="uname" />
									</div>
									<div class="form-group">
										<label for="exampleInputPassword1">گذرواژه</label>
										<i class="fa fa-lock"></i>
										<input value="" name="pass" type="password" class="form-control inp" id="pass" />
										<input name="kelid" id="kelid" type="password" value="186a2" class="inp" style="display:none" />
									</div>
									<div class="form-group">
										<label for="exampleInputPassword1">کلید</label>
										<i class="fa fa-lock"></i>
										<input value="" name="hpass" type="password" class="form-control inp" id="hpass" />
									</div>

									<button onclick="if(document.getElementById('uname').value!='' && document.getElementById('pass').value!=''){document.getElementById('frm1').submit();}else{alert('لطفاً نام کاربری و رمز عبور را وارد کنید');}" class="btn btn-danger">ورود</button>


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
								</form>
								<!-- 																<a href="../online">ورود عمومی</a> -->
<!-- 								</button> -->
								<?php if($is_qeshm){ ?>
<!-- 								<div style="text-align: justify;padding: 5px;border: 2px solid #eaeaea;margin-top: 10px;color: #811d1d;">
کلیه حقوق کپی رایت این سامانه نزد شرکت تجارت گستر گردشگران قشم محفوظ می باشد
این شرکت دارنده مجوز رسمی بشماره 730 / 95 و 360 / 96 از سازمان منطقه آزاد قشم می باشد
								</div> -->
								<?php } ?>
								<?php if($is_tourism){ ?>
								<div style="text-align: center;padding: 5px;border: 2px solid #eaeaea;margin-top: 10px;color: #811d1d;">
موسسه تحقیقات و مطالعات 
									<br/>
									<span style="font-weight:bold">
									متخصصین گردشگری ایران
									</span>
								</div>
								<?php } ?>
<!-- 								<div style="text-align: center;">
									<img src="img/ershad.jpg" style="width: 50%;" />
								</div> -->
							</div>
						</div>
					</div>
				</div>
			</section>
			<!--/LOGIN -->
		</section>
		<!--/PAGE -->
		<!-- JAVASCRIPTS -->
		<!-- Placed at the end of the document so the pages load faster -->
		<!-- JQUERY -->
		<script src="<?php echo $root ?>js/jquery/jquery-2.0.3.min.js"></script>
		<!-- JQUERY UI-->
		<script src="<?php echo $root ?>js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
		<!-- BOOTSTRAP -->
		<script src="<?php echo $root ?>bootstrap-dist/js/bootstrap.min.js"></script>


		<!-- UNIFORM -->
		<script type="text/javascript" src="<?php echo $root ?>js/uniform/jquery.uniform.min.js"></script>
		<!-- CUSTOM SCRIPT -->
		<script src="<?php echo $root ?>js/script.js"></script>
		<script>
			/*
					jQuery(document).ready(function() {		
						App.setPage("login");  //Set current page
						App.init(); //Initialise plugins and elements
					});
				
					function swapScreen(id) {
						jQuery('.visible').removeClass('visible animated fadeInUp');
						jQuery('#'+id).addClass('visible animated fadeInUp');
					}
				*/
		</script>
		<script language="javascript">
			document.getElementById("uname").focus();
		</script>
		<!-- /JAVASCRIPTS -->
	</body>

	</html>