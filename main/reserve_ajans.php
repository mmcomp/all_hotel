<?php
	session_start();
        include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
        $isAdmin = $se->detailAuth('all');
		
	function  loadHotel($inp=-1)
        {
                $inp = (int)$inp;
                $hotelList=daftar_class::hotelList((int)$_SESSION['daftar_id']);
                $shart = '';
                if($hotelList)
                        $shart=' and ( `id`='.implode(" or `id`=",$hotelList).")";
                $out = '<select name="hotel_id" class="inp" style="width:auto;" >';
                mysql_class::ex_sql("select `id`,`name` from `hotel` where `moeen_id` > 0 $shart order by `name` ",$q);
                while($r = mysql_fetch_array($q))
                {
                        $sel = (($r['id']==$inp)?'selected="selected"':'');
                        $out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
                }
                $out.='</select>';
                return $out;
        }
	function loadNumber($inp=-1)
        {
                $out = '';
                $inp = (int)$inp;
                for($i=1;$i<32;$i++)
                {
                        $sel = (($i==$inp)?'selected="selected"':'');
                        $out.="<option $sel  value='$i' >$i</option>\n";
                }
                return $out;
        }

	$hotel_id = ((isset($_REQUEST['hotel_id']))?(int)$_REQUEST['hotel_id']:-1);
        //-----newstart-----
//        $h_id = ((isset($_REQUEST['hotel_id']))?$hotel_id:(int)$_REQUEST['h_id']);

	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
        $az = strtotime($aztarikh);
        $no = strtotime(date('Y-m-d H:i:s'));
        if($_SESSION["typ"] !=0 && $az < $no)
                $aztarikh = date("Y-m-d H:i:s");

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
                        function radioChecked()
                        {
                                var out = false;
                                var inps = document.getElementsByTagName('input');
                                for(var i=0;i < inps.length;i++)
                                        if(inps[i].type=='radio' && inps[i].checked)
                                                out = true;
                                if(document.getElementById('daftar_id').selectedIndex <= 0 || document.getElementById('tour_mablagh').value=='' || (parseInt(document.getElementById('belit_mablagh').value,10)>0 && document.getElementById('daftar_idBelit').selectedIndex <= 0))
                                        out = false;
                                //if(parseInt(document.getElementById('tour_mablagh').value,10)>=parseInt(document.getElementById('belit_mablagh').value,10))
                                        //out= false;
                                return(out);
                        }
                        function checkboxChecked()
{
                                var out = false;
                                var tmp;
                                var inps = document.getElementsByTagName('input');
                                for(var i=0;i < inps.length;i++)
                                {
                                        tmp = String(inps[i].id).split('_');
                                        if(tmp[0]=='otagh' && inps[i].type=='checkbox' && inps[i].checked)
                                                out = true;
                                }
                                if(document.getElementById('daftar_id').selectedIndex <= 0 || document.getElementById('tour_mablagh').value=='' || (parseInt(document.getElementById('belit_mablagh_1').value,10)>0 && document.getElementById('daftar_idBelit_1').selectedIndex <= 0) || (parseInt(document.getElementById('belit_mablagh_2').value,10)>0 && document.getElementById('daftar_idBelit_2').selectedIndex <= 0) || parseInt(document.getElementById('tour_mablagh').value,10)==0 )
                                        out = false;
                                return(out);
                        }
                        function send_search()
                        {
                                if(document.getElementById('tour_mablagh')) document.getElementById('tour_mablagh').value='';
                                if(document.getElementById('belit_mablagh')) document.getElementById('belit_mablagh').value='';
                                if(document.getElementById('daftar_id')) document.getElementById('daftar_id').selectedIndex = -1;
                                if(document.getElementById('ajans_id')) document.getElementById('ajans_id').selectedIndex = -1;
                                if(document.getElementById('daftar_idBelit_1')) document.getElementById('daftar_idBelit_1').selectedIndex = -1;
                                if(document.getElementById('ajans_idBelit_1')) document.getElementById('ajans_idBelit_1').selectedIndex = -1;
                                if(document.getElementById('daftar_idBelit_2')) document.getElementById('daftar_idBelit_2').selectedIndex = -1;
                                if(document.getElementById('ajans_idBelit_2')) document.getElementById('ajans_idBelit_2').selectedIndex = -1;
 document.getElementById('mod').value=1;
                                if(document.getElementById('datepicker6').value!='')
                                        document.getElementById('frm1').submit();
                                else 
                                        alert("ﺕﺍﺭیﺥ ﺭﺍ ﻭﺍﺭﺩ کﻥیﺩ");
                        }
                        function kh_check(inp)
                        {
                                var mainObj = document.getElementById('kh_ch_'+inp);
                                var vObj = document.getElementById('kh_v_'+inp);
                                var khObj = document.getElementById('kh_kh_'+inp);
                                if(vObj.checked || khObj.checked )
                                        mainObj.checked = true;
                                else
                                        mainObj.checked = false;
                        }
                </script>
                <script type="text/javascript" src="../js/tavanir.js"></script>
                <script type="text/javascript">
            $(function() {
                //-----------------------------------
                // ﺎﻨﺘﺧﺎﺑ ﺏﺍ کﻝیک ﺏﺭ ﺭﻭی ﻉکﺱ
                $("#datepicker6").datepicker({
                    showOn: 'button',
                    dateFormat: 'yy/mm/dd',
                    buttonImage: '../js/styles/images/calendar.png',
                    buttonImageOnly: true
                });
            });
    </script>
                <style>
                        td{text-align:center;}
                </style>
                <title>
			سامانه رزرواسیون
                </title>
        </head>
        <body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
                <div align="center">
                        <br/>
                        <br/>
                        <form id="frm2" method="get">
                                <input type="hidden" id="mode1" name="mode1" value="1"/>
                        </form>
                        <br/>
                        <br/>
                        <form id='frm1'  method='GET' >
                        <table border='1' >
                                <tr>
                                        <th>نام هتل</th>
                                        <th>تاریخ</th>
                                        <th>مدت اقامت</th>
                                        <th>شب-رزرو<br/>(نیم شارژ ورودی)</th>
                                        <th>روز-رزرو<br/>(نیم شارژ خروجی)</th>
                                        <th>جستجو</th>
                                </tr>
<tr>
                                        <td>
                                                <?php
                                                        if(isset($_GET['h_id']))
                                                                echo loadHotel((int)$_GET['h_id']);
                                                        else
                                                                echo loadHotel($hotel_id);
                                                ?>
                                        </td>
                                        <td>
                                                   <input value="<?php echo ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdate($aztarikh):audit_class::hamed_pdate(date("Y-m-d H:i:s"))); ?>" type="text" name='aztarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker6" />
                                        </td>
                                        <td>
                                                <select  class='inp' name='shab' >
                                                        <?php  echo loadNumber($_REQUEST['shab']); ?>
                                                </select>
                                        </td>
                                        <td>
                                                <input name="shabreserve" id="shabreserve" type="checkbox" <?php echo ((isset($_REQUEST['shabreserve']))?'checked="checked"':''); ?> >
                                        </td>
                                        <td>
                                                <input name="roozreserve" id="roozreserve" type="checkbox" <?php echo ((isset($_REQUEST['roozreserve']))?'checked="checked"':''); ?> >
                                        </td>
                                        <td>
                                                <input type='hidden' name='mod' id='mod' value='1' >
                                                <input type='hidden' name='mode1' id='mode1' value='0' >
                                                <input type='button' value='جستجو' class='inp' onclick='send_search();' >
                                        </td>
                                </tr>
                        </table>
                        </form>
                </div>
        </body>
</html>

