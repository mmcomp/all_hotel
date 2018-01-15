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
	function loadDaftar()
        {
                $sdaftar_id=((isset($_REQUEST['sdaftar_id']))?(int)$_REQUEST['sdaftar_id']:-1);
                $outDaftar=null;
		if ($sdaftar_id==-2)
		{
                mysql_class::ex_sql("select name,id from daftar order by name",$q);
		}
		if ($sdaftar_id!=-2)
                {
                mysql_class::ex_sql("select name,id from daftar where `id` ='$sdaftar_id' order by name",$q);
                }

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
	$out=0;
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
        if (isset($_SESSION['user_id']) && isset($_SESSION['typ']))
        {
        if (!audit_class::isAdmin($_SESSION['typ']))
             {
		$user_id=$_SESSION['user_id'];
		$combo="<select class='inp' name='sdaftar_id' id='sdaftar_id' onchange=\"document.getElementById('gozaresh').submit();\">";
	        $sdaftar_id=((isset($_REQUEST['sdaftar_id']))?(int)$_REQUEST['sdaftar_id']:-1);
	        mysql_class::ex_sql("select * from user where `id`='$user_id'",$q);
	        $row=mysql_fetch_array($q);
	        $tmp=$row['daftar_id'];
	        mysql_class::ex_sql("select * from daftar where `id`='$tmp' order by name",$q1);
	        $combo.="<option class='inp' value='-1'></option>";
	        while ($r=mysql_fetch_array($q1,MYSQL_ASSOC))
		        {
                		if ((int) $r["id"]===$sdaftar_id)
                        	{
                                	$sel="selected='selected'";
                        	}
               			 else
                        	{
                                	$sel="";
                       		 }
               			 $combo.="<option class='inp' $sel  value='".$r["id"]."' >".$r["name"]."</option><br />\n";
		        }
	        $combo.="</select>";
                $comboAjans="<select class='inp' name='sajans_id' id='sajans_id'>";
		$sajans_id=((isset($_REQUEST['sajans_id']))?(int)$_REQUEST['sajans_id']:-1);
                mysql_class::ex_sql("select name,id from ajans where `daftar_id`='$sdaftar_id' order by name",$q);               $comboAjans.="<option class='inp' value='-1'></option>";
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
		if ((int) $r["id"]===$sajans_id)
                                {
                                        $sel="selected='selected'";
                                }
                                 else
                                {
                                        $sel="";
                                 }

                 $comboAjans.="<option class='inp' $sel  value='".$r["id"]."' >".$r["name"]."</option><br />\n";
                }
		if ((int) '-2'===$sajans_id)
                                {
                                        $sel1="selected='selected'";
                                }
                                 else
                                {
                                        $sel1="";
                                 }
                $comboAjans.="<option $sel1 class='inp' value='-2'>همه</option>";
                $comboAjans.="</select>";
		$comboHotel="<select class='inp' name='shotel_id' id='shotel_id'>";
		$shotel_id=((isset($_REQUEST['shotel_id']))?(int)$_REQUEST['shotel_id']:-1);
                mysql_class::ex_sql("select name,id from hotel order by name",$q);
		$comboHotel.="<option class='inp' value='-1'></option>";
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                if ((int) $r["id"]===$shotel_id)
                                {
                                        $sel="selected='selected'";
                                }
                                 else
                                {
                                        $sel="";
                                 }

                 $comboHotel.="<option class='inp' $sel  value='".$r["id"]."' >".$r["name"]."</option><br />\n";
                }
                if ((int) '-2'===$shotel_id)
                                {
                                        $sel1="selected='selected'";
                                }
                                 else
                                {
                                        $sel1="";
                                 }
                $comboHotel.="<option $sel1 class='inp' value='-2'>همه</option>";
                $comboHotel.="</select>";
		
        
	}
	else
	{
		$combo="<select class='inp' name='sdaftar_id' id='sdaftar_id' onchange=\"document.getElementById('gozaresh').submit();\">";
                $sdaftar_id=((isset($_REQUEST['sdaftar_id']))?(int)$_REQUEST['sdaftar_id']:-1);
                mysql_class::ex_sql("select * from daftar order by name",$q1);
                $combo.="<option class='inp' value='-1'></option>";
                while ($r=mysql_fetch_array($q1,MYSQL_ASSOC))
                        {
                                if ((int) $r["id"]===$sdaftar_id)
                                {
                                        $sel="selected='selected'";
                                }
                                 else
                                {
                                        $sel="";
                                 }
                                 $combo.="<option class='inp' $sel  value='".$r["id"]."' >".$r["name"]."</option>";
                        }
				if ((int) '-2'===$sdaftar_id)
                                {
                                        $sel1="selected='selected'";
                                }
                                 else
                                {
                                        $sel1="";
                                 }
		$combo.="<option class='inp' $sel1 value='-2'>همه</option>";
                $combo.="</select>";
                $comboAjans="<select class='inp' name='sajans_id' id='sajans_id'>";
		if ($sdaftar_id==-2)
		{
			mysql_class::ex_sql("select name,id from ajans order by name",$q);
		}
		else
		{
        	        mysql_class::ex_sql("select name,id from ajans where `daftar_id`='$sdaftar_id' order by name",$q);
		} 
		$comboAjans.="<option class='inp' value='-1'></option>";
		$sajans_id=((isset($_REQUEST['sajans_id']))?(int)$_REQUEST['sajans_id']:-1);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
		if ((int) $r["id"]===$sajans_id)
                                {
                                        $sel="selected='selected'";
                                }
                                 else
                                {
                                        $sel="";
                                 }
                 $comboAjans.="<option class='inp' $sel value='".$r["id"]."' >".$r["name"]."</option><br />";
                }
		 if ((int) '-2'===$sajans_id)
                                {
                                        $sel1="selected='selected'";
                                }
                                 else
                                {
                                        $sel1="";
                                 }
                $comboAjans.="<option $sel1 class='inp' value='-2'>همه</option>";
                $comboAjans.="</select>";
		$comboHotel="<select class='inp' name='shotel_id' id='shotel_id'>";
                $shotel_id=((isset($_REQUEST['shotel_id']))?(int)$_REQUEST['shotel_id']:-1);
                mysql_class::ex_sql("select name,id from hotel order by name",$q);
                $comboHotel.="<option class='inp' value='-1'></option>";
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                {
                if ((int) $r["id"]===$shotel_id)
                                {
                                        $sel="selected='selected'";
                                }
                                 else
                                {
                                        $sel="";
                                 }

                 $comboHotel.="<option class='inp' $sel  value='".$r["id"]."' >".$r["name"]."</option><br />\n";
                }
                if ((int) '-2'===$shotel_id)
                                {
                                        $sel1="selected='selected'";
                                }
                                 else
                                {
                                        $sel1="";
                                 }
                $comboHotel.="<option $sel1 class='inp' value='-2'>همه</option>";
                $comboHotel.="</select>";
	}
}
        else
        {
        	 die("<center><h1>ﺶﻣﺍ ﺐﻫ ﺍیﻥ ﺺﻔﺤﻫ ﺪﺴﺗﺮﺳی ﻥﺩﺍﺭیﺩ</h1></center>");        
	}       
	$sajans_id=-3;
	$f=-1;
	$t=-1;
	if (isset($_REQUEST['sajans_id']) && ($_REQUEST['sajans_id']!=""))
	{ 
		$sajans_id=$_REQUEST['sajans_id'];                        
	}
	if (isset($_REQUEST['shotel_id']) && ($_REQUEST['shotel_id']!=""))
        {
                $shotel_id=$_REQUEST['shotel_id'];
        }
	if (isset($_REQUEST['from']) && ($_REQUEST['from']!=""))
                {
                        $from=$_REQUEST['from'];
			$f=changedate($from);
		}
	if (isset($_REQUEST['to'])&& ($_REQUEST['to']!="") )
                {
                        $to=$_REQUEST['to'];
			$t=changedate($to);
		}
	$kolnafarat=0;
        $koleghamat=0;
        $koldaryafti=0;
        $kolbelit=0;
        $kolhotel=0;
        $kolbedehi=0;
	$grid = new jshowGrid_new("reserve","grid1");
	$grid->width="90%";
	$grid->query="select * from reserve where 1=-1";
	 if ((int) $sdaftar_id>0 && (int)  $sajans_id>0 && (int) $shotel_id>0)
        {
		$grid->query="select * from reserve where `daftar_id`='$sdaftar_id' and `ajans_id`='$sajans_id' and `hotel_id`='$shotel_id' and `tarikh`>'$f' and `tarikh`<='$t'";
		mysql_class::ex_sql("select sum(`nafarat`) as nafaratkol , sum(`shab`) as eghamatkol , sum(`m_belit`) as belit , sum(`m_hotel`) as hotel from reserve where `daftar_id`='$sdaftar_id' and `ajans_id`='$sajans_id' and `hotel_id`='$shotel_id' and `tarikh`>'$f' and `tarikh`<='$t'",$qbedehi);
		if($row=mysql_fetch_array($qbedehi))
		{
			$kolnafarat=$row['nafaratkol'];
                        $koleghamat=$row['eghamatkol'];
                        $kolbelit=$row['belit'];
                        $kolhotel=$row['hotel'];
		}	
        }
	if ((int) $sdaftar_id>0 && (int)  $sajans_id>0 && (int) $shotel_id==-2)
        {
                $grid->query="select * from reserve where `daftar_id`='$sdaftar_id' and `ajans_id`='$sajans_id' and `tarikh`>'$f' and `tarikh`<='$t'";
                mysql_class::ex_sql("select sum(`nafarat`) as nafaratkol , sum(`shab`) as eghamatkol , sum(`m_belit`) as belit , sum(`m_hotel`) as hotel from reserve where `daftar_id`='$sdaftar_id' and `ajans_id`='$sajans_id' and `tarikh`>'$f' and `tarikh`<='$t'",$qbedehi);
                if($row=mysql_fetch_array($qbedehi))
                {
                        $kolnafarat=$row['nafaratkol'];
                        $koleghamat=$row['eghamatkol'];
                        $kolbelit=$row['belit'];
                        $kolhotel=$row['hotel'];
                }
        }
	if ((int) $sdaftar_id==-2 && (int) $shotel_id==-2 && (int) $sajans_id>0)
	{
		$grid->query="select * from reserve where `ajans_id`='$sajans_id' and `tarikh`>'$f' and `tarikh`<='$t' ";
		mysql_class::ex_sql("select sum(`nafarat`) as nafaratkol , sum(`shab`) as eghamatkol , sum(`m_belit`) as belit , sum(`m_hotel`) as hotel from reserve where `ajans_id`='$sajans_id' and `tarikh`>'$f' and `tarikh`<='$t'",$qbedehi);
		if($row=mysql_fetch_array($qbedehi))

                {
			$kolnafarat=$row['nafaratkol'];
                        $koleghamat=$row['eghamatkol'];
                        $kolbelit=$row['belit'];
                        $kolhotel=$row['hotel'];
		}
	}
	if ((int) $sdaftar_id==-2 && (int) $shotel_id>0  && (int) $sajans_id>0)
        {
              $grid->query="select * from reserve where `ajans_id`='$sajans_id' and `hotel_id`='$shotel_id' and `tarikh`>'$f' and `tarikh`<='$t' ";
              mysql_class::ex_sql("select sum(`nafarat`) as nafaratkol , sum(`shab`) as eghamatkol , sum(`m_belit`) as belit , sum(`m_hotel`) as hotel from reserve where `ajans_id`='$sajans_id' and `hotel_id`='$shotel_id' and `tarikh`>'$f' and `tarikh`<='$t'",$qbedehi);
                if($row=mysql_fetch_array($qbedehi))

                {
                        $kolnafarat=$row['nafaratkol'];
                        $koleghamat=$row['eghamatkol'];
                        $kolbelit=$row['belit'];
                        $kolhotel=$row['hotel'];
                }
        }
	if ((int) $sdaftar_id>0 && (int) $sajans_id==-2 && (int) $shotel_id==-2)
	{
		$grid->query="select * from reserve where `daftar_id`='$sdaftar_id' and `tarikh`>='$f' and `tarikh`<='$t'";
		mysql_class::ex_sql("select sum(`nafarat`) as nafaratkol , sum(`shab`) as eghamatkol , sum(`m_belit`) as belit , sum(`m_hotel`) as hotel from reserve where `daftar_id`='$sdaftar_id' and `tarikh`>='$f' and `tarikh`<='$t'",$qbedehi);
		if($row=mysql_fetch_array($qbedehi))
                {
	                $kolnafarat=$row['nafaratkol'];
	                $koleghamat=$row['eghamatkol'];
	                $kolbelit=$row['belit'];
	                $kolhotel=$row['hotel'];
		}
	}
	if ((int) $sdaftar_id>0 && (int) $sajans_id==-2 && (int) $shotel_id>0)
        {
              $grid->query="select * from reserve where `daftar_id`='$sdaftar_id' and `hotel_id`='$shotel_id' and `tarikh`>='$f' and `tarikh`<='$t'";
              mysql_class::ex_sql("select sum(`nafarat`) as nafaratkol , sum(`shab`) as eghamatkol , sum(`m_belit`) as belit , sum(`m_hotel`) as hotel from reserve where `daftar_id`='$sdaftar_id' and `hotel_id`='$shotel_id' and `tarikh`>='$f' and `tarikh`<='$t'",$qbedehi);
                if($row=mysql_fetch_array($qbedehi))
                {
                        $kolnafarat=$row['nafaratkol'];
                        $koleghamat=$row['eghamatkol'];
                        $kolbelit=$row['belit'];
                        $kolhotel=$row['hotel'];
                }
        }
	if ((int) $sdaftar_id==-2 && (int) $sajans_id==-2 && (int) $shotel_id==-2)
        {
		$grid->query="select * from reserve where `tarikh`>'$f' and `tarikh`<='$t'";
		mysql_class::ex_sql("select sum(`nafarat`) as nafaratkol , sum(`shab`) as eghamatkol , sum(`m_belit`) as belit , sum(`m_hotel`) as hotel from reserve where `tarikh`>'$f' and `tarikh`<='$t'",$qbedehi);
		if($row=mysql_fetch_array($qbedehi))
                {
			$kolnafarat=$row['nafaratkol'];
                        $koleghamat=$row['eghamatkol'];
                        $kolbelit=$row['belit'];
                        $kolhotel=$row['hotel'];
		}
        }
	 if ((int) $sdaftar_id==-2 && (int) $sajans_id==-2 && (int) $shotel_id>0)
        {
               $grid->query="select * from reserve where `hotel_id`='$shotel_id' and `tarikh`>'$f' and `tarikh`<='$t'";
              mysql_class::ex_sql("select sum(`nafarat`) as nafaratkol , sum(`shab`) as eghamatkol , sum(`m_belit`) as belit , sum(`m_hotel`) as hotel from reserve where `hotel_id`='$shotel_id' and `tarikh`>'$f' and `tarikh`<='$t'",$qbedehi);
                if($row=mysql_fetch_array($qbedehi))
                {
                        $kolnafarat=$row['nafaratkol'];
                        $koleghamat=$row['eghamatkol'];
                        $kolbelit=$row['belit'];
                        $kolhotel=$row['hotel'];
                }
        }

	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="نام";
	$grid->columnHeaders[2]="نام خانوادگی";
	$grid->columnHeaders[3]="تعداد نفرات";
	$grid->columnHeaders[4]="مدت اقامت";
	$grid->columnHeaders[5]="نام هتل";
	$grid->columnHeaders[6]="بلیط";
	$grid->columnHeaders[7]="هتل";
	$grid->columnHeaders[9]="دفتر";
	$grid->columnHeaders[8]= "آژانس";
	$grid->columnHeaders[10]= "تاریخ";
	$grid->columnHeaders[11]= "توضیحات";
	$grid->columnHeaders[12]= "شماره سند";
	$grid->columnLists[9]=loadDaftar();
        $grid->columnLists[8]=loadAjans();
	$grid->columnLists[5]=loadHotel();
	$grid->columnFunctions[10]="hamed_pdate";
        $grid->columnCallBackFunctions[10]="hamed_pdateBack";
	$grid->canAdd=false;
	$grid->canDelete=false;
	$grid->canEdit=false;
