<?php 
function canShow($page){
	global $se;
	$pages= $se->pages;
	$upages=$se->upages;
	$out = in_array($page,$pages);
	if(!$out){
		$out = in_array($page,$upages);
	}
	return $out;
}
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);
?>
<div id="sidebar" class="sidebar">
	<div class="sidebar-menu nav-collapse">
		<!-- SIDEBAR MENU -->
		<ul>
			<li class="">
				<a href="rooms_vaziat.php">
					<i class="fa fa-tachometer fa-fw"></i> <span class="menu-text">صفحه اصلی</span>
					<span class="selected"></span>
				</a>
			</li>
			<?php if(canShow('reserve1.php')){?>
			<li class="">
				<a target="_blank" href="reserve1.php">
					<i class="fa fa-bell fa-fw"></i> <span class="menu-text">رزرو هتل</span>
					<span class="selected"></span>
				</a>
			</li>
			<?php } ?>
			<?php if(canShow('sanad_new.php') || canShow('kol.php') || canShow('moeen.php') || canShow('sanad_new_daftar.php')){?>
			<li class="has-sub">
				<a href="javascript:;" class="" id="requests-menu">
					<i class="fa fa-money fa-fw"></i><span class="menu-text"> حسابداری </span>
					<span class="arrow"></span>
				</a>
				<ul class="sub">
					<?php if(canShow('sanad_new.php')){?>
					<li><a class="" target="_blank" href="sanad_new.php"><span class="sub-menu-text">ثبت سند</span></a></li>
					<?php } ?>
					<?php if(canShow('kol.php')){?>
					<li><a class="" target="_blank" href="kol.php"><span class="sub-menu-text">حساب کل</span></a></li>
					<?php } ?>
					<?php if(canShow('moeen.php')){?>
					<li><a class="" target="_blank" href="moeen.php"><span class="sub-menu-text">حساب معین</span></a></li>
					<?php } ?>
					<?php if(canShow('sanad_new_daftar.php')){?>
					<li><a class="" target="_blank" href="sanad_new_daftar.php"><span class="sub-menu-text">ثبت دریافتی / پرداختی</span></a></li>
					<?php } ?>
<!-- 					<li><a class="" target="_blank" href="belit.php"><span class="sub-menu-text">ثبت سند بلیط</span></a></li> -->
				</ul>
			</li>
			<?php } ?>
			<li class="has-sub">
				<a href="javascript:;" class="">
					<i class="fa fa-book fa-fw"></i> <span class="menu-text"> گزارشات </span>
					<span class="arrow"></span>
				</a>
				<ul class="sub">
					<?php if(canShow('hotel_gozaresh.php')){?>
					<li><a class="" target="_blank" href="hotel_gozaresh.php?h_id=<?php echo $hotel_id ?>"><span class="sub-menu-text">گزارش خدمات</span></a></li>
					<?php } ?>
					<?php if(canShow('reportExitHours.php')){?>
					<li><a class="" target="_blank" href="reportExitHours.php?h_id=<?php echo $hotel_id ?>"><span class="sub-menu-text">گزارش اتاق ها</span></a></li>
					<?php } ?>
					<?php if(canShow('sanad_gozaresh.php')){?>
					<li><a class="" target="_blank" href="sanad_gozaresh.php"><span class="sub-menu-text">گزارش اسناد</span></a></li>
					<?php } ?>
					<?php if(canShow('sanad_mande.php')){?>
					<li><a class="" target="_blank" href="sanad_mande.php"><span class="sub-menu-text">گزارش مانده</span></a></li>
					<?php } ?>
					<?php if(canShow('sagozaresh_tedad_mehmannad_new.php')){?>
					<li><a class="" target="_blank" href="gozaresh_tedad_mehman.php"><span class="sub-menu-text">گزارش درصد اشغال</span></a></li>
					<?php } ?>
					<?php if(canShow('gozaresh_sms.php')){?>
					<li><a class="" target="_blank" href="gozaresh_sms.php"><span class="sub-menu-text">گزارش پیامک ها</span></a></li>
					<?php } ?>
					<?php if(canShow('sagozaresh_sms_sendnad_new.php')){?>
					<li><a class="" target="_blank" href="gozaresh_sms_send.php"><span class="sub-menu-text">گزارش پیامک های ارسال شده</span></a></li>
					<?php } ?>
					<?php if(canShow('sms_unread.php')){?>
					<li><a class="" target="_blank" href="sms_unread.php"><span class="sub-menu-text">گزارش پیامک های خوانده نشده</span></a></li>
					<?php } ?>
					<?php if(canShow('ghazaReport_kol.php')){?>
					<li><a class="" target="_blank" href="ghazaReport_kol.php"><span class="sub-menu-text">گزارش غذا</span></a></li>
					<?php } ?>
					<!--
					<?php if(canShow('onUser.php')){?>
					<li><a class="" target="_blank" href="onUser.php"><span class="sub-menu-text">کاربران آنلاین</span></a></li>
					<?php } ?>
					-->
					<?php if(canShow('gozaresh_tedad_mehman.php')){?>
					<li><a class="" target="_blank" href="gozaresh_tedad_mehman.php"><span class="sub-menu-text">گزارش اشغال</span></a></li>
					<?php } ?>
				</ul>
			</li>


			<li class="has-sub">
				<a href="javascript:;" class="" id="requests-menu">
					<i class="fa fa-list-alt fa-fw"></i><span class="menu-text"> لیست مهمان ها </span>
					<span class="arrow"></span>
				</a>
				<ul class="sub">
					<?php if(canShow('mehman.php')){?>
					<li><a class="" target="_blank" href="mehman.php?h_id=<?php echo $hotel_id ?>"><span class="sub-menu-text">مهمان های مقیم</span></a></li>
					<?php } ?>
					<?php if(canShow('mehman_grooh.php')){?>
					<li><a class="" target="_blank" href="mehman_grooh.php?h_id=<?php echo $hotel_id ?>"><span class="sub-menu-text">مهمان های گروهی</span></a></li>
					<?php } ?>
					<?php if(canShow('mehman_all.php')){?>
					<li><a class="" target="_blank" href="mehman_all.php?h_id=<?php echo $hotel_id ?>"><span class="sub-menu-text">کلیه مهمان ها</span></a></li>
					<?php } ?>
					<?php if(canShow('list_inOut.php')){?>
					<li><a class="" target="_blank" href="list_inOut.php?h_id=<?php echo $hotel_id ?>"><span class="sub-menu-text">ورودی و خروجی ها</span></a></li>
					<?php } ?>
				</ul>
			</li>

			<li class="has-sub">
				<a href="javascript:;" class="" id="requests-menu">
					<i class="fa fa-folder-open fa-fw"></i><span class="menu-text"> پرونده </span>
					<span class="arrow"></span>
				</a>
				<ul class="sub">
					<?php if(canShow('hotel.php')){?>
					<li><a class="" target="_blank" href="hotel.php"><span class="sub-menu-text">مدیریت هتل</span></a></li>
					<?php } ?>
					<?php if(canShow('user.php')){?>
					<li><a class="" target="_blank" href="user.php"><span class="sub-menu-text">مدیریت کاربران</span></a></li>
					<?php } ?>
					<?php if(canShow('daftar.php')){?>
					<li><a class="" target="_blank" href="daftar.php"><span class="sub-menu-text">مدیریت دفتر</span></a></li>
					<?php } ?>
					<?php if(canShow('hotel_daftar.php')){?>
					<li><a class="" target="_blank" href="hotel_daftar.php"><span class="sub-menu-text">دسترسی دفتر به هتل</span></a></li>
					<?php } ?>
					<?php if(canShow('ajans.php')){?>
					<li><a class="" target="_blank" href="ajans.php"><span class="sub-menu-text">مدیریت آژانس</span></a></li>
					<?php } ?>
					<?php if(canShow('manage_watcher.php')){?>
					<li><a class="" target="_blank" href="manage_watcher.php"><span class="sub-menu-text">تعریف واچر</span></a></li>
					<?php } ?>
					<?php if(canShow('tarif_sms.php')){?>
					<li><a class="" target="_blank" href="tarif_sms.php"><span class="sub-menu-text">تعریف پیامک</span></a></li>
					<?php } ?>
					<?php if(canShow('reserve1_2.php')){?>
					<li><a class="" target="_blank" href="reserve1_2.php"><span class="sub-menu-text">رزرو شناور</span></a></li>
					<?php } ?>
					<?php if(canShow('garanti_tabaghe.php')){?>
					<li><a class="" target="_blank" href="garanti_tabaghe.php"><span class="sub-menu-text">گارانتی دسته بندی شده</span></a></li>
					<?php } ?>
				</ul>
			</li>
			<li class="has-sub">
				<a href="javascript:;" class="" id="requests-menu">
					<i class="fa fa-desktop fa-fw"></i><span class="menu-text"> فرانت آفیس </span>
					<span class="arrow"></span>
				</a>
				<ul class="sub">
					<li><a class="" href="resturan.php"><span class="sub-menu-text">صندوق ها</span></a>
						<ul class="sub-sub">
							<?php
                                
                $user_id = (int)$_SESSION['user_id'];
								mysql_class::ex_sql("select * from `sandogh_user` where `user_id` = '$user_id'",$ss);
