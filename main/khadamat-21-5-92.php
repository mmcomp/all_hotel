<?php
/*	session_start();
	include_once("../kernel.php");
        if (isset($_SESSION['user_id']) && isset($_SESSION['typ']))
        {
                if (!audit_class::isAdmin($_SESSION['typ']))
                {
                        die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
                }
        }
        else
        {
                        die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
        }*/
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if (isset($_REQUEST["hotel_id"]))
        {
                $hotel_id=$_REQUEST["hotel_id"];
        }
        else
        {
                $hotel_id=-1;
        }
	function loadHotel()
        {
                $out=null;
                mysql_class::ex_sql("select * from `hotel` order by name",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $out[$r["name"]]=(int)$r["id"];
                }
                return $out;
        }
	function loadRoom()
        {
                $out = null;
                mysql_class::ex_sql("select * from room_typ order by zarfiat",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $out[$r["name"]]=(int)$r["id"];
                }
                return $out;
        }
	function loadKhadamat()
        {
                $out = null;
                mysql_class::ex_sql("select * from `khadamat` order by zarfiat",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $out[$r["name"]]=(int)$r["id"];
                }
                return $out;
        }
	function add_item()
	{
		$fields = null;

                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id" && $key != "new_en" )
                                {
                                        $fields[substr($key,4)] = $value;
                                }
                        }
                }
		if (isset($_REQUEST["hotel_id"]))
	        {
	                $hotel_id=$_REQUEST["hotel_id"];
	        }
	        else
	        {
        	        $hotel_id=-1;
	        }
		$fields["hotel_id"] = $hotel_id;
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
		$query="insert into `khadamat` $fi values $valu";
		mysql_class::ex_sqlx($query);
	}
	function delete_item($id)
	{
		mysql_class::ex_sqlx("update `khadamat` set `en` = 0 where `id` = $id");
	}
        $combo = "";
	$combo .= "<form name=\"selHotel\" id=\"selHotel\" method=\"POST\">";
	$combo .= "هتل : <select class='inp' id=\"hotel_id\" name=\"hotel_id\" onchange=\"document.getElementById('selHotel').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\n&nbsp\n</option>\n";
	mysql_class::ex_sql("select * from `hotel` order by `name`",$q);
        while($r = mysql_fetch_array($q))
        {
		if((int)$r["id"]== (int)$hotel_id)
                {
                        $select = "selected='selected'";
                }
                else
                {
                        $select = "";
                }
                $combo .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
                $combo .= $r["name"]."\n";
                $combo .= "</option>\n";
        }
	function loadtyp()
	{
		$out['دارد'] = 0;
		$out['ندارد'] = 1;
		return $out;
	}
	function loadLogicalTyp()
	{
		$out['ندارد'] = 0;
		$out['دارد'] = 1;
		return($out);
	}
	function loadVade()
	{
		$out['هردووعده‌اجباری'] = 0;
		$out['روز خروج اجباری'] = 1;
		$out['روز ورود اجباری'] = 2;
		return($out);
	}
	function loadtyp_ghaza()
	{
		$out['خیر'] = 0;
		$out['بله'] = 1;
		return($out);
	}
        $combo .="</select>";
	$combo .= "</form>";
        $grid = new jshowGrid_new("khadamat","grid1");
	$rec["hotel_id"] = $hotel_id;
        $grid->setERequest($rec);
	$grid->whereClause=" `hotel_id`='$hotel_id' and `en`='1' order by `name`";

        $grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = "خدمات";
	$grid->columnHeaders[3] = "قیمت پیش فرض";
	$grid->columnHeaders[4] = 'تعداددارد';
	$grid->columnLists[4] = loadtyp();
	$grid->columnLists[6] = loadLogicalTyp();
	$grid->columnLists[7] = loadLogicalTyp();
	$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6] = 'ورودی دارد';
        $grid->columnHeaders[7] = 'خروجی دارد';
        $grid->columnHeaders[8] = 'وعده اختیاری';
	$grid->columnLists[8] = loadVade();
	$grid->columnHeaders[9] = 'خدمات به عنوان غذا است؟';
	$grid->columnLists[9] = loadtyp_ghaza();
	$grid->addFunction = "add_item";
	$grid->deleteFunction = "delete_item";
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
			سامانه رزرواسیون هتل	
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php 
				echo $combo;
				echo "<br/>";
				echo $out;
			?>
		</div>
	</body>
</html>
