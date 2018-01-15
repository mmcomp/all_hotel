<?php
	class pay_class
	{
		public function getPishfactorDate($aztarikh,$tedad_pardakhti)
		{
			$out = jdate("d / m / Y",strtotime($aztarikh.' + '.$tedad_pardakhti.' month '));
			return($out);
		}
		public function verify($reserve_id,$refId)
		{
			$conf = new conf;
			$out = FALSE;
                        $client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
                        $namespace=$conf->mellat_namespace;//'http://interfaces.core.sw.bps.com/';
                        $terminalId = $conf->mellat_terminalId;
                        $userName = $conf->mellat_userName;
                        $userPassword = $conf->mellat_userPassword;
                        $orderId = $reserve_id;
        	        $verifySaleOrderId = $orderId;
                	$verifySaleReferenceId = $refId;

			$parameters = array(
			'terminalId' => $terminalId,
			'userName' => $userName,
			'userPassword' => $userPassword,
			'orderId' => $orderId,
			'saleOrderId' => $verifySaleOrderId,
			'saleReferenceId' => $verifySaleReferenceId);

			// Call the SOAP method
			$result = $client->call('bpVerifyRequest', $parameters, $namespace);
			return $result;
		}
		public function settle($reserve_id,$refId)
		{
                        $conf = new conf;
			$client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
                        $namespace=$conf->mellat_namespace;
			$terminalId = $conf->mellat_terminalId;
                        $userName = $conf->mellat_userName;
                        $userPassword = $conf->mellat_userPassword;
                        $orderId = $reserve_id;
			$settleSaleOrderId = $reserve_id;
			$settleSaleReferenceId = $refId;

			$parameters = array(
				'terminalId' => $terminalId,
				'userName' => $userName,
				'userPassword' => $userPassword,
				'orderId' => $orderId,
				'saleOrderId' => $settleSaleOrderId,
				'saleReferenceId' => $settleSaleReferenceId);

			// Call the SOAP method
			$result = $client->call('bpSettleRequest', $parameters, $namespace);
			return $result;
		}
		function revers($reserve_id,$refId)
		{
                        $conf = new conf;
			$out = FALSE;
			$client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
                        $namespace=$conf->mellat_namespace;
			$terminalId = $conf->mellat_terminalId;
                        $userName = $conf->mellat_userName;
                        $userPassword = $conf->mellat_userPassword;
                        $orderId = $reserve_id;
			$reversalSaleOrderId = $reserve_id;
			$reversalSaleReferenceId = $refId;
			$parameters = array(
				'terminalId' => $terminalId,
				'userName' => $userName,
				'userPassword' => $userPassword,
				'orderId' => $orderId,
				'saleOrderId' => $reversalSaleOrderId,
				'saleReferenceId' => $reversalSaleReferenceId);

			// Call the SOAP method
			$result = $client->call('bpReversalRequest', $parameters, $namespace);

			// Check for a fault
			if ($client->fault) {
				//
			} 
			else {
				$resultStr = $result;
				$err = $client->getError();
				if ($err) {
					// Display the error
				} 
				else 
					$out = $resultStr;
			}
			return $out;
		}
		function Inquiry($reserve_id,$refId)
		{
                        $conf = new conf;
			$out = FALSE;
			$client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
                        $namespace=$conf->mellat_namespace;
			$terminalId = $conf->mellat_terminalId;
                        $userName = $conf->mellat_userName;
                        $userPassword = $conf->mellat_userPassword;
                        $orderId = $reserve_id;
			$inquirySaleOrderId = $reserve_id;
			$inquirySaleReferenceId = $refId;

			$parameters = array(
				'terminalId' => $terminalId,
				'userName' => $userName,
				'userPassword' => $userPassword,
				'orderId' => $orderId,
				'saleOrderId' => $inquirySaleOrderId,
				'saleReferenceId' => $inquirySaleReferenceId);

			// Call the SOAP method
			$result = $client->call('bpInquiryRequest', $parameters, $namespace);

			// Check for a fault
			if ($client->fault) {
				//
			} 
			else {
				$resultStr = $result;
			
				$err = $client->getError();
				if ($err) {
					// Display the error
				} 
				else 
					$out = $resultStr;
			}// end Check for errors
			return $out;
		}
		public function pay($reserve_id,$amount)
		{
                        $conf = new conf;
			$out = FALSE;
			$client = new soapclient_nu($conf->mellat_wsdl);//'https://pgws.bpm.bankmellat.ir/pgwchannel/services/pgw?wsdl');
		        $namespace=$conf->mellat_namespace;//'http://interfaces.core.sw.bps.com/';
			$terminalId = $conf->mellat_terminalId;
        	        $userName = $conf->mellat_userName;
	                $userPassword = $conf->mellat_userPassword;
			$orderId = $reserve_id;
			$amount =audit_class::perToEn($amount);
			$localDate = date("Ymd");
			$localTime = date("His");
			$additionalData = '';
			$callBackUrl = $conf->mellat_callBackUrl;
			$payerId = 0;
			$parameters = array(
			'terminalId' => $terminalId,
			'userName' => $userName,
			'userPassword' => $userPassword,
			'orderId' => $orderId,
			'amount' => $amount,
			'localDate' => $localDate,
			'localTime' => $localTime,
			'additionalData' => $additionalData,
			'callBackUrl' => $callBackUrl,
			'payerId' => $payerId);

		// Call the SOAP method
			$result = $client->call('bpPayRequest', $parameters, $namespace);
			if ($client->fault)
			{
			//
			} 
			else 
			{
				// Check for errors
			
				$resultStr  = $result;

				$err = $client->getError();
				if ($err)
				{
					// Display the error
				} 
				else 
				{
					$out = $result;	
				}				
			}
			return($out);
		}
	}
?>
