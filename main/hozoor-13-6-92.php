<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function loadPic($inp)
	{
		$out = "<img style='cursor:pointer;' src='$inp' width='50px' onclick=\"wopen('$inp','',320,210)\" >";
		return $out;
	}
	function loadTyp($inp)
	{
		if($inp==1)
			$out = 'ورود';
		else if($inp==0)
			$out = 'خروج';
		return $out;
	}
	function loadHour($inp)
	{
		return audit_class::enToPer(date("H:i",strtotime($inp)));
	}
	function loadMovazaf($inp)
	{
		$out = '';
		mysql_class::ex_sql("select `vorood`,`khorooj` from `user` where `id`=$inp",$q);
//echo "select `vorood`,`khorooj` from `user` where `id`=$inp";
		if($r = mysql_fetch_array($q))
			$out = 'ساعت موظف ورود:'.audit_class::enToPer(date("H:i",strtotime($r['vorood']))).'<br/>'.'ساعت موظف خروج:'.audit_class::enToPer(date("H:i",strtotime($r['khorooj'])));
		return $out;
	}
	function loadpersonal($user_id)
	{
		$out = new user_class($user_id);
		$out = $out->fname.' '.$out->lname.'('.$user_id.')';
		return($out);
	}
	function loadExitHour($id)
	{
		$out = '';
		mysql_class::ex_sql("select `dat`,`user_id` from `vorood` where `id`=$id",$q);
		if($r = mysql_fetch_array($q))
		{
			$user_id = (int)$r['user_id'];
			$dat = date("Y-m-d H:i:s",strtotime($r['dat']));
			//$dat1 = date("Y-m-d 23:59:59",strtotime($r['dat']));
			$q = null;
			mysql_class::ex_sql("select `dat` from `vorood` where `typ`=0 and `user_id` = $user_id and `dat` > '$dat' and `id`>$id",$q);
			if($r = mysql_fetch_array($q))
			{
				$tmpdat = $r['dat'];
				$q = null;
				mysql_class::ex_sql("select `dat` from `vorood` where `typ`=1 and `user_id` = $user_id and `dat` < '$tmpdat' and `dat` > '$dat' and `id`>$id",$q);
				if($rr = mysql_fetch_array($q))
				{
					//No EXIT
				}
				else
				{
					$out .= audit_class::enToPer(date("H:i",strtotime($r['dat'])));
					if(date("Y-m-d",strtotime($r['dat']))>date("Y-m-d",strtotime($dat)))
						$out .= ' '.audit_class::hamed_pdate($r['dat']).' ';
				}
			}
		}
		return($out);
	}
	function loadExitPic($id)
	{
                $out = '';
                mysql_class::ex_sql("select `dat`,`user_id` from `vorood` where `id`=$id",$q);
                if($r = mysql_fetch_array($q))
                {
                        $user_id = (int)$r['user_id'];
                        $dat = date("Y-m-d H:i:s",strtotime($r['dat']));
                        //$dat1 = date("Y-m-d 23:59:59",strtotime($r['dat']));
                        $q = null;
                        mysql_class::ex_sql("select `img` from `vorood` where `typ`=0 and `user_id` = $user_id and `dat` > '$dat' and `id`>$id",$q);
                        if($r = mysql_fetch_array($q))
                        {
                                $tmpdat = $r['dat'];
                                $q = null;
                                mysql_class::ex_sql("select `dat` from `vorood` where `typ`=1 and `user_id` = $user_id and `dat` < '$tmpdat' and `dat` > '$dat' and `id`>$id",$q);
                                if($rr = mysql_fetch_array($q))
                                {
                                        //No EXIT
                                }
                                else
                                {
					//<img style='cursor:pointer;' src='khorooj_img/65_2012-03-10_22-37-02.png' width='50px' onclick="wopen('khorooj_img/65_2012-03-10_22-37-02.png','',320,210)" >
                                	$out = "<img style='cursor:pointer;' src=\"".$r['dat']."\" width='50px' onclick=\"wopen('".$r['dat']."','',320,210)\"/>";
                                }
                        }
                }
                return($out);
	}
	function loadTakhir($id)
	{
		$out = '';
		$ex_hour = audit_class::perToEn(loadExitHour($id));
		$ex_hour = explode(':',$ex_hour);
		$user_id = -1;
		$takhir = 0;
		$tajil = 0;
		mysql_class::ex_sql("select `typ`,`dat`,`user_id` from `vorood` where  `typ`=1 and `id`=$id",$q);
                if($r = mysql_fetch_array($q))
                {
			$enter_hour = explode(':',date("H:i",strtotime($r['dat'])));
			$user_id = (int)$r['user_id'];
			$user = new user_class($user_id);
			$vor = explode(':',$user->vorood);
			$khor = explode(':',$user->khorooj);
			$takhir = (((int)$enter_hour[0]*60+(int)$enter_hour[1]) - ((int)$vor[0]*60+(int)$vor[1])>0)?((int)$enter_hour[0]*60+(int)$enter_hour[1]) - ((int)$vor[0]*60+(int)$vor[1]):0;
			if(count($ex_hour) >= 2)
				$tajil = (((int)$khor[0]*60+(int)$khor[1]) - ((int)$ex_hour[0]*60+(int)$ex_hour[1])>0)?((int)$khor[0]*60+(int)$khor[1]) - ((int)$ex_hour[0]*60+(int)$ex_hour[1]):0;
			if($takhir+$tajil>0)
			{
				$GLOBALS['kol'] += $takhir+$tajil;
				$out = (($takhir>0)?'تأخیر '.audit_class::enToPer(minToHour($takhir)):'').(($takhir>0 && $tajil>0)?' و ':'').(($tajil>0)?'تعجیل '.audit_class::enToPer(minToHour($tajil)):'').((count($ex_hour)<2)?' خروج نامعلوم ':'').' کل '.audit_class::enToPer(minToHour($takhir+$tajil));
			}
			
		}
		return($out);
	}
	function minToHour($inp)
	{
		$inp = (int)$inp;
		$m = $inp % 60;
		$h = ($inp - $m) / 60;
		return("$h:$m");
	}
        function loadEnter($id)
        {
                $out = 'غایب';
                mysql_class::ex_sql("select `typ` from `vorood` where `id`=$id",$q);
                if($r = mysql_fetch_array($q))
			if((int)$r['typ']==0)
                		$out = 'خروج';
			else if((int)$r['typ']==1)
				$out = 'ورود';
			else if((int)$r['typ']==-1)
				$out ='غایب';
                return($out);
        }
	$msg = '';
	$p_id = ((isset($_REQUEST['p_id']) && $_REQUEST['p_id']!='')?$_REQUEST['p_id']:-1);
	if($p_id > 0)
		vorood_class::sabtGheybat($p_id,date('Y-m-d H:i:s'));
	else
	{
		mysql_class::ex_sql("select `id` from `user` order by `id",$qq);
		while($r = mysql_fetch_array($qq))
			vorood_class::sabtGheybat($r['id'],date('Y-m-d H:i:s'));
	}
	$GLOBALS['kol'] = 0;
	$p_user = new user_class($p_id);
	$daftar = new daftar_class($p_user->daftar_id);
	$daftar = $daftar->name;
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):date('Y-m-d H:i:s'));
	$aztarikh = date("Y-m-d 00:00:00",strtotime($aztarikh));
	$tatarikh = date("Y-m-d 23:59:59",strtotime($tatarikh));
	$user_id=(int)$_SESSION['user_id'];
	$out1 = null;
	mysql_class::ex_sql("select * from `vorood` where `dat`>='$aztarikh' and `dat`<='$tatarikh' ".(($p_id>0)?" and `user_id`=$p_id":"")." order by date(`dat`),`user_id` ",$q);
	while($r= mysql_fetch_array($q))
	{
	        $tmp = array();
                for($i = 0;$i <  mysql_num_fields($q);$i++)
                {
         	       $fi = mysql_fetch_field($q,$i);
                	$tmp[$fi->name] = $r[$fi->name];
                }
		if($p_id > 0 && count($out1)>0)
		{
			$tmp0 = $out1[count($out1)-1];
			$dat0 = date("Y-m-d",strtotime($tmp0['dat']));
			$dat = date("Y-m-d",strtotime($tmp['dat']));
			$dat1 = date("Y-m-d",strtotime($dat0.' + 1 day'));
			if($dat0 == $dat || $dat1 == $dat)
				$out1[] = $tmp;
			else
			{
				$time_index = strtotime($dat0.' + 1 day');
				$tmp0['typ'] = -1;
				$tmp0['id'] = -1;
				$tmp0['img'] = '';
				while($time_index < strtotime($dat))
				{
					$tmp0['dat'] = date("Y-m-d 00:00:00",$time_index);
					$time_index = strtotime(date("Y-m-d",$time_index).' + 1 day');
					$out1[] = $tmp0;
				}
				$out1[] = $tmp;
			}		
		}
		else if($p_id <= 0 && count($out1)>0 )
		{
			$az_tmp = date("Y-m-d",strtotime($aztarikh));
			$ta_tmp = date("Y-m-d",strtotime($tatarikh));
			if($az_tmp == $ta_tmp)
			{
				$tmp0 = $out1[count($out1)-1];
				$user_id0 = $tmp0['user_id'];
				$user_id = $tmp['user_id'];
				$be_users = user_class::loadBetweenUsers($user_id0,$user_id);
				$tmp0['typ'] = -1;
                        	$tmp0['id'] = -1;
				$tmp0['img'] = '';
				for($ind = 0;$ind < count($be_users);$ind++)
				{
					$tmp0['user_id'] = $be_users[$ind];
					$out1[] = $tmp0;
				}
			}
			$out1[] = $tmp;
		}
		else
	                $out1[] = $tmp;
		
	}
	if(is_array($out1))
		$grid1 = new jshowGrid_new('','grid1',$out1);
	else
		$grid1 = new jshowGrid_new('vorood','grid1');
	$grid1->index_width = '20px';
	$grid1->width = '100%';
	$grid1->pageCount = 3000;
	$grid1->whereClause = "`dat`>='$aztarikh' and `dat`<='$tatarikh' ".(($p_id>0)?" and `user_id`=$p_id":"")." order by date(`dat`),`user_id` ";
	$grid1->columnHeaders[0] = null;
	$grid1->columnHeaders[1]=(($p_id>0)?null:'نام و نام خانوادگی(شماره پرسنلی)');
	$grid1->columnFunctions[1] = 'loadpersonal';
	$grid1->columnHeaders[2]="تاریخ";
	$grid1->columnFunctions[2] = 'hamed_pdate';
	$grid1->fieldList[3] = 'dat';
	$grid1->columnHeaders[3] = 'ساعت ورود/خروج';
	$grid1->columnFunctions[3] = 'loadHour';	
	$grid1->columnHeaders[4] = "نمایه";
	$grid1->columnFunctions[4] = 'loadPic';
	$grid1->columnHeaders[5] = 'توضیحات';
	$grid1->addFeild('id');
	$grid1->columnHeaders[6] = 'وضعیت تأخیر';
	$grid1->columnFunctions[6] = 'loadTakhir';
        $grid1->addFeild('id');
        $grid1->columnHeaders[7] = '&#1608;&#1585;&#1608;&#1583;/&#1582;&#1585;&#1608;&#1580;';
        $grid1->columnFunctions[7] = 'loadEnter';
	$grid1->addFeild('user_id',3);
	$grid1->columnHeaders[3] = 'ساعت موظف';
	$grid1->columnFunctions[3] = 'loadMovazaf';
	$grid1->canAdd = FALSE;
	for($i = 0;$i < count($grid1->columnHeaders);$i++)
		$grid1->columnAccesses[$i] = 0;
	$grid1->columnAccesses[5] = 1;
	$grid1->canDelete = FALSE;
	$grid1->intial();
	$grid1->executeQuery();
	$out1 = $grid1->getGrid();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript">
		function send_search()
		{
			document.getElementById('mod').value= 2;
			document.getElementById('frm1').submit();
		}
		function set_value(inp)
		{
			document.getElementById('mablagh').value = document.getElementById(inp).innerHTML;
		}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#datepicker6").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#datepicker7").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
	    	</script>
		<style type="text/css" >
			td{text-align:center;}
		</style>
		<title>
			گزارش حضور و غیاب	
		</title>
		<script>
		function st()
		{
		week= new Array("يكشنبه","دوشنبه","سه شنبه","چهارشنبه","پنج شنبه","جمعه","شنبه")
		months = new Array("فروردين","ارديبهشت","خرداد","تير","مرداد","شهريور","مهر","آبان","آذر","دي","بهمن","اسفند");
		a = new Date();
		d= a.getDay();
		day= a.getDate();
		var h=a.getHours();
      		var m=a.getMinutes();
  		var s=a.getSeconds();
		month = a.getMonth()+1;
		year= a.getYear();
		year = (year== 0)?2000:year;
		(year<1000)? (year += 1900):true;
		year -= ( (month < 3) || ((month == 3) && (day < 21)) )? 622:621;
		switch (month) 
		{
			case 1: (day<21)? (month=10, day+=10):(month=11, day-=20); break;
			case 2: (day<20)? (month=11, day+=11):(month=12, day-=19); break;
			case 3: (day<21)? (month=12, day+=9):(month=1, day-=20); break;
			case 4: (day<21)? (month=1, day+=11):(month=2, day-=20); break;
			case 5:
			case 6: (day<22)? (month-=3, day+=10):(month-=2, day-=21); break;
			case 7:
			case 8:
			case 9: (day<23)? (month-=3, day+=9):(month-=2, day-=22); break;
			case 10:(day<23)? (month=7, day+=8):(month=8, day-=22); break;
			case 11:
			case 12:(day<22)? (month-=3, day+=9):(month-=2, day-=21); break;
			default: break;
		}
		//document.write(" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s);
			var total=" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s;
			    document.getElementById("tim").innerHTML=total;
   			    setTimeout('st()',500);
		}
		</script>
	</head>
	<body onload='st()'>
		<center>
		<span id='tim' >test2
		</span>
		</center>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="right" style="padding-right:30px;padding-top:10px;">
			<?php if (!isset($_REQUEST['p_id']))
				{
			 ?>
					<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
			<?php
				}
			?>
		</div>
		<div align="center">
			<br/>
			<br/>
			<table border='1' style='font-size:12px;width:95%;' >
				<tr>
					<th>شماره پرسنلی</th>
					<th>از تاریخ</th>
					<th>تا تاریخ</th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					<form id='frm1'  method='GET' >
					<td>	
						<input class='inp' style="width:60px;" name='p_id' id='p_id' value="<?php echo ((isset($_REQUEST['p_id']))?$_REQUEST['p_id']:''); ?>" >
					</td>
					<td>	
         					   <input value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td>
						<input value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
					</form>
				</tr>
				<tr>
					<td colspan="4" ><br/>
						<?php if($p_id>0) echo 'نام پرسنل : <b>'.$p_user->fname.' '.$p_user->lname.'</b> محل خدمت :<b>'.$daftar.'</b> جمع تأخیر : <b>'.audit_class::enToPer(minToHour($GLOBALS['kol'])); ?></b><br/>
						<?php echo $out1; ?>
					</td>
				</tr>
			</table>
			<?php echo $msg; ?>
		</div>
	</body>
</html>
