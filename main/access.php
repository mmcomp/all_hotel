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
	function loadGrooh()
        {
                $out=null;
                mysql_class::ex_sql("select name,id from grooh order by id",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                        $out[$r["name"]]=(int)$r["id"];
                }
                return $out;
        }
	function loadGroups()
	{
		$out = null;
		mysql_class::ex_sql('select `name`,`id` from `grop` where `en`=1 order by `name`',$q);
		while($r = mysql_fetch_array($q))
			$out[$r['name']] = (int)$r['id'];
		$q = null;
		mysql_class::ex_sql('select `fname`,`lname`,`id` from `user` order by `lname`,`fname`',$q);
                while($r = mysql_fetch_array($q))
                        $out[$r['fname'].' '.$r['lname']] = (int)$r['id'];
		return($out);
	}
	function loadPages()
	{
		$out = null;
		if ($handle = opendir('.')) 
		{
			while (false !== ($entry = readdir($handle))) 
			        $out[$entry] = $entry;
			closedir($handle);
		}
		return($out);
	}
	function loadDet($id)
	{
		$fr = access_det_class::loadByAcc($id);
		$fr = implode(' , ',$fr);	
		$out = "$fr<br/><u><span style=\"cursor:pointer;color:blue;\" onclick=\"wopen('access_det.php?acc_id=$id&','',500,400);\">ادامه</span></u>";
		return($out);
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
                $query="insert into `access` $fi values $valu";
                mysql_class::ex_sqlx($query);
		echo $query;

        }
	$yesNo['بله'] = 1;
	$yesNo['خیر'] = 0;
	$pages = loadPages();
	$grid = new jshowGrid_new("access","grid1");
	$grid->divProperty = '';
	$grid->index_width = '20px';
	$grid->enableComboAjax = TRUE;
	$grid->sortEnabled = TRUE;
	$grid->columnHeaders[0] = 'تعریف جزئیات';
	$grid->columnFunctions[0] = 'loadDet';
	$grid->columnAccesses[0] = 0;
	$grid->columnHeaders[1] = 'گروه';
	$grid->columnLists[1] = loadGroups();
	$grid->columnFilters[1] = TRUE;
	$grid->columnHeaders[2] = 'نام صفحه';
	$grid->columnLists[2] = $pages;
	$grid->columnFilters[2] = TRUE;
	$grid->columnHeaders[3] = 'گروه است؟';
	$grid->columnLists[3] = $yesNo;
	$grid->addFunction = 'add_item';
	$grid->pageCount = 30;
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
                <script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>
</html>
