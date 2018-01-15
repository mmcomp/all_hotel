<?php   
session_start();
include_once("../kernel.php");
$se = security_class::auth((int)$_SESSION['user_id']);



$Hname = (isset($_POST['Hname']))?$_POST['Hname']:"";
$Htype = (isset($_POST['Htype']))?$_POST['Htype']:"";
$Hshabnafare = (isset($_POST['Hshabnafare']))?$_POST['Hshabnafare']:"";
$kol_id = kol_class::addById($Hname);
$moeen_id = moeen_class::addById($kol_id,'درآمد رزرواسیون '.$Hname);
$moeen_hazine_id = moeen_class::addById($kol_id,'هزینه غذای '.$Hname);

$query = mysql_class::ex_sqlx("insert into `hotel` (`name`,`moeen_id`,`is_our`,`ghaza_moeen_id`,`is_shab_nafar`) values ('$Hname','$moeen_id','$Htype','$moeen_hazine_id','$Hshabnafare')");
if($query) 
    echo "1";  
else
    echo "0";


?>