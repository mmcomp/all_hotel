<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	/*
	function loadMoeen($inp)
        {
                $inp = (int)$inp;
                $aj = new anbar_factor_class($inp);
                if($aj->moeen_id>0)
                {
                        $moeen = new moeen_class($aj->moeen_id);
                        $nama = $moeen->name.'('.$moeen->code.')';
                }
                else
                {
                        $nama = 'انتخاب';
                }
                $out = "<u><span onclick=\"window.location =('select_hesab.php?refPage=factor_kala.php?anbar_typ_id=".$_REQUEST['anbar_typ_id']."&sel_id=$inp');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
                return $out;
        }
	*/
	function loadMoeen()
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`name` from `moeen` order by `name` ",$q);
		while($r = mysql_fetch_array($q))
            $out .= "<option value='".$r['id']."'>".$r['name']."</option>";
			//$out[(int)$r['id']]=$r['name'];
		return $out;
	}
	function add_item()
        {
                $fields = null;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id")
                                {
                                        $fields[substr($key,4)] = perToEnNums($value);
                                }
                        }
                }
		if(isset($_REQUEST['anbar_typ_id']) && $_REQUEST['anbar_typ_id']>0)
		{
			$fields["anbar_typ_id"] = $_REQUEST['anbar_typ_id'];
			$fields["user_id"] = (int)$_SESSION['user_id'];
			$fields['tarikh_resid'] = audit_class::hamed_pdateBack(perToEnNums($fields['tarikh_resid']));
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
		        $query="insert into `anbar_factor` $fi values $valu";
		        mysql_class::ex_sqlx($query);
		}
		else
			echo 'نوع ورودی یا خروجی به انبار انتخاب نشده است';
        }
	function hpdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function hpdateback($inp)
	{
		return(audit_class::hamed_pdateBack(perToEnNums($inp)));
	}
	function delete_item($id)
	{
		mysql_class::ex_sqlx("update `anbar` set `en`=0 where `id` = $id");
	}
	function loadUser($inp)
	{
		$out = new user_class($inp);
		return $out->fname.' '.$out->lname;
	}
	function loadDet($inp)
        {
		$out = "";
		$anbar_fc = new anbar_factor_class($inp);
		if($anbar_fc->moeen_id>0)
		{
		        $jozeeat ="صورت فاکتور";
			$out = "<a target='_blank' href='anbar_det.php?anbar_typ_id=".$_REQUEST['anbar_typ_id']."&anbar_factor_id=$inp'>$jozeeat</a>";
			mysql_class::ex_sql("select `en` from `anbar_det` where `anbar_factor_id`='$inp'",$q);
	                while($r = mysql_fetch_array($q))
			{
				if ($r["en"]!=0)	
				{
					$out = "<a target='_blank' href='anbar_det.php?anbar_typ_id=".$_REQUEST['anbar_typ_id']."&anbar_factor_id=$inp'>$jozeeat</a>";
				}
			}
		}
		else
			$out = '<span style="color:#999;">صورت فاکتور</span>';
                return $out;
        }
	function loadPrint($inp)
	{
		$out = "<a target='_blank' href='anbar_print2.php?id=$inp&'>چاپ</a>";
		return $out;
	}
	if(isset($_REQUEST['anbar_typ_id']) && $_REQUEST['anbar_typ_id']>0)
	{
		$anbar_type = new anbar_typ_class($_REQUEST['anbar_typ_id']);
		$msg = ' رسید ' .$anbar_type->name;
	}
	if(isset($_REQUEST['sel_id']))
        {
		$sel_id = $_REQUEST['sel_id'];
		if (isset($_REQUEST['moeen_id']))
	        {
        	        $moeen_id = (int)$_REQUEST['moeen_id'];
	                mysql_class::ex_sqlx("update `anbar_factor` set `moeen_id`=$moeen_id where `id`=$sel_id");
	        }
        	else
	        {
        	        $moeen_id = -1;
	        }
        }
	else
	{
		$sel_id = -1;
	}
	function hamed_pdateBack($inp)
        {
		$inp = perToEnNums($inp);
                $out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
			$y=(int)$tmp[2];
			$m=(int)$tmp[1];
			$d=(int)$tmp[0];
			if ($d>$y)
			{
				$tmp=$y;
				$y=$d;
				$d=$tmp;
			}
			if ($y<1000)
			{
				$y=$y+1300;
			}
			$inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out;
        }
	$anbar_typ_id = (isset($_REQUEST['anbar_typ_id']))?(int)$_REQUEST['anbar_typ_id']:-1;
	$aztarikh = (isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh'])." 00:00:00":date("Y-m-d 00:00:00");
	$tatarikh = (isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh'])." 23:59:59":date("Y-m-d 23:59:59");
    $out="";
    if($anbar_typ_id==1 || $anbar_typ_id==3){
        $out.='<table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">کد فاکتور</th>
                                            <th style="text-align:right;">شماره فاکتور فروشنده</th>
                                            <th style="text-align:right;">نام فروشنده</th>
                                            <th style="text-align:right;">توضیحات</th>
                                            <th style="text-align:right;">حساب معین</th>
                                            <th style="text-align:right;">تاریخ صدور رسید</th>
                                            <th style="text-align:right;">ثبت کننده</th>
                                            <th style="text-align:right;">صورت فاکتور</th>
                                            <th style="text-align:right;">رسید / حواله</th>
                                            <th style="text-align:right;">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
            mysql_class::ex_sql("select * from `anbar_factor` where `anbar_typ_id`=$anbar_typ_id and `tarikh_resid`>='$aztarikh' and `tarikh_resid`<='$tatarikh' order by `id` desc",$ss);
        $i=1;
		while($r = mysql_fetch_array($ss)){
            $id = $r['id'];
            $factor_id = $r['factor_id'];
            $name = $r['name'];
            $tozihat = $r['tozihat'];
            $moeen_id = $r['moeen_id'];
            mysql_class::ex_sql("select * from `moeen` where `id` = '$moeen_id' ",$m_id);
            $m_id1 = mysql_fetch_array($m_id);
            $mname = $m_id1['name'];
            $tarikh_resid = $r['tarikh_resid'];
            $ta_re = jdate('Y/n/j',strtotime($tarikh_resid));
            $user_id = $r['user_id'];
            mysql_class::ex_sql("select * from `user` where `id` = '$user_id' ",$u_id);
            $u_id1 = mysql_fetch_array($u_id);
            $ufname = $u_id1['fname'];
            $ulname = $u_id1['lname'];
            $uname = $ufname." ".$ulname;
            if(fmod($i,2)!=0){
                $out.="<tr class='odd'>
                <td>".$i."</td>
                <td>".$id."</td>
                <td>".$factor_id."</td>
                <td>".$name."</td>
                <td>".$tozihat."</td>
                <td>".$mname."</td>
                <td>".$ta_re."</td>
                <td>".$uname."</td>
                <td>".loadDet($id)."</td>
                <td>".loadPrint($id)."</td>
                <td><a onclick=\"editGfunc('".$id."','".$factor_id."','".$name."','".$tozihat."','".$moeen_id."','".$ta_re."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a></td>
                </tr>";
                $i++;
            }
            else{
                $out.="<tr class='even'>
                <td>".$i."</td>
                <td>".$id."</td>
                <td>".$factor_id."</td>
                <td>".$name."</td>
                <td>".$tozihat."</td>
                <td>".$mname."</td>
                <td>".$ta_re."</td>
                <td>".$uname."</td>
                <td>".loadDet($id)."</td>
                <td>".loadPrint($id)."</td>
                <td><a onclick=\"editGfunc('".$id."','".$factor_id."','".$name."','".$tozihat."','".$moeen_id."','".$ta_re."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a></td>
                </tr>";
                $i++;
            }
        }
        $out.="</tbody></table>";
    }
    else if($anbar_typ_id==2){
        $out.='<table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">کد فاکتور</th>
                                            <th style="text-align:right;">نام خریدار</th>
                                            <th style="text-align:right;">توضیحات</th>
                                            <th style="text-align:right;">حساب معین</th>
                                            <th style="text-align:right;">تاریخ صدور رسید</th>
                                            <th style="text-align:right;">ثبت کننده</th>
                                            <th style="text-align:right;">صورت فاکتور</th>
                                            <th style="text-align:right;">رسید / حواله</th>
                                            <th style="text-align:right;">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
        mysql_class::ex_sql("select * from `anbar_factor` where `anbar_typ_id`=$anbar_typ_id and `tarikh_resid`>='$aztarikh' and `tarikh_resid`<='$tatarikh' order by `id` desc",$ss);
        $i=1;
		while($r = mysql_fetch_array($ss)){
            $id = $r['id'];
            $factor_id = $r['factor_id'];
            $name = $r['name'];
            $tozihat = $r['tozihat'];
            $moeen_id = $r['moeen_id'];
            mysql_class::ex_sql("select * from `moeen` where `id` = '$moeen_id' ",$m_id);
            $m_id1 = mysql_fetch_array($m_id);
            $mname = $m_id1['name'];
            $tarikh_resid = $r['tarikh_resid'];
            $ta_re = jdate('Y/n/j',strtotime($tarikh_resid));
            $user_id = $r['user_id'];
            mysql_class::ex_sql("select * from `user` where `id` = '$user_id' ",$u_id);
            $u_id1 = mysql_fetch_array($u_id);
            $ufname = $u_id1['fname'];
            $ulname = $u_id1['lname'];
            $uname = $ufname." ".$ulname;
            if(fmod($i,2)!=0){
                $out.="<tr class='odd'>
                <td>".$i."</td>
                <td>".$id."</td>
                <td>".$name."</td>
                <td>".$tozihat."</td>
                <td>".$mname."</td>
                <td>".$ta_re."</td>
                <td>".$uname."</td>
                <td>".loadDet($id)."</td>
                <td>".loadPrint($id)."</td>
                <td><a onclick=\"editGfunc2('".$id."','".$name."','".$tozihat."','".$moeen_id."','".$ta_re."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a></td>
                
                </tr>";
                $i++;
            }
            else{
                $out.="<tr class='even'>
                <td>".$i."</td>
                <td>".$id."</td>
                <td>".$name."</td>
                <td>".$tozihat."</td>
                <td>".$mname."</td>
                <td>".$ta_re."</td>
                <td>".$uname."</td>
                <td>".loadDet($id)."</td>
                <td>".loadPrint($id)."</td>
                <td><a onclick=\"editGfunc2('".$id."','".$name."','".$tozihat."','".$moeen_id."','".$tarikh_resid."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a></td>
                </tr>";
                $i++;
            }
        }
        $out.="</tbody></table>";
        
    }

	$grid = new jshowGrid_new("anbar_factor","grid1");
	$grid->whereClause="`anbar_typ_id`=$anbar_typ_id and `tarikh_resid`>='$aztarikh' and `tarikh_resid`<='$tatarikh' order by `id` desc";
	$grid->width ='95%';
	$grid->index_width = '20px';
	$grid->columnHeaders[0] = "کد فاکتور";
	if((int)$anbar_type->typ==1)
	{
		$grid->columnHeaders[2] = 'نام فروشنده';
		$grid->columnHeaders[1] = 'شماره فاکتور فروشنده';
	}
	else if((int)$anbar_type->typ==-1)
	{
		$grid->columnHeaders[2] = 'نام خریدار';
		$grid->columnHeaders[1] = null;
	}
    $grid->columnHeaders[3] = 'توضیحات';
	$grid->columnHeaders[4] = 'حساب معین';
	$grid->columnLists[4]=loadMoeen();
	//$grid->enableComboAjax[4] = TRUE;
	$grid->list2 = TRUE;
	$grid->columnHeaders[5] = 'تاریخ صدور رسید';
    $grid->columnFunctions[5] = "hpdate";
    $grid->columnCallBackFunctions[5] = 'hpdateback';
	$grid->columnHeaders[6] = null;
	$grid->columnHeaders[7] = 'ثبت کننده';
	$grid->columnFunctions[7]='loadUser';
	$grid->columnAccesses[7] = 0;
	//$grid->addFeild('id');
	//$grid->columnHeaders[8] = "حساب معین";
	//$grid->columnFunctions[8]='loadMoeen';
	$grid->addFeild('id');
    $grid->columnHeaders[8] = 'صورت فاکتور';
    $grid->columnFunctions[8]='loadDet';
	$grid->addFeild('id');
    $grid->columnHeaders[9] = 'رسید/حواله';
    $grid->columnFunctions[9]='loadPrint';
	$grid->addFunction = 'add_item';
	$grid->columnAccesses[0] = 0;
	$grid->showAddDefault = FALSE;
    $grid->intial();
   	$grid->executeQuery();
    //$out = $grid->getGrid();
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>رسید انبار</title>
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
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/bootstrap-datepicker.fa.min.js"></script>
    
      <script>
    $(document).ready(function(){
    
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
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker4").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker5").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });
            
                $("#datepicker6").datepicker({
                    dateFormat: "yy/mm/dd",
                    changeMonth: true,
                    changeYear: true
                });                
        
        
    });
     
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-shopping-cart"></i><?php echo $msg; ?></h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                             <form id="frm1" >
                                 <?php if($anbar_typ_id==1) echo "
                            <a href=\"#newG\"  data-toggle=\"modal\"><button class=\"btn btn-success btn-lg\"><i class=\"fa fa-plus\"></i>افزودن رسید جدید</button></a>"; else if ($anbar_typ_id==2) echo "<a href=\"#newG2\"  data-toggle=\"modal\"><button class=\"btn btn-success btn-lg\"><i class=\"fa fa-plus\"></i>افزودن رسید جدید</button></a>";?>
                            <br/>
                            <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">از تاریخ:</label> 
                                    <div class="col-md-8"><input type="text" name="aztarikh" id="datepicker1" value="<?php echo (isset($_REQUEST['aztarikh']))?($_REQUEST['aztarikh']):audit_class::hamed_pdate((date('Y-m-d'))); ?>" class="form-control inp" />
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">تا تاریخ:</label> 
                                    <div class="col-md-8"><input type="text" name="tatarikh" id="datepicker2" value="<?php echo (isset($_REQUEST['tatarikh']))?($_REQUEST['tatarikh']):audit_class::hamed_pdate((date('Y-m-d'))); ?>" class="form-control inp" />
                                    
                                    </div>
                                </div>
                            <input type="hidden" name="anbar_typ_id" id="anbar_typ_id" value="<?php echo $anbar_typ_id; ?>" class="inp" />
                                <div class="col-md-4" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left" onclick="document.getElementById('frm1').submit();">جستجو</button></div>
                                </div>
                           <?php echo $out; ?>
                           
                            
                            
                            
                                    
                             </form> 
                               
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
                    <h4 class="modal-title">افزودن رسید</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-4">
                            <label>شماره فاکتور فروشنده: </label>
                            <input type="text" class="form-control" name="factor_id" />
                        </div>
                        <div class="col-md-4">
                            <label>نام فروشنده: </label>
                            <input type="text" class="form-control" name="cname" />
                        </div>
                        <div class="col-md-4">
                            <label>حساب معین: </label>
                            <select class="form-control" id="moeen_id">
                             <?php echo loadMoeen() ?>   
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>توضیحات: </label>
                            <input type="text" class="form-control" name="toz" />
                        </div>
                        <div class="col-md-4">
                            <label>تاریخ صدور رسید: </label>
                            <input id="datepicker3" type="text" class="form-control" name="tarikh" />
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
    <div class="modal fade" id="newG2">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">افزودن رسید</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-6">
                            <label>نام خریدار: </label>
                            <input type="text" class="form-control" name="cname1" />
                        </div>
                        <div class="col-md-6">
                            <label>حساب معین: </label>
                            <select class="form-control" id="moeen_id1">
                             <?php echo loadMoeen() ?>   
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>توضیحات: </label>
                            <input type="text" class="form-control" name="toz1" />
                        </div>
                        <div class="col-md-6">
                            <label>تاریخ صدور رسید: </label>
                            <input id="datepicker4" type="text" class="form-control" name="tarikh1" />
                        </div>
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="insertFinalG1()" type="button" class="btn btn-warning" data-dismiss="modal">افزودن</button>
                </div>
            
        </div>
    </div>
