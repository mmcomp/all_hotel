<?php
        session_start();
        include("../kernel.php");
	$out = '';
	if(isset($_REQUEST['RefId']) && isset($_REQUEST['ResCode']) && isset($_REQUEST['SaleOrderId']) && isset($_REQUEST['SaleReferenceId']) && isset($_REQUEST['CardHolderInfo']))
	{
		$RefId  = $_REQUEST['RefId'];
		$ResCode = $_REQUEST['ResCode'];
		$SaleOrderId = $_REQUEST['SaleOrderId'];
		$SaleReferenceId = $_REQUEST['SaleReferenceId'];
		$CardHolderInfo = $_REQUEST['CardHolderInfo'];
		$bank_out = array('RefId'=>$RefId,'ResCode'=>$ResCode,'SaleOrderId'=>$SaleOrderId,'SaleReferenceId'=>$SaleReferenceId,'CardHolderInfo'=>$CardHolderInfo);
		$pay = pay_class::verify($SaleOrderId,$SaleReferenceId);
		if(($pay == '0' || (int)$pay == 43) && (!is_array($pay)))
		{
			$pardakht = new pardakht_class($SaleOrderId);
			$moshtari = new moshtari_class($pardakht->moshtari_id);
			$tedad_pardakhti = (int)($pardakht->mablagh / $moshtari->mablagh);
			$pardakht->bank_out = serialize($bank_out);
			$pardakht->update();
			$moshtari->tedadpardakhti = $tedad_pardakhti;
			$moshtari->update();
			$rev = pay_class::settle($SaleOrderId,$SaleReferenceId);
			$out = 'پرداخت با موفقیت انجام شد
				<br />
				<input class="inp" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
		}
		else
			$out = ' پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، مبلغ از حساب شما کم نشده است
					<br/>
					<input class="inp" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
	}
	else
		$out = 'در تراکنش مالی مشکلی پیش آمده است پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، مبلغ از حساب شما کم نشده است
			<br/>
			<input class="inp" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="css/style.css" rel="stylesheet" />	
		<script type="text/javascript" src="js/tavanir.js"></script>
		<style>
		td { text-align: center; }
		</style>
		<title>
			سامانه رزرواسیون بهار
		</title>
		<script language="javascript" >
		</script>
	</head>
	<body style="background: #B5D3FF;padding-bottom: 0px;">
		<div align="center" style="background: #B5D3FF;" >
			<?php echo $out; ?>
			<br/>
			<br/>
		</div>
		</center>
	</body>
</html>