// 								echo "select * from `sandogh_user` where `user_id` = '$user_id'";
// 								die();
								while($r=mysql_fetch_array($ss)){
									$gid = $r['id'];
									$sandogh_idd = $r['sandogh_id'];
									mysql_class::ex_sql("select `name` from `sandogh` where `id` = '$sandogh_idd' ",$s_id);
									$s_id1 = mysql_fetch_array($s_id);
									$sname = $s_id1['name'];
									echo "<li><a class=\"\" target=\"_blank\" href=\"resturan.php?sandogh_id=$sandogh_idd&\"><span class=\"sub-sub-menu-text\">$sname</span></a></li>";
								}
              ?>

						</ul>
					</li>
					<?php if(canShow('sandogh.php')){?>
					<li><a class="" target="_blank" href="sandogh.php"><span class="sub-menu-text">تعریف صندوق</span></a></li>
					<?php } ?>
					<?php if(canShow('sandogh_user.php')){?>
					<li><a class="" target="_blank" href="sandogh_user.php"><span class="sub-menu-text">دسترسی فرانت افیس</span></a></li>
					<?php } ?>
					<?php if(canShow('sandogh_item.php')){?>
					<li><a class="" target="_blank" href="sandogh_item.php"><span class="sub-menu-text">موارد فرانت افیس</span></a></li>
					<?php } ?>
					<?php if(canShow('sandogh_det.php')){?>
					<li><a class="" target="_blank" href="sandogh_det.php"><span class="sub-menu-text">فرانت افیس</span></a></li>
					<?php } ?>
					<?php if(canShow('sandogh_factors.php')){?>
					<li><a class="" target="_blank" href="sandogh_factors.php"><span class="sub-menu-text">عملیات فرانت افیس</span></a></li>
					<?php } ?>
				</ul>
			</li>
