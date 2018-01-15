<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view || !$conf->anbar)
                die(lang_fa_class::access_deny);
	function delete_item($id)
	{
		mysql_class::ex_sqlx("update `anbar_det` set `en`=0 where `id` = $id");
	}
	function loadKala()
	{
		$out = "";
		mysql_class::ex_sql('select `name`,`code`,`id`,`vahed_id` from `kala` order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$vahed = new kala_vahed_class((int)$r['vahed_id']);
            $out.="<option value='".$r['id']."'>".$r['name'].' ('.$r['code'].')'.'['.$vahed->name.']'."</option>";
			//$out[$r['name'].' ('.$r['code'].')'.'['.$vahed->name.']'] = (int)$r['id'];
		}
		return($out);
	}
	function loadAnbar($anbar_id)
	{
		$out = '<select class="form-control inp" name="anbar_id" id="anbar_id" onchange="document.getElementById(\'frm1\').submit();" ><option value=""></option>';
		mysql_class::ex_sql('select `name`,`id` from `anbar` where `en`<>2 order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = ($r['id']==$anbar_id)?'selected="selected"':'';
			$out.= "<option $sel value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out .='</select>';
		return($out);
	}
	function loadUser()
	{
		$out = "";
		mysql_class::ex_sql('select `fname`,`lname`,`id` from `user` order by `lname`',$q);
		while($r = mysql_fetch_array($q))
		{
            $out.="<option value='".$r['id']."'>".$r['lname'].' '.$r['fname']."</option>";
			//$out[$r['lname'].' '.$r['fname']] = (int)$r['id'];
		}
		return($out);
	}

	function loadTyp()
	{
		$out = null;
		$abnar_typ_id = -1;
		if(isset($_REQUEST['anbar_typ_id']))
			$abnar_typ_id = (int)$_REQUEST['anbar_typ_id'];
		mysql_class::ex_sql("select `name`,`id` from `anbar_typ` where `id`=$abnar_typ_id order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$out[$r['name']] = (int)$r['id'];
		}
		return($out);
	}
	function hpdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function hpdateback($inp)
	{
		return(audit_class::hamed_pdateBack(perToEnNums($inp)));
	}
	function edit_item($id,$feild,$value)
	{
		if($feild=='ghimat' || $feild=='tedad')
		{
			$anbar_factor_id = new anbar_det_class($id);
			$anbar_factor_id = $anbar_factor_id->anbar_factor_id;
			mysql_class::ex_sqlx("update `anbar_det` set `en`='0' where `anbar_factor_id` = $anbar_factor_id");
		}
		if($feild=='tarikh')
			$value = audit_class::hamed_pdateBack(perToEnNums($value));
		mysql_class::ex_sqlx("update `anbar_det` set `$feild`='$value' where `id`=$id ");
	}
	function add_item()
	{
		$fields = null;
                foreach($_REQUEST as $key => $value)
                        if(substr($key,0,4)=='new_')
                                if($key != 'new_id' && $key != 'new_moeen_id' && $key != 'new_en' && $key != 'new_tedad_kh' )
                                        $fields[substr($key,4)] =perToEnNums($value);
		$anbar_type = new anbar_typ_class($_REQUEST['anbar_typ_id']);
		$fields['tarikh'] = audit_class::hamed_pdateBack(perToEnNums($fields['tarikh']));
		$fields['tedad'] = abs((int)$fields['tedad']);
		$fields['user_id'] = (int)$_SESSION['user_id'];
		$fields['anbar_id'] = ((isset($_REQUEST['anbar_id']))?(int)$_REQUEST['anbar_id']:-1);
		$fields['anbar_factor_id'] = ((isset($_REQUEST['anbar_factor_id']))?(int)$_REQUEST['anbar_factor_id']:-1);
		$query = '';
                $fi = "(";
	        $valu="(";
		$bool = TRUE;
		foreach ($fields as $field => $value)
        	{
		        $fi.="`$field`,";
                        $valu .="'$value',";
			if($anbar_type->typ==-1 && $field!='ghimat' && $value=='') 
				$bool = FALSE;
                }
		
       		$fi=substr($fi,0,-1);
                $valu=substr($valu,0,-1);
		$fi.=")";
        	$valu.=")";
		$query.="insert into `anbar_det` $fi values $valu";
		if($anbar_type->typ==1)
			$fields['ghimat'] = abs((int)$fields['ghimat']);
		else if($anbar_type->typ==-1)
			$fields['ghimat'] = anbar_det_class::calcGhimat($fields['kala_id'],$fields['tedad'],FALSE);
		if($bool)
			if($fields['tedad']>0)
				if($fields['ghimat']>0)
					if($fields['anbar_id']>0)
						if($fields['anbar_factor_id']>0)
							if($anbar_type->typ==-1)
							{
								$fields['ghimat'] = anbar_det_class::calcGhimat($fields['kala_id'],$fields['tedad'],TRUE);
								anbar_det_class::khorooj($fields['anbar_id'],$anbar_type->id,$fields['anbar_factor_id'],$fields['kala_id'],$fields['tedad'],$fields['user_id'],$fields['other_user_id'],$fields['ghimat'],$fields['tarikh']);
							}
							else if($anbar_type->typ==1)
								mysql_class::ex_sqlx($query);
						else
							echo 'شماره فاکتور درست وارد نشده است';
					else
						echo 'انبار انتخاب نشده است';
				else
					echo 'موجودی انبار کافی نیست';
			else
				echo 'تعداد کالا درست وارد نشده است';
		else
			echo '<center>تمامی موارد اطلاعات را وارد کنید</center>';
		
		
	}
	function loadDet($inp)
	{
		$jozeeat ="چاپ";
		 $out = "<u><span style=\"color:Blue;cursor:pointer;\" onclick=\"wopen('jozeeat_kala.php?sel_id=$inp&','',900,300);\">$jozeeat</span></u>";
		return $out;
	}
	//-----------g پردازش مربوط به ثبت نهایی
	if (isset($_REQUEST["access_mod"]) && ($_REQUEST["access_mod"]==1))
	{
		$access_mod = $_REQUEST["access_mod"];
	}
	else
	{
		$access_mod = 0;
	}
	if ($access_mod == 1)
	{
		$factor_id = (isset($_REQUEST["factor_id"]))?$_REQUEST["factor_id"]:0;
		if($factor_id >0)
		{
			$anbar_ids = anbar_det_class::loadByFactorId($factor_id);
			sanadzan_class::deleteAnbarSabt($factor_id);
			for($i = 0; $i<count($anbar_ids);$i++)
			{
				$anbar_type = new anbar_typ_class($anbar_ids[$i]->anbar_typ_id);
				$moshtari_moeen_id = new anbar_factor_class($factor_id);
				$moshtari_moeen_id = $moshtari_moeen_id->moeen_id; 
				$anbar_moeen_id = new anbar_class($anbar_ids[$i]->anbar_id);
				$anbar_moeen_id = $anbar_moeen_id->moeen_id;
				$sanad_rec = sanadzan_class::anbarSabt($factor_id,$anbar_ids[$i]->kala_id,$anbar_type->typ,$anbar_ids[$i]->anbar_id,$anbar_ids[$i]->tedad,$anbar_ids[$i]->ghimat,$moshtari_moeen_id,$anbar_moeen_id,$anbar_ids[$i]->user_id);
				if($sanad_rec[0]>0)
					mysql_class::ex_sqlx("insert into `sanad_anbar` (`sanad_record_id`,`anbar_factor_id`) values ('".$sanad_rec[0]."','$factor_id')");
			}
			mysql_class::ex_sqlx("update `anbar_det` set `en`='1' where `anbar_factor_id` = $factor_id");
			mysql_class::ex_sql("select *,sum(`ghimat`) as `gheimat_kol` from `anbar_det` where `anbar_factor_id` = $factor_id",$q);
		        if($r = mysql_fetch_array($q))
			{
				$anbar_factor_id=$r["anbar_factor_id"];
				$gheimat = $r["gheimat_kol"];
				$anbar_typ_id = $r["anbar_typ_id"];
			
			}
	//		echo $factor_id."<br/>".$gheimat."<br/>".$anbar_typ_id."<br/>".$_SESSION['user_id'];
			$sanad_rec = sanadzan_class::anbarSabtTak($factor_id,$gheimat,$anbar_typ_id,$_SESSION['user_id']);
			if($sanad_rec[0]>0)
				mysql_class::ex_sqlx("insert into `sanad_anbar` (`sanad_record_id`,`anbar_factor_id`) values ('".$sanad_rec[0]."','$factor_id')");
			$show = FALSE;
		}
	}
	//-------------g پایان پردازش مربوط به ثبت نهایی
	if(isset($_REQUEST['anbar_typ_id']) && $_REQUEST['anbar_typ_id']>0)
	{
		$anbar_type = new anbar_typ_class($_REQUEST['anbar_typ_id']);
		$msg = ' رسید ' .$anbar_type->name;
	}
	$anbar_factor_id = -1;
	$anbar_id_def = '';
	mysql_class::ex_sql("select `id` from `anbar` where `en`=1 order by `id` limit 1",$qq);
	if($r = mysql_fetch_array($qq))
		$anbar_id_def = $r['id'];
	$anbar_id = (isset($_REQUEST['anbar_id']))?$_REQUEST['anbar_id']:$anbar_id_def;
	$tmp_anbar_id = ($anbar_id=='')?-2:$anbar_id;
	if(isset($_REQUEST['anbar_factor_id']))
                $anbar_factor_id = (int)$_REQUEST['anbar_factor_id'];
	$grid = new jshowGrid_new("anbar_det","grid1");
	$grid->whereClause="anbar_factor_id=$anbar_factor_id and `anbar_id`=$tmp_anbar_id order by `tarikh`,`kala_id`";
	$grid->setERequest(array('anbar_id'=>$anbar_id));
	$grid->width = '95%';
	$grid->index_width = '20px';
	$grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = "کالا";
	$grid->columnLists[1] = loadKala();
	$grid->columnHeaders[2] = "تاریخ";
        $grid->columnFunctions[2] = "hpdate";
        $grid->columnCallBackFunctions[2] = "hpdateback";
	$grid->columnHeaders[3] = null;
	$grid->columnHeaders[4] = "تعداد";
	$grid->columnHeaders[5] = 'قیمت کل';
	$grid->columnHeaders[6] = 'تحویل دهنده';
	$grid->columnLists[6] = loadUser();
	$grid->columnHeaders[7] = null;
	$grid->columnHeaders[8] = "ورودی/خروجی";
	$grid->columnLists[8] =loadtyp();
	$grid->columnAccesses[8] = 0;
	$grid->columnHeaders[9] = null;
	$grid->columnHeaders[10] = null;
	$grid->columnHeaders[11] = null;
	mysql_class::ex_sql("select `anbar_factor_id`,`en`,sum(`ghimat`) as jam from `anbar_det` where `anbar_factor_id`=$anbar_factor_id and `anbar_id`=$tmp_anbar_id",$qu);
        if($row = mysql_fetch_array($qu))
        {
		if ((int)$row["en"]==1)
		{
			$grid->canAdd = FALSE;
			$grid->canEdit = FALSE;
			$grid->canDelete = FALSE;
			$jam = $row["jam"];
			$grid->footer = "<tr class='showgrid_insert_row'  ><td colspan='4' >&nbsp;</td><td>جمع قیمت کل:$jam</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
		}
        }
	if(anbar_factor_class::isJaari($row['anbar_factor_id']))
		$grid->canEdit = TRUE;
	$grid->editFunction = 'edit_item';
	$grid->canDelete = FALSE;
	$grid->addFunction = 'add_item';
        $grid->intial();
   	$grid->executeQuery();
	$show = FALSE;
	if ($tmp_anbar_id != -2)
		$show = TRUE;
	mysql_class::ex_sql("select `en` from `anbar_det` where `anbar_factor_id`=$anbar_factor_id and `anbar_id`=$tmp_anbar_id",$q);
        while($r = mysql_fetch_array($q))
	{
		((int)$r["en"]==1)?$show=FALSE:$show = $show && TRUE;
	}
        //$out = $grid->getGrid();

$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">کالا</th>
                                        <th style=\"text-align:right;\">تاریخ</th>
                                        <th style=\"text-align:right;\">تعداد</th>
                                        <th style=\"text-align:right;\">قیمت کل</th>
                                        <th style=\"text-align:right;\">تحویل دهنده</th>
                                        <th style=\"text-align:right;\">ورودی / خروجی</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";

 mysql_class::ex_sql("select * from `anbar_det` where `anbar_factor_id`='$anbar_factor_id' and `anbar_id`='$tmp_anbar_id' order by `tarikh`,`kala_id` ",$ss);
$i=1;
while($r=mysql_fetch_array($ss)){
    $id = $r['id'];
    $kala_id = $r['kala_id'];
    mysql_class::ex_sql("select `name` from `kala` where `id` = '$kala_id' ",$k_id);
    $k_id1 = mysql_fetch_array($k_id);
    $kname = $k_id1['name'];
    $tarikh = $r['tarikh'];
    $tar = jdate('Y/n/j',strtotime($tarikh));
    $tedad = $r['tedad'];
    $ghimat = $r['ghimat'];
    $other_user_id = $r['other_user_id'];
    mysql_class::ex_sql("select * from `user` where `id` = '$other_user_id' ",$u_id);
    $u_id1 = mysql_fetch_array($u_id);
    $ufname = $u_id1['fname'];
    $ulname = $u_id1['lname'];
    $uname = $ufname." ".$ulname;
    $anbar_typ_id = $r['anbar_typ_id'];
    mysql_class::ex_sql("select `name` from `anbar_typ` where `id` = '$anbar_typ_id' ",$a_id);
    $a_id1 = mysql_fetch_array($a_id);
    $aname = $a_id1['name'];
    if(fmod($i,2)!=0){
        $out.="
       <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$kname</td>
                                        <td>$tar</td>
                                        <td>$tedad</td>
                                        <td>$ghimat</td>
                                        <td>$uname</td>
                                        <td>$aname</td>
                                        <td>"; if ($grid->canEdit==1) $out.="
                                            <a onclick=\"editGfunc('".$id."','".$kala_id."','".$tar."','".$tedad."','".$ghimat."','".$other_user_id."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>"; if($grid->canDelete==1) $out.="
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
        ";
        $i++;
    }
    else{
        $out.="
        <tr class=\"even\">
                                        <td>$i</td>
                                        <td>$kname</td>
                                        <td>$tar</td>
                                        <td>$tedad</td>
                                        <td>$ghimat</td>
                                        <td>$uname</td>
                                        <td>$aname</td>
                                        <td>"; if ($grid->canEdit==1) $out.="
                                            <a onclick=\"editGfunc('".$id."','".$kala_id."','".$tar."','".$tedad."','".$ghimat."','".$other_user_id."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>"; if($grid->canDelete==1) $out.="
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
        ";
        $i++;
    }
    
}

$out.="</tbody></table></div></div>";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>صورت فاکتور</title>
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

    <script>
			function send_frm()
        	        {
                	        document.getElementById('mod_button').style.display = "none";
				document.getElementById('access_mod').value = "1";
                        	document.getElementById('frm1').submit();
	                }	
		</script>

    
	
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-book"></i>صورت فاکتور</h4>
                            
                        </div>
                        <!-- /.panel-heading -->
                        <?php if ($grid->canAdd==1) echo "
                              <a href=\"#newG\"  data-toggle=\"modal\"><button style='margin:5px;' class=\"btn btn-success btn-lg\"><i class=\"fa fa-plus\"></i>افزودن مورد جدید</button></a>"; ?>  
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                            <form method='post' id="frm1" >
				<?php echo '<b>'.$msg.'</b>'.loadAnbar($anbar_id) ?>
				<input type="hidden" value="0" name="access_mod" id="access_mod" class="inp" >
				<input type="hidden" value="<?php echo $anbar_factor_id;?>" name="factor_id" id="factor_id" class="inp" >
				
                            </form>

			<br/>
			<?php	
		//		echo $out;
			?>
			<input type="button" value="ثبت نهایی" id ="mod_button" class="btn btn-info inp" style="display:<?php echo ($show)?'':'none';?>" onclick="send_frm();">	
			<br/>
			<br/>
			<?php   
                                echo $out;
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
    
<div class="modal fade" id="newG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن مورد</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" name="anbar_id" value="<?php echo $tmp_anbar_id; ?>">
                        <input type="hidden" name="anbar_factor_id" value="<?php echo $anbar_factor_id; ?>">
                        <div class="col-md-4">
                            <label>کالا: </label>
                            <select name="kala1" id="kala1" class="form-control">
                            <?php echo loadKala(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>تاریخ: </label>
                            <input type="text" class="form-control" name="tarikh1" id="datepicker1">
                        </div>
                        <div class="col-md-4">
                            <label>تعداد: </label>
                            <input type="text" class="form-control" name="tedad1">
                        </div>
                        <div class="col-md-4">
                            <label>قیمت کل: </label>
                            <input type="text" class="form-control" name="ghimat1">
                        </div>
                        <div class="col-md-4">
                            <label>تحویل دهنده: </label>
                            <select name="other1" id="other1" class="form-control">
                            <?php echo loadUser();  ?>    
                            </select>
                        </div>
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalG()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="editG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش مورد</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id2" />
                        <div class="col-md-4">
                            <label>کالا: </label>
                            <select name="kala2" id="kala2" class="form-control">
                            <?php echo loadKala(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>تاریخ: </label>
                            <input type="text" class="form-control" name="tarikh2" id="datepicker2">
                        </div>
                        <div class="col-md-4">
                            <label>تعداد: </label>
                            <input type="text" class="form-control" name="tedad2">
                        </div>
                        <div class="col-md-4">
                            <label>قیمت کل: </label>
                            <input type="text" class="form-control" name="ghimat2">
                        </div>
                        <div class="col-md-4">
                            <label>تحویل دهنده: </label>
                            <select name="other2" id="other2" class="form-control">
                            <?php echo loadUser();  ?>    
                            </select>
                        </div>
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalG()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف گارانتی</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="gid" />
                        آیا از حذف مطمئن هستید؟                      
                        
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="deleteFinalG()" type="button" class="btn btn-danger" data-dismiss="modal">حذف</button>
                </div>
            
        </div>
    </div>
</div>
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
	<script language="javascript" >
		<?php
			if($anbar_type->typ == -1)
				echo "if(document.getElementById('new_ghimat'))\n document.getElementById('new_ghimat').style.display = 'none';";
		 ?>
		var inps = document.getElementsByTagName('input');
		var element;
		for(var i=0;i<inps.length;i++)
		{
			element = inps[i].id.split('_');
			if(element[0]=='new' && element[1]=='id')
				inps[i].style.display = 'none';
		}
		</script>
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
        function send_search()
		{
			document.getElementById('mod').value= 2;
			document.getElementById('frm1').submit();
		}
        function sbtFrm()
		{
			document.getElementById('frm1').submit();
		}
        function getPrint()
		{
			document.getElementById('panel-body').style.width = '18cm';
			window.print();
			document.getElementById('panel-body').style.width = 'auto';
		}
        function send_info(khadamat,cost_jam)
		{
			var cost_tedad = document.getElementById('cost_tedad').value;
			if(cost_tedad==0)
				alert('تعداد را وارد کنید');
			else
			{
				if(cost_jam<cost_tedad)
						alert('تعداد وارد شده بیش از مجموع  است');
				else
				{
					if(confirm('آیا کالا با جزئیات از انبار خارج شود؟'))
					{
                        StartLoading();
				
						var gUser_id = document.getElementById('gUser_id').options[document.getElementById('gUser_id').selectedIndex].value;
						var anbar_id = document.getElementById('anbar_id').options[document.getElementById('anbar_id').selectedIndex].value;
						var tarikh = document.getElementById('tarikh1').value;
						var kala_cost = document.getElementById('kala_cost').options[document.getElementById('kala_cost').selectedIndex].value;
                        
                        $.post("cost_anbar.php",{khadamat_id:khadamat,max_tedad:cost_jam,cost_tedad:cost_tedad,kala_cost:kala_cost,tarikh:tarikh,anbar_id:anbar_id,gUser_id:gUser_id},function(data){
                            
                            arr = data.split("_");
                            if(arr[0]=="1"){
                                var brr = arr[1].split("|");
                                var id = brr[0];
                                var cost_kala_id = brr[1];
                                var cost_tedad = brr[2];
                                alert("کالا ثبت شد");
                                $.post("anbar_print.php",{id:id,cost_kala_id:cost_kala_id,cost_tedad:cost_tedad},function(data){
                                    $("#anbar-modal").html(data);
                                    StopLoading();
                                    $('#anbar-modal').modal('show');
                                    
                                });
                            
                            }
                            else 
                                alert(data);

                        });
                        
					}
				}
			}
		}
        function insertFinalG(){
            StartLoading();
            
            var kala1 = $("#kala1 option:selected" ).val();
            var other1 = $("#other1 option:selected" ).val();
            var tarikh1 = $("input[name='tarikh1']").val();
            var tedad1 = $("input[name='tedad1']").val();
            var ghimat1 = $("input[name='ghimat1']").val();
            var anbar_id = $("input[name='anbar_id']").val();
            var anbar_factor_id = $("input[name='anbar_factor_id']").val();
           $.post("anbar_detAjax.php",{kala1:kala1,other1:other1,tarikh1:tarikh1,tedad1:tedad1,ghimat1:ghimat1,anbar_id:anbar_id,anbar_factor_id:anbar_factor_id},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }     
                                    
                                });
        }
        function editGfunc(id,kala_id,tar,tedad,ghimat,other_user_id){
            StartLoading();
            $("input[name='id2']").val(id);
            $("#kala2 option[value="+kala_id+"]").attr('selected','selected');
            $("input[name='tarikh2']").val(tar);
            $("input[name='tedad2']").val(tedad);
            $("input[name='ghimat2']").val(ghimat);
            $("#other2 option[value="+other_user_id+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
             var kala2 = $("#kala2 option:selected" ).val();
            var other2 = $("#other2 option:selected" ).val();
            var tarikh2 = $("input[name='tarikh2']").val();
            var tedad2 = $("input[name='tedad2']").val();
            var ghimat2 = $("input[name='ghimat2']").val();
            var id2 = $("input[name='id2']").val();
           $.post("anbar_detEditAjax.php",{kala2:kala2,other2:other2,tarikh2:tarikh2,tedad2:tedad2,ghimat2:ghimat2,id2:id2},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
            
        }
        function deleteGfunc(gid){
            StartLoading();
            $("input[name='gid']").val(gid);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var gid = $("input[name='gid']").val();
           $.post("garanti_tabagheDeleteAjax.php",{gid:gid},function(data){
               StopLoading();
               if(data=="0")
                   alert("خطا در حذف");
               if(data=="1"){
                   alert("حذف با موفقیت انجام شد");
                   location.reload();
               }
                                          
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