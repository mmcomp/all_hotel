<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$GLOBALS['msg'] = '';
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
                mysql_class::ex_sql("select * from hotel order by name",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out[$r["name"]]=(int)$r["id"];
                return $out;
        }
	function loadRoom()
        {
                $out = null;
                mysql_class::ex_sql("select * from room_typ order by zarfiat",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out[$r["name"]]=(int)$r["id"];
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
		if($fields['room_typ_id']!='' && $fields['name']!='' )
		{
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
			$query="insert into `room` $fi values $valu";
			mysql_class::ex_sqlx($query);
		}
		else
		{
			$GLOBALS['msg'] = 'نام اتاق یا نوع آن را وارد کنید';
		}
	}
	function delete_item($inp)
	{
		mysql_class::ex_sqlx("update `room` set `en`=0 where `id`=$inp");
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
	function loadPic($id)
	{
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('loadPic.php?room_id=$id&','',500,600);\">ادامه</span></u>";
		return($out);
	}
        $combo .="</select>";
	$combo .= "</form>";
        $grid = new jshowGrid_new("room","grid1");
	$rec["hotel_id"] = $hotel_id;
        $grid->setERequest($rec);
	$grid->whereClause=" `hotel_id`='$hotel_id' and `en`= 1 order by `name`,`room_typ_id`";
	$grid->showAddDefault = FALSE;
        $grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = "نوع اتاق";
	$grid->columnHeaders[3] = "نام";
	$grid->columnHeaders[4] = "توضیحات";
	//$grid->columnHeaders[5] = "قیمت";
	$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6] = null;
	//$grid->columnHeaders[7] = "حداکثر ظرفیت";
	$grid->columnHeaders[7] = null;
	$grid->columnHeaders[8] = null;
	$grid->columnHeaders[9] = null;
	$grid->columnHeaders[10] = 'شماره طبقه';
	$grid->columnHeaders[11] = null;
	$grid->addFeild('id');
	$grid->columnHeaders[12] = "تصویر اتاق";
	$grid->columnLists[1]=loadHotel();
	$grid->columnLists[2]=loadRoom();
	$grid->columnFunctions[12] = 'loadPic';
	$grid->columnAccesses[12] = 0;
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
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
			سامانه رزرواسیون هتل	
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<?php
				echo '<h2 style="color:red">'.$GLOBALS['msg'].'</h2>';
			?>
			<br/>
			<?php 
				echo $combo;
				echo "<br/>";
				echo $out;
			?>
		</div>
	</body>
</html>
