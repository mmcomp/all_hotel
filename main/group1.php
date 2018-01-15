<?php	session_start();
	include_once("../kernel.php");
        if (isset($_SESSION['user_id']) && isset($_SESSION['typ']))
        {
		$u = new user_class((int)$_SESSION['user_id']);
                if (!audit_class::isAdmin($_SESSION['typ']) || $u->user != 'mehrdad')
                {
                        die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
                }
        }
        else
        {
                        die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
        }
	function loadGrooh($inp=-1)
        {
                $out=null;
		$inp = (int)$inp;
                mysql_class::ex_sql("select `name`,`id` from `grop` order by `name`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
			if((int)$r["id"] != $inp)
	                        $out[$r["name"]]=(int)$r["id"];
                return $out;
        }
	function delete_item($id)
	{
		$id = (int)$id;
		mysql_class::ex_sqlx("update `grop` set `en` = 0 where `id` = $id");
	}
	function loadGorooh($inp)
	{
		$out = '';
		foreach($inp as $text => $value)
			$out .= "<option value=\"$value\">\n$text\n</option>\n";
		return($out);
	}
	function accessCopy($inp)
	{
		$inp = (int)$inp;
		$out = "<select id=\"from_grp_$inp\" class=\"inp\" >\n".loadGorooh(loadGrooh($inp))."\n</select>\n";
		$out .= "<input type=\"button\" class=\"inp\" value=\"ارث‌بری\" onclick=\"window.open('changeAccess.php?to_grp=$inp&from_grp='+document.getElementById('from_grp_$inp').value+'&r='+Math.random()+'&');\" />";
		return($out);
	}

        function add_item()
        {
                $fields = null;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id" && $key != "new_en")
                                {
                                        $fields[substr($key,4)] =$value;
                                }
                        }
                }
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
                $query="insert into `grop` $fi values $valu";
		echo $query;
                mysql_class::ex_sqlx($query);
        }


	$combo["بستانکار"]=1;
	$combo["بدهکار"]=-1;
	$grid = new jshowGrid_new("grop","grid1");
	$grid->whereClause = ' `en`=1 order by `name`';
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = "نام";
	$grid->columnHeaders[2] = null;
	$grid->addFeild('id');
	$grid->columnHeaders[3] = 'ارث بری دسترسی';
	$grid->columnFunctions[3] = 'accessCopy';
	$grid->columnAccesses[3] = 0;
	$grid->deleteFunction = 'delete_item';
	$grid->addFunction = 'add_item';
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
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>
</html>
