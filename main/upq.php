<?php   
session_start();
include_once("../kernel.php");
$qid = (isset($_POST['qid']))?$_POST['qid']:"";
$room_id = (isset($_POST['room_id']))?$_POST['room_id']:"";
$reserve_id = (isset($_POST['reserve_id']))?$_POST['reserve_id']:"";
$user_id = (isset($_POST['user_id']))?$_POST['user_id']:"";
$tarikh = (isset($_POST['tarikh']))?$_POST['tarikh']:"";

$ln = mysql_class::ex_sqlx("insert into ravabet (room_id,reserve_id,tarikh,user_id) values ($room_id,$reserve_id,'$tarikh',$user_id)",FALSE);
		$ravabet_id = mysql_insert_id($ln);
		mysql_close($ln);
		$q = null;
		mysql_class::ex_sql("select id from ravabet_ques order by id",$q);
		$ans = '';
		while($r = mysql_fetch_array($q))
			$ans .= (($ans!='')?' , ':'')."($ravabet_id,".$r['id'].")";
		mysql_class::ex_sqlx("insert into ravabet_det (ravabet_id,ravabet_ques_id) values $ans");


?>