$grid->footer = "<td class=\"showgrid_row_odd\">جمع:</td><td class=\"showgrid_row_odd\">&nbsp;</td><td class=\"showgrid_row_odd\">&nbsp;</td> <td class=\"showgrid_row_odd\">&nbsp;</td><td class=\"showgrid_row_odd\" style=\"border-right-style:solid;border-right-width:1px;border-right-color:#FFF;text-align:center;\">$kolnafarat</td><td class=\"showgrid_row_odd\" style=\"border-right-style:solid;border-right-width:1px;border-right-color:#FFF;text-align:center;\">$koleghamat</td><td class=\"showgrid_row_odd\" style=\"border-right-style:solid;border-right-width:1px;border-right-color:#FFF;text-align:center;\">&nbsp;</td><td class=\"showgrid_row_odd\" style=\"border-right-style:solid;border-right-width:1px;border-right-color:#FFF;text-align:center;\">$kolbelit</td><td class=\"showgrid_row_odd\" style=\"border-left-style:solid;border-left-width:1px;border-right-style:solid;border-right-width:1px;border-right-color:#FFF;border-left-color:#FFF;text-align:center;\">$kolhotel</td> <td class=\"showgrid_row_odd\">&nbsp;</td><td class=\"showgrid_row_odd\">&nbsp;</td><td class=\"showgrid_row_odd\">&nbsp;</td><td class=\"showgrid_row_odd\">&nbsp;</td><td class=\"showgrid_row_odd\" style=\"border-right-style:solid;border-right-width:1px;border-right-color:#FFF;text-align:center; \">&nbsp;</td>";
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
		سامانه ارزیابی عملکرد کارکنان شرکت مدیریت تولید نیروگاه‌های گازی خراسان
		</title>
	</head>
	<body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<form name="gozaresh" id="gozaresh">
				<label>دفتر</label>
				<?php echo $combo;?>
				<label>آژانس</label>
				<?php echo $comboAjans;?>
				<label>هتل</label>
                                <?php echo $comboHotel;?>
				<label>از تاریخ</label>
				<?php $setfrom=((isset($_REQUEST['from']))?$_REQUEST['from']:"");
				echo "<input class='inp' type=\"text\" name=\"from\" id=\"from\" value=\"$setfrom\"/>";
				$setto=((isset($_REQUEST['to']))?$_REQUEST['to']:"");
				echo "<label>تا تاریخ</label>";
				echo "<input class='inp' type=\"text\" name=\"to\" id=\"to\" value=\"$setto\"/>";
				?>
				<input class="inp" type="submit" name="send" value="گزارشگیری"/>
			</form>
			<br/>
			<br/>
			<?php echo $out;  ?>
		</div>
	</body>
</html>
