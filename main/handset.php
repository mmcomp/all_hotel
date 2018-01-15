<?php
	include_once("../kernel.php");
	$out = '';
	if(isset($_REQUEST['refid']))
	{
		$RefId = $_REQUEST['refid'];
		$orderid = $_REQUEST['orderid'];
		$out = pay_class::settle($orderid,$RefId);
	}       
?>
<html>
	<head>
	</head>
	<body dir="rtl">
		<?php echo var_dump($out).'<br/>'; ?>
		<form>
			RefId : <input name="refid" value='<?php echo $RefId; ?>' /><br/>
			OrderId : <input name="orderid" value='<?php echo $orderid; ?>' /><br/>
			<input type="submit" value="ثبت" />
		</form>
	</body>
</html>
