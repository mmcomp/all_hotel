<?php
// This results in an error.
// The output above is before the header() call
session_start();
$_SESSION['is_qeshm'] = FALSE;
if( $_SERVER['SERVER_NAME']=='hotel724.info'){
  header('Location: h724/');
}else{
  header('Location: main/');
}
?> 