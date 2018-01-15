<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadHotel()
        {
                $out=null;
                mysql_class::ex_sql("select `name`,`id` from `hotel` order by `name`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out[$r["name"]]=(int)$r["id"];
                return $out;
        }
	function add_item($id)
	{
		$fields = null;
                foreach($_REQUEST as $key => $value)
                        if(substr($key,0,4)=="new_")
                                if($key != "new_id" && $key != "new_moeen_id" &&  $key != "new_moeen_cash_id" )
                                        $fields[substr($key,4)] =perToEnNums($value);
		$hotel = new hotel_class((int)$fields['hotel_id']);
		$kol = new moeen_class($hotel->moeen_id);
		$moeen_id = moeen_class::addById($kol->kol_id,'درآمد صندوق '.$fields['name']);
		$moeen_cash_id = moeen_class::addById($kol->kol_id,'درآمد متفرقه '.$fields['name']);
		$fields['moeen_id'] = $moeen_id ;
		$fields['moeen_cash_id'] = $moeen_cash_id;
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
        	$query.="insert into `sandogh` $fi values $valu";
		mysql_class::ex_sqlx($query);
	}
	function delete_item($id)
	{
		$id = (int)$id;
		mysql_class::ex_sqlx("delete from `sandogh` where `id` = $id");
		mysql_class::ex_sqlx("delete from `sandogh_user` where `sandogh_id` = $id");
	}
	function loadType()
	{
		$out['دریافت نقدی داشته باشد'] = 1 ;
		$out['دریافت نقدی نکند'] = -1 ;
		return $out;
	}
	function loadIcon($id)
        {
		$ls = new listBox_class;
		$ls->input = $id;
		$ls->onClick = 'f1';
		$ls->vertical  = FALSE;
		$ls->height = '55px';
		$ls->imageHeight = '30px';
		$ls->imageWidth = '';
		$ls->width = '100px';
		$san = new sandogh_class($id);
		$img = $san->icon;
		$img = explode('/',$img);
		$img = $img[count($img)-1];
		$ls->selected = $img;
		$out = $ls->getOutput();
		
                return '<center>'.$out.'</center>';
        }
	function loadKhadamat($inp)
	{
		$out='<div class="msg" ><a href="sandogh_khadamat.php?sandogh_id='.$inp.'" target="_blank" >خدمات</a></div>';
		return($out);
	}
	if(isset($_REQUEST['mod']) && $_REQUEST['mod']='updateImg' )
	{
		$id= (int)$_REQUEST['id'];
		$img = $_REQUEST['img'];
		mysql_class::ex_sqlx("update `sandogh` set `icon`='../icon/$img' where `id`=$id");
	}
	$grid = new jshowGrid_new("sandogh","grid1");
	$grid->index_width = '30px';
	$grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = "نام";
	$grid->columnHeaders[2] = "هتل";
	$grid->columnLists[2]=loadHotel();
       	$grid->columnHeaders[3] =null;
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[5] = "نوع";
	$grid->columnLists[5]=loadType();
	$grid->columnHeaders[6] = null;
	$grid->addFeild('id');
	$grid->columnHeaders[7] = 'آیکون';
	$grid->columnFunctions[7] = 'loadIcon'; 
	$grid->addFeild('id');
	$grid->columnHeaders[8] = 'خدمات';
	$grid->columnFunctions[8] = 'loadKhadamat'; 
	
	$grid->addFunction = 'add_item';
	$grid->deleteFunction = 'delete_item';
	$grid->canDelete = FALSE;
	$grid->sortEnabled = TRUE;
        $grid->intial();
   	$grid->executeQuery();
        $out = $grid->getGrid();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
تعریف موارد فرانت آفیس
		</title>
		<script language="javascript" >
			function f1(img,input)
			{
				if(confirm('آیا تغییر آیکون انجام شود؟'))
				{
					var form = document.createElement("form");
					form.setAttribute("method", "POST");
					form.setAttribute("action", "sandogh.php");         
					form.setAttribute("target", "_self");
					var hiddenField1 = document.createElement("input");              
					hiddenField1.setAttribute("name", "id");
					hiddenField1.setAttribute("value", input);
					var hiddenField2 = document.createElement("input");              
					hiddenField2.setAttribute("name", "img");
					hiddenField2.setAttribute("value", img);
					var hiddenField3 = document.createElement("input");              
					hiddenField3.setAttribute("name", "mod");
					hiddenField3.setAttribute("value", "updateImg");
					form.appendChild(hiddenField1);
					form.appendChild(hiddenField2);
					form.appendChild(hiddenField3);
					document.body.appendChild(form);         
					form.submit();
					document.body.removeChild(form);
				}
			}
		</script>
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
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>

</html>
