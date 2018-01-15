<?php
//	include_once("../kernel.php");
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadKeys($fkey)
	{
		$out = '<select name="fkey" id="fkey" class="inp" onchange="frm_submit();" >';
		mysql_class::ex_sql("select `id`,`fkey` from `statics` group by `fkey`",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = ($fkey==$r['fkey'])?'selected="selected"':'';
			$out .="<option $sel value='".$r['fkey']."' >".$r['fkey']."</option>\n";
		}
		$out .='</select>';
		return $out;
	}
	function listOtagh($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `room` where `en` = 1 and `id`='$inp'",$qq);
		while($row = mysql_fetch_array($qq))
			$out = $row['name'];
		return $out;
	}
	function loadGender()
	{
		$tmp = statics_class::loadByKey('جنسیت');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadMellait()
	{
		$tmp = statics_class::loadByKey('ملیت');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadMakan()
	{
		$tmp = statics_class::loadByKey('شهر');
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function loadNesbat()
	{
		$tmp = statics_class::loadByKey('نسبت');
		$out['سرگروه'] = '-1';
		for($i=0;$i<count($tmp);$i++)
			$out[$tmp[$i]->fvalue]=$tmp[$i]->id;
		return $out;
	}
	function hpdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function hpdateback($inp)
	{
		return(audit_class::hamed_pdateBack(perToEnNums($inp)));
	}	
	$msg = 'میهمانی برای این پذیرش ثبت نگردیده است';
	$reserve_id = (isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:'';
	$name_mehman = '';
	$tedad = '';
	$name_hotel = '';
	$hotel_id = room_class::loadHotelByReserve($reserve_id);
	mysql_class::ex_sql("select `name` from `hotel` where `id`='$hotel_id'",$q_hotel);
	if($r_hotel = mysql_fetch_array($q_hotel))
		$name_hotel = $r_hotel['name'];
	mysql_class::ex_sql("select count(`id`) as `tedad` from `room_det` where `reserve_id`='$reserve_id'",$qu);
	if($row = mysql_fetch_array($qu))
		$tedad = $row['tedad'];
	mysql_class::ex_sql("select `fname`,`lname` from `hotel_reserve` where `reserve_id`='$reserve_id'",$qu);
	if($row = mysql_fetch_array($qu))
		$name_mehman = $row['fname'].$row['lname'];	
	$msg = '';
	$GLOBALS['msg'] = '';
	$user = new user_class((int)$_SESSION['user_id']);
   
	/*$grid = new jshowGrid_new("mehman","grid1");
	$grid->index_width = '20px';
	$grid->width = '95%';
	$grid->showAddDefault = FALSE;
	$grid->whereClause="`reserve_id`='$reserve_id' order by `room_id`";
	$grid->columnHeaders[0] = null;			
	$grid->columnHeaders[1] = "شماره اتاق";
	$grid->columnFunctions[1] = "listOtagh";
	//$grid->columnLists[1] = listOtagh();
	$grid->columnHeaders[2] = null;
	$grid->columnHeaders[3] = 'نام';
	$grid->columnHeaders[4] = 'نام  خانوادگی';
	$grid->columnFilters[4] = TRUE;
	$grid->columnHeaders[5] ='ساعت  ورود' ;
	$grid->columnHeaders[6] = 'نام  پدر';
	$grid->columnHeaders[7] = 'شماره  شناسنامه';
	$grid->columnHeaders[8] = 'تاریخ  تولد';
	$grid->columnFunctions[8] = "hpdate";
	$grid->columnCallBackFunctions[8] = "hpdateback";
	$grid->columnHeaders[9] = 'جنسیت';
	$grid->columnLists[9]=loadGender();
	$grid->columnHeaders[10] = null;
	//$grid->columnLists[10]=loadMellait();
	$grid->columnHeaders[11] = null;
	//$grid->columnLists[11]=loadMakan();
	$grid->columnHeaders[12] = null;
	$grid->columnHeaders[13] = null;
	$grid->columnHeaders[14] = null;
	//$grid->columnLists[14]=loadMakan();
	$grid->columnHeaders[15] = null;
	//$grid->columnLists[15]=loadMakan();
	$grid->columnHeaders[16] = null;
	$grid->columnHeaders[17] = null;
	//$grid->columnLists[17]=loadNesbat();
	$grid->columnHeaders[18] = null;
	$grid->columnHeaders[19] = null;
	$grid->columnHeaders[20] = null;
	$grid->columnHeaders[21] = null;
	$grid->columnHeaders[22] = null;
	//$grid->columnJavaScript[22] ='onkeyup="monize(this);"';
	$grid->columnHeaders[23] = null;
	//$grid->columnJavaScript[23] ='onkeyup="monize(this);"';
	$grid->columnHeaders[24] = null;
	$grid->columnHeaders[25] = null;
	$grid->columnHeaders[26] = null;	
	$grid->pageCount = 500;	
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();*/
$root="";
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	
	<meta charset="utf-8" />
	<title>لیست مهمان های گروهی</title>
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
            
                $("#datepicker1").datepicker();
                $("#datepicker1btn").click(function(event) {
                    event.preventDefault();
                    $("#datepicker1").focus();
                })
            
                $("#datepicker2").datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true
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
                            <h4 style="margin-right:20px;"><i style="margin-left:10px;" class="fa fa-group"></i>لیست مهمان های گروهی</h4>
                                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <form id='frm1'  method='GET' >
                            <div class="row form-group" style="border-bottom:dashed thin #5e87b0">
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <label class="col-md-3 control-label">شماره رزرو:</label> 
                                    <div class="col-md-9"><input type="text" id="reserve_id" name="reserve_id" class="form-control" value="<?php echo ((isset($_REQUEST['reserve_id']))?$_REQUEST['reserve_id']:''); ?>"></div>
                                </div>
                                <div class="col-md-3" style="margin-bottom:5px;">
                                    <div class="col-md-12"><input type="submit" class="btn btn-info col-md-8 pull-left" value="جستجو" /></div>
                                </div>
                            </div>
                            </form>
                            <br/>
			<?php echo $msg.'<br/>'.$GLOBALS['msg']; ?>
			<table style='font-size:12px;' >
				<tr valign="bottom" >
					<td colspan='4'>
						هتل:
						(<?php echo $name_hotel; ?>)
					<td>	
						سرگروه :
(<?php echo $name_mehman; ?>)
					</td>					
					<td>
						تعداد اتاق :(<?php echo $tedad; ?>)
					</td>					
				</tr>
			</table>
                            <div class="dataTable_wrapper" id="myTable" style="overflow-x:scroll">
                                <table style="width:100%;margin-right:10px;" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;width:1px;">رديف</th>
                                            <th style="text-align:right;">شماره اتاق</th>
                                            <th style="text-align:right;">نام</th>
                                            <th style="text-align:right;">نام خانوادگی</th>
                                            <th style="text-align:right;">ساعت ورود</th>
                                            <th style="text-align:right;">نام پدر</th>
                                            <th style="text-align:right;">شماره شناسنامه</th>
                                            <th style="text-align:right;">تاریخ تولد</th>
                                            <th style="text-align:right;">جنسیت</th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
$i=1;
mysql_class::ex_sql("select * from `mehman` where `reserve_id`='$reserve_id' order by `room_id`",$s);
 
       while ($ss = mysql_fetch_array($s))
	{
           $rid = $ss['room_id'];
           mysql_class::ex_sql("select `name` from `room` where `id` = '$rid'",$qqqq);
           while($row = mysql_fetch_array($qqqq))
               $rname = $row['name'];
                $gender = $ss['gender'];
           $gen="";
               if($gender==11)
                    $gen="مرد";
                if($gender==12)
                    $gen="زن";
           if(fmod($i,2)!=0){
               echo "
                                       
                                        <tr class='odd'>
                                            <td>$i</td>
                                            <td>$rname</td>
                                            <td>$ss[fname]</td>
                                            <td>$ss[lname]</td>
                                            <td>$ss[vorood_h]</td>
                                            <td>$ss[p_name]</td>
                                            <td>$ss[ss]</td>
                                            <td>$ss[tt]</td>
                                            <td>$gen</td>
                                            

                                            
                                        </tr>";$i++;}
               else {echo"
                
                                        <tr class='even'>
                                            <td>$i</td>
                                            <td>$rname</td>
                                            <td>$ss[fname]</td>
                                            <td>$ss[lname]</td>
                                            <td>$ss[vorood_h]</td>
                                            <td>$ss[p_name]</td>
                                            <td>$ss[ss]</td>
                                            <td>$ss[tt]</td>
                                            <td>$gen</td>
                                           
                                            
                                            
                                        </tr>"; $i++;}}?>
                
                                     
                                       
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                          
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
                
                <a target='_blank' href='amaken_list.php?h_id=<?php echo $h_id;?>&'><button class="btn btn-pink">لیست اماکن</button></a>
               
			</div>
		</div>
	</section>
	<!--/PAGE -->
    	<!-- Modal -->
    <!-- Modal edit (Long Modal)-->
<div class="modal fade" id="edit-guest-list">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			
			<div class="modal-header" style="background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
				<button style="float:left" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">ویرایش اطلاعات مهمان ها</h4>
			</div>
			<div class="modal-body" style="max-height:400px;overflow-y:scroll">
               <form class="form-horizontal row-border" action="#">
                   	  
                   <div class="form-group col-md-12">
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">نام:</label> 
						    <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">نام خانوادگی:</label> 
						    <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">نام پدر:</label> 
						    <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                   </div> 
                   <div class="form-group col-md-12">
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">ش شناسنامه:</label> 
						    <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">ص شناسنامه:</label> 
						    <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">تاریخ تولد:</label> 
						    <div class="col-md-8"><input id="datepicker2" type="text" name="regular" class="form-control"></div>
                        </div>
                   </div>
                   <div class="form-group col-md-12">
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">جنسیت:</label> 
						    <div class="col-md-8"><select class="form-control"><option value="1">مرد</option><option value="2">زن</option></select>                               </div>
                        </div>
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">ملیت:</label> 
						    <div class="col-md-8"><select class="form-control"><option value="1">ایرانی</option><option value="2">غیر ایرانی</option></select></div>
                        </div>
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">کد ملی:</label> 
						    <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                                          
                   </div>
                   <div class="form-group col-md-12">
                        <div class="col-md-4">
                            <label class="col-md-4 control-label">شغل:</label> 
                            <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-md-4 control-label">دلیل سفر:</label> 
							<div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">مبدا:</label> 
						    <div class="col-md-8"><select class="form-control"><option value="-1"></option><option value="8">اصفهان</option></select></div>
                        </div>
                                              
				    </div>
                    <div class="form-group col-md-12">
                        <div class="col-md-4">
                            <label class="col-md-4 control-label">مقصد:</label> 
                            <div class="col-md-8"><select class="form-control"><option value="-1"></option><option value="8">اصفهان</option></select></div>
                              
                        </div>
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">نسبت:</label> 
						    <div class="col-md-8"><select class="form-control"><option value="-1"></option><option value="8">برادر</option></select></div>
                        </div>
                        <div class="col-md-4">
						    <label class="col-md-4 control-label">موبایل:</label> 
						    <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
          
					</div>
                   <div class="form-group col-md-12">
                        <div class="col-md-4">
                            <label class="col-md-4 control-label">ت ازدواج:</label> 
								<div class="col-md-8"><input id="datepicker4" type="text" name="regular" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-md-4 control-label">نام تور:</label> 
                                <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                       <div class="col-md-4">
                           <label class="col-md-4 control-label">پیش پرداخت:</label> 
                           <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                       </div>
        
          
                   </div>
                   <div class="form-group col-md-12">
                        <div class="col-md-4">
                            <label class="col-md-4 control-label">هزینه:</label> 
                            <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-md-4 control-label">هزینه اضافی:</label> 
                            <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="col-md-4 control-label">نفر اضافی:</label> 
                            <div class="col-md-8"><input type="text" name="regular" class="form-control"></div>
                        </div>
                   </div>
                   <div class="form-group col-md-12">
                        <div class="col-md-8">
                            <label class="col-md-2 control-label">توضیح:</label> 
                            <div class="col-md-6"><textarea rows="3" cols="5" name="textarea" class="form-control"></textarea></div>
                        </div>              
                   </div>  
                </form>	
			 </div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">انصراف</button>
                <button onclick="" type="button" class="btn btn-warning" data-dismiss="modal">بروزرسانی</button>
			</div>
                </form>
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