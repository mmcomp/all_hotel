<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
	$user = new user_class((int)$_SESSION['user_id']);
	$isAdmin = ($user->user=='mehrdad')?TRUE:FALSE;
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
	function listOtagh()
	{
		$out = array();
		$reserve_id = hexdec($_REQUEST['reserve_id'])-10000;
		mysql_class::ex_sql("select `room_id` from `room_det` where `reserve_id`=$reserve_id",$q);
		while($r = mysql_fetch_array($q))
	        {
			$room_id = $r["room_id"];
			mysql_class::ex_sql("select `name` from `room` where `id`=$room_id",$qq);
			while($row = mysql_fetch_array($qq))
			{
				$name_room = $row['name'];
				$out[$name_room]= $room_id;
			}
		}
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
	function loadMakan1()
	{
		$tmp = statics_class::loadByKey('شهر');
		$out['مشهد']=1;
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
	function hpdateback1($inp)
	{
		$out = '';
		if ($inp=='0000-00-00')
			$out = '';
		else
			$out = audit_class::hamed_pdateBack(perToEnNums($inp));
		return($out);
	}	
	function hpdate1($inp)
	{
		$out = '';
		if ($inp=='0000-00-00')
			$out = '';
		else
			$out = audit_class::hamed_pdate($inp);
		return($out);
	}
	function add_item()
	{
		$user = new user_class((int)$_SESSION['user_id']);
		$isAdmin = ($user->user=='mehrdad')?TRUE:FALSE;
		$fields = jshowGrid_new::loadNewFeilds($_REQUEST);
		$fields['reserve_id'] = hexdec($_REQUEST['reserve_id'])-10000;
		if((int)$fields['room_id']>0)
		{
			$reserve_id = $fields['reserve_id'];
			mysql_class::ex_sql("select `id`,`room_id`,`tatarikh` from `room_det` where `reserve_id`=$reserve_id and `room_id`=".(int)$fields['room_id']." order by `tatarikh` desc",$q);
		        while($r = mysql_fetch_array($q))
			        mysql_class::ex_sqlx("update `room` set `vaziat` = 0 where `id` = ".(int)$r['room_id']);
			unset($fields['id']);
			foreach($fields as $ss=>$value)
				if($value=='')
					unset($fields[$ss]);
			if(isset($fields['tt']))
				$fields['tt'] = hpdateback($fields['tt']);
			if(isset($fields['hazine']))
				$fields['hazine'] = umonize($fields['hazine']);
			if(isset($fields['hazine_extra']))
				$fields['hazine_extra'] = umonize($fields['hazine_extra']);
			$fields['vorood_h'] = date("h:i:s");
			$qu = jshowGrid_new::createAddQuery($fields);
			mysql_class::ex_sqlx("insert into `mehman` ".$qu['fi']." values ".$qu['valu']);	
		}
		else
				echo "<script>alert('شماره اتاق وارد نشده است');</script>";
	}
	function edit_item($id,$field,$value)
	{
		if($field=='hazine' || $field=='hazine_extra')
			$value = umonize($value);
		if($field=='tt')
			$value = hpdateback($value);
		if($field=='t_ezdevaj')
			$value = hpdateback($value);
		mysql_class::ex_sqlx("update `mehman` set $field='$value' where `id`=$id ");
	}	
///////////////////////
$room_name = '';	
	$room_id = isset($_REQUEST['room_id']) ?(int)$_REQUEST['room_id']:-1;
	mysql_class::ex_sql("select `name` from `room` where `id`=$room_id",$qq);
	if($row = mysql_fetch_array($qq))
		$name_room = $row['name'];
	$reserve_tmp = isset($_REQUEST['reserve_id']) ?(int)$_REQUEST['reserve_id']:-1;
	$reserve_id = hexdec($reserve_tmp)-10000;
	if(isset($_REQUEST['name1']))
	{
		$num_room=$_REQUEST['name1'];
		$ma_sodor=$_REQUEST['name2'];
		$tour_name=$_REQUEST['name3'];
		$name=$_REQUEST['name4'];
		$job=$_REQUEST['name5'];
		$p_par=$_REQUEST['name6'];
		$lname=$_REQUEST['name7'];
		$d_safar=$_REQUEST['name8'];
		$cost=$_REQUEST['name9'];
		$h_enter=$_REQUEST['name10'];
		$sour=$_REQUEST['name11'];
		$ex_cost=$_REQUEST['name12'];
		$name_f=$_REQUEST['name13'];
		$des=$_REQUEST['name14'];
		$ex_person=$_REQUEST['name15'];
		$ss=$_REQUEST['name16'];
		$meli=$_REQUEST['name17'];
		//$tt=date(('Y-m-d'),strtotime($_REQUEST['name18']));
		$tt = audit_class::hamed_pdateBack($_REQUEST['name18']);
		$rel=$_REQUEST['name19'];
		$gender=$_REQUEST['name20'];
		$t_ezde=audit_class::hamed_pdateBack($_REQUEST['name21']);
		$nation=$_REQUEST['name22'];
		$mob=$_REQUEST['name23'];	
		$r_id=$_REQUEST['name24'];
		$res_id=$_REQUEST['name25'];	
		$query=new mysql_class;
		$query->ex_sqlx("insert into `mehman`
				(`room_id`,`reserve_id`,`fname`,`lname`,`vorood_h`,`p_name`,`ss`,`tt`,`gender`,`melliat`,
				`ms`,`job`,`safar_dalil`,`mabda`,`maghsad`,`code_melli`,`nesbat`,`t_ezdevaj`,
				`hamrah`,`toor_name`,`pish_pardakht`,`toz`,`hazine`,`hazine_extra`,`tedad_extra`,`khorooj`) 
				values ('$r_id','$res_id','$name','$lname','$h_enter','$name_f','$ss','$tt',
				'$gender','$nation','$ma_sodor','$job','$d_safar','$sour','$des','$meli','$rel','$t_ezde','$ex_person',
				'$tour_name','$p_par','','$cost','$ex_cost','$ex_person', '0000-00-00 00:00:00')");
		mysql_class::ex_sql("select `id`,`room_id`,`tatarikh` from `room_det` where
		`reserve_id`='$res_id' and `room_id`='$r_id'",$qur);
	        if($row2 = mysql_fetch_array($qur))
		{
			$room=$row2['room_id'];	 	
		        mysql_class::ex_sqlx("update `room` set `vaziat`=0 where `id` =$room");
			
		} 
		
		die("ok");
	}
//////////////////////
	$jensiat = "";
	$tmp = statics_class::loadByKey('جنسیت');	
	$jensiat .= "<select name='name20' id='id20' class='cl'>";
	$jensiat .= "<option value='-1'> </option>";
		for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$jensiat .= "<option value='$id'>$val</option>";
		}
	$jensiat .= "</select>";

	$meli="";
	$tmp = statics_class::loadByKey('ملیت');
	$meli.="<select name='name22' id='id22' class='cl'>";
	$meli .="<option value='-1'> </option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$meli .= "<option value='$id'>$val</option>";
		}
	$meli.="</select>";

	$sodor="";
	$tmp = statics_class::loadByKey('شهر');
	$sodor.="<select name='name2' id='id2' class='cl'>";
	$sodor .="<option value='-1'> </option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$sodor .= "<option value='$id'>$val</option>";
		}
	$meli.="</select>";


	$sour="";
	$tmp = statics_class::loadByKey('شهر');
	$sour.="<select name='name11' id='id11' class='cl'>";
	$sour .="<option value='-1'> </option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$sour .= "<option value='$id'>$val</option>";
		}
	$sour.="</select>";

	$de="";
	$tmp = statics_class::loadByKey('شهر');
	$de.="<select name='name14' id='id14' class='cl'>";
	$de .="<option value='1'> مشهد</option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$de .= "<option value='$id'>$val</option>";
		}
	$de.="</select>";

	$rel="";
	$tmp = statics_class::loadByKey('نسبت');
	$rel.="<select name='name19' id='id19' class='cl'>";
	$rel .="<option value='-1'></option>";
	for($i=0;$i<count($tmp);$i++)
		{
			$id = $tmp[$i]->id;
			$val = $tmp[$i]->fvalue;
			$rel .= "<option value='$id'>$val</option>";
		}
	$rel.="</select>";

	if(isset($_REQUEST['reserve_id']))
	{		
		$reserve_id = hexdec($_REQUEST['reserve_id'])-10000;
		if (isset($room_id))
			$shart = "`room_id`='$room_id' and `reserve_id`='$reserve_id'";
		else
			$shart = "`reserve_id`='$reserve_id'";
		$khorooj= isset($_REQUEST['kh'])?(int)$_REQUEST['kh']:0;
		if($khorooj==1)
		{
			$user_id=(int)$_SESSION['user_id'];
			mehman_class::khorooj($reserve_id,$room_id,$user_id);
			
			$out = "<h2>خروج مهمان با موفقیت انجام شد</h2>";
		}
		else
		{
			$q = null;
			$now = date("Y-m-d 23:59:59");
			$now_delay =date("Y-m-d 00:00:00",strtotime($now.' -'.$conf->limit_paziresh_day.' day'));
			$is_available = FALSE;
			mysql_class::ex_sql("select `id` from `room_det` where `reserve_id`=$reserve_id and `aztarikh`>='$now_delay' and `aztarikh`<='$now' ",$q);
			if($r = mysql_fetch_array($q,MYSQL_ASSOC))
				$is_available = TRUE;
			$grid = new jshowGrid_new("mehman","grid1");
			$grid->width = '99%';
			$grid->index_width = '20px';
			$grid->showAddDefault = FALSE;
			$grid->whereClause = $shart.' order by `lname`';
			$grid->columnHeaders[0] = null;			
			$grid->columnHeaders[1] = "شماره اتاق";
			$grid->columnLists[1] = listOtagh();
			$grid->columnHeaders[2] = null;
			$grid->columnHeaders[3] = 'نام';
			$grid->columnHeaders[4] = 'نام  خانوادگی';
		       	$grid->columnHeaders[5] ='ساعت  ورود' ;
			$grid->columnAccesses[5] = 0;
			$grid->columnHeaders[6] = 'نام  پدر';
			$grid->columnHeaders[7] = 'شماره  شناسنامه';
			$grid->columnHeaders[8] = 'تاریخ  تولد';
			$grid->columnFunctions[8] = "hpdate";
			$grid->columnCallBackFunctions[8] = "hpdateback";
			$grid->columnHeaders[9] = 'جنسیت';
			$grid->columnLists[9]=loadGender();
			$grid->columnHeaders[10] = 'ملیت';
			$grid->columnLists[10]=loadMellait();
			$grid->columnHeaders[11] = 'محل‌صدور  شناسنامه';
			$grid->columnLists[11]=loadMakan();
			$grid->columnHeaders[12] = 'شغل';
			$grid->columnHeaders[13] = 'دلیل  سفر';
			$grid->columnHeaders[14] = 'مبدأ';
			$grid->columnLists[14]=loadMakan();
			$grid->columnHeaders[15] = 'مقصد';
			$grid->columnLists[15]=loadMakan1();
			$grid->columnHeaders[16] = 'کد‌ملی';
			$grid->columnHeaders[17] = 'نسبت';
			$grid->columnLists[17]=loadNesbat();
			$grid->columnHeaders[18] = 'تاریخ ازدواج';
			$grid->columnFunctions[18] = "hpdate1";
			$grid->columnCallBackFunctions[18] = "hpdateback1";
			$grid->columnHeaders[19] = 'موبایل';
			$grid->columnHeaders[20] = 'نام تور';
			$grid->columnHeaders[21] = 'پیش پرداخت';
			$grid->columnHeaders[22] = 'توضیحات';
			$grid->columnHeaders[23] = 'هزینه';
			$grid->columnJavaScript[23] ='onkeyup="monize(this);"';
			$grid->columnHeaders[24] = 'هزینه اضافی';
			$grid->columnJavaScript[24] ='onkeyup="monize(this);"';
			$grid->columnHeaders[25] = 'نفراضافه';
			$grid->columnHeaders[26] = null;			
			//$grid->sortEnabled = TRUE;
			$grid->hideIndex = 10;
			$b = !(reserve_class::isKhorooj($reserve_id,$room_id) && !$se->detailAuth('all')) && ($is_available || $se->detailAuth('all'));
			$grid->canEdit = $b;
			$grid->canAdd = FALSE;
			$grid->canDelete = $b;
			$grid->addFunction = 'add_item';
			$grid->editFunction = 'edit_item';
			$grid->intial();
		   	$grid->executeQuery();
			$out = $grid->getGrid();
		}
	}
	else
		$out ='خطا در اطلاعات';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />
		<style>

		.cl
		{
				
		}
		</style>
		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		تعریف ها
		</title>
		<script langauge="javascript">
		function frm_submit()
		{
			document.getElementById('frm1').submit();
		}
		
		function vali()
		{
			var par = {};
			var out = true;
			$(".cl").each(function(id,field){
				if(($(field).is('input') && $(field).val() == '')||($(field).is('select') && $(field).val() == '-1'))
					out= false;
				par[$(field).attr('name')] = $(field).val();
			});
			if(out)
			{
				//console.log(par);
				$.post("paziresh.php",par,function(result){
					if (result=='ok')
					{
						alert('اطلاعات با موفقیت ثبت گردید');
						location.reload(); 
					}
					else
						alert('ثبت اطلاعات با مشکل مواجه است');
						
					
				});
			}
			else
				alert('لطفا همه موارد وارد شود');
			return(out);
		}
		function f1(obj){
			if(obj){
				if(obj.value){
					var str=unFixNums(String(obj.value));
					var datearr=str.split('/')
					var b=true;
					if(datearr.length==3){
						var d=datearr[0];
						var m=datearr[1];
						var y=datearr[2];
						var tmp;
						if(parseInt(d,10)>parseInt(y,10)){
							tmp=d;
							d=y;
							y=tmp;
						}
						if((parseInt(y,10)>=100)&&(parseInt(y,10)<=1356)){
							b=false;
						}
						if((parseInt(m,10)<=0)||(parseInt(m,10)>=13)){
							b=false;
						}
						
						if((parseInt(d,10)<=0)||(parseInt(d,10)>31)){
							b=false;
						}
						if(parseInt(y,10)<1300){
							y=String(parseInt(y,10)+1300);
						}
						if((parseInt(d,10)<10)&&(d.length==1)){
							d='0'+d;
						}
						if((parseInt(m,10)<10)&&(m.length==1)){
							m='0'+m;
						}
						if(b){
							obj.value=FixNums(y+'/'+m+'/'+d);
						}else{
							obj.value='';
							obj.focus();
							alert('فرمت تاریخ ایراددار است');
						}
					}
					else{
						alert('تاریخ صحیح نمی باشد');
						obj.value='';
						obj.focus();
					}
				}
			}
		}
	</script> 
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" class="general_div" >
			<?php
				echo $out;
			?>
		</div>
	<center>
	<table  class="general_div" align="center"  >
		<tr>
		<td colspan='6' align='center'><h1>فرم اطلاعات ‍ پذیرش <h1></td>
		</tr>

		<!--<form action="" method="get" onsubmit="return vali();">-->
		<tr><td>شماره اتاق :</td>
		<td><input type="text" name="name1" id="id1" class="cl" size="15" disabled=true value="<?php echo $name_room;?>"></td>
		<td>محل صدورشناسنامه :</td>
		<td><?php echo $sodor; ?></td>
		<td>نام تور :</td>
		<td><input type="text" name="name3" id="id3" class="cl" size="15"></td>
		</tr>
		

		<tr><td >نام:</td>
		<td><input type="text" name="name4" id="id4" class="cl" size="15"></td>
		<td>شغل :</td>
		<td><input type="text" name="name5" id="id5" class="cl" size="15"></td>
		<td> پیش پرداخت :</td>
		<td><input type="text" name="name6" id="id6" class="cl" size="15"></td>
		</tr>


		<tr><td>نام خانوادگی :</td>
		<td><input type="text" name="name7" id="id7" class="cl" size="15"></td>
		<td>دلیل سفر :</td>
		<td><input type="text" name="name8" id="id8" class="cl" size="15"></td>
		<td>هزینه :</td>
		<td><input type="text" name="name9" id="id9" class="cl" size="15"></td>
		</tr>
		

		<tr><td>ساعت ورود :</td>
		<td><input type="text" name="name10" id="id10" class="cl"
		 size="15" value="<?php echo(date("H:i:s"));?>" disabled=true ></td>
		<td>مبداء :</td>
		<td><?php echo $sour;?></td>
		<td>هزینه اضافی :</td>
		<td><input type="text" name="name12" id="id12" class="cl" size="15"></td>
		</tr>

		<tr><td>نام پدر :</td>
		<td><input type="text" name="name13" id="id13" class="cl" size="15"></td>
		<td>مقصد :</td>
		<td><?php echo $de;?></td>
		<td>نفر اضافی :</td>
		<td><input type="text" name="name15" id="id15" class="cl" size="15"></td>
		</tr>
		
		<tr><td>شماره شناسنامه :</td>
		<td><input type="text" name="name16" id="id16" class="cl" size="15"></td>
		<td>کد ملی :</td>
		<td><input type="text" name="name17" id="id17" class="cl" size="15"></td>
		
		</tr>
		
		<tr><td>تاریخ تولد :</td>
		<td><input type="text" name="name18" id="id18" class="cl" size="15" onblur="f1(this);"></td>
		<td>نسبت :</td>
		<td><?php echo $rel;?></td>
		</tr>
				
		<tr><td>جنسیت :</td>
		<td><?php echo $jensiat;?></td>
		<td>تاریخ ازدواج :</td>
		<td><input type="text" name="name21" id="id21" class="cl" size="15" onblur="f1(this);"></td>
		</tr>

		<tr><td>ملیت :</td>
		<td><?php echo $meli;?></td>
		<td>موبایل :</td>
		<td><input type="text" name="name23" id="id23" class="cl" size="15"></td>
		</tr>
		<td><input type="hidden" name="name24" id="id24" class="cl" value="<?php echo $room_id;?>"></td>
		<td><input type="hidden" name="name25" id="id25" class="cl" value="<?php echo $reserve_id;?>"></td>
		<tr><td colspan='6' align='center'><button onclick="vali();">ذخیره اطلاعات</td></tr>
		
		
	</table>
	</center>
	</button>
	</body>

</html>
