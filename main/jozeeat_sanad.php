<?php
/*	session_start();
        include_once("../kernel.php");
	/*      if (isset($_SESSION['user_id']) && isset($_SESSION['typ']))
        {
                if (!audit_class::isAdmin($_SESSION['typ']))
                {
                        //die("<center><h1>ﺶﻣﺍ ﺐﻫ ﺍیﻥ ﺺﻔﺤﻫ ﺪﺴﺗﺮﺳی ﻥﺩﺍﺭیﺩ</h1></center>");
                }
        }
        else
        {
                        die("<center><h1>ﺶﻣﺍ ﺐﻫ ﺍیﻥ ﺺﻔﺤﻫ ﺪﺴﺗﺮﺳی ﻥﺩﺍﺭیﺩ</h1></center>");
        }*/	
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //var_dump($_SESSION);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$record_id = ((isset($_REQUEST['sel_id']))?(int)$_REQUEST['sel_id']:-1);
	$readable = ((isset($_REQUEST['readable']))?(int)$_REQUEST['readable']:-1);
	mysql_class::ex_sql("select `sanad_record` from `sanad_reserve` where `reserve_id`='$record_id'",$q);
	$r=mysql_fetch_array($q,MYSQL_ASSOC);
	$sanad_id= $r['sanad_record'];	
	function loadCode()
	{
		$record_id = ((isset($_REQUEST['sel_id']))?(int)$_REQUEST['sel_id']:-1);
	        mysql_class::ex_sql("select `sanad_record` from `sanad_reserve` where `reserve_id`='$record_id'",$q);
	        $r=mysql_fetch_array($q,MYSQL_ASSOC);
	        $sanad_id= $r['sanad_record'];
                $out = null;
                mysql_class::ex_sql("select `group_id`,`kol_id`,`moeen_id`,`tafzili_id`,`tafzilishenavar_id`,`tafzilishenavar2_id` from `sanad` where `id`='$sanad_id'",$q);
                $r=mysql_fetch_array($q,MYSQL_ASSOC);
		if ($r['group_id']!=0)
		{
			$g=$r['group_id'];
		}
		else
		{
			$g="";
		}
		if ($r['kol_id']!=0)
                {
			$k=$r['kol_id'];
		}
                else
                {
			$k="";
		}
		if ($r['moeen_id']!=0)
                {
			$m=$r['moeen_id'];
		}
                else
                {
			$m="";
		}
		if ($r['tafzili_id']!=0)
                {
			$t=$r['tafzili_id'];
		}
                else
                {
			$t="";
		}
		if ($r['tafzilishenavar_id']!=0)
                {
			$t1=$r['tafzilishenavar_id'];
		}
                else
                {
			$t1="";
		}
		if ($r['tafzilishenavar2_id']!=0)
                {
			$t2=$r['tafzilishenavar2_id'];
		}
                else
                {
			$t2="";
		}
                $out= $g.$k.$m.$t.$t1.$t2;
                return $out;
	
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
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
	$grid = new jshowGrid_new("sanad","grid1");
        $grid->whereClause="`id`='$sanad_id'";
        $grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = "شماره سند";
        $grid->columnHeaders[2] = "کد سند";
	$grid->columnFunctions[2] = 'loadCode';
        $grid->columnHeaders[3] = null;
        $grid->columnHeaders[4] = null;
        $grid->columnHeaders[5] = null;
	$grid->columnHeaders[6] = null;
	$grid->columnHeaders[7] = null;
        $grid->columnHeaders[8] = null;
	$grid->columnHeaders[9] = "تاریخ";
	$grid->columnFunctions[9] = "hamed_pdate";	
        $grid->columnHeaders[10] = null;
	$grid->columnHeaders[11] = null;
        $grid->columnHeaders[12] = "توضیحات";
	$grid->columnHeaders[13] = null;
        $grid->columnHeaders[14] = "مبلغ";
	if ($readable==1)
	{
		$grid->canEdit = TRUE;
	        $grid->canDelete = TRUE;
	}
	else
	{
		$grid->canEdit = FALSE;
                $grid->canAdd = FALSE;
                $grid->canDelete = FALSE;

	}
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
                        <?php
				echo $out;
                        ?>
                </div>
        </body>
</html>

