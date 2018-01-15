<?php
//	include_once("../kernel.php");
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);	
	function loadDaftar($daftar_selected_id)
        {
                $daftar_id=((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
                $out='<select name="daftar_id" id="daftar_id" class="inp" ><option value="-1" >همه</option>';
		//if ($sdaftar_id==-2)
                mysql_class::ex_sql("select name,id from daftar order by name",$q);
		//if ($sdaftar_id!=-2)
			//mysql_class::ex_sql("select name,id from daftar where `id` ='$daftar_id' order by name",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
			$out .="<option ".(($daftar_selected_id==(int)$r["id"])?'selected="selected"':'')." value='".(int)$r["id"]."'>".$r["name"]."</option>\n";
                return $out;
        }
        function loadAjans()
        {
                $sdaftar_id=((isset($_REQUEST['sdaftar_id']))?(int)$_REQUEST['sdaftar_id']:-1);
                $outAjans=null;
		if ($sdaftar_id==-2)
                {
                mysql_class::ex_sql("select name,id from ajans order by name",$q);
		}
		if ($sdaftar_id!=-2)
                {
                mysql_class::ex_sql("select name,id from ajans where `daftar_id`='$sdaftar_id' order by name",$q);}
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $outAjans[$r["name"]]=(int)$r["id"];
                }
                return $outAjans;
        }
	function loadHotel()
        {
                $outHotel=null;
                mysql_class::ex_sql("select name,id from hotel order by name",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $outHotel[$r["name"]]=(int)$r["id"];
                }
                return $outHotel;
        }
	function getPic($datay,$us_id)
	{
		$i = 1;
		$datay1 =array();
		if(count($datay)==0)
			$datay = array(0);
		for($j = 0;$j<count($datay);$j++)
			$datay1[] = $datay[$j]['nafarshab'];
		$datay = $datay1;
		//$datax = array();
		//foreach($datay1 as $tarikh=>$value)
			//$datax[] = $i;
		$graph = new Graph(750,300,'auto');
	    	$graph->img->SetMargin(40,40,40,40);
		$graph->img->SetAntiAliasing();
		$graph->SetScale("textlin",0,max($datay));
	    	$graph->SetShadow();
	    	$graph->title->Set(" ");
	    	$p1 = new BarPlot($datay);
	    	$abplot = new AccBarPlot(array($p1));
		$abplot->SetShadow();
		$abplot->value->Show();
	    	$p1->SetColor("blue");
	    	$p1->SetCenter();
		$graph->SetMargin(40,10,40,80);
		$graph->xaxis->SetTickSide(SIDE_BOTTOM);
		//$graph->xaxis->SetTickLabels($datax);
		$graph->xaxis->SetLabelAngle(90);
	    	$graph->Add($abplot);
	    	$addr = "chart/$us_id.png";
	    	$graph->Stroke($addr);
		return $addr;
	}
	function hamed_pdateBack($inp)
        {
                $out = FALSE;
		$inp = audit_class::perToEn($inp);
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
	function changedate($tmpdate)
	{
			$tmp = explode("/",$tmpdate);
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
                        $date="$y/$m/$d";
			$out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($date));
			}
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	$out=0;
	$pic = '';
	$addr = '';
	$user_id = (int)$_SESSION['user_id'];
	$daftar_id=((isset($_REQUEST['daftar_id']))?(int)$_REQUEST['daftar_id']:-1);
	$aztarikh=((isset($_REQUEST['aztarikh']) && trim($_REQUEST['aztarikh'])!='')?hamed_pdateBack($_REQUEST['aztarikh']):date("Y-m-d"));
	$tatarikh=((isset($_REQUEST['tatarikh']) && trim($_REQUEST['tatarikh'])!='')?hamed_pdateBack($_REQUEST['tatarikh']):date("Y-m-d"));
        $out = ajans_class::getScore($aztarikh,$tatarikh,$daftar_id,FALSE);
	$data = $out['data'];
	$top10 = $out['top10'];
	//var_dump($top10);
	if(count($top10)>0)
		$addr = getPic($top10,$user_id);
	if($addr!='')
		$pic ="<img src='$addr' width='700px' style='cursor:pointer;'>";
	$out_data = "<table width='60%' border='1' >";
	$out_data .= "
<tr>
	<th>
		ردیف
	</th>
	<th>
		دفتر
	</th>
	<th>
	آژانس
	</th>
	<th>
	نفرشب
	</th>
";
	
	for($i=0;$i<count($data);$i++)
	{
		$daft = new daftar_class($data[$i]['daftar_id']);
		$out_data .="<tr><td align='center' >".($i+1)."</td><td align='center' >".$daft->name."</td><td align='center' >".$data[$i]['ajans_name']."</td><td align='center' >".$data[$i]['nafarshab']."</td></tr>";
	}
	$out_data .= "</table>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->

		<script type="text/javascript" src="../js/tavanir.js"></script>
		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
گزارش میزان رزرو آژانس های مختلف
		</title>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#aztarikh").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#tatarikh").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
	    	</script>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<form id="frm1" method="POST" >
				<table width="60%" border="1" >
					<tr>
						<th>نام دفتر</th>
						<th>ازتاریخ</th>
						<th>تا تاریخ</th>
						<th>جستجو</th>
					</tr>
					<tr>
						<td><?php echo loadDaftar($daftar_id); ?></td>
						<td><input type="input" class="inp" name="aztarikh" id="aztarikh" value="<?php echo hamed_pdate($aztarikh); ?>" ></td>
						<td><input type="input" class="inp" name="tatarikh" id="tatarikh" value="<?php echo hamed_pdate($tatarikh); ?>" ></td>
						<td><input type="submit" class="inp" value="جستجو" ></td>
					</tr>
				</table>
			</form>
			<?php echo $pic."<br/>".$out_data;  ?>
		</div>
	</body>
</html>
