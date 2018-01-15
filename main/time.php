<?php 
	include_once ("../kernel.php");
/*
	mysql_class::ex_sql("select `id`,`aztarikh`,`tatarikh` from `room_det`",$q);
	while($r = mysql_fetch_array($q))
	{
		$da = $r['aztarikh'];
		$da = explode(' ',$da);
		if($da[1]!='21:00:00' || $da[1]!='00:00:00' || $da[1]!='14:00:00')
		{
			$da = $da[0];
			$da = $da.' 14:00:00';
			mysql_class::ex_sqlx("update `room_det` set `aztarikh`='$da' where `id`=".$r['id']." ");
		}
		$da = $r['tatarikh'];
		$da = explode(' ',$da);
		if($da[1]!='21:00:00' || $da[1]!='00:00:00' || $da[1]!='14:00:00')
		{
			$da = $da[0];
			$da = $da.' 14:00:00';
			mysql_class::ex_sqlx("update `room_det` set `tatarikh`='$da' where `id`=".$r['id']." ");
		}
	}
*/
	echo jdate("d / m / Y ساعت  i : H");
?>
