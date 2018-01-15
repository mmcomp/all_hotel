<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);




$hotelName1 = (isset($_POST['hotelName1']))?$_POST['hotelName1']:"";
$type1 = (isset($_POST['type1']))?$_POST['type1']:"";
$name1 = (isset($_POST['name1']))?$_POST['name1']:"";
$hotel = new hotel_class($hotelName1);
$kol = new moeen_class($hotel->moeen_id);
$moeen_id = moeen_class::addById($kol->kol_id,'درآمد صندوق '.$name1);
$moeen_cash_id = moeen_class::addById($kol->kol_id,'درآمد متفرقه '.$name1);


 $query = mysql_class::ex_sqlx("insert into `sandogh` (`name`,`hotel_id`,`moeen_id`,`moeen_cash_id`,`can_cash`) values ('$name1','$hotelName1','$moeen_id','$moeen_cash_id','$type1')");
if($query) 
 echo "1";  
else
    echo "0";


?>