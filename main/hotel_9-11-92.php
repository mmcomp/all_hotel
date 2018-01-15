<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = $se->detailAuth('all');	
	function loadMoeen($inp)
	{
		$inp = (int)$inp;
		$aj = new hotel_class($inp);
		if($aj->moeen_id!=0)
		{
			$moeen = new moeen_class($aj->moeen_id);
			$nama = $moeen->name.'('.$moeen->code.')';
		}
		else
		{
			$nama = 'انتخاب';
		}
		
		$out = "<u><span onclick=\"window.location =('select_hesab_hotel.php?sel_id=$inp');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
		return $out;
	}
	function loadMoeenGhaza($inp)
	{
		$inp = (int)$inp;
		$aj = new hotel_class($inp);
		if($aj->ghaza_moeen_id!=0)
		{
			$moeen = new moeen_class($aj->ghaza_moeen_id);
			$nama = $moeen->name.'('.$moeen->code.')';
		}
		else
		{
			$nama = 'انتخاب';
		}
		
		$out = "<u><span onclick=\"window.location =('select_hesab.php?sel_id=$inp&return_name=sel_id_ghaza&refPage=hotel.php');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
		return $out;
	}
	function loadWork($inp)
	{
		$out = "<u><span onclick=\"wopen('hotelwork.php?hotel_id=$inp&','',500,200);\"  style='color:blue;cursor:pointer;' >ادامه</span></u>";
		return($out);
	}
	function loadReserve($inp)
	{
		$out = "<u><span onclick=\"wopen('reserve1.php?h_id=$inp&mode1=1&','',800,500);\"  style='color:blue;cursor:pointer;' >رزرو</span></u>&nbsp;<u><span onclick=\"wopen('gaant.php?hotel_id=$inp&','',800,500);\"  style='color:green;cursor:pointer;' >شیت</span></u>";
                return($out);
	}
	function loadRoom($inp)
	{
                $out = "<u><span onclick=\"wopen('rooms.php?hotel_id=$inp&','',500,200);\"  style='color:blue;cursor:pointer;' >اتـــاق</span></u> <u><span onclick=\"wopen('khadamat.php?hotel_id=$inp&','',500,200);\"  style='color:red;cursor:pointer;' >خدمات</span></u>";
                return($out);
        }
	function loadMalek()
	{
		$out['دیگران'] = 0;
		$out['اختصاصی'] = 1;
		$out['شناور'] = 2;
		return $out;
	}
	function loadCancel($inp)
	{
		$out = "<u><span onclick=\"wopen('refund.php','',800,500);\"  style='color:blue;cursor:pointer;' >کنسلی</span></u>";
                return($out);
	}
	function loadEdit($inp)
	{
		$out = "<u><span onclick=\"wopen('showreserve.php?h_id=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >اصلاحیه</span></u>";
                return($out);
	}
	function loadRep($inp)
	{
		$out = "<u><span onclick=\"wopen('hotel_gozaresh.php?h_id=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >گزارش‌خدمات</span></u>";
                return($out);
	}
	function loadPic($inp)
	{
//		$out = "<u><span onclick=\"wopen('upload_pic.php?h_id=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >ارسال</span></u>";
		$out = "<u><span onclick=\"wopen('total_upload.php?h_id=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >ارسال</span></u>";
                return($out);
	}
	function loadAdRep($inp)
	{
		$out = "<u><span onclick=\"wopen('search_name.php?hotel_id=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >جستجو ‌پیشرفته</span></u>";
		
                return($out);
	}
