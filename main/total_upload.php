<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = ($se->detailAuth('all') || $se->detailAuth('middle_manager') || $se->detailAuth('reserve'));
	function loadDaftar()
	{
		$out=null;
		mysql_class::ex_sql("SELECT `name` , `id` FROM `daftar` ORDER BY `id`",$q);
		$out['مرکزی']=-1;
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$out[$r["name"]]=(int)$r["id"];
		}
		return $out;
	}
	function loadDaftarCombo($inp)
	{
		$out='';
		$wer='';
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = ($se->detailAuth('all') || $se->detailAuth('middle_manager') || $se->detailAuth('reserve') || $se->detailAuth('hesabdar'));
		$out .= "<select class='form-control inp' name='daftar_id' id='daftar_id'>";
		if(/*$isAdmin*/$_SESSION['daftar_id']==49)
		{
			$wer.=' 1=1';
			$out .="<option value='-1'>همه</option>";
		}
		else
		{
			$user_id =(int)$_SESSION['user_id'];
			$user = new user_class($user_id);
			$wer.=" `id`=".$user->daftar_id;
		}
		mysql_class::ex_sql("select `name`,`id` from `daftar` where $wer order by `name`",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$sel = '';
			if($inp == (int)$r['id'])
				$sel = 'selected="selected"';
			$out.= "<option $sel value='".$r['id']."'>".$r['name']."</option>";
		}
		$out .= "</select>";
		return $out;
	}
	function loadKarbar()
	{
		$out=null;
		mysql_class::ex_sql("select `fname`,`lname`,`id` from `user` order by `id`",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$out[$r["fname"].' '.$r["lname"] ]=(int)$r["id"];
		}
		return $out;
	}
	function loadPic($inp)
	{
		$wer = '';
		$id = $inp;
		$aztarikh =((isset($_REQUEST['aztarikh1']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh1']):date("Y-m-d H:i:s"));
	        $aztarikh = explode(' ',$aztarikh);
        	$aztarikh = $aztarikh[0].' 00:0:00';;
	        $tatarikh = ((isset($_REQUEST['tatarikh1']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh1']):date("Y-m-d H:i:s + 1 day"));
        	$tatarikh = explode(' ',$tatarikh);
	        $tatarikh = $tatarikh[0].' 23:59:59';
		$daftar_id = ((isset($_REQUEST['daftar_id1']))?(int)$_REQUEST['daftar_id1']:-5);
//echo $daftar_id;
		mysql_class::ex_sql("select `daftar_id` from `upload` where `id`=$id",$q);
                if ($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $new_daftar = $r["daftar_id"];
		$out = "<u><span onclick=\"wopen('upload_pic.php?new_daftar=$new_daftar&daftar_id=$daftar_id&aztarikh=$aztarikh&tatarikh=$tatarikh&','',800,500);\"  style='color:blue;cursor:pointer;' >مشاهده</span></u>";
//		$out = "<a href='$inp' target='_blank' >مشاهده</a>";
                return($out);
	}
///////////
	function loadcount($inp)
        {
		$out=null;
		$wer = '';
		$id = $inp;
		$aztarikh =((isset($_REQUEST['aztarikh1']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh1']):date("Y-m-d H:i:s"));
	        $aztarikh = explode(' ',$aztarikh);
        	$aztarikh = $aztarikh[0].' 00:0:00';;
	        $tatarikh = ((isset($_REQUEST['tatarikh1']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh1']):date("Y-m-d H:i:s + 1 day"));
        	$tatarikh = explode(' ',$tatarikh);
	        $tatarikh = $tatarikh[0].' 23:59:59';
        	$daftar_id = ((isset($_REQUEST['daftar_id1']))?(int)$_REQUEST['daftar_id1']:-5);
	        $wer.="`tarikh`>'$aztarikh' and `tarikh`<'$tatarikh' and ";
		mysql_class::ex_sql("select `daftar_id` from `upload` where `id`=$id",$q);
                if ($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $new_daftar = $r["daftar_id"];
        	if($daftar_id ==-1)
	                $wer.=' `daftar_id`='.$new_daftar.' group by (`daftar_id`) ';
        	else
	                $wer.=" `daftar_id`=$daftar_id group by `daftar_id`";
		mysql_class::ex_sql("select count(`id`) as `totalPic` from `upload` where $wer",$q);
//		echo "select count(`id`) as `totalPic` from `upload` where $wer"; 
		if ($r=mysql_fetch_array($q,MYSQL_ASSOC))
	                $out = $r["totalPic"];
//echo $out."<br/>"; 
                return $out;
        }
///////////
	function date_parsi($inp)
	{
		return audit_class::hamed_pdate($inp);
	}
	function date_en($inp)
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
	$out ='';
	$out1 = '';
	$wer = '';
	$user_id = (int)$_SESSION['user_id'];
	$user = new user_class($user_id);
	$aztarikh_arr=array();
	if(isset($_FILES['uploadedfile']) && isset($_REQUEST['toz']) && isset($_REQUEST['mod']) && $_REQUEST['mod']==1 )
	{
		$toz = trim($_REQUEST['toz']);
		$tmp_target_path = "../upload";
		$ext = explode('.',basename( $_FILES['uploadedfile']['name']));
		$ext = $ext[count($ext)-1];
		if(strtolower($ext)=='jpg' || strtolower($ext)=='png' || strtolower($ext)=='gif' || strtolower($ext)=='tif' || strtolower($ext)=='jpeg' || strtolower($ext)=='pdf' || strtolower($ext)=='zip' || strtolower($ext)=='rar')
		{	
			$target_path =$tmp_target_path.'/'.$user->daftar_id.'_'.$user_id.'_'.basename( $_FILES['uploadedfile']['name']); 
			if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path))
			{
			
				mysql_class::ex_sqlx("insert into `upload` (`daftar_id`,`user_id`,`toz`,`pic_addr`) values ('".$user->daftar_id."','$user_id','$toz','') ");
				mysql_class::ex_sql("select * from `upload` order by `id` desc limit 1",$q);
				if($r = mysql_fetch_array($q))
				{
					$passvand = $r['id'];
					$tar = str_replace('gcom',$passvand,$target_path);
				
					if(rename($target_path,$tar))
					{
						mysql_class::ex_sqlx("update `upload` set `pic_addr`='$tar' where `id`=$passvand");
						$out = "<script> alert('ارسال با موفقیت انجام شد');//window.parent.location = 'index.php'; </script>";	
					}
					else
					{
						mysql_class::ex_sqlx("delete from `upload` where `id`=$passvand");
						$out =  "در ذخیره تصویر مشکلی پیش آمده ، لطفاًً مجدداً ارسال نمایید .";
					}
				}
			} else{
				$out =  "در ارسال تصویر مشکلی پیش آمده ، لطفاًً مجدداً ارسال نمایید .";
			}
		}
		else
		{
			$out =  "فایل ارسالی مجاز نمی باشد";
		}
	}
	//if(isset($_REQUEST['mod']) && $_REQUEST['mod']==2 )
	//{
	$aztarikh =((isset($_REQUEST['aztarikh1']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh1']):date("Y-m-d H:i:s"));
	$aztarikh = explode(' ',$aztarikh);
	$aztarikh = $aztarikh[0].' 00:0:00';;
	$tatarikh = ((isset($_REQUEST['tatarikh1']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh1']):date("Y-m-d H:i:s + 1 day"));
	$tatarikh = explode(' ',$tatarikh);
	$tatarikh = $tatarikh[0].' 23:59:59';
	$daftar_id = ((isset($_REQUEST['daftar_id1']))?(int)$_REQUEST['daftar_id1']:-5);
	//$aztarikh_arr = array('aztarikh1'=>$aztarikh,'tatarikh1'=>$tatarikh ,'mod'=>2);
	$wer.="`tarikh`>'$aztarikh' and `tarikh`<'$tatarikh' and ";
	if($daftar_id ==-1)
		{
			$name = "";
//			$wer.=" `daftar_id`<>0  and `name`<> $name group by `daftar_id`";
			$wer.=" `daftar_id`>0 group by `daftar_id`";
		}
	else
		{
			$wer.=" `daftar_id`=$daftar_id group by `daftar_id`";
		}
	$count_upload = null;
	mysql_class::ex_sql("select count(`id`) as `totalPic` from `upload` where $wer",$q);
        if ($r=mysql_fetch_array($q,MYSQL_ASSOC))
	        $count_upload = $r["totalPic"];
	if ($count_upload == 0 )
	{
		if ($daftar_id < 0)
		{
			$user_id =(int)$_SESSION['user_id'];
                        $user = new user_class($user_id);
                        $daftar_id=$user->daftar_id;
		}
		$out1 .= "<a href='upload_pic.php?daftar_id=$daftar_id&aztarikh=$aztarikh&tatarikh=$tatarikh&' target='_blank'><input type='button' style='margin:3px;' value='ارسال تصویر' class='btn btn-info inp' /></a>";
	}
	else
	{
		$out1 .= "";
	}

	
//}	
$out1.="<div class=\"box border orange\">
									
									<div class=\"box-body\" style=\"overflow-x:scroll\">
										<table class=\"table table-hover\">
											<thead>
											  <tr>
												<th style=\"text-align:right\">ردیف</th>
												<th style=\"text-align:right\">دفتر</th>
												<th style=\"text-align:right\">تعداد</th>
                                                <th style=\"text-align:right\">مشاهده</th>
											  </tr>
											</thead>
											<tbody>";
	mysql_class::ex_sql("select * from `upload` where $wer",$ss);

        while ($r = mysql_fetch_array($ss)){
            $daftar_id = $r['daftar_id'];
            $id = $r['id'];
            $out1.="<tr>
                <td>".$i."</td>
                <td>".$daftar_id."</td>
                <td>".loadcount($id)."</td>
                <td>".loadPic($id)."</td>
            </tr>";
            
        }
$out1.="</tbody></table></div></div>";
		/*$grid = new jshowGrid_new("upload","grid1");
		$grid->whereClause = $wer;
		$grid->pageCount = 50;
		$grid->columnHeaders[0] = null;
		$grid->columnHeaders[1] = 'دفتر';
		$grid->columnLists[1] = loadDaftar();
//		$grid->columnHeaders[2] = 'کاربر';
		$grid->columnHeaders[2] = null;
//		$grid->columnLists[2] = loadKarbar();
		$grid->columnHeaders[3] = null;
//		$grid->columnFunctions[3] = 'loadcount' ;
		$grid->columnHeaders[4] = null;
//		$grid->columnFunctions[4] = 'loadPic' ;
		$grid->columnHeaders[5] = null;
		$grid->columnHeaders[6] = null;
		$grid->addFeild('id');
                $grid->columnHeaders[7]='تعداد';
                $grid->columnFunctions[7]='loadcount';
		$grid->addFeild('id');
                $grid->columnHeaders[8]='مشاهده';
                $grid->columnFunctions[8]='loadPic';
//		$grid->columnHeaders[5] = 'تاریخ';
//		$grid->columnFunctions[5] = 'date_parsi';
		$grid->canEdit = FALSE;
		$grid->canAdd = FALSE;
		$grid->canDelete = FALSE;
		$grid->index_width= '20px';
		$grid->width= '95%';
		$grid->intial();
		$grid->executeQuery();
		$out1 .= $grid->getGrid();*/

$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>ارسال فایل</title>
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
				document.getElementById('mod').value= 1;
				var bool1 = ((document.getElementById('toz') && document.getElementById('toz').value!='')?true:false);
				var bool2 = ((document.getElementById('uploadedfile') && document.getElementById('uploadedfile').value!='')?true:false);
				document.getElementById('daftar_id1').value = document.getElementById('daftar_id').options[document.getElementById('daftar_id').selectedIndex].value;
				document.getElementById('aztarikh1').value = document.getElementById('datepicker1').value;
				document.getElementById('tatarikh1').value = document.getElementById('datepicker2').value;
				if(bool1 && bool2)
					document.getElementById('frm1').submit();
				else
					alert('کلیه اطلاعات باید وارد شود');
					
			}
			function send_search()
			{
				var bool1 = ((document.getElementById('datepicker1') && document.getElementById('datepicker1').value!='')?true:false);
				var bool2 = ((document.getElementById('datepicker2') && document.getElementById('datepicker2').value!='')?true:false);
				if(bool1 && bool2)
				{
					document.getElementById('daftar_id1').value = document.getElementById('daftar_id').options[document.getElementById('daftar_id').selectedIndex].value;
					document.getElementById('aztarikh1').value = document.getElementById('datepicker1').value;
					document.getElementById('tatarikh1').value = document.getElementById('datepicker2').value;
					document.getElementById('frm1').submit();
				}
				else
					alert('تاریخ ابتدا و انتها را انتخاب کنید');
				
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-upload"></i>ارسال فایل</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                                                 
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">دفتر:</label> 
                                    <div class="col-md-8">
                                            <?php echo loadDaftarCombo($daftar_id); ?>	
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">از تاریخ:</label> 
                                    <div class="col-md-8"><input value="<?php echo ((isset($_REQUEST['aztarikh1']))?$_REQUEST['aztarikh1']:''); ?>" type="text" name='aztarikh' readonly='readonly' class='form-control inp' id="datepicker1" />	
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">تا تاریخ:</label> 
                                    <div class="col-md-8"><input value="<?php echo ((isset($_REQUEST['tatarikh1']))?$_REQUEST['tatarikh1']:''); ?>" type="text" name='tatarikh' readonly='readonly' class='form-control inp' id="datepicker2" />
                                    
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <div class="col-md-12"><button class="btn btn-info col-md-8 pull-left" onclick="send_search();">جستجو</button></div>
                                </div>
                            </div>
                         
                            <?php echo $out1.' '.$out; ?>
                        </div>
                        <form enctype="multipart/form-data" method="POST" id="frm1" >
			<!--	<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
				<label>توضیحات</label>
				<input type="text" class="inp" name="toz" id="toz" >
				<input name="uploadedfile" type="file" id="uploadedfile" class="inp" /><br />
				<input type="button" class="inp" value="بروزرسانی" onclick="send_frm();" />-->
				<input type='hidden' name='mod' id='mod' value='2' >
				<input value="" type="hidden" name='aztarikh1'  id='aztarikh1' />	
				<input value="" type="hidden" name='tatarikh1' id='tatarikh1' />
				<input value="" type="hidden" name='daftar_id1' id='daftar_id1' />
			</form>
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