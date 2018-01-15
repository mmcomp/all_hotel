<?php
session_start();
	include("../kernel.php");
	include("../simplejson.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view || !isset($_REQUEST['room_id']) || !isset($_REQUEST['reserve_id']))
                die(lang_fa_class::access_deny);
	$is_admin = FALSE;
	if($se->detailAuth('all'))
		$is_admin = TRUE;
	function loadQues($ques_id)
	{
		$out = '----';
		$ques_id = (int)$ques_id;
		mysql_class::ex_sql("select name from ravabet_ques where id = $ques_id",$q);
		if($r = mysql_fetch_array($q))
			$out = $r['name'];
		return($out);
	}
	$user_id = (int)$_SESSION['user_id'];
	$GLOBALS['msg'] = '';
	//$room_id = (int)$_REQUEST['room_id'];
	$room_id = $_GET['room_id'];
    //$reserve_id = (int)$_REQUEST['reserve_id'];
	$reserve_id = $_GET['reserve_id'];
    $ravabet_id = -1;
	$tarikh = '----';
	mysql_class::ex_sql("select id,tarikh from ravabet where room_id = $room_id and reserve_id = $reserve_id",$q);
	if($r = mysql_fetch_array($q))
	{
		$ravabet_id = (int)$r['id'];
		$tarikh = ($r['tarikh'] != '0000-00-00 00:00:00')?jdate("Y/m/d",strtotime($r['tarikh'])):'----';
	}
	if($ravabet_id == -1)
	{
		$ln = mysql_class::ex_sqlx("insert into ravabet (room_id,reserve_id,tarikh,user_id) values ($room_id,$reserve_id,'$tarikh',$user_id)",FALSE);
		$ravabet_id = mysql_insert_id($ln);
		mysql_close($ln);
		$q = null;
		mysql_class::ex_sql("select id from ravabet_ques order by id",$q);
		$ans = '';
		while($r = mysql_fetch_array($q))
			$ans .= (($ans!='')?' , ':'')."($ravabet_id,".$r['id'].")";
		mysql_class::ex_sqlx("insert into ravabet_det (ravabet_id,ravabet_ques_id) values $ans");
	}

$out='<div class="box border orange">
									
									<div class="box-body">
										<table class="table table-hover">
											<thead>
											  <tr>
												<th style="text-align:right">ردیف</th>
												<th style="text-align:right">سوال</th>
												<th style="text-align:right">پاسخ</th>
                                                <th style="text-align:right">توضیحات</th>
											  </tr>
											</thead>
											<tbody>';

    mysql_class::ex_sql("select * from ravabet_ques",$ss);
$i=1;
    while($r = mysql_fetch_array($ss)){
        $name = $r['name'];
        $qid = $r['id'];
        if(fmod($i,2)!=0){
            $out.="
            <tr class=\"odd\">
                <td>$i</td>
                <td>$name</td>
                <td>
                <select class=\"form-control inp ans\" name=\"ans\" id=\"ans\"  onchange=\"updateQ('".$qid."','".$room_id."','".$reserve_id."','".$user_id."','".$tarikh."');\" >
                    <option value=\"0\"></option>
                    <option value=\"1\">بد</option>
                    <option value=\"2\">متوسط</option>
                    <option value=\"3\">خوب</option>
                    <option value=\"4\">عالی</option>
                </select>
                </td>
                <td><input class=\"form-control\" type=\"text\" id=\"toz".$qid."\" name=\"toz\" value=\"\" onkeypress=\"handleKeyPress(event,".$qid.")\" /></td>
            </tr>
            ";
            $i++;
        }
        else{
            $out.="
            <tr class=\"even\">
                <td>$i</td>
                <td>$name</td>
                <td>
                <select class=\"form-control inp ans\" name=\"ans\" id=\"ans\"  onchange=\"updateQ('".$qid."','".$room_id."','".$reserve_id."','".$user_id."','".$tarikh."');\" >
                    <option value=\"0\"></option>
                    <option value=\"1\">بد</option>
                    <option value=\"2\">متوسط</option>
                    <option value=\"3\">خوب</option>
                    <option value=\"4\">عالی</option>
                </select>
                </td>
                <td><input class=\"form-control\" type=\"text\" id=\"toz".$qid."\" name=\"toz\" value=\"\" onkeypress=\"handleKeyPress(event,".$qid.")\" /></td>
            </tr>
            ";
            $i++;
        }
    }
$out.='</tbody></table></div></div>';
	/*$grid = new jshowGrid_new("ravabet_det","grid1");
	$grid->width = '99%';
	$grid->index_width = '20px';
	$grid->whereClause = " ravabet_id = $ravabet_id order by `id`";
	$grid->columnHeaders[0] ='';
	$grid->columnHeaders[1] ="";
	$grid->columnHeaders[2] ="سوال";
	$grid->columnAccesses[2] = 0;
	$grid->columnFunctions[2] = "loadQues";
	$grid->columnHeaders[3] ="پاسخ";
	$grid->columnLists[3] = array(
					'بد'=>1,
					'متوسط'=>2,
					'خوب'=>3,
					'عالی'=>4
				);
	$grid->columnAccesses[3] = $se->detailAuth('ravabet') || $is_admin;
	$grid->columnHeaders[4] ="توضیحات";
	$grid->columnAccesses[4] = $se->detailAuth('ravabet') || $is_admin;
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	$grid->pageCount = 0;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();*/
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>نظرسنجی</title>
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-bar-chart-o"></i>مشاهده نظرسنجی</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body">
                            <?php echo '<h2>'.$GLOBALS['msg'].'</h2>' ?>
                            <br/>
                            <form id="frm1" method="get">
                                <div class="nazar">
                                    <?php echo $out;  ?>
                                    <!--<button class="btn btn-info col-md-4 pull-right" onclick="send_search();">افزودن نظر</button>-->
                                </div>
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
        function handleKeyPress(e,id){
            var qid = id;
            var id = "toz"+qid;
                var key=e.keyCode || e.which;
                if (key==13){
                    var toz = document.getElementById(id).value;
                    alert(toz);
        }
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
        
        function updateQ(id,room_id,reserve_id,user_id,tarikh){
            var qid = id;
            var room_id = room_id;
            var reserve_id = reserve_id;
            var user_id = user_id;
            var tarikh = tarikh;
            var ans = $("#ans option:selected" ).val();
            $.post("ravabetAjax.php",{qid:qid,room_id:room_id,reserve_id:reserve_id,user_id:user_id,tarikh:tarikh,ans:ans},function(data){           
                StopLoading();
                if(data=="0")
                    alert("خطا در درج نظر");
                if(data=="1"){
                    alert("درج نظر با موفقیت انجام شد");
                    location.reload();
                }
            });
        
        }
        
    function send_search(){
        $(".nazar .text-field").each(function() {
        alert($(this).val());
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