</div>
    <div class="modal fade" id="editG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش رسید</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id2" />
                        <div class="col-md-4">
                            <label>شماره فاکتور فروشنده: </label>
                            <input type="text" class="form-control" name="factor_id2" />
                        </div>
                        <div class="col-md-4">
                            <label>نام فروشنده: </label>
                            <input type="text" class="form-control" name="cname2" />
                        </div>
                        <div class="col-md-4">
                            <label>حساب معین: </label>
                            <select class="form-control" id="moeen_id2">
                             <?php echo loadMoeen() ?>   
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>توضیحات: </label>
                            <input type="text" class="form-control" name="toz2" />
                        </div>
                        <div class="col-md-4">
                            <label>تاریخ صدور رسید: </label>
                            <input id="datepicker5" type="text" class="form-control" name="tarikh2" />
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
    <div class="modal fade" id="editG1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش رسید</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id3" />
                        <div class="col-md-4">
                            <label>نام خریدار: </label>
                            <input type="text" class="form-control" name="cname3" />
                        </div>
                        <div class="col-md-4">
                            <label>حساب معین: </label>
                            <select class="form-control" id="moeen_id3">
                             <?php echo loadMoeen() ?>   
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>توضیحات: </label>
                            <input type="text" class="form-control" name="toz3" />
                        </div>
                        <div class="col-md-4">
                            <label>تاریخ صدور رسید: </label>
                            <input id="datepicker6" type="text" class="form-control" name="tarikh3" />
                        </div>
                       
                    </form>	
                </div>
			
                <div class="modal-footer">
				    <button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                    <button onclick="editFinalG2()" type="button" class="btn btn-warning" data-dismiss="modal">ویرایش</button>
                </div>
            
        </div>
    </div>
