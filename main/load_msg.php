<?php
	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = $se->detailAuth('all');
	function loadUser()
	{
		$out=null;
		mysql_class::ex_sql("select `fname`,`lname`,`user`.`id`,`daftar`.`name` from `user` left join `daftar` on (`daftar`.`id`=`daftar_id`) order by `id`",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$out[$r["fname"].' '.$r["lname"].' ( '.$r["name"].' ) ']=(int)$r["id"];
		}
		return $out;
	}
	function loadStatus($inp)
	{
		$inp = (int)$inp;
		$out = (($inp==0)?'<span style="color:red;">مشاهده‌نشده</span>':'مشاهده‌شده');
		return $out;
	}
	function loadView($inp)
	{
		$inp = (int)$inp;
		$out = "<span style='cursor:pointer;color:green;' onclick=\"wopen('View_Msg.php?msg_id=$inp','','500','600');\" ><u>مشاهده</u></span>";
		return $out;
	}
	$wer = '1=1';
	$grid = new jshowGrid_new("msg","grid1");
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->columnHeaders[1]=null;
	if($isAdmin)
	{
		$grid->columnHeaders[1]='کاربر';
		$grid->columnFilters[1] = -1;
		$grid->columnLists[1] = loadUser();
		$grid->canDelete = TRUE;
	}
	else
	{
		$user_id = (int)$_SESSION['user_id'];
		$wer = ' user_id='.$user_id;
		
	}
	
	$grid->whereClause= $wer;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[2]="موضوع";
	$grid->columnHeaders[3] = null;
	$grid->columnHeaders[4] = "وضعیت";
	$grid->columnFunctions[4] = "loadStatus";
	$grid->addfeild('id');
	$grid->columnHeaders[5] = "مشاهده";
	$grid->columnFunctions[5] = "loadView";
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
مشاهده پیام ها
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php echo $out;  ?>
		</div>
	</body>
</html>
