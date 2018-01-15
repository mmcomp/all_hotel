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
	function persian_time($inp)
        {
                $out ="ﻥﺎﻤﺸﺨﺿ";
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
                $out = (($inp==1)?"ﺏﺩﻮﻧ پﺎﺴﺧ":"پﺎﺴﺧ ﺩﺍﺪﻫ ﺵﺪﻫ");
                return $out;
        }
	function ticket_view($inp)
        {
		$out = "<a href=\"#?P_id=$inp&show=1\">مشاهده</a>";
//                $out = "<u><span style=\"cursor:pointer;color:blue;\"  onclick=\"wopen('viewMessage.php?id=$inp&access=1&','',800,400);\"  >ﻢﺷﺎﻫﺪﻫ </span></u>";
                return $out;
        }
	if ((isset($_REQUEST["access"])) && ($_REQUEST["access"]==1))
	{
		if(isset($_REQUEST["P_id"]) && $_REQUEST["P_id"] != "" && isset($_REQUEST["access"]))
		{
			$out = FALSE;
			$isAdmin = FALSE;
			$id = (int)$_REQUEST["id"];
			mysql_class::ex_sql("select * from `message` where `id`='$id'",$q);
			if($r = mysql_fetch_array($q))
			{
				$out = TRUE;
				$subject = trim($r["subject"]);
				$content = trim($r["content"]);
				$answer = trim($r["answer"]);
				$en = (int)$r["en"];
			}
/*		if((int)$_SESSION["user_type"] == 0 && $en == 1)
                        $isAdmin = TRUE;*/
		}
	}
	else if(isset($_REQUEST["id"]) && $_REQUEST["id"] != "" && isset($_REQUEST["mod"]) && $_REQUEST["mod"]=="edit")
	{
		$id = (int)$_REQUEST["id"];
		$answer = trim($_REQUEST["answer"]);		
		$answertime = date("Y-m-d H:i:s");
		mysql_class::ex_sqlx("update `message` set `answer` = '$answer' , `answertime` = '$answertime' , `en` = 2 where `id` = '$id'");
		$out = FALSE;
	}
/*	if(!$out)
	{
		die("<script> window.opener.location = window.opener.location; window.close();</script>");
	}*/
	$grid = new jshowGrid_new("message","grid1");
//        $grid->whereClause = " `sender`='$user_id' and en>0 order by id desc";
        $grid->canAdd = FALSE;
        $grid->canEdit = FALSE;
        $grid->canDelete = FALSE;
	$grid->addFeild("id");
        $grid->columnHeaders[10] = "ﺝﺰﺋیﺎﺗ";
        $grid->columnLists[10] = null;
        $grid->columnFunctions[10]= "ticket_view";
        $grid->columnAccesses[10] = null;
	$grid->columnHeaders[0] = null;
        $grid->columnHeaders[1] = null;
        $grid->columnHeaders[2] = "ﻡﻮﺿﻮﻋ";
        $grid->columnHeaders[3] = null;
        $grid->columnHeaders[4] = null;
        $grid->columnHeaders[5] = null;
        $grid->columnHeaders[6] = null;
        $grid->columnHeaders[4] = "پﺎﺴﺧ";
        $grid->columnHeaders[7] = "ﺰﻣﺎﻧ ﺍﺮﺳﺎﻟ";
        $grid->columnFunctions[7] = "persian_time";
        $grid->columnHeaders[8] = "ﺰﻣﺎﻧ پﺎﺴﺧ";
        $grid->columnFunctions[8] = "persian_time";
        $grid->columnHeaders[9] = "ﻮﻀﻋیﺕ";
        $grid->columnFunctions[9] = "ticket_status";
        $grid->divProperty = '';
        $grid->intial();
        $grid->executeQuery();
        $outgrid = $grid->getGrid();

?>
<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link type="text/css" href="../css/style.css" rel="stylesheet" />
        <script src="../js/tavanir.js" type="text/javascript"></script>
</head>
<body>
	<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
	<div align="center">
	<?php echo $outgrid;?>
	</div>
	<br/>
	<br/>
<form id="frm1">
        <div align="center">
	<?php if ((isset($_REQUEST["access"])) && ($_REQUEST["access"]==1))
		{
	?>
                <table width="80%">
                        <tr>
                                <td >
                                        موضوع:
                                </td>
			</tr>
			<tr>
                                <td align="right" style="border-style:solid;border-width:1px;border-color:#ffffff;" >
					<?php echo $subject ?>
                                        <!--<input type="text" name="subject" class="inp" style="width:99%"  readonly="readonly" value="<?php echo $subject ?>">-->
                                </td>
                        </tr>
                        <tr>
                                <td colspan="2" >
                                        پیشنهاد:
                                </td>
                        </tr>
                        <tr>
                                <td colspan="2" style="border-style:solid;border-width:1px;border-color:#ffffff;" >
                                        <!--<textarea class="inp" style="width:500px;text-align:right;"  name="content"  rows="10" cols="70" readonly="readonly"  >-->
						<?php
							echo $content;
						?>
                                        <!--</textarea>-->
                                </td>
                        </tr>
			<tr>
                                <td colspan="2" >
                                        پاسخ:
                                </td>
                        </tr>
                        <tr>
                                <td colspan="2"  >
<!--                                        <textarea class="inp" style="width:100%;text-align:right;"  name="answer"  rows="10" cols="70" <?php //echo (($isAdmin)?"":"readonly=\"readonly\""); ?>  ><?php //echo $answer; ?></textarea>-->
					    <textarea class="inp" style="width:100%;text-align:right;"  name="answer"  rows="10" cols="70"></textarea>

                                </td>
                        </tr>
                        <tr >
                                <td colspan="2"  >
                                        <input type="submit" class="inp" value="پاسخ به پیشنهاد"  >
					<input type="hidden" id="mod" name="mod" value="edit" />
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
                                </td>
                        </tr>			
                </table>
		<?php }?>
        </div>
</form>
</body>
</html>
