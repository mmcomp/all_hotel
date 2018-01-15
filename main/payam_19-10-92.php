<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);	
	if (isset($_REQUEST["group_id_new"]))
                $group_id_new = $_REQUEST["group_id_new"];
	else
		$group_id_new = -1;
	$_SESSION['grop'] = $group_id_new;
	function loadUser($group_id_new)
        {
                $out = array();
		if ($group_id_new!=-1)
		{
			mysql_class::ex_sql("select `id` , `fname`, `lname` from `user` where `typ`='$group_id_new' and `user`!='mehrdad'",$q);
			$out['همه']= -1;
			while($r = mysql_fetch_array($q))
			{
				$name = $r['fname'].' '.$r['lname'];
				$out[$name]=$r['id'];
			}
		}
		else
		{
			$out['همه']= -1;
			mysql_class::ex_sql("select `id` , `fname`, `lname` from `user` where `user`!='mehrdad'",$q);
			while($r = mysql_fetch_array($q))
			{
				$name = $r['fname'].' '.$r['lname'];
				$out[$name]=$r['id'];
			}
		}
		return($out);
        }
	function loadGropUser($inp)
        {
                $out = '';
		if ($inp==-1)
			$out = 'همه';
		else
		{
			mysql_class::ex_sql("select `id` , `fname`, `lname` from `user` where `id`='$inp'",$q);
			if($r = mysql_fetch_array($q))
				$out= $r['fname'].' '.$r['lname'];
		}
		return($out);
        }
	function loadUserPasMatn($inp)
        {
                $out = '';
		if ($inp==-1)
			$out = '---';
		else
			$out= $inp;
		return($out);
        }
	function loadUserPas($inp)
        {
                $out = '';
		if ($inp==-1)
			$out = '---';
		else
		{
			mysql_class::ex_sql("select `id` , `fname`, `lname` from `user` where `id`='$inp'",$q);
			if($r = mysql_fetch_array($q))
				$out= $r['fname'].' '.$r['lname'];
		}
		return($out);
        }
	function loadVaz($inp)
        {
                $out = '';
		if ($inp==-1)
			$out = 'پاسخ داده نشده';
		else
			$out = 'پاسخ داده شده';
		return($out);
        }
	function add_item($f)
        {

		$conf = new conf;
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
		$fields['user_id'] = $_SESSION['user_id'];
		$fields['toz' ] = $fields['toz'];
		$fields['rec_grop' ] = $_SESSION['grop'];
		$fields['rec_user'] = $fields['rec_user'];
		$fields['user_pasokh'] = -1;
		$fields['toz_pasokh'] = -1;
		$fields['en'] = -1;
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
		if (($fields['rec_user']!='')&&($fields['toz' ]!='توضیحات'))
		{
			$query="insert into `payam` $fi values $valu";
			mysql_class::ex_sqlx($query);
		}
		else
			echo '<br/><center><h3>'."لطفا تمامی اطلاعات درخواستی را با دقت و کامل وارد نمایید".'</h3></center>';
        }
	function edit_item($id,$feild,$value)
	{
		$conf = new conf;
		$user_id = $_SESSION['user_id'];
		$today = date("Y-m-d h:i:s");
		if($feild=='toz')
		{
			mysql_class::ex_sql("select `id` , `en` from `payam` where `id`='$id'",$q);
			if($r = mysql_fetch_array($q))
			{
				if ($r['en']!='1')
				{
					mysql_class::ex_sqlx("update `payam` set `toz`='$value' where `id`=$id");
				}	
				else
					echo "<script>alert('متن پیام پس از ثبت پاسخ قابل تغییر نمی باشد');</script>";
			}
		}
		
	}
	$combo_group = "";
	$combo_group .= "<form name=\"selGroup\" id=\"selGroup\" method=\"POST\">";
		$combo_group .= "گروه های کاربری : <select class='inp' id=\"group_id\" name=\"group_id_new\" onchange=\"document.getElementById('selGroup').submit();\" style=\"width:auto;\">\n<option value=\"-1\">\nهمه\n</option>\n";
		mysql_class::ex_sql("select * from `grop` where `en`>0 order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]== (int)$group_id_new)
		        {
		                $select = "selected='selected'";
		        }
		        else
		        {
		                $select = "";
		        }
		        $combo_group .= "<option value=\"".(int)$r["id"]."\" $select   >\n";
		        $combo_group .= $r["name"]."\n";
		        $combo_group .= "</option>\n";
		}
		$combo_group .= "</select>";
	$combo_group .= "</form>";
	if ($group_id_new==-1)
		$shart = '1=1';
	else
		$shart = " `rec_grop`=".$group_id_new;
	$grid = new jshowGrid_new("payam","grid1");
	$grid->whereClause= $shart.' order by `en`';
	$grid->setERequest(array('group_id_new'=>$group_id_new));
	$grid->columnHeaders[0]= null;
	$grid->columnHeaders[1]= 'کاربر ثبت کننده';
	$grid->columnFunctions[1] = 'loadUserPas';
	$grid->columnAccesses[1] = 0;
	$grid->columnHeaders[2]= 'متن پیام';
	$grid->columnHeaders[3]= 'گروه کاربری دریافت کننده پیام';
	$grid->columnFunctions[3] = 'loadGropUser';
	$grid->columnAccesses[3] = 0;
	$grid->columnHeaders[4]= 'کاربر دریافت کننده پیام';
	$grid->columnLists[4] = loadUser($group_id_new);
	$grid->columnAccesses[4] = 0;
	$grid->columnHeaders[5]= 'کاربر پاسخ دهنده';
	$grid->columnFunctions[5] = 'loadUserPas';
	$grid->columnAccesses[5] = 0;
	$grid->columnHeaders[6]= 'متن پاسخ';
	$grid->columnFunctions[6] = 'loadUserPasMatn';
	$grid->columnAccesses[6] = 0;
	$grid->columnHeaders[7]= 'وضعیت';
	$grid->columnFunctions[7] = 'loadVaz';
	$grid->columnAccesses[7] = 0;
	$grid->addFunction = 'add_item';
	$grid->editFunction = 'edit_item';
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
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
		<script>
			$(document).ready(function(){
				$("#new_user_id").hide();
				$("#new_rec_grop").hide();
				$("#new_user_pasokh").hide();
				$("#new_toz_pasokh").hide();
				$("#new_en").hide();
			});
		</script>
		<style>
			td{text-align:center;}
		</style>
		<title>
			پیام های ثبت شده	
		</title>
	</head>
	<body>
		<br/>
		<br/>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<?php echo $combo_group.'<br/>'.$out; ?>
		</div>
	</body>
</html>
