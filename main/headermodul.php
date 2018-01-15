<?php
function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
{
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
    
    $interval = date_diff($datetime1, $datetime2);
    
    return $interval->format($differenceFormat);
    
}
$root="";
session_start();
include_once('../kernel.php');
$log_user_id = $_SESSION["user_id"];
mysql_class::ex_sql("select count(`id`) as `t_payam` from `payam` where `rec_user_id`='$log_user_id' and `en`='-1'",$q_payam);
if ($r_payam = mysql_fetch_array($q_payam))
	$showPayam = $r_payam['t_payam'];
else
	$showPayam = 0;
$segment = '-';
$reserve_count = '-';
$cost = ($conf->cost>0)?(int)$conf->cost:30000;
$pay = 0;
$counts = 0;
$last_reserve_id = 0;

$my = new mysqli('localhost',$conf->user,$conf->pass,$conf->db.'-1');
$my->set_charset("utf8");
$sql = "select `reserve_id`,`id` from `hotel_reserve` where `reserve_id`>0 and `segment_done`=0 order by `reserve_id` desc limit 1";
if($result=$my->query($sql)){
	if($r = $result->fetch_assoc()){
		$last_reserve_id = (int)$r['reserve_id'];
	}
}
$seg_url = "http://raha-hotel.com/bank.php?d=".$conf->db."&code=$last_reserve_id&ajax=1";
$seg_out = file_get_contents($seg_url);
$seg_out_arr = explode(',',$seg_out);
if(count($seg_out_arr)==2){
  $segment = $seg_out_arr[0];
	$segment = number_format($segment,0,'',',');
	$counts = $seg_out_arr[1];
}
$user_data = ($is_qeshm)?'Qeshm':(($is_tourism)?'IRTourismExp':'Raha');
if($is_qeshm){
	$user = new user_class($log_user_id);
// 	var_dump($user);
	$user_data = $user->fname.' '.$user->lname;
}
?>
<header class="navbar clearfix navbar-fixed-top" id="header">
	<div class="container">
			<div class="navbar-brand">
				<!-- COMPANY LOGO -->
				<?php if(!$is_qeshm && !$is_tourism){ ?>
 				<a href="index.php" style="color:#ffffff;">
					<img src="<?php echo $root ?>img/photo_2016-10-24_10-20-14.png" alt="رها" class="img-responsive" height="30"  />
				</a>
				<?php } ?>
				<!-- /COMPANY LOGO -->
				<!-- SIDEBAR COLLAPSE -->
				<div id="sidebar-collapse" class="sidebar-collapse btn tip-bottom" data-placement="bottom" data-toggle="tooltip" title="عرض منوها">
					   <i class="fa fa-reorder" data-icon1="fa fa-reorder" data-icon2="fa fa-reorder"></i>
                </div>
				<!-- /SIDEBAR COLLAPSE -->
			</div>
			<!-- BEGIN TOP NAVIGATION MENU -->					
			<ul class="nav navbar-nav pull-left">
				
						<!-- BEGIN NOTIFICATION DROPDOWN -->	
						<li class="dropdown" id="header-notification" data-toggle="tooltip" data-placement="bottom" title="پیام ها">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="fa fa-bell"></i>
								
								
									<?php if($showPayam!=0) echo" <span class=\"badge\">".$showPayam."</span>"; ?>
									
								
								</a>
									<ul class="dropdown-menu notification">
										<li class="dropdown-title">
											<span><i class="fa fa-bell"></i> پیام ها</span>
										</li>
									
								
                                    <?php
            if($showPayam!=0){
                                        mysql_class::ex_sql("select * from `payam` where `rec_user_id`='$log_user_id' and `en`='-1'",$ss);
            
	while ($r = mysql_fetch_array($ss)){
        
        $payam_id = $r['payam_id'];
        mysql_class::ex_sql("select `toz` from `payam_toz` where `id` = '$payam_id' ",$h_id);
        $h_id1 = mysql_fetch_array($h_id);
        $hname = $h_id1['toz'];
        $tarikh = $r['tarikh'];
        echo"<li>
        <a onclick=\"showP('".$hname."')\"  data-toggle=\"modal\">
															<span class=\"label label-success\"><i class=\"fa fa-envelope\"></i></span>
															<span class=\"body\">
																<span style=\"width:16%;overflow:hidden;text-overflow:ellipsis\" class=\"message\">".$hname."</span>
																<span class=\"time\">
																	<i class=\"fa fa-clock-o\"></i>
																	<span>".$tarikh."<span class=\"badge green\">جدید </span> </span>
																</span>
															</span>
														</a>
													</li>
													
        ";}}
        else{
            echo"<li><a><span class=\"body\">
															پیامی وجود ندارد.
														</span></a></li>";
        }
        
    
														
                                                                        
                                                                        ?>
														
													
												
									
									<li class="footer">
										<a target="_blank" href="payam.php"> مشاهده همه پیام ها
                                            <i class="fa fa-arrow-circle-o-left"></i>
                                        </a>
									</li>
								</ul>
							</li>
							<!-- END NOTIFICATION DROPDOWN -->
						
					<!-- BEGIN Error view -->
						<li class="dropdown" id="header-message"  data-toggle="tooltip" data-placement="bottom" title="مشکل در فضای عمومی" >
							<a href="tasisat_tmp.php?omoomi=1&" target="_blank" class="dropdown-toggle">
							<i class="fa fa-gears"></i>
							</a>
						</li>
					<!-- END Error view -->
                
                					<!-- BEGIN Error register  -->
                <li class="dropdown" id="header-message"  data-toggle="tooltip" data-placement="bottom" title="مشکل در اتاق" >
							<a href="tasisat_tmp.php" target="_blank" class="dropdown-toggle">
							<i class="fa fa-exclamation-triangle"></i>
							</a>
						</li>
						
					<!-- END Error register -->
						
				
				
					<!-- BEGIN help  -->
						<li class="dropdown" id="header-message"  data-toggle="tooltip" data-placement="bottom" title="راهنما" >
							<a href="#" target="_blank" class="dropdown-toggle">
							<i class="fa fa-question-circle"></i>
							</a>
						</li>
					<!-- END help -->
				<?php if(!$is_qeshm && !$is_tourism){ ?>
					<!-- BEGIN Segment  -->
 						<li class="dropdown" id="header-message"  data-toggle="tooltip" data-placement="bottom" title="سگمنت - ریال" >
							<a href="http://raha-hotel.com/bank.php?d=<?php echo $conf->db; ?>&code=<?php echo $last_reserve_id; ?>" target="_blank" class="dropdown-toggle">
								<span style="color:#ffb848" id="foo"><?php echo $segment; ?>&nbsp;&nbsp;</span>
								<i class="fa fa-money"></i>
							</a>
						</li> 
					<!-- END Segment -->
				<?php } ?>
				<!-- BEGIN USER LOGIN DROPDOWN -->
				<li class="dropdown user" id="header-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img alt="" src="<?php echo $root ?>img/avatars/home.png" />
						<span class="username"><?php echo $user_data; ?></span>
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu">
						
						<!--<li><a href="#"><i class="fa fa-cog"></i> تنظیمات حساب کاربری</a></li>-->
						<li><a href="#box-setting" data-toggle="modal"><i class="fa fa-eye"></i> امنیت حساب کاربری</a></li>
						<li><a href="login.php?stat=exit&"><i class="fa fa-power-off"></i> خروج</a></li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
			</ul>
			<!-- END TOP NAVIGATION MENU -->
	</div>
</header>


			<script>
				var HIntervall = 1000*60*3;
			
				jQuery(document).ready(function() {
                    $('[data-toggle="tooltip"]').tooltip(); 
                });
					/*
					$(\'#ModalTableErrors\').slimScroll({
						height: \'300px\',
						position: \'left\',
						wheelStep: 100,
						allowPageScroll: true
					});
					$(\'#ModalTableNotifications\').slimScroll({
						height: \'300px\',
						position: \'left\',
						wheelStep: 100,
						allowPageScroll: true
					});
					*/
					
					
				
				function tooltip(e){
					$(e).tooltip("show");  
				}
                function showP(name){
                    $("#ModalTableErrors1").html(name);
                    $('#box-messages').modal('show');   
                }
			</script>

		<!-- Modal : error view -->
			<div class="modal fade" id="error-view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog modal-lg">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close pull-left" data-dismiss="modal" aria-hidden="true">×</button>
					  <h4 class="modal-title">عنوان اشکال</h4>
					 
					</div>
					<div class="modal-body">
						<div style="height:300px;overflow:auto;">
							<div id="ModalTableErrors" >
								
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" >بستن</button>
					</div>
				  </div>
				</div>
			</div>
			<!--/Modal : error view -->			

		<!-- Modal : Messages -->
			<div class="modal fade" id="box-messages" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog modal-lg">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close pull-left" data-dismiss="modal" aria-hidden="true">×</button>
					  <h4 class="modal-title">پیغام</h4>
					 
					</div>
					<div class="modal-body">
						<div style="height:300px;overflow:auto;">
							<div id="ModalTableErrors1" >
								
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" >بستن</button>
					</div>
				  </div>
				</div>
			</div>
			<!--/Modal : Messages -->	
		<!-- Modal : Errors -->
			<div class="modal fade" id="box-errors" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog modal-lg">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close pull-left" data-dismiss="modal" aria-hidden="true">×</button>
					  <h4 class="modal-title">ثبت عیب</h4>
					 
					</div>
					<div class="modal-body">
						<div style="height:300px;overflow:auto;">
							<div id="ModalTableErrors" >
								
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" >بستن</button>
					</div>
				  </div>
				</div>
			</div>
			<!--/Modal : Errors -->			
			
			

<!---------------------------- setting Modal ----------------------------->
<div class="modal fade" id="box-setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabelsetting" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
	  <div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close pull-left" data-dismiss="modal" aria-hidden="true">×</button>
		  <h4 class="modal-title"><?php echo $user_data; ?> | تنظیمات حساب کاربری</h4>
		</div>
		<div class="modal-body" id="setting-modal-body">
		   
		   <div class="box green border">
				<div class="box-title">
					<h4><i class="fa fa-eye"></i>تغییر پسورد</h4>
					
				</div>
				<div class="box-body">
					<table class="table">
						<tbody>
						  <tr>
							<td align="left">پسورد قبلی :</td>
							<td>
								<input id="oldpasstype" class="form-control" maxlength="16" type="password" />
							</td>
						  </tr>
						  <tr>
							<td align="left">پسورد جدید :</td>
							<td>
								<input id="passtype" class="form-control" maxlength="16" type="password" />
							</td>
						  </tr>
						  <tr>
							<td align="left">تکرار پسورد جدید :</td>
							<td>
								<input id="passretype" class="form-control" maxlength="16" type="password" />
							</td>
						  </tr>
						</tbody>
					 </table>
				</div>
			</div>
		   
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
		  <button type="button" class="btn btn-primary pull-left" >ذخیره</button>
		</div>
	  </div>
	</div>
  </div>
<!--------------------------- end setting modal ---------------------------------->
