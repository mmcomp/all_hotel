<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        $GLOBALS['msg'] = '';
	$user = new user_class((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);

        function add_item()
        {
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
		$daftar = new daftar_class($fields['daftar_id']);
		$kol_tmp = new kol_class($daftar->kol_id);
		if($kol_tmp->id>0)
		{
			$moeen_id = moeen_class::addById($daftar->kol_id,$fields['name']);
			$fields['moeen_id'] = $moeen_id;
		}
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
                $query="insert into `ajans` $fi values $valu";
echo $query;
                mysql_class::ex_sqlx($query);
        }


	function loadDaftar()
	{
		$out=null;
		 $se = security_class::auth((int)$_SESSION['user_id']);
		if($se->detailAuth('dafater'))
		{
			$daftar_id = $_SESSION['daftar_id'];
			mysql_class::ex_sql("select `name`,`id` from `daftar` where `id`='$daftar_id' order by `id`",$q);
		}
		else
			mysql_class::ex_sql("select `name`,`id` from `daftar` order by `id`",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
            $out.="<option value='".$r["id"]."'>".$r["name"]."</option>";
			//$out[$r["name"]]=(int)$r["id"];
		}
		return $out;
	}
	function loadMoeen($inp)
	{
		$inp = (int)$inp;
		$aj = new ajans_class($inp);
		if($aj->moeen_id>0)
		{
			$moeen = new moeen_class($aj->moeen_id);
			$nama = $moeen->name.'('.$moeen->code.')';
		}
		else
		{
			$nama = 'انتخاب';
		}
		
		$out = "<u><span onclick=\"window.location =('select_hesab.php?refPage=ajans.php&sel_id=$inp');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
		return $out;
	}
	function del_item($id)
	{
		$ag = new ajans_class((int)$id);
		if($ag->protected == 1)
			$GLOBALS['msg'] = 'امکان حذف این آژانس نمی باشد';
		else
		{
			mysql_class::ex_sqlx("update `moeen` set `name` ='".$ag->name."_پاک‌شده_$id' where `id`=".$ag->moeen_id);
			mysql_class::ex_sqlx("delete from `ajans` where `id`=$id ");
		}
	}
	if(isset($_REQUEST['sel_id']))
	{
		$moeen_id = (int)$_REQUEST['moeen_id'];
		$sel_id = $_REQUEST['sel_id'];
		mysql_class::ex_sqlx("update `ajans` set `moeen_id`=$moeen_id where `id`=$sel_id");
	}
	$ersal ["شود"]= 0;
	$ersal ["نشود"]= 1;
	if($_SESSION['daftar_id']!=49)//$se->detailAuth('dafater'))
	{
		$ajans_ids = ajans_class::loadByDafterId($_SESSION['daftar_id']);
		$werc = ' and `id` in ('.implode(',',$ajans_ids).') ';
	}
	else
		$werc = '';
	$grid = new jshowGrid_new("ajans","grid1");
	$grid->whereClause="1=1 $werc order by `daftar_id`,`name`";
	$grid->showAddDefault = FALSE;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="نام دفتر";
	if ($se->detailAuth('reserve') || $se->detailAuth('dafater'))
		$grid->columnAccesses[1] = 0;
	else
		$grid->columnAccesses[1] = 1;
	$grid->columnFilters[1] = TRUE;
	if($conf->is_hesabdari !== '')
		$grid->columnHeaders[2]="نام مشتری";
	else
		$grid->columnHeaders[2]="نام آژانس";
	$grid->columnFilters[2] = TRUE;
	$grid->columnHeaders[3]="توضیحات";
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[5] = null;
	if($conf->sms)
        	$grid->columnHeaders[5] = "ارسال پیام کوتاه به مشتری";
	if($conf->is_hesabdari !== '')
		$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6]="شماره همراه";
	$grid->columnHeaders[7] = null;
	if($conf->ajans_saghf_mande)
		$grid->columnHeaders[7] = "سقف خرید";
	if($conf->is_hesabdari !== '')
		$grid->columnHeaders[7] = null;
	$grid->columnJavaScript[7] = 'onkeyup="monize(this);"';
	$grid->columnCallBackFunctions[7] = "umonize";
	$grid->columnHeaders[8] = null;
	if($conf->ajans_saghf_mande)
		$grid->columnHeaders[8] = 'کمیسیون (درصد)';
	if($conf->is_hesabdari !== '')
		$grid->columnHeaders[8] = null;
	$grid->columnLists[5]=$ersal;
	$grid->columnHeaders[9]=null;
	if($user->user='mehrdad')
		$grid->columnHeaders[9]='protected';
	$grid->addFeild('id');
	$grid->columnHeaders[10] = 'حساب معین';
	$grid->columnLists[1]=loadDaftar();
	$grid->columnFunctions[10]='loadMoeen';
	if ($se->detailAuth('dafater'))
		$grid->columnAccesses[10] = 0;
	else
		$grid->columnAccesses[10] = 1;
