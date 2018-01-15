<?php
	include_once("../kernel.php");
	session_start();
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadDaftar()
	{
		$sdaftar_id=((isset($_REQUEST['sdaftar_id']))?(int)$_REQUEST['sdaftar_id']:-1);
		$outDaftar=null;
		mysql_class::ex_sql("select name,id from daftar where `id` ='$sdaftar_id' order by name",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$outDaftar[$r["name"]]=(int)$r["id"];
		}
		return $outDaftar;
	}
	function loadHotel()
        {
                $outDaftar=null;
                mysql_class::ex_sql("select name,id from hotel order by name",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $outDaftar[$r["name"]]=(int)$r["id"];
                }
                return $outDaftar;
        }

	function loadAjans()
	{
		$sdaftar_id=((isset($_REQUEST['sdaftar_id']))?(int)$_REQUEST['sdaftar_id']:-1);
		$outAjans=null;
		mysql_class::ex_sql("select name,id from ajans where `daftar_id`='$sdaftar_id' order by name",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$outAjans[$r["name"]]=(int)$r["id"];
		}
		return $outAjans;
	}
	function hamed_pdateBack($inp)
        {
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

                return $out." 12:00:00";
        }
        function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function add_item()
	{
		$fields = null;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id")
                                {
                                        $fields[substr($key,4)] = $value;
                                }
                        }
                }
		$fields["sanad_id"]=$_REQUEST["sanad"];
		$fields["aztarikh"]=hamed_pdateBack($fields["aztarikh"]);
		$fields["tatarikh"]=hamed_pdateBack($fields["tatarikh"]);
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
                $query="insert into `reserve` $fi values $valu";
                mysql_class::ex_sqlx($query);

	}
	function sanadtodaftar($sanad_id)
	{
		$out=-1;
		$sanad_id=(int)$sanad_id;
		mysql_class::ex_sql("select `daftar_id` from reserve where `sanad_id`='$sanad_id'",$q);
		if ($r=mysql_fetch_array($q))
		{
			$out=(int)$r["daftar_id"];
		}
		return $out;
	}
	if(!$_GET["sanad"])
	{
		die("<center><h2>شماره سند باید وارد شود</h2></center>");
	}
	$sanad_id = (int)$_GET["sanad"];
	$combo="<form id='sanad'> ";
	$combo.="<select class=\"inp\" name=\"sdaftar_id\" id=\"sdaftar_id\" onchange=\"document.getElementById('sanad').submit();\">";
	$sdaftar_id=((isset($_REQUEST['sdaftar_id']))?(int)$_REQUEST['sdaftar_id']:-1);
        mysql_class::ex_sql("select * from daftar order by name",$q);
	$combo.="<option class='inp' value='-1'></option>";
	while ($r = mysql_fetch_array($q,MYSQL_ASSOC))
        {
                if((int)$r["id"]===$sdaftar_id || (int) $r["id"]===sanadtodaftar($sanad_id))
                {
                        $select = "selected='selected'";
                }
                else
                {
                        $select = "";
                }
                $combo.="<option class='inp' $select  value='".$r["id"]."' >".$r["name"]."</option><br />\n";
        }
        $combo.="</select>";
	$combo.="<input type=\"submit\" class='inp'  value=\"نمایش \"/>";
	$combo .="<input type='hidden' value='$sanad_id' name='sanad'  >  ";
	$combo.="</form>";

	$grid = new jshowGrid_new("reserve","grid1");
	$grid->whereClause = "`daftar_id`=$sdaftar_id and sanad_id=$sanad_id";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="نام";
	$grid->columnHeaders[2]="نام خانوادگی";
	$grid->columnHeaders[3]="تعداد نفرات";
	$grid->columnHeaders[4]="مدت اقامت";
	$grid->columnHeaders[5]="هتل";
	$grid->columnHeaders[6]="مبلغ بلیط";
	$grid->columnHeaders[7]="مبلغ هتل";
	$grid->columnHeaders[8]="نام آژانس";
	$grid->columnHeaders[9]="نام دفتر";
	$grid->columnHeaders[10]="ازتاریخ";
	$grid->columnHeaders[11]="تاتاریخ";
	$grid->columnHeaders[12]="توضیحات";
	$grid->columnHeaders[13]=null;
	$grid->columnLists[5]=loadHotel();
	$grid->columnLists[9]=loadDaftar();
	$grid->columnLists[8]=loadAjans();
	$grid->columnFunctions[10]="hamed_pdate";
	$grid->columnCallBackFunctions[10]="hamed_pdateBack";
	$grid->columnFunctions[11]="hamed_pdate";
	$grid->columnCallBackFunctions[11]="hamed_pdateBack";
	$grid->addFunction="add_item";
	$grid->width="99%";
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
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
سامانه حسابداری دفاتر رزرو
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php echo $combo."<br>";?>
			<br/>
			<?php
				if ((isset($_REQUEST['sdaftar_id']))&&((int)$sdaftar_id!=-1 ))
				{
					 echo $out;  
				}
				//$hes = new hesab_class();
				//var_dump($hes->getOutput());
			?>
		</div>
		<script language="javascript" >
			if(document.getElementById("new_sanad_id"))
			{
				document.getElementById("new_sanad_id").value=<?php echo $sanad_id  ?>;
				document.getElementById("new_sanad_id").readOnly = true;
			}
		</script>
	</body>
</html>
