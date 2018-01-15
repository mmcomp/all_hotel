<?php
    header('Content-Type: text/html; charset=utf-8');
    require_once("../inc/jgmdate.php");
	require_once("../inc/ez_sql.php");
    session_start();
////////////////////////////////////////////////////////////////	
	$CrrentYear=jgmdate("Y");
	$CrrentMonth=jgmdate("m");
	$Crrentday=jgmdate("d");
	$CrrentDay=jgmdate("l");
	$CrrentHour=jgmdate("H");
	$CrrentMinute=jgmdate("i");
	$CrrentSecond=jgmdate("s");
      
    $CurrentDate=$CrrentYear."-".$CrrentMonth."-".$Crrentday;
    $CurrentDateInv=$Crrentday."-".$CrrentMonth."-".$CrrentYear;
    $CurrentTime=$CrrentHour.":".$CrrentMinute.":".$CrrentSecond;
////////////////////////////////////////////////////////////////
    $recevedyear = (isset($_POST['year']))?$_POST['year']:null;
    $recevedmonth = (isset($_POST['month']))?$_POST['month']:null;
    $recevedday = (isset($_POST['day']))?$_POST['day']:null;
    
    if($recevedyear&&$recevedmonth&&$recevedday){
        if(preg_match('/[0-9]*/' , $recevedyear )&&preg_match('/[0-9]*/' , $recevedmonth )&&preg_match('/[0-9]*/' , $recevedday )){
			
			$AccessStack = explode("|",$_SESSION['access']);
			$hasAccess = false;
					
			if($AccessStack){
				if((strpos($_SESSION['access'],"|ALL|")!==false)
					||(strpos($_SESSION['access'],"|ShiftManage|")!==false)){$hasAccess=true;}
				
				if($hasAccess){
					$targetdate = $recevedyear."-".$recevedmonth."-".$recevedday."|";
					$yearExists = $db->get_var("   	SELECT id
													FROM `f48c9489er_calendaroffdates`
													WHERE year='$recevedyear'  ");
					if(!$yearExists){
						$db->query("INSERT INTO `f48c9489er_calendaroffdates`(`id`, `year`       , `offdays`, `x`) 
																	  VALUES (''  ,'$recevedyear',''        ,''  )");
					}
					
					$offs =$db->get_var("   SELECT offdays
											FROM `f48c9489er_calendaroffdates`
											WHERE year='$recevedyear'  ");
					$x = strpos($offs, $targetdate);
					if($x){
						$offs = str_replace($targetdate,"",$offs);
						$db->query("UPDATE `f48c9489er_calendaroffdates` 
									SET offdays='$offs'
									WHERE year='$recevedyear' ");
						echo $offs;
					}else{
						$offs=$offs.$targetdate;
						$db->query("UPDATE `f48c9489er_calendaroffdates` 
									SET offdays='$offs'
									WHERE year='$recevedyear' ");
						echo $offs;
					}
				}else{echo 'خطا : شما دسترسی انجام این عملیات را ندارید !';}
			}else{echo 'خطا : اطلاعات دریافتی صحیح نمی باشد. لطفا صفحه را مجدد بارگذاری کنید و در صورت پابرجا بودن مشکل، مجدد وارد حساب کاربری خود شوید!';}
        }else{echo 'خطا : اطلاعات دریافتی صحیح نمی باشد. لطفا صفحه را مجدد بارگذاری کنید و در صورت پابرجا بودن مشکل، مجدد وارد حساب کاربری خود شوید!';}
    }else{echo 'خطا : اطلاعاتی دریافت نشد ! لطفا صفحه را مجدد بارگذاری کنید .';}
    
?>