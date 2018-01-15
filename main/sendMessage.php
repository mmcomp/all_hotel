<?php
	include_once("../kernel.php");
	session_start();
	if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
        $isAdmin = $se->detailAuth('all');
	$user_id =(int)$_SESSION['user_id'];
        $user = new user_class($user_id);
//	echo $user;
	function sendMail($from,$to,$subject,$message)
	{
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=utf-8" . "\r\n";
		$headers .= 'From: <'.$from.'>' . "\r\n";
		$resultWork=mail($to,$subject,$message,$headers);
		if ($resultWork)
			echo "<script> alert ('پیشنهاد شما ارسال گردید.');</script>";
		else
			echo "<script> alert ('در ارسال پیشنهاد شما مشکلی بوجود آمده است');</script>";	
		return $resultWork;
	}
	function add_ticket($sender_id,$subject,$content)
        {
		$email = "www.habibihatam7@gmail.com";
		$from = "info@gcom.ir";
		sendMail($from,$email,$subject,$content);
		
                mysql_class::ex_sqlx("insert into `message` (`subject`,`content`,`sender`) values ('$subject','$content','$sender_id')");
        }
	function persian_time($inp)
	{
		$out ="نامشخض";
		if($inp== "0000-00-00 00:00:00")
		{
			$out = "-----";
		}
		else
		{
			$out = audit_class::enToPer(pdate("d / m / Y",strtotime($inp)));
		}
		return $out;
	}
	function ticket_status($inp)
	{
		$out = (($inp==1)?"بدون پاسخ":"پاسخ داده شده");
		return $out;
	}
	function ticket_view($inp)
	{
		$out = "<u><span style=\"cursor:pointer;color:blue;\"  onclick=\"wopen('viewMessage.php?id=$inp&access=1&','',800,400);\"  >مشاهده </span></u>";
		return $out;
	}
	function ticket_view_admin($inp)
        {
                $out = "<u><span style=\"cursor:pointer;color:blue;\"  onclick=\"wopen('viewMessage.php?id=$inp&access=-1&','',800,400);\"  >مشاهده </span></u>";
                return $out;
        }
	
/*	$user_id =(int)$_SESSION['user_id'];
echo $user_id;
        $user = new user_class($user_id);*/
	if(isset($_REQUEST["subject"]) && isset($_REQUEST["content"]) && ($_REQUEST["subject"]!="") && ($_REQUEST["content"]!="")    )
	{
		add_ticket($user_id,trim($_REQUEST["subject"]),trim($_REQUEST["content"]));
	}
        $grid = new jshowGrid_new("message","grid1");
	$grid->whereClause = " `sender`='$user_id' and en>0 order by id desc";
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	if($se->detailAuth('read'))
        {
		$grid->addFeild("id");
	        $grid->columnHeaders[10] = "جزئیات";
	        $grid->columnLists[10] = null;
	        $grid->columnFunctions[10]= "ticket_view_admin";
	        $grid->columnAccesses[10] = null;
	}
	else if($se->detailAuth('write'))
        {
		$grid->addFeild("id");
	        $grid->columnHeaders[10] = "ﺝﺰﺋیﺎﺗ";
        	$grid->columnLists[10] = null;
	        $grid->columnFunctions[10]= "ticket_view";
	        $grid->columnAccesses[10] = null;
	}
	else
	{
		$grid->addFeild("id");
        	$grid->columnHeaders[10] = null;
	}
/*	$grid->addFeild("id");
	$grid->columnHeaders[10] = "جزئیات";
	$grid->columnLists[10] = null;
	$grid->columnFunctions[10]= "ticket_view";
	$grid->columnAccesses[10] = null;*/
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = null;
	$grid->columnHeaders[2] = "موضوع";
	$grid->columnHeaders[3] = null;	
	$grid->columnHeaders[4] = null; 
	$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6] = null;
	$grid->columnHeaders[4] = "پاسخ";
	$grid->columnHeaders[7] = "زمان ارسال";
	$grid->columnFunctions[7] = "persian_time";
	$grid->columnHeaders[8] = "زمان پاسخ";
	$grid->columnFunctions[8] = "persian_time";
	$grid->columnHeaders[9] = "وضعیت";
	$grid->columnFunctions[9] = "ticket_status";
	$grid->divProperty = '';
	$grid->intial();
	$grid->executeQuery();
        $out = $grid->getGrid();

?>
<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link type="text/css" href="../css/style.css" rel="stylesheet" />
	<script src="../js/tavanir.js" type="text/javascript"></script>
</head>
<body>
	<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
	<div align="center" >
	<?php
	echo $out;	

	?>
	<br/>
	<br/>
	<form id="frm1" method="post">
		<table border="0" >
			<tr>
				<td style="width:10%" >
					موضوع:
				</td>
				<td align="right" >
					<input type="text" name="subject" class="inp" style="width:99%"  >
				</td>
			</tr>
			<tr>
				<td colspan="2" >
					پیشنهاد:
				</td>
			</tr>
			<tr>
				<td colspan="2"  >
					<textarea class="inp" align="right"  style="width:500px;text-align:right;"  name="content"  rows="10" cols="70"  ></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"  >
					<input type="submit" class="inp" value="ارسال پیشنهاد"  >
				</td>
			</tr>
		</table>
	</div>
	</form>
</body>
</html>
