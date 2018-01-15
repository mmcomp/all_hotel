<?php
$client = new SoapClient('https://acquirer.samanepay.com/payments/referencepayment.asmx?WSDL');
// var_dump($client->__getFunctions());
echo "verifying transaction 'F/AJEHpVjMUARiynnIPaRhu1lXhhoc' for terminal '31035713'<br/>\n";
var_dump($client->verifyTransaction('F/AJEHpVjMUARiynnIPaRhu1lXhhoc','31035713'));