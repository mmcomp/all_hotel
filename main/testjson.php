<?php
        session_start();
        include("../kernel.php");
/*        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);*/
	$out = "<select name=\"daftar\" id=\"daftar\" class=\"inp\" style=\"width:auto;\" onchange=\"document.getElementById('frm1').submit();\" ><option value='0' ></option>";
                $out_mirror = "<select  id=\"mirror_daftar\"  style=\"display:none;\" ><option value='0' ></option>";
                mysql_class::ex_sql('select `id`,`name` from `daftar` where `kol_id` > 0 order by `name` ',$q);
                while($r = mysql_fetch_array($q))
                {
                        $sel = (($r['id']==2)?'selected="selected"':'');
                        $out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
                        $out_mirror.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
                }
                $out.='</select>';
                $out_mirror.='</select>';
		echo $_REQUEST['json_string'];
		if(isset($_REQUEST['json_string']) && isset($_REQUEST['reserve_id']) )
//		echo $_REQUEST['json_string'];
                changeLog_class::add((int)$_REQUEST['reserve_id'],0,$_REQUEST['json_string']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <!-- Style Includes -->
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <link type="text/css" href="../css/style.css" rel="stylesheet" />

                <link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
                <script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
                <script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
<script  type="text/javascript" > 
			function submit_frm()
                        {
                                //alert(document.getElementById('daftar_idBelit_2').selectedIndex);
                                if( (parseInt(document.getElementById('m_belit_1').value,10)==0 && document.getElementById('daftar_idBelit_1').selectedIndex >0) || (parseInt(document.getElementById('m_belit_1').value,10)>0 && document.getElementById('daftar_idBelit_1').selectedIndex <=0) || (parseInt(document.getElementById('m_belit_2').value,10)==0 && document.getElementById('daftar_idBelit_2').selectedIndex >0) || (parseInt(document.getElementById('m_belit_2').value,10)>0 && document.getElementById('daftar_idBelit_2').selectedIndex <=0) )
                                {
                                        alert('ﺎﻃﻼﻋﺎﺗ ﻡﺮﺑﻮﻃ ﺐﻫ ﺐﻟیﺕ ﺭﺍ ﻭﺍﺭﺩ کﻥیﺩ');
                                }
                                else
                                {    
					alert("hh"); 
                                        document.getElementById("mod").value = 301;
                                        //----Creating Change Log-----------------
                                        if(document.getElementById('json_string'))
                                                document.getElementById('json_string').value = fetchJSON();
                                        //----------------------------------------
                                        document.getElementById('frm1').submit();
                                }
                        }
			function send_search()
                        {
                                if(document.getElementById('daftar'))
                                        document.getElementById('daftar').selectedIndex = -1;
/*                                if(document.getElementById('ajans'))
                                        document.getElementById('ajans_idBelit_1').selectedIndex = -1;
                                if(document.getElementById('daftar_idBelit_2'))
                                        document.getElementById('daftar_idBelit_2').selectedIndex = -1;
                                if(document.getElementById('ajans_idBelit_2'))
                                        document.getElementById('ajans_idBelit_2').selectedIndex = -1;*/
                                document.getElementById('mod').value=1;
                                document.getElementById('frm1').submit();
				alert ("hh");
                        }
</script>
<style>
                        td{text-align:center;}
                </style>
                <title>
                </title>
        </head>
        <body>
<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
<form id='frm1'  method='GET' >
                        <table border='1' >
                                <tr>
                                        <th>شماره رزرو</th>
                                        <th>جستجو</th>
                                </tr>
                                <tr>
                                        <td>
                                                <input class="inp" name="reserve_id" id="reserve_id" type="text" value="<?php echo ((isset($_REQUEST['reserve_id']))?(int)$_REQUEST['reserve_id']:0); ?>" >
                                        </td>
                                        <td>
                                                <input type="text" id="json_string" name="json_string" value="" />
                                                <input type='hidden' name='mod' id='mod' value='1' >
                                                <input type='hidden' name='mode1' id='mode1' value='0' >
                                                <input type='hidden' name='d' value="1"/>
                                                <input type='hidden' name="h_id" id="h_id" value="1" >
                                                <input type='button' value='جستجو' class='inp' onclick='send_search();' >
                                        </td>
                                </tr>
                        </table>
                        <?php echo $out; ?>
                        </form>
</body>
</html>
