<?php
session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$msg = '';
	function loadKol()
        {
                $out="";
                mysql_class::ex_sql("select `name`,`id` from `kol` order by `id`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                    $out.="<option value='".$r["id"]."'>".$r["name"]."</option>";
                       // $out[$r["name"]]=(int)$r["id"];
                return $out;
        }
	function loadRoomCss($css_n='')
	{
		$out = null;
		$class_found = FALSE;
		$css_class = '';
		$css_array = array();
		$lines = file('css/fa-style.css');
		foreach($lines as $line)
		{
			if(strpos($line,'.room_closed_')!==FALSE)
			{
				$class_found = TRUE;
				$css_class .= $line."\n";
			}
			else if($class_found)
			{
				$css_class .= $line."\n";
				if(strpos($line,'}')!==FALSE)
				{
					$class_found = FALSE;
					$css_array[] = $css_class;
					$css_class = '';
				}
			}
		}
		for($i = 0;$i < count($css_array);$i++)
		{
			$tmp = explode('{',$css_array[$i]);
			$css_name = explode('.',$tmp[0]);
			$css_name = trim($css_name[1]);
			$css_color = '#100000';
			$tmp = explode(';',$tmp[1]);
			for($j=0;$j<count($tmp);$j++)
			{
				$tmp1 = explode(':',$tmp[$j]);
				if(trim($tmp1[0])=='background-color')
					$css_color = trim($tmp1[1]);
			}
			if($css_n == '')
                $out.="<option value=".$css_name.">".$css_color."</option>";
				//$out[$css_color] = $css_name;
			else if($css_n == $css_name)
				$out.="<option value=".$css_color.">".$css_color."</option>";
                //$out = $css_color;
		}
		return($out);
	}
function loadRoomCss1($css_n='')
	{
		$out = null;
		$class_found = FALSE;
		$css_class = '';
		$css_array = array();
		$lines = file('css/fa-style.css');
		foreach($lines as $line)
		{
			if(strpos($line,'.room_closed_')!==FALSE)
			{
				$class_found = TRUE;
				$css_class .= $line."\n";
			}
			else if($class_found)
			{
				$css_class .= $line."\n";
				if(strpos($line,'}')!==FALSE)
				{
					$class_found = FALSE;
					$css_array[] = $css_class;
					$css_class = '';
				}
			}
		}
		for($i = 0;$i < count($css_array);$i++)
		{
			$tmp = explode('{',$css_array[$i]);
			$css_name = explode('.',$tmp[0]);
			$css_name = trim($css_name[1]);
			$css_color = '#100000';
			$tmp = explode(';',$tmp[1]);
			for($j=0;$j<count($tmp);$j++)
			{
				$tmp1 = explode(':',$tmp[$j]);
				if(trim($tmp1[0])=='background-color')
					$css_color = trim($tmp1[1]);
			}
			if($css_n == '')
                //$out.="<option value=".$css_name.">".$css_color."</option>";
				$out[$css_color] = $css_name;
			else if($css_n == $css_name)
				//$out.="<option value=".$css_color.">".$css_color."</option>";
                $out = $css_color;
		}
		return($out);
	}
	function loadColor($inp)
	{
		$out ='&nbsp;';
		mysql_class::ex_sql("select `css_class` from `daftar` where `id` = $inp",$q);
		if($r = mysql_fetch_array($q))
			$out = "<span style=\"background-color:".loadRoomCss1($r['css_class']).";\" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
		return($out);
	}
	function loadMoeen($inp)
	{
		$inp = (int)$inp;
		$daftar = new daftar_class($inp);
		if($daftar->sandogh_moeen_id>0)
		{
			$moeen = new moeen_class($daftar->sandogh_moeen_id);
			$nama = $moeen->name.'('.$moeen->code.')';
		}
		else
		{
			$nama = 'انتخاب';
		}
		
		$out = "<u><span onclick=\"window.location =('select_hesab.php?refPage=daftar.php&sel_id=$inp');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
		return $out;
	}
        function add_item()
        {
                $fields = null;
                foreach($_REQUEST as $key => $value)
                        if(substr($key,0,4)=="new_")
                                if($key != "new_id" )
                                        $fields[substr($key,4)] =perToEnNums($value);
		$kol_id = kol_class::addById($fields['name']);
		$sandogh_moeen_id = moeen_class::addById($kol_id,'صندوق '.$fields['name']);
		$fields['kol_id'] = $kol_id;
		$fields['sandogh_moeen_id'] = $sandogh_moeen_id ;
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
                $query="insert into `daftar` $fi values $valu";
                mysql_class::ex_sqlx($query);
        }
	function delete_item($id)
	{
		$aj = new daftar_class($id);
		if($aj->protected == 1)
			$GLOBALS['msg'] = "<h2 style=\"color:red;\">امکان حذف این دفتر نمی باشد.</h2>";
		else
		{
			mysql_class::ex_sqlx("update `kol` set `name` = CONCAT(`name`,'_پاک‌شده_$id')  where `id` = ".$aj->kol_id."");
			mysql_class::ex_sqlx("update `moeen` set `name` = CONCAT(`name`,'_پاک‌شده_$id')  where `id` = ".$aj->sandogh_moeen_id);
			mysql_class::ex_sqlx("delete from `daftar` where `id` = $id");
		}
	}
	$GLOBALS['msg'] = '';
	$user = new user_class((int)$_SESSION['user_id']);
	if(isset($_REQUEST['sel_id']))
	{
		$moeen_id = (int)$_REQUEST['moeen_id'];
		$sel_id = $_REQUEST['sel_id'];
		$daf = new daftar_class((int)$_REQUEST['sel_id']);
		$q = null;
		$arr = array();
		mysql_class::ex_sql("select `id` from `moeen` where `kol_id`=".$daf->kol_id,$q);
		while($r = mysql_fetch_array($q))
		{
			$arr[] =(int)$r['id'];
		}
		//var_dump($arr);
		if(in_array($moeen_id,$arr))
			mysql_class::ex_sqlx("update `daftar` set `sandogh_moeen_id`=$moeen_id where `id`=$sel_id");
		else
			$msg = '<span style="color:red;">حساب معین انتخاب شده زیر مجموعه کل نیست</span>';
	}
	$grid = new jshowGrid_new("daftar","grid1");
	$grid->index_width = '20px';
	$grid->width = '95%';
	$grid->showAddDefault = FALSE;
	$grid->whereClause="1=1 order by `name`";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="نام دفتر";
	$grid->columnHeaders[2]="توضیحات";
	$grid->columnHeaders[3] = "حساب کل";
	$grid->columnLists[3]=loadKol();
	$grid->columnHeaders[4] = "نام کلاس گرافیکی";
	if($conf->is_hesabdari!=='')
		$grid->columnHeaders[4] =null;
	$grid->columnLists[4]=loadRoomCss();
	$grid->columnHeaders[5] =null;
	$grid->columnHeaders[6] = 'تخفیف(درصد)';
	if($conf->is_hesabdari!=='')
		$grid->columnHeaders[6] =null;
	$grid->columnHeaders[7] = (($user->user=='mehrdad')?'PROTECTED':null);
	$grid->addFeild('id');
	$grid->columnHeaders[8] = "حساب معین<br/>صندوق";
	$grid->columnFunctions[8] = 'loadMoeen';
	$grid->columnAccesses[8] = 0;
	$grid->addFeild('id');
	$grid->columnHeaders[9] = "نمونه رنگ";
	if($conf->is_hesabdari!=='')
		$grid->columnHeaders[9] =null;
	$grdi->columnAccesses[9] = 0;
	$grid->columnFunctions[9] = 'loadColor';
	$grid->addFunction = 'add_item';
	$grid->deleteFunction = 'delete_item';
	$grid->intial();
	$grid->executeQuery();
	$grid->canAdd = FALSE;
	if($grid->getRowCount()<$conf->limit_daftar)
		$grid->canAdd = TRUE;
		
	//$out = $grid->getGrid();
$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">نام دفتر</th>
                                        <th style=\"text-align:right;\">توضیحات</th>
                                        <th style=\"text-align:right;\">حساب کل</th>
                                        <th style=\"text-align:right;\">نام کلاس گرافیکی</th>
                                        <th style=\"text-align:right;\">تخفیف (درصد)</th>
                                        <th style=\"text-align:right;\">حساب معین صندوق</th>
                                        <th style=\"text-align:right;\">نمونه رنگ</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";

$www = '';
if($_SESSION['daftar_id']!=49){
// 	$www = ' and `id` = '.$_SESSION['daftar_id'];
    $www = ' and id not in (48,49)';
}
 mysql_class::ex_sql("select * from `daftar` where 1=1 $www order by `name` ",$ss);
$i=1;
while($r=mysql_fetch_array($ss)){
    $id = $r['id'];
   
    $name = $r['name'];
    
    $toz = $r['toz'];
    
    $kol_id = $r['kol_id'];
    mysql_class::ex_sql("select `name` from `kol` where `id` = '$kol_id' ",$k_id);
    $k_id1 = mysql_fetch_array($k_id);
    $kname = $k_id1['name'];
    
    $css_class = $r['css_class'];
    $ccc = explode("_",$css_class);
    $cc = $ccc[2];
    $takhfif = $r['takhfif'];
    
    $sandogh_moeen_id = $r['sandogh_moeen_id'];
    mysql_class::ex_sql("select `name` from `moeen` where `id` = '$sandogh_moeen_id' ",$s_id);
    $s_id1 = mysql_fetch_array($s_id);
    $sname = $s_id1['name'];
    
    if(fmod($i,2)!=0){
        $out.="
       <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$name</td>
                                        <td>$toz</td>
                                        <td>$kname</td>
                                        <td>$cc #</td>
                                        <td>$takhfif</td>
                                        <td>".loadMoeen($id)."</td>
                                        <td>".loadColor($id)."</td>
                                        <td>
                                
            <a onclick=\"editGfunc('".$id."','".$name."','".$toz."','".$kol_id."','".$cc."','".$takhfif."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
        
      
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
                                        <td>$name</td>
                                        <td>$toz</td>
                                        <td>$kname</td>
                                        <td>$cc #</td>
                                        <td>$takhfif</td>
                                        <td>".loadMoeen($id)."</td>
                                        <td>".loadColor($id)."</td>
                                        <td>
                                
            <a onclick=\"editGfunc('".$id."','".$name."','".$toz."','".$kol_id."','".$cc."','".$takhfif."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
        
      
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
	<title>مدیریت دفاتر</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-building"></i>مدیریت دفاتر</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                          <?php
				echo '<h2 style="color:red">'.$GLOBALS['msg'].'</h2>';
			?>
                               
                            <a href="#newG"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن دفتر جدید</button></a>
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
                    <h4 class="modal-title">افزودن دفتر</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                            <div class="col-md-4">
                                <label>نام دفتر: </label>
                                <input type="text" name="daftar1" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label>حساب کل: </label>
                                <select class="form-control" name="kol1" id="kol1">
                               <?php echo loadKol() ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>توضیحات: </label>
                                <input type="text" name="toz1" class="form-control" />
                            </div>
                            
                            <div class="col-md-4">
                                <label>نام کلاس گرافیکی: </label>
                                <select class="form-control" name="cg1" id="cg1">
                               <?php echo loadRoomCss() ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>تخفیف (درصد): </label>
                                <input type="text" name="takhf1" class="form-control" />
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
                    <h4 class="modal-title">ویرایش دفتر</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                        <input type="hidden" value="" name="id2" />
                        <div class="col-md-4">
                                <label>نام دفتر: </label>
                                <input type="text" name="daftar2" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label>حساب کل: </label>
                                <select class="form-control" name="kol2" id="kol2">
                               <?php echo loadKol() ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>توضیحات: </label>
                                <input type="text" name="toz2" class="form-control" />
                            </div>
                            
                            <div class="col-md-4">
                                <label>نام کلاس گرافیکی: </label>
                                <select class="form-control" name="cg2" id="cg2">
                                    <?php echo loadRoomCss() ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>تخفیف (درصد): </label>
                                <input type="text" name="takhf2" class="form-control" />
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
                    <h4 class="modal-title">حذف دفتر</h4>
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
    <script language="javascript" >
			<?php if($conf->hesab_auto){ ?>
			if(document.getElementById('new_kol_id'))
				document.getElementById('new_kol_id').style.display = 'none';
			<?php } ?>
			if(document.getElementById('new_css_class'))
				document.getElementById('new_css_class').style.fontFamily = 'tahoma';
			var inp = document.getElementsByName('new_id');
			for(var i=0;i<inp.length;i++)
				inp[i].style.display = 'none';

			
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

            var daftar1 = $("input[name='daftar1']").val();
            var toz1 = $("input[name='toz1']").val();
            var takhf1 = $("input[name='takhf1']").val();
            var cg1 = $("#cg1 option:selected" ).val();
            var kol1 = $("#kol1 option:selected" ).val();
           $.post("daftarAjax.php",{daftar1:daftar1,toz1:toz1,takhf1:takhf1,cg1:cg1,kol1:kol1},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در افزودن");
                                    if(data=="1"){
                                        alert("افزودن با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    
                                });
        }
        function editGfunc(id,name,toz,kol_id,cc,takhfif){
            StartLoading();
            $("input[name='id2']").val(id);
            $("input[name='daftar2']").val(name);
            $("input[name='toz2']").val(toz);
            $("input[name='takhf2']").val(takhfif);
            $("#kol2 option[value="+kol_id+"]").attr('selected','selected');
            $("#cg2 option[value='room_closed_"+cc+"']").attr('selected','selected');
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var daftar2 = $("input[name='daftar2']").val();
            var toz2 = $("input[name='toz2']").val();
            var takhf2 = $("input[name='takhf2']").val();
            var kol2 = $("#kol2 option:selected" ).val();
            var cg2 = $("#cg2 option:selected" ).val();
           $.post("daftarEditAjax.php",{id2:id2,daftar2:daftar2,toz2:toz2,takhf2:takhf2,kol2:kol2,cg2:cg2},function(data){
                                    StopLoading();
                                    if(data=="0")
                                        alert("خطا در ویرایش");
                                    else if(data=="1"){
                                        alert("ویرایش با موفقیت انجام شد");
                                        location.reload();
                                    }
                                        
                                    location.reload();
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
           $.post("daftarDeleteAjax.php",{id3:id3},function(data){
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