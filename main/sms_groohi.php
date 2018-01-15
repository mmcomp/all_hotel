<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        //if(!$se->can_view)
              //  die(lang_fa_class::access_deny);
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return $out;
        }
	function  loadDaftar($daftar_ids)
	{
		$out = '<select size="10" multiple="multiple" name="ajans_id[]" id="ajans_id" class="inp" style="width:auto;" ><option value="-1">همه</option>';
		mysql_class::ex_sql("select `id`,`name` from `daftar` where `kol_id` > 0 order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$sel = ((in_array($r['id'],$daftar_ids))?'selected="selected"':'');
			$out.="<option $sel  value='".$r['id']."' >".$r['name']."</option>\n";
		}
		$out.='</select>';
		return $out;
	}
	function loadTyp($typs)
	{
		$sel1 =(in_array(1,$typs))?'selected="selected"':'';
		$sel2 =(in_array(2,$typs))?'selected="selected"':'';
		$sel3 =(in_array(3,$typs))?'selected="selected"':'';
		$sel4 =(in_array(4,$typs))?'selected="selected"':'';
		$out="<option value='1' $sel1 >عالی</option>\n";
		$out.="<option value='2' $sel2 >خوب</option>\n";
		$out.="<option value='3' $sel3 >متوسط</option>\n";
		$out.="<option value='4' $sel4 >ضعیف</option>\n";
		return $out;
	}
	$daftar_ids = (isset($_REQUEST['ajans_id']))?$_REQUEST['ajans_id']:array();
	$typs = (isset($_REQUEST['typ']))?$_REQUEST['typ']:array();
	$matn = (isset($_REQUEST['matn']))?$_REQUEST['matn']:'';
	$aztarikh = ((isset($_REQUEST['aztarikh']))?audit_class::hamed_pdateBack($_REQUEST['aztarikh']):date('Y-m-d H:i:s'));
	$tatarikh = ((isset($_REQUEST['tatarikh']))?audit_class::hamed_pdateBack($_REQUEST['tatarikh']):date('Y-m-d H:i:s'));
	$aztarikh = date("Y-m-d 00:00:00",strtotime($aztarikh));
	$tatarikh = date("Y-m-d 23:59:59",strtotime($tatarikh));
	$user_id=(int)$_SESSION['user_id'];
	$mod_tmp = -1;
	$mod = (isset($_REQUEST['mod']))?$_REQUEST['mod']:-2;
	$msg = '';
	$reset = '';
	$label = 'محاسبه تعداد';
	if($mod==-1)
	{
		$tedad =0;
		mysql_class::ex_sql("SELECT  count(`hotel_reserve`.`tozih`) as `tedad`
FROM  `hotel_reserve`
LEFT JOIN  `room_det` ON (  `hotel_reserve`.`reserve_id` =  `room_det`.`reserve_id` )
WHERE  `room_det`.`aztarikh` >=  '$aztarikh'
AND  `room_det`.`tatarikh` <=  '$tatarikh'
AND  `hotel_reserve`.`reserve_id` >0
GROUP BY  `hotel_reserve`.`reserve_id`,`hotel_reserve`.`tozih`",$q);
		if($r = mysql_fetch_array($q))
		{
			$tedad = (int)$r['tedad'];
			$mod_tmp = $tedad;
			$label = 'بله';
			$reset = "<input class='inp' type='button' value='خیر' onclick=\"send_reset();\" >";
		}
		$msg =  ' تعداد نفرات جهت ارسال پیامک '.$tedad.' است آیا پیامک‌ها ارسال شود؟';
	}
	else if($mod>0)
	{
		mysql_class::ex_sql("SELECT `hotel_reserve`.`tozih`
FROM  `hotel_reserve`
LEFT JOIN  `room_det` ON (  `hotel_reserve`.`reserve_id` =  `room_det`.`reserve_id` )
WHERE  `room_det`.`aztarikh` >=  '$aztarikh'
AND  `room_det`.`tatarikh` <=  '$tatarikh'
AND  `hotel_reserve`.`reserve_id` >0
GROUP BY  `hotel_reserve`.`reserve_id`,`hotel_reserve`.`tozih`",$qq);
		if($r = mysql_fetch_array($qq))
		{
			//send sms;
		}
		$msg = 'ارسال با موفقیت انجام شد';
		$label = 'محاسبه تعداد';
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
			<form id='frm1'  method='GET' >
			<table border='1' style='font-size:12px;width:95%;' >
				<tr>
					<th>نام دفتر</th>
					<th>نظر</th>
					<th>از تاریخ</th>
					<th>تا تاریخ</th>
				</tr>
				<tr valign="top" >
					<td>	
						<?php echo loadDaftar($daftar_ids); ?>
					</td>
					<td>
						<select class="inp" name="typ[]" id="typ" size="10" multiple="multiple" >
							<?php echo loadTyp($typs); ?>	
						</select>	
					</td>
					<td>	
         					   <input value="<?php echo ((isset($_REQUEST['aztarikh']))?$_REQUEST['aztarikh']:''); ?>" type="text" name='aztarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker6" />	
					</td>
					<td>
						<input value="<?php echo ((isset($_REQUEST['tatarikh']))?$_REQUEST['tatarikh']:''); ?>" type="text" name='tatarikh' readonly='readonly' class='inp' style='direction:ltr;' id="datepicker7" />
					</td>
				</tr>
				<tr>
					<td colspan="4" valign="top" >
						متن پیام:
					</td>
				</tr>
				<tr>
					<td colspan="4" valign="top" >
						<textarea onkeyup="updateLengthAndMessageCount2('matn','harf','tedad',1)" name="matn" id="matn" rows="10" cols="50" style="font-family:tahoma;font-size:12px;" ><?php echo $matn; ?></textarea>
						<br/>
						تعداد پیامک:
						<input style="width:30px;" value="<?php echo ((isset($_REQUEST['tedad']))?$_REQUEST['tedad']:''); ?>" id="tedad" name="tedad" class="inp" readonly="readonly" >
												حروف:
						<input  style="width:50px;" value="<?php echo ((isset($_REQUEST['harf']))?$_REQUEST['harf']:''); ?>" id="harf" name="harf" class="inp" readonly="readonly" >
					</td>
				</tr>
				<tr>
					<td colspan="4" >
						<?php echo $msg; ?>
						<input type='hidden' name='mod' id='mod' value='<?php echo $mod_tmp; ?>' >
						<input type='button' value='<?php echo $label; ?>' class='inp' onclick='send_search();' >
						<?php echo $reset; ?>
					</td>
				</tr>
			</table>
			</form>
			<br/>
		</div>
	</body>
</html>
