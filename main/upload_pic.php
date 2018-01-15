<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = $se->detailAuth('all');
	function loadDaftar()
	{
		$out=null;
		mysql_class::ex_sql("select `name`,`id` from `daftar` order by `id`",$q);
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
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = $se->detailAuth('all');
		if($isAdmin)
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
		echo "select `name`,`id` from `daftar` where $wer order by `name`";
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$sel = '';
			if($inp == (int)$r['id'])
				$sel = 'selected="selected"';
			$out.= "<option $sel value='".$r['id']."'>".$r['name']."</option>";
		}
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
		$out ='';
		//$out = "<u><span onclick=\"wopen('view_pic.php?pic_addr=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >مشاهده</span></u>";
		$out = "<a href='$inp' target='_blank' >مشاهده</a>";
//		$out .="<img src=\"$inp\" style=\"width:50px;height:50px;\"/>";
                return($out);
	}
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
	$daftar_id = ((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-5);
	$daftar_id = $_SESSION['daftar_id'];
	$sanad_record_id = (isset($_REQUEST['sanad_record_id']))?(int)$_REQUEST['sanad_record_id']:-1;
	$show = (isset($_REQUEST['show']))?(int)$_REQUEST['show']:-1;
	$aztarikh =((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:date("Y-m-d H:i:s"));
	$aztarikh = explode(' ',$aztarikh);
	$aztarikh = $aztarikh[0].' 00:00:00';
	$tatarikh = ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:date("Y-m-d H:i:s + 1 day"));
	$tatarikh = explode(' ',$tatarikh);
	$tatarikh = $tatarikh[0].' 23:59:59';
	$new_daftar = ((isset($_REQUEST['new_daftar']))?(int)$_REQUEST['new_daftar']:-5);
	$wer.="`tarikh`>'$aztarikh' and `tarikh`<'$tatarikh' and ";
	if ($daftar_id == -5)
	{
		$wer = '';
		$daftar_id = ((isset($_REQUEST['new_daftar']))?(int)$_REQUEST['new_daftar']:-5);
		echo $wer."<br/>";
	}
	$aztarikh_arr=array();
	if(/*isset($_FILES['uploadedfile']) && isset($_REQUEST['toz']) && isset($_REQUEST['mod']) && $_REQUEST['mod']==1*/ isset($_REQUEST['toz']))
	{
		$toz = trim($_REQUEST['toz']);
		$tmp_target_path = "../upload";
		$target_dir = "../upload/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
// 				echo "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
		} else {
				$out = "<script> alert ('فایل ارسالی مجاز نمی باشد '); </script>";
				$uploadOk = 0;
		}

// 		if (file_exists($target_file)) {
// 			echo "Sorry, file already exists.";
// 			$uploadOk = 0;
// 		}
		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 500000) {
				$out = "<script> alert ('حجم فایل ارسالی بیش از ۴۰۰ کیلو می باشد.');</script>";
				$uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			$out = "<script> alert ('فایل ارسالی مجاز نمی باشد '); </script>";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$out = "<script> alert ('در ارسال تصویر مشکلی پیش آمده، لطفا مجددا ارسال نمایید.');</script>";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
// 					echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
				$aztarikh1 = explode(' ',$aztarikh);
				$aztarikh1 = $aztarikh1[0].' 12:00:00';
				$tarikh = ($sanad_record_id==-1)?date("Y-m-d H:i:s"):$aztarikh1;
				$ln=mysql_class::ex_sqlx("insert into `upload` (`daftar_id`,`user_id`,`toz`,`pic_addr`,`tarikh`,`sanad_record_id`) values ('".$daftar_id."','$user_id','$toz','$target_file','$tarikh',$sanad_record_id) ",FALSE);
			
				$passvand = mysql_insert_id($ln);
				mysql_close($ln);

			} else {
					$out = "<script> alert ('در ارسال تصویر مشکلی پیش آمده، لطفا مجددا ارسال نمایید.');</script>";
			}
		}

// 		die();





	}
$wer = '';
/*
	if($daftar_id ==-5 && !($se->detailAuth('all') || $se->detailAuth('hesabdar') ))
		$wer.='upload.`daftar_id`='.$new_daftar;
	else if($daftar_id ==-5 && ($se->detailAuth('all') || $se->detailAuth('hesabdar') ) )
		$wer .= '';
	else
		$wer.="upload.`daftar_id`=$daftar_id";
		*/
	if($_SESSION['daftar_id']!=49){
		$wer.="upload.`daftar_id`=".$_SESSION['daftar_id'];
	}
	$wer.=(($wer!='')?' and ':'')." `sanad_record_id`=$sanad_record_id" ;
	
//}	
	$out1.="<div class=\"box border orange\">
									
									<div class=\"box-body\">
										<table class=\"table table-hover\">
											<thead>
											  <tr>
												<th style=\"text-align:right\">ردیف</th>
												<th style=\"text-align:right\">دفتر</th>
												<th style=\"text-align:right\">کاربر</th>
                                                <th style=\"text-align:right\">توضیح</th>
                                                <th style=\"text-align:right\">تصویر</th>
                                                <th style=\"text-align:right\">تاریخ</th>
                                                
											  </tr>
											</thead>
											<tbody>";
	$query = "select upload.id,upload.toz,pic_addr,concat(user.fname,' ',user.lname) uname,daftar.name dname,tarikh from `upload` left join user on (user.id=user_id) left join daftar on (daftar.id=upload.daftar_id) where $wer";