//	$grid->columnHeaders[1] = "نام مشتری";
//	$grid->deleteFunction = "delete_item";
//	$grid->pageCount = 500;
	$grid->addFunction = 'add_item';
	$grid->deleteFunction = 'del_item';
	$grid->canDelete = TRUE;
	$grid->intial();
	$grid->executeQuery();
	$grid->canAdd = FALSE;
	if($grid->getRowCount()<$conf->limit_ajans)
		$grid->canAdd = TRUE;
	//$out = $grid->getGrid();
$out ="
<table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">
                                <thead>
                                    <tr>
                                        <th style=\"text-align:right;width:1px;\">رديف</th>
                                        <th style=\"text-align:right;\">نام دفتر</th>
                                        <th style=\"text-align:right;\">نام آژانس</th>
                                        <th style=\"text-align:right;\">توضیحات</th>
                                        <th style=\"text-align:right;\">ارسال پیام کوتاه به مشتری</th>
                                        <th style=\"text-align:right;\">شماره همراه</th>
                                        <!--<th style=\"text-align:right;\">protected</th>-->
                                        <th style=\"text-align:right;\">حساب معین</th>
                                        <th style=\"text-align:right;\">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>";

 mysql_class::ex_sql("select * from `ajans` where 1=1 $werc order by `daftar_id`,`name` ",$ss);
