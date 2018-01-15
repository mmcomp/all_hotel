<?php
	session_start();
	include("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	mysql_class::ex_sql("select `reserve_id`,`aztarikh` from `room_det`",$q);
//echo 	"select `reserve_id`,`aztarikh` from `room_det`".'<br/>';
	while($r = mysql_fetch_array($q))
	{
		$aztarikh = $r["aztarikh"];
		$reserve_id = $r["reserve_id"];
		mysql_class::ex_sql("select `id`,`tarikh` from `khadamat_det` where `reserve_id`='$reserve_id' and `khadamat_id`='98'",$qu);			
		if($ru = mysql_fetch_array($qu))
		{
			$id = $ru["id"];
			$to_date = date('Y-m-d', strtotime($aztarikh .' +1 day'));
			if ($ru["tarikh"]!=$to_date)
			{
				mysql_class::ex_sqlx("UPDATE `khadamat_det` SET `tarikh` = '$to_date' WHERE `id` ='$id'");
				echo "UPDATE `khadamat_det` SET `tarikh` = '$to_date' WHERE `id` ='$id'";
			}
		}
	}
?>
