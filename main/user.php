<?php
session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$is_admin = FALSE;
	if($se->detailAuth('all'))
		$is_admin = TRUE;
	$GLOBALS['msg'] = '';
	function loadDaftar()
	{
		$out="";
		mysql_class::ex_sql("select `name`,`id` from `daftar`".(($_SESSION['daftar_id']!=49)?' where id = '.$_SESSION['daftar_id']:'')." order by `name`",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
            $out.="<option value='".$r["id"]."'>".$r["name"]."</option>";
			//$out[$r["name"]]=(int)$r["id"];
		}
		return $out;
	}
        function loadAjans()
        {
                $out.="<option value='-1'>همه</option>";
                mysql_class::ex_sql("select `name`,`id` from `ajans` order by `name`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                    $out.="<option value='".$r["id"]."'>".$r["name"]."</option>";
                        //$out[$r["name"]]=(int)$r["id"];
                }
                return $out;
        }
	function loadType()
	{
		$out=array();
		$out['مدیر']=0;
		$out['عادی']=1;
		return $out;
	}
        function loadGroups($se)
        {
                $out = "";
                mysql_class::ex_sql('select `name`,`id` from `grop` where `en`=1 order by `name`',$q);
                while($r = mysql_fetch_array($q))
			if($se->detailAuth('all') || $se->detailAuth($r['name']) || (int)$r['id'] == (int)$_SESSION['typ'])
                    $out.="<option value='".$r['id']."'>".$r['name']."</option>";
	                        //$out[$r['name']] = (int)$r['id'];
                return($out);
        }
        function add_item($f)
        {
		$conf = new conf;
                $fields = null;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id")
                                {
                                        $fields[substr($key,4)] = $value;
                                }
                        }
                }
		$canAdd = TRUE;
		if($fields['ajans_id']!=-1 && user_class::ajansUserCount()>=$conf->limit_ajans_user )
			$canAdd = FALSE;
		if($canAdd)
		{
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
		        $query="insert into `user` $fi values $valu";
		        mysql_class::ex_sqlx($query);
		}
		else
			$GLOBALS['msg'] = 'با توجه به ظرفیت سرور امکان تعریف کاربر آژانسی بیش از تعداد موجود نمی باشد';
		/*
		var_dump($f);
		var_dump(jshowGrid_new::createAddQuery($f));
		*/
        }
	function edit_item($id,$feild,$value)
	{
		$conf = new conf;
		$se = security_class::auth((int)$_SESSION['user_id']);
		$canAdd = TRUE;
		if($feild=='ajans_id')
		{
			$pre_val = -1;
			mysql_class::ex_sql("select `ajans_id` from `user` where `id`=$id ",$q);
			if($r=mysql_fetch_array($q))
				$pre_val = $r['ajans_id'];
			if($pre_val==-1 && $value!=-1)
				if(user_class::ajansUserCount()>=$conf->limit_ajans_user )
					$canAdd = FALSE;
		}
		if($feild=='pass')
		{
			if((!$se->detailAuth('all')) && ($id!=$_SESSION['user_id']))
				echo "<script>alert('شما مجاز به تغییر  رمز عبور نیستید');</script>";
			else
				mysql_class::ex_sqlx("update `user` set `pass`='$value' where `id`=$id ");
		}
		elseif($canAdd)
			mysql_class::ex_sqlx("update `user` set `$feild`='$value' where `id`=$id ");
		else
			$GLOBALS['msg'] = 'با توجه به ظرفیت سرور امکان تعریف کاربر آژانسی بیش از تعداد موجود نمی باشد';
	}
	function hidePass_all($inp)
	{
		$out = '';
		return($out);
	}
	function hidePass($inp)
	{
		if($inp != (int)$_SESSION['user_id'])
			$out = "*****";
		else
            $out = "*****";
			//$out = "<a target=\"_blank\" href=\"changepass.php\" >تغییر کلمه عبور</a>";
		return($out);
	}
	function loadHozoor($id)
	{
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('hozoor.php?p_id=$id&','',700,400);\" >$id</span></u>";
                return($out);
	}
	$wer = '';
	if(!$is_admin && $se->detailAuth('middle_manager'))
		$wer = '';
	else if(!$is_admin && !$se->detailAuth('middle_manager'))
		$wer = ' and `id` = '.(int)$_SESSION['user_id'];
	else
		$wer = '';
