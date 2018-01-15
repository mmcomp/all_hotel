<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
					die(lang_fa_class::access_deny);
	$se = security_class::auth((int)$_SESSION['user_id']);
	//var_dump($_SESSION);
	if(!$se->can_view)
					die(lang_fa_class::access_deny);
	$is_admin = TRUE;
	$user_typ = '';
// 	if($se->detailAuth('all'))
// 					$is_admin = TRUE;	
	function  loadHotel($inp=-1)
	{
		$inp = (int)$inp;
		$hotelList=daftar_class::hotelList((int)$_SESSION['daftar_id']);
		$shart = ' and 1=0';
		if($hotelList)
			$shart=' and ( `id`='.implode(" or `id`=",$hotelList).")";
		$out = '<select name="hotel_id" class="form-control inp" style="width:auto;"  onchange="document.getElementById(\'frm1\').submit();">';
		$out .= '<option value="-1"></option>';
		mysql_class::ex_sql("select `id`,`name` from `hotel` where `is_our`!=2 and `moeen_id` > 0 $shart order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = (($r['id']==$inp)?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	$hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);
	$hotel = new hotel_class($hotel_id);
	$d = ((isset($_REQUEST['d']))?$_REQUEST['d']:perToEnNums(jdate("m")));
	$y = ((isset($_REQUEST['y']))?$_REQUEST['y']:perToEnNums(jdate("Y")));
	if($se->detailAuth('garanti'))
	{
		$user_typ = 'garanti';
		if ($y =='1392')
			$month = array('بهمن','اسفند');
		elseif ((int)$y >1392)
			$month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
		else
			$month = array();
	}
	elseif($se->detailAuth('dafater'))
	{
		$user_typ = 'dafater';
		$month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
	}
	else
		$month = array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');
	
	$da = audit_class::hamed_pdateBack(jdate("$y/$d/1"));
	$tmp = explode(" ",$da);
	$da = $tmp[0];
	$hotel = new hotel_class($hotel_id);
	$hotel->setRoomJavaScript = TRUE;
	$out = $hotel->loadRooms($da,$is_admin,'f1',$user_typ);
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>شیت هتل</title>
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
    <link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
<!-- DataTables JavaScript -->
    <!-- JQUERY -->
<script src="<?php echo $root ?>js/jquery/jquery-2.0.3.min.js"></script>
    <script src="<?php echo $root ?>datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $root ?>datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/bootstrap-datepicker.fa.min.js"></script>
    
    <link type="text/css" href="<?php echo $root ?>window/css/jquery.window.css" rel="stylesheet" />

                <!-- JavaScript Includes -->
    <script type="text/javascript" src="../js/tavanir.js"></script>
    <script type="text/javascript" src="<?php echo $root ?>window/jquery.window.js"></script> 
    <script language="JavaScript">
			var res_id;
			var room;
			function f1(reserve_id,room_id)
			{
				//alert(reserve_id+','+room_id);
				res_id  = reserve_id;
				room = room_id;
				if(document.getElementById('openw'))
				{
					$.window({
						title: "جزئیات",
						width: 600,
						height: 400,
						content: $("#window_block8"),
						containerClass: "my_container",
						headerClass: "my_header",
						frameClass: "my_frame",
						footerClass: "my_footer",
						selectedHeaderClass: "my_selected_header",
						createRandomOffset: {x:0, y:0},
						showFooter: false,
						showRoundCorner: true,
						x: 0,
						y: 0,
						url: "gaantinfo.origin.php?reserve_id="+reserve_id+"&room_id="+room_id+"&"
					});
				}
				else
					alert('not');
			}
			function resizeText(multiplier) 
			{
				if (document.body.style.fontSize == "") 
				{
				    document.body.style.fontSize = "1.0em";
				}
				document.body.style.fontSize = parseFloat(document.body.style.fontSize) + (multiplier * 0.2) + "em";
			}
			function resizeDef() 
                        {
                                    document.body.style.fontSize = "0.8em";
                        }
			$(document).ready(function(){
				$(window).scroll(function(inp,inp1){
					//console.log(inp1);
					//console.log('windowHeight = '+$(window).height()+',sc='+$(window).scrollTop());
				});
// 				$("tr").mouseover(function(evt){
// 					$(".moveHeader").remove();
// 					var ht = '<tr class="moveHeader">'+$("#first_tr").html()+'</tr>';
// 					$(evt.currentTarget).before(ht);
// 				});
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-building"></i>شیت هتل</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" id="panel-body" style="overflow-x:scroll">
                           <form id="frm1" method="get">
                               <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-12 control-label">وضعیت رزرو <?php echo loadHotel($hotel_id);//echo $hotel->name; ?> در :</label> 
                                    
                                </div>
				
                               <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">سال:</label> 
                                    <div class="col-md-8">
                                        <select name="y" class="form-control inp" onchange="document.getElementById('frm1').submit();">
				<?php
                                        for($i=1395;$i<=1400;$i++)
                                                echo "<option value=\"$i\"".(($i==$y)?"selected=\"selected\"":"").">\n$i\n</option>\n";
                                ?>
				</select>    
                                    </div>
                                </div>
                               
                               <div class="col-md-4" style="margin-bottom:5px;">
                                    <label class="col-md-4 control-label">ماه:</label> 
                                    <div class="col-md-8">
                                        <select name="d" class="form-control inp" onchange="document.getElementById('frm1').submit();">
				<?php
					for($i=1;$i<=count($month);$i++)
						echo "<option value=\"$i\"".(($i==$d)?"selected=\"selected\"":"").">\n".$month[$i-1]."\n</option>\n";
				?>
				</select>  
                                    </div>
                                </div>
                               
				
				
				
<!-- 				<input type="hidden" id="hotel_id" name="hotel_id" value="<?php //echo $hotel_id; ?>" /> -->
			</form>
			<br/>
			<br/>
			<!--<input type='button' value='بزرگ نمایی' class='inp' onclick='resizeText(1);' >
                        <input type='button' value='کوچک نمایی' class='inp' onclick='resizeText(-1);' >
			<input type='button' value='حالت پیش فرض' class='inp' onclick='resizeDef();' >-->
                        <?php
				//echo jdate("F",strtotime($da));
				echo $out;
                        ?>
                </div>
		<div id="openw" >
		</div>


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
            
                $("#openw").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "جزئیات",
                width: 500,
                height: 150,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:0, y:0},
                showFooter: false,
                showRoundCorner: true,
                x: 0,
                y: 0,
                url: "gaantinfo.php?reserve_id="+res_id
        });
    });
            
            $("#loading").hide(); 
            App.setPage("gallery");  //Set current page
			//App.setPage("widgets_box");  //Set current page
			App.init(); //Initialise plugins and elements
			getofflist();
            
            
             $('#dataTables-example').DataTable({
                responsive: true
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