// echo $query;
	mysql_class::ex_sql($query,$ss);

        while ($r = mysql_fetch_array($ss)){
            $daftar_id = $r['dname'];
            $id = $r['id'];
            $user_id = $r['uname'];
            $toz = $r['toz'];
            $pic_addr = $r['pic_addr'];
            $tarikh = jdate("Y/m/d",strtotime($r['tarikh']));
          
            $out1.="<tr>
                <td>".$i."</td>
                <td>".$daftar_id."</td>
                <td>".$user_id."</td>
                <td>".$toz."</td>
                <td><a target='_blank' href='".$pic_addr."'>ضمیمه</a></td>
                <td>".$tarikh."</td>
            </tr>";
            
        }
$out1.="</tbody></table></div></div>";
	
		/*$grid = new jshowGrid_new("upload","grid1");
		$grid->setERequest(array('sanad_record_id'=>$sanad_record_id));
		$grid->whereClause = $wer;
		$grid->columnHeaders[0] = null;
		$grid->columnHeaders[1] = 'دفتر';
		$grid->columnLists[1] = loadDaftar();
		$grid->columnHeaders[2] = 'کاربر';
		$grid->columnLists[2] = loadKarbar();
		$grid->columnHeaders[3] = 'توضیح';
		$grid->columnHeaders[4] = 'تصویر';
		$grid->columnFunctions[4] = 'loadPic' ;
		$grid->columnHeaders[5] = 'تاریخ';
		$grid->columnFunctions[5] = 'date_parsi';
		$grid->columnHeaders[6] = null;
		$grid->canEdit = FALSE;
		$grid->canAdd = FALSE;
		$grid->canDelete = FALSE;
		$grid->index_width= '20px';
		$grid->width= '95%';
		$grid->intial();
		$grid->executeQuery();
		$out1 = $grid->getGrid();*/

$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>ضمیمه</title>
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
     function send_frm()
			{
				document.getElementById('mod').value= 1;
// 				var bool1 = ((document.getElementById('toz') && document.getElementById('toz').value!='')?true:false);
// 				var bool2 = ((document.getElementById('uploadedfile') && document.getElementById('uploadedfile').value!='')?true:false);
// 				document.getElementById('daftar_id1').value = document.getElementById('daftar_id').value;
// 				document.getElementById('aztarikh1').value = document.getElementById('aztarikh').value;
// 				document.getElementById('tatarikh1').value = document.getElementById('tatarikh').value;
// 				if(bool1 && bool2)
					document.getElementById('frm1').submit();
// 				else
// 					alert('کلیه اطلاعات باید وارد شود');
					
			}
			function send_search()
			{
				var bool1 = ((document.getElementById('datepicker6') && document.getElementById('datepicker6').value!='')?true:false);
				var bool2 = ((document.getElementById('datepicker7') && document.getElementById('datepicker7').value!='')?true:false);
				if(bool1 && bool2)
				{
					document.getElementById('daftar_id1').value = document.getElementById('daftar_id').options[document.getElementById('daftar_id').selectedIndex].value;
					document.getElementById('aztarikh1').value = document.getElementById('datepicker6').value;
					document.getElementById('tatarikh1').value = document.getElementById('datepicker7').value;
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
                            <div  <?php echo ($show>0)?'':'style="display:none;"'; ?> >
<!--		                <form enctype="multipart/form-data" method="POST" id="frm1" >
		                        <input type="hidden" name='daftar_id' id='daftar_id' value='<?php echo $daftar_id;?> '/>
		                        <input type="hidden" name='aztarikh' id='aztarikh' value='<?php echo $aztarikh;?>' />
		                        <input type="hidden" name='tatarikh' id='tatarikh' value='<?php echo $tatarikh;?>'/>
		                        <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
		                        <label>توضیحات</label>
		                        <input type="text" class="inp" name="toz" id="toz" >
		                        <input name="uploadedfile" type="file" id="uploadedfile" class="inp" /><br />
		                        <input type="button" class="inp" value="بروزرسانی" onclick="send_frm();" />
		                        <input type='hidden' name='mod' id='mod' value='2' >
		                        <input type="hidden" name='aztarikh1'  id='aztarikh1' />
		                        <input type="hidden" name='tatarikh1' id='tatarikh1' />
		                        <input type="hidden" name='daftar_id1' id='daftar_id1' />
					<input type="hidden" name='sanad_record_id'  id='sanad_record_id' value="<?php echo $sanad_record_id; ?>" />
		                </form>-->

			</div>
			<br/>
			<br/>
			<?php
                                echo $out1.' '.$out;
                         ?>
                        </div>
<!--                        <form enctype="multipart/form-data" method="POST" id="frm1" >
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
				<label>توضیحات</label>
				<input type="text" class="inp" name="toz" id="toz" >
				<input name="uploadedfile" type="file" id="uploadedfile" class="inp" /><br />
				<input type="button" class="inp" value="بروزرسانی" onclick="send_frm();" />
				<input type='hidden' name='mod' id='mod' value='2' >
 				<input value="" type="hidden" name='aztarikh1'  id='aztarikh1' />	
				<input value="" type="hidden" name='tatarikh1' id='tatarikh1' />
				<input value="" type="hidden" name='daftar_id1' id='daftar_id1' /> 
			</form>-->
<form action="" method="post" enctype="multipart/form-data"  id="frm1">
	<label>توضیحات</label>
	<input type="text" class="inp" name="toz" id="toz" >
	<input type='hidden' name='mod' id='mod' value='2' >
	<input type="hidden" name='sanad_record_id'  id='sanad_record_id' value="<?php echo $sanad_record_id; ?>" />
	<input type="file" name="fileToUpload" id="fileToUpload">
<!-- 	<input type="button" class="inp" value="بروزرسانی" onclick="send_frm();" /> -->
	<input type="submit" value="بروزرسانی" name="submit">
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