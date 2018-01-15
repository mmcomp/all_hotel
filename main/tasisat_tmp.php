<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if (isset($_SESSION['user_id']))
		$user_id = $_SESSION['user_id'];
	else
		$user_id = -1;
	$msg = '';
	$isAdmin = $se->detailAuth("modir");
	function ppdate($inp)
	{
		return($inp!='0000-00-00 00:00:00'?audit_class::hamed_pdate($inp):'----');
	}
	function loadRoom()
	{
		if (isset($_REQUEST["hotel_id_new"]))
			$hotel_id_new = $_REQUEST["hotel_id_new"];
		else
			$hotel_id_new = -1;
		$out = array();
		mysql_class::ex_sql("select `id` , `name` from `room` where `hotel_id`='$hotel_id_new'",$q);
		while($r = mysql_fetch_array($q))
			$out[$r['name']]=$r['id'];
		$motefareghe = array(
			-1 => 'لابی',
			-2 => 'رستوران',
			-3 => 'کافی شاپ',
			-4 => 'آمفی تئاتر',
			-5 => 'آسانسور',
			-6 => 'زیرزمین',
			-7 => 'اتاق پرسنل'
		);
		foreach($motefareghe as $key=>$value)
			$out[$value] = $key;
		return($out);
	}
function loadRoom1()
	{
		if (isset($_REQUEST["hotel_id_new"]))
			$hotel_id_new = $_REQUEST["hotel_id_new"];
		else
			$hotel_id_new = -1;
		$out = "";
		mysql_class::ex_sql("select `id` , `name` from `room` where `hotel_id`='$hotel_id_new'",$q);
		while($r = mysql_fetch_array($q))
            $out.="<option value='".$r['id']."'>".$r['name']."</option>";
            $out.="<option value='-1'>لابی</option>
            <option value='-2'>رستوران</option>
            <option value='-3'>کافی شاپ</option>
            <option value='-4'>آمفی تئاتر</option>
            <option value='-5'>آسانسور</option>
            <option value='-6'>زیرزمین</option>
            <option value='-7'>اتاق پرسنل</option>";
			//$out[$r['name']]=$r['id'];
		/*$motefareghe = array(
			-1 => 'لابی',
			-2 => 'رستوران',
			-3 => 'کافی شاپ',
			-4 => 'آمفی تئاتر',
			-5 => 'آسانسور',
			-6 => 'زیرزمین',
			-7 => 'اتاق پرسنل'
		);*/
		//foreach($motefareghe as $key=>$value)
			//$out[$value] = $key;
		return($out);
	}
	function loadHotel()
        {
        $out = array();
		mysql_class::ex_sql("select `id` , `name` from `hotel`",$q);
		while($r = mysql_fetch_array($q))
			$out[$r['name']]=$r['id'];
		return($out);
        }