/*	else if($se->detailAuth('middle_manager'))
	{
		$werr = '';
		for($i = 0;$i < count($se->allDetails);$i++)
			$werr .= (($werr != '')?' or ':' where ')." `name`='".$se->allDetails[$i]."' ";
		mysql_class::ex_sql("select `id`,`name` from `grop` $werr  order by `name`",$qq);
		while($rr = mysql_fetch_array($qq))
		{
			$wer .= (($wer != '')?' or ':' and (').' `typ` = '.$rr['id'];
		}
		if($wer == '')
			$wer = ' and `id` = '.(int)$_SESSION['user_id'];
		else
			$wer .= ' or `id` = '.(int)$_SESSION['user_id'].')';
	}*/
	$groups = loadGroups($se);
	$grid = new jshowGrid_new("user","grid1");
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->whereClause = " `user` != 'mehrdad' $wer order by `id`";
	$grid->columnHeaders[0] ='کد پرسنلی';
	$grid->columnAccesses[0] = 0;
	$grid->columnHeaders[1]="دفتر";
	$grid->columnFilters[1] = TRUE;
	$grid->columnHeaders[2] = "آژانس";
	$grid->columnHeaders[3]="نام";
	$grid->columnFilters[3] = TRUE;
	$grid->columnHeaders[4]="نام خانوادگی";
	$grid->columnFilters[4] = TRUE;
	$grid->columnHeaders[5]="نام کاربری";
	$grid->columnFilters[5] = TRUE;
	$grid->columnAccesses[5] = 1;
	$grid->columnHeaders[6]="رمز عبور";
	if(!$is_admin)
	{
		$grid->columnFunctions[6] = "hidePass";
		$grid->columnCallBackFunctions[6] = "hidePassAll";
	}
	$grid->columnHeaders[7]="گروه کاربری";
	$grid->columnFilters[7] = TRUE;
	$grid->columnLists[7]=$groups;
	$grid->columnHeaders[8]="شماره کارت";
	$grid->columnHeaders[9]="ساعت موظف ورود";
	$grid->columnHeaders[10]="ساعت موظف خروج";
	$grid->columnHeaders[11]="ساعت موظف ورود<br/>شیفت دو";
	$grid->columnHeaders[12]="ساعت موظف خروج<br/>شیفت دو";
	$grid->columnHeaders[13]=null;
	$grid->columnHeaders[14]= "زمان حضور";
	$grid->columnLists[1]=loadDaftar();
	$grid->columnLists[2] = loadAjans();
	$grid->editFunction = 'edit_item';
	for($i = 0;$i < count($grid->columnHeaders);$i++)
		$grid->columnAccesses[$i] = 0;
	$grid->columnAccesses[6] = 1;
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	if($is_admin)
	{
		for($i = 1;$i < count($grid->columnHeaders);$i++)
        	        $grid->columnAccesses[$i] = 1;
	        $grid->canAdd = TRUE;
		$grid->addFunction = 'add_item';
	}
	else if($se->detailAuth('middle_manager'))
	{	
		$grid->columnFunctions[6] = 'hidePass_all';
		$grid->canAdd = TRUE;
		$grid->addFunction = 'add_item';
		//$grid->columnFunctions[6] = 'hidePass';
	}
	else
	{
		$grid->columnFunctions[6] = 'hidePass_all';
		$grid->canAdd = FALSE;
	}