$i=1;
while($r=mysql_fetch_array($ss)){
    $id = $r['id'];
    
    $daftar_id = $r['daftar_id'];
    mysql_class::ex_sql("select `name` from `daftar` where `id` = '$daftar_id' ",$d_id);
    $d_id1 = mysql_fetch_array($d_id);
    $dname = $d_id1['name'];
   
    $name = $r['name'];
    
    $tozihat = $r['tozihat'];
    
    $ersal_moshtari = $r['ersal_moshtari'];
    $ersmo="";
    if($ersal_moshtari==0)
        $ersmo="شود";
    else if($ersal_moshtari==1)
        $ersmo="نشود";
    else
        $ersmo="نامعلوم";
        
    $protected = $r['protected'];
    
    $tell = $r['tell'];
    
    
    if(fmod($i,2)!=0){
        $out.="
       <tr class=\"odd\">
                                        <td>$i</td>
                                        <td>$dname</td>
                                        <td>$name</td>
                                        <td>$tozihat</td>
                                        <td>$ersmo</td>
                                        <td>$tell</td>
                                        <!--<td>$protected</td>-->
                                        <td>".loadMoeen($id)."</td>
                                        <td>
                                
            <a onclick=\"editGfunc('".$id."','".$daftar_id."','".$name."','".$tozihat."','".$ersal_moshtari."','".$protected."','".$tell."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
        
      
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
                                        <td>$dname</td>
                                        <td>$name</td>
                                        <td>$tozihat</td>
                                        <td>$ersmo</td>
                                        <td>$tell</td>
                                        <!--<td>$protected</td>-->
                                        <td>".loadMoeen($id)."</td>
                                        <td>
                                
            <a onclick=\"editGfunc('".$id."','".$daftar_id."','".$name."','".$tozihat."','".$ersal_moshtari."','".$protected."','".$tell."')\" data-toggle=\"modal\"><button style=\"margin:5px;min-width:90px;\" class=\"btn btn-info\"><i class=\"fa fa-pencil-square-o\"></i> ویرایش</button></a>
        
      
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
	<title><?php
	if($conf->is_hesabdari !== '')
		echo "مشتری";
	else
		echo "آژانس";
?></title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-building"></i><?php
	if($conf->is_hesabdari !== '')
		echo "مشتری";
	else
		echo "آژانس";
?></h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                          <?php
				echo '<h2 style="color:red">'.$GLOBALS['msg'].'</h2>';
			?>
                               
                            <a href="#newG"  data-toggle="modal"><button class="btn btn-success btn-lg"><i class="fa fa-plus"></i>افزودن مورد جدید</button></a>
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
                    <h4 class="modal-title">افزودن مورد</h4>
                </div>
                <div class="modal-body" style="max-height:300px;overflow-y:scroll">
                    <form class="form-horizontal row-border" action="#">
                            <div class="col-md-4">
                                <label>نام دفتر: </label>
                                <select class="form-control" name="daftar1" id="daftar1">
                                    <?php echo loadDaftar() ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>نام آژانس: </label>
                                 <input type="text" name="ajans1" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label>توضیحات: </label>
                                <input type="text" name="toz1" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label>ارسال پیام کوتاه به مشتری: </label>
                                <select class="form-control" name="send1" id="send1">
                                    <option value="0">شود</option>
                                    <option value="1">نشود</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>شماره همراه: </label>
                                <input type="text" name="tell1" class="form-control" />
                            </div>
<!--                         <div class="col-md-4">
                                <label>protected: </label>
                                <input type="text" name="protected1" class="form-control" />
                            </div> -->
                       
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
                                <label>نام دفتر: </label>
                                <select class="form-control" name="daftar2" id="daftar2">
                                    <?php echo loadDaftar() ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>نام آژانس: </label>
                                 <input type="text" name="ajans2" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label>توضیحات: </label>
                                <input type="text" name="toz2" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label>ارسال پیام کوتاه به مشتری: </label>
                                <select class="form-control" name="send2" id="send2">
                                    <option value="0">شود</option>
                                    <option value="1">نشود</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>شماره همراه: </label>
                                <input type="text" name="tell2" class="form-control" />
                            </div>
<!--                         <div class="col-md-4">
                                <label>protected: </label>
                                <input type="text" name="protected2" class="form-control" />
                            </div> -->
                       
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
                    <h4 class="modal-title">حذف مورد</h4>
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
	<script language="javascript">
                        var ids = document.getElementsByName("new_id");
			for(var i=0;i<ids.length;i++)
				ids[i].style.display="none";
		</script>
<span id='tim' >test2
		</span>
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
        
       
        function insertFinalG(){
            StartLoading();
            var daftar1 = $("#daftar1 option:selected" ).val();
            var ajans1 = $("input[name='ajans1']").val();
            var toz1 = $("input[name='toz1']").val();
            var send1 = $("#send1 option:selected").val();
            var tell1 = $("input[name='tell1']").val();
            var protected1 = $("input[name='protected1']").val();
            
            $.post("ajansAjax.php",{daftar1:daftar1,ajans1:ajans1,toz1:toz1,send1:send1,tell1:tell1,protected1:protected1},function(data){
                StopLoading();
// 							console.log(data);
							
                if(data=="0")
                    alert("خطا در افزودن");
                if(data=="1"){
                    alert("افزودن با موفقیت انجام شد");
                    location.reload();
                } 
								
            });
        }
        function editGfunc(id,daftar_id,name,tozihat,ersal_moshtari,protected,tell){
            StartLoading();
            $("input[name='id2']").val(id);
            $("#daftar2 option[value="+daftar_id+"]").attr('selected','selected');
            $("input[name='ajans2']").val(name);
            $("input[name='toz2']").val(tozihat);
            $("#send2 option[value="+ersal_moshtari+"]").attr('selected','selected');
            $("input[name='protected2']").val(protected);
            $("input[name='tell2']").val(tell);
            $('#editG').modal('show');
            StopLoading();
        }
        function editFinalG(){
            StartLoading();
            var id2 = $("input[name='id2']").val();
            var daftar2 = $("#daftar2 option:selected" ).val();
            var ajans2 = $("input[name='ajans2']").val();
            var toz2 = $("input[name='toz2']").val();
            var send2 = $("#send2 option:selected").val();
            var tell2 = $("input[name='tell2']").val();
            var protected2 = $("input[name='protected2']").val();
            $.post("ajansEditAjax.php",{id2:id2,daftar2:daftar2,ajans2:ajans2,toz2:toz2,send2:send2,tell2:tell2,protected2:protected2},function(data){
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
           $.post("ajansDeleteAjax.php",{id3:id3},function(data){
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