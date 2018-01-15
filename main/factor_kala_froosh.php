<?php	session_start();
	include_once("../kernel.php");
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadMoeen($inp)
        {
                $inp = (int)$inp;
                $aj = new anbar_factor_class($inp);
                if($aj->moeen_id>0)
                {
                        $moeen = new moeen_class($aj->moeen_id);
                        $nama = $moeen->name.'('.$moeen->code.')';
                }
                else
                {
                        $nama = 'انتخاب';
                }
                $out = "<u><span onclick=\"window.location =('select_hesab.php?refPage=factor_kala.php&sel_id=$inp');\"  style='color:blue;cursor:pointer;' >$nama</span></u>";
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
		if(isset($_REQUEST['anbar_typ_id']))
		{
			$fields["anbar_typ_id"] = (int)$_REQUEST['anbar_typ_id'];
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
		        $query="insert into `anbar_factor` $fi values $valu";
		        mysql_class::ex_sqlx($query);
		}
		else
			echo 'نوع ورودی یا خروجی به انبار انتخاب نشده است';
        }
	function hpdate($inp)
	{
		return(audit_class::hamed_pdate($inp));
	}
	function hpdateback($inp)
	{
		return(audit_class::hamed_pdateBack(perToEnNums($inp)));
	}
	function delete_item($id)
	{
		mysql_class::ex_sqlx("update `anbar` set `en`=0 where `id` = $id");
	}
	function loadDet($inp)
        {
		$anbar_fc = new anbar_factor_class($inp);
		if($anbar_fc->moeen_id>0)
		{
		        $jozeeat ="صورت فاکتور";
		        $out = "<u><span style=\"color:Blue;cursor:pointer;\" onclick=\"wopen('anbar_det.php?anbar_typ=1&anbar_factor_id=$inp&','',900,300);\">$jozeeat</span></u>";
		}
		else
			$out = '<span style="color:#999;">صورت فاکتور</span>';
                return $out;
        }

	if(isset($_REQUEST['sel_id']))
        {
		$sel_id = $_REQUEST['sel_id'];
		if (isset($_REQUEST['moeen_id']))
	        {
        	        $moeen_id = (int)$_REQUEST['moeen_id'];
	                mysql_class::ex_sqlx("update `anbar_factor` set `moeen_id`=$moeen_id where `id`=$sel_id");
	        }
        	else
	        {
        	        $moeen_id = -1;
	        }
//                $moeen_id = (int)$_REQUEST['moeen_id'];
//                $sel_id = $_REQUEST['sel_id'];
  //              mysql_class::ex_sqlx("update `anbar_det` set `other_moeen_id`=$moeen_id where `id`=$sel_id");
        }
	else
	{
		$sel_id = -1;
	}
	$grid = new jshowGrid_new("anbar_factor","grid1");
//	$grid->whereClause="`id` = $sel_id order by `tarikh`";
	$grid->columnHeaders[0] = null;
        $grid->columnHeaders[2] = "نام فروشنده";
        $grid->columnHeaders[1] = "شماره فاکتور فروشنده";
        $grid->columnHeaders[3] = "توضیحات";
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[6] = null;
	$grid->columnHeaders[5] = "تاریخ صدور رسید";
	$grid->columnFunctions[5] = "hpdate";
        $grid->columnCallBackFunctions[5] = "hpdateback";
	$grid->addFeild('id');
	$grid->columnHeaders[7] = "حساب معین";
	$grid->columnFunctions[7]='loadMoeen';
	$grid->addFeild('id');
        $grid->columnHeaders[8] = "صورت فاکتور";
        $grid->columnFunctions[8]='loadDet';

//	$grid->deleteFunction = 'delete_item';
	$grid->addFunction = "add_item";
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
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
		<script language="javascript" >
			var inps = document.getElementsByTagName('input');
			var element;
			for(var i=0;i<inps.length;i++)
			{
				element = inps[i].id.split('_');
				if(element[0]=='new' && element[1]=='id')
					inps[i].style.display = 'none';
			}
		</script>
	</body>
</html>
