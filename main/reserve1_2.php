<?php
	session_start();
	include("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = ($se->detailAuth('all') || $se->detailAuth('reserve'));
	function loadFloatHotels($hotel_id)
	{
		$out = '<select name="hotel_id" id="hotel_id" class="form-control inp" >'."\n";
		$hotels = hotel_class::getFloatHotel();
		foreach($hotels as $id=>$name)
			$out .="<option ".(($hotel_id==$id)?'selected="selected"':'')." value=\"$id\" >$name</option>\n";
		$out .= '</select>';
		return $out;
	}
	function loadNumber($inp=2)
	{
		$out = '';
		$inp = (int)$inp;
		for($i=1;$i<32;$i++)
		{
			$sel = (($i==$inp)?'selected="selected"':'');
			$si = $i;
			$out.="<option $sel  value='$i' >$si</option>\n";
		}
		return $out;
	}
	function objChecked($room_typ_id)
	{
		$room_typ_id = (int)$room_typ_id;
		$out = ((isset($_REQUEST['room_'.$room_typ_id]))?' checked="checked" ':'');
		return($out);
	}
	function loadObj($room_typ_id,$name)
	{
		$nm = ((isset($_REQUEST['tedad_'.$room_typ_id]))?(int)$_REQUEST['tedad_'.$room_typ_id]:1);
		$out =
'
<table width="100%">
	<tr>
		<td>
			<input type="checkbox" id="room_'.$room_typ_id.'" name="room_'.$room_typ_id.'" '.objChecked($room_typ_id).'>'.$name.' 
		</td>
		<td style="width:100px">
			<select  class="form-control inp" name="tedad_'.$room_typ_id.'" id="tedad_'.$room_typ_id.'" >
				'.loadNumber($nm).'
			</select>
		</td>
	</tr>
</table>
';
		return $out;
	}
	function loadTable($row_count=3)
	{
		$out = "<table width=\"100%\" cellspacing=\"0\" >\n<tr>\n";
		$i = 1;
		mysql_class::ex_sql("select `id`,`name` from `room_typ` order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$cn = $i % 2;
			$out .= "<td class='room_typ_td$cn' >".loadObj($r['id'],$r['name'])."</td>\n";
			if($i % $row_count == 0)
				$out .= "</tr>\n<tr>\n";
			$i++;	
		}
		$out .= "</tr>\n</table>\n";
		return($out);
	}
	function loadDaftar($inp)
	{
		$inp = (int)$inp;
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = ($se->detailAuth('all'));
		$out = "<select name=\"daftar_id\" id=\"daftar_id\" class=\"form-control inp\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
		if($isAdmin)
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `kol_id`>0 order by `name` ',$q);
		else
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `id`='.$inp.' and `kol_id`>0 order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;	
	}
	function loadDaftarBelit($inp,$typ)
	{
		$inp = (int)$inp;
		$user = new user_class((int)$_SESSION['user_id']);
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = ($se->detailAuth('all'));
		$out = "<select name=\"daftar_idBelit_$typ\" id=\"daftar_idBelit_$typ\" class=\"form-control inp\" onchange=\"document.getElementById('mod').value='2';document.getElementById('frm1').submit();\" ><option value='0' ></option>";
		if($isAdmin)
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `kol_id`>0 order by `name` ',$q);
		else
			mysql_class::ex_sql('select `id`,`name` from `daftar` where `id`='.$user->daftar_id.' and `kol_id`>0 order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;	
	}
	function loadAjans($daftar_id,$sel_aj)
	{
		$daftar_id = (int)$daftar_id;
		$out = "<select id='ajans_id' name=\"ajans_id\" class=\"form-control inp\" >";
		//mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 and `saghf_kharid`>=".$conf->min_saghf_kharid." order by `name`",$q);
		$ajanses = ajans_class::loadByDaftar($daftar_id,TRUE);
		//var_dump($ajanses);
		for($i=0;$i<count($ajanses);$i++)
		{
			$sel = (($ajanses[$i]['id']==$sel_aj)?'selected="selected"':'');
			$out.="<option $sel  value='".$ajanses[$i]['id']."' >".$ajanses[$i]['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadAjansBelit($daftar_id,$sel_aj,$typ)
	{
		$daftar_id = (int)$daftar_id;
		$out = "<select id='ajans_idBelit_$typ' name=\"ajans_idBelit_$typ\" class=\"form-control inp\">";
		$conf = new conf;
		if($conf->ajans_saghf_mande)
			mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 and `saghf_kharid`>=".$conf->min_saghf_kharid." order by `name`",$q);
		else
			mysql_class::ex_sql("select `id`,`name` from `ajans`  where `daftar_id`='$daftar_id' and `moeen_id` > 0 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$sel_aj)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d 14:00:00'));
	$shab = ((isset($_REQUEST['shab']))?(int)$_REQUEST['shab']:1);
	$tatarikh = date("Y-m-d H:i:s",strtotime($aztarikh.' + '.$shab.' day'));
	$sel_aj = ((isset($_REQUEST['ajans_id']))?$_REQUEST['ajans_id']:0);
	$hotel_id = (isset($_REQUEST['hotel_id']))?$_REQUEST['hotel_id']:-1;
	$ajans_idBelit_1 = ((isset($_REQUEST['ajans_idBelit_1']))?(int)$_REQUEST['ajans_idBelit_1']:-1);
	$ajans_idBelit_2 = ((isset($_REQUEST['ajans_idBelit_2']))?(int)$_REQUEST['ajans_idBelit_2']:-1);
	$ajans_idBelit_3 = ((isset($_REQUEST['ajans_idBelit_3']))?(int)$_REQUEST['ajans_idBelit_3']:-1);
	$form = '';
	if($isAdmin )
	{
		$daftar_id = ((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
		$daftar_idBelit_1 = ((isset($_REQUEST['daftar_idBelit_1']))?(int)$_REQUEST['daftar_idBelit_1']:-1);
		$daftar_idBelit_2 = ((isset($_REQUEST['daftar_idBelit_2']))?(int)$_REQUEST['daftar_idBelit_2']:-1);
		$daftar_idBelit_3 = ((isset($_REQUEST['daftar_idBelit_3']))?(int)$_REQUEST['daftar_idBelit_3']:-1);
	}
	else
	{
		$daftar_id = (int)$_SESSION["daftar_id"] ;
		$daftar_idBelit_1 = (int)$_SESSION["daftar_id"] ;
		$daftar_idBelit_2 = (int)$_SESSION["daftar_id"] ;
		$daftar_idBelit_3 = (int)$_SESSION["daftar_id"] ;
	}
	if($conf->tour_enabled)
	{
		$tour_mab_view = 'مبلغ تور:';
		$raft_sherkat = 'شرکت بلیت رفت';
		$m_belit1_view = 'مبلغ  بلیــت  رفت:';
		$m_belit2_view = '<br/> مبلغ‌بلیت‌برگشت:';
		$m_belit3_view = '<br/> مبـلغ‌ کمیـسیـون:';
		$m_belit2_style = '';
		$m_belit3_style = '';
	}
	else
	{
		$tour_mab_view = 'مبلغ کل هتل:';
		$raft_sherkat = 'حساب کمیسیون';
		$m_belit1_view = 'مبلغ  کمیسیون:';
		$m_belit2_view = '';
		$m_belit3_view = '';
		$m_belit2_style = 'style="display:none;"';
		$m_belit3_style = 'style="display:none;"';
	}
	if(isset($_REQUEST['mod']) && $_REQUEST['mod']=='reserve')
	{
		$hotel_name = $_REQUEST['hotel_name'];
		$hotel_id = (int)$_REQUEST['hotel_id'];
		$aztarikh = $_REQUEST['aztarikh'];
		$shab = (int)$_REQUEST['shab'];
		$shabreserve = ((isset($_REQUEST['shabreserve']))?TRUE:FALSE);
		$roozreserve = ((isset($_REQUEST['roozreserve']))?TRUE:FALSE);
		$room_typs = null;
		foreach($_REQUEST as $key => $value)
		{
			$tmp = explode('_',$key);
			if($tmp[0] == 'room' && count($tmp)==2)
				$room_typs[(int)$tmp[1]] = (int)$_REQUEST['tedad_'.(int)$tmp[1]];
		}
		$kh_sobhane_txt =$_REQUEST['kh_txt_1'] ;
		$kh_sobhane_v = (isset($_REQUEST['kh_v_1'])?TRUE:FALSE);
		$kh_sobhane_kh = (isset($_REQUEST['kh_kh_1'])?TRUE:FALSE);
		$kh_nahar_txt =$_REQUEST['kh_txt_2'] ;
		$kh_nahar_v = (isset($_REQUEST['kh_v_2'])?TRUE:FALSE);
		$kh_nahar_kh = (isset($_REQUEST['kh_kh_2'])?TRUE:FALSE);
		$kh_sham_txt =$_REQUEST['kh_txt_3'] ;
		$kh_sham_v = (isset($_REQUEST['kh_v_3'])?TRUE:FALSE);
		$kh_sham_kh = (isset($_REQUEST['kh_kh_3'])?TRUE:FALSE);
		$kh_transfer_ch =(isset($_REQUEST['kh_ch_4'])?TRUE:FALSE);
		$kh_transfer_v = (isset($_REQUEST['kh_v_4'])?TRUE:FALSE);
		$kh_transfer_kh =(isset($_REQUEST['kh_kh_4'])?TRUE:FALSE);
		if($hotel_name=='')
		{
			$hot = new hotel_class($hotel_id);
			$hotel_name = $hot->name;
		}
		$new_hotel = hotel_class::add($hotel_name);
		$hotel_id = $new_hotel['hotel_id'];	
		$room_ids = array();
		foreach($room_typs as $room_typ_id=>$tedad)
			$room_ids[] = room_class::add($hotel_id,$room_typ_id,$tedad,$aztarikh,$tatarikh);
		$form = "
<div style='display:none;' >
	<form id=\"reserve2\" action=\"reserve2.php\" method=\"GET\">
		هتل:
<input name='hotel_id' value='$hotel_id' >
		از تاریخ:
<input name='aztarikh' value='$aztarikh' >
		شب:
<input name='shab' value='$shab' >
		مد
<input name='mod' value='1' >
		مد۲
<input name='mode1' value='0' >
		تعداد نفرات
<input name='tedad_nafarat' value='".$_REQUEST['tedad_nafarat']."' >
		دفتر:
<input name='daftar_id' value='$daftar_id' >
		آژانس
<input name='ajans_id' value='$sel_aj' >
		دفتر بلیط۱
<input name='daftar_idBelit_1' value='$daftar_idBelit_1' >
		دفتر بلیط۲
<input name='daftar_idBelit_2' value='$daftar_idBelit_2' >
		دفتر بلیط۳
<input name='daftar_idBelit_3' value='$daftar_idBelit_3' >
		آژانس بلیط۱
<input name='ajans_idBelit_1' value='$ajans_idBelit_1' >
		آژانس بلیط۲
<input name='ajans_idBelit_2' value='$ajans_idBelit_2' >
		آژانس بلیط۳
<input name='ajans_idBelit_3' value='$ajans_idBelit_3' >
		صبحانه
<input name='kh_txt_".$new_hotel['sobhane']."' value='$kh_sobhane_txt' >
<input name='kh_v_".$new_hotel['sobhane']."' type='checkbox' ".(($kh_sobhane_v)?'checked=checked':'')." >
<input name='kh_kh_".$new_hotel['sobhane']."' type='checkbox' ".(($kh_sobhane_kh)?'checked=checked':'')." >

		ناهار
<input name='kh_txt_".$new_hotel['nahar']."' value='$kh_nahar_txt' >
		<input name='kh_v_".$new_hotel['nahar']."' type='checkbox' ".(($kh_nahar_v)?'checked=checked':'')." >
		<input name='kh_kh_".$new_hotel['nahar']."' type='checkbox' ".(($kh_nahar_kh)?'checked=checked':'')." >

		شام
<input name='kh_txt_".$new_hotel['sham']."' value='$kh_sham_txt' >
		<input name='kh_v_".$new_hotel['sham']."' type='checkbox' ".(($kh_sham_v)?'checked=checked':'')." >
		<input name='kh_kh_".$new_hotel['sham']."' type='checkbox' ".(($kh_sham_kh)?'checked=checked':'')." >

		ترانسفر
<input name='kh_ch_".$new_hotel['transfer']."' type='checkbox' ".(($kh_transfer_ch)?'checked=checked':'')." >
		<input name='kh_v_".$new_hotel['transfer']."' type='checkbox' ".(($kh_transfer_v)?'checked=checked':'')." >
		<input name='kh_kh_".$new_hotel['transfer']."' type='checkbox' ".(($kh_transfer_kh)?'checked=checked':'')." >

		مبلغ
<input name='tour_mablagh' value='".$_REQUEST['tour_mablagh']."' >
		مبلغ بلیط
<input name='belit_mablagh_1' value='".$_REQUEST['belit_mablagh_1']."' >
		مبلغ بلیط۲
<input name='belit_mablagh_2' value='".((isset($_REQUEST['belit_mablagh_2']))?$_REQUEST['belit_mablagh_2']:'')."' >
		مبلغ بلیط۳(کمیسیون)
<input name='belit_mablagh_3' value='".((isset($_REQUEST['belit_mablagh_3']))?$_REQUEST['belit_mablagh_3']:'')."' >
		اتاقها
<input name='room_id_tmp' value='".(implode(',',$room_ids))."' >
	</form>
</div>
<script language='javascript' >document.getElementById('reserve2').submit();</script>
";
	}
	$tr_nafar = "
<table width='100%'  />
	<tr>
		<td>تعدادنفرات:<input onkeypress='return numbericOnKeypress(event);' type='text' id='tedad_nafarat' name='tedad_nafarat' value='".((isset($_REQUEST['tedad_nafarat']))?$_REQUEST['tedad_nafarat']:1)."' class='form-control inp' onblur='calculate_nafar();' ></td>
		<td colspan='3' >
			$tour_mab_view<input onkeyup='monize(this);' class='form-control inp' type='text' name='tour_mablagh' id='tour_mablagh' value='".((isset($_REQUEST['tour_mablagh']))?$_REQUEST['tour_mablagh']:"")."' >
		</td>
		<td>
			$m_belit1_view<input onkeyup='monize(this);' class='form-control inp' type='text' name='belit_mablagh_1' id='belit_mablagh_1' value='".((isset($_REQUEST['belit_mablagh_1']))?$_REQUEST['belit_mablagh_1']:"")."' >
		$m_belit2_view<input $m_belit2_style onkeyup='monize(this);' class='form-control inp' type='text' name='belit_mablagh_2' id='belit_mablagh_2' value='".((isset($_REQUEST['belit_mablagh_2']))?$_REQUEST['belit_mablagh_2']:"")."' >
		$m_belit3_view<input $m_belit2_style onkeyup='monize(this);' class='form-control inp' type='text' name='belit_mablagh_3' id='belit_mablagh_3' value='".((isset($_REQUEST['belit_mablagh_3']))?$_REQUEST['belit_mablagh_3']:"")."' >
		</td>
	</tr>
</table>

";
	$tr_hesab = "<table width='100%' style='border:dotted 1px #000000;margin-top:2px;' ><tr><td align='left' >نام دفتر:</td><td>".loadDaftar($daftar_id)."</td><td align='left'  >نام آژانس</td><td colspan='1' >". loadAjans($daftar_id,$sel_aj)."</td>";
			$sel_ajBelit_1 = ((isset($_REQUEST['ajans_idBelit_1']))?$_REQUEST['ajans_idBelit_1']:0);
			$tr_hesab .= "<tr><td align='left'>دفتر:</td><td>".loadDaftarBelit($daftar_idBelit_1,1)."</td><td align='left' >$raft_sherkat</td><td colspan='1' >". loadAjansBelit($daftar_idBelit_1,$sel_ajBelit_1,1)."</td>";
			$tr_hesab .= "</tr>";
			$sel_ajBelit_2 = ((isset($_REQUEST['ajans_idBelit_2']))?$_REQUEST['ajans_idBelit_2']:0);
			$tr_hesab .= "<tr $m_belit2_style ><td align='left' >دفتر:</td><td>".loadDaftarBelit($daftar_idBelit_2,2)."</td><td align='left' >شرکت بلیت برگشت</td><td colspan='1' >". loadAjansBelit($daftar_idBelit_2,$sel_ajBelit_2,2)."</td>";
			$sel_ajBelit_3 = ((isset($_REQUEST['ajans_idBelit_3']))?$_REQUEST['ajans_idBelit_3']:0);
			$tr_hesab .= "<tr $m_belit2_style ><td align='left' >دفتر:</td><td>".loadDaftarBelit($daftar_idBelit_3,3)."</td><td align='left' >کمیسیون</td><td colspan='1' >". loadAjansBelit($daftar_idBelit_3,$sel_ajBelit_3,3)."</td>";
			$tr_hesab .= "</tr></table>";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>رزرو شناور</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no" />
	<meta name="description" content="" />
	<meta name="author" content="" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/cloud-admin.css" />
	<!-- Clock -->
	<link href="<?php echo $root ?>inc/digital-clock/assets/css/style.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?php echo $root ?>js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>js/colorbox/colorbox.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $root ?>css/animatecss/animate.min.css" />
    <!-- DataTables CSS -->
    <link href="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="<?php echo $root ?>datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">
<!-- DataTables JavaScript -->
    <!-- JQUERY -->
<script src="<?php echo $root ?>js/jquery/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $root ?>datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
    
    <script type="text/javascript" src="../js/tavanir.js"></script>
    

    
	
	<!-- GLOBAL HEADER -->
	<?php include_once "inc/headinclude.php"; ?>
	
</head>
<body>
    <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
    <form method="POST" name="frmtedad" id ="frmtedad">
        <input name="txttedad" id="txttedad" type="hidden" value="1"/>
    </form>
	<!-- HEADER -->
	<?php include_once "headermodul.php"; ?>
	<!--/HEADER -->
	
	<!-- PAGE -->
	<section id="page">
			<!-- SIDEBAR -->
			<?php include_once "menubarmodul.php"; ?>
			<!-- /SIDEBAR -->
		<div id="main-content">
			<div class="container">
				
                
                <div class="row" style="margin-right:0px;margin-left:0px;">
                <div class="col-lg-12" style="padding:0px;">
                    <div class="panel panel-default" style="border: 1px solid #ffae2e;">
                        <div class="panel-heading" style="background-color:#ffae2e;color:white;padding:1px;">
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-bell"></i>رزرو شناور</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
    
                            <form id='frm1'  method='POST' >
                                
                                
                                <div class="box border orange">
									
									<div class="box-body" style="overflow-x:scroll">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">نام هتل</th>
												<th style="text-align:right">تاریخ</th>
												<th style="text-align:right">مدت اقامت</th>
                                                <th style="text-align:right">شب-‌رزرو<br/>(نیم شارژ ورودی)</th>
                                                <th style="text-align:right">روز-‌رزرو<br/>(نیم شارژ خروجی)</th>
											  </tr>
											</thead>
											<tbody>
				
	
					<tr class='names'>
						<td class='names' >
							<input class="form-control inp" name="hotel_name" id="hotel_name" value="<?php echo ((isset($_REQUEST['hotel_name']))?$_REQUEST['hotel_name']:'') ?>" >
							<?php echo loadFloatHotels($hotel_id); ?>
						</td>
						<td class='names' >	
		 					   <input value=" <?php echo ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdate($aztarikh):audit_class::hamed_pdate(date("Y-m-d H:i:s"))); ?>" type="text" name='aztarikh' class='form-control inp' style='direction:ltr;' id="datepicker1" />	
						</td>
						<td class='names' >
							<select  class='form-control inp' name='shab' id='shab' >
								<?php  echo loadNumber((isset($_REQUEST['shab']))?$_REQUEST['shab']:1); ?>
							</select>						
						</td>
						<td class='names' >
							<input name="shabreserve" id="shabreserve" type="checkbox" <?php echo ((isset($_REQUEST['shabreserve']))?'checked="checked"':''); ?> >
						</td>
						<td class='names' >
							<input name="roozreserve" id="roozreserve" type="checkbox" <?php echo ((isset($_REQUEST['roozreserve']))?'checked="checked"':''); ?> >
						</td>
					</tr>
					<tr>
						<td colspan="5" class='room_typ_td' >
							<?php echo loadTable(); ?>
						</td>
					</tr>
					<tr>
						<td colspan="5" class='names' >
                                                   	<?php echo $tr_nafar; ?>     
                                                </td>
					</tr>
					<tr>
						<td colspan="5" >
                                                   	<?php echo $tr_hesab; ?>     
                                                </td>
					</tr>
					<tr>
                                                <td colspan="5" class='room_typ_td' >
							<?php echo khadamat_class::loadFake(); ?>
                                                </td>
                                        </tr>
					<tr>
                                                <td colspan="5" class='names' >
							<input type='hidden' name='mod' id='mod' value='1' >
                                                    <button class="btn btn-info col-md-4" onclick="reserve();">رزرو</button>
                                                        
                                                </td>
                                        </tr>
                                                </tbody>
				</table>
			</form>
			<?php
				echo $form;
			?>
                        </div>
                        
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
        </div>
    </section>
	<!--/PAGE -->
    	<!-- Modal -->
    <!-- Modal : anbar modal -->
    <div class="modal fade" id="anbar-modal">
	
    </div>
			<!--/Modal : anbar modal-->
	<!-- FOOTER -->

    <!-- Loading -->
<div id="loading">
    <div class="container1">
	   <div class="content1">
        <div class="circle"></div>
        <div class="circle1"></div>
        </div>
    </div>
</div>    
	<!-- GLOBAL JAVASCRIPTS -->
	<?php include_once "inc/footinclude.php" ?>
	
	<!-- Clock -->
	<script src="<?php echo $root ?>inc/digital-clock/assets/js/script.js"></script>
	
	<!-- news ticker -->
	
	<!-- DATE RANGE PICKER -->
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/moment.min.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/daterangepicker.min.js"></script>
	

	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo $root ?>js/bootstrap-datepicker.fa.min.js"></script>
	<script>
	
		var i=0;
		var SSmsg = null;
	
		jQuery(document).ready(function() {
            
            
            $("#loading").hide(); 
            App.setPage("gallery");  //Set current page
			//App.setPage("widgets_box");  //Set current page
			App.init(); //Initialise plugins and elements
			getofflist();
            
             $(document).ready(function() {
                  $("#datepicker0").datepicker();
            
                $("#datepicker1").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                    
                });
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
                $("#datepicker2").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker3").datepicker({
                    numberOfMonths: 3,
                    showButtonPanel: true
                });
            
                $("#datepicker4").datepicker({
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker5").datepicker({
                    minDate: 0,
                    maxDate: "+14D"
                });
            
                $("#datepicker6").datepicker({
                    isRTL: true,
                    dateFormat: "d/m/yy"
                });                
        
        $('#dataTables-example').DataTable({
                responsive: true
        });
        
       
        
    });
            
            
		});
        
        function reserve()
			{
				if(checkboxChecked())
				{
					if(confirm('آیا رزرو انجام شود؟'))
					{
						document.getElementById('mod').value = 'reserve';
						document.getElementById('frm1').submit();
					}
				}
				else
					alert('لطفاً اطلاعات را کامل وارد کنید');
			}
			function clearKhadamat()
			{
				var inps = document.getElementsByTagName('input');
				var x;
				for(var i=0;i < inps.length;i++)
				{
					x = inps[i].id.split('_');
					if(x[0]=='kh' && x.length==3)
					{
						if(x[1]=='txt')
							inps[i].value=0;	
						if(x[1]=='v' || x[1]=='kh' || x[1]=='ch')
							inps[i].checked=false;
					}
				}
			}
			function resetOrNotKh(Obj)
			{
				if(Obj.checked)
					reset_full_board();
				else
					clearKhadamat();
			
			}
			function reset_full_board()
			{ 
				document.getElementById('kh_v_1').checked=false;
				document.getElementById('kh_kh_1').checked=true;
				document.getElementById('kh_v_2').checked=false;
				document.getElementById('kh_kh_2').checked=true;
				document.getElementById('kh_v_3').checked=true;
				document.getElementById('kh_kh_3').checked=false;
				document.getElementById('kh_v_4').checked=true;
				document.getElementById('kh_kh_4').checked=true;
				kh_check("4");
				calculate_nafar();
			}
			function kh_check(inp)
			{
				var mainObj = document.getElementById('kh_ch_'+inp);
				var vObj = document.getElementById('kh_v_'+inp);
				var khObj = document.getElementById('kh_kh_'+inp);
				if(vObj.checked || khObj.checked )
					mainObj.checked = true;
				else
					mainObj.checked = false;
			}
			function calculate_nafar()
			{
				if(document.getElementById('tedad_nafarat'))
				{
		                        var adult = document.getElementById('tedad_nafarat').value;   
		                        var inps =  document.getElementsByTagName('input');
		                        var tmp;
		                        adult = parseInt(adult,10);
		                        for (var i=0;i<inps.length;i++)
		                        { 
		                                tmp = inps[i].name.split('_');
		                                if (tmp.length==3 && tmp[0]=='kh' && tmp[1]=='txt')
		                                        inps[i].value = String(adult);
		                        }
				}
			}
			function checkboxChecked()
			{
				var out = false;
				var tmp;
                                var inps = document.getElementsByTagName('input');
				var tour = parseInt(unFixNums(umonize(document.getElementById('tour_mablagh').value)),10);
				if(isNaN(tour))
					tour =0;
				var belit1 = parseInt(unFixNums(umonize(document.getElementById('belit_mablagh_1').value)),10);
				if(isNaN(belit1))
					belit1 =0;
				var belit2 = parseInt(unFixNums(umonize(document.getElementById('belit_mablagh_2').value)),10);
				if(isNaN(belit2))
					belit2 =0;
				var belit3 = parseInt(unFixNums(umonize(document.getElementById('belit_mablagh_3').value)),10);
				if(isNaN(belit3))
					belit3 =0;
                                for(var i=0;i < inps.length;i++)
				{
					tmp = String(inps[i].id).split('_');
                                        if(tmp[0]=='room' && inps[i].type=='checkbox' && inps[i].checked && tmp.length==2)
                                                out = true;
				}
				if(document.getElementById('daftar_id').selectedIndex <= 0 ||
 document.getElementById('tour_mablagh').value=='' ||
 (parseInt(document.getElementById('belit_mablagh_1').value,10)>0 && document.getElementById('daftar_idBelit_1').selectedIndex <= 0) ||
 (parseInt(document.getElementById('belit_mablagh_2').value,10)>0 && document.getElementById('daftar_idBelit_2').selectedIndex <= 0) ||
(parseInt(document.getElementById('belit_mablagh_3').value,10)>0 && document.getElementById('daftar_idBelit_3').selectedIndex <= 0) ||
 parseInt(document.getElementById('tour_mablagh').value,10)==0 || 
tour<=(belit1+belit2+belit3)
 )
                                        out = false;
				return(out);
			}
        
        
		function aa(x){
            $.post("<?php echo $root ?>modules/CalendarGetOFFAjax.php",{},function (data){
                if(data){
                    i=data;
                }else{
                    alert("Error!");
                }
            });
        }
		
        function getofflist(){
            $("#cal-pr").html("<img align=\"middle\" class=\"img-responsive\" style=\"margin: auto;\" src=\"<?php echo $root ?>img/loaders/17.gif\">");
            $.post("<?php echo $root ?>modules/CalendarGetOFFAjax.php",{},function (data){
                if(data){
                    i=data;
                    $("#cal-pr").html("");
                    $("#cal-pr").datepicker({changeMonth: true});
                }else{
                    $("#cal-pr").html("<p class=\"fa fa-exclamation-circle text-danger\"> عدم برقراری ارتباط با پایگاه داده</p>");
                }
            });
        }
        
        function rakModal(rakId){
            StartLoading();
            var id=rakId;
            
            $.post("gaantinfo.php",{oid:id},function(data){
                StopLoading();
                $("#rk").html(data);
                $('#rak-modal').modal('show');             

                             });
        }
        
	function StartLoading(){
        
        $("#loading").show();    
		
    }
    function StopLoading(){
        $("#loading").hide(); 
    }
					


		
	</script>


	<?php include_once "footermodul.php"; ?>
	<!--/FOOTER -->
	

</body> 
</html>