</div>
   
    <div class="modal fade" id="deleteG">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">حذف رسید</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id4" />
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
	
	<!-- Clock -->
	<script src="<?php echo $root ?>inc/digital-clock/assets/js/script.js"></script>
	
	<!-- news ticker -->
	
	<!-- DATE RANGE PICKER -->
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/moment.min.js"></script>
	<script src="<?php echo $root ?>js/bootstrap-daterangepicker/daterangepicker.min.js"></script>
	
	<!-- DATE RANGE PICKER -->
    <script src="<?php echo $root ?>inc/bootstrap-datepicker.js"></script>
	<script src="<?php echo $root ?>inc/bootstrap-datepicker.fa.js"></script>
	<!-- ISOTOPE -->
	<script type="text/javascript" src="<?php echo $root ?>js/isotope/jquery.isotope.min.js"></script>
	<!-- COLORBOX -->
	<script type="text/javascript" src="<?php echo $root ?>js/colorbox/jquery.colorbox.min.js"></script>
    <script language="javascript" >
			if(document.getElementById('new_user_id'))
				document.getElementById('new_user_id').style.display = 'none';
			var inps = document.getElementsByTagName('input');
			var element;
			for(var i=0;i<inps.length;i++)
			{
				element = inps[i].id.split('_');
				if(element[0]=='new' && element[1]=='id')
					inps[i].style.display = 'none';
			}
		</script>
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
    
            var factor_id = $("input[name='factor_id']").val();
            var cname = $("input[name='cname']").val();
            var toz = $("input[name='toz']").val();
            var tarikh = $("input[name='tarikh']").val();
            var moeen_id = $("#moeen_id option:selected" ).val();
            $.post("factor_kalaAjax.php",{factor_id:factor_id,cname:cname,toz:toz,tarikh:tarikh,moeen_id:moeen_id},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function insertFinalG1(){
            StartLoading();
    
            var cname = $("input[name='cname1']").val();
            var toz = $("input[name='toz1']").val();
            var tarikh = $("input[name='tarikh1']").val();
            var moeen_id = $("#moeen_id1 option:selected" ).val();
            $.post("factor_kala1Ajax.php",{cname:cname,toz:toz,tarikh:tarikh,moeen_id:moeen_id},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        

        function editGfunc(id,factor_id,name,tozihat,moeen_id,tarikh_resid){
            StartLoading();
            $("input[name='id2']").val(id);
            $("input[name='cname2']").val(name);
            $("input[name='factor_id2']").val(factor_id);
            $("input[name='toz2']").val(tozihat);
            $("input[name='tarikh2']").val(tarikh_resid);
            $("#moeen_id2 option[value="+moeen_id+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editGfunc2(id,name,tozihat,moeen_id,tarikh_resid){
            StartLoading();
            $("input[name='id3']").val(id);
            $("input[name='cname3']").val(name);
            $("input[name='toz3']").val(tozihat);
            $("input[name='tarikh3']").val(tarikh_resid);
            $("#moeen_id3 option[value="+moeen_id+"]").attr('selected','selected');
            $('#editG1').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var cname2 = $("input[name='cname2']").val();
            var factor_id2 = $("input[name='factor_id2']").val();
            var toz2 = $("input[name='toz2']").val();
            var tarikh2 = $("input[name='tarikh2']").val();
            var moeen_id2 = $("#moeen_id2 option:selected" ).val();
            
           $.post("factor_kalaEditAjax.php",{id2:id2,cname2:cname2,factor_id2:factor_id2,toz2:toz2,tarikh2:tarikh2,moeen_id2:moeen_id2},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
            
        }
        function editFinalG2(){
            StartLoading();
            var id2 = $("input[name='id3']").val();
            var cname2 = $("input[name='cname3']").val();
            var toz2 = $("input[name='toz3']").val();
            var tarikh2 = $("input[name='tarikh3']").val();
            var moeen_id2 = $("#moeen_id3 option:selected" ).val();
            
           $.post("factor_kala1EditAjax.php",{id2:id2,cname2:cname2,toz2:toz2,tarikh2:tarikh2,moeen_id2:moeen_id2},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
            
        }
        function deleteGfunc(id){
            StartLoading();
            $("input[name='id4']").val(id);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var id4 = $("input[name='id4']").val();
           $.post("factor_kalaDeleteAjax.php",{id4:id4},function(data){
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