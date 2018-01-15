<?php
	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$isAdmin = $se->detailAuth('all');
	function loadDaftar()
	{
		$out=null;
		mysql_class::ex_sql("select `name`,`id` from `daftar` order by `id`",$q);
		$out['مرکزی']=-1;
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$out[$r["name"]]=(int)$r["id"];
		}
		return $out;
	}
	function loadDaftarCombo($inp)
	{
		$out='';
		$se = security_class::auth((int)$_SESSION['user_id']);
		$isAdmin = $se->detailAuth('all');
		if($isAdmin)
		{
			$wer.=' 1=1';
			$out .="<option value='-1'>همه</option>";
		}
		else
		{
			$user_id =(int)$_SESSION['user_id'];
			$user = new user_class($user_id);
			$wer.=" `id`=".$user->daftar_id;
		}
		mysql_class::ex_sql("select `name`,`id` from `daftar` where $wer order by `name`",$q);
		echo "select `name`,`id` from `daftar` where $wer order by `name`";
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$sel = '';
			if($inp == (int)$r['id'])
				$sel = 'selected="selected"';
			$out.= "<option $sel value='".$r['id']."'>".$r['name']."</option>";
		}
		return $out;
	}
	function loadKarbar()
	{
		$out=null;
		mysql_class::ex_sql("select `fname`,`lname`,`id` from `user` order by `id`",$q);
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$out[$r["fname"].' '.$r["lname"] ]=(int)$r["id"];
		}
		return $out;
	}
	function loadPic($inp)
	{
		//$out = "<u><span onclick=\"wopen('view_pic.php?pic_addr=$inp&','',800,500);\"  style='color:blue;cursor:pointer;' >مشاهده</span></u>";
		$out = "<a href='$inp' target='_blank' >مشاهده</a>";
                return($out);
	}
	function date_parsi($inp)
	{
		return audit_class::hamed_pdate($inp);
	}
	function date_en($inp)
	{
		$inp = perToEnNums($inp);
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
                return $out;
	}
	$out ='';
	$out1 = '';
	$wer = '';
	$user_id = (int)$_SESSION['user_id'];
	$user = new user_class($user_id);
	$aztarikh_arr=array();
	if(isset($_FILES['uploadedfile']) && isset($_REQUEST['toz']) && isset($_REQUEST['mod']) && $_REQUEST['mod']==1 )
	{
		$toz = trim($_REQUEST['toz']);
		$tmp_target_path = "../upload";
		$ext = explode('.',basename( $_FILES['uploadedfile']['name']));
		$ext = $ext[count($ext)-1];
		if(strtolower($ext)=='jpg' || strtolower($ext)=='png' || strtolower($ext)=='gif' || strtolower($ext)=='tif' || strtolower($ext)=='jpeg' || strtolower($ext)=='pdf' )
		{	
			$target_path =$tmp_target_path.'/'.$user->daftar_id.'_'.$user_id.'_'.basename( $_FILES['uploadedfile']['name']); 
			if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path))
			{
			
				mysql_class::ex_sqlx("insert into `upload` (`daftar_id`,`user_id`,`toz`,`pic_addr`) values ('".$user->daftar_id."','$user_id','$toz','') ");
				mysql_class::ex_sql("select * from `upload` order by `id` desc limit 1",$q);
				if($r = mysql_fetch_array($q))
				{
					$passvand = $r['id'];
					$tar = str_replace('gcom',$passvand,$target_path);
				
					if(rename($target_path,$tar))
					{
						mysql_class::ex_sqlx("update `upload` set `pic_addr`='$tar' where `id`=$passvand");
						$out = "<script> alert('ارسال با موفقیت انجام شد');//window.parent.location = 'index.php'; </script>";	
					}
					else
					{
						mysql_class::ex_sqlx("delete from `upload` where `id`=$passvand");
						$out =  "در ذخیره تصویر مشکلی پیش آمده ، لطفاًً مجدداً ارسال نمایید .";
					}
				}
			} else{
				$out =  "در ارسال تصویر مشکلی پیش آمده ، لطفاًً مجدداً ارسال نمایید .";
			}
		}
		else
		{
			$out =  "فایل ارسالی مجاز نمی باشد";
		}
	}
	//if(isset($_REQUEST['mod']) && $_REQUEST['mod']==2 )
	//{
	$aztarikh =((isset($_REQUEST['aztarikh1']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh1']):date("Y-m-d H:i:s"));
	$aztarikh = explode(' ',$aztarikh);
	$aztarikh = $aztarikh[0].' 00:0:00';;
	$tatarikh = ((isset($_REQUEST['tatarikh1']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh1']):date("Y-m-d H:i:s + 1 day"));
	$tatarikh = explode(' ',$tatarikh);
	$tatarikh = $tatarikh[0].' 23:59:59';
	$daftar_id = ((isset($_REQUEST['daftar_id1']))?(int)$_REQUEST['daftar_id1']:-5);
	//$aztarikh_arr = array('aztarikh1'=>$aztarikh,'tatarikh1'=>$tatarikh ,'mod'=>2);
	$wer.="`tarikh`>'$aztarikh' and `tarikh`<'$tatarikh' and ";
	if($daftar_id ==-1)
		$wer.=' 1=1';
	else

		$wer.=" `daftar_id`=$daftar_id";
	
//}	
	
	
	
		$grid = new jshowGrid_new("upload","grid1");
		$grid->whereClause = $wer;
		$grid->columnHeaders[0] = null;
		$grid->columnHeaders[1] = 'دفتر';
		$grid->columnLists[1] = loadDaftar();
		$grid->columnHeaders[2] = 'کاربر';
		$grid->columnLists[2] = loadKarbar();
		$grid->columnHeaders[3] = 'توضیح';
		$grid->columnHeaders[4] = 'تصویر';
		$grid->columnFunctions[4] = 'loadPic' ;
		$grid->columnHeaders[5] = 'تاریخ';
		$grid->columnFunctions[5] = 'date_parsi';
		$grid->canEdit = FALSE;
		$grid->canAdd = FALSE;
		$grid->canDelete = FALSE;
		$grid->index_width= '20px';
		$grid->width= '95%';
		$grid->intial();
		$grid->executeQuery();
		$out1 = $grid->getGrid();
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
        <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		
                <title>
			ارسال فایل
                </title>

		<script type="text/javascript">
			function send_frm()
			{
				document.getElementById('mod').value= 1;
				var bool1 = ((document.getElementById('toz') && document.getElementById('toz').value!='')?true:false);
				var bool2 = ((document.getElementById('uploadedfile') && document.getElementById('uploadedfile').value!='')?true:false);
				document.getElementById('daftar_id1').value = document.getElementById('daftar_id').options[document.getElementById('daftar_id').selectedIndex].value;
				document.getElementById('aztarikh1').value = document.getElementById('datepicker6').value;
				document.getElementById('tatarikh1').value = document.getElementById('datepicker7').value;
				if(bool1 && bool2)
					document.getElementById('frm1').submit();
				else
					alert('کلیه اطلاعات باید وارد شود');
					
			}
			function send_search()
			{
				var bool1 = ((document.getElementById('datepicker6') && document.getElementById('datepicker6').value!='')?true:false);
				var bool2 = ((document.getElementById('datepicker7') && document.getElementById('datepicker7').value!='')?true:false);
				if(bool1 && bool2)
				{
					document.getElementById('daftar_id1').value = document.getElementById('daftar_id').options[document.getElementById('daftar_id').selectedIndex].value;
					document.getElementById('aztarikh1').value = document.getElementById('datepicker6').value;
					document.getElementById('tatarikh1').value = document.getElementById('datepicker7').value;
					document.getElementById('frm1').submit();
				}
				else
					alert('تاریخ ابتدا و انتها را انتخاب کنید');
				
			}
		</script>
		<script type="text/javascript">
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#datepicker6").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
		    $(function() {
			//-----------------------------------
			// انتخاب با کلیک بر روی عکس
			$("#datepicker7").datepicker({
			    showOn: 'button',
			    dateFormat: 'yy/mm/dd',
			    buttonImage: '../js/styles/images/calendar.png',
			    buttonImageOnly: true
			});
		    });
	    	</script>
        </head>
        <body>
                <?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center" >	
			<table border='1' style='font-size:12px;' >
				<tr>
					<th>دفتر</th>
					<th>ازتاریخ</th>
					<th>تا تاریخ </th>
					<th>جستجو</th>
				</tr>
				<tr valign="bottom" >
					<td>	
         					<select class='inp' name='daftar_id' id='daftar_id'>
							<?php echo loadDaftarCombo($daftar_id); ?>	
						</select>
					</td>
					<td>	
         					   <input value="<?php echo ((isset($_REQUEST['aztarikh1']))?$_REQUEST['aztarikh1']:''); ?>" type="text" name='aztarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td>
						<input value="<?php echo ((isset($_REQUEST['tatarikh1']))?$_REQUEST['tatarikh1']:''); ?>" type="text" name='tatarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
					<td>
						<input type='button' value='جستجو' class='inp' onclick='send_search();' >
					</td>					
				</tr>
			</table>
			<?php 
				echo $out1.' '.$out;
			 ?>
			<br/>

			<form enctype="multipart/form-data" method="POST" id="frm1" >
				<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
				<label>توضیحات</label>
				<input type="text" class="inp" name="toz" id="toz" >
				<input name="uploadedfile" type="file" id="uploadedfile" class="inp" /><br />
				<input type="button" class="inp" value="بروزرسانی" onclick="send_frm();" />
				<input type='hidden' name='mod' id='mod' value='2' >
				<input value="" type="hidden" name='aztarikh1'  id='aztarikh1' />	
				<input value="" type="hidden" name='tatarikh1' id='tatarikh1' />
				<input value="" type="hidden" name='daftar_id1' id='daftar_id1' />
			</form>
		</div>
        </body>
</html>
