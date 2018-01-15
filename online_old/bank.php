<?php
/*
array(10) {
  ["State"]=>
  string(16) "Canceled By User"
  ["StateCode"]=>
  string(2) "-1"
  ["ResNum"]=>
  string(19) "0.00916052773705267"
  ["MID"]=>
  string(8) "10615632"
  ["RefNum"]=>
  string(0) ""
  ["CID"]=>
  string(0) ""
  ["TRACENO"]=>
  string(0) ""
  ["RRN"]=>
  string(0) ""
  ["SecurePan"]=>
  string(0) ""
  ["PHPSESSID"]=>
  string(26) "dbna0hh5oiujv9gip9cef3php3"
}

*/
if(isset($_REQUEST['StateCode'])==-1){
  $error = 'پرداخت توسط شما کنسل شد';
  require("index.php");
}else{
  var_dump($_REQUEST);
}