/*	if($is_admin && !$se->detailAuth('middle_manager'))
        {
	        for($i = 1;$i < count($grid->columnHeaders);$i++)
        	        $grid->columnAccesses[$i] = 1;
        	$grid->canAdd = TRUE;
	        $grid->canDelete = TRUE;
        	$grid->columnLists[7]=$groups;
		$grid->addFunction = 'add_item';
		$grid->columnFunctions[0] = 'loadHozoor';
	}
	else if(!$is_admin && !$se->detailAuth('middle_manager'))
	{
		$grid->columnAccesses[6] = 1;
		$grid->columnHeaders[7]=null;
		$grid->columnAccesses[6] = 0;
	}
	else if($se->detailAuth('middle_manager') && !$is_admin)
	{
		for($i = 1;$i < count($grid->columnHeaders);$i++)
                        $grid->columnAccesses[$i] = 0;
                //$grid->canAdd = TRUE;
                $grid->columnLists[7]=$groups;
                $grid->addFunction = 'add_item';
		$grid->fieldList[7] = 'id';
		$grid->columnFunctions[6] = 'hidePass';
		$grid->columnAccesses[6] = 0;
		$grid->columnFunctions[0] = 'loadHozoor';
		$grid->columnAccesses[6] = 0;
	}*/
	$grid->intial();
	$grid->executeQuery();
	if($grid->canAdd)
	{
		$grid->canAdd = FALSE;
		if($grid->getRowCount()<$conf->limit_kol_user)
			$grid->canAdd = TRUE;
	}
	//$out = $grid->getGrid();
$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">کد پرسنلی</th>
                                        <th style=\"text-align:right;\">دفتر</th>
                                        <th style=\"text-align:right;\">آژانس</th>
                                        <th style=\"text-align:right;\">نام</th>
                                        <th style=\"text-align:right;\">نام خانوادگی</th>
                                        <th style=\"text-align:right;\">نام کاربری</th>
                                        <th style=\"text-align:right;\">رمز عبور</th>
                                        <th style=\"text-align:right;\">گروه کاربری</th>
                                        <th style=\"text-align:right;\">شماره کارت</th>
                                        <th style=\"text-align:right;\">ساعت موظف ورود</th>
                                        <th style=\"text-align:right;\">ساعت موظف خروج</th>
                                        <th style=\"text-align:right;\">ساعت موظف ورود شیفت دو</th>
                                        <th style=\"text-align:right;\">ساعت موظف خروج شیفت دو</th>
                                        <th style=\"text-align:right;\">زمان حضور</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";
$www = ' and id not in (517,524) ';
if($_SESSION['daftar_id']!=49){
	$www = ' and `daftar_id` = '.$_SESSION['daftar_id'];
}
if($_SESSION['typ']!='0'){
	$www .= ' and `id` = '.$_SESSION['user_id'];
}

 mysql_class::ex_sql("select * from `user` where `user` != 'mehrdad' $www order by `id` ",$ss);
