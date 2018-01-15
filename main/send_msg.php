<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadDaftar()
        {
                $out='';
                mysql_class::ex_sql("select `name`,`id` from `daftar` order by `name`",$q);
                while($r=mysql_fetch_array($q,MYSQL_ASSOC))
                        $out.=$r['name']."<input checked='checked' name='daftar_".$r['id']."' id='daftar_".$r['id']."' type='checkbox' >&nbsp;&nbsp;&nbsp;\n";
                return $out;
        }
	function loadUsers($user_id=-1)
	{
		$out = "";
		mysql_class::ex_sql("select `id`,`fname`,`lname`,`daftar_id` from `user` where `user`<>'mehrdad' and `id` <> $user_id order by `daftar_id`,`lname`,`fname`",$q);
		while($r = mysql_fetch_array($q))
		{
			$daftar = new daftar_class((int)$r['daftar_id']);
			$out .= "<option value=\"".$r['id']."\">\n".$r['fname'].' '.$r['lname'].'('.$daftar->name.')'."\n</option>\n";
		}
		return($out);
	}
	$out= '';
	if(isset($_REQUEST['head']) && isset($_REQUEST['body']))
	{
		if($_REQUEST['head']=='' || $_REQUEST['body']=='')
		{
			$out = 'موضوع و متن پیام را کامل وارد کنید';
		}
		else
		{
			$head = $_REQUEST['head'];
			$body = $_REQUEST['body'];
			$user_ids = explode(',',$_REQUEST['user_id']);
			if(msg_class::sendUserMsg($head,$body,$user_ids))
				$out = 'پیام با موفقیت ارسال شد';
		}
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		ارسال پیام به کاربران
		</title>
		<script language="javascript">

			function appendOptionLast(elSel,txt,val)
			{
			  var elOptNew = document.createElement('option');
			  elOptNew.text = txt;
			  elOptNew.value = val;
			  try {
			    elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
			  }
			  catch(ex) {
			    elSel.add(elOptNew); // IE only
			  }
			}
			function selectUser(obj)
			{
				var userid = obj.options[obj.selectedIndex].value;
				var fname = obj.options[obj.selectedIndex].text;
				var v_user_id = document.getElementById('v_user_id');
				var user_id = document.getElementById('user_id');
				var users = user_id.value.split(',');
				var founded = false;
				for(var i=0;i<users.length;i++)
					if(users[i] == userid)
						founded = true;
				if(!founded)
				{
					user_id.value += ((user_id.value!='')?',':'')+userid;
					appendOptionLast(v_user_id,fname,userid);
				}
			}
			function selectAll(obj)
			{
				for(var i=1; i < obj.options.length;i++)
				{
					obj.selectedIndex = i;
					selectUser(obj);
				}
			}
			function deselectAll(obj)
			{
				for(var i=obj.options.length-1; i>=0 ;i--)
                                        obj.options[i] = null;
				document.getElementById('user_id').value = '';
			}
			function deselectUser(obj)
			{
				var main = document.getElementById('sel_user_id');
				var userid = obj.options[obj.selectedIndex].value;
                                var fname = obj.options[obj.selectedIndex].text;
                                var user_id = document.getElementById('user_id');
                                var users = user_id.value.split(',');
				user_id.value = '';
				for(var i=obj.options.length-1;i>=0;i--)
					obj.options[i] = null;
                                for(i=0;i<users.length;i++)
                                        if(users[i] != userid)
					{
						user_id.value += ((user_id.value!='')?',':'')+users[i];
						appendOptionLast(obj,loadText(main,users[i]),users[i]);
					}
			}
			function loadText(obj,val)
			{
				var out = '';
				for(var i=0;i < obj.options.length;i++)
				{
					if(obj.options[i].value == val)
						out = obj.options[i].text;
				}
				return(out);
			}
		</script>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<h3>ارسال پیام جهت دفاتر رزرو</h3>
			<form id="msgFrm" method="POST" >
				<table style="width:95%" >
					<tr>
						<td>موضوع</td>
						<td style="width:60%" align="right" ><input name="head" id="head" class="inp" style="width:95%" > </td>
						<td style="width:20%" >&nbsp;</td>
						<td style="width:20%" >&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" align="right" >متن کامل</td>
					</tr>
					<tr>
						<td colspan="2" align="center"  >
							<textarea style="width:90%;direction:rtl;font-family:tahoma,Tahoma;font-size:12px;" name="body" id="body" rows="25" cols="100" ></textarea>
						</td>
						<td style="width:20%"  align="center"  >
						<select SIZE="20" class="inp" id="v_user_id" ondblclick="deselectUser(this);" ><option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option></select><input type="hidden" id="user_id" value=""  name="user_id" />
						<br/>
						<input class="inp" type="button" value="لغو انتخاب" onclick="deselectUser(document.getElementById('v_user_id'));" />
						<input class="inp" type="button" value="لغو همه" onclick="deselectAll(document.getElementById('v_user_id'));" />
						</td>
						<td style="width:20%"  align="center"  >
						<select SIZE="20" ondblclick="selectUser(this);" class="inp" id="sel_user_id" ><?php echo /*loadDaftar();*/loadUsers(); ?></select>
						<br/>
						<input class="inp" type="button" value="انتخاب" onclick="selectUser(document.getElementById('sel_user_id'));" />
						
						<input class="inp" type="button" value="انتخاب همه" onclick="selectAll(document.getElementById('sel_user_id'));" />
						</td>
					</tr>
					<tr>
						<td colspan="2" ><input type="button" value="ارسال پیام" class="inp" onclick="document.getElementById('msgFrm').submit();"></td>
					</tr>
				</table>
			</form>			
			<?php echo $out; ?>
		</div>
	</body>
</html>
