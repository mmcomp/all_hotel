<?php
	include_once("../kernel.php");
	session_start();
	if(!isset($_SESSION['user_id']))
		die(lang_fa_class::access_deny);
	$se = security_class::auth((int)$_SESSION['user_id']);
	//var_dump($_SESSION);
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
	$out= '';
	$msg_id =-1;
	if(isset($_REQUEST['msg_id']) )
		$msg_id = (int)$_REQUEST['msg_id'];
	$msg = new msg_class($msg_id);
	if($msg->user_id==(int)$_SESSION['user_id'])
		msg_class::setRead($msg_id);
	
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
مشاهده پیام
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>
			<h3>مشاهده پیام</h3>

				<table style="width:80%" >
					<tr>
						<td>موضوع</td>
						<td style="width:95%" align="right" ><input readonly="readonly" value="<?php echo $msg->head; ?>" name="head" id="head" class="inp" style="width:95%" > </td>
					</tr>
					<tr>
						<td colspan="2" align="right" >متن کامل</td>
					</tr>
					<tr>
						<td colspan="2" >
							<textarea readonly="readonly" style="width:95%;direction:rtl;font-family:tahoma,Tahoma;font-size:12px;" name="body" id="body" rows="25" cols="100" ><?php echo $msg->body; ?></textarea>
						</td>
					</tr>
					
				</table>

			<?php echo $out; ?>
		</div>
	</body>
</html>