$i=1;
while($r=mysql_fetch_array($ss)){
    $id = $r['id'];
   
    $ajans_id = $r['ajans_id'];
    if($ajans_id!=-1){
    mysql_class::ex_sql("select `name` from `ajans` where `id` = '$ajans_id' ",$a_id);
    $a_id1 = mysql_fetch_array($a_id);
    $ajname = $a_id1['name'];
    }
    else
        $ajname="همه";
    
    $daftar_id = $r['daftar_id'];
    mysql_class::ex_sql("select `name` from `daftar` where `id` = '$daftar_id' ",$d_id);
    $d_id1 = mysql_fetch_array($d_id);
    $dname = $d_id1['name'];
    
    $fname = $r['fname'];
    
    $lname = $r['lname'];
    
    $user = $r['user'];
    
    $pass = $r['pass'];
    
    $typ = $r['typ'];
    mysql_class::ex_sql("select `name` from `grop` where `en`=1 and `id`='$typ'",$qq);
    $rr = mysql_fetch_array($qq);
    $ty = $rr['name'];
    
    $num_card = $r['num_card'];
    
    $vorood = $r['vorood'];
    
    $khorooj = $r['khorooj'];
    
    $vorood1 = $r['vorood1'];
    
    $khorooj1 = $r['khorooj1'];
        
    $zaman_hozur = $r['zaman_hozur'];
    
    if(fmod($i,2)!=0){
        $out.="
       <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$id</td>
                                        <td>$dname</td>
                                        <td>$ajname</td>
                                        <td>$fname</td>
                                        <td>$lname</td>
                                        <td>$user</td>
                                        <td>";if(!$is_admin)
	{
		$out.= "".hidePass($id)."";}
        else{
		$out.="$pass";}
        $out.="
                                        </td>
                                        <td>$ty</td>
                                        <td>$num_card</td>
                                        <td>$vorood</td>
                                        <td>$khorooj</td>
                                        <td>$vorood1</td>
                                        <td>$khorooj1</td>
                                        <td>$zaman_hozur</td>
                                        <td>";
        if($is_admin){
            $out.="
                                            <a onclick=\"editGfunc('".$id."','".$ajans_id."','".$daftar_id."','".$fname."','".$lname."','".$user."','".$pass."','".$typ."','".$num_card."','".$vorood."','".$khorooj."','".$vorood1."','".$khorooj1."','".$zaman_hozur."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>";}
        else {
            $out.=" <a onclick=\"editGfunc1('".$id."','".$pass."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>";
        }
        $out.="
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
                                        <td>$id</td>
                                        <td>$dname</td>
                                        <td>$ajname</td>
                                        <td>$fname</td>
                                        <td>$lname</td>
                                        <td>$user</td>
                                        <td>";if(!$is_admin)
	{
		$out.= "".hidePass($id)."";}
        else{
		$out.="$pass";}
        $out.="
                                        </td>
                                        <td>$ty</td>
                                        <td>$num_card</td>
                                        <td>$vorood</td>
                                        <td>$khorooj</td>
                                        <td>$vorood1</td>
                                        <td>$khorooj1</td>
                                        <td>$zaman_hozur</td>
                                        <td>";
        if($is_admin){
            $out.="
                                            <a onclick=\"editGfunc('".$id."','".$ajans_id."','".$daftar_id."','".$fname."','".$lname."','".$user."','".$pass."','".$typ."','".$num_card."','".$vorood."','".$khorooj."','".$vorood1."','".$khorooj1."','".$zaman_hozur."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>";}
        else {
            $out.=" <a onclick=\"editGfunc1('".$id."','".$pass."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>";
        }
        $out.="
                                           <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
        ";
        $i++;
    }
    
}
$out.="</tbody></table>";