function loadHotel1()
        {
        $out = "";
		mysql_class::ex_sql("select `id` , `name` from `hotel`",$q);
		while($r = mysql_fetch_array($q))
            $out.="<option value='".$r['id']."'>".$r['name']."</option>";
			//$out[$r['name']]=$r['id'];
		return($out);
        }
	function loadUser($user_id)
	{
		$out = "";
		mysql_class::ex_sql("select `fname` , `lname` from `user` where `id`='$user_id'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['fname'].' '.$r['lname'];
		return $out;
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
		$fields['room_id'] = (int)$fields['room_id'];
		$fields['user_reg' ] =$_SESSION['user_id'];
		$fields['toz' ] = $fields['toz'];
		$fields['regdate'] = date("Y-m-d H:i:s");
		$fields['en'] = '-1';
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
		if (($fields['room_id']!='')&&($fields['toz' ]!='توضیحات'))
		{
			$query="insert into `tasisat_tmp` $fi values $valu";
			mysql_class::ex_sqlx($query);
		}
		else
			echo '<br/><center><h3>'."لطفا تمامی اطلاعات درخواستی را با دقت و کامل وارد نمایید".'</h3></center>';
        }
	function loadRoomsCombo($room_s_id)
	{
		$motefareghe = array(
			-1 => 'لابی',
			-2 => 'رستوران',
			-3 => 'کافی شاپ',
			-4 => 'آمفی تئاتر',
			-5 => 'آسانسور',
			-6 => 'زیرزمین',
			-7 => 'اتاق پرسنل'
		);		
		$out ='<option value="0"></option>';
		$wer = isset($_REQUEST["hotel_id_new"])?'and hotel_id = '.$_REQUEST["hotel_id_new"]:'';
		mysql_class::ex_sql("select id,name from room where en = 1 $wer order by name",$q);
		while($r = mysql_fetch_array($q))
			$out .= '<option value = "'.$r['id'].'" '.(((int)$r['id']==(int)$room_s_id)?'selected':'').'>'.$r['name'].'</option>';
		foreach($motefareghe as $key=>$value)
			$out .= '<option value = "'.$key.'" '.(($key==(int)$room_s_id)?'selected':'').'>'.$value.'</option>';
		return($out);
	}
	function loadUsersCombo($user_s_id)
        {
		$out ='<option value="-1"></option>';
		mysql_class::ex_sql("select id,fname,lname from user where user<>'mehrdad' order by lname,fname",$q);
		while($r = mysql_fetch_array($q))
			$out .= '<option value = "'.$r['id'].'" '.(((int)$r['id']==(int)$user_s_id)?'selected':'').'>'.$r['fname'].' '.$r['lname'].'</option>';
                return($out);
        }
	$shart_1 = '';
	$hotel_acc = daftar_class::HotelList((int)$_SESSION['daftar_id']);
	$shart = '';
	if($hotel_acc!=null)
	{
		if (count($hotel_acc)==1)
			$_REQUEST["hotel_id_new"] = $hotel_acc[0];
		for($l=0;$l<count($hotel_acc);$l++)
			$shart.=(($l == 0) ? '  (' : ',').$hotel_acc[$l].(($l==count($hotel_acc)-1)?')':'');
		$shart_1 = "where `id` in ".$shart;
	}
////////////////////
	if (isset($_REQUEST["hotel_id_new"]))
                $hotel_id_new = $_REQUEST["hotel_id_new"];
	else
		$hotel_id_new = -1;
	$aztarikh = (isset($_REQUEST['aztarikh']) && trim($_REQUEST['aztarikh'])!='')?$_REQUEST['aztarikh']:'';
	$tatarikh = (isset($_REQUEST['tatarikh']) && trim($_REQUEST['tatarikh'])!='')?$_REQUEST['tatarikh']:'';
	$room_s_id = (isset($_REQUEST['room_s_id']) && trim($_REQUEST['room_s_id'])!='')?(int)$_REQUEST['room_s_id']:0;
	$user_s_id = (isset($_REQUEST['user_s_id']) && trim($_REQUEST['user_s_id'])!='')?(int)$_REQUEST['user_s_id']:-1;
	$wer = ' ';
	if($aztarikh != '')
		$wer .= ' and regdate >= \''.audit_class::hamed_pdateBack($aztarikh).'\'';
	if($tatarikh != '')
                $wer .= ' and regdate <= \''.audit_class::hamed_pdateBack($tatarikh).'\'';
	if($room_s_id != 0)
                $wer .= ' and room_id = \''.$room_s_id.'\'';
	if($user_s_id > 0)
                $wer .= ' and user_reg = \''.$user_s_id.'\'';
	$combo_hotel = "";
	
		$combo_hotel .= "<select class='form-control inp' id=\"hotel_id\" name=\"hotel_id_new\"><option value=\"-1\"></option>";
		mysql_class::ex_sql("select * from `hotel`$shart_1 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$hotel_id_new)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_hotel .= "<option value=\"".(int)$r["id"]."\" $select   >";
		        $combo_hotel .= $r["name"]."";
		        $combo_hotel .= "</option>";
		}
		$combo_hotel .= "</select>";



$combo_hotel1 = "";
	
		$combo_hotel1 .= "<select class='form-control inp' id=\"hotel_id1\" name=\"hotel_id_new\"><option value=\"-1\"></option>";
		mysql_class::ex_sql("select * from `hotel` order by `name`",$ss);
		while($r = mysql_fetch_array($ss))
		{
			if((int)$r["id"]== (int)$hotel_id_new)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_hotel1 .= "<option value=\"".(int)$r["id"]."\" $select   >";
		        $combo_hotel1 .= $r["name"]."";
		        $combo_hotel1 .= "</option>";
		}
		$combo_hotel1 .= "</select>";



        $combo_room = "";
                $combo_room .= "<select class=\"form-control\" id=\"room_s_id\" name=\"room_s_id\" >";
		$combo_room .= loadRoomsCombo($room_s_id);
		$combo_room .= "</select>";
		$combo_user = "";
		$combo_user .= "<select class=\"form-control\" id=\"user_s_id\" name=\"user_s_id\" >";
                $combo_user .= loadUsersCombo($user_s_id);
                $combo_user .= "</select>";
/*
		$combo_hotel .= "کاربر اصلاح کننده: ";
		$combo_hotel .= "<select id=\"user_s_id\" name=\"user_s_id\" >";
                $combo_hotel .= loadUsersCombo($user_t_id);
                $combo_hotel .= "</select>";
		$combo_hotel .= "وضعیت: ";
		$combo_hotel .= "<select id=\"user_s_id\" name=\"user_s_id\" >";
                $combo_hotel .= loadEn($en_s);
                $combo_hotel .= "</select>";
*/
		//$combo_hotel .= "<button>جستجو</button>";
	//$combo_hotel .= "</form>";
	$grid = new jshowGrid_new("tasisat_tmp","grid1");
	$grid->width = '99%';
	$grid->index_width = '20px';
	if(isset($_REQUEST['omoomi']))
		$wer .= ' and room_id < 0 ';
	$grid->whereClause= ((!$isAdmin)?' `en` = -1 and ':'')." `hotel_id`=".$hotel_id_new.$wer;
	$grid->setERequest(array('hotel_id_new'=>$hotel_id_new));
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = 'هتل';
	$grid->columnLists[1] = loadHotel();
	$grid->columnHeaders[2] = 'شماره اتاق';
	$grid->columnLists[2] = loadRoom();
	//$grid->columnFunctions[4] = 'ppdate';
	$grid->columnHeaders[3] = 'ثبت کننده ';
	$grid->columnFunctions[3] = 'loadUser';
	$grid->columnAccesses[3] = 0;
	$grid->columnHeaders[4] = ($isAdmin)?'کاربر تعمیر کننده':null;
	if($isAdmin)
		$grid->columnFunctions[4] = 'loadUser';
	$grid->columnHeaders[5] = 'توضیحات';
	$grid->columnHeaders[6] = null;
	$grid->columnHeaders[7] = 'تاریخ ثبت';
	$grid->columnFunctions[7] = 'ppdate';
	$grid->columnAccesses[7] = 0;
	$grid->columnHeaders[8] = ($isAdmin)?'تاریخ اصلاح':null;
	if($isAdmin)
		$grid->columnFunctions[8] = 'ppdate';
	$grid->columnHeaders[9] = ($isAdmin)?'وضعیت':null;
	if($isAdmin)
		$grid->columnLists[9] = array('خراب'=>-1,'اصلاح شده'=>1);
	$grid->addFunction = 'add_item';
	$grid->canEdit = FALSE;
	$grid->canAdd = $se->detailAuth('tasisat') || $se->detailAuth('modir');
	$grid->canDelete = $se->detailAuth('modir');
	$grid->pageCount = 0;
	$grid->intial();
	$grid->executeQuery();
	//$out = $grid->getGrid();

$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">هتل</th>
                                        <th style=\"text-align:right;\">شماره اتاق</th>
                                        <th style=\"text-align:right;\">ثبت کننده</th>
                                        <th style=\"text-align:right;\">کاربر تعمیر کننده</th>
                                        <th style=\"text-align:right;\">توضیحات</th>
                                        <th style=\"text-align:right;\">تاریخ ثبت</th>
                                        <th style=\"text-align:right;\">تاریخ اصلاح</th>
                                        <th style=\"text-align:right;\">وضعیت</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";

 mysql_class::ex_sql("select * from `tasisat_tmp` where ".$grid->whereClause." ",$ss);
$i=1;
while($r=mysql_fetch_array($ss)){
    $id = $r['id'];
    
    $hotel_id = $r['hotel_id'];
    mysql_class::ex_sql("select `name` from `hotel` where `id` = '$hotel_id' ",$h_id);
    $h_id1 = mysql_fetch_array($h_id);
    $hname = $h_id1['name'];
    
    $room_id = $r['room_id'];
    if($room_id<0){
        if($room_id==-1)
            $rname="لابی";
        if($room_id==-2)
            $rname="رستوران";
        if($room_id==-3)
            $rname="کافی شاپ";
        if($room_id==-4)
            $rname="آمفی تئاتر";
        if($room_id==-5)
            $rname="آسانسور";
        if($room_id==-6)
            $rname="زیرزمین";
        if($room_id==-7)
            $rname="اتاق پرسنل";
    }
    else{
    mysql_class::ex_sql("select `name` from `room` where `id` = '$room_id' ",$r_id);
    $r_id1 = mysql_fetch_array($r_id);
    $rname = $r_id1['name'];
    }
    $user_reg = $r['user_reg'];
    mysql_class::ex_sql("select * from `user` where `id` = '$user_reg' ",$u_id);
    $u_id1 = mysql_fetch_array($u_id);
    $fname = $u_id1['fname'];
    $lname = $u_id1['lname'];
    $uname = $fname." ".$lname;
    
    $user_fixed = $r['user_fixed'];
    mysql_class::ex_sql("select * from `user` where `id` = '$user_fixed' ",$f_id);
    $f_id1 = mysql_fetch_array($f_id);
    $fname1 = $f_id1['fname'];
    $lname1 = $f_id1['lname'];
    $uname1 = $fname1." ".$lname1;
    
    $toz = $r['toz'];
    
    $toz_fix = $r['toz_fix'];
    
    $regdate = $r['regdate'];
    $regd=jdate('Y/n/j',strtotime($regdate));
    $date_fix = $r['date_fix'];
    $daf=jdate('Y/n/j',strtotime($date_fix));
    $en = $r['en'];
    $state = "";
    if($en==1)
        $state="حل شده";
    if($en==-1)
        $state="خراب";
    
    if(fmod($i,2)!=0){
        $out.="
       <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$hname</td>
                                        <td>$rname</td>
                                        <td>$uname</td>
                                        <td>$uname1</td>
                                        <td>$toz</td>
                                        <td>$regd</td>
                                        <td>$daf</td>
                                        <td>$state</td>
                                        <td>
                                            <a onclick=\"editGfunc('".$id."','".$hotel_id."','".$room_id."','".$toz."','".$toz_fix."','".$en."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
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
                                        <td>$hname</td>
                                        <td>$rname</td>
                                        <td>$uname</td>
                                        <td>$uname1</td>
                                        <td>$toz</td>
                                        <td>$regd</td>
                                        <td>$daf</td>
                                        <td>$state</td>
                                        <td>
                                            <a onclick=\"editGfunc('".$id."','".$hotel_id."','".$room_id."','".$toz."','".$toz_fix."','".$en."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
                                            <a onclick=\"deleteGfunc(".$id.")\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-danger\"><i class=\"fa fa-times\"></i> حذف</button></a>
                                        </td>
                                    </tr>
        ";
        $i++;
    }
    
}
	/*$grid = new jshowGrid_new("hotel_garanti","grid1");
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= 'هتل';
	$grid->columnLists[1]= loadHotel();
	$grid->columnHeaders[2]= 'دفتر';
	$grid->columnLists[2]= loadDaftar();
	$grid->columnHeaders[3]= 'طبقه';
	$grid->columnLists[3]= loadTabaghe();
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();*/
$out.="</tbody></table></div></div>";
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>مشکلات هتل</title>
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
                            <h4 style="margin-right:20px;"><?php if($_GET['omoomi']==1) echo"<i style=\"margin-left:10px;\" class=\"fa fa-gears\"></i>مشکل در فضای عمومی"; else echo"<i style=\"margin-left:10px;\" class=\"fa fa-exclamation-triangle\"></i>مشکل در اتاق"; ?></h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                          
                               
                            <a href="#newG"  data-toggle="modal"><button style="margin:5px;" class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن مشکل جدید</button></a>
                            <br/>
                            <form name="selHotel" id="selHotel" method="POST">
                            <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">هتل:</label> 
                                    <div class="col-md-9"><?php echo $combo_hotel; ?></div>
                                </div>
                                 <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">از تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control" id="datepicker1" name="aztarikh" value="" />
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">تا تاریخ:</label> 
                                    <div class="col-md-8"><input class="form-control"  id="datepicker2" name="tatarikh" value="" />
                                    
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">اتاق:</label> 
                                    <div class="col-md-8">
                                    <?php echo $combo_room; ?>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">کاربر ثبت کننده:</label> 
                                    <div class="col-md-8">
                                        <?php echo $combo_user; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-2" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left">جستجو</button></div>
                                </div>
                                </form>
                          <?php 
				//echo $grid->whereClause;
				echo $msg.'<br/>';
				echo $out; ?>
                           
                            
                            
                            
                                    
                              
                               
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
                    <h4 class="modal-title">افزودن مشکل</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <div class="col-md-4">
                            <label>هتل: </label>
                            <select id="hotel1" class="form-control">
                                <?php echo loadHotel1() ?>
                            </select>
                            
                        </div>
                        <div class="col-md-4">
                            <label>شماره اتاق: </label>
                            <select id="room1" class="form-control">
                            <?php echo loadRoom1(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>توضیحات: </label>
                            <input type="text" class="form-control" name="toz1" />
                        </div>
                        <div class="col-md-4">
                            <label>وضعیت: </label>
                            <select id="state1" class="form-control">
                                <option value="1">حل شده</option>
                                <option value="-1">خراب</option>
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
                    <h4 class="modal-title">ویرایش مشکل</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id2" />
                         <div class="col-md-4">
                            <label>هتل: </label>
                            <select id="hotel2" class="form-control">
                                <?php echo loadHotel1() ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>شماره اتاق: </label>
                            <select id="room2" class="form-control">
                            <?php echo loadRoom1(); ?>
                            </select>
                        </div>
                        <div id="toz2" class="col-md-4">
                            <label>توضیحات: </label>
                            <input type="text" class="form-control" name="toz2" />
                        </div>
                        
                        <div class="col-md-4">
                            <label>وضعیت: </label>
                            <select onchange="getval(this);" id="state2" class="form-control">
                                <option value="1">حل شده</option>
                                <option value="-1">خراب</option>
                            </select>
                        </div>
                        <div id="fixtoz2" class="col-md-4">
                            <label>توضیح رفع مشکل: </label>
                            <input type="text" class="form-control" name="fixtoz2" />
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
                    <h4 class="modal-title">حذف مشکل</h4>
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
                 $("#new_regdate").hide();
        $("#new_user_reg").hide();
    
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
        function getval(sel) {
            if(sel.value==1){
                $("#fixtoz2").slideDown();
            }
            if(sel.value==-1){
                $("#fixtoz2").slideUp();
            }
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
            var hotel1 = $("#hotel1 option:selected" ).val();
            var room1 = $("#room1 option:selected" ).val();
            var state1 = $("#state1 option:selected" ).val();
            var toz1 = $("input[name='toz1']").val();
           $.post("tasisat_tmpAjax.php",{hotel1:hotel1,room1:room1,state1:state1,toz1:toz1},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function editGfunc(id,hotel_id,room_id,toz,toz_fix,en){
            StartLoading();
            var state = en;
            if(state==-1)
                $("#fixtoz2").hide();
            
            $("input[name='id2']").val(id);
            $("input[name='toz2']").val(toz);
            $("input[name='fixtoz2']").val(toz_fix);
            $("#hotel2 option[value="+hotel_id+"]").attr('selected','selected');
            $("#room2 option[value="+room_id+"]").attr('selected','selected');
            $("#state2 option[value="+en+"]").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var toz2 = $("input[name='toz2']").val();
            var fixtoz2 = $("input[name='fixtoz2']").val();
            var hotel2 = $("#hotel2 option:selected" ).val();
            var room2 = $("#room2 option:selected" ).val();
            var state2 = $("#state2 option:selected" ).val();
           
           $.post("tasisat_tmpEditAjax.php",{id2:id2,toz2:toz2,fixtoz2:fixtoz2,hotel2:hotel2,room2:room2,state2:state2},function(data){
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
            $("input[name='id3']").val(id);
            $('#deleteG').modal('show');
            StopLoading();
            
        }
        function deleteFinalG(){
            StartLoading();
            var id3 = $("input[name='id3']").val();
            $.post("tasisat_tmpDeleteAjax.php",{id3:id3},function(data){
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