//loadCost
        function loadCost($inp)
        {
                $out = "<u><span onclick=\"wopen('cost_khorooj.php?hotel_id=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >غذا</span></u>";
                return($out);
        }
	function loadChange($inp)
	{
		$out = "<u><span onclick=\"wopen('change.php?h_id=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >جابجایی</span></u>";		
                return($out);
	}
	//-------------------functions End---------------------
	if(isset($_REQUEST['sel_id']))
	{
		$moeen_id = (int)$_REQUEST['moeen_id'];
		$sel_id = $_REQUEST['sel_id'];
		mysql_class::ex_sqlx("update `hotel` set `moeen_id`=$moeen_id where `id`=$sel_id");
		//echo "update `hotel` set `moeen_id`=$moeen_id where `id`=$sel_id";
	}
	if(isset($_REQUEST['sel_id_ghaza']))
	{
		$moeen_id = (int)$_REQUEST['moeen_id'];
		$sel_id = $_REQUEST['sel_id_ghaza'];
		mysql_class::ex_sqlx("update `hotel` set `ghaza_moeen_id`=$moeen_id where `id`=$sel_id");
		//echo "update `hotel` set `moeen_id`=$moeen_id where `id`=$sel_id";
	}
       /* $combo=array();
	$shart = '';
	if ($_SESSION['typ']!='0')
	{
		$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);	
		if($hotel_acc!=null)
		{
			for($l=0;$l<count($hotel_acc);$l++)
				$shart.=(($l == 0) ? ' and (' : ' or').' `id`='.$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
		}
	}*/
	$combo=array();
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart = '';
	if($hotel_acc!=null)
	{
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? ' and (' : ' or').' `id`='.$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
	}
	function add_item()
	{
		$fields = null;
                foreach($_REQUEST as $key => $value)
		{
                        if(substr($key,0,4)=="new_")
			{
                                if($key != "new_id" && $key != "new_moeen_id" )
				{
                                        $fields[substr($key,4)] =perToEnNums($value);
                                }
                        }
		}
		$kol_id = kol_class::addById($fields['name']);
		$moeen_id = moeen_class::addById($kol_id,'درآمد رزرواسیون '.$fields['name']);
		$moeen_hazine_id = moeen_class::addById($kol_id,'هزینه غذای '.$fields['name']);
		$fields['moeen_id'] = $moeen_id;
		$fields['ghaza_moeen_id'] = $moeen_hazine_id;
		$query = '';
                $fi = "(";
	        $valu="(";
		foreach ($fields as $field => $value)
        	{
		        $fi.="`$field`,";
                        $valu .="'$value',";
                }
       		$fi=substr($fi,0,-1);
                $valu=substr($valu,0,-1);
		$fi.=")";
        	$valu.=")";
        	$query.="insert into `hotel` $fi values $valu";
		mysql_class::ex_sqlx($query);
	}
	function infoLink($inp)
	{
		$out = '';
		$hotel = new hotel_class($inp);
		$out = ((isset($hotel->info['properties']))?$hotel->info['properties']:'');
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('info.php?hotel_id=$inp&','',500,600);\">مشخصات : $out</span></u>"; 
		return($out);
	}
        $grid = new jshowGrid_new("hotel","grid1");
	$grid->whereClause="1=1 $shart order by `name`";
	$grid->width = '95%';
	$grid->index_width = '20px';
        $grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = "نام";
	$grid->columnHeaders[2] = null;
	$grid->columnHeaders[3] = null;
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[5] = null;
	if($isAdmin)
	{
		$grid->columnHeaders[3] = 'نوع مالکیت';
		$grid->columnLists['3'] = loadMalek();
		$grid->fieldList[4] = 'id';
		$grid->columnHeaders[4] = 'اطلاعات هتل';
		$grid->columnFunctions[4] = 'infoLink';
		$grid->columnAccesses[4] = 0;
		$grid->addFeild('id');
		$grid->columnHeaders[6] = 'حساب معین';
		$grid->columnFunctions[6]='loadMoeen';
		$grid->addFeild('id');
		$grid->columnHeaders[7] = 'زمان‌های فعالیت';
		$grid->columnFunctions[7]='loadWork';
        	$grid->addFeild('id');
		$grid->columnHeaders[8] = 'مدیریت‌اطلاعات‌اتاق';
	        $grid->columnFunctions[8]='loadRoom';
	        $grid->addFeild('id');
        	$grid->columnHeaders[9] = 'رزرو‌هتل';
        	$grid->columnFunctions[9]='loadReserve';
		$grid->addFeild('id');
		$grid->columnHeaders[10]='کنسلی';
	        $grid->columnFunctions[10]='loadCancel';
		$grid->addFeild('id');
		$grid->columnHeaders[11]='اصلاحیه';
	        $grid->columnFunctions[11]='loadEdit';
		$grid->addFeild('id');
		$grid->columnHeaders[12]='گزارش خدمات';
	        $grid->columnFunctions[12]='loadRep';
		$grid->addFeild('id');
		//$grid->columnHeaders[12]='ارسال تصویر';
		$grid->columnHeaders[13]=null;
	        $grid->columnFunctions[13]='loadPic';
		$grid->addFeild('id');
		$grid->columnHeaders[14]='گزارش پیشرفته';
	        $grid->columnFunctions[14]='loadAdRep';
                $grid->addFeild('id');
                $grid->columnHeaders[15]='جابجایی';
                $grid->columnFunctions[15]='loadChange';
		$grid->addFeild('id');
		$grid->columnHeaders[16] = ' حساب معین<br/>هزینه غذا';
		$grid->columnFunctions[16]='loadMoeenGhaza';
                $grid->addFeild('id');
		$grid->columnHeaders[17] = 'غذای پرسنل';
		$grid->columnFunctions[17]='loadCost';
	}
	else
	{
		$int_tmp = 6;
		if($se->detailAuth('own'))
		{
			$grid->columnHeaders[3] = 'نوع مالکیت';
			$grid->columnLists['3'] = loadMalek();
			$grid->columnAccesses[3] = 1;
		}
		if($se->detailAuth('info'))
		{
			$grid->fieldList[4] = 'id';
			$grid->columnHeaders[4] = 'اطلاعات هتل';
			$grid->columnFunctions[4] = 'infoLink';
			$grid->columnAccesses[4] = 0;
		}
		if($se->detailAuth('moeen'))
		{
			$grid->addFeild('id');
			$grid->columnHeaders[$int_tmp] = 'حساب معین';
		        $grid->columnFunctions[$int_tmp]='loadMoeen';
			$int_tmp++;
		}
		if($se->detailAuth('workingdate'))
		{
			$grid->addFeild('id');
			$grid->columnHeaders[$int_tmp] = 'زمان‌های فعالیت';
		        $grid->columnFunctions[$int_tmp]='loadWork';
			$int_tmp++;
		}
		if($se->detailAuth('rooms'))
		{
			$grid->addFeild('id');
			$grid->columnHeaders[$int_tmp] = 'مدیریت‌اطلاعات‌اتاق';
		        $grid->columnFunctions[$int_tmp]='loadRoom';
			$int_tmp++;
		}
		if($se->detailAuth('reserve'))
		{
			$grid->addFeild('id');
			$grid->columnHeaders[$int_tmp] = 'رزرو‌هتل';
		        $grid->columnFunctions[$int_tmp]='loadReserve';
			$int_tmp++;
		}
		if($se->detailAuth('edit_reserve'))
		{
			$grid->addFeild('id');
			$grid->columnHeaders[$int_tmp]='اصلاحیه';
			$grid->columnFunctions[$int_tmp]='loadEdit';
			$int_tmp++;
		}
		if($se->detailAuth('refund'))
		{
			$grid->addFeild('id');
			$grid->columnHeaders[$int_tmp]='کنسلی';
			$grid->columnFunctions[$int_tmp]='loadCancel';
			$int_tmp++;
		}
		if($se->detailAuth('change'))
                {
                        $grid->addFeild('id');
                        $grid->columnHeaders[$int_tmp]='جابجایی';
                        $grid->columnFunctions[$int_tmp]='loadChange';
                        $int_tmp++;
                }
/*
		if($se->detailAuth('send'))
		{
			$grid->addFeild('id');
			$grid->columnHeaders[$int_tmp]='ارسال تصویر';
			$grid->columnFunctions[$int_tmp]='loadPic';
			$int_tmp++;
		}
*/
		if($se->detailAuth('khadamat'))
		{
			$grid->addFeild('id');
			$grid->columnHeaders[$int_tmp]='گزارش خدمات';
			$grid->columnFunctions[$int_tmp]='loadRep';
			$int_tmp++;
		}
		if($se->detailAuth('advance'))
		{
	                $grid->addFeild('id');
        	        $grid->columnHeaders[$int_tmp]='گزارش پیشرفته';
                	$grid->columnFunctions[$int_tmp]='loadAdRep';
			$int_tmp++;
		}
		if($se->detailAuth('ghaza_moeen'))
		{
	                $grid->addFeild('id');
        	        $grid->columnHeaders[$int_tmp]=' حساب معین<br/>هزینه غذا';
                	$grid->columnFunctions[$int_tmp]='loadMoeenGhaza';
			$int_tmp++;
		}
                if($se->detailAuth('cost'))
                {
                        $grid->addFeild('id');
                        $grid->columnHeaders[$int_tmp]='غذای پرسنل';
                        $grid->columnFunctions[$int_tmp]='loadCost';
                        $int_tmp++;
                }
		$grid->canEdit = $se->detailAuth('canedit');
		$grid->canAdd = $se->detailAuth('canadd');
		$grid->canDelete = $se->detailAuth('candelete');
	}
	$grid->addFunction = 'add_item';
        $grid->intial();
        $grid->executeQuery();
	//$grid->canAdd = FALSE;
	if($grid->getRowCount()<$conf->limit_hotel)
		$grid->canAdd = TRUE;
        $out = $grid->getGrid();
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
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
			سامانه رزرواسیون هتل	
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>	
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<br/>
			<?php echo $out.'<br/>';
			 if($se->detailAuth('room_typ') || $se->detailAuth('all')) { ?>
				<table>
					<tr>
						<td>
							<a href="room.php" target="_blank"  ><img src="../img/room.gif" ></a>
						</td>
					</tr>
					<tr>
						<td>
							<a href="room.php" style="color:#000;" target="_blank"  >تعریف نوع اتاق</a>
						</td>
					</tr>
				</table>
			<?php } ?>
		</div>
		<script language="javascript">
                        var ids = document.getElementsByName("new_id");
			for(var i=0;i<ids.length;i++)
				ids[i].style.display="none";
		</script>
	</body>
</html>