$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>مدیریت کاربران</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-dollar"></i>مدیریت کاربران</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                          
                             <?php echo '<h2>'.$GLOBALS['msg'].'</h2>' ?>  
                            <?php
                            
                            if($is_admin)
	{
		echo "<a href=\"#newG\"  data-toggle=\"modal\"><button class=\"btn btn-success btn-lg\"><i class=\"fa fa-plus\"></i>افزودن کاربر جدید</button></a>";
	}
	else if($se->detailAuth('middle_manager'))
	{	
		echo "<a href=\"#newG\"  data-toggle=\"modal\"><button class=\"btn btn-success btn-lg\"><i class=\"fa fa-plus\"></i>افزودن کاربر جدید</button></a>";
	}
	else
	{
		echo "";
	}?>
                            
                            <br/>
                           <?php echo $out; ?>
                           
                            
                            
                            
                                    
                              
                               
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
                    <h4 class="modal-title">افزودن کاربر</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-3">
                            <label>دفتر: </label>
                            <select class="form-control" id="daftar1">
                            <?php echo loadDaftar() ?>    
                            </select>
                            
                        </div>
                        <div class="col-md-3">
                            <label>آژانس: </label>
                            <select class="form-control" id="ajans1">
                            <?php echo loadAjans() ?>    
                            </select>
                            
                        </div>
                        <div class="col-md-3">
                            <label>نام: </label>
                            <input type="text" class="form-control" name="fname1" />
                        </div>
                        <div class="col-md-3">
                            <label>نام خانوادگی: </label>
                            <input type="text" class="form-control" name="lname1" />
                        </div>
                        <div class="col-md-3">
                            <label>نام کاربری: </label>
                            <input type="text" class="form-control" name="user1" />
                        </div>
                        <div class="col-md-3">
                            <label>رمز عبور: </label>
                            <input type="text" class="form-control" name="pass1" />
                        </div>
                        <div class="col-md-3">
                            <label>گروه کاربری: </label>
                            <select class="form-control" id="group1">
                            <?php echo loadGroups($se) ?>    
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>شماره کارت: </label>
                            <input type="text" class="form-control" name="ccart1" />
                        </div>
                        <div class="col-md-3">
                            <label>ساعت موظف ورود: </label>
                            <input type="text" class="form-control" name="vh1" />
                        </div>
                        <div class="col-md-3">
                            <label>ساعت موظف خروج: </label>
                            <input type="text" class="form-control" name="kh1" />
                        </div>
                        <div class="col-md-3">
                            <label>ساعت موظف ورود شیفت دو: </label>
                            <input type="text" class="form-control" name="vh12" />
                        </div>
                        <div class="col-md-3">
                            <label>ساعت موظف خروج شیفت دو: </label>
                            <input type="text" class="form-control" name="kh12" />
                        </div>
                        <div class="col-md-3">
                            <label>زمان حضور: </label>
                            <input type="text" class="form-control" name="zh1" />
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
                    <h4 class="modal-title">ویرایش کاربر</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id2" />
                        <div class="col-md-3">
                            <label>دفتر: </label>
                            <select class="form-control" id="daftar2">
                            <?php echo loadDaftar() ?>    
                            </select>
                            
                        </div>
                        <div class="col-md-3">
                            <label>آژانس: </label>
                            <select class="form-control" id="ajans2">
                            <?php echo loadAjans() ?>    
                            </select>
                            
                        </div>
                        <div class="col-md-3">
                            <label>نام: </label>
                            <input type="text" class="form-control" name="fname2" />
                        </div>
                        <div class="col-md-3">
                            <label>نام خانوادگی: </label>
                            <input type="text" class="form-control" name="lname2" />
                        </div>
                        <div class="col-md-3">
                            <label>نام کاربری: </label>
                            <input type="text" class="form-control" name="user2" />
                        </div>
                        <div class="col-md-3">
                            <label>رمز عبور: </label>
                            <input type="text" class="form-control" name="pass2" />
                        </div>
                        <div class="col-md-3">
                            <label>گروه کاربری: </label>
                            <select class="form-control" id="group2">
                            <?php echo loadGroups($se) ?>    
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>شماره کارت: </label>
                            <input type="text" class="form-control" name="ccart2" />
                        </div>
                        <div class="col-md-3">
                            <label>ساعت موظف ورود: </label>
                            <input type="text" class="form-control" name="vh2" />
                        </div>
                        <div class="col-md-3">
                            <label>ساعت موظف خروج: </label>
                            <input type="text" class="form-control" name="kh2" />
                        </div>
                        <div class="col-md-3">
                            <label>ساعت موظف ورود شیفت دو: </label>
                            <input type="text" class="form-control" name="vh22" />
                        </div>
                        <div class="col-md-3">
                            <label>ساعت موظف خروج شیفت دو: </label>
                            <input type="text" class="form-control" name="kh22" />
                        </div>
                        <div class="col-md-3">
                            <label>زمان حضور: </label>
                            <input type="text" class="form-control" name="zh2" />
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
    <div class="modal fade" id="editG2">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
			
                <div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">ویرایش کاربر</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id22" />
                        
                        <div class="col-md-3">
                            <label>رمز عبور: </label>
                            <input type="text" class="form-control" name="pass22" />
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
                    <h4 class="modal-title">حذف کاربر</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id3" />
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
            


            var kh12 = $("input[name='kh12']").val();
            var zh1 = $("input[name='zh1']").val();
            var fname1 = $("input[name='fname1']").val();
            var lname1 = $("input[name='lname1']").val();
            var user1 = $("input[name='user1']").val();
            var pass1 = $("input[name='pass1']").val();
            var vh12 = $("input[name='vh12']").val();
            var ccart1 = $("input[name='ccart1']").val();
            var vh1 = $("input[name='vh1']").val();
            var kh1 = $("input[name='kh1']").val();
            var daftar1 = $("#daftar1 option:selected" ).val();
            var ajans1 = $("#ajans1 option:selected" ).val();
            var group1 = $("#group1 option:selected" ).val();
           $.post("userAjax.php",{kh12:kh12,zh1:zh1,fname1:fname1,lname1:lname1,user1:user1,pass1:pass1,vh12:vh12,ccart1:ccart1,vh1:vh1,kh1:kh1,daftar1:daftar1,ajans1:ajans1,group1:group1},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }

        function editGfunc(id,ajans_id,daftar_id,fname,lname,user,pass,ty,num_card,vorood,khorooj,vorood1,khorooj1,zaman_hozur){
            StartLoading();
            $("input[name='id2']").val(id);
            $("#daftar2 option[value="+daftar_id+"]").attr('selected','selected');
            $("#ajans2 option[value="+ajans_id+"]").attr('selected','selected');
            $("input[name='fname2']").val(fname);
            $("input[name='lname2']").val(lname);
            $("input[name='user2']").val(user);
            $("input[name='pass2']").val(pass);
            $("#group2 option[value="+ty+"]").attr('selected','selected');
            $("input[name='ccart2']").val(num_card);
            $("input[name='vh2']").val(vorood);
            $("input[name='kh2']").val(khorooj);
            $("input[name='vh22']").val(vorood1);
            $("input[name='kh22']").val(khorooj1);
            $("input[name='zh2']").val(zaman_hozur);
            $('#editG').modal('show');
            StopLoading();
        }
        function editGfunc1(id,pass){
            StartLoading();
            
            $("input[name='id22']").val(id);
            $("input[name='pass22']").val(pass);
            
            $('#editG2').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var daftar2 = $("#daftar2 option:selected" ).val();
            var ajans2 = $("#ajans2 option:selected" ).val();
            var fname2 = $("input[name='fname2']").val();
            var lname2 = $("input[name='lname2']").val();
            var user2 = $("input[name='user2']").val();
            var pass2 = $("input[name='pass2']").val();
            var group2 = $("#group2 option:selected" ).val();
            var ccart2 = $("input[name='ccart2']").val();
            var vh2 = $("input[name='vh2']").val();
            var kh2 = $("input[name='kh2']").val();
            var vh22 = $("input[name='vh22']").val();
            var kh22 = $("input[name='kh22']").val();
            var zh2 = $("input[name='zh2']").val();

           $.post("userEditAjax.php",{id2:id2,daftar2:daftar2,ajans2:ajans2,fname2:fname2,lname2:lname2,user2:user2,pass2:pass2,group2:group2,ccart2:ccart2,vh2:vh2,kh2:kh2,vh22:vh22,kh22:kh22,zh2:zh2},function(data){
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
            var id2 = $("input[name='id22']").val();
            var pass2 = $("input[name='pass22']").val();

           $.post("userEditAjax2.php",{id2:id2,pass2:pass2},function(data){
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
            $("input[name='id3']").val(gid);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var id3 = $("input[name='id3']").val();
           $.post("userDeleteAjax.php",{id3:id3},function(data){
               StopLoading();
// 						 console.log(data);
						 
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