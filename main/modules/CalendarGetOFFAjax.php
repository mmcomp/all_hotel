<?php
    header('Content-Type: text/html; charset=utf-8');
// 	require_once("../inc/ez_sql.php");
	require_once("../inc/jdf.php");
    session_start();
///////////////////////////////////////////////////////////////
	$AccessStack = explode("|",$_SESSION['access']);
	$hasAccess = true;
			
	if($AccessStack){
		if((strpos($_SESSION['access'],"|ALL|")!==false)
			||(strpos($_SESSION['access'],"|ShiftManage|")!==false)){$hasAccess=true;}
		
		if($hasAccess){
				
			$year=jdate('o','','','','en');
			$off=array();
			$count=0;
			$offs =$db->get_results("   SELECT offdays
									    FROM `f48c9489er_calendaroffdates`
									");
			if($offs){
				$answer = null;
				foreach($offs as $off){
					$answer = $answer.$off->offdays;
				}
				echo $answer;
			}else{
				/*
				for($month=1;$month<=12;$month++){
					if($month<7){
						for($day=1;$day<=31;$day++){
							$x = jdate('w',jmktime(0,0,0,$month,$day,$year));
							if($x=="۶"){
								$count++;
								// اگر به صورت 1394-06-07 میخواهید uncommnet خط پایین را پاک کنید
								//if($day<10){$off[$count]="".$year."-0".$month."-0".$day."";}
								//else{$off[$count]="".$year."-0".$month."-".$day."";}
								$off[$count]="".$year."-".$month."-".$day."";
							}
						}  
					}else if($month>6){
						for($day=1;$day<31;$day++){
							$x = jdate('w',jmktime(0,0,0,$month,$day,$year));
							if($x=="۶"){
								$count++;
								// اگر به صورت 1394-06-07 میخواهید uncommnet خط پایین را پاک کنید
								//if($day<10&&$month<10){$off[$count]="".$year."-0".$month."-0".$day."";}
								//else if($day<10&&$month>9){$off[$count]="".$year."-".$month."-0".$day."";}
								//else if($day>9&&$month<10){$off[$count]="".$year."-0".$month."-".$day."";}
								//else if($day>9&&$month>9){$off[$count]="".$year."-".$month."-".$day."";}
								$off[$count]="".$year."-".$month."-".$day."";
							}
						}  
					}
					
				}
				foreach($off as $i){
					$offs.=$i."|";
				}
				$db->query("INSERT INTO `f48c9489er_calendaroffdates`(`id`, `year`, `offdays`, `x`) 
															VALUES ( '' ,'$year', '$offs'  , '' ) ");
				echo $offs; */
			}
		}else{echo 'خطا : شما دسترسی انجام این عملیات را ندارید !';}
	}else{echo 'خطا : اطلاعات دریافتی صحیح نمی باشد. لطفا صفحه را مجدد بارگذاری کنید و در صورت پابرجا بودن مشکل، مجدد وارد حساب کاربری خود شوید!';}
?>