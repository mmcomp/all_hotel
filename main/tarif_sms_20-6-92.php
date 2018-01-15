<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //if(!$se->can_view)
              //  die(lang_fa_class::access_deny);
	$typ_sms[1] = "روز ورود";
	$typ_sms[2] = "روز خروج";
	$typ_sms[3] = "خدمات";
	$matns = '';
	if (isset($_REQUEST['mod']))
	{
		foreach($_REQUEST as $name)
		{
			$matns .= $name.',';
		}
		$re = explode(",",$matns);
		$id = $re[0];
		$typ = $re[1];
		$matn_sms = $re[2];
		if (($id!='')&&($typ!='')&&($matn_sms!=''))
		{
			mysql_class::ex_sqlx("update `mehman_sms` SET `matn` = '$matn_sms' WHERE `id` ='$id'");
		}
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<link type="text/css" href="../js/styles/jquery-ui-1.8.14.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery.ui.datepicker-cc.all.min.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript">
		function send_search()
		{
			if(trim(document.getElementById('matn').value)=='')
				alert('متن  پیامک وارد نشده است');
			else
				document.getElementById('frm1').submit();
		}
		function set_value(inp)
		{
			document.getElementById('mablagh').value = document.getElementById(inp).innerHTML;
		}
		function processEnter(input)
		{
		    var enterCount = 0;
		    for (var i = 0; i < input.length; i++)
		    {
			if (input.charCodeAt(i) == 10)
			{
			    enterCount ++;
			}
		    }
		    return enterCount;
		}
		function isEnglishString(input )
		{
		    if (input == '')
		    {
			return true;
		    }
		    
		    for (var i = 0; i < input.length; i++)
		    {
			if (input.charCodeAt(i) > 127)
			{   
			    return false;
			}
		    }
		    return true;
		}

		function updateLengthAndMessageCount2(MainTextFieldName, messageLengthFieldName, messageCountFieldName, fieldmobileCount)
		{
		    var fieldObj = document.getElementById(MainTextFieldName);
		    var messageLengthField = document.getElementById(messageLengthFieldName);
		    var messageCountField = document.getElementById(messageCountFieldName);
		    var messageContent1 = fieldObj.value;
		    var messageContent = messageContent1.replace(/(\r\n|\r|\n)/g, ' ');
		    
		    var enterCount = processEnter(messageContent);
		    var browserName = navigator.appName;
		    var messageLength = messageContent.length + fieldmobileCount;

		       
		       if (browserName != 'Netscape')
		 	{
				messageLength = messageLength - enterCount;
		    	}

		    var RemainLength = 0;

		    var maxMessageCount = 4;
		    var maxEnglishLength = 160;
		    var maxPersianLength = 70;
		    var maxLongEnglishLength = 153;
		    var maxLongPersianLength = 63;
		    var isEnMessage = isEnglishString(messageContent);
		    
		    var maxMessageLength = isEnMessage ? (maxMessageCount * maxLongEnglishLength) : (maxMessageCount * maxLongPersianLength);
		    fieldObj.MaxLength = maxMessageLength;

		    var messageCount = 1;

		    if (isEnMessage && messageLength > maxEnglishLength)
		    {
			messageCount = messageLength > maxMessageLength ?
				       maxMessageCount : parseInt(messageLength % maxLongEnglishLength) == 0 ?
				                         parseInt(messageLength / maxLongEnglishLength) :
				                         parseInt(messageLength / maxLongEnglishLength) + 1;
		    }
		    
		    if (!isEnMessage && messageLength > maxPersianLength)
		    {
			messageCount = messageLength > maxMessageLength ?
				       maxMessageCount : parseInt(messageLength % maxLongPersianLength) == 0 ?
				                         parseInt(messageLength / maxLongPersianLength) :
				                         parseInt(messageLength / maxLongPersianLength) + 1;

		    }
		    
		    if (messageCount == 1) 
		    {
		    RemainLength = isEnMessage ?  parseInt(maxEnglishLength - messageLength) : parseInt( maxPersianLength - messageLength)
		    
		    }
		    else
		    {
		    RemainLength = isEnMessage ?  parseInt(messageCount * maxLongEnglishLength - messageLength) : parseInt( messageCount * maxLongPersianLength - messageLength)
		   
		    }
		    messageLengthField.value =  RemainLength;
		    messageCountField.value = messageCount;

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
		<style type="text/css" >
			td{text-align:center;}
		</style>
		<title>
			ارسال پیامک	
		</title>
	</head>
	<body>
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<div align="center">
			<br/>
			<br/>	
			<table cellpadding="0" cellspacing="0" width="95%" style="border-style:solid;border-width:1px;border-color:Black;">
				<tr class="showgrid_header" >
					<th>ردیف</th>
					<th>متن پیامک</th>
					<th>نوع </th>
					<th> </th>
					<th> </th>
				</tr>		
			<?php
				mysql_class::ex_sql("select * from `mehman_sms`",$q);
				$i = 1;
				while($r=mysql_fetch_array($q))
				{
					$row_style = 'class="showgrid_row_odd"';
					$matn = $r['matn'];
					$noe_sms = $r['typ'];
					$noe = $typ_sms[$noe_sms];
					if($i%2==0)
						$row_style = 'class="showgrid_row_even"';
					$matn_name = "matn_".$i;
			?>
			<form id='frm1'  method='GET' >		
				<tr <?php echo $row_style;?>>
					<td valign="center" ><?php echo $i;?></td>
					<td valign="center" ><?php echo $noe;?></td>
					<td valign="top" >
						<input type='hidden' name="id_sms_<?php echo $i;?>" id="id_sms_<?php echo $i;?>" value="<?php echo $r['id'];?>" >
						<input type='hidden' name="typ_sms_<?php echo $i;?>" id="typ_sms_<?php echo $i;?>" value="<?php echo $noe_sms;?>" >
						<textarea onkeyup="updateLengthAndMessageCount2('matn_<?php echo $i;?>','harf_<?php echo $i;?>','tedad_<?php echo $i;?>',1)" name="<?php echo $matn_name;?>" id="<?php echo $matn_name;?>" rows="5" cols="30" style="font-family:tahoma;font-size:12px;" ><?php echo $matn; ?></textarea>
					</td>
					<td valign="center" >
						تعداد پیامک:
						<input style="width:30px;" value="<?php echo ((isset($_REQUEST['tedad']))?$_REQUEST['tedad']:''); ?>" id="tedad_<?php echo $i;?>" name="tedad_<?php echo $i;?>" class="inp" readonly="readonly" >
												حروف:
						<input  style="width:50px;" value="<?php echo ((isset($_REQUEST['harf']))?$_REQUEST['harf']:''); ?>" id="harf_<?php echo $i;?>" name="harf_<?php echo $i;?>" class="inp" readonly="readonly" >
					</td>
					<td>
						<input type='hidden' name='mod' id='mod' value='1' >				
						<input type="submit" value="ذخیره" class="inp"/>
					</td>
				</tr>
			</form>
			<?php 
				$i++;
				}
			?>
			</table>
			<br/>
		</div>
	</body>
</html>
