<?php	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
        function loadUser()
        {
                $out=null;
                mysql_class::ex_sql("select `fname`,`lname`,`id`,`daftar_id` from `user` where `user`<>'mehrdad' order by `fname`,`lname`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $daftar = new daftar_class($r["daftar_id"]);
                        $out[$r["fname"].' '.$r['lname'].'('.$daftar->name.')']=(int)$r["id"];
                }
                return $out;
        }
        function loadSandogh()
        {
                $out=null;
                $hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
                if($hotel_id>0)
                {
                        $san = hotel_class::getSondogh($hotel_id,FALSE);
                        $out = $san;
                }
                return $out;
        }
        function loadHotels($hotel_id)
        {
                $out = '<select name="hotel_id" id="hotel_id" class="inp" onchange="filter_frm();" ><option value="-1" ></option>'."\n";
                $hot = hotel_class::getHotels();
                for($i=0;$i<count($hot);$i++)
                        $out .="<option ".(($hotel_id==$hot[$i]['id'])?'selected="selected"':'')." value='".$hot[$i]['id']."' >".$hot[$i]['name']."</option>\n";
                $out .='</select>';
                return $out;
        }

        $hotel_id = (isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1;
        $grid = new jshowGrid_new("sandogh_item","grid1");
        $grid->setERequest(array('hotel_id'=>$hotel_id));
        $wer = '1=0';
        if($hotel_id>0)
        {
                $tmp = implode(',',hotel_class::getSondogh($hotel_id));
                if($tmp!='')
                        $wer = " `sandogh_id` in ($tmp)";
        }
	$combo['مبلغ غیرقابل تغییر است'] = 1;
	$combo['مبلغ قابل تغییر است'] = -1;
	$sandogh = loadSandogh();
        $grid->whereClause = $wer;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = "نام";
	$grid->columnFilters[1] = TRUE;
	$grid->columnHeaders[2] = "صندوق";
	$grid->columnFilters[2] = TRUE;
        $grid->columnHeaders[3] = "مبلغ";
        $grid->columnHeaders[4] = "نوع";
        $grid->columnLists[2]=$sandogh;
	$grid->columnLists[4]=$combo;
	$grid->canAdd = ($hotel_id>0 && $sandogh!=null);
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
                <script type="text/javascript" >
                        function filter_frm()
                        {
                                document.getElementById('frm1').submit();
                        }
                </script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه
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
			<a href="help.php" target="_blank"><img src="../img/help.png"/></a>
		</div>
		<div align="center">
                        <form id="frm1" >
                                        نام هتل:
                                <?php echo loadHotels($hotel_id); ?>
                        </form>
			<br/>
			<?php echo $out; ?>
		</div>
	</body>
</html>