<!-- 			<li class="has-sub">
				<a href="javascript:;" class="" id="requests-menu">
					<i class="fa fa-shopping-cart fa-fw"></i><span class="menu-text"> انبارداری </span>
					<span class="arrow"></span>
				</a>
				<ul class="sub">
					<?php if(canShow('anbar.php')){?>
					<li><a class="" target="_blank" href="anbar.php"><span class="sub-menu-text">مدیریت انبار</span></a></li>
					<?php } ?>
					<?php if(canShow('kala_no.php')){?>
					<li><a class="" target="_blank" href="kala_no.php"><span class="sub-menu-text">نوع کالا</span></a></li>
					<?php } ?>
					<?php if(canShow('kala.php')){?>
					<li><a class="" target="_blank" href="kala.php"><span class="sub-menu-text">کالا</span></a></li>
					<?php } ?>
					<?php if(canShow('factor_kala.php')){?>
					<li><a class="" target="_blank" href="factor_kala.php?anbar_typ_id=1&"><span class="sub-menu-text">ورود به انبار</span></a></li>
					<?php } ?>
					<?php if(canShow('factor_kala.php')){?>
					<li><a class="" target="_blank" href="factor_kala.php?anbar_typ_id=2&"><span class="sub-menu-text">خروج از انبار</span></a></li>
					<?php } ?>
					<?php if(canShow('factor_kala.php')){?>
					<li><a class="" target="_blank" href="factor_kala.php?anbar_typ_id=3&"><span class="sub-menu-text">بازگشت به انبار</span></a></li>
					<?php } ?>
					<?php if(canShow('kala_vahed.php')){?>
					<li><a class="" target="_blank" href="kala_vahed.php"><span class="sub-menu-text">تعریف واحد</span></a></li>
					<?php } ?>
				</ul>
			</li>
			<li class="has-sub">
				<a href="javascript:;" class="" id="requests-menu">
					<i class="fa fa-dollar fa-fw"></i><span class="menu-text"> کاست </span>
					<span class="arrow"></span>
				</a>
				<ul class="sub">
					<?php if(canShow('sabt_kala.php')){?>
					<li><a class="" target="_blank" href="sabt_kala.php"><span class="sub-menu-text">کالای ترکیبی</span></a></li>
					<?php } ?>
					<?php if(canShow('sabt_jozeeat_kala.php')){?>
					<li><a class="" target="_blank" href="sabt_jozeeat_kala.php"><span class="sub-menu-text">جزئیات کالای ترکیبی</span></a></li>
					<?php } ?>

				</ul>
			</li> -->
			<?php if(canShow('gaant.php')){?>
			<li class="has-sub">
				<a href="gaant.php?hotel_id=<?php echo $hotel_id ?>" target="_blank" class="">
					<i class="fa fa-calendar"></i> <span class="menu-text"> شیت هتل </span>

				</a>

			</li>
			<?php } ?>
			<?php if(canShow('change_paziresh.php')){?>
			<li class="has-sub">
				<a href="change_paziresh.php?h_id=<?php echo $hotel_id ?>" target="_blank" class="">
					<i class="fa fa-exchange fa-fw"></i> <span class="menu-text"> جابجایی </span>
				</a>
			</li>
			<?php } ?>
			<?php if(canShow('search_name.php')){?>
			<li class="has-sub">
				<a href="search_name.php?hotel_id=<?php echo $hotel_id ?>" target="_blank" class="">
					<i class="fa fa-search fa-fw"></i> <span class="menu-text">جستجوی پیشرفته</span>
				</a>

			</li>
			<?php } ?>
		</ul>
		<!-- /SIDEBAR MENU -->
	</div>
</div>